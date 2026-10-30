USE studenthub;

ALTER TABLE students
  ADD COLUMN status ENUM('active', 'inactive') NOT NULL DEFAULT 'active';

