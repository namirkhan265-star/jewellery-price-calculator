# Changelog

All notable changes to the Jewellery Price Calculator plugin will be documented in this file.

## [1.0.0] - 2025-12-25

### Added
- Initial release of Jewellery Price Calculator plugin
- Multi-metal support (Gold, Silver, Diamond, Platinum)
- Automatic price calculation based on formula: Metal weight × rate + making charges + wastage
- Manual metal rate updates with automatic product price recalculation
- Support for both simple and variable WooCommerce products
- INR currency support optimized for Indian market
- Price breakup display on product pages
- Comprehensive admin dashboard for metal rate management
- Price history logging and tracking
- Bulk metal price update functionality
- Metal groups management system
- Individual metals management with CRUD operations
- Product meta box for jewellery-specific fields
- Making charge support (percentage or fixed amount)
- Wastage charge support (percentage or fixed amount)
- Pearl cost field (optional)
- Stone cost field (optional)
- Extra fee field (optional)
- GST/Tax support with configurable rates
- Metal-specific GST rates (Gold, Silver, Diamond, Platinum)
- Discount system with granular control
- Discount on metals, making charges, and wastage charges
- Price rounding options (nearest 10/50/100, ceil, floor)
- Additional percentage field for extra charges
- 5 customizable extra fields per product
- Frontend price breakup display
- Detailed price breakdown template
- Shortcodes for displaying metal rates
  - List view shortcode
  - Table view shortcode
  - Marquee view shortcode
- Shortcode filtering by specific metal IDs
- Admin CSS styling
- Frontend CSS styling
- Admin JavaScript for AJAX operations
- Database tables for metal groups, metals, price history, and product logs
- Default metal groups (Gold, Silver, Diamond, Platinum)
- Default metals with sample prices
- Price change logging system
- Product price change tracking
- User-friendly admin interface
- Responsive design for mobile devices
- WordPress coding standards compliance
- Security features (nonce verification, capability checks)
- Comprehensive documentation (README.md)
- Installation guide (INSTALLATION.md)
- Shortcode documentation page in admin
- Price history statistics
- Translation ready (text domain: jewellery-price-calc)

### Features
- **Automatic Calculations**: Update metal price once, all products recalculate automatically
- **Flexible Pricing**: Support for percentage and fixed amount charges
- **Comprehensive Logging**: Track every price change with user and timestamp
- **Bulk Operations**: Update multiple metal prices simultaneously
- **Variable Products**: Full support for product variations with individual pricing
- **GST Management**: Global and metal-specific tax rates
- **Discount Control**: Apply discounts selectively on different components
- **Price Display**: Beautiful frontend price breakup for transparency
- **Shortcodes**: Display current metal rates anywhere on your site
- **Admin Dashboard**: Intuitive interface for managing all aspects

### Database Schema
- `wp_jpc_metal_groups` - Stores metal group definitions
- `wp_jpc_metals` - Stores individual metals with current prices
- `wp_jpc_price_history` - Logs all metal price changes
- `wp_jpc_product_price_log` - Logs all product price changes

### Shortcodes
- `[jpc_metal_rates]` - Display metal rates in list format
- `[jpc_metal_rates template="table"]` - Display in table format
- `[jpc_metal_rates template="marquee"]` - Display in scrolling marquee
- `[jpc_metal_rates metals="1,2,3"]` - Display specific metals only

### Admin Pages
- General Settings - Configure fields, GST, rounding, display options
- Metal Groups - Manage metal categories
- Metals - Manage individual metals and prices
- Discount Settings - Configure discount options
- Price History - View all price changes
- Shortcodes - Documentation and usage examples

### Security
- Nonce verification on all AJAX requests
- Capability checks (manage_woocommerce)
- Input sanitization and validation
- SQL injection prevention with prepared statements
- XSS protection with proper escaping

### Performance
- Efficient database queries
- Minimal frontend assets loading
- Optimized AJAX operations
- Proper WordPress hooks usage

### Compatibility
- WordPress 5.8+
- WooCommerce 5.0+
- PHP 7.4+
- MySQL 5.6+

### Known Limitations
- Currently supports INR currency (can be extended)
- Manual price updates only (no API integration in v1.0)
- Single-site only (multisite support planned for future)

## [Unreleased]

### Planned Features
- API integration for automatic metal rate updates
- Multi-currency support
- Export/Import functionality for metal prices
- Email notifications on price changes
- Advanced reporting and analytics
- Multisite support
- REST API endpoints
- Mobile app integration
- CSV bulk import for products
- Price comparison charts
- Historical price graphs
- Customer price alerts
- Wholesale pricing tiers
- Role-based pricing
- Integration with popular page builders

---

## Version History

- **1.0.0** (2025-12-25) - Initial Release

---

## Upgrade Notice

### 1.0.0
Initial release. No upgrade needed.

---

## Support

For bug reports, feature requests, or support:
- GitHub Issues: https://github.com/namirkhan265-star/jewellery-price-calculator/issues
- Email: brandwitty@gmail.com

---

## Credits

Developed by Brand Witty
Powered by Bhindi.io
