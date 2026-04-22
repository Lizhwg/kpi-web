<?php

namespace App\Models;

use PDO;

/**
 * User Model - Quản lý thông tin người dùng
 * Kết nối với bảng User (schema mới)
 */
class User
{
    private $pdo;
    private $table = 'User';

    public function __construct()
    {
        $this->pdo = $this->getConnection();
    }

    /**
     * Kết nối cơ sở dữ liệu
     */
    private function getConnection()
    {
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $db = $_ENV['DB_NAME'] ?? 'kpi_system';
        $user = $_ENV['DB_USER'] ?? 'root';
        $pass = $_ENV['DB_PASS'] ?? '';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (\PDOException $e) {
            die("Kết nối cơ sở dữ liệu thất bại: " . $e->getMessage());
        }
    }

    /**
     * Lấy tất cả người dùng (không bị xóa)
     */
    public function getAll()
    {
        $query = "SELECT * FROM {$this->table} WHERE is_deleted = 0";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy người dùng theo ID
     */
    public function getById($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id = ? AND is_deleted = 0";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy người dùng theo username
     */
    public function getByUsername($username)
    {
        $query = "SELECT * FROM {$this->table} WHERE username = ? AND is_deleted = 0";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Tạo người dùng mới
     */
    /**
     * Tạo tài khoản người dùng mới (Register)
     */
    /**
     * Thêm tài khoản mới vào Database
     */
    public function create($data)
    {
        try {
            // 1. MÃ HÓA MẬT KHẨU (Bắt buộc để bảo mật)
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            // 2. Viết câu lệnh SQL (Chú ý tên cột phải khớp 100% với bảng trong MySQL)
            $query = "INSERT INTO user (username, password, role, business_unit_id, is_deleted) 
                      VALUES (:username, :password, :role, :business_unit_id, 0)";
            
            $stmt = $this->pdo->prepare($query);
            
            // 3. Thực thi gán dữ liệu
            $stmt->execute([
                ':username' => $data['username'],
                ':password' => $hashedPassword,
                ':role' => $data['role'],
                ':business_unit_id' => $data['business_unit_id']
            ]);

            return [
                'status' => 'success', 
                'message' => 'Đăng ký thành công'
            ];

        } catch (\PDOException $e) {
            // Nếu MySQL từ chối (thiếu cột, trùng tên...), nó sẽ trả lỗi chi tiết về đây
            return [
                'status' => 'error', 
                'message' => 'Lỗi Database: ' . $e->getMessage() 
            ];
        }
    }
    /**
     * Cập nhật người dùng
     */
    public function update($id, $data)
    {
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            // Không cho phép cập nhật trực tiếp mật khẩu ở đây
            if ($key !== 'password') {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        $values[] = $id;

        if (empty($fields)) {
            return ['status' => 'error', 'message' => 'Không có dữ liệu để cập nhật'];
        }

        $query = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->pdo->prepare($query);

        try {
            $stmt->execute($values);
            return ['status' => 'success'];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Xóa mềm người dùng
     */
    public function delete($id)
    {
        $query = "UPDATE {$this->table} SET is_deleted = 1 WHERE id = ?";
        $stmt = $this->pdo->prepare($query);

        try {
            $stmt->execute([$id]);
            return ['status' => 'success'];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Xác thực đăng nhập
     */
    public function authenticate($username, $password)
    {
        $user = $this->getByUsername($username);

        if (!$user) {
            return ['status' => 'error', 'message' => 'Tên đăng nhập không tồn tại'];
        }

        if (!password_verify($password, $user['password'])) {
            return ['status' => 'error', 'message' => 'Mật khẩu không chính xác'];
        }

        unset($user['password']);
        return ['status' => 'success', 'user' => $user];
    }

    /**
     * Lấy người dùng theo phòng ban
     */
    public function getByBusinessUnit($business_unit_id)
    {
        $query = "SELECT * FROM {$this->table} WHERE business_unit_id = ? AND is_deleted = 0";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$business_unit_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy người dùng theo vai trò
     */
    public function getByRole($role)
    {
        $query = "SELECT * FROM {$this->table} WHERE role = ? AND is_deleted = 0";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$role]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Thay đổi mật khẩu
     */
    public function changePassword($id, $oldPassword, $newPassword)
    {
        $user = $this->getById($id);
        
        if (!$user) {
            return ['status' => 'error', 'message' => 'Người dùng không tồn tại'];
        }

        if (!password_verify($oldPassword, $user['password'])) {
            return ['status' => 'error', 'message' => 'Mật khẩu cũ không chính xác'];
        }

        $query = "UPDATE {$this->table} SET password = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($query);

        try {
            $stmt->execute([password_hash($newPassword, PASSWORD_BCRYPT), $id]);
            return ['status' => 'success', 'message' => 'Đổi mật khẩu thành công'];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
