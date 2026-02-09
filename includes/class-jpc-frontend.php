<?php
/**
 * Frontend Display Handler v2.2.0
 * CRITICAL: This class ONLY displays stored data - NO CALCULATIONS!
 * NEW: Adds JPC details to theme's existing PRODUCT DETAILS section
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
        
        // Add JPC details to theme's product details section
        add_filter('woocommerce_display_product_attributes', array($this, 'add_jpc_to_product_attributes'), 10, 2);
        add_filter('woocommerce_product_additional_information_heading', array($this, 'change_additional_info_heading'));
        
        // Add custom product attributes
        add_filter('woocommerce_product_get_attributes', array($this, 'add_jpc_attributes'), 10, 1);
        
        // ONLY add Price Breakup tab (remove other custom tabs)
        add_filter('woocommerce_product_tabs', array($this, 'add_price_breakup_tab'), 999);
    }
    
    /**
     * Change "Additional Information" heading to "Product Details"
     */
    public function change_additional_info_heading($heading) {
        return __('PRODUCT DETAILS', 'jewellery-price-calc');
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
        $jpc_attributes['jpc_metal_section'] = array(
            'name' => 'METAL DETAILS',
            'value' => '',
            'position' => 1,
            'is_visible' => 1,
            'is_variation' => 0,
            'is_taxonomy' => 0,
        );
        
        $jpc_attributes['jpc_metal_type'] = array(
            'name' => 'Type',
            'value' => $metal->display_name,
            'position' => 2,
            'is_visible' => 1,
            'is_variation' => 0,
            'is_taxonomy' => 0,
        );
        
        $jpc_attributes['jpc_karat'] = array(
            'name' => 'Karat',
            'value' => $metal->group_name,
            'position' => 3,
            'is_visible' => 1,
            'is_variation' => 0,
            'is_taxonomy' => 0,
        );
        
        $jpc_attributes['jpc_rate_per_gram'] = array(
            'name' => 'Rate Per Gram',
            'value' => wc_price($metal->price_per_unit),
            'position' => 4,
            'is_visible' => 1,
            'is_variation' => 0,
            'is_taxonomy' => 0,
        );
        
        if ($metal_weight) {
            $jpc_attributes['jpc_weight'] = array(
                'name' => 'Weight',
                'value' => $metal_weight . ' gram',
                'position' => 5,
                'is_visible' => 1,
                'is_variation' => 0,
                'is_taxonomy' => 0,
            );
        }
        
        if ($wastage_percentage) {
            $jpc_attributes['jpc_wastage'] = array(
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
            $jpc_attributes['jpc_diamond_section'] = array(
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
                    
                    $jpc_attributes['jpc_diamond_type'] = array(
                        'name' => 'Diamond Type',
                        'value' => $diamond->display_name,
                        'position' => 11,
                        'is_visible' => 1,
                        'is_variation' => 0,
                        'is_taxonomy' => 0,
                    );
                    
                    if ($shape) {
                        $jpc_attributes['jpc_diamond_shape'] = array(
                            'name' => 'Shape',
                            'value' => $shape->name,
                            'position' => 12,
                            'is_visible' => 1,
                            'is_variation' => 0,
                            'is_taxonomy' => 0,
                        );
                    }
                    
                    if ($clarity) {
                        $jpc_attributes['jpc_diamond_clarity'] = array(
                            'name' => 'Clarity',
                            'value' => $clarity->name,
                            'position' => 13,
                            'is_visible' => 1,
                            'is_variation' => 0,
                            'is_taxonomy' => 0,
                        );
                    }
                    
                    if ($colour) {
                        $jpc_attributes['jpc_diamond_colour'] = array(
                            'name' => 'Colour',
                            'value' => $colour->name,
                            'position' => 14,
                            'is_visible' => 1,
                            'is_variation' => 0,
                            'is_taxonomy' => 0,
                        );
                    }
                    
                    if ($cut) {
                        $jpc_attributes['jpc_diamond_cut'] = array(
                            'name' => 'Cut',
                            'value' => $cut->name,
                            'position' => 15,
                            'is_visible' => 1,
                            'is_variation' => 0,
                            'is_taxonomy' => 0,
                        );
                    }
                    
                    $jpc_attributes['jpc_diamond_carat'] = array(
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
                $jpc_attributes['jpc_diamond_type'] = array(
                    'name' => 'Diamond Type',
                    'value' => 'Custom Diamond',
                    'position' => 11,
                    'is_visible' => 1,
                    'is_variation' => 0,
                    'is_taxonomy' => 0,
                );
                
                $jpc_attributes['jpc_diamond_carat'] = array(
                    'name' => 'Diamond Carat',
                    'value' => $manual_diamond_weight . ' Carat',
                    'position' => 12,
                    'is_visible' => 1,
                    'is_variation' => 0,
                    'is_taxonomy' => 0,
                );
                
                if ($manual_diamond_rate) {
                    $jpc_attributes['jpc_diamond_rate'] = array(
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
                $jpc_attributes['jpc_num_diamonds'] = array(
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
            $jpc_attributes['jpc_additional_section'] = array(
                'name' => 'ADDITIONAL COMPONENTS',
                'value' => '',
                'position' => 20,
                'is_visible' => 1,
                'is_variation' => 0,
                'is_taxonomy' => 0,
            );
            
            if ($pearl_cost) {
                $jpc_attributes['jpc_pearl_cost'] = array(
                    'name' => 'Pearl Cost',
                    'value' => wc_price($pearl_cost),
                    'position' => 21,
                    'is_visible' => 1,
                    'is_variation' => 0,
                    'is_taxonomy' => 0,
                );
            }
            
            if ($stone_cost) {
                $jpc_attributes['jpc_stone_cost'] = array(
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
     * Add JPC details to product attributes display
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
        
        // The attributes are already added via add_jpc_attributes filter
        // This filter just ensures they display correctly
        return $product_attributes;
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        if (is_product()) {
            wp_enqueue_style('jpc-frontend-css', JPC_PLUGIN_URL . 'assets/css/frontend.css', array(), JPC_VERSION);
            wp_enqueue_script('jpc-frontend-js', JPC_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), JPC_VERSION, true);
            
            // Add inline CSS for section headers
            wp_add_inline_style('jpc-frontend-css', '
                .woocommerce-product-attributes-item__label[data-section-header="true"] {
                    background: #f0f0f0 !important;
                    font-weight: bold !important;
                    font-size: 1.1em !important;
                    padding: 12px !important;
                    text-transform: uppercase !important;
                }
                .woocommerce-product-attributes-item__label[data-section-header="true"] + .woocommerce-product-attributes-item__value {
                    display: none !important;
                }
            ');
        }
    }
    
    /**
     * Add ONLY Price Breakup tab
     */
    public function add_price_breakup_tab($tabs) {
        global $product;
        
        if (!$product) {
            return $tabs;
        }
        
        $product_id = $product->get_id();
        $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
        
        // Only add tab if this is a JPC product
        if (!$metal_id) {
            return $tabs;
        }
        
        // Add PRICE BREAKUP tab
        $tabs['jpc_price_breakup'] = array(
            'title'    => __('Price Breakup', 'jewellery-price-calc'),
            'priority' => 25,
            'callback' => array($this, 'render_price_breakup_tab_content')
        );
        
        return $tabs;
    }
    
    /**
     * Render Price Breakup tab content - USES ONLY STORED DATA
     */
    public function render_price_breakup_tab_content() {
        global $product;
        
        if (!$product) {
            return;
        }
        
        $product_id = $product->get_id();
        
        // FETCH STORED DATA - NO CALCULATIONS!
        $breakup = get_post_meta($product_id, '_jpc_price_breakup', true);
        $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
        $discount_percentage = floatval(get_post_meta($product_id, '_jpc_discount_percentage', true));
        
        // Get metal info
        $metal = JPC_Metals::get_by_id($metal_id);
        
        if (!$metal) {
            echo '<p>' . __('Invalid metal configuration.', 'jewellery-price-calc') . '</p>';
            return;
        }
        
        // Validate breakup data
        if (!$breakup || !is_array($breakup)) {
            echo '<div style="padding: 20px; background: #fff3cd; border: 2px solid #ffc107; border-radius: 5px;">';
            echo '<p style="color: #856404; font-weight: bold;">⚠️ Price breakup data not found!</p>';
            echo '<p>Please go to the product editor and click \"Regenerate Price Breakup\" button.</p>';
            echo '</div>';
            return;
        }
        
        // CRITICAL: Use stored discount from breakup, NOT calculated
        $discount_amount = isset($breakup['discount']) ? floatval($breakup['discount']) : 0;
        
        // Get prices from WooCommerce (these are already calculated and stored)
        $regular_price = floatval(get_post_meta($product_id, '_regular_price', true));
        $sale_price = floatval(get_post_meta($product_id, '_sale_price', true));
        
        // Fallback if no sale price
        if (empty($sale_price) || $sale_price <= 0) {
            $sale_price = $regular_price;
        }
        
        ?>
        <div class="jpc-price-breakup-tab" style="padding: 20px; background: #fff;">
            
            <h3 style="margin-bottom: 20px; font-size: 1.5em;"><?php _e('PRICE BREAKUP', 'jewellery-price-calc'); ?></h3>
            
            <table class="jpc-price-breakup-table" style="width: 100%; border-collapse: collapse;">
                <tbody>
                    <!-- Metal Price -->
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 12px;"><?php echo esc_html($metal->display_name); ?></td>
                        <td style="padding: 12px; text-align: right;"><?php echo wc_price($breakup['metal_price']); ?></td>
                    </tr>
                    
                    <!-- Diamond Price -->
                    <?php if (!empty($breakup['diamond_price']) && $breakup['diamond_price'] > 0): ?>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 12px;"><?php _e('Diamond', 'jewellery-price-calc'); ?></td>
                        <td style="padding: 12px; text-align: right;"><?php echo wc_price($breakup['diamond_price']); ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <!-- Making Charges -->
                    <?php if (!empty($breakup['making_charge']) && $breakup['making_charge'] > 0): ?>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 12px;"><?php _e('Making Charges', 'jewellery-price-calc'); ?></td>
                        <td style="padding: 12px; text-align: right;"><?php echo wc_price($breakup['making_charge']); ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <!-- Wastage Charge -->
                    <?php if (!empty($breakup['wastage_charge']) && $breakup['wastage_charge'] > 0): ?>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 12px;"><?php _e('Wastage Charge', 'jewellery-price-calc'); ?></td>
                        <td style="padding: 12px; text-align: right;"><?php echo wc_price($breakup['wastage_charge']); ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <!-- Pearl Cost -->
                    <?php if (!empty($breakup['pearl_cost']) && $breakup['pearl_cost'] > 0): ?>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 12px;"><?php _e('Pearl Cost', 'jewellery-price-calc'); ?></td>
                        <td style="padding: 12px; text-align: right;"><?php echo wc_price($breakup['pearl_cost']); ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <!-- Stone Cost -->
                    <?php if (!empty($breakup['stone_cost']) && $breakup['stone_cost'] > 0): ?>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 12px;"><?php _e('Stone Cost', 'jewellery-price-calc'); ?></td>
                        <td style="padding: 12px; text-align: right;"><?php echo wc_price($breakup['stone_cost']); ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <!-- Extra Fee -->
                    <?php if (!empty($breakup['extra_fee']) && $breakup['extra_fee'] > 0): ?>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 12px;"><?php _e('Extra Fee', 'jewellery-price-calc'); ?></td>
                        <td style="padding: 12px; text-align: right;"><?php echo wc_price($breakup['extra_fee']); ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <!-- Extra Fields #1-5 with custom labels -->
                    <?php
                    if (!empty($breakup['extra_fields']) && is_array($breakup['extra_fields'])) {
                        foreach ($breakup['extra_fields'] as $extra_field) {
                            if (!empty($extra_field['value']) && $extra_field['value'] > 0) {
                                ?>
                                <tr style="border-bottom: 1px solid #ddd;">
                                    <td style="padding: 12px;"><?php echo esc_html($extra_field['label']); ?></td>
                                    <td style="padding: 12px; text-align: right;"><?php echo wc_price($extra_field['value']); ?></td>
                                </tr>
                                <?php
                            }
                        }
                    }
                    ?>
                    
                    <!-- Additional Percentage -->
                    <?php if (!empty($breakup['additional_percentage']) && $breakup['additional_percentage'] > 0): ?>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 12px;"><?php echo esc_html($breakup['additional_percentage_label'] ?? 'Additional Percentage'); ?></td>
                        <td style="padding: 12px; text-align: right;"><?php echo wc_price($breakup['additional_percentage']); ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <!-- Discount Row - USES STORED DISCOUNT FROM BREAKUP -->
                    <?php if ($discount_percentage > 0 && $discount_amount > 0): ?>
                    <tr style="border-bottom: 1px solid #ddd; background: #d4edda;">
                        <td style="padding: 12px; color: #28a745; font-weight: bold;">
                            <?php printf(__('Discount (%s%% OFF)', 'jewellery-price-calc'), number_format($discount_percentage, 1)); ?>
                        </td>
                        <td style="padding: 12px; text-align: right; color: #28a745; font-weight: bold;">
                            -<?php echo wc_price($discount_amount); ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    
                    <!-- GST - USES STORED GST FROM BREAKUP -->
                    <?php 
                    $gst_value = isset($breakup['gst']) ? floatval($breakup['gst']) : 0;
                    $gst_label = isset($breakup['gst_label']) ? $breakup['gst_label'] : get_option('jpc_gst_label', 'GST');
                    $gst_percentage = isset($breakup['gst_percentage']) ? $breakup['gst_percentage'] : 0;
                    
                    if ($gst_value > 0): 
                    ?>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 12px;">
                            <?php echo esc_html($gst_label) . ' (' . number_format($gst_percentage, 2) . '%)'; ?>
                        </td>
                        <td style="padding: 12px; text-align: right;"><?php echo wc_price($gst_value); ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <!-- Separator -->
                    <tr style="border-top: 3px solid #000;">
                        <td colspan="2" style="padding: 10px;">&nbsp;</td>
                    </tr>
                    
                    <!-- PRICE BEFORE DISCOUNT -->
                    <tr style="border-bottom: 1px solid #ddd; background: #f9f9f9;">
                        <td style="padding: 15px;"><strong style="font-size: 1.1em;"><?php _e('Price Before Discount', 'jewellery-price-calc'); ?></strong></td>
                        <td style="padding: 15px; text-align: right;">
                            <strong style="font-size: 1.3em; <?php echo ($discount_percentage > 0) ? 'text-decoration: line-through; color: #999;' : 'color: #0066cc;'; ?>">
                                <?php echo wc_price($regular_price); ?>
                            </strong>
                        </td>
                    </tr>
                    
                    <!-- PRICE AFTER DISCOUNT (only if discount exists) -->
                    <?php if ($discount_percentage > 0 && $discount_amount > 0): ?>
                    <tr style="border-bottom: 1px solid #ddd; background: #f9f9f9;">
                        <td style="padding: 15px;"><strong style="color: #d63638; font-size: 1.2em;"><?php _e('Price After Discount', 'jewellery-price-calc'); ?></strong></td>
                        <td style="padding: 15px; text-align: right;"><strong style="color: #d63638; font-size: 1.5em;"><?php echo wc_price($sale_price); ?></strong></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <!-- Savings Badge - USES STORED DISCOUNT -->
            <?php if ($discount_percentage > 0 && $discount_amount > 0): ?>
            <div class="jpc-savings-badge" style="margin-top: 20px; padding: 20px; background: #d4edda; border: 2px solid #28a745; border-radius: 8px; text-align: center;">
                <strong style="color: #155724; font-size: 1.3em;">
                    🎉 You Save: <?php echo wc_price($discount_amount); ?> 
                    (<?php echo number_format($discount_percentage, 0); ?>% OFF)
                </strong>
            </div>
            <?php endif; ?>
        </div>
        <?php
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

// Initialize
JPC_Frontend::get_instance();
