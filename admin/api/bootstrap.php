<?php
/**
 * api/bootstrap.php
 * Include this at the top of every API endpoint file.
 */

require_once __DIR__ . '/../../config/bootstrap.php';

header("Content-Type: application/json");
$cors_ok = cors_headers();

// Handle CORS preflight requests (only meaningful for allow-listed origins)
if($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
    http_response_code($cors_ok ? 200 : 403);
    exit;
}

require_once __DIR__ . '/../config.php'; // adjust path if your config.php lives elsewhere

/**
 * Send a JSON response and stop execution.
 */
function send_response($success, $message, $data = null, $http_code = 200){
    http_response_code($http_code);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data'    => $data,
    ]);
    exit;
}

/**
 * Read and decode the JSON body of the request.
 */
function get_json_input(){
    $input = json_decode(file_get_contents('php://input'), true);
    return $input ?: [];
}
