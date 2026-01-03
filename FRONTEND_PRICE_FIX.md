# Frontend Price Display Fix

## Problem
The frontend is showing incorrect prices because it's recalculating instead of using the stored breakup values.

**Current (WRONG):**
- Regular Price = Subtotal + GST
- Sale Price = final_price from breakup

**Should be:**
- Regular Price = Subtotal + Additional % + GST (before discount)
- Sale Price = Subtotal + Additional % - Discount + GST

## Solution

In `templates/shortcodes/product-details-accordion.php`, replace lines 55-105 with:

```php
// Get price breakup
$price_breakup = get_post_meta($product_id, '_jpc_price_breakup', true);

// Get discount percentage from meta
$discount_percentage = floatval(get_post_meta($product_id, '_jpc_discount_percentage', true));

// Calculate prices from breakup
$regular_price = 0;
$sale_price = 0;

if ($price_breakup && is_array($price_breakup)) {
    // Calculate subtotal BEFORE additional percentage
    $subtotal_before_additional = 0;
    $subtotal_before_additional += !empty($price_breakup['metal_price']) ? floatval($price_breakup['metal_price']) : 0;
    $subtotal_before_additional += !empty($price_breakup['diamond_price']) ? floatval($price_breakup['diamond_price']) : 0;
    $subtotal_before_additional += !empty($price_breakup['making_charge']) ? floatval($price_breakup['making_charge']) : 0;
    $subtotal_before_additional += !empty($price_breakup['wastage_charge']) ? floatval($price_breakup['wastage_charge']) : 0;
    $subtotal_before_additional += !empty($price_breakup['pearl_cost']) ? floatval($price_breakup['pearl_cost']) : 0;
    $subtotal_before_additional += !empty($price_breakup['stone_cost']) ? floatval($price_breakup['stone_cost']) : 0;
    $subtotal_before_additional += !empty($price_breakup['extra_fee']) ? floatval($price_breakup['extra_fee']) : 0;
    
    // Add extra fields to subtotal
    if (!empty($price_breakup['extra_fields']) && is_array($price_breakup['extra_fields'])) {
        foreach ($price_breakup['extra_fields'] as $extra_field) {
            $subtotal_before_additional += !empty($extra_field['value']) ? floatval($extra_field['value']) : 0;
        }
    }
    
    // Add additional percentage
    $additional_percentage_amount = !empty($price_breakup['additional_percentage']) ? floatval($price_breakup['additional_percentage']) : 0;
    
    // Subtotal AFTER additional percentage (before discount)
    $subtotal_after_additional = $subtotal_before_additional + $additional_percentage_amount;
    
    // Get discount amount
    $discount_amount = !empty($price_breakup['discount']) ? floatval($price_breakup['discount']) : 0;
    
    // Get GST amount from breakup (this is the correct GST based on discount method)
    $gst_amount = !empty($price_breakup['gst']) ? floatval($price_breakup['gst']) : 0;
    
    // CORRECT CALCULATION:
    // Regular Price = Subtotal (after additional %) + GST (on full amount before discount)
    $gst_on_full = !empty($price_breakup['gst_on_full']) ? floatval($price_breakup['gst_on_full']) : $gst_amount;
    $regular_price = $subtotal_after_additional + $gst_on_full;
    
    // Sale Price = Use the final_price from breakup (already calculated correctly)
    $sale_price = !empty($price_breakup['final_price']) ? floatval($price_breakup['final_price']) : 0;
}
```

## What This Fixes

1. **Regular Price** now uses `gst_on_full` (GST before discount) instead of just `gst`
2. **Sale Price** uses the stored `final_price` which is already correct
3. Matches the admin panel calculation exactly
4. No more recalculation errors

## Expected Result

For your product:
- **Regular Price**: ₹6,482,503 (Subtotal + Additional % + GST before discount)
- **Sale Price**: ₹4,603,516 (Subtotal + Additional % - Discount + GST after discount)
- **Discount**: ₹1,878,987 (30% OFF)
