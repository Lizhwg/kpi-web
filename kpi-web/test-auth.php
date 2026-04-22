<?php
/**
 * Direct test - call AuthController login method
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load .env
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    if (strpos($class, $prefix) === 0) {
        $relativePath = substr($class, strlen($prefix));
        $file = __DIR__ . '/app/' . str_replace('\\', '/', $relativePath) . '.php';
        
        if (file_exists($file)) {
            require_once $file;
            return true;
        } else {
            echo "<!-- File not found: $file -->\n";
        }
    }
    return false;
});

// Start session
session_name($_ENV['SESSION_NAME'] ?? 'kpi_session');
session_start();

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Test Auth</title></head><body>";
echo "<pre>";

try {
    echo "1. Testing autoloader...\n";
    $testClass = 'App\\Controllers\\AuthController';
    echo "   Checking if $testClass exists...\n";
    
    if (!class_exists($testClass)) {
        echo "   ✗ Class not found, trying to load...\n";
        require_once __DIR__ . '/app/Controllers/AuthController.php';
    }
    
    if (class_exists($testClass)) {
        echo "   ✓ Class found\n";
        
        echo "\n2. Creating instance...\n";
        $controller = new \App\Controllers\AuthController();
        echo "   ✓ Instance created\n";
        
        echo "\n3. Checking method...\n";
        if (method_exists($controller, 'login')) {
            echo "   ✓ login() method exists\n";
        } else {
            echo "   ✗ login() method not found\n";
        }
    } else {
        echo "   ✗ Class still not found\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

echo "</pre></body></html>";
?>
