# Quick Start: v2.5.5 Implementation

## 🚀 3 Files to Update

### File 1: `includes/class-jpc-admin.php`

**Step 1:** Find line ~160 (in `handle_settings_save()` method)
```php
$checkbox_fields = array(
    'jpc_enable_pearl_cost',
    'jpc_enable_stone_cost',
    'jpc_enable_extra_fee',
    'jpc_enable_gst',  // ← ADD NEW LINE AFTER THIS
```

**Add this line:**
```php
    'jpc_enable_additional_percentage',
```

**Step 2:** Find line ~390 (in `register_settings()` method)
```php
register_setting('jpc_general_settings', 'jpc_additional_percentage_label');
```

**Add this line BEFORE it:**
```php
register_setting('jpc_general_settings', 'jpc_enable_additional_percentage');
```

---

### File 2: `templates/admin/general-settings.php`

**Step 1:** Find line ~23 (after `$extra_fee_type = ...`)

**Add this line:**
```php
$enable_additional_percentage = get_option('jpc_enable_additional_percentage', 'no');
```

**Step 2:** Find the "Additional Percentage" section (around line 226-250)

**Replace the ENTIRE section** with the code from:
`templates/admin/general-settings-v2.5.5-SNIPPET.php`

---

### File 3: `includes/class-jpc-price-calculator-v2.php`

**Find line ~210-216** (in `calculate_price()` method):

**Replace:**
```php
// Apply Additional Percentage (if enabled)
$additional_percentage_amount = 0;
$additional_percentage = floatval(get_option('jpc_additional_percentage_value', 0));
if ($additional_percentage > 0) {
    $additional_percentage_amount = ($subtotal_before_additional * $additional_percentage) / 100;
}
```

**With:**
```php
// Apply Additional Percentage (if enabled) - v2.5.5
$additional_percentage_amount = 0;
$enable_additional_percentage = get_option('jpc_enable_additional_percentage', 'no');
$additional_percentage = floatval(get_option('jpc_additional_percentage_value', 0));

if ($enable_additional_percentage === 'yes' && $additional_percentage > 0) {
    $additional_percentage_amount = ($subtotal_before_additional * $additional_percentage) / 100;
}
```

---

## ✅ Done!

After making these changes:
1. Go to **Jewellery Price → General**
2. You'll see the enhanced "Additional Percentage" section
3. Enable the checkbox to use the feature
4. Set your percentage value (e.g., 2 for 2%)
5. Save settings

---

## 📊 What You'll See

The new section includes:
- ✅ Enable/disable checkbox
- ✅ Clear description of what it does
- ✅ Calculation formula explanation
- ✅ Example calculation
- ✅ Price calculation order diagram
- ✅ Professional design matching v2.5.0 style

---

## 🎯 Key Benefits

1. **Clear Control**: Turn it on/off easily
2. **Better Understanding**: Know exactly how it's calculated
3. **Professional UI**: Matches the enhanced design pattern
4. **Consistent**: Same style as Pearl/Stone/Extra Fee fields

---

## 📝 Example Use Case

**Scenario:** You want to add 2% gateway charges

**Before v2.5.5:**
- No way to disable it
- Unclear how it's calculated
- No documentation

**After v2.5.5:**
- ☑ Enable Additional Percentage
- Label: "Gateway Charges"
- Value: 2%
- Clear explanation: "2% of ₹10,000 = ₹200"
- Shows it's applied before discount and GST

---

## 🔍 Need Help?

See the full implementation guide:
`IMPLEMENTATION-GUIDE-v2.5.5.md`

Or check the patch files:
- `PATCH-v2.5.5-admin-class.txt`
- `PATCH-v2.5.5-price-calculator.txt`
