<?php
require_once __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_response(false, "Only POST requests are allowed.", null, 405);
}

$input = get_json_input();

$full_name          = trim($input['full_name'] ?? '');
$mobile_number      = trim($input['mobile_number'] ?? '');
$email              = trim($input['email'] ?? '');
$property_type      = trim($input['property_type'] ?? '');
$bhk_type           = trim($input['bhk_type'] ?? '');
$budget_range       = trim($input['budget_range'] ?? '');
$preferred_locality = trim($input['preferred_locality'] ?? '');
$move_timeline      = trim($input['move_timeline'] ?? '');
$additional_info    = trim($input['additional_info'] ?? '');
$consent            = isset($input['consent']) ? (int)$input['consent'] : 0;
$enquiry_type       = trim($input['enquiry_type'] ?? 'Enquiry');
$source_page        = trim($input['source_page'] ?? '');

if ($full_name === '' || $mobile_number === '') {
    send_response(false, "Full Name and Mobile Number are required.", null, 422);
}

if (!preg_match('/^[0-9]{10}$/', $mobile_number)) {
    send_response(false, "Please enter a valid 10-digit mobile number.", null, 422);
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    send_response(false, "Please enter a valid email address.", null, 422);
}

$stmt = $conn->prepare("
    INSERT INTO enquiries
    (
        full_name,
        mobile_number,
        email,
        property_type,
        bhk_type,
        budget_range,
        preferred_locality,
        move_timeline,
        additional_info,
        consent,
        enquiry_type,
        source_page
    )
    VALUES
    (
        ?,?,?,?,?,?,?,?,?,?,?,?
    )
");

$stmt->bind_param(
    "sssssssssiss",
    $full_name,
    $mobile_number,
    $email,
    $property_type,
    $bhk_type,
    $budget_range,
    $preferred_locality,
    $move_timeline,
    $additional_info,
    $consent,
    $enquiry_type,
    $source_page
);

if (!$stmt->execute()) {
    send_response(false, "Failed to submit enquiry. Please try again.", null, 500);
}

$enquiry_id = $stmt->insert_id;

$stmt->close();

send_response(true, "Enquiry submitted successfully.", [
    "enquiry" => [
        "id"                 => $enquiry_id,
        "full_name"          => $full_name,
        "mobile_number"      => $mobile_number,
        "email"              => $email,
        "property_type"      => $property_type,
        "bhk_type"           => $bhk_type,
        "budget_range"       => $budget_range,
        "preferred_locality" => $preferred_locality,
        "move_timeline"      => $move_timeline,
        "additional_info"    => $additional_info,
        "consent"            => $consent,
        "enquiry_type"       => $enquiry_type,
        "source_page"        => $source_page
    ]
]);