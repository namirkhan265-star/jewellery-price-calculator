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
    
    // Safe number conversion
    function toNumber(value, defaultValue = 0) {
        const num = parseFloat(value);
        return isNaN(num) ? defaultValue : num;
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
        
        console.log('JPC: Sending AJAX request with data:', {
            metal_id: metalId,
            metal_weight: metalWeight,
            diamond_id: diamondId,
            diamond_quantity: diamondQuantity
        });
        
        // Make AJAX request
        $.ajax({
            url: jpcProductMeta.ajax_url,
            type: 'POST',
            data: {
                action: 'jpc_calculate_live_price',
                nonce: jpcProductMeta.nonce,
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
                console.log('JPC: AJAX Response:', response);
                
                if (response.success && response.data) {
                    console.log('JPC: Response Data Structure:', {
                        has_final_price: 'final_price' in response.data,
                        has_sale_price: 'sale_price' in response.data,
                        has_price_before_discount: 'price_before_discount' in response.data,
                        has_regular_price: 'regular_price' in response.data,
                        has_breakup: 'breakup' in response.data,
                        has_breakdown: 'breakdown' in response.data
                    });
                    
                    // Extract prices with fallbacks
                    const finalPrice = toNumber(response.data.final_price || response.data.sale_price, 0);
                    const priceBeforeDiscount = toNumber(response.data.price_before_discount || response.data.regular_price || finalPrice, 0);
                    const discountAmount = toNumber(response.data.discount_amount, 0);
                    
                    console.log('JPC: Extracted Prices:', {
                        finalPrice,
                        priceBeforeDiscount,
                        discountAmount
                    });
                    
                    currentCalculatedPrice = finalPrice;
                    currentPriceBeforeDiscount = priceBeforeDiscount;
                    currentDiscountAmount = discountAmount;
                    currentDiscountedPrice = finalPrice;
                    
                    displayPriceBreakup(response.data);
                    
                    // Auto-update WooCommerce price fields
                    autoUpdatePriceFields(currentPriceBeforeDiscount, currentDiscountedPrice, currentDiscountAmount);
                } else {
                    console.error('JPC: Error in response:', response);
                    const errorMsg = (response.data && response.data.message) ? response.data.message : 'Error calculating price';
                    $('.jpc-price-breakup-admin').html('<p style="color: red;">' + errorMsg + '</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('JPC: AJAX Error:', {xhr, status, error, responseText: xhr.responseText});
                $('.jpc-price-breakup-admin').html('<p style="color: red;">Error calculating price. Check console for details.</p>');
            }
        });
    }
    
    // Auto-update WooCommerce price fields
    function autoUpdatePriceFields(regularPrice, salePrice, discountAmount) {
        const formattedRegularPrice = toNumber(regularPrice, 0).toFixed(2);
        const formattedSalePrice = toNumber(salePrice, 0).toFixed(2);
        
        // Update regular price
        $('#_regular_price').val(formattedRegularPrice);
        
        // Update sale price if there's a discount
        if (discountAmount > 0) {
            $('#_sale_price').val(formattedSalePrice);
            $('.sale_price_dates_fields').show();
        } else {
            $('#_sale_price').val('');
        }
        
        // Trigger WooCommerce price update
        $('#_regular_price, #_sale_price').trigger('change');
    }
    
    // Display price breakup
    function displayPriceBreakup(data) {
        console.log('JPC: displayPriceBreakup called with:', data);
        
        // Get breakdown/breakup object
        const breakdown = data.breakup || data.breakdown || {};
        console.log('JPC: Breakdown object:', breakdown);
        
        // Extract prices
        const finalPrice = toNumber(data.final_price || data.sale_price, 0);
        const priceBeforeDiscount = toNumber(data.price_before_discount || data.regular_price || finalPrice, 0);
        const discountAmount = toNumber(data.discount_amount || breakdown.discount_amount, 0);
        const discountPercent = toNumber(data.discount_percentage || breakdown.discount_percentage, 0);
        
        console.log('JPC: Display values:', {
            finalPrice,
            priceBeforeDiscount,
            discountAmount,
            discountPercent
        });
        
        let html = '<div class=\"jpc-live-calc-wrapper\">';
        html += '<h4>💰 Live Price Calculation</h4>';
        
        // Price Summary Box
        html += '<div class=\"jpc-price-summary\">';
        
        if (discountAmount > 0) {
            html += '<div class=\"jpc-price-row jpc-before-discount\">';
            html += '<span class=\"label\">Price Before Discount:</span>';
            html += '<span class=\"value\">₹' + formatNumber(priceBeforeDiscount) + '</span>';
            html += '</div>';
            
            html += '<div class=\"jpc-price-row jpc-discount-row\">';
            html += '<span class=\"label\">Discount (' + discountPercent.toFixed(1) + '%):</span>';
            html += '<span class=\"value discount\">-₹' + formatNumber(discountAmount) + '</span>';
            html += '</div>';
            
            html += '<div class=\"jpc-price-row jpc-after-discount\">';
            html += '<span class=\"label\">Price After Discount:</span>';
            html += '<span class=\"value highlight\">₹' + formatNumber(finalPrice) + '</span>';
            html += '</div>';
        } else {
            html += '<div class=\"jpc-price-row jpc-final-price\">';
            html += '<span class=\"label\">Final Price:</span>';
            html += '<span class=\"value highlight\">₹' + formatNumber(finalPrice) + '</span>';
            html += '</div>';
        }
        
        html += '</div>';
        
        // Detailed Breakdown
        html += '<details class=\"jpc-breakdown-details\" open>';
        html += '<summary>View Detailed Breakdown</summary>';
        html += '<table class=\"jpc-breakdown-table\">';
        
        // Metal Price
        const metalPrice = toNumber(breakdown.metal_price, 0);
        console.log('JPC: Metal price from breakdown:', metalPrice);
        html += '<tr><td>Metal Price:</td><td>₹' + formatNumber(metalPrice) + '</td></tr>';
        
        // Diamond Price
        const diamondPrice = toNumber(breakdown.diamond_price, 0);
        if (diamondPrice > 0) {
            html += '<tr><td>Diamond Price:</td><td>₹' + formatNumber(diamondPrice) + '</td></tr>';
        }
        
        // Making Charge
        const makingCharge = toNumber(breakdown.making_charge, 0);
        if (makingCharge > 0) {
            html += '<tr><td>Making Charge:</td><td>₹' + formatNumber(makingCharge) + '</td></tr>';
        }
        
        // Wastage Charge
        const wastageCharge = toNumber(breakdown.wastage_charge, 0);
        if (wastageCharge > 0) {
            html += '<tr><td>Wastage Charge:</td><td>₹' + formatNumber(wastageCharge) + '</td></tr>';
        }
        
        // Pearl Cost
        const pearlCost = toNumber(breakdown.pearl_cost, 0);
        if (pearlCost > 0) {
            html += '<tr><td>Pearl Cost:</td><td>₹' + formatNumber(pearlCost) + '</td></tr>';
        }
        
        // Stone Cost
        const stoneCost = toNumber(breakdown.stone_cost, 0);
        if (stoneCost > 0) {
            html += '<tr><td>Stone Cost:</td><td>₹' + formatNumber(stoneCost) + '</td></tr>';
        }
        
        // Extra Fee
        const extraFee = toNumber(breakdown.extra_fee, 0);
        if (extraFee > 0) {
            html += '<tr><td>Extra Fee:</td><td>₹' + formatNumber(extraFee) + '</td></tr>';
        }
        
        // Extra Fields #1-5
        if (breakdown.extra_fields && Array.isArray(breakdown.extra_fields)) {
            console.log('JPC: Extra fields:', breakdown.extra_fields);
            breakdown.extra_fields.forEach(function(field) {
                if (field && field.label && toNumber(field.value, 0) > 0) {
                    html += '<tr><td>' + field.label + ':</td><td>₹' + formatNumber(toNumber(field.value, 0)) + '</td></tr>';
                }
            });
        }
        
        // Additional Percentage
        const additionalPercentage = toNumber(breakdown.additional_percentage, 0);
        if (additionalPercentage > 0) {
            const addPercentLabel = breakdown.additional_percentage_label || 'Additional Percentage';
            html += '<tr><td>' + addPercentLabel + ':</td><td>₹' + formatNumber(additionalPercentage) + '</td></tr>';
        }
        
        // GST
        const gst = toNumber(breakdown.gst, 0);
        if (gst > 0) {
            const gstLabel = breakdown.gst_label || 'GST';
            const gstPercent = toNumber(breakdown.gst_percentage, 0);
            html += '<tr><td>' + gstLabel + ' (' + gstPercent + '%):</td><td>₹' + formatNumber(gst) + '</td></tr>';
        }
        
        html += '</table>';
        html += '</details>';
        
        html += '</div>';
        
        $('.jpc-price-breakup-admin').html(html);
    }
    
    // Format number with commas
    function formatNumber(num) {
        const number = toNumber(num, 0);
        return number.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
    
    // Debounced calculate function
    const debouncedCalculate = debounce(calculateLivePrice, 500);
    
    // Bind events
    $('#_jpc_metal_id, #_jpc_metal_weight, #_jpc_diamond_id, #_jpc_diamond_quantity, #_jpc_making_charge, select[name="_jpc_making_charge_type"], #_jpc_wastage_charge, select[name="_jpc_wastage_charge_type"], #_jpc_pearl_cost, #_jpc_stone_cost, #_jpc_extra_fee, #_jpc_discount_percentage, #_jpc_extra_field_1, #_jpc_extra_field_2, #_jpc_extra_field_3, #_jpc_extra_field_4, #_jpc_extra_field_5').on('input change', debouncedCalculate);
    
    // Initial calculation if fields are filled
    if ($('#_jpc_metal_id').val() && $('#_jpc_metal_weight').val()) {
        calculateLivePrice();
    }
    
    console.log('JPC: Live calculator initialized');
});
