# 🚨 URGENT FIX REQUIRED - v2.5.37

## ⚡ QUICK START (Choose ONE method)

### Method 1: Automatic Fix (EASIEST - 2 minutes)
1. Download `apply-fix-v2.5.37.php` from this repository
2. Upload to your WordPress root directory
3. Visit: `https://detailx.co.in/apply-fix-v2.5.37.php`
4. Click "Apply Fix Now"
5. Delete the script file
6. Clear all caches
7. **DONE!**

### Method 2: Manual File Edit (5 minutes)
1. Open `includes/class-jpc-product-meta-box-v2.php`
2. Find line 153-158 (the metal dropdown)
3. Add these two lines after line 156:
   ```php
   data-enable-making="<?php echo esc_attr($metal->enable_making_charge ?? 0); ?>"
   data-enable-wastage="<?php echo esc_attr($metal->enable_wastage_charge ?? 0); ?>"
   ```
4. Save and upload
5. Clear all caches
6. **DONE!**

### Method 3: Replace Entire File (3 minutes)
1. Download latest `includes/class-jpc-product-meta-box-v2.php` from GitHub
2. **WAIT** - The file hasn't been updated yet! Use Method 1 or 2 instead.

---

## 📋 WHAT'S BROKEN

- ❌ Metals page shows critical error
- ❌ "Bulk Update All Prices" doesn't work
- ❌ Product edit page may show errors
- ❌ JavaScript console shows errors

## ✅ WHAT THIS FIX DOES

- ✅ Fixes critical error on Metals page
- ✅ Fixes "Bulk Update All Prices" functionality
- ✅ Fixes product edit page errors
- ✅ Removes JavaScript console errors
- ✅ Enables proper show/hide of making charges and wastage fields

---

## 🔍 THE PROBLEM

When you added enable/disable options for making charges and wastage in Metal Groups, the product meta box template wasn't updated to include these settings as data attributes. JavaScript tried to access attributes that didn't exist, causing errors.

## 🔧 THE SOLUTION

Add two missing data attributes to the metal dropdown:
- `data-enable-making` - Controls making charges field visibility
- `data-enable-wastage` - Controls wastage field visibility

---

## 📝 DETAILED INSTRUCTIONS

See `FIX-SUMMARY-v2.5.37.md` for complete technical details.

---

## ⏱️ TIME REQUIRED

- **Automatic Fix:** 2 minutes
- **Manual Fix:** 5 minutes
- **Testing:** 3 minutes
- **Total:** ~10 minutes

---

## 🎯 PRIORITY

**CRITICAL** - This fix is required for the plugin to function correctly.

---

## 📞 NEED HELP?

If you encounter any issues:
1. Check `FIX-SUMMARY-v2.5.37.md` for detailed instructions
2. Check `CRITICAL-FIX-v2.5.37.md` for manual fix steps
3. Run `jpc-debug.php` to diagnose issues
4. Contact support with error details

---

## ✨ AFTER APPLYING THE FIX

1. Clear ALL caches (WordPress, browser, server)
2. Test Metals page
3. Test "Bulk Update All Prices"
4. Test product edit page
5. Check browser console (F12) for errors
6. Delete all fix scripts from your server

---

**Version:** 2.5.37  
**Date:** 2026-02-12  
**Status:** READY TO DEPLOY  
**Estimated Fix Time:** 2-10 minutes
