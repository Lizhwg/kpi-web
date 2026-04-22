<?php
/**
 * Debug POST login request
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
        }
    }
});

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>POST Request Debug</title>
    <style>
        body { font-family: monospace; background: #1e1e1e; color: #0f0; padding: 20px; }
        pre { background: #333; padding: 10px; border-radius: 5px; overflow-x: auto; }
        .error { color: #f00; }
        .success { color: #0f0; }
    </style>
</head>
<body>

<h1>🔍 POST Request Debug</h1>

<?php
$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base_path = '/KPI/KPI/kpi-web';

if (strpos($request_uri, $base_path) === 0) {
    $request_uri = substr($request_uri, strlen($base_path));
}

echo "<h2>Request Info:</h2>";
echo "<pre>";
echo "METHOD: $request_method\n";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Parsed URI: $request_uri\n";
echo "Base path: $base_path\n";
echo "</pre>";

// Load routes
$routes = require __DIR__ . '/routes/web.php';

echo "<h2>Route Check:</h2>";
if (isset($routes[$request_method][$request_uri])) {
    echo "<p class='success'>✓ Route matched: " . $routes[$request_method][$request_uri] . "</p>";
} else {
    echo "<p class='error'>✗ Route NOT matched</p>";
    echo "<pre>";
    echo "Available $request_method routes:\n";
    if (isset($routes[$request_method])) {
        print_r(array_keys($routes[$request_method]));
    }
    echo "</pre>";
}

echo "<h2>Form Test (POST to /login):</h2>";
?>

<form method="POST" action="/KPI/KPI/kpi-web/login" style="background: #333; padding: 20px; border-radius: 5px;">
    <div style="margin-bottom: 10px;">
        <label>Username:</label><br>
        <input type="text" name="username" value="secretary_hr" style="width: 200px; padding: 5px;">
    </div>
    <div style="margin-bottom: 10px;">
        <label>Password:</label><br>
        <input type="password" name="password" value="password123" style="width: 200px; padding: 5px;">
    </div>
    <button type="submit" style="padding: 10px 20px; background: #0f0; color: #000; border: none; border-radius: 3px; cursor: pointer;">Test Login</button>
</form>

<hr style="border: 1px solid #0f0; margin-top: 30px;">

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST') : ?>
<h2>POST Data Received:</h2>
<pre>
<?php print_r($_POST); ?>
</pre>

<h2>Attempting to call handleLogin:</h2>
<?php
try {
    $auth = new \App\Controllers\AuthController();
    echo "<p class='success'>✓ AuthController instantiated</p>";
    
    if (method_exists($auth, 'handleLogin')) {
        echo "<p class='success'>✓ handleLogin method exists</p>";
        // Uncomment to actually test login:
        // $auth->handleLogin();
    } else {
        echo "<p class='error'>✗ handleLogin method NOT found</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
<?php endif; ?>

</body>
</html>
