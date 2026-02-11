# Product Meta Box PHP Changes

## File: `includes/class-jpc-product-meta-box-v2.php`

### Change 1: Add metal group settings check (After line 86)

**INSERT AFTER LINE 86** (after `$metal_weight = get_post_meta...`):

```php
// Get selected metal's group settings for conditional display
$selected_metal = null;
if ($metal_id) {
    foreach ($metals as $m) {
        if ($m->id == $metal_id) {
            $selected_metal = $m;
            break;
        }
    }
}
$enable_making = $selected_metal ? ($selected_metal->enable_making_charge ?? 1) : 1;
$enable_wastage = $selected_metal ? ($selected_metal->enable_wastage_charge ?? 1) : 1;
```

### Change 2: Update metal dropdown options (Around line 150)

**REPLACE:**
```php
<option value="<?php echo esc_attr($metal->id); ?>" 
        data-price="<?php echo esc_attr($metal->price_per_unit); ?>"
        data-making-charges="<?php echo esc_attr($metal->making_charges_per_gram ?? 0); ?>"
        <?php selected($metal_id, $metal->id); ?>>
```

**WITH:**
```php
<option value="<?php echo esc_attr($metal->id); ?>" 
        data-price="<?php echo esc_attr($metal->price_per_unit); ?>"
        data-making-charges="<?php echo esc_attr($metal->making_charges_per_gram ?? 0); ?>"
        data-enable-making="<?php echo esc_attr($metal->enable_making_charge ?? 1); ?>"
        data-enable-wastage="<?php echo esc_attr($metal->enable_wastage_charge ?? 1); ?>"
        <?php selected($metal_id, $metal->id); ?>>
```

### Change 3: Add ID and conditional display to wastage field (Around line 169)

**REPLACE:**
```php
<div class="jpc-form-field">
    <label for="jpc_wastage"><?php _e('Wastage (%)', 'jewellery-price-calc'); ?></label>
    <input type="number" id="jpc_wastage" name="jpc_wastage" 
           value="<?php echo esc_attr($wastage); ?>" 
           step="0.01" min="0">
</div>
```

**WITH:**
```php
<div class="jpc-form-field" id="jpc_wastage_field" style="display: <?php echo $enable_wastage ? 'block' : 'none'; ?>;">
    <label for="jpc_wastage"><?php _e('Wastage (%)', 'jewellery-price-calc'); ?></label>
    <input type="number" id="jpc_wastage" name="jpc_wastage" 
           value="<?php echo esc_attr($wastage); ?>" 
           step="0.01" min="0">
</div>
```

### Change 4: Add ID and conditional display to making charges section (Around line 177)

**REPLACE:**
```php
<!-- Making Charges Section v2.0.0 -->
<div class="jpc-section highlight">
    <h3>
        <?php _e('Making Charges', 'jewellery-price-calc'); ?>
        <span class="jpc-new-badge">v2.0 NEW</span>
    </h3>
```

**WITH:**
```php
<!-- Making Charges Section v2.0.0 -->
<div class="jpc-section highlight" id="jpc_making_charges_section" style="display: <?php echo $enable_making ? 'block' : 'none'; ?>;">
    <h3>
        <?php _e('Making Charges', 'jewellery-price-calc'); ?>
        <span class="jpc-new-badge">v2.0 NEW</span>
    </h3>
```

---

## Summary of Changes

1. **Line ~86**: Add logic to get selected metal's enable flags
2. **Line ~150**: Add `data-enable-making` and `data-enable-wastage` attributes to metal dropdown
3. **Line ~169**: Add `id="jpc_wastage_field"` and conditional `display` style to wastage field wrapper
4. **Line ~177**: Add `id="jpc_making_charges_section"` and conditional `display` style to making charges section

---

## How It Works

1. **On Page Load**: PHP checks if selected metal has making/wastage enabled and shows/hides fields accordingly
2. **On Metal Change**: JavaScript reads `data-enable-*` attributes and shows/hides fields dynamically
3. **Smooth UX**: Uses `slideDown()`/`slideUp()` for smooth transitions

---

## Testing

1. Edit a product
2. Select "22 Karat Gold" (with wastage disabled)
3. ✅ Wastage field should be hidden
4. Change to "Silver" (with wastage enabled)
5. ✅ Wastage field should slide into view

Same for making charges section.
