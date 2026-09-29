<?php
require_once __DIR__ . '/includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cookie Policy — <?php echo htmlspecialchars($site['name']); ?></title>

<!-- Favicon -->
<link rel="icon" type="image/png" href="<?php echo $site['favicon']; ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $site['favicon']; ?>">

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<!-- Fonts (same family set as the rest of the site) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">

<!-- Site styles -->
<link rel="stylesheet" href="assets/css/style.css">

<style>
/* ============================================================
   COOKIE POLICY PAGE — scoped under .tatito-cookies
   Shares the exact token system and dark/light mode behavior
   (body.dark-mode) used across the rest of the site.
   ============================================================ */

:root {
    --ta-ink: #12213a;
    --ta-muted: #6b7280;
    --ta-line: #e7e9ee;
    --ta-card: #ffffff;
    --ta-soft: #f8f9fa;
    --ta-gold: #c9a84c;
    --ta-gold-light: #e9c579;
    --ta-gold-dark: #8a6f3f;
    --ta-hero-bg: #f8f9fa;
    --ta-border: #e7e9ee;
    --ta-inset: #f2f2f4;
}

body.dark-mode {
    --ta-ink: #f0f6fc;
    --ta-muted: #8b949e;
    --ta-line: #30363d;
    --ta-card: #161b22;
    --ta-soft: #0d1117;
    --ta-hero-bg: #0d1117;
    --ta-border: #30363d;
    --ta-inset: #0d1117;
}

body.dark-mode .tatito-cookies { background: #0d1117; }

.tatito-cookies {
    font-family: 'Inter', sans-serif;
    color: var(--ta-ink);
    background: var(--ta-soft);
    transition: background-color 0.3s ease, color 0.3s ease;
}

.tatito-cookies * { box-sizing: border-box; }

.tatito-cookies h1,
.tatito-cookies h2,
.tatito-cookies h3,
.tatito-cookies h4 {
    font-family: 'Fraunces', serif;
    margin: 0;
    color: var(--ta-ink);
}

.tatito-cookies em { font-style: italic; color: var(--ta-gold); }
.tatito-cookies a { text-decoration: none; color: var(--ta-gold); }
.tatito-cookies a:hover { text-decoration: underline; }

/* ---------- Hero ---------- */
.tatito-cookies .ta-hero {
    padding: 56px 0 48px;
    border-bottom: 1px solid var(--ta-border);
    background: var(--ta-hero-bg);
    text-align: center;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.tatito-cookies .ta-hero-glyph {
    width: 56px;
    height: 56px;
    margin: 0 auto 22px;
    border-radius: 50%;
    background: var(--ta-card);
    border: 1px solid var(--ta-border);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--ta-gold);
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.tatito-cookies .ta-hero-glyph svg { width: 26px; height: 26px; }

.tatito-cookies .ta-hero h1 { font-size: 40px; font-weight: 600; }

.tatito-cookies .ta-hero .ta-rule {
    width: 60px;
    height: 1px;
    background: var(--ta-gold);
    margin: 18px auto 0;
}

/* ---------- Doc wrapper ---------- */
.tatito-cookies .ta-doc {
    max-width: 1000px;
    margin: 0 auto;
    padding: 48px 0 60px;
}

.tatito-cookies .ta-meta {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 12px;
    letter-spacing: 0.5px;
    color: var(--ta-muted);
    margin-bottom: 36px;
}

/* ---------- Section ---------- */
.tatito-cookies .ta-cp-section { margin-bottom: 44px; }

.tatito-cookies .ta-cp-section h2 {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 14px;
}

.tatito-cookies .ta-cp-section > p {
    font-size: 15px;
    line-height: 1.75;
    color: var(--ta-muted);
    margin-bottom: 18px;
    max-width: 780px;
}

/* ---------- Cookie type intro cards (session/persistent/third-party) ---------- */
.tatito-cookies .ta-intro-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.tatito-cookies .ta-intro-card {
    background: var(--ta-card);
    border: 1px solid var(--ta-border);
    border-radius: 14px;
    padding: 24px 20px;
    text-align: center;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.tatito-cookies .ta-intro-card .ta-icon {
    width: 40px;
    height: 40px;
    margin: 0 auto 14px;
    color: var(--ta-gold);
}

.tatito-cookies .ta-intro-card .ta-icon svg { width: 100%; height: 100%; }

.tatito-cookies .ta-intro-card h3 {
    font-size: 15px;
    font-weight: 600;
    margin-bottom: 8px;
    color: var(--ta-ink);
}

.tatito-cookies .ta-intro-card p {
    font-size: 13.5px;
    color: var(--ta-muted);
    margin: 0;
    line-height: 1.6;
}

@media (max-width: 760px) {
    .tatito-cookies .ta-intro-grid { grid-template-columns: 1fr; }
}

/* ---------- Cookie category cards (colour-coded) ---------- */
.tatito-cookies .ta-cat-card {
    background: var(--ta-card);
    border: 1px solid var(--ta-border);
    border-left-width: 4px;
    border-radius: 12px;
    padding: 26px 30px;
    margin-bottom: 20px;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.tatito-cookies .ta-cat-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 12px;
}

.tatito-cookies .ta-cat-head h3 {
    font-size: 19px;
    font-weight: 600;
    font-family: 'Fraunces', serif;
}

.tatito-cookies .ta-cat-badge {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 10.5px;
    letter-spacing: 0.6px;
    text-transform: uppercase;
    padding: 5px 14px;
    border-radius: 999px;
    background: rgba(46, 160, 67, 0.15);
    color: #2ea043;
    white-space: nowrap;
}

.tatito-cookies .ta-cat-card > p {
    font-size: 14.5px;
    line-height: 1.7;
    color: var(--ta-muted);
    margin-bottom: 16px;
    max-width: 760px;
}

.tatito-cookies .ta-cat-inset {
    background: var(--ta-inset);
    border-radius: 10px;
    padding: 18px 22px;
    transition: background-color 0.3s ease;
}

.tatito-cookies .ta-cat-inset .ta-inset-label {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 12px;
    letter-spacing: 0.4px;
    color: var(--ta-gold);
    margin-bottom: 10px;
    font-weight: 600;
}

.tatito-cookies .ta-cat-inset .ta-cols {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4px 24px;
}

.tatito-cookies .ta-cat-inset ul {
    margin: 0;
    padding: 0 0 0 18px;
    list-style: disc;
}

.tatito-cookies .ta-cat-inset ul li {
    font-size: 13.5px;
    color: var(--ta-muted);
    line-height: 1.7;
    margin-bottom: 4px;
}

@media (max-width: 640px) {
    .tatito-cookies .ta-cat-inset .ta-cols { grid-template-columns: 1fr; }
    .tatito-cookies .ta-cat-card { padding: 20px 20px; }
}

/* Category accent colors */
.tatito-cookies .ta-cat-essential { border-left-color: #2ea043; }
.tatito-cookies .ta-cat-essential .ta-cat-head h3 { color: #2ea043; }

.tatito-cookies .ta-cat-functional { border-left-color: #4c8bf5; }
.tatito-cookies .ta-cat-functional .ta-cat-head h3 { color: #4c8bf5; }

.tatito-cookies .ta-cat-analytics { border-left-color: #d9a521; }
.tatito-cookies .ta-cat-analytics .ta-cat-head h3 { color: #b8860b; }
body.dark-mode .tatito-cookies .ta-cat-analytics .ta-cat-head h3 { color: #e6b93d; }

.tatito-cookies .ta-cat-marketing { border-left-color: #9d6fe0; }
.tatito-cookies .ta-cat-marketing .ta-cat-head h3 { color: #9d6fe0; }

/* ---------- Browser management grid ---------- */
.tatito-cookies .ta-browser-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}

.tatito-cookies .ta-browser-card {
    background: var(--ta-card);
    border: 1px solid var(--ta-border);
    border-radius: 12px;
    padding: 20px 22px;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.tatito-cookies .ta-browser-card .ta-bhead {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 14px;
}

.tatito-cookies .ta-browser-card .ta-bnum {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: linear-gradient(160deg, var(--ta-gold-light), var(--ta-gold-dark));
    color: #1a1408;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Fraunces', serif;
    font-weight: 700;
    font-size: 12px;
    flex-shrink: 0;
}

.tatito-cookies .ta-browser-card h4 {
    font-size: 15px;
    font-weight: 600;
    color: var(--ta-ink);
}

.tatito-cookies .ta-browser-card ol {
    margin: 0;
    padding-left: 18px;
    color: var(--ta-muted);
}

.tatito-cookies .ta-browser-card ol li {
    font-size: 13px;
    line-height: 1.7;
    margin-bottom: 4px;
}

@media (max-width: 900px) {
    .tatito-cookies .ta-browser-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 500px) {
    .tatito-cookies .ta-browser-grid { grid-template-columns: 1fr; }
}

/* ---------- Contact section ---------- */
.tatito-cookies .ta-contact-card {
    background: var(--ta-card);
    border: 1px solid var(--ta-border);
    border-radius: 14px;
    padding: 24px 28px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.tatito-cookies .ta-contact-card p {
    font-size: 14px;
    color: var(--ta-muted);
    margin: 0 0 6px;
    line-height: 1.7;
}

.tatito-cookies .ta-contact-card p strong { color: var(--ta-ink); }

@media (max-width: 600px) {
    .tatito-cookies .ta-contact-card { grid-template-columns: 1fr; }
}

/* ---------- CTA ---------- */
.tatito-cookies .ta-cta {
    padding: 12px 0 60px;
    text-align: center;
}

.tatito-cookies .ta-buttons {
    display: flex;
    justify-content: center;
    gap: 16px;
    flex-wrap: wrap;
    margin-top: 8px;
}

.tatito-cookies .ta-btn {
    padding: 12px 28px;
    border-radius: 999px;
    font-size: 14px;
    letter-spacing: 0.3px;
    display: inline-block;
    transition: background 0.3s ease, color 0.3s ease, transform 0.2s ease, border-color 0.3s ease;
}

.tatito-cookies .ta-btn:hover { transform: translateY(-2px); text-decoration: none; }

.tatito-cookies .ta-btn.ta-gold {
    background: linear-gradient(160deg, #e9c579, #8a6f3f);
    color: #1a1408;
    font-weight: 600;
}

body.dark-mode .tatito-cookies .ta-btn.ta-gold {
    background: linear-gradient(160deg, #e9c579, #a8844a);
    color: #0d1117;
}

.tatito-cookies .ta-btn.ta-ghost {
    border: 1px solid var(--ta-gold);
    color: var(--ta-gold);
    background: transparent;
}

.tatito-cookies .ta-btn.ta-ghost:hover {
    background: var(--ta-gold);
    color: var(--ta-soft);
}

body.dark-mode .tatito-cookies .ta-btn.ta-ghost:hover { color: #0d1117; }

/* Responsive */
@media (max-width: 680px) {
    .tatito-cookies .ta-hero h1 { font-size: 30px; }
    .tatito-cookies .ta-cp-section h2 { font-size: 20px; }
}
</style>
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<!-- ============ COOKIE POLICY ============ -->
<div class="tatito-cookies">

  <!-- Hero -->
  <section class="ta-hero">
    <div class="container">
      <div class="ta-hero-glyph">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 2 A10 10 0 1 0 12 22 A5.5 5.5 0 0 1 17 15 A5 5 0 0 0 21.5 9.5 A10 10 0 0 0 12 2 Z"/><circle cx="9" cy="8.5" r="1" fill="currentColor" stroke="none"/><circle cx="8" cy="13" r="1" fill="currentColor" stroke="none"/><circle cx="12.5" cy="17" r="1" fill="currentColor" stroke="none"/><circle cx="14" cy="7" r="1" fill="currentColor" stroke="none"/></svg>
      </div>
      <h1>Cookie <em>Policy</em></h1>
      <div class="ta-rule"></div>
    </div>
  </section>

  <div class="container">
    <div class="ta-doc">

      <div class="ta-meta">Last Updated: March 2026 &nbsp;|&nbsp; Version 1.3</div>

      <!-- 1. What Are Cookies -->
      <div class="ta-cp-section">
        <h2>1. What Are Cookies?</h2>
        <p>Cookies are small text files that are placed on your computer or mobile device when you visit a website. They are widely used to make websites work more efficiently and provide useful information to the website owners.</p>

        <div class="ta-intro-grid">
          <div class="ta-intro-card">
            <div class="ta-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="4" y="3" width="16" height="18" rx="1.5"/><path d="M8 8 H16 M8 12 H16 M8 16 H12"/><path d="M15 17.5 L16.5 19 L19.5 15.5"/></svg></div>
            <h3>Session Cookies</h3>
            <p>Temporary cookies that expire when you close your browser</p>
          </div>
          <div class="ta-intro-card">
            <div class="ta-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3.5" y="8" width="17" height="12" rx="1"/><path d="M3.5 8 L12 3 L20.5 8"/><path d="M9 12 H15"/></svg></div>
            <h3>Persistent Cookies</h3>
            <p>Stay on your device until they expire or you delete them</p>
          </div>
          <div class="ta-intro-card">
            <div class="ta-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="8.5" cy="9" r="3"/><path d="M2.5 20 C2.5 15.5 5 13.5 8.5 13.5 C12 13.5 14.5 15.5 14.5 20"/><circle cx="17" cy="9.5" r="2.3"/><path d="M14.5 20 C14.5 16.3 16 14.8 17.8 14.5 C19.8 14.8 21.5 16.3 21.5 20"/></svg></div>
            <h3>Third-Party Cookies</h3>
            <p>Set by domains other than the one you're visiting</p>
          </div>
        </div>
      </div>

      <!-- 2. Types of Cookies We Use -->
      <div class="ta-cp-section">
        <h2>2. Types of Cookies We Use</h2>

        <div class="ta-cat-card ta-cat-essential">
          <div class="ta-cat-head">
            <h3>Essential Cookies</h3>
            <span class="ta-cat-badge">Always Active</span>
          </div>
          <p>These cookies are necessary for the website to function and cannot be switched off. They are usually only set in response to actions made by you such as setting your privacy preferences, logging in, or filling in forms.</p>
          <div class="ta-cat-inset">
            <div class="ta-inset-label">Examples:</div>
            <div class="ta-cols">
              <ul>
                <li>Session management and login authentication</li>
                <li>Remembering your cookie preferences</li>
              </ul>
              <ul>
                <li>Security and fraud prevention</li>
                <li>Property search session data</li>
              </ul>
            </div>
          </div>
        </div>

        <div class="ta-cat-card ta-cat-functional">
          <div class="ta-cat-head">
            <h3>Functional Cookies</h3>
          </div>
          <p>These cookies enable enhanced functionality and personalization. They may be set by us or by third-party providers whose services we have added to our pages.</p>
          <div class="ta-cat-inset">
            <div class="ta-inset-label">Examples:</div>
            <div class="ta-cols">
              <ul>
                <li>Remembering your preferred locations</li>
                <li>Storing recently viewed properties</li>
              </ul>
              <ul>
                <li>Saving your property filters</li>
                <li>Remembering your language preference</li>
              </ul>
            </div>
          </div>
        </div>

        <div class="ta-cat-card ta-cat-analytics">
          <div class="ta-cat-head">
            <h3>Analytics Cookies</h3>
          </div>
          <p>These cookies allow us to count visits and traffic sources so we can measure and improve the performance of our site. They help us know which pages are the most and least popular.</p>
          <div class="ta-cat-inset">
            <div class="ta-cols">
              <div>
                <div class="ta-inset-label">Tools Used:</div>
                <ul>
                  <li>Google Analytics</li>
                  <li>Mixpanel</li>
                  <li>Hotjar</li>
                </ul>
              </div>
              <div>
                <div class="ta-inset-label">Data Collected:</div>
                <ul>
                  <li>Page views and clicks</li>
                  <li>Time spent on site</li>
                  <li>Search behavior</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <div class="ta-cat-card ta-cat-marketing">
          <div class="ta-cat-head">
            <h3>Marketing Cookies</h3>
          </div>
          <p>These cookies may be set through our site by our advertising partners to build a profile of your interests and show you relevant ads on other sites.</p>
          <div class="ta-cat-inset">
            <div class="ta-inset-label">Advertising Partners:</div>
            <ul>
              <li>Google Ads</li>
              <li>Facebook Pixel</li>
              <li>LinkedIn Insight Tag</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- 3. How to Manage Cookies -->
      <div class="ta-cp-section">
        <h2>3. How to Manage Cookies</h2>

        <div class="ta-browser-grid">
          <div class="ta-browser-card">
            <div class="ta-bhead"><span class="ta-bnum">1</span><h4>Google Chrome</h4></div>
            <ol>
              <li>Click the three dots menu</li>
              <li>Go to Settings</li>
              <li>Click "Privacy and security"</li>
              <li>Select "Cookies and other site data"</li>
              <li>Choose your preferences</li>
            </ol>
          </div>
          <div class="ta-browser-card">
            <div class="ta-bhead"><span class="ta-bnum">2</span><h4>Mozilla Firefox</h4></div>
            <ol>
              <li>Click the menu button</li>
              <li>Select "Options"</li>
              <li>Go to "Privacy &amp; Security"</li>
              <li>Find "Cookies and Site Data"</li>
              <li>Adjust your settings</li>
            </ol>
          </div>
          <div class="ta-browser-card">
            <div class="ta-bhead"><span class="ta-bnum">3</span><h4>Safari</h4></div>
            <ol>
              <li>Click "Safari" in menu bar</li>
              <li>Select "Preferences"</li>
              <li>Click "Privacy" tab</li>
              <li>Manage cookie settings</li>
            </ol>
          </div>
          <div class="ta-browser-card">
            <div class="ta-bhead"><span class="ta-bnum">4</span><h4>Microsoft Edge</h4></div>
            <ol>
              <li>Click the three dots menu</li>
              <li>Go to Settings</li>
              <li>Click "Cookies and site permissions"</li>
              <li>Manage and delete cookies</li>
            </ol>
          </div>
        </div>
      </div>

      <!-- 4. Contact Us -->
      <div class="ta-cp-section">
        <h2>4. Contact Us</h2>
        <div class="ta-contact-card">
          <div>
            <p><strong>Email:</strong> <a href="mailto:tatitodevelopersandpromoters@gmail.com">tatitodevelopersandpromoters@gmail.com</a>,<br><a href="mailto:support@demo.tatitodevelopersandpromoters.com">support@demo.tatitodevelopersandpromoters.com</a></p>
            <p><strong>Phone:</strong> <a href="tel:+917093704706">+91 70937 04706</a></p>
          </div>
          <div>
            <p><strong>Address:</strong> 19-3-2/G11, Kanika Siddiah Colony, Tirupati, Andhra Pradesh - 517501</p>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- CTA -->
  <section class="ta-cta">
    <div class="container">
      <div class="ta-buttons">
        <a class="ta-btn ta-gold" href="sell-property.php">Enquire Now</a>
        <a class="ta-btn ta-ghost" href="properties.php">View Our Projects</a>
      </div>
    </div>
  </section>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
