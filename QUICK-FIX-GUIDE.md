# Quick Fix Guide - GST Calculation Base Not Saving

## ✅ What You Need To Do

You've already done **Step 1** (manual file edit). Now complete **Step 2**:

### Step 1: ✅ DONE - Manual File Edit
You already edited `general-settings.php` line 400:
- Changed `original_price` to `before_discount`

### Step 2: Fix Database Value

**Choose ONE method below:**

---

## 🚀 Method 1: SQL Fix (FASTEST - 30 seconds)

1. **Go to phpMyAdmin** (or your database management tool)

2. **Select your WordPress database**

3. **Run this SQL command:**
```sql
UPDATE wp_options 
SET option_value = 'before_discount' 
WHERE option_name = 'jpc_gst_calculation_base' 
AND option_value = 'original_price';
```

4. **Verify it worked:**
```sql
SELECT option_name, option_value 
FROM wp_options 
WHERE option_name = 'jpc_gst_calculation_base';
```

Expected result: `option_value` should be either `before_discount` or `after_discount` (NOT `original_price`)

5. **Done!** Go to Step 3 below.

---

## 🔧 Method 2: PHP Script (If you prefer)

1. **Download** `fix-gst-calculation-base.php` from your GitHub repo

2. **Upload** it to your WordPress root directory (same folder as `wp-config.php`)

3. **Visit** in browser: `https://yoursite.com/fix-gst-calculation-base.php`

4. **You'll see a success message** confirming the fix

5. **Delete the file** from your server (important for security)

6. **Done!** Go to Step 3 below.

---

## Step 3: Verify Everything Works

1. **Go to:** WordPress Admin → Jewellery Price → General Settings

2. **Find:** "GST Calculation Base" setting (should be in the GST section)

3. **Test:**
   - Select "After Discount (Recommended)"
   - Click "Save Settings"
   - Reload the page
   - ✅ Setting should still be "After Discount"

4. **Test the other option:**
   - Select "Original Price (Before Discount)"
   - Click "Save Settings"
   - Reload the page
   - ✅ Setting should still be "Original Price (Before Discount)"

5. **Test price calculation:**
   - Go to any product with a discount
   - Note the current GST amount
   - Change the GST Calculation Base setting
   - Click "Update All Product Prices" button
   - Go back to the product
   - ✅ GST amount should have changed

---

## 🎯 Expected Behavior After Fix

### When "After Discount" is selected:
```
Product Price: ₹10,000
Discount (10%): -₹1,000
Subtotal: ₹9,000
GST (3% on ₹9,000): ₹270
Final Price: ₹9,270
```

### When "Before Discount" is selected:
```
Product Price: ₹10,000
GST (3% on ₹10,000): ₹300
Subtotal with GST: ₹10,300
Discount (10% on ₹10,000): -₹1,000
Final Price: ₹9,300
```

Notice the difference: ₹9,270 vs ₹9,300 (₹30 difference in GST)

---

## ❓ Troubleshooting

### Problem: Setting still doesn't save
**Solution:** Make sure you completed BOTH steps:
1. ✅ Manual file edit (you did this)
2. ⚠️ Database fix (do Method 1 or Method 2 above)

### Problem: Can't access phpMyAdmin
**Solution:** 
- Contact your hosting provider for database access
- OR use Method 2 (PHP script) instead

### Problem: SQL command doesn't work
**Solution:** Your WordPress might use a different table prefix. Replace `wp_options` with your prefix:
```sql
-- If your prefix is 'wpxyz_':
UPDATE wpxyz_options 
SET option_value = 'before_discount' 
WHERE option_name = 'jpc_gst_calculation_base' 
AND option_value = 'original_price';
```

### Problem: GST amount still not changing
**Solution:**
1. Clear all caches (WordPress cache, browser cache, server cache)
2. Go to Jewellery Price → General Settings
3. Click "Update All Product Prices" button
4. Wait for completion
5. Check product again

---

## 📝 Summary

**What was wrong:**
- Setting was registered in wrong form group
- Template used `original_price` but code expected `before_discount`
- Setting appeared in two places causing confusion

**What we fixed:**
- Moved setting registration to correct form group
- Removed duplicate from Discount Settings page
- Standardized value to `before_discount`
- Added database migration tools

**What you need to do:**
1. ✅ Manual file edit (DONE)
2. ⚠️ Run SQL fix or PHP script (DO THIS NOW)
3. ✅ Verify it works (TEST)

---

## 🆘 Need Help?

If you're still having issues after following this guide:

1. Check `CHANGELOG-v2.5.17.md` for detailed technical information
2. Verify all files are updated from GitHub
3. Make sure you ran the database fix (Step 2)
4. Clear all caches
5. Try the "Update All Product Prices" button

---

**Last Updated:** v2.5.17  
**Status:** Ready to deploy  
**Estimated Time:** 2-5 minutes
