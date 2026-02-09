# Testing Custom Labels Feature

## ⚠️ IMPORTANT: You MUST Regenerate Price Breakup!

The custom labels are stored **inside the price breakup data** when you regenerate it. Simply changing the settings is NOT enough!

## Step-by-Step Testing Guide:

### Step 1: Set Custom Labels in Settings
1. Go to **Jewellery Price > General**
2. Scroll to "Additional Cost Fields"
3. Enable Field 1 (Pearl Cost)
4. Set Label Name to: **"Gemstone Cost"**
5. Enable Field 2 (Stone Cost)
6. Set Label Name to: **"Packaging Fee"**
7. Enable Field 3 (Extra Fee)
8. Set Label Name to: **"Certification"**
9. Click **Save Changes**

### Step 2: Verify Settings Were Saved
1. Refresh the page
2. Check if your custom labels are still there
3. If they disappeared, the settings aren't being saved properly

### Step 3: Edit a Product
1. Go to **Products > Edit** any product with JPC data
2. Scroll to "Jewellery Price Calculator" meta box
3. Enter values for:
   - Pearl Cost: 2999
   - Stone Cost: 1999
   - Extra Fee: 1500

### Step 4: **CRITICAL** - Regenerate Price Breakup
1. In the product editor, find the **"Regenerate Price Breakup"** button
2. Click it
3. Wait for success message
4. Save the product

### Step 5: View Frontend
1. View the product on frontend
2. Check the price breakup section
3. You should now see:
   - **"Gemstone Cost"** (not "Pearl Cost")
   - **"Packaging Fee"** (not "Stone Cost")
   - **"Certification"** (not "Extra Fee")

## 🔍 Debugging Steps

### If Labels Still Show as "Pearl Cost", "Stone Cost", "Extra Fee":

#### Check 1: Are Settings Saved?
Run this in WordPress admin > Tools > Site Health > Info > Copy site info to clipboard, then search for these options:

Or add this temporary code to functions.php:
```php
add_action('admin_notices', function() {
    if (current_user_can('manage_options')) {
        echo '<div class="notice notice-info">';
        echo '<p><strong>Pearl Cost Label:</strong> ' . get_option('jpc_pearl_cost_label', 'NOT SET') . '</p>';
        echo '<p><strong>Stone Cost Label:</strong> ' . get_option('jpc_stone_cost_label', 'NOT SET') . '</p>';
        echo '<p><strong>Extra Fee Label:</strong> ' . get_option('jpc_extra_fee_label', 'NOT SET') . '</p>';
        echo '</div>';
    }
});
```

#### Check 2: Is Breakup Data Updated?
Add this to functions.php to check a specific product:
```php
add_action('admin_notices', function() {
    if (current_user_can('manage_options') && isset($_GET['post'])) {
        $product_id = $_GET['post'];
        $breakup = get_post_meta($product_id, '_jpc_price_breakup', true);
        
        if ($breakup && is_array($breakup)) {
            echo '<div class="notice notice-info">';
            echo '<p><strong>Breakup Pearl Label:</strong> ' . (isset($breakup['pearl_cost_label']) ? $breakup['pearl_cost_label'] : 'NOT IN BREAKUP') . '</p>';
            echo '<p><strong>Breakup Stone Label:</strong> ' . (isset($breakup['stone_cost_label']) ? $breakup['stone_cost_label'] : 'NOT IN BREAKUP') . '</p>';
            echo '<p><strong>Breakup Extra Label:</strong> ' . (isset($breakup['extra_fee_label']) ? $breakup['extra_fee_label'] : 'NOT IN BREAKUP') . '</p>';
            echo '</div>';
        }
    }
});
```

#### Check 3: Is Code Updated?
1. Check file: `includes/class-jpc-price-calculator.php`
2. Search for: `pearl_cost_label`
3. Around line 360-370, you should see:
```php
// Get custom labels for pearl/stone/extra costs - v2.5.0
$pearl_cost_label = get_option('jpc_pearl_cost_label', 'Pearl Cost');
$stone_cost_label = get_option('jpc_stone_cost_label', 'Stone Cost');
$extra_fee_label = get_option('jpc_extra_fee_label', 'Extra Fee');
```

4. And in the breakup array:
```php
'pearl_cost_label' => $pearl_cost_label,  // v2.5.0: Store custom label
'stone_cost_label' => $stone_cost_label,  // v2.5.0: Store custom label
'extra_fee_label' => $extra_fee_label,    // v2.5.0: Store custom label
```

## 🎯 Common Issues & Solutions

### Issue 1: Settings Not Saving
**Symptom:** Labels reset to default after clicking Save  
**Solution:** 
- Check if `register_setting()` is called for these options
- File: `includes/class-jpc-admin.php` line ~377-385
- Should have:
  ```php
  register_setting('jpc_general_settings', 'jpc_pearl_cost_label');
  register_setting('jpc_general_settings', 'jpc_stone_cost_label');
  register_setting('jpc_general_settings', 'jpc_extra_fee_label');
  ```

### Issue 2: Labels Not in Breakup Data
**Symptom:** Settings save but frontend still shows defaults  
**Solution:**
- You MUST click "Regenerate Price Breakup" button
- Or use "Update All Prices" in Tools section
- Old breakup data doesn't have labels - only new/regenerated data does

### Issue 3: Frontend Template Not Updated
**Symptom:** Breakup has labels but frontend doesn't show them  
**Solution:**
- Check file: `templates/frontend/price-breakup.php`
- Around line 60-65, should have:
  ```php
  // Get custom labels from stored breakup data - v2.5.0 FIX
  $pearl_cost_label = isset($breakup['pearl_cost_label']) ? $breakup['pearl_cost_label'] : 'Pearl Cost';
  $stone_cost_label = isset($breakup['stone_cost_label']) ? $breakup['stone_cost_label'] : 'Stone Cost';
  $extra_fee_label = isset($breakup['extra_fee_label']) ? $breakup['extra_fee_label'] : 'Extra Fee';
  ```

### Issue 4: Caching
**Symptom:** Everything looks correct but frontend still shows old labels  
**Solution:**
- Clear WordPress cache (if using caching plugin)
- Clear browser cache (Ctrl+Shift+R or Cmd+Shift+R)
- Clear WooCommerce transients:
  ```php
  // Add to functions.php temporarily
  wc_delete_product_transients($product_id);
  wp_cache_flush();
  ```

## ✅ Expected Behavior

### Before Regeneration:
- Settings page shows your custom labels ✓
- Product editor shows default labels (Pearl Cost, Stone Cost, Extra Fee)
- Frontend shows default labels

### After Regeneration:
- Settings page shows your custom labels ✓
- Product editor shows your custom labels ✓
- Frontend shows your custom labels ✓
- Debug section shows labels from breakup data ✓

## 🚀 Bulk Update All Products

If you have many products and want to update all at once:

1. Go to **Jewellery Price > General**
2. Scroll to bottom
3. Click **"Update All Prices"** button
4. Wait for completion message
5. All products will now have the new labels

## 📝 Notes

- Labels are stored **per product** in the breakup data
- Changing settings doesn't affect existing products automatically
- You must regenerate breakup for each product (or use bulk update)
- This design allows historical data to remain consistent
- Future products will automatically use current labels

## 🆘 Still Not Working?

If you've followed all steps and it's still not working:

1. **Check PHP error log** for any errors
2. **Verify file permissions** - files should be writable
3. **Check WordPress version** - should be 5.0+
4. **Check WooCommerce version** - should be 3.0+
5. **Disable other plugins** temporarily to check for conflicts
6. **Switch to default theme** temporarily to check for theme conflicts

## 📧 Support

If none of the above works, provide:
1. Screenshot of General Settings page showing your custom labels
2. Screenshot of product editor showing the labels
3. Screenshot of frontend showing the labels
4. Output of the debug code snippets above
5. WordPress version
6. WooCommerce version
7. PHP version
