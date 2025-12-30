# Installation & Update Guide - v1.4.0

## 🚀 Fresh Installation

### **Step 1: Upload Plugin**
```
1. Download the plugin ZIP file
2. Go to WordPress Admin → Plugins → Add New
3. Click "Upload Plugin"
4. Choose the ZIP file
5. Click "Install Now"
6. Click "Activate Plugin"
```

### **Step 2: Verify Installation**
```
1. Go to WordPress Admin
2. Look for "Jewellery Price" in the left menu
3. You should see these menu items:
   - General
   - Metal Groups
   - Metals
   - Diamond Groups ← NEW!
   - Diamond Types ← NEW!
   - Certifications ← NEW!
   - Diamonds (Legacy)
   - Discount
   - Price History
   - Shortcodes
   - Debug
```

### **Step 3: Check Database Tables**
```
1. Go to Jewellery Price → Debug
2. Verify these tables exist:
   - jpc_metal_groups ✓
   - jpc_metals ✓
   - jpc_diamond_groups ✓ NEW!
   - jpc_diamond_types ✓ NEW!
   - jpc_diamond_certifications ✓ NEW!
   - jpc_diamonds ✓
   - jpc_price_history ✓
   - jpc_product_price_log ✓
```

### **Step 4: Verify Default Data**
```
1. Go to Jewellery Price → Diamond Groups
   Should see: Natural Diamond, Lab Grown Diamond, Moissanite

2. Go to Jewellery Price → Diamond Types
   Should see: Multiple carat ranges for each group

3. Go to Jewellery Price → Certifications
   Should see: GIA, IGI, HRD, None
```

---

## 🔄 Updating from Previous Version

### **If Updating from v1.3.0 or Earlier:**

#### **Step 1: Backup First!**
```
1. Backup your database
2. Backup your wp-content/plugins folder
3. Export all products (WooCommerce → Products → Export)
```

#### **Step 2: Deactivate Plugin**
```
1. Go to Plugins
2. Find "Jewellery Price Calculator"
3. Click "Deactivate"
```

#### **Step 3: Delete Old Version**
```
1. Click "Delete" on the deactivated plugin
2. Confirm deletion
```

#### **Step 4: Install New Version**
```
1. Upload new plugin ZIP
2. Install
3. Activate
```

#### **Step 5: Verify New Tables Created**
```
1. Go to Jewellery Price → Debug
2. Check if new tables exist:
   - jpc_diamond_groups
   - jpc_diamond_types
   - jpc_diamond_certifications
```

#### **Step 6: Check Default Data**
```
1. Go to Diamond Groups → Should see 3 groups
2. Go to Diamond Types → Should see carat ranges
3. Go to Certifications → Should see 4 certifications
```

---

## ⚠️ Troubleshooting

### **Problem 1: New Menu Items Not Showing**

**Solution:**
```
1. Deactivate plugin
2. Reactivate plugin
3. Clear browser cache (Ctrl+Shift+Delete)
4. Refresh admin page
```

### **Problem 2: Tables Not Created**

**Solution:**
```
1. Go to Jewellery Price → Debug
2. Click "Recreate Tables" button
3. Check if tables now exist
```

**Manual Solution:**
```sql
-- Run this in phpMyAdmin if needed
CREATE TABLE IF NOT EXISTS `wp_jpc_diamond_groups` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `slug` varchar(100) NOT NULL,
    `description` text,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `wp_jpc_diamond_types` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `diamond_group_id` bigint(20) NOT NULL,
    `carat_from` decimal(10,3) NOT NULL,
    `carat_to` decimal(10,3) NOT NULL,
    `price_per_carat` decimal(10,2) NOT NULL DEFAULT 0,
    `display_name` varchar(200) NOT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `diamond_group_id` (`diamond_group_id`),
    KEY `carat_range` (`carat_from`, `carat_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `wp_jpc_diamond_certifications` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `slug` varchar(100) NOT NULL,
    `adjustment_type` varchar(20) NOT NULL DEFAULT 'percentage',
    `adjustment_value` decimal(10,2) NOT NULL DEFAULT 0,
    `description` text,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### **Problem 3: Default Data Not Inserted**

**Solution:**
```
1. Go to Jewellery Price → Diamond Groups
2. Manually add:
   - Natural Diamond
   - Lab Grown Diamond
   - Moissanite

3. Go to Diamond Types
4. Add carat ranges for each group

5. Go to Certifications
6. Add: GIA (+20%), IGI (+15%), HRD (+18%), None (0%)
```

### **Problem 4: PHP Errors**

**Check PHP Version:**
```
Minimum Required: PHP 7.4
Recommended: PHP 8.0+
```

**Check WordPress Version:**
```
Minimum Required: WordPress 5.8
Recommended: WordPress 6.0+
```

**Check WooCommerce:**
```
Minimum Required: WooCommerce 5.0
Recommended: WooCommerce 8.0+
```

---

## ✅ Post-Installation Checklist

### **1. Verify Menu Structure**
```
☐ General settings page loads
☐ Metal Groups page loads
☐ Metals page loads
☐ Diamond Groups page loads (NEW)
☐ Diamond Types page loads (NEW)
☐ Certifications page loads (NEW)
☐ Diamonds (Legacy) page loads
☐ All other pages load
```

### **2. Verify Default Data**
```
☐ 3 Diamond Groups exist
☐ Multiple Diamond Types exist
☐ 4 Certifications exist
☐ Metal Groups exist
☐ Metals exist
```

### **3. Test Functionality**
```
☐ Can add new Diamond Group
☐ Can add new Diamond Type
☐ Can add new Certification
☐ Can edit existing items
☐ Can delete items
☐ AJAX requests work
```

### **4. Test Product Creation**
```
☐ Create test product
☐ Add metal data
☐ Add diamond data (new system)
☐ Price calculates correctly
☐ Save product
☐ View on frontend
```

---

## 📞 Support

### **If You Still Have Issues:**

1. **Check Error Logs:**
   ```
   wp-content/debug.log
   ```

2. **Enable WordPress Debug:**
   ```php
   // Add to wp-config.php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   define('WP_DEBUG_DISPLAY', false);
   ```

3. **Check Browser Console:**
   ```
   Press F12 → Console tab
   Look for JavaScript errors
   ```

4. **Verify File Permissions:**
   ```
   Folders: 755
   Files: 644
   ```

5. **Clear All Caches:**
   ```
   - WordPress cache
   - Browser cache
   - Server cache (if any)
   - CDN cache (if any)
   ```

---

## 🎯 Quick Start After Installation

### **Step 1: Review Diamond Groups**
```
Jewellery Price → Diamond Groups
Check: Natural, Lab Grown, Moissanite
```

### **Step 2: Customize Carat Pricing**
```
Jewellery Price → Diamond Types
Adjust prices for your market
```

### **Step 3: Configure Certifications**
```
Jewellery Price → Certifications
Adjust GIA, IGI, HRD premiums
```

### **Step 4: Create Test Product**
```
Products → Add New
Add diamond data using new system
Verify price calculation
```

### **Step 5: Bulk Import (Optional)**
```
Products → Import
Use new CSV format with Group IDs
Import products
```

---

**Installation Complete!** 🎉

You now have the most advanced diamond pricing system for WooCommerce!
