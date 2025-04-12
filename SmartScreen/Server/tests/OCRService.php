<?php
class OCRService {
    public static function extractText($imagePath) {
        if (!file_exists($imagePath)) {
            throw new Exception("Image not found at path: $imagePath");
        }

        // Make sure tesseract is installed and in PATH
        $command = escapeshellcmd("tesseract " . escapeshellarg($imagePath) . " stdout");
        $output = shell_exec($command);

        if ($output === null) {
            throw new Exception("Tesseract OCR failed to run.");
        }

        return trim($output);
    }
}
?>
