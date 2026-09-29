<?php
echo "Debug: PHP is working\n";
echo "Session ID: " . session_id() . "\n";
require_once __DIR__ . '/../../backend/config/database.php';
echo "Database connection: " . (getConnection() ? "OK" : "Failed") . "\n";
?>