CREATE DATABASE IF NOT EXISTS studenthub
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE studenthub;

CREATE TABLE IF NOT EXISTS students (
  student_id VARCHAR(30) NOT NULL,
  full_name VARCHAR(100) NOT NULL,
  age TINYINT UNSIGNED NOT NULL,
  email VARCHAR(254) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  gender VARCHAR(30) NOT NULL,
  course VARCHAR(100) NOT NULL,
  -- NULL is retained for demo/event-only profiles; register.php always stores a hash.
  password_hash VARCHAR(255) DEFAULT NULL,
  role ENUM('Student', 'Admin') NOT NULL DEFAULT 'Student',
  status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (student_id),
  UNIQUE KEY uq_students_email (email)
) ENGINE=InnoDB;

-- Administrator accounts are separate from student academic records.
CREATE TABLE IF NOT EXISTS admins (
  admin_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(254) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (admin_id),
  UNIQUE KEY uq_admins_email (email)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS audit_logs (
  audit_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  actor_email VARCHAR(254) NOT NULL,
  actor_role VARCHAR(20) NOT NULL,
  action VARCHAR(80) NOT NULL,
  details VARCHAR(500) NOT NULL DEFAULT '',
  ip_address VARCHAR(45) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (audit_id),
  KEY idx_audit_created (created_at),
  KEY idx_audit_actor (actor_email)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS events (
  event_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(140) NOT NULL,
  category VARCHAR(60) NOT NULL,
  description TEXT NOT NULL,
  event_date DATE NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME DEFAULT NULL,
  location VARCHAR(150) NOT NULL,
  poster_path VARCHAR(255) DEFAULT NULL,
  capacity SMALLINT UNSIGNED DEFAULT NULL,
  status ENUM('open', 'cancelled') NOT NULL DEFAULT 'open',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (event_id),
  KEY idx_events_date_status (event_date, status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS registrations (
  registration_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  student_id VARCHAR(30) NOT NULL,
  event_id INT UNSIGNED NOT NULL,
  registered_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status ENUM('registered', 'cancelled') NOT NULL DEFAULT 'registered',
  PRIMARY KEY (registration_id),
  UNIQUE KEY uq_registrations_student_event (student_id, event_id),
  KEY idx_registrations_event_status (event_id, status),
  CONSTRAINT fk_registrations_student
    FOREIGN KEY (student_id) REFERENCES students (student_id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_registrations_event
    FOREIGN KEY (event_id) REFERENCES events (event_id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Example student accounts are used only by the database connection check.
INSERT IGNORE INTO students (student_id, full_name, age, email, phone, gender, course)
VALUES
  ('DEMO001', 'Aarav Shah', 20, 'aarav.demo@example.edu', '+91 90000 00001', 'Prefer not to say', 'B.Tech'),
  ('DEMO002', 'Mira Patel', 19, 'mira.demo@example.edu', '+91 90000 00002', 'Prefer not to say', 'B.Tech');

INSERT IGNORE INTO events (event_id, title, category, description, event_date, start_time, end_time, location, capacity, status)
VALUES
  (1, 'Designing for the Web', 'Workshop', 'A practical session on accessible layouts, visual hierarchy, and responsive interface design.', '2026-11-05', '10:00:00', '12:00:00', 'Design Studio', 80, 'open'),
  (2, 'Student Research Colloquium', 'Research', 'Short talks and poster presentations from student research groups across the campus.', '2026-11-12', '14:00:00', '16:30:00', 'Central Seminar Hall', 120, 'open'),
  (3, 'Careers in Software Engineering', 'Career', 'A career conversation about internships, technical interviews, and early-career development.', '2026-11-18', '11:00:00', '12:30:00', 'Auditorium 2', 150, 'open'),
  (4, 'Student Project Showcase', 'Campus', 'Explore semester projects and meet the student teams building them.', '2026-11-26', '13:00:00', '16:00:00', 'Innovation Centre', 200, 'open');

INSERT IGNORE INTO registrations (student_id, event_id, status)
VALUES
  ('DEMO001', 1, 'registered'),
  ('DEMO002', 2, 'registered');

