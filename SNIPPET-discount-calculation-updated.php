<?php
/**
 * UPDATED DISCOUNT CALCULATION LOGIC
 * 
 * This replaces lines 160-195 in class-jpc-price-calculator.php
 * 
 * Implements the new discount calculation methods from admin settings
 */

// Get discount percentage
$discount_percentage = floatval(get_post_meta($product_id, '_jpc_discount_percentage', true));

// Calculate discount based on settings
$discount_amount = 0;
$subtotal_after_discount = $subtotal_before_discount;

if ($discount_percentage > 0) {
    // Get discount calculation method
    $discount_method = get_option('jpc_discount_calculation_method', 'simple');
    $discount_timing = get_option('jpc_discount_timing', 'after_additional');
    
    // Calculate discountable amount based on method
    $discountable_amount = 0;
    
    switch ($discount_method) {
        case 'simple':
            // Method 1: Component-Based Discount
            // Only discount selected components (Metal, Making, Wastage)
            $discount_on_metals = get_option('jpc_discount_on_metals');
            $discount_on_making = get_option('jpc_discount_on_making');
            $discount_on_wastage = get_option('jpc_discount_on_wastage');
            
            if ($discount_on_metals === 'yes' || $discount_on_metals === '1' || $discount_on_metals === 1 || $discount_on_metals === true) {
                $discountable_amount += $metal_price;
            }
            
            if ($discount_on_making === 'yes' || $discount_on_making === '1' || $discount_on_making === 1 || $discount_on_making === true) {
                $discountable_amount += $making_charge_amount;
            }
            
            if ($discount_on_wastage === 'yes' || $discount_on_wastage === '1' || $discount_on_wastage === 1 || $discount_on_wastage === true) {
                $discountable_amount += $wastage_charge_amount;
            }
            
            // If no components selected, apply to entire subtotal (backward compatibility)
            if ($discountable_amount == 0) {
                $discountable_amount = $subtotal_before_discount;
            }
            break;
            
        case 'advanced':
            // Method 2: All Components
            // Discount on ALL costs including Diamond, Pearl, Stone, Extra Fees, Extra Fields
            $discountable_amount = $subtotal_before_discount;
            break;
            
        case 'total_before_gst':
            // Method 3: Total Before GST (RECOMMENDED FOR YOUR CASE)
            // Discount on complete subtotal (after Additional %)
            // GST will be calculated on discounted amount
            $discountable_amount = $subtotal_before_discount;
            break;
            
        case 'total_after_additional':
            // Method 4: Total After Additional %
            // Same as Method 3 but explicitly includes Additional %
            $discountable_amount = $subtotal_before_discount;
            break;
            
        default:
            // Fallback to total discount
            $discountable_amount = $subtotal_before_discount;
            break;
    }
    
    // Calculate discount
    $discount_amount = ($discountable_amount * $discount_percentage) / 100;
    $subtotal_after_discount = $subtotal_before_discount - $discount_amount;
}

// Calculate GST based on settings
$gst_amount_on_discounted = 0;
$gst_amount_on_full = 0;
$gst_percentage = 0;
$gst_enabled = get_option('jpc_enable_gst');

if ($gst_enabled === 'yes' || $gst_enabled === '1' || $gst_enabled === 1 || $gst_enabled === true) {
    $gst_percentage = floatval(get_option('jpc_gst_value', 5));
    
    // Check for metal-specific GST rates
    $metal_group_name = strtolower(str_replace(' ', '_', $metal_group->name));
    $metal_specific_gst = get_option('jpc_gst_' . $metal_group_name);
    
    // Also try without underscores
    if ($metal_specific_gst === false || $metal_specific_gst === '') {
        $metal_group_name_no_underscore = strtolower(str_replace(' ', '', $metal_group->name));
        $metal_specific_gst = get_option('jpc_gst_' . $metal_group_name_no_underscore);
    }
    
    // Use metal-specific GST if found
    if ($metal_specific_gst !== false && $metal_specific_gst !== '' && $metal_specific_gst !== null) {
        $gst_percentage = floatval($metal_specific_gst);
    }
    
    // Get GST calculation base setting
    $gst_base = get_option('jpc_gst_calculation_base', 'after_discount');
    
    if ($gst_base === 'after_discount') {
        // GST on discounted amount (RECOMMENDED)
        $gst_amount_on_discounted = ($subtotal_after_discount * $gst_percentage) / 100;
        $gst_amount_on_full = ($subtotal_before_discount * $gst_percentage) / 100;
    } else {
        // GST on original amount (before discount)
        $gst_amount_on_discounted = ($subtotal_before_discount * $gst_percentage) / 100;
        $gst_amount_on_full = ($subtotal_before_discount * $gst_percentage) / 100;
    }
}

// Calculate final prices
$sale_price = $subtotal_after_discount + $gst_amount_on_discounted;
$regular_price = $subtotal_before_discount + $gst_amount_on_full;

?>
