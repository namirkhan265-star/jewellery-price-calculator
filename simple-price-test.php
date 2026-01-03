<?php
/**
 * SIMPLE PRICE TEST
 * Quick test to see if prices are calculating correctly
 * 
 * USAGE: Upload to WordPress root, access: yourdomain.com/simple-price-test.php
 */

require_once('wp-load.php');

if (!current_user_can('manage_options')) {
    die('Admin access required');
}

// Get first JPC product
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

if (empty($products)) {
    die('No JPC products found');
}

$product_id = $products[0]->ID;

echo '<h1>Simple Price Test - Product ID: ' . $product_id . '</h1>';
echo '<h2>' . get_the_title($product_id) . '</h2>';

// 1. Get stored breakup
echo '<h3>1. STORED BREAKUP DATA</h3>';
$breakup = get_post_meta($product_id, '_jpc_price_breakup', true);

if (!$breakup) {
    echo '<p style="color: red; font-weight: bold;">❌ NO BREAKUP DATA FOUND!</p>';
    echo '<p>Click here to fix: <a href="' . admin_url('admin.php?page=jpc-general-settings') . '">Update All Prices</a></p>';
} else {
    echo '<pre>';
    print_r($breakup);
    echo '</pre>';
    
    echo '<table border="1" cellpadding="10">';
    echo '<tr><th>Key</th><th>Value</th></tr>';
    echo '<tr><td>discount</td><td style="font-weight: bold; color: blue;">₹' . number_format($breakup['discount'], 2) . '</td></tr>';
    echo '<tr><td>gst</td><td style="font-weight: bold; color: blue;">₹' . number_format($breakup['gst'], 2) . '</td></tr>';
    echo '<tr><td>regular_price (from breakup)</td><td>₹' . number_format($breakup['subtotal'] ?? 0, 2) . '</td></tr>';
    echo '</table>';
}

// 2. Get WooCommerce prices
echo '<h3>2. WOOCOMMERCE STORED PRICES</h3>';
$regular_price = get_post_meta($product_id, '_regular_price', true);
$sale_price = get_post_meta($product_id, '_sale_price', true);
$discount_pct = get_post_meta($product_id, '_jpc_discount_percentage', true);

echo '<table border="1" cellpadding="10">';
echo '<tr><th>Meta Key</th><th>Value</th></tr>';
echo '<tr><td>_regular_price</td><td style="font-weight: bold;">₹' . number_format($regular_price, 2) . '</td></tr>';
echo '<tr><td>_sale_price</td><td style="font-weight: bold;">₹' . number_format($sale_price, 2) . '</td></tr>';
echo '<tr><td>_jpc_discount_percentage</td><td style="font-weight: bold;">' . $discount_pct . '%</td></tr>';
echo '<tr><td>Calculated Discount (wrong way)</td><td style="color: red;">₹' . number_format($regular_price - $sale_price, 2) . '</td></tr>';
echo '</table>';

// 3. Recalculate
echo '<h3>3. LIVE CALCULATION</h3>';
if (class_exists('JPC_Price_Calculator')) {
    $calculated = JPC_Price_Calculator::calculate_product_prices($product_id);
    
    if ($calculated) {
        echo '<table border="1" cellpadding="10">';
        echo '<tr><th>Component</th><th>Value</th></tr>';
        echo '<tr><td>discount_amount</td><td style="font-weight: bold; color: green;">₹' . number_format($calculated['discount_amount'], 2) . '</td></tr>';
        echo '<tr><td>gst_on_discounted</td><td style="font-weight: bold; color: green;">₹' . number_format($calculated['gst_on_discounted'], 2) . '</td></tr>';
        echo '<tr><td>regular_price</td><td>₹' . number_format($calculated['regular_price'], 2) . '</td></tr>';
        echo '<tr><td>sale_price</td><td>₹' . number_format($calculated['sale_price'], 2) . '</td></tr>';
        echo '</table>';
    } else {
        echo '<p style="color: red;">Calculation failed</p>';
    }
}

// 4. Comparison
echo '<h3>4. COMPARISON</h3>';
if ($breakup && $calculated) {
    $stored_discount = isset($breakup['discount']) ? $breakup['discount'] : 0;
    $calc_discount = $calculated['discount_amount'];
    $match = abs($stored_discount - $calc_discount) < 0.01;
    
    echo '<table border="1" cellpadding="10">';
    echo '<tr><th>Check</th><th>Stored</th><th>Calculated</th><th>Match?</th></tr>';
    echo '<tr>';
    echo '<td>Discount</td>';
    echo '<td>₹' . number_format($stored_discount, 2) . '</td>';
    echo '<td>₹' . number_format($calc_discount, 2) . '</td>';
    echo '<td style="background: ' . ($match ? '#d4edda' : '#f8d7da') . '; font-weight: bold;">' . ($match ? '✓ YES' : '✗ NO') . '</td>';
    echo '</tr>';
    echo '</table>';
    
    if (!$match) {
        echo '<p style="color: red; font-weight: bold;">⚠️ MISMATCH DETECTED! Click "Update All Prices" button.</p>';
    } else {
        echo '<p style="color: green; font-weight: bold;">✓ Prices match! Everything is correct.</p>';
    }
}

// 5. Actions
echo '<h3>5. ACTIONS</h3>';

if (isset($_GET['update'])) {
    echo '<p style="background: #fff3cd; padding: 15px; border: 2px solid #ffc107;">Updating product...</p>';
    $result = JPC_Price_Calculator::calculate_and_update_price($product_id);
    if ($result === true) {
        echo '<p style="background: #d4edda; padding: 15px; border: 2px solid #28a745;">✓ Update successful!</p>';
        echo '<meta http-equiv="refresh" content="2">';
    } else {
        echo '<p style="background: #f8d7da; padding: 15px; border: 2px solid #dc3545;">✗ Update failed!</p>';
    }
}

echo '<p>';
echo '<a href="?update=1" style="background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">🔄 Update This Product</a> ';
echo '<a href="' . get_permalink($product_id) . '" target="_blank" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">👁️ View Product</a> ';
echo '<a href="' . admin_url('admin.php?page=jpc-general-settings') . '" style="background: #ffc107; color: #000; padding: 10px 20px; text-decoration: none; border-radius: 5px;">⚙️ Settings</a>';
echo '</p>';

// 6. Frontend template check
echo '<h3>6. FRONTEND TEMPLATE CHECK</h3>';
$template_path = WP_PLUGIN_DIR . '/jewellery-price-calculator/templates/frontend/price-breakup.php';
if (file_exists($template_path)) {
    $content = file_get_contents($template_path);
    
    $has_stored_discount = strpos($content, "breakup['discount']") !== false;
    $has_calc_discount = strpos($content, '$regular_price - $sale_price') !== false;
    
    echo '<table border="1" cellpadding="10">';
    echo '<tr><th>Check</th><th>Status</th></tr>';
    echo '<tr><td>Uses stored discount</td><td style="background: ' . ($has_stored_discount ? '#d4edda' : '#f8d7da') . ';">' . ($has_stored_discount ? '✓ YES' : '✗ NO') . '</td></tr>';
    echo '<tr><td>Calculates discount (BAD)</td><td style="background: ' . ($has_calc_discount ? '#f8d7da' : '#d4edda') . ';">' . ($has_calc_discount ? '✗ YES (BAD!)' : '✓ NO (GOOD)') . '</td></tr>';
    echo '</table>';
    
    if ($has_calc_discount) {
        echo '<p style="color: red; font-weight: bold;">⚠️ PROBLEM FOUND! Template is calculating discount instead of using stored value!</p>';
    } else {
        echo '<p style="color: green; font-weight: bold;">✓ Template is correct!</p>';
    }
} else {
    echo '<p style="color: red;">Template file not found!</p>';
}
