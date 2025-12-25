<?php
/**
 * General Settings Page Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap jpc-admin-wrap">
    <h1><?php _e('General Settings', 'jewellery-price-calc'); ?></h1>
    
    <form method="post" action="options.php">
        <?php settings_fields('jpc_general_settings'); ?>
        
        <div class="jpc-card">
            <h2><?php _e('Additional Cost Fields', 'jewellery-price-calc'); ?></h2>
            
            <table class="form-table">
                <tr>
                    <th colspan="2">
                        <label>
                            <input type="checkbox" name="jpc_enable_pearl_cost" value="yes" <?php checked(get_option('jpc_enable_pearl_cost'), 'yes'); ?>>
                            <?php _e('Enable Pearl Cost Field', 'jewellery-price-calc'); ?>
                        </label>
                    </th>
                </tr>
                
                <tr>
                    <th colspan="2">
                        <label>
                            <input type="checkbox" name="jpc_enable_stone_cost" value="yes" <?php checked(get_option('jpc_enable_stone_cost'), 'yes'); ?>>
                            <?php _e('Enable Stone Cost Field', 'jewellery-price-calc'); ?>
                        </label>
                    </th>
                </tr>
                
                <tr>
                    <th colspan="2">
                        <label>
                            <input type="checkbox" name="jpc_enable_extra_fee" value="yes" <?php checked(get_option('jpc_enable_extra_fee'), 'yes'); ?>>
                            <?php _e('Enable Extra Fee Field', 'jewellery-price-calc'); ?>
                        </label>
                    </th>
                </tr>
            </table>
        </div>
        
        <div class="jpc-card">
            <h2><?php _e('Additional Percentage', 'jewellery-price-calc'); ?></h2>
            
            <table class="form-table">
                <tr>
                    <th><label for="jpc_additional_percentage_label"><?php _e('Label Name', 'jewellery-price-calc'); ?></label></th>
                    <td>
                        <input type="text" id="jpc_additional_percentage_label" name="jpc_additional_percentage_label" value="<?php echo esc_attr(get_option('jpc_additional_percentage_label', 'Additional Percentage')); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th><label for="jpc_additional_percentage_value"><?php _e('Value (%)', 'jewellery-price-calc'); ?></label></th>
                    <td>
                        <input type="number" id="jpc_additional_percentage_value" name="jpc_additional_percentage_value" value="<?php echo esc_attr(get_option('jpc_additional_percentage_value', 0)); ?>" step="0.01" min="0" class="regular-text">
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="jpc-card">
            <h2><?php _e('Extra Fields', 'jewellery-price-calc'); ?></h2>
            
            <table class="form-table">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                <tr>
                    <th colspan="2">
                        <label>
                            <input type="checkbox" name="jpc_enable_extra_field_<?php echo $i; ?>" value="yes" <?php checked(get_option('jpc_enable_extra_field_' . $i), 'yes'); ?>>
                            <?php printf(__('Enable Extra Field #%d', 'jewellery-price-calc'), $i); ?>
                        </label>
                    </th>
                </tr>
                <tr>
                    <th><label for="jpc_extra_field_label_<?php echo $i; ?>"><?php printf(__('Field #%d Label', 'jewellery-price-calc'), $i); ?></label></th>
                    <td>
                        <input type="text" id="jpc_extra_field_label_<?php echo $i; ?>" name="jpc_extra_field_label_<?php echo $i; ?>" value="<?php echo esc_attr(get_option('jpc_extra_field_label_' . $i)); ?>" class="regular-text">
                    </td>
                </tr>
                <?php endfor; ?>
            </table>
        </div>
        
        <div class="jpc-card">
            <h2><?php _e('GST/Tax Settings', 'jewellery-price-calc'); ?></h2>
            
            <table class="form-table">
                <tr>
                    <th colspan="2">
                        <label>
                            <input type="checkbox" name="jpc_enable_gst" value="yes" <?php checked(get_option('jpc_enable_gst'), 'yes'); ?>>
                            <?php _e('Include GST Tax in Product Price', 'jewellery-price-calc'); ?>
                        </label>
                    </th>
                </tr>
                
                <tr>
                    <th><label for="jpc_gst_label"><?php _e('Tax Label', 'jewellery-price-calc'); ?></label></th>
                    <td>
                        <input type="text" id="jpc_gst_label" name="jpc_gst_label" value="<?php echo esc_attr(get_option('jpc_gst_label', 'Tax')); ?>" class="regular-text">
                    </td>
                </tr>
                
                <tr>
                    <th><label for="jpc_gst_value"><?php _e('Tax Value (%)', 'jewellery-price-calc'); ?></label></th>
                    <td>
                        <input type="number" id="jpc_gst_value" name="jpc_gst_value" value="<?php echo esc_attr(get_option('jpc_gst_value', 5)); ?>" step="0.01" min="0" class="regular-text">
                    </td>
                </tr>
                
                <tr>
                    <th colspan="2">
                        <h3><?php _e('Metal-Specific GST Rates', 'jewellery-price-calc'); ?></h3>
                        <p class="description"><?php _e('Leave empty to use default GST rate', 'jewellery-price-calc'); ?></p>
                    </th>
                </tr>
                
                <tr>
                    <th><label for="jpc_gst_gold"><?php _e('GST for Gold (%)', 'jewellery-price-calc'); ?></label></th>
                    <td>
                        <input type="number" id="jpc_gst_gold" name="jpc_gst_gold" value="<?php echo esc_attr(get_option('jpc_gst_gold')); ?>" step="0.01" min="0" class="regular-text">
                    </td>
                </tr>
                
                <tr>
                    <th><label for="jpc_gst_silver"><?php _e('GST for Silver (%)', 'jewellery-price-calc'); ?></label></th>
                    <td>
                        <input type="number" id="jpc_gst_silver" name="jpc_gst_silver" value="<?php echo esc_attr(get_option('jpc_gst_silver')); ?>" step="0.01" min="0" class="regular-text">
                    </td>
                </tr>
                
                <tr>
                    <th><label for="jpc_gst_diamond"><?php _e('GST for Diamond (%)', 'jewellery-price-calc'); ?></label></th>
                    <td>
                        <input type="number" id="jpc_gst_diamond" name="jpc_gst_diamond" value="<?php echo esc_attr(get_option('jpc_gst_diamond')); ?>" step="0.01" min="0" class="regular-text">
                    </td>
                </tr>
                
                <tr>
                    <th><label for="jpc_gst_platinum"><?php _e('GST for Platinum (%)', 'jewellery-price-calc'); ?></label></th>
                    <td>
                        <input type="number" id="jpc_gst_platinum" name="jpc_gst_platinum" value="<?php echo esc_attr(get_option('jpc_gst_platinum')); ?>" step="0.01" min="0" class="regular-text">
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="jpc-card">
            <h2><?php _e('Price Rounding', 'jewellery-price-calc'); ?></h2>
            
            <table class="form-table">
                <tr>
                    <th><label for="jpc_price_rounding"><?php _e('Round Product Price To', 'jewellery-price-calc'); ?></label></th>
                    <td>
                        <select id="jpc_price_rounding" name="jpc_price_rounding" class="regular-text">
                            <option value="default" <?php selected(get_option('jpc_price_rounding'), 'default'); ?>><?php _e('Default (No Rounding)', 'jewellery-price-calc'); ?></option>
                            <option value="nearest_10" <?php selected(get_option('jpc_price_rounding'), 'nearest_10'); ?>><?php _e('Nearest 10', 'jewellery-price-calc'); ?></option>
                            <option value="nearest_50" <?php selected(get_option('jpc_price_rounding'), 'nearest_50'); ?>><?php _e('Nearest 50', 'jewellery-price-calc'); ?></option>
                            <option value="nearest_100" <?php selected(get_option('jpc_price_rounding'), 'nearest_100'); ?>><?php _e('Nearest 100', 'jewellery-price-calc'); ?></option>
                            <option value="ceil_10" <?php selected(get_option('jpc_price_rounding'), 'ceil_10'); ?>><?php _e('Ceil to 10', 'jewellery-price-calc'); ?></option>
                            <option value="ceil_50" <?php selected(get_option('jpc_price_rounding'), 'ceil_50'); ?>><?php _e('Ceil to 50', 'jewellery-price-calc'); ?></option>
                            <option value="ceil_100" <?php selected(get_option('jpc_price_rounding'), 'ceil_100'); ?>><?php _e('Ceil to 100', 'jewellery-price-calc'); ?></option>
                            <option value="floor_10" <?php selected(get_option('jpc_price_rounding'), 'floor_10'); ?>><?php _e('Floor to 10', 'jewellery-price-calc'); ?></option>
                            <option value="floor_50" <?php selected(get_option('jpc_price_rounding'), 'floor_50'); ?>><?php _e('Floor to 50', 'jewellery-price-calc'); ?></option>
                            <option value="floor_100" <?php selected(get_option('jpc_price_rounding'), 'floor_100'); ?>><?php _e('Floor to 100', 'jewellery-price-calc'); ?></option>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="jpc-card">
            <h2><?php _e('Display Options', 'jewellery-price-calc'); ?></h2>
            
            <table class="form-table">
                <tr>
                    <th colspan="2">
                        <label>
                            <input type="checkbox" name="jpc_show_price_breakup" value="yes" <?php checked(get_option('jpc_show_price_breakup'), 'yes'); ?>>
                            <?php _e('Show Price Breakup on Product Pages', 'jewellery-price-calc'); ?>
                        </label>
                    </th>
                </tr>
            </table>
        </div>
        
        <?php submit_button(); ?>
    </form>
</div>
