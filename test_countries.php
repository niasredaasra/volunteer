<?php
// Test script to check countries in database
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Countries Database Test</h2>";

try {
    require_once 'api/db.php';
    $conn = db();
    
    echo "<h3>All Countries in Database:</h3>";
    $res = $conn->query("SELECT id, country_name FROM countries ORDER BY country_name");
    
    if ($res && $res->num_rows > 0) {
        $indiaFound = false;
        $indiaId = null;
        
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Country Name</th><th>Is India?</th></tr>";
        
        while ($row = $res->fetch_assoc()) {
            $isIndia = (strtolower($row['country_name']) === 'india') ? 'YES' : 'No';
            if ($isIndia === 'YES') {
                $indiaFound = true;
                $indiaId = $row['id'];
            }
            
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['country_name']) . "</td>";
            echo "<td><strong>" . $isIndia . "</strong></td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<h3>Results:</h3>";
        if ($indiaFound) {
            echo "✅ <strong>India found in database with ID: $indiaId</strong><br>";
        } else {
            echo "❌ <strong>India NOT found in database</strong><br>";
            echo "Need to add India to countries table<br>";
        }
    } else {
        echo "❌ No countries found in database or countries table doesn't exist<br>";
        
        // Try to create countries table and add India
        echo "<h3>Attempting to create countries table and add India:</h3>";
        
        $createTable = "CREATE TABLE IF NOT EXISTS countries (
            id int(11) NOT NULL AUTO_INCREMENT,
            country_name varchar(100) NOT NULL,
            PRIMARY KEY (id)
        )";
        
        if ($conn->query($createTable)) {
            echo "✅ Countries table created/verified<br>";
            
            // Add India
            $insertIndia = "INSERT INTO countries (country_name) VALUES ('India')";
            if ($conn->query($insertIndia)) {
                echo "✅ India added to countries table<br>";
            } else {
                echo "❌ Error adding India: " . $conn->error . "<br>";
            }
        } else {
            echo "❌ Error creating countries table: " . $conn->error . "<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

echo "<hr>";
echo "<h3>API Test:</h3>";
echo "<a href='api/countries.php?fn=list' target='_blank'>Test Countries API</a><br>";
echo "<a href='index.php' target='_blank'>Back to Registration Form</a>";
?>

