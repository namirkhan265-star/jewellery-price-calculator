# QUICK FIX: Correct Discount Calculation

## Issue
Current discount calculation applies discount only on selected components (Metal, Making, Wastage).

**You want:** Discount on TOTAL before GST, then GST on discounted amount.

## Solution
Set the discount method to **"Total Before GST"** in admin settings.

## Steps to Fix:

### 1. Go to Admin Panel
Navigate to: **Jewellery Price → Discount Settings**

### 2. Configure Settings

**Section 1: Enable Discount**
- ✅ Check "Enable discount calculations"

**Section 2: Discount Calculation Method**
- ✅ Select **"Method 3: Total Before GST"**

**Section 4: Discount Application Timing**
- ✅ Select **"After Additional Percentage"**

**Section 5: GST Calculation Base**
- ✅ Select **"Discounted Price (Recommended)"**

### 3. Save Settings
Click **"Save Discount Settings"**

### 4. Regenerate Prices
Go to: **Jewellery Price → General Settings**
Click **"Bulk Regenerate Price Breakup"** button

---

## What This Does:

### Current (Wrong) Calculation:
```
Metal: ₹30,240
Making: ₹9,000
Wastage: ₹4,000
Diamond: ₹0

Discountable (only Metal + Making + Wastage) = ₹43,240
30% Discount = ₹12,972 ❌ WRONG
```

### Correct Calculation with "Total Before GST":
```
Metal: ₹30,240
Making: ₹9,000
Wastage: ₹4,000
Diamond: ₹0
Additional %: ₹0

TOTAL = ₹43,240
30% Discount = ₹12,972 (on TOTAL)
After Discount = ₹30,268

GST (3% on ₹30,268) = ₹908
Final Price = ₹31,176
```

---

## Code Update Needed

The calculator code needs to be updated to respect the new `jpc_discount_calculation_method` setting.

**File:** `includes/class-jpc-price-calculator.php`
**Lines:** 160-195

Replace the discount calculation section with the code from `SNIPPET-discount-calculation-updated.php`

---

## Verification

After updating:
1. Check product price breakup
2. Verify discount is applied on total
3. Verify GST is calculated on discounted amount
4. Check that final price matches expected calculation

---

## Expected Result for Your Product:

**With Method 3 (Total Before GST):**
```
Metal Price: ₹30,240
Making Charge: ₹9,000
Wastage Charge: ₹4,000
Diamond Cost: ₹0
Pearl Cost: ₹0
Stone Cost: ₹0
Extra Fee: ₹0
Additional %: ₹0

Subtotal: ₹43,240
Discount (30%): ₹12,972
After Discount: ₹30,268

GST (3%): ₹908
Final Price: ₹31,176
```

This is the correct calculation you're looking for! ✅
