<?php
/**
 * Diamond Management Class
 * Integrates with new 3-tab diamond system (Groups, Types, Certifications)
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
        add_action('wp_ajax_jpc_calculate_diamond_price', array($this, 'ajax_calculate_diamond_price'));
        add_action('wp_ajax_jpc_sync_legacy_diamonds', array($this, 'ajax_sync_legacy_diamonds'));
    }
    
    /**
     * Get all diamonds from legacy table
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
     * Get diamond groups from new system
     */
    public static function get_diamond_groups() {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_groups';
        $groups = $wpdb->get_results("SELECT * FROM `$table` ORDER BY name ASC");
        
        $result = array();
        foreach ($groups as $group) {
            $result[$group->slug] = $group->name;
        }
        return $result;
    }
    
    /**
     * Get diamond types (for backward compatibility)
     */
    public static function get_types() {
        // Try to get from new system first
        $groups = self::get_diamond_groups();
        if (!empty($groups)) {
            return $groups;
        }
        
        // Fallback to hardcoded
        return array(
            'natural-diamond' => __('Natural Diamond', 'jewellery-price-calc'),
            'lab-grown-diamond' => __('Lab Grown Diamond', 'jewellery-price-calc'),
            'moissanite' => __('Moissanite', 'jewellery-price-calc'),
        );
    }
    
    /**
     * Get certifications from new system
     */
    public static function get_certifications_from_system() {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_certifications';
        $certs = $wpdb->get_results("SELECT * FROM `$table` ORDER BY name ASC");
        
        $result = array();
        foreach ($certs as $cert) {
            $result[$cert->slug] = $cert->name;
        }
        return $result;
    }
    
    /**
     * Get certification types (for backward compatibility)
     */
    public static function get_certifications() {
        // Try to get from new system first
        $certs = self::get_certifications_from_system();
        if (!empty($certs)) {
            return $certs;
        }
        
        // Fallback to hardcoded
        return array(
            'gia' => __('GIA', 'jewellery-price-calc'),
            'igi' => __('IGI', 'jewellery-price-calc'),
            'hrd' => __('HRD', 'jewellery-price-calc'),
            'none' => __('None', 'jewellery-price-calc'),
        );
    }
    
    /**
     * Get carat sizes from diamond types table
     */
    public static function get_carat_sizes() {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_types';
        
        // Get all unique carat boundaries
        $carats = $wpdb->get_results("
            SELECT DISTINCT carat_from, carat_to 
            FROM `$table` 
            ORDER BY carat_from ASC
        ");
        
        if (empty($carats)) {
            // Fallback to default sizes
            return array(
                '0.25', '0.30', '0.40', '0.50', '0.60', '0.70', '0.75', '0.80', '0.90',
                '1.00', '1.25', '1.50', '1.75', '2.00', '2.50', '3.00', '3.50', '4.00', '5.00'
            );
        }
        
        // Generate common sizes within ranges
        $sizes = array();
        foreach ($carats as $range) {
            $from = floatval($range->carat_from);
            $to = floatval($range->carat_to);
            
            // Add some common sizes in this range
            if ($from < 0.5) {
                $sizes[] = '0.25';
                $sizes[] = '0.30';
                $sizes[] = '0.40';
                $sizes[] = '0.50';
            } elseif ($from < 1.0) {
                $sizes[] = '0.60';
                $sizes[] = '0.70';
                $sizes[] = '0.75';
                $sizes[] = '0.80';
                $sizes[] = '0.90';
                $sizes[] = '1.00';
            } elseif ($from < 2.0) {
                $sizes[] = '1.25';
                $sizes[] = '1.50';
                $sizes[] = '1.75';
                $sizes[] = '2.00';
            } elseif ($from < 3.0) {
                $sizes[] = '2.50';
                $sizes[] = '3.00';
            } else {
                $sizes[] = '3.50';
                $sizes[] = '4.00';
                $sizes[] = '5.00';
            }
        }
        
        return array_unique($sizes);
    }
    
    /**
     * Calculate diamond price based on new 3-tab system
     */
    public static function calculate_price($group_slug, $carat, $certification_slug) {
        global $wpdb;
        
        // Get diamond group ID
        $group = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM `{$wpdb->prefix}jpc_diamond_groups` WHERE slug = %s",
            $group_slug
        ));
        
        if (!$group) {
            return array('error' => 'Diamond group not found');
        }
        
        // Find matching carat range in diamond types
        $diamond_type = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM `{$wpdb->prefix}jpc_diamond_types` 
            WHERE diamond_group_id = %d 
            AND carat_from <= %f 
            AND carat_to >= %f
            ORDER BY carat_from ASC
            LIMIT 1",
            $group->id,
            floatval($carat),
            floatval($carat)
        ));
        
        if (!$diamond_type) {
            return array('error' => 'No price range found for this carat weight');
        }
        
        $base_price = floatval($diamond_type->price_per_carat);
        
        // Get certification adjustment
        $certification = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM `{$wpdb->prefix}jpc_diamond_certifications` WHERE slug = %s",
            $certification_slug
        ));
        
        if (!$certification) {
            return array('error' => 'Certification not found');
        }
        
        // Apply certification adjustment
        $final_price = $base_price;
        if ($certification->adjustment_type === 'percentage') {
            $final_price = $base_price * (1 + ($certification->adjustment_value / 100));
        } else {
            $final_price = $base_price + $certification->adjustment_value;
        }
        
        return array(
            'base_price' => $base_price,
            'certification_adjustment' => $certification->adjustment_value,
            'adjustment_type' => $certification->adjustment_type,
            'final_price_per_carat' => $final_price,
            'total_price' => $final_price * floatval($carat),
            'carat_range' => $diamond_type->carat_from . '-' . $diamond_type->carat_to,
            'display_name' => $diamond_type->display_name
        );
    }
    
    /**
     * Add diamond to legacy table
     */
    public static function add($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamonds';
        
        // If price not provided, calculate it
        if (empty($data['price_per_carat']) || $data['price_per_carat'] == 0) {
            $calc = self::calculate_price(
                $data['type'],
                $data['carat'],
                $data['certification']
            );
            
            if (isset($calc['error'])) {
                error_log('JPC: Price calculation error: ' . $calc['error']);
                return false;
            }
            
            $data['price_per_carat'] = $calc['final_price_per_carat'];
        }
        
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
            $type,
            floatval($carat),
            $certification
        ));
        
        if ($diamond) {
            return floatval($diamond->price_per_carat);
        }
        
        // If not found in legacy table, calculate from new system
        $calc = self::calculate_price($type, $carat, $certification);
        return isset($calc['final_price_per_carat']) ? $calc['final_price_per_carat'] : 0;
    }
    
    /**
     * AJAX: Calculate diamond price
     */
    public function ajax_calculate_diamond_price() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        $group_slug = sanitize_text_field($_POST['group_slug']);
        $carat = floatval($_POST['carat']);
        $cert_slug = sanitize_text_field($_POST['cert_slug']);
        
        $result = self::calculate_price($group_slug, $carat, $cert_slug);
        
        if (isset($result['error'])) {
            wp_send_json_error($result);
        } else {
            wp_send_json_success($result);
        }
    }
    
    /**
     * AJAX: Add diamond
     */
    public function ajax_add_diamond() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }
        
        $data = array(
            'type' => sanitize_text_field($_POST['type']),
            'carat' => floatval($_POST['carat']),
            'certification' => sanitize_text_field($_POST['certification']),
            'price_per_carat' => floatval($_POST['price_per_carat']),
            'display_name' => sanitize_text_field($_POST['display_name']),
        );
        
        error_log('JPC AJAX: Received add diamond request: ' . print_r($data, true));
        
        $id = self::add($data);
        
        if ($id) {
            error_log('JPC AJAX: Diamond added successfully with ID: ' . $id);
            wp_send_json_success(array(
                'message' => 'Diamond added successfully',
                'id' => $id
            ));
        } else {
            error_log('JPC AJAX: Failed to add diamond');
            wp_send_json_error(array('message' => 'Failed to add diamond'));
        }
    }
    
    /**
     * AJAX: Update diamond
     */
    public function ajax_update_diamond() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }
        
        $id = intval($_POST['id']);
        $data = array(
            'type' => sanitize_text_field($_POST['type']),
            'carat' => floatval($_POST['carat']),
            'certification' => sanitize_text_field($_POST['certification']),
            'price_per_carat' => floatval($_POST['price_per_carat']),
            'display_name' => sanitize_text_field($_POST['display_name']),
        );
        
        if (self::update($id, $data)) {
            wp_send_json_success(array('message' => 'Diamond updated successfully'));
        } else {
            wp_send_json_error(array('message' => 'Failed to update diamond'));
        }
    }
    
    /**
     * AJAX: Delete diamond
     */
    public function ajax_delete_diamond() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }
        
        $id = intval($_POST['id']);
        
        if (self::delete($id)) {
            wp_send_json_success(array('message' => 'Diamond deleted successfully'));
        } else {
            wp_send_json_error(array('message' => 'Failed to delete diamond'));
        }
    }
    
    /**
     * AJAX: Sync legacy diamonds from new system
     */
    public function ajax_sync_legacy_diamonds() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }
        
        global $wpdb;
        
        // Get all diamond groups
        $groups = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}jpc_diamond_groups`");
        
        // Get all diamond types
        $types = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}jpc_diamond_types`");
        
        // Get all certifications
        $certs = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}jpc_diamond_certifications`");
        
        $synced = 0;
        $errors = array();
        
        // For each combination, create legacy diamond entry
        foreach ($groups as $group) {
            foreach ($types as $type) {
                if ($type->diamond_group_id != $group->id) continue;
                
                // Use mid-point of carat range
                $carat = ($type->carat_from + $type->carat_to) / 2;
                
                foreach ($certs as $cert) {
                    // Calculate price
                    $calc = self::calculate_price($group->slug, $carat, $cert->slug);
                    
                    if (isset($calc['error'])) {
                        $errors[] = $calc['error'];
                        continue;
                    }
                    
                    // Check if already exists
                    $exists = $wpdb->get_var($wpdb->prepare(
                        "SELECT id FROM `{$wpdb->prefix}jpc_diamonds` 
                        WHERE type = %s AND carat = %f AND certification = %s",
                        $group->slug,
                        $carat,
                        $cert->slug
                    ));
                    
                    if (!$exists) {
                        $data = array(
                            'type' => $group->slug,
                            'carat' => $carat,
                            'certification' => $cert->slug,
                            'price_per_carat' => $calc['final_price_per_carat'],
                            'display_name' => sprintf(
                                '%s (%.2fct, %s)',
                                $group->name,
                                $carat,
                                $cert->name
                            )
                        );
                        
                        if (self::add($data)) {
                            $synced++;
                        }
                    }
                }
            }
        }
        
        wp_send_json_success(array(
            'message' => "Synced $synced diamonds",
            'synced' => $synced,
            'errors' => $errors
        ));
    }
}

// Initialize
JPC_Diamonds::get_instance();
