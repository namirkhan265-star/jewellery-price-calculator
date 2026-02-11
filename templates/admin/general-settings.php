<?php
/**
 * General Settings Template v2.5.5
 * - Additional Percentage with enable/disable and documentation
 * - GST with enable/disable and calculation transparency
 * - Additional Cost Fields renamed for clarity
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get current settings
$enable_additional_percentage = get_option('jpc_enable_additional_percentage', 'no');
$additional_percentage_label = get_option('jpc_additional_percentage_label', 'Additional Percentage');
$additional_percentage_value = get_option('jpc_additional_percentage_value', '0');

$enable_gst = get_option('jpc_enable_gst', 'yes');
$gst_label = get_option('jpc_gst_label', 'GST');
$gst_gold = get_option('jpc_gst_gold', '3');
$gst_silver = get_option('jpc_gst_silver', '3');
$gst_platinum = get_option('jpc_gst_platinum', '3');
$gst_default = get_option('jpc_gst_default', '3');
$gst_calculation_base = get_option('jpc_gst_calculation_base', 'after_discount');

$discount_calculation_method = get_option('jpc_discount_calculation_method', '1');
?>

<div class="wrap jpc-settings-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('jpc_general_settings');
        do_settings_sections('jpc_general_settings');
        ?>
        
        <!-- Additional Percentage Settings -->
        <div class="jpc-settings-section" style="background: #fff; padding: 20px; margin: 20px 0; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
            <h2 style="margin-top: 0; padding-bottom: 10px; border-bottom: 2px solid #2271b1;">Additional Percentage Settings</h2>
            
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
        
        <!-- Tax/GST Settings -->
        <div class="jpc-settings-section" style="background: #fff; padding: 20px; margin: 20px 0; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
            <h2 style="margin-top: 0; padding-bottom: 10px; border-bottom: 2px solid #2271b1;">Tax/GST Settings</h2>
            
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
                            <label for="jpc_gst_gold">Gold Tax (%)</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="jpc_gst_gold" 
                                   name="jpc_gst_gold" 
                                   value="<?php echo esc_attr($gst_gold); ?>" 
                                   step="0.01" 
                                   min="0" 
                                   class="small-text"> %
                            <p class="description">Tax percentage for gold products.</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="jpc_gst_silver">Silver Tax (%)</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="jpc_gst_silver" 
                                   name="jpc_gst_silver" 
                                   value="<?php echo esc_attr($gst_silver); ?>" 
                                   step="0.01" 
                                   min="0" 
                                   class="small-text"> %
                            <p class="description">Tax percentage for silver products.</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="jpc_gst_platinum">Platinum Tax (%)</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="jpc_gst_platinum" 
                                   name="jpc_gst_platinum" 
                                   value="<?php echo esc_attr($gst_platinum); ?>" 
                                   step="0.01" 
                                   min="0" 
                                   class="small-text"> %
                            <p class="description">Tax percentage for platinum products.</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="jpc_gst_default">Default Tax (%)</label>
                        </th>
                        <td>
                            <input type="number" 
                                   id="jpc_gst_default" 
                                   name="jpc_gst_default" 
                                   value="<?php echo esc_attr($gst_default); ?>" 
                                   step="0.01" 
                                   min="0" 
                                   class="small-text"> %
                            <p class="description">Default tax percentage for other metal types.</p>
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
                                <option value="original_price" <?php selected($gst_calculation_base, 'original_price'); ?>>
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
                            <strong>Scenario: Gold Product with 3% GST and 10% Discount</strong>
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
                        <h4 style="margin-top: 0;">🎯 Metal-Specific GST Rates:</h4>
                        <p style="margin: 5px 0;">The plugin automatically applies the correct GST rate based on the metal group:</p>
                        <ul style="margin: 10px 0; padding-left: 20px; line-height: 1.8;">
                            <li><strong>Gold Products:</strong> Uses "Gold Tax (%)" setting</li>
                            <li><strong>Silver Products:</strong> Uses "Silver Tax (%)" setting</li>
                            <li><strong>Platinum Products:</strong> Uses "Platinum Tax (%)" setting</li>
                            <li><strong>Other Metals:</strong> Uses "Default Tax (%)" setting</li>
                        </ul>
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
        
        <!-- Discount Calculation Method -->
        <div class="jpc-settings-section" style="background: #fff; padding: 20px; margin: 20px 0; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
            <h2 style="margin-top: 0; padding-bottom: 10px; border-bottom: 2px solid #2271b1;">Discount Settings</h2>
            
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="jpc_discount_calculation_method">Discount Calculation Method</label>
                    </th>
                    <td>
                        <select id="jpc_discount_calculation_method" name="jpc_discount_calculation_method" class="regular-text">
                            <option value="1" <?php selected($discount_calculation_method, '1'); ?>>
                                Simple: Metal + Making + Wastage
                            </option>
                            <option value="2" <?php selected($discount_calculation_method, '2'); ?>>
                                Advanced: All Components (Before Additional %)
                            </option>
                            <option value="3" <?php selected($discount_calculation_method, '3'); ?>>
                                Total Before GST (Recommended)
                            </option>
                        </select>
                        <p class="description">Choose which components to include when calculating discount percentage.</p>
                    </td>
                </tr>
            </table>
        </div>
        
        <?php submit_button('Save Settings'); ?>
    </form>
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
