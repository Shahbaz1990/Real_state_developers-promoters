<?php
header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']) || isset($_SESSION['user_email']);

if (!$isLoggedIn) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be logged in to submit a report.'
    ]);
    exit;
}

// Get form data
$project_id = isset($_POST['project_id']) ? (int)$_POST['project_id'] : 0;
$project_name = isset($_POST['project_name']) ? trim($_POST['project_name']) : '';
$report_reason = isset($_POST['report_reason']) ? trim($_POST['report_reason']) : '';
$report_details = isset($_POST['report_details']) ? trim($_POST['report_details']) : '';

// Validate
if (empty($report_reason)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please select a reason for reporting.'
    ]);
    exit;
}

if ($project_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid project ID.'
    ]);
    exit;
}

// TODO: Save the report to your database
// Example:
// $stmt = $conn->prepare("INSERT INTO reports (project_id, project_name, reason, details, user_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
// $stmt->bind_param('isssi', $project_id, $project_name, $report_reason, $report_details, $_SESSION['user_id']);
// $stmt->execute();

// For demo purposes, log the report
error_log("Report submitted - Project: $project_name ($project_id), Reason: $report_reason, Details: $report_details");

echo json_encode([
    'status' => 'success',
    'message' => 'Thank you for your report. Our team will review it shortly.'
]);
exit;
?>