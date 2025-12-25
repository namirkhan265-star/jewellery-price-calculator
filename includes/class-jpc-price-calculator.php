<?php
/**
 * Price Calculator - Core Logic
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Price_Calculator {
    
    private static $instance = null;
    
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
    }
    
    /**
     * Calculate and update product price
     */
    public static function calculate_and_update_price($product_id) {
        // Prevent infinite loops
        if (defined('JPC_CALCULATING_PRICE')) {
            return false;
        }
        define('JPC_CALCULATING_PRICE', true);
        
        $product = wc_get_product($product_id);
        
        if (!$product) {
            return false;
        }
        
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
        
        // Calculate subtotal before tax (including diamond price)
        $subtotal = $metal_price + $diamond_price + $making_charge_amount + $wastage_charge_amount + $pearl_cost + $stone_cost + $extra_fee;
        
        // Apply additional percentage if enabled
        $additional_percentage = get_option('jpc_additional_percentage_value', 0);
        if ($additional_percentage > 0) {
            $subtotal += ($subtotal * $additional_percentage) / 100;
        }
        
        // Apply discount if enabled
        $discount_amount = 0;
        if (get_option('jpc_enable_discount') === 'yes') {
            $discount_percentage = floatval(get_post_meta($product_id, '_jpc_discount_percentage', true));
            
            if ($discount_percentage > 0) {
                $discount_on_metals = get_option('jpc_discount_on_metals') === 'yes';
                $discount_on_making = get_option('jpc_discount_on_making') === 'yes';
                $discount_on_wastage = get_option('jpc_discount_on_wastage') === 'yes';
                
                $discountable_amount = 0;
                
                if ($discount_on_metals) {
                    $discountable_amount += $metal_price;
                }
                if ($discount_on_making) {
                    $discountable_amount += $making_charge_amount;
                }
                if ($discount_on_wastage) {
                    $discountable_amount += $wastage_charge_amount;
                }
                
                $discount_amount = ($discountable_amount * $discount_percentage) / 100;
                $subtotal -= $discount_amount;
            }
        }
        
        // Calculate GST
        $gst_amount = 0;
        if (get_option('jpc_enable_gst') === 'yes') {
            $gst_percentage = floatval(get_option('jpc_gst_value', 5));
            
            // Check for metal-specific GST
            $metal_group_name = strtolower($metal_group->name);
            $metal_gst = get_option('jpc_gst_' . $metal_group_name);
            
            if ($metal_gst !== false && $metal_gst !== '') {
                $gst_percentage = floatval($metal_gst);
            }
            
            $gst_amount = ($subtotal * $gst_percentage) / 100;
        }
        
        // Calculate final price
        $final_price = $subtotal + $gst_amount;
        
        // Apply rounding
        $rounding = get_option('jpc_price_rounding', 'default');
        $final_price = self::apply_rounding($final_price, $rounding);
        
        // Get old price for logging
        $old_price = $product->get_regular_price();
        
        // Update product price using direct meta update to avoid recursion
        update_post_meta($product_id, '_regular_price', $final_price);
        update_post_meta($product_id, '_price', $final_price);
        
        // Clear product cache
        wc_delete_product_transients($product_id);
        
        // Log price change
        if ($old_price != $final_price) {
            self::log_product_price_change($product_id, $old_price, $final_price, $metal_id);
        }
        
        // Store price breakup for display
        $breakup = array(
            'metal_price' => $metal_price,
            'diamond_price' => $diamond_price,
            'making_charge' => $making_charge_amount,
            'wastage_charge' => $wastage_charge_amount,
            'pearl_cost' => $pearl_cost,
            'stone_cost' => $stone_cost,
            'extra_fee' => $extra_fee,
            'discount' => $discount_amount,
            'subtotal' => $subtotal,
            'gst' => $gst_amount,
            'final_price' => $final_price,
        );
        
        update_post_meta($product_id, '_jpc_price_breakup', $breakup);
        
        return $final_price;
    }
    
    /**
     * Apply price rounding
     */
    private static function apply_rounding($price, $rounding) {
        switch ($rounding) {
            case 'nearest_10':
                return round($price / 10) * 10;
            case 'nearest_50':
                return round($price / 50) * 50;
            case 'nearest_100':
                return round($price / 100) * 100;
            case 'ceil_10':
                return ceil($price / 10) * 10;
            case 'ceil_50':
                return ceil($price / 50) * 50;
            case 'ceil_100':
                return ceil($price / 100) * 100;
            case 'floor_10':
                return floor($price / 10) * 10;
            case 'floor_50':
                return floor($price / 50) * 50;
            case 'floor_100':
                return floor($price / 100) * 100;
            default:
                return round($price, 2);
        }
    }
    
    /**
     * Log product price change
     */
    private static function log_product_price_change($product_id, $old_price, $new_price, $metal_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_product_price_log';
        
        $wpdb->insert($table, array(
            'product_id' => $product_id,
            'old_price' => $old_price,
            'new_price' => $new_price,
            'metal_id' => $metal_id,
        ));
    }
    
    /**
     * Get product price history
     */
    public static function get_product_price_history($product_id, $limit = 20) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_product_price_log';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE product_id = %d ORDER BY changed_at DESC LIMIT %d",
            $product_id,
            $limit
        ));
    }
}
