<?php
/**
 * FORCE TEMPLATE FIX
 * This script will check for theme overrides and force the correct template
 * 
 * USAGE: Upload to WordPress root, access: yourdomain.com/force-template-fix.php
 */

require_once('wp-load.php');

if (!current_user_can('manage_options')) {
    die('Admin access required');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Force Template Fix</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        h1 { color: #333; border-bottom: 3px solid #0073aa; padding-bottom: 10px; }
        h2 { color: #0073aa; margin-top: 30px; }
        .success { background: #d4edda; border: 2px solid #28a745; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; border: 2px solid #dc3545; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .warning { background: #fff3cd; border: 2px solid #ffc107; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; border: 2px solid #17a2b8; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .code { background: #f4f4f4; padding: 10px; border-left: 4px solid #0073aa; font-family: monospace; margin: 10px 0; overflow-x: auto; }
        .button { background: #0073aa; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }
        .button:hover { background: #005a87; }
        .button-danger { background: #dc3545; }
        .button-success { background: #28a745; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table th { background: #0073aa; color: white; padding: 12px; text-align: left; }
        table td { padding: 10px; border-bottom: 1px solid #ddd; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔧 Force Template Fix</h1>
    
    <?php
    
    // 1. CHECK PLUGIN TEMPLATE
    echo '<h2>1. Plugin Template Check</h2>';
    $plugin_template = WP_PLUGIN_DIR . '/jewellery-price-calculator/templates/frontend/price-breakup.php';
    
    if (file_exists($plugin_template)) {
        echo '<div class="success">✓ Plugin template exists</div>';
        
        $content = file_get_contents($plugin_template);
        
        // Check for correct code
        $has_stored_discount = strpos($content, "breakup['discount']") !== false;
        $has_wrong_calc = strpos($content, '$regular_price - $sale_price') !== false;
        
        echo '<table>';
        echo '<tr><th>Check</th><th>Status</th></tr>';
        echo '<tr><td>Uses stored discount</td><td style="background: ' . ($has_stored_discount ? '#d4edda' : '#f8d7da') . ';">' . ($has_stored_discount ? '✓ YES' : '✗ NO') . '</td></tr>';
        echo '<tr><td>Has wrong calculation</td><td style="background: ' . ($has_wrong_calc ? '#f8d7da' : '#d4edda') . ';">' . ($has_wrong_calc ? '✗ YES (BAD!)' : '✓ NO (GOOD)') . '</td></tr>';
        echo '</table>';
        
        if ($has_stored_discount && !$has_wrong_calc) {
            echo '<div class="success">✓ Plugin template is CORRECT!</div>';
        } else {
            echo '<div class="error">✗ Plugin template has issues!</div>';
        }
    } else {
        echo '<div class="error">✗ Plugin template NOT found at: ' . $plugin_template . '</div>';
    }
    
    // 2. CHECK THEME OVERRIDE
    echo '<h2>2. Theme Override Check</h2>';
    $theme = wp_get_theme();
    $theme_dir = get_stylesheet_directory();
    
    echo '<div class="info">Active Theme: <strong>' . $theme->get('Name') . '</strong></div>';
    
    $possible_overrides = array(
        $theme_dir . '/woocommerce/single-product/price-breakup.php',
        $theme_dir . '/jewellery-price-calculator/price-breakup.php',
        $theme_dir . '/templates/price-breakup.php',
        $theme_dir . '/woocommerce/price-breakup.php',
    );
    
    $found_override = false;
    foreach ($possible_overrides as $path) {
        if (file_exists($path)) {
            $found_override = true;
            echo '<div class="warning">⚠️ THEME OVERRIDE FOUND: ' . $path . '</div>';
            
            $override_content = file_get_contents($path);
            $has_wrong = strpos($override_content, '$regular_price - $sale_price') !== false;
            
            if ($has_wrong) {
                echo '<div class="error">✗ This override has WRONG calculation!</div>';
                echo '<p><strong>ACTION REQUIRED:</strong> Delete this file or update it with correct code.</p>';
            }
        }
    }
    
    if (!$found_override) {
        echo '<div class="success">✓ No theme overrides found (GOOD!)</div>';
    }
    
    // 3. CHECK WHICH TEMPLATE IS BEING USED
    echo '<h2>3. Active Template Detection</h2>';
    
    // Get a test product
    $products = get_posts(array(
        'post_type' => 'product',
        'posts_per_page' => 1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_jpc_metal_id',
                'compare' => 'EXISTS'
            )
        )
    ));
    
    if (!empty($products)) {
        $product_id = $products[0]->ID;
        
        // Simulate template loading
        global $product;
        $product = wc_get_product($product_id);
        
        // Check which template would be loaded
        $template_loader = new WC_Template_Loader();
        
        echo '<div class="info">';
        echo '<strong>Test Product:</strong> ' . get_the_title($product_id) . '<br>';
        echo '<strong>Product URL:</strong> <a href="' . get_permalink($product_id) . '" target="_blank">' . get_permalink($product_id) . '</a>';
        echo '</div>';
        
        // Check if template is being loaded correctly
        $breakup = get_post_meta($product_id, '_jpc_price_breakup', true);
        
        if ($breakup && isset($breakup['discount'])) {
            $stored_discount = $breakup['discount'];
            $regular_price = get_post_meta($product_id, '_regular_price', true);
            $sale_price = get_post_meta($product_id, '_sale_price', true);
            $wrong_calc = $regular_price - $sale_price;
            
            echo '<table>';
            echo '<tr><th>Method</th><th>Discount Amount</th><th>Correct?</th></tr>';
            echo '<tr><td>Stored (CORRECT)</td><td style="font-weight: bold; color: green;">₹' . number_format($stored_discount, 2) . '</td><td>✓</td></tr>';
            echo '<tr><td>Calculated (WRONG)</td><td style="font-weight: bold; color: red;">₹' . number_format($wrong_calc, 2) . '</td><td>✗</td></tr>';
            echo '</table>';
            
            echo '<div class="warning">';
            echo '<p><strong>What to check on frontend:</strong></p>';
            echo '<ol>';
            echo '<li>Visit the product page: <a href="' . get_permalink($product_id) . '" target="_blank">Click here</a></li>';
            echo '<li>Go to "Price Breakup" tab</li>';
            echo '<li>Check the discount amount</li>';
            echo '<li>If it shows <strong>₹' . number_format($wrong_calc, 2) . '</strong> → Template is WRONG</li>';
            echo '<li>If it shows <strong>₹' . number_format($stored_discount, 2) . '</strong> → Template is CORRECT</li>';
            echo '</ol>';
            echo '</div>';
        }
    }
    
    // 4. CACHE CHECK
    echo '<h2>4. Cache Check</h2>';
    
    $cache_plugins = array(
        'wp-super-cache/wp-cache.php' => 'WP Super Cache',
        'w3-total-cache/w3-total-cache.php' => 'W3 Total Cache',
        'wp-fastest-cache/wpFastestCache.php' => 'WP Fastest Cache',
        'wp-rocket/wp-rocket.php' => 'WP Rocket',
        'litespeed-cache/litespeed-cache.php' => 'LiteSpeed Cache',
    );
    
    $active_cache = array();
    foreach ($cache_plugins as $plugin => $name) {
        if (is_plugin_active($plugin)) {
            $active_cache[] = $name;
        }
    }
    
    if (!empty($active_cache)) {
        echo '<div class="warning">⚠️ Active cache plugins: ' . implode(', ', $active_cache) . '</div>';
        echo '<p><strong>ACTION REQUIRED:</strong> Clear cache in these plugins!</p>';
    } else {
        echo '<div class="success">✓ No cache plugins detected</div>';
    }
    
    // 5. ACTIONS
    echo '<h2>5. Fix Actions</h2>';
    
    if (isset($_GET['clear_cache'])) {
        // Clear WordPress object cache
        wp_cache_flush();
        
        // Clear WooCommerce transients
        if (function_exists('wc_delete_product_transients')) {
            wc_delete_product_transients();
        }
        
        // Clear all transients
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'");
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_site_transient_%'");
        
        echo '<div class="success">✓ Cache cleared!</div>';
        echo '<meta http-equiv="refresh" content="2">';
    }
    
    if (isset($_GET['delete_override']) && isset($_GET['file'])) {
        $file = base64_decode($_GET['file']);
        if (file_exists($file) && strpos($file, get_stylesheet_directory()) === 0) {
            if (unlink($file)) {
                echo '<div class="success">✓ Theme override deleted: ' . basename($file) . '</div>';
                echo '<meta http-equiv="refresh" content="2">';
            } else {
                echo '<div class="error">✗ Failed to delete file (check permissions)</div>';
            }
        }
    }
    
    echo '<p>';
    echo '<a href="?clear_cache=1" class="button button-success">🗑️ Clear All Cache</a> ';
    
    if ($found_override) {
        foreach ($possible_overrides as $path) {
            if (file_exists($path)) {
                $encoded = base64_encode($path);
                echo '<a href="?delete_override=1&file=' . $encoded . '" class="button button-danger" onclick="return confirm(\'Delete theme override: ' . basename($path) . '?\');">❌ Delete Override</a> ';
            }
        }
    }
    
    if (!empty($products)) {
        echo '<a href="' . get_permalink($product_id) . '" target="_blank" class="button">👁️ View Product</a> ';
    }
    
    echo '<a href="' . admin_url('admin.php?page=jpc-general-settings') . '" class="button">⚙️ Settings</a>';
    echo '</p>';
    
    // 6. SUMMARY
    echo '<h2>6. Summary & Next Steps</h2>';
    
    $issues = array();
    
    if (!file_exists($plugin_template)) {
        $issues[] = 'Plugin template missing - reinstall plugin';
    } elseif (!$has_stored_discount || $has_wrong_calc) {
        $issues[] = 'Plugin template has wrong code - update plugin files';
    }
    
    if ($found_override) {
        $issues[] = 'Theme override found - delete it or update it';
    }
    
    if (!empty($active_cache)) {
        $issues[] = 'Cache plugins active - clear cache';
    }
    
    if (empty($issues)) {
        echo '<div class="success">';
        echo '<h3>✓ No Issues Found!</h3>';
        echo '<p>Your template is correct. If you still see wrong prices:</p>';
        echo '<ol>';
        echo '<li>Click "Clear All Cache" button above</li>';
        echo '<li>Clear browser cache (Ctrl+Shift+Delete)</li>';
        echo '<li>View product in incognito/private window</li>';
        echo '<li>Check if discount matches stored value</li>';
        echo '</ol>';
        echo '</div>';
    } else {
        echo '<div class="error">';
        echo '<h3>⚠️ Issues Found:</h3>';
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
