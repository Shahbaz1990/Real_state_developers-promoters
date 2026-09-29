<?php
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// Get form data
$company_name = isset($_POST['company_name']) ? trim($_POST['company_name']) : '';
$project_name = isset($_POST['project_name']) ? trim($_POST['project_name']) : '';
$location = isset($_POST['location']) ? trim($_POST['location']) : '';
$budget = isset($_POST['budget']) ? trim($_POST['budget']) : '';
$square_feet = isset($_POST['square_feet']) ? trim($_POST['square_feet']) : '';
$project_id = isset($_POST['page_id']) ? (int) $_POST['page_id'] : 0;

// Validate required fields
if (empty($company_name) || empty($project_name) || empty($location) || 
    empty($budget) || empty($square_feet)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

// Insert into database
$stmt = $conn->prepare("INSERT INTO site_visit_requests (company_name, project_name, location, budget, square_feet, project_id) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param('sssssi', $company_name, $project_name, $location, $budget, $square_feet, $project_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Your site visit request has been submitted successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to submit request. Please try again.']);
}

$stmt->close();
$conn->close();
?>