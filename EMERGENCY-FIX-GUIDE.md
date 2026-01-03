# 🚨 EMERGENCY FIX GUIDE

## ⚠️ **CRITICAL ERROR - IMMEDIATE ACTION REQUIRED**

Your plugin is showing a critical error. Follow these steps **IN ORDER**:

---

## 🔧 **STEP 1: Run Diagnostic Script (2 minutes)**

### **1.1 Upload Diagnostic File**
```bash
cd /path/to/your/wordpress/root
git pull origin main
cp jewellery-price-calculator/jpc-emergency-diagnostic.php ./
```

### **1.2 Access Diagnostic Script**
Open in browser:
```
https://yoursite.com/jpc-emergency-diagnostic.php
```

### **1.3 Read the Results**
The script will show you:
- ✓ Which files are missing
- ✓ Which files have syntax errors
- ✓ Which classes failed to load
- ✓ The EXACT error message and line number

### **1.4 Take Screenshot**
Take a screenshot of the diagnostic results and share it if you need help.

---

## 🔄 **STEP 2: Fresh Install (5 minutes)**

If diagnostic shows errors, do a fresh install:

### **2.1 Backup Current Plugin**
```bash
cd /path/to/wp-content/plugins
mv jewellery-price-calculator jewellery-price-calculator-backup
```

### **2.2 Fresh Clone**
```bash
git clone https://github.com/namirkhan265-star/jewellery-price-calculator.git
```

### **2.3 Set Permissions**
```bash
chmod -R 755 jewellery-price-calculator
chmod -R 644 jewellery-price-calculator/*.php
chmod -R 644 jewellery-price-calculator/includes/*.php
```

### **2.4 Try Activating**
Go to WordPress Admin → Plugins → Activate "Jewellery Price Calculator"

---

## 🗄️ **STEP 3: Database Check (If Still Failing)**

### **3.1 Check if Tables Exist**
Run in phpMyAdmin or MySQL:
```sql
SHOW TABLES LIKE 'wp_jpc_%';
```

Should show:
- `wp_jpc_metal_groups`
- `wp_jpc_metals`
- `wp_jpc_diamonds`

### **3.2 If Tables Missing, Create Them**
```sql
-- Run the activation function manually
-- Access: https://yoursite.com/wp-admin/plugins.php?action=activate&plugin=jewellery-price-calculator/jewellery-price-calculator.php
```

---

## 🔍 **STEP 4: Check PHP Error Log**

### **4.1 Enable WordPress Debug**
Add to `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### **4.2 Check Error Log**
```bash
tail -f /path/to/wp-content/debug.log
```

### **4.3 Look for:**
- Fatal errors
- Class not found errors
- Syntax errors
- Missing file errors

---

## 🎯 **COMMON ISSUES & FIXES**

### **Issue 1: "Class not found"**
**Fix:**
```bash
cd /path/to/wp-content/plugins/jewellery-price-calculator
git pull origin main
```

### **Issue 2: "Parse error" or "Syntax error"**
**Fix:** File got corrupted during upload
```bash
rm -rf jewellery-price-calculator
git clone https://github.com/namirkhan265-star/jewellery-price-calculator.git
```

### **Issue 3: "Cannot redeclare class"**
**Fix:** Plugin loaded twice
```bash
# Check if plugin exists in multiple locations
find /path/to/wp-content/plugins -name "jewellery-price-calculator" -type d
# Remove duplicates
```

### **Issue 4: "WooCommerce not found"**
**Fix:** Activate WooCommerce first
```
WordPress Admin → Plugins → Activate WooCommerce
```

### **Issue 5: "Database table doesn't exist"**
**Fix:** Manually create tables
```sql
-- Access phpMyAdmin and run:
-- Copy SQL from includes/class-jpc-database.php create_tables() function
```

---

## 🚀 **STEP 5: Nuclear Option (If Nothing Works)**

### **5.1 Complete Clean Install**

```bash
# 1. Deactivate plugin via database
mysql -u username -p database_name
```

```sql
UPDATE wp_options 
SET option_value = REPLACE(option_value, 'jewellery-price-calculator/jewellery-price-calculator.php', '') 
WHERE option_name = 'active_plugins';
```

```bash
# 2. Remove plugin completely
cd /path/to/wp-content/plugins
rm -rf jewellery-price-calculator

# 3. Clear all caches
rm -rf /tmp/wordpress-cache/*
wp cache flush

# 4. Fresh clone
git clone https://github.com/namirkhan265-star/jewellery-price-calculator.git

# 5. Set permissions
chmod -R 755 jewellery-price-calculator
find jewellery-price-calculator -type f -exec chmod 644 {} \;

# 6. Activate
# Go to WordPress Admin → Plugins → Activate
```

---

## 📊 **VERIFICATION CHECKLIST**

After fixing, verify:

- [ ] Plugin shows as "Active" in plugins list
- [ ] No error messages on activation
- [ ] Can access Products → Add New
- [ ] Price calculator meta box visible
- [ ] Can save product without errors
- [ ] Frontend shows product correctly
- [ ] No PHP errors in debug.log

---

## 🆘 **IF STILL NOT WORKING**

### **Send Me This Information:**

1. **Diagnostic Script Results**
   - Screenshot of `jpc-emergency-diagnostic.php` output

2. **PHP Error Log**
   ```bash
   tail -100 /path/to/wp-content/debug.log
   ```

3. **Server Info**
   - PHP Version: `php -v`
   - WordPress Version
   - WooCommerce Version

4. **File Permissions**
   ```bash
   ls -la /path/to/wp-content/plugins/jewellery-price-calculator/
   ls -la /path/to/wp-content/plugins/jewellery-price-calculator/includes/
   ```

---

## 🔐 **SECURITY NOTE**

**IMPORTANT:** After diagnosis, delete the diagnostic script:
```bash
rm /path/to/wordpress/root/jpc-emergency-diagnostic.php
```

---

## 📞 **QUICK SUPPORT COMMANDS**

### **Check Plugin Status**
```bash
wp plugin list | grep jewellery
```

### **Check for Syntax Errors**
```bash
php -l /path/to/wp-content/plugins/jewellery-price-calculator/jewellery-price-calculator.php
```

### **Check File Integrity**
```bash
cd /path/to/wp-content/plugins/jewellery-price-calculator
git status
git diff
```

### **Reset to Known Good Version**
```bash
cd /path/to/wp-content/plugins/jewellery-price-calculator
git fetch origin
git reset --hard e7eca70  # Last known working commit
```

---

## ✅ **SUCCESS INDICATORS**

You'll know it's working when:

1. ✅ Plugin activates without error
2. ✅ Product editor loads normally
3. ✅ Price calculator meta box visible
4. ✅ Can save products
5. ✅ Prices calculate correctly
6. ✅ Frontend displays prices
7. ✅ No errors in debug.log

---

**Version:** Emergency Fix Guide v1.0  
**Last Updated:** January 4, 2026  
**Status:** 🚨 CRITICAL ERROR RESOLUTION
