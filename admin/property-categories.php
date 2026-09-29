<?php
require_once 'auth_check.php';

$msg = $_SESSION['flash_msg'] ?? "";
unset($_SESSION['flash_msg']);

// Turn "Independent House" into "independent-house"
function slugify($text){
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

// Make sure the slug is unique in property_categories (appends -2, -3, ... if needed).
// $ignoreId lets an update skip comparing against its own current row.
function uniqueCategorySlug($conn, $base, $ignoreId = 0){
    $slug = $base;
    $i = 2;
    while(true){
        $stmt = $conn->prepare("SELECT id FROM property_categories WHERE slug=? AND id!=? LIMIT 1");
        $stmt->bind_param("si", $slug, $ignoreId);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if(!$exists){
            return $slug;
        }
        $slug = $base . '-' . $i;
        $i++;
    }
}

// ===============================
// Fetch parent menus (Buy, Rent, Manage, Finance, Interiors, ...) for the
// "Parent Menu" dropdown on the Add/Edit category forms.
// ===============================
$menus = [];
$menuResult = $conn->query("SELECT id, menu_name FROM menus ORDER BY menu_order ASC, menu_name ASC");
if($menuResult){
    while($m = $menuResult->fetch_assoc()){
        $menus[] = $m;
    }
}
// id => name map, used to print the parent menu name in the table without re-querying per row
$menuNameById = [];
foreach($menus as $m){
    $menuNameById[$m['id']] = $m['menu_name'];
}

// Insert new category
if(isset($_POST['save'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: property-categories.php");
        exit;
    }

    $name  = trim($_POST['name'] ?? '');
    $menu_id = $_POST['menu_id'] !== '' ? (int) $_POST['menu_id'] : null;
    $order = isset($_POST['category_order']) ? (int) $_POST['category_order'] : 0;
    $status = ($_POST['status'] ?? 'Active') === 'Inactive' ? 'Inactive' : 'Active';

    $slugInput = isset($_POST['slug']) ? trim($_POST['slug']) : '';
    $baseSlug  = $slugInput !== '' ? slugify($slugInput) : slugify($name);
    $slug      = uniqueCategorySlug($conn, $baseSlug);

    $stmt = $conn->prepare("INSERT INTO property_categories(name, slug, category_order, status, menu_id) VALUES(?,?,?,?,?)");
    $stmt->bind_param("ssisi", $name, $slug, $order, $status, $menu_id);

    if($stmt->execute()){
        set_flash('success', "Category Added Successfully");
    }else{
        set_flash('error', "Something went wrong. Please try again.");
    }
    $stmt->close();

    header("Location: property-categories.php");
    exit;
}

// Update existing category (from edit modal)
if(isset($_POST['update'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: property-categories.php");
        exit;
    }

    $id     = isset($_POST['category_id']) ? (int) $_POST['category_id'] : 0;
    $name   = trim($_POST['name'] ?? '');
    $menu_id = $_POST['menu_id'] !== '' ? (int) $_POST['menu_id'] : null;
    $order  = isset($_POST['category_order']) ? (int) $_POST['category_order'] : 0;
    $status = ($_POST['status'] ?? 'Active') === 'Inactive' ? 'Inactive' : 'Active';

    if($id > 0 && $name !== ''){
        $slugInput = isset($_POST['slug']) ? trim($_POST['slug']) : '';
        $baseSlug  = $slugInput !== '' ? slugify($slugInput) : slugify($name);
        $slug      = uniqueCategorySlug($conn, $baseSlug, $id);

        $stmt = $conn->prepare("UPDATE property_categories SET name=?, slug=?, category_order=?, status=?, menu_id=? WHERE id=?");
        $stmt->bind_param("ssisii", $name, $slug, $order, $status, $menu_id, $id);

        if($stmt->execute()){
            set_flash('success', "Category Updated Successfully");
        }else{
            set_flash('error', "Something went wrong. Please try again.");
        }
        $stmt->close();
    } else {
        set_flash('error', "Please provide a valid category name and id.");
    }

    header("Location: property-categories.php");
    exit;
}

// Delete category
if(isset($_GET['delete'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: property-categories.php");
        exit;
    }

    $id = (int) $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM property_categories WHERE id=?");
    $stmt->bind_param("i", $id);

    if($stmt->execute()){
        set_flash('success', "Category Deleted Successfully");
    }else{
        // Most likely a foreign key block from trending_projects.category_id (ON DELETE SET NULL
        // means this normally won't happen, but keep a friendly message just in case).
        set_flash('error', "Could not delete this category. It may still be in use.");
    }
    $stmt->close();

    header("Location: property-categories.php");
    exit;
}

// Pagination setup for the category list
$perPage = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$currentPage = $page;
$totalCategories = 0;
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM property_categories");
if($stmt){
    $stmt->execute();
    $stmt->bind_result($totalCategories);
    $stmt->fetch();
    $stmt->close();
}

$totalPages = max(1, (int) ceil($totalCategories / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$categories = [];
$stmt = $conn->prepare("SELECT id, name, slug, category_order, status, menu_id FROM property_categories ORDER BY category_order ASC LIMIT ?, ?");
if($stmt){
    $stmt->bind_param("ii", $offset, $perPage);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()){
        $categories[] = $row;
    }
    $stmt->close();
}

function buildCategoryPageUrl($pageNumber) {
    $params = $_GET;
    unset($params['page'], $params['delete']);
    $params['page'] = $pageNumber;
    return 'property-categories.php?' . http_build_query($params);
}

$paginationHtml = '<ul class="pagination pagination-sm mb-0 justify-content-center">';
if($page > 1) {
    $paginationHtml .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars(buildCategoryPageUrl($page - 1)) . '">&lsaquo;</a></li>';
}
for($p = 1; $p <= $totalPages; $p++) {
    $active = $p === $page ? ' active' : '';
    $aria = $p === $page ? ' aria-current="page"' : '';
    $paginationHtml .= '<li class="page-item' . $active . '"><a class="page-link" href="' . htmlspecialchars(buildCategoryPageUrl($p)) . '"' . $aria . '>' . $p . '</a></li>';
}
if($page < $totalPages) {
    $paginationHtml .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars(buildCategoryPageUrl($page + 1)) . '">&rsaquo;</a></li>';
}
$paginationHtml .= '</ul>';

$tableInfo = sprintf(
    'Showing %d to %d of %d categories',
    $totalCategories === 0 ? 0 : $offset + 1,
    min($offset + $perPage, $totalCategories),
    $totalCategories
);

$showFooter = true;
$pageTitle         = 'Property Categories';
$tableTitle        = 'All Categories';
$showAddButton     = true;
$addButtonText     = 'Add New Category';
$addModalId        = 'addCategoryModal';
$saveButtonName    = 'save';
$saveButtonText    = 'Save Category';
$showSearch        = true;
$searchPlaceholder = 'Type category name / slug / order...';
$headers           = ['Sr No', 'Name', 'Parent Menu', 'Slug', 'Order', 'Status', 'Action'];

$formFields = [
    [
        'name'        => 'name',
        'label'       => 'Category Name',
        'type'        => 'text',
        'placeholder' => 'e.g. Apartment',
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'menu_id',
        'label'       => 'Parent Menu',
        'type'        => 'select',
        'placeholder' => 'Select a parent menu',
        'options'     => (function() use ($menus) {
            $opts = [];
            foreach($menus as $m){
                $opts[$m['id']] = $m['menu_name'];
            }
            return $opts;
        })(),
        'required'    => true
    ],
    [
        'name'        => 'slug',
        'label'       => 'URL Slug',
        'type'        => 'text',
        'placeholder' => 'Enter slug or leave blank to auto-generate',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'category_order',
        'label'       => 'Category Order',
        'type'        => 'number',
        'placeholder' => 'Enter category order',
        'value'       => '1',
        'required'    => true
    ],
    [
        'name'        => 'status',
        'label'       => 'Status',
        'type'        => 'select',
        'placeholder' => 'Select status',
        'options'     => [
            'Active'   => 'Active',
            'Inactive' => 'Inactive'
        ],
        'value'       => 'Active',
        'required'    => true
    ]
];

$editModalId      = 'editCategoryModal';
$editButtonText   = 'Edit Category';
$updateButtonName = 'update';
$updateButtonText = 'Update Category';
$editFormFields   = [
    [
        'name'        => 'category_id',
        'label'       => '',
        'type'        => 'hidden',
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'name',
        'label'       => 'Category Name',
        'type'        => 'text',
        'placeholder' => 'e.g. Apartment',
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'menu_id',
        'label'       => 'Parent Menu',
        'type'        => 'select',
        'placeholder' => 'Select a parent menu',
        'options'     => (function() use ($menus) {
            $opts = [];
            foreach($menus as $m){
                $opts[$m['id']] = $m['menu_name'];
            }
            return $opts;
        })(),
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'slug',
        'label'       => 'URL Slug',
        'type'        => 'text',
        'placeholder' => 'Enter slug or leave blank to auto-generate',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'category_order',
        'label'       => 'Category Order',
        'type'        => 'number',
        'placeholder' => 'Enter category order',
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'status',
        'label'       => 'Status',
        'type'        => 'select',
        'placeholder' => 'Select status',
        'options'     => [
            'Active'   => 'Active',
            'Inactive' => 'Inactive'
        ],
        'value'       => '',
        'required'    => true
    ]
];

$rows = [];
if(count($categories) > 0) {
    $startNumber = (($page - 1) * $perPage) + 1;
    $i = $startNumber;
    foreach($categories as $c) {
        $statusBadge = $c['status'] === 'Active'
            ? '<span class="badge rounded-pill" style="color:#1a7f4e;background:#e6f7ef;border:1px solid #b7ecd1;">Active</span>'
            : '<span class="badge rounded-pill" style="color:#8a1c1c;background:#fdecec;border:1px solid #f6c2c2;">Inactive</span>';

        $actionButtons = '
            <button class="btn btn-action btn-action-edit edit-btn" type="button"
               data-id="'.htmlspecialchars($c['id'], ENT_QUOTES).'"
               data-category_id="'.htmlspecialchars($c['id'], ENT_QUOTES).'"
               data-name="'.htmlspecialchars($c['name'], ENT_QUOTES).'"
               data-menu_id="'.htmlspecialchars($c['menu_id'] ?? '', ENT_QUOTES).'"
               data-slug="'.htmlspecialchars($c['slug'], ENT_QUOTES).'"
               data-category_order="'.htmlspecialchars($c['category_order'], ENT_QUOTES).'"
               data-status="'.htmlspecialchars($c['status'], ENT_QUOTES).'"
               data-bs-toggle="modal"
               data-bs-target="#editCategoryModal">
               <i class="bi bi-pencil"></i>
            </button>
            <a class="btn btn-action btn-action-delete" href="property-categories.php?delete='.$c['id'].'&csrf_token='.urlencode(csrf_token()).'" onclick="return confirm(\'Delete this category?\');">
                <i class="bi bi-trash"></i>
            </a>';

        $rows[] = [
            $i++,
            htmlspecialchars($c['name']),
            htmlspecialchars($menuNameById[$c['menu_id']] ?? '—', ENT_QUOTES),
            '<code>'.htmlspecialchars($c['slug']).'</code>',
            htmlspecialchars($c['category_order']),
            $statusBadge,
            $actionButtons
        ];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Property Categories</title>
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