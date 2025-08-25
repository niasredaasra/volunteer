<?php
// Endpoints:
// GET  ?fn=list
// POST ?fn=add   body: seva_name
require_once __DIR__ . '/db.php';

try {
    $conn = db();
    $fn = $_GET['fn'] ?? $_POST['fn'] ?? 'list';

    if ($fn === 'list') {
        // Check if table exists
        $tableCheck = $conn->query("SHOW TABLES LIKE 'seva_interests'");
        if (!$tableCheck || $tableCheck->num_rows === 0) {
            json_response(['error' => 'Seva interests table does not exist'], 500);
        }
        
        $res = $conn->query("SELECT id, seva_name FROM seva_interests ORDER BY seva_name");
        if (!$res) {
            json_response(['error' => 'Database query failed: ' . $conn->error], 500);
        }
        
        $out = [];
        while ($row = $res->fetch_assoc()) {
            $out[] = $row;
        }
        
        if (empty($out)) {
            json_response(['warning' => 'No seva interests found in database', 'data' => []]);
        }
        
        json_response($out);
        
    } elseif ($fn === 'add') {
        $name = clean_string($_POST['seva_name'] ?? '');
        if ($name === '') {
            json_response(['error' => 'Seva name required'], 400);
        }

        // Try find existing
        $stmt = $conn->prepare("SELECT id FROM seva_interests WHERE seva_name = ?");
        if (!$stmt) {
            json_response(['error' => 'Prepare failed: ' . $conn->error], 500);
        }
        
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->bind_result($existing_id);
        if ($stmt->fetch()) {
            $stmt->close();
            json_response(['ok' => true, 'id' => $existing_id, 'seva_name' => $name]);
        }
        $stmt->close();

        // Insert new
        $stmt = $conn->prepare("INSERT INTO seva_interests (seva_name) VALUES (?)");
        if (!$stmt) {
            json_response(['error' => 'Prepare failed: ' . $conn->error], 500);
        }
        
        $stmt->bind_param("s", $name);
        if (!$stmt->execute()) {
            json_response(['error' => 'Insert failed: ' . $stmt->error], 500);
        }
        $id = $stmt->insert_id;
        $stmt->close();
        json_response(['ok' => true, 'id' => $id, 'seva_name' => $name]);
        
    } else {
        json_response(['error' => 'Unknown function: ' . $fn], 400);
    }
    
} catch (Exception $e) {
    json_response(['error' => 'Server error: ' . $e->getMessage()], 500);
}
