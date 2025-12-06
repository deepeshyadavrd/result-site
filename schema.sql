-- schema.sql
CREATE DATABASE IF NOT EXISTS results_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE results_db;

-- users
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('superadmin','manager') NOT NULL DEFAULT 'manager',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- games
CREATE TABLE games (
  id INT AUTO_INCREMENT PRIMARY KEY,
  game_name VARCHAR(150) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- results
CREATE TABLE results (
  id INT AUTO_INCREMENT PRIMARY KEY,
  game_id INT NOT NULL,
  result_date DATE NOT NULL,
  result_value VARCHAR(50) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY u_game_date (game_id, result_date),
  FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
);

-- user_game_map
CREATE TABLE user_game_map (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  game_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY u_user_game (user_id, game_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
);

-- Seed a superadmin (change password after import)
INSERT INTO users (username, password, role) VALUES (
  'admin',
  -- password: admin123 (please change)
  '$2y$10$wH9k4r9j6/0N2fJZ2oT3SeFqzv2jKzYc3t7Zp0m6KZQh8s9EoZzqW',
  'superadmin'
);
