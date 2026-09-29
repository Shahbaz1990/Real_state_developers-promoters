<?php
/**
 * Site header — Mobile-first responsive header
 * Fixed session_start() warning issue
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($site)) { 
    require_once __DIR__ . '/config.php'; 
}
// Header settings
// NOTE: register_btn_text / register_btn_text_color / register_btn_bg_color
// require the header_settings_migration.sql to have been run (adds 3 columns
// to header_settings). Until then these simply fall back to the defaults below.
$headerSettings = [
    'logo'                    => '',
    'site_title'              => $site['name'] ?? 'Tatito Developers',
    'header_bg_color'         => '#141d4a',
    'nav_link_color'          => '#eef1fb',
    'login_btn_text'          => 'Login',
    'login_btn_text_color'    => '#ffffff',
    'login_btn_bg_color'      => 'transparent',
    'register_btn_text'       => 'Register',
    'register_btn_text_color' => '#12213a',
    'register_btn_bg_color'   => '#f0b429',
];
$hsResult = $conn->query("SELECT * FROM header_settings ORDER BY id DESC LIMIT 1");
if ($hsResult && $hsResult->num_rows > 0) {
    $headerSettings = array_merge($headerSettings, $hsResult->fetch_assoc());
}
$baseURL = "/";
$headerLogoPath = !empty($headerSettings['logo'])
    ? $baseURL . "admin/uploads/logos/" . $headerSettings['logo']
    : $baseURL . "front/assets/images/logo.jpeg";

// Cities
$allCitiesFull = [];
$cityFullRes = $conn->query("SELECT city_name FROM cities WHERE status='Active' ORDER BY city_order ASC, city_name ASC");
if ($cityFullRes) {
    while ($c = $cityFullRes->fetch_assoc()) {
        $allCitiesFull[] = $c['city_name'];
    }
}
$topCitiesForPicker = array_slice($allCitiesFull, 0, 12);
$otherCitiesForPicker = array_slice($allCitiesFull, 12);
sort($otherCitiesForPicker);
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root {
    --sy-header-live-bg: <?php echo htmlspecialchars($headerSettings['header_bg_color']); ?>;
    --sy-ink: #12213a;
    --sy-muted: #6b7686;
    --sy-blue: #1f6feb;
    --sy-gold: #f0b429;
    --sy-gold-dark: #d99b00;
    --sy-line: #e7e9ee;
    --sy-bg-soft: #f7f8fa;
    --sy-navy-1: #0c1230;
    --sy-navy-2: #141d4a;
    --sy-navy-3: #1c2b63;
    
    /* Dark mode variables */
    --dm-bg-primary: #0d1117;
    --dm-bg-secondary: #161b22;
    --dm-bg-tertiary: #1c2333;
    --dm-text-primary: #f0f6fc;
    --dm-text-secondary: #8b949e;
    --dm-border: #30363d;
    --dm-hover: #1f2937;
}

/* Dark mode styles - applied to body when .dark-mode is present */
body.dark-mode {
    background-color: var(--dm-bg-primary);
    color: var(--dm-text-primary);
}

body.dark-mode .sy-header {
    background: var(--dm-bg-secondary) !important;
}

body.dark-mode .sy-header-dark-wrap {
    background: var(--dm-bg-secondary) !important;
}

body.dark-mode .sy-topbar {
    background: var(--dm-bg-tertiary) !important;
}

body.dark-mode .sy-mainbar {
    background: var(--dm-bg-secondary) !important;
}

body.dark-mode .sy-brand-panel .brand-name-dark {
    color: var(--dm-text-primary);
}

body.dark-mode .ticker-item {
    color: var(--dm-text-secondary);
}

body.dark-mode .ticker-item .city {
    color: var(--dm-text-primary);
}

body.dark-mode .ticker-wrap {
    background: var(--dm-bg-tertiary);
}

body.dark-mode .sy-topbar .topbar-tools .font-size-switch {
    background: rgba(255,255,255,0.05);
    border-color: var(--dm-border);
}

body.dark-mode .sy-topbar .topbar-tools .font-size-switch button {
    color: var(--dm-text-secondary);
}

body.dark-mode .sy-topbar .topbar-tools .font-size-switch button:hover,
body.dark-mode .sy-topbar .topbar-tools .font-size-switch button.active {
    background: rgba(255,255,255,0.1);
    color: var(--dm-text-primary);
}

body.dark-mode .sy-topbar .lang-switch-toggle {
    background: rgba(255,255,255,0.05);
    border-color: var(--dm-border);
    color: var(--dm-text-primary);
}

body.dark-mode .sy-topbar .lang-switch-toggle:hover,
body.dark-mode .sy-topbar .lang-switch-toggle.show {
    background: rgba(255,255,255,0.1);
}

body.dark-mode .sy-topbar .lang-switch-menu {
    background: var(--dm-bg-secondary);
    border-color: var(--dm-border);
}

body.dark-mode .sy-topbar .lang-switch-menu .dropdown-item {
    color: var(--dm-text-secondary);
}

body.dark-mode .sy-topbar .lang-switch-menu .dropdown-item:hover {
    background: var(--dm-hover);
    color: var(--dm-text-primary);
}

body.dark-mode .sy-topbar .lang-switch-menu .dropdown-item.active {
    background: var(--dm-hover);
    color: var(--dm-text-primary);
}

body.dark-mode .sy-topbar .wishlist-btn {
    border-color: var(--dm-border);
    color: var(--dm-text-primary);
}

body.dark-mode .sy-topbar .wishlist-btn:hover {
    border-color: #ff6b95;
    color: #ff6b95;
}

body.dark-mode .sy-mainbar .navbar-nav .nav-link {
    color: var(--dm-text-secondary) !important;
}

body.dark-mode .sy-mainbar .navbar-nav .nav-link:hover {
    color: var(--sy-gold) !important;
}

body.dark-mode .sy-mainbar .navbar-toggler {
    background: rgba(255,255,255,0.05) !important;
    border-color: var(--dm-border) !important;
}

body.dark-mode .sy-mainbar .navbar-collapse {
    background: var(--dm-bg-secondary);
    border-color: var(--dm-border);
}

body.dark-mode .mega-menu {
    --bs-dropdown-bg: var(--dm-bg-tertiary) !important;
    background: var(--dm-bg-tertiary) !important;
}

body.dark-mode .sy-mainbar .navbar-nav .dropdown-item {
    color: var(--dm-text-secondary) !important;
}

body.dark-mode .sy-mainbar .navbar-nav .dropdown-item:hover {
    background: var(--dm-hover);
    color: var(--dm-text-primary) !important;
}

/* ===== FIXED: CITY PICKER DARK MODE VISIBILITY ===== */
body.dark-mode .sy-city-picker-wrap .city-picker-toggle {
    background: rgba(255,255,255,0.10);
    border-color: var(--dm-border);
    color: #ffffff;
}

body.dark-mode .sy-city-picker-wrap .city-picker-toggle i {
    color: #ffffff;
}

body.dark-mode .sy-city-picker-wrap .city-picker-toggle:hover {
    background: rgba(255,255,255,0.18);
    border-color: rgba(255,255,255,0.3);
}

body.dark-mode .sy-city-picker-wrap .city-picker-toggle:focus,
body.dark-mode .sy-city-picker-wrap .city-picker-toggle.show {
    color: #ffffff;
    border-color: rgba(255,255,255,0.4);
    background: rgba(255,255,255,0.18);
}

body.dark-mode .city-picker-menu {
    background: var(--dm-bg-secondary);
    border-color: var(--dm-border);
}

body.dark-mode .city-picker-search {
    background: var(--dm-bg-secondary);
    border-color: var(--dm-border);
}

body.dark-mode .city-picker-search .input-wrap {
    background: var(--dm-bg-tertiary);
}

body.dark-mode .city-picker-search input {
    color: var(--dm-text-primary);
    background: transparent;
}

body.dark-mode .city-picker-search input::placeholder {
    color: var(--dm-text-secondary);
}

body.dark-mode .city-picker-mode-switch button {
    background: var(--dm-bg-tertiary);
    border-color: var(--dm-border);
    color: var(--dm-text-secondary);
}

body.dark-mode .city-picker-mode-switch button.active {
    background: var(--sy-gold);
    border-color: var(--sy-gold);
    color: var(--sy-navy-1);
}

body.dark-mode .city-picker-section-label {
    background: var(--dm-bg-tertiary);
    color: var(--dm-text-secondary);
}

body.dark-mode .city-picker-top-grid button {
    color: var(--dm-text-primary);
}

body.dark-mode .city-picker-top-grid button:hover {
    background: var(--dm-hover);
}

body.dark-mode .city-picker-other-list button {
    color: var(--dm-text-primary);
    border-color: var(--dm-border);
}

body.dark-mode .city-picker-other-list button:hover {
    background: var(--dm-hover);
}

body.dark-mode .city-picker-actions .detect-loc {
    color: var(--dm-text-primary);
}

body.dark-mode .city-picker-actions .reset-city {
    color: var(--sy-gold);
}

body.dark-mode .sy-nav-actions .btn-login {
    color: var(--dm-text-primary);
}

body.dark-mode .sy-nav-actions .btn-login:hover {
    background: rgba(255,255,255,0.05);
    border-color: var(--dm-border);
}

body.dark-mode .sy-nav-actions .btn-register {
    background: var(--sy-gold);
    color: var(--sy-navy-1);
}

body.dark-mode .sy-nav-actions .btn-register:hover {
    background: var(--sy-gold-dark);
}

body.dark-mode .wishlist-offcanvas {
    background: var(--dm-bg-secondary);
}

body.dark-mode .wishlist-offcanvas .offcanvas-header {
    background: var(--dm-bg-tertiary);
    border-color: var(--dm-border);
}

body.dark-mode .wishlist-offcanvas .offcanvas-title {
    color: var(--dm-text-primary);
}

body.dark-mode .wishlist-offcanvas .offcanvas-subtitle {
    color: var(--dm-text-secondary);
}

body.dark-mode .wishlist-tab-switch {
    background: var(--dm-bg-tertiary);
}

body.dark-mode .wishlist-tab-switch .wishlist-tab-btn {
    color: var(--dm-text-secondary);
}

body.dark-mode .wishlist-tab-switch .wishlist-tab-btn.active {
    background: var(--dm-hover);
    color: var(--dm-text-primary);
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
}

body.dark-mode .wishlist-card {
    border-color: var(--dm-border);
}

body.dark-mode .wishlist-card .info h6 {
    color: var(--dm-text-primary);
}

body.dark-mode .wishlist-card .info .price {
    color: var(--dm-text-primary);
}

body.dark-mode .wishlist-bottom-bar {
    border-color: var(--dm-border);
}

body.dark-mode .wishlist-bottom-bar .btn-enquire {
    background: var(--dm-bg-tertiary);
    color: var(--dm-text-primary);
    border-color: var(--dm-border);
}

body.dark-mode .wishlist-bottom-bar .btn-compare {
    background: var(--sy-gold);
    color: var(--sy-navy-1);
}

body.dark-mode .wishlist-bottom-bar .btn-compare:disabled {
    background: var(--dm-bg-tertiary);
    color: var(--dm-text-secondary);
}

body.dark-mode #enquiryModal .modal-content {
    background: var(--dm-bg-secondary);
    color: var(--dm-text-primary);
}

body.dark-mode #enquiryModal .modal-header {
    border-color: var(--dm-border);
}

body.dark-mode #enquiryModal .form-control,
body.dark-mode #enquiryModal .form-select {
    background: var(--dm-bg-tertiary);
    border-color: var(--dm-border);
    color: var(--dm-text-primary);
}

body.dark-mode #enquiryModal .form-control:focus,
body.dark-mode #enquiryModal .form-select:focus {
    background: var(--dm-bg-tertiary);
    border-color: var(--sy-gold);
    color: var(--dm-text-primary);
}

body.dark-mode #enquiryModal .form-control::placeholder {
    color: var(--dm-text-secondary);
}

body.dark-mode #enquiryModal .form-check-label {
    color: var(--dm-text-secondary);
}

body.dark-mode #enquiryModal .btn-submit-enquiry {
    background: var(--sy-gold);
    color: var(--sy-navy-1);
}

body.dark-mode #enquiryModal .btn-submit-enquiry:hover {
    background: var(--sy-gold-dark);
}

body.dark-mode #enquiryModal .btn-outline-primary {
    border-color: var(--dm-border);
    color: var(--dm-text-primary);
}

body.dark-mode #enquiryModal .btn-outline-primary:hover {
    background: var(--dm-hover);
}

body.dark-mode .offcanvas {
    background: var(--dm-bg-secondary);
    color: var(--dm-text-primary);
}

body.dark-mode .offcanvas-header {
    border-color: var(--dm-border);
}

body.dark-mode .dropdown-menu {
    background: var(--dm-bg-secondary);
    border-color: var(--dm-border);
}

body.dark-mode .dropdown-item {
    color: var(--dm-text-secondary);
}

body.dark-mode .dropdown-item:hover {
    background: var(--dm-hover);
    color: var(--dm-text-primary);
}

body.dark-mode .dropdown-divider {
    border-color: var(--dm-border);
}

body.dark-mode .dropdown-item-text {
    color: var(--dm-text-primary) !important;
}
body.dark-mode .seller-advice-card {
    background: var(--dm-bg-secondary);
    border-color: var(--dm-border);
}
body.dark-mode .seller-advice-card .dropdown-item {
    color: var(--dm-text-secondary);
}
body.dark-mode .seller-advice-card .dropdown-item:hover {
    background-color: var(--dm-hover);
    color: var(--dm-text-primary);
}
body.dark-mode .modal-content {
    background: var(--dm-bg-secondary);
    border-color: var(--dm-border);
}

body.dark-mode .modal-header {
    border-color: var(--dm-border);
}

body.dark-mode .modal-footer {
    border-color: var(--dm-border);
}

body.dark-mode .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
}

body.dark-mode .text-muted {
    color: var(--dm-text-secondary) !important;
}

/* Dark mode toggle button styles */
.sy-topbar .theme-toggle-btn {
    width: 28px;
    height: 28px;
    flex-shrink: 0;
    background: transparent;
    border: 1.5px solid rgba(255,255,255,.28);
    border-radius: 50%;
    color: #fff;
    font-size: 0.7rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    position: relative;
    cursor: pointer;
    transition: all 0.3s ease;
}

.sy-topbar .theme-toggle-btn:hover {
    border-color: var(--sy-gold);
    color: var(--sy-gold);
    transform: rotate(20deg);
}

body.dark-mode .sy-topbar .theme-toggle-btn {
    border-color: var(--dm-border);
    color: var(--dm-text-primary);
}

body.dark-mode .sy-topbar .theme-toggle-btn:hover {
    border-color: var(--sy-gold);
    color: var(--sy-gold);
}

@media (min-width: 1400px) {
    .sy-topbar .theme-toggle-btn {
        width: 32px;
        height: 32px;
        font-size: 0.85rem;
    }
}

@media (max-width: 576px) {
    .sy-topbar .theme-toggle-btn {
        width: 24px;
        height: 24px;
        font-size: 0.55rem;
    }
}

/* ===== BASE HEADER ===== */
.sy-header {
    background: <?php echo htmlspecialchars($headerSettings['header_bg_color']); ?>;
    position: sticky;
    top: 0;
    z-index: 1030;
    box-shadow: 0 2px 14px rgba(18,33,58,.06);
    /* overflow-x: clip;  <-- delete this line */
}

.sy-header::before {
    content: "";
    display: block;
    height: 3px;
    width: 100%;
    background: linear-gradient(90deg, var(--sy-navy-1), var(--sy-blue), var(--sy-gold));
}

.sy-header-dark-wrap {
    background: var(--sy-navy-2);
    box-shadow: 0 4px 18px rgba(0,0,0,.28);
}
.sy-header-dark-wrap::before { display: none; }
    .sy-header-inner {
    display: flex;
    align-items: stretch;
    flex-wrap: wrap;
    width: 100%;
    max-width: 1600px;
    margin: 0 auto;
}

/* ===== BRAND PANEL ===== */
.sy-brand-panel {
    width: 100px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 0.3rem;
    text-align: center;
    text-decoration: none;
}
.sy-brand-panel:hover { text-decoration: none; }
.sy-brand-panel .brand-icon-ring {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.sy-brand-panel .brand-icon-ring img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 0;
}
.sy-brand-panel .brand-name-dark {
    color: #fff;
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    font-size: 0.55rem;
    letter-spacing: .04em;
    text-transform: uppercase;
    margin-top: 0.35rem;
    line-height: 1.2;
}

.sy-header-right {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
    max-width: 100%;
}

/* ===== TOP BAR ===== */
.sy-topbar {
    background: linear-gradient(90deg, var(--sy-navy-1), var(--sy-navy-3));
    color: rgba(255,255,255,.78);
    padding: 0.15rem 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    position: relative;
    z-index: 5;
}

.ticker-wrap {
    flex: 1;
    min-width: 0;
    overflow: hidden;
    padding: 0.2rem 0;
}
.ticker-track {
    display: inline-flex;
    white-space: nowrap;
    animation: sy-ticker-scroll 70s linear infinite;
}
.ticker-wrap:hover .ticker-track { animation-play-state: paused; }
.ticker-item {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0 0.6rem;
    font-size: 0.6rem;
    color: rgba(255,255,255,.85);
    border-right: 1px solid rgba(255,255,255,.15);
}
.ticker-item .city { font-weight: 700; color: #fff; }
.ticker-item .up { color: #3ddc84; font-weight: 600; }
@keyframes sy-ticker-scroll {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

.sy-topbar .wishlist-btn {
    flex-shrink: 0;
    width: 28px;
    height: 28px;
    background: transparent;
    border: 1.5px solid rgba(255,255,255,.28);
    border-radius: 50%;
    color: #fff;
    font-size: 0.7rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    position: relative;
}
.sy-topbar .wishlist-btn:hover { border-color: #ff6b95; color: #ff6b95; }
.sy-topbar .wishlist-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #e0245e;
    color: #fff;
    font-size: 0.5rem;
    font-weight: 700;
    min-width: 14px;
    height: 14px;
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 0 3px;
    line-height: 1;
}
.sy-topbar .wishlist-badge.show { display: flex; }

/* ===== TOPBAR TOOLS: font-size adjuster + language switcher ===== */
.sy-topbar .topbar-tools {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.sy-topbar .font-size-switch {
    display: flex;
    align-items: center;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.22);
    border-radius: 999px;
    overflow: hidden;
    flex-shrink: 0;
}
.sy-topbar .font-size-switch button {
    background: transparent;
    border: none;
    color: rgba(255,255,255,.85);
    font-weight: 700;
    padding: 0.2rem 0.45rem;
    font-size: 0.6rem;
    line-height: 1.4;
    white-space: nowrap;
}
.sy-topbar .font-size-switch button:not(:last-child) {
    border-right: 1px solid rgba(255,255,255,.15);
}
.sy-topbar .font-size-switch button:hover,
.sy-topbar .font-size-switch button.active {
    background: rgba(255,255,255,.2);
    color: #fff;
}
.sy-topbar .font-size-switch button[data-size="sm"] { font-size: 0.55rem; }
.sy-topbar .font-size-switch button[data-size="md"] { font-size: 0.65rem; }
.sy-topbar .font-size-switch button[data-size="lg"] { font-size: 0.75rem; }

/* ===== GOOGLE TRANSLATE CUSTOM STYLES ===== */
.sy-topbar .lang-switch-wrap { 
    position: relative; 
    flex-shrink: 0; 
}
.sy-topbar .lang-switch-toggle {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.22);
    border-radius: 999px;
    color: #fff;
    font-size: 0.6rem;
    font-weight: 600;
    padding: 0.22rem 0.6rem;
    white-space: nowrap;
    cursor: pointer;
}
.sy-topbar .lang-switch-toggle:hover,
.sy-topbar .lang-switch-toggle.show {
    background: rgba(255,255,255,.2);
}
.sy-topbar .lang-switch-toggle i.bi-globe2 { font-size: 0.7rem; }
.sy-topbar .lang-switch-toggle::after { vertical-align: middle; }

/* Hide Google Translate's default UI but keep it functional */
.goog-te-gadget {
    display: inline-block !important;
    font-size: 0 !important;
    line-height: 0 !important;
    opacity: 0 !important;
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    width: 0 !important;
    height: 0 !important;
    overflow: hidden !important;
    pointer-events: none !important;
}
.goog-te-gadget-simple {
    display: none !important;
}
.goog-logo-link {
    display: none !important;
}
.goog-te-gadget img {
    display: none !important;
}

/* Google Translate iframe - hide the default top bar */
.goog-te-banner-frame {
    display: none !important;
}
.goog-te-menu-frame {
    max-height: 400px !important;
    overflow-y: auto !important;
}
body {
    top: 0 !important;
}

/* Language dropdown menu styles */
.sy-topbar .lang-switch-menu {
    min-width: 180px;
    padding: 0.35rem;
    border-radius: 0.6rem;
    border: 1px solid var(--sy-line);
    box-shadow: 0 12px 30px rgba(18,33,58,.15);
    max-height: 400px;
    overflow-y: auto;
    background: #fff;
}
.sy-topbar .lang-switch-menu .dropdown-item {
    font-size: 0.85rem;
    border-radius: 0.4rem;
    padding: 0.45rem 0.6rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    cursor: pointer;
    color: #333;
    text-decoration: none;
}
.sy-topbar .lang-switch-menu .dropdown-item .bi-check2 {
    color: var(--sy-blue);
    display: none;
}
.sy-topbar .lang-switch-menu .dropdown-item.active .bi-check2 { display: inline-block; }
.sy-topbar .lang-switch-menu .dropdown-item.active,
.sy-topbar .lang-switch-menu .dropdown-item:hover {
    background: var(--sy-bg-soft);
    color: var(--sy-ink);
}
.sy-topbar .lang-switch-menu .dropdown-divider {
    margin: 0.3rem 0;
}

/* ===== MAIN NAV BAR ===== */
.sy-mainbar {
    background: var(--sy-navy-2);
    padding: 0.3rem 0.5rem;
}
.sy-mainbar .navbar {
    padding: 0;
}
.sy-mainbar .navbar-toggler {
    background: rgba(255,255,255,0.08) !important;
    border: 1px solid rgba(255,255,255,0.2) !important;
    padding: 0.4rem 0.6rem;
    border-radius: 0.4rem;
}
.sy-mainbar .navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255,255,255,1)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
}

/* ===== NAV ACTIONS ===== */
.sy-nav-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    flex: 0 0 auto;
    margin-left: auto;
    min-width: 0;
}

.sy-city-picker-wrap {
    position: relative;
    flex: 0 0 auto;
    width: 200px;
}

.sy-city-picker-wrap .city-picker-toggle {
    width: 60%;
    min-width: 0;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 0 14px;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.20);
    border-radius: 45px;
    color: #fff;
    font-size: .90rem;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    box-shadow: none;    margin-left: 80px;
}
.sy-city-picker-wrap .city-picker-toggle i {
    flex-shrink: 0;
    color: #fff;
    font-size: 1rem;
}
.sy-city-picker-wrap .city-picker-toggle:hover,
.sy-city-picker-wrap .city-picker-toggle:focus,
.sy-city-picker-wrap .city-picker-toggle.show {
    color: #fff;
    border-color: rgba(255,255,255,.45);
    background: rgba(255,255,255,.12);
    box-shadow: none;
}

/* ===== LOGIN / REGISTER ===== */
.sy-nav-actions .btn-login {
    height: 40px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 0 16px;
    border-radius: 2rem;
    border: 0 solid rgba(255,255,255,.55);
    color: #fff;
    background: transparent;
    font-size: .85rem;
    font-weight: 600;
    text-decoration: none;
    white-space: nowrap;
    transition: background .15s ease, border-color .15s ease;
}
.sy-nav-actions .btn-login:hover {
    background: rgba(255,255,255,.10);
    border-color: #fff;
    color: #fff;
}
.sy-nav-actions .btn-login.icon-only {
    width: 46px;
    min-width: 46px;
    height: 40px;
    padding: 0;
    justify-content: center;
    flex-shrink: 0;
}
.sy-nav-actions .btn-login.icon-only:hover,
.sy-nav-actions .btn-login.icon-only:focus,
.sy-nav-actions .btn-login.icon-only.show {
    background: rgba(255,255,255,.10);
    border-color: #fff;
    color: #fff;
}
.sy-nav-actions .btn-login.icon-only::after {
    margin-left: 4px;
    vertical-align: middle;
}
.sy-nav-actions .btn-register {
    height: 40px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 0 18px;
    border-radius: 2rem;
    border: none;
    background: var(--sy-gold);
    color: var(--sy-navy-1);
    font-size: .85rem;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
    transition: background .15s ease;
}
.sy-nav-actions .btn-register:hover {
    background: var(--sy-gold-dark);
}
/* Prevent horizontal page overflow (e.g. from the wishlist offcanvas on mobile) */
body {
    overflow-x: hidden;
}

/* Account dropdown (profile icon) card — keep it right-aligned to the icon and
   fully inside the viewport so it can never run off the right edge. */
.sy-nav-actions .dropdown.hover-dropdown .dropdown-menu[aria-labelledby="userMenuBtn"] {
    right: 0;
    left: auto;
    min-width: 200px;
    max-width: calc(100vw - 16px);
}

.seller-advice-item {
    position: relative;
}
.seller-advice-card {
    display: none;
    position: absolute;
    right: 100%;
    bottom: -5px;
    min-width: 140px;
    background: #ffffff;
    border: 1px solid #d0d7de;
    border-radius: 6px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.14);
    padding: 0.4rem 0;
    margin-right: 6px;
    z-index: 1060;
}
.seller-advice-card.show,
.seller-advice-item:hover .seller-advice-card,
.seller-advice-item:focus-within .seller-advice-card {
    display: block;
}
.seller-advice-card .dropdown-item {
    font-size: 0.85rem;
    padding: 0.4rem 1.15rem;
    color: #24292f;
}
.seller-advice-card .dropdown-item:hover {
    background-color: #f6f8fa;
    color: #0969da;
}
/* ===== DESKTOP ===== */
@media (min-width: 992px) {
    .sy-brand-panel {
        width: 150px;
        padding: 0.75rem 0.5rem;
    }
    .sy-brand-panel .brand-icon-ring {
        width: 80px;
        height: 80px;
    }
    .sy-brand-panel .brand-name-dark {
        font-size: 0.7rem;
        margin-top: 0.55rem;
    }

    .sy-topbar {
        padding: 0.25rem 1.25rem 0.25rem 0;
        gap: 1rem;
    }
    .ticker-item {
        font-size: 0.78rem;
        padding: 0 1.25rem;
    }
    .sy-topbar .wishlist-btn {
        width: 32px;
        height: 32px;
        font-size: 0.85rem;
    }
    .sy-topbar .wishlist-badge {
        font-size: 0.6rem;
        min-width: 16px;
        height: 16px;
        top: -6px;
        right: -6px;
    }
    .sy-topbar .topbar-tools {
        gap: 0.6rem;
    }
    .sy-topbar .font-size-switch button {
        font-size: 0.7rem;
        padding: 0.25rem 0.6rem;
    }
    .sy-topbar .lang-switch-toggle {
        font-size: 0.75rem;
        padding: 0.3rem 0.8rem;
    }

    .sy-mainbar {
        padding: 0.45rem 1.25rem;
    }
    .sy-mainbar .navbar {
        width: 100%;
    }
    .sy-mainbar .container-fluid {
        width: 100%;
        max-width: none;
        display: flex;
        align-items: center;
        min-width: 0;
    }
    .sy-mainbar .navbar-collapse {
        display: flex !important;
        align-items: center;
        flex: 1 1 auto;
        min-width: 0;
    }
    .sy-mainbar .navbar-toggler {
        display: none;
    }

    .sy-mainbar .navbar-nav {
        display: flex;
        align-items: center;
        flex-wrap: nowrap;
        margin: 0 !important;
        padding: 0;
        flex: 0 1 auto;
        min-width: 0;
    }
    .sy-mainbar .navbar-nav .nav-item {
        flex: 0 0 auto;
    }
    .sy-mainbar .navbar-nav .nav-link {
        color: var(--nav-link-color, #eef1fb) !important;
        padding: .5rem .85rem;
        font-size: .90rem;
        white-space: nowrap;
    }
    .sy-mainbar .navbar-nav .nav-link:hover {
        color: var(--sy-gold) !important;
    }
    .sy-mainbar .navbar-nav .dropdown-menu {
        position: absolute;
    }

    .sy-nav-actions {
        margin-left: auto;
        flex-shrink: 0;
    }
    .sy-city-picker-wrap {
        width: 200px;
    }

    .hover-dropdown {
        position: relative;
    }
    .hover-dropdown > .dropdown-menu {
        margin-top: 0px;
        margin-left: 0px;
    }
    .hover-dropdown:hover > .dropdown-menu,
    .hover-dropdown > .dropdown-menu:hover {
        display: block;
    }
}

/* ===== MEDIUM DESKTOP / TABLET ===== */
@media (min-width: 1400px) and (max-width: 1199.98px) {
    .sy-mainbar {
        padding-left: .75rem;
        padding-right: .75rem;
    }
    .sy-mainbar .navbar-nav .nav-link {
        padding-left: .55rem;
        padding-right: .55rem;
        font-size: .84rem;
    }
    .sy-nav-actions {
        gap: 6px;
    }
    .sy-city-picker-wrap {
        width: 220px;
    }
    .sy-city-picker-wrap .city-picker-toggle {
        padding-left: 10px;
        padding-right: 10px;
        font-size: .82rem;
    }
    .sy-nav-actions .btn-login.icon-only {
        width: 42px;
        min-width: 42px;
    }
}

/* ===== MOBILE / TABLET ===== */
@media (max-width: 991.98px) {
    .sy-mainbar {
        padding: .35rem .5rem;
    }
    .sy-mainbar .navbar-toggler {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-left: auto;
    }
        .sy-mainbar .navbar-collapse {
        width: 100%;
        margin-top: .5rem;
        background: var(--sy-navy-2);
        border: 1px solid rgba(255,255,255,.08);
        border-radius: 8px;
        padding: .5rem;
        max-height: 75vh;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }

    .sy-mainbar .navbar-nav {
        width: 100%;
        margin: 0 !important;
        padding: 0;
    }
    .sy-mainbar .navbar-nav .nav-link {
        width: 100%;
        padding: .65rem .75rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: var(--nav-link-color, #eef1fb) !important;
        font-size: .85rem;
        border-bottom: 1px solid rgba(255,255,255,.06);
    }
    .sy-mainbar .navbar-nav .nav-link:hover {
        color: var(--sy-gold) !important;
    }
    .sy-mainbar .navbar-nav .dropdown-menu {
        background: rgba(255,255,255,0.04);
        border: none;
        border-radius: 0.4rem;
        margin: 0.2rem 0.5rem 0.5rem 0.5rem;
        padding: 0.2rem 0;
        box-shadow: none;
        width: calc(100% - 1rem);
        position: static !important;
        transform: none !important;
        float: none !important;
    }
    .sy-mainbar .navbar-nav .dropdown-item {
        color: rgba(255,255,255,0.8) !important;
        padding: 0.4rem 1rem;
        font-size: 0.8rem;
        border-radius: 0.3rem;
    }
    .sy-mainbar .navbar-nav .dropdown-item:hover {
        background: rgba(255,255,255,0.08);
        color: var(--sy-gold) !important;
    }
    .sy-mainbar .navbar-nav .dropdown-toggle::after {
        margin-left: 0.5rem;
    }

        .hover-dropdown > .dropdown-menu {
        display: none;
    }
    .hover-dropdown > .dropdown-menu.show {
        display: block;
    }

    .sy-nav-actions {
        width: 100%;
        margin: .5rem 0 0;
        padding: .6rem 0 0;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: .5rem;
        border-top: 1px solid rgba(255,255,255,.08);
    }
    .sy-city-picker-wrap {
        width: 100%;
    }
    .sy-city-picker-wrap .city-picker-toggle {
        width: 100%;
        height: 38px;
        border-radius: 7px;
        font-size: .82rem;
    }
    .sy-nav-actions .btn-login.icon-only {
        width: 100%;
        height: 38px;
        min-width: 0;
        border-radius: 7px;
    }
    .sy-nav-actions .dropdown-menu {
        width: 100%;
        position: static !important;
        transform: none !important;
        margin-top: .35rem !important;
    }

    .sy-mainbar .city-picker-toggle {
        min-width: auto;
        width: 100%;
        justify-content: center;
        font-size: 0.78rem;
        padding: 0.35rem 0.7rem;
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.15);
        color: #fff;
        border-radius: 2rem;        margin-left: 0;
    }
    .sy-mainbar .city-picker-toggle i { color: rgba(255,255,255,0.5); }

    .sy-mainbar .btn-login,
    .sy-mainbar .btn-register {
        width: 100%;
        justify-content: center;
        height: 38px;
        font-size: 0.8rem;
    }
}

/* ===== SMALL PHONES ===== */
@media (max-width: 576px) {
    .sy-brand-panel {
        width: 70px;
        padding: 0.3rem 0.15rem;
    }
    .sy-brand-panel .brand-icon-ring {
        width: 32px;
        height: 32px;
    }
    .sy-brand-panel .brand-name-dark {
        font-size: 0.45rem;
        margin-top: 0.2rem;
    }

    .sy-topbar {
        padding: 0.1rem 0.3rem;
        gap: 0.3rem;
    }
    .ticker-item {
        font-size: 0.5rem;
        padding: 0 0.4rem;
        gap: 0.15rem;
    }
    .sy-topbar .wishlist-btn {
        width: 24px;
        height: 24px;
        font-size: 0.55rem;
    }
    .sy-topbar .wishlist-badge {
        font-size: 0.4rem;
        min-width: 12px;
        height: 12px;
        top: -4px;
        right: -4px;
    }
    .sy-topbar .topbar-tools {
        gap: 0.25rem;
    }
    .sy-topbar .font-size-switch button {
        padding: 0.12rem 0.32rem;
        font-size: 0.48rem !important;
    }
    .sy-topbar .lang-switch-toggle {
        font-size: 0.5rem;
        padding: 0.15rem 0.4rem;
    }
    .sy-topbar .lang-switch-toggle span { display: none; }
    .sy-topbar .lang-switch-toggle i.bi-globe2 { font-size: 0.65rem; }

    .sy-mainbar {
        padding: 0.2rem 0.3rem;
    }
    .sy-mainbar .navbar-toggler {
        padding: 0.3rem 0.4rem;
    }
    .sy-mainbar .navbar-collapse {
        margin-top: .35rem;
        padding: .35rem;
    }
    .sy-mainbar .navbar-nav .nav-link {
        font-size: 0.78rem;
        padding: 0.4rem 0.6rem;
    }
    .sy-mainbar .navbar-nav .dropdown-item {
        font-size: 0.75rem;
        padding: 0.3rem 0.8rem;
    }
    .sy-nav-actions {
        gap: .4rem;
        padding-top: .5rem;
    }
    .sy-city-picker-wrap .city-picker-toggle {
        height: 36px;
        padding: 0 .7rem;
        font-size: .76rem;
    }
    .sy-nav-actions .btn-login.icon-only {
        height: 36px;
        font-size: .9rem;
    }
    .sy-mainbar .city-picker-toggle {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }
    .sy-mainbar .btn-login,
    .sy-mainbar .btn-register {
        height: 34px;
        font-size: 0.75rem;
    }

    .city-picker-menu {
        width: min(300px, calc(100vw - 20px)) !important;
        max-width: calc(100vw - 20px);
        max-height: 380px;
    }
    .city-picker-top-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    .wishlist-offcanvas {
        width: 100%;
        max-width: 100%;
    }
    .wishlist-card img {
        width: 50px;
        height: 44px;
    }
}

/* ===== CITY PICKER DROPDOWN ===== */
.city-picker-menu {
    width: 340px;
    max-height: 480px;
    overflow-y: auto;
    padding: 0;
    border: 1px solid var(--sy-line);
    border-radius: 0.9rem;
    box-shadow: 0 16px 40px rgba(18,33,58,.15);
    background: #fff;
}
@media (min-width: 1400px) {
    .city-picker-menu {
        width: 340px !important;
        max-width: calc(100vw - 30px);
    }
}

.city-picker-search {
    padding: 0.9rem;
    border-bottom: 1px solid var(--sy-line);
    position: sticky;
    top: 0;
    background: #fff;
    z-index: 2;
}
.city-picker-search .input-wrap {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--sy-bg-soft);
    border-radius: 2rem;
    padding: 0.55rem 1rem;
}
.city-picker-search input {
    border: none;
    background: transparent;
    outline: none;
    width: 100%;
    font-size: 0.9rem;
}
.city-picker-search i { color: var(--sy-muted); }

.city-picker-mode-switch {
    display: flex;
    gap: 0.4rem;
    margin-bottom: 0.7rem;
}
.city-picker-mode-switch button {
    flex: 1;
    border: 1px solid var(--sy-line);
    background: #fff;
    color: var(--sy-muted);
    border-radius: 2rem;
    padding: 0.4rem 0.5rem;
    font-size: 0.82rem;
    font-weight: 600;
}
.city-picker-mode-switch button.active {
    background: var(--sy-ink);
    border-color: var(--sy-ink);
    color: #fff;
}
.city-picker-pincode-wrap { display: none; }
.city-picker-pincode-wrap .input-wrap {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--sy-bg-soft);
    border-radius: 2rem;
    padding: 0.55rem 1rem;
}
.city-picker-pincode-wrap input {
    border: none;
    background: transparent;
    outline: none;
    width: 100%;
    font-size: 0.9rem;
}
.city-picker-pincode-status {
    font-size: 0.8rem;
    padding: 0.5rem 0.1rem 0;
    color: var(--sy-muted);
    min-height: 1.2em;
}
.city-picker-pincode-status.error { color: #e0245e; }
.city-picker-pincode-status.success { color: #1a9c5c; }

.city-picker-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 0.9rem;
    border-bottom: 1px solid var(--sy-line);
    font-size: 0.88rem;
}
.city-picker-actions .detect-loc {
    color: var(--sy-ink);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    background: none;
    border: none;
}
.city-picker-actions .reset-city {
    color: var(--sy-gold-dark);
    font-weight: 600;
    background: none;
    border: none;
}

.city-picker-section-label {
    background: var(--sy-bg-soft);
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--sy-muted);
    text-transform: uppercase;
    letter-spacing: .04em;
    padding: 0.5rem 0.9rem;
}
.city-picker-top-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.25rem;
    padding: 0.6rem;
}
.city-picker-top-grid button {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.3rem;
    background: none;
    border: none;
    padding: 0.5rem 0.25rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--sy-ink);
    border-radius: 0.5rem;
}
.city-picker-top-grid button:hover {
    background: var(--sy-bg-soft);
    color: var(--sy-gold-dark);
}
.city-picker-top-grid button i {
    font-size: 1.3rem;
    /*color: var(--sy-ink);*/
}

.city-picker-other-list button {
    display: block;
    width: 100%;
    text-align: left;
    background: none;
    border: none;
    border-bottom: 1px solid var(--sy-line);
    padding: 0.7rem 0.9rem;
    font-size: 0.88rem;
    color: var(--sy-ink);
}
.city-picker-other-list button:hover {
    background: var(--sy-bg-soft);
    color: var(--sy-gold-dark);
}
.city-picker-no-results {
    padding: 1.5rem 0.9rem;
    text-align: center;
    color: var(--sy-muted);
    font-size: 0.88rem;
    display: none;
}

/* ===== WISHLIST OFFCANVAS ===== */
.wishlist-offcanvas {
    width: 420px;
    max-width: 92vw;
}
.wishlist-offcanvas .offcanvas-header {
    align-items: flex-start;
    padding: 1.1rem 1.25rem;
    border-bottom: 1px solid var(--sy-line);
    background: var(--sy-bg-soft);
}
.wishlist-offcanvas .offcanvas-header-icon {
    width: 38px;
    height: 38px;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #ffe3ea;
    color: #e0245e;
    font-size: 1.15rem;
}
.wishlist-offcanvas .offcanvas-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--sy-ink);
}
.wishlist-offcanvas .offcanvas-subtitle {
    font-size: 0.82rem;
    color: var(--sy-muted);
}
.wishlist-offcanvas .offcanvas-body { padding: 0; }

.wishlist-tab-switch {
    display: flex;
    background: var(--sy-bg-soft);
    border-radius: 0.7rem;
    padding: 0.3rem;
    gap: 0.3rem;
}
.wishlist-tab-switch .wishlist-tab-btn {
    flex: 1;
    border: none;
    background: transparent;
    border-radius: 0.5rem;
    padding: 0.55rem 0.5rem;
    font-weight: 700;
    font-size: 0.92rem;
    color: var(--sy-muted);
}
.wishlist-tab-switch .wishlist-tab-btn.active {
    background: #fff;
    color: var(--sy-ink);
    box-shadow: 0 2px 6px rgba(18,33,58,.1);
}

.wishlist-card {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    border: 1px solid var(--sy-line);
    border-radius: 0.75rem;
    padding: 0.6rem;
    margin-bottom: 0.75rem;
}
.wishlist-card .compare-check {
    flex-shrink: 0;
    width: 18px;
    height: 18px;
}
.wishlist-card img {
    width: 64px;
    height: 56px;
    object-fit: cover;
    border-radius: 0.5rem;
    flex-shrink: 0;
}
.wishlist-card .info { flex: 1; min-width: 0; }
.wishlist-card .info h6 {
    margin-bottom: 0.15rem;
    font-weight: 700;
    font-size: 0.88rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.wishlist-card .info .loc {
    color: var(--sy-muted);
    font-size: 0.76rem;
    margin-bottom: 0.15rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.wishlist-card .info .price {
    color: var(--sy-ink);
    font-weight: 700;
    font-size: 0.85rem;
}
.wishlist-card .remove-btn {
    border: none;
    background: none;
    color: var(--sy-muted);
    font-size: 1.05rem;
    flex-shrink: 0;
}
.wishlist-card .remove-btn:hover { color: #e0245e; }

.wishlist-bottom-bar {
    border-top: 1px solid var(--sy-line);
    padding: 0.9rem 1.1rem;
    display: flex;
    gap: 0.75rem;
}
.wishlist-bottom-bar .btn-enquire {
    flex: 1;
    background: #fff;
    color: var(--sy-ink);
    border: 1.5px solid var(--sy-line);
    border-radius: 0.6rem;
    padding: 0.7rem 0.5rem;
    font-weight: 600;
    font-size: 0.9rem;
}
.wishlist-bottom-bar .btn-compare {
    flex: 1;
    background: var(--sy-gold);
    color: var(--sy-ink);
    border: none;
    border-radius: 0.6rem;
    padding: 0.7rem 0.5rem;
    font-weight: 700;
    font-size: 0.9rem;
}
.wishlist-bottom-bar .btn-compare:disabled {
    background: var(--sy-bg-soft);
    color: var(--sy-muted);
    cursor: not-allowed;
}

/* ===== ENQUIRY MODAL ===== */
#enquiryModal .modal-content {
    border-radius: 0.9rem;
    border: none;
}
#enquiryModal .form-label {
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--sy-ink);
}
#enquiryModal .btn-submit-enquiry {
    background: var(--sy-ink);
    color: #fff;
    border: none;
    border-radius: 0.5rem;
    padding: 0.6rem 1.4rem;
    font-weight: 600;
}
#enquiryModal .btn-submit-enquiry:hover { background: #1a2f52; }
#enquiryModal .btn-submit-enquiry:disabled { opacity: .65; cursor: not-allowed; }
#enquiryFormAlert { display: none; }
.mega-menu {
    --bs-dropdown-bg: var(--sy-header-live-bg);
    --bs-dropdown-link-color: rgba(255,255,255,.75);
    --bs-dropdown-link-hover-color: var(--sy-gold);
    --bs-dropdown-link-hover-bg: transparent;
    --bs-dropdown-color: rgba(255,255,255,.75);
    min-width: 820px;
    max-width: 92vw;
    padding: 1rem;
    background: var(--sy-header-live-bg) !important;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 0.6rem;
    z-index: 1040;   /* add this — higher than .sy-header's 1030 */
}
    /* ===== "More" menu: nested items keep their full mega-menu ===== */
#moreNavMenu {
    min-width: 200px;
}
#moreNavMenu > .nav-item {
    list-style: none;
}
#moreNavMenu > .nav-item > .nav-link {
    color: var(--sy-ink) !important;
    padding: .55rem 1rem;
    font-size: .85rem;
    white-space: nowrap;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .5rem;
}
#moreNavMenu > .nav-item > .nav-link:hover {
    background: var(--sy-bg-soft);
    color: var(--sy-gold-dark) !important;
}
#moreNavMenu > .nav-item.hover-dropdown {
    position: relative;
}
#moreNavMenu > .nav-item.hover-dropdown > .dropdown-menu.mega-menu {
    top: 0;
    left: auto;
    right: 100%;
    margin-right: 4px;
}

body.dark-mode #moreNavMenu > .nav-item > .nav-link {
    color: var(--dm-text-primary) !important;
}
body.dark-mode #moreNavMenu > .nav-item > .nav-link:hover {
    background: var(--dm-hover);
    color: var(--sy-gold) !important;
}
.mega-menu-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 1rem;
}
.mega-menu-heading {
    display: block;
    color: var(--sy-gold) !important;
    font-weight: 700;
    font-size: .85rem;
    text-decoration: none;
    margin-bottom: .4rem;
}
.mega-menu-heading:hover { color: #fff !important; }
.mega-menu-sublist { list-style: none; margin: 0; padding: 0; }
.mega-menu-sublist li a {
    display: block;
    color: rgba(255,255,255,.75) !important;
    font-size: .8rem;
    padding: .25rem 0;
    text-decoration: none;
}
.mega-menu-sublist li a:hover { color: var(--sy-gold) !important; }
.mega-menu-col {
    max-height: 420px;
    overflow-y: auto;
}
.mega-menu-col::-webkit-scrollbar { width: 4px; }
.mega-menu-col::-webkit-scrollbar-thumb { background: rgba(255,255,255,.25); border-radius: 4px; }
.mega-menu-sublist details.mega-menu-details {
    border: none;
}
.mega-menu-sublist details.mega-menu-details > summary {
    list-style: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .4rem;
    padding: .25rem 0;
}
.mega-menu-sublist details.mega-menu-details > summary::-webkit-details-marker {
    display: none;
}
.mega-menu-sublist details.mega-menu-details > summary > a {
    color: rgba(255,255,255,.75);
    font-size: .8rem;
    text-decoration: none;
    padding: 0;
}
.mega-menu-sublist details.mega-menu-details > summary > a:hover {
    color: var(--sy-gold);
}
.mega-menu-sublist details.mega-menu-details > summary::after {
    content: '▸';
    font-size: .65em;
    color: rgba(255,255,255,.4);
    transition: transform .15s ease;
    flex-shrink: 0;
}
.mega-menu-sublist details.mega-menu-details[open] > summary::after {
    transform: rotate(90deg);
}
.mega-menu-sublist .mega-menu-nested {
    padding-left: .85rem;
    margin: .1rem 0 .3rem;
    border-left: 1px solid rgba(255,255,255,.1);
}

@media (max-width: 991.98px) {
    .mega-menu {
        min-width: 0;
        width: 100%;
        background: transparent;
        border: none;
        padding: .25rem .5rem .5rem 1.1rem;
        box-shadow: none;
    }
    .mega-menu-grid {
        grid-template-columns: 1fr;
        gap: 0;
    }
    .mega-menu-col {
        margin-bottom: 0.9rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid rgba(255,255,255,.08);
    }
    .mega-menu-col:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    .mega-menu-heading {
        font-size: .8rem;
        text-transform: uppercase;
        letter-spacing: .03em;
        margin-bottom: .5rem;
        padding-left: 10px;
        padding-bottom: .35rem;
        border-bottom: 1px solid rgba(240,180,41,.25);
    }
    .mega-menu-sublist li a {
        font-size: .82rem;
        padding: .42rem 0 .42rem .3rem;
        border-left: 2px solid rgba(255,255,255,.12);
        padding-left: .6rem;
    }
    .mega-menu-sublist li a:hover {
        border-left-color: var(--sy-gold);
        background: rgba(255,255,255,.04);
    }
}
   /* Add this after your existing mega-menu styles */
@media (min-width: 992px) {
    .sy-mainbar .navbar .container-fluid {
        position: relative;
    }
    
    .sy-mainbar .navbar-nav .dropdown-menu.mega-menu {
        position: fixed !important;
        left: 0 !important;
        right: 0 !important;
        top: auto !important;
        width: 100vw !important;
        max-width: 100vw !important;
        min-width: 100vw !important;
        border-radius: 0 !important;
        padding: 1.5rem 2rem !important;
        margin-top: 0 !important;
        box-shadow: 0 12px 40px rgba(0,0,0,0.3) !important;
        border: none !important;
        border-top: 2px solid var(--sy-gold) !important;
    }
    
    /* Position the mega menu correctly below the header */
    .sy-mainbar .navbar-nav .dropdown-menu.mega-menu {
        margin-top: 0.5rem !important;
    }
    
    /* Ensure the mega menu grid uses full width */
    .mega-menu .mega-menu-grid {
        max-width: 1400px;
        margin: 0 auto;
        grid-template-columns: repeat(6, 1fr);
        gap: 1.5rem;
        max-height: 420px;
        overflow-y: auto;
    }
    .mega-menu .mega-menu-grid::-webkit-scrollbar { width: 4px; }
    .mega-menu .mega-menu-grid::-webkit-scrollbar-thumb { background: rgba(255,255,255,.25); border-radius: 4px; }
}
    body.dark-mode .sy-mainbar .navbar-nav .dropdown-menu.mega-menu {
    background: var(--dm-bg-secondary) !important;
    border-color: var(--dm-border) !important;
}
    /* List Property Button Styles */
.sy-nav-actions .btn-list-property {
    height: 40px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 0 18px;
    border-radius: 2rem;
    border: none;
    background: linear-gradient(135deg, #f0b429, #f59e0b);
    color: var(--sy-navy-1, #12213a);
    font-size: .85rem;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
    transition: all .2s ease;
    box-shadow: 0 2px 8px rgba(240, 180, 41, 0.3);
}

.sy-nav-actions .btn-list-property:hover {
    background: linear-gradient(135deg, #d99b00, #f0b429);
    color: var(--sy-navy-1, #12213a);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(240, 180, 41, 0.4);
    text-decoration: none;
}

.sy-nav-actions .btn-list-property i {
    font-size: 1.1rem;
}

/* Mobile responsive */
@media (max-width: 991.98px) {
    .sy-nav-actions .btn-list-property {
        width: 100%;
        justify-content: center;
        height: 38px;
        font-size: 0.8rem;
        padding: 0 12px;
    }
}

@media (max-width: 576px) {
    .sy-nav-actions .btn-list-property {
        height: 34px;
        font-size: 0.75rem;
        padding: 0 10px;
    }
}

/* Dark mode support */
body.dark-mode .sy-nav-actions .btn-list-property {
    background: linear-gradient(135deg, #f0b429, #f59e0b);
    color: var(--sy-navy-1, #12213a);
}

body.dark-mode .sy-nav-actions .btn-list-property:hover {
    background: linear-gradient(135deg, #d99b00, #f0b429);
}
</style>

<!-- ===== GOOGLE TRANSLATE SCRIPTS ===== -->
<script type="text/javascript">
function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'en',
        layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
        autoDisplay: false
    }, 'google_translate_element');
}
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" defer></script>

<!-- Hidden div for Google Translate -->
<div id="google_translate_element" style="display:none;"></div>

<script>
// Google Translate integration - trigger translation from our custom dropdown
function triggerGoogleTranslate(langCode) {
    // Wait for Google Translate to load
    if (typeof google !== 'undefined' && google.translate) {
        // Find the Google Translate select element
        const select = document.querySelector('.goog-te-combo');
        if (select) {
            select.value = langCode;
            select.dispatchEvent(new Event('change'));
            // Store the selected language in localStorage
            localStorage.setItem('tatito_lang_code', langCode);
            return true;
        }
    }
    return false;
}

// Try to trigger translation after Google Translate loads
function waitForGoogleTranslate(langCode, retries) {
    retries = retries || 0;
    if (retries > 20) return; // Give up after 10 seconds
    
    if (triggerGoogleTranslate(langCode)) {
        return;
    }
    
    setTimeout(function() {
        waitForGoogleTranslate(langCode, retries + 1);
    }, 500);
}

// Restore previous language on page load
document.addEventListener('DOMContentLoaded', function() {
    const savedLang = localStorage.getItem('tatito_lang_code');
    if (savedLang && savedLang !== 'en') {
        // Wait for Google Translate to load and then apply
        setTimeout(function() {
            waitForGoogleTranslate(savedLang);
        }, 1000);
    }
});
</script>

<!-- ===== HEADER HTML ===== -->
<header class="sy-header sy-header-dark-wrap" style="background: <?php echo htmlspecialchars($headerSettings['header_bg_color']); ?>;">
    <div class="sy-header-inner">
    <!-- Brand panel -->
    <a class="sy-brand-panel" href="/front/">
        <span class="brand-icon-ring">
            <img src="<?php echo htmlspecialchars($headerLogoPath); ?>" alt="<?php echo htmlspecialchars($headerSettings['site_title']); ?>">
        </span>
        <?php /*<span class="brand-name-dark"><?php echo htmlspecialchars($headerSettings['site_title']); ?></span>*/ ?>
    </a>

    <div class="sy-header-right">
        <!-- Top bar with ticker -->
        <div class="sy-topbar" style="background: <?php echo htmlspecialchars($headerSettings['header_bg_color']); ?>;">
            
            <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
        <?php
        // Fetch trending projects data dynamically
        $tickerItems = [];
        $tickerSql = "SELECT 
                        project_name,
                        category_id,
                        city,
                        price,
                        area,
                        bedrooms,
                        bathrooms,
                        status 
                      FROM trending_projects 
                      WHERE status='Active' 
                      ORDER BY project_order ASC, created_at DESC 
                      LIMIT 20";
        $tickerResult = $conn->query($tickerSql);
        if ($tickerResult) {
            while ($tickerRow = $tickerResult->fetch_assoc()) {
                $tickerItems[] = $tickerRow;
            }
        }
        ?>
        <div class="ticker-wrap">
            <div class="ticker-track">
                <?php if (!empty($tickerItems)): ?>
                    <?php for ($tickerRep = 0; $tickerRep < 2; $tickerRep++): ?>
                        <?php foreach ($tickerItems as $item): ?>
                            <div class="ticker-item">
                                <span class="city"><?php echo htmlspecialchars($item['city'] ?? 'N/A'); ?></span>
                                <span class="up">
                                    <i class="bi bi-arrow-up-short"></i>
                                    <?php 
                                    if (!empty($item['price'])) {
                                        echo htmlspecialchars($item['price']);
                                    } else {
                                        echo 'Price on request';
                                    }
                                    ?>
                                </span>
                                <?php if (!empty($item['area'])): ?>
                                    <span class="up">
                                        <i class="bi bi-arrow-up-short"></i>
                                        <?php echo htmlspecialchars($item['area']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endfor; ?>
                <?php else: ?>
                    <div class="ticker-item" style="color: rgba(255,255,255,0.4); font-size: 0.6rem;">
                        No projects available
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <!-- When not logged in, keep an empty spacer to maintain layout -->
        <div class="ticker-wrap" style="flex: 1; min-width: 0;"></div>
    <?php endif; ?>
            
            <!-- Dark/Light Mode Toggle Button -->
            <button class="theme-toggle-btn" id="themeToggleBtn" aria-label="Toggle dark/light mode">
                <i class="bi bi-moon-fill" id="themeToggleIcon"></i>
            </button>
            
            <button class="wishlist-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#wishlistOffcanvas" aria-label="Wishlist">
                <i class="bi bi-heart-fill"></i>
                <span class="wishlist-badge" id="wishlistBadge">0</span>
            </button>

            <!-- ===== Font size adjuster + language switcher ===== -->
            <div class="topbar-tools">
                <div class="font-size-switch" role="group" aria-label="Adjust text size">
                    <button type="button" data-size="sm" aria-label="Decrease text size">A-</button>
                    <button type="button" data-size="md" class="active" aria-label="Reset text size">A</button>
                    <button type="button" data-size="lg" aria-label="Increase text size">A+</button>
                </div>

                <div class="dropdown lang-switch-wrap">
                    <button class="lang-switch-toggle dropdown-toggle" type="button" id="langSwitchBtn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <i class="bi bi-globe2"></i> <span id="langSwitchLabel">English</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end lang-switch-menu" aria-labelledby="langSwitchBtn">
                        <li><a class="dropdown-item lang-option" href="#" data-lang="en" data-label="English">English <i class="bi bi-check2"></i></a></li>
                        <li><a class="dropdown-item lang-option" href="#" data-lang="hi" data-label="हिंदी">हिंदी <i class="bi bi-check2"></i></a></li>
                        <li><a class="dropdown-item lang-option" href="#" data-lang="mr" data-label="मराठी">मराठी <i class="bi bi-check2"></i></a></li>
                        <li><a class="dropdown-item lang-option" href="#" data-lang="gu" data-label="ગુજરાતી">ગુજરાતી <i class="bi bi-check2"></i></a></li>
                        <li><a class="dropdown-item lang-option" href="#" data-lang="bn" data-label="বাংলা">বাংলা <i class="bi bi-check2"></i></a></li>
                        <li><a class="dropdown-item lang-option" href="#" data-lang="ta" data-label="தமிழ்">தமிழ் <i class="bi bi-check2"></i></a></li>
                        <li><a class="dropdown-item lang-option" href="#" data-lang="te" data-label="తెలుగు">తెలుగు <i class="bi bi-check2"></i></a></li>
                        <li><a class="dropdown-item lang-option" href="#" data-lang="kn" data-label="ಕನ್ನಡ">ಕನ್ನಡ <i class="bi bi-check2"></i></a></li>
                        <li><a class="dropdown-item lang-option" href="#" data-lang="ml" data-label="മലയാളം">മലയാളം <i class="bi bi-check2"></i></a></li>
                        <li><a class="dropdown-item lang-option" href="#" data-lang="pa" data-label="ਪੰਜਾਬੀ">ਪੰਜਾਬੀ <i class="bi bi-check2"></i></a></li>
                        <li><a class="dropdown-item lang-option" href="#" data-lang="or" data-label="ଓଡ଼ିଆ">ଓଡ଼ିଆ <i class="bi bi-check2"></i></a></li>
                        <li><a class="dropdown-item lang-option" href="#" data-lang="as" data-label="অসমীয়া">অসমীয়া <i class="bi bi-check2"></i></a></li>
                        <li><a class="dropdown-item lang-option" href="#" data-lang="es" data-label="Español">Español <i class="bi bi-check2"></i></a></li>
                        <li><a class="dropdown-item lang-option" href="#" data-lang="fr" data-label="Français">Français <i class="bi bi-check2"></i></a></li>
                        <li><a class="dropdown-item lang-option" href="#" data-lang="de" data-label="Deutsch">Deutsch <i class="bi bi-check2"></i></a></li>
                        <li><a class="dropdown-item lang-option" href="#" data-lang="ar" data-label="العربية">العربية <i class="bi bi-check2"></i></a></li>
                        <li><a class="dropdown-item lang-option" href="#" data-lang="zh-CN" data-label="中文">中文 <i class="bi bi-check2"></i></a></li>
                        <li><a class="dropdown-item lang-option" href="#" data-lang="ja" data-label="日本語">日本語 <i class="bi bi-check2"></i></a></li>
                        <li><a class="dropdown-item lang-option" href="#" data-lang="ko" data-label="한국어">한국어 <i class="bi bi-check2"></i></a></li>
                        <li><a class="dropdown-item lang-option" href="#" data-lang="ru" data-label="Русский">Русский <i class="bi bi-check2"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Main nav -->
        <div class="sy-mainbar" style="background: <?php echo htmlspecialchars($headerSettings['header_bg_color']); ?>;">
            <nav class="navbar navbar-expand-lg navbar-dark py-0">
                <div class="container-fluid px-0">
                    <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="mainNav">
                        <?php
                        // ============ MAIN NAV CONFIG (7 items) ============
                        /*$navConfig = [
                            ['label' => 'Buy',                 'types' => ['residential','commercial','land'], 'link_base' => '/front/properties.php?listing_type=for-sale'],
                            ['label' => 'Rent',                'types' => ['residential','commercial','land'], 'link_base' => '/front/properties.php?listing_type=for-rent-lease'],
                            ['label' => 'Vendors',             'types' => ['vendor'],                          'link_base' => '/front/vendors.php'],
                            ['label' => 'Local Services',      'types' => ['home_services'],                   'link_base' => '/front/local-services.php'],
                            ['label' => 'Interiors',           'types' => ['interior'],                        'link_base' => '/front/interiors.php'],
                            ['label' => 'Hotels & Homestays',  'types' => ['hotel','homestay'],                'link_base' => '/front/hotels-homestays.php'],
                            ['label' => 'PG & Hostels',        'types' => ['pg','hostel'],                     'link_base' => '/front/all-hotels-homestays.php'], // adjust if PG/Hostel gets its own page
                            ['label' => 'Blog',    'types' => [], 'link_base' => '/front/blog.php'],
                            ['label' => 'Finance', 'types' => [], 'link_base' => '/front/finance.php'],
                        ];*/
                        
                        /*$navConfig = [
                            ['label' => 'Buy',                'type' => 'buy',              'link_base' => '/front/properties.php?listing_type=for-sale'],
                            ['label' => 'Rent',                'type' => 'rent',              'link_base' => '/front/properties.php?listing_type=for-rent-lease'],
                            ['label' => 'Interiors',          'type' => 'interior',          'link_base' => '/front/interiors.php'],
                            ['label' => 'Vendors',            'type' => 'vendor',            'link_base' => '/front/vendors.php'],
                            ['label' => 'Local Services',     'type' => 'local_services',    'link_base' => '/front/local-services.php'],
                            ['label' => 'Finance', 'type' => null, 'link_base' => '/front/finance.php'],
                            ['label' => 'Blog',    'type' => null, 'link_base' => '/front/blog.php'],
                            ['label' => 'Hotels & Homestays', 'type' => 'hotels_homestays',  'link_base' => '/front/hotels-homestays.php'],
                            ['label' => 'PG & Hostels',       'type' => 'pg_hostels',        'link_base' => '/front/all-hotels-homestays.php'],
                            ['label' => 'Agents',    'type' => null, 'link_base' => '/front/agents.php'],
                        ];*/
                        $navConfig = [
    ['label' => 'Buy',                'type' => 'buy',              'link_base' => '/front/properties.php?listing_type=for-sale',            'target' => '_blank'],
    ['label' => 'Rent',               'type' => 'rent',             'link_base' => '/front/properties.php?listing_type=for-rent-lease',            'target' => '_blank'],
    ['label' => 'Interiors',          'type' => 'interior',         'link_base' => '/front/properties.php',            'target' => '_blank'],
    ['label' => 'Vendors',            'type' => 'vendor',           'link_base' => '/front/properties.php',            'target' => '_blank'],
    ['label' => 'Local Services',     'type' => 'local_services',   'link_base' => '/front/properties.php',            'target' => '_blank'],
    ['label' => 'Finance',            'type' => null,               'link_base' => '/front/finance.php',               'target' => '_blank'],
    ['label' => 'Blog',               'type' => null,               'link_base' => '/front/blog.php',            'target' => '_blank'],
    ['label' => 'Hotels & Homestays', 'type' => 'hotels_homestays', 'link_base' => '/front/hotels-homestays.php',      'target' => '_blank'],
    ['label' => 'PG & Hostels',       'type' => 'pg_hostels',       'link_base' => '/front/properties.php',            'target' => '_blank'],
    ['label' => 'Agents',             'type' => null,               'link_base' => '/front/agents.php',                'target' => '_blank'],
];

                        // Fetch every active category once, group by parent for fast lookup
                        $allCats = [];
                        //$catRes = $conn->query("SELECT id, parent_id, category_type, name, slug FROM property_categories WHERE status='Active' ORDER BY category_order ASC, name ASC");
                        $catRes = $conn->query("SELECT id, parent_id, category_type, name, slug FROM property_categories WHERE status='Active' ORDER BY category_order ASC, id ASC");
                        if ($catRes) {
                            while ($row = $catRes->fetch_assoc()) { $allCats[$row['id']] = $row; }
                        }
                        $childrenByParent = [];
                        foreach ($allCats as $cat) { $childrenByParent[$cat['parent_id']][] = $cat; }

                        // Build [ [cat => topLevelRow, children => [...]], ... ] filtered by allowed category_type(s)
                                                function build_full_tree($parentId, array $childrenByParent): array {
                            $nodes = [];
                            foreach (($childrenByParent[$parentId] ?? []) as $cat) {
                                $nodes[] = ['cat' => $cat, 'children' => build_full_tree($cat['id'], $childrenByParent)];
                            }
                            return $nodes;
                        }

                        function get_vertical_tree(string $category_type, array $childrenByParent): array {
                            $anchor = null;
                            foreach (($childrenByParent[0] ?? []) as $topCat) {
                                if ($topCat['category_type'] === $category_type) { $anchor = $topCat; break; }
                            }
                            if (!$anchor) return [];

                            $sections = $childrenByParent[$anchor['id']] ?? [];
                            $tree = [];
                            foreach ($sections as $section) {
                                $tree[] = ['cat' => $section, 'children' => build_full_tree($section['id'], $childrenByParent)];
                            }
                            return $tree;
                        }

                        function nav_link(string $base, string $slug): string {
                            $sep = (strpos($base, '?') !== false) ? '&' : '?';
                            return $base . $sep . 'category=' . urlencode($slug);
                        }

                        // Recursively renders a category node. Leaf nodes are plain links.
                        // Nodes with children become a <details> so deep branches expand on click.
                        function render_nav_node(array $node, string $linkBase, string $target = ''): void {
    $url = htmlspecialchars(nav_link($linkBase, $node['cat']['slug']));
    $name = htmlspecialchars($node['cat']['name']);
    $targetAttr = $target ? ' target="' . htmlspecialchars($target) . '" rel="noopener noreferrer"' : '';
    if (!empty($node['children'])) {
        echo '<details class="mega-menu-details">';
        echo '<summary><a href="' . $url . '"' . $targetAttr . '>' . $name . '</a></summary>';
        echo '<ul class="mega-menu-sublist mega-menu-nested">';
        foreach ($node['children'] as $child) {
            echo '<li>';
            render_nav_node($child, $linkBase, $target);
            echo '</li>';
        }
        echo '</ul>';
        echo '</details>';
    } else {
        echo '<a href="' . $url . '"' . $targetAttr . '>' . $name . '</a>';
    }
}
                        ?>

                        <ul class="navbar-nav mb-2 mb-lg-0" id="mainNavList" style="--nav-link-color: <?php echo htmlspecialchars($headerSettings['nav_link_color']); ?>;">
                            <?php foreach ($navConfig as $item):
    $tree = !empty($item['type']) ? get_vertical_tree($item['type'], $childrenByParent) : [];
    $navTarget = $item['target'] ?? '';
    $navTargetAttr = $navTarget ? ' target="' . htmlspecialchars($navTarget) . '" rel="noopener noreferrer"' : '';
?>
    <li class="nav-item dropdown hover-dropdown">
        <a class="nav-link<?php echo $tree ? ' dropdown-toggle' : ''; ?>"
           href="<?php echo htmlspecialchars($item['link_base']); ?>"
           <?php echo $tree ? 'data-bs-toggle="dropdown" aria-expanded="false"' : ''; ?><?php echo $navTargetAttr; ?>>
            <?php echo htmlspecialchars($item['label']); ?>
        </a>
        <?php if ($tree): ?>
        <div class="dropdown-menu mega-menu">
            <div class="mega-menu-grid">
                <?php foreach ($tree as $branch): ?>
                    <div class="mega-menu-col">
                        <a class="mega-menu-heading" href="<?php echo htmlspecialchars(nav_link($item['link_base'], $branch['cat']['slug'])); ?>"<?php echo $navTargetAttr; ?>>
                            <?php echo htmlspecialchars($branch['cat']['name']); ?>
                        </a>
                        <?php if ($branch['children']): ?>
                        <ul class="mega-menu-sublist">
                            <?php foreach ($branch['children'] as $child): ?>
                                <li><?php render_nav_node($child, $item['link_base'], $navTarget); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
                            <li class="nav-item dropdown hover-dropdown" id="moreNavItem" style="display:none;">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">More</a>
                                <ul class="dropdown-menu dropdown-menu-end" id="moreNavMenu"></ul>
                            </li>
                        </ul>

                        <!-- CTAs -->
                        <div class="sy-nav-actions">
                            <!-- City picker -->
                            <div class="dropdown sy-city-picker-wrap">
                                <button class="city-picker-toggle dropdown-toggle" type="button" id="cityPickerBtn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                    <i class="bi bi-geo-alt"></i> <span id="cityPickerLabel">Search</span>
                                </button>
                                <div class="dropdown-menu city-picker-menu" aria-labelledby="cityPickerBtn">
                                    <div class="city-picker-search">
                                        <div class="city-picker-mode-switch">
                                            <button type="button" class="active" id="cityModeByName">City</button>
                                            <button type="button" id="cityModeByPincode">Pincode</button>
                                        </div>
                                        <div class="input-wrap" id="cityPickerNameWrap">
                                            <i class="bi bi-search"></i>
                                            <input type="text" id="cityPickerSearch" placeholder="Type your city">
                                        </div>
                                        <div class="city-picker-pincode-wrap" id="cityPickerPincodeWrap">
                                            <div class="input-wrap">
                                                <i class="bi bi-mailbox"></i>
                                                <input type="text" id="cityPickerPincode" placeholder="Enter 6-digit pincode" inputmode="numeric" maxlength="6">
                                                <button type="button" id="cityPickerPincodeGo" class="btn-post" style="padding:.3rem .7rem;">Go</button>
                                            </div>
                                            <div class="city-picker-pincode-status" id="cityPickerPincodeStatus"></div>
                                        </div>
                                    </div>

                                    <div class="city-picker-actions">
                                        <button type="button" class="detect-loc" id="detectLocationBtn">
                                            <i class="bi bi-crosshair"></i> Detect
                                        </button>
                                        <button type="button" class="reset-city" id="resetCityBtn">Reset</button>
                                    </div>

                                    <div id="cityPickerResults">
                                        <?php if (!empty($topCitiesForPicker)): ?>
                                            <div class="city-picker-section-label">Top Cities</div>
                                            <div class="city-picker-top-grid" id="topCitiesGrid">
                                                <?php foreach ($topCitiesForPicker as $c): ?>
                                                    <button type="button" class="city-option-btn" data-city="<?php echo htmlspecialchars($c); ?>">
                                                        <i class="bi bi-buildings"></i>
                                                        <?php echo htmlspecialchars($c); ?>
                                                    </button>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($otherCitiesForPicker)): ?>
                                            <div class="city-picker-section-label">Other Cities</div>
                                            <div class="city-picker-other-list" id="otherCitiesList">
                                                <?php foreach ($otherCitiesForPicker as $c): ?>
                                                    <button type="button" class="city-option-btn" data-city="<?php echo htmlspecialchars($c); ?>">
                                                        <?php echo htmlspecialchars($c); ?>
                                                    </button>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="city-picker-no-results" id="cityPickerNoResults">No matching cities found.</div>
                                </div>
                            </div>
                            <!-- List Property Button - NEW -->
                            <a href="/front/sell-property.php" class="btn-list-property" target="_blank" rel="noopener noreferrer">
                                <i class="bi bi-plus-circle"></i> List Property
                            </a>

                            <!-- Login/Register -->
                            <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
                            <?php
                            if (!isset($_SESSION['account_type']) && isset($conn)) {
                                $chkUid = (int)$_SESSION['user_id'];
                                $chkRes = $conn->query("SELECT account_type, role FROM users WHERE id = $chkUid LIMIT 1");
                                if ($chkRes && $chkRow = $chkRes->fetch_assoc()) {
                                    $_SESSION['account_type'] = $chkRow['account_type'] ?? 'buyer';
                                    if (empty($_SESSION['user_role'])) {
                                        $_SESSION['user_role'] = $chkRow['role'] ?? 'user';
                                    }
                                }
                            }
                            $headerAccType = strtolower($_SESSION['account_type'] ?? $_SESSION['user_role'] ?? $_SESSION['role'] ?? '');
                            $isHeaderSeller = ($headerAccType === 'seller' || in_array($headerAccType, ['builder', 'agent', 'developer']));
                            ?>
                            <div class="dropdown hover-dropdown">
                                <a href="#" class="btn-login icon-only dropdown-toggle" id="userMenuBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-fill"></i>
                                </a>
                                <?php if ($isHeaderSeller): ?>
                                    <!-- Seller Account Dropdown -->
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenuBtn">
                                        <li class="dropdown-item-text text-muted small">
                                            <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></strong>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li class="dropdown-item-text small fw-semibold text-uppercase" style="letter-spacing:.04em;">My Activity</li>
                                        <li><a class="dropdown-item" href="/front/recently-searched.php">Recently Searched</a></li>
                                        <li><a class="dropdown-item" href="/front/recently-searched.php?tab=viewed">Recently Viewed</a></li>
                                        <li><a class="dropdown-item" href="/front/recently-searched.php?tab=shortlisted">Shortlisted</a></li>
                                        <li><a class="dropdown-item" href="/front/recently-searched.php?tab=contacted">Contacted</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="/front/mytatito.php">MyTATITO</a></li>
                                        <li><a class="dropdown-item" href="/front/manage-listings.php">Manage Listings</a></li>
                                        <li><a class="dropdown-item" href="/front/manage-responses.php">View All Responses</a></li>
                                        <li><a class="dropdown-item" href="/front/modify-profile.php">Modify Profile</a></li>
                                        <li><a class="dropdown-item" href="#" data-change-password-open>Change Password</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); logoutUser();">Logout</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); logoutUser(true);">Logout All Devices</a></li>
                                        <li class="seller-advice-item position-relative">
                                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="javascript:void(0)" id="sellerAdviceTrigger" onclick="event.stopPropagation(); document.getElementById('sellerAdviceCard').classList.toggle('show');">
                                                <span>&lsaquo; Advice</span>
                                            </a>
                                            <div class="seller-advice-card" id="sellerAdviceCard">
                                                <a class="dropdown-item" href="/front/articles.php">Articles</a>
                                                <a class="dropdown-item" href="/front/news.php">News</a>
                                                <a class="dropdown-item" href="/front/price-trends.php">Price Trends</a>
                                                <a class="dropdown-item" href="/front/mobile.php">Mobile</a>
                                            </div>
                                        </li>
                                    </ul>
                            </div>
                        <?php else: ?>
                                    <!-- Buyer Account Dropdown -->
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenuBtn">
                                        <li class="dropdown-item-text text-muted small">
                                            <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></strong>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li class="dropdown-item-text small fw-semibold text-uppercase" style="letter-spacing:.04em;">My Activity</li>
                                        <li><a class="dropdown-item" href="/front/recently-searched.php">Recently Searched</a></li>
                                        <li><a class="dropdown-item" href="/front/recently-searched.php?tab=viewed">Recently Viewed</a></li>
                                        <li><a class="dropdown-item" href="/front/recently-searched.php?tab=shortlisted">Shortlisted</a></li>
                                        <li><a class="dropdown-item" href="/front/recently-searched.php?tab=contacted">Contacted</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="/front/modify-profile.php"><i class="bi bi-person"></i> My Profile</a></li>
                                        <li><a class="dropdown-item" href="#" data-change-password-open>Change Password</a></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); logoutUser();"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="https://tatitodevelopersandpromoters.com/admin/login.php"><i class="bi bi-building"></i> Post Property <span class="badge bg-warning text-dark">FREE</span></a></li>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
    <!-- Not Logged In - Single Combined Button -->
    <a href="#" class="btn-register" data-bs-toggle="modal" data-bs-target="#authModal" data-mode="login"
       style="background: <?php echo htmlspecialchars($headerSettings['register_btn_bg_color']); ?>; color: <?php echo htmlspecialchars($headerSettings['register_btn_text_color']); ?>;">
        <i class="bi bi-person"></i> Sign In / Register
    </a>
<?php endif; ?>
                        </div>
                    </div>
                </div>
            </nav>
                </div>
    </div>
    </div>
</header>

<!-- ===== ENQUIRY MODAL ===== -->
<div class="modal fade" id="enquiryModal" tabindex="-1" aria-labelledby="enquiryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="enquiryModalLabel">Send an Enquiry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <div id="enquiryFormAlert" class="alert" role="alert"></div>
                <form id="enquiryForm">
                    <h6 class="fw-bold mb-3">Your Details</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <input type="text" class="form-control" name="full_name" id="full_name" placeholder="Full Name *" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <input type="tel" class="form-control" name="mobile_number" id="mobile_number" placeholder="Mobile Number *" required>
                        </div>
                        <div class="col-12 mb-4">
                            <input type="email" class="form-control" name="email" id="email" placeholder="Email Address">
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3">Requirement Details</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <select class="form-select" name="property_type" id="property_type">
                                <option value="">Property Type *</option>
                                <option>Apartment</option><option>Villa</option><option>Plot</option>
                                <option>Commercial</option><option>Office</option><option>Retail Shop</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <select class="form-select" name="bhk_type" id="bhk_type">
                                <option value="">BHK Type *</option>
                                <option>1 BHK</option><option>2 BHK</option><option>3 BHK</option>
                                <option>4 BHK</option><option>5+ BHK</option><option>Studio</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <select class="form-select" name="budget_range" id="budget_range">
                                <option value="">Budget Range *</option>
                                <option>Below ₹25 Lakh</option><option>₹25L - ₹50L</option>
                                <option>₹50L - ₹75L</option><option>₹75L - ₹1 Cr</option>
                                <option>₹1 Cr - ₹2 Cr</option><option>Above ₹2 Cr</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <input type="text" class="form-control" name="preferred_locality" id="preferred_locality" placeholder="Preferred Locality">
                        </div>
                        <div class="col-12 mb-3">
                            <select class="form-select" name="move_timeline" id="move_timeline">
                                <option value="">When are you looking to move?</option>
                                <option>Immediately</option><option>Within 1 Month</option>
                                <option>Within 3 Months</option><option>Within 6 Months</option>
                                <option>After 6 Months</option><option>Just Exploring</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3">Additional Information</h6>
                    <div class="mb-3">
                        <textarea class="form-control" rows="4" maxlength="500" name="additional_info" id="additional_info" placeholder="Tell us more about your requirement (Optional)"></textarea>
                        <div class="text-end"><small><span id="charCount">0</span>/500</small></div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="consent" name="consent" value="1">
                        <label class="form-check-label" for="consent">I agree to be contacted by property experts for this enquiry.</label>
                    </div>

                    <input type="hidden" name="source_page" id="source_page" value="">
                    <input type="hidden" name="enquiry_type" id="enquiry_type" value="Enquiry">

                    <button class="btn btn-primary w-100 py-2 mb-2" id="enquirySubmitBtn" type="submit">Submit Enquiry</button>
                    <button type="button" class="btn btn-outline-primary w-100" id="callbackBtn"><i class="bi bi-telephone"></i> Request a Callback</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ===== WISHLIST OFFCANVAS ===== -->
<div class="offcanvas offcanvas-end wishlist-offcanvas" tabindex="-1" id="wishlistOffcanvas" aria-labelledby="wishlistOffcanvasLabel">
    <div class="offcanvas-header">
        <div class="d-flex align-items-start gap-2">
            <span class="offcanvas-header-icon"><i class="bi bi-heart-fill"></i></span>
            <div>
                <h5 class="offcanvas-title" id="wishlistOffcanvasLabel">Shortlisted Properties</h5>
                <div class="offcanvas-subtitle">Compare side by side</div>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">
        <div class="p-3 pb-0">
            <div class="wishlist-tab-switch">
                <button type="button" class="wishlist-tab-btn active" data-tab="buy">Buy</button>
                <button type="button" class="wishlist-tab-btn" data-tab="rent">Rent</button>
            </div>
        </div>
        <div class="flex-grow-1 overflow-auto p-3">
            <div id="wishlistLoading" class="text-center text-muted py-4">
                <div class="spinner-border spinner-border-sm me-2"></div> Loading...
            </div>
            <div id="wishlistEmpty" class="text-center py-3">
                <div class="wishlist-empty-illustration">
                    <i class="bi bi-buildings"></i>
                    <span class="heart-badge"><i class="bi bi-heart-fill"></i></span>
                </div>
                <h6 class="mt-2">No properties shortlisted yet</h6>
                <p class="mb-4 px-2">Shortlist properties to compare them side by side.</p>
                <a href="properties.php" class="explore-btn">Explore Properties</a>
            </div>
            <div id="wishlistItems"></div>
        </div>
        <div class="wishlist-bottom-bar">
            <button type="button" class="btn-enquire" id="wishlistEnquireBtn"><i class="bi bi-telephone me-1"></i> Enquire</button>
            <button type="button" class="btn-compare" id="wishlistCompareBtn" disabled><i class="bi bi-arrow-left-right me-1"></i> Compare</button>
        </div>
    </div>
</div>

<!-- ===== SCRIPTS ===== -->
<script>
// ===== TATITO USER STORAGE ISOLATION ARCHITECTURE =====
<?php
$headerStorageUid = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : (isset($_SESSION['id']) ? (int)$_SESSION['id'] : null);
?>
window.TATITO_USER_PREFIX = <?php echo json_encode($headerStorageUid ? ('tatito_user_' . $headerStorageUid) : 'tatito_guest'); ?>;
window.TATITO_USER_ID = <?php echo json_encode($headerStorageUid); ?>;

window.getTatitoStorageKey = function(type) {
    var prefix = window.TATITO_USER_PREFIX || 'tatito_guest';
    return prefix + '_' + type;
};

window.getWishlistIds = function() {
    try {
        var key = window.getTatitoStorageKey('wishlist');
        var raw = localStorage.getItem(key);
        var parsed = JSON.parse(raw);
        return Array.isArray(parsed) ? parsed : [];
    } catch(e) { return []; }
};

window.saveWishlistIds = function(list) {
    try {
        var key = window.getTatitoStorageKey('wishlist');
        var data = JSON.stringify(list || []);
        localStorage.setItem(key, data);
        document.dispatchEvent(new CustomEvent('wishlistUpdated', { detail: list }));
    } catch(e) {}
};

window.recordTatitoSearch = function(item) {
    if (!item) return;
    try {
        var key = window.getTatitoStorageKey('searches');
        var raw = localStorage.getItem(key);
        var list = [];
        try { list = JSON.parse(raw) || []; } catch(e) { list = []; }
        if (!Array.isArray(list)) list = [];

        var title = (item.title || item.city || item.q || item.query || item.search || '').trim();
        if (!title) return;

        // Ignore internal technical category slugs
        if (/^(vnd-|int-|srv-|cat-|type-)/i.test(title)) return;

        // Clean redundant labels
        title = title.replace(/^Buy\s+Buy\s+/i, 'Buy ')
                     .replace(/^Properties\s+Search/i, '')
                     .trim();
        if (!title) return;

        // Deduplicate
        list = list.filter(function(s) {
            var existingTitle = (s && (s.title || s.city || s.q || s.query || s.search || '')).trim().toLowerCase();
            return existingTitle !== title.toLowerCase();
        });

        item.title = title;
        item.at = item.at || Date.now();
        list.unshift(item);

        // Cap to 30 items
        if (list.length > 30) list = list.slice(0, 30);
        var data = JSON.stringify(list);
        localStorage.setItem(key, data);
        document.dispatchEvent(new CustomEvent('tatitoSearchRecorded', { detail: item }));
    } catch(e) {
        console.warn('Failed to record search:', e);
    }
};

window.recordTatitoViewed = function(item) {
    if (!item || !item.id) return;
    try {
        var key = window.getTatitoStorageKey('viewed');
        var raw = localStorage.getItem(key);
        var list = [];
        try { list = JSON.parse(raw) || []; } catch(e) { list = []; }
        if (!Array.isArray(list)) list = [];

        var idNum = Number(item.id);
        list = list.filter(function(v) {
            var vId = v && v.id != null ? Number(v.id) : null;
            return vId !== idNum;
        });

        item.at = item.at || Date.now();
        list.unshift(item);

        if (list.length > 30) list = list.slice(0, 30);
        var data = JSON.stringify(list);
        localStorage.setItem(key, data);
        document.dispatchEvent(new CustomEvent('tatitoViewedRecorded', { detail: item }));
    } catch(e) {
        console.warn('Failed to record viewed:', e);
    }
};

window.recordTatitoContacted = function(item) {
    if (!item) return;
    try {
        var key = window.getTatitoStorageKey('contacted');
        var raw = localStorage.getItem(key);
        var list = [];
        try { list = JSON.parse(raw) || []; } catch(e) { list = []; }
        if (!Array.isArray(list)) list = [];

        var pid = item.property_id || item.id || null;
        var pName = (item.project_name || item.title || '').trim().toLowerCase();

        list = list.filter(function(c) {
            if (pid && (c.property_id === pid || c.id === pid)) return false;
            if (pName && (c.project_name || c.title || '').trim().toLowerCase() === pName) return false;
            return true;
        });

        item.at = item.at || Date.now();
        list.unshift(item);

        if (list.length > 30) list = list.slice(0, 30);
        var data = JSON.stringify(list);
        localStorage.setItem(key, data);
        document.dispatchEvent(new CustomEvent('tatitoContactedRecorded', { detail: item }));
    } catch(e) {
        console.warn('Failed to record contacted:', e);
    }
};

// Dark/Light Mode Toggle
(function() {
    const THEME_KEY = 'tatito_theme';
    const toggleBtn = document.getElementById('themeToggleBtn');
    const toggleIcon = document.getElementById('themeToggleIcon');
    
    // Function to set theme
    function setTheme(theme) {
        if (theme === 'dark') {
            document.body.classList.add('dark-mode');
            toggleIcon.className = 'bi bi-sun-fill';
            localStorage.setItem(THEME_KEY, 'dark');
        } else {
            document.body.classList.remove('dark-mode');
            toggleIcon.className = 'bi bi-moon-fill';
            localStorage.setItem(THEME_KEY, 'light');
        }
    }
    
    // Get saved theme or system preference
    function getPreferredTheme() {
        const saved = localStorage.getItem(THEME_KEY);
        if (saved) return saved;
        // Check system preference
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            return 'dark';
        }
        return 'light';
    }
    
    // Initialize theme
    setTheme(getPreferredTheme());
    
    // Toggle on click
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const isDark = document.body.classList.contains('dark-mode');
            setTheme(isDark ? 'light' : 'dark');
        });
    }
    
    // Listen for system theme changes
    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
            // Only apply if user hasn't manually set a preference
            if (!localStorage.getItem(THEME_KEY)) {
                setTheme(e.matches ? 'dark' : 'light');
            }
        });
    }
})();

// Wishlist
(function() {
    const badge = document.getElementById('wishlistBadge');
    const btn = document.querySelector('.wishlist-btn');
    const loadingEl = document.getElementById('wishlistLoading');
    const emptyEl = document.getElementById('wishlistEmpty');
    const itemsEl = document.getElementById('wishlistItems');
    const panelEl = document.getElementById('wishlistOffcanvas');
    const compareBtn = document.getElementById('wishlistCompareBtn');
    const enquireBtn = document.getElementById('wishlistEnquireBtn');
    let selectedForCompare = [];

    function updateBadge() {
        const ids = getWishlistIds();
        if (ids.length > 0) {
            badge.textContent = ids.length;
            badge.classList.add('show');
            btn.classList.add('has-items');
        } else {
            badge.classList.remove('show');
            btn.classList.remove('has-items');
        }
    }

    function updateCompareButton() {
        compareBtn.disabled = selectedForCompare.length < 2;
    }

    function renderItems(projects) {
        itemsEl.innerHTML = '';
        selectedForCompare = [];
        updateCompareButton();
        projects.forEach(function(p) {
            const card = document.createElement('div');
            card.className = 'wishlist-card';
            card.innerHTML = `
                <input type="checkbox" class="form-check-input compare-check" data-id="${p.id}">
                <img src="${p.image}" alt="">
                <div class="info">
                    <h6>${p.project_name}</h6>
                    <div class="loc"><i class="bi bi-geo-alt"></i> ${p.address || ''}</div>
                    <div class="price">${p.price || ''}</div>
                </div>
                <a href="/front/property-listing/${p.slug}/" class="btn btn-sm btn-primary">View</a>
                <button type="button" class="remove-btn" data-id="${p.id}"><i class="bi bi-x-circle"></i></button>
            `;
            itemsEl.appendChild(card);
        });
        itemsEl.querySelectorAll('.compare-check').forEach(function(cb) {
            cb.addEventListener('change', function() {
                const id = String(this.dataset.id);
                if (this.checked) selectedForCompare.push(id);
                else selectedForCompare = selectedForCompare.filter(x => x !== id);
                updateCompareButton();
            });
        });
        itemsEl.querySelectorAll('.remove-btn').forEach(function(rBtn) {
            rBtn.addEventListener('click', function() {
                const id = String(this.dataset.id);
                let ids = getWishlistIds().filter(x => x !== id);
                saveWishlistIds(ids);
                updateBadge();
                loadWishlist();
            });
        });
    }

    function loadWishlist() {
        const ids = getWishlistIds();
        loadingEl.style.display = 'none';
        emptyEl.style.display = 'none';
        itemsEl.innerHTML = '';
        selectedForCompare = [];
        updateCompareButton();
        if (ids.length === 0) { emptyEl.style.display = 'block'; return; }
        loadingEl.style.display = 'block';
        fetch('/admin/api/wishlist-projects.php?ids=' + ids.join(','))
            .then(res => res.json())
            .then(function(data) {
                loadingEl.style.display = 'none';
                if (data.success && data.data.length > 0) renderItems(data.data);
                else emptyEl.style.display = 'block';
            })
            .catch(function() {
                loadingEl.style.display = 'none';
                emptyEl.style.display = 'block';
            });
    }

    document.querySelectorAll('.wishlist-tab-btn').forEach(function(tabBtn) {
        tabBtn.addEventListener('click', function() {
            document.querySelectorAll('.wishlist-tab-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    if (enquireBtn) {
        enquireBtn.addEventListener('click', function() {
            const offcanvasInst = bootstrap.Offcanvas.getOrCreateInstance(panelEl);
            offcanvasInst.hide();
            panelEl.addEventListener('hidden.bs.offcanvas', function openEnquiry() {
                panelEl.removeEventListener('hidden.bs.offcanvas', openEnquiry);
                bootstrap.Modal.getOrCreateInstance(document.getElementById('enquiryModal')).show();
            });
        });
    }

    if (compareBtn) {
        compareBtn.addEventListener('click', function() {
            if (selectedForCompare.length < 2) return;
            window.location.href = 'compare.php?ids=' + selectedForCompare.join(',');
        });
    }

    updateBadge();
    if (panelEl) panelEl.addEventListener('show.bs.offcanvas', loadWishlist);

    // NEW — let other pages (e.g. project-details.php) notify us after a change
    document.addEventListener('wishlistUpdated', function() {
        updateBadge();
        if (panelEl && panelEl.classList.contains('show')) loadWishlist();
    });
})();

// Enquiry Form
(function() {
    const form = document.getElementById("enquiryForm");
    if (!form) return;
    const alertBox = document.getElementById("enquiryFormAlert");
    const submitBtn = document.getElementById("enquirySubmitBtn");

    function showAlert(type, message) {
        alertBox.className = "alert alert-" + type;
        alertBox.innerHTML = message;
        alertBox.style.display = "block";
    }

    const textarea = document.getElementById("additional_info");
    const counter = document.getElementById("charCount");
    if (textarea && counter) {
        textarea.addEventListener("input", function() { counter.innerHTML = this.value.length; });
    }

    document.getElementById("callbackBtn").addEventListener("click", function() {
        document.getElementById("enquiry_type").value = "Callback";
        form.requestSubmit();
    });

    form.addEventListener("submit", function(e) {
        e.preventDefault();
        alertBox.style.display = "none";
        var payload = {
            full_name: document.getElementById("full_name").value.trim(),
            mobile_number: document.getElementById("mobile_number").value.trim(),
            email: document.getElementById("email").value.trim(),
            property_type: document.getElementById("property_type").value,
            bhk_type: document.getElementById("bhk_type").value,
            budget_range: document.getElementById("budget_range").value,
            preferred_locality: document.getElementById("preferred_locality").value,
            move_timeline: document.getElementById("move_timeline").value,
            additional_info: document.getElementById("additional_info").value,
            consent: document.getElementById("consent").checked ? 1 : 0,
            enquiry_type: document.getElementById("enquiry_type").value,
            source_page: window.location.href
        };
        if (payload.full_name == "" || payload.mobile_number == "") {
            showAlert("warning", "Please enter Full Name and Mobile Number.");
            return;
        }
        submitBtn.disabled = true;
        submitBtn.innerHTML = "Submitting...";
        fetch("includes/submit-enquiry.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(function(response) {
            if (response.success) {
                showAlert("success", response.message);
                form.reset();
                counter.innerHTML = 0;
                document.getElementById("enquiry_type").value = "Enquiry";
            } else {
                showAlert("danger", response.message);
            }
        })
        .catch(function() { showAlert("danger", "Server error."); })
        .finally(function() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = "Submit Enquiry";
        });
    });
})();

// City Picker
(function() {
    const STORAGE_KEY = 'tatito_selected_city';
    const label = document.getElementById('cityPickerLabel');
    const toggleBtn = document.getElementById('cityPickerBtn');
    const searchInput = document.getElementById('cityPickerSearch');
    const resultsWrap = document.getElementById('cityPickerResults');
    const noResults = document.getElementById('cityPickerNoResults');
    const topGrid = document.getElementById('topCitiesGrid');
    const otherList = document.getElementById('otherCitiesList');
    const detectBtn = document.getElementById('detectLocationBtn');
    const resetBtn = document.getElementById('resetCityBtn');
    if (!label) return;

    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved) {
        label.textContent = saved;
        if (typeof window.recordTatitoSearch === 'function') {
            window.recordTatitoSearch({
                q: '',
                city: saved,
                title: saved,
                url: '/front/properties.php?city=' + encodeURIComponent(saved)
            });
        }
    }

    function closeDropdown() {
        if (window.bootstrap && toggleBtn) {
            const inst = bootstrap.Dropdown.getOrCreateInstance(toggleBtn);
            inst.hide();
        }
    }

    function selectCity(city, opts) {
        opts = opts || {};
        label.textContent = city;
        localStorage.setItem(STORAGE_KEY, city);
        if (typeof window.recordTatitoSearch === 'function') {
            window.recordTatitoSearch({
                q: '',
                city: city,
                title: city,
                url: '/front/properties.php?city=' + encodeURIComponent(city),
                at: Date.now()
            });
        }
        closeDropdown();
        if (opts.navigate !== false) {
            window.location.href = '/front/properties.php?city=' + encodeURIComponent(city);
        }
    }

    document.querySelectorAll('.city-option-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            selectCity(this.dataset.city);
        });
    });

    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            localStorage.removeItem(STORAGE_KEY);
            label.textContent = 'Search city...';
            searchInput.value = '';
            filterCities('');
            if (pincodeInput) pincodeInput.value = '';
            if (typeof setMode === 'function') setMode('name');
        });
    }

    function filterCities(query) {
        query = query.trim().toLowerCase();
        let anyVisible = false;
        document.querySelectorAll('.city-option-btn').forEach(function(btn) {
            const matches = btn.dataset.city.toLowerCase().includes(query);
            btn.style.display = matches ? '' : 'none';
            if (matches) anyVisible = true;
        });
        if (topGrid) {
            const topHasVisible = Array.from(topGrid.querySelectorAll('.city-option-btn')).some(b => b.style.display !== 'none');
            topGrid.style.display = topHasVisible ? '' : 'none';
            const topLabel = topGrid.previousElementSibling;
            if (topLabel && topLabel.classList.contains('city-picker-section-label')) topLabel.style.display = topHasVisible ? '' : 'none';
        }
        if (otherList) {
            const otherHasVisible = Array.from(otherList.querySelectorAll('.city-option-btn')).some(b => b.style.display !== 'none');
            otherList.style.display = otherHasVisible ? '' : 'none';
            const otherLabel = otherList.previousElementSibling;
            if (otherLabel && otherLabel.classList.contains('city-picker-section-label')) otherLabel.style.display = otherHasVisible ? '' : 'none';
        }
        noResults.style.display = anyVisible ? 'none' : 'block';
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() { filterCities(this.value); });
        searchInput.addEventListener('keydown', function(e) {
            if (e.key !== 'Enter') return;
            e.preventDefault();
            const firstMatch = Array.from(document.querySelectorAll('.city-option-btn')).find(btn => btn.style.display !== 'none');
            if (firstMatch) selectCity(firstMatch.dataset.city);
        });
    }

    const modeByNameBtn = document.getElementById('cityModeByName');
    const modeByPincodeBtn = document.getElementById('cityModeByPincode');
    const nameWrap = document.getElementById('cityPickerNameWrap');
    const pincodeWrap = document.getElementById('cityPickerPincodeWrap');
    const pincodeInput = document.getElementById('cityPickerPincode');
    const pincodeGoBtn = document.getElementById('cityPickerPincodeGo');
    const pincodeStatus = document.getElementById('cityPickerPincodeStatus');
    const actionsRow = document.getElementById('cityPickerActions');

    function setPincodeStatus(text, type) {
        pincodeStatus.textContent = text || '';
        pincodeStatus.classList.remove('error', 'success');
        if (type) pincodeStatus.classList.add(type);
    }

    function setMode(mode) {
        const isPincode = mode === 'pincode';
        modeByNameBtn.classList.toggle('active', !isPincode);
        modeByPincodeBtn.classList.toggle('active', isPincode);
        nameWrap.style.display = isPincode ? 'none' : '';
        pincodeWrap.style.display = isPincode ? 'block' : 'none';
        if (actionsRow) actionsRow.style.display = isPincode ? 'none' : '';
        if (resultsWrap) resultsWrap.style.display = isPincode ? 'none' : '';
        if (noResults) noResults.style.display = 'none';
        setPincodeStatus('');
        if (isPincode) {
            setTimeout(function() { pincodeInput.focus(); }, 50);
        } else {
            searchInput.value = '';
            filterCities('');
        }
    }

    if (modeByNameBtn && modeByPincodeBtn) {
        modeByNameBtn.addEventListener('click', function() { setMode('name'); });
        modeByPincodeBtn.addEventListener('click', function() { setMode('pincode'); });
    }

    function lookupPincode() {
        const pin = (pincodeInput.value || '').trim();
        if (!/^\d{6}$/.test(pin)) {
            setPincodeStatus('Enter a valid 6-digit pincode.', 'error');
            return;
        }
        setPincodeStatus('Looking up...', '');
        pincodeGoBtn.disabled = true;
        fetch('includes/pincode-lookup.php?pincode=' + encodeURIComponent(pin))
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data && data.success && data.city) {
                    setPincodeStatus('Found: ' + data.city, 'success');
                    selectCity(data.city);
                } else {
                    setPincodeStatus((data && data.message) || 'No city found.', 'error');
                }
            })
            .catch(function() {
                setPincodeStatus('Could not look up pincode.', 'error');
            })
            .finally(function() {
                pincodeGoBtn.disabled = false;
            });
    }

    if (pincodeGoBtn) pincodeGoBtn.addEventListener('click', lookupPincode);
    if (pincodeInput) {
        pincodeInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') { e.preventDefault();
                lookupPincode(); }
        });
        pincodeInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
        });
    }

    if (detectBtn) {
        detectBtn.addEventListener('click', function() {
            if (!navigator.geolocation) { alert('Location detection not supported.'); return; }
            const originalText = detectBtn.innerHTML;
            detectBtn.innerHTML = '<i class="bi bi-crosshair"></i> Detecting...';
            navigator.geolocation.getCurrentPosition(
                function() {
                    detectBtn.innerHTML = originalText;
                    alert('Location detected. Please select your city from the list.');
                },
                function() {
                    detectBtn.innerHTML = originalText;
                    alert('Could not detect location. Please select manually.');
                }
            );
        });
    }
})();

// ===== Font size adjuster =====
(function() {
    const FONT_KEY = 'tatito_font_size';
    const buttons = document.querySelectorAll('.font-size-switch button');
    if (!buttons.length) return;
    const sizes = { sm: '87.5%', md: '100%', lg: '112.5%' };

    function applySize(size) {
        document.documentElement.style.fontSize = sizes[size] || sizes.md;
        buttons.forEach(function(b) { b.classList.toggle('active', b.dataset.size === size); });
        localStorage.setItem(FONT_KEY, size);
    }

    buttons.forEach(function(btn) {
        btn.addEventListener('click', function() { applySize(this.dataset.size); });
    });

    applySize(localStorage.getItem(FONT_KEY) || 'md');
})();

// ===== Language switcher with Google Translate =====
(function() {
    const LANG_KEY = 'tatito_lang';
    const label = document.getElementById('langSwitchLabel');
    const options = document.querySelectorAll('.lang-option');
    if (!label) return;

    // Language mapping for Google Translate
    const langMap = {
        'en': 'en',
        'hi': 'hi',
        'mr': 'mr',
        'gu': 'gu',
        'bn': 'bn',
        'ta': 'ta',
        'te': 'te',
        'kn': 'kn',
        'ml': 'ml',
        'pa': 'pa',
        'or': 'or',
        'as': 'as',
        'es': 'es',
        'fr': 'fr',
        'de': 'de',
        'ar': 'ar',
        'zh-CN': 'zh-CN',
        'ja': 'ja',
        'ko': 'ko',
        'ru': 'ru'
    };

    function setLang(lang, langLabel) {
        label.textContent = langLabel;
        localStorage.setItem(LANG_KEY, JSON.stringify({ lang: lang, label: langLabel }));
        options.forEach(function(opt) {
            opt.classList.toggle('active', opt.dataset.lang === lang);
        });

        // Trigger Google Translate
        const googleLangCode = langMap[lang] || 'en';
        if (googleLangCode !== 'en') {
            // Wait a bit for Google Translate to be ready
            setTimeout(function() {
                // Try to find the Google Translate select element
                const select = document.querySelector('.goog-te-combo');
                if (select) {
                    select.value = googleLangCode;
                    select.dispatchEvent(new Event('change'));
                    localStorage.setItem('tatito_lang_code', googleLangCode);
                } else {
                    // If not found, try again after a delay
                    setTimeout(function() {
                        const select2 = document.querySelector('.goog-te-combo');
                        if (select2) {
                            select2.value = googleLangCode;
                            select2.dispatchEvent(new Event('change'));
                            localStorage.setItem('tatito_lang_code', googleLangCode);
                        }
                    }, 1000);
                }
            }, 500);
        } else {
            // Reset to English
            const select = document.querySelector('.goog-te-combo');
            if (select) {
                select.value = 'en';
                select.dispatchEvent(new Event('change'));
                localStorage.removeItem('tatito_lang_code');
            }
        }
    }

    options.forEach(function(opt) {
        opt.addEventListener('click', function(e) {
            e.preventDefault();
            setLang(this.dataset.lang, this.dataset.label);
        });
    });

    try {
        const saved = JSON.parse(localStorage.getItem(LANG_KEY));
        if (saved && saved.lang) setLang(saved.lang, saved.label);
        else setLang('en', 'English');
    } catch (e) { setLang('en', 'English'); }
})();

function logoutUser(allDevices) {
    const msg = allDevices ? 'Logout from ALL devices?' : 'Are you sure you want to logout?';
    if (confirm(msg)) {
        const url = '/front/api/auth.php?action=logout' + (allDevices ? '&all=1' : '');
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) window.location.href = '/front/index.php';
            else alert('Logout failed.');
        })
        .catch(error => { console.error('Logout error:', error);
            alert('Network error.'); });
    }
}
</script>
<script>
// ===== Overflow "More" menu for main nav =====
(function() {
    const navList = document.getElementById('mainNavList');
    const moreItem = document.getElementById('moreNavItem');
    const moreMenu = document.getElementById('moreNavMenu');
    if (!navList || !moreItem) return;
    const originalItems = Array.from(navList.children).filter(li => li !== moreItem);

    function reflow() {
        if (window.innerWidth < 992) { // matches navbar-expand-xxl
            moreItem.style.display = 'none';
            originalItems.forEach(li => { li.style.display = ''; navList.insertBefore(li, moreItem); }); // reset any moves from desktop mode
            return;
        }
        originalItems.forEach(li => { li.style.display = ''; navList.insertBefore(li, moreItem); });
        moreMenu.innerHTML = '';
        moreItem.style.display = 'none';

        const available = navList.clientWidth;
        const reserve = 90;
        let used = 0;
        originalItems.forEach(li => { used += li.offsetWidth; });

        if (used > available) {
            const overflowed = [];
            for (let i = originalItems.length - 1; i >= 0; i--) {
                used -= originalItems[i].offsetWidth;
                overflowed.unshift(originalItems[i]);
                if (used + reserve <= available) break;
            }
            overflowed.forEach(li => {
                li.classList.add('in-more-menu');
                moreMenu.appendChild(li); // move the actual li (with its mega-menu) into More
            });
            moreItem.style.display = '';
        }
    }
    window.addEventListener('resize', reflow);
    window.addEventListener('load', reflow);
    reflow();
})();
</script>
<!-- Include Auth Modal -->
<?php require_once __DIR__ . '/auth-modal.php'; ?>

<!-- Include Change Password Modal (site-wide) — guarded until the file exists -->
<?php if (file_exists(__DIR__ . '/change-password-modal.php')): ?>
    <?php require_once __DIR__ . '/change-password-modal.php'; ?>
<?php endif; ?>