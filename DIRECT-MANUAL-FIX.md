# Direct Manual Fix - Download & Replace

Since automated fixes aren't working, let's do it manually.

## Step 1: Check What's Actually on Your Server

1. Upload `check-actual-template.php` to: `/wp-content/plugins/jewellery-price-calculator-main/`
2. Visit: `https://detailx.co.in/wp-content/plugins/jewellery-price-calculator-main/check-actual-template.php`
3. Take a screenshot and send it to me

This will show if the template file on your server actually has the fixes or not.

---

## Step 2: Manual Download & Replace

If the check shows the file is NOT updated:

### Download the Correct File:
1. Go to: https://github.com/namirkhan265-star/jewellery-price-calculator/blob/main/templates/frontend/price-breakup.php
2. Click "Raw" button (top right)
3. Right-click → "Save As" → Save as `price-breakup.php`

### Upload to Your Server:
1. Using File Manager or FTP
2. Go to: `/wp-content/plugins/jewellery-price-calculator-main/templates/frontend/`
3. **Backup the old file first** (rename it to `price-breakup-OLD.php`)
4. Upload the new `price-breakup.php`
5. Make sure it overwrites the existing file

### Verify:
1. Check file size should be around **10,600 bytes**
2. Last modified should be today's date
3. Visit product page and check

---

## Step 3: If Still Not Working

If the file is correct but still not showing:

### Check for Theme Override:
Your theme might have its own copy of the template that's being used instead.

Check if this file exists:
```
/wp-content/themes/hello-elementor/jewellery-price-calculator/price-breakup.php
```

If it exists, you need to update THAT file instead.

---

## What Should Be in the Correct File:

### Line 56-62 (Diamond Section):
```php
<!-- Diamond Price - CRITICAL FIX: Use isset instead of !empty -->
<?php if (isset($breakup['diamond_price']) && $breakup['diamond_price'] > 0): ?>
<tr>
    <td><?php _e('Diamond', 'jewellery-price-calc'); ?></td>
    <td><?php echo wc_price($breakup['diamond_price']); ?></td>
</tr>
<?php endif; ?>
```

### Line 157-169 (GST Section):
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

---

## Let's Debug Together

Please run `check-actual-template.php` and send me a screenshot. That will tell us:
1. If the file on your server has the fixes
2. What the actual code looks like
3. Where the problem really is

Then we can fix it properly.
