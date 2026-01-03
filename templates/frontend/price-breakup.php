<?php
/**
 * Frontend Price Breakup Template - COMPLETE VERSION
 * Displays all price components including extra fields
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get product ID
$product_id = get_the_ID();

// FETCH DIRECTLY FROM DATABASE - NO CALCULATIONS
$stored_regular_price = get_post_meta($product_id, '_regular_price', true);
$stored_sale_price = get_post_meta($product_id, '_sale_price', true);
$stored_discount_percentage = get_post_meta($product_id, '_jpc_discount_percentage', true);

// Convert to float
$regular_price = floatval($stored_regular_price);
$sale_price = floatval($stored_sale_price);
$discount_percentage = floatval($stored_discount_percentage);

// Calculate discount amount
$discount_amount = $regular_price - $sale_price;

// If no sale price, use regular price
if (empty($sale_price) || $sale_price <= 0) {
    $sale_price = $regular_price;
    $discount_amount = 0;
    $discount_percentage = 0;
}
?>

<div class="jpc-price-breakup">
    <h3><?php _e('PRICE BREAKUP', 'jewellery-price-calc'); ?></h3>
    
    <table class="jpc-price-breakup-table">
        <tbody>
            <!-- Metal Price -->
            <tr>
                <td><?php echo esc_html($metal->display_name); ?></td>
                <td><?php echo wc_price($breakup['metal_price']); ?></td>
            </tr>
            
            <!-- Diamond Price -->
            <?php if (!empty($breakup['diamond_price']) && $breakup['diamond_price'] > 0): ?>
            <tr>
                <td><?php _e('Diamond', 'jewellery-price-calc'); ?></td>
                <td><?php echo wc_price($breakup['diamond_price']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Making Charges -->
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
            
            <!-- Pearl Cost -->
            <?php if (!empty($breakup['pearl_cost']) && $breakup['pearl_cost'] > 0): ?>
            <tr>
                <td><?php _e('Pearl Cost', 'jewellery-price-calc'); ?></td>
                <td><?php echo wc_price($breakup['pearl_cost']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Stone Cost -->
            <?php if (!empty($breakup['stone_cost']) && $breakup['stone_cost'] > 0): ?>
            <tr>
                <td><?php _e('Stone Cost', 'jewellery-price-calc'); ?></td>
                <td><?php echo wc_price($breakup['stone_cost']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Extra Fee -->
            <?php if (!empty($breakup['extra_fee']) && $breakup['extra_fee'] > 0): ?>
            <tr>
                <td><?php _e('Extra Fee', 'jewellery-price-calc'); ?></td>
                <td><?php echo wc_price($breakup['extra_fee']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Extra Fields #1-5 (from array) -->
            <?php
            if (!empty($breakup['extra_fields']) && is_array($breakup['extra_fields'])) {
                foreach ($breakup['extra_fields'] as $extra_field) {
                    if (!empty($extra_field['value']) && $extra_field['value'] > 0) {
                        ?>
                        <tr>
                            <td><?php echo esc_html($extra_field['label']); ?></td>
                            <td><?php echo wc_price($extra_field['value']); ?></td>
                        </tr>
                        <?php
                    }
                }
            }
            ?>
            
            <!-- Additional Percentage -->
            <?php if (!empty($breakup['additional_percentage']) && $breakup['additional_percentage'] > 0): ?>
            <tr>
                <td><?php echo esc_html($breakup['additional_percentage_label'] ?? 'Additional Percentage'); ?></td>
                <td><?php echo wc_price($breakup['additional_percentage']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Discount Row -->
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
            
            <!-- GST -->
            <?php if (!empty($breakup['gst']) && $breakup['gst'] > 0): ?>
            <tr class="gst-row">
                <td><?php echo get_option('jpc_gst_label', 'GST'); ?></td>
                <td><?php echo wc_price($breakup['gst']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Separator -->
            <tr style="border-top: 2px solid #000;">
                <td colspan="2" style="padding: 5px;">&nbsp;</td>
            </tr>
            
            <!-- REGULAR PRICE - FROM DATABASE -->
            <tr class="regular-price-row">
                <td><strong><?php _e('Regular Price', 'jewellery-price-calc'); ?></strong></td>
                <td>
                    <strong style="<?php echo ($discount_percentage > 0) ? 'text-decoration: line-through; color: #999;' : ''; ?>">
                        <?php echo wc_price($regular_price); ?>
                    </strong>
                </td>
            </tr>
            
            <!-- SALE PRICE - FROM DATABASE (only if discount exists) -->
            <?php if ($discount_percentage > 0 && $discount_amount > 0): ?>
            <tr class="sale-price-row">
                <td><strong style="color: #d63638; font-size: 1.1em;"><?php _e('Sale Price', 'jewellery-price-calc'); ?></strong></td>
                <td><strong style="color: #d63638; font-size: 1.2em;"><?php echo wc_price($sale_price); ?></strong></td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Savings Badge -->
    <?php if ($discount_percentage > 0 && $discount_amount > 0): ?>
    <div class="jpc-savings-badge" style="margin-top: 15px; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; text-align: center;">
        <strong style="color: #155724; font-size: 1.1em;">
            🎉 You Save: <?php echo wc_price($discount_amount); ?> 
            (<?php echo number_format($discount_percentage, 0); ?>% OFF)
        </strong>
    </div>
    <?php endif; ?>
</div>

<!-- DEBUG INFO - ALWAYS VISIBLE -->
<div style="background: #fff3cd; padding: 15px; margin-top: 20px; border: 2px solid #ffc107; border-radius: 5px;">
    <h4 style="margin: 0 0 10px 0; color: #856404;">🔍 DEBUG INFO (Database Values)</h4>
    <table style="width: 100%; font-size: 13px;">
        <tr>
            <td><strong>Product ID:</strong></td>
            <td><?php echo $product_id; ?></td>
        </tr>
        <tr>
            <td><strong>Regular Price (DB):</strong></td>
            <td style="color: #0066cc; font-weight: bold;">₹<?php echo number_format($regular_price, 2); ?></td>
        </tr>
        <tr>
            <td><strong>Sale Price (DB):</strong></td>
            <td style="color: #d63638; font-weight: bold;">₹<?php echo number_format($sale_price, 2); ?></td>
        </tr>
        <tr>
            <td><strong>Discount %:</strong></td>
            <td><?php echo $discount_percentage; ?>%</td>
        </tr>
        <tr>
            <td><strong>Discount Amount:</strong></td>
            <td>₹<?php echo number_format($discount_amount, 2); ?></td>
        </tr>
        <tr>
            <td><strong>Extra Fields Count:</strong></td>
            <td><?php echo !empty($breakup['extra_fields']) ? count($breakup['extra_fields']) : 0; ?></td>
        </tr>
        <tr>
            <td><strong>Additional %:</strong></td>
            <td>₹<?php echo !empty($breakup['additional_percentage']) ? number_format($breakup['additional_percentage'], 2) : '0.00'; ?></td>
        </tr>
        <tr>
            <td colspan="2" style="padding-top: 10px; border-top: 1px solid #ccc; margin-top: 10px;">
                <em>These are the EXACT values stored in your database.</em>
            </td>
        </tr>
    </table>
</div>
