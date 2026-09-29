<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit;
}
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'All Leads - Manage Responses';
$activeSellerMenu = 'all-leads';

// Dynamic leads placeholder for backend integration
$leadsList = [];
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
    <!-- Left Navigation Panel (All Leads Highlighted - Matching Screenshot) -->
    <?php include __DIR__ . '/includes/seller-sidebar.php'; ?>

    <!-- Main Content Area -->
    <main class="seller-main-content">
        <!-- Sub-Top Navigation Bar -->
        <?php include __DIR__ . '/includes/seller-subnav.php'; ?>

        <!-- Breadcrumb Bar (Matching Screenshot) -->
        <div class="seller-breadcrumb-bar">
            <ul class="seller-breadcrumb">
                <li><a href="/mytatito.php">Home</a></li>
                <li><span class="text-muted mx-1">/</span></li>
                <li class="active">All Leads</li>
            </ul>
        </div>

        <!-- Inner Content (Matching Screenshot) -->
        <div class="seller-body-container">
            <!-- Filter Bar (1-Line Horizontal Layout Matching Screenshot) -->
            <div class="seller-listings-filter-bar">
                <!-- 1. Duration -->
                <div class="seller-filter-section seller-filter-item-stacked">
                    <span class="seller-filter-item-label">DURATION</span>
                    <select class="seller-filter-select-borderless" id="durationFilter" aria-label="Duration">
                        <option value="6months" selected>All (Last 6 Months)</option>
                        <option value="30days">Last 30 Days</option>
                        <option value="1year">Last 1 Year</option>
                    </select>
                </div>

                <div class="seller-filter-vdivider"></div>

                <!-- 2. Search Projects -->
                <div class="seller-filter-section flex-grow-1" style="max-width: 320px;">
                    <div class="seller-search-box-borderless">
                        <i class="bi bi-search"></i>
                        <input type="text" class="seller-search-input-borderless" id="projectSearch" placeholder="Search Projects">
                    </div>
                </div>

                <div class="seller-filter-vdivider"></div>

                <!-- 3. Sort -->
                <div class="seller-filter-section seller-filter-item-stacked ms-auto">
                    <span class="seller-filter-item-label">SORT</span>
                    <select class="seller-filter-select-borderless" id="sortFilter" aria-label="Sort">
                        <option value="oldest">Oldest First</option>
                        <option value="highest_score">Highest Score First</option>
                        <option value="latest" selected>Latest First</option>
                    </select>
                </div>

                <div class="seller-filter-vdivider"></div>

                <!-- 4. Lead Score -->
                <div class="seller-filter-section seller-filter-item-stacked">
                    <span class="seller-filter-item-label">LEAD SCORE</span>
                    <select class="seller-filter-select-borderless" id="leadScoreFilter" aria-label="Lead Score">
                        <option value="all" selected>All</option>
                        <option value="high">High (&gt; 4.0)</option>
                        <option value="medium">Medium (3.0 - 4.0)</option>
                    </select>
                </div>
            </div>

            <!-- Dynamic Leads List or Empty State (Matching Screenshot) -->
            <?php if (empty($leadsList)): ?>
                <div class="seller-empty-state-box py-5">
                    <h3 class="seller-empty-state-title fs-4 fw-bold text-dark mb-2">Sorry, No Results !</h3>
                    <p class="seller-empty-state-text text-muted mb-5">We cannot find the item you are searching for.</p>
                    
                    <div class="position-relative mx-auto my-4" style="max-width: 620px;">
                        <!-- Background Skeleton Lines -->
                        <div class="d-flex flex-column gap-3 opacity-25">
                            <div class="bg-secondary rounded" style="height: 18px; width: 100%;"></div>
                            <div class="bg-secondary rounded" style="height: 18px; width: 100%;"></div>
                            <div class="bg-secondary rounded" style="height: 18px; width: 100%;"></div>
                            <div class="d-flex justify-content-between mt-2">
                                <div class="bg-secondary rounded" style="height: 18px; width: 30%;"></div>
                                <div class="bg-secondary rounded" style="height: 18px; width: 30%;"></div>
                            </div>
                        </div>

                        <!-- Magnifier Icon Overlay -->
                        <div class="position-absolute top-50 start-50 translate-middle">
                            <svg width="130" height="130" viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="58" cy="58" r="42" stroke="#b0b8c4" stroke-width="12" fill="#f8fafc" fill-opacity="0.95"/>
                                <text x="58" y="71" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="40" font-weight="700" fill="#9ca3af" text-anchor="middle">?</text>
                                <path d="M88 88L124 124" stroke="#b0b8c4" stroke-width="14" stroke-linecap="round"/>
                            </svg>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="vstack gap-2" id="leadsContainer">
                    <?php foreach ($leadsList as $lead): ?>
                        <div class="seller-lead-row-card">
                            <div class="seller-lead-identity">
                                <div class="seller-lead-identity-name">
                                    <?php echo htmlspecialchars($lead['name'] ?? ''); ?>
                                    <span class="text-muted fw-normal small">(<?php echo htmlspecialchars($lead['type'] ?? 'Individual'); ?>)</span>
                                </div>
                                <div class="seller-lead-identity-phone">
                                    <?php echo htmlspecialchars($lead['phone'] ?? ''); ?>
                                    <i class="bi bi-check-circle-fill seller-verified-check" title="Verified Mobile"></i>
                                </div>
                            </div>

                            <div class="seller-lead-rating-box">
                                <?php if (!empty($lead['rating'])): ?>
                                    <div class="seller-lead-stars-row">
                                        <span><?php echo htmlspecialchars($lead['rating']); ?></span>
                                        <span class="seller-lead-stars">
                                            <?php 
                                            $starsCount = (int)($lead['stars'] ?? 0);
                                            for ($i = 1; $i <= 5; $i++): 
                                            ?>
                                                <?php echo ($i <= $starsCount) ? '★' : '☆'; ?>
                                            <?php endfor; ?>
                                        </span>
                                        <a href="javascript:void(0)" class="seller-lead-behaviour-link">View Behaviour</a>
                                    </div>
                                <?php endif; ?>
                                <div class="seller-lead-timestamp">
                                    <?php echo htmlspecialchars($lead['activity'] ?? ''); ?>
                                </div>
                            </div>

                            <div class="seller-lead-location-box">
                                <div class="seller-lead-location-title">
                                    <?php echo htmlspecialchars($lead['project'] ?? ''); ?>
                                </div>
                            </div>

                            <div>
                                <button type="button" class="btn btn-sm btn-link text-muted p-0" aria-label="Expand Details">
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Bottom Support & Queries Box -->
            <?php include __DIR__ . '/includes/seller-contact-footer.php'; ?>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('projectSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase();
            const cards = document.querySelectorAll('.seller-lead-row-card');
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(query) ? 'flex' : 'none';
            });
        });
    }
});
</script>
</body>
</html>

