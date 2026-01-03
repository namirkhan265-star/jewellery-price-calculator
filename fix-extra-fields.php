<?php
/**
 * QUICK FIX SCRIPT - Run this once to fix the corrupted extra fields section
 * 
 * Instructions:
 * 1. Upload this file to your WordPress root directory (public_html)
 * 2. Visit: https://yoursite.com/fix-extra-fields.php
 * 3. Delete this file after running
 */

// Find WordPress root
$wp_root = __DIR__;

echo '<h2>Searching for the file...</h2>';
echo '<p>Starting from: ' . $wp_root . '</p>';

// Function to recursively search for the file
function findFile($dir, $filename, $maxDepth = 5, $currentDepth = 0) {
    if ($currentDepth > $maxDepth) return null;
    
    $files = @scandir($dir);
    if (!$files) return null;
    
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') continue;
        
        $path = $dir . '/' . $file;
        
        if (is_file($path) && $file === $filename) {
            return $path;
        }
        
        if (is_dir($path) && $file !== 'node_modules' && $file !== '.git') {
            $found = findFile($path, $filename, $maxDepth, $currentDepth + 1);
            if ($found) return $found;
        }
    }
    
    return null;
}

// Search for the file
echo '<p>Searching for product-details-accordion.php...</p>';
$file_path = findFile($wp_root, 'product-details-accordion.php');

if (!$file_path) {
    echo '<h1 style="color: red;">❌ File Not Found</h1>';
    echo '<p>Could not find product-details-accordion.php in your WordPress installation.</p>';
    echo '<h3>Manual Fix Instructions:</h3>';
    echo '<ol>';
    echo '<li>Go to your cPanel File Manager or FTP</li>';
    echo '<li>Navigate to: <code>wp-content/plugins/jewellery-price-calculator/templates/shortcodes/</code></li>';
    echo '<li>Find and edit <code>product-details-accordion.php</code></li>';
    echo '<li>Search for the line that says: <code>&lt;!-- Extra Fields #1-5 with custom labels --&gt;</code></li>';
    echo '<li>You will see duplicate/corrupted code. Replace lines 300-345 with the clean code from EXTRA_FIELDS_FIX_PATCH.md in your GitHub repo</li>';
    echo '</ol>';
    die();
}

echo '<h2 style="color: green;">✅ Found file at: ' . $file_path . '</h2>';

// Read the entire file
$content = file_get_contents($file_path);
$original_size = strlen($content);

echo '<p>File size: ' . number_format($original_size) . ' bytes</p>';

// The corrupted section - search for it
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

echo '<p>Searching for corrupted section...</p>';

// Check if the corrupted section exists
if (strpos($content, $search) === false) {
    echo '<h1 style="color: orange;">⚠️ Corrupted Section Not Found</h1>';
    echo '<p>The file may have already been fixed, or the corruption is different.</p>';
    echo '<p>Let me check if there\'s any duplicate code...</p>';
    
    // Check for the duplicate comment
    $comment_count = substr_count($content, '<!-- Extra Fields #1-5 with custom labels -->');
    echo '<p>Found "Extra Fields" comment ' . $comment_count . ' times.</p>';
    
    if ($comment_count > 1) {
        echo '<p style="color: red;">There IS duplicate code! Let me try a different approach...</p>';
        
        // Try to find and remove the duplicate more intelligently
        $lines = explode("\n", $content);
        $new_lines = [];
        $skip_until = -1;
        
        for ($i = 0; $i < count($lines); $i++) {
            if ($i < $skip_until) continue;
            
            // Look for the first occurrence of the duplicate
            if (strpos($lines[$i], '<!-- Extra Fields #1-5 with custom labels -->') !== false) {
                // Check if this is the start of the duplicate
                if (isset($lines[$i+1]) && strpos($lines[$i+1], '<?php') !== false) {
                    if (isset($lines[$i+2]) && strpos($lines[$i+2], 'if (!empty($price_breakup[\'extra_fields\'])') !== false) {
                        if (isset($lines[$i+3]) && strpos($lines[$i+3], '<!-- Extra Fields #1-5 with custom labels -->') !== false) {
                            // Found the duplicate! Skip to the corrected version
                            echo '<p style="color: green;">Found duplicate at line ' . ($i+1) . '! Removing...</p>';
                            
                            // Add the corrected version
                            $new_lines[] = '            <!-- Extra Fields #1-5 with custom labels -->';
                            $new_lines[] = '            <?php';
                            $new_lines[] = '            if (!empty($price_breakup[\'extra_fields\']) && is_array($price_breakup[\'extra_fields\'])) {';
                            $new_lines[] = '                $field_index = 0; // Track which field we\'re on';
                            $new_lines[] = '                foreach ($price_breakup[\'extra_fields\'] as $extra_field) {';
                            $new_lines[] = '                    $field_index++; // Increment for each field';
                            $new_lines[] = '                    if (!empty($extra_field[\'value\']) && $extra_field[\'value\'] > 0) {';
                            $new_lines[] = '                        // Get field number (use stored number or fallback to loop index)';
                            $new_lines[] = '                        $field_num = !empty($extra_field[\'field_number\']) ? $extra_field[\'field_number\'] : $field_index;';
                            $new_lines[] = '                        ';
                            $new_lines[] = '                        // Fetch live label from settings (with fallback to cached label)';
                            $new_lines[] = '                        $live_label = get_option(\'jpc_extra_field_label_\' . $field_num, $extra_field[\'label\']);';
                            $new_lines[] = '                        ?>';
                            $new_lines[] = '                        <div class="jpc-detail-row">';
                            $new_lines[] = '                            <span class="jpc-detail-label"><?php echo esc_html($live_label); ?></span>';
                            $new_lines[] = '                            <span class="jpc-detail-value">₹ <?php echo number_format($extra_field[\'value\'], 0); ?>/-</span>';
                            $new_lines[] = '                        </div>';
                            $new_lines[] = '                        <?php';
                            $new_lines[] = '                    }';
                            $new_lines[] = '                }';
                            $new_lines[] = '            }';
                            $new_lines[] = '            ?>';
                            $new_lines[] = '            ';
                            
                            // Skip all the duplicate lines (approximately 45 lines)
                            $skip_until = $i + 50;
                            
                            // Find where "Additional Percentage" starts
                            for ($j = $i; $j < min($i + 60, count($lines)); $j++) {
                                if (strpos($lines[$j], '<!-- Additional Percentage') !== false) {
                                    $skip_until = $j;
                                    break;
                                }
                            }
                            
                            continue;
                        }
                    }
                }
            }
            
            $new_lines[] = $lines[$i];
        }
        
        $new_content = implode("\n", $new_lines);
        
    } else {
        echo '<p style="color: green;">File appears to be clean already!</p>';
        die();
    }
} else {
    echo '<p style="color: green;">Found corrupted section! Replacing...</p>';
    // Replace the corrupted section
    $new_content = str_replace($search, $replace, $content);
}

$new_size = strlen($new_content);
echo '<p>New file size: ' . number_format($new_size) . ' bytes (difference: ' . ($new_size - $original_size) . ')</p>';

// Backup the original file
$backup_path = $file_path . '.backup.' . date('Y-m-d-H-i-s');
if (!copy($file_path, $backup_path)) {
    die('<h1 style="color: red;">❌ Error</h1><p>Could not create backup file. Check file permissions.</p>');
}

echo '<p style="color: green;">✅ Backup created: ' . $backup_path . '</p>';

// Write the fixed content
if (!file_put_contents($file_path, $new_content)) {
    die('<h1 style="color: red;">❌ Error</h1><p>Could not write to file. Check file permissions.</p>');
}

echo '<h1 style="color: green; font-size: 32px;">✅ SUCCESS!</h1>';
echo '<p style="font-size: 18px;"><strong>The extra fields section has been fixed!</strong></p>';
echo '<hr>';
echo '<h3>What was fixed:</h3>';
echo '<ul>';
echo '<li>✅ Removed duplicate code blocks</li>';
echo '<li>✅ Fixed $field_index counter initialization</li>';
echo '<li>✅ Corrected option name format (jpc_extra_field_label_1)</li>';
echo '<li>✅ Labels will now update live from settings</li>';
echo '</ul>';
echo '<hr>';
echo '<p style="color: red; font-weight: bold; font-size: 18px;">⚠️ IMPORTANT: Delete this fix script now for security!</p>';
echo '<p>File to delete: <code>' . __FILE__ . '</code></p>';
echo '<hr>';
echo '<h3>Next Steps:</h3>';
echo '<ol>';
echo '<li>Delete this fix-extra-fields.php file from your server</li>';
echo '<li>Clear your WordPress cache (if using any caching plugin)</li>';
echo '<li>Visit your product page and verify the extra fields show correct labels</li>';
echo '<li>Go to Settings → Extra Fields and try changing a label - it should update immediately</li>';
echo '</ol>';
?>
