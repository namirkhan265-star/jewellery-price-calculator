<?php
/**
 * Diamond Clarities Admin Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$clarities = JPC_Diamond_Clarities::get_all();
?>

<div class="wrap">
    <h1><?php _e('Diamond Clarities', 'jewellery-price-calc'); ?></h1>
    
    <p class="description">
        <?php _e('Manage diamond clarity grades and their price adjustments. Clarity grades range from FL (Flawless) to I3 (Included).', 'jewellery-price-calc'); ?>
    </p>
    
    <div class="jpc-admin-container">
        <!-- Add New Clarity Form -->
        <div class="jpc-card">
            <h2><?php _e('Add New Clarity Grade', 'jewellery-price-calc'); ?></h2>
            <form id="jpc-add-clarity-form" class="jpc-form">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="clarity_name"><?php _e('Clarity Grade', 'jewellery-price-calc'); ?> *</label>
                        </th>
                        <td>
                            <input type="text" id="clarity_name" name="name" class="regular-text" required>
                            <p class="description"><?php _e('e.g., FL, IF, VVS1, VVS2, VS1, VS2, SI1, SI2, I1-I3', 'jewellery-price-calc'); ?></p>
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
                                <?php _e('For Percentage: Enter 30 for FL (+30%), 0 for SI1 (baseline), -25 for I1-I3 (-25%). For Fixed: Enter amount.', 'jewellery-price-calc'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="clarity_description"><?php _e('Description', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <textarea id="clarity_description" name="description" class="large-text" rows="3"></textarea>
                            <p class="description"><?php _e('Brief description of this clarity grade', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary">
                        <?php _e('Add Clarity Grade', 'jewellery-price-calc'); ?>
                    </button>
                </p>
            </form>
        </div>
        
        <!-- Existing Clarities -->
        <div class="jpc-card">
            <h2><?php _e('Existing Clarity Grades', 'jewellery-price-calc'); ?></h2>
            
            <?php if (empty($clarities)): ?>
                <p><?php _e('No clarity grades found. Add your first clarity grade above.', 'jewellery-price-calc'); ?></p>
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
                        <?php foreach ($clarities as $clarity): ?>
                            <?php
                            // Calculate example impact
                            $base_price = 25000;
                            if ($clarity->adjustment_type === 'percentage') {
                                $adjusted_price = $base_price * (1 + ($clarity->adjustment_value / 100));
                                $impact = ($clarity->adjustment_value >= 0 ? '+' : '') . number_format($clarity->adjustment_value, 2) . '%';
                            } else {
                                $adjusted_price = $base_price + $clarity->adjustment_value;
                                $impact = ($clarity->adjustment_value >= 0 ? '+' : '') . wc_price($clarity->adjustment_value);
                            }
                            $example = wc_price($base_price) . ' → ' . wc_price($adjusted_price);
                            ?>
                            <tr>
                                <td><?php echo esc_html($clarity->id); ?></td>
                                <td><strong><?php echo esc_html($clarity->name); ?></strong></td>
                                <td><?php echo esc_html(ucfirst($clarity->adjustment_type)); ?></td>
                                <td><?php echo $impact; ?></td>
                                <td><?php echo $example; ?></td>
                                <td><?php echo esc_html($clarity->description); ?></td>
                                <td>
                                    <button class="button button-small jpc-edit-clarity" data-id="<?php echo esc_attr($clarity->id); ?>">
                                        <?php _e('Edit', 'jewellery-price-calc'); ?>
                                    </button>
                                    <button class="button button-small jpc-delete-clarity" data-id="<?php echo esc_attr($clarity->id); ?>">
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

<!-- Edit Clarity Modal -->
<div id="jpc-edit-clarity-modal" class="jpc-modal">
    <div class="jpc-modal-content">
        <span class="jpc-modal-close">&times;</span>
        <h2><?php _e('Edit Clarity Grade', 'jewellery-price-calc'); ?></h2>
        <form id="jpc-edit-clarity-form" class="jpc-form">
            <input type="hidden" id="edit_clarity_id" name="id">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="edit_clarity_name"><?php _e('Clarity Grade', 'jewellery-price-calc'); ?> *</label>
                    </th>
                    <td>
                        <input type="text" id="edit_clarity_name" name="name" class="regular-text" required>
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
                        <label for="edit_clarity_description"><?php _e('Description', 'jewellery-price-calc'); ?></label>
                    </th>
                    <td>
                        <textarea id="edit_clarity_description" name="description" class="large-text" rows="3"></textarea>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <button type="submit" class="button button-primary">
                    <?php _e('Update Clarity Grade', 'jewellery-price-calc'); ?>
                </button>
            </p>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Add clarity
    $('#jpc-add-clarity-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_add_diamond_clarity',
                nonce: jpcAdmin.nonce,
                name: $('#clarity_name').val(),
                adjustment_type: $('#adjustment_type').val(),
                adjustment_value: $('#adjustment_value').val(),
                description: $('#clarity_description').val()
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'Error adding clarity');
                }
            }
        });
    });
    
    // Edit clarity
    $('.jpc-edit-clarity').on('click', function() {
        var id = $(this).data('id');
        var row = $(this).closest('tr');
        
        $('#edit_clarity_id').val(id);
        $('#edit_clarity_name').val(row.find('td:eq(1)').text().trim());
        $('#edit_clarity_description').val(row.find('td:eq(5)').text().trim());
        
        $('#jpc-edit-clarity-modal').show();
    });
    
    // Update clarity
    $('#jpc-edit-clarity-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_update_diamond_clarity',
                nonce: jpcAdmin.nonce,
                id: $('#edit_clarity_id').val(),
                name: $('#edit_clarity_name').val(),
                adjustment_type: $('#edit_adjustment_type').val(),
                adjustment_value: $('#edit_adjustment_value').val(),
                description: $('#edit_clarity_description').val()
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'Error updating clarity');
                }
            }
        });
    });
    
    // Delete clarity
    $('.jpc-delete-clarity').on('click', function() {
        if (!confirm(jpcAdmin.confirmDelete)) return;
        
        var id = $(this).data('id');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_delete_diamond_clarity',
                nonce: jpcAdmin.nonce,
                id: id
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'Error deleting clarity');
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
