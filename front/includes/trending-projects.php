<?php
/* ==========================================================================
   BUY SECTION — dedicated single-vertical category section
   ------------------------------------------------------------------------
   Everything (helper functions, queries, HTML) is wrapped inside one
   function so this is safe to drop into WPCode / Code Snippets alongside
   your other page includes without "Cannot redeclare function ..." fatal
   errors or variable collisions from other snippets on the same page.

   Requires: $conn (mysqli connection) already available in scope.
   Usage:    echo render_buy_section($conn);
   ========================================================================== */

if (!function_exists('render_buy_section')) {

    function render_buy_section($conn) {

        if (!($conn instanceof mysqli)) {
            return '<div class="alert alert-warning mt-4 mb-0">Buy section: database connection not available.</div>';
        }

        $filterCategoryType = 'buy'; // hardcoded — this file is Buy-only

        // ---- local helpers (scoped to this function, safe from redeclare) ----
        $slugify = function ($text) {
            $text = strtolower(trim((string) $text));
            $text = preg_replace('/[^a-z0-9]+/', '-', $text);
            return trim($text, '-');
        };

        $getDescendantCatIds = function ($catId, array $childrenByParentId) use (&$getDescendantCatIds) {
            $ids = [(int) $catId];
            foreach (($childrenByParentId[$catId] ?? []) as $child) {
                $ids = array_merge($ids, $getDescendantCatIds($child['id'], $childrenByParentId));
            }
            return $ids;
        };

        // ===============================
        // Fetch the top-level "Buy" category
        // ===============================
        $topCategory = null;
        $topCatStmt = $conn->prepare(
            "SELECT id, name, slug, category_type, icon
             FROM property_categories
             WHERE status='Active' AND parent_id = 0 AND category_type = ?
             LIMIT 1"
        );
        if ($topCatStmt) {
            $topCatStmt->bind_param('s', $filterCategoryType);
            $topCatStmt->execute();
            $topCatRes = $topCatStmt->get_result();
            if ($topCatRes && $topCatRes->num_rows > 0) {
                $topCategory = $topCatRes->fetch_assoc();
            }
            $topCatStmt->close();
        }

        if (!$topCategory) {
            return '<div class="alert alert-warning mt-4 mb-0">No active "Buy" category found.</div>';
        }

        // ===============================
        // Fetch the full active category tree (all levels)
        // ===============================
        $allCatsRaw = [];
        $fullCatResult = $conn->query(
            "SELECT id, parent_id, name, slug, category_type
             FROM property_categories
             WHERE status='Active'
             ORDER BY category_order ASC, id ASC"
        );
        if ($fullCatResult) {
            while ($row = $fullCatResult->fetch_assoc()) {
                $allCatsRaw[] = $row;
            }
        }

        $childrenByParentId = [];
        foreach ($allCatsRaw as $cat) {
            $pid = $cat['parent_id'] ?? 0;
            $childrenByParentId[$pid][] = $cat;
        }

        // ===============================
        // Build subcategory tabs (immediate children of Buy)
        // ===============================
        $allBuyCatIds = $getDescendantCatIds($topCategory['id'], $childrenByParentId);

        $subcats = [];
        foreach (($childrenByParentId[$topCategory['id']] ?? []) as $sub) {
            $subcats[] = [
                'id'      => $sub['id'],
                'name'    => $sub['name'],
                'slug'    => $sub['slug'],
                'cat_ids' => $getDescendantCatIds($sub['id'], $childrenByParentId),
            ];
        }

        // Sub-sub categories (children of each subcategory)
        $subSubCategoriesByParentId = [];
        foreach ($subcats as $sub) {
            $kids = $childrenByParentId[$sub['id']] ?? [];
            if (!empty($kids)) {
                $subSubCategoriesByParentId[$sub['id']] = $kids;
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
        // Fetch active Buy projects only
        // ===============================
        $projects = [];
        if (!empty($allBuyCatIds)) {
            $placeholders = implode(',', array_fill(0, count($allBuyCatIds), '?'));
            $types = str_repeat('i', count($allBuyCatIds));

            $stmt2 = $conn->prepare(
                "SELECT tp.*
                 FROM trending_projects tp
                 WHERE tp.status='Active'
                   AND tp.category_id IN ($placeholders)
                 ORDER BY tp.project_order ASC
                 LIMIT 24"
            );
            if ($stmt2) {
                $stmt2->bind_param($types, ...$allBuyCatIds);
                $stmt2->execute();
                $result2 = $stmt2->get_result();
                if ($result2) {
                    while ($row = $result2->fetch_assoc()) {
                        $projects[] = $row;
                    }
                }
                $stmt2->close();
            }
        }

        // Count projects per subcategory
        $subcatCounts = [];
        foreach ($subcats as $sub) {
            $count = 0;
            foreach ($projects as $p) {
                if (in_array((int) ($p['category_id'] ?? 0), $sub['cat_ids'], true)) {
                    $count++;
                }
            }
            $subcatCounts[$sub['id']] = $count;
        }
        $totalProjects = count($projects);

        // ===============================
        // Render
        // ===============================
        ob_start();
        ?>
        <style>
          body.dark-mode .buy-tab {
            background: var(--dm-bg-tertiary, #1c2333);
            border-color: var(--dm-border, #30363d);
            color: var(--dm-text-secondary, #8b949e);
          }
          body.dark-mode .buy-tab:hover {
            border-color: var(--sy-gold, #f0b429);
            color: var(--sy-gold, #f0b429);
            background: rgba(240, 180, 41, 0.08);
          }
          body.dark-mode .buy-tab.active {
            background: var(--sy-gold, #f0b429);
            border-color: var(--sy-gold, #f0b429);
            color: var(--sy-navy-1, #12213a);
          }
          body.dark-mode .buy-tab-all {
            background: #2d3748;
            border-color: #2d3748;
            color: #e2e8f0;
          }
          body.dark-mode .buy-card {
            background: var(--dm-bg-secondary, #161b22);
            border-color: var(--dm-border, #30363d);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
          }
          body.dark-mode .buy-card:hover { box-shadow: 0 12px 28px rgba(0,0,0,0.5); }
          body.dark-mode .buy-card .body h3 { color: var(--dm-text-primary, #f0f6fc); }
          body.dark-mode .buy-card .locality { color: var(--dm-text-secondary, #8b949e); }
          body.dark-mode .buy-card .price { color: var(--sy-gold, #f0b429); }
          body.dark-mode .buy-card .beds { color: var(--dm-text-secondary, #8b949e); }
          body.dark-mode .buy-nav-btn {
            background: var(--dm-bg-secondary, #161b22);
            border-color: var(--dm-border, #30363d);
            color: var(--dm-text-primary, #f0f6fc);
          }
          body.dark-mode .buy-nav-btn:hover { border-color: var(--sy-gold, #f0b429); color: var(--sy-gold, #f0b429); }
          body.dark-mode .buy-nav-btn:disabled { opacity: 0.3; }
          body.dark-mode .buy-wishlist-btn {
            background: var(--dm-bg-secondary, #161b22);
            color: var(--dm-text-primary, #f0f6fc);
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
          }
          body.dark-mode .buy-wishlist-btn:hover { background: var(--dm-bg-tertiary, #1c2333); color: #e0245e; }
          body.dark-mode .buy-wishlist-btn.active { color: #e0245e; }
          body.dark-mode .buy-empty { color: var(--dm-text-secondary, #8b949e); }
          body.dark-mode .buy-section h2 { color: var(--dm-text-primary, #f0f6fc); }
          body.dark-mode .buy-alert {
            background: var(--dm-bg-tertiary, #1c2333);
            border-color: var(--dm-border, #30363d);
            color: var(--dm-text-secondary, #8b949e);
          }

          .buy-tabs-wrapper { display:flex; align-items:center; gap:.5rem; flex-wrap:wrap; margin-top:1rem; margin-bottom:.5rem; position:relative; }
          .buy-tabs-container { display:flex; align-items:center; gap:.5rem; flex-wrap:wrap; flex:1; }
          .buy-tab {
            flex:0 0 auto; display:inline-flex; align-items:center; white-space:nowrap;
            border-radius:2rem; padding:.45rem 1rem; font-size:.8rem; font-weight:600;
            text-decoration:none; border:1.5px solid #e7e9ee; background:#f8f9fc; color:#4a5568;
            cursor:pointer; transition:all .25s ease; letter-spacing:.02em; gap:.4rem;
          }
          .buy-tab::before {
            content:''; display:inline-block; width:6px; height:6px; border-radius:50%;
            background:#c9a84c; opacity:.3; transition:all .25s ease; flex-shrink:0;
          }
          .buy-tab:hover { border-color:#c9a84c; color:#c9a84c; background:#fffbf0; transform:translateY(-1px); box-shadow:0 4px 12px rgba(201,168,76,.15); }
          .buy-tab:hover::before { opacity:1; transform:scale(1.2); }
          .buy-tab.active { background:#c9a84c; border-color:#c9a84c; color:#fff; box-shadow:0 4px 14px rgba(201,168,76,.3); }
          .buy-tab.active::before { background:#fff; opacity:1; transform:scale(1.1); }
          .buy-tab-all { background:#12213a; border-color:#12213a; color:#fff; }
          .buy-tab-all::before { background:#fff; opacity:.5; }
          .buy-tab-all:hover { background:#1a2f4f; border-color:#1a2f4f; color:#fff; box-shadow:0 4px 12px rgba(18,33,58,.2); }
          .buy-tab-all.active { background:#c9a84c; border-color:#c9a84c; color:#fff; }
          .buy-tab-all.active::before { background:#fff; opacity:1; }

          .buy-count {
            display:inline-flex; align-items:center; justify-content:center; font-size:.6rem; font-weight:700;
            background:rgba(201,168,76,.15); color:#c9a84c; border-radius:50%; min-width:18px; height:18px; padding:0 5px;
          }
          .buy-tab.active .buy-count { background:rgba(255,255,255,.25); color:#fff; }
          .buy-tab-all .buy-count { background:rgba(255,255,255,.2); color:#fff; }
          body.dark-mode .buy-count { background:rgba(240,180,41,.2); color:var(--sy-gold,#f0b429); }
          body.dark-mode .buy-tab.active .buy-count { background:rgba(0,0,0,.15); color:var(--sy-navy-1,#12213a); }

          .buy-subsub-row { display:flex; flex-wrap:wrap; gap:.3rem; margin:.5rem 0 .25rem; padding-top:.5rem; border-top:1px dashed #e7e9ee; }
          .buy-subsub-tab {
            border:1px solid #e7e9ee; background:#f8f9fc; color:#8b9bb5; border-radius:1.5rem;
            padding:.25rem .8rem; font-size:.68rem; font-weight:600; cursor:pointer; transition:all .2s ease;
          }
          .buy-subsub-tab:hover { border-color:#c9a84c; color:#c9a84c; background:#fffbf0; }
          .buy-subsub-tab.active { background:#c9a84c; border-color:#c9a84c; color:#fff; }
          body.dark-mode .buy-subsub-row { border-top-color: var(--dm-border, #30363d); }
          body.dark-mode .buy-subsub-tab { background:var(--dm-bg-tertiary,#1c2333); border-color:var(--dm-border,#30363d); color:var(--dm-text-secondary,#8b949e); }
          body.dark-mode .buy-subsub-tab:hover { border-color:var(--sy-gold,#f0b429); color:var(--sy-gold,#f0b429); background:rgba(240,180,41,.08); }
          body.dark-mode .buy-subsub-tab.active { background:var(--sy-gold,#f0b429); border-color:var(--sy-gold,#f0b429); color:var(--sy-navy-1,#12213a); }

          .buy-slider-wrap { position:relative; margin-top:1.5rem; }
          .buy-grid { display:flex; gap:1.25rem; overflow-x:auto; scroll-snap-type:x mandatory; scroll-behavior:smooth; padding-bottom:.5rem; -ms-overflow-style:none; scrollbar-width:none; }
          .buy-grid::-webkit-scrollbar { display:none; }
          .buy-card {
            position:relative; flex:0 0 auto; width:280px; scroll-snap-align:start; border-radius:1rem; overflow:hidden;
            text-decoration:none; color:inherit; display:block; background:#fff; box-shadow:0 6px 20px rgba(18,33,58,.08);
            border:1px solid #e7e9ee; transition:box-shadow .2s ease, transform .2s ease;
          }
          .buy-card:hover { color:inherit; text-decoration:none; box-shadow:0 12px 28px rgba(18,33,58,.14); transform:translateY(-2px); }
          .buy-card.hidden { display:none; }
          .buy-nav-btn {
            width:36px; height:36px; border-radius:50%; border:1px solid #e7e9ee; background:#fff;
            display:inline-flex; align-items:center; justify-content:center; color:#12213a; transition:all .2s ease;
          }
          .buy-nav-btn:hover { border-color:#1f6feb; color:#1f6feb; }
          .buy-nav-btn:disabled { opacity:.4; cursor:not-allowed; }
          .buy-card .thumb { height:190px; background-size:cover; background-position:center; position:relative; }
          .buy-wishlist-btn {
            position:absolute; top:.75rem; right:.75rem; width:36px; height:36px; border-radius:50%; border:none;
            background:rgba(255,255,255,.9); display:flex; align-items:center; justify-content:center; color:#12213a;
            font-size:1.05rem; z-index:2; box-shadow:0 2px 8px rgba(18,33,58,.15); transition:all .2s ease; cursor:pointer;
          }
          .buy-wishlist-btn:hover { background:#fff; color:#e0245e; }
          .buy-wishlist-btn.active { color:#e0245e; }
          .buy-card .body { padding:.9rem 1rem 1.1rem; }
          .buy-card .body h3 { font-size:1.05rem; font-weight:700; margin-bottom:.25rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
          .buy-card .locality { color:#6b7686; font-size:.88rem; margin-bottom:.5rem; }
          .buy-card .footer-row { display:flex; justify-content:space-between; align-items:center; }
          .buy-card .price { color:#c9a84c; font-weight:700; font-size:1rem; }
          .buy-card .beds { color:#6b7686; font-size:.85rem; display:flex; align-items:center; gap:.3rem; }
          .buy-empty { display:none; padding:2rem; text-align:center; color:#6b7686; }

          @media (max-width: 576px) {
            .buy-card { width:240px; }
            .buy-card .thumb { height:160px; }
            .buy-tab { font-size:.7rem; padding:.35rem .75rem; }
            .buy-tab::before { width:4px; height:4px; }
          }
          @media (max-width: 400px) {
            .buy-tab { font-size:.65rem; padding:.25rem .6rem; }
          }
        </style>

        <section class="section pb-4 buy-section">
            <div class="container">

                <div class="d-flex justify-content-between align-items-end mb-4">
                    <div>
                        <h2 class="mt-1 mb-0" style="font-size:1.7rem;">
                            <?php echo htmlspecialchars($topCategory['name']); ?> Projects in India
                        </h2>
                    </div>
                </div>

                <?php if (!empty($subcats)): ?>
                    <div class="buy-tabs-wrapper">
                        <div class="buy-tabs-container" id="buyTabsContainer">
                            <button type="button" class="buy-tab buy-tab-all active" data-cat-ids="">
                                All <?php echo htmlspecialchars($topCategory['name']); ?>
                                <span class="buy-count"><?php echo (int) $totalProjects; ?></span>
                            </button>

                            <?php foreach ($subcats as $sub):
                                $count = $subcatCounts[$sub['id']] ?? 0;
                            ?>
                                <button type="button" class="buy-tab" data-cat-ids="<?php echo htmlspecialchars(implode(',', $sub['cat_ids'])); ?>" data-subcat-id="<?php echo (int) $sub['id']; ?>">
                                    <?php echo htmlspecialchars($sub['name']); ?>
                                    <span class="buy-count"><?php echo (int) $count; ?></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="buy-subsub-row" id="buySubsubRow" style="display:none;"></div>
                <?php endif; ?>

                <?php if (!empty($projects)): ?>
                    <div class="buy-slider-wrap">
                        <?php if (count($projects) >= 4): ?>
                            <div class="d-flex justify-content-end gap-2 mb-2">
                                <button type="button" class="buy-nav-btn" id="buyCsPrev" aria-label="Previous">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <button type="button" class="buy-nav-btn" id="buyCsNext" aria-label="Next">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                        <?php endif; ?>

                        <div class="buy-grid" id="buyProjectGrid">
                            <?php foreach ($projects as $project):
                                $slug = ($hasSlugColumn && !empty($project['slug']))
                                    ? $project['slug']
                                    : $slugify($project['project_name']);

                                $imagePath = '';
                                if (!empty($project['image'])) {
                                    if (strpos($project['image'], 'http') === 0 || strpos($project['image'], '/') === 0) {
                                        $imagePath = $project['image'];
                                    } else {
                                        $imagePath = "../admin/uploads/trending-projects/" . $project['image'];
                                    }
                                } else {
                                    $imagePath = "https://placehold.co/400x300/12213a/c9a84c?text=" . urlencode($project['project_name']);
                                }

                                $priceDisplay = !empty($project['price']) ? $project['price'] : 'Price on Request';
                            ?>
                                <a href="property-listing/<?php echo htmlspecialchars($slug); ?>/" class="buy-card" data-cat-id="<?php echo (int) ($project['category_id'] ?? 0); ?>">
                                    <div class="thumb" style="background-image:url('<?php echo htmlspecialchars($imagePath); ?>');">
                                        <button type="button" class="buy-wishlist-btn" data-project-id="<?php echo (int) $project['id']; ?>" aria-label="Add to wishlist">
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
                                            <?php if (!empty($project['bedrooms'])): ?>
                                                <div class="beds"><i class="bi bi-door-open"></i> <?php echo (int) $project['bedrooms']; ?> BHK</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="buy-empty" id="buyEmptyMsg">No projects found for this selection yet.</div>
                <?php else: ?>
                    <div class="alert alert-warning buy-alert mt-4 mb-0">
                        No projects found.
                    </div>
                <?php endif; ?>

            </div>
        </section>

        <script>
        (function(){
            const tabs = document.querySelectorAll('#buyTabsContainer .buy-tab');
            const cards = document.querySelectorAll('#buyProjectGrid .buy-card');
            const emptyMsg = document.getElementById('buyEmptyMsg');
            const subsubRow = document.getElementById('buySubsubRow');

            const subSubCategoriesByParentId = <?php echo json_encode($subSubCategoriesByParentId); ?>;

            function getDescendantIds(catId) {
                const children = subSubCategoriesByParentId[catId] || [];
                let ids = [catId];
                children.forEach(function(child) {
                    ids = ids.concat(getDescendantIds(child.id));
                });
                return ids;
            }

            function applyFilter(catIds, subSubIds) {
                const ids = catIds ? catIds.split(',').map(Number) : null;
                const subSub = subSubIds && subSubIds.length ? subSubIds : null;
                let visibleCount = 0;

                cards.forEach(function(card) {
                    const catId = Number(card.dataset.catId);
                    const matchesMain = !ids || ids.includes(catId);
                    const matchesSubSub = !subSub || subSub.includes(catId);
                    const matches = matchesMain && matchesSubSub;
                    card.classList.toggle('hidden', !matches);
                    if (matches) visibleCount++;
                });

                if (emptyMsg) emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
            }

            function renderSubSubTabs(subcatId, parentCatIds) {
                if (!subsubRow) return;
                const children = subSubCategoriesByParentId[subcatId] || [];

                if (children.length === 0) {
                    subsubRow.style.display = 'none';
                    subsubRow.innerHTML = '';
                    return;
                }

                let html = '<button type="button" class="buy-subsub-tab active" data-cat-ids="">All</button>';
                children.forEach(function(child) {
                    const descendantIds = getDescendantIds(child.id).join(',');
                    html += '<button type="button" class="buy-subsub-tab" data-cat-ids="' + descendantIds + '">' + child.name + '</button>';
                });
                subsubRow.innerHTML = html;
                subsubRow.style.display = 'flex';

                subsubRow.querySelectorAll('.buy-subsub-tab').forEach(function(tab) {
                    tab.addEventListener('click', function() {
                        subsubRow.querySelectorAll('.buy-subsub-tab').forEach(t => t.classList.remove('active'));
                        this.classList.add('active');
                        const subSubIds = this.dataset.catIds ? this.dataset.catIds.split(',').map(Number) : null;
                        applyFilter(parentCatIds, subSubIds);
                    });
                });
            }

            tabs.forEach(function(tab) {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    const catIds = this.dataset.catIds || '';
                    const subcatId = parseInt(this.dataset.subcatId) || 0;

                    applyFilter(catIds, null);

                    if (subcatId > 0) {
                        renderSubSubTabs(subcatId, catIds);
                    } else if (subsubRow) {
                        subsubRow.style.display = 'none';
                        subsubRow.innerHTML = '';
                    }
                });
            });
        })();

        (function(){
            const track = document.getElementById('buyProjectGrid');
            const prevBtn = document.getElementById('buyCsPrev');
            const nextBtn = document.getElementById('buyCsNext');
            if (!track || !prevBtn || !nextBtn) return;

            const scrollAmount = () => track.querySelector('.buy-card')?.offsetWidth + 20 || 300;

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

                document.querySelectorAll('.buy-wishlist-btn').forEach(function(btn){
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

            document.querySelectorAll('.buy-wishlist-btn').forEach(function(btn){
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
        </script>
        <?php
        return ob_get_clean();
    }
}

// Render it (assumes $conn is already defined on the including page)
echo render_buy_section($conn);