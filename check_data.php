<?php
// Quick database checker to see what tables and data exist
require_once 'config.php';

echo "<h2>📊 Database Data Check</h2>";
echo "<div style='font-family: monospace; background: #f5f5f5; padding: 15px; border-radius: 5px;'>";

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<strong>✅ Database Connected: " . DB_NAME . "</strong><br><br>";
    
    // Check which tables exist
    echo "<strong>📋 Available Tables:</strong><br>";
    $result = $conn->query("SHOW TABLES");
    $tables = [];
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
        echo "- " . $row[0] . "<br>";
    }
    echo "<br>";
    
    // Check each relevant table for data
    foreach (['volunteers', 'visitors', 'visitor_visits', 'villages', 'cities', 'states', 'countries', 'occupations', 'seva_interests', 'items'] as $table) {
        if (in_array($table, $tables)) {
            $result = $conn->query("SELECT COUNT(*) as count FROM `$table`");
            $count = $result->fetch_assoc()['count'];
            echo "<strong>$table:</strong> $count records<br>";
            
            // Show sample data for main tables
            if (in_array($table, ['volunteers', 'visitors']) && $count > 0) {
                echo "  <em>Sample records:</em><br>";
                $sample = $conn->query("SELECT id, name, mobile FROM `$table` LIMIT 3");
                while ($row = $sample->fetch_assoc()) {
                    echo "    ID: {$row['id']}, Name: {$row['name']}, Mobile: {$row['mobile']}<br>";
                }
            }
            echo "<br>";
        } else {
            echo "<strong style='color: red;'>$table:</strong> ❌ Table does not exist<br><br>";
        }
    }
    
    // Test API endpoints
    echo "<strong>🔌 API Endpoint Tests:</strong><br>";
    
    // Test volunteers API
    ob_start();
    include 'api/volunteers.php';
    $volunteers_output = ob_get_clean();
    
    echo "Volunteers API: ";
    $volunteers_data = json_decode($volunteers_output, true);
    if (is_array($volunteers_data)) {
        echo "✅ Working (" . count($volunteers_data) . " volunteers)<br>";
    } else {
        echo "❌ Error or no data<br>";
        echo "  Raw output: " . substr($volunteers_output, 0, 200) . "...<br>";
    }
    
    echo "<br>";
    
} catch (Exception $e) {
    echo "<strong style='color: red;'>❌ Error: " . $e->getMessage() . "</strong><br>";
}

echo "</div>";

echo "<h3>🛠️ Next Steps:</h3>";
echo "<ul>";
echo "<li>If tables are missing: Run <code>setup_hostinger_database.sql</code> in phpMyAdmin</li>";
echo "<li>If visitor tables are missing: Run <code>fix_visitor_visits_table.sql</code></li>";
echo "<li>If no data: Try registering a volunteer through <a href='index.php'>the form</a></li>";
echo "<li>Check the <a href='admin/list.php'>admin panel</a> after this</li>";
echo "</ul>";
?>
