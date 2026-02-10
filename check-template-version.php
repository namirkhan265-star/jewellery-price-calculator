<?php
/**
 * Template Version Checker
 * Upload this to your WordPress root and access via: yoursite.com/check-template-version.php
 */

// Load WordPress
require_once('wp-load.php');

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Template Version Check</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #0073aa; padding-bottom: 10px; }
        .check { margin: 20px 0; padding: 15px; border-radius: 5px; }
        .success { background: #d4edda; border: 2px solid #28a745; color: #155724; }
        .error { background: #f8d7da; border: 2px solid #dc3545; color: #721c24; }
        .warning { background: #fff3cd; border: 2px solid #ffc107; color: #856404; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .file-path { font-size: 12px; color: #666; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Template Version Checker</h1>
        
        <?php
        $plugin_path = WP_PLUGIN_DIR . '/jewellery-price-calculator';
        $template1 = $plugin_path . '/templates/frontend/price-breakup.php';
        $template2 = $plugin_path . '/templates/frontend/detailed-breakup.php';
        
        // Check if files exist
        if (!file_exists($template1)) {
            echo '<div class="check error"><strong>❌ ERROR:</strong> price-breakup.php not found!</div>';
        } else {
            // Read file content
            $content1 = file_get_contents($template1);
            
            // Check for v2.5.2 marker
            if (strpos($content1, 'v2.5.2') !== false) {
                echo '<div class="check success"><strong>✅ SUCCESS:</strong> price-breakup.php is updated to v2.5.2</div>';
                
                // Check if it has the correct code
                if (strpos($content1, "get_option('jpc_pearl_cost_label'") !== false) {
                    echo '<div class="check success"><strong>✅ CORRECT:</strong> Template fetches labels from settings</div>';
                } else {
                    echo '<div class="check error"><strong>❌ ERROR:</strong> Template does NOT fetch labels from settings</div>';
                }
            } else {
                echo '<div class="check error"><strong>❌ OLD VERSION:</strong> price-breakup.php is NOT updated!</div>';
                echo '<div class="file-path">File path: ' . $template1 . '</div>';
                
                // Show what version it is
                if (strpos($content1, 'v2.5.1') !== false) {
                    echo '<div class="check warning">Current version: v2.5.1 (needs update to v2.5.2)</div>';
                } elseif (strpos($content1, 'v2.5.0') !== false) {
                    echo '<div class="check warning">Current version: v2.5.0 (needs update to v2.5.2)</div>';
                } else {
                    echo '<div class="check warning">Current version: Unknown (needs update to v2.5.2)</div>';
                }
            }
        }
        
        echo '<hr style="margin: 30px 0;">';
        
        if (!file_exists($template2)) {
            echo '<div class="check error"><strong>❌ ERROR:</strong> detailed-breakup.php not found!</div>';
        } else {
            // Read file content
            $content2 = file_get_contents($template2);
            
            // Check for v2.5.2 marker
            if (strpos($content2, 'v2.5.2') !== false) {
                echo '<div class="check success"><strong>✅ SUCCESS:</strong> detailed-breakup.php is updated to v2.5.2</div>';
                
                // Check if it has the correct code
                if (strpos($content2, "get_option('jpc_pearl_cost_label'") !== false) {
                    echo '<div class="check success"><strong>✅ CORRECT:</strong> Template fetches labels from settings</div>';
                } else {
                    echo '<div class="check error"><strong>❌ ERROR:</strong> Template does NOT fetch labels from settings</div>';
                }
            } else {
                echo '<div class="check error"><strong>❌ OLD VERSION:</strong> detailed-breakup.php is NOT updated!</div>';
                echo '<div class="file-path">File path: ' . $template2 . '</div>';
                
                // Show what version it is
                if (strpos($content2, 'v2.5.1') !== false) {
                    echo '<div class="check warning">Current version: v2.5.1 (needs update to v2.5.2)</div>';
                } elseif (strpos($content2, 'v2.5.0') !== false) {
                    echo '<div class="check warning">Current version: v2.5.0 (needs update to v2.5.2)</div>';
                } else {
                    echo '<div class="check warning">Current version: Unknown (needs update to v2.5.2)</div>';
                }
            }
        }
        
        echo '<hr style="margin: 30px 0;">';
        
        // Check if settings exist
        echo '<h2>Settings Check:</h2>';
        $pearl_label = get_option('jpc_pearl_cost_label', 'NOT SET');
        $stone_label = get_option('jpc_stone_cost_label', 'NOT SET');
        $extra_label = get_option('jpc_extra_fee_label', 'NOT SET');
        
        echo '<div class="check ' . ($pearl_label !== 'NOT SET' ? 'success' : 'warning') . '">';
        echo '<strong>Pearl Cost Label:</strong> ' . esc_html($pearl_label);
        echo '</div>';
        
        echo '<div class="check ' . ($stone_label !== 'NOT SET' ? 'success' : 'warning') . '">';
        echo '<strong>Stone Cost Label:</strong> ' . esc_html($stone_label);
        echo '</div>';
        
        echo '<div class="check ' . ($extra_label !== 'NOT SET' ? 'success' : 'warning') . '">';
        echo '<strong>Extra Fee Label:</strong> ' . esc_html($extra_label);
        echo '</div>';
        
        echo '<hr style="margin: 30px 0;">';
        
        echo '<h2>📋 Action Required:</h2>';
        echo '<div class="check warning">';
        echo '<ol>';
        echo '<li>Download the updated templates from GitHub</li>';
        echo '<li>Upload to: <code>/wp-content/plugins/jewellery-price-calculator/templates/frontend/</code></li>';
        echo '<li>Clear browser cache (Ctrl+Shift+R)</li>';
        echo '<li>Refresh this page to verify</li>';
        echo '<li>Check product page</li>';
        echo '</ol>';
        echo '</div>';
        
        echo '<div class="check">';
        echo '<strong>Download Links:</strong><br>';
        echo '<a href="https://raw.githubusercontent.com/namirkhan265-star/jewellery-price-calculator/main/templates/frontend/price-breakup.php" target="_blank">📥 Download price-breakup.php</a><br>';
        echo '<a href="https://raw.githubusercontent.com/namirkhan265-star/jewellery-price-calculator/main/templates/frontend/detailed-breakup.php" target="_blank">📥 Download detailed-breakup.php</a>';
        echo '</div>';
        ?>
    </div>
</body>
</html>
