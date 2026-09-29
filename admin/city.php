<?php
require_once 'auth_check.php';

// Folder where city images get saved. Make sure this folder exists and is writable.
$upload_dir = __DIR__ . '/uploads/cities/';
if(!is_dir($upload_dir)){
    mkdir($upload_dir, 0755, true);
}

// Pull any flash message set by a previous redirect, then clear it
$msg = $_SESSION['flash_msg'] ?? "";
unset($_SESSION['flash_msg']);

// Insert new city
if(isset($_POST['save'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: city.php");
        exit;
    }

    $city_name = $_POST['city_name'];
    $order     = $_POST['city_order'];

    $image_name = "";

    if(isset($_FILES['city_image']) && $_FILES['city_image']['error'] === UPLOAD_ERR_OK){
        $uploaded = secure_upload('city_image', $upload_dir, 'city_', ADMIN_ALLOWED_IMAGE_MIMES, ADMIN_ALLOWED_IMAGE_EXTS);
        if($uploaded === false){
            set_flash('error', "Invalid image file. Allowed: jpg, jpeg, png, webp, gif.");
            header("Location: city.php");
            exit;
        }
        $image_name = $uploaded;
    }

    $stmt = $conn->prepare("INSERT INTO cities(city_name, city_image, city_order) VALUES(?,?,?)");
    $stmt->bind_param("ssi", $city_name, $image_name, $order);

    if($stmt->execute()){
        set_flash('success', "City Added Successfully");
    }else{
        set_flash('error', "Something went wrong. Please try again.");
    }
    $stmt->close();

    header("Location: city.php");
    exit;
}

// Update existing city (from edit modal)
if(isset($_POST['update'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: city.php");
        exit;
    }

    $id        = (int) $_POST['city_id'];
    $city_name = $_POST['city_name'];
    $order     = $_POST['city_order'];

    if(isset($_FILES['city_image']) && $_FILES['city_image']['error'] === UPLOAD_ERR_OK){

        $uploaded = secure_upload('city_image', $upload_dir, 'city_', ADMIN_ALLOWED_IMAGE_MIMES, ADMIN_ALLOWED_IMAGE_EXTS);
        if($uploaded === false){
            set_flash('error', "Invalid image file. Allowed: jpg, jpeg, png, webp, gif.");
            header("Location: city.php");
            exit;
        }
        $image_name = $uploaded;

        $old = $_POST['existing_image'];
        if($old && file_exists($upload_dir . $old)){
            unlink($upload_dir . $old);
        }

        $stmt = $conn->prepare("UPDATE cities SET city_name=?, city_image=?, city_order=? WHERE id=?");
        $stmt->bind_param("ssii", $city_name, $image_name, $order, $id);

    }else{
        $stmt = $conn->prepare("UPDATE cities SET city_name=?, city_order=? WHERE id=?");
        $stmt->bind_param("sii", $city_name, $order, $id);
    }

    if($stmt->execute()){
set_flash('success', "City Updated Successfully");
        }else{
            set_flash('error', "Something went wrong. Please try again.");
    }
    $stmt->close();

    header("Location: city.php");
    exit;
}

// Delete city
if(isset($_GET['delete'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: city.php");
        exit;
    }

    $id = (int) $_GET['delete'];

    $stmt = $conn->prepare("SELECT city_image FROM cities WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if($row && $row['city_image'] && file_exists($upload_dir . $row['city_image'])){
        unlink($upload_dir . $row['city_image']);
    }

    $stmt = $conn->prepare("DELETE FROM cities WHERE id=?");
    $stmt->bind_param("i", $id);

    if($stmt->execute()){
        set_flash('success', "City Deleted Successfully");
    }
    $stmt->close();

    header("Location: city.php");
    exit;
}

// Fetch total cities for pagination
$totalCities = 0;
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM cities");
if($stmt){
    $stmt->execute();
    $stmt->bind_result($totalCities);
    $stmt->fetch();
    $stmt->close();
}

$perPage = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$currentPage = $page;
$totalPages = max(1, (int) ceil($totalCities / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$cities = [];
$stmt = $conn->prepare("SELECT id, city_name, city_image, city_order, status FROM cities ORDER BY city_order ASC LIMIT ?, ?");
if($stmt){
    $stmt->bind_param("ii", $offset, $perPage);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()){
        $cities[] = $row;
    }
    $stmt->close();
}

$showFooter = true;

$pageTitle         = 'Cities';
$tableTitle        = 'All Cities';
$showAddButton     = true;
$addButtonText     = 'Add New City';
$addModalId        = 'addCityModal';
$saveButtonName    = 'save';
$saveButtonText    = 'Save City';
$showSearch        = true;
$searchPlaceholder = 'Type city name or order...';
$headers           = ['Sr No', 'Image', 'City Name', 'Order', 'Status', 'Action'];

$formFields = [
    [
        'name'        => 'city_name',
        'label'       => 'City Name',
        'type'        => 'text',
        'placeholder' => 'Enter city name',
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'city_image',
        'label'       => 'City Image (optional)',
        'type'        => 'file',
        'required'    => false
    ],
    [
        'name'        => 'city_order',
        'label'       => 'City Order',
        'type'        => 'number',
        'placeholder' => 'Enter city order',
        'value'       => '1',
        'required'    => true
    ]
];

$editModalId      = 'editCityModal';
$editButtonText   = 'Edit City';
$updateButtonName = 'update';
$updateButtonText = 'Update City';
$editFormFields   = [
    [
        'name'        => 'city_id',
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
        'name'        => 'city_name',
        'label'       => 'City Name',
        'type'        => 'text',
        'placeholder' => 'Enter city name',
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'city_image',
        'label'       => 'Replace Image (optional)',
        'type'        => 'file',
        'required'    => false
    ],
    [
        'name'        => 'city_order',
        'label'       => 'City Order',
        'type'        => 'number',
        'placeholder' => 'Enter city order',
        'value'       => '',
        'required'    => true
    ]
];

$rows = [];
if(count($cities) > 0){
    $i = 1;
    foreach($cities as $c){
        $actionButtons = '
            <button class="btn btn-action btn-action-edit edit-btn" type="button"
                data-id="'.htmlspecialchars($c['id'], ENT_QUOTES).'"
                data-city_id="'.htmlspecialchars($c['id'], ENT_QUOTES).'"
                data-city_name="'.htmlspecialchars($c['city_name'], ENT_QUOTES).'"
                data-city_order="'.htmlspecialchars($c['city_order'], ENT_QUOTES).'"
                data-existing_image="'.htmlspecialchars($c['city_image'], ENT_QUOTES).'"
                data-bs-toggle="modal"
                data-bs-target="#'.$editModalId.'">
                <i class="bi bi-pencil"></i>
            </button>
            <a class="btn btn-action btn-action-delete" href="city.php?delete='.$c['id'].'&csrf_token='.urlencode(csrf_token()).'" onclick="return confirm(\'Delete this city?\');">
                <i class="bi bi-trash"></i>
            </a>';

        $rows[] = [
            $i++,
            $c['city_image'] ? '<img src="uploads/cities/'.htmlspecialchars($c['city_image'], ENT_QUOTES).'" alt="city" style="height:50px; width:50px; object-fit:cover; border-radius:6px;">' : '',
            htmlspecialchars($c['city_name']),
            htmlspecialchars($c['city_order']),
            $c['status'] === 'Active' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>',
            $actionButtons
        ];
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
    <title>Add City</title>
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
