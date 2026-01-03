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
        <label for="_jpc_diamond_id"><?php _e('Select Diamond (Optional)', 'jewellery-price-calc'); ?></label>
        <select id="_jpc_diamond_id" name="_jpc_diamond_id" class="widefat">
            <option value=""><?php _e('No Diamond', 'jewellery-price-calc'); ?></option>
            <?php 
            $diamonds = JPC_Diamonds::get_all();
            foreach ($diamonds as $diamond): 
                $total_price = $diamond->price_per_carat * $diamond->carat;
            ?>
                <option value="<?php echo esc_attr($diamond->id); ?>" <?php selected($diamond_id, $diamond->id); ?>>
                    <?php echo esc_html($diamond->display_name); ?> - ₹<?php echo number_format($total_price, 2); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description"><?php _e('Select a diamond to include in the price calculation', 'jewellery-price-calc'); ?></p>
    </div>
    
    <div class="form-field">
        <label for="_jpc_diamond_quantity"><?php _e('Diamond Quantity', 'jewellery-price-calc'); ?></label>
        <input type="number" id="_jpc_diamond_quantity" name="_jpc_diamond_quantity" value="<?php echo esc_attr($diamond_quantity ?: 1); ?>" step="1" min="0" class="widefat">
        <p class="description"><?php _e('Number of diamonds (leave 0 or empty if no diamond)', 'jewellery-price-calc'); ?></p>
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
    
    <?php 
    // Extra Fields #1-5
    for ($i = 1; $i <= 5; $i++):
        $enabled = get_option('jpc_enable_extra_field_' . $i);
        if ($enabled === 'yes' || $enabled === '1'):
            $label = get_option('jpc_extra_field_label_' . $i, 'Extra Field #' . $i);
            $value = get_post_meta($post->ID, '_jpc_extra_field_' . $i, true);
    ?>
    <div class="form-field">
        <label for="_jpc_extra_field_<?php echo $i; ?>"><?php echo esc_html($label); ?></label>
        <input type="number" id="_jpc_extra_field_<?php echo $i; ?>" name="_jpc_extra_field_<?php echo $i; ?>" value="<?php echo esc_attr($value); ?>" step="0.01" min="0" class="widefat">
        <p class="description"><?php printf(__('Enter %s cost', 'jewellery-price-calc'), esc_html($label)); ?></p>
    </div>
    <?php 
        endif;
    endfor; 
    ?>
    
    <?php if (get_option('jpc_enable_discount') === 'yes'): ?>
    <div class="form-field">
        <label for="_jpc_discount_percentage"><?php _e('Discount Percentage', 'jewellery-price-calc'); ?></label>
        <input type="number" id="_jpc_discount_percentage" name="_jpc_discount_percentage" value="<?php echo esc_attr($discount_percentage); ?>" step="0.01" min="0" max="100" class="widefat">
        <p class="description"><?php _e('Enter discount percentage (0-100)', 'jewellery-price-calc'); ?></p>
    </div>
    <?php endif; ?>
    
    <!-- Live Price Breakup Container -->
    <div class="jpc-price-breakup-admin">
        <p class="description"><?php _e('Fill in the fields above to see live price calculation', 'jewellery-price-calc'); ?></p>
    </div>
    
    <p class="description">
        <strong><?php _e('Note:', 'jewellery-price-calc'); ?></strong> 
        <?php _e('Product price will be automatically calculated when you save. Make sure to fill in all required fields.', 'jewellery-price-calc'); ?>
    </p>
</div>
