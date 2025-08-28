<?php
// === Hostinger Database Configuration ===
// Replace these values with your actual Hostinger database details

define('DB_HOST', 'localhost');
define('DB_USER', 'u123456_shelter');        // Replace with your DB username from Hostinger
define('DB_PASS', 'your_strong_password');   // Replace with your DB password
define('DB_NAME', 'u123456_shelter');        // Replace with your DB name

// === WhatsApp Cloud API ===
// Keep your working WhatsApp API credentials
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
?>


