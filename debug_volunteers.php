<?php
require_once 'config.php';

echo "<h2>🔍 Volunteers API Debug</h2>";
echo "<div style='font-family: monospace; background: #f5f5f5; padding: 15px; border-radius: 5px;'>";

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<strong>✅ Database Connected</strong><br><br>";
    
    // Check if volunteers table has data
    echo "<strong>1. Direct volunteers table check:</strong><br>";
    $result = $conn->query("SELECT COUNT(*) as count FROM volunteers");
    $count = $result->fetch_assoc()['count'];
    echo "Total volunteers: $count<br><br>";
    
    if ($count > 0) {
        echo "<strong>2. Sample volunteer data (raw):</strong><br>";
        $result = $conn->query("SELECT * FROM volunteers LIMIT 3");
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['id']}, Name: {$row['name']}, Mobile: {$row['mobile']}<br>";
        }
        echo "<br>";
        
        // Check if reference tables exist
        echo "<strong>3. Reference tables check:</strong><br>";
        $tables = ['villages', 'cities', 'states', 'countries', 'occupations', 'seva_interests'];
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            if ($result->num_rows > 0) {
                $count_result = $conn->query("SELECT COUNT(*) as count FROM $table");
                $count = $count_result->fetch_assoc()['count'];
                echo "$table: ✅ exists ($count records)<br>";
            } else {
                echo "$table: ❌ missing<br>";
            }
        }
        echo "<br>";
        
        // Test simple query without JOINs
        echo "<strong>4. Simple query test (no JOINs):</strong><br>";
        $result = $conn->query("SELECT id, name, mobile, email FROM volunteers ORDER BY created_at DESC LIMIT 3");
        if ($result) {
            echo "✅ Simple query works<br>";
            while ($row = $result->fetch_assoc()) {
                echo "- ID: {$row['id']}, Name: {$row['name']}<br>";
            }
        } else {
            echo "❌ Simple query failed: " . $conn->error . "<br>";
        }
        echo "<br>";
        
        // Test complex query with JOINs
        echo "<strong>5. Complex query test (with JOINs):</strong><br>";
        $query = "
            SELECT v.id, v.name, v.mobile, v.email, v.items_brought,
                   v.phone, v.dob, v.remarks,
                   o.occupation_name, s.seva_name, g.village_name,
                   c.city_name, st.state_name, co.country_name,
                   DATE_FORMAT(v.created_at, '%Y-%m-%d %H:%i') AS created_at
            FROM volunteers v
            LEFT JOIN occupations o ON v.occupation_id = o.id
            LEFT JOIN seva_interests s ON v.seva_interest_id = s.id
            LEFT JOIN villages g ON v.village_id = g.id
            LEFT JOIN cities c ON v.city_id = c.id
            LEFT JOIN states st ON v.state_id = st.id
            LEFT JOIN countries co ON v.country_id = co.id
            ORDER BY v.created_at DESC
            LIMIT 3
        ";
        
        $result = $conn->query($query);
        if ($result) {
            echo "✅ Complex query works<br>";
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
                echo "- ID: {$row['id']}, Name: {$row['name']}, Village: " . ($row['village_name'] ?? 'NULL') . "<br>";
            }
            
            echo "<br><strong>6. JSON Response Test:</strong><br>";
            echo "<pre style='background: white; padding: 10px; border: 1px solid #ccc; max-height: 300px; overflow: auto;'>";
            echo json_encode($data, JSON_PRETTY_PRINT);
            echo "</pre>";
            
        } else {
            echo "❌ Complex query failed: " . $conn->error . "<br>";
        }
        
    } else {
        echo "<strong>❌ No volunteers found in database!</strong><br>";
        echo "Please register at least one volunteer first.<br>";
    }
    
} catch (Exception $e) {
    echo "<strong>❌ Error: " . $e->getMessage() . "</strong><br>";
}

echo "</div>";

echo "<h3>📋 Next Steps:</h3>";
echo "<ul>";
echo "<li>If reference tables are missing: Run setup_hostinger_database.sql</li>";
echo "<li>If no volunteers: Register one via <a href='index.php'>the form</a></li>";
echo "<li>If query fails: Check the specific error message above</li>";
echo "<li>If JSON looks good: The issue is in the frontend JavaScript</li>";
echo "</ul>";
?>
