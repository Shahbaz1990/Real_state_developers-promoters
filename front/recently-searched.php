<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Recent Activity';

$projects = [];
$sql = "SELECT id, project_name, slug, image, city, price, address, bedrooms, bathrooms, area, category_id, category_type FROM trending_projects WHERE status='Active' ORDER BY project_order ASC, created_at DESC";
$res = $conn->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) { $projects[] = $row; }
}

$interiors = [];
$intSql = "SELECT id, product_name, slug, category, brand, price, image, description FROM interiors WHERE status='Active' ORDER BY created_at DESC LIMIT 10";
$intRes = $conn->query($intSql);
if ($intRes) {
    while ($row = $intRes->fetch_assoc()) { $interiors[] = $row; }
}

$vendors = [];
$vndSql = "SELECT id, name, slug, category, city, address, phone, experience_years, rating, starting_price, image, description FROM vendors WHERE status='Active' ORDER BY created_at DESC LIMIT 10";
$vndRes = $conn->query($vndSql);
if ($vndRes) {
    while ($row = $vndRes->fetch_assoc()) { $vendors[] = $row; }
}

$localServices = [];
$srvSql = "SELECT id, service_name, slug, category, city, address, phone, price, rating, image, description FROM local_services WHERE status='Active' ORDER BY created_at DESC LIMIT 10";
$srvRes = $conn->query($srvSql);
if ($srvRes) {
    while ($row = $srvRes->fetch_assoc()) { $localServices[] = $row; }
}

$contacts = [];
if (isset($_SESSION['user_email'])) {
    $email = $_SESSION['user_email'];

    $stmt = $conn->prepare("SELECT id as enquiry_id, property_type, bhk_type, budget_range, preferred_locality, move_timeline, additional_info, enquiry_type, status, created_at FROM enquiries WHERE email = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resQ = $stmt->get_result();
    while ($row = $resQ->fetch_assoc()) {
        $parts = array_filter([$row['property_type'], $row['bhk_type'], $row['budget_range'], $row['preferred_locality']], function ($v) { return !empty(trim((string)$v)); });
        $row['summary'] = implode(' · ', array_map('htmlspecialchars', $parts));
        $row['kind'] = 'enquiry';
        $row['label'] = ($row['enquiry_type'] === 'Callback') ? 'Requested Callback' : 'Sent an Enquiry';
        $contacts[] = $row;
    }
    $stmt->close();

    $stmt2 = $conn->prepare("SELECT id as enquiry_id, page_id, page_id as property_id, page_title, appointment_date, created_at FROM property_contact_enquiries WHERE email = ? ORDER BY created_at DESC");
    $stmt2->bind_param("s", $email);
    $stmt2->execute();
    $resQ2 = $stmt2->get_result();
    while ($row = $resQ2->fetch_assoc()) {
        $row['summary'] = $row['page_title'];
        $row['kind'] = 'property_contact';
        $row['label'] = $row['page_title'];
        $contacts[] = $row;
    }
    $stmt2->close();

    usort($contacts, function ($a, $b) {
        return strtotime($b['created_at']) <=> strtotime($a['created_at']);
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($pageTitle); ?> - Tatito Developers</title>
<link rel="icon" type="image/png" href="<?php echo $site['favicon']; ?>">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/account-pages.css">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

<section class="page-shell">
    <div class="container">
        <div class="act-card dash-wrap">
            <div class="card-body">
                <!-- Tabs -->
                <div class="act-tabs" role="tablist" aria-label="Recent activity">
                    <button class="act-tab active" id="tabBtn-searches" role="tab" aria-selected="true" data-tab="searches" type="button">
                        <span class="tab-count" id="count-searches">0</span> Recent Searches
                    </button>
                    <button class="act-tab" id="tabBtn-viewed" role="tab" aria-selected="false" data-tab="viewed" type="button">
                        <span class="tab-count" id="count-viewed">0</span> Viewed
                    </button>
                    <button class="act-tab" id="tabBtn-shortlisted" role="tab" aria-selected="false" data-tab="shortlisted" type="button">
                        <span class="tab-count" id="count-shortlisted">0</span> Shortlisted
                    </button>
                    <button class="act-tab" id="tabBtn-contacted" role="tab" aria-selected="false" data-tab="contacted" type="button">
                        <span class="tab-count" id="count-contacted">0</span> Contacted
                    </button>
                    <span class="act-tab-indicator" id="tab-indicator" aria-hidden="true"></span>
                </div>

                <!-- Panels -->
                <div class="act-panel" id="panel-searches" role="tabpanel" aria-labelledby="tabBtn-searches"></div>
                <div class="act-panel d-none" id="panel-viewed" role="tabpanel" aria-labelledby="tabBtn-viewed"></div>
                <div class="act-panel d-none" id="panel-shortlisted" role="tabpanel" aria-labelledby="tabBtn-shortlisted"></div>
                <div class="act-panel d-none" id="panel-contacted" role="tabpanel" aria-labelledby="tabBtn-contacted"></div>
            </div>
        </div>
    </div>
</section>

<script>
(function () {
    var PROJECTS  = <?php echo json_encode($projects); ?>;
    var INTERIORS = <?php echo json_encode($interiors); ?>;
    var VENDORS   = <?php echo json_encode($vendors); ?>;
    var SERVICES  = <?php echo json_encode($localServices); ?>;
    var CONTACTS  = <?php echo json_encode($contacts); ?>;

    var isUserLoggedIn = <?php echo json_encode(isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])); ?>;
    var userPrefix = typeof window.TATITO_USER_PREFIX !== 'undefined' ? window.TATITO_USER_PREFIX : (isUserLoggedIn ? ('tatito_user_' + <?php echo (int)($_SESSION['user_id'] ?? 0); ?>) : 'tatito_guest');

    var SEARCH_KEY   = userPrefix + '_searches';
    var VIEWED_KEY   = userPrefix + '_viewed';
    var SHORT_KEY    = userPrefix + '_wishlist';
    var CONTACT_KEY  = userPrefix + '_contacted';

    var LOAD_MS = 250;

    function readKey(key) {
        try {
            var raw = localStorage.getItem(key);
            if (!raw) return [];
            var parsed = JSON.parse(raw);
            return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
            return [];
        }
    }

    function saveShortlist(list) {
        if (typeof window.saveWishlistIds === 'function') {
            window.saveWishlistIds(list);
        } else {
            var data = JSON.stringify(list || []);
            localStorage.setItem(SHORT_KEY, data);
            document.dispatchEvent(new CustomEvent('wishlistUpdated', { detail: list }));
        }
    }

    function esc(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }
    function price(p) { return p && p.price ? p.price : 'Price on Request'; }
    function addr(p) {
        if (p && p.address) {
            var parts = String(p.address).split(',').map(function (x) { return x.trim(); }).filter(Boolean).slice(-2);
            if (parts.length) return parts.join(', ');
        }
        return (p && p.city) ? p.city : '';
    }
    function when(ts) {
        if (!ts) return '';
        try { return new Date(ts).toLocaleString(); } catch (e) { return ''; }
    }

    var byId = {};
    PROJECTS.forEach(function (p) { byId[p.id] = p; });

    var interiorsById = {};
    INTERIORS.forEach(function (it) { interiorsById[it.id] = it; });

    var vendorsById = {};
    VENDORS.forEach(function (v) { vendorsById[v.id] = v; });

    var servicesById = {};
    SERVICES.forEach(function (s) { servicesById[s.id] = s; });

    // ===== data sources: return raw data (throw on error) =====
    var sources = {
        searches:   function () {
            var raw = readKey(SEARCH_KEY);
            var valid = [];
            var seen = {};

            (raw || []).forEach(function(s) {
                if (!s) return;
                var rawTitle = (s.title || s.q || s.city || '').trim();
                if (!rawTitle) return;
                
                // Exclude only technical contractor/interior category slugs (vnd-, lsv-, int-)
                if (/^(vnd|lsv|int)[-_]/i.test(rawTitle) || /^(vnd|lsv|int)\s/i.test(rawTitle)) {
                    return;
                }

                // Clean title if it has redundant internal labels
                var title = rawTitle.replace(/^Buy\s+Buy\s+/i, 'Buy ')
                                    .replace(/^Properties\s+Search/i, '')
                                    .trim();
                if (!title) title = rawTitle;

                var key = title.toLowerCase();
                if (!seen[key]) {
                    seen[key] = true;
                    s.title = title;
                    valid.push(s);
                }
            });

            return valid;
        },
        viewed:     function () { return readKey(VIEWED_KEY); },
        shortlisted:function () {
            var raw = readKey(SHORT_KEY) || [];
            var seen = {};
            var deduped = [];
            raw.forEach(function(item) {
                var id = (item && item.id != null) ? parseInt(item.id, 10) : parseInt(item, 10);
                if (id && !seen[id]) {
                    seen[id] = true;
                    deduped.push(item);
                }
            });
            return deduped;
        },
        contacted:  function () {
            var localContacts = readKey(CONTACT_KEY) || [];
            var dbContacts = CONTACTS || [];

            // Prioritize newest local storage and database entries, deduplicated by property
            var combined = [].concat(localContacts, dbContacts);
            var unique = [];
            var seen = {};

            combined.forEach(function(c) {
                if (!c) return;
                var pid = c.property_id || c.page_id || (c.type === 'property' ? c.id : null);
                var key = '';
                if (pid) {
                    key = 'prop_' + pid;
                } else if (c.enquiry_id) {
                    key = 'enq_' + c.enquiry_id;
                } else if (c.summary || c.label) {
                    key = 'label_' + (c.summary || c.label);
                } else {
                    key = 'item_' + JSON.stringify(c);
                }

                if (!seen[key]) {
                    seen[key] = true;
                    unique.push(c);
                }
            });

            return unique;
        }
    };

    var counts = { searches: 0, viewed: 0, shortlisted: 0, contacted: 0 };
    var cached = { searches: null, viewed: null, shortlisted: null, contacted: null };

    function loadAll() {
        Object.keys(sources).forEach(function (tab) {
            try { cached[tab] = sources[tab](); } catch (e) { cached[tab] = null; }
        });
        counts.searches    = cached.searches    ? cached.searches.length    : 0;
        counts.viewed      = cached.viewed      ? cached.viewed.length      : 0;
        counts.shortlisted = cached.shortlisted ? cached.shortlisted.length : 0;
        counts.contacted   = cached.contacted   ? cached.contacted.length   : 0;
    }

    function renderCounts() {
        Object.keys(counts).forEach(function (tab) {
            var el = document.getElementById('count-' + tab);
            if (el) el.textContent = counts[tab];
        });
    }

    // ===== renderers =====
    function emptyState(icon, title, desc) {
        return '<div class="empty-state">' +
            '<i class="bi ' + icon + '"></i>' +
            '<h3 class="empty-title">' + title + '</h3>' +
            '<p>' + desc + '</p>' +
        '</div>';
    }

    function panelTemplate() {
        return '<div class="act-loading"><div class="spinner-brand"></div>Loading your activity…</div>';
    }

    function errorState(onRetry) {
        return '<div class="act-error">' +
            '<i class="bi bi-exclamation-triangle"></i>' +
            '<h5>Something went wrong.</h5>' +
            '<p class="mb-3">We could not load this section. Please try again.</p>' +
            '<button type="button" class="btn btn-outline-secondary btn-sm" data-retry="1">Retry</button>' +
        '</div>';
    }

    function formatDateGroup(d) {
        var now = new Date();
        var isToday = (d.toDateString() === now.toDateString());
        var yesterday = new Date(now);
        yesterday.setDate(now.getDate() - 1);
        var isYesterday = (d.toDateString() === yesterday.toDateString());

        var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        var dayName = days[d.getDay()];
        var dayNum = d.getDate();
        var monthName = months[d.getMonth()];
        var year = d.getFullYear();

        if (isToday) {
            return 'Today - ' + dayName + ' (' + dayNum + ' ' + monthName + ' ' + year + ')';
        } else if (isYesterday) {
            return 'Yesterday - ' + dayName + ' (' + dayNum + ' ' + monthName + ' ' + year + ')';
        } else {
            return dayName + ' (' + dayNum + ' ' + monthName + ' ' + year + ')';
        }
    }

    function renderSearches(data) {
        if (!data || !data.length) {
            return emptyState('bi-search',
                "You haven't searched anything yet!",
                'You will see your search history here, once you start searching for localities, places, or cities in the search bar.');
        }

        // Sort newest first
        var sorted = data.slice().sort(function(a, b) {
            return (b.at || 0) - (a.at || 0);
        });

        // Group by day string
        var groups = {};
        var groupOrder = [];
        sorted.forEach(function(item) {
            var d = item.at ? new Date(item.at) : new Date();
            var groupKey = formatDateGroup(d);
            if (!groups[groupKey]) {
                groups[groupKey] = [];
                groupOrder.push(groupKey);
            }
            groups[groupKey].push(item);
        });

        var html = '<div class="recent-search-feed">';
        groupOrder.forEach(function(groupTitle) {
            html += '<div class="search-date-group">' +
                '<h3 class="search-group-title">' + esc(groupTitle) + '</h3>' +
                '<div class="search-group-cards">';
            
            groups[groupTitle].forEach(function(s) {
                var url = s.url || ('properties.php?search=' + encodeURIComponent(s.title || s.q || s.city || ''));
                var title = s.title || s.city || s.q || 'Place Search';

                html += '<a href="' + esc(url) + '" class="recent-search-card">' +
                    '<div class="recent-search-left">' +
                        '<i class="bi bi-geo-alt-fill recent-search-icon" style="color:#0284c7; font-size:1.15rem;"></i>' +
                        '<span class="recent-search-text">' + esc(title) + '</span>' +
                    '</div>' +
                    '<i class="bi bi-chevron-right recent-search-chevron"></i>' +
                '</a>';
            });

            html += '</div></div>';
        });
        html += '</div>';
        return html;
    }

    function formatAddrMini(address) {
        if (!address) return 'India';
        var parts = address.split(',').map(function(x) { return x.trim(); }).filter(Boolean);
        if (parts.length >= 2) {
            return parts.slice(-2).join(', ');
        }
        return parts[0] || 'India';
    }

    function renderPropertyCard(p, isProject, isShortlisted) {
        var name = p ? p.project_name : 'Property';
        var img = p && p.image
            ? '../backend/uploads/trending-projects/' + p.image
            : 'https://placehold.co/400x300/12213a/c9a84c?text=' + encodeURIComponent(name);
        var url = p && p.slug ? 'property-listing.php?slug=' + encodeURIComponent(p.slug) : 'properties.php';
        var displayPrice = price(p);
        var subAddr = p && p.address ? formatAddrMini(p.address) : (p.city || 'India');
        var reraBadge = isProject ? '<span class="viewed-card-rera"><i class="bi bi-patch-check-fill"></i> Rera</span>' : '';
        var poster = (p && p.posted_by) ? p.posted_by : (isProject ? 'Builder' : (p && p.bedrooms ? 'Owner' : 'Dealer'));
        var heartIcon = isShortlisted ? '<i class="bi bi-heart-fill" style="color:#e0245e;"></i>' : '<i class="bi bi-heart"></i>';
        var heartClass = isShortlisted ? 'viewed-card-heart active wishlist-toggle' : 'viewed-card-heart wishlist-toggle';

        return '<div class="viewed-item-card">' +
            '<div class="viewed-card-thumb" style="background-image:url(\'' + esc(img) + '\');">' +
                reraBadge +
                '<button type="button" class="' + heartClass + '" data-project-id="' + (p ? p.id : 0) + '" data-type="property" aria-label="Wishlist">' + heartIcon + '</button>' +
                '<span class="viewed-card-price-badge">' + esc(displayPrice) + '</span>' +
            '</div>' +
            '<a href="' + esc(url) + '" class="viewed-card-body text-decoration-none">' +
                '<div class="viewed-card-title">' + esc(name) + '</div>' +
                '<div class="viewed-card-sub">In ' + esc(subAddr) + '</div>' +
                '<div class="viewed-card-poster">Posted by ' + esc(poster) + '</div>' +
            '</a>' +
        '</div>';
    }

    function renderInteriorCard(it, isShortlisted) {
        var name = it.product_name || 'Interior Design';
        var img = it.image ? it.image : 'https://placehold.co/400x300/162a45/f5b942?text=' + encodeURIComponent(name);
        var url = 'properties.php?type=interior';
        var displayPrice = it.price || 'Price on Request';
        var brand = it.brand || 'Designer';
        var heartIcon = isShortlisted ? '<i class="bi bi-heart-fill" style="color:#e0245e;"></i>' : '<i class="bi bi-heart"></i>';
        var heartClass = isShortlisted ? 'viewed-card-heart active wishlist-toggle' : 'viewed-card-heart wishlist-toggle';

        return '<div class="viewed-item-card">' +
            '<div class="viewed-card-thumb" style="background-image:url(\'' + esc(img) + '\');">' +
                '<button type="button" class="' + heartClass + '" data-project-id="' + it.id + '" data-type="interior" aria-label="Wishlist">' + heartIcon + '</button>' +
                '<span class="viewed-card-price-badge">' + esc(displayPrice) + '</span>' +
            '</div>' +
            '<a href="' + esc(url) + '" class="viewed-card-body text-decoration-none">' +
                '<div class="viewed-card-title">' + esc(name) + '</div>' +
                '<div class="viewed-card-sub">By ' + esc(brand) + '</div>' +
                '<div class="viewed-card-poster">Category: ' + esc(it.category || 'Interiors') + '</div>' +
            '</a>' +
        '</div>';
    }

    function renderVendorCard(v, isShortlisted) {
        var name = v.name || 'Vendor';
        var img = v.image ? v.image : 'https://placehold.co/400x300/1b3558/ffffff?text=' + encodeURIComponent(name);
        var url = 'properties.php?type=vendor';
        var rate = v.rating ? '⭐ ' + v.rating : '⭐ 4.5';
        var exp = v.experience_years ? '(' + v.experience_years + ' yrs exp)' : '';
        var priceTag = v.starting_price || 'Enquire for Price';
        var heartIcon = isShortlisted ? '<i class="bi bi-heart-fill" style="color:#e0245e;"></i>' : '<i class="bi bi-heart"></i>';
        var heartClass = isShortlisted ? 'viewed-card-heart active wishlist-toggle' : 'viewed-card-heart wishlist-toggle';

        return '<div class="viewed-item-card">' +
            '<div class="viewed-card-thumb" style="background-image:url(\'' + esc(img) + '\');">' +
                '<button type="button" class="' + heartClass + '" data-project-id="' + v.id + '" data-type="vendor" aria-label="Wishlist">' + heartIcon + '</button>' +
                '<span class="viewed-card-price-badge">' + esc(priceTag) + '</span>' +
            '</div>' +
            '<a href="' + esc(url) + '" class="viewed-card-body text-decoration-none">' +
                '<div class="viewed-card-title">' + esc(name) + '</div>' +
                '<div class="viewed-card-sub">' + esc(rate) + ' ' + esc(exp) + '</div>' +
                '<div class="viewed-card-poster">' + esc(v.city || 'India') + ' · ' + esc(v.category || 'Services') + '</div>' +
            '</a>' +
        '</div>';
    }

    function renderServiceCard(s, isShortlisted) {
        var name = s.service_name || s.project_name || s.name || 'Local Service';
        var img = s.image
            ? (s.image.startsWith('http') || s.image.startsWith('../') || s.image.startsWith('/') ? s.image : '../admin/uploads/trending-projects/' + s.image)
            : 'https://placehold.co/400x300/0f223d/38bdf8?text=' + encodeURIComponent(name);
        var url = s.slug ? ('property-listing/' + encodeURIComponent(s.slug) + '/') : 'properties.php?type=local_services';
        var priceTag = s.price || '₹800/service';
        var subAddr = s.address ? formatAddrMini(s.address) : (s.city || 'India');
        var heartIcon = isShortlisted ? '<i class="bi bi-heart-fill" style="color:#e0245e;"></i>' : '<i class="bi bi-heart"></i>';
        var heartClass = isShortlisted ? 'viewed-card-heart active wishlist-toggle' : 'viewed-card-heart wishlist-toggle';

        return '<div class="viewed-item-card">' +
            '<div class="viewed-card-thumb" style="background-image:url(\'' + esc(img) + '\');">' +
                '<button type="button" class="' + heartClass + '" data-project-id="' + s.id + '" data-type="service" aria-label="Wishlist">' + heartIcon + '</button>' +
                '<span class="viewed-card-price-badge">' + esc(priceTag) + '</span>' +
            '</div>' +
            '<a href="' + esc(url) + '" class="viewed-card-body text-decoration-none">' +
                '<div class="viewed-card-title">' + esc(name) + '</div>' +
                '<div class="viewed-card-sub">In ' + esc(subAddr) + '</div>' +
                '<div class="viewed-card-poster"><i class="bi bi-tools me-1 text-warning"></i> Service</div>' +
            '</a>' +
        '</div>';
    }

    function isItemShortlisted(id, type) {
        var shortList = cached.shortlisted || readKey(SHORT_KEY) || [];
        var idNum = Number(id);
        return shortList.some(function(item) {
            var itId = (item && item.id != null) ? Number(item.id) : Number(item);
            return itId === idNum;
        });
    }

    function renderViewed(data) {
        if (!data || !data.length) {
            return emptyState('bi-eye',
                "You haven't viewed anything yet!",
                'Properties and services you open will show up here.');
        }

        var viewedProps = [];
        var viewedProjects = [];
        var viewedInteriors = [];
        var viewedVendors = [];
        var viewedServices = [];

        data.forEach(function(item) {
            var id = (item && item.id != null) ? item.id : item;
            var itemType = (item && item.type) ? item.type : '';

            if (itemType === 'interior' && interiorsById[id]) {
                viewedInteriors.push(interiorsById[id]);
            } else if (itemType === 'vendor' && vendorsById[id]) {
                viewedVendors.push(vendorsById[id]);
            } else if (itemType === 'service' && servicesById[id]) {
                viewedServices.push(servicesById[id]);
            } else if (byId[id]) {
                var p = byId[id];
                if (itemType === 'project' || (!p.bedrooms && p.category_type === 'project')) {
                    viewedProjects.push(p);
                } else if (p.category_type === 'local_services' || p.category_type === 'services' || p.category_type === 'service' || itemType === 'service') {
                    viewedServices.push(p);
                } else if (p.category_type === 'vendors' || p.category_type === 'vendor' || itemType === 'vendor') {
                    viewedVendors.push(p);
                } else if (p.category_type === 'interiors' || p.category_type === 'interior' || itemType === 'interior') {
                    viewedInteriors.push(p);
                } else {
                    viewedProps.push(p);
                }
            } else if (interiorsById[id]) {
                viewedInteriors.push(interiorsById[id]);
            } else if (vendorsById[id]) {
                viewedVendors.push(vendorsById[id]);
            } else if (servicesById[id]) {
                viewedServices.push(servicesById[id]);
            }
        });

        if (!viewedProps.length && !viewedProjects.length && !viewedInteriors.length && !viewedVendors.length && !viewedServices.length) {
            return emptyState('bi-eye',
                "You haven't viewed anything yet!",
                'Properties and services you open will show up here.');
        }

        var html = '<div class="viewed-sections-container">';

        // 1. Viewed Properties
        if (viewedProps.length) {
            html += '<div class="viewed-section-block">' +
                '<div class="viewed-section-header">' +
                    '<h2 class="viewed-section-title">Viewed Properties</h2>' +
                    '<p class="viewed-section-subtitle">Contact now to close the deal</p>' +
                '</div>' +
                '<div class="viewed-cards-grid">' +
                    viewedProps.map(function(p) { return renderPropertyCard(p, false, isItemShortlisted(p.id, 'property')); }).join('') +
                '</div>' +
            '</div>';
        }

        // 2. Viewed Projects
        if (viewedProjects.length) {
            html += '<div class="viewed-section-block">' +
                '<div class="viewed-section-header">' +
                    '<h2 class="viewed-section-title">Viewed Projects</h2>' +
                    '<p class="viewed-section-subtitle">Contact now to close the deal</p>' +
                '</div>' +
                '<div class="viewed-cards-grid">' +
                    viewedProjects.map(function(p) { return renderPropertyCard(p, true, isItemShortlisted(p.id, 'property')); }).join('') +
                '</div>' +
            '</div>';
        }

        // 3. Viewed Interiors
        if (viewedInteriors.length) {
            html += '<div class="viewed-section-block">' +
                '<div class="viewed-section-header">' +
                    '<h2 class="viewed-section-title">Viewed Interiors</h2>' +
                    '<p class="viewed-section-subtitle">Personalized interior designs and products you explored</p>' +
                '</div>' +
                '<div class="viewed-cards-grid">' +
                    viewedInteriors.map(function(it) { return renderInteriorCard(it, isItemShortlisted(it.id, 'interior')); }).join('') +
                '</div>' +
            '</div>';
        }

        // 4. Viewed Vendors
        if (viewedVendors.length) {
            html += '<div class="viewed-section-block">' +
                '<div class="viewed-section-header">' +
                    '<h2 class="viewed-section-title">Viewed Vendors</h2>' +
                    '<p class="viewed-section-subtitle">Contractors, architects and material experts you checked</p>' +
                '</div>' +
                '<div class="viewed-cards-grid">' +
                    viewedVendors.map(function(v) { return renderVendorCard(v, isItemShortlisted(v.id, 'vendor')); }).join('') +
                '</div>' +
            '</div>';
        }

        // 5. Viewed Local Services
        if (viewedServices.length) {
            html += '<div class="viewed-section-block">' +
                '<div class="viewed-section-header">' +
                    '<h2 class="viewed-section-title">Viewed Local Services</h2>' +
                    '<p class="viewed-section-subtitle">Home maintenance and verified service experts you viewed</p>' +
                '</div>' +
                '<div class="viewed-cards-grid">' +
                    viewedServices.map(function(s) { return renderServiceCard(s, isItemShortlisted(s.id, 'service')); }).join('') +
                '</div>' +
            '</div>';
        }

        html += '</div>';
        return html;
    }

    function renderShortlisted(data) {
        if (!data || !data.length) {
            return emptyState('bi-heart',
                "You haven't shortlisted anything yet!",
                'In case you have shortlisted something on another device/account, Login / Register to view them here');
        }

        var shortProps = [];
        var shortProjects = [];
        var shortInteriors = [];
        var shortVendors = [];
        var shortServices = [];

        data.forEach(function(item) {
            var id = (item && item.id != null) ? item.id : item;
            var itemType = (item && item.type) ? item.type : '';

            if (itemType === 'interior' && interiorsById[id]) {
                shortInteriors.push(interiorsById[id]);
            } else if (itemType === 'vendor' && vendorsById[id]) {
                shortVendors.push(vendorsById[id]);
            } else if (itemType === 'service' && servicesById[id]) {
                shortServices.push(servicesById[id]);
            } else if (byId[id]) {
                var p = byId[id];
                if (itemType === 'project' || (!p.bedrooms && p.category_type === 'project')) {
                    shortProjects.push(p);
                } else if (p.category_type === 'local_services' || p.category_type === 'services' || p.category_type === 'service' || itemType === 'service') {
                    shortServices.push(p);
                } else if (p.category_type === 'vendors' || p.category_type === 'vendor' || itemType === 'vendor') {
                    shortVendors.push(p);
                } else if (p.category_type === 'interiors' || p.category_type === 'interior' || itemType === 'interior') {
                    shortInteriors.push(p);
                } else {
                    shortProps.push(p);
                }
            } else if (interiorsById[id]) {
                shortInteriors.push(interiorsById[id]);
            } else if (vendorsById[id]) {
                shortVendors.push(vendorsById[id]);
            } else if (servicesById[id]) {
                shortServices.push(servicesById[id]);
            }
        });

        if (!shortProps.length && !shortProjects.length && !shortInteriors.length && !shortVendors.length && !shortServices.length) {
            return emptyState('bi-heart',
                "You haven't shortlisted anything yet!",
                'In case you have shortlisted something on another device/account, Login / Register to view them here');
        }

        var html = '<div class="viewed-sections-container">';

        // 1. Shortlisted Properties
        if (shortProps.length) {
            html += '<div class="viewed-section-block">' +
                '<div class="viewed-section-header">' +
                    '<h2 class="viewed-section-title">Shortlisted Properties</h2>' +
                    '<p class="viewed-section-subtitle">Contact now to close the deal</p>' +
                '</div>' +
                '<div class="viewed-cards-grid">' +
                    shortProps.map(function(p) { return renderPropertyCard(p, false, true); }).join('') +
                '</div>' +
            '</div>';
        }

        // 2. Shortlisted Projects
        if (shortProjects.length) {
            html += '<div class="viewed-section-block">' +
                '<div class="viewed-section-header">' +
                    '<h2 class="viewed-section-title">Shortlisted Projects</h2>' +
                    '<p class="viewed-section-subtitle">Contact now to close the deal</p>' +
                '</div>' +
                '<div class="viewed-cards-grid">' +
                    shortProjects.map(function(p) { return renderPropertyCard(p, true, true); }).join('') +
                '</div>' +
            '</div>';
        }

        // 3. Shortlisted Interiors
        if (shortInteriors.length) {
            html += '<div class="viewed-section-block">' +
                '<div class="viewed-section-header">' +
                    '<h2 class="viewed-section-title">Shortlisted Interiors</h2>' +
                    '<p class="viewed-section-subtitle">Personalized interior designs and products you shortlisted</p>' +
                '</div>' +
                '<div class="viewed-cards-grid">' +
                    shortInteriors.map(function(it) { return renderInteriorCard(it, true); }).join('') +
                '</div>' +
            '</div>';
        }

        // 4. Shortlisted Vendors
        if (shortVendors.length) {
            html += '<div class="viewed-section-block">' +
                '<div class="viewed-section-header">' +
                    '<h2 class="viewed-section-title">Shortlisted Vendors</h2>' +
                    '<p class="viewed-section-subtitle">Contractors, architects and material experts you shortlisted</p>' +
                '</div>' +
                '<div class="viewed-cards-grid">' +
                    shortVendors.map(function(v) { return renderVendorCard(v, true); }).join('') +
                '</div>' +
            '</div>';
        }

        // 5. Shortlisted Local Services
        if (shortServices.length) {
            html += '<div class="viewed-section-block">' +
                '<div class="viewed-section-header">' +
                    '<h2 class="viewed-section-title">Shortlisted Local Services</h2>' +
                    '<p class="viewed-section-subtitle">Home maintenance and verified service experts you shortlisted</p>' +
                '</div>' +
                '<div class="viewed-cards-grid">' +
                    shortServices.map(function(s) { return renderServiceCard(s, true); }).join('') +
                '</div>' +
            '</div>';
        }

        html += '</div>';
        return html;
    }

    function renderContactedCard(c) {
        var pid = c.property_id || c.page_id || (c.type === 'property' ? c.id : null);
        var p = pid ? byId[pid] : null;

        var name = p ? p.project_name : (c.project_name || c.page_title || c.title || 'Contacted Property');
        var img = p && p.image
            ? '../backend/uploads/trending-projects/' + p.image
            : (c.image ? '../backend/uploads/trending-projects/' + c.image : 'https://placehold.co/400x300/12213a/c9a84c?text=' + encodeURIComponent(name));
        var url = p && p.slug ? 'property-listing.php?slug=' + encodeURIComponent(p.slug) : (c.slug ? 'property-listing.php?slug=' + encodeURIComponent(c.slug) : 'properties.php');
        var displayPrice = p ? price(p) : (c.price || 'Price on Request');
        var subAddr = p && p.address ? formatAddrMini(p.address) : (c.address ? formatAddrMini(c.address) : (p && p.city ? p.city : (c.city || 'India')));
        var poster = (p && p.posted_by) ? p.posted_by : (p && p.bedrooms ? 'Owner' : 'Builder');
        var status = c.status || 'Contacted';
        var actionText = c.action_label || (c.appointment_date ? 'Site visit requested' : 'Contacted Builder');
        var dateStr = c.created_at ? new Date(c.created_at).toLocaleDateString('en-IN', { day: 'numeric', month: 'short' }) : 'Recently';

        return '<div class="viewed-item-card contacted-card">' +
            '<div class="viewed-card-thumb" style="background-image:url(\'' + esc(img) + '\');">' +
                '<span class="contacted-status-tag"><i class="bi bi-telephone-outbound me-1"></i>' + esc(status) + '</span>' +
                '<span class="viewed-card-price-badge">' + esc(displayPrice) + '</span>' +
            '</div>' +
            '<a href="' + esc(url) + '" class="viewed-card-body text-decoration-none">' +
                '<div class="viewed-card-title">' + esc(name) + '</div>' +
                '<div class="viewed-card-sub">In ' + esc(subAddr) + '</div>' +
                '<div class="contacted-action-info">' + esc(actionText) + ' &middot; ' + esc(dateStr) + '</div>' +
                '<div class="viewed-card-poster">Posted by ' + esc(poster) + '</div>' +
            '</a>' +
        '</div>';
    }

    function renderContacted(data) {
        if (!data || !data.length) {
            return emptyState('bi-chat-dots',
                "You haven't contacted anyone lately!",
                'You will see the list of properties / projects here, where you have contacted the advertiser');
        }

        var contactCards = [];
        var genericEnquiries = [];

        data.forEach(function(c) {
            if (c.kind === 'enquiry') {
                genericEnquiries.push(c);
            } else {
                var pid = c.property_id || c.page_id || (c.type === 'property' ? c.id : null);
                if (pid || c.project_name || c.page_title) {
                    contactCards.push(c);
                } else {
                    genericEnquiries.push(c);
                }
            }
        });

        var html = '';

        if (contactCards.length) {
            html += '<div class="viewed-sections-container">' +
                '<div class="viewed-section-block">' +
                    '<div class="viewed-section-header">' +
                        '<h2 class="viewed-section-title">Contacted Properties & Projects</h2>' +
                        '<p class="viewed-section-subtitle">Advertisers and sellers you reached out to</p>' +
                    '</div>' +
                    '<div class="viewed-cards-grid">' +
                        contactCards.map(function(c) { return renderContactedCard(c); }).join('') +
                    '</div>' +
                '</div>' +
            '</div>';
        }

        if (genericEnquiries.length) {
            html += '<div class="viewed-sections-container mt-4">' +
                '<div class="viewed-section-block">' +
                    '<div class="viewed-section-header">' +
                        '<h2 class="viewed-section-title">General Requests & Callbacks</h2>' +
                        '<p class="viewed-section-subtitle">Callback requests and custom property requirement leads</p>' +
                    '</div>' +
                    '<div class="viewed-cards-grid">' +
                        genericEnquiries.map(function (c) {
                            var status = c.status || 'New';
                            var dateStr = c.created_at ? new Date(c.created_at).toLocaleDateString('en-IN', { day: 'numeric', month: 'short' }) : 'Recently';
                            var title = c.label || 'Custom Property Enquiry';
                            var sub = c.summary || c.preferred_locality || 'Requirement submitted';
                            var priceTag = c.budget_range || 'Custom Budget';
                            var img = 'https://placehold.co/400x300/12213a/c9a84c?text=' + encodeURIComponent('Enquiry Lead');

                            return '<div class="viewed-item-card contacted-card">' +
                                '<div class="viewed-card-thumb" style="background-image:url(\'' + esc(img) + '\');">' +
                                    '<span class="contacted-status-tag"><i class="bi bi-chat-left-dots me-1"></i>' + esc(status) + '</span>' +
                                    '<span class="viewed-card-price-badge">' + esc(priceTag) + '</span>' +
                                '</div>' +
                                '<div class="viewed-card-body">' +
                                    '<div class="viewed-card-title">' + esc(title) + '</div>' +
                                    '<div class="viewed-card-sub">' + esc(sub) + '</div>' +
                                    '<div class="contacted-action-info"><i class="bi bi-clock me-1"></i>Submitted &middot; ' + esc(dateStr) + '</div>' +
                                    '<div class="viewed-card-poster">Status: ' + esc(status) + '</div>' +
                                '</div>' +
                            '</div>';
                        }).join('') +
                    '</div>' +
                '</div>' +
            '</div>';
        }

        return html;
    }

    var renderers = {
        searches:    renderSearches,
        viewed:      renderViewed,
        shortlisted: renderShortlisted,
        contacted:   renderContacted
    };

    // ===== tab logic =====
    var current;

    function moveIndicator() {
        var tabs = document.querySelector('.act-tabs');
        var active = tabs.querySelector('.act-tab.active');
        var ind = document.getElementById('tab-indicator');
        if (!active || !ind) return;
        ind.style.left = active.offsetLeft + 'px';
        ind.style.width = active.offsetWidth + 'px';
    }

    function setActiveTab(tab) {
        document.querySelectorAll('.act-tab').forEach(function (btn) {
            var active = btn.getAttribute('data-tab') === tab;
            btn.classList.toggle('active', active);
            btn.setAttribute('aria-selected', active ? 'true' : 'false');
        });
        document.querySelectorAll('.act-panel').forEach(function (p) {
            p.classList.toggle('d-none', p.id !== 'panel-' + tab);
        });
        current = tab;
        moveIndicator();
    }

    function renderCurrent() {
        var panel = document.getElementById('panel-' + current);
        panel.classList.remove('is-centered');
        panel.innerHTML = panelTemplate();
        panel.classList.add('act-panel-enter');

        setTimeout(function () {
            var data = cached[current];
            if (data === null) {
                panel.innerHTML = errorState();
                panel.classList.add('is-centered');
                return;
            }
            var html = renderers[current](data);
            panel.innerHTML = html;
            if (!html.trim() || html.indexOf('empty-state') !== -1 || html.indexOf('act-error') !== -1) {
                panel.classList.add('is-centered');
            }
        }, LOAD_MS);
    }

    function refreshCurrent() {
        try { cached[current] = sources[current](); } catch (e) { cached[current] = null; }
        counts[current] = cached[current] ? cached[current].length : 0;
        renderCounts();
        renderCurrent();
    }

    document.querySelectorAll('.act-tab').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (btn.getAttribute('aria-selected') === 'true') return;
            setActiveTab(btn.getAttribute('data-tab'));
            renderCurrent();
        });
    });

    // retry inside an error state and wishlist toggles
    document.addEventListener('click', function (e) {
        var retry = e.target.closest('[data-retry]');
        if (retry) refreshCurrent();

        var heart = e.target.closest('.viewed-card-heart, .wishlist-toggle');
        if (heart) {
            e.preventDefault();
            e.stopPropagation();
            var pid = parseInt(heart.getAttribute('data-project-id'), 10);
            var itemType = heart.getAttribute('data-type') || 'property';
            if (pid) {
                var currentList = readKey(SHORT_KEY);
                var isAlreadyShortlisted = false;

                for (var i = 0; i < currentList.length; i++) {
                    var it = currentList[i];
                    var itId = (it && it.id != null) ? parseInt(it.id, 10) : parseInt(it, 10);
                    if (itId === pid) {
                        isAlreadyShortlisted = true;
                        break;
                    }
                }

                if (isAlreadyShortlisted) {
                    // Remove all entries matching this ID
                    currentList = currentList.filter(function(it) {
                        var itId = (it && it.id != null) ? parseInt(it.id, 10) : parseInt(it, 10);
                        return itId !== pid;
                    });
                    heart.classList.remove('active');
                    var icon = heart.querySelector('i');
                    if (icon) {
                        icon.className = 'bi bi-heart';
                        icon.style.color = '';
                    }
                    if (current === 'shortlisted') {
                        var card = heart.closest('.viewed-item-card');
                        if (card) {
                            card.style.transition = 'all 0.25s ease';
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.95)';
                        }
                    }
                } else {
                    currentList.unshift({ id: pid, type: itemType });
                    heart.classList.add('active');
                    var icon = heart.querySelector('i');
                    if (icon) {
                        icon.className = 'bi bi-heart-fill';
                        icon.style.color = '#e0245e';
                    }
                }
                saveShortlist(currentList);
                cached.shortlisted = currentList;
                counts.shortlisted = currentList.length;
                renderCounts();
                if (current === 'shortlisted') {
                    setTimeout(renderCurrent, 250);
                }
            }
            return;
        }

        var remove = e.target.closest('.remove-short');
        if (remove && current === 'shortlisted') {
            var id = parseInt(remove.getAttribute('data-id'), 10);
            var list = (cached.shortlisted || []).filter(function (x) { return parseInt(x, 10) !== id; });
            saveShortlist(list);
            refreshCurrent();
        }
    });

    loadAll();
    renderCounts();
    var initialTab = 'searches';
    try {
        var params = new URLSearchParams(window.location.search);
        var tabParam = params.get('tab') || params.get('activeTab');
        if (tabParam) {
            tabParam = tabParam.toLowerCase();
            if (tabParam === 'recent_search' || tabParam === 'recent_searches' || tabParam === 'recent-search' || tabParam === 'searches') {
                initialTab = 'searches';
            } else if (tabParam === 'viewed' || tabParam === 'recent_viewed') {
                initialTab = 'viewed';
            } else if (tabParam === 'shortlisted' || tabParam === 'wishlist') {
                initialTab = 'shortlisted';
            } else if (tabParam === 'contacted' || tabParam === 'enquiries') {
                initialTab = 'contacted';
            } else if (sources.hasOwnProperty(tabParam)) {
                initialTab = tabParam;
            }
        }
    } catch (e) {}
    setActiveTab(initialTab);
    renderCurrent();
    window.addEventListener('resize', moveIndicator);
    window.addEventListener('load', moveIndicator);
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>