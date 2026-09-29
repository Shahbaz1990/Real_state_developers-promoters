<?php
require_once __DIR__ . '/bootstrap.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    send_response(false, "Only POST requests are allowed.", null, 405);
}

$input = get_json_input();

$fullname         = trim($input['fullname'] ?? '');
$username         = trim($input['username'] ?? ''); // this holds the email
$mobile_number    = trim($input['mobile_number'] ?? '');
$password         = $input['password'] ?? '';
$confirm_password = $input['confirm_password'] ?? '';
$role              = 'user'; // fixed server-side, never taken from request input

if($fullname === '' || $username === '' || $mobile_number === '' || $password === '' || $confirm_password === ''){
    send_response(false, "fullname, username, mobile_number, password, and confirm_password are all required.", null, 422);
}

if(!filter_var($username, FILTER_VALIDATE_EMAIL)){
    send_response(false, "Please provide a valid email address.", null, 422);
}

if(!preg_match('/^[0-9]{10}$/', $mobile_number)){
    send_response(false, "Please provide a valid 10-digit mobile number.", null, 422);
}

if(strlen($password) < 6){
    send_response(false, "Password must be at least 6 characters long.", null, 422);
}

if($password !== $confirm_password){
    send_response(false, "Password and confirm_password do not match.", null, 422);
}

// Check for existing username
$stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    send_response(false, "This email is already registered.", null, 409);
}
$stmt->close();

$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users(fullname, username, mobile_number, password, role) VALUES(?,?,?,?,?)");
$stmt->bind_param("sssss", $fullname, $username, $mobile_number, $hashed, $role);

if($stmt->execute()){
    send_response(true, "Registration successful.", ['id' => $stmt->insert_id], 201);
}else{
    send_response(false, "Something went wrong. Please try again.", null, 500);
}