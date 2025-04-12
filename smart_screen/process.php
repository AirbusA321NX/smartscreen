<?php
ob_start();
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');
error_reporting(E_ALL);

header('Content-Type: application/json');
include('config.php');

// Validate request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid Request"]);
    exit;
}

if (!isset($_FILES['screenshot'])) {
    echo json_encode(["error" => "No screenshot received"]);
    exit;
}

// Process image
try {
    $imageData = file_get_contents($_FILES['screenshot']['tmp_name']);
    if ($imageData === false)
        throw new Exception("Failed to read screenshot");

    $base64_image = base64_encode($imageData);
    $command = $_POST['command'] ?? "";

    // Save image locally
    if (!is_dir("images"))
        mkdir("images", 0755, true);
    $image_name = "screenshot_" . time() . ".jpg";
    $image_path = "images/" . $image_name;
    file_put_contents($image_path, $imageData);

    // === Imagga API Call ===
    $imagga_api_key = 'acc_7a0f52e2d42c434';
    $imagga_api_secret = '8ffcac02a14c52c0776e9d7c859ef236';
    $imagga_url = 'https://api.imagga.com/v2/tags';

    $imagga_response = file_get_contents("$imagga_url?image_base64=" . urlencode($base64_image), false, stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "Authorization: Basic " . base64_encode("$imagga_api_key:$imagga_api_secret")
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ]
    ]));

    $tags_output = "No tags found";
    if ($imagga_response !== false) {
        $json_imagga = json_decode($imagga_response, true);
        if (!empty($json_imagga['result']['tags'])) {
            $tags = array_slice($json_imagga['result']['tags'], 0, 5);
            $tag_names = array_column(array_column($tags, 'tag'), 'en');
            $tags_output = implode(', ', $tag_names);
        }
    }

    // === Mistral API Call ===
    $mistral_api_key = 'JCDn10oH2EaeYPMsXDctjFa5fdqoPPhe';
    $mistral_url = 'https://api.mistral.ai/v1/chat/completions';
    $mistral_prompt = "Analyze this command: '$command' using these image tags: $tags_output. Provide detailed analysis.";

    $mistral_payload = json_encode([
        "model" => "mistral-small-latest",
        "messages" => [
            ["role" => "user", "content" => $mistral_prompt]
        ],
        "temperature" => 0.7,
        "max_tokens" => 1000
    ]);

    $mistral_response = file_get_contents($mistral_url, false, stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", [
                "Content-Type: application/json",
                "Authorization: Bearer $mistral_api_key",
                "Content-Length: " . strlen($mistral_payload)
            ]),
            'content' => $mistral_payload,
            'timeout' => 30
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ]
    ]));

    $mistral_analysis = "API analysis failed";
    if ($mistral_response !== false) {
        $json_mistral = json_decode($mistral_response, true);
        if (!empty($json_mistral['choices'][0]['message']['content'])) {
            $mistral_analysis = trim($json_mistral['choices'][0]['message']['content']);
        }
    }

    // Save to database
    $final_analysis = "Tags: $tags_output | Analysis: $mistral_analysis";
    if (isset($conn)) {
        $stmt = $conn->prepare("INSERT INTO results (command, analysis, screenshot_path) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sss", $command, $final_analysis, $image_path);
            $stmt->execute();
            $stmt->close();
        }
    }

    $response = [
        "analysis" => $final_analysis,
        "screenshot" => "data:image/jpeg;base64," . base64_encode($imageData)
    ];

} catch (Exception $e) {
    $response = ["error" => $e->getMessage()];
}

ob_end_clean();
echo json_encode($response);
exit;
?>