<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/listing-helpers.php';

/*
 |--------------------------------------------------------------------------
 | Expected `interiors` table columns:
 | id, product_name, slug, category, brand, price, image, description,
 | status, created_at
 |
 | See schema.sql. Adjust the $sql / bind_param sections below if your
 | actual table/columns differ.
 |--------------------------------------------------------------------------
 */

$CATEGORY_PREFIX = 'int-';

$search   = isset($_GET['search']) ? trim($_GET['search']) : '';
$brand    = isset($_GET['brand']) ? trim($_GET['brand']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$sort     = isset($_GET['sort']) ? trim($_GET['sort']) : 'Newest';
$page     = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$perPage  = 12;

$categoryLabel = $category !== '' ? categorySlugToLabel($category, $CATEGORY_PREFIX) : '';

$interiorGroups = [
    'int-doors' => ['int-panel-door','int-flush-door','int-laminated-door','int-veneer-door','int-membrane-door','int-pvc-door','int-upvc-door','int-wpc-door','int-fiber-frp-door','int-plain-glass-door','int-frosted-glass-door','int-toughened-glass-door','int-sliding-door','int-folding-door','int-bi-fold-door','int-french-door','int-dutch-door','int-louvered-door','int-barn-door','int-pocket-door','int-revolving-door','int-teak-wood-door','int-sal-wood-door','int-sheesham-wood-door','int-metal-door','int-steel-door','int-aluminium-door','int-security-safety-door','int-fire-rated-door','int-soundproof-door','int-waterproof-bathroom-door','int-main-entrance-door','int-pooja-room-door','int-bedroom-door','int-kitchen-door','int-balcony-door','int-garage-door','int-rolling-shutter-door','int-collapsible-gate'],
    'int-windows' => ['int-sliding-window','int-casement-window','int-fixed-window','int-awning-window','int-bay-window','int-bow-window','int-louvered-window','int-georgian-window','int-skylight-window','int-tilt-turn-window','int-double-hung-window','int-single-hung-window','int-french-window','int-upvc-window','int-aluminium-window','int-wooden-window','int-steel-window','int-toughened-glass-window','int-combination-window','int-corner-window','int-ventilator-window','int-arch-round-window','int-frosted-bathroom-window','int-mosquito-mesh-window'],
    'int-flooring' => ['int-vitrified-tiles','int-ceramic-tiles','int-porcelain-tiles','int-marble-flooring','int-granite-flooring','int-italian-marble','int-kota-stone','int-cuddapah-stone','int-wooden-flooring','int-laminate-flooring','int-engineered-wood-flooring','int-vinyl-flooring','int-epoxy-flooring','int-pvc-flooring','int-rubber-flooring','int-carpet-flooring','int-terrazzo-flooring','int-mosaic-flooring','int-ips-flooring','int-cement-flooring','int-anti-skid-tiles','int-wooden-deck-flooring','int-stone-cladding-flooring','int-athangudi-tiles','int-handmade-tiles','int-kitchen-bathroom-tiles'],
    'int-wall-finishes-paints' => ['int-emulsion-paint','int-distemper-paint','int-texture-paint','int-pop-finish','int-wallpaper','int-wood-wall-panelling','int-pvc-wall-panel','int-wpc-wall-panel','int-stone-cladding','int-brick-cladding','int-exposed-brick-wall','int-3d-wall-panel','int-fabric-wall-panel','int-mirror-wall-panel','int-glass-wall-panel','int-wall-putty','int-waterproof-wall-coating','int-metallic-paint-finish','int-textured-wall-art'],
    'int-ceiling' => ['int-pop-false-ceiling','int-gypsum-false-ceiling','int-pvc-ceiling','int-wooden-ceiling','int-metal-ceiling','int-grid-ceiling','int-stretch-ceiling','int-coffered-ceiling','int-cove-ceiling','int-tray-ceiling','int-wooden-beam-ceiling','int-acoustic-ceiling'],
    'int-kitchen' => ['int-modular-kitchen','int-l-shaped-kitchen','int-u-shaped-kitchen','int-straight-kitchen','int-parallel-kitchen','int-island-kitchen','int-open-kitchen','int-closed-kitchen','int-laminate-kitchen-cabinets','int-acrylic-kitchen-cabinets','int-pu-finish-cabinets','int-membrane-kitchen-cabinets','int-veneer-kitchen-cabinets','int-granite-countertop','int-quartz-countertop','int-marble-countertop','int-corian-countertop','int-stainless-steel-countertop','int-stainless-steel-sink','int-granite-sink','int-ceramic-sink','int-kitchen-chimney'],
    'int-wardrobe-storage' => ['int-sliding-wardrobe','int-hinged-wardrobe','int-walk-in-wardrobe','int-modular-wardrobe','int-loft-wardrobe','int-mirror-wardrobe','int-laminate-wardrobe','int-veneer-wardrobe','int-acrylic-wardrobe','int-pu-finish-wardrobe','int-shoe-rack-units','int-crockery-units'],
    'int-bathroom-sanitaryware' => ['int-wall-mounted-wc','int-floor-mounted-wc','int-one-piece-wc','int-wash-basin','int-counter-basin','int-pedestal-basin','int-bathtub','int-shower-enclosure','int-shower-panel','int-health-faucet','int-diverter-mixer-tap','int-rain-shower','int-geyser-water-heater','int-bathroom-vanity-unit'],
    'int-lighting' => ['int-chandeliers','int-pendant-lights','int-led-panel-lights','int-cove-lighting','int-spotlights','int-track-lighting','int-wall-sconces','int-recessed-lighting','int-decorative-table-lamps','int-outdoor-garden-lighting','int-smart-lighting'],
    'int-furniture' => ['int-sofa-sets','int-dining-tables','int-beds','int-tv-units','int-study-tables','int-bookshelves','int-recliners','int-ottomans','int-coffee-tables','int-console-tables'],
    'int-curtains-blinds-soft-furnishing' => ['int-roller-blinds','int-roman-blinds','int-vertical-blinds','int-wooden-blinds','int-sheer-curtains','int-blackout-curtains','int-motorised-curtains','int-cushion-covers','int-bed-linen'],
    'int-hardware-fittings' => ['int-door-handles-locks','int-hinges','int-cabinet-handles','int-drawer-channels','int-soft-close-fittings','int-door-closers','int-magic-corner-fittings','int-wardrobe-accessories'],
];
$pillCategories = [];
if ($category !== '' && isset($interiorGroups[$category])) {
    $pillCategories = $interiorGroups[$category];
} elseif ($category !== '') {
    foreach ($interiorGroups as $group => $children) {
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
    $where[] = "(product_name LIKE ? OR description LIKE ? OR brand LIKE ?)";
    $like = "%{$search}%";
    $types .= 'sss';
    $params[] = $like; $params[] = $like; $params[] = $like;
}
if ($brand !== '') {
    $where[] = "brand = ?";
    $types .= 's';
    $params[] = $brand;
}
if ($category !== '') {
    if (isset($interiorGroups[$category])) {
        $placeholders = implode(',', array_fill(0, count($interiorGroups[$category]), '?'));
        $where[] = "category IN ($placeholders)";
        foreach ($interiorGroups[$category] as $c) { $types .= 's'; $params[] = $c; }
    } else {
        $where[] = "category = ?";
        $types .= 's';
        $params[] = $category;
    }
}
$whereSql = implode(' AND ', $where);
$orderSql = "created_at DESC";

$brands = [];
if ($res = $conn->query("SELECT DISTINCT brand FROM interiors WHERE status='Active' AND brand IS NOT NULL AND brand <> '' ORDER BY brand ASC")) {
    while ($row = $res->fetch_assoc()) { $brands[] = $row['brand']; }
}

$sql  = "SELECT * FROM interiors WHERE {$whereSql} ORDER BY {$orderSql}";
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
$products   = array_slice($allMatches, $offset, $perPage);

$pageTitle = $categoryLabel !== '' ? $categoryLabel : 'Shop Interiors';
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
        <p>Doors, flooring, kitchens, lighting and everything else to finish your home</p>
    </div>

    <div class="search-card">
        <form method="GET" id="filterForm">
            <?php if ($sort !== 'Newest'): ?><input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>"><?php endif; ?>
            <?php if ($category !== ''): ?><input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>"><?php endif; ?>

            <div class="search-input-group mb-3">
                <span class="input-group-text bg-transparent border-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control search-input"
                       placeholder="Search products by name, brand or keyword"
                       value="<?php echo htmlspecialchars($search); ?>">
                <button class="search-btn" type="submit">Search</button>
            </div>

            <div class="filters-grid">
                <div class="filter-col">
                    <label>Brand</label>
                    <select name="brand" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">All Brands</option>
                        <?php foreach ($brands as $b): ?>
                            <option value="<?php echo htmlspecialchars($b); ?>" <?php echo $brand === $b ? 'selected' : ''; ?>><?php echo htmlspecialchars($b); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-col">
                    <label>Sort By</label>
                    <select onchange="window.location.href=this.value" class="form-select">
                        <?php foreach (['Newest','Price Low-High','Price High-Low'] as $val):
                            $q = $_GET; $q['sort'] = $val; $sortUrl = 'interiors.php?' . http_build_query($q);
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
                    <a href="interiors.php" class="clear-btn text-decoration-none d-inline-flex align-items-center gap-2">
                        <i class="bi bi-funnel"></i> Clear all filters
                    </a>
                </div>
            </div>

            <div class="active-filters">
                <?php $hasActive = false; ?>
                <?php if ($search !== ''): $hasActive = true; ?>
                    <a href="<?php echo urlWithoutParam('interiors.php', 'search'); ?>" class="chip">Search: <?php echo htmlspecialchars($search); ?> <i class="bi bi-x"></i></a>
                <?php endif; ?>
                <?php if ($brand !== ''): $hasActive = true; ?>
                    <a href="<?php echo urlWithoutParam('interiors.php', 'brand'); ?>" class="chip">Brand: <?php echo htmlspecialchars($brand); ?> <i class="bi bi-x"></i></a>
                <?php endif; ?>
                <?php if ($category !== ''): $hasActive = true; ?>
                    <a href="<?php echo urlWithoutParam('interiors.php', 'category'); ?>" class="chip">Category: <?php echo htmlspecialchars($categoryLabel); ?> <i class="bi bi-x"></i></a>
                <?php endif; ?>
                <?php if (!$hasActive): ?><span class="text-muted small">No filters applied</span><?php endif; ?>
            </div>
        </form>

        <?php if (!empty($pillCategories)): ?>
        <div class="category-pill-row">
            <?php foreach ($pillCategories as $slug):
                $q = $_GET; $q['category'] = $slug; unset($q['page']);
            ?>
                <a class="category-pill <?php echo $category === $slug ? 'active' : ''; ?>" href="interiors.php?<?php echo http_build_query($q); ?>">
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
    <h2 class="fw-bold mb-4"><?php echo (int) $total; ?> Product<?php echo $total === 1 ? '' : 's'; ?> Found</h2>

    <?php if ($dbError): ?>
        <div class="alert alert-warning">
            Interior product listings aren't set up yet — the <code>interiors</code> table is missing or misconfigured
            (<?php echo htmlspecialchars($dbError); ?>). See <code>schema.sql</code> for the table this page expects.
        </div>
    <?php elseif (empty($products)): ?>
        <div class="alert alert-warning">No products found matching your filters.</div>
    <?php else: ?>
        <div class="listing-grid">
            <?php foreach ($products as $p):
                $imagePath = !empty($p['image']) ? "../backend/uploads/interiors/" . $p['image'] : "assets/images/interior-placeholder.jpg";
                $slug = !empty($p['slug']) ? $p['slug'] : null;
                $url  = $slug ? "interior-listing/{$slug}/" : '#';
                $catLabel = categorySlugToLabel(pv($p, 'category', ''), $CATEGORY_PREFIX);
            ?>
            <a href="<?php echo htmlspecialchars($url); ?>" class="listing-card">
                <div class="listing-image">
                    <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($p['product_name']); ?>">
                    <?php if ($catLabel !== ''): ?><span class="category-badge"><?php echo htmlspecialchars($catLabel); ?></span><?php endif; ?>
                </div>
                <div class="listing-body">
                    <h3 class="listing-title"><?php echo htmlspecialchars($p['product_name']); ?></h3>
                    <?php if (!empty($p['brand'])): ?>
                        <div class="listing-meta"><i class="bi bi-tag me-1"></i><?php echo htmlspecialchars($p['brand']); ?></div>
                    <?php endif; ?>
                    <div class="listing-footer">
                        <span class="price-tag"><?php echo htmlspecialchars(pv($p, 'price', 'Price on Request')); ?></span>
                        <span class="contact-btn">View Details</span>
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
            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>"><a class="page-link" href="interiors.php?<?php echo http_build_query($prevQ); ?>">&laquo;</a></li>
            <?php for ($i = 1; $i <= $totalPages; $i++): $pQ = $_GET; $pQ['page'] = $i; ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>"><a class="page-link" href="interiors.php?<?php echo http_build_query($pQ); ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>"><a class="page-link" href="interiors.php?<?php echo http_build_query($nextQ); ?>">&raquo;</a></li>
        </ul>
    </nav>
    <?php endif; ?>
</div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
