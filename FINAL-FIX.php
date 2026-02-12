<?php
/**
 * FINAL DATABASE FIX v2.5.37
 * This will check each column individually and only add if missing
 */

// Enable error display
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load WordPress
if (!file_exists(__DIR__ . '/wp-load.php')) {
    die('ERROR: Place this file in your WordPress root directory (same folder as wp-config.php)');
}

require_once __DIR__ . '/wp-load.php';

// Check admin
if (!current_user_can('manage_options')) {
    die('ERROR: You must be logged in as administrator');
}

global $wpdb;
$table = $wpdb->prefix . 'jpc_metal_groups';

// HTML output
?>
<!DOCTYPE html>
<html>
<head>
    <title>JPC Final Fix v2.5.37</title>
    <style>
        body { font-family: monospace; background: #1e1e1e; color: #d4d4d4; padding: 20px; }
        .success { color: #4ec9b0; }
        .error { color: #f48771; }
        .warning { color: #dcdcaa; }
        .info { color: #9cdcfe; }
        h1 { color: #4ec9b0; }
        pre { background: #252526; padding: 15px; border-left: 3px solid #007acc; }
    </style>
</head>
<body>
    <h1>🔧 JPC FINAL DATABASE FIX v2.5.37</h1>
    <pre>
<?php

echo "Table: $table\n\n";

// Get current columns
$columns = $wpdb->get_results("SHOW COLUMNS FROM `$table`");
$existing_columns = array();
foreach ($columns as $col) {
    $existing_columns[] = $col->Field;
}

echo "Existing columns:\n";
foreach ($existing_columns as $col) {
    echo "  ✓ $col\n";
}
echo "\n";

// Define columns to add
$columns_to_add = array(
    'enable_making_charge' => array(
        'type' => 'tinyint(1)',
        'default' => '1',
        'after' => 'unit'
    ),
    'making_charge_type' => array(
        'type' => 'varchar(20)',
        'default' => "'percentage'",
        'after' => 'enable_making_charge'
    ),
    'enable_wastage_charge' => array(
        'type' => 'tinyint(1)',
        'default' => '1',
        'after' => 'making_charge_type'
    ),
    'wastage_charge_type' => array(
        'type' => 'varchar(20)',
        'default' => "'percentage'",
        'after' => 'enable_wastage_charge'
    )
);

echo "Checking required columns...\n\n";

$added = 0;
$skipped = 0;
$errors = 0;

foreach ($columns_to_add as $column_name => $config) {
    if (in_array($column_name, $existing_columns)) {
        echo "<span class='warning'>⊘ SKIP:</span> $column_name (already exists)\n";
        $skipped++;
    } else {
        echo "<span class='info'>+ ADD:</span> $column_name ... ";
        
        $sql = "ALTER TABLE `$table` ADD COLUMN `$column_name` {$config['type']} DEFAULT {$config['default']} AFTER `{$config['after']}`";
        
        $result = $wpdb->query($sql);
        
        if ($result === false) {
            echo "<span class='error'>FAILED</span>\n";
            echo "  Error: " . $wpdb->last_error . "\n";
            $errors++;
        } else {
            echo "<span class='success'>SUCCESS</span>\n";
            $added++;
        }
    }
}

echo "\n";
echo "═══════════════════════════════════════\n";
echo "RESULTS:\n";
echo "  <span class='success'>✓ Added: $added</span>\n";
echo "  <span class='warning'>⊘ Skipped: $skipped</span>\n";
echo "  <span class='error'>✗ Errors: $errors</span>\n";
echo "═══════════════════════════════════════\n\n";

if ($errors === 0) {
    echo "<span class='success'>🎉 DATABASE FIX COMPLETE!</span>\n\n";
    
    // Verify by running the actual query
    echo "Testing the actual query...\n";
    $test_query = "
        SELECT m.*, 
               g.name as group_name, 
               g.unit,
               g.enable_making_charge,
               g.enable_wastage_charge
        FROM {$wpdb->prefix}jpc_metals m 
        LEFT JOIN {$wpdb->prefix}jpc_metal_groups g ON m.metal_group_id = g.id 
        LIMIT 1
    ";
    
    $test_result = $wpdb->get_results($test_query);
    
    if ($wpdb->last_error) {
        echo "<span class='error'>✗ Query FAILED:</span> " . $wpdb->last_error . "\n";
    } else {
        echo "<span class='success'>✓ Query WORKS!</span> Retrieved " . count($test_result) . " row(s)\n";
    }
    
    echo "\n";
    echo "NEXT STEPS:\n";
    echo "1. Clear ALL caches (WordPress, browser, server)\n";
    echo "2. Go to: Jewellery Price → Metals\n";
    echo "3. Should work perfectly now!\n";
    echo "4. DELETE THIS FILE from your server\n";
} else {
    echo "<span class='error'>⚠ ERRORS OCCURRED</span>\n\n";
    echo "Please check the error messages above.\n";
    echo "You may need to fix these manually in phpMyAdmin.\n";
}

?>
    </pre>
</body>
</html>
