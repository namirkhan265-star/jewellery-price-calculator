<?php
/**
 * MIGRATION SCRIPT v2.5.37
 * Adds missing enable_making_charge and enable_wastage_charge columns to jpc_metal_groups table
 * 
 * UPLOAD TO WORDPRESS ROOT AND VISIT ONCE
 */

// Load WordPress
require_once __DIR__ . '/wp-load.php';

// Security check
if (!current_user_can('manage_options')) {
    die('Access denied');
}

global $wpdb;
$table = $wpdb->prefix . 'jpc_metal_groups';

?>
<!DOCTYPE html>
<html>
<head>
    <title>JPC Migration v2.5.37</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2271b1; }
        .success { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; color: #155724; }
        .error { background: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 20px 0; color: #721c24; }
        .info { background: #d1ecf1; border-left: 4px solid #0c5460; padding: 15px; margin: 20px 0; color: #0c5460; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; color: #856404; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 4px; overflow-x: auto; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .button { display: inline-block; background: #2271b1; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; font-weight: bold; border: none; cursor: pointer; }
        .button:hover { background: #135e96; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f4f4f4; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 JPC Database Migration v2.5.37</h1>
        
        <?php
        // Check if table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table'");
        
        if (!$table_exists) {
            echo '<div class="error">';
            echo '<h3>❌ Error: Table Not Found</h3>';
            echo '<p>The table <code>' . $table . '</code> does not exist.</p>';
            echo '<p>Please activate the plugin first to create the tables.</p>';
            echo '</div>';
            exit;
        }
        
        // Get current columns
        $columns = $wpdb->get_results("SHOW COLUMNS FROM `$table`");
        $column_names = array_column($columns, 'Field');
        
        $has_enable_making = in_array('enable_making_charge', $column_names);
        $has_making_type = in_array('making_charge_type', $column_names);
        $has_enable_wastage = in_array('enable_wastage_charge', $column_names);
        $has_wastage_type = in_array('wastage_charge_type', $column_names);
        
        echo '<div class="info">';
        echo '<h3>📊 Current Table Status</h3>';
        echo '<table>';
        echo '<tr><th>Column</th><th>Status</th></tr>';
        echo '<tr><td>enable_making_charge</td><td>' . ($has_enable_making ? '✅ EXISTS' : '❌ MISSING') . '</td></tr>';
        echo '<tr><td>making_charge_type</td><td>' . ($has_making_type ? '✅ EXISTS' : '❌ MISSING') . '</td></tr>';
        echo '<tr><td>enable_wastage_charge</td><td>' . ($has_enable_wastage ? '✅ EXISTS' : '❌ MISSING') . '</td></tr>';
        echo '<tr><td>wastage_charge_type</td><td>' . ($has_wastage_type ? '✅ EXISTS' : '❌ MISSING') . '</td></tr>';
        echo '</table>';
        echo '</div>';
        
        // Check if migration is needed
        if ($has_enable_making && $has_making_type && $has_enable_wastage && $has_wastage_type) {
            echo '<div class="success">';
            echo '<h3>✅ All Columns Already Exist!</h3>';
            echo '<p>No migration needed. All required columns are present in the database.</p>';
            echo '<p><strong>You can delete this file now.</strong></p>';
            echo '</div>';
            exit;
        }
        
        // Perform migration if requested
        if (isset($_GET['migrate']) && $_GET['migrate'] === 'yes') {
            echo '<div class="info">';
            echo '<h3>🔄 Running Migration...</h3>';
            echo '</div>';
            
            $errors = [];
            $success = [];
            
            // Add enable_making_charge
            if (!$has_enable_making) {
                $result = $wpdb->query("ALTER TABLE `$table` ADD COLUMN `enable_making_charge` tinyint(1) DEFAULT 1 AFTER `unit`");
                if ($result === false) {
                    $errors[] = "Failed to add enable_making_charge: " . $wpdb->last_error;
                } else {
                    $success[] = "Added enable_making_charge column (default: 1 - enabled)";
                }
            }
            
            // Add making_charge_type
            if (!$has_making_type) {
                $result = $wpdb->query("ALTER TABLE `$table` ADD COLUMN `making_charge_type` varchar(20) DEFAULT 'percentage' AFTER `enable_making_charge`");
                if ($result === false) {
                    $errors[] = "Failed to add making_charge_type: " . $wpdb->last_error;
                } else {
                    $success[] = "Added making_charge_type column (default: 'percentage')";
                }
            }
            
            // Add enable_wastage_charge
            if (!$has_enable_wastage) {
                $result = $wpdb->query("ALTER TABLE `$table` ADD COLUMN `enable_wastage_charge` tinyint(1) DEFAULT 1 AFTER `making_charge_type`");
                if ($result === false) {
                    $errors[] = "Failed to add enable_wastage_charge: " . $wpdb->last_error;
                } else {
                    $success[] = "Added enable_wastage_charge column (default: 1 - enabled)";
                }
            }
            
            // Add wastage_charge_type
            if (!$has_wastage_type) {
                $result = $wpdb->query("ALTER TABLE `$table` ADD COLUMN `wastage_charge_type` varchar(20) DEFAULT 'percentage' AFTER `enable_wastage_charge`");
                if ($result === false) {
                    $errors[] = "Failed to add wastage_charge_type: " . $wpdb->last_error;
                } else {
                    $success[] = "Added wastage_charge_type column (default: 'percentage')";
                }
            }
            
            // Show results
            if (!empty($success)) {
                echo '<div class="success">';
                echo '<h3>✅ Migration Successful!</h3>';
                echo '<ul>';
                foreach ($success as $msg) {
                    echo '<li>' . $msg . '</li>';
                }
                echo '</ul>';
                echo '</div>';
            }
            
            if (!empty($errors)) {
                echo '<div class="error">';
                echo '<h3>❌ Migration Errors</h3>';
                echo '<ul>';
                foreach ($errors as $msg) {
                    echo '<li>' . $msg . '</li>';
                }
                echo '</ul>';
                echo '</div>';
            }
            
            if (empty($errors)) {
                echo '<div class="success">';
                echo '<h3>🎉 Migration Complete!</h3>';
                echo '<p><strong>Next Steps:</strong></p>';
                echo '<ol>';
                echo '<li>Clear all caches (WordPress, browser, server)</li>';
                echo '<li>Go to <strong>Jewellery Price → Metals</strong> - should work now!</li>';
                echo '<li>Test "Bulk Update All Prices" - should work!</li>';
                echo '<li><strong>DELETE THIS FILE</strong> from your server</li>';
                echo '</ol>';
                echo '</div>';
            }
            
        } else {
            // Show migration button
            echo '<div class="warning">';
            echo '<h3>⚠️ Migration Required</h3>';
            echo '<p>The following columns need to be added to your database:</p>';
            echo '<ul>';
            if (!$has_enable_making) echo '<li><code>enable_making_charge</code> - Controls if making charges are enabled for this metal group</li>';
            if (!$has_making_type) echo '<li><code>making_charge_type</code> - Type of making charge (percentage/fixed)</li>';
            if (!$has_enable_wastage) echo '<li><code>enable_wastage_charge</code> - Controls if wastage charges are enabled for this metal group</li>';
            if (!$has_wastage_type) echo '<li><code>wastage_charge_type</code> - Type of wastage charge (percentage/fixed)</li>';
            echo '</ul>';
            echo '<p><strong>This is safe and will not delete any existing data.</strong></p>';
            echo '</div>';
            
            echo '<p><a href="?migrate=yes" class="button">🚀 Run Migration Now</a></p>';
            
            echo '<div class="info">';
            echo '<h3>📝 What This Will Do:</h3>';
            echo '<ol>';
            echo '<li>Add the missing columns to the <code>jpc_metal_groups</code> table</li>';
            echo '<li>Set default values: <code>enable_making_charge = 1</code> (enabled)</li>';
            echo '<li>Set default values: <code>enable_wastage_charge = 1</code> (enabled)</li>';
            echo '<li>This ensures all existing metal groups have these features enabled by default</li>';
            echo '</ol>';
            echo '</div>';
        }
        ?>
        
        <hr style="margin: 30px 0;">
        
        <h3>🔍 Current Table Structure</h3>
        <table>
            <tr>
                <th>Column Name</th>
                <th>Type</th>
                <th>Default</th>
            </tr>
            <?php foreach ($columns as $col): ?>
            <tr>
                <td><code><?php echo $col->Field; ?></code></td>
                <td><?php echo $col->Type; ?></td>
                <td><?php echo $col->Default ?? 'NULL'; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        
        <p style="color: #666; font-size: 14px; margin-top: 30px;">
            <strong>Remember:</strong> Delete this file after the migration is complete!
        </p>
    </div>
</body>
</html>
