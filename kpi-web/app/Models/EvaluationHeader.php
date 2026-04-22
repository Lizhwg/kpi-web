<?php

namespace App\Models;

use PDO;

/**
 * EvaluationHeader Model - Lưu trữ đầu mục đánh giá
 */
class EvaluationHeader
{
    private $pdo;
    private $table = 'evaluation_headers';

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

    public function getById($id)
    {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $query = "INSERT INTO {$this->table} (mapping_id, evaluator_id, evaluatee_id, period_id, comments, status) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($query);

        try {
            $stmt->execute([
                $data['mapping_id'],
                $data['evaluator_id'],
                $data['evaluatee_id'],
                $data['period_id'],
                $data['comments'] ?? '',
                $data['status'] ?? 'DRAFT'
            ]);
            return ['status' => 'success', 'id' => $this->pdo->lastInsertId()];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function updateStatus($id, $status, $comment = '')
    {
        $query = "UPDATE {$this->table} SET status = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($query);

        try {
            $stmt->execute([$status, $id]);
            return ['status' => 'success'];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getEvaluationsByDepartment($department_id, $period_id)
    {
        $query = "SELECT eh.* FROM {$this->table} eh
                  JOIN users u ON eh.evaluatee_id = u.id
                  WHERE u.department_id = ? AND eh.period_id = ?
                  ORDER BY eh.created_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$department_id, $period_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEvaluationsByReviewer($reviewer_id)
    {
        $query = "SELECT eh.* FROM {$this->table} eh
                  WHERE eh.status = 'SUBMITTED'
                  ORDER BY eh.created_at DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
