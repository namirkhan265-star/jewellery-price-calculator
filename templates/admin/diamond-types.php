<?php
/**
 * Diamond Types Admin Template (Carat-Based Pricing)
 */

if (!defined('ABSPATH')) {
    exit;
}

$diamond_groups = JPC_Diamond_Groups::get_all();
$diamond_types = JPC_Diamond_Types::get_all();

// Group diamond types by group
$types_by_group = array();
foreach ($diamond_types as $type) {
    if (!isset($types_by_group[$type->diamond_group_id])) {
        $types_by_group[$type->diamond_group_id] = array();
    }
    $types_by_group[$type->diamond_group_id][] = $type;
}
?>

<div class="wrap">
    <h1><?php _e('Diamond Types & Carat Pricing', 'jewellery-price-calc'); ?></h1>
    
    <p class="description">
        <?php _e('Set prices for different carat ranges within each diamond group. Larger diamonds typically have higher price per carat.', 'jewellery-price-calc'); ?>
    </p>
    
    <div class="jpc-admin-container">
        <!-- Add New Diamond Type Form -->
        <div class="jpc-card">
            <h2><?php _e('Add New Carat Range', 'jewellery-price-calc'); ?></h2>
            <form id="jpc-add-diamond-type-form" class="jpc-form">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="diamond_group_id"><?php _e('Diamond Group', 'jewellery-price-calc'); ?> *</label>
                        </th>
                        <td>
                            <select id="diamond_group_id" name="diamond_group_id" class="regular-text" required>
                                <option value=""><?php _e('Select Diamond Group', 'jewellery-price-calc'); ?></option>
                                <?php foreach ($diamond_groups as $group): ?>
                                    <option value="<?php echo esc_attr($group->id); ?>">
                                        <?php echo esc_html($group->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="carat_from"><?php _e('Carat From', 'jewellery-price-calc'); ?> *</label>
                        </th>
                        <td>
                            <input type="number" id="carat_from" name="carat_from" class="small-text" step="0.001" min="0" required>
                            <p class="description"><?php _e('Starting carat weight (e.g., 0.00, 0.50, 1.00)', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="carat_to"><?php _e('Carat To', 'jewellery-price-calc'); ?> *</label>
                        </th>
                        <td>
                            <input type="number" id="carat_to" name="carat_to" class="small-text" step="0.001" min="0" required>
                            <p class="description"><?php _e('Ending carat weight (e.g., 0.50, 1.00, 2.00). Use 999.99 for unlimited.', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="price_per_carat"><?php _e('Price Per Carat', 'jewellery-price-calc'); ?> *</label>
                        </th>
                        <td>
                            <input type="number" id="price_per_carat" name="price_per_carat" class="regular-text" step="0.01" min="0" required>
                            <p class="description"><?php _e('Base price per carat for this range (before certification adjustments)', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="display_name"><?php _e('Display Name', 'jewellery-price-calc'); ?> *</label>
                        </th>
                        <td>
                            <input type="text" id="display_name" name="display_name" class="regular-text" required>
                            <p class="description"><?php _e('e.g., "Natural Diamond (0.50-1.00ct)"', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary">
                        <?php _e('Add Carat Range', 'jewellery-price-calc'); ?>
                    </button>
                </p>
            </form>
        </div>
        
        <!-- Existing Diamond Types by Group -->
        <?php foreach ($diamond_groups as $group): ?>
            <div class="jpc-card">
                <h2><?php echo esc_html($group->name); ?> - <?php _e('Carat Pricing', 'jewellery-price-calc'); ?></h2>
                
                <?php if (!isset($types_by_group[$group->id]) || empty($types_by_group[$group->id])): ?>
                    <p><?php _e('No carat ranges defined for this diamond group yet.', 'jewellery-price-calc'); ?></p>
                <?php else: ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th style="width: 60px;"><?php _e('ID', 'jewellery-price-calc'); ?></th>
                                <th><?php _e('Carat Range', 'jewellery-price-calc'); ?></th>
                                <th><?php _e('Price/Carat', 'jewellery-price-calc'); ?></th>
                                <th><?php _e('Display Name', 'jewellery-price-calc'); ?></th>
                                <th style="width: 150px;"><?php _e('Actions', 'jewellery-price-calc'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($types_by_group[$group->id] as $type): ?>
                                <tr data-id="<?php echo esc_attr($type->id); ?>">
                                    <td><?php echo esc_html($type->id); ?></td>
                                    <td>
                                        <strong><?php echo number_format($type->carat_from, 3); ?> - <?php echo number_format($type->carat_to, 3); ?> ct</strong>
                                    </td>
                                    <td>
                                        <?php echo wc_price($type->price_per_carat); ?>
                                    </td>
                                    <td><?php echo esc_html($type->display_name); ?></td>
                                    <td>
                                        <button class="button button-small jpc-edit-diamond-type" 
                                                data-id="<?php echo esc_attr($type->id); ?>"
                                                data-group-id="<?php echo esc_attr($type->diamond_group_id); ?>"
                                                data-carat-from="<?php echo esc_attr($type->carat_from); ?>"
                                                data-carat-to="<?php echo esc_attr($type->carat_to); ?>"
                                                data-price="<?php echo esc_attr($type->price_per_carat); ?>"
                                                data-name="<?php echo esc_attr($type->display_name); ?>">
                                            <?php _e('Edit', 'jewellery-price-calc'); ?>
                                        </button>
                                        <button class="button button-small jpc-delete-diamond-type" data-id="<?php echo esc_attr($type->id); ?>">
                                            <?php _e('Delete', 'jewellery-price-calc'); ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Edit Diamond Type Modal -->
<div id="jpc-edit-diamond-type-modal" class="jpc-modal" style="display: none;">
    <div class="jpc-modal-content">
        <span class="jpc-modal-close">&times;</span>
        <h2><?php _e('Edit Carat Range', 'jewellery-price-calc'); ?></h2>
        <form id="jpc-edit-diamond-type-form" class="jpc-form">
            <input type="hidden" id="edit_diamond_type_id" name="id">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="edit_diamond_group_id"><?php _e('Diamond Group', 'jewellery-price-calc'); ?> *</label>
                    </th>
                    <td>
                        <select id="edit_diamond_group_id" name="diamond_group_id" class="regular-text" required>
                            <?php foreach ($diamond_groups as $group): ?>
                                <option value="<?php echo esc_attr($group->id); ?>">
                                    <?php echo esc_html($group->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="edit_carat_from"><?php _e('Carat From', 'jewellery-price-calc'); ?> *</label>
                    </th>
                    <td>
                        <input type="number" id="edit_carat_from" name="carat_from" class="small-text" step="0.001" min="0" required>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="edit_carat_to"><?php _e('Carat To', 'jewellery-price-calc'); ?> *</label>
                    </th>
                    <td>
                        <input type="number" id="edit_carat_to" name="carat_to" class="small-text" step="0.001" min="0" required>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="edit_price_per_carat"><?php _e('Price Per Carat', 'jewellery-price-calc'); ?> *</label>
                    </th>
                    <td>
                        <input type="number" id="edit_price_per_carat" name="price_per_carat" class="regular-text" step="0.01" min="0" required>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="edit_display_name"><?php _e('Display Name', 'jewellery-price-calc'); ?> *</label>
                    </th>
                    <td>
                        <input type="text" id="edit_display_name" name="display_name" class="regular-text" required>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <button type="submit" class="button button-primary">
                    <?php _e('Update Carat Range', 'jewellery-price-calc'); ?>
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
    // Add diamond type
    $('#jpc-add-diamond-type-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: jpcAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_add_diamond_type',
                nonce: jpcAdmin.nonce,
                diamond_group_id: $('#diamond_group_id').val(),
                carat_from: $('#carat_from').val(),
                carat_to: $('#carat_to').val(),
                price_per_carat: $('#price_per_carat').val(),
                display_name: $('#display_name').val()
            },
            success: function(response) {
                if (response.success) {
                    alert('Carat range added successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });
    
    // Edit diamond type
    $('.jpc-edit-diamond-type').on('click', function() {
        var btn = $(this);
        
        $('#edit_diamond_type_id').val(btn.data('id'));
        $('#edit_diamond_group_id').val(btn.data('group-id'));
        $('#edit_carat_from').val(btn.data('carat-from'));
        $('#edit_carat_to').val(btn.data('carat-to'));
        $('#edit_price_per_carat').val(btn.data('price'));
        $('#edit_display_name').val(btn.data('name'));
        
        $('#jpc-edit-diamond-type-modal').show();
    });
    
    // Update diamond type
    $('#jpc-edit-diamond-type-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: jpcAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_update_diamond_type',
                nonce: jpcAdmin.nonce,
                id: $('#edit_diamond_type_id').val(),
                diamond_group_id: $('#edit_diamond_group_id').val(),
                carat_from: $('#edit_carat_from').val(),
                carat_to: $('#edit_carat_to').val(),
                price_per_carat: $('#edit_price_per_carat').val(),
                display_name: $('#edit_display_name').val()
            },
            success: function(response) {
                if (response.success) {
                    alert('Carat range updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });
    
    // Delete diamond type
    $('.jpc-delete-diamond-type').on('click', function() {
        if (!confirm(jpcAdmin.confirmDelete)) {
            return;
        }
        
        var id = $(this).data('id');
        
        $.ajax({
            url: jpcAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_delete_diamond_type',
                nonce: jpcAdmin.nonce,
                id: id
            },
            success: function(response) {
                if (response.success) {
                    alert('Carat range deleted successfully!');
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
