<?php
if (!function_exists('slugify')) {
    function slugify($text) {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
    }
}

// ===============================
// Map a category_type (or legacy type) to the short "vertical code"
// used everywhere else in this file (cards, filters, JS). Moved to the
// TOP of the file because we now need it before $allTopCategories is
// finalized, not just later when building $projects.
// ===============================
if (!function_exists('map_to_vertical_type')) {
    function map_to_vertical_type(?string $oldType, ?int $listingTypeId): string {
        switch ($oldType) {
            case 'buy':
                return 'buy';
            case 'rent':
                return 'rent';
            case 'vendor':
                return 'vnd';
            case 'interior':
                return 'int';
            case 'local_services':
                return 'lsv';
            case 'hotels_homestays':
                return 'hhs';
            case 'pg_hostels':
                return 'pgh';
            case 'residential':
            case 'commercial':
            case 'land':
                return ($listingTypeId == 2) ? 'rent' : 'buy';
            case 'hotel':
            case 'homestay':
                return 'hhs';
            case 'pg':
            case 'hostel':
                return 'pgh';
            case 'home_services':
                return 'lsv';
            default:
                return 'other';
        }
    }
}

// Helper function to get top-level category type (walks the parent chain)
if (!function_exists('get_top_level_category_type')) {
    function get_top_level_category_type($catId, $allCategoriesById, $childrenByParentId) {
        $current = $allCategoriesById[$catId] ?? null;
        if (!$current) return null;

        // If parent_id is 0, this is top-level
        if ($current['parent_id'] == 0) {
            return $current['category_type'];
        }

        // Recursively find parent
        return get_top_level_category_type($current['parent_id'], $allCategoriesById, $childrenByParentId);
    }
}

// ===============================
// Fetch ALL top-level active categories from property_categories 
// (this drives the tabs — independent of whether they have projects yet)
// ===============================
$allTopCategories = [];
$topCatSql = "SELECT id, name, slug, category_type, icon 
              FROM property_categories 
              WHERE status='Active' AND parent_id = 0 
              ORDER BY category_order ASC, id ASC";
$topCatResult = $conn->query($topCatSql);
if ($topCatResult) {
    while ($row = $topCatResult->fetch_assoc()) {
        $allTopCategories[] = $row;
    }
}

// =====================================================
// Keep Hot Selling categories in main menu order
// =====================================================
$categoryOrder = [
    'buy' => 1,
    'rent' => 2,
    'interior' => 3,
    'vendor' => 4,
    'local_services' => 5,
    'hotels_homestays' => 6,
    'pg_hostels' => 7
];
usort($allTopCategories, function ($a, $b) use ($categoryOrder) {
    $orderA = $categoryOrder[$a['category_type']] ?? 999;
    $orderB = $categoryOrder[$b['category_type']] ?? 999;

    return $orderA <=> $orderB;
});

// Remove duplicate category_type entries (keeps the first one after the
// menu-order sort above, e.g. picks the correct "Interiors" tree)
$uniqueCategories = [];
foreach ($allTopCategories as $category) {
    $type = $category['category_type'];

    if (!isset($uniqueCategories[$type])) {
        $uniqueCategories[$type] = $category;
    }
}
$allTopCategories = array_values($uniqueCategories);

// ===============================
// FIX: give every top category the SAME short "vertical code" the
// project cards use (buy, rent, int, vnd, lsv, hhs, pgh). Without this,
// tabs compared their raw category_type ("interior", "vendor", ...)
// against the cards' mapped matched_category_type ("int", "vnd", ...)
// and NEVER matched — so every tab except Buy/Rent showed 0 results.
// ===============================
foreach ($allTopCategories as &$cat) {
    $cat['vertical_code'] = map_to_vertical_type($cat['category_type'], null);
}
unset($cat);

// ===============================
// Fetch the FULL active category tree (all levels) so each top-level
// tab can show its own subcategories, same source data as the nav mega-menu
// ===============================
$allCatsRaw = [];
$fullCatSql = "SELECT id, parent_id, name, slug, category_type
               FROM property_categories
               WHERE status='Active'
               ORDER BY category_order ASC, id ASC";
$fullCatResult = $conn->query($fullCatSql);
if ($fullCatResult) {
    while ($row = $fullCatResult->fetch_assoc()) {
        $allCatsRaw[] = $row;
    }
}

$childrenByParentId = [];
$allCategoriesById = [];
foreach ($allCatsRaw as $cat) {
    $pid = $cat['parent_id'] ?? 0;
    $childrenByParentId[$pid][] = $cat;
    $allCategoriesById[$cat['id']] = $cat;
}

// Returns [subcatId, ...all its descendant ids] — used so a project tagged
// at any depth under a subcategory still counts as belonging to it
function get_descendant_cat_ids($catId, array $childrenByParentId): array {
    $ids = [(int) $catId];
    foreach (($childrenByParentId[$catId] ?? []) as $child) {
        $ids = array_merge($ids, get_descendant_cat_ids($child['id'], $childrenByParentId));
    }
    return $ids;
}

// Get immediate children of a category
function get_immediate_child_categories($catId, array $childrenByParentId): array {
    return $childrenByParentId[$catId] ?? [];
}

// Build immediate-children (sections) for every top-level tab, e.g.
// Buy -> Residential Properties / Plots / Commercial / Agricultural
$subcatsByTopId = [];
foreach ($allTopCategories as $topCat) {
    $sections = $childrenByParentId[$topCat['id']] ?? [];
    $withIds = [];
    foreach ($sections as $section) {
        $withIds[] = [
            'id'      => $section['id'],
            'name'    => $section['name'],
            'slug'    => $section['slug'],
            'cat_ids' => get_descendant_cat_ids($section['id'], $childrenByParentId),
        ];
    }
    $subcatsByTopId[$topCat['id']] = $withIds;
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
// Fetch active projects with category_type mapping
// ===============================
// (map_to_vertical_type and get_top_level_category_type are now defined
// near the top of the file — see above.)

// Fetch projects with their categories
$projects = [];
$sql2 = "SELECT tp.*, pc.category_type as pc_category_type 
         FROM trending_projects tp
         LEFT JOIN property_categories pc ON tp.category_id = pc.id
         WHERE tp.status='Active' 
         ORDER BY tp.project_order ASC LIMIT 200";
$result2 = $conn->query($sql2);
if ($result2) {
    while ($row = $result2->fetch_assoc()) {
        // Determine the category type
        $rawType = '';
        
        // First try from the joined category
        if (!empty($row['pc_category_type'])) {
            $rawType = $row['pc_category_type'];
        }
        // Then try from the project's own category_type
        if (empty($rawType) && !empty($row['category_type'])) {
            $rawType = $row['category_type'];
        }
        
        // Map to vertical type
        $row['matched_category_type'] = map_to_vertical_type($rawType, $row['listing_type_id'] ?? null);
        
        // If still no match, try to find parent category type
        if ($row['matched_category_type'] == 'other' && !empty($row['category_id'])) {
            // Find the top-level parent
            $parentType = get_top_level_category_type($row['category_id'], $allCategoriesById, $childrenByParentId);
            if ($parentType) {
                $row['matched_category_type'] = map_to_vertical_type($parentType, $row['listing_type_id'] ?? null);
            }
        }
        
        $projects[] = $row;
    }
}

// Count projects per category_type (used for the badge number on each tab)
$categoryTypeCounts = [];
foreach ($projects as $p) {
    $t = $p['matched_category_type'];
    $categoryTypeCounts[$t] = ($categoryTypeCounts[$t] ?? 0) + 1;
}
$totalProjects = count($projects);

// Build data for sub-sub categories
$subSubCategoriesByParentId = [];
foreach ($allCatsRaw as $cat) {
    if ($cat['parent_id'] != 0) {
        $parent = $allCategoriesById[$cat['parent_id']] ?? null;
        if ($parent && $parent['parent_id'] != 0) {
            $subSubCategoriesByParentId[$cat['parent_id']][] = $cat;
        }
    }
}
?>

<style>
  /* ============================================
     DARK MODE SUPPORT - Override variables
     ============================================ */
  body.dark-mode .category-tab {
    background: var(--dm-bg-tertiary, #1c2333);
    border-color: var(--dm-border, #30363d);
    color: var(--dm-text-secondary, #8b949e);
  }
  body.dark-mode .category-tab:hover {
    border-color: var(--sy-gold, #f0b429);
    color: var(--sy-gold, #f0b429);
  }
  body.dark-mode .category-tab.active {
    background: var(--sy-gold, #f0b429);
    border-color: var(--sy-gold, #f0b429);
    color: var(--sy-navy-1, #12213a);
  }

  body.dark-mode .cs-card {
    background: var(--dm-bg-secondary, #161b22);
    border-color: var(--dm-border, #30363d);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
  }
  body.dark-mode .cs-card:hover {
    box-shadow: 0 12px 28px rgba(0,0,0,0.5);
  }
  body.dark-mode .cs-card .body h3 {
    color: var(--dm-text-primary, #f0f6fc);
  }
  body.dark-mode .cs-card .locality {
    color: var(--dm-text-secondary, #8b949e);
  }
  body.dark-mode .cs-card .price {
    color: var(--sy-gold, #f0b429);
  }
  body.dark-mode .cs-card .beds {
    color: var(--dm-text-secondary, #8b949e);
  }

  body.dark-mode .cs-nav-btn {
    background: var(--dm-bg-secondary, #161b22);
    border-color: var(--dm-border, #30363d);
    color: var(--dm-text-primary, #f0f6fc);
  }
  body.dark-mode .cs-nav-btn:hover {
    border-color: var(--sy-gold, #f0b429);
    color: var(--sy-gold, #f0b429);
  }
  body.dark-mode .cs-nav-btn:disabled {
    opacity: 0.3;
  }

  body.dark-mode .cs-wishlist-btn {
    background: var(--dm-bg-secondary, #161b22);
    color: var(--dm-text-primary, #f0f6fc);
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
  }
  body.dark-mode .cs-wishlist-btn:hover {
    background: var(--dm-bg-tertiary, #1c2333);
    color: #e0245e;
  }
  body.dark-mode .cs-wishlist-btn.active {
    color: #e0245e;
  }

  body.dark-mode .cs-empty {
    color: var(--dm-text-secondary, #8b949e);
  }

  body.dark-mode .section h2 {
    color: var(--dm-text-primary, #f0f6fc);
  }

  body.dark-mode .alert-warning {
    background: var(--dm-bg-tertiary, #1c2333);
    border-color: var(--dm-border, #30363d);
    color: var(--dm-text-secondary, #8b949e);
  }

  body.dark-mode .category-dropdown-panel {
    background: var(--dm-bg-secondary, #161b22);
    border-color: var(--dm-border, #30363d);
    box-shadow: 0 8px 30px rgba(0,0,0,0.5);
  }
  body.dark-mode .category-dropdown-panel .category-tab {
    background: transparent;
    border-color: transparent;
    color: var(--dm-text-secondary, #8b949e);
  }
  body.dark-mode .category-dropdown-panel .category-tab:hover {
    background: var(--dm-bg-tertiary, #1c2333);
    border-color: var(--sy-gold, #f0b429);
    color: var(--sy-gold, #f0b429);
  }
  body.dark-mode .category-dropdown-panel .category-tab.active {
    background: var(--sy-gold, #f0b429);
    border-color: var(--sy-gold, #f0b429);
    color: var(--sy-navy-1, #12213a);
  }
  body.dark-mode .category-dropdown-panel .category-section-title {
    color: var(--dm-text-secondary, #8b949e);
  }
  body.dark-mode .category-dropdown-panel .category-divider {
    background: var(--dm-border, #30363d);
  }

  /* ============= CATEGORY TABS WITH MORE BUTTON ============= */
  .category-tabs-wrapper {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-top: 1rem;
    margin-bottom: 0.5rem;
    position: relative;
  }

  .category-tabs-container {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
    flex: 1;
  }

  /* Individual category tab */
  .category-tab {
    flex: 0 0 auto;
    display: inline-flex;
    align-items: center;
    white-space: nowrap;
    border-radius: 2rem;
    padding: 0.45rem 1rem;
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    border: 1.5px solid #e7e9ee;
    background: #f8f9fc;
    color: #4a5568;
    cursor: pointer;
    transition: all 0.25s ease;
    letter-spacing: 0.02em;
    gap: 0.4rem;
  }

  .category-tab::before {
    content: '';
    display: inline-block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #c9a84c;
    opacity: 0.3;
    transition: all 0.25s ease;
    flex-shrink: 0;
  }

  .category-tab:hover {
    border-color: #c9a84c;
    color: #c9a84c;
    background: #fffbf0;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(201, 168, 76, 0.15);
  }
  .category-tab:hover::before {
    opacity: 1;
    transform: scale(1.2);
  }

  .category-tab.active {
    background: #c9a84c;
    border-color: #c9a84c;
    color: #fff;
    box-shadow: 0 4px 14px rgba(201, 168, 76, 0.3);
  }
  .category-tab.active::before {
    background: #fff;
    opacity: 1;
    transform: scale(1.1);
  }
  .category-tab.active:hover {
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(201, 168, 76, 0.35);
  }

  /* "All Categories" special styling */
  .category-tab-all {
    background: #12213a;
    border-color: #12213a;
    color: #fff;
  }
  .category-tab-all::before {
    background: #fff;
    opacity: 0.5;
  }
  .category-tab-all:hover {
    background: #1a2f4f;
    border-color: #1a2f4f;
    color: #fff;
    box-shadow: 0 4px 12px rgba(18, 33, 58, 0.2);
  }
  .category-tab-all.active {
    background: #c9a84c;
    border-color: #c9a84c;
    color: #fff;
  }
  .category-tab-all.active::before {
    background: #fff;
    opacity: 1;
  }

  body.dark-mode .category-tab {
    background: var(--dm-bg-tertiary, #1c2333);
    border-color: var(--dm-border, #30363d);
    color: var(--dm-text-secondary, #8b949e);
  }
  body.dark-mode .category-tab:hover {
    border-color: var(--sy-gold, #f0b429);
    color: var(--sy-gold, #f0b429);
    background: rgba(240, 180, 41, 0.08);
  }
  body.dark-mode .category-tab.active {
    background: var(--sy-gold, #f0b429);
    border-color: var(--sy-gold, #f0b429);
    color: var(--sy-navy-1, #12213a);
  }
  body.dark-mode .category-tab-all {
    background: #2d3748;
    border-color: #2d3748;
    color: #e2e8f0;
  }
  body.dark-mode .category-tab-all:hover {
    background: #3d4a5f;
    border-color: #3d4a5f;
  }

  /* ============ MORE BUTTON ============ */
  .category-more-btn {
    flex: 0 0 auto;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border-radius: 2rem;
    padding: 0.45rem 1.2rem;
    font-size: 0.8rem;
    font-weight: 600;
    border: 1.5px solid #e7e9ee;
    background: #f8f9fc;
    color: #4a5568;
    cursor: pointer;
    transition: all 0.25s ease;
    position: relative;
  }
  .category-more-btn:hover {
    border-color: #c9a84c;
    color: #c9a84c;
    background: #fffbf0;
    transform: translateY(-1px);
  }
  .category-more-btn .badge {
    background: #c9a84c;
    color: #fff;
    font-size: 0.6rem;
    padding: 1px 8px;
    border-radius: 10px;
    font-weight: 700;
  }
  .category-more-btn.active {
    border-color: #c9a84c;
    background: #c9a84c;
    color: #fff;
  }
  .category-more-btn.active .badge {
    background: #fff;
    color: #c9a84c;
  }
  .category-more-btn .chevron-icon {
    transition: transform 0.3s ease;
  }
  .category-more-btn.active .chevron-icon {
    transform: rotate(180deg);
  }

  body.dark-mode .category-more-btn {
    background: var(--dm-bg-tertiary, #1c2333);
    border-color: var(--dm-border, #30363d);
    color: var(--dm-text-secondary, #8b949e);
  }
  body.dark-mode .category-more-btn:hover {
    border-color: var(--sy-gold, #f0b429);
    color: var(--sy-gold, #f0b429);
  }
  body.dark-mode .category-more-btn .badge {
    background: var(--sy-gold, #f0b429);
    color: var(--sy-navy-1, #12213a);
  }
  body.dark-mode .category-more-btn.active {
    background: var(--sy-gold, #f0b429);
    border-color: var(--sy-gold, #f0b429);
    color: var(--sy-navy-1, #12213a);
  }

  /* ============ DROPDOWN PANEL - IMPROVED UI ============ */
  .category-dropdown-panel {
    display: none;
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    min-width: 320px;
    max-width: 480px;
    max-height: 420px;
    background: #fff;
    border: 1px solid #e7e9ee;
    border-radius: 16px;
    padding: 1.25rem;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
    z-index: 100;
    overflow-y: auto;
  }
  .category-dropdown-panel.open {
    display: block;
  }

  /* Dropdown header */
  .category-dropdown-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 0.75rem;
    margin-bottom: 0.75rem;
    border-bottom: 1px solid #f0f2f5;
  }
  .category-dropdown-header h4 {
    font-size: 0.85rem;
    font-weight: 700;
    color: #12213a;
    margin: 0;
  }
  .category-dropdown-header .close-dropdown {
    background: none;
    border: none;
    color: #8b9bb5;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 0 4px;
    transition: all 0.2s ease;
    line-height: 1;
  }
  .category-dropdown-header .close-dropdown:hover {
    color: #e0245e;
    transform: rotate(90deg);
  }

  body.dark-mode .category-dropdown-header {
    border-bottom-color: var(--dm-border, #30363d);
  }
  body.dark-mode .category-dropdown-header h4 {
    color: var(--dm-text-primary, #f0f6fc);
  }
  body.dark-mode .category-dropdown-header .close-dropdown {
    color: var(--dm-text-secondary, #8b949e);
  }
  body.dark-mode .category-dropdown-header .close-dropdown:hover {
    color: #e0245e;
  }

  /* Category grid in dropdown */
  .category-dropdown-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.4rem;
  }

  .category-dropdown-panel .category-tab {
    flex: 1 1 auto;
    justify-content: flex-start;
    font-size: 0.75rem;
    padding: 0.4rem 0.8rem;
    border-radius: 8px;
    border: 1px solid #eef0f3;
    background: #fafbfc;
    width: 100%;
  }
  .category-dropdown-panel .category-tab::before {
    width: 4px;
    height: 4px;
  }
  .category-dropdown-panel .category-tab:hover {
    background: #fffbf0;
    border-color: #c9a84c;
  }
  .category-dropdown-panel .category-tab.active {
    background: #c9a84c;
    border-color: #c9a84c;
    color: #fff;
  }

  /* Category sections in dropdown */
  .category-section-title {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #8b9bb5;
    padding: 0.5rem 0 0.25rem 0;
    grid-column: 1 / -1;
    border-bottom: 1px solid #f0f2f5;
    margin-bottom: 0.25rem;
  }
  .category-divider {
    grid-column: 1 / -1;
    height: 1px;
    background: #f0f2f5;
    margin: 0.25rem 0;
  }

  body.dark-mode .category-dropdown-panel .category-tab {
    background: var(--dm-bg-tertiary, #1c2333);
    border-color: var(--dm-border, #30363d);
    color: var(--dm-text-secondary, #8b949e);
  }
  body.dark-mode .category-dropdown-panel .category-tab:hover {
    background: rgba(240, 180, 41, 0.08);
    border-color: var(--sy-gold, #f0b429);
    color: var(--sy-gold, #f0b429);
  }
  body.dark-mode .category-dropdown-panel .category-tab.active {
    background: var(--sy-gold, #f0b429);
    border-color: var(--sy-gold, #f0b429);
    color: var(--sy-navy-1, #12213a);
  }
  body.dark-mode .category-section-title {
    color: var(--dm-text-secondary, #8b949e);
  }
  body.dark-mode .category-divider {
    background: var(--dm-border, #30363d);
  }

  /* Scrollbar styling for dropdown */
  .category-dropdown-panel::-webkit-scrollbar {
    width: 4px;
  }
  .category-dropdown-panel::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
  }
  .category-dropdown-panel::-webkit-scrollbar-thumb {
    background: #c9a84c;
    border-radius: 10px;
  }
  .category-dropdown-panel::-webkit-scrollbar-thumb:hover {
    background: #b8973a;
  }

  body.dark-mode .category-dropdown-panel::-webkit-scrollbar-track {
    background: var(--dm-bg-tertiary, #1c2333);
  }
  body.dark-mode .category-dropdown-panel::-webkit-scrollbar-thumb {
    background: var(--sy-gold, #f0b429);
  }

  /* Category count badge */
  .category-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.6rem;
    font-weight: 700;
    background: rgba(201, 168, 76, 0.15);
    color: #c9a84c;
    border-radius: 50%;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
  }
  .category-tab.active .category-count {
    background: rgba(255, 255, 255, 0.25);
    color: #fff;
  }
  .category-tab-all .category-count {
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
  }

  body.dark-mode .category-count {
    background: rgba(240, 180, 41, 0.2);
    color: var(--sy-gold, #f0b429);
  }
  body.dark-mode .category-tab.active .category-count {
    background: rgba(0, 0, 0, 0.15);
    color: var(--sy-navy-1, #12213a);
  }

  /* Project card slider */
  .cs-slider-wrap {
    position: relative;
    margin-top: 1.5rem;
  }
  .cs-grid {
    display: flex;
    gap: 1.25rem;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    scroll-behavior: smooth;
    padding-bottom: 0.5rem;
    -ms-overflow-style: none;
    scrollbar-width: none;
  }
  .cs-grid::-webkit-scrollbar {
    display: none;
  }
  .cs-card {
    position: relative;
    flex: 0 0 auto;
    width: 280px;
    scroll-snap-align: start;
    border-radius: 1rem;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    display: block;
    background: #fff;
    box-shadow: 0 6px 20px rgba(18,33,58,.08);
    border: 1px solid #e7e9ee;
    transition: box-shadow .2s ease, transform .2s ease;
  }
  .cs-card:hover {
    color: inherit;
    text-decoration: none;
    box-shadow: 0 12px 28px rgba(18,33,58,.14);
    transform: translateY(-2px);
  }
  .cs-card.hidden {
    display: none;
  }

  .cs-nav-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1px solid #e7e9ee;
    background: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #12213a;
    transition: all 0.2s ease;
  }
  .cs-nav-btn:hover {
    border-color: #1f6feb;
    color: #1f6feb;
  }
  .cs-nav-btn:disabled {
    opacity: .4;
    cursor: not-allowed;
  }

  .cs-card .thumb {
    height: 190px;
    background-size: cover;
    background-position: center;
    position: relative;
  }

  .cs-wishlist-btn {
    position: absolute;
    top: .75rem;
    right: .75rem;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    background: rgba(255,255,255,.9);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #12213a;
    font-size: 1.05rem;
    z-index: 2;
    box-shadow: 0 2px 8px rgba(18,33,58,.15);
    transition: all 0.2s ease;
    cursor: pointer;
  }
  .cs-wishlist-btn:hover {
    background: #fff;
    color: #e0245e;
  }
  .cs-wishlist-btn.active {
    color: #e0245e;
  }

  .cs-card .body {
    padding: .9rem 1rem 1.1rem;
  }
  .cs-card .body h3 {
    font-size: 1.05rem;
    font-weight: 700;
    margin-bottom: .25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .cs-card .locality {
    color: #6b7686;
    font-size: .88rem;
    margin-bottom: .5rem;
  }
  .cs-card .footer-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .cs-card .price {
    color: #c9a84c;
    font-weight: 700;
    font-size: 1rem;
  }
  .cs-card .beds {
    color: #6b7686;
    font-size: .85rem;
    display: flex;
    align-items: center;
    gap: .3rem;
  }

  .cs-empty {
    display: none;
    padding: 2rem;
    text-align: center;
    color: #6b7686;
  }

  /* Mobile responsive adjustments */
  @media (max-width: 576px) {
    .cs-card {
      width: 240px;
    }
    .cs-card .thumb {
      height: 160px;
    }
    .category-tab {
      font-size: 0.7rem;
      padding: 0.35rem 0.75rem;
    }
    .category-tab::before {
      width: 4px;
      height: 4px;
    }
    .category-more-btn {
      font-size: 0.7rem;
      padding: 0.35rem 0.75rem;
    }
    .category-dropdown-panel {
      min-width: 280px;
      max-width: 320px;
      right: -10px;
      padding: 1rem;
    }
    .category-dropdown-grid {
      grid-template-columns: 1fr 1fr;
    }
    .category-dropdown-panel .category-tab {
      font-size: 0.7rem;
      padding: 0.3rem 0.6rem;
    }
  }

  @media (max-width: 400px) {
    .category-tab {
      font-size: 0.65rem;
      padding: 0.25rem 0.6rem;
    }
    .category-more-btn {
      font-size: 0.65rem;
      padding: 0.25rem 0.6rem;
    }
    .category-dropdown-panel {
      min-width: 240px;
      max-width: 260px;
      right: -5px;
      padding: 0.75rem;
    }
    .category-dropdown-grid {
      grid-template-columns: 1fr;
    }
  }
  .subcat-tabs-row {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
    margin: 0.5rem 0 0.75rem;
    padding-top: 0.5rem;
    border-top: 1px dashed #e7e9ee;
}
.subcat-tab {
    border: 1px solid #e7e9ee;
    background: #fff;
    color: #6b7686;
    border-radius: 1.5rem;
    padding: 0.3rem 0.8rem;
    font-size: 0.72rem;
    font-weight: 600;
    cursor: pointer;
}
.subcat-tab:hover { border-color: #c9a84c; color: #c9a84c; }
.subcat-tab.active { background: #12213a; border-color: #12213a; color: #fff; }
body.dark-mode .subcat-tabs-row { border-top-color: var(--dm-border, #30363d); }
body.dark-mode .subcat-tab { background: var(--dm-bg-tertiary,#1c2333); border-color: var(--dm-border,#30363d); color: var(--dm-text-secondary,#8b949e); }
body.dark-mode .subcat-tab.active { background: var(--sy-gold,#f0b429); border-color: var(--sy-gold,#f0b429); color: var(--sy-navy-1,#12213a); }

  /* ============ SUB-SUB CATEGORY TABS ============ */
  .subsubcat-tabs-row {
    display: flex;
    flex-wrap: wrap;
    gap: 0.3rem;
    margin: 0.3rem 0 0.5rem 1.5rem;
    padding-top: 0.3rem;
    border-top: 1px dashed #eef0f3;
  }
  .subsubcat-tab {
    border: 1px solid #e7e9ee;
    background: #f8f9fc;
    color: #8b9bb5;
    border-radius: 1.5rem;
    padding: 0.2rem 0.7rem;
    font-size: 0.65rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
  }
  .subsubcat-tab:hover { 
    border-color: #c9a84c; 
    color: #c9a84c; 
    background: #fffbf0;
  }
  .subsubcat-tab.active { 
    background: #c9a84c; 
    border-color: #c9a84c; 
    color: #fff; 
  }
  
  body.dark-mode .subsubcat-tabs-row { 
    border-top-color: var(--dm-border, #30363d); 
  }
  body.dark-mode .subsubcat-tab { 
    background: var(--dm-bg-tertiary, #1c2333); 
    border-color: var(--dm-border, #30363d); 
    color: var(--dm-text-secondary, #8b949e); 
  }
  body.dark-mode .subsubcat-tab:hover { 
    border-color: var(--sy-gold, #f0b429); 
    color: var(--sy-gold, #f0b429); 
    background: rgba(240, 180, 41, 0.08);
  }
  body.dark-mode .subsubcat-tab.active { 
    background: var(--sy-gold, #f0b429); 
    border-color: var(--sy-gold, #f0b429); 
    color: var(--sy-navy-1, #12213a); 
  }

  .subcat-tabs-wrapper {
    margin-bottom: 0.5rem;
  }
  .subcat-tabs-wrapper .subcat-tabs-row {
    margin-top: 0.25rem;
  }
</style>

<section class="section pb-4">
    <div class="container">

        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="mt-1 mb-0" style="font-size:1.7rem;">Hot Selling Real Estate Projects in India</h2>
            </div>
        </div>

        <?php if (!empty($allTopCategories)): 
    $visibleCategories = array_slice($allTopCategories, 0, 5);
    $hiddenCategories = array_slice($allTopCategories, 5);
    $hiddenCount = count($hiddenCategories);
?>
    <div class="category-tabs-wrapper">
        <div class="category-tabs-container" id="categoryTabsContainer">
            <button type="button" class="category-tab category-tab-all active" data-category="">
                All Categories
                <span class="category-count"><?php echo $totalProjects; ?></span>
            </button>

            <?php foreach ($visibleCategories as $cat): 
                // FIX: use vertical_code (buy/rent/int/vnd/lsv/hhs/pgh) so this
                // matches the cards' matched_category_type — not the raw
                // category_type ("interior","vendor", etc.) which never matched.
                $count = $categoryTypeCounts[$cat['vertical_code']] ?? 0;
            ?>
                <button type="button" class="category-tab" data-category="<?php echo htmlspecialchars($cat['vertical_code']); ?>" data-top-id="<?php echo (int) $cat['id']; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                    <span class="category-count"><?php echo $count; ?></span>
                </button>
            <?php endforeach; ?>
        </div>

        <?php if ($hiddenCount > 0): ?>
            <div style="position: relative;">
                <button type="button" class="category-more-btn" id="categoryMoreBtn">
                    <i class="bi bi-grid-3x3-gap-fill" style="font-size:0.9rem;"></i>
                    More
                    <span class="badge"><?php echo $hiddenCount; ?></span>
                    <i class="bi bi-chevron-down chevron-icon" style="font-size:0.6rem;"></i>
                </button>

                <div class="category-dropdown-panel" id="categoryDropdownPanel">
                    <div class="category-dropdown-header">
                        <h4><i class="bi bi-tags"></i> All Categories</h4>
                        <button type="button" class="close-dropdown" id="closeDropdownBtn">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <div class="category-dropdown-grid">
                        <?php foreach ($hiddenCategories as $cat): 
                            // FIX: same vertical_code lookup as above
                            $count = $categoryTypeCounts[$cat['vertical_code']] ?? 0;
                        ?>
                            <button type="button" class="category-tab" data-category="<?php echo htmlspecialchars($cat['vertical_code']); ?>" data-top-id="<?php echo (int) $cat['id']; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                                <span class="category-count"><?php echo $count; ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php foreach ($allTopCategories as $topCat):
        $subs = $subcatsByTopId[$topCat['id']] ?? [];
        if (empty($subs)) continue;
    ?>
    <div class="subcat-tabs-wrapper" id="subcatWrapper-<?php echo (int) $topCat['id']; ?>" data-parent="<?php echo htmlspecialchars($topCat['vertical_code']); ?>" style="display:none;">
        <div class="subcat-tabs-row" id="subcatRow-<?php echo (int) $topCat['id']; ?>">
            <button type="button" class="subcat-tab active" data-cat-ids="" data-subcat-id="0">
                All <?php echo htmlspecialchars($topCat['name']); ?>
            </button>
            <?php foreach ($subs as $sub): ?>
                <button type="button" class="subcat-tab" data-cat-ids="<?php echo htmlspecialchars(implode(',', $sub['cat_ids'])); ?>" data-subcat-id="<?php echo (int) $sub['id']; ?>">
                    <?php echo htmlspecialchars($sub['name']); ?>
                </button>
            <?php endforeach; ?>
        </div>
        
        <!-- Sub-sub categories row (hidden initially) -->
        <div class="subsubcat-tabs-row" id="subsubcatRow-<?php echo (int) $topCat['id']; ?>" style="display:none;"></div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

        <?php if (!empty($projects)): ?>
            <div class="cs-slider-wrap">
                <?php if (count($projects) >= 4): ?>
                    <div class="d-flex justify-content-end gap-2 mb-2">
                        <button type="button" class="cs-nav-btn" id="csPrev" aria-label="Previous">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button type="button" class="cs-nav-btn" id="csNext" aria-label="Next">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                <?php endif; ?>

                <div class="cs-grid" id="cityProjectGrid">
                    <!-- In the card display section -->
<?php foreach ($projects as $project):
    $slug = ($hasSlugColumn && !empty($project['slug']))
        ? $project['slug']
        : slugify($project['project_name']);

    // Image handling with placeholder fallback
    $imagePath = '';
    if (!empty($project['image'])) {
        // Check if the image exists in the uploads directory
        $fullImagePath = "../admin/uploads/trending-projects/" . $project['image'];
        if (file_exists($fullImagePath) && is_file($fullImagePath)) {
            $imagePath = $fullImagePath;
        } else {
            // Try alternative path
            $altImagePath = "admin/uploads/trending-projects/" . $project['image'];
            if (file_exists($altImagePath) && is_file($altImagePath)) {
                $imagePath = $altImagePath;
            } else {
                // Use placeholder
                $imagePath = "https://placehold.co/400x300/12213a/c9a84c?text=" . urlencode($project['project_name']);
            }
        }
    } else {
        $imagePath = "https://placehold.co/400x300/12213a/c9a84c?text=" . urlencode($project['project_name']);
    }
    
    // Determine if this is a service type
    $isService = in_array($project['category_type'], ['home_services', 'vendor', 'interior', 'local_services']);
    $isPGHostel = in_array($project['category_type'], ['pg', 'hostel']);
    $isHotel = in_array($project['category_type'], ['hotel', 'homestay']);
    
    // Format price display
    $priceDisplay = !empty($project['price']) ? $project['price'] : 'Price on Request';
?>
    <a href="property-listing/<?php echo htmlspecialchars($slug); ?>/" class="cs-card" data-category="<?php echo htmlspecialchars($project['matched_category_type']); ?>" data-cat-id="<?php echo (int) ($project['category_id'] ?? 0); ?>">
        <div class="thumb" style="background-image:url('<?php echo htmlspecialchars($imagePath); ?>');">
            <button type="button" class="cs-wishlist-btn" data-project-id="<?php echo (int) $project['id']; ?>" aria-label="Add to wishlist">
                <i class="bi bi-heart"></i>
            </button>
        </div>
        <div class="body">
            <h3><?php echo htmlspecialchars($project['project_name']); ?></h3>
            <div class="locality">
                <i class="bi bi-geo-alt"></i>
                <?php echo htmlspecialchars($project['address'] ?? 'Address not available'); ?>
            </div>
            <div class="footer-row">
                <div class="price"><?php echo htmlspecialchars($priceDisplay); ?></div>
                <?php if (!empty($project['bedrooms']) && !$isService && !$isPGHostel && !$isHotel): ?>
                    <div class="beds"><i class="bi bi-door-open"></i> <?php echo (int) $project['bedrooms']; ?> BHK</div>
                <?php elseif ($isPGHostel): ?>
                    <div class="beds"><i class="bi bi-building"></i> PG/Hostel</div>
                <?php elseif ($isHotel): ?>
                    <div class="beds"><i class="bi bi-hotel"></i> Hotel</div>
                <?php elseif ($isService): ?>
                    <div class="beds"><i class="bi bi-tools"></i> Service</div>
                <?php endif; ?>
            </div>
        </div>
    </a>
<?php endforeach; ?>
                </div>
            </div>
            <div class="cs-empty" id="cityEmptyMsg">No projects found for this selection yet.</div>
        <?php else: ?>
            <div class="alert alert-warning mt-4 mb-0">
                No projects found.
            </div>
        <?php endif; ?>

    </div>
</section>

<script>
// ============ Category tabs with More button dropdown + subcategory tabs + sub-subcategory tabs ============
(function(){
    const moreBtn = document.getElementById('categoryMoreBtn');
    const dropdownPanel = document.getElementById('categoryDropdownPanel');
    const closeDropdownBtn = document.getElementById('closeDropdownBtn');
    const allTabs = document.querySelectorAll('.category-tab');
    const containerTabs = document.querySelectorAll('#categoryTabsContainer .category-tab');
    const dropdownTabs = document.querySelectorAll('#categoryDropdownPanel .category-tab');
    const cards = document.querySelectorAll('#cityProjectGrid .cs-card');
    const emptyMsg = document.getElementById('cityEmptyMsg');
    const subcatWrappers = document.querySelectorAll('.subcat-tabs-wrapper');

    // Store sub-sub categories data from PHP
    const subSubCategoriesByParentId = <?php echo json_encode($subSubCategoriesByParentId); ?>;

    // Toggle dropdown
    if (moreBtn && dropdownPanel) {
        moreBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const isOpen = dropdownPanel.classList.toggle('open');
            this.classList.toggle('active', isOpen);
        });

        if (closeDropdownBtn) {
            closeDropdownBtn.addEventListener('click', function() {
                dropdownPanel.classList.remove('open');
                moreBtn.classList.remove('active');
            });
        }

        document.addEventListener('click', function(e) {
            if (!dropdownPanel.contains(e.target) && !moreBtn.contains(e.target)) {
                dropdownPanel.classList.remove('open');
                moreBtn.classList.remove('active');
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && dropdownPanel.classList.contains('open')) {
                dropdownPanel.classList.remove('open');
                moreBtn.classList.remove('active');
            }
        });
    }

    // Filter by top-level category type
    function filterByCategory(category) {
        let visibleCount = 0;
        cards.forEach(function(card) {
            const matches = category === '' || card.dataset.category === category;
            card.classList.toggle('hidden', !matches);
            if (matches) visibleCount++;
        });

        if (emptyMsg) {
            emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    // Show the correct subcategory wrapper for the selected top-level tab
    function showSubcatWrapperFor(categoryType) {
        subcatWrappers.forEach(wrapper => {
            wrapper.style.display = (wrapper.dataset.parent === categoryType) ? 'block' : 'none';
            
            // Reset subcat tabs to "All" state
            const subcatRow = wrapper.querySelector('.subcat-tabs-row');
            if (subcatRow) {
                subcatRow.querySelectorAll('.subcat-tab').forEach((t, i) => {
                    t.classList.toggle('active', i === 0);
                });
            }
            
            // Hide sub-sub category row
            const subsubRow = wrapper.querySelector('.subsubcat-tabs-row');
            if (subsubRow) {
                subsubRow.style.display = 'none';
                subsubRow.innerHTML = '';
            }
        });
    }

    // Filter cards by category IDs
    function filterBySubcat(catIdsAttr, subcatId, wrapper) {
        const ids = catIdsAttr ? catIdsAttr.split(',').map(Number) : null;
        let visibleCount = 0;
        
        const activeTopTab = document.querySelector('.category-tab.active');
        const topCategory = activeTopTab ? activeTopTab.dataset.category : '';
        
        cards.forEach(function(card) {
            const matchesTop = topCategory === '' || card.dataset.category === topCategory;
            const matchesSub = !ids || ids.includes(Number(card.dataset.catId));
            const matches = matchesTop && matchesSub;
            card.classList.toggle('hidden', !matches);
            if (matches) visibleCount++;
        });
        
        if (emptyMsg) emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
        
        // Show sub-sub categories for this subcategory if any
        if (wrapper && subcatId > 0) {
            showSubSubCategories(subcatId, wrapper);
        } else if (wrapper) {
            // Hide sub-sub row if "All" is selected
            const subsubRow = wrapper.querySelector('.subsubcat-tabs-row');
            if (subsubRow) {
                subsubRow.style.display = 'none';
                subsubRow.innerHTML = '';
            }
        }
    }

    // Show sub-sub categories for a given subcategory
    function showSubSubCategories(subcatId, wrapper) {
        const subsubRow = wrapper.querySelector('.subsubcat-tabs-row');
        if (!subsubRow) return;
        
        const children = subSubCategoriesByParentId[subcatId] || [];
        
        if (children.length === 0) {
            subsubRow.style.display = 'none';
            subsubRow.innerHTML = '';
            return;
        }
        
        // Build sub-sub tabs
        let html = '';
        children.forEach(function(child) {
            // Get descendant IDs for this sub-sub category
            // We need to fetch all descendant IDs for filtering
            const descendantIds = getDescendantIds(child.id);
            const idsAttr = descendantIds.join(',');
            html += '<button type="button" class="subsubcat-tab" data-cat-ids="' + idsAttr + '" data-subsubcat-id="' + child.id + '">';
            html += child.name;
            html += '</button>';
        });
        
        // Add "All" option at the beginning
        html = '<button type="button" class="subsubcat-tab active" data-cat-ids="" data-subsubcat-id="0">All</button>' + html;
        
        subsubRow.innerHTML = html;
        subsubRow.style.display = 'flex';
        
        // Add click handlers to sub-sub tabs
        subsubRow.querySelectorAll('.subsubcat-tab').forEach(function(tab) {
            tab.addEventListener('click', function(e) {
                e.stopPropagation();
                // Remove active from siblings
                subsubRow.querySelectorAll('.subsubcat-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                // Filter by this sub-sub category
                const catIds = this.dataset.catIds;
                const ids = catIds ? catIds.split(',').map(Number) : null;
                
                // Get the active subcat to combine filters
                const subcatRow = wrapper.querySelector('.subcat-tabs-row');
                const activeSubcat = subcatRow ? subcatRow.querySelector('.subcat-tab.active') : null;
                const subcatIds = activeSubcat ? activeSubcat.dataset.catIds : '';
                const subIds = subcatIds ? subcatIds.split(',').map(Number) : null;
                
                // Combine filters: subcat AND subsubcat
                let visibleCount = 0;
                const activeTopTab = document.querySelector('.category-tab.active');
                const topCategory = activeTopTab ? activeTopTab.dataset.category : '';
                
                cards.forEach(function(card) {
                    const matchesTop = topCategory === '' || card.dataset.category === topCategory;
                    const matchesSub = !subIds || subIds.includes(Number(card.dataset.catId));
                    const matchesSubSub = !ids || ids.includes(Number(card.dataset.catId));
                    const matches = matchesTop && matchesSub && matchesSubSub;
                    card.classList.toggle('hidden', !matches);
                    if (matches) visibleCount++;
                });
                
                if (emptyMsg) emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
            });
        });
    }

    // Helper to get descendant IDs (similar to PHP function)
    function getDescendantIds(catId) {
        const children = subSubCategoriesByParentId[catId] || [];
        let ids = [catId];
        children.forEach(function(child) {
            ids = ids.concat(getDescendantIds(child.id));
        });
        return ids;
    }

    // Handle top-level tab clicks
    allTabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            allTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const selectedCategory = this.dataset.category;
            filterByCategory(selectedCategory);
            showSubcatWrapperFor(selectedCategory);

            if (dropdownPanel && dropdownPanel.classList.contains('open')) {
                dropdownPanel.classList.remove('open');
                if (moreBtn) {
                    moreBtn.classList.remove('active');
                }
            }
        });
    });

    // Sync dropdown tabs with container tabs
    dropdownTabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            const category = this.dataset.category;
            containerTabs.forEach(function(t) {
                if (t.dataset.category === category) {
                    t.classList.add('active');
                } else {
                    t.classList.remove('active');
                }
            });
        });
    });

    // Handle subcategory tab clicks
    subcatWrappers.forEach(function(wrapper) {
        const subcatRow = wrapper.querySelector('.subcat-tabs-row');
        if (!subcatRow) return;
        
        subcatRow.querySelectorAll('.subcat-tab').forEach(function(tab) {
            tab.addEventListener('click', function(e) {
                e.stopPropagation();
                // Remove active from siblings in this row
                subcatRow.querySelectorAll('.subcat-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                // Filter by this subcategory
                const catIds = this.dataset.catIds;
                const subcatId = parseInt(this.dataset.subcatId) || 0;
                filterBySubcat(catIds, subcatId, wrapper);
            });
        });
    });

    // Initial state - show all
    filterByCategory('');
})();

// ============ Slider prev/next ============
(function(){
    const track = document.getElementById('cityProjectGrid');
    const prevBtn = document.getElementById('csPrev');
    const nextBtn = document.getElementById('csNext');
    if (!track || !prevBtn || !nextBtn) return;

    const scrollAmount = () => track.querySelector('.cs-card')?.offsetWidth + 20 || 300;

    prevBtn.addEventListener('click', () => track.scrollBy({ left: -scrollAmount(), behavior: 'smooth' }));
    nextBtn.addEventListener('click', () => track.scrollBy({ left: scrollAmount(), behavior: 'smooth' }));

    function updateButtons() {
        prevBtn.disabled = track.scrollLeft <= 4;
        nextBtn.disabled = track.scrollLeft + track.clientWidth >= track.scrollWidth - 4;
    }
    track.addEventListener('scroll', updateButtons);
    window.addEventListener('resize', updateButtons);
    updateButtons();
})();

// ============ Wishlist ============
(function(){
    const STORAGE_KEY = 'tatito_wishlist';

    function getWishlist() {
        try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || []; }
        catch (e) { return []; }
    }
    function saveWishlist(list) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(list));
    }

    const wishlist = getWishlist();

    document.querySelectorAll('.cs-wishlist-btn').forEach(function(btn){
        const id = btn.dataset.projectId;
        const icon = btn.querySelector('i');

        if (wishlist.includes(id)) {
            btn.classList.add('active');
            icon.classList.remove('bi-heart');
            icon.classList.add('bi-heart-fill');
        }

        btn.addEventListener('click', function(e){
            e.preventDefault();
            e.stopPropagation();

            let list = getWishlist();
            const isActive = btn.classList.toggle('active');

            if (isActive) {
                if (!list.includes(id)) list.push(id);
                icon.classList.remove('bi-heart');
                icon.classList.add('bi-heart-fill');
            } else {
                list = list.filter(x => x !== id);
                icon.classList.remove('bi-heart-fill');
                icon.classList.add('bi-heart');
            }

            saveWishlist(list);
        });
    });
})();

// ============ Dark Mode ============
(function(){
    function handleThemeChange() {
        const isDark = document.body.classList.contains('dark-mode');
    }

    document.addEventListener('themeChanged', function(e) {
        handleThemeChange();
    });

    handleThemeChange();

    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                handleThemeChange();
            }
        });
    });
    observer.observe(document.body, { attributes: true });
})();
</script>