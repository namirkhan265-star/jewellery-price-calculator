<?php
/**
 * JPC Debug Script
 * 
 * This script helps identify the exact PHP error causing the critical error
 * 
 * HOW TO USE:
 * 1. Upload this file to your WordPress root directory
 * 2. Visit: https://yoursite.com/jpc-debug.php
 * 3. Check the output for errors
 * 4. Delete this file after debugging
 */

// Load WordPress
require_once('wp-load.php');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html>
<head>
    <title>JPC Debug</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
        .success { color: #4ec9b0; }
        .error { color: #f48771; }
        .warning { color: #dcdcaa; }
        .section { background: #252526; padding: 15px; margin: 10px 0; border-left: 4px solid #007acc; }
        h2 { color: #4ec9b0; margin-top: 0; }
        pre { background: #1e1e1e; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 JPC Debug Report</h1>
    
    <div class="section">
        <h2>1. WordPress Status</h2>
        <?php
        echo '<p class="success">✓ WordPress loaded successfully</p>';
        echo '<p>WordPress Version: ' . get_bloginfo('version') . '</p>';
        echo '<p>PHP Version: ' . PHP_VERSION . '</p>';
        ?>
    </div>
    
    <div class="section">
        <h2>2. Plugin Status</h2>
        <?php
        if (defined('JPC_VERSION')) {
            echo '<p class="success">✓ JPC Plugin loaded (Version: ' . JPC_VERSION . ')</p>';
        } else {
            echo '<p class="error">✗ JPC Plugin NOT loaded</p>';
        }
        ?>
    </div>
    
    <div class="section">
        <h2>3. Required Classes</h2>
        <?php
        $required_classes = array(
            'JPC_Database',
            'JPC_Metal_Groups',
            'JPC_Metals',
            'JPC_Diamond_Groups',
            'JPC_Diamonds',
            'JPC_Price_Calculator',
            'JPC_Product_Meta_Box',
            'JPC_Admin',
        );
        
        foreach ($required_classes as $class) {
            if (class_exists($class)) {
                echo '<p class="success">✓ ' . $class . '</p>';
            } else {
                echo '<p class="error">✗ ' . $class . ' NOT FOUND</p>';
            }
        }
        ?>
    </div>
    
    <div class="section">
        <h2>4. Test Price Calculation</h2>
        <?php
        // Get a test product
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 1,
            'meta_query' => array(
                array(
                    'key' => '_jpc_metal_id',
                    'compare' => 'EXISTS'
                )
            )
        );
        
        $products = get_posts($args);
        
        if (empty($products)) {
            echo '<p class="warning">⚠ No products with JPC data found</p>';
        } else {
            $product = $products[0];
            echo '<p>Testing product: ' . $product->post_title . ' (ID: ' . $product->ID . ')</p>';
            
            try {
                $result = JPC_Price_Calculator::calculate_and_update_price($product->ID);
                
                if ($result !== false) {
                    echo '<p class="success">✓ Price calculation successful!</p>';
                    echo '<pre>';
                    print_r($result);
                    echo '</pre>';
                } else {
                    echo '<p class="error">✗ Price calculation returned false</p>';
                }
            } catch (Exception $e) {
                echo '<p class="error">✗ EXCEPTION: ' . $e->getMessage() . '</p>';
                echo '<pre>' . $e->getTraceAsString() . '</pre>';
            } catch (Error $e) {
                echo '<p class="error">✗ FATAL ERROR: ' . $e->getMessage() . '</p>';
                echo '<pre>' . $e->getTraceAsString() . '</pre>';
            }
        }
        ?>
    </div>
    
    <div class="section">
        <h2>5. Database Tables</h2>
        <?php
        global $wpdb;
        $tables = array(
            'jpc_metals',
            'jpc_metal_groups',
            'jpc_diamonds',
            'jpc_diamond_groups',
            'jpc_price_history',
        );
        
        foreach ($tables as $table) {
            $full_table = $wpdb->prefix . $table;
            $exists = $wpdb->get_var("SHOW TABLES LIKE '$full_table'");
            
            if ($exists) {
                $count = $wpdb->get_var("SELECT COUNT(*) FROM $full_table");
                echo '<p class="success">✓ ' . $full_table . ' (' . $count . ' rows)</p>';
            } else {
                echo '<p class="error">✗ ' . $full_table . ' NOT FOUND</p>';
            }
        }
        ?>
    </div>
    
    <div class="section">
        <h2>6. Recent PHP Errors</h2>
        <?php
        $error_log = ini_get('error_log');
        if ($error_log && file_exists($error_log)) {
            echo '<p>Error log location: ' . $error_log . '</p>';
            $errors = file_get_contents($error_log);
            $recent_errors = array_slice(explode("\n", $errors), -20);
            echo '<pre>' . implode("\n", $recent_errors) . '</pre>';
        } else {
            echo '<p class="warning">⚠ Error log not found or not configured</p>';
            echo '<p>To enable error logging, add this to wp-config.php:</p>';
            echo '<pre>';
            echo "define('WP_DEBUG', true);\n";
            echo "define('WP_DEBUG_LOG', true);\n";
            echo "define('WP_DEBUG_DISPLAY', false);\n";
            echo '</pre>';
        }
        ?>
    </div>
    
    <div class="section">
        <h2>7. Next Steps</h2>
        <ol>
            <li>If you see any errors above, that's the root cause</li>
            <li>If no errors, enable WordPress debug mode in wp-config.php</li>
            <li>Check /wp-content/debug.log for detailed errors</li>
            <li><strong>DELETE THIS FILE after debugging</strong></li>
        </ol>
    </div>
    
</body>
</html>
