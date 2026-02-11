<?php
/**
 * Price Calculator Class v2.5.7
 * Enhanced with:
 * - Auto/Manual Making Charges
 * - Manual Diamond Entry with 4Cs
 * - Pearl/Stone/Extra Fee percentage vs fixed calculation (v2.5.4)
 * - Additional Percentage enable/disable respect (v2.5.5)
 * - GST enable/disable respect with generic rate (v2.5.5)
 * - CRITICAL FIX: Use price_per_unit instead of price_per_gram (v2.5.7)
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Price_Calculator {
    
    // Track products being calculated to prevent infinite loops
    private static $calculating_products = array();
    
    /**
     * Calculate making charges (v2.0.0 - Auto/Manual modes)
     */
    private static function calculate_making_charges($product_id, $metal_price, $metal_id, $metal_weight) {
        $mode = get_post_meta($product_id, '_jpc_making_charges_mode', true) ?: 'auto';
        
        if ($mode === 'auto') {
            // Auto mode: Metal Weight × Making Charges per Gram
            $metal = JPC_Metals::get_by_id($metal_id);
            if (!$metal) return 0;
            
            $making_charges_per_gram = floatval($metal->making_charges_per_gram ?? 0);
            return $metal_weight * $making_charges_per_gram;
            
        } else {
            // Manual mode: Percentage or Fixed
            $value = floatval(get_post_meta($product_id, '_jpc_making_charges_value', true));
            $type = get_post_meta($product_id, '_jpc_making_charges_type', true) ?: 'percentage';
            
            if ($type === 'percentage') {
                return ($metal_price * $value) / 100;
            } else {
                return $value;
            }
        }
    }
    
    /**
     * Calculate additional cost field (v2.5.4 - Percentage or Fixed)
     * Used for Pearl Cost, Stone Cost, Extra Fee
     */
    private static function calculate_additional_cost($product_id, $field_name, $subtotal) {
        $value = floatval(get_post_meta($product_id, '_jpc_' . $field_name . '_value', true));
        $type = get_post_meta($product_id, '_jpc_' . $field_name . '_type', true) ?: 'percentage';
        
        if ($value <= 0) {
            return 0;
        }
        
        if ($type === 'percentage') {
            return ($subtotal * $value) / 100;
        } else {
            return $value;
        }
    }
    
    /**
     * Calculate diamond cost (v2.0.0 - Auto/Manual modes)
     */
    private static function calculate_diamond_cost($product_id) {
        $mode = get_post_meta($product_id, '_jpc_diamond_mode', true) ?: 'auto';
        
        if ($mode === 'auto') {
            // Auto mode: Use diamond from database
            $diamond_id = get_post_meta($product_id, '_jpc_diamond_id', true);
            $diamond_quantity = intval(get_post_meta($product_id, '_jpc_diamond_quantity', true));
            
            if (!$diamond_id || $diamond_quantity <= 0) {
                return 0;
            }
            
            $diamond = JPC_Diamonds::get_by_id($diamond_id);
            if (!$diamond) return 0;
            
            $diamond_unit_price = $diamond->price_per_carat * $diamond->carat;
            return $diamond_unit_price * $diamond_quantity;
            
        } else {
            // Manual mode: Calculate with 4Cs adjustments
            $carat = floatval(get_post_meta($product_id, '_jpc_manual_diamond_carat', true));
            $quantity = floatval(get_post_meta($product_id, '_jpc_manual_diamond_quantity', true));
            $base_price = floatval(get_post_meta($product_id, '_jpc_manual_diamond_price_per_carat', true));
            
            if ($carat <= 0 || $quantity <= 0 || $base_price <= 0) {
                return 0;
            }
            
            // Get 4Cs adjustments
            $cut_adjustment = floatval(get_post_meta($product_id, '_jpc_manual_diamond_cut_adjustment', true));
            $color_adjustment = floatval(get_post_meta($product_id, '_jpc_manual_diamond_color_adjustment', true));
            $clarity_adjustment = floatval(get_post_meta($product_id, '_jpc_manual_diamond_clarity_adjustment', true));
            
            // Calculate adjusted price per carat
            $adjusted_price = $base_price * (1 + ($cut_adjustment / 100)) * 
                             (1 + ($color_adjustment / 100)) * 
                             (1 + ($clarity_adjustment / 100));
            
            // Total diamond cost
            return $adjusted_price * $carat * $quantity;
        }
    }
    
    /**
     * Calculate product prices (v2.5.7 - Fixed price_per_unit bug)
     */
    public static function calculate_product_prices($product_id) {
        // Get metal info
        $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
        $metal_weight = floatval(get_post_meta($product_id, '_jpc_metal_weight', true));
        
        if (!$metal_id || $metal_weight <= 0) {
            return false;
        }
        
        $metal = JPC_Metals::get_by_id($metal_id);
        if (!$metal) {
            return false;
        }
        
        // Get metal group for GST calculation
        $metal_group = null;
        if (!empty($metal->metal_group_id)) {
            $metal_group = JPC_Metal_Groups::get_by_id($metal->metal_group_id);
        }
        
        // Calculate metal price - CRITICAL FIX: Use price_per_unit (database column name)
        $metal_price = floatval($metal->price_per_unit) * $metal_weight;
        
        // Calculate diamond cost
        $diamond_price = self::calculate_diamond_cost($product_id);
        
        // Calculate making charges
        $making_charge_amount = self::calculate_making_charges($product_id, $metal_price, $metal_id, $metal_weight);
        
        // Calculate wastage charges
        $wastage_percentage = floatval(get_post_meta($product_id, '_jpc_wastage_percentage', true));
        $wastage_charge_amount = ($metal_price * $wastage_percentage) / 100;
        
        // Calculate subtotal for percentage-based additional costs
        $subtotal_for_percentage = $metal_price + $diamond_price + $making_charge_amount + $wastage_charge_amount;
        
        // Calculate additional costs (v2.5.4 - Percentage or Fixed)
        $pearl_cost = self::calculate_additional_cost($product_id, 'pearl_cost', $subtotal_for_percentage);
        $stone_cost = self::calculate_additional_cost($product_id, 'stone_cost', $subtotal_for_percentage);
        $extra_fee = self::calculate_additional_cost($product_id, 'extra_fee', $subtotal_for_percentage);
        
        // Get extra field costs
        $extra_field_costs = 0;
        for ($i = 1; $i <= 5; $i++) {
            $extra_field_costs += floatval(get_post_meta($product_id, '_jpc_extra_field_' . $i, true));
        }
        
        // Calculate subtotal before additional percentage
        $subtotal_before_additional = $metal_price + $diamond_price + $making_charge_amount + 
                                      $wastage_charge_amount + $pearl_cost + $stone_cost + 
                                      $extra_fee + $extra_field_costs;
        
        // Apply Additional Percentage (if enabled) - v2.5.5
        $additional_percentage_amount = 0;
        $enable_additional_percentage = get_option('jpc_enable_additional_percentage', 'no');
        $additional_percentage = floatval(get_option('jpc_additional_percentage_value', 0));
        
        if ($enable_additional_percentage === 'yes' && $additional_percentage > 0) {
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
        
        // Get GST settings (v2.5.5 - Use generic rate)
        $enable_gst = get_option('jpc_enable_gst', 'yes');
        $gst_percentage = 0;
        
        if ($enable_gst === 'yes') {
            $gst_percentage = floatval(get_option('jpc_gst_value', 3));
        }
        
        $gst_calculation_base = get_option('jpc_gst_calculation_base', 'after_discount');
        
        // Calculate GST (only if enabled)
        $gst_on_full = 0;
        $gst_on_discounted = 0;
        
        if ($enable_gst === 'yes' && $gst_percentage > 0) {
            if ($gst_calculation_base === 'original_price') {
                // GST on original price (before discount)
                $gst_on_full = ($subtotal_after_additional * $gst_percentage) / 100;
                $gst_on_discounted = $gst_on_full;
            } else {
                // GST on discounted price (default)
                $gst_on_full = ($subtotal_after_additional * $gst_percentage) / 100;
                $gst_on_discounted = ($subtotal_after_discount * $gst_percentage) / 100;
            }
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
     * Calculate and store price breakup (for display purposes) v2.0.0
     */
    public static function calculate_and_store_breakup($product_id) {
        // Get prices
        $prices = self::calculate_product_prices($product_id);
        
        if (!$prices) {
            return false;
        }
        
        // Get extra field costs with labels
        $extra_fields = array();
        for ($i = 1; $i <= 5; $i++) {
            $enabled = get_option('jpc_enable_extra_field_' . $i);
            if ($enabled === 'yes' || $enabled === '1' || $enabled === 1 || $enabled === true) {
                $label = get_option('jpc_extra_field_label_' . $i, 'Extra Field #' . $i);
                $value = floatval(get_post_meta($product_id, '_jpc_extra_field_' . $i, true));
                $extra_fields[] = array(
                    'field_number' => $i,
                    'label' => $label,
                    'value' => $value
                );
            }
        }
        
        // Determine which GST to show in breakup
        $gst_to_display = 0;
        if ($prices['discount_percentage'] > 0) {
            $gst_to_display = $prices['gst_on_discounted'];
        } else {
            $gst_to_display = $prices['gst_on_full'];
        }
        
        // Get labels
        $additional_percentage_label = get_option('jpc_additional_percentage_label', 'Additional Percentage');
        $additional_percentage_value = floatval(get_option('jpc_additional_percentage_value', 0));
        $gst_label = get_option('jpc_gst_label', 'GST');
        
        // Store price breakup for display
        $breakup = array(
            'metal_price' => $prices['metal_price'],
            'diamond_price' => $prices['diamond_price'],
            'making_charge' => $prices['making_charge'],
            'wastage_charge' => $prices['wastage_charge'],
            'pearl_cost' => $prices['pearl_cost'],
            'stone_cost' => $prices['stone_cost'],
            'extra_fee' => $prices['extra_fee'],
            'extra_fields' => $extra_fields,
            'additional_percentage' => $prices['additional_percentage_amount'],
            'additional_percentage_label' => $additional_percentage_label,
            'additional_percentage_value' => $additional_percentage_value,
            'discount' => $prices['discount_amount'],
            'gst' => $gst_to_display,
            'gst_percentage' => $prices['gst_percentage'],
            'gst_label' => $gst_label,
            'gst_on_full' => $prices['gst_on_full'],
            'gst_on_discounted' => $prices['gst_on_discounted'],
            'subtotal' => $prices['sale_price'],
            'final_price' => $prices['sale_price'],
        );
        
        update_post_meta($product_id, '_jpc_price_breakup', $breakup);
        
        return $breakup;
    }
    
    /**
     * Calculate and update product price - RETURNS SUCCESS STATUS
     */
    public static function calculate_and_update_price($product_id) {
        // Prevent infinite loops
        if (isset(self::$calculating_products[$product_id])) {
            return false;
        }
        
        self::$calculating_products[$product_id] = true;
        
        try {
            // Calculate prices
            $prices = self::calculate_product_prices($product_id);
            
            if (!$prices) {
                unset(self::$calculating_products[$product_id]);
                return false;
            }
            
            // Clear all WooCommerce caches first
            wp_cache_delete('product-' . $product_id, 'products');
            wp_cache_delete($product_id, 'post_meta');
            wc_delete_product_transients($product_id);
            
            // Get WooCommerce product (fresh from database)
            $product = wc_get_product($product_id);
            
            if (!$product) {
                unset(self::$calculating_products[$product_id]);
                return false;
            }
            
            // Set prices using both WooCommerce methods AND direct meta updates
            $product->set_regular_price($prices['regular_price']);
            $product->set_sale_price($prices['sale_price']);
            $product->set_price($prices['sale_price']);
            
            // Save product (this triggers WooCommerce hooks)
            $product->save();
            
            // Direct meta updates to ensure values are stored
            update_post_meta($product_id, '_regular_price', $prices['regular_price']);
            update_post_meta($product_id, '_sale_price', $prices['sale_price']);
            update_post_meta($product_id, '_price', $prices['sale_price']);
            
            // Store discount percentage
            update_post_meta($product_id, '_jpc_discount_percentage', $prices['discount_percentage']);
            
            // Calculate and store breakup
            self::calculate_and_store_breakup($product_id);
            
            // Clear caches again after update
            wp_cache_delete('product-' . $product_id, 'products');
            wp_cache_delete($product_id, 'post_meta');
            wc_delete_product_transients($product_id);
            clean_post_cache($product_id);
            
            unset(self::$calculating_products[$product_id]);
            return true;
            
        } catch (Exception $e) {
            unset(self::$calculating_products[$product_id]);
            return false;
        }
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
