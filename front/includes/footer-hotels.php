<?php
/* =========================================================================
   includes/footer.php
   -------------------------------------------------------------------------
   Shared footer shell for the Hotels & Home Stays page family
   (hotels-homestays.php, all-hotels-homestays.php, ...).

   Variables a page may set BEFORE requiring this file (all optional):

     $logoHref          string   Where the footer "Real Estate" logo links
                          to. Default: 'index.php'

     $footerTagline      string   Short line next to the footer logo.
                          Default: "World's easiest way to book verified
                          hostels and home stays."

     $footerCityItems    array    "Explore stays" links, each:
                            ['label' => 'Mumbai', 'url' => '...']
                          Default: [] (falls back to a single
                          "Browse all stays" link to all-hotels-homestays.php)

     $extraBodyHtml       string   Raw HTML appended after the Bootstrap
                          bundle script and before </body> - use this for
                          page-specific scripts (Leaflet JS, filter/wishlist/
                          map JS, etc.) built with ob_start()/ob_get_clean()
                          in the including page. Default: '' (none)
   ========================================================================= */

if (!function_exists('hh_e')) {
    function hh_e($value) {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

$logoHref        = $logoHref        ?? 'index.php';
$footerTagline   = $footerTagline   ?? "World's easiest way to book verified hostels and home stays.";
$footerCityItems = (isset($footerCityItems) && is_array($footerCityItems)) ? $footerCityItems : [];
$extraBodyHtml   = $extraBodyHtml   ?? '';
?>
<style>
  /* ============ Footer ============ */
  .hh-footer{ background:var(--navy-deep); color:#c7cede; margin-top:3rem; }
  .hh-footer .hh-footer-top{
    display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between;
    gap:1rem; padding:1.6rem 0; border-bottom:1px solid rgba(255,255,255,.1);
  }
  .hh-footer .hh-footer-top .hh-logo{ color:#fff; }
  .hh-footer-cols{ display:flex; flex-wrap:wrap; gap:2.5rem; padding:2rem 0; }
  .hh-footer-cols h6{ color:#fff; font-size:.8rem; text-transform:uppercase; letter-spacing:.05em; margin-bottom:.9rem; }
  .hh-footer-cols a{ display:block; color:#c7cede; text-decoration:none; font-size:.85rem; margin-bottom:.6rem; }
  .hh-footer-cols a:hover{ color:var(--gold); }
  .hh-store-btn{
    display:inline-flex; align-items:center; gap:.5rem;
    background:#0a101c; border:1px solid rgba(255,255,255,.15); border-radius:.5rem;
    padding:.5rem .9rem; color:#fff; text-decoration:none; font-size:.8rem; margin-right:.6rem;
  }
  .hh-social{ display:flex; gap:.7rem; }
  .hh-social a{
    width:34px; height:34px; border-radius:50%; border:1px solid rgba(255,255,255,.2);
    display:flex; align-items:center; justify-content:center; color:#fff; text-decoration:none;
  }
  .hh-social a:hover{ background:var(--gold); border-color:var(--gold); color:var(--navy); }
  .hh-footer-bottom{ border-top:1px solid rgba(255,255,255,.1); padding:1rem 0; font-size:.78rem; color:#8a93a3; }
</style>

<footer class="hh-footer">
  <div class="container">
    <div class="hh-footer-top">
      <a href="<?= hh_e($logoHref) ?>" class="hh-logo">Real Estate</a>
      <div style="color:#c7cede; font-size:.9rem;"><?= hh_e($footerTagline) ?></div>
      <a href="agents.php" class="hh-login-btn" style="background:var(--gold); color:var(--navy);">List your property</a>
    </div>

    <div class="hh-footer-cols">
      <div>
        <h6>Get the app</h6>
        <div class="d-flex flex-wrap gap-2">
          <a href="#" class="hh-store-btn"><i class="bi bi-apple"></i> App Store</a>
          <a href="#" class="hh-store-btn"><i class="bi bi-google-play"></i> Google Play</a>
        </div>
      </div>
      <div>
        <h6>Company</h6>
        <a href="#">About Us</a>
        <a href="#">Careers</a>
        <a href="agents.php">Agents</a>
      </div>
      <div>
        <h6>Support</h6>
        <a href="#">Help Center</a>
        <a href="#">Terms &amp; Conditions</a>
        <a href="#">Privacy Policy</a>
      </div>
      <div>
        <h6>Explore stays</h6>
        <?php if (!empty($footerCityItems)): ?>
          <?php foreach ($footerCityItems as $item): ?>
            <a href="<?= hh_e($item['url'] ?? '#') ?>">Stays in <?= hh_e($item['label'] ?? '') ?></a>
          <?php endforeach; ?>
        <?php else: ?>
          <a href="all-hotels-homestays.php">Browse all stays</a>
        <?php endif; ?>
      </div>
      <div>
        <h6>Follow us</h6>
        <div class="hh-social">
          <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
          <a href="#" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
        </div>
      </div>
    </div>

    <div class="hh-footer-bottom">
      &copy; <?= date('Y') ?> Real Estate. All rights reserved.
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?= $extraBodyHtml ?>
</body>
</html>