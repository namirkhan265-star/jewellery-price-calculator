<?php
/**
 * Diamonds Management Page Template (Legacy)
 * Integrates with new 3-tab diamond system
 */

if (!defined('ABSPATH')) {
    exit;
}

$diamonds = JPC_Diamonds::get_all();
$types = JPC_Diamonds::get_types();
$certifications = JPC_Diamonds::get_certifications();
$carat_sizes = JPC_Diamonds::get_carat_sizes();

// Debug info
$diamond_count = count($diamonds);
error_log('JPC Diamonds Page: Found ' . $diamond_count . ' diamonds');
?>

<div class="wrap jpc-admin-wrap">
    <h1>
        <?php _e('Manage Diamonds (Legacy)', 'jewellery-price-calc'); ?>
        <a href="<?php echo admin_url('admin.php?page=jpc-diamonds'); ?>" class="page-title-action">
            <?php _e('Refresh', 'jewellery-price-calc'); ?>
        </a>
        <button type="button" id="jpc-sync-diamonds" class="page-title-action" style="background: #2196f3; color: white; border-color: #2196f3;">
            <span class="dashicons dashicons-update" style="margin-top: 3px;"></span>
            <?php _e('Sync from New System', 'jewellery-price-calc'); ?>
        </button>
    </h1>
    
    <!-- Info Notice -->
    <div class="notice notice-info" style="margin: 15px 0; padding: 15px; background: #e7f3ff; border-left: 4px solid #2196f3;">
        <h3 style="margin-top: 0;">
            <span class="dashicons dashicons-info" style="color: #2196f3;"></span>
            About This Page
        </h3>
        <p><strong>This is the Legacy Diamonds page.</strong> It shows individual diamond entries for backward compatibility.</p>
        <p><strong>New System:</strong> Use the 3-tab system for better management:</p>
        <ul style="margin-left: 20px;">
            <li>📊 <a href="<?php echo admin_url('admin.php?page=jpc-diamond-groups'); ?>"><strong>Diamond Groups</strong></a> - Manage diamond categories (Natural, Lab Grown, etc.)</li>
            <li>💎 <a href="<?php echo admin_url('admin.php?page=jpc-diamond-types'); ?>"><strong>Diamond Types</strong></a> - Set carat-based pricing ranges</li>
            <li>🏆 <a href="<?php echo admin_url('admin.php?page=jpc-diamond-certifications'); ?>"><strong>Certifications</strong></a> - Manage certification premiums</li>
        </ul>
        <p><strong>Auto-Pricing:</strong> When you add a diamond below, the price is automatically calculated from the 3-tab system. You can override it manually if needed.</p>
    </div>
    
    <div class="jpc-admin-content">
        <!-- Add New Diamond Form -->
        <div class="jpc-card">
            <h2><?php _e('Add New Diamond', 'jewellery-price-calc'); ?></h2>
            
            <form id="jpc-add-diamond-form" method="post">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="diamond_type"><?php _e('Diamond Group', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <select id="diamond_type" name="type" class="regular-text" required>
                                <option value=""><?php _e('Select Diamond Group', 'jewellery-price-calc'); ?></option>
                                <?php foreach ($types as $key => $label): ?>
                                    <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php _e('From Diamond Groups tab', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="carat"><?php _e('Carat Weight', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <div style="margin-bottom: 10px;">
                                <label style="display: inline-flex; align-items: center; margin-right: 20px;">
                                    <input type="radio" name="carat_input_type" value="dropdown" checked style="margin-right: 5px;">
                                    <?php _e('Select from list', 'jewellery-price-calc'); ?>
                                </label>
                                <label style="display: inline-flex; align-items: center;">
                                    <input type="radio" name="carat_input_type" value="manual" style="margin-right: 5px;">
                                    <?php _e('Enter manually', 'jewellery-price-calc'); ?>
                                </label>
                            </div>
                            
                            <div id="carat-dropdown-container">
                                <select id="carat" name="carat" class="regular-text">
                                    <option value=""><?php _e('Select Carat', 'jewellery-price-calc'); ?></option>
                                    <?php foreach ($carat_sizes as $size): ?>
                                        <option value="<?php echo esc_attr($size); ?>"><?php echo esc_html($size); ?> ct</option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="description"><?php _e('Select from predefined carat weights', 'jewellery-price-calc'); ?></p>
                            </div>
                            
                            <div id="carat-manual-container" style="display: none;">
                                <input type="number" id="carat_manual" name="carat_manual" class="regular-text" step="0.01" min="0.01" max="10" placeholder="e.g., 0.85">
                                <p class="description"><?php _e('Enter custom carat weight (e.g., 0.85, 1.23, 2.47)', 'jewellery-price-calc'); ?></p>
                            </div>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="certification"><?php _e('Certification', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <select id="certification" name="certification" class="regular-text" required>
                                <option value=""><?php _e('Select Certification', 'jewellery-price-calc'); ?></option>
                                <?php foreach ($certifications as $key => $label): ?>
                                    <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php _e('From Certifications tab', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    
                    <tr id="price-calculation-row" style="display: none;">
                        <th scope="row">
                            <label><?php _e('Calculated Price', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <div id="price-calculation-result" style="padding: 15px; background: #f0f9ff; border-left: 4px solid #2196f3; margin-bottom: 10px;">
                                <p style="margin: 0;"><strong>Calculating...</strong></p>
                            </div>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="display_name"><?php _e('Display Name', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="display_name" name="display_name" class="regular-text" required>
                            <p class="description"><?php _e('E.g., "1.00ct Natural Diamond (GIA)"', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="price_per_carat"><?php _e('Price per Carat (₹)', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="price_per_carat" name="price_per_carat" class="regular-text" step="0.01" min="0" required>
                            <p class="description">
                                <span class="dashicons dashicons-info" style="color: #2196f3;"></span>
                                <?php _e('Auto-filled from calculation. You can override this value if needed.', 'jewellery-price-calc'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <button type="submit" class="button button-primary">
                        <span class="dashicons dashicons-plus-alt" style="margin-top: 3px;"></span>
                        <?php _e('Add Diamond', 'jewellery-price-calc'); ?>
                    </button>
                </p>
            </form>
        </div>
        
        <!-- Existing Diamonds List -->
        <div class="jpc-card">
            <h2><?php _e('Existing Diamonds', 'jewellery-price-calc'); ?> (<?php echo $diamond_count; ?>)</h2>
            
            <?php if (empty($diamonds)): ?>
                <div class="notice notice-warning inline">
                    <p>
                        <strong><?php _e('No diamonds found!', 'jewellery-price-calc'); ?></strong><br>
                        <?php _e('Add diamonds using the form above, or click "Sync from New System" to auto-generate from your Diamond Groups, Types, and Certifications.', 'jewellery-price-calc'); ?>
                    </p>
                </div>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('ID', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Display Name', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Type', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Carat', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Certification', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Price/Carat', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Total Price', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Actions', 'jewellery-price-calc'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($diamonds as $diamond): 
                            $total_price = floatval($diamond->price_per_carat) * floatval($diamond->carat);
                        ?>
                        <tr>
                            <td><?php echo $diamond->id; ?></td>
                            <td><strong><?php echo esc_html($diamond->display_name); ?></strong></td>
                            <td>
                                <?php 
                                $type_label = isset($types[$diamond->type]) ? $types[$diamond->type] : $diamond->type;
                                echo esc_html($type_label); 
                                ?>
                            </td>
                            <td><?php echo number_format($diamond->carat, 2); ?> ct</td>
                            <td>
                                <?php 
                                $cert_label = isset($certifications[$diamond->certification]) ? $certifications[$diamond->certification] : $diamond->certification;
                                echo esc_html($cert_label); 
                                ?>
                            </td>
                            <td><strong>₹<?php echo number_format($diamond->price_per_carat, 2); ?></strong></td>
                            <td><strong>₹<?php echo number_format($total_price, 2); ?></strong></td>
                            <td>
                                <button type="button" class="button button-small jpc-edit-diamond" 
                                        data-id="<?php echo $diamond->id; ?>"
                                        data-type="<?php echo esc_attr($diamond->type); ?>"
                                        data-carat="<?php echo esc_attr($diamond->carat); ?>"
                                        data-certification="<?php echo esc_attr($diamond->certification); ?>"
                                        data-price="<?php echo esc_attr($diamond->price_per_carat); ?>"
                                        data-display-name="<?php echo esc_attr($diamond->display_name); ?>">
                                    <span class="dashicons dashicons-edit"></span>
                                    <?php _e('Edit', 'jewellery-price-calc'); ?>
                                </button>
                                <button type="button" class="button button-small button-link-delete jpc-delete-diamond" 
                                        data-id="<?php echo $diamond->id; ?>">
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

<!-- Edit Diamond Modal -->
<div id="jpc-edit-diamond-modal" class="jpc-modal" style="display: none;">
    <div class="jpc-modal-content">
        <span class="jpc-modal-close">&times;</span>
        <h2><?php _e('Edit Diamond', 'jewellery-price-calc'); ?></h2>
        
        <form id="jpc-edit-diamond-form">
            <input type="hidden" id="edit_diamond_id" name="id">
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="edit_diamond_type"><?php _e('Diamond Group', 'jewellery-price-calc'); ?></label>
                    </th>
                    <td>
                        <select id="edit_diamond_type" name="type" class="regular-text" required>
                            <?php foreach ($types as $key => $label): ?>
                                <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="edit_carat"><?php _e('Carat Weight', 'jewellery-price-calc'); ?></label>
                    </th>
                    <td>
                        <div style="margin-bottom: 10px;">
                            <label style="display: inline-flex; align-items: center; margin-right: 20px;">
                                <input type="radio" name="edit_carat_input_type" value="dropdown" checked style="margin-right: 5px;">
                                <?php _e('Select from list', 'jewellery-price-calc'); ?>
                            </label>
                            <label style="display: inline-flex; align-items: center;">
                                <input type="radio" name="edit_carat_input_type" value="manual" style="margin-right: 5px;">
                                <?php _e('Enter manually', 'jewellery-price-calc'); ?>
                            </label>
                        </div>
                        
                        <div id="edit-carat-dropdown-container">
                            <select id="edit_carat" name="carat" class="regular-text">
                                <?php foreach ($carat_sizes as $size): ?>
                                    <option value="<?php echo esc_attr($size); ?>"><?php echo esc_html($size); ?> ct</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div id="edit-carat-manual-container" style="display: none;">
                            <input type="number" id="edit_carat_manual" name="carat_manual" class="regular-text" step="0.01" min="0.01" max="10" placeholder="e.g., 0.85">
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="edit_certification"><?php _e('Certification', 'jewellery-price-calc'); ?></label>
                    </th>
                    <td>
                        <select id="edit_certification" name="certification" class="regular-text" required>
                            <?php foreach ($certifications as $key => $label): ?>
                                <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="edit_display_name"><?php _e('Display Name', 'jewellery-price-calc'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="edit_display_name" name="display_name" class="regular-text" required>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="edit_price_per_carat"><?php _e('Price per Carat (₹)', 'jewellery-price-calc'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="edit_price_per_carat" name="price_per_carat" class="regular-text" step="0.01" min="0" required>
                        <p class="description"><?php _e('You can manually override the calculated price', 'jewellery-price-calc'); ?></p>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" class="button button-primary">
                    <?php _e('Update Diamond', 'jewellery-price-calc'); ?>
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
    
    // Toggle between dropdown and manual carat input (Add form)
    $('input[name="carat_input_type"]').on('change', function() {
        if ($(this).val() === 'manual') {
            $('#carat-dropdown-container').hide();
            $('#carat-manual-container').show();
            $('#carat').prop('required', false);
            $('#carat_manual').prop('required', true);
        } else {
            $('#carat-dropdown-container').show();
            $('#carat-manual-container').hide();
            $('#carat').prop('required', true);
            $('#carat_manual').prop('required', false);
        }
        // Trigger price calculation
        calculatePrice();
    });
    
    // Toggle between dropdown and manual carat input (Edit form)
    $('input[name="edit_carat_input_type"]').on('change', function() {
        if ($(this).val() === 'manual') {
            $('#edit-carat-dropdown-container').hide();
            $('#edit-carat-manual-container').show();
            $('#edit_carat').prop('required', false);
            $('#edit_carat_manual').prop('required', true);
        } else {
            $('#edit-carat-dropdown-container').show();
            $('#edit-carat-manual-container').hide();
            $('#edit_carat').prop('required', true);
            $('#edit_carat_manual').prop('required', false);
        }
    });
    
    // Get current carat value (from dropdown or manual input)
    function getCurrentCarat() {
        if ($('input[name="carat_input_type"]:checked').val() === 'manual') {
            return $('#carat_manual').val();
        } else {
            return $('#carat').val();
        }
    }
    
    // Auto-calculate price when group, carat, or certification changes
    function calculatePrice() {
        var group = $('#diamond_type').val();
        var carat = getCurrentCarat();
        var cert = $('#certification').val();
        
        if (!group || !carat || !cert) {
            $('#price-calculation-row').hide();
            return;
        }
        
        $('#price-calculation-row').show();
        $('#price-calculation-result').html('<p style="margin: 0;"><strong>Calculating...</strong></p>');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_calculate_diamond_price',
                nonce: '<?php echo wp_create_nonce('jpc_admin_nonce'); ?>',
                group_slug: group,
                carat: carat,
                cert_slug: cert
            },
            success: function(response) {
                if (response.success) {
                    var data = response.data;
                    var html = '<div style="margin-bottom: 10px;">';
                    html += '<p style="margin: 5px 0;"><strong>Base Price:</strong> ₹' + parseFloat(data.base_price).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '/carat</p>';
                    html += '<p style="margin: 5px 0;"><strong>Certification Adjustment:</strong> ';
                    if (data.adjustment_type === 'percentage') {
                        html += (data.certification_adjustment >= 0 ? '+' : '') + data.certification_adjustment + '%';
                    } else {
                        html += (data.certification_adjustment >= 0 ? '+' : '') + '₹' + parseFloat(data.certification_adjustment).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    }
                    html += '</p>';
                    html += '<p style="margin: 5px 0;"><strong>Final Price/Carat:</strong> <span style="color: #2196f3; font-size: 16px;">₹' + parseFloat(data.final_price_per_carat).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span></p>';
                    html += '<p style="margin: 5px 0;"><strong>Total Price:</strong> <span style="color: #4caf50; font-size: 16px;">₹' + parseFloat(data.total_price).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span></p>';
                    html += '<p style="margin: 5px 0; font-size: 12px; color: #666;"><em>Carat Range: ' + data.carat_range + 'ct</em></p>';
                    html += '</div>';
                    
                    $('#price-calculation-result').html(html);
                    $('#price_per_carat').val(data.final_price_per_carat.toFixed(2));
                    
                    // Auto-generate display name if empty
                    if (!$('#display_name').val()) {
                        var groupName = $('#diamond_type option:selected').text();
                        var certName = $('#certification option:selected').text();
                        $('#display_name').val(carat + 'ct ' + groupName + ' (' + certName + ')');
                    }
                } else {
                    $('#price-calculation-result').html('<p style="margin: 0; color: #d32f2f;"><strong>Error:</strong> ' + response.data.error + '</p>');
                }
            },
            error: function() {
                $('#price-calculation-result').html('<p style="margin: 0; color: #d32f2f;"><strong>Error:</strong> Failed to calculate price</p>');
            }
        });
    }
    
    $('#diamond_type, #carat, #certification, #carat_manual').on('change keyup', calculatePrice);
    
    // Sync diamonds from new system
    $('#jpc-sync-diamonds').on('click', function() {
        if (!confirm('This will create diamond entries from your Diamond Groups, Types, and Certifications. Continue?')) {
            return;
        }
        
        var $btn = $(this);
        $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Syncing...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_sync_legacy_diamonds',
                nonce: '<?php echo wp_create_nonce('jpc_admin_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert('Error: ' + response.data.message);
                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-update"></span> Sync from New System');
                }
            },
            error: function() {
                alert('Failed to sync diamonds');
                $btn.prop('disabled', false).html('<span class="dashicons dashicons-update"></span> Sync from New System');
            }
        });
    });
    
    // Add diamond
    $('#jpc-add-diamond-form').on('submit', function(e) {
        e.preventDefault();
        
        var carat = getCurrentCarat();
        
        var formData = {
            action: 'jpc_add_diamond',
            nonce: '<?php echo wp_create_nonce('jpc_admin_nonce'); ?>',
            type: $('#diamond_type').val(),
            carat: carat,
            certification: $('#certification').val(),
            price_per_carat: $('#price_per_carat').val(),
            display_name: $('#display_name').val()
        };
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Diamond added successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('Failed to add diamond');
            }
        });
    });
    
    // Edit diamond
    $('.jpc-edit-diamond').on('click', function() {
        var $btn = $(this);
        var caratValue = parseFloat($btn.data('carat'));
        
        $('#edit_diamond_id').val($btn.data('id'));
        $('#edit_diamond_type').val($btn.data('type'));
        $('#edit_certification').val($btn.data('certification'));
        $('#edit_price_per_carat').val($btn.data('price'));
        $('#edit_display_name').val($btn.data('display-name'));
        
        // Check if carat exists in dropdown
        var caratExists = $('#edit_carat option[value="' + caratValue + '"]').length > 0;
        
        if (caratExists) {
            // Use dropdown
            $('input[name="edit_carat_input_type"][value="dropdown"]').prop('checked', true).trigger('change');
            $('#edit_carat').val(caratValue);
        } else {
            // Use manual input
            $('input[name="edit_carat_input_type"][value="manual"]').prop('checked', true).trigger('change');
            $('#edit_carat_manual').val(caratValue);
        }
        
        $('#jpc-edit-diamond-modal').fadeIn();
    });
    
    // Update diamond
    $('#jpc-edit-diamond-form').on('submit', function(e) {
        e.preventDefault();
        
        var carat;
        if ($('input[name="edit_carat_input_type"]:checked').val() === 'manual') {
            carat = $('#edit_carat_manual').val();
        } else {
            carat = $('#edit_carat').val();
        }
        
        var formData = {
            action: 'jpc_update_diamond',
            nonce: '<?php echo wp_create_nonce('jpc_admin_nonce'); ?>',
            id: $('#edit_diamond_id').val(),
            type: $('#edit_diamond_type').val(),
            carat: carat,
            certification: $('#edit_certification').val(),
            price_per_carat: $('#edit_price_per_carat').val(),
            display_name: $('#edit_display_name').val()
        };
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Diamond updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('Failed to update diamond');
            }
        });
    });
    
    // Delete diamond
    $('.jpc-delete-diamond').on('click', function() {
        if (!confirm('Are you sure you want to delete this diamond?')) {
            return;
        }
        
        var id = $(this).data('id');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_delete_diamond',
                nonce: '<?php echo wp_create_nonce('jpc_admin_nonce'); ?>',
                id: id
            },
            success: function(response) {
                if (response.success) {
                    alert('Diamond deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('Failed to delete diamond');
            }
        });
    });
    
    // Close modal
    $('.jpc-modal-close').on('click', function() {
        $('.jpc-modal').fadeOut();
    });
    
    // Close modal on outside click
    $(window).on('click', function(e) {
        if ($(e.target).hasClass('jpc-modal')) {
            $('.jpc-modal').fadeOut();
        }
    });
});
</script>

<style>
.jpc-modal {
    position: fixed;
    z-index: 100000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
}

.jpc-modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    border-radius: 4px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.jpc-modal-close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.jpc-modal-close:hover,
.jpc-modal-close:focus {
    color: #000;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.spin {
    animation: spin 1s linear infinite;
}
</style>
