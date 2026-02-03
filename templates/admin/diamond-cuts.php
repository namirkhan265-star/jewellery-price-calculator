<?php
/**
 * Diamond Cuts Admin Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$cuts = JPC_Diamond_Cuts::get_all();
?>

<div class="wrap">
    <h1><?php _e('Diamond Cuts', 'jewellery-price-calc'); ?></h1>
    
    <p class="description">
        <?php _e('Manage diamond cut grades and their price adjustments. Cut quality affects brilliance and fire, ranging from Excellent to Poor.', 'jewellery-price-calc'); ?>
    </p>
    
    <div class="jpc-admin-container">
        <!-- Add New Cut Form -->
        <div class="jpc-card">
            <h2><?php _e('Add New Cut Grade', 'jewellery-price-calc'); ?></h2>
            <form id="jpc-add-cut-form" class="jpc-form">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="cut_name"><?php _e('Cut Grade', 'jewellery-price-calc'); ?> *</label>
                        </th>
                        <td>
                            <input type="text" id="cut_name" name="name" class="regular-text" required>
                            <p class="description"><?php _e('e.g., Excellent, Very Good, Good, Fair, Poor', 'jewellery-price-calc'); ?></p>
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
                                <?php _e('For Percentage: Enter 15 for Excellent (+15%), 0 for Good (baseline), -20 for Poor (-20%). For Fixed: Enter amount.', 'jewellery-price-calc'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="cut_description"><?php _e('Description', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <textarea id="cut_description" name="description" class="large-text" rows="3"></textarea>
                            <p class="description"><?php _e('Brief description of this cut grade', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary">
                        <?php _e('Add Cut Grade', 'jewellery-price-calc'); ?>
                    </button>
                </p>
            </form>
        </div>
        
        <!-- Existing Cuts -->
        <div class="jpc-card">
            <h2><?php _e('Existing Cut Grades', 'jewellery-price-calc'); ?></h2>
            
            <?php if (empty($cuts)): ?>
                <p><?php _e('No cut grades found. Add your first cut grade above.', 'jewellery-price-calc'); ?></p>
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
                        <?php foreach ($cuts as $cut): ?>
                            <?php
                            // Calculate example impact
                            $base_price = 25000;
                            if ($cut->adjustment_type === 'percentage') {
                                $adjusted_price = $base_price * (1 + ($cut->adjustment_value / 100));
                                $impact = ($cut->adjustment_value >= 0 ? '+' : '') . number_format($cut->adjustment_value, 2) . '%';
                            } else {
                                $adjusted_price = $base_price + $cut->adjustment_value;
                                $impact = ($cut->adjustment_value >= 0 ? '+' : '') . wc_price($cut->adjustment_value);
                            }
                            $example = wc_price($base_price) . ' → ' . wc_price($adjusted_price);
                            ?>
                            <tr>
                                <td><?php echo esc_html($cut->id); ?></td>
                                <td><strong><?php echo esc_html($cut->name); ?></strong></td>
                                <td><?php echo esc_html(ucfirst($cut->adjustment_type)); ?></td>
                                <td><?php echo $impact; ?></td>
                                <td><?php echo $example; ?></td>
                                <td><?php echo esc_html($cut->description); ?></td>
                                <td>
                                    <button class="button button-small jpc-edit-cut" data-id="<?php echo esc_attr($cut->id); ?>">
                                        <?php _e('Edit', 'jewellery-price-calc'); ?>
                                    </button>
                                    <button class="button button-small jpc-delete-cut" data-id="<?php echo esc_attr($cut->id); ?>">
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

<!-- Edit Cut Modal -->
<div id="jpc-edit-cut-modal" class="jpc-modal">
    <div class="jpc-modal-content">
        <span class="jpc-modal-close">&times;</span>
        <h2><?php _e('Edit Cut Grade', 'jewellery-price-calc'); ?></h2>
        <form id="jpc-edit-cut-form" class="jpc-form">
            <input type="hidden" id="edit_cut_id" name="id">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="edit_cut_name"><?php _e('Cut Grade', 'jewellery-price-calc'); ?> *</label>
                    </th>
                    <td>
                        <input type="text" id="edit_cut_name" name="name" class="regular-text" required>
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
                        <label for="edit_cut_description"><?php _e('Description', 'jewellery-price-calc'); ?></label>
                    </th>
                    <td>
                        <textarea id="edit_cut_description" name="description" class="large-text" rows="3"></textarea>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <button type="submit" class="button button-primary">
                    <?php _e('Update Cut Grade', 'jewellery-price-calc'); ?>
                </button>
            </p>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Add cut
    $('#jpc-add-cut-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_add_diamond_cut',
                nonce: jpcAdmin.nonce,
                name: $('#cut_name').val(),
                adjustment_type: $('#adjustment_type').val(),
                adjustment_value: $('#adjustment_value').val(),
                description: $('#cut_description').val()
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'Error adding cut');
                }
            }
        });
    });
    
    // Edit cut
    $('.jpc-edit-cut').on('click', function() {
        var id = $(this).data('id');
        var row = $(this).closest('tr');
        
        $('#edit_cut_id').val(id);
        $('#edit_cut_name').val(row.find('td:eq(1)').text().trim());
        $('#edit_cut_description').val(row.find('td:eq(5)').text().trim());
        
        $('#jpc-edit-cut-modal').show();
    });
    
    // Update cut
    $('#jpc-edit-cut-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_update_diamond_cut',
                nonce: jpcAdmin.nonce,
                id: $('#edit_cut_id').val(),
                name: $('#edit_cut_name').val(),
                adjustment_type: $('#edit_adjustment_type').val(),
                adjustment_value: $('#edit_adjustment_value').val(),
                description: $('#edit_cut_description').val()
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'Error updating cut');
                }
            }
        });
    });
    
    // Delete cut
    $('.jpc-delete-cut').on('click', function() {
        if (!confirm(jpcAdmin.confirmDelete)) return;
        
        var id = $(this).data('id');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_delete_diamond_cut',
                nonce: jpcAdmin.nonce,
                id: id
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'Error deleting cut');
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
