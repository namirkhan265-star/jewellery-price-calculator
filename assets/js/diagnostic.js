/**
 * DIAGNOSTIC SCRIPT FOR LIVE CALCULATOR
 * 
 * This file helps diagnose why the live calculator isn't working.
 * Add this to your browser console to test:
 * 
 * 1. Check if jQuery is loaded
 * 2. Check if jpcProductMeta object exists
 * 3. Check if the meta box HTML elements exist
 * 4. Test a manual AJAX call
 */

// Test 1: Check jQuery
console.log('=== LIVE CALCULATOR DIAGNOSTIC ===');
console.log('1. jQuery loaded:', typeof jQuery !== 'undefined' ? 'YES ✓' : 'NO ✗');

// Test 2: Check jpcProductMeta object
console.log('2. jpcProductMeta exists:', typeof jpcProductMeta !== 'undefined' ? 'YES ✓' : 'NO ✗');
if (typeof jpcProductMeta !== 'undefined') {
    console.log('   - ajax_url:', jpcProductMeta.ajax_url);
    console.log('   - nonce:', jpcProductMeta.nonce);
}

// Test 3: Check if meta box elements exist
console.log('3. Meta box elements:');
console.log('   - #_jpc_metal_id:', jQuery('#_jpc_metal_id').length > 0 ? 'EXISTS ✓' : 'MISSING ✗');
console.log('   - #_jpc_metal_weight:', jQuery('#_jpc_metal_weight').length > 0 ? 'EXISTS ✓' : 'MISSING ✗');
console.log('   - .jpc-price-breakup-admin:', jQuery('.jpc-price-breakup-admin').length > 0 ? 'EXISTS ✓' : 'MISSING ✗');

// Test 4: Check if product-meta.js is loaded
console.log('4. Checking loaded scripts...');
const scripts = Array.from(document.querySelectorAll('script[src]'));
const productMetaScript = scripts.find(s => s.src.includes('product-meta.js'));
console.log('   - product-meta.js loaded:', productMetaScript ? 'YES ✓' : 'NO ✗');
if (productMetaScript) {
    console.log('   - Script URL:', productMetaScript.src);
}

// Test 5: Manual AJAX test function
window.testLiveCalculator = function() {
    if (typeof jpcProductMeta === 'undefined') {
        console.error('ERROR: jpcProductMeta is not defined. Script not loaded properly.');
        return;
    }
    
    const metalId = jQuery('#_jpc_metal_id').val();
    const metalWeight = jQuery('#_jpc_metal_weight').val();
    
    if (!metalId || !metalWeight) {
        console.error('ERROR: Please fill in Metal and Weight fields first');
        return;
    }
    
    console.log('=== TESTING AJAX CALL ===');
    console.log('Metal ID:', metalId);
    console.log('Metal Weight:', metalWeight);
    
    jQuery.ajax({
        url: jpcProductMeta.ajax_url,
        type: 'POST',
        data: {
            action: 'jpc_calculate_live_price',
            nonce: jpcProductMeta.nonce,
            metal_id: metalId,
            metal_weight: metalWeight,
            diamond_id: 0,
            diamond_quantity: 0,
            making_charge: 0,
            making_charge_type: 'percentage',
            wastage_charge: 0,
            wastage_charge_type: 'percentage',
            pearl_cost: 0,
            stone_cost: 0,
            extra_fee: 0,
            discount_percentage: 0,
            extra_field_1: 0,
            extra_field_2: 0,
            extra_field_3: 0,
            extra_field_4: 0,
            extra_field_5: 0
        },
        success: function(response) {
            console.log('✓ AJAX SUCCESS');
            console.log('Response:', response);
            
            if (response.success && response.data) {
                console.log('✓ Response has data');
                console.log('  - regular_price:', response.data.regular_price);
                console.log('  - sale_price:', response.data.sale_price);
                console.log('  - breakup:', response.data.breakup);
            } else {
                console.error('✗ Response missing data or not successful');
            }
        },
        error: function(xhr, status, error) {
            console.error('✗ AJAX ERROR');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('Response:', xhr.responseText);
        }
    });
};

console.log('\n=== HOW TO USE ===');
console.log('1. Fill in Metal and Weight fields in the product editor');
console.log('2. Run: testLiveCalculator()');
console.log('3. Check the console output for results');
console.log('==================\n');
