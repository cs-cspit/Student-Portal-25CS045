-- StudentHub Practical 10 user table and demo accounts.
-- Import database/studenthub.sql first for the complete portal database. Existing databases
-- must apply missing migrations before this file: 001 for password_hash, then 002 for role.
CREATE DATABASE IF NOT EXISTS studenthub
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE studenthub;

CREATE TABLE IF NOT EXISTS students (
  student_id VARCHAR(30) NOT NULL,
  full_name VARCHAR(100) NOT NULL,
  age TINYINT UNSIGNED NOT NULL,
  email VARCHAR(254) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  gender VARCHAR(30) NOT NULL,
  course VARCHAR(100) NOT NULL,
  password_hash VARCHAR(255) DEFAULT NULL,
  role ENUM('Student', 'Admin') NOT NULL DEFAULT 'Student',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (student_id),
  UNIQUE KEY uq_students_email (email)
) ENGINE=InnoDB;

INSERT INTO students
  (student_id, full_name, age, email, phone, gender, course, password_hash, role)
VALUES
  ('ADMIN10', 'StudentHub Administrator', 35, 'admin.demo@example.edu', '+91 90000 01001', 'Prefer not to say', 'Administration', '$2y$10$R4Oe9wPDsXVL6PBnxFxYyO6BWpzS5Jgz1s4x6m61GJ7kbmXpDacI.', 'Admin'),
  ('STUDENT10', 'StudentHub Demo Student', 20, 'student.demo@example.edu', '+91 90000 01002', 'Prefer not to say', 'B.Tech', '$2y$10$aM0/6bPPKywY1zebrQTcCePsmU9pkonYyj1GkO8HtjCi3wdii.aZa', 'Student')
ON DUPLICATE KEY UPDATE
  full_name = VALUES(full_name),
  email = VALUES(email),
  password_hash = VALUES(password_hash),
  role = VALUES(role);

