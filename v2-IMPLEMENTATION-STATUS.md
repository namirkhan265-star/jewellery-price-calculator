# v2.0.0 Implementation Status

## ✅ COMPLETED

### 1. Database Schema (Phase 1)
**File:** `includes/class-jpc-database-v2.php`
- ✅ Added `making_charges_per_gram` column to metals table
- ✅ Auto-migration for existing installations
- ✅ All diamond 4Cs tables ready

### 2. Metals Admin Interface (Phase 2)
**File:** `templates/admin/metals-v2.php`
- ✅ Added "Making Charges per Gram" field in Add Metal form
- ✅ Added "Making Charges/Gram" column in metals list table
- ✅ Added field in Edit Metal modal
- ✅ Updated JavaScript to handle new field
- ✅ Added helpful descriptions and examples
- ✅ Visual indicators for auto-calc enabled metals

---

## 🔄 IN PROGRESS

### 3. Metals Backend Handler
**File:** `includes/class-jpc-metals.php`
**Status:** Needs Update

**Required Changes:**
```php
// In add() method - line ~70
$insert_data = array(
    'name' => sanitize_text_field($data['name']),
    'display_name' => sanitize_text_field($data['display_name']),
    'metal_group_id' => intval($data['metal_group_id']),
    'price_per_unit' => floatval($data['price_per_unit']),
    'making_charges_per_gram' => floatval($data['making_charges_per_gram'] ?? 0), // ADD THIS
);

// In update() method - line ~95
$update_data = array(
    'name' => sanitize_text_field($data['name']),
    'display_name' => sanitize_text_field($data['display_name']),
    'metal_group_id' => intval($data['metal_group_id']),
    'price_per_unit' => floatval($data['price_per_unit']),
    'making_charges_per_gram' => floatval($data['making_charges_per_gram'] ?? 0), // ADD THIS
);

// In ajax_add_metal() method - line ~150
$data = array(
    'name' => $_POST['name'],
    'display_name' => $_POST['display_name'],
    'metal_group_id' => $_POST['metal_group_id'],
    'price_per_unit' => $_POST['price_per_unit'],
    'making_charges_per_gram' => $_POST['making_charges_per_gram'] ?? 0, // ADD THIS
);

// In ajax_update_metal() method - line ~180
$data = array(
    'name' => $_POST['name'],
    'display_name' => $_POST['display_name'],
    'metal_group_id' => $_POST['metal_group_id'],
    'price_per_unit' => $_POST['price_per_unit'],
    'making_charges_per_gram' => $_POST['making_charges_per_gram'] ?? 0, // ADD THIS
);
```

---

## 📋 TODO (Remaining Phases)

### Phase 3: Product Meta Box - Making Charges Toggle
**File:** `includes/class-jpc-product-meta-box-v2.php` (NEW)

**Required Features:**
1. Radio toggle: Auto Calculate / Manual Entry
2. Auto mode: Show calculated value (read-only)
3. Manual mode: Show input fields (percentage or fixed)
4. Save meta fields:
   - `_jpc_making_charges_mode` (auto/manual)
   - `_jpc_making_charges_value` (numeric)
   - `_jpc_making_charges_type` (percentage/fixed)

**UI Mockup:**
```
┌─────────────────────────────────────────┐
│ Making Charges Calculation              │
├─────────────────────────────────────────┤
│ (•) Auto Calculate                      │
│     Based on metal weight × ₹X per gram │
│     Calculated: ₹500 (10g × ₹50/g)     │
│                                         │
│ ( ) Manual Entry                        │
│     [ ] Percentage: [___]%              │
│     [ ] Fixed Amount: ₹[___]            │
└─────────────────────────────────────────┘
```

### Phase 4: Product Meta Box - Manual Diamond Entry
**File:** Same as Phase 3

**Required Features:**
1. Radio toggle: Select from List / Enter Manually
2. Dropdown mode: Existing functionality
3. Manual mode: Complete form with all fields
4. Save meta fields:
   - `_jpc_diamond_entry_mode` (dropdown/manual)
   - `_jpc_manual_diamond_*` (all manual fields)

**UI Mockup:**
```
┌─────────────────────────────────────────┐
│ Diamond Entry                           │
├─────────────────────────────────────────┤
│ ( ) Select from List                    │
│     Diamond: [Dropdown ▼]               │
│     Quantity: [___]                     │
│                                         │
│ (•) Enter Details Manually              │
│     Diamond Group: [Natural ▼]          │
│     Carat Size: [1.00 ▼]                │
│     Certification: [GIA ▼]              │
│     Shape: [Round ▼]                    │
│     Colour: [D ▼]                       │
│     Clarity: [VVS1 ▼]                   │
│     Cut: [Excellent ▼]                  │
│     Quantity: [1]                       │
│     Price per Carat: ₹[25000]           │
└─────────────────────────────────────────┘
```

### Phase 5: Price Calculator Update
**File:** `includes/class-jpc-price-calculator-v2.php` (NEW)

**Required Logic Updates:**

**Making Charges:**
```php
function calculate_making_charges($product_id) {
    $mode = get_post_meta($product_id, '_jpc_making_charges_mode', true) ?: 'auto';
    
    if ($mode === 'auto') {
        $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
        $metal_weight = get_post_meta($product_id, '_jpc_metal_weight', true);
        $metal = JPC_Metals::get_by_id($metal_id);
        
        return $metal_weight * ($metal->making_charges_per_gram ?? 0);
    } else {
        $value = get_post_meta($product_id, '_jpc_making_charges_value', true);
        $type = get_post_meta($product_id, '_jpc_making_charges_type', true);
        $metal_cost = calculate_metal_cost($product_id);
        
        if ($type === 'percentage') {
            return ($metal_cost * $value) / 100;
        } else {
            return $value;
        }
    }
}
```

**Diamond Cost:**
```php
function calculate_diamond_cost($product_id) {
    $mode = get_post_meta($product_id, '_jpc_diamond_entry_mode', true) ?: 'dropdown';
    
    if ($mode === 'dropdown') {
        // Existing logic
        $diamond_id = get_post_meta($product_id, '_jpc_diamond_id', true);
        $diamond = JPC_Diamonds::get_by_id($diamond_id);
        $quantity = get_post_meta($product_id, '_jpc_diamond_quantity', true);
        return $diamond->price_per_carat * $diamond->carat * $quantity;
    } else {
        // Manual entry logic
        $carat = get_post_meta($product_id, '_jpc_manual_diamond_carat', true);
        $quantity = get_post_meta($product_id, '_jpc_manual_diamond_quantity', true);
        $base_price = get_post_meta($product_id, '_jpc_manual_diamond_price_per_carat', true);
        
        // Get 4Cs adjustments
        $shape_id = get_post_meta($product_id, '_jpc_manual_diamond_shape_id', true);
        $colour_id = get_post_meta($product_id, '_jpc_manual_diamond_colour_id', true);
        $clarity_id = get_post_meta($product_id, '_jpc_manual_diamond_clarity_id', true);
        $cut_id = get_post_meta($product_id, '_jpc_manual_diamond_cut_id', true);
        $cert_id = get_post_meta($product_id, '_jpc_manual_diamond_certification_id', true);
        
        $adjusted_price = apply_all_adjustments($base_price, $shape_id, $colour_id, $clarity_id, $cut_id, $cert_id);
        
        return $adjusted_price * $carat * $quantity;
    }
}
```

### Phase 6: Admin Class Update
**File:** `includes/class-jpc-admin.php`

**Change Required:**
```php
// Line ~XXX - Update render_metals method
public function render_metals() {
    include JPC_PLUGIN_DIR . 'templates/admin/metals-v2.php'; // Change from metals.php
}
```

### Phase 7: Main Plugin File Update
**File:** `jewellery-price-calculator.php`

**Changes Required:**
```php
// Line 3 - Update version
* Version: 2.0.0

// Line ~30 - Update constant
define('JPC_VERSION', '2.0.0');

// Include new files
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-database-v2.php'; // Use v2
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-product-meta-box-v2.php'; // NEW
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-price-calculator-v2.php'; // NEW
```

---

## 🧪 TESTING CHECKLIST

### Metals
- [ ] Add new metal with making charges per gram
- [ ] Edit existing metal to add making charges
- [ ] Verify making charges display in list
- [ ] Test AJAX save/update

### Product - Making Charges
- [ ] Auto mode: Verify calculation (weight × per gram)
- [ ] Manual mode: Test percentage calculation
- [ ] Manual mode: Test fixed amount
- [ ] Toggle between modes
- [ ] Verify price breakup display

### Product - Diamond Entry
- [ ] Dropdown mode: Existing functionality works
- [ ] Manual mode: All fields save correctly
- [ ] Manual mode: 4Cs adjustments apply
- [ ] Manual mode: Price calculation correct
- [ ] Toggle between modes
- [ ] Verify price breakup display

### Price Calculation
- [ ] Auto making charges calculation
- [ ] Manual making charges (percentage)
- [ ] Manual making charges (fixed)
- [ ] Manual diamond with all 4Cs
- [ ] Combined: Auto making + Manual diamond
- [ ] Combined: Manual making + Dropdown diamond
- [ ] Final price matches breakup

### Backward Compatibility
- [ ] Existing products load correctly
- [ ] Default modes apply (auto/dropdown)
- [ ] No errors on old products
- [ ] Migration script works

---

## 📦 FILES SUMMARY

### ✅ Created
1. `includes/class-jpc-database-v2.php`
2. `templates/admin/metals-v2.php`
3. `v2-MAJOR-CHANGES-SPEC.md`
4. `v2-IMPLEMENTATION-STATUS.md` (this file)

### 🔄 Need to Create
1. `includes/class-jpc-metals-v2.php` (or update existing)
2. `includes/class-jpc-product-meta-box-v2.php`
3. `includes/class-jpc-price-calculator-v2.php`

### 🔄 Need to Update
1. `includes/class-jpc-metals.php` (add making_charges_per_gram support)
2. `includes/class-jpc-admin.php` (use metals-v2.php template)
3. `jewellery-price-calculator.php` (version bump, include new files)

---

## 🚀 NEXT STEPS

**Immediate Priority:**
1. Update `class-jpc-metals.php` to handle `making_charges_per_gram`
2. Update `class-jpc-admin.php` to use `metals-v2.php`
3. Test metals admin interface

**Then:**
4. Create `class-jpc-product-meta-box-v2.php` with both toggles
5. Create `class-jpc-price-calculator-v2.php` with updated logic
6. Update main plugin file
7. Full testing

---

**Status:** Phase 2 Complete (Metals UI)  
**Next:** Phase 3 (Update Metals Backend)  
**ETA:** 3-4 more phases to complete
