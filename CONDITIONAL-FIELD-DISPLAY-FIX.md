# Conditional Field Display Fix

## Problem
When a metal group has `enable_making_charge` or `enable_wastage_charge` set to 0 (disabled), the fields still show in the product meta box.

## Solution
Add conditional display logic to hide/show fields based on metal group settings.

## Changes Required

### File 1: `includes/class-jpc-metals.php` ✅ DONE
**Line 40-48:** Updated `get_all()` to include enable flags:
```php
SELECT m.*, 
       g.name as group_name, 
       g.unit,
       g.enable_making_charge,
       g.enable_wastage_charge
FROM $table m 
LEFT JOIN $groups_table g ON m.metal_group_id = g.id 
```

### File 2: `includes/class-jpc-product-meta-box-v2.php` - NEEDS UPDATE

#### Change 1: Add data attributes to metal dropdown (Line ~150)
```php
<option value="<?php echo esc_attr($metal->id); ?>" 
        data-price="<?php echo esc_attr($metal->price_per_unit); ?>"
        data-making-charges="<?php echo esc_attr($metal->making_charges_per_gram ?? 0); ?>"
        data-enable-making="<?php echo esc_attr($metal->enable_making_charge ?? 1); ?>"
        data-enable-wastage="<?php echo esc_attr($metal->enable_wastage_charge ?? 1); ?>"
        <?php selected($metal_id, $metal->id); ?>>
```

#### Change 2: Get selected metal's settings for initial display (Line ~86)
```php
// Get selected metal's group settings
$selected_metal = null;
if ($metal_id) {
    foreach ($metals as $m) {
        if ($m->id == $metal_id) {
            $selected_metal = $m;
            break;
        }
    }
}
$enable_making = $selected_metal ? $selected_metal->enable_making_charge : 1;
$enable_wastage = $selected_metal ? $selected_metal->enable_wastage_charge : 1;
```

#### Change 3: Conditional wastage field display (Line ~169)
```php
<div class="jpc-form-field" id="jpc_wastage_field" style="display: <?php echo $enable_wastage ? 'block' : 'none'; ?>;">
    <label for="jpc_wastage"><?php _e('Wastage (%)', 'jewellery-price-calc'); ?></label>
    <input type="number" id="jpc_wastage" name="jpc_wastage" 
           value="<?php echo esc_attr($wastage); ?>" 
           step="0.01" min="0">
</div>
```

#### Change 4: Conditional making charges section display (Line ~177)
```php
<div class="jpc-section highlight" id="jpc_making_charges_section" style="display: <?php echo $enable_making ? 'block' : 'none'; ?>;">
    <h3>
        <?php _e('Making Charges', 'jewellery-price-calc'); ?>
        <span class="jpc-new-badge">v2.0 NEW</span>
    </h3>
    ...
</div>
```

### File 3: `assets/js/product-meta-box-v2.js` - NEEDS UPDATE

#### Add JavaScript to toggle fields on metal change:
```javascript
// Toggle wastage and making charges fields based on metal group settings
$('#jpc_metal_id').on('change', function() {
    var selectedOption = $(this).find('option:selected');
    var enableMaking = selectedOption.data('enable-making');
    var enableWastage = selectedOption.data('enable-wastage');
    
    // Show/hide wastage field
    if (enableWastage == 1) {
        $('#jpc_wastage_field').show();
    } else {
        $('#jpc_wastage_field').hide();
        $('#jpc_wastage').val('0'); // Reset value when hidden
    }
    
    // Show/hide making charges section
    if (enableMaking == 1) {
        $('#jpc_making_charges_section').show();
    } else {
        $('#jpc_making_charges_section').hide();
        // Reset making charges when hidden
        $('input[name="jpc_making_charges_mode"][value="auto"]').prop('checked', true);
        $('#jpc_making_charges_value').val('');
    }
});
```

## Testing Steps

1. Go to Metal Groups
2. Disable "Enable Wastage Charge" for Gold
3. Go to Products → Edit any product
4. Select "22 Karat Gold" from metal dropdown
5. ✅ Wastage field should be HIDDEN
6. Select "Silver" (with wastage enabled)
7. ✅ Wastage field should be VISIBLE

Same for Making Charges section.

## Frontend Display

The frontend template also needs to be updated to hide disabled fields. This will be in the shortcode/template files.
