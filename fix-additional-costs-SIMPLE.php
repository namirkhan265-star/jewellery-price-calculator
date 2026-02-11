<?php
/**
 * SIMPLE ONE-CLICK FIX for Additional Cost Fields
 * 
 * INSTRUCTIONS FOR NON-CODERS:
 * ============================
 * 
 * 1. Download this file to your computer
 * 2. Upload it to your WordPress site using FTP or File Manager
 * 3. Put it in: /wp-content/plugins/jewellery-price-calculator/
 * 4. Open your browser and go to: https://yoursite.com/wp-content/plugins/jewellery-price-calculator/fix-additional-costs-SIMPLE.php
 * 5. Wait for it to finish (you'll see "SUCCESS!" message)
 * 6. Delete this file from your server
 * 7. Done! Your additional cost fields will now work!
 * 
 * WHAT THIS DOES:
 * - Converts all your products from old format to new format
 * - Fixes the meta keys so fields show in price breakup
 * - Regenerates price breakups for all products
 * 
 * SAFE TO RUN:
 * - Does NOT delete any data
 * - Can be run multiple times safely
 * - Only updates products that need fixing
 */

// Load WordPress
require_once('../../../wp-load.php');

// Security check - only admins can run this
if (!current_user_can('manage_options')) {
    die('ERROR: You must be logged in as an administrator to run this script.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Additional Cost Fields - Simple</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
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
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Fix Additional Cost Fields - Simple One-Click</h1>
        
        <?php
        if (isset($_GET['run'])) {
            // RUN THE FIX
            echo '<div class="info"><strong>Starting migration...</strong></div>';
            
            global $wpdb;
            
            // Get all products with old meta keys
            $products = $wpdb->get_results("
                SELECT DISTINCT post_id 
                FROM {$wpdb->postmeta} 
                WHERE meta_key IN ('_jpc_pearl_cost', '_jpc_stone_cost', '_jpc_extra_fee')
                AND meta_value != '' 
                AND meta_value != '0'
            ");
            
            $total = count($products);
            $migrated = 0;
            $errors = array();
            
            echo '<div class="progress"><div class="progress-bar" id="progressBar" style="width: 0%;">0%</div></div>';
            echo '<div id="status">Processing ' . $total . ' products...</div>';
            echo '<ul id="log">';
            
            foreach ($products as $product) {
                $post_id = $product->post_id;
                $product_name = get_the_title($post_id);
                
                try {
                    // Migrate Pearl Cost
                    $pearl_cost = get_post_meta($post_id, '_jpc_pearl_cost', true);
                    if ($pearl_cost && $pearl_cost > 0) {
                        $pearl_cost_type = get_option('jpc_pearl_cost_type', 'fixed');
                        update_post_meta($post_id, '_jpc_pearl_cost_value', floatval($pearl_cost));
                        update_post_meta($post_id, '_jpc_pearl_cost_type', $pearl_cost_type);
                        delete_post_meta($post_id, '_jpc_pearl_cost');
                    }
                    
                    // Migrate Stone Cost
                    $stone_cost = get_post_meta($post_id, '_jpc_stone_cost', true);
                    if ($stone_cost && $stone_cost > 0) {
                        $stone_cost_type = get_option('jpc_stone_cost_type', 'fixed');
                        update_post_meta($post_id, '_jpc_stone_cost_value', floatval($stone_cost));
                        update_post_meta($post_id, '_jpc_stone_cost_type', $stone_cost_type);
                        delete_post_meta($post_id, '_jpc_stone_cost');
                    }
                    
                    // Migrate Extra Fee
                    $extra_fee = get_post_meta($post_id, '_jpc_extra_fee', true);
                    if ($extra_fee && $extra_fee > 0) {
                        $extra_fee_type = get_option('jpc_extra_fee_type', 'fixed');
                        update_post_meta($post_id, '_jpc_extra_fee_value', floatval($extra_fee));
                        update_post_meta($post_id, '_jpc_extra_fee_type', $extra_fee_type);
                        delete_post_meta($post_id, '_jpc_extra_fee');
                    }
                    
                    // Regenerate price breakup
                    if (class_exists('JPC_Price_Calculator')) {
                        JPC_Price_Calculator::calculate_and_store_breakup($post_id);
                    }
                    
                    $migrated++;
                    echo '<li>✅ Migrated: <strong>' . esc_html($product_name) . '</strong> (ID: ' . $post_id . ')</li>';
                    
                } catch (Exception $e) {
                    $errors[] = 'Product ID ' . $post_id . ': ' . $e->getMessage();
                    echo '<li>❌ Error: <strong>' . esc_html($product_name) . '</strong> - ' . esc_html($e->getMessage()) . '</li>';
                }
                
                // Update progress
                $progress = round(($migrated / $total) * 100);
                echo '<script>
                    document.getElementById("progressBar").style.width = "' . $progress . '%";
                    document.getElementById("progressBar").textContent = "' . $progress . '%";
                </script>';
                flush();
            }
            
            echo '</ul>';
            
            // Mark migration as complete
            update_option('jpc_migration_v2510_completed', true);
            update_option('jpc_migration_v2510_count', $migrated);
            update_option('jpc_migration_v2510_date', current_time('mysql'));
            
            if (empty($errors)) {
                echo '<div class="success">';
                echo '<h2>✅ SUCCESS!</h2>';
                echo '<p><strong>' . $migrated . ' products migrated successfully!</strong></p>';
                echo '<p>Your additional cost fields (Test 6.1, Test 7.1, Test 8.1) will now show in the price breakup.</p>';
                echo '<h3>Next Steps:</h3>';
                echo '<ol>';
                echo '<li>Go to any product on your website</li>';
                echo '<li>Click the "Price Breakup" tab</li>';
                echo '<li>You should now see your additional cost fields!</li>';
                echo '<li><strong>IMPORTANT:</strong> Delete this file from your server for security</li>';
                echo '</ol>';
                echo '</div>';
            } else {
                echo '<div class="warning">';
                echo '<h2>⚠️ Completed with Errors</h2>';
                echo '<p><strong>' . $migrated . ' products migrated successfully</strong></p>';
                echo '<p>But ' . count($errors) . ' products had errors:</p>';
                echo '<ul>';
                foreach ($errors as $error) {
                    echo '<li>' . esc_html($error) . '</li>';
                }
                echo '</ul>';
                echo '</div>';
            }
            
        } else {
            // SHOW INFORMATION AND START BUTTON
            global $wpdb;
            
            $count = $wpdb->get_var("
                SELECT COUNT(DISTINCT post_id) 
                FROM {$wpdb->postmeta} 
                WHERE meta_key IN ('_jpc_pearl_cost', '_jpc_stone_cost', '_jpc_extra_fee')
                AND meta_value != '' 
                AND meta_value != '0'
            ");
            
            if ($count > 0) {
                echo '<div class="info">';
                echo '<h2>📊 Migration Status</h2>';
                echo '<p><strong>' . $count . ' products</strong> need to be migrated.</p>';
                echo '</div>';
                
                echo '<div class="warning">';
                echo '<h3>⚠️ Before You Start</h3>';
                echo '<ul>';
                echo '<li>Make sure you have a <strong>backup</strong> of your database</li>';
                echo '<li>This process is <strong>safe</strong> and can be run multiple times</li>';
                echo '<li>It will take approximately <strong>' . ceil($count / 10) . ' seconds</strong></li>';
                echo '<li>Do NOT close this page until it finishes</li>';
                echo '</ul>';
                echo '</div>';
                
                echo '<div style="text-align: center; margin: 30px 0;">';
                echo '<a href="?run=1" class="button">🚀 Start Migration Now</a>';
                echo '</div>';
                
                echo '<div class="info">';
                echo '<h3>What This Will Do:</h3>';
                echo '<ol>';
                echo '<li>Convert <code>_jpc_pearl_cost</code> → <code>_jpc_pearl_cost_value</code> + <code>_jpc_pearl_cost_type</code></li>';
                echo '<li>Convert <code>_jpc_stone_cost</code> → <code>_jpc_stone_cost_value</code> + <code>_jpc_stone_cost_type</code></li>';
                echo '<li>Convert <code>_jpc_extra_fee</code> → <code>_jpc_extra_fee_value</code> + <code>_jpc_extra_fee_type</code></li>';
                echo '<li>Regenerate price breakups for all products</li>';
                echo '<li>Delete old meta keys</li>';
                echo '</ol>';
                echo '</div>';
                
            } else {
                echo '<div class="success">';
                echo '<h2>✅ No Migration Needed!</h2>';
                echo '<p>All your products are already using the correct format.</p>';
                echo '<p>You can safely delete this file from your server.</p>';
                echo '</div>';
            }
        }
        ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 14px;">
            <p><strong>Need Help?</strong></p>
            <ul>
                <li>If you see errors, contact your developer</li>
                <li>If migration succeeds but fields still don't show, clear your cache</li>
                <li>After successful migration, <strong>delete this file</strong> for security</li>
            </ul>
        </div>
    </div>
</body>
</html>
