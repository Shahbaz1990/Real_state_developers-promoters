<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit;
}
require_once __DIR__ . '/includes/config.php';

$userId = (int) $_SESSION['user_id'];

// 1. Fetch user core record from backend (includes account_type and role)
$userRow = null;
$stmt = $conn->prepare("SELECT id, fullname, username AS email, mobile_number, role, account_type FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $userRow = $row;
}
$stmt->close();

// 2. Determine if user is a seller strictly based on account_type
$accountType = strtolower($userRow['account_type'] ?? $_SESSION['account_type'] ?? $userRow['role'] ?? $_SESSION['user_role'] ?? 'buyer');
$isSeller = ($accountType === 'seller' || in_array($accountType, ['builder', 'agent', 'developer']));

// 3. Set page context based on account_type
$pageTitle = 'Modify Profile';
if ($isSeller) {
    $pageTitle = 'Modify Profile - Seller Portal';
    $activeSellerMenu = 'modify-profile'; // Only set for seller pages
}

// 4. Build seller profile data array (used only when seller)
$sellerProfile = [
    'user_type'         => !empty($userRow['role']) ? ucfirst($userRow['role']) : (!empty($userRow['account_type']) ? ucfirst($userRow['account_type']) : 'Builder'),
    'fullname'          => $userRow['fullname'] ?? '',
    'email'             => $userRow['email'] ?? '',
    'company_name'      => $userRow['company_name'] ?? '',
    'mobile_number'     => $userRow['mobile_number'] ?? '',
    'allow_whatsapp'    => true,
    'phone_secondary_1' => '',
    'phone_secondary_2' => '',
    'company_url'       => '',
    'company_profile'   => '',
    'address'           => '',
    'landline_no'       => '',
    'subscribe_updates' => true,
];

// 5. Dynamic GST Details from backend
$gstDetails = [
    'state'   => $userRow['gst_state'] ?? '',
    'city'    => $userRow['gst_city'] ?? '',
    'address' => $userRow['gst_address'] ?? '',
    'gstin'   => $userRow['gstin_number'] ?? '',
];

$message = '';
$error = '';

// 6. Handle form POST (shared for both buyer & seller)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname        = trim($_POST['fullname'] ?? '');
    $mobile          = trim($_POST['mobile_number'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $companyName     = trim($_POST['company_name'] ?? '');
    $phoneSecondary1 = trim($_POST['phone_secondary_1'] ?? '');
    $phoneSecondary2 = trim($_POST['phone_secondary_2'] ?? '');
    $companyUrl      = trim($_POST['company_url'] ?? '');
    $companyProfile  = trim($_POST['company_profile'] ?? '');
    $address         = trim($_POST['address'] ?? '');
    $landlineNo      = trim($_POST['landline_no'] ?? '');

    if ($fullname === '') {
        $error = 'Please enter your name.';
    } else {
        $upd = $conn->prepare("UPDATE users SET fullname = ?, mobile_number = ? WHERE id = ?");
        $upd->bind_param("ssi", $fullname, $mobile, $userId);
        if ($upd->execute()) {
            $_SESSION['user_name'] = $fullname;
            $message = 'Profile saved successfully.';
            // Sync local arrays
            $userRow['fullname']       = $fullname;
            $userRow['mobile_number']  = $mobile;
            $sellerProfile['fullname']          = $fullname;
            $sellerProfile['email']             = $email;
            $sellerProfile['company_name']      = $companyName;
            $sellerProfile['mobile_number']     = $mobile;
            $sellerProfile['phone_secondary_1'] = $phoneSecondary1;
            $sellerProfile['phone_secondary_2'] = $phoneSecondary2;
            $sellerProfile['company_url']       = $companyUrl;
            $sellerProfile['company_profile']   = $companyProfile;
            $sellerProfile['address']           = $address;
            $sellerProfile['landline_no']       = $landlineNo;
        } else {
            $error = 'Could not save profile. Please try again.';
        }
        $upd->close();
    }
}
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
    <?php if ($isSeller): ?>
    <link rel="stylesheet" href="assets/css/seller-dashboard.css">
    <?php endif; ?>
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

<?php if ($isSeller): ?>
<!-- ========== SELLER MODIFY PROFILE LAYOUT ========== -->
<div class="seller-app-wrapper">
    <?php include __DIR__ . '/includes/seller-sidebar.php'; ?>

    <main class="seller-main-content">
        <?php include __DIR__ . '/includes/seller-subnav.php'; ?>

        <div class="seller-body-container">
            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <div><?php echo htmlspecialchars($message); ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div><?php echo htmlspecialchars($error); ?></div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Contact Details Notice Strip (Matching Screenshot) -->
            <div class="seller-contact-notice-bar">
                <div class="fw-semibold text-dark">Your Contact Details</div>
                <div class="text-muted small">(This is where people interested in your property will contact you)</div>
            </div>

            <div class="seller-profile-edit-card">
                <form method="POST" action="/modify-profile.php" class="seller-profile-form-grid" enctype="multipart/form-data">
                    <!-- 1. You are -->
                    <div class="seller-form-row">
                        <label class="seller-form-label">You are<span class="req">*</span></label>
                        <div class="seller-form-field">
                            <input type="text" class="seller-form-input" name="user_type" value="<?php echo htmlspecialchars($sellerProfile['user_type']); ?>" readonly style="background-color:#f8fafc;">
                        </div>
                    </div>
                    <!-- 2. Name -->
                    <div class="seller-form-row">
                        <label class="seller-form-label">Name<span class="req">*</span></label>
                        <div class="seller-form-field">
                            <input type="text" name="fullname" class="seller-form-input" required value="<?php echo htmlspecialchars($sellerProfile['fullname']); ?>">
                        </div>
                    </div>
                    <!-- 3. Email ID -->
                    <div class="seller-form-row">
                        <label class="seller-form-label">Email ID<span class="req">*</span></label>
                        <div class="seller-form-field">
                            <input type="email" name="email" class="seller-form-input" required value="<?php echo htmlspecialchars($sellerProfile['email']); ?>">
                        </div>
                    </div>
                    <!-- 4. Company Name -->
                    <div class="seller-form-row">
                        <label class="seller-form-label">Company Name</label>
                        <div class="seller-form-field">
                            <div class="d-flex align-items-center gap-2">
                                <input type="text" name="company_name" class="seller-form-input flex-grow-1" value="<?php echo htmlspecialchars($sellerProfile['company_name']); ?>">
                                <a href="javascript:void(0)" class="text-primary text-decoration-none small fw-bold text-nowrap">Change</a>
                            </div>
                        </div>
                    </div>
                    <!-- 5. Primary Phone Number -->
                    <div class="seller-form-row">
                        <label class="seller-form-label">Phone Number<span class="req">*</span></label>
                        <div class="seller-form-field">
                            <div class="seller-phone-input-group mb-2">
                                <select class="seller-phone-prefix" name="phone_prefix" aria-label="Country Code">
                                    <option value="+91" selected>+91 IND</option>
                                    <option value="+1">+1 USA</option>
                                    <option value="+971">+971 UAE</option>
                                </select>
                                <input type="tel" name="mobile_number" class="seller-phone-number" required value="<?php echo htmlspecialchars($sellerProfile['mobile_number']); ?>">
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="whatsappOpt" name="allow_whatsapp" value="1" <?php echo !empty($sellerProfile['allow_whatsapp']) ? 'checked' : ''; ?>>
                                <label class="form-check-label small text-primary fw-medium" for="whatsappOpt">
                                    Allow buyers to WhatsApp me on this number
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- 6. Secondary Phone 1 -->
                    <div class="seller-form-row">
                        <label class="seller-form-label">Phone Number</label>
                        <div class="seller-form-field">
                            <div class="seller-input-with-clear">
                                <div class="seller-phone-input-group flex-grow-1">
                                    <select class="seller-phone-prefix" aria-label="Country Code"><option value="+91">+91 IND</option></select>
                                    <input type="tel" name="phone_secondary_1" class="seller-phone-number" value="<?php echo htmlspecialchars($sellerProfile['phone_secondary_1']); ?>" placeholder="Phone Number">
                                </div>
                                <button type="button" class="seller-input-clear-btn" title="Remove" onclick="this.previousElementSibling.querySelector('input').value=''">&times;</button>
                            </div>
                        </div>
                    </div>
                    <!-- 7. Secondary Phone 2 -->
                    <div class="seller-form-row">
                        <label class="seller-form-label">Phone Number</label>
                        <div class="seller-form-field">
                            <div class="seller-phone-input-group">
                                <select class="seller-phone-prefix" aria-label="Country Code"><option value="+91">+91 IND</option></select>
                                <input type="tel" name="phone_secondary_2" class="seller-phone-number" value="<?php echo htmlspecialchars($sellerProfile['phone_secondary_2']); ?>" placeholder="Phone Number">
                            </div>
                        </div>
                    </div>
                    <!-- 8. Company URL -->
                    <div class="seller-form-row">
                        <label class="seller-form-label">Company URL</label>
                        <div class="seller-form-field">
                            <div class="seller-input-with-clear">
                                <input type="text" name="company_url" class="seller-form-input" value="<?php echo htmlspecialchars($sellerProfile['company_url']); ?>">
                                <button type="button" class="seller-input-clear-btn" title="Clear" onclick="this.previousElementSibling.value=''">&times;</button>
                            </div>
                        </div>
                    </div>
                    <!-- 9. Company Profile -->
                    <div class="seller-form-row">
                        <label class="seller-form-label">Company Profile<span class="req">*</span></label>
                        <div class="seller-form-field">
                            <textarea name="company_profile" class="seller-form-textarea" required><?php echo htmlspecialchars($sellerProfile['company_profile']); ?></textarea>
                        </div>
                    </div>
                    <!-- 10. Address -->
                    <div class="seller-form-row">
                        <label class="seller-form-label">Address</label>
                        <div class="seller-form-field">
                            <div class="seller-input-with-clear">
                                <input type="text" name="address" class="seller-form-input" value="<?php echo htmlspecialchars($sellerProfile['address']); ?>">
                                <button type="button" class="seller-input-clear-btn" title="Clear" onclick="this.previousElementSibling.value=''">&times;</button>
                            </div>
                        </div>
                    </div>
                    <!-- 11. Landline No -->
                    <div class="seller-form-row">
                        <label class="seller-form-label">Landline No</label>
                        <div class="seller-form-field">
                            <input type="text" name="landline_no" class="seller-form-input" value="<?php echo htmlspecialchars($sellerProfile['landline_no']); ?>" placeholder="Landline No">
                        </div>
                    </div>
                    <!-- 12. Upload Photos / Logos -->
                    <div class="seller-form-row">
                        <div class="seller-form-label"></div>
                        <div class="seller-form-field">
                            <div class="seller-upload-panel">
                                <label class="seller-upload-box" for="profilePhotoInput">
                                    <input type="file" id="profilePhotoInput" name="profile_photo" class="d-none" accept="image/*">
                                    <i class="bi bi-person-circle"></i>
                                    <span class="seller-upload-label">Upload profile photo</span>
                                </label>
                                <label class="seller-upload-box" for="companyLogoInput">
                                    <input type="file" id="companyLogoInput" name="company_logo" class="d-none" accept="image/*">
                                    <i class="bi bi-image"></i>
                                    <span class="seller-upload-label">Upload company logo (optional)</span>
                                </label>
                            </div>
                            <div class="text-muted small mt-2">Images should .jpg, .jpeg format &amp; less than 10 mb</div>
                        </div>
                    </div>
                    <!-- 13. GST Details (dynamic from backend) -->
                    <div class="seller-gst-section">
                        <h4 class="seller-gst-title">GST Details</h4>
                        <div class="row g-3 small text-dark mb-2">
                            <div class="col-md-6">
                                <div><strong>Current State:</strong> <?php echo htmlspecialchars($gstDetails['state']); ?></div>
                                <div class="mt-2"><strong>Current Address:</strong> <?php echo htmlspecialchars($gstDetails['address']); ?></div>
                            </div>
                            <div class="col-md-6">
                                <div><strong>Current City:</strong> <?php echo htmlspecialchars($gstDetails['city']); ?></div>
                                <div class="mt-2"><strong>GSTIN Number:</strong> <?php echo htmlspecialchars($gstDetails['gstin']); ?></div>
                            </div>
                        </div>
                        <div class="text-muted small mt-2">
                            <strong>Note:</strong> These details will be used to compute GST when you make a purchase. To edit GST related details contact <a href="mailto:gst.customer@tatito.in" class="text-primary text-decoration-none">gst.customer@tatito.in</a>.
                        </div>
                    </div>
                    <!-- 14. Subscribe -->
                    <div class="seller-subscribe-box">
                        <div class="fw-bold small text-dark mb-1">Subscribe For Updates From Tatito</div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="subscribeCheck" name="subscribe_updates" value="1" <?php echo !empty($sellerProfile['subscribe_updates']) ? 'checked' : ''; ?>>
                            <label class="form-check-label small text-muted" for="subscribeCheck">
                                I agree to be contacted by <strong>Tatito</strong> for similar properties or related services via WhatsApp, phone (overriding NDNC registration), sms, e-mail etc.
                            </label>
                        </div>
                    </div>
                    <!-- 15. Submit -->
                    <div class="text-center mt-3">
                        <div class="text-muted small mb-3">
                            By clicking below you agree to <a href="#" class="text-primary text-decoration-none">Terms and Conditions</a>
                        </div>
                        <button type="submit" class="seller-save-profile-btn">Save Profile</button>
                    </div>
                </form>
            </div>

            <?php include __DIR__ . '/includes/seller-contact-footer.php'; ?>
            <div class="small text-muted mt-3 mb-4">
                To delete your account, <a href="#" class="text-primary text-decoration-none fw-semibold">click here</a>
            </div>
        </div>
    </main>
</div>

<?php else: ?>
<!-- ========== BUYER MODIFY PROFILE LAYOUT ========== -->
<section class="page-shell">
    <div class="container" style="max-width: 640px;">
        <div class="act-card">
            <div class="card-body">
                <h4 class="fw-bold mb-4">Modify Profile</h4>

                <?php if ($message): ?>
                    <div class="alert alert-success d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle"></i>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="vstack gap-3">
                    <div>
                        <label class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="fullname" class="form-control" required value="<?php echo htmlspecialchars($userRow['fullname'] ?? ''); ?>">
                    </div>
                    <div>
                        <label class="form-label fw-semibold">Mobile Number</label>
                        <input type="tel" name="mobile_number" class="form-control" required maxlength="10" value="<?php echo htmlspecialchars($userRow['mobile_number'] ?? ''); ?>">
                    </div>
                    <div>
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($userRow['email'] ?? ''); ?>" disabled>
                    </div>
                    <button type="submit" class="btn btn-gold"><i class="bi bi-check2-circle me-1"></i> Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>