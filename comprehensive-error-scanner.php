<?php
/**
 * COMPREHENSIVE ERROR SCANNER
 * Scans ALL plugin files for syntax errors, missing files, and issues
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 COMPREHENSIVE PLUGIN ERROR SCANNER</h1>";
echo "<p>Scanning all files in jewellery-price-calculator-main plugin...</p>";
echo "<hr>";

$plugin_dir = __DIR__;
$errors_found = [];
$files_scanned = 0;
$syntax_errors = [];
$missing_includes = [];

/**
 * Recursively scan directory for PHP files
 */
function scan_directory($dir) {
    $files = [];
    if (!is_dir($dir)) {
        return $files;
    }
    
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            $files = array_merge($files, scan_directory($path));
        } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            $files[] = $path;
        }
    }
    return $files;
}

/**
 * Check PHP syntax
 */
function check_syntax($file) {
    $output = [];
    $return_var = 0;
    exec("php -l " . escapeshellarg($file) . " 2>&1", $output, $return_var);
    
    if ($return_var !== 0) {
        return implode("\n", $output);
    }
    return null;
}

/**
 * Check for common issues in code
 */
function check_code_issues($file) {
    $issues = [];
    $content = file_get_contents($file);
    
    // Check for HTML comments inside PHP blocks
    if (preg_match('/\<\?php.*?<!--.*?-->/s', $content)) {
        $issues[] = "HTML comment inside PHP block";
    }
    
    // Check for duplicate function definitions
    preg_match_all('/function\s+([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\s*\(/i', $content, $matches);
    if (!empty($matches[1])) {
        $functions = array_count_values($matches[1]);
        foreach ($functions as $func => $count) {
            if ($count > 1) {
                $issues[] = "Duplicate function definition: $func (defined $count times)";
            }
        }
    }
    
    // Check for unclosed PHP tags
    $php_open = substr_count($content, '<?php');
    $php_close = substr_count($content, '?>');
    if ($php_open > $php_close + 1) {
        $issues[] = "Possible unclosed PHP tag";
    }
    
    // Check for duplicate code blocks (same line repeated)
    $lines = explode("\n", $content);
    $line_counts = array_count_values($lines);
    foreach ($line_counts as $line => $count) {
        if ($count > 3 && strlen(trim($line)) > 50) {
            $issues[] = "Duplicate code line (repeated $count times): " . substr(trim($line), 0, 60) . "...";
        }
    }
    
    return $issues;
}

// Scan all PHP files
$all_files = scan_directory($plugin_dir);
$files_scanned = count($all_files);

echo "<h2>📁 Files Found: $files_scanned</h2>";
echo "<hr>";

// Check each file
foreach ($all_files as $file) {
    $relative_path = str_replace($plugin_dir . '/', '', $file);
    
    // Check syntax
    $syntax_error = check_syntax($file);
    if ($syntax_error) {
        $syntax_errors[$relative_path] = $syntax_error;
        echo "<div style='background: #ffebee; padding: 10px; margin: 10px 0; border-left: 4px solid #f44336;'>";
        echo "<strong style='color: #c62828;'>❌ SYNTAX ERROR: $relative_path</strong><br>";
        echo "<pre style='color: #d32f2f; margin: 5px 0;'>$syntax_error</pre>";
        echo "</div>";
    }
    
    // Check for code issues
    $code_issues = check_code_issues($file);
    if (!empty($code_issues)) {
        echo "<div style='background: #fff3e0; padding: 10px; margin: 10px 0; border-left: 4px solid #ff9800;'>";
        echo "<strong style='color: #e65100;'>⚠️ CODE ISSUES: $relative_path</strong><br>";
        foreach ($code_issues as $issue) {
            echo "<div style='color: #ef6c00; margin: 5px 0;'>• $issue</div>";
        }
        echo "</div>";
    }
}

echo "<hr>";
echo "<h2>📊 SCAN SUMMARY</h2>";
echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 5px;'>";
echo "<p><strong>Total Files Scanned:</strong> $files_scanned</p>";
echo "<p><strong>Syntax Errors Found:</strong> " . count($syntax_errors) . "</p>";
echo "</div>";

if (empty($syntax_errors)) {
    echo "<div style='background: #e8f5e9; padding: 15px; margin: 20px 0; border-left: 4px solid #4caf50;'>";
    echo "<h3 style='color: #2e7d32; margin: 0;'>✅ No syntax errors found!</h3>";
    echo "<p style='color: #388e3c;'>All PHP files have valid syntax.</p>";
    echo "</div>";
} else {
    echo "<div style='background: #ffebee; padding: 15px; margin: 20px 0; border-left: 4px solid #f44336;'>";
    echo "<h3 style='color: #c62828; margin: 0;'>❌ CRITICAL ERRORS FOUND</h3>";
    echo "<p style='color: #d32f2f;'>Fix these files immediately:</p>";
    echo "<ul>";
    foreach (array_keys($syntax_errors) as $file) {
        echo "<li style='color: #d32f2f;'><strong>$file</strong></li>";
    }
    echo "</ul>";
    echo "</div>";
}

// Check WordPress error log if available
$wp_debug_log = dirname(dirname(dirname(__DIR__))) . '/wp-content/debug.log';
if (file_exists($wp_debug_log)) {
    echo "<hr>";
    echo "<h2>📋 RECENT WORDPRESS ERRORS (Last 50 lines)</h2>";
    $log_lines = file($wp_debug_log);
    $recent_lines = array_slice($log_lines, -50);
    echo "<div style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd; max-height: 400px; overflow-y: scroll;'>";
    echo "<pre style='margin: 0; font-size: 12px;'>";
    foreach ($recent_lines as $line) {
        if (stripos($line, 'jewellery') !== false || stripos($line, 'jpc') !== false) {
            echo "<span style='background: #ffeb3b;'>" . htmlspecialchars($line) . "</span>";
        } else {
            echo htmlspecialchars($line);
        }
    }
    echo "</pre>";
    echo "</div>";
}

echo "<hr>";
echo "<h2>🔧 NEXT STEPS</h2>";
echo "<ol>";
echo "<li>Fix all syntax errors listed above</li>";
echo "<li>Check WordPress debug.log for runtime errors</li>";
echo "<li>Enable WP_DEBUG in wp-config.php if not already enabled</li>";
echo "<li>Clear all caches (WordPress, browser, server)</li>";
echo "<li>Deactivate and reactivate the plugin</li>";
echo "</ol>";

echo "<hr>";
echo "<p><em>Scan completed at " . date('Y-m-d H:i:s') . "</em></p>";
?>
