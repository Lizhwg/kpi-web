<?php

namespace App\Models;

use PDO;

/**
 * AssessmentItem Model - Quản lý các mục đánh giá chi tiết
 */
class AssessmentItem
{
    private $pdo;
    private $table = 'assessment_items';

    public function __construct()
    {
        $this->pdo = $this->getConnection();
    }

    private function getConnection()
    {
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $db = $_ENV['DB_NAME'] ?? 'kpi_system';
        $user = $_ENV['DB_USER'] ?? 'root';
        $pass = $_ENV['DB_PASS'] ?? '';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (\PDOException $e) {
            die("Kết nối cơ sở dữ liệu thất bại: " . $e->getMessage());
        }
    }

    public function getAll()
    {
        $query = "SELECT * FROM {$this->table} ORDER BY order_index";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByCriteriaGroup($group_id)
    {
        $query = "SELECT * FROM {$this->table} WHERE criteria_group_id = ? ORDER BY order_index";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$group_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $query = "INSERT INTO {$this->table} (criteria_group_id, item_name, description, weight, max_score, order_index) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($query);

        try {
            $stmt->execute([
                $data['criteria_group_id'],
                $data['item_name'],
                $data['description'] ?? '',
                $data['weight'] ?? 1.0,
                $data['max_score'] ?? 10.0,
                $data['order_index'] ?? 0
            ]);
            return ['status' => 'success', 'id' => $this->pdo->lastInsertId()];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
