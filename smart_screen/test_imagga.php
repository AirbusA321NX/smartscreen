<?php
// Replace with your API credentials
$api_key = 'acc_7a0f52e2d42c434';
$api_secret = '8ffcac02a14c52c0776e9d7c859ef236';

// Load image and encode in base64
$image_path = 'ocr_test.jpg'; // Make sure this image exists
$image_data = file_get_contents($image_path);
$base64_image = base64_encode($image_data);

// Prepare the payload
$data = http_build_query([
    'image_base64' => $base64_image
]);

// Create the context for POST request
$options = [
    'http' => [
        'method' => 'POST',
        'header' => [
            "Authorization: Basic " . base64_encode("$api_key:$api_secret"),
            "Content-Type: application/x-www-form-urlencoded",
            "Content-Length: " . strlen($data)
        ],
        'content' => $data,
        'timeout' => 30
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false
    ]
];

$url = "https://api.imagga.com/v2/tags";
$response = file_get_contents($url, false, stream_context_create($options));

// Parse the response
if ($response !== false) {
    $result = json_decode($response, true);
    if (!empty($result['result']['tags'])) {
        echo "Top tags:\n";
        foreach (array_slice($result['result']['tags'], 0, 5) as $tag) {
            echo "- " . $tag['tag']['en'] . "\n";
        }
    } else {
        echo "No tags found.\n";
    }
} else {
    echo "Failed to call Imagga API.\n";
}
?>