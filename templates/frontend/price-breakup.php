<?php
/**
 * Frontend Price Breakup Template - USES ONLY STORED BREAKUP DATA
 * NO CALCULATIONS - DISPLAYS STORED DATA ONLY
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get product ID
$product_id = get_the_ID();

// CRITICAL: Fetch ONLY stored breakup data - NO CALCULATIONS!
$breakup = get_post_meta($product_id, '_jpc_price_breakup', true);

// Validate breakup data exists
if (!$breakup || !is_array($breakup)) {
    echo '<div style="padding: 20px; background: #fff3cd; border: 2px solid #ffc107; border-radius: 5px;">';
    echo '<p style="color: #856404; font-weight: bold;">⚠️ Price breakup data not found!</p>';
    echo '<p>Please go to the product editor and click "Regenerate Price Breakup" button.</p>';
    echo '</div>';
    return;
}

// Get stored prices from WooCommerce
$regular_price = floatval(get_post_meta($product_id, '_regular_price', true));
$sale_price = floatval(get_post_meta($product_id, '_sale_price', true));
$discount_percentage = floatval(get_post_meta($product_id, '_jpc_discount_percentage', true));

// CRITICAL: Use stored discount from breakup, NOT calculated
$discount_amount = isset($breakup['discount']) ? floatval($breakup['discount']) : 0;

// Fallback if no sale price
if (empty($sale_price) || $sale_price <= 0) {
    $sale_price = $regular_price;
}

// Get metal info
$metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
$metal = JPC_Metals::get_by_id($metal_id);

if (!$metal) {
    echo '<p>' . __('Invalid metal configuration.', 'jewellery-price-calc') . '</p>';
    return;
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
            
            <!-- Discount Row - USES STORED DISCOUNT FROM BREAKUP -->
            <?php if ($discount_percentage > 0 && $discount_amount > 0): ?>
            <tr class="discount-row" style="color: #d63638;">
                <td>
                    <?php _e('Discount', 'jewellery-price-calc'); ?>
                    <span style="color: #d63638; font-weight: bold;">
                        (<?php echo number_format($discount_percentage, 1); ?>% OFF)
                    </span>
                </td>
                <td style="color: #d63638; font-weight: bold;">
                    - <?php echo wc_price($discount_amount); ?>
                </td>
            </tr>
            <?php endif; ?>
            
            <!-- GST - USES STORED GST FROM BREAKUP -->
            <?php 
            $gst_value = isset($breakup['gst']) ? floatval($breakup['gst']) : 0;
            $gst_label = isset($breakup['gst_label']) ? $breakup['gst_label'] : get_option('jpc_gst_label', 'GST');
            $gst_percentage = isset($breakup['gst_percentage']) ? $breakup['gst_percentage'] : 0;
            
            if ($gst_value > 0): 
            ?>
            <tr class="gst-row">
                <td><?php echo esc_html($gst_label) . ' (' . number_format($gst_percentage, 2) . '%)'; ?></td>
                <td><?php echo wc_price($gst_value); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Separator -->
            <tr style="border-top: 2px solid #000;">
                <td colspan="2" style="padding: 5px;">&nbsp;</td>
            </tr>
            
            <!-- REGULAR PRICE - FROM DATABASE -->
            <tr class="regular-price-row">
                <td><strong><?php _e('Price Before Discount', 'jewellery-price-calc'); ?></strong></td>
                <td>
                    <strong style="<?php echo ($discount_percentage > 0) ? 'text-decoration: line-through; color: #999;' : 'color: #0066cc;'; ?>">
                        <?php echo wc_price($regular_price); ?>
                    </strong>
                </td>
            </tr>
            
            <!-- SALE PRICE - FROM DATABASE (only if discount exists) -->
            <?php if ($discount_percentage > 0 && $discount_amount > 0): ?>
            <tr class="sale-price-row">
                <td><strong style="color: #d63638; font-size: 1.1em;"><?php _e('Price After Discount', 'jewellery-price-calc'); ?></strong></td>
                <td><strong style="color: #d63638; font-size: 1.2em;"><?php echo wc_price($sale_price); ?></strong></td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Savings Badge - USES STORED DISCOUNT -->
    <?php if ($discount_percentage > 0 && $discount_amount > 0): ?>
    <div class="jpc-savings-badge" style="margin-top: 15px; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; text-align: center;">
        <strong style="color: #155724; font-size: 1.1em;">
            🎉 You Save: <?php echo wc_price($discount_amount); ?> 
            (<?php echo number_format($discount_percentage, 0); ?>% OFF)
        </strong>
    </div>
    <?php endif; ?>
</div>

<!-- DEBUG INFO - SHOWS STORED VALUES -->
<div style="background: #fff3cd; padding: 15px; margin-top: 20px; border: 2px solid #ffc107; border-radius: 5px;">
    <h4 style="margin: 0 0 10px 0; color: #856404;">🔍 DEBUG INFO (Stored Values)</h4>
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
            <td><strong>Discount % (DB):</strong></td>
            <td><?php echo $discount_percentage; ?>%</td>
        </tr>
        <tr>
            <td><strong>Discount Amount (BREAKUP):</strong></td>
            <td style="color: #d63638; font-weight: bold;">₹<?php echo number_format($discount_amount, 2); ?></td>
        </tr>
        <tr>
            <td><strong>GST (BREAKUP):</strong></td>
            <td>₹<?php echo number_format($gst_value, 2); ?></td>
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
                <em style="color: #28a745;">✓ All values from stored breakup data - NO calculations!</em>
            </td>
        </tr>
    </table>
</div>
