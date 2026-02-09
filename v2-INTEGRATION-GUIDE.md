# v2.0.0 Integration Guide

## 🎯 OVERVIEW

This guide explains how to integrate all v2.0.0 changes into your existing plugin.

---

## 📋 STEP-BY-STEP INTEGRATION

### STEP 1: Update Main Plugin File

**File:** `jewellery-price-calculator.php`

**Changes Required:**

```php
// Line 3 - Update version
* Version: 2.0.0

// Line ~30 - Update constant
define('JPC_VERSION', '2.0.0');

// Line ~80 - Replace database class include
// OLD:
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-database.php';
// NEW:
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-database-v2.php';

// Line ~100 - Replace price calculator include
// OLD:
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-price-calculator.php';
// NEW:
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-price-calculator-v2.php';

// Line ~110 - Replace product meta box include
// OLD:
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-product-meta-box.php';
// NEW:
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-product-meta-box-v2.php';

// Line ~200 - Add migration hook (AFTER activation hook)
register_activation_hook(__FILE__, 'jpc_v2_migration');

function jpc_v2_migration() {
    // Run database v2 migration
    JPC_Database::create_tables();
    
    // Migrate existing products to v2 defaults
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_jpc_metal_id',
                'compare' => 'EXISTS'
            )
        )
    );
    
    $products = get_posts($args);
    
    foreach ($products as $product) {
        // Set default modes if not set
        if (!get_post_meta($product->ID, '_jpc_making_charges_mode', true)) {
            update_post_meta($product->ID, '_jpc_making_charges_mode', 'auto');
        }
        
        if (!get_post_meta($product->ID, '_jpc_diamond_entry_mode', true)) {
            update_post_meta($product->ID, '_jpc_diamond_entry_mode', 'dropdown');
        }
    }
    
    // Set migration flag
    update_option('jpc_v2_migrated', true);
}
```

---

### STEP 2: Update Admin Class

**File:** `includes/class-jpc-admin.php`

**Find (around line 425):**
```php
public function render_metals() {
    include JPC_PLUGIN_DIR . 'templates/admin/metals.php';
}
```

**Replace with:**
```php
public function render_metals() {
    include JPC_PLUGIN_DIR . 'templates/admin/metals-v2.php';
}
```

---

### STEP 3: Create Template Directories

Create these directories if they don't exist:
```
templates/product-meta-box/
```

---

### STEP 4: Upload New Files

Upload these files to your plugin directory:

**Includes:**
1. `includes/class-jpc-database-v2.php` ✅
2. `includes/class-jpc-metals.php` (updated) ✅
3. `includes/class-jpc-product-meta-box-v2.php` ✅
4. `includes/class-jpc-price-calculator-v2.php` ✅

**Templates:**
5. `templates/admin/metals-v2.php` ✅
6. `templates/product-meta-box/diamond-section-v2.php` ✅
7. `templates/product-meta-box/other-costs-section.php` ✅

**Assets:**
8. `assets/js/product-meta-box-v2.js` ✅

---

### STEP 5: Database Migration

The database will auto-migrate when you:
1. Activate the plugin (if deactivated)
2. Or manually run: `JPC_Database::create_tables()`

The migration adds:
- `making_charges_per_gram` column to `wp_jpc_metals` table

---

### STEP 6: Test Everything

**Test Metals Admin:**
1. Go to JPC → Metals
2. Add new metal with making charges per gram
3. Edit existing metal
4. Verify making charges column shows

**Test Product Page:**
1. Edit any product
2. See making charges toggle (Auto/Manual)
3. See diamond entry toggle (Dropdown/Manual)
4. Test auto-calculation displays
5. Save and verify price calculation

**Test Price Calculation:**
1. Create product with auto making charges
2. Create product with manual making charges
3. Create product with manual diamond entry
4. Verify all prices calculate correctly

---

## 🔄 BACKWARD COMPATIBILITY

### Existing Products
- Products without `_jpc_making_charges_mode` → Auto mode (default)
- Products without `_jpc_diamond_entry_mode` → Dropdown mode (default)
- All existing functionality preserved

### Old Meta Fields
- Old making charge fields still work
- Old diamond selection still works
- No data loss

---

## 📊 VERIFICATION CHECKLIST

After integration, verify:

- [ ] Plugin activates without errors
- [ ] Metals page loads with new fields
- [ ] Can add metal with making charges per gram
- [ ] Can edit metal and update making charges
- [ ] Product edit page shows new toggles
- [ ] Auto making charges calculates correctly
- [ ] Manual making charges works (percentage & fixed)
- [ ] Manual diamond entry shows all 4Cs
- [ ] Manual diamond price calculates with adjustments
- [ ] Product prices save correctly
- [ ] Frontend displays correct prices
- [ ] Existing products still work
- [ ] No JavaScript errors in console

---

## 🐛 TROUBLESHOOTING

### Issue: Metals page doesn't show new fields
**Solution:** Clear browser cache, check if metals-v2.php is loaded

### Issue: Product page doesn't show toggles
**Solution:** Verify class-jpc-product-meta-box-v2.php is included

### Issue: JavaScript not working
**Solution:** Check if product-meta-box-v2.js is enqueued, check console for errors

### Issue: Prices not calculating
**Solution:** Verify class-jpc-price-calculator-v2.php is included

### Issue: Database error
**Solution:** Run migration manually: `JPC_Database::create_tables()`

---

## 📝 ROLLBACK PROCEDURE

If you need to rollback to v1.x:

1. **Restore old files:**
   - `jewellery-price-calculator.php` (change version back)
   - `includes/class-jpc-admin.php` (use metals.php)

2. **Keep database:**
   - The `making_charges_per_gram` column won't hurt
   - Old code will ignore it

3. **Products will work:**
   - Default modes ensure compatibility
   - No data loss

---

## 🎉 POST-INTEGRATION

After successful integration:

1. **Update all metal records:**
   - Add making charges per gram to each metal
   - This enables auto-calculation

2. **Review existing products:**
   - Check if auto-calculation works
   - Adjust any that need manual mode

3. **Train users:**
   - Show new making charges toggle
   - Show manual diamond entry feature

---

## 📞 SUPPORT

If you encounter issues:

1. Check error logs: `wp-content/debug.log`
2. Enable WP_DEBUG in wp-config.php
3. Check browser console for JS errors
4. Verify all files uploaded correctly

---

**Version:** 2.0.0  
**Status:** Ready for Integration  
**Backward Compatible:** Yes  
**Data Loss Risk:** None
