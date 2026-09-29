<?php
$current_page = basename($_SERVER['PHP_SELF']);
$param_p = isset($_GET['p']) ? trim((string) $_GET['p']) : '';

function active($page)
{
    global $current_page;
    return ($page === $current_page) ? "active" : "";
}

function active_soon($label)
{
    global $current_page, $param_p;
    return ($current_page === 'coming-soon.php' && $param_p === $label) ? "active" : "";
}

/**
 * Sidebar model — single source of truth for the whole admin menu.
 * Menu entries (in order):
 *   group  -> has 'id' + 'icon' + 'label' + 'items'
 *   flat   -> has 'page' (real page) or 'soon' (placeholder label) + 'icon' + 'label'
 * Items inside a group may contain another collapsible via 'nested' + 'items'.
 */

$nav = [

    ['page' => 'dashboard.php', 'label' => 'Dashboard', 'icon' => 'bi-speedometer'],

    [
        'id'    => 'content',
        'icon'  => 'bi-file-earmark-text',
        'label' => 'Content Management',
        'items' => [
            [
                'nested' => 'homepage',
                'label'  => 'Homepage',
                'icon'   => 'bi-circle fs-9 fs-small-dot',
                'items'  => [
                    ['page' => 'banner.php', 'label' => 'Banner', 'icon' => 'bi-dash'],
                    ['page' => 'city.php',   'label' => 'City',   'icon' => 'bi-dash'],
                ],
            ],
            ['page' => 'about.php',  'label' => 'About Us'],
            ['soon' => 'Reviews',    'label' => 'Reviews & Testimonials', 'icon' => 'bi-star'],
        ],
    ],

    [
        'id'    => 'members',
        'icon'  => 'bi-people',
        'label' => 'Members',
        'items' => [
            ['soon' => 'Free Member',                 'label' => 'Free Member'],
            ['soon' => 'Premium Member',              'label' => 'Premium Member'],
            ['soon' => 'Bulk Member Add',             'label' => 'Bulk Member Add'],
            ['soon' => 'Deleted Member',              'label' => 'Deleted Member'],
            ['soon' => 'Reported Member',             'label' => 'Reported Member'],
            ['soon' => 'Unapproved Profile Pictures', 'label' => 'Unapproved Profile Pictures'],
        ],
    ],

    [
        'id'    => 'profile_attrs',
        'icon'  => 'bi-person-lines-fill',
        'label' => 'Profile Attributes',
        'items' => [
            ['soon' => 'Religions',               'label' => 'Religions'],
            ['soon' => 'Caste',                   'label' => 'Caste'],
            ['soon' => 'Sub Caste',               'label' => 'Sub Caste'],
            ['soon' => 'Member Language',         'label' => 'Member Language'],
            ['soon' => 'Country State City',      'label' => 'Country State City'],
            ['soon' => 'On Behalf Family Values', 'label' => 'On Behalf Family Values'],
            ['soon' => 'Family Status',           'label' => 'Family Status'],
            ['soon' => 'Material Status',         'label' => 'Material Status'],
        ],
    ],

    ['soon' => 'Profile Sections', 'label' => 'Profile Sections', 'icon' => 'bi-layout-sections'],

    [
        'id'    => 'packages',
        'icon'  => 'bi-box-seam',
        'label' => 'Packages',
        'items' => [
            ['soon' => 'Premium Packages', 'label' => 'Premium Packages'],
            ['soon' => 'Package Payments', 'label' => 'Package Payments'],
        ],
    ],

    [
        'id'    => 'wallet',
        'icon'  => 'bi-wallet2',
        'label' => 'Wallet',
        'items' => [
            ['soon' => 'Wallet',                    'label' => 'Wallet'],
            ['soon' => 'Wallet Transaction History', 'label' => 'Wallet Transaction History'],
            ['soon' => 'Manual Wallet Recharge Request', 'label' => 'Manual Wallet Recharge Request'],
        ],
    ],

    ['soon' => 'Happy Stories', 'label' => 'Happy Stories', 'icon' => 'bi-emoji-smile'],

    [
        'id'    => 'blog',
        'icon'  => 'bi-journal-text',
        'label' => 'Blog System',
        'items' => [
            ['soon' => 'Blog Categories', 'label' => 'Categories'],
            ['page' => 'blog.php',        'label' => 'All Posts'],
        ],
    ],

    [
        'id'    => 'marketing',
        'icon'  => 'bi-megaphone',
        'label' => 'Marketing',
        'items' => [
            ['soon' => 'Newsletter', 'label' => 'Newsletter'],
        ],
    ],

    [
        'id'    => 'support',
        'icon'  => 'bi-life-preserver',
        'label' => 'Support Ticket',
        'items' => [
            ['soon' => 'Active Tickets', 'label' => 'Active Tickets'],
            ['soon' => 'My Tickets',     'label' => 'My Tickets'],
            ['soon' => 'Solved Tickets', 'label' => 'Solved Tickets'],
            [
                'nested' => 'support_settings',
                'label'  => 'Support Settings',
                'icon'   => 'bi-circle fs-9 fs-small-dot',
                'items'  => [
                    ['soon' => 'Support Category',       'label' => 'Support Category'],
                    ['soon' => 'Default Assigned Agent', 'label' => 'Default Assigned Agent'],
                    ['soon' => 'Uploaded Files',         'label' => 'Uploaded Files'],
                ],
            ],
        ],
    ],

    [
        'id'    => 'setup',
        'icon'  => 'bi-gear',
        'label' => 'Website Setup',
        'items' => [
            ['page' => 'header-settings.php', 'label' => 'Header'],
            ['page' => 'footer-settings.php', 'label' => 'Footer'],
            ['page' => 'menu.php',            'label' => 'Menu'],
            ['page' => 'submenu.php',         'label' => 'Submenu'],
            ['page' => 'property-categories.php', 'label' => 'Property Categories'],
            ['soon' => 'Pages',               'label' => 'Pages'],
            ['soon' => 'Appearance Settings', 'label' => 'Appearance Settings'],
            ['soon' => 'General Settings',    'label' => 'General Settings'],
            ['soon' => 'Language',            'label' => 'Language'],
            ['soon' => 'Currency',            'label' => 'Currency'],
            ['soon' => 'Payment Methods',     'label' => 'Payment Methods'],
            ['soon' => 'SMTP Settings',       'label' => 'SMTP Settings'],
            ['soon' => 'Email Templates',     'label' => 'Email Templates'],
            ['soon' => 'Third Party Settings','label' => 'Third Party Settings'],
            ['soon' => 'Social Media Login',  'label' => 'Social Media Login'],
            ['soon' => 'Firebase Push Notification', 'label' => 'Firebase Push Notification'],
        ],
    ],

    [
        'id'    => 'employee',
        'icon'  => 'bi-person-badge',
        'label' => 'Employee Dashboard',
        'items' => [
            ['page' => 'dashboard.php', 'label' => 'Dashboard', 'skip_open' => true],
            ['soon' => 'Member List',   'label' => 'Member List'],
            ['soon' => 'Pending Approvals', 'label' => 'Pending Approvals'],
            [
                'nested' => 'employee_support',
                'label'  => 'Support Ticket',
                'icon'   => 'bi-dash',
                'items'  => [
                    ['soon' => 'Active Tickets', 'label' => 'Active Tickets'],
                    ['soon' => 'Solved Tickets', 'label' => 'Solved Tickets'],
                ],
            ],
            ['soon' => 'Settings', 'label' => 'Settings'],
        ],
    ],

    [
        'id'    => 'properties',
        'icon'  => 'bi-folder-check',
        'label' => 'Properties',
        'items' => [
            ['page' => 'trending-projects.php', 'label' => 'Property'],
        ],
    ],

    ['soon' => 'Enquiries', 'label' => 'Enquiries', 'icon' => 'bi-chat-left-dots'],

    [
        'id'    => 'otp',
        'icon'  => 'bi-shield-check',
        'label' => 'OTP System',
        'items' => [
            ['soon' => 'OTP Credentials', 'label' => 'OTP Credentials'],
            ['soon' => 'Send Bulk SMS',   'label' => 'Send Bulk SMS'],
        ],
    ],

    [
        'id'    => 'calls',
        'icon'  => 'bi-camera-video',
        'label' => 'Video/Audio Calls',
        'items' => [
            ['soon' => 'Call Settings', 'label' => 'Call Settings'],
        ],
    ],

    [
        'id'    => 'staff',
        'icon'  => 'bi-person-gear',
        'label' => 'Staff & Roles',
        'items' => [
            ['soon' => 'All Staffs',  'label' => 'All Staffs'],
            ['soon' => 'Staff Roles', 'label' => 'Staff Roles'],
        ],
    ],

    [
        'id'    => 'system',
        'icon'  => 'bi-arrow-repeat',
        'label' => 'System Update',
        'items' => [
            ['soon' => 'Server Status', 'label' => 'Server Status'],
        ],
    ],

    ['soon' => 'Addon Manager', 'label' => 'Addon Manager', 'icon' => 'bi-puzzle'],
    ['page' => 'profile.php',   'label' => 'My Profile',   'icon' => 'bi-person-circle'],
];

/* ============================================================
 * Render helpers
 * ============================================================ */

function sidebar_nav_active($item)
{
    global $current_page, $param_p;
    if (!empty($item['page'])) {
        return $item['page'] === $current_page;
    }
    if (!empty($item['soon'])) {
        return $current_page === 'coming-soon.php' && $param_p === $item['soon'];
    }
    if (!empty($item['nested'])) {
        foreach ($item['items'] as $child) {
            if (sidebar_nav_active($child)) {
                return true;
            }
        }
    }
    return false;
}

function sidebar_nav_href($item)
{
    if (!empty($item['page'])) {
        return $item['page'];
    }
    return 'coming-soon.php?p=' . rawurlencode($item['soon']);
}

function sidebar_render_flat($item)
{
    if (!empty($item['page'])) {
        $href = $item['page'];
        $cls  = active($item['page']);
    } else {
        $href = 'coming-soon.php?p=' . rawurlencode($item['soon']);
        $cls  = active_soon($item['soon']);
    }
    $icon = $item['icon'] ?? 'bi-circle fs-9 fs-small-dot';
    return '<li class="nav-item">'
         . '<a href="' . htmlspecialchars($href, ENT_QUOTES) . '" class="nav-link ' . $cls . '">'
         . '<i class="nav-icon ' . $icon . '"></i>'
         . '<p class="fs-7 mb-0">' . htmlspecialchars($item['label']) . '</p></a></li>';
}

function sidebar_render_nested($item, $prefix)
{
    $id   = $prefix . '_' . $item['nested'];
    $open = sidebar_nav_active($item);
    $icon = $item['icon'] ?? 'bi-dash';
    $html = '<li class="nav-item ' . ($open ? 'menu-open' : '') . '" id="' . $id . 'MenuItem">'
          . '<a href="javascript:void(0);" class="nav-link ' . ($open ? 'active' : '') . '" id="' . $id . 'Toggle">'
          . '<i class="nav-icon ' . $icon . '"></i>'
          . '<p class="fs-7 mb-0 d-flex justify-content-between align-items-center w-100">'
          . '<span>' . htmlspecialchars($item['label']) . '</span><i class="bi bi-chevron-right"></i>'
          . '</p></a>'
          . '<ul class="nav nav-treeview ' . ($open ? 'menu-is-open' : '') . '" id="' . $id . 'Submenu">';
    foreach ($item['items'] as $child) {
        if (isset($child['nested'])) {
            $html .= sidebar_render_nested($child, $id);
        } else {
            $html .= sidebar_render_flat($child);
        }
    }
    return $html . '</ul></li>';
}

function sidebar_render_group($group)
{
    $open = false;
    foreach ($group['items'] as $child) {
        if (!empty($child['skip_open'])) {
            continue;
        }
        if (sidebar_nav_active($child)) {
            $open = true;
            break;
        }
    }
    $id = $group['id'];
    $html = '<li class="nav-item ' . ($open ? 'menu-open' : '') . '" id="' . $id . 'MenuItem">'
          . '<a href="javascript:void(0);" class="nav-link ' . ($open ? 'active' : '') . '" id="' . $id . 'Toggle">'
          . '<i class="nav-icon ' . $group['icon'] . '"></i>'
          . '<p class="fs-7 mb-0 d-flex justify-content-between align-items-center w-100">'
          . '<span>' . htmlspecialchars($group['label']) . '</span><i class="bi bi-chevron-right"></i>'
          . '</p></a>'
          . '<ul class="nav nav-treeview ' . ($open ? 'menu-is-open' : '') . '" id="' . $id . 'Submenu">';
    foreach ($group['items'] as $child) {
        if (isset($child['nested'])) {
            $html .= sidebar_render_nested($child, $id);
        } else {
            $html .= sidebar_render_flat($child);
        }
    }
    return $html . '</ul></li>';
}

/* ============================================================
 * Accordion ids for the JS bindings
 * ============================================================ */
$sidebar_top_ids = [];
$sidebar_nested_ids = [];
foreach ($nav as $entry) {
    if (!isset($entry['items'])) {
        continue;
    }
    $sidebar_top_ids[] = $entry['id'];
    foreach ($entry['items'] as $child) {
        if (isset($child['nested'])) {
            $sidebar_nested_ids[] = $entry['id'] . '_' . $child['nested'];
        }
    }
}
?>

<!-- Smooth Slide CSS -->
<style>
    .nav-treeview {
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        transition: max-height 0.35s cubic-bezier(0, 1, 0, 1), opacity 0.25s ease-in-out;
    }

    .nav-treeview.menu-is-open {
        max-height: 3000px; /* Tall enough for the largest group (Website Setup) */
        opacity: 1;
        transition: max-height 0.45s ease-in-out, opacity 0.25s ease-in-out;
    }

    /* Chevron rotation */
    .bi-chevron-right {
        transition: transform 0.3s ease;
    }

    .menu-open > .nav-link .bi-chevron-right {
        transform: rotate(90deg);
    }

    /* Custom small icon class */
    .fs-small-dot {
        font-size: 0.45rem !important;
        vertical-align: middle;
    }

    /* Second-level nesting (e.g. Content > Homepage > Banner/City) gets extra indent + smaller marker */
    .nav-treeview .nav-treeview {
        padding-left: 1rem;
    }
    .nav-treeview .nav-treeview .nav-link i.bi-dash {
        font-size: 0.7rem;
        vertical-align: middle;
    }
</style>

<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark" style="background-color: #141423 !important;">

    <div class="sidebar-brand">
        <a href="dashboard.php" class="brand-link">
            <img src="assets/img/logo.jpeg" style="width:50px;height:50px;object-fit:cover;">
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav>
            <ul class="nav sidebar-menu flex-column">
                <?php foreach ($nav as $entry): ?>
                    <?= isset($entry['items']) ? sidebar_render_group($entry) : sidebar_render_flat($entry) ?>
                <?php endforeach; ?>

                <!-- Logout -->
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i class="nav-icon bi bi-box-arrow-right"></i>
                        <p class="fs-7 mb-0">Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // Top-level accordion ids rendered by PHP
    const topGroupIds = <?= json_encode($sidebar_top_ids) ?>;
    // Nested accordion ids rendered by PHP
    const nestedGroupIds = <?= json_encode($sidebar_nested_ids) ?>;

    function el(id) { return document.getElementById(id); }

    function closeGroups(groups, exceptSubmenuId) {
        groups.forEach(function (id) {
            const submenuId = id + "Submenu";
            if (submenuId === exceptSubmenuId) return;
            const submenu = el(submenuId);
            const parent  = el(id + "MenuItem");
            if (!submenu || !parent) return;
            submenu.classList.remove("menu-is-open");
            parent.classList.remove("menu-open");
        });
    }

    function bindTopSubmenu(id) {
        const toggle  = el(id + "Toggle");
        const submenu = el(id + "Submenu");
        const parent  = el(id + "MenuItem");
        if (!toggle || !submenu || !parent) return;

        toggle.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();

            const isAlreadyOpen = submenu.classList.contains("menu-is-open");

            // Close every other open top-level submenu first (accordion behavior)
            closeGroups(topGroupIds, id + "Submenu");

            if (isAlreadyOpen) {
                submenu.classList.remove("menu-is-open");
                parent.classList.remove("menu-open");
                closeGroups(nestedGroupIds, null); // also collapse any nested children
            } else {
                submenu.classList.add("menu-is-open");
                parent.classList.add("menu-open");
            }
        });
    }

    function bindNestedSubmenu(id) {
        const toggle  = el(id + "Toggle");
        const submenu = el(id + "Submenu");
        const parent  = el(id + "MenuItem");
        if (!toggle || !submenu || !parent) return;

        toggle.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();

            const isAlreadyOpen = submenu.classList.contains("menu-is-open");
            if (isAlreadyOpen) {
                submenu.classList.remove("menu-is-open");
                parent.classList.remove("menu-open");
            } else {
                submenu.classList.add("menu-is-open");
                parent.classList.add("menu-open");
            }
        });
    }

    topGroupIds.forEach(bindTopSubmenu);
    nestedGroupIds.forEach(bindNestedSubmenu);

});
</script>