<?php
// Simple Database Connection Check for Hostinger File Manager
// Run this through Hostinger's file manager or cPanel

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== DATABASE CONNECTION TEST ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n";
echo "Server: " . $_SERVER['SERVER_NAME'] ?? 'Unknown' . "\n\n";

// Test 1: Check if config file exists
if (file_exists('config.php')) {
    echo "✅ config.php file found\n";
    require_once 'config.php';
    
    echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NOT DEFINED') . "\n";
    echo "DB_USER: " . (defined('DB_USER') ? DB_USER : 'NOT DEFINED') . "\n";
    echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NOT DEFINED') . "\n";
    echo "DB_PASS: " . (defined('DB_PASS') ? '[SET]' : 'NOT DEFINED') . "\n\n";
} else {
    echo "❌ config.php file NOT found\n";
    exit;
}

// Test 2: Check MySQLi extension
if (extension_loaded('mysqli')) {
    echo "✅ MySQLi extension loaded\n";
} else {
    echo "❌ MySQLi extension NOT loaded\n";
    exit;
}

// Test 3: Attempt database connection
echo "\n=== TESTING CONNECTION ===\n";
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        echo "❌ Connection FAILED: " . $conn->connect_error . "\n";
        echo "Error Number: " . $conn->connect_errno . "\n";
    } else {
        echo "✅ Connection SUCCESSFUL!\n";
        echo "Host Info: " . $conn->host_info . "\n";
        echo "Server Info: " . $conn->server_info . "\n";
        echo "Protocol Version: " . $conn->protocol_version . "\n";
        
        // Test simple query
        $result = $conn->query("SELECT 1 as test");
        if ($result) {
            echo "✅ Query test PASSED\n";
            
            // Check tables
            echo "\n=== CHECKING TABLES ===\n";
            $tables = ['villages', 'occupations', 'seva_interests', 'items', 'cities', 'states', 'countries', 'volunteers'];
            
            foreach ($tables as $table) {
                $result = $conn->query("SHOW TABLES LIKE '$table'");
                if ($result && $result->num_rows > 0) {
                    $count_result = $conn->query("SELECT COUNT(*) as count FROM $table");
                    if ($count_result) {
                        $row = $count_result->fetch_assoc();
                        echo "✅ $table: {$row['count']} records\n";
                    } else {
                        echo "⚠️  $table: exists but count failed - " . $conn->error . "\n";
                    }
                } else {
                    echo "❌ $table: NOT EXISTS\n";
                }
            }
        } else {
            echo "❌ Query test FAILED: " . $conn->error . "\n";
        }
        
        $conn->close();
    }
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
echo "Please copy this entire output and share with your developer.\n";
?>
