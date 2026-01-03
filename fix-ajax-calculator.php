<?php
/**
 * FIX: Update AJAX calculator to support numeric discount methods (1, 2, 3, 4)
 * Currently it only supports string values ('simple', 'advanced', etc.)
 */

$file_path = __DIR__ . '/includes/class-jpc-product-meta.php';

if (!file_exists($file_path)) {
    die('Error: File not found');
}

$content = file_get_contents($file_path);

// Find and replace the switch statement
$search = <<<'EOD'
        switch ($discount_method) {
            case 'simple':
                // Method 1: Component-based (Metal, Making, Wastage only)
                $discountable_amount = $metal_price + $making_charge_amount + $wastage_charge_amount;
                break;
                
            case 'advanced':
                // Method 2: All components
                $discountable_amount = $subtotal;
                break;
                
            case 'total_before_gst':
                // Method 3: Discount on complete subtotal (including Additional %)
                $discountable_amount = $subtotal + $additional_percentage_amount;
                break;
                
            case 'total_after_additional':
                // Method 4: Discount includes Additional %
                $discountable_amount = $subtotal + $additional_percentage_amount;
                break;
                
            case 'discount_after_gst':
                // Method 5: Discount applied AFTER GST
                // We'll handle this differently below
                $discountable_amount = 0; // Not used in this method
                break;
                
            default:
                // Fallback to simple
                $discountable_amount = $metal_price + $making_charge_amount + $wastage_charge_amount;
        }
EOD;

$replace = <<<'EOD'
        switch ($discount_method) {
            case '1':
            case 'simple':
                // Method 1: Component-based (Metal, Making, Wastage only)
                $discountable_amount = $metal_price + $making_charge_amount + $wastage_charge_amount;
                break;
                
            case '2':
            case 'advanced':
                // Method 2: All components
                $discountable_amount = $subtotal;
                break;
                
            case '3':
            case 'total_before_gst':
                // Method 3: Discount on complete subtotal (including Additional %)
                $discountable_amount = $subtotal + $additional_percentage_amount;
                break;
                
            case '4':
            case 'total_after_additional':
                // Method 4: Discount includes Additional %
                $discountable_amount = $subtotal + $additional_percentage_amount;
                break;
                
            case '5':
            case 'discount_after_gst':
                // Method 5: Discount applied AFTER GST
                // We'll handle this differently below
                $discountable_amount = 0; // Not used in this method
                break;
                
            default:
                // Fallback to simple
                $discountable_amount = $metal_price + $making_charge_amount + $wastage_charge_amount;
        }
EOD;

$new_content = str_replace($search, $replace, $content);

if ($new_content === $content) {
    die('<h1 style="color: orange;">⚠️ WARNING</h1><p>Could not find the code to replace. File may have been modified.</p>');
}

// Backup
$backup_path = $file_path . '.backup.' . date('Y-m-d-H-i-s');
copy($file_path, $backup_path);

// Write
file_put_contents($file_path, $new_content);

echo '<h1 style="color: green;">✅ SUCCESS!</h1>';
echo '<p><strong>Fixed AJAX calculator to support numeric discount methods</strong></p>';
echo '<ul>';
echo '<li>Added support for numeric values: 1, 2, 3, 4, 5</li>';
echo '<li>Kept backward compatibility with string values</li>';
echo '<li>Now matches the main calculator logic</li>';
echo '</ul>';
echo '<p>Backup created: ' . basename($backup_path) . '</p>';
echo '<hr>';
echo '<h2>Next Steps:</h2>';
echo '<ol>';
echo '<li>Go to the product edit page</li>';
echo '<li>Click "Update" to save the product</li>';
echo '<li>Check the frontend - prices should now be correct!</li>';
echo '</ol>';
echo '<hr>';
echo '<p style="color: red; font-weight: bold;">⚠️ DELETE THIS SCRIPT NOW!</p>';
?>
