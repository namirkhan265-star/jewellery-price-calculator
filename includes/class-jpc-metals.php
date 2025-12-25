<?php
/**
 * Metals Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Metals {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_ajax_jpc_add_metal', array($this, 'ajax_add_metal'));
        add_action('wp_ajax_jpc_update_metal', array($this, 'ajax_update_metal'));
        add_action('wp_ajax_jpc_delete_metal', array($this, 'ajax_delete_metal'));
        add_action('wp_ajax_jpc_bulk_update_prices', array($this, 'ajax_bulk_update_prices'));
    }
    
    /**
     * Get all metals
     */
    public static function get_all() {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_metals';
        $groups_table = $wpdb->prefix . 'jpc_metal_groups';
        
        return $wpdb->get_results("
            SELECT m.*, g.name as group_name, g.unit 
            FROM $table m 
            LEFT JOIN $groups_table g ON m.metal_group_id = g.id 
            ORDER BY m.id ASC
        ");
    }
    
    /**
     * Get metal by ID
     */
    public static function get_by_id($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_metals';
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
    }
    
    /**
     * Get metals by group ID
     */
    public static function get_by_group($group_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_metals';
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE metal_group_id = %d ORDER BY id ASC", $group_id));
    }
    
    /**
     * Add new metal
     */
    public static function add($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_metals';
        
        $insert_data = array(
            'name' => sanitize_text_field($data['name']),
            'display_name' => sanitize_text_field($data['display_name']),
            'metal_group_id' => intval($data['metal_group_id']),
            'price_per_unit' => floatval($data['price_per_unit']),
        );
        
        $result = $wpdb->insert($table, $insert_data);
        
        if ($result) {
            return $wpdb->insert_id;
        }
        
        return false;
    }
    
    /**
     * Update metal
     */
    public static function update($id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_metals';
        
        // Get old price for history
        $old_metal = self::get_by_id($id);
        $old_price = $old_metal ? $old_metal->price_per_unit : 0;
        
        $update_data = array(
            'name' => sanitize_text_field($data['name']),
            'display_name' => sanitize_text_field($data['display_name']),
            'metal_group_id' => intval($data['metal_group_id']),
            'price_per_unit' => floatval($data['price_per_unit']),
        );
        
        $result = $wpdb->update($table, $update_data, array('id' => $id));
        
        // Log price change if price was updated
        if ($result && $old_price != $data['price_per_unit']) {
            self::log_price_change($id, $old_price, $data['price_per_unit']);
            
            // Update all products using this metal
            self::update_product_prices($id);
        }
        
        return $result;
    }
    
    /**
     * Delete metal
     */
    public static function delete($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_metals';
        
        // Check if any products are using this metal
        $products = self::get_products_using_metal($id);
        
        if (count($products) > 0) {
            return new WP_Error('has_products', __('Cannot delete metal. It is being used by products.', 'jewellery-price-calc'));
        }
        
        return $wpdb->delete($table, array('id' => $id));
    }
    
    /**
     * Log price change
     */
    private static function log_price_change($metal_id, $old_price, $new_price) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_price_history';
        
        $wpdb->insert($table, array(
            'metal_id' => $metal_id,
            'old_price' => $old_price,
            'new_price' => $new_price,
            'changed_by' => get_current_user_id(),
        ));
    }
    
    /**
     * Update all product prices using this metal
     */
    private static function update_product_prices($metal_id) {
        $products = self::get_products_using_metal($metal_id);
        
        foreach ($products as $product_id) {
            JPC_Price_Calculator::calculate_and_update_price($product_id);
        }
    }
    
    /**
     * Get products using a specific metal
     */
    private static function get_products_using_metal($metal_id) {
        global $wpdb;
        
        $query = $wpdb->prepare("
            SELECT post_id 
            FROM {$wpdb->postmeta} 
            WHERE meta_key = '_jpc_metal_id' 
            AND meta_value = %d
        ", $metal_id);
        
        $results = $wpdb->get_col($query);
        return $results;
    }
    
    /**
     * Get price history
     */
    public static function get_price_history($limit = 50) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_price_history';
        $metals_table = $wpdb->prefix . 'jpc_metals';
        $users_table = $wpdb->users;
        
        return $wpdb->get_results($wpdb->prepare("
            SELECT h.*, m.display_name as metal_name, u.display_name as user_name
            FROM $table h
            LEFT JOIN $metals_table m ON h.metal_id = m.id
            LEFT JOIN $users_table u ON h.changed_by = u.ID
            ORDER BY h.changed_at DESC
            LIMIT %d
        ", $limit));
    }
    
    /**
     * AJAX: Add metal
     */
    public function ajax_add_metal() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Permission denied', 'jewellery-price-calc')));
        }
        
        $data = array(
            'name' => $_POST['name'],
            'display_name' => $_POST['display_name'],
            'metal_group_id' => $_POST['metal_group_id'],
            'price_per_unit' => $_POST['price_per_unit'],
        );
        
        $result = self::add($data);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('Metal added successfully', 'jewellery-price-calc'),
                'id' => $result
            ));
        } else {
            wp_send_json_error(array('message' => __('Failed to add metal', 'jewellery-price-calc')));
        }
    }
    
    /**
     * AJAX: Update metal
     */
    public function ajax_update_metal() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Permission denied', 'jewellery-price-calc')));
        }
        
        $id = intval($_POST['id']);
        $data = array(
            'name' => $_POST['name'],
            'display_name' => $_POST['display_name'],
            'metal_group_id' => $_POST['metal_group_id'],
            'price_per_unit' => $_POST['price_per_unit'],
        );
        
        $result = self::update($id, $data);
        
        if ($result !== false) {
            wp_send_json_success(array('message' => __('Metal updated successfully. Product prices have been recalculated.', 'jewellery-price-calc')));
        } else {
            wp_send_json_error(array('message' => __('Failed to update metal', 'jewellery-price-calc')));
        }
    }
    
    /**
     * AJAX: Delete metal
     */
    public function ajax_delete_metal() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Permission denied', 'jewellery-price-calc')));
        }
        
        $id = intval($_POST['id']);
        $result = self::delete($id);
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        } elseif ($result) {
            wp_send_json_success(array('message' => __('Metal deleted successfully', 'jewellery-price-calc')));
        } else {
            wp_send_json_error(array('message' => __('Failed to delete metal', 'jewellery-price-calc')));
        }
    }
    
    /**
     * AJAX: Bulk update prices
     */
    public function ajax_bulk_update_prices() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Permission denied', 'jewellery-price-calc')));
        }
        
        $updates = $_POST['updates'];
        $updated_count = 0;
        
        foreach ($updates as $update) {
            $id = intval($update['id']);
            $price = floatval($update['price']);
            
            $metal = self::get_by_id($id);
            if ($metal) {
                $data = array(
                    'name' => $metal->name,
                    'display_name' => $metal->display_name,
                    'metal_group_id' => $metal->metal_group_id,
                    'price_per_unit' => $price,
                );
                
                if (self::update($id, $data) !== false) {
                    $updated_count++;
                }
            }
        }
        
        wp_send_json_success(array(
            'message' => sprintf(__('%d metal prices updated successfully. All product prices have been recalculated.', 'jewellery-price-calc'), $updated_count)
        ));
    }
}
