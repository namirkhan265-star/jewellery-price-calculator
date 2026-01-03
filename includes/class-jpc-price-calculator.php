<?php
/**
 * Price Calculator - Core Logic with Flexible Discount Methods
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
     * Calculate product prices with FLEXIBLE DISCOUNT METHODS
     * 
     * Supports 5 discount calculation methods:
     * 1. Simple: Component-based discount (Metal, Making, Wastage only)
     * 2. Advanced: Discount on all components
     * 3. Total Before GST: Discount on subtotal, GST on discounted amount
     * 4. Total After Additional %: Discount includes Additional %
     * 5. Discount After GST: Add GST first, then apply discount, then Additional % ⭐ NEW
     * 
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
        
        // Get discount percentage
        $discount_percentage = floatval(get_post_meta($product_id, '_jpc_discount_percentage', true));
        
        // Get discount calculation method
        $discount_method = get_option('jpc_discount_calculation_method', 'simple');
        
        // ============================================================
        // METHOD 5: DISCOUNT AFTER GST (NEW!)
        // ============================================================
        if ($discount_method === 'discount_after_gst') {
            // FLOW: Subtotal → + Additional % → + GST → - Discount → Final Price
            
            // Step 1: Add Additional % to subtotal
            if ($additional_percentage > 0) {
                $additional_percentage_amount = ($subtotal_before_additional * $additional_percentage) / 100;
            }
            $subtotal_with_additional = $subtotal_before_additional + $additional_percentage_amount;
            
            // Step 2: Calculate and add GST
            $gst_percentage = 0;
            $gst_amount = 0;
            $gst_enabled = get_option('jpc_enable_gst');
            
            if ($gst_enabled === 'yes' || $gst_enabled === '1' || $gst_enabled === 1 || $gst_enabled === true) {
                $gst_percentage = floatval(get_option('jpc_gst_value', 5));
                
                // Check for metal-specific GST rates
                $metal_group_name = strtolower(str_replace(' ', '_', $metal_group->name));
                $metal_specific_gst = get_option('jpc_gst_' . $metal_group_name);
                
                if ($metal_specific_gst === false || $metal_specific_gst === '') {
                    $metal_group_name_no_underscore = strtolower(str_replace(' ', '', $metal_group->name));
                    $metal_specific_gst = get_option('jpc_gst_' . $metal_group_name_no_underscore);
                }
                
                if ($metal_specific_gst !== false && $metal_specific_gst !== '' && $metal_specific_gst !== null) {
                    $gst_percentage = floatval($metal_specific_gst);
                }
                
                // GST on (Subtotal + Additional %)
                $gst_amount = ($subtotal_with_additional * $gst_percentage) / 100;
            }
            
            $total_with_gst = $subtotal_with_additional + $gst_amount;
            
            // Step 3: Apply discount on (Subtotal + Additional % + GST)
            $discount_amount = 0;
            if ($discount_percentage > 0) {
                $discount_amount = ($total_with_gst * $discount_percentage) / 100;
            }
            
            $sale_price = $total_with_gst - $discount_amount;
            $regular_price = $total_with_gst; // Regular price is before discount
            
            // Apply rounding
            $rounding = get_option('jpc_price_rounding', 'default');
            $sale_price = self::apply_rounding($sale_price, $rounding);
            $regular_price = self::apply_rounding($regular_price, $rounding);
            
            return array(
                'regular_price' => $regular_price,
                'sale_price' => $sale_price,
                'discount_amount' => $discount_amount,
                'discount_percentage' => $discount_percentage,
                'gst_on_full' => $gst_amount,
                'gst_on_discounted' => $gst_amount, // Same in this method
                'gst_percentage' => $gst_percentage,
                'additional_percentage_amount' => $additional_percentage_amount,
                'extra_field_costs' => $extra_field_costs,
                'calculation_method' => 'discount_after_gst'
            );
        }
        
        // ============================================================
        // METHODS 1-4: TRADITIONAL DISCOUNT BEFORE GST
        // ============================================================
        
        // Add Additional % for methods 1-4
        if ($additional_percentage > 0) {
            $additional_percentage_amount = ($subtotal_before_additional * $additional_percentage) / 100;
        }
        $subtotal_before_discount = $subtotal_before_additional + $additional_percentage_amount;
        
        // Calculate discount based on method
        $discount_amount = 0;
        $subtotal_after_discount = $subtotal_before_discount;
        
        if ($discount_percentage > 0) {
            $discountable_amount = 0;
            
            switch ($discount_method) {
                case 'simple':
                    // Method 1: Component-Based Discount
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
                    
                    if ($discountable_amount == 0) {
                        $discountable_amount = $subtotal_before_discount;
                    }
                    break;
                    
                case 'advanced':
                    // Method 2: All Components
                    $discountable_amount = $subtotal_before_discount;
                    break;
                    
                case 'total_before_gst':
                    // Method 3: Total Before GST
                    $discountable_amount = $subtotal_before_discount;
                    break;
                    
                case 'total_after_additional':
                    // Method 4: Total After Additional %
                    $discountable_amount = $subtotal_before_discount;
                    break;
                    
                default:
                    $discountable_amount = $subtotal_before_discount;
                    break;
            }
            
            $discount_amount = ($discountable_amount * $discount_percentage) / 100;
            $subtotal_after_discount = $subtotal_before_discount - $discount_amount;
        }
        
        // Calculate GST
        $gst_amount_on_discounted = 0;
        $gst_amount_on_full = 0;
        $gst_percentage = 0;
        $gst_enabled = get_option('jpc_enable_gst');
        
        if ($gst_enabled === 'yes' || $gst_enabled === '1' || $gst_enabled === 1 || $gst_enabled === true) {
            $gst_percentage = floatval(get_option('jpc_gst_value', 5));
            
            // Check for metal-specific GST rates
            $metal_group_name = strtolower(str_replace(' ', '_', $metal_group->name));
            $metal_specific_gst = get_option('jpc_gst_' . $metal_group_name);
            
            if ($metal_specific_gst === false || $metal_specific_gst === '') {
                $metal_group_name_no_underscore = strtolower(str_replace(' ', '', $metal_group->name));
                $metal_specific_gst = get_option('jpc_gst_' . $metal_group_name_no_underscore);
            }
            
            if ($metal_specific_gst !== false && $metal_specific_gst !== '' && $metal_specific_gst !== null) {
                $gst_percentage = floatval($metal_specific_gst);
            }
            
            // Get GST calculation base setting
            $gst_base = get_option('jpc_gst_calculation_base', 'after_discount');
            
            if ($gst_base === 'after_discount') {
                $gst_amount_on_discounted = ($subtotal_after_discount * $gst_percentage) / 100;
                $gst_amount_on_full = ($subtotal_before_discount * $gst_percentage) / 100;
            } else {
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
            'regular_price' => $regular_price,
            'sale_price' => $sale_price,
            'discount_amount' => $discount_amount,
            'discount_percentage' => $discount_percentage,
            'gst_on_full' => $gst_amount_on_full,
            'gst_on_discounted' => $gst_amount_on_discounted,
            'gst_percentage' => $gst_percentage,
            'additional_percentage_amount' => $additional_percentage_amount,
            'extra_field_costs' => $extra_field_costs,
            'calculation_method' => $discount_method
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
     * Calculate and update product price in database
     */
    public function calculate_and_update_price($product_id) {
        // Prevent infinite loops
        if (isset(self::$calculating_products[$product_id])) {
            return;
        }
        
        self::$calculating_products[$product_id] = true;
        
        // Calculate prices
        $prices = self::calculate_product_prices($product_id);
        
        if ($prices !== false) {
            // Remove hooks temporarily to prevent infinite loop
            remove_action('woocommerce_process_product_meta', array($this, 'calculate_and_update_price'), 30);
            remove_action('save_post_product', array($this, 'calculate_and_update_price'), 30);
            
            // Update prices in database
            update_post_meta($product_id, '_regular_price', $prices['regular_price']);
            update_post_meta($product_id, '_price', $prices['sale_price']);
            
            // Only set sale price if there's actually a discount
            if ($prices['discount_percentage'] > 0 && $prices['discount_amount'] > 0) {
                update_post_meta($product_id, '_sale_price', $prices['sale_price']);
            } else {
                delete_post_meta($product_id, '_sale_price');
            }
            
            // Also update the price breakup
            self::calculate_and_store_breakup($product_id);
            
            // Re-add hooks
            add_action('woocommerce_process_product_meta', array($this, 'calculate_and_update_price'), 30);
            add_action('save_post_product', array($this, 'calculate_and_update_price'), 30);
        }
        
        unset(self::$calculating_products[$product_id]);
    }
    
    /**
     * Calculate and store detailed price breakup
     */
    public static function calculate_and_store_breakup($product_id) {
        // Get all necessary data
        $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
        
        if (!$metal_id) {
            return false;
        }
        
        $metal = JPC_Metals::get_by_id($metal_id);
        if (!$metal) {
            return false;
        }
        
        $metal_group = JPC_Metal_Groups::get_by_id($metal->metal_group_id);
        $weight = floatval(get_post_meta($product_id, '_jpc_metal_weight', true));
        
        if (!$weight) {
            return false;
        }
        
        // Calculate all components
        $metal_price = $weight * $metal->price_per_unit;
        
        // Diamond
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
        
        // Making & Wastage
        $making_charge = floatval(get_post_meta($product_id, '_jpc_making_charge', true));
        $making_charge_type = get_post_meta($product_id, '_jpc_making_charge_type', true) ?: 'percentage';
        $wastage_charge = floatval(get_post_meta($product_id, '_jpc_wastage_charge', true));
        $wastage_charge_type = get_post_meta($product_id, '_jpc_wastage_charge_type', true) ?: 'percentage';
        
        $making_charge_amount = 0;
        if ($metal_group->enable_making_charge && $making_charge > 0) {
            if ($making_charge_type === 'percentage') {
                $making_charge_amount = ($metal_price * $making_charge) / 100;
            } else {
                $making_charge_amount = $making_charge;
            }
        }
        
        $wastage_charge_amount = 0;
        if ($metal_group->enable_wastage_charge && $wastage_charge > 0) {
            if ($wastage_charge_type === 'percentage') {
                $wastage_charge_amount = ($metal_price * $wastage_charge) / 100;
            } else {
                $wastage_charge_amount = $wastage_charge;
            }
        }
        
        // Additional costs
        $pearl_cost = floatval(get_post_meta($product_id, '_jpc_pearl_cost', true));
        $stone_cost = floatval(get_post_meta($product_id, '_jpc_stone_cost', true));
        $extra_fee = floatval(get_post_meta($product_id, '_jpc_extra_fee', true));
        
        // Extra fields
        $extra_fields = array();
        for ($i = 1; $i <= 5; $i++) {
            $enabled = get_option('jpc_enable_extra_field_' . $i);
            if ($enabled === 'yes' || $enabled === '1' || $enabled === 1 || $enabled === true) {
                $label = get_option('jpc_extra_field_label_' . $i, 'Extra Field #' . $i);
                $value = floatval(get_post_meta($product_id, '_jpc_extra_field_' . $i, true));
                $extra_fields[] = array(
                    'label' => $label,
                    'value' => $value
                );
            }
        }
        
        // Additional Percentage
        $additional_percentage = floatval(get_option('jpc_additional_percentage_value', 0));
        $additional_percentage_label = get_option('jpc_additional_percentage_label', 'Additional Percentage');
        $subtotal_before_additional = $metal_price + $diamond_price + $making_charge_amount + $wastage_charge_amount + $pearl_cost + $stone_cost + $extra_fee;
        
        foreach ($extra_fields as $field) {
            $subtotal_before_additional += $field['value'];
        }
        
        $additional_percentage_amount = 0;
        if ($additional_percentage > 0) {
            $additional_percentage_amount = ($subtotal_before_additional * $additional_percentage) / 100;
        }
        
        // Get prices from main calculation
        $prices = self::calculate_product_prices($product_id);
        
        // Build breakup array
        $breakup = array(
            'metal_price' => $metal_price,
            'diamond_price' => $diamond_price,
            'making_charge' => $making_charge_amount,
            'wastage_charge' => $wastage_charge_amount,
            'pearl_cost' => $pearl_cost,
            'stone_cost' => $stone_cost,
            'extra_fee' => $extra_fee,
            'extra_fields' => $extra_fields,
            'additional_percentage' => $additional_percentage_amount,
            'additional_percentage_label' => $additional_percentage_label,
            'gst' => $prices['gst_on_discounted'],
            'gst_label' => get_option('jpc_gst_label', 'GST'),
            'gst_percentage' => $prices['gst_percentage'],
            'discount_amount' => $prices['discount_amount'],
            'discount_percentage' => $prices['discount_percentage'],
            'calculation_method' => isset($prices['calculation_method']) ? $prices['calculation_method'] : 'simple'
        );
        
        // Store breakup
        update_post_meta($product_id, '_jpc_price_breakup', $breakup);
        
        return $breakup;
    }
    
    /**
     * Apply price rounding
     */
    private static function apply_rounding($price, $rounding) {
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
