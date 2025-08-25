<?php
// Simple database test - upload this to /volunteer/volunteer_app/ folder
echo "Database Test for sevakaro.in<br><br>";

// Test 1: PHP Info
echo "<strong>1. PHP Version:</strong> " . phpversion() . "<br>";

// Test 2: Config file
if (file_exists('config.php')) {
    echo "<strong>2. Config file:</strong> EXISTS<br>";
    include 'config.php';
    
    if (defined('DB_HOST')) {
        echo "<strong>3. DB_HOST:</strong> " . DB_HOST . "<br>";
        echo "<strong>4. DB_USER:</strong> " . DB_USER . "<br>";
        echo "<strong>5. DB_NAME:</strong> " . DB_NAME . "<br>";
        
        // Test database connection
        $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            echo "<strong>6. Database:</strong> FAILED - " . $conn->connect_error . "<br>";
        } else {
            echo "<strong>6. Database:</strong> CONNECTED ✅<br>";
        }
    } else {
        echo "<strong>3. Database config:</strong> NOT DEFINED<br>";
    }
} else {
    echo "<strong>2. Config file:</strong> NOT FOUND<br>";
}

// Test 3: Directory structure
echo "<br><strong>Current directory:</strong> " . __DIR__ . "<br>";
echo "<strong>Files in directory:</strong><br>";
$files = glob('*');
foreach($files as $file) {
    echo "- $file<br>";
}
?>

