<?php
/**
 * Frontend Price Breakup Template - Uses Backend Data
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get all data from backend
$product_id = get_the_ID();
$discount_percentage = floatval(get_post_meta($product_id, '_jpc_discount_percentage', true));
$regular_price = floatval(get_post_meta($product_id, '_regular_price', true));
$sale_price = floatval(get_post_meta($product_id, '_sale_price', true));
$discount_amount = $regular_price - $sale_price;
?>

<div class="jpc-price-breakup">
    <h3><?php _e('PRICE BREAKUP', 'jewellery-price-calc'); ?></h3>
    
    <table class="jpc-price-breakup-table">
        <tbody>
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
            
            <?php if ($discount_percentage > 0 && $discount_amount > 0): ?>
            <tr class="discount-row" style="color: #d63638;">
                <td>
                    <?php _e('Discount', 'jewellery-price-calc'); ?>
                    <span style="color: #d63638; font-weight: bold;">
                        (<?php echo number_format($discount_percentage, 0); ?>% OFF)
                    </span>
                </td>
                <td style="color: #d63638; font-weight: bold;">
                    - <?php echo wc_price($discount_amount); ?>
                </td>
            </tr>
            <?php endif; ?>
            
            <?php if (!empty($breakup['gst']) && $breakup['gst'] > 0): ?>
            <tr class="gst-row">
                <td><?php echo get_option('jpc_gst_label', 'GST'); ?></td>
                <td><?php echo JPC_Frontend::format_price($breakup['gst']); ?></td>
            </tr>
            <?php endif; ?>
            
            <tr style="border-top: 2px solid #000; padding-top: 10px;">
                <td colspan="2">&nbsp;</td>
            </tr>
            
            <!-- REGULAR PRICE (Before Discount) -->
            <tr class="regular-price-row">
                <td><strong><?php _e('Regular Price', 'jewellery-price-calc'); ?></strong></td>
                <td><strong style="text-decoration: line-through; color: #999;"><?php echo wc_price($regular_price); ?></strong></td>
            </tr>
            
            <!-- SALE PRICE (After Discount) -->
            <tr class="sale-price-row">
                <td><strong style="color: #d63638; font-size: 1.1em;"><?php _e('Sale Price', 'jewellery-price-calc'); ?></strong></td>
                <td><strong style="color: #d63638; font-size: 1.2em;"><?php echo wc_price($sale_price); ?></strong></td>
            </tr>
        </tbody>
    </table>
    
    <?php if ($discount_percentage > 0 && $discount_amount > 0): ?>
    <div class="jpc-savings-badge" style="margin-top: 15px; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; text-align: center;">
        <strong style="color: #155724; font-size: 1.1em;">
            🎉 You Save: <?php echo wc_price($discount_amount); ?> 
            (<?php echo number_format($discount_percentage, 0); ?>% OFF)
        </strong>
    </div>
    <?php endif; ?>
</div>
