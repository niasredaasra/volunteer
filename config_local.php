<?php
// === LOCAL XAMPP Configuration ===
// Use this for local development

define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // XAMPP default
define('DB_PASS', '');              // XAMPP default (empty)
define('DB_NAME', 'volunteer_app'); // Local database name

// === WhatsApp Cloud API ===
define('WA_PHONE_NUMBER_ID', '793669113818748');
define('WA_TOKEN', 'EAFZAbMAd3iwMBPHSqGy2g7HFL3WLbwYxh89UxGolzYb5WuFZAzfEFLxyLK3iqCCob2l2zmopfrrFcVjAwa5SzWI4Rv1IdZAYTCeKsO1AHr2Y5vovPj4yKgo56yw0kFr9EAtmDTTRTfAkVhyM3NzIJWrxMMQgbdquGSwpnDx0u3Gq8RZC9kvujHuupIXC3tvSZBQZDZD');
define('WA_API_VERSION', 'v20.0');

// Note: json_response function is now in api/db.php to avoid duplication
?>
