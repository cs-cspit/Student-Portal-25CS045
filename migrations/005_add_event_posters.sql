USE studenthub;

ALTER TABLE events
  ADD COLUMN poster_path VARCHAR(255) DEFAULT NULL AFTER location;
