<?php
/**
 * Diamond Types Management Class
 * Manages diamond types with carat-based pricing
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Diamond_Types {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_ajax_jpc_add_diamond_type', array($this, 'ajax_add_diamond_type'));
        add_action('wp_ajax_jpc_update_diamond_type', array($this, 'ajax_update_diamond_type'));
        add_action('wp_ajax_jpc_delete_diamond_type', array($this, 'ajax_delete_diamond_type'));
        add_action('wp_ajax_jpc_get_diamond_types_by_group', array($this, 'ajax_get_diamond_types_by_group'));
    }
    
    /**
     * Get all diamond types
     */
    public static function get_all() {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_types';
        return $wpdb->get_results("SELECT * FROM `$table` ORDER BY diamond_group_id, carat_from ASC");
    }
    
    /**
     * Get diamond type by ID
     */
    public static function get_by_id($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_types';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM `$table` WHERE id = %d", $id));
    }
    
    /**
     * Get diamond types by group
     */
    public static function get_by_group($group_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_types';
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM `$table` WHERE diamond_group_id = %d ORDER BY carat_from ASC",
            $group_id
        ));
    }
    
    /**
     * Get diamond type by group and carat
     */
    public static function get_by_group_and_carat($group_id, $carat) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_types';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM `$table` 
            WHERE diamond_group_id = %d 
            AND carat_from <= %f 
            AND carat_to >= %f 
            ORDER BY carat_from ASC 
            LIMIT 1",
            $group_id,
            $carat,
            $carat
        ));
    }
    
    /**
     * Add diamond type
     */
    public static function add($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_types';
        
        $insert_data = array(
            'diamond_group_id' => intval($data['diamond_group_id']),
            'carat_from' => floatval($data['carat_from']),
            'carat_to' => floatval($data['carat_to']),
            'price_per_carat' => floatval($data['price_per_carat']),
            'display_name' => sanitize_text_field($data['display_name']),
        );
        
        $result = $wpdb->insert(
            $table,
            $insert_data,
            array('%d', '%f', '%f', '%f', '%s')
        );
        
        if ($result === false) {
            return new WP_Error('db_error', 'Failed to add diamond type');
        }
        
        return $wpdb->insert_id;
    }
    
    /**
     * Update diamond type
     */
    public static function update($id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_types';
        
        $update_data = array(
            'diamond_group_id' => intval($data['diamond_group_id']),
            'carat_from' => floatval($data['carat_from']),
            'carat_to' => floatval($data['carat_to']),
            'price_per_carat' => floatval($data['price_per_carat']),
            'display_name' => sanitize_text_field($data['display_name']),
        );
        
        $result = $wpdb->update(
            $table,
            $update_data,
            array('id' => $id),
            array('%d', '%f', '%f', '%f', '%s'),
            array('%d')
        );
        
        if ($result === false) {
            return new WP_Error('db_error', 'Failed to update diamond type');
        }
        
        return true;
    }
    
    /**
     * Delete diamond type
     */
    public static function delete($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_types';
        
        $result = $wpdb->delete($table, array('id' => $id), array('%d'));
        
        if ($result === false) {
            return new WP_Error('db_error', 'Failed to delete diamond type');
        }
        
        return true;
    }
    
    /**
     * Calculate price for specific diamond
     */
    public static function calculate_price($group_id, $carat, $quantity, $certification_id = null) {
        // Get diamond type for this carat range
        $diamond_type = self::get_by_group_and_carat($group_id, $carat);
        
        if (!$diamond_type) {
            return new WP_Error('not_found', 'No diamond type found for this carat range');
        }
        
        $price_per_carat = floatval($diamond_type->price_per_carat);
        
        // Apply certification adjustment if provided
        if ($certification_id) {
            $cert = JPC_Diamond_Certifications::get_by_id($certification_id);
            if ($cert) {
                if ($cert->adjustment_type === 'percentage') {
                    $price_per_carat = $price_per_carat * (1 + ($cert->adjustment_value / 100));
                } else {
                    $price_per_carat = $price_per_carat + $cert->adjustment_value;
                }
            }
        }
        
        $unit_price = $price_per_carat * $carat;
        $total_price = $unit_price * $quantity;
        
        return array(
            'diamond_type_id' => $diamond_type->id,
            'price_per_carat' => $price_per_carat,
            'unit_price' => $unit_price,
            'total_price' => $total_price,
            'total_carat' => $carat * $quantity,
        );
    }
    
    /**
     * AJAX: Add diamond type
     */
    public function ajax_add_diamond_type() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $data = array(
            'diamond_group_id' => intval($_POST['diamond_group_id']),
            'carat_from' => floatval($_POST['carat_from']),
            'carat_to' => floatval($_POST['carat_to']),
            'price_per_carat' => floatval($_POST['price_per_carat']),
            'display_name' => sanitize_text_field($_POST['display_name']),
        );
        
        $result = self::add($data);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(array(
            'id' => $result,
            'message' => 'Diamond type added successfully'
        ));
    }
    
    /**
     * AJAX: Update diamond type
     */
    public function ajax_update_diamond_type() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $id = intval($_POST['id']);
        $data = array(
            'diamond_group_id' => intval($_POST['diamond_group_id']),
            'carat_from' => floatval($_POST['carat_from']),
            'carat_to' => floatval($_POST['carat_to']),
            'price_per_carat' => floatval($_POST['price_per_carat']),
            'display_name' => sanitize_text_field($_POST['display_name']),
        );
        
        $result = self::update($id, $data);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success('Diamond type updated successfully');
    }
    
    /**
     * AJAX: Delete diamond type
     */
    public function ajax_delete_diamond_type() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $id = intval($_POST['id']);
        $result = self::delete($id);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success('Diamond type deleted successfully');
    }
    
    /**
     * AJAX: Get diamond types by group
     */
    public function ajax_get_diamond_types_by_group() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        $group_id = intval($_POST['group_id']);
        $diamond_types = self::get_by_group($group_id);
        
        wp_send_json_success($diamond_types);
    }
    
    /**
     * Get default diamond types for initial setup
     */
    public static function get_default_types() {
        return array(
            // Natural Diamond ranges
            array(
                'group_slug' => 'natural',
                'carat_from' => 0.00,
                'carat_to' => 0.50,
                'price_per_carat' => 25000,
                'display_name' => 'Natural Diamond (0-0.5ct)',
            ),
            array(
                'group_slug' => 'natural',
                'carat_from' => 0.50,
                'carat_to' => 1.00,
                'price_per_carat' => 32500,
                'display_name' => 'Natural Diamond (0.5-1ct)',
            ),
            array(
                'group_slug' => 'natural',
                'carat_from' => 1.00,
                'carat_to' => 2.00,
                'price_per_carat' => 45000,
                'display_name' => 'Natural Diamond (1-2ct)',
            ),
            array(
                'group_slug' => 'natural',
                'carat_from' => 2.00,
                'carat_to' => 3.00,
                'price_per_carat' => 62500,
                'display_name' => 'Natural Diamond (2-3ct)',
            ),
            array(
                'group_slug' => 'natural',
                'carat_from' => 3.00,
                'carat_to' => 999.99,
                'price_per_carat' => 87500,
                'display_name' => 'Natural Diamond (3ct+)',
            ),
            
            // Lab Grown Diamond ranges
            array(
                'group_slug' => 'lab_grown',
                'carat_from' => 0.00,
                'carat_to' => 0.50,
                'price_per_carat' => 15000,
                'display_name' => 'Lab Grown Diamond (0-0.5ct)',
            ),
            array(
                'group_slug' => 'lab_grown',
                'carat_from' => 0.50,
                'carat_to' => 1.00,
                'price_per_carat' => 19500,
                'display_name' => 'Lab Grown Diamond (0.5-1ct)',
            ),
            array(
                'group_slug' => 'lab_grown',
                'carat_from' => 1.00,
                'carat_to' => 2.00,
                'price_per_carat' => 27000,
                'display_name' => 'Lab Grown Diamond (1-2ct)',
            ),
            array(
                'group_slug' => 'lab_grown',
                'carat_from' => 2.00,
                'carat_to' => 999.99,
                'price_per_carat' => 37500,
                'display_name' => 'Lab Grown Diamond (2ct+)',
            ),
            
            // Moissanite ranges
            array(
                'group_slug' => 'moissanite',
                'carat_from' => 0.00,
                'carat_to' => 1.00,
                'price_per_carat' => 5000,
                'display_name' => 'Moissanite (0-1ct)',
            ),
            array(
                'group_slug' => 'moissanite',
                'carat_from' => 1.00,
                'carat_to' => 999.99,
                'price_per_carat' => 6500,
                'display_name' => 'Moissanite (1ct+)',
            ),
        );
    }
}
