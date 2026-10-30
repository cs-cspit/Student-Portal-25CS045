USE studenthub;

-- Practical 13 uses StudentHub's existing events table; it does not create a demo table.
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
