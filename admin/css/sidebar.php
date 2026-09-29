<?php
$current_page = basename($_SERVER['PHP_SELF']);
$user_role = $_SESSION['role'] ?? 'user';

$pages_children = [
    'header-settings.php',
    'footer-settings.php',
    'menu.php',
    'submenu.php',
    'banner.php',
    'city.php',
    'trending-projects.php'
];

$pages_open = in_array($current_page, $pages_children);

function active($page)
{
    global $current_page;
    return ($page == $current_page) ? "active" : "";
}
?>

<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">

    <div class="sidebar-brand">
        <a href="dashboard.php" class="brand-link">
            <img src="assets/img/logo.jpeg"
                 style="width:50px;height:50px;object-fit:cover;">
        </a>
    </div>

    <div class="sidebar-wrapper">

        <nav>

            <ul class="nav sidebar-menu flex-column" data-accordion="false">

                <li class="nav-item">
                    <a href="dashboard.php"
                       class="nav-link <?= active('dashboard.php') ?>">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p class="fs-7 mb-0">Dashboard</p>
                    </a>
                </li>

                <?php if($user_role=="admin"){ ?>

                <li class="nav-item <?= $pages_open ? 'menu-open' : '' ?>" id="pagesMenuItem">

                    <a href="#"
                       class="nav-link <?= $pages_open ? 'active' : '' ?>"
                       id="pagesToggle">

                        <i class="nav-icon bi bi-file-earmark-text"></i>

                        <p class="fs-7 mb-0 d-flex justify-content-between align-items-center w-100">

                            <span>Pages</span>

                            <i id="pagesArrow"
                               class="bi <?= $pages_open ? 'bi-chevron-down' : 'bi-chevron-right' ?>"></i>

                        </p>

                    </a>

                    <ul class="nav nav-treeview"
                        id="pagesSubmenu"
                        style="display: <?= $pages_open ? 'block' : 'none' ?>;">

                        <li class="nav-item">
                            <a href="header-settings.php"
                               class="nav-link <?= active('header-settings.php') ?>">
                                <i class="nav-icon bi bi-circle" style="font-size:6px !important;"></i>
                                <p class="fs-7 mb-0">Header Settings</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="footer-settings.php"
                               class="nav-link <?= active('footer-settings.php') ?>">
                                <i class="nav-icon bi bi-circle" style="font-size:6px !important;"></i>
                                <p class="fs-7 mb-0">Footer Settings</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="menu.php"
                               class="nav-link <?= active('menu.php') ?>">
                                <i class="nav-icon bi bi-circle" style="font-size:6px !important;"></i>
                                <p class="fs-7 mb-0">Menu</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="submenu.php"
                               class="nav-link <?= active('submenu.php') ?>">
                                <i class="nav-icon bi bi-circle" style="font-size:6px !important;"></i>
                                <p class="fs-7 mb-0">Submenu</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="banner.php"
                               class="nav-link <?= active('banner.php') ?>">
                                <i class="nav-icon bi bi-circle" style="font-size:6px !important;"></i>
                                <p class="fs-7 mb-0">Banner</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="city.php"
                               class="nav-link <?= active('city.php') ?>">
                                <i class="nav-icon bi bi-circle" style="font-size:6px !important;"></i>
                                <p class="fs-7 mb-0">City</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="trending-projects.php"
                               class="nav-link <?= active('trending-projects.php') ?>">
                                <i class="nav-icon bi bi-circle" style="font-size:6px !important;"></i>
                                <p class="fs-7 mb-0">Trending Project</p>
                            </a>
                        </li>

                    </ul>

                </li>

                <?php } ?>

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

    const toggle = document.getElementById("pagesToggle");
    const submenu = document.getElementById("pagesSubmenu");
    const arrow = document.getElementById("pagesArrow");
    const parent = document.getElementById("pagesMenuItem");

    if (!toggle || !submenu) return;

    toggle.addEventListener("click", function (e) {

        e.preventDefault();

        const isOpen = submenu.style.display === "block";

        if (isOpen) {

            submenu.style.display = "none";
            parent.classList.remove("menu-open");
            toggle.classList.remove("active");

            if (arrow) {
                arrow.classList.remove("bi-chevron-down");
                arrow.classList.add("bi-chevron-right");
            }

        } else {

            submenu.style.display = "block";
            parent.classList.add("menu-open");
            toggle.classList.add("active");

            if (arrow) {
                arrow.classList.remove("bi-chevron-right");
                arrow.classList.add("bi-chevron-down");
            }

        }

    });

});
</script>