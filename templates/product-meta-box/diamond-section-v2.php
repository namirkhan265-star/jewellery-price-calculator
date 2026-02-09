<?php
/**
 * Diamond Section Template v2.0.0
 * Supports both dropdown selection and manual entry with 4Cs
 */

if (!defined('ABSPATH')) {
    exit;
}

// Generate carat size options (0.01 to 10.00)
function jpc_get_carat_options() {
    $options = array();
    
    // 0.01 to 0.99 (increment by 0.01)
    for ($i = 1; $i <= 99; $i++) {
        $carat = $i / 100;
        $options[] = number_format($carat, 2);
    }
    
    // 1.00 to 10.00 (increment by 0.10)
    for ($i = 10; $i <= 100; $i++) {
        $carat = $i / 10;
        $options[] = number_format($carat, 2);
    }
    
    return $options;
}

$carat_options = jpc_get_carat_options();
?>

<!-- Diamond Section v2.0.0 -->
<div class="jpc-section highlight">
    <h3>
        <?php _e('Diamond Details', 'jewellery-price-calc'); ?>
        <span class="jpc-new-badge">v2.0 NEW</span>
    </h3>
    
    <div class="jpc-radio-group">
        <label>
            <input type="radio" name="jpc_diamond_entry_mode" value="dropdown" 
                   <?php checked($diamond_entry_mode, 'dropdown'); ?>>
            <?php _e('Select from Pre-created Diamonds', 'jewellery-price-calc'); ?>
        </label>
        <label>
            <input type="radio" name="jpc_diamond_entry_mode" value="manual" 
                   <?php checked($diamond_entry_mode, 'manual'); ?>>
            <?php _e('Enter Diamond Details Manually', 'jewellery-price-calc'); ?>
        </label>
    </div>
    
    <!-- Dropdown Mode -->
    <div id="jpc_diamond_dropdown_mode" class="jpc-conditional" style="display: <?php echo $diamond_entry_mode === 'dropdown' ? 'block' : 'none'; ?>;">
        <div class="jpc-form-field">
            <label for="jpc_diamond_id"><?php _e('Select Diamond', 'jewellery-price-calc'); ?></label>
            <select id="jpc_diamond_id" name="jpc_diamond_id">
                <option value=""><?php _e('Select Diamond', 'jewellery-price-calc'); ?></option>
                <?php foreach ($diamonds as $diamond): ?>
                    <option value="<?php echo esc_attr($diamond->id); ?>" 
                            data-price="<?php echo esc_attr($diamond->price_per_carat); ?>"
                            data-carat="<?php echo esc_attr($diamond->carat); ?>"
                            <?php selected($diamond_id, $diamond->id); ?>>
                        <?php echo esc_html($diamond->display_name); ?> 
                        (<?php echo $diamond->carat; ?>ct @ ₹<?php echo number_format($diamond->price_per_carat, 2); ?>/ct)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="jpc-form-field">
            <label for="jpc_diamond_quantity"><?php _e('Diamond Quantity', 'jewellery-price-calc'); ?></label>
            <input type="number" id="jpc_diamond_quantity" name="jpc_diamond_quantity" 
                   value="<?php echo esc_attr($diamond_quantity); ?>" 
                   step="1" min="0">
        </div>
    </div>
    
    <!-- Manual Entry Mode -->
    <div id="jpc_diamond_manual_mode" class="jpc-conditional" style="display: <?php echo $diamond_entry_mode === 'manual' ? 'block' : 'none'; ?>;">
        <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 4px; margin-bottom: 15px;">
            <strong><?php _e('Manual Diamond Entry', 'jewellery-price-calc'); ?></strong>
            <p style="margin: 5px 0 0 0; font-size: 13px;">
                <?php _e('Enter all diamond details below. The 4Cs (Shape, Colour, Clarity, Cut) will automatically adjust the price based on your configured adjustment values.', 'jewellery-price-calc'); ?>
            </p>
        </div>
        
        <div class="jpc-form-field">
            <label for="jpc_manual_diamond_group_id"><?php _e('Diamond Group', 'jewellery-price-calc'); ?></label>
            <select id="jpc_manual_diamond_group_id" name="jpc_manual_diamond_group_id">
                <option value=""><?php _e('Select Diamond Group', 'jewellery-price-calc'); ?></option>
                <?php foreach ($diamond_groups as $group): ?>
                    <option value="<?php echo esc_attr($group->id); ?>" 
                            <?php selected($manual_diamond_group_id, $group->id); ?>>
                        <?php echo esc_html($group->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="jpc-help-text"><?php _e('e.g., Natural Diamond, Lab-Grown Diamond, etc.', 'jewellery-price-calc'); ?></p>
        </div>
        
        <div class="jpc-form-field">
            <label for="jpc_manual_diamond_carat"><?php _e('Carat Size', 'jewellery-price-calc'); ?></label>
            <select id="jpc_manual_diamond_carat" name="jpc_manual_diamond_carat">
                <option value=""><?php _e('Select Carat Size', 'jewellery-price-calc'); ?></option>
                <?php foreach ($carat_options as $carat): ?>
                    <option value="<?php echo esc_attr($carat); ?>" 
                            <?php selected($manual_diamond_carat, $carat); ?>>
                        <?php echo $carat; ?> ct
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="jpc-help-text"><?php _e('Select from 0.01 to 10.00 carats', 'jewellery-price-calc'); ?></p>
        </div>
        
        <div class="jpc-form-field">
            <label for="jpc_manual_diamond_certification_id"><?php _e('Certification', 'jewellery-price-calc'); ?></label>
            <select id="jpc_manual_diamond_certification_id" name="jpc_manual_diamond_certification_id">
                <option value=""><?php _e('Select Certification', 'jewellery-price-calc'); ?></option>
                <?php foreach ($diamond_certifications as $cert): ?>
                    <option value="<?php echo esc_attr($cert->id); ?>" 
                            data-adjustment-type="<?php echo esc_attr($cert->adjustment_type); ?>"
                            data-adjustment-value="<?php echo esc_attr($cert->adjustment_value); ?>"
                            <?php selected($manual_diamond_certification_id, $cert->id); ?>>
                        <?php echo esc_html($cert->name); ?>
                        <?php if ($cert->adjustment_value != 0): ?>
                            (<?php echo $cert->adjustment_type === 'percentage' ? $cert->adjustment_value . '%' : '₹' . $cert->adjustment_value; ?>)
                        <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="jpc-help-text"><?php _e('e.g., GIA, IGI, HRD, etc.', 'jewellery-price-calc'); ?></p>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
            <div class="jpc-form-field" style="margin-bottom: 0;">
                <label for="jpc_manual_diamond_shape_id"><?php _e('Shape', 'jewellery-price-calc'); ?></label>
                <select id="jpc_manual_diamond_shape_id" name="jpc_manual_diamond_shape_id">
                    <option value=""><?php _e('Select Shape', 'jewellery-price-calc'); ?></option>
                    <?php foreach ($diamond_shapes as $shape): ?>
                        <option value="<?php echo esc_attr($shape->id); ?>" 
                                data-adjustment-type="<?php echo esc_attr($shape->adjustment_type); ?>"
                                data-adjustment-value="<?php echo esc_attr($shape->adjustment_value); ?>"
                                <?php selected($manual_diamond_shape_id, $shape->id); ?>>
                            <?php echo esc_html($shape->name); ?>
                            <?php if ($shape->adjustment_value != 0): ?>
                                (<?php echo $shape->adjustment_type === 'percentage' ? $shape->adjustment_value . '%' : '₹' . $shape->adjustment_value; ?>)
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="jpc-form-field" style="margin-bottom: 0;">
                <label for="jpc_manual_diamond_colour_id"><?php _e('Colour', 'jewellery-price-calc'); ?></label>
                <select id="jpc_manual_diamond_colour_id" name="jpc_manual_diamond_colour_id">
                    <option value=""><?php _e('Select Colour', 'jewellery-price-calc'); ?></option>
                    <?php foreach ($diamond_colours as $colour): ?>
                        <option value="<?php echo esc_attr($colour->id); ?>" 
                                data-adjustment-type="<?php echo esc_attr($colour->adjustment_type); ?>"
                                data-adjustment-value="<?php echo esc_attr($colour->adjustment_value); ?>"
                                <?php selected($manual_diamond_colour_id, $colour->id); ?>>
                            <?php echo esc_html($colour->name); ?>
                            <?php if ($colour->adjustment_value != 0): ?>
                                (<?php echo $colour->adjustment_type === 'percentage' ? $colour->adjustment_value . '%' : '₹' . $colour->adjustment_value; ?>)
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
            <div class="jpc-form-field" style="margin-bottom: 0;">
                <label for="jpc_manual_diamond_clarity_id"><?php _e('Clarity', 'jewellery-price-calc'); ?></label>
                <select id="jpc_manual_diamond_clarity_id" name="jpc_manual_diamond_clarity_id">
                    <option value=""><?php _e('Select Clarity', 'jewellery-price-calc'); ?></option>
                    <?php foreach ($diamond_clarities as $clarity): ?>
                        <option value="<?php echo esc_attr($clarity->id); ?>" 
                                data-adjustment-type="<?php echo esc_attr($clarity->adjustment_type); ?>"
                                data-adjustment-value="<?php echo esc_attr($clarity->adjustment_value); ?>"
                                <?php selected($manual_diamond_clarity_id, $clarity->id); ?>>
                            <?php echo esc_html($clarity->name); ?>
                            <?php if ($clarity->adjustment_value != 0): ?>
                                (<?php echo $clarity->adjustment_type === 'percentage' ? $clarity->adjustment_value . '%' : '₹' . $clarity->adjustment_value; ?>)
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="jpc-form-field" style="margin-bottom: 0;">
                <label for="jpc_manual_diamond_cut_id"><?php _e('Cut', 'jewellery-price-calc'); ?></label>
                <select id="jpc_manual_diamond_cut_id" name="jpc_manual_diamond_cut_id">
                    <option value=""><?php _e('Select Cut', 'jewellery-price-calc'); ?></option>
                    <?php foreach ($diamond_cuts as $cut): ?>
                        <option value="<?php echo esc_attr($cut->id); ?>" 
                                data-adjustment-type="<?php echo esc_attr($cut->adjustment_type); ?>"
                                data-adjustment-value="<?php echo esc_attr($cut->adjustment_value); ?>"
                                <?php selected($manual_diamond_cut_id, $cut->id); ?>>
                            <?php echo esc_html($cut->name); ?>
                            <?php if ($cut->adjustment_value != 0): ?>
                                (<?php echo $cut->adjustment_type === 'percentage' ? $cut->adjustment_value . '%' : '₹' . $cut->adjustment_value; ?>)
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="jpc-form-field">
            <label for="jpc_manual_diamond_quantity"><?php _e('Quantity', 'jewellery-price-calc'); ?></label>
            <input type="number" id="jpc_manual_diamond_quantity" name="jpc_manual_diamond_quantity" 
                   value="<?php echo esc_attr($manual_diamond_quantity); ?>" 
                   step="1" min="0">
            <p class="jpc-help-text"><?php _e('Number of diamonds', 'jewellery-price-calc'); ?></p>
        </div>
        
        <div class="jpc-form-field">
            <label for="jpc_manual_diamond_price_per_carat"><?php _e('Base Price per Carat (₹)', 'jewellery-price-calc'); ?></label>
            <input type="number" id="jpc_manual_diamond_price_per_carat" name="jpc_manual_diamond_price_per_carat" 
                   value="<?php echo esc_attr($manual_diamond_price_per_carat); ?>" 
                   step="0.01" min="0">
            <p class="jpc-help-text"><?php _e('Base price before 4Cs adjustments are applied', 'jewellery-price-calc'); ?></p>
        </div>
        
        <!-- Live Calculation Display -->
        <div id="jpc_manual_diamond_calc_display" class="jpc-auto-calc-display" style="display: none; background: #d1ecf1; border-color: #bee5eb; color: #0c5460;">
            <span id="jpc_manual_diamond_calc_text"></span>
        </div>
    </div>
</div>
