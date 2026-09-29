<?php
/**
 * sell.php — "List Your Property" page
 * Follows the same include pattern as hotels-homestays.php / all-hotels-homestays.php:
 * includes/header.php and includes/footer.php are parameterized with
 * title, logo link, city-strip items, and extra head/body content.
 *
 * Adjust the require paths below to match your actual folder structure.
 */
require_once __DIR__ . '/includes/config.php';

$pageTitle = "Sell / List Your Property — EstateHub";

$extraHead = <<<HTML
<!-- These are included here in case header.php does not already load them.
     If header.php already links Bootstrap 5, Bootstrap Icons, and these
     Google Fonts, remove this block to avoid loading them twice. -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
  :root{
    --navy:#0f2540;
    --navy-deep:#081729;
    --gold:#c9a24b;
    --gold-soft:#e7cf8f;
    --ivory:#f7f4ee;
    --ink:#1c1f24;
    --muted:#5b6470;
    --line:#e4e1d8;
  }

  .sell-page{ font-family:'Inter',system-ui,sans-serif; color:var(--ink); background:var(--ivory); }
  .sell-page h1, .sell-page h2, .sell-page h3, .sell-page .font-display{
    font-family:'Fraunces', Georgia, serif;
  }

  /* ---------- HERO ---------- */
  .sell-hero{
    background: linear-gradient(160deg, var(--navy-deep) 0%, var(--navy) 62%, #163158 100%);
    color:#fff;
    position:relative;
    overflow:hidden;
    padding: 84px 0 110px;
  }
  .sell-hero::after{
    content:"";
    position:absolute; inset:0;
    background-image: radial-gradient(circle at 82% 20%, rgba(201,162,75,.16), transparent 45%);
    pointer-events:none;
  }
  .sell-eyebrow{
    display:inline-flex; align-items:center; gap:.5rem;
    text-transform:uppercase; letter-spacing:.12em; font-size:.72rem; font-weight:600;
    color: var(--gold-soft); border:1px solid rgba(231,207,143,.4);
    padding:.35rem .85rem; border-radius:999px;
  }
  .sell-hero h1{ font-size:2.6rem; line-height:1.12; margin:1rem 0 1rem; font-weight:600; }
  .sell-hero h1 em{ font-style:normal; color:var(--gold-soft); }
  .sell-hero p.lead{ color:rgba(255,255,255,.78); max-width:520px; font-size:1.05rem; }

  .sell-toggle{
    display:inline-flex; background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.18);
    border-radius:999px; padding:4px; margin-bottom:1.75rem;
  }
  .sell-toggle button{
    border:0; background:transparent; color:rgba(255,255,255,.7);
    padding:.5rem 1.25rem; border-radius:999px; font-weight:600; font-size:.85rem;
  }
  .sell-toggle button.active{ background:var(--gold); color:var(--navy-deep); }

  .sell-hero-card{
    background:#fff; border-radius:18px; padding:1.75rem;
    box-shadow: 0 30px 60px -20px rgba(0,0,0,.45);
    color:var(--ink);
  }
  .sell-hero-card label{ font-size:.72rem; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); font-weight:700; }
  .sell-hero-card .form-select, .sell-hero-card .form-control{
    border-color:var(--line); border-radius:10px; padding:.6rem .75rem; font-size:.92rem;
  }
  .btn-gold{
    background:var(--gold); border:1px solid var(--gold); color:var(--navy-deep);
    font-weight:700; border-radius:10px; padding:.65rem 1.1rem;
  }
  .btn-gold:hover{ background:var(--gold-soft); border-color:var(--gold-soft); color:var(--navy-deep); }

  .sell-stats{ display:flex; gap:2.25rem; margin-top:2.5rem; flex-wrap:wrap; }
  .sell-stats .num{ font-family:'Fraunces',serif; font-size:1.6rem; color:var(--gold-soft); font-weight:600; }
  .sell-stats .lbl{ font-size:.78rem; color:rgba(255,255,255,.65); }

  /* ---------- HOW IT WORKS ---------- */
  .how-it-works{ padding:80px 0; }
  .how-it-works .kicker{ color:var(--gold); font-weight:700; letter-spacing:.08em; text-transform:uppercase; font-size:.78rem; }
  .step-card{
    background:#fff; border:1px solid var(--line); border-radius:16px; padding:1.75rem;
    height:100%; position:relative; transition:transform .2s ease, box-shadow .2s ease;
  }
  .step-card:hover{ transform:translateY(-4px); box-shadow:0 20px 40px -24px rgba(15,37,64,.25); }
  .step-num{
    font-family:'Fraunces',serif; font-size:2.1rem; color:var(--gold); font-weight:600; line-height:1;
  }
  .step-card h3{ font-size:1.05rem; font-weight:600; margin:.75rem 0 .5rem; }
  .step-card p{ color:var(--muted); font-size:.9rem; margin:0; }

  /* ---------- FORM ---------- */
  .list-form-section{ background:#fff; padding:80px 0; border-top:1px solid var(--line); }
  .form-shell{
    background:var(--ivory); border:1px solid var(--line); border-radius:20px; padding:2.5rem;
  }
  .form-fieldset-title{
    display:flex; align-items:center; gap:.6rem;
    font-family:'Fraunces',serif; font-size:1.15rem; font-weight:600; color:var(--navy);
    margin-bottom:1.25rem; padding-bottom:.6rem; border-bottom:1px solid var(--line);
  }
  .form-fieldset-title .badge-step{
    width:28px; height:28px; border-radius:50%; background:var(--navy); color:var(--gold-soft);
    display:inline-flex; align-items:center; justify-content:center; font-size:.8rem; font-weight:700;
  }
  .list-form-section label{ font-size:.82rem; font-weight:600; color:var(--ink); }
  .list-form-section .form-control, .list-form-section .form-select{
    border-color:var(--line); border-radius:10px; padding:.6rem .75rem;
  }
  .list-form-section .form-control:focus, .list-form-section .form-select:focus{
    border-color:var(--gold); box-shadow:0 0 0 3px rgba(201,162,75,.18);
  }
  .chip-group{ display:flex; flex-wrap:wrap; gap:.5rem; }
  .chip-group input{ display:none; }
  .chip-group label{
    border:1px solid var(--line); border-radius:999px; padding:.4rem 1rem; font-size:.85rem;
    font-weight:600; color:var(--muted); cursor:pointer; margin:0;
  }
  .chip-group input:checked + label{ background:var(--navy); border-color:var(--navy); color:#fff; }

  .dropzone{
    border:2px dashed var(--line); border-radius:14px; padding:2.25rem 1.5rem; text-align:center;
    color:var(--muted); background:#fff;
  }
  .dropzone i{ font-size:1.8rem; color:var(--gold); }

  /* ---------- WHY SELL WITH US ---------- */
  .why-us{ background:var(--navy-deep); color:#fff; padding:80px 0; }
  .why-card{
    background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.1);
    border-radius:16px; padding:1.6rem; height:100%;
  }
  .why-card i{ color:var(--gold); font-size:1.4rem; margin-bottom:.75rem; display:block; }
  .why-card h3{ font-size:1rem; font-weight:600; margin-bottom:.4rem; }
  .why-card p{ color:rgba(255,255,255,.65); font-size:.88rem; margin:0; }

  /* ---------- FAQ ---------- */
  .faq-section{ padding:80px 0; }
  .accordion-item{ border:1px solid var(--line); border-radius:12px !important; margin-bottom:.75rem; overflow:hidden; }
  .accordion-button{ font-weight:600; font-family:'Fraunces',serif; }
  .accordion-button:not(.collapsed){ background:var(--ivory); color:var(--navy); box-shadow:none; }
  .accordion-button:focus{ box-shadow:none; }

  @media (max-width: 767px){
    .sell-hero h1{ font-size:1.9rem; }
    .sell-hero{ padding:60px 0 90px; }
  }
</style>
HTML;

require_once __DIR__ . '/includes/header.php';
// header.php is assumed to accept: $pageTitle, $extraHead, plus your existing
// nav / city-strip includes (same as on properties.php / hotels-homestays.php)
?>

<main class="sell-page">

  <!-- ================= HERO ================= -->
  <section class="sell-hero">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-6">
          <span class="sell-eyebrow"><i class="bi bi-shield-check"></i> Verified buyer network</span>
          <h1>Sell your property to <em>serious buyers</em>, not just browsers.</h1>
          <p class="lead">List free in under 5 minutes. Your property gets matched against buyer
            search filters the moment it goes live — city, budget, BHK, and move-in timeline.</p>

          <div class="sell-toggle" role="tablist" aria-label="Choose listing type">
            <button type="button" class="active" data-toggle="sell">Sell</button>
            <button type="button" data-toggle="rent">Rent</button>
          </div>

          <div class="sell-stats">
            <div><div class="num">42K+</div><div class="lbl">Verified buyers active</div></div>
            <div><div class="num">6 min</div><div class="lbl">Avg. time to list</div></div>
            <div><div class="num">0%</div><div class="lbl">Listing commission</div></div>
          </div>
        </div>

        <div class="col-lg-5 offset-lg-1">
          <div class="sell-hero-card">
            <h2 class="h5 mb-3" style="font-weight:600;">Start your listing</h2>
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label">Property type</label>
                <select class="form-select">
                  <option>Apartment</option>
                  <option>Villa</option>
                  <option>Independent House</option>
                  <option>Plot</option>
                  <option>Commercial Space</option>
                </select>
              </div>
              <div class="col-6">
                <label class="form-label">City</label>
                <input type="text" class="form-control" placeholder="e.g. Pune">
              </div>
              <div class="col-6">
                <label class="form-label">Pincode</label>
                <input type="text" class="form-control" placeholder="e.g. 411001">
              </div>
              <div class="col-12">
                <label class="form-label">Expected price</label>
                <input type="text" class="form-control" placeholder="₹">
              </div>
              <div class="col-12 d-grid mt-1">
                <a href="#list-your-property" class="btn btn-gold">
                  Continue <i class="bi bi-arrow-right ms-1"></i>
                </a>
              </div>
              <div class="col-12 text-center">
                <small style="color:var(--muted);">No login required to start</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ================= HOW IT WORKS ================= -->
  <section class="how-it-works">
    <div class="container">
      <div class="text-center mb-5">
        <div class="kicker">How it works</div>
        <h2 class="h3 mt-2">Four steps from listing to keys handed over</h2>
      </div>
      <div class="row g-4">
        <div class="col-md-3 col-6">
          <div class="step-card">
            <div class="step-num">01</div>
            <h3>Property details</h3>
            <p>Type, BHK, size, and location — the essentials buyers filter by first.</p>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="step-card">
            <div class="step-num">02</div>
            <h3>Photos & price</h3>
            <p>Upload up to 12 photos and set your expected price or price range.</p>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="step-card">
            <div class="step-num">03</div>
            <h3>Verification</h3>
            <p>Our team reviews your listing for accuracy — usually within 24 hours.</p>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="step-card">
            <div class="step-num">04</div>
            <h3>Go live</h3>
            <p>Your listing appears in search and gets matched to buyer alerts.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ================= LISTING FORM ================= -->
  <section class="list-form-section" id="list-your-property">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-9">

          <div class="text-center mb-5">
            <h2 class="h3">List your property</h2>
            <p style="color:var(--muted);">Fields marked * are required. You can edit your listing anytime after posting.</p>
          </div>

          <form class="form-shell" method="post" action="includes/submit-listing.php" enctype="multipart/form-data">

            <!-- Step 1: Basic details -->
            <div class="form-fieldset-title"><span class="badge-step">1</span> Basic details</div>
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label">Property type *</label>
                <select class="form-select" required>
                  <option value="">Select type</option>
                  <option>Apartment</option>
                  <option>Villa</option>
                  <option>Independent House</option>
                  <option>Penthouse</option>
                  <option>Plot</option>
                  <option>Farmhouse</option>
                  <option>Commercial Space</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">BHK type *</label>
                <div class="chip-group">
                  <?php foreach (['1 BHK','2 BHK','3 BHK','4 BHK','5+ BHK','Studio'] as $i => $bhk): ?>
                    <input type="radio" name="bhk" id="bhk<?= $i ?>" <?= $i===0 ? 'checked' : '' ?>>
                    <label for="bhk<?= $i ?>"><?= $bhk ?></label>
                  <?php endforeach; ?>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Built-up area (sq.ft) *</label>
                <input type="number" class="form-control" placeholder="e.g. 1250" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Age of property</label>
                <select class="form-select">
                  <option>Under construction</option>
                  <option>Ready to move — 0–1 yr</option>
                  <option>1–5 years</option>
                  <option>5–10 years</option>
                  <option>10+ years</option>
                </select>
              </div>
            </div>

            <!-- Step 2: Location -->
            <div class="form-fieldset-title"><span class="badge-step">2</span> Location</div>
            <div class="row g-3 mb-4">
              <div class="col-md-4">
                <label class="form-label">City *</label>
                <input type="text" class="form-control" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Locality *</label>
                <input type="text" class="form-control" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Pincode *</label>
                <input type="text" class="form-control" required>
              </div>
              <div class="col-12">
                <label class="form-label">Full address</label>
                <input type="text" class="form-control" placeholder="Society / building, street">
              </div>
            </div>

            <!-- Step 3: Photos -->
            <div class="form-fieldset-title"><span class="badge-step">3</span> Photos</div>
            <div class="mb-4">
              <div class="dropzone">
                <i class="bi bi-cloud-arrow-up d-block"></i>
                <p class="mb-1 mt-2" style="font-weight:600;color:var(--ink);">Drag & drop photos, or click to upload</p>
                <small>Up to 12 photos · JPG or PNG · 5MB max each</small>
                <input type="file" name="photos[]" multiple accept="image/*" class="form-control mt-3">
              </div>
            </div>

            <!-- Step 4: Pricing -->
            <div class="form-fieldset-title"><span class="badge-step">4</span> Pricing</div>
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label">Expected price *</label>
                <input type="text" class="form-control" placeholder="₹" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Price negotiable?</label>
                <select class="form-select">
                  <option>Yes</option>
                  <option>No</option>
                </select>
              </div>
              <div class="col-12">
                <label class="form-label">Description</label>
                <textarea class="form-control" rows="4" placeholder="What makes this property stand out?"></textarea>
              </div>
            </div>

            <!-- Step 5: Contact -->
            <div class="form-fieldset-title"><span class="badge-step">5</span> Your contact details</div>
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label">Full name *</label>
                <input type="text" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Mobile number *</label>
                <input type="tel" class="form-control" required>
              </div>
            </div>

            <div class="form-check mb-4">
              <input class="form-check-input" type="checkbox" id="agree" required>
              <label class="form-check-label" for="agree" style="font-size:.85rem;color:var(--muted);">
                I confirm the details above are accurate and I agree to be contacted by verified buyers.
              </label>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-gold btn-lg">Post my property — it's free</button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </section>

  <!-- ================= WHY SELL WITH US ================= -->
  <section class="why-us">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="h3">Why sell with EstateHub</h2>
      </div>
      <div class="row g-4">
        <div class="col-md-3 col-6">
          <div class="why-card">
            <i class="bi bi-graph-up-arrow"></i>
            <h3>Buyer-matched reach</h3>
            <p>Your listing is pushed to buyers whose saved search filters match it.</p>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="why-card">
            <i class="bi bi-patch-check"></i>
            <h3>Verified listing badge</h3>
            <p>Verified properties get up to 3x more enquiries than unverified ones.</p>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="why-card">
            <i class="bi bi-calendar2-check"></i>
            <h3>Site-visit scheduling</h3>
            <p>Buyers book visit slots directly — no back-and-forth calls needed.</p>
          </div>
        </div>
        <div class="col-md-3 col-6">
          <div class="why-card">
            <i class="bi bi-currency-rupee"></i>
            <h3>Zero commission</h3>
            <p>Listing and buyer enquiries are completely free for property owners.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ================= FAQ ================= -->
  <section class="faq-section">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="text-center mb-5">
            <h2 class="h3">Common questions</h2>
          </div>
          <div class="accordion" id="sellFaq">
            <div class="accordion-item">
              <h3 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                  Is listing my property really free?
                </button>
              </h3>
              <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#sellFaq">
                <div class="accordion-body" style="color:var(--muted);">
                  Yes. Basic listings are free with no commission on the eventual sale. Optional
                  featured placement is available if you want extra visibility.
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h3 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                  How long does verification take?
                </button>
              </h3>
              <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#sellFaq">
                <div class="accordion-body" style="color:var(--muted);">
                  Most listings are reviewed and go live within 24 hours of submission.
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h3 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                  Can I edit my listing after posting?
                </button>
              </h3>
              <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#sellFaq">
                <div class="accordion-body" style="color:var(--muted);">
                  Yes, you can update price, photos, and description anytime from your account dashboard.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

</main>

<script>
  // Sell / Rent toggle (purely visual — wire up to your form target as needed)
  document.querySelectorAll('.sell-toggle button').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.sell-toggle button').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
    });
  });
</script>

<!-- Remove this if footer.php already loads Bootstrap's JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>