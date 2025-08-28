<?php
// Environment Detection Test
require_once 'config.php';

echo "<h2>🔍 Environment Detection Test</h2>";
echo "<div style='font-family: monospace; background: #f5f5f5; padding: 15px; border-radius: 5px;'>";

echo "<strong>Detected Environment:</strong> " . ENVIRONMENT . "<br><br>";

echo "<strong>Database Configuration:</strong><br>";
echo "- DB_HOST: " . DB_HOST . "<br>";
echo "- DB_USER: " . DB_USER . "<br>";
echo "- DB_NAME: " . DB_NAME . "<br>";
echo "- DB_PASS: " . (DB_PASS ? '[SET]' : '[EMPTY]') . "<br><br>";

echo "<strong>Server Details:</strong><br>";
echo "- HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'Not set') . "<br>";
echo "- SERVER_NAME: " . ($_SERVER['SERVER_NAME'] ?? 'Not set') . "<br>";
echo "- DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Not set') . "<br><br>";

echo "<strong>Environment Checks:</strong><br>";
echo "- Hostinger HTTP_HOST: " . (strpos($_SERVER['HTTP_HOST'] ?? '', '.hostinger') !== false ? '✅ Yes' : '❌ No') . "<br>";
echo "- Hostinger SERVER_NAME: " . (strpos($_SERVER['SERVER_NAME'] ?? '', '.hostinger') !== false ? '✅ Yes' : '❌ No') . "<br>";
echo "- Custom Domain (sevakaro.in): " . (strpos($_SERVER['HTTP_HOST'] ?? '', 'sevakaro.in') !== false ? '✅ Yes' : '❌ No') . "<br>";
echo "- Hostinger Directory: " . (file_exists('/home/u231942554') ? '✅ Yes' : '❌ No') . "<br>";
echo "- Hostinger DOCUMENT_ROOT: " . (strpos($_SERVER['DOCUMENT_ROOT'] ?? '', 'hostinger') !== false ? '✅ Yes' : '❌ No') . "<br>";
echo "- Public HTML Path: " . (strpos($_SERVER['DOCUMENT_ROOT'] ?? '', 'public_html') !== false ? '✅ Yes' : '❌ No') . "<br>";
echo "- Debug Mode: " . (defined('DEBUG_MODE') && DEBUG_MODE ? '✅ Enabled' : '❌ Disabled') . "<br><br>";

// Test database connection
echo "<strong>Database Connection Test:</strong><br>";
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        echo "❌ Connection Failed: " . $conn->connect_error . "<br>";
    } else {
        echo "✅ Connection Successful!<br>";
        echo "- MySQL Version: " . $conn->server_info . "<br>";
        
        // Check if volunteers table exists
        $result = $conn->query("SHOW TABLES LIKE 'volunteers'");
        if ($result && $result->num_rows > 0) {
            $count_result = $conn->query("SELECT COUNT(*) as count FROM volunteers");
            $count = $count_result->fetch_assoc()['count'];
            echo "- Volunteers Table: ✅ Exists ({$count} records)<br>";
        } else {
            echo "- Volunteers Table: ❌ Not found<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Database Error: " . $e->getMessage() . "<br>";
}

echo "</div>";

echo "<h3>📋 Next Steps:</h3>";
if (ENVIRONMENT == 'LOCAL') {
    echo "<p>✅ <strong>Local Environment Detected</strong> - Ready to work!</p>";
    echo "<p>🔗 Access: <a href='index.php'>Main Form</a> | <a href='admin/list.php'>Admin Panel</a></p>";
} else {
    echo "<p>🌐 <strong>Hostinger Environment Detected</strong></p>";
    echo "<p>📝 Make sure you've imported the database using <code>setup_hostinger_database.sql</code></p>";
}
?>
