<?php
require_once 'auth_check.php';

$msg = $_SESSION['flash_msg'] ?? "";
unset($_SESSION['flash_msg']);

// Directory where the About page banner image is stored
$uploadDir = __DIR__ . '/uploads/about/';
if(!is_dir($uploadDir)){
    mkdir($uploadDir, 0755, true);
}

// Handles the uploaded file and returns the stored filename, or null if none/invalid
function handleAboutImageUpload($fileInputName, $uploadDir){
    if(empty($_FILES[$fileInputName]['name']) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK){
        return null;
    }

    $allowed = ['jpg','jpeg','png','webp'];
    $ext = strtolower(pathinfo($_FILES[$fileInputName]['name'], PATHINFO_EXTENSION));

    if(!in_array($ext, $allowed)){
        return false; // invalid type
    }

    // Verify the real file type with a MIME check (blocks renamed executables)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($_FILES[$fileInputName]['tmp_name']);
    if(!in_array($mime, ADMIN_ALLOWED_IMAGE_MIMES, true)){
        return false;
    }

    $filename = uniqid('about_', true) . '.' . $ext;
    $destination = $uploadDir . $filename;

    if(move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $destination)){
        return $filename;
    }

    return false;
}

// ===============================
// Fetch the existing About Us row (there should only ever be one)
// ===============================
function getAboutRow($conn){
    $result = $conn->query("SELECT * FROM about_us ORDER BY id ASC LIMIT 1");
    if($result && $result->num_rows > 0){
        return $result->fetch_assoc();
    }
    return null;
}

// ===============================
// Save (insert if none exists yet, otherwise update — an upsert)
// ===============================
if(isset($_POST['save'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: about.php");
        exit;
    }

    $pageTitle   = trim($_POST['page_title'] ?? 'About Us');
    $tagline     = trim($_POST['tagline'] ?? '');
    $intro       = $_POST['intro_content'] ?? '';
    $mission     = $_POST['mission'] ?? '';
    $vision      = $_POST['vision'] ?? '';
    $years       = isset($_POST['years_experience']) && $_POST['years_experience'] !== '' ? (int) $_POST['years_experience'] : null;
    $projects    = isset($_POST['projects_completed']) && $_POST['projects_completed'] !== '' ? (int) $_POST['projects_completed'] : null;
    $cities      = isset($_POST['cities_covered']) && $_POST['cities_covered'] !== '' ? (int) $_POST['cities_covered'] : null;
    $customers   = isset($_POST['happy_customers']) && $_POST['happy_customers'] !== '' ? (int) $_POST['happy_customers'] : null;
    $status      = in_array($_POST['status'] ?? '', ['Active','Inactive']) ? $_POST['status'] : 'Active';
    $existingId  = isset($_POST['about_id']) ? (int) $_POST['about_id'] : 0;

    $newImage = handleAboutImageUpload('banner_image', $uploadDir);
    if($newImage === false){
        set_flash('error', "Invalid image file. Allowed: jpg, jpeg, png, webp.");
        header("Location: about.php");
        exit;
    }

    if($existingId > 0){
        // ---- Update existing row ----
        if($newImage !== null){
            $old = $conn->prepare("SELECT banner_image FROM about_us WHERE id=?");
            $old->bind_param("i", $existingId);
            $old->execute();
            $old->bind_result($oldImage);
            $old->fetch();
            $old->close();

            if(!empty($oldImage) && file_exists($uploadDir . $oldImage)){
                @unlink($uploadDir . $oldImage);
            }

            $stmt = $conn->prepare("UPDATE about_us SET page_title=?, tagline=?, banner_image=?, intro_content=?, mission=?, vision=?, years_experience=?, projects_completed=?, cities_covered=?, happy_customers=?, status=? WHERE id=?");
            $stmt->bind_param(
                "sssssssiiisi",
                $pageTitle, $tagline, $newImage, $intro, $mission, $vision,
                $years, $projects, $cities, $customers, $status, $existingId
            );
            // NOTE: bind_param type string above intentionally corrected below
        } else {
            $stmt = $conn->prepare("UPDATE about_us SET page_title=?, tagline=?, intro_content=?, mission=?, vision=?, years_experience=?, projects_completed=?, cities_covered=?, happy_customers=?, status=? WHERE id=?");
            $stmt->bind_param(
                "ssssssiiisi",
                $pageTitle, $tagline, $intro, $mission, $vision,
                $years, $projects, $cities, $customers, $status, $existingId
            );
        }

        if($stmt->execute()){
            set_flash('success', "About Us Page Updated Successfully");
        } else {
            set_flash('error', "Something went wrong. Please try again.");
        }
        $stmt->close();

    } else {
        // ---- No row exists yet — insert the first one ----
        $imageToSave = $newImage ?? '';

        $stmt = $conn->prepare("INSERT INTO about_us (page_title, tagline, banner_image, intro_content, mission, vision, years_experience, projects_completed, cities_covered, happy_customers, status) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param(
            "ssssssiiiis",
            $pageTitle, $tagline, $imageToSave, $intro, $mission, $vision,
            $years, $projects, $cities, $customers, $status
        );

        if($stmt->execute()){
            set_flash('success', "About Us Page Created Successfully");
        } else {
            set_flash('error', "Something went wrong. Please try again.");
        }
        $stmt->close();
    }

    header("Location: about.php");
    exit;
}

$about = getAboutRow($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>About Us</title>
    <link rel="stylesheet" href="css/adminlte.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        .about-form-card{
            background:#fff;
            border-radius:.6rem;
            padding:1.75rem;
            box-shadow:0 2px 10px rgba(0,0,0,.05);
        }
        .about-form-card h5{
            font-weight:700;
            margin-bottom:1.25rem;
            padding-bottom:.6rem;
            border-bottom:1px solid #eef0f4;
        }
        .about-current-image{
            width:140px;
            height:90px;
            object-fit:cover;
            border-radius:.4rem;
            border:1px solid #dee2e6;
            margin-bottom:.6rem;
        }
    </style>
</head>
<body>

    <div class="app-wrapper">
        <!-- Navbar -->
        <?php include 'nav.php'; ?>
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <main class="app-main">
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-12">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0">About Us Page</h4>
                            <?php if ($about): ?>
                                <span class="badge <?= $about['status'] === 'Active' ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= htmlspecialchars($about['status']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <form method="POST" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="about_id" value="<?= htmlspecialchars($about['id'] ?? '') ?>">

                            <div class="about-form-card mb-4">
                                <h5>Page Basics</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Page Title</label>
                                        <input type="text" name="page_title" class="form-control"
                                               value="<?= htmlspecialchars($about['page_title'] ?? 'About Us') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tagline</label>
                                        <input type="text" name="tagline" class="form-control"
                                               placeholder="e.g. India's property search, sorted."
                                               value="<?= htmlspecialchars($about['tagline'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Banner Image</label>
                                        <?php if (!empty($about['banner_image']) && file_exists($uploadDir . $about['banner_image'])): ?>
                                            <div>
                                                <img src="uploads/about/<?= htmlspecialchars($about['banner_image']) ?>" class="about-current-image">
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" name="banner_image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                                        <small class="text-muted">Leave blank to keep the current image.</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="Active" <?= (($about['status'] ?? 'Active') === 'Active') ? 'selected' : '' ?>>Active</option>
                                            <option value="Inactive" <?= (($about['status'] ?? '') === 'Inactive') ? 'selected' : '' ?>>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="about-form-card mb-4">
                                <h5>Content</h5>
                                <div class="mb-3">
                                    <label class="form-label">Introduction</label>
                                    <textarea name="intro_content" class="form-control" rows="4"
                                              placeholder="Main introduction paragraph shown on the About page"><?= htmlspecialchars($about['intro_content'] ?? '') ?></textarea>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Mission</label>
                                        <textarea name="mission" class="form-control" rows="4"><?= htmlspecialchars($about['mission'] ?? '') ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Vision</label>
                                        <textarea name="vision" class="form-control" rows="4"><?= htmlspecialchars($about['vision'] ?? '') ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="about-form-card mb-4">
                                <h5>Stats Band</h5>
                                <p class="text-muted small mb-3">These numbers can be shown as highlight stats on the About page (e.g. "12+ Years", "500+ Projects").</p>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Years of Experience</label>
                                        <input type="number" name="years_experience" class="form-control"
                                               value="<?= htmlspecialchars($about['years_experience'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Projects Completed</label>
                                        <input type="number" name="projects_completed" class="form-control"
                                               value="<?= htmlspecialchars($about['projects_completed'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Cities Covered</label>
                                        <input type="number" name="cities_covered" class="form-control"
                                               value="<?= htmlspecialchars($about['cities_covered'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Happy Customers</label>
                                        <input type="number" name="happy_customers" class="form-control"
                                               value="<?= htmlspecialchars($about['happy_customers'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" name="save" class="btn btn-primary px-4">
                                    <i class="bi bi-check-circle me-1"></i> Save Changes
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php include __DIR__ . '/includes/ui_footer.php'; ?>

</body>
</html>