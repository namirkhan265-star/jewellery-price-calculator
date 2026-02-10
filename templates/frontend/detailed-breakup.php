<?php
/**
 * Detailed Price Breakup Template
 * v2.5.1: Fetches custom labels directly from settings (not from breakup data)
 */

if (!defined('ABSPATH')) {
    exit;
}

// v2.5.1 FIX: Fetch custom labels directly from WordPress settings
// This way labels update immediately without needing to regenerate breakup
$pearl_cost_label = get_option('jpc_pearl_cost_label', 'Pearl Cost');
$stone_cost_label = get_option('jpc_stone_cost_label', 'Stone Cost');
$extra_fee_label = get_option('jpc_extra_fee_label', 'Extra Fee');

// Fallback to stored labels if they exist (for backwards compatibility)
if (isset($breakup['pearl_cost_label']) && !empty($breakup['pearl_cost_label'])) {
    $pearl_cost_label = $breakup['pearl_cost_label'];
}
if (isset($breakup['stone_cost_label']) && !empty($breakup['stone_cost_label'])) {
    $stone_cost_label = $breakup['stone_cost_label'];
}
if (isset($breakup['extra_fee_label']) && !empty($breakup['extra_fee_label'])) {
    $extra_fee_label = $breakup['extra_fee_label'];
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
                
                <!-- Pearl Cost - v2.5.1 FIX - Fetches label from settings -->
                <?php if ($breakup['pearl_cost'] > 0): ?>
                <tr>
                    <td><?php echo esc_html($pearl_cost_label); ?></td>
                    <td><?php echo JPC_Frontend::format_price($breakup['pearl_cost']); ?></td>
                </tr>
                <?php endif; ?>
                
                <!-- Stone Cost - v2.5.1 FIX - Fetches label from settings -->
                <?php if ($breakup['stone_cost'] > 0): ?>
                <tr>
                    <td><?php echo esc_html($stone_cost_label); ?></td>
                    <td><?php echo JPC_Frontend::format_price($breakup['stone_cost']); ?></td>
                </tr>
                <?php endif; ?>
                
                <!-- Extra Fee - v2.5.1 FIX - Fetches label from settings -->
                <?php if ($breakup['extra_fee'] > 0): ?>
                <tr>
                    <td><?php echo esc_html($extra_fee_label); ?></td>
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
                    <td><?php _e('Discount', 'jewellery-price-calc'); ?> (<?php echo number_format($discount_percentage, 0); ?>%)</td>
                    <td class="discount-amount">-<?php echo JPC_Frontend::format_price($breakup['discount']); ?></td>
                </tr>
                <?php endif; ?>
                
                <tr class="total-row">
                    <td><strong><?php _e('Final Price', 'jewellery-price-calc'); ?></strong></td>
                    <td><strong><?php echo JPC_Frontend::format_price($breakup['subtotal']); ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</details>
