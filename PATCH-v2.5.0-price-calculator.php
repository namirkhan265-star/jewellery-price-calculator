<?php
/**
 * PATCH v2.5.0: Updated price calculation logic for pearl_cost, stone_cost, and extra_fee
 * 
 * INSTRUCTIONS:
 * 1. Open includes/class-jpc-price-calculator.php
 * 2. Find the section that gets additional costs (around line 79-82)
 * 3. Replace the "Get additional costs" section with the code below
 */

// Get additional costs - UPDATED v2.5.0 to support percentage calculations
$pearl_cost_value = floatval(get_post_meta($product_id, '_jpc_pearl_cost', true));
$pearl_cost_type = get_option('jpc_pearl_cost_type', 'fixed');
$pearl_cost = 0;
if ($pearl_cost_value > 0) {
    if ($pearl_cost_type === 'percentage') {
        $pearl_cost = ($metal_price * $pearl_cost_value) / 100;
    } else {
        $pearl_cost = $pearl_cost_value;
    }
}

$stone_cost_value = floatval(get_post_meta($product_id, '_jpc_stone_cost', true));
$stone_cost_type = get_option('jpc_stone_cost_type', 'fixed');
$stone_cost = 0;
if ($stone_cost_value > 0) {
    if ($stone_cost_type === 'percentage') {
        $stone_cost = ($metal_price * $stone_cost_value) / 100;
    } else {
        $stone_cost = $stone_cost_value;
    }
}

$extra_fee_value = floatval(get_post_meta($product_id, '_jpc_extra_fee', true));
$extra_fee_type = get_option('jpc_extra_fee_type', 'fixed');
$extra_fee = 0;
if ($extra_fee_value > 0) {
    if ($extra_fee_type === 'percentage') {
        $extra_fee = ($metal_price * $extra_fee_value) / 100;
    } else {
        $extra_fee = $extra_fee_value;
    }
}

// Get extra field costs (these remain as fixed amounts)
$extra_field_costs = 0;
for ($i = 1; $i <= 5; $i++) {
    $extra_field_costs += floatval(get_post_meta($product_id, '_jpc_extra_field_' . $i, true));
}

// Calculate subtotal before additional percentage
$subtotal_before_additional = $metal_price + $diamond_price + $making_charge_amount + $wastage_charge_amount + $pearl_cost + $stone_cost + $extra_fee + $extra_field_costs;
