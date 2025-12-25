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
                    alert(response.data.message || 'An error occurred');
                }
            }).fail(function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Failed to add metal. Please check console for details.');
            });
        });
        
        // Add Diamond Form
        $('#jpc-add-diamond-form').on('submit', function(e) {
            e.preventDefault();
            
            var formData = {
                action: 'jpc_add_diamond',
                nonce: jpcAdmin.nonce,
                type: $('#diamond_type').val(),
                carat: $('#carat').val(),
                certification: $('#certification').val(),
                display_name: $('#display_name').val(),
                price_per_carat: $('#price_per_carat').val()
            };
            
            console.log('Sending diamond data:', formData);
            
            $.post(jpcAdmin.ajaxurl, formData, function(response) {
                console.log('Diamond response:', response);
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message || 'Failed to add diamond');
                }
            }).fail(function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                alert('Failed to add diamond. Please check console for details.');
            });
        });
        
        // Edit Diamond - Open Modal
        $(document).on('click', '.jpc-edit-diamond', function() {
            var diamondId = $(this).data('id');
            var row = $(this).closest('tr');
            
            // Extract data from row
            var type = row.data('type');
            var carat = row.find('td').eq(2).text().replace(' ct', '').trim();
            var certBadge = row.find('.cert-badge').text().toLowerCase().trim();
            var displayName = row.find('td').eq(4).text().trim();
            var priceText = row.find('td').eq(5).text().replace('₹', '').replace(',', '').trim();
            
            console.log('Edit diamond:', {diamondId, type, carat, certBadge, displayName, priceText});
            
            // Populate edit form
            $('#edit_diamond_id').val(diamondId);
            $('#edit_diamond_type').val(type);
            $('#edit_carat').val(carat);
            $('#edit_certification').val(certBadge);
            $('#edit_display_name').val(displayName);
            $('#edit_price_per_carat').val(priceText);
            
            // Show modal
            $('#jpc-edit-diamond-modal').show();
        });
        
        // Update Diamond Form
        $('#jpc-edit-diamond-form').on('submit', function(e) {
            e.preventDefault();
            
            var formData = {
                action: 'jpc_update_diamond',
                nonce: jpcAdmin.nonce,
                id: $('#edit_diamond_id').val(),
                type: $('#edit_diamond_type').val(),
                carat: $('#edit_carat').val(),
                certification: $('#edit_certification').val(),
                display_name: $('#edit_display_name').val(),
                price_per_carat: $('#edit_price_per_carat').val()
            };
            
            console.log('Updating diamond:', formData);
            
            $.post(jpcAdmin.ajaxurl, formData, function(response) {
                console.log('Update response:', response);
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message || 'Failed to update diamond');
                }
            }).fail(function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                alert('Failed to update diamond. Please check console for details.');
            });
        });
        
        // Delete Diamond
        $(document).on('click', '.jpc-delete-diamond', function() {
            if (!confirm(jpcAdmin.confirmDelete)) {
                return;
            }
            
            var diamondId = $(this).data('id');
            
            $.post(jpcAdmin.ajaxurl, {
                action: 'jpc_delete_diamond',
                nonce: jpcAdmin.nonce,
                id: diamondId
            }, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message || 'An error occurred');
                }
            }).fail(function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Failed to delete diamond. Please check console for details.');
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
                    alert(response.data.message || 'An error occurred');
                }
            }).fail(function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Failed to update metal. Please check console for details.');
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
                    alert(response.data.message || 'An error occurred');
                }
            }).fail(function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Failed to delete metal. Please check console for details.');
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
                    alert(response.data.message || 'An error occurred');
                }
            }).fail(function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Failed to update prices. Please check console for details.');
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
                enable_wastage_charge: $('input[name="enable_wastage_charge"]').is(':checked') ? 1 : 0,
                making_charge_type: 'percentage',
                wastage_charge_type: 'percentage'
            };
            
            console.log('Sending data:', formData);
            
            $.post(jpcAdmin.ajaxurl, formData, function(response) {
                console.log('Response:', response);
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert(response.data.message || 'Failed to add metal group');
                }
            }).fail(function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                console.error('Status:', status);
                console.error('Error:', error);
                alert('Failed to add metal group. Please check console for details.');
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
                    alert(response.data.message || 'An error occurred');
                }
            }).fail(function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Failed to delete metal group. Please check console for details.');
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
