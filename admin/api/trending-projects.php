<?php
/**
 * GET /backend/api/trending-projects.php
 * GET /backend/api/trending-projects.php?slug=kalpataru-vian   -> single project
 *
 * Returns active rows from `trending_projects` as JSON.
 * Image paths are returned as full absolute URLs so any client (front-end
 * fetch, mobile app, etc.) can use them directly without guessing the host.
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../../config/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
$cors_ok = cors_headers();

// Preflight requests just get a 200 with no body (403 if origin not allowed)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code($cors_ok ? 200 : 403);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed. Use GET.']);
    exit;
}

// Build a full, absolute URL for an uploaded image filename
function imageUrl($filename) {
    if (empty($filename)) {
        return null;
    }
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'];
    return "{$scheme}://{$host}/backend/uploads/trending-projects/" . $filename;
}

// Shape one DB row into the API's response format
function formatProject(array $row) {
    return [
        'id'               => (int) $row['id'],
        'project_name'     => $row['project_name'],
        'slug'             => $row['slug'],
        'address'          => $row['address'],
        'price'            => $row['price'],
        'image'            => imageUrl($row['image'] ?? null),
        'project_order'    => (int) $row['project_order'],
        'bedrooms'         => isset($row['bedrooms']) ? (int) $row['bedrooms'] : null,
        'bathrooms'        => isset($row['bathrooms']) ? (int) $row['bathrooms'] : null,
        'area'             => $row['area'] ?? null,
        'year_built'       => $row['year_built'] ?? null,
        'furnished'        => $row['furnished'] ?? null,
        'plot_area'        => $row['plot_area'] ?? null,
        'amenities'        => !empty($row['amenities'])
            ? array_map('trim', explode(',', $row['amenities']))
            : [],
        'owner_email'      => $row['owner_email'] ?? null,
        'owner_phone'      => $row['owner_phone'] ?? null,
        'appointment_date' => $row['appointment_date'] ?? null,
        'created_at'       => $row['created_at'] ?? null,
        'updated_at'       => $row['updated_at'] ?? null,
    ];
}

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if ($slug !== '') {
    // ===============================
    // Single project by slug
    // ===============================
    $stmt = $conn->prepare("SELECT * FROM trending_projects WHERE slug = ? AND status = 'Active' LIMIT 1");
    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error.']);
        exit;
    }
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $project = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$project) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Project not found.']);
        exit;
    }

    echo json_encode(['success' => true, 'data' => formatProject($project)]);
    exit;
}

// ===============================
// List all active projects
// ===============================
$page    = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$perPage = isset($_GET['per_page']) ? max(1, min(50, (int) $_GET['per_page'])) : 20;
$offset  = ($page - 1) * $perPage;

$countResult = $conn->query("SELECT COUNT(*) AS total FROM trending_projects WHERE status = 'Active'");
$total = $countResult ? (int) $countResult->fetch_assoc()['total'] : 0;

$stmt = $conn->prepare("SELECT * FROM trending_projects WHERE status = 'Active' ORDER BY project_order ASC LIMIT ? OFFSET ?");
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error.']);
    exit;
}
$stmt->bind_param('ii', $perPage, $offset);
$stmt->execute();
$res = $stmt->get_result();

$projects = [];
while ($row = $res->fetch_assoc()) {
    $projects[] = formatProject($row);
}
$stmt->close();

echo json_encode([
    'success' => true,
    'meta' => [
        'total'       => $total,
        'page'        => $page,
        'per_page'    => $perPage,
        'total_pages' => max(1, (int) ceil($total / $perPage)),
    ],
    'data' => $projects,
]);