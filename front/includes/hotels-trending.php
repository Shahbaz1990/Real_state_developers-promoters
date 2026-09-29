<?php
/* ==========================================================================
   HOTELS & HOMESTAYS SECTION — dedicated single-vertical category section
   ------------------------------------------------------------------------
   Everything (helper functions, queries, HTML) is wrapped inside one
   function so this is safe to drop into WPCode / Code Snippets alongside
   your other page includes without "Cannot redeclare function ..." fatal
   errors or variable collisions from other snippets on the same page.

   Requires: $conn (mysqli connection) already available in scope.
   Usage:    echo render_hhs_section($conn);
   ========================================================================== */

if (!function_exists('render_hhs_section')) {

    function render_hhs_section($conn) {

        if (!($conn instanceof mysqli)) {
            return '<div class="alert alert-warning mt-4 mb-0">Hotels & Homestays section: database connection not available.</div>';
        }

        $filterCategoryType = 'hotels_homestays'; // hardcoded — this file is Hotels & Homestays-only

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
        // Fetch the top-level "Hotel & Homestay" category
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
            return '<div class="alert alert-warning mt-4 mb-0">No active "Hotel & Homestay" category found.</div>';
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
        // Build subcategory tabs (immediate children of Hotel & Homestay)
        // ===============================
        $allHhsCatIds = $getDescendantCatIds($topCategory['id'], $childrenByParentId);

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
        // Fetch active Hotel & Homestay projects only
        // ===============================
        $projects = [];
        if (!empty($allHhsCatIds)) {
            $placeholders = implode(',', array_fill(0, count($allHhsCatIds), '?'));
            $types = str_repeat('i', count($allHhsCatIds));

            $stmt2 = $conn->prepare(
                "SELECT tp.*
                 FROM trending_projects tp
                 WHERE tp.status='Active'
                   AND tp.category_id IN ($placeholders)
                 ORDER BY tp.project_order ASC
                 LIMIT 24"
            );
            if ($stmt2) {
                $stmt2->bind_param($types, ...$allHhsCatIds);
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
          body.dark-mode .hhs-tab {
            background: var(--dm-bg-tertiary, #1c2333);
            border-color: var(--dm-border, #30363d);
            color: var(--dm-text-secondary, #8b949e);
          }
          body.dark-mode .hhs-tab:hover {
            border-color: var(--sy-gold, #f0b429);
            color: var(--sy-gold, #f0b429);
            background: rgba(240, 180, 41, 0.08);
          }
          body.dark-mode .hhs-tab.active {
            background: var(--sy-gold, #f0b429);
            border-color: var(--sy-gold, #f0b429);
            color: var(--sy-navy-1, #12213a);
          }
          body.dark-mode .hhs-tab-all {
            background: #2d3748;
            border-color: #2d3748;
            color: #e2e8f0;
          }
          body.dark-mode .hhs-card {
            background: var(--dm-bg-secondary, #161b22);
            border-color: var(--dm-border, #30363d);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
          }
          body.dark-mode .hhs-card:hover { box-shadow: 0 12px 28px rgba(0,0,0,0.5); }
          body.dark-mode .hhs-card .body h3 { color: var(--dm-text-primary, #f0f6fc); }
          body.dark-mode .hhs-card .locality { color: var(--dm-text-secondary, #8b949e); }
          body.dark-mode .hhs-card .price { color: var(--sy-gold, #f0b429); }
          body.dark-mode .hhs-card .beds { color: var(--dm-text-secondary, #8b949e); }
          body.dark-mode .hhs-nav-btn {
            background: var(--dm-bg-secondary, #161b22);
            border-color: var(--dm-border, #30363d);
            color: var(--dm-text-primary, #f0f6fc);
          }
          body.dark-mode .hhs-nav-btn:hover { border-color: var(--sy-gold, #f0b429); color: var(--sy-gold, #f0b429); }
          body.dark-mode .hhs-nav-btn:disabled { opacity: 0.3; }
          body.dark-mode .hhs-wishlist-btn {
            background: var(--dm-bg-secondary, #161b22);
            color: var(--dm-text-primary, #f0f6fc);
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
          }
          body.dark-mode .hhs-wishlist-btn:hover { background: var(--dm-bg-tertiary, #1c2333); color: #e0245e; }
          body.dark-mode .hhs-wishlist-btn.active { color: #e0245e; }
          body.dark-mode .hhs-empty { color: var(--dm-text-secondary, #8b949e); }
          body.dark-mode .hhs-section h2 { color: var(--dm-text-primary, #f0f6fc); }
          body.dark-mode .hhs-alert {
            background: var(--dm-bg-tertiary, #1c2333);
            border-color: var(--dm-border, #30363d);
            color: var(--dm-text-secondary, #8b949e);
          }

          .hhs-tabs-wrapper { display:flex; align-items:center; gap:.5rem; flex-wrap:wrap; margin-top:1rem; margin-bottom:.5rem; position:relative; }
          .hhs-tabs-container { display:flex; align-items:center; gap:.5rem; flex-wrap:wrap; flex:1; }
          .hhs-tab {
            flex:0 0 auto; display:inline-flex; align-items:center; white-space:nowrap;
            border-radius:2rem; padding:.45rem 1rem; font-size:.8rem; font-weight:600;
            text-decoration:none; border:1.5px solid #e7e9ee; background:#f8f9fc; color:#4a5568;
            cursor:pointer; transition:all .25s ease; letter-spacing:.02em; gap:.4rem;
          }
          .hhs-tab::before {
            content:''; display:inline-block; width:6px; height:6px; border-radius:50%;
            background:#c9a84c; opacity:.3; transition:all .25s ease; flex-shrink:0;
          }
          .hhs-tab:hover { border-color:#c9a84c; color:#c9a84c; background:#fffbf0; transform:translateY(-1px); box-shadow:0 4px 12px rgba(201,168,76,.15); }
          .hhs-tab:hover::before { opacity:1; transform:scale(1.2); }
          .hhs-tab.active { background:#c9a84c; border-color:#c9a84c; color:#fff; box-shadow:0 4px 14px rgba(201,168,76,.3); }
          .hhs-tab.active::before { background:#fff; opacity:1; transform:scale(1.1); }
          .hhs-tab-all { background:#12213a; border-color:#12213a; color:#fff; }
          .hhs-tab-all::before { background:#fff; opacity:.5; }
          .hhs-tab-all:hover { background:#1a2f4f; border-color:#1a2f4f; color:#fff; box-shadow:0 4px 12px rgba(18,33,58,.2); }
          .hhs-tab-all.active { background:#c9a84c; border-color:#c9a84c; color:#fff; }
          .hhs-tab-all.active::before { background:#fff; opacity:1; }

          .hhs-count {
            display:inline-flex; align-items:center; justify-content:center; font-size:.6rem; font-weight:700;
            background:rgba(201,168,76,.15); color:#c9a84c; border-radius:50%; min-width:18px; height:18px; padding:0 5px;
          }
          .hhs-tab.active .hhs-count { background:rgba(255,255,255,.25); color:#fff; }
          .hhs-tab-all .hhs-count { background:rgba(255,255,255,.2); color:#fff; }
          body.dark-mode .hhs-count { background:rgba(240,180,41,.2); color:var(--sy-gold,#f0b429); }
          body.dark-mode .hhs-tab.active .hhs-count { background:rgba(0,0,0,.15); color:var(--sy-navy-1,#12213a); }

          .hhs-subsub-row { display:flex; flex-wrap:wrap; gap:.3rem; margin:.5rem 0 .25rem; padding-top:.5rem; border-top:1px dashed #e7e9ee; }
          .hhs-subsub-tab {
            border:1px solid #e7e9ee; background:#f8f9fc; color:#8b9bb5; border-radius:1.5rem;
            padding:.25rem .8rem; font-size:.68rem; font-weight:600; cursor:pointer; transition:all .2s ease;
          }
          .hhs-subsub-tab:hover { border-color:#c9a84c; color:#c9a84c; background:#fffbf0; }
          .hhs-subsub-tab.active { background:#c9a84c; border-color:#c9a84c; color:#fff; }
          body.dark-mode .hhs-subsub-row { border-top-color: var(--dm-border, #30363d); }
          body.dark-mode .hhs-subsub-tab { background:var(--dm-bg-tertiary,#1c2333); border-color:var(--dm-border,#30363d); color:var(--dm-text-secondary,#8b949e); }
          body.dark-mode .hhs-subsub-tab:hover { border-color:var(--sy-gold,#f0b429); color:var(--sy-gold,#f0b429); background:rgba(240,180,41,.08); }
          body.dark-mode .hhs-subsub-tab.active { background:var(--sy-gold,#f0b429); border-color:var(--sy-gold,#f0b429); color:var(--sy-navy-1,#12213a); }

          .hhs-slider-wrap { position:relative; margin-top:1.5rem; }
          .hhs-grid { display:flex; gap:1.25rem; overflow-x:auto; scroll-snap-type:x mandatory; scroll-behavior:smooth; padding-bottom:.5rem; -ms-overflow-style:none; scrollbar-width:none; }
          .hhs-grid::-webkit-scrollbar { display:none; }
          .hhs-card {
            position:relative; flex:0 0 auto; width:280px; scroll-snap-align:start; border-radius:1rem; overflow:hidden;
            text-decoration:none; color:inherit; display:block; background:#fff; box-shadow:0 6px 20px rgba(18,33,58,.08);
            border:1px solid #e7e9ee; transition:box-shadow .2s ease, transform .2s ease;
          }
          .hhs-card:hover { color:inherit; text-decoration:none; box-shadow:0 12px 28px rgba(18,33,58,.14); transform:translateY(-2px); }
          .hhs-card.hidden { display:none; }
          .hhs-nav-btn {
            width:36px; height:36px; border-radius:50%; border:1px solid #e7e9ee; background:#fff;
            display:inline-flex; align-items:center; justify-content:center; color:#12213a; transition:all .2s ease;
          }
          .hhs-nav-btn:hover { border-color:#1f6feb; color:#1f6feb; }
          .hhs-nav-btn:disabled { opacity:.4; cursor:not-allowed; }
          .hhs-card .thumb { height:190px; background-size:cover; background-position:center; position:relative; }
          .hhs-wishlist-btn {
            position:absolute; top:.75rem; right:.75rem; width:36px; height:36px; border-radius:50%; border:none;
            background:rgba(255,255,255,.9); display:flex; align-items:center; justify-content:center; color:#12213a;
            font-size:1.05rem; z-index:2; box-shadow:0 2px 8px rgba(18,33,58,.15); transition:all .2s ease; cursor:pointer;
          }
          .hhs-wishlist-btn:hover { background:#fff; color:#e0245e; }
          .hhs-wishlist-btn.active { color:#e0245e; }
          .hhs-card .body { padding:.9rem 1rem 1.1rem; }
          .hhs-card .body h3 { font-size:1.05rem; font-weight:700; margin-bottom:.25rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
          .hhs-card .locality { color:#6b7686; font-size:.88rem; margin-bottom:.5rem; }
          .hhs-card .footer-row { display:flex; justify-content:space-between; align-items:center; }
          .hhs-card .price { color:#c9a84c; font-weight:700; font-size:1rem; }
          .hhs-card .beds { color:#6b7686; font-size:.85rem; display:flex; align-items:center; gap:.3rem; }
          .hhs-empty { display:none; padding:2rem; text-align:center; color:#6b7686; }

          @media (max-width: 576px) {
            .hhs-card { width:240px; }
            .hhs-card .thumb { height:160px; }
            .hhs-tab { font-size:.7rem; padding:.35rem .75rem; }
            .hhs-tab::before { width:4px; height:4px; }
          }
          @media (max-width: 400px) {
            .hhs-tab { font-size:.65rem; padding:.25rem .6rem; }
          }
        </style>

        <section class="section pb-4 hhs-section">
            <div class="container">

                <div class="d-flex justify-content-between align-items-end mb-4">
                    <div>
                        <h2 class="mt-1 mb-0" style="font-size:1.7rem;">
                            <?php echo htmlspecialchars($topCategory['name']); ?> Projects in India
                        </h2>
                    </div>
                </div>

                <?php if (!empty($subcats)): ?>
                    <div class="hhs-tabs-wrapper">
                        <div class="hhs-tabs-container" id="hhsTabsContainer">
                            <button type="button" class="hhs-tab hhs-tab-all active" data-cat-ids="">
                                All <?php echo htmlspecialchars($topCategory['name']); ?>
                                <span class="hhs-count"><?php echo (int) $totalProjects; ?></span>
                            </button>

                            <?php foreach ($subcats as $sub):
                                $count = $subcatCounts[$sub['id']] ?? 0;
                            ?>
                                <button type="button" class="hhs-tab" data-cat-ids="<?php echo htmlspecialchars(implode(',', $sub['cat_ids'])); ?>" data-subcat-id="<?php echo (int) $sub['id']; ?>">
                                    <?php echo htmlspecialchars($sub['name']); ?>
                                    <span class="hhs-count"><?php echo (int) $count; ?></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="hhs-subsub-row" id="hhsSubsubRow" style="display:none;"></div>
                <?php endif; ?>

                <?php if (!empty($projects)): ?>
                    <div class="hhs-slider-wrap">
                        <?php if (count($projects) >= 4): ?>
                            <div class="d-flex justify-content-end gap-2 mb-2">
                                <button type="button" class="hhs-nav-btn" id="hhsCsPrev" aria-label="Previous">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <button type="button" class="hhs-nav-btn" id="hhsCsNext" aria-label="Next">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                        <?php endif; ?>

                        <div class="hhs-grid" id="hhsProjectGrid">
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
                                <a href="property-listing/<?php echo htmlspecialchars($slug); ?>/" class="hhs-card" data-cat-id="<?php echo (int) ($project['category_id'] ?? 0); ?>">
                                    <div class="thumb" style="background-image:url('<?php echo htmlspecialchars($imagePath); ?>');">
                                        <button type="button" class="hhs-wishlist-btn" data-project-id="<?php echo (int) $project['id']; ?>" aria-label="Add to wishlist">
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
                                            <div class="beds"><i class="bi bi-hotel"></i> Hotel</div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="hhs-empty" id="hhsEmptyMsg">No projects found for this selection yet.</div>
                <?php else: ?>
                    <div class="alert alert-warning hhs-alert mt-4 mb-0">
                        No projects found.
                    </div>
                <?php endif; ?>

            </div>
        </section>

        <script>
        (function(){
            const tabs = document.querySelectorAll('#hhsTabsContainer .hhs-tab');
            const cards = document.querySelectorAll('#hhsProjectGrid .hhs-card');
            const emptyMsg = document.getElementById('hhsEmptyMsg');
            const subsubRow = document.getElementById('hhsSubsubRow');

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

                let html = '<button type="button" class="hhs-subsub-tab active" data-cat-ids="">All</button>';
                children.forEach(function(child) {
                    const descendantIds = getDescendantIds(child.id).join(',');
                    html += '<button type="button" class="hhs-subsub-tab" data-cat-ids="' + descendantIds + '">' + child.name + '</button>';
                });
                subsubRow.innerHTML = html;
                subsubRow.style.display = 'flex';

                subsubRow.querySelectorAll('.hhs-subsub-tab').forEach(function(tab) {
                    tab.addEventListener('click', function() {
                        subsubRow.querySelectorAll('.hhs-subsub-tab').forEach(t => t.classList.remove('active'));
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
            const track = document.getElementById('hhsProjectGrid');
            const prevBtn = document.getElementById('hhsCsPrev');
            const nextBtn = document.getElementById('hhsCsNext');
            if (!track || !prevBtn || !nextBtn) return;

            const scrollAmount = () => track.querySelector('.hhs-card')?.offsetWidth + 20 || 300;

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

                document.querySelectorAll('.hhs-wishlist-btn').forEach(function(btn){
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

            document.querySelectorAll('.hhs-wishlist-btn').forEach(function(btn){
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
                        list.unshift({ id: id, type: 'hotel' });
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
echo render_hhs_section($conn);