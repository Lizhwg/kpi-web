<?php
/**
 * File Debug - Kiểm tra tại sao không redirect sau đăng nhập
 */

// Log request
error_log("=== DEBUG LOGIN ===");
error_log("REQUEST URI: " . $_SERVER['REQUEST_URI']);
error_log("REQUEST METHOD: " . $_SERVER['REQUEST_METHOD']);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_log("SESSION EXISTS: " . (isset($_SESSION['user_id']) ? 'YES' : 'NO'));
if (isset($_SESSION['user_id'])) {
    error_log("USER_ID: " . $_SESSION['user_id']);
    error_log("USER_ROLE: " . $_SESSION['user_role']);
    error_log("USERNAME: " . $_SESSION['username']);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug - Kiểm tra Redirect Login</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #333; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 4px; background: #fafafa; }
        .success { background: #d4edda; border-color: #c3e6cb; }
        .error { background: #f8d7da; border-color: #f5c6cb; }
        .info { background: #e3f2fd; border-color: #bbdefb; }
        code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
        button { padding: 10px 20px; margin: 5px; background: #667eea; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #764ba2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Debug - Kiểm tra Redirect sau Đăng nhập</h1>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <!-- Chưa đăng nhập -->
            <div class="section error">
                <h2>❌ Chưa đăng nhập</h2>
                <p>Vui lòng <a href="/KPI/KPI/kpi-web/login">đăng nhập</a> trước</p>
            </div>

        <?php else: ?>
            <!-- Đã đăng nhập -->
            <div class="section success">
                <h2>✓ Đã đăng nhập</h2>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                <p><strong>User ID:</strong> <?php echo $_SESSION['user_id']; ?></p>
                <p><strong>Role:</strong> <?php echo $_SESSION['user_role']; ?></p>
            </div>

            <?php if ($_SESSION['user_role'] == 2): ?>
                <!-- Role = 2 (Thư ký) -->
                <div class="section success">
                    <h2>✓ Role = 2 (Thư ký)</h2>
                    <p>Tài khoản này sẽ được chuyển hướng đến: <code>/KPI/KPI/kpi-web/mapping/review</code></p>

                    <h3>🔧 Thử các bước sau:</h3>
                    <ol>
                        <li>
                            <strong>Cách 1: Click vào link để test</strong>
                            <br><a href="/KPI/KPI/kpi-web/mapping/review" style="padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 4px; display: inline-block;">→ Vào trang mapping/review</a>
                            <br><small>Nếu được vào bình thường → vấn đề nằm ở redirect trong handleLogin()</small>
                        </li>
                        <li>
                            <strong>Cách 2: Test redirect bằng PHP</strong>
                            <br><button onclick="testRedirect()">Test Redirect</button>
                            <div id="redirectResult" style="margin-top: 10px;"></div>
                        </li>
                        <li>
                            <strong>Cách 3: Check xem có output trước header không</strong>
                            <br><button onclick="checkBuffer()">Check Output Buffer</button>
                            <div id="bufferResult" style="margin-top: 10px;"></div>
                        </li>
                        <li>
                            <strong>Cách 4: Test đăng nhập lại</strong>
                            <br><a href="/KPI/KPI/kpi-web/logout">Logout</a> → <a href="/KPI/KPI/kpi-web/login">Login lại</a>
                            <br><small>Kiểm tra xem có bị stuck ở trang login không</small>
                        </li>
                    </ol>
                </div>

                <!-- Test Redirect Script -->
                <script>
                    function testRedirect() {
                        const resultDiv = document.getElementById('redirectResult');
                        resultDiv.innerHTML = '<p style="color: #666;">Testing redirect...</p>';
                        
                        fetch('/KPI/KPI/kpi-web/mapping/review', {
                            method: 'GET',
                            credentials: 'include'
                        })
                        .then(response => {
                            resultDiv.innerHTML = `
                                <div style="background: #e3f2fd; padding: 10px; border-radius: 4px;">
                                    <p><strong>Status:</strong> ${response.status}</p>
                                    <p><strong>URL:</strong> ${response.url}</p>
                                    <p><strong>Redirected:</strong> ${response.redirected ? 'Yes' : 'No'}</p>
                                    ${response.status === 200 ? '<p style="color: green;">✓ Can access /mapping/review</p>' : '<p style="color: red;">✗ Cannot access /mapping/review</p>'}
                                </div>
                            `;
                        })
                        .catch(error => {
                            resultDiv.innerHTML = '<p style="color: red;">Error: ' + error.message + '</p>';
                        });
                    }

                    function checkBuffer() {
                        const resultDiv = document.getElementById('bufferResult');
                        resultDiv.innerHTML = '<p style="color: #666;">Checking output buffer...</p>';
                        
                        fetch('/KPI/KPI/kpi-web/check-buffer.php')
                        .then(r => r.text())
                        .then(text => {
                            resultDiv.innerHTML = '<pre>' + text + '</pre>';
                        })
                        .catch(e => {
                            resultDiv.innerHTML = '<p style="color: red;">Error: ' + e.message + '</p>';
                        });
                    }
                </script>

            <?php else: ?>
                <!-- Role khác 2 -->
                <div class="section info">
                    <h2>⚠️ Role không phải Thư ký</h2>
                    <p>Role hiện tại: <strong><?php echo $_SESSION['user_role']; ?></strong></p>
                    <p>Sẽ được chuyển hướng đến: <code>/KPI/KPI/kpi-web/dashboard</code></p>
                    <p><a href="/KPI/KPI/kpi-web/dashboard">→ Vào Dashboard</a></p>
                </div>
            <?php endif; ?>

        <?php endif; ?>

        <!-- Session Debug Info -->
        <div class="section info">
            <h2>🔧 Session Debug Info:</h2>
            <pre><?php
                echo "Session ID: " . session_id() . "\n";
                echo "Session Name: " . session_name() . "\n";
                echo "Session Status: " . (session_status() === PHP_SESSION_NONE ? 'NONE' : 'ACTIVE') . "\n";
                echo "Headers Sent: " . (headers_sent() ? 'YES' : 'NO') . "\n";
                echo "\nAll Session Data:\n";
                var_dump($_SESSION);
            ?></pre>
        </div>

        <!-- File Check -->
        <div class="section info">
            <h2>🗂️ File Check:</h2>
            <pre><?php
                $files = [
                    'index.php' => '/KPI/KPI/kpi-web/index.php',
                    'routes/web.php' => '/KPI/KPI/kpi-web/routes/web.php',
                    'app/Controllers/AuthController.php' => '/KPI/KPI/kpi-web/app/Controllers/AuthController.php',
                    'app/Controllers/MappingController.php' => '/KPI/KPI/kpi-web/app/Controllers/MappingController.php'
                ];
                
                $baseDir = __DIR__;
                foreach ($files as $name => $path) {
                    $file = $baseDir . '/' . $name;
                    $exists = file_exists($file) ? '✓' : '✗';
                    echo "$exists $name\n";
                }
            ?></pre>
        </div>

    </div>
</body>
</html>
