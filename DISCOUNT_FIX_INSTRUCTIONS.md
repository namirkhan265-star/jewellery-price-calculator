# Discount Display Fix Instructions

## File: `templates/shortcodes/product-details-accordion.php`

### Changes Required:

#### 1. Add enable check variable (after line 157)
After this line:
```php
$discount_percentage = floatval(get_post_meta($product_id, '_jpc_discount_percentage', true));
```

Add:
```php
// v2.5.28: Get enable/disable setting for Discount
$enable_discount = get_option('jpc_enable_discount', 'no');
```

#### 2. Update discount row display (around line 451)
Change from:
```php
<?php if (!empty($price_breakup['discount'])): ?>
```

To:
```php
<?php if ($enable_discount === 'yes' && !empty($price_breakup['discount'])): ?>
```

#### 3. Update savings badge display (around line 500)
Change from:
```php
<?php if (!empty($price_breakup['discount']) && $discount_percentage > 0): ?>
```

To:
```php
<?php if ($enable_discount === 'yes' && !empty($price_breakup['discount']) && $discount_percentage > 0): ?>
```

### Result:
- When "Enable Discount" is unchecked in settings, discount row and savings badge will be hidden in price breakup accordion
- Matches behavior of product meta box (already fixed in v2.5.28)
