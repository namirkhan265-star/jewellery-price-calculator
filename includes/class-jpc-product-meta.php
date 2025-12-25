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
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('woocommerce_process_product_meta', array($this, 'save_product_meta'), 10);
        add_action('woocommerce_save_product_variation', array($this, 'save_variation_meta'), 10, 2);
        
        // Add variation fields
        add_action('woocommerce_product_after_variable_attributes', array($this, 'add_variation_fields'), 10, 3);
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
        if (!isset($_POST['jpc_product_meta_nonce']) || !wp_verify_nonce($_POST['jpc_product_meta_nonce'], 'jpc_product_meta_nonce')) {
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
            update_post_meta($post_id, '_jpc_metal_weight', floatval($_POST['_jpc_metal_weight']));
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
        $making_charge = get_post_meta($variation_id, '_jpc_making_charge', true);
        $making_charge_type = get_post_meta($variation_id, '_jpc_making_charge_type', true) ?: 'percentage';
        $wastage_charge = get_post_meta($variation_id, '_jpc_wastage_charge', true);
        $wastage_charge_type = get_post_meta($variation_id, '_jpc_wastage_charge_type', true) ?: 'percentage';
        
        $metals = JPC_Metals::get_all();
        
        include JPC_PLUGIN_DIR . 'templates/admin/variation-fields.php';
    }
    
    /**
     * Save variation meta
     */
    public function save_variation_meta($variation_id, $i) {
        if (isset($_POST['_jpc_metal_id'][$i])) {
            update_post_meta($variation_id, '_jpc_metal_id', sanitize_text_field($_POST['_jpc_metal_id'][$i]));
        }
        
        if (isset($_POST['_jpc_metal_weight'][$i])) {
            update_post_meta($variation_id, '_jpc_metal_weight', floatval($_POST['_jpc_metal_weight'][$i]));
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
