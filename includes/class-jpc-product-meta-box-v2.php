<?php
/**
 * Product Meta Box Handler v2.5.26
 * Enhanced with:
 * - Making Charges Toggle (Auto/Manual)
 * - Manual Diamond Entry with 4Cs
 * v2.5.26: CRITICAL FIX - Save Additional Cost Fields with correct _value suffix
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Product_Meta_Box {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        add_action('save_post_product', array($this, 'save_meta_box'), 10, 2);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // AJAX handlers
        add_action('wp_ajax_jpc_calculate_auto_making_charges', array($this, 'ajax_calculate_auto_making_charges'));
        add_action('wp_ajax_jpc_calculate_manual_diamond_price', array($this, 'ajax_calculate_manual_diamond_price'));
    }
    
    /**
     * Add meta box
     */
    public function add_meta_box() {
        add_meta_box(
            'jpc_product_data',
            __('Jewellery Price Calculator', 'jewellery-price-calc'),
            array($this, 'render_meta_box'),
            'product',
            'normal',
            'high'
        );
    }
    
    /**
     * Enqueue scripts
     */
    public function enqueue_scripts($hook) {
        if ('post.php' !== $hook && 'post-new.php' !== $hook) {
            return;
        }
        
        global $post;
        if (!$post || $post->post_type !== 'product') {
            return;
        }
        
        wp_enqueue_script('jpc-product-meta-box', JPC_PLUGIN_URL . 'assets/js/product-meta-box-v2.js', array('jquery'), JPC_VERSION, true);
        wp_localize_script('jpc-product-meta-box', 'jpcProductMeta', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('jpc_product_meta_nonce'),
        ));
    }
    
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
        // Get selected metal's group settings for conditional display
$selected_metal = null;
if ($metal_id) {
    foreach ($metals as $m) {
        if ($m->id == $metal_id) {
            $selected_metal = $m;
            break;
        }
    }
}
$enable_making = $selected_metal ? ($selected_metal->enable_making_charge ?? 1) : 1;
$enable_wastage = $selected_metal ? ($selected_metal->enable_wastage_charge ?? 1) : 1;
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
                        <?php foreach ($metals as $metal): ?>
                           <option value="<?php echo esc_attr($metal->id); ?>" 
        data-price="<?php echo esc_attr($metal->price_per_unit); ?>"
        data-making-charges="<?php echo esc_attr($metal->making_charges_per_gram ?? 0); ?>"
        data-enable-making="<?php echo esc_attr($metal->enable_making_charge ?? 1); ?>"
        data-enable-wastage="<?php echo esc_attr($metal->enable_wastage_charge ?? 1); ?>"
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
                
                <div class="jpc-form-field" id="jpc_wastage_field" style="display: <?php echo $enable_wastage ? 'block' : 'none'; ?>;">
    <label for="jpc_wastage"><?php _e('Wastage (%)', 'jewellery-price-calc'); ?></label>
    <input type="number" id="jpc_wastage" name="jpc_wastage" 
           value="<?php echo esc_attr($wastage); ?>" 
           step="0.01" min="0">
</div>
            </div>
            
            <!-- Making Charges Section v2.0.0 -->
<div class="jpc-section highlight" id="jpc_making_charges_section" style="display: <?php echo $enable_making ? 'block' : 'none'; ?>;">
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
            
        </div>
        <?php
    }
    
    /**
     * Render diamond section (Part 2 - will create separately)
     */
    private function render_diamond_section($post, $diamond_entry_mode, $diamonds, $diamond_groups, 
                                           $diamond_certifications, $diamond_shapes, $diamond_colours, 
                                           $diamond_clarities, $diamond_cuts, $diamond_id, $diamond_quantity,
                                           $manual_diamond_group_id, $manual_diamond_carat, 
                                           $manual_diamond_certification_id, $manual_diamond_shape_id,
                                           $manual_diamond_colour_id, $manual_diamond_clarity_id,
                                           $manual_diamond_cut_id, $manual_diamond_quantity,
                                           $manual_diamond_price_per_carat) {
        // Will implement in next file
        include JPC_PLUGIN_DIR . 'templates/product-meta-box/diamond-section-v2.php';
    }
    
    /**
     * Render other costs section
     */
    private function render_other_costs_section($discount_percentage, $extra_fields) {
        // Will implement in next file
        include JPC_PLUGIN_DIR . 'templates/product-meta-box/other-costs-section.php';
    }
    
    /**
     * Save meta box (v2.5.26 - Fixed Additional Cost Fields to use _value suffix)
     */
    public function save_meta_box($post_id, $post) {
        // Verify nonce
        if (!isset($_POST['jpc_product_meta_box_nonce']) || 
            !wp_verify_nonce($_POST['jpc_product_meta_box_nonce'], 'jpc_product_meta_box')) {
            return;
        }
        
        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save metal data
        update_post_meta($post_id, '_jpc_metal_id', sanitize_text_field($_POST['jpc_metal_id'] ?? ''));
        update_post_meta($post_id, '_jpc_metal_weight', floatval($_POST['jpc_metal_weight'] ?? 0));
        update_post_meta($post_id, '_jpc_wastage', floatval($_POST['jpc_wastage'] ?? 0));
        
        // Save making charges v2.0.0
        update_post_meta($post_id, '_jpc_making_charges_mode', sanitize_text_field($_POST['jpc_making_charges_mode'] ?? 'auto'));
        update_post_meta($post_id, '_jpc_making_charges_value', floatval($_POST['jpc_making_charges_value'] ?? 0));
        update_post_meta($post_id, '_jpc_making_charges_type', sanitize_text_field($_POST['jpc_making_charges_type'] ?? 'percentage'));
        
        // Save diamond entry mode v2.0.0
        $diamond_entry_mode = sanitize_text_field($_POST['jpc_diamond_entry_mode'] ?? 'dropdown');
        update_post_meta($post_id, '_jpc_diamond_entry_mode', $diamond_entry_mode);
        
        if ($diamond_entry_mode === 'dropdown') {
            // Save dropdown mode data
            update_post_meta($post_id, '_jpc_diamond_id', sanitize_text_field($_POST['jpc_diamond_id'] ?? ''));
            update_post_meta($post_id, '_jpc_diamond_quantity', floatval($_POST['jpc_diamond_quantity'] ?? 0));
            
            // Clear manual fields
            $this->clear_manual_diamond_fields($post_id);
        } else {
            // Save manual mode data
            update_post_meta($post_id, '_jpc_manual_diamond_group_id', sanitize_text_field($_POST['jpc_manual_diamond_group_id'] ?? ''));
            update_post_meta($post_id, '_jpc_manual_diamond_carat', floatval($_POST['jpc_manual_diamond_carat'] ?? 0));
            update_post_meta($post_id, '_jpc_manual_diamond_certification_id', sanitize_text_field($_POST['jpc_manual_diamond_certification_id'] ?? ''));
            update_post_meta($post_id, '_jpc_manual_diamond_shape_id', sanitize_text_field($_POST['jpc_manual_diamond_shape_id'] ?? ''));
            update_post_meta($post_id, '_jpc_manual_diamond_colour_id', sanitize_text_field($_POST['jpc_manual_diamond_colour_id'] ?? ''));
            update_post_meta($post_id, '_jpc_manual_diamond_clarity_id', sanitize_text_field($_POST['jpc_manual_diamond_clarity_id'] ?? ''));
            update_post_meta($post_id, '_jpc_manual_diamond_cut_id', sanitize_text_field($_POST['jpc_manual_diamond_cut_id'] ?? ''));
            update_post_meta($post_id, '_jpc_manual_diamond_quantity', floatval($_POST['jpc_manual_diamond_quantity'] ?? 0));
            update_post_meta($post_id, '_jpc_manual_diamond_price_per_carat', floatval($_POST['jpc_manual_diamond_price_per_carat'] ?? 0));
            
            // Clear dropdown fields
            delete_post_meta($post_id, '_jpc_diamond_id');
        }
        
        // v2.5.26: CRITICAL FIX - Save Additional Cost Fields with correct _value suffix
        update_post_meta($post_id, '_jpc_stone_cost_value', floatval($_POST['jpc_stone_cost_value'] ?? 0));
        update_post_meta($post_id, '_jpc_pearl_cost_value', floatval($_POST['jpc_pearl_cost_value'] ?? 0));
        update_post_meta($post_id, '_jpc_extra_fee_value', floatval($_POST['jpc_extra_fee_value'] ?? 0));
        
        // Save discount
        update_post_meta($post_id, '_jpc_discount_percentage', floatval($_POST['jpc_discount_percentage'] ?? 0));
        
        // Save extra fields
        for ($i = 1; $i <= 5; $i++) {
            update_post_meta($post_id, '_jpc_extra_field_' . $i, sanitize_text_field($_POST['jpc_extra_field_' . $i] ?? ''));
        }
        
        // Recalculate and update price
        JPC_Price_Calculator::calculate_and_update_price($post_id);
    }
    
    /**
     * Clear manual diamond fields
     */
    private function clear_manual_diamond_fields($post_id) {
        delete_post_meta($post_id, '_jpc_manual_diamond_group_id');
        delete_post_meta($post_id, '_jpc_manual_diamond_carat');
        delete_post_meta($post_id, '_jpc_manual_diamond_certification_id');
        delete_post_meta($post_id, '_jpc_manual_diamond_shape_id');
        delete_post_meta($post_id, '_jpc_manual_diamond_colour_id');
        delete_post_meta($post_id, '_jpc_manual_diamond_clarity_id');
        delete_post_meta($post_id, '_jpc_manual_diamond_cut_id');
        delete_post_meta($post_id, '_jpc_manual_diamond_quantity');
        delete_post_meta($post_id, '_jpc_manual_diamond_price_per_carat');
    }
    
    /**
     * AJAX: Calculate auto making charges
     */
    public function ajax_calculate_auto_making_charges() {
        check_ajax_referer('jpc_product_meta_nonce', 'nonce');
        
        $metal_id = intval($_POST['metal_id']);
        $metal_weight = floatval($_POST['metal_weight']);
        
        $metal = JPC_Metals::get_by_id($metal_id);
        
        if (!$metal) {
            wp_send_json_error(array('message' => 'Metal not found'));
        }
        
        $making_charges_per_gram = $metal->making_charges_per_gram ?? 0;
        $total_making_charges = $metal_weight * $making_charges_per_gram;
        
        wp_send_json_success(array(
            'making_charges_per_gram' => $making_charges_per_gram,
            'total_making_charges' => $total_making_charges,
            'formatted' => sprintf(
                __('Auto-calculated: ₹%s (%s grams × ₹%s per gram)', 'jewellery-price-calc'),
                number_format($total_making_charges, 2),
                number_format($metal_weight, 3),
                number_format($making_charges_per_gram, 2)
            )
        ));
    }
    
    /**
     * AJAX: Calculate manual diamond price
     */
    public function ajax_calculate_manual_diamond_price() {
        check_ajax_referer('jpc_product_meta_nonce', 'nonce');
        
        // Get all inputs
        $carat = floatval($_POST['carat']);
        $quantity = floatval($_POST['quantity']);
        $base_price = floatval($_POST['base_price']);
        $shape_id = intval($_POST['shape_id']);
        $colour_id = intval($_POST['colour_id']);
        $clarity_id = intval($_POST['clarity_id']);
        $cut_id = intval($_POST['cut_id']);
        $cert_id = intval($_POST['cert_id']);
        
        // Calculate with adjustments
        $adjusted_price = $base_price;
        
        // Apply 4Cs adjustments
        if ($shape_id) {
            $shape = JPC_Diamond_Shapes::get_by_id($shape_id);
            $adjusted_price = $this->apply_adjustment($adjusted_price, $shape);
        }
        
        if ($colour_id) {
            $colour = JPC_Diamond_Colours::get_by_id($colour_id);
            $adjusted_price = $this->apply_adjustment($adjusted_price, $colour);
        }
        
        if ($clarity_id) {
            $clarity = JPC_Diamond_Clarities::get_by_id($clarity_id);
            $adjusted_price = $this->apply_adjustment($adjusted_price, $clarity);
        }
        
        if ($cut_id) {
            $cut = JPC_Diamond_Cuts::get_by_id($cut_id);
            $adjusted_price = $this->apply_adjustment($adjusted_price, $cut);
        }
        
        if ($cert_id) {
            $cert = JPC_Diamond_Certifications::get_by_id($cert_id);
            $adjusted_price = $this->apply_adjustment($adjusted_price, $cert);
        }
        
        $total_cost = $adjusted_price * $carat * $quantity;
        
        wp_send_json_success(array(
            'adjusted_price_per_carat' => $adjusted_price,
            'total_cost' => $total_cost,
            'formatted' => sprintf(
                __('Estimated: ₹%s (%s ct × %s pcs × ₹%s/ct)', 'jewellery-price-calc'),
                number_format($total_cost, 2),
                number_format($carat, 2),
                number_format($quantity, 0),
                number_format($adjusted_price, 2)
            )
        ));
    }
    
    /**
     * Apply adjustment to price
     */
    private function apply_adjustment($price, $attribute) {
        if (!$attribute) return $price;
        
        if ($attribute->adjustment_type === 'percentage') {
            return $price * (1 + ($attribute->adjustment_value / 100));
        } else {
            return $price + $attribute->adjustment_value;
        }
    }
}
