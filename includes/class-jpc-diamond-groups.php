<?php
/**
 * Diamond Groups Management Class
 * Similar to Metal Groups - manages diamond type categories
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Diamond_Groups {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_ajax_jpc_add_diamond_group', array($this, 'ajax_add_diamond_group'));
        add_action('wp_ajax_jpc_update_diamond_group', array($this, 'ajax_update_diamond_group'));
        add_action('wp_ajax_jpc_delete_diamond_group', array($this, 'ajax_delete_diamond_group'));
    }
    
    /**
     * Get all diamond groups
     */
    public static function get_all() {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_groups';
        return $wpdb->get_results("SELECT * FROM `$table` ORDER BY name ASC");
    }
    
    /**
     * Get diamond group by ID
     */
    public static function get_by_id($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_groups';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM `$table` WHERE id = %d", $id));
    }
    
    /**
     * Get diamond group by slug
     */
    public static function get_by_slug($slug) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_groups';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM `$table` WHERE slug = %s", $slug));
    }
    
    /**
     * Add diamond group
     */
    public static function add($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_groups';
        
        $slug = sanitize_title($data['name']);
        
        $insert_data = array(
            'name' => sanitize_text_field($data['name']),
            'slug' => $slug,
            'description' => sanitize_textarea_field($data['description']),
        );
        
        $result = $wpdb->insert($table, $insert_data, array('%s', '%s', '%s'));
        
        if ($result === false) {
            return new WP_Error('db_error', 'Failed to add diamond group');
        }
        
        return $wpdb->insert_id;
    }
    
    /**
     * Update diamond group
     */
    public static function update($id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_groups';
        
        $slug = sanitize_title($data['name']);
        
        $update_data = array(
            'name' => sanitize_text_field($data['name']),
            'slug' => $slug,
            'description' => sanitize_textarea_field($data['description']),
        );
        
        $result = $wpdb->update(
            $table,
            $update_data,
            array('id' => $id),
            array('%s', '%s', '%s'),
            array('%d')
        );
        
        if ($result === false) {
            return new WP_Error('db_error', 'Failed to update diamond group');
        }
        
        return true;
    }
    
    /**
     * Delete diamond group
     */
    public static function delete($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_groups';
        
        // Check if any diamonds are using this group
        $diamonds_table = $wpdb->prefix . 'jpc_diamond_types';
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM `$diamonds_table` WHERE diamond_group_id = %d",
            $id
        ));
        
        if ($count > 0) {
            return new WP_Error('in_use', 'Cannot delete diamond group that is in use by diamond types');
        }
        
        $result = $wpdb->delete($table, array('id' => $id), array('%d'));
        
        if ($result === false) {
            return new WP_Error('db_error', 'Failed to delete diamond group');
        }
        
        return true;
    }
    
    /**
     * AJAX: Add diamond group
     */
    public function ajax_add_diamond_group() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $data = array(
            'name' => sanitize_text_field($_POST['name']),
            'description' => sanitize_textarea_field($_POST['description']),
        );
        
        $result = self::add($data);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(array(
            'id' => $result,
            'message' => 'Diamond group added successfully'
        ));
    }
    
    /**
     * AJAX: Update diamond group
     */
    public function ajax_update_diamond_group() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $id = intval($_POST['id']);
        $data = array(
            'name' => sanitize_text_field($_POST['name']),
            'description' => sanitize_textarea_field($_POST['description']),
        );
        
        $result = self::update($id, $data);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success('Diamond group updated successfully');
    }
    
    /**
     * AJAX: Delete diamond group
     */
    public function ajax_delete_diamond_group() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $id = intval($_POST['id']);
        $result = self::delete($id);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success('Diamond group deleted successfully');
    }
    
    /**
     * Get default diamond groups for initial setup
     */
    public static function get_default_groups() {
        return array(
            array(
                'name' => 'Natural Diamond',
                'slug' => 'natural',
                'description' => 'Naturally mined diamonds formed over billions of years',
            ),
            array(
                'name' => 'Lab Grown Diamond',
                'slug' => 'lab_grown',
                'description' => 'Laboratory-created diamonds with same properties as natural diamonds',
            ),
            array(
                'name' => 'Moissanite',
                'slug' => 'moissanite',
                'description' => 'Silicon carbide gemstone with diamond-like appearance',
            ),
        );
    }
}
