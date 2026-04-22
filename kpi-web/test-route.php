<?php
/**
 * Simple test for /login route
 */

// Simulate the request
$_SERVER['REQUEST_URI'] = '/KPI/KPI/kpi-web/login';
$_SERVER['REQUEST_METHOD'] = 'GET';

// Load .env
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

// Start session
session_name($_ENV['SESSION_NAME'] ?? 'kpi_session');
session_start();

// Test URL parsing
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base_path = '/KPI/KPI/kpi-web';

echo "=== URL PARSING TEST ===\n";
echo "Original REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Parsed request_uri: " . $request_uri . "\n";
echo "Base path: " . $base_path . "\n";

if (strpos($request_uri, $base_path) === 0) {
    $request_uri = substr($request_uri, strlen($base_path));
}

echo "Final request_uri: " . $request_uri . "\n";
echo "\n";

// Load routes
echo "=== LOADING ROUTES ===\n";
$routes = require __DIR__ . '/routes/web.php';

if (isset($routes['GET']['/login'])) {
    echo "✓ Route GET /login found\n";
    echo "  Handler: " . $routes['GET']['/login'] . "\n";
} else {
    echo "✗ Route GET /login NOT found\n";
    echo "  Available GET routes:\n";
    foreach ($routes['GET'] as $path => $handler) {
        echo "    - $path\n";
    }
}

echo "\n";

// Test AuthController loading
echo "=== CONTROLLER TEST ===\n";

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    if (strpos($class, $prefix) === 0) {
        $relativePath = substr($class, strlen($prefix));
        $file = __DIR__ . '/app/' . str_replace('\\', '/', $relativePath) . '.php';
        
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    return false;
});

$controller = 'AuthController';
$controllerClass = "App\\Controllers\\$controller";

echo "Looking for: $controllerClass\n";
echo "File: " . __DIR__ . '/app/Controllers/AuthController.php' . "\n";
echo "File exists: " . (file_exists(__DIR__ . '/app/Controllers/AuthController.php') ? 'YES' : 'NO') . "\n";

if (class_exists($controllerClass)) {
    echo "✓ Class $controllerClass exists\n";
    
    if (method_exists($controllerClass, 'login')) {
        echo "✓ Method 'login' exists\n";
    } else {
        echo "✗ Method 'login' NOT found\n";
    }
} else {
    echo "✗ Class $controllerClass NOT found\n";
}

echo "\n=== TEST COMPLETE ===\n";

?>
