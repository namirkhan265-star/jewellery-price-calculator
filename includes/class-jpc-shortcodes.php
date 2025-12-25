<?php
/**
 * Shortcodes Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Shortcodes {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_shortcode('jpc_metal_rates', array($this, 'metal_rates_shortcode'));
        add_shortcode('jpc_metal_rates_marquee', array($this, 'metal_rates_marquee_shortcode'));
        add_shortcode('jpc_metal_rates_table', array($this, 'metal_rates_table_shortcode'));
    }
    
    /**
     * Metal rates shortcode - default display
     */
    public function metal_rates_shortcode($atts) {
        $atts = shortcode_atts(array(
            'template' => 'list',
            'metals' => '', // comma-separated metal IDs
        ), $atts);
        
        $metals = JPC_Metals::get_all();
        
        if (!empty($atts['metals'])) {
            $metal_ids = array_map('intval', explode(',', $atts['metals']));
            $metals = array_filter($metals, function($metal) use ($metal_ids) {
                return in_array($metal->id, $metal_ids);
            });
        }
        
        if (empty($metals)) {
            return '';
        }
        
        ob_start();
        
        if ($atts['template'] === 'marquee') {
            include JPC_PLUGIN_DIR . 'templates/shortcodes/metal-rates-marquee.php';
        } elseif ($atts['template'] === 'table') {
            include JPC_PLUGIN_DIR . 'templates/shortcodes/metal-rates-table.php';
        } else {
            include JPC_PLUGIN_DIR . 'templates/shortcodes/metal-rates-list.php';
        }
        
        return ob_get_clean();
    }
    
    /**
     * Metal rates marquee shortcode
     */
    public function metal_rates_marquee_shortcode($atts) {
        $atts['template'] = 'marquee';
        return $this->metal_rates_shortcode($atts);
    }
    
    /**
     * Metal rates table shortcode
     */
    public function metal_rates_table_shortcode($atts) {
        $atts['template'] = 'table';
        return $this->metal_rates_shortcode($atts);
    }
}
