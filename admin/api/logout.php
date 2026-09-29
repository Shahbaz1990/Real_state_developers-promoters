<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth_middleware.php';

$headers = getallheaders();
$token   = trim(substr($headers['Authorization'], 7));

$stmt = $conn->prepare("DELETE FROM api_tokens WHERE token=?");
$stmt->bind_param("s", $token);

if($stmt->execute()){
    send_response(true, "Logged out successfully.");
}else{
    send_response(false, "Something went wrong.", null, 500);
}