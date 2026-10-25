CREATE DATABASE IF NOT EXISTS studenthub;
USE studenthub;

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    event_date DATE NOT NULL,
    venue VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    poster_image VARCHAR(255) DEFAULT '',
    status ENUM('open', 'closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO events (title, description, event_date, venue, category, poster_image, status) VALUES
('Tech Fest & Hackathon', '24-hour university coding competition and project expo.', '2026-11-15', 'College Auditorium', 'Technical', '', 'open'),
('Inter-Branch Sports Meet', 'Cricket, Football, and Athletics tournaments.', '2026-11-22', 'Sports Complex', 'Sports', '', 'open'),
('Cultural Night - Spandan', 'Live musical performances, drama, and folk dance.', '2026-11-28', 'Open Amphitheatre', 'Cultural', '', 'open')
ON DUPLICATE KEY UPDATE title = VALUES(title);
