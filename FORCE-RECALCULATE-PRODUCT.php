<?php
/**
 * FORCE RECALCULATE PRODUCT SCRIPT
 * 
 * This script forces a product to recalculate its price and breakup.
 * Use this after uploading v2.5.23 to clear old cached price breakup.
 * 
 * INSTRUCTIONS:
 * 1. Upload this file to your WordPress root directory
 * 2. Visit: https://yoursite.com/FORCE-RECALCULATE-PRODUCT.php?product_id=YOUR_PRODUCT_ID
 * 3. Delete this file after use for security
 * 
 * Example: https://yoursite.com/FORCE-RECALCULATE-PRODUCT.php?product_id=123
 */

// Load WordPress
require_once('wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('ERROR: You must be logged in as an administrator to use this script.');
}

// Get product ID from URL
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if (!$product_id) {
    die('ERROR: Please provide a product_id parameter. Example: ?product_id=123');
}

// Check if product exists
$product = wc_get_product($product_id);
if (!$product) {
    die('ERROR: Product #' . $product_id . ' not found.');
}

echo '<h1>Force Recalculate Product #' . $product_id . '</h1>';
echo '<p><strong>Product:</strong> ' . $product->get_name() . '</p>';

// Show BEFORE state
echo '<h2>BEFORE Recalculation:</h2>';
$old_breakup = get_post_meta($product_id, '_jpc_price_breakup', true);
echo '<pre>';
print_r($old_breakup);
echo '</pre>';

// Clear all caches
wp_cache_delete('product-' . $product_id, 'products');
wp_cache_delete($product_id, 'post_meta');
wc_delete_product_transients($product_id);
clean_post_cache($product_id);

echo '<h2>Recalculating...</h2>';

// Force recalculate
if (class_exists('JPC_Price_Calculator')) {
    $success = JPC_Price_Calculator::calculate_and_update_price($product_id);
    
    if ($success) {
        echo '<p style="color: green; font-weight: bold;">✓ Recalculation successful!</p>';
    } else {
        echo '<p style="color: red; font-weight: bold;">✗ Recalculation failed!</p>';
    }
} else {
    die('ERROR: JPC_Price_Calculator class not found. Make sure the plugin is active.');
}

// Show AFTER state
echo '<h2>AFTER Recalculation:</h2>';
// Clear cache again to get fresh data
wp_cache_delete($product_id, 'post_meta');
$new_breakup = get_post_meta($product_id, '_jpc_price_breakup', true);
echo '<pre>';
print_r($new_breakup);
echo '</pre>';

// Show comparison
echo '<h2>Extra Fields Comparison:</h2>';
echo '<table border="1" cellpadding="10" style="border-collapse: collapse;">';
echo '<tr><th>Field</th><th>BEFORE</th><th>AFTER</th><th>Status</th></tr>';

$old_extra = isset($old_breakup['extra_fields']) ? $old_breakup['extra_fields'] : array();
$new_extra = isset($new_breakup['extra_fields']) ? $new_breakup['extra_fields'] : array();

echo '<tr>';
echo '<td>Extra Fields Count</td>';
echo '<td>' . count($old_extra) . '</td>';
echo '<td>' . count($new_extra) . '</td>';
echo '<td>' . (count($new_extra) < count($old_extra) ? '<span style="color: green;">✓ FIXED</span>' : '<span style="color: orange;">Same</span>') . '</td>';
echo '</tr>';

echo '</table>';

echo '<h3>OLD Extra Fields:</h3>';
if (empty($old_extra)) {
    echo '<p>None</p>';
} else {
    echo '<ul>';
    foreach ($old_extra as $field) {
        echo '<li>Field #' . $field['field_number'] . ': ' . $field['label'] . ' = ₹' . $field['value'] . '</li>';
    }
    echo '</ul>';
}

echo '<h3>NEW Extra Fields:</h3>';
if (empty($new_extra)) {
    echo '<p style="color: green; font-weight: bold;">✓ None (disabled fields removed!)</p>';
} else {
    echo '<ul>';
    foreach ($new_extra as $field) {
        $enabled = get_option('jpc_enable_extra_field_' . $field['field_number'], 'yes');
        $status = ($enabled === 'yes') ? '<span style="color: green;">✓ Enabled</span>' : '<span style="color: red;">✗ Disabled (should not be here!)</span>';
        echo '<li>Field #' . $field['field_number'] . ': ' . $field['label'] . ' = ₹' . $field['value'] . ' - ' . $status . '</li>';
    }
    echo '</ul>';
}

echo '<hr>';
echo '<h2>Settings Check:</h2>';
echo '<table border="1" cellpadding="10" style="border-collapse: collapse;">';
echo '<tr><th>Field</th><th>Enabled?</th><th>Label</th></tr>';
for ($i = 1; $i <= 5; $i++) {
    $enabled = get_option('jpc_enable_extra_field_' . $i, 'yes');
    $label = get_option('jpc_extra_field_label_' . $i, 'Extra Field #' . $i);
    $value = get_post_meta($product_id, '_jpc_extra_field_' . $i, true);
    
    echo '<tr>';
    echo '<td>Extra Field ' . $i . '</td>';
    echo '<td>' . ($enabled === 'yes' ? '<span style="color: green;">✓ YES</span>' : '<span style="color: red;">✗ NO</span>') . '</td>';
    echo '<td>' . $label . ' (Value: ₹' . $value . ')</td>';
    echo '</tr>';
}
echo '</table>';

echo '<hr>';
echo '<p><strong>Next Steps:</strong></p>';
echo '<ol>';
echo '<li>Check the "NEW Extra Fields" section above - it should only show enabled fields</li>';
echo '<li>Go to your product page and check the Price Breakup accordion</li>';
echo '<li>If fixed, delete this script file for security</li>';
echo '<li>If not fixed, check the Settings Check table to see which fields are enabled</li>';
echo '</ol>';

echo '<p style="color: red; font-weight: bold;">⚠️ IMPORTANT: Delete this file after use for security!</p>';
?>
