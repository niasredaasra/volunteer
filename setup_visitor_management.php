<?php
// Setup script for visitor management
require_once 'api/db.php';

echo "=== Visitor Management Setup ===\n";
echo "This script will create the visitor management tables and populate sample data.\n\n";

try {
    $conn = db();
    echo "✓ Database connection successful\n\n";
    
    // Read and execute the SQL schema
    $sql = file_get_contents('create_visitor_management_schema.sql');
    if (!$sql) {
        throw new Exception("Could not read schema file");
    }
    
    // Execute multi-query
    echo "Executing database schema...\n";
    if ($conn->multi_query($sql)) {
        do {
            // Fetch results to clear them
            if ($result = $conn->store_result()) {
                while ($row = $result->fetch_assoc()) {
                    if (isset($row['Status'])) {
                        echo "✓ " . $row['Status'] . "\n";
                    }
                }
                $result->free();
            }
        } while ($conn->next_result());
        echo "✓ Database schema executed successfully\n";
    } else {
        echo "✗ Error executing schema: " . $conn->error . "\n";
    }
    
    echo "\n=== Database Schema Summary ===\n";
    
    // Check visitors table
    $result = $conn->query("SHOW TABLES LIKE 'visitors'");
    if ($result->num_rows > 0) {
        echo "✓ visitors table exists\n";
        
        $result = $conn->query("SELECT COUNT(*) as count FROM visitors");
        $row = $result->fetch_assoc();
        echo "  - Contains " . $row['count'] . " records\n";
    } else {
        echo "✗ visitors table missing\n";
    }
    
    // Check visitor_visits table
    $result = $conn->query("SHOW TABLES LIKE 'visitor_visits'");
    if ($result->num_rows > 0) {
        echo "✓ visitor_visits table exists\n";
        
        $result = $conn->query("SELECT COUNT(*) as count FROM visitor_visits");
        $row = $result->fetch_assoc();
        echo "  - Contains " . $row['count'] . " visit records\n";
    } else {
        echo "✗ visitor_visits table missing\n";
    }
    
    echo "\n=== Testing API Endpoints ===\n";
    
    // Test check-mobile endpoint
    if (function_exists('curl_init')) {
        $test_mobile = '9876543210';
        $url = 'http://localhost/volunteer_app/api/visitors.php?fn=check-mobile&mobile=' . $test_mobile;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code === 200) {
            $data = json_decode($response, true);
            if ($data && isset($data['exists'])) {
                echo "✓ check-mobile API working - mobile " . $test_mobile . " exists: " . ($data['exists'] ? 'yes' : 'no') . "\n";
            } else {
                echo "⚠ check-mobile API returned unexpected response\n";
            }
        } else {
            echo "⚠ check-mobile API test failed (HTTP $http_code)\n";
        }
    } else {
        echo "⚠ cURL not available for API testing\n";
    }
    
    echo "\n=== Setup Complete! ===\n";
    echo "You can now access the visitor management system at:\n";
    echo "- visitor_management.html (new React-based interface)\n";
    echo "- index.php (original volunteer registration)\n\n";
    
    echo "API Endpoints available:\n";
    echo "- GET api/visitors.php?fn=check-mobile&mobile=XXXXXXXXXX\n";
    echo "- POST api/visitors.php?fn=add-visitor\n";
    echo "- POST api/visitors.php?fn=add-visit&mobile=XXXXXXXXXX\n";
    echo "- GET api/visitors.php?fn=get-history&mobile=XXXXXXXXXX\n";
    echo "- GET api/visitors.php?fn=list\n\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Please check your database connection and try again.\n";
}
?>
