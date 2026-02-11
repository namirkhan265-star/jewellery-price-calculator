<?php
/**
 * Debug GST Values on Product Page
 * 
 * Add this to your product page temporarily to see what values are being loaded
 */

// This should be added to the template temporarily
$product_id = get_the_ID();

echo '<div style="background: #fff3cd; border: 2px solid #ffc107; padding: 20px; margin: 20px 0; font-family: monospace;">';
echo '<h3 style="color: #856404;">🔍 DEBUG: GST Values</h3>';

// Check GST settings
$gst_label = get_option('jpc_gst_label', 'GST');
$enable_gst = get_option('jpc_enable_gst', 'yes');
$gst_value = get_option('jpc_gst_value', '3');
$gst_percentage = 0;

if ($enable_gst === 'yes') {
    $gst_percentage = floatval($gst_value);
}

echo '<table style="width: 100%; border-collapse: collapse;">';
echo '<tr><td style="padding: 8px; border: 1px solid #ddd; background: #f0f0f0;"><strong>GST Label (from settings)</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . esc_html($gst_label) . '</td></tr>';
echo '<tr><td style="padding: 8px; border: 1px solid #ddd; background: #f0f0f0;"><strong>Enable GST (from settings)</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . esc_html($enable_gst) . '</td></tr>';
echo '<tr><td style="padding: 8px; border: 1px solid #ddd; background: #f0f0f0;"><strong>GST Value (from settings)</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . esc_html($gst_value) . '</td></tr>';
echo '<tr><td style="padding: 8px; border: 1px solid #ddd; background: #f0f0f0;"><strong>GST Percentage (calculated)</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . $gst_percentage . '</td></tr>';

// Check breakup data
$breakup = get_post_meta($product_id, '_jpc_price_breakup', true);
if ($breakup && is_array($breakup)) {
    echo '<tr><td style="padding: 8px; border: 1px solid #ddd; background: #f0f0f0;"><strong>GST Amount (from breakup)</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . (isset($breakup['gst']) ? $breakup['gst'] : 'NOT SET') . '</td></tr>';
    echo '<tr><td style="padding: 8px; border: 1px solid #ddd; background: #f0f0f0;"><strong>Diamond Price (from breakup)</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . (isset($breakup['diamond_price']) ? $breakup['diamond_price'] : 'NOT SET') . '</td></tr>';
}

echo '</table>';

echo '<h4 style="color: #856404; margin-top: 20px;">What Should Display:</h4>';
if ($gst_percentage > 0) {
    $gst_display = (floor($gst_percentage) == $gst_percentage) 
        ? number_format($gst_percentage, 0) 
        : number_format($gst_percentage, 2);
    echo '<p><strong>GST Label:</strong> ' . esc_html($gst_label) . ' (' . $gst_display . '%)</p>';
} else {
    echo '<p style="color: red;"><strong>⚠️ GST Percentage is 0!</strong> This is why percentage is not showing.</p>';
}

echo '</div>';
?>
