<?php
/**
 * Plugin Name: Jewellery Price Calculator
 * Plugin URI: https://github.com/yourusername/jewellery-price-calculator
 * Description: Advanced price calculator for jewellery products with metal rates, making charges, and GST
 * Version: 1.8.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * Text Domain: jewellery-price-calc
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.5
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('JPC_VERSION', '1.8.0');
define('JPC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('JPC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('JPC_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Check if WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    add_action('admin_notices', function() {
        echo '<div class="error"><p><strong>Jewellery Price Calculator</strong> requires WooCommerce to be installed and active.</p></div>';
    });
    return;
}

// Include required files
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-database.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-metal-groups.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-metals.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-diamonds.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-price-calculator.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-product-meta.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-frontend.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-admin.php';

// Initialize plugin
add_action('plugins_loaded', 'jpc_init');

function jpc_init() {
    // Initialize database
    JPC_Database::init();
    
    // Initialize components
    JPC_Product_Meta::get_instance();
    JPC_Frontend::get_instance();
    JPC_Admin::get_instance();
}

// Activation hook
register_activation_hook(__FILE__, 'jpc_activate');

function jpc_activate() {
    JPC_Database::create_tables();
    
    // Set default options
    if (!get_option('jpc_gst_gold')) {
        update_option('jpc_gst_gold', 3);
    }
    if (!get_option('jpc_gst_silver')) {
        update_option('jpc_gst_silver', 3);
    }
    if (!get_option('jpc_gst_platinum')) {
        update_option('jpc_gst_platinum', 3);
    }
    if (!get_option('jpc_gst_default')) {
        update_option('jpc_gst_default', 3);
    }
    if (!get_option('jpc_gst_label')) {
        update_option('jpc_gst_label', 'GST');
    }
    if (!get_option('jpc_gst_calculation_base')) {
        update_option('jpc_gst_calculation_base', 'after_discount');
    }
    if (!get_option('jpc_discount_calculation_method')) {
        update_option('jpc_discount_calculation_method', '3');
    }
    
    flush_rewrite_rules();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'jpc_deactivate');

function jpc_deactivate() {
    flush_rewrite_rules();
}
