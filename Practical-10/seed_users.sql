-- Practical 10: Seed Users with Roles and Pre-hashed Passwords
-- Password for all accounts: Student@2026!
-- Generated via: password_hash('Student@2026!', PASSWORD_DEFAULT)
-- Password for admin: Admin@2026!

CREATE DATABASE IF NOT EXISTS `studenthub` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `studenthub`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('student', 'admin') NOT NULL DEFAULT 'student',
  `remember_token` VARCHAR(64) DEFAULT NULL,
  `last_login` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert / Update Test Users
-- Test Admin: admin@charusat.edu.in / Admin@2026!
-- Test Student: alice@charusat.edu.in / Student@2026!
INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`) VALUES
(1, 'System Administrator', 'admin@charusat.edu.in', '$2y$10$w857Q8LszwW9i.e5v74Bje6c7E8s48gYjM/L51D7oV.9tN3kR8zVO', 'admin'),
(2, 'Alice Patel (Student)', 'alice@charusat.edu.in', '$2y$10$tM3V.y5qO8q2H4jF/7zIBeYV7J7B0kQy.W9m8sL4l7jT0q5gA5wDO', 'student')
ON DUPLICATE KEY UPDATE `email`=`email`;
