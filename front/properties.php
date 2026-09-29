<?php
// Start session FIRST - before ANY output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/config.php';

/*
 |--------------------------------------------------------------------------
 | Actual `trending_projects` table columns in use here:
 | id, project_name, slug, address, city, price, image, project_order,
 | status, bedrooms, bathrooms, area, amenities, created_at
 |--------------------------------------------------------------------------
 */

// Convert any stored price string into whole lakhs
function priceToLakhs(string $price): int {
    $price = trim($price);
    if ($price === '') return 0;

    if (preg_match('/([\d,]+(?:\.\d+)?)\s*(cr|crore|lakh|lac|l)\b/i', $price, $m)) {
        $numRaw     = str_replace(',', '', $m[1]);
        $hasDecimal = strpos($numRaw, '.') !== false;
        $unit       = strtolower($m[2]);
        $isCr       = (strpos($unit, 'cr') === 0);

        if (!$hasDecimal && (int) $numRaw >= 1000) {
            $digits = preg_replace('/\D/', '', $price);
            return $digits === '' ? 0 : intval($digits / 100000);
        }

        $num = (float) $numRaw;
        return (int) round($isCr ? $num * 100 : $num);
    }

    $digits = preg_replace('/\D/', '', $price);
    return $digits === '' ? 0 : intval($digits / 100000);
}

function formatPriceDisplay($price) {
    if (empty($price)) return 'Price on Request';
    $price = trim($price);

    if (preg_match('/-/', $price) && preg_match('/cr|lakh|lac|\bl\b/i', $price)) {
        return (strpos($price, '₹') === false ? '₹' : '') . $price;
    }

    $lakhs = priceToLakhs($price);
    if ($lakhs <= 0) return $price;

    if ($lakhs >= 100) {
        return '₹' . rtrim(rtrim(number_format($lakhs / 100, 2), '0'), '.') . ' Cr';
    }
    return '₹' . rtrim(rtrim(number_format($lakhs, 2), '0'), '.') . ' L';
}

function pv($row, $key, $fallback = 'N/A') {
    return (!empty($row[$key])) ? $row[$key] : $fallback;
}

function formatAddressDisplay($address, $limit = 2) {
    if (empty($address)) return '';
    $parts = array_map('trim', explode(',', $address));
    $parts = array_filter($parts, fn($p) => $p !== '');
    $parts = array_values($parts);
    if (count($parts) > $limit) {
        $parts = array_slice($parts, -$limit);
    }
    return implode(', ', $parts);
}

function lakhsToDisplay($lakhs) {
    if ($lakhs <= 0) return '₹0';
    if ($lakhs >= 100) {
        $cr = $lakhs / 100;
        return '₹' . rtrim(rtrim(number_format($cr, 2), '0'), '.') . ' Cr';
    }
    return '₹' . number_format($lakhs) . ' L';
}

// --- Get URL Parameters ---
$search    = isset($_GET['search']) ? trim($_GET['search']) : '';
$bedrooms  = isset($_GET['bedrooms']) && $_GET['bedrooms'] !== '' ? (int) $_GET['bedrooms'] : null;
$bathrooms = isset($_GET['bathrooms']) && $_GET['bathrooms'] !== '' ? (int) $_GET['bathrooms'] : null;
$city      = isset($_GET['city']) ? trim($_GET['city']) : '';
$region    = isset($_GET['region']) ? trim($_GET['region']) : '';
$categorySlug = isset($_GET['category']) ? trim($_GET['category']) : '';
$sort      = isset($_GET['sort']) ? trim($_GET['sort']) : 'Newest';
$page      = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$perPage   = 10; // Changed to 10

// --- Determine Dynamic Page Title & Category Context ---
$pageTitle = "Find Your Dream Property";
$categoryNameForTitle = '';
$categorySlugForTitle = $categorySlug;

// Fetch category name if a slug is provided
if ($categorySlug) {
    $catStmt = $conn->prepare("SELECT name, slug FROM property_categories WHERE slug = ? AND status = 'Active' LIMIT 1");
    if ($catStmt) {
        $catStmt->bind_param("s", $categorySlug);
        $catStmt->execute();
        $catResult = $catStmt->get_result();
        if ($catRow = $catResult->fetch_assoc()) {
            $categoryNameForTitle = $catRow['name'];
            $categorySlugForTitle = $catRow['slug'];
        }
        $catStmt->close();
    }
}

// Build the title based on category name
if ($categoryNameForTitle) {
    $pageTitle = "Find Your " . $categoryNameForTitle . " Dream Property";
}

// Hero description text based on category
$heroDescription = "Discover the perfect home from our curated collection of properties";
// Only show "Dream Property" and "properties" for Buy & Rent categories
$buyRentSlugs = ['buy', 'rent', 'buy-property', 'rent-property'];
if (!in_array($categorySlugForTitle, $buyRentSlugs) && !empty($categorySlugForTitle)) {
    $pageTitle = "Find Your " . $categoryNameForTitle;
    $heroDescription = "Explore our curated collection of " . strtolower($categoryNameForTitle) . " properties";
} else if (empty($categorySlugForTitle)) {
    $pageTitle = "Find Your Dream Property";
    $heroDescription = "Discover the perfect home from our curated collection of properties";
}

// ===============================
// Build the WHERE clause
// ===============================
$where  = ["status = 'Active'"];
$types  = '';
$params = [];

if ($search !== '') {
    $where[] = "(project_name LIKE ? OR address LIKE ?)";
    $like = "%{$search}%";
    $types .= 'ss';
    $params[] = $like; 
    $params[] = $like;
}

if ($city !== '') {
    $where[] = "city = ?";
    $types .= 's';
    $params[] = $city;
}

if ($region !== '') {
    $where[] = "address LIKE ?";
    $types .= 's';
    $params[] = "%{$region}%";
}

// --- Category Filter - Use category_id (and all its descendants) ---
if ($categorySlug) {
    $catIdStmt = $conn->prepare("SELECT id FROM property_categories WHERE slug = ? AND status = 'Active' LIMIT 1");
    if ($catIdStmt) {
        $catIdStmt->bind_param("s", $categorySlug);
        $catIdStmt->execute();
        $catIdResult = $catIdStmt->get_result();
        if ($catIdRow = $catIdResult->fetch_assoc()) {
            $categoryId = $catIdRow['id'];

            // Get this category ID plus every descendant category ID
            $categoryIds = [];
            $catTreeSql = "
                WITH RECURSIVE cat_tree AS (
                    SELECT id FROM property_categories WHERE id = ?
                    UNION ALL
                    SELECT c.id FROM property_categories c
                    INNER JOIN cat_tree t ON c.parent_id = t.id
                )
                SELECT id FROM cat_tree
            ";
            $catTreeStmt = $conn->prepare($catTreeSql);
            if ($catTreeStmt) {
                $catTreeStmt->bind_param("i", $categoryId);
                $catTreeStmt->execute();
                $catTreeResult = $catTreeStmt->get_result();
                while ($row = $catTreeResult->fetch_assoc()) {
                    $categoryIds[] = (int) $row['id'];
                }
                $catTreeStmt->close();
            }

            if (!empty($categoryIds)) {
                $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
                $where[] = "category_id IN ($placeholders)";
                $types .= str_repeat('i', count($categoryIds));
                foreach ($categoryIds as $cid) {
                    $params[] = $cid;
                }
            }
        }
        $catIdStmt->close();
    }
}

if ($bedrooms !== null) {
    $where[] = "bedrooms = ?";
    $types .= 'i'; 
    $params[] = $bedrooms;
}
if ($bathrooms !== null) {
    $where[] = "bathrooms = ?";
    $types .= 'i'; 
    $params[] = $bathrooms;
}
$whereSql = implode(' AND ', $where);

$orderSql = "created_at DESC";
if ($sort === 'Price Low-High') $orderSql = "CAST(REGEXP_REPLACE(price, '[^0-9]', '') AS UNSIGNED) ASC";
if ($sort === 'Price High-Low') $orderSql = "CAST(REGEXP_REPLACE(price, '[^0-9]', '') AS UNSIGNED) DESC";

// Fetch Region + City options
$regionCities = [];
$locResult = $conn->query("SELECT DISTINCT address, city FROM trending_projects WHERE status='Active' AND address IS NOT NULL AND address <> ''");
if ($locResult) {
    while ($row = $locResult->fetch_assoc()) {
        $regionName = '';
        $cityName = $row['city'];
        
        if (!empty($row['address'])) {
            $addrParts = array_values(array_filter(array_map('trim', explode(',', $row['address'])), fn($p) => $p !== ''));
            $first = reset($addrParts);
            if ($first !== false) {
                $regionName = $first;
            }
            if (empty($cityName) && count($addrParts) >= 2) {
                $cityName = end($addrParts);
                $cityName = trim($cityName);
            }
        }
        
        if ($regionName === '') { $regionName = 'Other'; }
        if (empty($cityName)) { $cityName = 'Unknown'; }

        if (!isset($regionCities[$regionName])) {
            $regionCities[$regionName] = [];
        }
        if (!in_array($cityName, $regionCities[$regionName], true)) {
            $regionCities[$regionName][] = $cityName;
        }
    }
}
ksort($regionCities);
foreach ($regionCities as $rn => $cityArr) {
    sort($regionCities[$rn]);
}

$allCitiesFlat = [];
foreach ($regionCities as $cityArr) {
    foreach ($cityArr as $cn) {
        if (!in_array($cn, $allCitiesFlat, true)) $allCitiesFlat[] = $cn;
    }
}
sort($allCitiesFlat);

// Fetch matching projects
$sql  = "SELECT * FROM trending_projects WHERE {$whereSql} ORDER BY {$orderSql}";
$stmt = $conn->prepare($sql);
if ($types !== '') { 
    $stmt->bind_param($types, ...$params); 
}
$stmt->execute();
$res = $stmt->get_result();
$allMatches = [];
while ($row = $res->fetch_assoc()) { 
    $allMatches[] = $row; 
}
$stmt->close();

// Slider range
$dbMinLakhs = null;
$dbMaxLakhs = 0;
foreach ($allMatches as $row) {
    $l = priceToLakhs($row['price']);
    if ($l <= 0) continue;
    if ($dbMinLakhs === null || $l < $dbMinLakhs) { $dbMinLakhs = $l; }
    if ($l > $dbMaxLakhs) { $dbMaxLakhs = $l; }
}
if ($dbMinLakhs === null) { $dbMinLakhs = 0; }
if ($dbMaxLakhs <= 0) { $dbMaxLakhs = 650; }

$sliderMax = (int) (ceil($dbMaxLakhs / 10) * 10);

$minPrice = isset($_GET['min']) && $_GET['min'] !== '' ? intval(intval($_GET['min']) / 100000) : 0;
$maxPrice = isset($_GET['max']) && $_GET['max'] !== '' ? intval(intval($_GET['max']) / 100000) : $sliderMax;
if ($maxPrice > $sliderMax) { $maxPrice = $sliderMax; }
if ($minPrice > $maxPrice) { $minPrice = $maxPrice; }

// Price filtering & Pagination
$filtered = array_values(array_filter($allMatches, function ($row) use ($minPrice, $maxPrice) {
    $lakhs = priceToLakhs($row['price']);
    return $lakhs >= $minPrice && $lakhs <= $maxPrice;
}));

$total      = count($filtered);
$totalPages = max(1, (int) ceil($total / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;
$properties = array_slice($filtered, $offset, $perPage);

function urlWithout($param) {
    $query = $_GET;
    unset($query[$param]);
    unset($query['page']);
    $qs = http_build_query($query);
    return 'properties.php' . ($qs !== '' ? '?' . $qs : '');
}

$mapPayload = array_map(function ($p) {
    $slug = !empty($p['slug']) ? $p['slug'] : null;
    return [
        'id'      => (int) $p['id'],
        'name'    => $p['project_name'],
        'address' => pv($p, 'address', ''),
        'price'   => pv($p, 'price', 'Price on Request'),
        'url'     => $slug ? "property-listing/{$slug}/" : null,
    ];
}, $properties);

// --- Fetch Categories for Tabs (scoped to the current category's type) ---
$categories = [];

// Determine which category_type we're browsing, from the current $categorySlug
$tabCategoryType = null;
if ($categorySlug) {
    $ctStmt = $conn->prepare("SELECT category_type FROM property_categories WHERE slug = ? AND status = 'Active' LIMIT 1");
    if ($ctStmt) {
        $ctStmt->bind_param("s", $categorySlug);
        $ctStmt->execute();
        $ctResult = $ctStmt->get_result();
        if ($ctRow = $ctResult->fetch_assoc()) {
            $tabCategoryType = $ctRow['category_type'];
        }
        $ctStmt->close();
    }
}

// Default to 'buy' when nothing is selected yet
if (!$tabCategoryType) {
    $tabCategoryType = 'buy';
}

// Find the root category id for this type (the parent_id = 0 row of that type)
$tabRootId = null;
$rootStmt = $conn->prepare("SELECT id FROM property_categories WHERE category_type = ? AND parent_id = 0 AND status = 'Active' LIMIT 1");
if ($rootStmt) {
    $rootStmt->bind_param("s", $tabCategoryType);
    $rootStmt->execute();
    $rootResult = $rootStmt->get_result();
    if ($rootRow = $rootResult->fetch_assoc()) {
        $tabRootId = (int) $rootRow['id'];
    }
    $rootStmt->close();
}

// Pull only the level-2 categories under that root
// (e.g. Apartment/Flat, Villa, Agricultural Land — siblings, not every category in the table)
if ($tabRootId) {
    $catStmt2 = $conn->prepare(
        "SELECT c.id, c.name, c.slug, c.parent_id
         FROM property_categories c
         INNER JOIN property_categories p ON c.parent_id = p.id
         WHERE c.status = 'Active'
           AND c.category_type = ?
           AND p.parent_id = ?
         ORDER BY c.category_order ASC, c.name ASC"
    );
    if ($catStmt2) {
        $catStmt2->bind_param("si", $tabCategoryType, $tabRootId);
        $catStmt2->execute();
        $catResult2 = $catStmt2->get_result();
        while ($row = $catResult2->fetch_assoc()) {
            $categories[] = $row;
        }
        $catStmt2->close();
    }
}

// Show all active categories
$tabCategories = $categories;

// Split categories into visible and more
$visibleCategories = array_slice($tabCategories, 0, 8);
$moreCategories = array_slice($tabCategories, 8);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="icon" type="image/png" href="<?php echo $site['favicon']; ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $site['favicon']; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/property-search.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    
<style>
:root{
    --ink:#12213a;
    --muted:#6b7686;
    --line:#e7e9ee;
    --bg-soft:#f7f8fa;
    --blue:#1f6feb;
    --gold:#f5b942;
    --card-bg:#ffffff;
    --shadow-color:rgba(18,33,58,.08);
    --shadow-hover:rgba(18,33,58,.14);
    --dm-bg-primary:#0d1117;
    --dm-bg-secondary:#161b22;
    --dm-bg-tertiary:#1c2333;
    --dm-text-primary:#f0f6fc;
    --dm-text-secondary:#8b949e;
    --dm-border:#30363d;
    --header-bg: #0a101c;
    --header-text: #ffffff;
}

body.dark-mode {
    --ink: #f0f6fc;
    --muted: #8b949e;
    --line: #30363d;
    --bg-soft: #161b22;
    --card-bg: #0d1117;
    --shadow-color: rgba(0,0,0,0.3);
    --shadow-hover: rgba(0,0,0,0.5);
    --header-bg: #0d1117;
    --header-text: #f0f6fc;
}

body.dark-mode {
    background: var(--dm-bg-primary, #0d1117);
    color: var(--dm-text-primary, #f0f6fc);
}

body.dark-mode .search-card {
    background: var(--dm-bg-secondary, #161b22);
}

body.dark-mode .search-input-group {
    background: var(--dm-bg-tertiary, #1c2333);
    border-color: var(--dm-border, #30363d);
}

body.dark-mode .search-input-group .search-input {
    color: var(--dm-text-primary, #f0f6fc);
    background: var(--dm-bg-tertiary, #1c2333);
}

body.dark-mode .search-input-group .search-input::placeholder {
    color: var(--dm-text-secondary, #8b949e);
}

body.dark-mode .filter-col label {
    color: var(--dm-text-secondary, #8b949e);
}

body.dark-mode .form-select,
body.dark-mode .form-control {
    background: var(--dm-bg-tertiary, #1c2333);
    border-color: var(--dm-border, #30363d);
    color: var(--dm-text-primary, #f0f6fc);
}

body.dark-mode .form-select:focus,
body.dark-mode .form-control:focus {
    background: var(--dm-bg-tertiary, #1c2333);
    border-color: var(--blue, #1f6feb);
    color: var(--dm-text-primary, #f0f6fc);
}

body.dark-mode .form-select option {
    background: var(--dm-bg-secondary, #161b22);
    color: var(--dm-text-primary, #f0f6fc);
}

body.dark-mode .active-filters .chip {
    background: var(--dm-bg-tertiary, #1c2333);
    color: var(--blue, #1f6feb);
}

body.dark-mode .active-filters .chip:hover {
    background: var(--dm-border, #30363d);
}

body.dark-mode .active-filters .text-muted {
    color: var(--dm-text-secondary, #8b949e) !important;
}

body.dark-mode .clear-btn {
    color: var(--dm-text-secondary, #8b949e);
}

body.dark-mode .clear-btn:hover {
    color: var(--dm-text-primary, #f0f6fc);
}

body.dark-mode .property-card {
    background: var(--dm-bg-secondary, #161b22);
    border-color: var(--dm-border, #30363d);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}

body.dark-mode .property-card:hover {
    box-shadow: 0 16px 34px rgba(0,0,0,0.5);
}

body.dark-mode .property-title {
    color: var(--dm-text-primary, #f0f6fc);
}

body.dark-mode .property-location {
    color: var(--dm-text-secondary, #8b949e);
}

body.dark-mode .property-location i {
    color: var(--blue, #1f6feb);
}

body.dark-mode .feature-beds {
    background: var(--dm-bg-tertiary, #1c2333);
    color: var(--blue, #1f6feb);
}

body.dark-mode .feature-baths {
    background: var(--dm-bg-tertiary, #1c2333);
    color: #3ddc84;
}

body.dark-mode .feature-area {
    background: var(--dm-bg-tertiary, #1c2333);
    color: #a78bfa;
}

body.dark-mode .amenity-chip {
    background: var(--dm-bg-tertiary, #1c2333);
    color: var(--dm-text-secondary, #8b949e);
    border-color: var(--dm-border, #30363d);
}

body.dark-mode .property-footer {
    border-color: var(--dm-border, #30363d);
}

body.dark-mode .property-price {
    background: var(--dm-bg-tertiary, #1c2333);
    color: var(--blue, #1f6feb);
}

body.dark-mode .wishlist-icon-btn {
    background: rgba(255,255,255,0.1);
    color: var(--dm-text-primary, #f0f6fc);
}

body.dark-mode .wishlist-icon-btn:hover {
    background: var(--dm-bg-secondary, #161b22);
    color: #e0245e;
}

body.dark-mode .wishlist-icon-btn.active {
    color: #e0245e;
}

body.dark-mode .sale-badge {
    background: linear-gradient(135deg, var(--gold, #f5b942), #d99b00);
    color: var(--ink, #12213a);
}

body.dark-mode .view-toggle {
    border-color: var(--dm-border, #30363d);
}

body.dark-mode .view-toggle button {
    background: var(--dm-bg-secondary, #161b22);
    color: var(--dm-text-secondary, #8b949e);
}

body.dark-mode .view-toggle button.active {
    background: var(--dm-bg-tertiary, #1c2333);
    color: var(--dm-text-primary, #f0f6fc);
}

body.dark-mode .view-toggle button:hover:not(.active) {
    background: var(--dm-bg-tertiary, #1c2333);
}

body.dark-mode #mapView .leaflet-container {
    background: var(--dm-bg-secondary, #161b22) !important;
}

body.dark-mode .map-loading-overlay {
    background: var(--dm-bg-secondary, #161b22);
    color: var(--dm-text-secondary, #8b949e);
}

body.dark-mode .map-status {
    color: var(--dm-text-secondary, #8b949e);
}

body.dark-mode .map-popup h6 {
    color: var(--dm-text-primary, #f0f6fc);
}

body.dark-mode .map-popup .price {
    color: var(--blue, #1f6feb);
}

body.dark-mode .map-popup .addr {
    color: var(--dm-text-secondary, #8b949e);
}

body.dark-mode .map-popup a.view-link {
    color: var(--blue, #1f6feb);
}

body.dark-mode .page-link {
    background: var(--dm-bg-secondary, #161b22);
    border-color: var(--dm-border, #30363d);
    color: var(--dm-text-secondary, #8b949e);
}

body.dark-mode .page-link:hover {
    background: var(--dm-bg-tertiary, #1c2333);
    color: var(--dm-text-primary, #f0f6fc);
}

body.dark-mode .page-item.active .page-link {
    background: var(--blue, #1f6feb);
    border-color: var(--blue, #1f6feb);
    color: #fff;
}

body.dark-mode .page-item.disabled .page-link {
    background: var(--dm-bg-secondary, #161b22);
    border-color: var(--dm-border, #30363d);
    color: var(--dm-text-secondary, #8b949e);
    opacity: 0.5;
}

body.dark-mode .alert-warning {
    background: var(--dm-bg-tertiary, #1c2333);
    border-color: var(--dm-border, #30363d);
    color: var(--dm-text-secondary, #8b949e);
}

body.dark-mode .text-muted {
    color: var(--dm-text-secondary, #8b949e) !important;
}

body.dark-mode .dropdown-toggle::after {
    color: var(--dm-text-secondary, #8b949e);
}

/* Hero Section - matches header */
.hero-section {
    position: relative;
    background: var(--header-bg, #0a101c);
    padding: 3.5rem 0 6rem;
    overflow: hidden;
    transition: background 0.3s ease;
}

.hero-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 15% 20%, rgba(31,111,235,.25), transparent 45%),
        radial-gradient(circle at 85% 80%, rgba(245,185,66,.15), transparent 45%);
    pointer-events: none;
}

.hero-section .container {
    position: relative;
    z-index: 1;
}

.hero-section h2 {
    color: var(--header-text, #ffffff);
    transition: color 0.3s ease;
}

.hero-section p {
    color: rgba(255,255,255,.75);
    transition: color 0.3s ease;
}

body.dark-mode .hero-section {
    background: var(--dm-bg-primary, #0d1117);
}

body.dark-mode .hero-section h2 {
    color: var(--dm-text-primary, #f0f6fc);
}

body.dark-mode .hero-section p {
    color: var(--dm-text-secondary, #8b949e);
}

.search-card {
    background: #fff;
    border: none;
    border-radius: .8rem;
    padding: 1rem 1.15rem;
    box-shadow: 0 12px 30px rgba(10,16,28,.20);
    margin-top: 1rem;        /* was .25rem */
    transition: background 0.3s ease, box-shadow 0.3s ease;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(3,1fr);
    gap: 1rem 1.25rem;
    margin-bottom: 1rem !important;
}

.filter-col label {
    display: block;
    font-weight: 600;
    font-size: .82rem;
    color: var(--ink);
    margin-bottom: .35rem;
    transition: color 0.3s ease;
}

.search-input-group {
    display: flex;
    align-items: stretch;
    min-height: 48px;
    background: var(--bg-soft);
    border: 1px solid var(--line);
    border-radius: .65rem;
    overflow: hidden;
    margin-bottom: 1rem !important;
    transition: background 0.3s ease, border-color 0.3s ease;
}

.search-field-inner {
    display: flex;
    align-items: center;
    flex: 1 1 auto;
    min-width: 0;
}

.search-input-group .input-group-text {
    background: transparent;
    border: none;
    padding: .5rem .65rem;
        background-color: transparent !important;
}

.search-input-group .search-input {
    flex: 1 1 auto;
    min-width: 0;
    border: none;
    background: transparent;
    box-shadow: none;
    font-size: .9rem;
    padding: .55rem .5rem;
    transition: color 0.3s ease, background 0.3s ease;
}

.search-input-group .search-input:focus {
    box-shadow: none;
    background: transparent;
}

.search-btn {
    border: none;
    background: linear-gradient(135deg,var(--gold),#e0a72f);
    color: var(--ink);
    font-weight: 700;
    font-size: .85rem;
    padding: 0 1.35rem;
    text-transform: capitalize;
    white-space: nowrap;
    transition: filter .15s ease;
}

.filter-col .form-select,
.filter-col .form-control {
    min-height: 40px;
    height: 40px;
    padding: .35rem .7rem;
    font-size: .82rem;
    border-radius: .5rem;
    transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;
}

.form-select:disabled,
.form-select[disabled] {
    background-color: var(--bg-soft);
    color: var(--muted);
    cursor: not-allowed;
}

.filter-col .form-range {
    height: 1rem;
    margin-top: .1rem;
}

/* Category Tabs - 2 Row Limit with More Button */
.category-tabs-wrap {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 0.5rem;
    border-radius: 1.5rem;
    background: rgba(255,255,255,0.04);
    max-height: 85px;
    overflow: hidden;   /* clip anything beyond 2 rows */
    transition: max-height 0.4s ease, background 0.3s ease, padding 0.3s ease;
    position: relative;
}
.category-tabs-wrap.expanded {
    max-height: 600px;
    overflow: visible;  /* only allow the dropdown menu to escape once expanded */
    background: rgba(255,255,255,0.08);
    padding-bottom: 3.5rem;
}

.category-tabs-wrap .category-tab {
    padding: 0.4rem 1rem;
    border-radius: 2rem;
    background: rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.7);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.75rem;
    border: 1px solid rgba(255,255,255,0.08);
    transition: all 0.25s ease;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}

.category-tabs-wrap .category-tab:hover {
    background: rgba(255,255,255,0.15);
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}

.category-tabs-wrap .category-tab.active {
    background: var(--gold, #f5b942);
    color: var(--ink, #12213a);
    border-color: var(--gold, #f5b942);
    box-shadow: 0 4px 15px rgba(245,185,66,0.25);
    font-weight: 700;
}

.category-tabs-wrap .category-tab i {
    font-size: 0.75rem;
}

/* More Dropdown */
.more-dropdown-wrapper {
    position: relative;
    display: inline-block;
}

.more-dropdown-btn {
    padding: 0.4rem 1rem;
    border-radius: 2rem;
    background: rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.8);
    border: 1px solid rgba(255,255,255,0.15);
    font-weight: 600;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.25s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    white-space: nowrap;
}

.more-dropdown-btn:hover {
    background: rgba(255,255,255,0.18);
    color: #fff;
    transform: translateY(-2px);
}

.more-dropdown-btn .badge-count {
    background: var(--gold, #f5b942);
    color: var(--ink, #12213a);
    border-radius: 50%;
    padding: 0.1rem 0.4rem;
    font-size: 0.6rem;
    font-weight: 700;
    min-width: 18px;
    text-align: center;
}

.more-dropdown-btn .bi-chevron-down {
    transition: transform 0.3s ease;
    font-size: 0.7rem;
}

.more-dropdown-btn.active .bi-chevron-down {
    transform: rotate(180deg);
}

.more-dropdown-menu {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    min-width: 220px;
    max-width: 380px;
    max-height: 320px;
    overflow-y: auto;
    background: #ffffff;
    border-radius: 1rem;
    padding: 0.6rem;
    box-shadow: 0 16px 50px rgba(0,0,0,0.2);
    display: none;
    z-index: 1000;
    border: 1px solid var(--line, #e7e9ee);
    flex-wrap: wrap;
    gap: 0.3rem;
}

.more-dropdown-menu.show {
    display: flex;
}

.more-dropdown-menu .category-tab.more-item {
    display: inline-flex;
    background: var(--bg-soft, #f7f8fa);
    color: var(--ink, #12213a);
    border: 1px solid var(--line, #e7e9ee);
    font-size: 0.7rem;
    padding: 0.3rem 0.8rem;
    margin: 0;
    flex: 0 1 auto;
}

.more-dropdown-menu .category-tab.more-item:hover {
    background: var(--gold, #f5b942);
    color: var(--ink, #12213a);
    border-color: var(--gold, #f5b942);
}

.more-dropdown-menu .category-tab.more-item.active {
    background: var(--gold, #f5b942);
    color: var(--ink, #12213a);
    border-color: var(--gold, #f5b942);
}

/* Dark Mode */
body.dark-mode .category-tabs-wrap {
    background: rgba(255,255,255,0.03);
}

body.dark-mode .category-tabs-wrap .category-tab {
    background: rgba(255,255,255,0.05);
    color: var(--dm-text-secondary, #8b949e);
    border-color: var(--dm-border, #30363d);
}

body.dark-mode .category-tabs-wrap .category-tab:hover {
    background: rgba(255,255,255,0.12);
    color: var(--dm-text-primary, #f0f6fc);
}

body.dark-mode .category-tabs-wrap .category-tab.active {
    background: var(--gold, #f5b942);
    color: var(--ink, #12213a);
    border-color: var(--gold, #f5b942);
}

body.dark-mode .more-dropdown-btn {
    background: rgba(255,255,255,0.06);
    color: var(--dm-text-secondary, #8b949e);
    border-color: var(--dm-border, #30363d);
}

body.dark-mode .more-dropdown-btn:hover {
    background: rgba(255,255,255,0.12);
    color: var(--dm-text-primary, #f0f6fc);
}

body.dark-mode .more-dropdown-menu {
    background: var(--dm-bg-secondary, #161b22);
    border-color: var(--dm-border, #30363d);
}

body.dark-mode .more-dropdown-menu .category-tab.more-item {
    background: var(--dm-bg-tertiary, #1c2333);
    color: var(--dm-text-primary, #f0f6fc);
    border-color: var(--dm-border, #30363d);
}

body.dark-mode .more-dropdown-menu .category-tab.more-item:hover {
    background: var(--gold, #f5b942);
    color: var(--ink, #12213a);
}

body.dark-mode .more-dropdown-menu .category-tab.more-item.active {
    background: var(--gold, #f5b942);
    color: var(--ink, #12213a);
}

/* Scrollbar styling for dropdown */
.more-dropdown-menu::-webkit-scrollbar {
    width: 4px;
}

.more-dropdown-menu::-webkit-scrollbar-track {
    background: transparent;
}

.more-dropdown-menu::-webkit-scrollbar-thumb {
    background: var(--line, #e7e9ee);
    border-radius: 10px;
}

body.dark-mode .more-dropdown-menu::-webkit-scrollbar-thumb {
    background: var(--dm-border, #30363d);
}

/* Mobile responsive */
@media (max-width: 768px) {
    .category-tabs-wrap {
        max-height: 78px;
        padding: 0.5rem 0.25rem;
        gap: 0.3rem;
    }
    
    .category-tabs-wrap .category-tab {
        font-size: 0.65rem;
        padding: 0.3rem 0.7rem;
    }
    
    .more-dropdown-btn {
        font-size: 0.65rem;
        padding: 0.3rem 0.7rem;
    }
    
    .more-dropdown-menu {
        min-width: 180px;
        max-width: 300px;
        max-height: 250px;
        right: -10px;
    }
    
    .more-dropdown-menu .category-tab.more-item {
        font-size: 0.65rem;
        padding: 0.25rem 0.6rem;
    }
}

@media (max-width: 480px) {
    .category-tabs-wrap {
        max-height: 70px;
        gap: 0.25rem;
    }
    
    .category-tabs-wrap .category-tab {
        font-size: 0.55rem;
        padding: 0.2rem 0.5rem;
        border-radius: 1.5rem;
    }
    
    .more-dropdown-btn {
        font-size: 0.55rem;
        padding: 0.2rem 0.5rem;
    }
    
    .more-dropdown-menu {
        min-width: 150px;
        max-width: 260px;
        right: -5px;
        padding: 0.4rem;
    }
}

/* More Dropdown Button */
.category-tabs-wrap .more-dropdown {
    position: relative;
    display: inline-block;
}

.category-tabs-wrap .more-dropdown-btn {
    padding: 0.4rem 1.1rem;
    border-radius: 2rem;
    background: rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.7);
    border: 1px solid rgba(255,255,255,0.1);
    font-weight: 600;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.25s ease;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.category-tabs-wrap .more-dropdown-btn:hover {
    background: rgba(255,255,255,0.15);
    color: #fff;
}

body.dark-mode .category-tabs-wrap .more-dropdown-btn {
    background: rgba(255,255,255,0.05);
    color: var(--dm-text-secondary, #8b949e);
    border-color: var(--dm-border, #30363d);
}

body.dark-mode .category-tabs-wrap .more-dropdown-btn:hover {
    background: rgba(255,255,255,0.1);
    color: var(--dm-text-primary, #f0f6fc);
}

.category-tabs-wrap .more-dropdown-menu {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    min-width: 200px;
    max-width: 320px;
    max-height: 300px;
    overflow-y: auto;
    background: #fff;
    border-radius: 0.8rem;
    padding: 0.5rem;
    box-shadow: 0 12px 40px rgba(0,0,0,0.2);
    display: none;
    z-index: 1000;
    border: 1px solid var(--line);
}

.category-tabs-wrap .more-dropdown-menu.show {
    display: block;
}

body.dark-mode .category-tabs-wrap .more-dropdown-menu {
    background: var(--dm-bg-secondary, #161b22);
    border-color: var(--dm-border, #30363d);
}

.category-tabs-wrap .more-dropdown-menu .category-tab {
    display: block;
    border: none;
    border-radius: 0.5rem;
    padding: 0.4rem 0.8rem;
    font-size: 0.75rem;
    background: transparent;
    color: var(--ink);
    text-align: left;
}

.category-tabs-wrap .more-dropdown-menu .category-tab:hover {
    background: var(--bg-soft);
}

body.dark-mode .category-tabs-wrap .more-dropdown-menu .category-tab {
    color: var(--dm-text-primary, #f0f6fc);
}

body.dark-mode .category-tabs-wrap .more-dropdown-menu .category-tab:hover {
    background: var(--dm-bg-tertiary, #1c2333);
}

.active-filters .chip {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    background: #eaf1ff;
    color: var(--blue);
    border-radius: 2rem;
    padding: .28rem .65rem;
    font-size: .72rem;
    font-weight: 600;
    text-decoration: none;
    margin: .1rem;
    transition: background 0.3s ease, color 0.3s ease;
}

.active-filters .chip:hover {
    background: #dbe9ff;
}

.clear-btn {
    color: var(--muted);
    font-weight: 600;
    font-size: .78rem;
    transition: color 0.3s ease;
}

.clear-btn:hover {
    color: var(--ink);
}

.clear-btn i {
    color: var(--blue);
}

.view-toggle {
    display: inline-flex;
    border: 1px solid var(--line);
    border-radius: .5rem;
    overflow: hidden;
    transition: border-color 0.3s ease;
}

.view-toggle button {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .5rem 1rem;
    border: none;
    background: #fff;
    color: var(--ink);
    font-size: .9rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.3s ease, color 0.3s ease;
}

.view-toggle button.active {
    background: var(--ink);
    color: #fff;
}

.view-toggle button:hover:not(.active) {
    background: var(--bg-soft);
}

.wishlist-icon-btn {
    position: absolute;
    top: 16px;
    left: 16px;
    z-index: 2;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 50%;
    background: rgba(255,255,255,.9);
    color: #12213a;
    font-size: 1.05rem;
    box-shadow: 0 4px 14px rgba(18,33,58,.2);
    transition: all 0.2s ease;
}

.wishlist-icon-btn:hover {
    background: #fff;
    color: #e0245e;
}

.wishlist-icon-btn.active {
    color: #e0245e;
}

.property-card {
    display: flex;
    flex-direction: column;
    height: 100%;
    background: #fff;
    border: 1px solid var(--line);
    border-radius: 1.1rem;
    overflow: hidden;
    box-shadow: 0 6px 20px var(--shadow-color);
    transition: box-shadow .2s ease,transform .2s ease, background 0.3s ease, border-color 0.3s ease;
    text-decoration: none;
    color: inherit;
    cursor: pointer;
}

.property-card:hover {
    box-shadow: 0 16px 34px var(--shadow-hover);
    transform: translateY(-4px);
    color: inherit;
    text-decoration: none;
}

.property-image {
    position: relative;
    height: 220px;
    overflow: hidden;
    background: #f0f2f5;
}

.property-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .35s ease;
}

.property-image img[src=""],
.property-image img:not([src]),
.property-image img[src*="placeholder"] {
    object-fit: contain;
    padding: 20px;
    background: #f0f2f5;
}

.property-card:hover .property-image img {
    transform: scale(1.06);
}

.property-image::after {
    content: '';
    position: absolute;
    inset: auto 0 0;
    height: 55%;
    background: linear-gradient(to top,rgba(10,16,28,.55),transparent);
    pointer-events: none;
}

.sale-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    z-index: 2;
    background: linear-gradient(135deg,var(--gold),#e0a72f);
    color: var(--ink);
    font-weight: 700;
    font-size: .75rem;
    padding: .35rem .8rem;
    border-radius: 2rem;
    box-shadow: 0 4px 12px rgba(245,185,66,.4);
    transition: background 0.3s ease, color 0.3s ease;
}

.property-body {
    display: flex;
    flex-direction: column;
    flex: 1;
    padding: 1.25rem 1.35rem 1.4rem;
}

.property-title {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    color: var(--ink);
    font-size: 1.15rem;
    font-weight: 800;
    line-height: 1.35;
    transition: color 0.3s ease;
}

.property-location {
    color: var(--muted);
    font-size: .88rem;
    margin-bottom: .75rem;
    transition: color 0.3s ease;
}

.property-location i {
    color: var(--blue);
}

.property-features {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem;
    margin-bottom: .85rem;
}

.property-features span {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .32rem .7rem;
    border-radius: .6rem;
    font-size: .8rem;
    font-weight: 600;
    transition: background 0.3s ease, color 0.3s ease;
}

.feature-beds {
    background: #eaf1ff;
    color: var(--blue);
}

.feature-baths {
    background: #e8f9f1;
    color: #0f9d58;
}

.feature-area {
    background: #f5eeff;
    color: #7c3aed;
}

.amenity-chip {
    display: inline-block;
    background: var(--bg-soft);
    color: var(--muted);
    border: 1px solid var(--line);
    border-radius: 2rem;
    padding: .28rem .8rem;
    margin: 0 .3rem .3rem 0;
    font-size: .76rem;
    font-weight: 500;
    transition: background 0.3s ease, color 0.3s ease, border-color 0.3s ease;
}

.property-footer {
    margin-top: auto;
    padding-top: .9rem;
    border-top: 1px dashed var(--line);
    transition: border-color 0.3s ease;
}

.property-price {
    display: inline-block;
    background: linear-gradient(135deg,#eaf1ff,#dbe9ff);
    color: var(--blue);
    padding: .4rem .9rem;
    border-radius: .7rem;
    font-size: 1.15rem;
    font-weight: 800;
    transition: background 0.3s ease, color 0.3s ease;
}

#mapView {
    display: none;
    width: 100%;
}

#mapView.active {
    display: block !important;
}

#propertiesMap {
    width: 100%;
    height: 520px;
    background: #e5e3df;
    border: 1px solid var(--line);
    border-radius: 1rem;
    overflow: hidden;
    position: relative;
    z-index: 0;
    transition: border-color 0.3s ease;
}

.leaflet-container {
    background: #e5e3df !important;
    font-family: inherit;
}

.map-loading-overlay {
    position: absolute;
    inset: 0;
    z-index: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: .6rem;
    background: #e5e3df;
    color: var(--muted);
    font-size: .9rem;
    transition: background 0.3s ease, color 0.3s ease;
}

.map-loading-overlay .spinner-border {
    width: 2rem;
    height: 2rem;
}

.map-popup h6 {
    margin: 0 0 .2rem;
    color: var(--ink);
    font-size: .9rem;
    font-weight: 700;
    transition: color 0.3s ease;
}

.map-popup .price {
    color: var(--blue);
    font-size: .85rem;
    font-weight: 700;
    transition: color 0.3s ease;
}

.map-popup .addr {
    color: var(--muted);
    font-size: .78rem;
    margin-bottom: .4rem;
    transition: color 0.3s ease;
}

.map-popup a.view-link {
    font-size: .8rem;
    font-weight: 600;
    transition: color 0.3s ease;
}

.map-status {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    padding: 1rem;
    color: var(--muted);
    font-size: .9rem;
    transition: color 0.3s ease;
}

.location-dropdowns {
    display: flex;
    gap: 0.5rem;
}

.location-dropdowns .form-select {
    flex: 1;
    min-width: 0;
}

.view-container {
    display: none;
}

.view-container.active {
    display: block;
}

.list-card {
    display: flex;
    flex-direction: row;
    height: auto;
    min-height: 220px;
    overflow: hidden;
}

.list-card .property-image {
    width: 300px;
    min-width: 300px;
    height: 220px;
    flex-shrink: 0;
}

.list-card .property-body {
    flex: 1;
    padding: 1.25rem 1.5rem;
}

.list-card .property-title {
    font-size: 1.25rem;
}

.list-card .property-location {
    font-size: 0.95rem;
}

.list-card .property-features span {
    font-size: .85rem;
    padding: .35rem .8rem;
}

.list-card .property-price {
    font-size: 1.3rem;
    padding: .5rem 1rem;
}

.list-card .amenity-chip {
    font-size: .8rem;
    padding: .3rem .9rem;
}

.grid-card .property-image {
    height: 200px;
}

.grid-card .property-body {
    padding: 1rem 1.1rem 1.2rem;
}

.grid-card .property-title {
    font-size: 1rem;
}

.grid-card .property-features span {
    font-size: .7rem;
    padding: .25rem .6rem;
}

.grid-card .property-price {
    font-size: 1rem;
    padding: .3rem .8rem;
}

.grid-card .amenity-chip {
    font-size: .7rem;
    padding: .2rem .6rem;
}

@media (max-width:991.98px){
    .filters-grid {
        grid-template-columns: repeat(2,1fr);
        gap: .85rem 1rem;
    }
    .list-card {
        flex-direction: column;
    }
    .list-card .property-image {
        width: 100%;
        min-width: auto;
        height: 220px;
    }
}

@media (max-width:575.98px){
    .hero-section {
        padding: 2rem 0 4.5rem;
    }
    .hero-section h2 {
        font-size: 1.5rem;
    }
    .hero-section p {
        font-size: .9rem;
    }
    .search-card {
        padding: .85rem;
        border-radius: .7rem;    margin-top: 7.5rem;
    }
    .filters-grid {
        grid-template-columns: 1fr;
        gap: .85rem;
    }
    .search-input-group {
        flex-direction: column;
        align-items: stretch;
        min-height: auto;
        border-radius: .7rem;
    }
    .search-field-inner {
        width: 100%;
        min-height: 42px;
    }
    .search-input-group .input-group-text {
        padding: .6rem .5rem .6rem .9rem;
    }
    .search-input-group .search-input {
        padding: .6rem .5rem .6rem 0;
        font-size: .9rem;
    }
    .search-btn {
        width: 100%;
        min-height: 42px;
        padding: .55rem 1rem;
        font-size: .9rem;
    }
    .filter-col label {
        font-size: .82rem;
        margin-bottom: .35rem;
    }
    .filter-col .d-flex.gap-2 {
        flex-wrap: wrap;
        gap: .6rem !important;
    }
    .filter-col .d-flex.gap-2 input {
        flex: 1 1 calc(50% - .3rem);
        min-width: 0;
        font-size: .9rem;
    }
    .active-filters .chip {
        font-size: .78rem;
        padding: .35rem .7rem;
    }
    .clear-btn {
        font-size: .85rem;
    }
    section.py-5 .d-flex.justify-content-between {
        flex-direction: column;
        align-items: flex-start !important;
    }
    section.py-5 h2.fw-bold {
        font-size: 1.25rem;
    }
    .view-toggle,
    .view-toggle + .form-select {
        width: 100%;
    }
    .view-toggle button {
        flex: 1;
        justify-content: center;
    }
    
    .property-image {
        height: 190px;
    }
    .property-body {
        padding: 1.1rem 1.1rem 1.25rem;
    }
    .property-title {
        font-size: 1.05rem;
    }
    .location-dropdowns {
        flex-direction: column;
        gap: 0.5rem;
    }
    .list-card {
        flex-direction: column;
    }
    .list-card .property-image {
        width: 100%;
        min-width: auto;
        height: 190px;
    }
    .list-card .property-body {
        padding: 1rem 1.1rem 1.2rem;
    }
    .list-card .property-title {
        font-size: 1.05rem;
    }
    .list-card .property-price {
        font-size: 1.1rem;
        padding: .4rem .8rem;
    }
    .grid-card .property-image {
        height: 160px;
    }
    .category-tabs-wrap {
        gap: 0.35rem;
    }
    .category-tabs-wrap .category-tab {
        padding: 0.3rem 0.8rem;
        font-size: 0.7rem;
    }
    .category-tabs-wrap .more-dropdown-btn {
        padding: 0.3rem 0.8rem;
        font-size: 0.7rem;
    }
}

@media (max-width:400px){
    .filter-col .d-flex.gap-2 input {
        flex: 1 1 100%;
    }
}
    /* Category Tabs Container */
.category-tabs-container {
    position: relative;
    z-index: 10;
    margin-top: 1.5rem;
    margin-bottom: 2.5rem;   /* was 0.5rem */
}

/* Category Tabs - 2 Row Limit with More Button */
.category-tabs-wrap {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 0.5rem;
    border-radius: 1.5rem;
    background: rgba(255,255,255,0.04);
    max-height: 85px; /* About 2 rows */
    overflow: visible;
    transition: max-height 0.4s ease, background 0.3s ease, padding 0.3s ease;
    position: relative;
}

.category-tabs-wrap.expanded {
    max-height: 600px;
    background: rgba(255,255,255,0.08);
    padding-bottom: 3.5rem;
}

.category-tabs-wrap .category-tab {
    padding: 0.35rem 1rem;
    border-radius: 2rem;
    background: rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.7);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.75rem;
    border: 1px solid rgba(255,255,255,0.08);
    transition: all 0.25s ease;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    flex-shrink: 0;
}

.category-tabs-wrap .category-tab:hover {
    background: rgba(255,255,255,0.15);
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}

.category-tabs-wrap .category-tab.active {
    background: var(--gold, #f5b942);
    color: var(--ink, #12213a);
    border-color: var(--gold, #f5b942);
    box-shadow: 0 4px 15px rgba(245,185,66,0.25);
    font-weight: 700;
}

.category-tabs-wrap .category-tab i {
    font-size: 0.7rem;
}

/* More Dropdown Wrapper */
.more-dropdown-wrapper {
    position: relative;
    display: inline-flex;
    flex-shrink: 0;
    z-index: 100;
}

.more-dropdown-btn {
    padding: 0.35rem 1rem;
    border-radius: 2rem;
    background: rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.8);
    border: 1px solid rgba(255,255,255,0.15);
    font-weight: 600;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.25s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    white-space: nowrap;
}

.more-dropdown-btn:hover {
    background: rgba(255,255,255,0.2);
    color: #fff;
    transform: translateY(-2px);
}

.more-dropdown-btn .badge-count {
    background: var(--gold, #f5b942);
    color: var(--ink, #12213a);
    border-radius: 50%;
    padding: 0.05rem 0.4rem;
    font-size: 0.6rem;
    font-weight: 700;
    min-width: 18px;
    text-align: center;
}

.more-dropdown-btn .bi-chevron-down {
    transition: transform 0.3s ease;
    font-size: 0.65rem;
}

.more-dropdown-btn.active .bi-chevron-down {
    transform: rotate(180deg);
}

/* More Dropdown Menu - Positioned above the search card */
.more-dropdown-menu {
    position: absolute;
    top: calc(100% + 8px);
    left: 50%;
    transform: translateX(-50%);
    min-width: 320px;
    max-width: 500px;
    width: max-content;
    max-height: 350px;
    overflow-y: auto;
    background: #ffffff;
    border-radius: 1rem;
    padding: 0.6rem;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25);
    display: none;
    z-index: 1000;
    border: 1px solid var(--line, #e7e9ee);
}

.more-dropdown-menu.show {
    display: block;
}

.more-dropdown-inner {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
}

.more-dropdown-menu .category-tab.more-item {
    display: inline-flex;
    background: var(--bg-soft, #f7f8fa);
    color: var(--ink, #12213a);
    border: 1px solid var(--line, #e7e9ee);
    font-size: 0.7rem;
    padding: 0.3rem 0.8rem;
    margin: 0;
    border-radius: 1.5rem;
    flex: 0 1 auto;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
}

.more-dropdown-menu .category-tab.more-item:hover {
    background: var(--gold, #f5b942);
    color: var(--ink, #12213a);
    border-color: var(--gold, #f5b942);
    transform: translateY(-1px);
}

.more-dropdown-menu .category-tab.more-item.active {
    background: var(--gold, #f5b942);
    color: var(--ink, #12213a);
    border-color: var(--gold, #f5b942);
    font-weight: 700;
}

/* Scrollbar styling for dropdown */
.more-dropdown-menu::-webkit-scrollbar {
    width: 4px;
}

.more-dropdown-menu::-webkit-scrollbar-track {
    background: transparent;
}

.more-dropdown-menu::-webkit-scrollbar-thumb {
    background: #d0d5dd;
    border-radius: 10px;
}

/* Dark Mode */
body.dark-mode .category-tabs-container {
    z-index: 10;
}

body.dark-mode .category-tabs-wrap {
    background: rgba(255,255,255,0.03);
}

body.dark-mode .category-tabs-wrap .category-tab {
    background: rgba(255,255,255,0.05);
    color: var(--dm-text-secondary, #8b949e);
    border-color: var(--dm-border, #30363d);
}

body.dark-mode .category-tabs-wrap .category-tab:hover {
    background: rgba(255,255,255,0.12);
    color: var(--dm-text-primary, #f0f6fc);
}

body.dark-mode .category-tabs-wrap .category-tab.active {
    background: var(--gold, #f5b942);
    color: var(--ink, #12213a);
    border-color: var(--gold, #f5b942);
}

body.dark-mode .more-dropdown-btn {
    background: rgba(255,255,255,0.06);
    color: var(--dm-text-secondary, #8b949e);
    border-color: var(--dm-border, #30363d);
}

body.dark-mode .more-dropdown-btn:hover {
    background: rgba(255,255,255,0.12);
    color: var(--dm-text-primary, #f0f6fc);
}

body.dark-mode .more-dropdown-menu {
    background: var(--dm-bg-secondary, #161b22);
    border-color: var(--dm-border, #30363d);
}

body.dark-mode .more-dropdown-menu .category-tab.more-item {
    background: var(--dm-bg-tertiary, #1c2333);
    color: var(--dm-text-primary, #f0f6fc);
    border-color: var(--dm-border, #30363d);
}

body.dark-mode .more-dropdown-menu .category-tab.more-item:hover {
    background: var(--gold, #f5b942);
    color: var(--ink, #12213a);
    border-color: var(--gold, #f5b942);
}

body.dark-mode .more-dropdown-menu .category-tab.more-item.active {
    background: var(--gold, #f5b942);
    color: var(--ink, #12213a);
    border-color: var(--gold, #f5b942);
}

body.dark-mode .more-dropdown-menu::-webkit-scrollbar-thumb {
    background: var(--dm-border, #30363d);
}

/* Mobile responsive */
@media (max-width: 768px) {
    .category-tabs-container {
        z-index: 10;
    }
    
    .category-tabs-wrap {
        max-height: 78px;
        padding: 0.4rem 0.25rem;
        gap: 0.3rem;
    }
    
    .category-tabs-wrap .category-tab {
        font-size: 0.65rem;
        padding: 0.25rem 0.7rem;
    }
    
    .more-dropdown-btn {
        font-size: 0.65rem;
        padding: 0.25rem 0.7rem;
    }
    
    .more-dropdown-menu {
        min-width: 280px;
        max-width: 340px;
        max-height: 280px;
        left: 50%;
        transform: translateX(-50%);
        padding: 0.5rem;
    }
    
    .more-dropdown-menu .category-tab.more-item {
        font-size: 0.6rem;
        padding: 0.2rem 0.6rem;
    }
}

@media (max-width: 480px) {
    .category-tabs-container {
        z-index: 10;
    }
    
    .category-tabs-wrap {
        max-height: 70px;
        gap: 0.25rem;
        padding: 0.3rem 0.15rem;
    }
    
    .category-tabs-wrap .category-tab {
        font-size: 0.55rem;
        padding: 0.2rem 0.5rem;
        border-radius: 1.5rem;
    }
    
    .more-dropdown-btn {
        font-size: 0.55rem;
        padding: 0.2rem 0.5rem;
    }
    
    .more-dropdown-btn .badge-count {
        font-size: 0.5rem;
        min-width: 14px;
        padding: 0.02rem 0.3rem;
    }
    
    .more-dropdown-menu {
        min-width: 220px;
        max-width: 280px;
        max-height: 220px;
        left: 50%;
        transform: translateX(-50%);
        padding: 0.4rem;
        top: calc(100% + 5px);
    }
    
    .more-dropdown-menu .category-tab.more-item {
        font-size: 0.55rem;
        padding: 0.15rem 0.5rem;
    }
}
</style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<!-- HERO SECTION -->
<section class="hero-section py-4">
<div class="container">
    <div class="text-center mb-3">
        <h2 class="display-6 fw-bold"><?php echo htmlspecialchars($pageTitle); ?></h2>
        <p><?php echo htmlspecialchars($heroDescription); ?></p>
        
        <!-- Category Tabs with "More" Dropdown - Fixed Positioning -->
<?php if (!empty($tabCategories)): ?>
<div class="category-tabs-container">
    <div class="category-tabs-wrap" id="categoryTabsWrap">
        <?php 
        $baseTabQuery = $_GET;
        unset($baseTabQuery['category']);
        unset($baseTabQuery['page']);
        $baseTabUrl = 'properties.php?' . http_build_query($baseTabQuery);
        
        // Define buy/rent slugs for special handling
        $buyRentSlugs = ['buy', 'rent', 'buy-property', 'rent-property'];
        
        // Limit visible categories to 14 (2 rows of ~7 each)
        $visibleCount = 14;
        $totalCategories = count($tabCategories);
        $visibleCategories = array_slice($tabCategories, 0, $visibleCount);
        $moreCategories = array_slice($tabCategories, $visibleCount);
        ?>
        <a href="<?php echo htmlspecialchars($baseTabUrl); ?>" class="category-tab <?php echo empty($categorySlug) ? 'active' : ''; ?>">
            <i class="bi bi-grid"></i> All
        </a>
        
        <?php foreach ($visibleCategories as $cat): 
            $tabQuery = $_GET;
            $tabQuery['category'] = $cat['slug'];
            unset($tabQuery['page']);
            $tabUrl = 'properties.php?' . http_build_query($tabQuery);
            $isActive = ($categorySlug === $cat['slug']);
        ?>
            <a href="<?php echo htmlspecialchars($tabUrl); ?>" class="category-tab <?php echo $isActive ? 'active' : ''; ?>">
                <i class="bi bi-tag"></i> <?php echo htmlspecialchars($cat['name']); ?>
            </a>
        <?php endforeach; ?>
        
        <?php if (!empty($moreCategories)): ?>
        <div class="more-dropdown-wrapper">
            <button class="more-dropdown-btn" id="moreCategoriesBtn" onclick="toggleMoreCategories(event)">
                More <span class="badge-count"><?php echo count($moreCategories); ?></span> <i class="bi bi-chevron-down"></i>
            </button>
            <div class="more-dropdown-menu" id="moreCategoriesMenu">
                <div class="more-dropdown-inner">
                    <?php foreach ($moreCategories as $cat): 
                        $tabQuery = $_GET;
                        $tabQuery['category'] = $cat['slug'];
                        unset($tabQuery['page']);
                        $tabUrl = 'properties.php?' . http_build_query($tabQuery);
                        $isActive = ($categorySlug === $cat['slug']);
                    ?>
                        <a href="<?php echo htmlspecialchars($tabUrl); ?>" class="category-tab more-item <?php echo $isActive ? 'active' : ''; ?>">
                            <i class="bi bi-tag"></i> <?php echo htmlspecialchars($cat['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
    </div>

    <div class="search-card">
        <form method="GET" id="filterForm">
            <?php if ($sort !== 'Newest'): ?>
                <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
            <?php endif; ?>
            <?php if ($categorySlug): ?>
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($categorySlug); ?>">
            <?php endif; ?>
            <?php if ($city !== ''): ?>
                <input type="hidden" name="city" id="cityHiddenInput" value="<?php echo htmlspecialchars($city); ?>">
            <?php endif; ?>
            <?php if ($region !== ''): ?>
                <input type="hidden" name="region" id="regionHiddenInput" value="<?php echo htmlspecialchars($region); ?>">
            <?php endif; ?>

            <div class="search-input-group mb-3">
                <div class="search-field-inner">
                    <span class="input-group-text bg-white border-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text"
                           name="search"
                           id="searchInput"
                           class="form-control search-input"
                           placeholder="Search by project name or address"
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <button class="search-btn" type="submit" id="searchSubmitBtn">Search</button>
            </div>

            <div class="filters-grid mb-3">
                <div class="filter-col">
                    <label>Price Range</label>
                    <div class="d-flex gap-2 mb-2">
                        <input type="number" name="min" id="minInput" class="form-control" placeholder="Min" value="<?php echo $minPrice > 0 ? $minPrice * 100000 : ''; ?>">
                        <input type="number" name="max" id="maxInput" class="form-control" placeholder="Max" value="<?php echo $maxPrice * 100000; ?>">
                    </div>
                    <input type="range" id="priceRange" class="form-range" min="0" max="<?php echo $sliderMax; ?>" step="10" value="<?php echo $maxPrice; ?>">
                    <div class="d-flex justify-content-between small text-muted">
                        <span><?php echo lakhsToDisplay($dbMinLakhs); ?></span>
                        <span><?php echo lakhsToDisplay($dbMaxLakhs); ?></span>
                    </div>
                </div>

                <div class="filter-col">
                    <label>Location</label>
                    <div class="location-dropdowns">
                        <select name="region" id="regionFilterSelect" class="form-select">
                            <option value="">State</option>
                            <?php foreach (array_keys($regionCities) as $rn): ?>
                                <option value="<?php echo htmlspecialchars($rn); ?>" <?php echo $region === $rn ? 'selected' : ''; ?>><?php echo htmlspecialchars($rn); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="city" id="cityFilterSelect" class="form-select">
                            <option value="">City</option>
                            <?php
                                $cityOptionList = ($region !== '' && isset($regionCities[$region])) ? $regionCities[$region] : $allCitiesFlat;
                                foreach ($cityOptionList as $c):
                            ?>
                                <option value="<?php echo htmlspecialchars($c); ?>" <?php echo $city === $c ? 'selected' : ''; ?>><?php echo htmlspecialchars($c); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

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
            </div>
        </form>

        <div class="mt-2">
            <a href="#" id="clearAllBtn" class="clear-btn text-decoration-none d-inline-flex align-items-center gap-2">
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
$headingParts = [];
if ($search !== '') {
    $headingParts[] = 'for "<strong>' . htmlspecialchars($search) . '</strong>"';
}
if ($city !== '') {
    $headingParts[] = 'in <strong>' . htmlspecialchars($city) . '</strong>';
}
if ($region !== '') {
    $headingParts[] = 'in state <strong>' . htmlspecialchars($region) . '</strong>';
}
if ($categorySlug && $categoryNameForTitle) {
    $buyRentSlugs = ['buy', 'rent', 'buy-property', 'rent-property'];
    if (in_array($categorySlugForTitle, $buyRentSlugs)) {
        $headingParts[] = 'of <strong>' . htmlspecialchars($categoryNameForTitle) . ' Properties</strong>';
    } else {
        $headingParts[] = 'in <strong>' . htmlspecialchars($categoryNameForTitle) . '</strong>';
    }
}

$headingText = implode(' ', $headingParts);

if (!empty($headingText)) {
    echo (int) $total . ' Results ' . $headingText;
} else {
    echo (int) $total . ' Properties Found';
}
?>
</h2>
        <div class="d-flex align-items-center gap-2">
            <div class="view-toggle">
                <button type="button" id="listTabBtn" class="active"><i class="bi bi-list-ul"></i> List</button>
                <button type="button" id="gridTabBtn"><i class="bi bi-grid"></i> Grid</button>
                <button type="button" id="mapTabBtn"><i class="bi bi-map"></i> Map</button>
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

    <!-- List view -->
    <div id="listView" class="view-container active">
        <?php if (!empty($properties)): ?>
            <?php foreach ($properties as $property):
                // Get image path - use placeholder if no image or image is empty
                $imagePath = !empty($property['image']) 
                    ? "../admin/uploads/trending-projects/" . $property['image'] 
                    : "https://placehold.co/600x400/12213a/eef1fb?text=No+Image+Available";
                
                // Check if image file actually exists (optional - helps avoid broken images)
                if (!empty($property['image']) && !file_exists("../admin/uploads/trending-projects/" . $property['image'])) {
                    $imagePath = "https://placehold.co/600x400/12213a/eef1fb?text=No+Image+Available";
                }
                
                $amenities = !empty($property['amenities']) ? array_slice(explode(',', $property['amenities']), 0, 4) : [];
                $propertySlug = !empty($property['slug']) ? $property['slug'] : null;
                $propertyUrl  = $propertySlug ? "property-listing/{$propertySlug}/" : '#';
                
                // Determine listing type badge
                $listingBadge = 'For Sale';
                if (!empty($property['listing_type_id'])) {
                    $badgeStmt = $conn->prepare("SELECT name FROM listing_types WHERE id = ?");
                    if ($badgeStmt) {
                        $badgeStmt->bind_param("i", $property['listing_type_id']);
                        $badgeStmt->execute();
                        $badgeResult = $badgeStmt->get_result();
                        if ($badgeRow = $badgeResult->fetch_assoc()) {
                            $listingBadge = $badgeRow['name'];
                        }
                        $badgeStmt->close();
                    }
                }
            ?>
            <div class="mb-4">
                <a href="<?php echo htmlspecialchars($propertyUrl); ?>" class="property-card list-card">
                    <div class="property-image">
                        <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($property['project_name']); ?>" loading="lazy">
                        <span class="sale-badge"><?php echo htmlspecialchars($listingBadge); ?></span>
                        <button type="button" class="wishlist-icon-btn" data-project-id="<?php echo (int) $property['id']; ?>" aria-label="Add to wishlist">
                            <i class="bi bi-heart"></i>
                        </button>
                    </div>
                    <div class="property-body">
                        <h3 class="property-title mb-2"><?php echo htmlspecialchars($property['project_name']); ?></h3>
                        <div class="property-location">
                            <i class="bi bi-geo-alt me-1"></i>
                            <?php echo htmlspecialchars(formatAddressDisplay(pv($property, 'address', ''))); ?>
                        </div>

                        <div class="property-features">
                            <?php if (!empty($property['bedrooms']) && $property['bedrooms'] > 0): ?>
                                <span class="feature-beds"><i class="bi bi-door-open"></i> <?php echo (int) $property['bedrooms']; ?> Beds</span>
                            <?php endif; ?>
                            <?php if (!empty($property['bathrooms']) && $property['bathrooms'] > 0): ?>
                                <span class="feature-baths"><i class="bi bi-droplet"></i> <?php echo (int) $property['bathrooms']; ?> Baths</span>
                            <?php endif; ?>
                            <?php if (!empty($property['area'])): ?>
                                <span class="feature-area"><i class="bi bi-aspect-ratio"></i> <?php echo htmlspecialchars($property['area']); ?></span>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($amenities)): ?>
                        <div>
                            <?php foreach ($amenities as $a): ?>
                                <?php $a = trim($a); if (!empty($a)): ?>
                                <span class="amenity-chip"><?php echo htmlspecialchars($a); ?></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <div class="property-footer">
                            <div class="property-price"><?php echo htmlspecialchars(formatPriceDisplay(pv($property, 'price', ''))); ?></div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-warning">No properties found matching your filters.</div>
        <?php endif; ?>
    </div>

    <!-- Grid view -->
    <div id="gridView" class="view-container">
        <div class="row g-3 mt-2">
            <?php if (!empty($properties)): ?>
                <?php foreach ($properties as $property):
                    $imagePath = !empty($property['image']) 
                        ? "../admin/uploads/trending-projects/" . $property['image'] 
                        : "https://placehold.co/600x400/12213a/eef1fb?text=No+Image+Available";
                    
                    if (!empty($property['image']) && !file_exists("../admin/uploads/trending-projects/" . $property['image'])) {
                        $imagePath = "https://placehold.co/600x400/12213a/eef1fb?text=No+Image+Available";
                    }
                    
                    $amenities = !empty($property['amenities']) ? array_slice(explode(',', $property['amenities']), 0, 3) : [];
                    $propertySlug = !empty($property['slug']) ? $property['slug'] : null;
                    $propertyUrl  = $propertySlug ? "property-listing/{$propertySlug}/" : '#';
                    
                    $listingBadge = 'For Sale';
                    if (!empty($property['listing_type_id'])) {
                        $badgeStmt = $conn->prepare("SELECT name FROM listing_types WHERE id = ?");
                        if ($badgeStmt) {
                            $badgeStmt->bind_param("i", $property['listing_type_id']);
                            $badgeStmt->execute();
                            $badgeResult = $badgeStmt->get_result();
                            if ($badgeRow = $badgeResult->fetch_assoc()) {
                                $listingBadge = $badgeRow['name'];
                            }
                            $badgeStmt->close();
                        }
                    }
                ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="<?php echo htmlspecialchars($propertyUrl); ?>" class="property-card grid-card">
                        <div class="property-image">
                            <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($property['project_name']); ?>" loading="lazy">
                            <span class="sale-badge"><?php echo htmlspecialchars($listingBadge); ?></span>
                            <button type="button" class="wishlist-icon-btn" data-project-id="<?php echo (int) $property['id']; ?>" aria-label="Add to wishlist">
                                <i class="bi bi-heart"></i>
                            </button>
                        </div>
                        <div class="property-body">
                            <h3 class="property-title mb-1"><?php echo htmlspecialchars($property['project_name']); ?></h3>
                            <div class="property-location">
                                <i class="bi bi-geo-alt me-1"></i>
                                <?php echo htmlspecialchars(formatAddressDisplay(pv($property, 'address', ''), 1)); ?>
                            </div>

                            <div class="property-features">
                                <?php if (!empty($property['bedrooms']) && $property['bedrooms'] > 0): ?>
                                    <span class="feature-beds"><i class="bi bi-door-open"></i> <?php echo (int) $property['bedrooms']; ?></span>
                                <?php endif; ?>
                                <?php if (!empty($property['bathrooms']) && $property['bathrooms'] > 0): ?>
                                    <span class="feature-baths"><i class="bi bi-droplet"></i> <?php echo (int) $property['bathrooms']; ?></span>
                                <?php endif; ?>
                                <?php if (!empty($property['area'])): ?>
                                    <span class="feature-area"><i class="bi bi-aspect-ratio"></i> <?php echo htmlspecialchars($property['area']); ?></span>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($amenities)): ?>
                            <div>
                                <?php foreach ($amenities as $a): ?>
                                    <?php $a = trim($a); if (!empty($a)): ?>
                                    <span class="amenity-chip"><?php echo htmlspecialchars($a); ?></span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>

                            <div class="property-footer">
                                <div class="property-price"><?php echo htmlspecialchars(formatPriceDisplay(pv($property, 'price', ''))); ?></div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning">No properties found matching your filters.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Map view -->
    <div id="mapView" class="view-container">
        <div id="propertiesMap"></div>
        <div id="mapStatus" class="map-status"></div>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <nav class="mt-4" id="paginationNav">
        <ul class="pagination justify-content-center">
            <?php
              $prevQuery = $_GET; $prevQuery['page'] = max(1, $page - 1);
              $nextQuery = $_GET; $nextQuery['page'] = min($totalPages, $page + 1);
            ?>
            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="properties.php?<?php echo http_build_query($prevQuery); ?>">&laquo;</a>
            </li>
            <?php 
            // Show limited pagination with ellipsis
            $startPage = max(1, $page - 2);
            $endPage = min($totalPages, $page + 2);
            
            if ($startPage > 1) {
                echo '<li class="page-item"><a class="page-link" href="properties.php?' . http_build_query(array_merge($_GET, ['page' => 1])) . '">1</a></li>';
                if ($startPage > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            
            for ($i = $startPage; $i <= $endPage; $i++): 
                $pQuery = $_GET; $pQuery['page'] = $i; 
            ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                    <a class="page-link" href="properties.php?<?php echo http_build_query($pQuery); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; 
            
            if ($endPage < $totalPages) {
                if ($endPage < $totalPages - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                echo '<li class="page-item"><a class="page-link" href="properties.php?' . http_build_query(array_merge($_GET, ['page' => $totalPages])) . '">' . $totalPages . '</a></li>';
            }
            ?>
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
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ===== CATEGORY MORE DROPDOWN =====
function toggleMoreCategories(event) {
    event.stopPropagation();
    const btn = document.getElementById('moreCategoriesBtn');
    const menu = document.getElementById('moreCategoriesMenu');
    const wrap = document.getElementById('categoryTabsWrap');
    
    if (!btn || !menu) return;
    
    const isOpen = menu.classList.contains('show');
    
    // Close any other open dropdowns
    document.querySelectorAll('.more-dropdown-menu').forEach(m => {
        if (m !== menu) m.classList.remove('show');
    });
    
    // Remove active class from other buttons
    document.querySelectorAll('.more-dropdown-btn').forEach(b => {
        if (b !== btn) b.classList.remove('active');
    });
    
    if (!isOpen) {
        menu.classList.add('show');
        btn.classList.add('active');
        if (wrap) wrap.classList.add('expanded');
    } else {
        menu.classList.remove('show');
        btn.classList.remove('active');
        if (wrap) wrap.classList.remove('expanded');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const menu = document.getElementById('moreCategoriesMenu');
    const btn = document.getElementById('moreCategoriesBtn');
    const wrap = document.getElementById('categoryTabsWrap');
    const wrapper = document.querySelector('.more-dropdown-wrapper');
    
    if (menu && wrapper && !wrapper.contains(e.target)) {
        menu.classList.remove('show');
        if (btn) btn.classList.remove('active');
        if (wrap) wrap.classList.remove('expanded');
    }
});

// Prevent dropdown from closing when clicking inside it
document.getElementById('moreCategoriesMenu')?.addEventListener('click', function(e) {
    e.stopPropagation();
});

// Close dropdown when a category is selected
document.querySelectorAll('.more-dropdown-menu .category-tab').forEach(function(tab) {
    tab.addEventListener('click', function() {
        const menu = document.getElementById('moreCategoriesMenu');
        const btn = document.getElementById('moreCategoriesBtn');
        const wrap = document.getElementById('categoryTabsWrap');
        if (menu) menu.classList.remove('show');
        if (btn) btn.classList.remove('active');
        if (wrap) wrap.classList.remove('expanded');
    });
});

// Handle window resize to adjust dropdown position
window.addEventListener('resize', function() {
    const menu = document.getElementById('moreCategoriesMenu');
    if (menu && menu.classList.contains('show')) {
        // Reposition if needed
    }
});
</script>

<script>
    const priceRange = document.getElementById("priceRange");
    const minInput   = document.getElementById("minInput");
    const maxInput   = document.getElementById("maxInput");

    priceRange.addEventListener("input", () => {
        maxInput.value = priceRange.value * 100000;
    });
    priceRange.addEventListener("change", () => {
        document.getElementById('filterForm').submit();
    });
</script>
<script>
(function(){
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchSubmitBtn');
    const citySelect = document.getElementById('cityFilterSelect');
    const regionSelect = document.getElementById('regionFilterSelect');
    const filterForm = document.getElementById('filterForm');
    const cityHidden = document.getElementById('cityHiddenInput');
    const regionHidden = document.getElementById('regionHiddenInput');
    const clearAllBtn = document.getElementById('clearAllBtn');

    if (!searchInput || !citySelect || !regionSelect || !filterForm) return;

    function clearLocationFilters() {
        if (searchInput.value.trim() !== '') {
            citySelect.value = '';
            regionSelect.value = '';
            if (cityHidden) cityHidden.remove();
            if (regionHidden) regionHidden.remove();
        }
    }

    searchBtn.addEventListener('click', function(e) {
        clearLocationFilters();
    });

    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearLocationFilters();
            filterForm.submit();
        }
    });

    citySelect.addEventListener('change', function() {
        if (this.value !== '') {
            searchInput.value = '';
        }
        filterForm.submit();
    });

    regionSelect.addEventListener('change', function() {
        if (this.value !== '') {
            searchInput.value = '';
            citySelect.value = '';
        }
        filterForm.submit();
    });

    regionSelect.addEventListener('change', function() {
        const region = this.value;
        const cityDropdown = citySelect;
        const currentCity = cityDropdown.value;
        
        while (cityDropdown.options.length > 1) {
            cityDropdown.remove(1);
        }
        
        if (region !== '') {
            const regionCities = <?php echo json_encode($regionCities); ?>;
            const cities = regionCities[region] || [];
            cities.forEach(function(city) {
                const option = document.createElement('option');
                option.value = city;
                option.textContent = city;
                cityDropdown.appendChild(option);
            });
        } else {
            const allCities = <?php echo json_encode($allCitiesFlat); ?>;
            allCities.forEach(function(city) {
                const option = document.createElement('option');
                option.value = city;
                option.textContent = city;
                cityDropdown.appendChild(option);
            });
        }
        
        if (currentCity !== '') {
            for (let i = 0; i < cityDropdown.options.length; i++) {
                if (cityDropdown.options[i].value === currentCity) {
                    cityDropdown.selectedIndex = i;
                    break;
                }
            }
        }
    });

    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            searchInput.value = '';
            citySelect.value = '';
            regionSelect.value = '';
            
            if (cityHidden) cityHidden.remove();
            if (regionHidden) regionHidden.remove();
            
            const minInput = document.getElementById('minInput');
            const maxInput = document.getElementById('maxInput');
            if (minInput) minInput.value = '';
            if (maxInput) {
                const sliderMax = <?php echo $sliderMax; ?>;
                maxInput.value = sliderMax * 100000;
            }
            
            const priceRange = document.getElementById('priceRange');
            if (priceRange) {
                const sliderMax = <?php echo $sliderMax; ?>;
                priceRange.value = sliderMax;
            }
            
            const bedroomSelect = document.querySelector('select[name="bedrooms"]');
            const bathroomSelect = document.querySelector('select[name="bathrooms"]');
            if (bedroomSelect) bedroomSelect.value = '';
            if (bathroomSelect) bathroomSelect.value = '';
            
            const categoryHidden = document.querySelector('input[name="category"]');
            if (categoryHidden) categoryHidden.remove();
            
            const sortHidden = document.querySelector('input[name="sort"]');
            if (sortHidden) sortHidden.remove();
            
            filterForm.submit();
        });
    }
})();
</script>

<script>
(function(){
    function getWishlist() {
        if (typeof window.getWishlistIds === 'function') {
            return window.getWishlistIds();
        }
        var key = typeof getTatitoStorageKey === 'function' ? getTatitoStorageKey('wishlist') : 'tatito_guest_wishlist';
        try { return JSON.parse(localStorage.getItem(key)) || []; }
        catch (e) { return []; }
    }

    function saveWishlist(list) {
        if (typeof window.saveWishlistIds === 'function') {
            window.saveWishlistIds(list);
            return;
        }
        var key = typeof getTatitoStorageKey === 'function' ? getTatitoStorageKey('wishlist') : 'tatito_guest_wishlist';
        var data = JSON.stringify(list || []);
        try {
            localStorage.setItem(key, data);
            document.dispatchEvent(new CustomEvent('wishlistUpdated', { detail: list }));
        } catch(e) {}
    }

    function syncButtons() {
        var wishlist = getWishlist();
        var wishIds = wishlist.map(function(item) {
            return (item && item.id != null) ? Number(item.id) : Number(item);
        });

        document.querySelectorAll('.wishlist-icon-btn').forEach(function(btn){
            var id = Number(btn.dataset.projectId);
            if (!id) return;
            var icon = btn.querySelector('i');
            var isWish = wishIds.indexOf(id) !== -1;

            if (isWish) {
                btn.classList.add('active');
                if (icon) {
                    icon.classList.remove('bi-heart');
                    icon.classList.add('bi-heart-fill');
                    icon.style.color = '#e0245e';
                }
            } else {
                btn.classList.remove('active');
                if (icon) {
                    icon.classList.remove('bi-heart-fill');
                    icon.classList.add('bi-heart');
                    icon.style.color = '';
                }
            }
        });
    }

    document.querySelectorAll('.wishlist-icon-btn').forEach(function(btn){
        var id = Number(btn.dataset.projectId);
        if (!id) return;

        btn.addEventListener('click', function(e){
            e.preventDefault();
            e.stopPropagation();

            var list = getWishlist();
            var idNum = Number(id);
            var exists = list.some(function(item) {
                var itemId = (item && item.id != null) ? Number(item.id) : Number(item);
                return itemId === idNum;
            });

            if (!exists) {
                list.unshift({ id: idNum, type: 'property' });
            } else {
                list = list.filter(function(item) {
                    var itemId = (item && item.id != null) ? Number(item.id) : Number(item);
                    return itemId !== idNum;
                });
            }

            saveWishlist(list);
            syncButtons();
        });
    });

    document.addEventListener('DOMContentLoaded', syncButtons);
    document.addEventListener('wishlistUpdated', syncButtons);
    syncButtons();
})();
</script>

<script>
(function(){
    const listBtn  = document.getElementById('listTabBtn');
    const gridBtn  = document.getElementById('gridTabBtn');
    const mapBtn   = document.getElementById('mapTabBtn');
    const listView = document.getElementById('listView');
    const gridView = document.getElementById('gridView');
    const mapView  = document.getElementById('mapView');
    const pager    = document.getElementById('paginationNav');

    if (!listBtn || !gridBtn || !mapBtn || !listView || !gridView || !mapView) {
        console.error('View toggle: one or more required elements missing from the page.');
        return;
    }

    function showView(activeView) {
        listView.classList.remove('active');
        gridView.classList.remove('active');
        mapView.classList.remove('active');
        
        listBtn.classList.remove('active');
        gridBtn.classList.remove('active');
        mapBtn.classList.remove('active');
        
        if (activeView === 'list') {
            listView.classList.add('active');
            listBtn.classList.add('active');
            if (pager) pager.style.display = '';
        } else if (activeView === 'grid') {
            gridView.classList.add('active');
            gridBtn.classList.add('active');
            if (pager) pager.style.display = '';
        } else if (activeView === 'map') {
            mapView.classList.add('active');
            mapBtn.classList.add('active');
            if (pager) pager.style.display = 'none';
            setTimeout(function() {
                if (typeof initMap === 'function') {
                    initMap();
                } else if (window.map && typeof window.map.invalidateSize === 'function') {
                    window.map.invalidateSize();
                }
            }, 300);
        }
    }

    listBtn.addEventListener('click', function() { showView('list'); });
    gridBtn.addEventListener('click', function() { showView('grid'); });
    mapBtn.addEventListener('click', function() { showView('map'); });

    showView('list');
})();
</script>

<script>
(function(){
    const properties = <?php echo json_encode($mapPayload); ?>;
    const mapEl = document.getElementById('propertiesMap');
    const statusEl = document.getElementById('mapStatus');

    if (!mapEl || !statusEl) return;

    const DEFAULT_CENTER = [22.9734, 78.6569];
    const DEFAULT_ZOOM = 5;
    const GEOCODE_CACHE_KEY = 'tatito_geocode_cache_v1';

    let map = null;
    let markersLayer = null;
    let markersPlaced = false;
    let loadingOverlay = null;
    let isInitialized = false;

    function getCache(){
        try { return JSON.parse(sessionStorage.getItem(GEOCODE_CACHE_KEY)) || {}; }
        catch(e){ return {}; }
    }
    function saveCache(cache){
        try { sessionStorage.setItem(GEOCODE_CACHE_KEY, JSON.stringify(cache)); }
        catch(e){ /* storage full or unavailable — safe to ignore */ }
    }

    function showLoadingOverlay(text){
        if (!loadingOverlay){
            loadingOverlay = document.createElement('div');
            loadingOverlay.className = 'map-loading-overlay';
            mapEl.parentElement.style.position = 'relative';
            mapEl.parentElement.insertBefore(loadingOverlay, mapEl.nextSibling === statusEl ? statusEl : mapEl.nextSibling);
        }
        loadingOverlay.innerHTML =
            '<div class="spinner-border text-primary" role="status"></div>' +
            '<div>' + text + '</div>';
        loadingOverlay.style.display = 'flex';
    }
    function hideLoadingOverlay(){
        if (loadingOverlay) loadingOverlay.style.display = 'none';
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str || '';
        return div.innerHTML;
    }

    async function geocodeAddress(address){
        const cache = getCache();
        if (cache[address]) return cache[address];

        const url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&countrycodes=in&q=' + encodeURIComponent(address);

        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return null;
            const data = await res.json();
            if (!data.length) return null;

            const coords = [parseFloat(data[0].lat), parseFloat(data[0].lon)];
            cache[address] = coords;
            saveCache(cache);
            return coords;
        } catch (err) {
            console.warn('Geocoding failed for address:', address, err);
            return null;
        }
    }

    async function placeMarkers() {
        if (!properties.length) {
            statusEl.textContent = 'No properties to show on the map for this filter.';
            return;
        }

        showLoadingOverlay('Locating ' + properties.length + ' properties on the map...');

        const bounds = [];
        let placed = 0;
        let failed = 0;

        for (let i = 0; i < properties.length; i++) {
            const p = properties[i];
            const fullQuery = p.address ? (p.address + ', India') : null;
            let coords = fullQuery ? await geocodeAddress(fullQuery) : null;

            if (!coords) {
                failed++;
                continue;
            }

            const popupHtml =
                '<div class="map-popup">' +
                    '<h6>' + escapeHtml(p.name) + '</h6>' +
                    '<div class="addr">' + escapeHtml(p.address) + '</div>' +
                    '<div class="price">' + escapeHtml(p.price) + '</div>' +
                    (p.url ? '<a class="view-link" href="' + p.url + '">View details &rarr;</a>' : '') +
                '</div>';

            L.marker(coords).addTo(markersLayer).bindPopup(popupHtml);
            bounds.push(coords);
            placed++;

            if (i < properties.length - 1) {
                await new Promise(resolve => setTimeout(resolve, 1000));
            }
        }

        hideLoadingOverlay();

        if (bounds.length) {
            map.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 });
        }

        if (placed && !failed) {
            statusEl.textContent = 'Showing all ' + placed + ' properties at their mapped address.';
        } else if (placed && failed) {
            statusEl.textContent = 'Showing ' + placed + ' of ' + properties.length + ' properties — ' + failed + ' address(es) could not be located precisely.';
        } else {
            statusEl.textContent = 'Could not locate these addresses on the map.';
        }
    }

    window.initMap = function() {
        if (isInitialized) {
            if (map) {
                setTimeout(function() {
                    map.invalidateSize();
                }, 100);
            }
            return;
        }

        if (typeof L === 'undefined') {
            statusEl.textContent = 'Map library failed to load. Check your internet connection and refresh.';
            console.error('Leaflet (L) is not defined — leaflet.js did not load.');
            return;
        }

        try {
            if (!map) {
                map = L.map(mapEl, { scrollWheelZoom: true }).setView(DEFAULT_CENTER, DEFAULT_ZOOM);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);
                markersLayer = L.layerGroup().addTo(map);
                window.map = map;
                isInitialized = true;
            }

            setTimeout(function() {
                map.invalidateSize();
                if (!markersPlaced) {
                    markersPlaced = true;
                    placeMarkers();
                }
            }, 200);
        } catch (err) {
            console.error('Map initialization failed:', err);
            statusEl.textContent = 'Something went wrong loading the map. See console for details.';
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('mapView').classList.contains('active')) {
            setTimeout(window.initMap, 500);
        }
    });

    document.getElementById('mapTabBtn').addEventListener('click', function() {
        setTimeout(window.initMap, 400);
    });
})();
</script>

<script>
(function(){
    function handleThemeChange() {
        const isDark = document.body.classList.contains('dark-mode');
    }

    document.addEventListener('themeChanged', function(e) {
        handleThemeChange();
    });

    handleThemeChange();

    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                handleThemeChange();
            }
        });
    });
    observer.observe(document.body, { attributes: true });
})();
</script>

<script>
// ===== AUTO RECORD SEARCH ON PROPERTIES PAGE =====
(function() {
    <?php
    $searchLabelParts = [];
    if (!empty($search)) $searchLabelParts[] = $search;
    if (!empty($city)) $searchLabelParts[] = $city;
    if (!empty($categorySlug)) $searchLabelParts[] = ucfirst($categorySlug);
    if (!empty($bedrooms)) $searchLabelParts[] = $bedrooms . ' BHK';
    $searchLabel = !empty($searchLabelParts) ? implode(' · ', $searchLabelParts) : (!empty($city) ? $city : (!empty($search) ? $search : ''));
    ?>
    var queryTitle = <?php echo json_encode($searchLabel); ?>;
    var queryCity = <?php echo json_encode($city ?? ''); ?>;
    var querySearch = <?php echo json_encode($search ?? ''); ?>;

    if ((queryTitle || queryCity || querySearch) && typeof window.recordTatitoSearch === 'function') {
        window.recordTatitoSearch({
            q: querySearch,
            search: querySearch,
            city: queryCity,
            title: queryTitle || queryCity || querySearch,
            url: window.location.pathname + window.location.search,
            at: Date.now()
        });
    }
})();
</script>
</body>
</html>