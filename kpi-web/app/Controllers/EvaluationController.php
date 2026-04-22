<?php

namespace App\Controllers;

use App\Models\EvaluationHeader;
use App\Models\EvaluationDetail;
use App\Models\AssessmentItem;
// Lưu ý: Đảm bảo file trong app/Models là EvaluationMapping.php (Viết hoa E và M)
use App\Models\EvaluationMapping; 
use App\Models\User;
use PDO;

/**
 * EvaluationController - Xử lý chấm điểm và phê duyệt
 * Module 3: PM chấm điểm & Trưởng phòng phê duyệt
 */
class EvaluationController
{
    private $headerModel;
    private $detailModel;
    private $itemModel;
    private $mappingModel;
    private $userModel;

    public function __construct()
    {
        $this->headerModel = new EvaluationHeader();
        $this->detailModel = new EvaluationDetail();
        $this->itemModel = new AssessmentItem();
        $this->mappingModel = new EvaluationMapping();
        $this->userModel = new User();
    }

    /**
     * [MỚI] Hiển thị màn hình danh sách nhân viên cần đánh giá (Bảng màu xanh)
     */
    public function requestList()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // PM đăng nhập sẽ lấy user_id từ session
        $pm_id = $_SESSION['user_id'] ?? 0;
        
        try {
            $pdo = new \PDO("mysql:host=localhost;dbname=kpi_system;charset=utf8mb4", 'root', '');
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // SQL khớp với ERD em gửi:
            // Lấy dữ liệu từ evaluation_headers nối với user và evaluation_periods
            $sql = "SELECT 
                        eh.id as header_id, 
                        eh.mapping_id, 
                        ep.period_name, 
                        u.username as member_name, 
                        bu.bu_name as department, 
                        u.role, 
                        '1.1' as level, -- Giả sử level cố định hoặc lấy từ bảng khác
                        eh.status
                    FROM evaluation_headers eh
                    JOIN user u ON eh.member_id = u.id
                    LEFT JOIN business_unit bu ON u.business_unit_id = bu.id
                    LEFT JOIN evaluation_periods ep ON eh.period_id = ep.id
                    WHERE eh.manager_id = :pm_id
                    ORDER BY eh.id DESC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':pm_id' => $pm_id]);
            $evaluations = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $roleMap = [1 => 'Admin', 2 => 'Thư ký', 3 => 'PM', 4 => 'Trưởng phòng', 5 => 'HRBP', 6 => 'Dev/BA'];

            // Trỏ đến view hiển thị
            include __DIR__ . '/../../resources/views/mapping/request_list.php';
            
        } catch (\Exception $e) {
            die("Lỗi truy vấn Database: " . $e->getMessage());
        }
    }
    /**
     * Hiển thị form chấm điểm cho PM
     */
    public function pmForm()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // 1. Lấy ID của bản ghi từ URL
        $id = $_GET['mapping_id'] ?? null;

        if (!$id) {
            die("Không tìm thấy ID bản ghi để chấm điểm!");
        }

        // 2. Lấy dữ liệu từ bảng evaluation_headers (thông qua Model đã sửa ở bước 1)
        $header = $this->mappingModel->getById($id);
        
        if (!$header) {
            die("Bản ghi này không tồn tại trong Database.");
        }

        // 3. Lấy danh sách tiêu chí đánh giá
        $assessmentItems = $this->itemModel->getAll();
        
        // 4. CHỈNH SỬA QUAN TRỌNG: Theo ERD, cột chứa ID nhân viên là 'member_id'
        $evaluatee = $this->userModel->getById($header['member_id']);

        // 5. Load giao diện chấm điểm
        include __DIR__ . '/../../resources/views/evaluation/pm_form.php';
    }

    /**
     * Lưu điểm chấm từ form PM
     */
    public function saveScore()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Phương thức không hợp lệ']);
            return;
        }

        $mapping_id = $_POST['mapping_id'];
        $evaluator_id = $_SESSION['user_id'];
        $evaluatee_id = $_POST['evaluatee_id'];
        $period_id = $_POST['period_id'];
        $comments = $_POST['comments'] ?? '';
        $scores = $_POST['scores'] ?? [];

        $header = $this->headerModel->create([
            'mapping_id' => $mapping_id,
            'evaluator_id' => $evaluator_id,
            'evaluatee_id' => $evaluatee_id,
            'period_id' => $period_id,
            'comments' => $comments,
            'status' => 'DRAFT'
        ]);

        if (!$header) {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi tạo đánh giá']);
            return;
        }

        foreach ($scores as $item_id => $score) {
            $this->detailModel->create([
                'header_id' => $header['id'],
                'assessment_item_id' => $item_id,
                'score' => $score
            ]);
        }

        $this->mappingModel->updateStatus($mapping_id, 'IN_PROGRESS');
        echo json_encode(['status' => 'success', 'header_id' => $header['id']]);
    }

    /**
     * Submit đánh giá
     */
    public function submitEvaluation()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error']);
            return;
        }

        $header_id = $_POST['header_id'];
        $result = $this->headerModel->updateStatus($header_id, 'SUBMITTED');
        echo json_encode($result);
    }

    /**
     * Màn hình phê duyệt đánh giá cho Trưởng phòng
     */
    public function review()
    {
        if ($_SESSION['user_role'] !== 'MANAGER' && (int)$_SESSION['user_role'] !== 4) {
            http_response_code(403);
            die("Bạn không có quyền phê duyệt");
        }

        $period_id = $_GET['period_id'] ?? null;
        $department_id = $_SESSION['department_id'] ?? 0;

        $evaluations = $this->headerModel->getEvaluationsByDepartment($department_id, $period_id);
        include __DIR__ . '/../../resources/views/evaluation/review.php';
    }

    /**
     * Phê duyệt hoặc từ chối đánh giá
     */
    public function approveEvaluation()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error']);
            return;
        }

        $header_id = $_POST['header_id'];
        $action = $_POST['action']; 
        $comment = $_POST['comment'] ?? '';

        $result = $this->headerModel->updateStatus($header_id, $action, $comment);
        echo json_encode($result);
    }

    public function viewDetail()
    {
        $header_id = $_GET['header_id'] ?? null;

        $header = $this->headerModel->getById($header_id);
        $details = $this->detailModel->getByHeaderId($header_id);
        $evaluator = $this->userModel->getById($header['evaluator_id']);
        $evaluatee = $this->userModel->getById($header['evaluatee_id']);

        include __DIR__ . '/../../resources/views/evaluation/view_detail.php';
    }
}