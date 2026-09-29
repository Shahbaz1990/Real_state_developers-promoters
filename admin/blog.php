<?php
require_once 'auth_check.php';

$msg = $_SESSION['flash_msg'] ?? "";
unset($_SESSION['flash_msg']);

function slugify($text){
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

// Directory where blog images are stored
$uploadDir = __DIR__ . '/uploads/blogs/';
if(!is_dir($uploadDir)){
    mkdir($uploadDir, 0755, true);
}

// Handles the uploaded file and returns the stored filename, or null if none/invalid
function handleBlogImageUpload($fileInputName, $uploadDir){
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

    $filename = uniqid('blog_', true) . '.' . $ext;
    $destination = $uploadDir . $filename;

    if(move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $destination)){
        return $filename;
    }

    return false;
}

// ===============================
// Insert new blog
// ===============================
if(isset($_POST['save'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: blog.php");
        exit;
    }

    $title    = trim($_POST['title']);
    $slug     = trim($_POST['slug'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $author   = trim($_POST['author'] ?? '');
    $excerpt  = trim($_POST['excerpt'] ?? '');
    $content  = $_POST['content'] ?? '';
    $status   = in_array($_POST['status'] ?? '', ['Active','Inactive']) ? $_POST['status'] : 'Active';
    $order    = isset($_POST['blog_order']) ? (int) $_POST['blog_order'] : 1;

    if($slug === ''){
        $slug = slugify($title);
    }

    $image = handleBlogImageUpload('image', $uploadDir);
    if($image === false){
        set_flash('error', "Invalid image file. Allowed: jpg, jpeg, png, webp.");
        header("Location: blog.php");
        exit;
    }
    $image = $image ?? '';

    $stmt = $conn->prepare("INSERT INTO blogs (title, slug, category, author, image, excerpt, content, status, blog_order) VALUES (?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssssi", $title, $slug, $category, $author, $image, $excerpt, $content, $status, $order);

    if($stmt->execute()){
        set_flash('success', "Blog Added Successfully");
    } else {
        set_flash('error', (strpos($stmt->error, 'uniq_slug') !== false)
            ? "That slug is already in use. Please choose another."
            : "Something went wrong. Please try again.");
    }
    $stmt->close();

    header("Location: blog.php");
    exit;
}

// ===============================
// Update existing blog
// ===============================
if(isset($_POST['update'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: blog.php");
        exit;
    }

    $id       = isset($_POST['blog_id']) ? (int) $_POST['blog_id'] : 0;
    $title    = trim($_POST['title'] ?? '');
    $slug     = trim($_POST['slug'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $author   = trim($_POST['author'] ?? '');
    $excerpt  = trim($_POST['excerpt'] ?? '');
    $content  = $_POST['content'] ?? '';
    $status   = in_array($_POST['status'] ?? '', ['Active','Inactive']) ? $_POST['status'] : 'Active';
    $order    = isset($_POST['blog_order']) ? (int) $_POST['blog_order'] : 1;

    if($slug === ''){
        $slug = slugify($title);
    }

    if($id > 0 && $title !== ''){

        $newImage = handleBlogImageUpload('image', $uploadDir);
        if($newImage === false){
            set_flash('error', "Invalid image file. Allowed: jpg, jpeg, png, webp.");
            header("Location: blog.php");
            exit;
        }

        if($newImage !== null){
            // A new file was uploaded — replace the old one
            $old = $conn->prepare("SELECT image FROM blogs WHERE id=?");
            $old->bind_param("i", $id);
            $old->execute();
            $old->bind_result($oldImage);
            $old->fetch();
            $old->close();

            if(!empty($oldImage) && file_exists($uploadDir . $oldImage)){
                @unlink($uploadDir . $oldImage);
            }

            $stmt = $conn->prepare("UPDATE blogs SET title=?, slug=?, category=?, author=?, image=?, excerpt=?, content=?, status=?, blog_order=? WHERE id=?");
            $stmt->bind_param("ssssssssii", $title, $slug, $category, $author, $newImage, $excerpt, $content, $status, $order, $id);
        } else {
            // No new file — keep the existing image
            $stmt = $conn->prepare("UPDATE blogs SET title=?, slug=?, category=?, author=?, excerpt=?, content=?, status=?, blog_order=? WHERE id=?");
            $stmt->bind_param("sssssssi", $title, $slug, $category, $author, $excerpt, $content, $status, $order, $id);
        }

        if($stmt->execute()){
            set_flash('success', "Blog Updated Successfully");
        } else {
            set_flash('error', (strpos($stmt->error, 'uniq_slug') !== false)
                ? "That slug is already in use. Please choose another."
                : "Something went wrong. Please try again.");
        }
        $stmt->close();
    } else {
        set_flash('error', "Please provide a valid title and id.");
    }

    header("Location: blog.php");
    exit;
}

// ===============================
// Delete blog
// ===============================
if(isset($_GET['delete'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: blog.php");
        exit;
    }

    $id = (int) $_GET['delete'];

    $imgStmt = $conn->prepare("SELECT image FROM blogs WHERE id=?");
    $imgStmt->bind_param("i", $id);
    $imgStmt->execute();
    $imgStmt->bind_result($imageToDelete);
    $imgStmt->fetch();
    $imgStmt->close();

    $stmt = $conn->prepare("DELETE FROM blogs WHERE id=?");
    $stmt->bind_param("i", $id);

    if($stmt->execute()){
        if(!empty($imageToDelete) && file_exists($uploadDir . $imageToDelete)){
            @unlink($uploadDir . $imageToDelete);
        }
        set_flash('success', "Blog Deleted Successfully");
    }
    $stmt->close();

    header("Location: blog.php");
    exit;
}

// ===============================
// Pagination setup
// ===============================
$perPage = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$totalBlogs = 0;
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM blogs");
if($stmt){
    $stmt->execute();
    $stmt->bind_result($totalBlogs);
    $stmt->fetch();
    $stmt->close();
}

$totalPages = max(1, (int) ceil($totalBlogs / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$blogs = [];
$stmt = $conn->prepare("SELECT id, title, slug, category, author, image, status, blog_order FROM blogs ORDER BY blog_order ASC, id DESC LIMIT ?, ?");
if($stmt){
    $stmt->bind_param("ii", $offset, $perPage);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()){
        $blogs[] = $row;
    }
    $stmt->close();
}

function buildBlogPageUrl($pageNumber) {
    $params = $_GET;
    unset($params['page'], $params['delete']);
    $params['page'] = $pageNumber;
    return 'blog.php?' . http_build_query($params);
}

$paginationHtml = '<ul class="pagination pagination-sm mb-0 justify-content-center">';
if($page > 1) {
    $paginationHtml .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars(buildBlogPageUrl($page - 1)) . '">&lsaquo;</a></li>';
}
for($p = 1; $p <= $totalPages; $p++) {
    $active = $p === $page ? ' active' : '';
    $aria = $p === $page ? ' aria-current="page"' : '';
    $paginationHtml .= '<li class="page-item' . $active . '"><a class="page-link" href="' . htmlspecialchars(buildBlogPageUrl($p)) . '"' . $aria . '>' . $p . '</a></li>';
}
if($page < $totalPages) {
    $paginationHtml .= '<li class="page-item"><a class="page-link" href="' . htmlspecialchars(buildBlogPageUrl($page + 1)) . '">&rsaquo;</a></li>';
}
$paginationHtml .= '</ul>';

$tableInfo = sprintf(
    'Showing %d to %d of %d blogs',
    $totalBlogs === 0 ? 0 : $offset + 1,
    min($offset + $perPage, $totalBlogs),
    $totalBlogs
);

$showFooter         = true;
$pageTitle          = 'Blogs';
$tableTitle         = 'All Blogs';
$showAddButton      = true;
$addButtonText      = 'Add New Blog';
$addModalId         = 'addBlogModal';
$saveButtonName     = 'save';
$saveButtonText     = 'Save Blog';
$showSearch         = true;
$searchPlaceholder  = 'Type blog title / category...';
$headers            = ['Sr No', 'Image', 'Title', 'Category', 'Status', 'Order', 'Action'];
$formEncType        = 'multipart/form-data'; // needed for file upload

$formFields = [
    [
        'name'        => 'title',
        'label'       => 'Blog Title',
        'type'        => 'text',
        'placeholder' => 'Enter blog title',
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'slug',
        'label'       => 'Blog URL / Slug',
        'type'        => 'text',
        'placeholder' => 'e.g. buying-guide-2026 — leave blank to auto-generate',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'category',
        'label'       => 'Category',
        'type'        => 'text',
        'placeholder' => 'e.g. Buying Guide, Market Trends',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'author',
        'label'       => 'Author',
        'type'        => 'text',
        'placeholder' => 'Enter author name',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'image',
        'label'       => 'Featured Image',
        'type'        => 'file',
        'placeholder' => '',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'excerpt',
        'label'       => 'Short Excerpt',
        'type'        => 'textarea',
        'placeholder' => 'One or two lines shown on the blog listing card',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'content',
        'label'       => 'Full Content',
        'type'        => 'textarea',
        'placeholder' => 'Full blog content',
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'status',
        'label'       => 'Status',
        'type'        => 'select',
        'options'     => ['Active' => 'Active', 'Inactive' => 'Inactive'],
        'value'       => 'Active',
        'required'    => true
    ],
    [
        'name'        => 'blog_order',
        'label'       => 'Display Order',
        'type'        => 'number',
        'placeholder' => 'Enter display order',
        'value'       => '1',
        'required'    => true
    ]
];

$editModalId      = 'editBlogModal';
$editButtonText   = 'Edit Blog';
$updateButtonName = 'update';
$updateButtonText = 'Update Blog';
$editFormFields   = [
    [
        'name'        => 'blog_id',
        'label'       => '',
        'type'        => 'hidden',
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'title',
        'label'       => 'Blog Title',
        'type'        => 'text',
        'placeholder' => 'Enter blog title',
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'slug',
        'label'       => 'Blog URL / Slug',
        'type'        => 'text',
        'placeholder' => 'e.g. buying-guide-2026',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'category',
        'label'       => 'Category',
        'type'        => 'text',
        'placeholder' => 'e.g. Buying Guide, Market Trends',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'author',
        'label'       => 'Author',
        'type'        => 'text',
        'placeholder' => 'Enter author name',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'image',
        'label'       => 'Replace Image (leave blank to keep existing)',
        'type'        => 'file',
        'placeholder' => '',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'excerpt',
        'label'       => 'Short Excerpt',
        'type'        => 'textarea',
        'placeholder' => 'One or two lines shown on the blog listing card',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'content',
        'label'       => 'Full Content',
        'type'        => 'textarea',
        'placeholder' => 'Full blog content',
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'status',
        'label'       => 'Status',
        'type'        => 'select',
        'options'     => ['Active' => 'Active', 'Inactive' => 'Inactive'],
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'blog_order',
        'label'       => 'Display Order',
        'type'        => 'number',
        'placeholder' => 'Enter display order',
        'value'       => '',
        'required'    => true
    ]
];

$rows = [];
if(count($blogs) > 0) {
    $startNumber = (($page - 1) * $perPage) + 1;
    $i = $startNumber;
    foreach($blogs as $b) {

        $thumb = !empty($b['image']) && file_exists($uploadDir . $b['image'])
            ? '<img src="uploads/blogs/' . htmlspecialchars($b['image']) . '" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:.4rem;">'
            : '<span class="text-muted small">No image</span>';

        $statusBadge = $b['status'] === 'Active'
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-secondary">Inactive</span>';

        $actionButtons = '
            <button class="btn btn-action btn-action-edit edit-btn" type="button"
               data-id="'.htmlspecialchars($b['id'], ENT_QUOTES).'"
               data-blog_id="'.htmlspecialchars($b['id'], ENT_QUOTES).'"
               data-title="'.htmlspecialchars($b['title'], ENT_QUOTES).'"
               data-slug="'.htmlspecialchars($b['slug'], ENT_QUOTES).'"
               data-category="'.htmlspecialchars($b['category'] ?? '', ENT_QUOTES).'"
               data-author="'.htmlspecialchars($b['author'] ?? '', ENT_QUOTES).'"
               data-status="'.htmlspecialchars($b['status'], ENT_QUOTES).'"
               data-blog_order="'.htmlspecialchars($b['blog_order'], ENT_QUOTES).'"
               data-bs-toggle="modal"
               data-bs-target="#editBlogModal">
               <i class="bi bi-pencil"></i>
            </button>
            <a class="btn btn-action btn-action-delete" href="blog.php?delete='.$b['id'].'&csrf_token='.urlencode(csrf_token()).'" onclick="return confirm(\'Delete this blog post? This cannot be undone.\');">
                <i class="bi bi-trash"></i>
            </a>';

        $rows[] = [
            $i++,
            $thumb,
            htmlspecialchars($b['title']),
            htmlspecialchars($b['category'] ?? '—'),
            $statusBadge,
            htmlspecialchars($b['blog_order']),
            $actionButtons
        ];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Blogs</title>
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
        // Auto-suggest a slug from the title, unless the admin types their own
        (function(){
            function slugify(text){
                return text.toLowerCase().trim()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }

            function wireUp(titleSelector, slugSelector){
                const titleInput = document.querySelector(titleSelector);
                const slugInput  = document.querySelector(slugSelector);
                if(!titleInput || !slugInput) return;

                let slugTouched = slugInput.value.trim() !== '';

                slugInput.addEventListener('input', function(){
                    slugTouched = slugInput.value.trim() !== '';
                });

                titleInput.addEventListener('input', function(){
                    if(!slugTouched){
                        slugInput.value = slugify(titleInput.value);
                    }
                });
            }

            wireUp('#addBlogModal [name="title"]', '#addBlogModal [name="slug"]');
            wireUp('#editBlogModal [name="title"]', '#editBlogModal [name="slug"]');
        })();
    </script>

</body>
</html>