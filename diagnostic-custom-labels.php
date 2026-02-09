<?php
/**
 * Diagnostic Tool for Custom Labels
 * 
 * INSTRUCTIONS:
 * 1. Upload this file to your WordPress root directory
 * 2. Access it via: https://yoursite.com/diagnostic-custom-labels.php
 * 3. Check the output to see what's wrong
 * 4. DELETE this file after diagnosis for security
 */

// Load WordPress
require_once('wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    die('Access denied. You must be an administrator.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Custom Labels Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #0073aa; padding-bottom: 10px; }
        h2 { color: #0073aa; margin-top: 30px; }
        .status { padding: 15px; margin: 15px 0; border-radius: 4px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .warning { background: #fff3cd; border: 1px solid #ffc107; color: #856404; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #0073aa; color: white; font-weight: bold; }
        tr:hover { background: #f5f5f5; }
        .code { background: #f4f4f4; padding: 10px; border-left: 4px solid #0073aa; font-family: monospace; margin: 10px 0; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #28a745; color: white; }
        .badge-error { background: #dc3545; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Custom Labels Diagnostic Tool</h1>
        <p><strong>Date:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        
        <?php
        // Check 1: Are settings registered and saved?
        echo '<h2>1. Settings Configuration</h2>';
        
        $pearl_label = get_option('jpc_pearl_cost_label', false);
        $stone_label = get_option('jpc_stone_cost_label', false);
        $extra_label = get_option('jpc_extra_fee_label', false);
        
        echo '<table>';
        echo '<tr><th>Setting</th><th>Value</th><th>Status</th></tr>';
        
        echo '<tr>';
        echo '<td><strong>jpc_pearl_cost_label</strong></td>';
        echo '<td>' . ($pearl_label ? esc_html($pearl_label) : '<em>Not set</em>') . '</td>';
        echo '<td>';
        if ($pearl_label && $pearl_label !== 'Pearl Cost') {
            echo '<span class="badge badge-success">CUSTOM</span>';
        } elseif ($pearl_label === 'Pearl Cost') {
            echo '<span class="badge badge-warning">DEFAULT</span>';
        } else {
            echo '<span class="badge badge-error">NOT SET</span>';
        }
        echo '</td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<td><strong>jpc_stone_cost_label</strong></td>';
        echo '<td>' . ($stone_label ? esc_html($stone_label) : '<em>Not set</em>') . '</td>';
        echo '<td>';
        if ($stone_label && $stone_label !== 'Stone Cost') {
            echo '<span class="badge badge-success">CUSTOM</span>';
        } elseif ($stone_label === 'Stone Cost') {
            echo '<span class="badge badge-warning">DEFAULT</span>';
        } else {
            echo '<span class="badge badge-error">NOT SET</span>';
        }
        echo '</td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<td><strong>jpc_extra_fee_label</strong></td>';
        echo '<td>' . ($extra_label ? esc_html($extra_label) : '<em>Not set</em>') . '</td>';
        echo '<td>';
        if ($extra_label && $extra_label !== 'Extra Fee') {
            echo '<span class="badge badge-success">CUSTOM</span>';
        } elseif ($extra_label === 'Extra Fee') {
            echo '<span class="badge badge-warning">DEFAULT</span>';
        } else {
            echo '<span class="badge badge-error">NOT SET</span>';
        }
        echo '</td>';
        echo '</tr>';
        
        echo '</table>';
        
        if (!$pearl_label && !$stone_label && !$extra_label) {
            echo '<div class="status error">';
            echo '<strong>❌ ERROR:</strong> No custom labels are set in WordPress options!<br>';
            echo '<strong>Action Required:</strong> Go to Jewellery Price > General Settings and set your custom labels, then click Save Changes.';
            echo '</div>';
        } elseif ($pearl_label === 'Pearl Cost' && $stone_label === 'Stone Cost' && $extra_label === 'Extra Fee') {
            echo '<div class="status warning">';
            echo '<strong>⚠️ WARNING:</strong> All labels are set to default values.<br>';
            echo '<strong>Action Required:</strong> Go to Jewellery Price > General Settings and change the labels to your custom names.';
            echo '</div>';
        } else {
            echo '<div class="status success">';
            echo '<strong>✅ SUCCESS:</strong> Custom labels are configured in settings!';
            echo '</div>';
        }
        
        // Check 2: Check a sample product's breakup data
        echo '<h2>2. Product Breakup Data</h2>';
        
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 5,
            'meta_query' => array(
                array(
                    'key' => '_jpc_metal_id',
                    'compare' => 'EXISTS'
                )
            )
        );
        
        $products = get_posts($args);
        
        if (empty($products)) {
            echo '<div class="status warning">';
            echo '<strong>⚠️ WARNING:</strong> No products found with JPC data.';
            echo '</div>';
        } else {
            echo '<p>Checking first ' . count($products) . ' products with JPC data:</p>';
            echo '<table>';
            echo '<tr><th>Product ID</th><th>Product Name</th><th>Pearl Label</th><th>Stone Label</th><th>Extra Label</th><th>Status</th></tr>';
            
            foreach ($products as $product) {
                $breakup = get_post_meta($product->ID, '_jpc_price_breakup', true);
                
                echo '<tr>';
                echo '<td>' . $product->ID . '</td>';
                echo '<td>' . esc_html($product->post_title) . '</td>';
                
                if ($breakup && is_array($breakup)) {
                    $breakup_pearl = isset($breakup['pearl_cost_label']) ? $breakup['pearl_cost_label'] : 'NOT IN BREAKUP';
                    $breakup_stone = isset($breakup['stone_cost_label']) ? $breakup['stone_cost_label'] : 'NOT IN BREAKUP';
                    $breakup_extra = isset($breakup['extra_fee_label']) ? $breakup['extra_fee_label'] : 'NOT IN BREAKUP';
                    
                    echo '<td>' . esc_html($breakup_pearl) . '</td>';
                    echo '<td>' . esc_html($breakup_stone) . '</td>';
                    echo '<td>' . esc_html($breakup_extra) . '</td>';
                    
                    if ($breakup_pearl === 'NOT IN BREAKUP' && $breakup_stone === 'NOT IN BREAKUP' && $breakup_extra === 'NOT IN BREAKUP') {
                        echo '<td><span class="badge badge-error">NEEDS REGENERATION</span></td>';
                    } elseif ($breakup_pearl === 'Pearl Cost' && $breakup_stone === 'Stone Cost' && $breakup_extra === 'Extra Fee') {
                        echo '<td><span class="badge badge-warning">DEFAULT LABELS</span></td>';
                    } else {
                        echo '<td><span class="badge badge-success">HAS CUSTOM LABELS</span></td>';
                    }
                } else {
                    echo '<td colspan="3"><em>No breakup data</em></td>';
                    echo '<td><span class="badge badge-error">NO BREAKUP</span></td>';
                }
                
                echo '</tr>';
            }
            
            echo '</table>';
            
            // Check if any product needs regeneration
            $needs_regen = false;
            foreach ($products as $product) {
                $breakup = get_post_meta($product->ID, '_jpc_price_breakup', true);
                if (!$breakup || !is_array($breakup) || !isset($breakup['pearl_cost_label'])) {
                    $needs_regen = true;
                    break;
                }
            }
            
            if ($needs_regen) {
                echo '<div class="status error">';
                echo '<strong>❌ ACTION REQUIRED:</strong> Some products don\'t have custom labels in their breakup data!<br>';
                echo '<strong>Solution:</strong> You must regenerate the price breakup for these products:<br>';
                echo '<ol>';
                echo '<li>Edit each product individually and click "Regenerate Price Breakup" button</li>';
                echo '<li>OR go to Jewellery Price > General and click "Update All Prices" to bulk update all products</li>';
                echo '</ol>';
                echo '</div>';
            } else {
                echo '<div class="status success">';
                echo '<strong>✅ SUCCESS:</strong> All checked products have custom labels in their breakup data!';
                echo '</div>';
            }
        }
        
        // Check 3: Code version check
        echo '<h2>3. Code Version Check</h2>';
        
        $calc_file = WP_PLUGIN_DIR . '/jewellery-price-calculator/includes/class-jpc-price-calculator.php';
        if (file_exists($calc_file)) {
            $content = file_get_contents($calc_file);
            
            $has_label_fetch = strpos($content, "get_option('jpc_pearl_cost_label'") !== false;
            $has_label_store = strpos($content, "'pearl_cost_label' => \$pearl_cost_label") !== false;
            
            echo '<table>';
            echo '<tr><th>Check</th><th>Status</th></tr>';
            echo '<tr><td>Fetches custom labels from settings</td><td>';
            if ($has_label_fetch) {
                echo '<span class="badge badge-success">✓ YES</span>';
            } else {
                echo '<span class="badge badge-error">✗ NO</span>';
            }
            echo '</td></tr>';
            echo '<tr><td>Stores labels in breakup data</td><td>';
            if ($has_label_store) {
                echo '<span class="badge badge-success">✓ YES</span>';
            } else {
                echo '<span class="badge badge-error">✗ NO</span>';
            }
            echo '</td></tr>';
            echo '</table>';
            
            if (!$has_label_fetch || !$has_label_store) {
                echo '<div class="status error">';
                echo '<strong>❌ ERROR:</strong> Your code is outdated!<br>';
                echo '<strong>Action Required:</strong> Download the latest version of the plugin from GitHub and replace the files.';
                echo '</div>';
            } else {
                echo '<div class="status success">';
                echo '<strong>✅ SUCCESS:</strong> Code is up to date with v2.5.0 custom labels feature!';
                echo '</div>';
            }
        } else {
            echo '<div class="status error">';
            echo '<strong>❌ ERROR:</strong> Cannot find class-jpc-price-calculator.php file!';
            echo '</div>';
        }
        
        // Check 4: Frontend template check
        echo '<h2>4. Frontend Template Check</h2>';
        
        $template_file = WP_PLUGIN_DIR . '/jewellery-price-calculator/templates/frontend/price-breakup.php';
        if (file_exists($template_file)) {
            $content = file_get_contents($template_file);
            
            $uses_breakup_labels = strpos($content, "\$breakup['pearl_cost_label']") !== false;
            
            echo '<table>';
            echo '<tr><th>Check</th><th>Status</th></tr>';
            echo '<tr><td>Uses labels from breakup data</td><td>';
            if ($uses_breakup_labels) {
                echo '<span class="badge badge-success">✓ YES</span>';
            } else {
                echo '<span class="badge badge-error">✗ NO</span>';
            }
            echo '</td></tr>';
            echo '</table>';
            
            if (!$uses_breakup_labels) {
                echo '<div class="status error">';
                echo '<strong>❌ ERROR:</strong> Frontend template is outdated!<br>';
                echo '<strong>Action Required:</strong> Download the latest version of the plugin from GitHub and replace the files.';
                echo '</div>';
            } else {
                echo '<div class="status success">';
                echo '<strong>✅ SUCCESS:</strong> Frontend template is up to date!';
                echo '</div>';
            }
        } else {
            echo '<div class="status error">';
            echo '<strong>❌ ERROR:</strong> Cannot find price-breakup.php template file!';
            echo '</div>';
        }
        
        // Final recommendation
        echo '<h2>5. Final Recommendation</h2>';
        
        if (!$pearl_label && !$stone_label && !$extra_label) {
            echo '<div class="status error">';
            echo '<h3>🔴 CRITICAL: Settings Not Configured</h3>';
            echo '<p><strong>Steps to fix:</strong></p>';
            echo '<ol>';
            echo '<li>Go to <strong>Jewellery Price > General Settings</strong></li>';
            echo '<li>Scroll to "Additional Cost Fields"</li>';
            echo '<li>Enable each field and set custom labels</li>';
            echo '<li>Click <strong>Save Changes</strong></li>';
            echo '<li>Come back to this diagnostic page to verify</li>';
            echo '</ol>';
            echo '</div>';
        } elseif ($needs_regen) {
            echo '<div class="status warning">';
            echo '<h3>🟡 ACTION REQUIRED: Regenerate Price Breakup</h3>';
            echo '<p><strong>Your settings are correct, but products need to be updated:</strong></p>';
            echo '<ol>';
            echo '<li>Go to <strong>Jewellery Price > General</strong></li>';
            echo '<li>Scroll to bottom of page</li>';
            echo '<li>Click <strong>"Update All Prices"</strong> button</li>';
            echo '<li>Wait for completion message</li>';
            echo '<li>Check frontend - labels should now be custom!</li>';
            echo '</ol>';
            echo '</div>';
        } else {
            echo '<div class="status success">';
            echo '<h3>🟢 ALL GOOD!</h3>';
            echo '<p>Everything is configured correctly. Custom labels should be visible on the frontend.</p>';
            echo '<p>If you still see default labels on frontend:</p>';
            echo '<ul>';
            echo '<li>Clear your browser cache (Ctrl+Shift+R or Cmd+Shift+R)</li>';
            echo '<li>Clear WordPress cache if using a caching plugin</li>';
            echo '<li>Check if you\'re viewing the correct product</li>';
            echo '</ul>';
            echo '</div>';
        }
        
        echo '<hr style="margin: 40px 0;">';
        echo '<p style="color: #999; font-size: 12px;"><strong>Security Note:</strong> Please delete this diagnostic file after use for security reasons.</p>';
        ?>
    </div>
</body>
</html>
