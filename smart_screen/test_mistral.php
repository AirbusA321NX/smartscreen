<?php
// Mistral API Configuration
$mistral_api_key = 'ZLnOREIyt43T1iNRn05frDwX97RiT3va';
$mistral_url = 'https://api.mistral.ai/v1/chat/completions';

// Request payload
$data = [
    "model" => "mistral-small-latest",  // Updated model name
    "messages" => [
        [
            "role" => "user",
            "content" => 'Provide an analysis of the following image description: A bird flying in the sky.'
        ]
    ],
    "temperature" => 0.7  // Added recommended parameter
];

// Create context
$options = [
    'http' => [
        'method' => 'POST',
        'header' => implode("\r\n", [
            "Content-Type: application/json",
            "Authorization: Bearer $mistral_api_key",
            "Accept: application/json"
        ]),
        'content' => json_encode($data),
        'ignore_errors' => true  // Capture 4xx/5xx responses
    ]
];

$context = stream_context_create($options);

try {
    $response = file_get_contents($mistral_url, false, $context);

    if ($response === false) {
        throw new Exception("HTTP request failed");
    }

    $response_data = json_decode($response, true);

    if (isset($response_data['choices'][0]['message']['content'])) {
        echo "Analysis: " . $response_data['choices'][0]['message']['content'];
    } else {
        echo "Error in API response: " . print_r($response_data, true);
    }

} catch (Exception $e) {
    // Get HTTP status code
    preg_match('{HTTP\/\d\.\d\s(\d{3})}', $http_response_header[0], $matches);
    $status_code = $matches[1] ?? 'Unknown';

    echo "API Request Failed (Status: $status_code)\n";
    echo "Error: " . $e->getMessage();

    // For debugging:
    echo "\nRequest Headers:\n" . print_r($options['http']['header'], true);
    echo "\nRequest Body:\n" . json_encode($data, JSON_PRETTY_PRINT);
}
?>