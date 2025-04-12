<?php
class MistralService {
    private static $apiUrl = 'https://api.mistral.ai/v1/chat/completions';
    
    public static function analyzeText($prompt, $context) {
        // Retrieve API key from environment variables (ensure this is set in your .env file)
        $apiKey = getenv('MISTRAL_API_KEY');
        if (!$apiKey) {
            throw new Exception("API key not found in environment variables.");
        }
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ];

        $data = [
            'model' => 'mistral-medium',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => "Context:\n{$context}\n\nQuestion: {$prompt}"
                ]
            ]
        ];

        $options = [
            'http' => [
                'header'  => implode("\r\n", $headers),
                'method'  => 'POST',
                'content' => json_encode($data),
                'ignore_errors' => true // Allow retrieval of error responses
            ]
        ];

        $contextStream = stream_context_create($options);
        $response = file_get_contents(self::$apiUrl, false, $contextStream);
        if ($response === false) {
            $error = error_get_last();
            throw new Exception("Mistral API Error: " . $error['message']);
        }

        $result = json_decode($response, true);
        if (isset($result['choices'][0]['message']['content'])) {
            return $result['choices'][0]['message']['content'];
        } else {
            throw new Exception("Unexpected API response format: " . $response);
        }
    }
}
?>
