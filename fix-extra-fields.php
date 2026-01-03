<?php
/**
 * QUICK FIX SCRIPT - Run this once to fix the corrupted extra fields section
 * 
 * Instructions:
 * 1. Upload this file to your WordPress root directory
 * 2. Visit: https://yoursite.com/fix-extra-fields.php
 * 3. Delete this file after running
 */

// Path to the file
$file_path = __DIR__ . '/wp-content/plugins/jewellery-price-calculator/templates/shortcodes/product-details-accordion.php';

if (!file_exists($file_path)) {
    die('Error: File not found at ' . $file_path);
}

// Read the entire file
$content = file_get_contents($file_path);

// The corrupted section starts at line 300
// We need to replace everything from "<!-- Extra Fields #1-5" to just before "<!-- Additional Percentage"

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

$replace = <<<'EOD'
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
            
EOD;

// Replace the corrupted section
$new_content = str_replace($search, $replace, $content);

if ($new_content === $content) {
    die('Error: Could not find the corrupted section to replace. The file may have already been fixed or the corruption is different than expected.');
}

// Backup the original file
$backup_path = $file_path . '.backup.' . date('Y-m-d-H-i-s');
copy($file_path, $backup_path);

// Write the fixed content
file_put_contents($file_path, $new_content);

echo '<h1>✅ SUCCESS!</h1>';
echo '<p>The extra fields section has been fixed.</p>';
echo '<p>Backup created at: ' . $backup_path . '</p>';
echo '<p><strong>IMPORTANT:</strong> Delete this fix script now for security!</p>';
echo '<p>Test your product page to verify the fix works.</p>';
?>
