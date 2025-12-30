<?php
/**
 * Frontend Price Breakup Template
 */

if (!defined('ABSPATH')) {
    exit;
}

// Calculate discount percentage if discount exists
$discount_percentage = 0;
if (!empty($breakup['discount']) && $breakup['discount'] > 0) {
    // Get the price before discount
    $price_before_discount = $breakup['final_price'] + $breakup['discount'];
    if ($price_before_discount > 0) {
        $discount_percentage = ($breakup['discount'] / $price_before_discount) * 100;
    }
}
?>

<div class="jpc-price-breakup">
    <h3><?php _e('Price Breakup', 'jewellery-price-calc'); ?></h3>
    
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
            
            <?php if (!empty($breakup['discount']) && $breakup['discount'] > 0): ?>
            <tr class="discount-row">
                <td>
                    <?php _e('Discount', 'jewellery-price-calc'); ?>
                    <?php if ($discount_percentage > 0): ?>
                        <span style="color: #46b450; font-weight: 600;">(<?php printf('%.0f%%', $discount_percentage); ?> OFF)</span>
                    <?php endif; ?>
                </td>
                <td style="color: #46b450;">- <?php echo JPC_Frontend::format_price($breakup['discount']); ?></td>
            </tr>
            <?php endif; ?>
            
            <?php if (!empty($breakup['gst']) && $breakup['gst'] > 0): ?>
            <tr class="gst-row">
                <td><?php echo get_option('jpc_gst_label', 'GST'); ?></td>
                <td><?php echo JPC_Frontend::format_price($breakup['gst']); ?></td>
            </tr>
            <?php endif; ?>
            
            <tr class="total-row">
                <td><?php _e('Total', 'jewellery-price-calc'); ?></td>
                <td><?php echo JPC_Frontend::format_price($breakup['final_price']); ?></td>
            </tr>
        </tbody>
    </table>
</div>
