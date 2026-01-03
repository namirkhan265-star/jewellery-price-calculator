<?php
/**
 * Price Calculator - Core Logic
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Price_Calculator {
    
    private static $instance = null;
    private static $calculating_products = array(); // Track which products are being calculated
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Hook into product save - priority 30 to run AFTER meta is saved (meta saves at priority 10)
        add_action('woocommerce_process_product_meta', array($this, 'calculate_and_update_price'), 30);
        add_action('woocommerce_save_product_variation', array($this, 'calculate_and_update_price'), 30);
        
        // Also hook into save_post as a fallback
        add_action('save_post_product', array($this, 'calculate_and_update_price'), 30);
        
        // DISABLED DYNAMIC PRICING - Use stored database values instead
        // Dynamic pricing was causing incorrect regular price display (₹293,240 instead of ₹307,902)
        // Now prices are calculated ONCE on save and stored in database
        
        // Update price breakup dynamically
        add_filter('woocommerce_get_price_html', array($this, 'update_price_breakup_on_display'), 10, 2);
    }
    
    /**
     * Update price breakup when price is displayed
     */
    public function update_price_breakup_on_display($price_html, $product) {
        // Only on frontend
        if (is_admin()) {
            return $price_html;
        }
        
        $product_id = $product->get_id();
        
        // Check if this product uses JPC
        $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
        
        if (!$metal_id) {
            return $price_html;
        }
        
        // Recalculate and update breakup
        self::calculate_and_store_breakup($product_id);
        
        return $price_html;
    }
    
    /**
     * Calculate product prices (both regular and sale prices)
     * Returns array with 'regular_price' (before discount) and 'sale_price' (after discount)
     */
    public static function calculate_product_prices($product_id) {
        // Get metal data
        $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
        
        if (!$metal_id) {
            return false;
        }
        
        $metal = JPC_Metals::get_by_id($metal_id);
        
        if (!$metal) {
            return false;
        }
        
        $metal_group = JPC_Metal_Groups::get_by_id($metal->metal_group_id);
        
        // Get product metal data
        $weight = floatval(get_post_meta($product_id, '_jpc_metal_weight', true));
        
        if (!$weight) {
            return false;
        }
        
        $making_charge = floatval(get_post_meta($product_id, '_jpc_making_charge', true));
        $making_charge_type = get_post_meta($product_id, '_jpc_making_charge_type', true) ?: 'percentage';
        $wastage_charge = floatval(get_post_meta($product_id, '_jpc_wastage_charge', true));
        $wastage_charge_type = get_post_meta($product_id, '_jpc_wastage_charge_type', true) ?: 'percentage';
        
        // Calculate base metal price
        $metal_price = $weight * $metal->price_per_unit;
        
        // Get diamond data and calculate diamond price
        $diamond_price = 0;
        $diamond_id = get_post_meta($product_id, '_jpc_diamond_id', true);
        $diamond_quantity = intval(get_post_meta($product_id, '_jpc_diamond_quantity', true));
        
        if ($diamond_id && $diamond_quantity > 0) {
            $diamond = JPC_Diamonds::get_by_id($diamond_id);
            if ($diamond) {
                $diamond_unit_price = $diamond->price_per_carat * $diamond->carat;
                $diamond_price = $diamond_unit_price * $diamond_quantity;
            }
        }
        
        // Calculate making charge
        $making_charge_amount = 0;
        if ($metal_group->enable_making_charge && $making_charge > 0) {
            if ($making_charge_type === 'percentage') {
                $making_charge_amount = ($metal_price * $making_charge) / 100;
            } else {
                $making_charge_amount = $making_charge;
            }
        }
        
        // Calculate wastage charge
        $wastage_charge_amount = 0;
        if ($metal_group->enable_wastage_charge && $wastage_charge > 0) {
            if ($wastage_charge_type === 'percentage') {
                $wastage_charge_amount = ($metal_price * $wastage_charge) / 100;
            } else {
                $wastage_charge_amount = $wastage_charge;
            }
        }
        
        // Get additional costs
        $pearl_cost = floatval(get_post_meta($product_id, '_jpc_pearl_cost', true));
        $stone_cost = floatval(get_post_meta($product_id, '_jpc_stone_cost', true));
        $extra_fee = floatval(get_post_meta($product_id, '_jpc_extra_fee', true));
        
        // Get extra field costs (Extra Fields #1-5)
        $extra_field_costs = 0;
        for ($i = 1; $i <= 5; $i++) {
            $enabled = get_option('jpc_enable_extra_field_' . $i);
            if ($enabled === 'yes' || $enabled === '1' || $enabled === 1 || $enabled === true) {
                $field_value = floatval(get_post_meta($product_id, '_jpc_extra_field_' . $i, true));
                $extra_field_costs += $field_value;
            }
        }
        
        // Calculate subtotal before additional percentage
        $subtotal_before_additional = $metal_price + $diamond_price + $making_charge_amount + $wastage_charge_amount + $pearl_cost + $stone_cost + $extra_fee + $extra_field_costs;
        
        // Apply Additional Percentage (if enabled)
        $additional_percentage_amount = 0;
        $additional_percentage = floatval(get_option('jpc_additional_percentage_value', 0));
        if ($additional_percentage > 0) {
            $additional_percentage_amount = ($subtotal_before_additional * $additional_percentage) / 100;
        }
        
        // Subtotal after additional percentage
        $subtotal_before_discount = $subtotal_before_additional + $additional_percentage_amount;
        
        // Get discount percentage
        $discount_percentage = floatval(get_post_meta($product_id, '_jpc_discount_percentage', true));
        
        // ============================================================
        // ENHANCED DISCOUNT CALCULATION WITH 4 METHODS
        // ============================================================
        $discount_amount = 0;
        $subtotal_after_discount = $subtotal_before_discount;
        
        if ($discount_percentage > 0) {
            // Get discount calculation method
            $discount_method = get_option('jpc_discount_calculation_method', 'simple');
            
            // Calculate discountable amount based on method
            $discountable_amount = 0;
            
            switch ($discount_method) {
                case 'simple':
                    // Method 1: Component-Based Discount
                    // Only discount selected components (Metal, Making, Wastage)
                    $discount_on_metals = get_option('jpc_discount_on_metals');
                    $discount_on_making = get_option('jpc_discount_on_making');
                    $discount_on_wastage = get_option('jpc_discount_on_wastage');
                    
                    if ($discount_on_metals === 'yes' || $discount_on_metals === '1' || $discount_on_metals === 1 || $discount_on_metals === true) {
                        $discountable_amount += $metal_price;
                    }
                    
                    if ($discount_on_making === 'yes' || $discount_on_making === '1' || $discount_on_making === 1 || $discount_on_making === true) {
                        $discountable_amount += $making_charge_amount;
                    }
                    
                    if ($discount_on_wastage === 'yes' || $discount_on_wastage === '1' || $discount_on_wastage === 1 || $discount_on_wastage === true) {
                        $discountable_amount += $wastage_charge_amount;
                    }
                    
                    // If no components selected, apply to entire subtotal (backward compatibility)
                    if ($discountable_amount == 0) {
                        $discountable_amount = $subtotal_before_discount;
                    }
                    break;
                    
                case 'advanced':
                    // Method 2: All Components
                    // Discount on ALL costs including Diamond, Pearl, Stone, Extra Fees, Extra Fields
                    $discountable_amount = $subtotal_before_discount;
                    break;
                    
                case 'total_before_gst':
                    // Method 3: Total Before GST ⭐ RECOMMENDED
                    // Discount on complete subtotal (after Additional %)
                    // GST will be calculated on discounted amount
                    $discountable_amount = $subtotal_before_discount;
                    break;
                    
                case 'total_after_additional':
                    // Method 4: Total After Additional %
                    // Same as Method 3 but explicitly includes Additional %
                    $discountable_amount = $subtotal_before_discount;
                    break;
                    
                default:
                    // Fallback to total discount
                    $discountable_amount = $subtotal_before_discount;
                    break;
            }
            
            // Calculate discount
            $discount_amount = ($discountable_amount * $discount_percentage) / 100;
            $subtotal_after_discount = $subtotal_before_discount - $discount_amount;
        }
        
        // ============================================================
        // ENHANCED GST CALCULATION WITH BASE OPTIONS
        // ============================================================
        $gst_amount_on_discounted = 0;
        $gst_amount_on_full = 0;
        $gst_percentage = 0;
        $gst_enabled = get_option('jpc_enable_gst');
        
        // Check if GST is enabled
        if ($gst_enabled === 'yes' || $gst_enabled === '1' || $gst_enabled === 1 || $gst_enabled === true) {
            $gst_percentage = floatval(get_option('jpc_gst_value', 5));
            
            // Check for metal-specific GST rates
            // Try multiple option name formats for compatibility
            $metal_group_name = strtolower(str_replace(' ', '_', $metal_group->name));
            $metal_specific_gst = get_option('jpc_gst_' . $metal_group_name);
            
            // Also try without underscores
            if ($metal_specific_gst === false || $metal_specific_gst === '') {
                $metal_group_name_no_underscore = strtolower(str_replace(' ', '', $metal_group->name));
                $metal_specific_gst = get_option('jpc_gst_' . $metal_group_name_no_underscore);
            }
            
            // Use metal-specific GST if found
            if ($metal_specific_gst !== false && $metal_specific_gst !== '' && $metal_specific_gst !== null) {
                $gst_percentage = floatval($metal_specific_gst);
            }
            
            // Get GST calculation base setting
            $gst_base = get_option('jpc_gst_calculation_base', 'after_discount');
            
            if ($gst_base === 'after_discount') {
                // GST on discounted amount (RECOMMENDED)
                $gst_amount_on_discounted = ($subtotal_after_discount * $gst_percentage) / 100;
                $gst_amount_on_full = ($subtotal_before_discount * $gst_percentage) / 100;
            } else {
                // GST on original amount (before discount)
                $gst_amount_on_discounted = ($subtotal_before_discount * $gst_percentage) / 100;
                $gst_amount_on_full = ($subtotal_before_discount * $gst_percentage) / 100;
            }
        }
        
        // Calculate final prices
        $sale_price = $subtotal_after_discount + $gst_amount_on_discounted;
        $regular_price = $subtotal_before_discount + $gst_amount_on_full;
        
        // Apply rounding
        $rounding = get_option('jpc_price_rounding', 'default');
        $sale_price = self::apply_rounding($sale_price, $rounding);
        $regular_price = self::apply_rounding($regular_price, $rounding);
        
        return array(
            'regular_price' => $regular_price,  // Price before discount (with GST on full amount)
            'sale_price' => $sale_price,        // Price after discount (with GST on discounted amount)
            'discount_amount' => $discount_amount,
            'discount_percentage' => $discount_percentage,
            'gst_on_full' => $gst_amount_on_full,
            'gst_on_discounted' => $gst_amount_on_discounted,
            'gst_percentage' => $gst_percentage,
            'additional_percentage_amount' => $additional_percentage_amount,
            'extra_field_costs' => $extra_field_costs
        );
    }
    
    /**
     * Calculate product price (backward compatibility - returns sale price)
     */
    public static function calculate_product_price($product_id) {
        $prices = self::calculate_product_prices($product_id);
        if ($prices !== false) {
            return $prices['sale_price'];
        }
        return false;
    }
    
    /**
     * Calculate and store price breakup (for display purposes)
     */
    public static function calculate_and_store_breakup($product_id) {
        // Get metal data
        $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
        
        if (!$metal_id) {
            return false;
        }
        
        $metal = JPC_Metals::get_by_id($metal_id);
        
        if (!$metal) {
            return false;
        }
        
        $metal_group = JPC_Metal_Groups::get_by_id($metal->metal_group_id);
        
        // Get product metal data
        $weight = floatval(get_post_meta($product_id, '_jpc_metal_weight', true));
        $making_charge = floatval(get_post_meta($product_id, '_jpc_making_charge', true));
        $making_charge_type = get_post_meta($product_id, '_jpc_making_charge_type', true) ?: 'percentage';
        $wastage_charge = floatval(get_post_meta($product_id, '_jpc_wastage_charge', true));
        $wastage_charge_type = get_post_meta($product_id, '_jpc_wastage_charge_type', true) ?: 'percentage';
        
        // Calculate base metal price
        $metal_price = $weight * $metal->price_per_unit;
        
        // Get diamond data and calculate diamond price
        $diamond_price = 0;
        $diamond_id = get_post_meta($product_id, '_jpc_diamond_id', true);
        $diamond_quantity = intval(get_post_meta($product_id, '_jpc_diamond_quantity', true));
        
        if ($diamond_id && $diamond_quantity > 0) {
            $diamond = JPC_Diamonds::get_by_id($diamond_id);
            if ($diamond) {
                $diamond_unit_price = $diamond->price_per_carat * $diamond->carat;
                $diamond_price = $diamond_unit_price * $diamond_quantity;
            }
        }
        
        // Calculate making charge
        $making_charge_amount = 0;
        if ($metal_group->enable_making_charge && $making_charge > 0) {
            if ($making_charge_type === 'percentage') {
                $making_charge_amount = ($metal_price * $making_charge) / 100;
            } else {
                $making_charge_amount = $making_charge;
            }
        }
        
        // Calculate wastage charge
        $wastage_charge_amount = 0;
        if ($metal_group->enable_wastage_charge && $wastage_charge > 0) {
            if ($wastage_charge_type === 'percentage') {
                $wastage_charge_amount = ($metal_price * $wastage_charge) / 100;
            } else {
                $wastage_charge_amount = $wastage_charge;
            }
        }
        
        // Get additional costs
        $pearl_cost = floatval(get_post_meta($product_id, '_jpc_pearl_cost', true));
        $stone_cost = floatval(get_post_meta($product_id, '_jpc_stone_cost', true));
        $extra_fee = floatval(get_post_meta($product_id, '_jpc_extra_fee', true));
        
        // Get extra field costs with labels - INCLUDE ALL ENABLED FIELDS
        $extra_fields = array();
        for ($i = 1; $i <= 5; $i++) {
            $enabled = get_option('jpc_enable_extra_field_' . $i);
            // Check multiple formats for enabled status
            if ($enabled === 'yes' || $enabled === '1' || $enabled === 1 || $enabled === true) {
                $label = get_option('jpc_extra_field_label_' . $i, 'Extra Field #' . $i);
                $value = floatval(get_post_meta($product_id, '_jpc_extra_field_' . $i, true));
                // ALWAYS include enabled fields, even if value is 0 (for display)
                $extra_fields[] = array(
                    'label' => $label,
                    'value' => $value
                );
            }
        }
        
        // Get prices with GST
        $prices = self::calculate_product_prices($product_id);
        
        // Determine which GST to show in breakup
        $gst_to_display = 0;
        if ($prices['discount_percentage'] > 0) {
            // If there's a discount, show GST on discounted amount
            $gst_to_display = $prices['gst_on_discounted'];
        } else {
            // No discount, show GST on full amount
            $gst_to_display = $prices['gst_on_full'];
        }
        
        // Get additional percentage label
        $additional_percentage_label = get_option('jpc_additional_percentage_label', 'Additional Percentage');
        
        // Get GST label and percentage for display
        $gst_label = get_option('jpc_gst_label', 'GST');
        $gst_percentage = $prices['gst_percentage'];
        
        // Store price breakup for display
        $breakup = array(
            'metal_price' => $metal_price,
            'diamond_price' => $diamond_price,
            'making_charge' => $making_charge_amount,
            'wastage_charge' => $wastage_charge_amount,
            'pearl_cost' => $pearl_cost,
            'stone_cost' => $stone_cost,
            'extra_fee' => $extra_fee,
            'extra_fields' => $extra_fields,  // Array of extra fields with labels
            'additional_percentage' => $prices['additional_percentage_amount'],
            'additional_percentage_label' => $additional_percentage_label,
            'discount' => $prices['discount_amount'],
            'gst' => $gst_to_display,  // CRITICAL: Store GST in breakup
            'gst_percentage' => $gst_percentage,  // Store GST percentage for display
            'gst_label' => $gst_label,  // Store GST label
            'gst_on_full' => $prices['gst_on_full'],  // For reference
            'gst_on_discounted' => $prices['gst_on_discounted'],  // For reference
            'subtotal' => $prices['sale_price'],
            'final_price' => $prices['sale_price'],
        );
        
        update_post_meta($product_id, '_jpc_price_breakup', $breakup);
        
        return $breakup;
    }
    
    /**
     * Calculate and update product price
     */
    public static function calculate_and_update_price($product_id) {
        // Prevent infinite loops
        if (isset(self::$calculating_products[$product_id])) {
            return;
        }
        
        self::$calculating_products[$product_id] = true;
        
        // Remove hooks temporarily to prevent infinite loops
        remove_action('woocommerce_process_product_meta', array(self::get_instance(), 'calculate_and_update_price'), 30);
        remove_action('save_post_product', array(self::get_instance(), 'calculate_and_update_price'), 30);
        
        // Calculate prices
        $prices = self::calculate_product_prices($product_id);
        
        if ($prices !== false) {
            // Get product
            $product = wc_get_product($product_id);
            
            if ($product) {
                // Set regular price (before discount)
                $product->set_regular_price($prices['regular_price']);
                
                // Set sale price (after discount) only if there's a discount
                if ($prices['discount_percentage'] > 0) {
                    $product->set_sale_price($prices['sale_price']);
                    $product->set_price($prices['sale_price']); // Active price
                } else {
                    $product->set_sale_price(''); // Clear sale price
                    $product->set_price($prices['regular_price']); // Active price = regular price
                }
                
                // Save product
                $product->save();
                
                // Also calculate and store breakup
                self::calculate_and_store_breakup($product_id);
            }
        }
        
        // Re-add hooks
        add_action('woocommerce_process_product_meta', array(self::get_instance(), 'calculate_and_update_price'), 30);
        add_action('save_post_product', array(self::get_instance(), 'calculate_and_update_price'), 30);
        
        unset(self::$calculating_products[$product_id]);
    }
    
    /**
     * Apply price rounding
     */
    private static function apply_rounding($price, $rounding = 'default') {
        switch ($rounding) {
            case 'nearest_10':
                return round($price / 10) * 10;
            case 'nearest_100':
                return round($price / 100) * 100;
            case 'nearest_1000':
                return round($price / 1000) * 1000;
            case 'ceil_10':
                return ceil($price / 10) * 10;
            case 'ceil_100':
                return ceil($price / 100) * 100;
            case 'ceil_1000':
                return ceil($price / 1000) * 1000;
            case 'floor_10':
                return floor($price / 10) * 10;
            case 'floor_100':
                return floor($price / 100) * 100;
            case 'floor_1000':
                return floor($price / 1000) * 1000;
            default:
                return round($price, 2);
        }
    }
}
