<?php
/**
 * File test kiểm tra database - tất cả tài khoản
 */

// Load .env
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
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

?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Database - Kiểm tra tài khoản</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #667eea; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .success { background: #d4edda; }
        .error { background: #f8d7da; }
        h2 { color: #333; margin-top: 30px; }
    </style>
</head>
<body>
    <h1>🔍 Debug: Kiểm tra Database - Tài khoản người dùng</h1>

    <?php
    try {
        // Lấy tất cả tài khoản
        $query = "SELECT id, username, role, is_deleted, business_unit_id FROM User ORDER BY id DESC";
        $stmt = $pdo->query($query);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $roleNames = [
            1 => 'Admin',
            2 => 'Thư ký',
            3 => 'PM',
            4 => 'Trưởng phòng',
            5 => 'HRBP',
            6 => 'Nhân viên'
        ];

        echo "<h2>✓ Danh sách tài khoản trong Database:</h2>";
        echo "<p>Tổng số tài khoản: " . count($users) . "</p>";
        echo "<table>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>Username</th>";
        echo "<th>Role</th>";
        echo "<th>Role Name</th>";
        echo "<th>Business Unit ID</th>";
        echo "<th>Is Deleted</th>";
        echo "</tr>";

        foreach ($users as $u) {
            $rowClass = ($u['is_deleted'] == 1) ? 'error' : '';
            if ($u['role'] == 2) {
                $rowClass = 'success';
            }
            echo "<tr class='$rowClass'>";
            echo "<td>" . $u['id'] . "</td>";
            echo "<td><strong>" . htmlspecialchars($u['username']) . "</strong></td>";
            echo "<td>" . $u['role'] . "</td>";
            echo "<td>" . ($roleNames[$u['role']] ?? 'Unknown') . "</td>";
            echo "<td>" . ($u['business_unit_id'] ?? 'NULL') . "</td>";
            echo "<td>" . ($u['is_deleted'] ? '❌ Yes' : '✓ No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";

        // Kiểm tra tài khoản Thư ký
        echo "<h2>📋 Tài khoản Thư ký (Role = 2):</h2>";
        $secretaryQuery = "SELECT id, username, business_unit_id FROM User WHERE role = 2 AND is_deleted = 0";
        $stmt = $pdo->query($secretaryQuery);
        $secretaries = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($secretaries)) {
            echo "<div style='background: #fff3cd; padding: 15px; border-radius: 4px;'>";
            echo "⚠️ <strong>Không có tài khoản Thư ký nào!</strong>";
            echo "<p>Hãy tạo tài khoản với role = 2 để kiểm tra</p>";
            echo "</div>";
        } else {
            echo "<table>";
            echo "<tr>";
            echo "<th>ID</th>";
            echo "<th>Username</th>";
            echo "<th>Business Unit ID</th>";
            echo "</tr>";
            foreach ($secretaries as $sec) {
                echo "<tr>";
                echo "<td>" . $sec['id'] . "</td>";
                echo "<td>" . htmlspecialchars($sec['username']) . "</td>";
                echo "<td>" . ($sec['business_unit_id'] ?? 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }

    } catch (Exception $e) {
        echo "<div style='background: #f8d7da; padding: 15px; border-radius: 4px;'>";
        echo "❌ Lỗi: " . $e->getMessage();
        echo "</div>";
    }
    ?>

    <div style="margin-top: 30px; background: #e3f2fd; padding: 15px; border-radius: 4px;">
        <h3>📝 Hướng dẫn test:</h3>
        <ol>
            <li>Kiểm tra xem có tài khoản Thư ký (role = 2) không</li>
            <li>Nếu không có, tạo một tài khoản mới với SQL: <code>INSERT INTO User (username, password, role, is_deleted) VALUES ('secretary1', SHA2('password123', 256), 2, 0);</code></li>
            <li>Sau đó <a href="/KPI/KPI/kpi-web/login">đăng nhập</a> và kiểm tra <a href="/KPI/KPI/kpi-web/test-login-debug.php">session debug</a></li>
        </ol>
    </div>

</body>
</html>
