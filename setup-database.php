<?php
/**
 * Manual Database Setup Script
 * 
 * Access this file directly via browser after uploading to your plugin directory
 * URL: https://yoursite.com/wp-content/plugins/jewellery-price-calculator/setup-database.php
 */

// Define WordPress path - adjust if needed
$wp_load_path = dirname(dirname(dirname(__FILE__))) . '/wp-load.php';

// Try to load WordPress
if (file_exists($wp_load_path)) {
    require_once($wp_load_path);
} else {
    // Alternative path
    $wp_load_path = dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';
    if (file_exists($wp_load_path)) {
        require_once($wp_load_path);
    } else {
        die('Error: Could not find wp-load.php. Please check the file path.');
    }
}

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Access denied. You must be logged in as an administrator.');
}

global $wpdb;

?>
<!DOCTYPE html>
<html>
<head>
    <title>JPC Database Setup</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #0073aa; padding-bottom: 10px; }
        pre { background: #f9f9f9; padding: 15px; border-left: 4px solid #0073aa; overflow-x: auto; }
        .success { color: #46b450; }
        .error { color: #dc3232; }
        .info { color: #0073aa; }
        .button { display: inline-block; padding: 10px 20px; background: #0073aa; color: white; text-decoration: none; border-radius: 4px; margin: 10px 5px; }
        .button:hover { background: #005a87; }
    </style>
</head>
<body>
<div class="container">
<h1>Jewellery Price Calculator - Manual Database Setup</h1>
<pre>
<?php

$charset_collate = $wpdb->get_charset_collate();
$prefix = $wpdb->prefix;

echo "<span class='info'>Database: " . DB_NAME . "</span>\n";
echo "<span class='info'>Table Prefix: " . $prefix . "</span>\n";
echo "<span class='info'>Charset: " . $charset_collate . "</span>\n\n";

// Drop existing tables first
echo "=== STEP 1: Dropping existing tables ===\n";
$tables_to_drop = array(
    $prefix . 'jpc_product_price_log',
    $prefix . 'jpc_price_history',
    $prefix . 'jpc_metals',
    $prefix . 'jpc_metal_groups',
);

foreach ($tables_to_drop as $table) {
    $result = $wpdb->query("DROP TABLE IF EXISTS `$table`");
    if ($result !== false) {
        echo "<span class='success'>✓ Dropped: $table</span>\n";
    } else {
        echo "<span class='error'>✗ Failed to drop: $table</span>\n";
    }
}

echo "\n=== STEP 2: Creating tables ===\n";

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
if ($result1 !== false) {
    echo "<span class='success'>✓ Metal Groups Table Created</span>\n";
} else {
    echo "<span class='error'>✗ Metal Groups Table Failed</span>\n";
    if ($wpdb->last_error) {
        echo "<span class='error'>   Error: " . $wpdb->last_error . "</span>\n";
    }
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
if ($result2 !== false) {
    echo "<span class='success'>✓ Metals Table Created</span>\n";
} else {
    echo "<span class='error'>✗ Metals Table Failed</span>\n";
    if ($wpdb->last_error) {
        echo "<span class='error'>   Error: " . $wpdb->last_error . "</span>\n";
    }
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
if ($result3 !== false) {
    echo "<span class='success'>✓ Price History Table Created</span>\n";
} else {
    echo "<span class='error'>✗ Price History Table Failed</span>\n";
    if ($wpdb->last_error) {
        echo "<span class='error'>   Error: " . $wpdb->last_error . "</span>\n";
    }
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
if ($result4 !== false) {
    echo "<span class='success'>✓ Product Price Log Table Created</span>\n";
} else {
    echo "<span class='error'>✗ Product Price Log Table Failed</span>\n";
    if ($wpdb->last_error) {
        echo "<span class='error'>   Error: " . $wpdb->last_error . "</span>\n";
    }
}

// Insert default data
echo "\n=== STEP 3: Inserting default data ===\n";

// Insert metal groups
$groups = array(
    array('name' => 'Gold', 'unit' => 'gm', 'enable_making_charge' => 1, 'making_charge_type' => 'percentage', 'enable_wastage_charge' => 1, 'wastage_charge_type' => 'percentage'),
    array('name' => 'Silver', 'unit' => 'gm', 'enable_making_charge' => 1, 'making_charge_type' => 'percentage', 'enable_wastage_charge' => 1, 'wastage_charge_type' => 'percentage'),
    array('name' => 'Diamond', 'unit' => 'ct', 'enable_making_charge' => 0, 'making_charge_type' => 'fixed', 'enable_wastage_charge' => 0, 'wastage_charge_type' => 'fixed'),
    array('name' => 'Platinum', 'unit' => 'gm', 'enable_making_charge' => 1, 'making_charge_type' => 'percentage', 'enable_wastage_charge' => 1, 'wastage_charge_type' => 'percentage'),
);

foreach ($groups as $group) {
    $result = $wpdb->insert($prefix . 'jpc_metal_groups', $group);
    if ($result !== false) {
        echo "<span class='success'>✓ Inserted group: {$group['name']}</span>\n";
    } else {
        echo "<span class='error'>✗ Failed to insert group: {$group['name']}</span>\n";
        if ($wpdb->last_error) {
            echo "<span class='error'>   Error: " . $wpdb->last_error . "</span>\n";
        }
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
    if ($result !== false) {
        echo "<span class='success'>✓ Inserted metal: {$metal['display_name']}</span>\n";
    } else {
        echo "<span class='error'>✗ Failed to insert metal: {$metal['display_name']}</span>\n";
        if ($wpdb->last_error) {
            echo "<span class='error'>   Error: " . $wpdb->last_error . "</span>\n";
        }
    }
}

// Verify tables exist
echo "\n=== STEP 4: Verification ===\n";
$tables = array(
    $prefix . 'jpc_metal_groups',
    $prefix . 'jpc_metals',
    $prefix . 'jpc_price_history',
    $prefix . 'jpc_product_price_log',
);

$all_success = true;
foreach ($tables as $table) {
    $exists = $wpdb->get_var("SHOW TABLES LIKE '$table'") == $table;
    if ($exists) {
        $count = $wpdb->get_var("SELECT COUNT(*) FROM `$table`");
        echo "<span class='success'>✓ $table exists ($count records)</span>\n";
    } else {
        echo "<span class='error'>✗ $table is missing</span>\n";
        $all_success = false;
    }
}

echo "\n=== RESULT ===\n";
if ($all_success) {
    echo "<span class='success' style='font-size: 18px; font-weight: bold;'>✓ SETUP COMPLETE!</span>\n";
    echo "\nAll tables created successfully. You can now use the plugin.\n";
} else {
    echo "<span class='error' style='font-size: 18px; font-weight: bold;'>✗ SETUP FAILED</span>\n";
    echo "\nSome tables could not be created. Please check the errors above.\n";
}

?>
</pre>

<div style="margin-top: 20px;">
    <a href="<?php echo admin_url('admin.php?page=jpc-metal-groups'); ?>" class="button">Go to Metal Groups</a>
    <a href="<?php echo admin_url('admin.php?page=jpc-debug'); ?>" class="button">Go to Debug Page</a>
    <a href="<?php echo admin_url('plugins.php'); ?>" class="button">Go to Plugins</a>
</div>

<div style="margin-top: 30px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107;">
    <strong>Security Note:</strong> For security reasons, please delete this file (setup-database.php) after successful setup.
</div>

</div>
</body>
</html>
