CREATE DATABASE IF NOT EXISTS studenthub;
USE studenthub;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'faculty', 'student') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    name VARCHAR(100) NOT NULL,
    roll_no VARCHAR(20) NOT NULL UNIQUE,
    branch VARCHAR(50) NOT NULL,
    semester INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    category VARCHAR(50) NOT NULL,
    event_date DATE NOT NULL,
    venue VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    event_id INT NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    role VARCHAR(20) NOT NULL,
    action VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@charusat.edu.in', '$2y$10$w0X3GzFwP9.Kj9k0cKjMreX5bZ7kM5jA7O7Fh6s7r1wV8x1q2z3y4', 'admin'),
('gehna_patel', 'gehna@charusat.edu.in', '$2y$10$w0X3GzFwP9.Kj9k0cKjMreX5bZ7kM5jA7O7Fh6s7r1wV8x1q2z3y4', 'student'),
('aarav_sharma', 'aarav@charusat.edu.in', '$2y$10$w0X3GzFwP9.Kj9k0cKjMreX5bZ7kM5jA7O7Fh6s7r1wV8x1q2z3y4', 'student')
ON DUPLICATE KEY UPDATE password = VALUES(password);

INSERT INTO students (id, user_id, name, roll_no, branch, semester) VALUES
(1, 2, 'Gehna Patel', '23CE001', 'Computer Engineering', 3),
(2, 3, 'Aarav Sharma', '23IT012', 'Information Technology', 3)
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO events (id, title, category, event_date, venue) VALUES
(1, 'Hackathon 2026', 'Technical', '2026-11-15', 'Lab 4'),
(2, 'Sports Meet', 'Sports', '2026-11-20', 'Ground'),
(3, 'Cultural Night', 'Cultural', '2026-11-28', 'Amphitheatre')
ON DUPLICATE KEY UPDATE title = VALUES(title);

INSERT INTO registrations (id, student_id, event_id) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 1)
ON DUPLICATE KEY UPDATE id = VALUES(id);

INSERT INTO audit_logs (username, role, action, ip_address) VALUES
('admin', 'admin', 'SYSTEM_INITIALIZATION', '127.0.0.1');
