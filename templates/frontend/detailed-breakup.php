<?php
/**
 * Detailed Price Breakup Template
 */

if (!defined('ABSPATH')) {
    exit;
}

// Calculate discount percentage if discount exists
$discount_percentage = 0;
if ($breakup['discount'] > 0 && $breakup['subtotal'] > 0) {
    $price_before_discount = $breakup['subtotal'] + $breakup['discount'];
    $discount_percentage = ($breakup['discount'] / $price_before_discount) * 100;
}
?>

<?php if ($discount_percentage > 0): ?>
<div class="jpc-discount-badge">
    <span class="discount-icon">🎉</span>
    <?php printf(__('You Save: %.0f%% Off', 'jewellery-price-calc'), $discount_percentage); ?>
</div>
<?php endif; ?>

<details class="jpc-detailed-breakup">
    <summary><?php _e('View Detailed Price Breakup', 'jewellery-price-calc'); ?></summary>
    
    <div class="jpc-detailed-breakup-content">
        <table class="jpc-price-breakup-table">
            <tbody>
                <?php if ($breakup['metal_price'] > 0): ?>
                <tr>
                    <td><?php _e('Metal Price', 'jewellery-price-calc'); ?></td>
                    <td><?php echo JPC_Frontend::format_price($breakup['metal_price']); ?></td>
                </tr>
                <?php endif; ?>
                
                <?php if ($breakup['diamond_price'] > 0): ?>
                <tr>
                    <td><?php _e('Diamond Price', 'jewellery-price-calc'); ?></td>
                    <td><?php echo JPC_Frontend::format_price($breakup['diamond_price']); ?></td>
                </tr>
                <?php endif; ?>
                
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
                <tr>
                    <td><strong><?php _e('Subtotal (Before Discount)', 'jewellery-price-calc'); ?></strong></td>
                    <td><strong><?php echo JPC_Frontend::format_price($breakup['subtotal'] + $breakup['discount']); ?></strong></td>
                </tr>
                <?php else: ?>
                <tr>
                    <td><strong><?php _e('Subtotal', 'jewellery-price-calc'); ?></strong></td>
                    <td><strong><?php echo JPC_Frontend::format_price($breakup['subtotal']); ?></strong></td>
                </tr>
                <?php endif; ?>
                
                <?php if ($breakup['discount'] > 0): ?>
                <tr class="discount-row">
                    <td>
                        <?php _e('Discount', 'jewellery-price-calc'); ?>
                        <?php if ($discount_percentage > 0): ?>
                            <span style="color: #46b450; font-weight: 600;">(<?php printf('%.0f%%', $discount_percentage); ?>)</span>
                        <?php endif; ?>
                    </td>
                    <td style="color: #46b450;">-<?php echo JPC_Frontend::format_price($breakup['discount']); ?></td>
                </tr>
                <tr>
                    <td><strong><?php _e('Subtotal (After Discount)', 'jewellery-price-calc'); ?></strong></td>
                    <td><strong><?php echo JPC_Frontend::format_price($breakup['subtotal']); ?></strong></td>
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
        
        <p class="jpc-breakup-note">
            <small><?php _e('* All prices are inclusive of applicable taxes', 'jewellery-price-calc'); ?></small>
        </p>
    </div>
</details>
