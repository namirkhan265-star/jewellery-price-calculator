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

// Load WordPress
require_once('../../../wp-load.php');

// Security check - only admins can run this
if (!current_user_can('manage_options')) {
    die('ERROR: You must be logged in as admin to run this script.');
}

echo "<h1>🔧 EMERGENCY PRICE FIX</h1>";
echo "<p>Recalculating ALL product prices...</p>";
echo "<hr>";

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

echo "<p>Found " . count($products) . " JPC products to fix.</p>";
echo "<hr>";

$fixed = 0;
$errors = 0;

foreach ($products as $product) {
    $product_id = $product->ID;
    
    echo "<p><strong>Product #{$product_id}: {$product->post_title}</strong><br>";
    
    // Get current stored prices
    $old_regular = get_post_meta($product_id, '_regular_price', true);
    $old_sale = get_post_meta($product_id, '_sale_price', true);
    
    echo "Old Regular: ₹" . number_format($old_regular, 2) . "<br>";
    echo "Old Sale: ₹" . number_format($old_sale, 2) . "<br>";
    
    // Calculate correct prices using JPC
    $prices = JPC_Price_Calculator::calculate_product_prices($product_id);
    
    if ($prices === false) {
        echo "<span style='color:red;'>❌ FAILED to calculate</span></p>";
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
    
    // Clear all caches
    wc_delete_product_transients($product_id);
    wp_cache_delete($product_id, 'post_meta');
    wp_cache_delete($product_id, 'posts');
    clean_post_cache($product_id);
    
    echo "New Regular: ₹" . number_format($prices['regular_price'], 2) . "<br>";
    echo "New Sale: ₹" . number_format($prices['sale_price'], 2) . "<br>";
    echo "Discount: {$prices['discount_percentage']}%<br>";
    echo "<span style='color:green;'>✅ FIXED</span></p>";
    echo "<hr>";
    
    $fixed++;
    
    // Prevent timeout
    if ($fixed % 10 == 0) {
        echo "<p><em>Processed $fixed products so far...</em></p>";
        flush();
    }
}

// Clear global cache
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
}

echo "<h2 style='color:green;'>✅ ALL DONE!</h2>";
echo "<p><strong>Fixed: $fixed products</strong></p>";
echo "<p><strong>Errors: $errors products</strong></p>";
echo "<hr>";
echo "<p><strong>⚠️ IMPORTANT: DELETE THIS FILE NOW!</strong></p>";
echo "<p>For security, delete: <code>/public_html/wp-content/plugins/jewellery-price-calculator/fix-prices-now.php</code></p>";
echo "<hr>";
echo "<p>Now clear your browser cache (Ctrl+Shift+R) and check the frontend!</p>";
