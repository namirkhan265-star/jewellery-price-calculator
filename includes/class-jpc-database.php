<?php
/**
 * Database Handler
 * Creates and manages all plugin database tables
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
        
        // Diamond Groups Table (NEW)
        $table_diamond_groups = $prefix . 'jpc_diamond_groups';
        $sql_diamond_groups = "CREATE TABLE IF NOT EXISTS `$table_diamond_groups` (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `name` varchar(100) NOT NULL,
            `slug` varchar(100) NOT NULL,
            `description` text,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `slug` (`slug`)
        ) $charset_collate;";
        
        // Diamond Types Table (NEW - replaces old diamonds table)
        $table_diamond_types = $prefix . 'jpc_diamond_types';
        $sql_diamond_types = "CREATE TABLE IF NOT EXISTS `$table_diamond_types` (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `diamond_group_id` bigint(20) NOT NULL,
            `carat_from` decimal(10,3) NOT NULL,
            `carat_to` decimal(10,3) NOT NULL,
            `price_per_carat` decimal(10,2) NOT NULL DEFAULT 0,
            `display_name` varchar(200) NOT NULL,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `diamond_group_id` (`diamond_group_id`),
            KEY `carat_range` (`carat_from`, `carat_to`)
        ) $charset_collate;";
        
        // Diamond Certifications Table (NEW)
        $table_diamond_certs = $prefix . 'jpc_diamond_certifications';
        $sql_diamond_certs = "CREATE TABLE IF NOT EXISTS `$table_diamond_certs` (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `name` varchar(100) NOT NULL,
            `slug` varchar(100) NOT NULL,
            `adjustment_type` varchar(20) NOT NULL DEFAULT 'percentage',
            `adjustment_value` decimal(10,2) NOT NULL DEFAULT 0,
            `description` text,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `slug` (`slug`)
        ) $charset_collate;";
        
        // Old Diamonds Table (keep for backward compatibility)
        $table_diamonds = $prefix . 'jpc_diamonds';
        $sql_diamonds = "CREATE TABLE IF NOT EXISTS `$table_diamonds` (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `type` varchar(50) NOT NULL,
            `carat` decimal(10,2) NOT NULL,
            `certification` varchar(50) NOT NULL,
            `price_per_carat` decimal(10,2) NOT NULL DEFAULT 0,
            `display_name` varchar(200) NOT NULL,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `type` (`type`),
            KEY `carat` (`carat`),
            KEY `certification` (`certification`)
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
        $result3 = dbDelta($sql_diamond_groups);
        $result4 = dbDelta($sql_diamond_types);
        $result5 = dbDelta($sql_diamond_certs);
        $result6 = dbDelta($sql_diamonds);
        $result7 = dbDelta($sql_history);
        $result8 = dbDelta($sql_product_log);
        
        // Log results
        error_log('JPC Table Creation Results:');
        error_log('Metal Groups: ' . print_r($result1, true));
        error_log('Metals: ' . print_r($result2, true));
        error_log('Diamond Groups: ' . print_r($result3, true));
        error_log('Diamond Types: ' . print_r($result4, true));
        error_log('Diamond Certifications: ' . print_r($result5, true));
        error_log('Diamonds (Legacy): ' . print_r($result6, true));
        error_log('History: ' . print_r($result7, true));
        error_log('Product Log: ' . print_r($result8, true));
        
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
     * Insert default data
     */
    private static function insert_default_data() {
        global $wpdb;
        
        $table_groups = $wpdb->prefix . 'jpc_metal_groups';
        $table_metals = $wpdb->prefix . 'jpc_metals';
        $table_diamond_groups = $wpdb->prefix . 'jpc_diamond_groups';
        $table_diamond_types = $wpdb->prefix . 'jpc_diamond_types';
        $table_diamond_certs = $wpdb->prefix . 'jpc_diamond_certifications';
        
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
            $wpdb->insert($table_groups, $group);
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
            $wpdb->insert($table_metals, $metal);
        }
        
        // Insert default diamond groups
        $diamond_groups = JPC_Diamond_Groups::get_default_groups();
        foreach ($diamond_groups as $group) {
            $wpdb->insert($table_diamond_groups, $group);
        }
        
        // Insert default diamond types
        $diamond_types = JPC_Diamond_Types::get_default_types();
        foreach ($diamond_types as $type) {
            // Get group ID by slug
            $group = $wpdb->get_row($wpdb->prepare(
                "SELECT id FROM `$table_diamond_groups` WHERE slug = %s",
                $type['group_slug']
            ));
            
            if ($group) {
                $wpdb->insert($table_diamond_types, array(
                    'diamond_group_id' => $group->id,
                    'carat_from' => $type['carat_from'],
                    'carat_to' => $type['carat_to'],
                    'price_per_carat' => $type['price_per_carat'],
                    'display_name' => $type['display_name'],
                ));
            }
        }
        
        // Insert default certifications
        $certifications = JPC_Diamond_Certifications::get_default_certifications();
        foreach ($certifications as $cert) {
            $wpdb->insert($table_diamond_certs, $cert);
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
            $wpdb->prefix . 'jpc_diamond_groups',
            $wpdb->prefix . 'jpc_diamond_types',
            $wpdb->prefix . 'jpc_diamond_certifications',
            $wpdb->prefix . 'jpc_diamonds',
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
            $wpdb->prefix . 'jpc_diamond_certifications',
            $wpdb->prefix . 'jpc_diamond_types',
            $wpdb->prefix . 'jpc_diamond_groups',
            $wpdb->prefix . 'jpc_diamonds',
            $wpdb->prefix . 'jpc_metals',
            $wpdb->prefix . 'jpc_metal_groups',
        );
        
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS `$table`");
            error_log("JPC: Dropped table: $table");
        }
    }
}
