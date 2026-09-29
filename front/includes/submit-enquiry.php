<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request."
    ]);
    exit;
}

$full_name = trim($data['full_name'] ?? '');
$mobile_number = trim($data['mobile_number'] ?? '');
$email = trim($data['email'] ?? '');
$property_type = trim($data['property_type'] ?? '');
$bhk_type = trim($data['bhk_type'] ?? '');
$budget_range = trim($data['budget_range'] ?? '');
$preferred_locality = trim($data['preferred_locality'] ?? '');
$move_timeline = trim($data['move_timeline'] ?? '');
$additional_info = trim($data['additional_info'] ?? '');
$consent = isset($data['consent']) ? (int)$data['consent'] : 0;
$enquiry_type = trim($data['enquiry_type'] ?? 'Enquiry');
$source_page = trim($data['source_page'] ?? '');

if ($full_name == '' || $mobile_number == '') {

    echo json_encode([
        "success" => false,
        "message" => "Full Name and Mobile Number are required."
    ]);
    exit;
}

$sql = "INSERT INTO enquiries
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
)";

$stmt = $conn->prepare($sql);

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

if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Thank you! Your enquiry has been submitted successfully."
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Database Error : " . $stmt->error
    ]);

}

$stmt->close();
$conn->close();