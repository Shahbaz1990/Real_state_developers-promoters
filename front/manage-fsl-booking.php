<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit;
}
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'FSL Booking - Featured Listings Subscription - Tatito Developers';
$activeSellerMenu = 'fsl-booking';

// Data placeholders for backend integration (Locality FSL vs Filter FSL)
$localityFslData = [];
$filterFslData = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
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
    <!-- Left Navigation Panel (FSL Booking Highlighted - Matching Screenshot) -->
    <?php include __DIR__ . '/includes/seller-sidebar.php'; ?>

    <!-- Main Content Area -->
    <main class="seller-main-content">
        <!-- Sub-Top Navigation Bar -->
        <?php include __DIR__ . '/includes/seller-subnav.php'; ?>

        <div class="seller-body-container">
            <!-- Notice / Requirement Banner (Matching Screenshot) -->
            <div class="seller-alert-banner mb-4" id="fslPhotoBanner">
                <div class="seller-alert-banner-content">
                    <i class="bi bi-megaphone-fill text-dark me-1"></i>
                    <strong>FSL Photo Requirement</strong>
                    <span>We are allowing only listings with a minimum number of photos to activate for FSL Bookings</span>
                    <a href="javascript:void(0)" class="seller-alert-link" data-bs-toggle="modal" data-bs-target="#fslEligibilityModal">Check Eligibility</a>
                </div>
                <button type="button" class="seller-alert-close-btn" onclick="document.getElementById('fslPhotoBanner').style.display='none';" aria-label="Close">&times;</button>
            </div>

            <!-- Section Title -->
            <div class="fw-bold mb-2 text-dark" style="font-size: 0.95rem;">Featured Listings Subscription Details:</div>

            <!-- Card Box (Matching Screenshot Box Container) -->
            <div class="seller-fsl-box">
                <!-- Tab Header Row -->
                <div class="d-flex align-items-center gap-3 px-3 pt-3 pb-2 border-bottom">
                    <button type="button" class="seller-fsl-tab-link active" id="btnLocalityFsl" data-fsl-tab="locality">Locality FSL</button>
                    <span class="text-muted opacity-50">|</span>
                    <button type="button" class="seller-fsl-tab-link" id="btnFilterFsl" data-fsl-tab="filter">Filter FSL</button>
                </div>

                <!-- Info Callout Row -->
                <div class="d-flex align-items-center gap-2 px-3 py-2 bg-light border-bottom text-muted small" id="infoLocalityFsl">
                    <i class="bi bi-info-circle text-muted"></i>
                    <span>In case Filter FSL is already booked, Locality FSL will not be visible in relevant filter searches</span>
                </div>
                <div class="d-flex align-items-center gap-2 px-3 py-2 bg-light border-bottom text-muted small" id="infoFilterFsl" style="display: none;">
                    <i class="bi bi-info-circle text-muted"></i>
                    <span>Filter FSL will appear only on searches with filters for Property type</span>
                </div>

                <!-- Tab Panes -->
                <div class="seller-fsl-content">
                    <!-- 1. Locality FSL Content Pane -->
                    <div id="paneLocalityFsl" class="p-3">
                        <?php if (empty($localityFslData)): ?>
                            <div class="py-5 text-center text-muted" style="min-height: 180px; display: flex; align-items: center; justify-content: center;">
                                <span><i class="bi bi-bell me-2"></i>You have no active subscriptions</span>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="seller-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 50%;">Pack / Criteria</th>
                                            <th style="width: 15%;">Status</th>
                                            <th style="width: 20%;">Date</th>
                                            <th style="width: 15%;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($localityFslData as $slot): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($slot['criteria'] ?? ''); ?></td>
                                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($slot['status'] ?? ''); ?></span></td>
                                                <td><?php echo htmlspecialchars($slot['dates'] ?? ''); ?></td>
                                                <td><a href="/manage-responses.php" class="seller-table-action-link">View Responses</a></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 2. Filter FSL Content Pane -->
                    <div id="paneFilterFsl" class="p-3" style="display: none;">
                        <?php if (empty($filterFslData)): ?>
                            <div class="py-5 text-center text-muted" style="min-height: 180px; display: flex; align-items: center; justify-content: center;">
                                <span><i class="bi bi-bell me-2"></i>You have no active subscriptions</span>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="seller-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 50%;">Pack / Criteria</th>
                                            <th style="width: 15%;">Status</th>
                                            <th style="width: 20%;">Date</th>
                                            <th style="width: 15%;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($filterFslData as $slot): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($slot['criteria'] ?? ''); ?></td>
                                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($slot['status'] ?? ''); ?></span></td>
                                                <td><?php echo htmlspecialchars($slot['dates'] ?? ''); ?></td>
                                                <td><a href="/manage-responses.php" class="seller-table-action-link">View Responses</a></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Bottom Support & Queries Box -->
            <?php include __DIR__ . '/includes/seller-contact-footer.php'; ?>
        </div>
    </main>
</div>

<!-- FSL Photo Requirement Eligibility Modal (Matching Screenshot) -->
<div class="modal fade" id="fslEligibilityModal" tabindex="-1" aria-labelledby="fslEligibilityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 580px;">
        <div class="modal-content border-0 shadow-lg position-relative" style="border-radius: 12px; padding: 2rem 2.25rem 2rem;">
            <!-- Close Button -->
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>

            <!-- Illustration -->
            <div class="text-center mb-3">
                <svg width="200" height="100" viewBox="0 0 200 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Soft Warm Blob -->
                    <path d="M35 45C25 15 55 5 100 8C145 11 175 22 170 58C165 90 125 95 88 90C50 85 45 70 35 45Z" fill="#FFF3D6" opacity="0.85"/>
                    
                    <!-- Back Card (Featured Listing) -->
                    <rect x="70" y="20" width="95" height="50" rx="6" fill="#FFFFFF" stroke="#E2E8F0" stroke-width="1.5"/>
                    <rect x="78" y="28" width="30" height="10" rx="3" fill="#FFE4E6"/>
                    <text x="93" y="36" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="6.5" font-weight="700" fill="#E11D48" text-anchor="middle">Featured</text>
                    <rect x="114" y="30" width="42" height="3" rx="1.5" fill="#E2E8F0"/>
                    <rect x="78" y="44" width="78" height="2" fill="#F1F5F9"/>
                    <rect x="78" y="50" width="55" height="2" fill="#F1F5F9"/>

                    <!-- Front Card (Photo Card with Warning) -->
                    <rect x="52" y="28" width="68" height="46" rx="6" fill="#FFFFFF" stroke="#CBD5E1" stroke-width="1.5" filter="drop-shadow(0px 4px 6px rgba(0,0,0,0.06))"/>
                    <!-- Photo preview -->
                    <rect x="58" y="34" width="36" height="26" rx="3" fill="#EFF6FF"/>
                    <path d="M62 55L69 46L74 52L79 44L87 55H62Z" fill="#3B82F6" opacity="0.65"/>
                    <circle cx="68" cy="40" r="2.5" fill="#3B82F6" opacity="0.8"/>
                    
                    <!-- Alert Badge with Megaphone/Exclamation -->
                    <circle cx="88" cy="50" r="7.5" fill="#EF4444"/>
                    <text x="88" y="54" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="10" font-weight="900" fill="#FFFFFF" text-anchor="middle">!</text>
                    
                    <!-- Watermark -->
                    <path d="M64 68L68 64L72 68V72H64V68Z" fill="#94A3B8" opacity="0.3"/>
                </svg>
            </div>

            <!-- Title & Subtitle -->
            <h5 class="text-center fw-bold text-dark mb-2" id="fslEligibilityModalLabel">
                <i class="bi bi-megaphone-fill text-dark me-1" style="font-size: 1.1rem;"></i> FSL Photo Requirement
            </h5>
            <p class="text-center text-muted small px-2 mb-3" style="line-height: 1.45; font-size: 0.82rem;">
                For higher leads and quick selling, we are allowing only listing with minimum number of photos to be activated for FSL Booking
            </p>

            <!-- Requirements Table Box (Matching Screenshot) -->
            <div class="mb-3" style="border: 1px solid #d8e2ec; border-radius: 6px; overflow: hidden;">
                <table class="table mb-0" style="font-size: 0.84rem;">
                    <thead style="background: #f4f8fc;">
                        <tr>
                            <th class="py-2 px-3 text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; width: 70%; border-bottom: 1px solid #d8e2ec; letter-spacing: 0.5px;">PROPERTY TYPE</th>
                            <th class="py-2 px-3 text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; width: 30%; border-bottom: 1px solid #d8e2ec; letter-spacing: 0.5px;">MIN. PHOTOS REQUIRED</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid #edf2f7;">
                            <td class="py-2 px-3 fw-semibold text-dark">Lands / Plots, Properties Under Construction</td>
                            <td class="py-2 px-3 fw-bold text-dark">0</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #edf2f7;">
                            <td class="py-2 px-3 fw-semibold text-dark">1 BHK / 1 RK, Studio Apartment</td>
                            <td class="py-2 px-3 fw-bold text-dark">2</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 fw-semibold text-dark">Shops / Showrooms</td>
                            <td class="py-2 px-3 fw-bold text-dark">2</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Bottom Note -->
            <div class="text-muted" style="font-size: 0.78rem; line-height: 1.4;">
                <strong class="text-dark">Note:</strong> Users with live FSL Bookings without min. required photos are recommended to add photos to their listings to ensure higher responses
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnLocality = document.getElementById('btnLocalityFsl');
    const btnFilter = document.getElementById('btnFilterFsl');
    const paneLocality = document.getElementById('paneLocalityFsl');
    const paneFilter = document.getElementById('paneFilterFsl');
    const infoLocality = document.getElementById('infoLocalityFsl');
    const infoFilter = document.getElementById('infoFilterFsl');

    if (btnLocality && btnFilter) {
        function activateLocality() {
            btnLocality.classList.add('active');
            btnFilter.classList.remove('active');
            if (paneLocality) paneLocality.style.display = 'block';
            if (paneFilter) paneFilter.style.display = 'none';
            if (infoLocality) infoLocality.style.setProperty('display', 'flex', 'important');
            if (infoFilter) infoFilter.style.setProperty('display', 'none', 'important');
        }

        function activateFilter() {
            btnFilter.classList.add('active');
            btnLocality.classList.remove('active');
            if (paneFilter) paneFilter.style.display = 'block';
            if (paneLocality) paneLocality.style.display = 'none';
            if (infoFilter) infoFilter.style.setProperty('display', 'flex', 'important');
            if (infoLocality) infoLocality.style.setProperty('display', 'none', 'important');
        }

        btnLocality.addEventListener('click', activateLocality);
        btnFilter.addEventListener('click', activateFilter);

        // Ensure Locality FSL is explicitly visible on first page load
        activateLocality();
    }
});
</script>
</body>
</html>
