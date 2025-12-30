<?php
/**
 * Admin functionality
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
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    public function add_admin_menu() {
        add_menu_page(
            __('Jewellery Price Calculator', 'jewellery-price-calc'),
            __('Jewellery Price Calc', 'jewellery-price-calc'),
            'manage_options',
            'jewellery-price-calc',
            array($this, 'settings_page'),
            'dashicons-calculator',
            56
        );
        
        add_submenu_page(
            'jewellery-price-calc',
            __('Settings', 'jewellery-price-calc'),
            __('Settings', 'jewellery-price-calc'),
            'manage_options',
            'jewellery-price-calc',
            array($this, 'settings_page')
        );
        
        add_submenu_page(
            'jewellery-price-calc',
            __('Metal Rates', 'jewellery-price-calc'),
            __('Metal Rates', 'jewellery-price-calc'),
            'manage_options',
            'jpc-metals',
            array('JPC_Metal_Admin', 'render_page')
        );
        
        add_submenu_page(
            'jewellery-price-calc',
            __('Diamond Rates', 'jewellery-price-calc'),
            __('Diamond Rates', 'jewellery-price-calc'),
            'manage_options',
            'jpc-diamonds',
            array('JPC_Diamond_Admin', 'render_page')
        );
        
        add_submenu_page(
            'jewellery-price-calc',
            __('Diamond Groups', 'jewellery-price-calc'),
            __('Diamond Groups', 'jewellery-price-calc'),
            'manage_options',
            'jpc-diamond-groups',
            array('JPC_Diamond_Group_Admin', 'render_page')
        );
    }
    
    public function register_settings() {
        register_setting('jpc_settings', 'jpc_gst_enabled');
        register_setting('jpc_settings', 'jpc_gst_percentage');
        register_setting('jpc_settings', 'jpc_gst_label');
        register_setting('jpc_settings', 'jpc_currency_symbol');
        register_setting('jpc_settings', 'jpc_price_display');
    }
    
    public function settings_page() {
        ?>
        <div class="wrap jpc-admin-wrap">
            <h1><?php _e('Jewellery Price Calculator Settings', 'jewellery-price-calc'); ?></h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('jpc_settings');
                do_settings_sections('jpc_settings');
                ?>
                
                <div class="jpc-card">
                    <h2><?php _e('General Settings', 'jewellery-price-calc'); ?></h2>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="jpc_currency_symbol"><?php _e('Currency Symbol', 'jewellery-price-calc'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="jpc_currency_symbol" name="jpc_currency_symbol" 
                                       value="<?php echo esc_attr(get_option('jpc_currency_symbol', '₹')); ?>" 
                                       class="regular-text" />
                                <p class="description"><?php _e('Symbol to display for prices (e.g., ₹, $, €)', 'jewellery-price-calc'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="jpc_price_display"><?php _e('Price Display', 'jewellery-price-calc'); ?></label>
                            </th>
                            <td>
                                <select id="jpc_price_display" name="jpc_price_display">
                                    <option value="both" <?php selected(get_option('jpc_price_display', 'both'), 'both'); ?>>
                                        <?php _e('Show both calculated and manual prices', 'jewellery-price-calc'); ?>
                                    </option>
                                    <option value="calculated" <?php selected(get_option('jpc_price_display'), 'calculated'); ?>>
                                        <?php _e('Show only calculated price', 'jewellery-price-calc'); ?>
                                    </option>
                                    <option value="manual" <?php selected(get_option('jpc_price_display'), 'manual'); ?>>
                                        <?php _e('Show only manual price', 'jewellery-price-calc'); ?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="jpc-card">
                    <h2><?php _e('GST Settings', 'jewellery-price-calc'); ?></h2>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="jpc_gst_enabled"><?php _e('Enable GST', 'jewellery-price-calc'); ?></label>
                            </th>
                            <td>
                                <input type="checkbox" id="jpc_gst_enabled" name="jpc_gst_enabled" value="yes" 
                                       <?php checked(get_option('jpc_gst_enabled', 'yes'), 'yes'); ?> />
                                <label for="jpc_gst_enabled"><?php _e('Add GST to calculated prices', 'jewellery-price-calc'); ?></label>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="jpc_gst_percentage"><?php _e('GST Percentage', 'jewellery-price-calc'); ?></label>
                            </th>
                            <td>
                                <input type="number" id="jpc_gst_percentage" name="jpc_gst_percentage" 
                                       value="<?php echo esc_attr(get_option('jpc_gst_percentage', '3')); ?>" 
                                       min="0" max="100" step="0.01" class="small-text" />
                                <span>%</span>
                                <p class="description"><?php _e('GST percentage to apply', 'jewellery-price-calc'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="jpc_gst_label"><?php _e('GST Label', 'jewellery-price-calc'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="jpc_gst_label" name="jpc_gst_label" 
                                       value="<?php echo esc_attr(get_option('jpc_gst_label', 'GST')); ?>" 
                                       class="regular-text" />
                                <p class="description"><?php _e('Label to display for GST (e.g., GST, VAT, Tax)', 'jewellery-price-calc'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
