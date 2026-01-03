<?php
/**
 * Product Details Accordion Template
 * Displays product details, diamond details, metal details, price breakup, and tags
 * Usage: [jpc_product_details]
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get WooCommerce product weight (total product weight)
$product_weight = $product->get_weight();

// Get metal data
$metal_weight = get_post_meta($product_id, '_jpc_metal_weight', true);

// Get diamond data
$diamond_id = get_post_meta($product_id, '_jpc_diamond_id', true);
$diamond_quantity = intval(get_post_meta($product_id, '_jpc_diamond_quantity', true));

// Get diamond details
$diamond = null;
$diamond_type_label = '';
$diamond_cert_label = '';
if ($diamond_id) {
    $diamond = JPC_Diamonds::get_by_id($diamond_id);
    if ($diamond) {
        $types = JPC_Diamonds::get_types();
        $certs = JPC_Diamonds::get_certifications();
        $diamond_type_label = isset($types[$diamond->type]) ? $types[$diamond->type] : $diamond->type;
        $diamond_cert_label = isset($certs[$diamond->certification]) ? $certs[$diamond->certification] : $diamond->certification;
    }
}

// Get metal details
$metal = null;
$metal_group = null;
$metal_karat = '';
$is_silver = false;
if ($metal_id) {
    $metal = JPC_Metals::get_by_id($metal_id);
    if ($metal) {
        $metal_group = JPC_Metal_Groups::get_by_id($metal->metal_group_id);
        // Check if metal is silver
        if ($metal_group && strtolower($metal_group->name) === 'silver') {
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

// Calculate regular price (before discount) and sale price (after discount)
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
        
        // Calculate GST on the pre-discount subtotal
        // GST rate can be determined from the actual GST and final price
        $current_gst = !empty($price_breakup['gst']) ? floatval($price_breakup['gst']) : 0;
        $price_after_discount = $subtotal_before_discount - floatval($price_breakup['discount']);
        
        // Calculate GST rate from current values
        $gst_rate = ($price_after_discount > 0) ? ($current_gst / $price_after_discount) : 0;
        
        // Calculate GST on pre-discount subtotal
        $gst_on_regular_price = $subtotal_before_discount * $gst_rate;
        
        // Regular price = subtotal before discount + GST on that subtotal
        $regular_price = $subtotal_before_discount + $gst_on_regular_price;
        $sale_price = $price_breakup['final_price'];
    }
}

// Get product tags
$tags = wp_get_post_terms($product_id, 'product_tag');

// Check if we have any data to display
$has_product_details = $product_weight || $metal || $diamond;
$has_diamond_details = $diamond && $diamond_quantity > 0;
$has_metal_details = $metal;
$has_price_breakup = $price_breakup && is_array($price_breakup);
$has_tags = !empty($tags) && !is_wp_error($tags);
?>

<div class="jpc-product-details-accordion">
    
    <!-- Silver Badge (if applicable) -->
    <?php if ($is_silver): ?>
    <div class="jpc-silver-badge">
        <span class="jpc-silver-icon">🥈</span>
        <span class="jpc-silver-text">PURE SILVER</span>
    </div>
    <?php endif; ?>
    
    <!-- Product Details Section -->
    <?php if ($has_product_details): ?>
    <div class="jpc-accordion-section jpc-active">
        <div class="jpc-accordion-header">
            <h3>PRODUCT DETAILS</h3>
            <span class="jpc-accordion-toggle">−</span>
        </div>
        <div class="jpc-accordion-content">
            <?php if ($product_weight): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">
                    Total Weight 
                    <span class="jpc-info-icon" title="Total weight of the product including all components">ⓘ</span>
                </span>
                <span class="jpc-detail-value"><?php echo number_format($product_weight, 2); ?> gram</span>
            </div>
            <?php endif; ?>
            
            <?php if ($metal): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Metal Type</span>
                <span class="jpc-detail-value"><?php echo esc_html($metal->display_name); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($metal_weight): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">
                    Metal Weight 
                    <span class="jpc-info-icon" title="Weight of metal used in the product">ⓘ</span>
                </span>
                <span class="jpc-detail-value"><?php echo number_format($metal_weight, 2); ?> gram</span>
            </div>
            <?php endif; ?>
            
            <?php if ($diamond && $diamond_quantity > 0): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Diamond</span>
                <span class="jpc-detail-value"><?php echo esc_html($diamond->display_name); ?> (<?php echo $diamond_quantity; ?> pcs)</span>
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
            <span class="jpc-accordion-toggle">−</span>
        </div>
        <div class="jpc-accordion-content">
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Type</span>
                <span class="jpc-detail-value"><?php echo esc_html($diamond_type_label); ?></span>
            </div>
            
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Certification</span>
                <span class="jpc-detail-value"><?php echo esc_html($diamond_cert_label); ?></span>
            </div>
            
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Carat</span>
                <span class="jpc-detail-value"><?php echo number_format($diamond->carat, 2); ?> ct</span>
            </div>
            
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Quantity</span>
                <span class="jpc-detail-value"><?php echo $diamond_quantity; ?> pieces</span>
            </div>
            
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Price Per Carat</span>
                <span class="jpc-detail-value">₹ <?php echo number_format($diamond->price_per_carat, 2); ?>/-</span>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Metal Details Section -->
    <?php if ($has_metal_details): ?>
    <div class="jpc-accordion-section">
        <div class="jpc-accordion-header">
            <h3>METAL DETAILS</h3>
            <span class="jpc-accordion-toggle">−</span>
        </div>
        <div class="jpc-accordion-content">
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Type</span>
                <span class="jpc-detail-value"><?php echo esc_html($metal_group ? $metal_group->name : $metal->name); ?></span>
            </div>
            
            <?php if ($metal_karat): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Karat</span>
                <span class="jpc-detail-value"><?php echo esc_html($metal_karat); ?></span>
            </div>
            <?php endif; ?>
            
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Rate Per Gram</span>
                <span class="jpc-detail-value">₹ <?php echo number_format($metal->price_per_unit, 2); ?>/-</span>
            </div>
            
            <?php if ($metal_weight): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">
                    Weight 
                    <span class="jpc-info-icon" title="Metal weight used in product">ⓘ</span>
                </span>
                <span class="jpc-detail-value"><?php echo number_format($metal_weight, 2); ?> gram</span>
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
            <span class="jpc-accordion-toggle">−</span>
        </div>
        <div class="jpc-accordion-content">
            <?php if (!empty($price_breakup['metal_price'])): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label"><?php echo $metal_group ? esc_html($metal_group->name) : 'Metal'; ?></span>
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
            
            <?php if (!empty($price_breakup['pearl_cost'])): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Pearl Cost</span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['pearl_cost'], 0); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($price_breakup['stone_cost'])): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Stone Cost</span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['stone_cost'], 0); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($price_breakup['extra_fee'])): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Extra Fee</span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['extra_fee'], 0); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <!-- Extra Fields #1-5 with custom labels -->
            <?php
            if (!empty($price_breakup['extra_fields']) && is_array($price_breakup['extra_fields'])) {
                foreach ($price_breakup['extra_fields'] as $extra_field) {
                    if (!empty($extra_field['value']) && $extra_field['value'] > 0) {
                        ?>
                        <div class="jpc-detail-row">
                            <span class="jpc-detail-label"><?php echo esc_html($extra_field['label']); ?></span>
                            <span class="jpc-detail-value">₹ <?php echo number_format($extra_field['value'], 0); ?>/-</span>
                        </div>
                        <?php
                    }
                }
            }
            ?>
            
            <!-- Subtotal (before Additional % and discount) -->
            <?php 
            $subtotal = 0;
            $subtotal += !empty($price_breakup['metal_price']) ? floatval($price_breakup['metal_price']) : 0;
            $subtotal += !empty($price_breakup['diamond_price']) ? floatval($price_breakup['diamond_price']) : 0;
            $subtotal += !empty($price_breakup['making_charge']) ? floatval($price_breakup['making_charge']) : 0;
            $subtotal += !empty($price_breakup['wastage_charge']) ? floatval($price_breakup['wastage_charge']) : 0;
            $subtotal += !empty($price_breakup['pearl_cost']) ? floatval($price_breakup['pearl_cost']) : 0;
            $subtotal += !empty($price_breakup['stone_cost']) ? floatval($price_breakup['stone_cost']) : 0;
            $subtotal += !empty($price_breakup['extra_fee']) ? floatval($price_breakup['extra_fee']) : 0;
            
            // Add extra fields to subtotal
            if (!empty($price_breakup['extra_fields']) && is_array($price_breakup['extra_fields'])) {
                foreach ($price_breakup['extra_fields'] as $extra_field) {
                    $subtotal += !empty($extra_field['value']) ? floatval($extra_field['value']) : 0;
                }
            }
            
            if ($subtotal > 0):
            ?>
            <div class="jpc-detail-row jpc-subtotal-row" style="border-top: 2px solid #ddd; margin-top: 10px; padding-top: 10px;">
                <span class="jpc-detail-label"><strong>Subtotal</strong></span>
                <span class="jpc-detail-value"><strong>₹ <?php echo number_format($subtotal, 0); ?>/-</strong></span>
            </div>
            <?php endif; ?>
            
            <!-- Additional Percentage -->
            <?php if (!empty($price_breakup['additional_percentage']) && $price_breakup['additional_percentage'] > 0): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label"><?php echo esc_html($price_breakup['additional_percentage_label'] ?? 'Additional Percentage'); ?></span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['additional_percentage'], 0); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <!-- Discount -->
            <?php if (!empty($price_breakup['discount'])): ?>
            <div class="jpc-detail-row" style="color: #28a745; background: #d4edda; padding: 12px 0;">
                <span class="jpc-detail-label">
                    <strong>Discount</strong>
                    <?php if ($discount_percentage > 0): ?>
                        <span style="font-weight: bold;">(<?php echo number_format($discount_percentage, 1); ?>% OFF)</span>
                    <?php endif; ?>
                </span>
                <span class="jpc-detail-value" style="font-weight: bold;">- ₹ <?php echo number_format($price_breakup['discount'], 0); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <!-- GST with percentage -->
            <?php if (!empty($price_breakup['gst'])): 
                $gst_label = !empty($price_breakup['gst_label']) ? $price_breakup['gst_label'] : 'GST';
                $gst_percentage = !empty($price_breakup['gst_percentage']) ? $price_breakup['gst_percentage'] : 0;
            ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">
                    <?php echo esc_html($gst_label); ?>
                    <?php if ($gst_percentage > 0): ?>
                        (<?php echo number_format($gst_percentage, 0); ?>%)
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
            
            <?php if (!empty($price_breakup['discount']) && $discount_percentage > 0): ?>
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
            <span class="jpc-accordion-toggle">−</span>
        </div>
        <div class="jpc-accordion-content">
            <div class="jpc-tags-list">
                <?php 
                $tag_links = array();
                foreach ($tags as $tag) {
                    $tag_links[] = '<a href="' . get_term_link($tag) . '">' . esc_html($tag->name) . '</a>';
                }
                echo implode(', ', $tag_links);
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
    background: #fff;
}

.jpc-silver-badge {
    background: linear-gradient(135deg, #C0C0C0 0%, #E8E8E8 50%, #C0C0C0 100%);
    padding: 12px 20px;
    text-align: center;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.jpc-silver-icon {
    font-size: 20px;
    color: #666;
}

.jpc-silver-text {
    font-weight: 600;
    color: #333;
    font-size: 14px;
    letter-spacing: 0.5px;
}

.jpc-accordion-section {
    border-bottom: 1px solid #e0e0e0;
}

.jpc-accordion-section:last-child {
    border-bottom: none;
}

.jpc-accordion-header {
    padding: 15px 20px;
    background: #f8f8f8;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background 0.3s ease;
}

.jpc-accordion-header:hover {
    background: #f0f0f0;
}

.jpc-accordion-header h3 {
    margin: 0;
    font-size: 14px;
    font-weight: 600;
    color: #333;
    letter-spacing: 0.5px;
}

.jpc-accordion-toggle {
    font-size: 20px;
    font-weight: bold;
    color: #666;
    transition: transform 0.3s ease;
}

.jpc-accordion-section.jpc-active .jpc-accordion-toggle {
    transform: rotate(180deg);
}

.jpc-accordion-content {
    padding: 15px 20px;
    display: none;
}

.jpc-accordion-section.jpc-active .jpc-accordion-content {
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
    font-size: 13px;
    color: #666;
}

.jpc-detail-value {
    font-size: 13px;
    font-weight: 500;
    color: #333;
}

.jpc-info-icon {
    display: inline-block;
    width: 14px;
    height: 14px;
    line-height: 14px;
    text-align: center;
    background: #e0e0e0;
    border-radius: 50%;
    font-size: 10px;
    color: #666;
    cursor: help;
    margin-left: 4px;
}

.jpc-subtotal-row {
    background: #f0f8ff;
}

.jpc-total-row {
    background: #e8f4f8;
    padding: 15px 0 !important;
    margin-top: 10px;
    border-top: 2px solid #0066cc !important;
}

.jpc-total-row .jpc-detail-label,
.jpc-total-row .jpc-detail-value {
    font-size: 16px;
    color: #0066cc;
}

.jpc-price-summary {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 2px solid #ddd;
}

.jpc-regular-price-row {
    padding: 10px 0;
}

.jpc-sale-price-row {
    padding: 10px 0;
    background: #f0f8ff;
    margin-top: 5px;
}

.jpc-strikethrough {
    text-decoration: line-through;
    color: #999 !important;
}

.jpc-savings-badge {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    padding: 12px 15px;
    margin-top: 15px;
    border-radius: 5px;
    text-align: center;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.jpc-tags-list {
    font-size: 13px;
}

.jpc-tags-list a {
    color: #0066cc;
    text-decoration: none;
}

.jpc-tags-list a:hover {
    text-decoration: underline;
}
</style>

<script>
jQuery(document).ready(function($) {
    $('.jpc-accordion-header').on('click', function() {
        var section = $(this).closest('.jpc-accordion-section');
        section.toggleClass('jpc-active');
    });
});
</script>
