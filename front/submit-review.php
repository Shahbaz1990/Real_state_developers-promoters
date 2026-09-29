<?php
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// Get and sanitize inputs
$project_id = isset($_POST['project_id']) ? (int) $_POST['project_id'] : 0;
$user_name = isset($_POST['user_name']) ? trim($_POST['user_name']) : '';
$user_email = isset($_POST['user_email']) ? trim($_POST['user_email']) : '';
$category = isset($_POST['category']) ? trim($_POST['category']) : '';
$rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 5;
$review_text = isset($_POST['review_text']) ? trim($_POST['review_text']) : '';

// Validate
if ($project_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid project ID.']);
    exit;
}

if (empty($user_name) || strlen($user_name) < 2) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter your full name.']);
    exit;
}

if (empty($category)) {
    echo json_encode(['status' => 'error', 'message' => 'Please select a category.']);
    exit;
}

if (empty($review_text) || strlen($review_text) < 10) {
    echo json_encode(['status' => 'error', 'message' => 'Please write a review (minimum 10 characters).']);
    exit;
}

if ($rating < 1 || $rating > 5) {
    $rating = 5;
}

// Validate email if provided
if (!empty($user_email) && !filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address.']);
    exit;
}

// Insert into database
$stmt = $conn->prepare("INSERT INTO project_reviews (project_id, user_name, user_email, category, rating, review_text, is_approved, created_at) VALUES (?, ?, ?, ?, ?, ?, 0, NOW())");
$stmt->bind_param('isssis', $project_id, $user_name, $user_email, $category, $rating, $review_text);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success', 
        'message' => 'Thank you for your review! It will be visible after moderation.',
        'review_id' => $stmt->insert_id
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>