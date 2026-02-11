<?php
/**
 * Apply Discount Display Fix v2.5.28
 * Run this script once to update the product-details-accordion.php template
 * 
 * This script adds checks for jpc_enable_discount setting before displaying discount in accordion
 */

// Path to the template file
$file_path = __DIR__ . '/templates/shortcodes/product-details-accordion.php';

if (!file_exists($file_path)) {
    die("Error: File not found at $file_path\n");
}

// Read the file
$content = file_get_contents($file_path);

if ($content === false) {
    die("Error: Could not read file\n");
}

// Backup the original file
$backup_path = $file_path . '.backup-v2.5.28';
file_put_contents($backup_path, $content);
echo "Backup created at: $backup_path\n";

// Change 1: Add enable check variable after discount_percentage line
$search1 = '$discount_percentage = floatval(get_post_meta($product_id, \'_jpc_discount_percentage\', true));';
$replace1 = '$discount_percentage = floatval(get_post_meta($product_id, \'_jpc_discount_percentage\', true));

// v2.5.28: Get enable/disable setting for Discount
$enable_discount = get_option(\'jpc_enable_discount\', \'no\');';

$content = str_replace($search1, $replace1, $content, $count1);
echo "Change 1: Added enable check variable - " . ($count1 > 0 ? "SUCCESS" : "FAILED") . "\n";

// Change 2: Update discount row display condition
$search2 = '<?php if (!empty($price_breakup[\'discount\'])): ?>';
$replace2 = '<?php if ($enable_discount === \'yes\' && !empty($price_breakup[\'discount\'])): ?>';

$content = str_replace($search2, $replace2, $content, $count2);
echo "Change 2: Updated discount row condition - " . ($count2 > 0 ? "SUCCESS" : "FAILED") . "\n";

// Change 3: Update savings badge display condition
$search3 = '<?php if (!empty($price_breakup[\'discount\']) && $discount_percentage > 0): ?>';
$replace3 = '<?php if ($enable_discount === \'yes\' && !empty($price_breakup[\'discount\']) && $discount_percentage > 0): ?>';

$content = str_replace($search3, $replace3, $content, $count3);
echo "Change 3: Updated savings badge condition - " . ($count3 > 0 ? "SUCCESS" : "FAILED") . "\n";

// Update version number in header comment
$content = preg_replace(
    '/Product Details Accordion Template v2\.5\.\d+/',
    'Product Details Accordion Template v2.5.28',
    $content,
    1,
    $count4
);
echo "Change 4: Updated version number - " . ($count4 > 0 ? "SUCCESS" : "FAILED") . "\n";

// Add changelog entry to header
$changelog_entry = " * NEW v2.5.28: FIX - Hide Discount in accordion when disabled in settings\n * - Check jpc_enable_discount setting before displaying discount row and savings badge\n * - Matches behavior of product meta box\n * \n";

$content = preg_replace(
    '/(Product Details Accordion Template v2\.5\.28.*?\n \* \n)/',
    "$1" . $changelog_entry,
    $content,
    1,
    $count5
);
echo "Change 5: Added changelog entry - " . ($count5 > 0 ? "SUCCESS" : "FAILED") . "\n";

// Write the updated content back to the file
$result = file_put_contents($file_path, $content);

if ($result !== false) {
    echo "\n✅ SUCCESS! File updated successfully.\n";
    echo "Total changes applied: " . ($count1 + $count2 + $count3 + $count4 + $count5) . "\n";
    echo "\nTo revert changes, restore from backup:\n";
    echo "cp $backup_path $file_path\n";
} else {
    echo "\n❌ ERROR: Could not write to file\n";
}
