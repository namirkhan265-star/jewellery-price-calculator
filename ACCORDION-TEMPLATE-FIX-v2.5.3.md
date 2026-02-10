# ACCORDION TEMPLATE FIX v2.5.3
## Custom Labels Not Showing in Product Details Accordion

### THE PROBLEM:
The `product-details-accordion.php` template has **HARDCODED** labels for Pearl Cost, Stone Cost, and Extra Fee instead of fetching them from settings.

### THE FIX:
Replace lines 377-388 in `templates/shortcodes/product-details-accordion.php`

**FIND THESE LINES (around line 377-388):**
```php
<?php if (!empty($price_breakup['pearl_cost'])): ?>
<div class="jpc-detail-row">
    <span class="jpc-detail-label">Pearl Cost</span>
    <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['pearl_cost'], 0); ?>/-</span>
</div>
<?php endif; ?>

<?php if (!empty($price_breakup['stone_cost'])): ?>
<div class="jpc-detail-row">
    <span class="jpc-detail-label">Stone Cost</span>
    <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['stone_cost'], 0); ?>/-</span>
</div>
<?php endif; ?>

<?php if (!empty($price_breakup['extra_fee'])): ?>
<div class="jpc-detail-row">
    <span class="jpc-detail-label">Extra Fee</span>
    <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['extra_fee'], 0); ?>/-</span>
</div>
<?php endif; ?>
```

**REPLACE WITH:**
```php
<?php if (!empty($price_breakup['pearl_cost'])): ?>
<div class="jpc-detail-row">
    <span class="jpc-detail-label"><?php echo esc_html(get_option('jpc_pearl_cost_label', 'Pearl Cost')); ?></span>
    <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['pearl_cost'], 0); ?>/-</span>
</div>
<?php endif; ?>

<?php if (!empty($price_breakup['stone_cost'])): ?>
<div class="jpc-detail-row">
    <span class="jpc-detail-label"><?php echo esc_html(get_option('jpc_stone_cost_label', 'Stone Cost')); ?></span>
    <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['stone_cost'], 0); ?>/-</span>
</div>
<?php endif; ?>

<?php if (!empty($price_breakup['extra_fee'])): ?>
<div class="jpc-detail-row">
    <span class="jpc-detail-label"><?php echo esc_html(get_option('jpc_extra_fee_label', 'Extra Fee')); ?></span>
    <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['extra_fee'], 0); ?>/-</span>
</div>
<?php endif; ?>
```

### HOW TO APPLY:

**Option 1: Manual Edit (Recommended)**
1. Open file: `/wp-content/plugins/jewellery-price-calculator-main/templates/shortcodes/product-details-accordion.php`
2. Find lines 377-388 (search for "Pearl Cost")
3. Replace the 3 hardcoded labels with `get_option()` calls as shown above
4. Save the file
5. Clear browser cache (Ctrl+Shift+R)
6. Check product page

**Option 2: Download Updated File**
1. Download the updated file from GitHub (will be uploaded shortly)
2. Upload to: `/wp-content/plugins/jewellery-price-calculator-main/templates/shortcodes/`
3. Overwrite existing file
4. Clear cache and test

### VERIFICATION:
After applying the fix:
1. Go to any product page
2. Open "PRICE BREAKUP" accordion
3. You should see "Test 6", "Test 7", "Test 8" instead of "Pearl Cost", "Stone Cost", "Extra Fee"

### WHY THIS WORKS:
- The Extra Fields (#1-5) already use `get_option()` to fetch labels (line 407)
- We're applying the same pattern to Pearl/Stone/Extra Fee
- This matches how the separate `price-breakup.php` and `detailed-breakup.php` templates work

### FILES AFFECTED:
- `templates/shortcodes/product-details-accordion.php` (lines 377-388)

### VERSION:
- Current: v2.4.0
- After fix: v2.5.3
