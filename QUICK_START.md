# 🚀 QUICK START - Live Calculator Fix

## ⚡ 3-Step Fix

### 1️⃣ Download & Install
```
1. Download plugin from GitHub main branch
2. Deactivate old plugin
3. Delete old plugin
4. Upload new plugin
5. Activate
```

### 2️⃣ Clear Caches
```
1. WordPress cache (if using caching plugin)
2. Browser cache (Ctrl+Shift+R or Cmd+Shift+R)
3. CDN cache (if applicable)
```

### 3️⃣ Test
```
1. Edit any product
2. Fill Metal + Weight
3. See live price calculation appear ✅
```

---

## 🧪 Quick Test (30 seconds)

Open browser console (F12) and paste:

```javascript
// Test 1: Check if everything is loaded
console.log('=== QUICK TEST ===');
console.log('jQuery:', typeof jQuery !== 'undefined' ? '✅' : '❌');
console.log('jpcProductMeta:', typeof jpcProductMeta !== 'undefined' ? '✅' : '❌');
console.log('Metal field:', jQuery('#_jpc_metal_id').length > 0 ? '✅' : '❌');
console.log('Price div:', jQuery('.jpc-price-breakup-admin').length > 0 ? '✅' : '❌');

// Test 2: Manual AJAX test
if (typeof jpcProductMeta !== 'undefined') {
    jQuery.ajax({
        url: jpcProductMeta.ajax_url,
        type: 'POST',
        data: {
            action: 'jpc_calculate_live_price',
            nonce: jpcProductMeta.nonce,
            metal_id: jQuery('#_jpc_metal_id').val() || 1,
            metal_weight: jQuery('#_jpc_metal_weight').val() || 10,
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
        success: function(r) {
            console.log('AJAX Test:', r.success ? '✅ SUCCESS' : '❌ FAILED');
            if (r.success && r.data) {
                console.log('Price:', r.data.final_price || r.data.sale_price);
            }
        },
        error: function() {
            console.log('AJAX Test: ❌ ERROR');
        }
    });
}
```

**Expected Output:**
```
=== QUICK TEST ===
jQuery: ✅
jpcProductMeta: ✅
Metal field: ✅
Price div: ✅
AJAX Test: ✅ SUCCESS
Price: 50000
```

---

## ❌ Common Issues

| Issue | Fix |
|-------|-----|
| jpcProductMeta undefined | Clear cache, hard refresh |
| Calculating... never changes | Check Network tab for AJAX errors |
| ₹NaN displayed | Update to latest version |
| No price breakdown | Check if meta box div exists |

---

## 📁 Key Files

- `assets/js/product-meta.js` - Main live calculator script
- `includes/class-jpc-product-meta.php` - AJAX handler
- `assets/js/diagnostic.js` - Diagnostic tools
- `LIVE_CALCULATOR_FIX_SUMMARY.md` - Full documentation

---

## 🆘 Still Not Working?

1. **Check Files Exist:**
   ```
   /assets/js/product-meta.js ✅
   /includes/class-jpc-product-meta.php ✅
   ```

2. **Check Console for Errors:**
   - Open DevTools (F12)
   - Go to Console tab
   - Look for red errors

3. **Check Network Tab:**
   - Open DevTools (F12)
   - Go to Network tab
   - Fill Metal + Weight
   - Look for `admin-ajax.php` call
   - Check if it returns success

4. **Run Full Diagnostic:**
   - Copy content from `assets/js/diagnostic.js`
   - Paste in console
   - Run `testLiveCalculator()`

---

## ✅ Success Checklist

- [ ] Downloaded latest plugin from main branch
- [ ] Deactivated and deleted old plugin
- [ ] Uploaded and activated new plugin
- [ ] Cleared WordPress cache
- [ ] Cleared browser cache (Ctrl+Shift+R)
- [ ] Opened product editor
- [ ] Filled Metal and Weight fields
- [ ] Saw "Calculating..." message
- [ ] Saw price breakdown appear
- [ ] Ran quick test in console (all ✅)

---

**If all checkboxes are ✅, live calculator is working!** 🎉

For detailed documentation, see `LIVE_CALCULATOR_FIX_SUMMARY.md`
