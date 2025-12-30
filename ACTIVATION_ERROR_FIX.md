# 🚨 PLUGIN ACTIVATION ERROR - SOLUTION

## ❌ **COMMON PROBLEM:**

When you download from GitHub using "Download ZIP", it creates this structure:
```
jewellery-price-calculator-main/
└── jewellery-price-calculator/
    ├── jewellery-price-calculator.php
    ├── includes/
    ├── admin/
    └── assets/
```

**WordPress expects:**
```
jewellery-price-calculator/
├── jewellery-price-calculator.php
├── includes/
├── admin/
└── assets/
```

---

## ✅ **SOLUTION 1: Fix the Folder Structure**

### **Step 1: Extract the ZIP**
1. Download the ZIP from GitHub
2. Extract it on your computer
3. You'll see a folder named `jewellery-price-calculator-main`

### **Step 2: Rename the Folder**
1. Open the `jewellery-price-calculator-main` folder
2. You should see the plugin files inside
3. **Rename** `jewellery-price-calculator-main` to `jewellery-price-calculator`

### **Step 3: Upload to WordPress**
1. Compress the renamed folder as ZIP
2. Go to **WordPress Admin → Plugins → Add New → Upload Plugin**
3. Upload the new ZIP file
4. Click **Activate**

---

## ✅ **SOLUTION 2: Manual FTP Upload (Recommended)**

### **Step 1: Download from GitHub**
1. Go to: https://github.com/namirkhan265-star/jewellery-price-calculator
2. Click **"Code"** → **"Download ZIP"**
3. Extract the ZIP on your computer

### **Step 2: Rename the Folder**
1. Find the extracted folder (probably named `jewellery-price-calculator-main`)
2. Rename it to: `jewellery-price-calculator`

### **Step 3: Upload via FTP**
1. Connect to your server via FTP (FileZilla, cPanel File Manager, etc.)
2. Navigate to: `/wp-content/plugins/`
3. **Delete the old** `jewellery-price-calculator` folder (if exists)
4. **Upload the new** `jewellery-price-calculator` folder
5. Go to **WordPress Admin → Plugins**
6. Click **Activate** on "Jewellery Price Calculator"

---

## ✅ **SOLUTION 3: Direct Download Link**

I'll create a properly structured release for you. But for now, use this method:

### **Step 1: Delete Old Plugin**
1. Go to **WordPress Admin → Plugins**
2. Find "Jewellery Price Calculator"
3. Click **Deactivate** (if active)
4. Click **Delete**

### **Step 2: Download Correct Structure**
1. Download from GitHub
2. Extract the ZIP
3. Look inside the extracted folder
4. You should see these files/folders:
   ```
   ✅ jewellery-price-calculator.php
   ✅ includes/
   ✅ admin/
   ✅ assets/
   ✅ templates/
   ✅ README.md
   ```

### **Step 3: Create Proper ZIP**
1. Select ALL the files and folders (not the parent folder)
2. Right-click → **"Compress"** or **"Add to archive"**
3. Name it: `jewellery-price-calculator.zip`

### **Step 4: Upload to WordPress**
1. Go to **WordPress Admin → Plugins → Add New → Upload Plugin**
2. Upload the new ZIP
3. Click **Install Now**
4. Click **Activate**

---

## 🔍 **VERIFY CORRECT STRUCTURE:**

After uploading, check via FTP that the structure is:
```
/wp-content/plugins/jewellery-price-calculator/
├── jewellery-price-calculator.php  ← Main plugin file
├── includes/
│   ├── class-jpc-database.php
│   ├── class-jpc-metal.php
│   ├── class-jpc-diamond.php
│   └── ... (other files)
├── admin/
│   ├── class-jpc-admin.php
│   └── ... (other files)
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── frontend.css
│   └── js/
│       ├── admin.js
│       └── live-calculator.js
└── templates/
```

**NOT like this (WRONG):**
```
/wp-content/plugins/jewellery-price-calculator-main/
└── jewellery-price-calculator/  ← Extra folder level!
    ├── jewellery-price-calculator.php
    └── ...
```

---

## 🐛 **DEBUGGING ACTIVATION ERRORS:**

### **Error 1: "Plugin could not be activated because it triggered a fatal error"**

**Cause:** Missing files or PHP version too old

**Solution:**
1. Check PHP version: Must be **7.4 or higher**
   - Go to **WordPress Admin → Tools → Site Health → Info → Server**
   - Look for "PHP version"
2. Check all files are uploaded correctly
3. Enable WordPress debug mode to see the exact error:
   ```php
   // Add to wp-config.php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   define('WP_DEBUG_DISPLAY', false);
   ```
4. Try to activate again
5. Check `/wp-content/debug.log` for the error message

### **Error 2: "The plugin does not have a valid header"**

**Cause:** Wrong folder structure or corrupted main file

**Solution:**
1. Check that `jewellery-price-calculator.php` is in the root of the plugin folder
2. Open the file and verify the header exists:
   ```php
   /**
    * Plugin Name: Jewellery Price Calculator
    * Version: 1.5.0
    * ...
    */
   ```
3. Make sure there's no extra text before `<?php`

### **Error 3: White screen or nothing happens**

**Cause:** PHP fatal error

**Solution:**
1. Enable debug mode (see above)
2. Check error log
3. Common issues:
   - Missing required files
   - PHP syntax error
   - Memory limit too low

---

## 📋 **CHECKLIST BEFORE ACTIVATION:**

- [ ] PHP version is 7.4 or higher
- [ ] WordPress version is 5.8 or higher
- [ ] WooCommerce is installed and active
- [ ] Plugin folder is named `jewellery-price-calculator` (not `jewellery-price-calculator-main`)
- [ ] Main file `jewellery-price-calculator.php` is in the plugin root folder
- [ ] All subfolders exist: `includes/`, `admin/`, `assets/`, `templates/`
- [ ] File permissions are correct (755 for folders, 644 for files)

---

## 🆘 **STILL CAN'T ACTIVATE?**

### **Send me these details:**

1. **Exact error message** (screenshot or copy-paste)
2. **PHP version** (from Site Health)
3. **WordPress version**
4. **WooCommerce version**
5. **Folder structure** (screenshot of FTP showing the plugin folder)
6. **Debug log** (if you enabled debug mode)

### **Quick Test:**

Try this to see if it's a file issue:
1. Via FTP, go to `/wp-content/plugins/jewellery-price-calculator/`
2. Open `jewellery-price-calculator.php`
3. Check if the file opens correctly and shows PHP code
4. Check if `includes/class-jpc-database.php` exists
5. Check if `admin/class-jpc-admin.php` exists

If any files are missing, the upload was incomplete.

---

## 💡 **RECOMMENDED METHOD:**

**Use FTP for a clean installation:**

1. **Delete old plugin** via WordPress admin
2. **Download from GitHub** and extract
3. **Rename folder** to `jewellery-price-calculator`
4. **Upload via FTP** to `/wp-content/plugins/`
5. **Set permissions**: 755 for folders, 644 for files
6. **Activate** via WordPress admin

This method avoids ZIP compression issues and ensures correct structure.

---

## ✅ **AFTER SUCCESSFUL ACTIVATION:**

1. Go to **WordPress Admin → Plugins**
2. Verify version shows: **1.5.0**
3. Go to **Jewellery Price Calc** menu
4. Check if settings page loads
5. Edit a product and check if meta box appears

**If all these work, activation is successful!** 🎉
