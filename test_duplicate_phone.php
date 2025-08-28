<?php
// Test script to verify duplicate phone number prevention
// Run this in browser: http://localhost/volunteer_app/test_duplicate_phone.php

require_once __DIR__ . '/api/db.php';

echo "<h2>Testing Duplicate Phone Number Prevention</h2>";

// Simulate form data for testing
$test_data = [
    'name' => 'Test User',
    'mobile' => '9999999999',
    'phone' => '8888888888', // This will be our test phone number
    'email' => 'test@example.com'
];

echo "<h3>Test 1: Adding first volunteer with phone 8888888888</h3>";

// Test 1: Add first volunteer
$_POST = $test_data;
$_POST['fn'] = 'add';

ob_start();
include 'api/volunteers.php';
$response1 = ob_get_clean();

echo "<strong>Response:</strong> " . htmlspecialchars($response1) . "<br><br>";

echo "<h3>Test 2: Trying to add second volunteer with same phone 8888888888</h3>";

// Test 2: Try to add duplicate phone
$test_data2 = [
    'name' => 'Another User',
    'mobile' => '7777777777',
    'phone' => '8888888888', // Same phone number
    'email' => 'another@example.com'
];

$_POST = $test_data2;
$_POST['fn'] = 'add';

ob_start();
include 'api/volunteers.php';
$response2 = ob_get_clean();

echo "<strong>Response:</strong> " . htmlspecialchars($response2) . "<br><br>";

echo "<h3>Test 3: Adding volunteer with different phone number</h3>";

// Test 3: Add with different phone
$test_data3 = [
    'name' => 'Third User',
    'mobile' => '6666666666',
    'phone' => '5555555555', // Different phone number
    'email' => 'third@example.com'
];

$_POST = $test_data3;
$_POST['fn'] = 'add';

ob_start();
include 'api/volunteers.php';
$response3 = ob_get_clean();

echo "<strong>Response:</strong> " . htmlspecialchars($response3) . "<br><br>";

echo "<h3>Current volunteers with phone numbers:</h3>";

// Show current volunteers
$conn = db();
$result = $conn->query("SELECT id, name, phone, mobile FROM volunteers WHERE phone IS NOT NULL ORDER BY id DESC LIMIT 10");

if ($result && $result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Phone</th><th>Mobile</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
        echo "<td>" . htmlspecialchars($row['mobile']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No volunteers found with phone numbers.";
}

echo "<br><br><strong>Note:</strong> If Test 2 shows an error about duplicate phone number, then the duplicate prevention is working correctly!";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { margin: 10px 0; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
</style>

