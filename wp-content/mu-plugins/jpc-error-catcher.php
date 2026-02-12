<?php
/**
 * JPC Error Catcher
 * Place in wp-content/mu-plugins/ to catch fatal errors
 */

// Enable error display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log all errors
add_action('admin_init', function() {
    if (isset($_GET['page']) && strpos($_GET['page'], 'jpc-') !== false) {
        error_log('JPC Page Loading: ' . $_GET['page']);
    }
});

// Catch fatal errors
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        if (isset($_GET['page']) && strpos($_GET['page'], 'jpc-') !== false) {
            echo '<div style="background:#f8d7da;border:2px solid #dc3545;padding:20px;margin:20px;font-family:monospace;">';
            echo '<h2 style="color:#721c24;">JPC FATAL ERROR CAUGHT:</h2>';
            echo '<pre>';
            print_r($error);
            echo '</pre>';
            echo '</div>';
        }
    }
});
