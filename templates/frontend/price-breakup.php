<?php
/**
 * Frontend Price Breakup Template
 * Shows detailed price breakdown including price before and after discount
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get product and calculate prices
$product_id = get_the_ID();
$product = wc_get_product($product_id);

// Get discount percentage from product meta
$discount_percentage = floatval(get_post_meta($product_id, '_jpc_discount_percentage', true));

// Calculate prices using JPC calculator
$prices = JPC_Price_Calculator::calculate_product_prices($product_id);

// Calculate subtotal before discount (sum of all components)
$subtotal_before_discount = $breakup['metal_price'];

if (!empty($breakup['diamond_price'])) {
    $subtotal_before_discount += $breakup['diamond_price'];
}
if (!empty($breakup['making_charge'])) {
    $subtotal_before_discount += $breakup['making_charge'];
}
if (!empty($breakup['wastage_charge'])) {
    $subtotal_before_discount += $breakup['wastage_charge'];
}
if (!empty($breakup['pearl_cost'])) {
    $subtotal_before_discount += $breakup['pearl_cost'];
}
if (!empty($breakup['stone_cost'])) {
    $subtotal_before_discount += $breakup['stone_cost'];
}
if (!empty($breakup['extra_fee'])) {
    $subtotal_before_discount += $breakup['extra_fee'];
}

// Calculate GST on full amount (before discount)
$gst_percentage = floatval(get_option('jpc_gst_value', 5));
$metal_group = JPC_Metal_Groups::get_by_id($metal->metal_group_id);
if ($metal_group) {
    $metal_group_name = strtolower($metal_group->name);
    $metal_gst = get_option('jpc_gst_' . $metal_group_name);
    if ($metal_gst !== false && $metal_gst !== '') {
        $gst_percentage = floatval($metal_gst);
    }
}

$gst_on_full_amount = ($subtotal_before_discount * $gst_percentage) / 100;
$price_before_discount = $subtotal_before_discount + $gst_on_full_amount;

// Calculate discount amount
$discount_amount = 0;
if ($discount_percentage > 0) {
    $discount_amount = ($subtotal_before_discount * $discount_percentage) / 100;
}

// Calculate subtotal after discount
$subtotal_after_discount = $subtotal_before_discount - $discount_amount;

// Calculate GST on discounted amount
$gst_on_discounted_amount = ($subtotal_after_discount * $gst_percentage) / 100;

// Final price
$final_price = $subtotal_after_discount + $gst_on_discounted_amount;
?>

<div class="jpc-price-breakup">
    <h3><?php _e('Price Breakup', 'jewellery-price-calc'); ?></h3>
    
    <table class="jpc-price-breakup-table">
        <tbody>
            <!-- Component Breakdown -->
            <tr>
                <td><?php echo esc_html($metal->display_name); ?></td>
                <td><?php echo JPC_Frontend::format_price($breakup['metal_price']); ?></td>
            </tr>
            
            <?php if (!empty($breakup['diamond_price']) && $breakup['diamond_price'] > 0): ?>
            <tr>
                <td><?php _e('Diamond', 'jewellery-price-calc'); ?></td>
                <td><?php echo JPC_Frontend::format_price($breakup['diamond_price']); ?></td>
            </tr>
            <?php endif; ?>
            
            <?php if (!empty($breakup['making_charge']) && $breakup['making_charge'] > 0): ?>
            <tr>
                <td><?php _e('Making Charges', 'jewellery-price-calc'); ?></td>
                <td><?php echo JPC_Frontend::format_price($breakup['making_charge']); ?></td>
            </tr>
            <?php endif; ?>
            
            <?php if (!empty($breakup['wastage_charge']) && $breakup['wastage_charge'] > 0): ?>
            <tr>
                <td><?php _e('Wastage Charge', 'jewellery-price-calc'); ?></td>
                <td><?php echo JPC_Frontend::format_price($breakup['wastage_charge']); ?></td>
            </tr>
            <?php endif; ?>
            
            <?php if (!empty($breakup['pearl_cost']) && $breakup['pearl_cost'] > 0): ?>
            <tr>
                <td><?php _e('Pearl Cost', 'jewellery-price-calc'); ?></td>
                <td><?php echo JPC_Frontend::format_price($breakup['pearl_cost']); ?></td>
            </tr>
            <?php endif; ?>
            
            <?php if (!empty($breakup['stone_cost']) && $breakup['stone_cost'] > 0): ?>
            <tr>
                <td><?php _e('Stone Cost', 'jewellery-price-calc'); ?></td>
                <td><?php echo JPC_Frontend::format_price($breakup['stone_cost']); ?></td>
            </tr>
            <?php endif; ?>
            
            <?php if (!empty($breakup['extra_fee']) && $breakup['extra_fee'] > 0): ?>
            <tr>
                <td><?php _e('Extra Fee', 'jewellery-price-calc'); ?></td>
                <td><?php echo JPC_Frontend::format_price($breakup['extra_fee']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Subtotal Before Discount -->
            <tr class="subtotal-row">
                <td><strong><?php _e('Subtotal', 'jewellery-price-calc'); ?></strong></td>
                <td><strong><?php echo JPC_Frontend::format_price($subtotal_before_discount); ?></strong></td>
            </tr>
            
            <!-- GST on Full Amount -->
            <tr class="gst-row">
                <td><?php echo get_option('jpc_gst_label', 'GST'); ?> (<?php echo $gst_percentage; ?>%)</td>
                <td><?php echo JPC_Frontend::format_price($gst_on_full_amount); ?></td>
            </tr>
            
            <!-- Price Before Discount (Highlighted) -->
            <tr class="price-before-discount-row" style="background: #f0f0f0; font-weight: bold;">
                <td><strong><?php _e('Price Before Discount', 'jewellery-price-calc'); ?></strong></td>
                <td><strong><?php echo JPC_Frontend::format_price($price_before_discount); ?></strong></td>
            </tr>
            
            <!-- Discount -->
            <?php if ($discount_percentage > 0 && $discount_amount > 0): ?>
            <tr class="discount-row" style="color: #d63638;">
                <td>
                    <?php _e('Discount', 'jewellery-price-calc'); ?>
                    <span class="discount-percentage" style="color: #d63638; font-weight: bold;">
                        (<?php echo number_format($discount_percentage, 0); ?>% OFF)
                    </span>
                </td>
                <td class="discount-amount" style="color: #d63638; font-weight: bold;">
                    - <?php echo JPC_Frontend::format_price($discount_amount); ?>
                </td>
            </tr>
            
            <!-- Subtotal After Discount -->
            <tr class="subtotal-after-discount-row">
                <td><?php _e('Subtotal After Discount', 'jewellery-price-calc'); ?></td>
                <td><?php echo JPC_Frontend::format_price($subtotal_after_discount); ?></td>
            </tr>
            
            <!-- GST on Discounted Amount -->
            <tr class="gst-discounted-row">
                <td><?php echo get_option('jpc_gst_label', 'GST'); ?> (<?php echo $gst_percentage; ?>%)</td>
                <td><?php echo JPC_Frontend::format_price($gst_on_discounted_amount); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Final Price -->
            <tr class="total-row" style="background: #2271b1; color: white; font-size: 1.1em;">
                <td><strong><?php _e('Final Price', 'jewellery-price-calc'); ?></strong></td>
                <td><strong><?php echo JPC_Frontend::format_price($final_price); ?></strong></td>
            </tr>
        </tbody>
    </table>
    
    <?php if ($discount_percentage > 0): ?>
    <div class="jpc-savings-badge" style="margin-top: 15px; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; text-align: center;">
        <strong style="color: #155724; font-size: 1.1em;">
            🎉 You Save: <?php echo JPC_Frontend::format_price($discount_amount); ?> 
            (<?php echo number_format($discount_percentage, 0); ?>% OFF)
        </strong>
    </div>
    <?php endif; ?>
</div>
