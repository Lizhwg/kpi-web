<?php
/**
 * Dashboard - Trang chủ sau khi đăng nhập thành công
 */
$roles = [
    1 => 'Admin',
    2 => 'Thư ký',
    3 => 'PM',
    4 => 'Trưởng phòng',
    5 => 'HRBP',
    6 => 'Nhân viên'
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - KPI System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }

        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            font-size: 24px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .user-info a:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }

        main {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .card h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .card-value {
            font-size: 32px;
            font-weight: bold;
            color: #333;
        }

        .info-box {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .info-box h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 20px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #666;
            font-weight: 600;
        }

        .info-value {
            color: #333;
        }

        .role-badge {
            display: inline-block;
            background-color: #f59e0b;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <header>
        <h1>🏆 KPI System - Dashboard</h1>
        <div class="user-info">
            <span>Xin chào, <?php echo htmlspecialchars($user['username'] ?? 'User'); ?></span>
            <a href="/KPI/KPI/kpi-web/logout">Đăng xuất</a>
        </div>
    </header>

    <main>
        <div class="dashboard-grid">
            <div class="card">
                <h3>Tổng Đánh giá</h3>
                <div class="card-value">0</div>
            </div>
            <div class="card">
                <h3>Đang Xử lý</h3>
                <div class="card-value">0</div>
            </div>
            <div class="card">
                <h3>Đã Hoàn thành</h3>
                <div class="card-value">0</div>
            </div>
        </div>

        <div class="info-box">
            <h2>Thông Tin Tài Khoản</h2>
            <div class="info-item">
                <span class="info-label">Tên đăng nhập:</span>
                <span class="info-value"><?php echo htmlspecialchars($user['username']); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Vai trò:</span>
                <span class="info-value">
                    <span class="role-badge">
                        <?php echo $roles[$user['role']] ?? 'Không xác định'; ?>
                    </span>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Phòng ban:</span>
                <span class="info-value"><?php echo htmlspecialchars($user['business_unit_id'] ?? 'N/A'); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Trạng thái:</span>
                <span class="info-value">
                    <?php echo $user['is_deleted'] ? '<span style="color: red;">Bị khóa</span>' : '<span style="color: green;">Hoạt động</span>'; ?>
                </span>
            </div>
        </div>

        <div class="info-box">
            <h2>Hướng Dẫn Nhanh</h2>
            <p style="margin-bottom: 15px; color: #666;">
                Hệ thống KPI được chia thành 3 module chính:
            </p>
            <ul style="margin-left: 20px; color: #666; line-height: 2;">
                <li><strong>Module 1 - Xác thực:</strong> Đăng nhập, phân quyền người dùng</li>
                <li><strong>Module 2 - Quản lý danh sách:</strong> Thư ký duyệt danh sách "Ai đánh giá ai"</li>
                <li><strong>Module 3 - Đánh giá:</strong> PM chấm điểm & Trưởng phòng phê duyệt</li>
            </ul>
        </div>

        <?php if ((int)$user['role'] === 2): // Thư ký ?>
        <div class="info-box">
            <h2>📋 Chức Năng Thư Ký</h2>
            <p style="margin-bottom: 15px; color: #666;">
                Bạn có quyền truy cập các chức năng sau:
            </p>
            <ul style="margin-left: 20px; color: #666; line-height: 2;">
                <li><a href="/KPI/KPI/kpi-web/mapping/review" style="color: #667eea; text-decoration: none; font-weight: 600;">→ Xét duyệt người đánh giá</a> - Duyệt danh sách "Ai đánh giá ai" cho mỗi kỳ đánh giá</li>
                <li><a href="/KPI/KPI/kpi-web/mapping/list" style="color: #667eea; text-decoration: none; font-weight: 600;">→ Xem danh sách đánh giá</a> - Theo dõi tiến độ đánh giá</li>
            </ul>
        </div>
        <?php else: ?>
        <div class="info-box" style="background: #f0f4ff; border: 1px solid #667eea;">
            <p style="color: #667eea; font-weight: 600;">
                💡 Debug: Role hiện tại = <?php echo htmlspecialchars((int)$user['role']); ?> (cần role = 2 để xem chức năng Thư ký)
            </p>
        </div>
        <?php endif; ?>
    </main>
</body>
</html>
