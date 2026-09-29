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

if (!isset($_FILES['gallery_images']) || empty($_FILES['gallery_images']['name'][0])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'No images selected.']);
    exit;
}

$uploadDir = __DIR__ . '/../admin/uploads/trending-projects/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$uploaded = [];
$errors = [];

foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmpName) {
    if ($_FILES['gallery_images']['error'][$key] !== UPLOAD_ERR_OK) continue;
    
    $fileType = mime_content_type($tmpName);
    if (!in_array($fileType, ['image/jpeg', 'image/png', 'image/webp'])) {
        $errors[] = $_FILES['gallery_images']['name'][$key] . ' (invalid format)';
        continue;
    }
    
    $extension = pathinfo($_FILES['gallery_images']['name'][$key], PATHINFO_EXTENSION);
    $filename = 'gallery_' . $project_id . '_' . time() . '_' . $key . '.' . $extension;
    $uploadPath = $uploadDir . $filename;
    
    if (move_uploaded_file($tmpName, $uploadPath)) {
        $uploaded[] = $filename;
    } else {
        $errors[] = $_FILES['gallery_images']['name'][$key] . ' (upload failed)';
    }
}

if (empty($uploaded)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'No images uploaded. Errors: ' . implode(', ', $errors)]);
    exit;
}

// Save to database
$successCount = 0;
foreach ($uploaded as $filename) {
    $stmt = $conn->prepare("INSERT INTO trending_project_images (project_id, image, image_order) VALUES (?, ?, ?)");
    $order = $conn->query("SELECT COUNT(*) as count FROM trending_project_images WHERE project_id = $project_id")->fetch_assoc()['count'] + 1;
    $stmt->bind_param('isi', $project_id, $filename, $order);
    if ($stmt->execute()) $successCount++;
    $stmt->close();
}

header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'message' => $successCount . ' image(s) uploaded successfully.',
    'uploaded' => $uploaded,
    'errors' => $errors
]);
$conn->close();
?>