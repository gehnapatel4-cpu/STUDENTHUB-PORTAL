CREATE DATABASE IF NOT EXISTS studenthub;
USE studenthub;

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    roll_no VARCHAR(20) NOT NULL UNIQUE,
    branch VARCHAR(50) NOT NULL,
    semester INT NOT NULL,
    phone VARCHAR(15) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO students (name, email, roll_no, branch, semester, phone) VALUES
('Gehna Patel', 'gehna@charusat.edu.in', '23CE001', 'Computer Engineering', 3, '9876543210'),
('Aarav Sharma', 'aarav@charusat.edu.in', '23IT012', 'Information Technology', 3, '9123456789'),
('Diya Joshi', 'diya@charusat.edu.in', '23CE045', 'Computer Engineering', 3, '9822334455'),
('Rohan Mehta', 'rohan@charusat.edu.in', '23EC018', 'Electronics & Communication', 3, '9733445566'),
('Ananya Desai', 'ananya@charusat.edu.in', '23IT030', 'Information Technology', 3, '9644556677')
ON DUPLICATE KEY UPDATE name = VALUES(name);
