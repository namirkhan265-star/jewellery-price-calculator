<?php
/**
 * Discount Settings Page Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap jpc-admin-wrap">
    <h1><?php _e('Discount Settings', 'jewellery-price-calc'); ?></h1>
    
    <form method="post" action="options.php">
        <?php settings_fields('jpc_discount_settings'); ?>
        
        <div class="jpc-card">
            <table class="form-table">
                <tr>
                    <th colspan="2">
                        <label>
                            <input type="checkbox" name="jpc_enable_discount" value="yes" <?php checked(get_option('jpc_enable_discount'), 'yes'); ?>>
                            <?php _e('Enable Discount Feature', 'jewellery-price-calc'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Please note: This feature will update the product sale price. So if you want to enter sale price directly then do not enable this feature.', 'jewellery-price-calc'); ?>
                        </p>
                    </th>
                </tr>
                
                <tr>
                    <th colspan="2">
                        <label>
                            <input type="checkbox" name="jpc_discount_on_metals" value="yes" <?php checked(get_option('jpc_discount_on_metals'), 'yes'); ?>>
                            <?php _e('Enable Discount on Metals', 'jewellery-price-calc'); ?>
                        </label>
                    </th>
                </tr>
                
                <tr>
                    <th colspan="2">
                        <label>
                            <input type="checkbox" name="jpc_discount_on_making" value="yes" <?php checked(get_option('jpc_discount_on_making'), 'yes'); ?>>
                            <?php _e('Enable Discount on Making Charge', 'jewellery-price-calc'); ?>
                        </label>
                    </th>
                </tr>
                
                <tr>
                    <th colspan="2">
                        <label>
                            <input type="checkbox" name="jpc_discount_on_wastage" value="yes" <?php checked(get_option('jpc_discount_on_wastage'), 'yes'); ?>>
                            <?php _e('Enable Discount on Wastage Charge', 'jewellery-price-calc'); ?>
                        </label>
                    </th>
                </tr>
            </table>
        </div>
        
        <?php submit_button(__('Save Changes', 'jewellery-price-calc')); ?>
    </form>
</div>
