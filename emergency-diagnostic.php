<?php
/**
 * EMERGENCY: Find the critical error
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo '<h1>🚨 Emergency Diagnostic</h1>';
echo '<hr>';

// Try to load WordPress
echo '<h2>Step 1: Loading WordPress...</h2>';
$wp_load_path = __DIR__ . '/../../../wp-load.php';

if (!file_exists($wp_load_path)) {
    die('<p style="color: red;">❌ Cannot find wp-load.php at: ' . $wp_load_path . '</p>');
}

echo '<p>✅ Found wp-load.php</p>';

// Try to load it
try {
    require_once($wp_load_path);
    echo '<p>✅ WordPress loaded successfully</p>';
} catch (Exception $e) {
    die('<p style="color: red;">❌ Error loading WordPress: ' . $e->getMessage() . '</p>');
}

echo '<hr>';
echo '<h2>Step 2: Checking Plugin Files...</h2>';

$plugin_dir = __DIR__;
$required_files = array(
    'jewellery-price-calculator.php',
    'includes/class-jpc-database.php',
    'includes/class-jpc-metals.php',
    'includes/class-jpc-metal-groups.php',
    'includes/class-jpc-diamonds.php',
    'includes/class-jpc-diamond-groups.php',
    'includes/class-jpc-diamond-types.php',
    'includes/class-jpc-diamond-certifications.php',
    'includes/class-jpc-diamond-pricing.php',
    'includes/class-jpc-price-calculator.php',
    'includes/class-jpc-product-meta.php',
    'includes/class-jpc-admin.php',
    'includes/class-jpc-frontend.php',
);

$missing_files = array();
$syntax_errors = array();

foreach ($required_files as $file) {
    $full_path = $plugin_dir . '/' . $file;
    
    if (!file_exists($full_path)) {
        $missing_files[] = $file;
        echo '<p style="color: red;">❌ Missing: ' . $file . '</p>';
    } else {
        echo '<p style="color: green;">✅ Found: ' . $file . '</p>';
        
        // Check for syntax errors
        $output = array();
        $return_var = 0;
        exec('php -l ' . escapeshellarg($full_path) . ' 2>&1', $output, $return_var);
        
        if ($return_var !== 0) {
            $syntax_errors[$file] = implode("\n", $output);
            echo '<p style="color: red;">⚠️ SYNTAX ERROR in ' . $file . ':</p>';
            echo '<pre style="background: #ffebee; padding: 10px; border-left: 4px solid red;">' . htmlspecialchars(implode("\n", $output)) . '</pre>';
        }
    }
}

echo '<hr>';
echo '<h2>Step 3: Checking Database Tables...</h2>';

global $wpdb;

$tables = array(
    $wpdb->prefix . 'jpc_metals',
    $wpdb->prefix . 'jpc_metal_groups',
    $wpdb->prefix . 'jpc_diamonds',
    $wpdb->prefix . 'jpc_diamond_groups',
    $wpdb->prefix . 'jpc_diamond_types',
    $wpdb->prefix . 'jpc_diamond_certifications',
    $wpdb->prefix . 'jpc_diamond_pricing',
    $wpdb->prefix . 'jpc_price_history',
);

foreach ($tables as $table) {
    $exists = $wpdb->get_var("SHOW TABLES LIKE '$table'");
    if ($exists) {
        echo '<p style="color: green;">✅ Table exists: ' . $table . '</p>';
    } else {
        echo '<p style="color: red;">❌ Missing table: ' . $table . '</p>';
    }
}

echo '<hr>';
echo '<h2>Step 4: Checking WordPress Error Log...</h2>';

$error_log_paths = array(
    __DIR__ . '/../../../wp-content/debug.log',
    __DIR__ . '/../../../debug.log',
    ini_get('error_log'),
);

$found_log = false;
foreach ($error_log_paths as $log_path) {
    if ($log_path && file_exists($log_path)) {
        $found_log = true;
        echo '<p>✅ Found error log: ' . $log_path . '</p>';
        
        // Get last 50 lines
        $lines = file($log_path);
        $last_lines = array_slice($lines, -50);
        
        echo '<h3>Last 50 lines of error log:</h3>';
        echo '<pre style="background: #f5f5f5; padding: 10px; max-height: 400px; overflow: auto; border: 1px solid #ddd;">';
        echo htmlspecialchars(implode('', $last_lines));
        echo '</pre>';
        break;
    }
}

if (!$found_log) {
    echo '<p style="color: orange;">⚠️ No error log found. Enable WP_DEBUG in wp-config.php</p>';
}

echo '<hr>';
echo '<h2>Summary:</h2>';

if (count($missing_files) > 0) {
    echo '<div style="background: #ffebee; padding: 20px; border-left: 4px solid red;">';
    echo '<h3>❌ Missing Files:</h3>';
    echo '<ul>';
    foreach ($missing_files as $file) {
        echo '<li>' . $file . '</li>';
    }
    echo '</ul>';
    echo '</div>';
}

if (count($syntax_errors) > 0) {
    echo '<div style="background: #fff3cd; padding: 20px; border-left: 4px solid orange; margin-top: 20px;">';
    echo '<h3>⚠️ Syntax Errors Found:</h3>';
    foreach ($syntax_errors as $file => $error) {
        echo '<h4>' . $file . '</h4>';
        echo '<pre>' . htmlspecialchars($error) . '</pre>';
    }
    echo '</div>';
}

if (count($missing_files) === 0 && count($syntax_errors) === 0) {
    echo '<div style="background: #e8f5e9; padding: 20px; border-left: 4px solid green;">';
    echo '<h3>✅ All files present and no syntax errors detected</h3>';
    echo '<p>The error might be in the WordPress error log above. Check for:</p>';
    echo '<ul>';
    echo '<li>Fatal errors</li>';
    echo '<li>Class not found errors</li>';
    echo '<li>Function not found errors</li>';
    echo '<li>Memory limit errors</li>';
    echo '</ul>';
    echo '</div>';
}

echo '<hr>';
echo '<p style="color: red; font-weight: bold;">⚠️ DELETE THIS SCRIPT AFTER USE!</p>';
?>
