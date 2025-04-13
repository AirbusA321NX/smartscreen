<?php
$response = file_get_contents("https://www.google.com");
if ($response === false) {
    echo "❌ HTTPS requests are not working in PHP!";
} else {
    echo "✅ HTTPS requests are working!";
}
?>
