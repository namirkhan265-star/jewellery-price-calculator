/**
 * Product Meta Box JavaScript v2.0.0
 * Handles toggles, live calculations, and AJAX updates
 */

jQuery(document).ready(function($) {
    
    // ========================================
    // MAKING CHARGES TOGGLE
    // ========================================
    
    $('input[name="jpc_making_charges_mode"]').on('change', function() {
        var mode = $(this).val();
        
        if (mode === 'auto') {
            $('#jpc_making_charges_auto').show();
            $('#jpc_making_charges_manual').hide();
            calculateAutoMakingCharges();
        } else {
            $('#jpc_making_charges_auto').hide();
            $('#jpc_making_charges_manual').show();
        }
    });
    
    // Calculate auto making charges when metal or weight changes
    $('#jpc_metal_id, #jpc_metal_weight').on('change keyup', function() {
        if ($('input[name="jpc_making_charges_mode"]:checked').val() === 'auto') {
            calculateAutoMakingCharges();
        }
    });
    
    function calculateAutoMakingCharges() {
        var metalId = $('#jpc_metal_id').val();
        var metalWeight = parseFloat($('#jpc_metal_weight').val()) || 0;
        
        if (!metalId || metalWeight <= 0) {
            $('#jpc_auto_making_charges_display').html(
                '<span style="color: #666;">Select metal and enter weight to see auto-calculated making charges</span>'
            );
            return;
        }
        
        var makingChargesPerGram = parseFloat($('#jpc_metal_id option:selected').data('making-charges')) || 0;
        
        if (makingChargesPerGram <= 0) {
            $('#jpc_auto_making_charges_display').html(
                '<span style="color: #d32f2f;">⚠ This metal has no making charges per gram set. Please update the metal configuration or use manual entry.</span>'
            );
            return;
        }
        
        var totalMakingCharges = metalWeight * makingChargesPerGram;
        
        $('#jpc_auto_making_charges_display').html(
            '<strong>Auto-calculated: ₹' + totalMakingCharges.toFixed(2) + '</strong> ' +
            '(' + metalWeight.toFixed(3) + ' grams × ₹' + makingChargesPerGram.toFixed(2) + ' per gram)'
        );
    }
    
    // ========================================
    // DIAMOND ENTRY MODE TOGGLE
    // ========================================
    
    $('input[name="jpc_diamond_entry_mode"]').on('change', function() {
        var mode = $(this).val();
        
        if (mode === 'dropdown') {
            $('#jpc_diamond_dropdown_mode').show();
            $('#jpc_diamond_manual_mode').hide();
            $('#jpc_manual_diamond_calc_display').hide();
        } else {
            $('#jpc_diamond_dropdown_mode').hide();
            $('#jpc_diamond_manual_mode').show();
            calculateManualDiamondPrice();
        }
    });
    
    // Calculate manual diamond price when any field changes
    $('#jpc_manual_diamond_carat, #jpc_manual_diamond_quantity, #jpc_manual_diamond_price_per_carat, ' +
      '#jpc_manual_diamond_shape_id, #jpc_manual_diamond_colour_id, #jpc_manual_diamond_clarity_id, ' +
      '#jpc_manual_diamond_cut_id, #jpc_manual_diamond_certification_id').on('change keyup', function() {
        if ($('input[name="jpc_diamond_entry_mode"]:checked').val() === 'manual') {
            calculateManualDiamondPrice();
        }
    });
    
    function calculateManualDiamondPrice() {
        var carat = parseFloat($('#jpc_manual_diamond_carat').val()) || 0;
        var quantity = parseFloat($('#jpc_manual_diamond_quantity').val()) || 0;
        var basePrice = parseFloat($('#jpc_manual_diamond_price_per_carat').val()) || 0;
        
        if (carat <= 0 || quantity <= 0 || basePrice <= 0) {
            $('#jpc_manual_diamond_calc_display').hide();
            return;
        }
        
        // Get adjustment values
        var shapeAdj = getAdjustment('#jpc_manual_diamond_shape_id');
        var colourAdj = getAdjustment('#jpc_manual_diamond_colour_id');
        var clarityAdj = getAdjustment('#jpc_manual_diamond_clarity_id');
        var cutAdj = getAdjustment('#jpc_manual_diamond_cut_id');
        var certAdj = getAdjustment('#jpc_manual_diamond_certification_id');
        
        // Calculate adjusted price
        var adjustedPrice = basePrice;
        adjustedPrice = applyAdjustment(adjustedPrice, shapeAdj);
        adjustedPrice = applyAdjustment(adjustedPrice, colourAdj);
        adjustedPrice = applyAdjustment(adjustedPrice, clarityAdj);
        adjustedPrice = applyAdjustment(adjustedPrice, cutAdj);
        adjustedPrice = applyAdjustment(adjustedPrice, certAdj);
        
        var totalCost = adjustedPrice * carat * quantity;
        
        // Build adjustment summary
        var adjustmentSummary = '';
        if (adjustedPrice !== basePrice) {
            var totalAdjustment = ((adjustedPrice - basePrice) / basePrice * 100).toFixed(2);
            adjustmentSummary = ' (Base: ₹' + basePrice.toFixed(2) + '/ct, Adjusted: ' + 
                               (totalAdjustment >= 0 ? '+' : '') + totalAdjustment + '%)';
        }
        
        $('#jpc_manual_diamond_calc_text').html(
            '<strong>Estimated Total: ₹' + totalCost.toFixed(2) + '</strong> ' +
            '(' + carat.toFixed(2) + ' ct × ' + quantity + ' pcs × ₹' + adjustedPrice.toFixed(2) + '/ct)' +
            adjustmentSummary
        );
        $('#jpc_manual_diamond_calc_display').show();
    }
    
    function getAdjustment(selector) {
        var $selected = $(selector + ' option:selected');
        if (!$selected.val()) return null;
        
        return {
            type: $selected.data('adjustment-type'),
            value: parseFloat($selected.data('adjustment-value')) || 0
        };
    }
    
    function applyAdjustment(price, adjustment) {
        if (!adjustment || adjustment.value === 0) return price;
        
        if (adjustment.type === 'percentage') {
            return price * (1 + (adjustment.value / 100));
        } else {
            return price + adjustment.value;
        }
    }
    
    // ========================================
    // INITIALIZATION
    // ========================================
    
    // Trigger initial calculations on page load
    if ($('input[name="jpc_making_charges_mode"]:checked').val() === 'auto') {
        calculateAutoMakingCharges();
    }
    
    if ($('input[name="jpc_diamond_entry_mode"]:checked').val() === 'manual') {
        calculateManualDiamondPrice();
    }
    
    // ========================================
    // HELPER: Format Currency
    // ========================================
    
    function formatCurrency(amount) {
        return '₹' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }
    
    // ========================================
    // VALIDATION
    // ========================================
    
    // Validate before form submission
    $('form#post').on('submit', function(e) {
        var errors = [];
        
        // Check if metal is selected
        if ($('#jpc_metal_id').val() && !$('#jpc_metal_weight').val()) {
            errors.push('Please enter metal weight');
        }
        
        // Check manual diamond entry
        if ($('input[name="jpc_diamond_entry_mode"]:checked').val() === 'manual') {
            if ($('#jpc_manual_diamond_carat').val() && !$('#jpc_manual_diamond_price_per_carat').val()) {
                errors.push('Please enter base price per carat for manual diamond entry');
            }
        }
        
        if (errors.length > 0) {
            alert('Validation Errors:\n\n' + errors.join('\n'));
            e.preventDefault();
            return false;
        }
    });
    
    // ========================================
    // VISUAL FEEDBACK
    // ========================================
    
    // Highlight fields when they change
    $('.jpc-form-field input, .jpc-form-field select').on('change', function() {
        $(this).css('border-color', '#4caf50');
        setTimeout(() => {
            $(this).css('border-color', '');
        }, 1000);
    });
    
    // Show loading state for calculations
    function showCalculating($element) {
        $element.html('<span style="color: #666;">⏳ Calculating...</span>');
    }
    
    // ========================================
    // TOOLTIPS (Optional Enhancement)
    // ========================================
    
    // Add tooltips to help text
    $('.jpc-help-text').each(function() {
        $(this).attr('title', $(this).text());
    });
    
    // ========================================
    // CONSOLE LOGGING (Debug Mode)
    // ========================================
    
    if (window.jpcDebugMode) {
        console.log('JPC Product Meta Box v2.0.0 Initialized');
        
        // Log all changes
        $('.jpc-form-field input, .jpc-form-field select').on('change', function() {
            console.log('Field changed:', $(this).attr('name'), '=', $(this).val());
        });
    }
});
