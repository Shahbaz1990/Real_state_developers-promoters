<?php
require_once __DIR__ . '/includes/config.php';

/* ============================================================
   CONTACT FORM HANDLING
   Inserts submissions into the `contact_submissions` table.
   Responds with JSON when called via fetch() (AJAX),
   falls back to a normal redirect/flag for non-JS submissions.
============================================================ */

$formSuccess = false;
$formError   = '';
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {

    $firstName  = trim($_POST['first_name'] ?? '');
    $lastName   = trim($_POST['last_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $address    = trim($_POST['address'] ?? '');
    $lookingFor = trim($_POST['looking_for'] ?? '');
    $project    = trim($_POST['project'] ?? '');
    $message    = trim($_POST['message'] ?? '');

    if ($firstName === '' || $lastName === '' || $email === '' || $phone === '') {
        $formError = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $formError = 'Please enter a valid email address.';
    } else {
        $stmt = $conn->prepare("
            INSERT INTO contact_submissions
                (first_name, last_name, email, phone, address, looking_for, project, message)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            'ssssssss',
            $firstName, $lastName, $email, $phone, $address, $lookingFor, $project, $message
        );

        if ($stmt->execute()) {
            $formSuccess = true;
        } else {
            $formError = 'Something went wrong. Please try again later.';
        }
        $stmt->close();
    }

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $formSuccess,
            'error'   => $formError,
        ]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us — <?php echo htmlspecialchars($site['name']); ?></title>

<!-- Favicon -->
<link rel="icon" type="image/png" href="<?php echo $site['favicon']; ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $site['favicon']; ?>">

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">

<!-- Site styles -->
<link rel="stylesheet" href="assets/css/style.css">

<style>
/* ============================================================
   CONTACT US PAGE — scoped under .tatito-contact
   Supports both light and dark modes using body.dark-mode
   ============================================================ */

/* Light mode (default) variables */
:root {
    --tc-ink: #12213a;
    --tc-muted: #6b7280;
    --tc-line: #e7e9ee;
    --tc-card: #ffffff;
    --tc-soft: #f8f9fa;
    --tc-gold: #c9a84c;
    --tc-gold-light: #e9c579;
    --tc-gold-dark: #8a6f3f;
    --tc-bg: #f8f9fa;
    --tc-border: #e7e9ee;
    --tc-input-bg: #ffffff;
    --tc-input-border: #d1d5db;
    --tc-shadow: rgba(18, 33, 58, 0.08);
}

/* Dark mode variables */
body.dark-mode {
    --tc-ink: #f0f6fc;
    --tc-muted: #8b949e;
    --tc-line: #30363d;
    --tc-card: #161b22;
    --tc-soft: #0d1117;
    --tc-bg: #0d1117;
    --tc-border: #30363d;
    --tc-input-bg: #1c2333;
    --tc-input-border: #30363d;
    --tc-shadow: rgba(0, 0, 0, 0.3);
}

.tatito-contact {
    font-family: 'Inter', sans-serif;
    color: var(--tc-ink);
    background: var(--tc-bg);
    transition: background-color 0.3s ease, color 0.3s ease;
}

.tatito-contact * { box-sizing: border-box; }

.tatito-contact h1,
.tatito-contact h2,
.tatito-contact h3,
.tatito-contact h4 {
    font-family: 'Fraunces', serif;
    margin: 0;
    color: var(--tc-ink);
}

.tatito-contact em {
    font-style: italic;
    color: var(--tc-gold);
}

.tatito-contact .tc-label {
    font-family: 'IBM Plex Mono', monospace;
}

.tatito-contact a { text-decoration: none; }
.tatito-contact img { max-width: 100%; display: block; }

/* ---------- Hero Section ---------- */
.tatito-contact .tc-hero {
    padding: 50px 0 60px;
    border-bottom: 1px solid var(--tc-border);
    background: var(--tc-bg);
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.tatito-contact .tc-hero .tc-hero-content {
    text-align: center;
    max-width: 700px;
    margin: 0 auto;
}

.tatito-contact .tc-hero .tc-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 12px;
    letter-spacing: 2px;
    color: var(--tc-gold);
    margin-bottom: 16px;
}

.tatito-contact .tc-hero .tc-eyebrow::before,
.tatito-contact .tc-hero .tc-eyebrow::after {
    content: "";
    width: 30px;
    height: 1px;
    background: var(--tc-gold);
}

.tatito-contact .tc-hero h1 {
    font-size: 42px;
    line-height: 1.2;
    font-weight: 700;
    margin-bottom: 12px;
}

.tatito-contact .tc-hero p.tc-lead {
    color: var(--tc-muted);
    font-size: 16px;
    line-height: 1.7;
    max-width: 540px;
    margin: 0 auto;
}

/* ---------- Main Contact Section - Two Columns ---------- */
.tatito-contact .tc-main {
    padding: 40px 0 70px;
}

.tatito-contact .tc-main-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 48px;
    align-items: start;
}

/* Left Side - Contact Info */
.tatito-contact .tc-left-side {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.tatito-contact .tc-left-card {
    background: var(--tc-card);
    border: 1px solid var(--tc-border);
    border-radius: 16px;
    padding: 28px 24px;
    transition: background 0.3s ease, border-color 0.3s ease;
}

.tatito-contact .tc-left-card .tc-card-title {
    font-size: 18px;
    font-weight: 700;
    font-family: 'Inter', sans-serif;
    margin-bottom: 16px;
    color: var(--tc-ink);
}

.tatito-contact .tc-left-card .tc-card-title i {
    color: var(--tc-gold);
    margin-right: 8px;
}

.tatito-contact .tc-left-card .tc-contact-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 10px 0;
    border-bottom: 1px solid var(--tc-border);
    transition: border-color 0.3s ease;
}

.tatito-contact .tc-left-card .tc-contact-item:last-child {
    border-bottom: none;
}

.tatito-contact .tc-left-card .tc-contact-item .tc-item-icon {
    width: 36px;
    height: 36px;
    min-width: 36px;
    border-radius: 50%;
    background: rgba(201, 168, 76, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--tc-gold);
    font-size: 16px;
    margin-top: 2px;
}

.tatito-contact .tc-left-card .tc-contact-item .tc-item-content {
    flex: 1;
}

.tatito-contact .tc-left-card .tc-contact-item .tc-item-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--tc-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.tatito-contact .tc-left-card .tc-contact-item .tc-item-value {
    font-size: 14px;
    color: var(--tc-ink);
    font-weight: 500;
}

.tatito-contact .tc-left-card .tc-contact-item .tc-item-value a {
    color: var(--tc-ink);
    transition: color 0.3s ease;
}

.tatito-contact .tc-left-card .tc-contact-item .tc-item-value a:hover {
    color: var(--tc-gold);
}

/* Open in Maps - with embedded map */
.tatito-contact .tc-open-maps {
    padding: 0;
    overflow: hidden;
}

.tatito-contact .tc-open-maps .tc-card-title {
    padding: 20px 24px 0 24px;
}

.tatito-contact .tc-open-maps .tc-map-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    margin: 12px 16px 16px 16px;
    border-radius: 10px;
    border: 1px solid var(--tc-border);
    background: var(--tc-soft);
    color: var(--tc-ink);
    transition: all 0.3s ease;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
}

.tatito-contact .tc-open-maps .tc-map-btn:hover {
    border-color: var(--tc-gold);
    color: var(--tc-gold);
    background: rgba(201, 168, 76, 0.05);
}

.tatito-contact .tc-open-maps .tc-map-btn i {
    color: var(--tc-gold);
}

.tatito-contact .tc-embedded-map {
    width: 100%;
    height: 300px;
    border: none;
    display: block;
    border-top: 1px solid var(--tc-border);
    margin-top: 12px;
}

/* Form Styles - Right Side */
.tatito-contact .tc-form-card {
    background: var(--tc-card);
    border: 1px solid var(--tc-border);
    border-radius: 16px;
    padding: 40px 44px;
    transition: background 0.3s ease, border-color 0.3s ease;
}

.tatito-contact .tc-form-card .tc-form-title {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 4px;
}

.tatito-contact .tc-form-card .tc-form-subtitle {
    color: var(--tc-muted);
    font-size: 14px;
    margin-bottom: 24px;
}

.tatito-contact .tc-form-group {
    margin-bottom: 18px;
}

.tatito-contact .tc-form-group label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--tc-ink);
    margin-bottom: 5px;
}

.tatito-contact .tc-form-group .tc-form-control {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid var(--tc-input-border);
    border-radius: 10px;
    background: var(--tc-input-bg);
    color: var(--tc-ink);
    font-size: 14px;
    transition: all 0.3s ease;
}

.tatito-contact .tc-form-group .tc-form-control:focus {
    outline: none;
    border-color: var(--tc-gold);
    box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.15);
}

.tatito-contact .tc-form-group .tc-form-control::placeholder {
    color: var(--tc-muted);
}

.tatito-contact .tc-form-group .tc-form-control.tc-textarea {
    resize: vertical;
    min-height: 100px;
}

.tatito-contact .tc-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.tatito-contact .tc-submit-btn {
    width: 100%;
    padding: 12px 28px;
    border: none;
    border-radius: 999px;
    background: linear-gradient(160deg, #e9c579, #8a6f3f);
    color: #1a1408;
    font-weight: 700;
    font-size: 16px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.tatito-contact .tc-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(201, 168, 76, 0.3);
}

.tatito-contact .tc-form-alert {
    padding: 12px 16px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 18px;
}

.tatito-contact .tc-form-alert.tc-alert-success {
    background: rgba(67, 233, 123, 0.12);
    color: #1e7a44;
    border: 1px solid rgba(67, 233, 123, 0.35);
}

.tatito-contact .tc-form-alert.tc-alert-error {
    background: rgba(220, 53, 69, 0.1);
    color: #b02a37;
    border: 1px solid rgba(220, 53, 69, 0.3);
}

body.dark-mode .tatito-contact .tc-form-alert.tc-alert-success {
    color: #7ee2a8;
}

body.dark-mode .tatito-contact .tc-form-alert.tc-alert-error {
    color: #f4a3ab;
}

/* Responsive */
@media (max-width: 992px) {
    .tatito-contact .tc-main-grid {
        grid-template-columns: 1fr;
        gap: 32px;
    }
    
    .tatito-contact .tc-hero h1 {
        font-size: 34px;
    }
}

@media (max-width: 768px) {
    .tatito-contact .tc-form-card {
        padding: 28px 20px;
    }
    
    .tatito-contact .tc-form-row {
        grid-template-columns: 1fr;
    }
    
    .tatito-contact .tc-hero h1 {
        font-size: 28px;
    }
    
    .tatito-contact .tc-open-maps .tc-embedded-map {
        height: 220px;
    }
}

@media (max-width: 480px) {
    .tatito-contact .tc-hero {
        padding: 30px 0 40px;
    }
    
    .tatito-contact .tc-hero h1 {
        font-size: 24px;
    }
    
    .tatito-contact .tc-form-card {
        padding: 20px 16px;
    }
    
    .tatito-contact .tc-left-card {
        padding: 20px 16px;
    }
    
    .tatito-contact .tc-open-maps .tc-embedded-map {
        height: 200px;
    }
    
    .tatito-contact .tc-open-maps .tc-map-btn {
        margin: 8px 12px 12px 12px;
        padding: 10px 12px;
        font-size: 13px;
    }
}

/* Dark mode specific overrides */
body.dark-mode .tatito-contact .tc-form-card {
    background: #161b22;
    border-color: #30363d;
}

body.dark-mode .tatito-contact .tc-left-card {
    background: #161b22;
    border-color: #30363d;
}

body.dark-mode .tatito-contact .tc-submit-btn {
    background: linear-gradient(160deg, #e9c579, #a8844a);
    color: #0d1117;
}

body.dark-mode .tatito-contact .tc-open-maps .tc-map-btn {
    background: #0d1117;
    border-color: #30363d;
}

body.dark-mode .tatito-contact .tc-open-maps .tc-map-btn:hover {
    border-color: var(--tc-gold);
    color: var(--tc-gold);
}

body.dark-mode .tatito-contact .tc-embedded-map {
    border-color: #30363d;
}
</style>
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<!-- ============ CONTACT US ============ -->
<div class="tatito-contact">

    <!-- Hero Section -->
    <section class="tc-hero">
        <div class="container">
            <div class="tc-hero-content">
                <div class="tc-eyebrow">RESERVATION &amp; CONSULTATION</div>
                <h1>Secure Your <em>Legacy</em></h1>
                <p class="tc-lead">Our consultants are available 24/7 for high-value portfolio consultation and direct architectural reviews.</p>
            </div>
        </div>
    </section>

    <!-- Main Contact Section - Two Columns: Get in Touch (Left) | Send Enquiry (Right) -->
    <section class="tc-main">
        <div class="container">
            <div class="tc-main-grid">

                <!-- Left Side - Contact Info + Open in Maps with embedded map -->
                <div class="tc-left-side">
                    
                    <!-- Contact Details -->
                    <div class="tc-left-card">
                        <div class="tc-card-title"><i class="bi bi-info-circle"></i> Get in Touch</div>
                        
                        <div class="tc-contact-item">
                            <div class="tc-item-icon"><i class="bi bi-geo-alt"></i></div>
                            <div class="tc-item-content">
                                <div class="tc-item-label">Location</div>
                                <div class="tc-item-value">19-3-2/G11, KANIKA SIDDAIAH COLONY, TIRUPATI, ANDHRA PRADESH 517501</div>
                            </div>
                        </div>

                        <div class="tc-contact-item">
                            <div class="tc-item-icon"><i class="bi bi-envelope"></i></div>
                            <div class="tc-item-content">
                                <div class="tc-item-label">Email</div>
                                <div class="tc-item-value">
                                    <a href="mailto:tatitodeveloper@andpromotera.com">tatitodeveloper@andpromotera.com</a><br>
                                    <a href="mailto:support@demo.tatitodeveloperandpromotera.com">support@demo.tatitodeveloperandpromotera.com</a>
                                </div>
                            </div>
                        </div>

                        <div class="tc-contact-item">
                            <div class="tc-item-icon"><i class="bi bi-telephone"></i></div>
                            <div class="tc-item-content">
                                <div class="tc-item-label">Phone</div>
                                <div class="tc-item-value"><a href="tel:+917093704706">+91 70937 04706</a></div>
                            </div>
                        </div>

                        <div class="tc-contact-item">
                            <div class="tc-item-icon"><i class="bi bi-whatsapp"></i></div>
                            <div class="tc-item-content">
                                <div class="tc-item-label">WhatsApp</div>
                                <div class="tc-item-value"><a href="https://wa.me/917093704706" target="_blank">+91 70937 04706</a></div>
                            </div>
                        </div>
                    </div>

                    <!-- Open in Maps with embedded map -->
                    <div class="tc-left-card tc-open-maps">
                        <div class="tc-card-title"><i class="bi bi-map"></i> Open in Maps</div>
                        
                        <!-- Google Maps Embed -->
                        <iframe 
                            class="tc-embedded-map"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31108.43994722388!2d79.39760439254618!3d13.62874521603404!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a4d4b8b8b8b8b8b%3A0x8b8b8b8b8b8b8b8b!2sTirupati%2C%20Andhra%20Pradesh!5e0!3m2!1sen!2sin!4v1700000000000" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                        
                        <a href="https://www.google.com/maps?q=19-3-2/G11+KANIKA+SIDDAIAH+COLONY+TIRUPATI+ANDHRA+PRADESH+517501" target="_blank" class="tc-map-btn">
                            <i class="bi bi-box-arrow-up-right"></i> Open in Google Maps
                        </a>
                    </div>

                </div>

                <!-- Right Side - Form -->
                <div class="tc-form-card">
                    <h2 class="tc-form-title">Send Enquiry</h2>
                    <p class="tc-form-subtitle">Fill in the details below and our team will reach out to you shortly.</p>

                    <?php if (!$isAjax && $formSuccess): ?>
                    <div class="tc-form-alert tc-alert-success">Thank you! Your enquiry has been sent successfully.</div>
                    <?php elseif (!$isAjax && $formError !== ''): ?>
                    <div class="tc-form-alert tc-alert-error"><?= htmlspecialchars($formError) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="" id="contactForm">
                        <input type="hidden" name="contact_submit" value="1">

                        <div class="tc-form-row">
                            <div class="tc-form-group">
                                <label for="firstName">First Name</label>
                                <input type="text" id="firstName" name="first_name" class="tc-form-control" placeholder="John" required>
                            </div>
                            <div class="tc-form-group">
                                <label for="lastName">Last Name</label>
                                <input type="text" id="lastName" name="last_name" class="tc-form-control" placeholder="Doe" required>
                            </div>
                        </div>

                        <div class="tc-form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" class="tc-form-control" placeholder="you@example.com" required>
                        </div>

                        <div class="tc-form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="tc-form-control" placeholder="+91 98765 43210" required>
                        </div>

                        <div class="tc-form-group">
                            <label for="address">Residential Address / Pincode</label>
                            <input type="text" id="address" name="address" class="tc-form-control" placeholder="Your full address or pincode">
                        </div>

                        <div class="tc-form-row">
                            <div class="tc-form-group">
                                <label for="lookingFor">What are you looking for?</label>
                                <select id="lookingFor" name="looking_for" class="tc-form-control">
                                    <option value="">Select an option</option>
                                    <option value="open-plots">Open Plots</option>
                                    <option value="farm-lands">Farm Lands</option>
                                    <option value="royal-villas">Royal Villas</option>
                                    <option value="interiors">Interiors</option>
                                    <option value="investment">Investment Consultation</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="tc-form-group">
                                <label for="project">Target Project Name</label>
                                <input type="text" id="project" name="project" class="tc-form-control" placeholder="Project name">
                            </div>
                        </div>

                        <div class="tc-form-group">
                            <label for="message">Describe Your Enquiry</label>
                            <textarea id="message" name="message" class="tc-form-control tc-textarea" placeholder="Tell us about your requirements..." rows="4"></textarea>
                        </div>

                        <button type="submit" class="tc-submit-btn">Send Enquiry</button>
                    </form>
                </div>

            </div>
        </div>
    </section>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ===== CONTACT FORM HANDLING (submits to this same page via fetch) =====
(function() {
    const form = document.getElementById('contactForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Basic validation
        const firstName = form.elements['first_name'].value.trim();
        const lastName = form.elements['last_name'].value.trim();
        const email = form.elements['email'].value.trim();
        const phone = form.elements['phone'].value.trim();

        if (!firstName || !lastName || !email || !phone) {
            alert('Please fill in all required fields.');
            return;
        }

        const submitBtn = form.querySelector('.tc-submit-btn');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Sending...';
        submitBtn.disabled = true;

        fetch(window.location.href, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(form)
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) {
                submitBtn.textContent = '✓ Sent Successfully!';
                submitBtn.style.background = 'linear-gradient(160deg, #43e97b, #38f9d7)';
                form.reset();

                setTimeout(function() {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                    submitBtn.style.background = '';
                }, 3000);
            } else {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                alert(data.error || 'Something went wrong. Please try again.');
            }
        })
        .catch(function() {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
            alert('Something went wrong. Please try again.');
        });
    });
})();

// ===== DARK MODE SYNC =====
(function() {
    function syncDarkMode() {
        // CSS handles everything via body.dark-mode selector
    }

    document.addEventListener('themeChanged', function(e) {
        syncDarkMode();
    });

    syncDarkMode();

    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                syncDarkMode();
            }
        });
    });
    observer.observe(document.body, { attributes: true });
})();
</script>

</body>
</html>