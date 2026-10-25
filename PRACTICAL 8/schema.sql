CREATE DATABASE IF NOT EXISTS studenthub;
USE studenthub;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'faculty', 'admin') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    name VARCHAR(100) NOT NULL,
    roll_no VARCHAR(20) NOT NULL UNIQUE,
    branch VARCHAR(50) NOT NULL,
    semester INT NOT NULL,
    phone VARCHAR(15) NOT NULL,
    attendance_pct DECIMAL(5, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    event_date DATE NOT NULL,
    venue VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    event_id INT NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

INSERT IGNORE INTO users (id, username, email, password, role) VALUES
(1, 'admin', 'admin@charusat.edu.in', '$2y$10$w0X3GzFwP9.Kj9k0cKjMreX5bZ7kM5jA7O7Fh6s7r1wV8x1q2z3y4', 'admin'),
(2, 'gehna_patel', 'gehna@charusat.edu.in', '$2y$10$w0X3GzFwP9.Kj9k0cKjMreX5bZ7kM5jA7O7Fh6s7r1wV8x1q2z3y4', 'student'),
(3, 'aarav_sharma', 'aarav@charusat.edu.in', '$2y$10$w0X3GzFwP9.Kj9k0cKjMreX5bZ7kM5jA7O7Fh6s7r1wV8x1q2z3y4', 'student');

INSERT IGNORE INTO students (id, user_id, name, roll_no, branch, semester, phone, attendance_pct) VALUES
(1, 2, 'Gehna Patel', '23CE001', 'CE', 3, '9876543210', 92.50),
(2, 3, 'Aarav Sharma', '23IT012', 'IT', 3, '9123456789', 88.00);

INSERT IGNORE INTO events (id, title, description, event_date, venue, category) VALUES
(1, 'Tech Fest & Hackathon', '24-hour university coding competition.', '2026-11-15', 'Auditorium 1', 'Technical'),
(2, 'Inter-College Sports Day', 'Cricket, Badminton, and Athletic matches.', '2026-11-22', 'Campus Ground', 'Sports'),
(3, 'Cultural Fest - Spandan', 'Music, dance, and stage drama.', '2026-11-28', 'Open Amphitheatre', 'Cultural');

INSERT IGNORE INTO registrations (id, student_id, event_id) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 1);
