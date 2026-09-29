<?php
// upload-brochure.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check authentication
$isLoggedIn = isset($_SESSION['user_id']) || isset($_SESSION['user_email']);
if (!$isLoggedIn) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in.']);
    exit;
}

// Include config
require_once __DIR__ . '/includes/config.php';

$project_id = isset($_POST['project_id']) ? (int)$_POST['project_id'] : 0;
if (!$project_id) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid project ID.']);
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['brochure']) || $_FILES['brochure']['error'] !== UPLOAD_ERR_OK) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Please select a PDF file.']);
    exit;
}

$file = $_FILES['brochure'];
$fileType = mime_content_type($file['tmp_name']);

// Validate file type
if ($fileType !== 'application/pdf') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Only PDF files are allowed.']);
    exit;
}

// Validate file size (max 10MB)
if ($file['size'] > 10 * 1024 * 1024) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'File size must be less than 10MB.']);
    exit;
}

// Generate unique filename
$uploadDir = __DIR__ . '/../admin/uploads/brochures/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$filename = 'brochure_' . $project_id . '_' . time() . '.pdf';
$uploadPath = $uploadDir . $filename;

// Save file
if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Failed to upload file.']);
    exit;
}

// Update database
$stmt = $conn->prepare("UPDATE trending_projects SET brochure = ? WHERE id = ?");
$stmt->bind_param('si', $filename, $project_id);

if ($stmt->execute()) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Brochure uploaded successfully.']);
} else {
    // Delete uploaded file if database update fails
    unlink($uploadPath);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database update failed.']);
}

$stmt->close();
$conn->close();
?>