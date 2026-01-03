# Extra Fields Section Fix

## Problem
Lines 300-345 in `templates/shortcodes/product-details-accordion.php` have duplicate/corrupted code.

## Solution
Replace lines 300-345 with this clean code:

```php
            <!-- Extra Fields #1-5 with custom labels -->
            <?php
            if (!empty($price_breakup['extra_fields']) && is_array($price_breakup['extra_fields'])) {
                $field_index = 0; // Track which field we're on
                foreach ($price_breakup['extra_fields'] as $extra_field) {
                    $field_index++; // Increment for each field
                    if (!empty($extra_field['value']) && $extra_field['value'] > 0) {
                        // Get field number (use stored number or fallback to loop index)
                        $field_num = !empty($extra_field['field_number']) ? $extra_field['field_number'] : $field_index;
                        
                        // Fetch live label from settings (with fallback to cached label)
                        $live_label = get_option('jpc_extra_field_label_' . $field_num, $extra_field['label']);
                        ?>
                        <div class="jpc-detail-row">
                            <span class="jpc-detail-label"><?php echo esc_html($live_label); ?></span>
                            <span class="jpc-detail-value">₹ <?php echo number_format($extra_field['value'], 0); ?>/-</span>
                        </div>
                        <?php
                    }
                }
            }
            ?>
            
            <!-- Additional Percentage BEFORE Subtotal (with percentage value always shown) -->
```

## How to Apply

1. Open `templates/shortcodes/product-details-accordion.php`
2. Find line 300 (search for "Extra Fields #1-5 with custom labels")
3. Delete everything from line 300 to line 345 (before "Additional Percentage BEFORE Subtotal")
4. Paste the clean code above
5. Save the file

## What This Fixes

- Removes duplicate code blocks
- Properly initializes `$field_index` counter
- Correctly increments counter for each field
- Uses correct option name format: `jpc_extra_field_label_1` (not `jpc_extra_field_1_label`)
- Labels now update live from settings without recalculating prices
