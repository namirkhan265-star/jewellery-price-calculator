<?php
/**
 * Frontend Display Handler v2.3.0
 * Adds Diamond Details and Additional Components to theme's existing PRODUCT DETAILS section
 * NO CUSTOM TABS - Only enhances existing product attributes
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
        
        // Add to existing product attributes
        add_filter('woocommerce_display_product_attributes', array($this, 'add_jpc_to_product_attributes'), 10, 2);
    }
    
    /**
     * Add JPC details to existing product attributes display
     */
    public function add_jpc_to_product_attributes($product_attributes, $product) {
        if (!$product) {
            return $product_attributes;
        }
        
        $product_id = $product->get_id();
        $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
        
        // Only add if this is a JPC product
        if (!$metal_id) {
            return $product_attributes;
        }
        
        // Get diamond data
        $diamond_id = get_post_meta($product_id, '_jpc_diamond_id', true);
        $manual_diamond_weight = get_post_meta($product_id, '_jpc_manual_diamond_weight', true);
        $manual_diamond_rate = get_post_meta($product_id, '_jpc_manual_diamond_rate', true);
        $num_diamonds = get_post_meta($product_id, '_jpc_num_diamonds', true);
        
        // Get other details
        $pearl_cost = get_post_meta($product_id, '_jpc_pearl_cost', true);
        $stone_cost = get_post_meta($product_id, '_jpc_stone_cost', true);
        
        // Add Diamond Details Section (if applicable)
        if ($diamond_id || $manual_diamond_weight) {
            // Add section header
            $product_attributes['jpc_diamond_section'] = array(
                'label' => 'DIAMOND DETAILS',
                'value' => '',
            );
            
            if ($diamond_id) {
                // Pre-created diamond
                $diamond = JPC_Diamonds::get_by_id($diamond_id);
                if ($diamond) {
                    $product_attributes['jpc_diamond_type'] = array(
                        'label' => 'Diamond Type',
                        'value' => esc_html($diamond->display_name),
                    );
                    
                    $shape = JPC_Diamond_Shapes::get_by_id($diamond->shape_id);
                    if ($shape) {
                        $product_attributes['jpc_diamond_shape'] = array(
                            'label' => 'Shape',
                            'value' => esc_html($shape->name),
                        );
                    }
                    
                    $clarity = JPC_Diamond_Clarities::get_by_id($diamond->clarity_id);
                    if ($clarity) {
                        $product_attributes['jpc_diamond_clarity'] = array(
                            'label' => 'Clarity',
                            'value' => esc_html($clarity->name),
                        );
                    }
                    
                    $colour = JPC_Diamond_Colours::get_by_id($diamond->colour_id);
                    if ($colour) {
                        $product_attributes['jpc_diamond_colour'] = array(
                            'label' => 'Colour',
                            'value' => esc_html($colour->name),
                        );
                    }
                    
                    $cut = JPC_Diamond_Cuts::get_by_id($diamond->cut_id);
                    if ($cut) {
                        $product_attributes['jpc_diamond_cut'] = array(
                            'label' => 'Cut',
                            'value' => esc_html($cut->name),
                        );
                    }
                    
                    $product_attributes['jpc_diamond_carat'] = array(
                        'label' => 'Diamond Carat',
                        'value' => esc_html($diamond->weight) . ' Carat',
                    );
                }
            } elseif ($manual_diamond_weight) {
                // Manual diamond entry
                $product_attributes['jpc_diamond_type'] = array(
                    'label' => 'Diamond Type',
                    'value' => 'Custom Diamond',
                );
                
                $product_attributes['jpc_diamond_carat'] = array(
                    'label' => 'Diamond Carat',
                    'value' => esc_html($manual_diamond_weight) . ' Carat',
                );
                
                if ($manual_diamond_rate) {
                    $product_attributes['jpc_diamond_rate'] = array(
                        'label' => 'Rate per Carat',
                        'value' => wc_price($manual_diamond_rate),
                    );
                }
            }
            
            if ($num_diamonds) {
                $product_attributes['jpc_num_diamonds'] = array(
                    'label' => 'Number of Diamonds',
                    'value' => esc_html($num_diamonds),
                );
            }
        }
        
        // Add Additional Components Section (if applicable)
        if ($pearl_cost || $stone_cost) {
            $product_attributes['jpc_additional_section'] = array(
                'label' => 'ADDITIONAL COMPONENTS',
                'value' => '',
            );
            
            if ($pearl_cost) {
                $product_attributes['jpc_pearl_cost'] = array(
                    'label' => 'Pearl Cost',
                    'value' => wc_price($pearl_cost),
                );
            }
            
            if ($stone_cost) {
                $product_attributes['jpc_stone_cost'] = array(
                    'label' => 'Stone Cost',
                    'value' => wc_price($stone_cost),
                );
            }
        }
        
        return $product_attributes;
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        if (is_product()) {
            wp_enqueue_style('jpc-frontend-css', JPC_PLUGIN_URL . 'assets/css/frontend.css', array(), JPC_VERSION);
            
            // Add custom CSS for section headers
            $custom_css = "
                /* Style for JPC section headers */
                .woocommerce-product-attributes-item__label:contains('DIAMOND DETAILS'),
                .woocommerce-product-attributes-item__label:contains('ADDITIONAL COMPONENTS') {
                    background: #f0f0f0 !important;
                    font-weight: bold !important;
                    font-size: 1.1em !important;
                    padding: 12px !important;
                    text-transform: uppercase !important;
                }
                
                /* Hide empty values for section headers */
                tr:has(.woocommerce-product-attributes-item__label:contains('DIAMOND DETAILS')) .woocommerce-product-attributes-item__value,
                tr:has(.woocommerce-product-attributes-item__label:contains('ADDITIONAL COMPONENTS')) .woocommerce-product-attributes-item__value {
                    display: none !important;
                }
            ";
            wp_add_inline_style('jpc-frontend-css', $custom_css);
        }
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
