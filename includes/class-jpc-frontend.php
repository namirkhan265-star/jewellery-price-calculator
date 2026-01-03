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
     * Render Price Breakup tab content with COMPREHENSIVE DEBUG
     */
    public function render_price_breakup_tab_content() {
        global $product;
        
        if (!$product) {
            return;
        }
        
        $product_id = $product->get_id();
        
        // FETCH ALL DATA FROM DATABASE
        $regular_price = floatval(get_post_meta($product_id, '_regular_price', true));
        $sale_price = floatval(get_post_meta($product_id, '_sale_price', true));
        $discount_percentage = floatval(get_post_meta($product_id, '_jpc_discount_percentage', true));
        $breakup = get_post_meta($product_id, '_jpc_price_breakup', true);
        $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
        
        // Get all product meta for debugging
        $all_product_meta = array(
            '_jpc_metal_id' => get_post_meta($product_id, '_jpc_metal_id', true),
            '_jpc_metal_weight' => get_post_meta($product_id, '_jpc_metal_weight', true),
            '_jpc_diamond_id' => get_post_meta($product_id, '_jpc_diamond_id', true),
            '_jpc_diamond_quantity' => get_post_meta($product_id, '_jpc_diamond_quantity', true),
            '_jpc_making_charge' => get_post_meta($product_id, '_jpc_making_charge', true),
            '_jpc_making_charge_type' => get_post_meta($product_id, '_jpc_making_charge_type', true),
            '_jpc_wastage_charge' => get_post_meta($product_id, '_jpc_wastage_charge', true),
            '_jpc_wastage_charge_type' => get_post_meta($product_id, '_jpc_wastage_charge_type', true),
            '_jpc_pearl_cost' => get_post_meta($product_id, '_jpc_pearl_cost', true),
            '_jpc_stone_cost' => get_post_meta($product_id, '_jpc_stone_cost', true),
            '_jpc_extra_fee' => get_post_meta($product_id, '_jpc_extra_fee', true),
            '_jpc_extra_field_1' => get_post_meta($product_id, '_jpc_extra_field_1', true),
            '_jpc_extra_field_2' => get_post_meta($product_id, '_jpc_extra_field_2', true),
            '_jpc_extra_field_3' => get_post_meta($product_id, '_jpc_extra_field_3', true),
            '_jpc_extra_field_4' => get_post_meta($product_id, '_jpc_extra_field_4', true),
            '_jpc_extra_field_5' => get_post_meta($product_id, '_jpc_extra_field_5', true),
            '_jpc_discount_percentage' => get_post_meta($product_id, '_jpc_discount_percentage', true),
        );
        
        // Get GST settings
        $gst_settings = array(
            'enable_gst' => get_option('jpc_enable_gst'),
            'gst_value' => get_option('jpc_gst_value', 5),
            'gst_label' => get_option('jpc_gst_label', 'GST'),
            'additional_percentage_value' => get_option('jpc_additional_percentage_value', 0),
            'additional_percentage_label' => get_option('jpc_additional_percentage_label', 'Additional Percentage'),
        );
        
        // Get extra field settings
        $extra_field_settings = array();
        for ($i = 1; $i <= 5; $i++) {
            $extra_field_settings["extra_field_{$i}"] = array(
                'enabled' => get_option("jpc_enable_extra_field_{$i}"),
                'label' => get_option("jpc_extra_field_label_{$i}", "Extra Field #{$i}"),
            );
        }
        
        // Get discount settings
        $discount_settings = array(
            'discount_on_metals' => get_option('jpc_discount_on_metals'),
            'discount_on_making' => get_option('jpc_discount_on_making'),
            'discount_on_wastage' => get_option('jpc_discount_on_wastage'),
        );
        
        // Get metal info
        $metal = JPC_Metals::get_by_id($metal_id);
        
        if (!$metal) {
            echo '<p>' . __('Invalid metal configuration.', 'jewellery-price-calc') . '</p>';
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
            
            <!-- DEBUG BOX 1: STORED BREAKUP DATA -->
            <div style="background: #fff3cd; border: 2px solid #ffc107; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                <h4 style="margin: 0 0 10px 0; color: #856404;">🔍 DEBUG 1: Stored Breakup Array (_jpc_price_breakup)</h4>
                <pre style="background: #fff; padding: 10px; overflow: auto; font-size: 11px; max-height: 300px; margin: 0;"><?php print_r($breakup); ?></pre>
            </div>
            
            <!-- DEBUG BOX 2: WOOCOMMERCE PRICES & DISCOUNT -->
            <div style="background: #d1ecf1; border: 2px solid #17a2b8; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                <h4 style="margin: 0 0 10px 0; color: #0c5460;">💰 DEBUG 2: WooCommerce Price Fields</h4>
                <pre style="background: #fff; padding: 10px; overflow: auto; font-size: 11px; max-height: 200px; margin: 0;"><?php 
                echo "Regular Price (_regular_price): " . $regular_price . "\n";
                echo "Sale Price (_sale_price): " . $sale_price . "\n";
                echo "Discount %: " . $discount_percentage . "\n";
                echo "Discount Amount (Calculated): " . $discount_amount . "\n";
                ?></pre>
            </div>
            
            <!-- DEBUG BOX 3: ALL PRODUCT META -->
            <div style="background: #d4edda; border: 2px solid #28a745; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                <h4 style="margin: 0 0 10px 0; color: #155724;">📦 DEBUG 3: All Product Meta Fields</h4>
                <pre style="background: #fff; padding: 10px; overflow: auto; font-size: 11px; max-height: 300px; margin: 0;"><?php print_r($all_product_meta); ?></pre>
            </div>
            
            <!-- DEBUG BOX 4: GST & ADDITIONAL % SETTINGS -->
            <div style="background: #f8d7da; border: 2px solid #dc3545; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                <h4 style="margin: 0 0 10px 0; color: #721c24;">⚙️ DEBUG 4: GST & Additional % Settings</h4>
                <pre style="background: #fff; padding: 10px; overflow: auto; font-size: 11px; max-height: 200px; margin: 0;"><?php print_r($gst_settings); ?></pre>
            </div>
            
            <!-- DEBUG BOX 5: EXTRA FIELDS SETTINGS -->
            <div style="background: #e2e3e5; border: 2px solid #6c757d; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                <h4 style="margin: 0 0 10px 0; color: #383d41;">🏷️ DEBUG 5: Extra Fields Settings</h4>
                <pre style="background: #fff; padding: 10px; overflow: auto; font-size: 11px; max-height: 200px; margin: 0;"><?php print_r($extra_field_settings); ?></pre>
            </div>
            
            <!-- DEBUG BOX 6: DISCOUNT SETTINGS -->
            <div style="background: #d6d8db; border: 2px solid #495057; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                <h4 style="margin: 0 0 10px 0; color: #212529;">🎯 DEBUG 6: Discount Settings</h4>
                <pre style="background: #fff; padding: 10px; overflow: auto; font-size: 11px; max-height: 150px; margin: 0;"><?php print_r($discount_settings); ?></pre>
            </div>
            
            <hr style="margin: 30px 0; border: none; border-top: 3px solid #ddd;">
            
            <h3 style="margin-bottom: 20px; font-size: 1.5em;"><?php _e('PRICE BREAKUP', 'jewellery-price-calc'); ?></h3>
            
            <?php if (!$breakup || !is_array($breakup)): ?>
                <p style="color: red; font-weight: bold;"><?php _e('⚠️ ERROR: Price breakup array is empty or not an array!', 'jewellery-price-calc'); ?></p>
                <p><?php _e('Please click "Regenerate Price Breakup" button in the product editor (backend) to fix this.', 'jewellery-price-calc'); ?></p>
                <?php return; ?>
            <?php endif; ?>
            
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
                    
                    <!-- Discount Row -->
                    <?php if ($discount_percentage > 0 && $discount_amount > 0): ?>
                    <tr style="border-bottom: 1px solid #ddd; background: #d4edda;">
                        <td style="padding: 12px; color: #28a745; font-weight: bold;">
                            <?php printf(__('Discount (%s%%)', 'jewellery-price-calc'), number_format($discount_percentage, 1)); ?>
                        </td>
                        <td style="padding: 12px; text-align: right; color: #28a745; font-weight: bold;">
                            -<?php echo wc_price($discount_amount); ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                    
                    <!-- GST - ALWAYS SHOW IF GST IS ENABLED -->
                    <?php 
                    // Get GST info from breakup or fallback to settings
                    $gst_value = isset($breakup['gst']) ? floatval($breakup['gst']) : 0;
                    $gst_label = isset($breakup['gst_label']) ? $breakup['gst_label'] : get_option('jpc_gst_label', 'GST');
                    $gst_percentage = isset($breakup['gst_percentage']) ? $breakup['gst_percentage'] : get_option('jpc_gst_value', 5);
                    $gst_enabled = get_option('jpc_enable_gst');
                    
                    // Show GST if enabled (even if value is 0 for debugging)
                    if ($gst_enabled === 'yes' || $gst_enabled === '1' || $gst_enabled === 1 || $gst_enabled === true): 
                    ?>
                    <tr style="border-bottom: 1px solid #ddd; <?php echo ($gst_value <= 0) ? 'background: #fff3cd;' : ''; ?>">
                        <td style="padding: 12px;">
                            <?php echo esc_html($gst_label) . ' (' . number_format($gst_percentage, 2) . '%)'; ?>
                            <?php if ($gst_value <= 0): ?>
                                <span style="color: #856404; font-size: 0.9em;">(⚠️ GST is 0 - check calculation)</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 12px; text-align: right;"><?php echo wc_price($gst_value); ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <!-- Separator -->
                    <tr style="border-top: 3px solid #000;">
                        <td colspan="2" style="padding: 10px;">&nbsp;</td>
                    </tr>
                    
                    <!-- PRICE BEFORE DISCOUNT - FROM DATABASE -->
                    <tr style="border-bottom: 1px solid #ddd; background: #f9f9f9;">
                        <td style="padding: 15px;"><strong style="font-size: 1.1em;"><?php _e('Price Before Discount', 'jewellery-price-calc'); ?></strong></td>
                        <td style="padding: 15px; text-align: right;">
                            <strong style="font-size: 1.3em; <?php echo ($discount_percentage > 0) ? 'text-decoration: line-through; color: #999;' : 'color: #0066cc;'; ?>">
                                <?php echo wc_price($regular_price); ?>
                            </strong>
                        </td>
                    </tr>
                    
                    <!-- PRICE AFTER DISCOUNT - FROM DATABASE (only if discount exists) -->
                    <?php if ($discount_percentage > 0 && $discount_amount > 0): ?>
                    <tr style="border-bottom: 1px solid #ddd; background: #f9f9f9;">
                        <td style="padding: 15px;"><strong style="color: #d63638; font-size: 1.2em;"><?php _e('Price After Discount', 'jewellery-price-calc'); ?></strong></td>
                        <td style="padding: 15px; text-align: right;"><strong style="color: #d63638; font-size: 1.5em;"><?php echo wc_price($sale_price); ?></strong></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <!-- Savings Badge -->
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
