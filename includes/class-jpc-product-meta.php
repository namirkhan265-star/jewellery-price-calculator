<?php
/**
 * Product Meta Box Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Product_Meta {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Add meta box
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        
        // Save meta data
        add_action('save_post_product', array($this, 'save_meta_data'), 20);
        
        // Enqueue admin scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // AJAX handler for live price calculation
        add_action('wp_ajax_jpc_calculate_live_price', array($this, 'ajax_calculate_live_price'));
    }
    
    /**
     * Add meta box
     */
    public function add_meta_box() {
        add_meta_box(
            'jpc_product_meta',
            __('Jewellery Price Calculator', 'jewellery-price-calculator'),
            array($this, 'render_meta_box'),
            'product',
            'normal',
            'high'
        );
    }
    
    /**
     * Render meta box
     */
    public function render_meta_box($post) {
        wp_nonce_field('jpc_save_meta', 'jpc_meta_nonce');
        
        // Get saved values
        $metal_id = get_post_meta($post->ID, '_jpc_metal_id', true);
        $metal_weight = get_post_meta($post->ID, '_jpc_metal_weight', true);
        $diamond_id = get_post_meta($post->ID, '_jpc_diamond_id', true);
        $diamond_quantity = get_post_meta($post->ID, '_jpc_diamond_quantity', true);
        $making_charge = get_post_meta($post->ID, '_jpc_making_charge', true);
        $making_charge_type = get_post_meta($post->ID, '_jpc_making_charge_type', true) ?: 'percentage';
        $wastage_charge = get_post_meta($post->ID, '_jpc_wastage_charge', true);
        $wastage_charge_type = get_post_meta($post->ID, '_jpc_wastage_charge_type', true) ?: 'percentage';
        $pearl_cost = get_post_meta($post->ID, '_jpc_pearl_cost', true);
        $stone_cost = get_post_meta($post->ID, '_jpc_stone_cost', true);
        $extra_fee = get_post_meta($post->ID, '_jpc_extra_fee', true);
        $discount_percentage = get_post_meta($post->ID, '_jpc_discount_percentage', true);
        
        // Get extra fields
        $extra_field_1 = get_post_meta($post->ID, '_jpc_extra_field_1', true);
        $extra_field_2 = get_post_meta($post->ID, '_jpc_extra_field_2', true);
        $extra_field_3 = get_post_meta($post->ID, '_jpc_extra_field_3', true);
        $extra_field_4 = get_post_meta($post->ID, '_jpc_extra_field_4', true);
        $extra_field_5 = get_post_meta($post->ID, '_jpc_extra_field_5', true);
        
        // Get metals and diamonds
        $metals = JPC_Metals::get_all();
        $diamonds = JPC_Diamonds::get_all();
        
        // Load template
        include JPC_PLUGIN_DIR . 'templates/admin/product-meta-box.php';
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        global $post;
        
        if (($hook === 'post.php' || $hook === 'post-new.php') && $post->post_type === 'product') {
            wp_enqueue_style(
                'jpc-admin-style',
                JPC_PLUGIN_URL . 'assets/css/admin-style.css',
                array(),
                JPC_VERSION
            );
            
            wp_enqueue_script(
                'jpc-live-calculator',
                JPC_PLUGIN_URL . 'assets/js/live-calculator.js',
                array('jquery'),
                JPC_VERSION,
                true
            );
            
            wp_localize_script('jpc-live-calculator', 'jpcLiveCalc', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('jpc_live_calc_nonce')
            ));
        }
    }
    
    /**
     * Save meta data
     */
    public function save_meta_data($post_id) {
        // Check nonce
        if (!isset($_POST['jpc_meta_nonce']) || !wp_verify_nonce($_POST['jpc_meta_nonce'], 'jpc_save_meta')) {
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
        if (isset($_POST['_jpc_metal_id'])) {
            update_post_meta($post_id, '_jpc_metal_id', sanitize_text_field($_POST['_jpc_metal_id']));
        }
        
        if (isset($_POST['_jpc_metal_weight'])) {
            update_post_meta($post_id, '_jpc_metal_weight', sanitize_text_field($_POST['_jpc_metal_weight']));
        }
        
        // Save diamond data
        if (isset($_POST['_jpc_diamond_id'])) {
            update_post_meta($post_id, '_jpc_diamond_id', sanitize_text_field($_POST['_jpc_diamond_id']));
        }
        
        if (isset($_POST['_jpc_diamond_quantity'])) {
            update_post_meta($post_id, '_jpc_diamond_quantity', sanitize_text_field($_POST['_jpc_diamond_quantity']));
        }
        
        // Save charges
        if (isset($_POST['_jpc_making_charge'])) {
            update_post_meta($post_id, '_jpc_making_charge', sanitize_text_field($_POST['_jpc_making_charge']));
        }
        
        if (isset($_POST['_jpc_making_charge_type'])) {
            update_post_meta($post_id, '_jpc_making_charge_type', sanitize_text_field($_POST['_jpc_making_charge_type']));
        }
        
        if (isset($_POST['_jpc_wastage_charge'])) {
            update_post_meta($post_id, '_jpc_wastage_charge', sanitize_text_field($_POST['_jpc_wastage_charge']));
        }
        
        if (isset($_POST['_jpc_wastage_charge_type'])) {
            update_post_meta($post_id, '_jpc_wastage_charge_type', sanitize_text_field($_POST['_jpc_wastage_charge_type']));
        }
        
        // Save additional costs
        if (isset($_POST['_jpc_pearl_cost'])) {
            update_post_meta($post_id, '_jpc_pearl_cost', sanitize_text_field($_POST['_jpc_pearl_cost']));
        }
        
        if (isset($_POST['_jpc_stone_cost'])) {
            update_post_meta($post_id, '_jpc_stone_cost', sanitize_text_field($_POST['_jpc_stone_cost']));
        }
        
        if (isset($_POST['_jpc_extra_fee'])) {
            update_post_meta($post_id, '_jpc_extra_fee', sanitize_text_field($_POST['_jpc_extra_fee']));
        }
        
        // Save extra fields #1-5
        for ($i = 1; $i <= 5; $i++) {
            if (isset($_POST['_jpc_extra_field_' . $i])) {
                update_post_meta($post_id, '_jpc_extra_field_' . $i, sanitize_text_field($_POST['_jpc_extra_field_' . $i]));
            }
        }
        
        // Save discount
        if (isset($_POST['_jpc_discount_percentage'])) {
            update_post_meta($post_id, '_jpc_discount_percentage', sanitize_text_field($_POST['_jpc_discount_percentage']));
        }
    }
    
    /**
     * AJAX handler for live price calculation
     * UPDATED: Now supports 5 discount methods including "Discount After GST"
     */
    public function ajax_calculate_live_price() {
        check_ajax_referer('jpc_live_calc_nonce', 'nonce');
        
        $metal_id = intval($_POST['metal_id']);
        $metal_weight = floatval($_POST['metal_weight']);
        $diamond_id = intval($_POST['diamond_id']);
        $diamond_quantity = intval($_POST['diamond_quantity']);
        $making_charge = floatval($_POST['making_charge']);
        $making_charge_type = sanitize_text_field($_POST['making_charge_type']);
        $wastage_charge = floatval($_POST['wastage_charge']);
        $wastage_charge_type = sanitize_text_field($_POST['wastage_charge_type']);
        $pearl_cost = floatval($_POST['pearl_cost']);
        $stone_cost = floatval($_POST['stone_cost']);
        $extra_fee = floatval($_POST['extra_fee']);
        $discount_percentage = floatval($_POST['discount_percentage']);
        
        // Get extra fields #1-5
        $extra_field_1 = isset($_POST['extra_field_1']) ? floatval($_POST['extra_field_1']) : 0;
        $extra_field_2 = isset($_POST['extra_field_2']) ? floatval($_POST['extra_field_2']) : 0;
        $extra_field_3 = isset($_POST['extra_field_3']) ? floatval($_POST['extra_field_3']) : 0;
        $extra_field_4 = isset($_POST['extra_field_4']) ? floatval($_POST['extra_field_4']) : 0;
        $extra_field_5 = isset($_POST['extra_field_5']) ? floatval($_POST['extra_field_5']) : 0;
        $extra_field_costs = $extra_field_1 + $extra_field_2 + $extra_field_3 + $extra_field_4 + $extra_field_5;
        
        if (!$metal_id || !$metal_weight) {
            wp_send_json_error(array('message' => 'Metal and weight are required'));
            return;
        }
        
        $metal = JPC_Metals::get_by_id($metal_id);
        if (!$metal) {
            wp_send_json_error(array('message' => 'Invalid metal'));
            return;
        }
        
        $metal_group = JPC_Metal_Groups::get_by_id($metal->metal_group_id);
        
        // Calculate base metal price
        $metal_price = $metal_weight * $metal->price_per_unit;
        
        // Calculate diamond price
        $diamond_price = 0;
        if ($diamond_id && $diamond_quantity > 0) {
            $diamond = JPC_Diamonds::get_by_id($diamond_id);
            if ($diamond) {
                $diamond_unit_price = $diamond->price_per_carat * $diamond->carat;
                $diamond_price = $diamond_unit_price * $diamond_quantity;
            }
        }
        
        // Calculate making charge
        $making_charge_amount = 0;
        if ($metal_group->enable_making_charge && $making_charge > 0) {
            if ($making_charge_type === 'percentage') {
                $making_charge_amount = ($metal_price * $making_charge) / 100;
            } else {
                $making_charge_amount = $making_charge;
            }
        }
        
        // Calculate wastage charge
        $wastage_charge_amount = 0;
        if ($metal_group->enable_wastage_charge && $wastage_charge > 0) {
            if ($wastage_charge_type === 'percentage') {
                $wastage_charge_amount = ($metal_price * $wastage_charge) / 100;
            } else {
                $wastage_charge_amount = $wastage_charge;
            }
        }
        
        // Calculate subtotal before additional percentage
        $subtotal_before_additional = $metal_price + $diamond_price + $making_charge_amount + $wastage_charge_amount + $pearl_cost + $stone_cost + $extra_fee + $extra_field_costs;
        
        // Apply Additional Percentage (if enabled)
        $additional_percentage_amount = 0;
        $additional_percentage = floatval(get_option('jpc_additional_percentage_value', 0));
        $additional_percentage_label = get_option('jpc_additional_percentage_label', 'Additional Percentage');
        
        // Get discount calculation method
        $discount_method = get_option('jpc_discount_calculation_method', 'simple');
        
        // Get GST settings
        $gst_percentage = 0;
        $gst_label = get_option('jpc_gst_label', 'GST');
        $gst_enabled = get_option('jpc_enable_gst');
        
        if ($gst_enabled === 'yes' || $gst_enabled === '1' || $gst_enabled === 1 || $gst_enabled === true) {
            $gst_percentage = floatval(get_option('jpc_gst_value', 5));
            
            // Check for metal-specific GST rates
            $metal_group_name = strtolower(str_replace(' ', '_', $metal_group->name));
            $metal_specific_gst = get_option('jpc_gst_' . $metal_group_name);
            
            if ($metal_specific_gst === false || $metal_specific_gst === '') {
                $metal_group_name_no_underscore = strtolower(str_replace(' ', '', $metal_group->name));
                $metal_specific_gst = get_option('jpc_gst_' . $metal_group_name_no_underscore);
            }
            
            if ($metal_specific_gst !== false && $metal_specific_gst !== '' && $metal_specific_gst !== null) {
                $gst_percentage = floatval($metal_specific_gst);
            }
        }
        
        // ============================================================
        // METHOD 5: DISCOUNT AFTER GST (NEW!)
        // ============================================================
        if ($discount_method === 'discount_after_gst') {
            // FLOW: Subtotal → + Additional % → + GST → - Discount → Final Price
            
            // Step 1: Add Additional % to subtotal
            if ($additional_percentage > 0) {
                $additional_percentage_amount = ($subtotal_before_additional * $additional_percentage) / 100;
            }
            $subtotal_with_additional = $subtotal_before_additional + $additional_percentage_amount;
            
            // Step 2: Calculate and add GST
            $gst_amount = 0;
            if ($gst_enabled === 'yes' || $gst_enabled === '1' || $gst_enabled === 1 || $gst_enabled === true) {
                $gst_amount = ($subtotal_with_additional * $gst_percentage) / 100;
            }
            
            $total_with_gst = $subtotal_with_additional + $gst_amount;
            
            // Step 3: Apply discount on (Subtotal + Additional % + GST)
            $discount_amount = 0;
            if ($discount_percentage > 0) {
                $discount_amount = ($total_with_gst * $discount_percentage) / 100;
            }
            
            $final_price = $total_with_gst - $discount_amount;
            $price_before_discount = $total_with_gst;
            
            // Apply rounding
            $rounding = get_option('jpc_price_rounding', 'default');
            $final_price = $this->apply_rounding($final_price, $rounding);
            $price_before_discount = $this->apply_rounding($price_before_discount, $rounding);
            
            // Get extra field labels
            $extra_fields = array();
            for ($i = 1; $i <= 5; $i++) {
                $enabled = get_option('jpc_enable_extra_field_' . $i);
                if ($enabled === 'yes' || $enabled === '1' || $enabled === 1 || $enabled === true) {
                    $label = get_option('jpc_extra_field_label_' . $i, 'Extra Field #' . $i);
                    $value = ${'extra_field_' . $i};
                    $extra_fields[] = array(
                        'label' => $label,
                        'value' => $value
                    );
                }
            }
            
            wp_send_json_success(array(
                'final_price' => $final_price,
                'price_before_discount' => $price_before_discount,
                'breakdown' => array(
                    'metal_price' => $metal_price,
                    'diamond_price' => $diamond_price,
                    'making_charge' => $making_charge_amount,
                    'wastage_charge' => $wastage_charge_amount,
                    'pearl_cost' => $pearl_cost,
                    'stone_cost' => $stone_cost,
                    'extra_fee' => $extra_fee,
                    'extra_fields' => $extra_fields,
                    'additional_percentage' => $additional_percentage_amount,
                    'additional_percentage_label' => $additional_percentage_label,
                    'subtotal_before_gst' => $subtotal_with_additional,
                    'gst' => $gst_amount,
                    'gst_label' => $gst_label,
                    'gst_percentage' => $gst_percentage,
                    'total_with_gst' => $total_with_gst,
                    'discount_amount' => $discount_amount,
                    'discount_percentage' => $discount_percentage,
                    'calculation_method' => 'discount_after_gst'
                )
            ));
            return;
        }
        
        // ============================================================
        // METHODS 1-4: TRADITIONAL DISCOUNT BEFORE GST
        // ============================================================
        
        // Add Additional % for methods 1-4
        if ($additional_percentage > 0) {
            $additional_percentage_amount = ($subtotal_before_additional * $additional_percentage) / 100;
        }
        $subtotal_before_discount = $subtotal_before_additional + $additional_percentage_amount;
        
        // Calculate discount based on method
        $discount_amount = 0;
        $subtotal_after_discount = $subtotal_before_discount;
        
        if ($discount_percentage > 0) {
            $discountable_amount = 0;
            
            switch ($discount_method) {
                case 'simple':
                    // Method 1: Component-Based Discount
                    $discount_on_metals = get_option('jpc_discount_on_metals');
                    $discount_on_making = get_option('jpc_discount_on_making');
                    $discount_on_wastage = get_option('jpc_discount_on_wastage');
                    
                    if ($discount_on_metals === 'yes' || $discount_on_metals === '1' || $discount_on_metals === 1 || $discount_on_metals === true) {
                        $discountable_amount += $metal_price;
                    }
                    
                    if ($discount_on_making === 'yes' || $discount_on_making === '1' || $discount_on_making === 1 || $discount_on_making === true) {
                        $discountable_amount += $making_charge_amount;
                    }
                    
                    if ($discount_on_wastage === 'yes' || $discount_on_wastage === '1' || $discount_on_wastage === 1 || $discount_on_wastage === true) {
                        $discountable_amount += $wastage_charge_amount;
                    }
                    
                    if ($discountable_amount == 0) {
                        $discountable_amount = $subtotal_before_discount;
                    }
                    break;
                    
                case 'advanced':
                    // Method 2: All Components
                    $discountable_amount = $subtotal_before_discount;
                    break;
                    
                case 'total_before_gst':
                    // Method 3: Total Before GST
                    $discountable_amount = $subtotal_before_discount;
                    break;
                    
                case 'total_after_additional':
                    // Method 4: Total After Additional %
                    $discountable_amount = $subtotal_before_discount;
                    break;
                    
                default:
                    $discountable_amount = $subtotal_before_discount;
                    break;
            }
            
            $discount_amount = ($discountable_amount * $discount_percentage) / 100;
            $subtotal_after_discount = $subtotal_before_discount - $discount_amount;
        }
        
        // Calculate GST
        $gst_amount = 0;
        if ($gst_enabled === 'yes' || $gst_enabled === '1' || $gst_enabled === 1 || $gst_enabled === true) {
            $gst_base = get_option('jpc_gst_calculation_base', 'after_discount');
            
            if ($gst_base === 'after_discount') {
                $gst_amount = ($subtotal_after_discount * $gst_percentage) / 100;
            } else {
                $gst_amount = ($subtotal_before_discount * $gst_percentage) / 100;
            }
        }
        
        // Calculate final prices
        $final_price = $subtotal_after_discount + $gst_amount;
        
        $gst_on_full_subtotal = 0;
        if ($gst_enabled === 'yes' || $gst_enabled === '1' || $gst_enabled === 1 || $gst_enabled === true) {
            $gst_on_full_subtotal = ($subtotal_before_discount * $gst_percentage) / 100;
        }
        $price_before_discount = $subtotal_before_discount + $gst_on_full_subtotal;
        
        // Apply rounding
        $rounding = get_option('jpc_price_rounding', 'default');
        $final_price = $this->apply_rounding($final_price, $rounding);
        $price_before_discount = $this->apply_rounding($price_before_discount, $rounding);
        
        // Get extra field labels
        $extra_fields = array();
        for ($i = 1; $i <= 5; $i++) {
            $enabled = get_option('jpc_enable_extra_field_' . $i);
            if ($enabled === 'yes' || $enabled === '1' || $enabled === 1 || $enabled === true) {
                $label = get_option('jpc_extra_field_label_' . $i, 'Extra Field #' . $i);
                $value = ${'extra_field_' . $i};
                $extra_fields[] = array(
                    'label' => $label,
                    'value' => $value
                );
            }
        }
        
        wp_send_json_success(array(
            'final_price' => $final_price,
            'price_before_discount' => $price_before_discount,
            'breakdown' => array(
                'metal_price' => $metal_price,
                'diamond_price' => $diamond_price,
                'making_charge' => $making_charge_amount,
                'wastage_charge' => $wastage_charge_amount,
                'pearl_cost' => $pearl_cost,
                'stone_cost' => $stone_cost,
                'extra_fee' => $extra_fee,
                'extra_fields' => $extra_fields,
                'additional_percentage' => $additional_percentage_amount,
                'additional_percentage_label' => $additional_percentage_label,
                'gst' => $gst_amount,
                'gst_label' => $gst_label,
                'gst_percentage' => $gst_percentage,
                'discount_amount' => $discount_amount,
                'discount_percentage' => $discount_percentage,
                'calculation_method' => $discount_method
            )
        ));
    }
    
    /**
     * Apply price rounding
     */
    private function apply_rounding($price, $rounding) {
        switch ($rounding) {
            case 'nearest_10':
                return round($price / 10) * 10;
            case 'nearest_100':
                return round($price / 100) * 100;
            case 'nearest_1000':
                return round($price / 1000) * 1000;
            case 'ceil_10':
                return ceil($price / 10) * 10;
            case 'ceil_100':
                return ceil($price / 100) * 100;
            case 'ceil_1000':
                return ceil($price / 1000) * 1000;
            case 'floor_10':
                return floor($price / 10) * 10;
            case 'floor_100':
                return floor($price / 100) * 100;
            case 'floor_1000':
                return floor($price / 1000) * 1000;
            default:
                return round($price, 2);
        }
    }
}
