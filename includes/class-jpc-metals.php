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
        add_action('wp_ajax_jpc_get_price_history', array($this, 'ajax_get_price_history'));
        add_action('wp_ajax_jpc_delete_price_history', array($this, 'ajax_delete_price_history'));
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
            self::log_price_change($id, $old_price, $data['price_per_unit'], 'metal');
            
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
     * Log price change (supports both metals and diamonds)
     */
    public static function log_price_change($item_id, $old_price, $new_price, $item_type = 'metal') {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_price_history';
        
        // Check if table has item_type column, if not add it
        $columns = $wpdb->get_col("DESCRIBE $table");
        if (!in_array('item_type', $columns)) {
            $wpdb->query("ALTER TABLE $table ADD COLUMN `item_type` varchar(20) DEFAULT 'metal' AFTER `metal_id`");
            $wpdb->query("ALTER TABLE $table ADD COLUMN `diamond_id` bigint(20) DEFAULT NULL AFTER `item_type`");
            $wpdb->query("ALTER TABLE $table ADD COLUMN `item_name` varchar(200) DEFAULT NULL AFTER `diamond_id`");
        }
        
        // Get item name
        $item_name = '';
        if ($item_type === 'metal') {
            $metal = self::get_by_id($item_id);
            $item_name = $metal ? $metal->display_name : 'Unknown Metal';
        } elseif ($item_type === 'diamond') {
            $diamond = JPC_Diamonds::get_by_id($item_id);
            $item_name = $diamond ? $diamond->display_name : 'Unknown Diamond';
        }
        
        $insert_data = array(
            'old_price' => $old_price,
            'new_price' => $new_price,
            'changed_by' => get_current_user_id(),
            'item_type' => $item_type,
            'item_name' => $item_name,
        );
        
        if ($item_type === 'metal') {
            $insert_data['metal_id'] = $item_id;
        } elseif ($item_type === 'diamond') {
            $insert_data['diamond_id'] = $item_id;
            $insert_data['metal_id'] = 0; // Set to 0 for diamonds
        }
        
        $wpdb->insert($table, $insert_data);
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
     * Get price history with filters
     */
    public static function get_price_history($args = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_price_history';
        $users_table = $wpdb->users;
        
        $defaults = array(
            'limit' => 50,
            'offset' => 0,
            'item_type' => 'all', // all, metal, diamond
            'date_from' => null,
            'date_to' => null,
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = array('1=1');
        
        // Filter by item type
        if ($args['item_type'] !== 'all') {
            $where[] = $wpdb->prepare("h.item_type = %s", $args['item_type']);
        }
        
        // Filter by date range
        if ($args['date_from']) {
            $where[] = $wpdb->prepare("h.changed_at >= %s", $args['date_from']);
        }
        
        if ($args['date_to']) {
            $where[] = $wpdb->prepare("h.changed_at <= %s", $args['date_to'] . ' 23:59:59');
        }
        
        $where_clause = implode(' AND ', $where);
        
        $query = $wpdb->prepare("
            SELECT h.*, u.display_name as user_name
            FROM $table h
            LEFT JOIN $users_table u ON h.changed_by = u.ID
            WHERE $where_clause
            ORDER BY h.changed_at DESC
            LIMIT %d OFFSET %d
        ", $args['limit'], $args['offset']);
        
        return $wpdb->get_results($query);
    }
    
    /**
     * Get price history count
     */
    public static function get_price_history_count($args = array()) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_price_history';
        
        $defaults = array(
            'item_type' => 'all',
            'date_from' => null,
            'date_to' => null,
        );
        
        $args = wp_parse_args($args, $defaults);
        
        $where = array('1=1');
        
        if ($args['item_type'] !== 'all') {
            $where[] = $wpdb->prepare("item_type = %s", $args['item_type']);
        }
        
        if ($args['date_from']) {
            $where[] = $wpdb->prepare("changed_at >= %s", $args['date_from']);
        }
        
        if ($args['date_to']) {
            $where[] = $wpdb->prepare("changed_at <= %s", $args['date_to'] . ' 23:59:59');
        }
        
        $where_clause = implode(' AND ', $where);
        
        return $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE $where_clause");
    }
    
    /**
     * Delete price history entries
     */
    public static function delete_price_history($ids) {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_price_history';
        
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        
        $ids = array_map('intval', $ids);
        $placeholders = implode(',', array_fill(0, count($ids), '%d'));
        
        return $wpdb->query($wpdb->prepare("DELETE FROM $table WHERE id IN ($placeholders)", $ids));
    }
    
    /**
     * AJAX: Get price history
     */
    public function ajax_get_price_history() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Permission denied', 'jewellery-price-calc')));
        }
        
        $args = array(
            'limit' => isset($_POST['limit']) ? intval($_POST['limit']) : 50,
            'offset' => isset($_POST['offset']) ? intval($_POST['offset']) : 0,
            'item_type' => isset($_POST['item_type']) ? sanitize_text_field($_POST['item_type']) : 'all',
            'date_from' => isset($_POST['date_from']) ? sanitize_text_field($_POST['date_from']) : null,
            'date_to' => isset($_POST['date_to']) ? sanitize_text_field($_POST['date_to']) : null,
        );
        
        $history = self::get_price_history($args);
        $total = self::get_price_history_count($args);
        
        wp_send_json_success(array(
            'history' => $history,
            'total' => $total,
        ));
    }
    
    /**
     * AJAX: Delete price history
     */
    public function ajax_delete_price_history() {
        check_ajax_referer('jpc_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Permission denied', 'jewellery-price-calc')));
        }
        
        $ids = isset($_POST['ids']) ? $_POST['ids'] : array();
        
        if (empty($ids)) {
            wp_send_json_error(array('message' => __('No entries selected', 'jewellery-price-calc')));
        }
        
        $result = self::delete_price_history($ids);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => sprintf(__('%d history entries deleted successfully', 'jewellery-price-calc'), $result)
            ));
        } else {
            wp_send_json_error(array('message' => __('Failed to delete history entries', 'jewellery-price-calc')));
        }
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
