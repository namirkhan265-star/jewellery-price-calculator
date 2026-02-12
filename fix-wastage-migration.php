<?php
/**
 * One-Time Migration Script: Fix Wastage Field Name
 * 
 * This script migrates all products from old '_jpc_wastage' field to new '_jpc_wastage_percentage' field
 * Run this ONCE after updating to v2.5.35
 * 
 * HOW TO RUN:
 * 1. Upload this file to your WordPress root directory
 * 2. Visit: https://yoursite.com/fix-wastage-migration.php
 * 3. Delete this file after running
 */

// Load WordPress
require_once('wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    die('Permission denied. You must be an administrator to run this script.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>JPC Wastage Field Migration</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        .box { background: #f5f5f5; padding: 15px; margin: 10px 0; border-left: 4px solid #0073aa; }
    </style>
</head>
<body>
    <h1>🔧 JPC Wastage Field Migration</h1>
    <p>This script will migrate all products from <code>_jpc_wastage</code> to <code>_jpc_wastage_percentage</code></p>
    
    <?php
    if (isset($_GET['run'])) {
        echo '<div class="box">';
        echo '<h2>Migration in Progress...</h2>';
        
        global $wpdb;
        
        // Get all products with old wastage field
        $products_with_old_field = $wpdb->get_results("
            SELECT post_id, meta_value 
            FROM {$wpdb->postmeta} 
            WHERE meta_key = '_jpc_wastage'
        ");
        
        echo '<p class="info">Found ' . count($products_with_old_field) . ' products with old wastage field</p>';
        
        $migrated = 0;
        $skipped = 0;
        $errors = 0;
        
        foreach ($products_with_old_field as $product) {
            $product_id = $product->post_id;
            $wastage_value = $product->meta_value;
            
            // Check if new field already exists
            $existing_new = get_post_meta($product_id, '_jpc_wastage_percentage', true);
            
            if ($existing_new !== '') {
                echo '<p class="info">Product #' . $product_id . ': Already has new field (value: ' . $existing_new . '), skipping...</p>';
                $skipped++;
                continue;
            }
            
            // Migrate to new field
            $result = update_post_meta($product_id, '_jpc_wastage_percentage', $wastage_value);
            
            if ($result) {
                echo '<p class="success">✓ Product #' . $product_id . ': Migrated wastage value ' . $wastage_value . '%</p>';
                $migrated++;
                
                // Recalculate price
                if (class_exists('JPC_Price_Calculator')) {
                    JPC_Price_Calculator::calculate_and_update_price($product_id);
                    echo '<p class="success">  → Price recalculated</p>';
                }
            } else {
                echo '<p class="error">✗ Product #' . $product_id . ': Failed to migrate</p>';
                $errors++;
            }
        }
        
        echo '<hr>';
        echo '<h3>Migration Summary:</h3>';
        echo '<p class="success"><strong>Migrated:</strong> ' . $migrated . ' products</p>';
        echo '<p class="info"><strong>Skipped:</strong> ' . $skipped . ' products (already had new field)</p>';
        echo '<p class="error"><strong>Errors:</strong> ' . $errors . ' products</p>';
        
        if ($migrated > 0) {
            echo '<p class="success"><strong>✓ Migration completed successfully!</strong></p>';
            echo '<p>All product prices have been recalculated with the correct wastage values.</p>';
        }
        
        echo '</div>';
        
        echo '<div class="box">';
        echo '<h3>Next Steps:</h3>';
        echo '<ol>';
        echo '<li>Test a few products to ensure wastage is calculating correctly</li>';
        echo '<li>Go to Metals page and click "Bulk Update All Prices" to refresh all prices</li>';
        echo '<li><strong>DELETE THIS FILE (fix-wastage-migration.php) from your server</strong></li>';
        echo '</ol>';
        echo '</div>';
        
    } else {
        ?>
        <div class="box">
            <h2>⚠️ Before You Start</h2>
            <ul>
                <li>This script will migrate wastage values from old field to new field</li>
                <li>It will recalculate prices for all affected products</li>
                <li>This is a ONE-TIME operation</li>
                <li><strong>Backup your database before proceeding</strong></li>
            </ul>
        </div>
        
        <div class="box">
            <h2>Ready to Migrate?</h2>
            <p><a href="?run=1" style="display: inline-block; background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 3px;">▶ Start Migration</a></p>
        </div>
        <?php
    }
    ?>
    
</body>
</html>
