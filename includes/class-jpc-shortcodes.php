<?php
/**
 * Shortcodes Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Shortcodes {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_shortcode('jpc_metal_rates', array($this, 'metal_rates_shortcode'));
        add_shortcode('jpc_metal_rates_marquee', array($this, 'metal_rates_marquee_shortcode'));
        add_shortcode('jpc_metal_rates_table', array($this, 'metal_rates_table_shortcode'));
        add_shortcode('jpc_product_details', array($this, 'product_details_shortcode'));
    }
    
    /**
     * Product details shortcode - accordion style
     */
    public function product_details_shortcode($atts) {
        global $product;
        
        if (!$product) {
            return '';
        }
        
        $product_id = $product->get_id();
        
        // Get product meta
        $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
        $metal_weight = get_post_meta($product_id, '_jpc_metal_weight', true);
        $diamond_id = get_post_meta($product_id, '_jpc_diamond_id', true);
        $diamond_quantity = get_post_meta($product_id, '_jpc_diamond_quantity', true);
        $price_breakup = get_post_meta($product_id, '_jpc_price_breakup', true);
        
        // Get metal details
        $metal = null;
        $metal_group = null;
        if ($metal_id) {
            $metal = JPC_Metals::get_by_id($metal_id);
            if ($metal) {
                $metal_group = JPC_Metal_Groups::get_by_id($metal->metal_group_id);
            }
        }
        
        // Get diamond details
        $diamond = null;
        if ($diamond_id) {
            $diamond = JPC_Diamonds::get_by_id($diamond_id);
        }
        
        // Get product tags
        $tags = wp_get_post_terms($product_id, 'product_tag');
        
        ob_start();
        include JPC_PLUGIN_DIR . 'templates/shortcodes/product-details-accordion.php';
        return ob_get_clean();
    }
    
    /**
     * Metal rates shortcode - default display
     */
    public function metal_rates_shortcode($atts) {
        $atts = shortcode_atts(array(
            'template' => 'list',
            'metals' => '', // comma-separated metal IDs
        ), $atts);
        
        $metals = JPC_Metals::get_all();
        
        if (!empty($atts['metals'])) {
            $metal_ids = array_map('intval', explode(',', $atts['metals']));
            $metals = array_filter($metals, function($metal) use ($metal_ids) {
                return in_array($metal->id, $metal_ids);
            });
        }
        
        if (empty($metals)) {
            return '';
        }
        
        ob_start();
        
        if ($atts['template'] === 'marquee') {
            include JPC_PLUGIN_DIR . 'templates/shortcodes/metal-rates-marquee.php';
        } elseif ($atts['template'] === 'table') {
            include JPC_PLUGIN_DIR . 'templates/shortcodes/metal-rates-table.php';
        } else {
            include JPC_PLUGIN_DIR . 'templates/shortcodes/metal-rates-list.php';
        }
        
        return ob_get_clean();
    }
    
    /**
     * Metal rates marquee shortcode
     */
    public function metal_rates_marquee_shortcode($atts) {
        $atts['template'] = 'marquee';
        return $this->metal_rates_shortcode($atts);
    }
    
    /**
     * Metal rates table shortcode
     */
    public function metal_rates_table_shortcode($atts) {
        $atts['template'] = 'table';
        return $this->metal_rates_shortcode($atts);
    }
}
