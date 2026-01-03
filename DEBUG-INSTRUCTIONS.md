# EMERGENCY DEBUG INSTRUCTIONS

## Enable WordPress Debug Mode

Add these lines to your `wp-config.php` file (before the line that says "That's all, stop editing!"):

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);
```

## Check Error Logs

After enabling debug mode, try activating the plugin again. Then check:

1. **WordPress debug log:** `/wp-content/debug.log`
2. **PHP error log:** Usually in your hosting control panel or `/var/log/php-errors.log`
3. **Server error log:** Check your hosting control panel

## Send Me the Error

Once you have the error message, send it to me. It will look something like:

```
Fatal error: Uncaught Error: Call to undefined function...
or
PHP Fatal error: Cannot redeclare class...
or
PHP Parse error: syntax error...
```

## Quick Check - File Permissions

Make sure all plugin files have correct permissions:
- Folders: 755
- Files: 644

## Alternative: Check via FTP

If you can't access WordPress admin:

1. Connect via FTP
2. Go to `/wp-content/plugins/jewellery-price-calculator/`
3. Check if ALL these files exist:
   - jewellery-price-calculator.php
   - includes/class-jpc-database.php
   - includes/class-jpc-metal-groups.php
   - includes/class-jpc-metals.php
   - includes/class-jpc-diamond-groups.php
   - includes/class-jpc-diamond-types.php
   - includes/class-jpc-diamond-certifications.php
   - includes/class-jpc-diamonds.php
   - includes/class-jpc-price-calculator.php
   - includes/class-jpc-product-meta.php
   - includes/class-jpc-frontend.php
   - includes/class-jpc-admin.php

## If Files Are Missing

If any files are missing, download the complete plugin from GitHub and re-upload.

## Emergency Deactivation

If you need to deactivate the plugin without accessing admin:

1. Connect via FTP or File Manager
2. Go to `/wp-content/plugins/`
3. Rename folder `jewellery-price-calculator` to `jewellery-price-calculator-disabled`
4. This will deactivate the plugin

---

**IMPORTANT:** Send me the exact error message from debug.log so I can fix the actual issue!
