<?php
// Setup script to fix database issues and ensure India is in countries table
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Volunteer App Setup & Fix Script</h1>";

try {
    // Test database connection
    echo "<h2>1. Testing Database Connection</h2>";
    require_once 'config.php';
    require_once 'api/db.php';
    
    $conn = db();
    echo "✅ Database connection successful<br>";
    
    // Check if countries table exists
    echo "<h2>2. Checking Countries Table</h2>";
    $result = $conn->query("SHOW TABLES LIKE 'countries'");
    if ($result->num_rows > 0) {
        echo "✅ Countries table exists<br>";
    } else {
        echo "❌ Countries table missing. Creating...<br>";
        $createCountries = "CREATE TABLE countries (
            id int(11) NOT NULL AUTO_INCREMENT,
            country_name varchar(100) NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY unique_country (country_name)
        )";
        if ($conn->query($createCountries)) {
            echo "✅ Countries table created<br>";
        } else {
            echo "❌ Error creating countries table: " . $conn->error . "<br>";
        }
    }
    
    // Check if India exists in countries
    echo "<h2>3. Checking India in Countries</h2>";
    $indiaCheck = $conn->query("SELECT id FROM countries WHERE LOWER(country_name) = 'india'");
    if ($indiaCheck && $indiaCheck->num_rows > 0) {
        $indiaRow = $indiaCheck->fetch_assoc();
        echo "✅ India exists in countries table with ID: " . $indiaRow['id'] . "<br>";
    } else {
        echo "❌ India not found. Adding India...<br>";
        $insertIndia = "INSERT INTO countries (country_name) VALUES ('India') ON DUPLICATE KEY UPDATE country_name = 'India'";
        if ($conn->query($insertIndia)) {
            echo "✅ India added to countries table<br>";
            $indiaId = $conn->insert_id;
            echo "India ID: $indiaId<br>";
        } else {
            echo "❌ Error adding India: " . $conn->error . "<br>";
        }
    }
    
    // Check other required tables
    echo "<h2>4. Checking Other Required Tables</h2>";
    $requiredTables = ['states', 'cities', 'villages', 'occupations', 'seva_interests', 'items', 'volunteers'];
    
    foreach ($requiredTables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows > 0) {
            echo "✅ Table '$table' exists<br>";
        } else {
            echo "⚠️ Table '$table' missing<br>";
        }
    }
    
    // Add some basic countries if table is empty
    echo "<h2>5. Adding Basic Countries Data</h2>";
    $countResult = $conn->query("SELECT COUNT(*) as count FROM countries");
    $countRow = $countResult->fetch_assoc();
    
    if ($countRow['count'] < 5) {
        echo "Adding basic countries...<br>";
        $basicCountries = ['India', 'United States', 'United Kingdom', 'Canada', 'Australia'];
        
        foreach ($basicCountries as $country) {
            $stmt = $conn->prepare("INSERT INTO countries (country_name) VALUES (?) ON DUPLICATE KEY UPDATE country_name = ?");
            $stmt->bind_param("ss", $country, $country);
            if ($stmt->execute()) {
                echo "✅ Added/Updated: $country<br>";
            } else {
                echo "❌ Error with $country: " . $stmt->error . "<br>";
            }
            $stmt->close();
        }
    } else {
        echo "✅ Countries table has sufficient data (" . $countRow['count'] . " countries)<br>";
    }
    
    echo "<h2>6. Final Verification</h2>";
    $allCountries = $conn->query("SELECT id, country_name FROM countries ORDER BY country_name");
    echo "<strong>All countries in database:</strong><br>";
    echo "<ul>";
    while ($row = $allCountries->fetch_assoc()) {
        $highlight = (strtolower($row['country_name']) === 'india') ? ' style="color: green; font-weight: bold;"' : '';
        echo "<li$highlight>ID: {$row['id']} - {$row['country_name']}</li>";
    }
    echo "</ul>";
    
    echo "<h2>✅ Setup Complete!</h2>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li><a href='debug_index.php' target='_blank'>Run Debug Test</a></li>";
    echo "<li><a href='test_countries.php' target='_blank'>Test Countries API</a></li>";
    echo "<li><a href='index.php' target='_blank'>Test Registration Form</a></li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error During Setup</h2>";
    echo "Error: " . $e->getMessage() . "<br>";
    echo "Please check your database configuration in config.php";
}
?>

