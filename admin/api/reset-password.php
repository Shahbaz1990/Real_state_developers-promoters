<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../includes/security.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    send_response(false, "Only POST requests are allowed.", null, 405);
}

$input    = get_json_input();
$email    = trim($input['email'] ?? '');
$code     = trim($input['code'] ?? '');
$password = $input['password'] ?? '';

if($email === '' || $code === '' || $password === ''){
    send_response(false, "email, code and password are required.", null, 422);
}

if(strlen($password) < 6){
    send_response(false, "Password must be at least 6 characters long.", null, 422);
}

// Brute-force protection: max 5 attempts per account+IP per 10 minutes
$throttleKey = md5(strtolower($email) . '/' . ($_SERVER['REMOTE_ADDR'] ?? ''));
if(throttle_failures($throttleKey) >= 5){
    send_response(false, "Too many attempts. Please request a new reset code.", null, 429);
}

$stmt = $conn->prepare("SELECT id, reset_token, reset_expires FROM users WHERE username=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0){
    throttle_record_failure($throttleKey);
    send_response(false, "Invalid email or code.", null, 400);
}

$user = $result->fetch_assoc();
$stmt->close();

if(empty($user['reset_token']) || empty($user['reset_expires'])){
    throttle_record_failure($throttleKey);
    send_response(false, "No reset code was requested for this account.", null, 400);
}

if(strtotime($user['reset_expires']) < time()){
    throttle_record_failure($throttleKey);
    send_response(false, "This reset code has expired. Please request a new one.", null, 400);
}

if(!hash_equals((string) $user['reset_token'], $code)){
    throttle_record_failure($throttleKey);
    send_response(false, "Invalid email or code.", null, 400);
}

throttle_clear($throttleKey);

$hashed = password_hash($password, PASSWORD_DEFAULT);

$update = $conn->prepare("UPDATE users SET password=?, reset_token=NULL, reset_expires=NULL WHERE id=?");
$update->bind_param("si", $hashed, $user['id']);

if($update->execute()){
    send_response(true, "Password reset successful. You can now log in with your new password.");
}else{
    send_response(false, "Something went wrong. Please try again.", null, 500);
}