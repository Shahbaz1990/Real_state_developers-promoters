<?php
require_once __DIR__ . '/includes/config.php';
header('Content-Type: application/json');

// ============================================================
// SUBMIT PROPERTY ENQUIRY - Handles site visit form submissions
// ============================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get form data
    $page_id = isset($_POST['page_id']) ? (int) $_POST['page_id'] : 0;
    $page_title = isset($_POST['page_title']) ? trim($_POST['page_title']) : '';
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $appointment_date = !empty($_POST['appointment_date']) ? trim($_POST['appointment_date']) : date('Y-m-d', strtotime('+2 days'));

    // Validate required fields
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Please enter your full name.";
    }
    
    $cleanPhone = preg_replace('/[^0-9]/', '', $contact);
    if (empty($cleanPhone) || strlen($cleanPhone) < 10) {
        $errors[] = "Please enter a valid 10-digit contact number.";
    }
    
    if (empty($email)) {
        $errors[] = "Please enter your email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if (empty($description)) {
        $description = "Interested in site visit & details for " . ($page_title ?: "Property");
    }

    // If there are errors, return them
    if (!empty($errors)) {
        echo json_encode([
            'status' => 'error',
            'message' => implode(' ', $errors)
        ]);
        exit;
    }

    // Prepare and execute insert
    $stmt = $conn->prepare("INSERT INTO property_contact_enquiries 
        (page_id, page_title, name, email, contact, description, appointment_date, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    
    if ($stmt) {
        $stmt->bind_param("issssss", $page_id, $page_title, $name, $email, $contact, $description, $appointment_date);
        
        if ($stmt->execute()) {
            $_SESSION['user_email'] = $email;
            echo json_encode([
                'status' => 'success',
                'message' => 'Your site visit request has been submitted successfully! Our team will contact you shortly.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to save your request. Please try again.'
            ]);
        }
        $stmt->close();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error. Please try again later.'
        ]);
    }
    
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}
?>