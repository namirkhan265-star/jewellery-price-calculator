<?php
/**
 * Metals Management Page Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$metals = JPC_Metals::get_all();
$metal_groups = JPC_Metal_Groups::get_all();
?>

<div class="wrap jpc-admin-wrap">
    <h1><?php _e('Manage Metals', 'jewellery-price-calc'); ?></h1>
    
    <div class="jpc-admin-content">
        <!-- Add New Metal Form -->
        <div class="jpc-card">
            <h2><?php _e('Add New Metal', 'jewellery-price-calc'); ?></h2>
            
            <form id="jpc-add-metal-form" class="jpc-form">
                <table class="form-table">
                    <tr>
                        <th><label for="metal_name"><?php _e('Metal Name', 'jewellery-price-calc'); ?></label></th>
                        <td>
                            <input type="text" id="metal_name" name="name" class="regular-text" required>
                            <p class="description"><?php _e('Metal name like 14 kt Gold, 18 kt Gold, IF-VVS1-EF Diamond, VVS-EF Diamond etc.', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="metal_display_name"><?php _e('Metal Display Name', 'jewellery-price-calc'); ?></label></th>
                        <td>
                            <input type="text" id="metal_display_name" name="display_name" class="regular-text" required>
                            <p class="description"><?php _e('This metal display name will be used in front end.', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="metal_group"><?php _e('Metal Group', 'jewellery-price-calc'); ?></label></th>
                        <td>
                            <select id="metal_group" name="metal_group_id" class="regular-text" required>
                                <option value=""><?php _e('Select Metal Group', 'jewellery-price-calc'); ?></option>
                                <?php foreach ($metal_groups as $group): ?>
                                    <option value="<?php echo esc_attr($group->id); ?>">
                                        <?php echo esc_html($group->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="metal_price"><?php _e('Metal Price/gram', 'jewellery-price-calc'); ?></label></th>
                        <td>
                            <input type="number" id="metal_price" name="price_per_unit" class="regular-text" step="0.01" min="0" required>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <button type="submit" class="button button-primary"><?php _e('Add Metal', 'jewellery-price-calc'); ?></button>
                </p>
            </form>
        </div>
        
        <!-- Metals List -->
        <div class="jpc-card">
            <h2><?php _e('Existing Metals', 'jewellery-price-calc'); ?></h2>
            
            <?php if (empty($metals)): ?>
                <p><?php _e('No metals found. Add your first metal above.', 'jewellery-price-calc'); ?></p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="20%"><?php _e('Metal', 'jewellery-price-calc'); ?></th>
                            <th width="20%"><?php _e('Metal Display Name', 'jewellery-price-calc'); ?></th>
                            <th width="15%"><?php _e('Metal Group', 'jewellery-price-calc'); ?></th>
                            <th width="20%"><?php _e('Price/gram', 'jewellery-price-calc'); ?></th>
                            <th width="20%"><?php _e('Action', 'jewellery-price-calc'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($metals as $index => $metal): ?>
                            <tr data-metal-id="<?php echo esc_attr($metal->id); ?>">
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo esc_html($metal->name); ?></td>
                                <td><?php echo esc_html($metal->display_name); ?></td>
                                <td><?php echo esc_html($metal->group_name); ?></td>
                                <td>
                                    <span class="jpc-price-display">₹ <?php echo number_format($metal->price_per_unit, 2); ?></span>
                                    <input type="number" class="jpc-price-edit" value="<?php echo esc_attr($metal->price_per_unit); ?>" step="0.01" style="display:none;">
                                </td>
                                <td>
                                    <button class="button button-small jpc-edit-metal" data-id="<?php echo esc_attr($metal->id); ?>">
                                        <span class="dashicons dashicons-edit"></span> <?php _e('Edit', 'jewellery-price-calc'); ?>
                                    </button>
                                    <button class="button button-small jpc-delete-metal" data-id="<?php echo esc_attr($metal->id); ?>">
                                        <span class="dashicons dashicons-trash"></span> <?php _e('Delete', 'jewellery-price-calc'); ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="jpc-bulk-actions" style="margin-top: 20px;">
                    <h3><?php _e('Bulk Update Prices', 'jewellery-price-calc'); ?></h3>
                    <p class="description"><?php _e('Update multiple metal prices at once. When you update prices, all product prices will be automatically recalculated.', 'jewellery-price-calc'); ?></p>
                    <button type="button" id="jpc-bulk-update-btn" class="button button-primary">
                        <?php _e('Update All Prices', 'jewellery-price-calc'); ?>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Edit Metal Modal -->
<div id="jpc-edit-metal-modal" class="jpc-modal" style="display:none;">
    <div class="jpc-modal-content">
        <span class="jpc-modal-close">&times;</span>
        <h2><?php _e('Edit Metal', 'jewellery-price-calc'); ?></h2>
        
        <form id="jpc-edit-metal-form">
            <input type="hidden" id="edit_metal_id" name="id">
            
            <table class="form-table">
                <tr>
                    <th><label for="edit_metal_name"><?php _e('Metal Name', 'jewellery-price-calc'); ?></label></th>
                    <td><input type="text" id="edit_metal_name" name="name" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="edit_metal_display_name"><?php _e('Metal Display Name', 'jewellery-price-calc'); ?></label></th>
                    <td><input type="text" id="edit_metal_display_name" name="display_name" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="edit_metal_group"><?php _e('Metal Group', 'jewellery-price-calc'); ?></label></th>
                    <td>
                        <select id="edit_metal_group" name="metal_group_id" class="regular-text" required>
                            <?php foreach ($metal_groups as $group): ?>
                                <option value="<?php echo esc_attr($group->id); ?>">
                                    <?php echo esc_html($group->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="edit_metal_price"><?php _e('Metal Price/gram', 'jewellery-price-calc'); ?></label></th>
                    <td><input type="number" id="edit_metal_price" name="price_per_unit" class="regular-text" step="0.01" min="0" required></td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" class="button button-primary"><?php _e('Update Metal', 'jewellery-price-calc'); ?></button>
                <button type="button" class="button jpc-modal-close"><?php _e('Cancel', 'jewellery-price-calc'); ?></button>
            </p>
        </form>
    </div>
</div>
