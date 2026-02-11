<?php
/**
 * Database Handler v2.0.0
 * Enhanced with:
 * - Making charges per gram for metals
 * - Manual diamond entry support in products
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
     * v2.0.0: Added making_charges_per_gram field
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
        
        // Metals Table - v2.0.0: Added making_charges_per_gram
        $table_metals = $wpdb->prefix . 'jpc_metals';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_metals` (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `name` varchar(100) NOT NULL,
            `display_name` varchar(100) NOT NULL,
            `metal_group_id` bigint(20) NOT NULL,
            `price_per_unit` decimal(10,2) NOT NULL DEFAULT 0,
            `making_charges_per_gram` decimal(10,2) NOT NULL DEFAULT 0 COMMENT 'Making charges per gram for auto-calculation',
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `name` (`name`),
            KEY `metal_group_id` (`metal_group_id`)
        ) $charset_collate;";
        
        $wpdb->query($sql);
        error_log("JPC: Created/verified table: $table_metals with making_charges_per_gram");
        
        // Check if column exists, if not add it (for existing installations)
        $column_exists = $wpdb->get_results("SHOW COLUMNS FROM `$table_metals` LIKE 'making_charges_per_gram'");
        if (empty($column_exists)) {
            $wpdb->query("ALTER TABLE `$table_metals` ADD COLUMN `making_charges_per_gram` decimal(10,2) NOT NULL DEFAULT 0 COMMENT 'Making charges per gram for auto-calculation' AFTER `price_per_unit`");
            error_log("JPC: Added making_charges_per_gram column to metals table");
        }
    }
    
    /**
     * Create diamond-related tables
     * v1.9.0: Added shape, colour, clarity, cut tables
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
        
        $wpdb->query($sql);
        error_log("JPC: Created/verified table: $table_diamond_groups");
        
        // Diamond Types Table
        $table_diamond_types = $wpdb->prefix . 'jpc_diamond_types';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_diamond_types` (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `name` varchar(100) NOT NULL,
            `slug` varchar(100) NOT NULL,
            `diamond_group_id` bigint(20) NOT NULL,
            `description` text,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `slug` (`slug`),
            KEY `diamond_group_id` (`diamond_group_id`)
        ) $charset_collate;";
        
        $wpdb->query($sql);
        error_log("JPC: Created/verified table: $table_diamond_types");
        
        // Diamond Certifications Table
        $table_diamond_certifications = $wpdb->prefix . 'jpc_diamond_certifications';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_diamond_certifications` (
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
        
        $wpdb->query($sql);
        error_log("JPC: Created/verified table: $table_diamond_certifications");
        
        // Diamond Shapes Table (v1.9.0)
        $table_diamond_shapes = $wpdb->prefix . 'jpc_diamond_shapes';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_diamond_shapes` (
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
        
        $wpdb->query($sql);
        error_log("JPC: Created/verified table: $table_diamond_shapes");
        
        // Diamond Colours Table (v1.9.0)
        $table_diamond_colours = $wpdb->prefix . 'jpc_diamond_colours';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_diamond_colours` (
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
        
        $wpdb->query($sql);
        error_log("JPC: Created/verified table: $table_diamond_colours");
        
        // Diamond Clarities Table (v1.9.0)
        $table_diamond_clarities = $wpdb->prefix . 'jpc_diamond_clarities';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_diamond_clarities` (
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
        
        $wpdb->query($sql);
        error_log("JPC: Created/verified table: $table_diamond_clarities");
        
        // Diamond Cuts Table (v1.9.0)
        $table_diamond_cuts = $wpdb->prefix . 'jpc_diamond_cuts';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_diamond_cuts` (
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
        
        $wpdb->query($sql);
        error_log("JPC: Created/verified table: $table_diamond_cuts");
        
        // Diamonds Table (Legacy - for backward compatibility)
        $table_diamonds = $wpdb->prefix . 'jpc_diamonds';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_diamonds` (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `display_name` varchar(200) NOT NULL,
            `type` varchar(50) NOT NULL,
            `carat` decimal(10,3) NOT NULL,
            `shape_id` bigint(20) DEFAULT NULL,
            `colour_id` bigint(20) DEFAULT NULL,
            `clarity_id` bigint(20) DEFAULT NULL,
            `cut_id` bigint(20) DEFAULT NULL,
            `certification` varchar(50) NOT NULL,
            `price_per_carat` decimal(10,2) NOT NULL DEFAULT 0,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `type` (`type`),
            KEY `carat` (`carat`),
            KEY `shape_id` (`shape_id`),
            KEY `colour_id` (`colour_id`),
            KEY `clarity_id` (`clarity_id`),
            KEY `cut_id` (`cut_id`)
        ) $charset_collate;";
        
        $wpdb->query($sql);
        error_log("JPC: Created/verified table: $table_diamonds");
    }
    
    /**
     * Create history tables
     */
    private static function create_history_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        // Metal Price History
        $table_metal_history = $wpdb->prefix . 'jpc_metal_price_history';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_metal_history` (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `metal_id` bigint(20) NOT NULL,
            `old_price` decimal(10,2) NOT NULL,
            `new_price` decimal(10,2) NOT NULL,
            `changed_by` bigint(20) NOT NULL,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `metal_id` (`metal_id`),
            KEY `created_at` (`created_at`)
        ) $charset_collate;";
        
        $wpdb->query($sql);
        error_log("JPC: Created/verified table: $table_metal_history");
        
        // Diamond Price History
        $table_diamond_history = $wpdb->prefix . 'jpc_diamond_price_history';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_diamond_history` (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `diamond_id` bigint(20) NOT NULL,
            `old_price` decimal(10,2) NOT NULL,
            `new_price` decimal(10,2) NOT NULL,
            `changed_by` bigint(20) NOT NULL,
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `diamond_id` (`diamond_id`),
            KEY `created_at` (`created_at`)
        ) $charset_collate;";
        
        $wpdb->query($sql);
        error_log("JPC: Created/verified table: $table_diamond_history");
    }
    
    /**
     * Check if all required tables exist
     */
    public static function tables_exist() {
        global $wpdb;
        
        $required_tables = array(
            'jpc_metal_groups',
            'jpc_metals',
            'jpc_diamond_groups',
            'jpc_diamond_types',
            'jpc_diamond_certifications',
            'jpc_diamond_shapes',
            'jpc_diamond_colours',
            'jpc_diamond_clarities',
            'jpc_diamond_cuts',
            'jpc_diamonds',
            'jpc_metal_price_history',
            'jpc_diamond_price_history'
        );
        
        foreach ($required_tables as $table) {
            $full_table_name = $wpdb->prefix . $table;
            $result = $wpdb->get_var("SHOW TABLES LIKE '$full_table_name'");
            if ($result != $full_table_name) {
                error_log("JPC: Table missing: $full_table_name");
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Insert default data
     */
    private static function insert_default_data() {
        // Insert default metal groups
        self::insert_default_metal_groups();
        
        // Insert default diamond data
        self::insert_default_diamond_groups();
        self::insert_default_diamond_certifications();
        self::insert_default_diamond_shapes();
        self::insert_default_diamond_colours();
        self::insert_default_diamond_clarities();
        self::insert_default_diamond_cuts();
    }
    
    /**
     * Insert default metal groups
     * FIXED: Now includes enable_making_charge and enable_wastage_charge set to 1
     */
    private static function insert_default_metal_groups() {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_metal_groups';
        
        $existing = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($existing > 0) {
            error_log("JPC: Metal groups already exist, skipping default data");
            return;
        }
        
        $default_groups = array(
            array(
                'name' => 'Gold', 
                'unit' => 'gram',
                'enable_making_charge' => 1,
                'making_charge_type' => 'percentage',
                'enable_wastage_charge' => 1,
                'wastage_charge_type' => 'percentage'
            ),
            array(
                'name' => 'Silver', 
                'unit' => 'gram',
                'enable_making_charge' => 1,
                'making_charge_type' => 'percentage',
                'enable_wastage_charge' => 1,
                'wastage_charge_type' => 'percentage'
            ),
            array(
                'name' => 'Platinum', 
                'unit' => 'gram',
                'enable_making_charge' => 1,
                'making_charge_type' => 'percentage',
                'enable_wastage_charge' => 1,
                'wastage_charge_type' => 'percentage'
            ),
        );
        
        foreach ($default_groups as $group) {
            $wpdb->insert($table, $group);
        }
        
        error_log("JPC: Inserted default metal groups with making/wastage charges enabled");
    }
    
    /**
     * Insert default diamond groups
     */
    private static function insert_default_diamond_groups() {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_groups';
        
        $existing = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($existing > 0) {
            return;
        }
        
        $default_groups = array(
            array('name' => 'Natural Diamond', 'slug' => 'natural-diamond', 'description' => 'Naturally mined diamonds'),
            array('name' => 'Lab Grown Diamond', 'slug' => 'lab-grown-diamond', 'description' => 'Laboratory created diamonds'),
        );
        
        foreach ($default_groups as $group) {
            $wpdb->insert($table, $group);
        }
        
        error_log("JPC: Inserted default diamond groups");
    }
    
    /**
     * Insert default diamond certifications
     */
    private static function insert_default_diamond_certifications() {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_certifications';
        
        $existing = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($existing > 0) {
            return;
        }
        
        $default_certs = array(
            array('name' => 'GIA', 'slug' => 'gia', 'adjustment_type' => 'percentage', 'adjustment_value' => 10, 'description' => 'Gemological Institute of America'),
            array('name' => 'IGI', 'slug' => 'igi', 'adjustment_type' => 'percentage', 'adjustment_value' => 5, 'description' => 'International Gemological Institute'),
            array('name' => 'HRD', 'slug' => 'hrd', 'adjustment_type' => 'percentage', 'adjustment_value' => 8, 'description' => 'HRD Antwerp'),
        );
        
        foreach ($default_certs as $cert) {
            $wpdb->insert($table, $cert);
        }
        
        error_log("JPC: Inserted default diamond certifications");
    }
    
    /**
     * Insert default diamond shapes (v1.9.0)
     */
    private static function insert_default_diamond_shapes() {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_shapes';
        
        $existing = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($existing > 0) {
            return;
        }
        
        $default_shapes = array(
            array('name' => 'Round', 'slug' => 'round', 'adjustment_type' => 'percentage', 'adjustment_value' => 0, 'description' => 'Classic round brilliant cut'),
            array('name' => 'Princess', 'slug' => 'princess', 'adjustment_type' => 'percentage', 'adjustment_value' => -5, 'description' => 'Square princess cut'),
            array('name' => 'Cushion', 'slug' => 'cushion', 'adjustment_type' => 'percentage', 'adjustment_value' => -8, 'description' => 'Cushion cut with rounded corners'),
            array('name' => 'Emerald', 'slug' => 'emerald', 'adjustment_type' => 'percentage', 'adjustment_value' => -10, 'description' => 'Rectangular step cut'),
            array('name' => 'Oval', 'slug' => 'oval', 'adjustment_type' => 'percentage', 'adjustment_value' => -3, 'description' => 'Elongated oval shape'),
            array('name' => 'Pear', 'slug' => 'pear', 'adjustment_type' => 'percentage', 'adjustment_value' => -7, 'description' => 'Teardrop pear shape'),
            array('name' => 'Marquise', 'slug' => 'marquise', 'adjustment_type' => 'percentage', 'adjustment_value' => -12, 'description' => 'Elongated pointed ends'),
            array('name' => 'Heart', 'slug' => 'heart', 'adjustment_type' => 'percentage', 'adjustment_value' => -15, 'description' => 'Heart-shaped diamond'),
        );
        
        foreach ($default_shapes as $shape) {
            $wpdb->insert($table, $shape);
        }
        
        error_log("JPC: Inserted default diamond shapes");
    }
    
    /**
     * Insert default diamond colours (v1.9.0)
     */
    private static function insert_default_diamond_colours() {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_colours';
        
        $existing = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($existing > 0) {
            return;
        }
        
        $default_colours = array(
            array('name' => 'D - Colorless', 'slug' => 'd-colorless', 'adjustment_type' => 'percentage', 'adjustment_value' => 25, 'description' => 'Absolutely colorless, highest grade'),
            array('name' => 'E - Colorless', 'slug' => 'e-colorless', 'adjustment_type' => 'percentage', 'adjustment_value' => 20, 'description' => 'Colorless, minute traces'),
            array('name' => 'F - Colorless', 'slug' => 'f-colorless', 'adjustment_type' => 'percentage', 'adjustment_value' => 15, 'description' => 'Colorless, slight color detected'),
            array('name' => 'G - Near Colorless', 'slug' => 'g-near-colorless', 'adjustment_type' => 'percentage', 'adjustment_value' => 10, 'description' => 'Near colorless, slight warmth'),
            array('name' => 'H - Near Colorless', 'slug' => 'h-near-colorless', 'adjustment_type' => 'percentage', 'adjustment_value' => 5, 'description' => 'Near colorless, warmth visible'),
            array('name' => 'I - Near Colorless', 'slug' => 'i-near-colorless', 'adjustment_type' => 'percentage', 'adjustment_value' => 0, 'description' => 'Near colorless, slight yellow tint'),
            array('name' => 'J - Near Colorless', 'slug' => 'j-near-colorless', 'adjustment_type' => 'percentage', 'adjustment_value' => -5, 'description' => 'Near colorless, noticeable warmth'),
            array('name' => 'K-M - Faint', 'slug' => 'k-m-faint', 'adjustment_type' => 'percentage', 'adjustment_value' => -15, 'description' => 'Faint yellow color visible'),
        );
        
        foreach ($default_colours as $colour) {
            $wpdb->insert($table, $colour);
        }
        
        error_log("JPC: Inserted default diamond colours");
    }
    
    /**
     * Insert default diamond clarities (v1.9.0)
     */
    private static function insert_default_diamond_clarities() {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_clarities';
        
        $existing = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($existing > 0) {
            return;
        }
        
        $default_clarities = array(
            array('name' => 'FL - Flawless', 'slug' => 'fl-flawless', 'adjustment_type' => 'percentage', 'adjustment_value' => 30, 'description' => 'No inclusions or blemishes'),
            array('name' => 'IF - Internally Flawless', 'slug' => 'if-internally-flawless', 'adjustment_type' => 'percentage', 'adjustment_value' => 25, 'description' => 'No inclusions, only surface blemishes'),
            array('name' => 'VVS1 - Very Very Slightly Included', 'slug' => 'vvs1', 'adjustment_type' => 'percentage', 'adjustment_value' => 20, 'description' => 'Minute inclusions, very difficult to see'),
            array('name' => 'VVS2 - Very Very Slightly Included', 'slug' => 'vvs2', 'adjustment_type' => 'percentage', 'adjustment_value' => 15, 'description' => 'Minute inclusions, difficult to see'),
            array('name' => 'VS1 - Very Slightly Included', 'slug' => 'vs1', 'adjustment_type' => 'percentage', 'adjustment_value' => 10, 'description' => 'Minor inclusions, difficult to see'),
            array('name' => 'VS2 - Very Slightly Included', 'slug' => 'vs2', 'adjustment_type' => 'percentage', 'adjustment_value' => 5, 'description' => 'Minor inclusions, somewhat easy to see'),
            array('name' => 'SI1 - Slightly Included', 'slug' => 'si1', 'adjustment_type' => 'percentage', 'adjustment_value' => 0, 'description' => 'Noticeable inclusions, easy to see'),
            array('name' => 'SI2 - Slightly Included', 'slug' => 'si2', 'adjustment_type' => 'percentage', 'adjustment_value' => -10, 'description' => 'Noticeable inclusions, very easy to see'),
            array('name' => 'I1-I3 - Included', 'slug' => 'i1-i3-included', 'adjustment_type' => 'percentage', 'adjustment_value' => -25, 'description' => 'Obvious inclusions, may affect transparency'),
        );
        
        foreach ($default_clarities as $clarity) {
            $wpdb->insert($table, $clarity);
        }
        
        error_log("JPC: Inserted default diamond clarities");
    }
    
    /**
     * Insert default diamond cuts (v1.9.0)
     */
    private static function insert_default_diamond_cuts() {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamond_cuts';
        
        $existing = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($existing > 0) {
            return;
        }
        
        $default_cuts = array(
            array('name' => 'Excellent', 'slug' => 'excellent', 'adjustment_type' => 'percentage', 'adjustment_value' => 15, 'description' => 'Maximum brilliance and fire'),
            array('name' => 'Very Good', 'slug' => 'very-good', 'adjustment_type' => 'percentage', 'adjustment_value' => 10, 'description' => 'Superior brilliance'),
            array('name' => 'Good', 'slug' => 'good', 'adjustment_type' => 'percentage', 'adjustment_value' => 5, 'description' => 'Good brilliance'),
            array('name' => 'Fair', 'slug' => 'fair', 'adjustment_type' => 'percentage', 'adjustment_value' => 0, 'description' => 'Moderate brilliance'),
            array('name' => 'Poor', 'slug' => 'poor', 'adjustment_type' => 'percentage', 'adjustment_value' => -10, 'description' => 'Little brilliance'),
        );
        
        foreach ($default_cuts as $cut) {
            $wpdb->insert($table, $cut);
        }
        
        error_log("JPC: Inserted default diamond cuts");
    }
}
