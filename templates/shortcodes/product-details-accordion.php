<?php
/**
 * Product Details Accordion Template
 * Displays product details, diamond details, and metal details
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

// Get product tags
$tags = wp_get_post_terms($product_id, 'product_tag');

// Check if we have any data to display
$has_product_details = $product_weight || $metal || $diamond;
$has_diamond_details = $diamond && $diamond_quantity > 0;
$has_metal_details = $metal;
$has_tags = !empty($tags);
?>

<?php if ($has_product_details || $has_diamond_details || $has_metal_details || $has_tags): ?>
<div class="jpc-product-details-accordion">
    
    <!-- Silver Badge (if applicable) -->
    <?php if ($is_silver): ?>
    <div class="jpc-silver-badge">
        <span class="jpc-silver-icon">✦</span>
        <span class="jpc-silver-text">Made With Pure 925 Silver</span>
    </div>
    <?php endif; ?>
    
    <!-- Product Details Section -->
    <?php if ($has_product_details): ?>
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
            
            <?php if ($metal): ?>
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
    
    <!-- Diamond Details Section -->
    <?php if ($has_diamond_details): ?>
    <div class="jpc-accordion-section">
        <div class="jpc-accordion-header">
            <h3>DIAMOND DETAILS</h3>
            <span class="jpc-accordion-toggle">+</span>
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
    transform: rotate(45deg);
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
    flex: 1;
}

.jpc-detail-value {
    font-size: 13px;
    color: #333;
    font-weight: 500;
    text-align: right;
}

.jpc-info-icon {
    display: inline-block;
    width: 14px;
    height: 14px;
    line-height: 14px;
    text-align: center;
    border-radius: 50%;
    background: #e0e0e0;
    color: #666;
    font-size: 10px;
    cursor: help;
    margin-left: 4px;
}

.jpc-tags-list {
    font-size: 13px;
    line-height: 1.8;
}

.jpc-tags-list a {
    color: #666;
    text-decoration: none;
    transition: color 0.3s ease;
}

.jpc-tags-list a:hover {
    color: #333;
    text-decoration: underline;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .jpc-accordion-header h3 {
        font-size: 13px;
    }
    
    .jpc-detail-label,
    .jpc-detail-value {
        font-size: 12px;
    }
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
<?php endif; ?>
