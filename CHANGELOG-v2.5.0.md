# Version 2.5.0 - Pearl/Stone/Extra Fee Enhancement

## 🎯 Overview
This update adds **percentage-based calculation** and **custom labels** for Pearl Cost, Stone Cost, and Extra Fee fields, making them consistent with Making Charges and Wastage Charges.

---

## ✨ New Features

### 1. **Percentage or Fixed Amount**
Each of the three cost fields (Pearl, Stone, Extra Fee) can now be calculated as:
- **Fixed Amount** (default) - Direct cost in currency
- **Percentage** - Percentage of metal price

### 2. **Custom Labels**
Admin can customize the display labels for:
- Pearl Cost (default: "Pearl Cost")
- Stone Cost (default: "Stone Cost")  
- Extra Fee (default: "Extra Fee")

### 3. **Enhanced Admin UI**
New settings in General Settings page with:
- Toggle switches for each field
- Custom label input
- Calculation type selector (Fixed/Percentage)
- Visual indicators showing current configuration

---

## 📋 Files Modified

### 1. **templates/admin/general-settings.php** ✅ COMPLETE
- Added custom label inputs for pearl_cost, stone_cost, extra_fee
- Added calculation type selectors (Fixed/Percentage)
- Enhanced UI with better organization

### 2. **includes/class-jpc-admin.php** 
**Status:** PATCH FILE CREATED
- Added 6 new settings registrations:
  - `jpc_pearl_cost_label`
  - `jpc_pearl_cost_type`
  - `jpc_stone_cost_label`
  - `jpc_stone_cost_type`
  - `jpc_extra_fee_label`
  - `jpc_extra_fee_type`

**Action Required:**
- Open `includes/class-jpc-admin.php`
- Find `register_settings()` function (around line 374)
- Replace with code from `PATCH-v2.5.0-register-settings.php`

### 3. **includes/class-jpc-price-calculator.php**
**Status:** PATCH FILE CREATED
- Updated price calculation logic to support percentage calculations
- Reads calculation type from settings
- Calculates percentage based on metal price

**Action Required:**
- Open `includes/class-jpc-price-calculator.php`
- Find "Get additional costs" section (around line 79-82)
- Replace with code from `PATCH-v2.5.0-price-calculator.php`

### 4. **templates/frontend/price-breakup.php**
**Status:** PATCH FILE CREATED
- Updated to use custom labels from settings
- Falls back to default labels if not set

**Action Required:**
- Open `templates/frontend/price-breakup.php`
- Find Pearl Cost, Stone Cost, Extra Fee sections
- Replace with code from `PATCH-v2.5.0-frontend-templates.php`

### 5. **templates/frontend/detailed-breakup.php**
**Status:** PATCH FILE CREATED
- Updated to use custom labels from settings
- Falls back to default labels if not set

**Action Required:**
- Open `templates/frontend/detailed-breakup.php`
- Find Pearl Cost, Stone Cost, Extra Fee sections
- Replace with code from `PATCH-v2.5.0-frontend-templates.php`

---

## 🔧 Implementation Steps

### Step 1: Apply Admin Class Patch
```bash
# Open includes/class-jpc-admin.php
# Find register_settings() function (line ~374)
# Replace entire function with code from PATCH-v2.5.0-register-settings.php
```

### Step 2: Apply Price Calculator Patch
```bash
# Open includes/class-jpc-price-calculator.php
# Find "Get additional costs" section (line ~79-82)
# Replace section with code from PATCH-v2.5.0-price-calculator.php
```

### Step 3: Apply Frontend Template Patches
```bash
# Open templates/frontend/price-breakup.php
# Find Pearl Cost section (line ~88-93)
# Replace Pearl/Stone/Extra sections with code from PATCH-v2.5.0-frontend-templates.php

# Open templates/frontend/detailed-breakup.php
# Find Pearl Cost section (line ~60-65)
# Replace Pearl/Stone/Extra sections with code from PATCH-v2.5.0-frontend-templates.php
```

### Step 4: Test
1. Go to **Jewellery Price > General Settings**
2. Verify new fields appear for Pearl/Stone/Extra Fee
3. Set custom labels and calculation types
4. Save settings
5. Edit a product and set pearl/stone/extra values
6. Verify calculations work correctly
7. Check frontend price breakup displays custom labels

---

## 💡 Usage Examples

### Example 1: Pearl Cost as Fixed Amount
```
Settings:
- Pearl Cost Label: "Pearl Decoration"
- Pearl Cost Type: Fixed Amount

Product:
- Metal Price: ₹50,000
- Pearl Cost: ₹5,000

Result: Pearl Decoration = ₹5,000
```

### Example 2: Stone Cost as Percentage
```
Settings:
- Stone Cost Label: "Gemstone Charges"
- Stone Cost Type: Percentage

Product:
- Metal Price: ₹50,000
- Stone Cost: 10 (means 10%)

Result: Gemstone Charges = ₹5,000 (10% of ₹50,000)
```

### Example 3: Extra Fee as Percentage
```
Settings:
- Extra Fee Label: "Certification Fee"
- Extra Fee Type: Percentage

Product:
- Metal Price: ₹50,000
- Extra Fee: 2 (means 2%)

Result: Certification Fee = ₹1,000 (2% of ₹50,000)
```

---

## 🎨 UI Changes

### General Settings Page
**Before:**
```
☑ Enable Pearl Cost
☑ Enable Stone Cost
☑ Enable Extra Fee
```

**After:**
```
☑ Enable Pearl Cost
  Label: [Pearl Cost          ]
  Type:  [Fixed Amount ▼]

☑ Enable Stone Cost
  Label: [Stone Cost          ]
  Type:  [Fixed Amount ▼]

☑ Enable Extra Fee
  Label: [Extra Fee           ]
  Type:  [Fixed Amount ▼]
```

---

## 🔍 Technical Details

### Database Options Added
- `jpc_pearl_cost_label` (string, default: "Pearl Cost")
- `jpc_pearl_cost_type` (string, default: "fixed", options: "fixed"|"percentage")
- `jpc_stone_cost_label` (string, default: "Stone Cost")
- `jpc_stone_cost_type` (string, default: "fixed", options: "fixed"|"percentage")
- `jpc_extra_fee_label` (string, default: "Extra Fee")
- `jpc_extra_fee_type` (string, default: "fixed", options: "fixed"|"percentage")

### Calculation Logic
```php
// For percentage type:
$cost = ($metal_price * $value) / 100;

// For fixed type:
$cost = $value;
```

### Backward Compatibility
- Existing products continue to work (defaults to "fixed" type)
- Default labels used if custom labels not set
- No database migration required

---

## ✅ Testing Checklist

- [ ] Settings save correctly
- [ ] Custom labels display in admin
- [ ] Custom labels display in frontend
- [ ] Fixed amount calculation works
- [ ] Percentage calculation works
- [ ] Price breakup shows correct values
- [ ] Detailed breakup shows correct values
- [ ] Existing products still work
- [ ] New products work correctly

---

## 📝 Notes

1. **Percentage Base:** Percentages are calculated based on **metal price only** (same as making charges and wastage charges)

2. **Default Values:** If settings are not configured, the system uses:
   - Label: Original English labels
   - Type: Fixed amount

3. **Product Meta:** Product meta fields remain unchanged (`_jpc_pearl_cost`, `_jpc_stone_cost`, `_jpc_extra_fee`)

4. **Price Regeneration:** Existing products should regenerate price breakup to apply new calculation types

---

## 🚀 Version Bump

Update plugin version in main file:
```php
// jewellery-price-calculator.php
define('JPC_VERSION', '2.5.0');
```

---

## 📞 Support

For issues or questions about this update:
1. Check all patch files are applied correctly
2. Verify settings are saved
3. Clear WordPress cache
4. Regenerate price breakups for existing products

---

**Release Date:** February 2026  
**Compatibility:** WordPress 5.0+, WooCommerce 3.0+  
**Tested:** WordPress 6.4, WooCommerce 8.5
