<?php
/**
 * Other Costs Section Template
 * Stones, Pearls, Extra Fees, Discount, Extra Fields
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<!-- Other Costs Section -->
<div class="jpc-section">
    <h3><?php _e('Other Costs', 'jewellery-price-calc'); ?></h3>
    
    <div class="jpc-form-field">
        <label for="jpc_stone_cost"><?php _e('Stone Cost (₹)', 'jewellery-price-calc'); ?></label>
        <input type="number" id="jpc_stone_cost" name="jpc_stone_cost" 
               value="<?php echo esc_attr($stone_cost); ?>" 
               step="0.01" min="0">
        <p class="jpc-help-text"><?php _e('Total cost of stones (excluding diamonds)', 'jewellery-price-calc'); ?></p>
    </div>
    
    <div class="jpc-form-field">
        <label for="jpc_pearl_cost"><?php _e('Pearl Cost (₹)', 'jewellery-price-calc'); ?></label>
        <input type="number" id="jpc_pearl_cost" name="jpc_pearl_cost" 
               value="<?php echo esc_attr($pearl_cost); ?>" 
               step="0.01" min="0">
        <p class="jpc-help-text"><?php _e('Total cost of pearls', 'jewellery-price-calc'); ?></p>
    </div>
    
    <div class="jpc-form-field">
        <label for="jpc_extra_fee"><?php _e('Extra Fees (₹)', 'jewellery-price-calc'); ?></label>
        <input type="number" id="jpc_extra_fee" name="jpc_extra_fee" 
               value="<?php echo esc_attr($extra_fee); ?>" 
               step="0.01" min="0">
        <p class="jpc-help-text"><?php _e('Any additional fees or charges', 'jewellery-price-calc'); ?></p>
    </div>
    
    <div class="jpc-form-field">
        <label for="jpc_discount_percentage"><?php _e('Discount (%)', 'jewellery-price-calc'); ?></label>
        <input type="number" id="jpc_discount_percentage" name="jpc_discount_percentage" 
               value="<?php echo esc_attr($discount_percentage); ?>" 
               step="0.01" min="0" max="100">
        <p class="jpc-help-text"><?php _e('Discount percentage to apply on final price', 'jewellery-price-calc'); ?></p>
    </div>
</div>

<!-- Extra Fields Section -->
<div class="jpc-section">
    <h3><?php _e('Extra Fields (Optional)', 'jewellery-price-calc'); ?></h3>
    <p style="margin-top: 0; color: #666; font-size: 13px;">
        <?php _e('Use these fields to store any additional information about the product.', 'jewellery-price-calc'); ?>
    </p>
    
    <?php for ($i = 1; $i <= 5; $i++): ?>
        <div class="jpc-form-field">
            <label for="jpc_extra_field_<?php echo $i; ?>">
                <?php printf(__('Extra Field %d', 'jewellery-price-calc'), $i); ?>
            </label>
            <input type="text" id="jpc_extra_field_<?php echo $i; ?>" 
                   name="jpc_extra_field_<?php echo $i; ?>" 
                   value="<?php echo esc_attr($extra_fields[$i]); ?>" 
                   class="regular-text">
        </div>
    <?php endfor; ?>
</div>

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
            Metal Cost + Making Charges + Wastage + Diamond Cost + Stone Cost + Pearl Cost + Extra Fees + GST - Discount
        </code>
    </p>
</div>
