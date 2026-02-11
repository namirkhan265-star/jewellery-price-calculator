<?php
/**
 * General Settings Template
 * v2.5.5: Enhanced Additional Percentage section with enable/disable and documentation
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get current settings
$enable_pearl_cost = get_option('jpc_enable_pearl_cost', 'no');
$pearl_cost_label = get_option('jpc_pearl_cost_label', 'Pearl Cost');
$pearl_cost_type = get_option('jpc_pearl_cost_type', 'fixed');

$enable_stone_cost = get_option('jpc_enable_stone_cost', 'no');
$stone_cost_label = get_option('jpc_stone_cost_label', 'Stone Cost');
$stone_cost_type = get_option('jpc_stone_cost_type', 'fixed');

$enable_extra_fee = get_option('jpc_enable_extra_fee', 'no');
$extra_fee_label = get_option('jpc_extra_fee_label', 'Extra Fee');
$extra_fee_type = get_option('jpc_extra_fee_type', 'fixed');

$enable_additional_percentage = get_option('jpc_enable_additional_percentage', 'no'); // NEW v2.5.5
$additional_percentage_label = get_option('jpc_additional_percentage_label', 'Additional Percentage');
$additional_percentage_value = get_option('jpc_additional_percentage_value', 0);
$enable_gst = get_option('jpc_enable_gst', 'no');
$gst_label = get_option('jpc_gst_label', 'GST');
$gst_value = get_option('jpc_gst_value', 3);
$gst_gold = get_option('jpc_gst_gold', 3);
$gst_silver = get_option('jpc_gst_silver', 3);
$gst_diamond = get_option('jpc_gst_diamond', 3);
$gst_platinum = get_option('jpc_gst_platinum', 3);
$price_rounding = get_option('jpc_price_rounding', 'none');
$show_price_breakup = get_option('jpc_show_price_breakup', 'yes');

// Extra fields
$extra_fields = array();
for ($i = 1; $i <= 5; $i++) {
    $extra_fields[$i] = array(
        'enabled' => get_option('jpc_enable_extra_field_' . $i, 'no'),
        'label' => get_option('jpc_extra_field_label_' . $i, 'Extra Field ' . $i)
    );
}
?>

<div class="wrap jpc-admin-wrap">
    <h1><?php _e('General Settings', 'jewellery-price-calc'); ?></h1>
    
    <?php if (isset($_GET['settings-updated'])): ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Settings saved successfully!', 'jewellery-price-calc'); ?></p>
        </div>
    <?php endif; ?>
    
    <div class="jpc-admin-content">
        <form method="post" action="options.php">
            <?php settings_fields('jpc_general_settings'); ?>
            
            <!-- Additional Cost Fields Section - v2.5.0 ENHANCED -->
            <div class="jpc-card">
                <h2>
                    <?php _e('Additional Cost Fields', 'jewellery-price-calc'); ?>
                    <span class="jpc-badge jpc-badge-new">v2.5.0 ENHANCED</span>
                </h2>
                <p class="description">
                    <?php _e('Configure additional cost fields with custom labels and choose between fixed price or percentage calculation.', 'jewellery-price-calc'); ?>
                </p>
                
                <!-- Additional Cost Field 1 (Pearl Cost) -->
                <div class="jpc-setting-group">
                    <div class="jpc-setting-header">
                        <label class="jpc-toggle-label">
                            <input type="checkbox" name="jpc_enable_pearl_cost" value="yes" <?php checked($enable_pearl_cost, 'yes'); ?>>
                            <span class="jpc-toggle-text"><?php _e('Enable Additional Cost Field 1', 'jewellery-price-calc'); ?></span>
                        </label>
                    </div>
                    
                    <?php if ($enable_pearl_cost === 'yes'): ?>
                    <div class="jpc-setting-content">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="jpc_pearl_cost_label"><?php _e('Label Name', 'jewellery-price-calc'); ?></label>
                                </th>
                                <td>
                                    <input type="text" 
                                           id="jpc_pearl_cost_label" 
                                           name="jpc_pearl_cost_label" 
                                           value="<?php echo esc_attr($pearl_cost_label); ?>" 
                                           class="regular-text"
                                           placeholder="<?php _e('e.g., Pearl Cost, Gemstone, Certification', 'jewellery-price-calc'); ?>">
                                    <p class="description">
                                        <?php _e('This label will appear in product editor and price breakup.', 'jewellery-price-calc'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="jpc_pearl_cost_type"><?php _e('Calculation Type', 'jewellery-price-calc'); ?></label>
                                </th>
                                <td>
                                    <select id="jpc_pearl_cost_type" name="jpc_pearl_cost_type" class="regular-text">
                                        <option value="fixed" <?php selected($pearl_cost_type, 'fixed'); ?>>
                                            <?php _e('Fixed Price (₹)', 'jewellery-price-calc'); ?>
                                        </option>
                                        <option value="percentage" <?php selected($pearl_cost_type, 'percentage'); ?>>
                                            <?php _e('Percentage (%)', 'jewellery-price-calc'); ?>
                                        </option>
                                    </select>
                                    <p class="description">
                                        <em><?php _e('Fixed: Enter exact amount. Percentage: Calculate based on (Metal + Diamond + Making + Wastage).', 'jewellery-price-calc'); ?></em>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
                
                <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
                
                <!-- Additional Cost Field 2 (Stone Cost) -->
                <div class="jpc-setting-group">
                    <div class="jpc-setting-header">
                        <label class="jpc-toggle-label">
                            <input type="checkbox" name="jpc_enable_stone_cost" value="yes" <?php checked($enable_stone_cost, 'yes'); ?>>
                            <span class="jpc-toggle-text"><?php _e('Enable Additional Cost Field 2', 'jewellery-price-calc'); ?></span>
                        </label>
                    </div>
                    
                    <?php if ($enable_stone_cost === 'yes'): ?>
                    <div class="jpc-setting-content">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="jpc_stone_cost_label"><?php _e('Label Name', 'jewellery-price-calc'); ?></label>
                                </th>
                                <td>
                                    <input type="text" 
                                           id="jpc_stone_cost_label" 
                                           name="jpc_stone_cost_label" 
                                           value="<?php echo esc_attr($stone_cost_label); ?>" 
                                           class="regular-text"
                                           placeholder="<?php _e('e.g., Stone Cost, Packaging, Engraving', 'jewellery-price-calc'); ?>">
                                    <p class="description">
                                        <?php _e('This label will appear in product editor and price breakup.', 'jewellery-price-calc'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="jpc_stone_cost_type"><?php _e('Calculation Type', 'jewellery-price-calc'); ?></label>
                                </th>
                                <td>
                                    <select id="jpc_stone_cost_type" name="jpc_stone_cost_type" class="regular-text">
                                        <option value="fixed" <?php selected($stone_cost_type, 'fixed'); ?>>
                                            <?php _e('Fixed Price (₹)', 'jewellery-price-calc'); ?>
                                        </option>
                                        <option value="percentage" <?php selected($stone_cost_type, 'percentage'); ?>>
                                            <?php _e('Percentage (%)', 'jewellery-price-calc'); ?>
                                        </option>
                                    </select>
                                    <p class="description">
                                        <em><?php _e('Fixed: Enter exact amount. Percentage: Calculate based on (Metal + Diamond + Making + Wastage).', 'jewellery-price-calc'); ?></em>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
                
                <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
                
                <!-- Additional Cost Field 3 (Extra Fee) -->
                <div class="jpc-setting-group">
                    <div class="jpc-setting-header">
                        <label class="jpc-toggle-label">
                            <input type="checkbox" name="jpc_enable_extra_fee" value="yes" <?php checked($enable_extra_fee, 'yes'); ?>>
                            <span class="jpc-toggle-text"><?php _e('Enable Additional Cost Field 3', 'jewellery-price-calc'); ?></span>
                        </label>
                    </div>
                    
                    <?php if ($enable_extra_fee === 'yes'): ?>
                    <div class="jpc-setting-content">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="jpc_extra_fee_label"><?php _e('Label Name', 'jewellery-price-calc'); ?></label>
                                </th>
                                <td>
                                    <input type="text" 
                                           id="jpc_extra_fee_label" 
                                           name="jpc_extra_fee_label" 
                                           value="<?php echo esc_attr($extra_fee_label); ?>" 
                                           class="regular-text"
                                           placeholder="<?php _e('e.g., Extra Fee, Handling, Insurance', 'jewellery-price-calc'); ?>">
                                    <p class="description">
                                        <?php _e('This label will appear in product editor and price breakup.', 'jewellery-price-calc'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="jpc_extra_fee_type"><?php _e('Calculation Type', 'jewellery-price-calc'); ?></label>
                                </th>
                                <td>
                                    <select id="jpc_extra_fee_type" name="jpc_extra_fee_type" class="regular-text">
                                        <option value="fixed" <?php selected($extra_fee_type, 'fixed'); ?>>
                                            <?php _e('Fixed Price (₹)', 'jewellery-price-calc'); ?>
                                        </option>
                                        <option value="percentage" <?php selected($extra_fee_type, 'percentage'); ?>>
                                            <?php _e('Percentage (%)', 'jewellery-price-calc'); ?>
                                        </option>
                                    </select>
                                    <p class="description">
                                        <em><?php _e('Fixed: Enter exact amount. Percentage: Calculate based on (Metal + Diamond + Making + Wastage).', 'jewellery-price-calc'); ?></em>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Additional Percentage - v2.5.5 ENHANCED -->
            <div class="jpc-card">
                <h2>
                    <?php _e('Additional Percentage', 'jewellery-price-calc'); ?>
                    <span class="jpc-badge jpc-badge-new">v2.5.5 ENHANCED</span>
                </h2>
                <p class="description" style="margin-bottom: 20px;">
                    <?php _e('Add a percentage-based charge that applies to the subtotal before discount and GST. Commonly used for payment gateway charges, processing fees, or other percentage-based costs.', 'jewellery-price-calc'); ?>
                </p>
                
                <!-- Enable/Disable Toggle -->
                <div class="jpc-setting-group">
                    <div class="jpc-setting-header">
                        <label class="jpc-toggle-label">
                            <input type="checkbox" name="jpc_enable_additional_percentage" value="yes" <?php checked($enable_additional_percentage, 'yes'); ?>>
                            <span class="jpc-toggle-text"><?php _e('Enable Additional Percentage', 'jewellery-price-calc'); ?></span>
                        </label>
                    </div>
                    
                    <?php if ($enable_additional_percentage === 'yes'): ?>
                    <div class="jpc-setting-content">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="jpc_additional_percentage_label"><?php _e('Label', 'jewellery-price-calc'); ?></label>
                                </th>
                                <td>
                                    <input type="text" 
                                           id="jpc_additional_percentage_label" 
                                           name="jpc_additional_percentage_label" 
                                           value="<?php echo esc_attr($additional_percentage_label); ?>" 
                                           class="regular-text"
                                           placeholder="<?php _e('e.g., Gateway Charges, Processing Fee', 'jewellery-price-calc'); ?>">
                                    <p class="description">
                                        <?php _e('This label will appear in the price breakup.', 'jewellery-price-calc'); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="jpc_additional_percentage_value"><?php _e('Percentage Value', 'jewellery-price-calc'); ?></label>
                                </th>
                                <td>
                                    <input type="number" 
                                           id="jpc_additional_percentage_value" 
                                           name="jpc_additional_percentage_value" 
                                           value="<?php echo esc_attr($additional_percentage_value); ?>" 
                                           step="0.01" 
                                           min="0" 
                                           max="100"
                                           class="small-text">
                                    <span class="description">%</span>
                                    <p class="description">
                                        <?php _e('Enter percentage value (e.g., 2 for 2%)', 'jewellery-price-calc'); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                        
                        <!-- Calculation Documentation -->
                        <div style="background: #f0f6fc; border-left: 4px solid #2271b1; padding: 15px; margin-top: 20px; border-radius: 4px;">
                            <h4 style="margin-top: 0; color: #2271b1; font-size: 15px;">
                                <span class="dashicons dashicons-info" style="font-size: 18px; vertical-align: middle;"></span>
                                <?php _e('How It\'s Calculated', 'jewellery-price-calc'); ?>
                            </h4>
                            <p style="margin: 10px 0; font-size: 14px; line-height: 1.6;">
                                <?php _e('The additional percentage is calculated on the subtotal of:', 'jewellery-price-calc'); ?>
                            </p>
                            <ul style="margin: 10px 0 10px 20px; font-size: 14px; line-height: 1.8;">
                                <li><?php _e('Metal Price', 'jewellery-price-calc'); ?></li>
                                <li><?php _e('Diamond Cost', 'jewellery-price-calc'); ?></li>
                                <li><?php _e('Making Charges', 'jewellery-price-calc'); ?></li>
                                <li><?php _e('Wastage', 'jewellery-price-calc'); ?></li>
                                <li><?php _e('Pearl Cost (if enabled)', 'jewellery-price-calc'); ?></li>
                                <li><?php _e('Stone Cost (if enabled)', 'jewellery-price-calc'); ?></li>
                                <li><?php _e('Extra Fee (if enabled)', 'jewellery-price-calc'); ?></li>
                            </ul>
                            <p style="margin: 15px 0 10px 0; font-size: 14px;">
                                <strong><?php _e('Formula:', 'jewellery-price-calc'); ?></strong>
                                <code style="background: white; padding: 4px 8px; border-radius: 3px; font-size: 13px; display: inline-block; margin-top: 5px;">
                                    (Subtotal × Additional Percentage) ÷ 100
                                </code>
                            </p>
                            <p style="margin: 15px 0 0 0; font-size: 14px; color: #666;">
                                <strong><?php _e('Example:', 'jewellery-price-calc'); ?></strong>
                                <?php _e('If subtotal is ₹10,000 and additional percentage is 2%, then ₹200 will be added.', 'jewellery-price-calc'); ?>
                            </p>
                        </div>
                        
                        <!-- Price Calculation Order -->
                        <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin-top: 15px; border-radius: 4px;">
                            <h4 style="margin-top: 0; color: #856404; font-size: 15px;">
                                <span class="dashicons dashicons-list-view" style="font-size: 18px; vertical-align: middle;"></span>
                                <?php _e('Price Calculation Order', 'jewellery-price-calc'); ?>
                            </h4>
                            <ol style="margin: 10px 0 0 20px; font-size: 14px; line-height: 1.8; color: #856404;">
                                <li><?php _e('Metal + Diamond + Making + Wastage + Pearl + Stone + Extra Fee', 'jewellery-price-calc'); ?></li>
                                <li><strong><?php _e('Additional Percentage is applied here ←', 'jewellery-price-calc'); ?></strong></li>
                                <li><?php _e('Discount is applied (if any)', 'jewellery-price-calc'); ?></li>
                                <li><?php _e('GST/Tax is applied', 'jewellery-price-calc'); ?></li>
                                <li><?php _e('Final Price', 'jewellery-price-calc'); ?></li>
                            </ol>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- GST Settings -->
            <div class="jpc-card">
                <h2><?php _e('Tax/GST Settings', 'jewellery-price-calc'); ?></h2>
                <table class="form-table jpc-form">
                    <tr>
                        <th scope="row">
                            <label for="jpc_enable_gst"><?php _e('Enable Tax/GST', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="jpc_enable_gst" name="jpc_enable_gst" value="yes" <?php checked($enable_gst, 'yes'); ?>>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="jpc_gst_label"><?php _e('Tax Label', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="jpc_gst_label" name="jpc_gst_label" value="<?php echo esc_attr($gst_label); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="jpc_gst_gold"><?php _e('Gold Tax (%)', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="jpc_gst_gold" name="jpc_gst_gold" value="<?php echo esc_attr($gst_gold); ?>" step="0.01" min="0" class="small-text">
                            <span class="description">%</span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="jpc_gst_silver"><?php _e('Silver Tax (%)', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="jpc_gst_silver" name="jpc_gst_silver" value="<?php echo esc_attr($gst_silver); ?>" step="0.01" min="0" class="small-text">
                            <span class="description">%</span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="jpc_gst_diamond"><?php _e('Diamond Tax (%)', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="jpc_gst_diamond" name="jpc_gst_diamond" value="<?php echo esc_attr($gst_diamond); ?>" step="0.01" min="0" class="small-text">
                            <span class="description">%</span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="jpc_gst_platinum"><?php _e('Platinum Tax (%)', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="jpc_gst_platinum" name="jpc_gst_platinum" value="<?php echo esc_attr($gst_platinum); ?>" step="0.01" min="0" class="small-text">
                            <span class="description">%</span>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Extra Fields -->
            <div class="jpc-card">
                <h2><?php _e('Extra Fields', 'jewellery-price-calc'); ?></h2>
                <p class="description"><?php _e('Enable up to 5 additional custom fields for product pricing.', 'jewellery-price-calc'); ?></p>
                
                <?php for ($i = 1; $i <= 5; $i++): ?>
                <div style="margin-bottom: 20px; padding: 15px; background: #f9f9f9; border-left: 4px solid #2271b1;">
                    <table class="form-table jpc-form">
                        <tr>
                            <th scope="row">
                                <label for="jpc_enable_extra_field_<?php echo $i; ?>">
                                    <?php printf(__('Enable Extra Field %d', 'jewellery-price-calc'), $i); ?>
                                </label>
                            </th>
                            <td>
                                <input type="checkbox" 
                                       id="jpc_enable_extra_field_<?php echo $i; ?>" 
                                       name="jpc_enable_extra_field_<?php echo $i; ?>" 
                                       value="yes" 
                                       <?php checked($extra_fields[$i]['enabled'], 'yes'); ?>>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="jpc_extra_field_label_<?php echo $i; ?>">
                                    <?php _e('Field Label', 'jewellery-price-calc'); ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" 
                                       id="jpc_extra_field_label_<?php echo $i; ?>" 
                                       name="jpc_extra_field_label_<?php echo $i; ?>" 
                                       value="<?php echo esc_attr($extra_fields[$i]['label']); ?>" 
                                       class="regular-text">
                            </td>
                        </tr>
                    </table>
                </div>
                <?php endfor; ?>
            </div>
            
            <!-- Display Settings -->
            <div class="jpc-card">
                <h2><?php _e('Display Settings', 'jewellery-price-calc'); ?></h2>
                <table class="form-table jpc-form">
                    <tr>
                        <th scope="row">
                            <label for="jpc_price_rounding"><?php _e('Price Rounding', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <select id="jpc_price_rounding" name="jpc_price_rounding">
                                <option value="none" <?php selected($price_rounding, 'none'); ?>><?php _e('No Rounding', 'jewellery-price-calc'); ?></option>
                                <option value="nearest_10" <?php selected($price_rounding, 'nearest_10'); ?>><?php _e('Nearest 10', 'jewellery-price-calc'); ?></option>
                                <option value="nearest_50" <?php selected($price_rounding, 'nearest_50'); ?>><?php _e('Nearest 50', 'jewellery-price-calc'); ?></option>
                                <option value="nearest_100" <?php selected($price_rounding, 'nearest_100'); ?>><?php _e('Nearest 100', 'jewellery-price-calc'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="jpc_show_price_breakup"><?php _e('Show Price Breakup', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="jpc_show_price_breakup" name="jpc_show_price_breakup" value="yes" <?php checked($show_price_breakup, 'yes'); ?>>
                            <p class="description"><?php _e('Display detailed price breakdown on product pages', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <?php submit_button(__('Save Changes', 'jewellery-price-calc')); ?>
        </form>
    </div>
</div>

<style>
.jpc-badge {
    display: inline-block;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 3px;
    margin-left: 10px;
    vertical-align: middle;
}

.jpc-badge-new {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

.jpc-setting-group {
    margin-bottom: 20px;
}

.jpc-setting-header {
    margin-bottom: 15px;
}

.jpc-toggle-label {
    display: flex;
    align-items: center;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
}

.jpc-toggle-label input[type="checkbox"] {
    margin-right: 10px;
    width: 20px;
    height: 20px;
}

.jpc-toggle-text {
    color: #1d2327;
}

.jpc-setting-content {
    padding-left: 30px;
    border-left: 3px solid #2271b1;
    margin-top: 15px;
}
</style>
