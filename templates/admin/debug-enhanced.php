<?php
/**
 * Enhanced Debug/Troubleshooting Page with Price Calculation Details
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// Handle actions
if (isset($_POST['jpc_action'])) {
    if ($_POST['jpc_action'] === 'test_price_calculation') {
        check_admin_referer('jpc_test_price_calculation');
        $product_id = intval($_POST['product_id']);
        
        // Force recalculate
        JPC_Price_Calculator::calculate_and_update_price($product_id);
        
        echo '<div class="notice notice-success is-dismissible"><p><strong>Success!</strong> Price recalculated for product ID: ' . $product_id . '</p></div>';
    }
}

// Get a sample product for testing
$sample_product = $wpdb->get_row("
    SELECT p.ID, p.post_title 
    FROM {$wpdb->posts} p
    INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
    WHERE p.post_type = 'product' 
    AND pm.meta_key = '_jpc_metal_id'
    AND pm.meta_value != ''
    LIMIT 1
");

?>

<div class="wrap">
    <h1>🔧 Jewellery Price Calculator - Enhanced Debug</h1>
    
    <div class="notice notice-info" style="padding: 15px; margin: 20px 0;">
        <h3 style="margin-top: 0;">📋 Debug Information</h3>
        <p>This page helps you troubleshoot price calculation issues, GST problems, and discount errors.</p>
    </div>
    
    <!-- SECTION 1: GLOBAL SETTINGS -->
    <div class="jpc-card" style="background: #fff; padding: 20px; margin: 20px 0; border-left: 4px solid #2271b1;">
        <h2>⚙️ Global Settings</h2>
        
        <h3>GST Configuration</h3>
        <table class="widefat striped">
            <tr>
                <td width="30%"><strong>GST Enabled:</strong></td>
                <td>
                    <?php 
                    $gst_enabled = get_option('jpc_enable_gst');
                    echo $gst_enabled === 'yes' || $gst_enabled === '1' ? '<span style="color: green;">✓ YES</span>' : '<span style="color: red;">✗ NO</span>';
                    ?>
                    <code>(Value: <?php echo var_export($gst_enabled, true); ?>)</code>
                </td>
            </tr>
            <tr>
                <td><strong>GST Percentage:</strong></td>
                <td><?php echo get_option('jpc_gst_value', 5); ?>%</td>
            </tr>
            <tr>
                <td><strong>GST Label:</strong></td>
                <td><?php echo get_option('jpc_gst_label', 'GST'); ?></td>
            </tr>
        </table>
        
        <h3 style="margin-top: 20px;">Metal-Specific GST Rates</h3>
        <table class="widefat striped">
            <?php
            $metal_groups = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}jpc_metal_groups");
            foreach ($metal_groups as $group) {
                $group_name_underscore = strtolower(str_replace(' ', '_', $group->name));
                $group_name_no_space = strtolower(str_replace(' ', '', $group->name));
                
                $gst_rate_1 = get_option('jpc_gst_' . $group_name_underscore);
                $gst_rate_2 = get_option('jpc_gst_' . $group_name_no_space);
                
                echo '<tr>';
                echo '<td width="30%"><strong>' . esc_html($group->name) . ':</strong></td>';
                echo '<td>';
                
                if ($gst_rate_1 !== false && $gst_rate_1 !== '') {
                    echo '<span style="color: green;">' . $gst_rate_1 . '%</span> ';
                    echo '<code>(jpc_gst_' . $group_name_underscore . ')</code>';
                } elseif ($gst_rate_2 !== false && $gst_rate_2 !== '') {
                    echo '<span style="color: green;">' . $gst_rate_2 . '%</span> ';
                    echo '<code>(jpc_gst_' . $group_name_no_space . ')</code>';
                } else {
                    echo '<span style="color: orange;">Using global GST</span> ';
                    echo '<code>(No metal-specific rate set)</code>';
                }
                
                echo '</td>';
                echo '</tr>';
            }
            ?>
        </table>
        
        <h3 style="margin-top: 20px;">Additional Percentage</h3>
        <table class="widefat striped">
            <tr>
                <td width="30%"><strong>Additional Percentage:</strong></td>
                <td><?php echo get_option('jpc_additional_percentage_value', 0); ?>%</td>
            </tr>
            <tr>
                <td><strong>Label:</strong></td>
                <td><?php echo get_option('jpc_additional_percentage_label', 'Additional Percentage'); ?></td>
            </tr>
        </table>
        
        <h3 style="margin-top: 20px;">Discount Settings</h3>
        <table class="widefat striped">
            <tr>
                <td width="30%"><strong>Discount on Metals:</strong></td>
                <td>
                    <?php 
                    $discount_metals = get_option('jpc_discount_on_metals');
                    echo $discount_metals === 'yes' || $discount_metals === '1' ? '<span style="color: green;">✓ YES</span>' : '<span style="color: red;">✗ NO</span>';
                    ?>
                    <code>(Value: <?php echo var_export($discount_metals, true); ?>)</code>
                </td>
            </tr>
            <tr>
                <td><strong>Discount on Making:</strong></td>
                <td>
                    <?php 
                    $discount_making = get_option('jpc_discount_on_making');
                    echo $discount_making === 'yes' || $discount_making === '1' ? '<span style="color: green;">✓ YES</span>' : '<span style="color: red;">✗ NO</span>';
                    ?>
                    <code>(Value: <?php echo var_export($discount_making, true); ?>)</code>
                </td>
            </tr>
            <tr>
                <td><strong>Discount on Wastage:</strong></td>
                <td>
                    <?php 
                    $discount_wastage = get_option('jpc_discount_on_wastage');
                    echo $discount_wastage === 'yes' || $discount_wastage === '1' ? '<span style="color: green;">✓ YES</span>' : '<span style="color: red;">✗ NO</span>';
                    ?>
                    <code>(Value: <?php echo var_export($discount_wastage, true); ?>)</code>
                </td>
            </tr>
        </table>
        
        <h3 style="margin-top: 20px;">Extra Fields</h3>
        <table class="widefat striped">
            <?php for ($i = 1; $i <= 5; $i++): ?>
            <tr>
                <td width="30%"><strong>Extra Field #<?php echo $i; ?>:</strong></td>
                <td>
                    <?php 
                    $enabled = get_option('jpc_enable_extra_field_' . $i);
                    echo $enabled === 'yes' || $enabled === '1' ? '<span style="color: green;">✓ Enabled</span>' : '<span style="color: red;">✗ Disabled</span>';
                    ?>
                    - Label: <strong><?php echo get_option('jpc_extra_field_label_' . $i, 'Extra Field #' . $i); ?></strong>
                </td>
            </tr>
            <?php endfor; ?>
        </table>
    </div>
    
    <!-- SECTION 2: SAMPLE PRODUCT CALCULATION -->
    <?php if ($sample_product): ?>
    <div class="jpc-card" style="background: #fff; padding: 20px; margin: 20px 0; border-left: 4px solid #00a32a;">
        <h2>🧮 Sample Product Calculation Test</h2>
        <p><strong>Product:</strong> <?php echo esc_html($sample_product->post_title); ?> (ID: <?php echo $sample_product->ID; ?>)</p>
        
        <?php
        // Get product data
        $product_id = $sample_product->ID;
        $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
        $metal = JPC_Metals::get_by_id($metal_id);
        $metal_group = $metal ? JPC_Metal_Groups::get_by_id($metal->metal_group_id) : null;
        
        // Get all product meta
        $product_meta = array(
            'metal_id' => $metal_id,
            'metal_weight' => get_post_meta($product_id, '_jpc_metal_weight', true),
            'diamond_id' => get_post_meta($product_id, '_jpc_diamond_id', true),
            'diamond_quantity' => get_post_meta($product_id, '_jpc_diamond_quantity', true),
            'making_charge' => get_post_meta($product_id, '_jpc_making_charge', true),
            'making_charge_type' => get_post_meta($product_id, '_jpc_making_charge_type', true),
            'wastage_charge' => get_post_meta($product_id, '_jpc_wastage_charge', true),
            'wastage_charge_type' => get_post_meta($product_id, '_jpc_wastage_charge_type', true),
            'pearl_cost' => get_post_meta($product_id, '_jpc_pearl_cost', true),
            'stone_cost' => get_post_meta($product_id, '_jpc_stone_cost', true),
            'extra_fee' => get_post_meta($product_id, '_jpc_extra_fee', true),
            'extra_field_1' => get_post_meta($product_id, '_jpc_extra_field_1', true),
            'extra_field_2' => get_post_meta($product_id, '_jpc_extra_field_2', true),
            'extra_field_3' => get_post_meta($product_id, '_jpc_extra_field_3', true),
            'extra_field_4' => get_post_meta($product_id, '_jpc_extra_field_4', true),
            'extra_field_5' => get_post_meta($product_id, '_jpc_extra_field_5', true),
            'discount_percentage' => get_post_meta($product_id, '_jpc_discount_percentage', true),
        );
        
        // Calculate prices
        $prices = JPC_Price_Calculator::calculate_product_prices($product_id);
        $breakup = get_post_meta($product_id, '_jpc_price_breakup', true);
        
        // Get WooCommerce prices
        $wc_regular = get_post_meta($product_id, '_regular_price', true);
        $wc_sale = get_post_meta($product_id, '_sale_price', true);
        ?>
        
        <h3>Product Configuration</h3>
        <table class="widefat striped">
            <tr>
                <td width="30%"><strong>Metal:</strong></td>
                <td><?php echo $metal ? esc_html($metal->display_name) . ' (₹' . number_format($metal->price_per_unit, 2) . '/gm)' : 'N/A'; ?></td>
            </tr>
            <tr>
                <td><strong>Metal Group:</strong></td>
                <td><?php echo $metal_group ? esc_html($metal_group->name) : 'N/A'; ?></td>
            </tr>
            <tr>
                <td><strong>Weight:</strong></td>
                <td><?php echo $product_meta['metal_weight']; ?> grams</td>
            </tr>
            <tr>
                <td><strong>Making Charge:</strong></td>
                <td><?php echo $product_meta['making_charge']; ?> (<?php echo $product_meta['making_charge_type']; ?>)</td>
            </tr>
            <tr>
                <td><strong>Wastage Charge:</strong></td>
                <td><?php echo $product_meta['wastage_charge']; ?> (<?php echo $product_meta['wastage_charge_type']; ?>)</td>
            </tr>
            <tr>
                <td><strong>Discount:</strong></td>
                <td><?php echo $product_meta['discount_percentage']; ?>%</td>
            </tr>
        </table>
        
        <h3 style="margin-top: 20px;">Calculated Prices (from calculate_product_prices())</h3>
        <?php if ($prices): ?>
        <table class="widefat striped">
            <tr>
                <td width="30%"><strong>Regular Price:</strong></td>
                <td><strong style="font-size: 1.2em; color: #0066cc;">₹<?php echo number_format($prices['regular_price'], 2); ?></strong></td>
            </tr>
            <tr>
                <td><strong>Sale Price:</strong></td>
                <td><strong style="font-size: 1.2em; color: #d63638;">₹<?php echo number_format($prices['sale_price'], 2); ?></strong></td>
            </tr>
            <tr>
                <td><strong>Discount Amount:</strong></td>
                <td>₹<?php echo number_format($prices['discount_amount'], 2); ?></td>
            </tr>
            <tr>
                <td><strong>GST on Full:</strong></td>
                <td>₹<?php echo number_format($prices['gst_on_full'], 2); ?> (<?php echo $prices['gst_percentage']; ?>%)</td>
            </tr>
            <tr>
                <td><strong>GST on Discounted:</strong></td>
                <td>₹<?php echo number_format($prices['gst_on_discounted'], 2); ?> (<?php echo $prices['gst_percentage']; ?>%)</td>
            </tr>
            <tr>
                <td><strong>Additional Percentage:</strong></td>
                <td>₹<?php echo number_format($prices['additional_percentage_amount'], 2); ?></td>
            </tr>
        </table>
        <?php else: ?>
        <div class="notice notice-error inline"><p><strong>ERROR:</strong> Could not calculate prices!</p></div>
        <?php endif; ?>
        
        <h3 style="margin-top: 20px;">WooCommerce Stored Prices</h3>
        <table class="widefat striped">
            <tr>
                <td width="30%"><strong>_regular_price:</strong></td>
                <td>₹<?php echo number_format($wc_regular, 2); ?></td>
            </tr>
            <tr>
                <td><strong>_sale_price:</strong></td>
                <td>₹<?php echo number_format($wc_sale, 2); ?></td>
            </tr>
            <tr>
                <td><strong>Match Status:</strong></td>
                <td>
                    <?php if ($prices && abs($wc_regular - $prices['regular_price']) < 0.01): ?>
                        <span style="color: green;">✓ Prices match!</span>
                    <?php else: ?>
                        <span style="color: red;">✗ Prices don't match! Recalculation needed.</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        
        <h3 style="margin-top: 20px;">Stored Price Breakup</h3>
        <?php if ($breakup && is_array($breakup)): ?>
        <table class="widefat striped">
            <tr>
                <td width="30%"><strong>Metal Price:</strong></td>
                <td>₹<?php echo number_format($breakup['metal_price'], 2); ?></td>
            </tr>
            <?php if (!empty($breakup['diamond_price'])): ?>
            <tr>
                <td><strong>Diamond Price:</strong></td>
                <td>₹<?php echo number_format($breakup['diamond_price'], 2); ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td><strong>Making Charge:</strong></td>
                <td>₹<?php echo number_format($breakup['making_charge'], 2); ?></td>
            </tr>
            <tr>
                <td><strong>Wastage Charge:</strong></td>
                <td>₹<?php echo number_format($breakup['wastage_charge'], 2); ?></td>
            </tr>
            <?php if (!empty($breakup['additional_percentage'])): ?>
            <tr>
                <td><strong><?php echo esc_html($breakup['additional_percentage_label'] ?? 'Additional Percentage'); ?>:</strong></td>
                <td>₹<?php echo number_format($breakup['additional_percentage'], 2); ?></td>
            </tr>
            <?php endif; ?>
            <?php if (!empty($breakup['discount'])): ?>
            <tr>
                <td><strong>Discount:</strong></td>
                <td style="color: #28a745;">-₹<?php echo number_format($breakup['discount'], 2); ?></td>
            </tr>
            <?php endif; ?>
            <tr style="background: <?php echo (empty($breakup['gst']) || $breakup['gst'] <= 0) ? '#fff3cd' : 'transparent'; ?>;">
                <td><strong>GST:</strong></td>
                <td>
                    ₹<?php echo number_format($breakup['gst'] ?? 0, 2); ?>
                    <?php if (isset($breakup['gst_percentage'])): ?>
                        (<?php echo $breakup['gst_percentage']; ?>%)
                    <?php endif; ?>
                    <?php if (empty($breakup['gst']) || $breakup['gst'] <= 0): ?>
                        <span style="color: #856404;"> ⚠️ GST is 0!</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr style="background: #f0f0f0;">
                <td><strong>Final Price:</strong></td>
                <td><strong style="font-size: 1.2em;">₹<?php echo number_format($breakup['final_price'], 2); ?></strong></td>
            </tr>
        </table>
        <?php else: ?>
        <div class="notice notice-error inline"><p><strong>ERROR:</strong> Price breakup not found or invalid!</p></div>
        <?php endif; ?>
        
        <form method="post" style="margin-top: 20px;">
            <?php wp_nonce_field('jpc_test_price_calculation'); ?>
            <input type="hidden" name="jpc_action" value="test_price_calculation">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <button type="submit" class="button button-primary button-large">
                <span class="dashicons dashicons-update" style="margin-top: 3px;"></span>
                Recalculate This Product Now
            </button>
        </form>
    </div>
    <?php else: ?>
    <div class="notice notice-warning" style="padding: 15px; margin: 20px 0;">
        <p><strong>No products found with JPC configuration.</strong> Create a product with metal configuration first.</p>
    </div>
    <?php endif; ?>
    
    <!-- SECTION 3: SYSTEM INFO -->
    <div class="jpc-card" style="background: #fff; padding: 20px; margin: 20px 0; border-left: 4px solid #8c8f94;">
        <h2>💻 System Information</h2>
        <table class="widefat striped">
            <tr>
                <td width="30%"><strong>Plugin Version:</strong></td>
                <td><?php echo JPC_VERSION; ?></td>
            </tr>
            <tr>
                <td><strong>WordPress Version:</strong></td>
                <td><?php echo get_bloginfo('version'); ?></td>
            </tr>
            <tr>
                <td><strong>WooCommerce Version:</strong></td>
                <td><?php echo defined('WC_VERSION') ? WC_VERSION : 'Not installed'; ?></td>
            </tr>
            <tr>
                <td><strong>PHP Version:</strong></td>
                <td><?php echo PHP_VERSION; ?></td>
            </tr>
            <tr>
                <td><strong>MySQL Version:</strong></td>
                <td><?php echo $wpdb->db_version(); ?></td>
            </tr>
            <tr>
                <td><strong>Server:</strong></td>
                <td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
            </tr>
        </table>
    </div>
</div>

<style>
.jpc-card {
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border-radius: 4px;
}
.jpc-card h2 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 2px solid #f0f0f0;
}
.jpc-card h3 {
    margin-top: 20px;
    margin-bottom: 10px;
    color: #1d2327;
}
</style>
