<?php
// Comprehensive API testing script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧪 API Testing Dashboard</h1>";
echo "<p>Testing all dropdown APIs for the Volunteer Registration system</p>";

$apis = [
    'Countries' => 'api/countries.php?fn=list',
    'States' => 'api/states.php?fn=list',
    'Cities' => 'api/cities.php?fn=list', 
    'Villages' => 'api/villages.php?fn=list',
    'Occupations' => 'api/occupations.php?fn=list',
    'Seva Interests' => 'api/seva_interests.php?fn=list',
    'Items' => 'api/items.php?fn=list'
];

echo "<style>
.test-result { 
    margin: 10px 0; 
    padding: 10px; 
    border-radius: 5px; 
    border: 1px solid #ddd;
}
.success { background-color: #d4edda; border-color: #c3e6cb; }
.warning { background-color: #fff3cd; border-color: #ffeaa7; }
.error { background-color: #f8d7da; border-color: #f5c6cb; }
.api-data { max-height: 200px; overflow-y: auto; background: #f8f9fa; padding: 10px; margin: 5px 0; }
</style>";

function testAPI($name, $url) {
    echo "<h3>Testing: $name</h3>";
    echo "<div class='test-result ";
    
    try {
        // Test if file exists
        $filePath = str_replace('?fn=list', '', $url);
        if (!file_exists($filePath)) {
            echo "error'>";
            echo "❌ <strong>File Missing:</strong> $filePath does not exist";
            echo "</div>";
            return;
        }
        
        // Make HTTP request
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'method' => 'GET'
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            echo "error'>";
            echo "❌ <strong>Request Failed:</strong> Could not fetch data from $url";
            echo "</div>";
            return;
        }
        
        // Parse JSON
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "error'>";
            echo "❌ <strong>JSON Error:</strong> " . json_last_error_msg();
            echo "<div class='api-data'>Raw response: " . htmlspecialchars($response) . "</div>";
            echo "</div>";
            return;
        }
        
        // Check for API errors
        if (isset($data['error'])) {
            echo "error'>";
            echo "❌ <strong>API Error:</strong> " . htmlspecialchars($data['error']);
            echo "</div>";
            return;
        }
        
        // Check for warnings (empty data)
        if (isset($data['warning'])) {
            echo "warning'>";
            echo "⚠️ <strong>Warning:</strong> " . htmlspecialchars($data['warning']);
            echo "<p>The API is working but the database table is empty.</p>";
            echo "</div>";
            return;
        }
        
        // Success case
        if (is_array($data) && count($data) > 0) {
            echo "success'>";
            echo "✅ <strong>Success:</strong> Found " . count($data) . " records";
            echo "<div class='api-data'>";
            echo "<strong>Sample data:</strong><br>";
            
            // Show first few records
            $sample = array_slice($data, 0, 5);
            foreach ($sample as $i => $record) {
                echo ($i + 1) . ". ";
                if (isset($record['country_name'])) echo htmlspecialchars($record['country_name']);
                elseif (isset($record['state_name'])) echo htmlspecialchars($record['state_name']);
                elseif (isset($record['city_name'])) echo htmlspecialchars($record['city_name']);
                elseif (isset($record['village_name'])) echo htmlspecialchars($record['village_name']);
                elseif (isset($record['occupation_name'])) echo htmlspecialchars($record['occupation_name']);
                elseif (isset($record['seva_name'])) echo htmlspecialchars($record['seva_name']);
                elseif (isset($record['item_name'])) echo htmlspecialchars($record['item_name']);
                echo " (ID: " . $record['id'] . ")<br>";
            }
            
            if (count($data) > 5) {
                echo "... and " . (count($data) - 5) . " more records";
            }
            echo "</div>";
            echo "</div>";
        } else {
            echo "warning'>";
            echo "⚠️ <strong>Empty Response:</strong> API returned empty array";
            echo "</div>";
        }
        
    } catch (Exception $e) {
        echo "error'>";
        echo "❌ <strong>Exception:</strong> " . htmlspecialchars($e->getMessage());
        echo "</div>";
    }
}

echo "<h2>🔍 Individual API Tests</h2>";

foreach ($apis as $name => $url) {
    testAPI($name, $url);
}

echo "<h2>📊 Summary</h2>";
echo "<p><strong>What to do if tests fail:</strong></p>";
echo "<ol>";
echo "<li><strong>File Missing:</strong> Make sure all API files exist in the /api/ directory</li>";
echo "<li><strong>Database Error:</strong> Run <a href='populate_sample_data.php'>populate_sample_data.php</a> to create tables and add sample data</li>";
echo "<li><strong>Empty Data:</strong> Run <a href='populate_sample_data.php'>populate_sample_data.php</a> to add sample data</li>";
echo "<li><strong>Connection Error:</strong> Check your database configuration in config.php</li>";
echo "</ol>";

echo "<h2>🎯 Next Steps</h2>";
echo "<ol>";
echo "<li><a href='populate_sample_data.php' target='_blank'>Populate Sample Data</a> (if any tests failed)</li>";
echo "<li><a href='index.php' target='_blank'>Test Registration Form</a></li>";
echo "<li>Check browser console for JavaScript errors</li>";
echo "</ol>";

echo "<hr>";
echo "<p><small>Generated at: " . date('Y-m-d H:i:s') . "</small></p>";
?>
