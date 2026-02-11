/**
 * v2.5.29: Dynamic show/hide for making charges and wastage based on metal group settings
 * 
 * ADD THIS CODE to assets/js/product-meta-box-v2.js
 * Place it after the existing metal selection change handler
 */

jQuery(document).ready(function($) {
    
    /**
     * v2.5.29: Handle metal selection change - show/hide making charges and wastage
     */
    function jpcUpdateFieldVisibility() {
        var selectedOption = $('#jpc_metal_id').find('option:selected');
        var enableMaking = selectedOption.data('enable-making');
        var enableWastage = selectedOption.data('enable-wastage');
        
        // Show/hide making charges section
        if (enableMaking == 1 || enableMaking === true) {
            $('.jpc-making-charges-section').slideDown(300);
        } else {
            $('.jpc-making-charges-section').slideUp(300);
        }
        
        // Show/hide wastage field
        if (enableWastage == 1 || enableWastage === true) {
            $('.jpc-wastage-field').slideDown(300);
        } else {
            $('.jpc-wastage-field').slideUp(300);
        }
    }
    
    // Trigger on metal selection change
    $('#jpc_metal_id').on('change', function() {
        jpcUpdateFieldVisibility();
    });
    
    // Trigger on page load
    jpcUpdateFieldVisibility();
    
});
