<?php
/**
 * Regenerate Products with Manual Diamond Support
 * 
 * This will regenerate all products using the updated calculator
 * that now supports manual diamond entry.
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
    <title>Regenerate with Manual Diamonds</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2271b1; border-bottom: 3px solid #2271b1; padding-bottom: 10px; }
        .success { background: #d4edda; border: 2px solid #28a745; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .info { background: #d1ecf1; border: 2px solid #17a2b8; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .button { background: #2271b1; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; text-decoration: none; display: inline-block; }
        .button:hover { background: #135e96; }
        .progress { background: #e9ecef; border-radius: 5px; height: 30px; margin: 20px 0; overflow: hidden; }
        .progress-bar { background: #28a745; height: 100%; line-height: 30px; color: white; text-align: center; transition: width 0.3s; }
        ul { line-height: 1.8; max-height: 400px; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Regenerate Products with Manual Diamond Support</h1>
        
        <?php
        if (isset($_GET['run'])) {
            echo '<div class="info"><strong>Regenerating products...</strong></div>';
            
            global $wpdb;
            
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
            
            echo '<div class="progress"><div class="progress-bar" id="progressBar" style="width: 0%;">0%</div></div>';
            echo '<p>Processing ' . $total . ' products...</p>';
            echo '<ul>';
            
            foreach ($products as $product) {
                $post_id = $product->ID;
                $product_name = get_the_title($post_id);
                
                // Check for manual diamond fields
                $manual_carat = get_post_meta($post_id, '_jpc_manual_diamond_carat', true);
                $manual_quantity = get_post_meta($post_id, '_jpc_manual_diamond_quantity', true);
                $manual_price = get_post_meta($post_id, '_jpc_manual_diamond_price_per_carat', true);
                
                if (class_exists('JPC_Price_Calculator')) {
                    JPC_Price_Calculator::calculate_and_store_breakup($post_id);
                    
                    $breakup = get_post_meta($post_id, '_jpc_price_breakup', true);
                    if ($breakup && is_array($breakup)) {
                        $fixed++;
                        $has_diamond = isset($breakup['diamond_price']) && $breakup['diamond_price'] > 0;
                        
                        echo '<li>✅ ' . esc_html($product_name);
                        if ($has_diamond) {
                            echo ' <strong>(Diamond: ₹' . number_format($breakup['diamond_price'], 2) . ')</strong>';
                        }
                        if ($manual_carat && $manual_quantity && $manual_price) {
                            echo ' <em>(Manual: ' . $manual_carat . 'ct × ' . $manual_quantity . ' × ₹' . $manual_price . ')</em>';
                        }
                        echo '</li>';
                    }
                }
                
                $progress = round(($fixed / $total) * 100);
                echo '<script>
                    document.getElementById("progressBar").style.width = "' . $progress . '%";
                    document.getElementById("progressBar").textContent = "' . $progress . '%";
                </script>';
                flush();
            }
            
            echo '</ul>';
            
            echo '<div class="success">';
            echo '<h2>✅ COMPLETE!</h2>';
            echo '<p><strong>' . $fixed . ' products regenerated!</strong></p>';
            echo '<h3>Next Steps:</h3>';
            echo '<ol>';
            echo '<li>Clear browser cache (Ctrl+F5)</li>';
            echo '<li>Go to any product with manual diamonds</li>';
            echo '<li>Click "Price Breakup" tab</li>';
            echo '<li>You should now see:</li>';
            echo '<ul>';
            echo '<li><strong>Diamond price</strong> (if product has diamonds)</li>';
            echo '<li><strong>GST (3%)</strong> with percentage</li>';
            echo '</ul>';
            echo '<li>Delete this file from server</li>';
            echo '</ol>';
            echo '</div>';
            
        } else {
            echo '<div class="info">';
            echo '<h2>📋 What This Will Do</h2>';
            echo '<p>The calculator has been updated to support manual diamond entry.</p>';
            echo '<p>This script will regenerate all products to calculate manual diamonds.</p>';
            echo '</div>';
            
            echo '<div style="text-align: center; margin: 30px 0;">';
            echo '<a href="?run=1" class="button">🚀 Regenerate Products Now</a>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>
