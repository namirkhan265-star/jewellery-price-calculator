# Quick Reference - v2.5.0 Implementation

## 📦 Files to Update

| # | File | Lines | Action |
|---|------|-------|--------|
| 1 | `templates/admin/general-settings.php` | - | ✅ Already done |
| 2 | `includes/class-jpc-admin.php` | ~376-380 | Add 6 lines |
| 3 | `includes/class-jpc-price-calculator.php` | ~79-82 | Replace section |
| 4 | `templates/frontend/price-breakup.php` | ~88-105 | Update 3 labels |
| 5 | `templates/frontend/detailed-breakup.php` | ~60-75 | Update 3 labels |

---

## 🔧 File 2: class-jpc-admin.php

**Find:** (line ~376)
```php
register_setting('jpc_general_settings', 'jpc_enable_pearl_cost');
```

**Add after:**
```php
register_setting('jpc_general_settings', 'jpc_pearl_cost_label');
register_setting('jpc_general_settings', 'jpc_pearl_cost_type');
```

**Repeat for stone_cost and extra_fee**

---

## 🔧 File 3: class-jpc-price-calculator.php

**Find:** (line ~79)
```php
$pearl_cost = floatval(get_post_meta($product_id, '_jpc_pearl_cost', true));
```

**Replace with:**
```php
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
```

**Repeat for stone_cost and extra_fee**

---

## 🔧 File 4 & 5: Frontend Templates

**Find:**
```php
<td><?php _e('Pearl Cost', 'jewellery-price-calc'); ?></td>
```

**Replace with:**
```php
<td><?php echo esc_html(get_option('jpc_pearl_cost_label', __('Pearl Cost', 'jewellery-price-calc'))); ?></td>
```

**Repeat for:**
- Stone Cost → `jpc_stone_cost_label`
- Extra Fee → `jpc_extra_fee_label`

---

## ✅ Quick Test

1. Settings save? ✓
2. Labels show in admin? ✓
3. Percentage calculation works? ✓
4. Frontend displays correctly? ✓

---

## 📝 New Database Options

```
jpc_pearl_cost_label  (default: "Pearl Cost")
jpc_pearl_cost_type   (default: "fixed")
jpc_stone_cost_label  (default: "Stone Cost")
jpc_stone_cost_type   (default: "fixed")
jpc_extra_fee_label   (default: "Extra Fee")
jpc_extra_fee_type    (default: "fixed")
```

---

## 🎯 Version Update

```php
// jewellery-price-calculator.php
define('JPC_VERSION', '2.5.0');
```

---

**Total Time:** ~15 minutes  
**Difficulty:** Easy  
**Risk:** Low (backward compatible)
