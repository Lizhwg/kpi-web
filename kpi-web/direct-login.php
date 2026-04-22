<?php
/**
 * Direct login test - bypass routing
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

date_default_timezone_set('Asia/Ho_Chi_Minh');

// Start session
session_name($_ENV['SESSION_NAME'] ?? 'kpi_session');
session_start();

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

try {
    // Call AuthController directly
    $auth = new \App\Controllers\AuthController();
    $auth->login();
} catch (Exception $e) {
    echo "<h1>❌ ERROR</h1>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "\n" . $e->getTraceAsString() . "</pre>";
}
