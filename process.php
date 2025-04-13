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

try {
    // Read screenshot image
    $imageData = file_get_contents($_FILES['screenshot']['tmp_name']);
    if ($imageData === false)
        throw new Exception("Failed to read screenshot");

    $command = $_POST['command'] ?? "";

    // Save image locally
    if (!is_dir("images"))
        mkdir("images", 0755, true);
    $image_name = "screenshot_" . time() . ".jpg";
    $image_path = "images/" . $image_name;
    file_put_contents($image_path, $imageData);

    // === Call Python script ===
    $escaped_cmd = escapeshellarg($command);
    $escaped_img = escapeshellarg($image_path);
    $python_output = shell_exec("python analyze.py $escaped_cmd $escaped_img");

    $response_data = json_decode($python_output, true);

    if (!is_array($response_data) || !isset($response_data['analysis'])) {
        throw new Exception("Invalid response from Python script");
    }

    $final_analysis = $response_data['analysis'];

    // Save to database
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