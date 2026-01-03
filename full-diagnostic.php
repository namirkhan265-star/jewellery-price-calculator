<?php
/**
 * COMPREHENSIVE DIAGNOSTIC SCRIPT
 * Run this to check EVERYTHING about price calculations
 * 
 * USAGE: Upload to WordPress root, access via browser: yourdomain.com/full-diagnostic.php
 */

// Load WordPress
require_once('wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Access denied. Admin only.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>JPC Full Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #0073aa; padding-bottom: 10px; }
        h2 { color: #0073aa; margin-top: 30px; border-bottom: 2px solid #ddd; padding-bottom: 8px; }
        h3 { color: #555; margin-top: 20px; }
        .success { background: #d4edda; border: 2px solid #28a745; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; border: 2px solid #dc3545; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; border: 2px solid #ffc107; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; border: 2px solid #17a2b8; padding: 15px; border-radius: 5px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table th { background: #0073aa; color: white; padding: 12px; text-align: left; }
        table td { padding: 10px; border-bottom: 1px solid #ddd; }
        table tr:nth-child(even) { background: #f9f9f9; }
        .code { background: #f4f4f4; padding: 10px; border-left: 4px solid #0073aa; font-family: monospace; margin: 10px 0; overflow-x: auto; }
        .button { background: #0073aa; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }
        .button:hover { background: #005a87; }
        .button-danger { background: #dc3545; }
        .button-danger:hover { background: #c82333; }
        .button-success { background: #28a745; }
        .button-success:hover { background: #218838; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔍 JPC Comprehensive Diagnostic Report</h1>
    <p><strong>Generated:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
    
    <?php
    
    // 1. CHECK PLUGIN FILES
    echo '<h2>1. Plugin Files Check</h2>';
    $required_files = array(
        'jewellery-price-calculator.php',
        'includes/class-jpc-price-calculator.php',
        'includes/class-jpc-frontend.php',
        'includes/class-jpc-product-meta.php',
        'templates/frontend/price-breakup.php',
        'templates/admin/general-settings.php'
    );
    
    $missing_files = array();
    foreach ($required_files as $file) {
        $path = WP_PLUGIN_DIR . '/jewellery-price-calculator/' . $file;
        if (!file_exists($path)) {
            $missing_files[] = $file;
        }
    }
    
    if (empty($missing_files)) {
        echo '<div class="success">✓ All required plugin files exist</div>';
    } else {
        echo '<div class="error">✗ Missing files:<br>' . implode('<br>', $missing_files) . '</div>';
    }
    
    // 2. CHECK PLUGIN VERSION
    echo '<h2>2. Plugin Version</h2>';
    if (defined('JPC_VERSION')) {
        echo '<div class="info">Current Version: <strong>' . JPC_VERSION . '</strong></div>';
        if (version_compare(JPC_VERSION, '1.7.2', '>=')) {
            echo '<div class="success">✓ Version is up to date (1.7.2+)</div>';
        } else {
            echo '<div class="error">✗ Version is outdated. Expected 1.7.2+, found ' . JPC_VERSION . '</div>';
        }
    } else {
        echo '<div class="error">✗ JPC_VERSION constant not defined</div>';
    }
    
    // 3. CHECK CLASSES
    echo '<h2>3. Required Classes</h2>';
    $required_classes = array(
        'JPC_Price_Calculator',
        'JPC_Frontend',
        'JPC_Product_Meta',
        'JPC_Metals',
        'JPC_Metal_Groups'
    );
    
    foreach ($required_classes as $class) {
        if (class_exists($class)) {
            echo '<div class="success">✓ ' . $class . ' loaded</div>';
        } else {
            echo '<div class="error">✗ ' . $class . ' NOT loaded</div>';
        }
    }
    
    // 4. GET TEST PRODUCT
    echo '<h2>4. Test Product Analysis</h2>';
    
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_jpc_metal_id',
                'compare' => 'EXISTS'
            )
        )
    );
    
    $products = get_posts($args);
    
    if (empty($products)) {
        echo '<div class="warning">⚠ No JPC products found. Please create a product with JPC data first.</div>';
    } else {
        $product = $products[0];
        $product_id = $product->ID;
        
        echo '<div class="info">';
        echo '<strong>Test Product:</strong> ' . $product->post_title . ' (ID: ' . $product_id . ')<br>';
        echo '<strong>URL:</strong> <a href="' . get_permalink($product_id) . '" target="_blank">' . get_permalink($product_id) . '</a>';
        echo '</div>';
        
        // 5. CHECK PRODUCT META
        echo '<h3>5. Product Meta Data</h3>';
        echo '<table>';
        echo '<tr><th>Meta Key</th><th>Value</th></tr>';
        
        $meta_keys = array(
            '_jpc_metal_id',
            '_jpc_metal_weight',
            '_jpc_making_charge',
            '_jpc_wastage_charge',
            '_jpc_discount_percentage',
            '_regular_price',
            '_sale_price',
            '_price'
        );
        
        foreach ($meta_keys as $key) {
            $value = get_post_meta($product_id, $key, true);
            echo '<tr><td><code>' . $key . '</code></td><td>' . esc_html($value) . '</td></tr>';
        }
        echo '</table>';
        
        // 6. CHECK STORED BREAKUP
        echo '<h3>6. Stored Price Breakup</h3>';
        $breakup = get_post_meta($product_id, '_jpc_price_breakup', true);
        
        if (!$breakup || !is_array($breakup)) {
            echo '<div class="error">✗ No price breakup data found!</div>';
            echo '<div class="warning">⚠ This is the problem! Run "Update All Prices" button.</div>';
        } else {
            echo '<div class="success">✓ Price breakup data exists</div>';
            echo '<table>';
            echo '<tr><th>Component</th><th>Value</th></tr>';
            
            $components = array(
                'metal_price' => 'Metal Price',
                'diamond_price' => 'Diamond Price',
                'making_charge' => 'Making Charge',
                'wastage_charge' => 'Wastage Charge',
                'pearl_cost' => 'Pearl Cost',
                'stone_cost' => 'Stone Cost',
                'extra_fee' => 'Extra Fee',
                'additional_percentage' => 'Additional %',
                'discount' => 'Discount Amount',
                'gst' => 'GST Amount',
                'gst_percentage' => 'GST %',
                'regular_price' => 'Regular Price (Breakup)',
                'sale_price' => 'Sale Price (Breakup)'
            );
            
            foreach ($components as $key => $label) {
                $value = isset($breakup[$key]) ? $breakup[$key] : 'N/A';
                if (is_numeric($value)) {
                    $value = '₹' . number_format($value, 2);
                }
                echo '<tr><td>' . $label . '</td><td><strong>' . $value . '</strong></td></tr>';
            }
            
            // Extra fields
            if (!empty($breakup['extra_fields']) && is_array($breakup['extra_fields'])) {
                echo '<tr><td colspan="2"><strong>Extra Fields:</strong></td></tr>';
                foreach ($breakup['extra_fields'] as $field) {
                    echo '<tr><td>&nbsp;&nbsp;' . esc_html($field['label']) . '</td><td>₹' . number_format($field['value'], 2) . '</td></tr>';
                }
            }
            
            echo '</table>';
        }
        
        // 7. RECALCULATE AND COMPARE
        echo '<h3>7. Live Calculation Test</h3>';
        
        if (class_exists('JPC_Price_Calculator')) {
            $calculated = JPC_Price_Calculator::calculate_product_prices($product_id);
            
            if ($calculated) {
                echo '<div class="success">✓ Calculation successful</div>';
                echo '<table>';
                echo '<tr><th>Component</th><th>Stored Value</th><th>Calculated Value</th><th>Match?</th></tr>';
                
                $compare_keys = array(
                    'metal_price' => 'Metal Price',
                    'discount_amount' => 'Discount',
                    'gst_on_discounted' => 'GST',
                    'regular_price' => 'Regular Price',
                    'sale_price' => 'Sale Price'
                );
                
                foreach ($compare_keys as $key => $label) {
                    $stored = isset($breakup[$key]) ? floatval($breakup[$key]) : 0;
                    $calc = isset($calculated[$key]) ? floatval($calculated[$key]) : 0;
                    
                    // Special handling for discount
                    if ($key === 'discount_amount') {
                        $stored = isset($breakup['discount']) ? floatval($breakup['discount']) : 0;
                    }
                    
                    // Special handling for GST
                    if ($key === 'gst_on_discounted') {
                        $stored = isset($breakup['gst']) ? floatval($breakup['gst']) : 0;
                    }
                    
                    $match = abs($stored - $calc) < 0.01;
                    $match_icon = $match ? '✓' : '✗';
                    $match_class = $match ? 'success' : 'error';
                    
                    echo '<tr>';
                    echo '<td>' . $label . '</td>';
                    echo '<td>₹' . number_format($stored, 2) . '</td>';
                    echo '<td>₹' . number_format($calc, 2) . '</td>';
                    echo '<td style="background: ' . ($match ? '#d4edda' : '#f8d7da') . ';">' . $match_icon . '</td>';
                    echo '</tr>';
                }
                
                echo '</table>';
            } else {
                echo '<div class="error">✗ Calculation failed</div>';
            }
        }
        
        // 8. FRONTEND TEMPLATE CHECK
        echo '<h3>8. Frontend Template Check</h3>';
        $template_path = WP_PLUGIN_DIR . '/jewellery-price-calculator/templates/frontend/price-breakup.php';
        
        if (file_exists($template_path)) {
            $template_content = file_get_contents($template_path);
            
            // Check for critical code
            $checks = array(
                'Uses stored discount' => strpos($template_content, "breakup['discount']") !== false,
                'Uses stored GST' => strpos($template_content, "breakup['gst']") !== false,
                'No price calculation' => strpos($template_content, '$regular_price - $sale_price') === false,
            );
            
            foreach ($checks as $check => $result) {
                if ($result) {
                    echo '<div class="success">✓ ' . $check . '</div>';
                } else {
                    echo '<div class="error">✗ ' . $check . '</div>';
                }
            }
        } else {
            echo '<div class="error">✗ Template file not found</div>';
        }
    }
    
    // 9. ACTIONS
    echo '<h2>9. Quick Actions</h2>';
    
    if (isset($_GET['action'])) {
        if ($_GET['action'] === 'update_all' && check_admin_referer('jpc_diagnostic_update')) {
            echo '<div class="info">Running bulk update...</div>';
            
            $updated = 0;
            $errors = 0;
            
            $all_products = get_posts(array(
                'post_type' => 'product',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => '_jpc_metal_id',
                        'compare' => 'EXISTS'
                    )
                )
            ));
            
            foreach ($all_products as $prod) {
                $result = JPC_Price_Calculator::calculate_and_update_price($prod->ID);
                if ($result === true) {
                    $updated++;
                } else {
                    $errors++;
                }
            }
            
            echo '<div class="success">✓ Updated: ' . $updated . ' products</div>';
            if ($errors > 0) {
                echo '<div class="error">✗ Errors: ' . $errors . ' products</div>';
            }
            
            echo '<meta http-equiv="refresh" content="2">';
        }
    }
    
    $update_url = add_query_arg(array(
        'action' => 'update_all',
        '_wpnonce' => wp_create_nonce('jpc_diagnostic_update')
    ));
    
    echo '<a href="' . $update_url . '" class="button button-success" onclick="return confirm(\'Update all product prices?\');">🔄 Update All Prices Now</a>';
    echo '<a href="' . admin_url('admin.php?page=jpc-general-settings') . '" class="button">⚙️ Go to Settings</a>';
    
    if (!empty($products)) {
        echo '<a href="' . get_permalink($product_id) . '" class="button" target="_blank">👁️ View Test Product</a>';
        echo '<a href="' . admin_url('post.php?post=' . $product_id . '&action=edit') . '" class="button">✏️ Edit Test Product</a>';
    }
    
    ?>
    
    <h2>10. Summary & Recommendations</h2>
    
    <?php
    $issues = array();
    
    if (!empty($missing_files)) {
        $issues[] = 'Missing plugin files - reinstall plugin';
    }
    
    if (!defined('JPC_VERSION') || version_compare(JPC_VERSION, '1.7.2', '<')) {
        $issues[] = 'Plugin version outdated - update to 1.7.2+';
    }
    
    if (!empty($products) && (!$breakup || !is_array($breakup))) {
        $issues[] = 'Price breakup data missing - click "Update All Prices Now" button above';
    }
    
    if (empty($issues)) {
        echo '<div class="success">';
        echo '<h3>✓ All Checks Passed!</h3>';
        echo '<p>Your plugin is configured correctly. If you still see issues:</p>';
        echo '<ol>';
        echo '<li>Clear browser cache (Ctrl+Shift+Delete)</li>';
        echo '<li>Clear WordPress cache (if using caching plugin)</li>';
        echo '<li>Click "Update All Prices Now" button above</li>';
        echo '<li>Visit product page in incognito/private window</li>';
        echo '</ol>';
        echo '</div>';
    } else {
        echo '<div class="error">';
        echo '<h3>⚠ Issues Found:</h3>';
        echo '<ul>';
        foreach ($issues as $issue) {
            echo '<li>' . $issue . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }
    ?>
    
</div>
</body>
</html>
