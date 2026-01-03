<?php
/**
 * VERIFY CALCULATION LOGIC
 * This script will show you exactly what the plugin is calculating vs Excel
 */

// Include WordPress
require_once(__DIR__ . '/../../../wp-load.php');

$product_id = 1767; // Your product ID

echo '<h1>Calculation Verification</h1>';
echo '<style>
table { border-collapse: collapse; width: 100%; margin: 20px 0; }
th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
th { background: #4CAF50; color: white; }
tr:nth-child(even) { background: #f2f2f2; }
.correct { background: #d4edda !important; }
.wrong { background: #f8d7da !important; }
.section { background: #e7f3ff !important; font-weight: bold; }
</style>';

// Get all the raw data
$metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
$metal = JPC_Metals::get_by_id($metal_id);
$metal_group = JPC_Metal_Groups::get_by_id($metal->metal_group_id);

$weight = floatval(get_post_meta($product_id, '_jpc_metal_weight', true));
$making_charge = floatval(get_post_meta($product_id, '_jpc_making_charge', true));
$making_charge_type = get_post_meta($product_id, '_jpc_making_charge_type', true) ?: 'percentage';
$wastage_charge = floatval(get_post_meta($product_id, '_jpc_wastage_charge', true));
$wastage_charge_type = get_post_meta($product_id, '_jpc_wastage_charge_type', true) ?: 'percentage';

// Calculate base prices
$metal_price = $weight * $metal->price_per_unit;

// Diamond
$diamond_id = get_post_meta($product_id, '_jpc_diamond_id', true);
$diamond_quantity = intval(get_post_meta($product_id, '_jpc_diamond_quantity', true));
$diamond_price = 0;
if ($diamond_id && $diamond_quantity > 0) {
    $diamond = JPC_Diamonds::get_by_id($diamond_id);
    if ($diamond) {
        $diamond_unit_price = $diamond->price_per_carat * $diamond->carat;
        $diamond_price = $diamond_unit_price * $diamond_quantity;
    }
}

// Making charge
$making_charge_amount = 0;
if ($making_charge_type === 'percentage') {
    $making_charge_amount = ($metal_price * $making_charge) / 100;
} else {
    $making_charge_amount = $making_charge;
}

// Wastage charge
$wastage_charge_amount = 0;
if ($wastage_charge_type === 'percentage') {
    $wastage_charge_amount = ($metal_price * $wastage_charge) / 100;
} else {
    $wastage_charge_amount = $wastage_charge;
}

// Additional costs
$pearl_cost = floatval(get_post_meta($product_id, '_jpc_pearl_cost', true));
$stone_cost = floatval(get_post_meta($product_id, '_jpc_stone_cost', true));
$extra_fee = floatval(get_post_meta($product_id, '_jpc_extra_fee', true));

// Extra fields
$extra_field_costs = 0;
for ($i = 1; $i <= 5; $i++) {
    $extra_field_costs += floatval(get_post_meta($product_id, '_jpc_extra_field_' . $i, true));
}

// Subtotal before payment gateway
$subtotal_before_gateway = $metal_price + $diamond_price + $making_charge_amount + $wastage_charge_amount + $pearl_cost + $stone_cost + $extra_fee + $extra_field_costs;

// Payment Gateway (Additional Percentage)
$additional_percentage = floatval(get_option('jpc_additional_percentage_value', 0));
$payment_gateway_amount = ($subtotal_before_gateway * $additional_percentage) / 100;

// Subtotal after payment gateway (BASE FOR DISCOUNT)
$subtotal_after_gateway = $subtotal_before_gateway + $payment_gateway_amount;

// Discount
$discount_percentage = floatval(get_post_meta($product_id, '_jpc_discount_percentage', true));
$discount_amount = ($subtotal_after_gateway * $discount_percentage) / 100;

// Price after discount
$price_after_discount = $subtotal_after_gateway - $discount_amount;

// GST
$gst_percentage = 5; // From your Excel
$gst_on_full = ($subtotal_after_gateway * $gst_percentage) / 100;
$gst_on_discounted = ($price_after_discount * $gst_percentage) / 100;

// Final prices
$regular_price_correct = $subtotal_after_gateway + $gst_on_full;
$sale_price_correct = $price_after_discount + $gst_on_discounted;

// Get plugin's calculation
$plugin_prices = JPC_Price_Calculator::calculate_product_prices($product_id);

// Display comparison
echo '<h2>EXCEL vs PLUGIN Comparison</h2>';
echo '<table>';
echo '<tr><th>Item</th><th>Excel (Correct)</th><th>Plugin (Current)</th><th>Status</th></tr>';

$items = array(
    'Metal Price' => array($metal_price, $plugin_prices['metal_price']),
    'Diamond Price' => array($diamond_price, $plugin_prices['diamond_price']),
    'Making Charge' => array($making_charge_amount, $plugin_prices['making_charge']),
    'Wastage Charge' => array($wastage_charge_amount, $plugin_prices['wastage_charge']),
    'Pearl Cost' => array($pearl_cost, $plugin_prices['pearl_cost']),
    'Stone Cost' => array($stone_cost, $plugin_prices['stone_cost']),
    'Extra Fee' => array($extra_fee, $plugin_prices['extra_fee']),
    'Extra Fields' => array($extra_field_costs, $plugin_prices['extra_field_costs']),
);

foreach ($items as $label => $values) {
    $match = abs($values[0] - $values[1]) < 1;
    $class = $match ? 'correct' : 'wrong';
    echo "<tr class='$class'>";
    echo "<td>$label</td>";
    echo "<td>₹" . number_format($values[0], 2) . "</td>";
    echo "<td>₹" . number_format($values[1], 2) . "</td>";
    echo "<td>" . ($match ? '✓' : '✗') . "</td>";
    echo "</tr>";
}

echo '<tr class="section"><td colspan="4">CALCULATION FLOW</td></tr>';

echo "<tr><td>Subtotal Before Gateway</td><td>₹" . number_format($subtotal_before_gateway, 2) . "</td><td>₹" . number_format($plugin_prices['subtotal_before_additional'], 2) . "</td><td>" . (abs($subtotal_before_gateway - $plugin_prices['subtotal_before_additional']) < 1 ? '✓' : '✗') . "</td></tr>";

echo "<tr><td>Payment Gateway ($additional_percentage%)</td><td>₹" . number_format($payment_gateway_amount, 2) . "</td><td>₹" . number_format($plugin_prices['additional_percentage_amount'], 2) . "</td><td>" . (abs($payment_gateway_amount - $plugin_prices['additional_percentage_amount']) < 1 ? '✓' : '✗') . "</td></tr>";

echo "<tr><td><strong>Subtotal After Gateway</strong></td><td><strong>₹" . number_format($subtotal_after_gateway, 2) . "</strong></td><td><strong>₹" . number_format($plugin_prices['subtotal_after_additional'], 2) . "</strong></td><td>" . (abs($subtotal_after_gateway - $plugin_prices['subtotal_after_additional']) < 1 ? '✓' : '✗') . "</td></tr>";

echo "<tr><td>Discount ($discount_percentage%)</td><td>-₹" . number_format($discount_amount, 2) . "</td><td>-₹" . number_format($plugin_prices['discount_amount'], 2) . "</td><td>" . (abs($discount_amount - $plugin_prices['discount_amount']) < 1 ? '✓' : '✗') . "</td></tr>";

echo "<tr><td>Price After Discount</td><td>₹" . number_format($price_after_discount, 2) . "</td><td>₹" . number_format($plugin_prices['subtotal_after_discount'], 2) . "</td><td>" . (abs($price_after_discount - $plugin_prices['subtotal_after_discount']) < 1 ? '✓' : '✗') . "</td></tr>";

echo "<tr><td>GST on Full ($gst_percentage%)</td><td>₹" . number_format($gst_on_full, 2) . "</td><td>₹" . number_format($plugin_prices['gst_on_full'], 2) . "</td><td>" . (abs($gst_on_full - $plugin_prices['gst_on_full']) < 1 ? '✓' : '✗') . "</td></tr>";

echo "<tr><td>GST on Discounted ($gst_percentage%)</td><td>₹" . number_format($gst_on_discounted, 2) . "</td><td>₹" . number_format($plugin_prices['gst_on_discounted'], 2) . "</td><td>" . (abs($gst_on_discounted - $plugin_prices['gst_on_discounted']) < 1 ? '✓' : '✗') . "</td></tr>";

echo '<tr class="section"><td colspan="4">FINAL PRICES</td></tr>';

$regular_match = abs($regular_price_correct - $plugin_prices['regular_price']) < 1;
$sale_match = abs($sale_price_correct - $plugin_prices['sale_price']) < 1;

echo "<tr class='" . ($regular_match ? 'correct' : 'wrong') . "'><td><strong>Regular Price</strong></td><td><strong>₹" . number_format($regular_price_correct, 2) . "</strong></td><td><strong>₹" . number_format($plugin_prices['regular_price'], 2) . "</strong></td><td>" . ($regular_match ? '✓' : '✗') . "</td></tr>";

echo "<tr class='" . ($sale_match ? 'correct' : 'wrong') . "'><td><strong>Sale Price</strong></td><td><strong>₹" . number_format($sale_price_correct, 2) . "</strong></td><td><strong>₹" . number_format($plugin_prices['sale_price'], 2) . "</strong></td><td>" . ($sale_match ? '✓' : '✗') . "</td></tr>";

echo '</table>';

// Show settings
echo '<h2>Current Settings</h2>';
echo '<table>';
echo '<tr><th>Setting</th><th>Value</th></tr>';
echo '<tr><td>Discount Calculation Method</td><td>' . get_option('jpc_discount_calculation_method', '1') . '</td></tr>';
echo '<tr><td>GST Calculation Base</td><td>' . get_option('jpc_gst_calculation_base', 'after_discount') . '</td></tr>';
echo '<tr><td>Additional Percentage (Payment Gateway)</td><td>' . $additional_percentage . '%</td></tr>';
echo '<tr><td>Discount Percentage</td><td>' . $discount_percentage . '%</td></tr>';
echo '<tr><td>GST Percentage</td><td>' . $gst_percentage . '%</td></tr>';
echo '</table>';

echo '<hr>';
echo '<p style="color: red; font-weight: bold;">⚠️ DELETE THIS FILE AFTER CHECKING!</p>';
?>
