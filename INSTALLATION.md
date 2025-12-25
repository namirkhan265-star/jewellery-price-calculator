# Installation & Setup Guide

## Quick Start Guide

### Step 1: Install the Plugin

1. **Download** the plugin from GitHub
2. **Compress** the folder to a ZIP file
3. Go to **WordPress Admin → Plugins → Add New**
4. Click **Upload Plugin**
5. Choose the ZIP file and click **Install Now**
6. Click **Activate Plugin**

### Step 2: Verify Installation

After activation, you should see a new menu item **"Jewellery Price"** in your WordPress admin sidebar with a diamond icon.

### Step 3: Configure Metal Groups (Pre-configured)

The plugin comes with default metal groups:
- **Gold** (gm) - Making & Wastage enabled
- **Silver** (gm) - Making & Wastage enabled  
- **Diamond** (ct) - No charges
- **Platinum** (gm) - Making & Wastage enabled

Navigate to **Jewellery Price → Metal Groups** to view or modify.

### Step 4: Configure Metals (Pre-configured)

Default metals are already set up:
- **14 Karat Gold** - ₹3,234.10/gm
- **18 Karat Gold** - ₹4,158.15/gm
- **22 Karat Gold** - ₹5,082.20/gm
- **Silver** - ₹66.80/gm
- **Platinum** - ₹2,800.00/gm

Navigate to **Jewellery Price → Metals** to update prices.

### Step 5: Update Metal Prices Daily

1. Go to **Jewellery Price → Metals**
2. Click the **Edit** button next to any metal
3. Update the **Price/gram** field
4. Click **Update Metal**
5. ✅ All products using this metal will automatically recalculate!

**OR use Bulk Update:**
1. Edit prices directly in the table
2. Click **"Update All Prices"** button
3. Confirm the update
4. ✅ All product prices recalculate instantly!

### Step 6: Configure Your First Product

1. Go to **Products → Add New** (or edit existing)
2. Scroll to **"Jewellery Price Calculator"** meta box
3. Fill in the details:

   **Required Fields:**
   - Select Metal: Choose from dropdown (e.g., 18kt Gold)
   - Metal Weight: Enter weight in grams (e.g., 5.5)

   **Optional Fields:**
   - Making Charge: Enter % or fixed amount (e.g., 15%)
   - Wastage Charge: Enter % or fixed amount (e.g., 8%)
   - Pearl Cost: Fixed amount if applicable
   - Stone Cost: Fixed amount if applicable
   - Extra Fee: Any additional charges
   - Discount %: Discount percentage (0-100)

4. Click **Publish** or **Update**
5. ✅ Price calculates automatically!

### Step 7: Configure General Settings

Navigate to **Jewellery Price → General**

**Enable Additional Fields:**
- ☑️ Enable Pearl Cost Field
- ☑️ Enable Stone Cost Field
- ☑️ Enable Extra Fee Field

**GST Configuration:**
- ☑️ Include GST Tax in Product Price
- GST Label: "Tax" or "GST"
- GST Value: 5% (default)
- Metal-specific GST:
  - GST for Gold: 3%
  - GST for Silver: 3%
  - GST for Diamond: 0.25%
  - GST for Platinum: 3%

**Price Rounding:**
- Default (no rounding)
- Nearest 10
- Nearest 50
- Nearest 100
- Ceil to 10/50/100
- Floor to 10/50/100

**Display Options:**
- ☑️ Show Price Breakup on Product Pages

### Step 8: Configure Discount Settings

Navigate to **Jewellery Price → Discount**

- ☑️ Enable Discount Feature
- ☑️ Enable Discount on Metals
- ☑️ Enable Discount on Making Charge
- ☑️ Enable Discount on Wastage Charge

Now you can apply discounts per product!

### Step 9: Display Metal Rates on Your Site

Use shortcodes to display today's metal rates:

**On any page/post, add:**

```
[jpc_metal_rates template="table"]
```

**Or for a marquee:**
```
[jpc_metal_rates template="marquee"]
```

**Or for a simple list:**
```
[jpc_metal_rates]
```

### Step 10: Test Everything

1. **Create a test product**
2. **Assign metal and weight**
3. **Save and view on frontend**
4. **Verify price breakup displays**
5. **Update metal price**
6. **Verify product price updates automatically**

## Daily Workflow

### Morning Routine (Update Prices)

1. Check today's metal rates from your source
2. Go to **Jewellery Price → Metals**
3. Update prices for all metals
4. Click **"Update All Prices"**
5. ✅ Done! All products updated automatically

### Adding New Products

1. **Products → Add New**
2. Fill product details (name, description, images)
3. Scroll to **Jewellery Price Calculator**
4. Select metal and enter weight
5. Add making/wastage charges
6. **Publish**
7. ✅ Price calculated automatically!

### Applying Discounts

1. Edit product
2. Scroll to **Jewellery Price Calculator**
3. Enter **Discount Percentage**
4. **Update**
5. ✅ Discounted price applied!

## Variable Products Setup

For products with multiple sizes/variants:

1. Create **Variable Product**
2. Add **Attributes** (e.g., Size: Small, Medium, Large)
3. Go to **Variations** tab
4. **Generate variations**
5. For each variation:
   - Expand variation
   - Scroll to **Jewellery Price Calculator** section
   - Enter weight and charges for that size
   - Save variation
6. ✅ Each size has its own calculated price!

## Troubleshooting

### Price Not Calculating?

**Check:**
- ✅ Metal is selected
- ✅ Weight is entered
- ✅ Product is saved
- ✅ WooCommerce is active

### Price Breakup Not Showing?

**Check:**
- ✅ Go to Jewellery Price → General
- ✅ Enable "Show Price Breakup"
- ✅ Clear cache if using caching plugin

### Bulk Update Not Working?

**Check:**
- ✅ Edit prices in the table first
- ✅ Click "Update All Prices" button
- ✅ Confirm the dialog
- ✅ Wait for success message

### Database Tables Missing?

**Solution:**
1. Deactivate plugin
2. Reactivate plugin
3. Tables will be recreated

## Advanced Features

### Custom Price Formula

The plugin uses this formula:

```
Metal Price = Weight × Rate
Making Charge = Metal Price × Making% (or fixed)
Wastage Charge = Metal Price × Wastage% (or fixed)
Subtotal = Metal + Making + Wastage + Pearl + Stone + Extra
Discount = Discountable Amount × Discount%
After Discount = Subtotal - Discount
GST = After Discount × GST%
Final Price = After Discount + GST
```

### Price History

View complete history:
- **Jewellery Price → Price History**
- See all metal price changes
- Track who made changes
- View affected products

### Shortcode Options

**Display specific metals only:**
```
[jpc_metal_rates metals="1,2,3"]
```

**Different templates:**
```
[jpc_metal_rates template="list"]
[jpc_metal_rates template="table"]
[jpc_metal_rates template="marquee"]
```

## Support

Need help? Contact:
- **Email:** brandwitty@gmail.com
- **GitHub:** https://github.com/namirkhan265-star/jewellery-price-calculator

## Next Steps

1. ✅ Install and activate
2. ✅ Update metal prices
3. ✅ Configure first product
4. ✅ Test on frontend
5. ✅ Add shortcodes to pages
6. ✅ Start selling!

---

**You're all set! Happy selling! 💎**
