<?php
/**
 * Metal Groups Management Page Template v2.5.29
 * v2.5.29: Added Edit functionality with enable/disable for making/wastage charges
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
                            <p class="description"><?php _e('Metal Group like Gold, Silver, Platinum etc.', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="unit"><?php _e('Unit of Measurement', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="unit" name="unit" class="regular-text" required>
                            <p class="description"><?php _e('Unit of Metal Group Weight like gm for gram, kg for kilogram etc.', 'jewellery-price-calc'); ?></p>
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
                            <p class="description"><?php _e('When disabled, making charge fields will be hidden in product editor and frontend.', 'jewellery-price-calc'); ?></p>
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
                            <p class="description"><?php _e('When disabled, wastage charge fields will be hidden in product editor and frontend.', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <button type="submit" class="button button-primary"><?php _e('Add Metal Group', 'jewellery-price-calc'); ?></button>
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
                            <th style="width: 60px;"><?php _e('ID', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Name', 'jewellery-price-calc'); ?></th>
                            <th style="width: 80px;"><?php _e('Unit', 'jewellery-price-calc'); ?></th>
                            <th style="width: 150px;"><?php _e('Making Charge', 'jewellery-price-calc'); ?></th>
                            <th style="width: 150px;"><?php _e('Wastage Charge', 'jewellery-price-calc'); ?></th>
                            <th style="width: 180px;"><?php _e('Actions', 'jewellery-price-calc'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($metal_groups as $group): ?>
                        <tr data-group-id="<?php echo esc_attr($group->id); ?>">
                            <td><?php echo esc_html($group->id); ?></td>
                            <td class="group-name"><?php echo esc_html($group->name); ?></td>
                            <td class="group-unit"><?php echo esc_html($group->unit); ?></td>
                            <td class="making-charge-status">
                                <?php if ($group->enable_making_charge): ?>
                                    <span class="dashicons dashicons-yes" style="color: green;"></span>
                                    <?php echo esc_html(ucfirst($group->making_charge_type)); ?>
                                <?php else: ?>
                                    <span class="dashicons dashicons-no" style="color: red;"></span>
                                    <span style="color: #999;">Disabled</span>
                                <?php endif; ?>
                            </td>
                            <td class="wastage-charge-status">
                                <?php if ($group->enable_wastage_charge): ?>
                                    <span class="dashicons dashicons-yes" style="color: green;"></span>
                                    <?php echo esc_html(ucfirst($group->wastage_charge_type)); ?>
                                <?php else: ?>
                                    <span class="dashicons dashicons-no" style="color: red;"></span>
                                    <span style="color: #999;">Disabled</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="button button-small jpc-edit-group" 
                                        data-id="<?php echo esc_attr($group->id); ?>"
                                        data-name="<?php echo esc_attr($group->name); ?>"
                                        data-unit="<?php echo esc_attr($group->unit); ?>"
                                        data-enable-making="<?php echo esc_attr($group->enable_making_charge); ?>"
                                        data-enable-wastage="<?php echo esc_attr($group->enable_wastage_charge); ?>">
                                    <?php _e('Edit', 'jewellery-price-calc'); ?>
                                </button>
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

<!-- Edit Metal Group Modal -->
<div id="jpc-edit-group-modal" style="display: none;">
    <div class="jpc-modal-overlay"></div>
    <div class="jpc-modal-content">
        <div class="jpc-modal-header">
            <h2><?php _e('Edit Metal Group', 'jewellery-price-calc'); ?></h2>
            <button class="jpc-modal-close">&times;</button>
        </div>
        <div class="jpc-modal-body">
            <form id="jpc-edit-metal-group-form">
                <input type="hidden" id="edit_group_id" name="id">
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="edit_group_name"><?php _e('Metal Group Name', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="edit_group_name" name="name" class="regular-text" required>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="edit_unit"><?php _e('Unit of Measurement', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="edit_unit" name="unit" class="regular-text" required>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <?php _e('Enable Making Charge', 'jewellery-price-calc'); ?>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" id="edit_enable_making_charge" name="enable_making_charge" value="1">
                                <?php _e('Enable making charge for this metal group', 'jewellery-price-calc'); ?>
                            </label>
                            <p class="description"><?php _e('When disabled, making charge fields will be hidden in product editor and frontend.', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <?php _e('Enable Wastage Charge', 'jewellery-price-calc'); ?>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" id="edit_enable_wastage_charge" name="enable_wastage_charge" value="1">
                                <?php _e('Enable wastage charge for this metal group', 'jewellery-price-calc'); ?>
                            </label>
                            <p class="description"><?php _e('When disabled, wastage charge fields will be hidden in product editor and frontend.', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="jpc-modal-footer">
            <button type="button" class="button button-large jpc-modal-close"><?php _e('Cancel', 'jewellery-price-calc'); ?></button>
            <button type="button" class="button button-primary button-large" id="jpc-save-group-edit"><?php _e('Save Changes', 'jewellery-price-calc'); ?></button>
        </div>
    </div>
</div>

<style>
.jpc-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    z-index: 100000;
}

.jpc-modal-content {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 4px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    z-index: 100001;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.jpc-modal-header {
    padding: 20px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.jpc-modal-header h2 {
    margin: 0;
    font-size: 20px;
}

.jpc-modal-close {
    background: none;
    border: none;
    font-size: 28px;
    line-height: 1;
    cursor: pointer;
    color: #666;
    padding: 0;
    width: 30px;
    height: 30px;
}

.jpc-modal-close:hover {
    color: #000;
}

.jpc-modal-body {
    padding: 20px;
}

.jpc-modal-footer {
    padding: 15px 20px;
    border-top: 1px solid #ddd;
    text-align: right;
}

.jpc-modal-footer .button {
    margin-left: 10px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Add Metal Group
    $('#jpc-add-metal-group-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            action: 'jpc_add_metal_group',
            nonce: jpcAdmin.nonce,
            name: $('#group_name').val(),
            unit: $('#unit').val(),
            enable_making_charge: $('input[name="enable_making_charge"]').is(':checked') ? 1 : 0,
            enable_wastage_charge: $('input[name="enable_wastage_charge"]').is(':checked') ? 1 : 0
        };
        
        $.post(ajaxurl, formData, function(response) {
            if (response.success) {
                alert(response.data.message);
                location.reload();
            } else {
                alert(response.data.message);
            }
        });
    });
    
    // Open Edit Modal
    $('.jpc-edit-group').on('click', function() {
        var $btn = $(this);
        var id = $btn.data('id');
        var name = $btn.data('name');
        var unit = $btn.data('unit');
        var enableMaking = $btn.data('enable-making');
        var enableWastage = $btn.data('enable-wastage');
        
        // Populate form
        $('#edit_group_id').val(id);
        $('#edit_group_name').val(name);
        $('#edit_unit').val(unit);
        $('#edit_enable_making_charge').prop('checked', enableMaking == 1);
        $('#edit_enable_wastage_charge').prop('checked', enableWastage == 1);
        
        // Show modal
        $('#jpc-edit-group-modal').fadeIn(200);
    });
    
    // Close Modal
    $('.jpc-modal-close').on('click', function() {
        $('#jpc-edit-group-modal').fadeOut(200);
    });
    
    // Close modal on overlay click
    $('.jpc-modal-overlay').on('click', function() {
        $('#jpc-edit-group-modal').fadeOut(200);
    });
    
    // Save Edit
    $('#jpc-save-group-edit').on('click', function() {
        var formData = {
            action: 'jpc_update_metal_group',
            nonce: jpcAdmin.nonce,
            id: $('#edit_group_id').val(),
            name: $('#edit_group_name').val(),
            unit: $('#edit_unit').val(),
            enable_making_charge: $('#edit_enable_making_charge').is(':checked') ? 1 : 0,
            enable_wastage_charge: $('#edit_enable_wastage_charge').is(':checked') ? 1 : 0
        };
        
        $.post(ajaxurl, formData, function(response) {
            if (response.success) {
                alert(response.data.message);
                location.reload();
            } else {
                alert(response.data.message);
            }
        });
    });
    
    // Delete Metal Group
    $('.jpc-delete-group').on('click', function() {
        if (!confirm('<?php _e('Are you sure you want to delete this metal group?', 'jewellery-price-calc'); ?>')) {
            return;
        }
        
        var id = $(this).data('id');
        
        $.post(ajaxurl, {
            action: 'jpc_delete_metal_group',
            nonce: jpcAdmin.nonce,
            id: id
        }, function(response) {
            if (response.success) {
                alert(response.data.message);
                location.reload();
            } else {
                alert(response.data.message);
            }
        });
    });
});
</script>
