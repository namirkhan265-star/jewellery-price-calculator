# 🎯 EASIEST Way to Update Plugin for Custom Labels

## Your Current Situation:
- ✅ Custom labels are saved in settings (Test 6, Test 7, Test 8)
- ❌ Plugin files are outdated
- ❌ Products show default labels instead of custom ones

## 🚀 EASIEST Solution: Use Auto-Update Script

### Step 1: Download the Auto-Update Script

1. **Download this file:**
   - [auto-update-v2.5.0.php](https://raw.githubusercontent.com/namirkhan265-star/jewellery-price-calculator/main/auto-update-v2.5.0.php)
   - Right-click the link above > Save As > `auto-update-v2.5.0.php`

### Step 2: Upload to WordPress Root

1. **Connect to your server** (via FTP or cPanel File Manager)
2. **Navigate to WordPress root directory** (where wp-config.php is located)
3. **Upload** `auto-update-v2.5.0.php`

### Step 3: Run the Script

1. **Open in browser:** `https://yoursite.com/auto-update-v2.5.0.php`
2. **Review** the files that will be updated
3. **Click** "Update Now" button
4. **Wait** for completion (usually takes 10-30 seconds)

### Step 4: Regenerate Prices

1. **Go to:** Jewellery Price > General
2. **Scroll to bottom**
3. **Click:** "Update All Prices" button
4. **Wait** for completion message

### Step 5: Check Frontend

1. **View any product** on your website
2. **Check price breakup** section
3. **You should see:**
   - "Test 6" instead of "Pearl Cost"
   - "Test 7" instead of "Stone Cost"
   - "Test 8" instead of "Extra Fee"

### Step 6: Clean Up

1. **Delete** `auto-update-v2.5.0.php` from your server (for security)
2. **Delete** `diagnostic-custom-labels.php` if you uploaded it

---

## ✅ That's It!

The auto-update script will:
- ✅ Download latest files from GitHub automatically
- ✅ Backup your current files (with timestamp)
- ✅ Replace only the necessary files
- ✅ Show you exactly what was updated

---

## 🆘 If Auto-Update Doesn't Work

### Alternative: Manual Download

1. **Download entire plugin as ZIP:**
   - Go to: https://github.com/namirkhan265-star/jewellery-price-calculator
   - Click green "Code" button
   - Click "Download ZIP"

2. **Extract ZIP** on your computer

3. **Upload via FTP/cPanel:**
   - Backup current plugin folder
   - Upload new plugin folder
   - Rename to `jewellery-price-calculator`

4. **Regenerate prices** (same as Step 4 above)

---

## 📋 Quick Checklist

- [ ] Downloaded auto-update-v2.5.0.php
- [ ] Uploaded to WordPress root
- [ ] Ran the script (yoursite.com/auto-update-v2.5.0.php)
- [ ] Clicked "Update Now"
- [ ] Went to Jewellery Price > General
- [ ] Clicked "Update All Prices"
- [ ] Checked frontend - custom labels visible
- [ ] Deleted auto-update-v2.5.0.php
- [ ] Deleted diagnostic-custom-labels.php

---

## 🎉 Success!

Once you see your custom labels (Test 6, Test 7, Test 8) on the frontend, you're done!

You can now change these labels to anything you want:
1. Go to Jewellery Price > General
2. Change "Test 6" to "Gemstone Cost" (or whatever you want)
3. Change "Test 7" to "Packaging Fee" (or whatever you want)
4. Change "Test 8" to "Certification" (or whatever you want)
5. Save Changes
6. Click "Update All Prices" again
7. Check frontend - new labels should appear!

---

**Need help? The auto-update script shows detailed error messages if something goes wrong.**
