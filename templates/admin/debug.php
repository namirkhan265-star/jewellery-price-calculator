<?php
/**
 * Debug/Troubleshooting Page
 * 
 * Add this to your admin menu temporarily to debug issues
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// Check if tables exist
$tables_status = array();
$tables = array(
    'jpc_metal_groups',
    'jpc_metals',
    'jpc_price_history',
    'jpc_product_price_log'
);

foreach ($tables as $table) {
    $full_table = $wpdb->prefix . $table;
    $exists = $wpdb->get_var("SHOW TABLES LIKE '$full_table'") == $full_table;
    $tables_status[$table] = $exists;
}

// Get metal groups
$metal_groups_table = $wpdb->prefix . 'jpc_metal_groups';
$metal_groups = $wpdb->get_results("SELECT * FROM $metal_groups_table");
$metal_groups_count = $wpdb->get_var("SELECT COUNT(*) FROM $metal_groups_table");

// Get metals
$metals_table = $wpdb->prefix . 'jpc_metals';
$metals = $wpdb->get_results("SELECT * FROM $metals_table");
$metals_count = $wpdb->get_var("SELECT COUNT(*) FROM $metals_table");

?>

<div class="wrap">
    <h1>Jewellery Price Calculator - Debug Info</h1>
    
    <div class="jpc-card" style="background: #fff; padding: 20px; margin: 20px 0;">
        <h2>Database Tables Status</h2>
        <table class="widefat">
            <thead>
                <tr>
                    <th>Table Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tables_status as $table => $exists): ?>
                <tr>
                    <td><?php echo esc_html($wpdb->prefix . $table); ?></td>
                    <td>
                        <?php if ($exists): ?>
                            <span style="color: green;">✓ Exists</span>
                        <?php else: ?>
                            <span style="color: red;">✗ Missing</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div class="jpc-card" style="background: #fff; padding: 20px; margin: 20px 0;">
        <h2>Metal Groups (<?php echo $metal_groups_count; ?> total)</h2>
        <?php if ($metal_groups): ?>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Unit</th>
                        <th>Making Charge</th>
                        <th>Wastage Charge</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($metal_groups as $group): ?>
                    <tr>
                        <td><?php echo $group->id; ?></td>
                        <td><?php echo esc_html($group->name); ?></td>
                        <td><?php echo esc_html($group->unit); ?></td>
                        <td><?php echo $group->enable_making_charge ? 'Enabled' : 'Disabled'; ?></td>
                        <td><?php echo $group->enable_wastage_charge ? 'Enabled' : 'Disabled'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No metal groups found in database.</p>
        <?php endif; ?>
    </div>
    
    <div class="jpc-card" style="background: #fff; padding: 20px; margin: 20px 0;">
        <h2>Metals (<?php echo $metals_count; ?> total)</h2>
        <?php if ($metals): ?>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Display Name</th>
                        <th>Group ID</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($metals as $metal): ?>
                    <tr>
                        <td><?php echo $metal->id; ?></td>
                        <td><?php echo esc_html($metal->name); ?></td>
                        <td><?php echo esc_html($metal->display_name); ?></td>
                        <td><?php echo $metal->metal_group_id; ?></td>
                        <td>₹<?php echo number_format($metal->price_per_unit, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No metals found in database.</p>
        <?php endif; ?>
    </div>
    
    <div class="jpc-card" style="background: #fff; padding: 20px; margin: 20px 0;">
        <h2>WordPress Info</h2>
        <table class="widefat">
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
                <td><?php echo $wpdb->prefix; ?></td>
            </tr>
            <tr>
                <td><strong>WooCommerce Active:</strong></td>
                <td><?php echo class_exists('WooCommerce') ? 'Yes' : 'No'; ?></td>
            </tr>
        </table>
    </div>
    
    <div class="jpc-card" style="background: #fff; padding: 20px; margin: 20px 0;">
        <h2>Quick Actions</h2>
        <p>
            <a href="<?php echo admin_url('admin.php?page=jpc-metal-groups'); ?>" class="button">Go to Metal Groups</a>
            <a href="<?php echo admin_url('admin.php?page=jpc-metals'); ?>" class="button">Go to Metals</a>
        </p>
        
        <h3>Recreate Tables</h3>
        <p><strong>Warning:</strong> This will drop and recreate all tables. Use only if tables are corrupted.</p>
        <form method="post" onsubmit="return confirm('Are you sure? This will delete all data!');">
            <?php wp_nonce_field('jpc_recreate_tables'); ?>
            <input type="hidden" name="jpc_action" value="recreate_tables">
            <button type="submit" class="button button-secondary">Recreate Tables</button>
        </form>
        
        <?php
        if (isset($_POST['jpc_action']) && $_POST['jpc_action'] === 'recreate_tables') {
            check_admin_referer('jpc_recreate_tables');
            
            // Drop tables
            JPC_Database::drop_tables();
            
            // Recreate tables
            JPC_Database::create_tables();
            
            echo '<div class="notice notice-success"><p>Tables recreated successfully! Please refresh this page.</p></div>';
        }
        ?>
    </div>
</div>
