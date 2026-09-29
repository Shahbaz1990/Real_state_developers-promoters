<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_id']) || isset($_SESSION['user_email']);
if (!$isLoggedIn) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in.']);
    exit;
}

require_once __DIR__ . '/includes/config.php';

$project_id = isset($_POST['project_id']) ? (int)$_POST['project_id'] : 0;
if (!$project_id) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid project ID.']);
    exit;
}

if (!isset($_FILES['main_image']) || $_FILES['main_image']['error'] !== UPLOAD_ERR_OK) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Please select an image.']);
    exit;
}

$file = $_FILES['main_image'];
$fileType = mime_content_type($file['tmp_name']);
if (!in_array($fileType, ['image/jpeg', 'image/png', 'image/webp'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Only JPG, PNG, and WEBP images are allowed.']);
    exit;
}

$uploadDir = __DIR__ . '/../admin/uploads/trending-projects/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'main_' . $project_id . '_' . time() . '.' . $extension;
$uploadPath = $uploadDir . $filename;

if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
    $stmt = $conn->prepare("UPDATE trending_projects SET image = ? WHERE id = ?");
    $stmt->bind_param('si', $filename, $project_id);
    if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Main image updated successfully.']);
    } else {
        unlink($uploadPath);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Database update failed.']);
    }
    $stmt->close();
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'File upload failed.']);
}
$conn->close();
?>