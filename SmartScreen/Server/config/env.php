<?php
// Correct path with proper parenthesis
$envPath = realpath(dirname(__FILE__) . '/../../.env');

if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) continue;
        
        // Split key=value pairs
        list($name, $value) = explode('=', $line, 2);
        putenv("$name=$value");
    }
}
?>