<?php
/**
 * Diamond Certifications Management Class
 * Manages certification types with fixed or percentage price adjustments
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Diamond_Certifications {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_ajax_jpc_add_diamond_certification', array($this, 'ajax_add_certification'));
        add_action('wp_ajax_jpc_update_diamond_certification', array($this, 'ajax_update_certification'));
        add_action('wp_ajax_jpc_delete_diamond_certification', array($this, 'ajax_delete_certification'));
    }
    
    /**
     * Get all certifications
     */
    public static function get_all() {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_certifications';
        return $wpdb->get_results("SELECT * FROM `$table` ORDER BY name ASC");
    }
    
    /**
     * Get certification by ID
     */
    public static function get_by_id($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_certifications';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM `$table` WHERE id = %d", $id));
    }
    
    /**
     * Get certification by slug
     */
    public static function get_by_slug($slug) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_certifications';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM `$table` WHERE slug = %s", $slug));
    }
    
    /**
     * Add certification
     */
    public static function add($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_certifications';
        
        $slug = sanitize_title($data['name']);
        
        $insert_data = array(
            'name' => sanitize_text_field($data['name']),
            'slug' => $slug,
            'adjustment_type' => sanitize_text_field($data['adjustment_type']), // 'percentage' or 'fixed'
            'adjustment_value' => floatval($data['adjustment_value']),
            'description' => sanitize_textarea_field($data['description']),
        );
        
        $result = $wpdb->insert(
            $table,
            $insert_data,
            array('%s', '%s', '%s', '%f', '%s')
        );
        
        if ($result === false) {
            return new WP_Error('db_error', 'Failed to add certification');
        }
        
        return $wpdb->insert_id;
    }
    
    /**
     * Update certification
     */
    public static function update($id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_certifications';
        
        $slug = sanitize_title($data['name']);
        
        $update_data = array(
            'name' => sanitize_text_field($data['name']),
            'slug' => $slug,
            'adjustment_type' => sanitize_text_field($data['adjustment_type']),
            'adjustment_value' => floatval($data['adjustment_value']),
            'description' => sanitize_textarea_field($data['description']),
        );
        
        $result = $wpdb->update(
            $table,
            $update_data,
            array('id' => $id),
            array('%s', '%s', '%s', '%f', '%s'),
            array('%d')
        );
        
        if ($result === false) {
            return new WP_Error('db_error', 'Failed to update certification');
        }
        
        return true;
    }
    
    /**
     * Delete certification
     */
    public static function delete($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_certifications';
        
        $result = $wpdb->delete($table, array('id' => $id), array('%d'));
        
        if ($result === false) {
            return new WP_Error('db_error', 'Failed to delete certification');
        }
        
        return true;
    }
    
    /**
     * Calculate adjusted price
     */
    public static function calculate_adjusted_price($base_price, $certification_id) {
        if (!$certification_id) {
            return $base_price;
        }
        
        $cert = self::get_by_id($certification_id);
        if (!$cert) {
            return $base_price;
        }
        
        if ($cert->adjustment_type === 'percentage') {
            return $base_price * (1 + ($cert->adjustment_value / 100));
        } else {
            return $base_price + $cert->adjustment_value;
        }
    }
    
    /**
     * AJAX: Add certification
     */
    public function ajax_add_certification() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $data = array(
            'name' => sanitize_text_field($_POST['name']),
            'adjustment_type' => sanitize_text_field($_POST['adjustment_type']),
            'adjustment_value' => floatval($_POST['adjustment_value']),
            'description' => sanitize_textarea_field($_POST['description']),
        );
        
        $result = self::add($data);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(array(
            'id' => $result,
            'message' => 'Certification added successfully'
        ));
    }
    
    /**
     * AJAX: Update certification
     */
    public function ajax_update_certification() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $id = intval($_POST['id']);
        $data = array(
            'name' => sanitize_text_field($_POST['name']),
            'adjustment_type' => sanitize_text_field($_POST['adjustment_type']),
            'adjustment_value' => floatval($_POST['adjustment_value']),
            'description' => sanitize_textarea_field($_POST['description']),
        );
        
        $result = self::update($id, $data);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success('Certification updated successfully');
    }
    
    /**
     * AJAX: Delete certification
     */
    public function ajax_delete_certification() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $id = intval($_POST['id']);
        $result = self::delete($id);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success('Certification deleted successfully');
    }
    
    /**
     * Get default certifications for initial setup
     */
    public static function get_default_certifications() {
        return array(
            array(
                'name' => 'GIA',
                'slug' => 'gia',
                'adjustment_type' => 'percentage',
                'adjustment_value' => 20,
                'description' => 'Gemological Institute of America - Premium certification',
            ),
            array(
                'name' => 'IGI',
                'slug' => 'igi',
                'adjustment_type' => 'percentage',
                'adjustment_value' => 15,
                'description' => 'International Gemological Institute',
            ),
            array(
                'name' => 'HRD',
                'slug' => 'hrd',
                'adjustment_type' => 'percentage',
                'adjustment_value' => 18,
                'description' => 'HRD Antwerp - High quality certification',
            ),
            array(
                'name' => 'None',
                'slug' => 'none',
                'adjustment_type' => 'percentage',
                'adjustment_value' => 0,
                'description' => 'No certification',
            ),
        );
    }
}
