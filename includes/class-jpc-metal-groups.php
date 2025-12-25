<?php
/**
 * Metal Groups Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Metal_Groups {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_ajax_jpc_add_metal_group', array($this, 'ajax_add_metal_group'));
        add_action('wp_ajax_jpc_update_metal_group', array($this, 'ajax_update_metal_group'));
        add_action('wp_ajax_jpc_delete_metal_group', array($this, 'ajax_delete_metal_group'));
    }
    
    /**
     * Get all metal groups
     */
    public static function get_all() {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_metal_groups';
        return $wpdb->get_results("SELECT * FROM $table ORDER BY id ASC");
    }
    
    /**
     * Get metal group by ID
     */
    public static function get_by_id($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_metal_groups';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
    }
    
    /**
     * Get metal group by name
     */
    public static function get_by_name($name) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_metal_groups';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE name = %s", $name));
    }
    
    /**
     * Add new metal group
     */
    public static function add($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_metal_groups';
        
        $insert_data = array(
            'name' => sanitize_text_field($data['name']),
            'unit' => sanitize_text_field($data['unit']),
            'enable_making_charge' => isset($data['enable_making_charge']) ? 1 : 0,
            'making_charge_type' => sanitize_text_field($data['making_charge_type']),
            'enable_wastage_charge' => isset($data['enable_wastage_charge']) ? 1 : 0,
            'wastage_charge_type' => sanitize_text_field($data['wastage_charge_type']),
        );
        
        $result = $wpdb->insert($table, $insert_data);
        
        if ($result) {
            return $wpdb->insert_id;
        }
        
        return false;
    }
    
    /**
     * Update metal group
     */
    public static function update($id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_metal_groups';
        
        $update_data = array(
            'name' => sanitize_text_field($data['name']),
            'unit' => sanitize_text_field($data['unit']),
            'enable_making_charge' => isset($data['enable_making_charge']) ? 1 : 0,
            'making_charge_type' => sanitize_text_field($data['making_charge_type']),
            'enable_wastage_charge' => isset($data['enable_wastage_charge']) ? 1 : 0,
            'wastage_charge_type' => sanitize_text_field($data['wastage_charge_type']),
        );
        
        return $wpdb->update($table, $update_data, array('id' => $id));
    }
    
    /**
     * Delete metal group
     */
    public static function delete($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_metal_groups';
        
        // Check if any metals are using this group
        $metals_table = $wpdb->prefix . 'jpc_metals';
        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $metals_table WHERE metal_group_id = %d", $id));
        
        if ($count > 0) {
            return new WP_Error('has_metals', __('Cannot delete metal group. It has associated metals.', 'jewellery-price-calc'));
        }
        
        return $wpdb->delete($table, array('id' => $id));
    }
    
    /**
     * AJAX: Add metal group
     */
    public function ajax_add_metal_group() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Permission denied', 'jewellery-price-calc')));
        }
        
        $data = array(
            'name' => $_POST['name'],
            'unit' => $_POST['unit'],
            'enable_making_charge' => isset($_POST['enable_making_charge']),
            'making_charge_type' => $_POST['making_charge_type'] ?? 'percentage',
            'enable_wastage_charge' => isset($_POST['enable_wastage_charge']),
            'wastage_charge_type' => $_POST['wastage_charge_type'] ?? 'percentage',
        );
        
        $result = self::add($data);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('Metal group added successfully', 'jewellery-price-calc'),
                'id' => $result
            ));
        } else {
            wp_send_json_error(array('message' => __('Failed to add metal group', 'jewellery-price-calc')));
        }
    }
    
    /**
     * AJAX: Update metal group
     */
    public function ajax_update_metal_group() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Permission denied', 'jewellery-price-calc')));
        }
        
        $id = intval($_POST['id']);
        $data = array(
            'name' => $_POST['name'],
            'unit' => $_POST['unit'],
            'enable_making_charge' => isset($_POST['enable_making_charge']),
            'making_charge_type' => $_POST['making_charge_type'] ?? 'percentage',
            'enable_wastage_charge' => isset($_POST['enable_wastage_charge']),
            'wastage_charge_type' => $_POST['wastage_charge_type'] ?? 'percentage',
        );
        
        $result = self::update($id, $data);
        
        if ($result !== false) {
            wp_send_json_success(array('message' => __('Metal group updated successfully', 'jewellery-price-calc')));
        } else {
            wp_send_json_error(array('message' => __('Failed to update metal group', 'jewellery-price-calc')));
        }
    }
    
    /**
     * AJAX: Delete metal group
     */
    public function ajax_delete_metal_group() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Permission denied', 'jewellery-price-calc')));
        }
        
        $id = intval($_POST['id']);
        $result = self::delete($id);
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        } elseif ($result) {
            wp_send_json_success(array('message' => __('Metal group deleted successfully', 'jewellery-price-calc')));
        } else {
            wp_send_json_error(array('message' => __('Failed to delete metal group', 'jewellery-price-calc')));
        }
    }
}
