<?php
/**
 * DIAGNOSTIC SCRIPT: Test Pearl/Stone/Extra Fee Calculation
 * 
 * HOW TO USE:
 * 1. Upload this file to your WordPress root directory
 * 2. Access it via: https://yoursite.com/test-pearl-stone-calculation.php
 * 3. It will show you if the percentage calculation is working
 * 
 * IMPORTANT: Delete this file after testing for security!
 */

// Load WordPress
require_once('wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Access denied. You must be an administrator.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Pearl/Stone/Extra Fee Calculation Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2271b1; border-bottom: 3px solid #2271b1; padding-bottom: 10px; }
        h2 { color: #333; margin-top: 30px; background: #f0f0f1; padding: 10px; border-left: 4px solid #2271b1; }
        .test-result { margin: 20px 0; padding: 15px; border-radius: 4px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #2271b1; color: white; font-weight: bold; }
        tr:hover { background: #f5f5f5; }
        .code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .highlight { background: #fbbf24; padding: 2px 6px; border-radius: 3px; font-weight: bold; }
        .button { display: inline-block; padding: 10px 20px; background: #2271b1; color: white; text-decoration: none; border-radius: 4px; margin: 10px 5px; }
        .button:hover { background: #135e96; }
        .button-danger { background: #dc3545; }
        .button-danger:hover { background: #c82333; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Pearl/Stone/Extra Fee Calculation Diagnostic</h1>
        
        <?php
        // Test 1: Check if updated calculator class exists
        echo '<h2>Test 1: Check Calculator Class</h2>';
        
        if (class_exists('JPC_Price_Calculator')) {
            echo '<div class="test-result success">✅ JPC_Price_Calculator class found</div>';
            
            // Check if the new method exists
            if (method_exists('JPC_Price_Calculator', 'calculate_additional_cost')) {
                echo '<div class="test-result success">✅ NEW METHOD FOUND: calculate_additional_cost() exists!</div>';
                echo '<div class="test-result info">📝 This means you have uploaded the updated file correctly.</div>';
            } else {
                echo '<div class="test-result error">❌ OLD VERSION: calculate_additional_cost() method NOT found!</div>';
                echo '<div class="test-result warning">⚠️ You need to upload the updated class-jpc-price-calculator-v2.php file.</div>';
            }
        } else {
            echo '<div class="test-result error">❌ JPC_Price_Calculator class not found!</div>';
        }
        
        // Test 2: Check settings
        echo '<h2>Test 2: Check Settings</h2>';
        
        $pearl_type = get_option('jpc_pearl_cost_type', 'fixed');
        $stone_type = get_option('jpc_stone_cost_type', 'fixed');
        $extra_type = get_option('jpc_extra_fee_type', 'fixed');
        
        echo '<table>';
        echo '<tr><th>Setting</th><th>Current Value</th><th>Status</th></tr>';
        echo '<tr><td>Pearl Cost Type</td><td class="code">' . $pearl_type . '</td><td>' . ($pearl_type === 'percentage' ? '<span class="highlight">PERCENTAGE MODE</span>' : 'Fixed Mode') . '</td></tr>';
        echo '<tr><td>Stone Cost Type</td><td class="code">' . $stone_type . '</td><td>' . ($stone_type === 'percentage' ? '<span class="highlight">PERCENTAGE MODE</span>' : 'Fixed Mode') . '</td></tr>';
        echo '<tr><td>Extra Fee Type</td><td class="code">' . $extra_type . '</td><td>' . ($extra_type === 'percentage' ? '<span class="highlight">PERCENTAGE MODE</span>' : 'Fixed Mode') . '</td></tr>';
        echo '</table>';
        
        // Test 3: Test with sample product
        echo '<h2>Test 3: Sample Calculation Test</h2>';
        
        // Get a product with JPC data
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 1,
            'meta_query' => array(
                array(
                    'key' => '_jpc_metal_id',
                    'compare' => 'EXISTS'
                )
            )
        );
        
        $products = get_posts($args);
        
        if (!empty($products)) {
            $product = $products[0];
            $product_id = $product->ID;
            
            echo '<div class="test-result info">📦 Testing with product: <strong>' . $product->post_title . '</strong> (ID: ' . $product_id . ')</div>';
            
            // Get product data
            $pearl_value = floatval(get_post_meta($product_id, '_jpc_pearl_cost', true));
            $stone_value = floatval(get_post_meta($product_id, '_jpc_stone_cost', true));
            $extra_value = floatval(get_post_meta($product_id, '_jpc_extra_fee', true));
            
            echo '<table>';
            echo '<tr><th>Field</th><th>Stored Value</th><th>Type</th><th>Expected Behavior</th></tr>';
            
            if ($pearl_value > 0) {
                echo '<tr>';
                echo '<td>Pearl Cost</td>';
                echo '<td class="code">' . $pearl_value . '</td>';
                echo '<td class="code">' . $pearl_type . '</td>';
                echo '<td>' . ($pearl_type === 'percentage' ? 'Should calculate ' . $pearl_value . '% of subtotal' : 'Should add ₹' . $pearl_value . ' fixed') . '</td>';
                echo '</tr>';
            }
            
            if ($stone_value > 0) {
                echo '<tr>';
                echo '<td>Stone Cost</td>';
                echo '<td class="code">' . $stone_value . '</td>';
                echo '<td class="code">' . $stone_type . '</td>';
                echo '<td>' . ($stone_type === 'percentage' ? 'Should calculate ' . $stone_value . '% of subtotal' : 'Should add ₹' . $stone_value . ' fixed') . '</td>';
                echo '</tr>';
            }
            
            if ($extra_value > 0) {
                echo '<tr>';
                echo '<td>Extra Fee</td>';
                echo '<td class="code">' . $extra_value . '</td>';
                echo '<td class="code">' . $extra_type . '</td>';
                echo '<td>' . ($extra_type === 'percentage' ? 'Should calculate ' . $extra_value . '% of subtotal' : 'Should add ₹' . $extra_value . ' fixed') . '</td>';
                echo '</tr>';
            }
            
            echo '</table>';
            
            // Try to recalculate
            echo '<div class="test-result info">🔄 Attempting to recalculate this product...</div>';
            
            if (class_exists('JPC_Price_Calculator')) {
                $result = JPC_Price_Calculator::calculate_and_update_price($product_id);
                
                if ($result) {
                    echo '<div class="test-result success">✅ Price recalculated successfully!</div>';
                    echo '<div class="test-result info">📝 Check the product on frontend to see if the calculation is correct now.</div>';
                    echo '<a href="' . get_permalink($product_id) . '" class="button" target="_blank">View Product on Frontend</a>';
                } else {
                    echo '<div class="test-result error">❌ Price recalculation failed!</div>';
                }
            }
            
        } else {
            echo '<div class="test-result warning">⚠️ No products found with JPC data. Create a product first.</div>';
        }
        
        // Test 4: Action buttons
        echo '<h2>Test 4: Actions</h2>';
        
        echo '<div class="test-result info">';
        echo '<p><strong>What to do next:</strong></p>';
        echo '<ol>';
        echo '<li>If the <code>calculate_additional_cost()</code> method exists, the fix is applied ✅</li>';
        echo '<li>Go to <strong>Jewellery Price → General Settings</strong></li>';
        echo '<li>Scroll to bottom and click <strong>"Bulk Regenerate Price Breakup"</strong></li>';
        echo '<li>Wait for all products to be regenerated</li>';
        echo '<li>Check your products on the frontend - percentage calculation should work now!</li>';
        echo '</ol>';
        echo '</div>';
        
        echo '<a href="' . admin_url('admin.php?page=jewellery-price-calc') . '" class="button">Go to General Settings</a>';
        echo '<a href="' . admin_url('admin.php?page=jpc-debug') . '" class="button">Go to Debug Page</a>';
        
        // Security warning
        echo '<h2>⚠️ Security Warning</h2>';
        echo '<div class="test-result warning">';
        echo '<p><strong>IMPORTANT:</strong> Delete this test file after you\'re done!</p>';
        echo '<p>This file exposes diagnostic information and should not be left on your server.</p>';
        echo '<p>File location: <code>' . __FILE__ . '</code></p>';
        echo '</div>';
        
        ?>
        
    </div>
</body>
</html>
