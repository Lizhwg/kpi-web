<?php
/**
 * 🔍 KPI System - Diagnostic Tool
 * Kiểm tra cấu hình hệ thống
 * Truy cập: http://localhost/KPI/KPI/kpi-web/diagnostic.php
 */

echo "<!DOCTYPE html>
<html lang='vi'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>KPI Diagnostic</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        .section {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            border-radius: 5px;
        }
        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
            margin-left: 10px;
        }
        .ok {
            background: #d4edda;
            color: #155724;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
        }
        .info {
            color: #555;
            font-size: 14px;
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #667eea;
            color: white;
        }
        code {
            background: #f4f4f4;
            padding: 2px 5px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
<div class='container'>
    <h1>🔍 KPI System - Diagnostic Tool</h1>";

// ============================================================================
// 1. Kiểm tra PHP Version
// ============================================================================
echo "<div class='section'>
    <strong>PHP Version</strong>
    <span class='status ok'>" . phpversion() . "</span>
    <div class='info'>Yêu cầu: PHP 7.4+</div>
</div>";

// ============================================================================
// 2. Kiểm tra Extensions
// ============================================================================
echo "<div class='section'>
    <strong>Required Extensions</strong>";

$extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring', 'curl'];
$extTable = "<table><tr><th>Extension</th><th>Status</th></tr>";

foreach ($extensions as $ext) {
    $status = extension_loaded($ext) ? 
        "<span class='status ok'>✓ Loaded</span>" : 
        "<span class='status error'>✗ Missing</span>";
    $extTable .= "<tr><td>$ext</td><td>$status</td></tr>";
}

echo $extTable . "</table></div>";

// ============================================================================
// 3. Kiểm tra File Permissions
// ============================================================================
echo "<div class='section'>
    <strong>File Permissions</strong>";

$files = [
    '.env' => __DIR__ . '/.env',
    '.htaccess' => __DIR__ . '/.htaccess',
    'index.php' => __DIR__ . '/index.php',
];

$fileTable = "<table><tr><th>File</th><th>Exists</th><th>Readable</th><th>Writable</th></tr>";

foreach ($files as $name => $path) {
    $exists = file_exists($path) ? "<span class='status ok'>✓</span>" : "<span class='status error'>✗</span>";
    $readable = is_readable($path) ? "<span class='status ok'>✓</span>" : "<span class='status error'>✗</span>";
    $writable = is_writable($path) ? "<span class='status ok'>✓</span>" : "<span class='status warning'>-</span>";
    
    $fileTable .= "<tr><td><code>$name</code></td><td>$exists</td><td>$readable</td><td>$writable</td></tr>";
}

echo $fileTable . "</table></div>";

// ============================================================================
// 4. Kiểm tra Environment Variables
// ============================================================================
echo "<div class='section'>
    <strong>Environment Configuration (.env)</strong>";

// Load .env
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
    
    $envTable = "<table><tr><th>Variable</th><th>Value</th><th>Status</th></tr>";
    
    // Kiểm tra các biến quan trọng
    $requiredEnvs = ['DB_HOST', 'DB_NAME', 'DB_USER'];
    
    foreach ($requiredEnvs as $key) {
        $value = $env[$key] ?? 'NOT SET';
        $status = isset($env[$key]) ? 
            "<span class='status ok'>✓</span>" : 
            "<span class='status error'>✗</span>";
        $envTable .= "<tr><td><code>$key</code></td><td>$value</td><td>$status</td></tr>";
    }
    
    echo $envTable . "</table>";
} else {
    echo "<span class='status error'>✗ .env file not found</span>";
}
echo "</div>";

// ============================================================================
// 5. Kiểm tra Database Connection
// ============================================================================
echo "<div class='section'>
    <strong>Database Connection</strong>";

if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
    
    $host = $env['DB_HOST'] ?? 'localhost';
    $db = $env['DB_NAME'] ?? 'kpi_db';
    $user = $env['DB_USER'] ?? 'root';
    $pass = $env['DB_PASS'] ?? '';
    
    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$db;charset=utf8mb4",
            $user,
            $pass
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<span class='status ok'>✓ Connected</span>";
        echo "<div class='info'>Host: <code>$host</code> | DB: <code>$db</code></div>";
        
        // Kiểm tra các bảng
        $query = "SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$db]);
        $tableCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        echo "<div class='info'>Tables in database: <strong>$tableCount</strong> (Expected: 9)</div>";
        
        // Liệt kê bảng
        $query = "SHOW TABLES";
        $stmt = $pdo->query($query);
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (!empty($tables)) {
            echo "<div class='info'>Tables found: " . implode(", ", $tables) . "</div>";
        }
        
    } catch (PDOException $e) {
        echo "<span class='status error'>✗ Connection Failed</span>";
        echo "<div class='info'><strong>Error:</strong> " . $e->getMessage() . "</div>";
        echo "<div class='info'>
            <strong>Troubleshooting:</strong><br>
            • Kiểm tra MySQL đang chạy<br>
            • Kiểm tra .env configuration<br>
            • Kiểm tra DB_HOST, DB_NAME, DB_USER, DB_PASS
        </div>";
    }
} else {
    echo "<span class='status error'>✗ Cannot load configuration</span>";
}

echo "</div>";

// ============================================================================
// 6. Kiểm tra Autoloader
// ============================================================================
echo "<div class='section'>
    <strong>Autoloader & Controllers</strong>";

$controllerPath = __DIR__ . '/app/Controllers/AuthController.php';
if (file_exists($controllerPath)) {
    echo "<span class='status ok'>✓ AuthController found</span>";
} else {
    echo "<span class='status error'>✗ AuthController not found</span>";
}

$routesPath = __DIR__ . '/routes/web.php';
if (file_exists($routesPath)) {
    echo "<span class='status ok'>✓ Routes configured</span>";
} else {
    echo "<span class='status error'>✗ Routes not found</span>";
}

echo "</div>";

// ============================================================================
// 7. Kiểm tra mod_rewrite
// ============================================================================
echo "<div class='section'>
    <strong>Apache mod_rewrite</strong>";

if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "<span class='status ok'>✓ mod_rewrite enabled</span>";
    } else {
        echo "<span class='status error'>✗ mod_rewrite not enabled</span>";
        echo "<div class='info'>
            Enable it in <code>httpd.conf</code>:<br>
            <code>LoadModule rewrite_module modules/mod_rewrite.so</code>
        </div>";
    }
} else {
    echo "<span class='status warning'>⚠ Cannot detect (function not available)</span>";
}

echo "</div>";

// ============================================================================
// 8. Kiểm tra .htaccess
// ============================================================================
echo "<div class='section'>
    <strong>.htaccess Configuration</strong>";

$htaccessPath = __DIR__ . '/.htaccess';
if (file_exists($htaccessPath)) {
    echo "<span class='status ok'>✓ File exists</span>";
    $content = file_get_contents($htaccessPath);
    echo "<div class='info' style='background: #f4f4f4; padding: 10px; border-radius: 3px; margin-top: 10px;'>";
    echo "<pre style='margin: 0; font-size: 12px;'>" . htmlspecialchars($content) . "</pre>";
    echo "</div>";
} else {
    echo "<span class='status error'>✗ File not found</span>";
}

echo "</div>";

// ============================================================================
// 9. Quick Links
// ============================================================================
echo "<div class='section' style='background: #e7f3ff; border-left-color: #0066cc;'>
    <strong>🚀 Quick Links</strong>
    <div class='info'>
        <a href='/' style='display: inline-block; margin: 5px 10px 5px 0; padding: 8px 15px; background: #667eea; color: white; text-decoration: none; border-radius: 3px;'>Go to Home</a>
        <a href='/KPI/KPI/kpi-web/login' style='display: inline-block; margin: 5px 10px 5px 0; padding: 8px 15px; background: #667eea; color: white; text-decoration: none; border-radius: 3px;'>Login Page</a>
        <a href='http://localhost/phpmyadmin' style='display: inline-block; margin: 5px 10px 5px 0; padding: 8px 15px; background: #667eea; color: white; text-decoration: none; border-radius: 3px;'>phpMyAdmin</a>
    </div>
</div>";

// ============================================================================
// 10. Summary
// ============================================================================
echo "<div class='section' style='background: #f0f0f0; margin-top: 30px;'>
    <strong>📋 Summary</strong>
    <div class='info'>
        <p>Nếu tất cả các mục trên đều có ✓ (xanh), hệ thống sẵn sàng sử dụng.</p>
        <p>Nếu có ✗ (đỏ), vui lòng kiểm tra SETUP_GUIDE.md để khắc phục.</p>
    </div>
</div>";

echo "</div>
</body>
</html>";
?>
