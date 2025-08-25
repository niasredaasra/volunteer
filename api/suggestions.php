<?php
// GET ?field=village|city|state|country -> ["A","B","C"]
require_once __DIR__ . '/db.php';
$conn = db();

$allowed = ['village','city','state','country'];
$field = $_GET['field'] ?? '';
if (!in_array($field, $allowed, true)) {
    json_response(['error' => 'Invalid field'], 400);
}

$sql = "SELECT DISTINCT " . $field . " AS val FROM volunteers WHERE " . $field . " IS NOT NULL AND " . $field . " <> '' ORDER BY " . $field;
$res = $conn->query($sql);
$out = [];
while ($row = $res->fetch_assoc()) {
    $out[] = $row['val'];
}
json_response($out);
