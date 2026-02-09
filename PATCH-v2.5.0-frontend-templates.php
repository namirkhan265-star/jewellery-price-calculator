<?php
/**
 * PATCH v2.5.0: Update frontend templates to use custom labels
 * 
 * INSTRUCTIONS FOR price-breakup.php:
 * 1. Open templates/frontend/price-breakup.php
 * 2. Find the Pearl Cost section (around line 88-93)
 * 3. Replace the three sections (Pearl Cost, Stone Cost, Extra Fee) with the code below
 */

// ===== FOR templates/frontend/price-breakup.php =====

// Pearl Cost - UPDATED v2.5.0
<?php if (!empty($breakup['pearl_cost']) && $breakup['pearl_cost'] > 0): ?>
<tr>
    <td><?php echo esc_html(get_option('jpc_pearl_cost_label', __('Pearl Cost', 'jewellery-price-calc'))); ?></td>
    <td><?php echo wc_price($breakup['pearl_cost']); ?></td>
</tr>
<?php endif; ?>

// Stone Cost - UPDATED v2.5.0
<?php if (!empty($breakup['stone_cost']) && $breakup['stone_cost'] > 0): ?>
<tr>
    <td><?php echo esc_html(get_option('jpc_stone_cost_label', __('Stone Cost', 'jewellery-price-calc'))); ?></td>
    <td><?php echo wc_price($breakup['stone_cost']); ?></td>
</tr>
<?php endif; ?>

// Extra Fee - UPDATED v2.5.0
<?php if (!empty($breakup['extra_fee']) && $breakup['extra_fee'] > 0): ?>
<tr>
    <td><?php echo esc_html(get_option('jpc_extra_fee_label', __('Extra Fee', 'jewellery-price-calc'))); ?></td>
    <td><?php echo wc_price($breakup['extra_fee']); ?></td>
</tr>
<?php endif; ?>


/**
 * INSTRUCTIONS FOR detailed-breakup.php:
 * 1. Open templates/frontend/detailed-breakup.php
 * 2. Find the Pearl Cost section (around line 60-65)
 * 3. Replace the three sections (Pearl Cost, Stone Cost, Extra Fee) with the code below
 */

// ===== FOR templates/frontend/detailed-breakup.php =====

// Pearl Cost - UPDATED v2.5.0
<?php if ($breakup['pearl_cost'] > 0): ?>
<tr>
    <td><?php echo esc_html(get_option('jpc_pearl_cost_label', __('Pearl Cost', 'jewellery-price-calc'))); ?></td>
    <td><?php echo JPC_Frontend::format_price($breakup['pearl_cost']); ?></td>
</tr>
<?php endif; ?>

// Stone Cost - UPDATED v2.5.0
<?php if ($breakup['stone_cost'] > 0): ?>
<tr>
    <td><?php echo esc_html(get_option('jpc_stone_cost_label', __('Stone Cost', 'jewellery-price-calc'))); ?></td>
    <td><?php echo JPC_Frontend::format_price($breakup['stone_cost']); ?></td>
</tr>
<?php endif; ?>

// Extra Fee - UPDATED v2.5.0
<?php if ($breakup['extra_fee'] > 0): ?>
<tr>
    <td><?php echo esc_html(get_option('jpc_extra_fee_label', __('Extra Fee', 'jewellery-price-calc'))); ?></td>
    <td><?php echo JPC_Frontend::format_price($breakup['extra_fee']); ?></td>
</tr>
<?php endif; ?>
