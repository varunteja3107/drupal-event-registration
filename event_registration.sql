-- Event Registration module tables

CREATE TABLE event_registration_event (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reg_start VARCHAR(10) NOT NULL,
  reg_end VARCHAR(10) NOT NULL,
  event_date VARCHAR(10) NOT NULL,
  event_name VARCHAR(255) NOT NULL,
  category VARCHAR(64) NOT NULL,
  INDEX category_date (category, event_date),
  INDEX reg_window (reg_start, reg_end)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE event_registration_registration (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(255) NOT NULL,
  email VARCHAR(254) NOT NULL,
  college VARCHAR(255) NOT NULL,
  department VARCHAR(255) NOT NULL,
  category VARCHAR(64) NOT NULL,
  event_date VARCHAR(10) NOT NULL,
  event_name VARCHAR(255) NOT NULL,
  event_id INT NOT NULL,
  created INT NOT NULL DEFAULT 0,
  INDEX email_date (email, event_date),
  INDEX event_ref (event_id),
  INDEX event_name_date (event_name, event_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
