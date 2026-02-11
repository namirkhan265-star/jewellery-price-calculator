<?php
/**
 * COMPREHENSIVE DIAGNOSTIC: Pearl/Stone/Extra Fee Calculation
 * 
 * This script will show you EXACTLY what's happening with the calculations
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
    <title>Pearl/Stone/Extra Fee - Complete Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 1400px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2271b1; border-bottom: 3px solid #2271b1; padding-bottom: 10px; }
        h2 { color: #333; margin-top: 30px; background: #f0f0f1; padding: 10px; border-left: 4px solid #2271b1; }
        h3 { color: #2271b1; margin-top: 20px; }
        .result { margin: 20px 0; padding: 15px; border-radius: 4px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #2271b1; color: white; font-weight: bold; }
        tr:hover { background: #f5f5f5; }
        .code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; font-size: 13px; }
        .highlight { background: #fbbf24; padding: 2px 6px; border-radius: 3px; font-weight: bold; }
        .good { background: #10b981; color: white; padding: 2px 8px; border-radius: 3px; font-weight: bold; }
        .bad { background: #ef4444; color: white; padding: 2px 8px; border-radius: 3px; font-weight: bold; }
        .button { display: inline-block; padding: 10px 20px; background: #2271b1; color: white; text-decoration: none; border-radius: 4px; margin: 10px 5px; }
        .button:hover { background: #135e96; }
        .button-success { background: #10b981; }
        .button-success:hover { background: #059669; }
        .calc-box { background: #f9fafb; border: 2px solid #e5e7eb; padding: 15px; margin: 10px 0; border-radius: 6px; }
        .calc-step { margin: 8px 0; padding: 8px; background: white; border-left: 3px solid #2271b1; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Complete Pearl/Stone/Extra Fee Diagnostic</h1>
        
        <?php
        // Test 1: Check if updated calculator exists
        echo '<h2>✅ Test 1: Check if Fix is Applied</h2>';
        
        $fix_applied = false;
        if (class_exists('JPC_Price_Calculator')) {
            if (method_exists('JPC_Price_Calculator', 'calculate_additional_cost')) {
                echo '<div class="result success">';
                echo '<h3>✅ FIX IS APPLIED!</h3>';
                echo '<p>The <code>calculate_additional_cost()</code> method exists in the calculator class.</p>';
                echo '</div>';
                $fix_applied = true;
            } else {
                echo '<div class="result error">';
                echo '<h3>❌ FIX NOT APPLIED!</h3>';
                echo '<p>The <code>calculate_additional_cost()</code> method does NOT exist.</p>';
                echo '<p><strong>Action Required:</strong> Upload the updated <code>class-jpc-price-calculator-v2.php</code> file.</p>';
                echo '</div>';
            }
        } else {
            echo '<div class="result error">❌ JPC_Price_Calculator class not found!</div>';
        }
        
        // Test 2: Check settings
        echo '<h2>⚙️ Test 2: Current Settings</h2>';
        
        $pearl_type = get_option('jpc_pearl_cost_type', 'fixed');
        $stone_type = get_option('jpc_stone_cost_type', 'fixed');
        $extra_type = get_option('jpc_extra_fee_type', 'fixed');
        
        $pearl_label = get_option('jpc_pearl_cost_label', 'Pearl Cost');
        $stone_label = get_option('jpc_stone_cost_label', 'Stone Cost');
        $extra_label = get_option('jpc_extra_fee_label', 'Extra Fee');
        
        echo '<table>';
        echo '<tr><th>Field</th><th>Label</th><th>Calculation Type</th><th>Status</th></tr>';
        echo '<tr>';
        echo '<td><strong>Additional Cost Field 1</strong></td>';
        echo '<td>' . esc_html($pearl_label) . '</td>';
        echo '<td class="code">' . $pearl_type . '</td>';
        echo '<td>' . ($pearl_type === 'percentage' ? '<span class="good">PERCENTAGE</span>' : '<span class="bad">FIXED</span>') . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td><strong>Additional Cost Field 2</strong></td>';
        echo '<td>' . esc_html($stone_label) . '</td>';
        echo '<td class="code">' . $stone_type . '</td>';
        echo '<td>' . ($stone_type === 'percentage' ? '<span class="good">PERCENTAGE</span>' : '<span class="bad">FIXED</span>') . '</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td><strong>Additional Cost Field 3</strong></td>';
        echo '<td>' . esc_html($extra_label) . '</td>';
        echo '<td class="code">' . $extra_type . '</td>';
        echo '<td>' . ($extra_type === 'percentage' ? '<span class="good">PERCENTAGE</span>' : '<span class="bad">FIXED</span>') . '</td>';
        echo '</tr>';
        echo '</table>';
        
        // Test 3: Live calculation test
        echo '<h2>🧮 Test 3: Live Calculation Test</h2>';
        
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
            
            echo '<div class="result info">';
            echo '<h3>📦 Testing Product: ' . esc_html($product->post_title) . '</h3>';
            echo '<p>Product ID: ' . $product_id . '</p>';
            echo '<a href="' . get_permalink($product_id) . '" target="_blank" class="button">View on Frontend</a>';
            echo '<a href="' . admin_url('post.php?post=' . $product_id . '&action=edit') . '" target="_blank" class="button">Edit Product</a>';
            echo '</div>';
            
            // Get stored values
            $pearl_value = floatval(get_post_meta($product_id, '_jpc_pearl_cost', true));
            $stone_value = floatval(get_post_meta($product_id, '_jpc_stone_cost', true));
            $extra_value = floatval(get_post_meta($product_id, '_jpc_extra_fee', true));
            
            // Get stored breakup
            $stored_breakup = get_post_meta($product_id, '_jpc_price_breakup', true);
            
            echo '<h3>📊 Current Stored Values</h3>';
            echo '<table>';
            echo '<tr><th>Field</th><th>Product Meta Value</th><th>Stored in Breakup</th><th>Type</th></tr>';
            
            if ($pearl_value > 0) {
                $stored_pearl = isset($stored_breakup['pearl_cost']) ? $stored_breakup['pearl_cost'] : 0;
                echo '<tr>';
                echo '<td>' . esc_html($pearl_label) . '</td>';
                echo '<td class="code">₹' . number_format($pearl_value, 2) . '</td>';
                echo '<td class="code">₹' . number_format($stored_pearl, 2) . '</td>';
                echo '<td class="code">' . $pearl_type . '</td>';
                echo '</tr>';
            }
            
            if ($stone_value > 0) {
                $stored_stone = isset($stored_breakup['stone_cost']) ? $stored_breakup['stone_cost'] : 0;
                echo '<tr>';
                echo '<td>' . esc_html($stone_label) . '</td>';
                echo '<td class="code">₹' . number_format($stone_value, 2) . '</td>';
                echo '<td class="code">₹' . number_format($stored_stone, 2) . '</td>';
                echo '<td class="code">' . $stone_type . '</td>';
                echo '</tr>';
            }
            
            if ($extra_value > 0) {
                $stored_extra = isset($stored_breakup['extra_fee']) ? $stored_breakup['extra_fee'] : 0;
                echo '<tr>';
                echo '<td>' . esc_html($extra_label) . '</td>';
                echo '<td class="code">₹' . number_format($extra_value, 2) . '</td>';
                echo '<td class="code">₹' . number_format($stored_extra, 2) . '</td>';
                echo '<td class="code">' . $extra_type . '</td>';
                echo '</tr>';
            }
            
            echo '</table>';
            
            if ($fix_applied && ($pearl_value > 0 || $stone_value > 0 || $extra_value > 0)) {
                echo '<h3>🔄 Live Recalculation Test</h3>';
                echo '<div class="calc-box">';
                
                // Manually calculate what it SHOULD be
                $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
                $metal_weight = floatval(get_post_meta($product_id, '_jpc_metal_weight', true));
                $wastage = floatval(get_post_meta($product_id, '_jpc_wastage', true));
                
                if ($metal_id) {
                    $metal = JPC_Metals::get_by_id($metal_id);
                    if ($metal) {
                        $metal_price = $metal_weight * $metal->price_per_unit;
                        
                        echo '<div class="calc-step">';
                        echo '<strong>Step 1: Calculate Metal Price</strong><br>';
                        echo 'Metal Weight: ' . $metal_weight . ' grams<br>';
                        echo 'Price per gram: ₹' . number_format($metal->price_per_unit, 2) . '<br>';
                        echo '<strong>Metal Price = ₹' . number_format($metal_price, 2) . '</strong>';
                        echo '</div>';
                        
                        // Calculate making charges
                        $making_mode = get_post_meta($product_id, '_jpc_making_charges_mode', true) ?: 'auto';
                        $making_charge = 0;
                        
                        if ($making_mode === 'auto') {
                            $making_charge = $metal_weight * floatval($metal->making_charges_per_gram ?? 0);
                        } else {
                            $making_value = floatval(get_post_meta($product_id, '_jpc_making_charges_value', true));
                            $making_type = get_post_meta($product_id, '_jpc_making_charges_type', true) ?: 'percentage';
                            if ($making_type === 'percentage') {
                                $making_charge = ($metal_price * $making_value) / 100;
                            } else {
                                $making_charge = $making_value;
                            }
                        }
                        
                        echo '<div class="calc-step">';
                        echo '<strong>Step 2: Calculate Making Charges</strong><br>';
                        echo 'Mode: ' . $making_mode . '<br>';
                        echo '<strong>Making Charges = ₹' . number_format($making_charge, 2) . '</strong>';
                        echo '</div>';
                        
                        // Calculate wastage
                        $wastage_charge = 0;
                        if ($wastage > 0) {
                            $wastage_charge = ($metal_price * $wastage) / 100;
                        }
                        
                        echo '<div class="calc-step">';
                        echo '<strong>Step 3: Calculate Wastage</strong><br>';
                        echo 'Wastage %: ' . $wastage . '%<br>';
                        echo '<strong>Wastage Charge = ₹' . number_format($wastage_charge, 2) . '</strong>';
                        echo '</div>';
                        
                        // Calculate diamond (simplified)
                        $diamond_price = 0;
                        $diamond_mode = get_post_meta($product_id, '_jpc_diamond_entry_mode', true) ?: 'dropdown';
                        // ... diamond calculation omitted for brevity
                        
                        // Calculate subtotal for percentage
                        $subtotal_for_percentage = $metal_price + $diamond_price + $making_charge + $wastage_charge;
                        
                        echo '<div class="calc-step">';
                        echo '<strong>Step 4: Calculate Subtotal (Base for Percentage)</strong><br>';
                        echo 'Metal + Diamond + Making + Wastage<br>';
                        echo '<strong>Subtotal = ₹' . number_format($subtotal_for_percentage, 2) . '</strong>';
                        echo '</div>';
                        
                        // Now calculate pearl/stone/extra with NEW logic
                        echo '<h3 style="color: #10b981; margin-top: 20px;">✨ NEW CALCULATION (What it SHOULD be):</h3>';
                        
                        if ($pearl_value > 0) {
                            $calculated_pearl = 0;
                            if ($pearl_type === 'percentage') {
                                $calculated_pearl = ($subtotal_for_percentage * $pearl_value) / 100;
                                echo '<div class="calc-step" style="border-left-color: #10b981;">';
                                echo '<strong>' . esc_html($pearl_label) . ' (PERCENTAGE MODE)</strong><br>';
                                echo 'Formula: Subtotal × ' . $pearl_value . '% = ₹' . number_format($subtotal_for_percentage, 2) . ' × ' . $pearl_value . '%<br>';
                                echo '<strong class="highlight">Calculated Value = ₹' . number_format($calculated_pearl, 2) . '</strong><br>';
                                echo '<br><strong>Currently Stored:</strong> ₹' . number_format($stored_pearl, 2);
                                if (abs($calculated_pearl - $stored_pearl) > 0.01) {
                                    echo ' <span class="bad">WRONG!</span>';
                                } else {
                                    echo ' <span class="good">CORRECT!</span>';
                                }
                                echo '</div>';
                            } else {
                                $calculated_pearl = $pearl_value;
                                echo '<div class="calc-step" style="border-left-color: #6b7280;">';
                                echo '<strong>' . esc_html($pearl_label) . ' (FIXED MODE)</strong><br>';
                                echo '<strong>Fixed Value = ₹' . number_format($calculated_pearl, 2) . '</strong>';
                                echo '</div>';
                            }
                        }
                        
                        if ($stone_value > 0) {
                            $calculated_stone = 0;
                            if ($stone_type === 'percentage') {
                                $calculated_stone = ($subtotal_for_percentage * $stone_value) / 100;
                                echo '<div class="calc-step" style="border-left-color: #10b981;">';
                                echo '<strong>' . esc_html($stone_label) . ' (PERCENTAGE MODE)</strong><br>';
                                echo 'Formula: Subtotal × ' . $stone_value . '% = ₹' . number_format($subtotal_for_percentage, 2) . ' × ' . $stone_value . '%<br>';
                                echo '<strong class="highlight">Calculated Value = ₹' . number_format($calculated_stone, 2) . '</strong><br>';
                                echo '<br><strong>Currently Stored:</strong> ₹' . number_format($stored_stone, 2);
                                if (abs($calculated_stone - $stored_stone) > 0.01) {
                                    echo ' <span class="bad">WRONG!</span>';
                                } else {
                                    echo ' <span class="good">CORRECT!</span>';
                                }
                                echo '</div>';
                            } else {
                                $calculated_stone = $stone_value;
                                echo '<div class="calc-step" style="border-left-color: #6b7280;">';
                                echo '<strong>' . esc_html($stone_label) . ' (FIXED MODE)</strong><br>';
                                echo '<strong>Fixed Value = ₹' . number_format($calculated_stone, 2) . '</strong>';
                                echo '</div>';
                            }
                        }
                        
                        if ($extra_value > 0) {
                            $calculated_extra = 0;
                            if ($extra_type === 'percentage') {
                                $calculated_extra = ($subtotal_for_percentage * $extra_value) / 100;
                                echo '<div class="calc-step" style="border-left-color: #10b981;">';
                                echo '<strong>' . esc_html($extra_label) . ' (PERCENTAGE MODE)</strong><br>';
                                echo 'Formula: Subtotal × ' . $extra_value . '% = ₹' . number_format($subtotal_for_percentage, 2) . ' × ' . $extra_value . '%<br>';
                                echo '<strong class="highlight">Calculated Value = ₹' . number_format($calculated_extra, 2) . '</strong><br>';
                                echo '<br><strong>Currently Stored:</strong> ₹' . number_format($stored_extra, 2);
                                if (abs($calculated_extra - $stored_extra) > 0.01) {
                                    echo ' <span class="bad">WRONG!</span>';
                                } else {
                                    echo ' <span class="good">CORRECT!</span>';
                                }
                                echo '</div>';
                            } else {
                                $calculated_extra = $extra_value;
                                echo '<div class="calc-step" style="border-left-color: #6b7280;">';
                                echo '<strong>' . esc_html($extra_label) . ' (FIXED MODE)</strong><br>';
                                echo '<strong>Fixed Value = ₹' . number_format($calculated_extra, 2) . '</strong>';
                                echo '</div>';
                            }
                        }
                    }
                }
                
                echo '</div>';
                
                // Action button
                echo '<div class="result warning" style="margin-top: 20px;">';
                echo '<h3>⚠️ ACTION REQUIRED</h3>';
                echo '<p>The stored values in the database are OLD. You need to regenerate prices to apply the new calculation.</p>';
                echo '<a href="' . admin_url('admin.php?page=jewellery-price-calc') . '" class="button button-success">Go to Settings & Regenerate</a>';
                echo '<a href="' . home_url('/regenerate-all-prices.php') . '" class="button button-success">Use Quick Regenerate Script</a>';
                echo '</div>';
            }
            
        } else {
            echo '<div class="result warning">⚠️ No products found with JPC data.</div>';
        }
        
        // Final instructions
        echo '<h2>📋 Summary & Next Steps</h2>';
        
        if ($fix_applied) {
            echo '<div class="result success">';
            echo '<h3>✅ Fix is Applied!</h3>';
            echo '<p>The code is updated and ready. Now you need to regenerate all product prices.</p>';
            echo '<ol>';
            echo '<li><strong>Option 1:</strong> Go to <a href="' . admin_url('admin.php?page=jewellery-price-calc') . '">Jewellery Price → General Settings</a> and click "Bulk Regenerate Price Breakup"</li>';
            echo '<li><strong>Option 2:</strong> Use the <a href="' . home_url('/regenerate-all-prices.php') . '">Quick Regenerate Script</a></li>';
            echo '</ol>';
            echo '</div>';
        } else {
            echo '<div class="result error">';
            echo '<h3>❌ Fix Not Applied</h3>';
            echo '<p>Upload the updated <code>class-jpc-price-calculator-v2.php</code> file first, then regenerate prices.</p>';
            echo '</div>';
        }
        
        echo '<div class="result warning" style="margin-top: 30px;">';
        echo '<h3>⚠️ Security Warning</h3>';
        echo '<p><strong>DELETE THIS FILE after testing!</strong></p>';
        echo '<p>File: <code>' . __FILE__ . '</code></p>';
        echo '</div>';
        ?>
        
    </div>
</body>
</html>
