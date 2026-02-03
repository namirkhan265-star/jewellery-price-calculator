<?php
/**
 * Diamond Colour Management Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Diamond_Colours {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_ajax_jpc_add_diamond_colour', array($this, 'ajax_add_colour'));
        add_action('wp_ajax_jpc_update_diamond_colour', array($this, 'ajax_update_colour'));
        add_action('wp_ajax_jpc_delete_diamond_colour', array($this, 'ajax_delete_colour'));
    }
    
    /**
     * Get all colours
     */
    public static function get_all() {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_colours';
        return $wpdb->get_results("SELECT * FROM `$table` ORDER BY name ASC");
    }
    
    /**
     * Get colour by ID
     */
    public static function get_by_id($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_colours';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM `$table` WHERE id = %d", $id));
    }
    
    /**
     * Get colour by slug
     */
    public static function get_by_slug($slug) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_colours';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM `$table` WHERE slug = %s", $slug));
    }
    
    /**
     * Add new colour
     */
    public static function add($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_colours';
        
        $insert_data = array(
            'name' => sanitize_text_field($data['name']),
            'slug' => sanitize_title($data['slug'] ?: $data['name']),
            'adjustment_type' => sanitize_text_field($data['adjustment_type']),
            'adjustment_value' => floatval($data['adjustment_value']),
            'description' => sanitize_textarea_field($data['description'] ?? '')
        );
        
        $result = $wpdb->insert($table, $insert_data);
        
        if ($result === false) {
            return false;
        }
        
        return $wpdb->insert_id;
    }
    
    /**
     * Update colour
     */
    public static function update($id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_colours';
        
        $update_data = array(
            'name' => sanitize_text_field($data['name']),
            'slug' => sanitize_title($data['slug'] ?: $data['name']),
            'adjustment_type' => sanitize_text_field($data['adjustment_type']),
            'adjustment_value' => floatval($data['adjustment_value']),
            'description' => sanitize_textarea_field($data['description'] ?? '')
        );
        
        return $wpdb->update($table, $update_data, array('id' => $id));
    }
    
    /**
     * Delete colour
     */
    public static function delete($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_colours';
        return $wpdb->delete($table, array('id' => $id));
    }
    
    /**
     * AJAX: Add colour
     */
    public function ajax_add_colour() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }
        
        $result = self::add($_POST);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => 'Diamond colour added successfully',
                'id' => $result
            ));
        } else {
            wp_send_json_error(array('message' => 'Failed to add diamond colour'));
        }
    }
    
    /**
     * AJAX: Update colour
     */
    public function ajax_update_colour() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }
        
        $id = intval($_POST['id']);
        $result = self::update($id, $_POST);
        
        if ($result !== false) {
            wp_send_json_success(array('message' => 'Diamond colour updated successfully'));
        } else {
            wp_send_json_error(array('message' => 'Failed to update diamond colour'));
        }
    }
    
    /**
     * AJAX: Delete colour
     */
    public function ajax_delete_colour() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }
        
        $id = intval($_POST['id']);
        $result = self::delete($id);
        
        if ($result) {
            wp_send_json_success(array('message' => 'Diamond colour deleted successfully'));
        } else {
            wp_send_json_error(array('message' => 'Failed to delete diamond colour'));
        }
    }
}
