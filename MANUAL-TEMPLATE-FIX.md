# Manual Template Fix - Copy & Paste Solution

## Problem
The automated fix isn't working. Let's do it manually.

## Solution
Replace specific lines in the template file.

---

## Step 1: Open the File

Using File Manager or FTP, open this file:
```
/wp-content/plugins/jewellery-price-calculator-main/templates/frontend/price-breakup.php
```

---

## Step 2: Find Line 78 (Diamond Price Section)

Look for this code (around line 78):
```php
<!-- Diamond Price -->
<?php if (!empty($breakup['diamond_price']) && $breakup['diamond_price'] > 0): ?>
```

**REPLACE IT WITH:**
```php
<!-- Diamond Price - CRITICAL FIX: Always show if exists -->
<?php if (isset($breakup['diamond_price']) && $breakup['diamond_price'] > 0): ?>
```

**Change:** `!empty` to `isset`

---

## Step 3: Find Line 185-200 (GST Section)

Look for this code (around line 185-200):
```php
<!-- GST - ALWAYS show with percentage from settings (v2.5.9 FIX) -->
<?php if (!empty($breakup['gst']) && $breakup['gst'] > 0): ?>
<tr>
    <td>
        <?php 
        // ALWAYS show GST with percentage if enabled
        if ($gst_percentage > 0) {
            printf('%s (%s%%)', esc_html($gst_label), number_format($gst_percentage, 2));
        } else {
            echo esc_html($gst_label);
        }
        ?>
    </td>
    <td><?php echo wc_price($breakup['gst']); ?></td>
</tr>
<?php endif; ?>
```

**REPLACE IT WITH:**
```php
<!-- GST - CRITICAL FIX: Fetch label and percentage DYNAMICALLY -->
<?php if (!empty($breakup['gst']) && $breakup['gst'] > 0): ?>
<tr>
    <td>
        <?php 
        // Show GST with percentage as integer (3% not 3.00%)
        if ($gst_percentage > 0) {
            // Remove decimals if it's a whole number
            $gst_display = (floor($gst_percentage) == $gst_percentage) 
                ? number_format($gst_percentage, 0) 
                : number_format($gst_percentage, 2);
            printf('%s (%s%%)', esc_html($gst_label), $gst_display);
        } else {
            echo esc_html($gst_label);
        }
        ?>
    </td>
    <td><?php echo wc_price($breakup['gst']); ?></td>
</tr>
<?php endif; ?>
```

**Key change:** `number_format($gst_percentage, 2)` becomes smart formatting that shows "3" instead of "3.00"

---

## Step 4: Save the File

Save the file and upload it back to the server.

---

## Step 5: Clear All Caches

1. **WordPress Cache:** If you have a caching plugin (WP Super Cache, W3 Total Cache, etc.), clear it
2. **Browser Cache:** Press Ctrl+F5 (Windows) or Cmd+Shift+R (Mac)
3. **Server Cache:** If you have server-level caching, clear it

---

## Step 6: Test

1. Go to any product on your website
2. Click "Price Breakup" tab
3. You should now see:
   - Diamond price (if product has diamonds)
   - GST (3%) with percentage

---

## If Still Not Working

Run this debug script to see what's happening:
```
https://detailx.co.in/wp-content/plugins/jewellery-price-calculator-main/force-clear-cache-and-check.php
```

This will show you:
- Which template file is being used
- What's in the breakup data
- If the template has the fixes
- If there's a theme override

---

## Alternative: Download Fixed File

If manual editing is too difficult:

1. Download the fixed template from GitHub: `price-breakup-FIXED.php`
2. Rename it to: `price-breakup.php`
3. Upload it to: `/wp-content/plugins/jewellery-price-calculator-main/templates/frontend/`
4. Overwrite the existing file
5. Clear all caches
6. Test

---

## Still Having Issues?

Upload and run: `force-clear-cache-and-check.php`

This will tell us exactly what's wrong.
