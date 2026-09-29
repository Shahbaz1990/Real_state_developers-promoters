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

$input = json_decode(file_get_contents('php://input'), true);
$project_id = isset($input['project_id']) ? (int)$input['project_id'] : 0;
$image = isset($input['image']) ? $input['image'] : '';

if (!$project_id || empty($image)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid data.']);
    exit;
}

// Delete from database
$stmt = $conn->prepare("DELETE FROM trending_project_images WHERE project_id = ? AND image = ?");
$stmt->bind_param('is', $project_id, $image);
if ($stmt->execute()) {
    // Delete file from server
    $filePath = __DIR__ . '/../admin/uploads/trending-projects/' . $image;
    if (file_exists($filePath)) {
        unlink($filePath);
    }
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Image deleted successfully.']);
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database deletion failed.']);
}
$stmt->close();
$conn->close();
?>