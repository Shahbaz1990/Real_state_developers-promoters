<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require_once 'config.php';

if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit;
}

$msg = $_SESSION['flash_msg'] ?? "";
unset($_SESSION['flash_msg']);

/*
|--------------------------------------------------------------------------
| Default Footer Settings (Real EstateHub Style)
|--------------------------------------------------------------------------
*/

$settings = [
    'id'=>null,

    'bg_color'=>'#0f1f38',
    'text_color'=>'#c9d3e0',
    'heading_color'=>'#ffffff',
    'accent_color'=>'#f5a623',

    'brand_name'=>'Real EstateHub',
    'brand_tagline'=>"Empowering every citizen with transparent, accessible, and efficient real estate solutions through cutting-edge technology and a network of trusted real estate professionals.",

    'social_facebook'=>'#',
    'social_instagram'=>'#',
    'social_twitter'=>'#',
    'social_linkedin'=>'#',

    'copyright_text'=>'© 2025 Real EstateHub. All Rights Reserved.',
];

/*
|--------------------------------------------------------------------------
| Fetch Existing Settings
|--------------------------------------------------------------------------
*/

$result = $conn->query("SELECT * FROM footer_settings ORDER BY id DESC LIMIT 1");

if($result && $result->num_rows){
    $settings = array_merge($settings, $result->fetch_assoc());
}

/*
|--------------------------------------------------------------------------
| Save Footer Settings
|--------------------------------------------------------------------------
*/

if(isset($_POST['save'])){

    $bg_color=$_POST['bg_color'];
    $text_color=$_POST['text_color'];
    $heading_color=$_POST['heading_color'];
    $accent_color=$_POST['accent_color'];

    $brand_name=$_POST['brand_name'];
    $brand_tagline=$_POST['brand_tagline'];

    $social_facebook=$_POST['social_facebook'];
    $social_instagram=$_POST['social_instagram'];
    $social_twitter=$_POST['social_twitter'];
    $social_linkedin=$_POST['social_linkedin'];

    $copyright_text=$_POST['copyright_text'];
    
    /*==========================
      INSERT FOOTER COLUMN
    ==========================*/

    if(!empty($_POST['column_title'])){

        $stmtColumn=$conn->prepare("
            INSERT INTO footer_columns
            (title,column_order,status)
            VALUES(?,?,?)
        ");

        $stmtColumn->bind_param(
            "sis",
            $_POST['column_title'],
            $_POST['column_order'],
            $_POST['column_status']
        );

        $stmtColumn->execute();
        $stmtColumn->close();
    }
    
    /*==========================
      INSERT FOOTER LINK
    ==========================*/

    if(!empty($_POST['link_label'])){

        $stmtLink=$conn->prepare("
            INSERT INTO footer_links
            (column_id,label,url,link_order,status)
            VALUES(?,?,?,?,?)
        ");

        $stmtLink->bind_param(
            "issis",
            $_POST['column_id'],
            $_POST['link_label'],
            $_POST['link_url'],
            $_POST['link_order'],
            $_POST['link_status']
        );

        $stmtLink->execute();
        $stmtLink->close();
    }

    if($settings['id']==null){

        $stmt=$conn->prepare("INSERT INTO footer_settings
        (
        bg_color,
        text_color,
        heading_color,
        accent_color,

        brand_name,
        brand_tagline,

        social_facebook,
        social_instagram,
        social_twitter,
        social_linkedin,

        copyright_text

        )

        VALUES

        (?,?,?,?,?,?,?,?,?,?,?)");

        $stmt->bind_param(

        "sssssssssss",

        $bg_color,
        $text_color,
        $heading_color,
        $accent_color,

        $brand_name,
        $brand_tagline,

        $social_facebook,
        $social_instagram,
        $social_twitter,
        $social_linkedin,

        $copyright_text

        );

    }else{

        $stmt=$conn->prepare("UPDATE footer_settings SET

        bg_color=?,
        text_color=?,
        heading_color=?,
        accent_color=?,

        brand_name=?,
        brand_tagline=?,

        social_facebook=?,
        social_instagram=?,
        social_twitter=?,
        social_linkedin=?,

        copyright_text=?

        WHERE id=?");

        $stmt->bind_param(

        "sssssssssssi",

        $bg_color,
        $text_color,
        $heading_color,
        $accent_color,

        $brand_name,
        $brand_tagline,

        $social_facebook,
        $social_instagram,
        $social_twitter,
        $social_linkedin,

        $copyright_text,

        $settings['id']

        );

    }

    if($stmt->execute()){

        $_SESSION['flash_msg']="Footer Settings Updated Successfully.";

    }else{

        $_SESSION['flash_msg']="Something Went Wrong.";

    }

    header("Location: footer-settings.php");
    exit;

}

// Fetch columns for display
$columns = $conn->query("SELECT * FROM footer_columns WHERE status='Active' ORDER BY column_order ASC");
$allColumns = [];
while($col = $columns->fetch_assoc()){
    $col['links'] = [];
    $links = $conn->query("SELECT * FROM footer_links WHERE column_id=".$col['id']." AND status='Active' ORDER BY link_order ASC");
    while($link = $links->fetch_assoc()){
        $col['links'][] = $link;
    }
    $allColumns[] = $col;
}

// Fetch legal links
$legalLinks = $conn->query("SELECT * FROM footer_legal_links ORDER BY link_order ASC");

?>

<!DOCTYPE html>
<html>
<head>
<title>Footer Settings - Real EstateHub</title>
<link rel="stylesheet" href="css/adminlte.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
<style>
.color-box{
    display:flex;
    align-items:center;
    gap:10px;
}

.color-box input{
    width:55px;
    height:40px;
    cursor:pointer;
}

/* ============================================
   LIVE PREVIEW - Real EstateHub Style
   ============================================ */

.footer-preview{
    background:<?= htmlspecialchars($settings['bg_color']) ?>;
    padding:30px 25px;
    border-radius:12px;
    border-left:4px solid <?= htmlspecialchars($settings['accent_color']) ?>;
    transition: background 0.3s ease, border-color 0.3s ease;
}

.footer-preview .menu-heading{
    color:<?= htmlspecialchars($settings['heading_color']) ?>;
    font-weight:600;
    font-size:0.8rem;
    text-transform:uppercase;
    letter-spacing:0.5px;
    margin-bottom:10px;
    transition: color 0.3s ease;
}

.footer-preview .menu-link{
    color:<?= htmlspecialchars($settings['text_color']) ?>;
    font-size:0.8rem;
    opacity:0.8;
    text-decoration:none;
    display:block;
    margin-bottom:4px;
    transition: color 0.3s ease;
}

.footer-preview .section-title{
    color:<?= htmlspecialchars($settings['heading_color']) ?>;
    font-weight:600;
    font-size:0.85rem;
    text-transform:uppercase;
    letter-spacing:0.5px;
    margin-bottom:10px;
    transition: color 0.3s ease;
}

.footer-preview .stat-number{
    color:<?= htmlspecialchars($settings['heading_color']) ?>;
    font-weight:700;
    font-size:1.1rem;
    transition: color 0.3s ease;
}

.footer-preview .stat-label{
    color:<?= htmlspecialchars($settings['text_color']) ?>;
    font-size:0.75rem;
    opacity:0.6;
    display:block;
    transition: color 0.3s ease;
}

.footer-preview .brand-dot{
    display:inline-block;
    width:8px;
    height:8px;
    background:<?= htmlspecialchars($settings['accent_color']) ?>;
    border-radius:50%;
    margin-right:8px;
    transition: background 0.3s ease;
}

.footer-preview .brand-name{
    color:<?= htmlspecialchars($settings['heading_color']) ?>;
    font-weight:700;
    font-size:1.3rem;
    transition: color 0.3s ease;
}

.footer-preview .brand-sub{
    font-size:0.65rem;
    opacity:0.5;
    text-transform:uppercase;
    letter-spacing:0.5px;
    display:block;
    margin-top:2px;
    color:<?= htmlspecialchars($settings['text_color']) ?>;
    transition: color 0.3s ease;
}

.footer-preview .divider{
    border-color:rgba(255,255,255,0.08);
    margin:20px 0;
}

.footer-preview .badge-app{
    background:rgba(255,255,255,0.06);
    padding:4px 14px;
    border-radius:20px;
    font-size:0.75rem;
    color:<?= htmlspecialchars($settings['text_color']) ?>;
    display:inline-block;
    margin-right:6px;
    border:1px solid rgba(255,255,255,0.08);
    transition: color 0.3s ease, background 0.3s ease;
}

.footer-preview .feature-item{
    color:<?= htmlspecialchars($settings['text_color']) ?>;
    font-size:0.75rem;
    opacity:0.8;
    transition: color 0.3s ease;
}

.footer-preview .preview-text{
    color:<?= htmlspecialchars($settings['text_color']) ?>;
    transition: color 0.3s ease;
}

.footer-preview .preview-copyright{
    color:<?= htmlspecialchars($settings['text_color']) ?>;
    font-size:0.75rem;
    opacity:0.6;
    text-align:center;
    transition: color 0.3s ease;
}

/* Admin Card Styles */
.card{
    border-radius:12px;
    border:1px solid rgba(0,0,0,0.06);
    box-shadow:0 2px 8px rgba(0,0,0,0.04);
}

.card-header{
    background:#f8f9fc;
    font-weight:600;
    border-bottom:1px solid rgba(0,0,0,0.05);
    padding:14px 22px;
}

.card-body{
    padding:22px;
}

.form-label{
    margin-bottom:6px;
    font-weight:600;
    font-size:0.9rem;
}

.form-control,
.form-select{
    height:44px;
    border-radius:8px;
    border:1px solid #e2e8f0;
}

textarea.form-control{
    height:auto;
    min-height:100px;
}

.btn-primary{
    background:#1e3a6f;
    border:none;
    padding:12px 36px;
    font-weight:600;
    border-radius:8px;
}

.btn-primary:hover{
    background:#0f2a52;
}

.section-divider{
    border-top:2px dashed #e2e8f0;
    margin:30px 0;
}
</style>
</head>

<body>

    <div class="app-wrapper">
        <!-- Navbar -->
        <?php include 'nav.php'; ?>
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

<main class="app-main">
    <div class="container mt-4">

        <div class="row justify-content-center">

            <div class="col-lg-12">

                <div class="d-flex align-items-center mb-4">
                    <i class="bi bi-gear-fill text-primary me-2" style="font-size:1.8rem;"></i>
                    <h3 class="mb-0 fw-bold">Footer Settings</h3>
                    <span class="ms-3 badge bg-secondary" style="font-weight:400;">Real EstateHub</span>
                </div>

                <div class="card">

                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-palette me-2"></i>Customize Footer</span>
                        <span class="badge bg-success rounded-pill">Live Preview</span>
                    </div>

                    <div class="card-body">

                        <?php if($msg!=""){ ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="bi bi-check-circle-fill me-2"></i><?= $msg ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php } ?>

                        <!-- ============================================================
                             LIVE PREVIEW - Real EstateHub Style
                        ============================================================ -->

                        <label class="fw-bold mb-2 d-flex align-items-center">
                            <i class="bi bi-eye me-2"></i>Live Preview
                        </label>

                        <div class="footer-preview mb-4" id="footerPreview">

                            <!-- ROW 1: Logo + 3 Menu Columns -->
                            <div class="row g-3" id="previewMenuColumns">
                                <!-- Logo Column -->
                                <div class="col-12 col-md-6 col-lg-3">
                                    <div style="margin-bottom:12px;">
                                        <div style="background:rgba(255,255,255,0.06); width:45px; height:45px; border-radius:8px; display:flex; align-items:center; justify-content:center; margin-bottom:8px;">
                                            <i class="bi bi-building" style="color:#fff; font-size:1.5rem;"></i>
                                        </div>
                                        <div class="brand-name" id="previewBrandName"><?= htmlspecialchars($settings['brand_name']) ?></div>
                                        <div class="brand-sub">India's Premier Real Estate Platform</div>
                                        <p class="preview-text" style="font-size:0.75rem; opacity:0.85; max-width:280px; line-height:1.6; margin-top:10px;" id="previewTagline">
                                            <?= htmlspecialchars($settings['brand_tagline']) ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <?php 
                                $colCount = 0;
                                $maxCols = 3;
                                foreach($allColumns as $col):
                                    if($colCount >= $maxCols) break;
                                    $colCount++;
                                ?>
                                <div class="col-6 col-md-3 col-lg-3">
                                    <div class="menu-heading"><?= htmlspecialchars($col['title']) ?></div>
                                    <?php foreach($col['links'] as $link): ?>
                                    <a href="#" class="menu-link"><?= htmlspecialchars($link['label']) ?></a>
                                    <?php endforeach; ?>
                                </div>
                                <?php endforeach; ?>
                                
                                <?php if($colCount < 3): ?>
                                    <?php if($colCount < 1): ?>
                                    <div class="col-6 col-md-3 col-lg-3">
                                        <div class="menu-heading">Explore</div>
                                        <a href="#" class="menu-link">Home Page</a>
                                        <a href="#" class="menu-link">Premium Services</a>
                                        <a href="#" class="menu-link">Find Properties</a>
                                        <a href="#" class="menu-link">Browse Profiles</a>
                                        <a href="#" class="menu-link">Careers</a>
                                        <a href="#" class="menu-link">How It Works</a>
                                    </div>
                                    <?php endif; ?>
                                    <?php if($colCount < 2): ?>
                                    <div class="col-6 col-md-3 col-lg-3">
                                        <div class="menu-heading">More</div>
                                        <a href="#" class="menu-link">List a Property</a>
                                        <a href="#" class="menu-link">Property Status</a>
                                        <a href="#" class="menu-link">Documentation</a>
                                        <a href="#" class="menu-link">Create Blog</a>
                                        <a href="#" class="menu-link">Credits</a>
                                        <a href="#" class="menu-link">Site Map</a>
                                    </div>
                                    <?php endif; ?>
                                    <?php if($colCount < 3): ?>
                                    <div class="col-6 col-md-3 col-lg-3">
                                        <div class="menu-heading">For Buyers</div>
                                        <a href="#" class="menu-link">Search Properties</a>
                                        <a href="#" class="menu-link">Buyer How It Works</a>
                                        <a href="#" class="menu-link">Mortgage Calculator</a>
                                        <a href="#" class="menu-link">Buyer Guides</a>
                                        <a href="#" class="menu-link">New Launches</a>
                                        <a href="#" class="menu-link">Book a Site Visit</a>
                                    </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <hr class="divider">

                            <!-- ROW 2: 4 Columns (For Sellers, For Agents, Help, Legal) -->
                            <div class="row g-3">
                                <?php 
                                $colCount2 = 0;
                                $maxCols2 = 4;
                                $skipped = 0;
                                foreach($allColumns as $col):
                                    if($skipped < 3) {
                                        $skipped++;
                                        continue;
                                    }
                                    if($colCount2 >= $maxCols2) break;
                                    $colCount2++;
                                ?>
                                <div class="col-6 col-md-3">
                                    <div class="menu-heading"><?= htmlspecialchars($col['title']) ?></div>
                                    <?php foreach($col['links'] as $link): ?>
                                    <a href="#" class="menu-link"><?= htmlspecialchars($link['label']) ?></a>
                                    <?php endforeach; ?>
                                </div>
                                <?php endforeach; ?>
                                
                                <?php if($colCount2 < 4): ?>
                                    <?php if($colCount2 < 1): ?>
                                    <div class="col-6 col-md-3">
                                        <div class="menu-heading">For Sellers</div>
                                        <a href="#" class="menu-link">List Your Property</a>
                                        <a href="#" class="menu-link">Seller How It Works</a>
                                        <a href="#" class="menu-link">Property Valuation</a>
                                        <a href="#" class="menu-link">Seller Guides</a>
                                        <a href="#" class="menu-link">Selling Tips</a>
                                        <a href="#" class="menu-link">Compare Offers</a>
                                    </div>
                                    <?php endif; ?>
                                    <?php if($colCount2 < 2): ?>
                                    <div class="col-6 col-md-3">
                                        <div class="menu-heading">For Agents</div>
                                        <a href="#" class="menu-link">Agent Registration</a>
                                        <a href="#" class="menu-link">Agent Dashboard</a>
                                        <a href="#" class="menu-link">Lead Management</a>
                                        <a href="#" class="menu-link">Marketing Tools</a>
                                        <a href="#" class="menu-link">Agent Resources</a>
                                        <a href="#" class="menu-link">Commission Calculator</a>
                                    </div>
                                    <?php endif; ?>
                                    <?php if($colCount2 < 3): ?>
                                    <div class="col-6 col-md-3">
                                        <div class="menu-heading">Help</div>
                                        <a href="#" class="menu-link">Help Center</a>
                                        <a href="#" class="menu-link">FAQ</a>
                                        <a href="#" class="menu-link">Terms of Use</a>
                                        <a href="#" class="menu-link">Refund Policy</a>
                                        <a href="#" class="menu-link">Grievances</a>
                                        <a href="#" class="menu-link">Report an Issue</a>
                                    </div>
                                    <?php endif; ?>
                                    <?php if($colCount2 < 4): ?>
                                    <div class="col-6 col-md-3">
                                        <div class="menu-heading">Legal</div>
                                        <a href="#" class="menu-link">Privacy Policy</a>
                                        <a href="#" class="menu-link">Cookie Policy</a>
                                        <a href="#" class="menu-link">Fraud Alert</a>
                                        <a href="#" class="menu-link">RERA Compliance</a>
                                        <a href="#" class="menu-link">Disclaimer</a>
                                        <a href="#" class="menu-link">Copyright Policy</a>
                                    </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <hr class="divider">

                            <!-- APPROVED BY | PARTNERSHIP | COLLABORATION | ASSOCIATION -->
                            <div style="display:flex; flex-wrap:wrap; gap:20px 40px; margin-bottom:20px;">
                                <div>
                                    <span style="font-size:0.6rem; text-transform:uppercase; opacity:0.5; letter-spacing:0.5px; display:block; margin-bottom:4px; color:<?= htmlspecialchars($settings['text_color']) ?>;">APPROVED BY</span>
                                    <span class="badge-app" style="display:inline-block; margin-right:6px;">🏢 RERA</span>
                                    <span class="badge-app" style="display:inline-block; margin-right:6px;">📋 CREDAI</span>
                                </div>
                                <div>
                                    <span style="font-size:0.6rem; text-transform:uppercase; opacity:0.5; letter-spacing:0.5px; display:block; margin-bottom:4px; color:<?= htmlspecialchars($settings['text_color']) ?>;">PARTNERSHIP</span>
                                    <span class="badge-app" style="display:inline-block; margin-right:6px;">🤝 NAR India</span>
                                    <span class="badge-app" style="display:inline-block; margin-right:6px;">🏗️ Builders Association</span>
                                </div>
                                <div>
                                    <span style="font-size:0.6rem; text-transform:uppercase; opacity:0.5; letter-spacing:0.5px; display:block; margin-bottom:4px; color:<?= htmlspecialchars($settings['text_color']) ?>;">COLLABORATION</span>
                                    <span class="badge-app" style="display:inline-block; margin-right:6px;">📐 Architects Forum</span>
                                    <span class="badge-app" style="display:inline-block; margin-right:6px;">📊 PropTech Alliance</span>
                                </div>
                                <div>
                                    <span style="font-size:0.6rem; text-transform:uppercase; opacity:0.5; letter-spacing:0.5px; display:block; margin-bottom:4px; color:<?= htmlspecialchars($settings['text_color']) ?>;">ASSOCIATION</span>
                                    <span class="badge-app" style="display:inline-block; margin-right:6px;">⭐ REALTORS®</span>
                                    <span class="badge-app" style="display:inline-block; margin-right:6px;">📈 FIABCI</span>
                                </div>
                            </div>

                            <hr class="divider">

                            <!-- ROW 3: 3 Columns (Download Our App | Connect With Us | Platform Statistics) -->
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="section-title">DOWNLOAD OUR APP</div>
                                    <div class="mb-2">
                                        <span class="badge-app">📱 Download on the App Store</span>
                                        <span class="badge-app">▶️ Get it on Google Play</span>
                                    </div>
                                    <p class="preview-text" style="font-size:0.8rem; opacity:0.85;">
                                        Experience real estate convenience at your fingertips. Our mobile app is designed to provide you with seamless access to property experts, real-time listings, and secure document management anytime, anywhere.
                                    </p>
                                    <div>
                                        <div class="feature-item">✅ Real-time Property Listings</div>
                                        <div class="feature-item">✅ Secure Document Vault</div>
                                        <div class="feature-item">✅ Instant Agent Chat</div>
                                        <div class="feature-item">✅ 24/7 Property Assistance</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="section-title">CONNECT WITH US</div>
                                    <div class="preview-text" style="font-size:0.8rem; opacity:0.85; margin-bottom:10px;">
                                        <div>📧 info.realestatehub@gmail.com</div>
                                        <div>📧 support@realestatehub.com</div>
                                        <div>📞 +91 98765 43210</div>
                                    </div>
                                    <p class="preview-text" style="font-size:0.8rem; opacity:0.85;" id="previewTagline2">
                                        <?= htmlspecialchars($settings['brand_tagline']) ?>
                                    </p>
                                    <div class="preview-text" style="font-size:0.75rem; opacity:0.8; display:flex; flex-direction:column; gap:4px;">
                                        <span>🔒 256-bit SSL Secure Platform</span>
                                        <span>🕒 24/7 Priority Support for Members</span>
                                    </div>
                                    <div style="margin-top:12px;">
                                        <span class="brand-dot"></span>
                                        <span class="brand-name" id="previewBrandName2"><?= htmlspecialchars($settings['brand_name']) ?></span>
                                        <span class="brand-sub" style="display:block; margin-left:0; margin-top:2px;">India's Premier Real Estate Platform</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="section-title">PLATFORM STATISTICS</div>
                                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px 20px;">
                                        <div><span class="stat-number">5,420+</span><span class="stat-label">Total Properties</span></div>
                                        <div><span class="stat-number">3,280+</span><span class="stat-label">Total Clients</span></div>
                                        <div><span class="stat-number">1,850+</span><span class="stat-label">Total Agents</span></div>
                                        <div><span class="stat-number">12,940+</span><span class="stat-label">Property Views</span></div>
                                        <div><span class="stat-number">2,670+</span><span class="stat-label">Shortlisted</span></div>
                                        <div><span class="stat-number">4,520+</span><span class="stat-label">Enquiries</span></div>
                                    </div>
                                </div>
                            </div>

                            <hr class="divider">

                            <!-- Copyright -->
                            <div class="preview-copyright" id="previewCopyright">
                                <?= htmlspecialchars($settings['copyright_text']) ?>
                            </div>

                            <!-- Legal Links Preview -->
                            <div style="display:flex; flex-wrap:wrap; gap:10px 20px; justify-content:center; margin-top:10px;" id="previewLegalLinks">
                                <?php 
                                if($legalLinks && $legalLinks->num_rows){
                                    while($legal=$legalLinks->fetch_assoc()){
                                ?>
                                <span class="preview-text" style="font-size:0.7rem; opacity:0.6;"><?= htmlspecialchars($legal['label']) ?></span>
                                <?php
                                    }
                                } else {
                                ?>
                                <span class="preview-text" style="font-size:0.7rem; opacity:0.6;">Privacy Policy</span>
                                <span class="preview-text" style="font-size:0.7rem; opacity:0.6;">Terms of Use</span>
                                <span class="preview-text" style="font-size:0.7rem; opacity:0.6;">Sitemap</span>
                                <?php } ?>
                            </div>

                        </div>

                        <!-- ============================================================
                             FORM
                        ============================================================ -->

                        <form method="POST">

                            <!-- =========================
                             Add Footer Column & Link
                            ========================= -->

                            <div class="card mb-4 border-0 shadow-sm">

                                <div class="card-header bg-light">
                                    <i class="bi bi-layout-three-columns me-2"></i>Add Footer Column & Link
                                </div>

                                <div class="card-body">

                                    <div class="row g-3 mb-4">

                                        <div class="col-md-12">
                                            <h6 class="fw-bold text-muted"><i class="bi bi-plus-circle me-1"></i> New Column</h6>
                                        </div>

                                        <div class="col-md-5">
                                            <label class="form-label">Column Title</label>
                                            <input type="text" class="form-control" name="column_title" placeholder="e.g. Explore">
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">Order</label>
                                            <input type="number" class="form-control" name="column_order" value="1">
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" name="column_status">
                                                <option>Active</option>
                                                <option>Inactive</option>
                                            </select>
                                        </div>

                                    </div>

                                    <hr class="section-divider">

                                    <div class="row g-3">

                                        <div class="col-md-12">
                                            <h6 class="fw-bold text-muted"><i class="bi bi-link-45deg me-1"></i> New Link</h6>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">Column</label>
                                            <select class="form-select" name="column_id">
                                                <?php
                                                $cols=$conn->query("SELECT * FROM footer_columns ORDER BY column_order ASC");
                                                while($c=$cols->fetch_assoc()){
                                                ?>
                                                <option value="<?= $c['id']; ?>">
                                                    <?= htmlspecialchars($c['title']); ?>
                                                </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">Link Label</label>
                                            <input type="text" class="form-control" name="link_label" placeholder="e.g. Home Page">
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label">URL</label>
                                            <input type="text" class="form-control" name="link_url" value="#">
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label">Order</label>
                                            <input type="number" class="form-control" name="link_order" value="1">
                                        </div>

                                        <div class="col-md-1">
                                            <label class="form-label">Status</label>
                                            <select class="form-select" name="link_status">
                                                <option>Active</option>
                                                <option>Inactive</option>
                                            </select>
                                        </div>

                                    </div>

                                </div>

                            </div>

                            <!-- =========================
                             Current Footer Columns Preview
                            ========================= -->

                            <div class="card mb-4 border-0 shadow-sm">

                                <div class="card-header bg-light">
                                    <i class="bi bi-list-ul me-2"></i>Current Footer Columns
                                </div>

                                <div class="card-body">

                                    <div class="row">

                                        <?php
                                        $cols = $conn->query("
                                            SELECT *
                                            FROM footer_columns
                                            WHERE status='Active'
                                            ORDER BY column_order ASC
                                        ");

                                        while($col = $cols->fetch_assoc()){
                                        ?>

                                        <div class="col-lg-3 col-md-6 mb-3">

                                            <h6 class="fw-bold mb-2" style="color:#1e3a6f; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.3px;">
                                                <?= htmlspecialchars($col['title']) ?>
                                            </h6>

                                            <?php
                                            $links = $conn->query("
                                                SELECT *
                                                FROM footer_links
                                                WHERE column_id=".$col['id']."
                                                AND status='Active'
                                                ORDER BY link_order ASC
                                            ");

                                            while($link = $links->fetch_assoc()){
                                            ?>

                                            <div class="mb-1 text-muted small">
                                                <i class="bi bi-chevron-right me-1" style="font-size:0.5rem;"></i>
                                                <?= htmlspecialchars($link['label']) ?>
                                            </div>

                                            <?php } ?>

                                        </div>

                                        <?php } ?>

                                    </div>

                                </div>

                            </div>

                            <!-- =========================
                             Brand Settings
                            ========================= -->

                            <div class="card mb-4 border-0 shadow-sm">

                                <div class="card-header bg-light">
                                    <i class="bi bi-building me-2"></i>Brand Settings
                                </div>

                                <div class="card-body">

                                    <div class="mb-3">
                                        <label class="form-label">Brand Name</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="brand_name"
                                            id="brand_name"
                                            value="<?= htmlspecialchars($settings['brand_name']) ?>">
                                    </div>

                                    <div class="mb-0">
                                        <label class="form-label">Brand Tagline</label>
                                        <textarea
                                            class="form-control"
                                            rows="3"
                                            name="brand_tagline"
                                            id="brand_tagline"><?= htmlspecialchars($settings['brand_tagline']) ?></textarea>
                                    </div>

                                </div>

                            </div>

                            <!-- =========================
                             Color Settings
                            ========================= -->

                            <div class="card mb-4 border-0 shadow-sm">

                                <div class="card-header bg-light">
                                    <i class="bi bi-palette me-2"></i>Footer Colors
                                </div>

                                <div class="card-body">

                                    <div class="row g-3">

                                        <div class="col-md-3">

                                            <label class="form-label">Background</label>

                                            <div class="color-box">

                                                <input
                                                type="color"
                                                name="bg_color"
                                                id="bg_color"
                                                value="<?= htmlspecialchars($settings['bg_color']) ?>">

                                                <span id="bg_color_hex" style="font-size:0.8rem;">
                                                    <?= htmlspecialchars($settings['bg_color']) ?>
                                                </span>

                                            </div>

                                        </div>

                                        <div class="col-md-3">

                                            <label class="form-label">Heading</label>

                                            <div class="color-box">

                                                <input
                                                type="color"
                                                name="heading_color"
                                                id="heading_color"
                                                value="<?= htmlspecialchars($settings['heading_color']) ?>">

                                                <span id="heading_color_hex" style="font-size:0.8rem;">
                                                    <?= htmlspecialchars($settings['heading_color']) ?>
                                                </span>

                                            </div>

                                        </div>

                                        <div class="col-md-3">

                                            <label class="form-label">Text</label>

                                            <div class="color-box">

                                                <input
                                                type="color"
                                                name="text_color"
                                                id="text_color"
                                                value="<?= htmlspecialchars($settings['text_color']) ?>">

                                                <span id="text_color_hex" style="font-size:0.8rem;">
                                                    <?= htmlspecialchars($settings['text_color']) ?>
                                                </span>

                                            </div>

                                        </div>

                                        <div class="col-md-3">

                                            <label class="form-label">Accent</label>

                                            <div class="color-box">

                                                <input
                                                type="color"
                                                name="accent_color"
                                                id="accent_color"
                                                value="<?= htmlspecialchars($settings['accent_color']) ?>">

                                                <span id="accent_color_hex" style="font-size:0.8rem;">
                                                    <?= htmlspecialchars($settings['accent_color']) ?>
                                                </span>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <!-- =========================
                             Copyright
                            ========================= -->

                            <div class="card mb-4 border-0 shadow-sm">

                                <div class="card-header bg-light">
                                    <i class="bi bi-c-circle me-2"></i>Copyright
                                </div>

                                <div class="card-body">

                                    <div class="mb-3">
                                        <label class="form-label">Copyright Text</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            name="copyright_text"
                                            id="copyright_text"
                                            value="<?= htmlspecialchars($settings['copyright_text']) ?>">
                                    </div>

                                    <div class="mb-0">
                                        <label class="form-label">Legal Links</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php 
                                            $legalLinks2 = $conn->query("SELECT * FROM footer_legal_links ORDER BY link_order ASC");
                                            if($legalLinks2 && $legalLinks2->num_rows){
                                                while($legal=$legalLinks2->fetch_assoc()){
                                            ?>
                                            <span class="badge bg-light text-dark border"><?= htmlspecialchars($legal['label']) ?></span>
                                            <?php
                                                }
                                            } else {
                                            ?>
                                            <span class="badge bg-light text-dark border">Privacy Policy</span>
                                            <span class="badge bg-light text-dark border">Terms of Use</span>
                                            <span class="badge bg-light text-dark border">Sitemap</span>
                                            <?php } ?>
                                        </div>
                                        <small class="text-muted d-block mt-2">To add/edit legal links, modify the <code>footer_legal_links</code> table in your database.</small>
                                    </div>

                                </div>

                            </div>

                            <!-- =========================
                             Social Media
                            ========================= -->

                            <div class="card mb-4 border-0 shadow-sm">

                                <div class="card-header bg-light">
                                    <i class="bi bi-share me-2"></i>Social Media
                                </div>

                                <div class="card-body">

                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label">Facebook</label>
                                            <input type="text" class="form-control" name="social_facebook" value="<?= htmlspecialchars($settings['social_facebook'] ?? '#') ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Instagram</label>
                                            <input type="text" class="form-control" name="social_instagram" value="<?= htmlspecialchars($settings['social_instagram'] ?? '#') ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Twitter</label>
                                            <input type="text" class="form-control" name="social_twitter" value="<?= htmlspecialchars($settings['social_twitter'] ?? '#') ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">LinkedIn</label>
                                            <input type="text" class="form-control" name="social_linkedin" value="<?= htmlspecialchars($settings['social_linkedin'] ?? '#') ?>">
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <!-- =========================
                             Submit
                            ========================= -->

                            <div class="d-flex gap-3">
                                <button type="submit" name="save" class="btn btn-primary btn-lg px-5">
                                    <i class="bi bi-save me-2"></i>Save Footer Settings
                                </button>
                                <button type="reset" class="btn btn-outline-secondary btn-lg px-4">
                                    <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                                </button>
                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>
</main>

</div>

<script src="js/adminlte.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

function bindColor(inputId, hexId, callback) {
    const input = document.getElementById(inputId);
    const hex = document.getElementById(hexId);
    input.addEventListener("input", function() {
        hex.innerHTML = this.value;
        callback(this.value);
    });
}

/* Preview Elements */
const footer = document.getElementById("footerPreview");

/* Color Pickers */
bindColor("bg_color", "bg_color_hex", function(v) {
    footer.style.background = v;
});

bindColor("heading_color", "heading_color_hex", function(v) {
    document.querySelectorAll('.menu-heading').forEach(el => el.style.color = v);
    document.querySelectorAll('.section-title').forEach(el => el.style.color = v);
    document.querySelectorAll('.stat-number').forEach(el => el.style.color = v);
    document.querySelectorAll('.brand-name').forEach(el => el.style.color = v);
});

bindColor("text_color", "text_color_hex", function(v) {
    document.querySelectorAll('.menu-link').forEach(el => el.style.color = v);
    document.querySelectorAll('.stat-label').forEach(el => el.style.color = v);
    document.querySelectorAll('.badge-app').forEach(el => el.style.color = v);
    document.querySelectorAll('.feature-item').forEach(el => el.style.color = v);
    document.querySelectorAll('.preview-text').forEach(el => el.style.color = v);
    document.querySelectorAll('.preview-copyright').forEach(el => el.style.color = v);
    document.querySelectorAll('.brand-sub').forEach(el => el.style.color = v);
});

bindColor("accent_color", "accent_color_hex", function(v) {
    footer.style.borderLeftColor = v;
    document.querySelectorAll('.brand-dot').forEach(el => el.style.background = v);
});

/* Brand */
document.getElementById("brand_name").addEventListener("keyup", function() {
    document.querySelectorAll('.brand-name').forEach(el => {
        el.innerHTML = this.value;
    });
});

document.getElementById("brand_tagline").addEventListener("keyup", function() {
    document.querySelectorAll('#previewTagline, #previewTagline2').forEach(el => {
        if(el) el.innerHTML = this.value;
    });
});

/* Copyright */
document.getElementById("copyright_text").addEventListener("keyup", function() {
    const copyright = document.getElementById('previewCopyright');
    if (copyright) copyright.innerHTML = this.value;
});

</script>

</body>
</html>