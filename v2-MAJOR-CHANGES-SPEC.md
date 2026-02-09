# v2.0.0 - MAJOR CHANGES SPECIFICATION

## Overview
This document outlines the major changes requested for the Jewellery Price Calculator plugin.

---

## 1. METALS - Making Charges Per Gram

### Database Changes
**Table:** `wp_jpc_metals`
**New Column:** `making_charges_per_gram` DECIMAL(10,2) DEFAULT 0

### Admin Interface Changes

#### Metals Page (templates/admin/metals.php)
**Add New Metal Form - Add Field:**
```
Making Charges per Gram (₹): [input field]
Description: "This will be used to auto-calculate making charges (Metal Weight × Per Gram Rate)"
```

**Metals List Table - Add Column:**
```
| ID | Name | Display Name | Group | Price/Unit | Making Charges/Gram | Actions |
```

**Edit Modal - Add Field:**
```
Making Charges per Gram: [input field]
```

### Backend Changes
**File:** `includes/class-jpc-metals.php`
- Update `add()` method to include `making_charges_per_gram`
- Update `update()` method to include `making_charges_per_gram`
- Update `get_all()` to fetch `making_charges_per_gram`

---

## 2. PRODUCT PAGE - Making Charges Toggle

### Product Meta Box Changes
**File:** `includes/class-jpc-product-meta-box.php`

**Current:**
```
Making Charges (%): [input]
```

**New:**
```
Making Charges Calculation:
( ) Auto Calculate (Metal Weight × ₹X per gram)  [DEFAULT SELECTED]
( ) Manual Entry

[If Auto selected]
  Auto-calculated: ₹XXX (Based on X grams × ₹Y per gram)
  [Read-only display]

[If Manual selected]
  Making Charges (%): [input field]
  OR
  Making Charges (₹): [input field]
```

### Meta Fields to Store
```
_jpc_making_charges_mode: 'auto' or 'manual' (default: 'auto')
_jpc_making_charges_value: numeric value (only used if manual)
_jpc_making_charges_type: 'percentage' or 'fixed' (only used if manual)
```

---

## 3. PRODUCT PAGE - Manual Diamond Entry

### Current Diamond Section
```
Diamond: [Dropdown - Select from pre-created diamonds]
Diamond Quantity: [input]
```

### New Diamond Section
```
Diamond Entry Mode:
( ) Select from List [DEFAULT]
( ) Enter Details Manually

[If "Select from List"]
  Diamond: [Dropdown]
  Diamond Quantity: [input]

[If "Enter Details Manually"]
  ┌─────────────────────────────────────────┐
  │ Manual Diamond Entry                     │
  ├─────────────────────────────────────────┤
  │ Diamond Group: [Dropdown]               │
  │ Carat Size: [Dropdown - 0.01 to 10.00]  │
  │ Certification: [Dropdown - GIA/IGI/etc] │
  │ Shape: [Dropdown - Round/Princess/etc]  │
  │ Colour: [Dropdown - D/E/F/G/etc]        │
  │ Clarity: [Dropdown - FL/IF/VVS/etc]     │
  │ Cut: [Dropdown - Excellent/VG/etc]      │
  │ Quantity: [input]                        │
  │ Price per Carat (₹): [input]            │
  └─────────────────────────────────────────┘
```

### Meta Fields to Store
```
_jpc_diamond_entry_mode: 'dropdown' or 'manual' (default: 'dropdown')

[If dropdown mode]
_jpc_diamond_id: ID from diamonds table
_jpc_diamond_quantity: numeric

[If manual mode]
_jpc_manual_diamond_group_id: ID
_jpc_manual_diamond_carat: decimal
_jpc_manual_diamond_certification_id: ID
_jpc_manual_diamond_shape_id: ID
_jpc_manual_diamond_colour_id: ID
_jpc_manual_diamond_clarity_id: ID
_jpc_manual_diamond_cut_id: ID
_jpc_manual_diamond_quantity: numeric
_jpc_manual_diamond_price_per_carat: decimal
```

---

## 4. PRICE CALCULATION LOGIC

### Making Charges Calculation

**Auto Mode:**
```php
$metal_weight = get_post_meta($product_id, '_jpc_metal_weight', true);
$metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
$metal = JPC_Metals::get_by_id($metal_id);
$making_charges = $metal_weight * $metal->making_charges_per_gram;
```

**Manual Mode:**
```php
$mode = get_post_meta($product_id, '_jpc_making_charges_mode', true);
if ($mode === 'manual') {
    $value = get_post_meta($product_id, '_jpc_making_charges_value', true);
    $type = get_post_meta($product_id, '_jpc_making_charges_type', true);
    
    if ($type === 'percentage') {
        $making_charges = ($metal_cost * $value) / 100;
    } else {
        $making_charges = $value;
    }
}
```

### Diamond Price Calculation

**Dropdown Mode (Existing):**
```php
$diamond_id = get_post_meta($product_id, '_jpc_diamond_id', true);
$diamond = JPC_Diamonds::get_by_id($diamond_id);
$quantity = get_post_meta($product_id, '_jpc_diamond_quantity', true);
$diamond_cost = $diamond->price_per_carat * $diamond->carat * $quantity;
```

**Manual Mode (New):**
```php
$mode = get_post_meta($product_id, '_jpc_diamond_entry_mode', true);
if ($mode === 'manual') {
    $carat = get_post_meta($product_id, '_jpc_manual_diamond_carat', true);
    $quantity = get_post_meta($product_id, '_jpc_manual_diamond_quantity', true);
    $price_per_carat = get_post_meta($product_id, '_jpc_manual_diamond_price_per_carat', true);
    
    // Get 4Cs adjustments
    $shape_adj = JPC_Diamond_Shapes::get_adjustment($shape_id);
    $colour_adj = JPC_Diamond_Colours::get_adjustment($colour_id);
    $clarity_adj = JPC_Diamond_Clarities::get_adjustment($clarity_id);
    $cut_adj = JPC_Diamond_Cuts::get_adjustment($cut_id);
    $cert_adj = JPC_Diamond_Certifications::get_adjustment($cert_id);
    
    // Apply adjustments
    $adjusted_price = $price_per_carat;
    $adjusted_price = apply_adjustment($adjusted_price, $shape_adj);
    $adjusted_price = apply_adjustment($adjusted_price, $colour_adj);
    $adjusted_price = apply_adjustment($adjusted_price, $clarity_adj);
    $adjusted_price = apply_adjustment($adjusted_price, $cut_adj);
    $adjusted_price = apply_adjustment($adjusted_price, $cert_adj);
    
    $diamond_cost = $adjusted_price * $carat * $quantity;
}
```

---

## 5. FILES TO CREATE/MODIFY

### New Files
1. `includes/class-jpc-database-v2.php` ✅ CREATED
2. `templates/admin/metals-v2.php` (with making charges per gram)
3. `includes/class-jpc-product-meta-box-v2.php` (with toggles)
4. `includes/class-jpc-price-calculator-v2.php` (updated logic)

### Modified Files
1. `includes/class-jpc-metals.php` (add making_charges_per_gram support)
2. `includes/class-jpc-admin.php` (use new templates)
3. `jewellery-price-calculator.php` (version bump to 2.0.0)

---

## 6. IMPLEMENTATION PHASES

### Phase 1: Database ✅
- Add making_charges_per_gram column to metals table
- Status: COMPLETE

### Phase 2: Metals Admin Interface
- Update metals.php template
- Add making charges per gram field
- Update AJAX handlers

### Phase 3: Product Meta Box - Making Charges
- Add radio toggle (Auto/Manual)
- Show auto-calculated value
- Show manual input fields conditionally

### Phase 4: Product Meta Box - Diamond Entry
- Add radio toggle (Dropdown/Manual)
- Create manual entry form with all 4Cs
- Add carat size dropdown (0.01 to 10.00)

### Phase 5: Price Calculator
- Update making charges calculation logic
- Update diamond price calculation logic
- Handle both modes properly

### Phase 6: Testing
- Test auto making charges calculation
- Test manual making charges
- Test dropdown diamond selection
- Test manual diamond entry
- Test price calculations for all combinations

---

## 7. USER EXPERIENCE FLOW

### Adding a Product

**Step 1: Select Metal**
- Choose metal from dropdown
- Enter metal weight
- System shows: "Making charges will be auto-calculated at ₹X per gram"

**Step 2: Making Charges**
- Default: Auto-calculate (shows calculated value)
- Optional: Switch to manual and enter custom value

**Step 3: Diamond Entry**
- Default: Select from dropdown list
- Optional: Switch to manual and fill all 4Cs details

**Step 4: Save**
- System calculates final price
- Shows complete price breakup

---

## 8. BACKWARD COMPATIBILITY

### Existing Products
- Products without `_jpc_making_charges_mode` → default to 'auto'
- Products without `_jpc_diamond_entry_mode` → default to 'dropdown'
- Existing making charges values → migrate to manual mode

### Migration Script
```php
function jpc_migrate_to_v2() {
    // Get all products with JPC data
    $products = get_posts(array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'meta_key' => '_jpc_metal_id'
    ));
    
    foreach ($products as $product) {
        // Set default modes if not set
        if (!get_post_meta($product->ID, '_jpc_making_charges_mode', true)) {
            update_post_meta($product->ID, '_jpc_making_charges_mode', 'auto');
        }
        
        if (!get_post_meta($product->ID, '_jpc_diamond_entry_mode', true)) {
            update_post_meta($product->ID, '_jpc_diamond_entry_mode', 'dropdown');
        }
    }
}
```

---

## 9. VALIDATION RULES

### Making Charges
- Auto mode: No validation needed (calculated)
- Manual mode: Value must be >= 0

### Manual Diamond Entry
- All fields required except 4Cs (optional)
- Carat: 0.01 to 10.00
- Quantity: >= 1
- Price per carat: > 0

---

## 10. PRICE BREAKUP DISPLAY

### Updated Breakup
```
Metal Cost: ₹X
Making Charges: ₹Y [Auto: X grams × ₹Z/gram] or [Manual: X%]
Wastage: ₹Z
Diamond Cost: ₹A [From: Diamond Name] or [Manual: X ct × ₹Y/ct]
Stone Cost: ₹B
Pearl Cost: ₹C
Extra Fees: ₹D
Subtotal: ₹E
GST (3%): ₹F
─────────────
Total: ₹G
```

---

**Status:** Specification Complete
**Next Step:** Implementation Phase 2 (Metals Admin Interface)
