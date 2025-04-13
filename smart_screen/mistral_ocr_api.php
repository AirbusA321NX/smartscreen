<?php
$image_url = "https://raw.githubusercontent.com/mistralai/cookbook/refs/heads/main/mistral/ocr/receipt.png"; // Replace with your actual image URL

$payload = json_encode([
    "model" => "mistral-ocr-latest", // Replace with actual model name if different
    "document" => [
        "type" => "image_url", // Make sure to pass the URL type
        "imageUrl" => $image_url
    ]
]);

$headers = [
    "Content-Type: application/json",
    "Authorization: Bearer JCDn10oH2EaeYPMsXDctjFa5fdqoPPhe" // Replace with your actual Mistral API key
];

// Set up the context with HTTP request
$options = [
    'http' => [
        'method' => 'POST',
        'header' => implode("\r\n", $headers),
        'content' => $payload,
        'ignore_errors' => true // Allow non-2xx responses to be returned as body
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false
    ]
];

$context = stream_context_create($options);

// Get the response from Mistral API
$response = file_get_contents("https://api.mistral.ai/v1/ocr/process", false, $context);

// Get the response code to see if it's an issue with the request
$status_code = $http_response_header[0];

// Debugging output
echo "<pre>";
echo "Image: <img src='$image_url' width='300'><br><br>";
echo "HTTP Response Status Code: $status_code<br><br>";

if ($response === FALSE) {
    echo "Error: Failed to get a response from the API.<br>";
} else {
    echo "Raw OCR Response: \n";
    print_r(json_decode($response, true));
}
echo "</pre>";
?>