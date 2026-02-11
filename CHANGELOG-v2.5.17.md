# Changelog v2.5.17 - GST Calculation Base Fix

## 🐛 Critical Bug Fix: GST Calculation Base Setting Not Saving

### Problem Identified
The "GST Calculation Base" setting was appearing in two different admin pages but wasn't saving correctly:

1. **General Settings** page (displayed the setting)
2. **Discount Settings** page (also displayed the setting)

The setting was registered under `jpc_discount_settings` but displayed in General Settings which uses `jpc_general_settings` form group, causing a mismatch.

Additionally, the General Settings template used `original_price` as a value while the price calculator code expected `before_discount`, creating inconsistency.

### Root Cause
- Setting registered in wrong form group (`jpc_discount_settings` instead of `jpc_general_settings`)
- Duplicate setting display in two different pages
- Value mismatch: `original_price` (template) vs `before_discount` (calculator code)

### Solution Implemented

#### 1. **Moved Setting Registration** (`includes/class-jpc-admin.php`)
```php
// BEFORE (v2.5.16):
register_setting('jpc_discount_settings', 'jpc_gst_calculation_base');

// AFTER (v2.5.17):
register_setting('jpc_general_settings', 'jpc_gst_calculation_base');
```

#### 2. **Removed Duplicate from Discount Settings** (`templates/admin/discount-settings.php`)
- Removed "Section 5: GST Calculation Base" entirely
- Added notice pointing users to General Settings
- Updated JavaScript to read GST setting from database instead of form

#### 3. **Standardized Values** (`templates/admin/general-settings.php`)
```php
// BEFORE:
<option value="original_price">Original Price (Before Discount)</option>

// AFTER:
<option value="before_discount">Original Price (Before Discount)</option>
```

#### 4. **Database Migration Tools**
Created two helper files for users to fix existing database values:

**Option A: PHP Script** (`fix-gst-calculation-base.php`)
- Upload to WordPress root
- Visit the file in browser
- Automatically converts `original_price` → `before_discount`
- Delete after running

**Option B: SQL Command** (`SQL-FIX-GST-CALCULATION-BASE.txt`)
```sql
UPDATE wp_options 
SET option_value = 'before_discount' 
WHERE option_name = 'jpc_gst_calculation_base' 
AND option_value = 'original_price';
```

### Files Modified

1. **includes/class-jpc-admin.php**
   - Moved `jpc_gst_calculation_base` registration to `jpc_general_settings`
   - Updated version comment to v2.5.17

2. **templates/admin/discount-settings.php**
   - Removed GST Calculation Base section (Section 5)
   - Added notice with link to General Settings
   - Updated JavaScript to read GST setting from PHP variable

3. **templates/admin/general-settings.php** (Manual edit by user)
   - Changed `original_price` to `before_discount` on line ~400

### Testing Checklist

- [x] GST Calculation Base setting saves correctly in General Settings
- [x] Setting value persists after page reload
- [x] Price calculator respects the setting choice
- [x] "Update All Product Prices" button recalculates with correct GST base
- [x] No duplicate setting in Discount Settings page
- [x] JavaScript calculation flow summary works correctly
- [x] Database migration tools work as expected

### Upgrade Instructions

#### For New Installations
No action needed - works out of the box.

#### For Existing Installations

**Step 1:** Update plugin files from GitHub

**Step 2:** Fix database value (choose ONE method):

**Method A - Quick SQL Fix (Recommended):**
1. Go to phpMyAdmin
2. Run this SQL:
```sql
UPDATE wp_options 
SET option_value = 'before_discount' 
WHERE option_name = 'jpc_gst_calculation_base' 
AND option_value = 'original_price';
```

**Method B - PHP Script:**
1. Download `fix-gst-calculation-base.php` from GitHub
2. Upload to WordPress root directory
3. Visit: `https://yoursite.com/fix-gst-calculation-base.php`
4. Delete the file after running

**Step 3:** Verify the fix
1. Go to **Jewellery Price → General Settings**
2. Check "GST Calculation Base" setting
3. Change it and click "Save Settings"
4. Reload page - setting should persist
5. Go to a product and click "Update All Product Prices"
6. Verify GST amount changes based on your selection

### Technical Details

#### Setting Values
- `after_discount` - GST calculated on price after discount (Recommended)
- `before_discount` - GST calculated on original price before discount

#### Calculation Logic (from `class-jpc-price-calculator.php`)
```php
if ($gst_calculation_base === 'before_discount') {
    // GST on original price, then discount applied
    $sale_price = ($subtotal + $gst_on_full) - $discount_amount;
} else {
    // GST on discounted price (default/recommended)
    $sale_price = $subtotal_after_discount + $gst_on_discounted;
}
```

### Impact
- **High Priority**: Affects all products with discounts and GST enabled
- **User Impact**: GST amount now correctly reflects the chosen calculation base
- **Backward Compatibility**: Existing products will recalculate correctly after running "Update All Product Prices"

### Related Issues
- Fixes issue where GST setting wouldn't save
- Fixes inconsistency between template values and calculator code
- Improves admin UX by consolidating GST settings in one location

---

**Version:** 2.5.17  
**Date:** February 11, 2026  
**Priority:** Critical Bug Fix  
**Breaking Changes:** None (backward compatible)
