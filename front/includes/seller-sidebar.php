<?php
/**
 * Reusable Seller Sidebar Navigation Panel
 * Parameters:
 *  - $activeSellerMenu: identifier for the current page
 */
$activeSellerMenu = $activeSellerMenu ?? 'mytatito';
$sellerUserName   = $_SESSION['user_name'] ?? $_SESSION['fullname'] ?? 'Kruthika';
$sellerUserEmail  = $_SESSION['user_email'] ?? $_SESSION['username'] ?? '';

$isListingsOpen = in_array($activeSellerMenu, [
    'all-listings', 'manage-listings', 'plain-listings', 'platinum-listings', 'premium-listings', 'infinity-listings', 'ai-listings'
]);

$isProjectsOpen = in_array($activeSellerMenu, [
    'manage-projects', 'super-campaign', 'bbc', 'sab', 'np-fp', 'emailers', 'omni-ads'
]);

$isRespListingsOpen = in_array($activeSellerMenu, [
    'response-listings', 'response-plain', 'response-platinum', 'response-premium', 'response-infinity', 'response-ai'
]);

$isRespProjectsOpen = in_array($activeSellerMenu, [
    'response-projects', 'response-super-campaign', 'response-bbc', 'response-sab', 'response-np-fp', 'response-emailers', 'response-omni-ads'
]);

$isExpertPacksOpen = ($activeSellerMenu === 'all-leads');
?>

<!-- Mobile Sidebar Drawer Toggle Button -->
<button class="seller-mobile-toggle" type="button" id="sellerMobileToggleBtn">
    <i class="bi bi-layout-sidebar-inset"></i>
    <span>Seller Menu</span>
</button>

<div class="seller-sidebar-backdrop" id="sellerSidebarBackdrop"></div>

<aside class="seller-sidebar" id="sellerSidebar">
    <!-- Profile Card at top -->
    <div class="seller-profile-card">
        <div class="seller-avatar-wrap">
            <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <!-- Hardhat Helmet Graphic -->
                <path d="M50 15C36 15 26 25 24 38H76C74 25 64 15 50 15Z" fill="#F4B400"/>
                <rect x="20" y="38" width="60" height="7" rx="3.5" fill="#F4B400"/>
                <path d="M46 12H54V22H46V12Z" fill="#E37400"/>
                <!-- User Face & Body Graphic -->
                <circle cx="50" cy="52" r="14" fill="#3ddc84"/>
                <path d="M28 85C28 68 37 65 50 65C63 65 72 68 72 85" fill="#1f6feb"/>
            </svg>
        </div>
        <div class="seller-user-name" title="<?php echo htmlspecialchars($sellerUserName); ?>">
            <?php echo htmlspecialchars($sellerUserName); ?>
        </div>
        <div class="seller-user-role">Master User</div>
        <a href="/modify-profile.php" class="seller-modify-btn">Modify</a>
    </div>

    <!-- Navigation Items -->
    <nav class="seller-nav" id="sellerNavAccordion">
        <!-- MyTATITO Primary Link -->
        <a href="/front/mytatito.php" class="seller-primary-link <?php echo ($activeSellerMenu === 'mytatito') ? 'active' : ''; ?>">
            MyTATITO
        </a>

        <!-- MANAGE PRODUCTS Section -->
        <div class="seller-section-title">MANAGE PRODUCTS</div>
        
        <a href="/front/manage-all-products.php" class="seller-menu-item <?php echo ($activeSellerMenu === 'all-products') ? 'active' : ''; ?>">
            <span>All Product</span>
        </a>

        <div>
            <button class="seller-menu-item <?php echo $isListingsOpen ? 'active' : ''; ?>" type="button" data-seller-toggle="collapse" data-seller-target="#submenuListings" aria-expanded="<?php echo $isListingsOpen ? 'true' : 'false'; ?>">
                <span>Listings</span>
                <i class="bi bi-chevron-down chevron"></i>
            </button>
            <div class="seller-collapse <?php echo $isListingsOpen ? 'show' : ''; ?>" id="submenuListings">
                <ul class="seller-submenu">
                    <li><a href="/front/manage-listings.php" class="<?php echo ($activeSellerMenu === 'all-listings') ? 'active' : ''; ?>">All Listings</a></li>
                    <li><a href="/front/manage-plain-listings.php" class="<?php echo ($activeSellerMenu === 'plain-listings') ? 'active' : ''; ?>">Plain Listings</a></li>
                    <li><a href="/front/manage-platinum-listings.php" class="<?php echo ($activeSellerMenu === 'platinum-listings') ? 'active' : ''; ?>">Platinum Listings</a></li>
                    <li><a href="/front/manage-premium-listings.php" class="<?php echo ($activeSellerMenu === 'premium-listings') ? 'active' : ''; ?>">Premium Listings</a></li>
                    <li><a href="/front/manage-infinity-listings.php" class="<?php echo ($activeSellerMenu === 'infinity-listings') ? 'active' : ''; ?>">Infinity Listings</a></li>
                    <li><a href="/front/manage-ai-listings.php" class="<?php echo ($activeSellerMenu === 'ai-listings') ? 'active' : ''; ?>">AI Listings</a></li>
                </ul>
            </div>
        </div>

        <div>
            <button class="seller-menu-item <?php echo $isProjectsOpen ? 'active' : ''; ?>" type="button" data-seller-toggle="collapse" data-seller-target="#submenuProjects" aria-expanded="<?php echo $isProjectsOpen ? 'true' : 'false'; ?>">
                <span>Projects</span>
                <i class="bi bi-chevron-down chevron"></i>
            </button>
            <div class="seller-collapse <?php echo $isProjectsOpen ? 'show' : ''; ?>" id="submenuProjects">
                <ul class="seller-submenu">
                    <li><a href="/front/manage-projects.php" class="<?php echo ($activeSellerMenu === 'manage-projects') ? 'active' : ''; ?>">All Products</a></li>
                    <li><a href="/front/manage-super-campaign.php" class="<?php echo ($activeSellerMenu === 'super-campaign') ? 'active' : ''; ?>">Super Campaign</a></li>
                    <li><a href="/front/manage-bbc.php" class="<?php echo ($activeSellerMenu === 'bbc') ? 'active' : ''; ?>">BBC</a></li>
                    <li><a href="/front/manage-sab.php" class="<?php echo ($activeSellerMenu === 'sab') ? 'active' : ''; ?>">SAB</a></li>
                    <li><a href="/front/manage-np-fp.php" class="<?php echo ($activeSellerMenu === 'np-fp') ? 'active' : ''; ?>">NP/FP/PG/AP/AS</a></li>
                    <li><a href="/front/manage-emailers.php" class="<?php echo ($activeSellerMenu === 'emailers') ? 'active' : ''; ?>">E-Mailers</a></li>
                    <li><a href="/front/manage-omni-ads.php" class="<?php echo ($activeSellerMenu === 'omni-ads') ? 'active' : ''; ?>">Omni Ads</a></li>
                </ul>
            </div>
        </div>

        <a href="/front/manage-banners.php" class="seller-menu-item <?php echo ($activeSellerMenu === 'banners') ? 'active' : ''; ?>">
            <span>Banners</span>
        </a>
        <a href="/front/manage-fsl-booking.php" class="seller-menu-item <?php echo ($activeSellerMenu === 'fsl-booking') ? 'active' : ''; ?>">
            <span>FSL Booking</span>
        </a>

        <!-- MANAGE RESPONSES Section -->
        <div class="seller-section-title">MANAGE RESPONSES</div>
        
        <a href="/front/manage-responses.php" class="seller-menu-item <?php echo ($activeSellerMenu === 'all-responses') ? 'active' : ''; ?>">
            <span>All Responses</span>
        </a>

        <div>
            <button class="seller-menu-item <?php echo $isRespListingsOpen ? 'active' : ''; ?>" type="button" data-seller-toggle="collapse" data-seller-target="#submenuRespListings" aria-expanded="<?php echo $isRespListingsOpen ? 'true' : 'false'; ?>">
                <span>Listings</span>
                <i class="bi bi-chevron-down chevron"></i>
            </button>
            <div class="seller-collapse <?php echo $isRespListingsOpen ? 'show' : ''; ?>" id="submenuRespListings">
                <ul class="seller-submenu">
                    <li><a href="/front/manage-response-listings.php?type=all" class="<?php echo ($activeSellerMenu === 'response-listings') ? 'active' : ''; ?>">All Listings</a></li>
                    <li><a href="/front/manage-response-listings.php?type=plain" class="<?php echo ($activeSellerMenu === 'response-plain') ? 'active' : ''; ?>">Plain Listings</a></li>
                    <li><a href="/front/manage-response-listings.php?type=platinum" class="<?php echo ($activeSellerMenu === 'response-platinum') ? 'active' : ''; ?>">Platinum Listings</a></li>
                    <li><a href="/front/manage-response-listings.php?type=premium" class="<?php echo ($activeSellerMenu === 'response-premium') ? 'active' : ''; ?>">Premium Listings</a></li>
                    <li><a href="/front/manage-response-listings.php?type=infinity" class="<?php echo ($activeSellerMenu === 'response-infinity') ? 'active' : ''; ?>">Infinity Listings</a></li>
                    <li><a href="/front/manage-response-listings.php?type=ai" class="<?php echo ($activeSellerMenu === 'response-ai') ? 'active' : ''; ?>">AI Listings</a></li>
                </ul>
            </div>
        </div>

        <div>
            <button class="seller-menu-item <?php echo $isRespProjectsOpen ? 'active' : ''; ?>" type="button" data-seller-toggle="collapse" data-seller-target="#submenuRespProjects" aria-expanded="<?php echo $isRespProjectsOpen ? 'true' : 'false'; ?>">
                <span>Projects</span>
                <i class="bi bi-chevron-down chevron"></i>
            </button>
            <div class="seller-collapse <?php echo $isRespProjectsOpen ? 'show' : ''; ?>" id="submenuRespProjects">
                <ul class="seller-submenu">
                    <li><a href="/front/manage-response-projects.php" class="<?php echo ($activeSellerMenu === 'response-projects') ? 'active' : ''; ?>">All Products</a></li>
                    <li><a href="/front/manage-response-projects.php?type=super-campaign" class="<?php echo ($activeSellerMenu === 'response-super-campaign') ? 'active' : ''; ?>">Super Campaign</a></li>
                    <li><a href="/front/manage-response-projects.php?type=bbc" class="<?php echo ($activeSellerMenu === 'response-bbc') ? 'active' : ''; ?>">BBC</a></li>
                    <li><a href="/front/manage-response-projects.php?type=sab" class="<?php echo ($activeSellerMenu === 'response-sab') ? 'active' : ''; ?>">SAB</a></li>
                    <li><a href="/front/manage-response-projects.php?type=np-fp" class="<?php echo ($activeSellerMenu === 'response-np-fp') ? 'active' : ''; ?>">NP/FP/PG/AP/AS</a></li>
                    <li><a href="/front/manage-response-projects.php?type=emailers" class="<?php echo ($activeSellerMenu === 'response-emailers') ? 'active' : ''; ?>">E-Mailers</a></li>
                    <li><a href="/front/manage-response-projects.php?type=omni-ads" class="<?php echo ($activeSellerMenu === 'response-omni-ads') ? 'active' : ''; ?>">Omni Ads</a></li>
                </ul>
            </div>
        </div>

        <div>
            <button class="seller-menu-item <?php echo $isExpertPacksOpen ? 'active' : ''; ?>" type="button" data-seller-toggle="collapse" data-seller-target="#submenuExpertPacks" aria-expanded="<?php echo $isExpertPacksOpen ? 'true' : 'false'; ?>">
                <span>Expert Packs</span>
                <i class="bi bi-chevron-down chevron"></i>
            </button>
            <div class="seller-collapse <?php echo $isExpertPacksOpen ? 'show' : ''; ?>" id="submenuExpertPacks">
                <ul class="seller-submenu">
                    <li><a href="/front/manage-all-leads.php" class="<?php echo ($activeSellerMenu === 'all-leads') ? 'active' : ''; ?>">All Leads</a></li>
                </ul>
            </div>
        </div>

        <!-- UPLOAD MEDIA Section -->
        <div class="seller-section-title">UPLOAD MEDIA</div>
        <a href="/front/upload-ai-listings.php" class="seller-menu-item <?php echo ($activeSellerMenu === 'upload-ai-listings') ? 'active' : ''; ?>">
            <span>AI Listings</span>
        </a>

        <!-- MANAGE SUBSCRIPTIONS Section -->
        <div class="seller-section-title">MANAGE SUBSCRIPTIONS</div>
        <a href="/front/manage-payments.php" class="seller-menu-item <?php echo ($activeSellerMenu === 'manage-payments') ? 'active' : ''; ?>">
            <span>Manage Payments</span>
        </a>
        <a href="/front/subscription-status.php" class="seller-menu-item <?php echo ($activeSellerMenu === 'subscription-status') ? 'active' : ''; ?>">
            <span>Subscription Status</span>
        </a>
        <a href="/front/credit-usage-history.php" class="seller-menu-item <?php echo ($activeSellerMenu === 'credit-usage-history') ? 'active' : ''; ?>">
            <span>Credit Usage History</span>
        </a>
        <a href="/front/upgrade-history.php" class="seller-menu-item <?php echo ($activeSellerMenu === 'upgrade-history') ? 'active' : ''; ?>">
            <span>Upgrade History</span>
        </a>
        <a href="/front/manage-subusers.php" class="seller-menu-item <?php echo ($activeSellerMenu === 'manage-subusers') ? 'active' : ''; ?>">
            <span>Manage Sub users 0</span>
        </a>
        <a href="/front/omni-ads-performance.php" class="seller-menu-item <?php echo ($activeSellerMenu === 'omni-ads-performance') ? 'active' : ''; ?>">
            <span>Omni Ads Performance</span>
        </a>

        <!-- PROJECTS Section -->
        <div class="seller-section-title">PROJECTS</div>
        <a href="/front/activate-projects.php" class="seller-menu-item <?php echo ($activeSellerMenu === 'activate-projects') ? 'active' : ''; ?>">
            <span>Activate Projects</span>
        </a>
        <a href="/front/manage-seller-projects.php" class="seller-menu-item <?php echo ($activeSellerMenu === 'manage-seller-projects') ? 'active' : ''; ?>">
            <span>Manage Projects</span>
        </a>
        <a href="/front/authorise-activation.php" class="seller-menu-item <?php echo ($activeSellerMenu === 'authorise-activation') ? 'active' : ''; ?>">
            <span>Authorise Activation</span>
        </a>
        <a href="/front/authorise-boost.php" class="seller-menu-item <?php echo ($activeSellerMenu === 'authorise-boost') ? 'active' : ''; ?>">
            <span>Authorise Boost</span>
        </a>

        <!-- MY PROFILE Section -->
        <div class="seller-section-title">MY PROFILE</div>
        <a href="/front/recently-searched.php?tab=shortlisted" class="seller-menu-item">
            <span>Shortlist</span>
        </a>
        <a href="#" class="seller-menu-item" data-change-password-open>
            <span>Change Password</span>
        </a>
    </nav>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Mobile drawer toggle
    const toggleBtn = document.getElementById('sellerMobileToggleBtn');
    const sidebar = document.getElementById('sellerSidebar');
    const backdrop = document.getElementById('sellerSidebarBackdrop');

    if (toggleBtn && sidebar && backdrop) {
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('open');
            backdrop.classList.toggle('show');
        });

        backdrop.addEventListener('click', function () {
            sidebar.classList.remove('open');
            backdrop.classList.remove('show');
        });
    }

    // 2. Dropdown / Collapse Handler for Submenus (Clean toggle on every click)
    const menuToggleButtons = document.querySelectorAll('#sellerSidebar button.seller-menu-item[data-seller-toggle="collapse"]');
    menuToggleButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const targetSelector = this.getAttribute('data-seller-target');
            if (!targetSelector) return;
            const targetEl = document.querySelector(targetSelector);
            if (!targetEl) return;

            const isCurrentlyShown = targetEl.classList.contains('show');
            if (isCurrentlyShown) {
                targetEl.classList.remove('show');
                this.setAttribute('aria-expanded', 'false');
            } else {
                targetEl.classList.add('show');
                this.setAttribute('aria-expanded', 'true');
            }
        });
    });
});
</script>
