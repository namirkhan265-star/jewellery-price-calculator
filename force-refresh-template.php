<?php
/**
 * Force Refresh Template - Simple Cache Buster
 * 
 * This will:
 * 1. Touch the template file to update its timestamp
 * 2. Clear all WordPress caches
 * 3. Force browsers to reload
 */

// Load WordPress
require_once('../../../wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    die('ERROR: You must be logged in as an administrator.');
}

$template_file = __DIR__ . '/templates/frontend/price-breakup.php';

// Touch the file to update timestamp
if (file_exists($template_file)) {
    touch($template_file);
    $new_time = filemtime($template_file);
}

// Clear all caches
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
}

global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Template Refreshed</title>
    <meta http-equiv="refresh" content="3;url=<?php echo admin_url('edit.php?post_type=product'); ?>">
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 100px auto; padding: 40px; background: #f5f5f5; text-align: center; }
        .success { background: #d4edda; border: 3px solid #28a745; color: #155724; padding: 30px; border-radius: 10px; }
        h1 { color: #28a745; font-size: 32px; margin: 0 0 20px 0; }
        p { font-size: 18px; line-height: 1.6; }
        .button { background: #2271b1; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; text-decoration: none; display: inline-block; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="success">
        <h1>✅ Template Refreshed!</h1>
        <p><strong>Template file timestamp updated</strong></p>
        <p><strong>All caches cleared</strong></p>
        <p>Last modified: <?php echo date('Y-m-d H:i:s', $new_time); ?></p>
        <hr style="margin: 30px 0; border: 1px solid #28a745;">
        <h2>NOW DO THIS:</h2>
        <ol style="text-align: left; display: inline-block;">
            <li><strong>Press Ctrl+Shift+Delete</strong> (Windows) or <strong>Cmd+Shift+Delete</strong> (Mac)</li>
            <li>Select "Cached images and files"</li>
            <li>Click "Clear data"</li>
            <li>Go to any product page</li>
            <li>You should now see <strong>GST (3%)</strong></li>
        </ol>
        <p style="margin-top: 30px;"><em>Redirecting to products in 3 seconds...</em></p>
        <a href="<?php echo admin_url('edit.php?post_type=product'); ?>" class="button">Go to Products Now</a>
    </div>
</body>
</html>
<?php
// Delete this file after running
@unlink(__FILE__);
?>
