# LIVE CALCULATOR FIX INSTRUCTIONS

## Problem
The AJAX handler returns `regular_price` and `sale_price`, but the JavaScript expects `price_before_discount` and `final_price` as primary names.

## Solution
Update the AJAX response in `includes/class-jpc-product-meta.php` around line 365-390.

### FIND THIS CODE (around line 365):
```php
// Build response with detailed breakup
$response = array(
    'regular_price' => $regular_price,
    'sale_price' => $sale_price,
    'discount_amount' => $discount_amount,
    'discount_percentage' => $discount_percentage,
    'breakup' => array(
        'metal_price' => $metal_price,
        'diamond_price' => $diamond_price,
        'making_charge' => $making_charge_amount,
        'wastage_charge' => $wastage_charge_amount,
        'pearl_cost' => $pearl_cost,
        'stone_cost' => $stone_cost,
        'extra_fee' => $extra_fee,
        'extra_fields' => $extra_field_costs,
        'subtotal' => $subtotal,
        'additional_percentage' => $additional_percentage_amount,
        'additional_percentage_label' => $additional_percentage_label,
        'gst' => $gst_amount,
        'gst_percentage' => $gst_percentage,
        'discount_method' => $discount_method,
        'discountable_amount' => $discountable_amount
    )
);

wp_send_json_success($response);
```

### REPLACE WITH THIS CODE:
```php
// Build response with detailed breakup
// IMPORTANT: Include both naming conventions for backward compatibility
$response = array(
    // New naming (primary)
    'price_before_discount' => $regular_price,
    'final_price' => $sale_price,
    
    // Old naming (backward compatibility)
    'regular_price' => $regular_price,
    'sale_price' => $sale_price,
    
    'discount_amount' => $discount_amount,
    'discount_percentage' => $discount_percentage,
    
    // Include both 'breakup' and 'breakdown' for compatibility
    'breakup' => array(
        'metal_price' => $metal_price,
        'diamond_price' => $diamond_price,
        'making_charge' => $making_charge_amount,
        'wastage_charge' => $wastage_charge_amount,
        'pearl_cost' => $pearl_cost,
        'stone_cost' => $stone_cost,
        'extra_fee' => $extra_fee,
        'extra_fields' => $extra_field_costs,
        'subtotal' => $subtotal,
        'additional_percentage' => $additional_percentage_amount,
        'additional_percentage_label' => $additional_percentage_label,
        'gst' => $gst_amount,
        'gst_percentage' => $gst_percentage,
        'gst_label' => 'GST',
        'discount_method' => $discount_method,
        'discountable_amount' => $discountable_amount,
        'discount_amount' => $discount_amount,
        'discount_percentage' => $discount_percentage
    )
);

// Add 'breakdown' as alias for 'breakup'
$response['breakdown'] = $response['breakup'];

wp_send_json_success($response);
```

## Testing Steps

1. **Update the file** `includes/class-jpc-product-meta.php` with the code above
2. **Clear WordPress cache** (if using caching plugin)
3. **Hard refresh browser** (Ctrl+Shift+R or Cmd+Shift+R)
4. **Open product editor**
5. **Open browser console** (F12)
6. **Paste and run the diagnostic script** from `assets/js/diagnostic.js`
7. **Fill in Metal and Weight fields**
8. **Run**: `testLiveCalculator()`
9. **Check console output** - should show success with price data

## Expected Console Output

```
=== TESTING AJAX CALL ===
Metal ID: 1
Metal Weight: 10
✓ AJAX SUCCESS
Response: {success: true, data: {...}}
✓ Response has data
  - regular_price: 50000
  - sale_price: 50000
  - breakup: {...}
```

## If Still Not Working

Check these:
1. Is `product-meta.js` loaded? (Check Network tab in DevTools)
2. Is `jpcProductMeta` defined? (Type it in console)
3. Are the HTML elements present? (Check Elements tab)
4. Any JavaScript errors? (Check Console tab)
5. Is the AJAX endpoint correct? (Check Network tab for the AJAX call)

## Quick Diagnostic

Open browser console and paste:
```javascript
console.log('jQuery:', typeof jQuery);
console.log('jpcProductMeta:', typeof jpcProductMeta);
console.log('Metal field:', jQuery('#_jpc_metal_id').length);
console.log('Price breakup div:', jQuery('.jpc-price-breakup-admin').length);
```

All should show positive results.
