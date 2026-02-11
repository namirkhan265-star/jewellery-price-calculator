<?php
/**
 * Apply Discount Display Fix v2.5.28 - Web Version
 * Upload this file to your WordPress root directory and access via browser
 * Example: https://yoursite.com/run-fix-web.php
 */

// Auto-detect the correct path
$possible_paths = [
    __DIR__ . '/wp-content/plugins/jewellery-price-calculator-main/templates/shortcodes/product-details-accordion.php',
    __DIR__ . '/wp-content/plugins/jewellery-price-calculator/templates/shortcodes/product-details-accordion.php',
];

$file_path = null;
foreach ($possible_paths as $path) {
    if (file_exists($path)) {
        $file_path = $path;
        break;
    }
}

if (!$file_path) {
    die("<h2>❌ Error: Template file not found</h2><p>Searched in:</p><ul>" . 
        implode('', array_map(function($p) { return "<li>$p</li>"; }, $possible_paths)) . 
        "</ul><p>Please check your plugin folder name.</p>");
}

echo "<h2>🔍 Found template file at:</h2><p><code>$file_path</code></p><hr>";

// Read the file
$content = file_get_contents($file_path);

if ($content === false) {
    die("<h2>❌ Error: Could not read file</h2>");
}

// Backup the original file
$backup_path = $file_path . '.backup-v2.5.28';
file_put_contents($backup_path, $content);
echo "<p>✅ Backup created at: <code>$backup_path</code></p>";

// Change 1: Add enable check variable after discount_percentage line
$search1 = '$discount_percentage = floatval(get_post_meta($product_id, \'_jpc_discount_percentage\', true));';
$replace1 = '$discount_percentage = floatval(get_post_meta($product_id, \'_jpc_discount_percentage\', true));

// v2.5.28: Get enable/disable setting for Discount
$enable_discount = get_option(\'jpc_enable_discount\', \'no\');';

$content = str_replace($search1, $replace1, $content, $count1);
echo "<p>" . ($count1 > 0 ? "✅" : "❌") . " Change 1: Added enable check variable - " . ($count1 > 0 ? "SUCCESS" : "FAILED") . "</p>";

// Change 2: Update discount row display condition
$search2 = '<?php if (!empty($price_breakup[\'discount\'])): ?>';
$replace2 = '<?php if ($enable_discount === \'yes\' && !empty($price_breakup[\'discount\'])): ?>';

$content = str_replace($search2, $replace2, $content, $count2);
echo "<p>" . ($count2 > 0 ? "✅" : "❌") . " Change 2: Updated discount row condition - " . ($count2 > 0 ? "SUCCESS" : "FAILED") . "</p>";

// Change 3: Update savings badge display condition
$search3 = '<?php if (!empty($price_breakup[\'discount\']) && $discount_percentage > 0): ?>';
$replace3 = '<?php if ($enable_discount === \'yes\' && !empty($price_breakup[\'discount\']) && $discount_percentage > 0): ?>';

$content = str_replace($search3, $replace3, $content, $count3);
echo "<p>" . ($count3 > 0 ? "✅" : "❌") . " Change 3: Updated savings badge condition - " . ($count3 > 0 ? "SUCCESS" : "FAILED") . "</p>";

// Update version number in header comment
$content = preg_replace(
    '/Product Details Accordion Template v2\.5\.\d+/',
    'Product Details Accordion Template v2.5.28',
    $content,
    1,
    $count4
);
echo "<p>" . ($count4 > 0 ? "✅" : "❌") . " Change 4: Updated version number - " . ($count4 > 0 ? "SUCCESS" : "FAILED") . "</p>";

// Add changelog entry to header
$changelog_entry = " * NEW v2.5.28: FIX - Hide Discount in accordion when disabled in settings\n * - Check jpc_enable_discount setting before displaying discount row and savings badge\n * - Matches behavior of product meta box\n * \n";

$content = preg_replace(
    '/(Product Details Accordion Template v2\.5\.28.*?\n \* \n)/',
    "$1" . $changelog_entry,
    $content,
    1,
    $count5
);
echo "<p>" . ($count5 > 0 ? "✅" : "❌") . " Change 5: Added changelog entry - " . ($count5 > 0 ? "SUCCESS" : "FAILED") . "</p>";

// Write the updated content back to the file
$result = file_put_contents($file_path, $content);

echo "<hr>";

if ($result !== false) {
    $total_changes = $count1 + $count2 + $count3 + $count4 + $count5;
    echo "<h2>🎉 SUCCESS! File updated successfully.</h2>";
    echo "<p><strong>Total changes applied: $total_changes</strong></p>";
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Delete this file (<code>run-fix-web.php</code>) from your server for security</li>";
    echo "<li>Go to WordPress Admin → Jewellery Price Calculator Settings</li>";
    echo "<li>Ensure 'Enable Discount' is unchecked</li>";
    echo "<li>Edit a product and verify Discount section is hidden</li>";
    echo "<li>View product on frontend and verify discount doesn't appear in price breakup</li>";
    echo "</ol>";
    echo "<h3>To Revert Changes:</h3>";
    echo "<p>Restore from backup: <code>$backup_path</code></p>";
} else {
    echo "<h2>❌ ERROR: Could not write to file</h2>";
    echo "<p>Check file permissions on: <code>$file_path</code></p>";
}
