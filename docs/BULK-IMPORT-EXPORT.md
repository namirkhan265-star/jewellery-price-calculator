# Bulk Import/Export Guide

## Overview
Version 1.2.0 adds complete support for bulk importing and exporting all Jewellery Price Calculator fields using WooCommerce's native CSV import/export feature.

## New CSV Columns Added

When you export products, you'll now see these additional columns:

| Column Name | Description | Example Value |
|------------|-------------|---------------|
| `JPC Metal ID` | ID of the metal from your metals database | `1` |
| `JPC Metal Weight (grams)` | Weight of metal in grams | `4.32` |
| `JPC Diamond ID` | ID of the diamond from your diamonds database | `2` |
| `JPC Diamond Quantity` | Number of diamonds used | `10` |
| `JPC Making Charge` | Making charge value | `20` or `500` |
| `JPC Making Charge Type` | Type of making charge | `percentage` or `fixed` |
| `JPC Wastage Charge` | Wastage charge value | `5` or `200` |
| `JPC Wastage Charge Type` | Type of wastage charge | `percentage` or `fixed` |
| `JPC Pearl Cost` | Pearl cost (if enabled) | `1000` |
| `JPC Stone Cost` | Stone cost (if enabled) | `500` |
| `JPC Extra Fee` | Extra fee (if enabled) | `300` |
| `JPC Discount Percentage` | Discount percentage | `10` |

## How to Export Products

1. Go to **WooCommerce → Products**
2. Click **Export** button at the top
3. Select columns to export (all JPC columns are included by default)
4. Click **Generate CSV**
5. Download the CSV file

**All jewellery calculator fields will be included automatically!**

## How to Import Products

### Step 1: Prepare Your CSV File

Your CSV should include these columns:
```csv
SKU,Name,Regular price,JPC Metal ID,JPC Metal Weight (grams),JPC Diamond ID,JPC Diamond Quantity,JPC Making Charge,JPC Making Charge Type,JPC Wastage Charge,JPC Wastage Charge Type,JPC Pearl Cost,JPC Stone Cost,JPC Extra Fee,JPC Discount Percentage
RING001,Gold Ring,0,1,4.32,2,10,20,percentage,5,percentage,0,0,0,10
RING002,Diamond Ring,0,1,5.50,3,15,500,fixed,200,fixed,0,0,0,5
```

### Step 2: Import the CSV

1. Go to **WooCommerce → Products**
2. Click **Import** button at the top
3. Choose your CSV file
4. Click **Continue**
5. **Map the columns** - WooCommerce will auto-detect JPC columns
6. Click **Run the importer**

### Step 3: Automatic Price Calculation

**Important:** When you import products with JPC fields, the plugin will:
- ✅ Automatically calculate the final price
- ✅ Save the price breakup
- ✅ Update the product price
- ✅ Apply all your calculator settings (GST, rounding, etc.)

**You don't need to manually enter prices!** Just provide the calculator fields.

## Finding Metal and Diamond IDs

### Method 1: Export Existing Products
1. Export your products
2. Check the `JPC Metal ID` and `JPC Diamond ID` columns
3. Use these IDs in your import file

### Method 2: Check Admin Pages
1. Go to **Jewellery Calculator → Metals**
2. Hover over a metal name - the ID is in the URL: `?id=1`
3. Go to **Jewellery Calculator → Diamonds**
4. Hover over a diamond - the ID is in the URL: `?id=2`

### Method 3: Database Query
Run this in phpMyAdmin or your database tool:

**Get all metals:**
```sql
SELECT id, name FROM wp_jpc_metals;
```

**Get all diamonds:**
```sql
SELECT id, type, carat, price_per_carat FROM wp_jpc_diamonds;
```

## Example Import Scenarios

### Scenario 1: Update Metal Weight for All Products
```csv
ID,JPC Metal Weight (grams)
123,4.50
124,5.20
125,3.80
```

### Scenario 2: Add Diamonds to Existing Products
```csv
ID,JPC Diamond ID,JPC Diamond Quantity
123,2,10
124,3,15
125,2,8
```

### Scenario 3: Update Making Charges
```csv
ID,JPC Making Charge,JPC Making Charge Type
123,20,percentage
124,500,fixed
125,15,percentage
```

### Scenario 4: Bulk Discount Update
```csv
ID,JPC Discount Percentage
123,10
124,15
125,5
```

## Important Notes

### ✅ What Works
- Import/export all calculator fields
- Automatic price recalculation on import
- Update existing products
- Create new products with calculator fields
- Bulk updates to specific fields

### ⚠️ Important Rules
1. **Metal ID must exist** - Check your metals database first
2. **Diamond ID must exist** - Check your diamonds database first
3. **Making/Wastage Type** - Must be either `percentage` or `fixed`
4. **Numeric values** - Use numbers only (no currency symbols)
5. **Decimal separator** - Use dot (.) not comma (,)

### 🔄 Price Recalculation
- Prices are **automatically recalculated** after import
- Uses your current metal/diamond prices
- Applies all your calculator settings
- No manual price entry needed!

## Troubleshooting

### Problem: Prices not calculating after import
**Solution:** Make sure:
- Metal ID exists in your metals database
- Metal weight is provided
- Metal and diamond IDs are valid numbers

### Problem: Import shows errors
**Solution:** Check:
- CSV encoding is UTF-8
- Column names match exactly (case-sensitive)
- No special characters in numeric fields
- Making/Wastage types are `percentage` or `fixed`

### Problem: Some fields not importing
**Solution:**
- Re-map columns during import
- Check column names match exactly
- Ensure CSV has proper headers

## Best Practices

### 1. Always Export First
Before bulk importing, export your current products to:
- Get the correct column format
- See existing Metal/Diamond IDs
- Have a backup

### 2. Test with Small Batch
- Import 5-10 products first
- Verify prices calculate correctly
- Check all fields imported properly

### 3. Use Product IDs for Updates
- Include the `ID` column when updating
- This ensures you update the right products
- Prevents creating duplicates

### 4. Keep Metal/Diamond Prices Updated
- Import uses current prices from database
- Update metal/diamond prices before importing
- Prices will be calculated with latest rates

## Advanced Tips

### Bulk Update Only Calculator Fields
You can update just the calculator fields without touching other product data:
```csv
ID,JPC Metal Weight (grams),JPC Making Charge
123,4.50,20
124,5.20,25
```

### Create Products with Calculator Fields
Include all required WooCommerce fields + calculator fields:
```csv
Type,SKU,Name,Published,JPC Metal ID,JPC Metal Weight (grams)
simple,RING001,Gold Ring,1,1,4.32
simple,RING002,Silver Ring,1,2,5.50
```

### Update Prices for Specific Metal Type
1. Export all products
2. Filter by metal ID in Excel
3. Update fields for those products
4. Import back

## Support

If you encounter issues:
1. Check this guide first
2. Verify your CSV format
3. Test with a small batch
4. Check WooCommerce import logs

---

**Version:** 1.2.0  
**Last Updated:** December 2024
