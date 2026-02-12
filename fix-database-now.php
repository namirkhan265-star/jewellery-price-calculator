<?php
/**
 * SIMPLE DATABASE FIX v2.5.37
 * Adds missing columns to jpc_metal_groups table
 */

// Show all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>JPC Database Fix v2.5.37</h1>";
echo "<pre>";

// Load WordPress
echo "Loading WordPress...\n";
if (!file_exists(__DIR__ . '/wp-load.php')) {
    die("ERROR: wp-load.php not found. Make sure this file is in your WordPress root directory.");
}

require_once __DIR__ . '/wp-load.php';
echo "✓ WordPress loaded\n\n";

// Check if user is admin
if (!current_user_can('manage_options')) {
    die("ERROR: You must be logged in as an administrator to run this script.");
}

global $wpdb;
$table = $wpdb->prefix . 'jpc_metal_groups';

echo "=== CHECKING TABLE: $table ===\n\n";

// Check if table exists
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table'");
if (!$table_exists) {
    die("ERROR: Table $table does not exist. Please activate the plugin first.");
}
echo "✓ Table exists\n\n";

// Get current columns
$columns = $wpdb->get_results("SHOW COLUMNS FROM `$table`");
$column_names = array_column($columns, 'Field');

echo "Current columns:\n";
foreach ($column_names as $col) {
    echo "  - $col\n";
}
echo "\n";

// Check which columns are missing
$missing = [];
if (!in_array('enable_making_charge', $column_names)) $missing[] = 'enable_making_charge';
if (!in_array('making_charge_type', $column_names)) $missing[] = 'making_charge_type';
if (!in_array('enable_wastage_charge', $column_names)) $missing[] = 'enable_wastage_charge';
if (!in_array('wastage_charge_type', $column_names)) $missing[] = 'wastage_charge_type';

if (empty($missing)) {
    echo "✅ ALL COLUMNS ALREADY EXIST!\n";
    echo "No migration needed. You can delete this file.\n";
    exit;
}

echo "Missing columns:\n";
foreach ($missing as $col) {
    echo "  ❌ $col\n";
}
echo "\n";

// Run migration if requested
if (!isset($_GET['run'])) {
    echo "=== READY TO FIX ===\n\n";
    echo "Click here to add the missing columns:\n";
    echo "<a href='?run=yes' style='display:inline-block;background:#28a745;color:white;padding:10px 20px;text-decoration:none;border-radius:4px;margin:10px 0;'>RUN FIX NOW</a>\n";
    exit;
}

echo "=== RUNNING MIGRATION ===\n\n";

$success_count = 0;
$error_count = 0;

// Add enable_making_charge
if (in_array('enable_making_charge', $missing)) {
    echo "Adding enable_making_charge...\n";
    $result = $wpdb->query("ALTER TABLE `$table` ADD COLUMN `enable_making_charge` tinyint(1) DEFAULT 1 AFTER `unit`");
    if ($result === false) {
        echo "  ❌ FAILED: " . $wpdb->last_error . "\n";
        $error_count++;
    } else {
        echo "  ✅ SUCCESS\n";
        $success_count++;
    }
}

// Add making_charge_type
if (in_array('making_charge_type', $missing)) {
    echo "Adding making_charge_type...\n";
    $result = $wpdb->query("ALTER TABLE `$table` ADD COLUMN `making_charge_type` varchar(20) DEFAULT 'percentage' AFTER `enable_making_charge`");
    if ($result === false) {
        echo "  ❌ FAILED: " . $wpdb->last_error . "\n";
        $error_count++;
    } else {
        echo "  ✅ SUCCESS\n";
        $success_count++;
    }
}

// Add enable_wastage_charge
if (in_array('enable_wastage_charge', $missing)) {
    echo "Adding enable_wastage_charge...\n";
    $result = $wpdb->query("ALTER TABLE `$table` ADD COLUMN `enable_wastage_charge` tinyint(1) DEFAULT 1 AFTER `making_charge_type`");
    if ($result === false) {
        echo "  ❌ FAILED: " . $wpdb->last_error . "\n";
        $error_count++;
    } else {
        echo "  ✅ SUCCESS\n";
        $success_count++;
    }
}

// Add wastage_charge_type
if (in_array('wastage_charge_type', $missing)) {
    echo "Adding wastage_charge_type...\n";
    $result = $wpdb->query("ALTER TABLE `$table` ADD COLUMN `wastage_charge_type` varchar(20) DEFAULT 'percentage' AFTER `enable_wastage_charge`");
    if ($result === false) {
        echo "  ❌ FAILED: " . $wpdb->last_error . "\n";
        $error_count++;
    } else {
        echo "  ✅ SUCCESS\n";
        $success_count++;
    }
}

echo "\n=== RESULTS ===\n\n";
echo "✅ Successful: $success_count\n";
echo "❌ Failed: $error_count\n\n";

if ($error_count === 0) {
    echo "🎉 MIGRATION COMPLETE!\n\n";
    echo "Next steps:\n";
    echo "1. Clear all caches\n";
    echo "2. Go to Jewellery Price → Metals (should work now!)\n";
    echo "3. Test 'Bulk Update All Prices'\n";
    echo "4. DELETE THIS FILE from your server\n\n";
    
    // Verify columns were added
    $new_columns = $wpdb->get_results("SHOW COLUMNS FROM `$table`");
    $new_column_names = array_column($new_columns, 'Field');
    
    echo "Updated table structure:\n";
    foreach ($new_column_names as $col) {
        $is_new = in_array($col, $missing);
        echo "  " . ($is_new ? "✨ NEW: " : "  - ") . "$col\n";
    }
} else {
    echo "⚠️ SOME ERRORS OCCURRED\n\n";
    echo "Please check the error messages above.\n";
    echo "You may need to run the SQL commands manually in phpMyAdmin.\n";
}

echo "</pre>";
