<?php
/**
 * Metal Groups Management Page Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$metal_groups = JPC_Metal_Groups::get_all();
?>

<div class="wrap jpc-admin-wrap">
    <h1><?php _e('Manage Metal Groups', 'jewellery-price-calc'); ?></h1>
    
    <div class="jpc-admin-content">
        <!-- Add New Metal Group Form -->
        <div class="jpc-card">
            <h2><?php _e('Add New Metal Group', 'jewellery-price-calc'); ?></h2>
            
            <form id="jpc-add-metal-group-form" class="jpc-form">
                <table class="form-table">
                    <tr>
                        <th><label for="group_name"><?php _e('Metal Group Name', 'jewellery-price-calc'); ?></label></th>
                        <td>
                            <input type="text" id="group_name" name="name" class="regular-text" required>
                            <p class="description"><?php _e('Metal Group like Gold, Diamond etc.', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="unit"><?php _e('Unit of Measurement', 'jewellery-price-calc'); ?></label></th>
                        <td>
                            <input type="text" id="unit" name="unit" class="regular-text" required>
                            <p class="description"><?php _e('Unit of Metal Group Weight like gm for gram, ct for carat etc.', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Enable Making Charge', 'jewellery-price-calc'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="enable_making_charge" value="1">
                                <?php _e('Enable making charge for this metal group', 'jewellery-price-calc'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Enable Wastage Charge', 'jewellery-price-calc'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="enable_wastage_charge" value="1">
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
        
        <!-- Metal Groups List -->
        <div class="jpc-card">
            <h2><?php _e('Existing Metal Groups', 'jewellery-price-calc'); ?></h2>
            
            <?php if (empty($metal_groups)): ?>
                <p><?php _e('No metal groups found.', 'jewellery-price-calc'); ?></p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="15%"><?php _e('Metal Group', 'jewellery-price-calc'); ?></th>
                            <th width="10%"><?php _e('Weight Unit', 'jewellery-price-calc'); ?></th>
                            <th width="25%"><?php _e('Making Charge', 'jewellery-price-calc'); ?></th>
                            <th width="10%"><?php _e('Field', 'jewellery-price-calc'); ?></th>
                            <th width="10%"><?php _e('Type', 'jewellery-price-calc'); ?></th>
                            <th width="25%"><?php _e('Wastage Charge', 'jewellery-price-calc'); ?></th>
                            <th width="10%"><?php _e('Field', 'jewellery-price-calc'); ?></th>
                            <th width="10%"><?php _e('Type', 'jewellery-price-calc'); ?></th>
                            <th width="15%"><?php _e('Action', 'jewellery-price-calc'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($metal_groups as $index => $group): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo esc_html($group->name); ?></td>
                                <td><?php echo esc_html($group->unit); ?></td>
                                <td colspan="2">
                                    <?php echo $group->enable_making_charge ? __('Enabled', 'jewellery-price-calc') : __('Not Enabled', 'jewellery-price-calc'); ?>
                                </td>
                                <td>
                                    <?php echo $group->enable_making_charge ? ucfirst($group->making_charge_type) : '-'; ?>
                                </td>
                                <td colspan="2">
                                    <?php echo $group->enable_wastage_charge ? __('Enabled', 'jewellery-price-calc') : __('Not Enabled', 'jewellery-price-calc'); ?>
                                </td>
                                <td>
                                    <?php echo $group->enable_wastage_charge ? ucfirst($group->wastage_charge_type) : '-'; ?>
                                </td>
                                <td>
                                    <button class="button button-small jpc-edit-group" data-id="<?php echo esc_attr($group->id); ?>">
                                        <span class="dashicons dashicons-edit"></span>
                                    </button>
                                    <button class="button button-small jpc-delete-group" data-id="<?php echo esc_attr($group->id); ?>">
                                        <span class="dashicons dashicons-trash"></span>
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
