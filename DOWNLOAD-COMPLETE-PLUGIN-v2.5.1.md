# 📦 Download Complete Plugin v2.5.1 - Custom Labels (Instant Update)

## ✅ All Files Updated on GitHub!

The entire plugin on GitHub now includes the **v2.5.1 instant label fix**. You can download and upload the complete plugin.

---

## 🚀 Method 1: Download Entire Plugin (RECOMMENDED)

### Step 1: Download ZIP from GitHub

1. **Go to:** https://github.com/namirkhan265-star/jewellery-price-calculator
2. **Click** the green **"Code"** button (top right)
3. **Click** "Download ZIP"
4. **Save** the file to your computer

---

### Step 2: Extract the ZIP

1. **Extract** the downloaded ZIP file
2. You'll get a folder named: `jewellery-price-calculator-main`
3. **Rename** it to: `jewellery-price-calculator` (remove `-main`)

---

### Step 3: Backup Current Plugin

**IMPORTANT:** Backup your current plugin first!

Via FTP:
1. Connect to your server
2. Navigate to: `/wp-content/plugins/`
3. **Rename** `jewellery-price-calculator` to `jewellery-price-calculator-backup-OLD`

This preserves your old version in case you need it.

---

### Step 4: Upload New Plugin

1. **Upload** the `jewellery-price-calculator` folder to `/wp-content/plugins/`
2. **Overwrite** if prompted (or delete old one first)

---

### Step 5: Activate Plugin

1. Go to **WordPress Admin > Plugins**
2. Find **Jewellery Price Calculator**
3. If deactivated, click **Activate**

**Your settings are safe!** They're stored in the database, not in plugin files.

---

### Step 6: Verify Settings

1. Go to **Jewellery Price > General Settings**
2. Scroll to **"Additional Cost Fields"**
3. Verify your labels are still there:
   - Pearl Cost Label: **Test 6**
   - Stone Cost Label: **Test 7**
   - Extra Fee Label: **Test 8**

If they're missing, just re-enter them and click **Save Changes**.

---

### Step 7: Check Frontend (NO REGENERATION NEEDED!)

1. **Clear browser cache** (Ctrl+Shift+R or Cmd+Shift+R)
2. **Open any product** on your website
3. **Check price breakup** section
4. You should see:
   - **"Test 6"** instead of "Pearl Cost"
   - **"Test 7"** instead of "Stone Cost"
   - **"Test 8"** instead of "Extra Fee"

✅ **Labels appear instantly - no regeneration needed!**

---

### Step 8: Clean Up

1. **Delete backup** folder if everything works: `jewellery-price-calculator-backup-OLD`
2. **Delete diagnostic files** if you uploaded any:
   - `diagnostic-custom-labels.php`
   - `auto-update-v2.5.0.php`

---

## 🎨 Change Labels Anytime

Now you can change labels instantly:

1. Go to **Jewellery Price > General**
2. Change labels to anything:
   - "Test 6" → "Gemstone Cost"
   - "Test 7" → "Packaging Fee"
   - "Test 8" → "Certification Fee"
3. Click **Save Changes**
4. **Refresh frontend** (Ctrl+Shift+R)
5. New labels appear **immediately**!

**No regeneration needed anymore!** 🎉

---

## 📋 What's Included in v2.5.1

### Key Files Updated:

1. **templates/frontend/price-breakup.php**
   - Now fetches labels directly from settings
   - Instant updates, no regeneration needed

2. **templates/frontend/detailed-breakup.php**
   - Now fetches labels directly from settings
   - Instant updates, no regeneration needed

3. **includes/class-jpc-price-calculator.php**
   - Stores custom labels in breakup data (for backwards compatibility)

4. **includes/class-jpc-admin.php**
   - Settings page with custom label fields

5. **templates/admin/general-settings.php**
   - Admin interface for custom labels

### All Other Files:
- Complete plugin structure
- All features intact
- All settings preserved

---

## 🔧 Alternative: Update Only 2 Files

If you don't want to upload the entire plugin, you can update just 2 files:

1. **Download:**
   - [price-breakup.php](https://raw.githubusercontent.com/namirkhan265-star/jewellery-price-calculator/main/templates/frontend/price-breakup.php)
   - [detailed-breakup.php](https://raw.githubusercontent.com/namirkhan265-star/jewellery-price-calculator/main/templates/frontend/detailed-breakup.php)

2. **Upload to:**
   - `/wp-content/plugins/jewellery-price-calculator/templates/frontend/`

3. **Overwrite** existing files

4. **Clear cache** and check frontend

---

## ✅ Success Checklist

- [ ] Downloaded plugin ZIP from GitHub
- [ ] Extracted and renamed folder
- [ ] Backed up current plugin folder
- [ ] Uploaded new plugin folder via FTP
- [ ] Activated plugin in WordPress
- [ ] Verified settings are still there
- [ ] Checked frontend - custom labels visible
- [ ] Cleared browser cache
- [ ] Tested changing labels - updates instantly
- [ ] Deleted backup folder (if everything works)
- [ ] Deleted diagnostic files

---

## 🎉 What's New in v2.5.1

### Before (v2.5.0):
- ❌ Labels stored in breakup data
- ❌ Required regeneration for every change
- ❌ Slow to update
- ❌ Had to click "Update All Prices"

### After (v2.5.1):
- ✅ Labels fetched from settings
- ✅ No regeneration needed
- ✅ Instant updates
- ✅ Change anytime, see results immediately

---

## 🆘 Troubleshooting

### Issue: Plugin deactivated after upload

**Solution:**
1. Go to WordPress Admin > Plugins
2. Click "Activate" on Jewellery Price Calculator
3. Check settings are still there

### Issue: Settings are gone

**Solution:**
1. Settings are in database, not files
2. If missing, re-enter them:
   - Go to Jewellery Price > General
   - Enter custom labels
   - Click Save Changes

### Issue: Labels still show default names

**Solution:**
1. Clear browser cache (Ctrl+Shift+R)
2. Check settings are saved
3. Verify you uploaded the correct files
4. Check file paths are correct

### Issue: "Update All Prices" button missing

**Solution:**
- You don't need it anymore!
- Labels update instantly from settings
- No regeneration required

---

## 📝 File Structure

Your plugin should have this structure:

```
wp-content/plugins/jewellery-price-calculator/
├── includes/
│   ├── class-jpc-price-calculator.php
│   ├── class-jpc-admin.php
│   ├── class-jpc-frontend.php
│   └── ... (other files)
├── templates/
│   ├── admin/
│   │   ├── general-settings.php
│   │   └── ... (other files)
│   └── frontend/
│       ├── price-breakup.php          ← UPDATED v2.5.1
│       ├── detailed-breakup.php       ← UPDATED v2.5.1
│       └── ... (other files)
├── assets/
│   └── ... (CSS, JS files)
└── jewellery-price-calculator.php
```

---

## 🎯 Quick Start After Installation

1. **Activate plugin** (if needed)
2. **Go to** Jewellery Price > General
3. **Set custom labels:**
   - Pearl Cost Label: "Gemstone Cost"
   - Stone Cost Label: "Packaging Fee"
   - Extra Fee Label: "Certification Fee"
4. **Click** Save Changes
5. **View product** on frontend
6. **See labels** appear instantly!

---

## 📞 Support

If you encounter any issues:

1. Check this guide first
2. Verify file paths are correct
3. Clear all caches (browser, WordPress, server)
4. Check WordPress error logs
5. Try deactivating/reactivating plugin

---

## 🎉 Enjoy Instant Custom Labels!

**Version:** v2.5.1  
**Release Date:** February 9, 2026  
**Feature:** Custom Labels with Instant Updates  
**Compatibility:** WordPress 5.0+, WooCommerce 3.0+

---

**Download now and enjoy instant label updates! No more regeneration needed!** 🚀
