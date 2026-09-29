<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit;
}
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'All Responses - Manage Responses';
$activeSellerMenu = 'all-responses';

// Dynamic responses placeholder for backend integration
$responsesList = [];
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
    <!-- Left Navigation Panel (All Responses Highlighted - Screenshot 4) -->
    <?php include __DIR__ . '/includes/seller-sidebar.php'; ?>

    <!-- Main Content Area -->
    <main class="seller-main-content">
        <!-- Sub-Top Navigation Bar -->
        <?php include __DIR__ . '/includes/seller-subnav.php'; ?>

        <!-- Breadcrumb Bar -->
        <div class="seller-breadcrumb-bar">
            <ul class="seller-breadcrumb">
                <li><a href="/mytatito.php">Home</a></li>
                <li><span class="text-muted mx-1">/</span></li>
                <li class="active">Manage Responses</li>
            </ul>
        </div>

        <!-- Inner Content -->
        <div class="seller-body-container">
            <!-- Filter Bar (All Filters in 1 Horizontal Line) -->
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

                <!-- 2. Locality Search -->
                <div class="seller-filter-section">
                    <div class="seller-search-box-borderless">
                        <i class="bi bi-search"></i>
                        <input type="text" class="seller-search-input-borderless" id="localitySearch" placeholder="Enter Locality">
                    </div>
                </div>

                <div class="seller-filter-vdivider"></div>

                <!-- 3. Projects Search -->
                <div class="seller-filter-section">
                    <div class="seller-search-box-borderless">
                        <i class="bi bi-search"></i>
                        <input type="text" class="seller-search-input-borderless" id="projectSearch" placeholder="Search Projects">
                    </div>
                </div>

                <div class="seller-filter-vdivider"></div>

                <!-- 4. Product -->
                <div class="seller-filter-section seller-filter-item-stacked">
                    <span class="seller-filter-item-label">PRODUCT</span>
                    <select class="seller-filter-select-borderless" id="productFilter" aria-label="Product">
                        <option value="all_listings" selected>All Listings</option>
                        <option value="basic_listings">Basic Listings</option>
                        <option value="plain_listings">Plain Listings</option>
                        <option value="platinum_listings">Platinum Listings</option>
                        <option value="premium_listings">Premium Listings</option>
                        <option value="infinity_listings">Infinity Listings</option>
                        <option value="ai_listings">AI Listings</option>
                        <option value="rental_expert">Rental Expert</option>
                    </select>
                </div>

                <div class="seller-filter-vdivider"></div>

                <!-- 5. More Filters -->
                <div class="seller-filter-section">
                    <button type="button" class="seller-more-filters-link-btn" id="moreFiltersBtn">More Filters</button>
                </div>

                <div class="seller-filter-vdivider"></div>

                <!-- 6. Sort -->
                <div class="seller-filter-section seller-filter-item-stacked">
                    <span class="seller-filter-item-label">SORT</span>
                    <select class="seller-filter-select-borderless" id="sortFilter" aria-label="Sort">
                        <option value="latest" selected>Latest First</option>
                        <option value="highest_score">Highest Score First</option>
                        <option value="oldest">Oldest First</option>
                    </select>
                </div>

                <div class="seller-filter-vdivider"></div>

                <!-- 7. Lead Score -->
                <div class="seller-filter-section seller-filter-item-stacked">
                    <span class="seller-filter-item-label">LEAD SCORE</span>
                    <select class="seller-filter-select-borderless" id="leadScoreFilter" aria-label="Lead Score">
                        <option value="all" selected>All</option>
                        <option value="high">High (&gt; 4.0)</option>
                        <option value="medium">Medium (3.0 - 4.0)</option>
                    </select>
                </div>
            </div>

            <!-- More Filters Flyout Panel for Responses (Matching Screenshot) -->
            <div class="seller-more-filters-panel" id="moreFiltersPanel">
                <button type="button" class="seller-more-filters-close-btn" id="closeMoreFiltersBtn" aria-label="Close">&times;</button>
                
                <div class="seller-response-filters-grid">
                    <!-- Column 1: Residential Property Types -->
                    <div>
                        <div class="seller-filter-col-title">Property Type</div>
                        <div class="seller-filter-checkbox-list">
                            <label class="seller-filter-check-item head-item">
                                <input type="checkbox" name="res_all" value="ALL RESIDENTIAL" id="allResRadio" class="res-all-cb">
                                <span>ALL RESIDENTIAL</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb res-cb" value="Residential Apartment">
                                <span>Residential Apartment</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb res-cb" value="Independent House/Villa">
                                <span>Independent House/Villa</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb res-cb" value="Residential Land/Plot">
                                <span>Residential Land/Plot</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb res-cb" value="Independent/Builder Floor">
                                <span>Independent/Builder Floor</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb res-cb" value="Farm House">
                                <span>Farm House</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb res-cb" value="Serviced Apartments">
                                <span>Serviced Apartments</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb res-cb" value="New Projects">
                                <span>New Projects</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb res-cb" value="Other">
                                <span>Other</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb res-cb" value="Studio Apartment">
                                <span>Studio Apartment</span>
                            </label>
                        </div>
                    </div>

                    <!-- Column 2: Commercial Property Types -->
                    <div>
                        <div class="seller-filter-col-title" style="visibility: hidden;">Commercial</div>
                        <div class="seller-filter-checkbox-list">
                            <label class="seller-filter-check-item head-item">
                                <input type="checkbox" name="comm_all" value="ALL COMMERCIAL" id="allCommRadio" class="comm-all-cb">
                                <span>ALL COMMERCIAL</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="Commercial Shops">
                                <span>Commercial Shops</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="Commercial Showrooms">
                                <span>Commercial Showrooms</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="Commercial Office/Space">
                                <span>Commercial Office/Space</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="Commercial Land/Inst. Land">
                                <span>Commercial Land/Inst. Land</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="Hotel/Resorts">
                                <span>Hotel/Resorts</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="Guest-House/Banquet-Halls">
                                <span>Guest-House/Banquet-Halls</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="Time Share">
                                <span>Time Share</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="Office in Business Park">
                                <span>Office in Business Park</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="Office in IT Park">
                                <span>Office in IT Park</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="WareHouse">
                                <span>WareHouse</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="Industrial Lands/Plots">
                                <span>Industrial Lands/Plots</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="Cold Storage">
                                <span>Cold Storage</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="Factory">
                                <span>Factory</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="Manufacturing">
                                <span>Manufacturing</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="Agricultural/Farm Land">
                                <span>Agricultural/Farm Land</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="Business center">
                                <span>Business center</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="Other">
                                <span>Other</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="Ready to move Office Space">
                                <span>Ready to move Office Space</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="Bare Shell Office Space">
                                <span>Bare Shell Office Space</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="Co-working Office Space">
                                <span>Co-working Office Space</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="Food Court">
                                <span>Food Court</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="SCO Plots">
                                <span>SCO Plots</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="Restaurant">
                                <span>Restaurant</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="Kiosk">
                                <span>Kiosk</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="prop-type-cb comm-cb" value="Multiplex">
                                <span>Multiplex</span>
                            </label>
                        </div>
                    </div>

                    <!-- Column 3: From & Follow Up Status -->
                    <div>
                        <!-- From Section -->
                        <div class="seller-filter-col-title">From</div>
                        <div class="seller-filter-checkbox-list mb-4" style="max-height: none;">
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="resp-filter-cb" value="Dealer">
                                <span>Dealer</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="resp-filter-cb" value="Individual">
                                <span>Individual</span>
                            </label>
                        </div>

                        <!-- Follow Up Status Section -->
                        <div class="seller-filter-col-title mt-4">Follow Up Status</div>
                        <div class="seller-filter-checkbox-list" style="max-height: none;">
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="resp-filter-cb" value="Follow Up">
                                <span>Follow Up</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="resp-filter-cb" value="Requirements Differ">
                                <span>Requirements Differ</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="resp-filter-cb" value="Site Visit">
                                <span>Site Visit</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="resp-filter-cb" value="Later">
                                <span>Later</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="resp-filter-cb" value="Invalid">
                                <span>Invalid</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="resp-filter-cb" value="New">
                                <span>New</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="resp-filter-cb" value="Closed">
                                <span>Closed</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="resp-filter-cb" value="Contacted">
                                <span>Contacted</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="resp-filter-cb" value="Not Interested">
                                <span>Not Interested</span>
                            </label>
                        </div>
                    </div>

                    <!-- Column 4: Category, Site Visits, Phone Verification Status & Actions -->
                    <div>
                        <!-- Category Section -->
                        <div class="seller-filter-col-title">Category</div>
                        <div class="seller-filter-checkbox-list mb-4" style="max-height: none;">
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="resp-filter-cb" value="Buy">
                                <span>Buy</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="resp-filter-cb" value="Rent">
                                <span>Rent</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="resp-filter-cb" value="Lease">
                                <span>Lease</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="resp-filter-cb" value="PG">
                                <span>PG</span>
                            </label>
                        </div>

                        <!-- Site Visits Checkbox -->
                        <div class="my-4">
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="resp-filter-cb" value="Site Visits Only">
                                <span>Show Responses with Site Visits only</span>
                            </label>
                        </div>

                        <!-- Phone Verification Status Section -->
                        <div class="seller-filter-col-title mt-4">Phone Verification Status</div>
                        <div class="seller-filter-checkbox-list mb-4" style="max-height: none;">
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="resp-filter-cb" value="Validated">
                                <span>Validated</span>
                            </label>
                            <label class="seller-filter-check-item">
                                <input type="checkbox" class="resp-filter-cb" value="Verified">
                                <span>Verified</span>
                            </label>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex align-items-center gap-3 mt-5 pt-3">
                            <button type="button" class="seller-clear-all-text-btn" id="modalClearAllBtn">Clear ALL</button>
                            <button type="button" class="seller-done-btn" id="modalDoneBtn">DONE</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Header Row with Response Counter & Download (Matching Screenshot 4) -->
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-3">
                    <a href="#" class="fw-bold text-primary text-decoration-none">All Responses (<?php echo count($responsesList); ?>)</a>
                    <span class="text-muted">|</span>
                    <a href="#" class="text-muted text-decoration-none">All Respondents (<?php echo count($responsesList); ?>)</a>
                </div>
                <div>
                    <button type="button" class="seller-download-btn" id="downloadBtn">
                        <i class="bi bi-download me-1"></i> DOWNLOAD RESPONSES
                    </button>
                </div>
            </div>

            <!-- Dynamic Leads List or Empty State -->
            <?php if (empty($responsesList)): ?>
                <div class="seller-empty-state-box">
                    <div class="seller-magnifier-graphic">
                        <svg viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="58" cy="58" r="42" stroke="#d0d7de" stroke-width="12" fill="#f6f8fa"/>
                            <circle cx="58" cy="58" r="36" fill="#eef2f6"/>
                            <text x="58" y="70" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="38" font-weight="700" fill="#9ca3af" text-anchor="middle">?</text>
                            <path d="M88 88L124 124" stroke="#d0d7de" stroke-width="14" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3 class="seller-empty-state-title">Sorry, No Results !</h3>
                    <p class="seller-empty-state-text">We cannot find the item you are searching for.</p>
                </div>
            <?php else: ?>
                <div class="vstack gap-2" id="leadsContainer">
                    <?php foreach ($responsesList as $lead): ?>
                        <div class="seller-lead-row-card">
                            <!-- Identity / Phone -->
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

                            <!-- Rating / Behavior -->
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

                            <!-- Project Location -->
                            <div class="seller-lead-location-box">
                                <div class="seller-lead-location-title">
                                    <?php echo htmlspecialchars($lead['project'] ?? ''); ?>
                                </div>
                            </div>

                            <!-- Dropdown Action Chevron -->
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
    // Project Search
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

    // More Filters Flyout Toggle
    const moreFiltersBtn = document.getElementById('moreFiltersBtn');
    const moreFiltersPanel = document.getElementById('moreFiltersPanel');
    const closeMoreFiltersBtn = document.getElementById('closeMoreFiltersBtn');
    const modalDoneBtn = document.getElementById('modalDoneBtn');
    const modalClearAllBtn = document.getElementById('modalClearAllBtn');

    if (moreFiltersBtn && moreFiltersPanel) {
        moreFiltersBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            moreFiltersPanel.classList.toggle('show');
        });
    }

    if (closeMoreFiltersBtn && moreFiltersPanel) {
        closeMoreFiltersBtn.addEventListener('click', function () {
            moreFiltersPanel.classList.remove('show');
        });
    }

    if (modalDoneBtn && moreFiltersPanel) {
        modalDoneBtn.addEventListener('click', function () {
            moreFiltersPanel.classList.remove('show');
        });
    }

    // Clear ALL checkboxes inside panel
    if (modalClearAllBtn && moreFiltersPanel) {
        modalClearAllBtn.addEventListener('click', function () {
            const checkboxes = moreFiltersPanel.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(cb => cb.checked = false);
        });
    }

    // ALL RESIDENTIAL select all / deselect all
    const allResCb = document.getElementById('allResRadio');
    if (allResCb && moreFiltersPanel) {
        allResCb.addEventListener('change', function () {
            const resCbs = moreFiltersPanel.querySelectorAll('.res-cb');
            resCbs.forEach(cb => cb.checked = allResCb.checked);
        });
    }

    // ALL COMMERCIAL select all / deselect all
    const allCommCb = document.getElementById('allCommRadio');
    if (allCommCb && moreFiltersPanel) {
        allCommCb.addEventListener('change', function () {
            const commCbs = moreFiltersPanel.querySelectorAll('.comm-cb');
            commCbs.forEach(cb => cb.checked = allCommCb.checked);
        });
    }
});
</script>
</body>
</html>
