<?php
/**
 * SNIPPET FOR product-details-accordion.php
 * v2.5.29: Add conditional display for making charges and wastage based on metal group settings
 * 
 * ADD THIS CODE at the top of the file (after line 35, after getting $metal_id):
 */

// v2.5.29: Get metal group settings for conditional display
$metal_group = null;
if ($metal_id) {
    $metal = JPC_Metals::get_by_id($metal_id);
    if ($metal && $metal->metal_group_id) {
        $metal_group = JPC_Metal_Groups::get_by_id($metal->metal_group_id);
    }
}

/**
 * REPLACE lines 380-395 (Making Charges and Wastage Charge display) with this:
 */

            <?php 
            // v2.5.29: Only show making charges if enabled in metal group
            if (($metal_group && $metal_group->enable_making_charge) || !$metal_group): 
                if (!empty($price_breakup['making_charge'])): 
            ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Making Charges</span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['making_charge'], 0); ?>/-</span>
            </div>
            <?php 
                endif; 
            endif; 
            ?>
            
            <?php 
            // v2.5.29: Only show wastage charge if enabled in metal group
            if (($metal_group && $metal_group->enable_wastage_charge) || !$metal_group): 
                if (!empty($price_breakup['wastage_charge'])): 
            ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Wastage Charge</span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['wastage_charge'], 0); ?>/-</span>
            </div>
            <?php 
                endif; 
            endif; 
            ?>

/**
 * EXPLANATION:
 * 
 * 1. Added metal group retrieval at the top of the file
 * 2. Wrapped making charges display with metal group check
 * 3. Wrapped wastage charge display with metal group check
 * 4. Both default to showing if metal group doesn't exist (backward compatibility)
 * 5. Only hide if metal group explicitly has the charge disabled
 */
