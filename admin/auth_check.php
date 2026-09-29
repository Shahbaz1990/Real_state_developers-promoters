<?php
/**
 * auth_check.php
 * Include this at the very top of every protected admin page
 * (dashboard.php, menu.php, submenu.php, banner.php, city.php, trending-projects.php, etc.)
 * instead of the old inline session_start() + isset($_SESSION['id']) check.
 *
 * Handles: session start, DB connection, login check, idle timeout,
 * admin-role enforcement, and CSRF helper setup.
 */

require_once __DIR__ . '/includes/security.php';
session_start_secure();

require_once __DIR__ . '/config.php';

// How long (in seconds) a user can be inactive before being auto-logged-out.
// 1800 = 30 minutes. Change this number to whatever timeout you want.
$session_timeout = 1800;

// Not logged in at all -> straight to login
if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit;
}

// Logged in, but check how long since their last action
if(isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)){

    // Too long idle -> force logout
    $_SESSION = [];
    session_unset();
    session_destroy();

    header("Location: login.php?timeout=1");
    exit;
}

// Admin-role enforcement: only users with the admin role may use the admin panel.
if(($_SESSION['role'] ?? 'user') !== 'admin'){
    session_unset();
    session_destroy();
    header("Location: login.php?denied=1");
    exit;
}

// Still active -> reset the clock on every page load / action
$_SESSION['last_activity'] = time();

// Make sure a CSRF token exists for this session
csrf_token();

/**
 * Predicate for the current request: is the visitor a logged-in admin?
 * Only meaningful after auth_check.php has run; on any failure the include
 * above has already redirected and exited, so this returns true here.
 */
function admin_session_ok() {
    return isset($_SESSION['id']) && ($_SESSION['role'] ?? 'user') === 'admin';
}
