<?php
/* ==========================================================================
   AGENTS SECTION — dedicated section for the agents table
   ------------------------------------------------------------------------
   Not category-tree based like Buy/Rent/etc. — agents are flat, tabbed
   by their `specialization` column. Wrapped in one function so it's safe
   to sit on a page next to the other vertical sections without name or
   ID collisions.

   Requires: $conn (mysqli connection) already available in scope.
   Usage:    echo render_agt_section($conn);
   ========================================================================== */

if (!function_exists('render_agt_section')) {

    function render_agt_section($conn) {

        if (!($conn instanceof mysqli)) {
            return '<div class="alert alert-warning mt-4 mb-0">Agents section: database connection not available.</div>';
        }

        // Human-friendly labels for each specialization value stored in the DB.
        // Add to this list if you introduce new specializations later.
        $specializationLabels = [
            'residential'  => 'Residential',
            'commercial'   => 'Commercial',
            'rental'       => 'Rental',
            'luxury'       => 'Luxury',
            'agricultural' => 'Agricultural',
        ];

        $slugify = function ($text) {
            $text = strtolower(trim((string) $text));
            $text = preg_replace('/[^a-z0-9]+/', '-', $text);
            return trim($text, '-');
        };

        // ===============================
        // Fetch active agents
        // ===============================
        $agents = [];
        $result = $conn->query(
            "SELECT * FROM agents
             WHERE status='Active'
             ORDER BY agent_order ASC, id ASC
             LIMIT 24"
        );
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $agents[] = $row;
            }
        }

        // ===============================
        // Build tabs from specializations actually present in the data
        // ===============================
        $specializationCounts = [];
        foreach ($agents as $a) {
            $spec = $a['specialization'] ?? '';
            if ($spec === '') continue;
            $specializationCounts[$spec] = ($specializationCounts[$spec] ?? 0) + 1;
        }
        $totalAgents = count($agents);

        // ===============================
        // Render
        // ===============================
        ob_start();
        ?>
        <style>
          body.dark-mode .agt-tab {
            background: var(--dm-bg-tertiary, #1c2333);
            border-color: var(--dm-border, #30363d);
            color: var(--dm-text-secondary, #8b949e);
          }
          body.dark-mode .agt-tab:hover {
            border-color: var(--sy-gold, #f0b429);
            color: var(--sy-gold, #f0b429);
            background: rgba(240, 180, 41, 0.08);
          }
          body.dark-mode .agt-tab.active {
            background: var(--sy-gold, #f0b429);
            border-color: var(--sy-gold, #f0b429);
            color: var(--sy-navy-1, #12213a);
          }
          body.dark-mode .agt-tab-all {
            background: #2d3748;
            border-color: #2d3748;
            color: #e2e8f0;
          }
          body.dark-mode .agt-card {
            background: var(--dm-bg-secondary, #161b22);
            border-color: var(--dm-border, #30363d);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
          }
          body.dark-mode .agt-card:hover { box-shadow: 0 12px 28px rgba(0,0,0,0.5); }
          body.dark-mode .agt-card .name { color: var(--dm-text-primary, #f0f6fc); }
          body.dark-mode .agt-card .locality { color: var(--dm-text-secondary, #8b949e); }
          body.dark-mode .agt-card .stat { color: var(--dm-text-secondary, #8b949e); }
          body.dark-mode .agt-nav-btn {
            background: var(--dm-bg-secondary, #161b22);
            border-color: var(--dm-border, #30363d);
            color: var(--dm-text-primary, #f0f6fc);
          }
          body.dark-mode .agt-nav-btn:hover { border-color: var(--sy-gold, #f0b429); color: var(--sy-gold, #f0b429); }
          body.dark-mode .agt-nav-btn:disabled { opacity: 0.3; }
          body.dark-mode .agt-empty { color: var(--dm-text-secondary, #8b949e); }
          body.dark-mode .agt-section h2 { color: var(--dm-text-primary, #f0f6fc); }
          body.dark-mode .agt-alert {
            background: var(--dm-bg-tertiary, #1c2333);
            border-color: var(--dm-border, #30363d);
            color: var(--dm-text-secondary, #8b949e);
          }
          body.dark-mode .agt-contact-btn {
            border-color: var(--sy-gold, #f0b429);
            color: var(--sy-gold, #f0b429);
          }
          body.dark-mode .agt-contact-btn:hover {
            background: var(--sy-gold, #f0b429);
            color: var(--sy-navy-1, #12213a);
          }

          .agt-tabs-wrapper { display:flex; align-items:center; gap:.5rem; flex-wrap:wrap; margin-top:1rem; margin-bottom:.5rem; }
          .agt-tab {
            flex:0 0 auto; display:inline-flex; align-items:center; white-space:nowrap;
            border-radius:2rem; padding:.45rem 1rem; font-size:.8rem; font-weight:600;
            border:1.5px solid #e7e9ee; background:#f8f9fc; color:#4a5568;
            cursor:pointer; transition:all .25s ease; letter-spacing:.02em; gap:.4rem;
          }
          .agt-tab::before {
            content:''; display:inline-block; width:6px; height:6px; border-radius:50%;
            background:#c9a84c; opacity:.3; transition:all .25s ease; flex-shrink:0;
          }
          .agt-tab:hover { border-color:#c9a84c; color:#c9a84c; background:#fffbf0; transform:translateY(-1px); box-shadow:0 4px 12px rgba(201,168,76,.15); }
          .agt-tab:hover::before { opacity:1; transform:scale(1.2); }
          .agt-tab.active { background:#c9a84c; border-color:#c9a84c; color:#fff; box-shadow:0 4px 14px rgba(201,168,76,.3); }
          .agt-tab.active::before { background:#fff; opacity:1; transform:scale(1.1); }
          .agt-tab-all { background:#12213a; border-color:#12213a; color:#fff; }
          .agt-tab-all::before { background:#fff; opacity:.5; }
          .agt-tab-all:hover { background:#1a2f4f; border-color:#1a2f4f; color:#fff; box-shadow:0 4px 12px rgba(18,33,58,.2); }
          .agt-tab-all.active { background:#c9a84c; border-color:#c9a84c; color:#fff; }
          .agt-tab-all.active::before { background:#fff; opacity:1; }

          .agt-count {
            display:inline-flex; align-items:center; justify-content:center; font-size:.6rem; font-weight:700;
            background:rgba(201,168,76,.15); color:#c9a84c; border-radius:50%; min-width:18px; height:18px; padding:0 5px;
          }
          .agt-tab.active .agt-count { background:rgba(255,255,255,.25); color:#fff; }
          .agt-tab-all .agt-count { background:rgba(255,255,255,.2); color:#fff; }
          body.dark-mode .agt-count { background:rgba(240,180,41,.2); color:var(--sy-gold,#f0b429); }
          body.dark-mode .agt-tab.active .agt-count { background:rgba(0,0,0,.15); color:var(--sy-navy-1,#12213a); }

          .agt-slider-wrap { position:relative; margin-top:1.5rem; }
          .agt-grid { display:flex; gap:1.25rem; overflow-x:auto; scroll-snap-type:x mandatory; scroll-behavior:smooth; padding-bottom:.5rem; -ms-overflow-style:none; scrollbar-width:none; }
          .agt-grid::-webkit-scrollbar { display:none; }
          .agt-card {
            flex:0 0 auto; width:250px; scroll-snap-align:start; border-radius:1rem; overflow:hidden;
            background:#fff; box-shadow:0 6px 20px rgba(18,33,58,.08); border:1px solid #e7e9ee;
            transition:box-shadow .2s ease, transform .2s ease; text-align:center; padding:1.5rem 1rem 1.25rem;
          }
          .agt-card:hover { box-shadow:0 12px 28px rgba(18,33,58,.14); transform:translateY(-2px); }
          .agt-card.hidden { display:none; }
          .agt-avatar {
            width:76px; height:76px; border-radius:50%; margin:0 auto .75rem; background-size:cover;
            background-position:center; background-color:#12213a; border:3px solid #f0b429;
            display:flex; align-items:center; justify-content:center; color:#f0b429; font-weight:700; font-size:1.4rem;
          }
          .agt-card .name { font-size:1rem; font-weight:700; margin-bottom:.15rem; }
          .agt-card .spec-badge {
            display:inline-block; font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em;
            background:rgba(201,168,76,.15); color:#c9a84c; border-radius:1rem; padding:.15rem .6rem; margin-bottom:.5rem;
          }
          .agt-card .locality { color:#6b7686; font-size:.82rem; margin-bottom:.6rem; }
          .agt-card .stats-row { display:flex; justify-content:center; gap:1rem; margin-bottom:.85rem; }
          .agt-card .stat { font-size:.78rem; color:#6b7686; display:flex; align-items:center; gap:.25rem; }
          .agt-card .stat .rating-val { color:#c9a84c; font-weight:700; }
          .agt-contact-btn {
            display:inline-flex; align-items:center; gap:.35rem; border:1.5px solid #12213a; color:#12213a;
            background:transparent; border-radius:1.5rem; padding:.4rem 1rem; font-size:.78rem; font-weight:600;
            text-decoration:none; transition:all .2s ease;
          }
          .agt-contact-btn:hover { background:#12213a; color:#fff; }

          .agt-nav-btn {
            width:36px; height:36px; border-radius:50%; border:1px solid #e7e9ee; background:#fff;
            display:inline-flex; align-items:center; justify-content:center; color:#12213a; transition:all .2s ease;
          }
          .agt-nav-btn:hover { border-color:#1f6feb; color:#1f6feb; }
          .agt-nav-btn:disabled { opacity:.4; cursor:not-allowed; }
          .agt-empty { display:none; padding:2rem; text-align:center; color:#6b7686; }

          @media (max-width: 576px) {
            .agt-card { width:210px; padding:1.25rem .75rem 1rem; }
            .agt-tab { font-size:.7rem; padding:.35rem .75rem; }
          }
        </style>

        <section class="section pb-4 agt-section">
            <div class="container">

                <div class="d-flex justify-content-between align-items-end mb-4">
                    <div>
                        <h2 class="mt-1 mb-0" style="font-size:1.7rem;">Meet Our Trusted Agents</h2>
                    </div>
                </div>

                <?php if (!empty($specializationCounts)): ?>
                    <div class="agt-tabs-wrapper" id="agtTabsContainer">
                        <button type="button" class="agt-tab agt-tab-all active" data-spec="">
                            All Agents
                            <span class="agt-count"><?php echo (int) $totalAgents; ?></span>
                        </button>

                        <?php foreach ($specializationCounts as $spec => $count):
                            $label = $specializationLabels[$spec] ?? ucfirst(str_replace('_', ' ', $spec));
                        ?>
                            <button type="button" class="agt-tab" data-spec="<?php echo htmlspecialchars($spec); ?>">
                                <?php echo htmlspecialchars($label); ?>
                                <span class="agt-count"><?php echo (int) $count; ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($agents)): ?>
                    <div class="agt-slider-wrap">
                        <?php if (count($agents) >= 4): ?>
                            <div class="d-flex justify-content-end gap-2 mb-2">
                                <button type="button" class="agt-nav-btn" id="agtCsPrev" aria-label="Previous">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <button type="button" class="agt-nav-btn" id="agtCsNext" aria-label="Next">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                        <?php endif; ?>

                        <div class="agt-grid" id="agtGrid">
                            <?php foreach ($agents as $agent):
                                $slug = !empty($agent['slug']) ? $agent['slug'] : $slugify($agent['name']);
                                $spec = $agent['specialization'] ?? '';
                                $label = $specializationLabels[$spec] ?? ucfirst(str_replace('_', ' ', $spec));

                                $initials = '';
                                foreach (explode(' ', trim($agent['name'])) as $part) {
                                    if ($part !== '') $initials .= strtoupper($part[0]);
                                }
                                $initials = substr($initials, 0, 2);

                                $avatarStyle = !empty($agent['photo'])
                                    ? "background-image:url('../admin/uploads/agents/" . htmlspecialchars($agent['photo']) . "');"
                                    : '';

                                $rating = !empty($agent['rating']) ? number_format((float) $agent['rating'], 1) : '—';
                                $listings = (int) ($agent['listings_count'] ?? 0);
                                $phone = $agent['phone'] ?? '';
                            ?>
                                <div class="agt-card" data-spec="<?php echo htmlspecialchars($spec); ?>">
                                    <div class="agt-avatar" style="<?php echo $avatarStyle; ?>">
                                        <?php if (empty($agent['photo'])): ?>
                                            <?php echo htmlspecialchars($initials); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="name"><?php echo htmlspecialchars($agent['name']); ?></div>
                                    <span class="spec-badge"><?php echo htmlspecialchars($label); ?></span>
                                    <div class="locality">
                                        <i class="bi bi-geo-alt"></i>
                                        <?php echo htmlspecialchars(trim(($agent['locality'] ?? '') . ', ' . ($agent['city'] ?? ''), ', ')); ?>
                                    </div>
                                    <div class="stats-row">
                                        <div class="stat"><i class="bi bi-star-fill" style="color:#c9a84c;"></i> <span class="rating-val"><?php echo htmlspecialchars($rating); ?></span></div>
                                        <div class="stat"><i class="bi bi-houses"></i> <?php echo $listings; ?> listings</div>
                                    </div>
                                    <?php if (!empty($phone)): ?>
                                        <a href="tel:<?php echo htmlspecialchars(preg_replace('/\s+/', '', $phone)); ?>" class="agt-contact-btn">
                                            <i class="bi bi-telephone"></i> Contact
                                        </a>
                                    <?php else: ?>
                                        <a href="agent-profile/<?php echo htmlspecialchars($slug); ?>/" class="agt-contact-btn">
                                            <i class="bi bi-person"></i> View Profile
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="agt-empty" id="agtEmptyMsg">No agents found for this selection yet.</div>
                <?php else: ?>
                    <div class="alert alert-warning agt-alert mt-4 mb-0">
                        No agents found.
                    </div>
                <?php endif; ?>

            </div>
        </section>

        <script>
        (function(){
            const tabs = document.querySelectorAll('#agtTabsContainer .agt-tab');
            const cards = document.querySelectorAll('#agtGrid .agt-card');
            const emptyMsg = document.getElementById('agtEmptyMsg');

            function applyFilter(spec) {
                let visibleCount = 0;
                cards.forEach(function(card) {
                    const matches = spec === '' || card.dataset.spec === spec;
                    card.classList.toggle('hidden', !matches);
                    if (matches) visibleCount++;
                });
                if (emptyMsg) emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
            }

            tabs.forEach(function(tab) {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    applyFilter(this.dataset.spec || '');
                });
            });
        })();

        (function(){
            const track = document.getElementById('agtGrid');
            const prevBtn = document.getElementById('agtCsPrev');
            const nextBtn = document.getElementById('agtCsNext');
            if (!track || !prevBtn || !nextBtn) return;

            const scrollAmount = () => track.querySelector('.agt-card')?.offsetWidth + 20 || 270;

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
        </script>
        <?php
        return ob_get_clean();
    }
}

// Render it (assumes $conn is already defined on the including page)
echo render_agt_section($conn);