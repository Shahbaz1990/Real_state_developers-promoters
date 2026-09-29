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
    'title' => 'Explore Premium Properties',
    'subtitle' => 'Hand-picked'
];
$sectionResult = $conn->query("SELECT * FROM trending_projects_section LIMIT 1");
if ($sectionResult && $sectionResult->num_rows > 0) {
    $section = $sectionResult->fetch_assoc();
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
// Detect whether trending_projects has a column linking each project to a
// row in property_categories. This drives the "All property types" filter
// dropdown next to each city heading. If your linking column is named
// differently, change CATEGORY_ID_COLUMN below and nothing else needs to
// change.
// ===============================
if (!defined('CATEGORY_ID_COLUMN')) {
    define('CATEGORY_ID_COLUMN', 'category_id');
}
$hasCategoryIdColumn = false;
$catColCheck = $conn->query("SHOW COLUMNS FROM trending_projects LIKE '" . CATEGORY_ID_COLUMN . "'");
if ($catColCheck && $catColCheck->num_rows > 0) {
    $hasCategoryIdColumn = true;
}

// ===============================
// Fetch active property categories (for the filter dropdown options).
// ===============================
$propertyCategories = [];
$catResult = $conn->query("SELECT id, name, slug FROM property_categories WHERE status = 'Active' ORDER BY category_order ASC");
if ($catResult) {
    while ($row = $catResult->fetch_assoc()) {
        $propertyCategories[] = $row;
    }
}

// ===============================
// Fetch Explore Premium Properties, split per city.
// There's no dedicated city column on trending_projects yet, so — same
// approach used on the homepage's city tabs — this matches against the
// project's address text. Change the city names below (or add more
// columns) if you want different cities featured here.
// ===============================
if (!function_exists('fetchProjectsByCity')) {
    function fetchProjectsByCity($conn, array $cityTerms, $limit = 12) {
        $projects = [];

        $likeClauses = [];
        $params = [];
        $types = '';
        foreach ($cityTerms as $term) {
            $likeClauses[] = "address LIKE ?";
            $params[] = '%' . $term . '%';
            $types .= 's';
        }
        $where = implode(' OR ', $likeClauses);

        $sql = "SELECT * FROM trending_projects
                WHERE status='Active' AND ($where)
                ORDER BY project_order ASC LIMIT ?";
        $params[] = $limit;
        $types .= 'i';

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $projects[] = $row;
        }
        $stmt->close();

        return $projects;
    }
}

$ahmedabadProjects = fetchProjectsByCity($conn, ['Ahmedabad']);
$hyderabadProjects = fetchProjectsByCity($conn, ['Hyderabad']);

// (city key => [label, projects]) — makes the markup below a single loop
// instead of two near-duplicate blocks, and makes adding a third city later
// a one-line change.
$citySliders = [
    'ahmedabad' => ['label' => 'Ahmedabad', 'icon' => 'bi-building', 'projects' => $ahmedabadProjects],
    'hyderabad' => ['label' => 'Hyderabad', 'icon' => 'bi-building', 'projects' => $hyderabadProjects],
];
?>
<style>
  .pp-slider-wrap{ position:relative; }
  .pp-slider-track{
    display:flex;
    gap:1.1rem;
    overflow-x:auto;
    scroll-snap-type:x mandatory;
    scroll-behavior:smooth;
    padding-bottom:.5rem;
    -ms-overflow-style:none;
    scrollbar-width:none;
  }
  .pp-slider-track::-webkit-scrollbar{ display:none; }

  .pp-city-col + .pp-city-col{ margin-top:2.25rem; }
  @media (min-width: 992px){
    .pp-city-col + .pp-city-col{ margin-top:0; }
  }
  .pp-city-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:.75rem;
    min-height:34px;
    flex-wrap:wrap;
  }
  .pp-city-heading{
    font-size:1.15rem;
    font-weight:700;
    margin:0;
    line-height:1.2;
    white-space: nowrap;
    display:flex;
    align-items:center;
    gap:.5rem;
  }
  .pp-city-heading .bi{
    color:#1f6feb;
    font-size:1.2rem;
  }
  .pp-city-heading .city-badge{
    font-size:.7rem;
    font-weight:600;
    color:#fff;
    background:#1f6feb;
    padding:.15rem .65rem;
    border-radius:1rem;
    margin-left:.25rem;
  }
  .pp-city-header-right{
    display:flex;
    align-items:center;
    gap:.6rem;
  }

  /* ===== Property type filter dropdown ===== */
  .pp-filter{ position:relative; }
  .pp-filter-toggle{
    display:flex;
    align-items:center;
    gap:.4rem;
    white-space:nowrap;
    border-radius: 2rem;
    padding: .5rem 1.1rem;
    font-size: .85rem;
    font-weight: 600;
    text-decoration: none;
    border: 1px solid #e7e9ee;
    background: #f6f7fb;
    color: #12213a;
    cursor:pointer;
  }
  .pp-filter-toggle:hover{ border-color:#1f6feb; color:#1f6feb; }
  .pp-filter-toggle .bi-chevron-down{ font-size:.75rem; transition:transform .15s ease; }
  .pp-filter.open .pp-filter-toggle .bi-chevron-down{ transform:rotate(180deg); }

  .pp-filter-panel{
    position:fixed;
    width:230px;
    background:#fff;
    border:1px solid #e7e9ee;
    border-radius:.6rem;
    box-shadow:0 10px 28px rgba(18,33,58,.16);
    overflow:hidden;
    z-index:9999;
    display:none;
  }
  .pp-filter-panel.open{ display:block; }
  .pp-filter-panel-header{
    background:#1f6feb;
    color:#fff;
    font-weight:700;
    font-size:.88rem;
    padding:.65rem 1rem;
  }
  .pp-filter-panel-body{
    padding:.6rem .3rem;
    display:flex;
    flex-direction:column;
  }
  .pp-filter-option{
    display:flex;
    align-items:center;
    gap:.6rem;
    font-size:.9rem;
    color:#33415c;
    padding:.45rem .9rem;
    cursor:pointer;
    border-radius:.4rem;
  }
  .pp-filter-option:hover{ background:#f6f7fb; }
  .pp-filter-option input[type="checkbox"]{
    width:16px;
    height:16px;
    accent-color:#1f6feb;
    cursor:pointer;
  }

  .pp-card{
    flex:0 0 auto;
    width:260px;
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
    height:180px;
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
  }
  .pp-wishlist-btn:hover{ background:#fff; color:#e0245e; }
  .pp-wishlist-btn.active{ color:#e0245e; }

  .pp-card .body{ padding:.9rem 1rem 1.1rem; }
  .pp-card .body h3{
    font-size:1.02rem;
    font-weight:700;
    margin-bottom:.25rem;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }
  .pp-card .locality{
    color:#6b7686;
    font-size:.86rem;
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
    color:#1f6feb;
    font-weight:700;
    font-size:.98rem;
  }
  .pp-card .beds{
    color:#6b7686;
    font-size:.83rem;
    display:flex;
    align-items:center;
    gap:.3rem;
  }

  .pp-nav-btn{
    width:34px; height:34px;
    border-radius:50%;
    border:1px solid #e7e9ee;
    background:#fff;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    color:#12213a;
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
  }
  .section-title-wrapper h2{
    font-size:1.7rem;
    font-weight:700;
    margin:0;
  }
  .section-title-wrapper .title-underline{
    width:60px;
    height:3px;
    background:linear-gradient(90deg, #1f6feb, #6c9cf5);
    border-radius:2px;
    margin-top:.5rem;
  }
</style>

<section class="section pt-4 pp-slider-wrap">
    <div class="container">
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="section-title-wrapper">
                    <span class="section-subtitle">
                        <i class="bi bi-fire"></i> <?php echo htmlspecialchars($section['subtitle']); ?>
                    </span>
                    <h2 class="mt-1 mb-0">
                        Explore Premium Properties<?php //echo htmlspecialchars($section['title']); ?>
                    </h2>
                    <div class="title-underline"></div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($citySliders as $cityKey => $cityData): ?>
                <?php
                    $projects = $cityData['projects'];
                    $cityId   = ucfirst($cityKey);
                    $trackId  = 'ppTrack' . $cityId;
                    $prevId   = 'ppPrev' . $cityId;
                    $nextId   = 'ppNext' . $cityId;
                    $filterId = 'ppFilter' . $cityId;
                    $cityIcon = $cityData['icon'] ?? 'bi-building';
                    $projectCount = count($projects);
                ?>
                <div class="col-lg-6 pp-city-col mt-4">
                    <div class="card-box px-3 py-3" style="border-radius: 10px;background: #fff;box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
                    <div class="pp-city-header mb-3">
                        <h3 class="pp-city-heading">
                            <i class="bi <?php echo $cityIcon; ?>"></i>
                            <?php echo htmlspecialchars($cityData['label']); ?>
                            <span class="city-badge"><?php echo $projectCount; ?></span>
                        </h3>
                        <div class="pp-city-header-right">
                            <div class="pp-filter" id="<?php echo $filterId; ?>" data-track="<?php echo $trackId; ?>">
                                <button type="button" class="pp-filter-toggle" data-role="toggle">
                                    <span data-role="label">All property types</span>
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                                <div class="pp-filter-panel" data-role="panel">
                                    <div class="pp-filter-panel-header">
                                        <i class="bi bi-funnel"></i> Filter by type
                                    </div>
                                    <div class="pp-filter-panel-body">
                                        <?php if (!empty($propertyCategories)): ?>
                                            <?php foreach ($propertyCategories as $category): ?>
                                                <label class="pp-filter-option">
                                                    <input type="checkbox" value="<?php echo (int) $category['id']; ?>" data-name="<?php echo htmlspecialchars($category['name']); ?>">
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </label>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="pp-filter-option" style="cursor:default;">No categories found</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php if (count($projects) >= 3): ?>
                                <div class="d-flex gap-2">
                                    <button type="button" class="pp-nav-btn" id="<?php echo $prevId; ?>" aria-label="Previous">
                                        <i class="bi bi-chevron-left"></i>
                                    </button>
                                    <button type="button" class="pp-nav-btn" id="<?php echo $nextId; ?>" aria-label="Next">
                                        <i class="bi bi-chevron-right"></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($projects)): ?>
                        <div class="pp-slider-track" id="<?php echo $trackId; ?>">
                            <?php foreach ($projects as $project):
                                $slug = ($hasSlugColumn && !empty($project['slug']))
                                    ? $project['slug']
                                    : slugify($project['project_name']);

                                $imagePath = !empty($project['image'])
                                    ? "../backend/uploads/trending-projects/" . $project['image']
                                    : "assets/images/no-image.jpg";

                                $categoryId = $hasCategoryIdColumn
                                    ? ($project[CATEGORY_ID_COLUMN] ?? '')
                                    : '';
                            ?>
                                <a href="property-listing/<?php echo htmlspecialchars($slug); ?>/" class="pp-card" data-category-id="<?php echo htmlspecialchars((string) $categoryId); ?>">
                                    <div class="thumb" style="background-image:url('<?php echo htmlspecialchars($imagePath); ?>');">
                                        <button type="button" class="pp-wishlist-btn" data-project-id="<?php echo (int) $project['id']; ?>" aria-label="Add to wishlist">
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
                        <div class="pp-no-results" style="display:none;" data-role="no-results">
                            <i class="bi bi-exclamation-circle"></i>
                            No projects match the selected property type in <?php echo htmlspecialchars($cityData['label']); ?>.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-info-circle"></i>
                            No Premium Properties found in <?php echo htmlspecialchars($cityData['label']); ?>.
                        </div>
                    <?php endif; ?>
                </div>
                    </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
// ============ Slider prev/next — works for any number of sliders on the page ============
(function(){
    function initSlider(trackId, prevId, nextId){
        const track = document.getElementById(trackId);
        const prevBtn = document.getElementById(prevId);
        const nextBtn = document.getElementById(nextId);
        if (!track || !prevBtn || !nextBtn) return;

        const scrollAmount = () => track.querySelector('.pp-card')?.offsetWidth + 18 || 280;

        prevBtn.addEventListener('click', () => track.scrollBy({ left: -scrollAmount(), behavior: 'smooth' }));
        nextBtn.addEventListener('click', () => track.scrollBy({ left: scrollAmount(), behavior: 'smooth' }));

        function updateButtons() {
            prevBtn.disabled = track.scrollLeft <= 4;
            nextBtn.disabled = track.scrollLeft + track.clientWidth >= track.scrollWidth - 4;
        }
        track.addEventListener('scroll', updateButtons);
        window.addEventListener('resize', updateButtons);
        updateButtons();
    }

    <?php foreach ($citySliders as $cityKey => $cityData): ?>
    initSlider('ppTrack<?php echo ucfirst($cityKey); ?>', 'ppPrev<?php echo ucfirst($cityKey); ?>', 'ppNext<?php echo ucfirst($cityKey); ?>');
    <?php endforeach; ?>
})();

// ============ Property type filter dropdown ============
(function(){
    document.querySelectorAll('.pp-filter').forEach(function(filterEl){
        const toggleBtn  = filterEl.querySelector('[data-role="toggle"]');
        const labelSpan  = filterEl.querySelector('[data-role="label"]');
        const panel      = filterEl.querySelector('[data-role="panel"]');
        const checkboxes = filterEl.querySelectorAll('input[type="checkbox"]');
        const track      = document.getElementById(filterEl.dataset.track);
        const noResults  = track ? track.parentElement.querySelector('[data-role="no-results"]') : null;

        if (!toggleBtn || !panel || !track) return;

        function positionPanel(){
            const rect = toggleBtn.getBoundingClientRect();
            const panelWidth = panel.offsetWidth || 230;
            let left = rect.left;
            if (left + panelWidth > window.innerWidth - 8) {
                left = window.innerWidth - panelWidth - 8;
            }
            panel.style.top = (rect.bottom + 8) + 'px';
            panel.style.left = left + 'px';
        }

        function closeAll(){
            document.querySelectorAll('.pp-filter.open').forEach(function(openEl){
                openEl.classList.remove('open');
                const p = openEl.querySelector('[data-role="panel"]');
                if (p) p.classList.remove('open');
            });
        }

        toggleBtn.addEventListener('click', function(e){
            e.stopPropagation();
            const willOpen = !filterEl.classList.contains('open');
            closeAll();
            if (willOpen) {
                filterEl.classList.add('open');
                panel.classList.add('open');
                positionPanel();
            }
        });

        document.addEventListener('click', function(e){
            if (!filterEl.contains(e.target) && !panel.contains(e.target)) {
                filterEl.classList.remove('open');
                panel.classList.remove('open');
            }
        });

        window.addEventListener('scroll', function(){
            if (panel.classList.contains('open')) positionPanel();
        }, true);
        window.addEventListener('resize', function(){
            if (panel.classList.contains('open')) positionPanel();
        });

        function applyFilter(){
            const checkedBoxes = Array.from(checkboxes).filter(function(cb){ return cb.checked; });
            const selected = checkedBoxes.map(function(cb){ return cb.value; });
            const selectedNames = checkedBoxes.map(function(cb){ return cb.dataset.name || cb.value; });

            labelSpan.textContent = selectedNames.length === 0
                ? 'All property types'
                : (selectedNames.length <= 2 ? selectedNames.join(', ') : selectedNames.length + ' types selected');

            let visibleCount = 0;
            track.querySelectorAll('.pp-card').forEach(function(card){
                const categoryId = card.dataset.categoryId || '';
                const show = selected.length === 0 || selected.includes(categoryId);
                card.classList.toggle('pp-card-hidden', !show);
                if (show) visibleCount++;
            });

            if (noResults) {
                noResults.style.display = visibleCount === 0 ? '' : 'none';
            }
        }

        checkboxes.forEach(function(cb){
            cb.addEventListener('change', applyFilter);
        });
    });
})();

// ============ Wishlist (stored in the browser, no login required) ============
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

    document.querySelectorAll('.pp-wishlist-btn').forEach(function(btn){
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
</script>