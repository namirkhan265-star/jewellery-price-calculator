<?php
/**
 * AUTO-FIX SCRIPT v2.5.37
 * 
 * This script automatically adds the missing data-enable-making and data-enable-wastage
 * attributes to the metal dropdown in the product meta box.
 * 
 * HOW TO USE:
 * 1. Upload this file to your WordPress root directory
 * 2. Visit: https://yoursite.com/apply-fix-v2.5.37.php
 * 3. The script will automatically apply the fix
 * 4. DELETE THIS FILE after running
 */

// Security check
if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'yes') {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>JPC Fix v2.5.37</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
            .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            h1 { color: #d32f2f; }
            .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; }
            .info { background: #e7f3ff; border-left: 4px solid #2196f3; padding: 15px; margin: 20px 0; }
            .button { display: inline-block; background: #4caf50; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; font-weight: bold; }
            .button:hover { background: #45a049; }
            code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🔧 JPC Critical Fix v2.5.37</h1>
            
            <div class="warning">
                <strong>⚠️ WARNING:</strong> This script will modify your plugin files. Make sure you have a backup before proceeding.
            </div>
            
            <div class="info">
                <h3>What this fix does:</h3>
                <p>Adds missing <code>data-enable-making</code> and <code>data-enable-wastage</code> attributes to the metal dropdown in the product meta box.</p>
                
                <h3>Why you need this:</h3>
                <ul>
                    <li>Fixes critical errors on the Metals page</li>
                    <li>Fixes "Bulk Update All Prices" functionality</li>
                    <li>Fixes JavaScript errors in browser console</li>
                    <li>Enables proper show/hide of making charges and wastage fields</li>
                </ul>
                
                <h3>File to be modified:</h3>
                <p><code>wp-content/plugins/jewellery-price-calculator/includes/class-jpc-product-meta-box-v2.php</code></p>
            </div>
            
            <p><a href="?confirm=yes" class="button">✓ Apply Fix Now</a></p>
            
            <p style="color: #666; font-size: 14px;">After applying the fix, delete this file from your server.</p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Apply the fix
$file_path = __DIR__ . '/wp-content/plugins/jewellery-price-calculator/includes/class-jpc-product-meta-box-v2.php';

if (!file_exists($file_path)) {
    die('ERROR: File not found at: ' . $file_path);
}

// Read the file
$content = file_get_contents($file_path);

// Find and replace
$old_code = '                        <?php foreach ($metals as $metal): ?>
                            <option value="<?php echo esc_attr($metal->id); ?>" 
                                    data-price="<?php echo esc_attr($metal->price_per_unit); ?>"
                                    data-making-charges="<?php echo esc_attr($metal->making_charges_per_gram ?? 0); ?>"
                                    <?php selected($metal_id, $metal->id); ?>>';

$new_code = '                        <?php foreach ($metals as $metal): ?>
                            <option value="<?php echo esc_attr($metal->id); ?>" 
                                    data-price="<?php echo esc_attr($metal->price_per_unit); ?>"
                                    data-making-charges="<?php echo esc_attr($metal->making_charges_per_gram ?? 0); ?>"
                                    data-enable-making="<?php echo esc_attr($metal->enable_making_charge ?? 0); ?>"
                                    data-enable-wastage="<?php echo esc_attr($metal->enable_wastage_charge ?? 0); ?>"
                                    <?php selected($metal_id, $metal->id); ?>>';

if (strpos($content, $old_code) === false) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Fix Already Applied</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
            .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .success { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; color: #155724; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>✓ Fix Already Applied</h1>
            <div class="success">
                <p>The fix has already been applied to your installation, or the code structure has changed.</p>
                <p><strong>You can safely delete this file now.</strong></p>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Apply the fix
$content = str_replace($old_code, $new_code, $content);

// Write back to file
$result = file_put_contents($file_path, $content);

if ($result === false) {
    die('ERROR: Could not write to file. Check file permissions.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Applied Successfully</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; color: #155724; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✓ Fix Applied Successfully!</h1>
        
        <div class="success">
            <h3>The fix has been applied successfully!</h3>
            <p>File modified: <code><?php echo $file_path; ?></code></p>
            <p>Added attributes:</p>
            <ul>
                <li><code>data-enable-making</code></li>
                <li><code>data-enable-wastage</code></li>
            </ul>
        </div>
        
        <div class="warning">
            <h3>⚠️ IMPORTANT: Next Steps</h3>
            <ol>
                <li><strong>Clear all caches</strong> (WordPress, browser, server)</li>
                <li><strong>Test the Metals page</strong> - should load without errors</li>
                <li><strong>Test "Bulk Update All Prices"</strong> - should work</li>
                <li><strong>Test product edit page</strong> - should load without errors</li>
                <li><strong>DELETE THIS FILE</strong> from your server immediately</li>
            </ol>
        </div>
        
        <p style="color: #666; font-size: 14px;">
            If you still see errors, try clearing your browser cache with Ctrl+Shift+Delete (Windows) or Cmd+Shift+Delete (Mac).
        </p>
    </div>
</body>
</html>
