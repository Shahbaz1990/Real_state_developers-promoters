<?php
/**
 * save-floorplan-variant.php
 * Saves one row in floor_plans (a single BHK variant: area/price/status/possession).
 * Called via AJAX from includes/edit-mode.php when a "Save Row" button
 * inside a floor-plan-card is clicked.
 */
session_start();
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/json');

// ---- AUTH CHECK: adjust to match your real admin session convention ----
if (empty($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Not authorized.']);
    exit;
}

$variantId = isset($_POST['variant_id']) ? (int) $_POST['variant_id'] : 0;
$fieldsRaw = isset($_POST['fields']) ? $_POST['fields'] : '{}';
$fields    = json_decode($fieldsRaw, true);

if (!$variantId || !is_array($fields)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}

// Whitelist of editable columns on floor_plans
$allowed = ['carpet_area_sqft', 'carpet_area_sqm', 'price', 'price_range', 'status', 'possession_date'];

$setParts = [];
$params   = [];
$types    = '';

foreach ($allowed as $col) {
    if (array_key_exists($col, $fields)) {
        $setParts[] = "`$col` = ?";
        $params[]   = (string) $fields[$col];
        $types     .= 's';
    }
}

if (empty($setParts)) {
    echo json_encode(['status' => 'error', 'message' => 'Nothing to update.']);
    exit;
}

$sql = "UPDATE floor_plans SET " . implode(', ', $setParts) . " WHERE id = ? LIMIT 1";
$params[] = $variantId;
$types   .= 'i';

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Query preparation failed.']);
    exit;
}

$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Floor plan row updated.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error while saving.']);
}
$stmt->close();
