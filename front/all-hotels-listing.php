<?php
require_once __DIR__ . '/includes/config.php';

// ===============================
// Small helper: turn "Kalpataru Vian" into "kalpataru-vian"
// ===============================
if (!function_exists('slugify')) {
    function slugify($text) {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
    }
}

// Small helper: read a column if it exists on the row, else fall back
function pv($project, $key, $fallback = 'N/A') {
    return (!empty($project[$key])) ? $project[$key] : $fallback;
}

// Small helper: pick a Bootstrap Icon class that best matches an amenity name
if (!function_exists('amenity_icon')) {
    function amenity_icon($name) {
        $name = strtolower(trim($name));
        $map = [
            'power backup'   => 'bi-lightning-charge',
            'backup'         => 'bi-lightning-charge',
            'cctv'           => 'bi-camera-video',
            'video'          => 'bi-camera-video',
            'security'       => 'bi-shield-check',
            'fire'           => 'bi-fire',
            'treated water'  => 'bi-droplet',
            'water sup'      => 'bi-droplet-fill',
            'water'          => 'bi-droplet',
            'lift'           => 'bi-arrow-up-square',
            'elevator'       => 'bi-arrow-up-square',
            'intercom'       => 'bi-telephone',
            'kids play'      => 'bi-emoji-smile',
            'play area'      => 'bi-emoji-smile',
            'kids pool'      => 'bi-water',
            'pool'           => 'bi-water',
            'swimming'       => 'bi-water',
            'senior'         => 'bi-people',
            'gym'            => 'bi-heart-pulse',
            'club'           => 'bi-building',
            'garden'         => 'bi-tree',
            'park'           => 'bi-p-square',
            'gas'            => 'bi-fuel-pump',
            'wifi'           => 'bi-wifi',
            'internet'       => 'bi-wifi',
            'jogging'        => 'bi-person-walking',
            'yoga'           => 'bi-person-arms-up',
            'temple'         => 'bi-building',
            'rain'           => 'bi-cloud-rain',
            'solar'          => 'bi-brightness-high',
            'ev charging'    => 'bi-ev-front',
            'sewage'         => 'bi-recycle',
            'waste'          => 'bi-recycle',
            'clubhouse'      => 'bi-building',
            'library'        => 'bi-book',
            'theatre'        => 'bi-camera-reels',
            'cinema'         => 'bi-camera-reels',
        ];
        foreach ($map as $keyword => $icon) {
            if (strpos($name, $keyword) !== false) {
                return $icon;
            }
        }
        return 'bi-check-circle';
    }
}

// ===============================
// Detect whether trending_projects has a real `slug` column
// (avoids a fatal SQL error if the column hasn't been added yet)
// ===============================
$hasSlugColumn = false;
$colCheck = $conn->query("SHOW COLUMNS FROM trending_projects LIKE 'slug'");
if ($colCheck && $colCheck->num_rows > 0) {
    $hasSlugColumn = true;
}

// ===============================
// Get the requested project — the .htaccess rule rewrites
// /all-hotels-listing/{value}/  ->  all-hotels-listing.php?slug={value}
// ===============================
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$id   = isset($_GET['id']) ? (int) $_GET['id'] : 0; // kept for backward-compatible old links

$project = null;

if ($slug !== '' && $hasSlugColumn) {
    // Preferred path: a real slug column exists — look it up directly
    $stmt = $conn->prepare("SELECT * FROM trending_projects WHERE slug = ? AND status = 'Active' LIMIT 1");
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $project = $stmt->get_result()->fetch_assoc();
    $stmt->close();
} elseif ($slug !== '') {
    // Fallback: no slug column yet — match by slugifying project_name on the fly
    $result = $conn->query("SELECT * FROM trending_projects WHERE status = 'Active'");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if (slugify($row['project_name']) === $slug) {
                $project = $row;
                break;
            }
        }
    }
} elseif ($id > 0) {
    // Old-style ?id= links still work
    $stmt = $conn->prepare("SELECT * FROM trending_projects WHERE id = ? AND status = 'Active' LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $project = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// The canonical slug this project should be reachable at (for the link + any redirects)
$projectSlug = $project
    ? ($hasSlugColumn && !empty($project['slug']) ? $project['slug'] : slugify($project['project_name']))
    : '';

$pageTitle = $project ? $project['project_name'] : 'Project not found';

// ===============================
// Fetch this project's gallery images (trending_project_images), if that
// table exists yet — avoids a fatal error if create-gallery-table.sql
// hasn't been run.
// ===============================
$galleryImages = [];
if ($project) {
    $hasGalleryTable = false;
    $galTableCheck = $conn->query("SHOW TABLES LIKE 'trending_project_images'");
    if ($galTableCheck && $galTableCheck->num_rows > 0) {
        $hasGalleryTable = true;
    }

    if ($hasGalleryTable) {
        $galStmt = $conn->prepare("SELECT image FROM trending_project_images WHERE project_id = ? ORDER BY image_order ASC");
        $galStmt->bind_param('i', $project['id']);
        $galStmt->execute();
        $galRes = $galStmt->get_result();
        while ($g = $galRes->fetch_assoc()) {
            $galleryImages[] = $g['image'];
        }
        $galStmt->close();
    }
}

// Fetch all cities for the city strip
$allCitiesFlat = [];
$cityResult = $conn->query("SELECT DISTINCT city FROM trending_projects WHERE status='Active' AND city IS NOT NULL AND city <> ''");
if ($cityResult) {
    while ($row = $cityResult->fetch_assoc()) {
        $allCitiesFlat[] = $row['city'];
    }
}
sort($allCitiesFlat);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($pageTitle); ?> — Hotels & Home Stays</title>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<!-- Site styles -->
<link rel="stylesheet" href="/front/assets/css/style.css">
<style>
  :root{
    --navy:#12213a;
    --navy-deep:#0a101c;
    --gold:#f5b942;
    --gold-dark:#e0a72f;
    --blue:#1f6feb;
    --ink:#12213a;
    --muted:#6b7686;
    --hair:#e7e9ee;
    --bg-soft:#f7f8fa;
  }
  body{
    font-family:'Inter',sans-serif;
    color:var(--ink);
    background:#fff;
  }
  h1,h2,h3,.brand{ font-family:'Fraunces',serif; }
  .mono{ font-family:'IBM Plex Mono',monospace; }

  /* ============ Top bar + city strip (shared with hotels-homestays.php) ============ */
  .hh-topbar{ background:#fff; border-bottom:1px solid var(--hair); padding:.7rem 0; }
  .hh-topbar .hh-logo{ font-family:'Fraunces', serif; font-weight:700; font-size:1.3rem; color:var(--navy); text-decoration:none; }
  .hh-topbar .hh-topbar-links{ display:flex; align-items:center; gap:1.6rem; flex-wrap:wrap; }
  .hh-topbar .hh-topbar-link{ display:flex; align-items:center; gap:.5rem; color:var(--ink); text-decoration:none; font-size:.85rem; }
  .hh-topbar .hh-topbar-link i{ font-size:1.1rem; color:var(--navy); }
  .hh-topbar .hh-topbar-link .lbl{ font-weight:700; display:block; line-height:1.1; }
  .hh-topbar .hh-topbar-link .sub{ color:var(--muted); font-size:.72rem; }
  .hh-login-btn{ display:flex; align-items:center; gap:.4rem; background:var(--navy); color:#fff; border:none; border-radius:.5rem; padding:.5rem .9rem; font-weight:700; font-size:.85rem; text-decoration:none; }
  .hh-login-btn:hover{ background:var(--navy-deep); color:#fff; }
  .hh-city-strip{ background:#fbfaf7; border-bottom:1px solid var(--hair); padding:.55rem 0; overflow-x:auto; white-space:nowrap; }
  .hh-city-strip a{ color:var(--ink); text-decoration:none; font-size:.85rem; font-weight:600; margin-right:1.6rem; }
  .hh-city-strip a.active{ color:var(--blue); }
  .hh-city-strip a:hover{ color:var(--blue); }

  /* ============ Footer (shared with hotels-homestays.php) ============ */
  .hh-footer{ background:var(--navy-deep); color:#c7cede; margin-top:3rem; }
  .hh-footer .hh-footer-top{ display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:1rem; padding:1.6rem 0; border-bottom:1px solid rgba(255,255,255,.1); }
  .hh-footer .hh-footer-top .hh-logo{ color:#fff; }
  .hh-footer-cols{ display:flex; flex-wrap:wrap; gap:2.5rem; padding:2rem 0; }
  .hh-footer-cols h6{ color:#fff; font-size:.8rem; text-transform:uppercase; letter-spacing:.05em; margin-bottom:.9rem; }
  .hh-footer-cols a{ display:block; color:#c7cede; text-decoration:none; font-size:.85rem; margin-bottom:.6rem; }
  .hh-footer-cols a:hover{ color:var(--gold); }
  .hh-store-btn{ display:inline-flex; align-items:center; gap:.5rem; background:#0a101c; border:1px solid rgba(255,255,255,.15); border-radius:.5rem; padding:.5rem .9rem; color:#fff; text-decoration:none; font-size:.8rem; margin-right:.6rem; }
  .hh-social{ display:flex; gap:.7rem; }
  .hh-social a{ width:34px; height:34px; border-radius:50%; border:1px solid rgba(255,255,255,.2); display:flex; align-items:center; justify-content:center; color:#fff; text-decoration:none; }
  .hh-social a:hover{ background:var(--gold); border-color:var(--gold); color:var(--navy); }
  .hh-footer-bottom{ border-top:1px solid rgba(255,255,255,.1); padding:1rem 0; font-size:.78rem; color:#8a93a3; }

  /* ============ Gallery styles ============ */
  .gallery-col{ position:relative; }
  .hero-img{
    width:100%; height:min(52vh,420px); object-fit:cover; border-radius:1rem;
    background:var(--bg-soft);
  }
  .thumb-row{
    display:flex; gap:.5rem; margin-top:.6rem; overflow-x:auto; padding-bottom:.2rem;
  }
  .thumb{
    flex:0 0 auto; width:78px; height:64px; object-fit:cover; border-radius:.5rem; cursor:pointer;
    border:2px solid transparent; opacity:.75;
  }
  .thumb:hover{ opacity:1; }
  .thumb.active{ border-color:var(--blue); opacity:1; }

  .listing-title{ font-size:clamp(1.5rem,2.6vw,2rem); font-weight:600; margin-bottom:.35rem; }
  .listing-loc{ color:var(--muted); margin-bottom:.9rem; }
  .listing-loc i{ color:var(--blue); margin-right:.3rem; }

  .price-pill{
    background:var(--blue); color:#fff; border-radius:.5rem; padding:.4rem .9rem;
    font-weight:600; display:inline-block;
  }
  .status-pill{
    background:var(--bg-soft); color:var(--ink); border:1px solid var(--hair);
    border-radius:.5rem; padding:.4rem .9rem; font-weight:600;
  }

  .card-panel{
    border:1px solid var(--hair); border-radius:1rem; padding:1.75rem;
    background:#fff;
    overflow-wrap:anywhere;
  }
  .card-compact{ padding:1.15rem 1.5rem; }
  .hero-img{ cursor:zoom-in; }

  .detail-item .icon{ color:var(--blue); font-size:1.1rem; }
  .detail-item .label{ color:var(--muted); font-size:.85rem; }
  .detail-item .value{ font-weight:600; }

  .amenity-chip{
    background:var(--bg-soft); border-radius:2rem; padding:.4rem 1rem;
    font-size:.9rem; display:inline-block; margin:.2rem;
  }

  .amenity-grid{
    display:grid; grid-template-columns:repeat(6,1fr); gap:.9rem;
  }
  @media (max-width:991px){ .amenity-grid{ grid-template-columns:repeat(4,1fr); } }
  @media (max-width:575px){ .amenity-grid{ grid-template-columns:repeat(3,1fr); } }

  .amenity-icon-card{
    border:1px solid var(--hair); border-radius:.9rem; padding:1rem .5rem;
    text-align:center; background:#fff;
  }
  .amenity-icon-card .icon-circle{
    width:46px; height:46px; border-radius:50%; border:1px solid var(--hair);
    display:flex; align-items:center; justify-content:center; margin:0 auto .55rem;
    font-size:1.2rem; color:var(--ink);
  }
  .amenity-icon-card .label{
    font-size:.82rem; color:var(--blue); font-weight:600;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
  }

  .amenity-extra{ display:none; }
  .amenity-extra.show{ display:contents; }

  .amenity-more-card{
    border:1px solid var(--hair); border-radius:.9rem; padding:1rem .5rem;
    text-align:center; background:var(--bg-soft); cursor:pointer;
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    color:var(--blue); font-weight:600; font-size:.9rem; gap:.3rem;
  }
  .amenity-more-card i{ transition:transform .2s ease; }
  .amenity-more-card.expanded i{ transform:rotate(180deg); }

  .eyebrow{
    text-transform:uppercase; letter-spacing:.08em; font-size:.75rem;
    color:var(--blue); font-weight:600;
  }

  #map{ height:380px; width:100%; border-radius:.8rem; }

  .map-wrap{ position:relative; }
  .map-view-btn{
    position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);
    background:#12162a; color:#fff; border:none; border-radius:2rem;
    padding:.7rem 1.4rem; font-weight:600; font-size:.95rem;
    display:inline-flex; align-items:center; gap:.5rem;
    box-shadow:0 8px 20px rgba(18,33,58,.28);
    z-index:1001;
  }
  .map-view-btn:hover{ background:#1f2942; color:#fff; }
  .map-view-btn i{ color:#f5b942; }

  #nearbyMapModal .modal-content{ border:none; border-radius:0; }
  #nearbyMapModal .modal-header{ border-bottom:1px solid var(--hair); padding:.9rem 1.25rem; }
  #nearbyMapModal .modal-body{ padding:0; }

  .nearby-layout{ display:flex; height:calc(100vh - 58px); }
  .nearby-sidebar{
    width:340px; flex:0 0 340px; border-right:1px solid var(--hair);
    overflow-y:auto; background:#fff;
  }
  .nearby-sidebar-header{
    font-weight:700; font-size:1rem; padding:1rem 1.1rem .5rem;
    color:var(--ink);
  }
  .nearby-map-col{ flex:1 1 auto; position:relative; }
  #nearbyMap{ height:100%; width:100%; }

  .nearby-loading{
    padding:2rem 1.1rem; text-align:center; color:var(--muted); font-size:.9rem;
  }
  .nearby-empty{ padding:1.5rem 1.1rem; color:var(--muted); font-size:.9rem; display:none; }

  .nearby-category{ border-bottom:1px solid var(--hair); }
  .nearby-category-header{
    display:flex; align-items:center; gap:.6rem;
    padding:.85rem 1.1rem; cursor:pointer; user-select:none;
  }
  .nearby-category-header .cat-icon{
    width:32px; height:32px; border-radius:50%; background:var(--bg-soft);
    display:flex; align-items:center; justify-content:center; color:var(--ink); flex-shrink:0;
  }
  .nearby-category-header .cat-label{ font-weight:700; font-size:.92rem; flex:1; }
  .nearby-category-header .cat-count{ color:var(--muted); font-size:.82rem; margin-right:.3rem; }
  .nearby-category-header .chevron{ transition:transform .2s ease; color:var(--muted); }
  .nearby-category.expanded .chevron{ transform:rotate(180deg); }

  .nearby-category-list{ display:none; padding:0 1.1rem .6rem; }
  .nearby-category.expanded .nearby-category-list{ display:block; }

  .nearby-item{
    display:flex; align-items:center; justify-content:space-between; gap:.6rem;
    padding:.5rem .3rem; border-radius:.4rem; cursor:pointer; font-size:.88rem;
  }
  .nearby-item:hover{ background:var(--bg-soft); }
  .nearby-item.active{ background:#eaf1ff; }
  .nearby-item .item-name{ color:var(--ink); }
  .nearby-item .item-dist{ color:var(--muted); font-size:.78rem; white-space:nowrap; }

  .nearby-poi-icon{
    width:30px; height:30px; border-radius:50%;
    background:#12162a; color:#fff; display:flex; align-items:center; justify-content:center;
    font-size:.85rem; border:2px solid #fff; box-shadow:0 2px 6px rgba(18,33,58,.35);
  }
  .nearby-main-icon{
    width:38px; height:38px; border-radius:50%;
    background:#f5b942; color:#12162a; display:flex; align-items:center; justify-content:center;
    font-size:1.1rem; border:3px solid #fff; box-shadow:0 3px 10px rgba(18,33,58,.4);
  }

  @media (max-width:767.98px){
    .nearby-layout{ flex-direction:column; height:calc(100vh - 58px); }
    .nearby-sidebar{ width:100%; flex:0 0 40%; border-right:none; border-bottom:1px solid var(--hair); }
    .nearby-map-col{ flex:1 1 60%; }
  }

  .owner-name{ font-weight:700; font-size:1.1rem; }
  .verified-badge{
    background:#e6f6ec; color:#1a8f4c; font-size:.75rem; border-radius:2rem;
    padding:.15rem .6rem; display:inline-flex; align-items:center; gap:.3rem;
  }
  .contact-row{
    font-size:.92rem;
    margin-top:.5rem;
    min-width:0;
    max-width:100%;
    overflow-wrap:anywhere;
    word-break:break-word;
  }

  .btn-tour{ background:#8fc7e8; border:none; color:#fff; font-weight:600; border-radius:.5rem; }
  .btn-tour:hover{ background:#6fb3da; color:#fff; }
  .btn-report{ background:#8fc7e8; border:none; color:#fff; font-weight:600; border-radius:.5rem; }
  .auth-note{ background:#fdeef0; color:#b6435a; font-size:.85rem; border-radius:.6rem; padding:.6rem .8rem; }

  .rating-num{ font-size:2.6rem; font-weight:700; }
  .bar-track{ background:var(--hair); border-radius:1rem; height:8px; flex:1; }
  .bar-fill{ background:var(--gold); border-radius:1rem; height:8px; }

  .review-avatar{
    width:38px; height:38px; border-radius:50%; background:var(--bg-soft);
    display:flex; align-items:center; justify-content:center; font-weight:600; color:var(--blue);
  }

  section.section{ padding:2.5rem 0; }

  html{ scroll-behavior:smooth; }
  :root{ --tabs-offset:0px; }
  [id]{ scroll-margin-top:calc(var(--tabs-offset) + 76px); }
  .listing-tabs-wrap{
    position:relative; z-index:20; background:#fff;
    border-bottom:1px solid var(--hair); margin-bottom:1.5rem; padding:0 .25rem;
  }
  .listing-tabs-wrap.is-fixed{
    position:fixed; top:var(--tabs-offset);
    box-shadow:0 6px 14px rgba(18,33,58,.08);
  }
  .listing-tabs{
    display:flex; flex-wrap:nowrap; overflow-x:auto; gap:.25rem;
    list-style:none; margin:0; padding:0; -ms-overflow-style:none; scrollbar-width:none;
  }
  .listing-tabs::-webkit-scrollbar{ display:none; }
  .listing-tabs li{ flex:0 0 auto; }
  .tab-link{
    display:inline-block; white-space:nowrap; text-decoration:none;
    color:var(--muted); font-weight:600; font-size:.92rem;
    padding:.9rem 1.1rem; border-bottom:3px solid transparent;
  }
  .tab-link:hover{ color:var(--blue); }
  .tab-link.active{ color:var(--blue); border-bottom-color:var(--blue); }

  /* Breadcrumb */
  .hotels-breadcrumb{
    background: var(--bg-soft);
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--hair);
  }
  .hotels-breadcrumb a{
    color: var(--blue);
    text-decoration: none;
  }
  .hotels-breadcrumb a:hover{
    text-decoration: underline;
  }
</style>
</head>
<body>

<!-- ============ Top bar (Hotels & Home Stays header) ============ -->
<div class="hh-topbar">
  <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
    <a href="hotels-homestays.php" class="hh-logo">Real Estate</a>
    <div class="hh-topbar-links">
      <a href="properties.php" class="hh-topbar-link">
        <i class="bi bi-briefcase"></i>
        <span><span class="lbl">For Business</span><span class="sub">Trusted by builders &amp; agents</span></span>
      </a>
      <a href="agents.php" class="hh-topbar-link">
        <i class="bi bi-building-add"></i>
        <span><span class="lbl">List your property</span><span class="sub">Start earning in 30 mins</span></span>
      </a>
      <a href="tel:0124-6201611" class="hh-topbar-link">
        <i class="bi bi-telephone"></i>
        <span><span class="lbl">0124-6201611</span><span class="sub">Call us to book</span></span>
      </a>
      <a href="login.php" class="hh-login-btn"><i class="bi bi-person-circle"></i> Login / Signup</a>
    </div>
  </div>
</div>

<!-- ============ City quick-links strip (dynamic, from trending_projects) ============ -->
<div class="hh-city-strip">
  <div class="container">
    <?php foreach ($allCitiesFlat as $c): ?>
      <a href="/front/all-hotels-homestays.php?city=<?php echo urlencode($c); ?>"><?php echo htmlspecialchars($c); ?></a>
    <?php endforeach; ?>
    <a href="/front/all-hotels-homestays.php">All Cities</a>
  </div>
</div>

<!-- Breadcrumb -->
<div class="hotels-breadcrumb">
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="/front/index.php">Home</a></li>
        <li class="breadcrumb-item"><a href="/front/all-hotels-homestays.php">Hotels & Home Stays</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($pageTitle); ?></li>
      </ol>
    </nav>
  </div>
</div>

<?php if (!$project): ?>

  <div class="container py-5 text-center">
    <h2 class="mb-3">Hotel not found</h2>
    <p class="text-muted">The hotel you're looking for may have been removed or is no longer active.</p>
    <a href="/front/all-hotels-homestays.php" class="btn btn-primary" style="text-decoration:none;">Back to Hotels</a>
  </div>

<?php else: ?>

  <?php
    $imagePath = !empty($project['image'])
        ? "../../../backend/uploads/trending-projects/" . $project['image']
        : "/front/assets/images/no-image.jpg";

    $lat = !empty($project['lat']) ? (float) $project['lat'] : 19.2183;
    $lng = !empty($project['lng']) ? (float) $project['lng'] : 72.9781;
  ?>

  <!-- ============ TOP: Gallery (left) + Title/Owner/Site Visit (right) ============ -->
  <div class="container mt-4">
    <div class="row g-4 align-items-start">

      <!-- LEFT: image slider -->
      <div class="col-lg-5 gallery-col">
        <img class="hero-img" id="mainImage"
             src="<?php echo htmlspecialchars($imagePath); ?>"
             alt="<?php echo htmlspecialchars($project['project_name']); ?>"
             onclick="openGalleryModal()">

        <div class="thumb-row">
          <img class="thumb active" onclick="swapImage(this)" data-index="0"
               src="<?php echo htmlspecialchars($imagePath); ?>" alt="">
          <?php foreach ($galleryImages as $i => $galImage): ?>
            <?php $galPath = "../../../backend/uploads/trending-projects/" . $galImage; ?>
            <img class="thumb" onclick="swapImage(this)" data-index="<?php echo $i + 1; ?>"
                 src="<?php echo htmlspecialchars($galPath); ?>" alt="">
          <?php endforeach; ?>
        </div>
      </div>

      <!-- RIGHT: title, address, price, owner, schedule visit -->
      <div class="col-lg-7">

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <h1 class="listing-title"><?php echo htmlspecialchars($project['project_name']); ?></h1>
            <div class="listing-loc"><i class="bi bi-geo-alt-fill"></i><?php echo htmlspecialchars(pv($project, 'address', '')); ?></div>

            <div>
              <span class="price-pill"><?php echo htmlspecialchars($project['price']); ?></span>
              <span class="status-pill ms-2"><?php echo htmlspecialchars(pv($project, 'listing_type', 'For Sale')); ?></span>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card-panel card-compact h-100">
              <div class="eyebrow mb-1">Hotel Owner</div>
              <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                <div class="owner-name"><?php echo htmlspecialchars(pv($project, 'owner_name', $site['name'])); ?></div>
                <span class="verified-badge"><i class="bi bi-patch-check-fill"></i> Verified</span>
              </div>
              <div class="d-flex flex-column flex-sm-row flex-wrap column-gap-4">
                <div class="contact-row mb-0"><i class="bi bi-envelope me-2 text-primary"></i><?php echo htmlspecialchars(pv($project, 'owner_email', 'contact@' . $_SERVER['HTTP_HOST'])); ?></div>
                <div class="contact-row mb-0"><i class="bi bi-telephone me-2 text-primary"></i><?php echo htmlspecialchars(pv($project, 'owner_phone', '—')); ?></div>
              </div>
            </div>
          </div>
        </div>

        <div class="card-panel card-compact">
          <h3 class="mb-3" style="font-size:1.2rem;">Schedule For Site Visit</h3>

          <form id="propertyEnquiryForm">
              <input type="hidden" name="page_id" value="<?php echo $project['id']; ?>">
              <input type="hidden" name="page_title" value="<?php echo htmlspecialchars($project['project_name']); ?>">
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">Name*</label>
                  <input
                      type="text"
                      name="name"
                      class="form-control"
                      placeholder="Enter Your Name"
                      required>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">Email*</label>
                  <input
                      type="email"
                      name="email"
                      class="form-control"
                      placeholder="Enter Your Email"
                      required>
                </div>
              </div>

              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">Contact Number*</label>
                  <input
                      type="text"
                      name="contact"
                      class="form-control"
                      placeholder="Enter Contact Number"
                      required>
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">Appointment Date*</label>
                  <input
                      type="date"
                      name="appointment_date"
                      class="form-control"
                      value="<?php echo htmlspecialchars(pv($project,'appointment_date','')); ?>">
                </div>
              </div>
              <div id="formMessage"></div>
              <button type="submit" class="btn btn-primary w-100 py-2">Contact Now</button>

          </form>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
          <script>
$("#propertyEnquiryForm").submit(function(e){

    e.preventDefault();

    var form = $(this);

    $.ajax({
        url: "<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); ?>/submit-property-enquiry.php",
        type: "POST",
        data: form.serialize(),
        dataType: "json",

        beforeSend:function(){
            form.find("button")
                .prop("disabled", true)
                .text("Submitting...");
        },

        success:function(data){

            if(data.status == "success"){

                $("#formMessage").html(
                    '<div class="alert alert-success">'+data.message+'</div>'
                );

                form[0].reset();

            }else{

                $("#formMessage").html(
                    '<div class="alert alert-danger">'+data.message+'</div>'
                );

            }

        },

        error:function(xhr){

    console.log("Status:", xhr.status);
    console.log("Response:", xhr.responseText);

    $("#formMessage").html(
        '<div class="alert alert-danger">'+xhr.responseText+'</div>'
    );

},

        complete:function(){
            form.find("button")
                .prop("disabled", false)
                .text("Contact Now");
        }

    });

});
</script>
        </div>

      </div>
    </div>
  </div>

  <!-- ============ Image Lightbox (opens on main image click, slides through gallery) ============ -->
  <div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content bg-dark border-0">
        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" style="z-index:5;" data-bs-dismiss="modal" aria-label="Close"></button>
        <div id="galleryCarousel" class="carousel slide" data-bs-interval="false">
          <div class="carousel-inner">
            <div class="carousel-item active">
              <img src="<?php echo htmlspecialchars($imagePath); ?>" class="d-block w-100" style="max-height:85vh;object-fit:contain;">
            </div>
            <?php foreach ($galleryImages as $galImage): ?>
              <?php $galPath = "../../../backend/uploads/trending-projects/" . $galImage; ?>
              <div class="carousel-item">
                <img src="<?php echo htmlspecialchars($galPath); ?>" class="d-block w-100" style="max-height:85vh;object-fit:contain;">
              </div>
            <?php endforeach; ?>
          </div>
          <?php if (count($galleryImages) > 0): ?>
          <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
          </button>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- ============ Nearby Places Modal (opens from the "View on Map" button) ============ -->
  <div class="modal fade" id="nearbyMapModal" tabindex="-1" aria-labelledby="nearbyMapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold" id="nearbyMapModalLabel">Around <?php echo htmlspecialchars($project['project_name']); ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="nearby-layout">
            <div class="nearby-sidebar">
              <div class="nearby-sidebar-header">Around <?php echo htmlspecialchars($project['project_name']); ?></div>
              <div class="nearby-loading" id="nearbyLoading">
                <div class="spinner-border spinner-border-sm me-2"></div> Finding nearby places...
              </div>
              <div class="nearby-empty" id="nearbyEmpty">Couldn't load nearby places right now. Please try again in a moment.</div>
              <div id="nearbyCategories"></div>
            </div>
            <div class="nearby-map-col">
              <div id="nearbyMap"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ============ BELOW: Project Details / Amenities / Map (left) + Report (right) ============ -->
  <div class="container mt-4">

    <!-- ============ Tab nav — full width, click scrolls to matching section, fixes on scroll via JS ============ -->
    <div class="row">
      <div class="col-12">
        <div class="listing-tabs-wrap" id="listingTabsWrap">
          <ul class="listing-tabs">
            <li><a class="tab-link active" href="#project-details">Hotel Details</a></li>
            <li><a class="tab-link" href="#amenities">Amenities</a></li>
            <li><a class="tab-link" href="#extra-details">Extra Details</a></li>
            <li><a class="tab-link" href="#address">Address</a></li>
            <li><a class="tab-link" href="#maps">Maps</a></li>
          </ul>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <!-- LEFT COLUMN -->
      <div class="col-lg-8">

        <div id="project-details" class="card-panel mb-4">
          <h3 class="mb-2" style="font-size:1.4rem;">Hotel Details</h3>

          <div class="row g-3 mt-2">
            <div class="col-6 col-md-4 detail-item">
              <div class="icon"><i class="bi bi-door-closed"></i></div>
              <div class="label">Bedrooms</div>
              <div class="value"><?php echo htmlspecialchars(pv($project, 'bedrooms')); ?></div>
            </div>
            <div class="col-6 col-md-4 detail-item">
              <div class="icon"><i class="bi bi-droplet-half"></i></div>
              <div class="label">Bathrooms</div>
              <div class="value"><?php echo htmlspecialchars(pv($project, 'bathrooms')); ?></div>
            </div>
            <div class="col-6 col-md-4 detail-item">
              <div class="icon"><i class="bi bi-rulers"></i></div>
              <div class="label">Area</div>
              <div class="value"><?php echo htmlspecialchars(pv($project, 'area')); ?></div>
            </div>
            <div class="col-6 col-md-4 detail-item">
              <div class="icon"><i class="bi bi-calendar3"></i></div>
              <div class="label">Year Built</div>
              <div class="value"><?php echo htmlspecialchars(pv($project, 'year_built')); ?></div>
            </div>
          </div>
        </div>

        <div id="amenities" class="card-panel mb-4">
          <h3 class="mb-3" style="font-size:1.4rem;">Amenities</h3>
          <?php
            $amenities = !empty($project['amenities'])
                ? array_values(array_filter(array_map('trim', explode(',', $project['amenities']))))
                : [];
          ?>
          <?php if ($amenities): ?>
            <?php
              $visibleCount     = 11;
              $visibleAmenities = array_slice($amenities, 0, $visibleCount);
              $extraAmenities   = array_slice($amenities, $visibleCount);
              $extraCount       = count($extraAmenities);
            ?>
            <div class="amenity-grid">
              <?php foreach ($visibleAmenities as $a): ?>
                <div class="amenity-icon-card" title="<?php echo htmlspecialchars($a); ?>">
                  <div class="icon-circle"><i class="bi <?php echo amenity_icon($a); ?>"></i></div>
                  <div class="label"><?php echo htmlspecialchars($a); ?></div>
                </div>
              <?php endforeach; ?>

              <?php if ($extraCount > 0): ?>
                <div class="amenity-extra" id="amenityExtra">
                  <?php foreach ($extraAmenities as $a): ?>
                    <div class="amenity-icon-card" title="<?php echo htmlspecialchars($a); ?>">
                      <div class="icon-circle"><i class="bi <?php echo amenity_icon($a); ?>"></i></div>
                      <div class="label"><?php echo htmlspecialchars($a); ?></div>
                    </div>
                  <?php endforeach; ?>
                </div>

                <div class="amenity-more-card" id="amenityMoreBtn">
                  <span class="more-label">+<?php echo $extraCount; ?> More</span>
                  <i class="bi bi-chevron-down"></i>
                </div>
              <?php endif; ?>
            </div>
          <?php else: ?>
            <span class="text-muted">No amenities listed.</span>
          <?php endif; ?>
        </div>

        <div id="extra-details" class="card-panel mb-4">
          <h3 class="mb-3" style="font-size:1.4rem;">Extra Details</h3>
          <div class="row g-3">
            <div class="col-6 col-md-4 detail-item">
              <div class="icon"><i class="bi bi-arrow-repeat"></i></div>
              <div class="label">Is it furnished?</div>
              <div class="value"><?php echo htmlspecialchars(pv($project, 'furnished')); ?></div>
            </div>
            <div class="col-6 col-md-4 detail-item">
              <div class="icon"><i class="bi bi-bounding-box"></i></div>
              <div class="label">Plot Area/Total Area</div>
              <div class="value"><?php echo htmlspecialchars(pv($project, 'plot_area')); ?></div>
            </div>
          </div>
          <hr class="my-3">
          <div class="small text-muted">
            Listed on <?php echo htmlspecialchars(pv($project, 'created_at', 'N/A')); ?>
            <?php if (!empty($project['updated_at'])): ?>
              &middot; Last updated <?php echo htmlspecialchars($project['updated_at']); ?>
            <?php endif; ?>
          </div>
        </div>

        <div id="address" class="card-panel mb-4">
          <h3 class="mb-2" style="font-size:1.4rem;">Address</h3>
          <div class="d-flex align-items-start gap-2">
            <i class="bi bi-geo-alt-fill text-primary mt-1"></i>
            <div><?php echo htmlspecialchars(pv($project, 'address', 'N/A')); ?></div>
          </div>
        </div>

        <div id="maps" class="card-panel">
          <h3 class="mb-1" style="font-size:1.4rem;">Hotel's Location</h3>
          <p class="text-muted mb-3">Discover the exact location and surroundings of the hotel.</p>
          <div class="map-wrap">
            <div id="map"></div>
            <button type="button" class="map-view-btn" data-bs-toggle="modal" data-bs-target="#nearbyMapModal">
              <i class="bi bi-geo-alt-fill"></i> View on Map
            </button>
          </div>
        </div>

      </div>

      <!-- RIGHT COLUMN -->
      <div class="col-lg-4">

        <div class="card-panel">
          <h3 class="mb-2" style="font-size:1.2rem;">Report Inappropriate Content</h3>
          <div class="auth-note mb-3"><i class="bi bi-exclamation-circle me-1"></i>Authentication Required<br>You must be logged in to submit a report.</div>
          <label class="form-label small fw-semibold">Reason for reporting</label>
          <select class="form-select mb-3" disabled>
            <option>Select a reason</option>
          </select>
          <button class="btn btn-report w-100 py-2">Submit Report</button>
        </div>

      </div>
    </div>
  </div>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    let currentImageIndex = 0;

    function swapImage(el){
      document.getElementById('mainImage').src = el.src;
      document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
      el.classList.add('active');
      currentImageIndex = parseInt(el.dataset.index || '0', 10);
    }

    function openGalleryModal(){
      const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('galleryModal'));
      const carousel = bootstrap.Carousel.getOrCreateInstance(document.getElementById('galleryCarousel'));
      carousel.to(currentImageIndex);
      modal.show();
    }

    // ---- Sticky tab nav: highlight active tab on click and while scrolling ----
    (function(){
      const tabLinks = document.querySelectorAll('.listing-tabs .tab-link');
      const sections = Array.from(tabLinks)
        .map(link => document.querySelector(link.getAttribute('href')))
        .filter(Boolean);

      function setActiveTab(id){
        tabLinks.forEach(l => l.classList.toggle('active', l.getAttribute('href') === '#' + id));
      }

      tabLinks.forEach(link => {
        link.addEventListener('click', function(){ setActiveTab(this.getAttribute('href').slice(1)); });
      });

      if ('IntersectionObserver' in window && sections.length){
        const observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) setActiveTab(entry.target.id);
          });
        }, { rootMargin: '-90px 0px -65% 0px', threshold: 0 });
        sections.forEach(sec => observer.observe(sec));
      }
    })();

    // ---- Pin the tab bar with JS (works even if a parent has overflow:hidden, which silently breaks CSS position:sticky) ----
    (function(){
      const tabsWrap = document.getElementById('listingTabsWrap');
      if (!tabsWrap) return;

      // Placeholder keeps the page from jumping when the bar leaves normal flow
      const placeholder = document.createElement('div');
      placeholder.style.display = 'none';
      tabsWrap.parentNode.insertBefore(placeholder, tabsWrap.nextSibling);

      let originalTop = 0; // tabsWrap's natural distance from top of document

      function fixedHeaderHeight(){
        let height = 0;
        document.querySelectorAll('header, nav, .navbar, .site-header, .header').forEach(el => {
          if (el === tabsWrap || tabsWrap.contains(el)) return;
          const pos = window.getComputedStyle(el).position;
          const rect = el.getBoundingClientRect();
          if ((pos === 'fixed' || pos === 'sticky') && rect.top <= 5 && rect.height > 0){
            height = Math.max(height, rect.bottom);
          }
        });
        return height;
      }

      function unpin(){
        tabsWrap.classList.remove('is-fixed');
        tabsWrap.style.width = '';
        tabsWrap.style.left = '';
        placeholder.style.display = 'none';
      }

      function measure(){
        const wasFixed = tabsWrap.classList.contains('is-fixed');
        if (wasFixed) unpin(); // measure true flow position, not the fixed one
        originalTop = tabsWrap.getBoundingClientRect().top + window.scrollY;
        document.documentElement.style.setProperty('--tabs-offset', fixedHeaderHeight() + 'px');
      }

      function onScroll(){
        const offset = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--tabs-offset')) || 0;
        const shouldPin = window.scrollY + offset >= originalTop;

        if (shouldPin && !tabsWrap.classList.contains('is-fixed')){
          const rect = tabsWrap.getBoundingClientRect();
          placeholder.style.height = rect.height + 'px';
          placeholder.style.display = 'block';
          tabsWrap.style.width = rect.width + 'px';
          tabsWrap.style.left = rect.left + 'px';
          tabsWrap.classList.add('is-fixed');
        } else if (!shouldPin && tabsWrap.classList.contains('is-fixed')){
          unpin();
        }
      }

      measure();
      onScroll();
      window.addEventListener('scroll', onScroll, { passive: true });
      window.addEventListener('resize', function(){ measure(); onScroll(); });
      window.addEventListener('load', function(){ measure(); onScroll(); });
    })();

    // ---- Amenities: toggle the "+N More" card ----
    (function(){
      const moreBtn = document.getElementById('amenityMoreBtn');
      const extra = document.getElementById('amenityExtra');
      if (!moreBtn || !extra) return;
      const extraCount = extra.querySelectorAll('.amenity-icon-card').length;
      const label = moreBtn.querySelector('.more-label');

      moreBtn.addEventListener('click', function(){
        const showing = extra.classList.toggle('show');
        moreBtn.classList.toggle('expanded', showing);
        label.textContent = showing ? 'Less' : ('+' + extraCount + ' More');
      });
    })();

    const map = L.map('map').setView([<?php echo $lat; ?>, <?php echo $lng; ?>], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    L.marker([<?php echo $lat; ?>, <?php echo $lng; ?>]).addTo(map)
      .bindPopup(<?php echo json_encode($project['project_name']); ?>).openPopup();
  </script>

  <!-- ============ Nearby Places modal: fetch from OpenStreetMap Overpass API, group by category, render sidebar + map ============ -->
  <script>
  (function () {
    const PROJECT_LAT   = <?php echo $lat; ?>;
    const PROJECT_LNG   = <?php echo $lng; ?>;
    const PROJECT_NAME  = <?php echo json_encode($project['project_name']); ?>;
    const SEARCH_RADIUS = 1500; // meters
    const MAX_PER_CATEGORY = 10;

    // Each category: the Overpass tag(s) it matches, a label, and a Bootstrap
    // Icon to show both in the sidebar and on the map markers.
    const CATEGORIES = [
      { key: 'school',     label: 'Schools',        icon: 'bi-mortarboard-fill', tags: [['amenity', 'school']] },
      { key: 'hospital',   label: 'Hospitals',       icon: 'bi-hospital-fill',    tags: [['amenity', 'hospital'], ['amenity', 'clinic']] },
      { key: 'bus_stop',   label: 'Bus Stops',       icon: 'bi-bus-front-fill',   tags: [['highway', 'bus_stop']] },
      { key: 'restaurant', label: 'Restaurants & Cafes', icon: 'bi-cup-hot-fill', tags: [['amenity', 'restaurant'], ['amenity', 'cafe']] },
      { key: 'bank',       label: 'Banks & ATMs',    icon: 'bi-bank',             tags: [['amenity', 'bank'], ['amenity', 'atm']] },
      { key: 'shopping',   label: 'Shopping',        icon: 'bi-bag-fill',         tags: [['shop', 'mall'], ['shop', 'supermarket']] },
      { key: 'pharmacy',   label: 'Pharmacy',        icon: 'bi-capsule',          tags: [['amenity', 'pharmacy']] },
      { key: 'park',       label: 'Parks',           icon: 'bi-tree-fill',        tags: [['leisure', 'park']] },
    ];

    let nearbyMap = null;
    let nearbyMainMarker = null;
    let poiMarkers = [];
    let dataLoaded = false;

    function haversineKm(lat1, lon1, lat2, lon2) {
      const R = 6371;
      const dLat = (lat2 - lat1) * Math.PI / 180;
      const dLon = (lon2 - lon1) * Math.PI / 180;
      const a = Math.sin(dLat / 2) ** 2 +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) ** 2;
      return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    function buildOverpassQuery() {
      let clauses = '';
      CATEGORIES.forEach(function (cat) {
        cat.tags.forEach(function (pair) {
          clauses += `  node["${pair[0]}"="${pair[1]}"](around:${SEARCH_RADIUS},${PROJECT_LAT},${PROJECT_LNG});\n`;
        });
      });
      return `[out:json][timeout:25];\n(\n${clauses});\nout body ${MAX_PER_CATEGORY * CATEGORIES.length * 3};`;
    }

    function classify(tags) {
      for (const cat of CATEGORIES) {
        for (const [k, v] of cat.tags) {
          if (tags && tags[k] === v) return cat;
        }
      }
      return null;
    }

    function poiIcon() {
      return L.divIcon({
        className: '',
        html: '<div class="nearby-poi-icon"><i class="bi bi-geo-alt-fill"></i></div>',
        iconSize: [30, 30],
        iconAnchor: [15, 15]
      });
    }

    function mainIcon() {
      return L.divIcon({
        className: '',
        html: '<div class="nearby-main-icon"><i class="bi bi-house-door-fill"></i></div>',
        iconSize: [38, 38],
        iconAnchor: [19, 19]
      });
    }

    function renderSidebar(grouped) {
      const wrap = document.getElementById('nearbyCategories');
      wrap.innerHTML = '';

      let anyResults = false;

      CATEGORIES.forEach(function (cat) {
        const items = grouped[cat.key] || [];
        if (items.length === 0) return;
        anyResults = true;

        const catEl = document.createElement('div');
        catEl.className = 'nearby-category';
        catEl.innerHTML = `
          <div class="nearby-category-header">
            <span class="cat-icon"><i class="bi ${cat.icon}"></i></span>
            <span class="cat-label">${cat.label}</span>
            <span class="cat-count">${items.length}</span>
            <i class="bi bi-chevron-down chevron"></i>
          </div>
          <div class="nearby-category-list"></div>
        `;

        const header = catEl.querySelector('.nearby-category-header');
        header.addEventListener('click', function () {
          catEl.classList.toggle('expanded');
        });

        const listEl = catEl.querySelector('.nearby-category-list');
        items.forEach(function (item) {
          const row = document.createElement('div');
          row.className = 'nearby-item';
          row.innerHTML = `
            <span class="item-name">${item.name}</span>
            <span class="item-dist">${item.distance.toFixed(2)} KM</span>
          `;
          row.addEventListener('click', function () {
            document.querySelectorAll('.nearby-item.active').forEach(el => el.classList.remove('active'));
            row.classList.add('active');
            if (nearbyMap && item.marker) {
              nearbyMap.setView([item.lat, item.lng], 16);
              item.marker.openPopup();
            }
          });
          listEl.appendChild(row);
        });

        wrap.appendChild(catEl);
      });

      // Auto-expand the first category so the sidebar isn't just a wall of headers
      const first = wrap.querySelector('.nearby-category');
      if (first) first.classList.add('expanded');

      return anyResults;
    }

    function plotMarkers(grouped) {
      poiMarkers.forEach(m => nearbyMap.removeLayer(m));
      poiMarkers = [];

      Object.keys(grouped).forEach(function (key) {
        grouped[key].forEach(function (item) {
          const marker = L.marker([item.lat, item.lng], { icon: poiIcon() }).addTo(nearbyMap);
          marker.bindPopup(`<strong>${item.name}</strong><br>${item.distance.toFixed(2)} KM away`);
          item.marker = marker;
          poiMarkers.push(marker);
        });
      });
    }

    function loadNearbyPlaces() {
      if (dataLoaded) return;
      dataLoaded = true;

      const loadingEl = document.getElementById('nearbyLoading');
      const emptyEl   = document.getElementById('nearbyEmpty');

      fetch('https://overpass-api.de/api/interpreter', {
        method: 'POST',
        body: 'data=' + encodeURIComponent(buildOverpassQuery())
      })
        .then(res => res.json())
        .then(function (data) {
          loadingEl.style.display = 'none';

          const grouped = {};
          CATEGORIES.forEach(cat => grouped[cat.key] = []);

          (data.elements || []).forEach(function (el) {
            const cat = classify(el.tags);
            if (!cat) return;
            const name = (el.tags && el.tags.name) ? el.tags.name : cat.label.replace(/s$/, '');
            const distance = haversineKm(PROJECT_LAT, PROJECT_LNG, el.lat, el.lon);
            grouped[cat.key].push({ name: name, lat: el.lat, lng: el.lon, distance: distance });
          });

          // Nearest first, capped per category so the sidebar stays scannable
          Object.keys(grouped).forEach(function (key) {
            grouped[key].sort((a, b) => a.distance - b.distance);
            grouped[key] = grouped[key].slice(0, MAX_PER_CATEGORY);
          });

          const anyResults = renderSidebar(grouped);
          if (!anyResults) {
            emptyEl.textContent = 'No nearby places found within ' + (SEARCH_RADIUS / 1000) + ' km.';
            emptyEl.style.display = 'block';
          } else {
            plotMarkers(grouped);
          }
        })
        .catch(function () {
          loadingEl.style.display = 'none';
          emptyEl.style.display = 'block';
          dataLoaded = false; // allow retry next time the modal opens
        });
    }

    const nearbyModalEl = document.getElementById('nearbyMapModal');
    if (nearbyModalEl) {
      nearbyModalEl.addEventListener('shown.bs.modal', function () {
        if (!nearbyMap) {
          nearbyMap = L.map('nearbyMap').setView([PROJECT_LAT, PROJECT_LNG], 15);
          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
          }).addTo(nearbyMap);
          nearbyMainMarker = L.marker([PROJECT_LAT, PROJECT_LNG], { icon: mainIcon() })
            .addTo(nearbyMap)
            .bindPopup(PROJECT_NAME)
            .openPopup();
        }
        // Leaflet mis-sizes a map created while its container was hidden (display:none),
        // so nudge it once the modal has finished animating in.
        setTimeout(function () { nearbyMap.invalidateSize(); }, 200);

        loadNearbyPlaces();
      });
    }
  })();
  </script>

  <!-- ============ Recently Viewed tracking (localStorage, no login required) ============ -->
  <script>
  (function () {
      const RECENT_KEY = 'tatito_recently_viewed_hotels';
      const MAX_ITEMS = 10;
      const currentProjectId = <?php echo (int) $project['id']; ?>;

      if (!currentProjectId) return;

      let list = [];
      try { list = JSON.parse(localStorage.getItem(RECENT_KEY)) || []; }
      catch (e) { list = []; }

      list = list.filter(function (r) { return r.id !== currentProjectId; });

      list.unshift({ id: currentProjectId, viewedAt: new Date().toISOString() });

      list = list.slice(0, MAX_ITEMS);

      localStorage.setItem(RECENT_KEY, JSON.stringify(list));
  })();
  </script>

<?php endif; ?>

<!-- ============ Footer (shared with hotels-homestays.php) ============ -->
<footer class="hh-footer">
  <div class="container">
    <div class="hh-footer-top">
      <a href="hotels-homestays.php" class="hh-logo">Real Estate</a>
      <div style="color:#c7cede; font-size:.9rem;">World's easiest way to book verified hostels and home stays.</div>
      <a href="agents.php" class="hh-login-btn" style="background:var(--gold); color:var(--navy);">List your property</a>
    </div>

    <div class="hh-footer-cols">
      <div>
        <h6>Get the app</h6>
        <div class="d-flex flex-wrap gap-2">
          <a href="#" class="hh-store-btn"><i class="bi bi-apple"></i> App Store</a>
          <a href="#" class="hh-store-btn"><i class="bi bi-google-play"></i> Google Play</a>
        </div>
      </div>
      <div>
        <h6>Company</h6>
        <a href="#">About Us</a>
        <a href="#">Careers</a>
        <a href="agents.php">Agents</a>
      </div>
      <div>
        <h6>Support</h6>
        <a href="#">Help Center</a>
        <a href="#">Terms &amp; Conditions</a>
        <a href="#">Privacy Policy</a>
      </div>
      <div>
        <h6>Explore stays</h6>
        <?php foreach (array_slice($allCitiesFlat, 0, 5) as $c): ?>
          <a href="/front/all-hotels-homestays.php?city=<?php echo urlencode($c); ?>">Stays in <?php echo htmlspecialchars($c); ?></a>
        <?php endforeach; ?>
      </div>
      <div>
        <h6>Follow us</h6>
        <div class="hh-social">
          <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
          <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
        </div>
      </div>
    </div>

    <div class="hh-footer-bottom">
      &copy; <?php echo date('Y'); ?> Real Estate. All rights reserved.
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>