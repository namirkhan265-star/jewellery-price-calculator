<?php
/**
 * Diamonds Management Page Template (Enhanced with 4Cs)
 * v1.9.0 - Now includes Shape, Colour, Clarity, Cut attributes
 */

if (!defined('ABSPATH')) {
    exit;
}

$diamonds = JPC_Diamonds::get_all();
$types = JPC_Diamonds::get_types();
$certifications = JPC_Diamonds::get_certifications();
$carat_sizes = JPC_Diamonds::get_carat_sizes();

// NEW v1.9.0: Get 4Cs attributes
$shapes = JPC_Diamond_Shapes::get_all();
$colours = JPC_Diamond_Colours::get_all();
$clarities = JPC_Diamond_Clarities::get_all();
$cuts = JPC_Diamond_Cuts::get_all();

$diamond_count = count($diamonds);
?>

<div class="wrap jpc-admin-wrap">
    <h1>
        <?php _e('Manage Diamonds', 'jewellery-price-calc'); ?>
        <a href="<?php echo admin_url('admin.php?page=jpc-diamonds'); ?>" class="page-title-action">
            <?php _e('Refresh', 'jewellery-price-calc'); ?>
        </a>
    </h1>
    
    <!-- Info Notice -->
    <div class="notice notice-info" style="margin: 15px 0; padding: 15px; background: #e7f3ff; border-left: 4px solid #2196f3;">
        <h3 style="margin-top: 0;">
            <span class="dashicons dashicons-info" style="color: #2196f3;"></span>
            Diamond Management System
        </h3>
        <p><strong>Complete Diamond 4Cs System:</strong> Create detailed diamond listings with all quality attributes.</p>
        <p><strong>Quick Links:</strong></p>
        <ul style="margin-left: 20px;">
            <li>📊 <a href="<?php echo admin_url('admin.php?page=jpc-diamond-groups'); ?>"><strong>Diamond Groups</strong></a> - Categories (Natural, Lab Grown, etc.)</li>
            <li>🔷 <a href="<?php echo admin_url('admin.php?page=jpc-diamond-shapes'); ?>"><strong>Shapes</strong></a> - Round, Princess, Cushion, etc.</li>
            <li>🎨 <a href="<?php echo admin_url('admin.php?page=jpc-diamond-colours'); ?>"><strong>Colours</strong></a> - D, E, F, G, H, I, J, K-M</li>
            <li>💎 <a href="<?php echo admin_url('admin.php?page=jpc-diamond-clarities'); ?>"><strong>Clarities</strong></a> - FL, IF, VVS, VS, SI, I</li>
            <li>✨ <a href="<?php echo admin_url('admin.php?page=jpc-diamond-cuts'); ?>"><strong>Cuts</strong></a> - Excellent, Very Good, Good, Fair, Poor</li>
            <li>🏆 <a href="<?php echo admin_url('admin.php?page=jpc-diamond-certifications'); ?>"><strong>Certifications</strong></a> - GIA, IGI, HRD, etc.</li>
        </ul>
    </div>
    
    <div class="jpc-admin-content">
        <!-- Add New Diamond Form -->
        <div class="jpc-card">
            <h2><?php _e('Add New Diamond', 'jewellery-price-calc'); ?></h2>
            
            <form id="jpc-add-diamond-form" method="post">
                <table class="form-table">
                    <!-- Basic Information -->
                    <tr>
                        <th colspan="2" style="background: #f0f0f1; padding: 10px;">
                            <h3 style="margin: 0;"><?php _e('Basic Information', 'jewellery-price-calc'); ?></h3>
                        </th>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="display_name"><?php _e('Display Name', 'jewellery-price-calc'); ?> *</label>
                        </th>
                        <td>
                            <input type="text" id="display_name" name="display_name" class="regular-text" required>
                            <p class="description"><?php _e('E.g., "1.00ct Round D VVS1 Excellent (GIA)"', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="diamond_type"><?php _e('Diamond Group', 'jewellery-price-calc'); ?> *</label>
                        </th>
                        <td>
                            <select id="diamond_type" name="type" class="regular-text" required>
                                <option value=""><?php _e('Select Diamond Group', 'jewellery-price-calc'); ?></option>
                                <?php foreach ($types as $key => $label): ?>
                                    <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php _e('Natural, Lab Grown, etc.', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="carat"><?php _e('Carat Weight', 'jewellery-price-calc'); ?> *</label>
                        </th>
                        <td>
                            <input type="number" id="carat" name="carat" class="regular-text" step="0.01" min="0.01" max="10" required>
                            <p class="description"><?php _e('Enter carat weight (e.g., 0.50, 1.00, 2.50)', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    
                    <!-- Diamond 4Cs (NEW v1.9.0) -->
                    <tr>
                        <th colspan="2" style="background: #f0f0f1; padding: 10px;">
                            <h3 style="margin: 0;"><?php _e('Diamond 4Cs Quality Attributes', 'jewellery-price-calc'); ?></h3>
                        </th>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="shape_id"><?php _e('Shape', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <select id="shape_id" name="shape_id" class="regular-text">
                                <option value=""><?php _e('Select Shape (Optional)', 'jewellery-price-calc'); ?></option>
                                <?php foreach ($shapes as $shape): ?>
                                    <option value="<?php echo esc_attr($shape->id); ?>">
                                        <?php echo esc_html($shape->name); ?>
                                        <?php if ($shape->adjustment_type === 'percentage'): ?>
                                            (<?php echo ($shape->adjustment_value >= 0 ? '+' : '') . $shape->adjustment_value; ?>%)
                                        <?php else: ?>
                                            (<?php echo ($shape->adjustment_value >= 0 ? '+' : '') . wc_price($shape->adjustment_value); ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php _e('Round, Princess, Cushion, etc. Affects final price.', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="colour_id"><?php _e('Colour Grade', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <select id="colour_id" name="colour_id" class="regular-text">
                                <option value=""><?php _e('Select Colour (Optional)', 'jewellery-price-calc'); ?></option>
                                <?php foreach ($colours as $colour): ?>
                                    <option value="<?php echo esc_attr($colour->id); ?>">
                                        <?php echo esc_html($colour->name); ?>
                                        <?php if ($colour->adjustment_type === 'percentage'): ?>
                                            (<?php echo ($colour->adjustment_value >= 0 ? '+' : '') . $colour->adjustment_value; ?>%)
                                        <?php else: ?>
                                            (<?php echo ($colour->adjustment_value >= 0 ? '+' : '') . wc_price($colour->adjustment_value); ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php _e('D (Colorless) to K-M (Faint). Higher grades command premium prices.', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="clarity_id"><?php _e('Clarity Grade', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <select id="clarity_id" name="clarity_id" class="regular-text">
                                <option value=""><?php _e('Select Clarity (Optional)', 'jewellery-price-calc'); ?></option>
                                <?php foreach ($clarities as $clarity): ?>
                                    <option value="<?php echo esc_attr($clarity->id); ?>">
                                        <?php echo esc_html($clarity->name); ?>
                                        <?php if ($clarity->adjustment_type === 'percentage'): ?>
                                            (<?php echo ($clarity->adjustment_value >= 0 ? '+' : '') . $clarity->adjustment_value; ?>%)
                                        <?php else: ?>
                                            (<?php echo ($clarity->adjustment_value >= 0 ? '+' : '') . wc_price($clarity->adjustment_value); ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php _e('FL (Flawless) to I1-I3 (Included). Clarity affects brilliance and value.', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="cut_id"><?php _e('Cut Quality', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <select id="cut_id" name="cut_id" class="regular-text">
                                <option value=""><?php _e('Select Cut (Optional)', 'jewellery-price-calc'); ?></option>
                                <?php foreach ($cuts as $cut): ?>
                                    <option value="<?php echo esc_attr($cut->id); ?>">
                                        <?php echo esc_html($cut->name); ?>
                                        <?php if ($cut->adjustment_type === 'percentage'): ?>
                                            (<?php echo ($cut->adjustment_value >= 0 ? '+' : '') . $cut->adjustment_value; ?>%)
                                        <?php else: ?>
                                            (<?php echo ($cut->adjustment_value >= 0 ? '+' : '') . wc_price($cut->adjustment_value); ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php _e('Excellent to Poor. Cut quality determines brilliance and fire.', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    
                    <!-- Certification & Pricing -->
                    <tr>
                        <th colspan="2" style="background: #f0f0f1; padding: 10px;">
                            <h3 style="margin: 0;"><?php _e('Certification & Pricing', 'jewellery-price-calc'); ?></h3>
                        </th>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="certification"><?php _e('Certification', 'jewellery-price-calc'); ?> *</label>
                        </th>
                        <td>
                            <select id="certification" name="certification" class="regular-text" required>
                                <option value=""><?php _e('Select Certification', 'jewellery-price-calc'); ?></option>
                                <?php foreach ($certifications as $key => $label): ?>
                                    <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php _e('GIA, IGI, HRD, etc.', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="price_per_carat"><?php _e('Price per Carat (₹)', 'jewellery-price-calc'); ?> *</label>
                        </th>
                        <td>
                            <input type="number" id="price_per_carat" name="price_per_carat" class="regular-text" step="0.01" min="0" required>
                            <p class="description">
                                <span class="dashicons dashicons-info" style="color: #2196f3;"></span>
                                <?php _e('Base price per carat. 4Cs adjustments will be applied automatically.', 'jewellery-price-calc'); ?>
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
                        <?php _e('Add your first diamond using the form above.', 'jewellery-price-calc'); ?>
                    </p>
                </div>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th style="width: 50px;"><?php _e('ID', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Display Name', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Type', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Carat', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Shape', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Colour', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Clarity', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Cut', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Cert', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Price/Carat', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Actions', 'jewellery-price-calc'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($diamonds as $diamond): 
                            // Get 4Cs attribute names
                            $shape_name = '-';
                            $colour_name = '-';
                            $clarity_name = '-';
                            $cut_name = '-';
                            
                            if (!empty($diamond->shape_id)) {
                                foreach ($shapes as $s) {
                                    if ($s->id == $diamond->shape_id) {
                                        $shape_name = $s->name;
                                        break;
                                    }
                                }
                            }
                            
                            if (!empty($diamond->colour_id)) {
                                foreach ($colours as $c) {
                                    if ($c->id == $diamond->colour_id) {
                                        $colour_name = $c->name;
                                        break;
                                    }
                                }
                            }
                            
                            if (!empty($diamond->clarity_id)) {
                                foreach ($clarities as $cl) {
                                    if ($cl->id == $diamond->clarity_id) {
                                        $clarity_name = $cl->name;
                                        break;
                                    }
                                }
                            }
                            
                            if (!empty($diamond->cut_id)) {
                                foreach ($cuts as $cu) {
                                    if ($cu->id == $diamond->cut_id) {
                                        $cut_name = $cu->name;
                                        break;
                                    }
                                }
                            }
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
                            <td><?php echo esc_html($shape_name); ?></td>
                            <td><?php echo esc_html($colour_name); ?></td>
                            <td><?php echo esc_html($clarity_name); ?></td>
                            <td><?php echo esc_html($cut_name); ?></td>
                            <td>
                                <?php 
                                $cert_label = isset($certifications[$diamond->certification]) ? $certifications[$diamond->certification] : $diamond->certification;
                                echo esc_html($cert_label); 
                                ?>
                            </td>
                            <td><strong>₹<?php echo number_format($diamond->price_per_carat, 2); ?></strong></td>
                            <td>
                                <button type="button" class="button button-small jpc-edit-diamond" 
                                        data-id="<?php echo $diamond->id; ?>"
                                        data-type="<?php echo esc_attr($diamond->type); ?>"
                                        data-carat="<?php echo esc_attr($diamond->carat); ?>"
                                        data-shape-id="<?php echo esc_attr($diamond->shape_id ?? ''); ?>"
                                        data-colour-id="<?php echo esc_attr($diamond->colour_id ?? ''); ?>"
                                        data-clarity-id="<?php echo esc_attr($diamond->clarity_id ?? ''); ?>"
                                        data-cut-id="<?php echo esc_attr($diamond->cut_id ?? ''); ?>"
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
    <div class="jpc-modal-content" style="max-width: 800px;">
        <span class="jpc-modal-close">&times;</span>
        <h2><?php _e('Edit Diamond', 'jewellery-price-calc'); ?></h2>
        
        <form id="jpc-edit-diamond-form">
            <input type="hidden" id="edit_diamond_id" name="id">
            
            <table class="form-table">
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
                        <input type="number" id="edit_carat" name="carat" class="regular-text" step="0.01" min="0.01" required>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="edit_shape_id"><?php _e('Shape', 'jewellery-price-calc'); ?></label>
                    </th>
                    <td>
                        <select id="edit_shape_id" name="shape_id" class="regular-text">
                            <option value=""><?php _e('None', 'jewellery-price-calc'); ?></option>
                            <?php foreach ($shapes as $shape): ?>
                                <option value="<?php echo esc_attr($shape->id); ?>"><?php echo esc_html($shape->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="edit_colour_id"><?php _e('Colour', 'jewellery-price-calc'); ?></label>
                    </th>
                    <td>
                        <select id="edit_colour_id" name="colour_id" class="regular-text">
                            <option value=""><?php _e('None', 'jewellery-price-calc'); ?></option>
                            <?php foreach ($colours as $colour): ?>
                                <option value="<?php echo esc_attr($colour->id); ?>"><?php echo esc_html($colour->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="edit_clarity_id"><?php _e('Clarity', 'jewellery-price-calc'); ?></label>
                    </th>
                    <td>
                        <select id="edit_clarity_id" name="clarity_id" class="regular-text">
                            <option value=""><?php _e('None', 'jewellery-price-calc'); ?></option>
                            <?php foreach ($clarities as $clarity): ?>
                                <option value="<?php echo esc_attr($clarity->id); ?>"><?php echo esc_html($clarity->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="edit_cut_id"><?php _e('Cut', 'jewellery-price-calc'); ?></label>
                    </th>
                    <td>
                        <select id="edit_cut_id" name="cut_id" class="regular-text">
                            <option value=""><?php _e('None', 'jewellery-price-calc'); ?></option>
                            <?php foreach ($cuts as $cut): ?>
                                <option value="<?php echo esc_attr($cut->id); ?>"><?php echo esc_html($cut->name); ?></option>
                            <?php endforeach; ?>
                        </select>
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
                        <label for="edit_price_per_carat"><?php _e('Price per Carat (₹)', 'jewellery-price-calc'); ?></label>
                    </th>
                    <td>
                        <input type="number" id="edit_price_per_carat" name="price_per_carat" class="regular-text" step="0.01" min="0" required>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="submit" class="button button-primary">
                    <?php _e('Update Diamond', 'jewellery-price-calc'); ?>
                </button>
            </p>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Add diamond
    $('#jpc-add-diamond-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_add_diamond',
                nonce: jpcAdmin.nonce,
                display_name: $('#display_name').val(),
                type: $('#diamond_type').val(),
                carat: $('#carat').val(),
                shape_id: $('#shape_id').val() || null,
                colour_id: $('#colour_id').val() || null,
                clarity_id: $('#clarity_id').val() || null,
                cut_id: $('#cut_id').val() || null,
                certification: $('#certification').val(),
                price_per_carat: $('#price_per_carat').val()
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'Error adding diamond');
                }
            }
        });
    });
    
    // Edit diamond
    $('.jpc-edit-diamond').on('click', function() {
        var $btn = $(this);
        
        $('#edit_diamond_id').val($btn.data('id'));
        $('#edit_display_name').val($btn.data('display-name'));
        $('#edit_diamond_type').val($btn.data('type'));
        $('#edit_carat').val($btn.data('carat'));
        $('#edit_shape_id').val($btn.data('shape-id') || '');
        $('#edit_colour_id').val($btn.data('colour-id') || '');
        $('#edit_clarity_id').val($btn.data('clarity-id') || '');
        $('#edit_cut_id').val($btn.data('cut-id') || '');
        $('#edit_certification').val($btn.data('certification'));
        $('#edit_price_per_carat').val($btn.data('price'));
        
        $('#jpc-edit-diamond-modal').show();
    });
    
    // Update diamond
    $('#jpc-edit-diamond-form').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_update_diamond',
                nonce: jpcAdmin.nonce,
                id: $('#edit_diamond_id').val(),
                display_name: $('#edit_display_name').val(),
                type: $('#edit_diamond_type').val(),
                carat: $('#edit_carat').val(),
                shape_id: $('#edit_shape_id').val() || null,
                colour_id: $('#edit_colour_id').val() || null,
                clarity_id: $('#edit_clarity_id').val() || null,
                cut_id: $('#edit_cut_id').val() || null,
                certification: $('#edit_certification').val(),
                price_per_carat: $('#edit_price_per_carat').val()
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'Error updating diamond');
                }
            }
        });
    });
    
    // Delete diamond
    $('.jpc-delete-diamond').on('click', function() {
        if (!confirm(jpcAdmin.confirmDelete)) return;
        
        var id = $(this).data('id');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_delete_diamond',
                nonce: jpcAdmin.nonce,
                id: id
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || 'Error deleting diamond');
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
