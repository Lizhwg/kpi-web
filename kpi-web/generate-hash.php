<?php
/**
 * Tạo bcrypt hash cho password123
 */

$password = "password123";
$hash = password_hash($password, PASSWORD_BCRYPT);

echo "<!DOCTYPE html>";
echo "<html><head><meta charset='UTF-8'><style>body{font-family:monospace;background:#1e1e1e;color:#0f0;padding:20px}</style></head><body>";
echo "<h1>Bcrypt Hash Generator</h1>";
echo "<h2>Input Password:</h2>";
echo "<pre>$password</pre>";
echo "<h2>Generated Bcrypt Hash:</h2>";
echo "<pre style='background:#333;padding:10px;border-radius:5px;word-break:break-all'>$hash</pre>";
echo "<h2>Copy this hash to database!</h2>";
echo "<p style='color:#f00;font-weight:bold'>Hãy cập nhật tất cả user trong database với hash này</p>";
echo "<h2>SQL UPDATE:</h2>";
echo "<pre style='background:#333;padding:10px;border-radius:5px;color:#ff9'>
UPDATE User SET password = '$hash' WHERE username IN ('secretary_hr', 'admin', 'pm_leader', 'dev_lead');
</pre>";

// Verify
$verify = password_verify($password, $hash);
echo "<h2>Verify test:</h2>";
echo $verify ? "<p style='color:#0f0'>✓ Verification PASSED</p>" : "<p style='color:#f00'>✗ Verification FAILED</p>";

echo "</body></html>";
?>
