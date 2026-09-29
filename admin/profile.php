<?php
require_once __DIR__ . '/auth_check.php';

$id = $_SESSION["id"];

// Updated SQL query — matches actual users table columns.
// Sensitive columns (password, reset_token, reset_expires) are
// intentionally excluded from a profile-display query.
$stmt = $conn->prepare("SELECT id, fullname, username, mobile_number, profile_image, role, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Split full name for display in the dual inputs (First / Last)
$name_parts = explode(" ", $user["fullname"] ?? '', 2);
$first_name = $name_parts[0] ?? '';
$last_name = $name_parts[1] ?? '';

// Check existing uploaded image
$has_image = !empty($user["profile_image"]) && file_exists($user["profile_image"]);
$image_path = $has_image ? $user["profile_image"] : 'assets/img/sample.png';
$image_name = $has_image ? basename($user["profile_image"]) : 'No file selected';

// Role (read-only display)
$role = $user["role"] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>User Profile</title>
<link rel="stylesheet" href="css/adminlte.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
  body {
    background-color: #f4f6f9;
  }
  .profile-card {
    max-width: 650px;
    margin: 40px auto;
    border-radius: 4px;
    border: none;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.03);
  }
  .profile-card .card-header {
    background-color: #fff;
    border-bottom: 1px solid #eee;
    padding: 20px 30px;
    font-size: 1.25rem;
    font-weight: 600;
    color: #333;
  }
  .profile-card .card-body {
    padding: 30px;
  }
  .form-row {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1.5rem;
  }
  .form-row label {
    width: 140px;
    padding-top: 8px;
    font-weight: 500;
    color: #444;
    margin-bottom: 0;
  }
  .form-row .form-field {
    flex: 1;
  }
  .form-control {
    border: 1px solid #e1e5eb;
    border-radius: 4px;
    padding: 10px 14px;
    color: #495057;
  }
  .form-control:focus {
    border-color: #e1e5eb;
    box-shadow: none;
  }
  .input-password-active {
    background-color: #e8f0fe !important;
  }
  .role-badge {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 4px;
    background-color: #e8f0fe;
    color: #3b5bdb;
    font-weight: 500;
    font-size: 0.9rem;
    text-transform: capitalize;
  }
  /* Custom File Upload Styling */
  .file-upload-box {
    border: 1px solid #e1e5eb;
    border-radius: 4px;
    display: flex;
    align-items: center;
    background: #fff;
    overflow: hidden;
  }
  .file-upload-btn {
    background: #e9ecef;
    border: none;
    border-right: 1px solid #e1e5eb;
    padding: 10px 20px;
    color: #495057;
    cursor: pointer;
  }
  .file-upload-text {
    padding: 0 15px;
    color: #6c757d;
  }
  /* Image Thumbnail Preview */
  .image-preview-container {
    position: relative;
    display: <?php echo $has_image ? 'inline-block' : 'none'; ?>;
    margin-top: 15px;
    border: 1px solid #e1e5eb;
    border-radius: 6px;
    padding: 8px;
    width: 110px;
    text-align: center;
    background: #fff;
  }
  .image-preview-container img {
    width: 80px;
    height: 60px;
    object-fit: cover;
    border-radius: 4px;
  }
  .preview-filename {
    font-size: 11px;
    font-weight: bold;
    margin-top: 5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .preview-filesize {
    font-size: 10px;
    color: #888;
  }
  .btn-remove-img {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ffdede;
    color: #ff2e7e;
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    line-height: 18px;
    cursor: pointer;
  }
  .btn-save {
    background-color: #ff2e7e;
    border-color: #ff2e7e;
    color: #fff;
    padding: 10px 32px;
    font-weight: 500;
    border-radius: 4px;
    float: right;
  }
  .btn-save:hover {
    background-color: #e0266d;
    border-color: #e0266d;
    color: #fff;
  }
</style>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">
    <!-- Navbar -->
    <?php include 'nav.php'; ?>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main -->
    <main class="app-main">
        <div class="app-content">
            <div class="container-fluid">
                <div class="card profile-card">
                    <div class="card-header">
                        Profile
                    </div>
                    <div class="card-body">
                        <div id="profile-alert" class="d-none" role="alert"></div>
                        <form id="profile-form" action="profile_update.php" method="POST" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>

                            <!-- Name Row -->
                            <div class="form-row">
                                <label>Name</label>
                                <div class="form-field">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($first_name); ?>" placeholder="First Name">
                                        </div>
                                        <div class="col-6">
                                            <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($last_name); ?>" placeholder="Last Name">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mobile Number / Username Row -->
                            <div class="form-row">
                                <label>Mobile / User</label>
                                <div class="form-field">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="text" name="mobile_number" class="form-control" value="<?php echo htmlspecialchars($user["mobile_number"] ?? ''); ?>" placeholder="Mobile Number">
                                        </div>
                                        <div class="col-6">
                                            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user["username"] ?? ''); ?>" placeholder="Username" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Role Row (read-only) -->
                            <div class="form-row">
                                <label>Role</label>
                                <div class="form-field">
                                    <span class="role-badge"><?php echo htmlspecialchars($role ?: 'Not set'); ?></span>
                                </div>
                            </div>

                            <!-- New Password Row -->
                            <div class="form-row">
                                <label>New Password</label>
                                <div class="form-field">
                                    <input type="password" name="new_password" class="form-control input-password-active" placeholder="Enter new password">
                                </div>
                            </div>

                            <!-- Confirm Password Row -->
                            <div class="form-row">
                                <label>Confirm password</label>
                                <div class="form-field">
                                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password">
                                </div>
                            </div>

                            <!-- Image Row -->
                            <div class="form-row">
                                <label>Image <small class="text-muted">(90x90)</small></label>
                                <div class="form-field">
                                    <div class="file-upload-box">
                                        <button type="button" class="file-upload-btn" onclick="document.getElementById('profile_image').click();">Browse</button>
                                        <span class="file-upload-text" id="file-chosen"><?php echo $has_image ? '1 File selected' : 'No file selected'; ?></span>
                                        <input type="file" id="profile_image" name="profile_image" accept="image/*" style="display:none;" onchange="previewImage(event)">
                                        <input type="hidden" name="remove_image" id="remove_image" value="0">
                                    </div>

                                    <!-- Selected Image Preview -->
                                    <div class="image-preview-container" id="preview-box">
                                        <button type="button" class="btn-remove-img" onclick="removeImage()">&times;</button>
                                        <img id="image-preview" src="<?php echo htmlspecialchars($image_path); ?>" alt="Preview" onerror="this.src='https://via.placeholder.com/80x60?text=Image';">
                                        <div class="preview-filename" id="preview-filename"><?php echo htmlspecialchars($image_name); ?></div>
                                        <div class="preview-filesize" id="preview-filesize">Uploaded</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="clearfix mt-4">
                                <button type="submit" class="btn btn-save" id="save-btn">
                                    <span id="save-btn-text">Save</span>
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . '/includes/ui_footer.php'; ?>
<script>
function previewImage(event) {
    const input = event.target;
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();

        document.getElementById('remove_image').value = "0";
        document.getElementById('file-chosen').innerText = '1 File selected';
        document.getElementById('preview-filename').innerText = file.name;
        document.getElementById('preview-filesize').innerText = (file.size / 1024).toFixed(0) + ' KB';

        reader.onload = function(e) {
            document.getElementById('image-preview').src = e.target.result;
            document.getElementById('preview-box').style.display = 'inline-block';
        }

        reader.readAsDataURL(file);
    }
}

function removeImage() {
    document.getElementById('profile_image').value = "";
    document.getElementById('remove_image').value = "1";
    document.getElementById('file-chosen').innerText = 'No file selected';
    document.getElementById('preview-box').style.display = 'none';
}

// Submit the form via AJAX so we stay on profile.php instead of
// navigating to/reloading via profile_update.php.
document.getElementById('profile-form').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const saveBtn = document.getElementById('save-btn');
    let saveBtnText = document.getElementById('save-btn-text');
    const originalLabel = saveBtnText.innerText;

    function showToast(type, message) {
        if (!window.Toastify) return;
        const bg = type === 'error'
            ? 'linear-gradient(135deg, #dc2626, #ef4444)'
            : 'linear-gradient(135deg, #059669, #10b981)';
        const icon = type === 'error' ? '⛔  ' : '✅  ';
        Toastify({
            text: icon + (message || (type === 'error' ? 'Something went wrong. Please try again.' : 'Profile updated successfully.')),
            duration: 4000,
            gravity: 'top',
            position: 'right',
            close: true,
            stopOnFocus: true,
            style: { background: bg }
        }).showToast();
    }

    function setSaving(on) {
        saveBtn.disabled = on;
        if (on) {
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving…';
        } else {
            saveBtn.innerHTML = '<span id="save-btn-text">' + originalLabel + '</span>';
            saveBtnText = saveBtn.querySelector('#save-btn-text');
        }
    }

    setSaving(true);

    const formData = new FormData(form);

    fetch('profile_update.php', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function (res) {
        return res.json();
    })
    .then(function (data) {
        setSaving(false);

        if (data.success) {
            showToast('success', data.message);

            // Update the image preview in place, if a new image came back
            if (data.image_path) {
                document.getElementById('image-preview').src = data.image_path + '?t=' + Date.now();
                document.getElementById('preview-box').style.display = 'inline-block';
                document.getElementById('file-chosen').innerText = '1 File selected';
                if (data.image_name) {
                    document.getElementById('preview-filename').innerText = data.image_name;
                }
                document.getElementById('preview-filesize').innerText = 'Uploaded';
            }

            // Clear password fields after a successful save
            form.querySelector('[name="new_password"]').value = '';
            form.querySelector('[name="confirm_password"]').value = '';
            document.getElementById('remove_image').value = '0';
        } else {
            showToast('error', data.message);
        }
    })
    .catch(function () {
        setSaving(false);
        showToast('error', 'Network error — please try again.');
    });
});
</script>
</body>
</html>