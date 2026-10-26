-- ============================================================================
-- CHAROTAR UNIVERSITY OF SCIENCE AND TECHNOLOGY (CHARUSAT)
-- FACULTY OF TECHNOLOGY AND ENGINEERING (FTE)
-- WEB DEVELOPMENT FRAMEWORKS (ITUE203) - SEMESTER 3
-- Practical 15: StudentHub Master Database Dump
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `studenthub` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `studenthub`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `registrations`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Users Table
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('student', 'admin') NOT NULL DEFAULT 'student',
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `remember_token` VARCHAR(64) DEFAULT NULL,
  `last_login` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_users_email` (`email`),
  INDEX `idx_users_role` (`role`)
) ENGINE=InnoDB;

-- 2. Students Table
CREATE TABLE `students` (
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
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_students_enrollment` (`enrollment_no`),
  INDEX `idx_students_dept` (`department`)
) ENGINE=InnoDB;

-- 3. Events Table
CREATE TABLE `events` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  `event_date` DATE NOT NULL,
  `location` VARCHAR(150) NOT NULL,
  `seats` INT NOT NULL DEFAULT 50,
  `poster_image` VARCHAR(255) DEFAULT 'default_event.jpg',
  `status` ENUM('open', 'closed', 'cancelled') DEFAULT 'open',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_events_category` (`category`),
  INDEX `idx_events_date` (`event_date`)
) ENGINE=InnoDB;

-- 4. Registrations Table
CREATE TABLE `registrations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `event_id` INT NOT NULL,
  `registered_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('confirmed', 'waitlisted', 'cancelled') DEFAULT 'confirmed',
  UNIQUE KEY `unique_student_event` (`student_id`, `event_id`),
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Audit Logs Table
CREATE TABLE `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `action` VARCHAR(100) NOT NULL,
  `details` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) NOT NULL DEFAULT '127.0.0.1',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================================
-- SEED DATA
-- Default Passwords:
-- Admin: Admin@2026!
-- Students: Student@2026!
-- ============================================================================

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `status`) VALUES
(1, 'System Administrator', 'admin@charusat.edu.in', '$2y$10$w857Q8LszwW9i.e5v74Bje6c7E8s48gYjM/L51D7oV.9tN3kR8zVO', 'admin', 'active'),
(2, 'Alice Patel', 'alice@charusat.edu.in', '$2y$10$tM3V.y5qO8q2H4jF/7zIBeYV7J7B0kQy.W9m8sL4l7jT0q5gA5wDO', 'student', 'active'),
(3, 'Bhavik Shah', 'bhavik@charusat.edu.in', '$2y$10$tM3V.y5qO8q2H4jF/7zIBeYV7J7B0kQy.W9m8sL4l7jT0q5gA5wDO', 'student', 'active');

INSERT INTO `students` (`id`, `user_id`, `enrollment_no`, `name`, `email`, `mobile`, `department`, `semester`, `gpa`, `status`) VALUES
(1, 2, '24IT001', 'Alice Patel', 'alice@charusat.edu.in', '9876543210', 'IT', 3, 9.20, 'active'),
(2, 3, '24IT002', 'Bhavik Shah', 'bhavik@charusat.edu.in', '9823456781', 'IT', 3, 8.75, 'active'),
(3, NULL, '24CS015', 'Chirag Joshi', 'chirag.j@charusat.edu.in', '9712345678', 'CSE', 3, 8.10, 'active'),
(4, NULL, '24CE101', 'Dharti Varma', 'dharti.v@charusat.edu.in', '9898012345', 'CE', 3, 9.40, 'active'),
(5, NULL, '24AI001', 'Eshan Trivedi', 'eshan.t@charusat.edu.in', '9879023456', 'AIML', 3, 8.85, 'active');

INSERT INTO `events` (`id`, `title`, `category`, `event_date`, `location`, `seats`, `poster_image`, `status`) VALUES
(1, 'WebDev Hackathon 2026', 'Technical', '2026-11-12', 'CSPIT Lab 304', 78, 'default_event.jpg', 'open'),
(2, 'RoboWars Arena Championship', 'Robotics', '2026-11-20', 'Central Lawn', 40, 'default_event.jpg', 'open'),
(3, 'Deep Learning Masterclass', 'Workshop', '2026-12-05', 'DEPSTAR Aud 1', 120, 'default_event.jpg', 'open');

INSERT INTO `registrations` (`id`, `student_id`, `event_id`, `status`) VALUES
(1, 1, 1, 'confirmed'),
(2, 2, 1, 'confirmed');

INSERT INTO `audit_logs` (`user_id`, `action`, `details`, `ip_address`) VALUES
(1, 'SYSTEM_DEPLOYMENT', 'Master deployment dump verified and loaded successfully', '127.0.0.1');
