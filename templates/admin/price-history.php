<?php
/**
 * Price History Page Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$price_history = JPC_Metals::get_price_history(100);
?>

<div class="wrap jpc-admin-wrap">
    <h1><?php _e('Price History', 'jewellery-price-calc'); ?></h1>
    
    <div class="jpc-admin-content">
        <div class="jpc-card">
            <h2><?php _e('Metal Price Change History', 'jewellery-price-calc'); ?></h2>
            
            <?php if (empty($price_history)): ?>
                <p><?php _e('No price history found.', 'jewellery-price-calc'); ?></p>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped jpc-price-history-table">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="20%"><?php _e('Metal', 'jewellery-price-calc'); ?></th>
                            <th width="15%"><?php _e('Old Price', 'jewellery-price-calc'); ?></th>
                            <th width="15%"><?php _e('New Price', 'jewellery-price-calc'); ?></th>
                            <th width="15%"><?php _e('Change', 'jewellery-price-calc'); ?></th>
                            <th width="15%"><?php _e('Changed By', 'jewellery-price-calc'); ?></th>
                            <th width="15%"><?php _e('Date & Time', 'jewellery-price-calc'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($price_history as $index => $history): ?>
                            <?php 
                            $change = $history->new_price - $history->old_price;
                            $change_class = $change > 0 ? 'price-increase' : 'price-decrease';
                            $change_symbol = $change > 0 ? '+' : '';
                            ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo esc_html($history->metal_name); ?></td>
                                <td>₹<?php echo number_format($history->old_price, 2); ?></td>
                                <td>₹<?php echo number_format($history->new_price, 2); ?></td>
                                <td class="<?php echo $change_class; ?>">
                                    <?php echo $change_symbol; ?>₹<?php echo number_format(abs($change), 2); ?>
                                    (<?php echo $change_symbol; ?><?php echo number_format(($change / $history->old_price) * 100, 2); ?>%)
                                </td>
                                <td><?php echo esc_html($history->user_name); ?></td>
                                <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($history->changed_at)); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <div class="jpc-card">
            <h2><?php _e('Statistics', 'jewellery-price-calc'); ?></h2>
            
            <?php if (!empty($price_history)): ?>
                <?php
                $total_changes = count($price_history);
                $increases = 0;
                $decreases = 0;
                
                foreach ($price_history as $history) {
                    if ($history->new_price > $history->old_price) {
                        $increases++;
                    } else {
                        $decreases++;
                    }
                }
                ?>
                
                <table class="form-table">
                    <tr>
                        <th><?php _e('Total Price Changes:', 'jewellery-price-calc'); ?></th>
                        <td><?php echo $total_changes; ?></td>
                    </tr>
                    <tr>
                        <th><?php _e('Price Increases:', 'jewellery-price-calc'); ?></th>
                        <td class="price-increase"><?php echo $increases; ?></td>
                    </tr>
                    <tr>
                        <th><?php _e('Price Decreases:', 'jewellery-price-calc'); ?></th>
                        <td class="price-decrease"><?php echo $decreases; ?></td>
                    </tr>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
