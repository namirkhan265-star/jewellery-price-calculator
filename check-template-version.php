<?php
/**
 * Enhanced Template Version Checker with Live Test
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
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #0073aa; padding-bottom: 10px; }
        h2 { color: #0073aa; margin-top: 30px; }
        .check { margin: 20px 0; padding: 15px; border-radius: 5px; }
        .success { background: #d4edda; border: 2px solid #28a745; color: #155724; }
        .error { background: #f8d7da; border: 2px solid #dc3545; color: #721c24; }
        .warning { background: #fff3cd; border: 2px solid #ffc107; color: #856404; }
        .info { background: #d1ecf1; border: 2px solid #17a2b8; color: #0c5460; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 12px; }
        .file-path { font-size: 12px; color: #666; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table th, table td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        table th { background: #f8f9fa; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Enhanced Template Version Checker</h1>
        
        <?php
        $plugin_path = WP_PLUGIN_DIR . '/jewellery-price-calculator';
        $template1 = $plugin_path . '/templates/frontend/price-breakup.php';
        $template2 = $plugin_path . '/templates/frontend/detailed-breakup.php';
        
        echo '<h2>📁 File Existence Check:</h2>';
        
        // Check if files exist
        if (!file_exists($template1)) {
            echo '<div class="check error"><strong>❌ ERROR:</strong> price-breakup.php not found!</div>';
            echo '<div class="file-path">Expected path: ' . $template1 . '</div>';
        } else {
            echo '<div class="check success"><strong>✅ FOUND:</strong> price-breakup.php exists</div>';
            echo '<div class="file-path">Path: ' . $template1 . '</div>';
            
            // Read file content
            $content1 = file_get_contents($template1);
            
            // Check for v2.5.2 marker
            if (strpos($content1, 'v2.5.2') !== false) {
                echo '<div class="check success"><strong>✅ VERSION:</strong> price-breakup.php is v2.5.2</div>';
            } else {
                echo '<div class="check error"><strong>❌ OLD VERSION:</strong> price-breakup.php is NOT v2.5.2!</div>';
                
                // Show what version it is
                if (strpos($content1, 'v2.5.1') !== false) {
                    echo '<div class="check warning">Current version: v2.5.1</div>';
                } elseif (strpos($content1, 'v2.5.0') !== false) {
                    echo '<div class="check warning">Current version: v2.5.0</div>';
                } else {
                    echo '<div class="check warning">Current version: Unknown/Old</div>';
                }
            }
            
            // Check if it has the correct code
            if (strpos($content1, "get_option('jpc_pearl_cost_label'") !== false) {
                echo '<div class="check success"><strong>✅ CODE CHECK:</strong> Template fetches labels from settings correctly</div>';
            } else {
                echo '<div class="check error"><strong>❌ CODE ERROR:</strong> Template does NOT fetch labels from settings!</div>';
                
                // Show what it's using instead
                if (strpos($content1, "breakup['pearl_cost_label']") !== false) {
                    echo '<div class="check warning">Template is using OLD method: breakup data</div>';
                } else {
                    echo '<div class="check warning">Template is using UNKNOWN method</div>';
                }
            }
            
            // Show first 20 lines of the file
            echo '<details style="margin-top: 15px;"><summary style="cursor: pointer; font-weight: 600;">📄 View First 20 Lines of File</summary>';
            $lines = explode("\n", $content1);
            echo '<pre>' . esc_html(implode("\n", array_slice($lines, 0, 20))) . '</pre>';
            echo '</details>';
        }
        
        echo '<hr style="margin: 30px 0;">';
        
        if (!file_exists($template2)) {
            echo '<div class="check error"><strong>❌ ERROR:</strong> detailed-breakup.php not found!</div>';
            echo '<div class="file-path">Expected path: ' . $template2 . '</div>';
        } else {
            echo '<div class="check success"><strong>✅ FOUND:</strong> detailed-breakup.php exists</div>';
            echo '<div class="file-path">Path: ' . $template2 . '</div>';
            
            // Read file content
            $content2 = file_get_contents($template2);
            
            // Check for v2.5.2 marker
            if (strpos($content2, 'v2.5.2') !== false) {
                echo '<div class="check success"><strong>✅ VERSION:</strong> detailed-breakup.php is v2.5.2</div>';
            } else {
                echo '<div class="check error"><strong>❌ OLD VERSION:</strong> detailed-breakup.php is NOT v2.5.2!</div>';
                
                // Show what version it is
                if (strpos($content2, 'v2.5.1') !== false) {
                    echo '<div class="check warning">Current version: v2.5.1</div>';
                } elseif (strpos($content2, 'v2.5.0') !== false) {
                    echo '<div class="check warning">Current version: v2.5.0</div>';
                } else {
                    echo '<div class="check warning">Current version: Unknown/Old</div>';
                }
            }
            
            // Check if it has the correct code
            if (strpos($content2, "get_option('jpc_pearl_cost_label'") !== false) {
                echo '<div class="check success"><strong>✅ CODE CHECK:</strong> Template fetches labels from settings correctly</div>';
            } else {
                echo '<div class="check error"><strong>❌ CODE ERROR:</strong> Template does NOT fetch labels from settings!</div>';
            }
        }
        
        echo '<hr style="margin: 30px 0;">';
        
        // Check if settings exist
        echo '<h2>⚙️ Settings Check:</h2>';
        $pearl_label = get_option('jpc_pearl_cost_label', 'NOT SET');
        $stone_label = get_option('jpc_stone_cost_label', 'NOT SET');
        $extra_label = get_option('jpc_extra_fee_label', 'NOT SET');
        
        echo '<table>';
        echo '<tr><th>Setting</th><th>Value</th><th>Status</th></tr>';
        echo '<tr><td><code>jpc_pearl_cost_label</code></td><td><strong>' . esc_html($pearl_label) . '</strong></td><td>' . ($pearl_label !== 'NOT SET' ? '✅' : '❌') . '</td></tr>';
        echo '<tr><td><code>jpc_stone_cost_label</code></td><td><strong>' . esc_html($stone_label) . '</strong></td><td>' . ($stone_label !== 'NOT SET' ? '✅' : '❌') . '</td></tr>';
        echo '<tr><td><code>jpc_extra_fee_label</code></td><td><strong>' . esc_html($extra_label) . '</strong></td><td>' . ($extra_label !== 'NOT SET' ? '✅' : '❌') . '</td></tr>';
        echo '</table>';
        
        echo '<hr style="margin: 30px 0;">';
        
        // LIVE TEST: Simulate what the template would output
        echo '<h2>🧪 LIVE TEST - What Template Would Output:</h2>';
        echo '<div class="check info">';
        echo '<p><strong>If the template runs right now, it would show:</strong></p>';
        echo '<table>';
        echo '<tr><th>Field</th><th>Label Output</th></tr>';
        echo '<tr><td>Pearl Cost</td><td><strong>' . esc_html(get_option('jpc_pearl_cost_label', 'Pearl Cost')) . '</strong></td></tr>';
        echo '<tr><td>Stone Cost</td><td><strong>' . esc_html(get_option('jpc_stone_cost_label', 'Stone Cost')) . '</strong></td></tr>';
        echo '<tr><td>Extra Fee</td><td><strong>' . esc_html(get_option('jpc_extra_fee_label', 'Extra Fee')) . '</strong></td></tr>';
        echo '</table>';
        echo '</div>';
        
        // Check if WooCommerce is active
        echo '<hr style="margin: 30px 0;">';
        echo '<h2>🔌 Plugin Status:</h2>';
        if (class_exists('WooCommerce')) {
            echo '<div class="check success"><strong>✅ WooCommerce:</strong> Active</div>';
        } else {
            echo '<div class="check error"><strong>❌ WooCommerce:</strong> Not Active!</div>';
        }
        
        if (defined('JPC_VERSION')) {
            echo '<div class="check success"><strong>✅ JPC Plugin:</strong> Active (Version: ' . JPC_VERSION . ')</div>';
        } else {
            echo '<div class="check error"><strong>❌ JPC Plugin:</strong> Not Active!</div>';
        }
        
        // Check for caching
        echo '<hr style="margin: 30px 0;">';
        echo '<h2>💾 Caching Check:</h2>';
        echo '<div class="check warning">';
        echo '<p><strong>Possible caching issues:</strong></p>';
        echo '<ul>';
        echo '<li>Browser cache - Clear with Ctrl+Shift+R</li>';
        echo '<li>WordPress object cache - Check if Redis/Memcached is active</li>';
        echo '<li>Page cache plugins - WP Super Cache, W3 Total Cache, etc.</li>';
        echo '<li>Server cache - Nginx/Apache cache, Cloudflare, etc.</li>';
        echo '</ul>';
        echo '</div>';
        
        echo '<hr style="margin: 30px 0;">';
        
        echo '<h2>📋 Next Steps:</h2>';
        
        if (file_exists($template1) && file_exists($template2)) {
            if (strpos(file_get_contents($template1), 'v2.5.2') !== false && 
                strpos(file_get_contents($template2), 'v2.5.2') !== false) {
                echo '<div class="check success">';
                echo '<p><strong>✅ Templates are correct!</strong></p>';
                echo '<p>If labels still not showing on frontend:</p>';
                echo '<ol>';
                echo '<li>Clear ALL caches (browser, WordPress, server)</li>';
                echo '<li>Try incognito/private browsing mode</li>';
                echo '<li>Check if product has price breakup data saved</li>';
                echo '<li>Re-save the product to regenerate breakup</li>';
                echo '</ol>';
                echo '</div>';
            } else {
                echo '<div class="check error">';
                echo '<p><strong>❌ Templates need updating!</strong></p>';
                echo '<ol>';
                echo '<li>Download fresh templates from GitHub</li>';
                echo '<li>Upload to: <code>/wp-content/plugins/jewellery-price-calculator/templates/frontend/</code></li>';
                echo '<li>Overwrite existing files</li>';
                echo '<li>Refresh this page to verify</li>';
                echo '</ol>';
                echo '</div>';
            }
        } else {
            echo '<div class="check error">';
            echo '<p><strong>❌ Templates missing!</strong></p>';
            echo '<ol>';
            echo '<li>Create folder: <code>/wp-content/plugins/jewellery-price-calculator/templates/frontend/</code></li>';
            echo '<li>Download templates from GitHub</li>';
            echo '<li>Upload both files</li>';
            echo '<li>Refresh this page to verify</li>';
            echo '</ol>';
            echo '</div>';
        }
        
        echo '<div class="check info">';
        echo '<strong>Download Links:</strong><br>';
        echo '<a href="https://raw.githubusercontent.com/namirkhan265-star/jewellery-price-calculator/main/templates/frontend/price-breakup.php" target="_blank">📥 Download price-breakup.php</a><br>';
        echo '<a href="https://raw.githubusercontent.com/namirkhan265-star/jewellery-price-calculator/main/templates/frontend/detailed-breakup.php" target="_blank">📥 Download detailed-breakup.php</a>';
        echo '</div>';
        ?>
    </div>
</body>
</html>
