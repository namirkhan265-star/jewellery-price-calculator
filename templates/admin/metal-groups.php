<?php
/**
 * Metal Groups Management Page Template
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get all metal groups
$metal_groups = JPC_Metal_Groups::get_all();

// Debug: Log the count
error_log('JPC: Metal groups count: ' . count($metal_groups));
?>

<div class="wrap jpc-admin-wrap">
    <h1><?php _e('Manage Metal Groups', 'jewellery-price-calc'); ?></h1>
    
    <?php if (isset($_GET['debug'])): ?>
    <div class="notice notice-info">
        <p><strong>Debug Info:</strong></p>
        <p>Metal Groups Count: <?php echo count($metal_groups); ?></p>
        <p>Metal Groups Data: <pre><?php print_r($metal_groups); ?></pre></p>
    </div>
    <?php endif; ?>
    
    <div class="jpc-admin-content">
        <!-- Add New Metal Group Form -->
        <div class="jpc-card">
            <h2><?php _e('Add New Metal Group', 'jewellery-price-calc'); ?></h2>
            
            <form id="jpc-add-metal-group-form" method="post">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="group_name"><?php _e('Metal Group Name', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="group_name" name="name" class="regular-text" required>
                            <p class="description"><?php _e('Metal Group like Gold, Diamond etc.', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="unit"><?php _e('Unit of Measurement', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="unit" name="unit" class="regular-text" required>
                            <p class="description"><?php _e('Unit of Metal Group Weight like gm for gram, ct for carat etc.', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <?php _e('Enable Making Charge', 'jewellery-price-calc'); ?>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="enable_making_charge" value="1" checked>
                                <?php _e('Enable making charge for this metal group', 'jewellery-price-calc'); ?>
                            </label>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <?php _e('Enable Wastage Charge', 'jewellery-price-calc'); ?>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="enable_wastage_charge" value="1" checked>
                                <?php _e('Enable wastage charge for this metal group', 'jewellery-price-calc'); ?>
                            </label>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <button type="submit" class="button button-primary"><?php _e('Add', 'jewellery-price-calc'); ?></button>
                </p>
            </form>
        </div>
        
        <!-- Existing Metal Groups -->
        <div class="jpc-card">
            <h2><?php _e('Existing Metal Groups', 'jewellery-price-calc'); ?></h2>
            
            <?php if (empty($metal_groups)): ?>
                <p><?php _e('No metal groups found.', 'jewellery-price-calc'); ?></p>
                <p><a href="<?php echo admin_url('admin.php?page=jpc-debug'); ?>" class="button">Go to Debug Page</a></p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('ID', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Name', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Unit', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Making Charge', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Wastage Charge', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Actions', 'jewellery-price-calc'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($metal_groups as $group): ?>
                        <tr>
                            <td><?php echo esc_html($group->id); ?></td>
                            <td><?php echo esc_html($group->name); ?></td>
                            <td><?php echo esc_html($group->unit); ?></td>
                            <td>
                                <?php if ($group->enable_making_charge): ?>
                                    <span class="dashicons dashicons-yes" style="color: green;"></span>
                                    <?php echo esc_html(ucfirst($group->making_charge_type)); ?>
                                <?php else: ?>
                                    <span class="dashicons dashicons-no" style="color: red;"></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($group->enable_wastage_charge): ?>
                                    <span class="dashicons dashicons-yes" style="color: green;"></span>
                                    <?php echo esc_html(ucfirst($group->wastage_charge_type)); ?>
                                <?php else: ?>
                                    <span class="dashicons dashicons-no" style="color: red;"></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="button button-small jpc-delete-group" data-id="<?php echo esc_attr($group->id); ?>">
                                    <?php _e('Delete', 'jewellery-price-calc'); ?>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
