# 📦 Download & Install v2.5.0 - Custom Labels Feature

## ⚠️ IMPORTANT: Your Current Issue

Your diagnostic shows:
- ✅ Settings are saved correctly (Test 6, Test 7, Test 8)
- ❌ Plugin files are NOT updated in WordPress
- ❌ Products need regeneration after files are updated

## 🎯 Solution: Download & Replace Plugin Files

### Option 1: Download Entire Plugin (RECOMMENDED)

1. **Download the entire repository as ZIP:**
   - Go to: https://github.com/namirkhan265-star/jewellery-price-calculator
   - Click the green **"Code"** button
   - Click **"Download ZIP"**

2. **Extract the ZIP file** on your computer

3. **Locate your WordPress installation:**
   - Via FTP: `/wp-content/plugins/`
   - Via cPanel: `public_html/wp-content/plugins/`

4. **Backup your current plugin folder:**
   - Rename `jewellery-price-calculator` to `jewellery-price-calculator-backup`

5. **Upload the new plugin folder:**
   - Upload the extracted `jewellery-price-calculator-main` folder
   - Rename it to `jewellery-price-calculator`

6. **Activate the plugin** (if it was deactivated)

7. **Regenerate all prices:**
   - Go to **Jewellery Price > General**
   - Scroll to bottom
   - Click **"Update All Prices"**
   - Wait for completion

8. **Check frontend** - Your custom labels should now appear!

---

### Option 2: Replace Only Critical Files (FASTER)

If you want to replace only the files needed for custom labels:

#### Files to Download:

1. **includes/class-jpc-price-calculator.php**
   - URL: https://raw.githubusercontent.com/namirkhan265-star/jewellery-price-calculator/main/includes/class-jpc-price-calculator.php
   - Right-click > Save As > `class-jpc-price-calculator.php`

2. **includes/class-jpc-admin.php**
   - URL: https://raw.githubusercontent.com/namirkhan265-star/jewellery-price-calculator/main/includes/class-jpc-admin.php
   - Right-click > Save As > `class-jpc-admin.php`

3. **templates/frontend/price-breakup.php**
   - URL: https://raw.githubusercontent.com/namirkhan265-star/jewellery-price-calculator/main/templates/frontend/price-breakup.php
   - Right-click > Save As > `price-breakup.php`

4. **templates/frontend/detailed-breakup.php**
   - URL: https://raw.githubusercontent.com/namirkhan265-star/jewellery-price-calculator/main/templates/frontend/detailed-breakup.php
   - Right-click > Save As > `detailed-breakup.php`

5. **templates/admin/general-settings.php**
   - URL: https://raw.githubusercontent.com/namirkhan265-star/jewellery-price-calculator/main/templates/admin/general-settings.php
   - Right-click > Save As > `general-settings.php`

#### Upload via FTP/cPanel:

1. Connect to your server
2. Navigate to: `/wp-content/plugins/jewellery-price-calculator/`
3. Replace these files:
   - `includes/class-jpc-price-calculator.php`
   - `includes/class-jpc-admin.php`
   - `templates/frontend/price-breakup.php`
   - `templates/frontend/detailed-breakup.php`
   - `templates/admin/general-settings.php`

4. **Regenerate all prices:**
   - Go to **Jewellery Price > General**
   - Click **"Update All Prices"**

---

### Option 3: Use Auto-Update Script (EASIEST)

I can create a script that automatically downloads and updates the files for you.

**Would you like me to create this auto-update script?**

---

## 🔍 Verify Installation

After uploading files, run the diagnostic again:

1. Access: `https://yoursite.com/diagnostic-custom-labels.php`
2. Check that:
   - ✅ Code Version Check shows "YES"
   - ✅ Frontend Template Check shows "YES"
3. If both are YES, proceed to regenerate prices

---

## 🚀 After Installation

### Step 1: Regenerate All Prices

**Option A: Bulk Update (Recommended)**
1. Go to **Jewellery Price > General**
2. Scroll to bottom
3. Click **"Update All Prices"** button
4. Wait for completion message

**Option B: Individual Products**
1. Edit each product
2. Click **"Regenerate Price Breakup"** button
3. Save product

### Step 2: Verify Frontend

1. View any product on frontend
2. Check price breakup section
3. You should see:
   - **"Test 6"** instead of "Pearl Cost"
   - **"Test 7"** instead of "Stone Cost"
   - **"Test 8"** instead of "Extra Fee"

### Step 3: Clear Cache

If labels still don't show:
- Clear browser cache (Ctrl+Shift+R)
- Clear WordPress cache (if using caching plugin)
- Clear WooCommerce transients

---

## 📋 File Locations Reference

Your WordPress plugin should have this structure:

```
wp-content/plugins/jewellery-price-calculator/
├── includes/
│   ├── class-jpc-price-calculator.php  ← MUST UPDATE
│   ├── class-jpc-admin.php             ← MUST UPDATE
│   └── ...
├── templates/
│   ├── admin/
│   │   ├── general-settings.php        ← MUST UPDATE
│   │   └── ...
│   └── frontend/
│       ├── price-breakup.php           ← MUST UPDATE
│       ├── detailed-breakup.php        ← MUST UPDATE
│       └── ...
└── jewellery-price-calculator.php
```

---

## ❓ Troubleshooting

### Issue: "Cannot find class-jpc-price-calculator.php file"

**Cause:** Plugin folder name is different

**Solution:**
1. Check your actual plugin folder name in `/wp-content/plugins/`
2. It might be:
   - `jewellery-price-calc`
   - `jpc-calculator`
   - `jewellery-calculator`
3. Upload files to the correct folder

### Issue: Files uploaded but diagnostic still shows error

**Cause:** Wrong folder or file permissions

**Solution:**
1. Verify file paths are exactly:
   - `/wp-content/plugins/jewellery-price-calculator/includes/class-jpc-price-calculator.php`
2. Check file permissions (should be 644)
3. Clear server cache if using caching

### Issue: "Update All Prices" button doesn't work

**Cause:** Old admin file

**Solution:**
1. Make sure you updated `includes/class-jpc-admin.php`
2. Check WordPress error log for PHP errors
3. Try individual product regeneration instead

---

## 🆘 Still Need Help?

If you're still having issues after following this guide:

1. **Run diagnostic again** and share screenshot
2. **Check WordPress error log** for any PHP errors
3. **Verify file permissions** (should be 644 for files, 755 for folders)
4. **Try deactivating/reactivating** the plugin

---

## 📝 Version Information

- **Current Version:** v2.5.0
- **Release Date:** February 9, 2026
- **Feature:** Custom Labels for Additional Cost Fields
- **Compatibility:** WordPress 5.0+, WooCommerce 3.0+

---

## ✅ Success Checklist

- [ ] Downloaded latest plugin files
- [ ] Backed up current plugin folder
- [ ] Uploaded new files to correct location
- [ ] Verified files exist (via diagnostic or FTP)
- [ ] Regenerated all product prices
- [ ] Checked frontend - custom labels visible
- [ ] Cleared all caches
- [ ] Deleted diagnostic-custom-labels.php file

---

**Need the auto-update script? Let me know and I'll create it for you!**
