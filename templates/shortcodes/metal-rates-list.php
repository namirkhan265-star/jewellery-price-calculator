<?php
/**
 * Metal Rates List Shortcode Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="jpc-metal-rates">
    <ul class="jpc-metal-rates-list">
        <?php foreach ($metals as $metal): ?>
        <li>
            <span class="jpc-metal-name"><?php echo esc_html($metal->display_name); ?></span>
            <span class="jpc-metal-price">₹<?php echo number_format($metal->price_per_unit, 2); ?>/<?php echo esc_html($metal->unit); ?></span>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
