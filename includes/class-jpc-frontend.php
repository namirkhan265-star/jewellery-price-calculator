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
        
        // Add custom product tab for price breakup
        add_filter('woocommerce_product_tabs', array($this, 'add_price_breakup_tab'), 98);
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
     * Add Price Breakup tab to WooCommerce product tabs
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
        
        // Add or override the price breakup tab
        $tabs['jpc_price_breakup'] = array(
            'title'    => __('Price Breakup', 'jewellery-price-calc'),
            'priority' => 25,
            'callback' => array($this, 'render_price_breakup_tab_content')
        );
        
        return $tabs;
    }
    
    /**
     * Render Price Breakup tab content - FETCH FROM BACKEND ONLY
     */
    public function render_price_breakup_tab_content() {
        global $product;
        
        if (!$product) {
            return;
        }
        
        $product_id = $product->get_id();
        
        // FETCH ALL DATA FROM BACKEND (DATABASE) - NO RECALCULATION
        $regular_price = floatval(get_post_meta($product_id, '_regular_price', true));
        $sale_price = floatval(get_post_meta($product_id, '_sale_price', true));
        $discount_percentage = floatval(get_post_meta($product_id, '_jpc_discount_percentage', true));
        $breakup = get_post_meta($product_id, '_jpc_price_breakup', true);
        $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
        
        // Get metal info
        $metal = JPC_Metals::get_by_id($metal_id);
        
        if (!$metal || !$breakup || !is_array($breakup)) {
            echo '<p>' . __('Price breakup not available for this product.', 'jewellery-price-calc') . '</p>';
            return;
        }
        
        // Calculate discount amount
        $discount_amount = $regular_price - $sale_price;
        
        // If no sale price, use regular price
        if (empty($sale_price) || $sale_price <= 0) {
            $sale_price = $regular_price;
            $discount_amount = 0;
            $discount_percentage = 0;
        }
        
        ?>
        <div class="jpc-price-breakup-tab">
            <h3><?php _e('PRICE BREAKUP', 'jewellery-price-calc'); ?></h3>
            
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
                    
                    <!-- Discount Row -->
                    <?php if ($discount_percentage > 0 && $discount_amount > 0): ?>
                    <tr style="border-bottom: 1px solid #ddd; color: #d63638;">
                        <td style="padding: 12px;">
                            <?php _e('Discount', 'jewellery-price-calc'); ?>
                            <span style="color: #d63638; font-weight: bold;">
                                (<?php echo number_format($discount_percentage, 0); ?>% OFF)
                            </span>
                        </td>
                        <td style="padding: 12px; text-align: right; color: #d63638; font-weight: bold;">
                            - <?php echo wc_price($discount_amount); ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    
                    <!-- GST -->
                    <?php if (!empty($breakup['gst']) && $breakup['gst'] > 0): ?>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 12px;"><?php echo get_option('jpc_gst_label', 'GST'); ?></td>
                        <td style="padding: 12px; text-align: right;"><?php echo wc_price($breakup['gst']); ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <!-- Separator -->
                    <tr style="border-top: 3px solid #000;">
                        <td colspan="2" style="padding: 10px;">&nbsp;</td>
                    </tr>
                    
                    <!-- REGULAR PRICE - FROM DATABASE -->
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 12px;"><strong><?php _e('Regular Price', 'jewellery-price-calc'); ?></strong></td>
                        <td style="padding: 12px; text-align: right;">
                            <strong style="<?php echo ($discount_percentage > 0) ? 'text-decoration: line-through; color: #999;' : ''; ?>">
                                <?php echo wc_price($regular_price); ?>
                            </strong>
                        </td>
                    </tr>
                    
                    <!-- SALE PRICE - FROM DATABASE (only if discount exists) -->
                    <?php if ($discount_percentage > 0 && $discount_amount > 0): ?>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 12px;"><strong style="color: #d63638; font-size: 1.1em;"><?php _e('Sale Price', 'jewellery-price-calc'); ?></strong></td>
                        <td style="padding: 12px; text-align: right;"><strong style="color: #d63638; font-size: 1.2em;"><?php echo wc_price($sale_price); ?></strong></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <!-- Savings Badge -->
            <?php if ($discount_percentage > 0 && $discount_amount > 0): ?>
            <div class="jpc-savings-badge" style="margin-top: 15px; padding: 15px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; text-align: center;">
                <strong style="color: #155724; font-size: 1.1em;">
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
