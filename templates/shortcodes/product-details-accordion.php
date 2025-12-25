<?php
/**
 * Product Details Accordion Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="jpc-product-details-accordion">
    
    <!-- Product Details Section -->
    <div class="jpc-accordion-section">
        <div class="jpc-accordion-header">
            <h3>PRODUCT DETAILS</h3>
        </div>
        <div class="jpc-accordion-content jpc-active">
            <?php if ($product->get_sku()): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Product Code</span>
                <span class="jpc-detail-value"><?php echo esc_html($product->get_sku()); ?></span>
            </div>
            <?php endif; ?>
            
            <?php 
            // Get custom fields for length
            $length = get_post_meta($product_id, '_jpc_extra_field_1', true);
            if ($length): 
            ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">
                    Length 
                    <span class="jpc-info-icon" title="Product length">ⓘ</span>
                </span>
                <span class="jpc-detail-value"><?php echo esc_html($length); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($metal_weight): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">
                    Product Weight 
                    <span class="jpc-info-icon" title="Total product weight">ⓘ</span>
                </span>
                <span class="jpc-detail-value"><?php echo number_format($metal_weight, 2); ?> gram</span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Diamond Details Section -->
    <?php if ($diamond && $diamond_quantity > 0): ?>
    <div class="jpc-accordion-section">
        <div class="jpc-accordion-header">
            <h3>DIAMOND DETAILS</h3>
            <span class="jpc-accordion-toggle">+</span>
        </div>
        <div class="jpc-accordion-content">
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">
                    Total Weight 
                    <span class="jpc-info-icon" title="Total diamond weight">ⓘ</span>
                </span>
                <span class="jpc-detail-value"><?php echo number_format($diamond->carat * $diamond_quantity, 3); ?> Ct</span>
            </div>
            
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Total No. Of Diamonds</span>
                <span class="jpc-detail-value"><?php echo esc_html($diamond_quantity); ?></span>
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
        <div class="jpc-accordion-content jpc-active">
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">Type</span>
                <span class="jpc-detail-value"><?php echo esc_html($metal->name); ?></span>
            </div>
            
            <?php if ($metal_weight): ?>
            <div class="jpc-detail-row">
                <span class="jpc-detail-label">
                    Weight 
                    <span class="jpc-info-icon" title="Metal weight">ⓘ</span>
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
        <div class="jpc-accordion-content jpc-active">
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
        <div class="jpc-accordion-content jpc-active">
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
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
}

.jpc-accordion-section {
    border-bottom: 1px solid #e5e5e5;
    margin-bottom: 0;
}

.jpc-accordion-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 0;
    cursor: pointer;
    user-select: none;
}

.jpc-accordion-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    letter-spacing: 0.5px;
    color: #000;
}

.jpc-accordion-toggle {
    font-size: 24px;
    font-weight: 300;
    color: #666;
    transition: transform 0.3s ease;
}

.jpc-accordion-section.jpc-collapsed .jpc-accordion-toggle {
    transform: rotate(0deg);
}

.jpc-accordion-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
    padding: 0;
}

.jpc-accordion-content.jpc-active {
    max-height: 1000px;
    padding-bottom: 20px;
}

.jpc-detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f5f5f5;
}

.jpc-detail-row:last-child {
    border-bottom: none;
}

.jpc-detail-label {
    color: #5b7fa4;
    font-size: 14px;
    font-weight: 400;
    display: flex;
    align-items: center;
    gap: 5px;
}

.jpc-detail-value {
    color: #5b7fa4;
    font-size: 14px;
    font-weight: 500;
    text-align: right;
}

.jpc-info-icon {
    display: inline-block;
    width: 16px;
    height: 16px;
    background: #ccc;
    color: #fff;
    border-radius: 50%;
    text-align: center;
    line-height: 16px;
    font-size: 11px;
    cursor: help;
}

.jpc-total-row {
    margin-top: 10px;
    padding-top: 15px;
    border-top: 2px solid #e5e5e5 !important;
}

.jpc-total-row .jpc-detail-label,
.jpc-total-row .jpc-detail-value {
    font-size: 15px;
    color: #000;
}

.jpc-tags-list {
    color: #5b7fa4;
    font-size: 14px;
    line-height: 1.8;
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

/* Responsive */
@media (max-width: 768px) {
    .jpc-accordion-header h3 {
        font-size: 14px;
    }
    
    .jpc-detail-label,
    .jpc-detail-value {
        font-size: 13px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    $('.jpc-accordion-header').on('click', function() {
        const $section = $(this).closest('.jpc-accordion-section');
        const $content = $section.find('.jpc-accordion-content');
        const $toggle = $section.find('.jpc-accordion-toggle');
        
        // Toggle active state
        $content.toggleClass('jpc-active');
        $section.toggleClass('jpc-collapsed');
        
        // Update toggle icon
        if ($content.hasClass('jpc-active')) {
            $toggle.text('−');
        } else {
            $toggle.text('+');
        }
    });
});
</script>
