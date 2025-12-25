# Jewellery Price Calculator - WordPress Plugin

A comprehensive WordPress/WooCommerce plugin for automatic jewellery price calculation based on metal rates with support for Gold, Silver, Diamond, and Platinum.

## Features

### Core Features
- ✅ **Multi-Metal Support**: Gold, Silver, Diamond, Platinum
- ✅ **Automatic Price Calculation**: Metal weight × rate + making charges + wastage
- ✅ **Manual Rate Updates**: Update rates daily, auto-calculates all product prices
- ✅ **Simple & Variable Products**: Full support for both product types
- ✅ **INR Currency**: Optimized for Indian market
- ✅ **Price Breakup Display**: Show detailed price breakdown on product pages
- ✅ **Admin Dashboard**: Comprehensive metal rate management
- ✅ **Price History/Logs**: Track all price changes
- ✅ **Bulk Updates**: Update multiple metal prices at once

### Advanced Features
- **Metal Groups**: Organize metals into groups (Gold, Silver, etc.)
- **Flexible Charges**: Making and wastage charges (percentage or fixed)
- **Additional Costs**: Pearl cost, stone cost, extra fees
- **GST Support**: Configurable GST with metal-specific rates
- **Discount System**: Apply discounts on metals, making, or wastage
- **Price Rounding**: Multiple rounding options
- **Shortcodes**: Display today's metal rates anywhere
- **Extra Fields**: 5 customizable fields per product

## Installation

### Requirements
- WordPress 5.8 or higher
- WooCommerce 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

### Steps

1. **Download the Plugin**
   ```bash
   git clone https://github.com/namirkhan265-star/jewellery-price-calculator.git
   ```

2. **Upload to WordPress**
   - Compress the `jewellery-price-calculator` folder to a ZIP file
   - Go to WordPress Admin → Plugins → Add New → Upload Plugin
   - Upload the ZIP file and click "Install Now"
   - Activate the plugin

3. **Initial Setup**
   - Go to **Jewellery Price → Metal Groups**
   - Default groups (Gold, Silver, Diamond, Platinum) are pre-configured
   - Go to **Jewellery Price → Metals**
   - Default metals (14kt Gold, 18kt Gold, 22kt Gold, Silver, Platinum) are pre-configured with sample prices

## Usage

### Setting Up Metal Rates

1. **Navigate to Jewellery Price → Metals**
2. **Update Metal Prices**:
   - Edit existing metals or add new ones
   - Enter price per gram/carat
   - Click "Update Metal"
   - All products using this metal will automatically recalculate

### Configuring Products

1. **Edit a Product** in WooCommerce
2. **Scroll to "Jewellery Price Calculator" Meta Box**
3. **Configure**:
   - Select Metal (e.g., 18kt Gold)
   - Enter Weight (in grams/carats)
   - Enter Making Charge (% or fixed amount)
   - Enter Wastage Charge (% or fixed amount)
   - Add Pearl Cost, Stone Cost, Extra Fees (optional)
   - Set Discount Percentage (optional)
4. **Save Product** - Price calculates automatically!

### Price Calculation Formula

```
Metal Price = Weight × Metal Rate per Unit

Making Charge = Metal Price × Making % (or fixed amount)

Wastage Charge = Metal Price × Wastage % (or fixed amount)

Subtotal = Metal Price + Making + Wastage + Pearl + Stone + Extra Fee

Discount = (Discountable Amount × Discount %)

Subtotal After Discount = Subtotal - Discount

GST = Subtotal After Discount × GST %

Final Price = Subtotal After Discount + GST
```

### General Settings

**Navigate to Jewellery Price → General**

- **Enable Pearl Cost Field**: Add pearl cost to products
- **Enable Stone Cost Field**: Add stone cost to products
- **Enable Extra Fee Field**: Add additional fees
- **Additional Percentage**: Add percentage to total before tax
- **Extra Fields (1-5)**: Custom fields with labels
- **GST Settings**:
  - Enable/Disable GST
  - Set GST label and percentage
  - Metal-specific GST rates
- **Price Rounding**: Choose rounding method
- **Show Price Breakup**: Display on product pages

### Discount Settings

**Navigate to Jewellery Price → Discount**

- **Enable Discount Feature**: Turn on/off discounts
- **Enable Discount on Metals**: Apply discount to metal price
- **Enable Discount on Making Charge**: Apply discount to making charges
- **Enable Discount on Wastage Charge**: Apply discount to wastage charges

### Shortcodes

Display today's metal rates on any page:

**List View:**
```
[jpc_metal_rates]
```

**Table View:**
```
[jpc_metal_rates template="table"]
```

**Marquee View:**
```
[jpc_metal_rates template="marquee"]
```

**Specific Metals Only:**
```
[jpc_metal_rates metals="1,2,3"]
```

### Variable Products

For variable products (different sizes/variants):

1. Create product variations as usual
2. Each variation gets its own metal configuration
3. Set different weights/charges per variation
4. Prices calculate independently for each variation

## Database Structure

The plugin creates 4 custom tables:

- `wp_jpc_metal_groups` - Metal group definitions
- `wp_jpc_metals` - Individual metals with prices
- `wp_jpc_price_history` - Metal price change log
- `wp_jpc_product_price_log` - Product price change log

## Price History

**Navigate to Jewellery Price → Price History**

View complete history of:
- Metal price changes
- Who made the change
- When it was changed
- Old vs new prices
- Affected products

## Bulk Operations

**Update Multiple Metal Prices:**

1. Go to **Jewellery Price → Metals**
2. Edit prices directly in the table
3. Click **"Update All Prices"**
4. All product prices recalculate automatically

## Frontend Display

### Price Breakup on Product Page

When enabled, shows:
- Metal price breakdown
- Making charges
- Wastage charges
- Additional costs
- Discount applied
- GST amount
- Final price

### Customization

Templates are located in:
```
templates/frontend/price-breakup.php
templates/frontend/detailed-breakup.php
```

Copy to your theme to customize:
```
your-theme/jewellery-price-calculator/price-breakup.php
```

## Developer Hooks

### Actions

```php
// After price calculation
do_action('jpc_after_price_calculation', $product_id, $final_price);

// After metal price update
do_action('jpc_after_metal_update', $metal_id, $old_price, $new_price);
```

### Filters

```php
// Modify calculated price
apply_filters('jpc_calculated_price', $price, $product_id);

// Modify price breakup
apply_filters('jpc_price_breakup', $breakup, $product_id);
```

## Troubleshooting

### Prices Not Updating?

1. Check if metal is assigned to product
2. Verify weight is entered
3. Check if WooCommerce is active
4. Clear cache if using caching plugin

### Price Breakup Not Showing?

1. Go to **Jewellery Price → General**
2. Enable "Show Price Breakup"
3. Clear theme cache

### Database Issues?

Deactivate and reactivate plugin to recreate tables.

## Support

For issues, feature requests, or contributions:
- GitHub: https://github.com/namirkhan265-star/jewellery-price-calculator
- Email: brandwitty@gmail.com

## Changelog

### Version 1.0.0
- Initial release
- Multi-metal support (Gold, Silver, Diamond, Platinum)
- Automatic price calculation
- Price history logging
- Bulk update functionality
- Shortcodes for metal rates display
- GST and discount support
- Variable product support

## License

GPL v2 or later

## Credits

Developed by Brand Witty
Powered by Bhindi.io

---

**Made with ❤️ for the Jewellery Industry**
