<?php
/*
 |--------------------------------------------------------------------------
 | GET /backend/api/properties-search.php
 |
 | Search / filter trending_projects and return the same JSON shape as
 | trending-projects.php: { success, meta: {total,page,per_page,total_pages}, data: [...] }
 |
 | Query params (all optional):
 |   search      keyword — matches project_name OR address
 |   city        matches against address (no dedicated city column yet)
 |   bedrooms    exact match, e.g. bedrooms=3
 |   bathrooms   exact match, e.g. bathrooms=2
 |   min         min price in plain rupees, e.g. min=5000000
 |   max         max price in plain rupees, e.g. max=20000000
 |   sort        Newest | Price Low-High | Price High-Low   (default Newest)
 |   page        default 1
 |   per_page    default 20, max 50
 |
 | Example:
 |   properties-search.php?search=powai&bedrooms=3&min=10000000&max=25000000&sort=Price+Low-High&page=1
 |--------------------------------------------------------------------------
 */

// NOTE: adjust this path if trending-projects.php includes config.php from
// somewhere else — this assumes the same folder layout (backend/api/../config.php).
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../../config/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
cors_headers();

function priceToLakhs($price): float {
    $digits = preg_replace('/\D/', '', (string) $price);
    return $digits === '' ? 0 : ((float) $digits) / 100000;
}

// Matches the "https://test.tatitodevelopersandpromoters.com/backend/uploads/trending-projects/<file>"
// full URLs seen in trending-projects.php's output.
function imageUrl($filename): ?string {
    if (empty($filename)) return null;
    if (preg_match('#^https?://#i', $filename)) return $filename;
    return 'https://test.tatitodevelopersandpromoters.com/backend/uploads/trending-projects/' . $filename;
}

try {
    $search    = isset($_GET['search']) ? trim($_GET['search']) : '';
    $city      = isset($_GET['city']) ? trim($_GET['city']) : '';
    $bedrooms  = isset($_GET['bedrooms']) && $_GET['bedrooms'] !== '' ? (int) $_GET['bedrooms'] : null;
    $bathrooms = isset($_GET['bathrooms']) && $_GET['bathrooms'] !== '' ? (int) $_GET['bathrooms'] : null;
    $minLakhs  = isset($_GET['min']) && $_GET['min'] !== '' ? ((float) $_GET['min']) / 100000 : null;
    $maxLakhs  = isset($_GET['max']) && $_GET['max'] !== '' ? ((float) $_GET['max']) / 100000 : null;
    $sort      = isset($_GET['sort']) ? trim($_GET['sort']) : 'Newest';
    $page      = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
    $perPage   = isset($_GET['per_page']) ? max(1, min(50, (int) $_GET['per_page'])) : 20;

    // ===============================
    // WHERE clause — only real, indexable columns go here
    // ===============================
    $where  = ["status = 'Active'"];
    $types  = '';
    $params = [];

    if ($search !== '') {
        $where[]  = "(project_name LIKE ? OR address LIKE ?)";
        $like     = "%{$search}%";
        $types   .= 'ss';
        $params[] = $like; $params[] = $like;
    }
    if ($city !== '') {
        $where[]  = "address LIKE ?";
        $types   .= 's';
        $params[] = "%{$city}%";
    }
    if ($bedrooms !== null) {
        $where[]  = "bedrooms = ?";
        $types   .= 'i'; $params[] = $bedrooms;
    }
    if ($bathrooms !== null) {
        $where[]  = "bathrooms = ?";
        $types   .= 'i'; $params[] = $bathrooms;
    }
    $whereSql = implode(' AND ', $where);

    $orderSql = "created_at DESC";
    if ($sort === 'Price Low-High') $orderSql = "CAST(REGEXP_REPLACE(price, '[^0-9]', '') AS UNSIGNED) ASC";
    if ($sort === 'Price High-Low') $orderSql = "CAST(REGEXP_REPLACE(price, '[^0-9]', '') AS UNSIGNED) DESC";

    $sql  = "SELECT * FROM trending_projects WHERE {$whereSql} ORDER BY {$orderSql}";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Query prepare failed: ' . $conn->error);
    }
    if ($types !== '') { $stmt->bind_param($types, ...$params); }
    $stmt->execute();
    $res = $stmt->get_result();
    $all = [];
    while ($row = $res->fetch_assoc()) { $all[] = $row; }
    $stmt->close();

    // Price is free-text (e.g. "₹1,76,00,000"), so range filtering happens here
    if ($minLakhs !== null || $maxLakhs !== null) {
        $all = array_values(array_filter($all, function ($row) use ($minLakhs, $maxLakhs) {
            $lakhs = priceToLakhs($row['price']);
            if ($minLakhs !== null && $lakhs < $minLakhs) return false;
            if ($maxLakhs !== null && $lakhs > $maxLakhs) return false;
            return true;
        }));
    }

    $total      = count($all);
    $totalPages = max(1, (int) ceil($total / $perPage));
    $page       = min($page, $totalPages);
    $offset     = ($page - 1) * $perPage;
    $pageItems  = array_slice($all, $offset, $perPage);

    $data = array_map(function ($row) {
        return [
            'id'               => (int) $row['id'],
            'project_name'     => $row['project_name'],
            'slug'             => $row['slug'],
            'address'          => $row['address'],
            'price'            => $row['price'],
            'image'            => imageUrl($row['image']),
            'project_order'    => isset($row['project_order']) ? (int) $row['project_order'] : null,
            'bedrooms'         => $row['bedrooms'] !== null ? (int) $row['bedrooms'] : null,
            'bathrooms'        => $row['bathrooms'] !== null ? (int) $row['bathrooms'] : null,
            'area'             => $row['area'],
            'year_built'       => $row['year_built'],
            'furnished'        => $row['furnished'],
            'plot_area'        => $row['plot_area'],
            'amenities'        => !empty($row['amenities'])
                ? array_values(array_filter(array_map('trim', explode(',', $row['amenities']))))
                : [],
            'owner_email'      => $row['owner_email'],
            'owner_phone'      => $row['owner_phone'],
            'appointment_date' => $row['appointment_date'],
            'created_at'       => $row['created_at'],
            'updated_at'       => $row['updated_at'],
        ];
    }, $pageItems);

    echo json_encode([
        'success' => true,
        'meta' => [
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => $totalPages,
        ],
        'data' => $data,
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage(),
    ]);
}