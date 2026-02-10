<?php
/**
 * Frontend Price Breakup Template - USES ONLY STORED BREAKUP DATA
 * NO CALCULATIONS - DISPLAYS STORED DATA ONLY
 * v2.5.1 FINAL: Uses stored labels from breakup data (same as Extra Fields)
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

// v2.5.1 FINAL FIX: Use stored labels from breakup data (same as Extra Fields)
// Fallback to default labels if not found in breakup
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
            
            <!-- Pearl Cost - v2.5.1 FINAL - Uses stored label from breakup -->
            <?php if (!empty($breakup['pearl_cost']) && $breakup['pearl_cost'] > 0): ?>
            <tr>
                <td><?php echo esc_html($pearl_cost_label); ?></td>
                <td><?php echo wc_price($breakup['pearl_cost']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Stone Cost - v2.5.1 FINAL - Uses stored label from breakup -->
            <?php if (!empty($breakup['stone_cost']) && $breakup['stone_cost'] > 0): ?>
            <tr>
                <td><?php echo esc_html($stone_cost_label); ?></td>
                <td><?php echo wc_price($breakup['stone_cost']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Extra Fee - v2.5.1 FINAL - Uses stored label from breakup -->
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
            
            <!-- Extra Fields (1-5) - Uses stored labels from breakup -->
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <?php if (!empty($breakup['extra_field_' . $i]) && $breakup['extra_field_' . $i] > 0): ?>
                <tr>
                    <td><?php echo esc_html($breakup['extra_field_label_' . $i]); ?></td>
                    <td><?php echo wc_price($breakup['extra_field_' . $i]); ?></td>
                </tr>
                <?php endif; ?>
            <?php endfor; ?>
            
            <!-- Subtotal (Before GST) -->
            <tr class="jpc-subtotal">
                <td><strong><?php _e('Subtotal (Before GST)', 'jewellery-price-calc'); ?></strong></td>
                <td><strong><?php echo wc_price($breakup['subtotal_before_gst']); ?></strong></td>
            </tr>
            
            <!-- GST -->
            <?php if (!empty($breakup['gst']) && $breakup['gst'] > 0): ?>
            <tr>
                <td><?php echo esc_html($breakup['gst_label']); ?></td>
                <td><?php echo wc_price($breakup['gst']); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Total (Before Discount) -->
            <tr class="jpc-total-before-discount">
                <td><strong><?php _e('Total (Before Discount)', 'jewellery-price-calc'); ?></strong></td>
                <td><strong><?php echo wc_price($regular_price); ?></strong></td>
            </tr>
            
            <!-- Discount -->
            <?php if ($discount_amount > 0): ?>
            <tr class="jpc-discount">
                <td><?php _e('Discount', 'jewellery-price-calc'); ?> 
                    <?php if ($discount_percentage > 0): ?>
                        (<?php echo number_format($discount_percentage, 2); ?>%)
                    <?php endif; ?>
                </td>
                <td class="discount-amount">-<?php echo wc_price($discount_amount); ?></td>
            </tr>
            <?php endif; ?>
            
            <!-- Final Price -->
            <tr class="jpc-final-price">
                <td><strong><?php _e('Final Price', 'jewellery-price-calc'); ?></strong></td>
                <td><strong><?php echo wc_price($sale_price); ?></strong></td>
            </tr>
        </tbody>
    </table>
    
    <!-- View Detailed Breakup Link -->
    <div class="jpc-detailed-breakup-link">
        <a href="#" class="jpc-view-detailed-breakup" data-product-id="<?php echo esc_attr($product_id); ?>">
            <?php _e('View Detailed Breakup', 'jewellery-price-calc'); ?>
        </a>
    </div>
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
    margin-top: 0;
    margin-bottom: 15px;
    font-size: 18px;
    font-weight: bold;
    color: #333;
    border-bottom: 2px solid #0073aa;
    padding-bottom: 10px;
}

.jpc-price-breakup-table {
    width: 100%;
    border-collapse: collapse;
}

.jpc-price-breakup-table td {
    padding: 10px;
    border-bottom: 1px solid #eee;
}

.jpc-price-breakup-table tr:last-child td {
    border-bottom: none;
}

.jpc-price-breakup-table td:first-child {
    font-weight: 500;
    color: #555;
}

.jpc-price-breakup-table td:last-child {
    text-align: right;
    font-weight: 600;
    color: #333;
}

.jpc-subtotal td,
.jpc-total-before-discount td,
.jpc-final-price td {
    background: #f0f0f0;
    font-weight: bold !important;
}

.jpc-discount td {
    color: #d63638;
}

.jpc-discount .discount-amount {
    color: #d63638;
}

.jpc-detailed-breakup-link {
    margin-top: 15px;
    text-align: center;
}

.jpc-view-detailed-breakup {
    display: inline-block;
    padding: 10px 20px;
    background: #0073aa;
    color: white;
    text-decoration: none;
    border-radius: 3px;
    font-weight: 500;
    transition: background 0.3s;
}

.jpc-view-detailed-breakup:hover {
    background: #005a87;
    color: white;
}

/* Responsive */
@media (max-width: 600px) {
    .jpc-price-breakup {
        padding: 15px;
    }
    
    .jpc-price-breakup-table td {
        padding: 8px 5px;
        font-size: 14px;
    }
}
</style>
