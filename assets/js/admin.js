/**
 * Jewellery Price Calculator - Admin JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Add Metal Form
        $('#jpc-add-metal-form').on('submit', function(e) {
            e.preventDefault();
            
            var formData = {
                action: 'jpc_add_metal',
                nonce: jpcAdmin.nonce,
                name: $('#metal_name').val(),
                display_name: $('#metal_display_name').val(),
                metal_group_id: $('#metal_group').val(),
                price_per_unit: $('#metal_price').val()
            };
            
            $.post(jpcAdmin.ajaxurl, formData, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message);
                }
            });
        });
        
        // Edit Metal
        $('.jpc-edit-metal').on('click', function() {
            var metalId = $(this).data('id');
            var row = $(this).closest('tr');
            
            $('#edit_metal_id').val(metalId);
            $('#edit_metal_name').val(row.find('td:eq(1)').text());
            $('#edit_metal_display_name').val(row.find('td:eq(2)').text());
            
            $('#jpc-edit-metal-modal').show();
        });
        
        // Update Metal Form
        $('#jpc-edit-metal-form').on('submit', function(e) {
            e.preventDefault();
            
            var formData = {
                action: 'jpc_update_metal',
                nonce: jpcAdmin.nonce,
                id: $('#edit_metal_id').val(),
                name: $('#edit_metal_name').val(),
                display_name: $('#edit_metal_display_name').val(),
                metal_group_id: $('#edit_metal_group').val(),
                price_per_unit: $('#edit_metal_price').val()
            };
            
            $.post(jpcAdmin.ajaxurl, formData, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message);
                }
            });
        });
        
        // Delete Metal
        $('.jpc-delete-metal').on('click', function() {
            if (!confirm(jpcAdmin.confirmDelete)) {
                return;
            }
            
            var metalId = $(this).data('id');
            
            $.post(jpcAdmin.ajaxurl, {
                action: 'jpc_delete_metal',
                nonce: jpcAdmin.nonce,
                id: metalId
            }, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message);
                }
            });
        });
        
        // Bulk Update Prices
        $('#jpc-bulk-update-btn').on('click', function() {
            var updates = [];
            
            $('.jpc-price-edit').each(function() {
                var row = $(this).closest('tr');
                var metalId = row.data('metal-id');
                var price = $(this).val();
                
                updates.push({
                    id: metalId,
                    price: price
                });
            });
            
            if (updates.length === 0) {
                alert('No prices to update');
                return;
            }
            
            if (!confirm('This will update ' + updates.length + ' metal prices and recalculate all product prices. Continue?')) {
                return;
            }
            
            $.post(jpcAdmin.ajaxurl, {
                action: 'jpc_bulk_update_prices',
                nonce: jpcAdmin.nonce,
                updates: updates
            }, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message);
                }
            });
        });
        
        // Add Metal Group Form
        $('#jpc-add-metal-group-form').on('submit', function(e) {
            e.preventDefault();
            
            var formData = {
                action: 'jpc_add_metal_group',
                nonce: jpcAdmin.nonce,
                name: $('#group_name').val(),
                unit: $('#unit').val(),
                enable_making_charge: $('input[name="enable_making_charge"]').is(':checked') ? 1 : 0,
                enable_wastage_charge: $('input[name="enable_wastage_charge"]').is(':checked') ? 1 : 0
            };
            
            $.post(jpcAdmin.ajaxurl, formData, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message);
                }
            });
        });
        
        // Delete Metal Group
        $('.jpc-delete-group').on('click', function() {
            if (!confirm(jpcAdmin.confirmDelete)) {
                return;
            }
            
            var groupId = $(this).data('id');
            
            $.post(jpcAdmin.ajaxurl, {
                action: 'jpc_delete_metal_group',
                nonce: jpcAdmin.nonce,
                id: groupId
            }, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message);
                }
            });
        });
        
        // Modal Close
        $('.jpc-modal-close').on('click', function() {
            $(this).closest('.jpc-modal').hide();
        });
        
        // Close modal on outside click
        $(window).on('click', function(e) {
            if ($(e.target).hasClass('jpc-modal')) {
                $('.jpc-modal').hide();
            }
        });
        
        // Toggle price edit mode
        $('.jpc-edit-metal').on('click', function() {
            var row = $(this).closest('tr');
            row.find('.jpc-price-display').hide();
            row.find('.jpc-price-edit').show().focus();
        });
        
        // Price edit blur
        $('.jpc-price-edit').on('blur', function() {
            var newPrice = $(this).val();
            var row = $(this).closest('tr');
            row.find('.jpc-price-display').text('₹ ' + parseFloat(newPrice).toFixed(2)).show();
            $(this).hide();
        });
        
        // Calculate price preview in product meta box
        $('#jpc-calculate-preview').on('click', function() {
            var metalId = $('#_jpc_metal_id').val();
            var weight = $('#_jpc_metal_weight').val();
            var makingCharge = $('#_jpc_making_charge').val();
            var wastageCharge = $('#_jpc_wastage_charge').val();
            
            if (!metalId || !weight) {
                alert('Please select metal and enter weight');
                return;
            }
            
            // This would call an AJAX function to calculate preview
            // Implementation depends on your needs
        });
    });
    
})(jQuery);
