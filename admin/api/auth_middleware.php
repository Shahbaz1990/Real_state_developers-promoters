<?php
/**
 * api/auth_middleware.php
 * Include this (after bootstrap.php) on any API endpoint that requires login,
 * e.g. api/get-profile.php, api/logout.php, api/menu-list.php, etc.
 * Sets $current_user_id if the token is valid, otherwise sends a 401 response.
 */

$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';

if(!$authHeader || stripos($authHeader, 'Bearer ') !== 0){
    send_response(false, "Missing or invalid Authorization header.", null, 401);
}

$token = trim(substr($authHeader, 7));

$stmt = $conn->prepare("SELECT user_id, expires_at FROM api_tokens WHERE token=?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0){
    send_response(false, "Invalid token.", null, 401);
}

$row = $result->fetch_assoc();
$stmt->close();

if(strtotime($row['expires_at']) < time()){
    send_response(false, "Token has expired. Please log in again.", null, 401);
}

$current_user_id = $row['user_id'];
