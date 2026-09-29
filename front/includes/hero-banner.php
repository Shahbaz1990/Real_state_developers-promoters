<?php
// Fetch ALL active banners, in order — this now powers a real slider
$banners = [];

$sql = "SELECT title, subtitle, image FROM banners
        WHERE status='Active'
        ORDER BY banner_order ASC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $banners[] = $row;
    }
} else {
    // Default single slide if no banners exist yet
    $banners[] = [
        'title' => 'India\'s Fastest-Growing Real Estate Platform',
        'subtitle' => 'Find your dream property today',
        'image' => 'assets/images/home-desktop-cover.jpg'
    ];
}

// First slide, rendered server-side so there's no flash-of-empty-hero before JS runs
$banner = $banners[0];

// Tabs that carry a 'type' key show real sub-categories (pulled from
// property_categories) in the row below when clicked, instead of opening
// a new tab. Tabs with 'url'+'newTab' (Agents) have no
// category tree, so they keep the old instant-open-new-tab behavior.
$tabs = [
    ['label' => 'Buy',                'icon' => 'bi-house-door',       'type' => 'buy'],
    ['label' => 'Rent',                'icon' => 'bi-tag',              'type' => 'rent'],
    ['label' => 'Interiors',           'icon' => 'bi-palette2',         'type' => 'interior'],
    ['label' => 'Vendors',             'icon' => 'bi-shop',             'type' => 'vendor'],
    ['label' => 'Local Services',      'icon' => 'bi-tools',            'type' => 'local_services'],
    ['label' => 'Hotel & Homestays', 'icon' => 'bi-building-fill',    'type' => 'hotels_homestays'],
    ['label' => 'PG & Hostels',      'icon' => 'bi-house-door-fill',  'type' => 'pg_hostels'],
    ['label' => 'Agents',              'icon' => 'bi-person-badge', 'url' => 'all-hotels-homestays.php?type=' . urlencode('Agents'), 'newTab' => true],
];
$activeTabIndex = 0; // "Buy" active by default

// Fetch each vertical's direct children (its "1.1, 1.2, 1.3..." sections)
// for the dynamic sub-tab row. Guarded with function_exists since header.php
// (included on the same page) may already define a similarly-purposed helper.
if (!function_exists('get_hero_vertical_sections')) {
    function get_hero_vertical_sections($conn, $categoryType) {
        $sections = [];
        $safeType = $conn->real_escape_string($categoryType);
        $anchorRes = $conn->query("SELECT id FROM property_categories WHERE status='Active' AND parent_id=0 AND category_type='$safeType' LIMIT 1");
        if ($anchorRes && $anchorRow = $anchorRes->fetch_assoc()) {
            $secRes = $conn->query("SELECT name, slug FROM property_categories WHERE status='Active' AND parent_id=" . (int)$anchorRow['id'] . " ORDER BY category_order ASC, id ASC");
            if ($secRes) {
                while ($row = $secRes->fetch_assoc()) {
                    $sections[] = ['name' => $row['name'], 'slug' => $row['slug']];
                }
            }
        }
        return $sections;
    }
}

$heroTabSections = [];
foreach ($tabs as $tab) {
    if (!empty($tab['type'])) {
        $heroTabSections[$tab['type']] = get_hero_vertical_sections($conn, $tab['type']);
    }
}

// Cities for the search-bar city selector (reused from the cities table elsewhere on the site)
$cities = [];
$cityRes = $conn->query("SELECT city_name FROM cities WHERE status='Active' ORDER BY city_order ASC, city_name ASC");
if ($cityRes) {
    while ($c = $cityRes->fetch_assoc()) {
        $cities[] = $c['city_name'];
    }
}
// Add "All India" as the first option
$citiesWithAll = array_merge(['All India'], $cities);
$defaultCity = 'All India';

$imagePath = "../admin/uploads/banners/" . $banner['image'];

// Full slide data for the JS slider (image path resolved for each banner)
$slidesForJs = array_map(function ($b) {
    return [
        'title'    => $b['title'],
        'subtitle' => $b['subtitle'],
        'image'    => "../admin/uploads/banners/" . $b['image'],
    ];
}, $banners);

// AI Suggestions for quick actions
$aiSuggestions = [
    "Find 2BHK apartments in Mumbai under ₹1 Cr",
    "Best investment properties in Bangalore",
    "Commercial spaces for rent in Delhi",
    "New launch projects in Pune 2026",
    "Independent houses with garden in Chennai"
];
?>
<style>
  /* ============================================
     DARK MODE SUPPORT - Hero Banner
     ============================================ */
  body.dark-mode .hero-tabs-bar-wrap {
    background: rgba(13, 17, 23, 0.95);
  }
  
  body.dark-mode .hero-tabs-bar .tab-btn {
    color: var(--dm-text-secondary, #8b949e);
  }
  
  body.dark-mode .hero-tabs-bar .tab-btn:hover {
    color: var(--sy-gold);
  }
  
  body.dark-mode .hero-tabs-bar .tab-btn.active {
    background: rgba(255,255,255,0.05);
    color: var(--sy-gold);
  }
  
  body.dark-mode .hero-tabs-bar .tab-btn.active::after {
    background: #c9a84c;
  }
  
  body.dark-mode .hero-tabs-bar-sub {
    background: rgba(13, 17, 23, 0.85);
    border-color: var(--dm-border, #30363d);
  }

  body.dark-mode .hero-tabs-bar-sub-wrap {
    background: rgba(13, 17, 23, 0.85);
  }

  body.dark-mode .hero-tabs-fade.sub.left {
    background: linear-gradient(90deg, rgba(13,17,23,0.95), rgba(13,17,23,0));
  }

  body.dark-mode .hero-tabs-fade.sub.right {
    background: linear-gradient(270deg, rgba(13,17,23,0.95), rgba(13,17,23,0));
  }

  body.dark-mode .hero-tabs-scroll-btn.sub {
    color: var(--dm-text-primary, #f0f6fc);
  }

  body.dark-mode .hero-tabs-scroll-btn.sub i {
    background: rgba(255,255,255,0.1);
  }

  body.dark-mode .hero-tabs-scroll-btn.sub:hover i {
    background: rgba(255,255,255,0.2);
  }
  
  body.dark-mode .hero-tabs-bar-sub .tab-btn-sub {
    background: rgba(255,255,255,0.05);
    color: var(--dm-text-secondary, #8b949e);
  }
  
  body.dark-mode .hero-tabs-bar-sub .tab-btn-sub:hover {
    background: rgba(255,255,255,0.1);
    color: var(--dm-text-primary, #f0f6fc);
  }
  
  body.dark-mode .hero-tabs-bar-sub .tab-btn-sub.active {
    background: #c9a84c;
    color: #fff;
  }
  
  body.dark-mode .hero-search-row {
    background: var(--dm-bg-secondary, #161b22);
  }
  
  body.dark-mode .hero-city-select {
    color: var(--dm-text-primary, #f0f6fc);
    border-color: var(--dm-border, #30363d);
  }
  
  body.dark-mode .hero-city-select:hover {
    color: #c9a84c;
  }

  /* City / Pincode toggle buttons in the search row */
  body.dark-mode .hero-citypin-wrap {
    border-color: var(--dm-border, #30363d);
  }

  body.dark-mode .hero-citypin-btn {
    color: var(--dm-text-secondary, #8b949e);
  }

  body.dark-mode .hero-citypin-btn:hover {
    color: #c9a84c;
  }

  body.dark-mode .hero-citypin-btn.active {
    color: #c9a84c;
  }
  
  body.dark-mode .hero-search-input i {
    color: var(--dm-text-secondary, #8b949e);
  }
  
  body.dark-mode .hero-search-input input {
    color: var(--dm-text-primary, #f0f6fc);
  }
  
  body.dark-mode .hero-search-input input::placeholder {
    color: var(--dm-text-secondary, #8b949e);
  }
  
  body.dark-mode .hero-ask-ai-btn {
    background: var(--dm-bg-secondary, #161b22);
    color: var(--dm-text-primary, #f0f6fc);
    border: 1.5px solid transparent;
    background-image: linear-gradient(var(--dm-bg-secondary, #161b22), var(--dm-bg-secondary, #161b22)), linear-gradient(90deg, #c9a84c, #c9a84c, #c9a84c);
    background-origin: border-box;
    background-clip: padding-box, border-box;
  }
  
  body.dark-mode .hero-ask-ai-btn i {
    color: #c9a84c;
  }
  
  body.dark-mode .hero-city-menu {
    background: var(--dm-bg-secondary, #161b22);
    border-color: var(--dm-border, #30363d);
  }
  
  body.dark-mode .hero-city-mode-btn {
    background: var(--dm-bg-tertiary, #1c2333);
    border-color: var(--dm-border, #30363d);
    color: var(--dm-text-secondary, #8b949e);
  }
  
  body.dark-mode .hero-city-mode-btn.active {
    background: #c9a84c;
    border-color: #c9a84c;
    color: #fff;
  }
  
  body.dark-mode .hero-city-list .dropdown-item {
    color: var(--dm-text-secondary, #8b949e);
  }
  
  body.dark-mode .hero-city-list .dropdown-item:hover {
    background: var(--dm-bg-tertiary, #1c2333);
    color: var(--dm-text-primary, #f0f6fc);
  }
  
  body.dark-mode .hero-city-pincode-input {
    background: var(--dm-bg-tertiary, #1c2333);
  }
  
  body.dark-mode .hero-city-pincode-input input {
    color: var(--dm-text-primary, #f0f6fc);
    background: transparent;
  }
  
  body.dark-mode .hero-city-pincode-input input::placeholder {
    color: var(--dm-text-secondary, #8b949e);
  }
  
  body.dark-mode .hero-city-pincode-input i {
    color: var(--dm-text-secondary, #8b949e);
  }
  
  body.dark-mode .hero-tabs-fade.left {
    background: linear-gradient(90deg, rgba(13,17,23,0.95), rgba(13,17,23,0));
  }
  
  body.dark-mode .hero-tabs-fade.right {
    background: linear-gradient(270deg, rgba(13,17,23,0.95), rgba(13,17,23,0));
  }
  
  body.dark-mode .hero-tabs-scroll-btn i {
    background: rgba(255,255,255,0.1);
  }
  
  body.dark-mode .hero-tabs-scroll-btn:hover i {
    background: rgba(255,255,255,0.2);
  }
  
  body.dark-mode .hero-dots button {
    background: rgba(255,255,255,0.3);
  }
  
  body.dark-mode .hero-dots button.active {
    background: #c9a84c;
  }
  
  body.dark-mode .hero-arrow {
    background: rgba(0,0,0,0.5);
    color: var(--dm-text-primary, #f0f6fc);
  }
  
  body.dark-mode .hero-arrow:hover {
    background: rgba(0,0,0,0.7);
  }
  
  body.dark-mode .rating-badge {
    background: rgba(0,0,0,0.4);
    border-color: var(--dm-border, #30363d);
    color: var(--dm-text-primary, #f0f6fc);
  }
  
  body.dark-mode .rating-badge i {
    color: #c9a84c;
  }
  
  /* Dark mode for enhanced city search */
  body.dark-mode .hero-city-search-input {
    background: rgba(255,255,255,0.05);
  }

  body.dark-mode .hero-city-search-input input {
    color: var(--dm-text-primary, #f0f6fc);
  }

  body.dark-mode .hero-city-search-input input::placeholder {
    color: var(--dm-text-secondary, #8b949e);
  }

  body.dark-mode .hero-city-list .dropdown-item .city-state {
    color: var(--dm-text-secondary, #8b949e);
  }

  body.dark-mode .hero-city-no-results {
    color: var(--dm-text-secondary, #8b949e);
  }

  body.dark-mode .hero-quick-cities {
    border-color: var(--dm-border, #30363d);
  }

  body.dark-mode .quick-city-btn {
    background: var(--dm-bg-tertiary, #1c2333);
    border-color: var(--dm-border, #30363d);
    color: var(--dm-text-secondary, #8b949e);
  }

  body.dark-mode .quick-city-btn:hover {
    background: rgba(201, 168, 76, 0.2);
    border-color: #c9a84c;
    color: #c9a84c;
  }

  /* ============================================
     ORIGINAL STYLES
     ============================================ */
  .hero-banner {
  position: relative;
  padding: 0.5rem 0 0.5rem;
  overflow: visible !important; /* Prevents clipping of the dropdown */
  min-height: 500px;
  z-index: 100; /* Elevates the hero section above the section below it */
}

.hero-banner .hero-inner { 
  position: relative; 
  z-index: 101; 
}
  .hero-banner .hero-bg{
    position:absolute;
    inset:0;
    background-size:cover;
    background-position:center;
    transition:opacity .6s ease;
    opacity:1;
    min-height:500px;
  }
  .hero-banner .hero-bg.fading{ opacity:0; }
  .hero-banner .overlay{
    position:absolute;
    inset:0;
    background:linear-gradient(180deg, rgba(10,16,28,.55) 0%, rgba(10,16,28,.35) 55%, rgba(10,16,28,.65) 100%);
  }
  .hero-banner .hero-inner{ position:relative; z-index:2; }

  .hero-arrow{
    position:absolute;
    top:45%;
    transform:translateY(-50%);
    width:44px; height:44px;
    border-radius:50%;
    border:none;
    background:rgba(255,255,255,.2);
    color:#fff;
    display:flex; align-items:center; justify-content:center;
    z-index:4;
    backdrop-filter:blur(4px);
    transition: all 0.3s ease;
  }
  .hero-arrow:hover{ background:rgba(255,255,255,.35); }
  .hero-arrow.prev{ left:1.25rem; }
  .hero-arrow.next{ right:1.25rem; }

  .hero-dots{
    position:absolute;
    bottom:1.25rem;
    left:50%;
    transform:translateX(-50%);
    z-index:4;
    display:flex;
    gap:.5rem;
  }
  .hero-dots button{
    width:8px; height:8px;
    border-radius:50%;
    border:none;
    background:rgba(255,255,255,.45);
    padding:0;
    transition: all 0.3s ease;
  }
  .hero-dots button.active{ background:#c9a84c; width:22px; border-radius:4px; }

  .rating-badge{
    display:inline-flex; align-items:center; gap:.4rem;
    background:rgba(255,255,255,.15);
    border:1px solid rgba(255,255,255,.35);
    color:#fff;
    border-radius:2rem;
    padding:.4rem 1rem;
    font-size:.85rem;
    backdrop-filter:blur(4px);
    transition: all 0.3s ease;
  }
  .rating-badge i{ color:#c9a84c; }

  .hero-title{
    color:#fff;
    font-family:'Fraunces', serif;
    font-weight:600;
    font-size:clamp(1.8rem, 3.2vw, 2.6rem);
    margin-bottom:.75rem;
    transition: color 0.3s ease;
  }
  .hero-subtitle{
    color:#e7e9ee;
    font-size:1.05rem;
    margin-bottom:0;
    transition: color 0.3s ease;
  }
  .hero-subtitle .highlight{ color:#c9a84c; font-weight:600; }
  .hero-subtitle .sep{
    display:inline-block; width:5px; height:5px; border-radius:50%;
    background:#e7e9ee; vertical-align:middle;
  }

  /* ============ Search panel — centered on the banner image ============ */
  .hero-panel-wrap{
    position:relative;
    z-index:3;
    margin-top:2rem;
    text-align:left;
  }

  /* Dark tab bar wrapper — positions the scroll arrows + fade edges */
  .hero-tabs-bar-wrap{
    position:relative;
    background:rgba(8,12,20,.9);
    border-radius:1rem 1rem 0 0;
    transition: background 0.3s ease;
  }

  /* Dark tab bar */
  .hero-tabs-bar{
    display:flex;
    overflow-x:auto;
    scroll-behavior:smooth;
    gap:.15rem;
    background:transparent;
    border-radius:1rem 1rem 0 0;
    padding:.4rem .5rem 0;
    backdrop-filter:blur(6px);
  }
  .hero-tabs-bar::-webkit-scrollbar{ display:none; }
  .hero-tabs-bar{ scrollbar-width:none; }
  .hero-tabs-bar .tab-btn{
    position:relative;
    display:flex;
    align-items:center;
    gap:.5rem;
    white-space:nowrap;
    background:transparent;
    border:none;
    color:rgba(255,255,255,.7);
    font-size:.80rem;
    font-weight:600;
    padding:.85rem 1.1rem;
    border-radius:.7rem .7rem 0 0;
    transition: all 0.3s ease;
  }
  .hero-tabs-bar .tab-btn:hover{ color:var(--sy-gold); }
  .hero-tabs-bar .tab-btn.active{
    background:rgba(255,255,255,.08);
    color:var(--sy-gold);
  }
  .hero-tabs-bar .tab-btn.active::after{
    content:'';
    position:absolute;
    left:1.1rem; right:1.1rem; bottom:0;
    height:2px;
    background:#c9a84c;
    transition: background 0.3s ease;
  }
  .hero-tabs-bar .tab-btn .tab-badge{
    position:absolute;
    top:2px; right:2px;
    background:#c9a84c;
    color:#12213a;
    font-size:.6rem;
    font-weight:800;
    letter-spacing:.02em;
    padding:.1rem .3rem;
    border-radius:.3rem;
    line-height:1.3;
  }

  /* Edge fade to hint there's more content to scroll */
  .hero-tabs-fade{
    position:absolute;
    top:0; bottom:0;
    width:36px;
    pointer-events:none;
    z-index:2;
    opacity:0;
    transition:opacity .2s ease;
    border-radius:1rem 1rem 0 0;
  }
  .hero-tabs-fade.left{ left:0; background:linear-gradient(90deg, rgba(8,12,20,.95), rgba(8,12,20,0)); }
  .hero-tabs-fade.right{ right:0; background:linear-gradient(270deg, rgba(8,12,20,.95), rgba(8,12,20,0)); }
  .hero-tabs-fade.show{ opacity:1; }

  /* Scroll arrow buttons, sit on top of the tab bar */
  .hero-tabs-scroll-btn{
    position:absolute;
    top:0; bottom:0;
    width:28px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:transparent;
    border:none;
    color:#fff;
    z-index:3;
    opacity:0;
    pointer-events:none;
    transition:opacity .2s ease;
    font-size:.8rem;
  }
  .hero-tabs-scroll-btn.show{ opacity:1; pointer-events:auto; }
  .hero-tabs-scroll-btn.left{ left:0; justify-content:flex-start; padding-left:.15rem; }
  .hero-tabs-scroll-btn.right{ right:0; justify-content:flex-end; padding-right:.15rem; }
  .hero-tabs-scroll-btn i{
    background:rgba(255,255,255,.15);
    border-radius:50%;
    width:22px; height:22px;
    display:flex; align-items:center; justify-content:center;
    transition: background 0.3s ease;
  }
  .hero-tabs-scroll-btn:hover i{ background:rgba(255,255,255,.3); }

  /* Second tab row — property-type shortcuts, own row below the main tabs */
  .hero-tabs-bar-sub{
    display:flex;
    align-items:center;
    overflow-x:auto;
    gap:.5rem;
    background:#fff;
    border-top:1px solid rgba(0,0,0,.08);
    padding:.55rem .5rem;
    backdrop-filter:blur(6px);
    transition: background 0.3s ease, border-color 0.3s ease;
  }
  .hero-tabs-bar-sub::-webkit-scrollbar{ display:none; }
  .hero-tabs-bar-sub .tab-btn-sub{
    display:flex;
    align-items:center;
    gap:.4rem;
    white-space:nowrap;
    background:rgba(0,0,0,.06);
    border:none;
    color:rgba(0,0,0,.7);
    font-size:.8rem;
    font-weight:600;
    padding:.45rem .95rem;
    border-radius:1.2rem;
    transition: all 0.3s ease;
  }
  .hero-tabs-bar-sub .tab-btn-sub:hover{ 
    background:rgba(201, 168, 76, 0.15); 
    color:#12213a; 
  }
  .hero-tabs-bar-sub .tab-btn-sub.active{ 
    background:#c9a84c; 
    color:#fff; 
  }

  /* Sub-tabs row — same scroll-arrow/fade mechanism as the main tab bar,
     recolored for the light background this row sits on */
  .hero-tabs-bar-sub-wrap{
    position:relative;
    background:#fff;
    transition: background 0.3s ease;
  }
  .hero-tabs-fade.sub{ border-radius:0; }
  .hero-tabs-fade.sub.left{ background:linear-gradient(90deg, rgba(255,255,255,.95), rgba(255,255,255,0)); }
  .hero-tabs-fade.sub.right{ background:linear-gradient(270deg, rgba(255,255,255,.95), rgba(255,255,255,0)); }
  .hero-tabs-scroll-btn.sub{ color:#12213a; }
  .hero-tabs-scroll-btn.sub i{ background:rgba(0,0,0,.08); }
  .hero-tabs-scroll-btn.sub:hover i{ background:rgba(0,0,0,.15); }

  /* White search pill, sitting right under the dark tab bar */
  .hero-search-row{
    background:#fff;
    border-radius:0 0 1rem 1rem;
    box-shadow:0 20px 45px rgba(0,0,0,.25);
    display:flex;
    align-items:stretch;
    flex-wrap:wrap;
    transition: background 0.3s ease, box-shadow 0.3s ease;
    position:relative;
  }
  .hero-city-select{
    display:flex;
    align-items:center;
    gap:.4rem;
    background:none;
    border:none;
    border-right:1px solid #e7e9ee;
    color:#12213a;
    font-weight:700;
    font-size:.95rem;
    padding: 16px 1.1rem;
    flex:0 0 auto;
    transition: all 0.3s ease;
  }
  .hero-city-select:hover{ color:#c9a84c; }
  .hero-search-input{
    flex:1 1 220px;
    display:flex;
    align-items:center;
    gap:.6rem;
    padding:1rem 1.1rem;
  }
  .hero-search-input i{ 
    color:#6b7686;
    transition: color 0.3s ease;
  }
  .hero-search-input input{
    border:none;
    outline:none;
    width:100%;
    font-size:.95rem;
    color:#12213a;
    background:transparent;
    transition: color 0.3s ease;
  }
  .hero-search-input input::placeholder{ 
    color:#8a93a3;
    transition: color 0.3s ease;
  }
  .hero-search-btn{
    background:#c9a84c;
    color:#fff;
    border:none;
    font-weight:700;
    font-size:.95rem;
    padding:0 2rem;
    flex:0 0 auto;
    border-radius:0 0 1rem 0;
    transition: background 0.3s ease;
  }
  .hero-search-btn:hover{ background:#b8943a; }

  .hero-ask-ai-btn{
    display:flex;
    align-items:center;
    gap:.4rem;
    background:#fff;
    border:1.5px solid #c9a84c;
    border-radius:2rem;
    padding:.6rem 1.2rem;
    font-weight:700;
    font-size:.9rem;
    color:#12213a;
    flex:0 0 auto;
    margin:.5rem;
    transition: all 0.3s ease;
  }
  .hero-ask-ai-btn i{ color:#c9a84c; }
  .hero-ask-ai-btn:hover{ 
    background:#c9a84c;
    color:#fff;
    border-color:#c9a84c;
  }
  .hero-ask-ai-btn:hover i{ color:#fff; }

  /* ============ City dropdown: City / Pincode mode ============ */
  .hero-city-menu{
    width:260px;
    padding:.6rem;
    transition: background 0.3s ease, border-color 0.3s ease;
  }
  .hero-city-mode-switch{
    display:flex;
    gap:.4rem;
    margin-bottom:.5rem;
  }
  .hero-city-mode-switch .hero-city-mode-btn{
    flex:1;
    border:1px solid #e7e9ee;
    background:#fff;
    color:#6b7686;
    border-radius:2rem;
    padding:.35rem .5rem;
    font-size:.8rem;
    font-weight:600;
    transition: all 0.3s ease;
  }
  .hero-city-mode-switch .hero-city-mode-btn.active{
    background:#c9a84c;
    border-color:#c9a84c;
    color:#fff;
  }
  .hero-city-list{
    list-style:none;
    margin:0;
    padding:0;
    max-height:240px;
    overflow-y:auto;
  }
  .hero-city-list .dropdown-item{
    transition: background 0.3s ease, color 0.3s ease;
  }
  .hero-city-pincode-input{
    display:flex;
    align-items:center;
    gap:.5rem;
    background:#f7f8fa;
    border-radius:2rem;
    padding:.5rem .9rem;
    transition: background 0.3s ease;
  }
  .hero-city-pincode-input input{
    border:none;
    background:transparent;
    outline:none;
    width:100%;
    font-size:.88rem;
    transition: color 0.3s ease;
  }
  .hero-city-pincode-input i{ 
    color:#6b7686;
    transition: color 0.3s ease;
  }
  .hero-city-pincode-status{
    font-size:.78rem;
    padding:.4rem .1rem 0;
    color:#6b7686;
    min-height:1.1em;
    transition: color 0.3s ease;
  }
  .hero-city-pincode-status.error{ color:#e0245e; }
  .hero-city-pincode-status.success{ color:#1a9c5c; }

  /* Enhanced city search styles */
  .hero-city-search-input {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    background: rgba(255,255,255,0.08);
    border-radius: 2rem;
    padding: 0.4rem 0.9rem;
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
  }

  .hero-city-search-input i {
    color: rgba(255,255,255,0.6);
    font-size: 0.9rem;
  }

  .hero-city-search-input input {
    background: transparent;
    border: none;
    outline: none;
    color: #fff;
    width: 100%;
    font-size: 0.88rem;
    padding: 0.3rem 0;
  }

  .hero-city-search-input input::placeholder {
    color: rgba(255,255,255,0.5);
  }

  .hero-city-list {
    max-height: 200px;
    overflow-y: auto;
    margin-top: 0.3rem;
  }

  .hero-city-list .dropdown-item {
    padding: 0.5rem 0.7rem;
    border-radius: 0.4rem;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .hero-city-list .dropdown-item:hover {
    background: rgba(255,255,255,0.08);
    color: #c9a84c;
  }

  .hero-city-list .dropdown-item .city-state {
    font-size: 0.75rem;
    color: rgba(255,255,255,0.5);
    margin-left: 0.3rem;
  }

  .hero-city-list .dropdown-item .highlight {
    color: #c9a84c;
    font-weight: 600;
  }

  .hero-city-no-results {
    padding: 0.8rem;
    text-align: center;
    color: rgba(255,255,255,0.6);
    font-size: 0.85rem;
  }

  /* Quick city buttons */
  .hero-quick-cities {
    display: flex;
    gap: 0.4rem;
    padding: 0.4rem 0.8rem 0.6rem;
    flex-wrap: wrap;
    border-top: 1px solid rgba(255,255,255,0.05);
  }

  .quick-city-btn {
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.8);
    border-radius: 1.2rem;
    padding: 0.2rem 0.7rem;
    font-size: 0.72rem;
    font-weight: 500;
    transition: all 0.3s ease;
    cursor: pointer;
    white-space: nowrap;
  }

  .quick-city-btn:hover {
    background: rgba(201, 168, 76, 0.2);
    border-color: #c9a84c;
    color: #c9a84c;
  }

  .quick-city-btn:active {
    transform: scale(0.95);
  }

  .hero-citypin-wrap{
    position: relative;
    display: flex;
    gap: 0;
    flex: 0 0 auto;
    border-right: 1px solid #e7e9ee;
    z-index: 100;
  }
  .hero-citypin-btn{
    display: flex;
    align-items: center;
    gap: .35rem;
    background: none;
    border: none;
    color: #12213a;
    font-weight: 700;
    font-size: .88rem;
    padding: 16px 1rem;
    white-space: nowrap;
    transition: color .2s ease;
    cursor: pointer;
  }
  .hero-citypin-btn:hover{ color:#c9a84c; }
  .hero-citypin-btn.active{ color:#c9a84c; }

  .hero-citypin-panel{
    display: none;
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    width: 320px;
    min-width: 280px;
    background: #0a0e1c;
    border-radius: .9rem;
    box-shadow: 0 20px 60px rgba(0,0,0,.5);
    padding: .8rem;
    z-index: 9999;
    border: 1px solid rgba(255,255,255,0.08);
  }
  .hero-citypin-panel.open{ 
    display: block;
    animation: heroCityDropIn .25s ease;
  }
  
  @keyframes heroCityDropIn {
    from {
      opacity: 0;
      transform: translateY(-10px) scale(0.98);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }
  
  .hero-citypin-panel .hero-city-list{
    list-style: none;
    margin: 0;
    padding: 0;
    max-height: 220px;
    overflow-y: auto;
  }
  .hero-citypin-panel .hero-city-list .dropdown-item{
    color: rgba(255,255,255,.8);
    padding: .5rem .7rem;
    border-radius: .5rem;
    font-size: .9rem;
    display: block;
    text-decoration: none;
    transition: all 0.2s ease;
  }
  .hero-citypin-panel .hero-city-list .dropdown-item:hover{
    background: rgba(255,255,255,.08);
    color: #c9a84c;
  }
  .hero-citypin-panel .hero-city-pincode-input{
    background: rgba(255,255,255,.08);
    border-radius: 2rem;
    padding: .5rem .9rem;
    display: flex;
    align-items: center;
    gap: .5rem;
  }
  .hero-citypin-panel .hero-city-pincode-input input{
    background: transparent;
    border: none;
    outline: none;
    color: #fff;
    width: 100%;
    font-size: .88rem;
  }
  .hero-citypin-panel .hero-city-pincode-input input::placeholder{
    color: rgba(255,255,255,0.4);
  }
  .hero-citypin-panel .hero-city-pincode-input i{ color: rgba(255,255,255,.6); }
  .hero-citypin-panel .hero-city-pincode-status{
    font-size: .78rem;
    padding: .4rem .1rem 0;
    color: rgba(255,255,255,.6);
  }
  .hero-citypin-panel .hero-city-pincode-status.error{ color:#ff6b95; }
  .hero-citypin-panel .hero-city-pincode-status.success{ color:#3ddc84; }
  
  .hero-citypin-panel .hero-city-search-input {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    background: rgba(255,255,255,0.06);
    border-radius: 2rem;
    padding: 0.4rem 0.9rem;
    margin-bottom: 0.5rem;
    border: 1px solid rgba(255,255,255,0.05);
    transition: all 0.3s ease;
  }
  .hero-citypin-panel .hero-city-search-input:focus-within {
    border-color: rgba(201, 168, 76, 0.4);
    background: rgba(255,255,255,0.1);
  }
  .hero-citypin-panel .hero-city-search-input input{
    background: transparent;
    border: none;
    outline: none;
    color: #fff;
    width: 100%;
    font-size: 0.88rem;
  }
  .hero-citypin-panel .hero-city-search-input input::placeholder{
    color: rgba(255,255,255,0.4);
  }
  .hero-citypin-panel .hero-city-search-input i{
    color: rgba(255,255,255,0.4);
  }
  
  .hero-citypin-panel .hero-quick-cities {
    display: flex;
    gap: 0.3rem;
    padding: 0.4rem 0 0;
    flex-wrap: wrap;
    border-top: 1px solid rgba(255,255,255,0.06);
    margin-top: 0.3rem;
  }
  .hero-citypin-panel .quick-city-btn {
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.06);
    color: rgba(255,255,255,0.7);
    border-radius: 1.2rem;
    padding: 0.2rem 0.6rem;
    font-size: 0.7rem;
    font-weight: 500;
    transition: all 0.3s ease;
    cursor: pointer;
    white-space: nowrap;
  }
  .hero-citypin-panel .quick-city-btn:hover {
    background: rgba(201, 168, 76, 0.2);
    border-color: #c9a84c;
    color: #c9a84c;
  }

  .hero-citypin-panel .hero-city-no-results {
    padding: 0.8rem;
    text-align: center;
    color: rgba(255,255,255,0.5);
    font-size: 0.85rem;
  }

  /* ============ AI Assistant modal ============ */
  #aiAssistantModal .modal-dialog{ max-width:1100px; height:80vh; }
  #aiAssistantModal .modal-content{ height:100%; border-radius:1rem; border:none; overflow:hidden; }
  #aiAssistantModal .modal-body{ padding:0; height:100%; }
  .ai-shell{ display:flex; height:100%; }

  .ai-sidebar{
    width:230px;
    flex:0 0 auto;
    background:#f7f8fa;
    border-right:1px solid #e7e9ee;
    padding:1.25rem 1rem;
    display:flex;
    flex-direction:column;
    gap:.75rem;
  }
  .ai-sidebar .brand{ font-weight:800; font-size:1.05rem; color:#12213a; margin-bottom:.5rem; }
  .ai-sidebar .ai-side-btn{
    display:flex; align-items:center; gap:.5rem;
    background:#fff; border:1px solid #e7e9ee; border-radius:.6rem;
    padding:.6rem .8rem; font-size:.88rem; font-weight:600; color:#12213a;
    text-align:left;
    width:100%;
  }
  .ai-sidebar .ai-side-btn:hover{ border-color:#c9a84c; color:#c9a84c; }
  .ai-sidebar .ai-recent-label{
    text-transform:uppercase; font-size:.7rem; letter-spacing:.05em; color:#6b7686;
    font-weight:700; margin-top:.5rem;
  }
  .ai-sidebar .ai-recent-empty{ color:#8a93a3; font-size:.85rem; }
  .ai-sidebar .ai-suggestion-chips{
    display:flex;
    flex-wrap:wrap;
    gap:.4rem;
    margin-top:.5rem;
  }
  .ai-sidebar .ai-suggestion-chip{
    background:#fff;
    border:1px solid #e7e9ee;
    border-radius:1rem;
    padding:.3rem .7rem;
    font-size:.75rem;
    color:#12213a;
    cursor:pointer;
    transition:all .2s;
  }
  .ai-sidebar .ai-suggestion-chip:hover{
    border-color:#c9a84c;
    background:#f5f0e0;
  }

  .ai-main{ flex:1; display:flex; flex-direction:column; min-width:0; }
  .ai-main-header{
    display:flex; align-items:center; justify-content:space-between;
    padding:1rem 1.25rem; border-bottom:1px solid #e7e9ee;
  }
  .ai-main-header .ai-title{ display:flex; align-items:center; gap:.5rem; }
  .ai-main-header .ai-status-dot{ width:8px; height:8px; border-radius:50%; background:#22c55e; }
  .ai-main-header h6{ margin:0; font-weight:800; color:#12213a; }
  .ai-main-header small{ color:#6b7686; display:block; }

  .ai-messages{ 
    flex:1; 
    overflow-y:auto; 
    padding:1.25rem; 
    background:#f7f8fa;
    max-height: calc(80vh - 200px);
  }
  .ai-msg{ display:flex; gap:.6rem; margin-bottom:1rem; animation: fadeIn .3s ease; }
  .ai-msg .avatar{
    width:34px; height:34px; border-radius:.5rem; background:#c9a84c; color:#fff;
    display:flex; align-items:center; justify-content:center; font-weight:800; font-size:.75rem; flex-shrink:0;
  }
  .ai-msg .bubble{
    background:#fff; border:1px solid #e7e9ee; border-radius:.8rem; padding:.7rem 1rem;
    font-size:.92rem; color:#12213a; max-width:70%;
  }
  .ai-msg .bubble .typing-indicator{
    display:inline-flex;
    gap:.2rem;
    padding:.2rem 0;
  }
  .ai-msg .bubble .typing-indicator span{
    width:6px; height:6px; border-radius:50%; background:#6b7686;
    animation: typingBounce 1.4s infinite;
  }
  .ai-msg .bubble .typing-indicator span:nth-child(2){ animation-delay:.2s; }
  .ai-msg .bubble .typing-indicator span:nth-child(3){ animation-delay:.4s; }
  .ai-msg.user{ flex-direction:row-reverse; }
  .ai-msg.user .bubble{ background:#c9a84c; color:#fff; border-color:#c9a84c; }
  .ai-msg.user .avatar{ background:#c9a84c; color:#fff; }

  @keyframes fadeIn{
    from{ opacity:0; transform:translateY(10px); }
    to{ opacity:1; transform:translateY(0); }
  }
  @keyframes typingBounce{
    0%,60%,100%{ transform:translateY(0); }
    30%{ transform:translateY(-6px); }
  }

  .ai-input-row{ padding:1rem 1.25rem; border-top:1px solid #e7e9ee; background:#fff; }
  .ai-input-box{
    display:flex; align-items:center; gap:.6rem;
    background:#f7f8fa; border:1px solid #e7e9ee; border-radius:2rem; padding:.6rem 1.1rem;
  }
  .ai-input-box input{ flex:1; border:none; background:transparent; outline:none; font-size:.92rem; }
  .ai-input-box .mic-btn{ background:none; border:none; color:#6b7686; font-size:1.1rem; }
  .ai-input-box .send-btn{
    width:34px; height:34px; border-radius:50%; border:none; background:#c9a84c; color:#fff;
    display:flex; align-items:center; justify-content:center;
  }
  .ai-input-box .send-btn:disabled{ opacity:.5; cursor:not-allowed; }
  .ai-input-row .disclaimer{ text-align:center; font-size:.75rem; color:#8a93a3; margin-top:.5rem; }

  /* ============ MOBILE RESPONSIVE STYLES ============ */
  @media (max-width: 767.98px){
    /* Hide arrows on mobile */
    .hero-arrow {
      display: none !important;
    }

    /* Hide tab-bar scroll arrows/fades on mobile — swipe is enough there */
    .hero-tabs-scroll-btn, .hero-tabs-fade{
      display: none !important;
    }
    
    /* Smaller banner height */
    .hero-banner {
  position: relative;
  padding: 0.5rem 0 0.5rem;
  overflow: visible !important; /* Prevents clipping of the dropdown */
  min-height: 500px;
  z-index: 100; /* Elevates the hero section above the section below it */
}

.hero-banner .hero-inner { 
  position: relative; 
  z-index: 101; 
}
    
    .hero-banner .hero-bg {
      min-height: 380px !important;
      background-size: cover !important;
      background-position: center top !important;
    }
    
    /* Smaller title and subtitle */
    .hero-title {
      font-size: 1.2rem !important;
      margin-bottom: 0.3rem !important;
      line-height: 1.3 !important;
    }
    
    .hero-subtitle {
      font-size: 0.8rem !important;
      margin-bottom: 0 !important;
    }
    
    /* Adjust spacing */
    .hero-panel-wrap {
      margin-top: 0.8rem !important;
      margin-bottom: 0 !important;
    }
    
    .hero-content {
      padding-bottom: 0 !important;
      margin-bottom: 0 !important;
    }
    
    .hero-inner {
      padding-bottom: 0 !important;
    }
    
    /* Smaller dots */
    .hero-dots {
      bottom: 0.5rem !important;
    }
    
    .hero-dots button {
      width: 6px !important;
      height: 6px !important;
    }
    
    .hero-dots button.active {
      width: 16px !important;
      border-radius: 3px !important;
    }
    
    /* Search panel adjustments */
    .hero-tabs-bar .tab-btn {
      padding: 0.5rem 0.7rem !important;
      font-size: 0.72rem !important;
      gap: 0.3rem !important;
    }
    
    .hero-tabs-bar {
      padding: 0.3rem 0.3rem 0 !important;
      border-radius: 0.7rem 0.7rem 0 0 !important;
    }

    .hero-tabs-bar-wrap {
      border-radius: 0.7rem 0.7rem 0 0 !important;
    }
    
    .hero-tabs-bar-sub {
      padding: 0.3rem 0.3rem !important;
      gap: 0.3rem !important;
    }
    
    .hero-tabs-bar-sub .tab-btn-sub {
      font-size: 0.68rem !important;
      padding: 0.3rem 0.6rem !important;
      gap: 0.3rem !important;
    }
    
    .hero-citypin-wrap{ 
      border-right:none; 
      border-bottom:1px solid #e7e9ee; 
      width:100%; 
    }
    .hero-citypin-panel{ 
      width: calc(100vw - 2rem) !important;
      left: 0 !important;
    }
    
    .hero-citypin-btn {
      padding: 0.6rem 0.8rem !important;
      font-size: 0.8rem !important;
    }
    
    .hero-search-input {
      padding: 0.5rem 0.8rem !important;
      flex: 1 1 100% !important;
    }
    
    .hero-search-input input {
      font-size: 0.82rem !important;
    }
    
    .hero-search-btn {
      width: 100% !important;
      border-radius: 0 0 0.7rem 0.7rem !important;
      padding: 0.6rem !important;
      font-size: 0.85rem !important;
    }
    
    .hero-ask-ai-btn {
      padding: 0.4rem 0.8rem !important;
      font-size: 0.78rem !important;
      margin: 0.3rem !important;
    }
    
    .hero-search-row {
      border-radius: 0 0 0.7rem 0.7rem !important;
    }

    .hero-quick-cities {
      padding: 0.3rem 0.6rem 0.4rem !important;
      gap: 0.3rem !important;
    }

    .quick-city-btn {
      font-size: 0.65rem !important;
      padding: 0.15rem 0.5rem !important;
    }
    
    /* AI Modal mobile */
    .ai-sidebar {
      display: none !important;
    }
    
    #aiAssistantModal .modal-dialog {
      height: 90vh !important;
      margin: 0.5rem !important;
    }
    
    .ai-messages {
      max-height: calc(90vh - 160px) !important;
      padding: 0.8rem !important;
    }
    
    .ai-msg .bubble {
      font-size: 0.85rem !important;
      max-width: 85% !important;
      padding: 0.5rem 0.8rem !important;
    }
    
    .ai-input-box input {
      font-size: 0.85rem !important;
    }
    
    .ai-main-header h6 {
      font-size: 0.9rem !important;
    }
    
    .ai-main-header small {
      font-size: 0.7rem !important;
    }
    
    .ai-input-row {
      padding: 0.6rem 0.8rem !important;
    }
    
    .ai-input-box {
      padding: 0.4rem 0.8rem !important;
    }
  }

  /* Extra small devices */
  @media (max-width: 480px) {
    .hero-banner {
      min-height: 320px !important;
    }
    
    .hero-banner .hero-bg {
      min-height: 320px !important;
    }
    
    .hero-title {
      font-size: 1rem !important;
    }
    
    .hero-subtitle {
      font-size: 0.7rem !important;
    }
    
    .hero-tabs-bar .tab-btn {
      padding: 0.4rem 0.5rem !important;
      font-size: 0.65rem !important;
    }
    
    .hero-tabs-bar-sub .tab-btn-sub {
      font-size: 0.6rem !important;
      padding: 0.25rem 0.5rem !important;
    }
    
    .hero-panel-wrap {
      margin-top: 0.5rem !important;
    }

   .hero-citypin-panel {
  display: none;
  position: absolute;
  top: calc(100% + 8px);
  left: 0;
  width: 320px;
  min-width: 280px;
  background: #0a0e1c;
  border-radius: .9rem;
  box-shadow: 0 20px 60px rgba(0,0,0,.5);
  padding: .8rem;
  z-index: 999999 !important; /* Guarantees rendering on top */
  border: 1px solid rgba(255,255,255,0.08);
}
  }
  /* Add this to your style block or main CSS file */
section:not(.hero-banner) {
  position: relative;
  z-index: 1;
}
</style>

<section class="hero-banner">
    <div class="hero-bg" id="heroBg" style="background-image:url('<?php echo htmlspecialchars($imagePath); ?>');"></div>
    <div class="overlay"></div>

    <?php if (count($banners) > 1): ?>
        <!-- Prev/Next arrows - hidden on mobile via CSS -->
        <button class="hero-arrow prev" type="button" id="heroPrev" aria-label="Previous slide">
            <i class="bi bi-chevron-left"></i>
        </button>
        <button class="hero-arrow next" type="button" id="heroNext" aria-label="Next slide">
            <i class="bi bi-chevron-right"></i>
        </button>

        <!-- Dot indicators -->
        <div class="hero-dots" id="heroDots">
            <?php foreach ($banners as $i => $b): ?>
                <button type="button" data-index="<?php echo $i; ?>" class="<?php echo $i === 0 ? 'active' : ''; ?>" aria-label="Go to slide <?php echo $i + 1; ?>"></button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="hero-inner">
        <div class="container">
            <div class="hero-content text-left">

                <h1 class="hero-title" id="heroTitle">
                    <?php echo nl2br(htmlspecialchars($banner['title'])); ?>
                </h1>

                <p class="hero-subtitle" id="heroSubtitle">
                    <?php echo htmlspecialchars($banner['subtitle']); ?>
                </p>

                <!-- ============ Search panel: icon tabs, city + search row ============ -->
                <div class="hero-panel-wrap" style="max-width:100%;">

                    <!-- Tabs -->
                    <div class="hero-tabs-bar-wrap">
                        <button type="button" class="hero-tabs-scroll-btn left" id="heroTabsScrollLeft" aria-label="Scroll tabs left">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <div class="hero-tabs-fade left" id="heroTabsFadeLeft"></div>

                        <div class="hero-tabs-bar" id="heroTabsBar">
                            <?php foreach ($tabs as $i => $tab): ?>
                                <button
                                    type="button"
                                    class="tab-btn <?php echo ($i === $activeTabIndex) ? 'active' : ''; ?>"
                                    data-placeholder="<?php echo htmlspecialchars($tab['label']); ?> Property"
                                    <?php if (!empty($tab['type'])): ?>data-type="<?php echo htmlspecialchars($tab['type']); ?>"<?php endif; ?>
                                    <?php if (!empty($tab['url'])): ?>data-url="<?php echo htmlspecialchars($tab['url']); ?>"<?php endif; ?>
                                    <?php if (!empty($tab['newTab'])): ?>data-new-tab="1"<?php endif; ?>>
                                    <i class="bi <?php echo htmlspecialchars($tab['icon']); ?>"></i>
                                    <?php echo htmlspecialchars($tab['label']); ?>
                                    <?php if (!empty($tab['badge'])): ?>
                                        <span class="tab-badge"><?php echo htmlspecialchars($tab['badge']); ?></span>
                                    <?php endif; ?>
                                </button>
                            <?php endforeach; ?>
                        </div>

                        <div class="hero-tabs-fade right" id="heroTabsFadeRight"></div>
                        <button type="button" class="hero-tabs-scroll-btn right" id="heroTabsScrollRight" aria-label="Scroll tabs right">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>

                    <!-- Second row of tabs — populated dynamically by JS from the active main tab's real sub-categories -->
                    <div class="hero-tabs-bar-sub-wrap" id="heroTabsBarSubWrap">
                        <button type="button" class="hero-tabs-scroll-btn sub left" id="heroTabsSubScrollLeft" aria-label="Scroll sub tabs left">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <div class="hero-tabs-fade sub left" id="heroTabsSubFadeLeft"></div>

                        <div class="hero-tabs-bar-sub" id="heroTabsBarSub"></div>

                        <div class="hero-tabs-fade sub right" id="heroTabsSubFadeRight"></div>
                        <button type="button" class="hero-tabs-scroll-btn sub right" id="heroTabsSubScrollRight" aria-label="Scroll sub tabs right">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>

                    <!-- City + Search -->
                    <div class="hero-search-row" style="position:relative; z-index:10; overflow:visible;">
                        <div class="hero-citypin-wrap" id="heroCityPinWrap" style="position:relative; z-index:99999;">
                            <button type="button" class="hero-citypin-btn active" id="heroCityModeBtnMain" data-mode="name">
                                <i class="bi bi-geo-alt"></i> <span class="label-text">All India</span>
                            </button>
                            <button type="button" class="hero-citypin-btn" id="heroPincodeModeBtnMain" data-mode="pincode">
                                <i class="bi bi-mailbox"></i> Pincode
                            </button>

                            <div class="hero-citypin-panel" id="heroCityPinPanel" style="z-index:999999; position:absolute; top:100%; left:0; background:#0a0e1c; border-radius:12px; padding:12px; width:320px; box-shadow:0 20px 60px rgba(0,0,0,0.5);">
                              <!-- City Mode Content (includes search + city list) -->
                              <div id="heroCityModeContent">
                                  <!-- City search input -->
                                <div class="hero-city-search-input" style="display:flex; align-items:center; gap:8px; background:rgba(255,255,255,0.06); border-radius:20px; padding:6px 12px; margin-bottom:8px; border:1px solid rgba(255,255,255,0.05);">
            <i class="bi bi-search"></i>
            <input type="text" id="heroCitySearch" placeholder="Search cities in India..." autocomplete="off">
        </div>
                                
                                <?php 
                                $cities = [
                                    'Mumbai', 'Delhi', 'Bengaluru', 'Hyderabad', 'Ahmedabad', 
                                    'Chennai', 'Kolkata', 'Surat', 'Pune', 'Jaipur', 
                                    'Lucknow', 'Kanpur', 'Nagpur', 'Indore', 'Thane', 
                                    'Bhopal', 'Visakhapatnam', 'Patna', 'Vadodara', 'Ghaziabad',
                                    'Coimbatore', 'Agra', 'Madurai', 'Ludhiana', 'Nashik',
                                    'Meerut', 'Faridabad', 'Rajkot', 'Jamshedpur', 'Jabalpur',
                                    'Varanasi', 'Srinagar', 'Aurangabad', 'Dhanbad', 'Jodhpur',
                                    'Amritsar', 'Ranchi', 'Navi Mumbai', 'Gwalior', 'Howrah',
                                    'Chandigarh', 'Vijayawada', 'Bareilly', 'Cuttack', 'Raipur',
                                    'Kota', 'Guwahati', 'Thiruvananthapuram', 'Moradabad', 'Solapur',
                                    'Mysuru', 'Gurugram', 'Aligarh', 'Jalandhar', 'Bhubaneswar',
                                    'Salem', 'Warangal', 'Dehradun', 'Guntur', 'Bikaner',
                                
    // Tier 1 - Metros
    'Mumbai', 'Delhi', 'Bengaluru', 'Hyderabad', 'Ahmedabad', 
    'Chennai', 'Kolkata', 'Surat', 'Pune', 'Jaipur', 
    'Lucknow', 'Kanpur', 'Nagpur', 'Indore', 'Thane', 
    'Bhopal', 'Visakhapatnam', 'Patna', 'Vadodara', 'Ghaziabad',
    
    // Tier 2 - Major Cities
    'Coimbatore', 'Agra', 'Madurai', 'Ludhiana', 'Nashik',
    'Meerut', 'Faridabad', 'Rajkot', 'Jamshedpur', 'Jabalpur',
    'Varanasi', 'Srinagar', 'Aurangabad', 'Dhanbad', 'Jodhpur',
    'Amritsar', 'Ranchi', 'Navi Mumbai', 'Gwalior', 'Howrah',
    'Chandigarh', 'Vijayawada', 'Bareilly', 'Cuttack', 'Raipur',
    'Kota', 'Guwahati', 'Thiruvananthapuram', 'Moradabad', 'Solapur',
    'Mysuru', 'Gurugram', 'Aligarh', 'Jalandhar', 'Bhubaneswar',
    'Salem', 'Warangal', 'Dehradun', 'Guntur', 'Bikaner',
    
    // More cities
    'Agartala', 'Ajmer', 'Akola', 'Alappuzha', 'Allahabad',
    'Alwar', 'Ambala', 'Amravati', 'Asansol', 'Belgaum',
    'Bellary', 'Bhagalpur', 'Bharatpur', 'Bhavnagar', 'Bhilai',
    'Bhiwandi', 'Bhopal', 'Bhubaneshwar', 'Bhuj', 'Bidar',
    'Bijapur', 'Bikaner', 'Bilaspur', 'Bokaro', 'Chandrapur',
    'Daman', 'Darbhanga', 'Davangere', 'Dharwad', 'Dibrugarh',
    'Dindigul', 'Diu', 'Faizabad', 'Firozabad', 'Gandhinagar',
    'Gangtok', 'Gaya', 'Gorakhpur', 'Gulbarga', 'Haldia',
    'Hisar', 'Hubli', 'Imphal', 'Itanagar', 'Jammu',
    'Jhansi', 'Jorhat', 'Kakinada', 'Kannur', 'Karnal',
    'Karur', 'Khandwa', 'Kharagpur', 'Kochi', 'Kohima',
    'Kolhapur', 'Kollam', 'Kozhikode', 'Kumbakonam', 'Kurnool',
    'Latur', 'Machilipatnam', 'Malegaon', 'Mangalore', 'Mathura',
    'Medinipur', 'Mirzapur', 'Muzaffarnagar', 'Muzaffarpur', 'Nagercoil',
    'Nanded', 'Nandyal', 'Navsari', 'Nellore', 'Nizamabad',
    'Panaji', 'Panipat', 'Parbhani', 'Pathankot', 'Patiala',
    'Pilibhit', 'Pondicherry', 'Porbandar', 'Port Blair', 'Puri',
    'Purnia', 'Ratlam', 'Rewa', 'Roorkee', 'Rourkela',
    'Sagar', 'Saharanpur', 'Sambalpur', 'Shillong', 'Shimla',
    'Silchar', 'Siliguri', 'Solan', 'Srikakulam', 'Sultanpur',
    'Thanjavur', 'Tiruchirappalli', 'Tirunelveli', 'Tirupati', 'Trivandrum',
    'Tuticorin', 'Udaipur', 'Udupi', 'Ujjain', 'Vapi',
    'Vellore', 'Vizag'
];
                                ?>

                                <ul class="hero-city-list" id="heroCityList">
            <li><a class="dropdown-item city-option" href="#" data-city="All India">🇮🇳 All India</a></li>
            <?php foreach ($cities as $c): ?>
                <li><a class="dropdown-item city-option" href="#" data-city="<?php echo htmlspecialchars($c); ?>"><?php echo htmlspecialchars($c); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>

                                <!-- Pincode Mode Content (only pincode input, no city search) -->
    <div class="hero-city-pincode-wrap" id="heroCityPincodeWrap" style="display:none;">
        <div class="hero-city-pincode-input">
            <i class="bi bi-mailbox"></i>
            <input type="text" id="heroCityPincode" placeholder="Enter 6-digit pincode" inputmode="numeric" maxlength="6">
            <button type="button" id="heroCityPincodeGo" class="btn btn-sm btn-dark" style="background:#c9a84c; color:#fff; border:none; border-radius:1.5rem; padding:0.25rem 1rem; font-weight:600;">Go</button>
        </div>
        <div class="hero-city-pincode-status" id="heroCityPincodeStatus"></div>
                                </div>

                            </div>
                        </div>

                        <div class="hero-search-input">
                            <i class="bi bi-search"></i>
                            <input id="heroSearch" type="text" placeholder="Search by locality, property, or project">
                        </div>

                        <button type="button" class="hero-ask-ai-btn" data-bs-toggle="modal" data-bs-target="#aiAssistantModal">
                            <i class="bi bi-stars"></i> Ask AI
                        </button>

                        <button class="hero-search-btn" onclick="searchProperty()">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>

                </div>

            </div>
        </div>
    </div>

</section>

<!-- ============ AI ASSISTANT MODAL ============ -->
<div class="modal fade" id="aiAssistantModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body">
        <div class="ai-shell">

          <!-- Sidebar -->
          <div class="ai-sidebar">
            <div class="brand"><?php echo htmlspecialchars($site['name'] ?? 'Real Estate'); ?></div>

            <button type="button" class="ai-side-btn" id="aiNewSearch">
              <i class="bi bi-plus-lg"></i> New Search
            </button>

            <div class="dropdown">
              <button class="ai-side-btn dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-geo-alt"></i> <span id="aiLocationLabel">India</span>
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item ai-location-option" href="#">India</a></li>
                <?php foreach ($cities as $c): ?>
                  <li><a class="dropdown-item ai-location-option" href="#"><?php echo htmlspecialchars($c); ?></a></li>
                <?php endforeach; ?>
              </ul>
            </div>

            <button type="button" class="ai-side-btn" id="aiShortlistBtn">
              <i class="bi bi-heart"></i> Shortlist
            </button>

            <div class="ai-recent-label">Quick Suggestions</div>
            <div class="ai-suggestion-chips">
              <?php foreach ($aiSuggestions as $suggestion): ?>
                <button class="ai-suggestion-chip" data-suggestion="<?php echo htmlspecialchars($suggestion); ?>">
                  <?php echo htmlspecialchars($suggestion); ?>
                </button>
              <?php endforeach; ?>
            </div>

            <div class="ai-recent-label">Recent Chats</div>
            <div class="ai-recent-empty" id="aiRecentEmpty">No saved chats yet.</div>
            <div id="aiRecentChats"></div>
          </div>

          <!-- Main chat area -->
          <div class="ai-main">
            <div class="ai-main-header">
              <div class="ai-title">
                <span class="ai-status-dot" id="aiStatusDot"></span>
                <div>
                  <h6><?php echo htmlspecialchars($site['name'] ?? 'Real Estate'); ?> AI</h6>
                  <small id="aiStatusText">Search smarter &middot; Find faster &middot; Buy better</small>
                </div>
              </div>
              <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
            </div>

            <div class="ai-messages" id="aiMessages">
              <div class="ai-msg">
                <div class="avatar">AI</div>
                <div class="bubble">
                  👋 Hi! I'm your AI property assistant. Tell me what you're looking for and I'll help you find the perfect property.
                  <br><br>
                  Try asking me:
                  <ul style="margin:5px 0 0 15px; font-size:.85rem;">
                    <li>"Find 2BHK apartments in Mumbai under ₹1 Cr"</li>
                    <li>"Best areas to invest in Bangalore"</li>
                    <li>"Commercial properties for rent in Delhi"</li>
                  </ul>
                </div>
              </div>
            </div>

            <div class="ai-input-row">
              <div class="ai-input-box">
                <input type="text" id="aiChatInput" placeholder="Ask about properties, locations, prices..." autocomplete="off">
                <button type="button" class="mic-btn" id="aiMicBtn" aria-label="Voice input"><i class="bi bi-mic"></i></button>
                <button type="button" class="send-btn" id="aiSendBtn" aria-label="Send"><i class="bi bi-send-fill"></i></button>
              </div>
              <div class="disclaimer"><?php echo htmlspecialchars($site['name'] ?? 'Real Estate'); ?> AI may make mistakes. Always verify listing details.</div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<script>
function searchProperty() {
    let keyword = document.getElementById("heroSearch").value.trim();
    let city = window.selectedHeroCity || "";
    let activeSubTab = document.querySelector(".tab-btn-sub.active");
    let type = activeSubTab ? (activeSubTab.dataset.type || "").trim() : "";

    let params = new URLSearchParams();
    if (keyword !== "") params.set("search", keyword);
    if (city !== "" && city !== "Select City" && city !== "All India") {
        params.set("city", city);
    }
    if (type !== "") params.set("type", type);
    
    // Add flag for all-India search
    if (!city || city === "All India") {
        params.set("all_india", "1");
    }

    window.location.href = "properties.php" + (params.toString() ? "?" + params.toString() : "");
}

// ============ AI Assistant Chat Functionality ============
(function() {
    'use strict';

    // DOM Elements
    const aiMessages = document.getElementById('aiMessages');
    const aiInput = document.getElementById('aiChatInput');
    const aiSendBtn = document.getElementById('aiSendBtn');
    const aiMicBtn = document.getElementById('aiMicBtn');
    const aiNewSearch = document.getElementById('aiNewSearch');
    const aiStatusDot = document.getElementById('aiStatusDot');
    const aiStatusText = document.getElementById('aiStatusText');
    const aiLocationLabel = document.getElementById('aiLocationLabel');
    const aiShortlistBtn = document.getElementById('aiShortlistBtn');
    const aiRecentChats = document.getElementById('aiRecentChats');
    const aiRecentEmpty = document.getElementById('aiRecentEmpty');

    // State
    let isProcessing = false;
    let chatHistory = [];
    let currentLocation = 'India';
    let shortlistItems = [];

    // Load saved chats from localStorage
    function loadChatHistory() {
        try {
            const saved = localStorage.getItem('aiChatHistory');
            if (saved) {
                chatHistory = JSON.parse(saved);
                renderRecentChats();
            }
        } catch (e) {
            console.log('No saved chat history');
        }
    }

    // Save chats to localStorage
    function saveChatHistory() {
        try {
            localStorage.setItem('aiChatHistory', JSON.stringify(chatHistory));
            renderRecentChats();
        } catch (e) {
            console.log('Could not save chat history');
        }
    }

    // Render recent chats in sidebar
    function renderRecentChats() {
        if (chatHistory.length === 0) {
            aiRecentEmpty.style.display = 'block';
            aiRecentChats.innerHTML = '';
            return;
        }
        aiRecentEmpty.style.display = 'none';
        
        const recent = chatHistory.slice(-5).reverse();
        aiRecentChats.innerHTML = recent.map(chat => `
            <button class="ai-side-btn" style="font-size:.8rem; padding:.4rem .6rem;" 
                    onclick="loadChatConversation(${chat.id})">
                <i class="bi bi-chat-dots"></i> 
                ${chat.preview || chat.query.substring(0, 30) + '...'}
            </button>
        `).join('');
    }

    // Add a new chat to history
    function addToHistory(query, response) {
        const chat = {
            id: Date.now(),
            query: query,
            response: response,
            preview: query.substring(0, 40),
            location: currentLocation,
            timestamp: new Date().toISOString()
        };
        chatHistory.push(chat);
        saveChatHistory();
    }

    // Load a specific conversation
    window.loadChatConversation = function(id) {
        const chat = chatHistory.find(c => c.id === id);
        if (chat) {
            aiMessages.innerHTML = '';
            addAiMessage(chat.query, 'user');
            addAiMessage(chat.response, 'ai');
            // Update location if different
            if (chat.location && chat.location !== currentLocation) {
                currentLocation = chat.location;
                aiLocationLabel.textContent = currentLocation;
            }
        }
    };

    // Update AI status
    function setAIStatus(status, isTyping = false) {
        if (isTyping) {
            aiStatusDot.style.background = '#f5b942';
            aiStatusText.textContent = 'Thinking...';
            aiSendBtn.disabled = true;
        } else {
            aiStatusDot.style.background = '#22c55e';
            aiStatusText.textContent = status || 'Ready to help';
            aiSendBtn.disabled = false;
        }
    }

    // Add message to chat
    function addAiMessage(text, from, isHTML = false) {
        const wrap = document.createElement('div');
        wrap.className = 'ai-msg' + (from === 'user' ? ' user' : '');
        const avatar = from === 'user' ? 'You' : 'AI';
        
        let content = text;
        if (!isHTML) {
            content = text.replace(/\n/g, '<br>');
        }
        
        wrap.innerHTML = `
            <div class="avatar">${avatar}</div>
            <div class="bubble">${content}</div>
        `;
        aiMessages.appendChild(wrap);
        aiMessages.scrollTop = aiMessages.scrollHeight;
    }

    // Show typing indicator
    function showTypingIndicator() {
        const wrap = document.createElement('div');
        wrap.className = 'ai-msg';
        wrap.id = 'typingIndicator';
        wrap.innerHTML = `
            <div class="avatar">AI</div>
            <div class="bubble">
                <div class="typing-indicator">
                    <span></span><span></span><span></span>
                </div>
            </div>
        `;
        aiMessages.appendChild(wrap);
        aiMessages.scrollTop = aiMessages.scrollHeight;
    }

    // Remove typing indicator
    function removeTypingIndicator() {
        const indicator = document.getElementById('typingIndicator');
        if (indicator) indicator.remove();
    }

    // Generate AI response (simulated - replace with real API call)
    function generateAIResponse(query, location) {
        return new Promise((resolve) => {
            // Simulate API delay
            setTimeout(() => {
                const lowerQuery = query.toLowerCase();
                let response = '';

                // Check for property type
                const propertyTypes = ['apartment', 'house', 'villa', 'commercial', 'plot', 'farmland', 'land'];
                let propertyType = propertyTypes.find(type => lowerQuery.includes(type)) || 'property';
                
                // Check for city
                const cities = ['mumbai', 'delhi', 'bangalore', 'chennai', 'hyderabad', 'pune', 'kolkata', 'ahmedabad'];
                let cityFound = cities.find(c => lowerQuery.includes(c)) || location || 'your city';
                
                // Check for budget
                const budgetMatch = lowerQuery.match(/(?:under|below|less than|upto|up to)\s*([\d,.]+)\s*(cr|crore|lakh|k)/i);
                let budget = '';
                let budgetUnit = '';
                if (budgetMatch) {
                    budget = budgetMatch[1];
                    budgetUnit = budgetMatch[2];
                }

                // Check for bedrooms
                const bedroomMatch = lowerQuery.match(/(\d+)\s*(?:bhk|bedroom|bed room)/i);
                let bedrooms = bedroomMatch ? bedroomMatch[1] : '';

                // Generate contextual response
                if (lowerQuery.includes('hello') || lowerQuery.includes('hi') || lowerQuery.includes('hey')) {
                    response = `👋 Hello! I'm your AI property assistant. How can I help you find your dream ${propertyType} today? Feel free to ask about properties, locations, prices, or investment opportunities.`;
                }
                else if (lowerQuery.includes('thank')) {
                    response = `You're welcome! 😊 Is there anything else I can help you with? Whether it's finding properties, checking prices, or getting investment advice, I'm here to assist.`;
                }
                else if (lowerQuery.includes('help') || lowerQuery.includes('how')) {
                    response = `I can help you with:<br>
                        • Finding ${propertyType}s in ${cityFound}<br>
                        • Budget and price guidance<br>
                        • Investment opportunities<br>
                        • Property details and amenities<br>
                        • Location recommendations<br><br>
                        Just tell me what you're looking for! 🏠`;
                }
                else if (lowerQuery.includes('best area') || lowerQuery.includes('best location') || lowerQuery.includes('where to buy')) {
                    response = `🏙️ Some of the best areas to invest in ${cityFound} are:<br>
                        • <strong>Central Business District</strong> - Great for commercial and luxury properties<br>
                        • <strong>Emerging Suburbs</strong> - Excellent appreciation potential<br>
                        • <strong>Near IT Parks</strong> - High rental demand<br>
                        • <strong>Connectivity Hubs</strong> - Near metro stations and highways<br><br>
                        Would you like more details about any specific area?`;
                }
                else if (lowerQuery.includes('investment') || lowerQuery.includes('invest')) {
                    response = `📈 <strong>Investment Insights for ${cityFound}:</strong><br>
                        • Property prices have appreciated 12-18% in the last 2 years<br>
                        • Areas near new infrastructure projects show the highest ROI<br>
                        • Rental yields range from 3-5% in residential and 6-8% in commercial<br>
                        • Upcoming projects in ${cityFound} offer pre-launch discounts<br><br>
                        Would you like specific investment recommendations? 💰`;
                }
                else if (bedrooms && propertyType) {
                    response = `🏠 <strong>${bedrooms} BHK ${propertyType}s in ${cityFound}</strong><br><br>
                        Based on current market trends, here are some options:<br>
                        • <strong>Budget Range</strong>: Typically ₹${budget || '30-40'} Lakhs for economy, ₹${budget ? Math.round(parseFloat(budget) * 2) : '80'} Lakhs - ₹${budget ? Math.round(parseFloat(budget) * 3.5) : '1.5'} Cr for premium<br>
                        • <strong>Popular Localities</strong>: ${cityFound} has several options in your range<br>
                        • <strong>Features</strong>: Modern amenities, security, and good connectivity<br><br>
                        Would you like me to show you specific listings? 🔍`;
                }
                else if (propertyType) {
                    response = `🏘️ <strong>${propertyType.charAt(0).toUpperCase() + propertyType.slice(1)}s in ${cityFound}</strong><br><br>
                        Here's what's available:<br>
                        • <strong>Price Range</strong>: ₹${budget || '25'} Lakhs to ₹${budget ? Math.round(parseFloat(budget) * 2.5) : '3'} Crores<br>
                        • <strong>Options</strong>: Multiple configurations available<br>
                        • <strong>Location</strong>: Prime areas with good connectivity<br>
                        • <strong>Amenities</strong>: Parking, security, parks, and more<br><br>
                        Can you tell me more about your preferences? 🏡`;
                }
                else {
                    response = `🤔 I understand you're looking for information about ${propertyType}s in ${cityFound}.<br><br>
                        To give you the best recommendations, could you tell me:<br>
                        • What's your budget range?<br>
                        • How many bedrooms do you need?<br>
                        • Any specific locality preferences?<br>
                        • Are you looking for investment or self-use?<br><br>
                        This will help me find the perfect options for you! 🎯`;
                }

                resolve(response);
            }, 800 + Math.random() * 1200);
        });
    }

    // Main send message function
    async function sendMessage() {
        const text = aiInput.value.trim();
        if (text === '' || isProcessing) return;

        // Add user message
        addAiMessage(text, 'user');
        aiInput.value = '';
        isProcessing = true;
        setAIStatus('', true);

        // Show typing indicator
        showTypingIndicator();

        try {
            // Generate response (replace with real API call)
            const response = await generateAIResponse(text, currentLocation);
            
            // Remove typing indicator
            removeTypingIndicator();
            
            // Add AI response
            addAiMessage(response, 'ai', true);
            
            // Save to history
            addToHistory(text, response);
            
        } catch (error) {
            removeTypingIndicator();
            addAiMessage('Sorry, I encountered an error. Please try again.', 'ai');
            console.error('AI Error:', error);
        } finally {
            isProcessing = false;
            setAIStatus('Ready to help');
            aiInput.focus();
        }
    }

    // New search - clear chat
    function newSearch() {
        aiMessages.innerHTML = '';
        addAiMessage('👋 Hi! I\'m your AI property assistant. Tell me what you\'re looking for and I\'ll help you find the perfect property.', 'ai');
        setAIStatus('Ready to help');
        isProcessing = false;
        aiInput.focus();
    }

    // Shortlist toggle
    function toggleShortlist() {
        const lastMessage = aiMessages.querySelector('.ai-msg:last-child .bubble');
        if (lastMessage && !lastMessage.closest('.ai-msg')?.classList.contains('user')) {
            const text = lastMessage.textContent;
            const item = {
                text: text,
                timestamp: new Date().toISOString()
            };
            shortlistItems.push(item);
            alert('✅ Added to shortlist! View it in your shortlist.');
        } else {
            alert('💡 Please ask a question first, then shortlist the AI\'s response.');
        }
    }

    // Voice input (simulated)
    function startVoiceInput() {
        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            const recognition = new SpeechRecognition();
            recognition.lang = 'en-IN';
            recognition.continuous = false;
            recognition.interimResults = false;

            aiMicBtn.innerHTML = '<i class="bi bi-mic-fill" style="color:#f5b942;"></i>';
            
            recognition.onresult = function(event) {
                const transcript = event.results[0][0].transcript;
                aiInput.value = transcript;
                aiMicBtn.innerHTML = '<i class="bi bi-mic"></i>';
                sendMessage();
            };

            recognition.onerror = function() {
                aiMicBtn.innerHTML = '<i class="bi bi-mic"></i>';
                alert('Voice input failed. Please type your question instead.');
            };

            recognition.start();
        } else {
            alert('Voice input is not supported in your browser. Please type your question instead.');
        }
    }

    // ============ Event Listeners ============

    // Send button
    aiSendBtn.addEventListener('click', sendMessage);

    // Enter key
    aiInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // New search
    aiNewSearch.addEventListener('click', newSearch);

    // Voice input
    aiMicBtn.addEventListener('click', startVoiceInput);

    // Shortlist
    aiShortlistBtn.addEventListener('click', toggleShortlist);

    // Location change
    document.querySelectorAll('.ai-location-option').forEach(function(opt) {
        opt.addEventListener('click', function(e) {
            e.preventDefault();
            currentLocation = this.textContent;
            aiLocationLabel.textContent = currentLocation;
            // Show confirmation
            addAiMessage(`📍 Location changed to ${currentLocation}`, 'system');
        });
    });

    // Quick suggestion chips
    document.querySelectorAll('.ai-suggestion-chip').forEach(function(chip) {
        chip.addEventListener('click', function() {
            const suggestion = this.dataset.suggestion;
            aiInput.value = suggestion;
            sendMessage();
        });
    });

    // ============ Initialize ============
    loadChatHistory();
    setAIStatus('Ready to help');
    aiInput.focus();

    // Auto-focus when modal opens
    document.getElementById('aiAssistantModal').addEventListener('shown.bs.modal', function() {
        setTimeout(() => aiInput.focus(), 300);
    });

})();

// ============ Hero Banner Slider ============
(function () {
    const slides = <?php echo json_encode($slidesForJs); ?>;
    if (slides.length <= 1) return; // nothing to slide between

    const bg       = document.getElementById('heroBg');
    const titleEl   = document.getElementById('heroTitle');
    const subEl     = document.getElementById('heroSubtitle');
    const prevBtn   = document.getElementById('heroPrev');
    const nextBtn   = document.getElementById('heroNext');
    const dots      = document.querySelectorAll('#heroDots button');

    let current = 0;
    let timer = null;
    const AUTO_ADVANCE_MS = 4000;

    function goTo(index) {
        current = (index + slides.length) % slides.length;
        const slide = slides[current];

        bg.classList.add('fading');
        setTimeout(function () {
            bg.style.backgroundImage = `url('${slide.image}')`;
            titleEl.innerHTML = slide.title.replace(/\n/g, '<br>');
            subEl.textContent = slide.subtitle;
            bg.classList.remove('fading');
        }, 300);

        dots.forEach((d, i) => d.classList.toggle('active', i === current));
    }

    function next() { goTo(current + 1); }
    function prev() { goTo(current - 1); }

    function resetTimer() {
        if (timer) clearInterval(timer);
        timer = setInterval(next, AUTO_ADVANCE_MS);
    }

    if (prevBtn) prevBtn.addEventListener('click', function () { prev(); resetTimer(); });
    if (nextBtn) nextBtn.addEventListener('click', function () { next(); resetTimer(); });
    dots.forEach(dot => dot.addEventListener('click', function () {
        goTo(parseInt(this.dataset.index, 10));
        resetTimer();
    }));

    resetTimer();
})();

// ============ City Dropdown: City / Pincode mode ============
document.addEventListener("DOMContentLoaded", function () {
    // Must be set before any tab renders, since renderHeroSubTabs() — including
    // the initial call below for the default "Buy" tab — reads from it.
    window.HERO_TAB_SECTIONS = <?php echo json_encode($heroTabSections); ?>;

    const tabs = document.querySelectorAll(".tab-btn");
    const subTabs = document.querySelectorAll(".tab-btn-sub");
    const input = document.getElementById("heroSearch");

        function renderHeroSubTabs(type) {
        const subBar = document.getElementById("heroTabsBarSub");
        if (!subBar) return;
        const sections = (window.HERO_TAB_SECTIONS && window.HERO_TAB_SECTIONS[type]) || [];

        if (!type || sections.length === 0) {
            subBar.innerHTML = "";
            updateSubTabsScrollUI();
            return;
        }

        subBar.innerHTML = sections.map(function (sec, i) {
            return '<button type="button" class="tab-btn-sub' + (i === 0 ? ' active' : '') +
                   '" data-placeholder="' + sec.name.replace(/"/g, '&quot;') + '" data-type="' + sec.name.replace(/"/g, '&quot;') + '">' +
                   sec.name + '</button>';
        }).join('');

        subBar.querySelectorAll(".tab-btn-sub").forEach(function (btn) {
            btn.addEventListener("click", function () {
                subBar.querySelectorAll(".tab-btn-sub").forEach(b => b.classList.remove("active"));
                this.classList.add("active");
                input.placeholder = "Search " + this.dataset.placeholder;
            });
        });

        updateSubTabsScrollUI();
    }

    // ============ Hero sub-tabs bar: scroll arrows + fade indicators ============
    // Declared as a top-level function (not inside an IIFE) so it's hoisted and
    // callable from renderHeroSubTabs() above, including on the initial render.
    function updateSubTabsScrollUI() {
        const subBar   = document.getElementById("heroTabsBarSub");
        const subBtnL  = document.getElementById("heroTabsSubScrollLeft");
        const subBtnR  = document.getElementById("heroTabsSubScrollRight");
        const subFadeL = document.getElementById("heroTabsSubFadeLeft");
        const subFadeR = document.getElementById("heroTabsSubFadeRight");
        if (!subBar || !subBtnL || !subBtnR || !subFadeL || !subFadeR) return;

        const maxScroll = subBar.scrollWidth - subBar.clientWidth;
        const atStart = subBar.scrollLeft <= 2;
        const atEnd   = subBar.scrollLeft >= maxScroll - 2;
        const canScroll = maxScroll > 4;

        subBtnL.classList.toggle("show", canScroll && !atStart);
        subBtnR.classList.toggle("show", canScroll && !atEnd);
        subFadeL.classList.toggle("show", canScroll && !atStart);
        subFadeR.classList.toggle("show", canScroll && !atEnd);
    }

    tabs.forEach(tab => {
        tab.addEventListener("click", function () {
            if (this.dataset.newTab === "1" && this.dataset.url) {
                window.open(this.dataset.url, "_blank");
                return;
            }
            tabs.forEach(btn => btn.classList.remove("active"));
            this.classList.add("active");
            input.placeholder = "Search " + this.dataset.placeholder;
            renderHeroSubTabs(this.dataset.type);
        });
    });

    // Render sub-tabs for whichever tab is active by default (Buy)
    const initialActiveTab = document.querySelector(".tab-btn.active");
    if (initialActiveTab) renderHeroSubTabs(initialActiveTab.dataset.type);

    // ============ Hero tabs bar: scroll arrows + fade indicators ============
    (function () {
        const bar   = document.getElementById("heroTabsBar");
        const btnL  = document.getElementById("heroTabsScrollLeft");
        const btnR  = document.getElementById("heroTabsScrollRight");
        const fadeL = document.getElementById("heroTabsFadeLeft");
        const fadeR = document.getElementById("heroTabsFadeRight");
        if (!bar || !btnL || !btnR || !fadeL || !fadeR) return;

        function updateScrollUI() {
            const maxScroll = bar.scrollWidth - bar.clientWidth;
            const atStart = bar.scrollLeft <= 2;
            const atEnd   = bar.scrollLeft >= maxScroll - 2;
            const canScroll = maxScroll > 4;

            btnL.classList.toggle("show", canScroll && !atStart);
            btnR.classList.toggle("show", canScroll && !atEnd);
            fadeL.classList.toggle("show", canScroll && !atStart);
            fadeR.classList.toggle("show", canScroll && !atEnd);
        }

        btnL.addEventListener("click", () => bar.scrollBy({ left: -180, behavior: "smooth" }));
        btnR.addEventListener("click", () => bar.scrollBy({ left: 180, behavior: "smooth" }));
        bar.addEventListener("scroll", updateScrollUI);
        window.addEventListener("resize", updateScrollUI);

        updateScrollUI();
    })();

    (function () {
        const subBar  = document.getElementById("heroTabsBarSub");
        const subBtnL = document.getElementById("heroTabsSubScrollLeft");
        const subBtnR = document.getElementById("heroTabsSubScrollRight");
        if (!subBar || !subBtnL || !subBtnR) return;

        subBtnL.addEventListener("click", () => subBar.scrollBy({ left: -160, behavior: "smooth" }));
        subBtnR.addEventListener("click", () => subBar.scrollBy({ left: 160, behavior: "smooth" }));
        subBar.addEventListener("scroll", updateSubTabsScrollUI);
        window.addEventListener("resize", updateSubTabsScrollUI);
    })();

    // ============ Enhanced City Search with Autocomplete ============
    const citySearchInput = document.getElementById('heroCitySearch');
    const cityList = document.getElementById('heroCityList');
    const cityOptions = cityList ? cityList.querySelectorAll('.city-option') : [];
    let allCities = [];
    
    // Store all city names
    cityOptions.forEach(option => {
        allCities.push({
            name: option.textContent.trim(),
            element: option
        });
    });
    
    if (citySearchInput) {
        citySearchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            
            // Show all cities if query is empty
            if (query === '') {
                cityOptions.forEach(option => {
                    option.style.display = '';
                    // Restore original text
                    const cityName = option.dataset.city || option.textContent.trim();
                    option.innerHTML = cityName;
                });
                // Remove any "no results" message
                const noResults = cityList.querySelector('.hero-city-no-results');
                if (noResults) noResults.remove();
                return;
            }
            
            let matchCount = 0;
            cityOptions.forEach(option => {
                const cityName = option.textContent.toLowerCase();
                const cityData = option.dataset.city || '';
                const isMatch = cityName.includes(query) || 
                               cityData.toLowerCase().includes(query);
                
                if (isMatch) {
                    option.style.display = '';
                    matchCount++;
                    // Highlight matching text
                    const text = option.textContent;
                    const regex = new RegExp(query, 'gi');
                    const highlighted = text.replace(regex, match => 
                        `<span class="highlight">${match}</span>`
                    );
                    option.innerHTML = highlighted;
                } else {
                    option.style.display = 'none';
                }
            });
            
            // Show "no results" message
            const existingNoResults = cityList.querySelector('.hero-city-no-results');
            if (matchCount === 0) {
                if (!existingNoResults) {
                    const noResults = document.createElement('li');
                    noResults.className = 'hero-city-no-results';
                    noResults.innerHTML = '<i class="bi bi-search"></i> No cities found';
                    cityList.appendChild(noResults);
                }
            } else {
                if (existingNoResults) existingNoResults.remove();
            }
        });
        
        // Handle keyboard navigation
        citySearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                const visibleOptions = cityList.querySelectorAll('.city-option[style*="display: block"], .city-option:not([style*="display: none"])');
                if (visibleOptions.length === 0) return;
                
                let currentIndex = -1;
                visibleOptions.forEach((opt, idx) => {
                    if (opt === document.activeElement) currentIndex = idx;
                });
                
                let newIndex = currentIndex;
                if (e.key === 'ArrowDown') {
                    newIndex = (currentIndex + 1) % visibleOptions.length;
                } else {
                    newIndex = (currentIndex - 1 + visibleOptions.length) % visibleOptions.length;
                }
                
                visibleOptions[newIndex].focus();
            }
            
            if (e.key === 'Enter') {
                e.preventDefault();
                const activeElement = document.activeElement;
                if (activeElement && activeElement.classList.contains('city-option')) {
                    activeElement.click();
                }
            }
        });
    }

    // City / Pincode buttons
    const heroCityModeBtnMain  = document.getElementById("heroCityModeBtnMain");
    const heroPincodeModeBtnMain = document.getElementById("heroPincodeModeBtnMain");
    const heroCityPinPanel     = document.getElementById("heroCityPinPanel");
    const heroCityListEl       = document.getElementById("heroCityList");
    const heroCityPincodeWrap  = document.getElementById("heroCityPincodeWrap");
    const heroCityPincodeInput = document.getElementById("heroCityPincode");
    const heroCityPincodeGo    = document.getElementById("heroCityPincodeGo");
    const heroCityPincodeStatus = document.getElementById("heroCityPincodeStatus");
    window.selectedHeroCity = "All India";

    function openCityPinPanel(mode) {
    const isPincode = mode === "pincode";
    heroCityModeBtnMain.classList.toggle("active", !isPincode);
    heroPincodeModeBtnMain.classList.toggle("active", isPincode);
    
    // Show/hide city mode content (includes search + city list)
    const cityModeContent = document.getElementById("heroCityModeContent");
    if (cityModeContent) {
        cityModeContent.style.display = isPincode ? "none" : "block";
    }
    
    // Show/hide pincode content
    if (heroCityPincodeWrap) {
        heroCityPincodeWrap.style.display = isPincode ? "block" : "none";
    }
    
    heroCityPinPanel.classList.add("open");
    
    if (isPincode && heroCityPincodeInput) {
        setTimeout(() => heroCityPincodeInput.focus(), 50);
    } else if (!isPincode && citySearchInput) {
        setTimeout(() => citySearchInput.focus(), 100);
    }
}

    function setHeroCitySelection(cityName) {
        window.selectedHeroCity = cityName;
        const labelSpan = heroCityModeBtnMain.querySelector(".label-text");
        if (labelSpan) labelSpan.textContent = cityName;
        heroCityPinPanel.classList.remove("open");
        // Focus on main search input
        document.getElementById("heroSearch").focus();
    }

    heroCityModeBtnMain.addEventListener("click", function (e) {
        e.stopPropagation();
        if (heroCityPinPanel.classList.contains("open") && heroCityModeBtnMain.classList.contains("active")) {
            heroCityPinPanel.classList.remove("open");
        } else {
            openCityPinPanel("name");
        }
    });
    heroPincodeModeBtnMain.addEventListener("click", function (e) {
        e.stopPropagation();
        openCityPinPanel("pincode");
    });

    document.addEventListener("click", function (e) {
        if (!heroCityPinPanel.contains(e.target) && e.target !== heroCityModeBtnMain && e.target !== heroPincodeModeBtnMain) {
            heroCityPinPanel.classList.remove("open");
        }
    });

    document.querySelectorAll(".city-option").forEach(function (opt) {
        opt.addEventListener("click", function (e) {
            e.preventDefault();
            setHeroCitySelection(this.dataset.city || this.textContent.trim());
        });
    });

    // Handle quick city buttons
    document.querySelectorAll('.quick-city-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const city = this.dataset.city;
            setHeroCitySelection(city);
            // Update the city button text
            const cityBtn = document.getElementById('heroCityModeBtnMain');
            const labelSpan = cityBtn.querySelector('.label-text');
            if (labelSpan) labelSpan.textContent = city;
            // Focus on search input
            document.getElementById('heroSearch').focus();
        });
    });

    function heroLookupPincode() {
        const pin = (heroCityPincodeInput.value || "").trim();
        if (!/^\d{6}$/.test(pin)) {
            heroCityPincodeStatus.textContent = "Please enter a valid 6-digit pincode.";
            heroCityPincodeStatus.classList.remove("success");
            heroCityPincodeStatus.classList.add("error");
            return;
        }
        heroCityPincodeStatus.textContent = "Looking up...";
        heroCityPincodeStatus.classList.remove("error", "success");
        heroCityPincodeGo.disabled = true;

        fetch("includes/pincode-lookup.php?pincode=" + encodeURIComponent(pin))
            .then(res => res.json())
            .then(function (data) {
                if (data && data.success && data.city) {
                    heroCityPincodeStatus.textContent = "Found: " + data.city;
                    heroCityPincodeStatus.classList.remove("error");
                    heroCityPincodeStatus.classList.add("success");
                    setHeroCitySelection(data.city);
                } else {
                    heroCityPincodeStatus.textContent = (data && data.message) || "No city found for that pincode.";
                    heroCityPincodeStatus.classList.remove("success");
                    heroCityPincodeStatus.classList.add("error");
                }
            })
            .catch(function () {
                heroCityPincodeStatus.textContent = "Could not look up that pincode. Please try again.";
                heroCityPincodeStatus.classList.remove("success");
                heroCityPincodeStatus.classList.add("error");
            })
            .finally(function () {
                heroCityPincodeGo.disabled = false;
            });
    }

    if (heroCityPincodeGo) heroCityPincodeGo.addEventListener("click", heroLookupPincode);
    if (heroCityPincodeInput) {
        heroCityPincodeInput.addEventListener("keydown", function (e) {
            if (e.key === "Enter") { e.preventDefault(); heroLookupPincode(); }
        });
        heroCityPincodeInput.addEventListener("input", function () {
            this.value = this.value.replace(/\D/g, "").slice(0, 6);
        });
    }
});

// ============ Dark Mode - Listen for theme changes ============
(function(){
    // Function to handle dark mode transitions
    function handleThemeChange() {
        const isDark = document.body.classList.contains('dark-mode');
        // Any additional theme-specific adjustments can be added here
    }

    // Listen for theme toggle events from the header
    document.addEventListener('themeChanged', function(e) {
        handleThemeChange();
    });

    // Initial check
    handleThemeChange();

    // Watch for class changes on body (for dark mode)
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