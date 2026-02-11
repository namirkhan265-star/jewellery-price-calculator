<?php
/**
 * DEBUG SCRIPT - Check Product Breakup Data
 * 
 * INSTRUCTIONS:
 * 1. Upload to: /wp-content/plugins/jewellery-price-calculator-main/
 * 2. Visit: https://detailx.co.in/wp-content/plugins/jewellery-price-calculator-main/debug-product-breakup.php?product_id=2637
 * 3. This will show you EXACTLY what's stored in the product
 */

// Load WordPress
require_once('../../../wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    die('ERROR: You must be logged in as an administrator.');
}

$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if (!$product_id) {
    die('Please provide product_id in URL. Example: ?product_id=2637');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Product Breakup</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 5px; max-width: 1200px; margin: 0 auto; }
        h1 { color: #2271b1; }
        .section { margin: 20px 0; padding: 15px; background: #f9f9f9; border-left: 4px solid #2271b1; }
        .key { color: #d63384; font-weight: bold; }
        .value { color: #0d6efd; }
        pre { background: #282c34; color: #abb2bf; padding: 15px; border-radius: 5px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        table td { padding: 8px; border: 1px solid #ddd; }
        table td:first-child { background: #f0f0f0; font-weight: bold; width: 30%; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Product Breakup Debug - ID: <?php echo $product_id; ?></h1>
        
        <?php
        $product_name = get_the_title($product_id);
        echo '<h2>' . esc_html($product_name) . '</h2>';
        
        // Get all relevant meta
        $breakup = get_post_meta($product_id, '_jpc_price_breakup', true);
        $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
        $diamond_id = get_post_meta($product_id, '_jpc_diamond_id', true);
        $diamond_quantity = get_post_meta($product_id, '_jpc_diamond_quantity', true);
        $diamond_entry_mode = get_post_meta($product_id, '_jpc_diamond_entry_mode', true);
        
        // Manual diamond fields
        $manual_diamond_group_id = get_post_meta($product_id, '_jpc_manual_diamond_group_id', true);
        $manual_diamond_cert_id = get_post_meta($product_id, '_jpc_manual_diamond_cert_id', true);
        $manual_diamond_shape_id = get_post_meta($product_id, '_jpc_manual_diamond_shape_id', true);
        $manual_diamond_colour_id = get_post_meta($product_id, '_jpc_manual_diamond_colour_id', true);
        $manual_diamond_clarity_id = get_post_meta($product_id, '_jpc_manual_diamond_clarity_id', true);
        $manual_diamond_cut_id = get_post_meta($product_id, '_jpc_manual_diamond_cut_id', true);
        $manual_diamond_quantity = get_post_meta($product_id, '_jpc_manual_diamond_quantity', true);
        $manual_diamond_price_per_carat = get_post_meta($product_id, '_jpc_manual_diamond_price_per_carat', true);
        
        echo '<div class="section">';
        echo '<h3>📊 Product Meta Data</h3>';
        echo '<table>';
        echo '<tr><td>Metal ID</td><td>' . esc_html($metal_id) . '</td></tr>';
        echo '<tr><td>Diamond Entry Mode</td><td>' . esc_html($diamond_entry_mode ?: 'dropdown') . '</td></tr>';
        echo '<tr><td>Diamond ID (Dropdown)</td><td>' . esc_html($diamond_id) . '</td></tr>';
        echo '<tr><td>Diamond Quantity (Dropdown)</td><td>' . esc_html($diamond_quantity) . '</td></tr>';
        echo '<tr><td>Manual Diamond Group ID</td><td>' . esc_html($manual_diamond_group_id) . '</td></tr>';
        echo '<tr><td>Manual Diamond Cert ID</td><td>' . esc_html($manual_diamond_cert_id) . '</td></tr>';
        echo '<tr><td>Manual Diamond Shape ID</td><td>' . esc_html($manual_diamond_shape_id) . '</td></tr>';
        echo '<tr><td>Manual Diamond Colour ID</td><td>' . esc_html($manual_diamond_colour_id) . '</td></tr>';
        echo '<tr><td>Manual Diamond Clarity ID</td><td>' . esc_html($manual_diamond_clarity_id) . '</td></tr>';
        echo '<tr><td>Manual Diamond Cut ID</td><td>' . esc_html($manual_diamond_cut_id) . '</td></tr>';
        echo '<tr><td>Manual Diamond Quantity</td><td>' . esc_html($manual_diamond_quantity) . '</td></tr>';
        echo '<tr><td>Manual Diamond Price/Carat</td><td>' . esc_html($manual_diamond_price_per_carat) . '</td></tr>';
        echo '</table>';
        echo '</div>';
        
        echo '<div class="section">';
        echo '<h3>💎 Price Breakup Data</h3>';
        if ($breakup && is_array($breakup)) {
            echo '<table>';
            foreach ($breakup as $key => $value) {
                if (is_array($value)) {
                    echo '<tr><td>' . esc_html($key) . '</td><td><pre>' . print_r($value, true) . '</pre></td></tr>';
                } else {
                    echo '<tr><td>' . esc_html($key) . '</td><td>' . esc_html($value) . '</td></tr>';
                }
            }
            echo '</table>';
        } else {
            echo '<p style="color: red;">❌ No breakup data found!</p>';
        }
        echo '</div>';
        
        echo '<div class="section">';
        echo '<h3>📝 Full Breakup Array (Raw)</h3>';
        echo '<pre>';
        print_r($breakup);
        echo '</pre>';
        echo '</div>';
        
        // Check GST settings
        echo '<div class="section">';
        echo '<h3>⚙️ GST Settings</h3>';
        echo '<table>';
        echo '<tr><td>Enable GST</td><td>' . get_option('jpc_enable_gst', 'yes') . '</td></tr>';
        echo '<tr><td>GST Label</td><td>' . get_option('jpc_gst_label', 'GST') . '</td></tr>';
        echo '<tr><td>GST Value</td><td>' . get_option('jpc_gst_value', '3') . '%</td></tr>';
        echo '</table>';
        echo '</div>';
        
        ?>
        
        <div class="section">
            <h3>🔧 Actions</h3>
            <p>If diamond price is missing from breakup, you need to:</p>
            <ol>
                <li>Go to product editor</li>
                <li>Check diamond settings are correct</li>
                <li>Click "Update" to regenerate breakup</li>
            </ol>
        </div>
    </div>
</body>
</html>
