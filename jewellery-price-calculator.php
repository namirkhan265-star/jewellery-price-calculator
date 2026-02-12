<?php
/**
 * Plugin Name: Jewellery Price Calculator
 * Plugin URI: https://github.com/yourusername/jewellery-price-calculator
 * Description: Advanced price calculator for jewellery products with metal rates, making charges, and GST
 * Version: 2.5.34
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
define('JPC_VERSION', '2.5.34');
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

// Include required files - ALL class files in correct order
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-database-v2.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-metal-groups.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-metals.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-diamond-groups.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-diamond-types.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-diamond-certifications.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-diamond-shapes.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-diamond-colours.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-diamond-clarities.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-diamond-cuts.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-diamonds.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-price-calculator-v2.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-product-meta-box-v2.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-frontend.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-admin.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-shortcodes.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-bulk-import-export.php';
require_once JPC_PLUGIN_DIR . 'includes/class-jpc-data-migration-v2510.php'; // NEW v2.5.10

// Initialize plugin
add_action('plugins_loaded', 'jpc_init');

function jpc_init() {
    // Initialize ALL singleton classes (CRITICAL: Must initialize all classes with hooks/shortcodes/AJAX)
    // Note: Database class doesn't need initialization - it only has static methods
    JPC_Metal_Groups::get_instance();
    JPC_Metals::get_instance();
    JPC_Diamond_Groups::get_instance();
    JPC_Diamond_Types::get_instance();
    JPC_Diamond_Certifications::get_instance();
    JPC_Diamond_Shapes::get_instance();
    JPC_Diamond_Colours::get_instance();
    JPC_Diamond_Clarities::get_instance();
    JPC_Diamond_Cuts::get_instance();
    JPC_Diamonds::get_instance();
    JPC_Product_Meta_Box::get_instance();
    JPC_Frontend::get_instance();
    JPC_Admin::get_instance();
    JPC_Shortcodes::get_instance();
    JPC_Bulk_Import_Export::get_instance();
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
    if (!get_option('jpc_enable_gst')) {
        update_option('jpc_enable_gst', 'yes');
    }
    if (!get_option('jpc_gst_value')) {
        update_option('jpc_gst_value', 3);
    }
    if (!get_option('jpc_enable_discount')) {
        update_option('jpc_enable_discount', 'yes');
    }
    if (!get_option('jpc_discount_calculation_method')) {
        update_option('jpc_discount_calculation_method', '1');
    }
    if (!get_option('jpc_enable_additional_percentage')) {
        update_option('jpc_enable_additional_percentage', 'no');
    }
    if (!get_option('jpc_additional_percentage_value')) {
        update_option('jpc_additional_percentage_value', 0);
    }
    if (!get_option('jpc_additional_percentage_label')) {
        update_option('jpc_additional_percentage_label', 'Additional Percentage');
    }
    
    // Additional Cost Fields 1-3 (Pearl/Stone/Extra Fee)
    if (!get_option('jpc_enable_pearl_cost')) {
        update_option('jpc_enable_pearl_cost', 'yes');
    }
    if (!get_option('jpc_pearl_cost_label')) {
        update_option('jpc_pearl_cost_label', 'Pearl Cost');
    }
    if (!get_option('jpc_enable_stone_cost')) {
        update_option('jpc_enable_stone_cost', 'yes');
    }
    if (!get_option('jpc_stone_cost_label')) {
        update_option('jpc_stone_cost_label', 'Stone Cost');
    }
    if (!get_option('jpc_enable_extra_fee')) {
        update_option('jpc_enable_extra_fee', 'yes');
    }
    if (!get_option('jpc_extra_fee_label')) {
        update_option('jpc_extra_fee_label', 'Extra Fee');
    }
    
    // Extra Fields 1-5
    for ($i = 1; $i <= 5; $i++) {
        if (!get_option('jpc_enable_extra_field_' . $i)) {
            update_option('jpc_enable_extra_field_' . $i, 'yes');
        }
        if (!get_option('jpc_extra_field_label_' . $i)) {
            update_option('jpc_extra_field_label_' . $i, 'Extra Field #' . $i);
        }
    }
    
    flush_rewrite_rules();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'jpc_deactivate');

function jpc_deactivate() {
    flush_rewrite_rules();
}
