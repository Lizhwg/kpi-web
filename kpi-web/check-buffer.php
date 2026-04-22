<?php
/**
 * Check output buffer status
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: text/plain');

echo "=== OUTPUT BUFFER STATUS ===\n\n";
echo "ob_get_level(): " . ob_get_level() . "\n";
echo "ob_get_status(): \n";
var_dump(ob_get_status());

echo "\nOutput Buffer Contents Length: " . strlen(ob_get_contents()) . " bytes\n";

echo "\nHeaders List:\n";
$headers = headers_list();
foreach ($headers as $header) {
    echo "  " . $header . "\n";
}

echo "\nHeaders Sent: " . (headers_sent() ? 'YES' : 'NO') . "\n";

echo "\nChecking index.php for ob_start():\n";
$indexFile = __DIR__ . '/index.php';
if (file_exists($indexFile)) {
    $content = file_get_contents($indexFile);
    if (strpos($content, 'ob_start()') !== false) {
        echo "✓ ob_start() found in index.php\n";
    } else {
        echo "✗ ob_start() NOT found in index.php\n";
    }
} else {
    echo "✗ index.php not found\n";
}

?>
