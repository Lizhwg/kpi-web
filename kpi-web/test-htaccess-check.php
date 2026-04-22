<?php
// Nếu URL là /KPI/KPI/kpi-web/test-htaccess-check mà file này được load, thì .htaccess hoạt động

$uri = $_SERVER['REQUEST_URI'];
$script = $_SERVER['SCRIPT_NAME'];

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>.htaccess Test</title>
    <style>
        body { font-family: monospace; background: #1e1e1e; color: #0f0; padding: 20px; }
        .success { color: #0f0; }
        .error { color: #f00; }
    </style>
</head>
<body>
<h1>.htaccess Rewrite Test</h1>

<p>REQUEST_URI: <span class="success"><?php echo htmlspecialchars($uri); ?></span></p>
<p>SCRIPT_NAME: <span class="success"><?php echo htmlspecialchars($script); ?></span></p>

<?php
if ($script === '/KPI/KPI/kpi-web/index.php') {
    echo "<p class='success'>✓ .htaccess IS REWRITING requests to index.php</p>";
} else {
    echo "<p class='error'>✗ .htaccess might NOT be rewriting (script = $script)</p>";
}
?>

<hr>

<h2>Test Instructions:</h2>
<ol>
    <li>Open: <a href="/KPI/KPI/kpi-web/test-htaccess-check">http://localhost/KPI/KPI/kpi-web/test-htaccess-check</a></li>
    <li>If SCRIPT_NAME shows /KPI/KPI/kpi-web/index.php → .htaccess works ✓</li>
    <li>If SCRIPT_NAME shows /KPI/KPI/kpi-web/test-htaccess-check → .htaccess NOT working ✗</li>
</ol>

</body>
</html>
