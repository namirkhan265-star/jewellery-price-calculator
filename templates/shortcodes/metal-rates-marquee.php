<?php
/**
 * Metal Rates Marquee Shortcode Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="jpc-metal-rates-marquee">
    <div class="jpc-metal-rates-marquee-content">
        <?php foreach ($metals as $metal): ?>
        <span class="jpc-metal-rates-marquee-item">
            <span class="metal-name"><?php echo esc_html($metal->display_name); ?>:</span>
            <span class="metal-price">₹<?php echo number_format($metal->price_per_unit, 2); ?>/<?php echo esc_html($metal->unit); ?></span>
        </span>
        <?php endforeach; ?>
    </div>
</div>
