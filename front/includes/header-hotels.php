<?php
/* =========================================================================
   includes/header-hotels.php
   -------------------------------------------------------------------------
   Shared header shell for the Hotels & Home Stays page family
   (hotels-homestays.php, all-hotels-homestays.php, ...).

   Only truly shared chrome lives here: doctype/head boilerplate, the
   --navy/--gold/etc CSS variables, the top bar, and the city strip.
   Anything page-specific (hero styles, sidebar/filter styles, map styles,
   Leaflet's CSS, ...) is NOT here - each page passes its own via
   $extraHeadHtml so this file stays reusable instead of accumulating
   every page's CSS.

   Variables a page may set BEFORE requiring this file (all optional):

     $pageTitle       string   <title> text.
                       Default: 'Real Estate | Hotels and home stays'

     $logoHref        string   Where the "Real Estate" logo links to.
                       Default: 'index.php'

     $bodyClass       string   Extra class(es) on <body>.
                       Default: '' (none)

     $extraHeadHtml   string   Raw HTML appended just before </head> -
                       use this for page-specific <link>/<style> tags
                       (e.g. Leaflet's CSS, or a page's own layout CSS).
                       Default: '' (none)

     $cityStripItems  array    List of city-strip links, each:
                         ['label' => 'Mumbai', 'url' => '...', 'active' => bool]
                       Build this in the including page (it knows how to
                       construct its own city URLs / which city, if any,
                       is currently selected). Default: [] (strip doesn't
                       render at all if empty).

   hh_e() is defined here (guarded) so this file never fatals if required
   standalone, but every page in this project already defines its own
   copy before requiring this file.

   NOTE: The "not logged in" state now uses the same single combined
   "Sign In / Register" button (and same behavior — opens #authModal in
   login mode) as includes/header.php, instead of two separate
   Login / Register buttons.
   ========================================================================= */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('hh_e')) {
    function hh_e($value) {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

// Check if config is loaded
if (!isset($site) && file_exists(__DIR__ . '/../backend/config.php')) {
    require_once __DIR__ . '/../backend/config.php';
}

$pageTitle      = $pageTitle      ?? 'Real Estate | Hotels and home stays';
$logoHref       = $logoHref       ?? 'index.php';
$bodyClass      = $bodyClass      ?? '';
$extraHeadHtml  = $extraHeadHtml  ?? '';
$cityStripItems = (isset($cityStripItems) && is_array($cityStripItems)) ? $cityStripItems : [];

// ===== Logo (mirrors includes/header.php's brand panel) =====
$hhHeaderSettings = [
    'logo'       => '',
    'site_title' => $site['name'] ?? 'Tatito Developers',
];
if (isset($conn)) {
    $hhHsResult = $conn->query("SELECT * FROM header_settings ORDER BY id DESC LIMIT 1");
    if ($hhHsResult && $hhHsResult->num_rows > 0) {
        $hhHeaderSettings = array_merge($hhHeaderSettings, $hhHsResult->fetch_assoc());
    }
}
$hhBaseURL = "/";
$hhHeaderLogoPath = !empty($hhHeaderSettings['logo'])
    ? $hhBaseURL . "admin/uploads/logos/" . $hhHeaderSettings['logo']
    : $hhBaseURL . "front/assets/images/logo.jpeg";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= hh_e($pageTitle) ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  body{ font-family:'Inter', sans-serif; }

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

  /* ============ Top bar ============ */
  .hh-topbar{
    background:#fff;
    border-bottom:1px solid var(--hair);
    padding:.7rem 0;
  }
  .hh-topbar .hh-logo{
    display:flex;
    align-items:center;
    text-decoration:none;
  }
  .hh-topbar .hh-brand-icon-ring{
    width:44px;
    height:44px;
    display:flex;
    align-items:center;
    justify-content:center;
    overflow:hidden;
    flex-shrink:0;
  }
  .hh-topbar .hh-brand-icon-ring img{
    width:100%;
    height:100%;
    object-fit:contain;
  }
  @media (min-width:992px){
    .hh-topbar .hh-brand-icon-ring{ width:60px; height:60px; }
  }
  .hh-topbar .hh-topbar-links{
    display:flex; align-items:center; gap:1.6rem; flex-wrap:wrap;
  }
  .hh-topbar .hh-topbar-link{
    display:flex; align-items:center; gap:.5rem;
    color:var(--ink); text-decoration:none; font-size:.85rem;
  }
  .hh-topbar .hh-topbar-link i{ font-size:1.1rem; color:var(--navy); }
  .hh-topbar .hh-topbar-link .lbl{ font-weight:700; display:block; line-height:1.1; }
  .hh-topbar .hh-topbar-link .sub{ color:var(--muted); font-size:.72rem; }

  /* ===== Sign In / Register button (matches header.php's .btn-register) ===== */
  .hh-register-btn{
    display:flex; align-items:center; gap:.4rem;
    background:var(--gold); color:var(--navy); border:none; border-radius:.5rem;
    padding:.5rem .9rem; font-weight:700; font-size:.85rem; text-decoration:none;
  }
  .hh-register-btn:hover{ background:var(--gold-dark); color:var(--navy); }

  .hh-user-menu-btn{
    display:flex; align-items:center; gap:.4rem;
    background:var(--navy); color:#fff; border:none; border-radius:.5rem;
    padding:.5rem .9rem; font-weight:700; font-size:.85rem; text-decoration:none;
  }
  .hh-user-menu-btn:hover{ background:var(--navy-deep); color:#fff; }

  .hh-user-menu-btn .dropdown-toggle::after {
    margin-left: 0.5rem;
  }

  .hh-user-dropdown .dropdown-menu {
    min-width: 200px;
    padding: 0.5rem;
    border-radius: 0.5rem;
    border: 1px solid var(--hair);
    box-shadow: 0 8px 24px rgba(18,33,58,.12);
  }

  .hh-user-dropdown .dropdown-item {
    padding: 0.5rem 0.75rem;
    border-radius: 0.3rem;
    font-size: 0.85rem;
    color: var(--ink);
  }

  .hh-user-dropdown .dropdown-item:hover {
    background: var(--bg-soft);
    color: var(--ink);
  }

  .hh-user-dropdown .dropdown-item i {
    margin-right: 0.5rem;
    width: 1.1rem;
    text-align: center;
  }

  .hh-user-dropdown .dropdown-item.text-danger:hover {
    background: #fde8e8;
    color: #dc3545;
  }

  .hh-user-dropdown .dropdown-divider {
    margin: 0.3rem 0;
    border-color: var(--hair);
  }

  .hh-user-dropdown .dropdown-item-text {
    color: var(--muted);
    font-size: 0.8rem;
  }

  .hh-user-dropdown .dropdown-item-text strong {
    color: var(--ink);
    font-weight: 700;
  }

  /* ============ City strip ============ */
  .hh-city-strip{
    background:#fbfaf7;
    border-bottom:1px solid var(--hair);
    padding:.55rem 0;
    overflow-x:auto;
    white-space:nowrap;
  }
  .hh-city-strip a{
    color:var(--ink); text-decoration:none; font-size:.85rem; font-weight:600;
    margin-right:1.6rem;
  }
  .hh-city-strip a:hover{ color:var(--blue); }
  .hh-city-strip a.active{ color:var(--blue); font-weight:800; }

  /* ============ Auth Modal Styles ============ */
  #authModal .modal-content {
    border-radius: 0.9rem;
    border: none;
    box-shadow: 0 20px 48px rgba(18,33,58,.2);
  }
  #authModal .modal-header {
    border-bottom: 1px solid var(--hair);
    padding: 1.2rem 1.5rem;
  }
  #authModal .modal-header .modal-title {
    font-weight: 700;
    color: var(--navy);
  }
  #authModal .modal-body {
    padding: 1.5rem;
  }
  #authModal .form-label {
    font-weight: 600;
    color: var(--ink);
    font-size: 0.85rem;
  }
  #authModal .form-control {
    border-radius: 0.5rem;
    border: 1.5px solid var(--hair);
    padding: 0.6rem 1rem;
    font-size: 0.9rem;
  }
  #authModal .form-control:focus {
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(245, 185, 66, 0.15);
  }
  #authModal .btn-auth-submit {
    background: var(--gold);
    color: var(--navy);
    font-weight: 700;
    padding: 0.7rem 1.5rem;
    border: none;
    border-radius: 0.5rem;
    font-size: 0.95rem;
    width: 100%;
  }
  #authModal .btn-auth-submit:hover {
    background: var(--gold-dark);
    color: var(--navy);
  }
  #authModal .btn-auth-submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }
  #authModal .auth-alert {
    display: none;
    border-radius: 0.5rem;
    padding: 0.7rem 1rem;
    font-size: 0.85rem;
  }
  #authModal .auth-switch-link {
    color: var(--blue);
    cursor: pointer;
    font-weight: 600;
    text-decoration: none;
  }
  #authModal .auth-switch-link:hover {
    text-decoration: underline;
  }
  #authModal .password-toggle {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: var(--muted);
    background: none;
    border: none;
    padding: 0;
  }
  #authModal .password-toggle:hover {
    color: var(--ink);
  }
  .password-wrapper {
    position: relative;
  }
  .password-wrapper .form-control {
    padding-right: 2.8rem;
  }

  /* ============ Responsive ============ */
  @media (max-width: 992px) {
    .hh-topbar .hh-topbar-links {
      gap: 0.8rem;
    }
    .hh-topbar .hh-topbar-link .sub {
      display: none;
    }
    .hh-topbar .hh-topbar-link .lbl {
      font-size: 0.75rem;
    }
    .hh-register-btn, .hh-user-menu-btn {
      padding: 0.35rem 0.7rem;
      font-size: 0.75rem;
    }
  }

  @media (max-width: 768px) {
    .hh-topbar .hh-topbar-links {
      gap: 0.5rem;
    }
    .hh-topbar .hh-topbar-link i {
      font-size: 0.9rem;
    }
    .hh-topbar .hh-topbar-link .lbl {
      font-size: 0.65rem;
    }
    .hh-register-btn, .hh-user-menu-btn {
      padding: 0.25rem 0.5rem;
      font-size: 0.65rem;
    }
    .hh-register-btn i, .hh-user-menu-btn i {
      font-size: 0.8rem;
    }
    .hh-topbar .hh-logo {
      font-size: 1rem;
    }
  }

  @media (max-width: 576px) {
    .hh-topbar {
      padding: 0.5rem 0;
    }
    .hh-topbar .hh-topbar-links {
      gap: 0.3rem;
    }
    .hh-topbar .hh-topbar-link .lbl {
      font-size: 0.55rem;
    }
    .hh-register-btn, .hh-user-menu-btn {
      padding: 0.2rem 0.4rem;
      font-size: 0.6rem;
      border-radius: 0.3rem;
    }
    .hh-register-btn i, .hh-user-menu-btn i {
      font-size: 0.7rem;
    }
    .hh-topbar .hh-logo {
      font-size: 0.85rem;
    }
    .hh-city-strip a {
      font-size: 0.75rem;
      margin-right: 1rem;
    }
  }
</style>
<?= $extraHeadHtml ?>
</head>
<body<?= $bodyClass !== '' ? ' class="' . hh_e($bodyClass) . '"' : '' ?>>

<!-- ============ Top bar ============ -->
<div class="hh-topbar">
  <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
    <a href="<?= hh_e($logoHref) ?>" class="hh-logo">
      <span class="hh-brand-icon-ring">
        <img src="<?= hh_e($hhHeaderLogoPath) ?>" alt="<?= hh_e($hhHeaderSettings['site_title']) ?>">
      </span>
    </a>
    <div class="hh-topbar-links">
      <a href="properties.php" class="hh-topbar-link">
        <i class="bi bi-briefcase"></i>
        <span><span class="lbl">For Business</span><span class="sub">Trusted by builders &amp; agents</span></span>
      </a>
      <a href="sell-property.php" class="hh-topbar-link">
        <i class="bi bi-building-add"></i>
        <span><span class="lbl">List your property</span><span class="sub">Start earning in 30 mins</span></span>
      </a>

      <!-- Login/Register/User Menu -->
      <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
        <!-- Logged In - Show User Dropdown -->
        <div class="dropdown hh-user-dropdown">
          <a href="#" class="hh-user-menu-btn dropdown-toggle" id="hhUserMenuBtn" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle"></i>
            <span><?php echo hh_e($_SESSION['user_name'] ?? 'User'); ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="hhUserMenuBtn">
            <li class="dropdown-item-text text-muted small">
              <strong><?php echo hh_e($_SESSION['user_name'] ?? 'User'); ?></strong>
              <br>
              <span style="font-size: 0.7rem; color: var(--muted);"><?php echo hh_e($_SESSION['user_email'] ?? ''); ?></span>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="/front/profile.php"><i class="bi bi-person"></i> Profile</a></li>
            <li><a class="dropdown-item" href="/front/dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            <li><a class="dropdown-item" href="/front/my-properties.php"><i class="bi bi-building"></i> My Properties</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); hhLogoutUser();"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
          </ul>
        </div>
      <?php else: ?>
        <!-- Not Logged In - Single Combined Button (same as includes/header.php) -->
        <a href="#" class="hh-register-btn" data-bs-toggle="modal" data-bs-target="#authModal" data-mode="login">
          <i class="bi bi-person"></i> Sign In / Register
        </a>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- ============ City strip ============ -->
<?php if (!empty($cityStripItems)): ?>
<div class="hh-city-strip">
  <div class="container">
    <?php foreach ($cityStripItems as $item): ?>
      <a href="<?= hh_e($item['url'] ?? '#') ?>" class="<?= !empty($item['active']) ? 'active' : '' ?>"><?= hh_e($item['label'] ?? '') ?></a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- ============ SCRIPTS ============ -->
<script>
// ===== Logout Function =====
function hhLogoutUser() {
  if (confirm('Are you sure you want to logout?')) {
    fetch('/front/api/auth.php?action=logout', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        window.location.reload();
      } else {
        alert('Logout failed.');
      }
    })
    .catch(function() {
      alert('Network error.');
    });
  }
}

// ===== For Business and List Your Property links =====
// If you want these to open modals or go to specific pages, update the hrefs
</script>

<!-- Bootstrap JS for dropdowns and modals -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Include Auth Modal (site-wide, same as includes/header.php) -->
<?php require_once __DIR__ . '/auth-modal.php'; ?>

<!-- Include Change Password Modal (site-wide) — guarded until the file exists -->
<?php if (file_exists(__DIR__ . '/change-password-modal.php')): ?>
    <?php require_once __DIR__ . '/change-password-modal.php'; ?>
<?php endif; ?>

</body>
</html>