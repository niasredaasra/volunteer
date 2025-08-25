<?php
// Debug database connection for sevakaro.in
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Database Connection Debug</h2>";
echo "<hr>";

// Check if config file exists
echo "<h3>1. Config File Check:</h3>";
if (file_exists('config.php')) {
    echo "✅ config.php exists<br>";
    require_once 'config.php';
    
    echo "<strong>Configuration Values:</strong><br>";
    echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NOT DEFINED') . "<br>";
    echo "DB_USER: " . (defined('DB_USER') ? DB_USER : 'NOT DEFINED') . "<br>";
    echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NOT DEFINED') . "<br>";
    echo "DB_PASS: " . (defined('DB_PASS') ? (DB_PASS ? 'SET' : 'EMPTY') : 'NOT DEFINED') . "<br>";
} else {
    echo "❌ config.php NOT FOUND<br>";
}

echo "<hr>";

// Test database connection
echo "<h3>2. Database Connection Test:</h3>";
try {
    if (defined('DB_HOST') && defined('DB_USER') && defined('DB_NAME')) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            echo "❌ Connection failed: " . $conn->connect_error . "<br>";
            echo "<strong>Common fixes:</strong><br>";
            echo "- Check database exists in Hostinger panel<br>";
            echo "- Verify username/password<br>";
            echo "- Make sure database user has permissions<br>";
        } else {
            echo "✅ Database connected successfully!<br>";
            echo "Server info: " . $conn->host_info . "<br>";
            
            // Test if tables exist
            echo "<hr>";
            echo "<h3>3. Tables Check:</h3>";
            $tables = ['villages', 'occupations', 'seva_interests', 'items', 'cities', 'states', 'countries', 'volunteers'];
            
            foreach ($tables as $table) {
                $result = $conn->query("SHOW TABLES LIKE '$table'");
                if ($result && $result->num_rows > 0) {
                    $count_result = $conn->query("SELECT COUNT(*) as count FROM $table");
                    $count = $count_result->fetch_assoc()['count'];
                    echo "✅ $table table exists ($count records)<br>";
                } else {
                    echo "❌ $table table missing<br>";
                }
            }
        }
    } else {
        echo "❌ Database configuration incomplete<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h3>4. File Structure Check:</h3>";
echo "Current directory: " . __DIR__ . "<br>";
echo "Files in directory:<br>";
$files = scandir('.');
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        echo "- $file<br>";
    }
}

if (is_dir('api')) {
    echo "<br>API directory exists:<br>";
    $api_files = scandir('api');
    foreach ($api_files as $file) {
        if ($file != '.' && $file != '..') {
            echo "- api/$file<br>";
        }
    }
}
?>
