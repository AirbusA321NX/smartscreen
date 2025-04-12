-- 20230801_initial_schema.sql

-- Create the database (if it doesn't exist)
CREATE DATABASE IF NOT EXISTS smartscreen_insight;
USE smartscreen_insight;

-- Create the 'users' table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Create the 'analysis_logs' table
CREATE TABLE IF NOT EXISTS analysis_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    prompt TEXT NOT NULL,
    screenshot_text LONGTEXT NOT NULL,
    ai_response LONGTEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;
