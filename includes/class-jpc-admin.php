<?php
/**
 * Admin Interface Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Admin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('admin_head', array($this, 'inject_inline_css')); // CRITICAL: Inline CSS fallback
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_init', array($this, 'handle_settings_save'));
        add_action('admin_init', array($this, 'handle_bulk_regenerate'));
    }
    
    /**
     * Inject inline CSS as fallback (CRITICAL FIX)
     */
    public function inject_inline_css() {
        $screen = get_current_screen();
        if (!$screen || (strpos($screen->id, 'jewellery-price') === false && strpos($screen->id, 'jpc-') === false)) {
            return;
        }
        
        ?>
        <style type="text/css">
            /* CRITICAL: Inline CSS to ensure styling works even if external CSS fails */
            .jpc-admin-wrap { margin: 20px 20px 0 0; }
            .jpc-admin-content { max-width: 1200px; }
            .jpc-card { background: #fff; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04); margin-bottom: 20px; padding: 20px; }
            .jpc-card h2 { margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #eee; }
            .jpc-form .form-table th { width: 200px; padding: 15px 10px 15px 0; }
            .jpc-form .form-table td { padding: 15px 10px; }
            .jpc-modal { display: none; position: fixed; z-index: 100000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
            .jpc-modal-content { background-color: #fefefe; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px; border-radius: 4px; }
            .jpc-modal-close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
            .jpc-modal-close:hover { color: #000; }
            .jpc-edit-metal, .jpc-delete-metal, .jpc-edit-group, .jpc-delete-group { margin-right: 5px; }
            .button .dashicons { line-height: 28px; font-size: 16px; }
            .jpc-price-history-table { width: 100%; }
            .jpc-price-history-table .price-increase { color: #46b450; }
            .jpc-price-history-table .price-decrease { color: #dc3232; }
            .jpc-product-meta-box { padding: 12px; }
            .jpc-product-meta-box .form-field { margin-bottom: 15px; }
            .jpc-product-meta-box label { display: block; margin-bottom: 5px; font-weight: 600; }
            .jpc-product-meta-box input[type="text"], .jpc-product-meta-box input[type="number"], .jpc-product-meta-box select { width: 100%; max-width: 300px; }
            .jpc-price-breakup-admin { margin-top: 15px; padding: 15px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px; }
            .jpc-price-breakup-admin h4 { margin-top: 0; color: #2271b1; font-size: 16px; }
            .jpc-price-breakup-admin table { width: 100%; }
            .jpc-price-breakup-admin td { padding: 5px; }
            .jpc-price-breakup-admin .total-row { font-weight: bold; border-top: 2px solid #333; }
            .jpc-live-calc-wrapper { background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; padding: 20px; }
            .jpc-live-calc-wrapper h4 { margin: 0 0 15px 0; color: #1d2327; font-size: 16px; font-weight: 600; }
            .jpc-price-summary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
            .jpc-price-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.2); }
            .jpc-price-row:last-child { border-bottom: none; }
            .jpc-price-row .label { font-size: 15px; font-weight: 500; }
            .jpc-price-row .value { font-size: 18px; font-weight: 700; }
            .jpc-price-row.jpc-discount-row { background: rgba(74, 222, 128, 0.15); padding: 12px 15px; margin: 8px -15px; border-radius: 6px; border: 2px solid rgba(74, 222, 128, 0.3) !important; }
            .jpc-price-row.jpc-discount-row .value.discount { color: #4ade80 !important; font-size: 20px !important; font-weight: 800 !important; }
            .jpc-price-row .value.highlight { font-size: 26px; color: #fbbf24; }
            .jpc-breakdown-details { margin: 20px 0; border: 1px solid #ddd; border-radius: 4px; }
            .jpc-breakdown-details summary { padding: 12px 15px; background: #f0f0f1; cursor: pointer; font-weight: 600; list-style: none; position: relative; padding-left: 35px; }
            .jpc-breakdown-details summary:before { content: '+'; position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 18px; font-weight: bold; color: #2271b1; }
            .jpc-breakdown-details[open] summary:before { content: '−'; }
            .jpc-breakdown-details summary:hover { background: #e5e5e5; }
            .jpc-breakdown-table { width: 100%; border-collapse: collapse; }
            .jpc-breakdown-table tr { border-bottom: 1px solid #f0f0f1; }
            .jpc-breakdown-table td { padding: 10px 15px; }
            .jpc-breakdown-table td:first-child { color: #50575e; font-weight: 500; }
            .jpc-breakdown-table td:last-child { text-align: right; font-weight: 600; color: #1d2327; }
            .jpc-breakdown-table .total-row { background: #f9f9f9; border-top: 2px solid #2271b1; }
            .jpc-action-buttons { display: flex; gap: 10px; margin: 20px 0; flex-wrap: wrap; }
            .jpc-action-buttons .button { flex: 1; min-width: 150px; text-align: center; }
            .jpc-help-text { background: #f0f6fc; border-left: 4px solid #2271b1; padding: 15px; margin: 20px 0 0 0; border-radius: 4px; }
            .jpc-loading { display: inline-block; width: 20px; height: 20px; border: 3px solid rgba(0,0,0,.1); border-radius: 50%; border-top-color: #2271b1; animation: jpc-spin 1s ease-in-out infinite; }
            @keyframes jpc-spin { to { transform: rotate(360deg); } }
            .jpc-message { padding: 10px 15px; margin: 15px 0; border-radius: 4px; }
            .jpc-message.success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
            .jpc-message.error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        </style>
        <?php
    }
    
    /**
     * Handle bulk regenerate price breakup
     */
    public function handle_bulk_regenerate() {
        if (!isset($_POST['jpc_bulk_regenerate']) || !isset($_POST['_wpnonce'])) {
            return;
        }
        
        if (!wp_verify_nonce($_POST['_wpnonce'], 'jpc_bulk_regenerate')) {
            wp_die('Security check failed');
        }
        
        if (!current_user_can('manage_woocommerce')) {
            wp_die('Unauthorized');
        }
        
        // Get all products with JPC metal ID
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_jpc_metal_id',
                    'compare' => 'EXISTS'
                )
            )
        );
        
        $products = get_posts($args);
        $count = 0;
        
        foreach ($products as $product) {
            // Recalculate and update price
            JPC_Price_Calculator::calculate_and_update_price($product->ID);
            $count++;
        }
        
        // Redirect with success message
        wp_redirect(add_query_arg(array(
            'page' => 'jewellery-price-calc',
            'regenerated' => $count
        ), admin_url('admin.php')));
        exit;
    }
    
    /**
     * Handle settings save to properly handle checkboxes
     */
    public function handle_settings_save() {
        if (!isset($_POST['option_page']) || $_POST['option_page'] !== 'jpc_general_settings') {
            return;
        }
        
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'jpc_general_settings-options')) {
            return;
        }
        
        // Handle checkbox fields - set to 'no' if not checked
        $checkbox_fields = array(
            'jpc_enable_pearl_cost',
            'jpc_enable_stone_cost',
            'jpc_enable_extra_fee',
            'jpc_enable_gst',
            'jpc_show_price_breakup',
        );
        
        foreach ($checkbox_fields as $field) {
            if (!isset($_POST[$field])) {
                update_option($field, 'no');
            }
        }
        
        // Handle extra field checkboxes
        for ($i = 1; $i <= 5; $i++) {
            if (!isset($_POST['jpc_enable_extra_field_' . $i])) {
                update_option('jpc_enable_extra_field_' . $i, 'no');
            }
        }
        
        // Handle discount settings checkboxes
        if (isset($_POST['option_page']) && $_POST['option_page'] === 'jpc_discount_settings') {
            $discount_checkboxes = array(
                'jpc_enable_discount',
                'jpc_discount_on_metals',
                'jpc_discount_on_making',
                'jpc_discount_on_wastage',
            );
            
            foreach ($discount_checkboxes as $field) {
                if (!isset($_POST[$field])) {
                    update_option($field, 'no');
                }
            }
        }
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Jewellery Price Calculator', 'jewellery-price-calc'),
            __('Jewellery Price', 'jewellery-price-calc'),
            'manage_woocommerce',
            'jewellery-price-calc',
            array($this, 'render_general_settings'),
            'dashicons-admin-generic',
            56
        );
        
        add_submenu_page(
            'jewellery-price-calc',
            __('General Settings', 'jewellery-price-calc'),
            __('General', 'jewellery-price-calc'),
            'manage_woocommerce',
            'jewellery-price-calc',
            array($this, 'render_general_settings')
        );
        
        add_submenu_page(
            'jewellery-price-calc',
            __('Metal Groups', 'jewellery-price-calc'),
            __('Metal Groups', 'jewellery-price-calc'),
            'manage_woocommerce',
            'jpc-metal-groups',
            array($this, 'render_metal_groups')
        );
        
        add_submenu_page(
            'jewellery-price-calc',
            __('Metals', 'jewellery-price-calc'),
            __('Metals', 'jewellery-price-calc'),
            'manage_woocommerce',
            'jpc-metals',
            array($this, 'render_metals')
        );
        
        // NEW: Diamond Groups submenu
        add_submenu_page(
            'jewellery-price-calc',
            __('Diamond Groups', 'jewellery-price-calc'),
            __('Diamond Groups', 'jewellery-price-calc'),
            'manage_woocommerce',
            'jpc-diamond-groups',
            array($this, 'render_diamond_groups')
        );
        
        // NEW: Diamond Types submenu
        add_submenu_page(
            'jewellery-price-calc',
            __('Diamond Types', 'jewellery-price-calc'),
            __('Diamond Types', 'jewellery-price-calc'),
            'manage_woocommerce',
            'jpc-diamond-types',
            array($this, 'render_diamond_types')
        );
        
        // NEW: Diamond Certifications submenu
        add_submenu_page(
            'jewellery-price-calc',
            __('Diamond Certifications', 'jewellery-price-calc'),
            __('Certifications', 'jewellery-price-calc'),
            'manage_woocommerce',
            'jpc-diamond-certifications',
            array($this, 'render_diamond_certifications')
        );
        
        add_submenu_page(
            'jewellery-price-calc',
            __('Diamonds (Legacy)', 'jewellery-price-calc'),
            __('Diamonds (Legacy)', 'jewellery-price-calc'),
            'manage_woocommerce',
            'jpc-diamonds',
            array($this, 'render_diamonds')
        );
        
        add_submenu_page(
            'jewellery-price-calc',
            __('Discount Settings', 'jewellery-price-calc'),
            __('Discount', 'jewellery-price-calc'),
            'manage_woocommerce',
            'jpc-discount',
            array($this, 'render_discount_settings')
        );
        
        add_submenu_page(
            'jewellery-price-calc',
            __('Price History', 'jewellery-price-calc'),
            __('Price History', 'jewellery-price-calc'),
            'manage_woocommerce',
            'jpc-price-history',
            array($this, 'render_price_history')
        );
        
        add_submenu_page(
            'jewellery-price-calc',
            __('Shortcodes', 'jewellery-price-calc'),
            __('Shortcodes', 'jewellery-price-calc'),
            'manage_woocommerce',
            'jpc-shortcodes',
            array($this, 'render_shortcodes')
        );
        
        // Enhanced Debug page
        add_submenu_page(
            'jewellery-price-calc',
            __('Debug & Diagnostics', 'jewellery-price-calc'),
            __('🔧 Debug', 'jewellery-price-calc'),
            'manage_woocommerce',
            'jpc-debug',
            array($this, 'render_debug')
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'jewellery-price') === false && strpos($hook, 'jpc-') === false) {
            return;
        }
        
        wp_enqueue_style('jpc-admin-css', JPC_PLUGIN_URL . 'assets/css/admin.css', array(), JPC_VERSION);
        wp_enqueue_script('jpc-admin-js', JPC_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), JPC_VERSION, true);
        
        wp_localize_script('jpc-admin-js', 'jpcAdmin', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('jpc_admin_nonce'),
            'confirmDelete' => __('Are you sure you want to delete this item?', 'jewellery-price-calc'),
        ));
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        // General settings
        register_setting('jpc_general_settings', 'jpc_enable_pearl_cost');
        register_setting('jpc_general_settings', 'jpc_enable_stone_cost');
        register_setting('jpc_general_settings', 'jpc_enable_extra_fee');
        register_setting('jpc_general_settings', 'jpc_additional_percentage_label');
        register_setting('jpc_general_settings', 'jpc_additional_percentage_value');
        register_setting('jpc_general_settings', 'jpc_enable_gst');
        register_setting('jpc_general_settings', 'jpc_gst_label');
        register_setting('jpc_general_settings', 'jpc_gst_value');
        register_setting('jpc_general_settings', 'jpc_gst_gold');
        register_setting('jpc_general_settings', 'jpc_gst_silver');
        register_setting('jpc_general_settings', 'jpc_gst_diamond');
        register_setting('jpc_general_settings', 'jpc_gst_platinum');
        register_setting('jpc_general_settings', 'jpc_price_rounding');
        register_setting('jpc_general_settings', 'jpc_show_price_breakup');
        
        // Extra fields
        for ($i = 1; $i <= 5; $i++) {
            register_setting('jpc_general_settings', 'jpc_enable_extra_field_' . $i);
            register_setting('jpc_general_settings', 'jpc_extra_field_label_' . $i);
        }
        
        // Discount settings - ENHANCED
        register_setting('jpc_discount_settings', 'jpc_enable_discount');
        register_setting('jpc_discount_settings', 'jpc_discount_calculation_method'); // NEW
        register_setting('jpc_discount_settings', 'jpc_discount_timing'); // NEW
        register_setting('jpc_discount_settings', 'jpc_gst_calculation_base'); // NEW
        register_setting('jpc_discount_settings', 'jpc_discount_on_metals');
        register_setting('jpc_discount_settings', 'jpc_discount_on_making');
        register_setting('jpc_discount_settings', 'jpc_discount_on_wastage');
    }
    
    /**
     * Render general settings page
     */
    public function render_general_settings() {
        include JPC_PLUGIN_DIR . 'templates/admin/general-settings.php';
    }
    
    /**
     * Render metal groups page
     */
    public function render_metal_groups() {
        include JPC_PLUGIN_DIR . 'templates/admin/metal-groups.php';
    }
    
    /**
     * Render metals page
     */
    public function render_metals() {
        include JPC_PLUGIN_DIR . 'templates/admin/metals.php';
    }
    
    /**
     * Render diamond groups page
     */
    public function render_diamond_groups() {
        include JPC_PLUGIN_DIR . 'templates/admin/diamond-groups.php';
    }
    
    /**
     * Render diamond types page
     */
    public function render_diamond_types() {
        include JPC_PLUGIN_DIR . 'templates/admin/diamond-types.php';
    }
    
    /**
     * Render diamond certifications page
     */
    public function render_diamond_certifications() {
        include JPC_PLUGIN_DIR . 'templates/admin/diamond-certifications.php';
    }
    
    /**
     * Render diamonds page
     */
    public function render_diamonds() {
        include JPC_PLUGIN_DIR . 'templates/admin/diamonds.php';
    }
    
    /**
     * Render discount settings page
     */
    public function render_discount_settings() {
        include JPC_PLUGIN_DIR . 'templates/admin/discount-settings.php';
    }
    
    /**
     * Render price history page
     */
    public function render_price_history() {
        include JPC_PLUGIN_DIR . 'templates/admin/price-history.php';
    }
    
    /**
     * Render shortcodes page
     */
    public function render_shortcodes() {
        include JPC_PLUGIN_DIR . 'templates/admin/shortcodes.php';
    }
    
    /**
     * Render enhanced debug page
     */
    public function render_debug() {
        // Use enhanced debug page with price calculation details
        include JPC_PLUGIN_DIR . 'templates/admin/debug-enhanced.php';
    }
}
