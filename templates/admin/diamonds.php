<?php
/**
 * Diamonds Management Page Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$diamonds = JPC_Diamonds::get_all();
$types = JPC_Diamonds::get_types();
$certifications = JPC_Diamonds::get_certifications();
$carat_sizes = JPC_Diamonds::get_carat_sizes();
?>

<div class="wrap jpc-admin-wrap">
    <h1><?php _e('Manage Diamonds', 'jewellery-price-calc'); ?></h1>
    
    <div class="jpc-admin-content">
        <!-- Add New Diamond Form -->
        <div class="jpc-card">
            <h2><?php _e('Add New Diamond', 'jewellery-price-calc'); ?></h2>
            
            <form id="jpc-add-diamond-form" method="post">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="diamond_type"><?php _e('Diamond Type', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <select id="diamond_type" name="type" class="regular-text" required>
                                <option value=""><?php _e('Select Type', 'jewellery-price-calc'); ?></option>
                                <?php foreach ($types as $key => $label): ?>
                                    <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php _e('Natural, Lab Grown, or Moissanite', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="carat"><?php _e('Carat Weight', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <select id="carat" name="carat" class="regular-text" required>
                                <option value=""><?php _e('Select Carat', 'jewellery-price-calc'); ?></option>
                                <?php foreach ($carat_sizes as $size): ?>
                                    <option value="<?php echo esc_attr($size); ?>"><?php echo esc_html($size); ?> ct</option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php _e('Diamond carat weight', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="certification"><?php _e('Certification', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <select id="certification" name="certification" class="regular-text" required>
                                <option value=""><?php _e('Select Certification', 'jewellery-price-calc'); ?></option>
                                <?php foreach ($certifications as $key => $label): ?>
                                    <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description"><?php _e('GIA, IGI, HRD, or None', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="display_name"><?php _e('Display Name', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="display_name" name="display_name" class="regular-text" required>
                            <p class="description"><?php _e('E.g., "1.00ct Natural Diamond (GIA)"', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="price_per_carat"><?php _e('Price per Carat (₹)', 'jewellery-price-calc'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="price_per_carat" name="price_per_carat" class="regular-text" step="0.01" min="0" required>
                            <p class="description"><?php _e('Price per carat in rupees', 'jewellery-price-calc'); ?></p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <button type="submit" class="button button-primary"><?php _e('Add Diamond', 'jewellery-price-calc'); ?></button>
                </p>
            </form>
        </div>
        
        <!-- Existing Diamonds -->
        <div class="jpc-card">
            <h2><?php _e('Existing Diamonds', 'jewellery-price-calc'); ?></h2>
            
            <?php if (empty($diamonds)): ?>
                <p><?php _e('No diamonds found. Add your first diamond above.', 'jewellery-price-calc'); ?></p>
            <?php else: ?>
                <!-- Filter by Type -->
                <div style="margin-bottom: 20px;">
                    <label for="filter-type"><?php _e('Filter by Type:', 'jewellery-price-calc'); ?></label>
                    <select id="filter-type" style="margin-left: 10px;">
                        <option value=""><?php _e('All Types', 'jewellery-price-calc'); ?></option>
                        <?php foreach ($types as $key => $label): ?>
                            <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <table class="wp-list-table widefat fixed striped" id="diamonds-table">
                    <thead>
                        <tr>
                            <th><?php _e('ID', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Type', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Carat', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Certification', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Display Name', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Price/Carat', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Total Price', 'jewellery-price-calc'); ?></th>
                            <th><?php _e('Actions', 'jewellery-price-calc'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($diamonds as $diamond): ?>
                        <tr data-type="<?php echo esc_attr($diamond->type); ?>">
                            <td><?php echo esc_html($diamond->id); ?></td>
                            <td>
                                <span class="diamond-type-badge diamond-type-<?php echo esc_attr($diamond->type); ?>">
                                    <?php echo esc_html($types[$diamond->type]); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html($diamond->carat); ?> ct</td>
                            <td>
                                <span class="cert-badge cert-<?php echo esc_attr($diamond->certification); ?>">
                                    <?php echo esc_html(strtoupper($diamond->certification)); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html($diamond->display_name); ?></td>
                            <td>₹<?php echo number_format($diamond->price_per_carat, 2); ?></td>
                            <td>
                                <strong>₹<?php echo number_format($diamond->price_per_carat * $diamond->carat, 2); ?></strong>
                            </td>
                            <td>
                                <button class="button button-small jpc-edit-diamond" data-id="<?php echo esc_attr($diamond->id); ?>">
                                    <?php _e('Edit', 'jewellery-price-calc'); ?>
                                </button>
                                <button class="button button-small jpc-delete-diamond" data-id="<?php echo esc_attr($diamond->id); ?>">
                                    <?php _e('Delete', 'jewellery-price-calc'); ?>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <!-- Quick Add Presets -->
        <div class="jpc-card">
            <h2><?php _e('Quick Add Common Diamonds', 'jewellery-price-calc'); ?></h2>
            <p><?php _e('Click to quickly add common diamond configurations:', 'jewellery-price-calc'); ?></p>
            
            <div class="quick-add-buttons">
                <button class="button" onclick="quickAddDiamond('natural', '0.50', 'gia', '50000')">0.50ct Natural (GIA)</button>
                <button class="button" onclick="quickAddDiamond('natural', '1.00', 'gia', '95000')">1.00ct Natural (GIA)</button>
                <button class="button" onclick="quickAddDiamond('lab_grown', '0.50', 'igi', '25000')">0.50ct Lab Grown (IGI)</button>
                <button class="button" onclick="quickAddDiamond('lab_grown', '1.00', 'igi', '45000')">1.00ct Lab Grown (IGI)</button>
                <button class="button" onclick="quickAddDiamond('moissanite', '1.00', 'none', '15000')">1.00ct Moissanite</button>
            </div>
        </div>
    </div>
</div>

<style>
.diamond-type-badge {
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: bold;
}
.diamond-type-natural { background: #e3f2fd; color: #1976d2; }
.diamond-type-lab_grown { background: #f3e5f5; color: #7b1fa2; }
.diamond-type-moissanite { background: #fff3e0; color: #f57c00; }

.cert-badge {
    padding: 3px 6px;
    border-radius: 2px;
    font-size: 11px;
    font-weight: bold;
}
.cert-gia { background: #4caf50; color: white; }
.cert-igi { background: #2196f3; color: white; }
.cert-hrd { background: #ff9800; color: white; }
.cert-none { background: #9e9e9e; color: white; }

.quick-add-buttons { display: flex; gap: 10px; flex-wrap: wrap; }
.quick-add-buttons .button { margin: 0; }
</style>

<script>
function quickAddDiamond(type, carat, cert, price) {
    const types = <?php echo json_encode($types); ?>;
    const certs = <?php echo json_encode($certifications); ?>;
    
    document.getElementById('diamond_type').value = type;
    document.getElementById('carat').value = carat;
    document.getElementById('certification').value = cert;
    document.getElementById('price_per_carat').value = price;
    document.getElementById('display_name').value = carat + 'ct ' + types[type] + ' (' + certs[cert] + ')';
    
    // Scroll to form
    document.getElementById('jpc-add-diamond-form').scrollIntoView({ behavior: 'smooth' });
}

// Filter diamonds by type
document.getElementById('filter-type').addEventListener('change', function() {
    const filterValue = this.value;
    const rows = document.querySelectorAll('#diamonds-table tbody tr');
    
    rows.forEach(row => {
        if (!filterValue || row.dataset.type === filterValue) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
