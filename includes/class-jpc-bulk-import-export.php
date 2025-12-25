<?php
/**
 * Bulk Import/Export Handler
 * Adds jewellery calculator fields to WooCommerce CSV import/export
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
        // Export hooks
        add_filter('woocommerce_product_export_column_names', array($this, 'add_export_columns'));
        add_filter('woocommerce_product_export_product_default_columns', array($this, 'add_export_columns'));
        add_filter('woocommerce_product_export_product_column_jpc_metal_id', array($this, 'export_metal_id'), 10, 2);
        add_filter('woocommerce_product_export_product_column_jpc_metal_weight', array($this, 'export_metal_weight'), 10, 2);
        add_filter('woocommerce_product_export_product_column_jpc_diamond_id', array($this, 'export_diamond_id'), 10, 2);
        add_filter('woocommerce_product_export_product_column_jpc_diamond_quantity', array($this, 'export_diamond_quantity'), 10, 2);
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
     * Add export columns
     */
    public function add_export_columns($columns) {
        $columns['jpc_metal_id'] = 'JPC Metal ID';
        $columns['jpc_metal_weight'] = 'JPC Metal Weight (grams)';
        $columns['jpc_diamond_id'] = 'JPC Diamond ID';
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
     * Export diamond ID
     */
    public function export_diamond_id($value, $product) {
        return get_post_meta($product->get_id(), '_jpc_diamond_id', true);
    }
    
    /**
     * Export diamond quantity
     */
    public function export_diamond_quantity($value, $product) {
        return get_post_meta($product->get_id(), '_jpc_diamond_quantity', true);
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
        $columns['jpc_diamond_id'] = 'JPC Diamond ID';
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
        $columns['JPC Diamond ID'] = 'jpc_diamond_id';
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
        // Store JPC data temporarily
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
        
        if (isset($parsed_data['jpc_diamond_id'])) {
            $parsed_data['meta_data'][] = array(
                'key' => '_jpc_diamond_id',
                'value' => $parsed_data['jpc_diamond_id']
            );
        }
        
        if (isset($parsed_data['jpc_diamond_quantity'])) {
            $parsed_data['meta_data'][] = array(
                'key' => '_jpc_diamond_quantity',
                'value' => $parsed_data['jpc_diamond_quantity']
            );
        }
        
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
     */
    public function save_import_data($product, $data) {
        $product_id = $product->get_id();
        
        // Check if any JPC fields were imported
        $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
        
        if ($metal_id) {
            // Recalculate price using the calculator
            JPC_Price_Calculator::calculate_product_price($product_id);
        }
    }
}
