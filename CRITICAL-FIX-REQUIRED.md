# CRITICAL FIX REQUIRED - Plugin Activation Issue

## Problem
The plugin fails to activate with a fatal error because `class-jpc-diamonds.php` has an auto-initialization at the end of the file that runs before WordPress is fully loaded.

## Root Cause
Lines 588-589 in `includes/class-jpc-diamonds.php`:
```php
// Initialize
JPC_Diamonds::get_instance();
```

This causes the class to initialize immediately when the file is included, BEFORE WordPress hooks are ready.

## Solution Applied

### 1. Main Plugin File (v1.7.3)
✅ **FIXED** - Updated `jewellery-price-calculator.php` to properly initialize ALL singleton classes:
```php
function jpc_init() {
    // Initialize database
    JPC_Database::init();
    
    // Initialize ALL components (CRITICAL: Must initialize all singleton classes)
    JPC_Metal_Groups::get_instance();
    JPC_Metals::get_instance();
    JPC_Diamonds::get_instance();  // ← Now properly initialized
    JPC_Product_Meta::get_instance();
    JPC_Frontend::get_instance();
    JPC_Admin::get_instance();
}
```

### 2. Diamonds Class File
❌ **NEEDS MANUAL FIX** - Remove lines 588-589 from `includes/class-jpc-diamonds.php`

## Manual Fix Instructions

### Option 1: Using File Editor (Recommended)
1. Go to WordPress Admin → Plugins → Plugin File Editor
2. Select "Jewellery Price Calculator" from the dropdown
3. Open `includes/class-jpc-diamonds.php`
4. Scroll to the very bottom (lines 588-589)
5. **DELETE these two lines:**
   ```php
   // Initialize
   JPC_Diamonds::get_instance();
   ```
6. The file should end with just:
   ```php
           }
       }
   }
   ```
7. Click "Update File"
8. Try activating the plugin again

### Option 2: Using FTP/cPanel
1. Connect to your server via FTP or cPanel File Manager
2. Navigate to: `wp-content/plugins/jewellery-price-calculator/includes/`
3. Download `class-jpc-diamonds.php` as backup
4. Edit `class-jpc-diamonds.php`
5. Remove the last 2 lines (the auto-initialization)
6. Save and upload
7. Try activating the plugin again

### Option 3: Using SSH (Advanced)
```bash
cd /path/to/wp-content/plugins/jewellery-price-calculator

# Create backup
cp includes/class-jpc-diamonds.php includes/class-jpc-diamonds.php.backup

# Remove last 2 lines
head -n -2 includes/class-jpc-diamonds.php > includes/class-jpc-diamonds.php.tmp
mv includes/class-jpc-diamonds.php.tmp includes/class-jpc-diamonds.php

# Verify the fix
tail -5 includes/class-jpc-diamonds.php
```

## Verification
After applying the fix, the last few lines of `class-jpc-diamonds.php` should look like:
```php
            wp_send_json_error(array(
                'message' => 'No diamonds synced' . (!empty($errors) ? ': ' . implode(', ', $errors) : ''),
                'errors' => $errors
            ));
        }
    }
}
```

**NO auto-initialization line should be present!**

## Why This Happened
The diamonds class was the only class file with auto-initialization at the bottom. All other classes (Metals, Metal Groups, Product Meta, Frontend, Admin) properly use the singleton pattern without auto-initialization, relying on the main plugin file to initialize them at the correct time.

## After Fix
Once the manual fix is applied:
1. The plugin should activate successfully
2. All features will work normally
3. No functionality will be lost
4. The initialization happens at the proper time via `plugins_loaded` hook

## Need Help?
If you encounter any issues after applying this fix, please check:
1. PHP error logs for any new errors
2. WordPress debug.log (enable WP_DEBUG if needed)
3. Verify the file was saved correctly without the auto-initialization lines

---
**Version:** 1.7.3  
**Status:** Main plugin file fixed, diamonds class needs manual fix  
**Priority:** CRITICAL - Plugin cannot activate without this fix
