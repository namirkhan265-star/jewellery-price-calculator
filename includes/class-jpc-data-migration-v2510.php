<?php
/**
 * Data Migration v2.5.10
 * 
 * Migrates additional cost fields from old format to new format:
 * OLD: _jpc_pearl_cost, _jpc_stone_cost, _jpc_extra_fee
 * NEW: _jpc_pearl_cost_value + _jpc_pearl_cost_type, etc.
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Data_Migration_v2510 {
    
    /**
     * Run migration
     */
    public static function migrate() {
        global $wpdb;
        
        // Get all products with jewellery calculator data
        $products = $wpdb->get_results("
            SELECT DISTINCT post_id 
            FROM {$wpdb->postmeta} 
            WHERE meta_key IN ('_jpc_pearl_cost', '_jpc_stone_cost', '_jpc_extra_fee')
            AND meta_value != '' 
            AND meta_value != '0'
        ");
        
        $migrated_count = 0;
        
        foreach ($products as $product) {
            $post_id = $product->post_id;
            
            // Migrate Pearl Cost
            $pearl_cost = get_post_meta($post_id, '_jpc_pearl_cost', true);
            if ($pearl_cost && $pearl_cost > 0) {
                $pearl_cost_type = get_option('jpc_pearl_cost_type', 'fixed');
                update_post_meta($post_id, '_jpc_pearl_cost_value', floatval($pearl_cost));
                update_post_meta($post_id, '_jpc_pearl_cost_type', $pearl_cost_type);
                delete_post_meta($post_id, '_jpc_pearl_cost'); // Remove old key
            }
            
            // Migrate Stone Cost
            $stone_cost = get_post_meta($post_id, '_jpc_stone_cost', true);
            if ($stone_cost && $stone_cost > 0) {
                $stone_cost_type = get_option('jpc_stone_cost_type', 'fixed');
                update_post_meta($post_id, '_jpc_stone_cost_value', floatval($stone_cost));
                update_post_meta($post_id, '_jpc_stone_cost_type', $stone_cost_type);
                delete_post_meta($post_id, '_jpc_stone_cost'); // Remove old key
            }
            
            // Migrate Extra Fee
            $extra_fee = get_post_meta($post_id, '_jpc_extra_fee', true);
            if ($extra_fee && $extra_fee > 0) {
                $extra_fee_type = get_option('jpc_extra_fee_type', 'fixed');
                update_post_meta($post_id, '_jpc_extra_fee_value', floatval($extra_fee));
                update_post_meta($post_id, '_jpc_extra_fee_type', $extra_fee_type);
                delete_post_meta($post_id, '_jpc_extra_fee'); // Remove old key
            }
            
            // Regenerate price breakup with new data
            JPC_Price_Calculator::calculate_and_store_breakup($post_id);
            
            $migrated_count++;
        }
        
        // Mark migration as complete
        update_option('jpc_migration_v2510_completed', true);
        update_option('jpc_migration_v2510_count', $migrated_count);
        update_option('jpc_migration_v2510_date', current_time('mysql'));
        
        return array(
            'success' => true,
            'migrated_count' => $migrated_count,
            'message' => sprintf(__('%d products migrated successfully', 'jewellery-price-calc'), $migrated_count)
        );
    }
    
    /**
     * Check if migration is needed
     */
    public static function is_migration_needed() {
        // Check if already migrated
        if (get_option('jpc_migration_v2510_completed')) {
            return false;
        }
        
        global $wpdb;
        
        // Check if there are any products with old meta keys
        $count = $wpdb->get_var("
            SELECT COUNT(DISTINCT post_id) 
            FROM {$wpdb->postmeta} 
            WHERE meta_key IN ('_jpc_pearl_cost', '_jpc_stone_cost', '_jpc_extra_fee')
            AND meta_value != '' 
            AND meta_value != '0'
        ");
        
        return $count > 0;
    }
}
