<?php
/**
 * Frontend Display Handler v2.2.1
 * CRITICAL: This class ONLY displays stored data - NO CALCULATIONS!
 * Adds JPC details to theme's existing PRODUCT DETAILS section
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
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        
        // Add JPC details to theme's product details section
        add_filter('woocommerce_product_get_attributes', array($this, 'add_jpc_attributes'), 10, 1);
        add_filter('woocommerce_display_product_attributes', array($this, 'modify_attribute_display'), 10, 2);
    }
    
    /**
     * Add JPC attributes to product
     */
    public function add_jpc_attributes($attributes) {
        global $product;
        
        if (!$product) {
            return $attributes;
        }
        
        $product_id = $product->get_id();
        $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
        
        // Only add if this is a JPC product
        if (!$metal_id) {
            return $attributes;
        }
        
        $metal = JPC_Metals::get_by_id($metal_id);
        if (!$metal) {
            return $attributes;
        }
        
        $metal_weight = get_post_meta($product_id, '_jpc_metal_weight', true);
        $wastage_percentage = get_post_meta($product_id, '_jpc_wastage_percentage', true);
        
        // Diamond data
        $diamond_id = get_post_meta($product_id, '_jpc_diamond_id', true);
        $manual_diamond_weight = get_post_meta($product_id, '_jpc_manual_diamond_weight', true);
        $manual_diamond_rate = get_post_meta($product_id, '_jpc_manual_diamond_rate', true);
        $num_diamonds = get_post_meta($product_id, '_jpc_num_diamonds', true);
        
        // Other details
        $pearl_cost = get_post_meta($product_id, '_jpc_pearl_cost', true);
        $stone_cost = get_post_meta($product_id, '_jpc_stone_cost', true);
        
        // Create custom attributes array
        $jpc_attributes = array();
        
        // METAL DETAILS SECTION
        $jpc_attributes['jpc_metal_section'] = (object) array(
            'name' => 'METAL DETAILS',
            'value' => '',
            'position' => 1,
            'is_visible' => 1,
            'is_variation' => 0,
            'is_taxonomy' => 0,
        );
        
        $jpc_attributes['jpc_metal_type'] = (object) array(
            'name' => 'Type',
            'value' => $metal->display_name,
            'position' => 2,
            'is_visible' => 1,
            'is_variation' => 0,
            'is_taxonomy' => 0,
        );
        
        $jpc_attributes['jpc_karat'] = (object) array(
            'name' => 'Karat',
            'value' => $metal->group_name,
            'position' => 3,
            'is_visible' => 1,
            'is_variation' => 0,
            'is_taxonomy' => 0,
        );
        
        $jpc_attributes['jpc_rate_per_gram'] = (object) array(
            'name' => 'Rate Per Gram',
            'value' => wc_price($metal->price_per_unit),
            'position' => 4,
            'is_visible' => 1,
            'is_variation' => 0,
            'is_taxonomy' => 0,
        );
        
        if ($metal_weight) {
            $jpc_attributes['jpc_weight'] = (object) array(
                'name' => 'Weight',
                'value' => $metal_weight . ' gram',
                'position' => 5,
                'is_visible' => 1,
                'is_variation' => 0,
                'is_taxonomy' => 0,
            );
        }
        
        if ($wastage_percentage) {
            $jpc_attributes['jpc_wastage'] = (object) array(
                'name' => 'Wastage %',
                'value' => $wastage_percentage . '%',
                'position' => 6,
                'is_visible' => 1,
                'is_variation' => 0,
                'is_taxonomy' => 0,
            );
        }
        
        // DIAMOND DETAILS SECTION (if applicable)
        if ($diamond_id || $manual_diamond_weight) {
            $jpc_attributes['jpc_diamond_section'] = (object) array(
                'name' => 'DIAMOND DETAILS',
                'value' => '',
                'position' => 10,
                'is_visible' => 1,
                'is_variation' => 0,
                'is_taxonomy' => 0,
            );
            
            if ($diamond_id) {
                // Pre-created diamond
                $diamond = JPC_Diamonds::get_by_id($diamond_id);
                if ($diamond) {
                    $shape = JPC_Diamond_Shapes::get_by_id($diamond->shape_id);
                    $clarity = JPC_Diamond_Clarities::get_by_id($diamond->clarity_id);
                    $colour = JPC_Diamond_Colours::get_by_id($diamond->colour_id);
                    $cut = JPC_Diamond_Cuts::get_by_id($diamond->cut_id);
                    
                    $jpc_attributes['jpc_diamond_type'] = (object) array(
                        'name' => 'Diamond Type',
                        'value' => $diamond->display_name,
                        'position' => 11,
                        'is_visible' => 1,
                        'is_variation' => 0,
                        'is_taxonomy' => 0,
                    );
                    
                    if ($shape) {
                        $jpc_attributes['jpc_diamond_shape'] = (object) array(
                            'name' => 'Shape',
                            'value' => $shape->name,
                            'position' => 12,
                            'is_visible' => 1,
                            'is_variation' => 0,
                            'is_taxonomy' => 0,
                        );
                    }
                    
                    if ($clarity) {
                        $jpc_attributes['jpc_diamond_clarity'] = (object) array(
                            'name' => 'Clarity',
                            'value' => $clarity->name,
                            'position' => 13,
                            'is_visible' => 1,
                            'is_variation' => 0,
                            'is_taxonomy' => 0,
                        );
                    }
                    
                    if ($colour) {
                        $jpc_attributes['jpc_diamond_colour'] = (object) array(
                            'name' => 'Colour',
                            'value' => $colour->name,
                            'position' => 14,
                            'is_visible' => 1,
                            'is_variation' => 0,
                            'is_taxonomy' => 0,
                        );
                    }
                    
                    if ($cut) {
                        $jpc_attributes['jpc_diamond_cut'] = (object) array(
                            'name' => 'Cut',
                            'value' => $cut->name,
                            'position' => 15,
                            'is_visible' => 1,
                            'is_variation' => 0,
                            'is_taxonomy' => 0,
                        );
                    }
                    
                    $jpc_attributes['jpc_diamond_carat'] = (object) array(
                        'name' => 'Diamond Carat',
                        'value' => $diamond->weight . ' Carat',
                        'position' => 16,
                        'is_visible' => 1,
                        'is_variation' => 0,
                        'is_taxonomy' => 0,
                    );
                }
            } elseif ($manual_diamond_weight) {
                // Manual diamond entry
                $jpc_attributes['jpc_diamond_type'] = (object) array(
                    'name' => 'Diamond Type',
                    'value' => 'Custom Diamond',
                    'position' => 11,
                    'is_visible' => 1,
                    'is_variation' => 0,
                    'is_taxonomy' => 0,
                );
                
                $jpc_attributes['jpc_diamond_carat'] = (object) array(
                    'name' => 'Diamond Carat',
                    'value' => $manual_diamond_weight . ' Carat',
                    'position' => 12,
                    'is_visible' => 1,
                    'is_variation' => 0,
                    'is_taxonomy' => 0,
                );
                
                if ($manual_diamond_rate) {
                    $jpc_attributes['jpc_diamond_rate'] = (object) array(
                        'name' => 'Rate per Carat',
                        'value' => wc_price($manual_diamond_rate),
                        'position' => 13,
                        'is_visible' => 1,
                        'is_variation' => 0,
                        'is_taxonomy' => 0,
                    );
                }
            }
            
            if ($num_diamonds) {
                $jpc_attributes['jpc_num_diamonds'] = (object) array(
                    'name' => 'Number of Diamonds',
                    'value' => $num_diamonds,
                    'position' => 17,
                    'is_visible' => 1,
                    'is_variation' => 0,
                    'is_taxonomy' => 0,
                );
            }
        }
        
        // ADDITIONAL COMPONENTS SECTION (if applicable)
        if ($pearl_cost || $stone_cost) {
            $jpc_attributes['jpc_additional_section'] = (object) array(
                'name' => 'ADDITIONAL COMPONENTS',
                'value' => '',
                'position' => 20,
                'is_visible' => 1,
                'is_variation' => 0,
                'is_taxonomy' => 0,
            );
            
            if ($pearl_cost) {
                $jpc_attributes['jpc_pearl_cost'] = (object) array(
                    'name' => 'Pearl Cost',
                    'value' => wc_price($pearl_cost),
                    'position' => 21,
                    'is_visible' => 1,
                    'is_variation' => 0,
                    'is_taxonomy' => 0,
                );
            }
            
            if ($stone_cost) {
                $jpc_attributes['jpc_stone_cost'] = (object) array(
                    'name' => 'Stone Cost',
                    'value' => wc_price($stone_cost),
                    'position' => 22,
                    'is_visible' => 1,
                    'is_variation' => 0,
                    'is_taxonomy' => 0,
                );
            }
        }
        
        // Merge with existing attributes
        return array_merge($attributes, $jpc_attributes);
    }
    
    /**
     * Modify attribute display to style section headers
     */
    public function modify_attribute_display($product_attributes, $product) {
        if (!$product) {
            return $product_attributes;
        }
        
        // Add custom styling for section headers
        foreach ($product_attributes as $key => $attribute) {
            if (isset($attribute['label']) && 
                (strpos($attribute['label'], 'DETAILS') !== false || 
                 strpos($attribute['label'], 'COMPONENTS') !== false)) {
                // This is a section header
                $product_attributes[$key]['class'] = 'jpc-section-header';
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
            
            // Add inline CSS for section headers
            $custom_css = "
                .woocommerce-product-attributes-item.jpc-section-header .woocommerce-product-attributes-item__label {
                    background: #f0f0f0 !important;
                    font-weight: bold !important;
                    font-size: 1.1em !important;
                    padding: 12px !important;
                    text-transform: uppercase !important;
                }
                .woocommerce-product-attributes-item.jpc-section-header .woocommerce-product-attributes-item__value {
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

// Initialize
JPC_Frontend::get_instance();
