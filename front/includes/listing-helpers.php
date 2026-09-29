<?php
/**
 * listing-helpers.php
 * -----------------------------------------------------------------------
 * Shared helper functions for the "directory style" listing pages:
 * vendors.php, local-services.php, interiors.php
 *
 * These pages don't deal in BHK/Lakh/Crore real-estate pricing like
 * properties.php does — prices here are simple free-text values like
 * "₹500/service" or "₹2,500/room" or "₹45,000 onwards". So instead of
 * priceToLakhs()/formatPriceDisplay() we just need:
 *   - a numeric value pulled out of that text (for sorting/filtering)
 *   - the original text (for display, unchanged)
 * -----------------------------------------------------------------------
 */

if (!function_exists('pv')) {
    function pv($row, $key, $fallback = 'N/A') {
        return (!empty($row[$key])) ? $row[$key] : $fallback;
    }
}

/**
 * Pulls the first number out of a free-text price string so it can be
 * used for sorting / min-max filtering. Ignores currency symbols and
 * commas. Returns 0 if nothing numeric is found (e.g. "Price on Request").
 *
 * "₹500/service"      -> 500
 * "₹2,500/room"        -> 2500
 * "₹45,000 onwards"    -> 45000
 * "Price on Request"   -> 0
 */
function extractNumericPrice($price) {
    if (empty($price)) return 0;
    if (preg_match('/[\d,]+(\.\d+)?/', $price, $m)) {
        return (float) str_replace(',', '', $m[0]);
    }
    return 0;
}

/**
 * Cleans up messy addresses like "Karnataka , Bangalore , Whitefield"
 * into "Bangalore, Whitefield" — keeps only the last $limit segments.
 */
function formatAddressDisplay($address, $limit = 2) {
    if (empty($address)) return '';
    $parts = array_map('trim', explode(',', $address));
    $parts = array_values(array_filter($parts, fn($p) => $p !== ''));
    if (count($parts) > $limit) {
        $parts = array_slice($parts, -$limit);
    }
    return implode(', ', $parts);
}

/**
 * Turns a category slug like "vnd-architect" or "int-modular-kitchen"
 * into a readable label like "Architect" / "Modular Kitchen", without
 * needing a giant hand-maintained lookup table for every single one of
 * the ~300 category slugs used across the nav menu.
 *
 * $prefix is stripped first (e.g. "vnd-", "lsv-", "int-").
 */
function categorySlugToLabel($slug, $prefix = '') {
    if ($slug === '' || $slug === null) return '';

    $slug = strtolower(trim($slug));
    if ($prefix !== '' && strpos($slug, $prefix) === 0) {
        $slug = substr($slug, strlen($prefix));
    }

    // Terms that should stay fully upper-case (acronyms/initialisms
    // that show up throughout the vendor / local-service / interior
    // category trees).
    $upperExceptions = [
        'bhk','rk','pg','ac','ro','wc','wpc','pvc','upvc','pop','led',
        'cctv','rcc','hvac','stp','it','nri','rera','ips','dtcp','hmda',
        'gps','3d','frp','na','tmt','cp',
    ];
    // Small connector words that read better lower-case (unless first word).
    $lowerWords = ['for','of','and','on','at','the','a','in','&'];

    $words = explode('-', $slug);
    $out = [];
    foreach ($words as $i => $w) {
        if ($w === '') continue;
        if (in_array($w, $upperExceptions, true)) {
            $out[] = strtoupper($w);
        } elseif ($i > 0 && in_array($w, $lowerWords, true)) {
            $out[] = $w;
        } else {
            $out[] = ucfirst($w);
        }
    }
    return implode(' ', $out);
}

/**
 * Star rating markup (Bootstrap Icons), e.g. 4.5 -> ★★★★☆ (4.5)
 */
function renderStars($rating) {
    $rating = (float) $rating;
    if ($rating <= 0) return '';
    $full = (int) floor($rating);
    $half = ($rating - $full) >= 0.5;
    $html = '<span class="rating-stars">';
    for ($i = 0; $i < $full; $i++) $html .= '<i class="bi bi-star-fill"></i>';
    if ($half) $html .= '<i class="bi bi-star-half"></i>';
    $empty = 5 - $full - ($half ? 1 : 0);
    for ($i = 0; $i < $empty; $i++) $html .= '<i class="bi bi-star"></i>';
    $html .= ' <span class="rating-value">' . number_format($rating, 1) . '</span></span>';
    return $html;
}

/**
 * Rebuilds the current query string without a given param (and without
 * the page param, since removing a filter should reset pagination) —
 * used for the "active filter" chip [x] links.
 */
function urlWithoutParam($baseFile, $param) {
    $query = $_GET;
    unset($query[$param]);
    unset($query['page']);
    $qs = http_build_query($query);
    return $baseFile . ($qs !== '' ? '?' . $qs : '');
}
