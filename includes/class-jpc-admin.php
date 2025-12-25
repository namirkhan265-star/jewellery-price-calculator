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
        add_action('admin_init', array($this, 'register_settings'));
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
            'dashicons-money-alt',
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
        register_setting('jpc_general_settings', 'jpc_enable_pearl_weight');
        register_setting('jpc_general_settings', 'jpc_enable_stone_cost');
        register_setting('jpc_general_settings', 'jpc_enable_stone_weight');
        register_setting('jpc_general_settings', 'jpc_enable_extra_fee');
        register_setting('jpc_general_settings', 'jpc_enable_extra_fee_weight');
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
        
        // Discount settings
        register_setting('jpc_discount_settings', 'jpc_enable_discount');
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
}
