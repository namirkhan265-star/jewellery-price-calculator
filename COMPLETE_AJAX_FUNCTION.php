<?php
/**
 * COMPLETE AJAX FUNCTION REPLACEMENT
 * 
 * Replace the ajax_calculate_live_price() function in:
 * /includes/class-jpc-product-meta.php
 * 
 * Find the function starting around line 140 and replace it entirely with this code
 */

/**
 * AJAX handler for live price calculation
 */
public function ajax_calculate_live_price() {
    check_ajax_referer('jpc_live_calc_nonce', 'nonce');
    
    $metal_id = intval($_POST['metal_id']);
    $metal_weight = floatval($_POST['metal_weight']);
    $diamond_id = intval($_POST['diamond_id']);
    $diamond_quantity = intval($_POST['diamond_quantity']);
    $making_charge = floatval($_POST['making_charge']);
    $making_charge_type = sanitize_text_field($_POST['making_charge_type']);
    $wastage_charge = floatval($_POST['wastage_charge']);
    $wastage_charge_type = sanitize_text_field($_POST['wastage_charge_type']);
    $pearl_cost = floatval($_POST['pearl_cost']);
    $stone_cost = floatval($_POST['stone_cost']);
    $extra_fee = floatval($_POST['extra_fee']);
    $discount_percentage = floatval($_POST['discount_percentage']);
    
    // Get extra fields #1-5
    $extra_field_1 = isset($_POST['extra_field_1']) ? floatval($_POST['extra_field_1']) : 0;
    $extra_field_2 = isset($_POST['extra_field_2']) ? floatval($_POST['extra_field_2']) : 0;
    $extra_field_3 = isset($_POST['extra_field_3']) ? floatval($_POST['extra_field_3']) : 0;
    $extra_field_4 = isset($_POST['extra_field_4']) ? floatval($_POST['extra_field_4']) : 0;
    $extra_field_5 = isset($_POST['extra_field_5']) ? floatval($_POST['extra_field_5']) : 0;
    $extra_field_costs = $extra_field_1 + $extra_field_2 + $extra_field_3 + $extra_field_4 + $extra_field_5;
    
    if (!$metal_id || !$metal_weight) {
        wp_send_json_error(array('message' => 'Metal and weight are required'));
        return;
    }
    
    $metal = JPC_Metals::get_by_id($metal_id);
    if (!$metal) {
        wp_send_json_error(array('message' => 'Invalid metal'));
        return;
    }
    
    $metal_group = JPC_Metal_Groups::get_by_id($metal->metal_group_id);
    
    // Calculate base metal price
    $metal_price = $metal_weight * $metal->price_per_unit;
    
    // Calculate diamond price
    $diamond_price = 0;
    if ($diamond_id && $diamond_quantity > 0) {
        $diamond = JPC_Diamonds::get_by_id($diamond_id);
        if ($diamond) {
            $diamond_unit_price = $diamond->price_per_carat * $diamond->carat;
            $diamond_price = $diamond_unit_price * $diamond_quantity;
        }
    }
    
    // Calculate making charge
    $making_charge_amount = 0;
    if ($metal_group->enable_making_charge && $making_charge > 0) {
        if ($making_charge_type === 'percentage') {
            $making_charge_amount = ($metal_price * $making_charge) / 100;
        } else {
            $making_charge_amount = $making_charge;
        }
    }
    
    // Calculate wastage charge
    $wastage_charge_amount = 0;
    if ($metal_group->enable_wastage_charge && $wastage_charge > 0) {
        if ($wastage_charge_type === 'percentage') {
            $wastage_charge_amount = ($metal_price * $wastage_charge) / 100;
        } else {
            $wastage_charge_amount = $wastage_charge;
        }
    }
    
    // Calculate subtotal before additional percentage
    $subtotal_before_additional = $metal_price + $diamond_price + $making_charge_amount + $wastage_charge_amount + $pearl_cost + $stone_cost + $extra_fee + $extra_field_costs;
    
    // Apply Additional Percentage (if enabled)
    $additional_percentage_amount = 0;
    $additional_percentage = floatval(get_option('jpc_additional_percentage_value', 0));
    if ($additional_percentage > 0) {
        $additional_percentage_amount = ($subtotal_before_additional * $additional_percentage) / 100;
    }
    $additional_percentage_label = get_option('jpc_additional_percentage_label', 'Additional Percentage');
    
    // Subtotal after additional percentage
    $subtotal_before_discount = $subtotal_before_additional + $additional_percentage_amount;
    
    // Calculate discount based on settings
    $discount_amount = 0;
    $subtotal_after_discount = $subtotal_before_discount;
    
    if ($discount_percentage > 0) {
        // Check which components to apply discount on
        $discount_on_metals = get_option('jpc_discount_on_metals') === 'yes';
        $discount_on_making = get_option('jpc_discount_on_making') === 'yes';
        $discount_on_wastage = get_option('jpc_discount_on_wastage') === 'yes';
        
        // Calculate discountable amount
        $discountable_amount = 0;
        
        if ($discount_on_metals) {
            $discountable_amount += $metal_price;
        }
        
        if ($discount_on_making) {
            $discountable_amount += $making_charge_amount;
        }
        
        if ($discount_on_wastage) {
            $discountable_amount += $wastage_charge_amount;
        }
        
        // If no specific discount options are enabled, apply to entire subtotal (backward compatibility)
        if (!$discount_on_metals && !$discount_on_making && !$discount_on_wastage) {
            $discountable_amount = $subtotal_before_discount;
        }
        
        // Calculate discount
        $discount_amount = ($discountable_amount * $discount_percentage) / 100;
        $subtotal_after_discount = $subtotal_before_discount - $discount_amount;
    }
    
    // Calculate GST
    $gst_amount = 0;
    $gst_percentage = 0;
    $gst_label = get_option('jpc_gst_label', 'GST');
    $gst_enabled = get_option('jpc_enable_gst');
    
    if ($gst_enabled === 'yes' || $gst_enabled === '1' || $gst_enabled === 1 || $gst_enabled === true) {
        $gst_percentage = floatval(get_option('jpc_gst_value', 5));
        
        // Check for metal-specific GST rates
        $metal_group_name = strtolower($metal_group->name);
        $metal_specific_gst = get_option('jpc_gst_' . $metal_group_name);
        
        if ($metal_specific_gst !== false && $metal_specific_gst !== '') {
            $gst_percentage = floatval($metal_specific_gst);
        }
        
        // GST on discounted amount for final price
        $gst_amount = ($subtotal_after_discount * $gst_percentage) / 100;
    }
    
    // Calculate final price (after discount, with GST)
    $final_price = $subtotal_after_discount + $gst_amount;
    
    // Calculate price before discount (full subtotal + GST on full subtotal)
    $gst_on_full_subtotal = 0;
    if ($gst_enabled === 'yes' || $gst_enabled === '1' || $gst_enabled === 1 || $gst_enabled === true) {
        $gst_on_full_subtotal = ($subtotal_before_discount * $gst_percentage) / 100;
    }
    $price_before_discount = $subtotal_before_discount + $gst_on_full_subtotal;
    
    // Apply rounding
    $rounding = get_option('jpc_price_rounding', 'default');
    $final_price = $this->apply_rounding($final_price, $rounding);
    $price_before_discount = $this->apply_rounding($price_before_discount, $rounding);
    
    // Get extra field labels for display
    $extra_fields = array();
    for ($i = 1; $i <= 5; $i++) {
        $enabled = get_option('jpc_enable_extra_field_' . $i);
        if ($enabled === 'yes' || $enabled === '1') {
            $label = get_option('jpc_extra_field_label_' . $i, 'Extra Field #' . $i);
            $field_var = 'extra_field_' . $i;
            $value = $$field_var; // Get the value from the variable
            if ($value > 0) {
                $extra_fields[] = array(
                    'label' => $label,
                    'value' => $value
                );
            }
        }
    }
    
    wp_send_json_success(array(
        'metal_price' => $metal_price,
        'diamond_price' => $diamond_price,
        'making_charge' => $making_charge_amount,
        'wastage_charge' => $wastage_charge_amount,
        'pearl_cost' => $pearl_cost,
        'stone_cost' => $stone_cost,
        'extra_fee' => $extra_fee,
        'extra_fields' => $extra_fields,
        'additional_percentage' => $additional_percentage_amount,
        'additional_percentage_label' => $additional_percentage_label,
        'discount' => $discount_amount,
        'discount_percentage' => $discount_percentage,
        'subtotal' => $subtotal_after_discount,
        'gst' => $gst_amount,
        'gst_percentage' => $gst_percentage,
        'gst_label' => $gst_label,
        'final_price' => $final_price,
        'price_before_discount' => $price_before_discount,
    ));
}
