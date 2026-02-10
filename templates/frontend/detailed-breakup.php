<?php
/**
 * Detailed Price Breakup Template
 * Shows comprehensive breakdown with all calculations
 * v2.5.1 FINAL: Uses stored labels from breakup data (same as Extra Fields)
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get product ID from AJAX request
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

if (!$product_id) {
    wp_send_json_error('Invalid product ID');
    return;
}

// Get stored breakup data
$breakup = get_post_meta($product_id, '_jpc_price_breakup', true);

if (!$breakup || !is_array($breakup)) {
    wp_send_json_error('Price breakup data not found');
    return;
}

// Get metal info
$metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
$metal = JPC_Metals::get_by_id($metal_id);

if (!$metal) {
    wp_send_json_error('Invalid metal configuration');
    return;
}

// Get product data
$weight = floatval(get_post_meta($product_id, '_jpc_metal_weight', true));
$making_charge_input = floatval(get_post_meta($product_id, '_jpc_making_charge', true));
$making_charge_type = get_post_meta($product_id, '_jpc_making_charge_type', true) ?: 'percentage';
$wastage_charge_input = floatval(get_post_meta($product_id, '_jpc_wastage_charge', true));
$wastage_charge_type = get_post_meta($product_id, '_jpc_wastage_charge_type', true) ?: 'percentage';
$discount_percentage = floatval(get_post_meta($product_id, '_jpc_discount_percentage', true));

// Get prices
$regular_price = floatval(get_post_meta($product_id, '_regular_price', true));
$sale_price = floatval(get_post_meta($product_id, '_sale_price', true));
$discount_amount = isset($breakup['discount']) ? floatval($breakup['discount']) : 0;

// v2.5.1 FINAL FIX: Use stored labels from breakup data (same as Extra Fields)
$pearl_cost_label = isset($breakup['pearl_cost_label']) && !empty($breakup['pearl_cost_label']) 
    ? $breakup['pearl_cost_label'] 
    : 'Pearl Cost';

$stone_cost_label = isset($breakup['stone_cost_label']) && !empty($breakup['stone_cost_label']) 
    ? $breakup['stone_cost_label'] 
    : 'Stone Cost';

$extra_fee_label = isset($breakup['extra_fee_label']) && !empty($breakup['extra_fee_label']) 
    ? $breakup['extra_fee_label'] 
    : 'Extra Fee';
?>

<div class="jpc-detailed-breakup-modal">
    <div class="jpc-modal-header">
        <h2><?php _e('Detailed Price Breakup', 'jewellery-price-calc'); ?></h2>
        <button class="jpc-modal-close">&times;</button>
    </div>
    
    <div class="jpc-modal-body">
        <table class="jpc-detailed-table">
            <thead>
                <tr>
                    <th><?php _e('Component', 'jewellery-price-calc'); ?></th>
                    <th><?php _e('Calculation', 'jewellery-price-calc'); ?></th>
                    <th><?php _e('Amount', 'jewellery-price-calc'); ?></th>
                </tr>
            </thead>
            <tbody>
                <!-- Metal Price -->
                <tr>
                    <td><strong><?php echo esc_html($metal->display_name); ?></strong></td>
                    <td>
                        <?php echo number_format($weight, 3); ?> g × 
                        <?php echo wc_price($metal->price_per_unit); ?>/g
                    </td>
                    <td><?php echo wc_price($breakup['metal_price']); ?></td>
                </tr>
                
                <!-- Diamond Price -->
                <?php if (!empty($breakup['diamond_price']) && $breakup['diamond_price'] > 0): ?>
                <tr>
                    <td><strong><?php _e('Diamond', 'jewellery-price-calc'); ?></strong></td>
                    <td><?php _e('As per diamond specifications', 'jewellery-price-calc'); ?></td>
                    <td><?php echo wc_price($breakup['diamond_price']); ?></td>
                </tr>
                <?php endif; ?>
                
                <!-- Making Charges -->
                <?php if (!empty($breakup['making_charge']) && $breakup['making_charge'] > 0): ?>
                <tr>
                    <td><strong><?php _e('Making Charges', 'jewellery-price-calc'); ?></strong></td>
                    <td>
                        <?php if ($making_charge_type === 'percentage'): ?>
                            <?php echo number_format($making_charge_input, 2); ?>% of metal price
                        <?php else: ?>
                            <?php _e('Fixed amount', 'jewellery-price-calc'); ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo wc_price($breakup['making_charge']); ?></td>
                </tr>
                <?php endif; ?>
                
                <!-- Wastage Charge -->
                <?php if (!empty($breakup['wastage_charge']) && $breakup['wastage_charge'] > 0): ?>
                <tr>
                    <td><strong><?php _e('Wastage Charge', 'jewellery-price-calc'); ?></strong></td>
                    <td>
                        <?php if ($wastage_charge_type === 'percentage'): ?>
                            <?php echo number_format($wastage_charge_input, 2); ?>% of metal price
                        <?php else: ?>
                            <?php _e('Fixed amount', 'jewellery-price-calc'); ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo wc_price($breakup['wastage_charge']); ?></td>
                </tr>
                <?php endif; ?>
                
                <!-- Pearl Cost - v2.5.1 FINAL - Uses stored label -->
                <?php if (!empty($breakup['pearl_cost']) && $breakup['pearl_cost'] > 0): ?>
                <tr>
                    <td><strong><?php echo esc_html($pearl_cost_label); ?></strong></td>
                    <td><?php _e('Additional cost', 'jewellery-price-calc'); ?></td>
                    <td><?php echo wc_price($breakup['pearl_cost']); ?></td>
                </tr>
                <?php endif; ?>
                
                <!-- Stone Cost - v2.5.1 FINAL - Uses stored label -->
                <?php if (!empty($breakup['stone_cost']) && $breakup['stone_cost'] > 0): ?>
                <tr>
                    <td><strong><?php echo esc_html($stone_cost_label); ?></strong></td>
                    <td><?php _e('Additional cost', 'jewellery-price-calc'); ?></td>
                    <td><?php echo wc_price($breakup['stone_cost']); ?></td>
                </tr>
                <?php endif; ?>
                
                <!-- Extra Fee - v2.5.1 FINAL - Uses stored label -->
                <?php if (!empty($breakup['extra_fee']) && $breakup['extra_fee'] > 0): ?>
                <tr>
                    <td><strong><?php echo esc_html($extra_fee_label); ?></strong></td>
                    <td><?php _e('Additional cost', 'jewellery-price-calc'); ?></td>
                    <td><?php echo wc_price($breakup['extra_fee']); ?></td>
                </tr>
                <?php endif; ?>
                
                <!-- Additional Percentage -->
                <?php if (!empty($breakup['additional_percentage']) && $breakup['additional_percentage'] > 0): ?>
                <tr>
                    <td><strong><?php echo esc_html($breakup['additional_percentage_label']); ?></strong></td>
                    <td>
                        <?php echo number_format($breakup['additional_percentage_value'], 2); ?>% 
                        <?php _e('of subtotal', 'jewellery-price-calc'); ?>
                    </td>
                    <td><?php echo wc_price($breakup['additional_percentage']); ?></td>
                </tr>
                <?php endif; ?>
                
                <!-- Extra Fields (1-5) - Uses stored labels -->
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <?php if (!empty($breakup['extra_field_' . $i]) && $breakup['extra_field_' . $i] > 0): ?>
                    <tr>
                        <td><strong><?php echo esc_html($breakup['extra_field_label_' . $i]); ?></strong></td>
                        <td><?php _e('Additional cost', 'jewellery-price-calc'); ?></td>
                        <td><?php echo wc_price($breakup['extra_field_' . $i]); ?></td>
                    </tr>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <!-- Subtotal Before GST -->
                <tr class="jpc-subtotal-row">
                    <td colspan="2"><strong><?php _e('Subtotal (Before GST)', 'jewellery-price-calc'); ?></strong></td>
                    <td><strong><?php echo wc_price($breakup['subtotal_before_gst']); ?></strong></td>
                </tr>
                
                <!-- GST -->
                <?php if (!empty($breakup['gst']) && $breakup['gst'] > 0): ?>
                <tr>
                    <td><strong><?php echo esc_html($breakup['gst_label']); ?></strong></td>
                    <td><?php echo number_format($breakup['gst_percentage'], 2); ?>%</td>
                    <td><?php echo wc_price($breakup['gst']); ?></td>
                </tr>
                <?php endif; ?>
                
                <!-- Total Before Discount -->
                <tr class="jpc-total-row">
                    <td colspan="2"><strong><?php _e('Total (Before Discount)', 'jewellery-price-calc'); ?></strong></td>
                    <td><strong><?php echo wc_price($regular_price); ?></strong></td>
                </tr>
                
                <!-- Discount -->
                <?php if ($discount_amount > 0): ?>
                <tr class="jpc-discount-row">
                    <td><strong><?php _e('Discount', 'jewellery-price-calc'); ?></strong></td>
                    <td><?php echo number_format($discount_percentage, 2); ?>%</td>
                    <td class="discount-amount">-<?php echo wc_price($discount_amount); ?></td>
                </tr>
                <?php endif; ?>
                
                <!-- Final Price -->
                <tr class="jpc-final-row">
                    <td colspan="2"><strong><?php _e('FINAL PRICE', 'jewellery-price-calc'); ?></strong></td>
                    <td><strong><?php echo wc_price($sale_price); ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<style>
.jpc-detailed-breakup-modal {
    background: white;
    border-radius: 8px;
    max-width: 800px;
    margin: 0 auto;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.jpc-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 2px solid #0073aa;
}

.jpc-modal-header h2 {
    margin: 0;
    font-size: 24px;
    color: #333;
}

.jpc-modal-close {
    background: none;
    border: none;
    font-size: 32px;
    cursor: pointer;
    color: #999;
    line-height: 1;
    padding: 0;
    width: 32px;
    height: 32px;
}

.jpc-modal-close:hover {
    color: #333;
}

.jpc-modal-body {
    padding: 20px;
    max-height: 70vh;
    overflow-y: auto;
}

.jpc-detailed-table {
    width: 100%;
    border-collapse: collapse;
}

.jpc-detailed-table thead th {
    background: #0073aa;
    color: white;
    padding: 12px;
    text-align: left;
    font-weight: 600;
}

.jpc-detailed-table tbody td {
    padding: 12px;
    border-bottom: 1px solid #eee;
}

.jpc-detailed-table tbody tr:hover {
    background: #f9f9f9;
}

.jpc-detailed-table tbody td:last-child {
    text-align: right;
    font-weight: 600;
}

.jpc-subtotal-row td,
.jpc-total-row td,
.jpc-final-row td {
    background: #f0f0f0;
    font-weight: bold !important;
    font-size: 16px;
}

.jpc-final-row td {
    background: #0073aa;
    color: white;
    font-size: 18px;
}

.jpc-discount-row .discount-amount {
    color: #d63638;
}

@media (max-width: 600px) {
    .jpc-detailed-table {
        font-size: 14px;
    }
    
    .jpc-detailed-table thead th,
    .jpc-detailed-table tbody td {
        padding: 8px;
    }
}
</style>
