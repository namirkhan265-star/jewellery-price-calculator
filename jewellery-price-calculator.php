<?php
/**
 * Plugin Name: Jewellery Price Calculator
 * Plugin URI: https://github.com/yourusername/jewellery-price-calculator
 * Description: Advanced price calculator for jewellery products with metal rates, making charges, and GST
 * Version: 2.5.29
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
define('JPC_VERSION', '2.5.29');
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
    
    // v2.0.0 Migration
    jpc_v2_migration();
    
    flush_rewrite_rules();
}

// v2.0.0 Migration Function
function jpc_v2_migration() {
    // Check if already migrated
    if (get_option('jpc_v2_migrated')) {
        return;
    }
    
    // Run database v2 migration (adds making_charges_per_gram column)
    JPC_Database::create_tables();
    
    // Migrate existing products to v2 defaults
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
    
    foreach ($products as $product) {
        // Set default modes if not set
        if (!get_post_meta($product->ID, '_jpc_making_charges_mode', true)) {
            update_post_meta($product->ID, '_jpc_making_charges_mode', 'auto');
        }
        
        if (!get_post_meta($product->ID, '_jpc_diamond_entry_mode', true)) {
            update_post_meta($product->ID, '_jpc_diamond_entry_mode', 'dropdown');
        }
    }
    
    // Set migration flag
    update_option('jpc_v2_migrated', true);
    
    // Add admin notice
    set_transient('jpc_v2_migration_notice', true, 60);
}

// Show migration notice
add_action('admin_notices', 'jpc_v2_migration_notice');

function jpc_v2_migration_notice() {
    if (get_transient('jpc_v2_migration_notice')) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><strong>Jewellery Price Calculator v2.0.0:</strong> Migration completed successfully! New features are now available.</p>
            <ul style="list-style: disc; margin-left: 20px;">
                <li>Making Charges per Gram (Auto-calculation)</li>
                <li>Manual Diamond Entry with 4Cs</li>
                <li>Enhanced price calculation</li>
            </ul>
        </div>
        <?php
        delete_transient('jpc_v2_migration_notice');
    }
}

// v2.5.10 Migration Notice
add_action('admin_notices', 'jpc_v2510_migration_notice');

function jpc_v2510_migration_notice() {
    // Only show if migration is needed and not completed
    if (JPC_Data_Migration_v2510::is_migration_needed() && !get_option('jpc_migration_v2510_completed')) {
        $migration_url = admin_url('admin.php?page=jpc-migration-v2510');
        ?>
        <div class="notice notice-warning">
            <p>
                <strong>Jewellery Price Calculator v2.5.10:</strong> 
                Your products need to be migrated to support the new Additional Cost Fields format.
                <a href="<?php echo esc_url($migration_url); ?>" class="button button-primary" style="margin-left: 10px;">
                    Run Migration Now
                </a>
            </p>
        </div>
        <?php
    }
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'jpc_deactivate');

function jpc_deactivate() {
    flush_rewrite_rules();
}
