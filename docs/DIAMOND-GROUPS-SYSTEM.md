# Diamond Groups & Structured Pricing System - v1.4.0

## 🎯 Overview

Version 1.4.0 introduces a **revolutionary diamond management system** similar to the metal groups structure. This makes diamond pricing more flexible, maintainable, and scalable.

---

## 🏗️ New Structure

### **3-Tier Diamond System:**

```
Diamond Groups (Types)
    ↓
Diamond Types (Carat Ranges)
    ↓
Diamond Certifications (Price Adjustments)
```

---

## 📊 1. Diamond Groups

**What are Diamond Groups?**
- Categories of diamonds (like Metal Groups)
- Examples: Natural Diamond, Lab Grown, Moissanite
- Can add unlimited new types!

### **Default Diamond Groups:**

| Group | Description |
|-------|-------------|
| **Natural Diamond** | Naturally mined diamonds formed over billions of years |
| **Lab Grown Diamond** | Laboratory-created diamonds with same properties |
| **Moissanite** | Silicon carbide gemstone with diamond-like appearance |

### **Add Custom Groups:**
```
Admin → Jewellery Calculator → Diamond Groups → Add New

Examples:
- Cubic Zirconia
- White Sapphire
- Synthetic Diamond
- Colored Diamonds
```

---

## 💎 2. Diamond Types (Carat-Based Pricing)

**What are Diamond Types?**
- Carat ranges within each diamond group
- Each range has its own price per carat
- Larger diamonds = Higher price per carat (exponential pricing)

### **Example: Natural Diamond Pricing Table**

| Carat Range | Price/Carat | Why? |
|------------|-------------|------|
| 0.00 - 0.50ct | ₹25,000 | Small stones, base price |
| 0.50 - 1.00ct | ₹32,500 | Medium stones, +30% |
| 1.00 - 2.00ct | ₹45,000 | Large stones, +80% |
| 2.00 - 3.00ct | ₹62,500 | Premium size, +150% |
| 3.00ct+ | ₹87,500 | Rare size, +250% |

### **Example: Lab Grown Diamond Pricing Table**

| Carat Range | Price/Carat | Savings vs Natural |
|------------|-------------|-------------------|
| 0.00 - 0.50ct | ₹15,000 | 40% cheaper |
| 0.50 - 1.00ct | ₹19,500 | 40% cheaper |
| 1.00 - 2.00ct | ₹27,000 | 40% cheaper |
| 2.00ct+ | ₹37,500 | 40% cheaper |

### **Example: Moissanite Pricing Table**

| Carat Range | Price/Carat | Savings vs Natural |
|------------|-------------|-------------------|
| 0.00 - 1.00ct | ₹5,000 | 80% cheaper |
| 1.00ct+ | ₹6,500 | 80% cheaper |

---

## 🏆 3. Diamond Certifications

**What are Certifications?**
- Quality certificates from gemological labs
- Affect final price with fixed or percentage adjustments
- Can be positive (premium) or negative (discount)

### **Default Certifications:**

| Certification | Type | Adjustment | Final Impact |
|--------------|------|-----------|--------------|
| **GIA** | Percentage | +20% | Premium certification |
| **IGI** | Percentage | +15% | Standard certification |
| **HRD** | Percentage | +18% | High-quality certification |
| **None** | Percentage | 0% | No certification |

### **Adjustment Types:**

#### **1. Percentage Adjustment:**
```
Final Price = Base Price × (1 + Adjustment%)

Example:
Base Price: ₹25,000/carat
GIA (+20%): ₹25,000 × 1.20 = ₹30,000/carat
```

#### **2. Fixed Adjustment:**
```
Final Price = Base Price + Fixed Amount

Example:
Base Price: ₹25,000/carat
Premium Cert (+₹5,000): ₹25,000 + ₹5,000 = ₹30,000/carat
```

---

## 🧮 Complete Price Calculation

### **Formula:**
```
Step 1: Find diamond type based on carat range
Step 2: Get base price per carat from diamond type
Step 3: Apply certification adjustment
Step 4: Calculate unit price = Adjusted Price × Carat
Step 5: Calculate total = Unit Price × Quantity
```

### **Example Calculation:**

**Product:** Gold Ring with Natural Diamonds
- Diamond Group: Natural Diamond
- Carat per Diamond: 0.75ct
- Certification: GIA
- Quantity: 10 diamonds

**Step-by-Step:**

```
Step 1: Find Diamond Type
Carat: 0.75ct falls in range 0.50-1.00ct
Base Price: ₹32,500/carat

Step 2: Apply Certification
GIA = +20%
Adjusted Price = ₹32,500 × 1.20 = ₹39,000/carat

Step 3: Calculate Unit Price
Unit Price = ₹39,000 × 0.75ct = ₹29,250 per diamond

Step 4: Calculate Total
Total = ₹29,250 × 10 = ₹2,92,500
```

---

## 📥 CSV Import Format

### **New CSV Columns:**

```csv
JPC Diamond Group ID,JPC Diamond Carat,JPC Diamond Certification ID,JPC Diamond Quantity
```

### **Example CSV:**

```csv
SKU,Name,JPC Metal ID,JPC Metal Weight,JPC Diamond Group ID,JPC Diamond Carat,JPC Diamond Certification ID,JPC Diamond Quantity
RING001,Gold Ring,1,4.32,1,0.50,1,10
RING002,Silver Ring,2,3.50,2,0.30,2,8
RING003,Pendant,1,2.80,3,0.25,4,5
```

### **How to Find IDs:**

#### **Diamond Group IDs:**
```
Admin → Jewellery Calculator → Diamond Groups

1 = Natural Diamond
2 = Lab Grown Diamond
3 = Moissanite
```

#### **Certification IDs:**
```
Admin → Jewellery Calculator → Diamond Certifications

1 = GIA
2 = IGI
3 = HRD
4 = None
```

---

## 🎨 Admin Interface

### **1. Diamond Groups Tab:**
```
┌─────────────────────────────────────────┐
│ Diamond Groups                          │
├─────────────────────────────────────────┤
│ [Add New Group]                         │
│                                         │
│ ID | Name              | Description    │
│ 1  | Natural Diamond   | Naturally...   │
│ 2  | Lab Grown Diamond | Laboratory...  │
│ 3  | Moissanite        | Silicon...     │
└─────────────────────────────────────────┘
```

### **2. Diamond Types Tab (Carat Pricing):**
```
┌─────────────────────────────────────────┐
│ Diamond Types & Carat Pricing           │
├─────────────────────────────────────────┤
│ Group: [Natural Diamond ▼]              │
│ [Add New Range]                         │
│                                         │
│ Carat From | Carat To | Price/Carat    │
│ 0.00       | 0.50     | ₹25,000        │
│ 0.50       | 1.00     | ₹32,500        │
│ 1.00       | 2.00     | ₹45,000        │
│ 2.00       | 3.00     | ₹62,500        │
│ 3.00       | 999.99   | ₹87,500        │
└─────────────────────────────────────────┘
```

### **3. Diamond Certifications Tab:**
```
┌─────────────────────────────────────────┐
│ Diamond Certifications                  │
├─────────────────────────────────────────┤
│ [Add New Certification]                 │
│                                         │
│ Name | Type       | Adjustment         │
│ GIA  | Percentage | +20%               │
│ IGI  | Percentage | +15%               │
│ HRD  | Percentage | +18%               │
│ None | Percentage | 0%                 │
└─────────────────────────────────────────┘
```

---

## ✅ Benefits

### **1. Flexible Pricing:**
```
Before: Fixed price per diamond
After: Dynamic pricing based on carat range
```

### **2. Easy Updates:**
```
Before: Update each diamond individually
After: Update carat range, affects all products
```

### **3. Scalable:**
```
Before: Limited to pre-defined types
After: Add unlimited diamond groups
```

### **4. Professional:**
```
Before: Simple flat pricing
After: Industry-standard tiered pricing
```

---

## 🔄 Migration from v1.3.0

### **Old System (v1.3.0):**
```
Diamond Type: natural
Diamond Carat: 0.50
Diamond Certification: gia
```

### **New System (v1.4.0):**
```
Diamond Group ID: 1 (Natural Diamond)
Diamond Carat: 0.50
Diamond Certification ID: 1 (GIA)
```

### **Migration Steps:**

1. **Export existing products**
2. **Map old values to new IDs:**
   - `natural` → Diamond Group ID: 1
   - `lab_grown` → Diamond Group ID: 2
   - `moissanite` → Diamond Group ID: 3
   - `gia` → Certification ID: 1
   - `igi` → Certification ID: 2
   - `hrd` → Certification ID: 3
   - `none` → Certification ID: 4
3. **Update CSV with new IDs**
4. **Re-import products**

---

## 📝 Use Cases

### **Use Case 1: Jewelry Store with Multiple Diamond Types**
```
Setup:
- Natural Diamond (5 carat ranges)
- Lab Grown (4 carat ranges)
- Moissanite (2 carat ranges)
- 4 certification types

Result: 44 possible diamond combinations!
```

### **Use Case 2: Budget-Friendly Options**
```
Add new group: Cubic Zirconia
Carat ranges:
- 0.00-1.00ct: ₹500/carat
- 1.00ct+: ₹600/carat

Offer affordable alternatives!
```

### **Use Case 3: Premium Colored Diamonds**
```
Add new group: Colored Diamonds
Carat ranges:
- 0.00-0.50ct: ₹50,000/carat
- 0.50-1.00ct: ₹75,000/carat
- 1.00ct+: ₹1,00,000/carat

Premium pricing for rare stones!
```

---

## 🎯 Best Practices

### **1. Carat Range Setup:**
```
✅ No gaps: 0.00-0.50, 0.50-1.00, 1.00-2.00
❌ With gaps: 0.00-0.50, 0.60-1.00 (missing 0.50-0.60)
```

### **2. Pricing Strategy:**
```
✅ Exponential: Small stones cheaper, large stones premium
❌ Linear: Same price increase for all ranges
```

### **3. Certification Adjustments:**
```
✅ Percentage for consistency across all ranges
❌ Fixed amount (unless specific use case)
```

### **4. Group Organization:**
```
✅ Clear names: "Natural Diamond", "Lab Grown Diamond"
❌ Vague names: "Type 1", "Type 2"
```

---

## 🔧 Customization Examples

### **Example 1: Add Cubic Zirconia**
```
1. Go to Diamond Groups → Add New
   Name: Cubic Zirconia
   Description: Budget-friendly diamond alternative

2. Go to Diamond Types → Select "Cubic Zirconia"
   Add ranges:
   - 0.00-1.00ct: ₹500/carat
   - 1.00ct+: ₹600/carat

3. Products can now use Cubic Zirconia!
```

### **Example 2: Add Custom Certification**
```
1. Go to Diamond Certifications → Add New
   Name: AGS
   Type: Percentage
   Adjustment: +22%
   Description: American Gem Society

2. Now available in product selection!
```

### **Example 3: Seasonal Pricing**
```
1. Update carat ranges for sale:
   Natural Diamond 0.50-1.00ct
   Old: ₹32,500/carat
   New: ₹29,250/carat (10% off)

2. All products with 0.50-1.00ct diamonds updated!
```

---

## 📊 Comparison: Old vs New

| Feature | v1.3.0 | v1.4.0 |
|---------|--------|--------|
| Diamond Types | Fixed 3 types | Unlimited groups |
| Carat Pricing | Single price | Range-based pricing |
| Certifications | Hardcoded | Flexible adjustments |
| Price Updates | Manual per diamond | Update range, affects all |
| Scalability | Limited | Unlimited |
| CSV Import | Text-based | ID-based (clearer) |
| Admin Interface | Simple | Professional tables |

---

## 🚀 Getting Started

### **Step 1: Review Default Setup**
```
Admin → Jewellery Calculator → Diamond Groups
Check: Natural, Lab Grown, Moissanite
```

### **Step 2: Customize Pricing**
```
Admin → Jewellery Calculator → Diamond Types
Adjust prices for your market
```

### **Step 3: Configure Certifications**
```
Admin → Jewellery Calculator → Diamond Certifications
Add/modify certification premiums
```

### **Step 4: Import Products**
```
Use new CSV format with Group IDs and Cert IDs
Prices calculate automatically!
```

---

**Version:** 1.4.0  
**Last Updated:** December 2024  
**Author:** Brandwitty
