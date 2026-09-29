<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/listing-helpers.php';

/*
 |--------------------------------------------------------------------------
 | Expected `vendors` table columns:
 | id, name, slug, category, city, address, phone, experience_years,
 | rating, starting_price, image, description, status, created_at
 |
 | See schema.sql for the CREATE TABLE statement. If your table/columns
 | are named differently, adjust the $sql / bind_param sections below —
 | everything else (filters, cards, pagination) works off these results.
 |--------------------------------------------------------------------------
 */

$CATEGORY_PREFIX = 'vnd-';

$search   = isset($_GET['search']) ? trim($_GET['search']) : '';
$city     = isset($_GET['city']) ? trim($_GET['city']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$sort     = isset($_GET['sort']) ? trim($_GET['sort']) : 'Newest';
$page     = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$perPage  = 9;

$categoryLabel = $category !== '' ? categorySlugToLabel($category, $CATEGORY_PREFIX) : '';

// Sub-categories shown as pills under the hero (only the ones that
// actually belong to the currently selected group, if any is selected).
// This list mirrors the nav menu's "Design & Consultancy" / "Civil &
// Structural Contractors" / etc. groupings.
$vendorGroups = [
    'vnd-design-consultancy' => ['vnd-architect','vnd-structural-engineer','vnd-interior-designer','vnd-landscape-designer','vnd-vastu-consultant','vnd-site-engineer','vnd-land-surveyor','vnd-soil-testing-engineer','vnd-chartered-engineer','vnd-property-valuer','vnd-rera-consultant','vnd-3d-rendering-walkthrough-vendor','vnd-drone-survey-vendor','vnd-gps-land-survey-vendor'],
    'vnd-civil-structural-contractors' => ['vnd-civil-contractor','vnd-rcc-contractor','vnd-turnkey-contractor','vnd-excavation-contractor','vnd-earthwork-contractor','vnd-piling-contractor','vnd-foundation-contractor','vnd-masonry-contractor','vnd-brickwork-contractor','vnd-plastering-contractor','vnd-demolition-contractor','vnd-scaffolding-rental','vnd-crane-rental','vnd-jcb-excavator-rental','vnd-concrete-mixer-rental','vnd-labour-contractor'],
    'vnd-mep-mechanical-electrical-plumbing' => ['vnd-plumbing-contractor','vnd-electrical-contractor','vnd-hvac-contractor','vnd-fire-fighting-contractor','vnd-lift-elevator-vendor','vnd-solar-panel-installer','vnd-borewell-drilling-contractor','vnd-water-tank-supplier','vnd-stp-sewage-treatment-vendor','vnd-cctv-security-system-vendor','vnd-home-automation-vendor'],
    'vnd-finishing-contractors' => ['vnd-painting-contractor','vnd-waterproofing-contractor','vnd-tile-contractor','vnd-flooring-contractor','vnd-false-ceiling-contractor','vnd-carpentry-contractor','vnd-modular-kitchen-vendor','vnd-wardrobe-vendor','vnd-glass-glazing-vendor','vnd-aluminium-fabricator','vnd-upvc-window-vendor','vnd-door-vendor','vnd-grill-fabrication-vendor','vnd-welding-contractor','vnd-roofing-contractor','vnd-terrace-waterproofing-vendor','vnd-paver-block-contractor','vnd-compound-wall-contractor','vnd-swimming-pool-contractor','vnd-landscaping-contractor'],
    'vnd-material-suppliers' => ['vnd-concrete-rmc-supplier','vnd-steel-tmt-supplier','vnd-cement-dealer','vnd-sand-supplier','vnd-aggregate-supplier','vnd-bricks-supplier','vnd-aac-fly-ash-blocks-supplier','vnd-granite-marble-supplier','vnd-hardware-store','vnd-sanitary-ware-supplier','vnd-cp-fittings-supplier','vnd-electrical-fittings-supplier','vnd-switch-wire-supplier','vnd-cable-supplier','vnd-paint-brands-dealer','vnd-waterproofing-chemical-supplier'],
    'vnd-allied-services-agents' => ['vnd-termite-control-vendor','vnd-pest-control-vendor','vnd-property-legal-documentation-vendor','vnd-home-loan-dsa-agent','vnd-property-insurance-agent','vnd-packers-movers','vnd-property-photographer','vnd-liaisoning-agent-govt-approvals'],
];
$pillCategories = [];
if ($category !== '' && isset($vendorGroups[$category])) {
    $pillCategories = $vendorGroups[$category]; // a parent group was clicked -> show its children
} elseif ($category !== '') {
    foreach ($vendorGroups as $group => $children) {
        if (in_array($category, $children, true)) { $pillCategories = $children; break; } // a leaf was clicked -> show siblings
    }
}

// ===============================
// WHERE clause
// ===============================
$where  = ["status = 'Active'"];
$types  = '';
$params = [];

if ($search !== '') {
    $where[] = "(name LIKE ? OR description LIKE ? OR address LIKE ?)";
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
    if (isset($vendorGroups[$category])) {
        // Parent group selected -> match any of its child category slugs
        $placeholders = implode(',', array_fill(0, count($vendorGroups[$category]), '?'));
        $where[] = "category IN ($placeholders)";
        foreach ($vendorGroups[$category] as $c) { $types .= 's'; $params[] = $c; }
    } else {
        $where[] = "category = ?";
        $types .= 's';
        $params[] = $category;
    }
}
$whereSql = implode(' AND ', $where);

$orderSql = "created_at DESC";
if ($sort === 'Rating High-Low') $orderSql = "rating DESC";
if ($sort === 'Experience High-Low') $orderSql = "experience_years DESC";

// Distinct cities for the filter dropdown
$cities = [];
if ($res = $conn->query("SELECT DISTINCT city FROM vendors WHERE status='Active' AND city IS NOT NULL AND city <> '' ORDER BY city ASC")) {
    while ($row = $res->fetch_assoc()) { $cities[] = $row['city']; }
}

// Fetch matching vendors
$sql  = "SELECT * FROM vendors WHERE {$whereSql} ORDER BY {$orderSql}";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    // Table/columns not set up yet — fail gracefully instead of a white screen.
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
        $pa = extractNumericPrice(pv($a, 'starting_price', ''));
        $pb = extractNumericPrice(pv($b, 'starting_price', ''));
        return $sort === 'Price Low-High' ? $pa <=> $pb : $pb <=> $pa;
    });
}

$total      = count($allMatches);
$totalPages = max(1, (int) ceil($total / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;
$vendors    = array_slice($allMatches, $offset, $perPage);

$pageTitle = $categoryLabel !== '' ? $categoryLabel . ' Vendors' : 'Find Trusted Vendors';
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
        <p>Verified architects, contractors, suppliers and service agents for every stage of construction</p>
    </div>

    <div class="search-card">
        <form method="GET" id="filterForm">
            <?php if ($sort !== 'Newest'): ?><input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>"><?php endif; ?>
            <?php if ($category !== ''): ?><input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>"><?php endif; ?>

            <div class="search-input-group mb-3">
                <span class="input-group-text bg-transparent border-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control search-input"
                       placeholder="Search vendors by name or keyword"
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
                        <?php foreach (['Newest','Rating High-Low','Experience High-Low','Price Low-High','Price High-Low'] as $val):
                            $q = $_GET; $q['sort'] = $val; $sortUrl = 'vendors.php?' . http_build_query($q);
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
                    <a href="vendors.php" class="clear-btn text-decoration-none d-inline-flex align-items-center gap-2">
                        <i class="bi bi-funnel"></i> Clear all filters
                    </a>
                </div>
            </div>

            <div class="active-filters">
                <?php $hasActive = false; ?>
                <?php if ($search !== ''): $hasActive = true; ?>
                    <a href="<?php echo urlWithoutParam('vendors.php', 'search'); ?>" class="chip">Search: <?php echo htmlspecialchars($search); ?> <i class="bi bi-x"></i></a>
                <?php endif; ?>
                <?php if ($city !== ''): $hasActive = true; ?>
                    <a href="<?php echo urlWithoutParam('vendors.php', 'city'); ?>" class="chip">City: <?php echo htmlspecialchars($city); ?> <i class="bi bi-x"></i></a>
                <?php endif; ?>
                <?php if ($category !== ''): $hasActive = true; ?>
                    <a href="<?php echo urlWithoutParam('vendors.php', 'category'); ?>" class="chip">Category: <?php echo htmlspecialchars($categoryLabel); ?> <i class="bi bi-x"></i></a>
                <?php endif; ?>
                <?php if (!$hasActive): ?><span class="text-muted small">No filters applied</span><?php endif; ?>
            </div>
        </form>

        <?php if (!empty($pillCategories)): ?>
        <div class="category-pill-row">
            <?php foreach ($pillCategories as $slug):
                $q = $_GET; $q['category'] = $slug; unset($q['page']);
            ?>
                <a class="category-pill <?php echo $category === $slug ? 'active' : ''; ?>" href="vendors.php?<?php echo http_build_query($q); ?>">
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
    <h2 class="fw-bold mb-4"><?php echo (int) $total; ?> Vendor<?php echo $total === 1 ? '' : 's'; ?> Found</h2>

    <?php if ($dbError): ?>
        <div class="alert alert-warning">
            Vendor listings aren't set up yet — the <code>vendors</code> table is missing or misconfigured
            (<?php echo htmlspecialchars($dbError); ?>). See <code>schema.sql</code> for the table this page expects.
        </div>
    <?php elseif (empty($vendors)): ?>
        <div class="alert alert-warning">No vendors found matching your filters.</div>
    <?php else: ?>
        <div class="listing-grid">
            <?php foreach ($vendors as $v):
                $imagePath = !empty($v['image']) ? "../backend/uploads/vendors/" . $v['image'] : "assets/images/vendor-placeholder.jpg";
                $slug = !empty($v['slug']) ? $v['slug'] : null;
                $url  = $slug ? "vendor-listing/{$slug}/" : '#';
                $catLabel = categorySlugToLabel(pv($v, 'category', ''), $CATEGORY_PREFIX);
            ?>
            <a href="<?php echo htmlspecialchars($url); ?>" class="listing-card">
                <div class="listing-image">
                    <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($v['name']); ?>">
                    <?php if ($catLabel !== ''): ?><span class="category-badge"><?php echo htmlspecialchars($catLabel); ?></span><?php endif; ?>
                </div>
                <div class="listing-body">
                    <h3 class="listing-title"><?php echo htmlspecialchars($v['name']); ?></h3>
                    <div class="listing-meta">
                        <i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars(formatAddressDisplay(pv($v, 'address', pv($v, 'city', '')))); ?>
                        <?php if (!empty($v['experience_years'])): ?>
                            &nbsp;·&nbsp;<?php echo (int) $v['experience_years']; ?>+ yrs experience
                        <?php endif; ?>
                    </div>
                    <?php echo renderStars(pv($v, 'rating', 0)); ?>
                    <div class="listing-footer">
                        <span class="price-tag"><?php echo htmlspecialchars(pv($v, 'starting_price', 'Get Quote')); ?></span>
                        <?php if (!empty($v['phone'])): ?>
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
            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>"><a class="page-link" href="vendors.php?<?php echo http_build_query($prevQ); ?>">&laquo;</a></li>
            <?php for ($i = 1; $i <= $totalPages; $i++): $pQ = $_GET; $pQ['page'] = $i; ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>"><a class="page-link" href="vendors.php?<?php echo http_build_query($pQ); ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>"><a class="page-link" href="vendors.php?<?php echo http_build_query($nextQ); ?>">&raquo;</a></li>
        </ul>
    </nav>
    <?php endif; ?>
</div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
