<?php
/**
 * Diagnose Failed Products
 * 
 * This script checks why products failed to regenerate
 * and provides detailed information about missing data
 * 
 * INSTRUCTIONS:
 * 1. Upload this file to your WordPress root directory
 * 2. Access it via: https://yoursite.com/diagnose-failed-products.php
 * 3. Review the diagnostic information
 * 4. DELETE this file after use for security
 */

// Load WordPress
require_once('wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    die('Access denied. You must be an administrator.');
}

// List of failed product IDs from your screenshot
$failed_product_ids = array(
    2542, 2543, 2544, 2545, 2546, 2547, 2548, 2549, 2550, 2551,
    2552, 2553, 2554, 2555, 2556, 2557, 2558, 2559, 2560, 2561
);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Diagnose Failed Products</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #0073aa; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #0073aa; color: white; font-weight: bold; }
        tr:hover { background: #f5f5f5; }
        .status-ok { color: #28a745; font-weight: bold; }
        .status-missing { color: #dc3545; font-weight: bold; }
        .status-warning { color: #ffc107; font-weight: bold; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #28a745; color: white; }
        .badge-error { background: #dc3545; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
        .info-box { padding: 15px; margin: 15px 0; border-radius: 4px; background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnostic Report - Failed Products</h1>
        <p><strong>Date:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        
        <div class="info-box">
            <p><strong>Checking <?php echo count($failed_product_ids); ?> failed products...</strong></p>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Metal ID</th>
                    <th>Weight</th>
                    <th>Metal Exists</th>
                    <th>Metal Group</th>
                    <th>Issue</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($failed_product_ids as $product_id): ?>
                    <?php
                    $product = get_post($product_id);
                    $product_name = $product ? $product->post_title : 'Product not found';
                    
                    // Get JPC data
                    $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
                    $weight = get_post_meta($product_id, '_jpc_metal_weight', true);
                    
                    // Check if metal exists
                    $metal = null;
                    $metal_group = null;
                    $metal_exists = false;
                    $issue = array();
                    
                    if ($metal_id) {
                        $metal = JPC_Metals::get_by_id($metal_id);
                        if ($metal) {
                            $metal_exists = true;
                            $metal_group = JPC_Metal_Groups::get_by_id($metal->metal_group_id);
                        } else {
                            $issue[] = 'Metal ID ' . $metal_id . ' not found in database';
                        }
                    } else {
                        $issue[] = 'No metal ID assigned';
                    }
                    
                    if (empty($weight) || $weight <= 0) {
                        $issue[] = 'Weight is missing or zero';
                    }
                    
                    if (!$metal_group) {
                        $issue[] = 'Metal group not found';
                    }
                    
                    $issue_text = !empty($issue) ? implode(', ', $issue) : 'Unknown';
                    ?>
                    <tr>
                        <td><?php echo $product_id; ?></td>
                        <td><?php echo esc_html($product_name); ?></td>
                        <td>
                            <?php if ($metal_id): ?>
                                <span class="status-ok"><?php echo $metal_id; ?></span>
                            <?php else: ?>
                                <span class="status-missing">Missing</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($weight && $weight > 0): ?>
                                <span class="status-ok"><?php echo number_format($weight, 3); ?> g</span>
                            <?php else: ?>
                                <span class="status-missing">Missing</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($metal_exists): ?>
                                <span class="badge badge-success">Yes</span>
                            <?php else: ?>
                                <span class="badge badge-error">No</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($metal_group): ?>
                                <span class="status-ok"><?php echo esc_html($metal_group->name); ?></span>
                            <?php else: ?>
                                <span class="status-missing">Missing</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status-missing"><?php echo esc_html($issue_text); ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="info-box">
            <h3>📋 How to Fix:</h3>
            <ol>
                <li><strong>For products with "No metal ID assigned":</strong> Edit the product and assign a metal from the JPC Metal dropdown</li>
                <li><strong>For products with "Weight is missing or zero":</strong> Edit the product and enter the metal weight</li>
                <li><strong>For products with "Metal ID X not found":</strong> The metal was deleted. Assign a new metal to the product</li>
                <li><strong>After fixing:</strong> Run the regenerate-all-products.php script again</li>
            </ol>
        </div>
        
        <hr style="margin: 40px 0;">
        <p style="color: #999; font-size: 12px;">
            <strong>Security Note:</strong> Please delete this file (diagnose-failed-products.php) after use.
        </p>
    </div>
</body>
</html>
