<?php
/**
 * QUICK FIX SCRIPT - Run this once to fix the corrupted extra fields section
 * 
 * Instructions:
 * 1. Upload this file to your WordPress root directory
 * 2. Visit: https://yoursite.com/fix-extra-fields.php
 * 3. Delete this file after running
 */

// Find WordPress root
$wp_root = __DIR__;

// Try to find wp-config.php to confirm we're in the right place
if (!file_exists($wp_root . '/wp-config.php')) {
    die('Error: Please upload this file to your WordPress root directory (where wp-config.php is located)');
}

// Possible paths to check
$possible_paths = [
    $wp_root . '/wp-content/plugins/jewellery-price-calculator/templates/shortcodes/product-details-accordion.php',
    $wp_root . '/public_html/wp-content/plugins/jewellery-price-calculator/templates/shortcodes/product-details-accordion.php',
];

$file_path = null;
foreach ($possible_paths as $path) {
    if (file_exists($path)) {
        $file_path = $path;
        break;
    }
}

// If not found, try to search for it
if (!$file_path) {
    // Try to use WordPress to find the plugin path
    if (file_exists($wp_root . '/wp-load.php')) {
        require_once($wp_root . '/wp-load.php');
        $plugin_dir = WP_PLUGIN_DIR . '/jewellery-price-calculator/templates/shortcodes/product-details-accordion.php';
        if (file_exists($plugin_dir)) {
            $file_path = $plugin_dir;
        }
    }
}

if (!$file_path || !file_exists($file_path)) {
    die('Error: Could not find product-details-accordion.php. Please check that the jewellery-price-calculator plugin is installed.<br><br>Searched in:<br>' . implode('<br>', $possible_paths));
}

echo '<h2>Found file at: ' . $file_path . '</h2>';

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
    die('<h1>⚠️ Warning</h1><p>Could not find the corrupted section to replace. The file may have already been fixed or the corruption is different than expected.</p><p>File checked: ' . $file_path . '</p>');
}

// Backup the original file
$backup_path = $file_path . '.backup.' . date('Y-m-d-H-i-s');
if (!copy($file_path, $backup_path)) {
    die('Error: Could not create backup file. Check file permissions.');
}

// Write the fixed content
if (!file_put_contents($file_path, $new_content)) {
    die('Error: Could not write to file. Check file permissions.');
}

echo '<h1 style="color: green;">✅ SUCCESS!</h1>';
echo '<p><strong>The extra fields section has been fixed.</strong></p>';
echo '<p>File fixed: <code>' . $file_path . '</code></p>';
echo '<p>Backup created at: <code>' . $backup_path . '</code></p>';
echo '<hr>';
echo '<p style="color: red; font-weight: bold;">⚠️ IMPORTANT: Delete this fix script now for security!</p>';
echo '<p>Test your product page to verify the fix works.</p>';
echo '<p>Your extra field labels should now update live from the settings page.</p>';
?>
