<?php
/**
 * Other Costs Section Template
 * Stones, Pearls, Extra Fees, Discount, Extra Fields
 * v2.5.0: Now uses custom labels from settings
 * v2.5.3: Extra fields now show custom labels and only enabled fields
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get custom labels from settings - v2.5.0
$pearl_cost_label = get_option('jpc_pearl_cost_label', 'Pearl Cost');
$pearl_cost_type = get_option('jpc_pearl_cost_type', 'fixed');

$stone_cost_label = get_option('jpc_stone_cost_label', 'Stone Cost');
$stone_cost_type = get_option('jpc_stone_cost_type', 'fixed');

$extra_fee_label = get_option('jpc_extra_fee_label', 'Extra Fee');
$extra_fee_type = get_option('jpc_extra_fee_type', 'fixed');

// Get extra field settings - v2.5.3
$extra_field_settings = array();
for ($i = 1; $i <= 5; $i++) {
    $extra_field_settings[$i] = array(
        'enabled' => get_option('jpc_enable_extra_field_' . $i, 'no'),
        'label' => get_option('jpc_extra_field_label_' . $i, 'Extra Field ' . $i)
    );
}
?>

<!-- Other Costs Section -->
<div class="jpc-section">
    <h3><?php _e('Other Costs', 'jewellery-price-calc'); ?></h3>
    
    <!-- Stone Cost - v2.5.0 ENHANCED -->
    <div class="jpc-form-field">
        <label for="jpc_stone_cost">
            <?php echo esc_html($stone_cost_label); ?>
            <?php if ($stone_cost_type === 'percentage'): ?>
                <span style="color: #2271b1; font-weight: 600;">(% of Metal + Diamond + Making + Wastage)</span>
            <?php else: ?>
                <span style="color: #666;">(₹)</span>
            <?php endif; ?>
        </label>
        <input type="number" id="jpc_stone_cost" name="jpc_stone_cost" 
               value="<?php echo esc_attr($stone_cost); ?>" 
               step="0.01" min="0">
        <p class="jpc-help-text">
            <?php if ($stone_cost_type === 'percentage'): ?>
                <?php _e('Enter percentage value (e.g., 10 for 10% of Metal + Diamond + Making + Wastage)', 'jewellery-price-calc'); ?>
            <?php else: ?>
                <?php _e('Enter fixed amount in rupees', 'jewellery-price-calc'); ?>
            <?php endif; ?>
        </p>
    </div>
    
    <!-- Pearl Cost - v2.5.0 ENHANCED -->
    <div class="jpc-form-field">
        <label for="jpc_pearl_cost">
            <?php echo esc_html($pearl_cost_label); ?>
            <?php if ($pearl_cost_type === 'percentage'): ?>
                <span style="color: #2271b1; font-weight: 600;">(% of Metal + Diamond + Making + Wastage)</span>
            <?php else: ?>
                <span style="color: #666;">(₹)</span>
            <?php endif; ?>
        </label>
        <input type="number" id="jpc_pearl_cost" name="jpc_pearl_cost" 
               value="<?php echo esc_attr($pearl_cost); ?>" 
               step="0.01" min="0">
        <p class="jpc-help-text">
            <?php if ($pearl_cost_type === 'percentage'): ?>
                <?php _e('Enter percentage value (e.g., 5 for 5% of Metal + Diamond + Making + Wastage)', 'jewellery-price-calc'); ?>
            <?php else: ?>
                <?php _e('Enter fixed amount in rupees', 'jewellery-price-calc'); ?>
            <?php endif; ?>
        </p>
    </div>
    
    <!-- Extra Fee - v2.5.0 ENHANCED -->
    <div class="jpc-form-field">
        <label for="jpc_extra_fee">
            <?php echo esc_html($extra_fee_label); ?>
            <?php if ($extra_fee_type === 'percentage'): ?>
                <span style="color: #2271b1; font-weight: 600;">(% of Metal + Diamond + Making + Wastage)</span>
            <?php else: ?>
                <span style="color: #666;">(₹)</span>
            <?php endif; ?>
        </label>
        <input type="number" id="jpc_extra_fee" name="jpc_extra_fee" 
               value="<?php echo esc_attr($extra_fee); ?>" 
               step="0.01" min="0">
        <p class="jpc-help-text">
            <?php if ($extra_fee_type === 'percentage'): ?>
                <?php _e('Enter percentage value (e.g., 2 for 2% of Metal + Diamond + Making + Wastage)', 'jewellery-price-calc'); ?>
            <?php else: ?>
                <?php _e('Enter fixed amount in rupees', 'jewellery-price-calc'); ?>
            <?php endif; ?>
        </p>
    </div>
    
    <div class="jpc-form-field">
        <label for="jpc_discount_percentage"><?php _e('Discount (%)', 'jewellery-price-calc'); ?></label>
        <input type="number" id="jpc_discount_percentage" name="jpc_discount_percentage" 
               value="<?php echo esc_attr($discount_percentage); ?>" 
               step="0.01" min="0" max="100">
        <p class="jpc-help-text"><?php _e('Discount percentage to apply on final price', 'jewellery-price-calc'); ?></p>
    </div>
</div>

<?php
// Check if any extra fields are enabled - v2.5.3
$has_enabled_fields = false;
foreach ($extra_field_settings as $settings) {
    if ($settings['enabled'] === 'yes') {
        $has_enabled_fields = true;
        break;
    }
}
?>

<?php if ($has_enabled_fields): ?>
<!-- Extra Fields Section - v2.5.3 ENHANCED -->
<div class="jpc-section">
    <h3><?php _e('Extra Fields (Optional)', 'jewellery-price-calc'); ?></h3>
    <p style="margin-top: 0; color: #666; font-size: 13px;">
        <?php _e('Use these fields to store any additional information about the product.', 'jewellery-price-calc'); ?>
    </p>
    
    <?php for ($i = 1; $i <= 5; $i++): ?>
        <?php if ($extra_field_settings[$i]['enabled'] === 'yes'): ?>
            <div class="jpc-form-field">
                <label for="jpc_extra_field_<?php echo $i; ?>">
                    <?php echo esc_html($extra_field_settings[$i]['label']); ?>
                </label>
                <input type="text" id="jpc_extra_field_<?php echo $i; ?>" 
                       name="jpc_extra_field_<?php echo $i; ?>" 
                       value="<?php echo esc_attr($extra_fields[$i]); ?>" 
                       class="regular-text">
            </div>
        <?php endif; ?>
    <?php endfor; ?>
</div>
<?php endif; ?>

<!-- Price Calculation Info -->
<div class="jpc-section" style="background: #e8f5e9; border-left-color: #4caf50;">
    <h3 style="color: #2e7d32;">
        <span class="dashicons dashicons-info" style="color: #4caf50;"></span>
        <?php _e('Price Calculation', 'jewellery-price-calc'); ?>
    </h3>
    <p style="margin: 0; font-size: 13px; line-height: 1.6;">
        <strong><?php _e('The product price will be automatically calculated when you save/update this product.', 'jewellery-price-calc'); ?></strong>
    </p>
    <p style="margin: 10px 0 0 0; font-size: 13px; line-height: 1.6;">
        <?php _e('Formula:', 'jewellery-price-calc'); ?>
        <code style="background: white; padding: 2px 6px; border-radius: 3px; font-size: 12px;">
            Metal Cost + Making Charges + Wastage + Diamond Cost + <?php echo esc_html($stone_cost_label); ?> + <?php echo esc_html($pearl_cost_label); ?> + <?php echo esc_html($extra_fee_label); ?> + GST - Discount
        </code>
    </p>
</div>
