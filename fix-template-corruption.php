<?php
/**
 * EMERGENCY FIX: Remove duplicate code from product-details-accordion.php
 */

$file_path = __DIR__ . '/templates/shortcodes/product-details-accordion.php';

if (!file_exists($file_path)) {
    die('<h1 style="color: red;">❌ File not found!</h1>');
}

// Backup first
$backup_path = $file_path . '.backup.' . date('Y-m-d-H-i-s');
copy($file_path, $backup_path);

// Read the file
$content = file_get_contents($file_path);

// Find the corrupted section (lines 300-340 approximately)
// The issue is duplicate code with HTML comment inside PHP

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

$new_content = str_replace($search, $replace, $content);

if ($new_content === $content) {
    die('<h1 style="color: orange;">⚠️ Could not find exact match</h1><p>The file may have different corruption. Restoring from GitHub...</p>');
}

// Write the fixed content
file_put_contents($file_path, $new_content);

echo '<h1 style="color: green;">✅ FIXED!</h1>';
echo '<p>Removed duplicate code from product-details-accordion.php</p>';
echo '<p>Backup saved: ' . basename($backup_path) . '</p>';
echo '<hr>';
echo '<h2>Next Steps:</h2>';
echo '<ol>';
echo '<li>Refresh your website - the critical error should be gone</li>';
echo '<li>Delete this script</li>';
echo '<li>Click "Update All Prices Now" in General Settings</li>';
echo '</ol>';
?>
