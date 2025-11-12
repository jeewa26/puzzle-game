<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Get difficulty parameter (for guest mode, always use easy)
$difficulty = $_GET['difficulty'] ?? 'easy';

// Fetch puzzle from external API
$url = "https://marcconrad.com/uob/banana/api.php";
$response = false;

// Try to fetch using file_get_contents first
if (ini_get('allow_url_fopen')) {
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'method' => 'GET'
        ]
    ]);
    $response = @file_get_contents($url, false, $context);
}

// Fallback to cURL if file_get_contents fails or is disabled
if ($response === false && function_exists('curl_init')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        $response = false;
    }
}

if ($response === false) {
    http_response_code(500);
    echo json_encode([
        "error" => "Failed to fetch puzzle from API. Please check your server configuration.",
        "image" => null,
        "answer" => null
    ]);
    exit();
}

// Parse the JSON response
$data = json_decode($response, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    echo json_encode([
        "error" => "Failed to parse API response",
        "image" => null,
        "answer" => null
    ]);
    exit();
}

if ($data && isset($data['question']) && isset($data['solution'])) {
    // Map the API response to match what guest_game.php expects
    echo json_encode([
        "image" => $data['question'],
        "answer" => (int)$data['solution']
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "error" => "Invalid API response format",
        "image" => null,
        "answer" => null
    ]);
}
?>

