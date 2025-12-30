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
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Create tables using direct SQL (more reliable than dbDelta for new tables)
        self::create_metal_tables();
        self::create_diamond_tables();
        self::create_history_tables();
        
        // Verify tables were created
        if (self::tables_exist()) {
            // Insert default data
            self::insert_default_data();
            error_log('JPC: All tables created successfully');
            return true;
        } else {
            error_log('JPC: Failed to create some tables');
            if ($wpdb->last_error) {
                error_log('JPC Database Error: ' . $wpdb->last_error);
            }
            return false;
        }
    }
    
    /**
     * Create metal-related tables
     */
    private static function create_metal_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        // Metal Groups Table
        $table_metal_groups = $wpdb->prefix . 'jpc_metal_groups';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_metal_groups` (
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
        
        $wpdb->query($sql);
        error_log("JPC: Created/verified table: $table_metal_groups");
        
        // Metals Table
        $table_metals = $wpdb->prefix . 'jpc_metals';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_metals` (
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
        
        $wpdb->query($sql);
        error_log("JPC: Created/verified table: $table_metals");
    }
    
    /**
     * Create diamond-related tables
     */
    private static function create_diamond_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        // Diamond Groups Table
        $table_diamond_groups = $wpdb->prefix . 'jpc_diamond_groups';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_diamond_groups` (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `name` varchar(100) NOT NULL,
            `slug` varchar(100) NOT NULL,
            `description` text,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `slug` (`slug`)
        ) $charset_collate;";
        
        $result = $wpdb->query($sql);
        error_log("JPC: Created/verified table: $table_diamond_groups (result: $result)");
        if ($wpdb->last_error) {
            error_log("JPC Error creating diamond_groups: " . $wpdb->last_error);
        }
        
        // Diamond Types Table
        $table_diamond_types = $wpdb->prefix . 'jpc_diamond_types';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_diamond_types` (
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
        
        $result = $wpdb->query($sql);
        error_log("JPC: Created/verified table: $table_diamond_types (result: $result)");
        if ($wpdb->last_error) {
            error_log("JPC Error creating diamond_types: " . $wpdb->last_error);
        }
        
        // Diamond Certifications Table
        $table_diamond_certs = $wpdb->prefix . 'jpc_diamond_certifications';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_diamond_certs` (
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
        
        $result = $wpdb->query($sql);
        error_log("JPC: Created/verified table: $table_diamond_certs (result: $result)");
        if ($wpdb->last_error) {
            error_log("JPC Error creating diamond_certifications: " . $wpdb->last_error);
        }
        
        // Old Diamonds Table (backward compatibility)
        $table_diamonds = $wpdb->prefix . 'jpc_diamonds';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_diamonds` (
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
        
        $wpdb->query($sql);
        error_log("JPC: Created/verified table: $table_diamonds");
    }
    
    /**
     * Create history/log tables
     */
    private static function create_history_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        // Price History Table
        $table_price_history = $wpdb->prefix . 'jpc_price_history';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_price_history` (
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
        
        $wpdb->query($sql);
        error_log("JPC: Created/verified table: $table_price_history");
        
        // Product Price Log Table
        $table_product_log = $wpdb->prefix . 'jpc_product_price_log';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_product_log` (
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
        
        $wpdb->query($sql);
        error_log("JPC: Created/verified table: $table_product_log");
    }
    
    /**
     * Insert default data
     */
    private static function insert_default_data() {
        global $wpdb;
        
        $table_groups = $wpdb->prefix . 'jpc_metal_groups';
        $table_metals = $wpdb->prefix . 'jpc_metals';
        
        // Check if metal data already exists
        $count = $wpdb->get_var("SELECT COUNT(*) FROM `$table_groups`");
        if ($count > 0) {
            error_log('JPC: Metal data already exists, checking diamond data...');
            
            // Check if diamond data exists
            $diamond_groups_count = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}jpc_diamond_groups`");
            if ($diamond_groups_count == 0) {
                error_log('JPC: Diamond groups empty, inserting default diamond data...');
                self::insert_diamond_default_data();
            }
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
        
        // Insert diamond data
        self::insert_diamond_default_data();
    }
    
    /**
     * Insert default diamond data (groups, types, certifications)
     */
    private static function insert_diamond_default_data() {
        global $wpdb;
        
        $table_diamond_groups = $wpdb->prefix . 'jpc_diamond_groups';
        $table_diamond_types = $wpdb->prefix . 'jpc_diamond_types';
        $table_diamond_certs = $wpdb->prefix . 'jpc_diamond_certifications';
        
        // Insert default diamond groups
        $diamond_groups = array(
            array(
                'name' => 'Natural Diamond',
                'slug' => 'natural-diamond',
                'description' => 'Naturally mined diamonds formed over billions of years deep within the Earth'
            ),
            array(
                'name' => 'Lab Grown Diamond',
                'slug' => 'lab-grown-diamond',
                'description' => 'Laboratory-created diamonds with identical properties to natural diamonds'
            ),
            array(
                'name' => 'Moissanite',
                'slug' => 'moissanite',
                'description' => 'Silicon carbide gemstone with brilliant fire and diamond-like appearance'
            ),
        );
        
        foreach ($diamond_groups as $group) {
            $result = $wpdb->insert($table_diamond_groups, $group);
            if ($result) {
                error_log('JPC: Inserted diamond group: ' . $group['name']);
            } else {
                error_log('JPC: Failed to insert diamond group: ' . $group['name'] . ' - ' . $wpdb->last_error);
            }
        }
        
        // Insert default diamond types (carat ranges)
        $diamond_types = array(
            // Natural Diamond ranges
            array('diamond_group_id' => 1, 'carat_from' => 0.000, 'carat_to' => 0.500, 'price_per_carat' => 25000.00, 'display_name' => 'Natural Diamond (0.00-0.50ct)'),
            array('diamond_group_id' => 1, 'carat_from' => 0.500, 'carat_to' => 1.000, 'price_per_carat' => 32500.00, 'display_name' => 'Natural Diamond (0.50-1.00ct)'),
            array('diamond_group_id' => 1, 'carat_from' => 1.000, 'carat_to' => 2.000, 'price_per_carat' => 45000.00, 'display_name' => 'Natural Diamond (1.00-2.00ct)'),
            array('diamond_group_id' => 1, 'carat_from' => 2.000, 'carat_to' => 3.000, 'price_per_carat' => 62500.00, 'display_name' => 'Natural Diamond (2.00-3.00ct)'),
            array('diamond_group_id' => 1, 'carat_from' => 3.000, 'carat_to' => 999.990, 'price_per_carat' => 87500.00, 'display_name' => 'Natural Diamond (3.00ct+)'),
            
            // Lab Grown Diamond ranges
            array('diamond_group_id' => 2, 'carat_from' => 0.000, 'carat_to' => 0.500, 'price_per_carat' => 15000.00, 'display_name' => 'Lab Grown Diamond (0.00-0.50ct)'),
            array('diamond_group_id' => 2, 'carat_from' => 0.500, 'carat_to' => 1.000, 'price_per_carat' => 19500.00, 'display_name' => 'Lab Grown Diamond (0.50-1.00ct)'),
            array('diamond_group_id' => 2, 'carat_from' => 1.000, 'carat_to' => 2.000, 'price_per_carat' => 27000.00, 'display_name' => 'Lab Grown Diamond (1.00-2.00ct)'),
            array('diamond_group_id' => 2, 'carat_from' => 2.000, 'carat_to' => 999.990, 'price_per_carat' => 37500.00, 'display_name' => 'Lab Grown Diamond (2.00ct+)'),
            
            // Moissanite ranges
            array('diamond_group_id' => 3, 'carat_from' => 0.000, 'carat_to' => 1.000, 'price_per_carat' => 5000.00, 'display_name' => 'Moissanite (0.00-1.00ct)'),
            array('diamond_group_id' => 3, 'carat_from' => 1.000, 'carat_to' => 999.990, 'price_per_carat' => 6500.00, 'display_name' => 'Moissanite (1.00ct+)'),
        );
        
        foreach ($diamond_types as $type) {
            $result = $wpdb->insert($table_diamond_types, $type);
            if ($result) {
                error_log('JPC: Inserted diamond type: ' . $type['display_name']);
            } else {
                error_log('JPC: Failed to insert diamond type: ' . $type['display_name'] . ' - ' . $wpdb->last_error);
            }
        }
        
        // Insert default certifications
        $certifications = array(
            array(
                'name' => 'GIA',
                'slug' => 'gia',
                'adjustment_type' => 'percentage',
                'adjustment_value' => 20.00,
                'description' => 'Gemological Institute of America - Premium certification with highest industry standards'
            ),
            array(
                'name' => 'IGI',
                'slug' => 'igi',
                'adjustment_type' => 'percentage',
                'adjustment_value' => 15.00,
                'description' => 'International Gemological Institute - Widely recognized certification'
            ),
            array(
                'name' => 'HRD',
                'slug' => 'hrd',
                'adjustment_type' => 'percentage',
                'adjustment_value' => 18.00,
                'description' => 'HRD Antwerp - High quality European certification'
            ),
            array(
                'name' => 'None',
                'slug' => 'none',
                'adjustment_type' => 'percentage',
                'adjustment_value' => 0.00,
                'description' => 'No certification - Base price without premium'
            ),
        );
        
        foreach ($certifications as $cert) {
            $result = $wpdb->insert($table_diamond_certs, $cert);
            if ($result) {
                error_log('JPC: Inserted certification: ' . $cert['name']);
            } else {
                error_log('JPC: Failed to insert certification: ' . $cert['name'] . ' - ' . $wpdb->last_error);
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
    
    /**
     * Force insert diamond default data (for manual trigger)
     */
    public static function force_insert_diamond_data() {
        global $wpdb;
        
        // First, ensure tables exist
        self::create_diamond_tables();
        
        // Clear existing diamond data
        $wpdb->query("TRUNCATE TABLE `{$wpdb->prefix}jpc_diamond_groups`");
        $wpdb->query("TRUNCATE TABLE `{$wpdb->prefix}jpc_diamond_types`");
        $wpdb->query("TRUNCATE TABLE `{$wpdb->prefix}jpc_diamond_certifications`");
        
        // Insert fresh data
        self::insert_diamond_default_data();
        
        // Verify insertion
        $groups_count = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}jpc_diamond_groups`");
        $types_count = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}jpc_diamond_types`");
        $certs_count = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}jpc_diamond_certifications`");
        
        error_log("JPC: Force insert complete - Groups: $groups_count, Types: $types_count, Certs: $certs_count");
        
        return array(
            'groups' => $groups_count,
            'types' => $types_count,
            'certifications' => $certs_count
        );
    }
}
