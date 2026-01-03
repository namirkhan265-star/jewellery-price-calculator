// PASTE THIS IN BROWSER CONSOLE (F12) ON PRODUCT EDIT PAGE

console.log('=== LIVE CALCULATOR DIAGNOSTIC ===');

// Test 1: Check jQuery
console.log('1. jQuery:', typeof jQuery !== 'undefined' ? '✅ YES' : '❌ NO');

// Test 2: Check jpcProductMeta
console.log('2. jpcProductMeta:', typeof jpcProductMeta !== 'undefined' ? '✅ YES' : '❌ NO');
if (typeof jpcProductMeta !== 'undefined') {
    console.log('   ajax_url:', jpcProductMeta.ajax_url);
    console.log('   nonce:', jpcProductMeta.nonce);
}

// Test 3: Check HTML elements
console.log('3. HTML Elements:');
console.log('   #_jpc_metal_id:', jQuery('#_jpc_metal_id').length, '- Value:', jQuery('#_jpc_metal_id').val());
console.log('   #_jpc_metal_weight:', jQuery('#_jpc_metal_weight').length, '- Value:', jQuery('#_jpc_metal_weight').val());
console.log('   .jpc-price-breakup-admin:', jQuery('.jpc-price-breakup-admin').length);

// Test 4: Check loaded scripts
console.log('4. Loaded Scripts:');
const scripts = Array.from(document.querySelectorAll('script[src]'));
const productMeta = scripts.find(s => s.src.includes('product-meta.js'));
const liveCalc = scripts.find(s => s.src.includes('live-calculator.js'));
console.log('   product-meta.js:', productMeta ? '✅ LOADED' : '❌ NOT LOADED');
if (productMeta) console.log('      URL:', productMeta.src);
console.log('   live-calculator.js:', liveCalc ? '⚠️ LOADED (OLD)' : '✅ NOT LOADED');
if (liveCalc) console.log('      URL:', liveCalc.src);

// Test 5: Manual AJAX call
console.log('\n5. Testing AJAX Call...');
if (typeof jpcProductMeta !== 'undefined') {
    jQuery.ajax({
        url: jpcProductMeta.ajax_url,
        type: 'POST',
        data: {
            action: 'jpc_calculate_live_price',
            nonce: jpcProductMeta.nonce,
            metal_id: jQuery('#_jpc_metal_id').val(),
            metal_weight: jQuery('#_jpc_metal_weight').val(),
            diamond_id: jQuery('#_jpc_diamond_id').val() || 0,
            diamond_quantity: jQuery('#_jpc_diamond_quantity').val() || 0,
            making_charge: jQuery('#_jpc_making_charge').val() || 0,
            making_charge_type: jQuery('select[name="_jpc_making_charge_type"]').val(),
            wastage_charge: jQuery('#_jpc_wastage_charge').val() || 0,
            wastage_charge_type: jQuery('select[name="_jpc_wastage_charge_type"]').val(),
            pearl_cost: jQuery('#_jpc_pearl_cost').val() || 0,
            stone_cost: jQuery('#_jpc_stone_cost').val() || 0,
            extra_fee: jQuery('#_jpc_extra_fee').val() || 0,
            discount_percentage: jQuery('#_jpc_discount_percentage').val() || 0,
            extra_field_1: jQuery('#_jpc_extra_field_1').val() || 0,
            extra_field_2: jQuery('#_jpc_extra_field_2').val() || 0,
            extra_field_3: jQuery('#_jpc_extra_field_3').val() || 0,
            extra_field_4: jQuery('#_jpc_extra_field_4').val() || 0,
            extra_field_5: jQuery('#_jpc_extra_field_5').val() || 0
        },
        success: function(response) {
            console.log('   ✅ AJAX SUCCESS');
            console.log('   Response:', response);
            
            if (response.success && response.data) {
                console.log('   ✅ Has data');
                console.log('   Structure Check:');
                console.log('      - price_before_discount:', response.data.price_before_discount);
                console.log('      - final_price:', response.data.final_price);
                console.log('      - regular_price:', response.data.regular_price);
                console.log('      - sale_price:', response.data.sale_price);
                console.log('      - breakup:', response.data.breakup);
                console.log('      - breakdown:', response.data.breakdown);
                
                if (response.data.breakup) {
                    console.log('   Breakup Details:');
                    console.log('      - metal_price:', response.data.breakup.metal_price);
                    console.log('      - diamond_price:', response.data.breakup.diamond_price);
                    console.log('      - making_charge:', response.data.breakup.making_charge);
                    console.log('      - extra_fields:', response.data.breakup.extra_fields);
                }
            } else {
                console.log('   ❌ No data or not successful');
                console.log('   Error:', response.data ? response.data.message : 'Unknown error');
            }
        },
        error: function(xhr, status, error) {
            console.log('   ❌ AJAX ERROR');
            console.log('   Status:', status);
            console.log('   Error:', error);
            console.log('   Response Text:', xhr.responseText);
        }
    });
} else {
    console.log('   ❌ Cannot test - jpcProductMeta not defined');
}

console.log('\n=== END DIAGNOSTIC ===');
