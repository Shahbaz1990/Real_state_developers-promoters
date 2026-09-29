<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit;
}
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Manage Listings - Seller Portal';
$activeSellerMenu = 'all-listings';

$userId = $_SESSION['user_id'] ?? 0;
$userListings = [];
$statusFilter = $_GET['status'] ?? 'all';

// Query properties if database table exists
if ($conn) {
    $tableCheck = $conn->query("SHOW TABLES LIKE 'properties'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $stmt = $conn->prepare("SELECT * FROM properties WHERE user_id = ? ORDER BY created_at DESC");
        if ($stmt) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $userListings[] = $row;
            }
            $stmt->close();
        }
    }
}
$activeCount = count($userListings);
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

        <!-- Breadcrumb Bar -->
        <div class="seller-breadcrumb-bar">
            <ul class="seller-breadcrumb">
                <li><a href="/mytatito.php">Home</a></li>
                <li><span class="text-muted mx-1">/</span></li>
                <li class="active">Manage Listing</li>
            </ul>
        </div>

        <!-- Inner Content -->
        <div class="seller-body-container">
            <!-- Filter / Status Bar -->
            <!-- Filter / Status Bar (One Single Line Layout) -->
            <div class="seller-listings-filter-bar">
                <!-- Section 1: Activation Status (Label on top, Pills below) -->
                <div class="seller-filter-section">
                    <div class="seller-status-label">ACTIVATION STATUS</div>
                    <div class="seller-status-tabs" role="tablist">
                        <button type="button" class="seller-status-pill <?php echo ($statusFilter === 'all') ? 'active' : ''; ?>" data-status="all">ALL</button>
                        <span class="seller-status-divider">|</span>
                        <button type="button" class="seller-status-pill <?php echo ($statusFilter === 'active') ? 'active' : ''; ?>" data-status="active">Active</button>
                        <span class="seller-status-divider">|</span>
                        <button type="button" class="seller-status-pill <?php echo ($statusFilter === 'reported') ? 'active' : ''; ?>" data-status="reported">Reported</button>
                        <span class="seller-status-divider">|</span>
                        <button type="button" class="seller-status-pill <?php echo ($statusFilter === 'underscreening') ? 'active' : ''; ?>" data-status="underscreening">Underscreening</button>
                        <span class="seller-status-divider">|</span>
                        <button type="button" class="seller-status-pill <?php echo ($statusFilter === 'expired') ? 'active' : ''; ?>" data-status="expired">Expired</button>
                        <span class="seller-status-divider">|</span>
                        <button type="button" class="seller-status-pill <?php echo ($statusFilter === 'deleted') ? 'active' : ''; ?>" data-status="deleted">Deleted</button>
                    </div>
                </div>

                <div class="seller-filter-vdivider"></div>

                <!-- Section 2: Locality Search -->
                <div class="seller-filter-section">
                    <div class="seller-search-box-borderless">
                        <i class="bi bi-search"></i>
                        <input type="text" class="seller-search-input-borderless" id="localitySearch" placeholder="Enter Locality">
                    </div>
                </div>

                <div class="seller-filter-vdivider"></div>

                <!-- Section 3: Category (CATEGORY on top, Select dropdown below) -->
                <div class="seller-filter-section seller-filter-item-stacked">
                    <span class="seller-filter-item-label">CATEGORY</span>
                    <select class="seller-filter-select-borderless" id="categoryFilter" aria-label="Select Category">
                        <option value="" selected>Select</option>
                        <option value="PG">PG</option>
                        <option value="Rent">Rent</option>
                        <option value="Buy">Buy</option>
                        <option value="Lease">Lease</option>
                    </select>
                </div>

                <div class="seller-filter-vdivider"></div>

                <!-- Section 4: More Filters (Plain text link button, no icon) -->
                <div class="seller-filter-section">
                    <button type="button" class="seller-more-filters-link-btn" id="moreFiltersBtn">More Filters</button>
                </div>

                <div class="seller-filter-vdivider"></div>

                <!-- Section 5: Sort (SORT on top, Newest First dropdown below) -->
                <div class="seller-filter-section seller-filter-item-stacked">
                    <span class="seller-filter-item-label">SORT</span>
                    <select class="seller-filter-select-borderless" id="sortFilter" aria-label="Sort Listings">
                        <option value="newest" selected>Newest First</option>
                        <option value="expiring_last">Expiring Last</option>
                        <option value="oldest">Oldest First</option>
                        <option value="expiring_first">Expiring First</option>
                    </select>
                </div>
            </div>

            <!-- More Filters Flyout Panel -->
            <?php include __DIR__ . '/includes/seller-more-filters.php'; ?>

            <!-- Listings Header Row -->
            <div class="seller-listings-head">
                <div class="seller-listings-count">
                    <span id="productsCount"><?php echo $activeCount; ?></span> <span id="productsCountStatus"><?php echo ($statusFilter === 'all') ? '' : ucfirst($statusFilter); ?></span> Products
                </div>
                <div>
                    <a href="/properties.php" class="seller-premium-link">List of Premium New Launches</a>
                </div>
            </div>

            <!-- Applied Filter / Showing In Bar (Shows all active filter pills) -->
            <div class="seller-showing-in-bar" id="showingInBar" style="display: none;">
                <span class="seller-showing-in-label">Showing In:</span>
                <div class="seller-applied-pills-wrap d-inline-flex flex-wrap gap-2 align-items-center" id="appliedPillsContainer"></div>
                <a href="#" class="seller-clear-filters-link" id="clearAllFiltersBtn">Clear All Filters</a>
            </div>

            <!-- Listings Display / Empty State Box -->
            <?php if (empty($userListings)): ?>
                <div class="seller-empty-state-box" id="sellerEmptyState">
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
                <div class="vstack gap-3" id="sellerListingsList">
                    <?php foreach ($userListings as $prop): ?>
                        <div class="seller-lead-card">
                            <div class="seller-lead-header">
                                <div class="seller-lead-name">
                                    <i class="bi bi-building"></i>
                                    <?php echo htmlspecialchars($prop['title'] ?? 'Property Listing'); ?>
                                </div>
                                <span class="badge bg-success"><?php echo ucfirst($prop['status'] ?? 'Active'); ?></span>
                            </div>
                            <div class="seller-lead-details-grid">
                                <div>
                                    <div class="seller-lead-field-label">Price</div>
                                    <div class="seller-lead-field-value">₹ <?php echo htmlspecialchars($prop['price'] ?? 'Price on request'); ?></div>
                                </div>
                                <div>
                                    <div class="seller-lead-field-label">Location</div>
                                    <div class="seller-lead-field-value"><?php echo htmlspecialchars($prop['city'] ?? $prop['address'] ?? 'City'); ?></div>
                                </div>
                                <div>
                                    <div class="seller-lead-field-label">Category</div>
                                    <div class="seller-lead-field-value"><?php echo htmlspecialchars($prop['type'] ?? 'Residential'); ?></div>
                                </div>
                                <div>
                                    <div class="seller-lead-field-label">Posted Date</div>
                                    <div class="seller-lead-field-value"><?php echo htmlspecialchars(date('d M Y', strtotime($prop['created_at'] ?? 'now'))); ?></div>
                                </div>
                            </div>
                            <div class="seller-lead-actions">
                                <div class="small text-muted"><i class="bi bi-eye me-1"></i>0 Views &middot; <i class="bi bi-envelope me-1"></i>0 Responses</div>
                                <div class="seller-action-btn-group">
                                    <a href="/property-listing.php?id=<?php echo $prop['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                    <a href="https://tatitodevelopersandpromoters.com/backend/login.php" class="btn btn-sm btn-outline-secondary">Edit</a>
                                </div>
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
<script src="assets/js/seller-filters.js"></script>
</body>
</html>
