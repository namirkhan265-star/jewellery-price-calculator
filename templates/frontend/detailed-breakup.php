<?php
/**
 * Detailed Price Breakup Template
 * Shows comprehensive breakdown with all calculations
 * v2.5.2: Fetch labels directly from settings (simple solution)
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

// v2.5.2 SIMPLE FIX: Fetch labels directly from settings (same as how Extra Fields work)
$pearl_cost_label = get_option('jpc_pearl_cost_label', 'Pearl Cost');
$stone_cost_label = get_option('jpc_stone_cost_label', 'Stone Cost');
$extra_fee_label = get_option('jpc_extra_fee_label', 'Extra Fee');
?>

<div class="jpc-detailed-breakup">
    <h2><?php _e('Detailed Price Breakup', 'jewellery-price-calc'); ?></h2>
    
    <!-- Metal Calculation -->
    <div class="jpc-section">
        <h3><?php _e('Metal Calculation', 'jewellery-price-calc'); ?></h3>
        <table>
            <tr>
                <td><?php _e('Metal Type', 'jewellery-price-calc'); ?>:</td>
                <td><?php echo esc_html($metal->display_name); ?></td>
            </tr>
            <tr>
                <td><?php _e('Weight', 'jewellery-price-calc'); ?>:</td>
                <td><?php echo number_format($weight, 3); ?> g</td>
            </tr>
            <tr>
                <td><?php _e('Price per gram', 'jewellery-price-calc'); ?>:</td>
                <td><?php echo wc_price($metal->price_per_unit); ?></td>
            </tr>
            <tr class="total-row">
                <td><strong><?php _e('Metal Price', 'jewellery-price-calc'); ?>:</strong></td>
                <td><strong><?php echo wc_price($breakup['metal_price']); ?></strong></td>
            </tr>
        </table>
    </div>
    
    <!-- Diamond Calculation -->
    <?php if (!empty($breakup['diamond_price']) && $breakup['diamond_price'] > 0): ?>
    <?php
    $diamond_id = get_post_meta($product_id, '_jpc_diamond_id', true);
    $diamond_quantity = intval(get_post_meta($product_id, '_jpc_diamond_quantity', true));
    $diamond = JPC_Diamonds::get_by_id($diamond_id);
    ?>
    <div class="jpc-section">
        <h3><?php _e('Diamond Calculation', 'jewellery-price-calc'); ?></h3>
        <table>
            <tr>
                <td><?php _e('Diamond Type', 'jewellery-price-calc'); ?>:</td>
                <td><?php echo esc_html($diamond->display_name); ?></td>
            </tr>
            <tr>
                <td><?php _e('Carat', 'jewellery-price-calc'); ?>:</td>
                <td><?php echo number_format($diamond->carat, 3); ?></td>
            </tr>
            <tr>
                <td><?php _e('Price per carat', 'jewellery-price-calc'); ?>:</td>
                <td><?php echo wc_price($diamond->price_per_carat); ?></td>
            </tr>
            <tr>
                <td><?php _e('Quantity', 'jewellery-price-calc'); ?>:</td>
                <td><?php echo $diamond_quantity; ?></td>
            </tr>
            <tr class="total-row">
                <td><strong><?php _e('Diamond Price', 'jewellery-price-calc'); ?>:</strong></td>
                <td><strong><?php echo wc_price($breakup['diamond_price']); ?></strong></td>
            </tr>
        </table>
    </div>
    <?php endif; ?>
    
    <!-- Making Charge -->
    <?php if (!empty($breakup['making_charge']) && $breakup['making_charge'] > 0): ?>
    <div class="jpc-section">
        <h3><?php _e('Making Charge', 'jewellery-price-calc'); ?></h3>
        <table>
            <tr>
                <td><?php _e('Type', 'jewellery-price-calc'); ?>:</td>
                <td><?php echo $making_charge_type === 'percentage' ? __('Percentage', 'jewellery-price-calc') : __('Fixed Amount', 'jewellery-price-calc'); ?></td>
            </tr>
            <tr>
                <td><?php _e('Value', 'jewellery-price-calc'); ?>:</td>
                <td><?php echo $making_charge_type === 'percentage' ? number_format($making_charge_input, 2) . '%' : wc_price($making_charge_input); ?></td>
            </tr>
            <?php if ($making_charge_type === 'percentage'): ?>
            <tr>
                <td><?php _e('Base Amount', 'jewellery-price-calc'); ?>:</td>
                <td><?php echo wc_price($breakup['metal_price']); ?></td>
            </tr>
            <?php endif; ?>
            <tr class="total-row">
                <td><strong><?php _e('Making Charge', 'jewellery-price-calc'); ?>:</strong></td>
                <td><strong><?php echo wc_price($breakup['making_charge']); ?></strong></td>
            </tr>
        </table>
    </div>
    <?php endif; ?>
    
    <!-- Wastage Charge -->
    <?php if (!empty($breakup['wastage_charge']) && $breakup['wastage_charge'] > 0): ?>
    <div class="jpc-section">
        <h3><?php _e('Wastage Charge', 'jewellery-price-calc'); ?></h3>
        <table>
            <tr>
                <td><?php _e('Type', 'jewellery-price-calc'); ?>:</td>
                <td><?php echo $wastage_charge_type === 'percentage' ? __('Percentage', 'jewellery-price-calc') : __('Fixed Amount', 'jewellery-price-calc'); ?></td>
            </tr>
            <tr>
                <td><?php _e('Value', 'jewellery-price-calc'); ?>:</td>
                <td><?php echo $wastage_charge_type === 'percentage' ? number_format($wastage_charge_input, 2) . '%' : wc_price($wastage_charge_input); ?></td>
            </tr>
            <?php if ($wastage_charge_type === 'percentage'): ?>
            <tr>
                <td><?php _e('Base Amount', 'jewellery-price-calc'); ?>:</td>
                <td><?php echo wc_price($breakup['metal_price']); ?></td>
            </tr>
            <?php endif; ?>
            <tr class="total-row">
                <td><strong><?php _e('Wastage Charge', 'jewellery-price-calc'); ?>:</strong></td>
                <td><strong><?php echo wc_price($breakup['wastage_charge']); ?></strong></td>
            </tr>
        </table>
    </div>
    <?php endif; ?>
    
    <!-- Pearl Cost - v2.5.2 - Uses label from settings -->
    <?php if (!empty($breakup['pearl_cost']) && $breakup['pearl_cost'] > 0): ?>
    <div class="jpc-section">
        <h3><?php echo esc_html($pearl_cost_label); ?></h3>
        <table>
            <tr class="total-row">
                <td><strong><?php echo esc_html($pearl_cost_label); ?>:</strong></td>
                <td><strong><?php echo wc_price($breakup['pearl_cost']); ?></strong></td>
            </tr>
        </table>
    </div>
    <?php endif; ?>
    
    <!-- Stone Cost - v2.5.2 - Uses label from settings -->
    <?php if (!empty($breakup['stone_cost']) && $breakup['stone_cost'] > 0): ?>
    <div class="jpc-section">
        <h3><?php echo esc_html($stone_cost_label); ?></h3>
        <table>
            <tr class="total-row">
                <td><strong><?php echo esc_html($stone_cost_label); ?>:</strong></td>
                <td><strong><?php echo wc_price($breakup['stone_cost']); ?></strong></td>
            </tr>
        </table>
    </div>
    <?php endif; ?>
    
    <!-- Extra Fee - v2.5.2 - Uses label from settings -->
    <?php if (!empty($breakup['extra_fee']) && $breakup['extra_fee'] > 0): ?>
    <div class="jpc-section">
        <h3><?php echo esc_html($extra_fee_label); ?></h3>
        <table>
            <tr class="total-row">
                <td><strong><?php echo esc_html($extra_fee_label); ?>:</strong></td>
                <td><strong><?php echo wc_price($breakup['extra_fee']); ?></strong></td>
            </tr>
        </table>
    </div>
    <?php endif; ?>
    
    <!-- Extra Fields - Fetch labels from settings -->
    <?php 
    // Check if extra_fields is stored as nested array (old format)
    if (isset($breakup['extra_fields']) && is_array($breakup['extra_fields'])) {
        // Old format: nested array
        foreach ($breakup['extra_fields'] as $field) {
            if (!empty($field['value']) && $field['value'] > 0) {
                ?>
                <div class="jpc-section">
                    <h3><?php echo esc_html($field['label']); ?></h3>
                    <table>
                        <tr class="total-row">
                            <td><strong><?php echo esc_html($field['label']); ?>:</strong></td>
                            <td><strong><?php echo wc_price($field['value']); ?></strong></td>
                        </tr>
                    </table>
                </div>
                <?php
            }
        }
    } else {
        // New format: flat keys - fetch labels from settings
        for ($i = 1; $i <= 5; $i++) {
            $field_key = 'extra_field_' . $i;
            if (!empty($breakup[$field_key]) && $breakup[$field_key] > 0) {
                // Fetch label from settings
                $field_label = get_option('jpc_extra_field_label_' . $i, 'Extra Field #' . $i);
                ?>
                <div class="jpc-section">
                    <h3><?php echo esc_html($field_label); ?></h3>
                    <table>
                        <tr class="total-row">
                            <td><strong><?php echo esc_html($field_label); ?>:</strong></td>
                            <td><strong><?php echo wc_price($breakup[$field_key]); ?></strong></td>
                        </tr>
                    </table>
                </div>
                <?php
            }
        }
    }
    ?>
    
    <!-- Additional Percentage -->
    <?php if (!empty($breakup['additional_percentage']) && $breakup['additional_percentage'] > 0): ?>
    <div class="jpc-section">
        <h3><?php echo esc_html($breakup['additional_percentage_label']); ?></h3>
        <table>
            <tr>
                <td><?php _e('Percentage', 'jewellery-price-calc'); ?>:</td>
                <td><?php echo number_format($breakup['additional_percentage_value'], 2); ?>%</td>
            </tr>
            <tr class="total-row">
                <td><strong><?php echo esc_html($breakup['additional_percentage_label']); ?>:</strong></td>
                <td><strong><?php echo wc_price($breakup['additional_percentage']); ?></strong></td>
            </tr>
        </table>
    </div>
    <?php endif; ?>
    
    <!-- Subtotal Before GST -->
    <?php if (isset($breakup['subtotal_before_gst'])): ?>
    <div class="jpc-section jpc-subtotal-section">
        <h3><?php _e('Subtotal (Before GST)', 'jewellery-price-calc'); ?></h3>
        <table>
            <tr class="total-row">
                <td><strong><?php _e('Subtotal', 'jewellery-price-calc'); ?>:</strong></td>
                <td><strong><?php echo wc_price($breakup['subtotal_before_gst']); ?></strong></td>
            </tr>
        </table>
    </div>
    <?php endif; ?>
    
    <!-- GST -->
    <?php if (!empty($breakup['gst']) && $breakup['gst'] > 0): ?>
    <div class="jpc-section">
        <h3><?php echo esc_html($breakup['gst_label']); ?></h3>
        <table>
            <tr>
                <td><?php _e('GST Rate', 'jewellery-price-calc'); ?>:</td>
                <td><?php echo number_format($breakup['gst_percentage'], 2); ?>%</td>
            </tr>
            <tr class="total-row">
                <td><strong><?php echo esc_html($breakup['gst_label']); ?>:</strong></td>
                <td><strong><?php echo wc_price($breakup['gst']); ?></strong></td>
            </tr>
        </table>
    </div>
    <?php endif; ?>
    
    <!-- Discount -->
    <?php if ($discount_amount > 0): ?>
    <div class="jpc-section">
        <h3><?php _e('Discount', 'jewellery-price-calc'); ?></h3>
        <table>
            <tr>
                <td><?php _e('Discount Percentage', 'jewellery-price-calc'); ?>:</td>
                <td><?php echo number_format($discount_percentage, 2); ?>%</td>
            </tr>
            <tr>
                <td><?php _e('Total Before Discount', 'jewellery-price-calc'); ?>:</td>
                <td><?php echo wc_price($regular_price); ?></td>
            </tr>
            <tr class="total-row discount-row">
                <td><strong><?php _e('Discount Amount', 'jewellery-price-calc'); ?>:</strong></td>
                <td><strong>-<?php echo wc_price($discount_amount); ?></strong></td>
            </tr>
        </table>
    </div>
    <?php endif; ?>
    
    <!-- Final Price -->
    <div class="jpc-section jpc-final-section">
        <h3><?php _e('FINAL PRICE', 'jewellery-price-calc'); ?></h3>
        <table>
            <tr class="final-price-row">
                <td><strong><?php _e('FINAL PRICE', 'jewellery-price-calc'); ?>:</strong></td>
                <td><strong><?php echo wc_price($sale_price); ?></strong></td>
            </tr>
        </table>
    </div>
</div>

<style>
.jpc-detailed-breakup {
    max-width: 600px;
    margin: 0 auto;
}

.jpc-detailed-breakup h2 {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 3px solid #333;
}

.jpc-section {
    margin-bottom: 20px;
    padding: 15px;
    background: #f9f9f9;
    border-radius: 5px;
}

.jpc-section h3 {
    margin: 0 0 10px 0;
    font-size: 16px;
    color: #333;
}

.jpc-section table {
    width: 100%;
    border-collapse: collapse;
}

.jpc-section table tr {
    border-bottom: 1px solid #eee;
}

.jpc-section table tr:last-child {
    border-bottom: none;
}

.jpc-section table td {
    padding: 8px 5px;
}

.jpc-section table td:first-child {
    text-align: left;
}

.jpc-section table td:last-child {
    text-align: right;
    font-weight: 600;
}

.jpc-section table tr.total-row {
    border-top: 2px solid #ddd;
    margin-top: 5px;
}

.jpc-section table tr.total-row td {
    padding-top: 12px;
    font-size: 15px;
}

.jpc-subtotal-section {
    background: #e8f4f8;
    border: 2px solid #3498db;
}

.jpc-final-section {
    background: #d4edda;
    border: 3px solid #28a745;
}

.jpc-final-section h3 {
    font-size: 18px;
    color: #155724;
}

.final-price-row td {
    font-size: 20px !important;
    color: #155724;
}

.discount-row td {
    color: #27ae60;
}
</style>
