<?php
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../includes/security.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    send_response(false, "Only POST requests are allowed.", null, 405);
}

$input = get_json_input();
$email = trim($input['email'] ?? '');

if($email === ''){
    send_response(false, "email is required.", null, 422);
}

// Throttle code generation: max 5 per account+IP per 10 minutes
$throttleKey = md5('pwreset_' . strtolower($email) . '/' . ($_SERVER['REMOTE_ADDR'] ?? ''));
if(throttle_failures($throttleKey) >= 5){
    send_response(false, "Too many reset requests. Please wait a while and try again.", null, 429);
}

$stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Always return the same success message whether or not the email exists,
// so this endpoint can't be used to check which emails are registered.
if($result->num_rows === 0){
    send_response(true, "If this email exists in our system, a reset code has been sent.");
}

$user = $result->fetch_assoc();
$stmt->close();

// Generate a 6-digit numeric code, valid for 10 minutes
$code    = str_pad((string) random_int(0, 999999), 6, "0", STR_PAD_LEFT);
$expires = date("Y-m-d H:i:s", strtotime("+10 minutes"));

$update = $conn->prepare("UPDATE users SET reset_token=?, reset_expires=? WHERE id=?");
$update->bind_param("ssi", $code, $expires, $user['id']);
$update->execute();
$update->close();
throttle_record_failure($throttleKey);

// Send this via email in production (e.g. PHPMailer + SMTP).
// mail() alone rarely works reliably on shared hosting without SMTP config.
$subject = "Password Reset Code";
$body    = "Your password reset code is: " . $code . "\n\nThis code expires in 10 minutes.\n\nIf you didn't request this, you can ignore this email.";
@mail($email, $subject, $body);

send_response(true, "If this email exists in our system, a reset code has been sent.");