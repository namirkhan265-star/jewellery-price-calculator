<?php
/**
 * Metals Management Page Template v2.0.0
 * Enhanced with Making Charges per Gram
 */

if (!defined('ABSPATH')) {
    exit;
}

// Handle bulk price update
if (isset($_POST['jpc_bulk_update_prices']) && check_admin_referer('jpc_bulk_update_prices')) {
    $updated = 0;
    $errors = 0;
    
    // Get all products with JPC data
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_jpc_metal_id',
                'compare' => 'EXISTS'
            )
        )
    );
    
    $products = get_posts($args);
    
    foreach ($products as $product) {
        $result = JPC_Price_Calculator::calculate_and_update_price($product->ID);
        if ($result !== false) {
            $updated++;
        } else {
            $errors++;
        }
    }
    
    echo '<div class="notice notice-success is-dismissible"><p>';
    printf(__('Bulk price update completed! Updated: %d products. Errors: %d products.', 'jewellery-price-calc'), $updated, $errors);
    echo '</p></div>';
}

$metals = JPC_Metals::get_all();
$metal_groups = JPC_Metal_Groups::get_all();
?>

<div class="wrap jpc-admin-wrap">
    <h1><?php _e('Manage Metals', 'jewellery-price-calc'); ?></h1>
    
    <!-- Info Notice -->
    <div class="notice notice-info" style="margin: 15px 0; padding: 15px; background: #e7f3ff; border-left: 4px solid #2196f3;">
        <h3 style="margin-top: 0;">
            <span class="dashicons dashicons-info" style="color: #2196f3;"></span>
            Making Charges Per Gram (v2.0.0)
        </h3>
        <p><strong>New Feature:</strong> You can now set making charges per gram for each metal. This will be used to auto-calculate making charges on product pages.</p>
        <p><strong>Formula:</strong> Making Charges = Metal Weight (grams) × Making Charges per Gram (₹)</p>
        <p><strong>Example:</strong> If you set ₹50/gram and product has 10 grams of gold, making charges will be auto-calculated as ₹500</p>
    </div>
    
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
                            <p class="description"><?php _e('Current market price per gram', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    <tr style="background: #f0f6fc;">
                        <th><label for="making_charges_per_gram"><?php _e('Making Charges per Gram (₹)', 'jewellery-price-calc'); ?> <span style="color: #2196f3;">★ NEW</span></label></th>
                        <td>
                            <input type="number" id="making_charges_per_gram" name="making_charges_per_gram" class="regular-text" step="0.01" min="0" value="0">
                            <p class="description">
                                <span class="dashicons dashicons-info" style="color: #2196f3;"></span>
                                <?php _e('This will be used to auto-calculate making charges: Metal Weight × This Value', 'jewellery-price-calc'); ?>
                            </p>
                            <p class="description">
                                <strong><?php _e('Example:', 'jewellery-price-calc'); ?></strong> 
                                <?php _e('If you enter ₹50 and product has 10 grams, making charges = ₹500', 'jewellery-price-calc'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <button type="submit" class="button button-primary">
                        <span class="dashicons dashicons-plus-alt" style="margin-top: 3px;"></span>
                        <?php _e('Add Metal', 'jewellery-price-calc'); ?>
                    </button>
                </p>
            </form>
        </div>
        
        <!-- Metals List -->
        <div class="jpc-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h2 style="margin: 0;"><?php _e('Existing Metals', 'jewellery-price-calc'); ?></h2>
                
                <!-- Bulk Update Button -->
                <form method="post" style="margin: 0;">
                    <?php wp_nonce_field('jpc_bulk_update_prices'); ?>
                    <button type="submit" name="jpc_bulk_update_prices" class="button button-secondary">
                        <span class="dashicons dashicons-update" style="margin-top: 3px;"></span>
                        <?php _e('Bulk Update All Product Prices', 'jewellery-price-calc'); ?>
                    </button>
                </form>
            </div>
            
            <?php if (empty($metals)): ?>
                <p><?php _e('No metals found. Add your first metal using the form above.', 'jewellery-price-calc'); ?></p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width: 50px;"><?php _e('ID', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Name', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Display Name', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Group', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Price/Unit', 'jewellery-price-calc'); ?></th>
                            <th style="background: #f0f6fc;">
                                <?php _e('Making Charges/Gram', 'jewellery-price-calc'); ?>
                                <span style="color: #2196f3;">★</span>
                            </th>
                            <th><?php _e('Actions', 'jewellery-price-calc'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($metals as $metal): ?>
                        <tr>
                            <td><?php echo $metal->id; ?></td>
                            <td><strong><?php echo esc_html($metal->name); ?></strong></td>
                            <td><?php echo esc_html($metal->display_name); ?></td>
                            <td><?php echo esc_html($metal->group_name); ?></td>
                            <td><strong>₹<?php echo number_format($metal->price_per_unit, 2); ?></strong></td>
                            <td style="background: #f9fcff;">
                                <strong style="color: #2196f3;">₹<?php echo number_format($metal->making_charges_per_gram ?? 0, 2); ?></strong>
                                <?php if (($metal->making_charges_per_gram ?? 0) > 0): ?>
                                    <br><small style="color: #666;">Auto-calc enabled</small>
                                <?php else: ?>
                                    <br><small style="color: #999;">Not set</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="button button-small jpc-edit-metal" 
                                        data-id="<?php echo $metal->id; ?>"
                                        data-name="<?php echo esc_attr($metal->name); ?>"
                                        data-display-name="<?php echo esc_attr($metal->display_name); ?>"
                                        data-group-id="<?php echo esc_attr($metal->metal_group_id); ?>"
                                        data-price="<?php echo esc_attr($metal->price_per_unit); ?>"
                                        data-making-charges="<?php echo esc_attr($metal->making_charges_per_gram ?? 0); ?>">
                                    <span class="dashicons dashicons-edit"></span>
                                    <?php _e('Edit', 'jewellery-price-calc'); ?>
                                </button>
                                <button type="button" class="button button-small button-link-delete jpc-delete-metal" 
                                        data-id="<?php echo $metal->id; ?>">
                                    <span class="dashicons dashicons-trash"></span>
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

<!-- Edit Metal Modal -->
<div id="jpc-edit-metal-modal" class="jpc-modal" style="display: none;">
    <div class="jpc-modal-content">
        <span class="jpc-modal-close">&times;</span>
        <h2><?php _e('Edit Metal', 'jewellery-price-calc'); ?></h2>
        
        <form id="jpc-edit-metal-form">
            <input type="hidden" id="edit_metal_id" name="id">
            
            <table class="form-table">
                <tr>
                    <th><label for="edit_metal_name"><?php _e('Metal Name', 'jewellery-price-calc'); ?></label></th>
                    <td>
                        <input type="text" id="edit_metal_name" name="name" class="regular-text" required>
                    </td>
                </tr>
                <tr>
                    <th><label for="edit_metal_display_name"><?php _e('Metal Display Name', 'jewellery-price-calc'); ?></label></th>
                    <td>
                        <input type="text" id="edit_metal_display_name" name="display_name" class="regular-text" required>
                    </td>
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
                    <td>
                        <input type="number" id="edit_metal_price" name="price_per_unit" class="regular-text" step="0.01" min="0" required>
                    </td>
                </tr>
                <tr style="background: #f0f6fc;">
                    <th><label for="edit_making_charges_per_gram"><?php _e('Making Charges per Gram (₹)', 'jewellery-price-calc'); ?></label></th>
                    <td>
                        <input type="number" id="edit_making_charges_per_gram" name="making_charges_per_gram" class="regular-text" step="0.01" min="0" value="0">
                        <p class="description">
                            <?php _e('Auto-calculate making charges: Metal Weight × This Value', 'jewellery-price-calc'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" class="button button-primary">
                    <?php _e('Update Metal', 'jewellery-price-calc'); ?>
                </button>
            </p>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Add metal
    $('#jpc-add-metal-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_add_metal',
                nonce: jpcAdmin.nonce,
                name: $('#metal_name').val(),
                display_name: $('#metal_display_name').val(),
                metal_group_id: $('#metal_group').val(),
                price_per_unit: $('#metal_price').val(),
                making_charges_per_gram: $('#making_charges_per_gram').val() || 0
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'Error adding metal');
                }
            }
        });
    });
    
    // Edit metal
    $('.jpc-edit-metal').on('click', function() {
        var $btn = $(this);
        
        $('#edit_metal_id').val($btn.data('id'));
        $('#edit_metal_name').val($btn.data('name'));
        $('#edit_metal_display_name').val($btn.data('display-name'));
        $('#edit_metal_group').val($btn.data('group-id'));
        $('#edit_metal_price').val($btn.data('price'));
        $('#edit_making_charges_per_gram').val($btn.data('making-charges') || 0);
        
        $('#jpc-edit-metal-modal').show();
    });
    
    // Update metal
    $('#jpc-edit-metal-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_update_metal',
                nonce: jpcAdmin.nonce,
                id: $('#edit_metal_id').val(),
                name: $('#edit_metal_name').val(),
                display_name: $('#edit_metal_display_name').val(),
                metal_group_id: $('#edit_metal_group').val(),
                price_per_unit: $('#edit_metal_price').val(),
                making_charges_per_gram: $('#edit_making_charges_per_gram').val() || 0
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'Error updating metal');
                }
            }
        });
    });
    
    // Delete metal
    $('.jpc-delete-metal').on('click', function() {
        if (!confirm(jpcAdmin.confirmDelete)) return;
        
        var id = $(this).data('id');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_delete_metal',
                nonce: jpcAdmin.nonce,
                id: id
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'Error deleting metal');
                }
            }
        });
    });
    
    // Close modal
    $('.jpc-modal-close').on('click', function() {
        $(this).closest('.jpc-modal').hide();
    });
});
</script>
