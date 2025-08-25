<?php
// === Hostinger Database Configuration ===
// IMPORTANT: Replace these with your actual Hostinger database details

define('DB_HOST', 'localhost');
define('DB_USER', 'u231942554_volunteer');    // Your volunteer database username
define('DB_PASS', 'PwD$12345');    // Replace with password you set for volunteer DB
define('DB_NAME', 'u231942554_volunteer');    // Your volunteer database name

// === WhatsApp Cloud API ===
define('WA_PHONE_NUMBER_ID', '793669113818748');
define('WA_TOKEN', 'EAFZAbMAd3iwMBPHSqGy2g7HFL3WLbwYxh89UxGolzYb5WuFZAzfEFLxyLK3iqCCob2l2zmopfrrFcVjAwa5SzWI4Rv1IdZAYTCeKsO1AHr2Y5vovPj4yKgo56yw0kFr9EAtmDTTRTfAkVhyM3NzIJWrxMMQgbdquGSwpnDx0u3Gq8RZC9kvujHuupIXC3tvSZBQZDZD');
define('WA_API_VERSION', 'v20.0');

// Helper function for JSON responses
function json_response($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
