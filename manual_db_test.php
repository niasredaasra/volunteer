<?php
// Manual Database Connection Test
// Upload this file to your live server and test with manual input

$test_result = '';
$connection_details = '';

if ($_POST['test_connection']) {
    $host = $_POST['db_host'] ?? '';
    $user = $_POST['db_user'] ?? '';
    $pass = $_POST['db_pass'] ?? '';
    $name = $_POST['db_name'] ?? '';
    
    $connection_details = "Testing with: Host=$host, User=$user, Database=$name";
    
    try {
        // Attempt connection
        $conn = new mysqli($host, $user, $pass, $name);
        
        if ($conn->connect_error) {
            $test_result = "<div style='color: red; background: #ffe6e6; padding: 10px; margin: 10px 0; border-radius: 5px;'>
                ❌ <strong>Connection Failed:</strong> " . $conn->connect_error . "
            </div>";
        } else {
            $test_result = "<div style='color: green; background: #e6ffe6; padding: 10px; margin: 10px 0; border-radius: 5px;'>
                ✅ <strong>Connection Successful!</strong><br>
                Server Info: " . $conn->host_info . "<br>
                MySQL Version: " . $conn->server_info . "
            </div>";
            
            // Test basic query
            $result = $conn->query("SELECT 1 as test");
            if ($result) {
                $test_result .= "<div style='color: green; background: #e6ffe6; padding: 10px; margin: 10px 0; border-radius: 5px;'>
                    ✅ <strong>Query Test Passed:</strong> Can execute queries
                </div>";
                
                // Test if tables exist
                $tables = ['villages', 'occupations', 'seva_interests', 'items', 'cities', 'states', 'countries', 'volunteers'];
                $table_status = "<div style='background: #f0f0f0; padding: 10px; margin: 10px 0; border-radius: 5px;'>
                    <strong>Table Status:</strong><br>";
                
                foreach ($tables as $table) {
                    $result = $conn->query("SELECT COUNT(*) as count FROM $table");
                    if ($result) {
                        $row = $result->fetch_assoc();
                        $table_status .= "✅ <strong>$table:</strong> {$row['count']} records<br>";
                    } else {
                        $table_status .= "❌ <strong>$table:</strong> " . $conn->error . "<br>";
                    }
                }
                $table_status .= "</div>";
                $test_result .= $table_status;
                
            } else {
                $test_result .= "<div style='color: orange; background: #fff3cd; padding: 10px; margin: 10px 0; border-radius: 5px;'>
                    ⚠️ <strong>Query Test Failed:</strong> " . $conn->error . "
                </div>";
            }
            
            $conn->close();
        }
        
    } catch (Exception $e) {
        $test_result = "<div style='color: red; background: #ffe6e6; padding: 10px; margin: 10px 0; border-radius: 5px;'>
            ❌ <strong>Exception:</strong> " . $e->getMessage() . "
        </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual Database Connection Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #007cba;
            outline: none;
        }
        button {
            background: #007cba;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background: #005a8b;
        }
        .help-text {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .current-config {
            background: #e6f3ff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Manual Database Connection Test</h1>
        
        <div class="current-config">
            <strong>Current config.php values:</strong><br>
            Host: localhost<br>
            User: u231942554_volunteer<br>
            Database: u231942554_volunteer<br>
            <em>You can test with these values or enter different ones below</em>
        </div>

        <form method="POST">
            <div class="form-group">
                <label for="db_host">Database Host:</label>
                <input type="text" id="db_host" name="db_host" 
                       value="<?php echo $_POST['db_host'] ?? 'localhost'; ?>" required>
                <div class="help-text">Usually 'localhost' for Hostinger</div>
            </div>

            <div class="form-group">
                <label for="db_user">Database Username:</label>
                <input type="text" id="db_user" name="db_user" 
                       value="<?php echo $_POST['db_user'] ?? 'u231942554_volunteer'; ?>" required>
                <div class="help-text">Your Hostinger database username</div>
            </div>

            <div class="form-group">
                <label for="db_pass">Database Password:</label>
                <input type="password" id="db_pass" name="db_pass" 
                       value="<?php echo $_POST['db_pass'] ?? 'PwD$12345'; ?>" required>
                <div class="help-text">Your Hostinger database password</div>
            </div>

            <div class="form-group">
                <label for="db_name">Database Name:</label>
                <input type="text" id="db_name" name="db_name" 
                       value="<?php echo $_POST['db_name'] ?? 'u231942554_volunteer'; ?>" required>
                <div class="help-text">Your Hostinger database name</div>
            </div>

            <button type="submit" name="test_connection" value="1">🧪 Test Connection</button>
        </form>

        <?php if ($connection_details): ?>
            <div style="background: #f8f9fa; padding: 10px; margin: 20px 0; border-radius: 5px;">
                <strong>Connection Details:</strong> <?php echo htmlspecialchars($connection_details); ?>
            </div>
        <?php endif; ?>

        <?php if ($test_result): ?>
            <div style="margin-top: 20px;">
                <h3>Test Results:</h3>
                <?php echo $test_result; ?>
            </div>
        <?php endif; ?>

        <div style="margin-top: 30px; padding: 15px; background: #fff3cd; border-radius: 5px;">
            <strong>📋 Instructions:</strong>
            <ol>
                <li>Upload this file to your live server (same directory as config.php)</li>
                <li>Open it in your browser: http://yourdomain.com/manual_db_test.php</li>
                <li>Enter your database credentials manually</li>
                <li>Click "Test Connection" to see detailed results</li>
                <li>Share the results with your developer</li>
            </ol>
        </div>

        <div style="margin-top: 20px; padding: 15px; background: #e6ffe6; border-radius: 5px;">
            <strong>🔍 What this test checks:</strong>
            <ul>
                <li>Basic database connection with your credentials</li>
                <li>MySQL server information and version</li>
                <li>Ability to execute SQL queries</li>
                <li>Existence and record count of all required tables</li>
            </ul>
        </div>
    </div>
</body>
</html>
