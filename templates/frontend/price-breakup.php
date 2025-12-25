<?php
/**
 * Frontend Price Breakup Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="jpc-price-breakup">
    <h3><?php _e('Price Breakup', 'jewellery-price-calc'); ?></h3>
    
    <table class="jpc-price-breakup-table">
        <tbody>
            <tr>
                <td><?php echo esc_html($metal->display_name); ?> <?php _e('Price', 'jewellery-price-calc'); ?></td>
                <td><?php echo JPC_Frontend::format_price($breakup['metal_price']); ?></td>
            </tr>
            
            <?php if ($breakup['making_charge'] > 0): ?>
            <tr>
                <td><?php _e('Making Charges', 'jewellery-price-calc'); ?></td>
                <td><?php echo JPC_Frontend::format_price($breakup['making_charge']); ?></td>
            </tr>
            <?php endif; ?>
            
            <?php if ($breakup['wastage_charge'] > 0): ?>
            <tr>
                <td><?php _e('Wastage Charges', 'jewellery-price-calc'); ?></td>
                <td><?php echo JPC_Frontend::format_price($breakup['wastage_charge']); ?></td>
            </tr>
            <?php endif; ?>
            
            <?php if ($breakup['pearl_cost'] > 0): ?>
            <tr>
                <td><?php _e('Pearl Cost', 'jewellery-price-calc'); ?></td>
                <td><?php echo JPC_Frontend::format_price($breakup['pearl_cost']); ?></td>
            </tr>
            <?php endif; ?>
            
            <?php if ($breakup['stone_cost'] > 0): ?>
            <tr>
                <td><?php _e('Stone Cost', 'jewellery-price-calc'); ?></td>
                <td><?php echo JPC_Frontend::format_price($breakup['stone_cost']); ?></td>
            </tr>
            <?php endif; ?>
            
            <?php if ($breakup['extra_fee'] > 0): ?>
            <tr>
                <td><?php _e('Extra Fee', 'jewellery-price-calc'); ?></td>
                <td><?php echo JPC_Frontend::format_price($breakup['extra_fee']); ?></td>
            </tr>
            <?php endif; ?>
            
            <?php if ($breakup['discount'] > 0): ?>
            <tr class="discount-row">
                <td><?php _e('Discount', 'jewellery-price-calc'); ?></td>
                <td>-<?php echo JPC_Frontend::format_price($breakup['discount']); ?></td>
            </tr>
            <?php endif; ?>
            
            <?php if ($breakup['gst'] > 0): ?>
            <tr class="gst-row">
                <td><?php echo get_option('jpc_gst_label', 'Tax'); ?></td>
                <td><?php echo JPC_Frontend::format_price($breakup['gst']); ?></td>
            </tr>
            <?php endif; ?>
            
            <tr class="total-row">
                <td><?php _e('Total Price', 'jewellery-price-calc'); ?></td>
                <td><?php echo JPC_Frontend::format_price($breakup['final_price']); ?></td>
            </tr>
        </tbody>
    </table>
</div>
