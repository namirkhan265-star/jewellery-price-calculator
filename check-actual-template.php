<?php
/**
 * Check Actual Template File on Server
 * 
 * This will show you the ACTUAL content of the template file
 * that's currently on your server.
 */

// Load WordPress
require_once('../../../wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    die('ERROR: You must be logged in as an administrator.');
}

$template_file = __DIR__ . '/templates/frontend/price-breakup.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Check Template File</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 5px; max-width: 1400px; margin: 0 auto; }
        h1 { color: #2271b1; }
        .info { background: #d1ecf1; border: 2px solid #17a2b8; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .success { background: #d4edda; border: 2px solid #28a745; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .error { background: #f8d7da; border: 2px solid #dc3545; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; }
        pre { background: #282c34; color: #abb2bf; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 12px; line-height: 1.4; }
        .line-numbers { background: #21252b; color: #5c6370; padding: 15px 10px; border-radius: 5px 0 0 5px; float: left; margin-right: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        table td { padding: 8px; border: 1px solid #ddd; }
        table td:first-child { background: #f0f0f0; font-weight: bold; width: 30%; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Actual Template File Check</h1>
        
        <?php
        if (file_exists($template_file)) {
            echo '<div class="info">';
            echo '<h2>📁 File Information</h2>';
            echo '<table>';
            echo '<tr><td>File Path</td><td>' . $template_file . '</td></tr>';
            echo '<tr><td>File Size</td><td>' . filesize($template_file) . ' bytes</td></tr>';
            echo '<tr><td>Last Modified</td><td>' . date('Y-m-d H:i:s', filemtime($template_file)) . '</td></tr>';
            echo '<tr><td>Readable</td><td>' . (is_readable($template_file) ? '✅ YES' : '❌ NO') . '</td></tr>';
            echo '<tr><td>Writable</td><td>' . (is_writable($template_file) ? '✅ YES' : '❌ NO') . '</td></tr>';
            echo '</table>';
            echo '</div>';
            
            // Read the file
            $content = file_get_contents($template_file);
            $lines = explode("\n", $content);
            
            // Check for key fixes
            $has_dynamic_gst = strpos($content, 'get_option(\'jpc_gst_label\'') !== false;
            $has_isset_diamond = strpos($content, 'isset($breakup[\'diamond_price\'])') !== false;
            $has_gst_display = strpos($content, '$gst_display') !== false;
            
            echo '<div class="' . ($has_dynamic_gst && $has_isset_diamond && $has_gst_display ? 'success' : 'error') . '">';
            echo '<h2>🔍 Fix Status Check</h2>';
            echo '<table>';
            echo '<tr><td>Has Dynamic GST Label</td><td>' . ($has_dynamic_gst ? '✅ YES' : '❌ NO') . '</td></tr>';
            echo '<tr><td>Has isset Diamond Check</td><td>' . ($has_isset_diamond ? '✅ YES' : '❌ NO') . '</td></tr>';
            echo '<tr><td>Has GST Display Logic</td><td>' . ($has_gst_display ? '✅ YES' : '❌ NO') . '</td></tr>';
            echo '</table>';
            
            if (!$has_dynamic_gst || !$has_isset_diamond || !$has_gst_display) {
                echo '<p style="color: #dc3545; font-weight: bold;">❌ TEMPLATE FILE IS NOT UPDATED!</p>';
                echo '<p>The file on your server does NOT have the fixes.</p>';
            } else {
                echo '<p style="color: #28a745; font-weight: bold;">✅ TEMPLATE FILE HAS ALL FIXES!</p>';
                echo '<p>The file is correct. The issue must be elsewhere.</p>';
            }
            echo '</div>';
            
            // Show GST section specifically
            echo '<div class="info">';
            echo '<h2>📄 GST Section (Lines 145-175)</h2>';
            echo '<pre>';
            for ($i = 144; $i < 175 && $i < count($lines); $i++) {
                echo sprintf("%3d: %s\n", $i + 1, htmlspecialchars($lines[$i]));
            }
            echo '</pre>';
            echo '</div>';
            
            // Show Diamond section specifically
            echo '<div class="info">';
            echo '<h2>📄 Diamond Section (Lines 55-65)</h2>';
            echo '<pre>';
            for ($i = 54; $i < 65 && $i < count($lines); $i++) {
                echo sprintf("%3d: %s\n", $i + 1, htmlspecialchars($lines[$i]));
            }
            echo '</pre>';
            echo '</div>';
            
            // Show first 100 lines
            echo '<div class="info">';
            echo '<h2>📄 Full File Content (First 100 Lines)</h2>';
            echo '<pre>';
            for ($i = 0; $i < 100 && $i < count($lines); $i++) {
                echo sprintf("%3d: %s\n", $i + 1, htmlspecialchars($lines[$i]));
            }
            echo '</pre>';
            echo '</div>';
            
        } else {
            echo '<div class="error">';
            echo '<h2>❌ File Not Found!</h2>';
            echo '<p>Template file does not exist at:</p>';
            echo '<p><code>' . $template_file . '</code></p>';
            echo '</div>';
        }
        ?>
        
        <div class="info">
            <h2>🔧 What to Do Next</h2>
            <ol>
                <li>Check if "Fix Status Check" shows all ✅ YES</li>
                <li>If NO, the file wasn't updated - we need to upload it manually</li>
                <li>If YES, the file is correct - issue is somewhere else</li>
                <li>Send me a screenshot of this page</li>
            </ol>
        </div>
    </div>
</body>
</html>
