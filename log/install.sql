CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('superadmin','user') DEFAULT 'user',
  active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  log_name VARCHAR(100) NOT NULL,
  station_call VARCHAR(20) NOT NULL,
  timezone VARCHAR(64) NOT NULL,
  my_gridsquare VARCHAR(6),
  reference VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_logs_user (user_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE qso (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  log_id INT NOT NULL,
  callsign VARCHAR(20) NOT NULL,
  band VARCHAR(10) NOT NULL,
  mode VARCHAR(20) NOT NULL,
  freq DECIMAL(10,5) NULL,
  rst_sent VARCHAR(10),
  rst_rcvd VARCHAR(10),
  name VARCHAR(100),
  qth VARCHAR(100),
  gridsquare VARCHAR(10),
  notes TEXT,
  qso_datetime_utc DATETIME NOT NULL,
  qso_datetime_local DATETIME NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_user_log_date (user_id, log_id, qso_datetime_utc),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (log_id) REFERENCES logs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
