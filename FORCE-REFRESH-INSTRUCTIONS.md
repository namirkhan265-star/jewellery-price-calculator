# 🔄 FORCE REFRESH INSTRUCTIONS

## Problem
After uploading the fresh plugin, Metal Price and GST percentage are still not showing because WordPress is using cached template files.

## ✅ COMPLETE FIX (Follow ALL Steps)

### Step 1: Deactivate & Delete Plugin
```
1. Go to WordPress Admin → Plugins
2. Deactivate "Jewellery Price Calculator"
3. Click "Delete" to completely remove it
4. Confirm deletion
```

### Step 2: Clear ALL Caches
```
1. If using WP Super Cache: Settings → WP Super Cache → Delete Cache
2. If using W3 Total Cache: Performance → Dashboard → Empty All Caches
3. If using WP Rocket: Settings → Clear Cache
4. If using LiteSpeed Cache: LiteSpeed Cache → Toolbox → Purge All
5. Clear browser cache: Ctrl+Shift+Delete (Chrome/Firefox)
6. Clear object cache if using Redis/Memcached
```

### Step 3: Download Fresh Plugin from GitHub
```
1. Go to: https://github.com/namirkhan265-star/jewellery-price-calculator
2. Click green "Code" button
3. Click "Download ZIP"
4. Extract the ZIP file
5. Rename folder to: jewellery-price-calculator
```

### Step 4: Upload via FTP (RECOMMENDED)
```
1. Connect to your server via FTP (FileZilla, etc.)
2. Navigate to: /wp-content/plugins/
3. Upload the "jewellery-price-calculator" folder
4. Overwrite ALL files if prompted
```

**OR Upload via WordPress Admin:**
```
1. Go to Plugins → Add New → Upload Plugin
2. Choose the ZIP file
3. Click "Install Now"
4. Activate the plugin
```

### Step 5: Verify Template File
```
1. Via FTP, navigate to:
   /wp-content/plugins/jewellery-price-calculator/templates/frontend/
   
2. Open: price-breakup.php

3. Search for this code (around line 184):
   ```php
   if ($gst_percentage > 0) {
       printf('%s (%s%%)', esc_html($gst_label), number_format($gst_percentage, 2));
   }
   ```

4. If you DON'T see this code, the file didn't upload correctly!
```

### Step 6: Check Metal Price Display
```
1. Via FTP, in the same file (price-breakup.php)

2. Search for "Metal Price" (around line 65):
   ```php
   <!-- Metal Price - ALWAYS SHOW (CRITICAL) -->
   <?php if (isset($breakup['metal_price'])): ?>
   <tr>
       <td><?php echo esc_html($metal->display_name); ?></td>
       <td><?php echo wc_price($breakup['metal_price']); ?></td>
   </tr>
   <?php endif; ?>
   ```

3. If you DON'T see "ALWAYS SHOW (CRITICAL)" comment, file is old!
```

### Step 7: Fix Metal Price = 0 Issue
```
The debug shows Metal Price is 0.00. This means:

1. Go to Products → Edit "Test Product 2"
2. Check "Metal Weight" field - is it 0 or empty?
3. Check which Metal is selected
4. Go to Jewellery Price → Metals
5. Find the selected metal
6. Check "Price Per Gram" - is it 0 or empty?

Metal Price = Metal Weight × Price Per Gram

If either is 0, the result will be 0!
```

### Step 8: Regenerate Price Breakup
```
1. Go to Products → Edit "Test Product 2"
2. Scroll to "Jewellery Price Calculator" section
3. Click "Regenerate Price Breakup" button
4. Save product
```

### Step 9: Clear Caches Again
```
1. Clear WordPress cache
2. Clear browser cache (Ctrl+Shift+Delete)
3. Close browser completely
4. Reopen and visit product page
```

### Step 10: Test with Hard Refresh
```
1. Visit the product page
2. Press Ctrl+Shift+R (Windows) or Cmd+Shift+R (Mac)
3. This forces browser to reload everything
```

## 🎯 Expected Result

After following ALL steps, you should see:

```
PRICE BREAKUP
─────────────────────────────
Gold                 ₹ 104,520/-  ← SHOWS (if weight & price > 0)
Diamond              ₹ 7,500/-
Making Charges       ₹ 8,000/-
...
GST (3.00%)          ₹ 403/-      ← PERCENTAGE SHOWS!
Sale Price           ₹ 13,820.70/-
```

## 🔍 Still Not Working?

### Check Template Override
```
Some themes override plugin templates. Check if this file exists:

/wp-content/themes/YOUR-THEME/jewellery-price-calc/price-breakup.php

If it exists, DELETE IT or update it with the new code!
```

### Check PHP Version
```
1. Go to Tools → Site Health → Info → Server
2. Check PHP version - must be 7.4 or higher
3. If lower, contact your hosting provider
```

### Enable Debug Mode
```
1. Edit wp-config.php
2. Add these lines before "That's all, stop editing!":

define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

3. Check /wp-content/debug.log for errors
```

## 📞 Need Help?

If still not working after ALL steps:

1. Check debug.log for PHP errors
2. Verify template file content via FTP
3. Confirm Metal Weight and Price Per Gram are not 0
4. Try on a different browser
5. Disable all other plugins temporarily

---

**IMPORTANT:** The Metal Price = 0 issue is separate from the template issue. You need to:
1. Fix the template caching (Steps 1-10)
2. Fix the Metal Weight or Price Per Gram being 0 (Step 7)
