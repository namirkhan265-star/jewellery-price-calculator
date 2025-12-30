<?php
/**
 * Check Database Values - Direct SQL Query
 * Upload this to: /public_html/check-db-values.php
 * Then visit: https://detailx.co.in/check-db-values.php
 */

// Load WordPress
require_once(__DIR__ . '/wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Access denied. You must be logged in as admin.');
}

// Get product ID from URL or use default
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

// If no product ID, find the first JPC product
if (!$product_id) {
    global $wpdb;
    $product_id = $wpdb->get_var("
        SELECT post_id 
        FROM {$wpdb->postmeta} 
        WHERE meta_key = '_jpc_metal_id' 
        LIMIT 1
    ");
}

if (!$product_id) {
    die('No JPC products found in database.');
}

// Get product data
$product = wc_get_product($product_id);
if (!$product) {
    die('Product not found.');
}

// Get meta values directly from database
global $wpdb;
$meta_data = $wpdb->get_results($wpdb->prepare("
    SELECT meta_key, meta_value 
    FROM {$wpdb->postmeta} 
    WHERE post_id = %d 
    AND meta_key IN ('_regular_price', '_sale_price', '_price', '_jpc_discount_percentage', '_jpc_price_breakup')
    ORDER BY meta_key
", $product_id), ARRAY_A);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Values Check</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #0073aa; padding-bottom: 10px; }
        h2 { color: #0073aa; margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #0073aa; color: white; font-weight: bold; }
        tr:hover { background: #f9f9f9; }
        .price { font-size: 18px; font-weight: bold; color: #0066cc; }
        .sale-price { color: #d63638; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 4px; color: #155724; margin: 20px 0; }
        .warning { background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 4px; color: #856404; margin: 20px 0; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 4px; color: #721c24; margin: 20px 0; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Database Values Check</h1>
        
        <div class="success">
            <strong>Product:</strong> <?php echo esc_html($product->get_name()); ?> (ID: <?php echo $product_id; ?>)
        </div>
        
        <h2>📊 Price Meta Values (Direct from Database)</h2>
        <table>
            <thead>
                <tr>
                    <th>Meta Key</th>
                    <th>Meta Value</th>
                    <th>Formatted</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($meta_data as $meta): ?>
                <tr>
                    <td><code><?php echo esc_html($meta['meta_key']); ?></code></td>
                    <td><?php echo esc_html($meta['meta_value']); ?></td>
                    <td>
                        <?php 
                        if (in_array($meta['meta_key'], ['_regular_price', '_sale_price', '_price'])) {
                            $price = floatval($meta['meta_value']);
                            echo '<span class="price">₹' . number_format($price, 2) . '</span>';
                        } elseif ($meta['meta_key'] === '_jpc_discount_percentage') {
                            echo '<strong>' . floatval($meta['meta_value']) . '%</strong>';
                        } else {
                            echo '<em>Breakup data (array)</em>';
                        }
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <h2>💰 Calculated Values</h2>
        <?php
        $regular_price = floatval(get_post_meta($product_id, '_regular_price', true));
        $sale_price = floatval(get_post_meta($product_id, '_sale_price', true));
        $discount_percentage = floatval(get_post_meta($product_id, '_jpc_discount_percentage', true));
        $discount_amount = $regular_price - $sale_price;
        ?>
        <table>
            <tr>
                <th>Regular Price</th>
                <td class="price">₹<?php echo number_format($regular_price, 2); ?></td>
            </tr>
            <tr>
                <th>Sale Price</th>
                <td class="price sale-price">₹<?php echo number_format($sale_price, 2); ?></td>
            </tr>
            <tr>
                <th>Discount Percentage</th>
                <td><strong><?php echo $discount_percentage; ?>%</strong></td>
            </tr>
            <tr>
                <th>Discount Amount</th>
                <td class="price">₹<?php echo number_format($discount_amount, 2); ?></td>
            </tr>
        </table>
        
        <?php if ($regular_price != 307902): ?>
        <div class="error">
            <strong>⚠️ ISSUE FOUND!</strong><br>
            Regular Price in database is <strong>₹<?php echo number_format($regular_price, 2); ?></strong><br>
            Expected: <strong>₹307,902.00</strong><br><br>
            <strong>Solution:</strong> Go to <code>Jewellery Price → General</code> and click <strong>"Update All Prices Now"</strong> button.
        </div>
        <?php else: ?>
        <div class="success">
            <strong>✅ DATABASE IS CORRECT!</strong><br>
            Regular Price: ₹<?php echo number_format($regular_price, 2); ?><br>
            The issue is in the template display, not the database.
        </div>
        <?php endif; ?>
        
        <h2>🔗 Quick Links</h2>
        <p>
            <a href="<?php echo admin_url('admin.php?page=jewellery-price-calc'); ?>" style="background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 5px;">Go to Plugin Settings</a>
            <a href="<?php echo get_edit_post_link($product_id); ?>" style="background: #00a32a; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 5px;">Edit This Product</a>
            <a href="<?php echo get_permalink($product_id); ?>" style="background: #d63638; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 5px;">View Product Page</a>
        </p>
        
        <div class="warning">
            <strong>🗑️ DELETE THIS FILE</strong> after checking! It exposes database information.
        </div>
    </div>
</body>
</html>
