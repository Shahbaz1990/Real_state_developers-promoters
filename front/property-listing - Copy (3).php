<?php
// Start session FIRST - before ANY output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/config.php';

/* Check if user is logged in
if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_email'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to edit this page.']);
    exit;
}*/

// ===============================
// Small helper: turn "Kalpataru Vian" into "kalpataru-vian"
// ===============================
if (!function_exists('slugify')) {
    function slugify($text) {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
    }
}

// Small helper: read a column if it exists on the row, else fall back
function pv($project, $key, $fallback = 'N/A') {
    return (!empty($project[$key])) ? $project[$key] : $fallback;
}

// Small helper: pick a Bootstrap Icon class that best matches an amenity name
if (!function_exists('amenity_icon')) {
    function amenity_icon($name) {
        $name = strtolower(trim($name));
        $map = [
            'power backup'   => 'bi-lightning-charge',
            'backup'         => 'bi-lightning-charge',
            'cctv'           => 'bi-camera-video',
            'video'          => 'bi-camera-video',
            'security'       => 'bi-shield-check',
            'fire'           => 'bi-fire',
            'treated water'  => 'bi-droplet',
            'water sup'      => 'bi-droplet-fill',
            'water'          => 'bi-droplet',
            'lift'           => 'bi-arrow-up-square',
            'elevator'       => 'bi-arrow-up-square',
            'intercom'       => 'bi-telephone',
            'kids play'      => 'bi-emoji-smile',
            'play area'      => 'bi-emoji-smile',
            'kids pool'      => 'bi-water',
            'pool'           => 'bi-water',
            'swimming'       => 'bi-water',
            'senior'         => 'bi-people',
            'gym'            => 'bi-heart-pulse',
            'club'           => 'bi-building',
            'garden'         => 'bi-tree',
            'park'           => 'bi-p-square',
            'gas'            => 'bi-fuel-pump',
            'wifi'           => 'bi-wifi',
            'internet'       => 'bi-wifi',
            'jogging'        => 'bi-person-walking',
            'yoga'           => 'bi-person-arms-up',
            'temple'         => 'bi-building',
            'rain'           => 'bi-cloud-rain',
            'solar'          => 'bi-brightness-high',
            'ev charging'    => 'bi-ev-front',
            'sewage'         => 'bi-recycle',
            'waste'          => 'bi-recycle',
            'clubhouse'      => 'bi-building',
            'library'        => 'bi-book',
            'theatre'        => 'bi-camera-reels',
            'cinema'         => 'bi-camera-reels',
        ];
        foreach ($map as $keyword => $icon) {
            if (strpos($name, $keyword) !== false) {
                return $icon;
            }
        }
        return 'bi-check-circle';
    }
}

// ===============================
// Detect whether trending_projects has a real `slug` column
// ===============================
$hasSlugColumn = false;
$colCheck = $conn->query("SHOW COLUMNS FROM trending_projects LIKE 'slug'");
if ($colCheck && $colCheck->num_rows > 0) {
    $hasSlugColumn = true;
}

// ===============================
// Get the requested project
// ===============================
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$id   = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$project = null;

if ($slug !== '' && $hasSlugColumn) {
    $stmt = $conn->prepare("SELECT * FROM trending_projects WHERE slug = ? AND status = 'Active' LIMIT 1");
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $project = $stmt->get_result()->fetch_assoc();
    $stmt->close();
} elseif ($slug !== '') {
    $result = $conn->query("SELECT * FROM trending_projects WHERE status = 'Active'");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if (slugify($row['project_name']) === $slug) {
                $project = $row;
                break;
            }
        }
    }
} elseif ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM trending_projects WHERE id = ? AND status = 'Active' LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $project = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$projectSlug = $project
    ? ($hasSlugColumn && !empty($project['slug']) ? $project['slug'] : slugify($project['project_name']))
    : '';
// ===============================
// Edit Mode - only for logged-in users
// ===============================
$isLoggedIn = isset($_SESSION['user_id']) || isset($_SESSION['user_email']);
//echo '<!-- DEBUG isLoggedIn=' . var_export($isLoggedIn, true) . ' session=' . htmlspecialchars(json_encode($_SESSION ?? [])) . ' -->';
$pageTitle = $project ? $project['project_name'] : 'Project not found';
// Interior listings (Doors, Flooring, Modular Kitchen, etc.) don't have
// RERA numbers, government approvals, construction status, or a carpet
// area range the way real-estate projects do — so those fields are
// hidden entirely for this category, rather than showing empty/fake data.
$isInteriorProject = isset($project['category_type']) && $project['category_type'] === 'interior';
$isVendorProject    = isset($project['category_type']) && $project['category_type'] === 'vendor';
$isHotelProject     = isset($project['category_type']) && $project['category_type'] === 'hotel';

// ===============================
// Fetch gallery images
// ===============================
$galleryImages = [];
if ($project) {
    $hasGalleryTable = false;
    $galTableCheck = $conn->query("SHOW TABLES LIKE 'trending_project_images'");
    if ($galTableCheck && $galTableCheck->num_rows > 0) {
        $hasGalleryTable = true;
    }

    if ($hasGalleryTable) {
        $galStmt = $conn->prepare("SELECT image FROM trending_project_images WHERE project_id = ? ORDER BY image_order ASC");
        $galStmt->bind_param('i', $project['id']);
        $galStmt->execute();
        $galRes = $galStmt->get_result();
        while ($g = $galRes->fetch_assoc()) {
            $galleryImages[] = $g['image'];
        }
        $galStmt->close();
    }
}

// ===============================
// Fetch Floor Plans
// ===============================
$floorPlans = [];
if ($project) {
    $hasFloorPlanTable = false;
    $fpTableCheck = $conn->query("SHOW TABLES LIKE 'floor_plans'");
    if ($fpTableCheck && $fpTableCheck->num_rows > 0) {
        $hasFloorPlanTable = true;
    }

    if ($hasFloorPlanTable) {
        $fpStmt = $conn->prepare("
            SELECT * FROM floor_plans 
            WHERE project_id = ? AND status_active = 1 
            ORDER BY 
                FIELD(bhk_type, '2 BHK Apartment', '2 BHK', '3 BHK Apartment', '3 BHK', '4 BHK Apartment', '4 BHK'),
                display_order ASC
        ");
        $fpStmt->bind_param('i', $project['id']);
        $fpStmt->execute();
        $fpResult = $fpStmt->get_result();
        
        $tempPlans = [];
        while ($row = $fpResult->fetch_assoc()) {
            $bhk = $row['bhk_type'];
            if (!isset($tempPlans[$bhk])) {
                $tempPlans[$bhk] = [
                    'bhk' => $bhk,
                    'variants' => []
                ];
            }
            $tempPlans[$bhk]['variants'][] = $row;
        }
        $floorPlans = array_values($tempPlans);
        $fpStmt->close();
    }
}

// If no floor plans in DB, use sample data
if (empty($floorPlans)) {
    if ($isHotelProject) {
        $floorPlans = [
            [
                'bhk' => 'Classic with Twin Bed',
                'variants' => [
                    ['carpet_area_sqft' => '220', 'carpet_area_sqm' => '20.44', 'price' => '2,499', 'price_range' => NULL, 'status' => 'Available', 'possession_date' => '', 'floor_plan_image' => ''],
                ]
            ],
            [
                'bhk' => 'Deluxe',
                'variants' => [
                    ['carpet_area_sqft' => '280', 'carpet_area_sqm' => '26.01', 'price' => '3,499', 'price_range' => NULL, 'status' => 'Available', 'possession_date' => '', 'floor_plan_image' => ''],
                ]
            ],
            [
                'bhk' => 'Single Room',
                'variants' => [
                    ['carpet_area_sqft' => '180', 'carpet_area_sqm' => '16.72', 'price' => '1,799', 'price_range' => NULL, 'status' => 'Available', 'possession_date' => '', 'floor_plan_image' => ''],
                ]
            ],
            [
                'bhk' => 'Double Room',
                'variants' => [
                    ['carpet_area_sqft' => '240', 'carpet_area_sqm' => '22.30', 'price' => '2,999', 'price_range' => NULL, 'status' => 'Available', 'possession_date' => '', 'floor_plan_image' => ''],
                ]
            ],
        ];
    } else {
        $floorPlans = [
            [
                'bhk' => '2 BHK Apartment',
                'variants' => [
                    ['carpet_area_sqft' => '840 - 855', 'carpet_area_sqm' => '78.04 - 79.43', 'price' => NULL, 'price_range' => '91 L - 1.28 Cr', 'status' => 'Under Construction', 'possession_date' => 'Dec \'26', 'floor_plan_image' => ''],
                    ['carpet_area_sqft' => '837', 'carpet_area_sqm' => '77.76', 'price' => '96 L', 'price_range' => NULL, 'status' => 'Under Construction', 'possession_date' => 'Dec \'26', 'floor_plan_image' => ''],
                    ['carpet_area_sqft' => '880', 'carpet_area_sqm' => '81.75', 'price' => 'Price on request', 'price_range' => NULL, 'status' => 'Under Construction', 'possession_date' => 'Dec \'26', 'floor_plan_image' => ''],
                    ['carpet_area_sqft' => '900 - 920', 'carpet_area_sqm' => '83.61 - 85.47', 'price' => 'Price on request', 'price_range' => NULL, 'status' => 'Under Construction', 'possession_date' => 'Dec \'26', 'floor_plan_image' => ''],
                    ['carpet_area_sqft' => '955', 'carpet_area_sqm' => '88.72', 'price' => 'Price on request', 'price_range' => NULL, 'status' => 'Under Construction', 'possession_date' => 'Dec \'26', 'floor_plan_image' => ''],
                ]
            ],
            [
                'bhk' => '3 BHK Apartment',
                'variants' => [
                    ['carpet_area_sqft' => '1065 - 1120', 'carpet_area_sqm' => '98.94 - 104.05', 'price' => NULL, 'price_range' => '1.2 Cr - 1.8 Cr', 'status' => 'Under Construction', 'possession_date' => 'Dec \'26', 'floor_plan_image' => ''],
                    ['carpet_area_sqft' => '1100', 'carpet_area_sqm' => '102.19', 'price' => '1.5 Cr', 'price_range' => NULL, 'status' => 'Under Construction', 'possession_date' => 'Dec \'26', 'floor_plan_image' => ''],
                    ['carpet_area_sqft' => '1150', 'carpet_area_sqm' => '106.84', 'price' => 'Price on request', 'price_range' => NULL, 'status' => 'Under Construction', 'possession_date' => 'Dec \'26', 'floor_plan_image' => ''],
                ]
            ]
        ];
    }
}

$imagePath = !empty($project['image'])
    ? "../../../admin/uploads/trending-projects/" . $project['image']
    : "/front/assets/images/no-image.jpg";

// ===============================
// Resolve map coordinates from the address
// ===============================
$lat = null;
$lng = null;

if (!empty($project['lat']) && !empty($project['lng'])) {
    // Already geocoded and cached — use it, no network call needed
    $lat = (float) $project['lat'];
    $lng = (float) $project['lng'];
} elseif (!empty($project['address'])) {
    // No cached coords yet — geocode the address now
    $geocodeUrl = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q='
        . urlencode($project['address']);

    $geoContext = stream_context_create([
        'http' => [
            'header'  => "User-Agent: TatitoDevelopersPromoters/1.0 (contact@tatitodevelopersandpromoters.com)\r\n",
            'timeout' => 6,
        ],
    ]);

    $geoResp = @file_get_contents($geocodeUrl, false, $geoContext);
    if ($geoResp !== false) {
        $geoData = json_decode($geoResp, true);
        if (!empty($geoData[0]['lat']) && !empty($geoData[0]['lon'])) {
            $lat = (float) $geoData[0]['lat'];
            $lng = (float) $geoData[0]['lon'];

            // Cache it back so this project never needs geocoding again
            $latColCheck = $conn->query("SHOW COLUMNS FROM trending_projects LIKE 'lat'");
            $lngColCheck = $conn->query("SHOW COLUMNS FROM trending_projects LIKE 'lng'");
            if ($latColCheck && $latColCheck->num_rows > 0 && $lngColCheck && $lngColCheck->num_rows > 0) {
                $cacheStmt = $conn->prepare("UPDATE trending_projects SET lat = ?, lng = ? WHERE id = ?");
                if ($cacheStmt) {
                    $cacheStmt->bind_param('ddi', $lat, $lng, $project['id']);
                    $cacheStmt->execute();
                    $cacheStmt->close();
                }
            }
        } else {
            error_log('Geocoding found no results for address: ' . $project['address']);
        }
    } else {
        error_log('Geocoding request failed for address: ' . $project['address']);
    }
}

// Final fallback only if there's truly no address or geocoding failed
if ($lat === null || $lng === null) {
    $lat = 19.2183;
    $lng = 72.9781;
}

// Get brochure path
$brochurePath = !empty($project['brochure'])
    ? "../../../admin/uploads/brochures/" . $project['brochure']
    : '';

// Get features for Why You Should Consider section
if (!empty($project['amenities'])) {
    $features = array_values(array_filter(array_map('trim', explode(',', $project['amenities']))));
} elseif ($isInteriorProject) {
    // Interior-specific fallback — swap these for whatever copy fits your interior catalogue
    $features = [
        'Premium quality materials sourced from trusted vendors',
        'Custom design consultation tailored to your space',
        'Skilled in-house installation team',
        'Warranty backed workmanship',
        'Wide range of finishes and styles to choose from',
    ];
} else {
    $features = [
        'Four-sided open bungalow and every flat is a corner flat',
        'The project is connected with 200 feet and 80 feet wide roads',
        'Vaastu oriented and aesthetic landscaping',
        '50 feet entry, 60 feet exit, 100 feet arrival plaza',
        'Tower connected to clubhouse by a skywalk',
        '30,000sqft. podium garden with water body, waterfall and gazebos',
        'G+8 clubhouse 6000 sqft plot size on 48,000 sqft building'
    ];
}

// ============================================================
// About Project + Specifications tab content
// ============================================================
$aboutProjectText = pv($project, 'about_project',
    "Check out " . $project['project_name'] . " in " . pv($project, 'address', 'this location') . ", one of the upcoming under-construction housing societies. There are apartments for sale in this society. This society will have all basic facilities and amenities to suit homebuyer's needs and requirements.\n\nBeing a RERA-registered society, the project details and other important information is also available on the state RERA portal.\n\nHere's everything you need to know about the must-know features of this housing society along with photos, floor plans, payment plans, brochure download procedure and other exciting facts about your future home:"
);

$phases = [];
if (!empty($project['phases_json'])) {
    $decoded = json_decode($project['phases_json'], true);
    if (is_array($decoded)) $phases = $decoded;
}
if (empty($phases)) {
    $phases = [
        ['name' => 'Phase 1', 'status' => pv($project, 'construction_status', 'Under Construction'), 'date' => pv($project, 'possession_date', 'Dec, 2026')],
    ];
}

// For interior products, "Overview" and the specs accordion show the
// product's category instead of building-level fields (towers/floors/etc).
$interiorCategoryName = '';
if (($isInteriorProject || $isVendorProject) && !empty($project['category_id'])) {
    $catStmt = $conn->prepare("SELECT name FROM property_categories WHERE id = ? LIMIT 1");
    if ($catStmt) {
        $catStmt->bind_param('i', $project['category_id']);
        $catStmt->execute();
        $catRow = $catStmt->get_result()->fetch_assoc();
        if ($catRow && !empty($catRow['name'])) {
            $interiorCategoryName = $catRow['name'];
        }
        $catStmt->close();
    }
}

if ($isInteriorProject || $isVendorProject) {
    $specOverview = [
        'category' => ['label' => 'Category', 'value' => $interiorCategoryName ?: 'N/A'],
    ];

    $specSections = [
        ($interiorCategoryName ?: 'Category') => pv($project, 'about_project',
            'No additional details available for this category.'),
    ];

    // Used below to decide which DB column an accordion section saves to.
    $specFieldMapGlobal = [($interiorCategoryName ?: 'Category') => 'about_project'];
} else {
    $specOverview = [
        'towers'             => ['label' => 'Towers', 'value' => pv($project, 'towers')],
        'floors'             => ['label' => 'Floors', 'value' => pv($project, 'floors')],
        'units'              => ['label' => 'Units', 'value' => pv($project, 'units')],
        'total_project_area' => ['label' => 'Total Project Area', 'value' => pv($project, 'total_project_area', pv($project, 'plot_area'))],
        'open_area'          => ['label' => 'Open Area', 'value' => pv($project, 'open_area')],
    ];

    $specSections = [
        'Flooring' => pv($project, 'spec_flooring',
            "Wooden flooring in the master bedroom\n800x800mm double charged vitrified tiled flooring in all rooms\n600x600mm rustic tiles in terraces and balconies\n300x300mm matte finished tiled flooring in all toilets"),
        'Bathrooms' => pv($project, 'spec_bathrooms',
            "600x300mm digital wall tiles in all toilets up to lintel level\nProvision for exhaust fans\nHot and cold mixer unit in bathrooms\nJAQUAR/GROHE/CERA or equivalent CP fittings\nHINDWARE/RAK/CERA or equivalent sanitary ware\nConcealed plumbing"),
        'Kitchen' => pv($project, 'spec_kitchen',
            "Granite kitchen platform with stainless steel sink\n600x300mm digital wall tiles up to ceiling\nProvision for exhaust fan\nR.O. water purifier"),
        'Doors and Windows' => pv($project, 'spec_doors_windows',
            "Decorative main door with both side laminated flush door with laminated / polished wooden door frames in living and bed rooms\nGranite door frames in toilets and terraces with both side sliding/french doors for terraces attached to living rooms\nHigh density powder coated aluminium sliding windows with mosquito net & MS grills"),
    ];

    $specFieldMapGlobal = ['Flooring' => 'spec_flooring', 'Bathrooms' => 'spec_bathrooms', 'Kitchen' => 'spec_kitchen', 'Doors and Windows' => 'spec_doors_windows'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($pageTitle); ?> — <?php echo htmlspecialchars($site['name']); ?></title>
<!-- Favicon -->
<link rel="icon" type="image/png" href="/front/assets/images/favicon.png">
<link rel="apple-touch-icon" sizes="180x180" href="/front/assets/images/favicon.png">
<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<!-- Site styles -->
<link rel="stylesheet" href="/front/assets/css/style.css">
<link rel="stylesheet" href="/front/assets/css/property-listing.css">
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>
 
<?php if ($isLoggedIn && $project): ?>
<div id="editModeBar" class="edit-mode-bar">
    <button type="button" id="editModeToggle" class="edit-mode-toggle-btn">
        <i class="bi bi-pencil-square"></i> <span id="editModeToggleLabel">Edit Page</span>
    </button>
    <span id="editModeStatus" class="edit-mode-status"></span>
</div>
<style>
.edit-mode-bar{position:sticky;top:114px;z-index:1029;display:flex;align-items:center;gap:1rem;background:#12213a;color:#fff;padding:.6rem 1.25rem;}
.edit-mode-toggle-btn{background:#c9a84c;color:#fff;border:none;border-radius:.4rem;padding:.4rem 1rem;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:.4rem;}
.edit-mode-toggle-btn.active{background:#e74c3c;}
.edit-mode-status{font-size:.8rem;opacity:.85;}
body.edit-mode-active .editable-section{outline:2px dashed #c9a84c;outline-offset:4px;position:relative;}
body.edit-mode-active .editable-field{background:rgba(201,168,76,.08);outline:1px dashed rgba(201,168,76,.5);padding:.1rem .2rem;border-radius:.2rem;cursor:text;}
.section-save-btn{display:none;margin-top:.75rem;background:#c9a84c;color:#fff;border:none;border-radius:.4rem;padding:.45rem 1.1rem;font-weight:600;cursor:pointer;align-items:center;gap:.4rem;}
body.edit-mode-active .section-save-btn{display:inline-flex;}
.section-save-msg{font-size:.8rem;margin-left:.6rem;}
.editable-input{display:none;width:100%;padding:.5rem .7rem;border:1px solid var(--line,#e8ecf0);border-radius:.5rem;font-size:.9rem;}
</style>
<?php endif; ?>
    
<?php if (!$project): ?>

  <div class="container py-5 text-center">
    <h2 class="mb-3">Project not found</h2>
    <p class="text-muted">The project you're looking for may have been removed or is no longer active.</p>
    <a href="/front/index.php" class="btn-login" style="text-decoration:none;">Back to Home</a>
  </div>

<?php else: ?>

  <!-- ============ TOP SECTION: 2-COLUMN LAYOUT (70/30) ============ -->
<div class="container">
    <div class="property-top-wrap">

        <!-- LEFT COLUMN (70%) - Image + Title + Details -->
        <div class="property-left<?php echo $isLoggedIn ? ' editable-section' : ''; ?>" data-section="images" data-project-id="<?php echo (int) $project['id']; ?>">
            <div class="gallery-col" style="position:relative;">
                <img class="hero-img" id="mainImage"
                     src="<?php echo htmlspecialchars($imagePath); ?>"
                     alt="<?php echo htmlspecialchars($project['project_name']); ?>"
                     onclick="openEnhancedGallery(currentImageIndex || 0)">

                <!-- Share Button - Top Right -->
                <button class="share-btn-top" onclick="shareProperty()" 
                        style="position:absolute; top:15px; right:15px; z-index:5; 
                               background:rgba(255,255,255,0.95); border:1px solid rgba(0,0,0,0.1); 
                               border-radius:50%; width:44px; height:44px; 
                               display:flex; align-items:center; justify-content:center;
                               box-shadow:0 2px 12px rgba(0,0,0,0.15); cursor:pointer;
                               transition:all 0.3s ease; backdrop-filter:blur(4px);">
                    <i class="bi bi-share-fill" style="font-size:1.2rem; color:#12213a;"></i>
                </button>
              
                <!-- Wishlist Button - Top Right (next to share) -->
                <button class="wishlist-btn-top" id="wishlistBtn" onclick="toggleWishlist(this)" style="position:absolute; top:15px; right:70px; z-index:5; 
               background:rgba(255,255,255,0.95); border:1px solid rgba(0,0,0,0.1); 
               border-radius:50%; width:44px; height:44px; 
               display:flex; align-items:center; justify-content:center;
               box-shadow:0 2px 12px rgba(0,0,0,0.15); cursor:pointer;
               transition:all 0.3s ease; backdrop-filter:blur(4px);">
                    <i class="bi bi-heart" style="font-size:1.2rem; color:#12213a; transition:all 0.3s ease;"></i>
                </button>

                <!-- Verified Badge - Top Left -->
                <div class="verified-badge-top" style="position:absolute; top:15px; left:15px; background:#e6f6ec; color:#1a8f4c; font-size:0.75rem; border-radius:2rem; padding:0.25rem 0.8rem; display:inline-flex; align-items:center; gap:0.3rem; z-index:2; font-weight:600; box-shadow:0 2px 8px rgba(0,0,0,0.15);">
                    <i class="bi bi-patch-check-fill"></i> Verified
                </div>

                <!-- Price & Status Pills - Bottom Right Corner with Label -->
                <div style="position:absolute; bottom:15px; right:15px; z-index:5; display:flex; flex-direction:column; align-items:flex-end; gap:0.3rem;">
                    <!-- Budget Starts From Label -->
                    <span style="color:rgba(255,255,255,0.9); font-size:0.7rem; font-weight:500; text-shadow:0 1px 4px rgba(0,0,0,0.5); letter-spacing:0.05em; text-transform:uppercase; background:rgba(0,0,0,0.3); padding:0.15rem 0.6rem; border-radius:0.25rem; backdrop-filter:blur(2px);">
                        Budget Starts From
                    </span>
                    <!-- Price & Status Pills Row -->
                    <div style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap; justify-content:flex-end;">
                        <span class="price-pill" style="background:#c9a84c; color:#fff; border-radius:0.5rem; padding:0.4rem 0.9rem; font-weight:700; display:inline-block; box-shadow:0 2px 8px rgba(0,0,0,0.2); font-size:0.95rem;">
                            <?php echo htmlspecialchars($project['price']); ?>
                        </span>
                    </div>
                </div>

                <!-- Title & Address - Bottom Left Overlay -->
                <div class="title-overlay" style="position:absolute; bottom:0; left:0; right:0; padding:1.5rem 1.5rem 1.2rem; background:linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 100%); border-radius:0 0 1rem 1rem; z-index:2; color:#fff;">
                    <h1 class="listing-title" style="font-size:clamp(1.3rem,2.4vw,1.9rem); font-weight:700; margin-bottom:0.25rem; color:#fff; text-shadow:0 2px 4px rgba(0,0,0,0.3);">
                        <?php echo htmlspecialchars($project['project_name']); ?>
                    </h1>
                    <div class="listing-loc" style="color:rgba(255,255,255,0.85); margin-bottom:0.3rem; font-size:0.95rem; text-shadow:0 1px 3px rgba(0,0,0,0.3);">
                        <i class="bi bi-geo-alt-fill" style="color:#c9a84c;"></i>
                        <?php echo htmlspecialchars(pv($project, 'address', '')); ?>
                    </div>
                </div>

            </div>

            <!-- Thumbnails Row Below Big Image -->
            <div class="thumb-row my-1">
                <img class="thumb active" onclick="swapMainImage(this, 0)" data-index="0"
                     src="<?php echo htmlspecialchars($imagePath); ?>" alt="Main Image">
                <?php foreach ($galleryImages as $i => $galImage): ?>
                    <?php $galPath = "../../../admin/uploads/trending-projects/" . $galImage; ?>
                    <img class="thumb" onclick="swapMainImage(this, <?php echo $i + 1; ?>)" data-index="<?php echo $i + 1; ?>"
                         src="<?php echo htmlspecialchars($galPath); ?>" alt="Gallery Image <?php echo $i + 1; ?>">
                <?php endforeach; ?>
                <!-- Add Image Button (only in edit mode) -->
                <?php if ($isLoggedIn): ?>
                <div class="edit-mode-only" style="display:none; position:relative; width:80px; height:80px; flex-shrink:0; border:2px dashed #c9a84c; border-radius:0.5rem; cursor:pointer; background:rgba(201,168,76,0.1); transition:all 0.3s ease; overflow:hidden;" 
                     onclick="document.getElementById('galleryImageUpload').click()"
                     onmouseover="this.style.background='rgba(201,168,76,0.2)'"
                     onmouseout="this.style.background='rgba(201,168,76,0.1)'">
                    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); text-align:center; color:#c9a84c; font-size:0.7rem; font-weight:600; width:100%; padding:0.5rem;">
                        <i class="bi bi-plus-circle" style="font-size:1.5rem; display:block; margin-bottom:0.2rem;"></i>
                        Add Image
                    </div>
                    <input type="file" id="galleryImageUpload" accept="image/*" multiple style="display:none;" onchange="uploadGalleryImages(this, <?php echo (int) $project['id']; ?>)">
                </div>
                <?php endif; ?>
            </div>
            <!-- Image Management Controls (Edit Mode) -->
    <?php if ($isLoggedIn): ?>
    <div class="edit-mode-only" style="display:none; margin-top:0.75rem; padding:0.75rem; background:var(--bg-soft); border-radius:0.5rem; border:1px solid var(--line);">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.5rem;">
            <div style="display:flex; align-items:center; gap:0.75rem;">
                <span style="font-size:0.75rem; font-weight:600; color:var(--muted);">Main Image:</span>
                <input type="file" id="mainImageUpload" accept="image/*" style="font-size:0.7rem;">
                <button type="button" class="btn btn-sm" onclick="uploadMainImage(<?php echo (int) $project['id']; ?>)" style="background:#c9a84c; color:#fff; border:none; border-radius:0.3rem; padding:0.2rem 0.8rem; font-size:0.7rem; font-weight:600; cursor:pointer;">
                    <i class="bi bi-upload"></i> Update Main Image
                </button>
            </div>
            <div style="display:flex; align-items:center; gap:0.5rem;">
                <span style="font-size:0.7rem; color:var(--muted);">Click on any thumbnail to remove it</span>
                <button type="button" class="btn btn-sm btn-danger" onclick="enableImageDeletion()" style="border:none; border-radius:0.3rem; padding:0.2rem 0.8rem; font-size:0.7rem; font-weight:600; cursor:pointer; background:#dc3545; color:#fff;">
                    <i class="bi bi-trash"></i> Remove Images
                </button>
            </div>
        </div>
        <div id="imageDeleteStatus" style="font-size:0.7rem; color:#dc3545; margin-top:0.3rem; display:none;">
            <i class="bi bi-exclamation-triangle"></i> Click on any thumbnail to remove it
        </div>
    </div>
    <?php endif; ?>
            
        </div>

        <!-- RIGHT COLUMN (30%) - Property Info Display -->
<div class="property-right<?php echo $isLoggedIn ? ' editable-section' : ''; ?>" data-section="property-details" data-project-id="<?php echo (int) $project['id']; ?>">
    <div class="card-panel owner-card" style="margin-top:0; padding:1.2rem 1.2rem;">
        
        <!-- EstateHub with Star Rating - Row 1 -->
        <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-building" style="color:#c9a84c; font-size:1.1rem;"></i>
                <div style="font-size:0.95rem; font-weight:700; color:var(--ink);" class="editable-field" data-field="<?php echo $isInteriorProject ? 'project_name' : 'company_name'; ?>"
         contenteditable="false">
                    <?php //echo htmlspecialchars(pv($project, 'company_name', 'EstateHub')); ?>
                    <?php
                        echo $isInteriorProject
                            ? htmlspecialchars($project['project_name'])
                            : htmlspecialchars(pv($project, 'company_name', 'EstateHub'));
                    ?>
                </div>
            </div>
            <div class="stars" style="display:flex; align-items:center; gap:0.1rem; font-size:0.65rem; color:#f5b942; flex-shrink:0;">
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <i class="bi bi-star-fill"></i>
                <span style="color:var(--muted); font-size:0.6rem; font-weight:500; margin-left:0.15rem;">4.8</span>
            </div>
        </div>
        <?php if (!$isInteriorProject): ?>

        <!-- Property Name with Location - Row 2 -->
        <div style="font-size:1.1rem; font-weight:700; color:var(--ink); margin-bottom:0.2rem;">
            <span class="editable-field" data-field="project_name" contenteditable="false">
                <?php echo htmlspecialchars($project['project_name']); ?>
            </span>
        </div>
        <?php endif; ?>
        <div style="font-size:0.8rem; color:var(--muted); margin-bottom:0.8rem;">
            <i class="bi bi-geo-alt-fill" style="color:#c9a84c; margin-right:0.3rem;"></i>
            <span class="editable-field" data-field="address" contenteditable="false">
                <?php echo htmlspecialchars(pv($project, 'address', '')); ?>
            </span>
        </div>
                        <?php if (!$isInteriorProject && !$isHotelProject): ?>

        <!-- ===== APPROVALS & RERA / GST - LEFT ALIGNED ===== -->
        <div class="approvals-section" style="margin-top:0.5rem; margin-bottom:0.5rem; padding:0.5rem 0; border-top:1px solid var(--line); border-bottom:1px solid var(--line); transition: border-color 0.3s ease;">

            <?php if ($isVendorProject): ?>
                <!-- Vendors: GST Name + GST Registration Number (stored in the same rera_number / approvals columns) -->
                <div style="display:flex; align-items:center; padding:0.15rem 0;">
                    <span style="font-size:0.95rem; font-weight:600; color:var(--ink); min-width:155px; flex-shrink:0;">GST Name:-</span>
                    <span style="font-size:0.8rem; font-weight:600; color:var(--ink); transition: color 0.3s ease;" class="editable-field" data-field="rera_number" contenteditable="false">
                        <i class="bi bi-check-circle-fill" style="color:#22c55e; font-size:0.65rem; margin-right:0.3rem;"></i>
                        <?php echo htmlspecialchars(pv($project, 'rera_number', 'N/A')); ?>
                    </span>
                </div>
                <div style="display:flex; align-items:center; padding:0.15rem 0;">
                    <span style="font-size:0.95rem; font-weight:600; color:var(--ink); min-width:155px; flex-shrink:0;">Registration No.:-</span>
                    <span style="font-size:0.8rem; font-weight:600; color:var(--ink); transition: color 0.3s ease;" class="editable-field" data-field="approvals" contenteditable="false">
                        <i class="bi bi-check-circle-fill" style="color:#22c55e; font-size:0.65rem; margin-right:0.3rem;"></i>
                        <?php echo htmlspecialchars(pv($project, 'approvals', 'N/A')); ?>
                    </span>
                </div>
            <?php else: ?>
                <div style="display:flex; align-items:center; padding:0.15rem 0;">
                    <span style="font-size:0.95rem; font-weight:600; color:var(--ink); min-width:155px; flex-shrink:0;">RERA No:-</span>
                    <span style="font-size:0.8rem; font-weight:600; color:var(--ink); transition: color 0.3s ease;" class="editable-field" data-field="rera_number" contenteditable="false">
                        <i class="bi bi-check-circle-fill" style="color:#22c55e; font-size:0.65rem; margin-right:0.3rem;"></i>
                        <?php echo htmlspecialchars(pv($project, 'rera_number', 'P02400053321')); ?>
                    </span>
                </div>
                <div style="display:flex; align-items:center; padding:0.15rem 0;">
                    <span style="font-size:0.95rem; font-weight:600; color:var(--ink); min-width:155px; flex-shrink:0;">Approvals:-</span>
                    <span style="font-size:0.8rem; font-weight:600; color:var(--ink); transition: color 0.3s ease;" class="editable-field" data-field="approvals" contenteditable="false">
                        <i class="bi bi-check-circle-fill" style="color:#22c55e; font-size:0.65rem; margin-right:0.3rem;"></i>
                        <?php echo htmlspecialchars(pv($project, 'approvals', 'HMDA | RERA')); ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
<?php endif; ?>
        
        <!-- ===== PROPERTY INFO - LEFT ALIGNED ===== -->
        <div class="property-info-display" style="margin-top:0.5rem; margin-bottom:0.5rem; padding:0 0; border-bottom:1px solid var(--line); transition: border-color 0.3s ease;">
            <div style="display:flex; align-items:center; padding:0.15rem 0;">
                <span style="font-size:0.75rem; font-weight:600; color:var(--ink); min-width:155px; flex-shrink:0;">Budget Starts From:-</span>
                <span style="font-size:0.85rem; font-weight:700; color:#c9a84c; transition: color 0.3s ease;" class="editable-field" data-field="price">
                    <?php 
                        $displayPrice = htmlspecialchars($project['price']);
                        if (strpos($displayPrice, '₹') === false) {
                            $displayPrice = '₹' . $displayPrice;
                        }
                        echo $displayPrice;
                    ?>
                </span>
            </div>
            <?php if ($isHotelProject): ?>
<div style="display:flex; align-items:center; padding:0.15rem 0; border-top:1px solid var(--line); padding-top:0.3rem; margin-top:0.1rem; transition: border-color 0.3s ease;">
    <span style="font-size:0.75rem; font-weight:600; color:var(--ink); min-width:155px; flex-shrink:0;">Available Rooms:-</span>
    <span style="font-size:0.85rem; font-weight:600; color:var(--ink); transition: color 0.3s ease;" class="editable-field" data-field="available_rooms" contenteditable="false">
        <?php echo htmlspecialchars(pv($project, 'available_rooms', 'N/A')); ?>
    </span>
</div>
<!-- Date & Guest Selector for Hotels -->
<div style="display:flex; align-items:center; padding:0.15rem 0; margin-top:0.2rem; gap:0.5rem; flex-wrap:wrap; background:var(--bg-soft, #f7f8fa); border-radius:0.5rem; padding:0.3rem 0.6rem; border:1px solid var(--line, #e8ecf0);">
    <div style="display:flex; align-items:center; gap:0.3rem; font-size:0.7rem; color:var(--muted, #6b7a8f);">
        <i class="bi bi-calendar3" style="color:#c9a84c;"></i>
        <span id="hotelDateDisplay" style="font-weight:600; color:var(--ink, #12213a);">Wed, 2 Sep – Thu, 3 Sep</span>
    </div>
    <span style="color:var(--line, #e8ecf0);">|</span>
    <div style="display:flex; align-items:center; gap:0.3rem; font-size:0.7rem; color:var(--muted, #6b7a8f);">
        <i class="bi bi-person-fill" style="color:#c9a84c;"></i>
        <span id="hotelGuestDisplay" style="font-weight:600; color:var(--ink, #12213a);">1 Room, 1 Guest</span>
    </div>
    <button onclick="openHotelCalendar()" style="background:transparent; border:none; color:#c9a84c; font-size:0.7rem; font-weight:600; cursor:pointer; padding:0.1rem 0.3rem; border-radius:0.3rem; transition:all 0.2s ease; margin-left:auto;" onmouseover="this.style.background='rgba(201,168,76,0.1)'" onmouseout="this.style.background='transparent'">
        <i class="bi bi-pencil"></i> Edit
    </button>
</div>
<?php endif; ?>
            <?php if (!$isInteriorProject && !$isVendorProject && !$isHotelProject): ?>
            <div style="display:flex; align-items:center; padding:0.15rem 0; border-top:1px solid var(--line); padding-top:0.3rem; margin-top:0.1rem; transition: border-color 0.3s ease;">
                <span style="font-size:0.75rem; font-weight:600; color:var(--ink); min-width:155px; flex-shrink:0;" id="areaLabel">Starts From SFT:-</span>
                <span style="font-size:0.85rem; font-weight:600; color:var(--muted); transition: color 0.3s ease; display:flex; align-items:center; gap:0.5rem;">
                    <span id="sftDisplayValue" class="editable-field" data-field="min_sft" contenteditable="false">
                    <?php 
                        $minSft = '1000 - 3000';
                        $sftValue = '';
                        if (!empty($floorPlans)) {
                            $firstPlan = $floorPlans[0] ?? null;
                            if ($firstPlan && !empty($firstPlan['variants'])) {
                                $firstVariant = $firstPlan['variants'][0] ?? null;
                                if ($firstVariant && !empty($firstVariant['carpet_area_sqft'])) {
                                    $minSft = $firstVariant['carpet_area_sqft'];
                                }
                            }
                        }
                        $sftValue = $minSft;
                        echo htmlspecialchars($sftValue);
                    ?>
                </span>
                <select id="sftUnitSelect" style="border:1px solid var(--line); border-radius:0.3rem; padding:0.1rem 0.3rem; font-size:0.5rem; background:transparent; color:var(--muted); cursor:pointer;">
                    <option value="sqft" selected>sqft</option>
                    <option value="sqyd">sqyd</option>
                    <option value="sqm">sqm</option>
                    <option value="grounds">grounds</option>
                    <option value="aankadam">aankadam</option>
                    <option value="rood">rood</option>
                    <option value="chataks">chataks</option>
                    <option value="perch">perch</option>
                    <option value="guntha">guntha</option>
                    <option value="ares">ares</option>
                    <option value="biswa">biswa</option>
                    <option value="acres">acres</option>
                    <option value="bigha">bigha</option>
                    <option value="kottah">kottah</option>
                    <option value="hectares">hectares</option>
                    <option value="marla">marla</option>
                    <option value="kanal">kanal</option>
                    <option value="cents">cents</option>
                </select>
            </span>
            </div>
            <?php endif; ?>
                        <?php if ($isHotelProject): ?>
            <div style="display:flex; align-items:center; padding:0.15rem 0; border-top:1px solid var(--line); padding-top:0.3rem; margin-top:0.1rem; transition: border-color 0.3s ease;">
    <span style="font-size:0.75rem; font-weight:600; color:var(--ink); min-width:155px; flex-shrink:0;">Available Rooms:-</span>
    <span style="font-size:0.85rem; font-weight:600; color:var(--ink); transition: color 0.3s ease;" class="editable-field" data-field="available_rooms" contenteditable="false">
        <?php echo htmlspecialchars(pv($project, 'available_rooms', 'N/A')); ?>
    </span>
</div>
<?php elseif (!$isInteriorProject && !$isVendorProject): ?>
            <div style="display:flex; align-items:center; padding:0.15rem 0; border-top:1px solid var(--line); padding-top:0.3rem; margin-top:0.1rem; transition: border-color 0.3s ease;">
    <span style="font-size:0.75rem; font-weight:600; color:var(--ink); min-width:155px; flex-shrink:0;">Construction Status:-</span>
    <span style="font-size:0.85rem; font-weight:600; color:var(--ink); transition: color 0.3s ease;" class="editable-field" data-field="construction_status" contenteditable="false">
        <?php 
            $constructionStatus = !empty($project['construction_status']) ? $project['construction_status'] : 'Under Construction';
            echo htmlspecialchars($constructionStatus);
        ?>
        <span style="font-size:0.7rem; font-weight:400; color:var(--muted); display:block; text-align:right; margin-top:0.05rem;">
            Completion in 
            <span class="editable-field" data-field="year_built" contenteditable="false">
                <?php 
                    // Get completion date from year_built column
                    $completionDate = '';
                    $yearBuilt = !empty($project['year_built']) ? $project['year_built'] : '';
                    
                    if (!empty($yearBuilt)) {
                        // Check if it's a full date (YYYY-MM-DD)
                        if (strtotime($yearBuilt)) {
                            $timestamp = strtotime($yearBuilt);
                            $completionDate = date('M, Y', $timestamp);
                        } 
                        // Check if it's just a year (YYYY)
                        elseif (preg_match('/^\d{4}$/', $yearBuilt)) {
                            $completionDate = 'Dec, ' . $yearBuilt;
                        }
                        // Check if it's already formatted (e.g., "Dec, 2026")
                        elseif (preg_match('/^[A-Za-z]{3},\s?\d{4}$/', $yearBuilt)) {
                            $completionDate = $yearBuilt;
                        }
                        // Check if it's "YYYY-MM" format
                        elseif (preg_match('/^\d{4}-\d{2}$/', $yearBuilt)) {
                            $parts = explode('-', $yearBuilt);
                            $monthNum = (int)$parts[1];
                            $monthName = date('M', mktime(0, 0, 0, $monthNum, 1));
                            $completionDate = $monthName . ', ' . $parts[0];
                        }
                        // Default fallback
                        else {
                            // Try to extract year
                            preg_match('/\b(19|20)\d{2}\b/', $yearBuilt, $matches);
                            if (!empty($matches[0])) {
                                $completionDate = 'Dec, ' . $matches[0];
                            } else {
                                $completionDate = 'Dec, 2026';
                            }
                        }
                    } else {
                        // If no year_built is set, use current date's month + 1 year
                        $currentYear = date('Y');
                        $nextYear = $currentYear + 1;
                        $completionDate = 'Dec, ' . $nextYear;
                    }
                    
                    echo htmlspecialchars($completionDate);
                ?>
            </span>
                </span>
    </span>
</div>
<?php endif; ?>
        </div>
<!-- ===== SAVE BUTTON FOR THIS SECTION ===== -->
        <?php if ($isLoggedIn): ?>
        <div class="mt-2" style="border-top:1px solid var(--line); padding-top:0.75rem;">
            <button type="button" class="section-save-btn" style="background:#c9a84c; color:#fff; border:none; border-radius:0.4rem; padding:0.4rem 1.1rem; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:0.4rem;">
                <i class="bi bi-save"></i> Save Property Details
            </button>
            <span class="section-save-msg" style="font-size:0.8rem; margin-left:0.6rem;"></span>
        </div>
        <?php endif; ?>
        <script>
        // ===== SFT UNIT CONVERTER DROPDOWN =====
        document.addEventListener('DOMContentLoaded', function() {
            const sftSelect = document.getElementById('sftUnitSelect');
            const sftDisplay = document.getElementById('sftDisplayValue');
            const areaLabel = document.getElementById('areaLabel');

            if (!sftDisplay || !sftSelect) return;

            // Get the current SFT value (without any unit)
            let originalValue = sftDisplay.textContent.trim();
            // Remove any unit suffix if present
            originalValue = originalValue.replace(/\s*(sqft|sqyd|sqm|grounds|aankadam|rood|chataks|perch|guntha|ares|biswa|acres|bigha|kottah|hectares|marla|kanal|cents)$/i, '').trim();

            // Conversion factors (1 sqft = ?)
            const conversionFactors = {
                'sqft': 1,
                'sqyd': 0.111111,
                'sqm': 0.092903,
                'grounds': 0.000416667,
                'aankadam': 0.000416667,
                'rood': 0.0000918274,
                'chataks': 0.00229568,
                'perch': 0.000367309,
                'guntha': 0.000918274,
                'ares': 0.00092903,
                'biswa': 0.000074074,
                'acres': 0.0000229568,
                'bigha': 0.00003125,
                'kottah': 0.00004545,
                'hectares': 0.0000092903,
                'marla': 0.000367309,
                'kanal': 0.0000183655,
                'cents': 0.0000229568
            };

            // Unit display names for the label
            const unitLabels = {
                'sqft': 'Starts From SFT:-',
                'sqyd': 'Starts From SQYD:-',
                'sqm': 'Starts From SQM:-',
                'grounds': 'Starts From Grounds:-',
                'aankadam': 'Starts From Aankadam:-',
                'rood': 'Starts From Rood:-',
                'chataks': 'Starts From Chataks:-',
                'perch': 'Starts From Perch:-',
                'guntha': 'Starts From Guntha:-',
                'ares': 'Starts From Ares:-',
                'biswa': 'Starts From Biswa:-',
                'acres': 'Starts From Acres:-',
                'bigha': 'Starts From Bigha:-',
                'kottah': 'Starts From Kottah:-',
                'hectares': 'Starts From Hectares:-',
                'marla': 'Starts From Marla:-',
                'kanal': 'Starts From Kanal:-',
                'cents': 'Starts From Cents:-'
            };

            function convertSFT(value, unit) {
                const factor = conversionFactors[unit] || 1;

                if (value.indexOf(' - ') !== -1) {
                    const parts = value.split(' - ').map(function(p) { return parseFloat(p.trim()); });
                    const num1 = parts[0];
                    const num2 = parts[1];

                    const converted1 = (num1 * factor);
                    const converted2 = (num2 * factor);

                    // Format based on unit
                    let decimals = 2;
                    if (unit === 'hectares' || unit === 'acres' || unit === 'bigha' || unit === 'kanal' || unit === 'kottah') {
                        decimals = 4;
                    } else if (unit === 'grounds' || unit === 'aankadam' || unit === 'perch' || unit === 'marla' || unit === 'guntha' || unit === 'biswa') {
                        decimals = 3;
                    }

                    // Return only the number, no unit
                    return converted1.toFixed(decimals) + ' - ' + converted2.toFixed(decimals);
                } else {
                    const num = parseFloat(value);
                    const converted = num * factor;

                    let decimals = 2;
                    if (unit === 'hectares' || unit === 'acres' || unit === 'bigha' || unit === 'kanal' || unit === 'kottah') {
                        decimals = 4;
                    } else if (unit === 'grounds' || unit === 'aankadam' || unit === 'perch' || unit === 'marla' || unit === 'guntha' || unit === 'biswa') {
                        decimals = 3;
                    }

                    // Return only the number, no unit
                    return converted.toFixed(decimals);
                }
            }

            sftSelect.addEventListener('change', function() {
                const unit = this.value;
                const convertedValue = convertSFT(originalValue, unit);
                sftDisplay.textContent = convertedValue;

                // Update the label dynamically with the unit name
                if (areaLabel) {
                    areaLabel.textContent = unitLabels[unit] || 'Starts From ' + unit.toUpperCase() + ':-';
                }
            });
        });
        </script>

        <!-- ===== WHY YOU SHOULD CONSIDER SECTION ===== -->
        <div class="why-consider-section">
            <div class="consider-title">
                <i class="bi bi-info-circle-fill"></i>
                Why you should consider <?php echo htmlspecialchars($project['project_name']); ?>?
            </div>
            <ul class="consider-list">
                <?php
                $totalFeatures = count($features);
                $showCount = 3;
                $hiddenCount = max(0, $totalFeatures - $showCount);
                $visibleFeatures = array_slice($features, 0, $showCount);
                $hiddenFeatures = array_slice($features, $showCount);
                ?>
                <?php foreach ($visibleFeatures as $feature): ?>
                    <?php if (!empty($feature)): ?>
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <?php echo htmlspecialchars($feature); ?>
                    </li>
                    <?php endif; ?>
                <?php endforeach; ?>
                
                <?php if ($hiddenCount > 0): ?>
                    <li style="padding-top:0;">
                        <i class="bi bi-check-circle-fill" style="opacity:0.3;"></i>
                        <a href="#" class="more-link" data-bs-toggle="offcanvas" data-bs-target="#featuresOffcanvas" aria-controls="featuresOffcanvas">
                            View <?php echo $hiddenCount; ?> more →
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Contact Builder Button -->
        <?php
                $contactBtnLabel = 'Contact Builder';
        if ($isInteriorProject) {
            $contactBtnLabel = 'Contact Company';
        } elseif ($isVendorProject) {
            $contactBtnLabel = 'Contact Vendor';
        }
        ?>
        <div class="mt-2">
            <button class="btn-contact-builder w-100" onclick="openContactBuilder()" style="background:#c9a84c; color:#fff; border:none; border-radius:0.6rem; padding:0.6rem 1rem; font-weight:700; font-size:1rem; transition:all 0.3s ease; cursor:pointer; width:100%;">
                <i class="bi bi-building me-2"></i> <?php echo htmlspecialchars($contactBtnLabel); ?>
            </button>
        </div>
    </div>
</div>

    </div>
</div>

<!-- ============ FEATURES OFFCANVAS (POPUP) ============ -->
<div class="offcanvas offcanvas-end features-offcanvas" tabindex="-1" id="featuresOffcanvas" aria-labelledby="featuresOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="featuresOffcanvasLabel">
            <i class="bi bi-info-circle-fill"></i>
            Why you should consider <?php echo htmlspecialchars($project['project_name']); ?>?
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <?php foreach ($features as $feature): ?>
            <?php if (!empty($feature)): ?>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="bi bi-check"></i>
                </div>
                <div class="feature-text">
                    <?php 
                    // Check if feature contains "sqft" or "sq.ft" to highlight
                    if (preg_match('/(\d+[\s,]*sqft|sq\.?ft\.?)/i', $feature, $matches)) {
                        $highlight = $matches[0];
                        $feature = str_replace($highlight, '<span class="feature-highlight">' . $highlight . '</span>', $feature);
                    }
                    echo $feature;
                    ?>
                </div>
            </div>
      
            <?php endif; ?>
        <?php endforeach; ?>
        <!-- I'm Interested Button -->
        <button class="btn-interested">
            <i class="bi bi-building me-2"></i> I'm Interested in this Project
        </button>
    </div>
</div>

<!-- ============ SITE VISIT FORM - 2 COLUMN LAYOUT ============ -->
<div class="site-visit-full sv-compact">
    <div class="container">
        <div class="site-visit-form-wrapper">
            <form id="siteVisitForm" class="sv-form">
                <input type="hidden" name="page_id" value="<?php echo $project['id']; ?>">
                <input type="hidden" name="page_title" value="<?php echo htmlspecialchars($project['project_name']); ?>">

                <!-- ===== ROW 1: HEADER + NAME ===== -->
                <div class="sv-row sv-row-header">
                    <!-- LEFT: Title -->
                    <div class="sv-col-left">
                        <div class="sv-header">
                            <h2 class="sv-title">Request Site Visit</h2>
                            <p class="form-subtitle">Fill in the details and our team will get back to you shortly</p>
                        </div>
                    </div>
                    
                    <!-- RIGHT: Full Name -->
                    <div class="sv-col-right">
                        <div class="sv-field-group">
                            <label class="sv-label">Your full name <span class="text-danger">*</span></label>
                            <div class="sv-input-wrap">
                                <i class="bi bi-person-fill sv-input-icon"></i>
                                <input type="text" name="name" class="sv-input" placeholder="Enter your full name" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== ROW 2: TIMELINE (full width) ===== -->
                <div class="sv-row sv-row-timeline">
                    <div class="sv-col-left">
                        <div class="sv-field-group">
                            <label class="sv-label">By when are you planning to buy a property? <span class="text-danger">*</span></label>
                            <div class="sv-pill-group" role="radiogroup">
                                <label class="sv-pill">
                                    <input type="radio" name="buy_timeline" value="immediately" required>
                                    <span class="sv-pill-inner">
                                        <i class="bi bi-check-circle-fill sv-pill-check"></i>
                                        <span class="sv-pill-text">Immediately</span>
                                    </span>
                                </label>
                                <label class="sv-pill">
                                    <input type="radio" name="buy_timeline" value="3_months">
                                    <span class="sv-pill-inner">
                                        <i class="bi bi-check-circle-fill sv-pill-check"></i>
                                        <span class="sv-pill-text">3 months</span>
                                    </span>
                                </label>
                                <label class="sv-pill">
                                    <input type="radio" name="buy_timeline" value="6_months">
                                    <span class="sv-pill-inner">
                                        <i class="bi bi-check-circle-fill sv-pill-check"></i>
                                        <span class="sv-pill-text">6 months</span>
                                    </span>
                                </label>
                                <label class="sv-pill">
                                    <input type="radio" name="buy_timeline" value="more_than_6">
                                    <span class="sv-pill-inner">
                                        <i class="bi bi-check-circle-fill sv-pill-check"></i>
                                        <span class="sv-pill-text">More than 6 months</span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                        <div class="sv-col-right">
                        <div class="sv-field-group">
                            <label class="sv-label">Contact number <span class="text-danger">*</span></label>
                            <div class="sv-input-wrap">
                                <i class="bi bi-telephone-fill sv-input-icon"></i>
                                <input type="tel" name="contact" class="sv-input" placeholder="Enter your contact number" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== ROW 3: CONTACT + EMAIL (2 columns) ===== -->
                <div class="sv-row sv-row-contact">
                    <div class="sv-col-left">
                        
                        <!-- Description -->
                        <div class="sv-field-group">
                            <label class="sv-label">Description / What are you looking for</label>
                            <div class="sv-input-wrap sv-input-wrap-textarea">
                                <i class="bi bi-chat-left-text-fill sv-input-icon sv-input-icon-top"></i>
                                <textarea name="description" class="sv-input sv-textarea" rows="2" placeholder="Describe your requirements, budget preferences, and any specific needs..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="sv-col-right">
                        <div class="sv-field-group">
                            <label class="sv-label">Email Address <span class="text-danger">*</span></label>
                            <div class="sv-input-wrap">
                                <i class="bi bi-envelope-fill sv-input-icon"></i>
                                <input type="email" name="email" class="sv-input" placeholder="Enter your email address" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== ROW 4: CHECKBOXES + SUBMIT BUTTON ===== -->
                <div class="sv-row sv-row-submit">
                    <!-- LEFT: Checkboxes -->
                    <div class="sv-col-left">
                        <div class="sv-field-group sv-checkboxes-group">
                            <label class="sv-check">
                                <input type="checkbox" name="interested_home_loan" value="yes">
                                <span class="sv-check-box">
                                    <i class="bi bi-check"></i>
                                </span>
                                <span class="sv-check-text">I'm interested in Home loan</span>
                            </label>
                            <label class="sv-check">
                                <input type="checkbox" name="interested_site_visit" value="yes">
                                <span class="sv-check-box">
                                    <i class="bi bi-check"></i>
                                </span>
                                <span class="sv-check-text">I'm interested in site visit</span>
                            </label>
                            <label class="sv-check">
                                <input type="checkbox" name="terms_agree" value="yes">
                                <span class="sv-check-box">
                                    <i class="bi bi-check"></i>
                                </span>
                                <span class="sv-check-text">I agree to the Terms & Conditions</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- RIGHT: Submit Button -->
                    <div class="sv-col-right sv-submit-col">
                        <div id="siteVisitFormMessage"></div>
                        <button type="submit" class="sv-submit-btn">
                            <i class="bi bi-send me-2"></i> Submit Request
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<style>
/* ===== REQUEST SITE VISIT - 2 COLUMN LAYOUT ===== */
.site-visit-full.sv-compact {
    padding: 2.5rem 0;
    background: var(--bg-soft, #f7f8fa);
}

.sv-compact .site-visit-form-wrapper {
    background: var(--card-bg, #fff);
    padding: 2rem 2.5rem;
    border-radius: 1.25rem;
    box-shadow: 0 4px 24px rgba(0,0,0,0.06);
    border: 1px solid var(--line, #e8ecf0);
    max-width: 100%;
    margin: 0 auto;
}

/* ===== ROW SYSTEM ===== */
.sv-row {
    display: flex;
    gap: 2.5rem;
    margin-bottom: 1.5rem;
}

.sv-row:last-child {
    margin-bottom: 0;
}

.sv-col-left {
    flex: 0 0 48%;
    max-width: 48%;
}

.sv-col-right {
    flex: 1;
    min-width: 0;
}

.sv-col-full {
    flex: 1;
}

/* ===== HEADER ROW ===== */
.sv-row-header .sv-header {
    margin-bottom: 0;
}

.sv-row-header .sv-title {
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--ink, #12213a);
    margin-bottom: 0.15rem;
}

.sv-row-header .form-subtitle {
    font-size: 0.85rem;
    color: var(--muted, #6b7a8f);
    margin: 0;
}

/* ===== FIELD GROUPS ===== */
.sv-field-group {
    margin-bottom: 0;
}

.sv-label {
    display: block;
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--ink, #12213a);
    margin-bottom: 0.5rem;
    letter-spacing: 0.01em;
}

.sv-label .text-danger {
    color: #e74c3c;
}

/* ===== PILL GROUP (Timeline) ===== */
.sv-pill-group {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.sv-pill {
    position: relative;
    cursor: pointer;
    flex-shrink: 0;
}

.sv-pill input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.sv-pill-inner {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1.1rem;
    border: 1.5px solid var(--line, #e8ecf0);
    border-radius: 2.5rem;
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--muted, #6b7a8f);
    background: var(--bg-soft, #f7f8fa);
    transition: all 0.25s ease;
    white-space: nowrap;
}

.sv-pill:hover .sv-pill-inner {
    border-color: #c9a84c;
    color: var(--ink, #12213a);
}

.sv-pill .sv-pill-check {
    display: none;
    font-size: 0.9rem;
    color: #fff;
}

.sv-pill:has(input:checked) .sv-pill-inner {
    background: #c9a84c;
    border-color: #c9a84c;
    color: #fff;
    box-shadow: 0 2px 12px rgba(201, 168, 76, 0.35);
}

.sv-pill:has(input:checked) .sv-pill-check {
    display: inline-block;
}

/* ===== CHECKBOXES ===== */
.sv-checkboxes-group {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    padding-top: 0.25rem;
}

.sv-check {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    cursor: pointer;
    font-size: 0.82rem;
    font-weight: 500;
    color: var(--ink, #12213a);
    padding: 0.1rem 0;
    transition: color 0.2s ease;
}

.sv-check:hover {
    color: var(--ink, #12213a);
}

.sv-check input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.sv-check-box {
    flex-shrink: 0;
    width: 1.1rem;
    height: 1.1rem;
    border: 2px solid var(--line, #d0d5dd);
    border-radius: 0.3rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 0.7rem;
    transition: all 0.2s ease;
    background: var(--bg-soft, #f7f8fa);
}

.sv-check input:checked + .sv-check-box {
    background: #c9a84c;
    border-color: #c9a84c;
}

.sv-check-text {
    line-height: 1.3;
}

/* ===== INPUT FIELDS ===== */
.sv-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
}

.sv-input-icon {
    position: absolute;
    left: 0.9rem;
    top: 50%;
    transform: translateY(-50%);
    color: #c9a84c;
    font-size: 0.95rem;
    pointer-events: none;
    z-index: 2;
}

.sv-input {
    width: 100%;
    padding: 0.65rem 0.9rem 0.65rem 2.6rem;
    border: 1.5px solid var(--line, #e8ecf0);
    border-radius: 0.6rem;
    font-size: 0.85rem;
    font-weight: 400;
    color: var(--ink, #12213a);
    background: var(--bg-soft, #f7f8fa);
    transition: all 0.25s ease;
    outline: none;
}

.sv-input:focus {
    border-color: #c9a84c;
    background: var(--card-bg, #fff);
    box-shadow: 0 0 0 4px rgba(201, 168, 76, 0.12);
}

.sv-input::placeholder {
    color: var(--muted, #a0aec0);
    font-weight: 400;
}

/* ===== SUBMIT BUTTON - RIGHT COLUMN ===== */
.sv-submit-col {
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    align-items: flex-start;
    gap: 0.5rem;
}

.sv-submit-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.7rem 2.5rem;
    background: #c9a84c;
    color: #fff;
    border: none;
    border-radius: 0.6rem;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 3px 14px rgba(201, 168, 76, 0.3);
    gap: 0.5rem;
    min-width: 200px;
    margin-top: 0.25rem;
}

.sv-submit-btn:hover {
    background: #b8963a;
    transform: translateY(-2px);
    box-shadow: 0 6px 24px rgba(201, 168, 76, 0.4);
}

.sv-submit-btn:active {
    transform: translateY(0);
}

.sv-submit-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

#siteVisitFormMessage {
    width: 100%;
}

#siteVisitFormMessage .alert {
    margin-bottom: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.85rem;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 992px) {
    .sv-compact .site-visit-form-wrapper {
        padding: 1.5rem;
    }
    
    .sv-row {
        flex-direction: column;
        gap: 1rem;
    }
    
    .sv-col-left,
    .sv-col-right {
        flex: 1 1 auto;
        max-width: 100%;
    }
    
    .sv-pill-group {
        flex-wrap: wrap;
    }
    
    .sv-pill-inner {
        padding: 0.4rem 0.9rem;
        font-size: 0.75rem;
    }
    
    .sv-submit-col {
        align-items: center;
    }
    
    .sv-row-header .sv-header {
        margin-bottom: 0.5rem;
    }
}

@media (max-width: 576px) {
    .sv-compact .site-visit-form-wrapper {
        padding: 1rem 0.75rem;
        border-radius: 0.75rem;
    }
    
    .sv-row-header .sv-title {
        font-size: 1.1rem;
    }
    
    .sv-row-header .form-subtitle {
        font-size: 0.75rem;
    }
    
    .sv-pill-inner {
        padding: 0.3rem 0.7rem;
        font-size: 0.7rem;
    }
    
    .sv-pill-group {
        gap: 0.35rem;
    }
    
    .sv-input {
        font-size: 0.8rem;
        padding: 0.55rem 0.75rem 0.55rem 2.2rem;
    }
    
    .sv-check {
        font-size: 0.75rem;
    }
    
    .sv-submit-btn {
        font-size: 0.85rem;
        padding: 0.6rem 1.5rem;
        min-width: 160px;
        width: 100%;
    }
    
    .sv-submit-col {
        align-items: stretch;
    }
}
    /* Edit mode styles */
.edit-mode-only {
    display: none;
}

body.edit-mode-active .edit-mode-only {
    display: block !important;
}

/* Hide the editable-field inputs in edit mode when using the input fields */
body.edit-mode-active .editable-field[data-field="furnished"],
body.edit-mode-active .editable-field[data-field="plot_area"] {
    display: none;
}

/* Style for editable-inputs in edit mode */
.editable-input {
    display: none;
}

body.edit-mode-active .editable-input {
    display: block !important;
}

/* Style for the amenities textarea in edit mode */
body.edit-mode-active .editable-input[data-field="amenities"] {
    display: block !important;
    min-height: 80px;
    width: 100%;
    padding: 0.6rem;
    border: 2px solid #c9a84c;
    border-radius: 0.5rem;
    font-size: 0.9rem;
    background: #fff;
    color: #12213a;
}
    /* Edit mode styles for company details */
.edit-mode-only {
    display: none;
}

body.edit-mode-active .edit-mode-only {
    display: block !important;
}

body.edit-mode-active .editable-field[data-field="company_name"],
body.edit-mode-active .editable-field[data-field="owner_phone"],
body.edit-mode-active .editable-field[data-field="website"],
body.edit-mode-active .editable-field[data-field="address"],
body.edit-mode-active .editable-field[data-field="project_name"],
body.edit-mode-active .editable-field[data-field="owner_email"],
body.edit-mode-active .editable-field[data-field="owner_email2"],
body.edit-mode-active .editable-field[data-field="owner_email_display"] {
    outline: 2px dashed #c9a84c;
    outline-offset: 2px;
    padding: 0.1rem 0.3rem;
    border-radius: 0.2rem;
    cursor: text;
}

.editable-input {
    display: none;
}

body.edit-mode-active .editable-input {
    display: block !important;
}

/* Style for editable inputs in edit mode */
body.edit-mode-active .editable-input {
    border: 2px solid #c9a84c !important;
    background: #fff !important;
    padding: 0.5rem 0.7rem !important;
    border-radius: 0.4rem !important;
    font-size: 0.9rem !important;
}
    /* Edit mode styles for project stats */
body.edit-mode-active .editable-field[data-field="bedrooms"],
body.edit-mode-active .editable-field[data-field="bathrooms"],
body.edit-mode-active .editable-field[data-field="area"],
body.edit-mode-active .editable-field[data-field="year_built"] {
    outline: 2px dashed #c9a84c;
    outline-offset: 2px;
    padding: 0.1rem 0.3rem;
    border-radius: 0.2rem;
    cursor: text;
}
    /* Edit mode brochure controls */
.edit-mode-only {
    display: none !important;
}

body.edit-mode-active .edit-mode-only {
    display: flex !important;
}

/* Brochure upload form styling */
#brochureUploadForm input[type="file"] {
    max-width: 150px;
    font-size: 0.7rem;
}

#brochureUploadForm button:hover {
    opacity: 0.85;
}

.btn-danger:hover {
    opacity: 0.85;
}
    /* Edit mode for images */
.edit-mode-only {
    display: none !important;
}

body.edit-mode-active .edit-mode-only {
    display: flex !important;
}

/* Image deletion mode */
.thumb.deletion-mode {
    cursor: pointer;
    outline: 2px solid #dc3545;
    outline-offset: 2px;
    opacity: 0.8;
}

.thumb.deletion-mode:hover {
    opacity: 0.5;
    transform: scale(0.95);
}

/* Add image button hover */
.thumb-add-btn:hover {
    background: rgba(201, 168, 76, 0.2) !important;
    border-color: #c9a84c !important;
}
</style>

<script>
    // Extra client-side validation for the redesigned Site Visit form
    document.addEventListener('DOMContentLoaded', function () {
        var svForm = document.getElementById('siteVisitForm');
        if (!svForm) return;

        svForm.addEventListener('submit', function (e) {
            var timelineChosen = svForm.querySelector('input[name="buy_timeline"]:checked');
            var termsChecked = svForm.querySelector('input[name="terms_agree"]').checked;
            var msgDiv = document.getElementById('siteVisitFormMessage');

            if (!timelineChosen) {
                e.preventDefault();
                e.stopImmediatePropagation();
                msgDiv.innerHTML = '<div class="alert alert-danger">Please select when you are planning to buy a property.</div>';
                return;
            }
            if (!termsChecked) {
                e.preventDefault();
                e.stopImmediatePropagation();
                msgDiv.innerHTML = '<div class="alert alert-danger">Please agree to the Terms & Conditions.</div>';
                return;
            }
        }, true); // capture phase, runs before your existing $.ajax handler
    });
</script>
  
    <!-- ============ Share Modal ============ -->
    <div class="modal fade" id="shareModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Share Property</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3" style="font-size:0.9rem;">Share this property with your friends and family</p>
                <div class="d-flex justify-content-around">
                    <button class="btn btn-outline-secondary rounded-circle p-3" onclick="shareVia('whatsapp')" style="width:60px; height:60px; display:flex; align-items:center; justify-content:center; border-color:#25D366;">
                        <i class="bi bi-whatsapp" style="font-size:1.5rem; color:#25D366;"></i>
                    </button>
                    <button class="btn btn-outline-secondary rounded-circle p-3" onclick="shareVia('facebook')" style="width:60px; height:60px; display:flex; align-items:center; justify-content:center; border-color:#1877F2;">
                        <i class="bi bi-facebook" style="font-size:1.5rem; color:#1877F2;"></i>
                    </button>
                    <button class="btn btn-outline-secondary rounded-circle p-3" onclick="shareVia('twitter')" style="width:60px; height:60px; display:flex; align-items:center; justify-content:center; border-color:#000;">
                        <i class="bi bi-twitter-x" style="font-size:1.5rem; color:#000;"></i>
                    </button>
                    <button class="btn btn-outline-secondary rounded-circle p-3" onclick="shareVia('email')" style="width:60px; height:60px; display:flex; align-items:center; justify-content:center; border-color:#EA4335;">
                        <i class="bi bi-envelope" style="font-size:1.5rem; color:#EA4335;"></i>
                    </button>
                </div>
                <div class="mt-3">
                    <div class="input-group">
                        <input type="text" class="form-control" id="shareLink" value="<?php echo htmlspecialchars('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" readonly>
                        <button class="btn btn-primary" onclick="copyShareLink()" style="background:#c9a84c; border-color:#c9a84c;">
                            <i class="bi bi-copy"></i> Copy
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- ============ ENHANCED GALLERY POPUP ============ -->
    <?php include __DIR__ . '/gallery-popup.php'; ?>

    <!-- ============ CONTACT BUILDER MODAL - UPDATED DESIGN ============ -->
    <div class="modal fade" id="contactBuilderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 1rem; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
            <div class="modal-header" style="border-bottom: 1px solid var(--line); padding: 1.25rem 1.5rem;">
                <h5 class="modal-title fw-bold" style="font-size: 1.2rem;">
                    <i class="bi bi-building" style="color: #c9a84c;"></i> 
                    Please fill in your details to get seller contact more
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 1.5rem 2rem;">
                
                <form id="contactBuilderForm">
                    <input type="hidden" name="page_id" value="<?php echo $project['id']; ?>">
                    <input type="hidden" name="page_title" value="<?php echo htmlspecialchars($project['project_name']); ?>">
                    
                    <!-- BASIC INFORMATION Section -->
                    <div style="margin-bottom: 1.5rem;">
                        <h6 style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--muted); font-weight: 700; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--line);">
                            <i class="bi bi-info-circle" style="color: #c9a84c;"></i> BASIC INFORMATION
                        </h6>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Are you a property dealer?</label>
                                <div style="display: flex; gap: 1.5rem; padding-top: 0.3rem;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="is_dealer" id="dealerYes" value="yes" style="border-color: var(--line);">
                                        <label class="form-check-label" for="dealerYes" style="font-size: 0.9rem; font-weight: 500;">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="is_dealer" id="dealerNo" value="no" checked style="border-color: var(--line);">
                                        <label class="form-check-label" for="dealerNo" style="font-size: 0.9rem; font-weight: 500;">No</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">By when you are planning to buy <?php echo $isInteriorProject ? 'interiors' : 'the property'; ?></label>
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; padding-top: 0.3rem;">                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="buy_timeline" id="timeline2" value="Immediately" style="border-color: var(--line);">
                                        <label class="form-check-label" for="timeline2" style="font-size: 0.85rem; font-weight: 500;">Immediately</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="buy_timeline" id="timeline3" value="3_months" style="border-color: var(--line);">
                                        <label class="form-check-label" for="timeline3" style="font-size: 0.85rem; font-weight: 500;">3 months</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="buy_timeline" id="timeline6" value="6_months" style="border-color: var(--line);">
                                        <label class="form-check-label" for="timeline6" style="font-size: 0.85rem; font-weight: 500;">6 months</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="buy_timeline" id="timelineMore" value="more_than_6" checked style="border-color: var(--line);">
                                        <label class="form-check-label" for="timelineMore" style="font-size: 0.85rem; font-weight: 500;">More than 6 months</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- OPTIONAL INFORMATION Section -->
                    <div style="margin-bottom: 1.5rem;">
                        <h6 style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--muted); font-weight: 700; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid var(--line);">
                            <i class="bi bi-pencil-square" style="color: #c9a84c;"></i> OPTIONAL INFORMATION
                        </h6>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Name *</label>
                                <input type="text" name="name" class="form-control" placeholder="Enter your full name" required style="border-radius: 0.6rem; padding: 0.6rem 0.9rem; border: 1px solid var(--line); font-size: 0.9rem;">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Phone *</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background: var(--bg-soft); border: 1px solid var(--line); border-right: none; border-radius: 0.6rem 0 0 0.6rem; font-size: 0.85rem; font-weight: 600;">
                                        +91
                                    </span>
                                    <input type="text" name="contact" class="form-control" placeholder="Enter your phone number" required style="border-radius: 0 0.6rem 0.6rem 0; padding: 0.6rem 0.9rem; border: 1px solid var(--line); font-size: 0.9rem;">
                                </div>
                            </div>
                        </div>
                        
                                                <!-- Checkboxes -->
                        <div style="margin-top: 1rem;">
                            <?php if (!$isInteriorProject && !$isVendorProject): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="interestedHomeLoan" name="interested_home_loan" value="yes" style="border-color: var(--line);">
                                <label class="form-check-label" for="interestedHomeLoan" style="font-size: 0.85rem; font-weight: 500;">
                                    I am interested in home loan
                                </label>
                            </div>
                            <?php endif; ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="interestedSiteVisits" name="interested_site_visits" value="yes" style="border-color: var(--line);">
                                <label class="form-check-label" for="interestedSiteVisits" style="font-size: 0.85rem; font-weight: 500;">
                                    <?php echo $isVendorProject ? 'I am interested in visits' : 'I am interested in site visits.'; ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Terms & Conditions -->
                    <div style="margin-bottom: 1.25rem;">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="termsCheck" name="terms_agree" value="yes" required style="border-color: var(--line);">
                            <label class="form-check-label" for="termsCheck" style="font-size: 0.8rem; color: var(--muted);">
                                I agree to the Terms & Conditions and Privacy Policy
                            </label>
                        </div>
                    </div>
                    
                    <!-- What's your phone Number? -->
                    <div style="margin-bottom: 1rem; padding: 0.75rem 1rem; background: var(--bg-soft); border-radius: 0.6rem; border-left: 3px solid #c9a84c;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="bi bi-telephone-fill" style="color: #c9a84c; font-size: 1.1rem;"></i>
                            <span style="font-size: 0.85rem; font-weight: 600; color: var(--ink);">What's your phone Number?</span>
                        </div>
                        <div style="margin-top: 0.3rem; font-size: 0.75rem; color: var(--muted);">
                            We'll send you the seller contact details via SMS
                        </div>
                    </div>
                    
                    <div id="contactBuilderMessage"></div>
                    
                    <button type="submit" class="btn-submit" style="background: #c9a84c; color: #fff; border: none; border-radius: 0.6rem; padding: 0.8rem; font-weight: 700; font-size: 1rem; width: 100%; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <i class="bi bi-eye"></i> View Seller contact
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

    <!-- ============ Nearby Places Modal ============ -->
    <div class="modal fade" id="nearbyMapModal" tabindex="-1" aria-labelledby="nearbyMapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold" id="nearbyMapModalLabel">Around <?php echo htmlspecialchars($project['project_name']); ?></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="nearby-layout">
            <div class="nearby-sidebar">
              <div class="nearby-sidebar-header">Around <?php echo htmlspecialchars($project['project_name']); ?></div>
              <div class="nearby-loading" id="nearbyLoading">
                <div class="spinner-border spinner-border-sm me-2"></div> Finding nearby places...
              </div>
              <div class="nearby-empty" id="nearbyEmpty">Couldn't load nearby places right now. Please try again in a moment.</div>
              <div id="nearbyCategories"></div>
            </div>
            <div class="nearby-map-col">
              <div id="nearbyMap"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

    <!-- ============ FLOOR PLAN POPUP / OVERLAY - FULL WIDTH ============ -->
    <div class="floorplan-overlay" id="floorplanOverlay">
        <div class="floorplan-popup floorplan-popup-full">

            <!-- ===== HEADER: 3-COLUMN LAYOUT ===== -->
            <div class="floorplan-popup-header">

        <!-- COL 1: Title Section with Dropdown -->
        <div class="popup-title-section">
            <span class="popup-title-label">SHOWING FLOOR PLAN FOR</span>
            <div class="popup-title-dropdown-wrapper">
                <select class="popup-bhk-dropdown" id="popupBhkDropdown" onchange="changePopupBhk(this.value)">
                    <!-- Dynamically populated -->
                </select>
            </div>
        </div>

        <!-- COL 2: Area Info Tabs -->
        <div class="popup-bhk-tabs" id="popupBhkTabs">
            <!-- Dynamically populated with area info only -->
        </div>

        <!-- COL 3: Close Button -->
        <div class="popup-header-right">
            <button class="floorplan-popup-close" onclick="closeFloorplanPopup()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

    </div>

            <!-- ===== BODY: 2-COLUMN LAYOUT ===== -->
            <div class="floorplan-popup-body-full">

                <!-- LEFT COLUMN: Image -->
                <div class="floorplan-image-col">
                    <!-- ===== TOP ROW: PRICE, POSSESSION & VIEW NUMBER ===== -->
            <div class="floorplan-top-row">
                <!-- LEFT: Price & Possession -->
                <div class="floorplan-top-left">
                    <div class="fp-detail-item fp-price-item">
                        <span class="fp-detail-label">Price</span>
                        <span class="fp-detail-value fp-price-value" id="popupPrice">₹91 L - ₹1.28 Cr <span class="charges">+ Charges</span></span>
                    </div>
                    <div class="fp-detail-item">
                        <span class="fp-detail-label">Possession</span>
                        <span class="fp-detail-value" id="popupPossession">Dec '26</span>
                    </div>
                </div>
                <!-- RIGHT: View Number Button -->
                <div class="floorplan-top-right">
                    <button class="fp-view-number-btn">
                        View Number <i class="bi bi-chevron-down"></i>
                    </button>
                </div>
            </div>

            <!-- ===== HELPFUL SECTION - ABOVE IMAGE ===== -->
            <div class="floorplan-helpful-section">
                <span class="helpful-text">Is this 3D plan helpful?</span>
                <div class="helpful-btns">
                    <button class="helpful-yes" onclick="handleHelpful('yes')">Yes</button>
                    <button class="helpful-no" onclick="handleHelpful('no')">No</button>
                    <button class="skip-btn" onclick="handleHelpful('skip')">Skip</button>
                </div>
            </div>
                    <div class="floorplan-image-wrapper">
                        <img id="popupFloorImage" 
                             src="/front/assets/images/3d.jpg" 
                             alt="Floor Plan 3D View"
                             onclick="openFloorImageFull()">
                        <!-- Vertical View Tabs on Image -->
                        <div class="floorplan-view-tabs">
                            <button class="view-tab active" onclick="switchView('3d')">3D</button>
                            <button class="view-tab" onclick="switchView('2d')">2D</button>
                        </div>
                    </div>
                    <!-- Meters | Feet toggle below image -->
                    <div class="floorplan-unit-toggle">
                        <span>meters</span>
                        <span class="active">feet</span>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Sellers -->
                <div class="floorplan-details-col">

                    <!-- Sellers Section -->
                    <div class="fp-sellers-section">
                        <div class="fp-seller-title">Sellers for <span id="sellerBhk">2 BHK Apartment</span> - <span id="sellerArea">840 - 855 sqft</span></div>

                        <!-- Seller Card 1 - Builder -->
                        <div class="fp-seller-card">
                            <div class="fp-seller-info">
                                <div class="fp-seller-icon" style="background:#e8f4fd; color:#1a73e8;">PB</div>
                                <div>
                                    <div class="fp-seller-name">Puranik Builders</div>
                                    <div class="fp-seller-type">Builder</div>
                                </div>
                            </div>
                            <button class="fp-view-contact-btn" onclick="viewContact('Puranik Builders')">View Contact</button>
                        </div>

                        <!-- Seller Card 2 - Dealer -->
                        <div class="fp-seller-card">
                            <div class="fp-seller-info">
                                <div class="fp-seller-icon" style="background:#fef3e2; color:#e67e22;">MR</div>
                                <div>
                                    <div class="fp-seller-name">Mine Realty</div>
                                    <div class="fp-seller-type">Dealer</div>
                                </div>
                            </div>
                            <button class="fp-view-contact-btn" onclick="viewContact('Mine Realty')">View Contact</button>
                        </div>

                        <!-- Seller Card 3 - RERA Registered Dealer -->
                        <div class="fp-seller-card">
                            <div class="fp-seller-info">
                                <div class="fp-seller-icon" style="background:#e8f5e9; color:#2e7d32;">ES</div>
                                <div>
                                    <div class="fp-seller-name">Elite Unique Services</div>
                                    <div class="fp-seller-type">RERA REGISTERED Dealer <span class="rera-badge">RERA</span></div>
                                </div>
                            </div>
                            <button class="fp-view-contact-btn" onclick="viewContact('Elite Unique Services')">View Contact</button>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Full Screen Image Overlay -->
    <div class="floor-image-overlay" id="floorImageOverlay" onclick="closeFloorImageFull()">
        <button class="floor-image-close" onclick="closeFloorImageFull()">✕</button>
        <img id="fullFloorImage" src="/front/assets/images/3d.jpg" alt="Floor Plan Full View">
    </div>

  <!-- ============ BELOW: Project Details / Amenities / Map ============ -->
<div class="details-full">
    <div class="container">
    <!-- Tabs -->
    <div class="row">
      <div class="col-12">
        <div class="listing-tabs-wrap" id="listingTabsWrap">
                    <ul class="listing-tabs">
          <?php if ($isHotelProject): ?>
            <li><a class="tab-link active" href="#project-details">About this</a></li>
            <li><a class="tab-link" href="#floor-plans">Choose your room</a></li>
            <li><a class="tab-link" href="#reviews">Ratings and Reviews</a></li>
            <li><a class="tab-link" href="#extra-details">Hotel Rules</a></li>
            <li><a class="tab-link" href="#maps">What's nearby</a></li>
          <?php else: ?>
                        <li><a class="tab-link active" href="#project-details"><?php echo ($isInteriorProject || $isVendorProject) ? 'Company Details' : 'Project Details'; ?></a></li>
            <li><a class="tab-link" href="#floor-plans">Floor Plans</a></li>
            <li class="<?php echo ($isInteriorProject || $isVendorProject) ? 'd-none' : ''; ?>"><a class="tab-link" href="#amenities">Amenities</a></li>
            <li><a class="tab-link" href="#extra-details">Extra Details</a></li>
            <?php /*<li><a class="tab-link" href="#address">Address</a></li>*/ ?>
            <li><a class="tab-link" href="#maps">Maps</a></li>
            <li><a class="tab-link" href="#faq">FAQ</a></li>
            <li><a class="tab-link" href="#reviews">Reviews</a></li>
          <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <!-- LEFT COLUMN -->
      <div class="col-lg-8">

        <div id="project-details" class="card-panel mb-4<?php echo $isLoggedIn ? ' editable-section' : ''; ?>" data-section="project-details" data-project-id="<?php echo (int) $project['id']; ?>">
                        <h3 class="mb-1" style="font-size:1.4rem;"><?php echo ($isInteriorProject || $isVendorProject) ? 'Company Details' : 'Project Details'; ?></h3>
                        <?php if ($isLoggedIn && !$isInteriorProject && !$isVendorProject): ?>
    <!-- Edit mode: Project details inputs -->
    <div class="edit-mode-only" style="display:none; margin-bottom:1rem; padding:1rem; background:var(--bg-soft); border-radius:0.5rem; border:1px solid var(--line);">
        <h5 style="font-size:0.85rem; font-weight:600; color:var(--muted); margin-bottom:0.75rem;">Edit Project Stats</h5>
        <div class="row g-2">
            <div class="col-md-3">
                <label style="font-size:0.7rem; font-weight:600; color:var(--muted);">Bedrooms</label>
                <input type="text" class="editable-input" data-field="bedrooms" value="<?php echo htmlspecialchars(pv($project, 'bedrooms')); ?>" style="width:100%;padding:0.4rem 0.6rem;border:1px solid var(--line);border-radius:0.4rem;font-size:0.85rem;">
            </div>
            <div class="col-md-3">
                <label style="font-size:0.7rem; font-weight:600; color:var(--muted);">Bathrooms</label>
                <input type="text" class="editable-input" data-field="bathrooms" value="<?php echo htmlspecialchars(pv($project, 'bathrooms')); ?>" style="width:100%;padding:0.4rem 0.6rem;border:1px solid var(--line);border-radius:0.4rem;font-size:0.85rem;">
            </div>
            <div class="col-md-3">
                <label style="font-size:0.7rem; font-weight:600; color:var(--muted);">Area</label>
                <input type="text" class="editable-input" data-field="area" value="<?php echo htmlspecialchars(pv($project, 'area')); ?>" style="width:100%;padding:0.4rem 0.6rem;border:1px solid var(--line);border-radius:0.4rem;font-size:0.85rem;">
            </div>
            <div class="col-md-3">
                <label style="font-size:0.7rem; font-weight:600; color:var(--muted);">Year Built</label>
                <input type="text" class="editable-input" data-field="year_built" value="<?php echo htmlspecialchars(pv($project, 'year_built')); ?>" style="width:100%;padding:0.4rem 0.6rem;border:1px solid var(--line);border-radius:0.4rem;font-size:0.85rem;">
            </div>
        </div>
    </div>
    <?php endif; ?>
            <div class="project-stats-row">
                        <div class="stat-chip">
              <div class="stat-icon"><i class="bi bi-door-closed"></i></div>
              <div class="stat-text">
                <div class="stat-label"><?php echo $isInteriorProject ? 'No. of Projects' : 'Bedrooms'; ?></div>
                <div class="stat-value editable-field" data-field="bedrooms" contenteditable="false"><?php echo htmlspecialchars(pv($project, 'bedrooms')); ?></div>
              </div>
            </div>
            <div class="stat-chip">
              <div class="stat-icon"><i class="bi bi-droplet-half"></i></div>
              <div class="stat-text">
                <div class="stat-label"><?php echo $isInteriorProject ? 'Available Locations in India' : 'Bathrooms'; ?></div>
                <div class="stat-value editable-field" data-field="bathrooms" contenteditable="false"><?php echo htmlspecialchars(pv($project, 'bathrooms')); ?></div>
              </div>
            </div>
            <div class="stat-chip">
              <div class="stat-icon"><i class="bi bi-rulers"></i></div>
              <div class="stat-text">
                <div class="stat-label">Area</div>
                <div class="stat-value editable-field" data-field="area" contenteditable="false"><?php echo htmlspecialchars(pv($project, 'area')); ?></div>
              </div>
            </div>
            <div class="stat-chip">
              <div class="stat-icon"><i class="bi bi-calendar3"></i></div>
              <div class="stat-text">
                <div class="stat-label"><?php echo $isInteriorProject ? 'Since' : 'Year Built'; ?></div>
                <div class="stat-value editable-field" data-field="year_built" contenteditable="false"><?php echo htmlspecialchars(pv($project, 'year_built')); ?></div>
              </div>
            </div>
          </div>

          <!-- About Project / Specifications tabs -->
          <ul class="nav project-detail-tabs" id="projectDetailTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="about-project-tab" data-bs-toggle="tab" data-bs-target="#about-project-pane" type="button" role="tab" aria-controls="about-project-pane" aria-selected="true"><?php echo ($isInteriorProject || $isVendorProject) ? 'About Company' : 'About Project'; ?></button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications-pane" type="button" role="tab" aria-controls="specifications-pane" aria-selected="false">Specifications</button>
            </li>
          </ul>

  <div class="tab-content project-detail-tab-content" id="projectDetailTabContent">

    <!-- ABOUT PROJECT -->
    <div class="tab-pane fade show active" id="about-project-pane" role="tabpanel" aria-labelledby="about-project-tab">
      <p class="about-project-text editable-field" data-field="about_project"><?php echo nl2br(htmlspecialchars($aboutProjectText)); ?></p>

      <?php if (!empty($features)): ?>
        <div class="spec-section-title"><?php echo ($isInteriorProject || $isVendorProject) ? 'Key Features' : 'Features & Amenities'; ?></div>
    <ul class="about-bullet-list">
      <?php foreach ($features as $feature): ?>
        <?php if (!empty($feature)): ?>
          <li><?php echo htmlspecialchars($feature); ?></li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ul>
<?php endif; ?>

      <?php if (!empty($phases)): ?>
        <div class="table-responsive">
          <table class="table phases-table">
            <thead>
              <tr>
                <th>Phases</th>
                <th>Possession Status</th>
                <th>Possession/Completion Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($phases as $phase): ?>
                <tr>
                  <td><?php echo htmlspecialchars($phase['name'] ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($phase['status'] ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($phase['date'] ?? ''); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <!-- SPECIFICATIONS -->
    <div class="tab-pane fade" id="specifications-pane" role="tabpanel" aria-labelledby="specifications-tab">
            <div class="spec-section-title">Overview</div>
      <div class="spec-overview-grid">
  <?php foreach ($specOverview as $field => $item): ?>
    <div class="spec-overview-item">
      <span class="spec-label"><?php echo htmlspecialchars($item['label']); ?></span>
      <span class="spec-value editable-field" data-field="<?php echo htmlspecialchars($field); ?>" contenteditable="false"><?php echo htmlspecialchars($item['value']); ?></span>
    </div>
  <?php endforeach; ?>
</div>

            <?php
      $specIcons = [
          'Flooring'           => 'bi-grid-3x3-gap',
          'Bathrooms'          => 'bi-droplet-half',
          'Kitchen'            => 'bi-cup-hot',
          'Doors and Windows'  => 'bi-door-open',
      ];
      ?>
      <div class="spec-accordion" id="specAccordion">
        <?php $i = 0; foreach ($specSections as $sectionTitle => $sectionText): ?>
          <?php
            $specId = 'spec-' . preg_replace('/[^a-z0-9]+/', '-', strtolower($sectionTitle));
            $specIcon = $specIcons[$sectionTitle] ?? 'bi-info-circle';
          ?>
          <div class="spec-accordion-item<?php echo $i === 0 ? ' open' : ''; ?>">
            <div class="spec-accordion-header"
                 data-bs-toggle="collapse"
                 data-bs-target="#<?php echo $specId; ?>"
                 role="button"
                 aria-expanded="<?php echo $i === 0 ? 'true' : 'false'; ?>"
                 aria-controls="<?php echo $specId; ?>">
              <span class="spec-icon"><i class="bi <?php echo $specIcon; ?>"></i></span>
              <span class="spec-title"><?php echo htmlspecialchars($sectionTitle); ?></span>
              <i class="bi bi-chevron-down spec-chevron"></i>
            </div>
            <div class="collapse <?php echo $i === 0 ? 'show' : ''; ?>" id="<?php echo $specId; ?>" data-bs-parent="#specAccordion">
              <div class="spec-accordion-body">
                <?php
//$specFieldMap = ['Flooring' => 'spec_flooring', 'Bathrooms' => 'spec_bathrooms', 'Kitchen' => 'spec_kitchen', 'Doors and Windows' => 'spec_doors_windows'];
?>
<p class="spec-text editable-field" data-field="<?php echo htmlspecialchars($specFieldMapGlobal[$sectionTitle] ?? ''); ?>" contenteditable="false"><?php echo nl2br(htmlspecialchars($sectionText)); ?></p>
              </div>
            </div>
          </div>
        <?php $i++; endforeach; ?>
      </div>
    </div>

</div>
<?php if ($isLoggedIn): ?>
    <div class="mt-3" style="border-top:1px solid var(--line); padding-top:0.75rem;">
        <button type="button" class="section-save-btn"><i class="bi bi-save"></i> Save Project Details</button>
        <span class="section-save-msg"></span>
    </div>
    <?php endif; ?>
</div>
       <!-- ===== FLOOR PLANS SECTION WITH 3D IMAGES ===== -->
<div id="floor-plans" class="card-panel mb-4">
    
    <!-- ===== PRICE RANGE SECTION ===== -->
    <div class="price-range-wrapper mb-4">
        
        <!-- Header - Price and Brochure Button on Same Row -->
        <div class="price-range-header" style="padding: 0.5rem 0 0.75rem 0; text-align: left; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
            <!-- Left side: Labels and Price -->
            <div style="display: flex; align-items: baseline; gap: 0.75rem; flex-wrap: wrap;">
                <span style="font-size: 0.7rem; font-weight: 700; color: var(--muted); letter-spacing: 0.08em; text-transform: uppercase;">PRICE RANGE</span>
                <span style="font-size: 0.85rem; font-weight: 500; color: var(--muted);">|</span>
                <span style="font-size: 0.9rem; font-weight: 600; color: var(--ink);"><?php echo htmlspecialchars($project['project_name']); ?></span>
                <?php 
                $firstPlan = $floorPlans[0] ?? null;
                $firstVariant = $firstPlan['variants'][0] ?? null;
                if ($firstVariant):
                ?>
                    <span style="font-size: 1.1rem; font-weight: 600; color: var(--primary, #c9a84c);">
                        <?php if (!empty($firstVariant['price_range'])): ?>
                            <?php echo htmlspecialchars($firstVariant['price_range']); ?>
                            <span style="font-size: 0.7rem; font-weight: 400; color: var(--muted);">+ Charges</span>
                        <?php elseif (!empty($firstVariant['price'])): ?>
                            <?php echo htmlspecialchars($firstVariant['price']); ?>
                        <?php endif; ?>
                    </span>
                <?php endif; ?>
            </div>

            <!-- Right side: Download Brochure Button -->
    <div style="flex-shrink: 0; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
        <?php if ($isLoggedIn): ?>
            <!-- Edit mode: Brochure upload/update -->
            <div class="edit-mode-only" style="display:none; display:flex; align-items:center; gap:0.5rem;">
                <form id="brochureUploadForm" style="display:flex; align-items:center; gap:0.5rem;">
                    <input type="hidden" name="project_id" value="<?php echo (int) $project['id']; ?>">
                    <input type="file" name="brochure" accept=".pdf" style="font-size:0.7rem; padding:0.2rem; border:1px solid var(--line); border-radius:0.3rem;">
                    <button type="submit" class="btn btn-sm" style="background:#c9a84c; color:#fff; border:none; border-radius:0.3rem; padding:0.2rem 0.8rem; font-size:0.7rem; font-weight:600; cursor:pointer;">
                        <i class="bi bi-upload"></i> Upload
                    </button>
                </form>
                <?php if ($brochurePath): ?>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteBrochure(<?php echo (int) $project['id']; ?>)" style="border:none; border-radius:0.3rem; padding:0.2rem 0.8rem; font-size:0.7rem; font-weight:600; cursor:pointer; background:#dc3545; color:#fff;">
                        <i class="bi bi-trash"></i> Remove
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <!-- Download Brochure Button -->
        <?php if ($brochurePath): ?>
            <a href="<?php echo htmlspecialchars($brochurePath); ?>" target="_blank" class="btn btn-download-brochure" style="background: var(--primary, #c9a84c); color: #fff; border: none; border-radius: 2rem; padding: 0.4rem 1.5rem; font-weight: 600; font-size: 0.8rem; transition: all 0.2s ease; text-decoration: none; display: inline-flex; align-items: center; white-space: nowrap;">
                <i class="bi bi-file-pdf me-1"></i> Download Brochure
            </a>
        <?php else: ?>
            <span style="font-size:0.7rem; color:var(--muted); font-style:italic;">No brochure available</span>
            <?php if ($isLoggedIn): ?>
                <span style="font-size:0.65rem; color:#c9a84c;">(Upload one in edit mode)</span>
            <?php endif; ?>
        <?php endif; ?>
    </div>
        </div>

                                                        <?php if (!$isInteriorProject && !$isVendorProject && !$isHotelProject && !empty($floorPlans)): ?>  
            <!-- Tab Navigation - Compact -->
            <ul class="nav nav-pills price-variant-tabs mb-2" id="priceVariantTabs" role="tablist" style="border-bottom: 1px solid var(--line); padding-bottom: 0.4rem; gap: 0.3rem;">
                <?php foreach ($floorPlans as $index => $floorPlan): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo $index === 0 ? 'active' : ''; ?>" 
                                id="price-tab-<?php echo $index; ?>" 
                                data-bs-toggle="pill" 
                                data-bs-target="#price-panel-<?php echo $index; ?>" 
                                type="button" 
                                role="tab" 
                                aria-controls="price-panel-<?php echo $index; ?>" 
                                aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                                style="padding: 0.3rem 1rem; font-size: 0.8rem;">
                            <?php echo htmlspecialchars($floorPlan['bhk']); ?>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Tab Panels -->
            <div class="tab-content" id="priceVariantTabContent">
                <?php foreach ($floorPlans as $index => $floorPlan): ?>
                    <div class="tab-pane fade <?php echo $index === 0 ? 'show active' : ''; ?> price-variant-tab-panel" 
                         id="price-panel-<?php echo $index; ?>" 
                         role="tabpanel" 
                         aria-labelledby="price-tab-<?php echo $index; ?>"
                         style="padding-top: 0.5rem;">
                        
                        <?php if (!empty($floorPlan['variants'])): ?>
                            <?php $chunks = array_chunk($floorPlan['variants'], 3); ?>
                            <div class="price-variant-slider-wrapper" style="padding: 0.25rem 0;">
                                <div class="price-variant-slider" id="priceVariantSlider-<?php echo $index; ?>" style="gap: 0.75rem;">
                                    <?php foreach ($chunks as $chunk): ?>
                                        <div class="price-variant-slide" style="gap: 0.75rem;">
                                            <?php foreach ($chunk as $variant): ?>
                                                <div class="price-variant-card" style="padding: 0.6rem 0.8rem; gap: 0.35rem; background: var(--bg-soft); border-radius: 0.6rem; border: 1px solid var(--line); transition: all 0.3s ease;">
                                                    
                                                    
                                                    <!-- Area + Unit Dropdown - Same Row -->
                                                    <div class="variant-area" style="text-align: center;">
                                                        <span class="area-value" id="area-<?php echo md5(($floorPlan['bhk'] ?? '') . ($variant['carpet_area_sqft'] ?? '')); ?>" style="font-size: 0.8rem; font-weight: 600;">
                                                            <?php echo htmlspecialchars($variant['carpet_area_sqft'] ?? ''); ?> sqft
                                                        </span>
                                                        <select class="area-unit-select"
                                                                data-area="<?php echo htmlspecialchars($variant['carpet_area_sqft'] ?? ''); ?>"
                                                                data-sqm="<?php echo htmlspecialchars($variant['carpet_area_sqm'] ?? ''); ?>"
                                                                style="border:1px solid var(--line); border-radius:0.3rem; padding:0.1rem 0.3rem; font-size:0.5rem; background:transparent; color:var(--muted); cursor:pointer; margin-left: 0.3rem;">
                                                            <option value="sqft" selected>sqft</option>
                                                            <option value="sqyd">sqyd</option>
                                                            <option value="sqm">sqm</option>
                                                            <option value="grounds">grounds</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <!-- SQM Display -->
                                                    <?php if (!empty($variant['carpet_area_sqm'])): ?>
                                                    <div style="text-align: center; font-size: 0.6rem; color: var(--muted); margin-top: -0.1rem;">
                                                        (<?php echo htmlspecialchars($variant['carpet_area_sqm']); ?> sqm)
                                                    </div>
                                                    <?php endif; ?>

                                                    <!-- Price -->
                                                    <div class="variant-price-info" style="text-align: center; margin-top: 0.2rem;">
                                                        <?php if (!empty($variant['price_range'])): ?>
                                                            <span class="price-value" style="font-size: 0.9rem; font-weight: 700; color: var(--primary, #c9a84c);">
                                                                <?php echo htmlspecialchars($variant['price_range']); ?>
                                                            </span>
                                                            <span class="price-label-small" style="font-size: 0.5rem; color: var(--muted);">+ Charges</span>
                                                        <?php elseif (!empty($variant['price'])): ?>
                                                            <span class="price-value" style="font-size: 0.9rem; font-weight: 700; color: var(--primary, #c9a84c);">
                                                                <?php echo htmlspecialchars($variant['price']); ?>
                                                            </span>
                                                        <?php endif; ?>
                                                        <?php if (!$isVendorProject): ?>
                                                        <div style="font-size: 0.5rem; color:var(--muted); margin-top: 0.05rem;">
                                                            Under Construction            
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <!-- Slider Controls - Compact -->
                                <?php if (count($chunks) > 1): ?>
                                    <div class="price-variant-slider-controls" style="margin-top: 0.25rem; gap: 0.5rem; display: flex; justify-content: center; align-items: center;">
                                        <button class="price-slider-prev" data-slider="priceVariantSlider-<?php echo $index; ?>" style="width: 26px; height: 26px; border-radius: 50%; border: 1px solid var(--line); background: var(--bg-soft); color: var(--ink); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-chevron-left" style="font-size: 0.7rem;"></i>
                                        </button>
                                        <div class="price-variant-dots" id="priceDots-<?php echo $index; ?>" style="gap: 0.25rem; display: flex;">
                                            <?php foreach ($chunks as $dotIndex => $chunk): ?>
                                                <span class="dot <?php echo $dotIndex === 0 ? 'active' : ''; ?>" 
                                                      data-slider="priceVariantSlider-<?php echo $index; ?>" 
                                                      data-index="<?php echo $dotIndex; ?>"
                                                      style="width: 6px; height: 6px; border-radius: 50%; background: var(--muted); cursor: pointer; transition: all 0.3s ease;"></span>
                                            <?php endforeach; ?>
                                        </div>
                                        <button class="price-slider-next" data-slider="priceVariantSlider-<?php echo $index; ?>" style="width: 26px; height: 26px; border-radius: 50%; border: 1px solid var(--line); background: var(--bg-soft); color: var(--ink); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-chevron-right" style="font-size: 0.7rem;"></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

                                                                <?php elseif (!$isInteriorProject && !$isVendorProject && !$isHotelProject): ?>
            <p class="text-muted">No price range available for this property.</p>
        <?php endif; ?>
    </div>

    <!-- ===== ORIGINAL FLOOR PLANS SLIDER ===== -->
    <hr class="my-3">
    
    <h3 class="mb-2" style="font-size:1.2rem;">Floor Plans</h3>
    
    <?php if (!empty($floorPlans)): ?>
        <!-- Tab Navigation - Compact -->
        <ul class="nav nav-pills floor-plan-tabs mb-2" id="floorPlanTabs" role="tablist" style="border-bottom: 1px solid var(--line); padding-bottom: 0.4rem; gap: 0.3rem;">
            <?php foreach ($floorPlans as $index => $floorPlan): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?php echo $index === 0 ? 'active' : ''; ?>" 
                            id="tab-<?php echo $index; ?>" 
                            data-bs-toggle="pill" 
                            data-bs-target="#panel-<?php echo $index; ?>" 
                            type="button" 
                            role="tab" 
                            aria-controls="panel-<?php echo $index; ?>" 
                            aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                            style="padding: 0.3rem 1rem; font-size: 0.8rem;">
                        <?php echo htmlspecialchars($floorPlan['bhk']); ?>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- Tab Panels -->
        <div class="tab-content" id="floorPlanTabContent">
            <?php foreach ($floorPlans as $index => $floorPlan): ?>
                <div class="tab-pane fade <?php echo $index === 0 ? 'show active' : ''; ?>" 
                     id="panel-<?php echo $index; ?>" 
                     role="tabpanel" 
                     aria-labelledby="tab-<?php echo $index; ?>"
                     style="padding-top: 0.5rem;">
                    
                    <?php if (!empty($floorPlan['variants'])): ?>
                        <div class="floor-plan-slider-wrapper" style="padding: 0.25rem 0;">
                            <div class="floor-plan-slider" id="floorPlanSlider-<?php echo $index; ?>" style="display: flex; gap: 0.75rem; transition: transform 0.4s ease;">
                                <?php foreach ($floorPlan['variants'] as $variantIndex => $variant): ?>
                                    <div class="floor-plan-slide" style="flex: 0 0 calc(33.33% - 0.5rem); min-width: 200px;">
                                        <div class="floor-plan-card" style="padding: 0.6rem; gap: 0.3rem; background: var(--bg-soft); border-radius: 0.6rem; border: 1px solid var(--line);">
                                            
                                            
                                                                                        <div class="floor-plan-info" style="text-align: center;">
                                                <?php if (!empty($variant['carpet_area_sqft'])): ?>
                                                    <div class="area-info" style="font-size: 0.8rem; font-weight: 600;">
                                                        <?php if ($isInteriorProject): ?>
                                                            <strong><?php echo htmlspecialchars($variant['carpet_area_sqft']); ?></strong>
                                                        <?php else: ?>
                                                            <strong><?php echo htmlspecialchars($variant['carpet_area_sqft']); ?> sqft</strong>
                                                            <span class="text-muted" style="font-size: 0.7rem; font-weight: 400;">(<?php echo htmlspecialchars($variant['carpet_area_sqm'] ?? ''); ?> sqm)</span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                          
                                            <!-- ===== 3D FLOOR PLAN IMAGE ===== -->
                                            <div style="text-align: center; margin-bottom: 0.3rem; cursor: pointer;" 
                                                 onclick="openFloorplanPopup(<?php echo $index; ?>, <?php echo $variantIndex; ?>);">
                                                <?php 
                                                $fp_image = !empty($variant['floor_plan_image']) ? $variant['floor_plan_image'] : '';
                                                // Check if image exists in DB and file exists
                                                if ($fp_image && file_exists($fp_image)) {
                                                    $img_src = $fp_image;
                                                } else {
                                                    // Use default 3D image from assets
                                                    $img_src = '/front/assets/images/3d.jpg';
                                                }
                                                ?>
                                                <img src="<?php echo htmlspecialchars($img_src); ?>" 
                                                     alt="<?php echo htmlspecialchars($floorPlan['bhk']); ?> 3D Floor Plan"
                                                     style="width: 100%; max-width: 180px; height: auto; border-radius: 0.4rem; border: 1px solid var(--line); background: #fff; object-fit: cover; aspect-ratio: 4/3;"
                                                     onerror="this.src='/front/assets/images/3d.jpg'"
                                                     onmouseover="this.style.transform='scale(1.05)'"
                                                     onmouseout="this.style.transform='scale(1)'">
                                            </div>
                                            
                                            <div class="floor-plan-pricing" style="text-align: center; gap: 0.15rem;">
                                                <?php if (!empty($variant['price_range'])): ?>
                                                    <div class="price-range">
                                                        <strong style="font-size: 0.9rem; color: var(--primary, #c9a84c);"><?php echo htmlspecialchars($variant['price_range']); ?></strong>
                                                        <span style="font-size: 0.5rem; color: var(--muted);">+ Charges</span>
                                                    </div>
                                                <?php elseif (!empty($variant['price'])): ?>
                                                    <div class="price">
                                                        <strong style="font-size: 0.9rem; color: var(--primary, #c9a84c);"><?php echo htmlspecialchars($variant['price']); ?></strong>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!$isVendorProject): ?>
                                                <?php if (!empty($variant['status']) || !empty($variant['possession_date'])): ?>
                                                    <div class="status-info" style="display: flex; justify-content: center; gap: 0.3rem; flex-wrap: wrap;">
                                                        <?php if (!empty($variant['status'])): ?>
                                                            <span class="badge" style="background: #f0b42920; color: #f0b429; font-size: 0.55rem; padding: 0.15rem 0.5rem; border-radius: 1rem; font-weight: 600;"><?php echo htmlspecialchars($variant['status']); ?></span>
                                                        <?php endif; ?>
                                                        <?php if (!empty($variant['possession_date'])): ?>
                                                            <span class="possession text-muted" style="font-size: 0.65rem;"><?php echo htmlspecialchars($variant['possession_date']); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                            
                                                                                        <div class="floor-plan-action" style="padding-top: 0.2rem; display: flex; flex-direction: column; gap: 0.35rem; align-items: center;">
    <?php if ($isHotelProject): ?>
    <a href="#" class="btn btn-sm btn-outline-primary callback-btn room-select-btn"
       onclick="event.preventDefault(); toggleRoomWishlist(this, <?php echo (int) $project['id']; ?>, <?php echo json_encode($floorPlan['bhk']); ?>, <?php echo json_encode($variant['carpet_area_sqft'] ?? ''); ?>);"
       data-bhk="<?php echo htmlspecialchars($floorPlan['bhk']); ?>"
       data-area="<?php echo htmlspecialchars($variant['carpet_area_sqft'] ?? ''); ?>"
       style="padding: 0.15rem 1rem; font-size: 0.6rem; border-radius: 2rem; border: 1px solid var(--primary, #c9a84c); color: var(--primary, #c9a84c); background: transparent; text-decoration: none; cursor: pointer; transition: all 0.3s ease;">
        Selected
    </a>
    <?php else: ?>
    <a href="#" class="btn btn-sm btn-outline-primary callback-btn" 
       onclick="<?php echo $isVendorProject ? 'addToCart(' . $index . ', ' . $variantIndex . ')' : 'event.preventDefault(); openFloorplanPopup(' . $index . ', ' . $variantIndex . ');'; ?>"
       data-bhk="<?php echo htmlspecialchars($floorPlan['bhk']); ?>"
       data-area="<?php echo htmlspecialchars($variant['carpet_area_sqft'] ?? ''); ?>"
       style="padding: 0.15rem 1rem; font-size: 0.6rem; border-radius: 2rem; border: 1px solid var(--primary, #c9a84c); color: var(--primary, #c9a84c); background: transparent; text-decoration: none; cursor: pointer; transition: all 0.3s ease;"
       onmouseover="this.style.background='var(--primary, #c9a84c)'; this.style.color='#fff';"
       onmouseout="this.style.background='transparent'; this.style.color='var(--primary, #c9a84c)';">
        <?php echo $isVendorProject ? 'Add to Cart' : 'Request Callback'; ?>
    </a>
    <?php endif; ?>
</div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Slider Controls - Compact -->
                            <?php if (count($floorPlan['variants']) > 1): ?>
                                <div class="floor-plan-slider-controls" style="margin-top: 0.25rem; gap: 0.5rem; display: flex; justify-content: center; align-items: center;">
                                    <button class="slider-prev" data-slider="floorPlanSlider-<?php echo $index; ?>" style="width: 26px; height: 26px; border-radius: 50%; border: 1px solid var(--line); background: var(--bg-soft); color: var(--ink); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-chevron-left" style="font-size: 0.7rem;"></i>
                                    </button>
                                    <div class="slider-dots" id="dots-<?php echo $index; ?>" style="gap: 0.25rem; display: flex;">
                                        <?php foreach ($floorPlan['variants'] as $dotIndex => $variant): ?>
                                            <span class="dot <?php echo $dotIndex === 0 ? 'active' : ''; ?>" 
                                                  data-slider="floorPlanSlider-<?php echo $index; ?>" 
                                                  data-index="<?php echo $dotIndex; ?>"
                                                  style="width: 6px; height: 6px; border-radius: 50%; background: var(--muted); cursor: pointer; transition: all 0.3s ease;"></span>
                                        <?php endforeach; ?>
                                    </div>
                                    <button class="slider-next" data-slider="floorPlanSlider-<?php echo $index; ?>" style="width: 26px; height: 26px; border-radius: 50%; border: 1px solid var(--line); background: var(--bg-soft); color: var(--ink); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-chevron-right" style="font-size: 0.7rem;"></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-muted">No floor plans available for this property.</p>
    <?php endif; ?>
</div>

                            <?php if (!$isInteriorProject && !$isVendorProject): ?>
        <div id="amenities" class="card-panel mb-4<?php echo $isLoggedIn ? ' editable-section' : ''; ?>" data-section="amenities" data-project-id="<?php echo (int) $project['id']; ?>">
          <h3 class="mb-3" style="font-size:1.4rem;">Amenities</h3>
            <?php if ($isLoggedIn): ?>
            <!-- Edit mode: Textarea for amenities -->
            <div class="edit-mode-only" style="<?php echo !$isLoggedIn ? 'display:none;' : ''; ?>">
                <label style="font-size:0.8rem; font-weight:600; color:var(--muted);">Edit Amenities (comma-separated)</label>
                <textarea class="editable-input" data-field="amenities" placeholder="Enter amenities separated by commas" style="min-height:80px;margin-bottom:1rem;width:100%;padding:0.5rem;border:1px solid var(--line);border-radius:0.5rem;"><?php echo htmlspecialchars($project['amenities'] ?? ''); ?></textarea>
            </div>
            <?php endif; ?>
          <?php
            $amenities = !empty($project['amenities'])
                ? array_values(array_filter(array_map('trim', explode(',', $project['amenities']))))
                : [];
          ?>
          <?php if ($amenities): ?>
            <?php
              $visibleCount     = 11;
              $visibleAmenities = array_slice($amenities, 0, $visibleCount);
              $extraAmenities   = array_slice($amenities, $visibleCount);
              $extraCount       = count($extraAmenities);
            ?>
            <div class="amenity-grid">
              <?php foreach ($visibleAmenities as $a): ?>
                <div class="amenity-icon-card" title="<?php echo htmlspecialchars($a); ?>">
                  <div class="icon-circle"><i class="bi <?php echo amenity_icon($a); ?>"></i></div>
                  <div class="label"><?php echo htmlspecialchars($a); ?></div>
                </div>
              <?php endforeach; ?>

              <?php if ($extraCount > 0): ?>
                <div class="amenity-extra" id="amenityExtra">
                  <?php foreach ($extraAmenities as $a): ?>
                    <div class="amenity-icon-card" title="<?php echo htmlspecialchars($a); ?>">
                      <div class="icon-circle"><i class="bi <?php echo amenity_icon($a); ?>"></i></div>
                      <div class="label"><?php echo htmlspecialchars($a); ?></div>
                    </div>
                  <?php endforeach; ?>
                </div>

                <div class="amenity-more-card" id="amenityMoreBtn">
                  <span class="more-label">+<?php echo $extraCount; ?> More</span>
                  <i class="bi bi-chevron-down"></i>
                </div>
              <?php endif; ?>
            </div>
          <?php else: ?>
            <span class="text-muted">No amenities listed.</span>
          <?php endif; ?>
          <!-- ===== EXTRA DETAILS SECTION ===== -->
          <div class="card-panel mt-2 mb-4">
              <h4 class="mb-2" style="font-size:1.1rem; font-weight:600; color:var(--ink);">Extra Details</h4>
              <?php if ($isLoggedIn): ?>
        <!-- Edit mode inputs for extra details -->
        <div class="edit-mode-only" style="<?php echo !$isLoggedIn ? 'display:none;' : ''; ?>">
            <div class="row g-2 mb-2">
                <div class="col-md-6">
                    <label style="font-size:0.75rem; font-weight:600; color:var(--muted);">Furnished Status</label>
                    <input type="text" class="editable-input" data-field="furnished" value="<?php echo htmlspecialchars(pv($project, 'furnished')); ?>" style="width:100%;padding:0.4rem 0.6rem;border:1px solid var(--line);border-radius:0.4rem;font-size:0.85rem;">
                </div>
                <div class="col-md-6">
                    <label style="font-size:0.75rem; font-weight:600; color:var(--muted);">Plot Area/Total Area</label>
                    <input type="text" class="editable-input" data-field="plot_area" value="<?php echo htmlspecialchars(pv($project, 'plot_area')); ?>" style="width:100%;padding:0.4rem 0.6rem;border:1px solid var(--line);border-radius:0.4rem;font-size:0.85rem;">
                </div>
            </div>
        </div>
        <?php endif; ?>
              <!-- Display mode (always visible) -->
              <div class="row g-2">
                  <div class="col-6 col-md-4 detail-item">
                      <div class="icon"><i class="bi bi-arrow-repeat"></i></div>
                      <div class="label">Is it furnished?</div>
                      <div class="value editable-field" data-field="furnished" contenteditable="false"><?php echo htmlspecialchars(pv($project, 'furnished')); ?></div>
                  </div>
                  <div class="col-6 col-md-4 detail-item">
                      <div class="icon"><i class="bi bi-bounding-box"></i></div>
                      <div class="label">Plot Area/Total Area</div>
                      <div class="value editable-field" data-field="plot_area" contenteditable="false"><?php echo htmlspecialchars(pv($project, 'plot_area')); ?></div>
                  </div>
              </div>
                <hr class="my-2">
                <div class="small text-muted">
                    Listed on <?php echo htmlspecialchars(pv($project, 'created_at', 'N/A')); ?>
                    <?php if (!empty($project['updated_at'])): ?>
                        &middot; Last updated <?php echo htmlspecialchars($project['updated_at']); ?>
                    <?php endif; ?>
                </div>

                                <?php if ($isLoggedIn): ?>
                <div class="mt-2">
                    <button type="button" class="section-save-btn"><i class="bi bi-save"></i> Save Amenities & Details</button>
                    <span class="section-save-msg"></span>
                </div>
                <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php
/* =========================================================================
   REPLACEMENT for the "Company Details" block.
   Replace everything from:
       <div id="extra-details" class="card-panel mb-4">
   down to the matching closing </div> (right before the <style> block),
   AND replace the old <style> rules for .company-* with the ones below.
   ========================================================================= */

// Get company data from project or use defaults
$hasFilledForm = isset($_SESSION['form_filled']) && $_SESSION['form_filled'] === true;

//$companyName  = 'Estate Hub';
$companyName  = pv($project, 'company_name', 'Estate Hub');
$projectName  = $project['project_name'] ?? 'Kalpataru Vian';
$mobileNumber = !empty($project['owner_phone']) ? $project['owner_phone'] : '+91 9876513210';
$email1       = !empty($project['owner_email']) ? $project['owner_email'] : 'contact.kalpataruvian@gmail.com';
$email2       = !empty($project['owner_email2']) ? $project['owner_email2'] : 'kalpataruvian@gmail.com';
$emailDisplay = !empty($project['owner_email_display']) ? $project['owner_email_display'] : 'info@kalpataruvian.com';
$website          = !empty($project['website']) ? $project['website'] : 'https://estatehub.com';
$fullAddress      = !empty($project['address']) ? $project['address'] : 'Plot no.1234, South Andheri, Mumbai';
$checkInTime      = !empty($project['check_in_time'])  ? $project['check_in_time']  : '12:00 PM';
$checkOutTime     = !empty($project['check_out_time']) ? $project['check_out_time'] : '11:00 AM';
$isWebsiteOnline  = !empty($project['website_online']) ? $project['website_online'] : false;
// When the builder's own site isn't online, we fall back to showing our own listing URL for the project
$listingFallbackUrl = 'https://tatitodevelopersandpromoters.com/front/property-listing/' . (function_exists('slugify') ? slugify($projectName) : strtolower(preg_replace('/[^a-z0-9]+/i', '-', $projectName)));

// Mask the mobile number until the enquiry form has been filled
function mask_mobile($number) {
    $digits = preg_replace('/\D/', '', $number);
    if (strlen($digits) < 6) return $number;
    $visibleStart = substr($digits, 0, strlen($digits) - 8) . substr($digits, -8, 2);
    $masked = substr($digits, 0, strlen($digits) - 8) . substr($digits, -8, 2) . str_repeat('X', 4) . substr($digits, -2);
    return '+91 ' . $masked;
}
$mobileDisplay = $hasFilledForm ? $mobileNumber : mask_mobile($mobileNumber);
?>

<div id="extra-details" class="card-panel mb-4<?php echo $isLoggedIn ? ' editable-section' : ''; ?>" data-section="company-details" data-project-id="<?php echo (int) $project['id']; ?>">
    <h3 class="mb-3" style="font-size:1.4rem;">Company Details</h3>
    <?php if ($isLoggedIn): ?>
    <!-- Edit mode: Company details inputs -->
    <div class="edit-mode-only" style="display:none; margin-bottom:1rem; padding:1rem; background:var(--bg-soft); border-radius:0.5rem; border:1px solid var(--line);">
        <h5 style="font-size:0.85rem; font-weight:600; color:var(--muted); margin-bottom:0.75rem;">Edit Company Details</h5>
        <div class="row g-2">
            <?php if (!$isInteriorProject): ?>
            <div class="col-md-6">
                <label style="font-size:0.7rem; font-weight:600; color:var(--muted);">Company Name</label>
                <input type="text" class="editable-input" data-field="company_name" value="<?php echo htmlspecialchars($companyName); ?>" ...>
            </div>
            <?php endif; ?>
            <div class="col-md-6">
                <label style="font-size:0.7rem; font-weight:600; color:var(--muted);">Project Name</label>
                <input type="text" class="editable-input" data-field="project_name" value="<?php echo htmlspecialchars($projectName); ?>" style="width:100%;padding:0.4rem 0.6rem;border:1px solid var(--line);border-radius:0.4rem;font-size:0.85rem;">
            </div>
            <div class="col-md-6">
                <label style="font-size:0.7rem; font-weight:600; color:var(--muted);">Mobile Number</label>
                <input type="text" class="editable-input" data-field="owner_phone" value="<?php echo htmlspecialchars($mobileNumber); ?>" ...>
            </div>
            <div class="col-md-6">
                <label style="font-size:0.7rem; font-weight:600; color:var(--muted);">Full Address</label>
                <input type="text" class="editable-input" data-field="address" value="<?php echo htmlspecialchars($fullAddress); ?>" style="width:100%;padding:0.4rem 0.6rem;border:1px solid var(--line);border-radius:0.4rem;font-size:0.85rem;">
            </div>
            <div class="col-md-6">
                <label style="font-size:0.7rem; font-weight:600; color:var(--muted);">Email 1</label>
                <input type="text" class="editable-input" data-field="owner_email" value="<?php echo htmlspecialchars($email1); ?>" style="width:100%;padding:0.4rem 0.6rem;border:1px solid var(--line);border-radius:0.4rem;font-size:0.85rem;">
            </div>
            <div class="col-md-6">
                <label style="font-size:0.7rem; font-weight:600; color:var(--muted);">Email 2</label>
                <input type="text" class="editable-input" data-field="owner_email2" value="<?php echo htmlspecialchars($email2); ?>" style="width:100%;padding:0.4rem 0.6rem;border:1px solid var(--line);border-radius:0.4rem;font-size:0.85rem;">
            </div>
            <div class="col-md-6">
                <label style="font-size:0.7rem; font-weight:600; color:var(--muted);">Email Display</label>
                <input type="text" class="editable-input" data-field="owner_email_display" value="<?php echo htmlspecialchars($emailDisplay); ?>" style="width:100%;padding:0.4rem 0.6rem;border:1px solid var(--line);border-radius:0.4rem;font-size:0.85rem;">
            </div>
                        <div class="col-md-6">
                <label style="font-size:0.7rem; font-weight:600; color:var(--muted);">Website URL</label>
                <input type="text" class="editable-input" data-field="website" value="<?php echo htmlspecialchars($website); ?>" style="width:100%;padding:0.4rem 0.6rem;border:1px solid var(--line);border-radius:0.4rem;font-size:0.85rem;">
            </div>
            <?php if ($isHotelProject): ?>
            <div class="col-md-6">
                <label style="font-size:0.7rem; font-weight:600; color:var(--muted);">Check-in Time</label>
                <input type="text" class="editable-input" data-field="check_in_time" value="<?php echo htmlspecialchars($checkInTime); ?>" style="width:100%;padding:0.4rem 0.6rem;border:1px solid var(--line);border-radius:0.4rem;font-size:0.85rem;">
            </div>
            <div class="col-md-6">
                <label style="font-size:0.7rem; font-weight:600; color:var(--muted);">Check-out Time</label>
                <input type="text" class="editable-input" data-field="check_out_time" value="<?php echo htmlspecialchars($checkOutTime); ?>" style="width:100%;padding:0.4rem 0.6rem;border:1px solid var(--line);border-radius:0.4rem;font-size:0.85rem;">
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    <div class="company-details-grid">

        <!-- ===== LEFT COLUMN ===== -->
        <div class="company-col">
<?php if (!$isInteriorProject): ?>
            <div class="company-field">
                <div class="company-field-label"><i class="bi bi-building-fill"></i> Company Name</div>
                <div class="company-field-value editable-field" data-field="company_name" contenteditable="false">
                    <?php echo htmlspecialchars($companyName); ?>
                    <span class="company-status-badge">Verified</span>
                </div>
            </div>
<?php endif; ?>
            <div class="company-field">
                <div class="company-field-label"><i class="bi bi-phone-fill"></i> Mobile Number</div>
                <div class="company-field-value editable-field" data-field="owner_phone" contenteditable="false">
                    <?php if ($hasFilledForm): ?>
                        <?php echo htmlspecialchars($mobileDisplay); ?>
                    <?php else: ?>
                        <span class="company-mobile-masked">
                            <i class="bi bi-lock-fill"></i> <?php echo htmlspecialchars($mobileDisplay); ?>
                        </span>
                        <button type="button" class="company-unlock-inline-btn" onclick="openContactBuilder()">Fill form to view</button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="company-field">
                <div class="company-field-label"><i class="bi bi-globe2"></i> Web URL</div>
                <div class="company-field-value editable-field" data-field="website" contenteditable="false">
                    <?php if ($isWebsiteOnline): ?>
                        <a href="<?php echo htmlspecialchars($website); ?>" target="_blank" class="company-website-link">
                            <?php echo htmlspecialchars($website); ?> <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                    <?php else: ?>
                        <span class="company-website-offline">
                            <i class="bi bi-exclamation-circle"></i> Builder site is not online
                        </span>
                        <div class="company-website-fallback">
                            <span class="company-website-hint">Showing our listing URL instead:</span>
                            <a href="<?php echo htmlspecialchars($listingFallbackUrl); ?>" class="company-website-link">
                                <?php echo htmlspecialchars($listingFallbackUrl); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

                        <div class="company-field">
                <div class="company-field-label"><i class="bi bi-geo-alt-fill"></i> Full Address</div>
                <div class="company-field-value editable-field" data-field="address" contenteditable="false"><?php echo htmlspecialchars($fullAddress); ?></div>
            </div>

            <?php if ($isHotelProject): ?>
            <div class="company-field">
                <div class="company-field-label"><i class="bi bi-box-arrow-in-right"></i> Check-in</div>
                <div class="company-field-value editable-field" data-field="check_in_time" contenteditable="false"><?php echo htmlspecialchars($checkInTime); ?></div>
            </div>
            <div class="company-field">
                <div class="company-field-label"><i class="bi bi-box-arrow-right"></i> Check-out</div>
                <div class="company-field-value editable-field" data-field="check_out_time" contenteditable="false"><?php echo htmlspecialchars($checkOutTime); ?></div>
            </div>
            <?php endif; ?>

        </div>

        <!-- ===== RIGHT COLUMN ===== -->
        <div class="company-col">

            <div class="company-field">
                <div class="company-field-label"><i class="bi bi-house-fill"></i> Project Name</div>
                <div class="company-field-value editable-field" data-field="project_name" contenteditable="false"><?php echo htmlspecialchars($projectName); ?></div>
            </div>

            <div class="company-field">
                <div class="company-field-label"><i class="bi bi-envelope-fill"></i> Email Address</div>
                <div class="company-field-value company-email-stack">
                    <div class="editable-field" data-field="owner_email" contenteditable="false"><a href="mailto:<?php echo htmlspecialchars($email1); ?>"><?php echo htmlspecialchars($email1); ?></a></div>
                    <div class="company-email-divider">and</div>
                    <div class="editable-field" data-field="owner_email2" contenteditable="false"><a href="mailto:<?php echo htmlspecialchars($email2); ?>"><?php echo htmlspecialchars($email2); ?></a></div>
                    <div class="company-email-domain editable-field" data-field="owner_email_display" contenteditable="false">
                        <i class="bi bi-arrow-right"></i> <a href="mailto:<?php echo htmlspecialchars($emailDisplay); ?>"><?php echo htmlspecialchars($emailDisplay); ?></a>
                    </div>
                </div>
            </div>

            <div class="company-contact-note-box">
                <div class="company-contact-info">
                    <i class="bi bi-info-circle-fill"></i>
                    <span>Contact number shown after filling the form</span>
                </div>
                <div class="company-email-note">
                    <i class="bi bi-envelope-paper-fill"></i>
                    <span>Email details shown at all times</span>
                </div>
            </div>

        </div>

    </div>
    <?php if ($isLoggedIn): ?>
    <div class="mt-3" style="border-top:1px solid var(--line); padding-top:0.75rem;">
        <button type="button" class="section-save-btn"><i class="bi bi-save"></i> Save Company Details</button>
        <span class="section-save-msg"></span>
    </div>
    <?php endif; ?>
</div>

<style>
/* ===== COMPANY DETAILS — 2 COLUMN LAYOUT ===== */
/* ===== COMPANY DETAILS — 2 COLUMN LAYOUT ===== */
.company-details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0 2rem;
    border: 1px solid var(--line, #e8ecf0);
    border-radius: 0.75rem;
    padding: 1.25rem 1.5rem;
}

.company-col {
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
}

.company-col:first-child {
    border-right: 1px solid var(--line, #e8ecf0);
    padding-right: 2rem;
}

.company-field {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.company-field-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--muted, #6b7a8f);
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.company-field-label i {
    color: #c9a84c;
    font-size: 0.85rem;
}

.company-field-value {
    font-size: 0.92rem;
    font-weight: 600;
    color: var(--ink, #12213a);
    word-break: break-word;
}

.company-status-badge {
    display: inline-block;
    font-size: 0.65rem;
    font-weight: 700;
    color: #1a8f4c;
    background: #e6f6ec;
    padding: 0.1rem 0.5rem;
    border-radius: 1rem;
    margin-left: 0.4rem;
    vertical-align: middle;
}

.company-mobile-masked {
    color: var(--muted, #6b7a8f);
    letter-spacing: 0.05em;
}

.company-unlock-inline-btn {
    background: #c9a84c;
    color: #fff;
    border: none;
    border-radius: 0.35rem;
    padding: 0.2rem 0.6rem;
    font-size: 0.72rem;
    font-weight: 600;
    margin-left: 0.5rem;
    cursor: pointer;
    transition: background 0.2s ease;
}

.company-unlock-inline-btn:hover {
    background: #b8963a;
}

.company-website-link {
    color: #c9a84c;
    text-decoration: none;
    font-weight: 600;
}

.company-website-link:hover {
    text-decoration: underline;
}

.company-website-offline {
    color: #e74c3c;
    font-size: 0.85rem;
    font-weight: 500;
}

.company-website-fallback {
    margin-top: 0.3rem;
    font-size: 0.8rem;
}

.company-website-hint {
    display: block;
    color: var(--muted, #6b7a8f);
    font-size: 0.72rem;
    margin-bottom: 0.1rem;
}

.company-email-stack {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}

.company-email-stack a {
    color: var(--ink, #12213a);
    text-decoration: none;
    transition: color 0.2s ease;
}

.company-email-stack a:hover {
    color: #c9a84c;
}

.company-email-divider {
    font-size: 0.72rem;
    color: var(--muted, #6b7a8f);
    font-style: italic;
}

.company-email-domain {
    font-size: 0.85rem;
    color: #c9a84c;
    margin-top: 0.15rem;
}

.company-contact-note-box {
    margin-top: auto;
    padding: 0.75rem;
    background: var(--bg-soft, #f7f8fa);
    border-radius: 0.5rem;
    border-left: 3px solid #c9a84c;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    font-size: 0.78rem;
    color: var(--muted, #6b7a8f);
}

.company-contact-note-box i {
    color: #c9a84c;
    margin-right: 0.3rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .company-details-grid {
        grid-template-columns: 1fr;
        gap: 1.25rem 0;
        padding: 1rem;
    }
    
    .company-col:first-child {
        border-right: none;
        border-bottom: 1px solid var(--line, #e8ecf0);
        padding-right: 0;
        padding-bottom: 1.25rem;
    }
}

@media (max-width: 480px) {
    .company-field-value { font-size: 0.85rem; }
    .company-field-label { font-size: 0.72rem; }
}
</style>

        <?php /*<div id="address" class="card-panel mb-4">
          <h3 class="mb-2" style="font-size:1.4rem;">Address</h3>
          <div class="d-flex align-items-start gap-2">
            <i class="bi bi-geo-alt-fill text-primary mt-1"></i>
            <div><?php echo htmlspecialchars(pv($project, 'address', 'N/A')); ?></div>
          </div>
        </div>*/ ?>

        <div id="maps" class="card-panel">
          <h3 class="mb-1" style="font-size:1.4rem;">Property's Location</h3>
          
          <p class="text-muted mb-3">Discover the exact location and surroundings of the project.</p>
          <div class="map-wrap">
            <div id="map"></div>
            <button type="button" class="map-view-btn" data-bs-toggle="modal" data-bs-target="#nearbyMapModal">
              <i class="bi bi-geo-alt-fill"></i> View on Map
            </button>
          </div>
        </div>
        
        
      </div>

      <!-- RIGHT COLUMN (Report) -->
      <div class="col-lg-4">
        <?php include __DIR__ . '/right-col.php'; ?>
        
      </div>
      
    </div>
      
      <div class="row mt-4">
        <!-- ===== FAQ & REVIEWS SECTION ===== -->
        <?php include __DIR__ . '/faq-reviews.php'; ?>
      </div>
      
    </div>
</div>

<?php endif; ?>
<!-- ===== REPORT INAPPROPRIATE CONTENT POPUP ===== -->
<div class="report-overlay" id="reportOverlay">
    <div class="report-popup">
        <button class="report-popup-close" onclick="closeReportPopup()">✕</button>
        
        <div class="report-popup-header">
            <h3 style="margin:0; font-size:1.2rem; font-weight:700; color:var(--ink); display:flex; align-items:center; gap:0.5rem;">
                <i class="bi bi-flag-fill" style="color:#e74c3c;"></i> Report Inappropriate Content
            </h3>
        </div>
        
        <div class="report-popup-body">
            <?php
            // Check if user is logged in - adjust this according to your auth system
            $isLoggedIn = isset($_SESSION['user_id']) || isset($_SESSION['user_email']);
            echo '<!-- DEBUG isLoggedIn=' . var_export($isLoggedIn, true) . ' session=' . htmlspecialchars(json_encode($_SESSION ?? [])) . ' -->';
            ?>
            <!-- Auth Note - Always visible, but now with proper styling -->
            <div class="auth-note mb-3" style="background:<?php echo $isLoggedIn ? '#e6f6ec' : '#fdeef0'; ?>; color:<?php echo $isLoggedIn ? '#1a8f4c' : '#b6435a'; ?>; font-size:0.85rem; border-radius:0.6rem; padding:0.6rem 0.8rem; display:flex; align-items:flex-start; gap:0.5rem;">
                <i class="bi <?php echo $isLoggedIn ? 'bi-check-circle-fill' : 'bi-exclamation-circle'; ?> me-1" style="margin-top:0.1rem;"></i>
                <div>
                    <strong><?php echo $isLoggedIn ? 'You are logged in' : 'Authentication Required'; ?></strong><br>
                    <?php echo $isLoggedIn ? 'You can submit a report.' : 'Please log in to submit a report.'; ?>
                </div>
            </div>
            
            <form id="reportForm" method="POST" action="<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); ?>/submit-report.php">
                <input type="hidden" name="project_id" value="<?php echo $project['id'] ?? 0; ?>">
                <input type="hidden" name="project_name" value="<?php echo htmlspecialchars($project['project_name'] ?? ''); ?>">
                
                <!-- Reason -->
                <div class="form-group" style="margin-bottom:1rem;">
                    <label style="font-weight:600; font-size:0.9rem; display:block; margin-bottom:0.3rem; color:var(--ink);">Reason for reporting <span style="color:#e74c3c;">*</span></label>
                    <select name="report_reason" id="reportReason" required style="width:100%; padding:0.6rem 0.9rem; border:1px solid var(--line); border-radius:0.6rem; font-size:0.9rem; background:var(--bg-soft); color:var(--ink); cursor:pointer;">
                        <option value="">Select a reason</option>
                        <option value="Misleading Information">Misleading Information</option>
                        <option value="Inappropriate Content">Inappropriate Content</option>
                        <option value="Spam or Scam">Spam or Scam</option>
                        <option value="Fake Listing">Fake Listing</option>
                        <option value="Copyright Issue">Copyright Issue</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <!-- Additional Details -->
                <div class="form-group" style="margin-bottom:1.2rem;">
                    <label style="font-weight:600; font-size:0.9rem; display:block; margin-bottom:0.3rem; color:var(--ink);">Additional Details</label>
                    <textarea name="report_details" id="reportDetails" rows="3" placeholder="Please provide more details about your report..." style="width:100%; padding:0.6rem 0.9rem; border:1px solid var(--line); border-radius:0.6rem; font-size:0.9rem; resize:vertical; transition:border-color 0.3s ease; background:var(--bg-soft); color:var(--ink); min-height:80px;"></textarea>
                </div>
                
                <!-- Submit -->
                <div id="reportMessage"></div>
                <button type="submit" class="report-submit-btn" style="background:#c9a84c; color:#fff; border:none; border-radius:0.6rem; padding:0.75rem; font-weight:700; font-size:1rem; width:100%; cursor:pointer; transition:all 0.3s ease; display:flex; align-items:center; justify-content:center; gap:0.5rem;">
                    <i class="bi bi-flag"></i> Submit Report
                </button>
            </form>
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
    
<style>
/* ===== FLOOR PLAN POPUP - FULL WIDTH ===== */
.floorplan-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(4px);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    padding: 0;
}

.floorplan-overlay.active {
    display: flex;
}

.floorplan-popup-full {
    background: var(--card-bg, #fff);
    border-radius: 1rem;
    max-width: 100%;
    width: 100%;
    max-height: 92vh;
    overflow-y: auto;
    padding: 1.5rem 2rem 1.25rem;
    position: relative;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

/* ===== HEADER - 3 COLUMN LAYOUT ===== */
.floorplan-popup-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    margin-bottom: 0.75rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--line, #e8ecf0);
    flex-wrap: nowrap;
}

/* COL 1: Title Section with Dropdown */
.popup-title-section {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    flex: 0 0 auto;
    min-width: 0;
}

.popup-title-label {
    font-size: 0.65rem;
    font-weight: 600;
    color: var(--muted, #6b7a8f);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.popup-title-dropdown-wrapper {
    position: relative;
}

.popup-bhk-dropdown {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--ink, #12213a);
    background: transparent;
    border: none;
    border-bottom: 2px solid var(--primary, #c9a84c);
    padding: 0.1rem 1.8rem 0.1rem 0;
    cursor: pointer;
    appearance: none;
    -webkit-appearance: none;
    outline: none;
    font-family: inherit;
    min-width: 180px;
}

.popup-bhk-dropdown:hover {
    border-bottom-color: #b8963a;
}

.popup-bhk-dropdown:focus {
    border-bottom-color: var(--primary, #c9a84c);
}

.popup-title-dropdown-wrapper::after {
    content: '\f282';
    font-family: 'bootstrap-icons';
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    font-size: 0.9rem;
    color: var(--primary, #c9a84c);
    pointer-events: none;
}

/* COL 2: Area Info Tabs - Only area info */
.popup-bhk-tabs {
    display: flex;
    gap: 0.4rem;
    flex-wrap: wrap;
    flex: 1 1 auto;
    justify-content: flex-start;
    align-items: flex-start;
}

.popup-bhk-tab {
    padding: 0.3rem 0.9rem;
    border: 1.5px solid var(--line, #e8ecf0);
    border-radius: 2rem;
    font-size: 0.7rem;
    font-weight: 600;
    background: transparent;
    color: var(--muted, #6b7a8f);
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.popup-bhk-tab:hover {
    border-color: var(--primary, #c9a84c);
    color: var(--ink, #12213a);
    background: rgba(201, 168, 76, 0.08);
}

.popup-bhk-tab.active {
    background: var(--primary, #c9a84c);
    color: #fff;
    border-color: var(--primary, #c9a84c);
    box-shadow: 0 2px 12px rgba(201, 168, 76, 0.35);
}

.popup-bhk-tab .tab-area {
    font-weight: 500;
    font-size: 0.65rem;
}

.popup-bhk-tab .tab-sqm {
    font-weight: 400;
    font-size: 0.55rem;
    opacity: 0.7;
}

.popup-bhk-tab.active .tab-sqm {
    opacity: 1;
}

/* COL 3: Close Button - Right Aligned */
.popup-header-right {
    display: flex;
    align-items: center;
    flex-shrink: 0;
    padding-left: 0.5rem;
}

.floorplan-popup-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    color: var(--muted, #6b7a8f);
    cursor: pointer;
    padding: 0.3rem 0.5rem;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    flex-shrink: 0;
}

.floorplan-popup-close:hover {
    background: var(--bg-soft, #f7f8fa);
    color: var(--ink, #12213a);
}

.floorplan-popup-close i {
    font-size: 1.2rem;
}

/* ===== RESPONSIVE - Tablet ===== */
@media (max-width: 992px) {
    .floorplan-popup-full {
        padding: 1.25rem 1.5rem;
    }
    
    .floorplan-popup-header {
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    
    .popup-title-section {
        flex: 1 1 100%;
    }
    
    .popup-bhk-tabs {
        flex: 1 1 100%;
        justify-content: flex-start;
        gap: 0.3rem;
    }
    
    .popup-header-right {
        position: absolute;
        top: 0.75rem;
        right: 1rem;
        padding-left: 0;
    }
}

/* ===== RESPONSIVE - Mobile ===== */
@media (max-width: 768px) {
    .floorplan-popup-full {
        padding: 1rem 1rem 0.75rem;
        max-width: 100%;
        border-radius: 0.75rem;
        max-height: 95vh;
    }
    
    .floorplan-popup-header {
        flex-direction: row;
        flex-wrap: wrap;
        gap: 0.5rem;
        padding-bottom: 0.4rem;
        padding-right: 2.5rem;
    }
    
    .popup-title-section {
        flex: 1 1 100%;
    }
    
    .popup-title-label {
        font-size: 0.55rem;
    }
    
    .popup-bhk-dropdown {
        font-size: 0.9rem;
        min-width: 140px;
        padding-right: 1.5rem;
    }
    
    .popup-bhk-tabs {
        flex: 1 1 100%;
        justify-content: flex-start;
        gap: 0.25rem;
    }
    
    .popup-bhk-tab {
        font-size: 0.6rem;
        padding: 0.2rem 0.7rem;
        border-radius: 1.5rem;
    }
    
    .popup-bhk-tab .tab-area {
        font-size: 0.55rem;
    }
    
    .popup-bhk-tab .tab-sqm {
        font-size: 0.5rem;
    }
    
    .popup-header-right {
        position: absolute;
        top: 0.5rem;
        right: 0.75rem;
    }
    
    .floorplan-popup-close {
        width: 32px;
        height: 32px;
        font-size: 1rem;
    }
    
    .floorplan-popup-close i {
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .floorplan-popup-full {
        padding: 0.75rem 0.75rem 0.5rem;
        max-height: 98vh;
        border-radius: 0.5rem;
    }
    
    .floorplan-popup-header {
        gap: 0.3rem;
        padding-right: 2rem;
    }
    
    .popup-title-label {
        font-size: 0.5rem;
    }
    
    .popup-bhk-dropdown {
        font-size: 0.8rem;
        min-width: 120px;
        padding-right: 1.2rem;
    }
    
    .popup-bhk-tab {
        font-size: 0.5rem;
        padding: 0.12rem 0.5rem;
    }
    
    .popup-bhk-tab .tab-area {
        font-size: 0.5rem;
    }
    
    .popup-bhk-tab .tab-sqm {
        font-size: 0.45rem;
    }
    
    .popup-header-right {
        top: 0.4rem;
        right: 0.5rem;
    }
    
    .floorplan-popup-close {
        width: 28px;
        height: 28px;
        font-size: 0.85rem;
    }
    
    .floorplan-popup-close i {
        font-size: 0.85rem;
    }
}
    
    /* ===== RESPONSIVE - Tablet ===== */
@media (max-width: 992px) {
    .floorplan-popup-full {
        padding: 1.25rem 1.5rem;
    }
    
    .floorplan-popup-header {
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    
    .popup-title-section {
        flex: 1 1 100%;
    }
    
    .popup-bhk-tabs {
        flex: 1 1 100%;
        justify-content: flex-start;
        gap: 0.3rem;
    }
    
    .popup-header-right {
        position: absolute;
        top: 0.75rem;
        right: 1rem;
        padding-left: 0;
    }
}

/* ===== RESPONSIVE - Mobile ===== */
@media (max-width: 768px) {
    .floorplan-popup-full {
        padding: 1rem 1rem 0.75rem;
        max-width: 100%;
        border-radius: 0.75rem;
        max-height: 95vh;
    }
    
    .floorplan-popup-header {
        flex-direction: row;
        flex-wrap: wrap;
        gap: 0.5rem;
        padding-bottom: 0.4rem;
        padding-right: 2.5rem;
    }
    
    .popup-title-section {
        flex: 1 1 100%;
    }
    
    .popup-title-label {
        font-size: 0.55rem;
    }
    
    .popup-bhk-dropdown {
        font-size: 0.9rem;
        min-width: 140px;
        padding-right: 1.5rem;
    }
    
    .popup-bhk-tabs {
        flex: 1 1 100%;
        justify-content: flex-start;
        gap: 0.25rem;
    }
    
    .popup-bhk-tab {
        font-size: 0.6rem;
        padding: 0.2rem 0.7rem;
        border-radius: 1.5rem;
    }
    
    .popup-bhk-tab .tab-area {
        font-size: 0.55rem;
    }
    
    .popup-bhk-tab .tab-sqm {
        font-size: 0.5rem;
    }
    
    .popup-header-right {
        position: absolute;
        top: 0.5rem;
        right: 0.75rem;
    }
    
    .floorplan-popup-close {
        width: 32px;
        height: 32px;
        font-size: 1rem;
    }
    
    .floorplan-popup-close i {
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .floorplan-popup-full {
        padding: 0.75rem 0.75rem 0.5rem;
        max-height: 98vh;
        border-radius: 0.5rem;
    }
    
    .floorplan-popup-header {
        gap: 0.3rem;
        padding-right: 2rem;
    }
    
    .popup-title-label {
        font-size: 0.5rem;
    }
    
    .popup-bhk-dropdown {
        font-size: 0.8rem;
        min-width: 120px;
        padding-right: 1.2rem;
    }
    
    .popup-bhk-tab {
        font-size: 0.5rem;
        padding: 0.12rem 0.5rem;
    }
    
    .popup-bhk-tab .tab-area {
        font-size: 0.5rem;
    }
    
    .popup-bhk-tab .tab-sqm {
        font-size: 0.45rem;
    }
    
    .popup-header-right {
        top: 0.4rem;
        right: 0.5rem;
    }
    
    .floorplan-popup-close {
        width: 28px;
        height: 28px;
        font-size: 0.85rem;
    }
    
    .floorplan-popup-close i {
        font-size: 0.85rem;
    }
}

/* ===== TOP ROW: PRICE, POSSESSION & VIEW NUMBER ===== */
.floorplan-top-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem 0.75rem;
    margin-bottom: 0.75rem;
    background: var(--bg-soft, #f7f8fa);
    gap: 1rem;
}

.floorplan-top-left {
    display: flex;
    align-items: center;
    gap: 2rem;
}

.floorplan-top-right {
    flex-shrink: 0;
}

.fp-detail-item {
    display: flex;
    flex-direction: column;
    gap: 0.05rem;
}

.fp-detail-label {
    font-size: 0.6rem;
    font-weight: 600;
    color: var(--muted, #6b7a8f);
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.fp-detail-value {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--ink, #12213a);
}

.fp-price-value {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--primary, #c9a84c);
}

.fp-price-value .charges {
    font-size: 0.65rem;
    font-weight: 400;
    color: var(--muted, #6b7a8f);
}

/* View Number Button */
.fp-view-number-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1.5rem;
    background: #c9a84c;
    border: 1px solid var(--line, #e8ecf0);
    border-radius: 0.5rem;
    font-size: 0.85rem;
    font-weight: 600;
    color: #fff;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.fp-view-number-btn:hover {
    border-color: var(--primary, #c9a84c);
    background: var(--bg-soft, #f0f1f3);
}

.fp-view-number-btn i {
    font-size: 0.7rem;
    color: var(--muted, #6b7a8f);
}

/* ===== HELPFUL SECTION - ABOVE IMAGE ===== */
.floorplan-helpful-section {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    padding: 0.4rem 0.75rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.helpful-text {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--ink, #12213a);
}

.helpful-btns {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.helpful-btns button {
    padding: 0.2rem 1rem;
    border-radius: 0.4rem;
    border: 1px solid var(--line, #e8ecf0);
    font-size: 0.75rem;
    font-weight: 500;
    background: transparent;
    color: var(--ink, #12213a);
    cursor: pointer;
    transition: all 0.3s ease;
}

.helpful-btns button:hover {
    border-color: var(--primary, #c9a84c);
    background: var(--primary, #c9a84c);
    color: #fff;
}

.helpful-btns .helpful-yes:hover {
    border-color: #22c55e;
    background: #22c55e;
    color: #fff;
}

.helpful-btns .helpful-no:hover {
    border-color: #e74c3c;
    background: #e74c3c;
    color: #fff;
}

.helpful-btns .skip-btn {
    color: var(--muted, #6b7a8f);
}

.helpful-btns .skip-btn:hover {
    border-color: var(--muted, #6b7a8f);
    background: var(--muted, #6b7a8f);
    color: #fff;
}

/* ===== BODY: 2-COLUMN ===== */
.floorplan-popup-body-full {
    display: flex;
    gap: 2rem;
}

/* LEFT COLUMN - Image */
.floorplan-image-col {
    flex: 0 0 65%;
    max-width: 65%;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.floorplan-image-wrapper {
    position: relative;
    background: var(--bg-soft, #f7f8fa);
    border-radius: 0.75rem;
    overflow: hidden;
    border: 1px solid var(--line, #e8ecf0);
    cursor: pointer;
    aspect-ratio: 16/10;
    display: flex;
    align-items: center;
    justify-content: center;
}

.floorplan-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: transform 0.3s ease;
}

.floorplan-image-wrapper img:hover {
    transform: scale(1.02);
}

.floorplan-image-wrapper::after {
    content: 'Click to enlarge';
    position: absolute;
    bottom: 4rem;
    right: 0.75rem;
    background: rgba(0,0,0,0.6);
    color: #fff;
    font-size: 0.6rem;
    padding: 0.15rem 0.5rem;
    border-radius: 0.3rem;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 2;
}

.floorplan-image-wrapper:hover::after {
    opacity: 1;
}

/* Vertical View Tabs on Image - Right Bottom */
.floorplan-view-tabs {
    position: absolute;
    bottom: 1rem;
    right: 0.75rem;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
    z-index: 3;
}

.view-tab {
    padding: 0.2rem 0.7rem;
    border: 1px solid rgba(255,255,255,0.3);
    border-radius: 0.3rem;
    font-size: 0.6rem;
    font-weight: 600;
    background: rgba(0,0,0,0.5);
    color: rgba(255,255,255,0.7);
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(4px);
    letter-spacing: 0.05em;
}

.view-tab:hover {
    background: rgba(255,255,255,0.2);
    color: #fff;
}

.view-tab.active {
    background: var(--primary, #c9a84c);
    color: #fff;
    border-color: var(--primary, #c9a84c);
}

/* Meters | Feet toggle below image */
.floorplan-unit-toggle {
    display: flex;
    gap: 0.15rem;
    font-size: 0.6rem;
    font-weight: 600;
    color: var(--muted, #6b7a8f);
    align-self: flex-end;
}

.floorplan-unit-toggle span {
    padding: 0.1rem 0.5rem;
    border-radius: 0.2rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.floorplan-unit-toggle span:hover {
    color: var(--ink, #12213a);
}

.floorplan-unit-toggle span.active {
    background: var(--primary, #c9a84c);
    color: #fff;
}

/* RIGHT COLUMN - Sellers */
.floorplan-details-col {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
}

/* ===== SELLERS SECTION ===== */
.fp-sellers-section {
    padding-top: 0.25rem;
}

.fp-seller-title {
    font-size: 0.7rem;
    font-weight: 600;
    color: var(--muted, #6b7a8f);
    margin-bottom: 0.5rem;
}

.fp-seller-title span {
    color: var(--ink, #12213a);
}

.fp-seller-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem 0.6rem;
    border-radius: 0.5rem;
    background: var(--bg-soft, #f7f8fa);
    margin-bottom: 0.3rem;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.fp-seller-card:hover {
    background: var(--bg-soft, #f0f1f3);
    border-color: var(--line, #e8ecf0);
}

.fp-seller-info {
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.fp-seller-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    font-weight: 700;
    flex-shrink: 0;
}

.fp-seller-name {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--ink, #12213a);
}

.fp-seller-type {
    font-size: 0.55rem;
    color: var(--muted, #6b7a8f);
    display: flex;
    align-items: center;
    gap: 0.3rem;
    flex-wrap: wrap;
}

.rera-badge {
    background: #22c55e;
    color: #fff;
    padding: 0.05rem 0.4rem;
    border-radius: 0.2rem;
    font-size: 0.45rem;
    font-weight: 700;
    letter-spacing: 0.03em;
}

.fp-view-contact-btn {
    padding: 0.2rem 0.7rem;
    border: 1px solid var(--primary, #c9a84c);
    border-radius: 0.3rem;
    font-size: 0.6rem;
    font-weight: 600;
    color: var(--primary, #c9a84c);
    background: transparent;
    cursor: pointer;
    transition: all 0.3s ease;
    flex-shrink: 0;
    white-space: nowrap;
}

.fp-view-contact-btn:hover {
    background: var(--primary, #c9a84c);
    color: #fff;
}

/* ===== RESPONSIVE - Tablet ===== */
@media (max-width: 992px) {
    .floorplan-popup-full {
        padding: 1.25rem 1.5rem;
    }
    
    .floorplan-popup-header {
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    
    .popup-title-section {
        flex: 1 1 100%;
    }
    
    .popup-bhk-tabs {
        flex: 1 1 100%;
        justify-content: flex-start;
        gap: 0.3rem;
    }
    
    .popup-header-right {
        position: absolute;
        top: 0.75rem;
        right: 1rem;
        padding-left: 0;
    }
    
    .floorplan-popup-body-full {
        flex-direction: column;
        gap: 1rem;
    }
    
    .floorplan-image-col {
        flex: 1 1 auto;
        max-width: 100%;
    }
    
    .floorplan-image-wrapper {
        aspect-ratio: 16/9;
    }
    
    .floorplan-image-wrapper::after {
        display: none;
    }
    
    .floorplan-top-row {
        flex-wrap: wrap;
    }
    
    .floorplan-top-left {
        flex-wrap: wrap;
        gap: 1rem;
    }
}

/* ===== RESPONSIVE - Mobile ===== */
@media (max-width: 768px) {
    .floorplan-popup-full {
        padding: 1rem 1rem 0.75rem;
        max-width: 100%;
        border-radius: 0.75rem;
        max-height: 95vh;
    }
    
    .floorplan-popup-header {
        flex-direction: row;
        flex-wrap: wrap;
        gap: 0.5rem;
        padding-bottom: 0.4rem;
        padding-right: 2.5rem;
    }
    
    .popup-title-section {
        flex: 1 1 100%;
    }
    
    .popup-title-label {
        font-size: 0.55rem;
    }
    
    .popup-title-bhk {
        font-size: 0.9rem;
    }
    
    .popup-bhk-tabs {
        flex: 1 1 100%;
        justify-content: flex-start;
        gap: 0.25rem;
    }
    
    .popup-bhk-tab {
        font-size: 0.6rem;
        padding: 0.2rem 0.7rem;
        border-radius: 1.5rem;
    }
    
    .popup-bhk-tab .tab-area {
        font-size: 0.5rem;
        display: inline;
    }
    
    .popup-header-right {
        position: absolute;
        top: 0.5rem;
        right: 0.75rem;
    }
    
    .floorplan-popup-close {
        width: 32px;
        height: 32px;
        font-size: 1rem;
    }
    
    .floorplan-popup-close i {
        font-size: 1rem;
    }
    
    .floorplan-popup-body-full {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .floorplan-image-col {
        flex: 1 1 auto;
        max-width: 100%;
    }
    
    .floorplan-image-wrapper {
        aspect-ratio: 4/3;
    }
    
    .fp-seller-card {
        flex-wrap: wrap;
        gap: 0.3rem;
        padding: 0.4rem 0.5rem;
    }
    
    .fp-seller-info {
        flex: 1;
        min-width: 0;
    }
    
    .fp-seller-icon {
        width: 32px;
        height: 32px;
        font-size: 0.6rem;
    }
    
    .fp-seller-name {
        font-size: 0.7rem;
    }
    
    .fp-view-contact-btn {
        padding: 0.15rem 0.6rem;
        font-size: 0.55rem;
    }
    
    .floorplan-top-row {
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem;
    }
    
    .floorplan-top-left {
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .floorplan-top-right {
        width: 100%;
    }
    
    .fp-view-number-btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .floorplan-popup-full {
        padding: 0.75rem 0.75rem 0.5rem;
        max-height: 98vh;
        border-radius: 0.5rem;
    }
    
    .floorplan-popup-header {
        gap: 0.3rem;
        padding-right: 2rem;
    }
    
    .popup-title-label {
        font-size: 0.5rem;
    }
    
    .popup-title-bhk {
        font-size: 0.8rem;
    }
    
    .popup-bhk-tab {
        font-size: 0.5rem;
        padding: 0.15rem 0.5rem;
    }
    
    .popup-bhk-tab .tab-area {
        font-size: 0.45rem;
    }
    
    .popup-header-right {
        top: 0.4rem;
        right: 0.5rem;
    }
    
    .floorplan-popup-close {
        width: 28px;
        height: 28px;
        font-size: 0.85rem;
    }
    
    .floorplan-popup-close i {
        font-size: 0.85rem;
    }
    
    .fp-seller-card {
        flex-direction: column;
        align-items: stretch;
        gap: 0.3rem;
        padding: 0.4rem;
    }
    
    .fp-seller-info {
        gap: 0.4rem;
    }
    
    .fp-seller-icon {
        width: 28px;
        height: 28px;
        font-size: 0.55rem;
    }
    
    .fp-seller-name {
        font-size: 0.65rem;
    }
    
    .fp-seller-type {
        font-size: 0.5rem;
    }
    
    .fp-view-contact-btn {
        width: 100%;
        justify-content: center;
        padding: 0.25rem;
        font-size: 0.55rem;
    }
    
    .fp-detail-value {
        font-size: 0.85rem;
    }
    
    .fp-price-value {
        font-size: 0.95rem;
    }
}

/* ===== FULL SCREEN IMAGE OVERLAY ===== */
.floor-image-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.9);
    z-index: 10000;
    justify-content: center;
    align-items: center;
    padding: 2rem;
}

.floor-image-overlay.active {
    display: flex;
}

.floor-image-overlay img {
    max-width: 90%;
    max-height: 85vh;
    object-fit: contain;
    border-radius: 0.5rem;
}

.floor-image-close {
    position: absolute;
    top: 1.5rem;
    right: 2rem;
    background: rgba(255,255,255,0.2);
    border: none;
    color: #fff;
    font-size: 2rem;
    cursor: pointer;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.floor-image-close:hover {
    background: rgba(255,255,255,0.3);
}
</style>
    
<!-- ===== ALL SCRIPTS AT THE BOTTOM (jQuery FIRST) ===== -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// ===== GALLERY FUNCTIONS =====
var currentImageIndex = 0;
var map; // global reference so save handlers elsewhere can move the marker

// ===== SWAP MAIN IMAGE ON THUMBNAIL CLICK =====
window.swapMainImage = function(el, index) {
    // Update main image
    var mainImg = document.getElementById('mainImage');
    if (mainImg) {
        mainImg.src = el.src;
        mainImg.alt = el.alt || 'Property Image';
    }
    
    // Update active thumbnail
    document.querySelectorAll('.thumb').forEach(function(t) {
        t.classList.remove('active');
    });
    el.classList.add('active');
    
    // Update current index
    currentImageIndex = index || 0;
    
    // Update gallery data if available
    if (typeof galleryData !== 'undefined' && galleryData && galleryData.items) {
        var imageSrc = el.src;
        var foundIndex = -1;
        galleryData.items.forEach(function(item, idx) {
            if (item.image === imageSrc) {
                foundIndex = idx;
            }
        });
        if (foundIndex !== -1) {
            galleryData.currentIndex = foundIndex;
        }
    }
};

// ===== OPEN GALLERY MODAL =====
window.openEnhancedGallery = function(index) {
    var modal = new bootstrap.Modal(document.getElementById('enhancedGalleryModal'));
    var startIndex = (index !== undefined) ? index : currentImageIndex;
    galleryData.currentIndex = startIndex;
    updateGalleryImage();
    renderThumbnails();
    modal.show();
    resetZoom();
};

// ===== SHARE FUNCTIONALITY =====
function shareProperty() {
    var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('shareModal'));
    modal.show();
}

function shareVia(platform) {
    var url = encodeURIComponent('<?php echo htmlspecialchars('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>');
    var title = encodeURIComponent('<?php echo htmlspecialchars($project['project_name']); ?> - Property for Sale');
    var text = encodeURIComponent('Check out this amazing property: <?php echo htmlspecialchars($project['project_name']); ?> in <?php echo htmlspecialchars(pv($project, 'address', '')); ?>');
    
    var shareUrl = '';
    
    switch(platform) {
        case 'whatsapp':
            shareUrl = 'https://api.whatsapp.com/send?text=' + text + '%20' + url;
            break;
        case 'facebook':
            shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + url + '&quote=' + text;
            break;
        case 'twitter':
            shareUrl = 'https://twitter.com/intent/tweet?text=' + text + '&url=' + url;
            break;
        case 'email':
            shareUrl = 'mailto:?subject=' + title + '&body=' + text + '%0A%0A' + url;
            break;
        default:
            return;
    }
    
    if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=500');
    }
}

function copyShareLink() {
    var input = document.getElementById('shareLink');
    if (!input) return;
    input.select();
    input.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    var btn = input.nextElementSibling;
    if (btn) {
        var originalText = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check"></i> Copied!';
        setTimeout(function() {
            btn.innerHTML = originalText;
        }, 2000);
    }
}

// ===== WISHLIST FUNCTIONALITY =====
function toggleWishlist(element) {
    var icon = element.querySelector('i');
    var isWishlisted = element.classList.contains('wishlisted');
    var projectId = <?php echo (int) $project['id']; ?>;

    if (isWishlisted) {
        removeFromWishlist(projectId, element, icon);
    } else {
        addToWishlist(projectId, element, icon);
    }
}

function addToWishlist(projectId, element, icon) {
    var key = typeof getTatitoStorageKey === 'function' ? getTatitoStorageKey('wishlist') : 'tatito_guest_wishlist';
    var list = [];
    try { list = JSON.parse(localStorage.getItem(key)) || []; } catch (e) { list = []; }
    
    var idNum = Number(projectId);
    var exists = list.some(function(item) {
        var itemId = (item && item.id != null) ? Number(item.id) : Number(item);
        return itemId === idNum;
    });

    if (!exists) {
        list.unshift({ id: idNum, type: 'property' });
        try { localStorage.setItem(key, JSON.stringify(list)); } catch (e) {}
    }
    element.classList.add('wishlisted');
    if (icon) {
        icon.className = 'bi bi-heart-fill';
        icon.style.color = '#ff4757';
    }
    showWishlistToast('Added to wishlist ❤️', 'success');
    document.dispatchEvent(new CustomEvent('wishlistUpdated'));
}

function removeFromWishlist(projectId, element, icon) {
    var key = typeof getTatitoStorageKey === 'function' ? getTatitoStorageKey('wishlist') : 'tatito_guest_wishlist';
    var list = [];
    try { list = JSON.parse(localStorage.getItem(key)) || []; } catch (e) { list = []; }
    
    var idNum = Number(projectId);
    list = list.filter(function(item) {
        var itemId = (item && item.id != null) ? Number(item.id) : Number(item);
        return itemId !== idNum;
    });
    try { localStorage.setItem(key, JSON.stringify(list)); } catch (e) {}

    element.classList.remove('wishlisted');
    if (icon) {
        icon.className = 'bi bi-heart';
        icon.style.color = '#12213a';
    }
    showWishlistToast('Removed from wishlist 💔', 'error');
    document.dispatchEvent(new CustomEvent('wishlistUpdated'));
}

function getWishlistIds() {
    try {
        var key = typeof getTatitoStorageKey === 'function' ? getTatitoStorageKey('wishlist') : 'tatito_guest_wishlist';
        var raw = localStorage.getItem(key);
        return JSON.parse(raw) || [];
    } catch (e) { return []; }
}

function saveWishlistIds(ids) {
    try {
        var key = typeof getTatitoStorageKey === 'function' ? getTatitoStorageKey('wishlist') : 'tatito_guest_wishlist';
        localStorage.setItem(key, JSON.stringify(ids));
    } catch (e) {}
}

function showWishlistToast(message, type) {
    var existingToast = document.querySelector('.wishlist-toast');
    if (existingToast) existingToast.remove();

    var toast = document.createElement('div');
    toast.className = 'wishlist-toast';
    var iconClass = type === 'success' ? 'bi bi-heart-fill' : 'bi bi-heart-broken';
    toast.innerHTML = '<i class="' + iconClass + '" style="color:' + (type === 'success' ? '#22c55e' : '#ff4757') + ';"></i> ' + message;
    document.body.appendChild(toast);

    setTimeout(function() { toast.classList.add('show'); }, 10);
    setTimeout(function() {
        toast.classList.remove('show');
        setTimeout(function() { toast.remove(); }, 300);
    }, 3000);
}

// ===== ROOM "SELECTED" BUTTON — ADDS/REMOVES FROM WISHLIST (Hotels) =====
window.toggleRoomWishlist = function(btn, projectId, roomType, area) {
    var key = typeof getTatitoStorageKey === 'function' ? getTatitoStorageKey('wishlist') : 'tatito_guest_wishlist';
    var list = [];
    try { list = JSON.parse(localStorage.getItem(key)) || []; } catch (e) { list = []; }

    var idNum = Number(projectId);
    var isSelected = btn.classList.contains('selected');

    if (isSelected) {
        list = list.filter(function(item) {
            return !(item && Number(item.id) === idNum && item.room_type === roomType && item.area === area);
        });
        try { localStorage.setItem(key, JSON.stringify(list)); } catch (e) {}

        btn.classList.remove('selected');
        btn.textContent = 'Selected';
        btn.style.background = 'transparent';
        btn.style.color = 'var(--primary, #c9a84c)';
        showWishlistToast('Removed from wishlist 💔', 'error');
    } else {
        list.unshift({ id: idNum, type: 'room', room_type: roomType, area: area });
        try { localStorage.setItem(key, JSON.stringify(list)); } catch (e) {}

        btn.classList.add('selected');
        btn.textContent = '✓ Selected';
        btn.style.background = 'var(--primary, #c9a84c)';
        btn.style.color = '#fff';
        showWishlistToast('Added to wishlist ❤️', 'success');
    }

    document.dispatchEvent(new CustomEvent('wishlistUpdated'));
};

    // ===== ADD TO CART FOR VENDOR PROJECTS =====
function addToCart(bhkIndex, variantIndex) {
    var bhkData = popupData[bhkIndex];
    if (!bhkData) return;
    
    var variant = bhkData.variants[variantIndex] || bhkData.variants[0];
    var productName = bhkData.bhk + ' - ' + (variant.carpet_area_sqft || '');
    var productPrice = variant.price || variant.price_range || 'Price on request';
    
    // Store in localStorage cart
    var cartKey = typeof getTatitoStorageKey === 'function' ? getTatitoStorageKey('cart') : 'tatito_cart';
    var cart = [];
    try { cart = JSON.parse(localStorage.getItem(cartKey)) || []; } catch(e) { cart = []; }
    
    var item = {
        id: Date.now(),
        project_id: <?php echo (int) $project['id']; ?>,
        project_name: <?php echo json_encode($project['project_name']); ?>,
        bhk: bhkData.bhk,
        area: variant.carpet_area_sqft || '',
        price: productPrice,
        quantity: 1,
        added_at: new Date().toISOString()
    };
    
    cart.push(item);
    localStorage.setItem(cartKey, JSON.stringify(cart));
    
    // Show success message
    showCartToast('Added to cart: ' + productName);
    
    // Dispatch event for cart count update
    document.dispatchEvent(new CustomEvent('cartUpdated'));
}

function showCartToast(message) {
    var existingToast = document.querySelector('.cart-toast');
    if (existingToast) existingToast.remove();

    var toast = document.createElement('div');
    toast.className = 'cart-toast';
    toast.innerHTML = '<i class="bi bi-cart-check-fill" style="color:#22c55e;"></i> ' + message;
    toast.style.cssText = 'position:fixed; bottom:20px; left:50%; transform:translateX(-50%); background:#12213a; color:#fff; padding:12px 24px; border-radius:8px; z-index:9999; font-weight:600; box-shadow:0 4px 20px rgba(0,0,0,0.3); opacity:0; transition:opacity 0.3s ease;';
    document.body.appendChild(toast);

    setTimeout(function() { toast.style.opacity = '1'; }, 10);
    setTimeout(function() {
        toast.style.opacity = '0';
        setTimeout(function() { toast.remove(); }, 300);
    }, 3000);
}
    
// ===== CONTACT BUILDER MODAL =====
function openContactBuilder() {
    var modal = new bootstrap.Modal(document.getElementById('contactBuilderModal'));
    modal.show();
}

// ===== FLOOR PLAN POPUP - FULL WIDTH =====
var popupData = <?php echo json_encode($floorPlans); ?>;
var currentPopupBhkIndex = 0;
var currentPopupTabIndex = 0;

// Seller data for each BHK type
var sellerData = {
    '2 BHK Apartment': {
        sellers: [
            { initials: 'PB', name: 'Puranik Builders', type: 'Builder', color: '#e8f4fd', textColor: '#1a73e8' },
            { initials: 'MR', name: 'Mine Realty', type: 'Dealer', color: '#fef3e2', textColor: '#e67e22' },
            { initials: 'ES', name: 'Elite Unique Services', type: 'RERA REGISTERED Dealer', color: '#e8f5e9', textColor: '#2e7d32', rera: true }
        ]
    },
    '3 BHK Apartment': {
        sellers: [
            { initials: 'PB', name: 'Puranik Builders', type: 'Builder', color: '#e8f4fd', textColor: '#1a73e8' },
            { initials: 'MR', name: 'Mine Realty', type: 'Dealer', color: '#fef3e2', textColor: '#e67e22' },
            { initials: 'ES', name: 'Elite Unique Services', type: 'RERA REGISTERED Dealer', color: '#e8f5e9', textColor: '#2e7d32', rera: true }
        ]
    },
    '1 BHK Apartment': {
        sellers: [
            { initials: 'PB', name: 'Puranik Builders', type: 'Builder', color: '#e8f4fd', textColor: '#1a73e8' },
            { initials: 'MR', name: 'Mine Realty', type: 'Dealer', color: '#fef3e2', textColor: '#e67e22' }
        ]
    }
};

function openFloorplanPopup(bhkIndex, variantIndex) {
    currentPopupBhkIndex = bhkIndex || 0;
    currentPopupTabIndex = variantIndex || 0;
    
    renderPopupContentFull();
    document.getElementById('floorplanOverlay').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeFloorplanPopup() {
    document.getElementById('floorplanOverlay').classList.remove('active');
    document.body.style.overflow = '';
}

function renderPopupContentFull() {
    var bhkData = popupData[currentPopupBhkIndex];
    if (!bhkData) return;
    
    var variants = bhkData.variants || [];
    var selectedVariant = variants[currentPopupTabIndex] || variants[0];
    var bhkName = bhkData.bhk || 'Apartment';
    
    // Update BHK Dropdown
    var dropdown = document.getElementById('popupBhkDropdown');
    if (dropdown) {
        var dropdownHtml = '';
        popupData.forEach(function(item, index) {
            var selected = (index === currentPopupBhkIndex) ? 'selected' : '';
            dropdownHtml += '<option value="' + index + '" ' + selected + '>' + item.bhk + '</option>';
        });
        dropdown.innerHTML = dropdownHtml;
    }
    
    // Update Area Info Tabs (only area info, no BHK text)
    var tabsContainer = document.getElementById('popupBhkTabs');
    if (tabsContainer) {
        var tabsHtml = '';
        popupData.forEach(function(item, index) {
            var isActive = (index === currentPopupBhkIndex) ? 'active' : '';
            var firstVariant = item.variants && item.variants.length > 0 ? item.variants[0] : null;
            var areaDisplay = firstVariant && firstVariant.carpet_area_sqft ? firstVariant.carpet_area_sqft : '';
            var sqmDisplay = firstVariant && firstVariant.carpet_area_sqm ? ' (' + firstVariant.carpet_area_sqm + ' sqm)' : '';
            tabsHtml += '<button class="popup-bhk-tab ' + isActive + '" onclick="changePopupBhk(' + index + ')">' + 
                '<span class="tab-area">' + areaDisplay + '</span>' +
                '<span class="tab-sqm">' + sqmDisplay + '</span>' +
            '</button>';
        });
        tabsContainer.innerHTML = tabsHtml;
    }
    
    // Update BHK title in dropdown (already handled above)
    // No need to update popupBhkTitle anymore since we use dropdown
    
    // Update price
    var price = selectedVariant.price || selectedVariant.price_range || 'Price on request';
    var hasCharges = false;
    if (selectedVariant.price_range) {
        price = selectedVariant.price_range;
        hasCharges = true;
    }
    var priceDisplay = document.getElementById('popupPrice');
    if (priceDisplay) {
        priceDisplay.innerHTML = '₹' + price + (hasCharges ? ' <span class="charges">+ Charges</span>' : '');
    }
    
    // Update possession
    var possession = selectedVariant.possession_date || 'Dec \'26';
    var possessionDisplay = document.getElementById('popupPossession');
    if (possessionDisplay) {
        possessionDisplay.textContent = possession;
    }
    
    // Update seller info
    var area = selectedVariant.carpet_area_sqft || 'N/A';
    var sellerBhk = document.getElementById('sellerBhk');
    if (sellerBhk) sellerBhk.textContent = bhkName;
    var sellerArea = document.getElementById('sellerArea');
    if (sellerArea) sellerArea.textContent = area + ' sqft';
    
    // Update sellers list
    renderSellers(bhkName, area);
    
    // Update image
    var img = document.getElementById('popupFloorImage');
    var fpImage = selectedVariant.floor_plan_image || '/front/assets/images/3d.jpg';
    if (img) {
        img.src = fpImage;
    }
}

function changePopupBhk(index) {
    currentPopupBhkIndex = parseInt(index);
    currentPopupTabIndex = 0;
    renderPopupContentFull();
}

function renderSellers(bhkName, area) {
    var sellersContainer = document.querySelector('.fp-sellers-section');
    if (!sellersContainer) return;
    
    // Get sellers for this BHK type
    var sellers = sellerData[bhkName] ? sellerData[bhkName].sellers : sellerData['2 BHK Apartment'].sellers;
    
    var html = '<div class="fp-seller-title">Sellers for <span id="sellerBhk">' + bhkName + '</span> - <span id="sellerArea">' + area + ' sqft</span></div>';
    
    sellers.forEach(function(seller) {
        var reraBadge = seller.rera ? ' <span class="rera-badge">RERA</span>' : '';
        html += '<div class="fp-seller-card">' +
            '<div class="fp-seller-info">' +
                '<div class="fp-seller-icon" style="background:' + seller.color + '; color:' + seller.textColor + ';">' + seller.initials + '</div>' +
                '<div>' +
                    '<div class="fp-seller-name">' + seller.name + '</div>' +
                    '<div class="fp-seller-type">' + seller.type + reraBadge + '</div>' +
                '</div>' +
            '</div>' +
            '<button class="fp-view-contact-btn" onclick="viewContact(\'' + seller.name + '\')">View Contact</button>' +
        '</div>';
    });
    
    sellersContainer.innerHTML = html;
}

function changePopupBhk(index) {
    currentPopupBhkIndex = parseInt(index);
    currentPopupTabIndex = 0;
    renderPopupContentFull();
}

function switchView(view) {
    document.querySelectorAll('.view-tab').forEach(function(tab) {
        tab.classList.remove('active');
        if (tab.textContent.toLowerCase() === view) {
            tab.classList.add('active');
        }
    });
    
    var img = document.getElementById('popupFloorImage');
    if (img) {
        if (view === '3d') {
            img.src = img.src.replace('2d', '3d');
        } else {
            img.src = img.src.replace('3d', '2d');
        }
    }
}

function openFloorImageFull() {
    var overlay = document.getElementById('floorImageOverlay');
    var img = document.getElementById('fullFloorImage');
    var src = document.getElementById('popupFloorImage').src;
    if (img) {
        img.src = src;
    }
    if (overlay) {
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeFloorImageFull() {
    var overlay = document.getElementById('floorImageOverlay');
    if (overlay) {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function viewSellerNumber() {
    var modal = new bootstrap.Modal(document.getElementById('contactBuilderModal'));
    modal.show();
}

function viewContact(sellerName) {
    var modal = new bootstrap.Modal(document.getElementById('contactBuilderModal'));
    modal.show();
}

function handleHelpful(response) {
    var section = document.querySelector('.floorplan-helpful-section');
    if (section) {
        if (response === 'skip') {
            section.innerHTML = '<span style="font-size:0.8rem; color:var(--muted);">Thank you for your feedback!</span>';
        } else {
            section.innerHTML = '<span style="font-size:0.8rem; color:#22c55e;">Thank you! Your feedback is valuable to us.</span>';
        }
        setTimeout(function() {
            closeFloorplanPopup();
            setTimeout(function() {
                section.innerHTML = '<span class="helpful-text">Is this 3D plan helpful?</span><div class="helpful-btns"><button class="helpful-yes" onclick="handleHelpful(\'yes\')">Yes</button><button class="helpful-no" onclick="handleHelpful(\'no\')">No</button><button class="skip-btn" onclick="handleHelpful(\'skip\')">Skip</button></div>';
            }, 500);
        }, 1500);
    }
}

// Close popup on overlay click
document.addEventListener('DOMContentLoaded', function() {
    var overlay = document.getElementById('floorplanOverlay');
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closeFloorplanPopup();
            }
        });
    }
});

// Close popup with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeFloorplanPopup();
        closeFloorImageFull();
    }
});

// ===== REPORT POPUP FUNCTIONS =====
function openReportPopup() {
    var overlay = document.getElementById('reportOverlay');
    if (overlay) {
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        // Reset form when opening
        var form = document.getElementById('reportForm');
        if (form) form.reset();
        var msg = document.getElementById('reportMessage');
        if (msg) msg.innerHTML = '';
    }
}

function closeReportPopup() {
    var overlay = document.getElementById('reportOverlay');
    if (overlay) {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Click on overlay to close
document.addEventListener('DOMContentLoaded', function() {
    var overlay = document.getElementById('reportOverlay');
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closeReportPopup();
            }
        });
    }
});

// Escape key to close
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeReportPopup();
    }
});

// ===== REPORT FORM SUBMISSION =====
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('reportForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(form);
            var messageDiv = document.getElementById('reportMessage');
            var submitBtn = form.querySelector('.report-submit-btn');
            var reason = document.getElementById('reportReason').value;
            
            if (!reason) {
                messageDiv.innerHTML = '<div class="alert alert-danger" style="padding:0.5rem 0.8rem; border-radius:0.4rem; font-size:0.85rem; background:#fdeef0; color:#b6435a; margin-bottom:0.8rem;">Please select a reason for reporting.</div>';
                document.getElementById('reportReason').focus();
                return;
            }
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Submitting...';
            
            // Use the form's action URL
            var actionUrl = form.getAttribute('action') || '<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); ?>/submit-report.php';
            
            fetch(actionUrl, {
                method: 'POST',
                body: formData
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.status === 'success') {
                    messageDiv.innerHTML = '<div class="alert alert-success" style="padding:0.5rem 0.8rem; border-radius:0.4rem; font-size:0.85rem; background:#e6f6ec; color:#1a8f4c; margin-bottom:0.8rem;">' + data.message + '</div>';
                    form.reset();
                    setTimeout(function() {
                        closeReportPopup();
                    }, 3000);
                } else {
                    messageDiv.innerHTML = '<div class="alert alert-danger" style="padding:0.5rem 0.8rem; border-radius:0.4rem; font-size:0.85rem; background:#fdeef0; color:#b6435a; margin-bottom:0.8rem;">' + data.message + '</div>';
                }
            })
            .catch(function(error) {
                console.error('Report error:', error);
                messageDiv.innerHTML = '<div class="alert alert-danger" style="padding:0.5rem 0.8rem; border-radius:0.4rem; font-size:0.85rem; background:#fdeef0; color:#b6435a; margin-bottom:0.8rem;">Something went wrong. Please try again.</div>';
            })
            .finally(function() {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-flag"></i> Submit Report';
            });
        });
    }
});
    
    // ===== BROCHURE UPLOAD =====
document.addEventListener('DOMContentLoaded', function() {
    var brochureForm = document.getElementById('brochureUploadForm');
    if (brochureForm) {
        brochureForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);
            var submitBtn = this.querySelector('button[type="submit"]');
            var originalHtml = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            
            fetch('<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); ?>/upload-brochure.php', {
                method: 'POST',
                body: formData
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.status === 'success') {
                    // Reload page to show new brochure
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(function(error) {
                alert('Network error. Please try again.');
            })
            .finally(function() {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            });
        });
    }
});

// ===== DELETE BROCHURE =====
function deleteBrochure(projectId) {
    if (!confirm('Are you sure you want to remove this brochure?')) return;
    
    fetch('<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); ?>/delete-brochure.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ project_id: projectId })
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.status === 'success') {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(function(error) {
        alert('Network error. Please try again.');
    });
}

// ===== ALL JAVASCRIPT WRAPPED IN $(document).ready() =====
$(document).ready(function() {

    // ===== INITIALIZE GALLERY =====
    var galleryItems = [
        <?php
        $categories = ['outdoors', 'facilities', 'construction', 'masterplan', 'videos', 'brochure', 'request'];
        $titles = ['Elevation', 'Front View', 'Swimming Pool', 'Club House', 'Gymnasium', 'Kids Play Area', 
                   'Landscaped Garden', 'Community Hall', 'Construction Progress', 'Structural Work', 
                   'Finishing Work', 'Master Plan', 'Video Tour', '3D Walkthrough', 'Brochure Download', 
                   'Request More Photos'];
        
        // Main image
        echo "{ id: 0, category: 'outdoors', title: 'Elevation', image: '" . addslashes($imagePath) . "' },\n";
        
        // Gallery images
        foreach ($galleryImages as $i => $galImage) {
            $galPath = "../../../admin/uploads/trending-projects/" . $galImage;
            $cat = $categories[$i % count($categories)];
            $title = $titles[($i + 1) % count($titles)];
            echo "{ id: " . ($i + 1) . ", category: '" . $cat . "', title: '" . $title . "', image: '" . addslashes($galPath) . "' },\n";
        }
        ?>
    ];
    
    // Initialize gallery with items
    if (typeof initializeGallery === 'function') {
        initializeGallery(galleryItems);
    }

    // ===== FAQ TOGGLE =====
    window.toggleFaq = function(el) {
        var item = el.closest('.faq-item');
        var isActive = item.classList.contains('active');
        
        document.querySelectorAll('.faq-item').forEach(function(faq) {
            faq.classList.remove('active');
        });
        
        if (!isActive) {
            item.classList.add('active');
        }
    };

    // ===== STICKY TAB NAV =====
    (function() {
        var tabLinks = document.querySelectorAll('.listing-tabs .tab-link');
        var sections = Array.from(tabLinks)
            .map(function(link) { return document.querySelector(link.getAttribute('href')); })
            .filter(Boolean);

        function setActiveTab(id) {
            tabLinks.forEach(function(l) {
                l.classList.toggle('active', l.getAttribute('href') === '#' + id);
            });
        }

        tabLinks.forEach(function(link) {
            link.addEventListener('click', function() { setActiveTab(this.getAttribute('href').slice(1)); });
        });

        if ('IntersectionObserver' in window && sections.length) {
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) setActiveTab(entry.target.id);
                });
            }, { rootMargin: '-90px 0px -65% 0px', threshold: 0 });
            sections.forEach(function(sec) { observer.observe(sec); });
        }
    })();

    // ===== PIN THE TAB BAR =====
    (function() {
        var tabsWrap = document.getElementById('listingTabsWrap');
        if (!tabsWrap) return;

        var placeholder = document.createElement('div');
        placeholder.style.display = 'none';
        tabsWrap.parentNode.insertBefore(placeholder, tabsWrap.nextSibling);

        var originalTop = 0;

        function fixedHeaderHeight() {
            var height = 0;
            document.querySelectorAll('header, nav, .navbar, .site-header, .header').forEach(function(el) {
                if (el === tabsWrap || tabsWrap.contains(el)) return;
                var pos = window.getComputedStyle(el).position;
                var rect = el.getBoundingClientRect();
                if ((pos === 'fixed' || pos === 'sticky') && rect.top <= 5 && rect.height > 0) {
                    height = Math.max(height, rect.bottom);
                }
            });
            return height;
        }

        function unpin() {
            tabsWrap.classList.remove('is-fixed');
            tabsWrap.style.width = '';
            tabsWrap.style.left = '';
            placeholder.style.display = 'none';
        }

        function measure() {
            var wasFixed = tabsWrap.classList.contains('is-fixed');
            if (wasFixed) unpin();
            originalTop = tabsWrap.getBoundingClientRect().top + window.scrollY;
            document.documentElement.style.setProperty('--tabs-offset', fixedHeaderHeight() + 'px');
        }

        function onScroll() {
            var offset = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--tabs-offset')) || 0;
            var shouldPin = window.scrollY + offset >= originalTop;

            if (shouldPin && !tabsWrap.classList.contains('is-fixed')) {
                var rect = tabsWrap.getBoundingClientRect();
                placeholder.style.height = rect.height + 'px';
                placeholder.style.display = 'block';
                tabsWrap.style.width = rect.width + 'px';
                tabsWrap.style.left = rect.left + 'px';
                tabsWrap.classList.add('is-fixed');
            } else if (!shouldPin && tabsWrap.classList.contains('is-fixed')) {
                unpin();
            }
        }

        measure();
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', function() { measure(); onScroll(); });
        window.addEventListener('load', function() { measure(); onScroll(); });
    })();

    // ===== AMENITIES TOGGLE =====
    (function() {
        var moreBtn = document.getElementById('amenityMoreBtn');
        var extra = document.getElementById('amenityExtra');
        if (!moreBtn || !extra) return;
        var extraCount = extra.querySelectorAll('.amenity-icon-card').length;
        var label = moreBtn.querySelector('.more-label');

        moreBtn.addEventListener('click', function() {
            var showing = extra.classList.toggle('show');
            moreBtn.classList.toggle('expanded', showing);
            label.textContent = showing ? 'Less' : ('+' + extraCount + ' More');
        });
    })();

    // ===== MAP =====
    <?php if ($project): ?>
    map = L.map('map').setView([<?php echo $lat; ?>, <?php echo $lng; ?>], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    L.marker([<?php echo $lat; ?>, <?php echo $lng; ?>]).addTo(map)
        .bindPopup(<?php echo json_encode($project['project_name']); ?>).openPopup();
    <?php endif; ?>

    // ===== FORM SUBMISSIONS =====
    $("#siteVisitForm").submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find(".sv-submit-btn, .btn-submit");
        
        var name = form.find('input[name="name"]').val().trim();
        var contact = form.find('input[name="contact"]').val().trim();
        var email = form.find('input[name="email"]').val().trim();
        var termsChecked = form.find('input[name="terms_agree"]').is(':checked');

        if (!name) {
            $("#siteVisitFormMessage").html('<div class="alert alert-danger mt-2">Please enter your full name.</div>');
            form.find('input[name="name"]').focus();
            return false;
        }

        if (!contact || contact.length < 10) {
            $("#siteVisitFormMessage").html('<div class="alert alert-danger mt-2">Please enter a valid 10-digit contact number.</div>');
            form.find('input[name="contact"]').focus();
            return false;
        }

        if (!email) {
            $("#siteVisitFormMessage").html('<div class="alert alert-danger mt-2">Please enter your email address.</div>');
            form.find('input[name="email"]').focus();
            return false;
        }

        if (!termsChecked) {
            $("#siteVisitFormMessage").html('<div class="alert alert-danger mt-2">Please agree to the Terms & Conditions.</div>');
            return false;
        }

        $.ajax({
            url: "<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); ?>/submit-property-enquiry.php",
            type: "POST",
            data: form.serialize(),
            dataType: "json",
            beforeSend: function() {
                submitBtn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm me-2"></span> Submitting...');
                $("#siteVisitFormMessage").html('');
            },
            success: function(data) {
                if (data.status == "success") {
                    $("#siteVisitFormMessage").html('<div class="alert alert-success mt-2">' + data.message + '</div>');
                    form[0].reset();

                    // Track in localStorage for Contacted dashboard
                    try {
                        var KEY = typeof getTatitoStorageKey === 'function' ? getTatitoStorageKey('contacted') : 'tatito_guest_contacted';
                        var cList = JSON.parse(localStorage.getItem(KEY) || '[]');
                        var propName = <?php echo json_encode($project['project_name'] ?? 'Property'); ?>;
                        var propId = <?php echo (int)($project['id'] ?? 0); ?>;
                        var propSlug = <?php echo json_encode($project['slug'] ?? ''); ?>;
                        var propImg = <?php echo json_encode($project['image'] ?? ''); ?>;
                        var propPrice = <?php echo json_encode($project['price'] ?? ''); ?>;
                        var propAddr = <?php echo json_encode($project['address'] ?? ''); ?>;
                        var propCity = <?php echo json_encode($project['city'] ?? ''); ?>;
                        
                        // Avoid duplicates at the top
                        cList = cList.filter(function(x) { return !(x && (x.id === propId || x.page_id === propId) && x.action_label === 'Site Visit Requested'); });
                        cList.unshift({
                            id: propId,
                            page_id: propId,
                            property_id: propId,
                            project_name: propName,
                            title: propName,
                            slug: propSlug,
                            image: propImg,
                            price: propPrice,
                            address: propAddr,
                            city: propCity,
                            type: 'property',
                            kind: 'property_contact',
                            action_label: 'Site Visit Requested',
                            status: 'Scheduled',
                            appointment_date: form.find('[name="appointment_date"]').val() || '',
                            created_at: new Date().toISOString()
                        });
                        localStorage.setItem(KEY, JSON.stringify(cList.slice(0, 30)));
                    } catch(e) {}
                } else {
                    $("#siteVisitFormMessage").html('<div class="alert alert-danger mt-2">' + (data.message || 'Something went wrong.') + '</div>');
                }
            },
            error: function(xhr) {
                var msg = 'Something went wrong. Please try again.';
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res && res.message) msg = res.message;
                } catch(e) {}
                $("#siteVisitFormMessage").html('<div class="alert alert-danger mt-2">' + msg + '</div>');
            },
            complete: function() {
                submitBtn.prop("disabled", false).html('<i class="bi bi-send me-2"></i> Submit Request');
            }
        });
    });

    // Add this to your contact builder form success handler
$("#contactBuilderForm").submit(function(e) {
    e.preventDefault();
    var form = $(this);
    
    // Validate required fields
    var name = form.find('input[name="name"]').val().trim();
    var contact = form.find('input[name="contact"]').val().trim();
    var termsChecked = form.find('input[name="terms_agree"]').is(':checked');
    
    if (!name) {
        $("#contactBuilderMessage").html('<div class="alert alert-danger">Please enter your name.</div>');
        form.find('input[name="name"]').focus();
        return false;
    }
    
    if (!contact || contact.length < 10) {
        $("#contactBuilderMessage").html('<div class="alert alert-danger">Please enter a valid 10-digit phone number.</div>');
        form.find('input[name="contact"]').focus();
        return false;
    }
    
    if (!termsChecked) {
        $("#contactBuilderMessage").html('<div class="alert alert-danger">Please agree to the Terms & Conditions and Privacy Policy.</div>');
        return false;
    }
    
    $.ajax({
        url: "<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); ?>/submit-builder-contact.php",
        type: "POST",
        data: form.serialize(),
        dataType: "json",
        beforeSend: function() {
            form.find(".btn-submit").prop("disabled", true).html('<span class="spinner-border spinner-border-sm me-2"></span> Sending...');
            $("#contactBuilderMessage").html('');
        },
        success: function(data) {
            if (data.status == "success") {
                var contactInfo = data.seller_contact ? '<br><strong class="d-block mt-2 fs-6">📞 Seller Contact: ' + data.seller_contact + '</strong>' : '';
                $("#contactBuilderMessage").html('<div class="alert alert-success">' + data.message + contactInfo + '</div>');
                form[0].reset();
                
                // Track in localStorage for Contacted dashboard
                try {
                    var KEY = typeof getTatitoStorageKey === 'function' ? getTatitoStorageKey('contacted') : 'tatito_guest_contacted';
                    var cList = JSON.parse(localStorage.getItem(KEY) || '[]');
                    var propName = <?php echo json_encode($project['project_name'] ?? 'Property'); ?>;
                    var propId = <?php echo (int)($project['id'] ?? 0); ?>;
                    var propSlug = <?php echo json_encode($project['slug'] ?? ''); ?>;
                    var propImg = <?php echo json_encode($project['image'] ?? ''); ?>;
                    var propPrice = <?php echo json_encode($project['price'] ?? ''); ?>;
                    var propAddr = <?php echo json_encode($project['address'] ?? ''); ?>;
                    var propCity = <?php echo json_encode($project['city'] ?? ''); ?>;

                    // Avoid duplicates at top
                    cList = cList.filter(function(x) { return !(x && (x.id === propId || x.page_id === propId) && x.action_label === 'Contacted Builder'); });
                    cList.unshift({
                        id: propId,
                        page_id: propId,
                        property_id: propId,
                        project_name: propName,
                        title: propName,
                        slug: propSlug,
                        image: propImg,
                        price: propPrice,
                        address: propAddr,
                        city: propCity,
                        type: 'property',
                        kind: 'property_contact',
                        action_label: 'Contacted Builder',
                        status: 'Contacted',
                        seller_contact: data.seller_contact || '',
                        created_at: new Date().toISOString()
                    });
                    localStorage.setItem(KEY, JSON.stringify(cList.slice(0, 30)));
                } catch(e) {}

                setTimeout(function() {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('contactBuilderModal'));
                    if (modal) modal.hide();
                    $("#contactBuilderMessage").html('');
                }, 4000);
            } else {
                $("#contactBuilderMessage").html('<div class="alert alert-danger">' + (data.message || 'Please fill in all required fields.') + '</div>');
            }
        },
        error: function(xhr) {
            $("#contactBuilderMessage").html('<div class="alert alert-danger">Something went wrong. Please try again.</div>');
        },
        complete: function() {
            form.find(".btn-submit").prop("disabled", false).html('<i class="bi bi-eye"></i> View Seller contact');
        }
    });
});

    // ===== PRICE VARIANT SLIDER =====
    (function() {
        document.querySelectorAll('.price-variant-slider').forEach(function(slider) {
            var slides = slider.querySelectorAll('.price-variant-slide');
            var totalSlides = slides.length;

            if (totalSlides <= 1) return;

            var controls = slider.closest('.price-variant-slider-wrapper').querySelector('.price-variant-slider-controls');
            var prevBtn = controls ? controls.querySelector('.price-slider-prev') : null;
            var nextBtn = controls ? controls.querySelector('.price-slider-next') : null;
            var dots = controls ? controls.querySelectorAll('.price-variant-dots .dot') : [];

            var currentIndex = 0;
            var isTransitioning = false;

            function updateSlider() {
                var slideWidth = slides[0].offsetWidth + 16;
                var offset = currentIndex * slideWidth;
                slider.style.transform = 'translateX(-' + offset + 'px)';

                dots.forEach(function(dot, index) {
                    dot.classList.toggle('active', index === currentIndex);
                });

                if (prevBtn) prevBtn.disabled = currentIndex === 0;
                if (nextBtn) nextBtn.disabled = currentIndex >= totalSlides - 1;
            }

            function goTo(index) {
                if (isTransitioning) return;
                if (index < 0) index = 0;
                if (index > totalSlides - 1) index = totalSlides - 1;
                if (index === currentIndex) return;

                isTransitioning = true;
                currentIndex = index;
                updateSlider();

                setTimeout(function() {
                    isTransitioning = false;
                }, 400);
            }

            if (prevBtn) prevBtn.addEventListener('click', function() { goTo(currentIndex - 1); });
            if (nextBtn) nextBtn.addEventListener('click', function() { goTo(currentIndex + 1); });

            dots.forEach(function(dot, index) {
                dot.addEventListener('click', function() { goTo(index); });
            });

            var resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    updateSlider();
                }, 250);
            });

            setTimeout(updateSlider, 100);
        });
    })();

    // ===== FLOOR PLAN SLIDER =====
    (function() {
        document.querySelectorAll('.floor-plan-slider').forEach(function(slider) {
            var slides = slider.querySelectorAll('.floor-plan-slide');
            var totalSlides = slides.length;

            if (totalSlides <= 1) return;

            var controls = slider.closest('.floor-plan-slider-wrapper').querySelector('.floor-plan-slider-controls');
            var prevBtn = controls ? controls.querySelector('.slider-prev') : null;
            var nextBtn = controls ? controls.querySelector('.slider-next') : null;
            var dots = controls ? controls.querySelectorAll('.slider-dots .dot') : [];

            var currentIndex = 0;
            var slidesToShow = getSlidesToShow();
            var isTransitioning = false;

            function getSlidesToShow() {
                if (window.innerWidth < 576) return 1;
                if (window.innerWidth < 992) return 2;
                return 3;
            }

            function updateSlider() {
                var slideWidth = slides[0].offsetWidth + 16;
                var offset = currentIndex * slideWidth;
                slider.style.transform = 'translateX(-' + offset + 'px)';

                dots.forEach(function(dot, index) {
                    dot.classList.toggle('active', index === currentIndex);
                });

                if (prevBtn) prevBtn.disabled = currentIndex === 0;
                if (nextBtn) nextBtn.disabled = currentIndex >= totalSlides - slidesToShow;
            }

            function goTo(index) {
                if (isTransitioning) return;
                if (index < 0) index = 0;
                if (index > totalSlides - slidesToShow) index = totalSlides - slidesToShow;
                if (index === currentIndex) return;

                isTransitioning = true;
                currentIndex = index;
                updateSlider();

                setTimeout(function() {
                    isTransitioning = false;
                }, 400);
            }

            if (prevBtn) prevBtn.addEventListener('click', function() { goTo(currentIndex - 1); });
            if (nextBtn) nextBtn.addEventListener('click', function() { goTo(currentIndex + 1); });

            dots.forEach(function(dot, index) {
                dot.addEventListener('click', function() { goTo(index); });
            });

            var resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    var newSlidesToShow = getSlidesToShow();
                    if (newSlidesToShow !== slidesToShow) {
                        slidesToShow = newSlidesToShow;
                        if (currentIndex > totalSlides - slidesToShow) {
                            currentIndex = Math.max(0, totalSlides - slidesToShow);
                        }
                        updateSlider();
                    }
                }, 250);
            });

            setTimeout(updateSlider, 100);
        });
    })();

    // ===== AREA UNIT CONVERTER (dropdown) =====
(function() {
    document.querySelectorAll('.area-unit-select').forEach(function(select) {
        select.addEventListener('change', function() {
            var areaValue = this.dataset.area;
            var sqmValue  = this.dataset.sqm;
            var unit      = this.value;

            var card = this.closest('.price-variant-card');
            var areaDisplay = card ? card.querySelector('.area-value') : null;
            if (!areaDisplay) return;

            var areaNum = parseFloat(areaValue);
            var isRange = areaValue.indexOf(' - ') !== -1;
            var parts = isRange ? areaValue.split(' - ').map(function(p) { return parseFloat(p.trim()); }) : [];

            var SQFT_TO_SQYD = 0.111111;
            var SQFT_TO_SQM = 0.092903;
            var SQFT_TO_GROUNDS = 0.000416667;

            var convertedValue = '', unitLabel = '';

            switch (unit) {
                case 'sqft':
                    convertedValue = areaValue;
                    unitLabel = 'sqft';
                    break;
                case 'sqyd':
                    convertedValue = isRange
                        ? parts.map(function(p) { return (p * SQFT_TO_SQYD).toFixed(2); }).join(' - ')
                        : (areaNum * SQFT_TO_SQYD).toFixed(2);
                    unitLabel = 'sqyd';
                    break;
                case 'sqm':
                    if (sqmValue) {
                        convertedValue = sqmValue;
                    } else {
                        convertedValue = isRange
                            ? parts.map(function(p) { return (p * SQFT_TO_SQM).toFixed(2); }).join(' - ')
                            : (areaNum * SQFT_TO_SQM).toFixed(2);
                    }
                    unitLabel = 'sqm';
                    break;
                case 'grounds':
                    convertedValue = isRange
                        ? parts.map(function(p) { return (p * SQFT_TO_GROUNDS).toFixed(4); }).join(' - ')
                        : (areaNum * SQFT_TO_GROUNDS).toFixed(4);
                    unitLabel = 'grounds';
                    break;
            }

            areaDisplay.textContent = convertedValue + ' ' + unitLabel;
        });
    });
})();

    // ===== NEARBY PLACES =====
    (function() {
        <?php if ($project): ?>
        var PROJECT_LAT = <?php echo $lat; ?>;
        var PROJECT_LNG = <?php echo $lng; ?>;
        var PROJECT_NAME = <?php echo json_encode($project['project_name']); ?>;
        <?php else: ?>
        var PROJECT_LAT = 19.2183;
        var PROJECT_LNG = 72.9781;
        var PROJECT_NAME = 'Property';
        <?php endif; ?>
        var SEARCH_RADIUS = 1500;
        var MAX_PER_CATEGORY = 10;

        var CATEGORIES = [
            { key: 'school', label: 'Schools', icon: 'bi-mortarboard-fill', tags: [['amenity', 'school']] },
            { key: 'hospital', label: 'Hospitals', icon: 'bi-hospital-fill', tags: [['amenity', 'hospital'], ['amenity', 'clinic']] },
            { key: 'bus_stop', label: 'Bus Stops', icon: 'bi-bus-front-fill', tags: [['highway', 'bus_stop']] },
            { key: 'restaurant', label: 'Restaurants & Cafes', icon: 'bi-cup-hot-fill', tags: [['amenity', 'restaurant'], ['amenity', 'cafe']] },
            { key: 'bank', label: 'Banks & ATMs', icon: 'bi-bank', tags: [['amenity', 'bank'], ['amenity', 'atm']] },
            { key: 'shopping', label: 'Shopping', icon: 'bi-bag-fill', tags: [['shop', 'mall'], ['shop', 'supermarket']] },
            { key: 'pharmacy', label: 'Pharmacy', icon: 'bi-capsule', tags: [['amenity', 'pharmacy']] },
            { key: 'park', label: 'Parks', icon: 'bi-tree-fill', tags: [['leisure', 'park']] },
        ];

        var nearbyMap = null;
        var poiMarkers = [];
        var dataLoaded = false;

        function haversineKm(lat1, lon1, lat2, lon2) {
            var R = 6371;
            var dLat = (lat2 - lat1) * Math.PI / 180;
            var dLon = (lon2 - lon1) * Math.PI / 180;
            var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.sin(dLon / 2) * Math.sin(dLon / 2) *
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180);
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        }

        function buildOverpassQuery() {
            var clauses = '';
            CATEGORIES.forEach(function(cat) {
                cat.tags.forEach(function(pair) {
                    clauses += '  node["' + pair[0] + '"="' + pair[1] + '"](around:' + SEARCH_RADIUS + ',' + PROJECT_LAT + ',' + PROJECT_LNG + ');\n';
                });
            });
            return '[out:json][timeout:25];\n(\n' + clauses + ');\nout body ' + (MAX_PER_CATEGORY * CATEGORIES.length * 3) + ';';
        }

        function classify(tags) {
            for (var i = 0; i < CATEGORIES.length; i++) {
                var cat = CATEGORIES[i];
                for (var j = 0; j < cat.tags.length; j++) {
                    var pair = cat.tags[j];
                    if (tags && tags[pair[0]] === pair[1]) return cat;
                }
            }
            return null;
        }

        function poiIcon() {
            return L.divIcon({
                className: '',
                html: '<div class="nearby-poi-icon"><i class="bi bi-geo-alt-fill"></i></div>',
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            });
        }

        function mainIcon() {
            return L.divIcon({
                className: '',
                html: '<div class="nearby-main-icon"><i class="bi bi-house-door-fill"></i></div>',
                iconSize: [38, 38],
                iconAnchor: [19, 19]
            });
        }

        function renderSidebar(grouped) {
            var wrap = document.getElementById('nearbyCategories');
            if (!wrap) return;
            wrap.innerHTML = '';
            var anyResults = false;

            CATEGORIES.forEach(function(cat) {
                var items = grouped[cat.key] || [];
                if (items.length === 0) return;
                anyResults = true;

                var catEl = document.createElement('div');
                catEl.className = 'nearby-category';
                catEl.innerHTML = `
                    <div class="nearby-category-header">
                        <span class="cat-icon"><i class="bi ${cat.icon}"></i></span>
                        <span class="cat-label">${cat.label}</span>
                        <span class="cat-count">${items.length}</span>
                        <i class="bi bi-chevron-down chevron"></i>
                    </div>
                    <div class="nearby-category-list"></div>
                `;

                var header = catEl.querySelector('.nearby-category-header');
                header.addEventListener('click', function() {
                    catEl.classList.toggle('expanded');
                });

                var listEl = catEl.querySelector('.nearby-category-list');
                items.forEach(function(item) {
                    var row = document.createElement('div');
                    row.className = 'nearby-item';
                    row.innerHTML = `
                        <span class="item-name">${item.name}</span>
                        <span class="item-dist">${item.distance.toFixed(2)} KM</span>
                    `;
                    row.addEventListener('click', function() {
                        document.querySelectorAll('.nearby-item.active').forEach(function(el) { el.classList.remove('active'); });
                        row.classList.add('active');
                        if (nearbyMap && item.marker) {
                            nearbyMap.setView([item.lat, item.lng], 16);
                            item.marker.openPopup();
                        }
                    });
                    listEl.appendChild(row);
                });

                wrap.appendChild(catEl);
            });

            var first = wrap.querySelector('.nearby-category');
            if (first) first.classList.add('expanded');
            return anyResults;
        }

        function plotMarkers(grouped) {
            poiMarkers.forEach(function(m) { nearbyMap.removeLayer(m); });
            poiMarkers = [];

            Object.keys(grouped).forEach(function(key) {
                grouped[key].forEach(function(item) {
                    var marker = L.marker([item.lat, item.lng], { icon: poiIcon() }).addTo(nearbyMap);
                    marker.bindPopup('<strong>' + item.name + '</strong><br>' + item.distance.toFixed(2) + ' KM away');
                    item.marker = marker;
                    poiMarkers.push(marker);
                });
            });
        }

        function loadNearbyPlaces() {
            if (dataLoaded) return;
            dataLoaded = true;

            var loadingEl = document.getElementById('nearbyLoading');
            var emptyEl = document.getElementById('nearbyEmpty');
            if (!loadingEl || !emptyEl) return;

            fetch('https://overpass-api.de/api/interpreter', {
                method: 'POST',
                body: 'data=' + encodeURIComponent(buildOverpassQuery())
            })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    loadingEl.style.display = 'none';

                    var grouped = {};
                    CATEGORIES.forEach(function(cat) { grouped[cat.key] = []; });

                    (data.elements || []).forEach(function(el) {
                        var cat = classify(el.tags);
                        if (!cat) return;
                        var name = (el.tags && el.tags.name) ? el.tags.name : cat.label.replace(/s$/, '');
                        var distance = haversineKm(PROJECT_LAT, PROJECT_LNG, el.lat, el.lon);
                        grouped[cat.key].push({ name: name, lat: el.lat, lng: el.lon, distance: distance });
                    });

                    Object.keys(grouped).forEach(function(key) {
                        grouped[key].sort(function(a, b) { return a.distance - b.distance; });
                        grouped[key] = grouped[key].slice(0, MAX_PER_CATEGORY);
                    });

                    var anyResults = renderSidebar(grouped);
                    if (!anyResults) {
                        emptyEl.textContent = 'No nearby places found within ' + (SEARCH_RADIUS / 1000) + ' km.';
                        emptyEl.style.display = 'block';
                    } else {
                        plotMarkers(grouped);
                    }
                })
                .catch(function() {
                    loadingEl.style.display = 'none';
                    emptyEl.style.display = 'block';
                    dataLoaded = false;
                });
        }

        var nearbyModalEl = document.getElementById('nearbyMapModal');
        if (nearbyModalEl) {
            nearbyModalEl.addEventListener('shown.bs.modal', function() {
                if (!nearbyMap) {
                    nearbyMap = L.map('nearbyMap').setView([PROJECT_LAT, PROJECT_LNG], 15);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(nearbyMap);
                    L.marker([PROJECT_LAT, PROJECT_LNG], { icon: mainIcon() })
                        .addTo(nearbyMap)
                        .bindPopup(PROJECT_NAME)
                        .openPopup();
                }
                setTimeout(function() { nearbyMap.invalidateSize(); }, 200);
                loadNearbyPlaces();
            });
        }
    })();

    // ===== RECENTLY VIEWED =====
    (function() {
        var RECENT_KEY = typeof getTatitoStorageKey === 'function' ? getTatitoStorageKey('viewed') : 'tatito_guest_viewed';
        var MAX_ITEMS = 10;
        <?php if ($project): ?>
        var currentProjectId = <?php echo (int) $project['id']; ?>;
        <?php else: ?>
        var currentProjectId = 0;
        <?php endif; ?>

        if (!currentProjectId) return;

        var list = [];
        try { list = JSON.parse(localStorage.getItem(RECENT_KEY)) || []; } catch (e) { list = []; }

        list = list.filter(function(r) { return r.id !== currentProjectId; });
        list.unshift({ id: currentProjectId, viewedAt: new Date().toISOString() });
        list = list.slice(0, MAX_ITEMS);

        localStorage.setItem(RECENT_KEY, JSON.stringify(list));
    })();

    // ===== WISHLIST INITIALIZATION =====
    (function() {
        var projectId = <?php echo (int) $project['id']; ?>;
        var list = getWishlistIds();
        var idNum = Number(projectId);
        var isWish = list.some(function(item) {
            var itemId = (item && item.id != null) ? Number(item.id) : Number(item);
            return itemId === idNum;
        });

        if (isWish) {
            var btn = document.querySelector('.wishlist-btn-top');
            if (btn) {
                var icon = btn.querySelector('i');
                btn.classList.add('wishlisted');
                if (icon) {
                    icon.className = 'bi bi-heart-fill';
                    icon.style.color = '#ff4757';
                }
            }
        }
    })();

    // ============ Dark Mode ============
    (function(){
        function handleThemeChange() {
            var isDark = document.body.classList.contains('dark-mode');
        }

        document.addEventListener('themeChanged', function(e) {
            handleThemeChange();
        });

        handleThemeChange();

        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    handleThemeChange();
                }
            });
        });
        observer.observe(document.body, { attributes: true });
    })();
  
    // ===== TESTIMONIAL SLIDER =====
    (function() {
        var slider = document.getElementById('testimonialSlider');
        if (!slider) return;
        var slides = slider.querySelectorAll('.testimonial-slide');
        var totalSlides = slides.length;
        var prevBtn = document.getElementById('testimonialPrev');
        var nextBtn = document.getElementById('testimonialNext');
        var dots = document.querySelectorAll('#testimonialDots .dot');
        
        if (totalSlides === 0) return;
        
        var currentIndex = 0;
        var isTransitioning = false;
        var autoplayInterval = null;
        
        function updateSlider() {
            var slideWidth = slides[0].offsetWidth;
            slider.style.transform = 'translateX(-' + (currentIndex * slideWidth) + 'px)';
            
            dots.forEach(function(dot, index) {
                dot.classList.toggle('active', index === currentIndex);
            });
            
            if (prevBtn) prevBtn.disabled = currentIndex === 0;
            if (nextBtn) nextBtn.disabled = currentIndex >= totalSlides - 1;
        }
        
        function goTo(index) {
            if (isTransitioning) return;
            if (index < 0) index = 0;
            if (index > totalSlides - 1) index = totalSlides - 1;
            if (index === currentIndex) return;
            
            isTransitioning = true;
            currentIndex = index;
            updateSlider();
            
            setTimeout(function() {
                isTransitioning = false;
            }, 500);
            
            resetAutoplay();
        }
        
        function nextSlide() {
            if (currentIndex < totalSlides - 1) {
                goTo(currentIndex + 1);
            } else {
                goTo(0);
            }
        }
        
        function prevSlide() {
            if (currentIndex > 0) {
                goTo(currentIndex - 1);
            }
        }
        
        function startAutoplay() {
            stopAutoplay();
            autoplayInterval = setInterval(nextSlide, 5000);
        }
        
        function stopAutoplay() {
            if (autoplayInterval) {
                clearInterval(autoplayInterval);
                autoplayInterval = null;
            }
        }
        
        function resetAutoplay() {
            stopAutoplay();
            startAutoplay();
        }
        
        if (prevBtn) prevBtn.addEventListener('click', function() { prevSlide(); resetAutoplay(); });
        if (nextBtn) nextBtn.addEventListener('click', function() { nextSlide(); resetAutoplay(); });
        
        dots.forEach(function(dot, index) {
            dot.addEventListener('click', function() { goTo(index); resetAutoplay(); });
        });
        
        var wrapper = slider.closest('.testimonial-slider-wrapper');
        if (wrapper) {
            wrapper.addEventListener('mouseenter', stopAutoplay);
            wrapper.addEventListener('mouseleave', startAutoplay);
        }
        
        var resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                updateSlider();
            }, 250);
        });
        
        setTimeout(updateSlider, 100);
        startAutoplay();
    })();

});
    
    // ===== GALLERY IMAGE UPLOAD =====
function uploadGalleryImages(input, projectId) {
    if (!input.files || input.files.length === 0) return;
    
    var formData = new FormData();
    formData.append('project_id', projectId);
    
    for (var i = 0; i < input.files.length; i++) {
        formData.append('gallery_images[]', input.files[i]);
    }
    
    var submitBtn = input.parentElement;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    submitBtn.style.pointerEvents = 'none';
    
    fetch('<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); ?>/upload-gallery-images.php', {
        method: 'POST',
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.status === 'success') {
            location.reload();
        } else {
            alert('Error: ' + data.message);
            submitBtn.innerHTML = '<i class="bi bi-plus-circle" style="font-size:1.5rem; display:block; margin-bottom:0.2rem;"></i> Add Image';
            submitBtn.style.pointerEvents = 'auto';
        }
    })
    .catch(function(error) {
        alert('Network error. Please try again.');
        submitBtn.innerHTML = '<i class="bi bi-plus-circle" style="font-size:1.5rem; display:block; margin-bottom:0.2rem;"></i> Add Image';
        submitBtn.style.pointerEvents = 'auto';
    });
}

// ===== MAIN IMAGE UPLOAD =====
function uploadMainImage(projectId) {
    var input = document.getElementById('mainImageUpload');
    if (!input.files || input.files.length === 0) {
        alert('Please select an image file.');
        return;
    }
    
    var formData = new FormData();
    formData.append('project_id', projectId);
    formData.append('main_image', input.files[0]);
    
    var btn = input.nextElementSibling;
    var originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    
    fetch('<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); ?>/upload-main-image.php', {
        method: 'POST',
        body: formData
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.status === 'success') {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(function(error) {
        alert('Network error. Please try again.');
    })
    .finally(function() {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}

// ===== ENABLE IMAGE DELETION =====
var deletionMode = false;

function enableImageDeletion() {
    deletionMode = !deletionMode;
    var status = document.getElementById('imageDeleteStatus');
    var thumbs = document.querySelectorAll('.thumb');
    
    if (deletionMode) {
        status.style.display = 'block';
        thumbs.forEach(function(thumb, index) {
            // Skip the main image (index 0)
            if (index === 0) return;
            thumb.style.cursor = 'pointer';
            thumb.style.outline = '2px solid #dc3545';
            thumb.style.outlineOffset = '2px';
            thumb.style.opacity = '0.8';
            
            // Remove old click handlers and add delete handler
            thumb.onclick = function(e) {
                e.stopPropagation();
                if (confirm('Remove this image from gallery?')) {
                    deleteGalleryImage(thumb.src, index);
                }
            };
        });
    } else {
        status.style.display = 'none';
        thumbs.forEach(function(thumb) {
            thumb.style.cursor = 'pointer';
            thumb.style.outline = 'none';
            thumb.style.opacity = '1';
            // Restore original click handler
            var index = parseInt(thumb.getAttribute('data-index')) || 0;
            thumb.onclick = function() {
                swapMainImage(this, index);
            };
        });
    }
}

// ===== DELETE GALLERY IMAGE =====
function deleteGalleryImage(imageUrl, index) {
    var projectId = <?php echo (int) $project['id']; ?>;
    var filename = imageUrl.split('/').pop();
    
    fetch('<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); ?>/delete-gallery-image.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            project_id: projectId, 
            image: filename,
            index: index 
        })
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.status === 'success') {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(function(error) {
        alert('Network error. Please try again.');
    });
}

// ===== DELETE MULTIPLE GALLERY IMAGES =====
function deleteSelectedImages() {
    var selected = document.querySelectorAll('.thumb.selected');
    if (selected.length === 0) {
        alert('Please select images to delete.');
        return;
    }
    
    if (!confirm('Delete ' + selected.length + ' selected image(s)?')) return;
    
    var projectId = <?php echo (int) $project['id']; ?>;
    var images = [];
    selected.forEach(function(img) {
        var url = img.src;
        var filename = url.split('/').pop();
        images.push(filename);
    });
    
    fetch('<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); ?>/delete-gallery-images.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            project_id: projectId, 
            images: images 
        })
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.status === 'success') {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(function(error) {
        alert('Network error. Please try again.');
    });
}

    //edit mode
(function(){
    var isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
    if (!isLoggedIn) return;

    var toggleBtn = document.getElementById('editModeToggle');
    var toggleLabel = document.getElementById('editModeToggleLabel');
    var statusEl = document.getElementById('editModeStatus');
    if (!toggleBtn) return;

    var editing = false;

    function setEditable(on) {
        document.querySelectorAll('.editable-field').forEach(function(el){
            el.setAttribute('contenteditable', on ? 'true' : 'false');
        });
        document.querySelectorAll('.editable-input').forEach(function(el){
            el.style.display = on ? '' : 'none';
        });

        // min_sft is driven by a live unit converter — force it back to
        // raw sqft and lock the dropdown while editing, so what gets
        // saved always matches the DB column, not a converted display value.
        var sftDisplay = document.getElementById('sftDisplayValue');
        var sftSelect  = document.getElementById('sftUnitSelect');
        if (sftDisplay && sftSelect) {
            if (on) {
                sftSelect.value = 'sqft';
                sftSelect.disabled = true;
                sftSelect.title = 'Switch unit after saving';
            } else {
                sftSelect.disabled = false;
                sftSelect.title = '';
            }
        }
    }

    toggleBtn.addEventListener('click', function(){
        editing = !editing;
        document.body.classList.toggle('edit-mode-active', editing);
        toggleBtn.classList.toggle('active', editing);
        toggleLabel.textContent = editing ? 'Exit Edit Mode' : 'Edit Page';
        setEditable(editing);
        statusEl.textContent = editing ? 'Click a highlighted field to edit, then hit Save on that section.' : '';
    });
    
    // ===== SECTION SAVE BUTTON HANDLER =====
document.querySelectorAll('.section-save-btn').forEach(function(btn){
    btn.addEventListener('click', function(){
        var section = btn.closest('.editable-section');
        if (!section) return;
        var sectionKey = section.getAttribute('data-section');
        var projectId  = section.getAttribute('data-project-id');
        var fields = {};

        section.querySelectorAll('.editable-field[data-field]').forEach(function(el){
            fields[el.getAttribute('data-field')] = el.innerText.trim();
        });
        section.querySelectorAll('.editable-input[data-field]').forEach(function(el){
            fields[el.getAttribute('data-field')] = el.value.trim();
        });

        var msgEl = section.querySelector('.section-save-msg');
        btn.disabled = true;
        var originalHtml = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

        fetch('<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); ?>/save-property-section.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ project_id: projectId, section: sectionKey, fields: fields })
        })
        .then(function(r){ return r.json(); })
        .then(function(data){
            if (msgEl) {
                if (data.status === 'success') {
                    msgEl.textContent = '✅ Saved! Updated.';
                    msgEl.style.color = '#1a8f4c';

                    // If the address changed, the server geocoded it —
                    // move the live map to match, no reload needed.
                    if (data.lat && data.lng && typeof map !== 'undefined' && map) {
                        map.setView([data.lat, data.lng], 14);
                        map.eachLayer(function(layer){
                            if (layer instanceof L.Marker) map.removeLayer(layer);
                        });
                        L.marker([data.lat, data.lng]).addTo(map)
                            .bindPopup(fields.project_name || '').openPopup();
                    }
                } else {
                    msgEl.textContent = '❌ ' + (data.message || 'Save failed.');
                    msgEl.style.color = '#e74c3c';
                }
                setTimeout(function() { msgEl.textContent = ''; }, 3000);
            }
        })
        .catch(function(error){
            console.error('Save error:', error);
            if (msgEl) {
                msgEl.textContent = '❌ Network error. Please try again.';
                msgEl.style.color = '#e74c3c';
                setTimeout(function() { msgEl.textContent = ''; }, 3000);
            }
        })
        .finally(function(){
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    });
});

})();
</script>
</body>
</html>