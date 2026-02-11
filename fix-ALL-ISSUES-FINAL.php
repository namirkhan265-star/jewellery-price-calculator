<?php
/**
 * FINAL COMPREHENSIVE FIX - All Issues
 * 
 * FIXES:
 * 1. GST label and percentage display
 * 2. Missing diamond prices in breakup
 * 3. Regenerates all price breakups correctly
 * 
 * INSTRUCTIONS:
 * 1. Upload to: /wp-content/plugins/jewellery-price-calculator-main/
 * 2. Visit: https://detailx.co.in/wp-content/plugins/jewellery-price-calculator-main/fix-ALL-ISSUES-FINAL.php
 * 3. Click button
 * 4. Wait for completion
 * 5. Delete this file
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
    <title>Final Comprehensive Fix</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2271b1; border-bottom: 3px solid #2271b1; padding-bottom: 10px; }
        .success { background: #d4edda; border: 2px solid #28a745; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .info { background: #d1ecf1; border: 2px solid #17a2b8; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .warning { background: #fff3cd; border: 2px solid #ffc107; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .error { background: #f8d7da; border: 2px solid #dc3545; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .button { background: #2271b1; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; text-decoration: none; display: inline-block; }
        .button:hover { background: #135e96; }
        .progress { background: #e9ecef; border-radius: 5px; height: 30px; margin: 20px 0; overflow: hidden; }
        .progress-bar { background: #28a745; height: 100%; line-height: 30px; color: white; text-align: center; transition: width 0.3s; }
        ul { line-height: 1.8; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .log { max-height: 400px; overflow-y: auto; background: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Final Comprehensive Fix - All Issues</h1>
        
        <?php
        if (isset($_GET['run'])) {
            // RUN THE FIX
            echo '<div class="info"><strong>Starting comprehensive fix...</strong></div>';
            
            global $wpdb;
            
            // Get all products with JPC data
            $products = $wpdb->get_results("
                SELECT DISTINCT p.ID 
                FROM {$wpdb->posts} p
                INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
                WHERE p.post_type = 'product'
                AND p.post_status = 'publish'
                AND pm.meta_key = '_jpc_metal_id'
                AND pm.meta_value != ''
            ");
            
            $total = count($products);
            $fixed = 0;
            $errors = array();
            
            echo '<div class="progress"><div class="progress-bar" id="progressBar" style="width: 0%;">0%</div></div>';
            echo '<div id="status">Processing ' . $total . ' products...</div>';
            echo '<div class="log"><ul id="log">';
            
            foreach ($products as $product) {
                $post_id = $product->ID;
                $product_name = get_the_title($post_id);
                
                try {
                    // Regenerate price breakup using the calculator
                    if (class_exists('JPC_Price_Calculator')) {
                        JPC_Price_Calculator::calculate_and_store_breakup($post_id);
                        
                        // Verify breakup was created
                        $breakup = get_post_meta($post_id, '_jpc_price_breakup', true);
                        
                        if ($breakup && is_array($breakup)) {
                            $fixed++;
                            
                            $details = array();
                            if (isset($breakup['metal_price']) && $breakup['metal_price'] > 0) {
                                $details[] = 'Metal: ₹' . number_format($breakup['metal_price'], 2);
                            }
                            if (isset($breakup['diamond_price']) && $breakup['diamond_price'] > 0) {
                                $details[] = 'Diamond: ₹' . number_format($breakup['diamond_price'], 2);
                            }
                            if (isset($breakup['gst']) && $breakup['gst'] > 0) {
                                $gst_pct = isset($breakup['gst_percentage']) ? $breakup['gst_percentage'] : 0;
                                $details[] = 'GST: ₹' . number_format($breakup['gst'], 2) . ' (' . $gst_pct . '%)';
                            }
                            
                            echo '<li>✅ <strong>' . esc_html($product_name) . '</strong> (ID: ' . $post_id . ')';
                            if (!empty($details)) {
                                echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;' . implode(' | ', $details);
                            }
                            echo '</li>';
                        } else {
                            $errors[] = 'Product ID ' . $post_id . ': Breakup not generated';
                            echo '<li>⚠️ <strong>' . esc_html($product_name) . '</strong> - Breakup not generated</li>';
                        }
                    } else {
                        $errors[] = 'JPC_Price_Calculator class not found';
                        echo '<li>❌ Calculator class not found!</li>';
                        break;
                    }
                    
                } catch (Exception $e) {
                    $errors[] = 'Product ID ' . $post_id . ': ' . $e->getMessage();
                    echo '<li>❌ <strong>' . esc_html($product_name) . '</strong> - ' . esc_html($e->getMessage()) . '</li>';
                }
                
                // Update progress
                $progress = round(($fixed / $total) * 100);
                echo '<script>
                    document.getElementById("progressBar").style.width = "' . $progress . '%";
                    document.getElementById("progressBar").textContent = "' . $progress . '%";
                    var log = document.getElementById("log");
                    log.parentElement.scrollTop = log.parentElement.scrollHeight;
                </script>';
                flush();
            }
            
            echo '</ul></div>';
            
            if (empty($errors)) {
                echo '<div class="success">';
                echo '<h2>✅ SUCCESS!</h2>';
                echo '<p><strong>' . $fixed . ' products regenerated successfully!</strong></p>';
                echo '<h3>What Was Fixed:</h3>';
                echo '<ul>';
                echo '<li>✅ GST label and percentage now display correctly</li>';
                echo '<li>✅ Diamond prices restored in price breakup</li>';
                echo '<li>✅ All calculations regenerated with latest settings</li>';
                echo '</ul>';
                echo '<h3>Next Steps:</h3>';
                echo '<ol>';
                echo '<li>Go to any product on your website</li>';
                echo '<li>Click the "Price Breakup" tab</li>';
                echo '<li>You should now see:</li>';
                echo '<ul>';
                echo '<li>Diamond price (if product has diamonds)</li>';
                echo '<li>GST with percentage: <code>GST (3%)</code></li>';
                echo '<li>All other fields with correct values</li>';
                echo '</ul>';
                echo '<li><strong>IMPORTANT:</strong> Delete this file from your server</li>';
                echo '</ol>';
                echo '</div>';
            } else {
                echo '<div class="warning">';
                echo '<h2>⚠️ Completed with Some Issues</h2>';
                echo '<p><strong>' . $fixed . ' products fixed successfully</strong></p>';
                echo '<p>But ' . count($errors) . ' had issues:</p>';
                echo '<ul>';
                foreach (array_slice($errors, 0, 10) as $error) {
                    echo '<li>' . esc_html($error) . '</li>';
                }
                if (count($errors) > 10) {
                    echo '<li>... and ' . (count($errors) - 10) . ' more</li>';
                }
                echo '</ul>';
                echo '</div>';
            }
            
        } else {
            // SHOW INFORMATION
            global $wpdb;
            
            $count = $wpdb->get_var("
                SELECT COUNT(DISTINCT p.ID) 
                FROM {$wpdb->posts} p
                INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
                WHERE p.post_type = 'product'
                AND p.post_status = 'publish'
                AND pm.meta_key = '_jpc_metal_id'
                AND pm.meta_value != ''
            ");
            
            echo '<div class="info">';
            echo '<h2>📊 Products to Fix</h2>';
            echo '<p><strong>' . $count . ' products</strong> will be regenerated.</p>';
            echo '</div>';
            
            echo '<div class="warning">';
            echo '<h3>⚠️ What This Will Do</h3>';
            echo '<ul>';
            echo '<li><strong>Regenerate ALL price breakups</strong> from scratch</li>';
            echo '<li><strong>Fix GST display</strong> - will show label and percentage</li>';
            echo '<li><strong>Restore diamond prices</strong> in breakup</li>';
            echo '<li><strong>Update all calculations</strong> with latest settings</li>';
            echo '<li>Takes approximately <strong>' . ceil($count / 5) . ' seconds</strong></li>';
            echo '</ul>';
            echo '</div>';
            
            echo '<div class="info">';
            echo '<h3>✅ Safe to Run</h3>';
            echo '<ul>';
            echo '<li>Does NOT change product prices</li>';
            echo '<li>Only regenerates price breakup data</li>';
            echo '<li>Can be run multiple times</li>';
            echo '<li>Uses your current plugin settings</li>';
            echo '</ul>';
            echo '</div>';
            
            echo '<div style="text-align: center; margin: 30px 0;">';
            echo '<a href="?run=1" class="button">🚀 Fix All Issues Now</a>';
            echo '</div>';
        }
        ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 14px;">
            <p><strong>After Running This Fix:</strong></p>
            <ul>
                <li>Clear your website cache (if using caching plugin)</li>
                <li>Check a few products to verify everything looks correct</li>
                <li>Delete this file for security</li>
            </ul>
        </div>
    </div>
</body>
</html>
