<?php
/**
 * General Settings Template v2.5.5 SIMPLIFIED
 * - Additional Cost Fields (Pearl, Stone, Extra Fee) with custom labels and types
 * - Additional Percentage with enable/disable and documentation
 * - GST with enable/disable (simplified - generic rate only)
 * - Extra Fields 1-5
 * - Display Settings
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

$enable_additional_percentage = get_option('jpc_enable_additional_percentage', 'no');
$additional_percentage_label = get_option('jpc_additional_percentage_label', 'Additional Percentage');
$additional_percentage_value = get_option('jpc_additional_percentage_value', '0');

$enable_gst = get_option('jpc_enable_gst', 'yes');
$gst_label = get_option('jpc_gst_label', 'GST');
$gst_value = get_option('jpc_gst_value', '3');
$gst_calculation_base = get_option('jpc_gst_calculation_base', 'after_discount');

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
            
            <!-- Additional Percentage Settings -->
            <div class="jpc-settings-section" style="background: #fff; padding: 20px; margin: 20px 0; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
                <h2 style="margin-top: 0; padding-bottom: 10px; border-bottom: 2px solid #2271b1;">
                    Additional Percentage Settings
                    <span class="jpc-badge jpc-badge-new">v2.5.5 NEW</span>
                </h2>
                
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="jpc_enable_additional_percentage">Enable Additional Percentage</label>
                        </th>
                        <td>
                            <input type="checkbox" 
                                   id="jpc_enable_additional_percentage" 
                                   name="jpc_enable_additional_percentage" 
                                   value="yes" 
                                   <?php checked($enable_additional_percentage, 'yes'); ?>>
                            <p class="description">Check to enable additional percentage calculation on product prices.</p>
                        </td>
                    </tr>
                </table>
                
                <div id="additional_percentage_settings" style="<?php echo ($enable_additional_percentage !== 'yes') ? 'display:none;' : ''; ?>">
                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row">
                                <label for="jpc_additional_percentage_label">Additional Percentage Label</label>
                            </th>
                            <td>
                                <input type="text" 
                                       id="jpc_additional_percentage_label" 
                                       name="jpc_additional_percentage_label" 
                                       value="<?php echo esc_attr($additional_percentage_label); ?>" 
                                       class="regular-text">
                                <p class="description">Label to display for this charge (e.g., "Service Charge", "Handling Fee").</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="jpc_additional_percentage_value">Additional Percentage Value (%)</label>
                            </th>
                            <td>
                                <input type="number" 
                                       id="jpc_additional_percentage_value" 
                                       name="jpc_additional_percentage_value" 
                                       value="<?php echo esc_attr($additional_percentage_value); ?>" 
                                       step="0.01" 
                                       min="0" 
                                       class="small-text"> %
                                <p class="description">Percentage to add to the subtotal (e.g., 5 for 5%).</p>
                            </td>
                        </tr>
                    </table>
                    
                    <!-- Documentation Section -->
                    <div style="background: #f0f6fc; border-left: 4px solid #2271b1; padding: 15px; margin: 20px 0;">
                        <h3 style="margin-top: 0; color: #2271b1;">📊 How Additional Percentage is Calculated</h3>
                        
                        <div style="background: white; padding: 15px; border-radius: 4px; margin: 10px 0;">
                            <h4 style="margin-top: 0;">Calculation Base:</h4>
                            <p style="margin: 5px 0;">Additional Percentage is calculated on the <strong>Subtotal Before Additional Percentage</strong>, which includes:</p>
                            <ul style="margin: 10px 0; padding-left: 20px;">
                                <li>✓ Metal Price</li>
                                <li>✓ Diamond Price</li>
                                <li>✓ Making Charges</li>
                                <li>✓ Wastage Charges</li>
                                <li>✓ Additional Cost Field 1 (if enabled)</li>
                                <li>✓ Additional Cost Field 2 (if enabled)</li>
                                <li>✓ Additional Cost Field 3 (if enabled)</li>
                                <li>✓ Extra Fields 1-5 (if enabled)</li>
                            </ul>
                        </div>
                        
                        <div style="background: #fff3cd; padding: 15px; border-radius: 4px; margin: 10px 0; border: 1px solid #ffc107;">
                            <h4 style="margin-top: 0; color: #856404;">💡 Example Calculation:</h4>
                            <p style="margin: 5px 0; font-family: monospace; font-size: 13px;">
                                <strong>If Additional Percentage = 5%</strong><br>
                                Metal Price: ₹10,000<br>
                                Diamond Price: ₹5,000<br>
                                Making Charges: ₹2,000<br>
                                Wastage: ₹500<br>
                                Additional Cost Field 1: ₹1,000<br>
                                <span style="border-top: 1px solid #856404; display: block; margin: 5px 0;"></span>
                                <strong>Subtotal Before Additional %: ₹18,500</strong><br>
                                <strong style="color: #2271b1;">Additional Percentage (5%): ₹925</strong><br>
                                <span style="border-top: 1px solid #856404; display: block; margin: 5px 0;"></span>
                                <strong>Subtotal After Additional %: ₹19,425</strong>
                            </p>
                        </div>
                        
                        <div style="background: white; padding: 15px; border-radius: 4px; margin: 10px 0;">
                            <h4 style="margin-top: 0;">📋 Price Calculation Order:</h4>
                            <ol style="margin: 10px 0; padding-left: 20px; line-height: 1.8;">
                                <li><strong>Base Components:</strong> Metal + Diamond + Making + Wastage + Additional Cost Fields + Extra Fields</li>
                                <li><strong>Additional Percentage:</strong> Applied on above subtotal</li>
                                <li><strong>Discount:</strong> Applied based on selected discount calculation method</li>
                                <li><strong>GST:</strong> Applied on final amount (before or after discount based on settings)</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tax/GST Settings - SIMPLIFIED -->
            <div class="jpc-settings-section" style="background: #fff; padding: 20px; margin: 20px 0; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
                <h2 style="margin-top: 0; padding-bottom: 10px; border-bottom: 2px solid #2271b1;">
                    Tax/GST Settings
                    <span class="jpc-badge jpc-badge-new">v2.5.5 ENHANCED</span>
                </h2>
                
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="jpc_enable_gst">Enable Tax/GST</label>
                        </th>
                        <td>
                            <input type="checkbox" 
                                   id="jpc_enable_gst" 
                                   name="jpc_enable_gst" 
                                   value="yes" 
                                   <?php checked($enable_gst, 'yes'); ?>>
                            <p class="description">Check to enable GST/Tax calculation on product prices.</p>
                        </td>
                    </tr>
                </table>
                
                <div id="gst_settings" style="<?php echo ($enable_gst !== 'yes') ? 'display:none;' : ''; ?>">
                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row">
                                <label for="jpc_gst_label">Tax Label</label>
                            </th>
                            <td>
                                <input type="text" 
                                       id="jpc_gst_label" 
                                       name="jpc_gst_label" 
                                       value="<?php echo esc_attr($gst_label); ?>" 
                                       class="regular-text">
                                <p class="description">Label to display for tax (e.g., "GST", "VAT", "Tax").</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="jpc_gst_value">GST Percentage (%)</label>
                            </th>
                            <td>
                                <input type="number" 
                                       id="jpc_gst_value" 
                                       name="jpc_gst_value" 
                                       value="<?php echo esc_attr($gst_value); ?>" 
                                       step="0.01" 
                                       min="0" 
                                       class="small-text"> %
                                <p class="description">Tax percentage to apply on all products.</p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="jpc_gst_calculation_base">GST Calculation Base</label>
                            </th>
                            <td>
                                <select id="jpc_gst_calculation_base" name="jpc_gst_calculation_base" class="regular-text">
                                    <option value="after_discount" <?php selected($gst_calculation_base, 'after_discount'); ?>>
                                        After Discount (Recommended)
                                    </option>
                                    <option value="before_discount" <?php selected($gst_calculation_base, 'before_discount'); ?>>
                                        Original Price (Before Discount)
                                    </option>
                                </select>
                                <p class="description">Choose whether GST is calculated on the original price or after applying discount.</p>
                            </td>
                        </tr>
                    </table>
                    
                    <!-- GST Documentation Section -->
                    <div style="background: #f0f6fc; border-left: 4px solid #2271b1; padding: 15px; margin: 20px 0;">
                        <h3 style="margin-top: 0; color: #2271b1;">📊 How GST is Calculated</h3>
                        
                        <div style="background: white; padding: 15px; border-radius: 4px; margin: 10px 0;">
                            <h4 style="margin-top: 0;">GST Calculation Base:</h4>
                            <p style="margin: 5px 0;">GST is calculated based on your selected method:</p>
                            
                            <div style="margin: 15px 0; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                                <strong>Option 1: After Discount (Recommended)</strong>
                                <ul style="margin: 10px 0; padding-left: 20px;">
                                    <li>GST is calculated on the <strong>discounted price</strong></li>
                                    <li>Customer pays less GST when discount is applied</li>
                                    <li>More customer-friendly approach</li>
                                </ul>
                            </div>
                            
                            <div style="margin: 15px 0; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                                <strong>Option 2: Original Price (Before Discount)</strong>
                                <ul style="margin: 10px 0; padding-left: 20px;">
                                    <li>GST is calculated on the <strong>original price</strong></li>
                                    <li>GST amount remains same regardless of discount</li>
                                    <li>Discount only reduces the base price, not GST</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div style="background: #fff3cd; padding: 15px; border-radius: 4px; margin: 10px 0; border: 1px solid #ffc107;">
                            <h4 style="margin-top: 0; color: #856404;">💡 Example Calculation:</h4>
                            
                            <div style="margin: 10px 0;">
                                <strong>Scenario: Product with 3% GST and 10% Discount</strong>
                                <p style="margin: 5px 0; font-family: monospace; font-size: 13px;">
                                    Metal Price: ₹10,000<br>
                                    Making Charges: ₹2,000<br>
                                    Additional Percentage (5%): ₹600<br>
                                    <span style="border-top: 1px solid #856404; display: block; margin: 5px 0;"></span>
                                    <strong>Subtotal After Additional %: ₹12,600</strong><br>
                                    Discount (10%): ₹1,260<br>
                                    <strong>Subtotal After Discount: ₹11,340</strong>
                                </p>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px;">
                                <div style="background: white; padding: 10px; border-radius: 4px; border: 1px solid #dee2e6;">
                                    <strong style="color: #28a745;">✓ After Discount Method:</strong>
                                    <p style="margin: 5px 0; font-family: monospace; font-size: 12px;">
                                        GST Base: ₹11,340<br>
                                        GST (3%): ₹340.20<br>
                                        <strong>Final Price: ₹11,680.20</strong>
                                    </p>
                                </div>
                                
                                <div style="background: white; padding: 10px; border-radius: 4px; border: 1px solid #dee2e6;">
                                    <strong style="color: #dc3545;">✓ Original Price Method:</strong>
                                    <p style="margin: 5px 0; font-family: monospace; font-size: 12px;">
                                        GST Base: ₹12,600<br>
                                        GST (3%): ₹378<br>
                                        <strong>Final Price: ₹11,718</strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div style="background: white; padding: 15px; border-radius: 4px; margin: 10px 0;">
                            <h4 style="margin-top: 0;">📋 Complete Price Calculation Order:</h4>
                            <ol style="margin: 10px 0; padding-left: 20px; line-height: 1.8;">
                                <li><strong>Base Components:</strong> Metal + Diamond + Making + Wastage + Additional Cost Fields + Extra Fields</li>
                                <li><strong>Additional Percentage:</strong> Applied on above subtotal (if enabled)</li>
                                <li><strong>Discount:</strong> Applied based on selected discount calculation method</li>
                                <li><strong>GST:</strong> Applied on final amount based on your selected GST calculation base:
                                    <ul style="margin: 5px 0; padding-left: 20px;">
                                        <li>If "After Discount": GST on (Subtotal - Discount)</li>
                                        <li>If "Original Price": GST on Subtotal (before discount)</li>
                                    </ul>
                                </li>
                                <li><strong>Final Price:</strong> Subtotal ± Discount + GST</li>
                            </ol>
                        </div>
                    </div>
                </div>
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

<script>
jQuery(document).ready(function($) {
    // Toggle Additional Percentage settings
    $('#jpc_enable_additional_percentage').on('change', function() {
        if ($(this).is(':checked')) {
            $('#additional_percentage_settings').slideDown();
        } else {
            $('#additional_percentage_settings').slideUp();
        }
    });
    
    // Toggle GST settings
    $('#jpc_enable_gst').on('change', function() {
        if ($(this).is(':checked')) {
            $('#gst_settings').slideDown();
        } else {
            $('#gst_settings').slideUp();
        }
    });
});
</script>

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

.jpc-settings-wrap {
    max-width: 1200px;
}

.jpc-settings-section {
    border-radius: 4px;
}

.jpc-settings-section h2 {
    color: #1d2327;
    font-size: 1.3em;
}

.jpc-settings-section h3 {
    font-size: 1.1em;
}

.jpc-settings-section h4 {
    font-size: 1em;
    color: #2c3338;
}

.form-table th {
    width: 250px;
    padding: 15px 10px 15px 0;
}

.form-table td {
    padding: 15px 10px;
}

.description {
    color: #646970;
    font-style: italic;
}
</style>
