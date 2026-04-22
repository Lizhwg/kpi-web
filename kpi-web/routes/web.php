<?php

return [
    'GET' => [
        '/' => 'AuthController@dashboard',
        '/login' => 'AuthController@login',
        '/logout' => 'AuthController@logout',
        '/register' => 'AuthController@registerForm',
        '/dashboard' => 'AuthController@dashboard',
        
        // --- Routes cho Thư ký (Mapping) ---
        '/mapping/review' => 'MappingController@review',
        '/mapping/detail' => 'MappingController@detail',
        
        // --- [THÊM MỚI] Routes cho PM (Evaluation) ---
        '/evaluation/request-list' => 'EvaluationController@requestList', // Trang bảng màu xanh PM vừa làm
        '/evaluation/pmForm'       => 'EvaluationController@pmForm',       // Trang form chấm điểm chi tiết
        '/evaluation/view-detail'  => 'EvaluationController@viewDetail',    // Xem lại kết quả đã chấm
    ],
    'POST' => [
        '/login' => 'AuthController@handleLogin',
        '/register' => 'AuthController@handleRegister',
        
        // --- [THÊM MỚI] Xử lý dữ liệu cho Mapping ---
        '/mapping/review-approve' => 'MappingController@reviewApprove', 
        '/mapping/review-edit'    => 'MappingController@reviewEdit',
        
        // --- [THÊM MỚI] Xử lý dữ liệu cho Evaluation ---
        '/evaluation/save-score'   => 'EvaluationController@saveScore',   // Lưu điểm tạm (Draft)
        '/evaluation/submit'       => 'EvaluationController@submitEvaluation', // Gửi đánh giá lên cấp trên
        '/evaluation/approve'      => 'EvaluationController@approveEvaluation', // TP phê duyệt
    ]
];