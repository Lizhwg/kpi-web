<?php
/**
 * Fix Password - Update tất cả user password thành password123 (hash bcrypt)
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load .env
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

// Connect to database
$host = $_ENV['DB_HOST'] ?? 'localhost';
$db = $_ENV['DB_NAME'] ?? 'kpi_system';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}

// Generate correct bcrypt hash for "password123"
$password = "password123";
$correctHash = password_hash($password, PASSWORD_BCRYPT);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Fix Password</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; text-align: center; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #2196F3; }
        .success { background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #4CAF50; color: #2e7d32; }
        .error { background: #ffebee; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #f44336; color: #c62828; }
        .hash-box { background: #f5f5f5; padding: 15px; border-radius: 5px; word-break: break-all; font-family: monospace; font-size: 12px; margin: 15px 0; }
        button { background: #2196F3; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #1976D2; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table td { padding: 10px; border-bottom: 1px solid #ddd; }
        table td:first-child { font-weight: bold; color: #1976D2; }
        .button-group { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>

<div class="container">
    <h1>🔐 Fix Password</h1>
    
    <div class="info">
        <strong>Password mặc định:</strong> <code>password123</code>
    </div>

    <h2>Hash Bcrypt (Chính xác):</h2>
    <div class="hash-box"><?php echo htmlspecialchars($correctHash); ?></div>

    <h2>Bước 1: Update Database</h2>
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
        // Update all users with new hash
        $query = "UPDATE User SET password = ? WHERE is_deleted = 0";
        $stmt = $pdo->prepare($query);
        
        try {
            $stmt->execute([$correctHash]);
            $rowCount = $stmt->rowCount();
            
            echo "<div class='success'>";
            echo "✓ <strong>Cập nhật thành công!</strong><br>";
            echo "Đã update $rowCount user với password mới (password123)";
            echo "</div>";
        } catch (PDOException $e) {
            echo "<div class='error'>";
            echo "✗ <strong>Cập nhật thất bại:</strong><br>";
            echo htmlspecialchars($e->getMessage());
            echo "</div>";
        }
    }
    ?>

    <form method="POST">
        <input type="hidden" name="action" value="update">
        <button type="submit" onclick="return confirm('Bạn chắc chắn muốn cập nhật tất cả password?')">
            ✓ Cập nhật Password cho Tất cả User
        </button>
    </form>

    <h2>Bước 2: Kiểm tra User</h2>
    
    <?php
    // Get all users
    $query = "SELECT id, username, role FROM User WHERE is_deleted = 0 ORDER BY role, id";
    $stmt = $pdo->query($query);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $roles = [
        1 => 'Admin',
        2 => 'Thư ký',
        3 => 'PM',
        4 => 'Trưởng phòng',
        5 => 'HRBP',
        6 => 'Nhân viên'
    ];
    ?>

    <table>
        <tr style="background: #f5f5f5;">
            <td>Username</td>
            <td>Vai trò</td>
        </tr>
        <?php foreach ($users as $u) : ?>
        <tr>
            <td><?php echo htmlspecialchars($u['username']); ?></td>
            <td><?php echo $roles[$u['role']] ?? 'Unknown'; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>Bước 3: Đăng nhập</h2>
    
    <div class="info">
        <strong>Tài khoản test được khuyến nghị:</strong><br>
        - <strong>Username:</strong> <code>secretary_hr</code><br>
        - <strong>Password:</strong> <code>password123</code><br>
        <br>
        Hoặc các tài khoản khác: <code>admin</code>, <code>pm_leader</code>, <code>dev_lead</code>, v.v...
    </div>

    <div class="button-group">
        <a href="/KPI/KPI/kpi-web/login" style="background: #4CAF50; color: white; padding: 12px 30px; border-radius: 5px; text-decoration: none; display: inline-block;">
            → Đến Trang Đăng nhập
        </a>
    </div>

    <h2>Verify Hash (Test):</h2>
    <?php
    // Test verify
    $testPassword = "password123";
    $isValid = password_verify($testPassword, $correctHash);
    
    if ($isValid) {
        echo "<div class='success'>✓ Hash verification PASSED - Password123 khớp với hash</div>";
    } else {
        echo "<div class='error'>✗ Hash verification FAILED - Có vấn đề với hash</div>";
    }
    ?>

</div>

</body>
</html>
