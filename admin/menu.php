<?php
require_once 'auth_check.php';

$msg = $_SESSION['flash_msg'] ?? "";
unset($_SESSION['flash_msg']);

// Turn "Contact Us" into "contact-us"; used as a fallback when no slug is typed
function slugify($text){
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

// Insert new menu
if(isset($_POST['save'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: menu.php");
        exit;
    }

    $menu  = trim($_POST['menu_name']);
    $order = (int) $_POST['menu_order'];
    $slug  = trim($_POST['menu_slug'] ?? '');

    // If left blank, auto-generate from the menu name
    if($slug === ''){
        $slug = slugify($menu);
    }

    $stmt=$conn->prepare("INSERT INTO menus(menu_name, menu_slug, menu_order) VALUES(?,?,?)");
    $stmt->bind_param("ssi",$menu,$slug,$order);

    if($stmt->execute()){
        set_flash('success', "Menu Added Successfully");
    }else{
        set_flash('error', "Something went wrong. Please try again.");
    }
    $stmt->close();

    header("Location: menu.php");
    exit;
}

// Update existing menu (from edit modal)
if(isset($_POST['update'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: menu.php");
        exit;
    }

    $id    = isset($_POST['menu_id']) ? (int) $_POST['menu_id'] : 0;
    $menu  = trim($_POST['menu_name'] ?? '');
    $order = isset($_POST['menu_order']) ? (int) $_POST['menu_order'] : 1;
    $slug  = trim($_POST['menu_slug'] ?? '');

    if($slug === ''){
        $slug = slugify($menu);
    }

    if($id > 0 && $menu !== ''){
        $stmt=$conn->prepare("UPDATE menus SET menu_name=?, menu_slug=?, menu_order=? WHERE id=?");
        $stmt->bind_param("ssii",$menu,$slug,$order,$id);

        if($stmt->execute()){
            set_flash('success', "Menu Updated Successfully");
        }else{
            set_flash('error', "Something went wrong. Please try again.");
        }
        $stmt->close();
    } else {
        set_flash('error', "Please provide a valid menu name and id.");
    }

    header("Location: menu.php");
    exit;
}

// Delete menu
if(isset($_GET['delete'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: menu.php");
        exit;
    }

    $id=(int)$_GET['delete'];

    $stmt=$conn->prepare("DELETE FROM menus WHERE id=?");
    $stmt->bind_param("i",$id);

    if($stmt->execute()){
        set_flash('success', "Menu Deleted Successfully");
    }
    $stmt->close();

    header("Location: menu.php");
    exit;
}

// Pagination setup for the menu list
$perPage = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$currentPage = $page;
$totalMenus = 0;
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM menus");
if($stmt){
    $stmt->execute();
    $stmt->bind_result($totalMenus);
    $stmt->fetch();
    $stmt->close();
}

$totalPages = max(1, (int) ceil($totalMenus / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$menus = [];
$stmt = $conn->prepare("SELECT id, menu_name, menu_slug, menu_order FROM menus ORDER BY menu_order ASC LIMIT ?, ?");
if($stmt){
    $stmt->bind_param("ii", $offset, $perPage);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()){
        $menus[] = $row;
    }
    $stmt->close();
}

function buildMenuPageUrl($pageNumber) {
    $params = $_GET;
    unset($params['page'], $params['delete']);
    $params['page'] = $pageNumber;
    return 'menu.php?' . http_build_query($params);
}

$paginationHtml = '<ul class="pagination pagination-sm mb-0 justify-content-center">';
if($page > 1) {
    $paginationHtml .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars(buildMenuPageUrl($page - 1)) . '">&lsaquo;</a></li>';
}
for($p = 1; $p <= $totalPages; $p++) {
    $active = $p === $page ? ' active' : '';
    $aria = $p === $page ? ' aria-current="page"' : '';
    $paginationHtml .= '<li class="page-item' . $active . '"><a class="page-link" href="' . htmlspecialchars(buildMenuPageUrl($p)) . '"' . $aria . '>' . $p . '</a></li>';
}
if($page < $totalPages) {
    $paginationHtml .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars(buildMenuPageUrl($page + 1)) . '">&rsaquo;</a></li>';
}
$paginationHtml .= '</ul>';

$tableInfo = sprintf(
    'Showing %d to %d of %d menus',
    $totalMenus === 0 ? 0 : $offset + 1,
    min($offset + $perPage, $totalMenus),
    $totalMenus
);

$showFooter = true;
$pageTitle         = 'Menus';
$tableTitle        = 'All Menus';
$showAddButton     = true;
$addButtonText     = 'Add New Menu';
$addModalId        = 'addMenuModal';
$saveButtonName    = 'save';
$saveButtonText    = 'Save Menu';
$showSearch        = true;
$searchPlaceholder = 'Type menu name / order...';
$headers           = ['Sr No', 'Menu Name', 'Menu URL', 'Menu Order', 'Action'];

$formFields = [
    [
        'name'        => 'menu_name',
        'label'       => 'Menu Name',
        'type'        => 'text',
        'placeholder' => 'Enter menu name',
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'menu_slug',
        'label'       => 'Menu URL / Slug',
        'type'        => 'text',
        'placeholder' => 'e.g. blog.php — leave blank to auto-generate',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'menu_order',
        'label'       => 'Menu Order',
        'type'        => 'number',
        'placeholder' => 'Enter menu order',
        'value'       => '1',
        'required'    => true
    ]
];

$editModalId      = 'editMenuModal';
$editButtonText   = 'Edit Menu';
$updateButtonName = 'update';
$updateButtonText = 'Update Menu';
$editFormFields   = [
    [
        'name'        => 'menu_id',
        'label'       => '',
        'type'        => 'hidden',
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'menu_name',
        'label'       => 'Menu Name',
        'type'        => 'text',
        'placeholder' => 'Enter menu name',
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'menu_slug',
        'label'       => 'Menu URL / Slug',
        'type'        => 'text',
        'placeholder' => 'e.g. blog.php — leave blank to auto-generate',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'menu_order',
        'label'       => 'Menu Order',
        'type'        => 'number',
        'placeholder' => 'Enter menu order',
        'value'       => '',
        'required'    => true
    ]
];

$rows = [];
if(count($menus) > 0) {
    $startNumber = (($page - 1) * $perPage) + 1;
    $i = $startNumber;
    foreach($menus as $m) {
        $actionButtons = '
            <button class="btn btn-action btn-action-edit edit-btn" type="button"
               data-id="'.htmlspecialchars($m['id'], ENT_QUOTES).'"
               data-menu_id="'.htmlspecialchars($m['id'], ENT_QUOTES).'"
               data-menu_name="'.htmlspecialchars($m['menu_name'], ENT_QUOTES).'"
               data-menu_slug="'.htmlspecialchars($m['menu_slug'], ENT_QUOTES).'"
               data-menu_order="'.htmlspecialchars($m['menu_order'], ENT_QUOTES).'"
               data-bs-toggle="modal"
               data-bs-target="#editMenuModal">
               <i class="bi bi-pencil"></i>
            </button>
            <a class="btn btn-action btn-action-delete" href="menu.php?delete='.$m['id'].'&csrf_token='.urlencode(csrf_token()).'" onclick="return confirm(\'Delete this menu?\');">
                <i class="bi bi-trash"></i>
            </a>';

        $rows[] = [
            $i++,
            htmlspecialchars($m['menu_name']),
            '<code>' . htmlspecialchars($m['menu_slug']) . '</code>',
            htmlspecialchars($m['menu_order']),
            $actionButtons
        ];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Menu</title>
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

    <script>
        // Auto-suggest a slug as the admin types the menu name, but never
        // overwrite it once they've typed into the slug field themselves.
        (function(){
            function slugify(text){
                return text.toLowerCase().trim()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }

            function wireUp(nameSelector, slugSelector){
                const nameInput = document.querySelector(nameSelector);
                const slugInput = document.querySelector(slugSelector);
                if(!nameInput || !slugInput) return;

                let slugTouched = slugInput.value.trim() !== '';

                slugInput.addEventListener('input', function(){
                    slugTouched = slugInput.value.trim() !== '';
                });

                nameInput.addEventListener('input', function(){
                    if(!slugTouched){
                        slugInput.value = slugify(nameInput.value);
                    }
                });
            }

            // Add Menu modal
            wireUp('#addMenuModal [name="menu_name"]', '#addMenuModal [name="menu_slug"]');
            // Edit Menu modal
            wireUp('#editMenuModal [name="menu_name"]', '#editMenuModal [name="menu_slug"]');
        })();
    </script>

</body>
</html>