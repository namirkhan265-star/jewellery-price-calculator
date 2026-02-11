<?php
/**
 * Product Details Accordion Template v2.5.31
 * Displays product details, diamond details, metal details, price breakup, and tags
 * Usage: [jpc_product_details]
 * 
 * NEW v2.5.31: CRITICAL FIX - Combined fix for both fatal errors
 * - Fixed line 39: Added safety check for $product object before calling get_weight()
 * - Fixed line 94: Changed $diamond_colour->name to $colour->name
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

// v2.5.31: CRITICAL FIX - Safely get WooCommerce product weight
if (!isset($product) || !is_object($product)) {
    $product = wc_get_product($product_id);
}
$product_weight = $product ? $product->get_weight() : '';

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
    
    // v2.5.31: CRITICAL FIX - Changed $diamond_colour->name to $colour->name
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
                <span class="jpc-detail-label">Number of Diamonds</span>
                <span class="jpc-detail-value"><?php echo $diamond_quantity; ?></span>
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
            
            <?php if ($diamond_carat > 0): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Carat</span>
                <span class="jpc-detail-value"><?php echo number_format($diamond_carat, 3); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($diamond_price_per_carat > 0): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Price per Carat</span>
                <span class="jpc-detail-value">₹ <?php echo number_format($diamond_price_per_carat, 0); ?>/-</span>
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
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Metal Type</span>
                <span class="jpc-detail-value"><?php echo esc_html($metal->display_name); ?></span>
            </div>
            
            <?php if ($metal_weight): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Weight</span>
                <span class="jpc-detail-value"><?php echo number_format($metal_weight, 3); ?> g</span>
            </div>
            <?php endif; ?>
            
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Price per <?php echo $is_silver ? 'kg' : 'gram'; ?></span>
                <span class="jpc-detail-value">₹ <?php echo number_format($metal->price_per_unit, 0); ?>/-</span>
            </div>
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
                <span class="jpc-detail-label">Gold</span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['metal_price'], 0); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($price_breakup['diamond_price'])): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Diamond</span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['diamond_price'], 0); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($price_breakup['making_charge'])): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Making Charges</span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['making_charge'], 0); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($price_breakup['wastage_charge'])): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Wastage Charge</span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['wastage_charge'], 0); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php 
            // v2.5.24: CRITICAL FIX - Only show if value > 1 (not just !empty)
            if (!empty($price_breakup['pearl_cost']) && floatval($price_breakup['pearl_cost']) > 1): 
            ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label"><?php echo esc_html(get_option('jpc_pearl_cost_label', 'Pearl Cost')); ?></span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['pearl_cost'], 0); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php 
            // v2.5.24: CRITICAL FIX - Only show if value > 1 (not just !empty)
            if (!empty($price_breakup['stone_cost']) && floatval($price_breakup['stone_cost']) > 1): 
            ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label"><?php echo esc_html(get_option('jpc_stone_cost_label', 'Stone Cost')); ?></span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['stone_cost'], 0); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php 
            // v2.5.24: CRITICAL FIX - Only show if value > 1 (not just !empty)
            if (!empty($price_breakup['extra_fee']) && floatval($price_breakup['extra_fee']) > 1): 
            ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label"><?php echo esc_html(get_option('jpc_extra_fee_label', 'Extra Fee')); ?></span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['extra_fee'], 0); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php
            // v2.5.22: FIX - Extra Fields #1-5 with custom labels AND enable/disable check
            if (!empty($price_breakup['extra_fields']) && is_array($price_breakup['extra_fields'])) {
                $field_index = 0;
                foreach ($price_breakup['extra_fields'] as $extra_field) {
                    $field_index++;
                    if (!empty($extra_field['value']) && $extra_field['value'] > 0) {
                        $field_num = !empty($extra_field['field_number']) ? $extra_field['field_number'] : $field_index;
                        
                        // v2.5.22: CRITICAL FIX - Check if field is enabled in settings before displaying
                        $is_field_enabled = get_option('jpc_enable_extra_field_' . $field_num, 'yes') === 'yes';
                        
                        if ($is_field_enabled) {
                            $live_label = get_option('jpc_extra_field_label_' . $field_num, $extra_field['label']);
                            ?>
                            <div class="jpc-detail-row">
                                <span class="jpc-detail-label"><?php echo esc_html($live_label); ?></span>
                                <span class="jpc-detail-value">₹ <?php echo number_format($extra_field['value'], 0); ?>/-</span>
            </div>
                            <?php
                        }
                    }
                }
            }
            ?>
            
            <?php if (!empty($price_breakup['additional_percentage'])): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label"><?php echo esc_html(get_option('jpc_additional_percentage_label', 'Additional Percentage')); ?></span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['additional_percentage'], 0); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php if ($enable_discount === 'yes' && !empty($price_breakup['discount'])): ?>
            <div class="jpc-detail-row" style="color: #d63638;">
                <span class="jpc-detail-label">
                    Discount
                    <?php if ($discount_percentage > 0): ?>
                        <span style="font-weight: bold;">(<?php echo number_format($discount_percentage, 0); ?>% OFF)</span>
                    <?php endif; ?>
                </span>
                <span class="jpc-detail-value" style="font-weight: bold;">- ₹ <?php echo number_format($price_breakup['discount'], 0); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($price_breakup['gst'])): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">
                    <?php echo esc_html(!empty($price_breakup['gst_label']) ? $price_breakup['gst_label'] : 'GST'); ?>
                    <?php if (!empty($price_breakup['gst_percentage']) && $price_breakup['gst_percentage'] > 0): ?>
                        <span style="font-weight: bold;">(<?php echo number_format($price_breakup['gst_percentage'], 0); ?>%)</span>
                    <?php endif; ?>
                </span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['gst'], 0); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php if ($regular_price > 0 && $sale_price > 0): ?>
            <div class="jpc-price-summary">
                <div class="jpc-detail-row jpc-regular-price-row">
                    <span class="jpc-detail-label">Regular Price</span>
                    <span class="jpc-detail-value jpc-strikethrough">₹ <?php echo number_format($regular_price, 0); ?>/-</span>
                </div>
                <div class="jpc-detail-row jpc-sale-price-row">
                    <span class="jpc-detail-label"><strong>Sale Price</strong></span>
                    <span class="jpc-detail-value" style="color: #d63638; font-weight: bold; font-size: 16px;">₹ <?php echo number_format($sale_price, 2); ?>/-</span>
                </div>
            </div>
            <?php else: ?>
            <div class="jpc-detail-row jpc-total-row">
                <span class="jpc-detail-label"><strong>Total</strong></span>
                <span class="jpc-detail-value"><strong>₹ <?php echo number_format($price_breakup['final_price'], 0); ?>/-</strong></span>
            </div>
            <?php endif; ?>
            
            <?php if ($enable_discount === 'yes' && !empty($price_breakup['discount']) && $discount_percentage > 0): ?>
            <div class="jpc-savings-badge">
                🎉 <strong>You Save: ₹ <?php echo number_format($price_breakup['discount'], 0); ?>/- (<?php echo number_format($discount_percentage, 0); ?>% OFF)</strong>
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
                <?php 
                $tag_links = array();
                foreach ($tags as $tag) {
                    $tag_link = get_term_link($tag);
                    if (!is_wp_error($tag_link)) {
                        $tag_links[] = '<a href="' . esc_url($tag_link) . '" class="jpc-tag">' . esc_html($tag->name) . '</a>';
                    }
                }
                echo implode(' ', $tag_links);
                ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
</div>

<style>
.jpc-product-details-accordion {
    margin: 20px 0;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
}

.jpc-accordion-section {
    border-bottom: 1px solid #e0e0e0;
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
    font-size: 20px;
    font-weight: bold;
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
    padding: 8px 0;
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

.jpc-price-summary {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 2px solid #e0e0e0;
}

.jpc-strikethrough {
    text-decoration: line-through;
    color: #999 !important;
}

.jpc-savings-badge {
    margin-top: 15px;
    padding: 12px;
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-radius: 4px;
    text-align: center;
    color: #856404;
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
    text-decoration: none;
    color: #333;
    font-size: 13px;
    transition: background 0.3s;
}

.jpc-tag:hover {
    background: #e0e0e0;
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
