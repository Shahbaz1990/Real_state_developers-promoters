<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit;
}
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Omni Ad Summary - Manage Subscriptions';
$activeSellerMenu = 'omni-ads-performance';

// Dynamic Omni Ads placeholder for backend integration
$omniAdsList = [];
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
            <!-- Page Heading -->
            <div class="mb-4">
                <h1 class="fs-4 fw-normal text-dark mb-0">Omni Ad Summary</h1>
            </div>

            <!-- Omni Ad Table Container -->
            <div class="seller-omni-table-wrap">
                <table class="seller-omni-table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">S.NO</th>
                            <th style="width: 22%;">CAMPAIGN NAME</th>
                            <th style="width: 18%;">PROJECT NAME</th>
                            <th style="width: 12%;">START DATE</th>
                            <th style="width: 12%;">IMPRESSIONS</th>
                            <th style="width: 8%;">CLICKS</th>
                            <th style="width: 6%;">CTR</th>
                            <th style="width: 10%;">STATUS</th>
                            <th style="width: 7%; text-align: center;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($omniAdsList)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    No Omni Ad campaigns found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($omniAdsList as $index => $ad): ?>
                                <tr>
                                    <td><strong><?php echo ($index + 1) . '.'; ?></strong></td>
                                    <td><strong><?php echo htmlspecialchars($ad['campaign_name'] ?? ''); ?></strong></td>
                                    <td><?php echo htmlspecialchars($ad['project_name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($ad['start_date'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($ad['impressions'] ?? '0'); ?></td>
                                    <td><?php echo htmlspecialchars($ad['clicks'] ?? '0'); ?></td>
                                    <td><?php echo htmlspecialchars($ad['ctr'] ?? '0%'); ?></td>
                                    <td><?php echo htmlspecialchars(strtoupper($ad['status'] ?? 'COMPLETED')); ?></td>
                                    <td class="text-center">
                                        <button type="button" class="seller-omni-report-btn" data-bs-toggle="modal" data-bs-target="#omniReportModal" data-campaign="<?php echo htmlspecialchars($ad['campaign_name'] ?? ''); ?>">
                                            View<br>Report
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Footer Counter Text -->
            <div class="seller-omni-counter-text">
                Displaying <?php echo count($omniAdsList) > 0 ? ('1 - ' . count($omniAdsList)) : '0'; ?> of <?php echo count($omniAdsList); ?> results
            </div>

            <!-- Bottom Support & Queries Box -->
            <?php include __DIR__ . '/includes/seller-contact-footer.php'; ?>
        </div>
    </main>
</div>

<!-- View Report Modal -->
<div class="modal fade" id="omniReportModal" tabindex="-1" aria-labelledby="omniReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="omniReportModalLabel">Omni Ad Campaign Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info py-2 small mb-4">
                    Detailed multi-channel reach breakdown across Google Display, Meta Ads &amp; Network Portals.
                </div>
                <div class="row g-3 text-center">
                    <div class="col-md-3">
                        <div class="p-3 border rounded bg-light">
                            <div class="text-muted small">Total Impressions</div>
                            <div class="fs-5 fw-bold text-primary" id="modalImpressions">-</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 border rounded bg-light">
                            <div class="text-muted small">Total Clicks</div>
                            <div class="fs-5 fw-bold text-success" id="modalClicks">-</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 border rounded bg-light">
                            <div class="text-muted small">Click Through Rate</div>
                            <div class="fs-5 fw-bold text-dark" id="modalCtr">-</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 border rounded bg-light">
                            <div class="text-muted small">Campaign Status</div>
                            <div class="fs-5 fw-bold text-success" id="modalStatus">-</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-primary" onclick="window.print()">Download Report</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

