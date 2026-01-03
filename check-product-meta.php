<?php
/**
 * Diagnostic: Check what's stored in _jpc_price_breakup meta
 * This will show you the cached price data that the frontend is displaying
 */

// Load WordPress
require_once(__DIR__ . '/../../../wp-load.php');

if (!current_user_can('manage_options')) {
    die('Access denied');
}

// Get product ID from URL or use default
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if (!$product_id) {
    // Try to find any product with JPC data
    global $wpdb;
    $product_id = $wpdb->get_var("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_jpc_price_breakup' LIMIT 1");
}

if (!$product_id) {
    die('<h1>No product found</h1><p>Add ?product_id=XXX to the URL</p>');
}

$product = wc_get_product($product_id);
if (!$product) {
    die('<h1>Product not found</h1>');
}

echo '<h1>Product: ' . $product->get_name() . ' (ID: ' . $product_id . ')</h1>';
echo '<hr>';

// Get all JPC meta
$price_breakup = get_post_meta($product_id, '_jpc_price_breakup', true);
$discount_percentage = get_post_meta($product_id, '_jpc_discount_percentage', true);
$metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
$metal_weight = get_post_meta($product_id, '_jpc_metal_weight', true);

// Get global settings
$discount_method = get_option('jpc_discount_calculation_method', 'NOT SET');
$gst_calculation_base = get_option('jpc_gst_calculation_base', 'NOT SET');

echo '<h2>Global Settings:</h2>';
echo '<table border="1" cellpadding="10" style="border-collapse: collapse;">';
echo '<tr><td><strong>Discount Calculation Method</strong></td><td>' . $discount_method . '</td></tr>';
echo '<tr><td><strong>GST Calculation Base</strong></td><td>' . $gst_calculation_base . '</td></tr>';
echo '</table>';

echo '<h2>Product Meta:</h2>';
echo '<table border="1" cellpadding="10" style="border-collapse: collapse;">';
echo '<tr><td><strong>Metal ID</strong></td><td>' . $metal_id . '</td></tr>';
echo '<tr><td><strong>Metal Weight</strong></td><td>' . $metal_weight . ' grams</td></tr>';
echo '<tr><td><strong>Discount %</strong></td><td>' . $discount_percentage . '%</td></tr>';
echo '</table>';

echo '<h2>Stored Price Breakup (_jpc_price_breakup):</h2>';
if ($price_breakup && is_array($price_breakup)) {
    echo '<table border="1" cellpadding="10" style="border-collapse: collapse; width: 100%;">';
    echo '<tr><th>Component</th><th>Value</th></tr>';
    
    foreach ($price_breakup as $key => $value) {
        if ($key === 'extra_fields' && is_array($value)) {
            echo '<tr><td><strong>Extra Fields</strong></td><td>';
            echo '<table border="1" cellpadding="5" style="border-collapse: collapse; width: 100%;">';
            foreach ($value as $field) {
                echo '<tr><td>' . $field['label'] . '</td><td>₹ ' . number_format($field['value'], 2) . '</td></tr>';
            }
            echo '</table>';
            echo '</td></tr>';
        } else {
            $formatted_value = is_numeric($value) ? '₹ ' . number_format($value, 2) : $value;
            echo '<tr><td><strong>' . ucwords(str_replace('_', ' ', $key)) . '</strong></td><td>' . $formatted_value . '</td></tr>';
        }
    }
    
    echo '</table>';
    
    // Calculate what it SHOULD be
    echo '<hr>';
    echo '<h2>Expected Calculation (Based on Current Settings):</h2>';
    
    $subtotal_before_additional = 0;
    $subtotal_before_additional += !empty($price_breakup['metal_price']) ? floatval($price_breakup['metal_price']) : 0;
    $subtotal_before_additional += !empty($price_breakup['diamond_price']) ? floatval($price_breakup['diamond_price']) : 0;
    $subtotal_before_additional += !empty($price_breakup['making_charge']) ? floatval($price_breakup['making_charge']) : 0;
    $subtotal_before_additional += !empty($price_breakup['wastage_charge']) ? floatval($price_breakup['wastage_charge']) : 0;
    $subtotal_before_additional += !empty($price_breakup['pearl_cost']) ? floatval($price_breakup['pearl_cost']) : 0;
    $subtotal_before_additional += !empty($price_breakup['stone_cost']) ? floatval($price_breakup['stone_cost']) : 0;
    $subtotal_before_additional += !empty($price_breakup['extra_fee']) ? floatval($price_breakup['extra_fee']) : 0;
    
    if (!empty($price_breakup['extra_fields']) && is_array($price_breakup['extra_fields'])) {
        foreach ($price_breakup['extra_fields'] as $field) {
            $subtotal_before_additional += !empty($field['value']) ? floatval($field['value']) : 0;
        }
    }
    
    $additional_percentage_amount = !empty($price_breakup['additional_percentage']) ? floatval($price_breakup['additional_percentage']) : 0;
    $subtotal_after_additional = $subtotal_before_additional + $additional_percentage_amount;
    
    // Calculate discount based on method
    $expected_discount = 0;
    if ($discount_percentage > 0) {
        switch ($discount_method) {
            case '1':
            case 'simple':
                $base = $price_breakup['metal_price'] + $price_breakup['making_charge'] + $price_breakup['wastage_charge'];
                $expected_discount = ($base * $discount_percentage) / 100;
                break;
            case '2':
            case 'advanced':
                $expected_discount = ($subtotal_before_additional * $discount_percentage) / 100;
                break;
            case '3':
            case 'total_before_gst':
            case '4':
            case 'total_after_additional':
                $expected_discount = ($subtotal_after_additional * $discount_percentage) / 100;
                break;
        }
    }
    
    $subtotal_after_discount = $subtotal_after_additional - $expected_discount;
    $expected_gst = ($subtotal_after_discount * 5) / 100; // Assuming 5% GST
    $expected_final = $subtotal_after_discount + $expected_gst;
    
    echo '<table border="1" cellpadding="10" style="border-collapse: collapse; width: 100%;">';
    echo '<tr><th>Component</th><th>Stored Value</th><th>Expected Value</th><th>Match?</th></tr>';
    
    $stored_discount = !empty($price_breakup['discount']) ? floatval($price_breakup['discount']) : 0;
    $stored_gst = !empty($price_breakup['gst']) ? floatval($price_breakup['gst']) : 0;
    $stored_final = !empty($price_breakup['final_price']) ? floatval($price_breakup['final_price']) : 0;
    
    $discount_match = abs($stored_discount - $expected_discount) < 1 ? '✅' : '❌';
    $gst_match = abs($stored_gst - $expected_gst) < 1 ? '✅' : '❌';
    $final_match = abs($stored_final - $expected_final) < 1 ? '✅' : '❌';
    
    echo '<tr><td><strong>Subtotal (after additional %)</strong></td><td>₹ ' . number_format($subtotal_after_additional, 2) . '</td><td>₹ ' . number_format($subtotal_after_additional, 2) . '</td><td>✅</td></tr>';
    echo '<tr><td><strong>Discount (' . $discount_percentage . '%)</strong></td><td>₹ ' . number_format($stored_discount, 2) . '</td><td>₹ ' . number_format($expected_discount, 2) . '</td><td>' . $discount_match . '</td></tr>';
    echo '<tr><td><strong>GST (5%)</strong></td><td>₹ ' . number_format($stored_gst, 2) . '</td><td>₹ ' . number_format($expected_gst, 2) . '</td><td>' . $gst_match . '</td></tr>';
    echo '<tr><td><strong>Final Price</strong></td><td>₹ ' . number_format($stored_final, 2) . '</td><td>₹ ' . number_format($expected_final, 2) . '</td><td>' . $final_match . '</td></tr>';
    echo '</table>';
    
    if ($discount_match === '❌' || $gst_match === '❌' || $final_match === '❌') {
        echo '<div style="background: #ffebee; padding: 20px; margin: 20px 0; border-left: 4px solid #f44336;">';
        echo '<h3 style="color: #c62828; margin-top: 0;">⚠️ MISMATCH DETECTED!</h3>';
        echo '<p><strong>The stored price breakup does not match the expected calculation based on your current settings.</strong></p>';
        echo '<p><strong>Solution:</strong></p>';
        echo '<ol>';
        echo '<li>Go to the product edit page</li>';
        echo '<li>Click the "Update" button (you don\'t need to change anything)</li>';
        echo '<li>This will recalculate and save the correct prices</li>';
        echo '</ol>';
        echo '</div>';
    } else {
        echo '<div style="background: #e8f5e9; padding: 20px; margin: 20px 0; border-left: 4px solid #4caf50;">';
        echo '<h3 style="color: #2e7d32; margin-top: 0;">✅ CALCULATIONS MATCH!</h3>';
        echo '<p>The stored price breakup matches the expected calculation.</p>';
        echo '<p>If the frontend is still showing wrong values, there might be a caching issue.</p>';
        echo '</div>';
    }
    
} else {
    echo '<p style="color: red;">No price breakup data found!</p>';
    echo '<p>This product needs to be saved to generate price breakup data.</p>';
}

echo '<hr>';
echo '<p><a href="' . admin_url('post.php?post=' . $product_id . '&action=edit') . '">Edit this product</a></p>';
echo '<p style="color: red; font-weight: bold;">⚠️ DELETE THIS SCRIPT AFTER USE!</p>';
?>
