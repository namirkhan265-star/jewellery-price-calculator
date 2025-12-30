<?php
/**
 * Price History Page Template
 * Enhanced with filters, pagination, and delete functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get filter parameters
$item_type = isset($_GET['item_type']) ? sanitize_text_field($_GET['item_type']) : 'all';
$date_from = isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : '';
$paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$per_page = 20;

// Build filter args
$filter_args = array(
    'limit' => $per_page,
    'offset' => ($paged - 1) * $per_page,
    'item_type' => $item_type,
    'date_from' => $date_from,
    'date_to' => $date_to,
);

$price_history = JPC_Metals::get_price_history($filter_args);
$total_items = JPC_Metals::get_price_history_count($filter_args);
$total_pages = ceil($total_items / $per_page);
?>

<div class="wrap jpc-admin-wrap">
    <h1><?php _e('Price History', 'jewellery-price-calc'); ?></h1>
    
    <div class="jpc-admin-content">
        <!-- Filters Card -->
        <div class="jpc-card">
            <h2><?php _e('Filters', 'jewellery-price-calc'); ?></h2>
            
            <form method="get" action="" class="jpc-filter-form">
                <input type="hidden" name="page" value="jpc-price-history">
                
                <div class="jpc-filter-row">
                    <div class="jpc-filter-field">
                        <label for="item_type"><?php _e('Item Type:', 'jewellery-price-calc'); ?></label>
                        <select name="item_type" id="item_type">
                            <option value="all" <?php selected($item_type, 'all'); ?>><?php _e('All Items', 'jewellery-price-calc'); ?></option>
                            <option value="metal" <?php selected($item_type, 'metal'); ?>><?php _e('Metals Only', 'jewellery-price-calc'); ?></option>
                            <option value="diamond" <?php selected($item_type, 'diamond'); ?>><?php _e('Diamonds Only', 'jewellery-price-calc'); ?></option>
                        </select>
                    </div>
                    
                    <div class="jpc-filter-field">
                        <label for="date_from"><?php _e('From Date:', 'jewellery-price-calc'); ?></label>
                        <input type="date" name="date_from" id="date_from" value="<?php echo esc_attr($date_from); ?>">
                    </div>
                    
                    <div class="jpc-filter-field">
                        <label for="date_to"><?php _e('To Date:', 'jewellery-price-calc'); ?></label>
                        <input type="date" name="date_to" id="date_to" value="<?php echo esc_attr($date_to); ?>">
                    </div>
                    
                    <div class="jpc-filter-field">
                        <button type="submit" class="button button-primary"><?php _e('Apply Filters', 'jewellery-price-calc'); ?></button>
                        <a href="?page=jpc-price-history" class="button"><?php _e('Clear Filters', 'jewellery-price-calc'); ?></a>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Price History Table Card -->
        <div class="jpc-card">
            <div class="jpc-card-header">
                <h2><?php _e('Price Change History', 'jewellery-price-calc'); ?></h2>
                <div class="jpc-card-actions">
                    <button type="button" id="jpc-delete-selected" class="button button-secondary" disabled>
                        <?php _e('Delete Selected', 'jewellery-price-calc'); ?>
                    </button>
                </div>
            </div>
            
            <?php if (empty($price_history)): ?>
                <p><?php _e('No price history found.', 'jewellery-price-calc'); ?></p>
            <?php else: ?>
                <form id="jpc-price-history-form">
                    <table class="wp-list-table widefat fixed striped jpc-price-history-table">
                        <thead>
                            <tr>
                                <th width="3%" class="check-column">
                                    <input type="checkbox" id="jpc-select-all">
                                </th>
                                <th width="5%">#</th>
                                <th width="10%"><?php _e('Type', 'jewellery-price-calc'); ?></th>
                                <th width="20%"><?php _e('Item Name', 'jewellery-price-calc'); ?></th>
                                <th width="12%"><?php _e('Old Price', 'jewellery-price-calc'); ?></th>
                                <th width="12%"><?php _e('New Price', 'jewellery-price-calc'); ?></th>
                                <th width="13%"><?php _e('Change', 'jewellery-price-calc'); ?></th>
                                <th width="12%"><?php _e('Changed By', 'jewellery-price-calc'); ?></th>
                                <th width="13%"><?php _e('Date & Time', 'jewellery-price-calc'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($price_history as $index => $history): ?>
                                <?php 
                                $change = $history->new_price - $history->old_price;
                                $change_class = $change > 0 ? 'price-increase' : 'price-decrease';
                                $change_symbol = $change > 0 ? '+' : '';
                                $item_type_label = ucfirst($history->item_type ?? 'metal');
                                $item_name = $history->item_name ?? $history->metal_name ?? 'Unknown';
                                ?>
                                <tr>
                                    <th class="check-column">
                                        <input type="checkbox" name="history_ids[]" value="<?php echo $history->id; ?>" class="jpc-history-checkbox">
                                    </th>
                                    <td><?php echo (($paged - 1) * $per_page) + $index + 1; ?></td>
                                    <td>
                                        <span class="jpc-item-type-badge jpc-item-type-<?php echo esc_attr($history->item_type ?? 'metal'); ?>">
                                            <?php echo esc_html($item_type_label); ?>
                                        </span>
                                    </td>
                                    <td><?php echo esc_html($item_name); ?></td>
                                    <td>₹<?php echo number_format($history->old_price, 2); ?></td>
                                    <td>₹<?php echo number_format($history->new_price, 2); ?></td>
                                    <td class="<?php echo $change_class; ?>">
                                        <?php echo $change_symbol; ?>₹<?php echo number_format(abs($change), 2); ?>
                                        <?php if ($history->old_price > 0): ?>
                                            (<?php echo $change_symbol; ?><?php echo number_format(($change / $history->old_price) * 100, 2); ?>%)
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo esc_html($history->user_name ?? 'Unknown'); ?></td>
                                    <td><?php echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($history->changed_at)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </form>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="tablenav bottom">
                        <div class="tablenav-pages">
                            <span class="displaying-num">
                                <?php printf(__('%s items', 'jewellery-price-calc'), number_format_i18n($total_items)); ?>
                            </span>
                            <span class="pagination-links">
                                <?php
                                $base_url = add_query_arg(array(
                                    'page' => 'jpc-price-history',
                                    'item_type' => $item_type,
                                    'date_from' => $date_from,
                                    'date_to' => $date_to,
                                ));
                                
                                // First page
                                if ($paged > 1) {
                                    echo '<a class="first-page button" href="' . esc_url(add_query_arg('paged', 1, $base_url)) . '">«</a>';
                                    echo '<a class="prev-page button" href="' . esc_url(add_query_arg('paged', $paged - 1, $base_url)) . '">‹</a>';
                                } else {
                                    echo '<span class="tablenav-pages-navspan button disabled">«</span>';
                                    echo '<span class="tablenav-pages-navspan button disabled">‹</span>';
                                }
                                
                                // Page numbers
                                echo '<span class="paging-input">';
                                echo '<span class="tablenav-paging-text">';
                                printf(__('%1$s of %2$s', 'jewellery-price-calc'), 
                                    '<span class="current-page">' . $paged . '</span>',
                                    '<span class="total-pages">' . $total_pages . '</span>'
                                );
                                echo '</span>';
                                echo '</span>';
                                
                                // Last page
                                if ($paged < $total_pages) {
                                    echo '<a class="next-page button" href="' . esc_url(add_query_arg('paged', $paged + 1, $base_url)) . '">›</a>';
                                    echo '<a class="last-page button" href="' . esc_url(add_query_arg('paged', $total_pages, $base_url)) . '">»</a>';
                                } else {
                                    echo '<span class="tablenav-pages-navspan button disabled">›</span>';
                                    echo '<span class="tablenav-pages-navspan button disabled">»</span>';
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <!-- Statistics Card -->
        <div class="jpc-card">
            <h2><?php _e('Statistics', 'jewellery-price-calc'); ?></h2>
            
            <?php if (!empty($price_history)): ?>
                <?php
                $increases = 0;
                $decreases = 0;
                $metal_changes = 0;
                $diamond_changes = 0;
                
                // Get all history for stats (not just current page)
                $all_history = JPC_Metals::get_price_history(array(
                    'limit' => 1000,
                    'item_type' => $item_type,
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                ));
                
                foreach ($all_history as $history) {
                    if ($history->new_price > $history->old_price) {
                        $increases++;
                    } else {
                        $decreases++;
                    }
                    
                    $type = $history->item_type ?? 'metal';
                    if ($type === 'metal') {
                        $metal_changes++;
                    } else {
                        $diamond_changes++;
                    }
                }
                ?>
                
                <table class="form-table">
                    <tr>
                        <th><?php _e('Total Price Changes:', 'jewellery-price-calc'); ?></th>
                        <td><strong><?php echo $total_items; ?></strong></td>
                    </tr>
                    <?php if ($item_type === 'all'): ?>
                    <tr>
                        <th><?php _e('Metal Changes:', 'jewellery-price-calc'); ?></th>
                        <td><?php echo $metal_changes; ?></td>
                    </tr>
                    <tr>
                        <th><?php _e('Diamond Changes:', 'jewellery-price-calc'); ?></th>
                        <td><?php echo $diamond_changes; ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th><?php _e('Price Increases:', 'jewellery-price-calc'); ?></th>
                        <td class="price-increase"><strong><?php echo $increases; ?></strong></td>
                    </tr>
                    <tr>
                        <th><?php _e('Price Decreases:', 'jewellery-price-calc'); ?></th>
                        <td class="price-decrease"><strong><?php echo $decreases; ?></strong></td>
                    </tr>
                </table>
            <?php else: ?>
                <p><?php _e('No statistics available.', 'jewellery-price-calc'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.jpc-filter-form {
    margin: 20px 0;
}

.jpc-filter-row {
    display: flex;
    gap: 15px;
    align-items: flex-end;
    flex-wrap: wrap;
}

.jpc-filter-field {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.jpc-filter-field label {
    font-weight: 600;
    font-size: 13px;
}

.jpc-filter-field select,
.jpc-filter-field input[type="date"] {
    min-width: 150px;
    height: 32px;
}

.jpc-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.jpc-card-header h2 {
    margin: 0;
}

.jpc-item-type-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.jpc-item-type-metal {
    background: #ffd700;
    color: #000;
}

.jpc-item-type-diamond {
    background: #b9f2ff;
    color: #000;
}

.price-increase {
    color: #46b450;
    font-weight: 600;
}

.price-decrease {
    color: #dc3232;
    font-weight: 600;
}

.tablenav {
    padding: 10px 0;
}

.tablenav-pages {
    float: right;
}

.pagination-links {
    margin-left: 10px;
}

.pagination-links .button {
    margin: 0 2px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Select all checkbox
    $('#jpc-select-all').on('change', function() {
        $('.jpc-history-checkbox').prop('checked', $(this).prop('checked'));
        updateDeleteButton();
    });
    
    // Individual checkboxes
    $('.jpc-history-checkbox').on('change', function() {
        updateDeleteButton();
        
        // Update select all checkbox
        var total = $('.jpc-history-checkbox').length;
        var checked = $('.jpc-history-checkbox:checked').length;
        $('#jpc-select-all').prop('checked', total === checked);
    });
    
    // Update delete button state
    function updateDeleteButton() {
        var checked = $('.jpc-history-checkbox:checked').length;
        $('#jpc-delete-selected').prop('disabled', checked === 0);
    }
    
    // Delete selected
    $('#jpc-delete-selected').on('click', function() {
        var checked = $('.jpc-history-checkbox:checked');
        
        if (checked.length === 0) {
            return;
        }
        
        if (!confirm('<?php _e('Are you sure you want to delete the selected price history entries? This action cannot be undone.', 'jewellery-price-calc'); ?>')) {
            return;
        }
        
        var ids = [];
        checked.each(function() {
            ids.push($(this).val());
        });
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'jpc_delete_price_history',
                nonce: '<?php echo wp_create_nonce('jpc_admin_nonce'); ?>',
                ids: ids
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message || '<?php _e('Failed to delete entries', 'jewellery-price-calc'); ?>');
                }
            },
            error: function() {
                alert('<?php _e('An error occurred. Please try again.', 'jewellery-price-calc'); ?>');
            }
        });
    });
});
</script>
