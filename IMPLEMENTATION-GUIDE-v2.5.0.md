# Implementation Guide - Version 2.5.0

## 📋 Quick Summary
This guide will help you implement the Pearl/Stone/Extra Fee enhancement in **5 simple steps**.

---

## ✅ Step 1: Update General Settings Template (COMPLETE)

**File:** `templates/admin/general-settings.php`  
**Status:** ✅ Already updated via commit

No action needed - this file is already updated!

---

## 🔧 Step 2: Update Admin Class

**File:** `includes/class-jpc-admin.php`  
**Location:** Line ~374 (inside `register_settings()` function)

### What to do:
1. Open `includes/class-jpc-admin.php`
2. Find the `register_settings()` function (around line 374)
3. Look for this section:
```php
// General settings
register_setting('jpc_general_settings', 'jpc_enable_pearl_cost');
register_setting('jpc_general_settings', 'jpc_enable_stone_cost');
register_setting('jpc_general_settings', 'jpc_enable_extra_fee');
```

4. **ADD these 6 new lines** right after the above lines:
```php
register_setting('jpc_general_settings', 'jpc_pearl_cost_label'); // NEW v2.5.0
register_setting('jpc_general_settings', 'jpc_pearl_cost_type'); // NEW v2.5.0
register_setting('jpc_general_settings', 'jpc_stone_cost_label'); // NEW v2.5.0
register_setting('jpc_general_settings', 'jpc_stone_cost_type'); // NEW v2.5.0
register_setting('jpc_general_settings', 'jpc_extra_fee_label'); // NEW v2.5.0
register_setting('jpc_general_settings', 'jpc_extra_fee_type'); // NEW v2.5.0
```

### Result should look like:
```php
// General settings
register_setting('jpc_general_settings', 'jpc_enable_pearl_cost');
register_setting('jpc_general_settings', 'jpc_pearl_cost_label'); // NEW v2.5.0
register_setting('jpc_general_settings', 'jpc_pearl_cost_type'); // NEW v2.5.0

register_setting('jpc_general_settings', 'jpc_enable_stone_cost');
register_setting('jpc_general_settings', 'jpc_stone_cost_label'); // NEW v2.5.0
register_setting('jpc_general_settings', 'jpc_stone_cost_type'); // NEW v2.5.0

register_setting('jpc_general_settings', 'jpc_enable_extra_fee');
register_setting('jpc_general_settings', 'jpc_extra_fee_label'); // NEW v2.5.0
register_setting('jpc_general_settings', 'jpc_extra_fee_type'); // NEW v2.5.0
```

---

## 🔧 Step 3: Update Price Calculator

**File:** `includes/class-jpc-price-calculator.php`  
**Location:** Line ~79-82 (inside `calculate_product_prices()` function)

### What to do:
1. Open `includes/class-jpc-price-calculator.php`
2. Find this section (around line 79):
```php
// Get additional costs
$pearl_cost = floatval(get_post_meta($product_id, '_jpc_pearl_cost', true));
$stone_cost = floatval(get_post_meta($product_id, '_jpc_stone_cost', true));
$extra_fee = floatval(get_post_meta($product_id, '_jpc_extra_fee', true));
```

3. **REPLACE the entire section** with:
```php
// Get additional costs - UPDATED v2.5.0 to support percentage calculations
$pearl_cost_value = floatval(get_post_meta($product_id, '_jpc_pearl_cost', true));
$pearl_cost_type = get_option('jpc_pearl_cost_type', 'fixed');
$pearl_cost = 0;
if ($pearl_cost_value > 0) {
    if ($pearl_cost_type === 'percentage') {
        $pearl_cost = ($metal_price * $pearl_cost_value) / 100;
    } else {
        $pearl_cost = $pearl_cost_value;
    }
}

$stone_cost_value = floatval(get_post_meta($product_id, '_jpc_stone_cost', true));
$stone_cost_type = get_option('jpc_stone_cost_type', 'fixed');
$stone_cost = 0;
if ($stone_cost_value > 0) {
    if ($stone_cost_type === 'percentage') {
        $stone_cost = ($metal_price * $stone_cost_value) / 100;
    } else {
        $stone_cost = $stone_cost_value;
    }
}

$extra_fee_value = floatval(get_post_meta($product_id, '_jpc_extra_fee', true));
$extra_fee_type = get_option('jpc_extra_fee_type', 'fixed');
$extra_fee = 0;
if ($extra_fee_value > 0) {
    if ($extra_fee_type === 'percentage') {
        $extra_fee = ($metal_price * $extra_fee_value) / 100;
    } else {
        $extra_fee = $extra_fee_value;
    }
}
```

---

## 🔧 Step 4: Update Price Breakup Template

**File:** `templates/frontend/price-breakup.php`  
**Location:** Line ~88-105

### What to do:
1. Open `templates/frontend/price-breakup.php`
2. Find the Pearl Cost section (around line 88):
```php
<!-- Pearl Cost -->
<?php if (!empty($breakup['pearl_cost']) && $breakup['pearl_cost'] > 0): ?>
<tr>
    <td><?php _e('Pearl Cost', 'jewellery-price-calc'); ?></td>
    <td><?php echo wc_price($breakup['pearl_cost']); ?></td>
</tr>
<?php endif; ?>
```

3. **REPLACE** the label line with:
```php
<td><?php echo esc_html(get_option('jpc_pearl_cost_label', __('Pearl Cost', 'jewellery-price-calc'))); ?></td>
```

4. Do the same for **Stone Cost** section:
```php
<td><?php echo esc_html(get_option('jpc_stone_cost_label', __('Stone Cost', 'jewellery-price-calc'))); ?></td>
```

5. And for **Extra Fee** section:
```php
<td><?php echo esc_html(get_option('jpc_extra_fee_label', __('Extra Fee', 'jewellery-price-calc'))); ?></td>
```

---

## 🔧 Step 5: Update Detailed Breakup Template

**File:** `templates/frontend/detailed-breakup.php`  
**Location:** Line ~60-75

### What to do:
1. Open `templates/frontend/detailed-breakup.php`
2. Find the Pearl Cost section (around line 60):
```php
<?php if ($breakup['pearl_cost'] > 0): ?>
<tr>
    <td><?php _e('Pearl Cost', 'jewellery-price-calc'); ?></td>
    <td><?php echo JPC_Frontend::format_price($breakup['pearl_cost']); ?></td>
</tr>
<?php endif; ?>
```

3. **REPLACE** the label line with:
```php
<td><?php echo esc_html(get_option('jpc_pearl_cost_label', __('Pearl Cost', 'jewellery-price-calc'))); ?></td>
```

4. Do the same for **Stone Cost** section:
```php
<td><?php echo esc_html(get_option('jpc_stone_cost_label', __('Stone Cost', 'jewellery-price-calc'))); ?></td>
```

5. And for **Extra Fee** section:
```php
<td><?php echo esc_html(get_option('jpc_extra_fee_label', __('Extra Fee', 'jewellery-price-calc'))); ?></td>
```

---

## 🎯 Final Step: Update Plugin Version

**File:** `jewellery-price-calculator.php` (main plugin file)  
**Location:** Top of file

Update the version constant:
```php
define('JPC_VERSION', '2.5.0');
```

---

## ✅ Testing Checklist

After implementing all changes:

### 1. Admin Settings Test
- [ ] Go to **Jewellery Price > General**
- [ ] Verify Pearl Cost, Stone Cost, Extra Fee sections appear
- [ ] Verify each has: Enable checkbox, Label input, Type dropdown
- [ ] Change labels and types
- [ ] Click **Save Changes**
- [ ] Refresh page and verify settings are saved

### 2. Product Test - Fixed Amount
- [ ] Edit a product
- [ ] Set Pearl Cost = 1000 (with type = Fixed)
- [ ] Save product
- [ ] View product on frontend
- [ ] Verify price breakup shows ₹1,000

### 3. Product Test - Percentage
- [ ] Go to settings, change Pearl Cost Type to "Percentage"
- [ ] Edit same product
- [ ] Set Pearl Cost = 10 (means 10%)
- [ ] If metal price is ₹50,000, pearl cost should be ₹5,000
- [ ] Verify calculation is correct

### 4. Frontend Display Test
- [ ] View product on frontend
- [ ] Verify custom labels appear in price breakup
- [ ] Verify custom labels appear in detailed breakup
- [ ] Verify amounts are calculated correctly

---

## 🐛 Troubleshooting

### Settings not saving?
- Check file permissions
- Verify WordPress nonce is working
- Check for PHP errors in debug log

### Calculations wrong?
- Verify you updated the price calculator file correctly
- Check that metal_price is calculated before pearl/stone/extra costs
- Regenerate price breakup for the product

### Labels not showing?
- Clear WordPress cache
- Verify template files are updated
- Check that `get_option()` calls are correct

---

## 📞 Need Help?

If you encounter issues:
1. Check all 5 steps are completed
2. Verify no PHP syntax errors
3. Check WordPress debug log
4. Compare your code with patch files

---

## 🎉 Success!

Once all steps are complete and tests pass, you're done! 

Your plugin now supports:
- ✅ Custom labels for Pearl/Stone/Extra costs
- ✅ Percentage or fixed amount calculations
- ✅ Consistent UI with other charge types
- ✅ Full backward compatibility

**Version 2.5.0 is ready to use!** 🚀
