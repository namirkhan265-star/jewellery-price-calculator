<?php
/**
 * One-Time Fix Script for GST Calculation Base
 * v2.5.17: Converts 'original_price' to 'before_discount' for consistency
 * 
 * INSTRUCTIONS:
 * 1. Upload this file to your WordPress root directory
 * 2. Visit: https://yoursite.com/fix-gst-calculation-base.php
 * 3. Delete this file after running
 */

// Load WordPress
require_once('wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    die('Unauthorized access');
}

echo '<h1>JPC GST Calculation Base Fix</h1>';
echo '<p>This script will convert the GST calculation base value from "original_price" to "before_discount" for consistency.</p>';

// Get current value
$current_value = get_option('jpc_gst_calculation_base');

echo '<h2>Current Status:</h2>';
echo '<p><strong>Current Value:</strong> ' . esc_html($current_value) . '</p>';

// Fix the value if needed
if ($current_value === 'original_price') {
    update_option('jpc_gst_calculation_base', 'before_discount');
    echo '<p style="color: green;"><strong>✓ FIXED:</strong> Changed from "original_price" to "before_discount"</p>';
} elseif ($current_value === 'before_discount') {
    echo '<p style="color: blue;"><strong>✓ OK:</strong> Already set to "before_discount" - no changes needed</p>';
} elseif ($current_value === 'after_discount') {
    echo '<p style="color: blue;"><strong>✓ OK:</strong> Set to "after_discount" - no changes needed</p>';
} else {
    echo '<p style="color: orange;"><strong>⚠ WARNING:</strong> Unexpected value "' . esc_html($current_value) . '" - setting to default "after_discount"</p>';
    update_option('jpc_gst_calculation_base', 'after_discount');
}

// Verify the fix
$new_value = get_option('jpc_gst_calculation_base');
echo '<h2>After Fix:</h2>';
echo '<p><strong>New Value:</strong> ' . esc_html($new_value) . '</p>';

echo '<hr>';
echo '<h2>✅ Done!</h2>';
echo '<p><strong>IMPORTANT:</strong> Please delete this file (fix-gst-calculation-base.php) from your server now for security.</p>';
echo '<p>You can now go to <a href="' . admin_url('admin.php?page=jewellery-price-calc') . '">General Settings</a> and the GST Calculation Base should work correctly.</p>';
