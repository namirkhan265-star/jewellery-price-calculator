<?php
/**
 * Product Details Accordion Template v2.5.30
 * Displays product details, diamond details, metal details, price breakup, and tags
 * Usage: [jpc_product_details]
 * 
 * NEW v2.5.30: CRITICAL FIX - Safely get product weight to prevent fatal error
 * - Added safety check for $product object before calling get_weight()
 * - Falls back to getting product object from $product_id if not set
 * - Prevents "Call to a member function get_weight() on null" error
 * 
 * NEW v2.5.29: CRITICAL BUG FIX - Fixed undefined variable error
 * - Line 94: Changed $diamond_colour->name to $colour->name
 * - This was causing fatal error on frontend product pages
 * 
 * NEW v2.5.28: CRITICAL FIX - Hide Discount in accordion when disabled in settings
 * - Check jpc_enable_discount setting before displaying discount row and savings badge
 * - Matches behavior of product meta box
 * 
 * NEW v2.5.24: CRITICAL FIX - Hide Pearl/Stone/Extra Fee when value is ₹1 or less
 * - Changed display logic from !empty() to value > 1
 * - Prevents showing meaningless ₹1/- values in price breakup
 * 
 * NEW v2.5.22: FIX - Respect enable/disable setting for Additional Cost Fields
 * - Check get_option('jpc_enable_extra_field_X') before displaying each field
 * - Fixes bug where disabled fields still showed values in price breakup
 * 
 * NEW v2.5.21: CRITICAL FIX - Correct regular price calculation
 * - Use actual GST percentage from settings (not calculated from discounted price)
 * - Regular price = Subtotal Before Discount + GST (at actual rate)
 * - Matches WooCommerce _regular_price meta value
 * 
 * NEW v2.5.4: Fix GST display - show custom label and percentage like discount
 * NEW v2.5.3: Fetch custom labels from settings for Pearl/Stone/Extra Fee (same as Extra Fields)
 * NEW v2.4.0: Full support for manual diamond entry
 * - Fetches manual diamond data from _jpc_manual_diamond_* meta fields
 * - Displays manual diamond details in Diamond Details section
 * - Falls back to dropdown diamond if manual entry not used
 */

if (!defined('ABSPATH')) {
    exit;
}

// v2.5.30: CRITICAL FIX - Safely get product weight
// Ensure $product object is available
if (!isset($product) || !is_object($product)) {
    global $product;
    if (!$product && isset($product_id)) {
        $product = wc_get_product($product_id);
    }
}

// Get WooCommerce product weight (total product weight)
$product_weight = ($product && is_object($product)) ? $product->get_weight() : 0;

// Get metal ID (passed from shortcode handler)
if (!isset($metal_id)) {
    $metal_id = get_post_meta($product_id, '_jpc_metal_id', true);
}

// Get metal data
$metal_weight = get_post_meta($product_id, '_jpc_metal_weight', true);

// Check diamond entry mode
$diamond_entry_mode = get_post_meta($product_id, '_jpc_diamond_entry_mode', true) ?: 'dropdown';

// Initialize diamond variables
$diamond = null;
$diamond_quantity = 0;
$diamond_carat = 0;
$diamond_price_per_carat = 0;
$diamond_type_label = '';
$diamond_cert_label = '';
$diamond_shape_label = '';
$diamond_colour_label = '';
$diamond_clarity_label = '';
$diamond_cut_label = '';

if ($diamond_entry_mode === 'manual') {
    // MANUAL DIAMOND ENTRY MODE
    $diamond_quantity = intval(get_post_meta($product_id, '_jpc_manual_diamond_quantity', true));
    $diamond_carat = floatval(get_post_meta($product_id, '_jpc_manual_diamond_carat', true));
    $diamond_price_per_carat = floatval(get_post_meta($product_id, '_jpc_manual_diamond_price_per_carat', true));
    
    // Get manual diamond details
    $manual_diamond_group_id = get_post_meta($product_id, '_jpc_manual_diamond_group_id', true);
    $manual_diamond_cert_id = get_post_meta($product_id, '_jpc_manual_diamond_certification_id', true);
    $manual_diamond_shape_id = get_post_meta($product_id, '_jpc_manual_diamond_shape_id', true);
    $manual_diamond_colour_id = get_post_meta($product_id, '_jpc_manual_diamond_colour_id', true);
    $manual_diamond_clarity_id = get_post_meta($product_id, '_jpc_manual_diamond_clarity_id', true);
    $manual_diamond_cut_id = get_post_meta($product_id, '_jpc_manual_diamond_cut_id', true);
    
    // Get labels for manual diamond
    if ($manual_diamond_group_id) {
        $diamond_group = JPC_Diamond_Groups::get_by_id($manual_diamond_group_id);
        $diamond_type_label = $diamond_group ? $diamond_group->name : '';
    }
    
    if ($manual_diamond_cert_id) {
        $cert = JPC_Diamond_Certifications::get_by_id($manual_diamond_cert_id);
        $diamond_cert_label = $cert ? $cert->name : '';
    }
    
    if ($manual_diamond_shape_id) {
        $shape = JPC_Diamond_Shapes::get_by_id($manual_diamond_shape_id);
        $diamond_shape_label = $shape ? $shape->name : '';
    }
    
    if ($manual_diamond_colour_id) {
        $colour = JPC_Diamond_Colours::get_by_id($manual_diamond_colour_id);
        $diamond_colour_label = $colour ? $colour->name : '';
    }
    
    if ($manual_diamond_clarity_id) {
        $clarity = JPC_Diamond_Clarities::get_by_id($manual_diamond_clarity_id);
        $diamond_clarity_label = $clarity ? $clarity->name : '';
    }
    
    if ($manual_diamond_cut_id) {
        $cut = JPC_Diamond_Cuts::get_by_id($manual_diamond_cut_id);
        $diamond_cut_label = $cut ? $cut->name : '';
    }
    
} else {
    // DROPDOWN DIAMOND MODE (Original)
    $diamond_id = get_post_meta($product_id, '_jpc_diamond_id', true);
    $diamond_quantity = intval(get_post_meta($product_id, '_jpc_diamond_quantity', true));
    
    if ($diamond_id) {
        $diamond = JPC_Diamonds::get_by_id($diamond_id);
        if ($diamond) {
            $diamond_carat = $diamond->carat;
            $diamond_price_per_carat = $diamond->price_per_carat;
            
            // Get diamond details
            $diamond_type = JPC_Diamond_Types::get_by_id($diamond->diamond_type_id);
            $diamond_type_label = $diamond_type ? $diamond_type->name : '';
            
            $diamond_cert = JPC_Diamond_Certifications::get_by_id($diamond->certification_id);
            $diamond_cert_label = $diamond_cert ? $diamond_cert->name : '';
            
            $diamond_shape = JPC_Diamond_Shapes::get_by_id($diamond->shape_id);
            $diamond_shape_label = $diamond_shape ? $diamond_shape->name : '';
            
            $diamond_colour = JPC_Diamond_Colours::get_by_id($diamond->colour_id);
            $diamond_colour_label = $diamond_colour ? $diamond_colour->name : '';
            
            $diamond_clarity = JPC_Diamond_Clarities::get_by_id($diamond->clarity_id);
            $diamond_clarity_label = $diamond_clarity ? $diamond_clarity->name : '';
            
            $diamond_cut = JPC_Diamond_Cuts::get_by_id($diamond->cut_id);
            $diamond_cut_label = $diamond_cut ? $diamond_cut->name : '';
        }
    }
}

// Get metal details
$metal_karat = '';
$is_silver = false;
if ($metal_id) {
    $metal = JPC_Metals::get_by_id($metal_id);
    if ($metal) {
        // Check if it's silver
        if (stripos($metal->name, 'silver') !== false) {
            $is_silver = true;
        }
        // Extract karat from metal name (e.g., "22K Gold" -> "22K")
        if (preg_match('/(\d+K)/i', $metal->name, $matches)) {
            $metal_karat = $matches[1];
        }
    }
}

// Get price breakup
$price_breakup = get_post_meta($product_id, '_jpc_price_breakup', true);

// Get discount percentage from meta
$discount_percentage = floatval(get_post_meta($product_id, '_jpc_discount_percentage', true));

// v2.5.28: Get enable/disable setting for Discount
$enable_discount = get_option('jpc_enable_discount', 'no');

// v2.5.21: CRITICAL FIX - Calculate regular price correctly
$regular_price = 0;
$sale_price = 0;

if ($price_breakup && is_array($price_breakup)) {
    if (!empty($price_breakup['discount'])) {
        // Calculate subtotal before discount (sum of all components)
        $subtotal_before_discount = 0;
        $subtotal_before_discount += !empty($price_breakup['metal_price']) ? floatval($price_breakup['metal_price']) : 0;
        $subtotal_before_discount += !empty($price_breakup['diamond_price']) ? floatval($price_breakup['diamond_price']) : 0;
        $subtotal_before_discount += !empty($price_breakup['making_charge']) ? floatval($price_breakup['making_charge']) : 0;
        $subtotal_before_discount += !empty($price_breakup['wastage_charge']) ? floatval($price_breakup['wastage_charge']) : 0;
        $subtotal_before_discount += !empty($price_breakup['pearl_cost']) ? floatval($price_breakup['pearl_cost']) : 0;
        $subtotal_before_discount += !empty($price_breakup['stone_cost']) ? floatval($price_breakup['stone_cost']) : 0;
        $subtotal_before_discount += !empty($price_breakup['extra_fee']) ? floatval($price_breakup['extra_fee']) : 0;
        
        // Add extra fields to subtotal
        if (!empty($price_breakup['extra_fields']) && is_array($price_breakup['extra_fields'])) {
            foreach ($price_breakup['extra_fields'] as $extra_field) {
                $subtotal_before_discount += !empty($extra_field['value']) ? floatval($extra_field['value']) : 0;
            }
        }
        
        // Add additional percentage to subtotal
        if (!empty($price_breakup['additional_percentage'])) {
            $subtotal_before_discount += floatval($price_breakup['additional_percentage']);
        }
        
        // v2.5.21: CRITICAL FIX - Get actual GST percentage from settings (not calculated from discounted price)
        $enable_gst = get_option('jpc_enable_gst', 'yes');
        $gst_percentage = 0;
        
        if ($enable_gst === 'yes') {
            $gst_percentage = floatval(get_option('jpc_gst_value', 3));
        }
        
        // Calculate GST on pre-discount subtotal using actual GST percentage
        $gst_on_regular_price = ($subtotal_before_discount * $gst_percentage) / 100;
        
        // Regular price = subtotal before discount + GST on that subtotal
        $regular_price = $subtotal_before_discount + $gst_on_regular_price;
        $sale_price = $price_breakup['final_price'];
    }
}

// Get product tags
$tags = wp_get_post_terms($product_id, 'product_tag');

// Check if we have any data to display
$has_product_details = $product_weight || $metal || ($diamond_quantity > 0);
$has_diamond_details = $diamond_quantity > 0 && ($diamond_carat > 0 || $diamond_price_per_carat > 0);
$has_metal_details = $metal;
$has_price_breakup = $price_breakup && is_array($price_breakup);
$has_tags = !empty($tags);
?>

<?php if ($has_product_details || $has_diamond_details || $has_metal_details || $has_price_breakup || $has_tags): ?>
<div class="jpc-product-details-accordion">
    
    <!-- Product Details Section -->
    <?php if ($has_product_details): ?>
    <div class="jpc-accordion-section">
        <div class="jpc-accordion-header">
            <h3>PRODUCT DETAILS</h3>
            <span class="jpc-accordion-toggle">+</span>
        </div>
        <div class="jpc-accordion-content">
            <?php if ($product_weight): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Gross Weight</span>
                <span class="jpc-detail-value"><?php echo number_format($product_weight, 3); ?> g</span>
            </div>
            <?php endif; ?>
            
            <?php if ($metal_weight): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Net Weight</span>
                <span class="jpc-detail-value"><?php echo number_format($metal_weight, 3); ?> g</span>
            </div>
            <?php endif; ?>
            
            <?php if ($metal_karat): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Purity</span>
                <span class="jpc-detail-value"><?php echo esc_html($metal_karat); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($diamond_quantity > 0): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">No. of Diamonds</span>
                <span class="jpc-detail-value"><?php echo intval($diamond_quantity); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Diamond Details Section -->
    <?php if ($has_diamond_details): ?>
    <div class="jpc-accordion-section">
        <div class="jpc-accordion-header">
            <h3>DIAMOND DETAILS</h3>
            <span class="jpc-accordion-toggle">+</span>
        </div>
        <div class="jpc-accordion-content">
            <?php if ($diamond_type_label): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Type</span>
                <span class="jpc-detail-value"><?php echo esc_html($diamond_type_label); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($diamond_carat > 0): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Carat</span>
                <span class="jpc-detail-value"><?php echo number_format($diamond_carat, 2); ?> ct</span>
            </div>
            <?php endif; ?>
            
            <?php if ($diamond_cert_label): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Certification</span>
                <span class="jpc-detail-value"><?php echo esc_html($diamond_cert_label); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($diamond_shape_label): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Shape</span>
                <span class="jpc-detail-value"><?php echo esc_html($diamond_shape_label); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($diamond_colour_label): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Colour</span>
                <span class="jpc-detail-value"><?php echo esc_html($diamond_colour_label); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($diamond_clarity_label): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Clarity</span>
                <span class="jpc-detail-value"><?php echo esc_html($diamond_clarity_label); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($diamond_cut_label): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Cut</span>
                <span class="jpc-detail-value"><?php echo esc_html($diamond_cut_label); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Metal Details Section -->
    <?php if ($has_metal_details): ?>
    <div class="jpc-accordion-section">
        <div class="jpc-accordion-header">
            <h3>METAL DETAILS</h3>
            <span class="jpc-accordion-toggle">+</span>
        </div>
        <div class="jpc-accordion-content">
            <?php if ($metal): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Metal</span>
                <span class="jpc-detail-value"><?php echo esc_html($metal->display_name); ?></span>
            </div>
            
            <?php if ($metal_weight): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Weight</span>
                <span class="jpc-detail-value"><?php echo number_format($metal_weight, 3); ?> g</span>
            </div>
            <?php endif; ?>
            
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Current Rate</span>
                <span class="jpc-detail-value">₹ <?php echo number_format($metal->price_per_unit, 2); ?>/g</span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Price Breakup Section -->
    <?php if ($has_price_breakup): ?>
    <div class="jpc-accordion-section">
        <div class="jpc-accordion-header">
            <h3>PRICE BREAKUP</h3>
            <span class="jpc-accordion-toggle">+</span>
        </div>
        <div class="jpc-accordion-content">
            <?php if (!empty($price_breakup['metal_price'])): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Metal Price</span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['metal_price'], 2); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($price_breakup['diamond_price'])): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Diamond Price</span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['diamond_price'], 2); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($price_breakup['making_charge'])): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Making Charges</span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['making_charge'], 2); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($price_breakup['wastage_charge'])): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Wastage</span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['wastage_charge'], 2); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php 
            // v2.5.24: CRITICAL FIX - Only show Pearl/Stone/Extra Fee if value > 1
            // Get custom labels from settings
            $pearl_cost_label = get_option('jpc_pearl_cost_label', 'Pearl Cost');
            $stone_cost_label = get_option('jpc_stone_cost_label', 'Stone Cost');
            $extra_fee_label = get_option('jpc_extra_fee_label', 'Extra Fee');
            
            if (!empty($price_breakup['pearl_cost']) && floatval($price_breakup['pearl_cost']) > 1): 
            ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label"><?php echo esc_html($pearl_cost_label); ?></span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['pearl_cost'], 2); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($price_breakup['stone_cost']) && floatval($price_breakup['stone_cost']) > 1): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label"><?php echo esc_html($stone_cost_label); ?></span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['stone_cost'], 2); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($price_breakup['extra_fee']) && floatval($price_breakup['extra_fee']) > 1): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label"><?php echo esc_html($extra_fee_label); ?></span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['extra_fee'], 2); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php 
            // v2.5.22: FIX - Respect enable/disable setting for Additional Cost Fields
            // Display extra fields if they exist and are enabled
            if (!empty($price_breakup['extra_fields']) && is_array($price_breakup['extra_fields'])): 
                foreach ($price_breakup['extra_fields'] as $field_num => $extra_field):
                    // Check if this field is enabled in settings
                    $is_enabled = get_option('jpc_enable_extra_field_' . $field_num, 'no');
                    if ($is_enabled === 'yes' && !empty($extra_field['value'])):
            ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label"><?php echo esc_html($extra_field['label']); ?></span>
                <span class="jpc-detail-value">₹ <?php echo number_format($extra_field['value'], 2); ?>/-</span>
            </div>
            <?php 
                    endif;
                endforeach;
            endif; 
            ?>
            
            <?php if (!empty($price_breakup['additional_percentage'])): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Additional Percentage</span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['additional_percentage'], 2); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($price_breakup['subtotal'])): ?>
            <div class="jpc-detail-row jpc-subtotal">
                <span class="jpc-detail-label">Subtotal</span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['subtotal'], 2); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php 
            // v2.5.28: CRITICAL FIX - Only show discount if enabled in settings
            if ($enable_discount === 'yes' && !empty($price_breakup['discount'])): 
            ?>
            <div class="jpc-detail-row jpc-discount">
                <span class="jpc-detail-label">Discount (<?php echo number_format($discount_percentage, 2); ?>%)</span>
                <span class="jpc-detail-value">- ₹ <?php echo number_format($price_breakup['discount'], 2); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($price_breakup['gst'])): 
                // v2.5.4: Get custom GST label and percentage
                $gst_label = get_option('jpc_gst_label', 'GST');
                $gst_percentage = floatval(get_option('jpc_gst_value', 3));
            ?>
            <div class="jpc-detail-row jpc-gst">
                <span class="jpc-detail-label"><?php echo esc_html($gst_label); ?> (<?php echo number_format($gst_percentage, 2); ?>%)</span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['gst'], 2); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($price_breakup['final_price'])): ?>
            <div class="jpc-detail-row jpc-total">
                <span class="jpc-detail-label">Total Price</span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['final_price'], 2); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php 
            // v2.5.28: CRITICAL FIX - Only show savings badge if discount is enabled
            if ($enable_discount === 'yes' && $regular_price > 0 && $sale_price > 0 && $regular_price > $sale_price): 
                $savings = $regular_price - $sale_price;
                $savings_percentage = (($savings / $regular_price) * 100);
            ?>
            <div class="jpc-savings-badge">
                <span class="jpc-savings-text">You Save: ₹ <?php echo number_format($savings, 2); ?>/- (<?php echo number_format($savings_percentage, 1); ?>%)</span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Tags Section -->
    <?php if ($has_tags): ?>
    <div class="jpc-accordion-section">
        <div class="jpc-accordion-header">
            <h3>TAGS</h3>
            <span class="jpc-accordion-toggle">+</span>
        </div>
        <div class="jpc-accordion-content">
            <div class="jpc-tags-list">
                <?php foreach ($tags as $tag): ?>
                    <span class="jpc-tag"><?php echo esc_html($tag->name); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
</div>

<style>
.jpc-product-details-accordion {
    margin: 20px 0;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
}

.jpc-accordion-section {
    border-bottom: 1px solid #ddd;
}

.jpc-accordion-section:last-child {
    border-bottom: none;
}

.jpc-accordion-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background: #f8f8f8;
    cursor: pointer;
    transition: background 0.3s;
}

.jpc-accordion-header:hover {
    background: #f0f0f0;
}

.jpc-accordion-header h3 {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.jpc-accordion-toggle {
    font-size: 24px;
    font-weight: 300;
    transition: transform 0.3s;
}

.jpc-accordion-section.active .jpc-accordion-toggle {
    transform: rotate(45deg);
}

.jpc-accordion-content {
    display: none;
    padding: 20px;
    background: #fff;
}

.jpc-accordion-section.active .jpc-accordion-content {
    display: block;
}

.jpc-detail-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

.jpc-detail-row:last-child {
    border-bottom: none;
}

.jpc-detail-label {
    font-weight: 500;
    color: #666;
}

.jpc-detail-value {
    font-weight: 600;
    color: #333;
}

.jpc-detail-row.jpc-subtotal,
.jpc-detail-row.jpc-total {
    font-size: 16px;
    padding-top: 15px;
    margin-top: 10px;
    border-top: 2px solid #ddd;
}

.jpc-detail-row.jpc-discount {
    color: #d32f2f;
}

.jpc-detail-row.jpc-gst {
    color: #1976d2;
}

.jpc-savings-badge {
    margin-top: 15px;
    padding: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 6px;
    text-align: center;
}

.jpc-savings-text {
    color: #fff;
    font-weight: 600;
    font-size: 14px;
}

.jpc-tags-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.jpc-tag {
    display: inline-block;
    padding: 6px 12px;
    background: #f0f0f0;
    border-radius: 4px;
    font-size: 13px;
    color: #666;
}
</style>

<script>
jQuery(document).ready(function($) {
    $('.jpc-accordion-header').on('click', function() {
        $(this).parent('.jpc-accordion-section').toggleClass('active');
    });
});
</script>
<?php endif; ?>
