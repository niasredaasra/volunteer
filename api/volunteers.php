<?php
// POST ?fn=add  (save volunteer)
// GET  ?fn=list (list volunteers)
require_once __DIR__ . '/db.php';
$conn = db();
$fn = $_GET['fn'] ?? $_POST['fn'] ?? 'list';

if ($fn === 'add') {
    $name = clean_string($_POST['name'] ?? '');
    $village_id = intval($_POST['village_id'] ?? 0) ?: NULL;
    $city_id = intval($_POST['city_id'] ?? 0) ?: NULL;
    $state_id = intval($_POST['state_id'] ?? 0) ?: NULL;
    $country_id = intval($_POST['country_id'] ?? 0) ?: NULL;
    $mobile = clean_string($_POST['mobile'] ?? '');
    $email = clean_string($_POST['email'] ?? '');
    $phone = clean_string($_POST['phone'] ?? '');
    $occupation_id = intval($_POST['occupation_id'] ?? 0) ?: NULL;
    $seva_interest_id = intval($_POST['seva_interest_id'] ?? 0) ?: NULL;
    $dob = clean_string($_POST['dob'] ?? '') ?: NULL;
    $remarks = clean_string($_POST['remarks'] ?? '');
    
    // Handle items_brought array
    $items_brought = '';
    if (isset($_POST['items_brought']) && is_array($_POST['items_brought'])) {
        $items_brought = json_encode(array_filter($_POST['items_brought']));
    }

    if ($name === '' || $mobile === '') {
        json_response(['ok' => false, 'error' => 'Name and Mobile are required'], 400);
    }

    // Check for duplicate phone number if phone is provided
    if (!empty($phone)) {
        $check_stmt = $conn->prepare("SELECT id FROM volunteers WHERE phone = ?");
        $check_stmt->bind_param("s", $phone);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        if ($result->num_rows > 0) {
            $check_stmt->close();
            json_response(['ok' => false, 'error' => 'This phone number is already registered. Please use a different phone number.'], 400);
        }
        $check_stmt->close();
    }

    // Begin transaction to ensure data consistency
    $conn->begin_transaction();
    
    try {
        // Insert volunteer record
        $stmt = $conn->prepare("INSERT INTO volunteers 
            (name, village_id, city_id, state_id, country_id, mobile, email, phone, occupation_id, seva_interest_id, dob, remarks, items_brought) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siiiiissiisss", 
            $name, $village_id, $city_id, $state_id, $country_id, 
            $mobile, $email, $phone, $occupation_id, $seva_interest_id, $dob, $remarks, $items_brought
        );
        
        if (!$stmt->execute()) {
            // Check if error is due to duplicate key constraint
            if (strpos($stmt->error, 'unique_phone') !== false || strpos($stmt->error, 'Duplicate entry') !== false) {
                throw new Exception('This phone number is already registered. Please use a different phone number.');
            }
            throw new Exception('Failed to insert volunteer: ' . $stmt->error);
        }
        
        $volunteer_id = $stmt->insert_id;
        $stmt->close();
        
        // Create corresponding visitor record
        $stmt = $conn->prepare("INSERT INTO visitors 
            (name, mobile, email, phone, village_id, city_id, state_id, country_id, occupation_id, seva_interest_id, dob) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiiiiiis", 
            $name, $mobile, $email, $phone, $village_id, $city_id, $state_id, $country_id, 
            $occupation_id, $seva_interest_id, $dob
        );
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create visitor record: ' . $stmt->error);
        }
        
        $visitor_id = $stmt->insert_id;
        $stmt->close();
        
        // Create initial visit record (since they're physically present during registration)
        $stmt = $conn->prepare("INSERT INTO visitor_visits (visitor_id, items_brought, remarks) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $visitor_id, $items_brought, $remarks);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create visit record: ' . $stmt->error);
        }
        
        $visit_id = $stmt->insert_id;
        $stmt->close();
        
        // Commit transaction
        $conn->commit();
        
        json_response([
            'ok' => true, 
            'id' => $volunteer_id, 
            'visitor_id' => $visitor_id, 
            'visit_id' => $visit_id,
            'message' => 'Volunteer registered and visit recorded successfully!'
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        json_response(['ok' => false, 'error' => $e->getMessage()], 500);
    }

} else if ($fn === 'list') {
    $res = $conn->query("
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
        LIMIT 500
    ");
    $rows = [];
    while ($row = $res->fetch_assoc()) {
        $rows[] = $row;
    }
    json_response($rows);

} else {
    json_response(['error' => 'Unknown fn'], 400);
}
