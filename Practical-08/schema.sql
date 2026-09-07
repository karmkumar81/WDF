-- ============================================================================
-- Practical 8: StudentHub Normalized MySQL Database Schema & Seed Data
-- Course: ITUE203 - Web Development Frameworks | CHARUSAT
-- Relational Model Normalized to 3NF with Foreign Key Constraints & Triggers
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `studenthub` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `studenthub`;

-- 1. Users Table (Authentication & RBAC)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('student', 'admin') NOT NULL DEFAULT 'student',
  `remember_token` VARCHAR(64) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_users_email` (`email`),
  INDEX `idx_users_role` (`role`)
) ENGINE=InnoDB;

-- 2. Students Profile Table (Linked 1:1 with Users)
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
  `status` ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  INDEX `idx_students_enrollment` (`enrollment_no`),
  INDEX `idx_students_department` (`department`)
) ENGINE=InnoDB;

-- 3. Events Table
CREATE TABLE IF NOT EXISTS `events` (
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

-- 4. Event Registrations Associative Table (M:N between Students & Events)
CREATE TABLE IF NOT EXISTS `registrations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `event_id` INT NOT NULL,
  `registered_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `status` ENUM('confirmed', 'waitlisted', 'cancelled') DEFAULT 'confirmed',
  UNIQUE KEY `unique_student_event` (`student_id`, `event_id`),
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- 5. Audit Logs Table (Administrative Traceability)
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `action` VARCHAR(100) NOT NULL,
  `details` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) NOT NULL DEFAULT '127.0.0.1',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================================
-- ADVANCED EXTENSION: Stored Procedure for Atomic Event Registration
-- ============================================================================
DROP PROCEDURE IF EXISTS `sp_RegisterStudentForEvent`;
DELIMITER //
CREATE PROCEDURE `sp_RegisterStudentForEvent`(
    IN p_student_id INT,
    IN p_event_id INT,
    OUT p_status_code INT,
    OUT p_message VARCHAR(255)
)
BEGIN
    DECLARE v_available_seats INT;
    DECLARE v_already_registered INT;

    -- Check if student already registered
    SELECT COUNT(*) INTO v_already_registered
    FROM `registrations`
    WHERE `student_id` = p_student_id AND `event_id` = p_event_id;

    IF v_already_registered > 0 THEN
        SET p_status_code = 409;
        SET p_message = 'Student is already registered for this event.';
    ELSE
        -- Check available seats
        SELECT `seats` INTO v_available_seats FROM `events` WHERE `id` = p_event_id;

        IF v_available_seats <= 0 THEN
            SET p_status_code = 400;
            SET p_message = 'Registration closed: Event is fully booked.';
        ELSE
            START TRANSACTION;
            INSERT INTO `registrations` (`student_id`, `event_id`, `status`)
            VALUES (p_student_id, p_event_id, 'confirmed');

            UPDATE `events` SET `seats` = `seats` - 1 WHERE `id` = p_event_id;
            COMMIT;

            SET p_status_code = 200;
            SET p_message = 'Registration completed successfully.';
        END IF;
    END IF;
END //
DELIMITER ;

-- ============================================================================
-- SEED DATA (Password for all test users is: Password@123)
-- Password hash: $2y$10$wT2Hl9Xl9QoKzQeBfV5x2eR6vB5aOaGkNu1j0jC6sL2mNo7yQf6nO
-- ============================================================================

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`) VALUES
(1, 'Admin Officer', 'admin@charusat.edu.in', '$2y$10$8v5Z27vEom5p00C.jVdJwe8E2N9p5F3ZJ9h8Q9PzO.0wE4bUuU7eS', 'admin'),
(2, 'Alice Patel', 'alice@charusat.edu.in', '$2y$10$8v5Z27vEom5p00C.jVdJwe8E2N9p5F3ZJ9h8Q9PzO.0wE4bUuU7eS', 'student'),
(3, 'Bhavik Shah', 'bhavik@charusat.edu.in', '$2y$10$8v5Z27vEom5p00C.jVdJwe8E2N9p5F3ZJ9h8Q9PzO.0wE4bUuU7eS', 'student'),
(4, 'Chirag Joshi', 'chirag@charusat.edu.in', '$2y$10$8v5Z27vEom5p00C.jVdJwe8E2N9p5F3ZJ9h8Q9PzO.0wE4bUuU7eS', 'student')
ON DUPLICATE KEY UPDATE `id`=`id`;

INSERT INTO `students` (`id`, `user_id`, `enrollment_no`, `name`, `email`, `mobile`, `department`, `semester`, `gpa`, `status`) VALUES
(1, 2, '24IT001', 'Alice Patel', 'alice@charusat.edu.in', '9876543210', 'IT', 3, 9.20, 'active'),
(2, 3, '24IT002', 'Bhavik Shah', 'bhavik@charusat.edu.in', '9823456781', 'IT', 3, 8.75, 'active'),
(3, 4, '24CS015', 'Chirag Joshi', 'chirag@charusat.edu.in', '9712345678', 'CSE', 3, 8.10, 'active')
ON DUPLICATE KEY UPDATE `id`=`id`;

INSERT INTO `events` (`id`, `title`, `category`, `event_date`, `location`, `seats`, `poster_image`, `status`) VALUES
(1, 'WebDev Hackathon 2026', 'Technical', '2026-11-12', 'CSPIT Lab 304', 79, 'hackathon.jpg', 'open'),
(2, 'RoboWars Arena Championship', 'Robotics', '2026-11-20', 'CHARUSAT Central Lawn', 40, 'robowars.jpg', 'open'),
(3, 'AI & Generative Models Masterclass', 'Workshop', '2026-12-05', 'DEPSTAR Aud 1', 120, 'ai_workshop.jpg', 'open')
ON DUPLICATE KEY UPDATE `id`=`id`;

INSERT INTO `registrations` (`id`, `student_id`, `event_id`, `status`) VALUES
(1, 1, 1, 'confirmed')
ON DUPLICATE KEY UPDATE `id`=`id`;

INSERT INTO `audit_logs` (`user_id`, `action`, `details`, `ip_address`) VALUES
(1, 'INITIALIZE_SCHEMA', 'Practical 8 database schema initialized with seed data', '127.0.0.1');
