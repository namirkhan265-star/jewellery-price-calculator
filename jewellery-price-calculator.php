<?php
/**
 * Plugin Name: Jewellery Price Calculator
 * Plugin URI: https://brandwitty.com
 * Description: Automatic jewellery price calculation based on metal rates (Gold, Silver, Diamond, Platinum) with support for making charges, wastage, GST, and discounts
 * Version: 1.6.5
 * Author: Brandwitty
 * Author URI: https://brandwitty.com
 * Text Domain: jewellery-price-calc
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.5
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('JPC_VERSION', '1.6.5');
define('JPC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('JPC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('JPC_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Check if WooCommerce is active
 */
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    add_action('admin_notices', function() {
        echo '<div class="error"><p>Jewellery Price Calculator requires WooCommerce to be installed and active.</p></div>';
    });
    return;
}

/**
 * Load plugin files
 */
function jpc_load_files() {
    // Core includes - MATCH ACTUAL FILE NAMES
    $includes = array(
        'includes/class-jpc-database.php',
        'includes/class-jpc-metals.php',
        'includes/class-jpc-diamonds.php',
        'includes/class-jpc-diamond-groups.php',
        'includes/class-jpc-diamond-types.php',
        'includes/class-jpc-diamond-certifications.php',
        'includes/class-jpc-diamond-pricing.php',
        'includes/class-jpc-metal-groups.php',
        'includes/class-jpc-price-calculator.php',
        'includes/class-jpc-product-meta.php',
        'includes/class-jpc-frontend.php',
        'includes/class-jpc-shortcodes.php',
        'includes/class-jpc-bulk-import-export.php'
    );
    
    foreach ($includes as $file) {
        $filepath = JPC_PLUGIN_DIR . $file;
        if (file_exists($filepath)) {
            require_once $filepath;
        }
    }
    
    // Admin files
    if (is_admin()) {
        $admin_file = JPC_PLUGIN_DIR . 'includes/class-jpc-admin.php';
        if (file_exists($admin_file)) {
            require_once $admin_file;
        }
    }
}
add_action('plugins_loaded', 'jpc_load_files', 1);

/**
 * Initialize plugin
 */
function jpc_init() {
    // Initialize admin
    if (is_admin() && class_exists('JPC_Admin')) {
        JPC_Admin::get_instance();
    }
    
    // Initialize frontend
    if (class_exists('JPC_Frontend')) {
        JPC_Frontend::get_instance();
    }
    
    // Initialize product meta
    if (class_exists('JPC_Product_Meta')) {
        JPC_Product_Meta::get_instance();
    }
    
    // Initialize shortcodes
    if (class_exists('JPC_Shortcodes')) {
        JPC_Shortcodes::get_instance();
    }
}
add_action('plugins_loaded', 'jpc_init', 10);

/**
 * Plugin activation
 */
function jpc_activate() {
    if (class_exists('JPC_Database')) {
        JPC_Database::create_tables();
    }
    
    // Set default options
    $defaults = array(
        'jpc_gst_enabled' => 'yes',
        'jpc_gst_percentage' => '3',
        'jpc_gst_label' => 'GST',
        'jpc_currency_symbol' => '₹',
        'jpc_price_display' => 'both'
    );
    
    foreach ($defaults as $key => $value) {
        if (get_option($key) === false) {
            add_option($key, $value);
        }
    }
    
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'jpc_activate');

/**
 * Plugin deactivation
 */
function jpc_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'jpc_deactivate');

/**
 * Enqueue admin scripts
 */
function jpc_admin_scripts($hook) {
    if (strpos($hook, 'jewellery-price-calc') !== false || $hook === 'post.php' || $hook === 'post-new.php') {
        wp_enqueue_style('jpc-admin', JPC_PLUGIN_URL . 'assets/css/admin.css', array(), JPC_VERSION);
        wp_enqueue_script('jpc-admin', JPC_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), JPC_VERSION, true);
        
        if ($hook === 'post.php' || $hook === 'post-new.php') {
            global $post_type;
            if ($post_type === 'product') {
                wp_enqueue_script('jpc-live-calculator', JPC_PLUGIN_URL . 'assets/js/live-calculator.js', array('jquery'), JPC_VERSION, true);
                wp_localize_script('jpc-live-calculator', 'jpcLiveCalc', array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('jpc_live_calc_nonce')
                ));
            }
        }
    }
}
add_action('admin_enqueue_scripts', 'jpc_admin_scripts');

/**
 * Enqueue frontend scripts
 */
function jpc_frontend_scripts() {
    if (is_product()) {
        wp_enqueue_style('jpc-frontend', JPC_PLUGIN_URL . 'assets/css/frontend.css', array(), JPC_VERSION);
    }
}
add_action('wp_enqueue_scripts', 'jpc_frontend_scripts');
