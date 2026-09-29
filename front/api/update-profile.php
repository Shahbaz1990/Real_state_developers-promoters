<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../config/database.php';
$conn = getConnection();

$user_id = $_SESSION['user_id'];
$fullname = $_POST['fullname'] ?? '';
$mobile = $_POST['mobile'] ?? '';
$password = $_POST['password'] ?? '';

$updates = [];
$params = [];

if ($fullname) {
    $updates[] = "fullname = ?";
    $params[] = $fullname;
}

if ($mobile) {
    $updates[] = "mobile_number = ?";
    $params[] = $mobile;
}

if (!empty($password) && strlen($password) >= 6) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $updates[] = "password = ?";
    $params[] = $hashed;
}

// Handle profile image upload
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = __DIR__ . '/../uploads/profile/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    
    $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
    $filename = 'user_' . $user_id . '_' . time() . '.' . $ext;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $filepath)) {
        $updates[] = "profile_image = ?";
        $params[] = $filename;
    }
}

if (empty($updates)) {
    echo json_encode(['success' => false, 'message' => 'No changes to update']);
    exit;
}

$params[] = $user_id;
$sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat('s', count($updates)) . 'i', ...$params);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Profile updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed: ' . $conn->error]);
}
$stmt->close();
?>