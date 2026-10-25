CREATE DATABASE IF NOT EXISTS studenthub;
USE studenthub;

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    event_date DATE NOT NULL,
    venue VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO events (title, description, event_date, venue, category) VALUES
('Hackathon 2026', 'Inter-department coding and project building competition.', '2026-11-15', 'Lab 4', 'Technical'),
('Campus Cricket League', 'Tennis cricket tournament for student branches.', '2026-11-20', 'Ground', 'Sports'),
('Music & Acoustic Eve', 'Student musical bands and solo singer performances.', '2026-11-26', 'Amphitheatre', 'Cultural')
ON DUPLICATE KEY UPDATE title = VALUES(title);
