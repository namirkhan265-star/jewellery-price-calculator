# 🚨 URGENT: Update Instructions to See 4Cs on Diamonds Page

## The Issue
The Diamonds (Legacy) page is not showing the new 4Cs fields because you need to **re-upload the updated files** to your WordPress site.

## ✅ SOLUTION: 3 Simple Steps

### Step 1: Download Updated Plugin from GitHub
1. Go to: https://github.com/namirkhan265-star/jewellery-price-calculator
2. Click the green **"Code"** button
3. Click **"Download ZIP"**
4. Save the ZIP file to your computer

### Step 2: Deactivate Current Plugin
1. Go to your WordPress admin: **Plugins** → **Installed Plugins**
2. Find **"Jewellery Price Calculator"**
3. Click **"Deactivate"**
4. **DO NOT DELETE** - Just deactivate

### Step 3: Upload & Activate New Version
1. Go to **Plugins** → **Add New**
2. Click **"Upload Plugin"** at the top
3. Click **"Choose File"** and select the ZIP you downloaded
4. Click **"Install Now"**
5. Click **"Activate Plugin"**

## ✅ Verification

After activation, go to **Jewellery Price** → **Diamonds (Legacy)**

You should now see:

**In the Add Diamond Form:**
- ✅ Basic Information section
- ✅ **Diamond 4Cs Quality Attributes** section with:
  - Shape dropdown
  - Colour dropdown
  - Clarity dropdown
  - Cut dropdown
- ✅ Certification & Pricing section

**In the Diamonds List:**
- ✅ New columns: Shape, Colour, Clarity, Cut

## 🔧 Alternative Method (FTP/File Manager)

If you prefer to update specific files only:

### Files to Upload:

1. **Main File:**
   - `includes/class-jpc-admin.php` (UPDATED - line 480 now uses diamonds-v2.php)

2. **New Template:**
   - `templates/admin/diamonds-v2.php` (NEW - contains 4Cs fields)

### Upload via FTP:
1. Connect to your site via FTP
2. Navigate to: `/wp-content/plugins/jewellery-price-calculator/`
3. Upload the 2 files above to their respective folders
4. Overwrite existing files when prompted

### Upload via cPanel File Manager:
1. Login to cPanel
2. Go to **File Manager**
3. Navigate to: `public_html/wp-content/plugins/jewellery-price-calculator/`
4. Upload the 2 files to their respective folders
5. Overwrite when prompted

## 📋 What Changed

**File:** `includes/class-jpc-admin.php`
- **Line 480 (OLD):** `include JPC_PLUGIN_DIR . 'templates/admin/diamonds.php';`
- **Line 480 (NEW):** `include JPC_PLUGIN_DIR . 'templates/admin/diamonds-v2.php';`

**New File:** `templates/admin/diamonds-v2.php`
- Complete new template with 4Cs fields
- Shape, Colour, Clarity, Cut dropdowns
- Enhanced form layout
- New table columns

## ⚠️ Important Notes

1. **Your data is safe** - This update only changes the display template
2. **No database changes** - The 4Cs tables were already created in v1.9.0
3. **Existing diamonds** - Will show "-" for 4Cs until you edit them
4. **New diamonds** - Can have all 4Cs attributes filled

## 🎯 Expected Result

After updating, when you add a new diamond, you'll see:

```
Add New Diamond Form:

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Basic Information
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Display Name: [                    ]
Diamond Group: [Select Group ▼]
Carat Weight: [                    ]

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Diamond 4Cs Quality Attributes
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Shape: [Select Shape ▼]
Colour Grade: [Select Colour ▼]
Clarity Grade: [Select Clarity ▼]
Cut Quality: [Select Cut ▼]

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Certification & Pricing
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Certification: [Select Cert ▼]
Price per Carat: [                    ]

[Add Diamond]
```

## 🆘 Still Not Working?

If you still don't see the 4Cs fields after updating:

1. **Clear WordPress cache** (if using a caching plugin)
2. **Clear browser cache** (Ctrl+Shift+Delete)
3. **Check file permissions** - Should be 644 for files, 755 for folders
4. **Verify file upload** - Make sure `diamonds-v2.php` exists in `templates/admin/`
5. **Check for errors** - Go to **Jewellery Price** → **Debug** and look for errors

## 📞 Need Help?

Check these files exist on your server:
- ✅ `includes/class-jpc-admin.php` (updated)
- ✅ `templates/admin/diamonds-v2.php` (new)
- ✅ `includes/class-jpc-diamond-shapes.php`
- ✅ `includes/class-jpc-diamond-colours.php`
- ✅ `includes/class-jpc-diamond-clarities.php`
- ✅ `includes/class-jpc-diamond-cuts.php`

---

**Last Updated:** January 2026  
**Version Required:** 1.9.0  
**Status:** ✅ Files ready on GitHub
