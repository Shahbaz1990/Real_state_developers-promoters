<?php
/**
 * security.php
 * Shared security helpers for the admin panel:
 *  - Hardened session startup
 *  - CSRF token generation / rendering / verification
 *  - Login brute-force throttle (session based, no schema change)
 *  - Secure file upload validation (extension + MIME + size)
 */

// ------------------------------------------------------------------
// Hardened session startup
// ------------------------------------------------------------------
if (!function_exists('session_start_secure')) {
    function session_start_secure() {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.use_only_cookies', '1');
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'httponly' => true,
                'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }
}

// ------------------------------------------------------------------
// CSRF helpers
// ------------------------------------------------------------------
if (!function_exists('csrf_token')) {
    function csrf_token() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field() {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES) . '">';
    }
}

if (!function_exists('verify_csrf')) {
    /**
     * Validate the CSRF token present in the request (POST preferred, falls
     * back to GET for the existing query-string delete actions).
     */
    function verify_csrf() {
        $sent = $_POST['csrf_token'] ?? ($_GET['csrf_token'] ?? '');
        return is_string($sent) && $sent !== '' && isset($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $sent);
    }
}

if (!function_exists('set_flash')) {
    /**
     * Queue a one-shot notification for the next page load.
     * Rendered as a Toastify toast by includes/ui_footer.php.
     *
     * @param string $type    'success' | 'error' | 'info'
     * @param string $message
     */
    function set_flash($type, $message) {
        if (!isset($_SESSION['flash_msgs']) || !is_array($_SESSION['flash_msgs'])) {
            $_SESSION['flash_msgs'] = [];
        }
        $_SESSION['flash_msgs'][] = [
            'type'    => in_array($type, ['success', 'error', 'info'], true) ? $type : 'info',
            'message' => (string) $message,
        ];
    }
}

// ------------------------------------------------------------------
// Login throttle (session based)
// ------------------------------------------------------------------
if (!function_exists('login_throttle_failures')) {
    function login_throttle_failures() {
        $window = 600; // 10 minutes
        $fails = $_SESSION['login_failures'] ?? [];
        $fails = array_values(array_filter($fails, function ($t) use ($window) {
            return (time() - (int) $t) < $window;
        }));
        $_SESSION['login_failures'] = $fails;
        return count($fails);
    }
}

if (!function_exists('login_throttle_blocked')) {
    function login_throttle_blocked() {
        return login_throttle_failures() >= 5;
    }
}

if (!function_exists('login_throttle_record_failure')) {
    function login_throttle_record_failure() {
        $_SESSION['login_failures'][] = time();
    }
}

if (!function_exists('login_throttle_reset')) {
    function login_throttle_reset() {
        unset($_SESSION['login_failures']);
    }
}

if (!function_exists('throttle_failures')) {
    /**
     * Filesystem-backed attempt counter, keyed by a string (e.g. email hash).
     * Used where no session is available (JSON APIs). Returns the number of
     * attempts still inside the window.
     */
    function throttle_failures($key, $window = 600) {
        $dir  = sys_get_temp_dir() . '/tatito_throttle';
        if (!is_dir($dir)) @mkdir($dir, 0700, true);
        $file = $dir . '/' . preg_replace('/[^a-f0-9]/', '', $key) . '.log';
        if (!file_exists($file)) return 0;
        $times = array_values(array_filter(array_map('intval', @file($file, FILE_IGNORE_NEW_LINES) ?: []), function ($t) use ($window) {
            return (time() - $t) < $window;
        }));
        return count($times);
    }
}

if (!function_exists('throttle_record_failure')) {
    function throttle_record_failure($key, $window = 600) {
        $dir  = sys_get_temp_dir() . '/tatito_throttle';
        if (!is_dir($dir)) @mkdir($dir, 0700, true);
        $file = $dir . '/' . preg_replace('/[^a-f0-9]/', '', $key) . '.log';
        $recent = array_values(array_filter(array_map('intval', @file($file, FILE_IGNORE_NEW_LINES) ?: []), function ($t) use ($window) {
            return (time() - $t) < $window;
        }));
        $recent[] = time();
        @file_put_contents($file, implode("\n", $recent) . "\n");
    }
}

if (!function_exists('throttle_clear')) {
    function throttle_clear($key) {
        $file = sys_get_temp_dir() . '/tatito_throttle/' . preg_replace('/[^a-f0-9]/', '', $key) . '.log';
        @unlink($file);
    }
}

// ------------------------------------------------------------------
// Secure upload helper
// ------------------------------------------------------------------
if (!function_exists('upload_dir_guard')) {
    /**
     * Ensure a newly-created upload directory contains an .htaccess that
     * blocks PHP execution. Safe to call on existing directories too.
     */
    function upload_dir_guard($dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $guard = $dir . '.htaccess';
        if (!file_exists($guard)) {
            @file_put_contents($guard,
                "# Block PHP execution inside the uploads directory.\n" .
                "Options -ExecCGI\n" .
                "RemoveHandler .php .phtml .php3 .php4 .php5 .php7 .phar .cgi .pl .py\n\n" .
                "<FilesMatch \"\\.(?i:php|phtml|php3|php4|php5|php7|phar|pl|py|cgi)$\">\n" .
                "    Require all denied\n" .
                "</FilesMatch>\n"
            );
        }
        return $dir;
    }
}

if (!function_exists('secure_upload_from_file')) {
    /**
     * Like secure_upload() but accepts a single-file array directly, so it can
     * be reused for entries inside a multi-upload field (e.g. gallery images).
     * $file should look like: [ 'name'=>..., 'tmp_name'=>..., 'size'=>..., 'error'=>... ]
     */
    function secure_upload_from_file($file, $dir, $prefix, $allowedMimes = [], $allowedExts = [], $maxBytes = 2097152) {
        if (empty($file['name'])) {
            return null;
        }
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return false;
        }
        if (($file['size'] ?? 0) > $maxBytes) {
            return false;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!empty($allowedExts) && !in_array($ext, $allowedExts)) {
            return false;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        if (!empty($allowedMimes) && !in_array($mime, $allowedMimes)) {
            return false;
        }

        $name = $prefix . bin2hex(random_bytes(6)) . '.' . $ext;

        upload_dir_guard($dir);
        if (!move_uploaded_file($file['tmp_name'], $dir . $name)) {
            return false;
        }
        return $name;
    }
}

if (!function_exists('secure_upload')) {
    /**
     * Validates and stores a single uploaded file. Returns the generated
     * filename on success, null when no file was supplied, or false on an
     * invalid file (so callers can surface a friendly error).
     */
    function secure_upload($field, $dir, $prefix, $allowedMimes = [], $allowedExts = [], $maxBytes = 2097152) {
        if (empty($_FILES[$field]['name'])) {
            return null;
        }
        return secure_upload_from_file($_FILES[$field], $dir, $prefix, $allowedMimes, $allowedExts, $maxBytes);
    }
}

// Common whitelist for image uploads used across admin pages
if (!defined('ADMIN_ALLOWED_IMAGE_MIMES')) {
    define('ADMIN_ALLOWED_IMAGE_MIMES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
}
if (!defined('ADMIN_ALLOWED_IMAGE_EXTS')) {
    define('ADMIN_ALLOWED_IMAGE_EXTS', ['jpg', 'jpeg', 'png', 'webp', 'gif']);
}
