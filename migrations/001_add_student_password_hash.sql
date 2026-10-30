-- Run once on databases created before Practical 9.
-- Nullable values preserve demo/event-only student profiles; register.php
-- always inserts a non-null password_hash for newly registered accounts.
ALTER TABLE students
  ADD COLUMN password_hash VARCHAR(255) DEFAULT NULL AFTER course;
