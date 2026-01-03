<?php
/**
 * Product Meta Box Handler
 * Handles the admin product meta box for price calculator
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Product_Meta {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_product', array($this, 'save_product_meta'), 10, 2);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // AJAX handler for instant price update
        add_action('wp_ajax_jpc_update_single_price', array($this, 'ajax_update_single_price'));
    }
    
    /**
     * AJAX handler for instant price update with feedback
     */
    public function ajax_update_single_price() {
        check_ajax_referer('jpc_update_price', 'nonce');
        
        if (!current_user_can('edit_products')) {
            wp_send_json_error(array(
                'message' => 'Permission denied'
            ));
        }
        
        $product_id = intval($_POST['product_id']);
        
        if (!$product_id) {
            wp_send_json_error(array(
                'message' => 'Invalid product ID'
            ));
        }
        
        try {
            // Clear all caches BEFORE calculation
            wp_cache_delete($product_id, 'post_meta');
            wp_cache_delete($product_id, 'posts');
            clean_post_cache($product_id);
            
            // Calculate and update prices
            $result = JPC_Price_Calculator::calculate_and_update_price($product_id);
            
            if ($result === true) {
                // Get the updated prices for display
                $prices = JPC_Price_Calculator::calculate_product_prices($product_id);
                $breakup = get_post_meta($product_id, '_jpc_price_breakup', true);
                
                // Force cache clear AFTER update
                wp_cache_flush();
                clean_post_cache($product_id);
                
                // Get WooCommerce prices
                $regular_price = get_post_meta($product_id, '_regular_price', true);
                $sale_price = get_post_meta($product_id, '_sale_price', true);
                
                wp_send_json_success(array(
                    'message' => 'Price updated successfully!',
                    'prices' => array(
                        'regular_price' => wc_price($regular_price),
                        'sale_price' => wc_price($sale_price),
                        'discount' => wc_price($prices['discount_amount']),
                        'discount_percentage' => number_format($prices['discount_percentage'], 1) . '%',
                        'gst' => wc_price($prices['gst_on_discounted']),
                        'gst_percentage' => number_format($prices['gst_percentage'], 1) . '%',
                    ),
                    'raw_prices' => array(
                        'regular_price' => $regular_price,
                        'sale_price' => $sale_price,
                        'discount_amount' => $prices['discount_amount'],
                        'gst_amount' => $prices['gst_on_discounted'],
                    )
                ));
            } else {
                wp_send_json_error(array(
                    'message' => 'Failed to update price. Please check product configuration.'
                ));
            }
        } catch (Exception $e) {
            wp_send_json_error(array(
                'message' => 'Error: ' . $e->getMessage()
            ));
        }
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        global $post;
        
        if (($hook === 'post.php' || $hook === 'post-new.php') && $post && $post->post_type === 'product') {
            wp_enqueue_style('jpc-admin-css', JPC_PLUGIN_URL . 'assets/css/admin.css', array(), JPC_VERSION);
            wp_enqueue_script('jpc-admin-js', JPC_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), JPC_VERSION, true);
            
            // Add AJAX variables
            wp_localize_script('jpc-admin-js', 'jpcAjax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('jpc_update_price'),
                'product_id' => $post->ID,
            ));
        }
    }
    
    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'jpc_product_calculator',
            __('Jewellery Price Calculator', 'jewellery-price-calc'),
            array($this, 'render_calculator_meta_box'),
            'product',
            'normal',
            'high'
        );
        
        add_meta_box(
            'jpc_live_calculation',
            __('Live Price Calculation', 'jewellery-price-calc'),
            array($this, 'render_live_calculation_meta_box'),
            'product',
            'side',
            'high'
        );
    }
    
    /**
     * Render calculator meta box
     */
    public function render_calculator_meta_box($post) {
        wp_nonce_field('jpc_save_product_meta', 'jpc_product_meta_nonce');
        
        // Get saved values
        $metal_id = get_post_meta($post->ID, '_jpc_metal_id', true);
        $metal_weight = get_post_meta($post->ID, '_jpc_metal_weight', true);
        $making_charge = get_post_meta($post->ID, '_jpc_making_charge', true);
        $making_charge_type = get_post_meta($post->ID, '_jpc_making_charge_type', true) ?: 'percentage';
        $wastage_charge = get_post_meta($post->ID, '_jpc_wastage_charge', true);
        $wastage_charge_type = get_post_meta($post->ID, '_jpc_wastage_charge_type', true) ?: 'percentage';
        $diamond_id = get_post_meta($post->ID, '_jpc_diamond_id', true);
        $diamond_quantity = get_post_meta($post->ID, '_jpc_diamond_quantity', true);
        $pearl_cost = get_post_meta($post->ID, '_jpc_pearl_cost', true);
        $stone_cost = get_post_meta($post->ID, '_jpc_stone_cost', true);
        $extra_fee = get_post_meta($post->ID, '_jpc_extra_fee', true);
        $discount_percentage = get_post_meta($post->ID, '_jpc_discount_percentage', true);
        
        // Get metals and diamonds
        $metals = JPC_Metals::get_all();
        $diamonds = JPC_Diamonds::get_all();
        
        include JPC_PLUGIN_DIR . 'templates/admin/product-meta-box.php';
    }
    
    /**
     * Render live calculation meta box with INSTANT UPDATE button
     */
    public function render_live_calculation_meta_box($post) {
        $prices = JPC_Price_Calculator::calculate_product_prices($post->ID);
        
        if (!$prices) {
            echo '<p>' . __('Please configure metal and other details first.', 'jewellery-price-calc') . '</p>';
            return;
        }
        
        $discount_percentage = $prices['discount_percentage'];
        
        ?>
        <div class="jpc-live-calculation">
            <style>
                .jpc-live-calculation {
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
                }
                .jpc-price-summary {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 20px;
                    border-radius: 8px;
                    margin-bottom: 15px;
                }
                .jpc-price-row {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 12px 0;
                    border-bottom: 1px solid rgba(255,255,255,0.2);
                }
                .jpc-price-row:last-child {
                    border-bottom: none;
                }
                .jpc-price-label {
                    font-size: 14px;
                    opacity: 0.9;
                }
                .jpc-price-value {
                    font-size: 18px;
                    font-weight: 600;
                }
                .jpc-price-value.highlight {
                    font-size: 22px;
                    background: rgba(255,255,255,0.2);
                    padding: 8px 15px;
                    border-radius: 5px;
                }
                .jpc-price-value.discount {
                    color: #4ade80;
                }
                .jpc-update-button {
                    width: 100%;
                    padding: 12px;
                    background: #10b981;
                    color: white;
                    border: none;
                    border-radius: 6px;
                    font-size: 15px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    margin-bottom: 10px;
                    position: relative;
                }
                .jpc-update-button:hover:not(:disabled) {
                    background: #059669;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
                }
                .jpc-update-button:disabled {
                    background: #9ca3af;
                    cursor: not-allowed;
                }
                .jpc-update-button .spinner {
                    display: none;
                    width: 16px;
                    height: 16px;
                    border: 2px solid rgba(255,255,255,0.3);
                    border-top-color: white;
                    border-radius: 50%;
                    animation: spin 0.6s linear infinite;
                    margin-right: 8px;
                    vertical-align: middle;
                }
                .jpc-update-button.loading .spinner {
                    display: inline-block;
                }
                @keyframes spin {
                    to { transform: rotate(360deg); }
                }
                .jpc-message {
                    padding: 12px;
                    border-radius: 6px;
                    margin-bottom: 10px;
                    font-size: 13px;
                    display: none;
                }
                .jpc-message.success {
                    background: #d1fae5;
                    color: #065f46;
                    border: 1px solid #10b981;
                }
                .jpc-message.error {
                    background: #fee2e2;
                    color: #991b1b;
                    border: 1px solid #ef4444;
                }
                .jpc-breakdown {
                    background: #f9fafb;
                    border: 1px solid #e5e7eb;
                    border-radius: 6px;
                    padding: 15px;
                }
                .jpc-breakdown-row {
                    display: flex;
                    justify-content: space-between;
                    padding: 8px 0;
                    font-size: 13px;
                    border-bottom: 1px solid #e5e7eb;
                }
                .jpc-breakdown-row:last-child {
                    border-bottom: none;
                    font-weight: 600;
                    font-size: 14px;
                    padding-top: 12px;
                    margin-top: 8px;
                    border-top: 2px solid #d1d5db;
                }
                .jpc-breakdown-label {
                    color: #6b7280;
                }
                .jpc-breakdown-value {
                    color: #111827;
                    font-weight: 500;
                }
            </style>
            
            <!-- Update Button with AJAX -->
            <button type="button" class="jpc-update-button" id="jpc-update-price-btn">
                <span class="spinner"></span>
                <span class="button-text">🔄 Update Price Now</span>
            </button>
            
            <!-- Message Container -->
            <div id="jpc-update-message" class="jpc-message"></div>
            
            <!-- Price Summary -->
            <div class="jpc-price-summary">
                <div class="jpc-price-row">
                    <span class="jpc-price-label">Price Before Discount:</span>
                    <span class="jpc-price-value" id="jpc-regular-price"><?php echo wc_price($prices['regular_price']); ?></span>
                </div>
                
                <?php if ($discount_percentage > 0): ?>
                <div class="jpc-price-row">
                    <span class="jpc-price-label">Discount (<?php echo number_format($discount_percentage, 1); ?>%):</span>
                    <span class="jpc-price-value discount" id="jpc-discount">-<?php echo wc_price($prices['discount_amount']); ?></span>
                </div>
                <?php endif; ?>
                
                <div class="jpc-price-row">
                    <span class="jpc-price-label">Price After Discount:</span>
                    <span class="jpc-price-value highlight" id="jpc-sale-price"><?php echo wc_price($prices['sale_price']); ?></span>
                </div>
            </div>
            
            <!-- Detailed Breakdown -->
            <details style="margin-top: 15px;">
                <summary style="cursor: pointer; font-weight: 600; padding: 10px; background: #f3f4f6; border-radius: 6px;">
                    📊 View Detailed Breakdown
                </summary>
                <div class="jpc-breakdown" style="margin-top: 10px;">
                    <div class="jpc-breakdown-row">
                        <span class="jpc-breakdown-label">Metal Price:</span>
                        <span class="jpc-breakdown-value"><?php echo wc_price($prices['metal_price']); ?></span>
                    </div>
                    <?php if ($prices['diamond_price'] > 0): ?>
                    <div class="jpc-breakdown-row">
                        <span class="jpc-breakdown-label">Diamond Price:</span>
                        <span class="jpc-breakdown-value"><?php echo wc_price($prices['diamond_price']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($prices['making_charge'] > 0): ?>
                    <div class="jpc-breakdown-row">
                        <span class="jpc-breakdown-label">Making Charge:</span>
                        <span class="jpc-breakdown-value"><?php echo wc_price($prices['making_charge']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($prices['wastage_charge'] > 0): ?>
                    <div class="jpc-breakdown-row">
                        <span class="jpc-breakdown-label">Wastage Charge:</span>
                        <span class="jpc-breakdown-value"><?php echo wc_price($prices['wastage_charge']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($prices['pearl_cost'] > 0): ?>
                    <div class="jpc-breakdown-row">
                        <span class="jpc-breakdown-label">Pearl Cost:</span>
                        <span class="jpc-breakdown-value"><?php echo wc_price($prices['pearl_cost']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($prices['stone_cost'] > 0): ?>
                    <div class="jpc-breakdown-row">
                        <span class="jpc-breakdown-label">Stone Cost:</span>
                        <span class="jpc-breakdown-value"><?php echo wc_price($prices['stone_cost']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($prices['extra_fee'] > 0): ?>
                    <div class="jpc-breakdown-row">
                        <span class="jpc-breakdown-label">Extra Fee:</span>
                        <span class="jpc-breakdown-value"><?php echo wc_price($prices['extra_fee']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($prices['extra_field_costs'] > 0): ?>
                    <div class="jpc-breakdown-row">
                        <span class="jpc-breakdown-label">Extra Fields:</span>
                        <span class="jpc-breakdown-value"><?php echo wc_price($prices['extra_field_costs']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($prices['additional_percentage_amount'] > 0): ?>
                    <div class="jpc-breakdown-row">
                        <span class="jpc-breakdown-label"><?php echo get_option('jpc_additional_percentage_label', 'Additional %'); ?>:</span>
                        <span class="jpc-breakdown-value"><?php echo wc_price($prices['additional_percentage_amount']); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="jpc-breakdown-row">
                        <span class="jpc-breakdown-label">Subtotal:</span>
                        <span class="jpc-breakdown-value"><?php echo wc_price($prices['subtotal_after_additional']); ?></span>
                    </div>
                    <?php if ($discount_percentage > 0): ?>
                    <div class="jpc-breakdown-row" style="background: #d1fae5; margin: 8px -15px; padding: 8px 15px;">
                        <span class="jpc-breakdown-label" style="color: #065f46; font-weight: 600;">Discount (<?php echo number_format($discount_percentage, 1); ?>%):</span>
                        <span class="jpc-breakdown-value" style="color: #065f46; font-weight: 600;">-<?php echo wc_price($prices['discount_amount']); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="jpc-breakdown-row">
                        <span class="jpc-breakdown-label">GST (<?php echo number_format($prices['gst_percentage'], 1); ?>%):</span>
                        <span class="jpc-breakdown-value" id="jpc-gst"><?php echo wc_price($prices['gst_on_discounted']); ?></span>
                    </div>
                    <div class="jpc-breakdown-row">
                        <span class="jpc-breakdown-label">Final Price:</span>
                        <span class="jpc-breakdown-value"><?php echo wc_price($prices['sale_price']); ?></span>
                    </div>
                </div>
            </details>
            
            <script>
            jQuery(document).ready(function($) {
                $('#jpc-update-price-btn').on('click', function() {
                    var $btn = $(this);
                    var $message = $('#jpc-update-message');
                    
                    // Prevent double-click
                    if ($btn.prop('disabled')) {
                        return;
                    }
                    
                    // Show loading state
                    $btn.prop('disabled', true).addClass('loading');
                    $btn.find('.button-text').text('Updating...');
                    $message.hide().removeClass('success error');
                    
                    // AJAX request
                    $.ajax({
                        url: jpcAjax.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'jpc_update_single_price',
                            product_id: jpcAjax.product_id,
                            nonce: jpcAjax.nonce
                        },
                        success: function(response) {
                            if (response.success) {
                                // Update displayed prices
                                $('#jpc-regular-price').html(response.data.prices.regular_price);
                                $('#jpc-sale-price').html(response.data.prices.sale_price);
                                $('#jpc-discount').html('-' + response.data.prices.discount);
                                $('#jpc-gst').html(response.data.prices.gst);
                                
                                // Show success message
                                $message.removeClass('error').addClass('success')
                                    .html('✓ ' + response.data.message + '<br><small>Regular: ' + response.data.prices.regular_price + ' | Sale: ' + response.data.prices.sale_price + '</small>')
                                    .fadeIn();
                                
                                // Auto-hide after 5 seconds
                                setTimeout(function() {
                                    $message.fadeOut();
                                }, 5000);
                            } else {
                                $message.removeClass('success').addClass('error')
                                    .html('✗ ' + response.data.message)
                                    .fadeIn();
                            }
                        },
                        error: function(xhr, status, error) {
                            $message.removeClass('success').addClass('error')
                                .html('✗ AJAX Error: ' + error)
                                .fadeIn();
                        },
                        complete: function() {
                            // Reset button state
                            $btn.prop('disabled', false).removeClass('loading');
                            $btn.find('.button-text').text('🔄 Update Price Now');
                        }
                    });
                });
            });
            </script>
        </div>
        <?php
    }
    
    /**
     * Save product meta
     */
    public function save_product_meta($post_id, $post) {
        // Verify nonce
        if (!isset($_POST['jpc_product_meta_nonce']) || !wp_verify_nonce($_POST['jpc_product_meta_nonce'], 'jpc_save_product_meta')) {
            return;
        }
        
        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_product', $post_id)) {
            return;
        }
        
        // Save metal data
        if (isset($_POST['_jpc_metal_id'])) {
            update_post_meta($post_id, '_jpc_metal_id', sanitize_text_field($_POST['_jpc_metal_id']));
        }
        
        if (isset($_POST['_jpc_metal_weight'])) {
            update_post_meta($post_id, '_jpc_metal_weight', floatval($_POST['_jpc_metal_weight']));
        }
        
        if (isset($_POST['_jpc_making_charge'])) {
            update_post_meta($post_id, '_jpc_making_charge', floatval($_POST['_jpc_making_charge']));
        }
        
        if (isset($_POST['_jpc_making_charge_type'])) {
            update_post_meta($post_id, '_jpc_making_charge_type', sanitize_text_field($_POST['_jpc_making_charge_type']));
        }
        
        if (isset($_POST['_jpc_wastage_charge'])) {
            update_post_meta($post_id, '_jpc_wastage_charge', floatval($_POST['_jpc_wastage_charge']));
        }
        
        if (isset($_POST['_jpc_wastage_charge_type'])) {
            update_post_meta($post_id, '_jpc_wastage_charge_type', sanitize_text_field($_POST['_jpc_wastage_charge_type']));
        }
        
        // Save diamond data
        if (isset($_POST['_jpc_diamond_id'])) {
            update_post_meta($post_id, '_jpc_diamond_id', sanitize_text_field($_POST['_jpc_diamond_id']));
        }
        
        if (isset($_POST['_jpc_diamond_quantity'])) {
            update_post_meta($post_id, '_jpc_diamond_quantity', intval($_POST['_jpc_diamond_quantity']));
        }
        
        // Save additional costs
        if (isset($_POST['_jpc_pearl_cost'])) {
            update_post_meta($post_id, '_jpc_pearl_cost', floatval($_POST['_jpc_pearl_cost']));
        }
        
        if (isset($_POST['_jpc_stone_cost'])) {
            update_post_meta($post_id, '_jpc_stone_cost', floatval($_POST['_jpc_stone_cost']));
        }
        
        if (isset($_POST['_jpc_extra_fee'])) {
            update_post_meta($post_id, '_jpc_extra_fee', floatval($_POST['_jpc_extra_fee']));
        }
        
        // Save extra fields
        for ($i = 1; $i <= 5; $i++) {
            if (isset($_POST['_jpc_extra_field_' . $i])) {
                update_post_meta($post_id, '_jpc_extra_field_' . $i, floatval($_POST['_jpc_extra_field_' . $i]));
            }
        }
        
        // Save discount
        if (isset($_POST['_jpc_discount_percentage'])) {
            update_post_meta($post_id, '_jpc_discount_percentage', floatval($_POST['_jpc_discount_percentage']));
        }
        
        // Calculate and update prices automatically on save
        JPC_Price_Calculator::calculate_and_update_price($post_id);
    }
}
