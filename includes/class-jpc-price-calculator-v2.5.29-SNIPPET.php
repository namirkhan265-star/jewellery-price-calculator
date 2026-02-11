<?php
/**
 * SNIPPET FOR class-jpc-price-calculator-v2.php
 * v2.5.29: Add conditional calculation for making charges and wastage based on metal group settings
 * 
 * REPLACE the calculate_making_charges method (lines 28-54) with this:
 */

    /**
     * Calculate making charges (v2.0.0 - Auto/Manual modes)
     * v2.5.29: Check if making charges are enabled in metal group
     */
    private static function calculate_making_charges($product_id, $metal_price, $metal_id, $metal_weight) {
        // v2.5.29: Get metal group settings
        $metal = JPC_Metals::get_by_id($metal_id);
        if (!$metal) return 0;
        
        $metal_group = null;
        if ($metal->metal_group_id) {
            $metal_group = JPC_Metal_Groups::get_by_id($metal->metal_group_id);
        }
        
        // v2.5.29: Check if making charges are enabled in metal group
        if ($metal_group && !$metal_group->enable_making_charge) {
            return 0; // Making charges disabled for this metal group
        }
        
        $mode = get_post_meta($product_id, '_jpc_making_charges_mode', true) ?: 'auto';
        
        if ($mode === 'auto') {
            // Auto mode: Metal Weight × Making Charges per Gram
            $making_charges_per_gram = floatval($metal->making_charges_per_gram ?? 0);
            return $metal_weight * $making_charges_per_gram;
            
        } else {
            // Manual mode: Percentage or Fixed
            $value = floatval(get_post_meta($product_id, '_jpc_making_charges_value', true));
            $type = get_post_meta($product_id, '_jpc_making_charges_type', true) ?: 'percentage';
            
            if ($type === 'percentage') {
                return ($metal_price * $value) / 100;
            } else {
                return $value;
            }
        }
    }

/**
 * REPLACE lines 160-170 (wastage calculation) with this:
 */

        // Calculate making charges
        $making_charge_amount = self::calculate_making_charges($product_id, $metal_price, $metal_id, $metal_weight);
        
        // v2.5.29: Calculate wastage charges - check if enabled in metal group
        $wastage_charge_amount = 0;
        if (!$metal_group || $metal_group->enable_wastage_charge) {
            $wastage_percentage = floatval(get_post_meta($product_id, '_jpc_wastage_percentage', true));
            $wastage_charge_amount = ($metal_price * $wastage_percentage) / 100;
        }

/**
 * EXPLANATION:
 * 
 * 1. Updated calculate_making_charges() method:
 *    - Gets metal group settings
 *    - Returns 0 if making charges are disabled in metal group
 *    - Otherwise calculates normally (auto or manual mode)
 * 
 * 2. Updated wastage calculation:
 *    - Checks if wastage is enabled in metal group
 *    - Only calculates if enabled (or if no metal group found - backward compatibility)
 *    - Sets to 0 if disabled
 * 
 * 3. Backward Compatibility:
 *    - If metal group doesn't exist or doesn't have these settings, both charges default to enabled
 *    - Existing products continue to work without any issues
 */
