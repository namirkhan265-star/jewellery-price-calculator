<?php
/**
 * Diamond Management Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Diamonds {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_ajax_jpc_add_diamond', array($this, 'ajax_add_diamond'));
        add_action('wp_ajax_jpc_update_diamond', array($this, 'ajax_update_diamond'));
        add_action('wp_ajax_jpc_delete_diamond', array($this, 'ajax_delete_diamond'));
    }
    
    /**
     * Get all diamonds
     */
    public static function get_all() {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamonds';
        return $wpdb->get_results("SELECT * FROM `$table` ORDER BY type, carat ASC");
    }
    
    /**
     * Get diamond by ID
     */
    public static function get_by_id($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamonds';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM `$table` WHERE id = %d", $id));
    }
    
    /**
     * Get diamonds by type
     */
    public static function get_by_type($type) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamonds';
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM `$table` WHERE type = %s ORDER BY carat ASC", $type));
    }
    
    /**
     * Get diamond types
     */
    public static function get_types() {
        return array(
            'natural' => __('Natural Diamond', 'jewellery-price-calc'),
            'lab_grown' => __('Lab Grown Diamond', 'jewellery-price-calc'),
            'moissanite' => __('Moissanite', 'jewellery-price-calc'),
        );
    }
    
    /**
     * Get certification types
     */
    public static function get_certifications() {
        return array(
            'gia' => __('GIA', 'jewellery-price-calc'),
            'igi' => __('IGI', 'jewellery-price-calc'),
            'hrd' => __('HRD', 'jewellery-price-calc'),
            'none' => __('None', 'jewellery-price-calc'),
        );
    }
    
    /**
     * Get common carat sizes
     */
    public static function get_carat_sizes() {
        return array(
            '0.25', '0.30', '0.40', '0.50', '0.60', '0.70', '0.75', '0.80', '0.90',
            '1.00', '1.25', '1.50', '1.75', '2.00', '2.50', '3.00', '3.50', '4.00', '5.00'
        );
    }
    
    /**
     * Add diamond
     */
    public static function add($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamonds';
        
        $insert_data = array(
            'type' => sanitize_text_field($data['type']),
            'carat' => floatval($data['carat']),
            'certification' => sanitize_text_field($data['certification']),
            'price_per_carat' => floatval($data['price_per_carat']),
            'display_name' => sanitize_text_field($data['display_name']),
        );
        
        error_log('JPC: Attempting to insert diamond: ' . print_r($insert_data, true));
        
        $result = $wpdb->insert($table, $insert_data);
        
        if ($result) {
            error_log('JPC: Diamond inserted successfully with ID: ' . $wpdb->insert_id);
            return $wpdb->insert_id;
        } else {
            error_log('JPC: Failed to insert diamond. Error: ' . $wpdb->last_error);
            return false;
        }
    }
    
    /**
     * Update diamond
     */
    public static function update($id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamonds';
        
        $update_data = array(
            'type' => sanitize_text_field($data['type']),
            'carat' => floatval($data['carat']),
            'certification' => sanitize_text_field($data['certification']),
            'price_per_carat' => floatval($data['price_per_carat']),
            'display_name' => sanitize_text_field($data['display_name']),
        );
        
        return $wpdb->update($table, $update_data, array('id' => $id));
    }
    
    /**
     * Delete diamond
     */
    public static function delete($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamonds';
        return $wpdb->delete($table, array('id' => $id));
    }
    
    /**
     * Get price for specific diamond configuration
     */
    public static function get_price($type, $carat, $certification) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamonds';
        
        $diamond = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM `$table` WHERE type = %s AND carat = %f AND certification = %s",
            $type, $carat, $certification
        ));
        
        if ($diamond) {
            return $diamond->price_per_carat;
        }
        
        return 0;
    }
    
    /**
     * AJAX: Add diamond
     */
    public function ajax_add_diamond() {
        error_log('JPC: ajax_add_diamond called');
        error_log('JPC: POST data: ' . print_r($_POST, true));
        
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            error_log('JPC: Permission denied for user');
            wp_send_json_error(array('message' => __('Permission denied', 'jewellery-price-calc')));
            return;
        }
        
        // Validate required fields
        if (empty($_POST['type']) || empty($_POST['carat']) || empty($_POST['certification']) || 
            empty($_POST['display_name']) || empty($_POST['price_per_carat'])) {
            error_log('JPC: Missing required fields');
            wp_send_json_error(array('message' => __('All fields are required', 'jewellery-price-calc')));
            return;
        }
        
        $data = array(
            'type' => $_POST['type'],
            'carat' => $_POST['carat'],
            'certification' => $_POST['certification'],
            'price_per_carat' => $_POST['price_per_carat'],
            'display_name' => $_POST['display_name'],
        );
        
        $id = self::add($data);
        
        if ($id) {
            error_log('JPC: Diamond added successfully with ID: ' . $id);
            wp_send_json_success(array(
                'message' => __('Diamond added successfully', 'jewellery-price-calc'),
                'id' => $id
            ));
        } else {
            error_log('JPC: Failed to add diamond');
            wp_send_json_error(array('message' => __('Failed to add diamond. Check error log.', 'jewellery-price-calc')));
        }
    }
    
    /**
     * AJAX: Update diamond
     */
    public function ajax_update_diamond() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Permission denied', 'jewellery-price-calc')));
        }
        
        $id = intval($_POST['id']);
        $data = array(
            'type' => $_POST['type'],
            'carat' => $_POST['carat'],
            'certification' => $_POST['certification'],
            'price_per_carat' => $_POST['price_per_carat'],
            'display_name' => $_POST['display_name'],
        );
        
        $result = self::update($id, $data);
        
        if ($result !== false) {
            wp_send_json_success(array('message' => __('Diamond updated successfully', 'jewellery-price-calc')));
        } else {
            wp_send_json_error(array('message' => __('Failed to update diamond', 'jewellery-price-calc')));
        }
    }
    
    /**
     * AJAX: Delete diamond
     */
    public function ajax_delete_diamond() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Permission denied', 'jewellery-price-calc')));
        }
        
        $id = intval($_POST['id']);
        $result = self::delete($id);
        
        if ($result) {
            wp_send_json_success(array('message' => __('Diamond deleted successfully', 'jewellery-price-calc')));
        } else {
            wp_send_json_error(array('message' => __('Failed to delete diamond', 'jewellery-price-calc')));
        }
    }
}
