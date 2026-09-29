<?php
// ============================================================
// 1. Start session FIRST
// ============================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
// ============================================================
// 2. Check if this is a POST request (AJAX)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // If not POST, just exit silently or redirect
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// ============================================================
// 3. Debug logging (remove in production)
// ============================================================
define('SAVE_SECTION_DEBUG', false); // flip to true only while actively debugging

if (SAVE_SECTION_DEBUG) {
    error_log('=== SAVE PROPERTY SECTION ===');
    error_log('SESSION: ' . print_r($_SESSION, true));
    error_log('REQUEST METHOD: ' . $_SERVER['REQUEST_METHOD']);
}

// ============================================================
// 4. Check authentication (only on POST requests)
// ============================================================
$isLoggedIn = isset($_SESSION['user_id']) || isset($_SESSION['user_email']);
if (!$isLoggedIn) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to edit this page.']);
    exit;
}

// ============================================================
// 5. Include config - TRY MULTIPLE PATHS
// ============================================================
$configPaths = [
    __DIR__ . '/includes/config.php',
    __DIR__ . '/../includes/config.php',
    __DIR__ . '/../../includes/config.php',
    $_SERVER['DOCUMENT_ROOT'] . '/includes/config.php',
    $_SERVER['DOCUMENT_ROOT'] . '/front/includes/config.php',
];

$configLoaded = false;
foreach ($configPaths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $configLoaded = true;
        error_log('Config loaded from: ' . $path);
        break;
    }
}

if (!$configLoaded) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Configuration file not found.']);
    exit;
}

// ============================================================
// 6. Check if database connection exists
// ============================================================
if (!isset($conn) || !$conn) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit;
}

// ============================================================
// 7. Get POST data
// ============================================================
$input = json_decode(file_get_contents('php://input'), true);
error_log('INPUT DATA: ' . print_r($input, true));

$project_id = isset($input['project_id']) ? (int)$input['project_id'] : 0;
$section = isset($input['section']) ? $input['section'] : '';
$fields = isset($input['fields']) ? $input['fields'] : [];

if (!$project_id || empty($fields)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid data. Project ID: ' . $project_id . ', Fields: ' . count($fields)]);
    exit;
}

// ============================================================
// 8. Get table columns to validate fields exist
// ============================================================
$tableColumns = [];
$colResult = $conn->query("SHOW COLUMNS FROM trending_projects");
if ($colResult) {
    while ($col = $colResult->fetch_assoc()) {
        $tableColumns[] = $col['Field'];
    }
    error_log('Table columns: ' . print_r($tableColumns, true));
}
// ============================================================
// 8b. Geocode the address server-side if it's being updated
// Uses OpenStreetMap's free Nominatim service — no API key,
// no billing, no account needed.
// ============================================================
$geocodedLat = null;
$geocodedLng = null;

if (!empty($fields['address']) && is_string($fields['address'])) {
    $geocodeUrl = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q='
        . urlencode($fields['address']);

    // Nominatim requires a real User-Agent identifying your app — requests
    // without one are frequently blocked.
    $context = stream_context_create([
        'http' => [
            'header' => "User-Agent: TatitoDevelopersPromoters/1.0 (contact@tatitodevelopersandpromoters.com)\r\n",
            'timeout' => 8,
        ],
    ]);

    $geocodeResponse = @file_get_contents($geocodeUrl, false, $context);
    if ($geocodeResponse !== false) {
        $geocodeData = json_decode($geocodeResponse, true);
        if (!empty($geocodeData[0]['lat']) && !empty($geocodeData[0]['lon'])) {
            $geocodedLat = (float) $geocodeData[0]['lat'];
            $geocodedLng = (float) $geocodeData[0]['lon'];
            $fields['lat'] = $geocodedLat;
            $fields['lng'] = $geocodedLng;
        } else {
            error_log('Geocoding found no results for address: ' . $fields['address']);
            // Address text still saves normally — we just skip updating lat/lng.
        }
    } else {
        error_log('Geocoding request failed for address: ' . $fields['address']);
    }
}
// ============================================================
// 9. Validate allowed fields (security)
// ============================================================
$allowedFields = [
    // Project details
    'project_name', 'address', 'price', 'bedrooms', 'bathrooms', 
    'area', 'year_built', 'construction_status', 'possession_date',
    'about_project', 'amenities', 'features',
    
    // Property details (right column)
    'rera_number', 'approvals', 'min_sft', 'completion_date',
    
    // Company details
    'owner_phone', 'owner_email', 'owner_email2', 'owner_email_display',
    'website', 'website_online', 'company_name',
    
    // Location
    'lat', 'lng',
    
    // Specifications
    'spec_flooring', 'spec_bathrooms', 'spec_kitchen', 'spec_doors_windows',
    'towers', 'floors', 'units', 'total_project_area', 'open_area',
    'plot_area', 'furnished',
    
    // Other
    'status', 'slug', 'image', 'brochure',
];

// Filter only allowed fields that exist in the table
$filteredFields = [];
$skippedFields = [];
foreach ($fields as $field => $value) {
    if (!is_scalar($value)) {
        continue; // skip anything that isn't a plain string/number
    }
    if (in_array($field, $allowedFields)) {
        // Check if column exists in table
        if (in_array($field, $tableColumns)) {
            $filteredFields[$field] = $value;
        } else {
            $skippedFields[] = $field . ' (column does not exist)';
            error_log('Skipping field (column does not exist): ' . $field);
        }
    } else {
        $skippedFields[] = $field . ' (not allowed)';
        error_log('Skipping unauthorized field: ' . $field);
    }
}

if (empty($filteredFields)) {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error', 
        'message' => 'No valid fields to update. Skipped: ' . implode(', ', $skippedFields)
    ]);
    exit;
}

// ============================================================
// 10. Build update query
// ============================================================
$updates = [];
$params = [];
$types = '';

foreach ($filteredFields as $field => $value) {
    $updates[] = "$field = ?";
    $params[] = trim($value);
    $types .= 's';
}

$params[] = $project_id;
$types .= 'i';

$sql = "UPDATE trending_projects SET " . implode(', ', $updates) . " WHERE id = ?";
error_log('SQL: ' . $sql);
error_log('Params: ' . print_r($params, true));

// ============================================================
// 11. Execute update
// ============================================================
$stmt = $conn->prepare($sql);
if (!$stmt) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    $affectedRows = $stmt->affected_rows;
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success', 
        'message' => 'Updated successfully. Affected rows: ' . $affectedRows,
        'fields_updated' => array_keys($filteredFields),
        'lat' => $geocodedLat,
        'lng' => $geocodedLng,
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error', 
        'message' => 'Update failed: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>