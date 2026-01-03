<?php
/**
 * Debug Calculator Script
 * 
 * Upload this file to your WordPress root directory
 * Access it via: https://yoursite.com/debug-calculator.php?product_id=123
 * 
 * This will show you exactly what values are being used in calculations
 */

// Load WordPress
require_once('wp-load.php');

// Get product ID from URL
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if (!$product_id) {
    die('Please provide a product_id in the URL: ?product_id=123');
}

echo "<h1>JPC Calculator Debug - Product #$product_id</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    th { background-color: #4CAF50; color: white; }
    tr:nth-child(even) { background-color: #f2f2f2; }
    .section { margin: 30px 0; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
</style>";

// Check if product exists
$product = wc_get_product($product_id);
if (!$product) {
    die("<p class='error'>Product #$product_id not found!</p>");
}

echo "<p class='success'>✓ Product found: " . $product->get_name() . "</p>";

// 1. Check Product Meta Values
echo "<div class='section'>";
echo "<h2>1. Product Meta Values (Saved in Database)</h2>";
echo "<table>";
echo "<tr><th>Meta Key</th><th>Value</th><th>Status</th></tr>";

$meta_keys = array(
    '_jpc_metal_id' => 'Metal ID',
    '_jpc_metal_weight' => 'Metal Weight',
    '_jpc_diamond_id' => 'Diamond ID',
    '_jpc_diamond_quantity' => 'Diamond Quantity',
    '_jpc_making_charge' => 'Making Charge',
    '_jpc_making_charge_type' => 'Making Charge Type',
    '_jpc_wastage_charge' => 'Wastage Charge',
    '_jpc_wastage_charge_type' => 'Wastage Charge Type',
    '_jpc_pearl_cost' => 'Pearl Cost',
    '_jpc_stone_cost' => 'Stone Cost',
    '_jpc_extra_fee' => 'Extra Fee',
    '_jpc_extra_field_1' => 'Extra Field #1',
    '_jpc_extra_field_2' => 'Extra Field #2',
    '_jpc_extra_field_3' => 'Extra Field #3',
    '_jpc_extra_field_4' => 'Extra Field #4',
    '_jpc_extra_field_5' => 'Extra Field #5',
    '_jpc_discount_percentage' => 'Discount %',
    '_regular_price' => 'WooCommerce Regular Price',
    '_sale_price' => 'WooCommerce Sale Price',
);

foreach ($meta_keys as $key => $label) {
    $value = get_post_meta($product_id, $key, true);
    $status = $value ? "<span class='success'>✓ Set</span>" : "<span class='warning'>⚠ Empty</span>";
    $display_value = $value ? $value : '<em>empty</em>';
    echo "<tr><td>$label</td><td>$display_value</td><td>$status</td></tr>";
}

echo "</table>";
echo "</div>";

// 2. Check Global Settings
echo "<div class='section'>";
echo "<h2>2. Global Settings (Plugin Configuration)</h2>";
echo "<table>";
echo "<tr><th>Setting</th><th>Value</th><th>Status</th></tr>";

$settings = array(
    'jpc_enable_gst' => 'GST Enabled',
    'jpc_gst_value' => 'Default GST %',
    'jpc_gst_gold' => 'Gold GST %',
    'jpc_gst_silver' => 'Silver GST %',
    'jpc_gst_diamond' => 'Diamond GST %',
    'jpc_gst_platinum' => 'Platinum GST %',
    'jpc_additional_percentage_value' => 'Additional Percentage %',
    'jpc_additional_percentage_label' => 'Additional Percentage Label',
    'jpc_enable_extra_field_1' => 'Extra Field #1 Enabled',
    'jpc_extra_field_label_1' => 'Extra Field #1 Label',
    'jpc_enable_extra_field_2' => 'Extra Field #2 Enabled',
    'jpc_extra_field_label_2' => 'Extra Field #2 Label',
    'jpc_enable_extra_field_3' => 'Extra Field #3 Enabled',
    'jpc_extra_field_label_3' => 'Extra Field #3 Label',
    'jpc_enable_extra_field_4' => 'Extra Field #4 Enabled',
    'jpc_extra_field_label_4' => 'Extra Field #4 Label',
    'jpc_enable_extra_field_5' => 'Extra Field #5 Enabled',
    'jpc_extra_field_label_5' => 'Extra Field #5 Label',
    'jpc_discount_on_metals' => 'Discount on Metals',
    'jpc_discount_on_making' => 'Discount on Making',
    'jpc_discount_on_wastage' => 'Discount on Wastage',
);

foreach ($settings as $key => $label) {
    $value = get_option($key);
    $status = $value ? "<span class='success'>✓ Set</span>" : "<span class='warning'>⚠ Not Set</span>";
    $display_value = $value ? $value : '<em>not set</em>';
    echo "<tr><td>$label</td><td>$display_value</td><td>$status</td></tr>";
}

echo "</table>";
echo "</div>";

// 3. Check Metal Data
$metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
if ($metal_id) {
    echo "<div class='section'>";
    echo "<h2>3. Metal Information</h2>";
    
    global $wpdb;
    $metal = $wpdb->get_row($wpdb->prepare(
        "SELECT m.*, mg.name as group_name 
         FROM {$wpdb->prefix}jpc_metals m 
         LEFT JOIN {$wpdb->prefix}jpc_metal_groups mg ON m.metal_group_id = mg.id 
         WHERE m.id = %d",
        $metal_id
    ));
    
    if ($metal) {
        echo "<table>";
        echo "<tr><th>Property</th><th>Value</th></tr>";
        echo "<tr><td>Metal Name</td><td>{$metal->display_name}</td></tr>";
        echo "<tr><td>Metal Group</td><td>{$metal->group_name}</td></tr>";
        echo "<tr><td>Price Per Unit</td><td>₹" . number_format($metal->price_per_unit, 2) . "</td></tr>";
        echo "<tr><td>Unit</td><td>{$metal->unit}</td></tr>";
        
        // Check metal-specific GST
        $metal_group_lower = strtolower($metal->group_name);
        $metal_gst = get_option('jpc_gst_' . $metal_group_lower);
        echo "<tr><td>Metal-Specific GST</td><td>" . ($metal_gst ? $metal_gst . '%' : '<em>Using default GST</em>') . "</td></tr>";
        
        echo "</table>";
    } else {
        echo "<p class='error'>✗ Metal #$metal_id not found in database!</p>";
    }
    echo "</div>";
}

// 4. Run Actual Calculation
echo "<div class='section'>";
echo "<h2>4. Live Calculation Test</h2>";

if (class_exists('JPC_Price_Calculator')) {
    $prices = JPC_Price_Calculator::calculate_product_prices($product_id);
    
    if ($prices) {
        echo "<table>";
        echo "<tr><th>Component</th><th>Value</th></tr>";
        echo "<tr><td>Regular Price (Before Discount)</td><td class='success'>₹" . number_format($prices['regular_price'], 2) . "</td></tr>";
        echo "<tr><td>Sale Price (After Discount)</td><td class='success'>₹" . number_format($prices['sale_price'], 2) . "</td></tr>";
        echo "<tr><td>Discount Amount</td><td>₹" . number_format($prices['discount_amount'], 2) . "</td></tr>";
        echo "<tr><td>Discount Percentage</td><td>" . $prices['discount_percentage'] . "%</td></tr>";
        echo "<tr><td>GST on Full Amount</td><td>₹" . number_format($prices['gst_on_full'], 2) . "</td></tr>";
        echo "<tr><td>GST on Discounted Amount</td><td>₹" . number_format($prices['gst_on_discounted'], 2) . "</td></tr>";
        echo "<tr><td>Additional Percentage Amount</td><td>₹" . number_format($prices['additional_percentage_amount'], 2) . "</td></tr>";
        echo "<tr><td>Extra Field Costs Total</td><td>₹" . number_format($prices['extra_field_costs'], 2) . "</td></tr>";
        echo "</table>";
        
        echo "<p class='success'>✓ Calculation successful!</p>";
    } else {
        echo "<p class='error'>✗ Calculation failed! Check if metal and weight are set.</p>";
    }
} else {
    echo "<p class='error'>✗ JPC_Price_Calculator class not found!</p>";
}

echo "</div>";

// 5. JavaScript Check
echo "<div class='section'>";
echo "<h2>5. JavaScript Files Check</h2>";
echo "<p>Check if these files exist on your server:</p>";
echo "<ul>";
echo "<li><code>/wp-content/plugins/jewellery-price-calculator/assets/js/live-calculator.js</code></li>";
echo "</ul>";
echo "<p>Clear your browser cache and check the browser console for JavaScript errors.</p>";
echo "</div>";

echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>If Extra Fields show 'Empty' but you filled them in admin, the meta is not being saved. Check class-jpc-product-meta.php save function.</li>";
echo "<li>If GST settings show 'Not Set', go to plugin settings and configure them.</li>";
echo "<li>If calculation shows ₹0.00 for extra fields, they're not being included in the calculation.</li>";
echo "<li>Clear ALL caches: WordPress cache, browser cache, and any caching plugins.</li>";
echo "</ol>";
?>
