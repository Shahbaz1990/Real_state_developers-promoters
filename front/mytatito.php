<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit;
}
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'MyTATITO - Seller Dashboard';
$activeSellerMenu = 'mytatito';
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
    <!-- Left Navigation Panel -->
    <?php include __DIR__ . '/includes/seller-sidebar.php'; ?>

    <!-- Main Content Area -->
    <main class="seller-main-content">
        <!-- Sub-Top Navigation Bar -->
        <?php include __DIR__ . '/includes/seller-subnav.php'; ?>

        <!-- Inner Content -->
        <div class="seller-body-container">
            <!-- Period Selector Filter -->
            <div class="mb-3">
                <select class="seller-period-select" id="sellerPeriodFilter" aria-label="Select Period">
                    <option selected>Last 30 Days</option>
                    <option>Last 7 Days</option>
                    <option>Last 90 Days</option>
                    <option>This Month</option>
                    <option>All Time</option>
                </select>
            </div>

            <!-- New Projects Response Card -->
            <div class="seller-metric-card">
                <div class="seller-metric-header">
                    <h3 class="seller-metric-title">New Projects</h3>
                    <i class="bi bi-info-circle seller-metric-info-icon" title="Response and leads received for your active projects in the selected period"></i>
                </div>
                <div class="seller-metric-total">
                    <strong>82</strong> responses
                </div>

                <div class="seller-metric-subcard">
                    <div class="seller-megaphone-icon">
                        <i class="bi bi-megaphone-fill"></i>
                    </div>
                    <div>
                        <div class="seller-enquiry-label">Enquiry</div>
                        <div class="seller-enquiry-count-row">
                            <span class="seller-enquiry-number">82</span>
                            <span class="seller-badge-new">new</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Promotional / Feature Banners -->
            <div class="seller-promo-grid">
                <!-- Mobile App Promo Card -->
                <div class="seller-promo-card seller-promo-blue">
                    <div class="seller-promo-content">
                        <h4>See responses on the go</h4>
                        <p>On Tatito mobile app</p>
                        <a href="javascript:void(0)" class="seller-promo-btn">Get mobile app</a>
                    </div>
                    <div class="seller-promo-illustration">
                        <svg viewBox="0 0 100 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Mobile Phone Mockup Illustration -->
                            <rect x="15" y="5" width="70" height="110" rx="12" fill="#1b2a4a" stroke="#0969da" stroke-width="3"/>
                            <rect x="20" y="15" width="60" height="90" rx="4" fill="#ffffff"/>
                            <rect x="38" y="9" width="24" height="3" rx="1.5" fill="#485b7f"/>
                            <circle cx="50" cy="110" r="3" fill="#485b7f"/>
                            <!-- App Content Wireframe -->
                            <rect x="25" y="22" width="50" height="28" rx="4" fill="#0969da"/>
                            <rect x="25" y="56" width="35" height="5" rx="2" fill="#24292f"/>
                            <rect x="25" y="65" width="50" height="4" rx="2" fill="#8c959f"/>
                            <rect x="25" y="73" width="45" height="4" rx="2" fill="#8c959f"/>
                            <rect x="25" y="84" width="50" height="14" rx="3" fill="#dafbe1"/>
                        </svg>
                    </div>
                </div>

                <!-- FAQ / Help Promo Card -->
                <div class="seller-promo-card seller-promo-yellow">
                    <div class="seller-promo-content">
                        <h4>Need help with any feature?</h4>
                        <p>Get help from FAQ section</p>
                        <a href="javascript:void(0)" class="seller-promo-btn">See FAQ section</a>
                    </div>
                    <div class="seller-promo-illustration">
                        <svg viewBox="0 0 100 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Help Hand & Document Illustration -->
                            <rect x="20" y="20" width="60" height="75" rx="8" fill="#ffffff" stroke="#f5b942" stroke-width="2.5"/>
                            <rect x="28" y="32" width="20" height="14" rx="3" fill="#f5b942"/>
                            <rect x="28" y="53" width="44" height="4" rx="2" fill="#24292f"/>
                            <rect x="28" y="62" width="36" height="4" rx="2" fill="#8c959f"/>
                            <rect x="28" y="71" width="40" height="4" rx="2" fill="#8c959f"/>
                            <!-- Helping Hand Graphic -->
                            <path d="M12 105C12 90 28 85 45 92C60 98 75 92 88 105" stroke="#f5b942" stroke-width="4" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Bottom Support & Queries Box -->
            <?php include __DIR__ . '/includes/seller-contact-footer.php'; ?>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
