<?php
/**
 * Plugin Name: Jewellery Price Calculator
 * Plugin URI: https://brandwitty.com
 * Description: Automatic jewellery price calculation based on metal rates (Gold, Silver, Diamond, Platinum) with support for making charges, wastage, GST, and discounts
 * Version: 1.5.0
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
define('JPC_VERSION', '1.5.0');
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
        if (!$this->is_woocommerce_active()) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }
        
        // Load plugin files
        $this->load_dependencies();
        
        // Initialize plugin
        $this->init();
    }
    
    /**
     * Check if WooCommerce is active
     */
    private function is_woocommerce_active() {
        return in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')));
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
    
    /**
     * Load plugin dependencies
     */
    private function load_dependencies() {
        // Core classes
        require_once JPC_PLUGIN_DIR . 'includes/class-jpc-database.php';
        require_once JPC_PLUGIN_DIR . 'includes/class-jpc-metal.php';
        require_once JPC_PLUGIN_DIR . 'includes/class-jpc-diamond.php';
        require_once JPC_PLUGIN_DIR . 'includes/class-jpc-diamond-group.php';
        require_once JPC_PLUGIN_DIR . 'includes/class-jpc-calculator.php';
        require_once JPC_PLUGIN_DIR . 'includes/class-jpc-product-meta.php';
        require_once JPC_PLUGIN_DIR . 'includes/class-jpc-settings.php';
        require_once JPC_PLUGIN_DIR . 'includes/class-jpc-frontend.php';
        require_once JPC_PLUGIN_DIR . 'includes/class-jpc-price-history.php';
        
        // Admin classes
        if (is_admin()) {
            require_once JPC_PLUGIN_DIR . 'admin/class-jpc-admin.php';
            require_once JPC_PLUGIN_DIR . 'admin/class-jpc-metal-admin.php';
            require_once JPC_PLUGIN_DIR . 'admin/class-jpc-diamond-admin.php';
            require_once JPC_PLUGIN_DIR . 'admin/class-jpc-diamond-group-admin.php';
        }
    }
    
    /**
     * Initialize plugin
     */
    private function init() {
        // Register activation/deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Initialize classes
        add_action('plugins_loaded', array($this, 'init_classes'));
        
        // Load text domain
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        
        // Enqueue scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'frontend_enqueue_scripts'));
    }
    
    /**
     * Initialize plugin classes
     */
    public function init_classes() {
        JPC_Metal::get_instance();
        JPC_Diamond::get_instance();
        JPC_Diamond_Group::get_instance();
        JPC_Calculator::get_instance();
        JPC_Product_Meta::get_instance();
        JPC_Settings::get_instance();
        JPC_Frontend::get_instance();
        JPC_Price_History::get_instance();
        
        if (is_admin()) {
            JPC_Admin::get_instance();
            JPC_Metal_Admin::get_instance();
            JPC_Diamond_Admin::get_instance();
            JPC_Diamond_Group_Admin::get_instance();
        }
    }
    
    /**
     * Load plugin text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain('jewellery-price-calc', false, dirname(JPC_PLUGIN_BASENAME) . '/languages');
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function admin_enqueue_scripts($hook) {
        // Only load on plugin pages and product edit page
        if (strpos($hook, 'jewellery-price-calc') !== false || $hook === 'post.php' || $hook === 'post-new.php') {
            wp_enqueue_style('jpc-admin', JPC_PLUGIN_URL . 'assets/css/admin.css', array(), JPC_VERSION);
            wp_enqueue_script('jpc-admin', JPC_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), JPC_VERSION, true);
            
            // Enqueue live calculator on product edit page
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
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function frontend_enqueue_scripts() {
        if (is_product()) {
            wp_enqueue_style('jpc-frontend', JPC_PLUGIN_URL . 'assets/css/frontend.css', array(), JPC_VERSION);
        }
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
    }
}

// Initialize plugin
function jewellery_price_calculator() {
    return Jewellery_Price_Calculator::get_instance();
}

// Start the plugin
jewellery_price_calculator();
