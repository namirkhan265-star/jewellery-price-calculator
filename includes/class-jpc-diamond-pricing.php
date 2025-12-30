<?php
/**
 * Diamond Pricing Engine
 * Handles automatic diamond price calculation based on type, carat, and certification
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Diamond_Pricing {
    
    private static $instance = null;
    
    /**
     * Base prices per carat for different diamond types (in your currency)
     * These are starting prices that get adjusted by carat weight and certification
     */
    private static $base_prices = array(
        'natural' => 25000,      // Natural Diamond base price per carat
        'lab_grown' => 15000,    // Lab Grown Diamond base price per carat
        'moissanite' => 5000,    // Moissanite base price per carat
    );
    
    /**
     * Certification multipliers
     * Higher certification = higher price
     */
    private static $cert_multipliers = array(
        'gia' => 1.20,    // GIA certified: +20%
        'igi' => 1.15,    // IGI certified: +15%
        'hrd' => 1.18,    // HRD certified: +18%
        'none' => 1.00,   // No certification: base price
    );
    
    /**
     * Carat weight multipliers
     * Larger diamonds are exponentially more expensive per carat
     */
    private static $carat_multipliers = array(
        array('min' => 0.00, 'max' => 0.50, 'multiplier' => 1.00),   // 0-0.5ct: base price
        array('min' => 0.50, 'max' => 1.00, 'multiplier' => 1.30),   // 0.5-1ct: +30%
        array('min' => 1.00, 'max' => 2.00, 'multiplier' => 1.80),   // 1-2ct: +80%
        array('min' => 2.00, 'max' => 3.00, 'multiplier' => 2.50),   // 2-3ct: +150%
        array('min' => 3.00, 'max' => 5.00, 'multiplier' => 3.50),   // 3-5ct: +250%
        array('min' => 5.00, 'max' => 999.99, 'multiplier' => 5.00), // 5ct+: +400%
    );
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Calculate price per carat based on type, carat weight, and certification
     * 
     * @param string $type Diamond type (natural, lab_grown, moissanite)
     * @param float $carat Carat weight
     * @param string $certification Certification type (gia, igi, hrd, none)
     * @return float Price per carat
     */
    public static function calculate_price_per_carat($type, $carat, $certification = 'none') {
        // Get base price for diamond type
        $base_price = isset(self::$base_prices[$type]) ? self::$base_prices[$type] : self::$base_prices['natural'];
        
        // Get certification multiplier
        $cert_multiplier = isset(self::$cert_multipliers[$certification]) ? self::$cert_multipliers[$certification] : 1.00;
        
        // Get carat weight multiplier
        $carat_multiplier = self::get_carat_multiplier($carat);
        
        // Calculate final price per carat
        $price_per_carat = $base_price * $carat_multiplier * $cert_multiplier;
        
        return round($price_per_carat, 2);
    }
    
    /**
     * Get carat weight multiplier based on carat size
     */
    private static function get_carat_multiplier($carat) {
        foreach (self::$carat_multipliers as $range) {
            if ($carat >= $range['min'] && $carat < $range['max']) {
                return $range['multiplier'];
            }
        }
        return 1.00; // Default multiplier
    }
    
    /**
     * Calculate total diamond price
     * 
     * @param string $type Diamond type
     * @param float $carat Carat weight per diamond
     * @param int $quantity Number of diamonds
     * @param string $certification Certification type
     * @return array Array with price_per_carat, unit_price, and total_price
     */
    public static function calculate_total_price($type, $carat, $quantity, $certification = 'none') {
        $price_per_carat = self::calculate_price_per_carat($type, $carat, $certification);
        $unit_price = $price_per_carat * $carat; // Price for one diamond
        $total_price = $unit_price * $quantity;   // Total price for all diamonds
        
        return array(
            'price_per_carat' => $price_per_carat,
            'unit_price' => $unit_price,
            'total_price' => $total_price,
            'total_carat' => $carat * $quantity,
        );
    }
    
    /**
     * Get or create diamond entry
     * If diamond with same specs exists, return it
     * If not, create new entry
     * 
     * @param string $type Diamond type
     * @param float $carat Carat weight
     * @param string $certification Certification type
     * @return int Diamond ID
     */
    public static function get_or_create_diamond($type, $carat, $certification = 'none') {
        global $wpdb;
        $table = $wpdb->prefix . 'jpc_diamonds';
        
        // Check if diamond already exists
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM `$table` WHERE type = %s AND carat = %f AND certification = %s",
            $type,
            $carat,
            $certification
        ));
        
        if ($existing) {
            return $existing->id;
        }
        
        // Calculate price
        $price_per_carat = self::calculate_price_per_carat($type, $carat, $certification);
        
        // Create display name
        $types = JPC_Diamonds::get_types();
        $certs = JPC_Diamonds::get_certifications();
        $type_label = isset($types[$type]) ? $types[$type] : ucfirst($type);
        $cert_label = isset($certs[$certification]) ? $certs[$certification] : 'No Cert';
        $display_name = sprintf('%s - %.2fct - %s', $type_label, $carat, $cert_label);
        
        // Insert new diamond
        $wpdb->insert(
            $table,
            array(
                'type' => $type,
                'carat' => $carat,
                'certification' => $certification,
                'price_per_carat' => $price_per_carat,
                'display_name' => $display_name,
            ),
            array('%s', '%f', '%s', '%f', '%s')
        );
        
        return $wpdb->insert_id;
    }
    
    /**
     * Update base prices (admin can customize)
     */
    public static function update_base_prices($prices) {
        update_option('jpc_diamond_base_prices', $prices);
        self::$base_prices = $prices;
    }
    
    /**
     * Get base prices
     */
    public static function get_base_prices() {
        $saved_prices = get_option('jpc_diamond_base_prices');
        return $saved_prices ? $saved_prices : self::$base_prices;
    }
    
    /**
     * Update certification multipliers
     */
    public static function update_cert_multipliers($multipliers) {
        update_option('jpc_diamond_cert_multipliers', $multipliers);
        self::$cert_multipliers = $multipliers;
    }
    
    /**
     * Get certification multipliers
     */
    public static function get_cert_multipliers() {
        $saved_multipliers = get_option('jpc_diamond_cert_multipliers');
        return $saved_multipliers ? $saved_multipliers : self::$cert_multipliers;
    }
    
    /**
     * Update carat multipliers
     */
    public static function update_carat_multipliers($multipliers) {
        update_option('jpc_diamond_carat_multipliers', $multipliers);
        self::$carat_multipliers = $multipliers;
    }
    
    /**
     * Get carat multipliers
     */
    public static function get_carat_multipliers() {
        $saved_multipliers = get_option('jpc_diamond_carat_multipliers');
        return $saved_multipliers ? $saved_multipliers : self::$carat_multipliers;
    }
}
