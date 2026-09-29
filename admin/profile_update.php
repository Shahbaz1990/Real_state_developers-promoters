<?php
require_once __DIR__ . '/auth_check.php';

header('Content-Type: application/json');

if (!admin_session_ok()) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Session expired. Please log in again."]);
    exit();
}

if (!verify_csrf()) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Invalid or missing security token. Please refresh the page and try again."]);
    exit();
}

$id = $_SESSION["id"];

function respond($success, $message, $extra = []) {
    echo json_encode(array_merge(["success" => $success, "message" => $message], $extra));
    exit();
}

// ---- Collect + sanitize input ----
$first_name    = trim($_POST["first_name"] ?? '');
$last_name     = trim($_POST["last_name"] ?? '');
$mobile_number = trim($_POST["mobile_number"] ?? '');
$new_password  = $_POST["new_password"] ?? '';
$confirm_password = $_POST["confirm_password"] ?? '';
$remove_image  = ($_POST["remove_image"] ?? '0') === '1';

$fullname = trim($first_name . ' ' . $last_name);

if ($fullname === '') {
    respond(false, "Name is required.");
}

// ---- Password validation (only if the user typed one) ----
if ($new_password !== '' || $confirm_password !== '') {
    if ($new_password !== $confirm_password) {
        respond(false, "Passwords do not match.");
    }
    if (strlen($new_password) < 8) {
        respond(false, "Password must be at least 8 characters.");
    }
}

// ---- Fetch current image path (needed for removal / overwrite) ----
$stmt = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$current = $stmt->get_result()->fetch_assoc();
$current_image = $current["profile_image"] ?? '';

$new_image_path = null;
$new_image_name = null;

// ---- Handle image removal ----
if ($remove_image && $current_image && file_exists($current_image)) {
    @unlink($current_image);
    $new_image_path = '';
}

// ---- Handle image upload ----
if (isset($_FILES["profile_image"]) && $_FILES["profile_image"]["error"] === UPLOAD_ERR_OK) {
    $allowed = ["image/jpeg", "image/png", "image/webp"];
    $file = $_FILES["profile_image"];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file["tmp_name"]);
    finfo_close($finfo);

    if (!in_array($mime, $allowed)) {
        respond(false, "Only JPG, PNG, or WEBP images are allowed.");
    }
    if ($file["size"] > 2 * 1024 * 1024) {
        respond(false, "Image must be smaller than 2MB.");
    }

    $extMap = [
        "image/jpeg" => "jpg",
        "image/png"  => "png",
        "image/webp" => "webp",
    ];
    $ext = $extMap[$mime] ?? 'jpg';
    $safe_name = "user_{$id}_" . time() . "." . $ext;
    $upload_dir = "uploads/profile/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    $destination = $upload_dir . $safe_name;

    if (!move_uploaded_file($file["tmp_name"], $destination)) {
        respond(false, "Failed to save uploaded image.");
    }

    // Remove old image if one existed
    if ($current_image && file_exists($current_image)) {
        @unlink($current_image);
    }

    $new_image_path = $destination;
    $new_image_name = $safe_name;
}

// ---- Build update query dynamically ----
$fields = ["fullname = ?", "mobile_number = ?"];
$params = [$fullname, $mobile_number];
$types = "ss";

if ($new_image_path !== null) {
    $fields[] = "profile_image = ?";
    $params[] = $new_image_path;
    $types .= "s";
}

if ($new_password !== '') {
    $fields[] = "password = ?";
    $params[] = password_hash($new_password, PASSWORD_DEFAULT);
    $types .= "s";
}

$params[] = $id;
$types .= "i";

$sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

if (!$stmt->execute()) {
    respond(false, "Database error. Please try again.");
}

respond(true, "Profile updated successfully.", [
    "image_path" => $new_image_path !== null ? ($new_image_path ?: 'css/img/sample.png') : null,
    "image_name" => $new_image_name
]);
