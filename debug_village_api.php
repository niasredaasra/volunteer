<?php
// Debug village API - upload this to test village add functionality
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Village API Debug</h2>";
echo "<hr>";

// Test database connection
require_once 'api/db.php';
try {
    $conn = db();
    echo "✅ Database connected<br>";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
    exit;
}

// Check if villages table exists
echo "<h3>1. Villages Table Check:</h3>";
$result = $conn->query("SHOW TABLES LIKE 'villages'");
if ($result && $result->num_rows > 0) {
    echo "✅ Villages table exists<br>";
    
    // Check table structure
    $result = $conn->query("DESCRIBE villages");
    echo "<strong>Table Structure:</strong><br>";
    while ($row = $result->fetch_assoc()) {
        echo "- {$row['Field']}: {$row['Type']}<br>";
    }
    
    // Check existing records
    $result = $conn->query("SELECT COUNT(*) as count FROM villages");
    $count = $result->fetch_assoc()['count'];
    echo "<strong>Existing Records:</strong> $count<br>";
    
} else {
    echo "❌ Villages table does NOT exist<br>";
    echo "<strong>Creating villages table...</strong><br>";
    
    $create_sql = "CREATE TABLE villages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        village_name VARCHAR(100) NOT NULL
    )";
    
    if ($conn->query($create_sql)) {
        echo "✅ Villages table created<br>";
        
        // Insert default villages
        $insert_sql = "INSERT INTO villages (village_name) VALUES 
            ('Mustafabad'), ('Saran'), ('Delhi'), ('Gurgaon'), ('Other')";
        
        if ($conn->query($insert_sql)) {
            echo "✅ Default villages inserted<br>";
        } else {
            echo "❌ Error inserting villages: " . $conn->error . "<br>";
        }
    } else {
        echo "❌ Error creating table: " . $conn->error . "<br>";
    }
}

// Test adding a new village
echo "<hr>";
echo "<h3>2. Test Adding Village:</h3>";

$test_village = "Test Village " . date('H:i:s');
$stmt = $conn->prepare("INSERT INTO villages (village_name) VALUES (?)");
$stmt->bind_param("s", $test_village);

if ($stmt->execute()) {
    echo "✅ Test village added successfully<br>";
    echo "New village ID: " . $stmt->insert_id . "<br>";
    echo "Village name: $test_village<br>";
} else {
    echo "❌ Error adding test village: " . $stmt->error . "<br>";
}
$stmt->close();

// Show all villages
echo "<hr>";
echo "<h3>3. All Villages:</h3>";
$result = $conn->query("SELECT id, village_name FROM villages ORDER BY village_name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['id']}, Name: {$row['village_name']}<br>";
    }
} else {
    echo "❌ Error fetching villages: " . $conn->error . "<br>";
}

echo "<hr>";
echo "<h3>4. API Test:</h3>";
echo "✅ Village API should now work!<br>";
echo "Try adding village from main form again.<br>";
?>

