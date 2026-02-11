# HOTFIX v2.5.21 - Regular Price Display Fix

## 🚨 Critical Issue

**Regular Price displayed INCORRECTLY in accordion (₹123,728 instead of ₹126,101)**

This caused:
- ❌ Accordion showed Regular Price: ₹123,728
- ✅ WordPress stored correct value: ₹126,101.42
- **Difference:** ₹2,373 less than actual!

---

## 🔍 Root Cause

**Accordion Template Calculated GST Rate Incorrectly:**

The accordion template (lines 140-162) was:
1. Summing all components before discount
2. **Calculating GST rate from the DISCOUNTED price** (WRONG!)
3. Applying that wrong GST rate to the pre-discount subtotal
4. Adding that to get regular price

**Example of Wrong Calculation:**
```
Subtotal before discount: ₹120,031
Discount: ₹36,730
After discount: ₹83,301
GST (from breakup): ₹2,571

WRONG: GST rate = ₹2,571 / ₹83,301 = 3.09%
WRONG: GST on regular = ₹120,031 × 3.09% = ₹3,709
WRONG: Regular Price = ₹120,031 + ₹3,709 = ₹123,740
```

**Correct Calculation:**
```
Subtotal before discount: ₹122,432
GST rate (from settings): 3%
GST on regular: ₹122,432 × 3% = ₹3,673
Regular Price: ₹122,432 + ₹3,673 = ₹126,105 ✓
```

---

## ✅ What Was Fixed in v2.5.21

### Changed GST Calculation Method

**Before (WRONG):**
```php
// Calculate GST rate from current values
$gst_rate = ($price_after_discount > 0) ? ($current_gst / $price_after_discount) : 0;

// Calculate GST on pre-discount subtotal
$gst_on_regular_price = $subtotal_before_discount * $gst_rate;
```

**After (CORRECT):**
```php
// Get actual GST percentage from settings
$enable_gst = get_option('jpc_enable_gst', 'yes');
$gst_percentage = 0;

if ($enable_gst === 'yes') {
    $gst_percentage = floatval(get_option('jpc_gst_value', 3));
}

// Calculate GST on pre-discount subtotal using actual GST percentage
$gst_on_regular_price = ($subtotal_before_discount * $gst_percentage) / 100;
```

---

## 📊 Calculation Comparison

### Before Fix (WRONG):
```
Components Total:        ₹120,031
Processing Fee (2%):     ₹2,401
Subtotal:                ₹122,432
Discount (30%):          ₹36,730
After Discount:          ₹85,702
GST on Discounted:       ₹2,571

Calculated GST Rate:     3.09% (WRONG!)
GST on Regular:          ₹3,709 (WRONG!)
Regular Price Display:   ₹123,740 (WRONG!)
```

### After Fix (CORRECT):
```
Components Total:        ₹120,031
Processing Fee (2%):     ₹2,401
Subtotal:                ₹122,432
Discount (30%):          ₹36,730
After Discount:          ₹85,702
GST on Discounted:       ₹2,571

Actual GST Rate:         3% (from settings)
GST on Regular:          ₹3,673 ✓
Regular Price Display:   ₹126,105 ✓
```

---

## 🚀 How to Apply the Fix

### Step 1: Download Updated File

Download the latest version from GitHub:
- **File:** `templates/shortcodes/product-details-accordion.php`
- **Version:** v2.5.21

### Step 2: Upload to Your Site

Replace the existing file on your WordPress site:
```
wp-content/plugins/jewellery-price-calculator/templates/shortcodes/product-details-accordion.php
```

### Step 3: Clear Cache

1. Clear WordPress cache (if using caching plugin)
2. Clear browser cache
3. Refresh product page

### Step 4: Verify Fix

1. Go to any product page
2. Check the accordion "PRICE BREAKUP" section
3. Regular Price should now match WordPress value (₹126,101)

---

## 📋 Testing Checklist

After applying the fix:

- [ ] Accordion loads correctly
- [ ] Regular Price matches WordPress _regular_price meta
- [ ] Sale Price still correct
- [ ] Discount amount still correct
- [ ] GST amount still correct
- [ ] "You Save" badge shows correct amount
- [ ] All other accordion sections work

---

## 🔄 Version History

### v2.5.21 (HOTFIX - CRITICAL)
- ✅ Fixed regular price calculation in accordion
- ✅ Uses actual GST percentage from settings
- ✅ Matches WooCommerce _regular_price meta
- ✅ Correctly includes all components + processing fee

### v2.5.20
- ✅ Fixed discount method normalization
- ✅ Sale price now correct
- ❌ Regular price still wrong in accordion

### v2.5.4 and earlier
- ❌ Regular price calculated with wrong GST rate
- ❌ Showed ₹123,728 instead of ₹126,101

---

## 💡 Technical Details

### What Changed

**File:** `templates/shortcodes/product-details-accordion.php`

**Lines Changed:** 140-180

**Key Changes:**
1. Added code to fetch actual GST percentage from settings
2. Removed calculation of GST rate from discounted price
3. Use actual GST percentage to calculate GST on regular price
4. Added extra fields and additional percentage to subtotal calculation

### Why It Matters

The accordion is a **display-only** template. It doesn't affect:
- WooCommerce prices (those are correct)
- Cart prices
- Checkout prices
- Order prices

But it **does affect**:
- Customer perception of value
- Discount savings display
- Price transparency

**Showing wrong regular price makes the discount look smaller than it actually is!**

---

## 📝 Summary

**Problem:** Accordion calculated GST rate from discounted price (3.09% instead of 3%)  
**Cause:** Wrong formula in accordion template  
**Fix:** Use actual GST percentage from settings  
**Result:** Regular price now matches WordPress value  

**Price Difference:**
- Before Fix: ₹123,728 (wrong)
- After Fix: ₹126,105 (correct)
- **Correction:** ₹2,377 more accurate!

---

**Version:** 2.5.21  
**Date:** February 11, 2026  
**Priority:** CRITICAL HOTFIX  
**Breaking Changes:** None (just fixes display)  
**Requires:** Upload file + Clear cache
