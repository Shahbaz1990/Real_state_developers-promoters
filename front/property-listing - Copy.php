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
// /property-listing/{value}/  ->  property-listing.php?slug={value}
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($pageTitle); ?> — <?php echo htmlspecialchars($site['name']); ?></title>

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
    --ink:#12213a;
    --muted:#6b7686;
    --gold:#f5b942;
    --blue:#1f6feb;
    --line:#e7e9ee;
    --bg-soft:#f7f8fa;
  }
  body{
    font-family:'Inter',sans-serif;
    color:var(--ink);
    background:#fff;
  }
  h1,h2,h3,.brand{ font-family:'Fraunces',serif; }
  .mono{ font-family:'IBM Plex Mono',monospace; }

  /* hero */
  .hero-wrap{ position:relative; }
  .hero-img{
    width:100%; height:min(70vh,560px); object-fit:cover; border-radius:0 0 1rem 1rem;
  }
  .hero-overlay{
    position:absolute; left:0; right:0; bottom:0;
    background:linear-gradient(0deg, rgba(0,0,0,.65), rgba(0,0,0,0));
    padding:2.5rem 2rem 1.75rem; border-radius:0 0 1rem 1rem;
  }
  .hero-overlay h1{ color:#fff; font-size:clamp(1.6rem,3vw,2.2rem); font-weight:600; margin-bottom:.15rem; }
  .hero-overlay .loc{ color:#e7e9ee; }
  .price-pill{
    background:var(--blue); color:#fff; border-radius:.5rem; padding:.4rem .9rem;
    font-weight:600; display:inline-block;
  }
  .status-pill{
    background:#fff; color:var(--ink); border-radius:.5rem; padding:.4rem .9rem; font-weight:600;
  }

  .thumb{
    width:100%; height:120px; object-fit:cover; border-radius:.6rem; cursor:pointer;
    border:2px solid transparent;
  }
  .thumb.active{ border-color:var(--blue); }

  .card-panel{
    border:1px solid var(--line); border-radius:1rem; padding:1.75rem;
    background:#fff;
  }

  .detail-item .icon{ color:var(--blue); font-size:1.1rem; }
  .detail-item .label{ color:var(--muted); font-size:.85rem; }
  .detail-item .value{ font-weight:600; }

  .amenity-chip{
    background:var(--bg-soft); border-radius:2rem; padding:.4rem 1rem;
    font-size:.9rem; display:inline-block; margin:.2rem;
  }

  .eyebrow{
    text-transform:uppercase; letter-spacing:.08em; font-size:.75rem;
    color:var(--blue); font-weight:600;
  }

  #map{ height:380px; width:100%; border-radius:.8rem; }

  .owner-name{ font-weight:700; font-size:1.1rem; }
  .verified-badge{
    background:#e6f6ec; color:#1a8f4c; font-size:.75rem; border-radius:2rem;
    padding:.15rem .6rem; display:inline-flex; align-items:center; gap:.3rem;
  }
  .contact-row{ font-size:.92rem; margin-top:.5rem; }

  .btn-tour{ background:#8fc7e8; border:none; color:#fff; font-weight:600; border-radius:.5rem; }
  .btn-tour:hover{ background:#6fb3da; color:#fff; }
  .btn-report{ background:#8fc7e8; border:none; color:#fff; font-weight:600; border-radius:.5rem; }
  .auth-note{ background:#fdeef0; color:#b6435a; font-size:.85rem; border-radius:.6rem; padding:.6rem .8rem; }

  .rating-num{ font-size:2.6rem; font-weight:700; }
  .bar-track{ background:var(--line); border-radius:1rem; height:8px; flex:1; }
  .bar-fill{ background:var(--gold); border-radius:1rem; height:8px; }

  .review-avatar{
    width:38px; height:38px; border-radius:50%; background:var(--bg-soft);
    display:flex; align-items:center; justify-content:center; font-weight:600; color:var(--blue);
  }

  section.section{ padding:2.5rem 0; }
</style>
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<?php if (!$project): ?>

  <div class="container py-5 text-center">
    <h2 class="mb-3">Project not found</h2>
    <p class="text-muted">The project you're looking for may have been removed or is no longer active.</p>
    <a href="/front/index.php" class="btn-login" style="text-decoration:none;">Back to Home</a>
  </div>

<?php else: ?>

  <?php
    $imagePath = !empty($project['image'])
        ? "../../../backend/uploads/trending-projects/" . $project['image']
        : "/front/assets/images/no-image.jpg";

    $lat = !empty($project['lat']) ? (float) $project['lat'] : 19.2183;
    $lng = !empty($project['lng']) ? (float) $project['lng'] : 72.9781;
  ?>

  <!-- ============ HERO ============ -->
  <div class="container mt-3 hero-wrap">
    <img class="hero-img" id="mainImage"
         src="<?php echo htmlspecialchars($imagePath); ?>"
         alt="<?php echo htmlspecialchars($project['project_name']); ?>">
    <div class="hero-overlay">
      <h1><?php echo htmlspecialchars($project['project_name']); ?></h1>
      <div class="loc mb-2"><?php echo htmlspecialchars(pv($project, 'address', '')); ?></div>
      <span class="price-pill"><?php echo htmlspecialchars($project['price']); ?></span>
      <span class="status-pill ms-2"><?php echo htmlspecialchars(pv($project, 'listing_type', 'For Sale')); ?></span>
    </div>
  </div>

  <div class="container mt-3">
    <div class="row g-2">
      <div class="col-4 col-md-2">
        <img class="thumb active" onclick="swapImage(this)"
             src="<?php echo htmlspecialchars($imagePath); ?>" alt="">
      </div>
      <?php foreach ($galleryImages as $galImage): ?>
        <?php $galPath = "../../../backend/uploads/trending-projects/" . $galImage; ?>
        <div class="col-4 col-md-2">
          <img class="thumb" onclick="swapImage(this)"
               src="<?php echo htmlspecialchars($galPath); ?>" alt="">
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ============ MAIN CONTENT ============ -->
  <div class="container mt-4">
    <div class="row g-4">
      <!-- LEFT COLUMN -->
      <div class="col-lg-8">

        <div class="card-panel mb-4">
          <h3 class="mb-2" style="font-size:1.4rem;">Project Details</h3>

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

        <div class="card-panel mb-4">
          <h3 class="mb-3" style="font-size:1.4rem;">Amenities</h3>
          <?php
            $amenities = !empty($project['amenities']) ? explode(',', $project['amenities']) : [];
            if ($amenities):
                foreach ($amenities as $a): ?>
                  <span class="amenity-chip"><i class="bi bi-check-circle me-1"></i><?php echo htmlspecialchars(trim($a)); ?></span>
            <?php endforeach;
            else: ?>
              <span class="text-muted">No amenities listed.</span>
            <?php endif; ?>
        </div>

        <div class="card-panel">
          <h3 class="mb-1" style="font-size:1.4rem;">Property's Location</h3>
          <p class="text-muted mb-3">Discover the exact location and surroundings of the project.</p>
          <div id="map"></div>
        </div>

      </div>

      <!-- RIGHT COLUMN -->
      <div class="col-lg-4">

        <div class="card-panel mb-3">
          <h3 class="mb-3" style="font-size:1.2rem;">Property Owner</h3>
          <div class="owner-name"><?php echo htmlspecialchars(pv($project, 'owner_name', $site['name'])); ?></div>
          <span class="verified-badge mt-1"><i class="bi bi-patch-check-fill"></i> Verified</span>
          <div class="contact-row"><i class="bi bi-envelope me-2 text-primary"></i><?php echo htmlspecialchars(pv($project, 'owner_email', 'contact@' . $_SERVER['HTTP_HOST'])); ?></div>
          <div class="contact-row"><i class="bi bi-telephone me-2 text-primary"></i><?php echo htmlspecialchars(pv($project, 'owner_phone', '—')); ?></div>
        </div>

        <div class="card-panel mb-3">
          <h3 class="mb-2" style="font-size:1.2rem;">Schedule For Site Visit</h3>
          <label class="form-label small fw-semibold">Appointment Date*</label>
          <input type="date" class="form-control mb-2" value="<?php echo htmlspecialchars(pv($project, 'appointment_date', '')); ?>">
          <div class="auth-note mb-3">You must be logged in to schedule a visit</div>
          <button class="btn btn-tour w-100 py-2">Request Site Visit</button>
        </div>

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
    function swapImage(el){
      document.getElementById('mainImage').src = el.src;
      document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
      el.classList.add('active');
    }

    const map = L.map('map').setView([<?php echo $lat; ?>, <?php echo $lng; ?>], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    L.marker([<?php echo $lat; ?>, <?php echo $lng; ?>]).addTo(map)
      .bindPopup(<?php echo json_encode($project['project_name']); ?>).openPopup();
  </script>

<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>