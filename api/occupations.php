<?php
// Endpoints:
// GET  ?fn=list
// POST ?fn=add   body: occupation_name
require_once __DIR__ . '/db.php';

try {
    $conn = db();
    $fn = $_GET['fn'] ?? $_POST['fn'] ?? 'list';

    if ($fn === 'list') {
        // Check if table exists
        $tableCheck = $conn->query("SHOW TABLES LIKE 'occupations'");
        if (!$tableCheck || $tableCheck->num_rows === 0) {
            json_response(['error' => 'Occupations table does not exist'], 500);
        }
        
        $res = $conn->query("SELECT id, occupation_name FROM occupations ORDER BY occupation_name");
        if (!$res) {
            json_response(['error' => 'Database query failed: ' . $conn->error], 500);
        }
        
        $out = [];
        while ($row = $res->fetch_assoc()) {
            $out[] = $row;
        }
        
        if (empty($out)) {
            json_response(['warning' => 'No occupations found in database', 'data' => []]);
        }
        
        json_response($out);
        
    } elseif ($fn === 'add') {
        $name = clean_string($_POST['occupation_name'] ?? '');
        if ($name === '') {
            json_response(['error' => 'Occupation name required'], 400);
        }

        // Try find existing
        $stmt = $conn->prepare("SELECT id FROM occupations WHERE occupation_name = ?");
        if (!$stmt) {
            json_response(['error' => 'Prepare failed: ' . $conn->error], 500);
        }
        
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->bind_result($existing_id);
        if ($stmt->fetch()) {
            $stmt->close();
            json_response(['ok' => true, 'id' => $existing_id, 'occupation_name' => $name]);
        }
        $stmt->close();

        // Insert new
        $stmt = $conn->prepare("INSERT INTO occupations (occupation_name) VALUES (?)");
        if (!$stmt) {
            json_response(['error' => 'Prepare failed: ' . $conn->error], 500);
        }
        
        $stmt->bind_param("s", $name);
        if (!$stmt->execute()) {
            json_response(['error' => 'Insert failed: ' . $stmt->error], 500);
        }
        $id = $stmt->insert_id;
        $stmt->close();
        json_response(['ok' => true, 'id' => $id, 'occupation_name' => $name]);
        
    } else {
        json_response(['error' => 'Unknown function: ' . $fn], 400);
    }
    
} catch (Exception $e) {
    json_response(['error' => 'Server error: ' . $e->getMessage()], 500);
}
