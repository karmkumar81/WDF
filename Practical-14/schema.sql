-- Practical 14: Master Tables for Admin Dashboard with Audit Logging
CREATE DATABASE IF NOT EXISTS `studenthub` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `studenthub`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('student', 'admin') NOT NULL DEFAULT 'student',
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `students` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `enrollment_no` VARCHAR(20) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `mobile` VARCHAR(15) NOT NULL,
  `department` VARCHAR(50) NOT NULL,
  `semester` TINYINT NOT NULL DEFAULT 3,
  `gpa` DECIMAL(3,2) DEFAULT 8.50,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `events` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  `event_date` DATE NOT NULL,
  `location` VARCHAR(150) NOT NULL,
  `seats` INT NOT NULL DEFAULT 50,
  `poster_image` VARCHAR(255) DEFAULT 'default_event.jpg',
  `status` ENUM('open', 'closed', 'cancelled') DEFAULT 'open',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `registrations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `event_id` INT NOT NULL,
  `registered_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('confirmed', 'waitlisted', 'cancelled') DEFAULT 'confirmed',
  UNIQUE KEY `unique_student_event` (`student_id`, `event_id`),
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `action` VARCHAR(100) NOT NULL,
  `details` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) NOT NULL DEFAULT '127.0.0.1',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Initial Seed Data
INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `status`) VALUES
(1, 'Admin Officer', 'admin@charusat.edu.in', '$2y$10$w857Q8LszwW9i.e5v74Bje6c7E8s48gYjM/L51D7oV.9tN3kR8zVO', 'admin', 'active'),
(2, 'Alice Patel', 'alice@charusat.edu.in', '$2y$10$tM3V.y5qO8q2H4jF/7zIBeYV7J7B0kQy.W9m8sL4l7jT0q5gA5wDO', 'student', 'active'),
(3, 'Bhavik Shah', 'bhavik@charusat.edu.in', '$2y$10$tM3V.y5qO8q2H4jF/7zIBeYV7J7B0kQy.W9m8sL4l7jT0q5gA5wDO', 'student', 'inactive')
ON DUPLICATE KEY UPDATE `id`=`id`;

INSERT INTO `audit_logs` (`user_id`, `action`, `details`, `ip_address`) VALUES
(1, 'SYSTEM_INIT', 'Database tables and admin dashboard initialized', '127.0.0.1'),
(1, 'USER_STATUS_TOGGLE', 'Toggled user ID 3 status to inactive', '127.0.0.1');
