<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');
error_reporting(E_ALL);

// Your Imagga API credentials
$api_key = 'acc_7a0f52e2d42c434';
$api_secret = '8ffcac02a14c52c0776e9d7c859ef236';

// Path to the image you want to test
$image_path = 'ocr_test.jpg'; // Ensure this image exists in your directory

// Read the image and encode it in base64
$image_data = file_get_contents($image_path);
if ($image_data === false) {
    die("Failed to read the image");
}
$base64_image = base64_encode($image_data);

// Prepare the POST request data
$data = http_build_query([
    'image_base64' => $base64_image
]);

// Set up the HTTP context for the POST request
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

// Imagga API URL for tagging
$url = "https://api.imagga.com/v2/tags";

// Perform the API request
$response = file_get_contents($url, false, stream_context_create($options));

// Check for a valid response
if ($response === false) {
    die("Failed to call Imagga API");
}

// Decode the response JSON
$result = json_decode($response, true);
if (isset($result['result']['tags'])) {
    echo "Top tags:\n";
    foreach (array_slice($result['result']['tags'], 0, 5) as $tag) {
        echo "- " . $tag['tag']['en'] . "\n";
    }
} else {
    echo "No tags found.\n";
}
?>