<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/config.php';

// ===============================
// Fetch the full category tree for the Listing Category selects —
// Vertical (Buy/Rent/Vendors/Local Services/Interiors/Hotels & Homestays/
// PG & Hostels) -> Section -> Item, same taxonomy used site-wide.
// ===============================
$categoryVerticals = [];
$allCatsFlat = [];
$catTreeRes = $conn->query("SELECT id, parent_id, category_type, name FROM property_categories WHERE status='Active' ORDER BY category_order ASC, id ASC");
if ($catTreeRes) {
    while ($row = $catTreeRes->fetch_assoc()) {
        $allCatsFlat[$row['id']] = $row;
    }
}
$catChildrenByParent = [];
foreach ($allCatsFlat as $cat) {
    $catChildrenByParent[$cat['parent_id']][] = $cat;
}
function build_sell_category_tree($parentId, array $childrenByParent) {
    $nodes = [];
    foreach (($childrenByParent[$parentId] ?? []) as $cat) {
        $nodes[] = [
            'id' => (int) $cat['id'],
            'name' => $cat['name'],
            'children' => build_sell_category_tree($cat['id'], $childrenByParent),
        ];
    }
    return $nodes;
}
foreach (($catChildrenByParent[0] ?? []) as $vertical) {
    $categoryVerticals[] = [
        'id' => (int) $vertical['id'],
        'type' => $vertical['category_type'],
        'name' => $vertical['name'],
        'children' => build_sell_category_tree($vertical['id'], $catChildrenByParent),
    ];
}

$google_api_key = 'YOUR_GOOGLE_PLACES_API_KEY'; // Get from Google Cloud Console

// ---- Handles the property_images[] file upload ----
// Returns an array of relative paths (e.g. "uploads/properties/xxxx.jpg") on success.
// On any file's failure, adds an error to $upload_errors and skips that file.
function handle_property_image_uploads(array $files, array &$upload_errors): array {
    $saved = [];

    if (empty($files['name'][0])) {
        return $saved; // no files selected — allowed, photos are optional
    }

    $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
    $max_size = 5 * 1024 * 1024; // 5MB per image
    // sell-property.php lives in /front, uploads folder lives in /backend
    // adjust this if your actual folder layout differs
    $upload_dir = __DIR__ . '/../admin/uploads/trending-projects/';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $count = count($files['name']);
    for ($i = 0; $i < $count; $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        if ($files['error'][$i] !== UPLOAD_ERR_OK) {
            $upload_errors[] = 'Error uploading ' . htmlspecialchars($files['name'][$i]) . '.';
            continue;
        }
        if ($files['size'][$i] > $max_size) {
            $upload_errors[] = htmlspecialchars($files['name'][$i]) . ' is larger than 5MB.';
            continue;
        }

        $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_ext, true)) {
            $upload_errors[] = htmlspecialchars($files['name'][$i]) . ' is not a supported image type.';
            continue;
        }

        // Matches existing filename style: project_6a76c97fc76e05.16820444.jpeg
        $new_name = 'project_' . uniqid('', true) . '.' . $ext;
        $dest = $upload_dir . $new_name;

        if (move_uploaded_file($files['tmp_name'][$i], $dest)) {
            $saved[] = $new_name; // store filename only, not a path
        } else {
            $upload_errors[] = 'Failed to save ' . htmlspecialchars($files['name'][$i]) . '.';
        }
    }

    return $saved;
}

// Handle form submission
$submit_success = false;
$form_errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_property'])) {

    // Reject if token missing/mismatched (stale or resubmitted form)
    if (empty($_POST['_token']) || empty($_SESSION['post_property_token']) || !hash_equals($_SESSION['post_property_token'], $_POST['_token'])) {
        header('Location: sell-property.php?already=1');
        exit;
    }

    // Basic validation

    //$required = ['property_title', 'for_sale_rent', 'property_type', 'price', 'area', 'bedrooms', 'bathrooms', 'furnished_status', 'description', 'country', 'state', 'city', 'pincode', 'locality', 'full_address', 'package'];
    $required = ['property_title', 'category_id', 'price', 'area', 'bedrooms', 'bathrooms', 'furnished_status', 'description', 'country', 'state', 'city', 'pincode', 'locality', 'full_address', 'package'];
    foreach ($required as $field) {
        if (empty(trim($_POST[$field] ?? ''))) {
            $form_errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        }
    }

    if (empty($form_errors)) {
        // ---- Fields that map directly to a column in trending_projects ----
        // form field          -> trending_projects column
        // property_title      -> project_name
        // for_sale_rent       -> category_id
        // full_address        -> address
        // city                -> city
        // price               -> price
        // bedrooms            -> bedrooms
        // bathrooms           -> bathrooms
        // area                -> area
        // furnished_status    -> furnished
        // package             -> package_id (resolved below via listing_packages)
        //$project_name = $_POST['property_title'];
        //$category_id = ($_POST['for_sale_rent'] == 'sale') ? 1 : 2;
        $project_name = $_POST['property_title'];
        $category_id = (int) ($_POST['category_id'] ?? 0);
        // Look up the vertical's category_type (buy/rent/vendor/local_services/
        // interior/hotels_homestays/pg_hostels) from the category tree already
        // fetched above, so trending_projects.category_type stays in sync with
        // whichever category the user actually picked.
        $category_type = $allCatsFlat[$category_id]['category_type'] ?? null;
        
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $project_name)));
        $address = $_POST['full_address'];
        $city = $_POST['city'];
        $price = $_POST['price'];
        $bedrooms = $_POST['bedrooms'];
        $bathrooms = $_POST['bathrooms'];
        $area = $_POST['area'];
        $year_built = !empty($_POST['year_built']) ? (int)$_POST['year_built'] : date('Y');
        $furnished = $_POST['furnished_status'];
        $plot_area = $_POST['plot_area'] ?? null;
        $amenities = $_POST['amenities'] ?? '';
        $owner_email = $_POST['owner_email'] ?? '';
        $owner_phone = $_POST['owner_phone'] ?? '';
        $appointment_date = $_POST['appointment_date'] ?? null;
        $image = ''; // default; overwritten below if a file was uploaded
        $project_order = 0;
        $status = 1;

        // ---- Fields with NO matching column in trending_projects ----
        // These do NOT alter trending_projects. They go into a separate
        // property_extra_details table, linked back via project_id.
        // form field       -> property_extra_details column
        // property_type    -> property_type
        // description      -> description
        // country          -> country
        // state            -> state
        // pincode          -> pincode
        // locality         -> locality
        $property_type = $_POST['property_type'];
        $description = $_POST['description'];
        $country = $_POST['country'];
        $state = $_POST['state'];
        $pincode = $_POST['pincode'];
        $locality = $_POST['locality'];

        // Resolve the chosen package (free/standard/premium) to its listing_packages.id
        $package_id = null;
        $package_key = $_POST['package'];
        $pkg_stmt = $conn->prepare("SELECT id FROM listing_packages WHERE package_key = ? AND is_active = 1 LIMIT 1");
        $pkg_stmt->bind_param("s", $package_key);
        $pkg_stmt->execute();
        $pkg_result = $pkg_stmt->get_result();
        if ($pkg_row = $pkg_result->fetch_assoc()) {
            $package_id = (int) $pkg_row['id'];
        }
        $pkg_stmt->close();

        if ($package_id === null) {
            $form_errors[] = 'Selected package is invalid.';
        }

        // Handle image uploads now, before the DB transaction starts
        $upload_errors = [];
        $uploaded_images = handle_property_image_uploads($_FILES['property_images'] ?? [], $upload_errors);
        if (!empty($upload_errors)) {
            $form_errors = array_merge($form_errors, $upload_errors);
        }
        // First uploaded image becomes the legacy `image` column value on trending_projects
        if (!empty($uploaded_images)) {
            $image = $uploaded_images[0];
        }
    }

    if (empty($form_errors)) {
        $conn->begin_transaction();
        $db_error = null;

        // 1) Insert the trending_projects row (unchanged table/columns)
                $stmt = $conn->prepare("INSERT INTO trending_projects 
            (project_name, category_id, category_type, slug, address, city, price, bedrooms, bathrooms, area, year_built, furnished, plot_area, amenities, owner_email, owner_phone, appointment_date, image, project_order, status, package_id, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");

        $stmt->bind_param(
            "sissssssisssssssssiii",
            $project_name,
            $category_id,
            $category_type,
            $slug,
            $address,
            $city,
            $price,
            $bedrooms,
            $bathrooms,
            $area,
            $year_built,
            $furnished,
            $plot_area,
            $amenities,
            $owner_email,
            $owner_phone,
            $appointment_date,
            $image,
            $project_order,
            $status,
            $package_id
        );

        if ($stmt->execute()) {
            $project_id = $conn->insert_id;
            $stmt->close();

            // 2) Insert the fields that have no home in trending_projects
            //    into the new property_extra_details table, linked by project_id.
            $extra_stmt = $conn->prepare("INSERT INTO property_extra_details 
                (project_id, property_type, description, country, state, pincode, locality, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");

            $extra_stmt->bind_param(
                "issssss",
                $project_id,
                $property_type,
                $description,
                $country,
                $state,
                $pincode,
                $locality
            );

            if ($extra_stmt->execute()) {
                $extra_stmt->close();
            } else {
                $db_error = 'property_extra_details insert failed: ' . $extra_stmt->error;
                $extra_stmt->close();
            }

            // 3) Insert every uploaded image into trending_project_images,
            //    preserving upload order via image_order.
            if ($db_error === null && !empty($uploaded_images)) {
                $img_stmt = $conn->prepare("INSERT INTO trending_project_images 
                    (project_id, image, image_order, created_at) 
                    VALUES (?, ?, ?, NOW())");

                foreach ($uploaded_images as $order => $img_path) {
                    $img_stmt->bind_param("isi", $project_id, $img_path, $order);
                    if (!$img_stmt->execute()) {
                        $db_error = 'trending_project_images insert failed: ' . $img_stmt->error;
                        break;
                    }
                }
                $img_stmt->close();
            }
        } else {
            $db_error = 'trending_projects insert failed: ' . $stmt->error;
            $stmt->close();
        }

        if ($db_error === null) {
            $conn->commit();
            unset($_SESSION['post_property_token']); // one-time use — invalidate so refresh/back can't resubmit
            header('Location: sell-property.php?success=1');
            exit;
        } else {
            $conn->rollback();
            // Remove any files we already saved to disk, since the DB rows didn't stick
            foreach ($uploaded_images as $img_name) {
                $full_path = __DIR__ . '/../admin/uploads/trending-projects/' . $img_name;
                if (is_file($full_path)) {
                    @unlink($full_path);
                }
            }
            $form_errors[] = 'Database error: ' . $db_error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($site['name']); ?> — Post Your Property</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo $site['favicon']; ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $site['favicon']; ?>">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
    <!-- Site styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Google Places API (used only for the Full Address field now) -->
    <!--
        IMPORTANT: initAutocomplete must be defined BEFORE this script tag loads,
        otherwise the async callback can fire before the function exists
        ("initAutocomplete is not a function"). Keep this order.
    -->
    <script>
        let autocomplete;

        function initAutocomplete() {
            const addressInput = document.getElementById('autocomplete');
            if (!addressInput) return;

            const options = {
                types: ['address'],
                componentRestrictions: { country: 'in' }
            };

            autocomplete = new google.maps.places.Autocomplete(addressInput, options);
            autocomplete.setFields(['address_components', 'formatted_address']);
            autocomplete.addListener('place_changed', fillInAddress);
        }

        function selectOptionIfExists(selectEl, value) {
            if (!value) return false;
            for (const opt of selectEl.options) {
                if (opt.value.toLowerCase() === value.toLowerCase()) {
                    selectEl.value = opt.value;
                    return true;
                }
            }
            return false;
        }

        function fillInAddress() {
            const place = autocomplete.getPlace();

            if (!place.address_components) {
                return;
            }

            const pincodeEl = document.getElementById('pincode');
            const localityEl = document.getElementById('locality');
            pincodeEl.value = '';
            localityEl.value = '';

            let matchedState = '';
            let matchedCity = '';

            for (const component of place.address_components) {
                const types = component.types;

                if (types.includes('administrative_area_level_1')) {
                    matchedState = component.long_name;
                }
                if (types.includes('locality') || types.includes('sublocality_level_1') || types.includes('sublocality')) {
                    if (!matchedCity) matchedCity = component.long_name;
                }
                if (types.includes('postal_code')) {
                    pincodeEl.value = component.long_name;
                }
                if (types.includes('sublocality_level_2') || types.includes('neighborhood')) {
                    if (!localityEl.value) {
                        localityEl.value = component.long_name;
                    }
                }
            }

            // Try to select the matching State option, then populate/select City.
            // populateCityDropdown() is defined later in the page's own script block,
            // but by the time a user actually picks an address (place_changed fires
            // after user interaction) the whole page has finished loading, so it's
            // safe to reference it here.
            const stateSelect = document.getElementById('state');
            if (stateSelect && selectOptionIfExists(stateSelect, matchedState)) {
                populateCityDropdown(stateSelect.value, matchedCity);
            }

            if (!localityEl.value && matchedCity) {
                localityEl.value = matchedCity;
            }

            document.getElementById('autocomplete').value = place.formatted_address;
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_api_key; ?>&libraries=places&loading=async&callback=initAutocomplete" async defer></script>
    <style>
        /* ============================================
           DARK MODE SUPPORT - Inherit from header
           ============================================ */
        body.dark-mode {
            --bg-body: #0d1117;
            --bg-card: #161b22;
            --bg-input: #1c2333;
            --border-light: #30363d;
            --text-primary: #f0f6fc;
            --text-muted: #8b949e;
            --shadow-card: 0 12px 28px rgba(0, 0, 0, 0.5);
            --accent-blue: #58a6ff;
            --accent-hover: #79c0ff;
            --badge-free: #1a3350;
            --badge-text: #58a6ff;
            --package-bg: #1c2333;
            --package-border: #30363d;
            background: #0d1117;
            color: #f0f6fc;
        }

        body.dark-mode .card {
            background: var(--bg-card, #161b22);
            border-color: var(--border-light, #30363d);
        }

        body.dark-mode .card h2 {
            color: var(--text-primary, #f0f6fc);
        }

        body.dark-mode .form-group label {
            color: var(--text-primary, #f0f6fc);
        }

        body.dark-mode .form-group input,
        body.dark-mode .form-group select,
        body.dark-mode .form-group textarea {
            background: var(--bg-input, #1c2333);
            border-color: var(--border-light, #30363d);
            color: var(--text-primary, #f0f6fc);
        }

        body.dark-mode .form-group input::placeholder,
        body.dark-mode .form-group textarea::placeholder {
            color: var(--text-muted, #8b949e);
        }

        body.dark-mode .form-group input:focus,
        body.dark-mode .form-group select:focus,
        body.dark-mode .form-group textarea:focus {
            outline-color: var(--accent-blue, #58a6ff);
        }

        body.dark-mode .photo-upload-area {
            background: var(--bg-input, #1c2333);
            border-color: var(--border-light, #30363d);
            color: var(--text-muted, #8b949e);
        }

        body.dark-mode .photo-upload-area i {
            color: var(--accent-blue, #58a6ff);
        }

        body.dark-mode .package-item {
            background: var(--package-bg, #1c2333);
            border-color: var(--package-border, #30363d);
        }

        body.dark-mode .package-item .pkg-name {
            color: var(--text-primary, #f0f6fc);
        }

        body.dark-mode .package-item .pkg-price {
            color: var(--accent-blue, #58a6ff);
        }

        body.dark-mode .package-item .pkg-duration {
            color: var(--text-muted, #8b949e);
        }

        body.dark-mode .package-item .pkg-features {
            color: var(--text-muted, #8b949e);
        }

        body.dark-mode .package-item .badge-free {
            background: var(--badge-free, #1a3350);
            color: var(--badge-text, #58a6ff);
        }

        body.dark-mode .selected-package-info {
            background: var(--bg-input, #1c2333);
            border-color: var(--border-light, #30363d);
            color: var(--text-primary, #f0f6fc);
        }

        body.dark-mode .sub-headline {
            color: var(--text-muted, #8b949e);
        }

        body.dark-mode .page-title {
            color: var(--text-primary, #f0f6fc);
        }

        body.dark-mode .form-hint {
            color: var(--text-muted, #8b949e);
        }

        body.dark-mode .alert-success {
            background: var(--bg-card, #161b22);
            border-color: #238636;
            color: #3fb950;
        }

        body.dark-mode .alert-danger {
            background: var(--bg-card, #161b22);
            border-color: #da3633;
            color: #f85149;
        }

        body.dark-mode .btn-submit {
            background: var(--accent-blue, #58a6ff);
            color: #0d1117;
        }

        body.dark-mode .btn-submit:hover {
            background: var(--accent-hover, #79c0ff);
        }

        body.dark-mode .theme-toggle {
            background: var(--bg-input, #1c2333);
            border-color: var(--border-light, #30363d);
            color: var(--text-primary, #f0f6fc);
        }

        body.dark-mode .theme-toggle:hover {
            background: var(--border-light, #30363d);
        }

        body.dark-mode .form-group select option {
            background: var(--bg-input, #1c2333);
            color: var(--text-primary, #f0f6fc);
        }

        /* ============================================
           ORIGINAL STYLES
           ============================================ */
        .post-property-wrapper {
            max-width: 960px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }
        .page-title {
            font-size: 1.9rem;
            font-weight: 700;
            color: var(--text-primary, #1b2a3f);
            margin-bottom: 0.3rem;
            transition: color 0.3s ease;
        }
        .sub-headline {
            color: var(--text-muted, #4a5b6e);
            margin-bottom: 2rem;
            font-size: 1rem;
            border-left: 4px solid var(--accent-blue, #1e6bff);
            padding-left: 1rem;
            transition: color 0.3s ease;
        }
        .card {
            background: var(--bg-card, #ffffff);
            border-radius: 24px;
            box-shadow: var(--shadow-card, 0 12px 30px rgba(0, 20, 40, 0.06));
            padding: 1.8rem 2rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-light, #dce3ed);
            transition: background 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .card h2 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1.4rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-primary, #1b2a3f);
            transition: color 0.3s ease;
        }
        .card h2 i {
            color: var(--accent-blue, #1e6bff);
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.2rem 1.8rem;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        .form-group label {
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--text-primary, #1b2a3f);
            transition: color 0.3s ease;
        }
        .form-group label .hint {
            font-weight: 400;
            color: var(--text-muted, #4a5b6e);
            font-size: 0.8rem;
            transition: color 0.3s ease;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            background: var(--bg-input, #f0f3f8);
            border: 1px solid var(--border-light, #dce3ed);
            border-radius: 14px;
            padding: 0.7rem 1rem;
            font-size: 0.95rem;
            color: var(--text-primary, #1b2a3f);
            transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;
            width: 100%;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: 2px solid var(--accent-blue, #1e6bff);
            outline-offset: 2px;
            border-color: transparent;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 90px;
        }
        .form-group select:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .photo-upload-area {
            background: var(--bg-input, #f0f3f8);
            border: 2px dashed var(--border-light, #dce3ed);
            border-radius: 20px;
            padding: 2rem 1rem;
            text-align: center;
            color: var(--text-muted, #4a5b6e);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }
        .photo-upload-area i {
            font-size: 2.2rem;
            color: var(--accent-blue, #1e6bff);
            transition: color 0.3s ease;
        }
        .package-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin: 1rem 0 0.8rem;
        }
        .package-item {
            background: var(--package-bg, #f8faff);
            border: 1px solid var(--package-border, #e6edf8);
            border-radius: 20px;
            padding: 1.2rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
            position: relative;
            transition: background 0.3s ease, border-color 0.3s ease;
            cursor: pointer;
        }
        .package-item .pkg-name {
            font-weight: 600;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }
        .package-item .pkg-price {
            font-weight: 700;
            color: var(--accent-blue, #1e6bff);
            transition: color 0.3s ease;
        }
        .package-item .pkg-duration {
            font-size: 0.8rem;
            color: var(--text-muted, #4a5b6e);
            transition: color 0.3s ease;
        }
        .package-item .pkg-features {
            font-size: 0.8rem;
            color: var(--text-muted, #4a5b6e);
            list-style: none;
            padding: 0;
            margin: 0.3rem 0;
            transition: color 0.3s ease;
        }
        .package-item .pkg-features li {
            padding: 0.1rem 0;
        }
        .package-item .badge-free {
            background: var(--badge-free, #e7f0ff);
            color: var(--badge-text, #1e6bff);
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.15rem 0.6rem;
            border-radius: 30px;
            display: inline-block;
            width: fit-content;
            transition: background 0.3s ease, color 0.3s ease;
        }
        .package-item input[type="radio"] {
            accent-color: var(--accent-blue, #1e6bff);
            width: 1rem;
            height: 1rem;
            margin-right: 0.3rem;
        }
        .selected-package-info {
            background: var(--bg-input, #f0f3f8);
            padding: 0.5rem 1.2rem;
            border-radius: 40px;
            display: inline-block;
            border: 1px solid var(--border-light, #dce3ed);
            font-weight: 500;
            font-size: 0.95rem;
            transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }
        .btn-submit {
            background: var(--accent-blue, #1e6bff);
            border: none;
            border-radius: 40px;
            padding: 0.9rem 2.4rem;
            font-weight: 600;
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            box-shadow: 0 6px 14px rgba(30, 107, 255, 0.25);
        }
        .btn-submit:hover {
            background: var(--accent-hover, #1351d0);
            transform: scale(1.01);
        }
        .flex-center {
            display: flex;
            justify-content: center;
            margin-top: 0.5rem;
        }
        .form-hint {
            font-size: 0.8rem;
            color: var(--text-muted, #4a5b6e);
            margin-top: 0.2rem;
            transition: color 0.3s ease;
        }
        .alert-custom {
            border-radius: 16px;
            padding: 1rem 1.5rem;
            transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }
        .theme-toggle {
            background: var(--bg-input, #f0f3f8);
            border: 1px solid var(--border-light, #dce3ed);
            border-radius: 40px;
            padding: 0.4rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            cursor: pointer;
            color: var(--text-primary, #1b2a3f);
            transition: background 0.3s ease, border-color 0.3s ease, color 0.3s ease;
            font-size: 0.9rem;
        }
        .theme-toggle:hover {
            background: var(--border-light, #dce3ed);
        }
        .theme-toggle i {
            font-size: 1rem;
        }
        body {
            background: var(--bg-body, #f4f7fc);
            color: var(--text-primary, #1b2a3f);
            transition: background 0.3s ease, color 0.3s ease;
            line-height: 1.5;
        }
        
        /* Google Places Autocomplete Styling (Full Address field only) */
        .pac-container {
            border-radius: 14px;
            border: 1px solid var(--border-light, #dce3ed);
            box-shadow: var(--shadow-card, 0 12px 30px rgba(0, 20, 40, 0.06));
            font-family: 'Inter', system-ui, sans-serif;
        }
        .pac-item {
            padding: 8px 12px;
            color: var(--text-primary, #1b2a3f);
            transition: background 0.2s ease;
        }
        .pac-item:hover {
            background: var(--bg-input, #f0f3f8);
        }
        .pac-item-query {
            font-weight: 500;
        }
        .pac-matched {
            font-weight: 600;
        }
        
        @media (max-width: 650px) {
            .form-grid { grid-template-columns: 1fr; }
            .package-grid { grid-template-columns: 1fr 1fr; }
            .post-property-wrapper { padding: 0 1rem; }
            .card { padding: 1.2rem 1rem; }
            .page-title { font-size: 1.5rem; }
        }
        @media (max-width: 460px) {
            .package-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="post-property-wrapper">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
        <div>
            <h1 class="page-title"><i class="bi bi-house-fill" style="color: var(--accent-blue, #1e6bff); margin-right: 12px; transition: color 0.3s ease;"></i> Post Your Property</h1>
            <div class="sub-headline">Reach thousands of buyers and tenants across India</div>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-custom" role="alert">
            <i class="bi bi-check-circle-fill"></i> Property submitted successfully! Your listing will appear shortly.
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['already'])): ?>
        <div class="alert alert-danger alert-custom" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i> That submission was already processed or the form expired — please fill it out again.
        </div>
    <?php endif; ?>

    <?php if (!empty($form_errors)): ?>
        <div class="alert alert-danger alert-custom" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <ul class="mb-0 mt-1">
                <?php foreach ($form_errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data" id="propertyForm">
        <input type="hidden" name="submit_property" value="1">
        <?php if (empty($_SESSION['post_property_token'])) { $_SESSION['post_property_token'] = bin2hex(random_bytes(16)); } ?>
        <input type="hidden" name="_token" value="<?php echo $_SESSION['post_property_token']; ?>">

        <!-- 1. Property Details -->
        <section class="card">
            <h2><i class="bi bi-list-ul"></i> 1. Property Details</h2>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Property Title <span class="hint">e.g. 3BHK Apartment in MVP Colony</span></label>
                    <input type="text" name="property_title" placeholder="e.g. 3BHK Apartment in MVP Colony" value="<?php echo htmlspecialchars($_POST['property_title'] ?? '3BHK Apartment in MVP Colony'); ?>" required>
                </div>

                                <div class="form-group">
                    <label>Listing Category</label>
                    <select id="listingVertical" class="form-select" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categoryVerticals as $v): ?>
                            <option value="<?php echo (int) $v['id']; ?>"><?php echo htmlspecialchars($v['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Section</label>
                    <select id="listingSection" class="form-select" required disabled>
                        <option value="">Select Category First</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Type <span class="hint">final property type</span></label>
                    <select id="listingSubcategory" class="form-select" required disabled>
                        <option value="">Select Section First</option>
                    </select>
                </div>

                <input type="hidden" name="category_id" id="categoryIdInput" value="<?php echo htmlspecialchars($_POST['category_id'] ?? ''); ?>">
                <input type="hidden" name="property_type" id="propertyTypeInput" value="<?php echo htmlspecialchars($_POST['property_type'] ?? ''); ?>">

                <div class="form-group">
                    <label>Price (₹) <span class="hint">e.g. 4500000</span></label>
                    <input type="number" name="price" placeholder="4500000" value="<?php echo htmlspecialchars($_POST['price'] ?? '4500000'); ?>" required>
                </div>

                <div class="form-group">
                    <label>Area (sqft) <span class="hint">e.g. 1200</span></label>
                    <input type="number" name="area" placeholder="1200" value="<?php echo htmlspecialchars($_POST['area'] ?? '1200'); ?>" required>
                </div>

                <div class="form-group">
                    <label>Bedrooms</label>
                    <select name="bedrooms" class="form-select" required>
                        <option value="">Select</option>
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo (($_POST['bedrooms'] ?? '2') == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                        <option value="7" <?php echo (($_POST['bedrooms'] ?? '') == '7') ? 'selected' : ''; ?>>7+</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Bathrooms</label>
                    <select name="bathrooms" class="form-select" required>
                        <option value="">Select</option>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo (($_POST['bathrooms'] ?? '2') == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                        <option value="6" <?php echo (($_POST['bathrooms'] ?? '') == '6') ? 'selected' : ''; ?>>6+</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Furnished Status</label>
                    <select name="furnished_status" class="form-select" required>
                        <option value="">Select</option>
                        <option value="Fully Furnished" <?php echo (($_POST['furnished_status'] ?? '') == 'Fully Furnished') ? 'selected' : ''; ?>>Fully Furnished</option>
                        <option value="Semi-Furnished" <?php echo (($_POST['furnished_status'] ?? 'Semi-Furnished') == 'Semi-Furnished') ? 'selected' : ''; ?>>Semi-Furnished</option>
                        <option value="Unfurnished" <?php echo (($_POST['furnished_status'] ?? '') == 'Unfurnished') ? 'selected' : ''; ?>>Unfurnished</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label>Description</label>
                    <textarea name="description" placeholder="Describe your property..." required><?php echo htmlspecialchars($_POST['description'] ?? 'Spacious 3BHK with modern amenities, close to schools and shopping.'); ?></textarea>
                </div>
            </div>
        </section>

        <!-- 2. Location -->
        <section class="card">
            <h2><i class="bi bi-geo-alt"></i> 2. Location</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label>Country</label>
                    <input type="text" name="country" id="country" value="<?php echo htmlspecialchars($_POST['country'] ?? 'India'); ?>" required readonly>
                </div>
                <div class="form-group">
                    <label>State <span class="hint">Choose from list</span></label>
                    <select name="state" id="state" class="form-select" required>
                        <option value="">Select State</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>City <span class="hint">Choose from list</span></label>
                    <select name="city" id="city" class="form-select" required disabled>
                        <option value="">Select State First</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Pincode</label>
                    <input type="text" name="pincode" id="pincode" placeholder="e.g. 530003" value="<?php echo htmlspecialchars($_POST['pincode'] ?? ''); ?>" required>
                </div>
                <div class="form-group full-width">
                    <label>Full Address <span class="hint">Start typing for suggestions</span></label>
                    <input type="text" name="full_address" id="autocomplete" placeholder="Search for your property address..." value="<?php echo htmlspecialchars($_POST['full_address'] ?? ''); ?>" required autocomplete="off">
                </div>
                <div class="form-group full-width">
                    <label>Locality / Area</label>
                    <input type="text" name="locality" id="locality" placeholder="e.g. MVP Colony, Madhurawada" value="<?php echo htmlspecialchars($_POST['locality'] ?? ''); ?>" required>
                </div>
                <!-- extra fields for trending_projects table -->
                <div class="form-group">
                    <label>Year Built</label>
                    <input type="number" name="year_built" placeholder="e.g. 2020" value="<?php echo htmlspecialchars($_POST['year_built'] ?? '2020'); ?>">
                </div>
                <div class="form-group">
                    <label>Plot Area (sqft) <span class="hint">optional</span></label>
                    <input type="text" name="plot_area" placeholder="e.g. 1500" value="<?php echo htmlspecialchars($_POST['plot_area'] ?? ''); ?>">
                </div>
                <div class="form-group full-width">
                    <label>Amenities <span class="hint">comma separated</span></label>
                    <input type="text" name="amenities" placeholder="e.g. Gym, Pool, Parking" value="<?php echo htmlspecialchars($_POST['amenities'] ?? 'Gym, Pool, Security'); ?>">
                </div>
            </div>
        </section>

        <!-- 3. Photos -->
        <section class="card">
            <h2><i class="bi bi-camera"></i> 3. Photos</h2>
            <div class="photo-upload-area">
                <i class="bi bi-cloud-upload"></i>
                <div><strong>Upload Property Photos</strong></div>
                <div>
                    <input type="file" name="property_images[]" multiple accept="image/*">
                </div>
                <div class="form-hint">Upload multiple photos for better visibility</div>
            </div>
        </section>

        <!-- 4. Packages -->
        <section class="card">
            <h2><i class="bi bi-gem"></i> 4. Choose Listing Package</h2>
            <div class="package-grid">
                <label class="package-item">
                    <input type="radio" name="package" value="free" <?php echo (($_POST['package'] ?? 'free') == 'free') ? 'checked' : ''; ?>>
                    <span class="pkg-name">Basic <span class="badge-free">FREE</span></span>
                    <span class="pkg-price">₹0</span>
                    <span class="pkg-duration">30 days</span>
                    <ul class="pkg-features">
                        <li><i class="bi bi-check-circle-fill" style="color:var(--accent-blue,#1e6bff);"></i> 5 photos</li>
                        <li><i class="bi bi-check-circle-fill" style="color:var(--accent-blue,#1e6bff);"></i> Standard visibility</li>
                    </ul>
                </label>
                <label class="package-item">
                    <input type="radio" name="package" value="standard" <?php echo (($_POST['package'] ?? '') == 'standard') ? 'checked' : ''; ?>>
                    <span class="pkg-name">Standard</span>
                    <span class="pkg-price">₹499</span>
                    <span class="pkg-duration">30 days</span>
                    <ul class="pkg-features">
                        <li><i class="bi bi-check-circle-fill" style="color:var(--accent-blue,#1e6bff);"></i> 15 photos</li>
                        <li><i class="bi bi-check-circle-fill" style="color:var(--accent-blue,#1e6bff);"></i> Highlighted</li>
                        <li><i class="bi bi-check-circle-fill" style="color:var(--accent-blue,#1e6bff);"></i> Top search</li>
                    </ul>
                </label>
                <label class="package-item">
                    <input type="radio" name="package" value="premium" <?php echo (($_POST['package'] ?? '') == 'premium') ? 'selected' : ''; ?>>
                    <span class="pkg-name">Premium <span style="background:#fde7d9; color:#b85c2b; font-size:0.7rem; padding:0.1rem 0.6rem; border-radius:30px;">Popular</span></span>
                    <span class="pkg-price">₹999</span>
                    <span class="pkg-duration">45 days</span>
                    <ul class="pkg-features">
                        <li><i class="bi bi-check-circle-fill" style="color:var(--accent-blue,#1e6bff);"></i> 30 photos</li>
                        <li><i class="bi bi-check-circle-fill" style="color:var(--accent-blue,#1e6bff);"></i> Premium badge</li>
                        <li><i class="bi bi-check-circle-fill" style="color:var(--accent-blue,#1e6bff);"></i> Social promotion</li>
                    </ul>
                </label>
            </div>
            <div class="selected-package-info" id="selectedPackageDisplay">
                <i class="bi bi-check-circle-fill" style="color:var(--accent-blue,#1e6bff);"></i> Selected: <strong>Free Package — ₹0</strong>
            </div>
        </section>

        <!-- Contact Info (extra for DB) -->
        <section class="card">
            <h2><i class="bi bi-person"></i> Contact Information</h2>
            <div class="form-grid">
                <div class="form-group"><label>Owner Email</label><input type="email" name="owner_email" placeholder="owner@example.com" value="<?php echo htmlspecialchars($_POST['owner_email'] ?? ''); ?>"></div>
                <div class="form-group"><label>Owner Phone</label><input type="text" name="owner_phone" placeholder="+91 98765 43210" value="<?php echo htmlspecialchars($_POST['owner_phone'] ?? ''); ?>"></div>
                <div class="form-group"><label>Appointment Date <span class="hint">optional</span></label><input type="date" name="appointment_date" value="<?php echo htmlspecialchars($_POST['appointment_date'] ?? ''); ?>"></div>
            </div>
        </section>

        <div class="flex-center">
            <button type="submit" class="btn-submit"><i class="bi bi-send"></i> Submit Property</button>
        </div>
        <p style="text-align:center; margin-top: 0.8rem; color: var(--text-muted, #4a5b6e); font-size:0.85rem; transition: color 0.3s ease;">By submitting you agree to our terms &amp; privacy policy</p>
    </form>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function() {
    // Dark mode toggle - uses body class system like header
    const toggle = document.getElementById('themeToggle');
    const icon = document.getElementById('themeIcon');
    const label = document.getElementById('themeLabel');

    if (!toggle || !icon || !label) {
        console.warn('Theme toggle elements not found on this page — skipping local theme script.');
        return; // bail out early, no crash
    }

    function getCurrentTheme() {
        return document.body.classList.contains('dark-mode') ? 'dark' : 'light';
    }

    function setTheme(theme) {
        if (theme === 'dark') {
            document.body.classList.add('dark-mode');
            icon.className = 'bi bi-sun';
            label.textContent = 'Light';
        } else {
            document.body.classList.remove('dark-mode');
            icon.className = 'bi bi-moon';
            label.textContent = 'Dark';
        }
        localStorage.setItem('theme', theme);
        document.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: theme } }));
    }

    const saved = localStorage.getItem('theme') || 'light';
    setTheme(saved);

    document.addEventListener('themeChanged', function(e) {
        const theme = e.detail.theme || 'light';
        if (theme === 'dark') {
            document.body.classList.add('dark-mode');
            icon.className = 'bi bi-sun';
            label.textContent = 'Light';
        } else {
            document.body.classList.remove('dark-mode');
            icon.className = 'bi bi-moon';
            label.textContent = 'Dark';
        }
    });

    toggle.addEventListener('click', function(e) {
        const current = getCurrentTheme();
        const next = current === 'dark' ? 'light' : 'dark';
        setTheme(next);
    });

    // Package selection update
    const packageRadios = document.querySelectorAll('input[name="package"]');
    const displayDiv = document.getElementById('selectedPackageDisplay');

    function updatePackageDisplay() {
        let selected = document.querySelector('input[name="package"]:checked');
        if (!selected) return;
        const pkgValue = selected.value;
        let labelText = '';
        if (pkgValue === 'free') labelText = 'Free Package — ₹0';
        else if (pkgValue === 'standard') labelText = 'Standard Package — ₹499';
        else if (pkgValue === 'premium') labelText = 'Premium Package — ₹999';
        displayDiv.innerHTML = `<i class="bi bi-check-circle-fill" style="color:var(--accent-blue,#1e6bff);"></i> Selected: <strong>${labelText}</strong>`;
    }

    packageRadios.forEach(radio => {
        radio.addEventListener('change', updatePackageDisplay);
    });
    updatePackageDisplay();
})();

    const CATEGORY_TREE = <?php echo json_encode($categoryVerticals); ?>;

function findCategoryPath(nodes, targetId, path) {
    for (const node of nodes) {
        const newPath = path.concat([node]);
        if (node.id === targetId) return newPath;
        if (node.children && node.children.length) {
            const found = findCategoryPath(node.children, targetId, newPath);
            if (found) return found;
        }
    }
    return null;
}

function populateSelectOptions(selectEl, items, placeholderText) {
    selectEl.innerHTML = '';
    const ph = document.createElement('option');
    ph.value = '';
    ph.textContent = placeholderText;
    selectEl.appendChild(ph);
    items.forEach(function(item) {
        const opt = document.createElement('option');
        opt.value = item.id;
        opt.textContent = item.name;
        selectEl.appendChild(opt);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const verticalSelect = document.getElementById('listingVertical');
    const sectionSelect  = document.getElementById('listingSection');
    const subcatSelect   = document.getElementById('listingSubcategory');
    const categoryIdInput  = document.getElementById('categoryIdInput');
    const propertyTypeInput = document.getElementById('propertyTypeInput');

    function onSubcategoryChange() {
        const id = subcatSelect.value;
        categoryIdInput.value = id;
        const selectedOption = subcatSelect.options[subcatSelect.selectedIndex];
        propertyTypeInput.value = id ? selectedOption.textContent : '';
    }

    function onSectionChange(preselectSubId) {
        const verticalId = parseInt(verticalSelect.value, 10);
        const sectionId   = parseInt(sectionSelect.value, 10);
        const vertical = CATEGORY_TREE.find(v => v.id === verticalId);
        const section  = vertical ? vertical.children.find(s => s.id === sectionId) : null;

        if (section && section.children.length) {
            subcatSelect.disabled = false;
            populateSelectOptions(subcatSelect, section.children, 'Select Type');
            if (preselectSubId) subcatSelect.value = preselectSubId;
        } else if (section) {
            // This section has no further children — it IS the final category
            subcatSelect.disabled = true;
            subcatSelect.innerHTML = '<option value="' + section.id + '">' + section.name + '</option>';
            subcatSelect.value = section.id;
        } else {
            subcatSelect.disabled = true;
            subcatSelect.innerHTML = '<option value="">Select Section First</option>';
        }
        onSubcategoryChange();
    }

    function onVerticalChange(preselectSectionId, preselectSubId) {
        const verticalId = parseInt(verticalSelect.value, 10);
        const vertical = CATEGORY_TREE.find(v => v.id === verticalId);

        if (vertical && vertical.children.length) {
            sectionSelect.disabled = false;
            populateSelectOptions(sectionSelect, vertical.children, 'Select Section');
            if (preselectSectionId) sectionSelect.value = preselectSectionId;
        } else {
            sectionSelect.disabled = true;
            sectionSelect.innerHTML = '<option value="">Select Category First</option>';
        }
        onSectionChange(preselectSubId);
    }

    verticalSelect.addEventListener('change', function() { onVerticalChange(); });
    sectionSelect.addEventListener('change', function() { onSectionChange(); });
    subcatSelect.addEventListener('change', onSubcategoryChange);

    // Re-populate the cascade if the form is redisplayed after a validation error
    const existingCategoryId = parseInt(categoryIdInput.value, 10);
    if (existingCategoryId) {
        const path = findCategoryPath(CATEGORY_TREE, existingCategoryId, []);
        if (path && path.length) {
            const verticalNode = path[0];
            const sectionNode  = path.length > 1 ? path[1] : null;
            const subNode      = path.length > 2 ? path[2] : null;
            verticalSelect.value = verticalNode.id;
            onVerticalChange(sectionNode ? sectionNode.id : null, subNode ? subNode.id : (sectionNode ? sectionNode.id : null));
        }
    }
});
    
// =============================================
// Static India States -> Cities data (no database)
// Edit/extend this list freely.
// =============================================
const INDIA_STATES_CITIES = {
    "Andhra Pradesh": ["Visakhapatnam", "Vijayawada", "Guntur", "Nellore", "Kurnool", "Rajahmundry", "Tirupati", "Kakinada", "Anantapur", "Kadapa"],
    "Arunachal Pradesh": ["Itanagar", "Naharlagun", "Pasighat", "Tawang"],
    "Assam": ["Guwahati", "Silchar", "Dibrugarh", "Jorhat", "Nagaon", "Tezpur"],
    "Bihar": ["Patna", "Gaya", "Bhagalpur", "Muzaffarpur", "Darbhanga", "Purnia"],
    "Chhattisgarh": ["Raipur", "Bhilai", "Bilaspur", "Korba", "Durg"],
    "Goa": ["Panaji", "Margao", "Vasco da Gama", "Mapusa"],
    "Gujarat": ["Ahmedabad", "Surat", "Vadodara", "Rajkot", "Bhavnagar", "Jamnagar", "Gandhinagar"],
    "Haryana": ["Gurugram", "Faridabad", "Panipat", "Ambala", "Karnal", "Hisar"],
    "Himachal Pradesh": ["Shimla", "Manali", "Dharamshala", "Solan", "Mandi"],
    "Jharkhand": ["Ranchi", "Jamshedpur", "Dhanbad", "Bokaro", "Hazaribagh"],
    "Karnataka": ["Bengaluru", "Mysuru", "Mangaluru", "Hubballi", "Belagavi", "Davanagere"],
    "Kerala": ["Thiruvananthapuram", "Kochi", "Kozhikode", "Thrissur", "Kollam", "Kannur"],
    "Madhya Pradesh": ["Bhopal", "Indore", "Jabalpur", "Gwalior", "Ujjain"],
    "Maharashtra": ["Mumbai", "Pune", "Nagpur", "Nashik", "Aurangabad", "Thane", "Solapur"],
    "Manipur": ["Imphal", "Thoubal"],
    "Meghalaya": ["Shillong", "Tura"],
    "Mizoram": ["Aizawl", "Lunglei"],
    "Nagaland": ["Kohima", "Dimapur"],
    "Odisha": ["Bhubaneswar", "Cuttack", "Rourkela", "Berhampur", "Sambalpur"],
    "Punjab": ["Ludhiana", "Amritsar", "Jalandhar", "Patiala", "Mohali", "Bathinda"],
    "Rajasthan": ["Jaipur", "Jodhpur", "Udaipur", "Kota", "Ajmer", "Bikaner"],
    "Sikkim": ["Gangtok", "Namchi"],
    "Tamil Nadu": ["Chennai", "Coimbatore", "Madurai", "Tiruchirappalli", "Salem", "Tirunelveli"],
    "Telangana": ["Hyderabad", "Warangal", "Nizamabad", "Karimnagar", "Khammam"],
    "Tripura": ["Agartala", "Udaipur"],
    "Uttar Pradesh": ["Lucknow", "Kanpur", "Noida", "Ghaziabad", "Agra", "Varanasi", "Meerut", "Prayagraj"],
    "Uttarakhand": ["Dehradun", "Haridwar", "Nainital", "Rishikesh", "Haldwani"],
    "West Bengal": ["Kolkata", "Howrah", "Durgapur", "Asansol", "Siliguri"],
    "Andaman and Nicobar Islands": ["Port Blair"],
    "Chandigarh": ["Chandigarh"],
    "Dadra and Nagar Haveli and Daman and Diu": ["Daman", "Diu", "Silvassa"],
    "Delhi": ["New Delhi", "Dwarka", "Rohini", "Saket", "Karol Bagh"],
    "Jammu and Kashmir": ["Srinagar", "Jammu", "Anantnag"],
    "Ladakh": ["Leh", "Kargil"],
    "Lakshadweep": ["Kavaratti"],
    "Puducherry": ["Puducherry", "Karaikal"]
};

function populateStateDropdown() {
    const stateSelect = document.getElementById('state');
    const currentValue = "<?php echo htmlspecialchars($_POST['state'] ?? '', ENT_QUOTES); ?>";

    Object.keys(INDIA_STATES_CITIES).sort().forEach(function(stateName) {
        const opt = document.createElement('option');
        opt.value = stateName;
        opt.textContent = stateName;
        if (stateName === currentValue) opt.selected = true;
        stateSelect.appendChild(opt);
    });

    populateCityDropdown(currentValue, "<?php echo htmlspecialchars($_POST['city'] ?? '', ENT_QUOTES); ?>");
}

function populateCityDropdown(stateName, preselectCity) {
    const citySelect = document.getElementById('city');
    citySelect.innerHTML = '';

    if (!stateName || !INDIA_STATES_CITIES[stateName]) {
        citySelect.innerHTML = '<option value="">Select State First</option>';
        citySelect.disabled = true;
        return;
    }

    citySelect.disabled = false;
    const placeholder = document.createElement('option');
    placeholder.value = '';
    placeholder.textContent = 'Select City';
    citySelect.appendChild(placeholder);

    INDIA_STATES_CITIES[stateName].forEach(function(cityName) {
        const opt = document.createElement('option');
        opt.value = cityName;
        opt.textContent = cityName;
        if (preselectCity && cityName === preselectCity) opt.selected = true;
        citySelect.appendChild(opt);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    populateStateDropdown();

    document.getElementById('state').addEventListener('change', function() {
        populateCityDropdown(this.value, null);
    });
});

// =============================================
// initAutocomplete / fillInAddress / selectOptionIfExists
// are now defined in <head>, above the Google Maps script
// tag, so the async callback always finds them ready.
// =============================================

// Handle Enter key to prevent form submission from autocomplete
document.addEventListener('DOMContentLoaded', function() {
    const autocompleteInput = document.getElementById('autocomplete');
    autocompleteInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
        }
    });
});
</script>
</body>
</html>