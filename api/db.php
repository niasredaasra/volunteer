<?php
require_once __DIR__ . '/../config.php';

function db() {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            http_response_code(500);
            die('DB connection failed: ' . $conn->connect_error);
        }
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}

function json_response($data, $code = 200) {
    // Set environment-appropriate cache headers
    $cache_headers = [
        'Content-Type' => 'application/json; charset=utf-8'
    ];
    
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'HOSTINGER') {
        $cache_headers['Cache-Control'] = 'public, max-age=300'; // 5 minutes cache in production
    } else {
        $cache_headers['Cache-Control'] = 'no-cache, no-store, must-revalidate';
        $cache_headers['Pragma'] = 'no-cache';
        $cache_headers['Expires'] = '0';
    }
    
    foreach ($cache_headers as $header => $value) {
        header($header . ': ' . $value);
    }
    
    http_response_code($code);
    
    // Add environment info in debug mode
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        if (is_array($data)) {
            $data['_debug'] = [
                'environment' => ENVIRONMENT ?? 'UNKNOWN',
                'timestamp' => date('Y-m-d H:i:s'),
                'memory_usage' => memory_get_usage(true)
            ];
        }
    }
    
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function clean_string($s) {
    return trim($s ?? '');
}
