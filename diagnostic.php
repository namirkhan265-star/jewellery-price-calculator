<?php
/**
 * Plugin Diagnostic Tool
 * 
 * Upload this file to your plugin folder and access it via browser:
 * https://yoursite.com/wp-content/plugins/jewellery-price-calculator/diagnostic.php
 */

// Prevent direct access
if (!isset($_GET['run'])) {
    die('Add ?run=1 to the URL to run diagnostics');
}

echo '<h1>Jewellery Price Calculator - Diagnostic Report</h1>';
echo '<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} .warning{color:orange;} pre{background:#f5f5f5;padding:10px;border:1px solid #ddd;}</style>';

// 1. Check PHP Version
echo '<h2>1. PHP Version Check</h2>';
$php_version = PHP_VERSION;
if (version_compare($php_version, '7.4', '>=')) {
    echo '<p class="success">✅ PHP Version: ' . $php_version . ' (OK)</p>';
} else {
    echo '<p class="error">❌ PHP Version: ' . $php_version . ' (Requires 7.4 or higher)</p>';
}

// 2. Check WordPress
echo '<h2>2. WordPress Check</h2>';
$wp_config = dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-config.php';
if (file_exists($wp_config)) {
    echo '<p class="success">✅ WordPress detected</p>';
    
    // Load WordPress
    require_once($wp_config);
    require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php');
    
    echo '<p class="success">✅ WordPress Version: ' . get_bloginfo('version') . '</p>';
    
    // Check WooCommerce
    if (class_exists('WooCommerce')) {
        echo '<p class="success">✅ WooCommerce is active (Version: ' . WC()->version . ')</p>';
    } else {
        echo '<p class="error">❌ WooCommerce is NOT active</p>';
    }
} else {
    echo '<p class="error">❌ WordPress not found</p>';
}

// 3. Check Plugin Files
echo '<h2>3. Plugin Files Check</h2>';
$plugin_dir = dirname(__FILE__);
echo '<p>Plugin Directory: <code>' . $plugin_dir . '</code></p>';

$required_files = array(
    'jewellery-price-calculator.php' => 'Main plugin file',
    'includes/class-jpc-database.php' => 'Database class',
    'includes/class-jpc-metal.php' => 'Metal class',
    'includes/class-jpc-diamond.php' => 'Diamond class',
    'includes/class-jpc-diamond-group.php' => 'Diamond Group class',
    'includes/class-jpc-calculator.php' => 'Calculator class',
    'includes/class-jpc-product-meta.php' => 'Product Meta class',
    'includes/class-jpc-settings.php' => 'Settings class',
    'includes/class-jpc-frontend.php' => 'Frontend class',
    'includes/class-jpc-price-history.php' => 'Price History class',
    'admin/class-jpc-admin.php' => 'Admin class',
    'admin/class-jpc-metal-admin.php' => 'Metal Admin class',
    'admin/class-jpc-diamond-admin.php' => 'Diamond Admin class',
    'admin/class-jpc-diamond-group-admin.php' => 'Diamond Group Admin class',
    'assets/css/admin.css' => 'Admin CSS',
    'assets/css/frontend.css' => 'Frontend CSS',
    'assets/js/admin.js' => 'Admin JS',
    'assets/js/live-calculator.js' => 'Live Calculator JS'
);

$missing_files = array();
foreach ($required_files as $file => $description) {
    $file_path = $plugin_dir . '/' . $file;
    if (file_exists($file_path)) {
        $size = filesize($file_path);
        echo '<p class="success">✅ ' . $description . ': <code>' . $file . '</code> (' . number_format($size) . ' bytes)</p>';
    } else {
        echo '<p class="error">❌ MISSING: ' . $description . ': <code>' . $file . '</code></p>';
        $missing_files[] = $file;
    }
}

// 4. Check File Permissions
echo '<h2>4. File Permissions Check</h2>';
$main_file = $plugin_dir . '/jewellery-price-calculator.php';
if (file_exists($main_file)) {
    $perms = substr(sprintf('%o', fileperms($main_file)), -4);
    if (is_readable($main_file)) {
        echo '<p class="success">✅ Main file is readable (Permissions: ' . $perms . ')</p>';
    } else {
        echo '<p class="error">❌ Main file is NOT readable (Permissions: ' . $perms . ')</p>';
    }
}

// 5. Try to load main file
echo '<h2>5. Plugin Load Test</h2>';
if (empty($missing_files)) {
    echo '<p>Attempting to load plugin classes...</p>';
    
    try {
        // Define constants if not defined
        if (!defined('JPC_VERSION')) {
            define('JPC_VERSION', '1.5.0');
        }
        if (!defined('JPC_PLUGIN_DIR')) {
            define('JPC_PLUGIN_DIR', $plugin_dir . '/');
        }
        if (!defined('JPC_PLUGIN_URL')) {
            define('JPC_PLUGIN_URL', plugins_url('/', __FILE__));
        }
        if (!defined('JPC_PLUGIN_BASENAME')) {
            define('JPC_PLUGIN_BASENAME', plugin_basename(__FILE__));
        }
        
        // Try to load each class
        $classes_to_load = array(
            'includes/class-jpc-database.php' => 'JPC_Database',
            'includes/class-jpc-metal.php' => 'JPC_Metal',
            'includes/class-jpc-diamond.php' => 'JPC_Diamond',
            'includes/class-jpc-calculator.php' => 'JPC_Calculator'
        );
        
        foreach ($classes_to_load as $file => $class_name) {
            require_once($plugin_dir . '/' . $file);
            if (class_exists($class_name)) {
                echo '<p class="success">✅ Class loaded: ' . $class_name . '</p>';
            } else {
                echo '<p class="error">❌ Class NOT found after loading: ' . $class_name . '</p>';
            }
        }
        
        echo '<p class="success">✅ All classes loaded successfully!</p>';
        
    } catch (Exception $e) {
        echo '<p class="error">❌ Error loading plugin: ' . $e->getMessage() . '</p>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    }
} else {
    echo '<p class="error">❌ Cannot test plugin load - missing files</p>';
}

// 6. Check Database Tables
if (isset($wpdb)) {
    echo '<h2>6. Database Tables Check</h2>';
    $tables = array(
        $wpdb->prefix . 'jpc_metals' => 'Metals table',
        $wpdb->prefix . 'jpc_diamonds' => 'Diamonds table',
        $wpdb->prefix . 'jpc_diamond_groups' => 'Diamond Groups table',
        $wpdb->prefix . 'jpc_price_history' => 'Price History table'
    );
    
    foreach ($tables as $table => $description) {
        $result = $wpdb->get_var("SHOW TABLES LIKE '$table'");
        if ($result === $table) {
            $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
            echo '<p class="success">✅ ' . $description . ': <code>' . $table . '</code> (' . $count . ' rows)</p>';
        } else {
            echo '<p class="warning">⚠️ ' . $description . ': <code>' . $table . '</code> (Not created yet - will be created on activation)</p>';
        }
    }
}

// 7. Summary
echo '<h2>7. Summary</h2>';
if (empty($missing_files) && version_compare($php_version, '7.4', '>=')) {
    echo '<p class="success"><strong>✅ Plugin should activate successfully!</strong></p>';
    echo '<p>If activation still fails, check WordPress error log at: <code>wp-content/debug.log</code></p>';
} else {
    echo '<p class="error"><strong>❌ Plugin cannot activate due to issues above</strong></p>';
    if (!empty($missing_files)) {
        echo '<p>Missing files:</p><ul>';
        foreach ($missing_files as $file) {
            echo '<li>' . $file . '</li>';
        }
        echo '</ul>';
    }
}

// 8. Enable Debug Mode Instructions
echo '<h2>8. Enable Debug Mode</h2>';
echo '<p>To see detailed error messages, add these lines to <code>wp-config.php</code>:</p>';
echo '<pre>define(\'WP_DEBUG\', true);
define(\'WP_DEBUG_LOG\', true);
define(\'WP_DEBUG_DISPLAY\', false);</pre>';
echo '<p>Then check <code>wp-content/debug.log</code> for errors.</p>';

echo '<hr>';
echo '<p><small>Diagnostic completed at: ' . date('Y-m-d H:i:s') . '</small></p>';
