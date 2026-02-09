<?php
/**
 * PATCH FOR: includes/class-jpc-admin.php
 * 
 * FIND THIS FUNCTION (around line 424):
 */

// OLD CODE:
public function render_metals() {
    include JPC_PLUGIN_DIR . 'templates/admin/metals.php';
}

// REPLACE WITH:
public function render_metals() {
    include JPC_PLUGIN_DIR . 'templates/admin/metals-v2.php';
}

/**
 * THAT'S IT! Just change metals.php to metals-v2.php
 * 
 * EXACT LOCATION:
 * File: includes/class-jpc-admin.php
 * Line: ~425 (approximately)
 * 
 * BEFORE:
 * include JPC_PLUGIN_DIR . 'templates/admin/metals.php';
 * 
 * AFTER:
 * include JPC_PLUGIN_DIR . 'templates/admin/metals-v2.php';
 */
