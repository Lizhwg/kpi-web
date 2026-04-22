<?php
/**
 * Tool INSERT - Tạo dữ liệu kiểm thử cho màn hình xét duyệt đánh giá
 */

// Load .env
if (file_exists(__DIR__ . '/../kpi-web/.env')) {
    $env = parse_ini_file(__DIR__ . '/../kpi-web/.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

$host = $_ENV['DB_HOST'] ?? 'localhost';
$db = $_ENV['DB_NAME'] ?? 'kpi_system';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Kết nối Database thất bại: " . $e->getMessage());
}

$message = '';
$error = '';
$created_users = [];
$created_evaluations = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insert_data'])) {
    try {
        // Password mặc định
        $defaultPassword = 'password123';
        $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

        // 0. Delete data in correct order (respect foreign keys)
        // Must delete child tables first, then parent tables
        $pdo->exec("DELETE FROM Evaluation_Headers WHERE member_id IN (SELECT id FROM User WHERE username IN ('secretary1', 'pm1', 'pm2', 'head1', 'head2', 'nhvien1', 'nhvien2', 'nhvien3', 'nhvien4', 'nhvien5', 'hrbp1'))");
        $pdo->exec("DELETE FROM Evaluation_Periods WHERE period_name LIKE 'Đánh giá KPI%'");
        $pdo->exec("DELETE FROM User WHERE username IN ('secretary1', 'pm1', 'pm2', 'head1', 'head2', 'nhvien1', 'nhvien2', 'nhvien3', 'nhvien4', 'nhvien5', 'hrbp1')");
        $pdo->exec("DELETE FROM business_unit WHERE bu_name IN ('Phòng IT', 'Phòng Nhân sự', 'Phòng Kinh doanh', 'Phòng Kỹ thuật')");
        
        // 1. Insert phòng ban
        $buStmt = $pdo->prepare("INSERT INTO business_unit (bu_name, is_deleted) VALUES (?, 0)");
        $businessUnits = [
            'Phòng IT' => 1,
            'Phòng Nhân sự' => 2,
            'Phòng Kinh doanh' => 3,
            'Phòng Kỹ thuật' => 4
        ];
        
        foreach (array_keys($businessUnits) as $buName) {
            $buStmt->execute([$buName]);
        }

        // 2. Insert kỳ đánh giá
        $periodStmt = $pdo->prepare("INSERT INTO Evaluation_Periods (period_name, start_date, end_date, status) VALUES (?, ?, ?, ?)");;
        
        $periodStmt = $pdo->prepare("INSERT INTO Evaluation_Periods (period_name, start_date, end_date, status) VALUES (?, ?, ?, ?)");
        $periods = [
            ['Đánh giá KPI Quý 1/2026', '2026-01-01', '2026-03-31', 'Open'],
            ['Đánh giá KPI Quý 2/2026', '2026-04-01', '2026-06-30', 'Open'],
            ['Đánh giá KPI Quý 3/2026', '2026-07-01', '2026-09-30', 'Closed']
        ];
        
        foreach ($periods as $period) {
            $periodStmt->execute($period);
        }

        // 3. Insert người dùng

        $userStmt = $pdo->prepare("INSERT INTO User (business_unit_id, username, password, role, is_deleted) VALUES (?, ?, ?, ?, 0)");
        
        $users = [
            // [business_unit_id, username, role]
            [1, 'secretary1', 2],  // Thư ký
            [1, 'pm1', 3],         // PM
            [2, 'pm2', 3],         // PM
            [1, 'head1', 4],       // Trưởng phòng
            [2, 'head2', 4],       // Trưởng phòng
            [1, 'hrbp1', 5],       // HRBP
            [1, 'nhvien1', 6],     // Nhân viên
            [1, 'nhvien2', 6],     // Nhân viên
            [1, 'nhvien3', 6],     // Nhân viên
            [2, 'nhvien4', 6],     // Nhân viên
            [2, 'nhvien5', 6],     // Nhân viên
        ];

        $userIds = [];
        foreach ($users as $u) {
            $userStmt->execute([$u[0], $u[1], $hashedPassword, $u[2]]);
            $userIds[$u[1]] = $pdo->lastInsertId();
            $created_users[] = [
                'username' => $u[1],
                'password' => $defaultPassword,
                'role' => ['', '', 'Thư ký', 'PM', 'Trưởng phòng', 'HRBP', 'Nhân viên'][$u[2]]
            ];
        }

        // 4. Insert Evaluation_Headers (dữ liệu xét duyệt)

        $periodId = $userIds['nhvien1'] ?? null;
        // Get period ID
        $periodQuery = $pdo->prepare("SELECT id FROM Evaluation_Periods WHERE period_name = 'Đánh giá KPI Quý 1/2026'");
        $periodQuery->execute();
        $periodResult = $periodQuery->fetch(PDO::FETCH_ASSOC);
        $periodId = $periodResult['id'] ?? null;

        if (!$periodId) {
            throw new Exception("Không tìm thấy kỳ đánh giá");
        }

        // Insert evaluation headers
        $evalStmt = $pdo->prepare("
            INSERT INTO Evaluation_Headers (member_id, period_id, status, manager_id, dev_head_id, hr_id, final_total_score) 
            VALUES (?, ?, 'Chờ duyệt', ?, ?, NULL, ?)
        ");

        $evaluationData = [
            ['nhvien1', $userIds['head1'], $userIds['pm1'], 8.5],
            ['nhvien2', $userIds['head1'], $userIds['pm1'], 7.8],
            ['nhvien3', $userIds['head1'], $userIds['pm1'], 8.2],
            ['nhvien4', $userIds['head2'], $userIds['pm2'], 7.5],
            ['nhvien5', $userIds['head2'], $userIds['pm2'], 8.0],
        ];

        foreach ($evaluationData as $eval) {
            $evalStmt->execute([$userIds[$eval[0]], $periodId, $eval[1], $eval[2], $eval[3]]);
            $created_evaluations[] = [
                'member' => $eval[0],
                'manager' => array_search($eval[1], $userIds),
                'pm' => array_search($eval[2], $userIds),
                'score' => $eval[3]
            ];
        }

        $message = "✅ <strong>Tạo dữ liệu kiểm thử thành công!</strong>";

    } catch (Exception $e) {
        $error = "❌ Lỗi: " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Insert Test Data - Xét duyệt đánh giá</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 20px; }
        .alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-info { background: #e3f2fd; color: #1565c0; border: 1px solid #90caf9; }
        .btn { padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn:hover { background: #764ba2; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #667eea; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .role-2 { background: #fff3cd; }
        .role-3 { background: #e3f2fd; }
        .role-4 { background: #f0f4c3; }
        .role-6 { background: #e8f5e9; }
        .section { margin: 30px 0; }
        .section h2 { color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
        code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; }
        .warning { background: #fff3cd; padding: 15px; border-radius: 4px; border-left: 4px solid #ffc107; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Tạo dữ liệu kiểm thử - Màn hình xét duyệt đánh giá</h1>

        <?php if (!empty($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Hướng dẫn -->
        <div class="alert alert-info">
            <strong>ℹ️ Hướng dẫn:</strong> Nhấn nút dưới để tạo dữ liệu test gồm: 11 người dùng, 3 kỳ đánh giá, 5 bản đánh giá chờ duyệt.
        </div>

        <!-- Button Insert -->
        <div class="section">
            <form method="POST">
                <button type="submit" name="insert_data" value="1" class="btn">✅ Tạo dữ liệu kiểm thử</button>
            </form>
        </div>

        <!-- Danh sách tài khoản được tạo -->
        <?php if (!empty($created_users)): ?>
        <div class="section">
            <h2>📋 Tài khoản được tạo</h2>
            <table>
                <tr>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Vai trò</th>
                    <th>Ghi chú</th>
                </tr>
                <?php foreach ($created_users as $u): ?>
                <tr class="role-<?php echo ['', '', 2, 3, 4, 5, 6][in_array($u['role'], ['Thư ký', 'PM', 'Trưởng phòng', 'HRBP', 'Nhân viên']) ? array_search($u['role'], ['', '', 'Thư ký', 'PM', 'Trưởng phòng', 'HRBP', 'Nhân viên']) : 0]; ?>">
                    <td><strong><?php echo htmlspecialchars($u['username']); ?></strong></td>
                    <td><code><?php echo htmlspecialchars($u['password']); ?></code></td>
                    <td><?php echo htmlspecialchars($u['role']); ?></td>
                    <td>
                        <?php if ($u['role'] === 'Thư ký'): ?>
                            👤 Người xét duyệt (đăng nhập để test)
                        <?php elseif ($u['role'] === 'Nhân viên'): ?>
                            👥 Người được đánh giá
                        <?php elseif ($u['role'] === 'PM'): ?>
                            📊 Người chấm điểm
                        <?php elseif ($u['role'] === 'Trưởng phòng'): ?>
                            👔 Người quản lý
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>

        <!-- Dữ liệu đánh giá -->
        <?php if (!empty($created_evaluations)): ?>
        <div class="section">
            <h2>📊 Dữ liệu đánh giá (Chờ duyệt)</h2>
            <table>
                <tr>
                    <th>Nhân viên</th>
                    <th>Trưởng phòng</th>
                    <th>PM</th>
                    <th>Điểm số</th>
                    <th>Trạng thái</th>
                </tr>
                <?php foreach ($created_evaluations as $eval): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($eval['member']); ?></strong></td>
                    <td><?php echo htmlspecialchars($eval['manager']); ?></td>
                    <td><?php echo htmlspecialchars($eval['pm']); ?></td>
                    <td><?php echo htmlspecialchars($eval['score']); ?></td>
                    <td><span style="background: #fff3cd; padding: 4px 8px; border-radius: 3px;">Chờ duyệt</span></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>

        <!-- Hướng dẫn sử dụng -->
        <div class="section">
            <h2>🚀 Hướng dẫn sử dụng</h2>
            
            <div class="warning">
                <strong>⚠️ Lưu ý:</strong> Những lần chạy tiếp theo sẽ xóa dữ liệu cũ và tạo mới. Nếu bạn có dữ liệu quan trọng, hãy backup trước.
            </div>

            <h3>1️⃣ Đăng nhập bằng tài khoản Thư ký</h3>
            <ul>
                <li><strong>URL:</strong> <a href="/KPI/kpi-web/login">/KPI/kpi-web/login</a></li>
                <li><strong>Username:</strong> <code>secretary1</code></li>
                <li><strong>Password:</strong> <code>password123</code></li>
            </ul>

            <h3>2️⃣ Xem trang xét duyệt</h3>
            <p>Sau khi đăng nhập, sẽ tự động chuyển hướng đến: <a href="/KPI/kpi-web/mapping/review">/KPI/kpi-web/mapping/review</a></p>

            <h3>3️⃣ Danh sách nhân viên</h3>
            <p>Sẽ thấy 5 nhân viên chờ duyệt:</p>
            <ul>
                <li>nhvien1 - Phòng IT - Điểm: 8.5 - Trưởng phòng: head1 - PM: pm1</li>
                <li>nhvien2 - Phòng IT - Điểm: 7.8 - Trưởng phòng: head1 - PM: pm1</li>
                <li>nhvien3 - Phòng IT - Điểm: 8.2 - Trưởng phòng: head1 - PM: pm1</li>
                <li>nhvien4 - Phòng Nhân sự - Điểm: 7.5 - Trưởng phòng: head2 - PM: pm2</li>
                <li>nhvien5 - Phòng Nhân sự - Điểm: 8.0 - Trưởng phòng: head2 - PM: pm2</li>
            </ul>

            <h3>4️⃣ Tác vụ có thể làm</h3>
            <ul>
                <li>✅ Phê duyệt một hoặc nhiều nhân viên</li>
                <li>📝 Chỉnh sửa người đánh giá (PM)</li>
                <li>🔍 Xem chi tiết đánh giá</li>
            </ul>
        </div>

        <!-- Thông tin cơ sở dữ liệu -->
        <div class="section">
            <h2>🗄️ Thông tin cơ sở dữ liệu</h2>
            <table>
                <tr>
                    <th>Thành phần</th>
                    <th>Số lượng</th>
                </tr>
                <tr>
                    <td>Phòng ban</td>
                    <td>4</td>
                </tr>
                <tr>
                    <td>Kỳ đánh giá</td>
                    <td>3 (1 mở, 2 đóng)</td>
                </tr>
                <tr>
                    <td>Người dùng</td>
                    <td>11</td>
                </tr>
                <tr>
                    <td>Bản đánh giá chờ duyệt</td>
                    <td>5</td>
                </tr>
            </table>
        </div>

        <!-- File SQL Alternative -->
        <div class="section">
            <h2>💾 Phương án khác: Sử dụng SQL Script</h2>
            <p>Nếu muốn, có thể chạy file SQL trực tiếp:</p>
            <pre>SOURCE /path/to/KPI/database/03_insert_test_data.sql;</pre>
            <p><em>(Lưu ý: Cần sửa password hash trong file SQL)</em></p>
        </div>

    </div>
</body>
</html>
