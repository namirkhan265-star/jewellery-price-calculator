<?php
/**
 * Price Calculator Class v2.0.0
 * Enhanced with:
 * - Auto/Manual Making Charges
 * - Manual Diamond Entry with 4Cs
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
     * Calculate diamond cost (v2.0.0 - Dropdown/Manual modes)
     */
    private static function calculate_diamond_cost($product_id) {
        $mode = get_post_meta($product_id, '_jpc_diamond_entry_mode', true) ?: 'dropdown';
        
        if ($mode === 'dropdown') {
            // Dropdown mode: Existing logic
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
            
            // Get 4Cs IDs
            $shape_id = get_post_meta($product_id, '_jpc_manual_diamond_shape_id', true);
            $colour_id = get_post_meta($product_id, '_jpc_manual_diamond_colour_id', true);
            $clarity_id = get_post_meta($product_id, '_jpc_manual_diamond_clarity_id', true);
            $cut_id = get_post_meta($product_id, '_jpc_manual_diamond_cut_id', true);
            $cert_id = get_post_meta($product_id, '_jpc_manual_diamond_certification_id', true);
            
            // Apply adjustments
            $adjusted_price = $base_price;
            
            if ($shape_id) {
                $shape = JPC_Diamond_Shapes::get_by_id($shape_id);
                $adjusted_price = self::apply_adjustment($adjusted_price, $shape);
            }
            
            if ($colour_id) {
                $colour = JPC_Diamond_Colours::get_by_id($colour_id);
                $adjusted_price = self::apply_adjustment($adjusted_price, $colour);
            }
            
            if ($clarity_id) {
                $clarity = JPC_Diamond_Clarities::get_by_id($clarity_id);
                $adjusted_price = self::apply_adjustment($adjusted_price, $clarity);
            }
            
            if ($cut_id) {
                $cut = JPC_Diamond_Cuts::get_by_id($cut_id);
                $adjusted_price = self::apply_adjustment($adjusted_price, $cut);
            }
            
            if ($cert_id) {
                $cert = JPC_Diamond_Certifications::get_by_id($cert_id);
                $adjusted_price = self::apply_adjustment($adjusted_price, $cert);
            }
            
            return $adjusted_price * $carat * $quantity;
        }
    }
    
    /**
     * Apply 4Cs adjustment to price
     */
    private static function apply_adjustment($price, $attribute) {
        if (!$attribute) return $price;
        
        if ($attribute->adjustment_type === 'percentage') {
            return $price * (1 + ($attribute->adjustment_value / 100));
        } else {
            return $price + $attribute->adjustment_value;
        }
    }
    
    /**
     * Calculate product prices with GST (v2.0.0)
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
        $wastage = floatval(get_post_meta($product_id, '_jpc_wastage', true));
        
        // Calculate base metal price
        $metal_price = $weight * $metal->price_per_unit;
        
        // Calculate making charges (v2.0.0 - Auto/Manual)
        $making_charge_amount = self::calculate_making_charges($product_id, $metal_price, $metal_id, $weight);
        
        // Calculate wastage charge
        $wastage_charge_amount = 0;
        if ($wastage > 0) {
            $wastage_charge_amount = ($metal_price * $wastage) / 100;
        }
        
        // Calculate diamond cost (v2.0.0 - Dropdown/Manual)
        $diamond_price = self::calculate_diamond_cost($product_id);
        
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
        $subtotal_before_additional = $metal_price + $diamond_price + $making_charge_amount + 
                                      $wastage_charge_amount + $pearl_cost + $stone_cost + 
                                      $extra_fee + $extra_field_costs;
        
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
