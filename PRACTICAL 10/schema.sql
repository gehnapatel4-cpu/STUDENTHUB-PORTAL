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

INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@charusat.edu.in', '$2y$10$w0X3GzFwP9.Kj9k0cKjMreX5bZ7kM5jA7O7Fh6s7r1wV8x1q2z3y4', 'admin'),
('gehna_patel', 'gehna@charusat.edu.in', '$2y$10$Z3o2MvhgXfP2U1N/2rV69epm39O0R/H/w2bJq6q6v6e2x/y2z.12a', 'student')
ON DUPLICATE KEY UPDATE password = VALUES(password);
