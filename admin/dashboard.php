<?php
require_once 'auth_check.php';

/*
 * Dashboard metric helpers.
 * All queries read only, use only internal (non-user) parameters, and fail
 * softly so the page still renders if the database returns an error.
 */

function dash_val($conn, $sql)
{
    $res = @$conn->query($sql);
    if ($res === false) {
        return 0;
    }
    $row = $res->fetch_row();
    $res->free();
    return $row === null ? 0 : (int) $row[0];
}

function dash_avg($conn, $sql)
{
    $res = @$conn->query($sql);
    if ($res === false) {
        return 0;
    }
    $row = $res->fetch_row();
    $res->free();
    return $row[0] === null ? 0 : round((float) $row[0], 2);
}

function dash_map($conn, $sql)
{
    $res = @$conn->query($sql);
    if ($res === false) {
        return [];
    }
    $out = [];
    while ($row = $res->fetch_assoc()) {
        $out[] = $row;
    }
    $res->free();
    return $out;
}

// Zero-filled keys for the last $n calendar months (oldest -> newest).
function dash_months($n)
{
    $range = [];
    $d = new DateTime('first day of this month');
    for ($i = 0; $i < $n; $i++) {
        $range[$d->format('Y-m')] = 0;
        $d->modify('-1 month');
    }
    return array_reverse($range, true);
}

function dash_short_date($value)
{
    if ($value === null || $value === '') {
        return '<span class="text-muted">—</span>';
    }
    $ts = strtotime($value);
    return date('d M Y', $ts ?: time());
}

function dash_enq_badge($status)
{
    $map = ['New' => 'bg-warning text-dark', 'Contacted' => 'bg-primary', 'Closed' => 'bg-secondary'];
    $cls = $map[$status] ?? 'bg-secondary';
    return '<span class="badge ' . $cls . '">' . htmlspecialchars($status ?: 'New') . '</span>';
}

/* ---------------------------------------------------------------
 * 1. Top stat cards
 * --------------------------------------------------------------- */
$propertyListings = dash_val($conn, "SELECT COUNT(*) FROM trending_projects WHERE status='Active'");
$totalEnquiries   = dash_val($conn, "SELECT COUNT(*) FROM enquiries");
$newEnquiries     = dash_val($conn, "SELECT COUNT(*) FROM enquiries WHERE status='New'");
$appointments     = dash_val($conn, "SELECT COUNT(*) FROM property_contact_enquiries");
$blogPosts        = dash_val($conn, "SELECT COUNT(*) FROM blogs WHERE status='Active'");
$reviewCount      = dash_val($conn, "SELECT COUNT(*) FROM reviews WHERE status='Active'");
$reviewAvg        = dash_avg($conn, "SELECT AVG(rating) FROM reviews WHERE status='Active'");
$totalMembers     = dash_val($conn, "SELECT COUNT(*) FROM users");
$unverified       = dash_val($conn, "SELECT COUNT(*) FROM users WHERE email_verified = 0");

/* ---------------------------------------------------------------
 * 2. Chart data (zero-filled where required)
 * --------------------------------------------------------------- */
$enquiryTrend = dash_months(6);
foreach (dash_map($conn, "SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS c FROM enquiries GROUP BY ym") as $r) {
    if (isset($enquiryTrend[$r['ym']])) {
        $enquiryTrend[$r['ym']] = (int) $r['c'];
    }
}

$catRows = dash_map($conn, "SELECT c.name AS name, COUNT(p.id) AS c
                            FROM property_categories c
                            LEFT JOIN trending_projects p ON p.category_id = c.id AND p.status = 'Active'
                            GROUP BY c.id, c.name, c.category_order ORDER BY c.category_order");
$projectsByCat = ['labels' => [], 'values' => []];
foreach ($catRows as $r) {
    $n = (int) $r['c'];
    if ($n > 0) {
        $projectsByCat['labels'][] = $r['name'];
        $projectsByCat['values'][] = $n;
    }
}

$chartData = [
    'enquiries' => [
        'type'   => 'bar',
        'labels' => array_keys($enquiryTrend),
        'values' => array_values($enquiryTrend),
        'colors' => ['#5c4cc9'],
        'empty'  => 'No enquiries have been submitted in the last 6 months.',
    ],
    'projects' => [
        'type'   => 'doughnut',
        'labels' => $projectsByCat['labels'],
        'values' => $projectsByCat['values'],
        'colors' => ['#5c4cc9', '#3fa1e4', '#bd4f9e', '#ff7c44', '#0d9488', '#16a34a'],
        'empty'  => 'No active property listings yet.',
    ],
];

/* ---------------------------------------------------------------
 * 3. Recent data (latest 5 of each)
 * --------------------------------------------------------------- */
$recentEnquiries = dash_map($conn, "SELECT full_name, enquiry_type, preferred_locality, budget_range, status, source_page, created_at
                                     FROM enquiries ORDER BY created_at DESC LIMIT 5");
$recentMembers   = dash_map($conn, "SELECT fullname, mobile_number, email_verified, created_at, last_login
                                     FROM users ORDER BY created_at DESC LIMIT 5");
$recentBlogs     = dash_map($conn, "SELECT title, category, author, status, created_at
                                     FROM blogs ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html>
    <head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/adminlte.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    </head>
    <style>
    .small-box{
      min-height: 160px;
    }
    .small-box > .inner{
      padding: 28px 18px;
    }
    .small-box .inner h3{
      font-size: 1.25rem;
      margin: 0 0 8px;
      line-height: 1.1;
      font-weight: 700;
    }
    @media (min-width: 1200px){
      .small-box .inner h3{ font-size: 1.5rem; }
    }
    /* Back Layer Wave */
    .small-box::before {
      content: "";
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 55px;
      background: rgba(255, 255, 255, 0.14);
      z-index: 2;
      clip-path: polygon(0% 72%, 24% 53%, 52% 83%, 78% 58%, 100% 72%, 100% 100%, 0% 100%);
    }
    .small-box::after {
      content: "";
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 55px;
      background: rgba(255, 255, 255, 0.22);
      z-index: 3;
      clip-path: polygon(0% 88%, 28% 68%, 58% 81%, 84% 53%, 100% 68%, 100% 100%, 0% 100%);
    }
    .box-purple { background: linear-gradient(135deg, #5c4cc9, #765bc8); }
    .box-blue   { background: linear-gradient(135deg, #3fa1e4, #54b2ea); }
    .box-pink   { background: linear-gradient(135deg, #bd4f9e, #d64f8c); }
    .box-orange { background: linear-gradient(135deg, #ff7c44, #ffa53d); }
    .box-teal   { background: linear-gradient(135deg, #0d9488, #14b8a6); }
    .box-green  { background: linear-gradient(135deg, #16a34a, #22c55e); }

    .small-box-link{ display:block; cursor:pointer; text-decoration:none; color:#fff; }
    .small-box-link .small-box{ transition:transform .15s ease, box-shadow .15s ease; }
    .small-box-link:hover .small-box{ transform:translateY(-2px); box-shadow:0 10px 22px rgba(18,33,58,.18); }

    .chart-wrap { position: relative; height: 260px; }
    .quick-action-btn { min-width: 150px; }
    </style>
<body>
    <div class="app-wrapper">
    <?php include 'nav.php'; include 'sidebar.php'; ?>
      <main class="app-main">
        <div class="app-content-header"><div class="container-fluid"><div class="row"><div class="col-sm-6"><h1 class="mb-0 fs-3">Dashboard</h1></div><div class="col-sm-6"><nav aria-label="breadcrumb"><ol class="breadcrumb float-sm-end"><li class="breadcrumb-item"><a href="#">Home</a></li><li class="breadcrumb-item active" aria-current="page">Dashboard</li></ol></nav></div></div></div></div>
        <div class="app-content">
          <div class="container-fluid">
            <h5 class="mb-2">Welcome <?php echo htmlspecialchars($_SESSION['fullname']); ?> <span class="badge <?= ($_SESSION['role'] ?? '') === 'admin' ? 'bg-primary' : 'bg-secondary' ?>"><?php echo ucfirst($_SESSION['role'] ?? 'user'); ?></span></h5>

            <!-- Row 1: Stat cards -->
            <div class="row g-3">
              <div class="col-12 col-sm-6 col-xl-4"><a class="small-box-link" href="trending-projects.php"><div class="small-box box-purple text-white"><div class="inner"><h3><?php echo $propertyListings; ?></h3><p>Property Listings</p></div></div></a></div>
              <div class="col-12 col-sm-6 col-xl-4"><a class="small-box-link" href="coming-soon.php?p=Enquiries"><div class="small-box box-blue text-white"><div class="inner"><h3><?php echo $totalEnquiries; ?></h3><p><?php echo $newEnquiries; ?> New pending</p></div></div></a></div>
              <div class="col-12 col-sm-6 col-xl-4"><div class="small-box box-pink text-white"><div class="inner"><h3><?php echo $appointments; ?></h3><p>Contact Requests</p></div></div></div>
              <div class="col-12 col-sm-6 col-xl-4"><a class="small-box-link" href="blog.php"><div class="small-box box-orange text-white"><div class="inner"><h3><?php echo $blogPosts; ?></h3><p>Blog Posts</p></div></div></a></div>
              <div class="col-12 col-sm-6 col-xl-4"><a class="small-box-link" href="coming-soon.php?p=Reviews"><div class="small-box box-teal text-white"><div class="inner"><h3><?php echo $reviewCount; ?></h3><p>Avg <?php echo $reviewAvg; ?> &#9733;</p></div></div></a></div>
              <div class="col-12 col-sm-6 col-xl-4"><a class="small-box-link" href="coming-soon.php?p=Members"><div class="small-box box-green text-white"><div class="inner"><h3><?php echo $totalMembers; ?></h3><p><?php echo $unverified; ?> unverified</p></div></div></a></div>
            </div>

            <!-- Row 2: Enquiries trend | Projects by category -->
            <div class="row g-3 mt-1">
              <div class="col-xl-6 col-12">
                <div class="card shadow-sm"><div class="card-header"><h3 class="card-title fs-6 mb-0"><i class="bi bi-chat-left-text me-1"></i>Enquiries — Last 6 Months</h3></div>
                <div class="card-body"><div class="chart-wrap"><canvas id="chartEnquiries" data-chart="enquiries"></canvas></div><div class="chart-fallback d-none text-center text-muted py-4" data-fallback-for="enquiries">Enquiries chart could not be loaded.</div></div></div>
              </div>
              <div class="col-xl-6 col-12">
                <div class="card shadow-sm"><div class="card-header"><h3 class="card-title fs-6 mb-0"><i class="bi bi-building me-1"></i>Projects by Category</h3></div>
                <div class="card-body"><div class="chart-wrap"><canvas id="chartProjects" data-chart="projects"></canvas></div><div class="chart-fallback d-none text-center text-muted py-4" data-fallback-for="projects">Projects chart could not be loaded.</div></div></div>
              </div>
            </div>

            <!-- Row 3: Recent enquiries | Recent members -->
            <div class="row g-3 mt-1">
              <div class="col-xl-6 col-12">
                <div class="card shadow-sm"><div class="card-header"><h3 class="card-title fs-6 mb-0"><i class="bi bi-inbox me-1"></i>Recent Enquiries</h3></div>
                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                      <thead><tr><th>Name</th><th>Type</th><th>Locality</th><th>Budget</th><th>Status</th><th>Source</th><th>Created</th></tr></thead>
                      <tbody>
                        <?php if (empty($recentEnquiries)): ?>
                          <tr><td colspan="7" class="text-center py-4 text-muted">No enquiries yet.</td></tr>
                        <?php else: foreach ($recentEnquiries as $e): ?>
                          <?php $srcRaw = (string) ($e['source_page'] ?? ''); $srcShort = preg_replace('#^https?://#i', '', $srcRaw); if (strlen($srcShort) > 26) { $srcShort = substr($srcShort, 0, 24) . '…'; } ?>
                          <tr>
                            <td><?php echo htmlspecialchars($e['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($e['enquiry_type'] ?: 'Enquiry'); ?></td>
                            <td><?php echo htmlspecialchars($e['preferred_locality'] ?: '—'); ?></td>
                            <td><?php echo htmlspecialchars($e['budget_range'] ?: '—'); ?></td>
                            <td><?php echo dash_enq_badge($e['status']); ?></td>
                            <td title="<?php echo htmlspecialchars($srcRaw); ?>"><?php echo $srcShort ? htmlspecialchars($srcShort) : '<span class="text-muted">—</span>'; ?></td>
                            <td><?php echo dash_short_date($e['created_at']); ?></td>
                          </tr>
                        <?php endforeach; endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div></div>
              </div>
              <div class="col-xl-6 col-12">
                <div class="card shadow-sm"><div class="card-header"><h3 class="card-title fs-6 mb-0"><i class="bi bi-person-badge me-1"></i>Recent Members</h3></div>
                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                      <thead><tr><th>Name</th><th>Mobile</th><th>Verification</th><th>Created</th><th>Last Login</th></tr></thead>
                      <tbody>
                        <?php if (empty($recentMembers)): ?>
                          <tr><td colspan="5" class="text-center py-4 text-muted">No members yet.</td></tr>
                        <?php else: foreach ($recentMembers as $m): ?>
                          <tr>
                            <td><?php echo htmlspecialchars($m['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($m['mobile_number'] ?: '—'); ?></td>
                            <td><?php echo !empty($m['email_verified']) ? '<span class="badge bg-success">Verified</span>' : '<span class="badge bg-secondary">Unverified</span>'; ?></td>
                            <td><?php echo dash_short_date($m['created_at']); ?></td>
                            <td><?php echo dash_short_date($m['last_login']); ?></td>
                          </tr>
                        <?php endforeach; endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div></div>
              </div>
            </div>

            <!-- Row 4: Recent blog posts | Quick actions -->
            <div class="row g-3 mt-1 mb-3">
              <div class="col-xl-6 col-12">
                <div class="card shadow-sm"><div class="card-header"><h3 class="card-title fs-6 mb-0"><i class="bi bi-journal-text me-1"></i>Recent Blog Posts</h3></div>
                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                      <thead><tr><th>Title</th><th>Category</th><th>Author</th><th>Status</th><th>Created</th></tr></thead>
                      <tbody>
                        <?php if (empty($recentBlogs)): ?>
                          <tr><td colspan="5" class="text-center py-4 text-muted">No blog posts yet.</td></tr>
                        <?php else: foreach ($recentBlogs as $b): ?>
                          <?php $t = (string) $b['title']; if (strlen($t) > 40) { $t = substr($t, 0, 38) . '…'; } ?>
                          <tr>
                            <td title="<?php echo htmlspecialchars($b['title']); ?>"><?php echo htmlspecialchars($t); ?></td>
                            <td><?php echo htmlspecialchars($b['category'] ?: '—'); ?></td>
                            <td><?php echo htmlspecialchars($b['author'] ?: '—'); ?></td>
                            <td><?php echo $b['status'] === 'Active' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>'; ?></td>
                            <td><?php echo dash_short_date($b['created_at']); ?></td>
                          </tr>
                        <?php endforeach; endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div></div>
              </div>
              <div class="col-xl-6 col-12">
                <div class="card shadow-sm h-100"><div class="card-header"><h3 class="card-title fs-6 mb-0"><i class="bi bi-lightning me-1"></i>Quick Actions</h3></div>
                <div class="card-body">
                  <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-primary btn-sm quick-action-btn" href="trending-projects.php"><i class="bi bi-building-add me-1"></i>Add Project</a>
                    <a class="btn btn-info btn-sm quick-action-btn" href="blog.php"><i class="bi bi-plus-square me-1"></i>Add Blog</a>
                    <a class="btn btn-warning btn-sm quick-action-btn" href="banner.php"><i class="bi bi-image me-1"></i>Add Banner</a>
                    <a class="btn btn-success btn-sm quick-action-btn" href="city.php"><i class="bi bi-geo-alt me-1"></i>Manage Cities</a>
                    <a class="btn btn-secondary btn-sm quick-action-btn" href="property-categories.php"><i class="bi bi-tags me-1"></i>Manage Categories</a>
                  </div>
                </div></div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
    <?php include __DIR__ . '/includes/ui_footer.php'; ?>
    <script>
    // Charts are a progressive enhancement: if Chart.js cannot load (offline /
    // CDN blocked), every chart container falls back to a short message while
    // the rest of the dashboard still works normally.
    (function () {
        "use strict";
        var CHARTS = <?php echo json_encode($chartData); ?>;
        var CHARTS_SCRIPT = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js';

        function allCanvases() {
            return document.querySelectorAll('canvas[data-chart]');
        }
        function fallbackFor(name) {
            return document.querySelector('[data-fallback-for="' + name + '"]');
        }
        function showEmptyState(name, message) {
            var canvas = document.querySelector('canvas[data-chart="' + name + '"]');
            var fallback = fallbackFor(name);
            if (canvas) canvas.closest('.chart-wrap').classList.add('d-none');
            if (fallback) {
                fallback.textContent = message || fallback.textContent;
                fallback.classList.remove('d-none');
            }
        }

        function renderCharts() {
            Object.keys(CHARTS).forEach(function (name) {
                var cfg = CHARTS[name];
                var canvas = document.querySelector('canvas[data-chart="' + name + '"]');
                if (!canvas) return;

                var sum = (cfg.values || []).reduce(function (a, b) { return a + b; }, 0);
                if (sum === 0) { showEmptyState(name, cfg.empty); return; }

                new Chart(canvas, {
                    type: cfg.type,
                    data: {
                        labels: cfg.labels,
                        datasets: [{
                            label: name,
                            data: cfg.values,
                            backgroundColor: cfg.type === 'doughnut' ? cfg.colors : cfg.colors[0],
                            borderColor: cfg.type === 'doughnut' ? '#ffffff' : cfg.colors[0],
                            borderWidth: cfg.type === 'doughnut' ? 2 : 1,
                            fill: cfg.type === 'line' ? true : false,
                            tension: cfg.type === 'line' ? 0.3 : undefined
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: cfg.type === 'doughnut', position: 'bottom' }
                        },
                        scales: cfg.type === 'doughnut' ? {} : {
                            y: { beginAtZero: true, ticks: { precision: 0 } }
                        }
                    }
                });
            });
        }

        function showAllFallbacks() {
            document.querySelectorAll('canvas[data-chart]').forEach(function (canvas) {
                canvas.closest('.chart-wrap').classList.add('d-none');
            });
            document.querySelectorAll('.chart-fallback').forEach(function (el) {
                el.classList.remove('d-none');
            });
        }

        if (!window.Chart) {
            var s = document.createElement('script');
            s.src = CHARTS_SCRIPT;
            s.onload = function () { if (window.Chart) { renderCharts(); } else { showAllFallbacks(); } };
            s.onerror = function () { showAllFallbacks(); };
            document.head.appendChild(s);
        } else {
            renderCharts();
        }
    })();
    </script>
</body>
</html>