# HOTFIX v2.5.20 - Discount Calculation Method Fix

## 🚨 Critical Issue

**Discount was ALWAYS calculated as Method 1 (Simple)** regardless of what you selected in Discount Settings!

This caused:
- ❌ Discount applied only to Gold + Making + Wastage (₹33,756)
- ✅ Should apply to Total Before GST (₹36,729)
- **Difference:** ₹2,973 less discount than expected!

---

## 🔍 Root Cause

**Settings Page vs Calculator Mismatch:**

| Settings Page Saves | Calculator Expected | Result |
|---------------------|---------------------|--------|
| `'simple'` | `'1'` | ❌ Mismatch |
| `'advanced'` | `'2'` | ❌ Mismatch |
| `'total_before_gst'` | `'3'` | ❌ Mismatch |
| `'total_after_additional'` | `'4'` | ❌ Mismatch |

**What Happened:**
1. You select "Method 3: Total Before GST" in Discount Settings
2. Settings page saves `'total_before_gst'` to database
3. Calculator checks for `'3'`, doesn't find it
4. Falls back to `default` case = Method 1 (Simple)
5. Discount calculated incorrectly!

---

## ✅ What Was Fixed in v2.5.20

### Added Method Normalization

Created a new function `normalize_discount_method()` that converts text values to numeric:

```php
'simple' → '1'
'advanced' → '2'
'total_before_gst' → '3'
'total_after_additional' → '4'
```

Now the calculator:
1. Gets the setting value (text or numeric)
2. Normalizes it to numeric
3. Uses the correct calculation method
4. **Works with both old and new values!**

---

## 📊 Calculation Comparison

### Your Excel (CORRECT):
```
Subtotal:               ₹1,20,028
Processing Fee (2%):    ₹2,401
Total Before Tax:       ₹1,22,429
30% Discount:           ₹36,729 (30% of ₹1,22,429)
After Discount:         ₹85,700
GST (3%):               ₹2,571
Final Price:            ₹88,271
```

### Plugin BEFORE Fix (WRONG):
```
Subtotal:               ₹1,20,031
Processing Fee (2%):    ₹2,401
Total Before Tax:       ₹1,22,432
30% Discount:           ₹33,756 (30% of ₹1,12,520) ← WRONG BASE!
After Discount:         ₹88,676
GST (3%):               ₹2,660
Final Price:            ₹91,336
```

### Plugin AFTER Fix (CORRECT):
```
Subtotal:               ₹1,20,031
Processing Fee (2%):    ₹2,401
Total Before Tax:       ₹1,22,432
30% Discount:           ₹36,730 (30% of ₹1,22,432) ✓
After Discount:         ₹85,702
GST (3%):               ₹2,571
Final Price:            ₹88,273
```

**Difference from Excel:** Only ₹2 (due to rounding and extra fields)

---

## 🚀 How to Apply the Fix

### Step 1: Download Updated File

Download the latest version from GitHub:
- **File:** `includes/class-jpc-price-calculator-v2.php`
- **Version:** v2.5.20

### Step 2: Upload to Your Site

Replace the existing file on your WordPress site:
```
wp-content/plugins/jewellery-price-calculator/includes/class-jpc-price-calculator-v2.php
```

### Step 3: Verify Settings

1. Go to **Jewellery Price → Discount Settings**
2. Make sure **"Method 3: Total Before GST"** is selected
3. Save settings (even if already selected)

### Step 4: Update Product Prices

1. Go to **Jewellery Price → Metals**
2. Click **"Update All Product Prices"** button
3. All products will recalculate with correct discount

---

## 📋 Testing Checklist

After applying the fix:

- [ ] Discount Settings page loads correctly
- [ ] Method 3 (Total Before GST) is selected
- [ ] Product prices updated
- [ ] Discount amount matches Excel calculation
- [ ] Final price matches Excel (within ₹5 rounding)
- [ ] Price breakup shows correct discount
- [ ] GST calculation still works correctly

---

## 🔄 Version History

### v2.5.20 (HOTFIX - CRITICAL)
- ✅ Fixed discount method normalization
- ✅ Supports both text and numeric values
- ✅ Discount now calculated correctly
- ✅ Matches Excel calculation

### v2.5.19
- ✅ Restored missing methods
- ✅ Metals page works
- ❌ Discount still calculated wrong

### v2.5.18 and earlier
- ❌ Discount always used Method 1 (Simple)
- ❌ Didn't respect Discount Settings selection

---

## 💡 Understanding the 4 Discount Methods

### Method 1: Simple (Component-Based)
**Discount Base:** Gold + Making + Wastage only  
**Example:** ₹104,520 + ₹8,000 + ₹0 = ₹112,520  
**30% Discount:** ₹33,756  
**Use When:** You want to exclude diamonds, processing fees, etc. from discount

### Method 2: Advanced (All Components)
**Discount Base:** All components before processing fee  
**Example:** Gold + Diamond + Making + Wastage + Extras = ₹120,031  
**30% Discount:** ₹36,009  
**Use When:** You want to discount everything except processing fee

### Method 3: Total Before GST (RECOMMENDED) ✅
**Discount Base:** Complete total including processing fee, before GST  
**Example:** ₹120,031 + ₹2,401 = ₹122,432  
**30% Discount:** ₹36,730  
**Use When:** You want to discount the full amount (matches your Excel)

### Method 4: Total After Additional %
**Discount Base:** Same as Method 3 (if Additional % is enabled)  
**Example:** Same as Method 3  
**Use When:** You have Additional Percentage enabled

---

## 📝 Summary

**Problem:** Discount calculated on wrong base (₹112,520 instead of ₹122,432)  
**Cause:** Settings page and calculator used different value formats  
**Fix:** Added normalization to support both formats  
**Result:** Discount now matches Excel calculation  

**Price Difference:**
- Before Fix: ₹91,336
- After Fix: ₹88,273
- **Savings:** ₹3,063 more discount per product!

---

**Version:** 2.5.20  
**Date:** February 11, 2026  
**Priority:** CRITICAL HOTFIX  
**Breaking Changes:** None (just fixes existing functionality)  
**Requires:** Upload file + Update All Product Prices
