<?php

namespace App\Models;

use PDO;

class EvaluationMapping
{
    private $pdo;

    public function __construct()
    {
        // Khởi tạo kết nối PDO (Huông thay thông số cho khớp với máy em nhé)
        $host = 'localhost';
        $db   = 'kpi_system';
        $user = 'root';
        $pass = '';
        $this->pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    }

    public function getById($id)
    {
        // SỬA: Đổi từ evaluation_mappings thành evaluation_headers
        $sql = "SELECT * FROM evaluation_headers WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Các hàm khác như getMappingsByEvaluator...
}