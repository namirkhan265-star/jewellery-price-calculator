<?php
/**
 * PHP Syntax Checker
 * Upload this to your plugin folder and run it to find syntax errors
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo '<h1>PHP Syntax Checker</h1>';
echo '<p>Checking all PHP files in the plugin...</p>';

$plugin_dir = __DIR__;
$files_to_check = array(
    'jewellery-price-calculator.php',
    'includes/class-jpc-database.php',
    'includes/class-jpc-metals.php',
    'includes/class-jpc-diamonds.php',
    'includes/class-jpc-diamond-groups.php',
    'includes/class-jpc-diamond-types.php',
    'includes/class-jpc-diamond-certifications.php',
    'includes/class-jpc-diamond-pricing.php',
    'includes/class-jpc-metal-groups.php',
    'includes/class-jpc-price-calculator.php',
    'includes/class-jpc-product-meta.php',
    'includes/class-jpc-frontend.php',
    'includes/class-jpc-shortcodes.php',
    'includes/class-jpc-bulk-import-export.php',
    'includes/class-jpc-admin.php'
);

$errors = array();
$success = array();

foreach ($files_to_check as $file) {
    $filepath = $plugin_dir . '/' . $file;
    
    if (!file_exists($filepath)) {
        $errors[] = array(
            'file' => $file,
            'error' => 'File not found'
        );
        continue;
    }
    
    // Check syntax using php -l
    $output = array();
    $return_var = 0;
    exec("php -l " . escapeshellarg($filepath) . " 2>&1", $output, $return_var);
    
    if ($return_var !== 0) {
        $errors[] = array(
            'file' => $file,
            'error' => implode("\n", $output)
        );
    } else {
        $success[] = $file;
    }
}

// Display results
if (!empty($errors)) {
    echo '<h2 style="color: red;">❌ Syntax Errors Found:</h2>';
    echo '<div style="background: #fee; padding: 15px; border: 2px solid red; margin: 10px 0;">';
    foreach ($errors as $error) {
        echo '<h3>' . htmlspecialchars($error['file']) . '</h3>';
        echo '<pre style="background: white; padding: 10px; overflow: auto;">' . htmlspecialchars($error['error']) . '</pre>';
    }
    echo '</div>';
} else {
    echo '<h2 style="color: green;">✅ No Syntax Errors!</h2>';
}

if (!empty($success)) {
    echo '<h3>Files Checked Successfully:</h3>';
    echo '<ul>';
    foreach ($success as $file) {
        echo '<li style="color: green;">✓ ' . htmlspecialchars($file) . '</li>';
    }
    echo '</ul>';
}

echo '<hr>';
echo '<p style="color: red; font-weight: bold;">⚠️ DELETE THIS FILE AFTER CHECKING!</p>';
?>
