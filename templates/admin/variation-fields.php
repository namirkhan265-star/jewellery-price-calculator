<?php
/**
 * Variation Fields Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="jpc-variation-fields">
    <p class="form-row form-row-full">
        <label><?php _e('Jewellery Price Calculator', 'jewellery-price-calc'); ?></label>
    </p>
    
    <p class="form-row form-row-full">
        <label for="_jpc_metal_id_<?php echo $loop; ?>"><?php _e('Select Metal', 'jewellery-price-calc'); ?></label>
        <select id="_jpc_metal_id_<?php echo $loop; ?>" name="_jpc_metal_id[<?php echo $loop; ?>]" class="widefat">
            <option value=""><?php _e('Select Metal', 'jewellery-price-calc'); ?></option>
            <?php foreach ($metals as $metal): ?>
                <option value="<?php echo esc_attr($metal->id); ?>" <?php selected($metal_id, $metal->id); ?>>
                    <?php echo esc_html($metal->display_name); ?> - ₹<?php echo number_format($metal->price_per_unit, 2); ?>/<?php echo esc_html($metal->unit); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    
    <p class="form-row form-row-full">
        <label for="_jpc_metal_weight_<?php echo $loop; ?>"><?php _e('Metal Weight', 'jewellery-price-calc'); ?></label>
        <input type="number" id="_jpc_metal_weight_<?php echo $loop; ?>" name="_jpc_metal_weight[<?php echo $loop; ?>]" value="<?php echo esc_attr($metal_weight); ?>" step="0.001" min="0" class="widefat">
    </p>
    
    <p class="form-row form-row-first">
        <label for="_jpc_making_charge_<?php echo $loop; ?>"><?php _e('Making Charge', 'jewellery-price-calc'); ?></label>
        <input type="number" id="_jpc_making_charge_<?php echo $loop; ?>" name="_jpc_making_charge[<?php echo $loop; ?>]" value="<?php echo esc_attr($making_charge); ?>" step="0.01" min="0" class="widefat">
    </p>
    
    <p class="form-row form-row-last">
        <label for="_jpc_making_charge_type_<?php echo $loop; ?>"><?php _e('Type', 'jewellery-price-calc'); ?></label>
        <select id="_jpc_making_charge_type_<?php echo $loop; ?>" name="_jpc_making_charge_type[<?php echo $loop; ?>]" class="widefat">
            <option value="percentage" <?php selected($making_charge_type, 'percentage'); ?>><?php _e('Percentage', 'jewellery-price-calc'); ?></option>
            <option value="fixed" <?php selected($making_charge_type, 'fixed'); ?>><?php _e('Fixed', 'jewellery-price-calc'); ?></option>
        </select>
    </p>
    
    <p class="form-row form-row-first">
        <label for="_jpc_wastage_charge_<?php echo $loop; ?>"><?php _e('Wastage Charge', 'jewellery-price-calc'); ?></label>
        <input type="number" id="_jpc_wastage_charge_<?php echo $loop; ?>" name="_jpc_wastage_charge[<?php echo $loop; ?>]" value="<?php echo esc_attr($wastage_charge); ?>" step="0.01" min="0" class="widefat">
    </p>
    
    <p class="form-row form-row-last">
        <label for="_jpc_wastage_charge_type_<?php echo $loop; ?>"><?php _e('Type', 'jewellery-price-calc'); ?></label>
        <select id="_jpc_wastage_charge_type_<?php echo $loop; ?>" name="_jpc_wastage_charge_type[<?php echo $loop; ?>]" class="widefat">
            <option value="percentage" <?php selected($wastage_charge_type, 'percentage'); ?>><?php _e('Percentage', 'jewellery-price-calc'); ?></option>
            <option value="fixed" <?php selected($wastage_charge_type, 'fixed'); ?>><?php _e('Fixed', 'jewellery-price-calc'); ?></option>
        </select>
    </p>
</div>
