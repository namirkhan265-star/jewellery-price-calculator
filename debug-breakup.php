<?php
/**
 * DEBUG TOOL: Check Price Breakup Data
 * 
 * INSTRUCTIONS:
 * 1. Upload this file to your WordPress root directory
 * 2. Visit: https://yoursite.com/debug-breakup.php?product_id=123
 * 3. Replace 123 with your actual product ID
 * 4. This will show you exactly what's stored in the breakup data
 * 5. DELETE THIS FILE after debugging for security!
 */

// Load WordPress
require_once('wp-load.php');

// Get product ID from URL
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if (!$product_id) {
    die('Please provide a product_id parameter. Example: debug-breakup.php?product_id=123');
}

// Get breakup data
$breakup = get_post_meta($product_id, '_jpc_price_breakup', true);

// Get product title
$product = get_post($product_id);
$product_title = $product ? $product->post_title : 'Unknown Product';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Price Breakup Debug - Product #<?php echo $product_id; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #2271b1;
            padding-bottom: 10px;
        }
        .info {
            background: #e7f3ff;
            border-left: 4px solid #2271b1;
            padding: 15px;
            margin: 20px 0;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
        }
        .success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            border: 1px solid #ddd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background: #2271b1;
            color: white;
            font-weight: bold;
        }
        table tr:hover {
            background: #f5f5f5;
        }
        .check {
            color: #28a745;
            font-weight: bold;
        }
        .cross {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Price Breakup Debug Tool</h1>
        
        <div class="info">
            <strong>Product ID:</strong> <?php echo $product_id; ?><br>
            <strong>Product Title:</strong> <?php echo esc_html($product_title); ?>
        </div>
        
        <?php if (!$breakup || !is_array($breakup)): ?>
            <div class="error">
                <strong>❌ ERROR:</strong> No price breakup data found for this product!<br>
                <strong>Solution:</strong> Go to the product editor and click "Regenerate Price Breakup" button.
            </div>
        <?php else: ?>
            <div class="success">
                <strong>✅ SUCCESS:</strong> Price breakup data found!
            </div>
            
            <h2>📊 Breakup Data Analysis</h2>
            
            <table>
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Status</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Metal Price</strong></td>
                        <td><?php echo isset($breakup['metal_price']) ? '<span class="check">✓ EXISTS</span>' : '<span class="cross">✗ MISSING</span>'; ?></td>
                        <td><?php echo isset($breakup['metal_price']) ? number_format($breakup['metal_price'], 2) : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>GST Amount</strong></td>
                        <td><?php echo isset($breakup['gst']) ? '<span class="check">✓ EXISTS</span>' : '<span class="cross">✗ MISSING</span>'; ?></td>
                        <td><?php echo isset($breakup['gst']) ? number_format($breakup['gst'], 2) : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>GST Percentage</strong></td>
                        <td><?php echo isset($breakup['gst_percentage']) ? '<span class="check">✓ EXISTS</span>' : '<span class="cross">✗ MISSING</span>'; ?></td>
                        <td><?php echo isset($breakup['gst_percentage']) ? $breakup['gst_percentage'] . '%' : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>GST Label</strong></td>
                        <td><?php echo isset($breakup['gst_label']) ? '<span class="check">✓ EXISTS</span>' : '<span class="cross">✗ MISSING</span>'; ?></td>
                        <td><?php echo isset($breakup['gst_label']) ? $breakup['gst_label'] : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Diamond Price</strong></td>
                        <td><?php echo isset($breakup['diamond_price']) ? '<span class="check">✓ EXISTS</span>' : '<span class="cross">✗ MISSING</span>'; ?></td>
                        <td><?php echo isset($breakup['diamond_price']) ? number_format($breakup['diamond_price'], 2) : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Making Charge</strong></td>
                        <td><?php echo isset($breakup['making_charge']) ? '<span class="check">✓ EXISTS</span>' : '<span class="cross">✗ MISSING</span>'; ?></td>
                        <td><?php echo isset($breakup['making_charge']) ? number_format($breakup['making_charge'], 2) : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Discount</strong></td>
                        <td><?php echo isset($breakup['discount']) ? '<span class="check">✓ EXISTS</span>' : '<span class="cross">✗ MISSING</span>'; ?></td>
                        <td><?php echo isset($breakup['discount']) ? number_format($breakup['discount'], 2) : 'N/A'; ?></td>
                    </tr>
                </tbody>
            </table>
            
            <?php if (!isset($breakup['gst_percentage'])): ?>
                <div class="warning">
                    <strong>⚠️ WARNING:</strong> GST Percentage is missing from breakup data!<br>
                    <strong>This means:</strong> The product was calculated with an old version of the plugin.<br>
                    <strong>Solution:</strong> Click "Regenerate Price Breakup" button in the product editor.
                </div>
            <?php endif; ?>
            
            <?php if (!isset($breakup['metal_price'])): ?>
                <div class="error">
                    <strong>❌ CRITICAL:</strong> Metal Price is missing from breakup data!<br>
                    <strong>This means:</strong> The breakup data is corrupted or incomplete.<br>
                    <strong>Solution:</strong> Click "Regenerate Price Breakup" button in the product editor.
                </div>
            <?php endif; ?>
            
            <h2>📝 Complete Raw Data</h2>
            <pre><?php print_r($breakup); ?></pre>
            
            <h2>⚙️ Plugin Settings</h2>
            <table>
                <thead>
                    <tr>
                        <th>Setting</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Enable GST</strong></td>
                        <td><?php echo get_option('jpc_enable_gst', 'yes'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>GST Label</strong></td>
                        <td><?php echo get_option('jpc_gst_label', 'GST'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>GST Value (%)</strong></td>
                        <td><?php echo get_option('jpc_gst_value', '3'); ?>%</td>
                    </tr>
                    <tr>
                        <td><strong>GST Calculation Base</strong></td>
                        <td><?php echo get_option('jpc_gst_calculation_base', 'after_discount'); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
        
        <div class="info" style="margin-top: 30px;">
            <strong>🔒 SECURITY NOTE:</strong> Delete this file (debug-breakup.php) after debugging!
        </div>
    </div>
</body>
</html>
