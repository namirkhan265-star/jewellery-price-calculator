<?php
/**
 * Product Meta Box Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="jpc-product-meta-box">
    <div class="form-field">
        <label for="_jpc_metal_id"><?php _e('Select Metal', 'jewellery-price-calc'); ?> <span class="required">*</span></label>
        <select id="_jpc_metal_id" name="_jpc_metal_id" class="widefat">
            <option value=""><?php _e('Select Metal', 'jewellery-price-calc'); ?></option>
            <?php foreach ($metals as $metal): ?>
                <option value="<?php echo esc_attr($metal->id); ?>" <?php selected($metal_id, $metal->id); ?>>
                    <?php echo esc_html($metal->display_name); ?> (<?php echo esc_html($metal->group_name); ?>) - ₹<?php echo number_format($metal->price_per_unit, 2); ?>/<?php echo esc_html($metal->unit); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description"><?php _e('Select the metal type for this product', 'jewellery-price-calc'); ?></p>
    </div>
    
    <div class="form-field">
        <label for="_jpc_metal_weight"><?php _e('Metal Weight', 'jewellery-price-calc'); ?> <span class="required">*</span></label>
        <input type="number" id="_jpc_metal_weight" name="_jpc_metal_weight" value="<?php echo esc_attr($metal_weight); ?>" step="0.001" min="0" class="widefat">
        <p class="description"><?php _e('Enter weight in grams or carats', 'jewellery-price-calc'); ?></p>
    </div>
    
    <div class="form-field">
        <label for="_jpc_making_charge"><?php _e('Making Charge', 'jewellery-price-calc'); ?></label>
        <div style="display: flex; gap: 10px;">
            <input type="number" id="_jpc_making_charge" name="_jpc_making_charge" value="<?php echo esc_attr($making_charge); ?>" step="0.01" min="0" style="flex: 1;">
            <select name="_jpc_making_charge_type" style="width: 150px;">
                <option value="percentage" <?php selected($making_charge_type, 'percentage'); ?>><?php _e('Percentage', 'jewellery-price-calc'); ?></option>
                <option value="fixed" <?php selected($making_charge_type, 'fixed'); ?>><?php _e('Fixed Amount', 'jewellery-price-calc'); ?></option>
            </select>
        </div>
        <p class="description"><?php _e('Enter making charge as percentage or fixed amount', 'jewellery-price-calc'); ?></p>
    </div>
    
    <div class="form-field">
        <label for="_jpc_wastage_charge"><?php _e('Wastage Charge', 'jewellery-price-calc'); ?></label>
        <div style="display: flex; gap: 10px;">
            <input type="number" id="_jpc_wastage_charge" name="_jpc_wastage_charge" value="<?php echo esc_attr($wastage_charge); ?>" step="0.01" min="0" style="flex: 1;">
            <select name="_jpc_wastage_charge_type" style="width: 150px;">
                <option value="percentage" <?php selected($wastage_charge_type, 'percentage'); ?>><?php _e('Percentage', 'jewellery-price-calc'); ?></option>
                <option value="fixed" <?php selected($wastage_charge_type, 'fixed'); ?>><?php _e('Fixed Amount', 'jewellery-price-calc'); ?></option>
            </select>
        </div>
        <p class="description"><?php _e('Enter wastage charge as percentage or fixed amount', 'jewellery-price-calc'); ?></p>
    </div>
    
    <?php if (get_option('jpc_enable_pearl_cost') === 'yes'): ?>
    <div class="form-field">
        <label for="_jpc_pearl_cost"><?php _e('Pearl Cost', 'jewellery-price-calc'); ?></label>
        <input type="number" id="_jpc_pearl_cost" name="_jpc_pearl_cost" value="<?php echo esc_attr($pearl_cost); ?>" step="0.01" min="0" class="widefat">
        <p class="description"><?php _e('Enter pearl cost if applicable', 'jewellery-price-calc'); ?></p>
    </div>
    <?php endif; ?>
    
    <?php if (get_option('jpc_enable_stone_cost') === 'yes'): ?>
    <div class="form-field">
        <label for="_jpc_stone_cost"><?php _e('Stone Cost', 'jewellery-price-calc'); ?></label>
        <input type="number" id="_jpc_stone_cost" name="_jpc_stone_cost" value="<?php echo esc_attr($stone_cost); ?>" step="0.01" min="0" class="widefat">
        <p class="description"><?php _e('Enter stone cost if applicable', 'jewellery-price-calc'); ?></p>
    </div>
    <?php endif; ?>
    
    <?php if (get_option('jpc_enable_extra_fee') === 'yes'): ?>
    <div class="form-field">
        <label for="_jpc_extra_fee"><?php _e('Extra Fee', 'jewellery-price-calc'); ?></label>
        <input type="number" id="_jpc_extra_fee" name="_jpc_extra_fee" value="<?php echo esc_attr($extra_fee); ?>" step="0.01" min="0" class="widefat">
        <p class="description"><?php _e('Enter any additional fees', 'jewellery-price-calc'); ?></p>
    </div>
    <?php endif; ?>
    
    <?php if (get_option('jpc_enable_discount') === 'yes'): ?>
    <div class="form-field">
        <label for="_jpc_discount_percentage"><?php _e('Discount Percentage', 'jewellery-price-calc'); ?></label>
        <input type="number" id="_jpc_discount_percentage" name="_jpc_discount_percentage" value="<?php echo esc_attr($discount_percentage); ?>" step="0.01" min="0" max="100" class="widefat">
        <p class="description"><?php _e('Enter discount percentage (0-100)', 'jewellery-price-calc'); ?></p>
    </div>
    <?php endif; ?>
    
    <?php if ($price_breakup && is_array($price_breakup)): ?>
    <div class="jpc-price-breakup-admin">
        <h4><?php _e('Current Price Breakup', 'jewellery-price-calc'); ?></h4>
        <table>
            <tr>
                <td><?php _e('Metal Price:', 'jewellery-price-calc'); ?></td>
                <td>₹<?php echo number_format($price_breakup['metal_price'], 2); ?></td>
            </tr>
            <?php if ($price_breakup['making_charge'] > 0): ?>
            <tr>
                <td><?php _e('Making Charge:', 'jewellery-price-calc'); ?></td>
                <td>₹<?php echo number_format($price_breakup['making_charge'], 2); ?></td>
            </tr>
            <?php endif; ?>
            <?php if ($price_breakup['wastage_charge'] > 0): ?>
            <tr>
                <td><?php _e('Wastage Charge:', 'jewellery-price-calc'); ?></td>
                <td>₹<?php echo number_format($price_breakup['wastage_charge'], 2); ?></td>
            </tr>
            <?php endif; ?>
            <?php if ($price_breakup['discount'] > 0): ?>
            <tr>
                <td><?php _e('Discount:', 'jewellery-price-calc'); ?></td>
                <td>-₹<?php echo number_format($price_breakup['discount'], 2); ?></td>
            </tr>
            <?php endif; ?>
            <?php if ($price_breakup['gst'] > 0): ?>
            <tr>
                <td><?php _e('GST:', 'jewellery-price-calc'); ?></td>
                <td>₹<?php echo number_format($price_breakup['gst'], 2); ?></td>
            </tr>
            <?php endif; ?>
            <tr class="total-row">
                <td><?php _e('Final Price:', 'jewellery-price-calc'); ?></td>
                <td>₹<?php echo number_format($price_breakup['final_price'], 2); ?></td>
            </tr>
        </table>
    </div>
    <?php endif; ?>
    
    <p class="description">
        <strong><?php _e('Note:', 'jewellery-price-calc'); ?></strong> 
        <?php _e('Product price will be automatically calculated when you save. Make sure to fill in all required fields.', 'jewellery-price-calc'); ?>
    </p>
</div>
