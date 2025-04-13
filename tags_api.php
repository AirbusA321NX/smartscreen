<?php
// Get base64 image from the frontend
$imageBase64 = $_POST['image_base64'] ?? null;

if ($imageBase64) {
    // Prepare API request for Imagga
    $apiKey = 'your_imagga_api_key';  // Replace with your Imagga API Key
    $apiSecret = 'your_imagga_api_secret';  // Replace with your Imagga API Secret
    $url = 'https://api.imagga.com/v2/tags';

    // Prepare data
    $data = http_build_query(['image_base64' => $imageBase64]);

    // Set HTTP context for the API request
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => [
                "Authorization: Basic " . base64_encode("$apiKey:$apiSecret"),
                "Content-Type: application/x-www-form-urlencoded",
                "Content-Length: " . strlen($data)
            ],
            'content' => $data,
            'timeout' => 30
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    // Handle the response
    if ($response !== false) {
        $result = json_decode($response, true);
        if (!empty($result['result']['tags'])) {
            $tags = array_map(function ($tag) {
                return $tag['tag']['en'];
            }, $result['result']['tags']);
        } else {
            $tags = [];
        }
    } else {
        $tags = [];
    }

    // Return the tags as JSON
    echo json_encode(['tags' => $tags]);
} else {
    echo json_encode(['tags' => []]);
}
?>