<?php
/**
 * POST /backend/api/login.php
 * Body (JSON): { "email", "password" }
 *
 * Matches your existing `users` table: email is stored in `username`,
 * password is the bcrypt hash in `password`.
 */
session_start();
require_once __DIR__ . '/../../front/includes/config.php';
header('Content-Type: application/json');

$input    = json_decode(file_get_contents('php://input'), true) ?: [];
$email    = isset($input['email']) ? trim($input['email']) : '';
$password = isset($input['password']) ? (string) $input['password'] : '';

if ($email === '' || $password === '') {
    echo json_encode(['success' => false, 'message' => 'Please enter your email and password.']);
    exit;
}

$stmt = $conn->prepare("SELECT id, fullname, username, password, role FROM users WHERE username = ? LIMIT 1");
$stmt->bind_param('s', $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user || !password_verify($password, $user['password'])) {
    echo json_encode(['success' => false, 'message' => 'Incorrect email or password.']);
    exit;
}

$_SESSION['user_id']  = $user['id'];
$_SESSION['fullname'] = $user['fullname'];
$_SESSION['username'] = $user['username'];
$_SESSION['role']     = $user['role'];

echo json_encode([
    'success' => true,
    'message' => 'Logged in successfully.',
    'user'    => ['id' => $user['id'], 'fullname' => $user['fullname'], 'username' => $user['username'], 'role' => $user['role']],
]);
