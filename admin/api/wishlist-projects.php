<?php
/**
 * GET /backend/api/wishlist-projects.php?ids=1,4,7
 *
 * Takes a comma-separated list of trending_projects IDs (as stored in the
 * visitor's browser localStorage wishlist) and returns their current details.
 * Returns JSON: {"success": true, "data": [...]}
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../../config/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
cors_headers();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$idsParam = isset($_GET['ids']) ? trim($_GET['ids']) : '';

if ($idsParam === '') {
    echo json_encode(['success' => true, 'data' => []]);
    exit;
}

// Sanitize: keep only positive integers from the comma-separated list
$ids = array_filter(array_map('intval', explode(',', $idsParam)), function ($n) {
    return $n > 0;
});
$ids = array_values(array_unique($ids));

if (empty($ids)) {
    echo json_encode(['success' => true, 'data' => []]);
    exit;
}

// Build the right number of placeholders for the IN (...) clause
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$types = str_repeat('i', count($ids));

$sql = "SELECT id, project_name, slug, address, price, image
        FROM trending_projects
        WHERE status = 'Active' AND id IN ({$placeholders})";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error.']);
    exit;
}
$stmt->bind_param($types, ...$ids);
$stmt->execute();
$res = $stmt->get_result();

$projects = [];
while ($row = $res->fetch_assoc()) {
    $projects[] = [
        'id'           => (int) $row['id'],
        'project_name' => $row['project_name'],
        'slug'         => $row['slug'],
        'address'      => $row['address'],
        'price'        => $row['price'],
        'image'        => !empty($row['image'])
            ? "/backend/uploads/trending-projects/" . $row['image']
            : "/front/assets/images/no-image.jpg",
    ];
}
$stmt->close();

echo json_encode(['success' => true, 'data' => $projects]);