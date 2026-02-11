# Manual Diamond Calculation Fix

## Problem
Manual diamond entry shows correct calculation in product editor (₹7,500), but `diamond_price` in breakup is 0.

## Root Cause
The calculator (`class-jpc-price-calculator.php` lines 44-56) only handles dropdown diamonds:

```php
$diamond_id = get_post_meta($product_id, '_jpc_diamond_id', true);
$diamond_quantity = intval(get_post_meta($product_id, '_jpc_diamond_quantity', true));

if ($diamond_id && $diamond_quantity > 0) {
    // Only works for dropdown diamonds
}
```

It doesn't check for manual diamond entry fields.

## Solution
We need to add manual diamond calculation support.

## Manual Diamond Meta Fields
Based on the product editor screenshot, these fields are saved:
- `_jpc_manual_diamond_group_id`
- `_jpc_manual_diamond_cert_id`
- `_jpc_manual_diamond_shape_id`
- `_jpc_manual_diamond_colour_id`
- `_jpc_manual_diamond_clarity_id`
- `_jpc_manual_diamond_cut_id`
- `_jpc_manual_diamond_carat` (0.50)
- `_jpc_manual_diamond_quantity` (5)
- `_jpc_manual_diamond_price_per_carat` (3000)

## Calculation
```
Diamond Price = Carat × Quantity × Price Per Carat
Diamond Price = 0.50 × 5 × 3000 = ₹7,500
```

## Fix Required
Add this code after line 56 in `class-jpc-price-calculator.php`:

```php
// Check for manual diamond entry if no dropdown diamond
if ($diamond_price == 0) {
    $manual_carat = floatval(get_post_meta($product_id, '_jpc_manual_diamond_carat', true));
    $manual_quantity = intval(get_post_meta($product_id, '_jpc_manual_diamond_quantity', true));
    $manual_price_per_carat = floatval(get_post_meta($product_id, '_jpc_manual_diamond_price_per_carat', true));
    
    if ($manual_carat > 0 && $manual_quantity > 0 && $manual_price_per_carat > 0) {
        $diamond_price = $manual_carat * $manual_quantity * $manual_price_per_carat;
    }
}
```

This will calculate manual diamonds if no dropdown diamond is selected.
