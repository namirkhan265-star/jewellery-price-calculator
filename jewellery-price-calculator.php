<?php
/**
 * Plugin Name: Jewellery Price Calculator
 * Plugin URI: https://github.com/namirkhan265-star/jewellery-price-calculator
 * Description: Automatic jewellery price calculation based on metal rates (Gold, Silver, Diamond, Platinum) with support for making charges, wastage, GST, and discounts
 * Version: 1.0.6
 * Author: Brand Witty
 * Author URI: https://bhindi.io
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
define('JPC_VERSION', '1.0.6');
define('JPC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('JPC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('JPC_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
class Jewellery_Price_Calculator {
    
    private static $instance = null;
    
    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        // Check if WooCommerce is active
        add_action('plugins_loaded', array($this, 'init'));
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Check WooCommerce dependency
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }
        
        // Load plugin files
        $this->load_dependencies();
        
        // Initialize components
        $this->init_hooks();
        
        // Load text domain
        load_plugin_textdomain('jewellery-price-calc', false, dirname(JPC_PLUGIN_BASENAME) . '/languages');
    }
    
    /**
     * Load required files
     */
    private function load_dependencies() {
        // Admin files
        require_once JPC_PLUGIN_DIR . 'includes/class-jpc-admin.php';
        require_once JPC_PLUGIN_DIR . 'includes/class-jpc-metal-groups.php';
        require_once JPC_PLUGIN_DIR . 'includes/class-jpc-metals.php';
        require_once JPC_PLUGIN_DIR . 'includes/class-jpc-diamonds.php';
        require_once JPC_PLUGIN_DIR . 'includes/class-jpc-product-meta.php';
        require_once JPC_PLUGIN_DIR . 'includes/class-jpc-price-calculator.php';
        require_once JPC_PLUGIN_DIR . 'includes/class-jpc-database.php';
        require_once JPC_PLUGIN_DIR . 'includes/class-jpc-frontend.php';
        require_once JPC_PLUGIN_DIR . 'includes/class-jpc-shortcodes.php';
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Activation/Deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Initialize components
        JPC_Admin::get_instance();
        JPC_Metal_Groups::get_instance();
        JPC_Metals::get_instance();
        JPC_Diamonds::get_instance();
        JPC_Product_Meta::get_instance();
        JPC_Price_Calculator::get_instance();
        JPC_Frontend::get_instance();
        JPC_Shortcodes::get_instance();
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables
        JPC_Database::create_tables();
        
        // Set default options
        $this->set_default_options();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Set default plugin options
     */
    private function set_default_options() {
        $defaults = array(
            'jpc_enable_pearl_cost' => 'no',
            'jpc_enable_stone_cost' => 'no',
            'jpc_enable_extra_fee' => 'no',
            'jpc_enable_gst' => 'yes',
            'jpc_gst_label' => 'Tax',
            'jpc_gst_value' => '5',
            'jpc_enable_discount' => 'yes',
            'jpc_price_rounding' => 'default',
            'jpc_show_price_breakup' => 'yes',
        );
        
        foreach ($defaults as $key => $value) {
            if (get_option($key) === false) {
                add_option($key, $value);
            }
        }
    }
    
    /**
     * WooCommerce missing notice
     */
    public function woocommerce_missing_notice() {
        ?>
        <div class="error">
            <p><?php _e('Jewellery Price Calculator requires WooCommerce to be installed and active.', 'jewellery-price-calc'); ?></p>
        </div>
        <?php
    }
}

// Initialize plugin
function JPC() {
    return Jewellery_Price_Calculator::get_instance();
}

// Start the plugin
JPC();
