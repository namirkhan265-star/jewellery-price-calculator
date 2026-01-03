<?php
/**
 * EMERGENCY DIAGNOSTIC SCRIPT
 * 
 * Upload this file to your WordPress root directory and access it via browser
 * Example: https://yoursite.com/jpc-emergency-diagnostic.php
 * 
 * This will show you the EXACT error causing the critical error
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Jewellery Price Calculator - Emergency Diagnostic</h1>";
echo "<hr>";

// Check if WordPress is loaded
if (!file_exists('./wp-load.php')) {
    die("<p style='color:red;'>ERROR: This file must be in your WordPress root directory!</p>");
}

echo "<h2>Step 1: Loading WordPress...</h2>";
require_once('./wp-load.php');
echo "<p style='color:green;'>✓ WordPress loaded successfully</p>";

echo "<h2>Step 2: Checking Plugin Files...</h2>";

$plugin_dir = WP_PLUGIN_DIR . '/jewellery-price-calculator/';
$required_files = array(
    'jewellery-price-calculator.php',
    'includes/class-jpc-database.php',
    'includes/class-jpc-metal-groups.php',
    'includes/class-jpc-metals.php',
    'includes/class-jpc-diamonds.php',
    'includes/class-jpc-price-calculator.php',
    'includes/class-jpc-product-meta.php',
    'includes/class-jpc-frontend.php',
    'includes/class-jpc-admin.php',
);

$missing_files = array();
foreach ($required_files as $file) {
    $full_path = $plugin_dir . $file;
    if (file_exists($full_path)) {
        echo "<p style='color:green;'>✓ {$file}</p>";
    } else {
        echo "<p style='color:red;'>✗ {$file} - MISSING!</p>";
        $missing_files[] = $file;
    }
}

if (!empty($missing_files)) {
    die("<p style='color:red;'><strong>ERROR: Missing files! Please upload all plugin files.</strong></p>");
}

echo "<h2>Step 3: Checking for PHP Syntax Errors...</h2>";

foreach ($required_files as $file) {
    $full_path = $plugin_dir . $file;
    echo "<p>Checking {$file}...</p>";
    
    // Try to include the file and catch any errors
    ob_start();
    $result = include_once($full_path);
    $output = ob_get_clean();
    
    if ($result === false) {
        echo "<p style='color:red;'>✗ {$file} - SYNTAX ERROR!</p>";
        echo "<pre>" . htmlspecialchars($output) . "</pre>";
    } else {
        echo "<p style='color:green;'>✓ {$file} - No syntax errors</p>";
    }
}

echo "<h2>Step 4: Checking if Classes Exist...</h2>";

$required_classes = array(
    'JPC_Database',
    'JPC_Metal_Groups',
    'JPC_Metals',
    'JPC_Diamonds',
    'JPC_Price_Calculator',
    'JPC_Product_Meta',
    'JPC_Frontend',
    'JPC_Admin',
);

foreach ($required_classes as $class) {
    if (class_exists($class)) {
        echo "<p style='color:green;'>✓ {$class} exists</p>";
    } else {
        echo "<p style='color:red;'>✗ {$class} - CLASS NOT FOUND!</p>";
    }
}

echo "<h2>Step 5: Checking WooCommerce...</h2>";

if (class_exists('WooCommerce')) {
    echo "<p style='color:green;'>✓ WooCommerce is active</p>";
    echo "<p>WooCommerce Version: " . WC()->version . "</p>";
} else {
    echo "<p style='color:red;'>✗ WooCommerce is NOT active!</p>";
}

echo "<h2>Step 6: Checking Database Tables...</h2>";

global $wpdb;

$tables = array(
    $wpdb->prefix . 'jpc_metal_groups',
    $wpdb->prefix . 'jpc_metals',
    $wpdb->prefix . 'jpc_diamonds',
);

foreach ($tables as $table) {
    $exists = $wpdb->get_var("SHOW TABLES LIKE '{$table}'");
    if ($exists) {
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
        echo "<p style='color:green;'>✓ {$table} exists ({$count} rows)</p>";
    } else {
        echo "<p style='color:red;'>✗ {$table} - TABLE MISSING!</p>";
    }
}

echo "<h2>Step 7: Trying to Initialize Plugin...</h2>";

try {
    // Try to initialize the plugin components
    JPC_Database::init();
    echo "<p style='color:green;'>✓ Database initialized</p>";
    
    JPC_Product_Meta::get_instance();
    echo "<p style='color:green;'>✓ Product Meta initialized</p>";
    
    JPC_Frontend::get_instance();
    echo "<p style='color:green;'>✓ Frontend initialized</p>";
    
    JPC_Admin::get_instance();
    echo "<p style='color:green;'>✓ Admin initialized</p>";
    
    echo "<h2 style='color:green;'>✓✓✓ ALL CHECKS PASSED! ✓✓✓</h2>";
    echo "<p><strong>The plugin should work now. Try activating it again.</strong></p>";
    
} catch (Exception $e) {
    echo "<h2 style='color:red;'>ERROR FOUND!</h2>";
    echo "<p style='color:red;'><strong>Error Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<h2>PHP Information:</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>WordPress Version:</strong> " . get_bloginfo('version') . "</p>";
echo "<p><strong>Memory Limit:</strong> " . ini_get('memory_limit') . "</p>";
echo "<p><strong>Max Execution Time:</strong> " . ini_get('max_execution_time') . " seconds</p>";

echo "<hr>";
echo "<p><strong>IMPORTANT:</strong> Delete this file after diagnosis for security!</p>";
