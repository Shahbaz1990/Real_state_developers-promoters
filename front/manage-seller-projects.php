<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit;
}
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Manage Projects - Projects';
$activeSellerMenu = 'manage-seller-projects';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Tatito Developers</title>
    <link rel="icon" type="image/png" href="<?php echo $site['favicon'] ?? 'assets/images/favicon.png'; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/account-pages.css">
    <link rel="stylesheet" href="assets/css/seller-dashboard.css">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

<div class="seller-app-wrapper">
    <!-- Sidebar Navigation -->
    <?php include __DIR__ . '/includes/seller-sidebar.php'; ?>

    <!-- Main Content Area -->
    <main class="seller-main-content">
        <!-- Sub-Top Navigation Bar -->
        <?php include __DIR__ . '/includes/seller-subnav.php'; ?>

        <!-- Inner Content (Matching Reference Screenshot) -->
        <div class="seller-body-container">
            <!-- Top Header with Balance Pill -->
            <div class="seller-project-top-bar">
                <h1 class="seller-project-page-title">Manage Projects</h1>
                <div class="seller-project-balance-badge">
                    <span class="text-muted fw-semibold">Balance</span>
                    <span class="seller-badge-stat"><i class="bi bi-star-fill text-success"></i> - Ad Star</span>
                    <span class="seller-badge-stat"><i class="bi bi-suit-diamond-fill text-primary"></i> - Ad Pro</span>
                    <span class="seller-badge-stat">- Credits</span>
                </div>
            </div>

            <!-- Coming Soon Central Box (Matching Screenshot) -->
            <div class="seller-coming-soon-box">
                <div class="seller-coming-soon-graphic">
                    <svg width="150" height="150" viewBox="0 0 160 160" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Back Support Stand / Shadow -->
                        <polygon points="35,130 46,38 116,38 127,130" fill="#e8edf3"/>
                        <polygon points="120,42 136,132 116,132 106,42" fill="#cbd5e1" opacity="0.7"/>
                        
                        <!-- Main Calendar Card -->
                        <rect x="30" y="44" width="100" height="88" rx="8" fill="#ffffff" stroke="#d5e0ea" stroke-width="1.5"/>
                        
                        <!-- Header Strip -->
                        <path d="M30 52C30 47.5817 33.5817 44 38 44H122C126.418 44 130 47.5817 130 52V60H30V52Z" fill="#a8c7fa"/>
                        
                        <!-- Spiral Binding Rings at Top -->
                        <rect x="48" y="38" width="6" height="12" rx="3" fill="#64748b"/>
                        <rect x="77" y="38" width="6" height="12" rx="3" fill="#64748b"/>
                        <rect x="106" y="38" width="6" height="12" rx="3" fill="#64748b"/>
                        
                        <!-- Calendar Grid Cells -->
                        <rect x="42" y="68" width="10" height="8" rx="2" fill="#bfdbfe"/>
                        <rect x="58" y="68" width="10" height="8" rx="2" fill="#bfdbfe"/>
                        <rect x="74" y="68" width="10" height="8" rx="2" fill="#bfdbfe"/>
                        <rect x="90" y="68" width="10" height="8" rx="2" fill="#bfdbfe"/>
                        <rect x="106" y="68" width="10" height="8" rx="2" fill="#bfdbfe"/>
                        
                        <rect x="42" y="82" width="10" height="8" rx="2" fill="#bfdbfe"/>
                        <rect x="58" y="82" width="10" height="8" rx="2" fill="#bfdbfe"/>
                        <rect x="74" y="82" width="10" height="8" rx="2" fill="#bfdbfe"/>
                        <rect x="90" y="82" width="10" height="8" rx="2" fill="#bfdbfe"/>
                        <rect x="106" y="82" width="10" height="8" rx="2" fill="#bfdbfe"/>
                        
                        <rect x="42" y="96" width="10" height="8" rx="2" fill="#bfdbfe"/>
                        <rect x="58" y="96" width="10" height="8" rx="2" fill="#bfdbfe"/>
                        <rect x="74" y="96" width="10" height="8" rx="2" fill="#bfdbfe"/>
                        <rect x="90" y="96" width="10" height="8" rx="2" fill="#bfdbfe"/>
                        <rect x="106" y="96" width="10" height="8" rx="2" fill="#bfdbfe"/>
                        
                        <rect x="42" y="110" width="10" height="8" rx="2" fill="#bfdbfe"/>
                        <rect x="58" y="110" width="10" height="8" rx="2" fill="#bfdbfe"/>
                        <rect x="74" y="110" width="10" height="8" rx="2" fill="#bfdbfe"/>
                        <rect x="90" y="110" width="10" height="8" rx="2" fill="#bfdbfe"/>
                        <rect x="106" y="110" width="10" height="8" rx="2" fill="#bfdbfe"/>
                    </svg>
                </div>
                <h2 class="seller-coming-soon-title">Coming Soon!</h2>
                <p class="seller-coming-soon-desc">
                    Everything you need to manage your projects, in one place. Coming Soon to your locality.
                </p>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
