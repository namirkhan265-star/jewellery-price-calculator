<?php
/**
 * Diamond Shapes Admin Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$shapes = JPC_Diamond_Shapes::get_all();
?>

<div class="wrap">
    <h1><?php _e('Diamond Shapes', 'jewellery-price-calc'); ?></h1>
    
    <p class="description">
        <?php _e('Manage diamond shapes and their price adjustments. Different shapes can affect the diamond price based on cutting complexity and demand.', 'jewellery-price-calc'); ?>
    </p>
    
    <div class="jpc-admin-container">
        <!-- Add New Shape Form -->
        <div class="jpc-card">
            <h2><?php _e('Add New Shape', 'jewellery-price-calc'); ?></h2>
            <form id="jpc-add-shape-form" class="jpc-form">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="shape_name"><?php _e('Shape Name', 'jewellery-price-calc'); ?> *</label>
                        </th>
                        <td>
                            <input type="text" id="shape_name" name="name" class="regular-text" required>
                            <p class="description"><?php _e('e.g., Round, Princess, Cushion, Emerald, Oval, Pear, Marquise, Heart', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="adjustment_type"><?php _e('Adjustment Type', 'jewellery-price-calc'); ?> *</label>
                        </th>
                        <td>
                            <select id="adjustment_type" name="adjustment_type" class="regular-text" required>
                                <option value="percentage"><?php _e('Percentage', 'jewellery-price-calc'); ?></option>
                                <option value="fixed"><?php _e('Fixed Amount', 'jewellery-price-calc'); ?></option>
                            </select>
                            <p class="description"><?php _e('Percentage: Multiply base price by (1 + value/100). Fixed: Add value to base price.', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="adjustment_value"><?php _e('Adjustment Value', 'jewellery-price-calc'); ?> *</label>
                        </th>
                        <td>
                            <input type="number" id="adjustment_value" name="adjustment_value" class="regular-text" step="0.01" required>
                            <p class="description">
                                <?php _e('For Percentage: Enter 0 for Round (baseline), -5 for Princess, -10 for Emerald. For Fixed: Enter amount to add/subtract.', 'jewellery-price-calc'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="shape_description"><?php _e('Description', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <textarea id="shape_description" name="description" class="large-text" rows="3"></textarea>
                            <p class="description"><?php _e('Brief description of this shape', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary">
                        <?php _e('Add Shape', 'jewellery-price-calc'); ?>
                    </button>
                </p>
            </form>
        </div>
        
        <!-- Existing Shapes -->
        <div class="jpc-card">
            <h2><?php _e('Existing Shapes', 'jewellery-price-calc'); ?></h2>
            
            <?php if (empty($shapes)): ?>
                <p><?php _e('No shapes found. Add your first shape above.', 'jewellery-price-calc'); ?></p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width: 60px;"><?php _e('ID', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Name', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Type', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Adjustment', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Example Impact', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Description', 'jewellery-price-calc'); ?></th>
                            <th style="width: 150px;"><?php _e('Actions', 'jewellery-price-calc'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($shapes as $shape): ?>
                            <?php
                            // Calculate example impact
                            $base_price = 25000;
                            if ($shape->adjustment_type === 'percentage') {
                                $adjusted_price = $base_price * (1 + ($shape->adjustment_value / 100));
                                $impact = ($shape->adjustment_value >= 0 ? '+' : '') . number_format($shape->adjustment_value, 2) . '%';
                            } else {
                                $adjusted_price = $base_price + $shape->adjustment_value;
                                $impact = ($shape->adjustment_value >= 0 ? '+' : '') . wc_price($shape->adjustment_value);
                            }
                            $example = wc_price($base_price) . ' → ' . wc_price($adjusted_price);
                            ?>
                            <tr>
                                <td><?php echo esc_html($shape->id); ?></td>
                                <td><strong><?php echo esc_html($shape->name); ?></strong></td>
                                <td><?php echo esc_html(ucfirst($shape->adjustment_type)); ?></td>
                                <td><?php echo $impact; ?></td>
                                <td><?php echo $example; ?></td>
                                <td><?php echo esc_html($shape->description); ?></td>
                                <td>
                                    <button class="button button-small jpc-edit-shape" data-id="<?php echo esc_attr($shape->id); ?>">
                                        <?php _e('Edit', 'jewellery-price-calc'); ?>
                                    </button>
                                    <button class="button button-small jpc-delete-shape" data-id="<?php echo esc_attr($shape->id); ?>">
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

<!-- Edit Shape Modal -->
<div id="jpc-edit-shape-modal" class="jpc-modal">
    <div class="jpc-modal-content">
        <span class="jpc-modal-close">&times;</span>
        <h2><?php _e('Edit Shape', 'jewellery-price-calc'); ?></h2>
        <form id="jpc-edit-shape-form" class="jpc-form">
            <input type="hidden" id="edit_shape_id" name="id">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="edit_shape_name"><?php _e('Shape Name', 'jewellery-price-calc'); ?> *</label>
                    </th>
                    <td>
                        <input type="text" id="edit_shape_name" name="name" class="regular-text" required>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="edit_adjustment_type"><?php _e('Adjustment Type', 'jewellery-price-calc'); ?> *</label>
                    </th>
                    <td>
                        <select id="edit_adjustment_type" name="adjustment_type" class="regular-text" required>
                            <option value="percentage"><?php _e('Percentage', 'jewellery-price-calc'); ?></option>
                            <option value="fixed"><?php _e('Fixed Amount', 'jewellery-price-calc'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="edit_adjustment_value"><?php _e('Adjustment Value', 'jewellery-price-calc'); ?> *</label>
                    </th>
                    <td>
                        <input type="number" id="edit_adjustment_value" name="adjustment_value" class="regular-text" step="0.01" required>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="edit_shape_description"><?php _e('Description', 'jewellery-price-calc'); ?></label>
                    </th>
                    <td>
                        <textarea id="edit_shape_description" name="description" class="large-text" rows="3"></textarea>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <button type="submit" class="button button-primary">
                    <?php _e('Update Shape', 'jewellery-price-calc'); ?>
                </button>
            </p>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Add shape
    $('#jpc-add-shape-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_add_diamond_shape',
                nonce: jpcAdmin.nonce,
                name: $('#shape_name').val(),
                adjustment_type: $('#adjustment_type').val(),
                adjustment_value: $('#adjustment_value').val(),
                description: $('#shape_description').val()
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'Error adding shape');
                }
            }
        });
    });
    
    // Edit shape
    $('.jpc-edit-shape').on('click', function() {
        var id = $(this).data('id');
        var row = $(this).closest('tr');
        
        $('#edit_shape_id').val(id);
        $('#edit_shape_name').val(row.find('td:eq(1)').text().trim());
        $('#edit_shape_description').val(row.find('td:eq(5)').text().trim());
        
        $('#jpc-edit-shape-modal').show();
    });
    
    // Update shape
    $('#jpc-edit-shape-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_update_diamond_shape',
                nonce: jpcAdmin.nonce,
                id: $('#edit_shape_id').val(),
                name: $('#edit_shape_name').val(),
                adjustment_type: $('#edit_adjustment_type').val(),
                adjustment_value: $('#edit_adjustment_value').val(),
                description: $('#edit_shape_description').val()
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'Error updating shape');
                }
            }
        });
    });
    
    // Delete shape
    $('.jpc-delete-shape').on('click', function() {
        if (!confirm(jpcAdmin.confirmDelete)) return;
        
        var id = $(this).data('id');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_delete_diamond_shape',
                nonce: jpcAdmin.nonce,
                id: id
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'Error deleting shape');
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
