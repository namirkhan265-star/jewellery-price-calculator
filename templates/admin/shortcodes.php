<?php
/**
 * Shortcodes Documentation Page Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap jpc-admin-wrap">
    <h1><?php _e('Shortcodes', 'jewellery-price-calc'); ?></h1>
    
    <div class="jpc-admin-content">
        <div class="jpc-card">
            <h2><?php _e('Available Shortcodes', 'jewellery-price-calc'); ?></h2>
            
            <h3><?php _e('1. Display Metal Rates - List View', 'jewellery-price-calc'); ?></h3>
            <p><?php _e('Display today\'s metal rates in a simple list format.', 'jewellery-price-calc'); ?></p>
            <code>[jpc_metal_rates]</code>
            <p><?php _e('or', 'jewellery-price-calc'); ?></p>
            <code>[jpc_metal_rates template="list"]</code>
            
            <hr>
            
            <h3><?php _e('2. Display Metal Rates - Table View', 'jewellery-price-calc'); ?></h3>
            <p><?php _e('Display today\'s metal rates in a table format.', 'jewellery-price-calc'); ?></p>
            <code>[jpc_metal_rates template="table"]</code>
            <p><?php _e('or', 'jewellery-price-calc'); ?></p>
            <code>[jpc_metal_rates_table]</code>
            
            <hr>
            
            <h3><?php _e('3. Display Metal Rates - Marquee View', 'jewellery-price-calc'); ?></h3>
            <p><?php _e('Display today\'s metal rates in a scrolling marquee format.', 'jewellery-price-calc'); ?></p>
            <code>[jpc_metal_rates template="marquee"]</code>
            <p><?php _e('or', 'jewellery-price-calc'); ?></p>
            <code>[jpc_metal_rates_marquee]</code>
            
            <hr>
            
            <h3><?php _e('4. Display Specific Metals Only', 'jewellery-price-calc'); ?></h3>
            <p><?php _e('Display only selected metals by providing their IDs.', 'jewellery-price-calc'); ?></p>
            <code>[jpc_metal_rates metals="1,2,3"]</code>
            
            <hr>
            
            <h3><?php _e('Metal IDs Reference', 'jewellery-price-calc'); ?></h3>
            <p><?php _e('Use these IDs in the metals parameter:', 'jewellery-price-calc'); ?></p>
            
            <?php
            $metals = JPC_Metals::get_all();
            if (!empty($metals)):
            ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('ID', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Metal Name', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Display Name', 'jewellery-price-calc'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($metals as $metal): ?>
                        <tr>
                            <td><strong><?php echo $metal->id; ?></strong></td>
                            <td><?php echo esc_html($metal->name); ?></td>
                            <td><?php echo esc_html($metal->display_name); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <div class="jpc-card">
            <h2><?php _e('Usage Examples', 'jewellery-price-calc'); ?></h2>
            
            <h3><?php _e('Example 1: Homepage Banner', 'jewellery-price-calc'); ?></h3>
            <p><?php _e('Add a scrolling marquee to your homepage:', 'jewellery-price-calc'); ?></p>
            <code>[jpc_metal_rates_marquee]</code>
            
            <hr>
            
            <h3><?php _e('Example 2: Pricing Page', 'jewellery-price-calc'); ?></h3>
            <p><?php _e('Add a detailed table to your pricing page:', 'jewellery-price-calc'); ?></p>
            <code>[jpc_metal_rates_table]</code>
            
            <hr>
            
            <h3><?php _e('Example 3: Sidebar Widget', 'jewellery-price-calc'); ?></h3>
            <p><?php _e('Add a simple list to your sidebar:', 'jewellery-price-calc'); ?></p>
            <code>[jpc_metal_rates]</code>
            
            <hr>
            
            <h3><?php _e('Example 4: Gold Rates Only', 'jewellery-price-calc'); ?></h3>
            <p><?php _e('Show only gold rates (assuming IDs 1, 2, 3 are gold variants):', 'jewellery-price-calc'); ?></p>
            <code>[jpc_metal_rates metals="1,2,3" template="table"]</code>
        </div>
        
        <div class="jpc-card">
            <h2><?php _e('How to Use Shortcodes', 'jewellery-price-calc'); ?></h2>
            
            <ol>
                <li><?php _e('Copy the shortcode you want to use', 'jewellery-price-calc'); ?></li>
                <li><?php _e('Go to Pages → Edit the page where you want to display metal rates', 'jewellery-price-calc'); ?></li>
                <li><?php _e('Paste the shortcode in the content editor', 'jewellery-price-calc'); ?></li>
                <li><?php _e('Update/Publish the page', 'jewellery-price-calc'); ?></li>
                <li><?php _e('View the page on frontend to see the metal rates', 'jewellery-price-calc'); ?></li>
            </ol>
            
            <p><strong><?php _e('Note:', 'jewellery-price-calc'); ?></strong> <?php _e('You can also use shortcodes in widgets, posts, and custom templates.', 'jewellery-price-calc'); ?></p>
        </div>
    </div>
</div>
