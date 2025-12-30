# How Diamond Groups, Types & Certifications Work Together

## 🎯 Overview

The new diamond system has **3 interconnected tabs** that work together to create flexible, professional diamond pricing:

```
┌─────────────────────────────────────────────────────────┐
│                    DIAMOND GROUPS                       │
│  (Categories: Natural, Lab Grown, Moissanite)          │
│                         ↓                               │
│                  Provides: Group ID                     │
└─────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────┐
│                    DIAMOND TYPES                        │
│  (Carat Ranges with Base Prices)                       │
│  Group 1: 0-0.5ct = ₹25,000/ct                        │
│  Group 1: 0.5-1ct = ₹32,500/ct                        │
│                         ↓                               │
│              Provides: Base Price/Carat                 │
└─────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────┐
│               DIAMOND CERTIFICATIONS                    │
│  (Price Adjustments)                                    │
│  GIA: +20%, IGI: +15%, HRD: +18%                       │
│                         ↓                               │
│            Provides: Final Adjusted Price               │
└─────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────┐
│                  FINAL DIAMOND PRICE                    │
│  (Used in Products & Legacy Tab)                       │
└─────────────────────────────────────────────────────────┘
```

---

## 📊 Tab 1: Diamond Groups

**Purpose:** Define diamond categories

### **What It Contains:**
```
ID | Name              | Slug              | Description
1  | Natural Diamond   | natural-diamond   | Naturally mined...
2  | Lab Grown Diamond | lab-grown-diamond | Laboratory created...
3  | Moissanite        | moissanite        | Silicon carbide...
```

### **How It's Used:**
- Each group represents a **type of diamond**
- Groups are referenced by **Diamond Types** (Tab 2)
- You can add unlimited custom groups (Cubic Zirconia, etc.)

### **Example:**
```
Add New Group:
Name: Cubic Zirconia
Description: Budget-friendly diamond alternative
→ Creates Group ID: 4
```

---

## 💎 Tab 2: Diamond Types (Carat Pricing)

**Purpose:** Set prices for different carat ranges within each group

### **What It Contains:**
```
ID | Group ID | Carat Range    | Price/Carat | Display Name
1  | 1        | 0.00 - 0.50ct  | ₹25,000     | Natural (0-0.5ct)
2  | 1        | 0.50 - 1.00ct  | ₹32,500     | Natural (0.5-1ct)
3  | 1        | 1.00 - 2.00ct  | ₹45,000     | Natural (1-2ct)
4  | 2        | 0.00 - 0.50ct  | ₹15,000     | Lab Grown (0-0.5ct)
5  | 2        | 0.50 - 1.00ct  | ₹19,500     | Lab Grown (0.5-1ct)
```

### **How It Works:**
1. **Select Diamond Group** (from Tab 1)
2. **Define Carat Range** (e.g., 0.50 - 1.00ct)
3. **Set Base Price** per carat
4. System automatically finds correct price based on carat weight

### **Example:**
```
Product has 0.75ct diamond
Group: Natural Diamond (ID: 1)

System searches Diamond Types:
- 0.00-0.50ct? No (0.75 > 0.50)
- 0.50-1.00ct? YES! (0.50 ≤ 0.75 ≤ 1.00)
→ Base Price: ₹32,500/carat
```

---

## 🏆 Tab 3: Diamond Certifications

**Purpose:** Apply price adjustments based on certification

### **What It Contains:**
```
ID | Name | Type       | Adjustment | Description
1  | GIA  | Percentage | +20%       | Premium certification
2  | IGI  | Percentage | +15%       | Standard certification
3  | HRD  | Percentage | +18%       | High quality cert
4  | None | Percentage | 0%         | No certification
```

### **How It Works:**
1. Takes **base price** from Diamond Types (Tab 2)
2. Applies **adjustment** (percentage or fixed)
3. Returns **final price** per carat

### **Adjustment Types:**

#### **Percentage:**
```
Formula: Base Price × (1 + Adjustment/100)

Example:
Base: ₹32,500/carat
GIA (+20%): ₹32,500 × 1.20 = ₹39,000/carat
```

#### **Fixed:**
```
Formula: Base Price + Adjustment

Example:
Base: ₹32,500/carat
Premium (+₹5,000): ₹32,500 + ₹5,000 = ₹37,500/carat
```

---

## 🔄 Complete Flow Example

### **Scenario:**
Creating a product with **0.75ct Natural Diamond with GIA certification**

### **Step-by-Step:**

#### **Step 1: Diamond Group (Tab 1)**
```
User selects: Natural Diamond
System gets: Group ID = 1
```

#### **Step 2: Diamond Type (Tab 2)**
```
System searches for Group ID 1 with carat 0.75:
- 0.00-0.50ct? No
- 0.50-1.00ct? YES! ✓
→ Base Price: ₹32,500/carat
```

#### **Step 3: Certification (Tab 3)**
```
User selects: GIA
System gets: +20% adjustment
Calculation: ₹32,500 × 1.20 = ₹39,000/carat
→ Final Price: ₹39,000/carat
```

#### **Step 4: Total Price**
```
Price per carat: ₹39,000
Carat weight: 0.75ct
Total: ₹39,000 × 0.75 = ₹29,250 per diamond
```

---

## 📋 Real-World Examples

### **Example 1: Budget Ring**
```
Diamond: 0.30ct Lab Grown, No Certification

Tab 1 (Group): Lab Grown Diamond (ID: 2)
Tab 2 (Type): 0.00-0.50ct range → ₹15,000/carat
Tab 3 (Cert): None (0%) → ₹15,000/carat
Final: ₹15,000 × 0.30 = ₹4,500
```

### **Example 2: Premium Engagement Ring**
```
Diamond: 1.50ct Natural, GIA Certified

Tab 1 (Group): Natural Diamond (ID: 1)
Tab 2 (Type): 1.00-2.00ct range → ₹45,000/carat
Tab 3 (Cert): GIA (+20%) → ₹54,000/carat
Final: ₹54,000 × 1.50 = ₹81,000
```

### **Example 3: Affordable Alternative**
```
Diamond: 1.00ct Moissanite, No Certification

Tab 1 (Group): Moissanite (ID: 3)
Tab 2 (Type): 0.00-1.00ct range → ₹5,000/carat
Tab 3 (Cert): None (0%) → ₹5,000/carat
Final: ₹5,000 × 1.00 = ₹5,000
```

---

## 🎨 How It Appears in Legacy Tab

The **Diamonds (Legacy)** tab shows the **final calculated diamonds** that combine all three tabs:

```
Legacy Tab Display:
┌────────────────────────────────────────────────────────┐
│ Type: Natural Diamond (from Tab 1)                     │
│ Carat: 0.75ct                                          │
│ Certification: GIA (from Tab 3)                        │
│ Price/Carat: ₹39,000 (calculated from Tab 2 + Tab 3)  │
│ Total: ₹29,250                                         │
└────────────────────────────────────────────────────────┘
```

---

## 🔧 How to Populate Empty Tabs

### **Method 1: Automatic (Recommended)**
```
1. Go to: Jewellery Price → Debug
2. Click: "Populate Diamond Data" button
3. Refresh page
4. Check tabs - should now have data!
```

### **Method 2: Manual**
```
1. Go to Diamond Groups → Add:
   - Natural Diamond
   - Lab Grown Diamond
   - Moissanite

2. Go to Diamond Types → Add ranges for each group:
   Natural: 0-0.5ct, 0.5-1ct, 1-2ct, 2-3ct, 3ct+
   Lab Grown: 0-0.5ct, 0.5-1ct, 1-2ct, 2ct+
   Moissanite: 0-1ct, 1ct+

3. Go to Certifications → Add:
   - GIA (+20%)
   - IGI (+15%)
   - HRD (+18%)
   - None (0%)
```

---

## 💡 Key Benefits

### **1. Flexibility**
```
Before: Fixed prices for each diamond
After: Dynamic pricing based on carat ranges
```

### **2. Easy Updates**
```
Before: Update 100 products individually
After: Update one carat range, affects all products
```

### **3. Professional Pricing**
```
Before: 0.50ct = ₹25,000, 1.50ct = ₹25,000 (same!)
After: 0.50ct = ₹25,000, 1.50ct = ₹45,000 (realistic!)
```

### **4. Scalability**
```
Before: Limited to 3 types
After: Add unlimited groups and ranges
```

---

## 🎯 Quick Reference

### **When to Use Each Tab:**

#### **Diamond Groups (Tab 1):**
- Adding new diamond categories
- Managing diamond types (Natural, Lab, etc.)
- Organizing diamond inventory

#### **Diamond Types (Tab 2):**
- Setting carat-based pricing
- Updating market prices
- Creating price tiers

#### **Certifications (Tab 3):**
- Managing certification premiums
- Adjusting for quality grades
- Setting lab-specific pricing

#### **Legacy Tab:**
- Viewing final calculated diamonds
- Quick reference for existing data
- Backward compatibility

---

## 📊 Data Flow Diagram

```
USER CREATES PRODUCT
        ↓
Selects: Natural Diamond (Group ID: 1)
        ↓
Enters: 0.75ct carat weight
        ↓
Selects: GIA Certification (Cert ID: 1)
        ↓
SYSTEM CALCULATES:
        ↓
1. Find Diamond Type:
   Group ID 1 + Carat 0.75
   → Found: 0.50-1.00ct range
   → Base Price: ₹32,500/carat
        ↓
2. Apply Certification:
   GIA = +20%
   → ₹32,500 × 1.20 = ₹39,000/carat
        ↓
3. Calculate Total:
   ₹39,000 × 0.75ct = ₹29,250
        ↓
FINAL PRODUCT PRICE INCLUDES:
Metal Cost + Diamond Cost (₹29,250) + Making + Wastage + GST
```

---

## ✅ Verification Checklist

After populating data, verify:

```
☐ Diamond Groups tab shows 3 groups
☐ Diamond Types tab shows 11 carat ranges
☐ Certifications tab shows 4 certifications
☐ Each group has multiple carat ranges
☐ Prices increase with carat size
☐ Certifications show percentage adjustments
☐ Legacy tab can reference new data
```

---

## 🚀 Next Steps

1. **Populate Data:** Use Debug page button
2. **Verify Tabs:** Check all 3 tabs have data
3. **Test Calculation:** Create test product
4. **Customize Prices:** Adjust for your market
5. **Add Custom Groups:** Cubic Zirconia, etc.

---

**The 3 tabs work together to create the most flexible diamond pricing system!** 💎

**Tab 1 (Groups)** → **Tab 2 (Carat Pricing)** → **Tab 3 (Certifications)** = **Final Price**
