<?php
/**
 * PATCH FILE FOR class-jpc-admin.php
 * 
 * This file shows the EXACT code that needs to be added to includes/class-jpc-admin.php
 * 
 * STEP 1: Add these 4 menu items after line 264 (after Diamond Certifications menu)
 * STEP 2: Add the 4 render methods after line 410 (after render_diamond_certifications method)
 */

// ============================================================================
// STEP 1: ADD THESE 4 MENU ITEMS IN add_admin_menu() METHOD
// Location: After the Diamond Certifications submenu (around line 264)
// Insert BEFORE the "Diamonds (Legacy)" menu item
// ============================================================================

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

// ============================================================================
// STEP 2: ADD THESE 4 RENDER METHODS
// Location: After render_diamond_certifications() method (around line 410)
// Insert BEFORE the render_diamonds() method
// ============================================================================

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

// ============================================================================
// VISUAL GUIDE - WHERE TO INSERT THE CODE
// ============================================================================

/*

BEFORE (Current structure around line 264):

        // NEW: Diamond Certifications submenu
        add_submenu_page(
            'jewellery-price-calc',
            __('Diamond Certifications', 'jewellery-price-calc'),
            __('Certifications', 'jewellery-price-calc'),
            'manage_woocommerce',
            'jpc-diamond-certifications',
            array($this, 'render_diamond_certifications')
        );
        
        add_submenu_page(                              <-- INSERT NEW MENUS HERE (BEFORE THIS LINE)
            'jewellery-price-calc',
            __('Diamonds (Legacy)', 'jewellery-price-calc'),
            __('Diamonds (Legacy)', 'jewellery-price-calc'),
            'manage_woocommerce',
            'jpc-diamonds',
            array($this, 'render_diamonds')
        );

AFTER (With new menus added):

        // NEW: Diamond Certifications submenu
        add_submenu_page(
            'jewellery-price-calc',
            __('Diamond Certifications', 'jewellery-price-calc'),
            __('Certifications', 'jewellery-price-calc'),
            'manage_woocommerce',
            'jpc-diamond-certifications',
            array($this, 'render_diamond_certifications')
        );
        
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
        
        add_submenu_page(
            'jewellery-price-calc',
            __('Diamonds (Legacy)', 'jewellery-price-calc'),
            __('Diamonds (Legacy)', 'jewellery-price-calc'),
            'manage_woocommerce',
            'jpc-diamonds',
            array($this, 'render_diamonds')
        );

*/

/*

BEFORE (Current structure around line 410):

    /**
     * Render diamond certifications page
     */
    public function render_diamond_certifications() {
        include JPC_PLUGIN_DIR . 'templates/admin/diamond-certifications.php';
    }
    
    /**                                                <-- INSERT NEW RENDER METHODS HERE
     * Render diamonds page
     */
    public function render_diamonds() {
        include JPC_PLUGIN_DIR . 'templates/admin/diamonds.php';
    }

AFTER (With new render methods added):

    /**
     * Render diamond certifications page
     */
    public function render_diamond_certifications() {
        include JPC_PLUGIN_DIR . 'templates/admin/diamond-certifications.php';
    }
    
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
    
    /**
     * Render diamonds page
     */
    public function render_diamonds() {
        include JPC_PLUGIN_DIR . 'templates/admin/diamonds.php';
    }

*/
