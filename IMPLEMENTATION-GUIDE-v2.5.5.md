# Implementation Guide: v2.5.5 - Additional Percentage Enhancement

## Overview
This update adds an enable/disable checkbox and comprehensive documentation to the "Additional Percentage" field, making it clear what the field does and how it's calculated.

## Files to Modify

### 1. `includes/class-jpc-admin.php`

#### Change 1: Add checkbox handling (Line ~158-164)
```php
// Handle checkbox fields - set to 'no' if not checked
$checkbox_fields = array(
    'jpc_enable_pearl_cost',
    'jpc_enable_stone_cost',
    'jpc_enable_extra_fee',
    'jpc_enable_additional_percentage', // ← ADD THIS LINE
    'jpc_enable_gst',
    'jpc_show_price_breakup',
);
```

#### Change 2: Register new setting (Line ~390-391)
```php
register_setting('jpc_general_settings', 'jpc_enable_additional_percentage'); // ← ADD THIS LINE
register_setting('jpc_general_settings', 'jpc_additional_percentage_label');
register_setting('jpc_general_settings', 'jpc_additional_percentage_value');
```

---

### 2. `templates/admin/general-settings.php`

#### Change 1: Add setting variable (Line ~23, after other settings)
```php
$enable_additional_percentage = get_option('jpc_enable_additional_percentage', 'no');
```

#### Change 2: Replace entire "Additional Percentage" section (Line ~226-250)

**REMOVE the old section:**
```php
<!-- Additional Percentage -->
<div class="jpc-card">
    <h2><?php _e('Additional Percentage', 'jewellery-price-calc'); ?></h2>
    <table class="form-table jpc-form">
        <tr>
            <th scope="row">
                <label for="jpc_additional_percentage_label"><?php _e('Label', 'jewellery-price-calc'); ?></label>
            </th>
            <td>
                <input type="text" id="jpc_additional_percentage_label" name="jpc_additional_percentage_label" value="<?php echo esc_attr($additional_percentage_label); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="jpc_additional_percentage_value"><?php _e('Percentage Value', 'jewellery-price-calc'); ?></label>
            </th>
            <td>
                <input type="number" id="jpc_additional_percentage_value" name="jpc_additional_percentage_value" value="<?php echo esc_attr($additional_percentage_value); ?>\" step=\"0.01\" min=\"0\" class=\"small-text\">
                <span class=\"description\">%</span>
            </td>
        </tr>
    </table>
</div>
```

**REPLACE with the enhanced version from:**
`templates/admin/general-settings-v2.5.5-SNIPPET.php`

---

### 3. `includes/class-jpc-price-calculator-v2.php`

#### Change: Update additional percentage calculation (Line ~210-216)

**REPLACE:**
```php
// Apply Additional Percentage (if enabled)
$additional_percentage_amount = 0;
$additional_percentage = floatval(get_option('jpc_additional_percentage_value', 0));
if ($additional_percentage > 0) {
    $additional_percentage_amount = ($subtotal_before_additional * $additional_percentage) / 100;
}
```

**WITH:**
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

## Testing Checklist

After implementing the changes:

### 1. Settings Page
- [ ] Navigate to Jewellery Price → General
- [ ] Verify "Additional Percentage" section shows with v2.5.5 ENHANCED badge
- [ ] Verify enable/disable checkbox is present
- [ ] Verify documentation boxes are displayed
- [ ] Check that fields are hidden when checkbox is unchecked
- [ ] Check that fields appear when checkbox is checked

### 2. Functionality
- [ ] Enable the checkbox and set a value (e.g., 2%)
- [ ] Save settings
- [ ] Edit a product and verify price calculation includes additional percentage
- [ ] Disable the checkbox
- [ ] Save settings
- [ ] Verify price calculation no longer includes additional percentage

### 3. Price Calculation
- [ ] Create a test product with:
  - Metal: ₹10,000
  - Making: ₹2,000
  - Wastage: ₹500
- [ ] Enable Additional Percentage at 2%
- [ ] Verify calculation:
  - Subtotal: ₹12,500
  - Additional 2%: ₹250
  - New Subtotal: ₹12,750

---

## Visual Preview

### Before (Current):
```
┌─────────────────────────────────┐
│ Additional Percentage           │
├─────────────────────────────────┤
│ Label: [Gateway Charges      ]  │
│ Percentage Value: [2] %         │
└─────────────────────────────────┘
```

### After (v2.5.5):
```
┌──────────────────────────────────────────────┐
│ Additional Percentage [v2.5.5 ENHANCED]      │
├──────────────────────────────────────────────┤
│ Description: Add a percentage-based charge...│
│                                              │
│ ☑ Enable Additional Percentage              │
│                                              │
│ ┌──────────────────────────────────────┐   │
│ │ Label: [Gateway Charges           ]  │   │
│ │ Percentage Value: [2] %              │   │
│ │                                      │   │
│ │ ℹ How It's Calculated                │   │
│ │ • Metal Price                        │   │
│ │ • Diamond Cost                       │   │
│ │ • Making Charges                     │   │
│ │ • Wastage                            │   │
│ │ • Pearl/Stone/Extra Fee              │   │
│ │                                      │   │
│ │ Formula: (Subtotal × %) ÷ 100       │   │
│ │ Example: ₹10,000 × 2% = ₹200        │   │
│ │                                      │   │
│ │ ⚠ Price Calculation Order            │   │
│ │ 1. Metal + Diamond + Making...       │   │
│ │ 2. Additional % applied here ←       │   │
│ │ 3. Discount applied                  │   │
│ │ 4. GST applied                       │   │
│ │ 5. Final Price                       │   │
│ └──────────────────────────────────────┘   │
└──────────────────────────────────────────────┘
```

---

## Benefits

✅ **Clear Control**: Enable/disable checkbox for better control
✅ **Better UX**: Fields hidden when disabled (consistent with other sections)
✅ **Documentation**: Comprehensive explanation of what it does
✅ **Formula**: Clear calculation formula with example
✅ **Context**: Shows where it fits in the price calculation order
✅ **Professional**: Matches the v2.5.0 enhanced design pattern

---

## Version History

- **v2.5.5**: Added enable/disable checkbox and comprehensive documentation
- **v2.5.0**: Enhanced other cost fields with similar pattern
- **Previous**: Basic label and value fields only

---

## Support

If you encounter any issues:
1. Check that all three files are updated
2. Clear WordPress cache
3. Test with a fresh product
4. Verify settings are saved correctly

---

## Notes

- The additional percentage is calculated BEFORE discount and GST
- It applies to the sum of: Metal + Diamond + Making + Wastage + Pearl + Stone + Extra Fee
- When disabled, the field is completely ignored in calculations
- The label is customizable and appears in price breakup
