<?php
/**
 * CRITICAL FIX v2.5.10
 * 
 * This file contains the corrected save_meta_box function that saves
 * additional cost fields with the correct meta keys (_value and _type).
 * 
 * REPLACE lines 338-340 in class-jpc-product-meta-box-v2.php with this code:
 */

// Save additional cost fields with correct meta keys (v2.5.10 FIX)
// Pearl Cost (Additional Cost Field 1)
$pearl_cost_value = floatval($_POST['_jpc_pearl_cost_value'] ?? 0);
$pearl_cost_type = get_option('jpc_pearl_cost_type', 'fixed');
update_post_meta($post_id, '_jpc_pearl_cost_value', $pearl_cost_value);
update_post_meta($post_id, '_jpc_pearl_cost_type', $pearl_cost_type);

// Stone Cost (Additional Cost Field 2)
$stone_cost_value = floatval($_POST['_jpc_stone_cost_value'] ?? 0);
$stone_cost_type = get_option('jpc_stone_cost_type', 'fixed');
update_post_meta($post_id, '_jpc_stone_cost_value', $stone_cost_value);
update_post_meta($post_id, '_jpc_stone_cost_type', $stone_cost_type);

// Extra Fee (Additional Cost Field 3)
$extra_fee_value = floatval($_POST['_jpc_extra_fee_value'] ?? 0);
$extra_fee_type = get_option('jpc_extra_fee_type', 'fixed');
update_post_meta($post_id, '_jpc_extra_fee_value', $extra_fee_value);
update_post_meta($post_id, '_jpc_extra_fee_type', $extra_fee_type);

// Keep discount as is
update_post_meta($post_id, '_jpc_discount_percentage', floatval($_POST['_jpc_discount_percentage'] ?? 0));
