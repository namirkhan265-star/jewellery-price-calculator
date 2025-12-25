<?php
/**
 * Product Meta Handler
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
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_product', array($this, 'save_product_meta'), 10);
        add_action('woocommerce_product_after_variable_attributes', array($this, 'add_variation_fields'), 10, 3);
        add_action('woocommerce_save_product_variation', array($this, 'save_variation_fields'), 10, 2);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_jpc_calculate_live_price', array($this, 'ajax_calculate_live_price'));
        add_action('wp_ajax_jpc_sync_price_to_product', array($this, 'ajax_sync_price_to_product'));
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        if ('post.php' !== $hook && 'post-new.php' !== $hook) {
            return;
        }
        
        global $post;
        if (!$post || $post->post_type !== 'product') {
            return;
        }
        
        wp_enqueue_script(
            'jpc-live-calculator',
            JPC_PLUGIN_URL . 'assets/js/live-calculator.js',
            array('jquery'),
            JPC_VERSION,
            true
        );
        
        wp_localize_script('jpc-live-calculator', 'jpcLiveCalc', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('jpc_live_calc_nonce'),
        ));
    }
    
    /**
     * AJAX handler to sync calculated price to product price field
     */
    public function ajax_sync_price_to_product() {
        check_ajax_referer('jpc_live_calc_nonce', 'nonce');
        
        $final_price = floatval($_POST['final_price']);
        
        wp_send_json_success(array(
            'price' => $final_price,
            'formatted' => number_format($final_price, 2, '.', '')
        ));
    }
    
    /**
     * AJAX handler for live price calculation
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
        
        // Calculate subtotal
        $subtotal = $metal_price + $diamond_price + $making_charge_amount + $wastage_charge_amount + $pearl_cost + $stone_cost + $extra_fee;
        
        // Apply discount
        $discount_amount = 0;
        if (get_option('jpc_enable_discount') === 'yes' && $discount_percentage > 0) {
            $discount_on_metals = get_option('jpc_discount_on_metals') === 'yes';
            $discount_on_making = get_option('jpc_discount_on_making') === 'yes';
            $discount_on_wastage = get_option('jpc_discount_on_wastage') === 'yes';
            
            $discountable_amount = 0;
            if ($discount_on_metals) $discountable_amount += $metal_price;
            if ($discount_on_making) $discountable_amount += $making_charge_amount;
            if ($discount_on_wastage) $discountable_amount += $wastage_charge_amount;
            
            $discount_amount = ($discountable_amount * $discount_percentage) / 100;
            $subtotal -= $discount_amount;
        }
        
        // Calculate GST
        $gst_amount = 0;
        if (get_option('jpc_enable_gst') === 'yes') {
            $gst_percentage = floatval(get_option('jpc_gst_value', 5));
            
            $metal_group_name = strtolower($metal_group->name);
            $metal_gst = get_option('jpc_gst_' . $metal_group_name);
            
            if ($metal_gst !== false && $metal_gst !== '') {
                $gst_percentage = floatval($metal_gst);
            }
            
            $gst_amount = ($subtotal * $gst_percentage) / 100;
        }
        
        // Calculate final price
        $final_price = $subtotal + $gst_amount;
        
        wp_send_json_success(array(
            'metal_price' => $metal_price,
            'diamond_price' => $diamond_price,
            'making_charge' => $making_charge_amount,
            'wastage_charge' => $wastage_charge_amount,
            'pearl_cost' => $pearl_cost,
            'stone_cost' => $stone_cost,
            'extra_fee' => $extra_fee,
            'discount' => $discount_amount,
            'subtotal' => $subtotal,
            'gst' => $gst_amount,
            'final_price' => $final_price,
        ));
    }
    
    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'jpc_product_meta',
            __('Jewellery Price Calculator', 'jewellery-price-calc'),
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
        wp_nonce_field('jpc_product_meta_nonce', 'jpc_product_meta_nonce');
        
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
        
        $metals = JPC_Metals::get_all();
        $price_breakup = get_post_meta($post->ID, '_jpc_price_breakup', true);
        
        include JPC_PLUGIN_DIR . 'templates/admin/product-meta-box.php';
    }
    
    /**
     * Save product meta
     */
    public function save_product_meta($post_id) {
        // Verify nonce
        if (!isset($_POST['jpc_product_meta_nonce']) || !wp_verify_nonce($_POST['jpc_product_meta_nonce'], 'jpc_product_meta_nonce')) {
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
        
        // Check if it's a product
        if (get_post_type($post_id) !== 'product') {
            return;
        }
        
        // Save metal data
        if (isset($_POST['_jpc_metal_id'])) {
            update_post_meta($post_id, '_jpc_metal_id', sanitize_text_field($_POST['_jpc_metal_id']));
        }
        
        if (isset($_POST['_jpc_metal_weight'])) {
            update_post_meta($post_id, '_jpc_metal_weight', floatval($_POST['_jpc_metal_weight']));
        }
        
        // Save diamond data
        if (isset($_POST['_jpc_diamond_id'])) {
            update_post_meta($post_id, '_jpc_diamond_id', sanitize_text_field($_POST['_jpc_diamond_id']));
        }
        
        if (isset($_POST['_jpc_diamond_quantity'])) {
            update_post_meta($post_id, '_jpc_diamond_quantity', intval($_POST['_jpc_diamond_quantity']));
        }
        
        if (isset($_POST['_jpc_making_charge'])) {
            update_post_meta($post_id, '_jpc_making_charge', floatval($_POST['_jpc_making_charge']));
        }
        
        if (isset($_POST['_jpc_making_charge_type'])) {
            update_post_meta($post_id, '_jpc_making_charge_type', sanitize_text_field($_POST['_jpc_making_charge_type']));
        }
        
        if (isset($_POST['_jpc_wastage_charge'])) {
            update_post_meta($post_id, '_jpc_wastage_charge', floatval($_POST['_jpc_wastage_charge']));
        }
        
        if (isset($_POST['_jpc_wastage_charge_type'])) {
            update_post_meta($post_id, '_jpc_wastage_charge_type', sanitize_text_field($_POST['_jpc_wastage_charge_type']));
        }
        
        // Save additional costs
        if (isset($_POST['_jpc_pearl_cost'])) {
            update_post_meta($post_id, '_jpc_pearl_cost', floatval($_POST['_jpc_pearl_cost']));
        }
        
        if (isset($_POST['_jpc_stone_cost'])) {
            update_post_meta($post_id, '_jpc_stone_cost', floatval($_POST['_jpc_stone_cost']));
        }
        
        if (isset($_POST['_jpc_extra_fee'])) {
            update_post_meta($post_id, '_jpc_extra_fee', floatval($_POST['_jpc_extra_fee']));
        }
        
        // Save discount
        if (isset($_POST['_jpc_discount_percentage'])) {
            update_post_meta($post_id, '_jpc_discount_percentage', floatval($_POST['_jpc_discount_percentage']));
        }
        
        // Save extra fields
        for ($i = 1; $i <= 5; $i++) {
            if (isset($_POST['_jpc_extra_field_' . $i])) {
                update_post_meta($post_id, '_jpc_extra_field_' . $i, sanitize_text_field($_POST['_jpc_extra_field_' . $i]));
            }
        }
    }
    
    /**
     * Add variation fields
     */
    public function add_variation_fields($loop, $variation_data, $variation) {
        $variation_id = $variation->ID;
        
        $metal_id = get_post_meta($variation_id, '_jpc_metal_id', true);
        $metal_weight = get_post_meta($variation_id, '_jpc_metal_weight', true);
        $diamond_id = get_post_meta($variation_id, '_jpc_diamond_id', true);
        $diamond_quantity = get_post_meta($variation_id, '_jpc_diamond_quantity', true);
        $making_charge = get_post_meta($variation_id, '_jpc_making_charge', true);
        $making_charge_type = get_post_meta($variation_id, '_jpc_making_charge_type', true) ?: 'percentage';
        $wastage_charge = get_post_meta($variation_id, '_jpc_wastage_charge', true);
        $wastage_charge_type = get_post_meta($variation_id, '_jpc_wastage_charge_type', true) ?: 'percentage';
        
        $metals = JPC_Metals::get_all();
        
        include JPC_PLUGIN_DIR . 'templates/admin/variation-fields.php';
    }
    
    /**
     * Save variation fields
     */
    public function save_variation_fields($variation_id, $i) {
        if (isset($_POST['_jpc_metal_id'][$i])) {
            update_post_meta($variation_id, '_jpc_metal_id', sanitize_text_field($_POST['_jpc_metal_id'][$i]));
        }
        
        if (isset($_POST['_jpc_metal_weight'][$i])) {
            update_post_meta($variation_id, '_jpc_metal_weight', floatval($_POST['_jpc_metal_weight'][$i]));
        }
        
        if (isset($_POST['_jpc_diamond_id'][$i])) {
            update_post_meta($variation_id, '_jpc_diamond_id', sanitize_text_field($_POST['_jpc_diamond_id'][$i]));
        }
        
        if (isset($_POST['_jpc_diamond_quantity'][$i])) {
            update_post_meta($variation_id, '_jpc_diamond_quantity', intval($_POST['_jpc_diamond_quantity'][$i]));
        }
        
        if (isset($_POST['_jpc_making_charge'][$i])) {
            update_post_meta($variation_id, '_jpc_making_charge', floatval($_POST['_jpc_making_charge'][$i]));
        }
        
        if (isset($_POST['_jpc_making_charge_type'][$i])) {
            update_post_meta($variation_id, '_jpc_making_charge_type', sanitize_text_field($_POST['_jpc_making_charge_type'][$i]));
        }
        
        if (isset($_POST['_jpc_wastage_charge'][$i])) {
            update_post_meta($variation_id, '_jpc_wastage_charge', floatval($_POST['_jpc_wastage_charge'][$i]));
        }
        
        if (isset($_POST['_jpc_wastage_charge_type'][$i])) {
            update_post_meta($variation_id, '_jpc_wastage_charge_type', sanitize_text_field($_POST['_jpc_wastage_charge_type'][$i]));
        }
    }
}
