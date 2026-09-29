<?php
if (!function_exists('slugify')) {
    function slugify($text) {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
    }
}

// ===============================
// Fetch Section Heading
// ===============================
$section = [
    'title' => 'Sell / List Property',
    'subtitle' => 'List with confidence'
];
try {
    $sectionResult = $conn->query("SELECT * FROM trending_projects_section LIMIT 1");
    if ($sectionResult && $sectionResult->num_rows > 0) {
        $section = $sectionResult->fetch_assoc();
    }
} catch (\Throwable $e) {
    // keep default $section above
}

// ===============================
// Step 1: Find the "Sell" menu's id from the menus table.
// menus columns confirmed: id, menu_name, menu_slug, menu_order, status.
// ===============================
$sellMenuId = null;
$menusTableError = false;
try {
    $menuResult = $conn->query("SELECT id FROM menus WHERE (LOWER(menu_name) = 'sell' OR LOWER(menu_slug) = 'sell') AND status = 'Active' LIMIT 1");
    if ($menuResult && $menuResult->num_rows > 0) {
        $menuRow = $menuResult->fetch_assoc();
        $sellMenuId = (int) $menuRow['id'];
    }
} catch (\Throwable $e) {
    $menusTableError = true;
}

// ===============================
// Detect whether trending_projects has a real `slug` column
// ===============================
$hasSlugColumn = false;
try {
    $colCheck = $conn->query("SHOW COLUMNS FROM trending_projects LIKE 'slug'");
    if ($colCheck && $colCheck->num_rows > 0) {
        $hasSlugColumn = true;
    }
} catch (\Throwable $e) {
    // ignore — falls back to slugify()
}

// ===============================
// trending_projects links to property_categories via category_id
// ===============================
if (!defined('CATEGORY_ID_COLUMN')) {
    define('CATEGORY_ID_COLUMN', 'category_id');
}
$hasCategoryIdColumn = false;
try {
    $catColCheck = $conn->query("SHOW COLUMNS FROM trending_projects LIKE '" . CATEGORY_ID_COLUMN . "'");
    if ($catColCheck && $catColCheck->num_rows > 0) {
        $hasCategoryIdColumn = true;
    }
} catch (\Throwable $e) {
    // leave false — warning banner below will explain
}

// ===============================
// Step 2: Fetch active property categories that belong to the Sell menu
// (drives the "All property types" filter dropdown, scoped to Sell only).
// Since categories don't have menu_id linked, we'll fetch by other methods
// ===============================
$propertyCategories = [];
$sellCategoryIds = [];

// Method 1: Try to get categories by menu_id (if any exist)
if ($sellMenuId !== null) {
    try {
        $stmt = $conn->prepare("SELECT id, name, slug FROM property_categories WHERE status = 'Active' AND menu_id = ? ORDER BY category_order ASC");
        $stmt->bind_param('i', $sellMenuId);
        $stmt->execute();
        $catResult = $stmt->get_result();
        while ($row = $catResult->fetch_assoc()) {
            $propertyCategories[] = $row;
            $sellCategoryIds[] = (int) $row['id'];
        }
        $stmt->close();
    } catch (\Throwable $e) {
        $propertyCategories = [];
        $sellCategoryIds = [];
    }
}

// Method 2: If no categories found with menu_id, fetch by category_type
// that are typically associated with Selling (all residential/commercial categories)
if (empty($propertyCategories)) {
    try {
        // Fetch all residential and commercial categories
        $sellTypes = ['residential', 'commercial', 'land'];
        $placeholders = implode(',', array_fill(0, count($sellTypes), '?'));
        $types = str_repeat('s', count($sellTypes));
        
        $sql = "SELECT id, name, slug FROM property_categories 
                WHERE status = 'Active' 
                AND category_type IN ($placeholders)
                ORDER BY category_order ASC LIMIT 20";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$sellTypes);
        $stmt->execute();
        $catResult = $stmt->get_result();
        while ($row = $catResult->fetch_assoc()) {
            $propertyCategories[] = $row;
            $sellCategoryIds[] = (int) $row['id'];
        }
        $stmt->close();
    } catch (\Throwable $e) {
        // ignore
    }
}

// Method 3: If still empty, fetch all active categories
if (empty($propertyCategories)) {
    try {
        $sql = "SELECT id, name, slug FROM property_categories 
                WHERE status = 'Active' 
                ORDER BY category_order ASC LIMIT 20";
        $result = $conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $propertyCategories[] = $row;
                $sellCategoryIds[] = (int) $row['id'];
            }
        }
    } catch (\Throwable $e) {
        $propertyCategories = [];
        $sellCategoryIds = [];
    }
}

// ===============================
// Step 3: Fetch Trending Projects 
// First try by category_ids, then by listing_type_id = 1 (For Sale)
// ===============================
$projects = [];
$hasViewsColumn = false;
try {
    $viewsCheck = $conn->query("SHOW COLUMNS FROM trending_projects LIKE 'views'");
    if ($viewsCheck && $viewsCheck->num_rows > 0) {
        $hasViewsColumn = true;
    }
} catch (\Throwable $e) {
    // ignore — falls back to project_order
}

$orderBy = $hasViewsColumn ? "views DESC" : "project_order DESC";

// Method 1: Try to get projects by category IDs
if ($hasCategoryIdColumn && !empty($sellCategoryIds)) {
    try {
        $placeholders = implode(',', array_fill(0, count($sellCategoryIds), '?'));
        $types = str_repeat('i', count($sellCategoryIds));
        $sql = "SELECT * FROM trending_projects
                WHERE status='Active' AND " . CATEGORY_ID_COLUMN . " IN ($placeholders)
                ORDER BY $orderBy LIMIT 12";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$sellCategoryIds);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $projects[] = $row;
        }
        $stmt->close();
    } catch (\Throwable $e) {
        $projects = [];
    }
}

// Method 2: If no projects found, try by listing_type_id = 1 (For Sale)
if (empty($projects)) {
    try {
        $sql = "SELECT * FROM trending_projects 
                WHERE status='Active' AND listing_type_id = 1 
                ORDER BY $orderBy LIMIT 12";
        $result = $conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $projects[] = $row;
            }
        }
    } catch (\Throwable $e) {
        $projects = [];
    }
}

// Method 3: If still no projects, get all active projects
if (empty($projects)) {
    try {
        $sql = "SELECT * FROM trending_projects 
                WHERE status='Active' 
                ORDER BY $orderBy LIMIT 12";
        $result = $conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $projects[] = $row;
            }
        }
    } catch (\Throwable $e) {
        $projects = [];
    }
}
?>
<style>
  /* ============================================
     DARK MODE SUPPORT - Override variables
     ============================================ */
  body.dark-mode .pp-card {
    background: var(--dm-bg-secondary, #161b22);
    border-color: var(--dm-border, #30363d);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
  }
  body.dark-mode .pp-card:hover {
    box-shadow: 0 12px 28px rgba(0,0,0,0.5);
  }
  body.dark-mode .pp-card .body h3 {
    color: var(--dm-text-primary, #f0f6fc);
  }
  body.dark-mode .pp-card .locality {
    color: var(--dm-text-secondary, #8b949e);
  }
  body.dark-mode .pp-card .price {
    color: var(--sy-gold, #f0b429);
  }
  body.dark-mode .pp-card .beds {
    color: var(--dm-text-secondary, #8b949e);
  }
  body.dark-mode .pp-card .view-count {
    color: var(--dm-text-secondary, #8b949e);
  }

  body.dark-mode .pp-nav-btn {
    background: var(--dm-bg-secondary, #161b22);
    border-color: var(--dm-border, #30363d);
    color: var(--dm-text-primary, #f0f6fc);
  }
  body.dark-mode .pp-nav-btn:hover {
    border-color: var(--sy-gold, #f0b429);
    color: var(--sy-gold, #f0b429);
  }
  body.dark-mode .pp-nav-btn:disabled {
    opacity: 0.3;
  }

  body.dark-mode .pp-wishlist-btn {
    background: var(--dm-bg-secondary, #161b22);
    color: var(--dm-text-primary, #f0f6fc);
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
  }
  body.dark-mode .pp-wishlist-btn:hover {
    background: var(--dm-bg-tertiary, #1c2333);
    color: #e0245e;
  }
  body.dark-mode .pp-wishlist-btn.active {
    color: #e0245e;
  }

  body.dark-mode .section-title-wrapper h2 {
    color: var(--dm-text-primary, #f0f6fc);
  }
  body.dark-mode .section-title-wrapper .section-subtitle {
    color: var(--sy-gold, #f0b429);
  }

  body.dark-mode .alert-warning {
    background: var(--dm-bg-tertiary, #1c2333);
    border-color: var(--dm-border, #30363d);
    color: var(--dm-text-secondary, #8b949e);
  }

  body.dark-mode .pp-no-results {
    color: var(--dm-text-secondary, #8b949e);
  }

  body.dark-mode .thumb span {
    background: rgba(0,0,0,0.8) !important;
    color: var(--dm-text-primary, #f0f6fc) !important;
  }

  /* ============================================
     ORIGINAL STYLES
     ============================================ */
  .pp-slider-wrap{ position:relative; }
  .pp-slider-track{
    display:flex;
    gap:1.25rem;
    overflow-x:auto;
    scroll-snap-type:x mandatory;
    scroll-behavior:smooth;
    padding-bottom:.5rem;
    -ms-overflow-style:none;
    scrollbar-width:none;
  }
  .pp-slider-track::-webkit-scrollbar{ display:none; }

  .pp-filter-wrap{
    display:flex;
    align-items:center;
    gap:.6rem;
  }

  .pp-card{
    flex:0 0 auto;
    width:280px;
    scroll-snap-align:start;
    border-radius:1rem;
    overflow:hidden;
    text-decoration:none;
    color:inherit;
    display:block;
    background:#fff;
    box-shadow:0 6px 20px rgba(18,33,58,.08);
    border:1px solid #e7e9ee;
    transition:box-shadow .2s ease, transform .2s ease;
    position:relative;
  }
  .pp-card:hover{
    color:inherit;
    text-decoration:none;
    box-shadow:0 12px 28px rgba(18,33,58,.14);
    transform:translateY(-2px);
  }
  .pp-card.pp-card-hidden{ display:none !important; }

  .pp-card .thumb{
    height:190px;
    background-size:cover;
    background-position:center;
    border-radius:0;
    position:relative;
  }

  .pp-wishlist-btn{
    position:absolute;
    top:.75rem; right:.75rem;
    width:36px; height:36px;
    border-radius:50%;
    border:none;
    background:rgba(255,255,255,.9);
    display:flex;
    align-items:center;
    justify-content:center;
    color:#12213a;
    font-size:1.05rem;
    z-index:2;
    box-shadow:0 2px 8px rgba(18,33,58,.15);
    transition: all 0.2s ease;
    cursor: pointer;
  }
  .pp-wishlist-btn:hover{ background:#fff; color:#e0245e; }
  .pp-wishlist-btn.active{ color:#e0245e; }

  .pp-card .body{ padding:.9rem 1rem 1.1rem; }
  .pp-card .body h3{
    font-size:1.05rem;
    font-weight:700;
    margin-bottom:.25rem;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }
  .pp-card .locality{
    color:#6b7686;
    font-size:.88rem;
    margin-bottom:.5rem;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }
  .pp-card .footer-row{
    display:flex;
    justify-content:space-between;
    align-items:center;
  }
  .pp-card .price{
    color:#c9a84c;
    font-weight:700;
    font-size:1rem;
    transition: color 0.2s ease;
  }
  .pp-card .beds{
    color:#6b7686;
    font-size:.85rem;
    display:flex;
    align-items:center;
    gap:.3rem;
  }
  .pp-card .view-count{
    color:#6b7686;
    font-size:.78rem;
    display:flex;
    align-items:center;
    gap:.3rem;
    margin-top:.25rem;
  }

  .pp-nav-btn{
    width:36px; height:36px;
    border-radius:50%;
    border:1px solid #e7e9ee;
    background:#fff;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    color:#12213a;
    transition: all 0.2s ease;
  }
  .pp-nav-btn:hover{ border-color:#1f6feb; color:#1f6feb; }
  .pp-nav-btn:disabled{ opacity:.4; cursor:not-allowed; }

  .pp-no-results{
    color:#6b7686;
    font-size:.9rem;
    padding:.75rem 0;
  }

  /* Section title styling */
  .section-title-wrapper{
    position:relative;
    padding-bottom:.5rem;
  }
  .section-title-wrapper .section-subtitle{
    font-size:.85rem;
    font-weight:600;
    color:#1f6feb;
    text-transform:uppercase;
    letter-spacing:.5px;
    display:block;
    margin-bottom:.25rem;
    transition: color 0.2s ease;
  }
  .section-title-wrapper h2{
    font-size:1.7rem;
    font-weight:700;
    margin:0;
    transition: color 0.2s ease;
  }
  .section-title-wrapper .title-underline{
    width:60px;
    height:3px;
    background:linear-gradient(90deg, #c9a84c, #c9a84c);
    border-radius:2px;
    margin-top:.5rem;
  }

  /* Mobile responsive */
  @media (max-width: 576px) {
    .pp-card {
      width: 240px;
    }
    .pp-card .thumb {
      height: 160px;
    }
    .section-title-wrapper h2 {
      font-size: 1.3rem;
    }
  }
</style>

<section class="section pt-4 pp-slider-wrap">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div class="section-title-wrapper">
                <h2 class="mt-1 mb-0">
                    Sell / List Property
                </h2>
                <div class="title-underline"></div>
            </div>
            <div class="pp-filter-wrap">
                <div class="d-flex gap-2">
                    <button type="button" class="pp-nav-btn" id="ppPrevSell" aria-label="Previous">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button type="button" class="pp-nav-btn" id="ppNextSell" aria-label="Next">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <?php if ($menusTableError): ?>
            <div class="alert alert-warning mb-0">
                <i class="bi bi-info-circle"></i>
                The <code>menus</code> table couldn't be read, so the Sell section can't look up its menu id.
            </div>
        <?php elseif ($sellMenuId === null): ?>
            <div class="alert alert-warning mb-0">
                <i class="bi bi-info-circle"></i>
                Couldn't find a "Sell" row in the <code>menus</code> table (checked <code>menu_name</code> / <code>menu_slug</code> = "sell").
            </div>
        <?php elseif (!$hasCategoryIdColumn): ?>
            <div class="alert alert-warning mb-0">
                <i class="bi bi-info-circle"></i>
                trending_projects doesn't have a <?php echo CATEGORY_ID_COLUMN; ?> column yet, so projects can't be grouped by category.
            </div>
        <?php elseif (empty($projects)): ?>
            <div class="alert alert-warning">
                <i class="bi bi-info-circle"></i>
                No properties available for sale yet. Please add projects with listing_type_id = 1 (For Sale) or assign categories to the Sell menu.
            </div>
        <?php else: ?>
            <div class="pp-slider-track" id="ppTrackSell">
                <?php foreach ($projects as $project):
                    $slug = ($hasSlugColumn && !empty($project['slug']))
                        ? $project['slug']
                        : slugify($project['project_name']);

                    // Image handling with placeholder fallback
                    $imagePath = '';
                    if (!empty($project['image'])) {
                        // Check if the image file exists in the uploads directory
                        $fullImagePath = "../admin/uploads/trending-projects/" . $project['image'];
                        if (file_exists($fullImagePath) && is_file($fullImagePath)) {
                            $imagePath = $fullImagePath;
                        } else {
                            // Try alternative path
                            $altImagePath = "admin/uploads/trending-projects/" . $project['image'];
                            if (file_exists($altImagePath) && is_file($altImagePath)) {
                                $imagePath = $altImagePath;
                            } else {
                                // Image doesn't exist, use placeholder
                                $imagePath = "https://placehold.co/400x300/12213a/c9a84c?text=" . urlencode($project['project_name']);
                            }
                        }
                    } else {
                        // No image set, use placeholder
                        $imagePath = "https://placehold.co/400x300/12213a/c9a84c?text=" . urlencode($project['project_name']);
                    }

                    $categoryId = $hasCategoryIdColumn
                        ? ($project[CATEGORY_ID_COLUMN] ?? '')
                        : '';

                    $viewCount = $hasViewsColumn ? ((int)($project['views'] ?? 0)) : 0;
                ?>
                    <a href="property-listing/<?php echo htmlspecialchars($slug); ?>/" class="pp-card">
                        <div class="thumb" style="background-image:url('<?php echo htmlspecialchars($imagePath); ?>');">
                            <button type="button" class="pp-wishlist-btn" data-project-id="<?php echo (int) $project['id']; ?>" aria-label="Add to wishlist">
                                <i class="bi bi-heart"></i>
                            </button>
                            <?php if ($hasViewsColumn && $viewCount > 0): ?>
                                <span style="position:absolute; bottom:.75rem; left:.75rem; background:rgba(0,0,0,.7); color:#fff; padding:.25rem .65rem; border-radius:1rem; font-size:.7rem; z-index:2; display:flex; align-items:center; gap:.3rem;">
                                    <i class="bi bi-eye"></i> <?php echo number_format($viewCount); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="body">
                            <h3><?php echo htmlspecialchars($project['project_name']); ?></h3>
                            <div class="locality">
                                <i class="bi bi-geo-alt"></i>
                                <?php echo htmlspecialchars($project['address']); ?>
                            </div>
                            <div class="footer-row">
                                <div class="price"><?php echo htmlspecialchars($project['price']); ?></div>
                                <?php if (!empty($project['bedrooms'])): ?>
                                    <div class="beds"><i class="bi bi-door-open"></i> <?php echo (int) $project['bedrooms']; ?> BHK</div>
                                <?php endif; ?>
                            </div>
                            <?php if ($hasViewsColumn && $viewCount > 0): ?>
                                <div class="view-count">
                                    <i class="bi bi-eye"></i> <?php echo number_format($viewCount); ?> views
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
// ============ Slider prev/next ============
(function(){
    const track = document.getElementById('ppTrackSell');
    const prevBtn = document.getElementById('ppPrevSell');
    const nextBtn = document.getElementById('ppNextSell');
    if (!track || !prevBtn || !nextBtn) return;

    const scrollAmount = () => track.querySelector('.pp-card')?.offsetWidth + 20 || 300;

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

// ============ Wishlist (stored in the browser, no login required) ============
(function(){
    function getWishlist() {
        if (typeof window.getWishlistIds === 'function') return window.getWishlistIds();
        var key = typeof getTatitoStorageKey === 'function' ? getTatitoStorageKey('wishlist') : 'tatito_guest_wishlist';
        try { return JSON.parse(localStorage.getItem(key)) || []; }
        catch (e) { return []; }
    }

    function saveWishlist(list) {
        if (typeof window.saveWishlistIds === 'function') {
            window.saveWishlistIds(list);
            return;
        }
        var key = typeof getTatitoStorageKey === 'function' ? getTatitoStorageKey('wishlist') : 'tatito_guest_wishlist';
        var data = JSON.stringify(list || []);
        try {
            localStorage.setItem(key, data);
            document.dispatchEvent(new CustomEvent('wishlistUpdated', { detail: list }));
        } catch(e) {}
    }

    function syncWishlistUI() {
        const wishlist = getWishlist();
        const wishIds = wishlist.map(function(item) {
            return (item && item.id != null) ? Number(item.id) : Number(item);
        });

        document.querySelectorAll('.pp-wishlist-btn').forEach(function(btn){
            const id = Number(btn.dataset.projectId);
            if (!id) return;
            const icon = btn.querySelector('i');
            const isWish = wishIds.indexOf(id) !== -1;

            if (isWish) {
                btn.classList.add('active');
                if (icon) {
                    icon.classList.remove('bi-heart');
                    icon.classList.add('bi-heart-fill');
                    icon.style.color = '#e0245e';
                }
            } else {
                btn.classList.remove('active');
                if (icon) {
                    icon.classList.remove('bi-heart-fill');
                    icon.classList.add('bi-heart');
                    icon.style.color = '';
                }
            }
        });
    }

    document.querySelectorAll('.pp-wishlist-btn').forEach(function(btn){
        const id = Number(btn.dataset.projectId);
        if (!id) return;

        btn.addEventListener('click', function(e){
            e.preventDefault();
            e.stopPropagation();

            let list = getWishlist();
            const exists = list.some(function(item) {
                const itemId = (item && item.id != null) ? Number(item.id) : Number(item);
                return itemId === id;
            });

            if (!exists) {
                list.unshift({ id: id, type: 'property' });
            } else {
                list = list.filter(function(item) {
                    const itemId = (item && item.id != null) ? Number(item.id) : Number(item);
                    return itemId !== id;
                });
            }

            saveWishlist(list);
            syncWishlistUI();
        });
    });

    document.addEventListener('DOMContentLoaded', syncWishlistUI);
    document.addEventListener('wishlistUpdated', syncWishlistUI);
    syncWishlistUI();
})();

// ============ Dark Mode - Listen for theme changes ============
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