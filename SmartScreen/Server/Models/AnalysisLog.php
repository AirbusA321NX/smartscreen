<?php
class AnalysisLog {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function create($userId, $prompt, $screenshotText, $aiResponse) {
        $stmt = $this->db->conn->prepare(
            "INSERT INTO analysis_logs 
            (user_id, prompt, screenshot_text, ai_response) 
            VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([$userId, $prompt, $screenshotText, $aiResponse]);
    }

    public function getHistory($userId, $limit = 10) {
        $stmt = $this->db->conn->prepare(
            "SELECT * FROM analysis_logs 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT ?"
        );
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }
}
?>