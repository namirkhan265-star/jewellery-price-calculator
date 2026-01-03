<?php
/**
 * EMERGENCY FIX - Remove duplicate code causing syntax error
 * Line 303 has HTML comment inside PHP which is invalid
 */

$file_path = __DIR__ . '/templates/shortcodes/product-details-accordion.php';

if (!file_exists($file_path)) {
    die('Error: File not found at: ' . $file_path);
}

// Read file
$content = file_get_contents($file_path);

// The BROKEN code (lines 300-344) - duplicated and has HTML inside PHP
$search = <<<'EOD'
            <!-- Extra Fields #1-5 with custom labels -->
<?php
if (!empty($price_breakup['extra_fields']) && is_array($price_breakup['extra_fields'])) {
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
        if (!empty($extra_field['value']) && $extra_field['value'] > 0) {
            // Get field number (use stored number or fallback to index)
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
EOD;

// The CORRECT code (clean, no duplication)
$replace = <<<'EOD'
            <!-- Extra Fields #1-5 with custom labels -->
            <?php
            if (!empty($price_breakup['extra_fields']) && is_array($price_breakup['extra_fields'])) {
                $field_index = 0;
                foreach ($price_breakup['extra_fields'] as $extra_field) {
                    $field_index++;
                    if (!empty($extra_field['value']) && $extra_field['value'] > 0) {
                        $field_num = !empty($extra_field['field_number']) ? $extra_field['field_number'] : $field_index;
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
EOD;

// Replace
$new_content = str_replace($search, $replace, $content);

if ($new_content === $content) {
    die('<h1 style="color: orange;">⚠️ WARNING</h1><p>Could not find the exact code to replace. The file may have already been fixed or modified.</p>');
}

// Backup
$backup_path = $file_path . '.backup.' . date('Y-m-d-H-i-s');
copy($file_path, $backup_path);

// Write
file_put_contents($file_path, $new_content);

echo '<h1 style="color: green;">✅ SUCCESS!</h1>';
echo '<p><strong>Fixed the syntax error in product-details-accordion.php</strong></p>';
echo '<ul>';
echo '<li>Removed duplicate code (lines 300-344)</li>';
echo '<li>Fixed HTML comment inside PHP error</li>';
echo '<li>Cleaned up extra fields display code</li>';
echo '</ul>';
echo '<p>Backup created: ' . basename($backup_path) . '</p>';
echo '<hr>';
echo '<p style="color: red; font-weight: bold;">⚠️ DELETE THIS SCRIPT NOW!</p>';
echo '<p><strong>Now try activating the plugin again - it should work!</strong></p>';
?>
