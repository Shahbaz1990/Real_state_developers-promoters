<?php
require_once __DIR__ . '/includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Terms &amp; Conditions — <?php echo htmlspecialchars($site['name']); ?></title>

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
   TERMS & CONDITIONS PAGE — scoped under .tatito-terms
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
    --ta-warn-bg: #fbf5e6;
    --ta-warn-border: #e3cd8f;
}

body.dark-mode {
    --ta-ink: #f0f6fc;
    --ta-muted: #8b949e;
    --ta-line: #30363d;
    --ta-card: #161b22;
    --ta-soft: #0d1117;
    --ta-hero-bg: #0d1117;
    --ta-border: #30363d;
    --ta-warn-bg: #201a0c;
    --ta-warn-border: #5c4a20;
}

body.dark-mode .tatito-terms { background: #0d1117; }

.tatito-terms {
    font-family: 'Inter', sans-serif;
    color: var(--ta-ink);
    background: var(--ta-soft);
    transition: background-color 0.3s ease, color 0.3s ease;
}

.tatito-terms * { box-sizing: border-box; }

.tatito-terms h1,
.tatito-terms h2,
.tatito-terms h3,
.tatito-terms h4 {
    font-family: 'Fraunces', serif;
    margin: 0;
    color: var(--ta-ink);
}

.tatito-terms em { font-style: italic; color: var(--ta-gold); }
.tatito-terms a { text-decoration: none; color: var(--ta-gold); }
.tatito-terms a:hover { text-decoration: underline; }

/* ---------- Hero ---------- */
.tatito-terms .ta-hero {
    padding: 56px 0 48px;
    border-bottom: 1px solid var(--ta-border);
    background: var(--ta-hero-bg);
    text-align: center;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.tatito-terms .ta-hero-glyph {
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

.tatito-terms .ta-hero-glyph svg { width: 26px; height: 26px; }

.tatito-terms .ta-hero h1 {
    font-size: 40px;
    font-weight: 600;
}

.tatito-terms .ta-hero .ta-rule {
    width: 60px;
    height: 1px;
    background: var(--ta-gold);
    margin: 18px auto 0;
}

/* ---------- Card shell wrapping the whole document ---------- */
.tatito-terms .ta-doc {
    max-width: 980px;
    margin: 0 auto;
    padding: 48px 0 60px;
}

.tatito-terms .ta-meta {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 12px;
    letter-spacing: 0.5px;
    color: var(--ta-muted);
    margin-bottom: 36px;
}

/* ---------- Section ---------- */
.tatito-terms .ta-tc-section { margin-bottom: 40px; }

.tatito-terms .ta-tc-section h2 {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 14px;
}

.tatito-terms .ta-tc-section > p {
    font-size: 15px;
    line-height: 1.75;
    color: var(--ta-muted);
    margin-bottom: 18px;
    max-width: 760px;
}

/* ---------- Two-up subsection cards ---------- */
.tatito-terms .ta-subgrid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 18px;
}

.tatito-terms .ta-subcard {
    background: var(--ta-card);
    border: 1px solid var(--ta-border);
    border-radius: 14px;
    padding: 22px 26px;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.tatito-terms .ta-subcard.ta-full { grid-column: 1 / -1; }

.tatito-terms .ta-subcard h3 {
    font-size: 15px;
    font-weight: 600;
    font-family: 'IBM Plex Mono', monospace;
    letter-spacing: 0.3px;
    color: var(--ta-gold);
    margin-bottom: 10px;
}

.tatito-terms .ta-subcard p {
    font-size: 14px;
    line-height: 1.7;
    color: var(--ta-muted);
    margin: 0 0 10px;
}

.tatito-terms .ta-subcard p:last-child { margin-bottom: 0; }

.tatito-terms .ta-subcard ul {
    margin: 8px 0 0 18px;
    padding: 0;
    color: var(--ta-muted);
}

.tatito-terms .ta-subcard ul li {
    font-size: 14px;
    line-height: 1.7;
    margin-bottom: 6px;
}

.tatito-terms .ta-subcard .ta-list-cols {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4px 24px;
}

.tatito-terms .ta-subcard .ta-list-cols ul { margin-top: 0; }

@media (max-width: 760px) {
    .tatito-terms .ta-subgrid { grid-template-columns: 1fr; }
    .tatito-terms .ta-subcard .ta-list-cols { grid-template-columns: 1fr; }
}

/* ---------- Highlight variant (disclaimers, RERA) ---------- */
.tatito-terms .ta-subcard.ta-highlight {
    background: var(--ta-warn-bg);
    border-color: var(--ta-warn-border);
}

.tatito-terms .ta-subcard.ta-highlight h3 { color: var(--ta-gold-dark); }
body.dark-mode .tatito-terms .ta-subcard.ta-highlight h3 { color: var(--ta-gold-light); }

.tatito-terms .ta-subcard.ta-highlight p,
.tatito-terms .ta-subcard.ta-highlight ul li {
    color: var(--ta-ink);
    opacity: 0.85;
}

.tatito-terms .ta-subcard.ta-highlight p strong { color: var(--ta-ink); opacity: 1; }

/* ---------- Contact section ---------- */
.tatito-terms .ta-contact-card {
    background: var(--ta-card);
    border: 1px solid var(--ta-border);
    border-radius: 14px;
    padding: 24px 28px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.tatito-terms .ta-contact-card p {
    font-size: 14px;
    color: var(--ta-muted);
    margin: 0 0 6px;
    line-height: 1.7;
}

.tatito-terms .ta-contact-card p strong { color: var(--ta-ink); }

@media (max-width: 600px) {
    .tatito-terms .ta-contact-card { grid-template-columns: 1fr; }
}

/* ---------- Closing acknowledgment ---------- */
.tatito-terms .ta-ack {
    margin-top: 32px;
    text-align: center;
    border: 1px solid var(--ta-border);
    border-radius: 999px;
    padding: 16px 28px;
    font-size: 13.5px;
    color: var(--ta-muted);
    transition: border-color 0.3s ease;
}

/* ---------- CTA ---------- */
.tatito-terms .ta-cta {
    padding: 12px 0 60px;
    text-align: center;
}

.tatito-terms .ta-buttons {
    display: flex;
    justify-content: center;
    gap: 16px;
    flex-wrap: wrap;
    margin-top: 8px;
}

.tatito-terms .ta-btn {
    padding: 12px 28px;
    border-radius: 999px;
    font-size: 14px;
    letter-spacing: 0.3px;
    display: inline-block;
    transition: background 0.3s ease, color 0.3s ease, transform 0.2s ease, border-color 0.3s ease;
}

.tatito-terms .ta-btn:hover { transform: translateY(-2px); text-decoration: none; }

.tatito-terms .ta-btn.ta-gold {
    background: linear-gradient(160deg, #e9c579, #8a6f3f);
    color: #1a1408;
    font-weight: 600;
}

body.dark-mode .tatito-terms .ta-btn.ta-gold {
    background: linear-gradient(160deg, #e9c579, #a8844a);
    color: #0d1117;
}

.tatito-terms .ta-btn.ta-ghost {
    border: 1px solid var(--ta-gold);
    color: var(--ta-gold);
    background: transparent;
}

.tatito-terms .ta-btn.ta-ghost:hover {
    background: var(--ta-gold);
    color: var(--ta-soft);
}

body.dark-mode .tatito-terms .ta-btn.ta-ghost:hover { color: #0d1117; }

/* Responsive */
@media (max-width: 680px) {
    .tatito-terms .ta-hero h1 { font-size: 30px; }
    .tatito-terms .ta-tc-section h2 { font-size: 20px; }
    .tatito-terms .ta-subcard { padding: 18px 20px; }
}
</style>
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<!-- ============ TERMS & CONDITIONS ============ -->
<div class="tatito-terms">

  <!-- Hero -->
  <section class="ta-hero">
    <div class="container">
      <div class="ta-hero-glyph">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M6 3 H15 L19 7 V21 H6 Z"/><path d="M15 3 V7 H19"/><path d="M9 12 H15 M9 15.5 H15 M9 8.5 H12"/></svg>
      </div>
      <h1>Terms &amp; <em>Conditions</em></h1>
      <div class="ta-rule"></div>
    </div>
  </section>

  <div class="container">
    <div class="ta-doc">

      <div class="ta-meta">Last Updated: January 2024 &nbsp;|&nbsp; Version 3.2</div>

      <!-- 1. Acceptance of Terms -->
      <div class="ta-tc-section">
        <h2>1. Acceptance of Terms</h2>
        <p>By accessing or using our real estate platform, you agree to be bound by these Terms and Conditions. If you do not agree to all terms, please do not use our services.</p>

        <div class="ta-subgrid">
          <div class="ta-subcard">
            <h3>1.1 Eligibility</h3>
            <p>You must be at least 18 years old and legally capable of entering into contracts to use our services. By using our platform, you represent that you meet these requirements.</p>
          </div>
          <div class="ta-subcard">
            <h3>1.2 Updates to Terms</h3>
            <p>We may update these terms periodically. Continued use of the platform after changes constitutes acceptance of the modified terms.</p>
          </div>
        </div>
      </div>

      <!-- 2. Our Services -->
      <div class="ta-tc-section">
        <h2>2. Our Services</h2>
        <p>Our platform provides an online marketplace connecting property buyers, sellers, renters, and real estate professionals.</p>

        <div class="ta-subgrid">
          <div class="ta-subcard">
            <h3>2.1 Property Listings</h3>
            <p>We provide a platform for listing properties, but we are not a party to any transaction. We do not guarantee the accuracy of listings or the conduct of users.</p>
          </div>
          <div class="ta-subcard">
            <h3>2.2 User Accounts</h3>
            <p>You may need to create an account to access certain features. You are responsible for maintaining account security and all activities under your account.</p>
          </div>
        </div>
      </div>

      <!-- 3. User Obligations -->
      <div class="ta-tc-section">
        <h2>3. User Obligations</h2>

        <div class="ta-subgrid">
          <div class="ta-subcard ta-full">
            <h3>3.1 Accurate Information</h3>
            <p>You agree to provide accurate, current, and complete information when using our services.</p>
          </div>
          <div class="ta-subcard ta-full">
            <h3>3.2 Prohibited Conduct</h3>
            <p>You agree not to:</p>
            <div class="ta-list-cols">
              <ul>
                <li>Post false or misleading property listings</li>
                <li>Use the platform for illegal purposes</li>
                <li>Copy or reproduce our content without permission</li>
              </ul>
              <ul>
                <li>Harass, threaten, or harm other users</li>
                <li>Attempt to gain unauthorized access to our systems</li>
                <li>Engage in any fraudulent activity</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- 4. Property Listings -->
      <div class="ta-tc-section">
        <h2>4. Property Listings</h2>

        <div class="ta-subgrid">
          <div class="ta-subcard">
            <h3>4.1 Listing Requirements</h3>
            <p>When listing a property, you confirm that:</p>
            <ul>
              <li>You have the legal right to list the property</li>
              <li>All information provided is accurate and complete</li>
              <li>You have the authority to share property images</li>
              <li>The property complies with all applicable laws</li>
              <li>You will promptly update the listing if status changes</li>
            </ul>
          </div>
          <div class="ta-subcard">
            <h3>4.2 Listing Removal</h3>
            <p>We reserve the right to remove any listing that violates our terms or policies, including but not limited to:</p>
            <ul>
              <li>False or misleading information</li>
              <li>Duplicate or spam listings</li>
              <li>Copyright or trademark violations</li>
              <li>Multiple user complaints</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- 5. Disclaimers and Limitation of Liability -->
      <div class="ta-tc-section">
        <h2>5. Disclaimers and Limitation of Liability</h2>

        <div class="ta-subgrid">
          <div class="ta-subcard ta-highlight">
            <h3>5.1 No Warranty</h3>
            <p>Our services are provided "as is" without warranties of any kind. We do not guarantee that:</p>
            <ul>
              <li>The platform will be uninterrupted or error-free</li>
              <li>Listings are accurate, complete, or reliable</li>
              <li>Users will act lawfully or appropriately</li>
              <li>Property values or market conditions</li>
            </ul>
          </div>
          <div class="ta-subcard ta-highlight">
            <h3>5.2 Limitation of Liability</h3>
            <p>To the maximum extent permitted by law, we shall not be liable for any indirect, incidental, special, or consequential damages arising from your use of our services.</p>
            <p><strong>Maximum Liability:</strong> Our total liability to you shall not exceed the amount you paid to us in the past 12 months.</p>
          </div>
        </div>
      </div>

      <!-- 6. RERA Compliance -->
      <div class="ta-tc-section">
        <h2>6. RERA Compliance</h2>
        <div class="ta-subgrid">
          <div class="ta-subcard ta-highlight ta-full">
            <p style="margin:0;">Our platform complies with the Real Estate (Regulation and Development) Act, 2016. All registered projects on our platform display their RERA registration numbers. Users are advised to verify project details with the respective RERA authority before making any purchase decisions.</p>
          </div>
        </div>
      </div>

      <!-- 7. Contact Information -->
      <div class="ta-tc-section">
        <h2>7. Contact Information</h2>
        <div class="ta-contact-card">
          <div>
            <p><strong>Email:</strong> <a href="mailto:support@demo.tatitodevelopersandpromoters.com">support@demo.tatitodevelopersandpromoters.com</a></p>
            <p><strong>Phone:</strong> <a href="tel:+917093704706">+91 70937 04706</a></p>
          </div>
          <div>
            <p><strong>Address:</strong> 19-3-2/G11, Kanika Siddiah Colony, Tirupati, Andhra Pradesh - 517501</p>
            <p><strong>Hours:</strong> Monday to Friday, 9:00 AM to 6:00 PM IST</p>
          </div>
        </div>
      </div>

      <div class="ta-ack">By using our platform, you acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions.</div>

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
