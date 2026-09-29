<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../includes/security.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    send_response(false, "Only POST requests are allowed.", null, 405);
}

$input = get_json_input();

$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';

if($username === '' || $password === ''){
    send_response(false, "username and password are required.", null, 422);
}

// Brute-force protection: max 5 failed attempts per account+IP per 10 minutes
$throttleKey = md5('api_login_' . strtolower($username) . '/' . ($_SERVER['REMOTE_ADDR'] ?? ''));
if(throttle_failures($throttleKey) >= 5){
    send_response(false, "Too many failed login attempts. Please try again in a few minutes.", null, 429);
}

$stmt = $conn->prepare("SELECT id, fullname, username, password FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows !== 1){
    throttle_record_failure($throttleKey);
    send_response(false, "Invalid username or password.", null, 401);
}

$user = $result->fetch_assoc();
$stmt->close();

if(!password_verify($password, $user['password'])){
    throttle_record_failure($throttleKey);
    send_response(false, "Invalid username or password.", null, 401);
}

throttle_clear($throttleKey);

// Generate a token valid for 30 days
$token      = bin2hex(random_bytes(32));
$expires_at = date("Y-m-d H:i:s", strtotime("+30 days"));

$stmt = $conn->prepare("INSERT INTO api_tokens(user_id, token, expires_at) VALUES(?,?,?)");
$stmt->bind_param("iss", $user['id'], $token, $expires_at);
$stmt->execute();
$stmt->close();

send_response(true, "Login successful.", [
    'token'    => $token,
    'expires'  => $expires_at,
    'user'     => [
        'id'       => $user['id'],
        'fullname' => $user['fullname'],
        'username' => $user['username'],
    ],
]);
