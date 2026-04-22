<?php
/**
 * File debug để kiểm tra session sau khi đăng nhập
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Login Session</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .success { background: #d4edda; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .error { background: #f8d7da; padding: 15px; border-radius: 4px; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 Debug: Kiểm tra Session</h1>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="success">
            <h3>✓ Session đã được set</h3>
            <pre><?php 
                echo "Session ID: " . session_id() . "\n";
                echo "User ID: " . $_SESSION['user_id'] . "\n";
                echo "Username: " . $_SESSION['username'] . "\n";
                echo "Role: " . $_SESSION['user_role'] . "\n";
                echo "Business Unit ID: " . $_SESSION['business_unit_id'] . "\n";
            ?></pre>
            
            <?php if ($_SESSION['user_role'] == 2): ?>
                <div class="success">
                    <p>✓ Role = 2 (Thư ký) - Sẽ chuyển hướng đến: <strong>/KPI/KPI/kpi-web/mapping/review</strong></p>
                    <p><a href="/KPI/KPI/kpi-web/mapping/review">👉 Nhấn để vào trang xét duyệt</a></p>
                </div>
            <?php else: ?>
                <div class="info">
                    <p>Role = <?php echo $_SESSION['user_role']; ?> (Không phải Thư ký)</p>
                    <p>Sẽ chuyển hướng đến: <strong>/KPI/KPI/kpi-web/dashboard</strong></p>
                    <p><a href="/KPI/KPI/kpi-web/dashboard">👉 Nhấn để vào trang dashboard</a></p>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="error">
            <h3>✗ Chưa đăng nhập</h3>
            <p>Session chưa có user_id. Vui lòng <a href="/KPI/KPI/kpi-web/login">đăng nhập</a> trước.</p>
        </div>
    <?php endif; ?>

    <div class="info">
        <h3>Thông tin Session toàn bộ:</h3>
        <pre><?php var_dump($_SESSION); ?></pre>
    </div>

</body>
</html>
