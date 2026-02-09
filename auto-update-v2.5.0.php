<?php
/**
 * Auto-Update Script for v2.5.0 Custom Labels Feature
 * 
 * INSTRUCTIONS:
 * 1. Upload this file to your WordPress root directory
 * 2. Access it via: https://yoursite.com/auto-update-v2.5.0.php
 * 3. Click "Update Now" button
 * 4. Wait for completion
 * 5. DELETE this file after update for security
 * 
 * SECURITY: This file will only work for administrators
 */

// Load WordPress
require_once('wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    die('Access denied. You must be an administrator.');
}

// GitHub raw file URLs
$files_to_update = array(
    'includes/class-jpc-price-calculator.php' => 'https://raw.githubusercontent.com/namirkhan265-star/jewellery-price-calculator/main/includes/class-jpc-price-calculator.php',
    'includes/class-jpc-admin.php' => 'https://raw.githubusercontent.com/namirkhan265-star/jewellery-price-calculator/main/includes/class-jpc-admin.php',
    'templates/frontend/price-breakup.php' => 'https://raw.githubusercontent.com/namirkhan265-star/jewellery-price-calculator/main/templates/frontend/price-breakup.php',
    'templates/frontend/detailed-breakup.php' => 'https://raw.githubusercontent.com/namirkhan265-star/jewellery-price-calculator/main/templates/frontend/detailed-breakup.php',
    'templates/admin/general-settings.php' => 'https://raw.githubusercontent.com/namirkhan265-star/jewellery-price-calculator/main/templates/admin/general-settings.php',
);

// Handle update request
$update_result = null;
if (isset($_POST['do_update']) && wp_verify_nonce($_POST['_wpnonce'], 'jpc_auto_update')) {
    $update_result = array(
        'success' => array(),
        'failed' => array(),
        'skipped' => array(),
    );
    
    $plugin_dir = WP_PLUGIN_DIR . '/jewellery-price-calculator/';
    
    // Check if plugin directory exists
    if (!is_dir($plugin_dir)) {
        $update_result['error'] = 'Plugin directory not found: ' . $plugin_dir;
    } else {
        foreach ($files_to_update as $local_path => $github_url) {
            $full_path = $plugin_dir . $local_path;
            $dir = dirname($full_path);
            
            // Create directory if it doesn't exist
            if (!is_dir($dir)) {
                if (!mkdir($dir, 0755, true)) {
                    $update_result['failed'][$local_path] = 'Could not create directory: ' . $dir;
                    continue;
                }
            }
            
            // Backup existing file
            if (file_exists($full_path)) {
                $backup_path = $full_path . '.backup-' . date('Y-m-d-H-i-s');
                if (!copy($full_path, $backup_path)) {
                    $update_result['failed'][$local_path] = 'Could not create backup';
                    continue;
                }
            }
            
            // Download new file
            $new_content = @file_get_contents($github_url);
            
            if ($new_content === false) {
                $update_result['failed'][$local_path] = 'Could not download from GitHub';
                continue;
            }
            
            // Write new file
            if (file_put_contents($full_path, $new_content) === false) {
                $update_result['failed'][$local_path] = 'Could not write file';
                continue;
            }
            
            $update_result['success'][$local_path] = 'Updated successfully';
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Auto-Update v2.5.0 - Custom Labels</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #0073aa; padding-bottom: 10px; }
        h2 { color: #0073aa; margin-top: 30px; }
        .status { padding: 15px; margin: 15px 0; border-radius: 4px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .warning { background: #fff3cd; border: 1px solid #ffc107; color: #856404; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        .btn { display: inline-block; padding: 12px 24px; background: #0073aa; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn:hover { background: #005a87; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #0073aa; color: white; font-weight: bold; }
        tr:hover { background: #f5f5f5; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #28a745; color: white; }
        .badge-error { background: #dc3545; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
        ul { line-height: 1.8; }
        .step { background: #f8f9fa; padding: 15px; margin: 10px 0; border-left: 4px solid #0073aa; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Auto-Update v2.5.0 - Custom Labels Feature</h1>
        <p><strong>Date:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        
        <?php if ($update_result === null): ?>
            
            <div class="status info">
                <h3>📋 What This Script Will Do:</h3>
                <ul>
                    <li>✅ Download latest files from GitHub</li>
                    <li>✅ Backup your current files (with timestamp)</li>
                    <li>✅ Replace 5 critical files for custom labels feature</li>
                    <li>✅ Preserve all your settings and data</li>
                </ul>
            </div>
            
            <h2>Files to be Updated:</h2>
            <table>
                <tr>
                    <th>#</th>
                    <th>File Path</th>
                    <th>Current Status</th>
                </tr>
                <?php 
                $i = 1;
                $plugin_dir = WP_PLUGIN_DIR . '/jewellery-price-calculator/';
                foreach ($files_to_update as $local_path => $github_url): 
                    $full_path = $plugin_dir . $local_path;
                    $exists = file_exists($full_path);
                ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><code><?php echo esc_html($local_path); ?></code></td>
                    <td>
                        <?php if ($exists): ?>
                            <span class="badge badge-success">EXISTS</span>
                        <?php else: ?>
                            <span class="badge badge-warning">NEW FILE</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            
            <div class="status warning">
                <h3>⚠️ Before You Continue:</h3>
                <ul>
                    <li><strong>Backup Recommended:</strong> This script creates automatic backups, but a full site backup is recommended</li>
                    <li><strong>Plugin Active:</strong> Make sure the Jewellery Price Calculator plugin is active</li>
                    <li><strong>Write Permissions:</strong> Plugin directory must be writable</li>
                </ul>
            </div>
            
            <form method="post" onsubmit="return confirm('Are you sure you want to update the plugin files? Backups will be created automatically.');">
                <?php wp_nonce_field('jpc_auto_update'); ?>
                <input type="hidden" name="do_update" value="1">
                <button type="submit" class="btn">🚀 Update Now</button>
            </form>
            
        <?php else: ?>
            
            <?php if (isset($update_result['error'])): ?>
                <div class="status error">
                    <h3>❌ Critical Error</h3>
                    <p><?php echo esc_html($update_result['error']); ?></p>
                </div>
            <?php else: ?>
                
                <?php if (!empty($update_result['success'])): ?>
                    <div class="status success">
                        <h3>✅ Successfully Updated Files</h3>
                        <table>
                            <tr>
                                <th>File</th>
                                <th>Status</th>
                            </tr>
                            <?php foreach ($update_result['success'] as $file => $message): ?>
                            <tr>
                                <td><code><?php echo esc_html($file); ?></code></td>
                                <td><span class="badge badge-success"><?php echo esc_html($message); ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($update_result['failed'])): ?>
                    <div class="status error">
                        <h3>❌ Failed to Update</h3>
                        <table>
                            <tr>
                                <th>File</th>
                                <th>Error</th>
                            </tr>
                            <?php foreach ($update_result['failed'] as $file => $error): ?>
                            <tr>
                                <td><code><?php echo esc_html($file); ?></code></td>
                                <td><span class="badge badge-error"><?php echo esc_html($error); ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                <?php endif; ?>
                
                <?php if (empty($update_result['failed'])): ?>
                    <div class="status success">
                        <h3>🎉 Update Complete!</h3>
                        <p><strong>All files have been updated successfully.</strong></p>
                    </div>
                    
                    <h2>📋 Next Steps:</h2>
                    
                    <div class="step">
                        <h3>Step 1: Regenerate All Product Prices</h3>
                        <p>You MUST regenerate prices for the custom labels to appear:</p>
                        <ol>
                            <li>Go to <strong>Jewellery Price > General</strong></li>
                            <li>Scroll to the bottom of the page</li>
                            <li>Click the <strong>"Update All Prices"</strong> button</li>
                            <li>Wait for the completion message</li>
                        </ol>
                        <a href="<?php echo admin_url('admin.php?page=jewellery-price-calc'); ?>" class="btn">Go to General Settings</a>
                    </div>
                    
                    <div class="step">
                        <h3>Step 2: Verify Custom Labels</h3>
                        <p>Check that your custom labels are working:</p>
                        <ol>
                            <li>View any product on the frontend</li>
                            <li>Look at the price breakup section</li>
                            <li>You should see your custom labels:
                                <ul>
                                    <li><strong>"Test 6"</strong> instead of "Pearl Cost"</li>
                                    <li><strong>"Test 7"</strong> instead of "Stone Cost"</li>
                                    <li><strong>"Test 8"</strong> instead of "Extra Fee"</li>
                                </ul>
                            </li>
                        </ol>
                    </div>
                    
                    <div class="step">
                        <h3>Step 3: Clear Cache</h3>
                        <p>If labels don't appear immediately:</p>
                        <ul>
                            <li>Clear your browser cache (Ctrl+Shift+R or Cmd+Shift+R)</li>
                            <li>Clear WordPress cache (if using a caching plugin)</li>
                            <li>Clear WooCommerce transients</li>
                        </ul>
                    </div>
                    
                    <div class="step">
                        <h3>Step 4: Delete This File</h3>
                        <p><strong>IMPORTANT:</strong> For security, delete this file after use:</p>
                        <ul>
                            <li>File to delete: <code>auto-update-v2.5.0.php</code></li>
                            <li>Location: WordPress root directory</li>
                        </ul>
                    </div>
                    
                    <div class="status info">
                        <h3>📁 Backup Files Created</h3>
                        <p>Your original files have been backed up with timestamp. You can find them in the same directories with <code>.backup-YYYY-MM-DD-HH-MM-SS</code> extension.</p>
                        <p>If something goes wrong, you can restore by removing the <code>.backup-*</code> extension.</p>
                    </div>
                    
                <?php else: ?>
                    <div class="status warning">
                        <h3>⚠️ Partial Update</h3>
                        <p>Some files were updated successfully, but others failed. Please check the errors above and try updating the failed files manually.</p>
                        <a href="<?php echo admin_url('admin.php?page=jewellery-price-calc'); ?>" class="btn">Go to Settings</a>
                    </div>
                <?php endif; ?>
                
            <?php endif; ?>
            
        <?php endif; ?>
        
        <hr style="margin: 40px 0;">
        <p style="color: #999; font-size: 12px;">
            <strong>Security Note:</strong> Please delete this file (auto-update-v2.5.0.php) after use.<br>
            <strong>Support:</strong> If you encounter any issues, check the diagnostic tool or contact support.
        </p>
    </div>
</body>
</html>
