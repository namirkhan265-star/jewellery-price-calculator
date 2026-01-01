<?php
/**
 * Bulk Import/Export Handler
 * Adds jewellery calculator fields to WooCommerce CSV import/export
 * Supports BOTH legacy diamond system (diamond_id) and new flexible system (type/carat/cert)
 */

if (!defined('ABSPATH')) {
    exit;
}

class JPC_Bulk_Import_Export {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Export hooks - FIXED: Use correct WooCommerce filter
        add_filter('woocommerce_product_export_product_default_columns', array($this, 'add_export_columns'));
        
        // Metal exports
        add_filter('woocommerce_product_export_product_column_jpc_metal_id', array($this, 'export_metal_id'), 10, 2);
        add_filter('woocommerce_product_export_product_column_jpc_metal_weight', array($this, 'export_metal_weight'), 10, 2);
        
        // Diamond exports - BOTH SYSTEMS
        add_filter('woocommerce_product_export_product_column_jpc_diamond_id', array($this, 'export_diamond_id'), 10, 2);
        add_filter('woocommerce_product_export_product_column_jpc_diamond_name', array($this, 'export_diamond_name'), 10, 2);
        add_filter('woocommerce_product_export_product_column_jpc_diamond_group', array($this, 'export_diamond_group'), 10, 2);
        add_filter('woocommerce_product_export_product_column_jpc_diamond_type', array($this, 'export_diamond_type'), 10, 2);
        add_filter('woocommerce_product_export_product_column_jpc_diamond_carat', array($this, 'export_diamond_carat'), 10, 2);
        add_filter('woocommerce_product_export_product_column_jpc_diamond_certification', array($this, 'export_diamond_certification'), 10, 2);
        add_filter('woocommerce_product_export_product_column_jpc_diamond_quantity', array($this, 'export_diamond_quantity'), 10, 2);
        
        // Other exports
        add_filter('woocommerce_product_export_product_column_jpc_making_charge', array($this, 'export_making_charge'), 10, 2);
        add_filter('woocommerce_product_export_product_column_jpc_making_charge_type', array($this, 'export_making_charge_type'), 10, 2);
        add_filter('woocommerce_product_export_product_column_jpc_wastage_charge', array($this, 'export_wastage_charge'), 10, 2);
        add_filter('woocommerce_product_export_product_column_jpc_wastage_charge_type', array($this, 'export_wastage_charge_type'), 10, 2);
        add_filter('woocommerce_product_export_product_column_jpc_pearl_cost', array($this, 'export_pearl_cost'), 10, 2);
        add_filter('woocommerce_product_export_product_column_jpc_stone_cost', array($this, 'export_stone_cost'), 10, 2);
        add_filter('woocommerce_product_export_product_column_jpc_extra_fee', array($this, 'export_extra_fee'), 10, 2);
        add_filter('woocommerce_product_export_product_column_jpc_discount_percentage', array($this, 'export_discount_percentage'), 10, 2);
        
        // Import hooks
        add_filter('woocommerce_csv_product_import_mapping_options', array($this, 'add_import_columns'));
        add_filter('woocommerce_csv_product_import_mapping_default_columns', array($this, 'add_import_column_mapping'));
        add_filter('woocommerce_product_importer_parsed_data', array($this, 'parse_import_data'), 10, 2);
        add_action('woocommerce_product_import_inserted_product_object', array($this, 'save_import_data'), 10, 2);
    }
    
    /**
     * Add export columns - FIXED: Using correct filter
     */
    public function add_export_columns($columns) {
        // Metal columns
        $columns['jpc_metal_id'] = 'JPC Metal ID';
        $columns['jpc_metal_weight'] = 'JPC Metal Weight (grams)';
        
        // Diamond columns - BOTH systems with all details
        $columns['jpc_diamond_id'] = 'JPC Diamond ID';
        $columns['jpc_diamond_name'] = 'JPC Diamond Name';
        $columns['jpc_diamond_group'] = 'JPC Diamond Group';
        $columns['jpc_diamond_type'] = 'JPC Diamond Type';
        $columns['jpc_diamond_carat'] = 'JPC Diamond Carat';
        $columns['jpc_diamond_certification'] = 'JPC Diamond Certification';
        $columns['jpc_diamond_quantity'] = 'JPC Diamond Quantity';
        
        // Other columns
        $columns['jpc_making_charge'] = 'JPC Making Charge';
        $columns['jpc_making_charge_type'] = 'JPC Making Charge Type';
        $columns['jpc_wastage_charge'] = 'JPC Wastage Charge';
        $columns['jpc_wastage_charge_type'] = 'JPC Wastage Charge Type';
        $columns['jpc_pearl_cost'] = 'JPC Pearl Cost';
        $columns['jpc_stone_cost'] = 'JPC Stone Cost';
        $columns['jpc_extra_fee'] = 'JPC Extra Fee';
        $columns['jpc_discount_percentage'] = 'JPC Discount Percentage';
        
        return $columns;
    }
    
    /**
     * Export metal ID
     */
    public function export_metal_id($value, $product) {
        return get_post_meta($product->get_id(), '_jpc_metal_id', true);
    }
    
    /**
     * Export metal weight
     */
    public function export_metal_weight($value, $product) {
        return get_post_meta($product->get_id(), '_jpc_metal_weight', true);
    }
    
    /**
     * Export diamond ID (legacy system)
     */
    public function export_diamond_id($value, $product) {
        return get_post_meta($product->get_id(), '_jpc_diamond_id', true);
    }
    
    /**
     * Export diamond name (from legacy diamond table)
     */
    public function export_diamond_name($value, $product) {
        $diamond_id = get_post_meta($product->get_id(), '_jpc_diamond_id', true);
        if ($diamond_id) {
            global $wpdb;
            $table = $wpdb->prefix . 'jpc_diamonds';
            $diamond = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$table` WHERE id = %d", $diamond_id));
            if ($diamond) {
                return $diamond->display_name;
            }
        }
        return '';
    }
    
    /**
     * Export diamond group (from legacy diamond table)
     */
    public function export_diamond_group($value, $product) {
        $diamond_id = get_post_meta($product->get_id(), '_jpc_diamond_id', true);
        if ($diamond_id) {
            global $wpdb;
            $table = $wpdb->prefix . 'jpc_diamonds';
            $diamond = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$table` WHERE id = %d", $diamond_id));
            if ($diamond && !empty($diamond->group_name)) {
                return $diamond->group_name;
            }
        }
        return '';
    }
    
    /**
     * Export diamond type (natural, lab_grown, moissanite)
     */
    public function export_diamond_type($value, $product) {
        // First check if there's a direct type stored
        $type = get_post_meta($product->get_id(), '_jpc_diamond_type', true);
        if ($type) {
            return $type;
        }
        
        // Try to get from legacy diamond
        $diamond_id = get_post_meta($product->get_id(), '_jpc_diamond_id', true);
        if ($diamond_id) {
            global $wpdb;
            $table = $wpdb->prefix . 'jpc_diamonds';
            $diamond = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$table` WHERE id = %d", $diamond_id));
            if ($diamond && !empty($diamond->type_name)) {
                return $diamond->type_name;
            }
        }
        
        return '';
    }
    
    /**
     * Export diamond carat
     */
    public function export_diamond_carat($value, $product) {
        // First check if there's a direct carat stored
        $carat = get_post_meta($product->get_id(), '_jpc_diamond_carat', true);
        if ($carat) {
            return $carat;
        }
        
        // Try to get from legacy diamond
        $diamond_id = get_post_meta($product->get_id(), '_jpc_diamond_id', true);
        if ($diamond_id) {
            global $wpdb;
            $table = $wpdb->prefix . 'jpc_diamonds';
            $diamond = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$table` WHERE id = %d", $diamond_id));
            if ($diamond && !empty($diamond->carat)) {
                return $diamond->carat;
            }
        }
        
        return '';
    }
    
    /**
     * Export diamond certification
     */
    public function export_diamond_certification($value, $product) {
        // First check if there's a direct certification stored
        $cert = get_post_meta($product->get_id(), '_jpc_diamond_certification', true);
        if ($cert) {
            return $cert;
        }
        
        // Try to get from legacy diamond
        $diamond_id = get_post_meta($product->get_id(), '_jpc_diamond_id', true);
        if ($diamond_id) {
            global $wpdb;
            $table = $wpdb->prefix . 'jpc_diamonds';
            $diamond = $wpdb->get_row($wpdb->prepare("SELECT * FROM `$table` WHERE id = %d", $diamond_id));
            if ($diamond && !empty($diamond->certification_name)) {
                return $diamond->certification_name;
            }
        }
        
        return '';
    }
    
    /**
     * Export diamond quantity
     */
    public function export_diamond_quantity($value, $product) {
        $qty = get_post_meta($product->get_id(), '_jpc_diamond_quantity', true);
        return $qty ? $qty : '';
    }
    
    /**
     * Export making charge
     */
    public function export_making_charge($value, $product) {
        return get_post_meta($product->get_id(), '_jpc_making_charge', true);
    }
    
    /**
     * Export making charge type
     */
    public function export_making_charge_type($value, $product) {
        return get_post_meta($product->get_id(), '_jpc_making_charge_type', true);
    }
    
    /**
     * Export wastage charge
     */
    public function export_wastage_charge($value, $product) {
        return get_post_meta($product->get_id(), '_jpc_wastage_charge', true);
    }
    
    /**
     * Export wastage charge type
     */
    public function export_wastage_charge_type($value, $product) {
        return get_post_meta($product->get_id(), '_jpc_wastage_charge_type', true);
    }
    
    /**
     * Export pearl cost
     */
    public function export_pearl_cost($value, $product) {
        return get_post_meta($product->get_id(), '_jpc_pearl_cost', true);
    }
    
    /**
     * Export stone cost
     */
    public function export_stone_cost($value, $product) {
        return get_post_meta($product->get_id(), '_jpc_stone_cost', true);
    }
    
    /**
     * Export extra fee
     */
    public function export_extra_fee($value, $product) {
        return get_post_meta($product->get_id(), '_jpc_extra_fee', true);
    }
    
    /**
     * Export discount percentage
     */
    public function export_discount_percentage($value, $product) {
        return get_post_meta($product->get_id(), '_jpc_discount_percentage', true);
    }
    
    /**
     * Add import columns
     */
    public function add_import_columns($columns) {
        $columns['jpc_metal_id'] = 'JPC Metal ID';
        $columns['jpc_metal_weight'] = 'JPC Metal Weight (grams)';
        
        // BOTH diamond systems
        $columns['jpc_diamond_id'] = 'JPC Diamond ID';
        $columns['jpc_diamond_group'] = 'JPC Diamond Group';
        $columns['jpc_diamond_type'] = 'JPC Diamond Type';
        $columns['jpc_diamond_carat'] = 'JPC Diamond Carat';
        $columns['jpc_diamond_certification'] = 'JPC Diamond Certification';
        $columns['jpc_diamond_quantity'] = 'JPC Diamond Quantity';
        
        $columns['jpc_making_charge'] = 'JPC Making Charge';
        $columns['jpc_making_charge_type'] = 'JPC Making Charge Type';
        $columns['jpc_wastage_charge'] = 'JPC Wastage Charge';
        $columns['jpc_wastage_charge_type'] = 'JPC Wastage Charge Type';
        $columns['jpc_pearl_cost'] = 'JPC Pearl Cost';
        $columns['jpc_stone_cost'] = 'JPC Stone Cost';
        $columns['jpc_extra_fee'] = 'JPC Extra Fee';
        $columns['jpc_discount_percentage'] = 'JPC Discount Percentage';
        
        return $columns;
    }
    
    /**
     * Add import column mapping
     */
    public function add_import_column_mapping($columns) {
        $columns['JPC Metal ID'] = 'jpc_metal_id';
        $columns['JPC Metal Weight (grams)'] = 'jpc_metal_weight';
        
        // BOTH diamond systems
        $columns['JPC Diamond ID'] = 'jpc_diamond_id';
        $columns['JPC Diamond Group'] = 'jpc_diamond_group';
        $columns['JPC Diamond Type'] = 'jpc_diamond_type';
        $columns['JPC Diamond Carat'] = 'jpc_diamond_carat';
        $columns['JPC Diamond Certification'] = 'jpc_diamond_certification';
        $columns['JPC Diamond Quantity'] = 'jpc_diamond_quantity';
        
        $columns['JPC Making Charge'] = 'jpc_making_charge';
        $columns['JPC Making Charge Type'] = 'jpc_making_charge_type';
        $columns['JPC Wastage Charge'] = 'jpc_wastage_charge';
        $columns['JPC Wastage Charge Type'] = 'jpc_wastage_charge_type';
        $columns['JPC Pearl Cost'] = 'jpc_pearl_cost';
        $columns['JPC Stone Cost'] = 'jpc_stone_cost';
        $columns['JPC Extra Fee'] = 'jpc_extra_fee';
        $columns['JPC Discount Percentage'] = 'jpc_discount_percentage';
        
        return $columns;
    }
    
    /**
     * Parse import data
     */
    public function parse_import_data($parsed_data, $importer) {
        // Metal data
        if (isset($parsed_data['jpc_metal_id'])) {
            $parsed_data['meta_data'][] = array(
                'key' => '_jpc_metal_id',
                'value' => $parsed_data['jpc_metal_id']
            );
        }
        
        if (isset($parsed_data['jpc_metal_weight'])) {
            $parsed_data['meta_data'][] = array(
                'key' => '_jpc_metal_weight',
                'value' => $parsed_data['jpc_metal_weight']
            );
        }
        
        // Diamond ID (legacy system) - takes priority
        if (isset($parsed_data['jpc_diamond_id']) && !empty($parsed_data['jpc_diamond_id'])) {
            $parsed_data['meta_data'][] = array(
                'key' => '_jpc_diamond_id',
                'value' => intval($parsed_data['jpc_diamond_id'])
            );
        }
        
        // NEW: Flexible diamond data (only if no diamond_id provided)
        if (isset($parsed_data['jpc_diamond_type']) && !isset($parsed_data['jpc_diamond_id'])) {
            $parsed_data['meta_data'][] = array(
                'key' => '_jpc_diamond_type',
                'value' => sanitize_text_field($parsed_data['jpc_diamond_type'])
            );
        }
        
        if (isset($parsed_data['jpc_diamond_carat'])) {
            $parsed_data['meta_data'][] = array(
                'key' => '_jpc_diamond_carat',
                'value' => floatval($parsed_data['jpc_diamond_carat'])
            );
        }
        
        if (isset($parsed_data['jpc_diamond_certification'])) {
            $parsed_data['meta_data'][] = array(
                'key' => '_jpc_diamond_certification',
                'value' => sanitize_text_field($parsed_data['jpc_diamond_certification'])
            );
        }
        
        if (isset($parsed_data['jpc_diamond_quantity'])) {
            $parsed_data['meta_data'][] = array(
                'key' => '_jpc_diamond_quantity',
                'value' => intval($parsed_data['jpc_diamond_quantity'])
            );
        }
        
        // Other fields
        if (isset($parsed_data['jpc_making_charge'])) {
            $parsed_data['meta_data'][] = array(
                'key' => '_jpc_making_charge',
                'value' => $parsed_data['jpc_making_charge']
            );
        }
        
        if (isset($parsed_data['jpc_making_charge_type'])) {
            $parsed_data['meta_data'][] = array(
                'key' => '_jpc_making_charge_type',
                'value' => $parsed_data['jpc_making_charge_type']
            );
        }
        
        if (isset($parsed_data['jpc_wastage_charge'])) {
            $parsed_data['meta_data'][] = array(
                'key' => '_jpc_wastage_charge',
                'value' => $parsed_data['jpc_wastage_charge']
            );
        }
        
        if (isset($parsed_data['jpc_wastage_charge_type'])) {
            $parsed_data['meta_data'][] = array(
                'key' => '_jpc_wastage_charge_type',
                'value' => $parsed_data['jpc_wastage_charge_type']
            );
        }
        
        if (isset($parsed_data['jpc_pearl_cost'])) {
            $parsed_data['meta_data'][] = array(
                'key' => '_jpc_pearl_cost',
                'value' => $parsed_data['jpc_pearl_cost']
            );
        }
        
        if (isset($parsed_data['jpc_stone_cost'])) {
            $parsed_data['meta_data'][] = array(
                'key' => '_jpc_stone_cost',
                'value' => $parsed_data['jpc_stone_cost']
            );
        }
        
        if (isset($parsed_data['jpc_extra_fee'])) {
            $parsed_data['meta_data'][] = array(
                'key' => '_jpc_extra_fee',
                'value' => $parsed_data['jpc_extra_fee']
            );
        }
        
        if (isset($parsed_data['jpc_discount_percentage'])) {
            $parsed_data['meta_data'][] = array(
                'key' => '_jpc_discount_percentage',
                'value' => $parsed_data['jpc_discount_percentage']
            );
        }
        
        return $parsed_data;
    }
    
    /**
     * Save import data and recalculate price
     * Handles BOTH legacy diamond_id and flexible diamond data
     */
    public function save_import_data($product, $data) {
        $product_id = $product->get_id();
        
        // Check if we have flexible diamond data (and no diamond_id)
        $diamond_id = get_post_meta($product_id, '_jpc_diamond_id', true);
        
        if (!$diamond_id) {
            $diamond_type = get_post_meta($product_id, '_jpc_diamond_type', true);
            $diamond_carat = get_post_meta($product_id, '_jpc_diamond_carat', true);
            $diamond_cert = get_post_meta($product_id, '_jpc_diamond_certification', true);
            
            // If we have diamond data, get or create diamond entry
            if ($diamond_type && $diamond_carat) {
                $diamond_id = JPC_Diamond_Pricing::get_or_create_diamond(
                    $diamond_type,
                    floatval($diamond_carat),
                    $diamond_cert ? $diamond_cert : 'none'
                );
                
                // Save diamond ID for backward compatibility
                update_post_meta($product_id, '_jpc_diamond_id', $diamond_id);
            }
        }
        
        // Check if any JPC fields were imported
        $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
        
        if ($metal_id) {
            // Recalculate price using the calculator
            JPC_Price_Calculator::calculate_and_update_price($product_id);
        }
    }
}
