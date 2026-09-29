<?php
require_once 'auth_check.php';

// Folder where banner images get saved. Make sure this folder exists and is writable.
$upload_dir = __DIR__ . '/uploads/banners/';
if(!is_dir($upload_dir)){
    mkdir($upload_dir, 0755, true);
}

// Pull any flash message set by a previous redirect, then clear it
$msg = $_SESSION['flash_msg'] ?? "";
unset($_SESSION['flash_msg']);

// Insert new banner
if(isset($_POST['save'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: banner.php");
        exit;
    }

    $title    = $_POST['title'];
    $subtitle = $_POST['subtitle'];
    $order    = $_POST['banner_order'];

    $image_name = "";

    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK){
        $uploaded = secure_upload('image', $upload_dir, 'banner_', ADMIN_ALLOWED_IMAGE_MIMES, ADMIN_ALLOWED_IMAGE_EXTS);
        if($uploaded === false){
            set_flash('error', "Invalid image file. Allowed: jpg, jpeg, png, webp, gif.");
            header("Location: banner.php");
            exit;
        }
        $image_name = $uploaded;
    }

    $stmt = $conn->prepare("INSERT INTO banners(title, subtitle, image, banner_order) VALUES(?,?,?,?)");
    $stmt->bind_param("sssi", $title, $subtitle, $image_name, $order);

    if($stmt->execute()){
        set_flash('success', "Banner Added Successfully");
    }else{
        set_flash('error', "Something went wrong. Please try again.");
    }
    $stmt->close();

    header("Location: banner.php");
    exit;
}

// Update existing banner (from edit modal)
if(isset($_POST['update'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: banner.php");
        exit;
    }

    $id       = (int) $_POST['banner_id'];
    $title    = $_POST['title'];
    $subtitle = $_POST['subtitle'];
    $order    = $_POST['banner_order'];

    // If a new image was uploaded, replace it; otherwise keep the existing one.
    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK){

        $uploaded = secure_upload('image', $upload_dir, 'banner_', ADMIN_ALLOWED_IMAGE_MIMES, ADMIN_ALLOWED_IMAGE_EXTS);
        if($uploaded === false){
            set_flash('error', "Invalid image file. Allowed: jpg, jpeg, png, webp, gif.");
            header("Location: banner.php");
            exit;
        }
        $image_name = $uploaded;

        // Delete the old image file to avoid orphaned files piling up
        $old = $_POST['existing_image'];
        if($old && file_exists($upload_dir . $old)){
            unlink($upload_dir . $old);
        }

        $stmt = $conn->prepare("UPDATE banners SET title=?, subtitle=?, image=?, banner_order=? WHERE id=?");
        $stmt->bind_param("sssii", $title, $subtitle, $image_name, $order, $id);

    }else{
        $stmt = $conn->prepare("UPDATE banners SET title=?, subtitle=?, banner_order=? WHERE id=?");
        $stmt->bind_param("ssii", $title, $subtitle, $order, $id);
    }

    if($stmt->execute()){
        set_flash('success', "Banner Updated Successfully");
    }else{
        set_flash('error', "Something went wrong. Please try again.");
    }
    $stmt->close();

    header("Location: banner.php");
    exit;
}

// Delete banner
if(isset($_GET['delete'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: banner.php");
        exit;
    }

    $id = (int) $_GET['delete'];

    // Fetch the image filename first so we can remove the file too
    $stmt = $conn->prepare("SELECT image FROM banners WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if($row && $row['image'] && file_exists($upload_dir . $row['image'])){
        unlink($upload_dir . $row['image']);
    }

    $stmt = $conn->prepare("DELETE FROM banners WHERE id=?");
    $stmt->bind_param("i", $id);

    if($stmt->execute()){
        set_flash('success', "Banner Deleted Successfully");
    }
    $stmt->close();

    header("Location: banner.php");
    exit;
}

// Fetch total banners for pagination
$totalBanners = 0;
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM banners");
if($stmt){
    $stmt->execute();
    $stmt->bind_result($totalBanners);
    $stmt->fetch();
    $stmt->close();
}

$perPage = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$currentPage = $page;
$totalPages = max(1, (int) ceil($totalBanners / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$banners = [];
$stmt = $conn->prepare("SELECT id, title, subtitle, image, banner_order FROM banners ORDER BY banner_order ASC LIMIT ?, ?");
if($stmt){
    $stmt->bind_param("ii", $offset, $perPage);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()){
        $banners[] = $row;
    }
    $stmt->close();
}

$showFooter = true;

$pageTitle         = 'Homepage Banners';
$tableTitle        = 'All Banners';
$showAddButton     = true;
$addButtonText     = 'Add New Banner';
$addModalId        = 'addBannerModal';
$saveButtonName    = 'save';
$saveButtonText    = 'Save Banner';
$showSearch        = true;
$searchPlaceholder = 'Type title, subtitle or order...';
$headers           = ['Sr No', 'Image', 'Title', 'Subtitle', 'Order', 'Action'];

$formFields = [
    [
        'name'        => 'title',
        'label'       => 'Title',
        'type'        => 'text',
        'placeholder' => 'Enter banner title',
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'subtitle',
        'label'       => 'Subtitle',
        'type'        => 'text',
        'placeholder' => 'Enter subtitle',
        'value'       => ''
    ],
    [
        'name'        => 'image',
        'label'       => 'Banner Image',
        'type'        => 'file',
        'required'    => true
    ],
    [
        'name'        => 'banner_order',
        'label'       => 'Banner Order',
        'type'        => 'number',
        'placeholder' => 'Enter banner order',
        'value'       => '1',
        'required'    => true
    ]
];

$editModalId      = 'editBannerModal';
$editButtonText   = 'Edit Banner';
$updateButtonName = 'update';
$updateButtonText = 'Update Banner';
$editFormFields   = [
    [
        'name'        => 'banner_id',
        'label'       => '',
        'type'        => 'hidden',
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'existing_image',
        'label'       => '',
        'type'        => 'hidden',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'title',
        'label'       => 'Title',
        'type'        => 'text',
        'placeholder' => 'Enter banner title',
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'subtitle',
        'label'       => 'Subtitle',
        'type'        => 'text',
        'placeholder' => 'Enter subtitle',
        'value'       => ''
    ],
    [
        'name'        => 'banner_order',
        'label'       => 'Banner Order',
        'type'        => 'number',
        'placeholder' => 'Enter banner order',
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'image',
        'label'       => 'Replace Image',
        'type'        => 'file',
        'required'    => false
    ]
];

$rows = [];
if(count($banners) > 0){
    $i = 1;
    foreach($banners as $b){
        $actionButtons = '
            <div class="dropdown">
                <button class="btn btn-action btn-action-more dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <button class="dropdown-item edit-btn" type="button"
                            data-id="'.htmlspecialchars($b['id'], ENT_QUOTES).'"
                            data-banner_id="'.htmlspecialchars($b['id'], ENT_QUOTES).'"
                            data-title="'.htmlspecialchars($b['title'], ENT_QUOTES).'"
                            data-subtitle="'.htmlspecialchars($b['subtitle'], ENT_QUOTES).'"
                            data-banner_order="'.htmlspecialchars($b['banner_order'], ENT_QUOTES).'"
                            data-existing_image="'.htmlspecialchars($b['image'], ENT_QUOTES).'"
                            data-bs-toggle="modal"
                            data-bs-target="#'.$editModalId.'">
                            Edit
                        </button>
                    </li>
                    <li>
                        <a class="dropdown-item text-danger" href="banner.php?delete='.$b['id'].'&csrf_token='.urlencode(csrf_token()).'" onclick="return confirm(\'Delete this banner?\');">
                            Delete
                        </a>
                    </li>
                </ul>
            </div>';

        $rows[] = [
            $i++,
            $b['image'] ? '<img src="uploads/banners/'.htmlspecialchars($b['image'], ENT_QUOTES).'" alt="banner" style="height:60px; object-fit:cover;">' : '',
            htmlspecialchars($b['title']),
            htmlspecialchars($b['subtitle']),
            htmlspecialchars($b['banner_order']),
            $actionButtons
        ];
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
    <title>Homepage Banner</title>
    <link rel="stylesheet" href="css/adminlte.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
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

                        <?php include __DIR__ . '/components/table/table.php'; ?>

                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php include __DIR__ . '/includes/ui_footer.php'; ?>
</body>
</html>
