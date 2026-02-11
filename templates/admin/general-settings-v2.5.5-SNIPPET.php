<?php
/**
 * SNIPPET: Enhanced Additional Percentage Section for general-settings.php
 * v2.5.5: Added enable/disable checkbox and comprehensive documentation
 * 
 * REPLACE the existing "Additional Percentage" section (around line 226-250)
 * with this enhanced version
 */

// Add this at the top with other settings (around line 23):
$enable_additional_percentage = get_option('jpc_enable_additional_percentage', 'no');
?>

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
