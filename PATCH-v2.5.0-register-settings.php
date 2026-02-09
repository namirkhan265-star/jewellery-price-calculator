<?php
/**
 * PATCH v2.5.0: Updated register_settings() function
 * 
 * INSTRUCTIONS:
 * 1. Open includes/class-jpc-admin.php
 * 2. Find the register_settings() function (around line 374)
 * 3. Replace the ENTIRE function with the code below
 */

/**
 * Register settings
 */
public function register_settings() {
    // General settings
    register_setting('jpc_general_settings', 'jpc_enable_pearl_cost');
    register_setting('jpc_general_settings', 'jpc_pearl_cost_label'); // NEW v2.5.0
    register_setting('jpc_general_settings', 'jpc_pearl_cost_type'); // NEW v2.5.0
    
    register_setting('jpc_general_settings', 'jpc_enable_stone_cost');
    register_setting('jpc_general_settings', 'jpc_stone_cost_label'); // NEW v2.5.0
    register_setting('jpc_general_settings', 'jpc_stone_cost_type'); // NEW v2.5.0
    
    register_setting('jpc_general_settings', 'jpc_enable_extra_fee');
    register_setting('jpc_general_settings', 'jpc_extra_fee_label'); // NEW v2.5.0
    register_setting('jpc_general_settings', 'jpc_extra_fee_type'); // NEW v2.5.0
    
    register_setting('jpc_general_settings', 'jpc_additional_percentage_label');
    register_setting('jpc_general_settings', 'jpc_additional_percentage_value');
    register_setting('jpc_general_settings', 'jpc_enable_gst');
    register_setting('jpc_general_settings', 'jpc_gst_label');
    register_setting('jpc_general_settings', 'jpc_gst_value');
    register_setting('jpc_general_settings', 'jpc_gst_gold');
    register_setting('jpc_general_settings', 'jpc_gst_silver');
    register_setting('jpc_general_settings', 'jpc_gst_diamond');
    register_setting('jpc_general_settings', 'jpc_gst_platinum');
    register_setting('jpc_general_settings', 'jpc_price_rounding');
    register_setting('jpc_general_settings', 'jpc_show_price_breakup');
    
    // Extra fields
    for ($i = 1; $i <= 5; $i++) {
        register_setting('jpc_general_settings', 'jpc_enable_extra_field_' . $i);
        register_setting('jpc_general_settings', 'jpc_extra_field_label_' . $i);
    }
    
    // Discount settings - ENHANCED
    register_setting('jpc_discount_settings', 'jpc_enable_discount');
    register_setting('jpc_discount_settings', 'jpc_discount_calculation_method');
    register_setting('jpc_discount_settings', 'jpc_discount_timing');
    register_setting('jpc_discount_settings', 'jpc_gst_calculation_base');
    register_setting('jpc_discount_settings', 'jpc_discount_on_metals');
    register_setting('jpc_discount_settings', 'jpc_discount_on_making');
    register_setting('jpc_discount_settings', 'jpc_discount_on_wastage');
}
