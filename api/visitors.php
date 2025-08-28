<?php
// Visitor Management API
// Supports multiple functions: check-mobile, add-visitor, add-visit, get-history, list
require_once __DIR__ . '/db.php';

$conn = db();
$fn = $_GET['fn'] ?? $_POST['fn'] ?? 'list';

// Function: check-mobile
// GET/POST ?fn=check-mobile&mobile=9876543210
if ($fn === 'check-mobile') {
    $mobile = clean_string($_GET['mobile'] ?? $_POST['mobile'] ?? '');
    
    if (empty($mobile)) {
        json_response(['ok' => false, 'error' => 'Mobile number is required'], 400);
    }
    
    $exists = false;
    $found_in = '';
    
    // Check if mobile exists in visitors table
    $stmt = $conn->prepare("SELECT id FROM visitors WHERE mobile = ?");
    $stmt->bind_param("s", $mobile);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $exists = true;
        $found_in = 'visitors';
    }
    $stmt->close();
    
    // If not found in visitors, check volunteers table
    if (!$exists) {
        $stmt = $conn->prepare("SELECT id FROM volunteers WHERE mobile = ?");
        $stmt->bind_param("s", $mobile);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $exists = true;
            $found_in = 'volunteers';
        }
        $stmt->close();
    }
    
    json_response(['exists' => $exists, 'found_in' => $found_in]);

// Function: add-visitor
// POST ?fn=add-visitor
} else if ($fn === 'add-visitor') {
    $name = clean_string($_POST['name'] ?? '');
    $mobile = clean_string($_POST['mobile'] ?? '');
    $email = clean_string($_POST['email'] ?? '');
    $phone = clean_string($_POST['phone'] ?? '');
    $village_id = intval($_POST['village_id'] ?? 0) ?: NULL;
    $city_id = intval($_POST['city_id'] ?? 0) ?: NULL;
    $state_id = intval($_POST['state_id'] ?? 0) ?: NULL;
    $country_id = intval($_POST['country_id'] ?? 0) ?: NULL;
    $occupation_id = intval($_POST['occupation_id'] ?? 0) ?: NULL;
    $seva_interest_id = intval($_POST['seva_interest_id'] ?? 0) ?: NULL;
    $dob = clean_string($_POST['dob'] ?? '') ?: NULL;
    $items_brought = $_POST['items_brought'] ?? [];
    $remarks = clean_string($_POST['remarks'] ?? '');

    if (empty($name) || empty($mobile)) {
        json_response(['ok' => false, 'error' => 'Name and Mobile are required'], 400);
    }

    // Check if mobile already exists
    $stmt = $conn->prepare("SELECT id FROM visitors WHERE mobile = ?");
    $stmt->bind_param("s", $mobile);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        json_response(['ok' => false, 'error' => 'Mobile number already exists'], 409);
    }
    $stmt->close();

    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Insert visitor
        $stmt = $conn->prepare("INSERT INTO visitors 
            (name, mobile, email, phone, village_id, city_id, state_id, country_id, occupation_id, seva_interest_id, dob) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiiiiiis", 
            $name, $mobile, $email, $phone, $village_id, $city_id, $state_id, $country_id, 
            $occupation_id, $seva_interest_id, $dob
        );
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to insert visitor: ' . $stmt->error);
        }
        
        $visitor_id = $stmt->insert_id;
        $stmt->close();
        
        // Insert initial visit record
        $items_json = json_encode(array_filter($items_brought));
        $stmt = $conn->prepare("INSERT INTO visitor_visits (visitor_id, items_brought, remarks) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $visitor_id, $items_json, $remarks);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to insert visit record: ' . $stmt->error);
        }
        
        $visit_id = $stmt->insert_id;
        $stmt->close();
        
        // Commit transaction
        $conn->commit();
        
        json_response(['ok' => true, 'visitor_id' => $visitor_id, 'visit_id' => $visit_id]);
        
    } catch (Exception $e) {
        $conn->rollback();
        json_response(['ok' => false, 'error' => $e->getMessage()], 500);
    }

// Function: add-visit
// POST ?fn=add-visit&mobile=9876543210
} else if ($fn === 'add-visit') {
    $mobile = clean_string($_POST['mobile'] ?? '');
    $items_brought = $_POST['items_brought'] ?? [];
    $remarks = clean_string($_POST['remarks'] ?? '');

    if (empty($mobile)) {
        json_response(['ok' => false, 'error' => 'Mobile number is required'], 400);
    }

    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // First check if visitor exists
        $stmt = $conn->prepare("SELECT id FROM visitors WHERE mobile = ?");
        $stmt->bind_param("s", $mobile);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $visitor_id = null;
        
        if ($result->num_rows > 0) {
            // Visitor exists
            $visitor = $result->fetch_assoc();
            $visitor_id = $visitor['id'];
        } else {
            // Visitor doesn't exist, check if it's a volunteer and create visitor record
            $stmt->close();
            $stmt = $conn->prepare("SELECT id, name, email FROM volunteers WHERE mobile = ? LIMIT 1");
            $stmt->bind_param("s", $mobile);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                // Found in volunteers, create visitor record
                $volunteer = $result->fetch_assoc();
                $stmt->close();
                
                $stmt = $conn->prepare("INSERT INTO visitors (name, mobile, email) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $volunteer['name'], $mobile, $volunteer['email']);
                
                if (!$stmt->execute()) {
                    throw new Exception('Failed to create visitor record: ' . $stmt->error);
                }
                
                $visitor_id = $stmt->insert_id;
            } else {
                throw new Exception('Mobile number not found in any database');
            }
        }
        
        $stmt->close();

        // Insert visit record
        $items_json = json_encode(array_filter($items_brought));
        $stmt = $conn->prepare("INSERT INTO visitor_visits (visitor_id, items_brought, remarks) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $visitor_id, $items_json, $remarks);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to add visit: ' . $stmt->error);
        }
        
        $visit_id = $stmt->insert_id;
        $stmt->close();
        
        // Commit transaction
        $conn->commit();
        
        json_response(['ok' => true, 'visit_id' => $visit_id, 'visitor_id' => $visitor_id]);
        
    } catch (Exception $e) {
        $conn->rollback();
        json_response(['ok' => false, 'error' => $e->getMessage()], 500);
    }

// Function: get-history
// GET ?fn=get-history&mobile=9876543210
} else if ($fn === 'get-history') {
    $mobile = clean_string($_GET['mobile'] ?? '');
    
    if (empty($mobile)) {
        json_response(['ok' => false, 'error' => 'Mobile number is required'], 400);
    }
    
    // Get visitor details with visit history
    // If visitor info is incomplete, also get from volunteers table
    $stmt = $conn->prepare("
        SELECT v.id, v.name, v.mobile, v.email, v.phone, v.dob,
               COALESCE(g.village_name, vg.village_name) as village_name,
               COALESCE(c.city_name, vc.city_name) as city_name, 
               COALESCE(st.state_name, vst.state_name) as state_name,
               COALESCE(co.country_name, vco.country_name) as country_name,
               COALESCE(o.occupation_name, vo.occupation_name) as occupation_name,
               COALESCE(s.seva_name, vs.seva_name) as seva_name,
               COALESCE(v.dob, vol.dob) as dob,
               v.created_at as registered_at
        FROM visitors v
        LEFT JOIN villages g ON v.village_id = g.id
        LEFT JOIN cities c ON v.city_id = c.id
        LEFT JOIN states st ON v.state_id = st.id
        LEFT JOIN countries co ON v.country_id = co.id
        LEFT JOIN occupations o ON v.occupation_id = o.id
        LEFT JOIN seva_interests s ON v.seva_interest_id = s.id
        LEFT JOIN volunteers vol ON v.mobile = vol.mobile
        LEFT JOIN villages vg ON vol.village_id = vg.id
        LEFT JOIN cities vc ON vol.city_id = vc.id
        LEFT JOIN states vst ON vol.state_id = vst.id
        LEFT JOIN countries vco ON vol.country_id = vco.id
        LEFT JOIN occupations vo ON vol.occupation_id = vo.id
        LEFT JOIN seva_interests vs ON vol.seva_interest_id = vs.id
        WHERE v.mobile = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $mobile);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        json_response(['ok' => false, 'error' => 'Visitor not found'], 404);
    }
    
    $visitor = $result->fetch_assoc();
    $stmt->close();
    
    // Get visit history
    $stmt = $conn->prepare("
        SELECT id, items_brought, remarks, 
               DATE_FORMAT(visit_date, '%Y-%m-%d %H:%i:%s') as visit_date
        FROM visitor_visits 
        WHERE visitor_id = ? 
        ORDER BY visit_date DESC
    ");
    $stmt->bind_param("i", $visitor['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $visits = [];
    while ($row = $result->fetch_assoc()) {
        // Decode JSON items
        $row['items_brought'] = json_decode($row['items_brought'] ?? '[]', true);
        $visits[] = $row;
    }
    $stmt->close();
    
    $visitor['visits'] = $visits;
    $visitor['total_visits'] = count($visits);
    
    json_response(['ok' => true, 'visitor' => $visitor]);

// Function: list
// GET ?fn=list
} else if ($fn === 'list') {
    $res = $conn->query("
        SELECT v.id, v.name, v.mobile, v.email, v.phone, v.dob,
               g.village_name, c.city_name, st.state_name, co.country_name,
               o.occupation_name, s.seva_name,
               DATE_FORMAT(v.created_at, '%Y-%m-%d %H:%i') as registered_at,
               COUNT(vv.id) as total_visits,
               MAX(vv.visit_date) as last_visit
        FROM visitors v
        LEFT JOIN villages g ON v.village_id = g.id
        LEFT JOIN cities c ON v.city_id = c.id
        LEFT JOIN states st ON v.state_id = st.id
        LEFT JOIN countries co ON v.country_id = co.id
        LEFT JOIN occupations o ON v.occupation_id = o.id
        LEFT JOIN seva_interests s ON v.seva_interest_id = s.id
        LEFT JOIN visitor_visits vv ON v.id = vv.visitor_id
        GROUP BY v.id
        ORDER BY v.created_at DESC
        LIMIT 500
    ");
    
    $rows = [];
    while ($row = $res->fetch_assoc()) {
        $rows[] = $row;
    }
    json_response($rows);

} else {
    json_response(['error' => 'Unknown function'], 400);
}
?>
