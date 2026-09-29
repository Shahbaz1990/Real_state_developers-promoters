<?php
/**
 * POST /backend/api/verify-register.php
 * Body (JSON): { "fullname", "email", "mobile_number", "password", "otp" }
 *
 * Verifies the OTP, then creates the account in the existing `users` table
 * (email goes into `username`, which is UNIQUE) and logs the user in.
 */
session_start();
require_once __DIR__ . '/../../front/includes/config.php';
header('Content-Type: application/json');

$input        = json_decode(file_get_contents('php://input'), true) ?: [];
$fullname     = isset($input['fullname']) ? trim($input['fullname']) : '';
$email        = isset($input['email']) ? trim($input['email']) : '';
$mobileNumber = isset($input['mobile_number']) ? trim($input['mobile_number']) : '';
$password     = isset($input['password']) ? (string) $input['password'] : '';
$otp          = isset($input['otp']) ? trim($input['otp']) : '';

if ($fullname === '' || $email === '' || $password === '' || $otp === '' || $mobileNumber === '') {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}
if (!preg_match('/^\d{10}$/', $mobileNumber)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid 10-digit mobile number.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}
if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
    exit;
}

// Find a matching, unused, unexpired OTP for this email
$stmt = $conn->prepare("
    SELECT id FROM otp_verifications
    WHERE email = ? AND otp_code = ? AND purpose = 'register'
      AND is_used = 0 AND expires_at >= NOW()
    ORDER BY id DESC LIMIT 1
");
$stmt->bind_param('ss', $email, $otp);
$stmt->execute();
$otpRow = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$otpRow) {
    echo json_encode(['success' => false, 'message' => 'That code is invalid or has expired. Please request a new one.']);
    exit;
}

// Double-check the email (username) hasn't been registered in the meantime
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
$stmt->bind_param('s', $email);
$stmt->execute();
$existingUser = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($existingUser) {
    echo json_encode(['success' => false, 'message' => 'An account with this email already exists. Please log in instead.']);
    exit;
}

// Mark the OTP as used
$stmt = $conn->prepare("UPDATE otp_verifications SET is_used = 1 WHERE id = ?");
$stmt->bind_param('i', $otpRow['id']);
$stmt->execute();
$stmt->close();

// Create the account
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("
    INSERT INTO users (fullname, username, mobile_number, password, role, created_at)
    VALUES (?, ?, ?, ?, 'user', NOW())
");
$stmt->bind_param('ssss', $fullname, $email, $mobileNumber, $passwordHash);
$stmt->execute();
$userId = $stmt->insert_id;
$stmt->close();

// Log the new user in
$_SESSION['user_id']   = $userId;
$_SESSION['fullname']  = $fullname;
$_SESSION['username']  = $email;
$_SESSION['role']      = 'user';

echo json_encode([
    'success' => true,
    'message' => 'Account created successfully.',
    'user'    => ['id' => $userId, 'fullname' => $fullname, 'username' => $email],
]);
