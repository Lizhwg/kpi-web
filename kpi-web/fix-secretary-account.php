<?php
/**
 * File fix - Sửa/tạo tài khoản Thư ký với password hash đúng
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

$message = '';
$error = '';

// Nếu submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        
        if (empty($username) || empty($password)) {
            $error = "Username và password không được để trống";
        } else if (strlen($password) < 6) {
            $error = "Mật khẩu phải có ít nhất 6 ký tự";
        } else {
            // Check nếu username đã tồn tại
            $checkStmt = $pdo->prepare("SELECT id FROM User WHERE username = ?");
            $checkStmt->execute([$username]);
            
            if ($checkStmt->fetch()) {
                $error = "Username '$username' đã tồn tại. Hãy chọn username khác";
            } else {
                // Hash password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert tài khoản mới
                $insertStmt = $pdo->prepare("
                    INSERT INTO User (username, password, role, business_unit_id, is_deleted) 
                    VALUES (?, ?, 2, NULL, 0)
                ");
                
                try {
                    $insertStmt->execute([$username, $hashedPassword]);
                    $message = "✓ Tạo tài khoản Thư ký thành công!<br>Username: <strong>$username</strong><br>Password: <strong>$password</strong>";
                } catch (Exception $e) {
                    $error = "Lỗi khi tạo tài khoản: " . $e->getMessage();
                }
            }
        }
    }
    
    if ($action === 'update') {
        $user_id = (int)($_POST['user_id'] ?? 0);
        $password = trim($_POST['password'] ?? '');
        
        if ($user_id <= 0) {
            $error = "User ID không hợp lệ";
        } else if (empty($password)) {
            $error = "Password không được để trống";
        } else if (strlen($password) < 6) {
            $error = "Mật khẩu phải có ít nhất 6 ký tự";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $updateStmt = $pdo->prepare("UPDATE User SET password = ?, role = 2, is_deleted = 0 WHERE id = ?");
            
            try {
                $updateStmt->execute([$hashedPassword, $user_id]);
                
                // Lấy thông tin user để hiển thị
                $getStmt = $pdo->prepare("SELECT id, username FROM User WHERE id = ?");
                $getStmt->execute([$user_id]);
                $u = $getStmt->fetch(PDO::FETCH_ASSOC);
                
                $message = "✓ Cập nhật tài khoản thành công!<br>Username: <strong>" . htmlspecialchars($u['username']) . "</strong><br>Role: <strong>2 (Thư ký)</strong><br>Password: <strong>$password</strong>";
            } catch (Exception $e) {
                $error = "Lỗi khi cập nhật: " . $e->getMessage();
            }
        }
    }
}

// Lấy danh sách tài khoản
$query = "SELECT id, username, role FROM User ORDER BY id DESC";
$stmt = $pdo->query($query);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$roleNames = [1 => 'Admin', 2 => 'Thư ký', 3 => 'PM', 4 => 'Trưởng phòng', 5 => 'HRBP', 6 => 'Nhân viên'];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix - Tạo/Sửa tài khoản Thư ký</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #333; margin-bottom: 20px; }
        .message { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6cb; }
        .form-section { margin-bottom: 30px; border: 1px solid #ddd; padding: 15px; border-radius: 4px; background: #fafafa; }
        .form-section h2 { font-size: 18px; color: #333; margin-top: 0; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: #667eea; box-shadow: 0 0 5px rgba(102, 126, 234, 0.3); }
        button { padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 14px; }
        button:hover { background: #764ba2; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #667eea; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .role-2 { background: #d4edda; }
        .info-box { background: #e3f2fd; padding: 15px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #2196F3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Tạo/Sửa tài khoản Thư ký</h1>

        <div class="info-box">
            <strong>💡 Lưu ý:</strong> Khi sửa hoặc tạo tài khoản trong tool này, mật khẩu sẽ được hash tự động. Đơn giản hơn là dùng SQL trực tiếp.
        </div>

        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="error">❌ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Form tạo tài khoản mới -->
        <div class="form-section">
            <h2>1️⃣ Tạo tài khoản Thư ký mới</h2>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div class="form-group">
                    <label for="new_username">Username:</label>
                    <input type="text" id="new_username" name="username" placeholder="Ví dụ: secretary1" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Password:</label>
                    <input type="text" id="new_password" name="password" placeholder="Ví dụ: password123" required>
                </div>
                <button type="submit">Tạo tài khoản mới</button>
            </form>
        </div>

        <!-- Form sửa tài khoản hiện có -->
        <div class="form-section">
            <h2>2️⃣ Sửa tài khoản hiện có thành Thư ký (Role 2)</h2>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <div class="form-group">
                    <label for="user_id">Chọn tài khoản:</label>
                    <select id="user_id" name="user_id" required>
                        <option value="">-- Chọn tài khoản --</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?php echo $u['id']; ?>">
                                #<?php echo $u['id']; ?> - <?php echo htmlspecialchars($u['username']); ?> (Role: <?php echo $roleNames[$u['role']] ?? 'Unknown'; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="update_password">Password mới:</label>
                    <input type="text" id="update_password" name="password" placeholder="Ví dụ: newpassword123" required>
                </div>
                <button type="submit">Cập nhật thành Thư ký</button>
            </form>
        </div>

        <!-- Danh sách tài khoản -->
        <div class="form-section">
            <h2>📋 Danh sách tài khoản hiện có</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                </tr>
                <?php foreach ($users as $u): ?>
                    <tr <?php echo ($u['role'] == 2) ? 'class="role-2"' : ''; ?>>
                        <td><?php echo $u['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($u['username']); ?></strong></td>
                        <td><?php echo $roleNames[$u['role']] ?? 'Unknown'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="info-box">
            <strong>✅ Sau khi tạo/sửa xong:</strong>
            <ol>
                <li>Quay lại <a href="/KPI/KPI/kpi-web/login">trang đăng nhập</a></li>
                <li>Đăng nhập bằng username/password vừa tạo</li>
                <li>Hệ thống tự động chuyển hướng đến trang xét duyệt đánh giá</li>
            </ol>
        </div>
    </div>
</body>
</html>
