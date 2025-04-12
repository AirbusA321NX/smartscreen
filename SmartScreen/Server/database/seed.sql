-- seed.sql

-- Insert users into the 'users' table
INSERT INTO users (username, password, created_at) 
VALUES
('john_doe', 'password123', NOW()),
('jane_smith', 'securepass456', NOW());
