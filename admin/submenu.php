<?php
require_once 'auth_check.php';

// Pull any flash message set by a previous redirect, then clear it
$msg = $_SESSION['flash_msg'] ?? "";
unset($_SESSION['flash_msg']);

// Insert new submenu
if(isset($_POST['save'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: submenu.php");
        exit;
    }

    $menu_id=(int)$_POST['menu_id'];
    $submenu=$_POST['submenu_name'];
    $link=$_POST['submenu_link'];
    $order=(int)$_POST['submenu_order'];

    $stmt=$conn->prepare("INSERT INTO submenus(menu_id,submenu_name,submenu_link,submenu_order) VALUES(?,?,?,?)");
    $stmt->bind_param("issi",$menu_id,$submenu,$link,$order);

    if($stmt->execute()){
        set_flash('success', "Submenu Added Successfully");
    }else{
        set_flash('error', "Something went wrong. Please try again.");
    }
    $stmt->close();

    header("Location: submenu.php");
    exit;
}

// Update existing submenu (from edit modal)
if(isset($_POST['update'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: submenu.php");
        exit;
    }

    $id=(int)$_POST['submenu_id'];
    $menu_id=(int)$_POST['menu_id'];
    $submenu=$_POST['submenu_name'];
    $link=$_POST['submenu_link'];
    $order=(int)$_POST['submenu_order'];

    $stmt=$conn->prepare("UPDATE submenus SET menu_id=?, submenu_name=?, submenu_link=?, submenu_order=? WHERE id=?");
    $stmt->bind_param("issii",$menu_id,$submenu,$link,$order,$id);

    if($stmt->execute()){
        set_flash('success', "Submenu Updated Successfully");
    }else{
        set_flash('error', "Something went wrong. Please try again.");
    }
    $stmt->close();

    header("Location: submenu.php");
    exit;
}

// Delete submenu
if(isset($_GET['delete'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: submenu.php");
        exit;
    }

    $id=(int)$_GET['delete'];

    $stmt=$conn->prepare("DELETE FROM submenus WHERE id=?");
    $stmt->bind_param("i",$id);

    if($stmt->execute()){
        set_flash('success', "Submenu Deleted Successfully");
    }
    $stmt->close();

    header("Location: submenu.php");
    exit;
}

// Fetch all menus (for the dropdown)
$menus=[];
$menuResult=$conn->query("SELECT id, menu_name FROM menus WHERE status='Active' ORDER BY menu_order ASC");
if($menuResult){
    while($row=$menuResult->fetch_assoc()){
        $menus[]=$row;
    }
}

// Fetch total submenus for pagination
$totalSubmenus = 0;
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM submenus");
if($stmt){
    $stmt->execute();
    $stmt->bind_result($totalSubmenus);
    $stmt->fetch();
    $stmt->close();
}

$perPage = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$currentPage = $page;
$totalPages = max(1, (int) ceil($totalSubmenus / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$submenus=[];
$subStmt = $conn->prepare(
    "SELECT s.id, s.submenu_name, s.submenu_link, s.submenu_order, s.menu_id, m.menu_name " .
    "FROM submenus s " .
    "INNER JOIN menus m ON s.menu_id = m.id " .
    "ORDER BY m.menu_order ASC, s.submenu_order ASC " .
    "LIMIT ?, ?"
);
if($subStmt){
    $subStmt->bind_param("ii", $offset, $perPage);
    $subStmt->execute();
    $result = $subStmt->get_result();
    while($row = $result->fetch_assoc()){
        $submenus[] = $row;
    }
    $subStmt->close();
}

$showFooter = true;

$pageTitle         = 'Submenus';
$tableTitle        = 'All Submenus';
$showAddButton     = count($menus) > 0;
$addButtonText     = 'Add New Submenu';
$addModalId        = 'addSubmenuModal';
$saveButtonName    = 'save';
$saveButtonText    = 'Save Submenu';
$showSearch        = true;
$searchPlaceholder = 'Type menu name, submenu or link...';
$headers           = ['Sr No', 'Parent Menu', 'Submenu Name', 'Link', 'Order', 'Action'];

$menuOptions = [];
foreach($menus as $menuItem){
    $menuOptions[$menuItem['id']] = $menuItem['menu_name'];
}

$formFields = [
    [
        'name'        => 'menu_id',
        'label'       => 'Parent Menu',
        'type'        => 'select',
        'options'     => $menuOptions,
        'placeholder' => '-- Select Menu --',
        'required'    => true
    ],
    [
        'name'        => 'submenu_name',
        'label'       => 'Submenu Name',
        'type'        => 'text',
        'placeholder' => 'Enter submenu name',
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'submenu_link',
        'label'       => 'Submenu Link',
        'type'        => 'text',
        'placeholder' => 'e.g. apartments.php',
        'value'       => ''
    ],
    [
        'name'        => 'submenu_order',
        'label'       => 'Submenu Order',
        'type'        => 'number',
        'placeholder' => 'Enter submenu order',
        'value'       => '1',
        'required'    => true
    ]
];

$editModalId      = 'editSubmenuModal';
$editButtonText   = 'Edit Submenu';
$updateButtonName = 'update';
$updateButtonText = 'Update Submenu';
$editFormFields   = [
    [
        'name'        => 'submenu_id',
        'label'       => '',
        'type'        => 'hidden',
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'menu_id',
        'label'       => 'Parent Menu',
        'type'        => 'select',
        'options'     => $menuOptions,
        'placeholder' => '-- Select Menu --',
        'required'    => true
    ],
    [
        'name'        => 'submenu_name',
        'label'       => 'Submenu Name',
        'type'        => 'text',
        'placeholder' => 'Enter submenu name',
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'submenu_link',
        'label'       => 'Submenu Link',
        'type'        => 'text',
        'placeholder' => 'e.g. apartments.php',
        'value'       => ''
    ],
    [
        'name'        => 'submenu_order',
        'label'       => 'Submenu Order',
        'type'        => 'number',
        'placeholder' => 'Enter submenu order',
        'value'       => '',
        'required'    => true
    ]
];

$rows = [];
if(count($submenus) > 0){
    $i = 1;
    foreach($submenus as $s){
        $actionButtons = '
            <button class="btn btn-action btn-action-edit edit-btn" type="button"
                data-id="'.htmlspecialchars($s['id'], ENT_QUOTES).'"
                data-menu_id="'.htmlspecialchars($s['menu_id'], ENT_QUOTES).'"
                data-submenu_id="'.htmlspecialchars($s['id'], ENT_QUOTES).'"
                data-submenu_name="'.htmlspecialchars($s['submenu_name'], ENT_QUOTES).'"
                data-submenu_link="'.htmlspecialchars($s['submenu_link'], ENT_QUOTES).'"
                data-submenu_order="'.htmlspecialchars($s['submenu_order'], ENT_QUOTES).'"
                data-bs-toggle="modal"
                data-bs-target="#'.$editModalId.'">
                <i class="bi bi-pencil"></i>
            </button>
            <a href="submenu.php?delete='.$s['id'].'&csrf_token='.urlencode(csrf_token()).'" class="btn btn-action btn-action-delete" onclick="return confirm(\'Delete this submenu?\');">
                <i class="bi bi-trash"></i>
            </a>';

        $rows[] = [
            $i++,
            htmlspecialchars($s['menu_name']),
            htmlspecialchars($s['submenu_name']),
            htmlspecialchars($s['submenu_link']),
            htmlspecialchars($s['submenu_order']),
            $actionButtons
        ];
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
    <title>Submenus</title>
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

                        <?php if(count($menus) == 0){ ?>
                            <div class="alert alert-warning mt-3">
                                Please add a Main Menu first before adding submenus.
                            </div>
                        <?php } ?>

                        <?php include __DIR__ . '/components/table/table.php'; ?>

                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php include __DIR__ . '/includes/ui_footer.php'; ?>
</body>
</html>