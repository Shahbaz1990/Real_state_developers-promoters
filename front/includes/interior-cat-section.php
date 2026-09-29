<?php
if (!function_exists('slugify')) {
    function slugify($text) {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
    }
}

// ===============================
// Fetch all active property categories (for the category tabs)
// ===============================
$categories = [];
$sqlCat = "SELECT * FROM property_categories
           WHERE status='Active'
           ORDER BY category_order ASC, name ASC";
$resultCat = $conn->query($sqlCat);
if ($resultCat) {
    while ($row = $resultCat->fetch_assoc()) {
        $categories[] = $row;
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
// Detect whether trending_projects has a real `category_id` column
// ===============================
$hasCategoryColumn = false;
$colCheck2 = $conn->query("SHOW COLUMNS FROM trending_projects LIKE 'category_id'");
if ($colCheck2 && $colCheck2->num_rows > 0) {
    $hasCategoryColumn = true;
}

// ===============================
// Fetch active projects to display under the tabs
// ===============================
$projects = [];
$sql2 = "SELECT * FROM trending_projects WHERE status='Active' ORDER BY project_order ASC LIMIT 24";
$result2 = $conn->query($sql2);
if ($result2) {
    while ($row = $result2->fetch_assoc()) {
        // Resolve the category name from category_id, if the column/link exists
        $row['matched_category'] = '';
        if ($hasCategoryColumn && !empty($row['category_id'])) {
            foreach ($categories as $cat) {
                if ((int) $cat['id'] === (int) $row['category_id']) {
                    $row['matched_category'] = $cat['name'];
                    break;
                }
            }
        }

        $projects[] = $row;
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

  /* Category tabs — same pill style as the old city tabs, secondary look */
  .category-tabs-wrap{
    display:flex;
    align-items:center;
    gap:.5rem;
    overflow-x:auto;
    padding-bottom:.5rem;
    margin-top:.5rem;
    -ms-overflow-style:none;
    scrollbar-width:none;
  }
  .category-tabs-wrap::-webkit-scrollbar{ display:none; }

  .category-tab{
    flex:0 0 auto;
    display:inline-flex;
    align-items:center;
    white-space:nowrap;
    border-radius:2rem;
    padding:.5rem 1.1rem;
    font-size:.85rem;
    font-weight:600;
    text-decoration:none;
    border:1px solid #e7e9ee;
    background:#f6f7fb;
    color:#12213a;
    cursor:pointer;
    transition: all 0.2s ease;
  }
  .category-tab:hover{ border-color:#c9a84c; color:#c9a84c; }
  .category-tab.active{
    background:#c9a84c;
    border-color:#c9a84c;
    color:#fff;
  }
  .category-tab.active:hover{ color:#fff; }

  /* Project card slider — same visual language as the trending-projects slider */
  .cs-slider-wrap{ position:relative; margin-top:1.5rem; }
  .cs-grid{
    display:flex;
    gap:1.25rem;
    overflow-x:auto;
    scroll-snap-type:x mandatory;
    scroll-behavior:smooth;
    padding-bottom:.5rem;
    -ms-overflow-style:none;
    scrollbar-width:none;
  }
  .cs-grid::-webkit-scrollbar{ display:none; }
  .cs-card{
    position:relative;
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
  }
  .cs-card:hover{
    color:inherit;
    text-decoration:none;
    box-shadow:0 12px 28px rgba(18,33,58,.14);
    transform:translateY(-2px);
  }
  .cs-card.hidden{ display:none; }

  .cs-nav-btn{
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
  .cs-nav-btn:hover{ border-color:#1f6feb; color:#1f6feb; }
  .cs-nav-btn:disabled{ opacity:.4; cursor:not-allowed; }

  .cs-card .thumb{
    height:190px;
    background-size:cover;
    background-position:center;
    position:relative;
  }

  .cs-wishlist-btn{
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
  .cs-wishlist-btn:hover{ background:#fff; color:#e0245e; }
  .cs-wishlist-btn.active{ color:#e0245e; }

  .cs-card .body{ padding:.9rem 1rem 1.1rem; }
  .cs-card .body h3{
    font-size:1.05rem;
    font-weight:700;
    margin-bottom:.25rem;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }
  .cs-card .locality{
    color:#6b7686;
    font-size:.88rem;
    margin-bottom:.5rem;
  }
  .cs-card .footer-row{
    display:flex;
    justify-content:space-between;
    align-items:center;
  }
  .cs-card .price{
    color:#c9a84c;
    font-weight:700;
    font-size:1rem;
  }
  .cs-card .beds{
    color:#6b7686;
    font-size:.85rem;
    display:flex;
    align-items:center;
    gap:.3rem;
  }

  .cs-empty{ display:none; padding:2rem; text-align:center; color:#6b7686; }

  /* Mobile responsive adjustments */
  @media (max-width: 576px) {
    .cs-card {
      width: 240px;
    }
    .cs-card .thumb {
      height: 160px;
    }
    .category-tab {
      font-size: 0.75rem;
      padding: 0.4rem 0.8rem;
    }
  }
</style>

<section class="section pb-4">
    <div class="container">

        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="mt-1 mb-0" style="font-size:1.7rem;">Hot Selling Real Estate Projects in India</h2>
            </div>
        </div>

        <?php if (!empty($categories)): ?>
            <div class="category-tabs-wrap" id="categoryTabs">
                <button type="button" class="category-tab active" data-category="">All Categories</button>
                <?php foreach ($categories as $category): ?>
                    <button type="button" class="category-tab" data-category="<?php echo htmlspecialchars($category['name']); ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </button>
                <?php endforeach; ?>
            </div>
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
                    <?php foreach ($projects as $project):
                        $slug = ($hasSlugColumn && !empty($project['slug']))
                            ? $project['slug']
                            : slugify($project['project_name']);

                        $imagePath = !empty($project['image'])
                            ? "../backend/uploads/trending-projects/" . $project['image']
                            : "assets/images/no-image.jpg";
                    ?>
                        <a href="property-listing/<?php echo htmlspecialchars($slug); ?>/" class="cs-card" data-category="<?php echo htmlspecialchars($project['matched_category']); ?>">
                            <div class="thumb" style="background-image:url('<?php echo htmlspecialchars($imagePath); ?>');">
                                <button type="button" class="cs-wishlist-btn" data-project-id="<?php echo (int) $project['id']; ?>" aria-label="Add to wishlist">
                                    <i class="bi bi-heart"></i>
                                </button>
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
// ============ Category tab filtering ============
(function(){
    const categoryTabs = document.querySelectorAll('#categoryTabs .category-tab');
    const cards         = document.querySelectorAll('#cityProjectGrid .cs-card');
    const emptyMsg       = document.getElementById('cityEmptyMsg');

    categoryTabs.forEach(function(tab){
        tab.addEventListener('click', function(){
            categoryTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const selectedCategory = this.dataset.category;
            let visibleCount = 0;

            cards.forEach(function(card){
                const matches = selectedCategory === '' || card.dataset.category === selectedCategory;
                card.classList.toggle('hidden', !matches);
                if (matches) visibleCount++;
            });

            if (emptyMsg) emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
        });
    });
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

// ============ Wishlist (shared with trending-projects.php + header.php) ============
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

// ============ Dark Mode - Listen for theme changes ============
(function(){
    // Function to handle dark mode transitions
    function handleThemeChange() {
        const isDark = document.body.classList.contains('dark-mode');
        // Any additional theme-specific adjustments can be added here
    }

    // Listen for theme toggle events from the header
    document.addEventListener('themeChanged', function(e) {
        handleThemeChange();
    });

    // Initial check
    handleThemeChange();

    // Watch for class changes on body (for dark mode)
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