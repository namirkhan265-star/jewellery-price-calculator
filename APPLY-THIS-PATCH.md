# ⚡ APPLY THIS PATCH - ONE LINE CHANGE

## 🎯 You need to change ONE line in ONE file

### File: `includes/class-jpc-admin.php`

**Find line 425 (approximately):**
```php
include JPC_PLUGIN_DIR . 'templates/admin/metals.php';
```

**Replace with:**
```php
include JPC_PLUGIN_DIR . 'templates/admin/metals-v2.php';
```

---

## ✅ THAT'S IT!

After making this change:

1. **Deactivate** the plugin
2. **Reactivate** the plugin
3. Go to **JPC → Metals**
4. You should now see the **"Making Charges per Gram"** field!

---

## 📋 VERIFICATION

After the change, verify:

### Metals Page
- [ ] New field: "Making Charges per Gram (₹)"
- [ ] New column in metals list
- [ ] Can add/edit metals with making charges

### Product Edit Page
- [ ] See "Making Charges" section with toggle
- [ ] See "Diamond Details" section with toggle
- [ ] Both toggles work (Auto/Manual)

---

## 🐛 STILL NOT WORKING?

If you still don't see changes:

### Check 1: Files Uploaded
Verify these files exist on your server:
```
includes/class-jpc-database-v2.php
includes/class-jpc-product-meta-box-v2.php
includes/class-jpc-price-calculator-v2.php
templates/admin/metals-v2.php
templates/product-meta-box/diamond-section-v2.php
templates/product-meta-box/other-costs-section.php
assets/js/product-meta-box-v2.js
```

### Check 2: Main Plugin File
Open `jewellery-price-calculator.php` and verify:
- Line 6: `Version: 2.0.0`
- Line 22: `define('JPC_VERSION', '2.0.0');`
- Line 35: `require_once JPC_PLUGIN_DIR . 'includes/class-jpc-database-v2.php';`
- Line 47: `require_once JPC_PLUGIN_DIR . 'includes/class-jpc-price-calculator-v2.php';`
- Line 48: `require_once JPC_PLUGIN_DIR . 'includes/class-jpc-product-meta-box-v2.php';`

### Check 3: Clear Cache
- Clear WordPress cache
- Clear browser cache
- Try in incognito/private window

### Check 4: Check for Errors
Enable debugging in `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Then check `wp-content/debug.log` for errors.

---

## 📞 QUICK TROUBLESHOOTING

**Problem:** "Fatal error: Class not found"
**Solution:** Make sure all v2 files are uploaded

**Problem:** "Metals page looks the same"
**Solution:** Make sure you changed `metals.php` to `metals-v2.php` in admin class

**Problem:** "Product page looks the same"
**Solution:** Make sure main plugin file includes `class-jpc-product-meta-box-v2.php`

**Problem:** "JavaScript not working"
**Solution:** Make sure `assets/js/product-meta-box-v2.js` is uploaded

---

## ✅ COMPLETE FILE LIST

Make sure you have ALL these files:

**Downloaded from GitHub:**
1. ✅ `jewellery-price-calculator.php` (UPDATED - re-download)
2. ✅ `includes/class-jpc-database-v2.php` (NEW)
3. ✅ `includes/class-jpc-metals.php` (UPDATED - re-download)
4. ✅ `includes/class-jpc-product-meta-box-v2.php` (NEW)
5. ✅ `includes/class-jpc-price-calculator-v2.php` (NEW)
6. ✅ `templates/admin/metals-v2.php` (NEW)
7. ✅ `templates/product-meta-box/diamond-section-v2.php` (NEW)
8. ✅ `templates/product-meta-box/other-costs-section.php` (NEW)
9. ✅ `assets/js/product-meta-box-v2.js` (NEW)

**Manual Change:**
10. ✅ `includes/class-jpc-admin.php` - Change line 425

---

## 🎉 AFTER IT WORKS

Once you see the new fields:

1. **Add making charges to metals:**
   - Go to JPC → Metals
   - Edit each metal
   - Add "Making Charges per Gram" (e.g., ₹50)
   - Save

2. **Test on a product:**
   - Edit any product
   - Select metal with making charges
   - Enter weight
   - See auto-calculated making charges!

---

**Need more help?** Let me know what error you're seeing!
