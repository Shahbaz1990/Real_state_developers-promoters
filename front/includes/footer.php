<?php
if (!isset($site)) {
    require_once __DIR__ . '/config.php';
}

/* ----------------------------------------
   Footer Settings (Real Tatito Developers And Promoters Style)
   -> matches `footer_settings` table
---------------------------------------- */

$footer = [
    'bg_color' => '#0f1f38',
    'text_color' => '#c9d3e0',
    'heading_color' => '#ffffff',
    'accent_color' => '#f5a623',
    'brand_name' => 'Tatito Developers And Promoters',
    'brand_tagline' => "Empowering every citizen with transparent, accessible, and efficient real estate solutions through cutting-edge technology and a network of trusted real estate professionals.",
    'social_facebook' => '',
    'social_instagram' => '',
    'social_twitter' => '',
    'social_linkedin' => '',
    'copyright_text' => '© 2026 Tatito Developers And Promoters. All Rights Reserved.',
    'approved_by_badges' => 'RERA|CREDAI',
    'partnership_badges' => 'NAR India|Builders Association',
    'collaboration_badges' => 'Architects Forum|PropTech Alliance',
    'association_badges' => 'REALTORS®|FIABCI',
    'contact_email_1' => 'info.tatitodevelopersandpromoters@gmail.com',
    'contact_email_2' => 'support@tatitodevelopersandpromoters.com',
    'contact_phone' => '+91 98765 43210',
    'app_store_link' => '#',
    'google_play_link' => '#',
];

$result = $conn->query("SELECT * FROM footer_settings ORDER BY id DESC LIMIT 1");

if ($result && $result->num_rows > 0) {
    $footer = array_merge($footer, $result->fetch_assoc());
}

// Brand name can be blank in the DB row — fall back to a sane default instead of printing nothing.
$brandName = $footer['brand_name'] !== '' ? $footer['brand_name'] : 'Tatito Developers And Promoters';

// Parse badge strings into arrays
$approvedByBadges = array_filter(array_map('trim', explode('|', $footer['approved_by_badges'] ?? 'RERA|CREDAI')));
$partnershipBadges = array_filter(array_map('trim', explode('|', $footer['partnership_badges'] ?? 'NAR India|Builders Association')));
$collaborationBadges = array_filter(array_map('trim', explode('|', $footer['collaboration_badges'] ?? 'Architects Forum|PropTech Alliance')));
$associationBadges = array_filter(array_map('trim', explode('|', $footer['association_badges'] ?? 'REALTORS®|FIABCI')));

// Social links — matches footer_settings.social_* columns
$socialLinks = array_filter([
    'Facebook'  => $footer['social_facebook']  ?? '',
    'Instagram' => $footer['social_instagram'] ?? '',
    'Twitter'   => $footer['social_twitter']   ?? '',
    'LinkedIn'  => $footer['social_linkedin']  ?? '',
]);

// Inline SVG glyphs (currentColor) so each platform shows its real icon, not an initial
$socialIcons = [
    'Facebook'  => '<path d="M13.5 21v-7.5h2.5l.4-3H13.5V8.5c0-.87.24-1.46 1.49-1.46H16.5V4.35C16.24 4.31 15.35 4.24 14.31 4.24c-2.15 0-3.62 1.31-3.62 3.72V10.5H8.19v3h2.5V21h2.81z"/>',
    'Instagram' => '<path d="M12 8.4a3.6 3.6 0 1 0 0 7.2 3.6 3.6 0 0 0 0-7.2zm0 5.94a2.34 2.34 0 1 1 0-4.68 2.34 2.34 0 0 1 0 4.68zm4.59-6.09a.84.84 0 1 1-1.68 0 .84.84 0 0 1 1.68 0zM20 8.16c-.05-1.1-.3-2.07-1.1-2.87-.8-.8-1.77-1.05-2.87-1.1C14.9 4.14 9.1 4.14 8 4.19c-1.1.05-2.06.3-2.87 1.1-.8.8-1.05 1.77-1.1 2.87-.05 1.1-.05 6.9 0 8 .05 1.1.3 2.07 1.1 2.87.81.8 1.77 1.05 2.87 1.1 1.1.05 6.9.05 8 0 1.1-.05 2.07-.3 2.87-1.1.8-.8 1.05-1.77 1.1-2.87.05-1.1.05-6.9 0-8zM18.4 17.5a2.6 2.6 0 0 1-1.46 1.46c-1.01.4-3.4.31-4.94.31s-3.94.09-4.94-.31A2.6 2.6 0 0 1 5.6 17.5c-.4-1.01-.31-3.4-.31-4.94s-.09-3.94.31-4.94A2.6 2.6 0 0 1 7.06 6.16c1.01-.4 3.4-.31 4.94-.31s3.94-.09 4.94.31a2.6 2.6 0 0 1 1.46 1.46c.4 1.01.31 3.4.31 4.94s.09 3.94-.31 4.94z"/>',
    'Twitter'   => '<path d="M18.24 4H21l-6.06 6.93L22 20h-6.24l-4.89-6.39L5.24 20H2.47l6.5-7.43L2 4h6.39l4.42 5.84L18.24 4zm-1.09 14.24h1.53L7.9 5.68H6.26l10.89 12.56z"/>',
    'LinkedIn'  => '<path d="M6.94 8.5H4.06V20h2.88V8.5zM5.5 4a1.67 1.67 0 1 0 0 3.34A1.67 1.67 0 0 0 5.5 4zM20 20h-2.88v-5.84c0-1.39-.03-3.18-1.94-3.18-1.94 0-2.24 1.51-2.24 3.08V20H10.06V8.5h2.76v1.57h.04c.38-.73 1.33-1.5 2.74-1.5 2.93 0 3.47 1.93 3.47 4.43V20z"/>',
];

/* ----------------------------------------
   Footer Columns - dynamic from `footer_columns` / `footer_links`
   (currently: For Buyers, For Tenants, For Owners, Company)
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

/* ----------------------------------------
   Legal Links - dynamic from `footer_legal_links`
---------------------------------------- */

$footerLegalLinks = [];
$legalQuery = $conn->query("
SELECT *
FROM footer_legal_links
ORDER BY link_order ASC
");
if ($legalQuery) {
    while ($legal = $legalQuery->fetch_assoc()) {
        $footerLegalLinks[] = $legal;
    }
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
body.dark-mode .sy-footer h5,
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

body.dark-mode .sy-footer .bottom-bar .follow-label {
    color: var(--dm-text-secondary, #8b949e) !important;
}

body.dark-mode .sy-footer .bottom-bar .social-icons a {
    background: var(--dm-bg-tertiary, #1c2333) !important;
    border-color: var(--dm-border, #30363d) !important;
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
     ROW 1: Logo + Menu Columns (Dynamic from footer_columns table)
============================================================ -->

<div class="row g-4">

    <!-- ===== COLUMN 1: LOGO + BRAND ===== -->
    <div class="col-12 col-md-6 col-lg-4">
        <div class="footer-brand">
            <!-- Logo -->
            <div class="footer-logo mb-3">
                <img src="/front/assets/images/logo.jpeg"
                     alt="<?= htmlspecialchars($brandName) ?>"
                     style="height:50px; width:auto; filter:brightness(0) invert(1); transition: filter 0.3s ease;">
            </div>

            <!-- Brand Name -->
            <h3 style="color:<?= htmlspecialchars($footer['heading_color']) ?>; font-weight:700; font-size:1.5rem; margin-bottom:0.25rem; transition: color 0.3s ease;">
                <?= htmlspecialchars($brandName) ?>
            </h3>

            <!-- Tagline -->
            <p style="font-size:0.75rem; opacity:0.6; text-transform:uppercase; letter-spacing:1px; margin-bottom:1rem; transition: color 0.3s ease;">
                India's Premier Real Estate Platform
            </p>

            <!-- Description -->
            <p style="font-size:0.85rem; opacity:0.85; max-width:280px; line-height:1.6; transition: color 0.3s ease;">
                <?= htmlspecialchars($footer['brand_tagline']) ?>
            </p>

            <!-- Social Links - Dynamic from footer_settings -->
            <?php if (!empty($socialLinks)): ?>
            <div class="social-icons d-flex gap-2 mt-2">
                <?php foreach ($socialLinks as $label => $url): ?>
                <a href="<?= htmlspecialchars($url) ?>" title="<?= htmlspecialchars($label) ?>" aria-label="<?= htmlspecialchars($label) ?>"
                   style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:50%; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.08); color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; transition: background 0.2s, color 0.2s;"
                   onmouseover="this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'"
                   onmouseout="this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><?= $socialIcons[$label] ?? '' ?></svg>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ===== DYNAMIC COLUMNS from footer_columns / footer_links tables ===== -->
    <?php foreach ($footerColumns as $col): ?>
    <div class="col-6 col-md-3 col-lg-2">
        <h6 style="color:<?= htmlspecialchars($footer['heading_color']) ?>; font-weight:600; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:14px; transition: color 0.3s ease;">
            <?= htmlspecialchars($col['title']) ?>
        </h6>
        <?php foreach ($col['links'] as $link): ?>
        <a href="<?= htmlspecialchars($link['url']) ?>"
           style="display:block; margin-bottom:6px; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; font-size:0.82rem; opacity:0.7; transition:opacity 0.2s, color 0.2s;"
           onmouseover="this.style.opacity='1'; this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'"
           onmouseout="this.style.opacity='0.7'; this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">
            <?= htmlspecialchars($link['label']) ?>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>

</div>

<hr style="border-color:rgba(255,255,255,0.08); margin:30px 0; transition: border-color 0.3s ease;">

<!-- ============================================================
     APPROVED BY | PARTNERSHIP | COLLABORATION | ASSOCIATION
     - Dynamic from footer_settings badge columns
============================================================ -->

<div class="row g-4 mb-4">
    <div class="col-12">
        <div style="display:flex; flex-wrap:wrap; gap:20px 40px; justify-content:flex-start; align-items:center;">

            <!-- Approved By -->
            <?php if (!empty($approvedByBadges)): ?>
            <div>
                <span style="font-size:0.65rem; text-transform:uppercase; opacity:0.5; letter-spacing:0.5px; display:block; margin-bottom:6px; transition: color 0.3s ease;">APPROVED BY</span>
                <div style="display:flex; flex-wrap:wrap; gap:12px; align-items:center;">
                    <?php foreach ($approvedByBadges as $badge): ?>
                    <span style="background:rgba(255,255,255,0.06); padding:4px 14px; border-radius:15px; font-size:0.75rem; border:1px solid rgba(255,255,255,0.08); transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;">
                        <?= htmlspecialchars($badge) ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Partnership -->
            <?php if (!empty($partnershipBadges)): ?>
            <div>
                <span style="font-size:0.65rem; text-transform:uppercase; opacity:0.5; letter-spacing:0.5px; display:block; margin-bottom:6px; transition: color 0.3s ease;">PARTNERSHIP</span>
                <div style="display:flex; flex-wrap:wrap; gap:12px; align-items:center;">
                    <?php foreach ($partnershipBadges as $badge): ?>
                    <span style="background:rgba(255,255,255,0.06); padding:4px 14px; border-radius:15px; font-size:0.75rem; border:1px solid rgba(255,255,255,0.08); transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;">
                        <?= htmlspecialchars($badge) ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Collaboration -->
            <?php if (!empty($collaborationBadges)): ?>
            <div>
                <span style="font-size:0.65rem; text-transform:uppercase; opacity:0.5; letter-spacing:0.5px; display:block; margin-bottom:6px; transition: color 0.3s ease;">COLLABORATION</span>
                <div style="display:flex; flex-wrap:wrap; gap:12px; align-items:center;">
                    <?php foreach ($collaborationBadges as $badge): ?>
                    <span style="background:rgba(255,255,255,0.06); padding:4px 14px; border-radius:15px; font-size:0.75rem; border:1px solid rgba(255,255,255,0.08); transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;">
                        <?= htmlspecialchars($badge) ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Association -->
            <?php if (!empty($associationBadges)): ?>
            <div>
                <span style="font-size:0.65rem; text-transform:uppercase; opacity:0.5; letter-spacing:0.5px; display:block; margin-bottom:6px; transition: color 0.3s ease;">ASSOCIATION</span>
                <div style="display:flex; flex-wrap:wrap; gap:12px; align-items:center;">
                    <?php foreach ($associationBadges as $badge): ?>
                    <span style="background:rgba(255,255,255,0.06); padding:4px 14px; border-radius:15px; font-size:0.75rem; border:1px solid rgba(255,255,255,0.08); transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;">
                        <?= htmlspecialchars($badge) ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<hr style="border-color:rgba(255,255,255,0.08); margin:30px 0; transition: border-color 0.3s ease;">

<!-- ============================================================
     ROW 2: 3 Columns (Download Our App | Connect With Us | Platform Statistics)
     - Dynamic from footer_settings
============================================================ -->

<div class="row g-4">

    <!-- ===== COLUMN 1: Download Our App ===== -->
    <div class="col-md-4">
        <h6 style="color:<?= htmlspecialchars($footer['heading_color']) ?>; font-weight:600; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px; transition: color 0.3s ease;">
            DOWNLOAD OUR APP
        </h6>

        <div style="display:flex; flex-direction:column; gap:8px; margin-bottom:12px;">
            <a href="<?= htmlspecialchars($footer['app_store_link'] ?? '#') ?>"
               style="background:rgba(255,255,255,0.06); padding:8px 16px; border-radius:20px; font-size:0.8rem; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; border:1px solid rgba(255,255,255,0.08); text-align:center; width:fit-content; transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;"
               onmouseover="this.style.background='rgba(255,255,255,0.12)'"
               onmouseout="this.style.background='rgba(255,255,255,0.06)'">
                📱 Download on the App Store
            </a>
            <a href="<?= htmlspecialchars($footer['google_play_link'] ?? '#') ?>"
               style="background:rgba(255,255,255,0.06); padding:8px 16px; border-radius:20px; font-size:0.8rem; color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; border:1px solid rgba(255,255,255,0.08); text-align:center; width:fit-content; transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;"
               onmouseover="this.style.background='rgba(255,255,255,0.12)'"
               onmouseout="this.style.background='rgba(255,255,255,0.06)'">
                ▶️ Get it on Google Play
            </a>
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
            <div>📧 <?= htmlspecialchars($footer['contact_email_1'] ?? 'info.tatitodevelopersandpromoters@gmail.com') ?></div>
            <div>📧 <?= htmlspecialchars($footer['contact_email_2'] ?? 'support@tatitodevelopersandpromoters.com') ?></div>
            <div>📞 <?= htmlspecialchars($footer['contact_phone'] ?? '+91 98765 43210') ?></div>
        </div>

        <p style="font-size:0.85rem; opacity:0.85; line-height:1.6; transition: color 0.3s ease;">
            <?= htmlspecialchars($footer['brand_tagline']) ?>
        </p>

        <div style="font-size:0.8rem; opacity:0.8; display:flex; flex-direction:column; gap:4px; transition: color 0.3s ease;">
            <span>🔒 256-bit SSL Secure Platform</span>
            <span>🕒 24/7 Priority Support for Members</span>
        </div>

        <!-- Brand Name -->
        <div style="margin-top:12px;">
            <span style="display:inline-block;width:8px;height:8px;background:<?= htmlspecialchars($footer['accent_color']) ?>;border-radius:50%;margin-right:8px; transition: background 0.3s ease;"></span>
            <span style="color:<?= htmlspecialchars($footer['heading_color']) ?>; font-weight:700; font-size:1.1rem; transition: color 0.3s ease;">
                <?= htmlspecialchars($brandName) ?>
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
     BOTTOM BAR - Copyright (left) + Follow Us on: + Social Icons (right)
============================================================ -->

<div class="bottom-bar mt-4 pt-3" style="border-top:1px solid rgba(255,255,255,0.06); transition: border-color 0.3s ease;">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">

        <div class="copyright-text" style="font-size:0.75rem; opacity:0.6; transition: color 0.3s ease;">
            <?= htmlspecialchars($footer['copyright_text']) ?>
        </div>

        <?php if (!empty($socialLinks)): ?>
        <div class="d-flex flex-column align-items-center align-items-md-end gap-2">
            <span class="follow-label" style="font-size:0.7rem; text-transform:uppercase; letter-spacing:0.5px; opacity:0.6; transition: color 0.3s ease;">Follow Us on:</span>
            <div class="social-icons d-flex gap-2">
                <?php foreach ($socialLinks as $label => $url): ?>
                <a href="<?= htmlspecialchars($url) ?>" title="<?= htmlspecialchars($label) ?>" aria-label="<?= htmlspecialchars($label) ?>"
                   style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:50%; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.08); color:<?= htmlspecialchars($footer['text_color']) ?>; text-decoration:none; transition: background 0.2s, color 0.2s;"
                   onmouseover="this.style.color='<?= htmlspecialchars($footer['accent_color']) ?>'"
                   onmouseout="this.style.color='<?= htmlspecialchars($footer['text_color']) ?>'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><?= $socialIcons[$label] ?? '' ?></svg>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

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