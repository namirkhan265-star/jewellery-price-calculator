<?php
/**
 * Discount Settings Page Template - Enhanced with Multiple Calculation Methods
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get current settings
$discount_method = get_option('jpc_discount_calculation_method', 'simple');
$discount_timing = get_option('jpc_discount_timing', 'before_additional');
$gst_calculation_base = get_option('jpc_gst_calculation_base', 'after_discount');
?>

<div class="wrap jpc-admin-wrap">
    <h1><?php _e('Discount Calculation Settings', 'jewellery-price-calc'); ?></h1>
    
    <p class="description" style="font-size: 14px; margin-bottom: 20px;">
        <?php _e('Configure how discounts are calculated and applied to your products. Choose the method that best fits your business requirements.', 'jewellery-price-calc'); ?>
    </p>
    
    <form method="post" action="options.php">
        <?php settings_fields('jpc_discount_settings'); ?>
        
        <!-- SECTION 1: ENABLE DISCOUNT -->
        <div class="jpc-card" style="margin-bottom: 20px;">
            <h2 style="margin-top: 0;"><?php _e('1. Enable Discount Feature', 'jewellery-price-calc'); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="jpc_enable_discount"><?php _e('Enable Discount', 'jewellery-price-calc'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" id="jpc_enable_discount" name="jpc_enable_discount" value="yes" <?php checked(get_option('jpc_enable_discount'), 'yes'); ?>>
                            <?php _e('Enable discount calculations', 'jewellery-price-calc'); ?>
                        </label>
                        <p class="description">
                            <strong><?php _e('Note:', 'jewellery-price-calc'); ?></strong> 
                            <?php _e('This will automatically update product sale prices. If you want to manually set sale prices, keep this disabled.', 'jewellery-price-calc'); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- SECTION 2: DISCOUNT CALCULATION METHOD -->
        <div class="jpc-card" style="margin-bottom: 20px;">
            <h2 style="margin-top: 0;"><?php _e('2. Discount Calculation Method', 'jewellery-price-calc'); ?></h2>
            <p class="description" style="margin-bottom: 15px;">
                <?php _e('Choose how discount should be calculated. Each method applies discount differently.', 'jewellery-price-calc'); ?>
            </p>
            
            <table class="form-table">
                <tr>
                    <td colspan="2">
                        <!-- METHOD 1: SIMPLE (COMPONENT-BASED) -->
                        <div style="border: 2px solid <?php echo ($discount_method === 'simple') ? '#2271b1' : '#ddd'; ?>; padding: 15px; margin-bottom: 15px; border-radius: 5px; background: <?php echo ($discount_method === 'simple') ? '#f0f6fc' : '#fff'; ?>;">
                            <label style="display: flex; align-items: flex-start; cursor: pointer;">
                                <input type="radio" name="jpc_discount_calculation_method" value="simple" <?php checked($discount_method, 'simple'); ?> style="margin-top: 3px; margin-right: 10px;">
                                <div>
                                    <strong style="font-size: 15px; display: block; margin-bottom: 5px;">
                                        <?php _e('Method 1: Simple (Component-Based)', 'jewellery-price-calc'); ?>
                                    </strong>
                                    <p class="description" style="margin: 5px 0;">
                                        <?php _e('Apply discount only on selected components (Metal, Making, Wastage). Other costs are not discounted.', 'jewellery-price-calc'); ?>
                                    </p>
                                    <div style="background: #fff; padding: 10px; margin-top: 10px; border-left: 3px solid #2271b1; font-family: monospace; font-size: 12px;">
                                        <strong><?php _e('Example:', 'jewellery-price-calc'); ?></strong><br>
                                        Metal: ₹30,000 | Making: ₹9,000 | Wastage: ₹4,000 | Diamond: ₹5,000<br>
                                        <em><?php _e('If discount on Metal + Making enabled:', 'jewellery-price-calc'); ?></em><br>
                                        Discountable = ₹30,000 + ₹9,000 = ₹39,000<br>
                                        30% Discount = ₹11,700<br>
                                        <strong><?php _e('Final:', 'jewellery-price-calc'); ?></strong> (₹30,000 + ₹9,000 + ₹4,000 + ₹5,000) - ₹11,700 = ₹36,300
                                    </div>
                                </div>
                            </label>
                        </div>
                        
                        <!-- METHOD 2: ADVANCED (ALL COMPONENTS) -->
                        <div style="border: 2px solid <?php echo ($discount_method === 'advanced') ? '#2271b1' : '#ddd'; ?>; padding: 15px; margin-bottom: 15px; border-radius: 5px; background: <?php echo ($discount_method === 'advanced') ? '#f0f6fc' : '#fff'; ?>;">
                            <label style="display: flex; align-items: flex-start; cursor: pointer;">
                                <input type="radio" name="jpc_discount_calculation_method" value="advanced" <?php checked($discount_method, 'advanced'); ?> style="margin-top: 3px; margin-right: 10px;">
                                <div>
                                    <strong style="font-size: 15px; display: block; margin-bottom: 5px;">
                                        <?php _e('Method 2: Advanced (All Components)', 'jewellery-price-calc'); ?>
                                    </strong>
                                    <p class="description" style="margin: 5px 0;">
                                        <?php _e('Apply discount on ALL cost components including Diamond, Pearl, Stone, Extra Fees, and Extra Fields.', 'jewellery-price-calc'); ?>
                                    </p>
                                    <div style="background: #fff; padding: 10px; margin-top: 10px; border-left: 3px solid #2271b1; font-family: monospace; font-size: 12px;">
                                        <strong><?php _e('Example:', 'jewellery-price-calc'); ?></strong><br>
                                        Metal: ₹30,000 | Making: ₹9,000 | Wastage: ₹4,000 | Diamond: ₹5,000<br>
                                        Total = ₹48,000<br>
                                        30% Discount = ₹14,400<br>
                                        <strong><?php _e('Final:', 'jewellery-price-calc'); ?></strong> ₹48,000 - ₹14,400 = ₹33,600
                                    </div>
                                </div>
                            </label>
                        </div>
                        
                        <!-- METHOD 3: TOTAL BEFORE GST -->
                        <div style="border: 2px solid <?php echo ($discount_method === 'total_before_gst') ? '#2271b1' : '#ddd'; ?>; padding: 15px; margin-bottom: 15px; border-radius: 5px; background: <?php echo ($discount_method === 'total_before_gst') ? '#f0f6fc' : '#fff'; ?>;">
                            <label style="display: flex; align-items: flex-start; cursor: pointer;">
                                <input type="radio" name="jpc_discount_calculation_method" value="total_before_gst" <?php checked($discount_method, 'total_before_gst'); ?> style="margin-top: 3px; margin-right: 10px;">
                                <div>
                                    <strong style="font-size: 15px; display: block; margin-bottom: 5px;">
                                        <?php _e('Method 3: Total Before GST', 'jewellery-price-calc'); ?>
                                    </strong>
                                    <p class="description" style="margin: 5px 0;">
                                        <?php _e('Apply discount on the complete subtotal (after Additional %) but before GST. GST is calculated on discounted amount.', 'jewellery-price-calc'); ?>
                                    </p>
                                    <div style="background: #fff; padding: 10px; margin-top: 10px; border-left: 3px solid #2271b1; font-family: monospace; font-size: 12px;">
                                        <strong><?php _e('Example:', 'jewellery-price-calc'); ?></strong><br>
                                        Subtotal (with Additional %): ₹50,000<br>
                                        30% Discount = ₹15,000<br>
                                        After Discount: ₹35,000<br>
                                        GST (3% on ₹35,000) = ₹1,050<br>
                                        <strong><?php _e('Final:', 'jewellery-price-calc'); ?></strong> ₹35,000 + ₹1,050 = ₹36,050
                                    </div>
                                </div>
                            </label>
                        </div>
                        
                        <!-- METHOD 4: TOTAL AFTER ADDITIONAL % -->
                        <div style="border: 2px solid <?php echo ($discount_method === 'total_after_additional') ? '#2271b1' : '#ddd'; ?>; padding: 15px; margin-bottom: 15px; border-radius: 5px; background: <?php echo ($discount_method === 'total_after_additional') ? '#f0f6fc' : '#fff'; ?>;">
                            <label style="display: flex; align-items: flex-start; cursor: pointer;">
                                <input type="radio" name="jpc_discount_calculation_method" value="total_after_additional" <?php checked($discount_method, 'total_after_additional'); ?> style="margin-top: 3px; margin-right: 10px;">
                                <div>
                                    <strong style="font-size: 15px; display: block; margin-bottom: 5px;">
                                        <?php _e('Method 4: Total After Additional %', 'jewellery-price-calc'); ?>
                                    </strong>
                                    <p class="description" style="margin: 5px 0;">
                                        <?php _e('Apply discount on subtotal after Additional Percentage is added. Most comprehensive discount.', 'jewellery-price-calc'); ?>
                                    </p>
                                    <div style="background: #fff; padding: 10px; margin-top: 10px; border-left: 3px solid #2271b1; font-family: monospace; font-size: 12px;">
                                        <strong><?php _e('Example:', 'jewellery-price-calc'); ?></strong><br>
                                        Base Total: ₹48,000<br>
                                        Additional % (5%): ₹2,400<br>
                                        Subtotal: ₹50,400<br>
                                        30% Discount = ₹15,120<br>
                                        <strong><?php _e('Final:', 'jewellery-price-calc'); ?></strong> ₹50,400 - ₹15,120 = ₹35,280 (+ GST)
                                    </div>
                                </div>
                            </label>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- SECTION 3: COMPONENT SELECTION (Only for Simple Method) -->
        <div class="jpc-card" id="component-selection" style="margin-bottom: 20px; <?php echo ($discount_method !== 'simple') ? 'display: none;' : ''; ?>">
            <h2 style="margin-top: 0;"><?php _e('3. Select Components for Discount (Simple Method Only)', 'jewellery-price-calc'); ?></h2>
            <p class="description" style="margin-bottom: 15px;">
                <?php _e('Choose which components should be included when calculating discount.', 'jewellery-price-calc'); ?>
            </p>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Discount Components', 'jewellery-price-calc'); ?></th>
                    <td>
                        <label style="display: block; margin-bottom: 10px;">
                            <input type="checkbox" name="jpc_discount_on_metals" value="yes" <?php checked(get_option('jpc_discount_on_metals'), 'yes'); ?>>
                            <?php _e('Apply discount on Metal Price', 'jewellery-price-calc'); ?>
                        </label>
                        
                        <label style="display: block; margin-bottom: 10px;">
                            <input type="checkbox" name="jpc_discount_on_making" value="yes" <?php checked(get_option('jpc_discount_on_making'), 'yes'); ?>>
                            <?php _e('Apply discount on Making Charge', 'jewellery-price-calc'); ?>
                        </label>
                        
                        <label style="display: block; margin-bottom: 10px;">
                            <input type="checkbox" name="jpc_discount_on_wastage" value="yes" <?php checked(get_option('jpc_discount_on_wastage'), 'yes'); ?>>
                            <?php _e('Apply discount on Wastage Charge', 'jewellery-price-calc'); ?>
                        </label>
                        
                        <p class="description">
                            <?php _e('If no components are selected, discount will apply to the entire subtotal (backward compatibility).', 'jewellery-price-calc'); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- SECTION 4: DISCOUNT TIMING -->
        <div class="jpc-card" style="margin-bottom: 20px;">
            <h2 style="margin-top: 0;"><?php _e('4. Discount Application Timing', 'jewellery-price-calc'); ?></h2>
            <p class="description" style="margin-bottom: 15px;">
                <?php _e('When should the discount be applied in the calculation sequence?', 'jewellery-price-calc'); ?>
            </p>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Apply Discount', 'jewellery-price-calc'); ?></th>
                    <td>
                        <label style="display: block; margin-bottom: 10px;">
                            <input type="radio" name="jpc_discount_timing" value="before_additional" <?php checked($discount_timing, 'before_additional'); ?>>
                            <strong><?php _e('Before Additional Percentage', 'jewellery-price-calc'); ?></strong>
                            <p class="description" style="margin-left: 25px;">
                                <?php _e('Discount → Additional % → GST (Discount doesn\'t include Additional %)', 'jewellery-price-calc'); ?>
                            </p>
                        </label>
                        
                        <label style="display: block; margin-bottom: 10px;">
                            <input type="radio" name="jpc_discount_timing" value="after_additional" <?php checked($discount_timing, 'after_additional'); ?>>
                            <strong><?php _e('After Additional Percentage', 'jewellery-price-calc'); ?></strong>
                            <p class="description" style="margin-left: 25px;">
                                <?php _e('Additional % → Discount → GST (Discount includes Additional %)', 'jewellery-price-calc'); ?>
                            </p>
                        </label>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- SECTION 5: GST CALCULATION BASE -->
        <div class="jpc-card" style="margin-bottom: 20px;">
            <h2 style="margin-top: 0;"><?php _e('5. GST Calculation Base', 'jewellery-price-calc'); ?></h2>
            <p class="description" style="margin-bottom: 15px;">
                <?php _e('Should GST be calculated on the original price or discounted price?', 'jewellery-price-calc'); ?>
            </p>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Calculate GST On', 'jewellery-price-calc'); ?></th>
                    <td>
                        <label style="display: block; margin-bottom: 10px;">
                            <input type="radio" name="jpc_gst_calculation_base" value="after_discount" <?php checked($gst_calculation_base, 'after_discount'); ?>>
                            <strong><?php _e('Discounted Price (Recommended)', 'jewellery-price-calc'); ?></strong>
                            <p class="description" style="margin-left: 25px;">
                                <?php _e('GST is calculated on the price after discount is applied. Customer pays less GST.', 'jewellery-price-calc'); ?>
                            </p>
                        </label>
                        
                        <label style="display: block; margin-bottom: 10px;">
                            <input type="radio" name="jpc_gst_calculation_base" value="before_discount" <?php checked($gst_calculation_base, 'before_discount'); ?>>
                            <strong><?php _e('Original Price', 'jewellery-price-calc'); ?></strong>
                            <p class="description" style="margin-left: 25px;">
                                <?php _e('GST is calculated on the original price before discount. Discount is applied after GST.', 'jewellery-price-calc'); ?>
                            </p>
                        </label>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- CALCULATION FLOW SUMMARY -->
        <div class="jpc-card" style="background: #f0f6fc; border-left: 4px solid #2271b1;">
            <h3 style="margin-top: 0;"><?php _e('📊 Current Calculation Flow', 'jewellery-price-calc'); ?></h3>
            <div id="calculation-flow-summary" style="font-family: monospace; font-size: 13px; line-height: 1.8;">
                <!-- Will be updated via JavaScript -->
            </div>
        </div>
        
        <?php submit_button(__('Save Discount Settings', 'jewellery-price-calc'), 'primary', 'submit', true, array('style' => 'font-size: 16px; padding: 10px 30px;')); ?>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Show/hide component selection based on method
    $('input[name="jpc_discount_calculation_method"]').on('change', function() {
        if ($(this).val() === 'simple') {
            $('#component-selection').slideDown();
        } else {
            $('#component-selection').slideUp();
        }
        updateCalculationFlow();
    });
    
    // Update calculation flow on any change
    $('input[type="radio"], input[type="checkbox"]').on('change', updateCalculationFlow);
    
    // Initial update
    updateCalculationFlow();
    
    function updateCalculationFlow() {
        var method = $('input[name="jpc_discount_calculation_method"]:checked').val();
        var timing = $('input[name="jpc_discount_timing"]:checked').val();
        var gstBase = $('input[name="jpc_gst_calculation_base"]:checked').val();
        
        var flow = '<strong>Step-by-Step Calculation:</strong><br><br>';
        
        flow += '1️⃣ Calculate Base Costs<br>';
        flow += '   → Metal + Diamond + Making + Wastage + Pearl + Stone + Extra Fees + Extra Fields<br><br>';
        
        if (timing === 'before_additional') {
            flow += '2️⃣ Apply Discount (' + getMethodName(method) + ')<br>';
            flow += '   → Subtotal after discount<br><br>';
            
            flow += '3️⃣ Add Additional Percentage<br>';
            flow += '   → Subtotal + Additional %<br><br>';
        } else {
            flow += '2️⃣ Add Additional Percentage<br>';
            flow += '   → Subtotal + Additional %<br><br>';
            
            flow += '3️⃣ Apply Discount (' + getMethodName(method) + ')<br>';
            flow += '   → Subtotal after discount<br><br>';
        }
        
        if (gstBase === 'after_discount') {
            flow += '4️⃣ Calculate GST on Discounted Amount<br>';
            flow += '   → GST = (Discounted Subtotal × GST %)<br><br>';
        } else {
            flow += '4️⃣ Calculate GST on Original Amount<br>';
            flow += '   → GST = (Original Subtotal × GST %)<br><br>';
        }
        
        flow += '5️⃣ <strong>Final Price = Subtotal + GST</strong>';
        
        $('#calculation-flow-summary').html(flow);
    }
    
    function getMethodName(method) {
        switch(method) {
            case 'simple': return 'Component-Based';
            case 'advanced': return 'All Components';
            case 'total_before_gst': return 'Total Before GST';
            case 'total_after_additional': return 'Total After Additional %';
            default: return 'Unknown';
        }
    }
});
</script>

<style>
.jpc-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    padding: 20px;
    border-radius: 4px;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.jpc-card h2 {
    color: #1d2327;
    font-size: 18px;
    font-weight: 600;
}

.form-table th {
    width: 250px;
    font-weight: 600;
}

input[type="radio"], input[type="checkbox"] {
    margin-right: 8px;
}
</style>
