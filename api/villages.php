<?php
require_once __DIR__ . '/db.php';

try {
    $conn = db();
    $fn = $_GET['fn'] ?? $_POST['fn'] ?? 'list';

    if ($fn === 'list') {
        // Check if table exists
        $tableCheck = $conn->query("SHOW TABLES LIKE 'villages'");
        if (!$tableCheck || $tableCheck->num_rows === 0) {
            json_response(['error' => 'Villages table does not exist'], 500);
        }
        
        $res = $conn->query("SELECT id, village_name FROM villages ORDER BY village_name");
        if (!$res) {
            json_response(['error' => 'Database query failed: ' . $conn->error], 500);
        }
        
        $rows = [];
        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }
        
        if (empty($rows)) {
            json_response(['warning' => 'No villages found in database', 'data' => []]);
        }
        
        json_response($rows);
        
    } else if ($fn === 'add') {
        $name = trim($_POST['village_name'] ?? '');
        if ($name === '') {
            json_response(['error' => 'Village name required'], 400);
        }
        
        // Check if table exists
        $tableCheck = $conn->query("SHOW TABLES LIKE 'villages'");
        if (!$tableCheck || $tableCheck->num_rows === 0) {
            json_response(['error' => 'Villages table does not exist'], 500);
        }
        
        $stmt = $conn->prepare("INSERT INTO villages (village_name) VALUES (?)");
        if (!$stmt) {
            json_response(['error' => 'Prepare failed: ' . $conn->error], 500);
        }
        
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            json_response(['ok' => true, 'id' => $stmt->insert_id, 'village_name' => $name]);
        } else {
            json_response(['error' => 'Insert failed: ' . $stmt->error], 500);
        }
        $stmt->close();
        
    } else {
        json_response(['error' => 'Unknown function: ' . $fn], 400);
    }
    
} catch (Exception $e) {
    json_response(['error' => 'Server error: ' . $e->getMessage()], 500);
}
