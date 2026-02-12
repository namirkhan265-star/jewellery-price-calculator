<?php
/**
 * EMERGENCY DEBUG SCRIPT
 * Upload to WordPress root and visit to see actual errors
 */

// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Load WordPress
require_once __DIR__ . '/wp-load.php';

echo "<h1>JPC Debug - Finding the Error</h1>";
echo "<pre>";

// Test 1: Check if plugin is active
echo "\n=== TEST 1: Plugin Status ===\n";
if (is_plugin_active('jewellery-price-calculator/jewellery-price-calculator.php')) {
    echo "✓ Plugin is ACTIVE\n";
} else {
    echo "✗ Plugin is NOT ACTIVE\n";
}

// Test 2: Check if classes exist
echo "\n=== TEST 2: Class Existence ===\n";
$classes = [
    'JPC_Database',
    'JPC_Metal_Groups',
    'JPC_Metals',
    'JPC_Price_Calculator',
    'JPC_Product_Meta_Box',
    'JPC_Admin'
];

foreach ($classes as $class) {
    if (class_exists($class)) {
        echo "✓ $class exists\n";
    } else {
        echo "✗ $class MISSING\n";
    }
}

// Test 3: Try to get metals
echo "\n=== TEST 3: Get Metals (This is where it breaks) ===\n";
try {
    $metals = JPC_Metals::get_all();
    echo "✓ Got " . count($metals) . " metals\n";
    
    if (!empty($metals)) {
        echo "\nFirst metal data:\n";
        print_r($metals[0]);
    }
} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}

// Test 4: Check database tables
echo "\n=== TEST 4: Database Tables ===\n";
global $wpdb;
$tables = [
    'jpc_metal_groups',
    'jpc_metals',
    'jpc_diamond_groups',
    'jpc_diamonds'
];

foreach ($tables as $table) {
    $full_table = $wpdb->prefix . $table;
    $exists = $wpdb->get_var("SHOW TABLES LIKE '$full_table'");
    if ($exists) {
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $full_table");
        echo "✓ $full_table exists ($count rows)\n";
    } else {
        echo "✗ $full_table MISSING\n";
    }
}

// Test 5: Check metal_groups columns
echo "\n=== TEST 5: Metal Groups Table Structure ===\n";
$columns = $wpdb->get_results("SHOW COLUMNS FROM {$wpdb->prefix}jpc_metal_groups");
echo "Columns in jpc_metal_groups:\n";
foreach ($columns as $col) {
    echo "  - {$col->Field} ({$col->Type})\n";
}

// Test 6: Check if enable_making_charge and enable_wastage_charge exist
echo "\n=== TEST 6: Check Enable Columns ===\n";
$has_enable_making = false;
$has_enable_wastage = false;
foreach ($columns as $col) {
    if ($col->Field === 'enable_making_charge') $has_enable_making = true;
    if ($col->Field === 'enable_wastage_charge') $has_enable_wastage = true;
}

if ($has_enable_making) {
    echo "✓ enable_making_charge column EXISTS\n";
} else {
    echo "✗ enable_making_charge column MISSING\n";
}

if ($has_enable_wastage) {
    echo "✓ enable_wastage_charge column EXISTS\n";
} else {
    echo "✗ enable_wastage_charge column MISSING\n";
}

// Test 7: Try the actual query from JPC_Metals::get_all()
echo "\n=== TEST 7: Actual Query Test ===\n";
$query = "
    SELECT m.*, mg.name as group_name, mg.enable_making_charge, mg.enable_wastage_charge
    FROM {$wpdb->prefix}jpc_metals m
    LEFT JOIN {$wpdb->prefix}jpc_metal_groups mg ON m.metal_group_id = mg.id
    ORDER BY m.display_name ASC
";

echo "Query:\n$query\n\n";

try {
    $results = $wpdb->get_results($query);
    if ($wpdb->last_error) {
        echo "✗ SQL ERROR: " . $wpdb->last_error . "\n";
    } else {
        echo "✓ Query successful, got " . count($results) . " results\n";
        if (!empty($results)) {
            echo "\nFirst result:\n";
            print_r($results[0]);
        }
    }
} catch (Exception $e) {
    echo "✗ EXCEPTION: " . $e->getMessage() . "\n";
}

echo "</pre>";

echo "<h2>Next Steps:</h2>";
echo "<ul>";
echo "<li>If enable_making_charge or enable_wastage_charge columns are MISSING, we need to add them to the database</li>";
echo "<li>If there's an SQL error, that's the root cause</li>";
echo "<li>If the query works here but fails on the metals page, it's a different issue</li>";
echo "</ul>";

echo "<p><strong>DELETE THIS FILE after viewing!</strong></p>";
