<?php
// === Smart Database Configuration ===
// Automatically detects Local vs Hostinger environment

// Check if we're on Hostinger (common indicators)
$isHostinger = (
    strpos($_SERVER['HTTP_HOST'] ?? '', '.hostinger') !== false ||
    strpos($_SERVER['SERVER_NAME'] ?? '', '.hostinger') !== false ||
    strpos($_SERVER['HTTP_HOST'] ?? '', 'sevakaro.in') !== false ||
    isset($_SERVER['HOSTINGER']) ||
    file_exists('/home/u231942554') || // Hostinger user directory
    strpos($_SERVER['DOCUMENT_ROOT'] ?? '', 'hostinger') !== false ||
    strpos($_SERVER['DOCUMENT_ROOT'] ?? '', 'public_html') !== false
);

if ($isHostinger) {
    // === HOSTINGER Configuration ===
    define('DB_HOST', 'localhost');
    define('DB_USER', 'u231942554_volunteer');
    define('DB_PASS', 'PwD$12345');
    define('DB_NAME', 'u231942554_volunteer');
    define('ENVIRONMENT', 'HOSTINGER');
} else {
    // === LOCAL XAMPP Configuration ===
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'volunteer_app');
    define('ENVIRONMENT', 'LOCAL');
}

// === WhatsApp Cloud API ===
define('WA_PHONE_NUMBER_ID', '793669113818748');
define('WA_TOKEN', 'EAFZAbMAd3iwMBPHSqGy2g7HFL3WLbwYxh89UxGolzYb5WuFZAzfEFLxyLK3iqCCob2l2zmopfrrFcVjAwa5SzWI4Rv1IdZAYTCeKsO1AHr2Y5vovPj4yKgo56yw0kFr9EAtmDTTRTfAkVhyM3NzIJWrxMMQgbdquGSwpnDx0u3Gq8RZC9kvujHuupIXC3tvSZBQZDZD');
define('WA_API_VERSION', 'v20.0');

// === Environment-specific settings ===
if ($isHostinger) {
    // Production settings
    define('DEBUG_MODE', false);
    define('ERROR_REPORTING', false);
} else {
    // Development settings
    define('DEBUG_MODE', true);
    define('ERROR_REPORTING', true);
}

// Note: json_response function is now in api/db.php to avoid duplication
