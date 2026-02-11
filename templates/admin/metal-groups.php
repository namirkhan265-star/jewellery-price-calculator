<?php
/**
 * Metal Groups Management Page Template v2.5.29
 * Added: Edit functionality with enable/disable making/wastage charges
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get all metal groups
$metal_groups = JPC_Metal_Groups::get_all();
?>

<div class="wrap jpc-admin-wrap">
    <h1><?php _e('Manage Metal Groups', 'jewellery-price-calc'); ?></h1>
    
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
                            <p class="description"><?php _e('When disabled, making charge fields will be hidden in product editor', 'jewellery-price-calc'); ?></p>
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
                            <p class="description"><?php _e('When disabled, wastage fields will be hidden in product editor', 'jewellery-price-calc'); ?></p>
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
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width: 50px;"><?php _e('ID', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Name', 'jewellery-price-calc'); ?></th>
                            <th style="width: 80px;"><?php _e('Unit', 'jewellery-price-calc'); ?></th>
                            <th style="width: 150px;"><?php _e('Making Charge', 'jewellery-price-calc'); ?></th>
                            <th style="width: 150px;"><?php _e('Wastage Charge', 'jewellery-price-calc'); ?></th>
                            <th style="width: 150px;"><?php _e('Actions', 'jewellery-price-calc'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($metal_groups as $group): ?>
                        <tr>
                            <td><?php echo esc_html($group->id); ?></td>
                            <td><strong><?php echo esc_html($group->name); ?></strong></td>
                            <td><?php echo esc_html($group->unit); ?></td>
                            <td>
                                <?php if ($group->enable_making_charge): ?>
                                    <span class="dashicons dashicons-yes" style="color: green;"></span>
                                    <?php echo esc_html(ucfirst($group->making_charge_type ?? 'percentage')); ?>
                                <?php else: ?>
                                    <span class="dashicons dashicons-no" style="color: red;"></span>
                                    <span style="color: #999;"><?php _e('Disabled', 'jewellery-price-calc'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($group->enable_wastage_charge): ?>
                                    <span class="dashicons dashicons-yes" style="color: green;"></span>
                                    <?php echo esc_html(ucfirst($group->wastage_charge_type ?? 'percentage')); ?>
                                <?php else: ?>
                                    <span class="dashicons dashicons-no" style="color: red;"></span>
                                    <span style="color: #999;"><?php _e('Disabled', 'jewellery-price-calc'); ?></span>
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
<div id="jpc-edit-metal-group-modal" class="jpc-modal" style="display: none;">
    <div class="jpc-modal-content">
        <div class="jpc-modal-header">
            <h2><?php _e('Edit Metal Group', 'jewellery-price-calc'); ?></h2>
            <span class="jpc-modal-close">&times;</span>
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
                            <p class="description"><?php _e('When disabled, making charge fields will be hidden in product editor', 'jewellery-price-calc'); ?></p>
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
                            <p class="description"><?php _e('When disabled, wastage fields will be hidden in product editor', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="jpc-modal-footer">
            <button type="button" class="button jpc-modal-close"><?php _e('Cancel', 'jewellery-price-calc'); ?></button>
            <button type="button" class="button button-primary" id="jpc-save-edit-group"><?php _e('Save Changes', 'jewellery-price-calc'); ?></button>
        </div>
    </div>
</div>

<style>
.jpc-modal {
    display: none;
    position: fixed;
    z-index: 100000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.jpc-modal-content {
    background-color: #fff;
    margin: 5% auto;
    width: 600px;
    max-width: 90%;
    border-radius: 4px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
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
}

.jpc-modal-close {
    font-size: 28px;
    font-weight: bold;
    color: #aaa;
    cursor: pointer;
}

.jpc-modal-close:hover {
    color: #000;
}

.jpc-modal-body {
    padding: 20px;
}

.jpc-modal-footer {
    padding: 20px;
    border-top: 1px solid #ddd;
    text-align: right;
}

.jpc-modal-footer .button {
    margin-left: 10px;
}
</style>

<script>
jQuery(document).ready(function($) {
    
    // Edit Metal Group
    $('.jpc-edit-group').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var unit = $(this).data('unit');
        var enableMaking = $(this).data('enable-making');
        var enableWastage = $(this).data('enable-wastage');
        
        $('#edit_group_id').val(id);
        $('#edit_group_name').val(name);
        $('#edit_unit').val(unit);
        $('#edit_enable_making_charge').prop('checked', enableMaking == 1);
        $('#edit_enable_wastage_charge').prop('checked', enableWastage == 1);
        
        $('#jpc-edit-metal-group-modal').fadeIn();
    });
    
    // Close Modal
    $('.jpc-modal-close').on('click', function() {
        $('#jpc-edit-metal-group-modal').fadeOut();
    });
    
    // Close on outside click
    $(window).on('click', function(e) {
        if ($(e.target).is('#jpc-edit-metal-group-modal')) {
            $('#jpc-edit-metal-group-modal').fadeOut();
        }
    });
    
    // Save Edit
    $('#jpc-save-edit-group').on('click', function() {
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
                alert('Metal group updated successfully!');
                location.reload();
            } else {
                alert('Error: ' + (response.data || 'Unknown error'));
            }
        });
    });
    
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
                alert('Metal group added successfully!');
                location.reload();
            } else {
                alert('Error: ' + (response.data || 'Unknown error'));
            }
        });
    });
    
    // Delete Metal Group
    $('.jpc-delete-group').on('click', function() {
        if (!confirm('Are you sure you want to delete this metal group?')) {
            return;
        }
        
        var id = $(this).data('id');
        
        $.post(ajaxurl, {
            action: 'jpc_delete_metal_group',
            nonce: jpcAdmin.nonce,
            id: id
        }, function(response) {
            if (response.success) {
                alert('Metal group deleted successfully!');
                location.reload();
            } else {
                alert('Error: ' + (response.data || 'Unknown error'));
            }
        });
    });
});
</script>
