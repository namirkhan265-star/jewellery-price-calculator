<?php
/**
 * Regenerate All Products - Price Breakup Script
 * 
 * This script regenerates price breakup for ALL products with JPC data
 * Run this ONCE after updating to v2.5.1 to get custom labels working
 * 
 * INSTRUCTIONS:
 * 1. Upload this file to your WordPress root directory
 * 2. Access it via: https://yoursite.com/regenerate-all-products.php
 * 3. Click "Regenerate All Products" button
 * 4. Wait for completion
 * 5. DELETE this file after use for security
 */

// Load WordPress
require_once('wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    die('Access denied. You must be an administrator.');
}

// Handle regeneration request
$regeneration_result = null;
if (isset($_POST['do_regenerate']) && wp_verify_nonce($_POST['_wpnonce'], 'jpc_regenerate_all')) {
    $regeneration_result = array(
        'success' => array(),
        'failed' => array(),
        'skipped' => array(),
        'total' => 0,
    );
    
    // Get all products with JPC data
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
    $regeneration_result['total'] = count($products);
    
    foreach ($products as $product) {
        $product_id = $product->ID;
        $product_name = $product->post_title;
        
        try {
            // Regenerate price breakup
            $breakup = JPC_Price_Calculator::calculate_and_store_breakup($product_id);
            
            if ($breakup) {
                // Update WooCommerce prices
                $prices = JPC_Price_Calculator::calculate_product_prices($product_id);
                
                if ($prices) {
                    update_post_meta($product_id, '_regular_price', $prices['regular_price']);
                    update_post_meta($product_id, '_sale_price', $prices['sale_price']);
                    update_post_meta($product_id, '_price', $prices['sale_price']);
                    
                    $regeneration_result['success'][$product_id] = $product_name;
                } else {
                    $regeneration_result['failed'][$product_id] = $product_name . ' (Price calculation failed)';
                }
            } else {
                $regeneration_result['failed'][$product_id] = $product_name . ' (Breakup calculation failed)';
            }
        } catch (Exception $e) {
            $regeneration_result['failed'][$product_id] = $product_name . ' (Error: ' . $e->getMessage() . ')';
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Regenerate All Products - JPC v2.5.1</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #0073aa; padding-bottom: 10px; }
        h2 { color: #0073aa; margin-top: 30px; }
        .status { padding: 15px; margin: 15px 0; border-radius: 4px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .warning { background: #fff3cd; border: 1px solid #ffc107; color: #856404; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        .btn { display: inline-block; padding: 12px 24px; background: #0073aa; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn:hover { background: #005a87; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #0073aa; color: white; font-weight: bold; }
        tr:hover { background: #f5f5f5; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #28a745; color: white; }
        .badge-error { background: #dc3545; color: white; }
        .badge-info { background: #17a2b8; color: white; }
        ul { line-height: 1.8; }
        .progress { margin: 20px 0; }
        .progress-bar { width: 100%; height: 30px; background: #f0f0f0; border-radius: 5px; overflow: hidden; }
        .progress-fill { height: 100%; background: #0073aa; transition: width 0.3s; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔄 Regenerate All Products - v2.5.1</h1>
        <p><strong>Date:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        
        <?php if ($regeneration_result === null): ?>
            
            <div class="status info">
                <h3>📋 What This Script Will Do:</h3>
                <ul>
                    <li>✅ Find all products with JPC data</li>
                    <li>✅ Regenerate price breakup for each product</li>
                    <li>✅ Store custom labels in breakup data</li>
                    <li>✅ Update WooCommerce prices</li>
                    <li>✅ Make custom labels appear on frontend</li>
                </ul>
            </div>
            
            <div class="status warning">
                <h3>⚠️ Why You Need This:</h3>
                <p>After updating to v2.5.1, the custom labels are stored in the plugin code, but your existing products still have OLD breakup data without the custom labels.</p>
                <p>This script will regenerate the breakup data for ALL products so they include your custom labels (Test 6, Test 7, Test 8).</p>
            </div>
            
            <div class="status warning">
                <h3>⚠️ Before You Continue:</h3>
                <ul>
                    <li><strong>Backup Recommended:</strong> Take a database backup before proceeding</li>
                    <li><strong>Custom Labels Set:</strong> Make sure you've set your custom labels in Jewellery Price > General Settings</li>
                    <li><strong>Time Required:</strong> This may take a few minutes depending on number of products</li>
                </ul>
            </div>
            
            <form method="post" onsubmit="return confirm('Are you sure you want to regenerate ALL products? This will update price breakup data for all products with JPC data.');">
                <?php wp_nonce_field('jpc_regenerate_all'); ?>
                <input type="hidden" name="do_regenerate" value="1">
                <button type="submit" class="btn">🚀 Regenerate All Products</button>
            </form>
            
        <?php else: ?>
            
            <div class="status <?php echo empty($regeneration_result['failed']) ? 'success' : 'warning'; ?>">
                <h3>📊 Regeneration Summary</h3>
                <table>
                    <tr>
                        <th>Status</th>
                        <th>Count</th>
                    </tr>
                    <tr>
                        <td><strong>Total Products Found</strong></td>
                        <td><span class="badge badge-info"><?php echo $regeneration_result['total']; ?></span></td>
                    </tr>
                    <tr>
                        <td><strong>Successfully Regenerated</strong></td>
                        <td><span class="badge badge-success"><?php echo count($regeneration_result['success']); ?></span></td>
                    </tr>
                    <tr>
                        <td><strong>Failed</strong></td>
                        <td><span class="badge badge-error"><?php echo count($regeneration_result['failed']); ?></span></td>
                    </tr>
                </table>
            </div>
            
            <?php if (!empty($regeneration_result['success'])): ?>
                <div class="progress">
                    <?php 
                    $success_percentage = ($regeneration_result['total'] > 0) 
                        ? (count($regeneration_result['success']) / $regeneration_result['total']) * 100 
                        : 0;
                    ?>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $success_percentage; ?>%;">
                            <?php echo round($success_percentage); ?>% Complete
                        </div>
                    </div>
                </div>
                
                <div class="status success">
                    <h3>✅ Successfully Regenerated Products (<?php echo count($regeneration_result['success']); ?>)</h3>
                    <table>
                        <tr>
                            <th>Product ID</th>
                            <th>Product Name</th>
                        </tr>
                        <?php foreach ($regeneration_result['success'] as $product_id => $product_name): ?>
                        <tr>
                            <td><?php echo $product_id; ?></td>
                            <td><?php echo esc_html($product_name); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($regeneration_result['failed'])): ?>
                <div class="status error">
                    <h3>❌ Failed to Regenerate (<?php echo count($regeneration_result['failed']); ?>)</h3>
                    <table>
                        <tr>
                            <th>Product ID</th>
                            <th>Product Name / Error</th>
                        </tr>
                        <?php foreach ($regeneration_result['failed'] as $product_id => $error): ?>
                        <tr>
                            <td><?php echo $product_id; ?></td>
                            <td><?php echo esc_html($error); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php endif; ?>
            
            <?php if (empty($regeneration_result['failed'])): ?>
                <div class="status success">
                    <h3>🎉 Regeneration Complete!</h3>
                    <p><strong>All products have been successfully regenerated.</strong></p>
                </div>
                
                <h2>📋 Next Steps:</h2>
                
                <div class="status info">
                    <h3>Step 1: Check Frontend</h3>
                    <p>View any product on your website and check the price breakup section. You should now see your custom labels:</p>
                    <ul>
                        <li><strong>"Test 6"</strong> instead of "Pearl Cost"</li>
                        <li><strong>"Test 7"</strong> instead of "Stone Cost"</li>
                        <li><strong>"Test 8"</strong> instead of "Extra Fee"</li>
                    </ul>
                </div>
                
                <div class="status info">
                    <h3>Step 2: Clear Cache</h3>
                    <p>If labels don't appear immediately:</p>
                    <ul>
                        <li>Clear your browser cache (Ctrl+Shift+R or Cmd+Shift+R)</li>
                        <li>Clear WordPress cache (if using a caching plugin)</li>
                        <li>Clear WooCommerce transients</li>
                    </ul>
                </div>
                
                <div class="status warning">
                    <h3>Step 3: Delete This File</h3>
                    <p><strong>IMPORTANT:</strong> For security, delete this file after use:</p>
                    <ul>
                        <li>File to delete: <code>regenerate-all-products.php</code></li>
                        <li>Location: WordPress root directory</li>
                    </ul>
                </div>
                
            <?php else: ?>
                <div class="status warning">
                    <h3>⚠️ Partial Regeneration</h3>
                    <p>Some products were regenerated successfully, but others failed. Please check the errors above and fix any issues with those products.</p>
                </div>
            <?php endif; ?>
            
        <?php endif; ?>
        
        <hr style="margin: 40px 0;">
        <p style="color: #999; font-size: 12px;">
            <strong>Security Note:</strong> Please delete this file (regenerate-all-products.php) after use.<br>
            <strong>Support:</strong> If you encounter any issues, check the WordPress error log or contact support.
        </p>
    </div>
</body>
</html>
