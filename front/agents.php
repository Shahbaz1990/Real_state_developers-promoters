<?php
// Start session FIRST - before ANY output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/config.php';

/*
 |--------------------------------------------------------------------------
 | Actual `agents` table columns in use here:
 | id, name, slug, photo, specialization, designation, phone, email,
 | city, locality, experience_years, listings_count, rating,
 | reviews_count, status, agent_order, created_at
 |--------------------------------------------------------------------------
 */

function pv($row, $key, $fallback = 'N/A') {
    return (!empty($row[$key])) ? $row[$key] : $fallback;
}

// Map specialization codes to display labels
$specializationLabels = [
    'buy'              => 'Buy Specialist',
    'rent'             => 'Rental Specialist',
    'vendor'           => 'Vendor Relations',
    'interior'         => 'Interior Consultant',
    'local_services'   => 'Local Services',
    'hotels_homestays' => 'Hotels & Homestays',
    'pg_hostels'       => 'PG & Hostels',
];
function specLabel($code, $map) {
    return $map[$code] ?? ucwords(str_replace('_', ' ', $code));
}

// --- Get URL Parameters ---
$search         = isset($_GET['search']) ? trim($_GET['search']) : '';
$specialization = isset($_GET['specialization']) ? trim($_GET['specialization']) : '';
$city           = isset($_GET['city']) ? trim($_GET['city']) : '';
$sort           = isset($_GET['sort']) ? trim($_GET['sort']) : 'Rating';
$page           = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$perPage        = 12;

// ===============================
// Build the WHERE clause
// ===============================
$where  = ["status = 'Active'"];
$types  = '';
$params = [];

if ($search !== '') {
    $where[] = "(name LIKE ? OR designation LIKE ? OR locality LIKE ?)";
    $like = "%{$search}%";
    $types .= 'sss';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if ($specialization !== '') {
    $where[] = "specialization = ?";
    $types .= 's';
    $params[] = $specialization;
}

if ($city !== '') {
    $where[] = "city = ?";
    $types .= 's';
    $params[] = $city;
}

$whereSql = implode(' AND ', $where);

$orderSql = "agent_order ASC, rating DESC";
if ($sort === 'Experience')  $orderSql = "experience_years DESC";
if ($sort === 'Reviews')     $orderSql = "reviews_count DESC";
if ($sort === 'Newest')      $orderSql = "created_at DESC";
if ($sort === 'Rating')      $orderSql = "rating DESC, reviews_count DESC";

// --- Distinct cities & specializations for filter dropdowns ---
$cityOptions = [];
$cityResult = $conn->query("SELECT DISTINCT city FROM agents WHERE status='Active' AND city IS NOT NULL AND city <> '' ORDER BY city ASC");
if ($cityResult) {
    while ($row = $cityResult->fetch_assoc()) {
        $cityOptions[] = $row['city'];
    }
}

$specOptions = [];
$specResult = $conn->query("SELECT DISTINCT specialization FROM agents WHERE status='Active' AND specialization IS NOT NULL AND specialization <> '' ORDER BY specialization ASC");
if ($specResult) {
    while ($row = $specResult->fetch_assoc()) {
        $specOptions[] = $row['specialization'];
    }
}

// --- Fetch matching agents ---
$sql  = "SELECT * FROM agents WHERE {$whereSql} ORDER BY {$orderSql}";
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

// --- Pagination ---
$total      = count($allMatches);
$totalPages = max(1, (int) ceil($total / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;
$agents     = array_slice($allMatches, $offset, $perPage);

function urlWithout($param) {
    $query = $_GET;
    unset($query[$param]);
    unset($query['page']);
    $qs = http_build_query($query);
    return 'agents.php' . ($qs !== '' ? '?' . $qs : '');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Agents</title>
    <link rel="icon" type="image/png" href="<?php echo $site['favicon']; ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $site['favicon']; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/property-search.css">
    <link rel="stylesheet" href="assets/css/style.css">

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

body.dark-mode { background: var(--dm-bg-primary); color: var(--dm-text-primary); }
body.dark-mode .search-card { background: var(--dm-bg-secondary); }
body.dark-mode .search-input-group { background: var(--dm-bg-tertiary); border-color: var(--dm-border); }
body.dark-mode .search-input-group .search-input { color: var(--dm-text-primary); background: var(--dm-bg-tertiary); }
body.dark-mode .filter-col label { color: var(--dm-text-secondary); }
body.dark-mode .form-select, body.dark-mode .form-control {
    background: var(--dm-bg-tertiary); border-color: var(--dm-border); color: var(--dm-text-primary);
}
body.dark-mode .form-select option { background: var(--dm-bg-secondary); color: var(--dm-text-primary); }
body.dark-mode .clear-btn { color: var(--dm-text-secondary); }
body.dark-mode .clear-btn:hover { color: var(--dm-text-primary); }
body.dark-mode .agent-card { background: var(--dm-bg-secondary); border-color: var(--dm-border); }
body.dark-mode .agent-name { color: var(--dm-text-primary); }
body.dark-mode .agent-meta { color: var(--dm-text-secondary); }
body.dark-mode .spec-badge { background: var(--dm-bg-tertiary); color: var(--blue); }
body.dark-mode .agent-stats { border-color: var(--dm-border); }
body.dark-mode .page-link { background: var(--dm-bg-secondary); border-color: var(--dm-border); color: var(--dm-text-secondary); }
body.dark-mode .page-link:hover { background: var(--dm-bg-tertiary); color: var(--dm-text-primary); }
body.dark-mode .page-item.active .page-link { background: var(--blue); border-color: var(--blue); color: #fff; }
body.dark-mode .alert-warning { background: var(--dm-bg-tertiary); border-color: var(--dm-border); color: var(--dm-text-secondary); }
body.dark-mode .text-muted { color: var(--dm-text-secondary) !important; }

.hero-section {
    position: relative;
    background: var(--header-bg);
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
.hero-section .container { position: relative; z-index: 1; }
.hero-section h2 { color: var(--header-text); }
.hero-section p { color: rgba(255,255,255,.75); }
body.dark-mode .hero-section { background: var(--dm-bg-primary); }
body.dark-mode .hero-section h2 { color: var(--dm-text-primary); }
body.dark-mode .hero-section p { color: var(--dm-text-secondary); }

.search-card {
    background: #fff;
    border: none;
    border-radius: .8rem;
    padding: 1rem 1.15rem;
    box-shadow: 0 12px 30px rgba(10,16,28,.20);
    margin-top: 1rem;
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
}
.search-field-inner { display: flex; align-items: center; flex: 1 1 auto; min-width: 0; }
.search-input-group .input-group-text { background: transparent; border: none; padding: .5rem .65rem; }
.search-input-group .search-input {
    flex: 1 1 auto; min-width: 0; border: none; background: transparent;
    box-shadow: none; font-size: .9rem; padding: .55rem .5rem;
}
.search-input-group .search-input:focus { box-shadow: none; background: transparent; }
.search-btn {
    border: none;
    background: linear-gradient(135deg,var(--gold),#e0a72f);
    color: var(--ink);
    font-weight: 700;
    font-size: .85rem;
    padding: 0 1.35rem;
    white-space: nowrap;
    transition: filter .15s ease;
}
.filter-col .form-select {
    min-height: 40px; height: 40px; padding: .35rem .7rem;
    font-size: .82rem; border-radius: .5rem;
}
.clear-btn { color: var(--muted); font-weight: 600; font-size: .78rem; }
.clear-btn:hover { color: var(--ink); }
.clear-btn i { color: var(--blue); }

.agent-card {
    display: flex;
    flex-direction: column;
    height: 100%;
    background: #fff;
    border: 1px solid var(--line);
    border-radius: 1.1rem;
    overflow: hidden;
    box-shadow: 0 6px 20px var(--shadow-color);
    transition: box-shadow .2s ease, transform .2s ease;
}
.agent-card:hover {
    box-shadow: 0 16px 34px var(--shadow-hover);
    transform: translateY(-4px);
}
.agent-photo-wrap {
    position: relative;
    height: 210px;
    background: #f0f2f5;
    overflow: hidden;
}
.agent-photo-wrap img {
    width: 100%; height: 100%; object-fit: cover;
}
.agent-body { padding: 1.15rem 1.25rem 1.3rem; display: flex; flex-direction: column; flex: 1; }
.agent-name { font-size: 1.05rem; font-weight: 800; color: var(--ink); margin-bottom: .15rem; }
.agent-designation { font-size: .82rem; color: var(--blue); font-weight: 600; margin-bottom: .55rem; }
.agent-meta { font-size: .82rem; color: var(--muted); display: flex; align-items: center; gap: .35rem; margin-bottom: .3rem; }
.agent-meta i { color: var(--blue); }
.spec-badge {
    display: inline-flex; align-items: center; gap: .3rem;
    background: #eaf1ff; color: var(--blue);
    border-radius: 2rem; padding: .25rem .7rem;
    font-size: .72rem; font-weight: 700; margin-bottom: .6rem;
    width: fit-content;
}
.agent-rating { display: flex; align-items: center; gap: .3rem; font-size: .85rem; font-weight: 700; color: var(--ink); margin-bottom: .5rem; }
.agent-rating i { color: var(--gold); }
.agent-stats {
    display: flex; justify-content: space-between;
    border-top: 1px dashed var(--line);
    padding-top: .75rem; margin-top: auto;
    font-size: .78rem; color: var(--muted);
}
.agent-stats strong { color: var(--ink); font-size: .85rem; }
.agent-actions { display: flex; gap: .5rem; margin-top: .8rem; }
.agent-actions a {
    flex: 1; text-align: center;
    padding: .5rem .6rem;
    border-radius: .5rem;
    font-size: .82rem;
    font-weight: 700;
    text-decoration: none;
}
.agent-actions .btn-call { background: var(--ink); color: #fff; }
.agent-actions .btn-call:hover { filter: brightness(1.15); color: #fff; }
.agent-actions .btn-email { background: var(--bg-soft); color: var(--ink); border: 1px solid var(--line); }
.agent-actions .btn-email:hover { background: var(--line); }
.agent-actions .btn-login-view {
    flex: 1;
    text-align: center;
    padding: .5rem .6rem;
    border-radius: .5rem;
    font-size: .8rem;
    font-weight: 700;
    text-decoration: none;
    background: var(--bg-soft);
    color: var(--muted);
    border: 1px dashed var(--line);
}
.agent-actions .btn-login-view:hover {
    background: var(--line);
    color: var(--ink);
}
body.dark-mode .agent-actions .btn-login-view {
    background: var(--dm-bg-tertiary);
    color: var(--dm-text-secondary);
    border-color: var(--dm-border);
}
@media (max-width:991.98px){
    .filters-grid { grid-template-columns: repeat(2,1fr); gap: .85rem 1rem; }
}
@media (max-width:575.98px){
    .hero-section { padding: 2rem 0 4.5rem; }
    .hero-section h2 { font-size: 1.5rem; }
    .search-card { padding: .85rem; border-radius: .7rem; }
    .filters-grid { grid-template-columns: 1fr; gap: .85rem; }
    .search-input-group { flex-direction: column; align-items: stretch; min-height: auto; border-radius: .7rem; }
    .search-btn { width: 100%; min-height: 42px; padding: .55rem 1rem; font-size: .9rem; }
    .agent-photo-wrap { height: 180px; }
}
</style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<!-- HERO SECTION -->
<section class="hero-section py-4">
<div class="container">
    <div class="text-center mb-3">
        <h2 class="display-6 fw-bold">Meet Our Agents</h2>
        <p>Verified property experts across buy, rent, interiors, and more</p>
    </div>

    <div class="search-card">
        <form method="GET" id="filterForm">
            <?php if ($sort !== 'Rating'): ?>
                <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
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
                           placeholder="Search by agent name or locality"
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <button class="search-btn" type="submit">Search</button>
            </div>

            <div class="filters-grid mb-3">
                <div class="filter-col">
                    <label>Specialization</label>
                    <select name="specialization" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">All Specializations</option>
                        <?php foreach ($specOptions as $s): ?>
                            <option value="<?php echo htmlspecialchars($s); ?>" <?php echo $specialization === $s ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(specLabel($s, $specializationLabels)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-col">
                    <label>City</label>
                    <select name="city" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">All Cities</option>
                        <?php foreach ($cityOptions as $c): ?>
                            <option value="<?php echo htmlspecialchars($c); ?>" <?php echo $city === $c ? 'selected' : ''; ?>><?php echo htmlspecialchars($c); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-col">
                    <label>Sort By</label>
                    <select name="sort" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <?php foreach (['Rating' => 'Top Rated', 'Experience' => 'Most Experienced', 'Reviews' => 'Most Reviewed', 'Newest' => 'Newest'] as $val => $label): ?>
                            <option value="<?php echo $val; ?>" <?php echo $sort === $val ? 'selected' : ''; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>

        <div class="mt-2">
            <a href="agents.php" class="clear-btn text-decoration-none d-inline-flex align-items-center gap-2">
                <i class="bi bi-funnel"></i> Clear all
            </a>
        </div>
    </div>
</div>
</section>

<!-- RESULTS SECTION -->
<section class="py-5">
<div class="container">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <h2 class="fw-bold mb-0">
            <?php
            $headingParts = [];
            if ($search !== '') $headingParts[] = 'for "<strong>' . htmlspecialchars($search) . '</strong>"';
            if ($city !== '') $headingParts[] = 'in <strong>' . htmlspecialchars($city) . '</strong>';
            if ($specialization !== '') $headingParts[] = 'specializing in <strong>' . htmlspecialchars(specLabel($specialization, $specializationLabels)) . '</strong>';
            $headingText = implode(' ', $headingParts);
            if (!empty($headingText)) {
                echo (int) $total . ' Agents Found ' . $headingText;
            } else {
                echo (int) $total . ' Agents Available';
            }
            ?>
        </h2>
    </div>

    <div class="row g-4">
        <?php if (!empty($agents)): ?>
            <?php foreach ($agents as $agent):
                $photoPath = !empty($agent['photo'])
                    ? "../admin/uploads/agents/" . $agent['photo']
                    : "https://placehold.co/400x400/12213a/eef1fb?text=" . urlencode(explode(' ', $agent['name'])[0]);

                if (!empty($agent['photo']) && !file_exists("../admin/uploads/agents/" . $agent['photo'])) {
                    $photoPath = "https://placehold.co/400x400/12213a/eef1fb?text=" . urlencode(explode(' ', $agent['name'])[0]);
                }

                $rating = (float) pv($agent, 'rating', 0);
            ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="agent-card">
                    <div class="agent-photo-wrap">
                        <img src="<?php echo htmlspecialchars($photoPath); ?>" alt="<?php echo htmlspecialchars($agent['name']); ?>" loading="lazy">
                    </div>
                    <div class="agent-body">
                        <div class="spec-badge">
                            <i class="bi bi-briefcase"></i>
                            <?php echo htmlspecialchars(specLabel($agent['specialization'], $specializationLabels)); ?>
                        </div>
                        <div class="agent-name"><?php echo htmlspecialchars($agent['name']); ?></div>
                        <?php if (!empty($agent['designation'])): ?>
                            <div class="agent-designation"><?php echo htmlspecialchars($agent['designation']); ?></div>
                        <?php endif; ?>

                        <?php if (!empty($agent['city']) || !empty($agent['locality'])): ?>
                        <div class="agent-meta">
                            <i class="bi bi-geo-alt"></i>
                            <?php echo htmlspecialchars(trim(implode(', ', array_filter([$agent['locality'], $agent['city']])))); ?>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($agent['experience_years'])): ?>
                        <div class="agent-meta">
                            <i class="bi bi-award"></i>
                            <?php echo (int) $agent['experience_years']; ?> yrs experience
                        </div>
                        <?php endif; ?>

                        <div class="agent-rating">
                            <i class="bi bi-star-fill"></i>
                            <?php echo number_format($rating, 1); ?>
                            <span class="text-muted fw-normal">(<?php echo (int) pv($agent, 'reviews_count', 0); ?> reviews)</span>
                        </div>

                        <div class="agent-stats">
                            <div><strong><?php echo (int) pv($agent, 'listings_count', 0); ?></strong><br>Listings</div>
                            <div><strong><?php echo (int) pv($agent, 'experience_years', 0); ?>+</strong><br>Years</div>
                            <div><strong><?php echo number_format($rating, 1); ?></strong><br>Rating</div>
                        </div>

                        <?php $isLoggedIn = isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0; ?>

                        <div class="agent-actions">
                            <?php if ($isLoggedIn): ?>
                                <?php if (!empty($agent['phone'])): ?>
                                    <a href="tel:<?php echo htmlspecialchars(preg_replace('/\s+/', '', $agent['phone'])); ?>" class="btn-call">
                                        <i class="bi bi-telephone-fill"></i> Call
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($agent['email'])): ?>
                                    <a href="mailto:<?php echo htmlspecialchars($agent['email']); ?>" class="btn-email">
                                        <i class="bi bi-envelope"></i> Email
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="login.php?redirect=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="btn-login-view">
                                    <i class="bi bi-lock-fill"></i> Login to view contact
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning">No agents found matching your filters.</div>
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
                <a class="page-link" href="agents.php?<?php echo http_build_query($prevQuery); ?>">&laquo;</a>
            </li>
            <?php
            $startPage = max(1, $page - 2);
            $endPage = min($totalPages, $page + 2);

            if ($startPage > 1) {
                echo '<li class="page-item"><a class="page-link" href="agents.php?' . http_build_query(array_merge($_GET, ['page' => 1])) . '">1</a></li>';
                if ($startPage > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }

            for ($i = $startPage; $i <= $endPage; $i++):
                $pQuery = $_GET; $pQuery['page'] = $i;
            ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                    <a class="page-link" href="agents.php?<?php echo http_build_query($pQuery); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor;

            if ($endPage < $totalPages) {
                if ($endPage < $totalPages - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                echo '<li class="page-item"><a class="page-link" href="agents.php?' . http_build_query(array_merge($_GET, ['page' => $totalPages])) . '">' . $totalPages . '</a></li>';
            }
            ?>
            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="agents.php?<?php echo http_build_query($nextQuery); ?>">&raquo;</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>