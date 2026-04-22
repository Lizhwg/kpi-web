<?php

namespace App\Controllers;

use App\Models\User;

/**
 * AuthController - Xử lý xác thực và quản lý phiên
 * Module 1: Đăng nhập, Phân quyền
 */
class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Hiển thị trang đăng nhập
     */
    public function login()
    {
        // Đảm bảo session đã được bật trước khi gọi $_SESSION
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // ==========================================
        // [ĐÃ SỬA LỖI 1]: Điều hướng chuẩn theo Role nếu đã đăng nhập
        // ==========================================
        if (isset($_SESSION['user_id'])) {
            $role = (int)$_SESSION['user_role'];
            if ($role === 2) {
                // Thư ký -> về trang Mapping
                header('Location: /KPI/KPI/kpi-web/mapping/review');
            } else {
                // Các Role khác -> về Dashboard
                header('Location: /KPI/KPI/kpi-web/dashboard');
            }
            exit;
        }

        $error = null;
        include __DIR__ . '/../../resources/views/auth/login.php';
    }

    /**
     * Xử lý đăng nhập
     */
    public function handleLogin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Phương thức không hợp lệ";
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        // Validate input
        if (empty($username) || empty($password)) {
            $error = "Vui lòng nhập tên đăng nhập và mật khẩu";
            include __DIR__ . '/../../resources/views/auth/login.php';
            return;
        }

        // Xác thực người dùng thông qua Model
        $result = $this->userModel->authenticate($username, $password);

        if ($result['status'] !== 'success') {
            $error = $result['message'] ?? 'Đăng nhập thất bại';
            include __DIR__ . '/../../resources/views/auth/login.php';
            return;
        }

        // Regenerate session ID để bảo mật
        session_regenerate_id(true);

        // Lưu thông tin người dùng vào session
        $user = $result['user'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_role'] = (int)$user['role'];
        $_SESSION['business_unit_id'] = $user['business_unit_id'];

        // ==========================================
        // [ĐÃ SỬA LỖI 1]: Phân luồng ngay sau khi đăng nhập thành công
        // ==========================================
        $role = (int)$_SESSION['user_role'];

        switch ($role) {
            case 2: // Thư ký
                header('Location: /KPI/KPI/kpi-web/mapping/review');
                break;
            
            case 3: // PM
            case 4: // Trưởng phòng (TP cũng cần xem danh sách để duyệt)
                header('Location: /KPI/KPI/kpi-web/evaluation/request-list');
                break;
            
            case 1: // Admin
                header('Location: /KPI/KPI/kpi-web/admin/dashboard');
                break;

            default: // Nhân viên hoặc các Role khác
                header('Location: /KPI/KPI/kpi-web/dashboard');
                break;
        }
        exit;
    }

    /**
     * Hiển thị trang dashboard
     */
    public function dashboard()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Kiểm tra người dùng đã đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header('Location: /KPI/KPI/kpi-web/login');
            exit;
        }

        $user = $this->userModel->getById($_SESSION['user_id']);
        $roles = [
            1 => 'Admin',
            2 => 'Thư ký',
            3 => 'PM',
            4 => 'Trưởng phòng',
            5 => 'HRBP',
            6 => 'Nhân viên'
        ];

        include __DIR__ . '/../../resources/views/auth/dashboard.php';
    }

    /**
     * Hiển thị trang đăng ký
     */
    public function registerForm()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Nếu đã đăng nhập, chuyển hướng đến dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: /KPI/KPI/kpi-web/dashboard');
            exit;
        }

        $error = null;
        $success = null;
        include __DIR__ . '/../../resources/views/auth/register.php';
    }

    /**
     * Xử lý đăng ký tài khoản
     */
    public function handleRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Phương thức không hợp lệ";
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        // Validate input
        if (empty($username) || empty($password) || empty($confirmPassword)) {
            $error = "Vui lòng điền đầy đủ thông tin";
            include __DIR__ . '/../../resources/views/auth/register.php';
            return;
        }

        if (strlen($username) < 3) {
            $error = "Tên đăng nhập phải có ít nhất 3 ký tự";
            include __DIR__ . '/../../resources/views/auth/register.php';
            return;
        }

        if (strlen($password) < 6) {
            $error = "Mật khẩu phải có ít nhất 6 ký tự";
            include __DIR__ . '/../../resources/views/auth/register.php';
            return;
        }

        if ($password !== $confirmPassword) {
            $error = "Mật khẩu xác nhận không khớp";
            include __DIR__ . '/../../resources/views/auth/register.php';
            return;
        }

        // Kiểm tra tên đăng nhập đã tồn tại
        $existingUser = $this->userModel->getByUsername($username);
        if ($existingUser) {
            $error = "Tên đăng nhập đã tồn tại";
            include __DIR__ . '/../../resources/views/auth/register.php';
            return;
        }

        // Tạo tài khoản mới (mặc định là Nhân viên - role 6)
        $result = $this->userModel->create([
            'username' => $username,
            'password' => $password,
            'role' => 6,  // Nhân viên
            'business_unit_id' => null
        ]);

       if ($result['status'] !== 'success') {
            // THAY ĐỔI Ở ĐÂY: In trực tiếp câu lỗi của MySQL ra màn hình để biết đường sửa
            $error = $result['message']; 
            include __DIR__ . '/../../resources/views/auth/register.php';
            return;
        }

        // Hiển thị thông báo thành công
        $success = "Đăng ký thành công! Hãy đăng nhập để tiếp tục.";
        include __DIR__ . '/../../resources/views/auth/register.php';
    }

    /**
     * Đăng xuất
     */
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Xoá tất cả session variables
        $_SESSION = [];
        
        // Xoá session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        // Destroy session
        session_destroy();
        
        // Redirect to login (Đường dẫn này đã chuẩn xác)
        header('Location: /KPI/KPI/kpi-web/login');
        exit;
    }
}
