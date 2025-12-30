<?php
/**
 * Diamond Certifications Admin Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$certifications = JPC_Diamond_Certifications::get_all();
?>

<div class="wrap">
    <h1><?php _e('Diamond Certifications', 'jewellery-price-calc'); ?></h1>
    
    <p class="description">
        <?php _e('Manage certification types and their price adjustments. Certifications can add a percentage or fixed amount to the base diamond price.', 'jewellery-price-calc'); ?>
    </p>
    
    <div class="jpc-admin-container">
        <!-- Add New Certification Form -->
        <div class="jpc-card">
            <h2><?php _e('Add New Certification', 'jewellery-price-calc'); ?></h2>
            <form id="jpc-add-certification-form" class="jpc-form">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="cert_name"><?php _e('Certification Name', 'jewellery-price-calc'); ?> *</label>
                        </th>
                        <td>
                            <input type="text" id="cert_name" name="name" class="regular-text" required>
                            <p class="description"><?php _e('e.g., GIA, IGI, HRD, AGS, None', 'jewellery-price-calc'); ?></p>
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
                                <?php _e('For Percentage: Enter 20 for +20%, -10 for -10%. For Fixed: Enter amount to add/subtract.', 'jewellery-price-calc'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="cert_description"><?php _e('Description', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <textarea id="cert_description" name="description" class="large-text" rows="3"></textarea>
                            <p class="description"><?php _e('Brief description of this certification', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary">
                        <?php _e('Add Certification', 'jewellery-price-calc'); ?>
                    </button>
                </p>
            </form>
        </div>
        
        <!-- Existing Certifications -->
        <div class="jpc-card">
            <h2><?php _e('Existing Certifications', 'jewellery-price-calc'); ?></h2>
            
            <?php if (empty($certifications)): ?>
                <p><?php _e('No certifications found. Add your first certification above.', 'jewellery-price-calc'); ?></p>
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
                        <?php foreach ($certifications as $cert): ?>
                            <?php
                            // Calculate example impact
                            $base_price = 25000;
                            if ($cert->adjustment_type === 'percentage') {
                                $adjusted = $base_price * (1 + ($cert->adjustment_value / 100));
                                $impact = ($cert->adjustment_value >= 0 ? '+' : '') . $cert->adjustment_value . '%';
                            } else {
                                $adjusted = $base_price + $cert->adjustment_value;
                                $impact = ($cert->adjustment_value >= 0 ? '+' : '') . wc_price($cert->adjustment_value);
                            }
                            $example = wc_price($base_price) . ' → ' . wc_price($adjusted);
                            ?>
                            <tr data-id="<?php echo esc_attr($cert->id); ?>">
                                <td><?php echo esc_html($cert->id); ?></td>
                                <td>
                                    <strong><?php echo esc_html($cert->name); ?></strong><br>
                                    <code><?php echo esc_html($cert->slug); ?></code>
                                </td>
                                <td>
                                    <span class="jpc-badge jpc-badge-<?php echo esc_attr($cert->adjustment_type); ?>">
                                        <?php echo esc_html(ucfirst($cert->adjustment_type)); ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?php echo $impact; ?></strong>
                                </td>
                                <td>
                                    <small><?php echo $example; ?></small>
                                </td>
                                <td><?php echo esc_html($cert->description); ?></td>
                                <td>
                                    <button class="button button-small jpc-edit-certification" 
                                            data-id="<?php echo esc_attr($cert->id); ?>"
                                            data-name="<?php echo esc_attr($cert->name); ?>"
                                            data-type="<?php echo esc_attr($cert->adjustment_type); ?>"
                                            data-value="<?php echo esc_attr($cert->adjustment_value); ?>"
                                            data-description="<?php echo esc_attr($cert->description); ?>">
                                        <?php _e('Edit', 'jewellery-price-calc'); ?>
                                    </button>
                                    <button class="button button-small jpc-delete-certification" data-id="<?php echo esc_attr($cert->id); ?>">
                                        <?php _e('Delete', 'jewellery-price-calc'); ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <!-- Pricing Examples -->
        <div class="jpc-card">
            <h2><?php _e('Pricing Examples', 'jewellery-price-calc'); ?></h2>
            <p><?php _e('How certifications affect diamond pricing:', 'jewellery-price-calc'); ?></p>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Scenario', 'jewellery-price-calc'); ?></th>
                        <th><?php _e('Base Price', 'jewellery-price-calc'); ?></th>
                        <th><?php _e('Certification', 'jewellery-price-calc'); ?></th>
                        <th><?php _e('Final Price', 'jewellery-price-calc'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $examples = array(
                        array('scenario' => '0.50ct Natural Diamond', 'base' => 25000),
                        array('scenario' => '1.00ct Lab Grown Diamond', 'base' => 15000),
                        array('scenario' => '0.75ct Moissanite', 'base' => 5000),
                    );
                    
                    foreach ($examples as $example):
                        foreach ($certifications as $cert):
                            if ($cert->adjustment_type === 'percentage') {
                                $final = $example['base'] * (1 + ($cert->adjustment_value / 100));
                            } else {
                                $final = $example['base'] + $cert->adjustment_value;
                            }
                            ?>
                            <tr>
                                <td><?php echo esc_html($example['scenario']); ?></td>
                                <td><?php echo wc_price($example['base']); ?></td>
                                <td><?php echo esc_html($cert->name); ?></td>
                                <td><strong><?php echo wc_price($final); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Certification Modal -->
<div id="jpc-edit-certification-modal" class="jpc-modal" style="display: none;">
    <div class="jpc-modal-content">
        <span class="jpc-modal-close">&times;</span>
        <h2><?php _e('Edit Certification', 'jewellery-price-calc'); ?></h2>
        <form id="jpc-edit-certification-form" class="jpc-form">
            <input type="hidden" id="edit_cert_id" name="id">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="edit_cert_name"><?php _e('Certification Name', 'jewellery-price-calc'); ?> *</label>
                    </th>
                    <td>
                        <input type="text" id="edit_cert_name" name="name" class="regular-text" required>
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
                        <label for="edit_cert_description"><?php _e('Description', 'jewellery-price-calc'); ?></label>
                    </th>
                    <td>
                        <textarea id="edit_cert_description" name="description" class="large-text" rows="3"></textarea>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <button type="submit" class="button button-primary">
                    <?php _e('Update Certification', 'jewellery-price-calc'); ?>
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
    // Add certification
    $('#jpc-add-certification-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: jpcAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_add_diamond_certification',
                nonce: jpcAdmin.nonce,
                name: $('#cert_name').val(),
                adjustment_type: $('#adjustment_type').val(),
                adjustment_value: $('#adjustment_value').val(),
                description: $('#cert_description').val()
            },
            success: function(response) {
                if (response.success) {
                    alert('Certification added successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });
    
    // Edit certification
    $('.jpc-edit-certification').on('click', function() {
        var btn = $(this);
        
        $('#edit_cert_id').val(btn.data('id'));
        $('#edit_cert_name').val(btn.data('name'));
        $('#edit_adjustment_type').val(btn.data('type'));
        $('#edit_adjustment_value').val(btn.data('value'));
        $('#edit_cert_description').val(btn.data('description'));
        
        $('#jpc-edit-certification-modal').show();
    });
    
    // Update certification
    $('#jpc-edit-certification-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: jpcAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_update_diamond_certification',
                nonce: jpcAdmin.nonce,
                id: $('#edit_cert_id').val(),
                name: $('#edit_cert_name').val(),
                adjustment_type: $('#edit_adjustment_type').val(),
                adjustment_value: $('#edit_adjustment_value').val(),
                description: $('#edit_cert_description').val()
            },
            success: function(response) {
                if (response.success) {
                    alert('Certification updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.data);
                }
            }
        });
    });
    
    // Delete certification
    $('.jpc-delete-certification').on('click', function() {
        if (!confirm(jpcAdmin.confirmDelete)) {
            return;
        }
        
        var id = $(this).data('id');
        
        $.ajax({
            url: jpcAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_delete_diamond_certification',
                nonce: jpcAdmin.nonce,
                id: id
            },
            success: function(response) {
                if (response.success) {
                    alert('Certification deleted successfully!');
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

.jpc-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.jpc-badge-percentage {
    background: #e3f2fd;
    color: #1976d2;
}

.jpc-badge-fixed {
    background: #f3e5f5;
    color: #7b1fa2;
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
