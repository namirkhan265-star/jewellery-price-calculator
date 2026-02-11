# Changelog v2.5.18 - GST "Before Discount" Calculation Fix

## 🐛 Critical Bug Fix: GST Calculation for "Original Price (Before Discount)" Option

### Problem Identified

When "GST Calculation Base" was set to "Original Price (Before Discount)", the GST was being calculated incorrectly:

**WRONG Calculation (v2.5.17 and earlier):**
```
Subtotal: ₹122,428
Discount (30%): ₹36,728
After Discount: ₹85,700
GST (3% on ₹85,700): ₹2,571  ← WRONG! Should be on original price
Sale Price: ₹88,271
```

**CORRECT Calculation (v2.5.18):**
```
Subtotal: ₹122,428
GST (3% on ₹122,428): ₹3,673  ← Calculated on original price
Subtotal + GST: ₹126,101
Discount (30%): ₹36,728
Sale Price: ₹89,373  ← Discount applied AFTER GST
```

### Root Causes

1. **Wrong Setting Value Check**
   - Code checked for `'original_price'` 
   - But setting actually uses `'before_discount'`
   - Result: Condition never matched, always used "after discount" logic

2. **Wrong Calculation Formula**
   - Old: `Sale Price = (Subtotal - Discount) + GST`
   - Correct: `Sale Price = (Subtotal + GST) - Discount`

3. **Wrong GST Variable Assignment**
   - When "before_discount" was selected, code set:
   - `$gst_on_discounted = $gst_on_full` (both same value)
   - Then used `$gst_on_discounted` in sale price calculation
   - This defeated the purpose of the setting

### Solution Implemented

#### File: `includes/class-jpc-price-calculator-v2.php`

**1. Fixed Setting Value Check (Line 227)**
```php
// BEFORE (v2.5.17):
if ($gst_calculation_base === 'original_price') {

// AFTER (v2.5.18):
if ($gst_calculation_base === 'before_discount') {
```

**2. Fixed GST Calculation Logic (Lines 229-237)**
```php
// BEFORE (v2.5.17):
if ($gst_calculation_base === 'original_price') {
    $gst_on_full = ($subtotal_after_additional * $gst_percentage) / 100;
    $gst_on_discounted = $gst_on_full;  // ← BUG: Both same!
} else {
    $gst_on_full = ($subtotal_after_additional * $gst_percentage) / 100;
    $gst_on_discounted = ($subtotal_after_discount * $gst_percentage) / 100;
}

// AFTER (v2.5.18):
// Always calculate both GST amounts correctly
$gst_on_full = ($subtotal_after_additional * $gst_percentage) / 100;
$gst_on_discounted = ($subtotal_after_discount * $gst_percentage) / 100;
```

**3. Fixed Sale Price Calculation (Lines 244-256)**
```php
// BEFORE (v2.5.17):
$regular_price = $subtotal_after_additional + $gst_on_full;
$sale_price = $subtotal_after_discount + $gst_on_discounted;

// AFTER (v2.5.18):
$regular_price = $subtotal_after_additional + $gst_on_full;

if ($discount_percentage > 0) {
    if ($gst_calculation_base === 'before_discount') {
        // GST on original price, then discount applied
        $sale_price = ($subtotal_after_additional + $gst_on_full) - $discount_amount;
    } else {
        // GST on discounted price (default)
        $sale_price = $subtotal_after_discount + $gst_on_discounted;
    }
} else {
    $sale_price = $regular_price;
}
```

**4. Fixed Breakup Display (Lines 295-306)**
```php
// v2.5.18: Show correct GST amount in price breakup
$gst_to_display = 0;
if ($prices['discount_percentage'] > 0) {
    if ($prices['gst_calculation_base'] === 'before_discount') {
        // Show GST on full amount (before discount)
        $gst_to_display = $prices['gst_on_full'];
    } else {
        // Show GST on discounted amount (default)
        $gst_to_display = $prices['gst_on_discounted'];
    }
} else {
    $gst_to_display = $prices['gst_on_full'];
}
```

### Calculation Examples

#### Example 1: After Discount (Recommended)
```
Gold: ₹104,520
Diamond: ₹7,500
Making: ₹8,000
Extra Fields: ₹7
Processing Fee (2%): ₹2,401
─────────────────────────
Subtotal: ₹122,428
Discount (30%): -₹36,728
After Discount: ₹85,700
GST (3% on ₹85,700): ₹2,571
─────────────────────────
Sale Price: ₹88,271
```

#### Example 2: Before Discount (Fixed in v2.5.18)
```
Gold: ₹104,520
Diamond: ₹7,500
Making: ₹8,000
Extra Fields: ₹7
Processing Fee (2%): ₹2,401
─────────────────────────
Subtotal: ₹122,428
GST (3% on ₹122,428): ₹3,673
Subtotal + GST: ₹126,101
Discount (30%): -₹36,728
─────────────────────────
Sale Price: ₹89,373
```

**Difference:** ₹89,373 - ₹88,271 = ₹1,102 (the GST difference)

### Testing Checklist

- [x] "After Discount" option works correctly
- [x] "Before Discount" option works correctly
- [x] GST amount displayed in breakup matches calculation
- [x] Sale price calculation is correct for both options
- [x] Regular price remains unchanged
- [x] Discount amount is calculated correctly
- [x] "Update All Product Prices" button recalculates correctly

### Upgrade Instructions

#### For Existing Installations

**Step 1:** Update plugin files from GitHub
- Download latest version
- Replace `includes/class-jpc-price-calculator-v2.php`

**Step 2:** Recalculate all product prices
1. Go to **Jewellery Price → General Settings**
2. Scroll to bottom
3. Click **"Update All Product Prices"** button
4. Wait for completion message

**Step 3:** Verify the fix
1. Go to any product with a discount
2. Check the price breakup
3. Verify GST amount matches your selected calculation base:
   - **After Discount**: GST should be lower (calculated on discounted price)
   - **Before Discount**: GST should be higher (calculated on original price)

### Technical Details

#### Calculation Flow - Before Discount

```
1. Calculate all components (Metal, Diamond, Making, etc.)
2. Add Additional Percentage (Processing Fee)
3. Calculate GST on FULL amount (before discount)
4. Add GST to subtotal
5. Apply discount to (Subtotal + GST)
6. Result: Sale Price
```

#### Calculation Flow - After Discount (Default)

```
1. Calculate all components (Metal, Diamond, Making, etc.)
2. Add Additional Percentage (Processing Fee)
3. Apply discount to subtotal
4. Calculate GST on DISCOUNTED amount
5. Add GST to discounted subtotal
6. Result: Sale Price
```

### Impact

- **Priority:** Critical Bug Fix
- **Affects:** All products with discounts when "Before Discount" is selected
- **User Impact:** Prices will change after update (will be correct)
- **Backward Compatibility:** Maintained - existing products will recalculate correctly

### Files Modified

1. **includes/class-jpc-price-calculator-v2.php**
   - Fixed setting value check: `'original_price'` → `'before_discount'`
   - Fixed GST calculation logic
   - Fixed sale price calculation formula
   - Fixed breakup display logic
   - Updated version comment to v2.5.18

### Related Issues

- Fixes incorrect GST calculation when "Before Discount" is selected
- Fixes price breakup showing wrong GST amount
- Ensures GST amount remains constant when "Before Discount" is selected
- Properly implements the two GST calculation methods

---

**Version:** 2.5.18  
**Date:** February 11, 2026  
**Priority:** Critical Bug Fix  
**Breaking Changes:** None (prices will recalculate correctly)  
**Requires:** Running "Update All Product Prices" after update
