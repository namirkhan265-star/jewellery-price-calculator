<?php
/**
 * Apply Fixed Price Breakup Template
 * 
 * This script replaces the old price-breakup.php with the fixed version
 * that dynamically fetches GST label/percentage and shows diamond prices.
 * 
 * INSTRUCTIONS:
 * 1. Upload to: /wp-content/plugins/jewellery-price-calculator-main/
 * 2. Visit: https://detailx.co.in/wp-content/plugins/jewellery-price-calculator-main/apply-template-fix.php
 * 3. Click button
 * 4. Delete this file
 */

// Load WordPress
require_once('../../../wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    die('ERROR: You must be logged in as an administrator.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Apply Template Fix</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2271b1; border-bottom: 3px solid #2271b1; padding-bottom: 10px; }
        .success { background: #d4edda; border: 2px solid #28a745; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .info { background: #d1ecf1; border: 2px solid #17a2b8; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .warning { background: #fff3cd; border: 2px solid #ffc107; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .error { background: #f8d7da; border: 2px solid #dc3545; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .button { background: #2271b1; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; text-decoration: none; display: inline-block; }
        .button:hover { background: #135e96; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        ul { line-height: 1.8; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Apply Fixed Price Breakup Template</h1>
        
        <?php
        if (isset($_GET['run'])) {
            // RUN THE FIX
            echo '<div class="info"><strong>Applying template fix...</strong></div>';
            
            $plugin_dir = WP_PLUGIN_DIR . '/jewellery-price-calculator-main';
            $template_dir = $plugin_dir . '/templates/frontend';
            $old_file = $template_dir . '/price-breakup.php';
            $new_file = $template_dir . '/price-breakup-FIXED.php';
            $backup_file = $template_dir . '/price-breakup-BACKUP-' . date('Y-m-d-His') . '.php';
            
            // Check if files exist
            if (!file_exists($new_file)) {
                echo '<div class="error">';
                echo '<h3>❌ Error: Fixed template not found!</h3>';
                echo '<p>Please make sure <code>price-breakup-FIXED.php</code> exists in:</p>';
                echo '<p><code>' . $template_dir . '</code></p>';
                echo '</div>';
            } else {
                // Backup old file
                if (file_exists($old_file)) {
                    if (copy($old_file, $backup_file)) {
                        echo '<p>✅ Backed up old template to: <code>' . basename($backup_file) . '</code></p>';
                    }
                }
                
                // Copy fixed file over old file
                if (copy($new_file, $old_file)) {
                    echo '<div class="success">';
                    echo '<h2>✅ SUCCESS!</h2>';
                    echo '<p><strong>Template has been updated successfully!</strong></p>';
                    echo '<h3>What Was Fixed:</h3>';
                    echo '<ul>';
                    echo '<li>✅ GST label now fetched dynamically from settings</li>';
                    echo '<li>✅ GST percentage now fetched dynamically from settings</li>';
                    echo '<li>✅ GST shows as "GST (3%)" instead of "GST (3.00%)"</li>';
                    echo '<li>✅ Diamond price now always shows if it exists</li>';
                    echo '<li>✅ All labels fetched from settings in real-time</li>';
                    echo '</ul>';
                    echo '<h3>Next Steps:</h3>';
                    echo '<ol>';
                    echo '<li>Clear your website cache (if using caching plugin)</li>';
                    echo '<li>Go to any product on your website</li>';
                    echo '<li>Click "Price Breakup" tab</li>';
                    echo '<li>You should now see:</li>';
                    echo '<ul>';
                    echo '<li>Diamond price (if product has diamonds)</li>';
                    echo '<li>GST (3%) with percentage</li>';
                    echo '<li>Custom GST label if you changed it</li>';
                    echo '</ul>';
                    echo '<li><strong>IMPORTANT:</strong> Delete this file from your server</li>';
                    echo '</ol>';
                    echo '<p><strong>Backup saved as:</strong> <code>' . basename($backup_file) . '</code></p>';
                    echo '<p>If anything goes wrong, you can restore from the backup.</p>';
                    echo '</div>';
                } else {
                    echo '<div class="error">';
                    echo '<h3>❌ Error: Could not copy file!</h3>';
                    echo '<p>Please check file permissions on:</p>';
                    echo '<p><code>' . $template_dir . '</code></p>';
                    echo '</div>';
                }
            }
            
        } else {
            // SHOW INFORMATION
            echo '<div class="info">';
            echo '<h2>📋 What This Will Do</h2>';
            echo '<p>This script will replace your current price breakup template with a fixed version that:</p>';
            echo '<ul>';
            echo '<li><strong>Fetches GST label dynamically</strong> from settings (not from stored breakup)</li>';
            echo '<li><strong>Fetches GST percentage dynamically</strong> from settings</li>';
            echo '<li><strong>Shows GST as "GST (3%)"</strong> instead of "GST (3.00%)"</li>';
            echo '<li><strong>Always shows diamond price</strong> if it exists in breakup</li>';
            echo '<li><strong>Fetches all labels</strong> from settings in real-time</li>';
            echo '</ul>';
            echo '</div>';
            
            echo '<div class="warning">';
            echo '<h3>⚠️ Before You Start</h3>';
            echo '<ul>';
            echo '<li>A backup of your current template will be created automatically</li>';
            echo '<li>Safe to run - only updates the display template</li>';
            echo '<li>Does NOT affect calculations or stored data</li>';
            echo '</ul>';
            echo '</div>';
            
            echo '<div style="text-align: center; margin: 30px 0;">';
            echo '<a href="?run=1" class="button">🚀 Apply Template Fix Now</a>';
            echo '</div>';
            
            echo '<div class="info">';
            echo '<h3>Why This Fix is Different:</h3>';
            echo '<p>Previous fixes regenerated the <strong>data</strong>, but the <strong>display template</strong> was still using old code.</p>';
            echo '<p>This fix updates the <strong>display template</strong> to fetch GST settings dynamically, so:</p>';
            echo '<ul>';
            echo '<li>If you change GST label in settings → it updates immediately</li>';
            echo '<li>If you change GST percentage → it updates immediately</li>';
            echo '<li>No need to regenerate breakups when you change settings</li>';
            echo '</ul>';
            echo '</div>';
        }
        ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 14px;">
            <p><strong>Technical Details:</strong></p>
            <p>This replaces: <code>templates/frontend/price-breakup.php</code></p>
            <p>With: <code>templates/frontend/price-breakup-FIXED.php</code></p>
        </div>
    </div>
</body>
</html>
