<?php
class MistralService {
    private static $apiUrl = 'https://api.mistral.ai/v1/chat/completions';
    
    public static function analyzeText($prompt, $context) {
        $apiKey = getenv('JCDn10oH2EaeYPMsXDctjFa5fdqoPPhe');
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ];

        $data = [
            'model' => 'mistral-medium',
            'messages' => [[
                'role' => 'user',
                'content' => "Context:\n{$context}\n\nQuestion: {$prompt}"
            ]]
        ];

        $options = [
            'http' => [
                'header'  => implode("\r\n", $headers),
                'method'  => 'POST',
                'content' => json_encode($data)
            ]
        ];

        try {
            $response = file_get_contents(self::$apiUrl, false, stream_context_create($options));
            $result = json_decode($response, true);
            return $result['choices'][0]['message']['content'];
        } catch(Exception $e) {
            throw new Exception("Mistral API Error: " . $e->getMessage());
        }
    }
}
?>