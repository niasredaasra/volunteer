<?php
// Script to populate sample data for testing dropdowns
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Populate Sample Data for Volunteer App</h1>";

try {
    require_once 'config.php';
    require_once 'api/db.php';
    
    $conn = db();
    echo "<p>✅ Database connection successful</p>";
    
    // Function to create table if it doesn't exist
    function createTableIfNotExists($conn, $tableName, $createSql) {
        $result = $conn->query("SHOW TABLES LIKE '$tableName'");
        if ($result->num_rows == 0) {
            if ($conn->query($createSql)) {
                echo "<p>✅ Created table: $tableName</p>";
            } else {
                echo "<p>❌ Error creating table $tableName: " . $conn->error . "</p>";
                return false;
            }
        } else {
            echo "<p>✅ Table $tableName already exists</p>";
        }
        return true;
    }
    
    // Function to insert sample data
    function insertSampleData($conn, $tableName, $columnName, $data) {
        $count = 0;
        foreach ($data as $item) {
            $stmt = $conn->prepare("INSERT IGNORE INTO $tableName ($columnName) VALUES (?)");
            $stmt->bind_param("s", $item);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) $count++;
            }
            $stmt->close();
        }
        echo "<p>✅ Inserted $count new records into $tableName</p>";
    }
    
    echo "<h2>Creating Tables</h2>";
    
    // Create countries table
    createTableIfNotExists($conn, 'countries', "
        CREATE TABLE countries (
            id int(11) NOT NULL AUTO_INCREMENT,
            country_name varchar(100) NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY unique_country (country_name)
        )
    ");
    
    // Create states table
    createTableIfNotExists($conn, 'states', "
        CREATE TABLE states (
            id int(11) NOT NULL AUTO_INCREMENT,
            state_name varchar(100) NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY unique_state (state_name)
        )
    ");
    
    // Create cities table
    createTableIfNotExists($conn, 'cities', "
        CREATE TABLE cities (
            id int(11) NOT NULL AUTO_INCREMENT,
            city_name varchar(100) NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY unique_city (city_name)
        )
    ");
    
    // Create villages table
    createTableIfNotExists($conn, 'villages', "
        CREATE TABLE villages (
            id int(11) NOT NULL AUTO_INCREMENT,
            village_name varchar(100) NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY unique_village (village_name)
        )
    ");
    
    // Create occupations table
    createTableIfNotExists($conn, 'occupations', "
        CREATE TABLE occupations (
            id int(11) NOT NULL AUTO_INCREMENT,
            occupation_name varchar(100) NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY unique_occupation (occupation_name)
        )
    ");
    
    // Create seva_interests table
    createTableIfNotExists($conn, 'seva_interests', "
        CREATE TABLE seva_interests (
            id int(11) NOT NULL AUTO_INCREMENT,
            seva_name varchar(100) NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY unique_seva (seva_name)
        )
    ");
    
    // Create items table
    createTableIfNotExists($conn, 'items', "
        CREATE TABLE items (
            id int(11) NOT NULL AUTO_INCREMENT,
            item_name varchar(100) NOT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY unique_item (item_name)
        )
    ");
    
    echo "<h2>Inserting Sample Data</h2>";
    
    // Sample countries
    $countries = [
        'India', 'United States', 'United Kingdom', 'Canada', 'Australia', 
        'Germany', 'France', 'Japan', 'Brazil', 'South Africa'
    ];
    insertSampleData($conn, 'countries', 'country_name', $countries);
    
    // Sample Indian states
    $states = [
        'Punjab', 'Haryana', 'Delhi', 'Uttar Pradesh', 'Bihar', 
        'Maharashtra', 'Karnataka', 'Tamil Nadu', 'Kerala', 'Gujarat',
        'Rajasthan', 'Madhya Pradesh', 'West Bengal', 'Odisha', 'Assam'
    ];
    insertSampleData($conn, 'states', 'state_name', $states);
    
    // Sample cities
    $cities = [
        'Amritsar', 'Chandigarh', 'New Delhi', 'Mumbai', 'Bangalore',
        'Chennai', 'Kolkata', 'Hyderabad', 'Pune', 'Ahmedabad',
        'Jaipur', 'Lucknow', 'Kanpur', 'Nagpur', 'Indore'
    ];
    insertSampleData($conn, 'cities', 'city_name', $cities);
    
    // Sample villages
    $villages = [
        'Anandpur Sahib', 'Kiratpur', 'Chamkaur Sahib', 'Fatehgarh Sahib',
        'Ropar', 'Mohali', 'Kharar', 'Kurali', 'Morinda', 'Samrala',
        'Khanna', 'Doraha', 'Sahnewal', 'Raikot', 'Malerkotla'
    ];
    insertSampleData($conn, 'villages', 'village_name', $villages);
    
    // Sample occupations
    $occupations = [
        'Student', 'Teacher', 'Engineer', 'Doctor', 'Farmer',
        'Business Owner', 'Software Developer', 'Nurse', 'Driver', 'Shopkeeper',
        'Government Employee', 'Private Employee', 'Retired', 'Homemaker', 'Self Employed'
    ];
    insertSampleData($conn, 'occupations', 'occupation_name', $occupations);
    
    // Sample seva interests
    $seva_interests = [
        'Langar Seva', 'Cleaning Seva', 'Kirtan', 'Path', 'Teaching',
        'Security', 'Medical Help', 'Traffic Management', 'Registration', 'Parking',
        'Stage Management', 'Sound System', 'Photography', 'Social Media', 'Translation'
    ];
    insertSampleData($conn, 'seva_interests', 'seva_name', $seva_interests);
    
    // Sample items
    $items = [
        'Water Bottles', 'Food Packets', 'Blankets', 'Medicines', 'Books',
        'Clothes', 'Shoes', 'Utensils', 'Bags', 'Stationery',
        'Toys', 'Electronic Items', 'Sports Equipment', 'Musical Instruments', 'Tools'
    ];
    insertSampleData($conn, 'items', 'item_name', $items);
    
    echo "<h2>✅ Sample Data Population Complete!</h2>";
    
    // Show summary
    echo "<h3>Summary:</h3>";
    $tables = ['countries', 'states', 'cities', 'villages', 'occupations', 'seva_interests', 'items'];
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Table</th><th>Record Count</th></tr>";
    
    foreach ($tables as $table) {
        $result = $conn->query("SELECT COUNT(*) as count FROM $table");
        $row = $result->fetch_assoc();
        echo "<tr><td>$table</td><td>{$row['count']}</td></tr>";
    }
    echo "</table>";
    
    echo "<br><p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li><a href='index.php' target='_blank'>Test Registration Form</a></li>";
    echo "<li><a href='api/countries.php?fn=list' target='_blank'>Test Countries API</a></li>";
    echo "<li><a href='api/states.php?fn=list' target='_blank'>Test States API</a></li>";
    echo "<li><a href='api/cities.php?fn=list' target='_blank'>Test Cities API</a></li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration in config.php</p>";
}
?>

