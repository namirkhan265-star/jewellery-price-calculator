<?php
/**
 * SIMPLE ONE-CLICK FIX for GST Label and Percentage Display
 * 
 * INSTRUCTIONS:
 * 1. Upload this file to: /wp-content/plugins/jewellery-price-calculator-main/
 * 2. Visit: https://yoursite.com/wp-content/plugins/jewellery-price-calculator-main/fix-gst-label-SIMPLE.php
 * 3. Click the button
 * 4. Delete this file after success
 * 
 * FIXES:
 * - GST label not showing custom name
 * - GST percentage not displaying
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
    <title>Fix GST Label & Percentage</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2271b1; border-bottom: 3px solid #2271b1; padding-bottom: 10px; }
        .success { background: #d4edda; border: 2px solid #28a745; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .info { background: #d1ecf1; border: 2px solid #17a2b8; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .warning { background: #fff3cd; border: 2px solid #ffc107; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .button { background: #2271b1; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; text-decoration: none; display: inline-block; }
        .button:hover { background: #135e96; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        ul { line-height: 1.8; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Fix GST Label & Percentage Display</h1>
        
        <?php
        if (isset($_GET['run'])) {
            // RUN THE FIX
            echo '<div class="info"><strong>Fixing GST display...</strong></div>';
            
            global $wpdb;
            
            // Get all products with price breakup
            $products = $wpdb->get_results("
                SELECT post_id 
                FROM {$wpdb->postmeta} 
                WHERE meta_key = '_jpc_price_breakup'
            ");
            
            $total = count($products);
            $fixed = 0;
            
            // Get current GST settings
            $gst_label = get_option('jpc_gst_label', 'GST');
            $gst_value = floatval(get_option('jpc_gst_value', 3));
            
            echo '<div class="info">';
            echo '<p><strong>Current GST Settings:</strong></p>';
            echo '<ul>';
            echo '<li>Label: <code>' . esc_html($gst_label) . '</code></li>';
            echo '<li>Percentage: <code>' . $gst_value . '%</code></li>';
            echo '</ul>';
            echo '</div>';
            
            echo '<p>Updating ' . $total . ' products...</p>';
            echo '<ul>';
            
            foreach ($products as $product) {
                $post_id = $product->post_id;
                $product_name = get_the_title($post_id);
                
                // Get existing breakup
                $breakup = get_post_meta($post_id, '_jpc_price_breakup', true);
                
                if ($breakup && is_array($breakup)) {
                    // Update GST label and percentage in breakup
                    $breakup['gst_label'] = $gst_label;
                    $breakup['gst_percentage'] = $gst_value;
                    
                    // Save updated breakup
                    update_post_meta($post_id, '_jpc_price_breakup', $breakup);
                    
                    $fixed++;
                    echo '<li>✅ Updated: <strong>' . esc_html($product_name) . '</strong> (ID: ' . $post_id . ')</li>';
                }
            }
            
            echo '</ul>';
            
            echo '<div class="success">';
            echo '<h2>✅ SUCCESS!</h2>';
            echo '<p><strong>' . $fixed . ' products updated successfully!</strong></p>';
            echo '<p>GST label and percentage will now display correctly in price breakup.</p>';
            echo '<h3>Next Steps:</h3>';
            echo '<ol>';
            echo '<li>Go to any product on your website</li>';
            echo '<li>Click the "Price Breakup" tab</li>';
            echo '<li>GST should now show: <code>' . esc_html($gst_label) . ' (' . $gst_value . '%)</code></li>';
            echo '<li><strong>IMPORTANT:</strong> Delete this file from your server</li>';
            echo '</ol>';
            echo '</div>';
            
        } else {
            // SHOW INFORMATION
            $gst_label = get_option('jpc_gst_label', 'GST');
            $gst_value = get_option('jpc_gst_value', 3);
            
            echo '<div class="info">';
            echo '<h2>📊 Current GST Settings</h2>';
            echo '<ul>';
            echo '<li><strong>Label:</strong> ' . esc_html($gst_label) . '</li>';
            echo '<li><strong>Percentage:</strong> ' . $gst_value . '%</li>';
            echo '</ul>';
            echo '<p>These values will be added to all product price breakups.</p>';
            echo '</div>';
            
            echo '<div class="warning">';
            echo '<h3>⚠️ Before You Start</h3>';
            echo '<ul>';
            echo '<li>This will update ALL products with price breakups</li>';
            echo '<li>Safe to run multiple times</li>';
            echo '<li>Takes about 5 seconds</li>';
            echo '</ul>';
            echo '</div>';
            
            echo '<div style="text-align: center; margin: 30px 0;">';
            echo '<a href="?run=1" class="button">🚀 Fix GST Display Now</a>';
            echo '</div>';
            
            echo '<div class="info">';
            echo '<h3>What This Will Do:</h3>';
            echo '<ol>';
            echo '<li>Read your GST settings from WordPress options</li>';
            echo '<li>Update all product price breakups with correct GST label and percentage</li>';
            echo '<li>GST will display as: <code>' . esc_html($gst_label) . ' (' . $gst_value . '%)</code></li>';
            echo '</ol>';
            echo '</div>';
        }
        ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 14px;">
            <p><strong>Need to Change GST Settings?</strong></p>
            <p>Go to: <strong>Jewellery Price Calculator → General Settings → GST Section</strong></p>
            <p>After changing settings, run this fix again to update all products.</p>
        </div>
    </div>
</body>
</html>
