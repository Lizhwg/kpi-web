<?php

namespace App\Models;

use PDO;

/**
 * EvaluationDetail Model - Lưu trữ chi tiết điểm đánh giá
 */
class EvaluationDetail
{
    private $pdo;
    private $table = 'evaluation_details';

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

    public function create($data)
    {
        $query = "INSERT INTO {$this->table} (header_id, assessment_item_id, score, comment) 
                  VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($query);

        try {
            $stmt->execute([
                $data['header_id'],
                $data['assessment_item_id'],
                $data['score'] ?? null,
                $data['comment'] ?? ''
            ]);
            return ['status' => 'success', 'id' => $this->pdo->lastInsertId()];
        } catch (\PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getByHeaderId($header_id)
    {
        $query = "SELECT ed.*, ai.item_name, ai.max_score FROM {$this->table} ed
                  JOIN assessment_items ai ON ed.assessment_item_id = ai.id
                  WHERE ed.header_id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$header_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
