<?php
/**
 * Price Calculator Class
 * Handles all price calculations for jewellery products
 * v2.5.14: Added manual diamond calculation support
 * v2.5.1: Fixed label storage and extra fields format for frontend compatibility
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Price_Calculator {
    
    /**
     * Calculate product prices with all components
     * Returns array with all price components for WooCommerce
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
        
        // v2.5.14: Check for manual diamond entry if no dropdown diamond
        if ($diamond_price == 0) {
            $manual_carat = floatval(get_post_meta($product_id, '_jpc_manual_diamond_carat', true));
            $manual_quantity = intval(get_post_meta($product_id, '_jpc_manual_diamond_quantity', true));
            $manual_price_per_carat = floatval(get_post_meta($product_id, '_jpc_manual_diamond_price_per_carat', true));
            
            if ($manual_carat > 0 && $manual_quantity > 0 && $manual_price_per_carat > 0) {
                $diamond_price = $manual_carat * $manual_quantity * $manual_price_per_carat;
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
        
        // Get additional costs - v2.5.0: Support percentage calculations
        $pearl_cost_value = floatval(get_post_meta($product_id, '_jpc_pearl_cost', true));
        $pearl_cost_type = get_option('jpc_pearl_cost_type', 'fixed');
        $pearl_cost = 0;
        if ($pearl_cost_value > 0) {
            if ($pearl_cost_type === 'percentage') {
                $pearl_cost = ($metal_price * $pearl_cost_value) / 100;
            } else {
                $pearl_cost = $pearl_cost_value;
            }
        }
        
        $stone_cost_value = floatval(get_post_meta($product_id, '_jpc_stone_cost', true));
        $stone_cost_type = get_option('jpc_stone_cost_type', 'fixed');
        $stone_cost = 0;
        if ($stone_cost_value > 0) {
            if ($stone_cost_type === 'percentage') {
                $stone_cost = ($metal_price * $stone_cost_value) / 100;
            } else {
                $stone_cost = $stone_cost_value;
            }
        }
        
        $extra_fee_value = floatval(get_post_meta($product_id, '_jpc_extra_fee', true));
        $extra_fee_type = get_option('jpc_extra_fee_type', 'fixed');
        $extra_fee = 0;
        if ($extra_fee_value > 0) {
            if ($extra_fee_type === 'percentage') {
                $extra_fee = ($metal_price * $extra_fee_value) / 100;
            } else {
                $extra_fee = $extra_fee_value;
            }
        }
        
        // Get extra field costs
        $extra_field_costs = 0;
        for ($i = 1; $i <= 5; $i++) {
            $enabled = get_option('jpc_enable_extra_field_' . $i);
            if ($enabled === 'yes' || $enabled === '1' || $enabled === 1 || $enabled === true) {
                $value = floatval(get_post_meta($product_id, '_jpc_extra_field_' . $i, true));
                $extra_field_costs += $value;
            }
        }
        
        // Calculate subtotal before additional percentage
        $subtotal_before_additional = $metal_price + $diamond_price + $making_charge_amount + 
                                      $wastage_charge_amount + $pearl_cost + $stone_cost + 
                                      $extra_fee + $extra_field_costs;
        
        // Get additional percentage
        $additional_percentage_value = floatval(get_option('jpc_additional_percentage_value', 0));
        $additional_percentage_amount = 0;
        if ($additional_percentage_value > 0) {
            $additional_percentage_amount = ($subtotal_before_additional * $additional_percentage_value) / 100;
        }
        
        // Calculate subtotal after additional percentage (before GST)
        $subtotal_after_additional = $subtotal_before_additional + $additional_percentage_amount;
        
        // Get discount
        $discount_percentage = floatval(get_post_meta($product_id, '_jpc_discount_percentage', true));
        $discount_amount = 0;
        if ($discount_percentage > 0) {
            $discount_amount = ($subtotal_after_additional * $discount_percentage) / 100;
        }
        
        // Calculate subtotal after discount (before GST)
        $subtotal_after_discount = $subtotal_after_additional - $discount_amount;
        
        // Get GST percentage
        $gst_percentage = floatval(get_option('jpc_gst_value', 0));
        
        // Calculate GST on full amount (before discount)
        $gst_on_full = 0;
        if ($gst_percentage > 0) {
            $gst_on_full = ($subtotal_after_additional * $gst_percentage) / 100;
        }
        
        // Calculate GST on discounted amount
        $gst_on_discounted = 0;
        if ($gst_percentage > 0) {
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
     * v2.5.14: Added manual diamond calculation support
     * v2.5.1: CRITICAL FIX - Store labels and extra fields in correct format for frontend
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
        
        // v2.5.14: Check for manual diamond entry if no dropdown diamond
        if ($diamond_price == 0) {
            $manual_carat = floatval(get_post_meta($product_id, '_jpc_manual_diamond_carat', true));
            $manual_quantity = intval(get_post_meta($product_id, '_jpc_manual_diamond_quantity', true));
            $manual_price_per_carat = floatval(get_post_meta($product_id, '_jpc_manual_diamond_price_per_carat', true));
            
            if ($manual_carat > 0 && $manual_quantity > 0 && $manual_price_per_carat > 0) {
                $diamond_price = $manual_carat * $manual_quantity * $manual_price_per_carat;
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
        
        // Get additional costs - UPDATED v2.5.0 to support percentage calculations
        $pearl_cost_value = floatval(get_post_meta($product_id, '_jpc_pearl_cost', true));
        $pearl_cost_type = get_option('jpc_pearl_cost_type', 'fixed');
        $pearl_cost = 0;
        if ($pearl_cost_value > 0) {
            if ($pearl_cost_type === 'percentage') {
                $pearl_cost = ($metal_price * $pearl_cost_value) / 100;
            } else {
                $pearl_cost = $pearl_cost_value;
            }
        }
        
        $stone_cost_value = floatval(get_post_meta($product_id, '_jpc_stone_cost', true));
        $stone_cost_type = get_option('jpc_stone_cost_type', 'fixed');
        $stone_cost = 0;
        if ($stone_cost_value > 0) {
            if ($stone_cost_type === 'percentage') {
                $stone_cost = ($metal_price * $stone_cost_value) / 100;
            } else {
                $stone_cost = $stone_cost_value;
            }
        }
        
        $extra_fee_value = floatval(get_post_meta($product_id, '_jpc_extra_fee', true));
        $extra_fee_type = get_option('jpc_extra_fee_type', 'fixed');
        $extra_fee = 0;
        if ($extra_fee_value > 0) {
            if ($extra_fee_type === 'percentage') {
                $extra_fee = ($metal_price * $extra_fee_value) / 100;
            } else {
                $extra_fee = $extra_fee_value;
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
        
        // v2.5.1 CRITICAL FIX: Get custom labels for pearl/stone/extra costs
        $pearl_cost_label = get_option('jpc_pearl_cost_label', 'Pearl Cost');
        $stone_cost_label = get_option('jpc_stone_cost_label', 'Stone Cost');
        $extra_fee_label = get_option('jpc_extra_fee_label', 'Extra Fee');
        
        // v2.5.1 CRITICAL FIX: Build breakup array with FLAT structure for extra fields
        $breakup = array(
            'metal_price' => $metal_price,
            'diamond_price' => $diamond_price,
            'making_charge' => $making_charge_amount,
            'wastage_charge' => $wastage_charge_amount,
            'pearl_cost' => $pearl_cost,
            'pearl_cost_label' => $pearl_cost_label,  // v2.5.1: Store custom label
            'stone_cost' => $stone_cost,
            'stone_cost_label' => $stone_cost_label,  // v2.5.1: Store custom label
            'extra_fee' => $extra_fee,
            'extra_fee_label' => $extra_fee_label,  // v2.5.1: Store custom label
            'additional_percentage' => $prices['additional_percentage_amount'],
            'additional_percentage_label' => $additional_percentage_label,
            'additional_percentage_value' => $additional_percentage_value,
            'discount' => $prices['discount_amount'],
            'gst' => $gst_to_display,
            'gst_percentage' => $gst_percentage,
            'gst_label' => $gst_label,
            'gst_on_full' => $prices['gst_on_full'],
            'gst_on_discounted' => $prices['gst_on_discounted'],
            'subtotal_before_gst' => $prices['subtotal_after_additional'],  // v2.5.1: Add missing field
            'subtotal' => $prices['sale_price'],
            'final_price' => $prices['sale_price'],
        );
        
        // v2.5.1 CRITICAL FIX: Store extra fields in FLAT format (not nested array)
        // Frontend expects: extra_field_1, extra_field_label_1, etc.
        for ($i = 1; $i <= 5; $i++) {
            $enabled = get_option('jpc_enable_extra_field_' . $i);
            if ($enabled === 'yes' || $enabled === '1' || $enabled === 1 || $enabled === true) {
                $label = get_option('jpc_extra_field_label_' . $i, 'Extra Field #' . $i);
                $value = floatval(get_post_meta($product_id, '_jpc_extra_field_' . $i, true));
                
                // Store in flat format
                $breakup['extra_field_' . $i] = $value;
                $breakup['extra_field_label_' . $i] = $label;
            }
        }
        
        update_post_meta($product_id, '_jpc_price_breakup', $breakup);
        
        return $breakup;
    }
    
    /**
     * Calculate and update product price
     * Updates WooCommerce price fields
     */
    public static function calculate_and_update_price($product_id) {
        try {
            $prices = self::calculate_product_prices($product_id);
            
            if (!$prices) {
                return false;
            }
            
            // Update WooCommerce price fields
            update_post_meta($product_id, '_regular_price', $prices['regular_price']);
            update_post_meta($product_id, '_sale_price', $prices['sale_price']);
            update_post_meta($product_id, '_price', $prices['sale_price']);
            
            // Also update breakup
            self::calculate_and_store_breakup($product_id);
            
            return true;
        } catch (Exception $e) {
            error_log('JPC Price Calculation Error for Product ' . $product_id . ': ' . $e->getMessage());
            return false;
        }
    }
}
