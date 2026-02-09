<?php
/**
 * Frontend Display Handler v2.0.0
 * CRITICAL: This class ONLY displays stored data - NO CALCULATIONS!
 * NEW: Added DIAMOND DETAILS and METAL DETAILS tabs
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
        
        // FORCE OVERRIDE: Add custom product tabs with HIGHEST priority
        add_filter('woocommerce_product_tabs', array($this, 'add_product_details_tabs'), 999);
        
        // FORCE OVERRIDE: Remove any other price breakup tabs
        add_filter('woocommerce_product_tabs', array($this, 'remove_theme_price_breakup_tabs'), 1);
    }
    
    /**
     * Remove theme's price breakup tabs
     */
    public function remove_theme_price_breakup_tabs($tabs) {
        // Remove common theme tab keys
        $remove_keys = array(
            'price_breakup',
            'price-breakup',
            'pricebreakup',
            'breakup',
            'price_breakdown',
            'price-breakdown'
        );
        
        foreach ($remove_keys as $key) {
            if (isset($tabs[$key])) {
                unset($tabs[$key]);
            }
        }
        
        return $tabs;
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        if (is_product()) {
            wp_enqueue_style('jpc-frontend-css', JPC_PLUGIN_URL . 'assets/css/frontend.css', array(), JPC_VERSION);
            wp_enqueue_script('jpc-frontend-js', JPC_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), JPC_VERSION, true);
            
            // Add inline CSS to hide theme's price breakup if it still shows
            wp_add_inline_style('jpc-frontend-css', '
                .theme-price-breakup,
                .tikona-price-breakup,
                .price-breakup-theme {
                    display: none !important;
                }
            ');
        }
    }
    
    /**
     * Add Product Details tabs (Metal Details, Diamond Details, Price Breakup)
     */
    public function add_product_details_tabs($tabs) {
        global $product;
        
        if (!$product) {
            return $tabs;
        }
        
        $product_id = $product->get_id();
        $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
        
        // Only add tabs if this is a JPC product
        if (!$metal_id) {
            return $tabs;
        }
        
        // FORCE: Remove ALL existing price breakup tabs first
        foreach ($tabs as $key => $tab) {
            if (stripos($key, 'price') !== false || stripos($key, 'breakup') !== false) {
                unset($tabs[$key]);
            }
        }
        
        // Add METAL DETAILS tab
        $tabs['jpc_metal_details'] = array(
            'title'    => __('Metal Details', 'jewellery-price-calc'),
            'priority' => 5,
            'callback' => array($this, 'render_metal_details_tab')
        );
        
        // Add DIAMOND DETAILS tab (only if product has diamond)
        $diamond_id = get_post_meta($product_id, '_jpc_diamond_id', true);
        $manual_diamond_weight = get_post_meta($product_id, '_jpc_manual_diamond_weight', true);
        
        if ($diamond_id || $manual_diamond_weight) {
            $tabs['jpc_diamond_details'] = array(
                'title'    => __('Diamond Details', 'jewellery-price-calc'),
                'priority' => 10,
                'callback' => array($this, 'render_diamond_details_tab')
            );
        }
        
        // Add PRICE BREAKUP tab
        $tabs['jpc_price_breakup'] = array(
            'title'    => __('Price Breakup', 'jewellery-price-calc'),
            'priority' => 15,
            'callback' => array($this, 'render_price_breakup_tab_content')
        );
        
        return $tabs;
    }
    
    /**
     * Render METAL DETAILS tab
     */
    public function render_metal_details_tab() {
        global $product;
        
        if (!$product) {
            return;
        }
        
        $product_id = $product->get_id();
        $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
        $metal = JPC_Metals::get_by_id($metal_id);
        
        if (!$metal) {
            echo '<p>' . __('Metal information not available.', 'jewellery-price-calc') . '</p>';
            return;
        }
        
        $metal_weight = get_post_meta($product_id, '_jpc_metal_weight', true);
        $wastage_percentage = get_post_meta($product_id, '_jpc_wastage_percentage', true);
        
        ?>
        <div class="jpc-metal-details-tab" style="padding: 20px;">
            <h3><?php _e('METAL DETAILS', 'jewellery-price-calc'); ?></h3>
            
            <table class="jpc-details-table" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <tbody>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 12px; font-weight: bold; width: 40%;"><?php _e('Metal Type', 'jewellery-price-calc'); ?></td>
                        <td style="padding: 12px;"><?php echo esc_html($metal->display_name); ?></td>
                    </tr>
                    
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 12px; font-weight: bold;"><?php _e('Metal Group', 'jewellery-price-calc'); ?></td>
                        <td style="padding: 12px;"><?php echo esc_html($metal->group_name); ?></td>
                    </tr>
                    
                    <?php if ($metal_weight): ?>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 12px; font-weight: bold;"><?php _e('Metal Weight', 'jewellery-price-calc'); ?></td>
                        <td style="padding: 12px;"><?php echo esc_html($metal_weight); ?> <?php echo esc_html($metal->unit); ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 12px; font-weight: bold;"><?php _e('Price per Unit', 'jewellery-price-calc'); ?></td>
                        <td style="padding: 12px;"><?php echo wc_price($metal->price_per_unit); ?> / <?php echo esc_html($metal->unit); ?></td>
                    </tr>
                    
                    <?php if ($metal->making_charges_per_gram > 0): ?>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 12px; font-weight: bold;"><?php _e('Making Charges', 'jewellery-price-calc'); ?></td>
                        <td style="padding: 12px;"><?php echo wc_price($metal->making_charges_per_gram); ?> / gram</td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if ($wastage_percentage): ?>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 12px; font-weight: bold;"><?php _e('Wastage Percentage', 'jewellery-price-calc'); ?></td>
                        <td style="padding: 12px;"><?php echo esc_html($wastage_percentage); ?>%</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
    
    /**
     * Render DIAMOND DETAILS tab
     */
    public function render_diamond_details_tab() {
        global $product;
        
        if (!$product) {
            return;
        }
        
        $product_id = $product->get_id();
        
        // Check if using pre-created diamond or manual entry
        $diamond_id = get_post_meta($product_id, '_jpc_diamond_id', true);
        $manual_diamond_weight = get_post_meta($product_id, '_jpc_manual_diamond_weight', true);
        $manual_diamond_rate = get_post_meta($product_id, '_jpc_manual_diamond_rate', true);
        
        ?>
        <div class="jpc-diamond-details-tab" style="padding: 20px;">
            <h3><?php _e('DIAMOND DETAILS', 'jewellery-price-calc'); ?></h3>
            
            <table class="jpc-details-table" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <tbody>
                    <?php if ($diamond_id): 
                        // Pre-created diamond
                        $diamond = JPC_Diamonds::get_by_id($diamond_id);
                        if ($diamond):
                            // Get related data
                            $shape = JPC_Diamond_Shapes::get_by_id($diamond->shape_id);
                            $clarity = JPC_Diamond_Clarities::get_by_id($diamond->clarity_id);
                            $colour = JPC_Diamond_Colours::get_by_id($diamond->colour_id);
                            $cut = JPC_Diamond_Cuts::get_by_id($diamond->cut_id);
                            $certification = JPC_Diamond_Certifications::get_by_id($diamond->certification_id);
                    ?>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 12px; font-weight: bold; width: 40%;"><?php _e('Diamond Name', 'jewellery-price-calc'); ?></td>
                            <td style="padding: 12px;"><?php echo esc_html($diamond->display_name); ?></td>
                        </tr>
                        
                        <?php if ($shape): ?>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 12px; font-weight: bold;"><?php _e('Shape', 'jewellery-price-calc'); ?></td>
                            <td style="padding: 12px;"><?php echo esc_html($shape->name); ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if ($clarity): ?>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 12px; font-weight: bold;"><?php _e('Clarity', 'jewellery-price-calc'); ?></td>
                            <td style="padding: 12px;"><?php echo esc_html($clarity->name); ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if ($colour): ?>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 12px; font-weight: bold;"><?php _e('Colour', 'jewellery-price-calc'); ?></td>
                            <td style="padding: 12px;"><?php echo esc_html($colour->name); ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <?php if ($cut): ?>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 12px; font-weight: bold;"><?php _e('Cut', 'jewellery-price-calc'); ?></td>
                            <td style="padding: 12px;"><?php echo esc_html($cut->name); ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 12px; font-weight: bold;"><?php _e('Weight', 'jewellery-price-calc'); ?></td>
                            <td style="padding: 12px;"><?php echo esc_html($diamond->weight); ?> Carat</td>
                        </tr>
                        
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 12px; font-weight: bold;"><?php _e('Price per Carat', 'jewellery-price-calc'); ?></td>
                            <td style="padding: 12px;"><?php echo wc_price($diamond->price_per_carat); ?></td>
                        </tr>
                        
                        <?php if ($certification): ?>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 12px; font-weight: bold;"><?php _e('Certification', 'jewellery-price-calc'); ?></td>
                            <td style="padding: 12px;"><?php echo esc_html($certification->name); ?></td>
                        </tr>
                        <?php endif; ?>
                        
                    <?php 
                        endif;
                    elseif ($manual_diamond_weight): 
                        // Manual diamond entry
                    ?>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 12px; font-weight: bold; width: 40%;"><?php _e('Diamond Type', 'jewellery-price-calc'); ?></td>
                            <td style="padding: 12px;"><?php _e('Manual Entry', 'jewellery-price-calc'); ?></td>
                        </tr>
                        
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 12px; font-weight: bold;"><?php _e('Weight', 'jewellery-price-calc'); ?></td>
                            <td style="padding: 12px;"><?php echo esc_html($manual_diamond_weight); ?> Carat</td>
                        </tr>
                        
                        <?php if ($manual_diamond_rate): ?>
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td style="padding: 12px; font-weight: bold;"><?php _e('Rate per Carat', 'jewellery-price-calc'); ?></td>
                            <td style="padding: 12px;"><?php echo wc_price($manual_diamond_rate); ?></td>
                        </tr>
                        <?php endif; ?>
                        
                        <tr style="border-bottom: 1px solid #ddd; background: #fff3cd;">
                            <td colspan="2" style="padding: 12px;">
                                <em><?php _e('Note: This product uses manually entered diamond specifications. For detailed diamond information (shape, clarity, color, cut), please contact us.', 'jewellery-price-calc'); ?></em>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
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
