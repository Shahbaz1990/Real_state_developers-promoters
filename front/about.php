<?php
require_once __DIR__ . '/includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About Us — <?php echo htmlspecialchars($site['name']); ?></title>

<!-- Favicon -->
<link rel="icon" type="image/png" href="<?php echo $site['favicon']; ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $site['favicon']; ?>">

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<!-- Fonts (same family set as the homepage) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">

<!-- Site styles -->
<link rel="stylesheet" href="assets/css/style.css">

<style>
/* ============================================================
   ABOUT US PAGE — scoped under .tatito-about
   Now properly supports both light and dark modes using body.dark-mode
   ============================================================ */

/* Light mode (default) variables */
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
    --ta-dark-bg: #0d1117;
    --ta-dark-card: #161b22;
    --ta-dark-border: #30363d;
}

/* Dark mode variables */
body.dark-mode {
    --ta-ink: #f0f6fc;
    --ta-muted: #8b949e;
    --ta-line: #30363d;
    --ta-card: #161b22;
    --ta-soft: #0d1117;
    --ta-hero-bg: #0d1117;
    --ta-border: #30363d;
}

body.dark-mode .tatito-about {
    background: #0d1117;
}

.tatito-about {
    font-family: 'Inter', sans-serif;
    color: var(--ta-ink);
    background: var(--ta-soft);
    transition: background-color 0.3s ease, color 0.3s ease;
}

.tatito-about * { box-sizing: border-box; }

.tatito-about h1,
.tatito-about h2,
.tatito-about h3,
.tatito-about h4 {
    font-family: 'Fraunces', serif;
    margin: 0;
    color: var(--ta-ink);
}

.tatito-about em {
    font-style: italic;
    color: var(--ta-gold);
}

.tatito-about .ta-label {
    font-family: 'IBM Plex Mono', monospace;
}

.tatito-about a { text-decoration: none; }
.tatito-about img { max-width: 100%; display: block; }

/* ---------- Hero ---------- */
.tatito-about .ta-hero {
    padding: 64px 0 72px;
    border-bottom: 1px solid var(--ta-border);
    background: var(--ta-hero-bg);
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.tatito-about .ta-hero .ta-grid {
    display: grid;
    grid-template-columns: 0.85fr 1.15fr;
    gap: 56px;
    align-items: center;
}

/* Hero visual card - dark by default, adapts in light mode */
.tatito-about .ta-hero-visual {
    border-radius: 20px;
    padding: 40px 30px;
    text-align: center;
    background: linear-gradient(160deg, #1c1712, #100d0a);
    border: 1px solid rgba(201, 168, 76, 0.35);
    transition: background 0.3s ease, border-color 0.3s ease;
}

body.dark-mode .tatito-about .ta-hero-visual {
    background: linear-gradient(160deg, #1c1712, #100d0a);
    border-color: rgba(201, 168, 76, 0.5);
}

.tatito-about .ta-hero-visual .ta-glyph { 
    margin: 0 auto 20px; 
    width: 88px; 
    height: 88px; 
}

.tatito-about .ta-hero-visual .ta-glyph svg { 
    width: 100%; 
    height: 100%; 
}

.tatito-about .ta-hero-visual .ta-word {
    font-family: 'Fraunces', serif;
    font-size: 28px;
    letter-spacing: 3px;
    color: #f3ecdf;
}

.tatito-about .ta-hero-visual .ta-sub {
    display: block;
    margin-top: 6px;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 11px;
    letter-spacing: 2.5px;
    color: #e9c579;
}

.tatito-about .ta-hero-visual .ta-tag {
    margin-top: 16px;
    font-size: 13.5px;
    color: #b9ac96;
    font-style: italic;
}

.tatito-about .ta-hero-visual .ta-badge {
    margin: 26px auto 0;
    display: inline-flex;
    align-items: center;
    gap: 14px;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 999px;
    padding: 8px 20px 8px 8px;
}

.tatito-about .ta-hero-visual .ta-num {
    width: 42px; 
    height: 42px;
    border-radius: 50%;
    background: linear-gradient(160deg, #e9c579, #8a6f3f);
    color: #1a1408;
    display: flex; 
    align-items: center; 
    justify-content: center;
    font-family: 'Fraunces', serif;
    font-weight: 700;
    font-size: 15px;
}

.tatito-about .ta-hero-visual .ta-num-label {
    text-align: left;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 10px;
    letter-spacing: 0.8px;
    color: #b9ac96;
    text-transform: uppercase;
    line-height: 1.4;
}

.tatito-about .ta-eyebrow {
    display: flex; 
    align-items: center; 
    gap: 10px;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 12px;
    letter-spacing: 2px;
    color: var(--ta-gold);
    margin-bottom: 16px;
}

.tatito-about .ta-eyebrow::before {
    content: "";
    width: 20px; 
    height: 2px;
    background: var(--ta-gold);
}

.tatito-about .ta-hero h1 {
    font-size: 42px;
    line-height: 1.2;
    font-weight: 600;
    max-width: 560px;
}

.tatito-about .ta-hero p.ta-lead {
    margin-top: 20px;
    max-width: 540px;
    color: var(--ta-muted);
    font-size: 16px;
    line-height: 1.7;
}

.tatito-about .ta-fact-grid {
    margin-top: 36px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px 36px;
}

.tatito-about .ta-fact {
    border-top: 1px solid var(--ta-border);
    padding-top: 14px;
    transition: border-color 0.3s ease;
}

.tatito-about .ta-fact .ta-fh {
    display: flex; 
    align-items: center; 
    gap: 9px;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 12px;
    letter-spacing: 0.8px;
    color: var(--ta-gold);
    margin-bottom: 7px;
}

.tatito-about .ta-fact .ta-fh svg { 
    width: 15px; 
    height: 15px; 
    flex-shrink: 0; 
}

.tatito-about .ta-fact p { 
    margin: 0; 
    font-size: 14px; 
    color: var(--ta-muted); 
}

@media (max-width: 900px) {
    .tatito-about .ta-hero .ta-grid { 
        grid-template-columns: 1fr; 
        gap: 40px; 
    }
    .tatito-about .ta-hero h1 { 
        font-size: 32px; 
    }
}

/* ---------- Section shell ---------- */
.tatito-about .ta-section { 
    padding: 76px 0; 
    border-bottom: 1px solid var(--ta-border);
    background: var(--ta-soft);
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.tatito-about .ta-section-head { 
    text-align: center; 
    max-width: 620px; 
    margin: 0 auto 48px; 
}

.tatito-about .ta-section-head h2 { 
    font-size: 30px; 
    font-weight: 600; 
}

.tatito-about .ta-section-head .ta-rule { 
    width: 60px; 
    height: 1px; 
    background: var(--ta-gold); 
    margin: 18px auto 0; 
}

/* ---------- Pillars ---------- */
.tatito-about .ta-pillars {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1px;
    background: var(--ta-border);
    border: 1px solid var(--ta-border);
    border-radius: 12px;
    overflow: hidden;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.tatito-about .ta-pillar { 
    background: var(--ta-card); 
    padding: 30px 26px;
    transition: background-color 0.3s ease;
}

.tatito-about .ta-pillar .ta-icon { 
    width: 28px; 
    height: 28px; 
    margin-bottom: 18px; 
    color: var(--ta-gold); 
}

.tatito-about .ta-pillar .ta-icon svg { 
    width: 100%; 
    height: 100%; 
}

.tatito-about .ta-pillar h3 { 
    font-size: 17px; 
    font-weight: 600; 
    margin-bottom: 8px; 
}

.tatito-about .ta-pillar p { 
    font-size: 13.5px; 
    color: var(--ta-muted); 
    margin: 0; 
    line-height: 1.6; 
}

@media (max-width: 900px) { 
    .tatito-about .ta-pillars { 
        grid-template-columns: repeat(2, 1fr); 
    } 
}

@media (max-width: 560px) { 
    .tatito-about .ta-pillars { 
        grid-template-columns: 1fr; 
    } 
}

/* ---------- Leadership ---------- */
.tatito-about .ta-leaders {
    display: grid; 
    grid-template-columns: repeat(2, 1fr);
    gap: 44px; 
    max-width: 760px; 
    margin: 0 auto;
}

.tatito-about .ta-leader { 
    text-align: center; 
}

.tatito-about .ta-leader .ta-frame {
    width: 100%; 
    aspect-ratio: 1 / 1.05;
    border-radius: 16px;
    border: 1px solid var(--ta-border);
    background: var(--ta-card);
    display: flex; 
    align-items: center; 
    justify-content: center;
    margin-bottom: 20px; 
    overflow: hidden;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.tatito-about .ta-leader .ta-frame img { 
    width: 100%; 
    height: 100%; 
    object-fit: cover; 
}

.tatito-about .ta-leader .ta-frame .ta-mono-icon { 
    width: 60px; 
    height: 60px; 
    color: var(--ta-muted); 
}

.tatito-about .ta-leader .ta-frame .ta-mono-icon svg { 
    width: 100%; 
    height: 100%; 
}

.tatito-about .ta-leader h3 { 
    font-size: 20px; 
    font-style: italic; 
    font-weight: 600; 
}

.tatito-about .ta-leader .ta-role {
    display: inline-block; 
    margin: 12px 0 14px;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 10.5px; 
    letter-spacing: 1px;
    color: var(--ta-gold);
    border: 1px solid var(--ta-gold);
    border-radius: 999px; 
    padding: 6px 16px;
    transition: border-color 0.3s ease;
}

.tatito-about .ta-leader p.ta-bio { 
    font-size: 14px; 
    color: var(--ta-muted); 
    max-width: 300px; 
    margin: 0 auto; 
    line-height: 1.6; 
}

@media (max-width: 700px) { 
    .tatito-about .ta-leaders { 
        grid-template-columns: 1fr; 
    } 
}

/* ---------- Team Slider ---------- */
.ta-team-slider-wrapper {
    position: relative;
    overflow: hidden;
    padding: 0 0 20px;
}

.ta-team-slider {
    display: flex;
    gap: 24px;
    transition: transform 0.5s ease;
    will-change: transform;
}

.ta-team-slider .ta-team-card {
    flex: 0 0 calc(20% - 20px);
    min-width: 200px;
    border: 1px solid var(--ta-border);
    border-radius: 16px;
    overflow: hidden;
    background: var(--ta-card);
    transition: border-color 0.3s ease, transform 0.2s ease, background-color 0.3s ease;
    text-align: center;
}

.ta-team-slider .ta-team-card:hover { 
    border-color: var(--ta-gold); 
    transform: translateY(-4px); 
}

.ta-team-slider .ta-team-card .ta-avatar {
    aspect-ratio: 1 / 1.1;
    background: var(--ta-soft);
    display: flex; 
    align-items: center; 
    justify-content: center;
    overflow: hidden;
    transition: background-color 0.3s ease;
}

.ta-team-slider .ta-team-card .ta-avatar img { 
    width: 100%; 
    height: 100%; 
    object-fit: cover; 
}

.ta-team-slider .ta-team-card .ta-avatar svg { 
    width: 50px; 
    height: 50px; 
    color: var(--ta-muted); 
}

.ta-team-slider .ta-team-card .ta-info { 
    padding: 16px 12px 20px; 
}

.ta-team-slider .ta-team-card .ta-info h4 { 
    font-size: 15px; 
    font-weight: 700; 
}

.ta-team-slider .ta-team-card .ta-info .ta-role {
    display: block; 
    margin-top: 4px;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 11px; 
    letter-spacing: 0.4px;
    color: var(--ta-muted);
}

/* Slider Controls */
.ta-slider-controls {
    display: flex;
    justify-content: center;
    gap: 16px;
    margin-top: 24px;
}

.ta-slider-btn {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    border: 2px solid var(--ta-gold);
    background: transparent;
    color: var(--ta-gold);
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.ta-slider-btn:hover {
    background: var(--ta-gold);
    color: var(--ta-soft);
}

.ta-slider-btn:disabled {
    opacity: 0.3;
    cursor: not-allowed;
    transform: none;
}

.ta-slider-btn:disabled:hover {
    background: transparent;
    color: var(--ta-gold);
}

.ta-slider-dots {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 16px;
}

.ta-slider-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: none;
    background: var(--ta-border);
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 0;
}

.ta-slider-dot.active {
    background: var(--ta-gold);
    transform: scale(1.2);
}

/* Dark mode team slider */
body.dark-mode .ta-team-slider .ta-team-card {
    background: #161b22;
    border-color: #30363d;
}

body.dark-mode .ta-team-slider .ta-team-card .ta-avatar {
    background: #0d1117;
}

/* ---------- Projects Showcase Slider (EXACT MATCH FROM SCREENSHOT) ---------- */
.ta-projects-showcase {
    background: #0a101c !important;
    padding: 60px 0 80px !important;
    border-bottom: none !important;
}

.ta-projects-slider-wrapper {
    position: relative;
    overflow: hidden;
    padding: 10px 0 30px;
}

.ta-projects-slider {
    display: flex;
    gap: 30px;
    transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    will-change: transform;
}

.ta-project-card {
    flex: 0 0 calc(33.333% - 20px);
    min-width: 280px;
    background: transparent !important;
    border: none !important;
    border-radius: 0 !important;
    overflow: hidden;
    transition: all 0.3s ease;
    text-align: left;
}

.ta-project-card:hover {
    transform: translateY(-6px);
}

.ta-project-card .ta-project-image {
    position: relative;
    aspect-ratio: 16 / 10;
    overflow: hidden;
    border-radius: 12px;
    background: #1a2332;
    margin-bottom: 16px;
}

.ta-project-card .ta-project-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.ta-project-card:hover .ta-project-image img {
    transform: scale(1.05);
}

.ta-project-card .ta-project-badge {
    position: absolute;
    top: 14px;
    left: 14px;
    color: #ffffff;
    font-weight: 700;
    font-size: 13px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    text-shadow: 0 2px 8px rgba(0,0,0,0.5);
}

.ta-project-card .ta-project-body {
    padding: 0 4px;
}

.ta-project-card .ta-project-body h3 {
    font-family: 'Fraunces', serif;
    font-size: 22px;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 4px;
    letter-spacing: 0.5px;
}

.ta-project-card .ta-project-body .ta-project-location {
    color: rgba(255,255,255,0.6);
    font-size: 14px;
    margin-bottom: 8px;
    font-weight: 400;
}

.ta-project-card .ta-project-body .ta-project-location i {
    margin-right: 4px;
    color: var(--ta-gold);
}

.ta-project-card .ta-project-body .ta-project-details {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 12px;
}

.ta-project-card .ta-project-body .ta-project-details span {
    font-size: 13px;
    color: rgba(255,255,255,0.6);
    font-weight: 400;
}

.ta-project-card .ta-project-body .ta-project-details span strong {
    color: #ffffff;
    font-weight: 600;
}

.ta-project-card .ta-project-body .ta-project-type {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 10px;
    letter-spacing: 1.5px;
    color: var(--ta-gold) !important;
    font-weight: 600 !important;
    text-transform: uppercase;
}

.ta-project-card .ta-project-body .ta-project-tagline {
    font-size: 14px;
    color: rgba(255,255,255,0.7);
    font-style: italic;
    line-height: 1.6;
    margin-bottom: 14px;
    padding-top: 12px;
    border-top: 1px solid rgba(255,255,255,0.08);
}

.ta-project-card .ta-project-body .ta-project-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 12px;
}

.ta-project-card .ta-project-body .ta-project-btn {
    display: inline-block;
    padding: 8px 20px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    transition: all 0.3s ease;
    text-decoration: none;
}

.ta-project-card .ta-project-body .ta-project-btn.ta-btn-primary {
    background: var(--ta-gold);
    color: #1a1408;
}

.ta-project-card .ta-project-body .ta-project-btn.ta-btn-primary:hover {
    background: #dbb85a;
    transform: translateY(-2px);
}

.ta-project-card .ta-project-body .ta-project-btn.ta-btn-outline {
    border: 1px solid rgba(255,255,255,0.2);
    color: rgba(255,255,255,0.7);
}

.ta-project-card .ta-project-body .ta-project-btn.ta-btn-outline:hover {
    border-color: var(--ta-gold);
    color: var(--ta-gold);
    transform: translateY(-2px);
}

.ta-project-card .ta-project-body .ta-project-crown {
    font-family: 'Fraunces', serif;
    font-size: 16px;
    font-weight: 700;
    color: var(--ta-gold);
    letter-spacing: 1px;
    padding-top: 10px;
    border-top: 1px solid rgba(255,255,255,0.06);
}

.ta-project-card .ta-project-body .ta-project-crown i {
    margin-right: 8px;
    font-size: 14px;
}

/* Projects Slider Controls */
.ta-projects-slider-controls {
    display: flex;
    justify-content: center;
    gap: 16px;
    margin-top: 16px;
}

.ta-projects-slider-btn {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    border: 2px solid var(--ta-gold);
    background: transparent;
    color: var(--ta-gold);
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.ta-projects-slider-btn:hover {
    background: var(--ta-gold);
    color: #0a101c;
}

.ta-projects-slider-btn:disabled {
    opacity: 0.3;
    cursor: not-allowed;
    transform: none;
}

.ta-projects-slider-btn:disabled:hover {
    background: transparent;
    color: var(--ta-gold);
}

.ta-projects-slider-dots {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 16px;
}

.ta-projects-slider-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: none;
    background: rgba(255,255,255,0.2);
    cursor: pointer;
    transition: all 0.3s ease;
    padding: 0;
}

.ta-projects-slider-dot.active {
    background: var(--ta-gold);
    transform: scale(1.2);
}

/* Dark mode projects */
body.dark-mode .ta-projects-showcase {
    background: #0a101c !important;
}

body.dark-mode .ta-project-card {
    background: transparent !important;
}

body.dark-mode .ta-project-card .ta-project-image {
    background: #1a2332;
}

body.dark-mode .ta-project-card .ta-project-body h3 {
    color: #ffffff;
}

body.dark-mode .ta-project-card .ta-project-body .ta-project-location {
    color: rgba(255,255,255,0.6);
}

body.dark-mode .ta-project-card .ta-project-body .ta-project-details span {
    color: rgba(255,255,255,0.6);
}

body.dark-mode .ta-project-card .ta-project-body .ta-project-details span strong {
    color: #ffffff;
}

body.dark-mode .ta-project-card .ta-project-body .ta-project-tagline {
    color: rgba(255,255,255,0.7);
    border-color: rgba(255,255,255,0.08);
}

body.dark-mode .ta-project-card .ta-project-body .ta-project-crown {
    border-color: rgba(255,255,255,0.06);
}

body.dark-mode .ta-projects-slider-btn {
    border-color: var(--ta-gold);
    color: var(--ta-gold);
}

body.dark-mode .ta-projects-slider-btn:hover {
    background: var(--ta-gold);
    color: #0a101c;
}

body.dark-mode .ta-projects-slider-dot {
    background: rgba(255,255,255,0.15);
}

body.dark-mode .ta-projects-slider-dot.active {
    background: var(--ta-gold);
}

/* ---------- CTA ---------- */
.tatito-about .ta-cta { 
    border-bottom: none; 
    padding: 60px 0 76px; 
    text-align: center;
    background: var(--ta-soft);
    transition: background-color 0.3s ease;
}

.tatito-about .ta-cta h2 { 
    font-size: 26px; 
    margin-bottom: 14px; 
}

.tatito-about .ta-cta p { 
    color: var(--ta-muted); 
    max-width: 460px; 
    margin: 0 auto 26px; 
    font-size: 15px; 
}

.tatito-about .ta-buttons { 
    display: flex; 
    justify-content: center; 
    gap: 16px; 
    flex-wrap: wrap; 
}

.tatito-about .ta-btn { 
    padding: 12px 28px; 
    border-radius: 999px; 
    font-size: 14px; 
    letter-spacing: 0.3px; 
    display: inline-block;
    transition: background 0.3s ease, color 0.3s ease, transform 0.2s ease, border-color 0.3s ease;
}

.tatito-about .ta-btn:hover {
    transform: translateY(-2px);
}

.tatito-about .ta-btn.ta-gold { 
    background: linear-gradient(160deg, #e9c579, #8a6f3f); 
    color: #1a1408; 
    font-weight: 600;
}

body.dark-mode .tatito-about .ta-btn.ta-gold {
    background: linear-gradient(160deg, #e9c579, #a8844a);
    color: #0d1117;
}

.tatito-about .ta-btn.ta-ghost { 
    border: 1px solid var(--ta-gold); 
    color: var(--ta-gold);
    background: transparent;
}

.tatito-about .ta-btn.ta-ghost:hover {
    background: var(--ta-gold);
    color: var(--ta-soft);
}

body.dark-mode .tatito-about .ta-btn.ta-ghost:hover {
    color: #0d1117;
}

/* Responsive */
@media (max-width: 1200px) {
    .ta-team-slider .ta-team-card {
        flex: 0 0 calc(25% - 18px);
    }
    .ta-project-card {
        flex: 0 0 calc(50% - 15px);
    }
}

@media (max-width: 992px) {
    .ta-team-slider .ta-team-card {
        flex: 0 0 calc(33.33% - 16px);
    }
    .ta-project-card {
        flex: 0 0 calc(50% - 15px);
    }
}

@media (max-width: 768px) {
    .ta-team-slider .ta-team-card {
        flex: 0 0 calc(50% - 12px);
        min-width: unset;
    }
    .ta-project-card {
        flex: 0 0 calc(100% - 0px);
    }
    .ta-project-card .ta-project-body h3 {
        font-size: 19px;
    }
    .ta-project-card .ta-project-body .ta-project-tagline {
        font-size: 13px;
    }
    .ta-project-card .ta-project-body .ta-project-crown {
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .ta-team-slider .ta-team-card {
        flex: 0 0 calc(100% - 0px);
    }
    .ta-project-card {
        flex: 0 0 calc(100% - 0px);
    }
    .ta-slider-btn,
    .ta-projects-slider-btn {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
    .ta-project-card .ta-project-body {
        padding: 0 2px;
    }
    .ta-project-card .ta-project-body h3 {
        font-size: 17px;
    }
    .ta-project-card .ta-project-body .ta-project-details span {
        font-size: 12px;
    }
    .ta-project-card .ta-project-body .ta-project-btn {
        font-size: 10px;
        padding: 6px 14px;
    }
}

/* Dark mode specific overrides for cards */
body.dark-mode .tatito-about .ta-pillar {
    background: #161b22;
}

body.dark-mode .tatito-about .ta-team-card {
    background: #161b22;
    border-color: #30363d;
}

body.dark-mode .tatito-about .ta-team-card .ta-avatar {
    background: #0d1117;
}

body.dark-mode .tatito-about .ta-leader .ta-frame {
    background: #161b22;
    border-color: #30363d;
}

body.dark-mode .tatito-about .ta-fact {
    border-color: #30363d;
}

body.dark-mode .tatito-about .ta-hero-visual .ta-badge {
    border-color: rgba(255,255,255,0.08);
}

body.dark-mode .tatito-about .ta-hero-visual .ta-word {
    color: #f3ecdf;
}

body.dark-mode .tatito-about .ta-hero-visual .ta-sub {
    color: #e9c579;
}

body.dark-mode .tatito-about .ta-hero-visual .ta-tag {
    color: #b9ac96;
}

body.dark-mode .tatito-about .ta-hero-visual .ta-num-label {
    color: #b9ac96;
}

body.dark-mode .tatito-about .ta-section-head .ta-rule {
    background: var(--ta-gold);
}

body.dark-mode .tatito-about .ta-pillars {
    background: #30363d;
    border-color: #30363d;
}

body.dark-mode .tatito-about .ta-pillar .ta-icon {
    color: var(--ta-gold);
}

body.dark-mode .tatito-about .ta-pillar p {
    color: var(--ta-muted);
}

body.dark-mode .tatito-about .ta-leader .ta-role {
    border-color: var(--ta-gold);
    color: var(--ta-gold);
}

body.dark-mode .tatito-about .ta-leader p.ta-bio {
    color: var(--ta-muted);
}

body.dark-mode .tatito-about .ta-cta {
    background: #0d1117;
}

body.dark-mode .tatito-about .ta-cta p {
    color: var(--ta-muted);
}

body.dark-mode .ta-slider-btn {
    border-color: var(--ta-gold);
    color: var(--ta-gold);
}

body.dark-mode .ta-slider-btn:hover {
    background: var(--ta-gold);
    color: #0d1117;
}

body.dark-mode .ta-slider-dot {
    background: #30363d;
}

body.dark-mode .ta-slider-dot.active {
    background: var(--ta-gold);
}
</style>
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<!-- ============ ABOUT US ============ -->
<div class="tatito-about">

  <!-- Hero -->
  <section class="ta-hero">
    <div class="container ta-grid">

      <div class="ta-hero-visual">
        <div class="ta-glyph">
          <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M50 6 L84 26 V70 L50 90 L16 70 V26 Z" stroke="url(#taG1)" stroke-width="2"/>
            <path d="M32 62 V44 L50 34 L68 44 V62" stroke="url(#taG1)" stroke-width="2" stroke-linejoin="round"/>
            <path d="M44 62 V50 H56 V62" stroke="url(#taG1)" stroke-width="2"/>
            <path d="M50 34 V16 M50 16 L58 22" stroke="url(#taG1)" stroke-width="2" stroke-linecap="round"/>
            <defs>
              <linearGradient id="taG1" x1="0" y1="0" x2="100" y2="100">
                <stop offset="0" stop-color="#f0c766"/>
                <stop offset="1" stop-color="#8a6f3f"/>
              </linearGradient>
            </defs>
          </svg>
        </div>
        <div class="ta-word">TATITO</div>
        <span class="ta-sub">DEVELOPERS &amp; PROMOTERS</span>
        <div class="ta-tag">Complete Property Solutions</div>
        <div class="ta-badge">
          <span class="ta-num">8+</span>
          <span class="ta-num-label">Years of<br>Market Leadership</span>
        </div>
      </div>

      <div>
        <div class="ta-eyebrow">About Us</div>
        <h1>Tatito <em>Developers</em><br>&amp; Promoters</h1>
        <p class="ta-lead">At TATITO Developers and Promoters, we go beyond mere structures. We curate diverse lifestyle streams including premium Open Plots, sustainable Farm Lands, royal Villas, and bespoke Interiors.</p>

        <div class="ta-fact-grid">
          <div class="ta-fact">
            <div class="ta-fh">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 2 L21 7 V17 L12 22 L3 17 V7 Z"/><circle cx="12" cy="12" r="3"/></svg>
              Our Vision
            </div>
            <p>To be the global benchmark in high-end living, harmonizing nature's farm lands with ultra-modern Royal Villas.</p>
          </div>
          <div class="ta-fact">
            <div class="ta-fh">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/><circle cx="12" cy="12" r="0.8" fill="currentColor"/></svg>
              Our Goal
            </div>
            <p>Delivering clear-title Open Plots and high-yield Real Estate investments through an uncompromising ethical framework.</p>
          </div>
          <div class="ta-fact">
            <div class="ta-fh">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 21 V10 L12 4 L20 10 V21 M4 21 H20 M9 21 V15 H15 V21"/></svg>
              Happy Families
            </div>
            <p>Over 500+ families have realized their dream through our bespoke Interiors and sophisticated Apartment projects.</p>
          </div>
          <div class="ta-fact">
            <div class="ta-fh">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="9"/><path d="M12 7 V12 L15.5 14.5"/></svg>
              Full Spectrum
            </div>
            <p>We master the entire lifecycle of real estate, from strategic land acquisition to the final interior aesthetic.</p>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- Pillars -->
  <section class="ta-section">
    <div class="container">
      <div class="ta-section-head">
        <h2>Pillars of TATITO Eco System</h2>
        <div class="ta-rule"></div>
      </div>

      <div class="ta-pillars">

        <div class="ta-pillar">
          <div class="ta-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2 L20 5 V11 C20 16 17 20 12 22 C7 20 4 16 4 11 V5 Z"/></svg></div>
          <h3>Precision</h3>
          <p>Meticulous architectural integrity in every structure we build, ensuring that every detail meets the highest global standards of luxury and safety.</p>
        </div>

        <div class="ta-pillar">
          <div class="ta-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="5"/><path d="M8.5 12.5 L7 21 L12 18.5 L17 21 L15.5 12.5"/></svg></div>
          <h3>Excellence</h3>
          <p>Award-winning standards in global real estate, recognized for our commitment to quality and architectural innovation across major territories.</p>
        </div>

        <div class="ta-pillar">
          <div class="ta-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2 L22 12 L12 22 L2 12 Z"/></svg></div>
          <h3>Bespoke</h3>
          <p>Hand-curated properties for the elite lifestyle, designed for those who demand exclusivity and a personal touch in their living spaces.</p>
        </div>

        <div class="ta-pillar">
          <div class="ta-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="9"/><path d="M12 7 V12 L15.5 14.5"/></svg></div>
          <h3>Legacy</h3>
          <p>Building generational wealth through timeless assets that appreciate over time, providing a secure and prestigious future for your family.</p>
        </div>

        <div class="ta-pillar">
          <div class="ta-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="4" y="9" width="16" height="12" rx="1"/><path d="M8 9 V6 C8 4 9.5 3 12 3 C14.5 3 16 4 16 6 V9"/></svg></div>
          <h3>Innovation</h3>
          <p>Pioneering future-forward design philosophies that blend sustainable technology with classical high-end aesthetics for the modern homeowner.</p>
        </div>

        <div class="ta-pillar">
          <div class="ta-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="9"/><path d="M3 12 H21 M12 3 C15 6.5 15 17.5 12 21 C9 17.5 9 6.5 12 3 Z"/></svg></div>
          <h3>Global</h3>
          <p>Connecting world-class standards with local roots, bringing international luxury experiences to your doorstep with unparalleled precision.</p>
        </div>

        <div class="ta-pillar">
          <div class="ta-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 21 C7 17 3 13 3 8.5 A4.5 4.5 0 0 1 12 6.5 A4.5 4.5 0 0 1 21 8.5 C21 13 17 17 12 21 Z"/></svg></div>
          <h3>Wellness</h3>
          <p>Prioritizing the health and comfort of residents through advanced air purification, natural light optimization, and holistic spatial design.</p>
        </div>

        <div class="ta-pillar">
          <div class="ta-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 20 V9 L12 4 L20 9 V20 M4 20 H20"/><path d="M9 20 V14 H15 V20"/></svg></div>
          <h3>Quality</h3>
          <p>Uncompromising build standards for longevity, using premium materials and expert craftsmanship to ensure your home stands the test of time.</p>
        </div>

      </div>
    </div>
  </section>

  <!-- Leadership -->
  <section class="ta-section">
    <div class="container">
      <div class="ta-section-head">
        <h2>Visionary Leadership</h2>
        <div class="ta-rule"></div>
      </div>

      <div class="ta-leaders">
        <div class="ta-leader">
          <div class="ta-frame">
            <?php $manoj_photo = ''; /* e.g. '/uploads/team/manoj.jpg' */ ?>
            <?php if (!empty($manoj_photo)): ?>
              <img src="<?php echo htmlspecialchars($manoj_photo); ?>" alt="Manoj Kumar Vanam">
            <?php else: ?>
              <div class="ta-mono-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3"><circle cx="12" cy="8" r="4"/><path d="M4 21 C4 16 7.5 14 12 14 C16.5 14 20 16 20 21"/></svg></div>
            <?php endif; ?>
          </div>
          <h3>Manoj Kumar Vanam</h3>
          <span class="ta-role">Founder &amp; CEO of Tatito Projects</span>
          <p class="ta-bio">Visionary architect of the Tatito legacy, driving the strategic expansion of luxury real estate portfolios across the region.</p>
        </div>

        <div class="ta-leader">
          <div class="ta-frame">
            <?php $kasi_photo = ''; /* e.g. '/uploads/team/kasi.jpg' */ ?>
            <?php if (!empty($kasi_photo)): ?>
              <img src="<?php echo htmlspecialchars($kasi_photo); ?>" alt="Kasi Kalyan Vanam">
            <?php else: ?>
              <div class="ta-mono-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3"><circle cx="12" cy="8" r="4"/><path d="M4 21 C4 16 7.5 14 12 14 C16.5 14 20 16 20 21"/></svg></div>
            <?php endif; ?>
          </div>
          <h3>Kasi Kalyan Vanam</h3>
          <span class="ta-role">Cofounder</span>
          <p class="ta-bio">Leading operational excellence and project lifecycle management, ensuring every Tatito development meets the global standards of prestige.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Meet Our Expert Team - Slider -->
  <section class="ta-section">
    <div class="container">
      <div class="ta-section-head">
        <h2>Meet Our Expert Team</h2>
        <div class="ta-rule"></div>
      </div>

      <?php
        // Team members data
        $teamMembers = [
          ['name' => 'Rakesh Vanam',  'role' => 'Site Coordinator',  'photo' => ''],
          ['name' => 'Prasad Benny',  'role' => 'Sales Manager',     'photo' => ''],
          ['name' => 'Mounika Reddy', 'role' => 'Client Relations',  'photo' => ''],
          ['name' => 'Uddav Goshika', 'role' => 'Legal Associate',   'photo' => ''],
          ['name' => 'Pradeep',       'role' => 'Project Executive', 'photo' => ''],
          ['name' => 'Suresh Kumar',  'role' => 'Architect',         'photo' => ''],
          ['name' => 'Anitha Rao',    'role' => 'Interior Designer', 'photo' => ''],
          ['name' => 'Vikram Singh',  'role' => 'Project Manager',   'photo' => ''],
        ];
      ?>

      <div class="ta-team-slider-wrapper">
        <div class="ta-team-slider" id="teamSlider">
          <?php foreach ($teamMembers as $member): ?>
            <div class="ta-team-card">
              <div class="ta-avatar">
                <?php if (!empty($member['photo'])): ?>
                  <img src="<?php echo htmlspecialchars($member['photo']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>">
                <?php else: ?>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3"><circle cx="12" cy="8" r="4"/><path d="M4 21 C4 16 7.5 14 12 14 C16.5 14 20 16 20 21"/></svg>
                <?php endif; ?>
              </div>
              <div class="ta-info">
                <h4><?php echo htmlspecialchars($member['name']); ?></h4>
                <span class="ta-role"><?php echo htmlspecialchars($member['role']); ?></span>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Slider Controls -->
        <div class="ta-slider-controls">
          <button class="ta-slider-btn" id="teamSliderPrev" aria-label="Previous">
            <i class="bi bi-chevron-left"></i>
          </button>
          <button class="ta-slider-btn" id="teamSliderNext" aria-label="Next">
            <i class="bi bi-chevron-right"></i>
          </button>
        </div>

        <div class="ta-slider-dots" id="teamSliderDots"></div>
      </div>
    </div>
  </section>

  <!-- Projects Showcase Slider - EXACT MATCH FROM SCREENSHOT -->
  <section class="ta-section ta-projects-showcase">
    <div class="container">
      <div class="ta-section-head">
        <h2 style="color: #ffffff;">Our Signature Projects</h2>
        <div class="ta-rule"></div>
      </div>

      <div class="ta-projects-slider-wrapper">
        <div class="ta-projects-slider" id="projectsSlider">

          <!-- Project 1 - AstraValley -->
          <div class="ta-project-card">
            <div class="ta-project-image">
              <img src="https://placehold.co/600x400/1a2332/8b949e?text=Open+Plots" alt="Open Plots">
              <div class="ta-project-badge">Open Plots</div>
            </div>
            <div class="ta-project-body">
              <h3>AstraValley Phase I</h3>
              <div class="ta-project-location">
                <i class="bi bi-geo-alt"></i> MADUGULA, VISAKHAPATNAM
              </div>
              <div class="ta-project-details">
                <span><strong>152-300</strong> SQFT</span>
                <span class="ta-project-type">LUXURY PLOTS</span>
              </div>
              <div class="ta-project-tagline">
                "An exquisite sanctuary where curated land holdings meet the whispering winds of the valley, crafted for the true connoisseur."
              </div>
              <div class="ta-project-actions">
                <a href="#" class="ta-project-btn ta-btn-primary">EXPLORE PROJECT</a>
                <a href="#" class="ta-project-btn ta-btn-outline">FULL CASE STUDY</a>
              </div>
              <div class="ta-project-crown">
                <i class="bi bi-gem"></i> THE VALLEY'S CROWN JEWEL
              </div>
            </div>
          </div>

          <!-- Project 2 - GoutamValley (from screenshot) -->
          <div class="ta-project-card">
            <div class="ta-project-image">
              <img src="https://placehold.co/600x400/1a2332/8b949e?text=Coastal+Plots" alt="Coastal Plots">
              <div class="ta-project-badge">Coastal Plots</div>
            </div>
            <div class="ta-project-body">
              <h3>GoutamValley</h3>
              <div class="ta-project-location">
                <i class="bi bi-geo-alt"></i> BHEEMILI, VISAKHAPATNAM
              </div>
              <div class="ta-project-details">
                <span><strong>152-300</strong> SQFT</span>
                <span class="ta-project-type">COASTAL PLOTS</span>
              </div>
              <div class="ta-project-tagline">
                "Bespoke coastal living that blends the tranquility of the sea with meticulously planned luxury infrastructure."
              </div>
              <div class="ta-project-actions">
                <a href="#" class="ta-project-btn ta-btn-primary">EXPLORE PROJECT</a>
                <a href="#" class="ta-project-btn ta-btn-outline">FULL CASE STUDY</a>
              </div>
              <div class="ta-project-crown">
                <i class="bi bi-gem"></i> COASTAL ELEGANCE DEFINED
              </div>
            </div>
          </div>

          <!-- Project 3 - GreenValley Estates -->
          <div class="ta-project-card">
            <div class="ta-project-image">
              <img src="https://placehold.co/600x400/1a2332/8b949e?text=Farm+Lands" alt="Farm Lands">
              <div class="ta-project-badge">Farm Lands</div>
            </div>
            <div class="ta-project-body">
              <h3>GreenValley Estates</h3>
              <div class="ta-project-location">
                <i class="bi bi-geo-alt"></i> ARAKU VALLEY, VISAKHAPATNAM
              </div>
              <div class="ta-project-details">
                <span><strong>1-5</strong> ACRES</span>
                <span class="ta-project-type">SUSTAINABLE LIVING</span>
              </div>
              <div class="ta-project-tagline">
                "Where nature meets luxury — sustainable farm lands designed for the discerning few who value purity and tranquility."
              </div>
              <div class="ta-project-actions">
                <a href="#" class="ta-project-btn ta-btn-primary">EXPLORE PROJECT</a>
                <a href="#" class="ta-project-btn ta-btn-outline">FULL CASE STUDY</a>
              </div>
              <div class="ta-project-crown">
                <i class="bi bi-gem"></i> THE VALLEY'S CROWN JEWEL
              </div>
            </div>
          </div>

          <!-- Project 4 - Royal Palm Enclave -->
          <div class="ta-project-card">
            <div class="ta-project-image">
              <img src="https://placehold.co/600x400/1a2332/8b949e?text=Royal+Villas" alt="Royal Villas">
              <div class="ta-project-badge">Royal Villas</div>
            </div>
            <div class="ta-project-body">
              <h3>Royal Palm Enclave</h3>
              <div class="ta-project-location">
                <i class="bi bi-geo-alt"></i> RUSHIKONDA, VISAKHAPATNAM
              </div>
              <div class="ta-project-details">
                <span><strong>4-6</strong> BHK</span>
                <span class="ta-project-type">PREMIUM LIVING</span>
              </div>
              <div class="ta-project-tagline">
                "The crown jewel of coastal living — where every sunset is a masterpiece and every home tells a story of royalty."
              </div>
              <div class="ta-project-actions">
                <a href="#" class="ta-project-btn ta-btn-primary">EXPLORE PROJECT</a>
                <a href="#" class="ta-project-btn ta-btn-outline">FULL CASE STUDY</a>
              </div>
              <div class="ta-project-crown">
                <i class="bi bi-gem"></i> COASTAL ELEGANCE DEFINED
              </div>
            </div>
          </div>

        </div>

        <!-- Projects Slider Controls -->
        <div class="ta-projects-slider-controls">
          <button class="ta-projects-slider-btn" id="projectsSliderPrev" aria-label="Previous">
            <i class="bi bi-chevron-left"></i>
          </button>
          <button class="ta-projects-slider-btn" id="projectsSliderNext" aria-label="Next">
            <i class="bi bi-chevron-right"></i>
          </button>
        </div>

        <div class="ta-projects-slider-dots" id="projectsSliderDots"></div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="ta-cta">
    <div class="container">
      <h2>Begin your journey with Tatito</h2>
      <p>Speak with our team about Open Plots, Farm Lands, Royal Villas, or bespoke Interiors — and see why 500+ families trust us.</p>
      <div class="ta-buttons">
        <a class="ta-btn ta-gold" href="sell-property.php">Enquire Now</a>
        <a class="ta-btn ta-ghost" href="properties.php">View Our Projects</a>
      </div>
    </div>
  </section>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ===== TEAM SLIDER =====
(function() {
    const slider = document.getElementById('teamSlider');
    const prevBtn = document.getElementById('teamSliderPrev');
    const nextBtn = document.getElementById('teamSliderNext');
    const dotsContainer = document.getElementById('teamSliderDots');

    if (!slider || !prevBtn || !nextBtn || !dotsContainer) return;

    const cards = slider.querySelectorAll('.ta-team-card');
    const totalCards = cards.length;
    let currentIndex = 0;
    let cardsPerView = getCardsPerView();
    let maxIndex = Math.max(0, totalCards - cardsPerView);

    function getCardsPerView() {
        const width = window.innerWidth;
        if (width < 480) return 1;
        if (width < 768) return 2;
        if (width < 992) return 3;
        if (width < 1200) return 4;
        return 5;
    }

    function updateSlider() {
        cardsPerView = getCardsPerView();
        maxIndex = Math.max(0, totalCards - cardsPerView);
        if (currentIndex > maxIndex) currentIndex = maxIndex;

        const cardWidth = cards[0].offsetWidth + 24;
        const translateX = currentIndex * cardWidth;
        slider.style.transform = `translateX(-${translateX}px)`;

        const totalDots = Math.ceil(totalCards / cardsPerView);
        dotsContainer.innerHTML = '';
        for (let i = 0; i < totalDots; i++) {
            const dot = document.createElement('button');
            dot.className = 'ta-slider-dot' + (i === Math.floor(currentIndex / cardsPerView) ? ' active' : '');
            dot.setAttribute('data-index', i);
            dot.addEventListener('click', function() {
                currentIndex = i * cardsPerView;
                if (currentIndex > maxIndex) currentIndex = maxIndex;
                updateSlider();
            });
            dotsContainer.appendChild(dot);
        }

        prevBtn.disabled = currentIndex === 0;
        nextBtn.disabled = currentIndex >= maxIndex;
    }

    prevBtn.addEventListener('click', function() {
        if (currentIndex > 0) {
            currentIndex = Math.max(0, currentIndex - cardsPerView);
            updateSlider();
        }
    });

    nextBtn.addEventListener('click', function() {
        if (currentIndex < maxIndex) {
            currentIndex = Math.min(maxIndex, currentIndex + cardsPerView);
            updateSlider();
        }
    });

    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(updateSlider, 200);
    });

    setTimeout(updateSlider, 100);
})();

// ===== PROJECTS SHOWCASE SLIDER =====
(function() {
    const slider = document.getElementById('projectsSlider');
    const prevBtn = document.getElementById('projectsSliderPrev');
    const nextBtn = document.getElementById('projectsSliderNext');
    const dotsContainer = document.getElementById('projectsSliderDots');

    if (!slider || !prevBtn || !nextBtn || !dotsContainer) return;

    const cards = slider.querySelectorAll('.ta-project-card');
    const totalCards = cards.length;
    let currentIndex = 0;
    let cardsPerView = getCardsPerView();
    let maxIndex = Math.max(0, totalCards - cardsPerView);

    function getCardsPerView() {
        const width = window.innerWidth;
        if (width < 768) return 1;
        if (width < 1200) return 2;
        return 3;
    }

    function updateSlider() {
        cardsPerView = getCardsPerView();
        maxIndex = Math.max(0, totalCards - cardsPerView);
        if (currentIndex > maxIndex) currentIndex = maxIndex;

        const cardWidth = cards[0].offsetWidth + 30;
        const translateX = currentIndex * cardWidth;
        slider.style.transform = `translateX(-${translateX}px)`;

        const totalDots = Math.ceil(totalCards / cardsPerView);
        dotsContainer.innerHTML = '';
        for (let i = 0; i < totalDots; i++) {
            const dot = document.createElement('button');
            dot.className = 'ta-projects-slider-dot' + (i === Math.floor(currentIndex / cardsPerView) ? ' active' : '');
            dot.setAttribute('data-index', i);
            dot.addEventListener('click', function() {
                currentIndex = i * cardsPerView;
                if (currentIndex > maxIndex) currentIndex = maxIndex;
                updateSlider();
            });
            dotsContainer.appendChild(dot);
        }

        prevBtn.disabled = currentIndex === 0;
        nextBtn.disabled = currentIndex >= maxIndex;
    }

    prevBtn.addEventListener('click', function() {
        if (currentIndex > 0) {
            currentIndex = Math.max(0, currentIndex - cardsPerView);
            updateSlider();
        }
    });

    nextBtn.addEventListener('click', function() {
        if (currentIndex < maxIndex) {
            currentIndex = Math.min(maxIndex, currentIndex + cardsPerView);
            updateSlider();
        }
    });

    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(updateSlider, 200);
    });

    setTimeout(updateSlider, 100);
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