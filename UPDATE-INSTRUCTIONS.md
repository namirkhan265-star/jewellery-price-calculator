# v1.9.0 Update Instructions

## Current Status

✅ **Completed:**
- 4 new database tables created
- 4 new management classes created  
- 4 new admin template files created
- Main plugin file updated to v1.9.0
- Database class updated with new tables
- Default data ready to be inserted

❌ **Missing:**
- Admin menu items for the 4 new pages
- Render methods in admin class

## Quick Fix (2 Minutes)

### Option 1: Manual Edit

Open `includes/class-jpc-admin.php` in your code editor and make these 2 changes:

**Change 1:** Find this code (around line 264):

```php
        // NEW: Diamond Certifications submenu
        add_submenu_page(
            'jewellery-price-calc',
            __('Diamond Certifications', 'jewellery-price-calc'),
            __('Certifications', 'jewellery-price-calc'),
            'manage_woocommerce',
            'jpc-diamond-certifications',
            array($this, 'render_diamond_certifications')
        );
        
        add_submenu_page(
            'jewellery-price-calc',
            __('Diamonds (Legacy)', 'jewellery-price-calc'),
```

**Add these 4 blocks BETWEEN the two sections above:**

```php
        
        // NEW: Diamond Shapes submenu
        add_submenu_page(
            'jewellery-price-calc',
            __('Diamond Shapes', 'jewellery-price-calc'),
            __('Shapes', 'jewellery-price-calc'),
            'manage_woocommerce',
            'jpc-diamond-shapes',
            array($this, 'render_diamond_shapes')
        );
        
        // NEW: Diamond Colours submenu
        add_submenu_page(
            'jewellery-price-calc',
            __('Diamond Colours', 'jewellery-price-calc'),
            __('Colours', 'jewellery-price-calc'),
            'manage_woocommerce',
            'jpc-diamond-colours',
            array($this, 'render_diamond_colours')
        );
        
        // NEW: Diamond Clarities submenu
        add_submenu_page(
            'jewellery-price-calc',
            __('Diamond Clarities', 'jewellery-price-calc'),
            __('Clarities', 'jewellery-price-calc'),
            'manage_woocommerce',
            'jpc-diamond-clarities',
            array($this, 'render_diamond_clarities')
        );
        
        // NEW: Diamond Cuts submenu
        add_submenu_page(
            'jewellery-price-calc',
            __('Diamond Cuts', 'jewellery-price-calc'),
            __('Cuts', 'jewellery-price-calc'),
            'manage_woocommerce',
            'jpc-diamond-cuts',
            array($this, 'render_diamond_cuts')
        );
```

**Change 2:** Find this code (around line 410):

```php
    /**
     * Render diamond certifications page
     */
    public function render_diamond_certifications() {
        include JPC_PLUGIN_DIR . 'templates/admin/diamond-certifications.php';
    }
    
    /**
     * Render diamonds page
     */
    public function render_diamonds() {
```

**Add these 4 methods BETWEEN the two sections above:**

```php
    
    /**
     * Render diamond shapes page
     */
    public function render_diamond_shapes() {
        include JPC_PLUGIN_DIR . 'templates/admin/diamond-shapes.php';
    }
    
    /**
     * Render diamond colours page
     */
    public function render_diamond_colours() {
        include JPC_PLUGIN_DIR . 'templates/admin/diamond-colours.php';
    }
    
    /**
     * Render diamond clarities page
     */
    public function render_diamond_clarities() {
        include JPC_PLUGIN_DIR . 'templates/admin/diamond-clarities.php';
    }
    
    /**
     * Render diamond cuts page
     */
    public function render_diamond_cuts() {
        include JPC_PLUGIN_DIR . 'templates/admin/diamond-cuts.php';
    }
```

Save the file.

### Option 2: Copy-Paste Ready Code

See `PATCH-admin-class.php` for the exact code blocks to copy.

## After Making Changes

1. **Upload the updated file** to your server
2. **Deactivate the plugin** in WordPress admin
3. **Reactivate the plugin** to trigger table creation
4. **Check the menu** - You should now see:
   - Jewellery Price
     - General
     - Metal Groups
     - Metals
     - Diamond Groups
     - Diamond Types
     - Certifications
     - **Shapes** ← NEW
     - **Colours** ← NEW
     - **Clarities** ← NEW
     - **Cuts** ← NEW
     - Diamonds (Legacy)
     - Discount
     - Price History
     - Shortcodes
     - 🔧 Debug

5. **Visit each new page** to verify:
   - Shapes page shows 8 default shapes
   - Colours page shows 8 default colour grades
   - Clarities page shows 9 default clarity grades
   - Cuts page shows 5 default cut grades

## Verification

Run this SQL query to verify tables were created:

```sql
SELECT 
    'Shapes' as Type, COUNT(*) as Count FROM wp_jpc_diamond_shapes
UNION ALL
SELECT 'Colours', COUNT(*) FROM wp_jpc_diamond_colours
UNION ALL
SELECT 'Clarities', COUNT(*) FROM wp_jpc_diamond_clarities
UNION ALL
SELECT 'Cuts', COUNT(*) FROM wp_jpc_diamond_cuts;
```

Expected results:
- Shapes: 8
- Colours: 8
- Clarities: 9
- Cuts: 5

## Troubleshooting

**Problem:** Menu items don't appear
- **Solution:** Clear WordPress cache, check file was uploaded correctly

**Problem:** Pages show errors
- **Solution:** Verify all 4 template files exist in `templates/admin/`

**Problem:** No default data
- **Solution:** Go to Debug page, check database tables exist

**Problem:** Database tables not created
- **Solution:** Deactivate and reactivate plugin

## Need Help?

Check these files:
- `CHANGELOG-v1.9.0.md` - Complete feature documentation
- `PATCH-admin-class.php` - Exact code to add
- `TODO-v1.9.0-COMPLETION.md` - Detailed checklist

## Summary

You only need to edit **ONE file**: `includes/class-jpc-admin.php`

Add **8 code blocks total**:
- 4 menu items (in `add_admin_menu()` method)
- 4 render methods (after `render_diamond_certifications()`)

That's it! The rest is already done.
