<?php
/* =========================================================================
   hotels-homestays.php
   -------------------------------------------------------------------------
   Dynamic version of the "Hotels and home stays" landing page.
   Pulls the city list AND the featured stay cards from the
   `trending_projects` table instead of using hardcoded HTML.

   Header (doctype/head/topbar/city-strip) and footer are now shared
   includes so other pages can reuse the same chrome:
     includes/header.php
     includes/footer.php

   Adjust the three ASSUMPTIONS below if your project differs:
     1. $conn            -> mysqli connection object created in config.php
     2. STATUS_ACTIVE    -> the value in `status` that means "show this"
     3. $imageBase        -> folder where `image` filenames live
   ========================================================================= */

require_once __DIR__ . '/includes/config.php';

// ---- ASSUMPTIONS (edit if your setup differs) --------------------------
const STATUS_ACTIVE = 'active';               // value of `status` col for live listings
$imageBase           = 'assets/images/projects/'; // folder for `image` filenames
$fallbackImage        = 'https://placehold.co/600x400/';
// --------------------------------------------------------------------------

if (!isset($conn) || !($conn instanceof mysqli)) {
    die('Database connection ($conn) not found. Check includes/config.php.');
}

/* -------------------------------------------------------------------------
   1. Distinct list of cities, for the city strip + the search datalist.
   ------------------------------------------------------------------------- */
$cities = [];
$cityStmt = $conn->prepare(
    "SELECT DISTINCT `city`
       FROM `trending_projects`
      WHERE `status` = ?
        AND `city` IS NOT NULL
        AND `city` <> ''
      ORDER BY `city` ASC"
);
$cityStmt->bind_param('s', $activeStatus);
$activeStatus = STATUS_ACTIVE;
$cityStmt->execute();
$cityResult = $cityStmt->get_result();
while ($row = $cityResult->fetch_assoc()) {
    $cities[] = $row['city'];
}
$cityStmt->close();

/* -------------------------------------------------------------------------
   2. Featured stays (top N trending projects) for the card grid.
   ------------------------------------------------------------------------- */
$stays = [];
$staysStmt = $conn->prepare(
    "SELECT `id`, `project_name`, `slug`, `address`, `city`, `price`, `image`
       FROM `trending_projects`
      WHERE `status` = ?
      ORDER BY `project_order` ASC, `created_at` DESC
      LIMIT 8"
);
$staysStmt->bind_param('s', $activeStatus);
$staysStmt->execute();
$staysResult = $staysStmt->get_result();
while ($row = $staysResult->fetch_assoc()) {
    $stays[] = $row;
}
$staysStmt->close();

/* -------------------------------------------------------------------------
   3. Small helpers
   ------------------------------------------------------------------------- */
function hh_e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
function hh_price($value) {
    if ($value === null || $value === '') return 'Price on request';
    return '&#8377;' . number_format((float) $value) . ' <small>/ night</small>';
}
function hh_image_url($filename, $base, $fallback) {
    $filename = trim((string) $filename);
    return $filename !== '' ? $base . $filename : $fallback;
}
function hh_city_link($city) {
    return 'all-hotels-homestays.php?city=' . urlencode($city);
}
// Same pretty-URL pattern used site-wide, e.g. header.php's wishlist card
// links to "/front/property-listing/{slug}/". Falls back to the
// id-based property-details.php URL only when a listing has no slug yet.
function hh_property_url($stay) {
    if (!empty($stay['slug'])) {
        return 'property-listing/' . urlencode($stay['slug']) . '/';
    }
    return 'property-details.php?id=' . (int) $stay['id'];
}

/* -------------------------------------------------------------------------
   4. Header / footer setup (shared includes/header.php + includes/footer.php)
   ------------------------------------------------------------------------- */
$pageTitle = 'Real Estate | Hotels and home stays';
$logoHref  = 'index.php';

$cityStripItems = [];
foreach ($cities as $city) {
    $cityStripItems[] = ['label' => $city, 'url' => hh_city_link($city)];
}
$cityStripItems[] = ['label' => 'All Cities', 'url' => 'all-hotels-homestays.php'];

$footerCityItems = [];
foreach (array_slice($cities, 0, 5) as $city) {
    $footerCityItems[] = ['label' => $city, 'url' => hh_city_link($city)];
}

// Page-specific styles (hero, search panel, promo, stay cards, etc.) -
// not shared with other pages, so they travel with this page rather than
// living in includes/header.php.
ob_start();
?>
<style>
  .hh-page{ background:#fbfaf7; }

  /* ============ Hero ============ */
  .hh-hero{
    position:relative;
    min-height:460px;
    display:flex;
    align-items:center;
    /* NOTE: overflow was previously "hidden" here, which clipped the
       position:absolute Dates / Rooms & Guests dropdowns so they showed
       only their header row. The ::before/::after layers below use
       inset:0, so they never extend past this box anyway -
       overflow:hidden was not needed for them. */
    background:linear-gradient(160deg, var(--navy-deep) 0%, var(--navy) 65%, #1a2c4a 100%);
  }
  .hh-hero::before{
    content:'';
    position:absolute; inset:0;
    background-image:url('assets/images/hostels-homestays-cover.jpg');
    background-size:cover; background-position:center;
    opacity:.35;
    border-radius:inherit;
    z-index:0;
  }
  .hh-hero::after{
    content:'';
    position:absolute; inset:0;
    background:linear-gradient(180deg, rgba(10,16,28,.55) 0%, rgba(10,16,28,.25) 45%, rgba(10,16,28,.75) 100%);
    border-radius:inherit;
    z-index:0;
  }
  .hh-hero-inner{ position:relative; z-index:2; width:100%; padding:3rem 0 2.5rem; }

  .hh-eyebrow{
    display:inline-flex; align-items:center; gap:.4rem;
    background:rgba(255,255,255,.12);
    border:1px solid rgba(255,255,255,.3);
    color:#fff;
    border-radius:2rem;
    padding:.35rem .9rem;
    font-size:.8rem;
    font-weight:700;
    letter-spacing:.02em;
    margin-bottom:1rem;
  }
  .hh-eyebrow i{ color:var(--gold); }

  .hh-title{
    color:#fff;
    font-family:'Fraunces', serif;
    font-weight:600;
    font-size:clamp(1.9rem, 3.6vw, 2.9rem);
    margin-bottom:.6rem;
    max-width:720px;
  }
  .hh-subtitle{
    color:#dfe3ea;
    font-size:1.05rem;
    margin-bottom:2rem;
    max-width:600px;
  }
  .hh-subtitle strong{ color:var(--gold); font-weight:700; }

  /* ---- Search panel ---- */
  .hh-search-panel{
    background:#fff;
    border-radius:1rem;
    box-shadow:0 24px 60px rgba(0,0,0,.28);
    display:flex;
    flex-wrap:wrap;
    align-items:stretch;
    max-width:980px;
    position:relative;
  }
  .hh-field{
    flex:1 1 200px;
    display:flex;
    flex-direction:column;
    justify-content:center;
    gap:.15rem;
    padding:.85rem 1.15rem;
    border-right:1px solid var(--hair);
    min-width:0;
    position:relative;
  }
  .hh-field:last-of-type{ border-right:none; }
  .hh-field label{
    font-size:.7rem;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.04em;
    color:var(--muted);
    margin-bottom:.1rem;
  }
  .hh-field .hh-field-value{
    display:flex; align-items:center; gap:.5rem;
    color:var(--ink); font-weight:600; font-size:.95rem;
    cursor:pointer;
  }
  .hh-field .hh-field-value i{ color:var(--muted); flex-shrink:0; }
  .hh-field input, .hh-field select{
    border:none; outline:none; background:transparent;
    width:100%; font-size:.95rem; font-weight:600; color:var(--ink);
    padding:0;
  }
  .hh-field input::placeholder{ color:#8a93a3; font-weight:500; }

  /* ---- Shared dropdown shell (dates + guests) ---- */
  .hh-dropdown{
    display:none;
    position:absolute; top:calc(100% + .6rem); left:0;
    background:#fff; border:1px solid var(--hair); border-radius:.9rem;
    box-shadow:0 20px 48px rgba(18,33,58,.22);
    z-index:20;
  }
  .hh-dropdown.open{ display:block; }

  /* ---- Date range calendar ---- */
  .hh-cal-dropdown{ padding:1.1rem 1.2rem 1.2rem; width:640px; max-width:90vw; }
  .hh-cal-months{ display:flex; gap:2rem; }
  .hh-cal-month{ flex:1 1 0; min-width:0; }
  .hh-cal-header{ display:flex; align-items:center; justify-content:space-between; margin-bottom:.7rem; }
  .hh-cal-header .hh-cal-title{ font-weight:700; color:var(--ink); font-size:.95rem; }
  .hh-cal-nav{
    width:28px; height:28px; border-radius:50%; border:1px solid var(--hair);
    background:#fff; color:var(--ink); display:flex; align-items:center; justify-content:center;
    flex-shrink:0;
  }
  .hh-cal-nav:hover{ border-color:var(--blue); color:var(--blue); }
  .hh-cal-nav:disabled{ opacity:.3; pointer-events:none; }
  .hh-cal-nav.hh-cal-nav-ghost{ visibility:hidden; }
  .hh-cal-grid{ display:grid; grid-template-columns:repeat(7,1fr); gap:2px; }
  .hh-cal-dow{ text-align:center; font-size:.72rem; font-weight:700; color:var(--muted); padding:.3rem 0; }
  .hh-cal-day{
    position:relative;
    text-align:center;
    font-size:.85rem;
    font-weight:600;
    color:var(--ink);
    padding:.45rem 0;
    border-radius:.4rem;
    cursor:pointer;
    background:transparent;
    border:none;
  }
  .hh-cal-day.is-blank{ visibility:hidden; cursor:default; }
  .hh-cal-day.is-disabled{ color:#c7cdd9; cursor:not-allowed; }
  .hh-cal-day:not(.is-disabled):not(.is-blank):hover{ background:rgba(31,111,235,.1); }
  .hh-cal-day.is-start,
  .hh-cal-day.is-end{
    background:var(--navy); color:#fff;
    border-radius:.4rem;
  }
  .hh-cal-day.is-in-range{ background:rgba(31,111,235,.12); border-radius:0; }
  .hh-cal-day.is-start.is-in-range,
  .hh-cal-day.is-end.is-in-range{ background:var(--navy); }
  .hh-cal-footer{
    display:flex; align-items:center; justify-content:space-between;
    margin-top:1rem; padding-top:.9rem; border-top:1px solid var(--hair);
  }
  .hh-cal-summary{ font-size:.82rem; color:var(--muted); }
  .hh-cal-summary strong{ color:var(--ink); }
  .hh-cal-apply{
    background:var(--gold); color:var(--navy); border:none; font-weight:700;
    font-size:.85rem; padding:.5rem 1.2rem; border-radius:.5rem;
  }
  .hh-cal-apply:hover{ background:var(--gold-dark); }
  @media (max-width: 767.98px){
    .hh-cal-dropdown{ width:320px; }
    .hh-cal-months{ flex-direction:column; gap:1.4rem; }
  }

  /* ---- Rooms & Guests panel ---- */
  .hh-guest-menu{ padding:0; width:300px; }
  .hh-guest-menu-head{
    display:grid; grid-template-columns:1fr auto;
    padding:.8rem 1.1rem .6rem; font-size:.72rem; font-weight:700;
    text-transform:uppercase; letter-spacing:.04em; color:var(--muted);
    border-bottom:1px solid var(--hair);
  }
  .hh-room-block{ padding:.8rem 1.1rem; border-bottom:1px solid var(--hair); }
  .hh-room-block:last-of-type{ border-bottom:none; }
  .hh-room-row{ display:flex; align-items:center; justify-content:space-between; padding:.35rem 0; }
  .hh-room-row span.hh-room-label{ font-size:.88rem; font-weight:700; color:var(--ink); }
  .hh-guest-stepper-ctrl{ display:flex; align-items:center; gap:.6rem; }
  .hh-stepper-btn{
    width:26px; height:26px; border-radius:.4rem; border:1px solid var(--hair);
    background:#fff; color:var(--ink); font-weight:700; line-height:1;
    display:flex; align-items:center; justify-content:center;
  }
  .hh-stepper-btn:hover:not(:disabled){ border-color:var(--blue); color:var(--blue); }
  .hh-stepper-btn:disabled{ opacity:.35; }
  .hh-guest-count{ min-width:14px; text-align:center; font-weight:700; color:var(--ink); font-size:.9rem; }
  .hh-room-actions{
    display:flex; align-items:center; justify-content:space-between;
    padding-top:.3rem; font-size:.78rem; font-weight:700;
  }
  .hh-room-delete{ color:#c94a4a; background:none; border:none; padding:0; }
  .hh-room-delete:disabled{ color:#c7cdd9; cursor:not-allowed; }
  .hh-room-add-wrap{ padding:.75rem 1.1rem; }
  .hh-room-add{
    color:var(--blue); background:none; border:none; padding:0; font-size:.85rem; font-weight:700;
  }
  .hh-guest-done-wrap{ padding:.8rem 1.1rem; border-top:1px solid var(--hair); text-align:right; }
  .hh-guest-done{
    background:var(--gold); color:var(--navy); border:none; font-weight:700;
    font-size:.85rem; padding:.5rem 1.2rem; border-radius:.5rem;
  }
  .hh-guest-done:hover{ background:var(--gold-dark); }

  .hh-search-btn{
    flex:0 0 auto;
    background:var(--gold);
    color:var(--navy);
    border:none;
    font-weight:700;
    font-size:.95rem;
    padding:0 2.2rem;
    border-radius:0 1rem 1rem 0;
  }
  .hh-search-btn:hover{ background:var(--gold-dark); }

  @media (max-width: 767.98px){
    .hh-search-panel{ border-radius:.9rem; }
    .hh-field{ border-right:none; border-bottom:1px solid var(--hair); flex:1 1 100%; }
    .hh-search-btn{ width:100%; border-radius:0 0 .9rem .9rem; padding:.9rem; }
  }

  /* ============ Promo strip ============ */
  .hh-promo{
    position:relative;
    border-radius:1rem;
    overflow:hidden;
    min-height:220px;
    display:flex;
    align-items:center;
    background:linear-gradient(115deg, var(--navy) 0%, #23385c 60%, #2c4a78 100%);
    padding:2.2rem 2.5rem;
    color:#fff;
  }
  .hh-promo h3{
    font-family:'Fraunces', serif;
    font-weight:600;
    font-size:clamp(1.4rem, 2.4vw, 2rem);
    max-width:420px;
    margin-bottom:1.1rem;
  }
  .hh-promo .hh-off{
    position:absolute;
    right:2.5rem; top:50%; transform:translateY(-50%);
    text-align:right;
  }
  .hh-promo .hh-off .num{
    font-family:'Fraunces', serif;
    font-weight:700;
    font-size:3rem;
    color:var(--gold);
    line-height:1;
  }
  .hh-promo .hh-off .cap{ font-size:.85rem; color:#dfe3ea; }
  .hh-btn-light{
    display:inline-block;
    background:#fff;
    color:var(--navy);
    font-weight:700;
    font-size:.9rem;
    padding:.6rem 1.4rem;
    border-radius:2rem;
  }
  .hh-btn-light:hover{ background:var(--gold); color:var(--navy); }

  /* ============ Feature ribbon ============ */
  .hh-feature-ribbon{
    background:var(--navy-deep);
    border-radius:1rem;
    padding:1.6rem 2rem;
    display:flex;
    flex-wrap:wrap;
    gap:1.6rem;
    align-items:center;
    justify-content:space-between;
  }
  .hh-feature-ribbon .hh-tag{
    display:inline-flex; align-items:center; gap:.5rem;
    background:rgba(245,185,66,.12);
    border:1px solid rgba(245,185,66,.4);
    color:var(--gold);
    border-radius:.6rem;
    padding:.4rem .8rem;
    font-weight:700;
    font-size:.85rem;
    flex:0 0 auto;
  }
  .hh-feature-list{ display:flex; flex-wrap:wrap; gap:1.5rem; }
  .hh-feature-item{ display:flex; align-items:center; gap:.5rem; color:#dfe3ea; font-size:.85rem; font-weight:600; }
  .hh-feature-item i{ color:var(--gold); font-size:1rem; }

  /* ============ Stay cards ============ */
  .hh-section-head{ display:flex; align-items:baseline; justify-content:space-between; margin-bottom:1.2rem; flex-wrap:wrap; gap:.5rem; }
  .hh-section-head h2{ font-family:'Fraunces', serif; font-weight:600; color:var(--ink); font-size:1.6rem; margin:0; }
  .hh-section-head a{ color:var(--blue); font-weight:700; font-size:.9rem; text-decoration:none; }

  .hh-stay-card{
    border:1px solid var(--hair);
    border-radius:.9rem;
    overflow:hidden;
    background:#fff;
    height:100%;
    transition:box-shadow .2s ease, transform .2s ease;
  }
  .hh-stay-card:hover{ box-shadow:0 16px 34px rgba(18,33,58,.12); transform:translateY(-2px); }
  .hh-stay-card .hh-stay-img{
    height:170px; background-size:cover; background-position:center; background-color:#eef1f5;
    position:relative;
  }
  .hh-stay-card .hh-stay-badge{
    position:absolute; top:.7rem; left:.7rem;
    background:#fff; color:var(--ink); font-weight:700; font-size:.7rem;
    padding:.25rem .55rem; border-radius:.4rem;
  }
  .hh-stay-card .hh-stay-body{ padding:.9rem 1rem 1.1rem; }
  .hh-stay-card h6{ font-weight:700; color:var(--ink); margin-bottom:.2rem; font-size:.95rem; }
  .hh-stay-card .hh-stay-loc{ color:var(--muted); font-size:.8rem; margin-bottom:.6rem; display:flex; align-items:center; gap:.3rem; }
  .hh-stay-card .hh-stay-price{ font-weight:700; color:var(--ink); font-size:1rem; }
  .hh-stay-card .hh-stay-price small{ font-weight:500; color:var(--muted); font-size:.75rem; }

  .hh-empty-state{
    border:1px dashed var(--hair);
    border-radius:1rem;
    padding:2.5rem 1.5rem;
    text-align:center;
    color:var(--muted);
  }

  /* ============ Coverage / stats ============ */
  .hh-coverage{
    background:#fff;
    border:1px solid var(--hair);
    border-radius:1rem;
    padding:2.2rem;
    display:flex;
    flex-wrap:wrap;
    gap:2rem;
    align-items:center;
    justify-content:space-between;
  }
  .hh-coverage h3{ font-family:'Fraunces', serif; font-weight:600; color:var(--ink); font-size:1.5rem; margin-bottom:.4rem; }
  .hh-coverage p{ color:var(--muted); margin-bottom:0; max-width:420px; }
  .hh-stat-grid{ display:flex; gap:2.2rem; }
  .hh-stat .num{ font-family:'Fraunces', serif; font-weight:700; font-size:2rem; color:var(--ink); line-height:1; }
  .hh-stat .cap{ color:var(--muted); font-size:.85rem; }

  /* ============ Newsletter ============ */
  .hh-newsletter{
    background:#fff;
    border:1px solid var(--hair);
    border-radius:1rem;
    padding:1.6rem 2rem;
    display:flex;
    flex-wrap:wrap;
    align-items:center;
    justify-content:space-between;
    gap:1rem;
  }
  .hh-newsletter form{ display:flex; gap:.6rem; flex-wrap:wrap; }
  .hh-newsletter input[type=email]{
    border:1px solid var(--hair); border-radius:.6rem; padding:.6rem .9rem; min-width:240px; font-size:.9rem;
  }
  .hh-newsletter button{
    background:var(--navy); color:#fff; border:none; border-radius:.6rem;
    padding:.6rem 1.3rem; font-weight:700; font-size:.9rem;
  }
  .hh-newsletter button:hover{ background:var(--navy-deep); }
</style>
<?php
$extraHeadHtml = ob_get_clean();

require __DIR__ . '/includes/header-hotels.php';
?>

<main class="hh-page">

  <!-- ============ Hero + search ============ -->
  <section class="hh-hero">
    <div class="hh-hero-inner">
      <div class="container">
        <span class="hh-eyebrow"><i class="bi bi-patch-check-fill"></i> Verified hostels &amp; home stays</span>
        <h1 class="hh-title">Hotels and home stays, ready whenever you are</h1>
        <p class="hh-subtitle">
          Book verified hostels and home stays across <strong><?= count($cities) ?>+ cities</strong> —
          managed listings, doorstep support, and clear pricing, no surprises.
        </p>

        <form class="hh-search-panel" id="hhSearchForm" action="all-hotels-homestays.php" method="get">

          <div class="hh-field">
            <label for="hhCity">City / Locality</label>
            <div class="hh-field-value">
              <i class="bi bi-geo-alt"></i>
              <input type="text" id="hhCity" name="search" list="hhCityList" placeholder="Search city or locality">
              <datalist id="hhCityList">
                <?php foreach ($cities as $city): ?>
                  <option value="<?= hh_e($city) ?>">
                <?php endforeach; ?>
              </datalist>
            </div>
          </div>

          <!-- ---- Dates field (dropdown two-month range calendar) ---- -->
          <div class="hh-field" id="hhDateField">
            <label>Dates</label>
            <div class="hh-field-value" id="hhDateToggle" role="button">
              <i class="bi bi-calendar-check"></i>
              <span id="hhDateLabel">Add dates</span>
            </div>
            <input type="hidden" name="checkin" id="hhCheckinInput">
            <input type="hidden" name="checkout" id="hhCheckoutInput">

            <div class="hh-dropdown hh-cal-dropdown" id="hhCalDropdown">
              <div class="hh-cal-months">
                <div class="hh-cal-month">
                  <div class="hh-cal-header">
                    <button type="button" class="hh-cal-nav" id="hhCalPrev" aria-label="Previous month"><i class="bi bi-chevron-left"></i></button>
                    <span class="hh-cal-title" id="hhCalTitleLeft"></span>
                    <button type="button" class="hh-cal-nav hh-cal-nav-ghost"><i class="bi bi-chevron-left"></i></button>
                  </div>
                  <div class="hh-cal-grid" id="hhCalGridLeft"></div>
                </div>
                <div class="hh-cal-month">
                  <div class="hh-cal-header">
                    <button type="button" class="hh-cal-nav hh-cal-nav-ghost"><i class="bi bi-chevron-right"></i></button>
                    <span class="hh-cal-title" id="hhCalTitleRight"></span>
                    <button type="button" class="hh-cal-nav" id="hhCalNext" aria-label="Next month"><i class="bi bi-chevron-right"></i></button>
                  </div>
                  <div class="hh-cal-grid" id="hhCalGridRight"></div>
                </div>
              </div>
              <div class="hh-cal-footer">
                <div class="hh-cal-summary" id="hhCalSummary">Select check-in date</div>
                <button type="button" class="hh-cal-apply" id="hhCalApply">Apply</button>
              </div>
            </div>
          </div>

          <!-- ---- Rooms & Guests field (dropdown stepper panel) ---- -->
          <div class="hh-field" id="hhGuestField">
            <label>Rooms &amp; Guests</label>
            <div class="hh-field-value" id="hhGuestToggle" role="button">
              <i class="bi bi-people"></i>
              <span id="hhGuestLabel">1 Room, 1 Guest</span>
            </div>
            <input type="hidden" name="rooms" id="hhRoomsInput" value="1">
            <input type="hidden" name="guests" id="hhGuestInput" value="1">

            <div class="hh-dropdown hh-guest-menu" id="hhGuestMenu">
              <div class="hh-guest-menu-head">
                <span>Rooms</span>
                <span>Guests</span>
              </div>
              <div id="hhRoomList"></div>
              <div class="hh-room-add-wrap">
                <button type="button" class="hh-room-add" id="hhAddRoom"><i class="bi bi-plus-lg"></i> Add Room</button>
              </div>
              <div class="hh-guest-done-wrap">
                <button type="button" class="hh-guest-done" id="hhGuestDone">Done</button>
              </div>
            </div>
          </div>

          <button type="submit" class="hh-search-btn">
            <i class="bi bi-search"></i> Search stays
          </button>
        </form>
      </div>
    </div>
  </section>

  <div class="container" style="padding-top:2.5rem; padding-bottom:3rem;">
    <div class="d-flex flex-column gap-4">

      <!-- ============ Promo strip ============ -->
      <div class="hh-promo">
        <div>
          <h3>Weekend Getaway Sale is live</h3>
          <a href="all-hotels-homestays.php" class="hh-btn-light">Book now</a>
        </div>
        <div class="hh-off">
          <div class="num">40%</div>
          <div class="cap">off select stays</div>
        </div>
      </div>

      <!-- ============ Feature ribbon ============ -->
      <div class="hh-feature-ribbon">
        <span class="hh-tag"><i class="bi bi-patch-check-fill"></i> Verified &amp; managed</span>
        <div class="hh-feature-list">
          <span class="hh-feature-item"><i class="bi bi-shield-check"></i> Verified hosts</span>
          <span class="hh-feature-item"><i class="bi bi-wifi"></i> Free Wi-Fi</span>
          <span class="hh-feature-item"><i class="bi bi-headset"></i> Doorstep support</span>
          <span class="hh-feature-item"><i class="bi bi-cash-coin"></i> No hidden charges</span>
          <span class="hh-feature-item"><i class="bi bi-arrow-repeat"></i> Flexible stay dates</span>
        </div>
      </div>

      <!-- ============ Featured stays (dynamic, from trending_projects) ============ -->
      <div>
        <div class="hh-section-head">
          <h2>Featured hostels &amp; home stays</h2>
          <a href="all-hotels-homestays.php">View all <i class="bi bi-arrow-right"></i></a>
        </div>

        <?php if (!empty($stays)): ?>
          <div class="row g-3">
            <?php foreach ($stays as $stay): ?>
              <?php
                $detailUrl = hh_property_url($stay);
                $imgUrl = hh_image_url($stay['image'], $imageBase, $fallbackImage);
              ?>
              <div class="col-6 col-md-4 col-lg-3">
                <a href="<?= hh_e($detailUrl) ?>" class="text-decoration-none">
                  <div class="hh-stay-card">
                    <div class="hh-stay-img" data-img="<?= hh_e($imgUrl) ?>" data-fallback="<?= hh_e($fallbackImage) ?>" style="background-image:url('<?= hh_e($imgUrl) ?>');">
                      <span class="hh-stay-badge"><i class="bi bi-patch-check-fill text-primary"></i> Verified</span>
                    </div>
                    <div class="hh-stay-body">
                      <h6><?= hh_e($stay['project_name']) ?></h6>
                      <div class="hh-stay-loc">
                        <i class="bi bi-geo-alt"></i>
                        <?= hh_e(trim(($stay['address'] ? $stay['address'] . ', ' : '') . $stay['city'])) ?>
                      </div>
                      <div class="hh-stay-price"><?= hh_price($stay['price']) ?></div>
                    </div>
                  </div>
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="hh-empty-state">
            <i class="bi bi-house-door" style="font-size:1.6rem;"></i>
            <p class="mb-0 mt-2">No stays available right now. Check back soon.</p>
          </div>
        <?php endif; ?>
      </div>

      <!-- ============ Seasonal offer ============ -->
      <div class="hh-promo" style="background:linear-gradient(115deg, #16324f 0%, #1f6feb 120%);">
        <div>
          <h3>Stays starting at &#8377;999</h3>
          <a href="all-hotels-homestays.php" class="hh-btn-light">Explore stays</a>
        </div>
        <div class="hh-off">
          <div class="num">&#8377;999</div>
          <div class="cap">per night, select cities</div>
        </div>
      </div>

      <!-- ============ Coverage / stats ============ -->
      <div class="hh-coverage">
        <div>
          <h3>There's a stay near you</h3>
          <p>More cities, more choices, more ease of booking.</p>
        </div>
        <div class="hh-stat-grid">
          <div class="hh-stat">
            <div class="num"><?= count($cities) ?>+</div>
            <div class="cap">Cities covered</div>
          </div>
          <div class="hh-stat">
            <div class="num"><?= count($stays) ?>+</div>
            <div class="cap">Verified stays</div>
          </div>
        </div>
      </div>

      <!-- ============ Newsletter ============ -->
      <div class="hh-newsletter">
        <div>
          <strong style="color:var(--ink);">Get access to exclusive stay deals</strong>
          <div style="color:var(--muted); font-size:.85rem;">Only the best deals reach your inbox.</div>
        </div>
        <form onsubmit="event.preventDefault(); alert('Thanks — we will keep you posted!');">
          <input type="email" placeholder="e.g., john@email.com" required>
          <button type="submit">Notify me</button>
        </form>
      </div>

    </div>
  </div>

</main>

<script>
(function () {

  /* =========================================================
     Featured stay images — swap to the placehold.co fallback
     if the actual image file 404s / fails to load (a missing
     filename in the DB is already handled server-side; this
     covers a filename that exists in the DB but not on disk).
  ========================================================= */
  document.querySelectorAll('.hh-stay-img[data-img]').forEach(function (el) {
    var src = el.getAttribute('data-img');
    var fallback = el.getAttribute('data-fallback');
    if (!src || !fallback || src === fallback) return;
    var probe = new Image();
    probe.onerror = function () {
      el.style.backgroundImage = "url('" + fallback + "')";
    };
    probe.src = src;
  });

  /* =========================================================
     Rooms & Guests dropdown
  ========================================================= */
  const guestField   = document.getElementById('hhGuestField');
  const guestToggle  = document.getElementById('hhGuestToggle');
  const guestMenu    = document.getElementById('hhGuestMenu');
  const guestLabel   = document.getElementById('hhGuestLabel');
  const roomsInput   = document.getElementById('hhRoomsInput');
  const guestsInput  = document.getElementById('hhGuestInput');
  const roomListEl   = document.getElementById('hhRoomList');
  const addRoomBtn   = document.getElementById('hhAddRoom');
  const guestDoneBtn = document.getElementById('hhGuestDone');

  // FIX: the room/guest steppers and the delete-room button live inside
  // roomListEl, which gets its innerHTML fully rebuilt on every click
  // (renderRooms()). That destroys the exact element that was just
  // clicked. The "click outside closes the menu" listener below (on
  // document) then sees a *detached* e.target and guestField.contains()
  // wrongly returns false, closing the panel right after every click.
  // Stopping propagation here means those clicks never reach the
  // document listener in the first place.
  guestMenu.addEventListener('click', function (e) {
    e.stopPropagation();
  });

  const MAX_ROOMS = 6;
  let rooms = [{ guests: 1 }]; // each room only tracks a guest count, matching the reference UI

  function renderRooms() {
    roomListEl.innerHTML = '';
    rooms.forEach(function (room, idx) {
      const block = document.createElement('div');
      block.className = 'hh-room-block';
      block.innerHTML =
        '<div class="hh-room-row">' +
          '<span class="hh-room-label">Room ' + (idx + 1) + '</span>' +
          '<div class="hh-guest-stepper-ctrl">' +
            '<button type="button" class="hh-stepper-btn" data-idx="' + idx + '" data-step="-1"' + (room.guests <= 1 ? ' disabled' : '') + '>−</button>' +
            '<span class="hh-guest-count">' + room.guests + '</span>' +
            '<button type="button" class="hh-stepper-btn" data-idx="' + idx + '" data-step="1"' + (room.guests >= 8 ? ' disabled' : '') + '>+</button>' +
          '</div>' +
        '</div>' +
        '<div class="hh-room-actions">' +
          '<button type="button" class="hh-room-delete" data-idx="' + idx + '"' + (rooms.length <= 1 ? ' disabled' : '') + '>Delete Room</button>' +
          '<span></span>' +
        '</div>';
      roomListEl.appendChild(block);
    });

    roomListEl.querySelectorAll('.hh-stepper-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        const idx = parseInt(this.dataset.idx, 10);
        const step = parseInt(this.dataset.step, 10);
        rooms[idx].guests = Math.min(8, Math.max(1, rooms[idx].guests + step));
        renderRooms();
      });
    });

    roomListEl.querySelectorAll('.hh-room-delete').forEach(function (btn) {
      btn.addEventListener('click', function () {
        const idx = parseInt(this.dataset.idx, 10);
        if (rooms.length > 1) {
          rooms.splice(idx, 1);
          renderRooms();
        }
      });
    });

    addRoomBtn.disabled = rooms.length >= MAX_ROOMS;
    syncGuestSummary();
  }

  function syncGuestSummary() {
    const totalGuests = rooms.reduce(function (sum, r) { return sum + r.guests; }, 0);
    roomsInput.value = rooms.length;
    guestsInput.value = totalGuests;
    guestLabel.textContent =
      rooms.length + (rooms.length === 1 ? ' Room, ' : ' Rooms, ') +
      totalGuests + (totalGuests === 1 ? ' Guest' : ' Guests');
  }

  addRoomBtn.addEventListener('click', function () {
    if (rooms.length < MAX_ROOMS) {
      rooms.push({ guests: 1 });
      renderRooms();
    }
  });

  guestToggle.addEventListener('click', function (e) {
    e.stopPropagation();
    closeCalendar();
    guestMenu.classList.toggle('open');
  });

  guestDoneBtn.addEventListener('click', function () {
    guestMenu.classList.remove('open');
  });

  function closeGuestMenu() { guestMenu.classList.remove('open'); }

  document.addEventListener('click', function (e) {
    if (!guestField.contains(e.target)) closeGuestMenu();
  });

  renderRooms();

  /* =========================================================
     Dates dropdown — two-month range calendar
  ========================================================= */
  const dateField    = document.getElementById('hhDateField');
  const dateToggle    = document.getElementById('hhDateToggle');
  const dateLabel      = document.getElementById('hhDateLabel');
  const calDropdown  = document.getElementById('hhCalDropdown');
  const gridLeft        = document.getElementById('hhCalGridLeft');
  const gridRight        = document.getElementById('hhCalGridRight');
  const titleLeft         = document.getElementById('hhCalTitleLeft');
  const titleRight         = document.getElementById('hhCalTitleRight');
  const calPrev          = document.getElementById('hhCalPrev');
  const calNext          = document.getElementById('hhCalNext');
  const calSummary   = document.getElementById('hhCalSummary');
  const calApply       = document.getElementById('hhCalApply');
  const checkinInput  = document.getElementById('hhCheckinInput');
  const checkoutInput = document.getElementById('hhCheckoutInput');

  // FIX: same detached-node problem as the guest menu above — every day
  // click rebuilds the whole calendar grid (buildMonthGrid does
  // container.innerHTML = ''), which destroys the button that was just
  // clicked. Without this, the "click outside" listener on document
  // would immediately see a detached target and close the calendar
  // right after your check-in click, before you could pick a check-out
  // date — which is why the Dates field never appeared to update.
  calDropdown.addEventListener('click', function (e) {
    e.stopPropagation();
  });

  const DOW = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
  const MONTH_NAMES = ['January','February','March','April','May','June','July','August','September','October','November','December'];

  const today = new Date();
  today.setHours(0, 0, 0, 0);

  // The left panel always shows "viewYear/viewMonth", right panel shows the month after it.
  let viewYear = today.getFullYear();
  let viewMonth = today.getMonth();

  let startDate = null; // Date or null
  let endDate = null;   // Date or null

  function fmtISO(d) {
    return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
  }
  function fmtNice(d) {
    return d.toLocaleDateString('en-US', { weekday: 'short', day: '2-digit', month: 'short' });
  }
  function sameDay(a, b) {
    return a && b && a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate();
  }

  function buildMonthGrid(container, titleEl, year, month) {
    titleEl.textContent = MONTH_NAMES[month] + ' ' + year;
    container.innerHTML = '';

    DOW.forEach(function (d) {
      const el = document.createElement('div');
      el.className = 'hh-cal-dow';
      el.textContent = d;
      container.appendChild(el);
    });

    const firstOfMonth = new Date(year, month, 1);
    // Convert Sunday(0)..Saturday(6) to Monday-first index 0..6
    let leadingBlanks = firstOfMonth.getDay() - 1;
    if (leadingBlanks < 0) leadingBlanks = 6;

    for (let i = 0; i < leadingBlanks; i++) {
      const blank = document.createElement('div');
      blank.className = 'hh-cal-day is-blank';
      container.appendChild(blank);
    }

    const daysInMonth = new Date(year, month + 1, 0).getDate();
    for (let day = 1; day <= daysInMonth; day++) {
      const cellDate = new Date(year, month, day);
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'hh-cal-day';
      btn.textContent = day;

      const isPast = cellDate < today;
      if (isPast) btn.classList.add('is-disabled');

      if (sameDay(cellDate, startDate)) btn.classList.add('is-start');
      if (sameDay(cellDate, endDate)) btn.classList.add('is-end');
      if (startDate && endDate && cellDate > startDate && cellDate < endDate) btn.classList.add('is-in-range');

      if (!isPast) {
        btn.addEventListener('click', function () { handleDayClick(cellDate); });
      }
      container.appendChild(btn);
    }
  }

  function renderCalendar() {
    buildMonthGrid(gridLeft, titleLeft, viewYear, viewMonth);
    let nextMonth = viewMonth + 1, nextYear = viewYear;
    if (nextMonth > 11) { nextMonth = 0; nextYear++; }
    buildMonthGrid(gridRight, titleRight, nextYear, nextMonth);

    calPrev.disabled = (viewYear === today.getFullYear() && viewMonth === today.getMonth());

    if (startDate && endDate) {
      calSummary.innerHTML = '<strong>' + fmtNice(startDate) + '</strong> &nbsp;→&nbsp; <strong>' + fmtNice(endDate) + '</strong>';
    } else if (startDate) {
      calSummary.innerHTML = '<strong>' + fmtNice(startDate) + '</strong> — select check-out date';
    } else {
      calSummary.textContent = 'Select check-in date';
    }
  }

  // Keeps the visible "Dates" field + hidden checkin/checkout inputs in
  // sync with whatever is currently selected in the calendar, WITHOUT
  // requiring the user to click "Apply" first. This is what was missing
  // before: previously the field only updated inside applyDates(), so if
  // someone picked dates and went straight to "Search stays", nothing
  // was ever written to the field or submitted with the form.
  function syncDateField() {
    checkinInput.value  = startDate ? fmtISO(startDate) : '';
    checkoutInput.value = endDate ? fmtISO(endDate) : '';

    if (startDate && endDate) {
      dateLabel.textContent = fmtNice(startDate) + ' – ' + fmtNice(endDate);
    } else if (startDate) {
      dateLabel.textContent = fmtNice(startDate) + ' – Add check-out';
    } else {
      dateLabel.textContent = 'Add dates';
    }
  }

  function handleDayClick(cellDate) {
    if (!startDate || (startDate && endDate)) {
      // start a fresh selection
      startDate = cellDate;
      endDate = null;
    } else if (cellDate < startDate) {
      // picked an earlier date -> restart range from here
      startDate = cellDate;
      endDate = null;
    } else if (sameDay(cellDate, startDate)) {
      // clicking the same day again does nothing special
    } else {
      endDate = cellDate;
    }
    renderCalendar();
    syncDateField();
  }

  function applyDates() {
    syncDateField();
    closeCalendar();
  }

  calApply.addEventListener('click', applyDates);

  calPrev.addEventListener('click', function () {
    viewMonth--;
    if (viewMonth < 0) { viewMonth = 11; viewYear--; }
    renderCalendar();
  });
  calNext.addEventListener('click', function () {
    viewMonth++;
    if (viewMonth > 11) { viewMonth = 0; viewYear++; }
    renderCalendar();
  });

  function openCalendar() {
    closeGuestMenu();
    renderCalendar();
    calDropdown.classList.add('open');
  }
  function closeCalendar() { calDropdown.classList.remove('open'); }

  dateToggle.addEventListener('click', function (e) {
    e.stopPropagation();
    if (calDropdown.classList.contains('open')) {
      closeCalendar();
    } else {
      openCalendar();
    }
  });

  document.addEventListener('click', function (e) {
    if (!dateField.contains(e.target)) closeCalendar();
  });

})();
</script>

<?php
// $logoHref and $footerCityItems were set earlier, before includes/header.php.
require __DIR__ . '/includes/footer-hotels.php';
?>