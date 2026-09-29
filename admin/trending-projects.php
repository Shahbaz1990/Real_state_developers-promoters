<?php
require_once 'auth_check.php';

// Folder where project images get saved. Make sure this folder exists and is writable.
$upload_dir = __DIR__ . '/uploads/trending-projects/';
if(!is_dir($upload_dir)){
    mkdir($upload_dir, 0755, true);
}

// ===============================
// Detect whether the trending_project_images gallery table exists yet.
// If create-gallery-table.sql hasn't been run, every gallery-related query
// below is skipped entirely so the page never crashes.
// ===============================
$hasGalleryTable = false;
$galTableCheck = $conn->query("SHOW TABLES LIKE 'trending_project_images'");
if ($galTableCheck && $galTableCheck->num_rows > 0) {
    $hasGalleryTable = true;
}

// ===============================
// Detect whether the floor_plans table exists
// ===============================
$hasFloorPlanTable = false;
$fpTableCheck = $conn->query("SHOW TABLES LIKE 'floor_plans'");
if ($fpTableCheck && $fpTableCheck->num_rows > 0) {
    $hasFloorPlanTable = true;
}

// ===============================
// Load property categories (for the Category dropdown + display lookup).
// trending_projects.category_id -> property_categories.id
// ===============================
$categories = [];
$catResult = $conn->query("SELECT id, name FROM property_categories WHERE status='Active' ORDER BY category_order ASC, name ASC");
if($catResult){
    while($c = $catResult->fetch_assoc()){
        $categories[] = $c;
    }
}
// id => name map, used to print the category name in the table without re-querying per row
$categoryNameById = [];
foreach($categories as $c){
    $categoryNameById[$c['id']] = $c['name'];
}

// Turn "Kalpataru Vian" into "kalpataru-vian"
function slugify($text){
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

// Make sure the slug is unique in trending_projects (appends -2, -3, ... if needed).
// $ignoreId lets an update skip comparing against its own current row.
function uniqueSlug($conn, $base, $ignoreId = 0){
    $slug = $base;
    $i = 2;
    while(true){
        $stmt = $conn->prepare("SELECT id FROM trending_projects WHERE slug=? AND id!=? LIMIT 1");
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

// Pull any flash message set by a previous redirect, then clear it
$msg = $_SESSION['flash_msg'] ?? "";
unset($_SESSION['flash_msg']);

// ---------------------------------------------------------------
// SECTION HEADING (title + subtitle) — single row, save_section updates it
// ---------------------------------------------------------------
if(isset($_POST['save_section'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: trending-projects.php");
        exit;
    }

    $title    = $_POST['title'];
    $subtitle = $_POST['subtitle'];

    // Check if a section row already exists
    $existing = $conn->query("SELECT id FROM trending_projects_section LIMIT 1");

    if($existing && $existing->num_rows > 0){
        $row = $existing->fetch_assoc();
        $stmt = $conn->prepare("UPDATE trending_projects_section SET title=?, subtitle=? WHERE id=?");
        $stmt->bind_param("ssi", $title, $subtitle, $row['id']);
    }else{
        $stmt = $conn->prepare("INSERT INTO trending_projects_section(title, subtitle) VALUES(?,?)");
        $stmt->bind_param("ss", $title, $subtitle);
    }

    if($stmt->execute()){
        set_flash('success', "Section Heading Saved Successfully");
    }else{
        set_flash('error', "Something went wrong. Please try again.");
    }
    $stmt->close();

    header("Location: trending-projects.php");
    exit;
}

// Delete section heading (clears it so a new one can be added)
if(isset($_GET['delete_section'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: trending-projects.php");
        exit;
    }

    $conn->query("DELETE FROM trending_projects_section");
    set_flash('success', "Section Heading Deleted Successfully");

    header("Location: trending-projects.php");
    exit;
}

// ---------------------------------------------------------------
// PROJECTS (looped list)
// ---------------------------------------------------------------

// Insert new trending project
if(isset($_POST['save'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: trending-projects.php");
        exit;
    }

    $project_name = $_POST['project_name'];
    $category_id  = $_POST['category_id'] !== '' ? (int) $_POST['category_id'] : null;
    $address      = $_POST['address'];
    $price        = $_POST['price'];
    $order        = (int) $_POST['project_order'];

    // New fields
    $bedrooms         = $_POST['bedrooms'] !== '' ? (int) $_POST['bedrooms'] : null;
    $bathrooms        = $_POST['bathrooms'] !== '' ? (int) $_POST['bathrooms'] : null;
    $area             = $_POST['area'];
    $year_built       = $_POST['year_built'];
    $furnished        = $_POST['furnished'];
    $plot_area        = $_POST['plot_area'];
    $amenities        = $_POST['amenities'];
    $owner_email      = $_POST['owner_email'];
    $owner_phone      = $_POST['owner_phone'];
    $appointment_date = $_POST['appointment_date'] !== '' ? $_POST['appointment_date'] : null;

    // Use the admin-entered slug if provided, otherwise derive one from the project name
    $slugInput = isset($_POST['slug']) ? trim($_POST['slug']) : '';
    $baseSlug  = $slugInput !== '' ? slugify($slugInput) : slugify($project_name);
    $slug      = uniqueSlug($conn, $baseSlug);

    $image_name = "";

    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK){
        $uploaded = secure_upload('image', $upload_dir, 'project_', ADMIN_ALLOWED_IMAGE_MIMES, ADMIN_ALLOWED_IMAGE_EXTS);
        if($uploaded === false){
            set_flash('error', "Invalid project image. Allowed: jpg, jpeg, png, webp, gif.");
            header("Location: trending-projects.php");
            exit;
        }
        $image_name = $uploaded;
    }

    $stmt = $conn->prepare("INSERT INTO trending_projects
        (project_name, category_id, slug, address, price, image, project_order,
         bedrooms, bathrooms, area, year_built, furnished, plot_area, amenities,
         owner_email, owner_phone, appointment_date)
        VALUES (?,?,?,?,?,?,?, ?,?,?,?,?,?,?, ?,?,?)");
    $stmt->bind_param(
        "sissssi" . "iisssss" . "sss",
        $project_name, $category_id, $slug, $address, $price, $image_name, $order,
        $bedrooms, $bathrooms, $area, $year_built, $furnished, $plot_area, $amenities,
        $owner_email, $owner_phone, $appointment_date
    );

    if($stmt->execute()){
        $newProjectId = $conn->insert_id;

        // Save any additional gallery images uploaded alongside the thumbnail
        if($hasGalleryTable && !empty($_FILES['gallery_images']['name'][0])){
            foreach($_FILES['gallery_images']['name'] as $idx => $filename){
                if($_FILES['gallery_images']['error'][$idx] === UPLOAD_ERR_OK){
                    $gName = secure_upload_from_file([
                        'name'     => $_FILES['gallery_images']['name'][$idx],
                        'tmp_name' => $_FILES['gallery_images']['tmp_name'][$idx],
                        'size'     => $_FILES['gallery_images']['size'][$idx],
                        'error'    => $_FILES['gallery_images']['error'][$idx],
                    ], $upload_dir, 'gallery_', ADMIN_ALLOWED_IMAGE_MIMES, ADMIN_ALLOWED_IMAGE_EXTS);
                    if($gName === false || $gName === null){
                        continue;
                    }

                    $gStmt = $conn->prepare("INSERT INTO trending_project_images (project_id, image, image_order) VALUES (?, ?, ?)");
                    $gStmt->bind_param("isi", $newProjectId, $gName, $idx);
                    $gStmt->execute();
                    $gStmt->close();
                }
            }
        }

        // Save floor plans
        if($hasFloorPlanTable && isset($_POST['floor_plan_bhk']) && is_array($_POST['floor_plan_bhk'])){
            for($i = 0; $i < count($_POST['floor_plan_bhk']); $i++){
                $bhk = trim($_POST['floor_plan_bhk'][$i]);
                if(empty($bhk)) continue;
                
                $area_sqft = trim($_POST['floor_plan_area_sqft'][$i] ?? '');
                $area_sqm = trim($_POST['floor_plan_area_sqm'][$i] ?? '');
                $price_fp = trim($_POST['floor_plan_price'][$i] ?? '');
                $price_range = trim($_POST['floor_plan_price_range'][$i] ?? '');
                $status_fp = trim($_POST['floor_plan_status'][$i] ?? 'Under Construction');
                $possession = trim($_POST['floor_plan_possession'][$i] ?? '');
                
                $fpStmt = $conn->prepare("INSERT INTO floor_plans 
                    (project_id, bhk_type, carpet_area_sqft, carpet_area_sqm, price, price_range, status, possession_date, display_order) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $fpStmt->bind_param("isssssssi", $newProjectId, $bhk, $area_sqft, $area_sqm, $price_fp, $price_range, $status_fp, $possession, $i);
                $fpStmt->execute();
                $fpStmt->close();
            }
        }

        set_flash('success', "Property Added Successfully");
    }else{
        set_flash('error', "Something went wrong. Please try again.");
    }
    $stmt->close();

    header("Location: trending-projects.php");
    exit;
}

// Update existing trending project (from edit modal)
if(isset($_POST['update'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: trending-projects.php");
        exit;
    }

    $id           = (int) $_POST['project_id'];
    $project_name = $_POST['project_name'];
    $category_id  = $_POST['category_id'] !== '' ? (int) $_POST['category_id'] : null;
    $address      = $_POST['address'];
    $price        = $_POST['price'];
    $order        = $_POST['project_order'];

    // New fields
    $bedrooms         = $_POST['bedrooms'] !== '' ? (int) $_POST['bedrooms'] : null;
    $bathrooms        = $_POST['bathrooms'] !== '' ? (int) $_POST['bathrooms'] : null;
    $area             = $_POST['area'];
    $year_built       = $_POST['year_built'];
    $furnished        = $_POST['furnished'];
    $plot_area        = $_POST['plot_area'];
    $amenities        = $_POST['amenities'];
    $owner_email      = $_POST['owner_email'];
    $owner_phone      = $_POST['owner_phone'];
    $appointment_date = $_POST['appointment_date'] !== '' ? $_POST['appointment_date'] : null;

    // Use the admin-entered slug if provided, otherwise derive one from the project name
    $slugInput = isset($_POST['slug']) ? trim($_POST['slug']) : '';
    $baseSlug  = $slugInput !== '' ? slugify($slugInput) : slugify($project_name);
    $slug      = uniqueSlug($conn, $baseSlug, (int) $id);

    // FIX: Get the existing image first if no new image is uploaded
    $existing_image = $_POST['existing_image'] ?? '';
    
    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK){
        $uploaded = secure_upload('image', $upload_dir, 'project_', ADMIN_ALLOWED_IMAGE_MIMES, ADMIN_ALLOWED_IMAGE_EXTS);
        if($uploaded === false){
            set_flash('error', "Invalid project image. Allowed: jpg, jpeg, png, webp, gif.");
            header("Location: trending-projects.php");
            exit;
        }
        $image_name = $uploaded;

        // Delete old image if it exists
        if($existing_image && file_exists($upload_dir . $existing_image)){
            unlink($upload_dir . $existing_image);
        }

        $stmt = $conn->prepare("UPDATE trending_projects SET
            project_name=?, category_id=?, slug=?, address=?, price=?, image=?, project_order=?,
            bedrooms=?, bathrooms=?, area=?, year_built=?, furnished=?, plot_area=?, amenities=?,
            owner_email=?, owner_phone=?, appointment_date=?
            WHERE id=?");
        $stmt->bind_param(
            "sissssi" . "iisssss" . "sss" . "i",
            $project_name, $category_id, $slug, $address, $price, $image_name, $order,
            $bedrooms, $bathrooms, $area, $year_built, $furnished, $plot_area, $amenities,
            $owner_email, $owner_phone, $appointment_date, $id
        );
    }else{
        // Use the existing image if no new one is uploaded
        $image_name = $existing_image;
        
        $stmt = $conn->prepare("UPDATE trending_projects SET
            project_name=?, category_id=?, slug=?, address=?, price=?, project_order=?,
            bedrooms=?, bathrooms=?, area=?, year_built=?, furnished=?, plot_area=?, amenities=?,
            owner_email=?, owner_phone=?, appointment_date=?
            WHERE id=?");
        $stmt->bind_param(
            "sisssi" . "iisssss" . "sss" . "i",
            $project_name, $category_id, $slug, $address, $price, $order,
            $bedrooms, $bathrooms, $area, $year_built, $furnished, $plot_area, $amenities,
            $owner_email, $owner_phone, $appointment_date, $id
        );
    }

    if($stmt->execute()){
        // Append any newly uploaded gallery images (existing ones are untouched)
        if($hasGalleryTable && !empty($_FILES['gallery_images']['name'][0])){
            // Start numbering after whatever the highest current image_order is, so new ones go to the end
            $orderStart = 0;
            $orderResult = $conn->prepare("SELECT COALESCE(MAX(image_order), -1) AS max_order FROM trending_project_images WHERE project_id = ?");
            $orderResult->bind_param("i", $id);
            $orderResult->execute();
            $orderRow = $orderResult->get_result()->fetch_assoc();
            $orderResult->close();
            $orderStart = ((int) $orderRow['max_order']) + 1;

            foreach($_FILES['gallery_images']['name'] as $idx => $filename){
                if($_FILES['gallery_images']['error'][$idx] === UPLOAD_ERR_OK){
                    $gName = secure_upload_from_file([
                        'name'     => $_FILES['gallery_images']['name'][$idx],
                        'tmp_name' => $_FILES['gallery_images']['tmp_name'][$idx],
                        'size'     => $_FILES['gallery_images']['size'][$idx],
                        'error'    => $_FILES['gallery_images']['error'][$idx],
                    ], $upload_dir, 'gallery_', ADMIN_ALLOWED_IMAGE_MIMES, ADMIN_ALLOWED_IMAGE_EXTS);
                    if($gName === false || $gName === null){
                        continue;
                    }

                    $gOrder = $orderStart + $idx;
                    $gStmt = $conn->prepare("INSERT INTO trending_project_images (project_id, image, image_order) VALUES (?, ?, ?)");
                    $gStmt->bind_param("isi", $id, $gName, $gOrder);
                    $gStmt->execute();
                    $gStmt->close();
                }
            }
        }

        // ===== UPDATE FLOOR PLANS =====
        if($hasFloorPlanTable){
            // Delete existing floor plans for this project
            $delFp = $conn->prepare("DELETE FROM floor_plans WHERE project_id = ?");
            $delFp->bind_param("i", $id);
            $delFp->execute();
            $delFp->close();
            
            // Insert new floor plans
            if(isset($_POST['floor_plan_bhk']) && is_array($_POST['floor_plan_bhk'])){
                for($i = 0; $i < count($_POST['floor_plan_bhk']); $i++){
                    $bhk = trim($_POST['floor_plan_bhk'][$i]);
                    if(empty($bhk)) continue;
                    
                    $area_sqft = trim($_POST['floor_plan_area_sqft'][$i] ?? '');
                    $area_sqm = trim($_POST['floor_plan_area_sqm'][$i] ?? '');
                    $price_fp = trim($_POST['floor_plan_price'][$i] ?? '');
                    $price_range = trim($_POST['floor_plan_price_range'][$i] ?? '');
                    $status_fp = trim($_POST['floor_plan_status'][$i] ?? 'Under Construction');
                    $possession = trim($_POST['floor_plan_possession'][$i] ?? '');
                    
                    $fpStmt = $conn->prepare("INSERT INTO floor_plans 
                        (project_id, bhk_type, carpet_area_sqft, carpet_area_sqm, price, price_range, status, possession_date, display_order) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $fpStmt->bind_param("isssssssi", $id, $bhk, $area_sqft, $area_sqm, $price_fp, $price_range, $status_fp, $possession, $i);
                    $fpStmt->execute();
                    $fpStmt->close();
                }
            }
        }

        set_flash('success', "Property Updated Successfully");
    }else{
        set_flash('error', "Something went wrong. Please try again.");
    }
    $stmt->close();

    header("Location: trending-projects.php");
    exit;
}

// Delete a single gallery image
if($hasGalleryTable && isset($_GET['delete_gallery_image'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: trending-projects.php");
        exit;
    }

    $imgId = (int) $_GET['delete_gallery_image'];

    $stmt = $conn->prepare("SELECT image FROM trending_project_images WHERE id=?");
    $stmt->bind_param("i", $imgId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if($row){
        if($row['image'] && file_exists($upload_dir . $row['image'])){
            unlink($upload_dir . $row['image']);
        }
        $stmt = $conn->prepare("DELETE FROM trending_project_images WHERE id=?");
        $stmt->bind_param("i", $imgId);
        $stmt->execute();
        $stmt->close();
        set_flash('info', "Gallery image removed");
    }

    header("Location: trending-projects.php");
    exit;
}

// Delete trending project
if(isset($_GET['delete'])){

    if(!verify_csrf()){
        set_flash('error', "Your session expired. Please try again.");
        header("Location: trending-projects.php");
        exit;
    }

    $id = (int) $_GET['delete'];

    $stmt = $conn->prepare("SELECT image FROM trending_projects WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if($row && $row['image'] && file_exists($upload_dir . $row['image'])){
        unlink($upload_dir . $row['image']);
    }

    // Clean up gallery image files too (the DB rows cascade-delete via the foreign key,
    // but the actual uploaded files on disk need to be removed manually)
    if($hasGalleryTable){
        $galStmt = $conn->prepare("SELECT image FROM trending_project_images WHERE project_id=?");
        $galStmt->bind_param("i", $id);
        $galStmt->execute();
        $galResult = $galStmt->get_result();
        while($g = $galResult->fetch_assoc()){
            if($g['image'] && file_exists($upload_dir . $g['image'])){
                unlink($upload_dir . $g['image']);
            }
        }
        $galStmt->close();
    }

    // Delete floor plans
    if($hasFloorPlanTable){
        $delFp = $conn->prepare("DELETE FROM floor_plans WHERE project_id = ?");
        $delFp->bind_param("i", $id);
        $delFp->execute();
        $delFp->close();
    }

    $stmt = $conn->prepare("DELETE FROM trending_projects WHERE id=?");
    $stmt->bind_param("i", $id);

    if($stmt->execute()){
        set_flash('success', "Property Deleted Successfully");
    }
    $stmt->close();

    header("Location: trending-projects.php");
    exit;
}

// Fetch the section heading (title + subtitle)
$section = ['title' => '', 'subtitle' => ''];
$sectionResult = $conn->query("SELECT title, subtitle FROM trending_projects_section LIMIT 1");
if($sectionResult && $sectionResult->num_rows > 0){
    $section = $sectionResult->fetch_assoc();
}

// Pagination setup for trending projects
$totalProjects = 0;
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM trending_projects");
if($stmt){
    $stmt->execute();
    $stmt->bind_result($totalProjects);
    $stmt->fetch();
    $stmt->close();
}

$perPage = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$currentPage = $page;
$totalPages = max(1, (int) ceil($totalProjects / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$trending_projects = [];
$stmt = $conn->prepare("SELECT id, project_name, category_id, slug, address, price, image, project_order,
    bedrooms, bathrooms, area, year_built, furnished, plot_area, amenities,
    owner_email, owner_phone, appointment_date
    FROM trending_projects ORDER BY project_order ASC LIMIT ?, ?");
if($stmt){
    $stmt->bind_param("ii", $offset, $perPage);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()){
        $trending_projects[] = $row;
    }
    $stmt->close();
}

// Gallery images for every project, grouped by project_id, for the list table + edit modal
$galleryByProject = [];
if($hasGalleryTable){
    $galleryResult = $conn->query("SELECT id, project_id, image FROM trending_project_images ORDER BY image_order ASC");
    if($galleryResult){
        while($g = $galleryResult->fetch_assoc()){
            $galleryByProject[$g['project_id']][] = $g;
        }
    }
}

// Floor plans for every project, grouped by project_id
$floorPlansByProject = [];
if($hasFloorPlanTable){
    $fpResult = $conn->query("SELECT id, project_id, bhk_type, carpet_area_sqft, carpet_area_sqm, price, price_range, status, possession_date, display_order FROM floor_plans ORDER BY display_order ASC");
    if($fpResult){
        while($fp = $fpResult->fetch_assoc()){
            $floorPlansByProject[$fp['project_id']][] = $fp;
        }
    }
}

$showFooter = true;

$pageTitle         = 'Property';
$tableTitle        = 'All Properties';
$showAddButton     = true;
$addButtonText     = 'Add New Property';
$addModalId        = 'addProjectModal';
$saveButtonName    = 'save';
$saveButtonText    = 'Save Property';
$showSearch        = true;
$searchPlaceholder = 'Type project name, slug, address or order...';
$headers           = ['Sr No', 'Image', 'Project Name', 'Category', 'Slug', 'Address', 'Price', 'Beds', 'Baths', 'Area', 'Order', 'Action'];

$formFields = [
    [
        'name'        => 'project_name',
        'label'       => 'Project Name',
        'type'        => 'text',
        'placeholder' => 'Enter property name',
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'category_id',
        'label'       => 'Category',
        'type'        => 'select',
        'placeholder' => 'Select a category',
        'options'     => (function() use ($categories) {
            $opts = [];
            foreach($categories as $c){
                $opts[$c['id']] = $c['name'];
            }
            return $opts;
        })(),
        'required'    => true
    ],
    [
        'name'        => 'slug',
        'label'       => 'URL Slug',
        'type'        => 'text',
        'placeholder' => 'Enter slug or leave blank',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'address',
        'label'       => 'Address',
        'type'        => 'text',
        'placeholder' => 'Enter address',
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'price',
        'label'       => 'Price',
        'type'        => 'text',
        'placeholder' => 'Enter price',
        'value'       => '',
        'required'    => true
    ],
    [
        'name'        => 'bedrooms',
        'label'       => 'Bedrooms',
        'type'        => 'number',
        'placeholder' => 'Enter bedrooms',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'bathrooms',
        'label'       => 'Bathrooms',
        'type'        => 'number',
        'placeholder' => 'Enter bathrooms',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'area',
        'label'       => 'Area',
        'type'        => 'text',
        'placeholder' => 'Enter area',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'year_built',
        'label'       => 'Year Built',
        'type'        => 'text',
        'placeholder' => 'Enter year built',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'furnished',
        'label'       => 'Is it furnished?',
        'type'        => 'select',
        'placeholder' => 'Select furnished status',
        'options'     => [
            '' => 'Select',
            'Furnished' => 'Furnished',
            'Semi-furnished' => 'Semi-furnished',
            'Unfurnished' => 'Unfurnished'
        ],
        'required'    => false
    ],
    [
        'name'        => 'plot_area',
        'label'       => 'Plot Area/Total Area',
        'type'        => 'text',
        'placeholder' => 'Enter plot area',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'amenities',
        'label'       => 'Amenities',
        'type'        => 'text',
        'placeholder' => 'Comma-separated amenities',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'owner_email',
        'label'       => 'Owner Email',
        'type'        => 'email',
        'placeholder' => 'Enter owner email',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'owner_phone',
        'label'       => 'Owner Phone',
        'type'        => 'text',
        'placeholder' => 'Enter owner phone',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'appointment_date',
        'label'       => 'Appointment Date',
        'type'        => 'date',
        'placeholder' => '',
        'value'       => '',
        'required'    => false
    ],
    [
        'name'        => 'image',
        'label'       => 'Project Image (Thumbnail)',
        'type'        => 'file',
        'required'    => true,
        'accept'      => 'image/*'
    ],
    [
        'name'        => 'gallery_images[]',
        'label'       => 'Gallery Images',
        'type'        => 'file',
        'required'    => false,
        'accept'      => 'image/*',
        'multiple'    => true
    ],
    [
        'name'        => 'project_order',
        'label'       => 'Project Order',
        'type'        => 'number',
        'placeholder' => 'Enter project order',
        'value'       => '1',
        'required'    => true
    ]
];

// Build an HTML snippet for the Add modal that groups non-file inputs two-per-row.
// This is page-specific so we don't change the reusable component itself.
$addFormHtml = '';
$bufferCols = [];
foreach($formFields as $f){
    $isFile = (($f['type'] ?? 'text') === 'file');
    $classes = $isFile ? 'full-row' : 'col-2';

    // render field HTML
    ob_start();
    ?>
    <div class="<?= $classes ?> mb-3">
        <label class="form-label-custom fw-medium text-secondary"><?= htmlspecialchars($f['label'] ?? '') ?></label>
        <?php if(($f['type'] ?? 'text') === 'select' && !empty($f['options']) && is_array($f['options'])): ?>
            <select name="<?= htmlspecialchars($f['name']) ?>" class="form-select form-input-custom" <?= !empty($f['required']) ? 'required' : '' ?> >
                <option value="" selected disabled><?= htmlspecialchars($f['placeholder'] ?? 'Select an option') ?></option>
                <?php foreach($f['options'] as $optVal => $optLbl): ?>
                    <option value="<?= htmlspecialchars($optVal) ?>"><?= htmlspecialchars($optLbl) ?></option>
                <?php endforeach; ?>
            </select>
        <?php elseif(($f['type'] ?? 'text') === 'textarea'): ?>
            <textarea name="<?= htmlspecialchars($f['name']) ?>" class="form-control form-input-custom" placeholder="<?= htmlspecialchars($f['placeholder'] ?? '') ?>" <?= !empty($f['required']) ? 'required' : '' ?>></textarea>
        <?php else: ?>
            <input
                type="<?= htmlspecialchars($f['type'] ?? 'text') ?>"
                name="<?= htmlspecialchars($f['name']) ?>"
                class="form-control form-input-custom"
                placeholder="<?= htmlspecialchars($f['placeholder'] ?? '') ?>"
                <?= !empty($f['required']) ? 'required' : '' ?>
                <?= !empty($f['accept']) ? 'accept="' . htmlspecialchars($f['accept']) . '"' : '' ?>
                <?= !empty($f['multiple']) ? 'multiple' : '' ?>
            >
        <?php endif; ?>
    </div>
    <?php
    $html = ob_get_clean();

    if($isFile){
        // flush any buffered cols first
        if(!empty($bufferCols)){
            $addFormHtml .= '<div class="form-row-2">'.implode('', $bufferCols).'</div>';
            $bufferCols = [];
        }
        $addFormHtml .= '<div class="form-row-2">'.$html.'</div>'; // file spans full row
    }else{
        $bufferCols[] = $html;
        if(count($bufferCols) === 2){
            $addFormHtml .= '<div class="form-row-2">'.implode('', $bufferCols).'</div>';
            $bufferCols = [];
        }
    }
}
if(!empty($bufferCols)){
    $addFormHtml .= '<div class="form-row-2">'.implode('', $bufferCols).'</div>';
}

$editModalId      = 'editProjectModal';
$editButtonText   = 'Edit Property';
$updateButtonName = 'update';
$updateButtonText = 'Update Property';
$editFormFields   = [];

$rows = [];
if(count($trending_projects) > 0){
    $startNumber = $offset + 1;
    $i = $startNumber;
    foreach($trending_projects as $p){
        $galleryCount = count($galleryByProject[$p['id']] ?? []);
        $floorPlanCount = count($floorPlansByProject[$p['id']] ?? []);
        $actionButtons = '
            <div class="dropdown">
                <button class="btn btn-action btn-action-more dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <button class="dropdown-item edit-btn" type="button"
                            data-id="'.htmlspecialchars($p['id'], ENT_QUOTES).'"
                            data-project_id="'.htmlspecialchars($p['id'], ENT_QUOTES).'"
                            data-projectname="'.htmlspecialchars($p['project_name'], ENT_QUOTES).'"
                            data-category="'.htmlspecialchars($p['category_id'] ?? '', ENT_QUOTES).'"
                            data-slug="'.htmlspecialchars($p['slug'], ENT_QUOTES).'"
                            data-address="'.htmlspecialchars($p['address'], ENT_QUOTES).'"
                            data-price="'.htmlspecialchars($p['price'], ENT_QUOTES).'"
                            data-order="'.htmlspecialchars($p['project_order'], ENT_QUOTES).'"
                            data-image="'.htmlspecialchars($p['image'], ENT_QUOTES).'"
                            data-bedrooms="'.htmlspecialchars($p['bedrooms'] ?? '', ENT_QUOTES).'"
                            data-bathrooms="'.htmlspecialchars($p['bathrooms'] ?? '', ENT_QUOTES).'"
                            data-area="'.htmlspecialchars($p['area'] ?? '', ENT_QUOTES).'"
                            data-yearbuilt="'.htmlspecialchars($p['year_built'] ?? '', ENT_QUOTES).'"
                            data-furnished="'.htmlspecialchars($p['furnished'] ?? '', ENT_QUOTES).'"
                            data-plotarea="'.htmlspecialchars($p['plot_area'] ?? '', ENT_QUOTES).'"
                            data-amenities="'.htmlspecialchars($p['amenities'] ?? '', ENT_QUOTES).'"
                            data-owneremail="'.htmlspecialchars($p['owner_email'] ?? '', ENT_QUOTES).'"
                            data-ownerphone="'.htmlspecialchars($p['owner_phone'] ?? '', ENT_QUOTES).'"
                            data-appointmentdate="'.htmlspecialchars($p['appointment_date'] ?? '', ENT_QUOTES).'"
                            data-gallery="'.htmlspecialchars(json_encode($galleryByProject[$p['id']] ?? []), ENT_QUOTES).'"
                            data-floorplans="'.htmlspecialchars(json_encode($floorPlansByProject[$p['id']] ?? []), ENT_QUOTES).'"
                            data-bs-toggle="modal"
                            data-bs-target="#editProjectModal">
                            Edit
                        </button>
                    </li>
                    <li>
                        <a class="dropdown-item text-danger" href="trending-projects.php?delete='.htmlspecialchars($p['id'], ENT_QUOTES).'&csrf_token='.urlencode(csrf_token()).'" onclick="return confirm(\'Delete this project?\');">
                            Delete
                        </a>
                    </li>
                </ul>
            </div>';

        $rows[] = [
            $i++,
            $p['image'] ? '<img src="uploads/trending-projects/'.htmlspecialchars($p['image'], ENT_QUOTES).'" alt="project" style="height:60px; width:80px; object-fit:cover; border-radius:6px;">' : '',
            htmlspecialchars($p['project_name'], ENT_QUOTES),
            htmlspecialchars($categoryNameById[$p['category_id']] ?? '—', ENT_QUOTES),
            '<code>'.htmlspecialchars($p['slug'], ENT_QUOTES).'</code>',
            htmlspecialchars($p['address'], ENT_QUOTES),
            htmlspecialchars($p['price'], ENT_QUOTES),
            htmlspecialchars($p['bedrooms'] ?? '', ENT_QUOTES),
            htmlspecialchars($p['bathrooms'] ?? '', ENT_QUOTES),
            htmlspecialchars($p['area'] ?? '', ENT_QUOTES),
            htmlspecialchars($p['project_order'], ENT_QUOTES),
            $actionButtons
        ];
    }
}

?>
<!DOCTYPE html>
<html>
    <head>
    <title>Properties</title>
    <link rel="stylesheet" href="css/adminlte.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    </head>
<body>

    <style>
    /* Scoped two-column form for Add Project modal on this page only */
    #addProjectModal .form-row-2 {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    #addProjectModal .form-row-2 .col-2 {
        flex: 1 1 48%;
    }
    /* Make file inputs and full-width controls span whole row */
    #addProjectModal .form-row-2 .full-row {
        flex: 1 1 100%;
    }
    /* Floor plan row styles */
    .floor-plan-row {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 10px;
        margin-bottom: 8px;
        position: relative;
    }
    .floor-plan-row .btn-remove-fp {
        position: absolute;
        top: -8px;
        right: -8px;
        width: 24px;
        height: 24px;
        padding: 0;
        border-radius: 50%;
        font-size: 12px;
        line-height: 24px;
        text-align: center;
    }
    #floorPlansContainer .floor-plan-row .form-control-sm {
        font-size: 0.8rem;
    }
    /* Modal large for add */
    #addProjectModal .modal-dialog {
        max-width: 800px;
    }
    #editProjectModal .modal-dialog {
        max-width: 900px;
    }
    </style>

    <div class="app-wrapper">
        <!-- Navbar -->
        <?php include 'nav.php'; ?>
        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>

        <main class="app-main">
            <div class="container mt-5">

            <div class="row justify-content-center">

            <div class="col-md-12">

            <?php if(!$hasGalleryTable){ ?>
            <div class="alert alert-warning">
                Gallery image uploads are disabled until the <code>trending_project_images</code> table is created.
                Run <code>create-gallery-table.sql</code> in your database, then reload this page.
            </div>
            <?php } ?>

            <?php if(!$hasFloorPlanTable){ ?>
            <div class="alert alert-warning">
                Floor Plans management is disabled until the <code>floor_plans</code> table is created.
                Run the SQL to create the table, then reload this page.
            </div>
            <?php } ?>

            <!-- Section Heading (title + subtitle) -->
            <?php if(empty($section['title'])){ ?>

            <div class="card">

            <div class="card-header bg-primary text-white">

            <h4>Add Section Heading</h4>

            </div>

            <div class="card-body">

            <form method="post">

            <div class="mb-3">

            <label>Title</label>

            <input
            type="text"
            name="title"
            class="form-control"
            required>

            </div>

            <div class="mb-3">

            <label>Subtitle</label>

            <input
            type="text"
            name="subtitle"
            class="form-control">

            </div>

            <button
            name="save_section"
            class="btn btn-primary">

            Save Section Heading

            </button>

            </form>

            </div>

            </div>

            <?php } ?>

            <!-- Section Heading List -->
            <div class="card mt-4">

            <div class="card-header ">

            <h5>Section Heading</h5>

            </div>

            <div class="card-body">

            <table class="table table-custom align-middle">
                <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>Title</th>
                        <th>Subtitle</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(!empty($section['title'])){ ?>
                    <tr>
                        <td>1</td>
                        <td><?= htmlspecialchars($section['title']) ?></td>
                        <td><?= htmlspecialchars($section['subtitle']) ?></td>
          <td>
    <button type="button" class="btn btn-sm rounded-pill me-2" data-bs-toggle="modal" data-bs-target="#editSectionModal" style="color:#7e9adb;background:#eff6ff;border:1px solid #bfdbfe;"><i class="bi bi-pencil"></i></button>

    <a href="trending-projects.php?delete_section=1&csrf_token=<?php echo urlencode(csrf_token()); ?>" class="btn btn-sm rounded-pill" onclick="return confirm('Delete this section heading?');" style="color:#f5a9a9;background:#fef2f2;border:1px solid #fecaca;"><i class="bi bi-trash"></i></a>
</td>
                    </tr>
                <?php }else{ ?>
                    <tr>
                        <td colspan="4" class="text-center">No section heading added yet</td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

            </div>

            </div>

            <!-- ===== ADD PROJECT MODAL WITH FLOOR PLANS ===== -->
            <div class="modal fade" id="addProjectModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        <form method="post" enctype="multipart/form-data">

                        <div class="modal-header">
                            <h5 class="modal-title">Add New Property</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <div class="mb-3">
                                <label>Project Name</label>
                                <input type="text" name="project_name" id="add_project_name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label>Category</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="" selected disabled>Select a category</option>
                                    <?php foreach($categories as $c): ?>
                                        <option value="<?= htmlspecialchars($c['id'], ENT_QUOTES) ?>"><?= htmlspecialchars($c['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>URL Slug</label>
                                <input type="text" name="slug" id="add_slug" class="form-control" placeholder="e.g. kalpataru-vian">
                                <small class="text-muted">Used in the page URL: /property-listing/&lt;slug&gt;/. Leave blank to auto-generate from the name.</small>
                            </div>

                            <div class="mb-3">
                                <label>Address</label>
                                <input type="text" name="address" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label>Price</label>
                                <input type="text" name="price" class="form-control" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Bedrooms</label>
                                    <input type="number" name="bedrooms" class="form-control" min="0">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Bathrooms</label>
                                    <input type="number" name="bathrooms" class="form-control" min="0">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Area</label>
                                    <input type="text" name="area" class="form-control" placeholder="e.g. 1,200 sqft">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Plot Area/Total Area</label>
                                    <input type="text" name="plot_area" class="form-control" placeholder="e.g. 2,400 sqft">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Year Built</label>
                                    <input type="text" name="year_built" class="form-control" placeholder="e.g. 2024">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Is it furnished?</label>
                                    <select name="furnished" class="form-select">
                                        <option value="">Select</option>
                                        <option>Furnished</option>
                                        <option>Semi-furnished</option>
                                        <option>Unfurnished</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Owner Email</label>
                                    <input type="email" name="owner_email" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Owner Phone</label>
                                    <input type="text" name="owner_phone" class="form-control">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>Amenities</label>
                                <input type="text" name="amenities" class="form-control" placeholder="Parking, Lift, Security, Swimming Pool, Garden, Clubhouse">
                                <small class="text-muted">Comma-separated — each one shows as a chip on the project page.</small>
                            </div>

                            <div class="mb-3">
                                <label>Appointment Date</label>
                                <input type="date" name="appointment_date" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label>Project Image (Thumbnail)</label>
                                <input type="file" name="image" class="form-control" accept="image/*" required>
                            </div>

                            <div class="mb-3">
                                <label>Gallery Images</label>
                                <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
                                <small class="text-muted">Select multiple images for the gallery.</small>
                            </div>

                            <!-- ===== FLOOR PLANS MANAGEMENT (ADD MODAL) ===== -->
                            <?php if($hasFloorPlanTable): ?>
                            <div class="mb-3">
                                <label class="fw-bold">Floor Plans &amp; Pricing</label>
                                <div id="addFloorPlansContainer">
                                    <!-- Default one empty row -->
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addFloorPlanRow('addFloorPlansContainer')">
                                    <i class="bi bi-plus-circle"></i> Add Floor Plan
                                </button>
                                <small class="text-muted d-block">Manage floor plans for each BHK type (2 BHK, 3 BHK, etc.)</small>
                            </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label>Property Order</label>
                                <input type="number" name="project_order" class="form-control" value="1" required>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="save" class="btn" style="background-color: #ff2d7a; border-color: #ff2d7a; color: #fff; border-radius: 20px;">Save Property</button>
                        </div>

                        </form>

                    </div>
                </div>
            </div>

            <?php
                // Override the add modal to use our custom one; clearing the field list
                // also stops the component from emitting a duplicate #addProjectModal.
                $page_add_modal_html = '';
                $formFields = [];
                include __DIR__ . '/components/table/table.php';
            ?>

            </div>

            </div>

            </div>

            </div>
        </main>
    </div>

    <!-- Edit Section Heading Modal -->
    <div class="modal fade" id="editSectionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <form method="post">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Section Heading</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Title</label>
                        <input
                            type="text"
                            name="title"
                            class="form-control"
                            value="<?= htmlspecialchars($section['title']) ?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label>Subtitle</label>
                        <input
                            type="text"
                            name="subtitle"
                            class="form-control"
                            value="<?= htmlspecialchars($section['subtitle']) ?>">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="save_section" class="btn" style="background-color: #ff2d7a; border-color: #ff2d7a; color: #fff; border-radius: 20px;">Update Section</button>
                </div>

                </form>

            </div>
        </div>
    </div>

    <!-- Edit Trending Project Modal -->
    <div class="modal fade" id="editProjectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <form method="post" enctype="multipart/form-data">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Trending Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="project_id" id="edit_project_id">
                    <input type="hidden" name="existing_image" id="edit_existing_image">

                    <div class="mb-3">
                        <label>Current Image</label><br>
                        <img id="edit_current_image" src="" alt="current project" style="height:80px; width:110px; object-fit:cover; border-radius:6px;">
                    </div>

                    <div class="mb-3">
                        <label>Property Name</label>
                        <input
                            type="text"
                            name="project_name"
                            id="edit_project_name"
                            class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label>Category</label>
                        <select name="category_id" id="edit_category_id" class="form-select" required>
                            <option value="" disabled>Select a category</option>
                            <?php foreach($categories as $c): ?>
                                <option value="<?= htmlspecialchars($c['id'], ENT_QUOTES) ?>"><?= htmlspecialchars($c['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>URL Slug</label>
                        <input
                            type="text"
                            name="slug"
                            id="edit_slug"
                            class="form-control"
                            placeholder="e.g. kalpataru-vian">
                        <small class="text-muted">Used in the page URL: /property-listing/&lt;slug&gt;/. Leave blank to auto-generate from the name.</small>
                    </div>

                    <div class="mb-3">
                        <label>Address</label>
                        <input
                            type="text"
                            name="address"
                            id="edit_address"
                            class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label>Price</label>
                        <input
                            type="text"
                            name="price"
                            id="edit_price"
                            class="form-control"
                            required>
                    </div>

                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label>Bedrooms</label>
                        <input type="number" name="bedrooms" id="edit_bedrooms" class="form-control" min="0">
                      </div>
                      <div class="col-md-6 mb-3">
                        <label>Bathrooms</label>
                        <input type="number" name="bathrooms" id="edit_bathrooms" class="form-control" min="0">
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label>Area</label>
                        <input type="text" name="area" id="edit_area" class="form-control" placeholder="e.g. 1,200 sqft">
                      </div>
                      <div class="col-md-6 mb-3">
                        <label>Plot Area/Total Area</label>
                        <input type="text" name="plot_area" id="edit_plot_area" class="form-control" placeholder="e.g. 2,400 sqft">
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label>Year Built</label>
                        <input type="text" name="year_built" id="edit_year_built" class="form-control" placeholder="e.g. 2024">
                      </div>
                      <div class="col-md-6 mb-3">
                        <label>Is it furnished?</label>
                        <select name="furnished" id="edit_furnished" class="form-select">
                          <option value="">Select</option>
                          <option>Furnished</option>
                          <option>Semi-furnished</option>
                          <option>Unfurnished</option>
                        </select>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label>Owner Email</label>
                        <input type="email" name="owner_email" id="edit_owner_email" class="form-control">
                      </div>
                      <div class="col-md-6 mb-3">
                        <label>Owner Phone</label>
                        <input type="text" name="owner_phone" id="edit_owner_phone" class="form-control">
                      </div>
                    </div>

                    <div class="mb-3">
                      <label>Amenities</label>
                      <input type="text" name="amenities" id="edit_amenities" class="form-control" placeholder="Parking, Lift, Security, Swimming Pool, Garden, Clubhouse">
                      <small class="text-muted">Comma-separated — each one shows as a chip on the project page.</small>
                    </div>

                    <div class="mb-3">
                      <label>Appointment Date</label>
                      <input type="date" name="appointment_date" id="edit_appointment_date" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Replace Image (optional)</label>
                        <input
                            type="file"
                            name="image"
                            class="form-control"
                            accept="image/*">
                        <small class="text-muted">Leave empty to keep the current image.</small>
                    </div>

                    <div class="mb-3">
                        <label>Gallery Images</label>
                        <div id="edit_gallery_preview" class="d-flex flex-wrap gap-2 mb-2"></div>
                        <input type="file" name="gallery_images[]" class="form-control" accept="image/*" multiple>
                        <small class="text-muted">Select more images to add them to the gallery — existing ones above are untouched unless you delete them.</small>
                    </div>

                    <!-- ===== FLOOR PLANS MANAGEMENT (EDIT MODAL) ===== -->
                    <?php if($hasFloorPlanTable): ?>
                    <div class="mb-3">
                        <label class="fw-bold">Floor Plans &amp; Pricing</label>
                        <div id="editFloorPlansContainer">
                            <!-- Existing floor plans will be loaded here via JavaScript -->
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addFloorPlanRow('editFloorPlansContainer')">
                            <i class="bi bi-plus-circle"></i> Add Floor Plan
                        </button>
                        <small class="text-muted d-block">Manage floor plans for each BHK type (2 BHK, 3 BHK, etc.)</small>
                    </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label>Property Order</label>
                        <input
                            type="number"
                            name="project_order"
                            id="edit_project_order"
                            class="form-control"
                            required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update" class="btn" style="background-color: #ff2d7a; border-color: #ff2d7a; color: #fff; border-radius: 20px;">Update Project</button>
                </div>

                </form>

            </div>
        </div>
    </div>

    <?php include __DIR__ . '/includes/ui_footer.php'; ?>

    <script>
    // Simple client-side slugify — mirrors the PHP slugify() for a live preview only;
    // the server always regenerates/verifies the final slug on save.
    function jsSlugify(text){
        return text.toLowerCase().trim()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    // Live-update the slug preview on the Add Project form as the name is typed,
    // but only while the admin hasn't typed a custom slug themselves.
    (function(){
        const nameInput = document.getElementById('add_project_name');
        const slugInput = document.getElementById('add_slug');
        let slugTouched = false;

        if(slugInput){
            slugInput.addEventListener('input', function(){ slugTouched = true; });
        }
        if(nameInput){
            nameInput.addEventListener('input', function(){
                const suggested = jsSlugify(this.value);
                if(!slugTouched){ slugInput.value = suggested; }
            });
        }
    })();

    // ===== FLOOR PLANS MANAGEMENT =====
    let floorPlanCounter = 0;

    function addFloorPlanRow(containerId, data) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        const row = document.createElement('div');
        row.className = 'floor-plan-row';
        row.dataset.index = floorPlanCounter++;

        const bhk = data?.bhk_type || '';
        const area_sqft = data?.carpet_area_sqft || '';
        const area_sqm = data?.carpet_area_sqm || '';
        const price = data?.price || '';
        const price_range = data?.price_range || '';
        const status = data?.status || 'Under Construction';
        const possession = data?.possession_date || '';

        row.innerHTML = `
            <button type="button" class="btn btn-sm btn-danger btn-remove-fp" onclick="this.closest('.floor-plan-row').remove()">
                <i class="bi bi-x"></i>
            </button>
            <div class="row g-2">
                <div class="col-md-3">
                    <input type="text" name="floor_plan_bhk[]" class="form-control form-control-sm" placeholder="BHK Type" value="${bhk}">
                </div>
                <div class="col-md-2">
                    <input type="text" name="floor_plan_area_sqft[]" class="form-control form-control-sm" placeholder="Area sqft" value="${area_sqft}">
                </div>
                <div class="col-md-2">
                    <input type="text" name="floor_plan_area_sqm[]" class="form-control form-control-sm" placeholder="Area sqm" value="${area_sqm}">
                </div>
                <div class="col-md-2">
                    <input type="text" name="floor_plan_price[]" class="form-control form-control-sm" placeholder="Price" value="${price}">
                </div>
                <div class="col-md-2">
                    <input type="text" name="floor_plan_price_range[]" class="form-control form-control-sm" placeholder="Price Range" value="${price_range}">
                </div>
                <div class="col-md-2">
                    <input type="text" name="floor_plan_status[]" class="form-control form-control-sm" placeholder="Status" value="${status}">
                </div>
                <div class="col-md-2">
                    <input type="text" name="floor_plan_possession[]" class="form-control form-control-sm" placeholder="Possession" value="${possession}">
                </div>
            </div>
        `;
        container.appendChild(row);
    }

    // ===== ADD MODAL: Initialize with one empty floor plan row =====
    document.addEventListener('DOMContentLoaded', function() {
        // Add one empty row in the add modal
        const addContainer = document.getElementById('addFloorPlansContainer');
        if (addContainer) {
            addFloorPlanRow('addFloorPlansContainer', null);
        }
    });

    // ===== EDIT MODAL: Load floor plans when edit button is clicked =====
    document.querySelectorAll('.edit-btn').forEach(function(btn){
        btn.addEventListener('click', function(){
            // Existing fields
            document.getElementById('edit_project_id').value = this.dataset.id;
            document.getElementById('edit_existing_image').value = this.dataset.image;
            document.getElementById('edit_project_name').value = this.dataset.projectname;
            document.getElementById('edit_category_id').value = this.dataset.category;
            document.getElementById('edit_slug').value = this.dataset.slug;
            document.getElementById('edit_address').value = this.dataset.address;
            document.getElementById('edit_price').value = this.dataset.price;
            document.getElementById('edit_project_order').value = this.dataset.order;
            
            // Set current image
            const currentImage = document.getElementById('edit_current_image');
            if (this.dataset.image) {
                currentImage.src = 'uploads/trending-projects/' + this.dataset.image;
                currentImage.style.display = 'inline-block';
            } else {
                currentImage.src = '';
                currentImage.style.display = 'none';
            }

            document.getElementById('edit_bedrooms').value = this.dataset.bedrooms;
            document.getElementById('edit_bathrooms').value = this.dataset.bathrooms;
            document.getElementById('edit_area').value = this.dataset.area;
            document.getElementById('edit_year_built').value = this.dataset.yearbuilt;
            document.getElementById('edit_furnished').value = this.dataset.furnished;
            document.getElementById('edit_plot_area').value = this.dataset.plotarea;
            document.getElementById('edit_amenities').value = this.dataset.amenities;
            document.getElementById('edit_owner_email').value = this.dataset.owneremail;
            document.getElementById('edit_owner_phone').value = this.dataset.ownerphone;
            document.getElementById('edit_appointment_date').value = this.dataset.appointmentdate;

            // Gallery images
            const galleryPreview = document.getElementById('edit_gallery_preview');
            galleryPreview.innerHTML = '';
            let gallery = [];
            try { gallery = JSON.parse(this.dataset.gallery || '[]'); } catch (e) { gallery = []; }

            gallery.forEach(function(img){
                const wrap = document.createElement('div');
                wrap.style.position = 'relative';
                wrap.style.display = 'inline-block';

                const thumb = document.createElement('img');
                thumb.src = 'uploads/trending-projects/' + img.image;
                thumb.style.height = '70px';
                thumb.style.width = '90px';
                thumb.style.objectFit = 'cover';
                thumb.style.borderRadius = '6px';
                thumb.style.border = '1px solid #dee2e6';

                const delBtn = document.createElement('a');
                delBtn.href = 'trending-projects.php?delete_gallery_image=' + img.id + '&csrf_token=<?php echo urlencode(csrf_token()); ?>';
                delBtn.className = 'btn btn-sm btn-danger';
                delBtn.style.position = 'absolute';
                delBtn.style.top = '-8px';
                delBtn.style.right = '-8px';
                delBtn.style.padding = '0 6px';
                delBtn.style.lineHeight = '18px';
                delBtn.style.borderRadius = '50%';
                delBtn.innerHTML = '&times;';
                delBtn.onclick = function(){ return confirm('Remove this gallery image?'); };

                wrap.appendChild(thumb);
                wrap.appendChild(delBtn);
                galleryPreview.appendChild(wrap);
            });

            if (gallery.length === 0) {
                galleryPreview.innerHTML = '<span class="text-muted small">No gallery images yet.</span>';
            }

            // ===== LOAD FLOOR PLANS IN EDIT MODAL =====
            const container = document.getElementById('editFloorPlansContainer');
            if (container) {
                container.innerHTML = '';
                floorPlanCounter = 0;

                let floorPlans = [];
                try { floorPlans = JSON.parse(this.dataset.floorplans || '[]'); } catch (e) { floorPlans = []; }

                if (floorPlans.length > 0) {
                    floorPlans.forEach(function(fp) {
                        addFloorPlanRow('editFloorPlansContainer', fp);
                    });
                } else {
                    // Add one empty row by default
                    addFloorPlanRow('editFloorPlansContainer', null);
                }
            }
        });
    });
    </script>
</body>
</html>