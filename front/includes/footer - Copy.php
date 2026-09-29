<?php
if (!isset($site)) {
    require_once __DIR__ . '/config.php';
}

/* ----------------------------------------
   Footer Settings (Real EstateHub Style)
---------------------------------------- */

$footer = [
    'bg_color' => '#0f1f38',
    'text_color' => '#c9d3e0',
    'heading_color' => '#ffffff',
    'accent_color' => '#f5a623',
    'brand_name' => 'Real EstateHub',
    'brand_tagline' => "Empowering every citizen with transparent, accessible, and efficient real estate solutions through cutting-edge technology and a network of trusted real estate professionals.",
    'social_facebook' => '#',
    'social_instagram' => '#',
    'social_twitter' => '#',
    'social_linkedin' => '#',
    'copyright_text' => '© 2025 Real EstateHub. All Rights Reserved.'
];

$result = $conn->query("SELECT * FROM footer_settings ORDER BY id DESC LIMIT 1");

if ($result && $result->num_rows > 0) {
    $footer = array_merge($footer, $result->fetch_assoc());
}

/* ----------------------------------------
   Footer Columns - Real EstateHub Structure
---------------------------------------- */

$footerColumns = [];

$columnQuery = $conn->query("
SELECT *
FROM footer_columns
WHERE status='Active'
ORDER BY column_order ASC
");

while ($column = $columnQuery->fetch_assoc()) {

    $column['links'] = [];

    $linkQuery = $conn->query("
    SELECT *
    FROM footer_links
    WHERE column_id=" . (int)$column['id'] . "
    AND status='Active'
    ORDER BY link_order ASC
    ");

    while ($link = $linkQuery->fetch_assoc()) {
        $column['links'][] = $link;
    }

    $footerColumns[] = $column;
}
?>

<style>
/* ============================================
   DARK MODE SUPPORT - Footer
   ============================================ */
body.dark-mode .sy-footer {
    background: var(--dm-bg-secondary, #161b22) !important;
}

body.dark-mode .sy-footer [style*="background"] {
    background: var(--dm-bg-secondary, #161b22) !important;
}

body.dark-mode .sy-footer h3,
body.dark-mode .sy-footer h6,
body.dark-mode .sy-footer .brand-name,
body.dark-mode .sy-footer .footer-brand h3 {
    color: var(--dm-text-primary, #f0f6fc) !important;
}

body.dark-mode .sy-footer p,
body.dark-mode .sy-footer div,
body.dark-mode .sy-footer span,
body.dark-mode .sy-footer .text-muted {
    color: var(--dm-text-secondary, #8b949e) !important;
}

body.dark-mode .sy-footer a {
    color: var(--dm-text-secondary, #8b949e) !important;
}

body.dark-mode .sy-footer a:hover {
    color: var(--sy-gold, #f0b429) !important;
}

body.dark-mode .sy-footer hr {
    border-color: var(--dm-border, #30363d) !important;
}

body.dark-mode .sy-footer .bottom-bar {
    border-color: var(--dm-border, #30363d) !important;
}

body.dark-mode .sy-footer .bottom-bar a {
    color: var(--dm-text-secondary, #8b949e) !important;
}

body.dark-mode .sy-footer .bottom-bar a:hover {
    color: var(--sy-gold, #f0b429) !important;
}

body.dark-mode .sy-footer .approval-badge,
body.dark-mode .sy-footer .badge-app,
body.dark-mode .sy-footer span[style*="background:rgba(255,255,255,0.06)"] {
    background: var(--dm-bg-tertiary, #1c2333) !important;
    border-color: var(--dm-border, #30363d) !important;
    color: var(--dm-text-secondary, #8b949e) !important;
}

body.dark-mode .sy-footer .footer-logo img {
    filter: brightness(0.8) invert(1) !important;
}

body.dark-mode .sy-footer .brand-dot {
    background: var(--sy-gold, #f0b429) !important;
}

body.dark-mode .sy-footer .social-icons a {
    color: var(--dm-text-secondary, #8b949e) !important;
}

body.dark-mode .sy-footer .social-icons a:hover {
    color: var(--sy-gold, #f0b429) !important;
}

body.dark-mode .sy-footer .stat-number {
    color: var(--dm-text-primary, #f0f6fc) !important;
}

body.dark-mode .sy-footer .stat-label {
    color: var(--dm-text-secondary, #8b949e) !important;
}

body.dark-mode .sy-footer .app-store-badge,
body.dark-mode .sy-footer .google-play-badge {
    background: var(--dm-bg-tertiary, #1c2333) !important;
    border-color: var(--dm-border, #30363d) !important;
    color: var(--dm-text-secondary, #8b949e) !important;
}

body.dark-mode .sy-footer .app-store-badge:hover,
body.dark-mode .sy-footer .google-play-badge:hover {
    background: var(--dm-border, #30363d) !important;
}

body.dark-mode .sy-footer .collab-badge {
    color: var(--dm-text-secondary, #8b949e) !important;
}

body.dark-mode .sy-footer .feature-item {
    color: var(--dm-text-secondary, #8b949e) !important;
}

body.dark-mode .sy-footer .contact-info {
    color: var(--dm-text-secondary, #8b949e) !important;
}

body.dark-mode .sy-footer .copyright-text {
    color: var(--dm-text-secondary, #8b949e) !important;
}
</style>

<footer class="sy-footer pt-5 pb-3"
style="
background:<?= htmlspecialchars($footer['bg_color']) ?>;
color:<?= htmlspecialchars($footer['text_color']) ?>;
transition: background 0.3s ease, color 0.3s ease;
">

<div class="container">

<!-- ============================================================
     ROW 1: Logo + 3 Menu Columns (Explore, More, For Buyers)
============================================================ -->

<div class="row g-4">

    <!-- ===== COLUMN 1: LOGO + BRAND ===== -->
    <div class="col-12 col-md-6 col-lg-3">
        <div class="footer-brand">
            <!-- Logo -->
            <div class="footer-logo mb-3">
                <img src="/front/assets/images/logo.jpeg" 
                     alt="Real EstateHub" 
                     style="height:50px; width:auto; filter:brightness(0) invert(1); transition: filter 0.3s ease;">
            </div>
            
            <!-- Brand Name -->
            <h3 style="color:<?= htmlspecialchars($footer['heading_color']) ?>; font-weight:700; font-size:1.5rem; margin-bottom:0.25rem; transition: color 0.3s ease;">
                <?= htmlspecialchars($footer['brand_name']) ?>
            </h3>
            
            <!-- Tagline -->
            <p style="font-size:0.75rem; opacity:0.6; text-transform:uppercase; letter-spacing:1px; margin-bottom:1rem; transition: color 0.3s ease;">
                India's Premier Real Estate Platform
            </p>
            
            <!-- Description -->
            <p style="font-size:0.85rem; opacity:0.85; max-width:280px; line-height:1.6; transition: color 0.3s ease;">
                <?= htmlspecialchars($footer['brand_tagline']) ?>
            </p>
            
        </div>
    </div>

    <!-- ===== COLUMN 2: Explore ===== -->
    <div class="col-6 col-md-3 col-lg-3">
        <h6 style="color:<?= htmlspecialchars($footer['heading_color']) ?>; font-weight:600; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:14px; transition: color 0.3s ease;">
            Explore
        </h6>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Home Page</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Premium Services</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Find Properties</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Browse Profiles</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Careers</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">How It Works</a>
    </div>

    <!-- ===== COLUMN 3: More ===== -->
    <div class="col-6 col-md-3 col-lg-3">
        <h6 style="color:<?= htmlspecialchars($footer['heading_color']) ?>; font-weight:600; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:14px; transition: color 0.3s ease;">
            More
        </h6>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">List a Property</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Property Status</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Documentation</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Create Blog</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Credits</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Site Map</a>
    </div>

    <!-- ===== COLUMN 4: For Buyers ===== -->
    <div class="col-6 col-md-3 col-lg-3">
        <h6 style="color:<?= htmlspecialchars($footer['heading_color']) ?>; font-weight:600; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:14px; transition: color 0.3s ease;">
            For Buyers
        </h6>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Search Properties</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Buyer How It Works</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Mortgage Calculator</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Buyer Guides</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">New Launches</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Book a Site Visit</a>
    </div>

</div>

<hr style="border-color:rgba(255,255,255,0.08); margin:30px 0; transition: border-color 0.3s ease;">

<!-- ============================================================
     ROW 2: 4 Columns (For Sellers, For Agents, Help, Legal)
============================================================ -->

<div class="row g-4">

    <!-- ===== COLUMN 1: For Sellers ===== -->
    <div class="col-6 col-md-3 col-lg-3">
        <h6 style="color:<?= htmlspecialchars($footer['heading_color']) ?>; font-weight:600; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:14px; transition: color 0.3s ease;">
            For Sellers
        </h6>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">List Your Property</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Seller How It Works</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Property Valuation</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Seller Guides</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Selling Tips</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Compare Offers</a>
    </div>

    <!-- ===== COLUMN 2: For Agents ===== -->
    <div class="col-6 col-md-3 col-lg-3">
        <h6 style="color:<?= htmlspecialchars($footer['heading_color']) ?>; font-weight:600; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:14px; transition: color 0.3s ease;">
            For Agents
        </h6>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Agent Registration</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Agent Dashboard</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Lead Management</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Marketing Tools</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Agent Resources</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Commission Calculator</a>
    </div>

    <!-- ===== COLUMN 3: Help ===== -->
    <div class="col-6 col-md-3 col-lg-3">
        <h6 style="color:<?= htmlspecialchars($footer['heading_color']) ?>; font-weight:600; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:14px; transition: color 0.3s ease;">
            Help
        </h6>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Help Center</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">FAQ</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Terms of Use</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Refund Policy</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Grievances</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Report an Issue</a>
    </div>

    <!-- ===== COLUMN 4: Legal ===== -->
    <div class="col-6 col-md-3 col-lg-3">
        <h6 style="color:<?= htmlspecialchars($footer['heading_color']) ?>; font-weight:600; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:14px; transition: color 0.3s ease;">
            Legal
        </h6>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Privacy Policy</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Cookie Policy</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Fraud Alert</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">RERA Compliance</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Disclaimer</a>
        <a href="#" style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Copyright Policy</a>
    </div>

</div>

<hr style="border-color:rgba(255,255,255,0.08); margin:30px 0; transition: border-color 0.3s ease;">

<!-- ============================================================
     APPROVED BY | PARTNERSHIP | COLLABORATION | ASSOCIATION
============================================================ -->

<div class="row g-4 mb-4">
    <div class="col-12">
        <div style="display:flex; flex-wrap:wrap; gap:20px 40px; justify-content:flex-start; align-items:center;">
            
            <!-- Approved By -->
            <div>
                <span style="font-size:0.65rem; text-transform:uppercase; opacity:0.5; letter-spacing:0.5px; display:block; margin-bottom:6px; transition: color 0.3s ease;">APPROVED BY</span>
                <div style="display:flex; flex-wrap:wrap; gap:12px; align-items:center;">
                    <span style="background:rgba(255,255,255,0.06); padding:4px 14px; border-radius:15px; font-size:0.75rem; border:1px solid rgba(255,255,255,0.08); transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;">🏢 RERA</span>
                    <span style="background:rgba(255,255,255,0.06); padding:4px 14px; border-radius:15px; font-size:0.75rem; border:1px solid rgba(255,255,255,0.08); transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;">📋 CREDAI</span>
                </div>
            </div>

            <!-- Partnership -->
            <div>
                <span style="font-size:0.65rem; text-transform:uppercase; opacity:0.5; letter-spacing:0.5px; display:block; margin-bottom:6px; transition: color 0.3s ease;">PARTNERSHIP</span>
                <div style="display:flex; flex-wrap:wrap; gap:12px; align-items:center;">
                    <span style="background:rgba(255,255,255,0.06); padding:4px 14px; border-radius:15px; font-size:0.75rem; border:1px solid rgba(255,255,255,0.08); transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;">🤝 NAR India</span>
                    <span style="background:rgba(255,255,255,0.06); padding:4px 14px; border-radius:15px; font-size:0.75rem; border:1px solid rgba(255,255,255,0.08); transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;">🏗️ Builders Association</span>
                </div>
            </div>

            <!-- Collaboration -->
            <div>
                <span style="font-size:0.65rem; text-transform:uppercase; opacity:0.5; letter-spacing:0.5px; display:block; margin-bottom:6px; transition: color 0.3s ease;">COLLABORATION</span>
                <div style="display:flex; flex-wrap:wrap; gap:12px; align-items:center;">
                    <span style="background:rgba(255,255,255,0.06); padding:4px 14px; border-radius:15px; font-size:0.75rem; border:1px solid rgba(255,255,255,0.08); transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;">📐 Architects Forum</span>
                    <span style="background:rgba(255,255,255,0.06); padding:4px 14px; border-radius:15px; font-size:0.75rem; border:1px solid rgba(255,255,255,0.08); transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;">📊 PropTech Alliance</span>
                </div>
            </div>

            <!-- Association -->
            <div>
                <span style="font-size:0.65rem; text-transform:uppercase; opacity:0.5; letter-spacing:0.5px; display:block; margin-bottom:6px; transition: color 0.3s ease;">ASSOCIATION</span>
                <div style="display:flex; flex-wrap:wrap; gap:12px; align-items:center;">
                    <span style="background:rgba(255,255,255,0.06); padding:4px 14px; border-radius:15px; font-size:0.75rem; border:1px solid rgba(255,255,255,0.08); transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;">⭐ REALTORS®</span>
                    <span style="background:rgba(255,255,255,0.06); padding:4px 14px; border-radius:15px; font-size:0.75rem; border:1px solid rgba(255,255,255,0.08); transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;">📈 FIABCI</span>
                </div>
            </div>

        </div>
    </div>
</div>

<hr style="border-color:rgba(255,255,255,0.08); margin:30px 0; transition: border-color 0.3s ease;">

<!-- ============================================================
     ROW 3: 3 Columns (Download Our App | Connect With Us | Platform Statistics)
============================================================ -->

<div class="row g-4">

    <!-- ===== COLUMN 1: Download Our App ===== -->
    <div class="col-md-4">
        <h6 style="color:<?= htmlspecialchars($footer['heading_color']) ?>; font-weight:600; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px; transition: color 0.3s ease;">
            DOWNLOAD OUR APP
        </h6>

        <div style="display:flex; flex-direction:column; gap:8px; margin-bottom:12px;">
            <span style="background:rgba(255,255,255,0.06); padding:8px 16px; border-radius:20px; font-size:0.8rem; color:<?= htmlspecialchars($footer['text_color']) ?>; cursor:pointer; border:1px solid rgba(255,255,255,0.08); text-align:center; width:fit-content; transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;">
                📱 Download on the App Store
            </span>
            <span style="background:rgba(255,255,255,0.06); padding:8px 16px; border-radius:20px; font-size:0.8rem; color:<?= htmlspecialchars($footer['text_color']) ?>; cursor:pointer; border:1px solid rgba(255,255,255,0.08); text-align:center; width:fit-content; transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;">
                ▶️ Get it on Google Play
            </span>
        </div>

        <p style="font-size:0.85rem; opacity:0.85; line-height:1.6; transition: color 0.3s ease;">
            Experience real estate convenience at your fingertips. Our mobile app is designed to provide you with seamless access to property experts, real-time listings, and secure document management anytime, anywhere.
        </p>

        <div style="font-size:0.8rem; opacity:0.8; line-height:1.8; transition: color 0.3s ease;">
            <div>✅ Real-time Property Listings</div>
            <div>✅ Secure Document Vault</div>
            <div>✅ Instant Agent Chat</div>
            <div>✅ 24/7 Property Assistance</div>
        </div>
    </div>

    <!-- ===== COLUMN 2: Connect With Us ===== -->
    <div class="col-md-4">
        <h6 style="color:<?= htmlspecialchars($footer['heading_color']) ?>; font-weight:600; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px; transition: color 0.3s ease;">
            CONNECT WITH US
        </h6>

        <div style="font-size:0.85rem; opacity:0.85; margin-bottom:12px; line-height:1.8; transition: color 0.3s ease;">
            <div>📧 info.realestatehub@gmail.com</div>
            <div>📧 support@realestatehub.com</div>
            <div>📞 +91 98765 43210</div>
        </div>

        <p style="font-size:0.85rem; opacity:0.85; line-height:1.6; transition: color 0.3s ease;">
            Empowering every citizen with transparent, accessible, and efficient real estate solutions through cutting-edge technology and a network of trusted real estate professionals.
        </p>

        <div style="font-size:0.8rem; opacity:0.8; display:flex; flex-direction:column; gap:4px; transition: color 0.3s ease;">
            <span>🔒 256-bit SSL Secure Platform</span>
            <span>🕒 24/7 Priority Support for Members</span>
        </div>

        <!-- Brand Name -->
        <div style="margin-top:12px;">
            <span style="display:inline-block;width:8px;height:8px;background:<?= htmlspecialchars($footer['accent_color']) ?>;border-radius:50%;margin-right:8px; transition: background 0.3s ease;"></span>
            <span style="color:<?= htmlspecialchars($footer['heading_color']) ?>; font-weight:700; font-size:1.1rem; transition: color 0.3s ease;">
                <?= htmlspecialchars($footer['brand_name']) ?>
            </span>
            <span style="font-size:0.75rem; opacity:0.5; display:block; margin-top:2px; transition: color 0.3s ease;">India's Premier Real Estate Platform</span>
        </div>
    </div>

    <!-- ===== COLUMN 3: Platform Statistics ===== -->
    <div class="col-md-4">
        <h6 style="color:<?= htmlspecialchars($footer['heading_color']) ?>; font-weight:600; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px; transition: color 0.3s ease;">
            PLATFORM STATISTICS
        </h6>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px 20px;">

            <div>
                <span style="color:<?= htmlspecialchars($footer['heading_color']) ?>; font-weight:700; font-size:1.1rem; transition: color 0.3s ease;">5,420+</span>
                <span style="font-size:0.75rem; opacity:0.6; display:block; transition: color 0.3s ease;">Total Properties</span>
            </div>

            <div>
                <span style="color:<?= htmlspecialchars($footer['heading_color']) ?>; font-weight:700; font-size:1.1rem; transition: color 0.3s ease;">3,280+</span>
                <span style="font-size:0.75rem; opacity:0.6; display:block; transition: color 0.3s ease;">Total Clients</span>
            </div>

            <div>
                <span style="color:<?= htmlspecialchars($footer['heading_color']) ?>; font-weight:700; font-size:1.1rem; transition: color 0.3s ease;">1,850+</span>
                <span style="font-size:0.75rem; opacity:0.6; display:block; transition: color 0.3s ease;">Total Agents</span>
            </div>

            <div>
                <span style="color:<?= htmlspecialchars($footer['heading_color']) ?>; font-weight:700; font-size:1.1rem; transition: color 0.3s ease;">12,940+</span>
                <span style="font-size:0.75rem; opacity:0.6; display:block; transition: color 0.3s ease;">Property Views</span>
            </div>

            <div>
                <span style="color:<?= htmlspecialchars($footer['heading_color']) ?>; font-weight:700; font-size:1.1rem; transition: color 0.3s ease;">2,670+</span>
                <span style="font-size:0.75rem; opacity:0.6; display:block; transition: color 0.3s ease;">Shortlisted</span>
            </div>

            <div>
                <span style="color:<?= htmlspecialchars($footer['heading_color']) ?>; font-weight:700; font-size:1.1rem; transition: color 0.3s ease;">4,520+</span>
                <span style="font-size:0.75rem; opacity:0.6; display:block; transition: color 0.3s ease;">Enquiries</span>
            </div>

        </div>
    </div>

</div>

<!-- ============================================================
     LEGAL LINKS (Bottom Bar)
============================================================ -->

<?php
$legalQuery = $conn->query("
SELECT *
FROM footer_legal_links
ORDER BY link_order ASC
");
?>

<div class="bottom-bar mt-4 pt-3" style="border-top:1px solid rgba(255,255,255,0.06); transition: border-color 0.3s ease;">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">

        <div style="font-size:0.75rem; opacity:0.6; transition: color 0.3s ease;">
            <?= htmlspecialchars($footer['copyright_text']) ?>
        </div>

        <div class="d-flex flex-wrap gap-3">

            <?php
            if($legalQuery && $legalQuery->num_rows){
                while($legal=$legalQuery->fetch_assoc()){
            ?>

            <a href="<?= htmlspecialchars($legal['url']) ?>" 
               style="color:<?= htmlspecialchars($footer['text_color']) ?>; font-size:0.75rem; text-decoration:none; opacity:0.6; transition:opacity 0.2s, color 0.2s;" 
               onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" 
               onmouseout="this.style.opacity='0.6'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">
                <?= htmlspecialchars($legal['label']) ?>
            </a>

            <?php
                }
            } else {
                // Fallback legal links
            ?>
            <a href="#" style="color:<?= htmlspecialchars($footer['text_color']) ?>; font-size:0.75rem; text-decoration:none; opacity:0.6; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.6'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Privacy Policy</a>
            <a href="#" style="color:<?= htmlspecialchars($footer['text_color']) ?>; font-size:0.75rem; text-decoration:none; opacity:0.6; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.6'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Terms of Use</a>
            <a href="#" style="color:<?= htmlspecialchars($footer['text_color']) ?>; font-size:0.75rem; text-decoration:none; opacity:0.6; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.6'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Cookie Policy</a>
            <a href="#" style="color:<?= htmlspecialchars($footer['text_color']) ?>; font-size:0.75rem; text-decoration:none; opacity:0.6; transition:opacity 0.2s, color 0.2s;" onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'" onmouseout="this.style.opacity='0.6'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">Sitemap</a>
            <?php
            }
            ?>

        </div>

    </div>

</div>

</div>

</footer>

<script>
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