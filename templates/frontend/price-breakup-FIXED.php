<?php
/**
 * Frontend Price Breakup Template - FIXED VERSION v2.5.12
 * FIXES:
 * - GST label and percentage fetched dynamically from settings
 * - Diamond price always shows if exists in breakup
 * - Percentage shows as integer (3% not 3.00%)
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get product ID
$product_id = get_the_ID();

// Fetch ONLY stored breakup data
$breakup = get_post_meta($product_id, '_jpc_price_breakup', true);

// Validate breakup data exists
if (!$breakup || !is_array($breakup)) {
    echo '<div style="padding: 20px; background: #fff3cd; border: 2px solid #ffc107; border-radius: 5px;">';
    echo '<p style="color: #856404; font-weight: bold;">⚠️ Price breakup data not found!</p>';
    echo '<p>Please go to the product editor and click "Update" to regenerate price breakup.</p>';
    echo '</div>';
    return;
}

// Get stored prices from WooCommerce
$regular_price = floatval(get_post_meta($product_id, '_regular_price', true));
$sale_price = floatval(get_post_meta($product_id, '_sale_price', true));
$discount_percentage = floatval(get_post_meta($product_id, '_jpc_discount_percentage', true));

// Use stored discount from breakup
$discount_amount = isset($breakup['discount']) ? floatval($breakup['discount']) : 0;

// Fallback if no sale price
if (empty($sale_price) || $sale_price <= 0) {
    $sale_price = $regular_price;
}

// CRITICAL: Fetch labels DYNAMICALLY from settings (not from breakup)
$pearl_cost_label = get_option('jpc_pearl_cost_label', 'Pearl Cost');
$stone_cost_label = get_option('jpc_stone_cost_label', 'Stone Cost');
$extra_fee_label = get_option('jpc_extra_fee_label', 'Extra Fee');

// CRITICAL FIX: ALWAYS fetch GST settings DYNAMICALLY from options
$gst_label = get_option('jpc_gst_label', 'GST');
$enable_gst = get_option('jpc_enable_gst', 'yes');
$gst_percentage = 0;

if ($enable_gst === 'yes') {
    $gst_percentage = floatval(get_option('jpc_gst_value', 3));
}
?>

<div class="jpc-price-breakup">
    <h3><?php _e('PRICE BREAKUP', 'jewellery-price-calc'); ?></h3>
    
    <table class="jpc-price-breakup-table">
        <tbody>
            <!-- Metal Price - ALWAYS SHOW -->
            <?php if (isset($breakup['metal_price']) && $breakup['metal_price'] > 0): ?>
            <tr>
                <td><?php _e('Gold', 'jewellery-price-calc'); ?></td>
                <td><?php echo wc_price($breakup['metal_price']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Diamond Price - CRITICAL FIX: Always show if exists -->
            <?php if (isset($breakup['diamond_price']) && $breakup['diamond_price'] > 0): ?>
            <tr>
                <td><?php _e('Diamond', 'jewellery-price-calc'); ?></td>
                <td><?php echo wc_price($breakup['diamond_price']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Making Charge -->
            <?php if (!empty($breakup['making_charge']) && $breakup['making_charge'] > 0): ?>
            <tr>
                <td><?php _e('Making Charges', 'jewellery-price-calc'); ?></td>
                <td><?php echo wc_price($breakup['making_charge']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Wastage Charge -->
            <?php if (!empty($breakup['wastage_charge']) && $breakup['wastage_charge'] > 0): ?>
            <tr>
                <td><?php _e('Wastage Charge', 'jewellery-price-calc'); ?></td>
                <td><?php echo wc_price($breakup['wastage_charge']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Pearl Cost - Uses label from settings -->
            <?php if (!empty($breakup['pearl_cost']) && $breakup['pearl_cost'] > 0): ?>
            <tr>
                <td><?php echo esc_html($pearl_cost_label); ?></td>
                <td><?php echo wc_price($breakup['pearl_cost']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Stone Cost - Uses label from settings -->
            <?php if (!empty($breakup['stone_cost']) && $breakup['stone_cost'] > 0): ?>
            <tr>
                <td><?php echo esc_html($stone_cost_label); ?></td>
                <td><?php echo wc_price($breakup['stone_cost']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Extra Fee - Uses label from settings -->
            <?php if (!empty($breakup['extra_fee']) && $breakup['extra_fee'] > 0): ?>
            <tr>
                <td><?php echo esc_html($extra_fee_label); ?></td>
                <td><?php echo wc_price($breakup['extra_fee']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Extra Fields (1-5) - Fetch labels from settings -->
            <?php 
            for ($i = 1; $i <= 5; $i++) {
                $field_key = 'extra_field_' . $i;
                if (!empty($breakup[$field_key]) && $breakup[$field_key] > 0) {
                    $field_label = get_option('jpc_extra_field_label_' . $i, 'Extra Field #' . $i);
                    echo '<tr>';
                    echo '<td>' . esc_html($field_label) . '</td>';
                    echo '<td>' . wc_price($breakup[$field_key]) . '</td>';
                    echo '</tr>';
                }
            }
            ?>
            
            <!-- Additional Percentage -->
            <?php if (!empty($breakup['additional_percentage']) && $breakup['additional_percentage'] > 0): ?>
            <tr>
                <td><?php echo esc_html($breakup['additional_percentage_label']); ?></td>
                <td><?php echo wc_price($breakup['additional_percentage']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Processing Fee -->
            <?php if (!empty($breakup['processing_fee']) && $breakup['processing_fee'] > 0): ?>
            <tr>
                <td><?php _e('Processing Fee', 'jewellery-price-calc'); ?></td>
                <td><?php echo wc_price($breakup['processing_fee']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Discount -->
            <?php if ($discount_amount > 0): ?>
            <tr class="jpc-discount">
                <td><?php printf(__('Discount (%s%% OFF)', 'jewellery-price-calc'), number_format($discount_percentage, 0)); ?></td>
                <td>- <?php echo wc_price($discount_amount); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- GST - CRITICAL FIX: Fetch label and percentage DYNAMICALLY -->
            <?php if (!empty($breakup['gst']) && $breakup['gst'] > 0): ?>
            <tr>
                <td>
                    <?php 
                    // Show GST with percentage as integer (3% not 3.00%)
                    if ($gst_percentage > 0) {
                        // Remove decimals if it's a whole number
                        $gst_display = (floor($gst_percentage) == $gst_percentage) 
                            ? number_format($gst_percentage, 0) 
                            : number_format($gst_percentage, 2);
                        printf('%s (%s%%)', esc_html($gst_label), $gst_display);
                    } else {
                        echo esc_html($gst_label);
                    }
                    ?>
                </td>
                <td><?php echo wc_price($breakup['gst']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Separator before final prices -->
            <tr class="jpc-separator">
                <td colspan="2"></td>
            </tr>
            
            <!-- Regular Price (if discount exists) -->
            <?php if ($discount_amount > 0): ?>
            <tr class="jpc-regular-price">
                <td><?php _e('Regular Price', 'jewellery-price-calc'); ?></td>
                <td><del><?php echo wc_price($regular_price); ?></del></td>
            </tr>
            <?php endif; ?>
            
            <!-- Sale Price (Final Price) -->
            <tr class="jpc-final-price">
                <td><strong><?php _e('Sale Price', 'jewellery-price-calc'); ?></strong></td>
                <td><strong><?php echo wc_price($sale_price); ?></strong></td>
            </tr>
            
            <!-- You Save (if discount exists) -->
            <?php if ($discount_amount > 0): ?>
            <tr class="jpc-savings">
                <td colspan="2">
                    <div class="jpc-savings-badge">
                        🎉 <?php printf(__('You Save: %s (%s%% OFF)', 'jewellery-price-calc'), 
                            wc_price($discount_amount), 
                            number_format($discount_percentage, 0)); ?>
                    </div>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
.jpc-price-breakup {
    margin: 20px 0;
    padding: 20px;
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.jpc-price-breakup h3 {
    margin: 0 0 15px 0;
    padding-bottom: 10px;
    border-bottom: 2px solid #333;
    font-size: 18px;
    font-weight: bold;
}

.jpc-price-breakup-table {
    width: 100%;
    border-collapse: collapse;
}

.jpc-price-breakup-table tr {
    border-bottom: 1px solid #eee;
}

.jpc-price-breakup-table td {
    padding: 12px 8px;
    font-size: 16px;
}

.jpc-price-breakup-table td:first-child {
    color: #666;
}

.jpc-price-breakup-table td:last-child {
    text-align: right;
    font-weight: 500;
}

.jpc-price-breakup-table tr.jpc-discount td {
    color: #28a745;
    font-weight: 600;
}

.jpc-price-breakup-table tr.jpc-separator td {
    padding: 5px;
    border-bottom: 2px solid #333;
}

.jpc-price-breakup-table tr.jpc-regular-price td {
    color: #999;
}

.jpc-price-breakup-table tr.jpc-final-price {
    background: #f0f0f0;
    border-top: 2px solid #333;
}

.jpc-price-breakup-table tr.jpc-final-price td {
    padding: 15px 8px;
    font-size: 18px;
    color: #d63384;
}

.jpc-price-breakup-table tr.jpc-savings {
    border-bottom: none;
}

.jpc-savings-badge {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
    padding: 12px 20px;
    border-radius: 5px;
    text-align: center;
    font-weight: bold;
    font-size: 16px;
    border: 2px solid #28a745;
}

@media (max-width: 768px) {
    .jpc-price-breakup {
        padding: 15px;
    }
    
    .jpc-price-breakup-table td {
        padding: 10px 5px;
        font-size: 14px;
    }
    
    .jpc-price-breakup-table tr.jpc-final-price td {
        font-size: 16px;
    }
}
</style>
