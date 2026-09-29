<?php
require_once __DIR__ . '/includes/security.php';
session_start_secure();
// Recreate the session id to stop session fixation, then clear the session data.
$_SESSION = [];
if(ini_get("session.use_cookies")){
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}
session_destroy();

// Start a fresh session so login.php can show a friendly "logged out" toast.
session_start();
set_flash('info', "You have been logged out.");

header("Location: login.php");
exit;
?>