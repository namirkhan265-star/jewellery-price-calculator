# Admin Menu Update for v1.9.0

## New Menu Items to Add

Add these 4 new submenu items in `includes/class-jpc-admin.php` after the Diamond Certifications menu item (around line 264):

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

## New Render Methods to Add

Add these 4 new render methods after `render_diamond_certifications()` (around line 410):

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

## Menu Structure After Update

```
Jewellery Price
├── General
├── Metal Groups
├── Metals
├── Diamond Groups
├── Diamond Types
├── Certifications
├── Shapes          ← NEW
├── Colours         ← NEW
├── Clarities       ← NEW
├── Cuts            ← NEW
├── Diamonds (Legacy)
├── Discount
├── Price History
├── Shortcodes
└── 🔧 Debug
```
