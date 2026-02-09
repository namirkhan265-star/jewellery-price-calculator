<?php
/**
 * Frontend Display Handler v2.2.2
 * Simplified version to prevent critical errors
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
        add_filter('woocommerce_product_tabs', array($this, 'add_custom_product_tab'), 98);
    }
    
    /**
     * Add custom product tab for JPC details
     */
    public function add_custom_product_tab($tabs) {
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
        
        // Add JPC Details tab
        $tabs['jpc_details'] = array(
            'title'    => __('Product Details', 'jewellery-price-calc'),
            'priority' => 50,
            'callback' => array($this, 'render_jpc_details_tab')
        );
        
        return $tabs;
    }
    
    /**
     * Render JPC Details tab content
     */
    public function render_jpc_details_tab() {
        global $product;
        
        if (!$product) {
            return;
        }
        
        $product_id = $product->get_id();
        $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
        
        if (!$metal_id) {
            return;
        }
        
        // Get metal info
        $metal = JPC_Metals::get_by_id($metal_id);
        if (!$metal) {
            echo '<p>Metal information not found.</p>';
            return;
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
        
        ?>
        <div class="jpc-product-details" style="padding: 20px;">
            
            <!-- METAL DETAILS -->
            <h3 style="margin-bottom: 15px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Metal Details</h3>
            <table class="shop_attributes" style="width: 100%; margin-bottom: 30px;">
                <tbody>
                    <tr>
                        <th style="width: 30%; padding: 10px; background: #f9f9f9;">Type</th>
                        <td style="padding: 10px;"><?php echo esc_html($metal->display_name); ?></td>
                    </tr>
                    <tr>
                        <th style="width: 30%; padding: 10px; background: #f9f9f9;">Karat</th>
                        <td style="padding: 10px;"><?php echo esc_html($metal->group_name); ?></td>
                    </tr>
                    <tr>
                        <th style="width: 30%; padding: 10px; background: #f9f9f9;">Rate Per Gram</th>
                        <td style="padding: 10px;"><?php echo wc_price($metal->price_per_unit); ?></td>
                    </tr>
                    <?php if ($metal_weight): ?>
                    <tr>
                        <th style="width: 30%; padding: 10px; background: #f9f9f9;">Weight</th>
                        <td style="padding: 10px;"><?php echo esc_html($metal_weight); ?> gram</td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($wastage_percentage): ?>
                    <tr>
                        <th style="width: 30%; padding: 10px; background: #f9f9f9;">Wastage %</th>
                        <td style="padding: 10px;"><?php echo esc_html($wastage_percentage); ?>%</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <!-- DIAMOND DETAILS -->
            <?php if ($diamond_id || $manual_diamond_weight): ?>
            <h3 style="margin-bottom: 15px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Diamond Details</h3>
            <table class="shop_attributes" style="width: 100%; margin-bottom: 30px;">
                <tbody>
                    <?php if ($diamond_id): 
                        $diamond = JPC_Diamonds::get_by_id($diamond_id);
                        if ($diamond):
                            $shape = JPC_Diamond_Shapes::get_by_id($diamond->shape_id);
                            $clarity = JPC_Diamond_Clarities::get_by_id($diamond->clarity_id);
                            $colour = JPC_Diamond_Colours::get_by_id($diamond->colour_id);
                            $cut = JPC_Diamond_Cuts::get_by_id($diamond->cut_id);
                    ?>
                    <tr>
                        <th style="width: 30%; padding: 10px; background: #f9f9f9;">Diamond Type</th>
                        <td style="padding: 10px;"><?php echo esc_html($diamond->display_name); ?></td>
                    </tr>
                    <?php if ($shape): ?>
                    <tr>
                        <th style="width: 30%; padding: 10px; background: #f9f9f9;">Shape</th>
                        <td style="padding: 10px;"><?php echo esc_html($shape->name); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($clarity): ?>
                    <tr>
                        <th style="width: 30%; padding: 10px; background: #f9f9f9;">Clarity</th>
                        <td style="padding: 10px;"><?php echo esc_html($clarity->name); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($colour): ?>
                    <tr>
                        <th style="width: 30%; padding: 10px; background: #f9f9f9;">Colour</th>
                        <td style="padding: 10px;"><?php echo esc_html($colour->name); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($cut): ?>
                    <tr>
                        <th style="width: 30%; padding: 10px; background: #f9f9f9;">Cut</th>
                        <td style="padding: 10px;"><?php echo esc_html($cut->name); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th style="width: 30%; padding: 10px; background: #f9f9f9;">Diamond Carat</th>
                        <td style="padding: 10px;"><?php echo esc_html($diamond->weight); ?> Carat</td>
                    </tr>
                    <?php endif; ?>
                    <?php elseif ($manual_diamond_weight): ?>
                    <tr>
                        <th style="width: 30%; padding: 10px; background: #f9f9f9;">Diamond Type</th>
                        <td style="padding: 10px;">Custom Diamond</td>
                    </tr>
                    <tr>
                        <th style="width: 30%; padding: 10px; background: #f9f9f9;">Diamond Carat</th>
                        <td style="padding: 10px;"><?php echo esc_html($manual_diamond_weight); ?> Carat</td>
                    </tr>
                    <?php if ($manual_diamond_rate): ?>
                    <tr>
                        <th style="width: 30%; padding: 10px; background: #f9f9f9;">Rate per Carat</th>
                        <td style="padding: 10px;"><?php echo wc_price($manual_diamond_rate); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php if ($num_diamonds): ?>
                    <tr>
                        <th style="width: 30%; padding: 10px; background: #f9f9f9;">Number of Diamonds</th>
                        <td style="padding: 10px;"><?php echo esc_html($num_diamonds); ?></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php endif; ?>
            
            <!-- ADDITIONAL COMPONENTS -->
            <?php if ($pearl_cost || $stone_cost): ?>
            <h3 style="margin-bottom: 15px; border-bottom: 2px solid #ddd; padding-bottom: 10px;">Additional Components</h3>
            <table class="shop_attributes" style="width: 100%;">
                <tbody>
                    <?php if ($pearl_cost): ?>
                    <tr>
                        <th style="width: 30%; padding: 10px; background: #f9f9f9;">Pearl Cost</th>
                        <td style="padding: 10px;"><?php echo wc_price($pearl_cost); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($stone_cost): ?>
                    <tr>
                        <th style="width: 30%; padding: 10px; background: #f9f9f9;">Stone Cost</th>
                        <td style="padding: 10px;"><?php echo wc_price($stone_cost); ?></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php endif; ?>
            
        </div>
        <?php
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
