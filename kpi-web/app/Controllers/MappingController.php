<?php

namespace App\Controllers;

use PDO;

class MappingController
{
    private $pdo;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user_id']) || (int)$_SESSION['user_role'] !== 2) {
            header('Location: /KPI/KPI/kpi-web/login');
            exit;
        }
        $this->pdo = $this->getConnection();
    }

    private function getConnection()
    {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=kpi_system;charset=utf8mb4", 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (\PDOException $e) {
            die("Lỗi kết nối: " . $e->getMessage());
        }
    }

    public function review()
    {
        try {
            $bu_id = $_SESSION['business_unit_id'] ?? 0;
            
            // 1. CHỈNH SỬA: Lấy số bản ghi từ URL (per_page), nếu không có mới mặc định là 20
            $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 20;
            
            // Đảm bảo $perPage luôn hợp lệ
            if ($perPage <= 0) $perPage = 20;

            // 2. Lấy số trang hiện tại
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            if ($page <= 0) $page = 1;

            // 3. Tính toán offset chuẩn theo $perPage mới
            $offset = ($page - 1) * $perPage;
            $selectedPeriodId = isset($_GET['period_id']) ? (int)$_GET['period_id'] : 0;

            $periods = $this->pdo->query("SELECT id, period_name FROM Evaluation_Periods ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
            
            $stmtEval = $this->pdo->prepare("SELECT id, username FROM user WHERE role IN (3, 4) AND business_unit_id = ?");
            $stmtEval->execute([$bu_id]);
            $evaluators = $stmtEval->fetchAll(PDO::FETCH_ASSOC);

            // Sửa đoạn WHERE này
            // Sửa lại dòng này trong hàm review()
            $whereSql = "WHERE u.business_unit_id = :bu_id AND eh.status IN ('Chưa đánh giá', 'Chờ duyệt', 'Đánh giá lại')";
            if ($selectedPeriodId > 0) $whereSql .= " AND eh.period_id = :period_id";

            $sql = "SELECT eh.id, u.username as member_name, bu.bu_name as business_unit, 
                           m.username as manager_name, ep.period_name, eh.status
                    FROM Evaluation_Headers eh
                    JOIN user u ON eh.member_id = u.id
                    LEFT JOIN business_unit bu ON u.business_unit_id = bu.id
                    LEFT JOIN user m ON eh.manager_id = m.id
                    LEFT JOIN Evaluation_Periods ep ON eh.period_id = ep.id
                    $whereSql ORDER BY eh.id DESC LIMIT :limit OFFSET :offset";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':bu_id', $bu_id, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            if ($selectedPeriodId > 0) $stmt->bindValue(':period_id', $selectedPeriodId, PDO::PARAM_INT);
            $stmt->execute();
            $evaluations = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $countStmt = $this->pdo->prepare("SELECT COUNT(*) FROM Evaluation_Headers eh JOIN user u ON eh.member_id = u.id $whereSql");
            $countStmt->bindValue(':bu_id', $bu_id, PDO::PARAM_INT);
            if ($selectedPeriodId > 0) $countStmt->bindValue(':period_id', $selectedPeriodId, PDO::PARAM_INT);
            $countStmt->execute();
            $total_pages = ceil($countStmt->fetchColumn() / $perPage);

            $success_message = $_SESSION['success_message'] ?? null;
            $error_message = $_SESSION['error_message'] ?? null;
            unset($_SESSION['success_message'], $_SESSION['error_message']);

            include __DIR__ . '/../../resources/views/mapping/review.php';
        } catch (\Exception $e) {
            die("Lỗi: " . $e->getMessage());
        }
    }

    public function reviewApprove()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $selectedIdsJson = $_POST['selected_ids'] ?? null;
        $action = $_POST['action'] ?? ''; // 'approve' hoặc 'edit'
        $newManagerId = $_POST['evaluator_id'] ?? null; // ID người đánh giá mới

        $ids = json_decode($selectedIdsJson, true);
        if (empty($ids)) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng chọn bản ghi']);
            return;
        }

        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));

            // TRƯỜNG HỢP 1: CHỈNH SỬA NGƯỜI ĐÁNH GIÁ (EDIT)
            if ($action === 'edit') {
                if (!$newManagerId) {
                    echo json_encode(['status' => 'error', 'message' => 'Chưa chọn người đánh giá mới']);
                    return;
                }
                
                // Cập nhật manager_id và giữ trạng thái hoặc đưa về 'Chưa đánh giá' để PM mới thấy
                $sql = "UPDATE evaluation_headers SET manager_id = ?, status = 'Chưa đánh giá' WHERE id IN ($placeholders)";
                $stmt = $this->pdo->prepare($sql);
                $params = array_merge([$newManagerId], $ids);
            } 
            
            // TRƯỜNG HỢP 2: PHÊ DUYỆT (APPROVE)
            else if ($action === 'approve') {
                $sql = "UPDATE evaluation_headers SET status = 'Chưa đánh giá' WHERE id IN ($placeholders)";
                $stmt = $this->pdo->prepare($sql);
                $params = $ids;
            }

           $result = $stmt->execute($params);

            if ($result) {
                // Thay vì redirect bằng PHP, ta trả về JSON thành công
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Thao tác thành công!'
                ]);
                // Lưu message vào session để trang sau khi load lại có thể hiển thị
                $_SESSION['success_message'] = "Thao tác thành công!";
                exit;
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
   
}