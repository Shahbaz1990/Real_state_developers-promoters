<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/listing-helpers.php';

/*
 |--------------------------------------------------------------------------
 | Expected `local_services` table columns:
 | id, service_name, slug, category, city, address, phone, price,
 | rating, image, description, status, created_at
 |
 | See schema.sql. Adjust the $sql / bind_param sections below if your
 | actual table/columns differ.
 |--------------------------------------------------------------------------
 */

$CATEGORY_PREFIX = 'lsv-';

$search   = isset($_GET['search']) ? trim($_GET['search']) : '';
$city     = isset($_GET['city']) ? trim($_GET['city']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$sort     = isset($_GET['sort']) ? trim($_GET['sort']) : 'Newest';
$page     = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$perPage  = 9;

$categoryLabel = $category !== '' ? categorySlugToLabel($category, $CATEGORY_PREFIX) : '';

$serviceGroups = [
    'lsv-cleaning-pest-control' => ['lsv-house-cleaning','lsv-deep-cleaning','lsv-sofa-cleaning','lsv-carpet-cleaning','lsv-water-tank-cleaning','lsv-chimney-cleaning','lsv-pest-control','lsv-termite-control','lsv-cockroach-control','lsv-rodent-control','lsv-mosquito-fogging','lsv-bathroom-deep-cleaning'],
    'lsv-repairs-maintenance' => ['lsv-ac-repair-service','lsv-ac-installation','lsv-refrigerator-repair','lsv-washing-machine-repair','lsv-ro-water-purifier-service','lsv-electrician-service','lsv-plumber-service','lsv-carpenter-service','lsv-locksmith-service','lsv-key-duplication','lsv-glass-repair-replacement','lsv-furniture-repair','lsv-motor-repair-water-motor','lsv-solar-water-heater-service','lsv-inverter-battery-service','lsv-generator-rental'],
    'lsv-painting-d-cor-installation' => ['lsv-home-painting','lsv-wall-putty-service','lsv-waterproofing-service','lsv-pop-false-ceiling-service','lsv-cctv-installation','lsv-home-automation-setup','lsv-solar-installation','lsv-curtain-blinds-installation','lsv-wallpaper-installation','lsv-upholstery-service','lsv-photo-framing-service'],
    'lsv-gardening-outdoor' => ['lsv-gardening-service','lsv-landscaping-service','lsv-lawn-mowing','lsv-borewell-service','lsv-water-tanker-supply'],
    'lsv-household-staffing-personal-care' => ['lsv-security-guard-service','lsv-housekeeping-staff','lsv-maid-service','lsv-cook-service','lsv-babysitting-nanny-service','lsv-elderly-care-service','lsv-nursing-care-at-home','lsv-physiotherapy-at-home','lsv-salon-at-home','lsv-spa-at-home'],
    'lsv-moving-vehicles-events' => ['lsv-packers-movers-local','lsv-packers-movers-interstate','lsv-bike-car-transportation','lsv-storage-warehouse-service','lsv-vehicle-cleaning','lsv-car-wash','lsv-bike-servicing','lsv-event-decoration','lsv-catering-service','lsv-tent-house-service'],
    'lsv-property-society-management' => ['lsv-home-inspection-service','lsv-vastu-consultation-service','lsv-legal-documentation-service','lsv-society-apartment-management-service','lsv-facility-management-service'],
];
$pillCategories = [];
if ($category !== '' && isset($serviceGroups[$category])) {
    $pillCategories = $serviceGroups[$category];
} elseif ($category !== '') {
    foreach ($serviceGroups as $group => $children) {
        if (in_array($category, $children, true)) { $pillCategories = $children; break; }
    }
}

// ===============================
// WHERE clause
// ===============================
$where  = ["status = 'Active'"];
$types  = '';
$params = [];

if ($search !== '') {
    $where[] = "(service_name LIKE ? OR description LIKE ? OR address LIKE ?)";
    $like = "%{$search}%";
    $types .= 'sss';
    $params[] = $like; $params[] = $like; $params[] = $like;
}
if ($city !== '') {
    $where[] = "city = ?";
    $types .= 's';
    $params[] = $city;
}
if ($category !== '') {
    if (isset($serviceGroups[$category])) {
        $placeholders = implode(',', array_fill(0, count($serviceGroups[$category]), '?'));
        $where[] = "category IN ($placeholders)";
        foreach ($serviceGroups[$category] as $c) { $types .= 's'; $params[] = $c; }
    } else {
        $where[] = "category = ?";
        $types .= 's';
        $params[] = $category;
    }
}
$whereSql = implode(' AND ', $where);

$orderSql = "created_at DESC";
if ($sort === 'Rating High-Low') $orderSql = "rating DESC";

$cities = [];
if ($res = $conn->query("SELECT DISTINCT city FROM local_services WHERE status='Active' AND city IS NOT NULL AND city <> '' ORDER BY city ASC")) {
    while ($row = $res->fetch_assoc()) { $cities[] = $row['city']; }
}

$sql  = "SELECT * FROM local_services WHERE {$whereSql} ORDER BY {$orderSql}";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    $allMatches = [];
    $dbError = $conn->error;
} else {
    if ($types !== '') { $stmt->bind_param($types, ...$params); }
    $stmt->execute();
    $res = $stmt->get_result();
    $allMatches = [];
    while ($row = $res->fetch_assoc()) { $allMatches[] = $row; }
    $stmt->close();
    $dbError = null;
}

if ($sort === 'Price Low-High' || $sort === 'Price High-Low') {
    usort($allMatches, function ($a, $b) use ($sort) {
        $pa = extractNumericPrice(pv($a, 'price', ''));
        $pb = extractNumericPrice(pv($b, 'price', ''));
        return $sort === 'Price Low-High' ? $pa <=> $pb : $pb <=> $pa;
    });
}

$total      = count($allMatches);
$totalPages = max(1, (int) ceil($total / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;
$services   = array_slice($allMatches, $offset, $perPage);

$pageTitle = $categoryLabel !== '' ? $categoryLabel . ' Services' : 'Local Services Near You';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="icon" type="image/png" href="<?php echo $site['favicon']; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/listing-common.css">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

<section class="listing-hero py-4">
<div class="container">
    <div class="text-center mb-4">
        <h2 class="display-6 fw-bold"><?php echo htmlspecialchars($pageTitle); ?></h2>
        <p>Book trusted home services — cleaning, repairs, staffing, moving and more, near you</p>
    </div>

    <div class="search-card">
        <form method="GET" id="filterForm">
            <?php if ($sort !== 'Newest'): ?><input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>"><?php endif; ?>
            <?php if ($category !== ''): ?><input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>"><?php endif; ?>

            <div class="search-input-group mb-3">
                <span class="input-group-text bg-transparent border-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control search-input"
                       placeholder="Search services by name or keyword"
                       value="<?php echo htmlspecialchars($search); ?>">
                <button class="search-btn" type="submit">Search</button>
            </div>

            <div class="filters-grid">
                <div class="filter-col">
                    <label>City</label>
                    <select name="city" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">All Cities</option>
                        <?php foreach ($cities as $c): ?>
                            <option value="<?php echo htmlspecialchars($c); ?>" <?php echo $city === $c ? 'selected' : ''; ?>><?php echo htmlspecialchars($c); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-col">
                    <label>Sort By</label>
                    <select onchange="window.location.href=this.value" class="form-select">
                        <?php foreach (['Newest','Rating High-Low','Price Low-High','Price High-Low'] as $val):
                            $q = $_GET; $q['sort'] = $val; $sortUrl = 'local-services.php?' . http_build_query($q);
                        ?>
                            <option value="<?php echo htmlspecialchars($sortUrl); ?>" <?php echo $sort === $val ? 'selected' : ''; ?>><?php echo $val; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-col">
                    <label>Category</label>
                    <input type="text" class="form-control" readonly
                           value="<?php echo $categoryLabel !== '' ? htmlspecialchars($categoryLabel) : 'All Categories'; ?>">
                </div>
                <div class="filter-col d-flex align-items-end">
                    <a href="local-services.php" class="clear-btn text-decoration-none d-inline-flex align-items-center gap-2">
                        <i class="bi bi-funnel"></i> Clear all filters
                    </a>
                </div>
            </div>

            <div class="active-filters">
                <?php $hasActive = false; ?>
                <?php if ($search !== ''): $hasActive = true; ?>
                    <a href="<?php echo urlWithoutParam('local-services.php', 'search'); ?>" class="chip">Search: <?php echo htmlspecialchars($search); ?> <i class="bi bi-x"></i></a>
                <?php endif; ?>
                <?php if ($city !== ''): $hasActive = true; ?>
                    <a href="<?php echo urlWithoutParam('local-services.php', 'city'); ?>" class="chip">City: <?php echo htmlspecialchars($city); ?> <i class="bi bi-x"></i></a>
                <?php endif; ?>
                <?php if ($category !== ''): $hasActive = true; ?>
                    <a href="<?php echo urlWithoutParam('local-services.php', 'category'); ?>" class="chip">Category: <?php echo htmlspecialchars($categoryLabel); ?> <i class="bi bi-x"></i></a>
                <?php endif; ?>
                <?php if (!$hasActive): ?><span class="text-muted small">No filters applied</span><?php endif; ?>
            </div>
        </form>

        <?php if (!empty($pillCategories)): ?>
        <div class="category-pill-row">
            <?php foreach ($pillCategories as $slug):
                $q = $_GET; $q['category'] = $slug; unset($q['page']);
            ?>
                <a class="category-pill <?php echo $category === $slug ? 'active' : ''; ?>" href="local-services.php?<?php echo http_build_query($q); ?>">
                    <?php echo htmlspecialchars(categorySlugToLabel($slug, $CATEGORY_PREFIX)); ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
</section>

<section class="py-5">
<div class="container">
    <h2 class="fw-bold mb-4"><?php echo (int) $total; ?> Service<?php echo $total === 1 ? '' : 's'; ?> Found</h2>

    <?php if ($dbError): ?>
        <div class="alert alert-warning">
            Local service listings aren't set up yet — the <code>local_services</code> table is missing or misconfigured
            (<?php echo htmlspecialchars($dbError); ?>). See <code>schema.sql</code> for the table this page expects.
        </div>
    <?php elseif (empty($services)): ?>
        <div class="alert alert-warning">No services found matching your filters.</div>
    <?php else: ?>
        <div class="listing-grid">
            <?php foreach ($services as $s):
                $imagePath = !empty($s['image']) ? "../backend/uploads/local-services/" . $s['image'] : "assets/images/service-placeholder.jpg";
                $slug = !empty($s['slug']) ? $s['slug'] : null;
                $url  = $slug ? "service-listing/{$slug}/" : '#';
                $catLabel = categorySlugToLabel(pv($s, 'category', ''), $CATEGORY_PREFIX);
            ?>
            <a href="<?php echo htmlspecialchars($url); ?>" class="listing-card">
                <div class="listing-image">
                    <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($s['service_name']); ?>">
                    <?php if ($catLabel !== ''): ?><span class="category-badge"><?php echo htmlspecialchars($catLabel); ?></span><?php endif; ?>
                </div>
                <div class="listing-body">
                    <h3 class="listing-title"><?php echo htmlspecialchars($s['service_name']); ?></h3>
                    <div class="listing-meta">
                        <i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars(formatAddressDisplay(pv($s, 'address', pv($s, 'city', '')))); ?>
                    </div>
                    <?php echo renderStars(pv($s, 'rating', 0)); ?>
                    <div class="listing-footer">
                        <span class="price-tag"><?php echo htmlspecialchars(pv($s, 'price', 'Get Quote')); ?></span>
                        <?php if (!empty($s['phone'])): ?>
                            <span class="contact-btn"><i class="bi bi-telephone-fill me-1"></i>Contact</span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($totalPages > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <?php $prevQ = $_GET; $prevQ['page'] = max(1, $page - 1); $nextQ = $_GET; $nextQ['page'] = min($totalPages, $page + 1); ?>
            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>"><a class="page-link" href="local-services.php?<?php echo http_build_query($prevQ); ?>">&laquo;</a></li>
            <?php for ($i = 1; $i <= $totalPages; $i++): $pQ = $_GET; $pQ['page'] = $i; ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>"><a class="page-link" href="local-services.php?<?php echo http_build_query($pQ); ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>"><a class="page-link" href="local-services.php?<?php echo http_build_query($nextQ); ?>">&raquo;</a></li>
        </ul>
    </nav>
    <?php endif; ?>
</div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
