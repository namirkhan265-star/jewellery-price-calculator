<?php
/**
 * General Settings Page Template
 */

if (!defined('ABSPATH')) {
    exit;
}

// Handle bulk price update
if (isset($_POST['jpc_bulk_update_prices']) && check_admin_referer('jpc_bulk_update_prices')) {
    $updated = 0;
    $errors = 0;
    
    // Get all products with JPC data
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_jpc_metal_id',
                'compare' => 'EXISTS'
            )
        )
    );
    
    $products = get_posts($args);
    
    foreach ($products as $product) {
        $result = JPC_Price_Calculator::calculate_and_update_price($product->ID);
        if ($result !== false) {
            $updated++;
        } else {
            $errors++;
        }
    }
    
    echo '<div class="notice notice-success is-dismissible"><p>';
    printf(__('Bulk price update completed! Updated: %d products. Errors: %d products.', 'jewellery-price-calc'), $updated, $errors);
    echo '</p></div>';
}
?>

<div class="wrap jpc-admin-wrap">
    <h1><?php _e('General Settings', 'jewellery-price-calc'); ?></h1>
    
    <!-- BULK UPDATE PRICES SECTION -->
    <div class="jpc-card" style="background: #fff3cd; border-left: 4px solid #ffc107;">
        <h2>🔄 <?php _e('Bulk Update All Product Prices', 'jewellery-price-calc'); ?></h2>
        <p><?php _e('Click this button to recalculate and update prices for ALL products based on current metal rates. This will fix any pricing discrepancies.', 'jewellery-price-calc'); ?></p>
        <form method="post" action="">
            <?php wp_nonce_field('jpc_bulk_update_prices'); ?>
            <button type="submit" name="jpc_bulk_update_prices" class="button button-primary button-large" onclick="return confirm('This will update ALL product prices. Continue?');">
                🔄 <?php _e('Update All Prices Now', 'jewellery-price-calc'); ?>
            </button>
        </form>
        <p><em><?php _e('Note: This may take a few moments if you have many products.', 'jewellery-price-calc'); ?></em></p>
    </div>
    
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
                    <th><label for="jpc_gst_label"><?php _e('GST Label', 'jewellery-price-calc'); ?></label></th>
                    <td>
                        <input type="text" id="jpc_gst_label" name="jpc_gst_label" value="<?php echo esc_attr(get_option('jpc_gst_label', 'GST')); ?>" class="regular-text">
                    </td>
                </tr>
                
                <tr>
                    <th><label for="jpc_gst_value"><?php _e('GST Value (%)', 'jewellery-price-calc'); ?></label></th>
                    <td>
                        <input type="number" id="jpc_gst_value" name="jpc_gst_value" value="<?php echo esc_attr(get_option('jpc_gst_value', 5)); ?>" step="0.01" min="0" class="regular-text">
                        <p class="description"><?php _e('Default GST percentage for all products', 'jewellery-price-calc'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th colspan="2"><strong><?php _e('Metal-Specific GST Rates (Optional)', 'jewellery-price-calc'); ?></strong></th>
                </tr>
                
                <tr>
                    <th><label for="jpc_gst_gold"><?php _e('Gold GST (%)', 'jewellery-price-calc'); ?></label></th>
                    <td>
                        <input type="number" id="jpc_gst_gold" name="jpc_gst_gold" value="<?php echo esc_attr(get_option('jpc_gst_gold')); ?>" step="0.01" min="0" class="regular-text">
                    </td>
                </tr>
                
                <tr>
                    <th><label for="jpc_gst_silver"><?php _e('Silver GST (%)', 'jewellery-price-calc'); ?></label></th>
                    <td>
                        <input type="number" id="jpc_gst_silver" name="jpc_gst_silver" value="<?php echo esc_attr(get_option('jpc_gst_silver')); ?>" step="0.01" min="0" class="regular-text">
                    </td>
                </tr>
                
                <tr>
                    <th><label for="jpc_gst_diamond"><?php _e('Diamond GST (%)', 'jewellery-price-calc'); ?></label></th>
                    <td>
                        <input type="number" id="jpc_gst_diamond" name="jpc_gst_diamond" value="<?php echo esc_attr(get_option('jpc_gst_diamond')); ?>" step="0.01" min="0" class="regular-text">
                    </td>
                </tr>
                
                <tr>
                    <th><label for="jpc_gst_platinum"><?php _e('Platinum GST (%)', 'jewellery-price-calc'); ?></label></th>
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
