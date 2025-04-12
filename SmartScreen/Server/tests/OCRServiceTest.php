<?php
require_once __DIR__ . '/../core/OCRService.php';

try {
    $text = OCRService::extractText(__DIR__ . '/test.png'); // Ensure test.png exists here
    echo "✅ OCR Success:\n" . $text;
} catch(Exception $e) {
    echo "❌ OCR Failed: " . $e->getMessage();
}
?>
