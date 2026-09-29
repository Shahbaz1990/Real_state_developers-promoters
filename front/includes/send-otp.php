<?php
/**
 * POST /backend/api/send-otp.php
 * Body (JSON): { "email": "...", "purpose": "register" }
 *
 * Generates a 6-digit OTP, stores it (10-minute expiry), and emails it.
 * Uses the existing `users` table — email is stored in the `username`
 * column there, which is already UNIQUE.
 */
require_once __DIR__ . '/../../front/includes/config.php';
header('Content-Type: application/json');

$input   = json_decode(file_get_contents('php://input'), true) ?: [];
$email   = isset($input['email']) ? trim($input['email']) : '';
$purpose = isset($input['purpose']) && in_array($input['purpose'], ['register', 'login', 'reset_password'], true)
    ? $input['purpose']
    : 'register';

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

// For registration, block if this email (stored in `username`) is already taken
if ($purpose === 'register') {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $exists = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($exists) {
        echo json_encode(['success' => false, 'message' => 'An account with this email already exists. Please log in instead.']);
        exit;
    }
}

// Basic throttle — max 1 OTP request per 60 seconds per email+purpose
$stmt = $conn->prepare("SELECT created_at FROM otp_verifications WHERE email = ? AND purpose = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param('ss', $email, $purpose);
$stmt->execute();
$last = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($last && (time() - strtotime($last['created_at'])) < 60) {
    echo json_encode(['success' => false, 'message' => 'Please wait a minute before requesting another code.']);
    exit;
}

$otp       = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
$expiresAt = date('Y-m-d H:i:s', time() + 600); // 10 minutes

$stmt = $conn->prepare("INSERT INTO otp_verifications (email, otp_code, purpose, expires_at) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssss', $email, $otp, $purpose, $expiresAt);
$stmt->execute();
$stmt->close();

// ============ Send the email ============
// Uses PHP's built-in mail(). Many hosts block this or land it in spam —
// swap for PHPMailer + real SMTP creds if delivery is unreliable.
$siteName = $site['name'] ?? 'Tatito Developers';
$subject  = "Your $siteName verification code";
$message  = "Your OTP is: $otp\n\nThis code expires in 10 minutes. If you didn't request this, you can ignore this email.";
$headers  = "From: no-reply@" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n" .
            "Content-Type: text/plain; charset=UTF-8\r\n";

$sent = @mail($email, $subject, $message, $headers);

if (!$sent) {
    error_log("send-otp.php: mail() failed for $email");
}

echo json_encode(['success' => true, 'message' => 'A verification code has been sent to your email.']);
