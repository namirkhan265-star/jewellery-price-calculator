# Diamond Legacy Tab - Auto-Pricing Guide

## 🎯 What is the Legacy Tab?

The **Diamonds (Legacy)** tab shows individual diamond entries for backward compatibility with older products. It now **automatically integrates** with the new 3-tab system!

---

## ✨ New Features

### **1. Auto-Price Calculation**
When you add a diamond, the price is **automatically calculated** from:
- Diamond Groups (Tab 1)
- Diamond Types / Carat Ranges (Tab 2)
- Certifications (Tab 3)

### **2. Manual Price Override**
You can **manually edit** the calculated price if needed for special cases.

### **3. Sync from New System**
Click **"Sync from New System"** to auto-generate legacy diamonds from your 3-tab setup.

---

## 📊 How It Works

### **Step 1: Select Diamond Group**
```
Choose: Natural Diamond
→ System looks up in Diamond Groups table
```

### **Step 2: Select Carat Weight**
```
Choose: 0.75ct
→ System finds matching carat range in Diamond Types
→ Found: 0.50-1.00ct range with base price ₹32,500/carat
```

### **Step 3: Select Certification**
```
Choose: GIA
→ System applies certification adjustment
→ GIA = +20%
→ Final Price: ₹32,500 × 1.20 = ₹39,000/carat
```

### **Step 4: Auto-Calculation Display**
```
┌─────────────────────────────────────────┐
│ Calculated Price                        │
├─────────────────────────────────────────┤
│ Base Price: ₹32,500/carat              │
│ Certification Adjustment: +20%          │
│ Final Price/Carat: ₹39,000             │
│ Total Price: ₹29,250                   │
│ Carat Range: 0.50-1.00ct               │
└─────────────────────────────────────────┘
```

### **Step 5: Price Auto-Filled**
```
Price per Carat field: ₹39,000.00 (auto-filled)
Display Name: 0.75ct Natural Diamond (GIA) (auto-generated)

You can edit these if needed!
```

---

## 🔄 Sync from New System

### **What It Does:**
Automatically creates legacy diamond entries from your 3-tab system.

### **How It Works:**
```
1. Gets all Diamond Groups (Natural, Lab Grown, Moissanite)
2. Gets all Diamond Types (carat ranges)
3. Gets all Certifications (GIA, IGI, HRD, None)
4. Creates combinations:
   - Natural Diamond + 0.25ct + GIA
   - Natural Diamond + 0.25ct + IGI
   - Natural Diamond + 0.25ct + HRD
   - Natural Diamond + 0.25ct + None
   - Natural Diamond + 0.75ct + GIA
   - ... and so on
```

### **Example Result:**
```
Before Sync: 0 diamonds
After Sync: 132 diamonds

Breakdown:
- 3 Groups × 11 Carat Ranges × 4 Certifications = 132 combinations
```

### **When to Use:**
- ✅ First time setup
- ✅ After adding new groups/types/certifications
- ✅ To populate empty legacy table
- ❌ Don't use if you have custom manual entries (will create duplicates)

---

## 💡 Use Cases

### **Use Case 1: Standard Diamond**
```
Scenario: Adding 1.00ct Natural Diamond with GIA

Steps:
1. Select: Natural Diamond
2. Select: 1.00ct
3. Select: GIA
4. See calculation:
   - Base: ₹32,500/carat (from 0.50-1.00ct range)
   - GIA: +20%
   - Final: ₹39,000/carat
5. Auto-filled display name: "1.00ct Natural Diamond (GIA)"
6. Click "Add Diamond"

Result: Diamond added with ₹39,000/carat price
```

### **Use Case 2: Custom Price Override**
```
Scenario: Special discount for bulk order

Steps:
1. Select: Lab Grown Diamond
2. Select: 1.50ct
3. Select: IGI
4. See calculation: ₹22,425/carat
5. Manually change to: ₹20,000/carat (10% discount)
6. Click "Add Diamond"

Result: Diamond added with custom ₹20,000/carat price
```

### **Use Case 3: Quick Populate**
```
Scenario: Need all combinations quickly

Steps:
1. Go to Diamond Groups → Add 3 groups
2. Go to Diamond Types → Add 11 carat ranges
3. Go to Certifications → Add 4 certifications
4. Go to Diamonds (Legacy) → Click "Sync from New System"
5. Wait for confirmation

Result: 132 diamonds auto-created with correct prices
```

---

## 📋 Price Calculation Formula

### **Formula:**
```
Final Price = Base Price × (1 + Certification Adjustment%)

Example:
Base Price: ₹32,500/carat
Certification: GIA (+20%)
Final Price: ₹32,500 × 1.20 = ₹39,000/carat
```

### **For Fixed Adjustments:**
```
Final Price = Base Price + Fixed Amount

Example:
Base Price: ₹32,500/carat
Certification: Premium (+₹5,000)
Final Price: ₹32,500 + ₹5,000 = ₹37,500/carat
```

---

## 🎨 UI Features

### **1. Info Notice**
```
Blue box at top explaining:
- What this page is
- Links to 3-tab system
- How auto-pricing works
```

### **2. Calculated Price Box**
```
Shows when you select group + carat + certification:
- Base price from carat range
- Certification adjustment
- Final price per carat
- Total price for selected carat
- Which carat range was used
```

### **3. Auto-Generated Display Name**
```
Format: [Carat]ct [Group Name] ([Certification])

Examples:
- 0.75ct Natural Diamond (GIA)
- 1.50ct Lab Grown Diamond (IGI)
- 2.00ct Moissanite (None)
```

### **4. Sync Button**
```
Big blue button: "Sync from New System"
- Creates all combinations
- Shows count of synced diamonds
- Prevents duplicates
```

---

## ⚠️ Important Notes

### **Price Editability:**
```
✅ You CAN manually edit prices
✅ Useful for special cases
✅ Overrides auto-calculation
✅ Saved to database
```

### **When Prices Update:**
```
❌ Existing legacy diamonds DON'T auto-update
✅ Only NEW diamonds get latest prices
✅ To update existing: Delete and re-add
✅ Or manually edit each one
```

### **Sync Behavior:**
```
✅ Only creates if doesn't exist
✅ Checks: group + carat + certification
❌ Won't overwrite existing entries
✅ Safe to run multiple times
```

---

## 🔧 Troubleshooting

### **Problem: "No price range found for this carat weight"**
```
Cause: No matching carat range in Diamond Types

Solution:
1. Go to Diamond Types tab
2. Add carat range that includes your carat
3. Example: For 0.75ct, need range like 0.50-1.00ct
```

### **Problem: "Diamond group not found"**
```
Cause: Group doesn't exist in Diamond Groups table

Solution:
1. Go to Diamond Groups tab
2. Add the missing group
3. Or select existing group from dropdown
```

### **Problem: "Certification not found"**
```
Cause: Certification doesn't exist in Certifications table

Solution:
1. Go to Certifications tab
2. Add the missing certification
3. Or select existing certification from dropdown
```

### **Problem: Price shows ₹0.00**
```
Cause: Calculation failed

Solution:
1. Check all 3 tabs have data
2. Verify carat range exists for your carat
3. Check browser console for errors
4. Try different carat weight
```

---

## 📊 Example Workflow

### **Complete Setup:**

#### **Step 1: Populate 3-Tab System**
```
1. Go to Debug page
2. Click "Populate Diamond Data"
3. Verify:
   - Diamond Groups: 3 records
   - Diamond Types: 11 records
   - Certifications: 4 records
```

#### **Step 2: Sync Legacy Diamonds**
```
1. Go to Diamonds (Legacy)
2. Click "Sync from New System"
3. Wait for "Synced 132 diamonds" message
4. Refresh page
```

#### **Step 3: Verify**
```
Check table shows:
- Natural Diamond entries
- Lab Grown Diamond entries
- Moissanite entries
- All with correct prices
```

#### **Step 4: Add Custom Diamond**
```
1. Select: Natural Diamond
2. Select: 0.85ct (custom size)
3. Select: GIA
4. See auto-calculated price
5. Override if needed
6. Add diamond
```

---

## 🎯 Best Practices

### **1. Use 3-Tab System for Management**
```
✅ Add groups in Diamond Groups
✅ Set prices in Diamond Types
✅ Manage certifications in Certifications
❌ Don't manually add hundreds of legacy diamonds
```

### **2. Sync After Changes**
```
When you:
- Add new diamond group
- Add new carat range
- Add new certification

Then:
- Go to Legacy tab
- Click "Sync from New System"
- Get new combinations automatically
```

### **3. Manual Override for Special Cases**
```
Use manual price override for:
- Bulk order discounts
- VIP customer pricing
- Promotional offers
- Damaged/clearance diamonds
```

### **4. Keep Legacy Tab for Products**
```
Legacy tab is used by:
- Product meta boxes
- Price calculations
- CSV imports
- Backward compatibility

Don't delete it!
```

---

## 🚀 Quick Reference

### **Add Diamond:**
```
1. Select group, carat, certification
2. See auto-calculated price
3. Edit if needed
4. Click "Add Diamond"
```

### **Sync All:**
```
1. Click "Sync from New System"
2. Confirm
3. Wait for completion
4. Refresh page
```

### **Edit Price:**
```
1. Click "Edit" on diamond
2. Change price_per_carat
3. Click "Update Diamond"
```

### **Delete Diamond:**
```
1. Click "Delete" on diamond
2. Confirm
3. Diamond removed
```

---

## 💎 Summary

The Legacy tab now:
- ✅ **Auto-calculates** prices from 3-tab system
- ✅ **Allows manual override** for special cases
- ✅ **Syncs automatically** from new system
- ✅ **Shows calculation breakdown** for transparency
- ✅ **Maintains backward compatibility** with products

**Best of both worlds: Automation + Flexibility!** 🎉
