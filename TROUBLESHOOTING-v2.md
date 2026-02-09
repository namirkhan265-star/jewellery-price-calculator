# 🔧 TROUBLESHOOTING: Making Charges Field Not Showing

## 🎯 PROBLEM
You don't see the "Making Charges per Gram" field on the Metals page.

---

## ✅ SOLUTION (Step-by-Step)

### STEP 1: Verify File Upload

Check if this file exists on your server:
```
wp-content/plugins/jewellery-price-calculator/templates/admin/metals-v2.php
```

**How to check:**
- Via FTP: Navigate to the path above
- Via cPanel File Manager: Navigate to the path above
- Via SSH: `ls -la wp-content/plugins/jewellery-price-calculator/templates/admin/metals-v2.php`

**If file is missing:**
- Download `metals-v2.php` from GitHub
- Upload to `templates/admin/` folder

---

### STEP 2: Update Admin Class

**File to edit:** `includes/class-jpc-admin.php`

**Find this (around line 424-426):**
```php
public function render_metals() {
    include JPC_PLUGIN_DIR . 'templates/admin/metals.php';
}
```

**Change to:**
```php
public function render_metals() {
    include JPC_PLUGIN_DIR . 'templates/admin/metals-v2.php';
}
```

**Just change:** `metals.php` → `metals-v2.php`

---

### STEP 3: Clear Cache

1. **WordPress Cache:**
   - If using caching plugin (WP Super Cache, W3 Total Cache, etc.), clear it
   - Or deactivate caching plugin temporarily

2. **Browser Cache:**
   - Press `Ctrl + Shift + R` (Windows) or `Cmd + Shift + R` (Mac)
   - Or open in Incognito/Private window

3. **Server Cache:**
   - If using server-level caching (Varnish, Redis, etc.), clear it

---

### STEP 4: Verify Database

The database should have the `making_charges_per_gram` column.

**Check via phpMyAdmin:**
1. Open phpMyAdmin
2. Select your WordPress database
3. Find table: `wp_jpc_metals` (prefix may vary)
4. Click "Structure"
5. Look for column: `making_charges_per_gram`

**If column is missing:**
Run this SQL query:
```sql
ALTER TABLE `wp_jpc_metals` 
ADD COLUMN `making_charges_per_gram` DECIMAL(10,2) DEFAULT 0 
AFTER `price_per_unit`;
```

**Or deactivate and reactivate the plugin** (this runs migration automatically)

---

### STEP 5: Check for Errors

**Enable WordPress Debug:**

Edit `wp-config.php` and add:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

**Check error log:**
- Location: `wp-content/debug.log`
- Look for errors related to JPC or metals

**Common errors:**
- "Template not found" → File not uploaded
- "Column not found" → Database not migrated
- "Class not found" → Wrong file included

---

## 🔍 VERIFICATION CHECKLIST

After following steps above, verify:

### Files Exist:
```bash
✅ templates/admin/metals-v2.php
✅ includes/class-jpc-database-v2.php
✅ includes/class-jpc-metals.php (updated version)
```

### Admin Class Updated:
```bash
✅ includes/class-jpc-admin.php line ~425 uses metals-v2.php
```

### Database Column Exists:
```bash
✅ wp_jpc_metals table has making_charges_per_gram column
```

### Main Plugin File:
```bash
✅ jewellery-price-calculator.php version is 2.0.0
✅ Includes class-jpc-database-v2.php
```

---

## 🎯 WHAT YOU SHOULD SEE

### On Metals Page (JPC → Metals):

**Add New Metal Form:**
- Field: "Making Charges per Gram (₹)" with ★ NEW badge
- Blue info box explaining the feature
- Example: "If you enter ₹50 and product has 10 grams..."

**Metals List Table:**
- Column: "Making Charges/Gram" with ★ icon
- Shows: ₹0.00 (if not set) or ₹50.00 (if set)
- Shows: "Auto-calc enabled" or "Not set"

**Edit Metal Modal:**
- Field: "Making Charges per Gram (₹)"
- Pre-filled with current value

---

## 🐛 STILL NOT WORKING?

### Quick Diagnostic:

**1. Check which template is loading:**

Add this temporarily to `includes/class-jpc-admin.php` in the `render_metals()` function:

```php
public function render_metals() {
    echo '<div style="background: yellow; padding: 10px;">Loading template: ' . JPC_PLUGIN_DIR . 'templates/admin/metals-v2.php</div>';
    include JPC_PLUGIN_DIR . 'templates/admin/metals-v2.php';
}
```

If you see the yellow box, the function is being called. If not, there's a routing issue.

**2. Check if file is readable:**

Add this to test:
```php
public function render_metals() {
    $file = JPC_PLUGIN_DIR . 'templates/admin/metals-v2.php';
    if (file_exists($file)) {
        echo '<div style="background: green; color: white; padding: 10px;">✅ File exists!</div>';
    } else {
        echo '<div style="background: red; color: white; padding: 10px;">❌ File NOT found at: ' . $file . '</div>';
    }
    include $file;
}
```

**3. Check database column:**

Add this to metals-v2.php at the top (after `<?php`):
```php
global $wpdb;
$columns = $wpdb->get_results("SHOW COLUMNS FROM {$wpdb->prefix}jpc_metals LIKE 'making_charges_per_gram'");
if (empty($columns)) {
    echo '<div style="background: red; color: white; padding: 10px;">❌ Database column missing! Run migration.</div>';
} else {
    echo '<div style="background: green; color: white; padding: 10px;">✅ Database column exists!</div>';
}
```

---

## 📞 MANUAL FIX (If All Else Fails)

### Option 1: Force Database Migration

Run this in WordPress admin → Tools → Site Health → Info → Database:

```sql
ALTER TABLE `wp_jpc_metals` 
ADD COLUMN IF NOT EXISTS `making_charges_per_gram` DECIMAL(10,2) DEFAULT 0 
AFTER `price_per_unit`;
```

### Option 2: Manually Edit Old Template

If you can't get metals-v2.php to load, edit the old `metals.php`:

**Find the price field (around line 100):**
```php
<tr>
    <th><label for="metal_price">Metal Price/gram</label></th>
    <td>
        <input type="number" id="metal_price" name="price_per_unit" class="regular-text" step="0.01" min="0" required>
    </td>
</tr>
```

**Add this AFTER it:**
```php
<tr style="background: #f0f6fc;">
    <th><label for="making_charges_per_gram">Making Charges per Gram (₹) <span style="color: #2196f3;">★ NEW</span></label></th>
    <td>
        <input type="number" id="making_charges_per_gram" name="making_charges_per_gram" class="regular-text" step="0.01" min="0" value="0">
        <p class="description">This will be used to auto-calculate making charges: Metal Weight × This Value</p>
    </td>
</tr>
```

Do the same in the Edit modal section.

---

## ✅ SUCCESS INDICATORS

You'll know it's working when you see:

1. **Blue info box** at top of Metals page explaining the feature
2. **"Making Charges per Gram (₹)"** field in Add New Metal form
3. **"Making Charges/Gram"** column in metals list
4. **★ NEW** badge next to the field label
5. **Auto-calc enabled/Not set** status in the list

---

## 📧 NEED MORE HELP?

If still not working, provide:

1. **Screenshot** of your Metals page
2. **Error messages** from debug.log
3. **File list** of templates/admin/ folder
4. **Database structure** of wp_jpc_metals table
5. **WordPress version** and **PHP version**

---

**Last Updated:** v2.0.0  
**Status:** Complete Troubleshooting Guide
