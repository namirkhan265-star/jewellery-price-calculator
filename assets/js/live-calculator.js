jQuery(document).ready(function($) {
    'use strict';
    
    let currentCalculatedPrice = 0;
    
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
                    displayPriceBreakup(response.data);
                } else {
                    $('.jpc-price-breakup-admin').html('<p style="color: red;">' + response.data.message + '</p>');
                }
            },
            error: function() {
                $('.jpc-price-breakup-admin').html('<p style="color: red;">Error calculating price</p>');
            }
        });
    }
    
    // Apply calculated price to product price field
    function applyPriceToProduct() {
        if (currentCalculatedPrice > 0) {
            const formattedPrice = currentCalculatedPrice.toFixed(2);
            $('#_regular_price').val(formattedPrice);
            
            // Show success message
            const $button = $('.jpc-apply-price-btn');
            const originalText = $button.text();
            $button.text('✓ Applied!').prop('disabled', true);
            
            setTimeout(function() {
                $button.text(originalText).prop('disabled', false);
            }, 2000);
        }
    }
    
    // Display price breakup
    function displayPriceBreakup(data) {
        let html = '<h4>Live Price Calculation</h4>';
        html += '<p class="description" style="color: #2271b1; margin-bottom: 10px;">';
        html += '<strong>Note:</strong> This is a live calculation. Click the button below to apply this price to the product.';
        html += '</p>';
        html += '<table>';
        
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
            html += '<tr><td>Discount:</td><td>-₹' + formatNumber(data.discount) + '</td></tr>';
        }
        
        if (data.gst > 0) {
            html += '<tr><td>GST:</td><td>₹' + formatNumber(data.gst) + '</td></tr>';
        }
        
        html += '<tr class="total-row"><td><strong>Final Price:</strong></td><td><strong>₹' + formatNumber(data.final_price) + '</strong></td></tr>';
        html += '</table>';
        
        html += '<p style="margin-top: 15px;">';
        html += '<button type="button" class="button button-primary jpc-apply-price-btn">Apply to Product Price</button>';
        html += '</p>';
        
        $('.jpc-price-breakup-admin').html(html);
        
        // Bind click event to the button
        $('.jpc-apply-price-btn').off('click').on('click', applyPriceToProduct);
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
