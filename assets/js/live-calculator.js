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
        
        // Get extra fields #1-5
        const extraField1 = $('#_jpc_extra_field_1').val() || 0;
        const extraField2 = $('#_jpc_extra_field_2').val() || 0;
        const extraField3 = $('#_jpc_extra_field_3').val() || 0;
        const extraField4 = $('#_jpc_extra_field_4').val() || 0;
        const extraField5 = $('#_jpc_extra_field_5').val() || 0;
        
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
                discount_percentage: discountPercentage,
                extra_field_1: extraField1,
                extra_field_2: extraField2,
                extra_field_3: extraField3,
                extra_field_4: extraField4,
                extra_field_5: extraField5
            },
            success: function(response) {
                if (response.success) {
                    // BACKWARD COMPATIBLE: Support both old and new response structures
                    // New structure: final_price, price_before_discount
                    // Old structure: sale_price, regular_price
                    const finalPrice = response.data.final_price || response.data.sale_price || 0;
                    const priceBeforeDiscount = response.data.price_before_discount || response.data.regular_price || finalPrice;
                    
                    currentCalculatedPrice = finalPrice;
                    currentPriceBeforeDiscount = priceBeforeDiscount;
                    
                    // Get discount from top level or breakdown
                    currentDiscountAmount = response.data.discount_amount || (response.data.breakdown && response.data.breakdown.discount_amount) || 0;
                    currentDiscountedPrice = currentCalculatedPrice;
                    
                    displayPriceBreakup(response.data);
                    
                    // Auto-update WooCommerce price fields
                    autoUpdatePriceFields(currentPriceBeforeDiscount, currentDiscountedPrice, currentDiscountAmount);
                } else {
                    $('.jpc-price-breakup-admin').html('<p style="color: red;">' + (response.data.message || 'Error calculating price') + '</p>');
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
        // BACKWARD COMPATIBLE: Support both old and new response structures
        const breakdown = data.breakup || data.breakdown || {};
        const finalPrice = data.final_price || data.sale_price || 0;
        const priceBeforeDiscount = data.price_before_discount || data.regular_price || finalPrice;
        const discountAmount = data.discount_amount || (breakdown.discount_amount) || 0;
        const discountPercent = data.discount_percentage || (breakdown.discount_percentage) || 0;
        
        let html = '<div class="jpc-live-calc-wrapper">';
        html += '<h4>💰 Live Price Calculation</h4>';
        
        // Price Summary Box
        html += '<div class="jpc-price-summary">';
        
        if (discountAmount > 0) {
            html += '<div class="jpc-price-row jpc-before-discount">';
            html += '<span class="label">Price Before Discount:</span>';
            html += '<span class="value">₹' + formatNumber(priceBeforeDiscount) + '</span>';
            html += '</div>';
            
            html += '<div class="jpc-price-row jpc-discount-row">';
            html += '<span class="label">Discount (' + parseFloat(discountPercent).toFixed(1) + '%):</span>';
            html += '<span class="value discount">-₹' + formatNumber(discountAmount) + '</span>';
            html += '</div>';
            
            html += '<div class="jpc-price-row jpc-after-discount">';
            html += '<span class="label">Price After Discount:</span>';
            html += '<span class="value highlight">₹' + formatNumber(finalPrice) + '</span>';
            html += '</div>';
        } else {
            html += '<div class="jpc-price-row jpc-final-price">';
            html += '<span class="label">Final Price:</span>';
            html += '<span class="value highlight">₹' + formatNumber(finalPrice) + '</span>';
            html += '</div>';
        }
        
        html += '</div>';
        
        // Detailed Breakdown
        html += '<details class="jpc-breakdown-details" open>';
        html += '<summary>View Detailed Breakdown</summary>';
        html += '<table class="jpc-breakdown-table">';
        
        html += '<tr><td>Metal Price:</td><td>₹' + formatNumber(breakdown.metal_price || 0) + '</td></tr>';
        
        if (breakdown.diamond_price > 0) {
            html += '<tr><td>Diamond Price:</td><td>₹' + formatNumber(breakdown.diamond_price) + '</td></tr>';
        }
        
        if (breakdown.making_charge > 0) {
            html += '<tr><td>Making Charge:</td><td>₹' + formatNumber(breakdown.making_charge) + '</td></tr>';
        }
        
        if (breakdown.wastage_charge > 0) {
            html += '<tr><td>Wastage Charge:</td><td>₹' + formatNumber(breakdown.wastage_charge) + '</td></tr>';
        }
        
        if (breakdown.pearl_cost > 0) {
            html += '<tr><td>Pearl Cost:</td><td>₹' + formatNumber(breakdown.pearl_cost) + '</td></tr>';
        }
        
        if (breakdown.stone_cost > 0) {
            html += '<tr><td>Stone Cost:</td><td>₹' + formatNumber(breakdown.stone_cost) + '</td></tr>';
        }
        
        if (breakdown.extra_fee > 0) {
            html += '<tr><td>Extra Fee:</td><td>₹' + formatNumber(breakdown.extra_fee) + '</td></tr>';
        }
        
        // Display extra fields with labels - SHOW ALL ENABLED FIELDS
        if (breakdown.extra_fields && breakdown.extra_fields.length > 0) {
            for (let i = 0; i < breakdown.extra_fields.length; i++) {
                const field = breakdown.extra_fields[i];
                // Show ALL enabled fields, even if value is 0
                html += '<tr><td>' + field.label + ':</td><td>₹' + formatNumber(field.value) + '</td></tr>';
            }
        }
        
        // Display additional percentage
        if (breakdown.additional_percentage > 0) {
            const addPercentLabel = breakdown.additional_percentage_label || 'Additional Percentage';
            html += '<tr><td>' + addPercentLabel + ':</td><td>₹' + formatNumber(breakdown.additional_percentage) + '</td></tr>';
        }
        
        // Display GST
        if (breakdown.gst > 0) {
            const gstLabel = breakdown.gst_label || 'GST';
            const gstPercent = breakdown.gst_percentage || 0;
            html += '<tr><td>' + gstLabel + ' (' + gstPercent + '%):</td><td>₹' + formatNumber(breakdown.gst) + '</td></tr>';
        }
        
        html += '</table>';
        html += '</details>';
        
        html += '</div>';
        
        $('.jpc-price-breakup-admin').html(html);
    }
    
    // Format number with commas
    function formatNumber(num) {
        return parseFloat(num).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
    
    // Debounced calculate function
    const debouncedCalculate = debounce(calculateLivePrice, 500);
    
    // Bind events
    $('#_jpc_metal_id, #_jpc_metal_weight, #_jpc_diamond_id, #_jpc_diamond_quantity, #_jpc_making_charge, select[name="_jpc_making_charge_type"], #_jpc_wastage_charge, select[name="_jpc_wastage_charge_type"], #_jpc_pearl_cost, #_jpc_stone_cost, #_jpc_extra_fee, #_jpc_discount_percentage, #_jpc_extra_field_1, #_jpc_extra_field_2, #_jpc_extra_field_3, #_jpc_extra_field_4, #_jpc_extra_field_5').on('input change', debouncedCalculate);
    
    // Apply price button
    $(document).on('click', '.jpc-apply-price-btn', applyPriceToProduct);
    
    // Sync buttons
    $(document).on('click', '.jpc-sync-regular-btn', syncRegularPrice);
    $(document).on('click', '.jpc-sync-sale-btn', syncSalePrice);
    
    // Initial calculation if fields are filled
    if ($('#_jpc_metal_id').val() && $('#_jpc_metal_weight').val()) {
        calculateLivePrice();
    }
});
