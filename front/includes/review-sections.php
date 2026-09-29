<?php
// ===============================
// Fetch active customer reviews for the Reviews slider
// ===============================
$reviews = [];
$revResult = $conn->query("SELECT reviewer_name, project_name, rating, review_text
                            FROM reviews
                            WHERE status = 'Active'
                            ORDER BY review_order ASC, created_at DESC
                            LIMIT 12");
if ($revResult) {
    while ($row = $revResult->fetch_assoc()) {
        $reviews[] = $row;
    }
}
?>
<style>
  /* ============================================
     DARK MODE SUPPORT - Reviews Section
     ============================================ */
  body.dark-mode .review-card {
    background: var(--dm-bg-secondary, #161b22);
    border-color: var(--dm-border, #30363d);
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
  }
  body.dark-mode .review-card:hover {
    box-shadow: 0 12px 28px rgba(0,0,0,0.4);
  }
  body.dark-mode .review-card .review-text {
    color: var(--dm-text-primary, #f0f6fc);
  }
  body.dark-mode .review-card .reviewer .name {
    color: var(--dm-text-primary, #f0f6fc);
  }
  body.dark-mode .review-card .reviewer .meta {
    color: var(--dm-text-secondary, #8b949e);
  }
  body.dark-mode .review-card .reviewer {
    border-color: var(--dm-border, #30363d);
  }
  body.dark-mode .review-card .quote-icon {
    color: var(--dm-border, #30363d);
  }
  body.dark-mode .review-card .stars {
    color: var(--sy-gold, #f0b429);
  }
  body.dark-mode .review-card .rating-badge {
    background: var(--dm-bg-tertiary, #1c2333);
    color: var(--sy-gold, #f0b429);
  }

  body.dark-mode .review-nav-btn {
    background: var(--dm-bg-secondary, #161b22);
    border-color: var(--dm-border, #30363d);
    color: var(--dm-text-primary, #f0f6fc);
  }
  body.dark-mode .review-nav-btn:hover {
    border-color: var(--sy-gold, #f0b429);
    color: var(--sy-gold, #f0b429);
  }
  body.dark-mode .review-nav-btn:disabled {
    opacity: 0.3;
  }

  body.dark-mode .review-section-title-wrapper h2 {
    color: var(--dm-text-primary, #f0f6fc);
  }
  body.dark-mode .review-section-title-wrapper .section-subtitle {
    color: var(--sy-gold, #f0b429);
  }
  body.dark-mode .review-section-title-wrapper .title-underline {
    background: linear-gradient(90deg, var(--sy-gold, #f0b429), #d99b00);
  }

  body.dark-mode .review-header-wrap .rating-summary {
    background: var(--dm-bg-tertiary, #1c2333);
    border-color: var(--dm-border, #30363d);
  }
  body.dark-mode .review-header-wrap .rating-summary .big-num {
    color: var(--sy-gold, #f0b429);
  }
  body.dark-mode .review-header-wrap .rating-summary .small-stars {
    color: var(--sy-gold, #f0b429);
  }
  body.dark-mode .review-header-wrap .rating-summary .total-reviews {
    color: var(--dm-text-secondary, #8b949e);
  }

  body.dark-mode .alert-warning {
    background: var(--dm-bg-tertiary, #1c2333);
    border-color: var(--dm-border, #30363d);
    color: var(--dm-text-secondary, #8b949e);
  }

  /* ============================================
     ORIGINAL STYLES
     ============================================ */
  .review-section-title-wrapper{
    position:relative;
    padding-bottom:.5rem;
  }
  .review-section-title-wrapper .section-subtitle{
    font-size:.85rem;
    font-weight:600;
    color:#1f6feb;
    text-transform:uppercase;
    letter-spacing:.5px;
    display:block;
    margin-bottom:.25rem;
    transition: color 0.3s ease;
  }
  .review-section-title-wrapper h2{
    font-size:1.7rem;
    font-weight:700;
    margin:0;
    transition: color 0.3s ease;
  }
  .review-section-title-wrapper .title-underline{
    width:60px;
    height:3px;
    background:linear-gradient(90deg, #1f6feb, #6c9cf5);
    border-radius:2px;
    margin-top:.5rem;
    transition: background 0.3s ease;
  }

  .review-slider-wrap{ position:relative; margin-top:.5rem; }
  .review-grid{
    display:flex;
    gap:1.25rem;
    overflow-x:auto;
    scroll-snap-type:x mandatory;
    scroll-behavior:smooth;
    padding-bottom:.5rem;
    -ms-overflow-style:none;
    scrollbar-width:none;
  }
  .review-grid::-webkit-scrollbar{ display:none; }

  .review-card{
    position:relative;
    flex:0 0 auto;
    width:340px;
    scroll-snap-align:start;
    border:1px solid #e7e9ee;
    border-radius:1rem;
    padding:1.5rem;
    background:#fff;
    transition:box-shadow .2s ease, transform .2s ease, background 0.3s ease, border-color 0.3s ease;
  }
  .review-card:hover{
    box-shadow:0 12px 28px rgba(18,33,58,.10);
    transform:translateY(-2px);
  }
  .review-card .quote-icon{
    position:absolute;
    top:1rem; right:1.1rem;
    font-size:1.8rem;
    color:#e7e9ee;
    opacity:0.6;
    transition: color 0.3s ease;
  }
  .review-card .stars{ 
    color:#f5b942; 
    font-size:.95rem; 
    margin-bottom:.7rem;
    letter-spacing:2px;
    transition: color 0.3s ease;
  }
  .review-card .review-text{
    font-size:.94rem;
    color:#12213a;
    line-height:1.6;
    margin-bottom:1.2rem;
    display:-webkit-box;
    -webkit-line-clamp:5;
    -webkit-box-orient:vertical;
    overflow:hidden;
    font-style:italic;
    transition: color 0.3s ease;
  }
  .review-card .reviewer{ 
    display:flex; 
    align-items:center; 
    gap:.75rem;
    padding-top:.75rem;
    border-top:1px solid #f0f2f5;
    transition: border-color 0.3s ease;
  }
  .review-card .reviewer .name{ 
    font-weight:700; 
    font-size:.95rem;
    color:#12213a;
    transition: color 0.3s ease;
  }
  .review-card .reviewer .meta{ 
    color:#6b7686; 
    font-size:.82rem;
    transition: color 0.3s ease;
  }
  .badge-logo-sm{
    width:40px; 
    height:40px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-shrink:0;
    font-weight:700;
    font-size:1rem;
    color:#fff;
  }
  .badge-logo-sm.grad-r1{ background:linear-gradient(135deg,#ff6b6b,#f06595); }
  .badge-logo-sm.grad-r2{ background:linear-gradient(135deg,#4dabf7,#3b5bdb); }
  .badge-logo-sm.grad-r3{ background:linear-gradient(135deg,#38d9a9,#12b886); }
  .badge-logo-sm.grad-r4{ background:linear-gradient(135deg,#ffa94d,#f76707); }
  .badge-logo-sm.grad-r5{ background:linear-gradient(135deg,#9775fa,#7048e8); }
  .badge-logo-sm.grad-r6{ background:linear-gradient(135deg,#20c997,#0ca678); }

  .review-nav-btn{
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
  .review-nav-btn:hover{ border-color:#1f6feb; color:#1f6feb; }
  .review-nav-btn:disabled{ opacity:.4; cursor:not-allowed; }

  .review-card .rating-badge{
    display:inline-flex;
    align-items:center;
    gap:.3rem;
    background:#f8f9fc;
    padding:.2rem .6rem;
    border-radius:1rem;
    font-size:.75rem;
    font-weight:600;
    color:#f5b942;
    margin-bottom:.5rem;
    transition: background 0.3s ease, color 0.3s ease;
  }

  .review-header-wrap{
    display:flex;
    justify-content:space-between;
    align-items:flex-end;
    flex-wrap:wrap;
    gap:1rem;
  }
  .review-header-wrap .rating-summary{
    display:flex;
    align-items:center;
    gap:.5rem;
    background:#f8f9fc;
    padding:.4rem 1rem;
    border-radius:2rem;
    border:1px solid #e7e9ee;
    transition: background 0.3s ease, border-color 0.3s ease;
  }
  .review-header-wrap .rating-summary .big-num{
    font-size:1.5rem;
    font-weight:800;
    color:#1f6feb;
    transition: color 0.3s ease;
  }
  .review-header-wrap .rating-summary .small-stars{
    color:#f5b942;
    font-size:.85rem;
    letter-spacing:1px;
    transition: color 0.3s ease;
  }
  .review-header-wrap .rating-summary .total-reviews{
    color:#6b7686;
    font-size:.8rem;
    font-weight:500;
    transition: color 0.3s ease;
  }

  /* Mobile responsive */
  @media (max-width: 575.98px) {
    .review-card {
      width: 280px;
      padding: 1.25rem;
    }
    .review-section-title-wrapper h2 {
      font-size: 1.3rem;
    }
    .review-section-title-wrapper .section-subtitle {
      font-size: 0.75rem;
    }
    .review-header-wrap .rating-summary {
      padding: .3rem .7rem;
    }
    .review-header-wrap .rating-summary .big-num {
      font-size: 1.2rem;
    }
    .review-header-wrap .rating-summary .small-stars {
      font-size: .7rem;
    }
    .review-header-wrap .rating-summary .total-reviews {
      font-size: .7rem;
    }
  }

  @media (max-width: 400px) {
    .review-card {
      width: 240px;
      padding: 1rem;
    }
  }
</style>

<!-- ============ REVIEWS ============ -->
<section class="section pt-4">
  <div class="container">
    <div class="review-header-wrap mb-4">
      <div class="review-section-title-wrapper">
        <span class="section-subtitle">
          <i class="bi bi-chat-quote"></i> Real stories
        </span>
        <h2 class="mt-1 mb-0">
          What Our Customers Say
        </h2>
        <div class="title-underline"></div>
      </div>
      <div class="d-flex align-items-center gap-3">
        <?php if (!empty($reviews)): ?>
          <div class="rating-summary d-none d-md-flex">
            <span class="big-num">4.8</span>
            <span class="small-stars">
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-half"></i>
            </span>
            <span class="total-reviews">(<?php echo count($reviews); ?> reviews)</span>
          </div>
        <?php endif; ?>
        <?php if (count($reviews) >= 4): ?>
          <div class="d-flex gap-2">
            <button type="button" class="review-nav-btn" id="reviewPrev" aria-label="Previous">
              <i class="bi bi-chevron-left"></i>
            </button>
            <button type="button" class="review-nav-btn" id="reviewNext" aria-label="Next">
              <i class="bi bi-chevron-right"></i>
            </button>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <?php if (!empty($reviews)): ?>
      <div class="review-slider-wrap">
        <div class="review-grid" id="reviewGrid">
          <?php 
            $gradClasses = ['grad-r1', 'grad-r2', 'grad-r3', 'grad-r4', 'grad-r5', 'grad-r6'];
            $i = 0;
            foreach ($reviews as $r):
          ?>
            <div class="review-card">
              <i class="bi bi-quote quote-icon"></i>
              <div class="stars">
                <?php for ($j = 1; $j <= 5; $j++): ?>
                  <i class="bi <?php echo $j <= (int) $r['rating'] ? 'bi-star-fill' : 'bi-star'; ?>"></i>
                <?php endfor; ?>
              </div>
              <p class="review-text">&ldquo;<?php echo htmlspecialchars($r['review_text']); ?>&rdquo;</p>
              <div class="reviewer">
                <div class="badge-logo-sm <?php echo $gradClasses[$i % count($gradClasses)]; ?>">
                  <?php echo strtoupper(substr($r['reviewer_name'], 0, 1)); ?>
                </div>
                <div>
                  <div class="name"><?php echo htmlspecialchars($r['reviewer_name']); ?></div>
                  <?php if (!empty($r['project_name'])): ?>
                    <div class="meta"><i class="bi bi-building"></i> <?php echo htmlspecialchars($r['project_name']); ?></div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php $i++; endforeach; ?>
        </div>
      </div>
    <?php else: ?>
      <div class="alert alert-warning">
        <i class="bi bi-info-circle"></i>
        No reviews yet. Be the first to share your experience!
      </div>
    <?php endif; ?>
  </div>
</section>

<script>
(function(){
    const track = document.getElementById('reviewGrid');
    const prevBtn = document.getElementById('reviewPrev');
    const nextBtn = document.getElementById('reviewNext');
    if (!track || !prevBtn || !nextBtn) return;

    const scrollAmount = () => track.querySelector('.review-card')?.offsetWidth + 20 || 300;

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