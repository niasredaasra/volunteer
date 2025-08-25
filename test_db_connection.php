<?php
// Upload this file to test database connection on Hostinger

require_once 'api/db.php';

echo "<h2>Database Connection Test</h2>";

try {
    $conn = db();
    echo "<p style='color: green;'>✅ Database connected successfully!</p>";
    echo "<p><strong>Server Info:</strong> " . $conn->host_info . "</p>";
    
    // Test tables
    $tables = ['villages', 'occupations', 'seva_interests', 'items', 'cities', 'states', 'countries', 'volunteers'];
    
    echo "<h3>Table Status:</h3>";
    foreach ($tables as $table) {
        $result = $conn->query("SELECT COUNT(*) as count FROM $table");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<p>✅ <strong>$table:</strong> {$row['count']} records</p>";
        } else {
            echo "<p>❌ <strong>$table:</strong> Error - " . $conn->error . "</p>";
        }
    }
    
    echo "<p style='color: green;'><strong>🎉 Database setup complete! Ready to use.</strong></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    echo "<p><strong>Check:</strong></p>";
    echo "<ul>";
    echo "<li>Database credentials in config.php</li>";
    echo "<li>Database exists in Hostinger panel</li>";
    echo "<li>Username/password are correct</li>";
    echo "</ul>";
}
?>

