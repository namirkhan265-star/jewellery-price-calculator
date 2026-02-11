<?php
/**
 * SNIPPET FOR class-jpc-product-meta-box-v2.php
 * v2.5.29: Add conditional display for making charges and wastage based on metal group settings
 * 
 * REPLACE LINES 70-250 in class-jpc-product-meta-box-v2.php with this code
 */

    /**
     * Render meta box
     */
    public function render_meta_box($post) {
        wp_nonce_field('jpc_product_meta_box', 'jpc_product_meta_box_nonce');
        
        // Get all data
        $metals = JPC_Metals::get_all();
        $diamonds = JPC_Diamonds::get_all();
        $diamond_groups = JPC_Diamond_Groups::get_all();
        $diamond_certifications = JPC_Diamond_Certifications::get_all();
        $diamond_shapes = JPC_Diamond_Shapes::get_all();
        $diamond_colours = JPC_Diamond_Colours::get_all();
        $diamond_clarities = JPC_Diamond_Clarities::get_all();
        $diamond_cuts = JPC_Diamond_Cuts::get_all();
        
        // Get saved values
        $metal_id = get_post_meta($post->ID, '_jpc_metal_id', true);
        $metal_weight = get_post_meta($post->ID, '_jpc_metal_weight', true);
        $wastage = get_post_meta($post->ID, '_jpc_wastage', true);
        
        // Making charges v2.0.0
        $making_charges_mode = get_post_meta($post->ID, '_jpc_making_charges_mode', true) ?: 'auto';
        $making_charges_value = get_post_meta($post->ID, '_jpc_making_charges_value', true);
        $making_charges_type = get_post_meta($post->ID, '_jpc_making_charges_type', true) ?: 'percentage';
        
        // Diamond entry v2.0.0
        $diamond_entry_mode = get_post_meta($post->ID, '_jpc_diamond_entry_mode', true) ?: 'dropdown';
        $diamond_id = get_post_meta($post->ID, '_jpc_diamond_id', true);
        $diamond_quantity = get_post_meta($post->ID, '_jpc_diamond_quantity', true);
        
        // Manual diamond fields
        $manual_diamond_group_id = get_post_meta($post->ID, '_jpc_manual_diamond_group_id', true);
        $manual_diamond_carat = get_post_meta($post->ID, '_jpc_manual_diamond_carat', true);
        $manual_diamond_certification_id = get_post_meta($post->ID, '_jpc_manual_diamond_certification_id', true);
        $manual_diamond_shape_id = get_post_meta($post->ID, '_jpc_manual_diamond_shape_id', true);
        $manual_diamond_colour_id = get_post_meta($post->ID, '_jpc_manual_diamond_colour_id', true);
        $manual_diamond_clarity_id = get_post_meta($post->ID, '_jpc_manual_diamond_clarity_id', true);
        $manual_diamond_cut_id = get_post_meta($post->ID, '_jpc_manual_diamond_cut_id', true);
        $manual_diamond_quantity = get_post_meta($post->ID, '_jpc_manual_diamond_quantity', true);
        $manual_diamond_price_per_carat = get_post_meta($post->ID, '_jpc_manual_diamond_price_per_carat', true);
        
        // Other fields - v2.5.26: These are now loaded in the template file
        $discount_percentage = get_post_meta($post->ID, '_jpc_discount_percentage', true);
        
        // Extra fields
        $extra_fields = array();
        for ($i = 1; $i <= 5; $i++) {
            $extra_fields[$i] = get_post_meta($post->ID, '_jpc_extra_field_' . $i, true);
        }
        
        // v2.5.29: Get metal group settings for conditional display
        $selected_metal_group = null;
        if ($metal_id) {
            $selected_metal = JPC_Metals::get_by_id($metal_id);
            if ($selected_metal && $selected_metal->metal_group_id) {
                $selected_metal_group = JPC_Metal_Groups::get_by_id($selected_metal->metal_group_id);
            }
        }
        
        ?>
        <div class="jpc-product-meta-box">
            <style>
                .jpc-product-meta-box { padding: 15px; }
                .jpc-section { margin-bottom: 25px; padding: 15px; background: #f9f9f9; border-left: 4px solid #2271b1; }
                .jpc-section h3 { margin-top: 0; color: #2271b1; }
                .jpc-section.highlight { background: #e7f3ff; border-left-color: #0073aa; }
                .jpc-form-field { margin-bottom: 15px; }
                .jpc-form-field label { display: block; margin-bottom: 5px; font-weight: 600; }
                .jpc-form-field input[type="text"],
                .jpc-form-field input[type="number"],
                .jpc-form-field select { width: 100%; max-width: 400px; }
                .jpc-radio-group { margin: 10px 0; }
                .jpc-radio-group label { display: inline-block; margin-right: 20px; font-weight: normal; }
                .jpc-radio-group input[type="radio"] { margin-right: 5px; }
                .jpc-conditional { margin-top: 15px; padding: 15px; background: white; border: 1px solid #ddd; border-radius: 4px; }
                .jpc-auto-calc-display { padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; color: #155724; font-weight: 600; }
                .jpc-help-text { font-size: 12px; color: #666; font-style: italic; margin-top: 5px; }
                .jpc-new-badge { background: #2196f3; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; font-weight: bold; margin-left: 10px; }
            </style>
            
            <!-- Metal Section -->
            <div class="jpc-section">
                <h3><?php _e('Metal Details', 'jewellery-price-calc'); ?></h3>
                
                <div class="jpc-form-field">
                    <label for="jpc_metal_id"><?php _e('Select Metal', 'jewellery-price-calc'); ?></label>
                    <select id="jpc_metal_id" name="jpc_metal_id">
                        <option value=""><?php _e('Select Metal', 'jewellery-price-calc'); ?></option>
                        <?php foreach ($metals as $metal): 
                            $metal_group = JPC_Metal_Groups::get_by_id($metal->metal_group_id);
                        ?>
                            <option value="<?php echo esc_attr($metal->id); ?>" 
                                    data-metal-group-id="<?php echo esc_attr($metal->metal_group_id); ?>"
                                    data-enable-making="<?php echo $metal_group ? esc_attr($metal_group->enable_making_charge) : '1'; ?>"
                                    data-enable-wastage="<?php echo $metal_group ? esc_attr($metal_group->enable_wastage_charge) : '1'; ?>"
                                    data-price="<?php echo esc_attr($metal->price_per_unit); ?>"
                                    data-making-charges="<?php echo esc_attr($metal->making_charges_per_gram ?? 0); ?>"
                                    <?php selected($metal_id, $metal->id); ?>>
                                <?php echo esc_html($metal->display_name); ?> 
                                (₹<?php echo number_format($metal->price_per_unit, 2); ?>/gram)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="jpc-form-field">
                    <label for="jpc_metal_weight"><?php _e('Metal Weight (grams)', 'jewellery-price-calc'); ?></label>
                    <input type="number" id="jpc_metal_weight" name="jpc_metal_weight" 
                           value="<?php echo esc_attr($metal_weight); ?>" 
                           step="0.001" min="0">
                </div>
                
                <?php if (!$selected_metal_group || $selected_metal_group->enable_wastage_charge): ?>
                <div class="jpc-form-field jpc-wastage-field">
                    <label for="jpc_wastage"><?php _e('Wastage (%)', 'jewellery-price-calc'); ?></label>
                    <input type="number" id="jpc_wastage" name="jpc_wastage" 
                           value="<?php echo esc_attr($wastage); ?>" 
                           step="0.01" min="0">
                    <p class="jpc-help-text"><?php _e('Wastage percentage to add to metal cost', 'jewellery-price-calc'); ?></p>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if (!$selected_metal_group || $selected_metal_group->enable_making_charge): ?>
            <!-- Making Charges Section v2.0.0 -->
            <div class="jpc-section highlight jpc-making-charges-section">
                <h3>
                    <?php _e('Making Charges', 'jewellery-price-calc'); ?>
                    <span class="jpc-new-badge">v2.0 NEW</span>
                </h3>
                
                <div class="jpc-radio-group">
                    <label>
                        <input type="radio" name="jpc_making_charges_mode" value="auto" 
                               <?php checked($making_charges_mode, 'auto'); ?>>
                        <?php _e('Auto Calculate (Based on Metal Weight × Per Gram Rate)', 'jewellery-price-calc'); ?>
                    </label>
                    <label>
                        <input type="radio" name="jpc_making_charges_mode" value="manual" 
                               <?php checked($making_charges_mode, 'manual'); ?>>
                        <?php _e('Manual Entry', 'jewellery-price-calc'); ?>
                    </label>
                </div>
                
                <!-- Auto Mode Display -->
                <div id="jpc_making_charges_auto" class="jpc-conditional" style="display: <?php echo $making_charges_mode === 'auto' ? 'block' : 'none'; ?>;">
                    <div class="jpc-auto-calc-display">
                        <span id="jpc_auto_making_charges_display">
                            <?php _e('Select metal and enter weight to see auto-calculated making charges', 'jewellery-price-calc'); ?>
                        </span>
                    </div>
                    <p class="jpc-help-text">
                        <?php _e('Making charges will be automatically calculated as: Metal Weight × Making Charges per Gram (set in metal configuration)', 'jewellery-price-calc'); ?>
                    </p>
                </div>
                
                <!-- Manual Mode Input -->
                <div id="jpc_making_charges_manual" class="jpc-conditional" style="display: <?php echo $making_charges_mode === 'manual' ? 'block' : 'none'; ?>;">
                    <div class="jpc-form-field">
                        <label><?php _e('Making Charges Type', 'jewellery-price-calc'); ?></label>
                        <div class="jpc-radio-group">
                            <label>
                                <input type="radio" name="jpc_making_charges_type" value="percentage" 
                                       <?php checked($making_charges_type, 'percentage'); ?>>
                                <?php _e('Percentage (%)', 'jewellery-price-calc'); ?>
                            </label>
                            <label>
                                <input type="radio" name="jpc_making_charges_type" value="fixed" 
                                       <?php checked($making_charges_type, 'fixed'); ?>>
                                <?php _e('Fixed Amount (₹)', 'jewellery-price-calc'); ?>
                            </label>
                        </div>
                    </div>
                    
                    <div class="jpc-form-field">
                        <label for="jpc_making_charges_value"><?php _e('Making Charges Value', 'jewellery-price-calc'); ?></label>
                        <input type="number" id="jpc_making_charges_value" name="jpc_making_charges_value" 
                               value="<?php echo esc_attr($making_charges_value); ?>" 
                               step="0.01" min="0">
                        <p class="jpc-help-text">
                            <?php _e('Enter percentage (e.g., 10 for 10%) or fixed amount (e.g., 500 for ₹500)', 'jewellery-price-calc'); ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php
            // Continue in next part...
            $this->render_diamond_section($post, $diamond_entry_mode, $diamonds, $diamond_groups, 
                                         $diamond_certifications, $diamond_shapes, $diamond_colours, 
                                         $diamond_clarities, $diamond_cuts, $diamond_id, $diamond_quantity,
                                         $manual_diamond_group_id, $manual_diamond_carat, 
                                         $manual_diamond_certification_id, $manual_diamond_shape_id,
                                         $manual_diamond_colour_id, $manual_diamond_clarity_id,
                                         $manual_diamond_cut_id, $manual_diamond_quantity,
                                         $manual_diamond_price_per_carat);
            
            $this->render_other_costs_section($discount_percentage, $extra_fields);
            ?>
