<?php
/**
 * DEBUG - Kiểm tra session và request
 */

// Load .env
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

session_name($_ENV['SESSION_NAME'] ?? 'kpi_session');
session_start();

// Lấy URL
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base_path = '/KPI/KPI/kpi-web';
if (strpos($request_uri, $base_path) === 0) {
    $request_uri = substr($request_uri, strlen($base_path));
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Debug - KPI System</title>
    <style>
        body { font-family: monospace; background: #1e1e1e; color: #00ff00; padding: 20px; }
        .section { background: #2d2d2d; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .title { color: #ffff00; font-weight: bold; margin-bottom: 10px; }
        .ok { color: #00ff00; }
        .error { color: #ff0000; }
        .info { color: #00ccff; }
    </style>
</head>
<body>

<h1>🔍 KPI DEBUG INFORMATION</h1>

<div class="section">
    <div class="title">📍 URL Information</div>
    <div>Request URI: <span class="info"><?php echo htmlspecialchars($request_uri); ?></span></div>
    <div>Full URL: <span class="info"><?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?></span></div>
    <div>Base Path: <span class="info"><?php echo '/KPI/KPI/kpi-web'; ?></span></div>
</div>

<div class="section">
    <div class="title">👤 Session Information</div>
    <div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="ok">✓ User ID: <?php echo $_SESSION['user_id']; ?></div>
            <div class="ok">✓ Username: <?php echo htmlspecialchars($_SESSION['username']); ?></div>
            <div class="ok">✓ Role: <?php echo $_SESSION['user_role']; ?> (Type: <?php echo gettype($_SESSION['user_role']); ?>)</div>
            <div class="ok">✓ Business Unit: <?php echo $_SESSION['business_unit_id'] ?? 'NULL'; ?></div>
        <?php else: ?>
            <div class="error">✗ Not logged in</div>
        <?php endif; ?>
    </div>
</div>

<div class="section">
    <div class="title">🔗 Routes</div>
    <?php
    $routes = require __DIR__ . '/routes/web.php';
    if (isset($routes['GET'][$request_uri])) {
        $handler = $routes['GET'][$request_uri];
        echo "<div class='ok'>✓ Route found for GET $request_uri</div>";
        echo "<div class='info'>Handler: $handler</div>";
    } else {
        echo "<div class='error'>✗ No route for GET $request_uri</div>";
        echo "<div class='info'>Available GET routes:</div>";
        foreach ($routes['GET'] as $path => $handler) {
            echo "<div style='margin-left: 20px;'>- $path → $handler</div>";
        }
    }
    ?>
</div>

<div class="section">
    <div class="title">📂 File Check</div>
    <?php
    $controller_path = __DIR__ . '/app/Controllers/MappingController.php';
    $view_path = __DIR__ . '/resources/views/mapping/list.php';
    $detail_path = __DIR__ . '/resources/views/mapping/detail.php';
    
    echo "<div>";
    echo "MappingController: " . (file_exists($controller_path) ? "<span class='ok'>✓ EXISTS</span>" : "<span class='error'>✗ NOT FOUND</span>");
    echo " ($controller_path)</div>";
    
    echo "<div>";
    echo "list.php: " . (file_exists($view_path) ? "<span class='ok'>✓ EXISTS</span>" : "<span class='error'>✗ NOT FOUND</span>");
    echo " ($view_path)</div>";
    
    echo "<div>";
    echo "detail.php: " . (file_exists($detail_path) ? "<span class='ok'>✓ EXISTS</span>" : "<span class='error'>✗ NOT FOUND</span>");
    echo " ($detail_path)</div>";
    ?>
</div>

<div class="section">
    <div class="title">⚙️ Configuration</div>
    <div>
        <?php
        $env_path = __DIR__ . '/.env';
        if (file_exists($env_path)) {
            echo "<div class='ok'>✓ .env file exists</div>";
            $env = parse_ini_file($env_path);
            echo "<div>DB_NAME: <span class='info'>" . ($env['DB_NAME'] ?? 'NOT SET') . "</span></div>";
        } else {
            echo "<div class='error'>✗ .env file not found</div>";
        }
        ?>
    </div>
</div>

<div class="section">
    <div class="title">🔧 PHP Info</div>
    <div>PHP Version: <span class="info"><?php echo phpversion(); ?></span></div>
    <div>Session: <span class="info"><?php echo session_status() === PHP_SESSION_ACTIVE ? 'ACTIVE' : 'INACTIVE'; ?></span></div>
</div>

</body>
</html>
