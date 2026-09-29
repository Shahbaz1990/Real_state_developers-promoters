<?php
// Start session FIRST - before ANY output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/config.php';

/*
 |--------------------------------------------------------------------------
 | all-hotels-homestays.php
 |--------------------------------------------------------------------------
 | Search/browse page for the Hotels & Home Stays vertical. Same filtering,
 | sorting, pagination, and list/map logic as properties.php (all against
 | the `trending_projects` table), but:
 |   - uses the shared Hotels & Home Stays header (topbar + city strip)
 |     and footer via includes/header.php / includes/footer.php - same
 |     as hotels-homestays.php, just with this page's own title, logo
 |     link, city-strip items, and extra head/body content
 |   - lays out filters in a LEFT sidebar with results on the right,
 |     instead of properties.php's top search-card + full-width grid
 |   - drops the "Property Type" (Apartments/Villas/Commercial/...) and
 |     "For Sale / For Rent" controls, since they don't apply to
 |     per-night stays
 |   - only List and Map tabs (no Grid tab)
 |   - reads the checkin / checkout / rooms / guests that the hero
 |     search on hotels-homestays.php submits, and carries them through
 |     every filter, sort, and pagination link on this page
 |
 | ASSUMPTION carried over from properties.php: `status = 'Active'`
 | (capital A) marks a live listing — matches the working properties.php
 | code you supplied.
 |--------------------------------------------------------------------------
 */

// Convert any stored price string into whole lakhs, correctly handling
// both raw-rupee strings ("₹1,25,00,000") and Cr/Lakh-suffixed strings
// ("₹1.76 Cr", "4,50,00,000 Cr.") without losing the decimal point.
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

// For a per-night stay, prices are usually small (₹649–₹5,000 range) rather
// than lakhs/crores, so display the raw rupee figure with a "/ night" caption
// when it doesn't parse as a lakh/crore amount; fall back to the Cr/L format
// for any listing that *does* use that convention.
function formatStayPrice($price) {
    if (empty($price)) return 'Price on Request';
    $price = trim($price);

    if (preg_match('/cr|lakh|lac|\bl\b/i', $price)) {
        $lakhs = priceToLakhs($price);
        if ($lakhs > 0) {
            return $lakhs >= 100
                ? '&#8377;' . rtrim(rtrim(number_format($lakhs / 100, 2), '0'), '.') . ' Cr'
                : '&#8377;' . rtrim(rtrim(number_format($lakhs, 2), '0'), '.') . ' L';
        }
    }

    $digits = preg_replace('/\D/', '', $price);
    if ($digits === '') return htmlspecialchars($price);
    return '&#8377;' . number_format((int) $digits) . ' <small>/ night</small>';
}

function pv($row, $key, $fallback = 'N/A') {
    return (!empty($row[$key])) ? $row[$key] : $fallback;
}

// Cleans up messy addresses like "Karnataka , Bangalore , Whitefield" -> "Bangalore, Whitefield"
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

// Turns an ISO ("2026-09-12") or any strtotime-parseable date string into
// a friendly display like "Sat, 12 Sep 2026". Falls back to the raw
// (escaped) string if it can't be parsed, so a malformed value never
// throws — it just displays as-is instead of a date.
function hh_fmt_date($isoDate) {
    if (empty($isoDate)) return '';
    $ts = strtotime($isoDate);
    return $ts ? date('D, d M Y', $ts) : htmlspecialchars($isoDate);
}

$selfUrl = basename($_SERVER['PHP_SELF']);

function urlWithout($param) {
    global $selfUrl;
    $query = $_GET;
    unset($query[$param]);
    unset($query['page']);
    $qs = http_build_query($query);
    return $selfUrl . ($qs !== '' ? '?' . $qs : '');
}

// Same as urlWithout() but drops several params at once — used for the
// "Dates" chip (checkin + checkout together) and the "Rooms & Guests"
// chip (rooms + guests together), so one click clears the whole group.
function urlWithoutMulti(array $paramsToRemove) {
    global $selfUrl;
    $query = $_GET;
    foreach ($paramsToRemove as $p) {
        unset($query[$p]);
    }
    unset($query['page']);
    $qs = http_build_query($query);
    return $selfUrl . ($qs !== '' ? '?' . $qs : '');
}

$search    = isset($_GET['search']) ? trim($_GET['search']) : '';
$bedrooms  = isset($_GET['bedrooms']) && $_GET['bedrooms'] !== '' ? (int) $_GET['bedrooms'] : null;
$bathrooms = isset($_GET['bathrooms']) && $_GET['bathrooms'] !== '' ? (int) $_GET['bathrooms'] : null;
$city      = isset($_GET['city']) ? trim($_GET['city']) : '';
$region    = isset($_GET['region']) ? trim($_GET['region']) : '';
$sort      = isset($_GET['sort']) ? trim($_GET['sort']) : 'Newest';
$page      = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$perPage   = 6;

// Search params carried over from the hero search on hotels-homestays.php.
// The `trending_projects` table has no per-night availability data, so
// these don't filter the query — they're carried through, displayed back
// to the user, and preserved across every other filter/sort/page change.
$checkin  = isset($_GET['checkin']) ? trim($_GET['checkin']) : '';
$checkout = isset($_GET['checkout']) ? trim($_GET['checkout']) : '';
$rooms    = isset($_GET['rooms']) && $_GET['rooms'] !== '' ? max(1, (int) $_GET['rooms']) : null;
$guests   = isset($_GET['guests']) && $_GET['guests'] !== '' ? max(1, (int) $_GET['guests']) : null;

// Number of nights, only when both dates are present and valid.
$stayNights = null;
if ($checkin !== '' && $checkout !== '') {
    $ciTs = strtotime($checkin);
    $coTs = strtotime($checkout);
    if ($ciTs && $coTs && $coTs > $ciTs) {
        $stayNights = (int) round(($coTs - $ciTs) / 86400);
    }
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

// Fetch Region (state, derived from address) + City options — sourced from
// trending_projects itself. Addresses are ordered state -> locality -> city
// (e.g. "Maharashtra , Mumbai , Andheri West"), so the state is the FIRST
// comma-separated segment, not the last.
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
                $cityName = trim(end($addrParts));
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

// Flat list of every city — used for the header city strip, the footer
// links, and the sidebar city dropdown when no region is selected yet.
$allCitiesFlat = [];
foreach ($regionCities as $cityArr) {
    foreach ($cityArr as $cn) {
        if (!in_array($cn, $allCitiesFlat, true)) $allCitiesFlat[] = $cn;
    }
}
sort($allCitiesFlat);

// Fetch matching stays
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

// Slider range — derived from the actual price data currently matching
// the other filters, not a hardcoded guess.
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
$stays      = array_slice($filtered, $offset, $perPage);

// Lightweight payload for the map tab — only what markers/popups need.
$mapPayload = array_map(function ($p) {
    $slug = !empty($p['slug']) ? $p['slug'] : null;
    return [
        'id'      => (int) $p['id'],
        'name'    => $p['project_name'],
        'address' => pv($p, 'address', ''),
        'price'   => strip_tags(formatStayPrice(pv($p, 'price', ''))),
        'url'     => $slug ? "property-listing/{$slug}/" : null,
    ];
}, $stays);

function hh_e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
function hh_city_link($city, $selfUrl) {
    return $selfUrl . '?' . http_build_query(['city' => $city]);
}

/* -------------------------------------------------------------------------
   Header / footer setup (shared includes/header.php + includes/footer.php)
   ------------------------------------------------------------------------- */
$pageTitle = 'Real Estate | Search Hotels & Home Stays';
$logoHref  = 'hotels-homestays.php';
$bodyClass = 'ahs-page';

$cityStripItems = [];
foreach ($allCitiesFlat as $c) {
    $cityStripItems[] = ['label' => $c, 'url' => hh_city_link($c, $selfUrl), 'active' => $city === $c];
}
$cityStripItems[] = ['label' => 'All Cities', 'url' => $selfUrl, 'active' => $city === ''];

$footerCityItems = [];
foreach (array_slice($allCitiesFlat, 0, 5) as $c) {
    $footerCityItems[] = ['label' => $c, 'url' => hh_city_link($c, $selfUrl)];
}

// Page-specific <head> content: Leaflet's CSS plus this page's own layout
// CSS (page head strip, sidebar filters, results list, map). Not shared
// with hotels-homestays.php, so it travels with this page instead of
// living in includes/header.php.
ob_start();
?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    body {
        overflow-x: hidden;
    }
  /* ============ Page header strip ============ */
  .ahs-page-head{
    background:linear-gradient(135deg,#0a101c 0%,#12213a 60%,#16294a 100%);
    padding:2rem 0;
  }
  .ahs-page-head h1{ color:#fff; font-family:'Fraunces', serif; font-weight:600; font-size:1.7rem; margin-bottom:.2rem; }
  .ahs-page-head p{ color:rgba(255,255,255,.7); margin:0; font-size:.9rem; }

  /* ============ Sidebar filters ============ */
  .ahs-sidebar{
    background:#fff;
    border:1px solid var(--hair);
    border-radius:1rem;
    padding:1.3rem 1.3rem 1.5rem;
    position:sticky;
    top:1rem;
  }
  .ahs-sidebar h6{
    font-weight:800; color:var(--ink); font-size:.9rem;
    text-transform:uppercase; letter-spacing:.03em; margin-bottom:1rem;
    display:flex; align-items:center; gap:.4rem;
  }
  .ahs-sidebar h6 i{ color:var(--gold); }
  .ahs-filter-block{ padding:1rem 0; border-bottom:1px solid var(--hair); }
  .ahs-filter-block:first-of-type{ padding-top:0; }
  .ahs-filter-block:last-of-type{ border-bottom:none; }
  .ahs-filter-block label{ display:block; font-weight:700; font-size:.8rem; color:var(--ink); margin-bottom:.4rem; }
  .ahs-filter-block .form-control,
  .ahs-filter-block .form-select{
    font-size:.85rem; border-radius:.5rem;
  }
  .ahs-search-btn{
    width:100%; background:var(--gold); color:var(--navy); border:none;
    font-weight:700; font-size:.85rem; padding:.55rem; border-radius:.5rem; margin-top:.6rem;
  }
  .ahs-search-btn:hover{ background:var(--gold-dark); }
  .ahs-active-filters .chip{
    display:inline-flex; align-items:center; gap:.3rem;
    background:#eaf1ff; color:var(--blue); border-radius:2rem;
    padding:.28rem .65rem; font-size:.72rem; font-weight:600;
    text-decoration:none; margin:.15rem .25rem .15rem 0;
  }
  .ahs-active-filters .chip:hover{ background:#dbe9ff; }
  .ahs-clear-btn{ color:var(--muted); font-weight:700; font-size:.8rem; text-decoration:none; }
  .ahs-clear-btn:hover{ color:var(--ink); }
  .ahs-clear-btn i{ color:var(--blue); }
  .ahs-stay-dates-summary{ font-size:.82rem; color:var(--ink); font-weight:600; margin-bottom:.2rem; }
  .ahs-stay-dates-summary i{ color:var(--blue); margin-right:.3rem; }
  .ahs-stay-guests-summary{ font-size:.82rem; color:var(--muted); margin-bottom:.6rem; }
  .ahs-stay-guests-summary i{ color:var(--blue); margin-right:.3rem; }

  /* ============ Results header ============ */
  .ahs-results-head{
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem;
    margin-bottom:1.1rem;
  }
  .ahs-results-head h2{ font-family:'Fraunces', serif; font-weight:600; color:var(--ink); font-size:1.3rem; margin:0; }
  .ahs-results-sub{ color:var(--muted); font-size:.85rem; margin-top:.2rem; }
  .ahs-results-sub i{ color:var(--blue); margin-right:.3rem; }
  .ahs-view-toggle{ display:inline-flex; border:1px solid var(--hair); border-radius:.5rem; overflow:hidden; }
  .ahs-view-toggle button{
    display:inline-flex; align-items:center; gap:.35rem;
    padding:.5rem 1rem; border:none; background:#fff; color:var(--ink);
    font-size:.85rem; font-weight:700; cursor:pointer;
  }
  .ahs-view-toggle button.active{ background:var(--navy); color:#fff; }
  .ahs-view-toggle button:hover:not(.active){ background:var(--bg-soft); }

  /* ============ Stay list cards ============ */
  .ahs-view-container{ display:none; }
  .ahs-view-container.active{ display:block; }

  .ahs-stay-card{
    display:flex; gap:0; background:#fff; border:1px solid var(--hair); border-radius:1rem;
    overflow:hidden; text-decoration:none; color:inherit;
    box-shadow:0 6px 18px rgba(18,33,58,.06);
    transition:box-shadow .2s ease, transform .2s ease;
  }
  .ahs-stay-card:hover{ box-shadow:0 16px 34px rgba(18,33,58,.14); transform:translateY(-2px); color:inherit; }
  .ahs-stay-img{
    position:relative; flex:0 0 260px; min-height:200px;
    background-size:cover; background-position:center; background-color:#eef1f5;
  }
  .ahs-stay-badge{
    position:absolute; top:.8rem; left:.8rem; background:#fff; color:var(--ink);
    font-weight:700; font-size:.7rem; padding:.25rem .6rem; border-radius:.4rem;
  }
  .ahs-wishlist-btn{
    position:absolute; top:.8rem; right:.8rem; width:34px; height:34px;
    border:none; border-radius:50%; background:rgba(255,255,255,.9);
    display:flex; align-items:center; justify-content:center; color:var(--navy);
  }
  .ahs-wishlist-btn.active{ color:#e0245e; }
  .ahs-stay-body{ flex:1; padding:1.1rem 1.3rem; display:flex; flex-direction:column; }
  .ahs-stay-body h3{ font-size:1.1rem; font-weight:800; color:var(--ink); margin-bottom:.25rem; }
  .ahs-stay-loc{ color:var(--muted); font-size:.85rem; margin-bottom:.7rem; }
  .ahs-stay-loc i{ color:var(--blue); }
  .ahs-stay-features{ display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:.7rem; }
  .ahs-stay-features span{
    display:inline-flex; align-items:center; gap:.35rem; padding:.3rem .7rem;
    border-radius:.6rem; font-size:.78rem; font-weight:600;
  }
  .ahs-f-beds{ background:#eaf1ff; color:var(--blue); }
  .ahs-f-baths{ background:#e8f9f1; color:#0f9d58; }
  .ahs-f-area{ background:#f5eeff; color:#7c3aed; }
  .ahs-amenity-chip{
    display:inline-block; background:var(--bg-soft); color:var(--muted);
    border:1px solid var(--hair); border-radius:2rem; padding:.25rem .75rem;
    margin:0 .3rem .3rem 0; font-size:.72rem; font-weight:500;
  }
  .ahs-stay-footer{ margin-top:auto; padding-top:.7rem; border-top:1px dashed var(--hair); }
  .ahs-stay-price{
    display:inline-block; background:linear-gradient(135deg,#eaf1ff,#dbe9ff); color:var(--blue);
    padding:.4rem .9rem; border-radius:.7rem; font-size:1.05rem; font-weight:800;
  }
  .ahs-stay-price small{ font-weight:600; font-size:.7rem; }

  @media (max-width:767.98px){
    .ahs-stay-card{ flex-direction:column; }
    .ahs-stay-img{ flex:0 0 190px; width:100%; }
  }

  /* ============ Map ============ */
  #ahsMapView{ display:none; width:100%; }
  #ahsMapView.active{ display:block !important; }
  #ahsPropertiesMap{
    width:100%; height:560px; background:#e5e3df; border:1px solid var(--hair);
    border-radius:1rem; overflow:hidden; position:relative; z-index:0;
  }
  .leaflet-container{ background:#e5e3df !important; font-family:inherit; }
  .ahs-map-loading-overlay{
    position:absolute; inset:0; z-index:500; display:flex; align-items:center; justify-content:center;
    flex-direction:column; gap:.6rem; background:#e5e3df; color:var(--muted); font-size:.9rem;
  }
  .ahs-map-loading-overlay .spinner-border{ width:2rem; height:2rem; }
  .ahs-map-popup h6{ margin:0 0 .2rem; color:var(--ink); font-size:.9rem; font-weight:700; }
  .ahs-map-popup .price{ color:var(--blue); font-size:.85rem; font-weight:700; }
  .ahs-map-popup .addr{ color:var(--muted); font-size:.78rem; margin-bottom:.4rem; }
  .ahs-map-popup a.view-link{ font-size:.8rem; font-weight:600; }
  .ahs-map-status{ display:flex; align-items:center; justify-content:center; gap:.5rem; padding:1rem; color:var(--muted); font-size:.9rem; }
</style>
<?php
$extraHeadHtml = ob_get_clean();

require __DIR__ . '/includes/header-hotels.php';
?>

<!-- ============ Page head ============ -->
<div class="ahs-page-head">
  <div class="container">
    <h1>Search Hotels &amp; Home Stays</h1>
    <p>Filter by city, price, and rooms to find the right stay.</p>
  </div>
</div>

<main>
  <div class="container py-4">
    <div class="row g-4">

      <!-- ============ LEFT SIDEBAR: filters ============ -->
      <aside class="col-lg-3">
        <form method="GET" id="filterForm" action="<?= hh_e($selfUrl) ?>">
          <?php if ($sort !== 'Newest'): ?>
              <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
          <?php endif; ?>
          <?php if ($city !== ''): ?>
              <input type="hidden" name="city" id="cityHiddenInput" value="<?php echo htmlspecialchars($city); ?>">
          <?php endif; ?>
          <?php if ($region !== ''): ?>
              <input type="hidden" name="region" id="regionHiddenInput" value="<?php echo htmlspecialchars($region); ?>">
          <?php endif; ?>
          <?php if ($checkin !== ''): ?>
              <input type="hidden" name="checkin" id="checkinHiddenInput" value="<?php echo htmlspecialchars($checkin); ?>">
          <?php endif; ?>
          <?php if ($checkout !== ''): ?>
              <input type="hidden" name="checkout" id="checkoutHiddenInput" value="<?php echo htmlspecialchars($checkout); ?>">
          <?php endif; ?>
          <?php if ($rooms !== null): ?>
              <input type="hidden" name="rooms" id="roomsHiddenInput" value="<?php echo (int) $rooms; ?>">
          <?php endif; ?>
          <?php if ($guests !== null): ?>
              <input type="hidden" name="guests" id="guestsHiddenInput" value="<?php echo (int) $guests; ?>">
          <?php endif; ?>

          <div class="ahs-sidebar">
            <h6><i class="bi bi-sliders"></i> Filters</h6>

            <div class="ahs-filter-block">
              <label>Search</label>
              <input type="text" name="search" id="searchInput" class="form-control"
                     placeholder="Stay name or address"
                     value="<?php echo htmlspecialchars($search); ?>">
            </div>

            <!-- Stay dates & guests, carried over from the hero search on
                 hotels-homestays.php. There's no availability data to filter
                 by, so this is a read-only summary + a link back to the
                 hero to change them — but it's preserved (via the hidden
                 inputs above) across every other filter/sort/page change. -->
            <div class="ahs-filter-block">
              <label>Stay Dates &amp; Guests</label>
              <?php if ($checkin !== '' && $checkout !== ''): ?>
                <div class="ahs-stay-dates-summary">
                  <i class="bi bi-calendar-check"></i><?php echo hh_fmt_date($checkin); ?> &rarr; <?php echo hh_fmt_date($checkout); ?>
                  <?php if ($stayNights): ?>
                    &middot; <?php echo $stayNights; ?> night<?php echo $stayNights > 1 ? 's' : ''; ?>
                  <?php endif; ?>
                </div>
              <?php else: ?>
                <div class="ahs-stay-dates-summary text-muted"><i class="bi bi-calendar"></i>No dates selected</div>
              <?php endif; ?>
              <div class="ahs-stay-guests-summary">
                <i class="bi bi-people"></i><?php echo $rooms ? (int) $rooms : 1; ?> Room<?php echo ($rooms && $rooms > 1) ? 's' : ''; ?>,
                <?php echo $guests ? (int) $guests : 1; ?> Guest<?php echo ($guests && $guests > 1) ? 's' : ''; ?>
              </div>
              <a href="hotels-homestays.php" class="ahs-clear-btn d-inline-flex align-items-center gap-2">
                <i class="bi bi-pencil"></i> Change dates &amp; guests
              </a>
            </div>

            <div class="ahs-filter-block">
              <label>Location</label>
              <select name="region" id="regionFilterSelect" class="form-select mb-2">
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

            <div class="ahs-filter-block">
              <label>Price Range (per stay)</label>
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

            <div class="ahs-filter-block">
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

            <button type="submit" class="ahs-search-btn"><i class="bi bi-search"></i> Apply Filters</button>

            <div class="ahs-filter-block">
              <label class="mb-2">Active filters</label>
              <div class="ahs-active-filters">
                <?php
                  $hasActive = false;

                  if ($search !== ''): $hasActive = true; ?>
                      <a href="<?php echo urlWithout('search'); ?>" class="chip">Search: <?php echo htmlspecialchars($search); ?> <i class="bi bi-x"></i></a>
                  <?php endif; ?>

                  <?php if ($city !== ''): $hasActive = true; ?>
                      <a href="<?php echo urlWithout('city'); ?>" class="chip">City: <?php echo htmlspecialchars($city); ?> <i class="bi bi-x"></i></a>
                  <?php endif; ?>

                  <?php if ($region !== ''): $hasActive = true; ?>
                      <a href="<?php echo urlWithout('region'); ?>" class="chip">State: <?php echo htmlspecialchars($region); ?> <i class="bi bi-x"></i></a>
                  <?php endif; ?>

                  <?php if ($checkin !== '' && $checkout !== ''): $hasActive = true; ?>
                      <a href="<?php echo urlWithoutMulti(['checkin', 'checkout']); ?>" class="chip">
                          <?php echo hh_fmt_date($checkin); ?> &rarr; <?php echo hh_fmt_date($checkout); ?> <i class="bi bi-x"></i>
                      </a>
                  <?php endif; ?>

                  <?php if ($rooms !== null || $guests !== null): $hasActive = true; ?>
                      <a href="<?php echo urlWithoutMulti(['rooms', 'guests']); ?>" class="chip">
                          <?php echo $rooms ? (int) $rooms : 1; ?> Room<?php echo ($rooms && $rooms > 1) ? 's' : ''; ?>,
                          <?php echo $guests ? (int) $guests : 1; ?> Guest<?php echo ($guests && $guests > 1) ? 's' : ''; ?> <i class="bi bi-x"></i>
                      </a>
                  <?php endif; ?>

                  <?php if ($bedrooms !== null): $hasActive = true; ?>
                      <a href="<?php echo urlWithout('bedrooms'); ?>" class="chip"><?php echo $bedrooms; ?> BHK <i class="bi bi-x"></i></a>
                  <?php endif; ?>

                  <?php if ($bathrooms !== null): $hasActive = true; ?>
                      <a href="<?php echo urlWithout('bathrooms'); ?>" class="chip"><?php echo $bathrooms; ?> Bath <i class="bi bi-x"></i></a>
                  <?php endif; ?>

                  <?php if ($minPrice > 0): $hasActive = true; ?>
                      <a href="<?php echo urlWithout('min'); ?>" class="chip">Min &#8377;<?php echo number_format($minPrice * 100000); ?> <i class="bi bi-x"></i></a>
                  <?php endif; ?>

                  <?php if ($maxPrice < $sliderMax): $hasActive = true; ?>
                      <a href="<?php echo urlWithout('max'); ?>" class="chip">Max &#8377;<?php echo number_format($maxPrice * 100000); ?> <i class="bi bi-x"></i></a>
                  <?php endif; ?>

                  <?php if (!$hasActive): ?>
                    <span class="text-muted small">No filters applied</span>
                  <?php endif; ?>
              </div>
            </div>

            <div class="pt-2">
              <a href="#" id="clearAllBtn" class="ahs-clear-btn d-inline-flex align-items-center gap-2">
                <i class="bi bi-funnel"></i> Clear all
              </a>
            </div>
          </div>
        </form>
      </aside>

      <!-- ============ RIGHT: results ============ -->
      <section class="col-lg-9">
        <div class="ahs-results-head">
          <div>
            <h2>
              <?php
              $headingParts = [];
              if ($search !== '') { $headingParts[] = 'for "<strong>' . htmlspecialchars($search) . '</strong>"'; }
              if ($city !== '')   { $headingParts[] = 'in <strong>' . htmlspecialchars($city) . '</strong>'; }
              if ($region !== '') { $headingParts[] = 'in state <strong>' . htmlspecialchars($region) . '</strong>'; }
              $headingText = implode(' ', $headingParts);
              if (!empty($headingText)) {
                  echo (int) $total . ' Stays ' . $headingText;
              } else {
                  echo (int) $total . ' Stays Found';
              }
              ?>
            </h2>
            <?php if ($checkin !== '' && $checkout !== ''): ?>
              <div class="ahs-results-sub">
                <i class="bi bi-calendar-check"></i><?php echo hh_fmt_date($checkin); ?> &rarr; <?php echo hh_fmt_date($checkout); ?>
                <?php if ($stayNights): ?>
                  (<?php echo $stayNights; ?> night<?php echo $stayNights > 1 ? 's' : ''; ?>)
                <?php endif; ?>
                &middot;
                <?php echo $rooms ? (int) $rooms : 1; ?> Room<?php echo ($rooms && $rooms > 1) ? 's' : ''; ?>,
                <?php echo $guests ? (int) $guests : 1; ?> Guest<?php echo ($guests && $guests > 1) ? 's' : ''; ?>
              </div>
            <?php endif; ?>
          </div>
          <div class="d-flex align-items-center gap-2">
            <div class="ahs-view-toggle">
              <button type="button" id="ahsListTabBtn" class="active"><i class="bi bi-list-ul"></i> List</button>
              <button type="button" id="ahsMapTabBtn"><i class="bi bi-map"></i> Map</button>
            </div>
            <select class="form-select w-auto" onchange="window.location.href=this.value">
              <?php foreach (['Newest' => 'Newest', 'Price Low-High' => 'Price Low-High', 'Price High-Low' => 'Price High-Low'] as $val => $label):
                  $q = $_GET; $q['sort'] = $val;
                  $sortUrl = $selfUrl . '?' . http_build_query($q);
              ?>
                  <option value="<?php echo htmlspecialchars($sortUrl); ?>" <?php echo $sort === $val ? 'selected' : ''; ?>><?php echo $label; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- ===== List view ===== -->
        <div id="ahsListView" class="ahs-view-container active">
          <?php if (!empty($stays)): ?>
            <div class="d-flex flex-column gap-3">
                            <?php foreach ($stays as $stay):
                  $imagePath = !empty($stay['image'])
                      ? "../backend/uploads/trending-projects/" . $stay['image']
                      : "../backend/uploads/trending-projects/project_6a5df445d17f17.80638806.jpg";
                  $amenities = !empty($stay['amenities']) ? array_slice(explode(',', $stay['amenities']), 0, 4) : [];
                  $staySlug  = !empty($stay['slug']) ? $stay['slug'] : null;
                  $stayUrl   = $staySlug ? "property-listing/{$staySlug}/" : '#';
              ?>
              <a href="<?php echo htmlspecialchars($stayUrl); ?>" class="ahs-stay-card">
                <div class="ahs-stay-img" style="background-image:url('<?php echo htmlspecialchars($imagePath); ?>');">
                  <span class="ahs-stay-badge"><i class="bi bi-patch-check-fill text-primary"></i> Verified</span>
                  <button type="button" class="ahs-wishlist-btn" data-project-id="<?php echo (int) $stay['id']; ?>" aria-label="Add to wishlist">
                    <i class="bi bi-heart"></i>
                  </button>
                </div>
                <div class="ahs-stay-body">
                  <h3><?php echo htmlspecialchars($stay['project_name']); ?></h3>
                  <div class="ahs-stay-loc"><i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars(formatAddressDisplay(pv($stay, 'address', ''))); ?></div>

                  <div class="ahs-stay-features">
                    <?php if (!empty($stay['bedrooms'])): ?>
                        <span class="ahs-f-beds"><i class="bi bi-door-open"></i> <?php echo (int) $stay['bedrooms']; ?> Beds</span>
                    <?php endif; ?>
                    <?php if (!empty($stay['bathrooms'])): ?>
                        <span class="ahs-f-baths"><i class="bi bi-droplet"></i> <?php echo (int) $stay['bathrooms']; ?> Baths</span>
                    <?php endif; ?>
                    <?php if (!empty($stay['area'])): ?>
                        <span class="ahs-f-area"><i class="bi bi-aspect-ratio"></i> <?php echo htmlspecialchars($stay['area']); ?></span>
                    <?php endif; ?>
                  </div>

                  <div>
                    <?php foreach ($amenities as $a): ?>
                        <span class="ahs-amenity-chip"><?php echo htmlspecialchars(trim($a)); ?></span>
                    <?php endforeach; ?>
                  </div>

                  <div class="ahs-stay-footer">
                    <div class="ahs-stay-price"><?php echo formatStayPrice(pv($stay, 'price', '')); ?></div>
                  </div>
                </div>
              </a>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="alert alert-warning">No stays found matching your filters.</div>
          <?php endif; ?>
        </div>

        <!-- ===== Map view ===== -->
        <div id="ahsMapView" class="ahs-view-container">
          <div id="ahsPropertiesMap"></div>
          <div id="ahsMapStatus" class="ahs-map-status"></div>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav class="mt-4" id="ahsPaginationNav">
          <ul class="pagination justify-content-center">
            <?php
              $prevQuery = $_GET; $prevQuery['page'] = max(1, $page - 1);
              $nextQuery = $_GET; $nextQuery['page'] = min($totalPages, $page + 1);
            ?>
            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo hh_e($selfUrl); ?>?<?php echo http_build_query($prevQuery); ?>">&laquo;</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): $pQuery = $_GET; $pQuery['page'] = $i; ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                    <a class="page-link" href="<?php echo hh_e($selfUrl); ?>?<?php echo http_build_query($pQuery); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo hh_e($selfUrl); ?>?<?php echo http_build_query($nextQuery); ?>">&raquo;</a>
            </li>
          </ul>
        </nav>
        <?php endif; ?>
      </section>

    </div>
  </div>
</main>

<?php
// Page-specific scripts (price slider, filter behaviour, wishlist,
// list/map tab switch, Leaflet map init). Not shared with
// hotels-homestays.php, so they travel with this page via $extraBodyHtml
// rather than living in includes/footer.php.
ob_start();
?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

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

    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearLocationFilters();
            filterForm.submit();
        }
    });

    citySelect.addEventListener('change', function() {
        if (this.value !== '') { searchInput.value = ''; }
        filterForm.submit();
    });

    regionSelect.addEventListener('change', function() {
        if (this.value !== '') {
            searchInput.value = '';
            citySelect.value = '';
        }

        const region = this.value;
        const currentCity = citySelect.value;

        while (citySelect.options.length > 1) {
            citySelect.remove(1);
        }

        const regionCities = <?php echo json_encode($regionCities); ?>;
        const allCities = <?php echo json_encode($allCitiesFlat); ?>;
        const cities = region !== '' ? (regionCities[region] || []) : allCities;

        cities.forEach(function(c) {
            const option = document.createElement('option');
            option.value = c;
            option.textContent = c;
            citySelect.appendChild(option);
        });

        if (currentCity !== '') {
            for (let i = 0; i < citySelect.options.length; i++) {
                if (citySelect.options[i].value === currentCity) {
                    citySelect.selectedIndex = i;
                    break;
                }
            }
        }

        filterForm.submit();
    });

    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function(e) {
            e.preventDefault();

            searchInput.value = '';
            citySelect.value = '';
            regionSelect.value = '';
            if (cityHidden) cityHidden.remove();
            if (regionHidden) regionHidden.remove();

            // Also drop the hero-search date/rooms/guests values — "Clear all"
            // means all filters, including the ones carried over from
            // hotels-homestays.php.
            const checkinHidden = document.getElementById('checkinHiddenInput');
            const checkoutHidden = document.getElementById('checkoutHiddenInput');
            const roomsHidden = document.getElementById('roomsHiddenInput');
            const guestsHidden = document.getElementById('guestsHiddenInput');
            if (checkinHidden) checkinHidden.remove();
            if (checkoutHidden) checkoutHidden.remove();
            if (roomsHidden) roomsHidden.remove();
            if (guestsHidden) guestsHidden.remove();

            const minInputEl = document.getElementById('minInput');
            const maxInputEl = document.getElementById('maxInput');
            const sliderMax = <?php echo $sliderMax; ?>;
            if (minInputEl) minInputEl.value = '';
            if (maxInputEl) maxInputEl.value = sliderMax * 100000;

            const priceRangeEl = document.getElementById('priceRange');
            if (priceRangeEl) priceRangeEl.value = sliderMax;

            const bedroomSelect = document.querySelector('select[name="bedrooms"]');
            const bathroomSelect = document.querySelector('select[name="bathrooms"]');
            if (bedroomSelect) bedroomSelect.value = '';
            if (bathroomSelect) bathroomSelect.value = '';

            const sortHidden = document.querySelector('input[name="sort"]');
            if (sortHidden) sortHidden.remove();

            filterForm.submit();
        });
    }
})();
</script>

<script>
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

    document.querySelectorAll('.ahs-wishlist-btn').forEach(function(btn){
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

<!-- ============ List / Map tab switch ============ -->
<script>
(function(){
    const listBtn  = document.getElementById('ahsListTabBtn');
    const mapBtn   = document.getElementById('ahsMapTabBtn');
    const listView = document.getElementById('ahsListView');
    const mapView  = document.getElementById('ahsMapView');
    const pager    = document.getElementById('ahsPaginationNav');

    if (!listBtn || !mapBtn || !listView || !mapView) return;

    function showView(activeView) {
        listView.classList.remove('active');
        mapView.classList.remove('active');
        listBtn.classList.remove('active');
        mapBtn.classList.remove('active');

        if (activeView === 'list') {
            listView.classList.add('active');
            listBtn.classList.add('active');
            if (pager) pager.style.display = '';
        } else if (activeView === 'map') {
            mapView.classList.add('active');
            mapBtn.classList.add('active');
            if (pager) pager.style.display = 'none';
            setTimeout(function() {
                if (typeof initAhsMap === 'function') {
                    initAhsMap();
                } else if (window.ahsMap && typeof window.ahsMap.invalidateSize === 'function') {
                    window.ahsMap.invalidateSize();
                }
            }, 300);
        }
    }

    listBtn.addEventListener('click', function() { showView('list'); });
    mapBtn.addEventListener('click', function() { showView('map'); });

    showView('list');
})();
</script>

<!-- Map initialization -->
<script>
(function(){
    const stays = <?php echo json_encode($mapPayload); ?>;
    const mapEl = document.getElementById('ahsPropertiesMap');
    const statusEl = document.getElementById('ahsMapStatus');

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
            loadingOverlay.className = 'ahs-map-loading-overlay';
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
        if (!stays.length) {
            statusEl.textContent = 'No stays to show on the map for this filter.';
            return;
        }

        showLoadingOverlay('Locating ' + stays.length + ' stays on the map...');

        const bounds = [];
        let placed = 0;
        let failed = 0;

        for (let i = 0; i < stays.length; i++) {
            const p = stays[i];
            const fullQuery = p.address ? (p.address + ', India') : null;
            let coords = fullQuery ? await geocodeAddress(fullQuery) : null;

            if (!coords) {
                failed++;
                continue;
            }

            const popupHtml =
    '<div class="ahs-map-popup">' +
        '<h6>' + escapeHtml(p.name) + '</h6>' +
        '<div class="addr">' + escapeHtml(p.address) + '</div>' +
        '<div class="price">' + escapeHtml(p.price) + '</div>' +
        (p.url ? '<a class="view-link" href="' + p.url + '">View details &rarr;</a>' : '') +
    '</div>';

            L.marker(coords).addTo(markersLayer).bindPopup(popupHtml);
            bounds.push(coords);
            placed++;

            if (i < stays.length - 1) {
                await new Promise(resolve => setTimeout(resolve, 1000));
            }
        }

        hideLoadingOverlay();

        if (bounds.length) {
            map.fitBounds(bounds, { padding: [40, 40], maxZoom: 15 });
        }

        if (placed && !failed) {
            statusEl.textContent = 'Showing all ' + placed + ' stays at their mapped address.';
        } else if (placed && failed) {
            statusEl.textContent = 'Showing ' + placed + ' of ' + stays.length + ' stays — ' + failed + ' address(es) could not be located precisely.';
        } else {
            statusEl.textContent = 'Could not locate these addresses on the map.';
        }
    }

    window.initAhsMap = function() {
        if (isInitialized) {
            if (map) { setTimeout(function() { map.invalidateSize(); }, 100); }
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
                window.ahsMap = map;
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
})();
</script>
<?php
$extraBodyHtml = ob_get_clean();

// $logoHref and $footerCityItems were set earlier, before includes/header.php.
$footerTagline = "World's easiest way to book verified hostels and home stays.";
require __DIR__ . '/includes/footer-hotels.php';
?>