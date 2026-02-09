<?php
/**
 * Frontend Display Handler v2.3.3
 * Adds Product Details tab to WooCommerce product tabs
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
        // Only add hooks if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            return;
        }
        
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_filter('woocommerce_product_tabs', array($this, 'add_product_details_tab'), 10);
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        if (is_product()) {
            wp_enqueue_style('jpc-frontend-css', JPC_PLUGIN_URL . 'assets/css/frontend.css', array(), JPC_VERSION);
        }
    }
    
    /**
     * Add Product Details tab to WooCommerce product tabs
     */
    public function add_product_details_tab($tabs) {
        global $product;
        
        if (!$product) {
            return $tabs;
        }
        
        $product_id = $product->get_id();
        
        // Check if product has JPC data
        $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
        $diamond_id = get_post_meta($product_id, '_jpc_diamond_id', true);
        $price_breakup = get_post_meta($product_id, '_jpc_price_breakup', true);
        
        // Only add tab if product has JPC data
        if ($metal_id || $diamond_id || $price_breakup) {
            $tabs['jpc_product_details'] = array(
                'title'    => __('Product Details', 'jewellery-price-calculator'),
                'priority' => 5, // Show before Description tab (priority 10)
                'callback' => array($this, 'product_details_tab_content')
            );
        }
        
        return $tabs;
    }
    
    /**
     * Product Details tab content
     */
    public function product_details_tab_content() {
        echo do_shortcode('[jpc_product_details]');
    }
    
    /**
     * Format price for display
     */
    public static function format_price($price) {
        return wc_price($price);
    }
}

// Initialize only if WooCommerce is active
if (class_exists('WooCommerce')) {
    JPC_Frontend::get_instance();
}
