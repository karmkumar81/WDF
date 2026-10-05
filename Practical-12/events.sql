-- Practical 12: Events Database Table
CREATE DATABASE IF NOT EXISTS `studenthub` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `studenthub`;

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

INSERT INTO `events` (`id`, `title`, `category`, `event_date`, `location`, `seats`, `poster_image`, `status`) VALUES
(1, 'WebDev Hackathon 2026', 'Technical', '2026-11-12', 'CSPIT Lab 304', 80, 'default_event.jpg', 'open'),
(2, 'RoboWars Arena Championship', 'Robotics', '2026-11-20', 'Central Lawn', 40, 'default_event.jpg', 'open'),
(3, 'AI & Generative Models Masterclass', 'Workshop', '2026-12-05', 'DEPSTAR Aud 1', 120, 'default_event.jpg', 'open'),
(4, 'Inter-College Cricket Tournament', 'Sports', '2026-12-15', 'University Ground', 16, 'default_event.jpg', 'closed')
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);
