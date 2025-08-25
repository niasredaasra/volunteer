<?php
// POST: message, video_url (optional), volunteer_ids = "all" or JSON array of IDs
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../config.php';
$conn = db();

$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?: $_POST;

$message = trim($data['message'] ?? '');
$video_url = trim($data['video_url'] ?? '');
$ids = $data['volunteer_ids'] ?? 'all';

if ($message === '' && $video_url === '') {
    json_response(['ok' => false, 'error' => 'Provide message or video_url'], 400);
}

// Resolve recipient list
if ($ids === 'all') {
    $res = $conn->query("SELECT id, name, mobile FROM volunteers WHERE mobile IS NOT NULL AND mobile <> '' ORDER BY id DESC LIMIT 1000");
    $recipients = [];
    while ($row = $res->fetch_assoc()) {
        $recipients[] = $row;
    }
} else if (is_array($ids)) {
    $in = implode(',', array_map('intval', $ids));
    if ($in === '') json_response(['ok' => false, 'error' => 'Empty recipients'], 400);
    $res = $conn->query("SELECT id, name, mobile FROM volunteers WHERE id IN ($in) AND mobile IS NOT NULL AND mobile <> ''");
    $recipients = [];
    while ($row = $res->fetch_assoc()) {
        $recipients[] = $row;
    }
} else {
    json_response(['ok' => false, 'error' => 'Invalid volunteer_ids'], 400);
}

$results = [];
$success_count = 0;
$error_count = 0;

foreach ($recipients as $r) {
    $mobile = $r['mobile'];
    $formatted_number = formatPhoneNumber($mobile);
    
    if ($formatted_number === false) {
        $results[] = [
            'id' => $r['id'], 
            'mobile' => $mobile, 
            'formatted' => 'INVALID',
            'ok' => false, 
            'error' => 'Invalid phone number format'
        ];
        $error_count++;
        continue;
    }
    
    $send_result = send_whatsapp($formatted_number, $message, $video_url, $r['name']);
    
    if ($send_result['ok']) {
        $success_count++;
    } else {
        $error_count++;
    }
    
    $results[] = [
        'id' => $r['id'], 
        'mobile' => $mobile, 
        'formatted' => $formatted_number,
        'ok' => $send_result['ok'], 
        'error' => $send_result['error'] ?? null,
        'api_response' => $send_result['api_response'] ?? null
    ];
}

json_response([
    'ok' => true, 
    'total' => count($results),
    'success' => $success_count,
    'errors' => $error_count,
    'results' => $results
]);

function formatPhoneNumber($mobile) {
    // Remove all non-digit characters
    $clean = preg_replace('/\D+/', '', $mobile);
    
    // Check if it's a valid Indian mobile number
    if (strlen($clean) === 10 && (substr($clean, 0, 1) === '6' || substr($clean, 0, 1) === '7' || substr($clean, 0, 1) === '8' || substr($clean, 0, 1) === '9')) {
        return '91' . $clean; // Add India country code
    }
    
    // Check if it already has country code
    if (strlen($clean) === 12 && substr($clean, 0, 2) === '91') {
        return $clean;
    }
    
    // Check if it's 11 digits with 0 prefix
    if (strlen($clean) === 11 && substr($clean, 0, 1) === '0') {
        return '91' . substr($clean, 1);
    }
    
    return false; // Invalid format
}

function send_whatsapp($to, $text, $video_url, $recipient_name = '') {
    $url = "https://graph.facebook.com/" . WA_API_VERSION . "/" . WA_PHONE_NUMBER_ID . "/messages";
    $headers = [
        "Authorization: Bearer " . WA_TOKEN,
        "Content-Type: application/json"
    ];

    $results = [];
    $ok_any = false;

    // Send text message if provided
    if ($text !== '') {
        // Replace {{name}} placeholder with actual recipient name
        $personalized_message = str_replace('{{name}}', $recipient_name, $text);
        
        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $to,
            "type" => "text",
            "text" => ["body" => $personalized_message]
        ];
        
        $text_result = call_api($url, $headers, $payload, 'text');
        $results['text'] = $text_result;
        $ok_any = $text_result['ok'] || $ok_any;
    }

    // Send video if provided
    if ($video_url !== '') {
        $payload = [
            "messaging_product" => "whatsapp",
            "to" => $to,
            "type" => "video",
            "video" => ["link" => $video_url]
        ];
        
        $video_result = call_api($url, $headers, $payload, 'video');
        $results['video'] = $video_result;
        $ok_any = $video_result['ok'] || $ok_any;
    }

    return [
        'ok' => $ok_any,
        'results' => $results,
        'error' => $ok_any ? null : 'All message attempts failed'
    ];
}

function call_api($url, $headers, $payload, $type = 'unknown') {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    // Log the API call for debugging (optional - remove in production)
    error_log("WhatsApp API $type: HTTP $httpcode - " . ($httpcode >= 200 && $httpcode < 300 ? 'SUCCESS' : 'FAILED'));

    if ($httpcode >= 200 && $httpcode < 300) {
        $response_data = json_decode($response, true);
        return [
            'ok' => true,
            'http_code' => $httpcode,
            'api_response' => $response_data,
            'message_id' => $response_data['messages'][0]['id'] ?? null
        ];
    } else {
        $response_data = json_decode($response, true);
        return [
            'ok' => false,
            'http_code' => $httpcode,
            'error' => $response_data['error']['message'] ?? 'HTTP Error: ' . $httpcode,
            'api_response' => $response_data,
            'curl_error' => $err
        ];
    }
}
