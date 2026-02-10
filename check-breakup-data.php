<?php
/**
 * Check Breakup Data
 * 
 * This script shows the actual breakup data stored in product meta
 * to verify if custom labels are being stored correctly
 * 
 * INSTRUCTIONS:
 * 1. Upload this file to your WordPress root directory
 * 2. Access it via: https://yoursite.com/check-breakup-data.php?product_id=2637
 * 3. Review the breakup data
 * 4. DELETE this file after use for security
 */

// Load WordPress
require_once('wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    die('Access denied. You must be an administrator.');
}

// Get product ID from URL
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 2637;

// Get product
$product = get_post($product_id);
if (!$product) {
    die('Product not found');
}

// Get breakup data
$breakup = get_post_meta($product_id, '_jpc_price_breakup', true);

// Get settings labels
$pearl_cost_label_setting = get_option('jpc_pearl_cost_label', 'Pearl Cost');
$stone_cost_label_setting = get_option('jpc_stone_cost_label', 'Stone Cost');
$extra_fee_label_setting = get_option('jpc_extra_fee_label', 'Extra Fee');

?>
<!DOCTYPE html>
<html>
<head>
    <title>Check Breakup Data - Product <?php echo $product_id; ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #0073aa; padding-bottom: 10px; }
        h2 { color: #0073aa; margin-top: 30px; }
        .info-box { padding: 15px; margin: 15px 0; border-radius: 4px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .warning { background: #fff3cd; border: 1px solid #ffc107; color: #856404; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 4px; overflow-x: auto; border: 1px solid #ddd; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #0073aa; color: white; font-weight: bold; }
        .highlight { background: #ffeb3b; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Breakup Data Check</h1>
        
        <div class="info-box info">
            <p><strong>Product ID:</strong> <?php echo $product_id; ?></p>
            <p><strong>Product Name:</strong> <?php echo esc_html($product->post_title); ?></p>
            <p><strong>Date:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>
        
        <h2>📋 Settings Labels (from WordPress Options)</h2>
        <table>
            <tr>
                <th>Setting</th>
                <th>Value</th>
            </tr>
            <tr>
                <td>Pearl Cost Label</td>
                <td class="highlight"><?php echo esc_html($pearl_cost_label_setting); ?></td>
            </tr>
            <tr>
                <td>Stone Cost Label</td>
                <td class="highlight"><?php echo esc_html($stone_cost_label_setting); ?></td>
            </tr>
            <tr>
                <td>Extra Fee Label</td>
                <td class="highlight"><?php echo esc_html($extra_fee_label_setting); ?></td>
            </tr>
        </table>
        
        <h2>💾 Stored Breakup Data (from Product Meta)</h2>
        
        <?php if ($breakup && is_array($breakup)): ?>
            
            <table>
                <tr>
                    <th>Key</th>
                    <th>Value</th>
                </tr>
                
                <!-- Check for custom labels in breakup -->
                <tr>
                    <td><strong>pearl_cost_label</strong></td>
                    <td class="highlight">
                        <?php 
                        if (isset($breakup['pearl_cost_label'])) {
                            echo esc_html($breakup['pearl_cost_label']);
                        } else {
                            echo '<span style="color: red;">NOT FOUND IN BREAKUP DATA</span>';
                        }
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <td><strong>stone_cost_label</strong></td>
                    <td class="highlight">
                        <?php 
                        if (isset($breakup['stone_cost_label'])) {
                            echo esc_html($breakup['stone_cost_label']);
                        } else {
                            echo '<span style="color: red;">NOT FOUND IN BREAKUP DATA</span>';
                        }
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <td><strong>extra_fee_label</strong></td>
                    <td class="highlight">
                        <?php 
                        if (isset($breakup['extra_fee_label'])) {
                            echo esc_html($breakup['extra_fee_label']);
                        } else {
                            echo '<span style="color: red;">NOT FOUND IN BREAKUP DATA</span>';
                        }
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <td>pearl_cost</td>
                    <td><?php echo isset($breakup['pearl_cost']) ? $breakup['pearl_cost'] : 'N/A'; ?></td>
                </tr>
                
                <tr>
                    <td>stone_cost</td>
                    <td><?php echo isset($breakup['stone_cost']) ? $breakup['stone_cost'] : 'N/A'; ?></td>
                </tr>
                
                <tr>
                    <td>extra_fee</td>
                    <td><?php echo isset($breakup['extra_fee']) ? $breakup['extra_fee'] : 'N/A'; ?></td>
                </tr>
            </table>
            
            <h2>🔍 Full Breakup Data (Raw)</h2>
            <pre><?php print_r($breakup); ?></pre>
            
            <?php
            // Check if labels are in breakup
            $has_pearl_label = isset($breakup['pearl_cost_label']) && !empty($breakup['pearl_cost_label']);
            $has_stone_label = isset($breakup['stone_cost_label']) && !empty($breakup['stone_cost_label']);
            $has_extra_label = isset($breakup['extra_fee_label']) && !empty($breakup['extra_fee_label']);
            
            if ($has_pearl_label && $has_stone_label && $has_extra_label):
            ?>
                <div class="info-box success">
                    <h3>✅ Labels Found in Breakup Data!</h3>
                    <p>The custom labels ARE stored in the breakup data. The frontend template should display them.</p>
                    <p><strong>Next Steps:</strong></p>
                    <ul>
                        <li>Clear your browser cache (Ctrl+Shift+R)</li>
                        <li>Clear WordPress cache (if using a caching plugin)</li>
                        <li>Check if your theme is overriding the template files</li>
                    </ul>
                </div>
            <?php else: ?>
                <div class="info-box error">
                    <h3>❌ Labels NOT Found in Breakup Data!</h3>
                    <p>The custom labels are NOT stored in the breakup data. This is why the frontend shows old labels.</p>
                    <p><strong>Solution:</strong></p>
                    <ul>
                        <li>Go to the product editor for this product</li>
                        <li>Click "Regenerate Price Breakup" button</li>
                        <li>OR run the regenerate-all-products.php script again</li>
                    </ul>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            
            <div class="info-box error">
                <h3>❌ No Breakup Data Found!</h3>
                <p>This product doesn't have any breakup data stored.</p>
                <p><strong>Solution:</strong> Go to the product editor and click "Regenerate Price Breakup"</p>
            </div>
            
        <?php endif; ?>
        
        <h2>🔧 Test Other Products</h2>
        <p>Check other products by changing the URL:</p>
        <ul>
            <li><a href="?product_id=2637">Product 2637 (Test Product)</a></li>
            <li><a href="?product_id=2869">Product 2869 (Test Product 2)</a></li>
            <li><a href="?product_id=2541">Product 2541 (Elegant V-Halo Solitaire Ring)</a></li>
            <li><a href="?product_id=2537">Product 2537 (Silver Krishna Flute Bracelet)</a></li>
        </ul>
        
        <hr style="margin: 40px 0;">
        <p style="color: #999; font-size: 12px;">
            <strong>Security Note:</strong> Please delete this file (check-breakup-data.php) after use.
        </p>
    </div>
</body>
</html>
