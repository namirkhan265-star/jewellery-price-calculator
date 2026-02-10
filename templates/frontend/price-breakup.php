<?php
/**
 * Frontend Price Breakup Template - USES ONLY STORED BREAKUP DATA
 * NO CALCULATIONS - DISPLAYS STORED DATA ONLY
 * v2.5.2: Fetch labels directly from settings (simple solution)
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
    echo '<p>Please go to the product editor and click \"Regenerate Price Breakup\" button.</p>';
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

// v2.5.2 SIMPLE FIX: Fetch labels directly from settings (same as how Extra Fields work)
$pearl_cost_label = get_option('jpc_pearl_cost_label', 'Pearl Cost');
$stone_cost_label = get_option('jpc_stone_cost_label', 'Stone Cost');
$extra_fee_label = get_option('jpc_extra_fee_label', 'Extra Fee');
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
            
            <!-- Making Charge -->
            <?php if (!empty($breakup['making_charge']) && $breakup['making_charge'] > 0): ?>
            <tr>
                <td><?php _e('Making Charge', 'jewellery-price-calc'); ?></td>
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
            
            <!-- Pearl Cost - v2.5.2 - Uses label from settings -->
            <?php if (!empty($breakup['pearl_cost']) && $breakup['pearl_cost'] > 0): ?>
            <tr>
                <td><?php echo esc_html($pearl_cost_label); ?></td>
                <td><?php echo wc_price($breakup['pearl_cost']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Stone Cost - v2.5.2 - Uses label from settings -->
            <?php if (!empty($breakup['stone_cost']) && $breakup['stone_cost'] > 0): ?>
            <tr>
                <td><?php echo esc_html($stone_cost_label); ?></td>
                <td><?php echo wc_price($breakup['stone_cost']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Extra Fee - v2.5.2 - Uses label from settings -->
            <?php if (!empty($breakup['extra_fee']) && $breakup['extra_fee'] > 0): ?>
            <tr>
                <td><?php echo esc_html($extra_fee_label); ?></td>
                <td><?php echo wc_price($breakup['extra_fee']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Additional Percentage -->
            <?php if (!empty($breakup['additional_percentage']) && $breakup['additional_percentage'] > 0): ?>
            <tr>
                <td><?php echo esc_html($breakup['additional_percentage_label']); ?></td>
                <td><?php echo wc_price($breakup['additional_percentage']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Extra Fields (1-5) - Fetch labels from settings like pearl/stone/extra -->
            <?php 
            // Check if extra_fields is stored as nested array (old format)
            if (isset($breakup['extra_fields']) && is_array($breakup['extra_fields'])) {
                // Old format: nested array
                foreach ($breakup['extra_fields'] as $field) {
                    if (!empty($field['value']) && $field['value'] > 0) {
                        echo '<tr>';
                        echo '<td>' . esc_html($field['label']) . '</td>';
                        echo '<td>' . wc_price($field['value']) . '</td>';
                        echo '</tr>';
                    }
                }
            } else {
                // New format: flat keys - fetch labels from settings
                for ($i = 1; $i <= 5; $i++) {
                    $field_key = 'extra_field_' . $i;
                    if (!empty($breakup[$field_key]) && $breakup[$field_key] > 0) {
                        // Fetch label from settings
                        $field_label = get_option('jpc_extra_field_label_' . $i, 'Extra Field #' . $i);
                        echo '<tr>';
                        echo '<td>' . esc_html($field_label) . '</td>';
                        echo '<td>' . wc_price($breakup[$field_key]) . '</td>';
                        echo '</tr>';
                    }
                }
            }
            ?>
            
            <!-- Subtotal (Before GST) -->
            <?php if (isset($breakup['subtotal_before_gst'])): ?>
            <tr class="jpc-subtotal">
                <td><strong><?php _e('Subtotal (Before GST)', 'jewellery-price-calc'); ?></strong></td>
                <td><strong><?php echo wc_price($breakup['subtotal_before_gst']); ?></strong></td>
            </tr>
            <?php endif; ?>
            
            <!-- GST -->
            <?php if (!empty($breakup['gst']) && $breakup['gst'] > 0): ?>
            <tr>
                <td><?php echo esc_html($breakup['gst_label']); ?></td>
                <td><?php echo wc_price($breakup['gst']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Total (Before Discount) -->
            <?php if ($discount_amount > 0): ?>
            <tr class="jpc-subtotal">
                <td><strong><?php _e('Total (Before Discount)', 'jewellery-price-calc'); ?></strong></td>
                <td><strong><?php echo wc_price($regular_price); ?></strong></td>
            </tr>
            
            <!-- Discount -->
            <tr class="jpc-discount">
                <td><?php printf(__('Discount (%s%%)', 'jewellery-price-calc'), number_format($discount_percentage, 2)); ?></td>
                <td>-<?php echo wc_price($discount_amount); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Final Price -->
            <tr class="jpc-final-price">
                <td><strong><?php _e('FINAL PRICE', 'jewellery-price-calc'); ?></strong></td>
                <td><strong><?php echo wc_price($sale_price); ?></strong></td>
            </tr>
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
    padding: 10px 5px;
}

.jpc-price-breakup-table td:first-child {
    text-align: left;
    font-weight: 500;
}

.jpc-price-breakup-table td:last-child {
    text-align: right;
    font-weight: 600;
}

.jpc-price-breakup-table tr.jpc-subtotal td,
.jpc-price-breakup-table tr.jpc-final-price td {
    padding-top: 15px;
    font-size: 16px;
}

.jpc-price-breakup-table tr.jpc-final-price {
    border-top: 2px solid #333;
    border-bottom: 2px solid #333;
}

.jpc-price-breakup-table tr.jpc-final-price td {
    font-size: 18px;
    color: #2c3e50;
}

.jpc-price-breakup-table tr.jpc-discount td {
    color: #27ae60;
}
</style>
