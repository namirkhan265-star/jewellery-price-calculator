<?php
/**
 * Frontend Display Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Frontend {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('woocommerce_single_product_summary', array($this, 'display_price_breakup'), 25);
        // Removed: add_action('woocommerce_after_add_to_cart_button', array($this, 'display_detailed_breakup'), 15);
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        if (is_product()) {
            wp_enqueue_style('jpc-frontend-css', JPC_PLUGIN_URL . 'assets/css/frontend.css', array(), JPC_VERSION);
            wp_enqueue_script('jpc-frontend-js', JPC_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), JPC_VERSION, true);
        }
    }
    
    /**
     * Display price breakup on product page
     */
    public function display_price_breakup() {
        if (get_option('jpc_show_price_breakup') !== 'yes') {
            return;
        }
        
        global $product;
        
        if (!$product) {
            return;
        }
        
        $product_id = $product->get_id();
        $breakup = get_post_meta($product_id, '_jpc_price_breakup', true);
        
        if (!$breakup || !is_array($breakup)) {
            return;
        }
        
        $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
        $metal = JPC_Metals::get_by_id($metal_id);
        
        if (!$metal) {
            return;
        }
        
        include JPC_PLUGIN_DIR . 'templates/frontend/price-breakup.php';
    }
    
    /**
     * Format price for display
     */
    public static function format_price($price) {
        return wc_price($price);
    }
}
