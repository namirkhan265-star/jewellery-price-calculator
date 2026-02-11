<?php
/**
 * TEMPLATE CHECKER - Diagnose why GST percentage is not showing
 * 
 * Upload to WordPress root and visit: yoursite.com/template-checker.php
 * DELETE after use!
 */

require_once('wp-load.php');

$plugin_template = WP_PLUGIN_DIR . '/jewellery-price-calculator/templates/frontend/price-breakup.php';
$theme_override = get_stylesheet_directory() . '/jewellery-price-calc/price-breakup.php';

echo "<h1>Template Checker</h1>";

// Check plugin template
if (file_exists($plugin_template)) {
    $content = file_get_contents($plugin_template);
    $has_code = strpos($content, 'if ($gst_percentage > 0)') !== false;
    
    echo "<h2>Plugin Template: " . ($has_code ? "✅ HAS CODE" : "❌ MISSING CODE") . "</h2>";
    echo "<p>Path: $plugin_template</p>";
} else {
    echo "<h2>❌ Plugin template NOT FOUND!</h2>";
}

// Check theme override
if (file_exists($theme_override)) {
    echo "<h2>⚠️ THEME OVERRIDE FOUND!</h2>";
    echo "<p>Your theme is overriding the plugin template!</p>";
    echo "<p>Path: $theme_override</p>";
    echo "<p><strong>DELETE THIS FILE to use plugin template!</strong></p>";
} else {
    echo "<h2>✅ No theme override</h2>";
}

echo "<hr><p><strong>DELETE this file after checking!</strong></p>";
