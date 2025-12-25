<?php
/**
 * Database Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Database {
    
    /**
     * Create database tables
     */
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Ensure we're using the correct table prefix
        $prefix = $wpdb->prefix;
        
        // Metal Groups Table
        $table_metal_groups = $prefix . 'jpc_metal_groups';
        $sql_groups = "CREATE TABLE IF NOT EXISTS `$table_metal_groups` (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `name` varchar(100) NOT NULL,
            `unit` varchar(20) NOT NULL,
            `enable_making_charge` tinyint(1) DEFAULT 0,
            `making_charge_type` varchar(20) DEFAULT 'percentage',
            `enable_wastage_charge` tinyint(1) DEFAULT 0,
            `wastage_charge_type` varchar(20) DEFAULT 'percentage',
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `name` (`name`)
        ) $charset_collate;";
        
        // Metals Table
        $table_metals = $prefix . 'jpc_metals';
        $sql_metals = "CREATE TABLE IF NOT EXISTS `$table_metals` (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `name` varchar(100) NOT NULL,
            `display_name` varchar(100) NOT NULL,
            `metal_group_id` bigint(20) NOT NULL,
            `price_per_unit` decimal(10,2) NOT NULL DEFAULT 0,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `name` (`name`),
            KEY `metal_group_id` (`metal_group_id`)
        ) $charset_collate;";
        
        // Price History Table
        $table_price_history = $prefix . 'jpc_price_history';
        $sql_history = "CREATE TABLE IF NOT EXISTS `$table_price_history` (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `metal_id` bigint(20) NOT NULL,
            `old_price` decimal(10,2) NOT NULL,
            `new_price` decimal(10,2) NOT NULL,
            `changed_by` bigint(20) NOT NULL,
            `changed_at` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `metal_id` (`metal_id`),
            KEY `changed_at` (`changed_at`)
        ) $charset_collate;";
        
        // Product Price Log Table
        $table_product_log = $prefix . 'jpc_product_price_log';
        $sql_product_log = "CREATE TABLE IF NOT EXISTS `$table_product_log` (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `product_id` bigint(20) NOT NULL,
            `old_price` decimal(10,2) NOT NULL,
            `new_price` decimal(10,2) NOT NULL,
            `metal_id` bigint(20) NOT NULL,
            `changed_at` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `product_id` (`product_id`),
            KEY `changed_at` (`changed_at`)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Execute queries
        $result1 = dbDelta($sql_groups);
        $result2 = dbDelta($sql_metals);
        $result3 = dbDelta($sql_history);
        $result4 = dbDelta($sql_product_log);
        
        // Log results
        error_log('JPC Table Creation Results:');
        error_log('Groups: ' . print_r($result1, true));
        error_log('Metals: ' . print_r($result2, true));
        error_log('History: ' . print_r($result3, true));
        error_log('Product Log: ' . print_r($result4, true));
        
        // Check if tables were created
        if (self::tables_exist()) {
            // Insert default data
            self::insert_default_data();
            error_log('JPC: Tables created successfully');
            return true;
        } else {
            error_log('JPC: Failed to create tables');
            if ($wpdb->last_error) {
                error_log('JPC Database Error: ' . $wpdb->last_error);
            }
            return false;
        }
    }
    
    /**
     * Insert default metal groups and metals
     */
    private static function insert_default_data() {
        global $wpdb;
        
        $table_groups = $wpdb->prefix . 'jpc_metal_groups';
        $table_metals = $wpdb->prefix . 'jpc_metals';
        
        // Check if data already exists
        $count = $wpdb->get_var("SELECT COUNT(*) FROM `$table_groups`");
        if ($count > 0) {
            error_log('JPC: Default data already exists');
            return;
        }
        
        // Insert default metal groups
        $groups = array(
            array('name' => 'Gold', 'unit' => 'gm', 'enable_making_charge' => 1, 'making_charge_type' => 'percentage', 'enable_wastage_charge' => 1, 'wastage_charge_type' => 'percentage'),
            array('name' => 'Silver', 'unit' => 'gm', 'enable_making_charge' => 1, 'making_charge_type' => 'percentage', 'enable_wastage_charge' => 1, 'wastage_charge_type' => 'percentage'),
            array('name' => 'Diamond', 'unit' => 'ct', 'enable_making_charge' => 0, 'making_charge_type' => 'fixed', 'enable_wastage_charge' => 0, 'wastage_charge_type' => 'fixed'),
            array('name' => 'Platinum', 'unit' => 'gm', 'enable_making_charge' => 1, 'making_charge_type' => 'percentage', 'enable_wastage_charge' => 1, 'wastage_charge_type' => 'percentage'),
        );
        
        foreach ($groups as $group) {
            $result = $wpdb->insert($table_groups, $group);
            if ($wpdb->last_error) {
                error_log('JPC Insert Group Error: ' . $wpdb->last_error);
            } else {
                error_log('JPC: Inserted group: ' . $group['name']);
            }
        }
        
        // Insert default metals
        $metals = array(
            array('name' => '14kt_gold', 'display_name' => '14 Karat Gold', 'metal_group_id' => 1, 'price_per_unit' => 3234.10),
            array('name' => '18kt_gold', 'display_name' => '18 Karat Gold', 'metal_group_id' => 1, 'price_per_unit' => 4158.15),
            array('name' => '22kt_gold', 'display_name' => '22 Karat Gold', 'metal_group_id' => 1, 'price_per_unit' => 5082.20),
            array('name' => 'silver', 'display_name' => 'Silver', 'metal_group_id' => 2, 'price_per_unit' => 66.80),
            array('name' => 'platinum', 'display_name' => 'Platinum', 'metal_group_id' => 4, 'price_per_unit' => 2800.00),
        );
        
        foreach ($metals as $metal) {
            $result = $wpdb->insert($table_metals, $metal);
            if ($wpdb->last_error) {
                error_log('JPC Insert Metal Error: ' . $wpdb->last_error);
            } else {
                error_log('JPC: Inserted metal: ' . $metal['display_name']);
            }
        }
    }
    
    /**
     * Check if tables exist
     */
    public static function tables_exist() {
        global $wpdb;
        
        $tables = array(
            $wpdb->prefix . 'jpc_metal_groups',
            $wpdb->prefix . 'jpc_metals',
            $wpdb->prefix . 'jpc_price_history',
            $wpdb->prefix . 'jpc_product_price_log',
        );
        
        foreach ($tables as $table) {
            $result = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table));
            if ($result != $table) {
                error_log("JPC: Table missing: $table");
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Drop all plugin tables
     */
    public static function drop_tables() {
        global $wpdb;
        
        $tables = array(
            $wpdb->prefix . 'jpc_product_price_log',
            $wpdb->prefix . 'jpc_price_history',
            $wpdb->prefix . 'jpc_metals',
            $wpdb->prefix . 'jpc_metal_groups',
        );
        
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS `$table`");
            error_log("JPC: Dropped table: $table");
        }
    }
}
