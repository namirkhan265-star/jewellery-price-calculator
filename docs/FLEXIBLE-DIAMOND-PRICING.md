# Flexible Diamond Pricing System - v1.3.0

## 🎯 Problem Solved

**Before v1.3.0:**
- ❌ Had to pre-create diamond entries in admin
- ❌ Confusing diamond IDs in CSV
- ❌ Manual price entry required
- ❌ Difficult bulk imports

**After v1.3.0:**
- ✅ Direct entry of diamond specs in CSV
- ✅ Automatic price calculation
- ✅ Auto-creation of diamond entries
- ✅ Simple 3-field system

---

## 📊 New CSV Format

### **Old Format (v1.2.x):**
```csv
SKU,Name,JPC Diamond ID,JPC Diamond Quantity
RING001,Gold Ring,5,10
```
❌ What is Diamond ID 5? You had to check admin panel!

### **New Format (v1.3.0):**
```csv
SKU,Name,JPC Diamond Type,JPC Diamond Carat,JPC Diamond Certification,JPC Diamond Quantity
RING001,Gold Ring,natural,0.50,gia,10
RING002,Silver Ring,lab_grown,0.30,igi,8
RING003,Pendant,moissanite,0.25,none,5
```
✅ Clear! You can see exactly what diamond is used!

---

## 🔧 How It Works

### **3 Simple Fields:**

1. **JPC Diamond Type** - Type of diamond
   - `natural` - Natural Diamond
   - `lab_grown` - Lab Grown Diamond
   - `moissanite` - Moissanite

2. **JPC Diamond Carat** - Weight per diamond
   - Examples: `0.25`, `0.50`, `1.00`, `2.50`
   - Can be any decimal value

3. **JPC Diamond Certification** - Certificate type
   - `gia` - GIA Certified (+20% price)
   - `igi` - IGI Certified (+15% price)
   - `hrd` - HRD Certified (+18% price)
   - `none` - No Certification (base price)

4. **JPC Diamond Quantity** - Number of diamonds
   - Examples: `1`, `10`, `25`, `100`

---

## 💰 Automatic Price Calculation

### **Formula:**
```
Final Price = Base Price × Carat Multiplier × Certification Multiplier
```

### **Base Prices (per carat):**
- Natural Diamond: ₹25,000
- Lab Grown Diamond: ₹15,000
- Moissanite: ₹5,000

### **Carat Multipliers:**
| Carat Range | Multiplier | Why? |
|------------|-----------|------|
| 0.00 - 0.50ct | 1.00× | Base price |
| 0.50 - 1.00ct | 1.30× | +30% (larger stones) |
| 1.00 - 2.00ct | 1.80× | +80% (premium size) |
| 2.00 - 3.00ct | 2.50× | +150% (rare size) |
| 3.00 - 5.00ct | 3.50× | +250% (very rare) |
| 5.00ct+ | 5.00× | +400% (extremely rare) |

### **Certification Multipliers:**
| Certification | Multiplier | Price Impact |
|--------------|-----------|--------------|
| GIA | 1.20× | +20% |
| IGI | 1.15× | +15% |
| HRD | 1.18× | +18% |
| None | 1.00× | Base price |

---

## 📝 Example Calculations

### **Example 1: Small Natural Diamond**
```
Type: natural
Carat: 0.30
Certification: none
Quantity: 10

Calculation:
Base Price = ₹25,000 (natural)
Carat Multiplier = 1.00× (0.30ct is < 0.50ct)
Cert Multiplier = 1.00× (no certification)

Price per Carat = ₹25,000 × 1.00 × 1.00 = ₹25,000
Price per Diamond = ₹25,000 × 0.30ct = ₹7,500
Total Price = ₹7,500 × 10 = ₹75,000
```

### **Example 2: Large GIA Certified Diamond**
```
Type: natural
Carat: 1.50
Certification: gia
Quantity: 1

Calculation:
Base Price = ₹25,000 (natural)
Carat Multiplier = 1.80× (1.50ct is in 1-2ct range)
Cert Multiplier = 1.20× (GIA certified)

Price per Carat = ₹25,000 × 1.80 × 1.20 = ₹54,000
Price per Diamond = ₹54,000 × 1.50ct = ₹81,000
Total Price = ₹81,000 × 1 = ₹81,000
```

### **Example 3: Lab Grown with IGI**
```
Type: lab_grown
Carat: 0.75
Certification: igi
Quantity: 5

Calculation:
Base Price = ₹15,000 (lab grown)
Carat Multiplier = 1.30× (0.75ct is in 0.5-1ct range)
Cert Multiplier = 1.15× (IGI certified)

Price per Carat = ₹15,000 × 1.30 × 1.15 = ₹22,425
Price per Diamond = ₹22,425 × 0.75ct = ₹16,819
Total Price = ₹16,819 × 5 = ₹84,095
```

### **Example 4: Moissanite (Budget Option)**
```
Type: moissanite
Carat: 0.50
Certification: none
Quantity: 20

Calculation:
Base Price = ₹5,000 (moissanite)
Carat Multiplier = 1.00× (0.50ct is at boundary)
Cert Multiplier = 1.00× (no certification)

Price per Carat = ₹5,000 × 1.00 × 1.00 = ₹5,000
Price per Diamond = ₹5,000 × 0.50ct = ₹2,500
Total Price = ₹2,500 × 20 = ₹50,000
```

---

## 📥 Bulk Import Examples

### **Example CSV:**
```csv
SKU,Name,Regular price,JPC Metal ID,JPC Metal Weight (grams),JPC Diamond Type,JPC Diamond Carat,JPC Diamond Certification,JPC Diamond Quantity,JPC Making Charge,JPC Making Charge Type
RING001,Gold Ring,0,1,4.32,natural,0.50,gia,10,20,percentage
RING002,Silver Ring,0,2,3.50,lab_grown,0.30,igi,8,15,percentage
RING003,Pendant,0,1,2.80,moissanite,0.25,none,5,500,fixed
RING004,Earrings,0,1,3.20,natural,0.40,hrd,12,18,percentage
RING005,Bracelet,0,2,5.50,lab_grown,0.60,gia,15,25,percentage
```

### **What Happens on Import:**

1. **Row 1 (RING001):**
   - Checks if "natural, 0.50ct, GIA" exists in database
   - If not, creates new diamond entry
   - Calculates price: ₹25,000 × 1.00 × 1.20 = ₹30,000/carat
   - Saves diamond ID for future use
   - Calculates total product price

2. **Row 2 (RING002):**
   - Checks if "lab_grown, 0.30ct, IGI" exists
   - Creates if needed
   - Calculates price: ₹15,000 × 1.00 × 1.15 = ₹17,250/carat
   - Saves and calculates

3. **Automatic Reuse:**
   - If Row 10 also has "natural, 0.50ct, GIA"
   - System finds existing entry
   - Reuses same diamond ID
   - No duplicates created!

---

## ✅ Benefits

### **1. No Pre-Configuration Needed**
```
Before: Create 50 diamond entries manually
After: Just enter specs in CSV, auto-created!
```

### **2. Clear CSV Data**
```
Before: Diamond ID = 23 (what is this?)
After: natural, 0.50ct, GIA (crystal clear!)
```

### **3. Automatic Price Updates**
```
If you change base prices:
- All diamonds recalculate automatically
- No manual updates needed
```

### **4. Smart Deduplication**
```
Same specs = Same diamond entry
Different specs = New entry
No duplicates!
```

---

## 🎛️ Customizing Prices

### **Method 1: Update Base Prices**
Go to **Jewellery Calculator → Diamond Pricing**

Change base prices:
- Natural Diamond: ₹25,000 → ₹30,000
- Lab Grown: ₹15,000 → ₹18,000
- Moissanite: ₹5,000 → ₹6,000

All future diamonds use new prices!

### **Method 2: Adjust Multipliers**
Change carat multipliers:
```php
0.50 - 1.00ct: 1.30× → 1.40× (increase premium)
1.00 - 2.00ct: 1.80× → 2.00× (increase more)
```

### **Method 3: Certification Premiums**
Adjust certification multipliers:
```php
GIA: 1.20× → 1.25× (increase GIA premium)
IGI: 1.15× → 1.18× (increase IGI premium)
```

---

## 📋 CSV Template

### **Complete Product Import Template:**
```csv
Type,SKU,Name,Published,JPC Metal ID,JPC Metal Weight (grams),JPC Diamond Type,JPC Diamond Carat,JPC Diamond Certification,JPC Diamond Quantity,JPC Making Charge,JPC Making Charge Type,JPC Wastage Charge,JPC Wastage Charge Type,JPC Discount Percentage
simple,RING001,Gold Diamond Ring,1,1,4.32,natural,0.50,gia,10,20,percentage,5,percentage,10
simple,RING002,Silver Lab Diamond Ring,1,2,3.50,lab_grown,0.30,igi,8,15,percentage,3,percentage,5
simple,PEND001,Gold Moissanite Pendant,1,1,2.80,moissanite,0.25,none,5,500,fixed,200,fixed,0
simple,EARR001,Platinum Diamond Earrings,1,3,3.20,natural,0.40,hrd,12,18,percentage,4,percentage,8
```

---

## 🔍 Finding Diamond Types

### **Valid Diamond Types:**
```
natural      → Natural Diamond
lab_grown    → Lab Grown Diamond
moissanite   → Moissanite
```

### **Valid Certifications:**
```
gia   → GIA Certified
igi   → IGI Certified
hrd   → HRD Certified
none  → No Certification
```

### **Case Insensitive:**
```
✅ natural, Natural, NATURAL (all work)
✅ gia, GIA, Gia (all work)
```

---

## ⚠️ Important Notes

### **1. Backward Compatibility**
- Old "Diamond ID" method still works
- Can mix old and new methods
- Gradual migration supported

### **2. Automatic Entry Creation**
- Diamonds auto-created on import
- Check **Jewellery Calculator → Diamonds** to see entries
- Can edit prices manually if needed

### **3. Price Recalculation**
- Prices calculate on import
- Recalculate anytime by re-saving product
- Bulk recalculation available

### **4. Validation**
- Invalid diamond type → Error message
- Invalid certification → Defaults to 'none'
- Invalid carat → Error message

---

## 🚀 Migration Guide

### **From Old System (v1.2.x) to New System (v1.3.0):**

**Step 1: Export Current Products**
```
WooCommerce → Products → Export
```

**Step 2: Add New Columns**
Add these columns to your CSV:
- JPC Diamond Type
- JPC Diamond Carat
- JPC Diamond Certification

**Step 3: Fill Diamond Data**
For each product with diamonds:
```
Old: JPC Diamond ID = 5
New: JPC Diamond Type = natural
     JPC Diamond Carat = 0.50
     JPC Diamond Certification = gia
```

**Step 4: Import**
```
WooCommerce → Products → Import
Map columns → Run import
```

**Step 5: Verify**
- Check product prices
- Verify diamond details
- Confirm calculations

---

## 💡 Pro Tips

### **Tip 1: Use Consistent Specs**
```
✅ Always use: natural, 0.50, gia
❌ Don't mix: natural, 0.5, GIA (different formatting)
```

### **Tip 2: Round Carat Weights**
```
✅ Use: 0.25, 0.50, 0.75, 1.00
❌ Avoid: 0.2347, 0.5123 (too precise)
```

### **Tip 3: Batch Similar Products**
```
Group products with same diamond specs
Import together for efficiency
```

### **Tip 4: Test First**
```
Import 5-10 products first
Verify prices are correct
Then import rest
```

---

## 📞 Support

If you have questions:
1. Check this documentation
2. Review example calculations
3. Test with small batch first
4. Contact support if needed

---

**Version:** 1.3.0  
**Last Updated:** December 2024  
**Author:** Brandwitty
