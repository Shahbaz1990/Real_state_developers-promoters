<?php
/**
 * save-section.php
 * Saves one "section" of a trending_projects row at a time.
 * Called via AJAX from includes/edit-mode.php.
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

$projectId = isset($_POST['project_id']) ? (int) $_POST['project_id'] : 0;
$section   = isset($_POST['section']) ? trim($_POST['section']) : '';
$fieldsRaw = isset($_POST['fields']) ? $_POST['fields'] : '{}';
$fields    = json_decode($fieldsRaw, true);

if (!$projectId || !$section || !is_array($fields)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}

// Whitelist of editable columns per section.
// NEVER trust client-supplied column names directly — only these are allowed through.
$allowedColumns = [
    'project_details' => [
        'about_project', 'bedrooms', 'bathrooms', 'area', 'year_built',
        'towers', 'floors', 'units', 'total_project_area', 'plot_area', 'open_area',
        'spec_flooring', 'spec_bathrooms', 'spec_kitchen', 'spec_doors_windows',
    ],
    'amenities' => [
        'amenities',
    ],
    'company_details' => [
        'owner_phone', 'owner_email', 'owner_email2', 'owner_email_display',
        'website', 'address',
    ],
    'maps' => [
        'lat', 'lng',
    ],
];

if (!isset($allowedColumns[$section])) {
    echo json_encode(['status' => 'error', 'message' => 'Unknown section.']);
    exit;
}

$setParts = [];
$params   = [];
$types    = '';

foreach ($allowedColumns[$section] as $col) {
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

$sql = "UPDATE trending_projects SET " . implode(', ', $setParts) . " WHERE id = ? LIMIT 1";
$params[] = $projectId;
$types   .= 'i';

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Query preparation failed.']);
    exit;
}

$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Saved successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error while saving.']);
}
$stmt->close();
