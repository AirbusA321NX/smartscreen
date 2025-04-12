<?php
class FileHandler {
    public static function saveImage($base64Data) {
        $storagePath = getenv('FILE_STORAGE_PATH');
        
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $data = explode(',', $base64Data);
        $imageData = base64_decode($data[1]);
        $fileName = uniqid('img_') . '.png';
        $filePath = $storagePath . '/' . $fileName;

        if (file_put_contents($filePath, $imageData)) {
            return $filePath;
        }
        
        throw new Exception("Failed to save image");
    }
}
?>