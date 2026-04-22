<?php
session_start();
header("Content-Type: text/plain");

echo "=== SESSION DEBUG ===\n";
echo "Session ID: " . session_id() . "\n";
echo "User ID: " . ($_SESSION["user_id"] ?? "NOT SET") . "\n";
echo "Username: " . ($_SESSION["username"] ?? "NOT SET") . "\n";
echo "User Role: " . ($_SESSION["user_role"] ?? "NOT SET") . "\n";
echo "Business Unit ID: " . ($_SESSION["business_unit_id"] ?? "NOT SET") . "\n";
echo "Business Unit Name: " . ($_SESSION["business_unit_name"] ?? "NOT SET") . "\n";
echo "Is Logged In: " . (isset($_SESSION["user_id"]) ? "YES" : "NO") . "\n";

echo "\n=== ALL SESSION DATA ===\n";
print_r($_SESSION);
?>
