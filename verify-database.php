<?php
/**
 * DATABASE VERIFICATION SCRIPT v2.5.37
 * Checks what's actually wrong with the database
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/wp-load.php';

if (!current_user_can('manage_options')) {
    die('Access denied');
}

global $wpdb;

?>
<!DOCTYPE html>
<html>
<head>
    <title>JPC Database Verification</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2271b1; }
        .success { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; }
        .error { background: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 20px 0; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; }
        .info { background: #d1ecf1; border-left: 4px solid #0c5460; padding: 15px; margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background: #f4f4f4; font-weight: bold; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 4px; overflow-x: auto; }
        .good { color: #28a745; font-weight: bold; }
        .bad { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 JPC Database Verification v2.5.37</h1>
        
        <?php
        // Check metal_groups table structure
        $table = $wpdb->prefix . 'jpc_metal_groups';
        $columns = $wpdb->get_results("SHOW COLUMNS FROM `$table`");
        
        echo '<h2>Table Structure: ' . $table . '</h2>';
        echo '<table>';
        echo '<tr><th>Column Name</th><th>Type</th><th>Null</th><th>Default</th><th>Status</th></tr>';
        
        $required_columns = [
            'enable_making_charge' => 'tinyint(1)',
            'making_charge_type' => 'varchar(20)',
            'enable_wastage_charge' => 'tinyint(1)',
            'wastage_charge_type' => 'varchar(20)'
        ];
        
        $found_columns = [];
        foreach ($columns as $col) {
            $is_required = isset($required_columns[$col->Field]);
            $status = $is_required ? '<span class="good">✓ REQUIRED</span>' : '';
            
            echo '<tr>';
            echo '<td><strong>' . $col->Field . '</strong></td>';
            echo '<td>' . $col->Type . '</td>';
            echo '<td>' . $col->Null . '</td>';
            echo '<td>' . ($col->Default ?? 'NULL') . '</td>';
            echo '<td>' . $status . '</td>';
            echo '</tr>';
            
            if ($is_required) {
                $found_columns[$col->Field] = true;
            }
        }
        echo '</table>';
        
        // Check if all required columns exist
        $missing = [];
        foreach ($required_columns as $col => $type) {
            if (!isset($found_columns[$col])) {
                $missing[] = $col;
            }
        }
        
        if (empty($missing)) {
            echo '<div class="success">';
            echo '<h3>✅ All Required Columns Exist!</h3>';
            echo '<p>The database structure is correct.</p>';
            echo '</div>';
        } else {
            echo '<div class="error">';
            echo '<h3>❌ Missing Columns</h3>';
            echo '<ul>';
            foreach ($missing as $col) {
                echo '<li>' . $col . '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }
        
        // Now test the actual query that's failing
        echo '<h2>Testing JPC_Metals::get_all() Query</h2>';
        
        $query = "
            SELECT m.*, mg.name as group_name, mg.enable_making_charge, mg.enable_wastage_charge
            FROM {$wpdb->prefix}jpc_metals m
            LEFT JOIN {$wpdb->prefix}jpc_metal_groups mg ON m.metal_group_id = mg.id
            ORDER BY m.display_name ASC
            LIMIT 5
        ";
        
        echo '<div class="info">';
        echo '<h4>Query:</h4>';
        echo '<pre>' . $query . '</pre>';
        echo '</div>';
        
        $results = $wpdb->get_results($query);
        
        if ($wpdb->last_error) {
            echo '<div class="error">';
            echo '<h3>❌ Query Failed!</h3>';
            echo '<p><strong>Error:</strong> ' . $wpdb->last_error . '</p>';
            echo '</div>';
        } else {
            echo '<div class="success">';
            echo '<h3>✅ Query Successful!</h3>';
            echo '<p>Retrieved ' . count($results) . ' metals</p>';
            echo '</div>';
            
            if (!empty($results)) {
                echo '<h4>Sample Data:</h4>';
                echo '<table>';
                echo '<tr><th>ID</th><th>Name</th><th>Group</th><th>Enable Making</th><th>Enable Wastage</th></tr>';
                foreach ($results as $row) {
                    echo '<tr>';
                    echo '<td>' . $row->id . '</td>';
                    echo '<td>' . $row->display_name . '</td>';
                    echo '<td>' . ($row->group_name ?? 'N/A') . '</td>';
                    echo '<td>' . ($row->enable_making_charge ?? 'NULL') . '</td>';
                    echo '<td>' . ($row->enable_wastage_charge ?? 'NULL') . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            }
        }
        
        // Test if the plugin can load
        echo '<h2>Testing Plugin Classes</h2>';
        
        $classes_to_test = [
            'JPC_Metals' => 'get_all',
            'JPC_Metal_Groups' => 'get_all',
            'JPC_Admin' => null
        ];
        
        echo '<table>';
        echo '<tr><th>Class</th><th>Status</th><th>Test Result</th></tr>';
        
        foreach ($classes_to_test as $class => $method) {
            echo '<tr>';
            echo '<td><strong>' . $class . '</strong></td>';
            
            if (class_exists($class)) {
                echo '<td><span class="good">✓ Exists</span></td>';
                
                if ($method) {
                    try {
                        $result = call_user_func([$class, $method]);
                        echo '<td><span class="good">✓ ' . $method . '() works (' . count($result) . ' items)</span></td>';
                    } catch (Exception $e) {
                        echo '<td><span class="bad">✗ ' . $method . '() failed: ' . $e->getMessage() . '</span></td>';
                    }
                } else {
                    echo '<td>-</td>';
                }
            } else {
                echo '<td><span class="bad">✗ Missing</span></td>';
                echo '<td>-</td>';
            }
            
            echo '</tr>';
        }
        echo '</table>';
        
        // Final diagnosis
        echo '<h2>🎯 Diagnosis</h2>';
        
        if (empty($missing) && !$wpdb->last_error) {
            echo '<div class="success">';
            echo '<h3>✅ Database is CORRECT!</h3>';
            echo '<p>The database structure is fine. The issue must be elsewhere.</p>';
            echo '<p><strong>Possible causes:</strong></p>';
            echo '<ul>';
            echo '<li>Cached files (clear all caches)</li>';
            echo '<li>Old plugin files (make sure you uploaded the latest version)</li>';
            echo '<li>PHP errors in other parts of the code</li>';
            echo '</ul>';
            echo '<p><strong>Next steps:</strong></p>';
            echo '<ol>';
            echo '<li>Clear ALL caches (WordPress, browser, server)</li>';
            echo '<li>Deactivate and reactivate the plugin</li>';
            echo '<li>Try accessing the Metals page again</li>';
            echo '<li>Check browser console (F12) for JavaScript errors</li>';
            echo '</ol>';
            echo '</div>';
        } else {
            echo '<div class="error">';
            echo '<h3>❌ Issues Found</h3>';
            if (!empty($missing)) {
                echo '<p>Missing columns need to be added.</p>';
            }
            if ($wpdb->last_error) {
                echo '<p>Query is failing: ' . $wpdb->last_error . '</p>';
            }
            echo '</div>';
        }
        ?>
        
        <hr style="margin: 30px 0;">
        <p style="color: #666; font-size: 14px;">
            <strong>Delete this file after viewing!</strong>
        </p>
    </div>
</body>
</html>
