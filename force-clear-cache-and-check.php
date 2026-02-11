<?php
/**
 * Force Clear Cache and Check Template Location
 * 
 * This will:
 * 1. Clear all WordPress caches
 * 2. Show which template file is actually being used
 * 3. Show the actual GST settings
 * 4. Force regenerate one product to test
 */

// Load WordPress
require_once('../../../wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    die('ERROR: You must be logged in as an administrator.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Force Clear Cache & Debug</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 5px; max-width: 1200px; margin: 0 auto; }
        h1 { color: #2271b1; }
        .section { margin: 20px 0; padding: 15px; background: #f9f9f9; border-left: 4px solid #2271b1; }
        .success { background: #d4edda; border-left-color: #28a745; }
        .error { background: #f8d7da; border-left-color: #dc3545; }
        .info { background: #d1ecf1; border-left-color: #17a2b8; }
        pre { background: #282c34; color: #abb2bf; padding: 15px; border-radius: 5px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        table td { padding: 8px; border: 1px solid #ddd; }
        table td:first-child { background: #f0f0f0; font-weight: bold; width: 30%; }
        .button { background: #2271b1; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Force Clear Cache & Debug Template</h1>
        
        <?php
        // Clear all caches
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
            echo '<div class="section success">✅ WordPress object cache cleared</div>';
        }
        
        // Clear transients
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'");
        echo '<div class="section success">✅ All transients cleared</div>';
        
        // Check template location
        echo '<div class="section info">';
        echo '<h2>📁 Template File Locations</h2>';
        
        $plugin_template = WP_PLUGIN_DIR . '/jewellery-price-calculator-main/templates/frontend/price-breakup.php';
        $theme_template = get_stylesheet_directory() . '/jewellery-price-calculator/price-breakup.php';
        
        echo '<table>';
        echo '<tr><td>Plugin Template</td><td>' . $plugin_template . '</td></tr>';
        echo '<tr><td>Exists?</td><td>' . (file_exists($plugin_template) ? '✅ YES' : '❌ NO') . '</td></tr>';
        if (file_exists($plugin_template)) {
            echo '<tr><td>Last Modified</td><td>' . date('Y-m-d H:i:s', filemtime($plugin_template)) . '</td></tr>';
            echo '<tr><td>File Size</td><td>' . filesize($plugin_template) . ' bytes</td></tr>';
        }
        echo '<tr><td>Theme Override</td><td>' . $theme_template . '</td></tr>';
        echo '<tr><td>Exists?</td><td>' . (file_exists($theme_template) ? '⚠️ YES (This will be used instead!)' : '✅ NO') . '</td></tr>';
        echo '</table>';
        echo '</div>';
        
        // Check GST settings
        echo '<div class="section info">';
        echo '<h2>⚙️ Current GST Settings</h2>';
        echo '<table>';
        echo '<tr><td>Enable GST</td><td>' . get_option('jpc_enable_gst', 'yes') . '</td></tr>';
        echo '<tr><td>GST Label</td><td>' . get_option('jpc_gst_label', 'GST') . '</td></tr>';
        echo '<tr><td>GST Value</td><td>' . get_option('jpc_gst_value', '3') . '%</td></tr>';
        echo '<tr><td>GST Calculation Base</td><td>' . get_option('jpc_gst_calculation_base', 'after_discount') . '</td></tr>';
        echo '</table>';
        echo '</div>';
        
        // Get a test product
        $test_product = get_posts(array(
            'post_type' => 'product',
            'posts_per_page' => 1,
            'meta_query' => array(
                array(
                    'key' => '_jpc_metal_id',
                    'compare' => 'EXISTS'
                )
            )
        ));
        
        if ($test_product) {
            $product_id = $test_product[0]->ID;
            $product_name = get_the_title($product_id);
            
            echo '<div class="section info">';
            echo '<h2>🧪 Test Product: ' . esc_html($product_name) . ' (ID: ' . $product_id . ')</h2>';
            
            $breakup = get_post_meta($product_id, '_jpc_price_breakup', true);
            
            if ($breakup && is_array($breakup)) {
                echo '<h3>Stored Breakup Data:</h3>';
                echo '<table>';
                foreach ($breakup as $key => $value) {
                    if (!is_array($value)) {
                        echo '<tr><td>' . esc_html($key) . '</td><td>' . esc_html($value) . '</td></tr>';
                    }
                }
                echo '</table>';
                
                echo '<h3>What Should Display:</h3>';
                echo '<ul>';
                if (isset($breakup['metal_price']) && $breakup['metal_price'] > 0) {
                    echo '<li>✅ Gold: ₹' . number_format($breakup['metal_price'], 2) . '</li>';
                }
                if (isset($breakup['diamond_price']) && $breakup['diamond_price'] > 0) {
                    echo '<li>✅ Diamond: ₹' . number_format($breakup['diamond_price'], 2) . '</li>';
                } else {
                    echo '<li>❌ Diamond: Not in breakup data</li>';
                }
                if (isset($breakup['gst']) && $breakup['gst'] > 0) {
                    $gst_pct = get_option('jpc_gst_value', 3);
                    $gst_label = get_option('jpc_gst_label', 'GST');
                    echo '<li>✅ ' . $gst_label . ' (' . $gst_pct . '%): ₹' . number_format($breakup['gst'], 2) . '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p style="color: red;">❌ No breakup data found for this product!</p>';
            }
            
            echo '<p><a href="' . get_permalink($product_id) . '" target="_blank" class="button">View Product on Frontend</a></p>';
            echo '</div>';
        }
        
        // Check if template has the fix
        if (file_exists($plugin_template)) {
            $template_content = file_get_contents($plugin_template);
            
            echo '<div class="section">';
            echo '<h2>🔍 Template Code Check</h2>';
            
            $has_dynamic_gst = strpos($template_content, 'get_option(\'jpc_gst_label\'') !== false;
            $has_diamond_fix = strpos($template_content, 'isset($breakup[\'diamond_price\'])') !== false;
            
            echo '<table>';
            echo '<tr><td>Has Dynamic GST Label</td><td>' . ($has_dynamic_gst ? '✅ YES' : '❌ NO') . '</td></tr>';
            echo '<tr><td>Has Diamond Price Check</td><td>' . ($has_diamond_fix ? '✅ YES' : '❌ NO') . '</td></tr>';
            echo '</table>';
            
            if (!$has_dynamic_gst || !$has_diamond_fix) {
                echo '<div class="section error">';
                echo '<h3>❌ Template Not Updated!</h3>';
                echo '<p>The template file exists but doesn\'t have the fixes.</p>';
                echo '<p>The apply-template-fix.php script may not have worked correctly.</p>';
                echo '</div>';
            } else {
                echo '<div class="section success">';
                echo '<h3>✅ Template Has Fixes!</h3>';
                echo '<p>The template code looks correct.</p>';
                echo '</div>';
            }
            echo '</div>';
        }
        
        // Show first 50 lines of template
        if (file_exists($plugin_template)) {
            echo '<div class="section">';
            echo '<h2>📄 Template File Content (First 100 lines)</h2>';
            $lines = file($plugin_template);
            echo '<pre>';
            echo esc_html(implode('', array_slice($lines, 0, 100)));
            echo '</pre>';
            echo '</div>';
        }
        ?>
        
        <div class="section info">
            <h2>🔧 Next Steps</h2>
            <ol>
                <li>Check if "Template Has Fixes" shows ✅ YES for both items</li>
                <li>If NO, the template wasn't updated correctly</li>
                <li>If YES, check if there's a theme override (shows ⚠️ above)</li>
                <li>Clear your browser cache (Ctrl+F5)</li>
                <li>Clear your website cache plugin (if you have one)</li>
                <li>Visit the test product link above</li>
            </ol>
        </div>
    </div>
</body>
</html>
