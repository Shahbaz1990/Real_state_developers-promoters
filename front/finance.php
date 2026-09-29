<?php
// ============================================================
// FINANCE PAGE - Loans, Banks & EMI Calculator
// Complete page with header, footer, and tab design
// ============================================================

// Set page title and site name dynamically
$pageTitle = "Finance - Loans, EMI Calculator & Bank Directory";
$siteName = isset($site['name']) ? $site['name'] : 'Tatito Developers & Promoters';

// You can also make it more dynamic based on the active tab
// For example, if a specific tab is active via URL parameter
if (isset($_GET['tab'])) {
    $tab = $_GET['tab'];
    switch ($tab) {
        case 'calculator':
            $pageTitle = "EMI Calculator - Finance";
            break;
        case 'compare':
            $pageTitle = "Compare Loans - Finance";
            break;
        default:
            $pageTitle = "Finance - Loans & Bank Directory";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($pageTitle); ?> — <?php echo htmlspecialchars($siteName); ?></title>

<!-- Meta Description for SEO -->
<meta name="description" content="Compare loans from SBI, HDFC, ICICI and more. Calculate your EMI instantly and apply for the best loan offers at <?php echo htmlspecialchars($siteName); ?>.">

<!-- Open Graph Meta Tags -->
<meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?> — <?php echo htmlspecialchars($siteName); ?>">
<meta property="og:description" content="Compare loans from all major banks, calculate your EMI, and apply directly. Your one-stop finance solution.">
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo isset($_SERVER['REQUEST_URI']) ? htmlspecialchars($_SERVER['REQUEST_URI']) : ''; ?>">
<meta property="og:image" content="/front/assets/images/og-image.jpg">

<!-- Favicon -->
<link rel="icon" type="image/png" href="/front/assets/images/favicon.png">
<link rel="apple-touch-icon" sizes="180x180" href="/front/assets/images/favicon.png">

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
/* ============================================================
   FINANCE PAGE DARK MODE FIXES
   These override and ensure proper dark mode display
   ============================================================ */

/* Dark Mode CSS Variables - Force these to apply */
body.dark-mode {
    --card-bg: #0d1117 !important;
    --bg-soft: #161b22 !important;
    --ink: #f0f6fc !important;
    --muted: #8b949e !important;
    --line: #30363d !important;
    --primary: #d4b85e !important;
    --border: #30363d !important;
    --surface-1: #161b22 !important;
    --surface-2: #0d1117 !important;
    --text-secondary: #8b949e !important;
    --text-muted: #6b7686 !important;
    --text-success: #3fb950 !important;
    --bg-accent: #1c2333 !important;
    --text-accent: #d4b85e !important;
}

/* Ensure all elements use CSS variables properly */
body.dark-mode .page-wrapper {
    background: transparent;
}

body.dark-mode .tab-link {
    color: var(--ink, #f0f6fc) !important;
}

body.dark-mode .tab-link.active {
    color: var(--primary, #d4b85e) !important;
    border-bottom-color: var(--primary, #d4b85e) !important;
}

body.dark-mode .tab-link:hover {
    color: var(--primary, #d4b85e) !important;
}

body.dark-mode .filter-btn {
    background: transparent !important;
    border-color: var(--line, #30363d) !important;
    color: var(--ink, #f0f6fc) !important;
}

body.dark-mode .filter-btn:hover {
    background: var(--bg-soft, #161b22) !important;
}

body.dark-mode .filter-btn.active {
    background: var(--primary, #d4b85e) !important;
    color: #0d1117 !important;
    border-color: var(--primary, #d4b85e) !important;
}

body.dark-mode .bank-card {
    background: var(--card-bg, #0d1117) !important;
    border-color: var(--line, #30363d) !important;
}

body.dark-mode .bank-card:hover {
    border-color: var(--primary, #d4b85e) !important;
    box-shadow: 0 12px 40px rgba(0,0,0,0.4) !important;
}

body.dark-mode .bank-card .bank-name {
    color: var(--ink, #f0f6fc) !important;
}

body.dark-mode .bank-card .bank-rate {
    color: var(--primary, #d4b85e) !important;
}

body.dark-mode .bank-card .loan-tag {
    background: var(--bg-soft, #161b22) !important;
    color: var(--muted, #8b949e) !important;
}

body.dark-mode .bank-card .bank-tag {
    background: var(--bg-soft, #161b22) !important;
    color: var(--muted, #8b949e) !important;
}

body.dark-mode .bank-card .rbi-badge {
    background: #1a3a2a !important;
    color: #3fb950 !important;
}

body.dark-mode .bank-card a:first-child {
    border-color: var(--line, #30363d) !important;
    color: var(--ink, #f0f6fc) !important;
}

body.dark-mode .bank-card a:first-child:hover {
    background: var(--bg-soft, #161b22) !important;
}

body.dark-mode .bank-card a:last-child {
    background: var(--primary, #d4b85e) !important;
    color: #0d1117 !important;
}

body.dark-mode .bank-card a:last-child:hover {
    opacity: 0.85;
}

/* EMI Calculator Dark Mode */
body.dark-mode #loanAmount,
body.dark-mode #interestRate,
body.dark-mode #loanTenure {
    background: var(--bg-soft, #161b22) !important;
    border-color: var(--line, #30363d) !important;
    color: var(--ink, #f0f6fc) !important;
}

body.dark-mode #loanAmount:focus,
body.dark-mode #interestRate:focus,
body.dark-mode #loanTenure:focus {
    border-color: var(--primary, #d4b85e) !important;
    box-shadow: 0 0 0 3px rgba(212, 184, 94, 0.15) !important;
}

body.dark-mode .emi-result-box {
    background: var(--bg-soft, #161b22) !important;
    border-color: var(--line, #30363d) !important;
}

body.dark-mode .emi-result-box .emi-label {
    color: var(--muted, #8b949e) !important;
}

body.dark-mode .emi-result-box .emi-value {
    color: var(--primary, #d4b85e) !important;
}

body.dark-mode .emi-result-box .emi-value-dark {
    color: var(--ink, #f0f6fc) !important;
}

body.dark-mode .emi-disclaimer {
    border-color: var(--line, #30363d) !important;
    color: var(--muted, #8b949e) !important;
}

/* Heading and text dark mode */
body.dark-mode .page-wrapper h1 {
    color: var(--ink, #f0f6fc) !important;
}

body.dark-mode .page-wrapper .subtitle {
    color: var(--muted, #8b949e) !important;
}

body.dark-mode .tab-content h2 {
    color: var(--ink, #f0f6fc) !important;
}

body.dark-mode .tab-content h3 {
    color: var(--ink, #f0f6fc) !important;
}

body.dark-mode .tab-content p {
    color: var(--muted, #8b949e) !important;
}

body.dark-mode .compare-placeholder i {
    color: var(--primary, #d4b85e) !important;
}

body.dark-mode .compare-placeholder button {
    background: var(--primary, #d4b85e) !important;
    color: #0d1117 !important;
}

/* Ensure labels are visible in dark mode */
body.dark-mode .form-label {
    color: var(--ink, #f0f6fc) !important;
}

/* Fix for the divider lines */
body.dark-mode .divider {
    border-color: var(--line, #30363d) !important;
}

/* Responsive fixes for dark mode */
@media (max-width: 768px) {
    body.dark-mode .filter-btn {
        font-size: 0.8rem !important;
        padding: 6px 14px !important;
    }
    
    body.dark-mode .tab-link {
        font-size: 0.85rem !important;
        padding: 0.5rem 1rem !important;
    }
}

@media (max-width: 480px) {
    body.dark-mode .bank-card {
        padding: 1rem !important;
    }
    
    body.dark-mode .bank-card .bank-name {
        font-size: 1rem !important;
    }
    
    body.dark-mode .bank-card a {
        font-size: 0.75rem !important;
        padding: 8px 0 !important;
    }
}

/* Finance Page Styles */
.page-wrapper {
    animation: fadeInUp 0.5s ease;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Tab Navigation */
.tab-link {
    position: relative;
    transition: all 0.3s ease;
}

.tab-link:hover {
    color: var(--primary, #c9a84c);
}

.tab-link.active {
    color: var(--primary, #c9a84c);
    border-bottom-color: var(--primary, #c9a84c) !important;
}

/* Filter Buttons */
.filter-btn {
    transition: all 0.3s ease;
    font-family: inherit;
    cursor: pointer;
}

.filter-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.filter-btn.active {
    background: var(--primary, #c9a84c);
    color: #fff;
    border-color: var(--primary, #c9a84c);
}

/* Bank Cards */
.bank-card {
    transition: all 0.3s ease;
}

.bank-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.08);
    border-color: var(--primary, #c9a84c) !important;
}

/* Bank Card Links */
.bank-card a {
    transition: all 0.3s ease;
}

.bank-card a:first-child:hover {
    background: var(--bg-soft, #f3f4f6);
}

.bank-card a:last-child:hover {
    opacity: 0.85;
    transform: scale(0.98);
}

/* EMI Calculator Inputs */
#loanAmount:focus,
#interestRate:focus,
#loanTenure:focus {
    outline: none;
    border-color: var(--primary, #c9a84c);
    box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.15);
}

/* Responsive */
@media (max-width: 768px) {
    .page-wrapper {
        padding: 1rem 0.75rem;
    }
    
    .page-wrapper h1 {
        font-size: 1.5rem;
    }
    
    #banksGrid {
        grid-template-columns: 1fr !important;
    }
    
    .filter-btn {
        font-size: 0.8rem !important;
        padding: 6px 14px !important;
    }
    
    .tab-link {
        padding: 0.5rem 1rem !important;
        font-size: 0.85rem !important;
    }
}

@media (max-width: 480px) {
    .bank-card {
        padding: 1rem !important;
    }
    
    .bank-card .bank-name {
        font-size: 1rem !important;
    }
    
    .bank-card a {
        font-size: 0.75rem !important;
        padding: 8px 0 !important;
    }
    
    .tab-link {
        font-size: 0.75rem !important;
        padding: 0.4rem 0.8rem !important;
    }
}
</style>

</head>
<body>
<?php
// Include your site's header
// Adjust the path based on your project structure
include __DIR__ . '/includes/header.php';
?>

<!-- ============================================================
     PAGE CONTENT STARTS HERE
     ============================================================ -->
<div class="page-wrapper" style="max-width:1200px; margin:0 auto; padding:2rem 1.5rem;">

    <!-- Page Header with Breadcrumb -->
    <div style="margin-bottom:2rem;">
        
        <h1 style="font-size:2rem; font-weight:700; color:var(--ink, #12213a); margin:0 0 0.5rem 0; display:flex; align-items:center; gap:12px;">
            <i class="bi bi-bank" style="color:var(--primary, #c9a84c);"></i>
            Finance Center
        </h1>
        <p style="font-size:1rem; color:var(--muted, #6b7280); margin:0;" class="subtitle">
            Compare loans from all major banks, calculate your EMI, and apply directly
        </p>
    </div>

    <!-- Tab Navigation -->
    <div style="border-bottom:2px solid var(--line, #e7e9ee); margin-bottom:2rem; display:flex; flex-wrap:wrap; gap:0;">
        <a href="#banks" class="tab-link active" data-tab="banks" style="padding:0.75rem 1.5rem; font-size:0.95rem; font-weight:600; color:var(--ink, #12213a); text-decoration:none; border-bottom:3px solid var(--primary, #c9a84c); transition:all 0.3s ease;">
            <i class="bi bi-building"></i> Banks & Loans
        </a>
        <a href="#calculator" class="tab-link" data-tab="calculator" style="padding:0.75rem 1.5rem; font-size:0.95rem; font-weight:600; color:var(--muted, #6b7280); text-decoration:none; border-bottom:3px solid transparent; transition:all 0.3s ease;">
            <i class="bi bi-calculator"></i> EMI Calculator
        </a>
        <a href="#compare" class="tab-link" data-tab="compare" style="padding:0.75rem 1.5rem; font-size:0.95rem; font-weight:600; color:var(--muted, #6b7280); text-decoration:none; border-bottom:3px solid transparent; transition:all 0.3s ease;">
            <i class="bi bi-graph-up-arrow"></i> Compare Loans
        </a>
    </div>

    <!-- Tab Content -->
    <div class="tab-content-wrapper">

        <!-- TAB 1: BANKS & LOANS -->
        <div id="tab-banks" class="tab-content active" style="display:block;">
            
            <!-- Loan Type Filters -->
            <div style="display:flex; flex-wrap:wrap; gap:10px; justify-content:center; margin-bottom:2rem;">
                <button class="filter-btn active" data-filter="all" style="padding:8px 20px; border-radius:999px; border:none; font-size:0.9rem; font-weight:600; cursor:pointer; transition:all 0.3s ease; background:var(--primary, #c9a84c); color:#fff;">All Loans</button>
                <button class="filter-btn" data-filter="personal" style="padding:8px 20px; border-radius:999px; border:1px solid var(--line, #e7e9ee); font-size:0.9rem; font-weight:500; cursor:pointer; transition:all 0.3s ease; background:transparent; color:var(--ink, #12213a);">Personal</button>
                <button class="filter-btn" data-filter="home" style="padding:8px 20px; border-radius:999px; border:1px solid var(--line, #e7e9ee); font-size:0.9rem; font-weight:500; cursor:pointer; transition:all 0.3s ease; background:transparent; color:var(--ink, #12213a);">Home</button>
                <button class="filter-btn" data-filter="car" style="padding:8px 20px; border-radius:999px; border:1px solid var(--line, #e7e9ee); font-size:0.9rem; font-weight:500; cursor:pointer; transition:all 0.3s ease; background:transparent; color:var(--ink, #12213a);">Car</button>
                <button class="filter-btn" data-filter="business" style="padding:8px 20px; border-radius:999px; border:1px solid var(--line, #e7e9ee); font-size:0.9rem; font-weight:500; cursor:pointer; transition:all 0.3s ease; background:transparent; color:var(--ink, #12213a);">Business</button>
                <button class="filter-btn" data-filter="gold" style="padding:8px 20px; border-radius:999px; border:1px solid var(--line, #e7e9ee); font-size:0.9rem; font-weight:500; cursor:pointer; transition:all 0.3s ease; background:transparent; color:var(--ink, #12213a);">Gold</button>
                <button class="filter-btn" data-filter="education" style="padding:8px 20px; border-radius:999px; border:1px solid var(--line, #e7e9ee); font-size:0.9rem; font-weight:500; cursor:pointer; transition:all 0.3s ease; background:transparent; color:var(--ink, #12213a);">Education</button>
            </div>

            <!-- Banks Grid -->
            <div id="banksGrid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:20px; margin-bottom:2rem;">
                
                <!-- Bank 1: SBI -->
                <div class="bank-card" data-loans="personal home car gold" style="background:var(--card-bg, #fff); border:1px solid var(--line, #e7e9ee); border-radius:16px; padding:1.5rem; transition:all 0.3s ease; box-shadow:0 2px 10px rgba(0,0,0,0.04);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.5rem;">
                        <span class="bank-tag" style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; padding:3px 12px; border-radius:999px; background:var(--bg-soft, #f3f4f6); color:var(--muted, #6b7280);">🏦 BANK</span>
                        <span class="rbi-badge" style="font-size:0.75rem; color:#16a34a; font-weight:600; background:#dcfce7; padding:2px 10px; border-radius:999px;">RBI Approved</span>
                    </div>
                    <div class="bank-name" style="font-size:1.2rem; font-weight:700; color:var(--ink, #12213a); margin:0.3rem 0 0.2rem;">State Bank of India</div>
                    <div style="font-size:0.85rem; color:var(--muted, #6b7280); margin-bottom:0.5rem;">SBI</div>
                    <div class="bank-rate" style="font-size:1.1rem; color:var(--primary, #c9a84c); font-weight:700; margin:0.5rem 0;">From 10.00% p.a.</div>
                    <div style="font-size:0.8rem; color:var(--muted, #6b7280); margin-bottom:1rem; display:flex; flex-wrap:wrap; gap:4px;">
                        <span class="loan-tag" style="background:var(--bg-soft, #f3f4f6); padding:2px 10px; border-radius:999px;">Personal</span>
                        <span class="loan-tag" style="background:var(--bg-soft, #f3f4f6); padding:2px 10px; border-radius:999px;">Home</span>
                        <span class="loan-tag" style="background:var(--bg-soft, #f3f4f6); padding:2px 10px; border-radius:999px;">Car</span>
                        <span class="loan-tag" style="background:var(--bg-soft, #f3f4f6); padding:2px 10px; border-radius:999px;">Gold</span>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <a href="#" style="flex:1; text-align:center; font-size:0.85rem; font-weight:600; padding:10px 0; border-radius:10px; border:1px solid var(--line, #e7e9ee); color:var(--ink, #12213a); text-decoration:none; transition:all 0.3s ease;">Apply on SBI</a>
                        <a href="#" style="flex:1; text-align:center; font-size:0.85rem; font-weight:600; padding:10px 0; border-radius:10px; background:var(--primary, #c9a84c); color:#fff; border:none; text-decoration:none; transition:all 0.3s ease;">Apply via us</a>
                    </div>
                </div>

                <!-- Bank 2: HDFC -->
                <div class="bank-card" data-loans="personal home car business" style="background:var(--card-bg, #fff); border:1px solid var(--line, #e7e9ee); border-radius:16px; padding:1.5rem; transition:all 0.3s ease; box-shadow:0 2px 10px rgba(0,0,0,0.04);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.5rem;">
                        <span class="bank-tag" style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; padding:3px 12px; border-radius:999px; background:var(--bg-soft, #f3f4f6); color:var(--muted, #6b7280);">🏦 BANK</span>
                        <span class="rbi-badge" style="font-size:0.75rem; color:#16a34a; font-weight:600; background:#dcfce7; padding:2px 10px; border-radius:999px;">RBI Approved</span>
                    </div>
                    <div class="bank-name" style="font-size:1.2rem; font-weight:700; color:var(--ink, #12213a); margin:0.3rem 0 0.2rem;">HDFC Bank</div>
                    <div style="font-size:0.85rem; color:var(--muted, #6b7280); margin-bottom:0.5rem;">HDFC</div>
                    <div class="bank-rate" style="font-size:1.1rem; color:var(--primary, #c9a84c); font-weight:700; margin:0.5rem 0;">From 10.50% p.a.</div>
                    <div style="font-size:0.8rem; color:var(--muted, #6b7280); margin-bottom:1rem; display:flex; flex-wrap:wrap; gap:4px;">
                        <span class="loan-tag" style="background:var(--bg-soft, #f3f4f6); padding:2px 10px; border-radius:999px;">Personal</span>
                        <span class="loan-tag" style="background:var(--bg-soft, #f3f4f6); padding:2px 10px; border-radius:999px;">Home</span>
                        <span class="loan-tag" style="background:var(--bg-soft, #f3f4f6); padding:2px 10px; border-radius:999px;">Car</span>
                        <span class="loan-tag" style="background:var(--bg-soft, #f3f4f6); padding:2px 10px; border-radius:999px;">Business</span>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <a href="#" style="flex:1; text-align:center; font-size:0.85rem; font-weight:600; padding:10px 0; border-radius:10px; border:1px solid var(--line, #e7e9ee); color:var(--ink, #12213a); text-decoration:none; transition:all 0.3s ease;">Apply on HDFC</a>
                        <a href="#" style="flex:1; text-align:center; font-size:0.85rem; font-weight:600; padding:10px 0; border-radius:10px; background:var(--primary, #c9a84c); color:#fff; border:none; text-decoration:none; transition:all 0.3s ease;">Apply via us</a>
                    </div>
                </div>

                <!-- Bank 3: ICICI -->
                <div class="bank-card" data-loans="personal home car education" style="background:var(--card-bg, #fff); border:1px solid var(--line, #e7e9ee); border-radius:16px; padding:1.5rem; transition:all 0.3s ease; box-shadow:0 2px 10px rgba(0,0,0,0.04);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.5rem;">
                        <span class="bank-tag" style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; padding:3px 12px; border-radius:999px; background:var(--bg-soft, #f3f4f6); color:var(--muted, #6b7280);">🏦 BANK</span>
                        <span class="rbi-badge" style="font-size:0.75rem; color:#16a34a; font-weight:600; background:#dcfce7; padding:2px 10px; border-radius:999px;">RBI Approved</span>
                    </div>
                    <div class="bank-name" style="font-size:1.2rem; font-weight:700; color:var(--ink, #12213a); margin:0.3rem 0 0.2rem;">ICICI Bank</div>
                    <div style="font-size:0.85rem; color:var(--muted, #6b7280); margin-bottom:0.5rem;">ICICI</div>
                    <div class="bank-rate" style="font-size:1.1rem; color:var(--primary, #c9a84c); font-weight:700; margin:0.5rem 0;">From 9.99% p.a.</div>
                    <div style="font-size:0.8rem; color:var(--muted, #6b7280); margin-bottom:1rem; display:flex; flex-wrap:wrap; gap:4px;">
                        <span class="loan-tag" style="background:var(--bg-soft, #f3f4f6); padding:2px 10px; border-radius:999px;">Personal</span>
                        <span class="loan-tag" style="background:var(--bg-soft, #f3f4f6); padding:2px 10px; border-radius:999px;">Home</span>
                        <span class="loan-tag" style="background:var(--bg-soft, #f3f4f6); padding:2px 10px; border-radius:999px;">Car</span>
                        <span class="loan-tag" style="background:var(--bg-soft, #f3f4f6); padding:2px 10px; border-radius:999px;">Education</span>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <a href="#" style="flex:1; text-align:center; font-size:0.85rem; font-weight:600; padding:10px 0; border-radius:10px; border:1px solid var(--line, #e7e9ee); color:var(--ink, #12213a); text-decoration:none; transition:all 0.3s ease;">Apply on ICICI</a>
                        <a href="#" style="flex:1; text-align:center; font-size:0.85rem; font-weight:600; padding:10px 0; border-radius:10px; background:var(--primary, #c9a84c); color:#fff; border:none; text-decoration:none; transition:all 0.3s ease;">Apply via us</a>
                    </div>
                </div>

                <!-- Bank 4: Axis -->
                <div class="bank-card" data-loans="personal home car business gold" style="background:var(--card-bg, #fff); border:1px solid var(--line, #e7e9ee); border-radius:16px; padding:1.5rem; transition:all 0.3s ease; box-shadow:0 2px 10px rgba(0,0,0,0.04);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.5rem;">
                        <span class="bank-tag" style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; padding:3px 12px; border-radius:999px; background:var(--bg-soft, #f3f4f6); color:var(--muted, #6b7280);">🏦 BANK</span>
                        <span class="rbi-badge" style="font-size:0.75rem; color:#16a34a; font-weight:600; background:#dcfce7; padding:2px 10px; border-radius:999px;">RBI Approved</span>
                    </div>
                    <div class="bank-name" style="font-size:1.2rem; font-weight:700; color:var(--ink, #12213a); margin:0.3rem 0 0.2rem;">Axis Bank</div>
                    <div style="font-size:0.85rem; color:var(--muted, #6b7280); margin-bottom:0.5rem;">Axis</div>
                    <div class="bank-rate" style="font-size:1.1rem; color:var(--primary, #c9a84c); font-weight:700; margin:0.5rem 0;">From 10.25% p.a.</div>
                    <div style="font-size:0.8rem; color:var(--muted, #6b7280); margin-bottom:1rem; display:flex; flex-wrap:wrap; gap:4px;">
                        <span class="loan-tag" style="background:var(--bg-soft, #f3f4f6); padding:2px 10px; border-radius:999px;">Personal</span>
                        <span class="loan-tag" style="background:var(--bg-soft, #f3f4f6); padding:2px 10px; border-radius:999px;">Home</span>
                        <span class="loan-tag" style="background:var(--bg-soft, #f3f4f6); padding:2px 10px; border-radius:999px;">Car</span>
                        <span class="loan-tag" style="background:var(--bg-soft, #f3f4f6); padding:2px 10px; border-radius:999px;">Business</span>
                        <span class="loan-tag" style="background:var(--bg-soft, #f3f4f6); padding:2px 10px; border-radius:999px;">Gold</span>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <a href="#" style="flex:1; text-align:center; font-size:0.85rem; font-weight:600; padding:10px 0; border-radius:10px; border:1px solid var(--line, #e7e9ee); color:var(--ink, #12213a); text-decoration:none; transition:all 0.3s ease;">Apply on Axis</a>
                        <a href="#" style="flex:1; text-align:center; font-size:0.85rem; font-weight:600; padding:10px 0; border-radius:10px; background:var(--primary, #c9a84c); color:#fff; border:none; text-decoration:none; transition:all 0.3s ease;">Apply via us</a>
                    </div>
                </div>

                <!-- Bank 5: Kotak -->
                <div class="bank-card" data-loans="personal home car education" style="background:var(--card-bg, #fff); border:1px solid var(--line, #e7e9ee); border-radius:16px; padding:1.5rem; transition:all 0.3s ease; box-shadow:0 2px 10px rgba(0,0,0,0.04);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.5rem;">
                        <span class="bank-tag" style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; padding:3px 12px; border-radius:999px; background:var(--bg-soft, #f3f4f6); color:var(--muted, #6b7280);">🏦 BANK</span>
                        <span class="rbi-badge" style="font-size:0.75rem; color:#16a34a; font-weight:600; background:#dcfce7; padding:2px 10px; border-radius:999px;">RBI Approved</span>
                    </div>
                    <div class="bank-name" style="font-size:1.2rem; font-weight:700; color:var(--ink, #12213a); margin:0.3rem 0 0.2rem;">Kotak Mahindra Bank</div>
                    <div style="font-size:0.85rem; color:var(--muted, #6b7280); margin-bottom:0.5rem;">Kotak</div>
                    <div class="bank-rate" style="font-size:1.1rem; color:var(--primary, #c9a84c); font-weight:700; margin:0.5rem 0;">From 10.75% p.a.</div>
                    <div style="font-size:0.8rem; color:var(--muted, #6b7280); margin-bottom:1rem; display:flex; flex-wrap:wrap; gap:4px;">
                        <span class="loan-tag" style="background:var(--bg-soft, #f3f4f6); padding:2px 10px; border-radius:999px;">Personal</span>
                        <span class="loan-tag" style="background:var(--bg-soft, #f3f4f6); padding:2px 10px; border-radius:999px;">Home</span>
                        <span class="loan-tag" style="background:var(--bg-soft, #f3f4f6); padding:2px 10px; border-radius:999px;">Car</span>
                        <span class="loan-tag" style="background:var(--bg-soft, #f3f4f6); padding:2px 10px; border-radius:999px;">Education</span>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <a href="#" style="flex:1; text-align:center; font-size:0.85rem; font-weight:600; padding:10px 0; border-radius:10px; border:1px solid var(--line, #e7e9ee); color:var(--ink, #12213a); text-decoration:none; transition:all 0.3s ease;">Apply on Kotak</a>
                        <a href="#" style="flex:1; text-align:center; font-size:0.85rem; font-weight:600; padding:10px 0; border-radius:10px; background:var(--primary, #c9a84c); color:#fff; border:none; text-decoration:none; transition:all 0.3s ease;">Apply via us</a>
                    </div>
                </div>

                <!-- Bank 6: Yes Bank -->
                <div class="bank-card" data-loans="personal business gold" style="background:var(--card-bg, #fff); border:1px solid var(--line, #e7e9ee); border-radius:16px; padding:1.5rem; transition:all 0.3s ease; box-shadow:0 2px 10px rgba(0,0,0,0.04);">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.5rem;">
                        <span class="bank-tag" style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; padding:3px 12px; border-radius:999px; background:var(--bg-soft, #f3f4f6); color:var(--muted, #6b7280);">🏦 BANK</span>
                        <span class="rbi-badge" style="font-size:0.75rem; color:#16a34a; font-weight:600; background:#dcfce7; padding:2px 10px; border-radius:999px;">RBI Approved</span>
                    </div>
                    <div class="bank-name" style="font-size:1.2rem; font-weight:700; color:var(--ink, #12213a); margin:0.3rem 0 0.2rem;">Yes Bank</div>
                    <div style="font-size:0.85rem; color:var(--muted, #6b7280); margin-bottom:0.5rem;">Yes</div>
                    <div class="bank-rate" style="font-size:1.1rem; color:var(--primary, #c9a84c); font-weight:700; margin:0.5rem 0;">From 11.00% p.a.</div>
                    <div style="font-size:0.8rem; color:var(--muted, #6b7280); margin-bottom:1rem; display:flex; flex-wrap:wrap; gap:4px;">
                        <span class="loan-tag" style="background:var(--bg-soft, #f3f4f6); padding:2px 10px; border-radius:999px;">Personal</span>
                        <span class="loan-tag" style="background:var(--bg-soft, #f3f4f6); padding:2px 10px; border-radius:999px;">Business</span>
                        <span class="loan-tag" style="background:var(--bg-soft, #f3f4f6); padding:2px 10px; border-radius:999px;">Gold</span>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <a href="#" style="flex:1; text-align:center; font-size:0.85rem; font-weight:600; padding:10px 0; border-radius:10px; border:1px solid var(--line, #e7e9ee); color:var(--ink, #12213a); text-decoration:none; transition:all 0.3s ease;">Apply on Yes Bank</a>
                        <a href="#" style="flex:1; text-align:center; font-size:0.85rem; font-weight:600; padding:10px 0; border-radius:10px; background:var(--primary, #c9a84c); color:#fff; border:none; text-decoration:none; transition:all 0.3s ease;">Apply via us</a>
                    </div>
                </div>
            </div>
            
            <!-- View All Banks Link -->
            <div style="text-align:center; margin-top:1rem;">
                <a href="#" style="color:var(--primary, #c9a84c); font-weight:600; text-decoration:none; font-size:0.95rem;">
                    View All Banks <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- TAB 2: EMI CALCULATOR -->
        <div id="tab-calculator" class="tab-content" style="display:none; max-width:700px; margin:0 auto;">
            <div style="text-align:center; margin-bottom:1.5rem;">
                <h2 style="font-size:1.5rem; font-weight:600; color:var(--ink, #12213a); margin:0 0 0.3rem 0;">
                    <i class="bi bi-calculator" style="color:var(--primary, #c9a84c);"></i>
                    EMI Calculator
                </h2>
                <p style="font-size:0.9rem; color:var(--muted, #6b7280); margin:0;">Calculate your monthly EMI instantly</p>
            </div>

            <div style="background:var(--card-bg, #fff); border:1px solid var(--line, #e7e9ee); border-radius:16px; padding:1.5rem 2rem; box-shadow:0 2px 10px rgba(0,0,0,0.04);">
                <!-- Input Fields -->
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:1.2rem; margin-bottom:1.5rem;">
                    <div>
                        <label class="form-label" style="display:block; font-size:0.85rem; font-weight:600; color:var(--ink, #12213a); margin-bottom:0.3rem;">
                            <i class="bi bi-currency-rupee" style="color:var(--primary, #c9a84c);"></i> Loan Amount
                        </label>
                        <input type="number" id="loanAmount" value="500000" step="1000" min="0" style="width:100%; padding:0.7rem 1rem; border:1px solid var(--line, #e7e9ee); border-radius:10px; font-size:1rem; background:var(--bg-soft, #f8f9fa); color:var(--ink, #12213a); transition:border-color 0.3s ease;">
                    </div>
                    <div>
                        <label class="form-label" style="display:block; font-size:0.85rem; font-weight:600; color:var(--ink, #12213a); margin-bottom:0.3rem;">
                            <i class="bi bi-percent" style="color:var(--primary, #c9a84c);"></i> Interest Rate (% p.a.)
                        </label>
                        <input type="number" id="interestRate" value="10.5" step="0.05" min="0" style="width:100%; padding:0.7rem 1rem; border:1px solid var(--line, #e7e9ee); border-radius:10px; font-size:1rem; background:var(--bg-soft, #f8f9fa); color:var(--ink, #12213a); transition:border-color 0.3s ease;">
                    </div>
                    <div>
                        <label class="form-label" style="display:block; font-size:0.85rem; font-weight:600; color:var(--ink, #12213a); margin-bottom:0.3rem;">
                            <i class="bi bi-clock" style="color:var(--primary, #c9a84c);"></i> Tenure (months)
                        </label>
                        <input type="number" id="loanTenure" value="60" step="1" min="1" max="360" style="width:100%; padding:0.7rem 1rem; border:1px solid var(--line, #e7e9ee); border-radius:10px; font-size:1rem; background:var(--bg-soft, #f8f9fa); color:var(--ink, #12213a); transition:border-color 0.3s ease;">
                    </div>
                </div>

                <!-- Results Grid -->
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(160px, 1fr)); gap:1rem;">
                    <div class="emi-result-box" style="background:var(--bg-soft, #f8f9fa); border-radius:12px; padding:1rem 1.2rem; text-align:center; border:1px solid var(--line, #e7e9ee);">
                        <div class="emi-label" style="font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.3px; color:var(--muted, #6b7280); margin-bottom:0.2rem;">Monthly EMI</div>
                        <div id="emiResult" class="emi-value" style="font-size:1.6rem; font-weight:700; color:var(--primary, #c9a84c);">₹0</div>
                    </div>
                    <div class="emi-result-box" style="background:var(--bg-soft, #f8f9fa); border-radius:12px; padding:1rem 1.2rem; text-align:center; border:1px solid var(--line, #e7e9ee);">
                        <div class="emi-label" style="font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.3px; color:var(--muted, #6b7280); margin-bottom:0.2rem;">Total Interest</div>
                        <div id="totalInterest" class="emi-value-dark" style="font-size:1.6rem; font-weight:700; color:var(--ink, #12213a);">₹0</div>
                    </div>
                    <div class="emi-result-box" style="background:var(--bg-soft, #f8f9fa); border-radius:12px; padding:1rem 1.2rem; text-align:center; border:1px solid var(--line, #e7e9ee);">
                        <div class="emi-label" style="font-size:0.75rem; font-weight:600; text-transform:uppercase; letter-spacing:0.3px; color:var(--muted, #6b7280); margin-bottom:0.2rem;">Total Payment</div>
                        <div id="totalPayment" class="emi-value-dark" style="font-size:1.6rem; font-weight:700; color:var(--ink, #12213a);">₹0</div>
                    </div>
                </div>

                <!-- Disclaimer -->
                <p class="emi-disclaimer" style="font-size:0.7rem; color:var(--muted, #6b7280); text-align:center; margin-top:1rem; border-top:1px solid var(--line, #e7e9ee); padding-top:0.8rem;">
                    <i class="bi bi-info-circle"></i> This is an indicative EMI calculation. Actual rates may vary based on bank policies and your credit profile.
                </p>
            </div>
        </div>

        <!-- TAB 3: COMPARE LOANS -->
        <div id="tab-compare" class="tab-content" style="display:none;">
            <div class="compare-placeholder" style="text-align:center; padding:3rem 1rem;">
                <i class="bi bi-graph-up-arrow" style="font-size:3rem; color:var(--primary, #c9a84c); display:block; margin-bottom:1rem;"></i>
                <h3 style="font-size:1.3rem; font-weight:600; color:var(--ink, #12213a); margin:0 0 0.5rem 0;">Compare Loan Offers</h3>
                <p style="color:var(--muted, #6b7280); max-width:500px; margin:0 auto 1.5rem;">
                    Select multiple banks to compare interest rates, processing fees, and loan terms side by side.
                </p>
                <button style="background:var(--primary, #c9a84c); color:#fff; border:none; border-radius:10px; padding:0.8rem 2rem; font-weight:600; cursor:pointer; transition:all 0.3s ease;">
                    Start Comparing
                </button>
            </div>
        </div>
    </div>

</div>

<?php
// ============================================================
// INCLUDE FOOTER
// ============================================================
include __DIR__ . '/includes/footer.php'; // Adjust path as needed
?>
        
<!-- ===== ALL SCRIPTS AT THE BOTTOM (jQuery FIRST) ===== -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Finance Page Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ===== TAB FUNCTIONALITY =====
    var tabLinks = document.querySelectorAll('.tab-link');
    var tabContents = {
        banks: document.getElementById('tab-banks'),
        calculator: document.getElementById('tab-calculator'),
        compare: document.getElementById('tab-compare')
    };
    
    tabLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Update active tab link
            tabLinks.forEach(function(l) { l.classList.remove('active'); });
            this.classList.add('active');
            
            // Update active tab content
            var tab = this.getAttribute('data-tab');
            Object.keys(tabContents).forEach(function(key) {
                if (tabContents[key]) {
                    tabContents[key].style.display = (key === tab) ? 'block' : 'none';
                }
            });
        });
    });
    
    // ===== FILTER FUNCTIONALITY =====
    var filterBtns = document.querySelectorAll('.filter-btn');
    var bankCards = document.querySelectorAll('.bank-card');
    
    filterBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            // Update active button
            filterBtns.forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            
            var filter = this.getAttribute('data-filter');
            
            bankCards.forEach(function(card) {
                var loans = card.getAttribute('data-loans') || '';
                
                if (filter === 'all') {
                    card.style.display = 'block';
                } else {
                    if (loans.includes(filter)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                }
            });
        });
    });
    
    // ===== EMI CALCULATOR =====
    var loanAmount = document.getElementById('loanAmount');
    var interestRate = document.getElementById('interestRate');
    var loanTenure = document.getElementById('loanTenure');
    var emiResult = document.getElementById('emiResult');
    var totalInterest = document.getElementById('totalInterest');
    var totalPayment = document.getElementById('totalPayment');
    
    function calculateEMI() {
        var p = parseFloat(loanAmount.value) || 0;
        var r = parseFloat(interestRate.value) || 0;
        var t = parseFloat(loanTenure.value) || 0;
        
        var monthlyRate = (r / 12) / 100;
        var emi = 0;
        
        if (t > 0 && p > 0) {
            if (monthlyRate === 0) {
                emi = p / t;
            } else {
                var power = Math.pow(1 + monthlyRate, t);
                emi = (p * monthlyRate * power) / (power - 1);
            }
        }
        
        var total = emi * t;
        var interest = total - p;
        
        emiResult.textContent = '₹' + Math.round(emi).toLocaleString('en-IN');
        totalInterest.textContent = '₹' + Math.round(interest).toLocaleString('en-IN');
        totalPayment.textContent = '₹' + Math.round(total).toLocaleString('en-IN');
    }
    
    // Add event listeners
    [loanAmount, interestRate, loanTenure].forEach(function(input) {
        input.addEventListener('input', calculateEMI);
    });
    
    // Initial calculation
    calculateEMI();
    
});
</script>
</body>
</html>