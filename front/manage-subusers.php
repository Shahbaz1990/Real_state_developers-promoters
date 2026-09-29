<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit;
}
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Manage Sub users - Manage Subscriptions';
$activeSellerMenu = 'manage-subusers';

// Dynamic sub users placeholder for backend integration
$subUsersList = [];
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
            <!-- Manage Sub Users Box Container -->
            <div class="seller-subusers-box">
                <div class="seller-subusers-title">Manage Sub Users</div>

                <!-- Action Header Bar -->
                <div class="seller-subusers-top-bar">
                    <button type="button" class="seller-add-subuser-btn" data-bs-toggle="modal" data-bs-target="#addSubUserModal">
                        Add Sub User
                    </button>
                    <span class="text-muted opacity-50">|</span>
                    <span class="seller-subusers-note">
                        Note: Maximum 20 Sub Users can be active at a point of time
                    </span>
                </div>

                <!-- Table Container -->
                <div class="seller-subusers-table-wrap">
                    <table class="seller-subusers-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Date of Last Access</th>
                                <th>Post,Refresh &amp; Recall</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($subUsersList)): ?>
                                <!-- Empty state when 0 sub users are returned -->
                                <tr>
                                    <td colspan="6" class="p-0 border-0"></td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($subUsersList as $user): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($user['name'] ?? ''); ?></strong></td>
                                        <td><?php echo htmlspecialchars($user['username'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($user['last_access'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($user['permissions'] ?? 'Enabled'); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo (($user['status'] ?? '') === 'Active') ? 'success' : 'secondary'; ?>">
                                                <?php echo htmlspecialchars($user['status'] ?? 'Active'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="#" class="text-primary text-decoration-none me-2">Edit</a>
                                            <a href="#" class="text-danger text-decoration-none">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer Counter Bar -->
                <div class="seller-subusers-footer-bar">
                    Displaying 1 - <?php echo count($subUsersList) > 0 ? min(5, count($subUsersList)) : '5'; ?> of <?php echo count($subUsersList); ?> results
                </div>
            </div>

            <!-- Bottom Support & Queries Box (Matching Screenshot) -->
            <?php include __DIR__ . '/includes/seller-contact-footer.php'; ?>
        </div>
    </main>
</div>

<!-- Add Sub User Modal -->
<div class="modal fade" id="addSubUserModal" tabindex="-1" aria-labelledby="addSubUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="addSubUserModalLabel">Add New Sub User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/manage-subusers.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="subUserName" class="form-label small fw-bold">Full Name</label>
                        <input type="text" class="form-control form-control-sm" id="subUserName" name="name" required placeholder="e.g. Sales Agent Name">
                    </div>
                    <div class="mb-3">
                        <label for="subUserEmail" class="form-label small fw-bold">Username / Email</label>
                        <input type="email" class="form-control form-control-sm" id="subUserEmail" name="username" required placeholder="e.g. agent@company.com">
                    </div>
                    <div class="mb-3">
                        <label for="subUserPhone" class="form-label small fw-bold">Mobile Number</label>
                        <input type="tel" class="form-control form-control-sm" id="subUserPhone" name="phone" placeholder="e.g. 9876543210">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary">Create Sub User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

