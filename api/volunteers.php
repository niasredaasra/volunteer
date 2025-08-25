<?php
// POST ?fn=add  (save volunteer)
// GET  ?fn=list (list volunteers)
require_once __DIR__ . '/db.php';
$conn = db();
$fn = $_GET['fn'] ?? $_POST['fn'] ?? 'list';

if ($fn === 'add') {
    $name = clean_string($_POST['name'] ?? '');
    $village_id = intval($_POST['village_id'] ?? 0) ?: NULL;  // ✅ foreign key
    $city = clean_string($_POST['city'] ?? '');
    $state = clean_string($_POST['state'] ?? '');
    $country = clean_string($_POST['country'] ?? 'India');
    $mobile = clean_string($_POST['mobile'] ?? '');
    $email = clean_string($_POST['email'] ?? '');
    $phone = clean_string($_POST['phone'] ?? '');
    $occupation_id = intval($_POST['occupation_id'] ?? 0) ?: NULL;
    $seva_interest_id = intval($_POST['seva_interest_id'] ?? 0) ?: NULL;
    $dob = clean_string($_POST['dob'] ?? '');
    $remarks = clean_string($_POST['remarks'] ?? '');

    if ($name === '' || $mobile === '') {
        json_response(['ok' => false, 'error' => 'Name and Mobile are required'], 400);
    }

    // Normalize foreign keys as null if 0
    $vill = ($village_id && $village_id > 0) ? $village_id : NULL;
    $occ = ($occupation_id && $occupation_id > 0) ? $occupation_id : NULL;
    $seva = ($seva_interest_id && $seva_interest_id > 0) ? $seva_interest_id : NULL;

    // ✅ insert with village_id as foreign key
    $stmt = $conn->prepare("INSERT INTO volunteers 
        (name, village_id, city, state, country, mobile, email, phone, occupation_id, seva_interest_id, dob, remarks) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissssssisss", 
        $name, $vill, $city, $state, $country, 
        $mobile, $email, $phone, $occ, $seva, $dob, $remarks
    );
    if (!$stmt->execute()) {
        json_response(['ok' => false, 'error' => 'Insert failed', 'details' => $stmt->error], 500);
    }
    $id = $stmt->insert_id;
    $stmt->close();
    json_response(['ok' => true, 'id' => $id]);

} else if ($fn === 'list') {
    $res = $conn->query("
        SELECT v.id, v.name, v.mobile, v.email, v.city, v.state, v.country,
               v.phone, v.dob, v.remarks,
               o.occupation_name, s.seva_name, g.village_name,
               DATE_FORMAT(v.created_at, '%Y-%m-%d %H:%i') AS created_at
        FROM volunteers v
        LEFT JOIN occupations o ON v.occupation_id = o.id
        LEFT JOIN seva_interests s ON v.seva_interest_id = s.id
        LEFT JOIN villages g ON v.village_id = g.id   -- ✅ join with villages table
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
