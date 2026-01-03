<?php
/**
 * SIMPLE FIX - Frontend Price Display
 * 
 * This fixes ONLY the regular price calculation
 * Changes 3 lines, touches nothing else
 */

$file_path = __DIR__ . '/wp-content/plugins/jewellery-price-calculator-main/templates/shortcodes/product-details-accordion.php';

if (!file_exists($file_path)) {
    die('Error: File not found. Please check the path.');
}

// Read file
$content = file_get_contents($file_path);

// The WRONG code (3 lines)
$search = <<<'EOD'
    // Calculate regular price: Subtotal (before discount) + GST (on discounted amount)
    // This matches the admin panel calculation where Regular Price = Subtotal + GST shown in breakup
    $regular_price = $subtotal_after_additional + $gst_amount;
EOD;

// The CORRECT code (4 lines)
$replace = <<<'EOD'
    // CORRECT CALCULATION:
    // Regular Price = Subtotal (after additional %) + GST (on full amount before discount)
    $gst_on_full = !empty($price_breakup['gst_on_full']) ? floatval($price_breakup['gst_on_full']) : $gst_amount;
    $regular_price = $subtotal_after_additional + $gst_on_full;
EOD;

// Replace
$new_content = str_replace($search, $replace, $content);

if ($new_content === $content) {
    die('Error: Could not find the code to replace. File may have already been fixed.');
}

// Backup
$backup_path = $file_path . '.backup.' . date('Y-m-d-H-i-s');
copy($file_path, $backup_path);

// Write
file_put_contents($file_path, $new_content);

echo '<h1 style="color: green;">✅ SUCCESS!</h1>';
echo '<p>Fixed the regular price calculation in frontend template.</p>';
echo '<p><strong>What was changed:</strong></p>';
echo '<ul>';
echo '<li>Regular Price now uses <code>gst_on_full</code> (GST before discount)</li>';
echo '<li>This matches the admin panel calculation exactly</li>';
echo '<li>Only 3 lines changed, nothing else touched</li>';
echo '</ul>';
echo '<p>Backup created: ' . $backup_path . '</p>';
echo '<p style="color: red; font-weight: bold;">⚠️ Delete this script now!</p>';
echo '<p>Now test your product page - prices should be correct!</p>';
?>
