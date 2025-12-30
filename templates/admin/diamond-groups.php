<?php
/**
 * Diamond Groups Admin Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$diamond_groups = JPC_Diamond_Groups::get_all();
?>

<div class="wrap">
    <h1><?php _e('Diamond Groups', 'jewellery-price-calc'); ?></h1>
    
    <p class="description">
        <?php _e('Manage diamond categories (Natural, Lab Grown, Moissanite, etc.). Each group can have multiple carat ranges with different prices.', 'jewellery-price-calc'); ?>
    </p>
    
    <div class="jpc-admin-container">
        <!-- Add New Diamond Group Form -->
        <div class="jpc-card">
            <h2><?php _e('Add New Diamond Group', 'jewellery-price-calc'); ?></h2>
            <form id="jpc-add-diamond-group-form" class="jpc-form">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="diamond_group_name"><?php _e('Group Name', 'jewellery-price-calc'); ?> *</label>
                        </th>
                        <td>
                            <input type="text" id="diamond_group_name" name="name" class="regular-text" required>
                            <p class="description"><?php _e('e.g., Natural Diamond, Lab Grown Diamond, Moissanite, Cubic Zirconia', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="diamond_group_description"><?php _e('Description', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <textarea id="diamond_group_description" name="description" class="large-text" rows="3"></textarea>
                            <p class="description"><?php _e('Brief description of this diamond type', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary">
                        <?php _e('Add Diamond Group', 'jewellery-price-calc'); ?>
                    </button>
                </p>
            </form>
        </div>
        
        <!-- Existing Diamond Groups -->
        <div class="jpc-card">
            <h2><?php _e('Existing Diamond Groups', 'jewellery-price-calc'); ?></h2>
            
            <?php if (empty($diamond_groups)): ?>
                <p><?php _e('No diamond groups found. Add your first diamond group above.', 'jewellery-price-calc'); ?></p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width: 60px;"><?php _e('ID', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Name', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Slug', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Description', 'jewellery-price-calc'); ?></th>
                            <th style="width: 150px;"><?php _e('Actions', 'jewellery-price-calc'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($diamond_groups as $group): ?>
                            <tr data-id="<?php echo esc_attr($group->id); ?>">
                                <td><?php echo esc_html($group->id); ?></td>
                                <td>
                                    <strong><?php echo esc_html($group->name); ?></strong>
                                </td>
                                <td>
                                    <code><?php echo esc_html($group->slug); ?></code>
                                </td>
                                <td><?php echo esc_html($group->description); ?></td>
                                <td>
                                    <button class="button button-small jpc-edit-diamond-group" data-id="<?php echo esc_attr($group->id); ?>">
                                        <?php _e('Edit', 'jewellery-price-calc'); ?>
                                    </button>
                                    <button class="button button-small jpc-delete-diamond-group" data-id="<?php echo esc_attr($group->id); ?>">
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

<!-- Edit Diamond Group Modal -->
<div id="jpc-edit-diamond-group-modal" class="jpc-modal" style="display: none;">
    <div class="jpc-modal-content">
        <span class="jpc-modal-close">&times;</span>
        <h2><?php _e('Edit Diamond Group', 'jewellery-price-calc'); ?></h2>
        <form id="jpc-edit-diamond-group-form" class="jpc-form">
            <input type="hidden" id="edit_diamond_group_id" name="id">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="edit_diamond_group_name"><?php _e('Group Name', 'jewellery-price-calc'); ?> *</label>
                    </th>
                    <td>
                        <input type="text" id="edit_diamond_group_name" name="name" class="regular-text" required>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="edit_diamond_group_description"><?php _e('Description', 'jewellery-price-calc'); ?></label>
                    </th>
                    <td>
                        <textarea id="edit_diamond_group_description" name="description" class="large-text" rows="3"></textarea>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <button type="submit" class="button button-primary">
                    <?php _e('Update Diamond Group', 'jewellery-price-calc'); ?>
                </button>
                <button type="button" class="button jpc-modal-close">
                    <?php _e('Cancel', 'jewellery-price-calc'); ?>
                </button>
            </p>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Add diamond group
    $('#jpc-add-diamond-group-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: jpcAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_add_diamond_group',
                nonce: jpcAdmin.nonce,
                name: $('#diamond_group_name').val(),
                description: $('#diamond_group_description').val()
            },
            success: function(response) {
                if (response.success) {
                    alert('Diamond group added successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });
    
    // Edit diamond group
    $('.jpc-edit-diamond-group').on('click', function() {
        var id = $(this).data('id');
        var row = $(this).closest('tr');
        
        $('#edit_diamond_group_id').val(id);
        $('#edit_diamond_group_name').val(row.find('strong').text());
        $('#edit_diamond_group_description').val(row.find('td:eq(3)').text());
        
        $('#jpc-edit-diamond-group-modal').show();
    });
    
    // Update diamond group
    $('#jpc-edit-diamond-group-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: jpcAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_update_diamond_group',
                nonce: jpcAdmin.nonce,
                id: $('#edit_diamond_group_id').val(),
                name: $('#edit_diamond_group_name').val(),
                description: $('#edit_diamond_group_description').val()
            },
            success: function(response) {
                if (response.success) {
                    alert('Diamond group updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });
    
    // Delete diamond group
    $('.jpc-delete-diamond-group').on('click', function() {
        if (!confirm(jpcAdmin.confirmDelete)) {
            return;
        }
        
        var id = $(this).data('id');
        
        $.ajax({
            url: jpcAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_delete_diamond_group',
                nonce: jpcAdmin.nonce,
                id: id
            },
            success: function(response) {
                if (response.success) {
                    alert('Diamond group deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });
    
    // Modal close
    $('.jpc-modal-close').on('click', function() {
        $('.jpc-modal').hide();
    });
});
</script>

<style>
.jpc-admin-container {
    max-width: 1200px;
}

.jpc-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    margin: 20px 0;
    padding: 20px;
}

.jpc-card h2 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

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
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    border-radius: 4px;
}

.jpc-modal-close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.jpc-modal-close:hover {
    color: #000;
}
</style>
