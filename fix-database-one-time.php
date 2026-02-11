<?php
/**
 * ONE-TIME DATABASE FIX SCRIPT
 * 
 * This script fixes existing metal groups that have making/wastage charges disabled
 * 
 * HOW TO USE:
 * 1. Upload this file to: wp-content/plugins/jewellery-price-calculator/
 * 2. Access it via browser: https://yoursite.com/wp-content/plugins/jewellery-price-calculator/fix-database-one-time.php
 * 3. You should see "SUCCESS" message
 * 4. DELETE this file after running (for security)
 * 
 * WHAT IT DOES:
 * - Updates all metal groups to enable making_charge and wastage_charge
 * - Shows before/after comparison
 */

// Load WordPress
require_once('../../../wp-load.php');

// Security check - only admins can run this
if (!current_user_can('manage_options')) {
    die('ERROR: You must be logged in as administrator to run this script.');
}

global $wpdb;
$table = $wpdb->prefix . 'jpc_metal_groups';

echo "<h1>Metal Groups Database Fix</h1>";
echo "<hr>";

// Show BEFORE state
echo "<h2>BEFORE Fix:</h2>";
$before = $wpdb->get_results("SELECT id, name, unit, enable_making_charge, enable_wastage_charge FROM $table");
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Name</th><th>Unit</th><th>Making Charge</th><th>Wastage Charge</th></tr>";
foreach ($before as $row) {
    $making = $row->enable_making_charge ? '✓ Enabled' : '✗ Disabled';
    $wastage = $row->enable_wastage_charge ? '✓ Enabled' : '✗ Disabled';
    $making_color = $row->enable_making_charge ? 'green' : 'red';
    $wastage_color = $row->enable_wastage_charge ? 'green' : 'red';
    
    echo "<tr>";
    echo "<td>{$row->id}</td>";
    echo "<td><strong>{$row->name}</strong></td>";
    echo "<td>{$row->unit}</td>";
    echo "<td style='color: $making_color;'>$making</td>";
    echo "<td style='color: $wastage_color;'>$wastage</td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr>";
echo "<h2>Running Fix...</h2>";

// Run the fix
$result = $wpdb->query("
    UPDATE $table 
    SET 
        enable_making_charge = 1,
        enable_wastage_charge = 1
    WHERE 
        enable_making_charge = 0 
        OR enable_wastage_charge = 0
");

if ($result === false) {
    echo "<p style='color: red;'><strong>ERROR:</strong> " . $wpdb->last_error . "</p>";
} else {
    echo "<p style='color: green;'><strong>SUCCESS:</strong> Updated $result row(s)</p>";
}

echo "<hr>";

// Show AFTER state
echo "<h2>AFTER Fix:</h2>";
$after = $wpdb->get_results("SELECT id, name, unit, enable_making_charge, enable_wastage_charge FROM $table");
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Name</th><th>Unit</th><th>Making Charge</th><th>Wastage Charge</th></tr>";
foreach ($after as $row) {
    $making = $row->enable_making_charge ? '✓ Enabled' : '✗ Disabled';
    $wastage = $row->enable_wastage_charge ? '✓ Enabled' : '✗ Disabled';
    $making_color = $row->enable_making_charge ? 'green' : 'red';
    $wastage_color = $row->enable_wastage_charge ? 'green' : 'red';
    
    echo "<tr>";
    echo "<td>{$row->id}</td>";
    echo "<td><strong>{$row->name}</strong></td>";
    echo "<td>{$row->unit}</td>";
    echo "<td style='color: $making_color;'>$making</td>";
    echo "<td style='color: $wastage_color;'>$wastage</td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr>";
echo "<h2 style='color: green;'>✓ FIX COMPLETE!</h2>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>Go to <strong>Jewellery Calculator → Metal Groups</strong></li>";
echo "<li>Verify that all groups now show <span style='color: green;'>✓ Per Gram</span> and <span style='color: green;'>✓ Percentage</span></li>";
echo "<li><strong style='color: red;'>DELETE THIS FILE (fix-database-one-time.php) for security!</strong></li>";
echo "</ol>";
?>
