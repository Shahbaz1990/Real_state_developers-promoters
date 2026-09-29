<?php
/**
 * includes/pincode-lookup.php
 *
 * Resolves a 6-digit Indian pincode to a city name for the header's
 * "Search by Pincode" option. Called via GET fetch from header.php.
 *
 * Response shape (ALWAYS valid JSON, even on internal errors):
 *   { "success": true,  "city": "Pune" }
 *   { "success": false, "message": "..." }
 *
 * Lookup order:
 *   1. Local `pincodes` table, if you have one (pincode -> city_name),
 *      matched against your own `cities` table.
 *   2. Fallback to the free India Post pincode API, cross-checked
 *      against your `cities` table.
 *
 * DEBUGGING: add &debug=1 to the request URL (e.g. while testing) to get
 * the internal failure reason back in the JSON "debug" field instead of
 * just a generic message. Remove/ignore this in production if you'd
 * rather not expose it.
 */

// Never let a stray notice/warning corrupt the JSON output — buffer
// everything and always emit clean JSON at the end.
ob_start();
header('Content-Type: application/json');

$debug = isset($_GET['debug']) && $_GET['debug'] == '1';
$debugInfo = [];

function respond($success, $extra = [], $debugInfo = [], $debug = false) {
    while (ob_get_level() > 0) { ob_end_clean(); }
    $payload = array_merge(['success' => $success], $extra);
    if ($debug && !empty($debugInfo)) {
        $payload['debug'] = $debugInfo;
    }
    echo json_encode($payload);
    exit;
}

// Catch fatal errors (e.g. missing config.php) so the client still gets JSON.
register_shutdown_function(function () use (&$debugInfo, $debug) {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $debugInfo[] = 'Fatal error: ' . $err['message'] . ' in ' . $err['file'] . ':' . $err['line'];
        respond(false, ['message' => 'Server error while looking up that pincode.'], $debugInfo, $debug);
    }
});

$pincode = isset($_GET['pincode']) ? trim($_GET['pincode']) : '';
if (!preg_match('/^\d{6}$/', $pincode)) {
    respond(false, ['message' => 'Please enter a valid 6-digit pincode.'], $debugInfo, $debug);
}

// ---- Load config / DB connection, without fataling if it's missing ----
$conn = null;
$configPath = __DIR__ . '/../config.php';
if (file_exists($configPath)) {
    try {
        require_once $configPath;
    } catch (\Throwable $e) {
        $debugInfo[] = 'config.php threw: ' . $e->getMessage();
    }
} else {
    $debugInfo[] = "config.php not found at $configPath — adjust the path in this file if your folder layout differs.";
}

$activeCities = [];
if (isset($conn) && $conn instanceof mysqli) {
    // ---- Step 1: local pincodes table (if present) ----
    $stmt = @$conn->prepare("SELECT city_name FROM pincodes WHERE pincode = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('s', $pincode);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $row = $result->fetch_assoc()) {
            $stmt->close();
            respond(true, ['city' => $row['city_name']], $debugInfo, $debug);
        }
        $stmt->close();
    } else {
        $debugInfo[] = 'No local `pincodes` table (or query failed) — falling back to India Post API. ' . $conn->error;
    }

    // Build lookup of this site's active cities so we only ever select a
    // city that actually exists on the site.
    $cityRes = $conn->query("SELECT city_name FROM cities WHERE status='Active'");
    if ($cityRes) {
        while ($row = $cityRes->fetch_assoc()) {
            $activeCities[mb_strtolower($row['city_name'])] = $row['city_name'];
        }
    }
} else {
    $debugInfo[] = '$conn (mysqli) not available from config.php — skipping local DB checks.';
}

// ---- Step 2: fallback to India Post's public pincode API ----
$apiUrl = 'https://api.postalpincode.in/pincode/' . urlencode($pincode);
$apiResponse = false;
$fetchError = '';

if (function_exists('curl_init')) {
    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 6,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $apiResponse = curl_exec($ch);
    if ($apiResponse === false) {
        $fetchError = 'curl error: ' . curl_error($ch) . ' (errno ' . curl_errno($ch) . ')';
    }
    curl_close($ch);
} elseif (ini_get('allow_url_fopen')) {
    $context = stream_context_create(['http' => ['timeout' => 6]]);
    $apiResponse = @file_get_contents($apiUrl, false, $context);
    if ($apiResponse === false) {
        $fetchError = 'file_get_contents failed (curl extension unavailable, and allow_url_fopen fetch also failed)';
    }
} else {
    $fetchError = 'Neither the curl extension nor allow_url_fopen is available on this server, so the external pincode API cannot be reached. Either enable one of those, or add rows to a local `pincodes` table so lookups never need the external API.';
}

if ($fetchError) {
    $debugInfo[] = $fetchError;
    respond(false, ['message' => 'Could not reach the pincode lookup service. Please try again, or select your city from the list.'], $debugInfo, $debug);
}

$data = json_decode($apiResponse, true);
if (!empty($data[0]['PostOffice'][0])) {
    $district = $data[0]['PostOffice'][0]['District'] ?? '';
    $lower = mb_strtolower($district);

    if (isset($activeCities[$lower])) {
        respond(true, ['city' => $activeCities[$lower]], $debugInfo, $debug);
    }

    // If we couldn't check against active cities (no DB), just trust the API result.
    if (empty($activeCities) && $district) {
        respond(true, ['city' => $district], $debugInfo, $debug);
    }

    respond(false, ['message' => $district
        ? "We don't currently cover $district. Please pick a city from the list."
        : 'No city found for that pincode.'], $debugInfo, $debug);
}

$debugInfo[] = 'API response: ' . substr((string) $apiResponse, 0, 300);
respond(false, ['message' => 'No city found for that pincode.'], $debugInfo, $debug);