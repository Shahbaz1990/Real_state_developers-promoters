<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/auth_middleware.php';

// If we reach this line, auth_middleware.php already confirmed the token
// is valid and set $current_user_id for us.

$stmt = $conn->prepare("SELECT id, fullname, username FROM users WHERE id=?");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0){
    send_response(false, "User not found.", null, 404);
}

$user = $result->fetch_assoc();
send_response(true, "Profile fetched successfully.", $user);