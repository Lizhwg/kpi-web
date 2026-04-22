<?php

namespace App\Controllers;

use App\Models\EvaluationHeader;
use App\Models\EvaluationDetail;
use App\Models\AssessmentItem;
use App\Models\EvaluationMapping;
use App\Models\User;

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
     * Hiển thị form chấm điểm cho PM
     */
    public function pmForm()
    {
        // Kiểm tra quyền: chỉ PM mới xem được
        if ($_SESSION['user_role'] !== 'PM') {
            http_response_code(403);
            echo "Bạn không có quyền truy cập";
            return;
        }

        $mapping_id = $_GET['mapping_id'] ?? null;
        $mapping = $this->mappingModel->getById($mapping_id);
        $assessmentItems = $this->itemModel->getAll();
        $evaluatee = $this->userModel->getById($mapping['evaluatee_id']);

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

        // Tạo hoặc cập nhật header
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

        // Lưu các chi tiết điểm
        foreach ($scores as $item_id => $score) {
            $this->detailModel->create([
                'header_id' => $header['id'],
                'assessment_item_id' => $item_id,
                'score' => $score
            ]);
        }

        // Cập nhật trạng thái mapping
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
        // Kiểm tra quyền: chỉ MANAGER mới phê duyệt
        if ($_SESSION['user_role'] !== 'MANAGER') {
            http_response_code(403);
            echo "Bạn không có quyền phê duyệt";
            return;
        }

        $period_id = $_GET['period_id'] ?? null;
        $department_id = $_SESSION['department_id'];

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
        $action = $_POST['action']; // 'approve' hoặc 'reject'
        $comment = $_POST['comment'] ?? '';

        $result = $this->headerModel->updateStatus($header_id, $action, $comment);

        echo json_encode($result);
    }

    /**
     * Yêu cầu đánh giá lại
     */
    public function requestReview()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error']);
            return;
        }

        $header_id = $_POST['header_id'];
        $reason = $_POST['reason'] ?? '';

        // Đổi trạng thái về DRAFT để PM có thể chỉnh sửa
        $result = $this->headerModel->updateStatus($header_id, 'DRAFT', $reason);

        echo json_encode($result);
    }

    /**
     * Xem chi tiết đánh giá
     */
    public function viewDetail()
    {
        $header_id = $_GET['header_id'] ?? null;

        $header = $this->headerModel->getById($header_id);
        $details = $this->detailModel->getByHeaderId($header_id);
        $evaluator = $this->userModel->getById($header['evaluator_id']);
        $evaluatee = $this->userModel->getById($header['evaluatee_id']);

        include __DIR__ . '/../../resources/views/evaluation/view_detail.php';
    }

    /**
     * API: Lấy danh sách đánh giá cần xử lý
     */
    public function getMyEvaluations()
    {
        $user_id = $_SESSION['user_id'];
        $role = $_SESSION['user_role'];

        if ($role === 'PM') {
            $mappings = $this->mappingModel->getMappingsByEvaluator($user_id);
        } else if ($role === 'MANAGER') {
            $evaluations = $this->headerModel->getEvaluationsByReviewer($user_id);
        }

        echo json_encode(['status' => 'success', 'data' => $mappings ?? $evaluations ?? []]);
    }
}
