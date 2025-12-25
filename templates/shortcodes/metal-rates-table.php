<?php
/**
 * Metal Rates Table Shortcode Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="jpc-metal-rates">
    <table class="jpc-metal-rates-table">
        <thead>
            <tr>
                <th><?php _e('Metal', 'jewellery-price-calc'); ?></th>
                <th><?php _e('Price per Unit', 'jewellery-price-calc'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($metals as $metal): ?>
            <tr>
                <td class="jpc-metal-name"><?php echo esc_html($metal->display_name); ?></td>
                <td class="jpc-metal-price">₹<?php echo number_format($metal->price_per_unit, 2); ?>/<?php echo esc_html($metal->unit); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
