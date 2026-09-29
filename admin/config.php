<?php
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');

$conn = new mysqli("localhost", "u424007618_Developers", "Tatitodevelopers@123", "u424007618_Developers");
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>