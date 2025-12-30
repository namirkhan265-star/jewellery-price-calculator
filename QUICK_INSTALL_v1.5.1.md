# 🚀 QUICK INSTALLATION GUIDE - v1.5.1

## ✅ **THE PLUGIN IS FIXED ON GITHUB**

All files are now properly restored in the GitHub repository. You just need to download and install them.

---

## 📥 **STEP-BY-STEP INSTALLATION:**

### **STEP 1: Download from GitHub**

1. Click this link: **https://github.com/namirkhan265-star/jewellery-price-calculator**
2. Click the green **"Code"** button
3. Click **"Download ZIP"**
4. Save the ZIP file to your computer

---

### **STEP 2: Extract and Prepare**

1. **Extract the ZIP file** on your computer
2. You'll see a folder named: `jewellery-price-calculator-main`
3. **RENAME IT TO:** `jewellery-price-calculator` (remove the `-main` part)
4. **Open the folder** and verify you see:
   ```
   ✅ jewellery-price-calculator.php
   ✅ admin/ (folder with 4 files inside)
   ✅ includes/ (folder)
   ✅ assets/ (folder)
   ✅ templates/ (folder)
   ```

---

### **STEP 3: Delete Old Plugin**

**Via WordPress Admin:**
1. Go to **WordPress Admin → Plugins**
2. Find **"Jewellery Price Calculator"**
3. Click **"Deactivate"**
4. Click **"Delete"**

**OR Via FTP:**
1. Connect to your server via FTP
2. Go to `/wp-content/plugins/`
3. Delete the `jewellery-price-calculator` folder

---

### **STEP 4: Upload New Plugin**

**METHOD A: Via WordPress Admin (Easier)**
1. Compress the `jewellery-price-calculator` folder as a ZIP
2. Go to **WordPress Admin → Plugins → Add New**
3. Click **"Upload Plugin"**
4. Choose your ZIP file
5. Click **"Install Now"**
6. Click **"Activate"**

**METHOD B: Via FTP (More Reliable)**
1. Connect to your server via FTP
2. Go to `/wp-content/plugins/`
3. Upload the `jewellery-price-calculator` folder
4. Go to **WordPress Admin → Plugins**
5. Click **"Activate"** on Jewellery Price Calculator

---

### **STEP 5: Verify Installation**

After activation, check:

1. **✅ Version number:** Should show **1.5.1** in plugins list
2. **✅ Menu appears:** "Jewellery Price Calc" in WordPress sidebar
3. **✅ Submenus visible:**
   - Settings
   - Metal Rates
   - Diamond Rates
   - Diamond Groups
4. **✅ Product meta box:** Edit any product and scroll down to see calculator

---

## 🔍 **VERIFY FILES VIA FTP:**

Connect via FTP and check:

```
/wp-content/plugins/jewellery-price-calculator/
├── jewellery-price-calculator.php  ← Main file
├── admin/                          ← THIS FOLDER MUST EXIST!
│   ├── class-jpc-admin.php
│   ├── class-jpc-metal-admin.php
│   ├── class-jpc-diamond-admin.php
│   └── class-jpc-diamond-group-admin.php
├── includes/
├── assets/
└── templates/
```

**If `admin/` folder is missing, you didn't download the latest version!**

---

## ❌ **COMMON MISTAKES:**

### **Mistake 1: Not Renaming Folder**
- ❌ Folder named: `jewellery-price-calculator-main`
- ✅ Should be: `jewellery-price-calculator`

### **Mistake 2: Extra Folder Level**
- ❌ Structure: `jewellery-price-calculator/jewellery-price-calculator/jewellery-price-calculator.php`
- ✅ Should be: `jewellery-price-calculator/jewellery-price-calculator.php`

### **Mistake 3: Old Version Still Cached**
- Solution: Clear WordPress cache + browser cache
- Deactivate and reactivate plugin

---

## 🎯 **WHAT'S FIXED IN v1.5.1:**

- ✅ Restored missing `admin/` folder
- ✅ Restored all 4 admin class files
- ✅ All menu items working
- ✅ Metal rates management working
- ✅ Diamond rates management working
- ✅ Product calculator working
- ✅ Frontend display working

---

## 🆘 **STILL HAVING ISSUES?**

### **Check 1: Verify Download**
- Go to GitHub and check the latest commit date
- Should be recent (today)
- Download again if needed

### **Check 2: Verify Upload**
- Use FTP to check if `admin/` folder exists
- Check if all 4 files are inside admin folder

### **Check 3: Clear Everything**
- Clear WordPress cache
- Clear browser cache (Ctrl+Shift+R)
- Deactivate and reactivate plugin

### **Check 4: Enable Debug Mode**
Add to `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```
Check `/wp-content/debug.log` for errors.

---

## ✅ **SUCCESS INDICATORS:**

You'll know it's working when:
1. ✅ Plugin activates without errors
2. ✅ "Jewellery Price Calc" menu appears
3. ✅ All 4 submenus are visible
4. ✅ Settings page loads
5. ✅ Metal Rates page loads
6. ✅ Diamond Rates page loads
7. ✅ Product calculator appears in product edit page

---

## 📞 **AFTER INSTALLATION:**

Please confirm:
- [ ] Downloaded from GitHub
- [ ] Renamed folder (removed `-main`)
- [ ] Uploaded to `/wp-content/plugins/`
- [ ] Activated successfully
- [ ] Version shows 1.5.1
- [ ] Menu appears in sidebar
- [ ] All tabs are visible

**If all checked, installation is successful!** 🎉

---

## 💡 **IMPORTANT NOTE:**

**The GitHub repository IS updated and working.** The issue is that your WordPress site needs the new files. Simply downloading and uploading will fix everything!

**Download link:** https://github.com/namirkhan265-star/jewellery-price-calculator
