<?php
/**
 * Price Calculator Class
 * Handles all price calculations for jewellery products
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Price_Calculator {
    
    // Track products being calculated to prevent infinite loops
    private static $calculating_products = array();
    
    /**
     * Calculate product prices with GST
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
        
        // Get extra field costs
        $extra_field_costs = 0;
        for ($i = 1; $i <= 5; $i++) {
            $extra_field_costs += floatval(get_post_meta($product_id, '_jpc_extra_field_' . $i, true));
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
        $subtotal_after_additional = $subtotal_before_additional + $additional_percentage_amount;
        
        // Get discount settings
        $discount_percentage = floatval(get_post_meta($product_id, '_jpc_discount_percentage', true));
        $discount_calculation_method = get_option('jpc_discount_calculation_method', '1');
        
        // Calculate discount based on method
        $discount_amount = 0;
        $subtotal_for_discount = 0;
        
        if ($discount_percentage > 0) {
            switch ($discount_calculation_method) {
                case '1': // Simple: Metal + Making + Wastage
                    $subtotal_for_discount = $metal_price + $making_charge_amount + $wastage_charge_amount;
                    break;
                    
                case '2': // Advanced: All components except GST
                    $subtotal_for_discount = $subtotal_before_additional;
                    break;
                    
                case '3': // Total Before GST (RECOMMENDED)
                    $subtotal_for_discount = $subtotal_after_additional;
                    break;
                    
                case '4': // Total After Additional %
                    $subtotal_for_discount = $subtotal_after_additional;
                    break;
                    
                default:
                    $subtotal_for_discount = $metal_price + $making_charge_amount + $wastage_charge_amount;
            }
            
            $discount_amount = ($subtotal_for_discount * $discount_percentage) / 100;
        }
        
        // Subtotal after discount
        $subtotal_after_discount = $subtotal_after_additional - $discount_amount;
        
        // Get GST settings
        $gst_calculation_base = get_option('jpc_gst_calculation_base', 'after_discount');
        
        // Determine GST percentage based on metal group
        $gst_percentage = 0;
        if ($metal_group) {
            $metal_group_name = strtolower($metal_group->name);
            
            if ($metal_group_name === 'gold') {
                $gst_percentage = floatval(get_option('jpc_gst_gold', 3));
            } elseif ($metal_group_name === 'silver') {
                $gst_percentage = floatval(get_option('jpc_gst_silver', 3));
            } elseif ($metal_group_name === 'platinum') {
                $gst_percentage = floatval(get_option('jpc_gst_platinum', 3));
            } else {
                $gst_percentage = floatval(get_option('jpc_gst_default', 3));
            }
        }
        
        // Calculate GST
        $gst_on_full = 0;
        $gst_on_discounted = 0;
        
        if ($gst_calculation_base === 'original_price') {
            // GST on original price (before discount)
            $gst_on_full = ($subtotal_after_additional * $gst_percentage) / 100;
            $gst_on_discounted = $gst_on_full;
        } else {
            // GST on discounted price (default)
            $gst_on_full = ($subtotal_after_additional * $gst_percentage) / 100;
            $gst_on_discounted = ($subtotal_after_discount * $gst_percentage) / 100;
        }
        
        // Calculate final prices
        $regular_price = $subtotal_after_additional + $gst_on_full;
        $sale_price = $subtotal_after_discount + $gst_on_discounted;
        
        return array(
            'metal_price' => $metal_price,
            'diamond_price' => $diamond_price,
            'making_charge' => $making_charge_amount,
            'wastage_charge' => $wastage_charge_amount,
            'pearl_cost' => $pearl_cost,
            'stone_cost' => $stone_cost,
            'extra_fee' => $extra_fee,
            'extra_field_costs' => $extra_field_costs,
            'additional_percentage_amount' => $additional_percentage_amount,
            'subtotal_before_additional' => $subtotal_before_additional,
            'subtotal_after_additional' => $subtotal_after_additional,
            'discount_percentage' => $discount_percentage,
            'discount_amount' => $discount_amount,
            'subtotal_after_discount' => $subtotal_after_discount,
            'gst_percentage' => $gst_percentage,
            'gst_on_full' => $gst_on_full,
            'gst_on_discounted' => $gst_on_discounted,
            'regular_price' => $regular_price,
            'sale_price' => $sale_price,
        );
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
            'field_number' => $i,  // Store the actual field number
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
        
        // Get additional percentage label and value
        $additional_percentage_label = get_option('jpc_additional_percentage_label', 'Additional Percentage');
        $additional_percentage_value = floatval(get_option('jpc_additional_percentage_value', 0));
        
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
            'additional_percentage_value' => $additional_percentage_value,  // Store percentage value for display
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
        
        // Calculate prices
        $prices = self::calculate_product_prices($product_id);
        
        if ($prices) {
            // Get WooCommerce product
            $product = wc_get_product($product_id);
            
            if ($product) {
                // Set regular price and sale price
                $product->set_regular_price($prices['regular_price']);
                $product->set_sale_price($prices['sale_price']);
                $product->set_price($prices['sale_price']);
                
                // Save product
                $product->save();
                
                // Store discount percentage
                update_post_meta($product_id, '_jpc_discount_percentage', $prices['discount_percentage']);
                
                // Calculate and store breakup
                self::calculate_and_store_breakup($product_id);
            }
        }
        
        unset(self::$calculating_products[$product_id]);
    }
    
    /**
     * Recalculate all product prices
     */
    public static function recalculate_all_prices() {
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_jpc_metal_id',
                    'compare' => 'EXISTS'
                )
            )
        );
        
        $products = get_posts($args);
        
        foreach ($products as $product) {
            self::calculate_and_update_price($product->ID);
        }
        
        return count($products);
    }
}
