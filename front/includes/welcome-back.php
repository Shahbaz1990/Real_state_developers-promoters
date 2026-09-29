<?php
/*
 |--------------------------------------------------------------------------
 | "Welcome back!" dashboard section.
 |
 | HONESTY NOTE on what's real vs. illustrative here:
 | - "Your Recent Viewed" and "Price Insights" are genuinely dynamic — they
 |   read from localStorage (same pattern as the wishlist) + your real
 |   trending_projects data via the APIs below. If nothing's been viewed
 |   yet, both panels show an honest empty state, not fake data.
 | - "Property Recommendations" is a static promo panel — there's no
 |   recommendation engine or user-preference data in this project yet.
 | - "Explore Calculators" links to pages that don't exist yet
 |   (affordability.php, buy-vs-rent.php, cost-of-living.php) — placeholders
 |   until those get built.
 |--------------------------------------------------------------------------
 */
?>
<style>
  :root{
    --wb-navy:#12213a;
    --wb-blue:#1f6feb;
    --wb-gold:#f5b942;
    --wb-green:#0f9d58;
    --wb-pink:#e0245e;
    --wb-muted:#6b7686;
    --wb-line:#e7e9ee;
    --wb-bg-soft:#f7f8fa;
  }

 /* ============ Medium-dark section band: blueprint grid + soft glow ============ */
  .wb-section{
    position:relative;
    background:#0d1117;
    padding:3.75rem 0 3.5rem;
    overflow:hidden;
  }
  .wb-section .container{ position:relative; z-index:1; }

   .wb-eyebrow{
    display:block;
    color:#8fb4f5;
    text-transform:uppercase;
    letter-spacing:.08em;
    font-size:.78rem;
    font-weight:700;
    margin-bottom:.4rem;
  }
  .wb-heading{
    font-family:'Fraunces', serif;
    font-size:1.9rem;
    font-weight:700;
    color:#fff;
    margin-bottom:1.75rem;
  }

  /* ============ Cards ============ */
  .wb-card{
    background:#fff;
    border-radius:1rem;
    padding:1.5rem;
    box-shadow:0 18px 40px rgba(5,10,20,.35);
    display:flex;
    flex-direction:column;
    height:100%;
    position:relative;
    overflow:hidden;
    border-top:3px solid transparent;
    transition:transform .2s ease, box-shadow .2s ease;
  }
  .wb-card:hover{
    transform:translateY(-3px);
    box-shadow:0 24px 48px rgba(5,10,20,.42);
  }
  .wb-card.accent-gold{ border-top-color:var(--wb-gold); }
  .wb-card.accent-green{ border-top-color:var(--wb-green); }
  .wb-card.accent-pink{ border-top-color:var(--wb-pink); }
  .wb-card.accent-blue{ border-top-color:var(--wb-blue); }

  .wb-card-title{
    display:flex;
    align-items:center;
    gap:.7rem;
    font-weight:700;
    color:var(--wb-navy);
    margin-bottom:1.1rem;
    font-size:1rem;
  }
  .wb-card-title .icon-box{
    width:38px;
    height:38px;
    border-radius:.65rem;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:1.05rem;
    flex-shrink:0;
    color:#fff;
    box-shadow:0 6px 14px rgba(18,33,58,.25);
  }
  .icon-box.gold-grad{ background:linear-gradient(135deg, var(--wb-gold), #e0a72f); color:var(--wb-navy); }
  .icon-box.green-grad{ background:linear-gradient(135deg, #17c982, var(--wb-green)); }
  .icon-box.pink-grad{ background:linear-gradient(135deg, #ff6b8b, var(--wb-pink)); }
  .icon-box.blue-grad{ background:linear-gradient(135deg, #4f8bf5, var(--wb-blue)); }

  .wb-card-body{ flex:1; }
  .wb-card-footer{
    margin-top:1rem;
    padding-top:1rem;
    border-top:1px solid var(--wb-line);
    font-weight:700;
    font-size:.9rem;
    color:var(--wb-navy);
    display:flex;
    align-items:center;
    gap:.4rem;
    text-decoration:none;
  }
  .wb-card-footer:hover{ color:var(--wb-blue); text-decoration:none; gap:.6rem; }
  .wb-card-footer i{ transition:transform .15s ease; }

  /* ============ Recommendations (static promo) ============ */
  .wb-rec-icons{ display:flex; gap:.6rem; margin-bottom:1rem; }
  .wb-rec-icons span{
    flex:1;
    height:46px;
    border-radius:.6rem;
    background:var(--wb-bg-soft);
    border:1px solid var(--wb-line);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:1.05rem;
    color:var(--wb-blue);
  }
  .wb-rec-illustration{
    background:linear-gradient(135deg, #eaf1ff, #f5f8ff);
    border:1px solid var(--wb-line);
    border-radius:.7rem;
    padding:1.1rem;
    text-align:center;
    margin-bottom:1rem;
  }
  .wb-rec-illustration .stars{ color:var(--wb-gold); font-size:1.1rem; letter-spacing:.15rem; margin-bottom:.3rem; }
  .wb-rec-illustration .home-icon{ font-size:1.9rem; color:var(--wb-blue); }
  .wb-rec-note{ text-align:center; font-weight:700; color:var(--wb-navy); }
  .wb-rec-sub{ text-align:center; color:var(--wb-muted); font-size:.85rem; margin-top:.3rem; }

  /* ============ Recent viewed ============ */
  .wb-recent-card{ background:var(--wb-bg-soft); border:1px solid var(--wb-line); border-radius:.7rem; overflow:hidden; }
  .wb-recent-card img{ width:100%; height:110px; object-fit:cover; display:block; }
  .wb-recent-card .info{ padding:.75rem .9rem; }
  .wb-recent-card h6{ font-weight:700; font-size:.92rem; margin-bottom:.1rem; color:var(--wb-navy); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .wb-recent-card .loc{ color:var(--wb-muted); font-size:.8rem; margin-bottom:.3rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .wb-recent-card .price{ font-weight:700; font-size:.85rem; color:var(--wb-blue); }
  .wb-recent-card .meta{ font-size:.75rem; color:#8a93a3; margin-top:.4rem; }
  .wb-recent-card .enquire-link{ font-weight:700; font-size:.82rem; color:var(--wb-navy); text-decoration:underline; }
  .wb-recent-card .enquire-link:hover{ color:var(--wb-blue); }
  .wb-empty-note{ color:var(--wb-muted); font-size:.85rem; text-align:center; padding:1.5rem 0; }

  /* Recent viewed — slider (up to 10 items, one visible at a time) */
  .wb-recent-slider-wrap{ position:relative; }
  .wb-recent-slider{
    display:flex;
    overflow-x:auto;
    scroll-snap-type:x mandatory;
    -webkit-overflow-scrolling:touch;
    scrollbar-width:none;
  }
  .wb-recent-slider::-webkit-scrollbar{ display:none; }
  .wb-recent-slider .wb-recent-card{
    flex:0 0 100%;
    scroll-snap-align:start;
  }
  .wb-recent-nav{
    position:absolute;
    top:38px;
    transform:translateY(-50%);
    width:28px; height:28px;
    border-radius:50%;
    border:none;
    background:rgba(18,33,58,.65);
    color:#fff;
    display:flex; align-items:center; justify-content:center;
    font-size:.8rem;
    z-index:2;
  }
  .wb-recent-nav:hover{ background:var(--wb-navy); }
  .wb-recent-nav.prev{ left:.4rem; }
  .wb-recent-nav.next{ right:.4rem; }
  .wb-recent-dots{
    display:flex; justify-content:center; gap:.35rem; margin-top:.6rem;
  }
  .wb-recent-dots button{
    width:6px; height:6px; border-radius:50%; border:none; padding:0;
    background:var(--wb-line);
  }
  .wb-recent-dots button.active{ background:var(--wb-blue); width:16px; border-radius:3px; }

  /* ============ Price insights ============ */
  .wb-insight-box{
    border-radius:.7rem;
    padding:.9rem 1rem;
    font-size:.85rem;
    margin-bottom:.75rem;
    display:flex;
    gap:.65rem;
    border:1px solid transparent;
  }
  .wb-insight-box.green{ background:#e8f9f1; border-color:#c9efdc; color:#0b5e37; }
  .wb-insight-box.gold{ background:#fef3e0; border-color:#f7e2b4; color:#7a5a06; }
  .wb-insight-box .emoji{ font-size:1.1rem; flex-shrink:0; }
  .wb-insight-box strong{ color:inherit; }

  /* ============ Calculators ============ */
  .wb-calc-link{
    display:flex; align-items:center; justify-content:space-between;
    background:var(--wb-bg-soft); border:1px solid var(--wb-line);
    border-radius:.7rem; padding:.8rem 1rem; margin-bottom:.6rem;
    text-decoration:none; color:var(--wb-navy);
    transition:background .15s ease, border-color .15s ease;
  }
  .wb-calc-link:last-child{ margin-bottom:0; }
  .wb-calc-link:hover{ background:#eaf1ff; border-color:#cfe0ff; color:var(--wb-blue); }
  .wb-calc-link .left{ display:flex; align-items:center; gap:.7rem; }
  .wb-calc-link .icon-box{ background:linear-gradient(135deg, var(--wb-gold), #e0a72f); color:var(--wb-navy); width:34px; height:34px; font-size:.95rem; }
  .wb-calc-link small{ display:block; color:var(--wb-muted); font-size:.78rem; font-weight:400; }
  .wb-calc-link:hover small{ color:var(--wb-blue); }
  .wb-calc-link i{ color:var(--wb-muted); }
  .wb-calc-link:hover i{ color:var(--wb-blue); }
</style>

<section class="wb-section">
  <div class="container">
    <span class="wb-eyebrow">Your dashboard</span>
    <h2 class="wb-heading">Welcome back!</h2>

    <div class="row g-4">

      <!-- ============ Property Recommendations (static) ============ -->
      <div class="col-md-6 col-lg-3">
        <div class="wb-card accent-gold">
          <div class="wb-card-title">
            <span class="icon-box gold-grad"><i class="bi bi-house-heart-fill"></i></span>
            Property Recommendations
          </div>
          <div class="wb-card-body">
            <div class="wb-rec-icons">
              <span><i class="bi bi-person-fill"></i></span>
              <span><i class="bi bi-geo-alt-fill"></i></span>
              <span><i class="bi bi-currency-rupee"></i></span>
            </div>
            <div class="wb-rec-illustration">
              <div class="stars"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
              <div class="home-icon"><i class="bi bi-houses-fill"></i></div>
            </div>
            <div class="wb-rec-note">Smart recommendations</div>
            <div class="wb-rec-sub">Based on your needs, budget, and preferred locations.</div>
          </div>
          <a href="properties.php" class="wb-card-footer">Show Recommendations <i class="bi bi-arrow-right"></i></a>
        </div>
      </div>

      <!-- ============ Your Recent Viewed (dynamic, up to 10, slider) ============ -->
      <div class="col-md-6 col-lg-3">
        <div class="wb-card accent-green">
          <div class="wb-card-title">
            <span class="icon-box green-grad"><i class="bi bi-clock-history"></i></span>
            Your Recent Viewed
          </div>
          <div class="wb-card-body" id="wbRecentBody">
            <div class="wb-empty-note">Loading...</div>
          </div>
        </div>
      </div>

      <!-- ============ Price Insights (dynamic) ============ -->
      <div class="col-md-6 col-lg-3">
        <div class="wb-card accent-pink">
          <div class="wb-card-title">
            <span class="icon-box pink-grad"><i class="bi bi-graph-up-arrow"></i></span>
            <span id="wbInsightsTitle">Price Insights</span>
          </div>
          <div class="wb-card-body" id="wbInsightsBody">
            <div class="wb-empty-note">View a project to see local pricing insights.</div>
          </div>
          <a href="properties.php" class="wb-card-footer" id="wbInsightsFooter" style="display:none;">View Detailed Insights <i class="bi bi-arrow-right"></i></a>
        </div>
      </div>

      <!-- ============ Explore Calculators (static links) ============ -->
      <div class="col-md-6 col-lg-3">
        <div class="wb-card accent-blue">
          <div class="wb-card-title">
            <span class="icon-box blue-grad"><i class="bi bi-calculator-fill"></i></span>
            Explore Calculators
          </div>
          <div class="wb-card-body">
            <a href="finance.php" class="wb-calc-link">
              <span class="left">
                <span class="icon-box"><i class="bi bi-calculator"></i></span>
                <span>
                  Check Affordability
                  <small>Know your buying budget</small>
                </span>
              </span>
              <i class="bi bi-arrow-right"></i>
            </a>
            <a href="finance.php" class="wb-calc-link">
              <span class="left">
                <span class="icon-box"><i class="bi bi-tag-fill"></i></span>
                <span>
                  Buy vs Rent
                  <small>Compare owning vs renting</small>
                </span>
              </span>
              <i class="bi bi-arrow-right"></i>
            </a>
            <a href="finance.php" class="wb-calc-link">
              <span class="left">
                <span class="icon-box"><i class="bi bi-bar-chart-fill"></i></span>
                <span>
                  Cost of Living
                  <small>Monthly expense estimate</small>
                </span>
              </span>
              <i class="bi bi-arrow-right"></i>
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<script>
(function () {
    const RECENT_KEY = 'tatito_recently_viewed'; // array of { id, viewedAt }
    const MAX_RECENT = 10;

    function getRecentlyViewed() {
        try { return JSON.parse(localStorage.getItem(RECENT_KEY)) || []; }
        catch (e) { return []; }
    }

    function timeAgo(isoString) {
        const diffMs = Date.now() - new Date(isoString).getTime();
        const days = Math.floor(diffMs / 86400000);
        if (days <= 0) return 'Today';
        if (days === 1) return '1 day ago';
        return days + ' days ago';
    }

    const recentBody     = document.getElementById('wbRecentBody');
    const insightsBody   = document.getElementById('wbInsightsBody');
    const insightsTitle  = document.getElementById('wbInsightsTitle');
    const insightsFooter = document.getElementById('wbInsightsFooter');

    const recentList = getRecentlyViewed()
        .sort((a, b) => new Date(b.viewedAt) - new Date(a.viewedAt))
        .slice(0, MAX_RECENT);

    if (recentList.length === 0) {
        recentBody.innerHTML = '<div class="wb-empty-note">No properties viewed yet. Browse listings to see them here.</div>';
        return;
    }

    const mostRecent = recentList[0];

    fetch('/backend/api/wishlist-projects.php?ids=' + recentList.map(r => r.id).join(','))
        .then(res => res.json())
        .then(function (result) {
            if (!result.success || result.data.length === 0) {
                recentBody.innerHTML = '<div class="wb-empty-note">No properties viewed yet.</div>';
                return;
            }

            // Preserve recency order, and only include entries we actually
            // got project data back for.
            const projectsById = new Map(result.data.map(p => [String(p.id), p]));
            const slides = recentList
                .map(r => ({ meta: r, project: projectsById.get(String(r.id)) }))
                .filter(entry => entry.project);

            if (slides.length === 0) {
                recentBody.innerHTML = '<div class="wb-empty-note">No properties viewed yet.</div>';
                return;
            }

            const cardsHtml = slides.map(function (entry) {
                const p = entry.project;
                return `
                    <div class="wb-recent-card">
                        <img src="${p.image}" alt="">
                        <div class="info">
                            <h6>${p.project_name}</h6>
                            <div class="loc">${p.address ?? ''}</div>
                            <div class="price">${p.price ?? ''}</div>
                            <div class="meta">${timeAgo(entry.meta.viewedAt)}</div>
                            <a href="/front/property-listing/${p.slug}/" class="enquire-link">View Again</a>
                        </div>
                    </div>
                `;
            }).join('');

            const showNav = slides.length > 1;

            recentBody.innerHTML = `
                <div class="wb-recent-slider-wrap">
                    ${showNav ? '<button type="button" class="wb-recent-nav prev" aria-label="Previous"><i class="bi bi-chevron-left"></i></button>' : ''}
                    <div class="wb-recent-slider" id="wbRecentSlider">${cardsHtml}</div>
                    ${showNav ? '<button type="button" class="wb-recent-nav next" aria-label="Next"><i class="bi bi-chevron-right"></i></button>' : ''}
                </div>
                ${showNav ? '<div class="wb-recent-dots" id="wbRecentDots">' + slides.map((_, i) => `<button type="button" data-index="${i}" class="${i === 0 ? 'active' : ''}" aria-label="Go to item ${i + 1}"></button>`).join('') + '</div>' : ''}
            `;

            if (showNav) {
                const sliderEl = document.getElementById('wbRecentSlider');
                const dotEls   = document.querySelectorAll('#wbRecentDots button');
                const prevBtn  = recentBody.querySelector('.wb-recent-nav.prev');
                const nextBtn  = recentBody.querySelector('.wb-recent-nav.next');

                function goTo(index) {
                    const clamped = Math.max(0, Math.min(index, slides.length - 1));
                    sliderEl.scrollTo({ left: clamped * sliderEl.clientWidth, behavior: 'smooth' });
                }

                if (prevBtn) prevBtn.addEventListener('click', function () {
                    goTo(Math.round(sliderEl.scrollLeft / sliderEl.clientWidth) - 1);
                });
                if (nextBtn) nextBtn.addEventListener('click', function () {
                    goTo(Math.round(sliderEl.scrollLeft / sliderEl.clientWidth) + 1);
                });
                dotEls.forEach(function (dot) {
                    dot.addEventListener('click', function () { goTo(parseInt(this.dataset.index, 10)); });
                });

                // Keep dots in sync when the user swipes/scrolls manually
                let scrollTimer = null;
                sliderEl.addEventListener('scroll', function () {
                    if (scrollTimer) clearTimeout(scrollTimer);
                    scrollTimer = setTimeout(function () {
                        const active = Math.round(sliderEl.scrollLeft / sliderEl.clientWidth);
                        dotEls.forEach((d, i) => d.classList.toggle('active', i === active));
                    }, 100);
                });
            }

            // Derive a locality from the most-recently-viewed project's
            // address (first comma-separated segment) for the insights panel.
            const mostRecentProject = projectsById.get(String(mostRecent.id));
            const locality = (mostRecentProject && mostRecentProject.address ? mostRecentProject.address : '').split(',')[0].trim();
            if (locality === '') return;

            insightsTitle.textContent = locality + ' Price Insights';

            fetch('/backend/api/locality-insights.php?locality=' + encodeURIComponent(locality))
                .then(res => res.json())
                .then(function (insightResult) {
                    const data = insightResult.data;
                    if (!insightResult.success || !data) {
                        insightsBody.innerHTML = '<div class="wb-empty-note">Not enough listings in this locality yet for pricing insights.</div>';
                        return;
                    }

                    insightsBody.innerHTML = `
                        <div class="wb-insight-box green">
                            <span class="emoji"><i class="bi bi-graph-up"></i></span>
                            <span>${data.locality} has <strong>${data.listing_count}</strong> listing${data.listing_count === 1 ? '' : 's'} on this site, with a median price of <strong>${data.median_price}</strong>.</span>
                        </div>
                        <div class="wb-insight-box gold">
                            <span class="emoji"><i class="bi bi-star-fill"></i></span>
                            <span>Prices in ${data.locality} range from <strong>${data.min_price}</strong> to <strong>${data.max_price}</strong>${data.bedroom_types.length ? ', across ' + data.bedroom_types.join(', ') + ' configurations' : ''}.</span>
                        </div>
                    `;
                    insightsFooter.style.display = 'flex';
                    insightsFooter.href = '/front/properties.php?search=' + encodeURIComponent(locality);
                });
        })
        .catch(function () {
            recentBody.innerHTML = '<div class="wb-empty-note">Could not load recently viewed properties.</div>';
        });
})();
</script>