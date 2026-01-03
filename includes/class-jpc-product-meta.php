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
        add_action('add_meta_boxes', array($this, 'add_product_meta_box'));
        add_action('woocommerce_process_product_meta', array($this, 'save_product_meta'), 10);
        add_action('woocommerce_save_product_variation', array($this, 'save_product_meta'), 10);
        
        // AJAX handler for live price calculation
        add_action('wp_ajax_jpc_calculate_live_price', array($this, 'ajax_calculate_live_price'));
        
        // Enqueue admin scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        global $post;
        
        if (('post.php' === $hook || 'post-new.php' === $hook) && 'product' === $post->post_type) {
            wp_enqueue_script('jpc-product-meta', JPC_PLUGIN_URL . 'assets/js/product-meta.js', array('jquery'), JPC_VERSION, true);
            
            wp_localize_script('jpc-product-meta', 'jpcProductMeta', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('jpc_live_calc_nonce')
            ));
        }
    }
    
    /**
     * Add product meta box
     */
    public function add_product_meta_box() {
        add_meta_box(
            'jpc_product_meta',
            __('Jewellery Price Calculator', 'jewellery-price-calc'),
            array($this, 'render_product_meta_box'),
            'product',
            'normal',
            'high'
        );
    }
    
    /**
     * Render product meta box
     */
    public function render_product_meta_box($post) {
        // Get all metals
        $metals = JPC_Metals::get_all();
        
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
        
        // Load template
        include JPC_PLUGIN_DIR . 'templates/admin/product-meta-box.php';
    }
    
    /**
     * Save product meta - IMPROVED VERSION
     * Ensures ALL fields are saved properly and breakup is regenerated
     */
    public function save_product_meta($post_id) {
        // Security checks
        if (!isset($_POST['_jpc_metal_id'])) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
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
        
        // Save making charge
        if (isset($_POST['_jpc_making_charge'])) {
            update_post_meta($post_id, '_jpc_making_charge', sanitize_text_field($_POST['_jpc_making_charge']));
        }
        
        if (isset($_POST['_jpc_making_charge_type'])) {
            update_post_meta($post_id, '_jpc_making_charge_type', sanitize_text_field($_POST['_jpc_making_charge_type']));
        }
        
        // Save wastage charge
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
        
        // Save extra fields #1-5 - ALWAYS save even if empty to clear old values
        for ($i = 1; $i <= 5; $i++) {
            $field_key = '_jpc_extra_field_' . $i;
            if (isset($_POST[$field_key])) {
                $value = sanitize_text_field($_POST[$field_key]);
                update_post_meta($post_id, $field_key, $value);
            } else {
                // If field is not in POST, set it to empty (clear old value)
                update_post_meta($post_id, $field_key, '');
            }
        }
        
        // Save discount
        if (isset($_POST['_jpc_discount_percentage'])) {
            update_post_meta($post_id, '_jpc_discount_percentage', sanitize_text_field($_POST['_jpc_discount_percentage']));
        }
        
        // FORCE BREAKUP REGENERATION after all meta is saved
        // This ensures the breakup array includes the latest extra field values
        JPC_Price_Calculator::calculate_and_store_breakup($post_id);
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
        
        // Get extra fields #1-5
        $extra_field_costs = array();
        for ($i = 1; $i <= 5; $i++) {
            $enabled = get_option('jpc_enable_extra_field_' . $i);
            if ($enabled === 'yes' || $enabled === '1' || $enabled === 1 || $enabled === true) {
                $label = get_option('jpc_extra_field_label_' . $i, 'Extra Field #' . $i);
                $value = isset($_POST['extra_field_' . $i]) ? floatval($_POST['extra_field_' . $i]) : 0;
                $extra_field_costs[] = array(
                    'label' => $label,
                    'value' => $value
                );
            }
        }
        
        $discount_percentage = floatval($_POST['discount_percentage']);
        
        // Get metal data
        $metal = JPC_Metals::get_by_id($metal_id);
        
        if (!$metal) {
            wp_send_json_error(array('message' => 'Invalid metal selected'));
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
        
        // Calculate extra field costs total
        $extra_field_costs_total = 0;
        foreach ($extra_field_costs as $field) {
            $extra_field_costs_total += $field['value'];
        }
        
        // Calculate subtotal (all components before Additional %)
        $subtotal = $metal_price + $diamond_price + $making_charge_amount + $wastage_charge_amount + $pearl_cost + $stone_cost + $extra_fee + $extra_field_costs_total;
        
        // Get Additional Percentage setting
        $additional_percentage = floatval(get_option('jpc_additional_percentage_value', 0));
        $additional_percentage_label = get_option('jpc_additional_percentage_label', 'Additional Percentage');
        $additional_percentage_amount = 0;
        if ($additional_percentage > 0) {
            $additional_percentage_amount = ($subtotal * $additional_percentage) / 100;
        }
        
        // Get discount calculation method
        $discount_method = get_option('jpc_discount_calculation_method', 'simple');
        
        // Calculate discount based on method
        $discount_amount = 0;
        $discountable_amount = 0;
        
        switch ($discount_method) {
            case 'simple':
                // Method 1: Component-based (Metal, Making, Wastage only)
                $discountable_amount = $metal_price + $making_charge_amount + $wastage_charge_amount;
                break;
                
            case 'advanced':
                // Method 2: All components
                $discountable_amount = $subtotal;
                break;
                
            case 'total_before_gst':
                // Method 3: Discount on complete subtotal (including Additional %)
                $discountable_amount = $subtotal + $additional_percentage_amount;
                break;
                
            case 'total_after_additional':
                // Method 4: Discount includes Additional %
                $discountable_amount = $subtotal + $additional_percentage_amount;
                break;
                
            case 'discount_after_gst':
                // Method 5: Discount applied AFTER GST
                // We'll handle this differently below
                $discountable_amount = 0; // Not used in this method
                break;
                
            default:
                // Fallback to simple
                $discountable_amount = $metal_price + $making_charge_amount + $wastage_charge_amount;
        }
        
        // Calculate discount amount (except for discount_after_gst method)
        if ($discount_method !== 'discount_after_gst' && $discount_percentage > 0) {
            $discount_amount = ($discountable_amount * $discount_percentage) / 100;
        }
        
        // Calculate GST
        $gst_enabled = get_option('jpc_enable_gst');
        $gst_percentage = floatval(get_option('jpc_gst_value', 5));
        $gst_amount = 0;
        
        // Get metal-specific GST if enabled
        $use_metal_gst = get_option('jpc_use_metal_specific_gst');
        if ($use_metal_gst === 'yes' && !empty($metal->gst_percentage)) {
            $gst_percentage = floatval($metal->gst_percentage);
        }
        
        if ($gst_enabled === 'yes') {
            if ($discount_method === 'discount_after_gst') {
                // Method 5: Add GST BEFORE discount
                $base_for_gst = $subtotal + $additional_percentage_amount;
                $gst_amount = ($base_for_gst * $gst_percentage) / 100;
                
                // Now calculate discount on (Subtotal + Additional % + GST)
                if ($discount_percentage > 0) {
                    $total_with_gst = $base_for_gst + $gst_amount;
                    $discount_amount = ($total_with_gst * $discount_percentage) / 100;
                }
            } else {
                // Methods 1-4: GST calculation based on setting
                $gst_calculation_base = get_option('jpc_gst_calculation_base', 'after_discount');
                
                if ($gst_calculation_base === 'after_discount' || $discount_method === 'total_before_gst') {
                    // GST on discounted amount
                    $base_for_gst = $subtotal + $additional_percentage_amount - $discount_amount;
                } else {
                    // GST on original price
                    $base_for_gst = $subtotal + $additional_percentage_amount;
                }
                
                $gst_amount = ($base_for_gst * $gst_percentage) / 100;
            }
        }
        
        // Calculate final prices
        if ($discount_method === 'discount_after_gst') {
            // Method 5: Final = Subtotal + Additional % + GST - Discount
            $regular_price = $subtotal + $additional_percentage_amount + $gst_amount;
            $sale_price = $regular_price - $discount_amount;
        } else {
            // Methods 1-4: Final = Subtotal + Additional % - Discount + GST
            $regular_price = $subtotal + $additional_percentage_amount + $gst_amount;
            $sale_price = $subtotal + $additional_percentage_amount - $discount_amount + $gst_amount;
        }
        
        // Build response with detailed breakup
        // IMPORTANT: Include both naming conventions for backward compatibility
        $response = array(
            // New naming (primary)
            'price_before_discount' => $regular_price,
            'final_price' => $sale_price,
            
            // Old naming (backward compatibility)
            'regular_price' => $regular_price,
            'sale_price' => $sale_price,
            
            'discount_amount' => $discount_amount,
            'discount_percentage' => $discount_percentage,
            
            // Include both 'breakup' and 'breakdown' for compatibility
            'breakup' => array(
                'metal_price' => $metal_price,
                'diamond_price' => $diamond_price,
                'making_charge' => $making_charge_amount,
                'wastage_charge' => $wastage_charge_amount,
                'pearl_cost' => $pearl_cost,
                'stone_cost' => $stone_cost,
                'extra_fee' => $extra_fee,
                'extra_fields' => $extra_field_costs,
                'subtotal' => $subtotal,
                'additional_percentage' => $additional_percentage_amount,
                'additional_percentage_label' => $additional_percentage_label,
                'gst' => $gst_amount,
                'gst_percentage' => $gst_percentage,
                'gst_label' => 'GST',
                'discount_method' => $discount_method,
                'discountable_amount' => $discountable_amount,
                'discount_amount' => $discount_amount,
                'discount_percentage' => $discount_percentage
            )
        );
        
        // Add 'breakdown' as alias for 'breakup'
        $response['breakdown'] = $response['breakup'];
        
        wp_send_json_success($response);
    }
}
