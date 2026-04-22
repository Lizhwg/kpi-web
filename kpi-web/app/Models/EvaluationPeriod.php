<?php

namespace App\Models;

use PDO;

/**
 * EvaluationPeriod Model - Quản lý kỳ đánh giá
 */
class EvaluationPeriod
{
    private $pdo;
    private $table = 'evaluation_periods';

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
        $query = "SELECT * FROM {$this->table} ORDER BY start_date DESC";
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

    public function getActive()
    {
        $query = "SELECT * FROM {$this->table} WHERE status = 'ACTIVE'";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $query = "INSERT INTO {$this->table} (period_name, start_date, end_date, status) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($query);

        try {
            $stmt->execute([
                $data['period_name'],
                $data['start_date'],
                $data['end_date'],
                $data['status'] ?? 'DRAFT'
            ]);
            return ['status' => 'success', 'id' => $this->pdo->lastInsertId()];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function update($id, $data)
    {
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;

        $query = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->pdo->prepare($query);

        try {
            $stmt->execute($values);
            return ['status' => 'success'];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
