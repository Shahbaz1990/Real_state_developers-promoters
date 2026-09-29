<?php
// delete-brochure.php
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

$input = json_decode(file_get_contents('php://input'), true);
$project_id = isset($input['project_id']) ? (int)$input['project_id'] : 0;

if (!$project_id) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid project ID.']);
    exit;
}

// Get current brochure filename
$stmt = $conn->prepare("SELECT brochure FROM trending_projects WHERE id = ?");
$stmt->bind_param('i', $project_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!$row || empty($row['brochure'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'No brochure found.']);
    exit;
}

// Delete file from server
$filePath = __DIR__ . '/../admin/uploads/brochures/' . $row['brochure'];
if (file_exists($filePath)) {
    unlink($filePath);
}

// Update database
$stmt = $conn->prepare("UPDATE trending_projects SET brochure = NULL WHERE id = ?");
$stmt->bind_param('i', $project_id);

if ($stmt->execute()) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Brochure removed successfully.']);
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database update failed.']);
}

$stmt->close();
$conn->close();
?>