<?php
/**
 * Debug/Troubleshooting Page
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// Handle actions
if (isset($_POST['jpc_action'])) {
    if ($_POST['jpc_action'] === 'recreate_tables') {
        check_admin_referer('jpc_recreate_tables');
        JPC_Database::drop_tables();
        JPC_Database::create_tables();
        echo '<div class="notice notice-success"><p>Tables recreated successfully!</p></div>';
    } elseif ($_POST['jpc_action'] === 'populate_diamond_data') {
        check_admin_referer('jpc_populate_diamond_data');
        JPC_Database::force_insert_diamond_data();
        echo '<div class="notice notice-success"><p>Diamond data populated successfully!</p></div>';
    }
}

// Check if tables exist
$tables_status = array();
$tables = array(
    'jpc_metal_groups',
    'jpc_metals',
    'jpc_diamond_groups',
    'jpc_diamond_types',
    'jpc_diamond_certifications',
    'jpc_diamonds',
    'jpc_price_history',
    'jpc_product_price_log'
);

foreach ($tables as $table) {
    $full_table = $wpdb->prefix . $table;
    $exists = $wpdb->get_var("SHOW TABLES LIKE '$full_table'") == $full_table;
    $count = $exists ? $wpdb->get_var("SELECT COUNT(*) FROM `$full_table`") : 0;
    $tables_status[$table] = array('exists' => $exists, 'count' => $count);
}

// Get diamond groups
$diamond_groups = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}jpc_diamond_groups");

// Get diamond types
$diamond_types = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}jpc_diamond_types");

// Get certifications
$certifications = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}jpc_diamond_certifications");

?>

<div class="wrap">
    <h1>Jewellery Price Calculator - Debug Info</h1>
    
    <div class="jpc-card" style="background: #fff; padding: 20px; margin: 20px 0;">
        <h2>Database Tables Status</h2>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th>Table Name</th>
                    <th>Status</th>
                    <th>Records</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tables_status as $table => $status): ?>
                <tr>
                    <td><code><?php echo esc_html($wpdb->prefix . $table); ?></code></td>
                    <td>
                        <?php if ($status['exists']): ?>
                            <span style="color: green; font-weight: bold;">✓ Exists</span>
                        <?php else: ?>
                            <span style="color: red; font-weight: bold;">✗ Missing</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($status['exists']): ?>
                            <strong><?php echo $status['count']; ?></strong> records
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Diamond Groups -->
    <div class="jpc-card" style="background: #fff; padding: 20px; margin: 20px 0;">
        <h2>Diamond Groups (<?php echo count($diamond_groups); ?> total)</h2>
        <?php if ($diamond_groups): ?>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($diamond_groups as $group): ?>
                    <tr>
                        <td><?php echo $group->id; ?></td>
                        <td><strong><?php echo esc_html($group->name); ?></strong></td>
                        <td><code><?php echo esc_html($group->slug); ?></code></td>
                        <td><?php echo esc_html($group->description); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="notice notice-warning inline">
                <p><strong>No diamond groups found!</strong> Click "Populate Diamond Data" below to add default data.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Diamond Types -->
    <div class="jpc-card" style="background: #fff; padding: 20px; margin: 20px 0;">
        <h2>Diamond Types / Carat Ranges (<?php echo count($diamond_types); ?> total)</h2>
        <?php if ($diamond_types): ?>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Group ID</th>
                        <th>Carat Range</th>
                        <th>Price/Carat</th>
                        <th>Display Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($diamond_types as $type): ?>
                    <tr>
                        <td><?php echo $type->id; ?></td>
                        <td><?php echo $type->diamond_group_id; ?></td>
                        <td><strong><?php echo number_format($type->carat_from, 3); ?> - <?php echo number_format($type->carat_to, 3); ?> ct</strong></td>
                        <td>₹<?php echo number_format($type->price_per_carat, 2); ?></td>
                        <td><?php echo esc_html($type->display_name); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="notice notice-warning inline">
                <p><strong>No diamond types found!</strong> Click "Populate Diamond Data" below to add default data.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Certifications -->
    <div class="jpc-card" style="background: #fff; padding: 20px; margin: 20px 0;">
        <h2>Diamond Certifications (<?php echo count($certifications); ?> total)</h2>
        <?php if ($certifications): ?>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Type</th>
                        <th>Adjustment</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($certifications as $cert): ?>
                    <tr>
                        <td><?php echo $cert->id; ?></td>
                        <td><strong><?php echo esc_html($cert->name); ?></strong></td>
                        <td><code><?php echo esc_html($cert->slug); ?></code></td>
                        <td><?php echo ucfirst($cert->adjustment_type); ?></td>
                        <td>
                            <?php if ($cert->adjustment_type === 'percentage'): ?>
                                <strong><?php echo ($cert->adjustment_value >= 0 ? '+' : '') . $cert->adjustment_value; ?>%</strong>
                            <?php else: ?>
                                <strong><?php echo ($cert->adjustment_value >= 0 ? '+' : '') . '₹' . number_format($cert->adjustment_value, 2); ?></strong>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($cert->description); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="notice notice-warning inline">
                <p><strong>No certifications found!</strong> Click "Populate Diamond Data" below to add default data.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- System Info -->
    <div class="jpc-card" style="background: #fff; padding: 20px; margin: 20px 0;">
        <h2>System Information</h2>
        <table class="widefat striped">
            <tr>
                <td><strong>Plugin Version:</strong></td>
                <td><?php echo JPC_VERSION; ?></td>
            </tr>
            <tr>
                <td><strong>WordPress Version:</strong></td>
                <td><?php echo get_bloginfo('version'); ?></td>
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
                <td><strong>Table Prefix:</strong></td>
                <td><code><?php echo $wpdb->prefix; ?></code></td>
            </tr>
            <tr>
                <td><strong>WooCommerce Active:</strong></td>
                <td><?php echo class_exists('WooCommerce') ? '<span style="color: green;">✓ Yes</span>' : '<span style="color: red;">✗ No</span>'; ?></td>
            </tr>
        </table>
    </div>
    
    <!-- Quick Actions -->
    <div class="jpc-card" style="background: #fff; padding: 20px; margin: 20px 0;">
        <h2>Quick Actions</h2>
        
        <div style="margin-bottom: 20px;">
            <h3>Navigation</h3>
            <p>
                <a href="<?php echo admin_url('admin.php?page=jpc-diamond-groups'); ?>" class="button">Diamond Groups</a>
                <a href="<?php echo admin_url('admin.php?page=jpc-diamond-types'); ?>" class="button">Diamond Types</a>
                <a href="<?php echo admin_url('admin.php?page=jpc-diamond-certifications'); ?>" class="button">Certifications</a>
                <a href="<?php echo admin_url('admin.php?page=jpc-diamonds'); ?>" class="button">Diamonds (Legacy)</a>
            </p>
        </div>
        
        <div style="margin-bottom: 20px; padding: 15px; background: #e7f3ff; border-left: 4px solid #2196f3;">
            <h3 style="margin-top: 0;">Populate Diamond Data</h3>
            <p><strong>Use this if your Diamond Groups, Types, or Certifications tabs are empty.</strong></p>
            <p>This will add default data:</p>
            <ul>
                <li>3 Diamond Groups (Natural, Lab Grown, Moissanite)</li>
                <li>11 Diamond Types with carat ranges</li>
                <li>4 Certifications (GIA, IGI, HRD, None)</li>
            </ul>
            <form method="post" onsubmit="return confirm('This will clear and repopulate diamond data. Continue?');">
                <?php wp_nonce_field('jpc_populate_diamond_data'); ?>
                <input type="hidden" name="jpc_action" value="populate_diamond_data">
                <button type="submit" class="button button-primary button-large">Populate Diamond Data</button>
            </form>
        </div>
        
        <div style="padding: 15px; background: #fff3cd; border-left: 4px solid #ff9800;">
            <h3 style="margin-top: 0;">Recreate All Tables</h3>
            <p><strong>⚠️ Warning:</strong> This will drop and recreate all tables. Use only if tables are corrupted.</p>
            <p><strong style="color: red;">This will delete ALL data!</strong></p>
            <form method="post" onsubmit="return confirm('Are you ABSOLUTELY sure? This will DELETE ALL DATA!');">
                <?php wp_nonce_field('jpc_recreate_tables'); ?>
                <input type="hidden" name="jpc_action" value="recreate_tables">
                <button type="submit" class="button button-secondary">Recreate All Tables</button>
            </form>
        </div>
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
    border-bottom: 2px solid #eee;
}
.jpc-card h3 {
    margin-top: 0;
}
</style>
