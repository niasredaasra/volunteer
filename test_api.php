<?php
// Test API endpoints to see what they return
echo "<h2>🧪 API Test Results</h2>";
echo "<div style='font-family: monospace; background: #f5f5f5; padding: 15px; border-radius: 5px;'>";

echo "<h3>📋 Testing Volunteers API</h3>";
echo "<strong>URL:</strong> /api/volunteers.php?fn=list<br>";
echo "<strong>Response:</strong><br>";

// Test volunteers API
ob_start();
$_GET['fn'] = 'list';
include 'api/volunteers.php';
$volunteers_response = ob_get_clean();

echo "<pre style='background: white; padding: 10px; border: 1px solid #ccc;'>";
echo htmlspecialchars($volunteers_response);
echo "</pre>";

// Parse JSON to see if it's valid
$volunteers_data = json_decode($volunteers_response, true);
if ($volunteers_data !== null) {
    echo "<strong>✅ JSON Valid:</strong> " . (is_array($volunteers_data) ? count($volunteers_data) . " items" : "Single object") . "<br>";
    if (is_array($volunteers_data) && count($volunteers_data) > 0) {
        echo "<strong>Sample first record:</strong><br>";
        echo "<pre style='background: #e8f5e8; padding: 10px;'>";
        print_r($volunteers_data[0]);
        echo "</pre>";
    }
} else {
    echo "<strong>❌ JSON Invalid or Error</strong><br>";
    echo "<strong>JSON Error:</strong> " . json_last_error_msg() . "<br>";
}

echo "<hr>";

echo "<h3>👥 Testing Visitors API</h3>";
echo "<strong>URL:</strong> /api/visitors.php?fn=list<br>";
echo "<strong>Response:</strong><br>";

// Reset $_GET for visitors
unset($_GET['fn']);
$_GET['fn'] = 'list';

ob_start();
include 'api/visitors.php';
$visitors_response = ob_get_clean();

echo "<pre style='background: white; padding: 10px; border: 1px solid #ccc;'>";
echo htmlspecialchars($visitors_response);
echo "</pre>";

// Parse JSON to see if it's valid
$visitors_data = json_decode($visitors_response, true);
if ($visitors_data !== null) {
    echo "<strong>✅ JSON Valid:</strong> " . (is_array($visitors_data) ? count($visitors_data) . " items" : "Single object") . "<br>";
    if (is_array($visitors_data) && count($visitors_data) > 0) {
        echo "<strong>Sample first record:</strong><br>";
        echo "<pre style='background: #e8f5e8; padding: 10px;'>";
        print_r($visitors_data[0]);
        echo "</pre>";
    }
} else {
    echo "<strong>❌ JSON Invalid or Error</strong><br>";
    echo "<strong>JSON Error:</strong> " . json_last_error_msg() . "<br>";
}

echo "</div>";

echo "<h3>🔧 Debugging Tips:</h3>";
echo "<ul>";
echo "<li>If you see PHP errors: Check your error logs</li>";
echo "<li>If JSON is invalid: Look for PHP warnings/notices in response</li>";
echo "<li>If empty arrays: Check if data exists in database tables</li>";
echo "<li>Test these URLs directly in browser:</li>";
echo "<ul>";
echo "<li><a href='api/volunteers.php?fn=list' target='_blank'>api/volunteers.php?fn=list</a></li>";
echo "<li><a href='api/visitors.php?fn=list' target='_blank'>api/visitors.php?fn=list</a></li>";
echo "</ul>";
echo "</ul>";
?>
