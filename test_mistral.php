<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration for the Mistral API
$mistral_api_key = 'JCDn10oH2EaeYPMsXDctjFa5fdqoPPhe';
$mistral_url = 'https://api.mistral.ai/v1/chat/completions';

// Test command and prompt
$command = "Test the Mistral API with a dummy command.";
$mistral_prompt = "Analyze the following command: '$command'. Provide detailed analysis.";

// Build the JSON payload
$mistral_payload = json_encode([
    "model" => "mistral-small-latest",
    "messages" => [
        ["role" => "user", "content" => $mistral_prompt]
    ],
    "temperature" => 0.7,
    "max_tokens" => 1000
]);

// Prepare HTTP headers for the POST request
$mistral_headers =
    "Content-Type: application/json\r\n" .
    "Authorization: Bearer $mistral_api_key\r\n" .
    "Content-Length: " . strlen($mistral_payload) . "\r\n";

// Create a stream context with ignore_errors to capture any error responses
$mistral_context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => $mistral_headers,
        'content' => $mistral_payload,
        'timeout' => 30,
        'ignore_errors' => true,
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ]
]);

// Make the API call
$response = file_get_contents($mistral_url, false, $mistral_context);

// Display the response
echo "Mistral API Response:\n";
if ($response === false) {
    echo "Mistral API call failed. Error details:\n";
    print_r(error_get_last());
} else {
    echo $response;
}
?>