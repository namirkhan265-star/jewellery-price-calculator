# Troubleshooting: Missing Diamond Tables

## 🔴 Problem: Diamond Tables Missing

If you see this in the Debug page:

```
✗ Missing: wp_jpc_diamond_groups
✗ Missing: wp_jpc_diamond_types
✗ Missing: wp_jpc_diamond_certifications
```

**Don't panic!** This is easy to fix.

---

## ✅ Solution 1: Create Missing Tables (Recommended)

### **Step 1: Go to Debug Page**
```
WordPress Admin → Jewellery Price → Debug
```

### **Step 2: Look for Red Warning Box**
```
⚠️ Missing Tables Detected!

The following tables are missing:
- wp_jpc_diamond_groups
- wp_jpc_diamond_types
- wp_jpc_diamond_certifications

[Create Missing Tables Now] ← Click this button
```

### **Step 3: Click the Button**
```
Click: "Create Missing Tables Now"
Wait for success message
Refresh the page
```

### **Step 4: Verify Tables Created**
```
Check Database Tables Status section:
✓ wp_jpc_diamond_groups - Exists (0 records)
✓ wp_jpc_diamond_types - Exists (0 records)
✓ wp_jpc_diamond_certifications - Exists (0 records)
```

### **Step 5: Populate Data**
```
Now click: "Populate Diamond Data" button
Wait for success message showing:
- Diamond Groups: 3
- Diamond Types: 11
- Certifications: 4
```

---

## ✅ Solution 2: Recreate All Tables (If Solution 1 Fails)

### **⚠️ Warning: This deletes ALL data!**

### **Step 1: Backup First**
```
1. Export all products (WooCommerce → Products → Export)
2. Backup database via phpMyAdmin or hosting panel
3. Note down all your custom metal prices
```

### **Step 2: Recreate Tables**
```
Go to: Jewellery Price → Debug
Scroll to: "Recreate All Tables" section
Click: "Recreate All Tables" button
Confirm: "Are you ABSOLUTELY sure?"
```

### **Step 3: Verify**
```
Refresh page
All 8 tables should now show: ✓ Exists
```

### **Step 4: Check Default Data**
```
Should automatically have:
- 4 Metal Groups
- 5 Metals
- 3 Diamond Groups
- 11 Diamond Types
- 4 Certifications
```

---

## 🔍 Why Did This Happen?

### **Common Causes:**

1. **Plugin Updated While Active**
   - Tables weren't created during update
   - Solution: Deactivate → Reactivate plugin

2. **Database Permissions**
   - User doesn't have CREATE TABLE permission
   - Solution: Check with hosting provider

3. **MySQL Version Too Old**
   - Some SQL syntax not supported
   - Solution: Update MySQL to 5.7+

4. **Table Prefix Conflict**
   - Custom table prefix causing issues
   - Solution: Check wp-config.php

5. **dbDelta() Function Failed**
   - WordPress upgrade function didn't work
   - Solution: Use direct SQL (our new method)

---

## 🛠️ Manual SQL Fix (Advanced)

If buttons don't work, run this SQL in phpMyAdmin:

### **Replace `wp_` with your actual table prefix!**

```sql
-- Diamond Groups Table
CREATE TABLE IF NOT EXISTS `wp_jpc_diamond_groups` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `slug` varchar(100) NOT NULL,
    `description` text,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Diamond Types Table
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Diamond Certifications Table
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### **Then Insert Default Data:**

```sql
-- Insert Diamond Groups
INSERT INTO `wp_jpc_diamond_groups` (`name`, `slug`, `description`) VALUES
('Natural Diamond', 'natural-diamond', 'Naturally mined diamonds formed over billions of years'),
('Lab Grown Diamond', 'lab-grown-diamond', 'Laboratory-created diamonds with identical properties'),
('Moissanite', 'moissanite', 'Silicon carbide gemstone with brilliant fire');

-- Insert Diamond Types (Natural)
INSERT INTO `wp_jpc_diamond_types` (`diamond_group_id`, `carat_from`, `carat_to`, `price_per_carat`, `display_name`) VALUES
(1, 0.000, 0.500, 25000.00, 'Natural Diamond (0.00-0.50ct)'),
(1, 0.500, 1.000, 32500.00, 'Natural Diamond (0.50-1.00ct)'),
(1, 1.000, 2.000, 45000.00, 'Natural Diamond (1.00-2.00ct)'),
(1, 2.000, 3.000, 62500.00, 'Natural Diamond (2.00-3.00ct)'),
(1, 3.000, 999.990, 87500.00, 'Natural Diamond (3.00ct+)');

-- Insert Diamond Types (Lab Grown)
INSERT INTO `wp_jpc_diamond_types` (`diamond_group_id`, `carat_from`, `carat_to`, `price_per_carat`, `display_name`) VALUES
(2, 0.000, 0.500, 15000.00, 'Lab Grown Diamond (0.00-0.50ct)'),
(2, 0.500, 1.000, 19500.00, 'Lab Grown Diamond (0.50-1.00ct)'),
(2, 1.000, 2.000, 27000.00, 'Lab Grown Diamond (1.00-2.00ct)'),
(2, 2.000, 999.990, 37500.00, 'Lab Grown Diamond (2.00ct+)');

-- Insert Diamond Types (Moissanite)
INSERT INTO `wp_jpc_diamond_types` (`diamond_group_id`, `carat_from`, `carat_to`, `price_per_carat`, `display_name`) VALUES
(3, 0.000, 1.000, 5000.00, 'Moissanite (0.00-1.00ct)'),
(3, 1.000, 999.990, 6500.00, 'Moissanite (1.00ct+)');

-- Insert Certifications
INSERT INTO `wp_jpc_diamond_certifications` (`name`, `slug`, `adjustment_type`, `adjustment_value`, `description`) VALUES
('GIA', 'gia', 'percentage', 20.00, 'Gemological Institute of America - Premium certification'),
('IGI', 'igi', 'percentage', 15.00, 'International Gemological Institute - Widely recognized'),
('HRD', 'hrd', 'percentage', 18.00, 'HRD Antwerp - High quality European certification'),
('None', 'none', 'percentage', 0.00, 'No certification - Base price without premium');
```

---

## 📋 Verification Checklist

After fixing, verify everything works:

### **1. Check Debug Page**
```
☐ All 8 tables show "✓ Exists"
☐ Diamond Groups: 3 records
☐ Diamond Types: 11 records
☐ Certifications: 4 records
```

### **2. Check Admin Pages**
```
☐ Diamond Groups page loads
☐ Shows 3 groups with descriptions
☐ Diamond Types page loads
☐ Shows 11 carat ranges
☐ Certifications page loads
☐ Shows 4 certifications with examples
```

### **3. Test Functionality**
```
☐ Can add new diamond group
☐ Can add new diamond type
☐ Can add new certification
☐ Can edit existing items
☐ Can delete items
```

---

## 🔧 Still Not Working?

### **Check PHP Error Log**
```
Location: wp-content/debug.log

Enable debug mode in wp-config.php:
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### **Check MySQL Error Log**
```
Look for errors like:
- "Table doesn't exist"
- "Access denied"
- "Syntax error"
```

### **Check Database User Permissions**
```
Required permissions:
- CREATE
- ALTER
- DROP
- INSERT
- SELECT
- UPDATE
- DELETE
```

### **Check Server Requirements**
```
Minimum:
- PHP 7.4+
- MySQL 5.7+
- WordPress 5.8+
- WooCommerce 5.0+
```

---

## 💡 Prevention Tips

### **For Future Updates:**

1. **Always Deactivate Before Updating**
   ```
   Plugins → Deactivate → Update → Activate
   ```

2. **Backup Before Major Changes**
   ```
   Database + Files backup
   ```

3. **Test on Staging First**
   ```
   If you have staging environment
   ```

4. **Check Debug Page After Update**
   ```
   Verify all tables exist
   ```

---

## 📞 Need More Help?

### **Gather This Info:**

1. **WordPress Version:** `<?php echo get_bloginfo('version'); ?>`
2. **PHP Version:** `<?php echo PHP_VERSION; ?>`
3. **MySQL Version:** Check in Debug page
4. **Table Prefix:** Check in Debug page
5. **Error Messages:** From debug.log
6. **Screenshot:** Of Debug page showing missing tables

---

## 🎯 Quick Summary

### **Most Common Fix:**
```
1. Go to Debug page
2. Click "Create Missing Tables Now"
3. Click "Populate Diamond Data"
4. Done! ✓
```

### **If That Doesn't Work:**
```
1. Backup everything
2. Click "Recreate All Tables"
3. Verify in Debug page
4. Done! ✓
```

### **If Still Broken:**
```
1. Run manual SQL (see above)
2. Check error logs
3. Verify database permissions
4. Contact hosting support
```

---

**99% of the time, clicking "Create Missing Tables Now" fixes everything!** 🎉
