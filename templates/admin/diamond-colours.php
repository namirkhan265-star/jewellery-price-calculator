<?php
/**
 * Diamond Colours Admin Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$colours = JPC_Diamond_Colours::get_all();
?>

<div class="wrap">
    <h1><?php _e('Diamond Colours', 'jewellery-price-calc'); ?></h1>
    
    <p class="description">
        <?php _e('Manage diamond colour grades and their price adjustments. Colour grades range from D (colorless) to Z (light yellow/brown).', 'jewellery-price-calc'); ?>
    </p>
    
    <div class="jpc-admin-container">
        <!-- Add New Colour Form -->
        <div class="jpc-card">
            <h2><?php _e('Add New Colour Grade', 'jewellery-price-calc'); ?></h2>
            <form id="jpc-add-colour-form" class="jpc-form">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="colour_name"><?php _e('Colour Grade', 'jewellery-price-calc'); ?> *</label>
                        </th>
                        <td>
                            <input type="text" id="colour_name" name="name" class="regular-text" required>
                            <p class="description"><?php _e('e.g., D (Colorless), E, F, G, H, I, J, K-M (Faint)', 'jewellery-price-calc'); ?></p>
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
                                <?php _e('For Percentage: Enter 25 for D grade (+25%), 0 for I grade (baseline), -15 for K-M (-15%). For Fixed: Enter amount.', 'jewellery-price-calc'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="colour_description"><?php _e('Description', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <textarea id="colour_description" name="description" class="large-text" rows="3"></textarea>
                            <p class="description"><?php _e('Brief description of this colour grade', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary">
                        <?php _e('Add Colour Grade', 'jewellery-price-calc'); ?>
                    </button>
                </p>
            </form>
        </div>
        
        <!-- Existing Colours -->
        <div class="jpc-card">
            <h2><?php _e('Existing Colour Grades', 'jewellery-price-calc'); ?></h2>
            
            <?php if (empty($colours)): ?>
                <p><?php _e('No colour grades found. Add your first colour grade above.', 'jewellery-price-calc'); ?></p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width: 60px;"><?php _e('ID', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Grade', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Type', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Adjustment', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Example Impact', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Description', 'jewellery-price-calc'); ?></th>
                            <th style="width: 150px;"><?php _e('Actions', 'jewellery-price-calc'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($colours as $colour): ?>
                            <?php
                            // Calculate example impact
                            $base_price = 25000;
                            if ($colour->adjustment_type === 'percentage') {
                                $adjusted_price = $base_price * (1 + ($colour->adjustment_value / 100));
                                $impact = ($colour->adjustment_value >= 0 ? '+' : '') . number_format($colour->adjustment_value, 2) . '%';
                            } else {
                                $adjusted_price = $base_price + $colour->adjustment_value;
                                $impact = ($colour->adjustment_value >= 0 ? '+' : '') . wc_price($colour->adjustment_value);
                            }
                            $example = wc_price($base_price) . ' → ' . wc_price($adjusted_price);
                            ?>
                            <tr>
                                <td><?php echo esc_html($colour->id); ?></td>
                                <td><strong><?php echo esc_html($colour->name); ?></strong></td>
                                <td><?php echo esc_html(ucfirst($colour->adjustment_type)); ?></td>
                                <td><?php echo $impact; ?></td>
                                <td><?php echo $example; ?></td>
                                <td><?php echo esc_html($colour->description); ?></td>
                                <td>
                                    <button class="button button-small jpc-edit-colour" data-id="<?php echo esc_attr($colour->id); ?>">
                                        <?php _e('Edit', 'jewellery-price-calc'); ?>
                                    </button>
                                    <button class="button button-small jpc-delete-colour" data-id="<?php echo esc_attr($colour->id); ?>">
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

<!-- Edit Colour Modal -->
<div id="jpc-edit-colour-modal" class="jpc-modal">
    <div class="jpc-modal-content">
        <span class="jpc-modal-close">&times;</span>
        <h2><?php _e('Edit Colour Grade', 'jewellery-price-calc'); ?></h2>
        <form id="jpc-edit-colour-form" class="jpc-form">
            <input type="hidden" id="edit_colour_id" name="id">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="edit_colour_name"><?php _e('Colour Grade', 'jewellery-price-calc'); ?> *</label>
                    </th>
                    <td>
                        <input type="text" id="edit_colour_name" name="name" class="regular-text" required>
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
                        <label for="edit_colour_description"><?php _e('Description', 'jewellery-price-calc'); ?></label>
                    </th>
                    <td>
                        <textarea id="edit_colour_description" name="description" class="large-text" rows="3"></textarea>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <button type="submit" class="button button-primary">
                    <?php _e('Update Colour Grade', 'jewellery-price-calc'); ?>
                </button>
            </p>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Add colour
    $('#jpc-add-colour-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_add_diamond_colour',
                nonce: jpcAdmin.nonce,
                name: $('#colour_name').val(),
                adjustment_type: $('#adjustment_type').val(),
                adjustment_value: $('#adjustment_value').val(),
                description: $('#colour_description').val()
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'Error adding colour');
                }
            }
        });
    });
    
    // Edit colour
    $('.jpc-edit-colour').on('click', function() {
        var id = $(this).data('id');
        var row = $(this).closest('tr');
        
        $('#edit_colour_id').val(id);
        $('#edit_colour_name').val(row.find('td:eq(1)').text().trim());
        $('#edit_colour_description').val(row.find('td:eq(5)').text().trim());
        
        $('#jpc-edit-colour-modal').show();
    });
    
    // Update colour
    $('#jpc-edit-colour-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_update_diamond_colour',
                nonce: jpcAdmin.nonce,
                id: $('#edit_colour_id').val(),
                name: $('#edit_colour_name').val(),
                adjustment_type: $('#edit_adjustment_type').val(),
                adjustment_value: $('#edit_adjustment_value').val(),
                description: $('#edit_colour_description').val()
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'Error updating colour');
                }
            }
        });
    });
    
    // Delete colour
    $('.jpc-delete-colour').on('click', function() {
        if (!confirm(jpcAdmin.confirmDelete)) return;
        
        var id = $(this).data('id');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_delete_diamond_colour',
                nonce: jpcAdmin.nonce,
                id: id
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'Error deleting colour');
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
