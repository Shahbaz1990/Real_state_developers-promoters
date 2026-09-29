<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit;
}
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Pending Payments - Manage Subscriptions';
$activeSellerMenu = 'manage-payments';

// Dynamic pending payments placeholder for backend integration
$pendingPayments = [];
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
            <!-- Page Title -->
            <div class="mb-4">
                <h1 class="fs-3 fw-bold text-dark mb-0">Pending Payments</h1>
            </div>

            <!-- Dynamic Pending Payments or Empty State -->
            <?php if (empty($pendingPayments)): ?>
                <div class="seller-no-payments-box">
                    <div class="seller-no-payments-graphic">
                        <svg viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Soft Circular Background -->
                            <circle cx="70" cy="70" r="58" fill="#eef0f3"/>
                            
                            <!-- Magnifying Glass Lens & Handle -->
                            <circle cx="62" cy="62" r="30" stroke="#7d8793" stroke-width="6" fill="#ffffff"/>
                            <path d="M84 84L108 108" stroke="#7d8793" stroke-width="9" stroke-linecap="round"/>
                            
                            <!-- Speech Bubble with Alert Tag -->
                            <rect x="74" y="24" width="34" height="28" rx="8" fill="#ffffff" stroke="#d0d7de" stroke-width="2"/>
                            <path d="M84 52L80 58L88 52H84Z" fill="#ffffff" stroke="#d0d7de" stroke-width="1.5"/>
                            
                            <!-- Red Cross Icon inside Speech Bubble -->
                            <path d="M85 33L97 43M97 33L85 43" stroke="#ea4335" stroke-width="3.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3 class="seller-no-payments-title">No Pending Payments</h3>
                </div>
            <?php else: ?>
                <div class="seller-card-box">
                    <div class="seller-table-wrap">
                        <table class="seller-table">
                            <thead>
                                <tr>
                                    <th>Order / Invoice ID</th>
                                    <th>Package / Service</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingPayments as $payment): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($payment['id'] ?? ''); ?></strong></td>
                                        <td><?php echo htmlspecialchars($payment['service'] ?? ''); ?></td>
                                        <td><strong>₹ <?php echo htmlspecialchars($payment['amount'] ?? ''); ?></strong></td>
                                        <td><?php echo htmlspecialchars($payment['dueDate'] ?? ''); ?></td>
                                        <td><span class="badge bg-warning text-dark">Pending</span></td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-primary">Pay Now</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Bottom Support & Queries Box -->
            <?php include __DIR__ . '/includes/seller-contact-footer.php'; ?>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

