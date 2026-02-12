# CRITICAL FIX v2.5.37 - Missing Data Attributes

## ROOT CAUSE
The metal dropdown in `includes/class-jpc-product-meta-box-v2.php` is missing `data-enable-making` and `data-enable-wastage` attributes. This causes JavaScript errors and critical errors on the metals page.

## FILE TO EDIT
`includes/class-jpc-product-meta-box-v2.php`

## LINE NUMBERS
Lines 153-161 (approximately)

## FIND THIS CODE:
```php
<?php foreach ($metals as $metal): ?>
    <option value="<?php echo esc_attr($metal->id); ?>" 
            data-price="<?php echo esc_attr($metal->price_per_unit); ?>"
            data-making-charges="<?php echo esc_attr($metal->making_charges_per_gram ?? 0); ?>"
            <?php selected($metal_id, $metal->id); ?>>
        <?php echo esc_html($metal->display_name); ?> 
        (₹<?php echo number_format($metal->price_per_unit, 2); ?>/gram)
    </option>
<?php endforeach; ?>
```

## REPLACE WITH THIS CODE:
```php
<?php foreach ($metals as $metal): ?>
    <option value="<?php echo esc_attr($metal->id); ?>" 
            data-price="<?php echo esc_attr($metal->price_per_unit); ?>"
            data-making-charges="<?php echo esc_attr($metal->making_charges_per_gram ?? 0); ?>"
            data-enable-making="<?php echo esc_attr($metal->enable_making_charge ?? 0); ?>"
            data-enable-wastage="<?php echo esc_attr($metal->enable_wastage_charge ?? 0); ?>"
            <?php selected($metal_id, $metal->id); ?>>
        <?php echo esc_html($metal->display_name); ?> 
        (₹<?php echo number_format($metal->price_per_unit, 2); ?>/gram)
    </option>
<?php endforeach; ?>
```

## WHAT THIS FIXES
- Adds `data-enable-making` attribute from metal group's `enable_making_charge` setting
- Adds `data-enable-wastage` attribute from metal group's `enable_wastage_charge` setting
- These attributes are already available in `$metal` object (from JPC_Metals::get_all() query)
- JavaScript uses these to show/hide making charges and wastage fields dynamically
- This is the ROOT CAUSE of all errors after adding enable/disable feature to metal groups

## HOW TO APPLY
1. Open `includes/class-jpc-product-meta-box-v2.php` in your code editor
2. Find the code block shown above (around line 153-161)
3. Add the two new lines:
   - `data-enable-making="<?php echo esc_attr($metal->enable_making_charge ?? 0); ?>"`
   - `data-enable-wastage="<?php echo esc_attr($metal->enable_wastage_charge ?? 0); ?>"`
4. Save the file
5. Upload to your server
6. Clear all caches
7. Test the metals page and product edit page

## VERIFICATION
After applying this fix:
1. Go to Metals page - should load without errors
2. Click "Bulk Update All Prices" - should work
3. Edit a product - should load without errors
4. Check browser console - no JavaScript errors about undefined properties

## DELETE THIS FILE
After applying the fix, delete this instruction file from your repository.
