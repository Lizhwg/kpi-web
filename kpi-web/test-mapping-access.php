<?php
/**
 * File test kiểm tra xem trang /mapping/review có hoạt động không
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Access Mapping Review</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .box { padding: 15px; border-radius: 4px; margin: 10px 0; }
        .success { background: #d4edda; }
        .error { background: #f8d7da; }
        .info { background: #e3f2fd; }
    </style>
</head>
<body>
    <h1>🔍 Test: Truy cập trang /mapping/review</h1>

    <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="box error">
            <h3>❌ Chưa đăng nhập</h3>
            <p>Không có session. <a href="/KPI/KPI/kpi-web/login">Hãy đăng nhập trước</a></p>
        </div>
    <?php else: ?>
        <div class="box success">
            <h3>✓ Đã đăng nhập</h3>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
            <p><strong>User ID:</strong> <?php echo $_SESSION['user_id']; ?></p>
            <p><strong>Role:</strong> <?php echo $_SESSION['user_role']; ?></p>
        </div>

        <?php if ($_SESSION['user_role'] != 2): ?>
            <div class="box error">
                <h3>❌ Role không phải Thư ký</h3>
                <p>Tài khoản này có role = <?php echo $_SESSION['user_role']; ?>, không phải role = 2 (Thư ký)</p>
                <p>Chỉ role 2 mới có thể truy cập trang xét duyệt.</p>
            </div>
        <?php else: ?>
            <div class="box success">
                <h3>✓ Role = 2 (Thư ký)</h3>
                <p>Tài khoản này có quyền truy cập trang xét duyệt.</p>
                <p><a href="/KPI/KPI/kpi-web/mapping/review" style="font-size: 16px; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 4px; display: inline-block;">👉 Vào trang xét duyệt đánh giá</a></p>
            </div>

            <div class="box info">
                <h3>📋 Kiểm tra Database</h3>
                <p><a href="/KPI/KPI/kpi-web/test-database-accounts.php">👉 Xem danh sách tài khoản trong Database</a></p>
            </div>
        <?php endif; ?>

    <?php endif; ?>

    <div class="box info">
        <h3>🔧 Debug Information:</h3>
        <pre><?php 
            echo "Session ID: " . session_id() . "\n";
            echo "Session Status: " . (session_status() === PHP_SESSION_NONE ? 'NONE' : 'ACTIVE') . "\n";
            echo "Session Name: " . session_name() . "\n";
            echo "\nCookie Status: ";
            if (isset($_COOKIE[session_name()])) {
                echo "✓ Cookie found\n";
            } else {
                echo "❌ Cookie not found\n";
            }
        ?></pre>
    </div>

</body>
</html>
