<?php

namespace App\Services;

/**
 * RedmineClient - Giao tiếp với Java API để lấy dữ liệu từ Redmine
 * Đây là file trung gian gọi sang Java API
 */
class RedmineClient
{
    private $baseUrl;
    private $apiKey;

    public function __construct()
    {
        $this->baseUrl = $_ENV['REDMINE_API_URL'] ?? 'http://localhost:8080/api';
        $this->apiKey = $_ENV['REDMINE_API_KEY'] ?? '';
    }

    /**
     * Lấy điểm đánh giá từ Redmine cho một người dùng
     */
    public function getUserScore($user_id, $start_date, $end_date)
    {
        $url = "{$this->baseUrl}/sync/user-score";
        $params = [
            'user_id' => $user_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'api_key' => $this->apiKey
        ];

        return $this->makeRequest($url, 'GET', $params);
    }

    /**
     * Lấy danh sách log-time từ Redmine
     */
    public function getLogtimes($user_id, $start_date, $end_date)
    {
        $url = "{$this->baseUrl}/sync/logtimes";
        $params = [
            'user_id' => $user_id,
            'start_date' => $start_date,
            'end_date' => $end_date
        ];

        return $this->makeRequest($url, 'GET', $params);
    }

    /**
     * Lấy danh sách task của người dùng
     */
    public function getUserTasks($user_id)
    {
        $url = "{$this->baseUrl}/sync/tasks";
        $params = ['user_id' => $user_id];

        return $this->makeRequest($url, 'GET', $params);
    }

    /**
     * Gửi yêu cầu HTTP
     */
    private function makeRequest($url, $method = 'GET', $params = [])
    {
        // Thêm tham số vào URL nếu là GET
        if ($method === 'GET' && !empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return ['status' => 'error', 'message' => "HTTP $httpCode"];
        }

        return json_decode($response, true);
    }

    /**
     * Đồng bộ hóa dữ liệu toàn bộ (Batch sync)
     */
    public function syncAll($period_id)
    {
        $url = "{$this->baseUrl}/sync/all";
        $params = [
            'period_id' => $period_id,
            'api_key' => $this->apiKey
        ];

        return $this->makeRequest($url, 'POST', $params);
    }
}
