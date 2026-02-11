<?php
/**
 * Migration v2.5.10 Admin Page
 */

if (!defined('ABSPATH')) {
    exit;
}

// Handle migration request
$migration_result = null;
if (isset($_POST['run_migration']) && check_admin_referer('jpc_migration_v2510')) {
    $migration_result = JPC_Data_Migration_v2510::migrate();
}

$is_migration_needed = JPC_Data_Migration_v2510::is_migration_needed();
$migration_completed = get_option('jpc_migration_v2510_completed');
$migration_count = get_option('jpc_migration_v2510_count', 0);
$migration_date = get_option('jpc_migration_v2510_date');
?>

<div class="wrap">
    <h1><?php _e('Data Migration v2.5.10', 'jewellery-price-calc'); ?></h1>
    
    <div class="notice notice-info">
        <p>
            <strong><?php _e('What does this migration do?', 'jewellery-price-calc'); ?></strong><br>
            <?php _e('This migration converts additional cost fields (Pearl Cost, Stone Cost, Extra Fee) from the old format to the new format that supports both fixed prices and percentages.', 'jewellery-price-calc'); ?>
        </p>
        <p>
            <strong><?php _e('Changes:', 'jewellery-price-calc'); ?></strong>
        </p>
        <ul style="list-style: disc; margin-left: 20px;">
            <li><?php _e('OLD: _jpc_pearl_cost → NEW: _jpc_pearl_cost_value + _jpc_pearl_cost_type', 'jewellery-price-calc'); ?></li>
            <li><?php _e('OLD: _jpc_stone_cost → NEW: _jpc_stone_cost_value + _jpc_stone_cost_type', 'jewellery-price-calc'); ?></li>
            <li><?php _e('OLD: _jpc_extra_fee → NEW: _jpc_extra_fee_value + _jpc_extra_fee_type', 'jewellery-price-calc'); ?></li>
        </ul>
        <p>
            <?php _e('After migration, all products will have their price breakups regenerated to show the correct values.', 'jewellery-price-calc'); ?>
        </p>
    </div>
    
    <?php if ($migration_result): ?>
        <?php if ($migration_result['success']): ?>
            <div class="notice notice-success">
                <p><strong><?php echo esc_html($migration_result['message']); ?></strong></p>
            </div>
        <?php else: ?>
            <div class="notice notice-error">
                <p><strong><?php _e('Migration failed!', 'jewellery-price-calc'); ?></strong></p>
                <p><?php echo esc_html($migration_result['message']); ?></p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <?php if ($migration_completed): ?>
        <div class="notice notice-success">
            <p>
                <strong><?php _e('Migration already completed!', 'jewellery-price-calc'); ?></strong><br>
                <?php printf(__('Migrated %d products on %s', 'jewellery-price-calc'), $migration_count, $migration_date); ?>
            </p>
        </div>
        
        <form method="post" onsubmit="return confirm('<?php _e('Are you sure you want to run the migration again? This will re-process all products.', 'jewellery-price-calc'); ?>');">
            <?php wp_nonce_field('jpc_migration_v2510'); ?>
            <input type="hidden" name="run_migration" value="1">
            <p>
                <button type="submit" class="button button-secondary">
                    <?php _e('Run Migration Again', 'jewellery-price-calc'); ?>
                </button>
            </p>
        </form>
    <?php elseif ($is_migration_needed): ?>
        <div class="notice notice-warning">
            <p>
                <strong><?php _e('Migration Required!', 'jewellery-price-calc'); ?></strong><br>
                <?php _e('Your products need to be migrated to the new format. Click the button below to start the migration.', 'jewellery-price-calc'); ?>
            </p>
        </div>
        
        <form method="post" onsubmit="return confirm('<?php _e('Are you sure you want to run the migration? This will update all products with additional cost fields.', 'jewellery-price-calc'); ?>');">
            <?php wp_nonce_field('jpc_migration_v2510'); ?>
            <input type="hidden" name="run_migration" value="1">
            <p>
                <button type="submit" class="button button-primary button-large">
                    <?php _e('Run Migration Now', 'jewellery-price-calc'); ?>
                </button>
            </p>
        </form>
    <?php else: ?>
        <div class="notice notice-info">
            <p>
                <strong><?php _e('No migration needed!', 'jewellery-price-calc'); ?></strong><br>
                <?php _e('No products found with old additional cost field format.', 'jewellery-price-calc'); ?>
            </p>
        </div>
    <?php endif; ?>
    
    <hr>
    
    <h2><?php _e('Manual Fix', 'jewellery-price-calc'); ?></h2>
    <p><?php _e('If you prefer to manually update your products:', 'jewellery-price-calc'); ?></p>
    <ol>
        <li><?php _e('Go to each product with additional costs', 'jewellery-price-calc'); ?></li>
        <li><?php _e('Re-enter the values in the "Other Costs" section', 'jewellery-price-calc'); ?></li>
        <li><?php _e('Click "Update" to save the product', 'jewellery-price-calc'); ?></li>
        <li><?php _e('The price breakup will be automatically regenerated', 'jewellery-price-calc'); ?></li>
    </ol>
</div>
