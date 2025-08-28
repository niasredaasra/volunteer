<?php
// Debug script to identify issues in index.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h2>Debug Information</h2>";

// Test 1: Check if config.php exists and loads
echo "<h3>1. Config File Test</h3>";
if (file_exists('config.php')) {
    echo "✅ config.php exists<br>";
    try {
        include 'config.php';
        echo "✅ config.php loaded successfully<br>";
        
        if (defined('DB_HOST')) {
            echo "✅ Database constants defined<br>";
            echo "DB_HOST: " . DB_HOST . "<br>";
            echo "DB_USER: " . DB_USER . "<br>";
            echo "DB_NAME: " . DB_NAME . "<br>";
        } else {
            echo "❌ Database constants not defined<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error loading config.php: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ config.php file missing<br>";
}

// Test 2: Check database connection
echo "<h3>2. Database Connection Test</h3>";
if (defined('DB_HOST')) {
    try {
        $test_conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($test_conn->connect_error) {
            echo "❌ Database connection failed: " . $test_conn->connect_error . "<br>";
        } else {
            echo "✅ Database connection successful<br>";
            $test_conn->close();
        }
    } catch (Exception $e) {
        echo "❌ Database connection error: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Cannot test database - constants not defined<br>";
}

// Test 3: Check if api/db.php loads
echo "<h3>3. API Database File Test</h3>";
if (file_exists('api/db.php')) {
    echo "✅ api/db.php exists<br>";
    try {
        require_once 'api/db.php';
        echo "✅ api/db.php loaded successfully<br>";
        
        $db_conn = db();
        echo "✅ Database function works<br>";
    } catch (Exception $e) {
        echo "❌ Error with api/db.php: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ api/db.php file missing<br>";
}

// Test 4: Check countries API
echo "<h3>4. Countries API Test</h3>";
if (file_exists('api/countries.php')) {
    echo "✅ api/countries.php exists<br>";
    
    // Test the API endpoint
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost/volunteer_app/api/countries.php?fn=list');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($response !== false && $httpcode == 200) {
        echo "✅ Countries API responds successfully<br>";
        $data = json_decode($response, true);
        if ($data && is_array($data)) {
            echo "✅ Countries data found: " . count($data) . " countries<br>";
            
            // Check if India exists
            $india_found = false;
            foreach ($data as $country) {
                if (strtolower($country['country_name']) === 'india') {
                    $india_found = true;
                    echo "✅ India found in database with ID: " . $country['id'] . "<br>";
                    break;
                }
            }
            if (!$india_found) {
                echo "⚠️ India not found in countries database<br>";
            }
        } else {
            echo "❌ Invalid JSON response from countries API<br>";
        }
    } else {
        echo "❌ Countries API failed. HTTP Code: $httpcode<br>";
    }
} else {
    echo "❌ api/countries.php file missing<br>";
}

echo "<h3>5. Current PHP Configuration</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Error Reporting: " . error_reporting() . "<br>";
echo "Display Errors: " . ini_get('display_errors') . "<br>";

echo "<h3>6. Testing index.php inclusion</h3>";
echo "Now attempting to load index.php content...<br>";
?>

<hr>
<h2>Index.php Content Below:</h2>

<?php
// Try to include the actual index.php content
ob_start();
try {
    include 'index.php';
} catch (Exception $e) {
    echo "❌ Error loading index.php: " . $e->getMessage();
}
$content = ob_get_clean();

if (empty($content)) {
    echo "❌ index.php produced no output (blank screen issue confirmed)";
} else {
    echo $content;
}
?>

