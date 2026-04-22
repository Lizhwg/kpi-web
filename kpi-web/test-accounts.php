<?php
/**
 * TEST - Kiểm tra tài khoản thư ký
 */

// Load .env
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

// Kết nối database
$host = $_ENV['DB_HOST'] ?? 'localhost';
$db = $_ENV['DB_NAME'] ?? 'kpi_system';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}

// Lấy tất cả user
$stmt = $pdo->query("SELECT id, username, role, is_deleted FROM User ORDER BY id");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Test - KPI System</title>
    <style>
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            padding: 20px;
            margin: 0;
        }
        .container { 
            max-width: 900px; 
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        h1 {
            color: #667eea;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        thead {
            background: #667eea;
            color: white;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        tr:hover {
            background: #f5f5f5;
        }
        .role-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .role-1 { background: #fef08a; color: #92400e; }
        .role-2 { background: #bfdbfe; color: #1e3a8a; }
        .role-3 { background: #dbeafe; color: #0c4a6e; }
        .role-4 { background: #e9d5ff; color: #5b21b6; }
        .role-5 { background: #fecdd3; color: #831843; }
        .role-6 { background: #dcfce7; color: #166534; }
        
        .role-name {
            font-size: 12px;
            margin-top: 3px;
            font-weight: 500;
        }
        
        .status-ok { color: #059669; font-weight: bold; }
        .status-error { color: #dc2626; font-weight: bold; }
        
        .section {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        
        .instruction {
            background: #e0f2fe;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #0284c7;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>🧪 TEST - Kiểm Tra Tài Khoản</h1>
    
    <div class="instruction">
        <strong>ℹ️ Hướng dẫn:</strong> 
        <ol>
            <li>Tìm người dùng có <span class="role-badge role-2">Role 2 - Thư ký</span></li>
            <li>Ghi nhớ username</li>
            <li>Mật khẩu: <code>password123</code></li>
            <li>Đăng nhập vào: <a href="/KPI/KPI/kpi-web/login" target="_blank">http://localhost/KPI/KPI/kpi-web/login</a></li>
            <li>Truy cập: <a href="/KPI/KPI/kpi-web/mapping/list" target="_blank">http://localhost/KPI/KPI/kpi-web/mapping/list</a></li>
        </ol>
    </div>

    <div class="section">
        <strong>📊 Danh Sách Tài Khoản:</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Trạng Thái</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($u['username']); ?></strong></td>
                    <td>
                        <span class="role-badge role-<?php echo $u['role']; ?>">
                            Role <?php echo $u['role']; ?>
                        </span>
                        <div class="role-name">
                            <?php
                                $roles = [
                                    1 => 'Admin',
                                    2 => 'Thư ký',
                                    3 => 'PM',
                                    4 => 'Trưởng phòng',
                                    5 => 'HRBP',
                                    6 => 'Nhân viên'
                                ];
                                echo $roles[$u['role']] ?? 'Unknown';
                            ?>
                        </div>
                    </td>
                    <td>
                        <?php if ($u['is_deleted'] == 0): ?>
                            <span class="status-ok">✓ Hoạt động</span>
                        <?php else: ?>
                            <span class="status-error">✗ Bị xóa</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="section" style="margin-top: 30px;">
        <strong>✅ Tài Khoản Thư Ký (Cần Dùng):</strong>
        <table style="width: 100%; margin-top: 10px;">
            <tr>
                <td><strong>Username:</strong></td>
                <td><code>secretary_hr</code></td>
            </tr>
            <tr>
                <td><strong>Password:</strong></td>
                <td><code>password123</code></td>
            </tr>
            <tr>
                <td><strong>Role:</strong></td>
                <td><span class="role-badge role-2">Role 2 - Thư ký</span></td>
            </tr>
        </table>
    </div>

</div>

</body>
</html>
