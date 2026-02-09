# ⚡ v2.0.0 QUICK DEPLOYMENT GUIDE

## 🎯 5-MINUTE SETUP

### STEP 1: Download Files (1 min)

Download these 8 files from GitHub:

**Includes:**
- `includes/class-jpc-database-v2.php`
- `includes/class-jpc-metals.php` (updated)
- `includes/class-jpc-product-meta-box-v2.php`
- `includes/class-jpc-price-calculator-v2.php`

**Templates:**
- `templates/admin/metals-v2.php`
- `templates/product-meta-box/diamond-section-v2.php`
- `templates/product-meta-box/other-costs-section.php`

**Assets:**
- `assets/js/product-meta-box-v2.js`

---

### STEP 2: Update 2 Files (2 min)

**File 1: `jewellery-price-calculator.php`**

```php
// Line 3
* Version: 2.0.0

// Line ~30
define('JPC_VERSION', '2.0.0');

// Line ~80 - Change these 3 includes:
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-database-v2.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-price-calculator-v2.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-product-meta-box-v2.php';
```

**File 2: `includes/class-jpc-admin.php`**

```php
// Line ~425
public function render_metals() {
    include JPC_PLUGIN_DIR . 'templates/admin/metals-v2.php';
}
```

---

### STEP 3: Upload & Activate (1 min)

1. Upload all files via FTP/cPanel
2. Deactivate plugin
3. Reactivate plugin (runs migration)
4. Done!

---

### STEP 4: Test (1 min)

**Quick Test:**
1. Go to JPC → Metals
2. Add metal with making charges per gram: ₹50
3. Edit product
4. See new toggles
5. Save product
6. Check price calculated correctly

✅ If all works → You're done!

---

## 🔥 WHAT'S NEW

### Metals Page
- New field: "Making Charges per Gram (₹)"
- New column in list showing making charges
- Visual indicators (★ NEW)

### Product Page
- **Making Charges Toggle:**
  - Auto: Weight × Per Gram
  - Manual: Custom % or ₹

- **Diamond Entry Toggle:**
  - Dropdown: Select from list
  - Manual: Enter all 4Cs

---

## 📋 FILE CHECKLIST

Before deploying, verify you have:

- [ ] class-jpc-database-v2.php
- [ ] class-jpc-metals.php (updated)
- [ ] class-jpc-product-meta-box-v2.php
- [ ] class-jpc-price-calculator-v2.php
- [ ] metals-v2.php
- [ ] diamond-section-v2.php
- [ ] other-costs-section.php
- [ ] product-meta-box-v2.js
- [ ] Updated main plugin file
- [ ] Updated admin class

---

## ⚠️ IMPORTANT NOTES

### Backward Compatible
- ✅ Old products work without changes
- ✅ No data loss
- ✅ Can rollback if needed

### Migration
- Runs automatically on activation
- Adds `making_charges_per_gram` column
- Sets default modes for old products

### Defaults
- Making charges: Auto mode
- Diamond entry: Dropdown mode
- Making charges per gram: 0 (can update)

---

## 🐛 TROUBLESHOOTING

**Issue:** Metals page doesn't show new fields  
**Fix:** Clear cache, verify metals-v2.php uploaded

**Issue:** Product page doesn't show toggles  
**Fix:** Verify meta-box-v2.php uploaded and included

**Issue:** JavaScript not working  
**Fix:** Check product-meta-box-v2.js uploaded to assets/js/

**Issue:** Prices not calculating  
**Fix:** Verify calculator-v2.php uploaded and included

---

## 📞 NEED HELP?

1. Check `v2-INTEGRATION-GUIDE.md` for detailed steps
2. Check `v2-COMPLETE-SUMMARY.md` for full documentation
3. Enable WP_DEBUG to see errors
4. Check browser console for JS errors

---

## ✅ POST-DEPLOYMENT

After successful deployment:

1. **Update Metals:**
   - Add making charges per gram to each metal
   - Example: Gold 22kt → ₹50/gram

2. **Test Products:**
   - Create new product with auto making charges
   - Create new product with manual diamond entry
   - Verify prices calculate correctly

3. **Train Users:**
   - Show new making charges toggle
   - Show manual diamond entry
   - Explain when to use each mode

---

## 🎉 DONE!

Your plugin is now v2.0.0 with:
- ✅ Auto making charges calculation
- ✅ Manual making charges override
- ✅ Manual diamond entry with 4Cs
- ✅ Live price calculations
- ✅ Full backward compatibility

**Enjoy the new features! 🚀**

---

**Quick Links:**
- Full Guide: `v2-INTEGRATION-GUIDE.md`
- Complete Summary: `v2-COMPLETE-SUMMARY.md`
- Specification: `v2-MAJOR-CHANGES-SPEC.md`
