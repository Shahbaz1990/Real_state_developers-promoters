<?php
require_once __DIR__ . '/includes/config.php';

/*
 |--------------------------------------------------------------------------
 | Actual `trending_projects` table columns in use here:
 | id, project_name, slug, address, price, image, project_order, status,
 | bedrooms, bathrooms, area, amenities, created_at
 |
 | region, city, property_type, listing_type, rating, lat/lng still don't
 | exist on this table, so Location / Property Type / Listing Type are shown
 | as disabled placeholders (matching the design) until those columns exist.
 |--------------------------------------------------------------------------
 */

// Convert a price string like "₹1,76,00,000" into whole lakhs (176)
function priceToLakhs(string $price): int {
    $digits = preg_replace('/\D/', '', $price);
    return $digits === '' ? 0 : intval($digits / 100000);
}

function pv($row, $key, $fallback = 'N/A') {
    return (!empty($row[$key])) ? $row[$key] : $fallback;
}

$search    = isset($_GET['search']) ? trim($_GET['search']) : '';
$bedrooms  = isset($_GET['bedrooms']) && $_GET['bedrooms'] !== '' ? (int) $_GET['bedrooms'] : null;
$bathrooms = isset($_GET['bathrooms']) && $_GET['bathrooms'] !== '' ? (int) $_GET['bathrooms'] : null;
$sort      = isset($_GET['sort']) ? trim($_GET['sort']) : 'Newest';
$page      = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$perPage   = 6;

// ===============================
// Build the WHERE clause for everything that's a real, filterable column
// ===============================
$where  = ["status = 'Active'"];
$types  = '';
$params = [];

if ($search !== '') {
    $where[] = "(project_name LIKE ? OR address LIKE ?)";
    $like = "%{$search}%";
    $types .= 'ss';
    $params[] = $like; $params[] = $like;
}
if ($bedrooms !== null) {
    $where[] = "bedrooms = ?";
    $types .= 'i'; $params[] = $bedrooms;
}
if ($bathrooms !== null) {
    $where[] = "bathrooms = ?";
    $types .= 'i'; $params[] = $bathrooms;
}
$whereSql = implode(' AND ', $where);

$orderSql = "created_at DESC";
if ($sort === 'Price Low-High') $orderSql = "CAST(REGEXP_REPLACE(price, '[^0-9]', '') AS UNSIGNED) ASC";
if ($sort === 'Price High-Low') $orderSql = "CAST(REGEXP_REPLACE(price, '[^0-9]', '') AS UNSIGNED) DESC";

// ===============================
// Property Type options — from menus.menu_name
// ===============================
$propertyType = isset($_GET['property_type']) ? trim($_GET['property_type']) : '';
$menus = [];
$menuResult = $conn->query("SELECT menu_name FROM menus WHERE status='Active' ORDER BY menu_order ASC, menu_name ASC");
if ($menuResult) { while ($m = $menuResult->fetch_assoc()) { $menus[] = $m['menu_name']; } }

// ===============================
// Location options — from cities.city_name
// ===============================
$city = isset($_GET['city']) ? trim($_GET['city']) : '';
$cities = [];
$cityResult = $conn->query("SELECT city_name FROM cities WHERE status='Active' ORDER BY city_order ASC, city_name ASC");
if ($cityResult) { while ($c = $cityResult->fetch_assoc()) { $cities[] = $c['city_name']; } }

// ===============================
// Pull matching rows — price-range filtering happens in PHP below since
// price is stored as free-text (e.g. "₹1,76,00,000"), not a number
// ===============================
$sql  = "SELECT * FROM trending_projects WHERE {$whereSql} ORDER BY {$orderSql}";
$stmt = $conn->prepare($sql);
if ($types !== '') { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$res = $stmt->get_result();
$allMatches = [];
while ($row = $res->fetch_assoc()) { $allMatches[] = $row; }
$stmt->close();

// Slider ceiling: use the highest price actually in the data, rounded up
$maxPriceLakhs = 0;
foreach ($allMatches as $row) {
    $maxPriceLakhs = max($maxPriceLakhs, priceToLakhs($row['price']));
}
if ($maxPriceLakhs <= 0) { $maxPriceLakhs = 650; }
$sliderMax = (int) (ceil($maxPriceLakhs / 10) * 10);

$minPrice = isset($_GET['min']) && $_GET['min'] !== '' ? intval($_GET['min']) : 0;
$maxPrice = isset($_GET['max']) && $_GET['max'] !== '' ? intval($_GET['max']) : $sliderMax;
if ($maxPrice > $sliderMax) { $maxPrice = $sliderMax; }
if ($minPrice > $maxPrice) { $minPrice = $maxPrice; }

// ===============================
// Apply price filter in PHP, then paginate the filtered set
// ===============================
$filtered = array_values(array_filter($allMatches, function ($row) use ($minPrice, $maxPrice) {
    $lakhs = priceToLakhs($row['price']);
    return $lakhs >= $minPrice && $lakhs <= $maxPrice;
}));

$total      = count($filtered);
$totalPages = max(1, (int) ceil($total / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;
$properties = array_slice($filtered, $offset, $perPage);

// ===============================
// Active filter chips
// ===============================
function urlWithout($param) {
    $query = $_GET;
    unset($query[$param]);
    unset($query['page']);
    $qs = http_build_query($query);
    return 'properties.php' . ($qs !== '' ? '?' . $qs : '');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Properties</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/property-search.css">
    <style>
      :root{ --ink:#12213a; --muted:#6b7686; --line:#e7e9ee; --bg-soft:#f7f8fa; --blue:#1f6feb; }

      .search-card{ border:1px solid var(--line); border-radius:1rem; padding:1.75rem; }

      .filters-grid{ display:grid; grid-template-columns:repeat(3, 1fr); gap:1.75rem 2rem; }
      .filter-col label{ font-weight:600; font-size:.92rem; color:var(--ink); margin-bottom:.5rem; display:block; }
      @media (max-width: 991.98px){ .filters-grid{ grid-template-columns:1fr; gap:1.25rem; } }

      .form-select:disabled, .form-select[disabled]{ background-color:var(--bg-soft); color:var(--muted); cursor:not-allowed; }

      .toggle-group{ display:inline-flex; border:1px solid var(--line); border-radius:.5rem; overflow:hidden; }
      .toggle-group .toggle-btn{ border:none; background:#fff; padding:.55rem 1.1rem; font-size:.9rem; font-weight:600; color:var(--muted); }
      .toggle-group .toggle-btn.active{ background:var(--bg-soft); color:var(--ink); }
      .toggle-group .toggle-btn:disabled{ opacity:.55; cursor:not-allowed; }

      .active-filters .chip{
        display:inline-flex; align-items:center; gap:.4rem;
        background:#eaf1ff; color:var(--blue); border-radius:2rem; padding:.4rem .9rem;
        font-size:.85rem; font-weight:600; text-decoration:none; margin:.15rem;
      }
      .active-filters .chip:hover{ background:#dbe9ff; }

      .view-toggle{ display:inline-flex; border:1px solid var(--line); border-radius:.5rem; overflow:hidden; }
      .view-toggle a{ padding:.5rem 1rem; font-size:.9rem; font-weight:600; color:var(--ink); text-decoration:none; display:inline-flex; align-items:center; gap:.35rem; }
      .view-toggle a.active{ background:var(--blue); color:#fff; }
      .view-toggle a.disabled{ color:var(--muted); pointer-events:none; }

      .amenity-chip{ background:var(--bg-soft); border-radius:2rem; padding:.25rem .8rem; font-size:.8rem; margin:.15rem .25rem .15rem 0; display:inline-block; }
      .wishlist-icon-btn{
        position:absolute; top:16px; left:16px;
        width:36px; height:36px; border-radius:50%; border:none;
        background:rgba(255,255,255,.9); display:flex; align-items:center; justify-content:center;
        color:#12213a; font-size:1.05rem; z-index:2; box-shadow:0 2px 8px rgba(18,33,58,.15);
      }
      .wishlist-icon-btn:hover{ background:#fff; color:#e0245e; }
      .wishlist-icon-btn.active{ color:#e0245e; }
      .like-btn{ border:1px solid var(--line); border-radius:50%; width:38px; height:38px; display:inline-flex; align-items:center; justify-content:center; color:var(--muted); background:#fff; }
      .like-btn:hover{ border-color:var(--blue); color:var(--blue); }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<!-- HERO SECTION -->
<section class="hero-section py-4">
<div class="container">
    <div class="text-center mb-4">
        <h2 class="display-6 fw-bold">Find Your Dream Property</h2>
        <p>Discover the perfect home from our curated collection of properties</p>
    </div>

    <div class="search-card">
        <form method="GET" id="filterForm">
            <?php if ($sort !== 'Newest'): ?>
                <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
            <?php endif; ?>
            <!-- Search Input -->
            <div class="input-group input-group-lg mb-4">
                <span class="input-group-text bg-white border-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text"
                       name="search"
                       class="form-control search-input"
                       placeholder="Search by project name or address"
                       value="<?php echo htmlspecialchars($search); ?>">
                <button class="search-btn" type="submit">search</button>
            </div>

            <div class="filters-grid mb-4">

                <!-- Price Range -->
                <div class="filter-col">
                    <label>Price Range</label>
                    <div class="d-flex gap-2 mb-2">
                        <input type="number" name="min" id="minInput" class="form-control" placeholder="Min" value="<?php echo $minPrice > 0 ? $minPrice * 100000 : ''; ?>">
                        <input type="number" name="max" id="maxInput" class="form-control" placeholder="Max" value="<?php echo $maxPrice * 100000; ?>">
                    </div>
                    <input type="range" id="priceRange" class="form-range" min="0" max="<?php echo $sliderMax; ?>" step="10" value="<?php echo $maxPrice; ?>">
                    <div class="d-flex justify-content-between small text-muted">
                        <span>₹0</span>
                        <span>₹<?php echo number_format($sliderMax / 100); ?> Cr</span>
                    </div>
                </div>

                <!-- Location — from cities.city_name -->
                <div class="filter-col">
                    <label>Location</label>
                    <select name="city" class="form-select mb-2" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Select Region</option>
                        <?php foreach ($cities as $c): ?>
                            <option value="<?php echo htmlspecialchars($c); ?>" <?php echo $city === $c ? 'selected' : ''; ?>><?php echo htmlspecialchars($c); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select class="form-select" disabled title="Sub-regions aren't in the cities table yet">
                        <option>Select region first</option>
                    </select>
                </div>

                <!-- Property Type — from menus.menu_name -->
                <div class="filter-col">
                    <label>Property Type</label>
                    <select name="property_type" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">All Types</option>
                        <?php foreach ($menus as $m): ?>
                            <option value="<?php echo htmlspecialchars($m); ?>" <?php echo $propertyType === $m ? 'selected' : ''; ?>><?php echo htmlspecialchars($m); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Listing Type (needs `listing_type` column — placeholder for now) -->
                <div class="filter-col">
                    <label>Listing Type</label>
                    <div class="toggle-group">
                        <button type="button" class="toggle-btn active" disabled title="Add a listing_type column to trending_projects to enable this">For Sale</button>
                        <button type="button" class="toggle-btn" disabled>For Rent</button>
                    </div>
                </div>

                <!-- Rooms — real columns, fully functional -->
                <div class="filter-col">
                    <label>Rooms</label>
                    <div class="d-flex gap-2">
                        <select name="bedrooms" class="form-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="" <?php echo $bedrooms === null ? 'selected' : ''; ?>>Bedrooms</option>
                            <?php foreach ([1,2,3,4,5] as $n): ?>
                                <option value="<?php echo $n; ?>" <?php echo $bedrooms === $n ? 'selected' : ''; ?>><?php echo $n; ?> BHK</option>
                            <?php endforeach; ?>
                        </select>
                        <select name="bathrooms" class="form-select" onchange="document.getElementById('filterForm').submit()">
                            <option value="" <?php echo $bathrooms === null ? 'selected' : ''; ?>>Bathrooms</option>
                            <?php foreach ([1,2,3,4] as $n): ?>
                                <option value="<?php echo $n; ?>" <?php echo $bathrooms === $n ? 'selected' : ''; ?>><?php echo $n; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Active Filters -->
                <div class="filter-col">
                    <label>Active Filters</label>
                    <div class="active-filters">
                        <?php
                          $activeMap = [
                              'search'        => $search !== '' ? 'Search: ' . $search : null,
                              'city'          => $city !== '' ? 'Location: ' . $city : null,
                              'property_type' => $propertyType !== '' ? $propertyType : null,
                              'bedrooms'      => $bedrooms !== null ? $bedrooms . ' BHK' : null,
                              'bathrooms'     => $bathrooms !== null ? $bathrooms . ' Bath' : null,
                              'min'           => $minPrice > 0 ? 'Min ₹' . number_format($minPrice * 100000) : null,
                              'max'           => $maxPrice < $sliderMax ? 'Max ₹' . number_format($maxPrice * 100000) : null,
                          ];
                          $hasActive = false;
                          foreach ($activeMap as $param => $label):
                              if ($label === null) continue;
                              $hasActive = true;
                        ?>
                          <a href="<?php echo urlWithout($param); ?>" class="chip"><?php echo htmlspecialchars($label); ?> <i class="bi bi-x"></i></a>
                        <?php endforeach; ?>
                        <?php if (!$hasActive): ?>
                          <span class="text-muted small">No filters applied</span>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </form>

        <div class="mt-2">
            <?php
              $clearUrl = 'properties.php' . ($search !== '' ? '?' . http_build_query(['search' => $search]) : '');
            ?>
            <a href="<?php echo htmlspecialchars($clearUrl); ?>" class="clear-btn text-decoration-none d-inline-flex align-items-center gap-2">
                <i class="bi bi-funnel"></i>
                Clear all
            </a>
        </div>
    </div>
</div>
</section>

<!-- RESULTS SECTION -->
<section class="py-5">
<div class="container">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-2">
        <h2 class="fw-bold mb-0">
            <?php
            if ($search !== '') {
                echo (int) $total . ' Results for "<strong>' . htmlspecialchars($search) . '</strong>"';
            } else {
                echo (int) $total . ' Properties Found';
            }
            ?>
        </h2>
        <div class="d-flex align-items-center gap-2">
            <div class="view-toggle">
                <a href="#" class="active"><i class="bi bi-grid"></i> List</a>
                <a href="#" class="disabled" title="Needs lat/lng columns to enable"><i class="bi bi-map"></i> Map</a>
            </div>
            <select class="form-select w-auto" onchange="window.location.href=this.value">
                <?php foreach (['Newest' => 'Newest', 'Price Low-High' => 'Price Low-High', 'Price High-Low' => 'Price High-Low'] as $val => $label):
                    $q = $_GET; $q['sort'] = $val;
                    $sortUrl = 'properties.php?' . http_build_query($q);
                ?>
                    <option value="<?php echo htmlspecialchars($sortUrl); ?>" <?php echo $sort === $val ? 'selected' : ''; ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <?php if (!empty($properties)): ?>
            <?php foreach ($properties as $property):
                $imagePath = !empty($property['image'])
                    ? "../backend/uploads/trending-projects/" . $property['image']
                    : "../backend/uploads/trending-projects/project_6a5df445d17f17.80638806.jpg";
                $amenities = !empty($property['amenities']) ? array_slice(explode(',', $property['amenities']), 0, 4) : [];
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="property-card">
                    <div class="property-image">
                        <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($property['project_name']); ?>">
                        <span class="sale-badge">For Sale</span>
                        <button type="button" class="wishlist-icon-btn" data-project-id="<?php echo (int) $property['id']; ?>" aria-label="Add to wishlist">
                            <i class="bi bi-heart"></i>
                        </button>
                    </div>
                    <div class="property-body">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <h3 class="property-title mb-2"><?php echo htmlspecialchars($property['project_name']); ?></h3>
                        </div>
                        <div class="property-location">
                            <i class="bi bi-geo-alt me-1"></i>
                            <?php echo htmlspecialchars(pv($property, 'address', '')); ?>
                        </div>
                        <div class="d-flex gap-3 my-2 small text-muted">
                            <?php if (!empty($property['bedrooms'])): ?><span><i class="bi bi-door-open"></i> <?php echo (int) $property['bedrooms']; ?> Beds</span><?php endif; ?>
                            <?php if (!empty($property['bathrooms'])): ?><span><i class="bi bi-droplet"></i> <?php echo (int) $property['bathrooms']; ?> Baths</span><?php endif; ?>
                            <?php if (!empty($property['area'])): ?><span><i class="bi bi-aspect-ratio"></i> <?php echo htmlspecialchars($property['area']); ?></span><?php endif; ?>
                        </div>
                        <?php foreach ($amenities as $a): ?>
                            <span class="amenity-chip"><?php echo htmlspecialchars(trim($a)); ?></span>
                        <?php endforeach; ?>
                        <div class="property-footer mt-2 d-flex justify-content-between align-items-center">
                            <div class="property-price"><?php echo htmlspecialchars(pv($property, 'price', 'Price on Request')); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning">No properties found matching your filters.</div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <?php
              $prevQuery = $_GET; $prevQuery['page'] = max(1, $page - 1);
              $nextQuery = $_GET; $nextQuery['page'] = min($totalPages, $page + 1);
            ?>
            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="properties.php?<?php echo http_build_query($prevQuery); ?>">&laquo;</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): $pQuery = $_GET; $pQuery['page'] = $i; ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                    <a class="page-link" href="properties.php?<?php echo http_build_query($pQuery); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="properties.php?<?php echo http_build_query($nextQuery); ?>">&raquo;</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const priceRange = document.getElementById("priceRange");
    const minInput   = document.getElementById("minInput");
    const maxInput   = document.getElementById("maxInput");

    function indianFormat(num) {
        return Number(num).toLocaleString("en-IN");
    }

    priceRange.addEventListener("input", () => {
        maxInput.value = priceRange.value * 100000;
    });
    priceRange.addEventListener("change", () => {
        document.getElementById('filterForm').submit();
    });
</script>

<script>
// ============ Wishlist (same shared storage as trending-projects.php + header.php) ============
(function(){
    const STORAGE_KEY = 'tatito_wishlist';

    function getWishlist() {
        try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || []; }
        catch (e) { return []; }
    }
    function saveWishlist(list) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(list));
    }

    const wishlist = getWishlist();

    document.querySelectorAll('.wishlist-icon-btn').forEach(function(btn){
        const id = btn.dataset.projectId;
        const icon = btn.querySelector('i');

        if (wishlist.includes(id)) {
            btn.classList.add('active');
            icon.classList.remove('bi-heart');
            icon.classList.add('bi-heart-fill');
        }

        btn.addEventListener('click', function(e){
            e.preventDefault();
            e.stopPropagation();

            let list = getWishlist();
            const isActive = btn.classList.toggle('active');

            if (isActive) {
                if (!list.includes(id)) list.push(id);
                icon.classList.remove('bi-heart');
                icon.classList.add('bi-heart-fill');
            } else {
                list = list.filter(x => x !== id);
                icon.classList.remove('bi-heart-fill');
                icon.classList.add('bi-heart');
            }

            saveWishlist(list);
        });
    });
})();
</script>
</body>
</html>