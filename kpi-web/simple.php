<?php
// Nếu file này được gọi thì .htaccess đang hoạt động
if ($_SERVER['REQUEST_URI'] === '/KPI/KPI/kpi-web/test-htaccess' || 
    $_SERVER['REQUEST_URI'] === '/KPI/KPI/kpi-web/test-htaccess/') {
    echo "✓ .htaccess rewrite đang hoạt động!<br>";
    echo "URL: " . $_SERVER['REQUEST_URI'] . "<br>";
    echo "Script: " . $_SERVER['SCRIPT_NAME'] . "<br>";
} else {
    echo "File: simple.php<br>";
    echo "URL: " . $_SERVER['REQUEST_URI'] . "<br>";
}
?>
