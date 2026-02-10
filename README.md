# 💎 Jewellery Price Calculator for WooCommerce

A comprehensive WordPress plugin for calculating jewellery prices based on metal rates, weight, making charges, and additional costs.

---

## 🚀 Quick Download & Install (v2.5.1)

### **Latest Version: v2.5.1 - Custom Labels with Instant Updates**

**New in v2.5.1:**
- ✅ Custom labels for Pearl Cost, Stone Cost, and Extra Fee
- ✅ **Instant updates** - no regeneration needed!
- ✅ Change labels anytime, see results immediately
- ✅ Works with all existing products

---

## 📥 Download & Install

### **Step 1: Download Plugin**

Click the green **"Code"** button above → **"Download ZIP"**

### **Step 2: Extract & Rename**

1. Extract the ZIP file
2. Rename folder from `jewellery-price-calculator-main` to `jewellery-price-calculator`

### **Step 3: Upload via FTP**

1. Backup your current plugin folder (rename to `jewellery-price-calculator-backup`)
2. Upload `jewellery-price-calculator` to `/wp-content/plugins/`
3. Activate in WordPress Admin > Plugins

### **Step 4: Configure Custom Labels**

1. Go to **Jewellery Price > General Settings**
2. Scroll to **"Additional Cost Fields"**
3. Set custom labels:
   - Pearl Cost Label: "Gemstone Cost" (or any name you want)
   - Stone Cost Label: "Packaging Fee" (or any name you want)
   - Extra Fee Label: "Certification Fee" (or any name you want)
4. Click **Save Changes**
5. View any product - labels appear **instantly**!

**No regeneration needed!** Labels update immediately when you change settings.

---

## 📚 Complete Documentation

- **[Complete Installation Guide](DOWNLOAD-COMPLETE-PLUGIN-v2.5.1.md)** - Detailed step-by-step instructions
- **[Instant Fix Guide](INSTANT-FIX-v2.5.1.md)** - Quick 2-file update method
- **[Custom Labels Testing](TEST-CUSTOM-LABELS.md)** - How to test and verify custom labels

---

## ✨ Features

### Core Features:
- ✅ **Dynamic Price Calculation** - Based on live metal rates
- ✅ **Multiple Metals** - Gold (24K, 22K, 18K, 14K), Silver, Platinum
- ✅ **Diamond Pricing** - Automatic diamond cost calculation
- ✅ **Making Charges** - Percentage or fixed amount
- ✅ **Wastage Charges** - Configurable wastage percentage
- ✅ **GST Support** - Automatic GST calculation
- ✅ **Discount System** - Percentage-based discounts
- ✅ **Price Breakup Display** - Detailed cost breakdown on frontend

### v2.5.1 Features:
- ✅ **Custom Labels** - Rename Pearl Cost, Stone Cost, Extra Fee
- ✅ **Instant Updates** - No regeneration needed
- ✅ **Dynamic Fetching** - Labels always reflect current settings
- ✅ **Backwards Compatible** - Works with existing products

### Additional Features:
- ✅ **Extra Fields** - Up to 5 additional cost fields
- ✅ **Additional Percentage** - Extra percentage-based charges
- ✅ **Bulk Price Update** - Update all products at once
- ✅ **Product-Level Settings** - Override global settings per product
- ✅ **Responsive Design** - Mobile-friendly price breakup display

---

## 🎯 Quick Start

### 1. Install & Activate
Upload plugin and activate in WordPress

### 2. Configure Settings
Go to **Jewellery Price > General Settings**

### 3. Set Metal Rates
Configure current metal prices (auto-updates available)

### 4. Add Products
Create WooCommerce products with JPC data

### 5. Customize Labels (NEW!)
Set custom names for additional cost fields

### 6. View Frontend
Check price breakup on product pages

---

## 🔧 System Requirements

- **WordPress:** 5.0 or higher
- **WooCommerce:** 3.0 or higher
- **PHP:** 7.0 or higher
- **MySQL:** 5.6 or higher

---

## 📖 How It Works

### Price Calculation Formula:

```
Metal Price = Weight × Metal Rate × Purity
Diamond Price = Calculated from diamond data
Making Charge = Metal Price × Making %
Wastage Charge = Metal Price × Wastage %
Pearl Cost = Custom amount (with custom label)
Stone Cost = Custom amount (with custom label)
Extra Fee = Custom amount (with custom label)
Additional % = Subtotal × Additional %
Extra Fields = Custom amounts (1-5)

Subtotal = Sum of all above
GST = Subtotal × GST %
Total = Subtotal + GST
Discount = Total × Discount %
Final Price = Total - Discount
```

---

## 🎨 Custom Labels Feature

### Before v2.5.1:
- Fixed labels: "Pearl Cost", "Stone Cost", "Extra Fee"
- No customization possible

### After v2.5.1:
- **Fully customizable labels**
- Change to any name you want
- Updates **instantly** on frontend
- No regeneration needed

### Example Use Cases:

**Gemstone Jewellery:**
- Pearl Cost → "Gemstone Cost"
- Stone Cost → "Semi-Precious Stones"
- Extra Fee → "Setting Charges"

**Luxury Jewellery:**
- Pearl Cost → "Premium Pearls"
- Stone Cost → "Precious Stones"
- Extra Fee → "Certification Fee"

**Custom Jewellery:**
- Pearl Cost → "Custom Design Fee"
- Stone Cost → "Engraving Cost"
- Extra Fee → "Packaging Premium"

---

## 📋 File Structure

```
jewellery-price-calculator/
├── includes/
│   ├── class-jpc-price-calculator.php  (Core calculation engine)
│   ├── class-jpc-admin.php             (Admin interface)
│   ├── class-jpc-frontend.php          (Frontend display)
│   ├── class-jpc-metals.php            (Metal management)
│   └── class-jpc-diamonds.php          (Diamond calculations)
├── templates/
│   ├── admin/
│   │   ├── general-settings.php        (Settings page)
│   │   └── product-meta-box.php        (Product editor)
│   └── frontend/
│       ├── price-breakup.php           (Price display - v2.5.1)
│       └── detailed-breakup.php        (Detailed view - v2.5.1)
├── assets/
│   ├── css/                            (Stylesheets)
│   └── js/                             (JavaScript)
└── jewellery-price-calculator.php      (Main plugin file)
```

---

## 🔄 Updating from Previous Versions

### From v2.5.0 to v2.5.1:

**Option 1: Full Plugin Update (Recommended)**
1. Download complete plugin
2. Backup current plugin folder
3. Upload new plugin folder
4. Activate plugin
5. Settings are preserved automatically

**Option 2: Quick 2-File Update**
1. Download `price-breakup.php` and `detailed-breakup.php`
2. Upload to `templates/frontend/`
3. Clear cache
4. Done!

### From Earlier Versions:
- Follow full installation guide
- Settings and data are preserved
- Test on staging site first (recommended)

---

## 🆘 Troubleshooting

### Labels Not Showing Custom Names

**Solution:**
1. Clear browser cache (Ctrl+Shift+R)
2. Verify settings are saved
3. Check you uploaded v2.5.1 files
4. Clear WordPress cache

### Plugin Not Activating

**Solution:**
1. Check PHP version (7.0+ required)
2. Check WooCommerce is active
3. Check file permissions (644 for files, 755 for folders)
4. Check WordPress error logs

### Prices Not Calculating

**Solution:**
1. Verify metal rates are set
2. Check product has JPC data
3. Regenerate price breakup (product editor)
4. Check for JavaScript errors in browser console

---

## 📞 Support

- **Documentation:** See guides in repository
- **Issues:** Check troubleshooting section
- **Updates:** Watch repository for new releases

---

## 📝 Changelog

### v2.5.1 (February 9, 2026)
- ✅ **NEW:** Instant label updates - no regeneration needed
- ✅ **IMPROVED:** Labels fetch directly from settings
- ✅ **FIXED:** Backwards compatibility with stored labels
- ✅ **ENHANCED:** Better user experience for label management

### v2.5.0 (February 9, 2026)
- ✅ Custom labels for Pearl Cost, Stone Cost, Extra Fee
- ✅ Settings page integration
- ✅ Label storage in breakup data

### Earlier Versions
- See [CHANGELOG.md](CHANGELOG.md) for complete history

---

## 📄 License

This plugin is proprietary software. All rights reserved.

---

## 🎉 Get Started Now!

1. **Download** the plugin (green "Code" button → Download ZIP)
2. **Install** following the guide above
3. **Configure** your settings
4. **Customize** labels to match your business
5. **Enjoy** instant price calculations!

---

**Latest Version:** v2.5.1  
**Last Updated:** February 9, 2026  
**Compatibility:** WordPress 5.0+, WooCommerce 3.0+

---

**Questions? Check the [Complete Installation Guide](DOWNLOAD-COMPLETE-PLUGIN-v2.5.1.md) for detailed instructions!**
