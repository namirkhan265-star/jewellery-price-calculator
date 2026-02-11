# Quick Fix Guide - GST "Before Discount" Calculation

## ✅ What Was Fixed

The GST calculation for "Original Price (Before Discount)" option was completely broken. It was calculating GST on the discounted amount instead of the original price.

## 🚀 How to Apply the Fix

### Step 1: Update Plugin File

**Option A - Download from GitHub:**
1. Go to your GitHub repository
2. Download `includes/class-jpc-price-calculator-v2.php`
3. Upload to your WordPress site (replace existing file)

**Option B - Direct Upload:**
1. The file has already been updated in your GitHub repo
2. Download the latest version
3. Upload via FTP or File Manager

### Step 2: Recalculate All Prices

**IMPORTANT:** You MUST recalculate all product prices after updating the file!

1. **Go to:** WordPress Admin → Jewellery Price → General Settings
2. **Scroll to bottom** of the page
3. **Click:** "Update All Product Prices" button
4. **Wait** for the success message
5. **Done!**

---

## 🧪 How to Test the Fix

### Test 1: Verify "After Discount" (Recommended)

1. Go to **General Settings**
2. Set **GST Calculation Base** to "After Discount (Recommended)"
3. Click **Save Settings**
4. Go to a product with 30% discount
5. Check the price breakup

**Expected Result:**
```
Subtotal: ₹122,428
Discount (30%): -₹36,728
After Discount: ₹85,700
GST (3%): ₹2,571  ← Lower GST (on discounted amount)
Sale Price: ₹88,271
```

### Test 2: Verify "Before Discount" (Fixed!)

1. Go to **General Settings**
2. Set **GST Calculation Base** to "Original Price (Before Discount)"
3. Click **Save Settings**
4. Click **"Update All Product Prices"** button
5. Go to the same product
6. Check the price breakup

**Expected Result:**
```
Subtotal: ₹122,428
Discount (30%): -₹36,728
GST (3%): ₹3,673  ← Higher GST (on original amount)
Sale Price: ₹89,373
```

**Notice:** Sale Price is ₹1,102 higher because GST is calculated on the original price!

---

## 📊 Understanding the Difference

### After Discount (Recommended)
- **GST calculated on:** Discounted price
- **Customer pays:** Less GST
- **Best for:** Customer-friendly pricing
- **Formula:** `(Subtotal - Discount) + GST`

**Example:**
- Original: ₹100
- Discount (10%): -₹10
- After Discount: ₹90
- GST (3% on ₹90): ₹2.70
- **Final: ₹92.70**

### Before Discount (Fixed in v2.5.18)
- **GST calculated on:** Original price
- **Customer pays:** Full GST (no GST discount)
- **Best for:** Tax compliance in some regions
- **Formula:** `(Subtotal + GST) - Discount`

**Example:**
- Original: ₹100
- GST (3% on ₹100): ₹3.00
- Subtotal + GST: ₹103
- Discount (10%): -₹10
- **Final: ₹93.00**

**Difference:** ₹93.00 - ₹92.70 = ₹0.30 (the GST difference)

---

## ❓ Troubleshooting

### Problem: Prices didn't change after update

**Solution:**
1. Make sure you uploaded the correct file (`class-jpc-price-calculator-v2.php`)
2. Clear all caches (WordPress, browser, server)
3. Click "Update All Product Prices" button again
4. Check a product

### Problem: GST still showing wrong amount

**Solution:**
1. Go to General Settings
2. Change GST Calculation Base to the other option
3. Save Settings
4. Change it back to your preferred option
5. Save Settings
6. Click "Update All Product Prices"

### Problem: Sale price seems wrong

**Solution:**
This is expected! The old calculation was WRONG. The new prices are CORRECT.

**Old (Wrong):**
- Sale Price: ₹88,271 (GST on discounted amount even when "Before Discount" selected)

**New (Correct):**
- Sale Price: ₹89,373 (GST on original amount when "Before Discount" selected)

---

## 🔍 What Changed in the Code

### Bug 1: Wrong Setting Value
```php
// BEFORE (v2.5.17):
if ($gst_calculation_base === 'original_price') {  // ← Never matched!

// AFTER (v2.5.18):
if ($gst_calculation_base === 'before_discount') {  // ← Correct value
```

### Bug 2: Wrong Calculation Formula
```php
// BEFORE (v2.5.17):
$sale_price = $subtotal_after_discount + $gst_on_discounted;  // ← Always used discounted GST

// AFTER (v2.5.18):
if ($gst_calculation_base === 'before_discount') {
    $sale_price = ($subtotal_after_additional + $gst_on_full) - $discount_amount;  // ← Correct!
} else {
    $sale_price = $subtotal_after_discount + $gst_on_discounted;
}
```

### Bug 3: Wrong GST Display
```php
// BEFORE (v2.5.17):
$gst_to_display = $prices['gst_on_discounted'];  // ← Always showed discounted GST

// AFTER (v2.5.18):
if ($prices['gst_calculation_base'] === 'before_discount') {
    $gst_to_display = $prices['gst_on_full'];  // ← Shows full GST
} else {
    $gst_to_display = $prices['gst_on_discounted'];  // ← Shows discounted GST
}
```

---

## 📝 Summary

**What was broken:**
- "Before Discount" option didn't work at all
- GST was always calculated on discounted amount
- Setting value check was wrong (`'original_price'` vs `'before_discount'`)
- Calculation formula was wrong

**What was fixed:**
- Corrected setting value check
- Fixed calculation formula
- Fixed GST display in breakup
- Both options now work correctly

**What you need to do:**
1. ✅ Update `class-jpc-price-calculator-v2.php` file
2. ✅ Click "Update All Product Prices" button
3. ✅ Verify prices are correct

---

**Last Updated:** v2.5.18  
**Status:** Fixed and tested  
**Estimated Time:** 2-3 minutes
