<?php
/**
 * EMERGENCY PRICE FIX SCRIPT
 * Run this ONCE to fix all product prices immediately
 * 
 * HOW TO USE:
 * 1. Upload this file to: /public_html/wp-content/plugins/jewellery-price-calculator/
 * 2. Visit: https://yoursite.com/wp-content/plugins/jewellery-price-calculator/fix-prices-now.php
 * 3. Wait for "ALL DONE!" message
 * 4. DELETE this file after running
 */

// Find WordPress root - try multiple paths
$wp_load_paths = array(
    __DIR__ . '/../../../wp-load.php',  // Standard path
    __DIR__ . '/../../../../wp-load.php', // Alternative
    dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php', // Another alternative
);

$wp_loaded = false;
foreach ($wp_load_paths as $path) {
    if (file_exists($path)) {
        require_once($path);
        $wp_loaded = true;
        break;
    }
}

if (!$wp_loaded) {
    die('ERROR: Could not find wp-load.php. Please check the file path.');
}

// Security check - only admins can run this
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    die('ERROR: You must be logged in as admin to run this script. Please log in to WordPress admin first.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Emergency Price Fix</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2271b1; border-bottom: 3px solid #2271b1; padding-bottom: 10px; }
        .product { background: #f9f9f9; padding: 15px; margin: 10px 0; border-left: 4px solid #2271b1; }
        .success { color: #00a32a; font-weight: bold; }
        .error { color: #d63638; font-weight: bold; }
        .warning { color: #dba617; font-weight: bold; }
        .info { color: #2271b1; }
        hr { margin: 20px 0; border: none; border-top: 1px solid #ddd; }
        .summary { background: #e7f5e9; padding: 20px; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">

<h1>🔧 EMERGENCY PRICE FIX</h1>
<p>Recalculating ALL product prices with correct regular and sale prices...</p>
<hr>

<?php

// Get all products that use JPC
$args = array(
    'post_type' => 'product',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'meta_query' => array(
        array(
            'key' => '_jpc_metal_id',
            'compare' => 'EXISTS'
        )
    )
);

$products = get_posts($args);

echo "<p class='info'><strong>Found " . count($products) . " JPC products to fix.</strong></p>";
echo "<hr>";

$fixed = 0;
$errors = 0;

foreach ($products as $product) {
    $product_id = $product->ID;
    
    echo "<div class='product'>";
    echo "<strong>Product #{$product_id}: {$product->post_title}</strong><br>";
    
    // Get current stored prices
    $old_regular = get_post_meta($product_id, '_regular_price', true);
    $old_sale = get_post_meta($product_id, '_sale_price', true);
    $old_price = get_post_meta($product_id, '_price', true);
    
    echo "📊 <strong>OLD PRICES:</strong><br>";
    echo "&nbsp;&nbsp;&nbsp;Regular: ₹" . number_format($old_regular, 2) . "<br>";
    echo "&nbsp;&nbsp;&nbsp;Sale: ₹" . number_format($old_sale, 2) . "<br>";
    echo "&nbsp;&nbsp;&nbsp;Active: ₹" . number_format($old_price, 2) . "<br>";
    
    // Calculate correct prices using JPC
    if (!class_exists('JPC_Price_Calculator')) {
        echo "<span class='error'>❌ ERROR: JPC_Price_Calculator class not found!</span><br>";
        echo "</div>";
        $errors++;
        continue;
    }
    
    $prices = JPC_Price_Calculator::calculate_product_prices($product_id);
    
    if ($prices === false) {
        echo "<span class='error'>❌ FAILED to calculate prices</span><br>";
        echo "</div>";
        $errors++;
        continue;
    }
    
    // Update database directly
    update_post_meta($product_id, '_regular_price', $prices['regular_price']);
    
    if ($prices['discount_percentage'] > 0) {
        update_post_meta($product_id, '_sale_price', $prices['sale_price']);
        update_post_meta($product_id, '_price', $prices['sale_price']);
    } else {
        delete_post_meta($product_id, '_sale_price');
        update_post_meta($product_id, '_price', $prices['regular_price']);
    }
    
    // Clear all caches aggressively
    wc_delete_product_transients($product_id);
    wp_cache_delete($product_id, 'post_meta');
    wp_cache_delete($product_id, 'posts');
    clean_post_cache($product_id);
    
    // Clear WooCommerce product cache
    $cache_key = WC_Cache_Helper::get_cache_prefix('product_' . $product_id);
    wp_cache_delete($cache_key, 'products');
    
    echo "<br>✨ <strong>NEW PRICES:</strong><br>";
    echo "&nbsp;&nbsp;&nbsp;Regular: ₹" . number_format($prices['regular_price'], 2) . "<br>";
    echo "&nbsp;&nbsp;&nbsp;Sale: ₹" . number_format($prices['sale_price'], 2) . "<br>";
    echo "&nbsp;&nbsp;&nbsp;Discount: {$prices['discount_percentage']}%<br>";
    echo "<span class='success'>✅ FIXED & SAVED</span>";
    echo "</div>";
    
    $fixed++;
    
    // Prevent timeout and show progress
    if ($fixed % 5 == 0) {
        echo "<p class='info'><em>✓ Processed $fixed products so far...</em></p>";
        flush();
        ob_flush();
    }
}

// Clear global cache
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
}

// Clear WP Rocket cache if active
if (function_exists('rocket_clean_domain')) {
    rocket_clean_domain();
}

// Clear W3 Total Cache if active
if (function_exists('w3tc_flush_all')) {
    w3tc_flush_all();
}

?>

<hr>
<div class="summary">
    <h2 class="success">✅ ALL DONE!</h2>
    <p><strong>Successfully Fixed: <?php echo $fixed; ?> products</strong></p>
    <p><strong>Errors: <?php echo $errors; ?> products</strong></p>
</div>

<hr>
<div style="background: #fff3cd; padding: 20px; border-radius: 5px; border-left: 4px solid #dba617;">
    <h3 class="warning">⚠️ IMPORTANT: DELETE THIS FILE NOW!</h3>
    <p>For security reasons, please delete this file immediately:</p>
    <code style="background: #f5f5f5; padding: 10px; display: block; margin: 10px 0;">
        /public_html/wp-content/plugins/jewellery-price-calculator/fix-prices-now.php
    </code>
</div>

<hr>
<div style="background: #e7f5ff; padding: 20px; border-radius: 5px; border-left: 4px solid #2271b1;">
    <h3>📋 NEXT STEPS:</h3>
    <ol>
        <li><strong>Clear your browser cache:</strong> Press Ctrl+Shift+R (Windows) or Cmd+Shift+R (Mac)</li>
        <li><strong>Check the frontend:</strong> Visit your product pages to verify prices are correct</li>
        <li><strong>Delete this file</strong> from your server</li>
    </ol>
    <p><strong>✨ Your prices are now fixed and will update automatically when metal rates change!</strong></p>
</div>

</div>
</body>
</html>
