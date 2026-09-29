<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit;
}
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Recently Viewed';

$projects = [];
$sql = "SELECT id, project_name, slug, image, city, price, address FROM trending_projects WHERE status='Active' ORDER BY project_order ASC, created_at DESC";
$res = $conn->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) { $projects[] = $row; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($pageTitle); ?> - Tatito Developers</title>
<link rel="icon" type="image/png" href="<?php echo $site['favicon']; ?>">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/account-pages.css">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

<section class="page-hero">
    <div class="container">
        <nav class="small mb-1">
            <a href="/profile.php" class="text-white-50 text-decoration-none">Account</a>
            <span class="text-white-50 mx-1">/</span>
            <span class="text-white-50">My Activity</span>
            <span class="text-white-50 mx-1">/</span>
            <span><i class="bi bi-eye me-1"></i><?php echo $pageTitle; ?></span>
        </nav>
        <h1><i class="bi bi-eye me-2"></i><?php echo $pageTitle; ?></h1>
        <p>Properties you have looked at recently.</p>
    </div>
</section>

<section class="page-shell">
    <div class="container">
        <div class="act-card">
            <div class="card-head d-flex justify-content-between align-items-center">
                <h5>View History</h5>
                <button type="button" class="btn btn-sm btn-outline-danger" id="clearViewedBtn"><i class="bi bi-trash"></i> Clear History</button>
            </div>
            <div class="card-body">
                <div class="empty-state d-none" id="emptyViewed">
                    <i class="bi bi-eye"></i>
                    <h5>Nothing viewed yet</h5>
                    <p>Properties you open will show up here.</p>
                    <a href="/properties.php" class="btn btn-gold mt-2">Browse Properties</a>
                </div>
                <div class="vstack gap-3" id="viewedList"></div>
            </div>
        </div>
    </div>
</section>

<script>
(function () {
    const STORAGE_KEY = typeof getTatitoStorageKey === 'function' ? getTatitoStorageKey('viewed') : 'tatito_guest_viewed';

    var results = <?php echo json_encode($projects); ?>;

    function readStorage() {
        try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || []; }
        catch (e) { return []; }
    }
    function clearAll() {
        try { localStorage.removeItem(STORAGE_KEY); } catch (e) {}
        location.reload();
    }

    function esc(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }
    function price(p) { return p && p.price ? p.price : 'Price on Request'; }
    function addr(p) {
        if (p && p.address) {
            var parts = String(p.address).split(',').map(function (x) { return x.trim(); }).filter(Boolean).slice(-2);
            if (parts.length) return parts.join(', ');
        }
        return (p && p.city) ? p.city : '';
    }

    function render() {
        var list = readStorage();
        var listEl = document.getElementById('viewedList');
        var emptyEl = document.getElementById('emptyViewed');
        if (!list.length) {
            listEl.innerHTML = '';
            emptyEl.classList.remove('d-none');
            return;
        }
        var byId = {};
        results.forEach(function (p) { byId[p.id] = p; });
        var html = '';
        list.forEach(function (item) {
            var p = byId[item.id];
            var when = item.viewedAt ? new Date(item.viewedAt).toLocaleString() : 'Recently';
            var img = p && p.image
                ? '../backend/uploads/trending-projects/' + p.image
                : '../backend/uploads/trending-projects/project_6a5df445d17f17.80638806.jpg';
            var url = p && p.slug ? '/property-listing/' + p.slug + '/' : '/properties.php';
            var name = p ? p.project_name : 'Property #' + item.id;
            html +=
                '<div class="property-mini">' +
                    '<img src="' + esc(img) + '" alt="' + esc(name) + '">' +
                    '<div class="flex-grow-1 min-w-0">' +
                        '<a class="fw-semibold prop-link d-block" href="' + esc(url) + '">' + esc(name) + '</a>' +
                        '<div class="meta small">' +
                            (p && p.city ? '<i class="bi bi-geo-alt me-1"></i>' + esc(addr(p)) + ' &middot; ' : '') +
                            '<span class="fw-semibold text-body">' + esc(price(p)) + '</span>' +
                        '</div>' +
                    '</div>' +
                    '<div class="text-end ms-auto d-none d-md-block">' +
                        '<small class="text-muted">Viewed on ' + when + '</small>' +
                    '</div>' +
                '</div>';
        });
        listEl.innerHTML = html;
        emptyEl.classList.add('d-none');
    }

    document.getElementById('clearViewedBtn').addEventListener('click', clearAll);
    render();
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>