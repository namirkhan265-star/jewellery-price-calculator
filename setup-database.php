<?php
/**
 * Manual Database Setup Script
 * 
 * Access this file directly: /wp-content/plugins/jewellery-price-calculator/setup-database.php
 * This will manually create all required tables
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Access denied. You must be an administrator.');
}

global $wpdb;

echo '<h1>Jewellery Price Calculator - Manual Database Setup</h1>';
echo '<pre>';

$charset_collate = $wpdb->get_charset_collate();
$prefix = $wpdb->prefix;

echo "Database: " . DB_NAME . "\n";
echo "Table Prefix: " . $prefix . "\n";
echo "Charset: " . $charset_collate . "\n\n";

// Drop existing tables first
echo "=== Dropping existing tables ===\n";
$tables_to_drop = array(
    $prefix . 'jpc_product_price_log',
    $prefix . 'jpc_price_history',
    $prefix . 'jpc_metals',
    $prefix . 'jpc_metal_groups',
);

foreach ($tables_to_drop as $table) {
    $result = $wpdb->query("DROP TABLE IF EXISTS `$table`");
    echo "Dropped: $table - " . ($result !== false ? 'Success' : 'Failed') . "\n";
}

echo "\n=== Creating tables ===\n";

// 1. Metal Groups Table
$sql1 = "CREATE TABLE `{$prefix}jpc_metal_groups` (
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

$result1 = $wpdb->query($sql1);
echo "1. Metal Groups Table: " . ($result1 !== false ? 'Created ✓' : 'Failed ✗') . "\n";
if ($wpdb->last_error) {
    echo "   Error: " . $wpdb->last_error . "\n";
}

// 2. Metals Table
$sql2 = "CREATE TABLE `{$prefix}jpc_metals` (
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

$result2 = $wpdb->query($sql2);
echo "2. Metals Table: " . ($result2 !== false ? 'Created ✓' : 'Failed ✗') . "\n";
if ($wpdb->last_error) {
    echo "   Error: " . $wpdb->last_error . "\n";
}

// 3. Price History Table
$sql3 = "CREATE TABLE `{$prefix}jpc_price_history` (
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

$result3 = $wpdb->query($sql3);
echo "3. Price History Table: " . ($result3 !== false ? 'Created ✓' : 'Failed ✗') . "\n";
if ($wpdb->last_error) {
    echo "   Error: " . $wpdb->last_error . "\n";
}

// 4. Product Price Log Table
$sql4 = "CREATE TABLE `{$prefix}jpc_product_price_log` (
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

$result4 = $wpdb->query($sql4);
echo "4. Product Price Log Table: " . ($result4 !== false ? 'Created ✓' : 'Failed ✗') . "\n";
if ($wpdb->last_error) {
    echo "   Error: " . $wpdb->last_error . "\n";
}

// Insert default data
echo "\n=== Inserting default data ===\n";

// Insert metal groups
$groups = array(
    array('name' => 'Gold', 'unit' => 'gm', 'enable_making_charge' => 1, 'making_charge_type' => 'percentage', 'enable_wastage_charge' => 1, 'wastage_charge_type' => 'percentage'),
    array('name' => 'Silver', 'unit' => 'gm', 'enable_making_charge' => 1, 'making_charge_type' => 'percentage', 'enable_wastage_charge' => 1, 'wastage_charge_type' => 'percentage'),
    array('name' => 'Diamond', 'unit' => 'ct', 'enable_making_charge' => 0, 'making_charge_type' => 'fixed', 'enable_wastage_charge' => 0, 'wastage_charge_type' => 'fixed'),
    array('name' => 'Platinum', 'unit' => 'gm', 'enable_making_charge' => 1, 'making_charge_type' => 'percentage', 'enable_wastage_charge' => 1, 'wastage_charge_type' => 'percentage'),
);

foreach ($groups as $group) {
    $result = $wpdb->insert($prefix . 'jpc_metal_groups', $group);
    echo "Inserted group '{$group['name']}': " . ($result !== false ? 'Success ✓' : 'Failed ✗') . "\n";
    if ($wpdb->last_error) {
        echo "   Error: " . $wpdb->last_error . "\n";
    }
}

// Insert metals
$metals = array(
    array('name' => '14kt_gold', 'display_name' => '14 Karat Gold', 'metal_group_id' => 1, 'price_per_unit' => 3234.10),
    array('name' => '18kt_gold', 'display_name' => '18 Karat Gold', 'metal_group_id' => 1, 'price_per_unit' => 4158.15),
    array('name' => '22kt_gold', 'display_name' => '22 Karat Gold', 'metal_group_id' => 1, 'price_per_unit' => 5082.20),
    array('name' => 'silver', 'display_name' => 'Silver', 'metal_group_id' => 2, 'price_per_unit' => 66.80),
    array('name' => 'platinum', 'display_name' => 'Platinum', 'metal_group_id' => 4, 'price_per_unit' => 2800.00),
);

foreach ($metals as $metal) {
    $result = $wpdb->insert($prefix . 'jpc_metals', $metal);
    echo "Inserted metal '{$metal['display_name']}': " . ($result !== false ? 'Success ✓' : 'Failed ✗') . "\n";
    if ($wpdb->last_error) {
        echo "   Error: " . $wpdb->last_error . "\n";
    }
}

// Verify tables exist
echo "\n=== Verification ===\n";
$tables = array(
    $prefix . 'jpc_metal_groups',
    $prefix . 'jpc_metals',
    $prefix . 'jpc_price_history',
    $prefix . 'jpc_product_price_log',
);

foreach ($tables as $table) {
    $exists = $wpdb->get_var("SHOW TABLES LIKE '$table'") == $table;
    echo "$table: " . ($exists ? 'Exists ✓' : 'Missing ✗') . "\n";
    
    if ($exists) {
        $count = $wpdb->get_var("SELECT COUNT(*) FROM `$table`");
        echo "   Records: $count\n";
    }
}

echo "\n=== DONE ===\n";
echo "Setup complete! You can now go to: Jewellery Price → Metal Groups\n";
echo "\n<a href='" . admin_url('admin.php?page=jpc-metal-groups') . "'>Go to Metal Groups</a>\n";
echo "<a href='" . admin_url('admin.php?page=jpc-debug') . "'>Go to Debug Page</a>\n";

echo '</pre>';
