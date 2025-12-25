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
if ($metal_id) {
    $metal = JPC_Metals::get_by_id($metal_id);
    if ($metal) {
        $metal_group = JPC_Metal_Groups::get_by_id($metal->metal_group_id);
        // Extract karat from metal name (e.g., "22K Gold" -> "22K")
        if (preg_match('/(\d+K)/i', $metal->name, $matches)) {
            $metal_karat = $matches[1];
        }
    }
}

// Get price breakup
$price_breakup = get_post_meta($product_id, '_jpc_price_breakup', true);

// Get product tags
$tags = wp_get_post_terms($product_id, 'product_tag');
?>

<div class="jpc-product-details-accordion">
    
    <!-- Product Details Section -->
    <div class="jpc-accordion-section jpc-active">
        <div class="jpc-accordion-header">
            <h3>PRODUCT DETAILS</h3>
        </div>
        <div class="jpc-accordion-content">
            <?php if ($product_weight): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">
                    Product Weight 
                    <span class="jpc-info-icon" title="Total product weight including all components">ⓘ</span>
                </span>
                <span class="jpc-detail-value"><?php echo number_format($product_weight, 2); ?> gram</span>
            </div>
            <?php endif; ?>
            
            <?php if ($metal && $metal_karat): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Metal Type</span>
                <span class="jpc-detail-value"><?php echo esc_html($metal->name); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($diamond): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Diamond Type</span>
                <span class="jpc-detail-value"><?php echo esc_html($diamond_type_label); ?></span>
            </div>
            
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Certificate</span>
                <span class="jpc-detail-value"><?php echo esc_html($diamond_cert_label); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Diamond Details Section -->
    <?php if ($diamond && $diamond_quantity > 0): ?>
    <div class="jpc-accordion-section">
        <div class="jpc-accordion-header">
            <h3>DIAMOND DETAILS</h3>
            <span class="jpc-accordion-toggle">−</span>
        </div>
        <div class="jpc-accordion-content">
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">
                    Total Weight 
                    <span class="jpc-info-icon" title="Weight per diamond">ⓘ</span>
                </span>
                <span class="jpc-detail-value"><?php echo number_format($diamond->carat, 3); ?> Ct</span>
            </div>
            
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Total No. Of Diamonds</span>
                <span class="jpc-detail-value"><?php echo esc_html($diamond_quantity); ?></span>
            </div>
            
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Price Per Carat</span>
                <span class="jpc-detail-value">₹ <?php echo number_format($diamond->price_per_carat, 0); ?>/-</span>
            </div>
            
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Diamond Type</span>
                <span class="jpc-detail-value"><?php echo esc_html($diamond_type_label); ?></span>
            </div>
            
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Certificate</span>
                <span class="jpc-detail-value"><?php echo esc_html($diamond_cert_label); ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Metal Details Section -->
    <?php if ($metal): ?>
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
    <?php if ($price_breakup && is_array($price_breakup)): ?>
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
            
            <?php if (!empty($price_breakup['discount'])): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Discount</span>
                <span class="jpc-detail-value">- ₹ <?php echo number_format($price_breakup['discount'], 0); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($price_breakup['gst'])): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">GST</span>
                <span class="jpc-detail-value">₹ <?php echo number_format($price_breakup['gst'], 0); ?>/-</span>
            </div>
            <?php endif; ?>
            
            <div class="jpc-detail-row jpc-total-row">
                <span class="jpc-detail-label"><strong>Total</strong></span>
                <span class="jpc-detail-value"><strong>₹ <?php echo number_format($price_breakup['final_price'], 0); ?>/-</strong></span>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Tags Section -->
    <?php if (!empty($tags)): ?>
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
    max-width: 1200px;
    margin: 20px 0;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 4px;
    padding: 0 24px;
}

.jpc-accordion-section {
    border-bottom: 1px solid #e5e5e5;
}

.jpc-accordion-section:last-child {
    border-bottom: none;
}

.jpc-accordion-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px 0;
    cursor: pointer;
    user-select: none;
}

.jpc-accordion-header h3 {
    margin: 0;
    font-size: 15px;
    font-weight: 600;
    letter-spacing: 0.8px;
    color: #1a1a1a;
    text-transform: uppercase;
}

.jpc-accordion-toggle {
    font-size: 28px;
    font-weight: 300;
    color: #1a1a1a;
    line-height: 1;
    transition: transform 0.2s ease;
    min-width: 20px;
    text-align: center;
}

.jpc-accordion-section:not(.jpc-active) .jpc-accordion-toggle {
    transform: rotate(0deg);
}

.jpc-accordion-section.jpc-active .jpc-accordion-toggle {
    transform: rotate(0deg);
}

.jpc-accordion-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
    padding: 0;
}

.jpc-accordion-section.jpc-active .jpc-accordion-content {
    max-height: 2000px;
    padding-bottom: 24px;
}

.jpc-detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 0;
    border-bottom: 1px solid #f5f5f5;
}

.jpc-detail-row:last-child {
    border-bottom: none;
}

.jpc-detail-label {
    color: #5b7fa4;
    font-size: 15px;
    font-weight: 400;
    display: flex;
    align-items: center;
    gap: 6px;
}

.jpc-detail-value {
    color: #5b7fa4;
    font-size: 15px;
    font-weight: 500;
    text-align: right;
}

.jpc-total-row {
    margin-top: 8px;
    padding-top: 16px;
    border-top: 2px solid #e5e5e5 !important;
}

.jpc-total-row .jpc-detail-label,
.jpc-total-row .jpc-detail-value {
    color: #1a1a1a;
    font-size: 16px;
}

.jpc-info-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    background: #d0d0d0;
    color: #fff;
    border-radius: 50%;
    font-size: 11px;
    font-weight: 600;
    cursor: help;
    font-style: normal;
}

.jpc-tags-list {
    color: #5b7fa4;
    font-size: 15px;
    line-height: 1.6;
}

.jpc-tags-list a {
    color: #5b7fa4;
    text-decoration: none;
    transition: color 0.2s ease;
}

.jpc-tags-list a:hover {
    color: #3d5a7a;
    text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 768px) {
    .jpc-product-details-accordion {
        padding: 0 16px;
    }
    
    .jpc-accordion-header {
        padding: 18px 0;
    }
    
    .jpc-accordion-header h3 {
        font-size: 14px;
    }
    
    .jpc-detail-label,
    .jpc-detail-value {
        font-size: 14px;
    }
    
    .jpc-detail-row {
        padding: 12px 0;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Toggle accordion sections
    $('.jpc-accordion-header').on('click', function() {
        var section = $(this).closest('.jpc-accordion-section');
        var toggle = $(this).find('.jpc-accordion-toggle');
        
        // Toggle active state
        section.toggleClass('jpc-active');
        
        // Update toggle icon
        if (section.hasClass('jpc-active')) {
            toggle.text('−');
        } else {
            toggle.text('+');
        }
    });
});
</script>
