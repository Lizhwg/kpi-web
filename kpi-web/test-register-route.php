<?php
/**
 * Test /register route
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

// Check session
$isLoggedIn = isset($_SESSION['user_id']);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register Route Test</title>
    <style>
        body { font-family: monospace; background: #1e1e1e; color: #0f0; padding: 20px; }
        .box { background: #333; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .success { color: #0f0; }
        .error { color: #f00; }
    </style>
</head>
<body>

<h1>🔍 Register Route Debug</h1>

<div class="box">
    <h2>1. Session Status:</h2>
    <p>Is logged in? <span class="<?php echo $isLoggedIn ? 'error' : 'success'; ?>">
        <?php echo $isLoggedIn ? '❌ YES (Logged in) - Will be redirected to dashboard' : '✓ NO (Not logged in) - Can see register form'; ?>
    </span></p>
</div>

<div class="box">
    <h2>2. Direct Access Test:</h2>
    <p><a href="/KPI/KPI/kpi-web/register" style="color: #0f0; text-decoration: underline;">
        → Click here to test /register
    </a></p>
</div>

<div class="box">
    <h2>3. Expected Result:</h2>
    <p>If you are NOT logged in:</p>
    <ul>
        <li>✓ Should see register form</li>
        <li>✓ Form with 3 fields: username, password, confirm_password</li>
    </ul>
    <p>If you ARE logged in:</p>
    <ul>
        <li>❌ Will redirect to /dashboard</li>
        <li>❌ Won't see register form</li>
    </ul>
</div>

<div class="box">
    <h2>4. Solution:</h2>
    <p>If logged in, please <a href="/KPI/KPI/kpi-web/logout" style="color: #0f0;">logout first</a></p>
    <p>Then try <a href="/KPI/KPI/kpi-web/register" style="color: #0f0;">register again</a></p>
</div>

<div class="box">
    <h2>5. Routes Check:</h2>
    <?php
    $routes = require __DIR__ . '/routes/web.php';
    
    if (isset($routes['GET']['/register'])) {
        echo "<p class='success'>✓ GET /register route exists: " . $routes['GET']['/register'] . "</p>";
    } else {
        echo "<p class='error'>✗ GET /register route NOT found</p>";
    }
    
    if (isset($routes['POST']['/register'])) {
        echo "<p class='success'>✓ POST /register route exists: " . $routes['POST']['/register'] . "</p>";
    } else {
        echo "<p class='error'>✗ POST /register route NOT found</p>";
    }
    ?>
</div>

</body>
</html>
