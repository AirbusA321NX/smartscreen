CREATE DATABASE IF NOT EXISTS smart_screen;
USE smart_screen;

CREATE TABLE IF NOT EXISTS results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    command TEXT,
    analysis TEXT,
    screenshot_path TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);