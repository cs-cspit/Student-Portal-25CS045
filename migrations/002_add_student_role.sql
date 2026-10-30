-- Run once on databases created before Practical 10.
ALTER TABLE students
  ADD COLUMN role ENUM('Student', 'Admin') NOT NULL DEFAULT 'Student' AFTER password_hash;
