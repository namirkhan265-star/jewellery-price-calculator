<?php
/**
 * DEBUG: Live Calculation Trace
 * This will show EXACTLY what calculate_product_prices() returns
 */

require_once('wp-load.php');

if (!current_user_can('manage_options')) {
    die('Access denied');
}

// Get product ID from URL or use first product
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if (!$product_id) {
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
        $product_id = $products[0]->ID;
    }
}

if (!$product_id) {
    die('No product found');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Live Calculation Debug</title>
    <style>
        body { font-family: monospace; margin: 40px; background: #1e1e1e; color: #d4d4d4; }
        .container { max-width: 1200px; margin: 0 auto; background: #252526; padding: 30px; border-radius: 8px; }
        h1 { color: #4ec9b0; border-bottom: 2px solid #4ec9b0; padding-bottom: 10px; }
        h2 { color: #dcdcaa; margin-top: 30px; }
        .step { background: #1e1e1e; padding: 15px; margin: 10px 0; border-left: 4px solid #4ec9b0; }
        .value { color: #ce9178; font-weight: bold; }
        .label { color: #9cdcfe; }
        .error { color: #f48771; }
        .success { color: #4ec9b0; }
        .warning { color: #dcdcaa; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #3e3e42; }
        th { background: #1e1e1e; color: #4ec9b0; }
        .highlight { background: #264f78; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Live Calculation Debug - Product #<?php echo $product_id; ?></h1>
        
        <?php
        $product = get_post($product_id);
        echo '<p class="label">Product: <span class="value">' . esc_html($product->post_title) . '</span></p>';
        
        echo '<h2>📊 Step 1: Get Product Meta Values</h2>';
        
        $pearl_meta = floatval(get_post_meta($product_id, '_jpc_pearl_cost', true));
        $stone_meta = floatval(get_post_meta($product_id, '_jpc_stone_cost', true));
        $extra_meta = floatval(get_post_meta($product_id, '_jpc_extra_fee', true));
        
        echo '<div class="step">';
        echo '<p><span class="label">_jpc_pearl_cost:</span> <span class="value">' . $pearl_meta . '</span></p>';
        echo '<p><span class="label">_jpc_stone_cost:</span> <span class="value">' . $stone_meta . '</span></p>';
        echo '<p><span class="label">_jpc_extra_fee:</span> <span class="value">' . $extra_meta . '</span></p>';
        echo '</div>';
        
        echo '<h2>⚙️ Step 2: Get Settings</h2>';
        
        $pearl_type = get_option('jpc_pearl_cost_type', 'fixed');
        $stone_type = get_option('jpc_stone_cost_type', 'fixed');
        $extra_type = get_option('jpc_extra_fee_type', 'fixed');
        
        echo '<div class="step">';
        echo '<p><span class="label">jpc_pearl_cost_type:</span> <span class="value">' . $pearl_type . '</span></p>';
        echo '<p><span class="label">jpc_stone_cost_type:</span> <span class="value">' . $stone_type . '</span></p>';
        echo '<p><span class="label">jpc_extra_fee_type:</span> <span class="value">' . $extra_type . '</span></p>';
        echo '</div>';
        
        echo '<h2>🧮 Step 3: Call calculate_product_prices()</h2>';
        
        if (class_exists('JPC_Price_Calculator')) {
            $prices = JPC_Price_Calculator::calculate_product_prices($product_id);
            
            if ($prices) {
                echo '<div class="step">';
                echo '<p class="success">✅ Function executed successfully!</p>';
                echo '<table>';
                echo '<tr><th>Key</th><th>Value</th></tr>';
                foreach ($prices as $key => $value) {
                    if (is_numeric($value)) {
                        echo '<tr><td class="label">' . $key . '</td><td class="value">₹' . number_format($value, 2) . '</td></tr>';
                    } else {
                        echo '<tr><td class="label">' . $key . '</td><td class="value">' . esc_html($value) . '</td></tr>';
                    }
                }
                echo '</table>';
                echo '</div>';
                
                echo '<h2>🎯 Step 4: Focus on Pearl/Stone/Extra</h2>';
                
                echo '<div class="step">';
                echo '<p><span class="label">Subtotal for Percentage Base:</span></p>';
                echo '<p class="value">Metal (₹' . number_format($prices['metal_price'], 2) . ') + ';
                echo 'Diamond (₹' . number_format($prices['diamond_price'], 2) . ') + ';
                echo 'Making (₹' . number_format($prices['making_charge'], 2) . ') + ';
                echo 'Wastage (₹' . number_format($prices['wastage_charge'], 2) . ')</p>';
                
                $subtotal = $prices['metal_price'] + $prices['diamond_price'] + $prices['making_charge'] + $prices['wastage_charge'];
                echo '<p class="highlight">= ₹' . number_format($subtotal, 2) . '</p>';
                echo '</div>';
                
                echo '<div class="step">';
                echo '<h3 class="success">Pearl Cost Calculation:</h3>';
                echo '<p><span class="label">Meta Value:</span> <span class="value">' . $pearl_meta . '</span></p>';
                echo '<p><span class="label">Type:</span> <span class="value">' . $pearl_type . '</span></p>';
                if ($pearl_type === 'percentage') {
                    $expected = ($subtotal * $pearl_meta) / 100;
                    echo '<p><span class="label">Expected:</span> <span class="value">₹' . number_format($subtotal, 2) . ' × ' . $pearl_meta . '% = ₹' . number_format($expected, 2) . '</span></p>';
                } else {
                    echo '<p><span class="label">Expected:</span> <span class="value">₹' . number_format($pearl_meta, 2) . ' (fixed)</span></p>';
                }
                echo '<p><span class="label">Returned by function:</span> <span class="value">₹' . number_format($prices['pearl_cost'], 2) . '</span></p>';
                
                if ($pearl_type === 'percentage') {
                    $expected = ($subtotal * $pearl_meta) / 100;
                    if (abs($prices['pearl_cost'] - $expected) < 0.01) {
                        echo '<p class="success">✅ CORRECT!</p>';
                    } else {
                        echo '<p class="error">❌ WRONG! Expected ₹' . number_format($expected, 2) . ' but got ₹' . number_format($prices['pearl_cost'], 2) . '</p>';
                    }
                }
                echo '</div>';
                
                echo '<div class="step">';
                echo '<h3 class="success">Stone Cost Calculation:</h3>';
                echo '<p><span class="label">Meta Value:</span> <span class="value">' . $stone_meta . '</span></p>';
                echo '<p><span class="label">Type:</span> <span class="value">' . $stone_type . '</span></p>';
                if ($stone_type === 'percentage') {
                    $expected = ($subtotal * $stone_meta) / 100;
                    echo '<p><span class="label">Expected:</span> <span class="value">₹' . number_format($subtotal, 2) . ' × ' . $stone_meta . '% = ₹' . number_format($expected, 2) . '</span></p>';
                } else {
                    echo '<p><span class="label">Expected:</span> <span class="value">₹' . number_format($stone_meta, 2) . ' (fixed)</span></p>';
                }
                echo '<p><span class="label">Returned by function:</span> <span class="value">₹' . number_format($prices['stone_cost'], 2) . '</span></p>';
                
                if ($stone_type === 'percentage') {
                    $expected = ($subtotal * $stone_meta) / 100;
                    if (abs($prices['stone_cost'] - $expected) < 0.01) {
                        echo '<p class="success">✅ CORRECT!</p>';
                    } else {
                        echo '<p class="error">❌ WRONG! Expected ₹' . number_format($expected, 2) . ' but got ₹' . number_format($prices['stone_cost'], 2) . '</p>';
                    }
                }
                echo '</div>';
                
                echo '<div class="step">';
                echo '<h3 class="success">Extra Fee Calculation:</h3>';
                echo '<p><span class="label">Meta Value:</span> <span class="value">' . $extra_meta . '</span></p>';
                echo '<p><span class="label">Type:</span> <span class="value">' . $extra_type . '</span></p>';
                if ($extra_type === 'percentage') {
                    $expected = ($subtotal * $extra_meta) / 100;
                    echo '<p><span class="label">Expected:</span> <span class="value">₹' . number_format($subtotal, 2) . ' × ' . $extra_meta . '% = ₹' . number_format($expected, 2) . '</span></p>';
                } else {
                    echo '<p><span class="label">Expected:</span> <span class="value">₹' . number_format($extra_meta, 2) . ' (fixed)</span></p>';
                }
                echo '<p><span class="label">Returned by function:</span> <span class="value">₹' . number_format($prices['extra_fee'], 2) . '</span></p>';
                
                if ($extra_type === 'percentage') {
                    $expected = ($subtotal * $extra_meta) / 100;
                    if (abs($prices['extra_fee'] - $expected) < 0.01) {
                        echo '<p class="success">✅ CORRECT!</p>';
                    } else {
                        echo '<p class="error">❌ WRONG! Expected ₹' . number_format($expected, 2) . ' but got ₹' . number_format($prices['extra_fee'], 2) . '</p>';
                    }
                }
                echo '</div>';
                
                echo '<h2>💾 Step 5: Check Stored Breakup</h2>';
                
                $stored_breakup = get_post_meta($product_id, '_jpc_price_breakup', true);
                
                if ($stored_breakup) {
                    echo '<div class="step">';
                    echo '<table>';
                    echo '<tr><th>Field</th><th>Calculated (from function)</th><th>Stored (in database)</th><th>Match?</th></tr>';
                    
                    $fields = array('pearl_cost', 'stone_cost', 'extra_fee');
                    foreach ($fields as $field) {
                        $calc = isset($prices[$field]) ? $prices[$field] : 0;
                        $stored = isset($stored_breakup[$field]) ? $stored_breakup[$field] : 0;
                        $match = abs($calc - $stored) < 0.01;
                        
                        echo '<tr>';
                        echo '<td class="label">' . $field . '</td>';
                        echo '<td class="value">₹' . number_format($calc, 2) . '</td>';
                        echo '<td class="value">₹' . number_format($stored, 2) . '</td>';
                        echo '<td>' . ($match ? '<span class="success">✅ MATCH</span>' : '<span class="error">❌ MISMATCH</span>') . '</td>';
                        echo '</tr>';
                    }
                    
                    echo '</table>';
                    echo '</div>';
                } else {
                    echo '<div class="step">';
                    echo '<p class="error">❌ No price breakup stored!</p>';
                    echo '</div>';
                }
                
                echo '<h2>🔄 Step 6: Test Regeneration</h2>';
                
                echo '<div class="step">';
                echo '<p class="warning">Click button below to regenerate THIS product only:</p>';
                echo '<form method="post">';
                echo '<input type="hidden" name="regenerate_single" value="1">';
                echo '<button type="submit" style="padding: 10px 20px; background: #4ec9b0; color: #1e1e1e; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;">Regenerate Product #' . $product_id . '</button>';
                echo '</form>';
                echo '</div>';
                
                if (isset($_POST['regenerate_single'])) {
                    echo '<div class="step">';
                    echo '<p class="warning">🔄 Regenerating...</p>';
                    
                    $result = JPC_Price_Calculator::calculate_and_store_breakup($product_id);
                    
                    if ($result) {
                        echo '<p class="success">✅ Regeneration complete!</p>';
                        echo '<p><a href="?" style="color: #4ec9b0;">Refresh page to see new values</a></p>';
                    } else {
                        echo '<p class="error">❌ Regeneration failed!</p>';
                    }
                    echo '</div>';
                }
                
            } else {
                echo '<div class="step">';
                echo '<p class="error">❌ calculate_product_prices() returned FALSE!</p>';
                echo '</div>';
            }
        } else {
            echo '<div class="step">';
            echo '<p class="error">❌ JPC_Price_Calculator class not found!</p>';
            echo '</div>';
        }
        
        echo '<div class="step" style="margin-top: 40px; border-left-color: #f48771;">';
        echo '<h3 class="error">⚠️ DELETE THIS FILE AFTER TESTING!</h3>';
        echo '<p>File: <code>' . __FILE__ . '</code></p>';
        echo '</div>';
        ?>
        
    </div>
</body>
</html>
