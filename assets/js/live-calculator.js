jQuery(document).ready(function($) {
    'use strict';
    
    let currentCalculatedPrice = 0;
    let currentDiscountedPrice = 0;
    let currentDiscountAmount = 0;
    let currentPriceBeforeDiscount = 0;
    
    // Debounce function to avoid too many AJAX calls
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Calculate live price
    function calculateLivePrice() {
        const metalId = $('#_jpc_metal_id').val();
        const metalWeight = $('#_jpc_metal_weight').val();
        const diamondId = $('#_jpc_diamond_id').val();
        const diamondQuantity = $('#_jpc_diamond_quantity').val();
        const makingCharge = $('#_jpc_making_charge').val();
        const makingChargeType = $('select[name="_jpc_making_charge_type"]').val();
        const wastageCharge = $('#_jpc_wastage_charge').val();
        const wastageChargeType = $('select[name="_jpc_wastage_charge_type"]').val();
        const pearlCost = $('#_jpc_pearl_cost').val() || 0;
        const stoneCost = $('#_jpc_stone_cost').val() || 0;
        const extraFee = $('#_jpc_extra_fee').val() || 0;
        const discountPercentage = $('#_jpc_discount_percentage').val() || 0;
        
        // Check if required fields are filled
        if (!metalId || !metalWeight) {
            return;
        }
        
        // Show loading state
        $('.jpc-price-breakup-admin').html('<p>Calculating...</p>');
        
        // Make AJAX request
        $.ajax({
            url: jpcLiveCalc.ajax_url,
            type: 'POST',
            data: {
                action: 'jpc_calculate_live_price',
                nonce: jpcLiveCalc.nonce,
                metal_id: metalId,
                metal_weight: metalWeight,
                diamond_id: diamondId,
                diamond_quantity: diamondQuantity,
                making_charge: makingCharge,
                making_charge_type: makingChargeType,
                wastage_charge: wastageCharge,
                wastage_charge_type: wastageChargeType,
                pearl_cost: pearlCost,
                stone_cost: stoneCost,
                extra_fee: extraFee,
                discount_percentage: discountPercentage
            },
            success: function(response) {
                if (response.success) {
                    currentCalculatedPrice = response.data.final_price;
                    if (response.success) {
    currentCalculatedPrice = response.data.final_price;
    currentDiscountAmount = response.data.discount || 0;
    
    // Use price_before_discount from backend if available
    currentDiscountedPrice = currentCalculatedPrice;
    currentPriceBeforeDiscount = response.data.price_before_discount || currentCalculatedPrice;
                    displayPriceBreakup(response.data);
                    
                    // Auto-update WooCommerce price fields
                    autoUpdatePriceFields(currentPriceBeforeDiscount, currentDiscountedPrice, currentDiscountAmount);
                } else {
                    $('.jpc-price-breakup-admin').html('<p style="color: red;">' + response.data.message + '</p>');
                }
            },
            error: function() {
                $('.jpc-price-breakup-admin').html('<p style="color: red;">Error calculating price</p>');
            }
        });
    }
    
    // Auto-update WooCommerce price fields
    function autoUpdatePriceFields(regularPrice, salePrice, discountAmount) {
        const formattedRegularPrice = regularPrice.toFixed(2);
        const formattedSalePrice = salePrice.toFixed(2);
        
        // Update regular price
        $('#_regular_price').val(formattedRegularPrice);
        
        // Update sale price if there's a discount
        if (discountAmount > 0) {
            $('#_sale_price').val(formattedSalePrice);
            
            // Show sale price field if hidden
            $('.sale_price_dates_fields').show();
        } else {
            // Clear sale price if no discount
            $('#_sale_price').val('');
        }
        
        // Trigger WooCommerce price update
        $('#_regular_price, #_sale_price').trigger('change');
    }
    
    // Apply calculated price to product price field (manual button)
    function applyPriceToProduct() {
        if (currentCalculatedPrice > 0) {
            autoUpdatePriceFields(currentPriceBeforeDiscount, currentCalculatedPrice, currentDiscountAmount);
            
            // Show success message
            const $button = $('.jpc-apply-price-btn');
            const originalText = $button.text();
            $button.text('✓ Applied!').prop('disabled', true);
            
            setTimeout(function() {
                $button.text(originalText).prop('disabled', false);
            }, 2000);
        }
    }
    
    // Sync Regular Price to WooCommerce
    function syncRegularPrice() {
        if (currentPriceBeforeDiscount > 0) {
            $('#_regular_price').val(currentPriceBeforeDiscount.toFixed(2));
            $('#_regular_price').trigger('change');
            
            const $button = $('.jpc-sync-regular-btn');
            const originalText = $button.text();
            $button.text('✓ Synced!').prop('disabled', true);
            
            setTimeout(function() {
                $button.text(originalText).prop('disabled', false);
            }, 2000);
        }
    }
    
    // Sync Sale Price to WooCommerce
    function syncSalePrice() {
        if (currentDiscountedPrice > 0 && currentDiscountAmount > 0) {
            $('#_sale_price').val(currentDiscountedPrice.toFixed(2));
            $('#_sale_price').trigger('change');
            $('.sale_price_dates_fields').show();
            
            const $button = $('.jpc-sync-sale-btn');
            const originalText = $button.text();
            $button.text('✓ Synced!').prop('disabled', true);
            
            setTimeout(function() {
                $button.text(originalText).prop('disabled', false);
            }, 2000);
        }
    }
    
    // Display price breakup
    function displayPriceBreakup(data) {
        let html = '<div class="jpc-live-calc-wrapper">';
        html += '<h4>💰 Live Price Calculation</h4>';
        
        // Price Summary Box
        html += '<div class="jpc-price-summary">';
        
        if (data.discount > 0) {
            // Use the actual discount_percentage from data instead of calculating
            const discountPercent = data.discount_percentage ? parseFloat(data.discount_percentage).toFixed(1) : ((data.discount / (data.final_price + data.discount)) * 100).toFixed(1);
            html += '<div class="jpc-price-row jpc-before-discount">';
            html += '<span class="label">Price Before Discount:</span>';
            html += '<span class="value">₹' + formatNumber(data.final_price + data.discount) + '</span>';
            html += '</div>';
            
            html += '<div class="jpc-price-row jpc-discount-row">';
            html += '<span class="label">Discount (' + discountPercent + '%):</span>';
            html += '<span class="value discount">-₹' + formatNumber(data.discount) + '</span>';
            html += '</div>';
            
            html += '<div class="jpc-price-row jpc-after-discount">';
            html += '<span class="label">Price After Discount:</span>';
            html += '<span class="value highlight">₹' + formatNumber(data.final_price) + '</span>';
            html += '</div>';
        } else {
            html += '<div class="jpc-price-row jpc-final-price">';
            html += '<span class="label">Final Price:</span>';
            html += '<span class="value highlight">₹' + formatNumber(data.final_price) + '</span>';
            html += '</div>';
        }
        
        html += '</div>';
        
        // Detailed Breakdown
        html += '<details class="jpc-breakdown-details" open>';
        html += '<summary>View Detailed Breakdown</summary>';
        html += '<table class="jpc-breakdown-table">';
        
        html += '<tr><td>Metal Price:</td><td>₹' + formatNumber(data.metal_price) + '</td></tr>';
        
        if (data.diamond_price > 0) {
            html += '<tr><td>Diamond Price:</td><td>₹' + formatNumber(data.diamond_price) + '</td></tr>';
        }
        
        if (data.making_charge > 0) {
            html += '<tr><td>Making Charge:</td><td>₹' + formatNumber(data.making_charge) + '</td></tr>';
        }
        
        if (data.wastage_charge > 0) {
            html += '<tr><td>Wastage Charge:</td><td>₹' + formatNumber(data.wastage_charge) + '</td></tr>';
        }
        
        if (data.pearl_cost > 0) {
            html += '<tr><td>Pearl Cost:</td><td>₹' + formatNumber(data.pearl_cost) + '</td></tr>';
        }
        
        if (data.stone_cost > 0) {
            html += '<tr><td>Stone Cost:</td><td>₹' + formatNumber(data.stone_cost) + '</td></tr>';
        }
        
        if (data.extra_fee > 0) {
            html += '<tr><td>Extra Fee:</td><td>₹' + formatNumber(data.extra_fee) + '</td></tr>';
        }
        
        if (data.discount > 0) {
            html += '<tr class="discount-row"><td>Discount:</td><td>-₹' + formatNumber(data.discount) + '</td></tr>';
        }
        
        // Always show GST line if GST is enabled
        if (typeof data.gst !== 'undefined') {
            const gstLabel = data.gst_label || 'GST';
            const gstPercentage = data.gst_percentage || '';
            const gstLabelText = gstPercentage ? gstLabel + ' (' + gstPercentage + '%)' : gstLabel;
            html += '<tr class="gst-row"><td>' + gstLabelText + ':</td><td>₹' + formatNumber(data.gst) + '</td></tr>';
        }
        
        html += '<tr class="total-row"><td><strong>Final Price:</strong></td><td><strong>₹' + formatNumber(data.final_price) + '</strong></td></tr>';
        html += '</table>';
        html += '</details>';
        
        // Action Buttons
        html += '<div class="jpc-action-buttons">';
        html += '<button type="button" class="button button-primary jpc-apply-price-btn">✓ Apply All Prices</button>';
        
        if (data.discount > 0) {
            html += '<button type="button" class="button jpc-sync-regular-btn">Sync Regular Price</button>';
            html += '<button type="button" class="button jpc-sync-sale-btn">Sync Sale Price</button>';
        }
        html += '</div>';
        
        // Help Text
        html += '<div class="jpc-help-text">';
        if (data.discount > 0) {
            html += '<p><strong>📌 Price Mapping:</strong></p>';
            html += '<ul>';
            html += '<li><strong>Regular Price:</strong> ₹' + formatNumber(data.final_price + data.discount) + ' (before discount)</li>';
            html += '<li><strong>Sale Price:</strong> ₹' + formatNumber(data.final_price) + ' (after discount)</li>';
            html += '</ul>';
            html += '<p class="note">💡 Prices are auto-updated. Use manual sync buttons if needed.</p>';
        } else {
            html += '<p><strong>📌 Regular Price:</strong> ₹' + formatNumber(data.final_price) + '</p>';
            html += '<p class="note">💡 Price is auto-updated when you change values.</p>';
        }
        html += '</div>';
        
        html += '</div>';
        
        $('.jpc-price-breakup-admin').html(html);
        
        // Bind click events
        $('.jpc-apply-price-btn').off('click').on('click', applyPriceToProduct);
        $('.jpc-sync-regular-btn').off('click').on('click', syncRegularPrice);
        $('.jpc-sync-sale-btn').off('click').on('click', syncSalePrice);
    }
    
    // Format number with 2 decimals
    function formatNumber(num) {
        return parseFloat(num).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
    
    // Debounced calculation function
    const debouncedCalculate = debounce(calculateLivePrice, 500);
    
    // Bind events to all input fields
    $('#_jpc_metal_id, #_jpc_metal_weight, #_jpc_diamond_id, #_jpc_diamond_quantity, #_jpc_making_charge, select[name="_jpc_making_charge_type"], #_jpc_wastage_charge, select[name="_jpc_wastage_charge_type"], #_jpc_pearl_cost, #_jpc_stone_cost, #_jpc_extra_fee, #_jpc_discount_percentage').on('change keyup', function() {
        debouncedCalculate();
    });
    
    // Initial calculation if fields are filled
    if ($('#_jpc_metal_id').val() && $('#_jpc_metal_weight').val()) {
        calculateLivePrice();
    }
});
