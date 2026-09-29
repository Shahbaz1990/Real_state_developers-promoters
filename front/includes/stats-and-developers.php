<!-- ============ STATS + TOP DEVELOPERS (2-COLUMN ROW) ============ -->
<style>
  /* ============================================
     DARK MODE SUPPORT - Stats & Developers
     ============================================ */
  body.dark-mode .dev-card {
    background: var(--dm-bg-secondary, #161b22);
    border-color: var(--dm-border, #30363d);
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
  }
  body.dark-mode .dev-card:hover {
    box-shadow: 0 12px 28px rgba(0,0,0,0.4);
  }
  body.dark-mode .dev-card .name {
    color: var(--dm-text-primary, #f0f6fc);
  }
  body.dark-mode .dev-card .meta {
    color: var(--dm-text-secondary, #8b949e);
  }
  body.dark-mode .dev-card .meta .count-num {
    color: var(--dm-text-primary, #f0f6fc);
  }

  body.dark-mode .stats-band {
    background: linear-gradient(135deg, #0a0f1a, #0d1a2e) !important;
  }
  body.dark-mode .stats-band .num {
    color: var(--dm-text-primary, #f0f6fc);
  }
  body.dark-mode .stats-band .label {
    color: var(--dm-text-secondary, #8b949e);
  }
  body.dark-mode .stats-band .stats-divider {
    background: var(--dm-border, #30363d);
  }
  body.dark-mode .stats-band::before {
    background: radial-gradient(circle, rgba(31,111,235,0.15) 0%, transparent 70%);
  }

  body.dark-mode .dev-nav-btn {
    background: var(--dm-bg-secondary, #161b22);
    border-color: var(--dm-border, #30363d);
    color: var(--dm-text-primary, #f0f6fc);
  }
  body.dark-mode .dev-nav-btn:hover {
    border-color: var(--sy-gold, #f0b429);
    color: var(--sy-gold, #f0b429);
  }
  body.dark-mode .dev-nav-btn:disabled {
    opacity: 0.3;
  }

  body.dark-mode .sd-section-title-wrapper h2 {
    color: var(--dm-text-primary, #f0f6fc);
  }
  body.dark-mode .sd-section-title-wrapper .section-subtitle {
    color: var(--sy-gold, #f0b429);
  }
  body.dark-mode .sd-section-title-wrapper .title-underline {
    background: linear-gradient(90deg, var(--sy-gold, #f0b429), #d99b00);
  }

  body.dark-mode .badge-logo {
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
  }
  .badge-logo.grad-1{ background:linear-gradient(135deg,#ff6b6b,#f06595); }
  .badge-logo.grad-2{ background:linear-gradient(135deg,#4dabf7,#3b5bdb); }
  .badge-logo.grad-3{ background:linear-gradient(135deg,#38d9a9,#12b886); }
  .badge-logo.grad-4{ background:linear-gradient(135deg,#ffa94d,#f76707); }
  .badge-logo.grad-5{ background:linear-gradient(135deg,#9775fa,#7048e8); }
  .badge-logo.grad-6{ background:linear-gradient(135deg,#ff8787,#e64980); }
  .badge-logo.grad-7{ background:linear-gradient(135deg,#20c997,#0ca678); }
  .badge-logo.grad-8{ background:linear-gradient(135deg,#ffd43b,#fab005); }
  
  /* ============================================
     ORIGINAL STYLES
     ============================================ */
  .sd-section-title-wrapper{
    position:relative;
    padding-bottom:.5rem;
  }
  .sd-section-title-wrapper .section-subtitle{
    font-size:.85rem;
    font-weight:600;
    color:#1f6feb;
    text-transform:uppercase;
    letter-spacing:.5px;
    display:block;
    margin-bottom:.25rem;
    transition: color 0.3s ease;
  }
  .sd-section-title-wrapper h2{
    font-size:1.7rem;
    font-weight:700;
    margin:0;
    transition: color 0.3s ease;
  }
  .sd-section-title-wrapper .title-underline{
    width:60px;
    height:3px;
    background:linear-gradient(90deg, #1f6feb, #6c9cf5);
    border-radius:2px;
    margin-top:.5rem;
    transition: background 0.3s ease;
  }

  .dev-card{
    background:#fff;
    border:1px solid #e7e9ee;
    border-radius:1rem;
    padding:1.25rem 1rem;
    text-align:center;
    height:100%;
    transition:box-shadow .2s ease, transform .2s ease, background 0.3s ease, border-color 0.3s ease;
  }
  .dev-card:hover{
    box-shadow:0 12px 28px rgba(18,33,58,.10);
    transform:translateY(-2px);
  }
  .dev-card .badge-logo{
    width:56px; height:56px;
    margin:0 auto .75rem;
    border-radius:50%;
    color:#fff;
    font-weight:700;
    font-size:1.3rem;
    display:flex;
    align-items:center;
    justify-content:center;
    box-shadow:0 4px 12px rgba(18,33,58,.18);
    transition: box-shadow 0.3s ease;
  }
  
  .dev-card .name{
    font-weight:700;
    font-size:1rem;
    margin-bottom:.25rem;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
    transition: color 0.3s ease;
  }
  .dev-card .meta{
    color:#6b7686;
    font-size:.85rem;
    transition: color 0.3s ease;
  }
  .dev-card .meta .count-num{
    font-weight:700;
    color:#12213a;
    transition: color 0.3s ease;
  }

  /* Stack the two columns on small screens, side by side from lg up */
  .stats-dev-col + .stats-dev-col{
    margin-top:2.25rem;
  }
  @media (min-width: 992px){
    .stats-dev-col + .stats-dev-col{
      margin-top:0;
    }
  }

  .stats-band{
    height:100%;
    display:flex;
    align-items:center;
    background:linear-gradient(135deg, #030411, #030411);
    border-radius:1rem;
    padding:2rem 1.5rem;
    position:relative;
    overflow:hidden;
    transition: background 0.3s ease;
  }
  .stats-band::before{
    content:'';
    position:absolute;
    top:-50%;
    right:-50%;
    width:100%;
    height:100%;
    background:radial-gradient(circle, rgba(31,111,235,0.1) 0%, transparent 70%);
    pointer-events:none;
    transition: background 0.3s ease;
  }
  .stats-band .row{ 
    width:100%;
    position:relative;
    z-index:1;
  }

  .stats-band .num{
    font-size:2.8rem;
    font-weight:800;
    color:#fff;
    line-height:1.2;
    margin-bottom:.25rem;
    transition: color 0.3s ease;
  }
  .stats-band .label{
    font-size:.95rem;
    font-weight:500;
    color:rgba(255,255,255,0.7);
    transition: color 0.3s ease;
  }

  .dev-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:1rem;
  }
  .dev-slider-track{
    display:flex;
    gap:1rem;
    overflow-x:auto;
    scroll-snap-type:x mandatory;
    scroll-behavior:smooth;
    padding-bottom:.5rem;
    -ms-overflow-style:none;
    scrollbar-width:none;
  }
  .dev-slider-track::-webkit-scrollbar{ display:none; }
  .dev-slider-track .dev-card{
    flex:0 0 auto;
    width:150px;
    scroll-snap-align:start;
  }
  .dev-nav-btn{
    width:34px; height:34px;
    border-radius:50%;
    border:1px solid #e7e9ee;
    background:#fff;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    color:#12213a;
    transition: all 0.2s ease;
  }
  .dev-nav-btn:hover{ border-color:#1f6feb; color:#1f6feb; }
  .dev-nav-btn:disabled{ opacity:.4; cursor:not-allowed; }

  .dev-card .badge-logo .bi{
    font-size:1.8rem;
  }

  /* Stats counter separator */
  .stats-divider{
    width:40px;
    height:2px;
    background:rgba(255,255,255,0.2);
    margin:0.25rem auto 0.75rem;
    transition: background 0.3s ease;
  }

  /* Mobile responsive */
  @media (max-width: 575.98px) {
    .stats-band .num {
      font-size: 2rem;
    }
    .stats-band .label {
      font-size: 0.8rem;
    }
    .dev-slider-track .dev-card {
      width: 130px;
    }
    .sd-section-title-wrapper h2 {
      font-size: 1.3rem;
    }
    .sd-section-title-wrapper .section-subtitle {
      font-size: 0.75rem;
    }
  }
</style>

<section class="section pt-4 stats-counter-section dev-counter-section">
  <div class="container">
    <div class="row g-4 align-items-stretch">

      <!-- ===== Column 1: Stats band (Dark background) ===== -->
      <div class="col-lg-6 stats-dev-col">
        <div class="stats-band">
          <div class="row text-center g-4">
            <?php foreach ($stats as $s): ?>
              <div class="col-6">
                <div class="num" data-count-value="<?php echo htmlspecialchars($s['value']); ?>">0</div>
                <div class="stats-divider"></div>
                <div class="label"><?php echo htmlspecialchars($s['label']); ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- ===== Column 2: Top Developers (slider) ===== -->
      <div class="col-lg-6 stats-dev-col">
        <div class="dev-header">
          <div class="sd-section-title-wrapper">
            <span class="section-subtitle">
              <i class="bi bi-trophy"></i> Trusted builders
            </span>
            <h2 class="mt-1 mb-0">
              Top Developers
            </h2>
            <div class="title-underline"></div>
          </div>
          <?php if (count($developers) >= 3): ?>
            <div class="d-flex gap-2">
              <button type="button" class="dev-nav-btn" id="devPrev" aria-label="Previous">
                <i class="bi bi-chevron-left"></i>
              </button>
              <button type="button" class="dev-nav-btn" id="devNext" aria-label="Next">
                <i class="bi bi-chevron-right"></i>
              </button>
            </div>
          <?php endif; ?>
        </div>

        <div class="dev-slider-track" id="devTrack">
          <?php
            $gradClasses = ['grad-1', 'grad-2', 'grad-3', 'grad-4', 'grad-5', 'grad-6', 'grad-7', 'grad-8'];
            $i = 0;
          ?>
          <?php foreach ($developers as $d): ?>
            <div class="dev-card">
              <div class="badge-logo <?php echo $gradClasses[$i % count($gradClasses)]; ?>">
                <?php if (!empty($d['logo']) && file_exists($d['logo'])): ?>
                  <img src="<?php echo htmlspecialchars($d['logo']); ?>" alt="<?php echo htmlspecialchars($d['name']); ?>" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">
                <?php else: ?>
                  <?php echo strtoupper(substr($d['name'], 0, 1)); ?>
                <?php endif; ?>
              </div>
              <div class="name"><?php echo htmlspecialchars($d['name']); ?></div>
              <div class="meta">
                <span class="count-num" data-count-target="<?php echo (int) $d['projects']; ?>">0</span> projects ·
                <span class="count-num" data-count-target="<?php echo (int) $d['years']; ?>">0</span> yrs
              </div>
            </div>
          <?php $i++; endforeach; ?>
        </div>
      </div>

    </div>
  </div>
</section>

<script>
// ============ Count-up animation for both the stats band and developer cards,
// each triggered once its own section scrolls into view ============
(function(){
    const DURATION = 1400; // ms

    function parseStatValue(raw) {
        const match = raw.match(/^([^\d]*)([\d,]*\.?\d+)(.*)$/);
        if (!match) {
            return { prefix: '', target: 0, suffix: raw, decimals: 0 };
        }
        const prefix = match[1] || '';
        const numStr = match[2].replace(/,/g, '');
        const suffix = match[3] || '';
        const decimals = numStr.includes('.') ? numStr.split('.')[1].length : 0;
        const target = parseFloat(numStr);
        return { prefix, target, suffix, decimals };
    }

    function animateStat(el) {
        const raw = el.dataset.countValue;
        const { prefix, target, suffix, decimals } = parseStatValue(raw);
        const start = performance.now();

        function tick(now) {
            const progress = Math.min((now - start) / DURATION, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const current = eased * target;
            el.textContent = prefix + current.toFixed(decimals) + suffix;

            if (progress < 1) {
                requestAnimationFrame(tick);
            } else {
                el.textContent = prefix + target.toFixed(decimals) + suffix;
            }
        }
        requestAnimationFrame(tick);
    }

    function animateCount(el) {
        const target = parseInt(el.dataset.countTarget, 10) || 0;
        const start = performance.now();

        function tick(now) {
            const progress = Math.min((now - start) / DURATION, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.round(eased * target);

            if (progress < 1) {
                requestAnimationFrame(tick);
            } else {
                el.textContent = target;
            }
        }
        requestAnimationFrame(tick);
    }

    function runCounters(section) {
        section.querySelectorAll('.num[data-count-value]').forEach(animateStat);
        section.querySelectorAll('.count-num').forEach(animateCount);
    }

    const section = document.querySelector('.stats-counter-section.dev-counter-section');
    if (!section) return;

    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function(entries, obs){
            entries.forEach(function(entry){
                if (entry.isIntersecting) {
                    runCounters(entry.target);
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2 });

        observer.observe(section);
    } else {
        runCounters(section);
    }
})();

// ============ Top Developers slider prev/next ============
(function(){
    const track = document.getElementById('devTrack');
    const prevBtn = document.getElementById('devPrev');
    const nextBtn = document.getElementById('devNext');
    if (!track || !prevBtn || !nextBtn) return;

    const scrollAmount = () => track.querySelector('.dev-card')?.offsetWidth + 16 || 166;

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