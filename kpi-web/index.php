<?php

/**
 * KPI Web - Tệp khởi chạy đầu tiên
 * Điểm vào chính của ứng dụng web
 */

// ============================================================================
// 0. OUTPUT BUFFERING - FIX LỖI REDIRECT
// ============================================================================
ob_start();

// ============================================================================
// 1. CẤU HÌNH CHUNG
// ============================================================================
date_default_timezone_set('Asia/Ho_Chi_Minh');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ============================================================================
// 2. LOAD CÁC CẤU HÌNH
// ============================================================================
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

// ============================================================================
// 3. AUTOLOADER - TỰ ĐỘNG LOAD CÁC CLASS
// ============================================================================
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

// ============================================================================
// 4. SESSION & SECURITY
// ============================================================================
header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

session_name($_ENV['SESSION_NAME'] ?? 'kpi_session');
session_start();

// Định nghĩa base_path một lần để dùng chung cho cả file
$base_path = '/KPI/KPI/kpi-web'; 

if (isset($_SESSION['last_activity'])) {
    $timeout = (int)($_ENV['SESSION_TIMEOUT'] ?? 3600);
    if (time() - $_SESSION['last_activity'] > $timeout) {
        session_destroy();
        // [ĐÃ SỬA]: Đồng bộ đường dẫn login
        header("Location: $base_path/login");
        exit;
    }
}
$_SESSION['last_activity'] = time();

// ============================================================================
// 5. ROUTING - XỬ LÝ ĐỐI TƯỢNG YÊU CẦU
// ============================================================================
$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Cắt bỏ base_path để so khớp với mảng routes
if (strpos($request_uri, $base_path) === 0) {
    $request_uri = substr($request_uri, strlen($base_path));
}

if (empty($request_uri) || $request_uri === '/') {
    $request_uri = '/';
}

$routes = require __DIR__ . '/routes/web.php';

// ============================================================================
// 6. KIỂM TRA XÁC THỰC VÀ PHÂN QUYỀN
// ============================================================================
$publicRoutes = ['/login', '/register', '/logout'];
$isPublicRoute = in_array($request_uri, $publicRoutes);
$isLoginPage = $request_uri === '/login';

// [ĐÃ SỬA]: Nếu chưa đăng nhập -> Đá về Login (Dùng $base_path)
if (!isset($_SESSION['user_id']) && !$isPublicRoute) {
    header("Location: $base_path/login");
    exit;
}

// [ĐÃ SỬA]: Nếu đã đăng nhập mà vẫn vào /login -> Điều hướng (Dùng $base_path)
if (isset($_SESSION['user_id']) && $isLoginPage) {
    $role = (int)($_SESSION['user_role'] ?? 0);
    
    if ($role === 2) { // Role Thư ký
        header("Location: $base_path/mapping/review");
    } else {
        header("Location: $base_path/dashboard");
    }
    exit;
}
// ============================================================================
// 7. THỰC HIỆN HANDLER
// ============================================================================
$found = false;
$handler = null;

if (isset($routes[$request_method][$request_uri])) {
    $handler = $routes[$request_method][$request_uri];
    $found = true;
}

if ($found && $handler) {
    if (strpos($handler, '@') !== false) {
        list($controller, $method) = explode('@', $handler);
        $controllerClass = "App\\Controllers\\$controller";

        if (class_exists($controllerClass)) {
            $controllerInstance = new $controllerClass();
            if (method_exists($controllerInstance, $method)) {
                call_user_func([$controllerInstance, $method]);
            } else {
                http_response_code(404);
                echo "❌ Method không tìm thấy: $method trong $controllerClass";
            }
        } else {
            http_response_code(404);
            echo "❌ Controller không tìm thấy: $controllerClass<br>";
        }
    }
} else {
    http_response_code(404);
    echo "404 - Trang không tìm thấy<br>";
    echo "Request: $request_method $request_uri<br>";
}
