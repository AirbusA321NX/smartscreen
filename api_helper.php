<?php

function get_image_tags($base64_image)
{
    $api_key = 'acc_7a0f52e2d42c434';
    $api_secret = '8ffcac02a14c52c0776e9d7c859ef236';
    $url = 'https://api.imagga.com/v2/tags?image_base64=' . urlencode($base64_image);

    $headers = ["Authorization: Basic " . base64_encode("$api_key:$api_secret")];
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => implode("\r\n", $headers),
            'timeout' => 20
        ],
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
    ]);

    $response = file_get_contents($url, false, $context);
    if (!$response)
        return "No tags found";

    $data = json_decode($response, true);
    if (!empty($data['result']['tags'])) {
        $tags = array_slice($data['result']['tags'], 0, 5);
        $tag_names = array_column(array_column($tags, 'tag'), 'en');
        return implode(', ', $tag_names);
    }

    return "No tags found";
}

function get_ocr_text($base64_image)
{
    $ocr_api_key = 'K88801117788957';
    $url = 'https://api.ocr.space/parse/image';

    $payload = http_build_query([
        'base64Image' => 'data:image/jpeg;base64,' . $base64_image,
        'language' => 'eng',
        'isOverlayRequired' => false
    ]);

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "apikey: $ocr_api_key\r\nContent-Type: application/x-www-form-urlencoded",
            'content' => $payload,
            'timeout' => 30
        ]
    ]);

    $response = file_get_contents($url, false, $context);
    if (!$response)
        return "OCR processing failed or no text detected.";

    $json = json_decode($response, true);
    return $json['ParsedResults'][0]['ParsedText'] ?? "No OCR text";
}

function get_mistral_analysis($command, $tags, $ocr_text)
{
    $api_key = 'JCDn10oH2EaeYPMsXDctjFa5fdqoPPhe';
    $url = 'https://api.mistral.ai/v1/chat/completions';

    $prompt = "Analyze this command: '$command' using these image tags: $tags and extracted OCR text: $ocr_text. Provide detailed analysis.";
    $payload = json_encode([
        "model" => "mistral-small-latest",
        "messages" => [
            ["role" => "user", "content" => $prompt]
        ],
        "temperature" => 0.7,
        "max_tokens" => 1000
    ]);

    $headers = [
        "Content-Type: application/json",
        "Authorization: Bearer $api_key"
    ];

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => $payload,
            'timeout' => 30
        ],
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
    ]);

    $response = file_get_contents($url, false, $context);
    if (!$response)
        return "Mistral API analysis failed";

    $json = json_decode($response, true);
    return $json['choices'][0]['message']['content'] ?? "Mistral analysis missing";
}
?>