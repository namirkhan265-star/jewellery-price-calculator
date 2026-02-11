<?php
/**
 * QUICK FIX SCRIPT: Regenerate All Product Prices
 * 
 * HOW TO USE:
 * 1. Upload this file to your WordPress root directory
 * 2. Access it via: https://yoursite.com/regenerate-all-prices.php
 * 3. It will regenerate all product prices with the new calculation
 * 
 * IMPORTANT: Delete this file after use for security!
 */

// Load WordPress
require_once('wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Access denied. You must be an administrator.');
}

// Set time limit
set_time_limit(300); // 5 minutes

?>
<!DOCTYPE html>
<html>
<head>
    <title>Regenerate All Prices</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2271b1; border-bottom: 3px solid #2271b1; padding-bottom: 10px; }
        .result { margin: 20px 0; padding: 15px; border-radius: 4px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
        .progress { background: #e9ecef; border-radius: 4px; height: 30px; margin: 20px 0; overflow: hidden; }
        .progress-bar { background: #2271b1; height: 100%; line-height: 30px; color: white; text-align: center; transition: width 0.3s; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #2271b1; color: white; }
        .button { display: inline-block; padding: 10px 20px; background: #2271b1; color: white; text-decoration: none; border-radius: 4px; margin: 10px 5px; }
        .button:hover { background: #135e96; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔄 Regenerate All Product Prices</h1>
        
        <?php
        if (!isset($_GET['confirm'])) {
            // Show confirmation page
            ?>
            <div class="result warning">
                <h3>⚠️ Before You Continue</h3>
                <p>This script will:</p>
                <ul>
                    <li>Find all products with JPC metal data</li>
                    <li>Recalculate prices using the NEW percentage calculation for Pearl/Stone/Extra costs</li>
                    <li>Update all product prices in the database</li>
                </ul>
                <p><strong>This action cannot be undone!</strong></p>
            </div>
            
            <?php
            // Count products
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => '_jpc_metal_id',
                        'compare' => 'EXISTS'
                    )
                ),
                'fields' => 'ids'
            );
            
            $product_ids = get_posts($args);
            $count = count($product_ids);
            
            echo '<div class="result info">';
            echo '<p><strong>Products to update:</strong> ' . $count . '</p>';
            echo '</div>';
            
            if ($count > 0) {
                echo '<a href="?confirm=yes" class="button">✅ Yes, Regenerate ' . $count . ' Products</a>';
                echo '<a href="' . admin_url('admin.php?page=jewellery-price-calc') . '" class="button" style="background: #6c757d;">❌ Cancel</a>';
            } else {
                echo '<div class="result warning">No products found with JPC data.</div>';
            }
            
        } else {
            // Execute regeneration
            echo '<div class="result info">🔄 Starting regeneration process...</div>';
            
            // Get all products
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => '_jpc_metal_id',
                        'compare' => 'EXISTS'
                    )
                )
            );
            
            $products = get_posts($args);
            $total = count($products);
            $success = 0;
            $failed = 0;
            $failed_products = array();
            
            echo '<div class="progress">';
            echo '<div class="progress-bar" id="progress-bar" style="width: 0%;">0%</div>';
            echo '</div>';
            
            echo '<div id="status">Processing...</div>';
            
            echo '<table>';
            echo '<tr><th>Product ID</th><th>Product Name</th><th>Status</th></tr>';
            
            foreach ($products as $index => $product) {
                $product_id = $product->ID;
                $product_name = $product->post_title;
                
                // Calculate progress
                $progress = round((($index + 1) / $total) * 100);
                
                // Update progress bar via JavaScript
                echo '<script>';
                echo 'document.getElementById("progress-bar").style.width = "' . $progress . '%";';
                echo 'document.getElementById("progress-bar").innerText = "' . $progress . '%";';
                echo 'document.getElementById("status").innerText = "Processing ' . ($index + 1) . ' of ' . $total . '...";';
                echo '</script>';
                
                // Flush output
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
                
                // Recalculate price
                $result = JPC_Price_Calculator::calculate_and_update_price($product_id);
                
                if ($result === true) {
                    $success++;
                    echo '<tr style="background: #d4edda;">';
                    echo '<td>' . $product_id . '</td>';
                    echo '<td>' . esc_html($product_name) . '</td>';
                    echo '<td>✅ Success</td>';
                    echo '</tr>';
                } else {
                    $failed++;
                    $failed_products[] = array('id' => $product_id, 'name' => $product_name);
                    echo '<tr style="background: #f8d7da;">';
                    echo '<td>' . $product_id . '</td>';
                    echo '<td>' . esc_html($product_name) . '</td>';
                    echo '<td>❌ Failed</td>';
                    echo '</tr>';
                }
                
                // Flush output after each product
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            }
            
            echo '</table>';
            
            // Summary
            echo '<div class="result ' . ($failed > 0 ? 'warning' : 'success') . '">';
            echo '<h3>📊 Summary</h3>';
            echo '<p><strong>Total Products:</strong> ' . $total . '</p>';
            echo '<p><strong>Successfully Updated:</strong> ' . $success . '</p>';
            echo '<p><strong>Failed:</strong> ' . $failed . '</p>';
            echo '</div>';
            
            if ($failed > 0) {
                echo '<div class="result error">';
                echo '<h3>❌ Failed Products</h3>';
                echo '<ul>';
                foreach ($failed_products as $fp) {
                    echo '<li>' . $fp['name'] . ' (ID: ' . $fp['id'] . ')</li>';
                }
                echo '</ul>';
                echo '</div>';
            }
            
            echo '<div class="result success">';
            echo '<h3>✅ Done!</h3>';
            echo '<p>All products have been processed. Check your products on the frontend to verify the calculations.</p>';
            echo '</div>';
            
            echo '<a href="' . admin_url('admin.php?page=jewellery-price-calc') . '" class="button">Go to Settings</a>';
            echo '<a href="' . admin_url('edit.php?post_type=product') . '" class="button">View Products</a>';
        }
        ?>
        
        <div class="result warning" style="margin-top: 30px;">
            <h3>⚠️ Security Warning</h3>
            <p><strong>IMPORTANT:</strong> Delete this file after you're done!</p>
            <p>File location: <code><?php echo __FILE__; ?></code></p>
        </div>
        
    </div>
</body>
</html>
