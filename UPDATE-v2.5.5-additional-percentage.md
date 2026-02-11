# v2.5.5: Additional Percentage Enhancement

## Changes Made

### 1. Added Enable/Disable Checkbox
- New setting: `jpc_enable_additional_percentage` (yes/no)
- Checkbox added to admin settings
- Properly handled in `handle_settings_save()` method

### 2. Clear Documentation
- Added comprehensive help text explaining:
  - What the field does
  - How it's calculated
  - When it's applied in the price calculation
  - Example calculation

### 3. Calculation Formula
```
Additional Percentage is calculated on:
Metal Price + Diamond Cost + Making Charges + Wastage + Pearl Cost + Stone Cost + Extra Fee

Formula: (Subtotal × Additional Percentage) / 100
```

### 4. Files Modified
1. `includes/class-jpc-admin.php` - Added checkbox handling and registration
2. `templates/admin/general-settings.php` - Enhanced UI with toggle and documentation

### 5. Price Calculation Order
```
1. Metal Price
2. Diamond Cost
3. Making Charges
4. Wastage
5. Pearl Cost (if enabled)
6. Stone Cost (if enabled)
7. Extra Fee (if enabled)
8. = Subtotal Before Additional %
9. Additional Percentage (if enabled) ← Applied here
10. = Subtotal After Additional %
11. Discount (if any)
12. GST/Tax
13. = Final Price
```

## Usage Example

If you set Additional Percentage to 2% (Gateway Charges):

**Product Costs:**
- Metal: ₹10,000
- Diamond: ₹5,000
- Making: ₹2,000
- Wastage: ₹500
- **Subtotal: ₹17,500**

**Additional Percentage Calculation:**
- 2% of ₹17,500 = ₹350
- **New Subtotal: ₹17,850**

Then discount and GST are applied on ₹17,850.

## Benefits
- ✅ Clear enable/disable control
- ✅ Comprehensive documentation
- ✅ Visual calculation formula
- ✅ Example for better understanding
- ✅ Consistent with other cost fields UI
