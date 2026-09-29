<?php
require_once __DIR__ . '/includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Privacy Policy — <?php echo htmlspecialchars($site['name']); ?></title>

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
   PRIVACY POLICY PAGE — scoped under .tatito-privacy
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
}

body.dark-mode {
    --ta-ink: #f0f6fc;
    --ta-muted: #8b949e;
    --ta-line: #30363d;
    --ta-card: #161b22;
    --ta-soft: #0d1117;
    --ta-hero-bg: #0d1117;
    --ta-border: #30363d;
}

body.dark-mode .tatito-privacy { background: #0d1117; }

.tatito-privacy {
    font-family: 'Inter', sans-serif;
    color: var(--ta-ink);
    background: var(--ta-soft);
    transition: background-color 0.3s ease, color 0.3s ease;
}

.tatito-privacy * { box-sizing: border-box; }

.tatito-privacy h1,
.tatito-privacy h2,
.tatito-privacy h3,
.tatito-privacy h4 {
    font-family: 'Fraunces', serif;
    margin: 0;
    color: var(--ta-ink);
}

.tatito-privacy em { font-style: italic; color: var(--ta-gold); }
.tatito-privacy .ta-label { font-family: 'IBM Plex Mono', monospace; }
.tatito-privacy a { text-decoration: none; color: var(--ta-gold); }
.tatito-privacy a:hover { text-decoration: underline; }

/* ---------- Hero ---------- */
.tatito-privacy .ta-hero {
    padding: 64px 0 56px;
    border-bottom: 1px solid var(--ta-border);
    background: var(--ta-hero-bg);
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.tatito-privacy .ta-eyebrow {
    display: flex;
    align-items: center;
    gap: 10px;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 12px;
    letter-spacing: 2px;
    color: var(--ta-gold);
    margin-bottom: 16px;
}

.tatito-privacy .ta-eyebrow::before {
    content: "";
    width: 20px;
    height: 2px;
    background: var(--ta-gold);
}

.tatito-privacy .ta-hero h1 {
    font-size: 40px;
    line-height: 1.15;
    font-weight: 600;
    max-width: 600px;
}

.tatito-privacy .ta-hero-meta {
    margin-top: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 12px 28px;
}

.tatito-privacy .ta-hero-meta span {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 12px;
    letter-spacing: 0.5px;
    color: var(--ta-muted);
}

.tatito-privacy .ta-hero-meta span strong {
    color: var(--ta-ink);
    font-weight: 600;
}

/* ---------- Section shell ---------- */
.tatito-privacy .ta-section {
    padding: 56px 0;
    border-bottom: 1px solid var(--ta-border);
    background: var(--ta-soft);
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.tatito-privacy .ta-section:last-of-type { border-bottom: none; }

.tatito-privacy .ta-section h2 {
    font-size: 26px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 8px;
}

.tatito-privacy .ta-section h2 .ta-num {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: linear-gradient(160deg, var(--ta-gold-light), var(--ta-gold-dark));
    color: #1a1408;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Fraunces', serif;
    font-weight: 700;
    font-size: 15px;
    flex-shrink: 0;
}

.tatito-privacy .ta-section-rule {
    width: 60px;
    height: 1px;
    background: var(--ta-gold);
    margin: 14px 0 28px 52px;
}

.tatito-privacy .ta-section h3 {
    font-size: 15px;
    font-weight: 600;
    font-family: 'IBM Plex Mono', monospace;
    letter-spacing: 0.4px;
    text-transform: none;
    color: var(--ta-gold);
    margin: 24px 0 12px;
}

.tatito-privacy .ta-section p {
    font-size: 15px;
    line-height: 1.75;
    color: var(--ta-muted);
    margin-bottom: 12px;
    max-width: 720px;
}

.tatito-privacy .ta-section ul {
    margin: 0 0 18px 20px;
    color: var(--ta-muted);
    max-width: 720px;
}

.tatito-privacy .ta-section ul li {
    margin-bottom: 6px;
    font-size: 14.5px;
    line-height: 1.7;
}

.tatito-privacy .ta-section ul li strong { color: var(--ta-ink); }

/* ---------- Use-of-information cards (mirrors ta-pillars) ---------- */
.tatito-privacy .ta-use-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1px;
    background: var(--ta-border);
    border: 1px solid var(--ta-border);
    border-radius: 12px;
    overflow: hidden;
    margin-top: 8px;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.tatito-privacy .ta-use-card {
    background: var(--ta-card);
    padding: 26px 28px;
    transition: background-color 0.3s ease;
}

.tatito-privacy .ta-use-card h4 {
    font-size: 15px;
    font-weight: 600;
    margin-bottom: 10px;
    color: var(--ta-ink);
}

.tatito-privacy .ta-use-card ul {
    list-style: none;
    margin: 0;
    padding: 0;
}

.tatito-privacy .ta-use-card ul li {
    font-size: 13.5px;
    color: var(--ta-muted);
    padding-left: 18px;
    position: relative;
    margin-bottom: 8px;
}

.tatito-privacy .ta-use-card ul li::before {
    content: "";
    position: absolute;
    left: 0;
    top: 7px;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--ta-gold);
}

@media (max-width: 700px) {
    .tatito-privacy .ta-use-grid { grid-template-columns: 1fr; }
}

/* ---------- Sharing steps ---------- */
.tatito-privacy .ta-sharing-steps {
    display: flex;
    flex-direction: column;
    gap: 14px;
    margin: 18px 0 20px;
    max-width: 760px;
}

.tatito-privacy .ta-sharing-step {
    display: flex;
    align-items: flex-start;
    gap: 18px;
    background: var(--ta-card);
    border: 1px solid var(--ta-border);
    padding: 18px 22px;
    border-radius: 12px;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.tatito-privacy .ta-sharing-step .ta-step-num {
    font-family: 'Fraunces', serif;
    font-weight: 700;
    color: var(--ta-gold);
    font-size: 20px;
    min-width: 26px;
}

.tatito-privacy .ta-sharing-step .ta-step-text {
    font-size: 14.5px;
    color: var(--ta-muted);
    line-height: 1.65;
}

.tatito-privacy .ta-sharing-step .ta-step-text strong { color: var(--ta-ink); }

.tatito-privacy .ta-note {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: var(--ta-card);
    border: 1px dashed var(--ta-gold);
    padding: 12px 22px;
    border-radius: 999px;
    color: var(--ta-muted);
    font-size: 13.5px;
    margin-top: 6px;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.tatito-privacy .ta-note i { color: var(--ta-gold); }

/* ---------- Security ---------- */
.tatito-privacy .ta-security-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-top: 8px;
    max-width: 780px;
}

.tatito-privacy .ta-security-block {
    background: var(--ta-card);
    border: 1px solid var(--ta-border);
    border-radius: 14px;
    padding: 22px 24px;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.tatito-privacy .ta-security-block h4 {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 12px;
    color: var(--ta-ink);
}

.tatito-privacy .ta-security-block .ta-sec-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13.5px;
    color: var(--ta-muted);
    margin-bottom: 8px;
}

.tatito-privacy .ta-security-block .ta-sec-item i { color: var(--ta-gold); width: 16px; }

@media (max-width: 700px) {
    .tatito-privacy .ta-security-grid { grid-template-columns: 1fr; }
}

/* ---------- Rights grid ---------- */
.tatito-privacy .ta-rights-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-top: 8px;
    max-width: 820px;
}

.tatito-privacy .ta-right-item {
    background: var(--ta-card);
    border: 1px solid var(--ta-border);
    border-radius: 14px;
    padding: 20px 16px;
    text-align: center;
    transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
}

.tatito-privacy .ta-right-item:hover { transform: translateY(-3px); border-color: var(--ta-gold); }

.tatito-privacy .ta-right-item .ta-right-icon {
    width: 30px;
    height: 30px;
    margin: 0 auto 10px;
    color: var(--ta-gold);
}

.tatito-privacy .ta-right-item .ta-right-icon svg { width: 100%; height: 100%; }

.tatito-privacy .ta-right-item .ta-right-label {
    font-weight: 600;
    font-size: 14px;
    color: var(--ta-ink);
    margin-bottom: 4px;
}

.tatito-privacy .ta-right-item .ta-right-sub {
    font-size: 12.5px;
    color: var(--ta-muted);
}

@media (max-width: 700px) {
    .tatito-privacy .ta-rights-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 460px) {
    .tatito-privacy .ta-rights-grid { grid-template-columns: 1fr; }
}

/* ---------- Contact ---------- */
.tatito-privacy .ta-contact-box {
    background: var(--ta-card);
    border: 1px solid var(--ta-border);
    border-radius: 16px;
    padding: 28px 32px;
    margin-top: 8px;
    max-width: 620px;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.tatito-privacy .ta-contact-box .ta-role {
    display: inline-block;
    margin-bottom: 14px;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 10.5px;
    letter-spacing: 1px;
    color: var(--ta-gold);
    border: 1px solid var(--ta-gold);
    border-radius: 999px;
    padding: 6px 16px;
}

.tatito-privacy .ta-contact-box h3 {
    font-family: 'Fraunces', serif;
    font-size: 20px;
    font-weight: 600;
    font-style: italic;
    margin-bottom: 4px;
    color: var(--ta-ink);
}

.tatito-privacy .ta-contact-box p {
    font-size: 14px;
    color: var(--ta-muted);
    margin-bottom: 6px;
}

.tatito-privacy .ta-contact-box p strong { color: var(--ta-ink); }
.tatito-privacy .ta-contact-box hr {
    border: none;
    border-top: 1px solid var(--ta-border);
    margin: 16px 0;
}

/* ---------- Closing note / CTA ---------- */
.tatito-privacy .ta-cta {
    padding: 48px 0 64px;
    text-align: center;
    background: var(--ta-soft);
    border-bottom: none;
    transition: background-color 0.3s ease;
}

.tatito-privacy .ta-cta p {
    color: var(--ta-muted);
    max-width: 560px;
    margin: 0 auto 24px;
    font-size: 13.5px;
    line-height: 1.7;
}

.tatito-privacy .ta-buttons {
    display: flex;
    justify-content: center;
    gap: 16px;
    flex-wrap: wrap;
}

.tatito-privacy .ta-btn {
    padding: 12px 28px;
    border-radius: 999px;
    font-size: 14px;
    letter-spacing: 0.3px;
    display: inline-block;
    transition: background 0.3s ease, color 0.3s ease, transform 0.2s ease, border-color 0.3s ease;
}

.tatito-privacy .ta-btn:hover { transform: translateY(-2px); text-decoration: none; }

.tatito-privacy .ta-btn.ta-gold {
    background: linear-gradient(160deg, #e9c579, #8a6f3f);
    color: #1a1408;
    font-weight: 600;
}

body.dark-mode .tatito-privacy .ta-btn.ta-gold {
    background: linear-gradient(160deg, #e9c579, #a8844a);
    color: #0d1117;
}

.tatito-privacy .ta-btn.ta-ghost {
    border: 1px solid var(--ta-gold);
    color: var(--ta-gold);
    background: transparent;
}

.tatito-privacy .ta-btn.ta-ghost:hover {
    background: var(--ta-gold);
    color: var(--ta-soft);
}

body.dark-mode .tatito-privacy .ta-btn.ta-ghost:hover { color: #0d1117; }

/* Responsive tweaks */
@media (max-width: 680px) {
    .tatito-privacy .ta-hero h1 { font-size: 30px; }
    .tatito-privacy .ta-section h2 { font-size: 21px; }
    .tatito-privacy .ta-section-rule { margin-left: 0; }
    .tatito-privacy .ta-contact-box { padding: 22px 20px; }
}
</style>
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<!-- ============ PRIVACY POLICY ============ -->
<div class="tatito-privacy">

  <!-- Hero -->
  <section class="ta-hero">
    <div class="container">
      <div class="ta-eyebrow">Legal</div>
      <h1>Privacy <em>Policy</em></h1>
      <div class="ta-hero-meta">
        <span>Version <strong>2.1</strong></span>
        <span>Last Updated <strong>January 2024</strong></span>
        <span>Effective <strong>January 1, 2024</strong></span>
      </div>
    </div>
  </section>

  <!-- 1. Information We Collect -->
  <section class="ta-section">
    <div class="container">
      <h2><span class="ta-num">1</span> Information We Collect</h2>
      <div class="ta-section-rule"></div>

      <h3>Personal Information You Provide</h3>
      <p>When you use our real estate platform, we collect information you voluntarily provide.</p>

      <h3>Account Information</h3>
      <ul>
        <li>Full name and email address</li>
        <li>Phone number and mobile number</li>
        <li>Postal address and property location preferences</li>
        <li>Password and security details</li>
      </ul>

      <h3>Property Related Information</h3>
      <ul>
        <li>Property search history and saved searches</li>
        <li>Favorite properties and saved listings</li>
        <li>Property inquiries and messages</li>
        <li>Scheduled property visits</li>
      </ul>

      <h3>Financial Information</h3>
      <ul>
        <li>Budget and price range preferences</li>
        <li>Loan eligibility information</li>
        <li>Income details (for loan pre-qualification)</li>
        <li>Payment history for premium services</li>
      </ul>

      <h3>Verification Documents</h3>
      <ul>
        <li>Government ID proofs (PAN, Aadhaar, Passport)</li>
        <li>Property ownership documents (for sellers)</li>
        <li>RERA registration details (for developers)</li>
      </ul>

      <h3>Automatically Collected Information</h3>
      <ul>
        <li><strong>Device Information:</strong> IP address, browser type, operating system, device identifiers</li>
        <li><strong>Usage Data:</strong> Pages visited, time spent, clicks, property views, search queries</li>
        <li><strong>Location Data:</strong> GPS coordinates, IP-based location, preferred property locations</li>
      </ul>
    </div>
  </section>

  <!-- 2. How We Use Your Information -->
  <section class="ta-section">
    <div class="container">
      <h2><span class="ta-num">2</span> How We Use Your Information</h2>
      <div class="ta-section-rule"></div>

      <div class="ta-use-grid">
        <div class="ta-use-card">
          <h4>Service Delivery</h4>
          <ul>
            <li>Provide property listings and matching services</li>
            <li>Connect buyers with sellers and agents</li>
            <li>Process property inquiries and requests</li>
          </ul>
        </div>
        <div class="ta-use-card">
          <h4>Personalization</h4>
          <ul>
            <li>Recommend properties based on preferences</li>
            <li>Save search filters and favorite listings</li>
            <li>Customize property alerts and notifications</li>
          </ul>
        </div>
        <div class="ta-use-card">
          <h4>Communication</h4>
          <ul>
            <li>Send property updates and new listings</li>
            <li>Respond to inquiries and support requests</li>
            <li>Share market insights and real estate news</li>
          </ul>
        </div>
        <div class="ta-use-card">
          <h4>Improvement</h4>
          <ul>
            <li>Analyze user behavior to improve the platform</li>
            <li>Develop new features and services</li>
            <li>Enhance property matching algorithms</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <!-- 3. Information Sharing -->
  <section class="ta-section">
    <div class="container">
      <h2><span class="ta-num">3</span> Information Sharing</h2>
      <div class="ta-section-rule"></div>

      <div class="ta-sharing-steps">
        <div class="ta-sharing-step">
          <span class="ta-step-num">1</span>
          <span class="ta-step-text"><strong>With Property Owners &amp; Agents:</strong> When you express interest in a property, we share your contact information with the listing owner or agent to facilitate communication.</span>
        </div>
        <div class="ta-sharing-step">
          <span class="ta-step-num">2</span>
          <span class="ta-step-text"><strong>With Service Providers:</strong> We share necessary information with banks, legal advisors, and home loan providers to assist with your property transaction.</span>
        </div>
        <div class="ta-sharing-step">
          <span class="ta-step-num">3</span>
          <span class="ta-step-text"><strong>Legal Compliance:</strong> When required by law, court order, or government authorities, we may disclose information to comply with legal obligations.</span>
        </div>
      </div>

      <div class="ta-note">
        <i class="bi bi-shield-check"></i>
        We never sell your personal information to third parties for marketing purposes without your explicit consent.
      </div>
    </div>
  </section>

  <!-- 4. Data Security -->
  <section class="ta-section">
    <div class="container">
      <h2><span class="ta-num">4</span> Data Security</h2>
      <div class="ta-section-rule"></div>

      <div class="ta-security-grid">
        <div class="ta-security-block">
          <h4>Security Measures</h4>
          <div class="ta-sec-item"><i class="bi bi-lock"></i> 256-bit SSL encryption</div>
          <div class="ta-sec-item"><i class="bi bi-key"></i> Two-factor authentication</div>
          <div class="ta-sec-item"><i class="bi bi-shield-check"></i> Regular security audits</div>
          <div class="ta-sec-item"><i class="bi bi-hdd-network"></i> Secure data centers</div>
        </div>
        <div class="ta-security-block">
          <h4>Your Responsibility</h4>
          <div class="ta-sec-item"><i class="bi bi-exclamation-triangle"></i> Keep login credentials secure</div>
          <div class="ta-sec-item"><i class="bi bi-key"></i> Use strong passwords</div>
          <div class="ta-sec-item"><i class="bi bi-box-arrow-right"></i> Log out from shared devices</div>
          <div class="ta-sec-item"><i class="bi bi-flag"></i> Report suspicious activity</div>
        </div>
      </div>
    </div>
  </section>

  <!-- 5. Your Privacy Rights -->
  <section class="ta-section">
    <div class="container">
      <h2><span class="ta-num">5</span> Your Privacy Rights</h2>
      <div class="ta-section-rule"></div>

      <div class="ta-rights-grid">
        <div class="ta-right-item">
          <div class="ta-right-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 12 C4.5 6 8 4 12 4 C16 4 19.5 6 22 12 C19.5 18 16 20 12 20 C8 20 4.5 18 2 12 Z"/><circle cx="12" cy="12" r="3.2"/></svg></div>
          <div class="ta-right-label">Right to Access</div>
          <div class="ta-right-sub">View all personal data we hold</div>
        </div>
        <div class="ta-right-item">
          <div class="ta-right-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 20 L4.6 16.5 L15.5 5.6 A2 2 0 0 1 18.4 5.6 L18.4 5.6 A2 2 0 0 1 18.4 8.5 L7.5 19.4 Z"/></svg></div>
          <div class="ta-right-label">Right to Rectify</div>
          <div class="ta-right-sub">Correct inaccurate information</div>
        </div>
        <div class="ta-right-item">
          <div class="ta-right-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 7 H19 M9 7 V4.5 A1.5 1.5 0 0 1 10.5 3 H13.5 A1.5 1.5 0 0 1 15 4.5 V7 M7 7 L8 20 A1.5 1.5 0 0 0 9.5 21.3 H14.5 A1.5 1.5 0 0 0 16 20 L17 7"/></svg></div>
          <div class="ta-right-label">Right to Delete</div>
          <div class="ta-right-sub">Request data deletion</div>
        </div>
        <div class="ta-right-item">
          <div class="ta-right-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="9"/><path d="M9.5 9 V15 M14.5 9 V15"/></svg></div>
          <div class="ta-right-label">Right to Restrict</div>
          <div class="ta-right-sub">Limit data processing</div>
        </div>
        <div class="ta-right-item">
          <div class="ta-right-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 3 V15 M12 3 L8 7 M12 3 L16 7"/><path d="M4 15 V19 A2 2 0 0 0 6 21 H18 A2 2 0 0 0 20 19 V15"/></svg></div>
          <div class="ta-right-label">Data Portability</div>
          <div class="ta-right-sub">Receive data in a common format</div>
        </div>
        <div class="ta-right-item">
          <div class="ta-right-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="9"/><path d="M6.5 6.5 L17.5 17.5"/></svg></div>
          <div class="ta-right-label">Right to Object</div>
          <div class="ta-right-sub">Opt out of marketing</div>
        </div>
      </div>
    </div>
  </section>

  <!-- 6. Contact Us -->
  <section class="ta-section">
    <div class="container">
      <h2><span class="ta-num">6</span> Contact Us</h2>
      <div class="ta-section-rule"></div>

      <div class="ta-contact-box">
        <span class="ta-role">Privacy Grievance Officer</span>
        <h3>Manoj Kumar Vanam</h3>
        <p><strong>Designation:</strong> Founder and CEO of Tatito Projects</p>
        <p><strong>Email:</strong> <a href="mailto:Tatitodevelopersandpromoters@gmail.com">Tatitodevelopersandpromoters@gmail.com</a></p>
        <p><strong>Phone:</strong> <a href="tel:+917093704706">+91 70937 04706</a></p>
        <p><strong>Response Time:</strong> Estimated 48–72 hours</p>
        <hr>
        <p><strong>Office Address</strong></p>
        <p>Tatito Projects<br>
        19-3-2/G11, Kanika Siddiah Colony<br>
        Tirupati, Andhra Pradesh - 517501<br>
        Andhra Pradesh, India</p>
      </div>
    </div>
  </section>

  <!-- Closing note -->
  <section class="ta-cta">
    <div class="container">
      <p>By using our platform, you acknowledge that you have read and understood this privacy policy. We may update this policy periodically, and we will notify you of any material changes via email or website notice.</p>
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