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
        
        // FORCE OVERRIDE: Add custom product tab for price breakup with HIGHEST priority
        add_filter('woocommerce_product_tabs', array($this, 'add_price_breakup_tab'), 999);
        
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
     * Add Price Breakup tab to WooCommerce product tabs - FORCE OVERRIDE
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
        
        // FORCE: Remove ALL existing price breakup tabs first
        foreach ($tabs as $key => $tab) {
            if (stripos($key, 'price') !== false || stripos($key, 'breakup') !== false) {
                unset($tabs[$key]);
            }
        }
        
        // Add our tab with HIGHEST priority
        $tabs['jpc_price_breakup'] = array(
            'title'    => __('Price Breakup', 'jewellery-price-calc'),
            'priority' => 5, // Very high priority to show first
            'callback' => array($this, 'render_price_breakup_tab_content')
        );
        
        return $tabs;
    }
    
    /**
     * Render Price Breakup tab content - FETCH FROM DATABASE ONLY
     */
    public function render_price_breakup_tab_content() {
        global $product;
        
        if (!$product) {
            return;
        }
        
        $product_id = $product->get_id();
        
        // FETCH ALL DATA FROM DATABASE - NO RECALCULATION
        $regular_price = floatval(get_post_meta($product_id, '_regular_price', true));
        $sale_price = floatval(get_post_meta($product_id, '_sale_price', true));
        $discount_percentage = floatval(get_post_meta($product_id, '_jpc_discount_percentage', true));
        $breakup = get_post_meta($product_id, '_jpc_price_breakup', true);
        $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
        
        // DEBUG: Show what we're fetching
        echo '<!-- DEBUG: Regular Price from DB: ' . $regular_price . ' -->';
        echo '<!-- DEBUG: Sale Price from DB: ' . $sale_price . ' -->';
        echo '<!-- DEBUG: Discount %: ' . $discount_percentage . ' -->';
        
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
        <div class="jpc-price-breakup-tab" style="padding: 20px; background: #fff;">
            <h3 style="margin-bottom: 20px; font-size: 1.5em;"><?php _e('PRICE BREAKUP', 'jewellery-price-calc'); ?></h3>
            
            <!-- BIG DEBUG BOX -->
            <div style="background: #fff3cd; border: 3px solid #ffc107; padding: 20px; margin-bottom: 20px; border-radius: 8px;">
                <h4 style="color: #856404; margin: 0 0 10px 0;">🔍 DEBUG INFO (Database Values)</h4>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 8px;"><strong>Product ID:</strong></td>
                        <td style="padding: 8px;"><?php echo $product_id; ?></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 8px;"><strong>Regular Price (DB):</strong></td>
                        <td style="padding: 8px; font-size: 1.2em; color: #0066cc;"><strong>₹<?php echo number_format($regular_price, 2); ?></strong></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 8px;"><strong>Sale Price (DB):</strong></td>
                        <td style="padding: 8px; font-size: 1.2em; color: #d63638;"><strong>₹<?php echo number_format($sale_price, 2); ?></strong></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ddd;">
                        <td style="padding: 8px;"><strong>Discount %:</strong></td>
                        <td style="padding: 8px;"><strong><?php echo $discount_percentage; ?>%</strong></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px;"><strong>Discount Amount:</strong></td>
                        <td style="padding: 8px;"><strong>₹<?php echo number_format($discount_amount, 2); ?></strong></td>
                    </tr>
                </table>
            </div>
            
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
                    
                    <!-- Separator -->
                    <tr style="border-top: 3px solid #000;">
                        <td colspan="2" style="padding: 10px;">&nbsp;</td>
                    </tr>
                    
                    <!-- REGULAR PRICE - FROM DATABASE -->
                    <tr style="border-bottom: 1px solid #ddd; background: #f9f9f9;">
                        <td style="padding: 15px;"><strong style="font-size: 1.1em;"><?php _e('Regular Price', 'jewellery-price-calc'); ?></strong></td>
                        <td style="padding: 15px; text-align: right;">
                            <strong style="font-size: 1.3em; <?php echo ($discount_percentage > 0) ? 'text-decoration: line-through; color: #999;' : 'color: #0066cc;'; ?>">
                                ₹<?php echo number_format($regular_price, 2); ?>
                            </strong>
                        </td>
                    </tr>
                    
                    <!-- SALE PRICE - FROM DATABASE (only if discount exists) -->
                    <?php if ($discount_percentage > 0 && $discount_amount > 0): ?>
                    <tr style="border-bottom: 1px solid #ddd; background: #f9f9f9;">
                        <td style="padding: 15px;"><strong style="color: #d63638; font-size: 1.2em;"><?php _e('Sale Price', 'jewellery-price-calc'); ?></strong></td>
                        <td style="padding: 15px; text-align: right;"><strong style="color: #d63638; font-size: 1.5em;">₹<?php echo number_format($sale_price, 2); ?></strong></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <!-- Savings Badge -->
            <?php if ($discount_percentage > 0 && $discount_amount > 0): ?>
            <div class="jpc-savings-badge" style="margin-top: 20px; padding: 20px; background: #d4edda; border: 2px solid #28a745; border-radius: 8px; text-align: center;">
                <strong style="color: #155724; font-size: 1.3em;">
                    🎉 You Save: ₹<?php echo number_format($discount_amount, 2); ?> 
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
