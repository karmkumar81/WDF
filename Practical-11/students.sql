-- Practical 11: Students Database Table & Minimum 20 Seed Records
CREATE DATABASE IF NOT EXISTS `studenthub` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `studenthub`;

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

-- Truncate / Replace with 20 distinct records for testing CRUD operations
INSERT INTO `students` (`id`, `enrollment_no`, `name`, `email`, `mobile`, `department`, `semester`, `gpa`, `status`) VALUES
(1, '24IT001', 'Alice Patel', 'alice.p@charusat.edu.in', '9876543210', 'IT', 3, 9.20, 'active'),
(2, '24IT002', 'Bhavik Shah', 'bhavik.s@charusat.edu.in', '9823456781', 'IT', 3, 8.75, 'active'),
(3, '24IT003', 'Chirag Joshi', 'chirag.j@charusat.edu.in', '9712345678', 'IT', 3, 7.90, 'active'),
(4, '24IT004', 'Divya Prajapati', 'divya.p@charusat.edu.in', '9898012345', 'IT', 3, 8.95, 'active'),
(5, '24IT005', 'Ekta Makwana', 'ekta.m@charusat.edu.in', '9879023456', 'IT', 3, 9.40, 'active'),
(6, '24CS015', 'Falguni Dave', 'falguni.d@charusat.edu.in', '9724034567', 'CSE', 3, 8.80, 'active'),
(7, '24CS016', 'Gaurav Modi', 'gaurav.m@charusat.edu.in', '9825045678', 'CSE', 3, 7.60, 'inactive'),
(8, '24CS017', 'Hetvi Desai', 'hetvi.d@charusat.edu.in', '9909056789', 'CSE', 3, 9.15, 'active'),
(9, '24CS018', 'Ishan Parikh', 'ishan.p@charusat.edu.in', '9426067890', 'CSE', 3, 8.45, 'active'),
(10, '24CS019', 'Jhanvi Mehta', 'jhanvi.m@charusat.edu.in', '9824078901', 'CSE', 3, 9.30, 'active'),
(11, '24CE101', 'Karan Thakkar', 'karan.t@charusat.edu.in', '9714089012', 'CE', 3, 8.00, 'active'),
(12, '24CE102', 'Lalit Solanki', 'lalit.s@charusat.edu.in', '9925090123', 'CE', 3, 8.65, 'active'),
(13, '24CE103', 'Meera Rajput', 'meera.r@charusat.edu.in', '9879101234', 'CE', 3, 9.05, 'active'),
(14, '24CE104', 'Nirav Barot', 'nirav.b@charusat.edu.in', '9725112345', 'CE', 3, 8.35, 'active'),
(15, '24CE105', 'Omkar Trivedi', 'omkar.t@charusat.edu.in', '9824123456', 'CE', 3, 7.85, 'inactive'),
(16, '24AI001', 'Prachi Somani', 'prachi.s@charusat.edu.in', '9909134567', 'AIML', 3, 9.50, 'active'),
(17, '24AI002', 'Qasim Vohra', 'qasim.v@charusat.edu.in', '9426145678', 'AIML', 3, 8.20, 'active'),
(18, '24AI003', 'Rohan Bhatt', 'rohan.b@charusat.edu.in', '9825156789', 'AIML', 3, 8.90, 'active'),
(19, '24AI004', 'Sneha Soni', 'sneha.s@charusat.edu.in', '9714167890', 'AIML', 3, 9.10, 'active'),
(20, '24AI005', 'Tanmay Pandya', 'tanmay.p@charusat.edu.in', '9925178901', 'AIML', 3, 8.40, 'active')
ON DUPLICATE KEY UPDATE `enrollment_no`=VALUES(`enrollment_no`);
