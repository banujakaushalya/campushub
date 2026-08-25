-- CampusHub Complete Database Schema & Initial Data
-- Web Programming I (H7DT) Assessment Project

SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `campushub` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `campushub`;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` INT AUTO_INCREMENT PRIMARY KEY,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(75) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `phone` VARCHAR(25) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `gender` VARCHAR(10) NOT NULL DEFAULT 'other',
  `role` ENUM('admin', 'student') NOT NULL DEFAULT 'student',
  `student_id` VARCHAR(50) NULL,
  `institution` VARCHAR(150) NULL DEFAULT 'Java Institute for Advanced Technology',
  `profile_photo` VARCHAR(255) NULL DEFAULT 'default-avatar.png',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Admin Details Table
CREATE TABLE IF NOT EXISTS `admin` (
  `admin_id` INT AUTO_INCREMENT PRIMARY KEY,
  `users_user_id` INT NOT NULL,
  `department` VARCHAR(100) DEFAULT 'Student Services',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_admin_users` FOREIGN KEY (`users_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Events Table
CREATE TABLE IF NOT EXISTS `events` (
  `event_id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `category` VARCHAR(50) NOT NULL DEFAULT 'Workshop',
  `club_name` VARCHAR(100) NOT NULL DEFAULT 'CampusHub General',
  `event_date` DATE NOT NULL,
  `event_time` TIME NOT NULL,
  `location` VARCHAR(255) NOT NULL,
  `max_participants` INT NOT NULL DEFAULT 50,
  `banner_image` VARCHAR(255) NULL,
  `meta_data` JSON NULL,
  `status` ENUM('upcoming', 'completed', 'cancelled') DEFAULT 'upcoming',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Registrations Table
CREATE TABLE IF NOT EXISTS `registrations` (
  `registration_id` INT AUTO_INCREMENT PRIMARY KEY,
  `events_event_id` INT NOT NULL,
  `users_user_id` INT NOT NULL,
  `status` ENUM('confirmed', 'pending', 'cancelled') DEFAULT 'confirmed',
  `notes` TEXT NULL,
  `registered_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_registrations_events` FOREIGN KEY (`events_event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_registrations_users` FOREIGN KEY (`users_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_registration` (`events_event_id`, `users_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Announcements Table
CREATE TABLE IF NOT EXISTS `announcements` (
  `announcement_id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `category` VARCHAR(50) NOT NULL DEFAULT 'General',
  `priority` ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
  `admin_admin_id` INT NULL,
  `meta_data` JSON NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_announcements_admin` FOREIGN KEY (`admin_admin_id`) REFERENCES `admin` (`admin_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Media Table (Audio, Video, Photos)
CREATE TABLE IF NOT EXISTS `media` (
  `media_id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `media_type` ENUM('photo', 'video', 'audio') NOT NULL DEFAULT 'photo',
  `file_url` VARCHAR(255) NOT NULL,
  `thumbnail_url` VARCHAR(255) NULL,
  `users_user_id` INT NULL,
  `events_event_id` INT NULL,
  `meta_data` JSON NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_media_users` FOREIGN KEY (`users_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_media_events` FOREIGN KEY (`events_event_id`) REFERENCES `events` (`event_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Master Control / System Settings
CREATE TABLE IF NOT EXISTS `master_control` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) UNIQUE NOT NULL,
  `setting_value` TEXT NOT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- SEED DATA (Default password for all sample accounts is: Password123!)
-- password_hash for 'Password123!': $2y$10$wE96tO1r7n.m4P1XvLgE..1z9B.hTslzL964r56aL7HkI8kFsqDkO

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `phone`, `password_hash`, `gender`, `role`, `student_id`, `institution`) VALUES
(1, 'Admin', 'Officer', 'admin@campushub.edu', '0771234567', '$2y$10$e7QcI2Zg95pUa54mX2e/k.1G0r9fO45vJ4pTqB2uUo3bX8F8P99jC', 'male', 'admin', 'ADM-001', 'Java Institute'),
(2, 'Kamal', 'Perera', 'kamal@student.edu', '0719876543', '$2y$10$e7QcI2Zg95pUa54mX2e/k.1G0r9fO45vJ4pTqB2uUo3bX8F8P99jC', 'male', 'student', 'JI-2024-042', 'Java Institute'),
(3, 'Nimali', 'Silva', 'nimali@student.edu', '0761122334', '$2y$10$e7QcI2Zg95pUa54mX2e/k.1G0r9fO45vJ4pTqB2uUo3bX8F8P99jC', 'female', 'student', 'JI-2024-089', 'Java Institute')
ON DUPLICATE KEY UPDATE `email`=`email`;

INSERT INTO `admin` (`admin_id`, `users_user_id`, `department`) VALUES
(1, 1, 'Student Services & Academic Coordination')
ON DUPLICATE KEY UPDATE `department`=`department`;

INSERT INTO `events` (`event_id`, `title`, `description`, `category`, `club_name`, `event_date`, `event_time`, `location`, `max_participants`, `banner_image`, `status`) VALUES
(1, 'Full-Stack Web Dev Hackathon 2026', 'Join our 24-hour coding sprint building modern web applications with PHP, JavaScript, and APIs.', 'Competition', 'Computing Society', '2026-09-15', '09:00:00', 'Main Auditorium, Tech Campus', 80, 'hackathon.jpg', 'upcoming'),
(2, 'AI & Robotics Innovation Workshop', 'Hands-on workshop exploring real-world AI applications and embedded hardware programming.', 'Workshop', 'Robotics Club', '2026-09-22', '14:00:00', 'Lab 04, Innovation Wing', 40, 'robotics.jpg', 'upcoming'),
(3, 'Annual Inter-University Sports Meet', 'Athletics, badminton, table tennis, and chess championships across participating campuses.', 'Sports', 'Sports Council', '2026-10-05', '08:00:00', 'University Sports Complex', 200, 'sports.jpg', 'upcoming'),
(4, 'Music & Arts Acoustic Night', 'An evening of acoustic melodies, spoken word poetry, and creative student artwork exhibitions.', 'Cultural', 'Music & Arts Circle', '2026-10-18', '18:30:00', 'Open Amphitheatre', 120, 'music.jpg', 'upcoming')
ON DUPLICATE KEY UPDATE `title`=`title`;

INSERT INTO `announcements` (`announcement_id`, `title`, `content`, `category`, `priority`, `admin_admin_id`) VALUES
(1, 'Welcome to CampusHub 2026 Semester Portal', 'The unified student portal is now live! Register for upcoming student club events and access multimedia resources.', 'General', 'high', 1),
(2, 'Club Registration Deadline for Term 3', 'All students wishing to join competitive societies must finalize registrations before September 10th.', 'Clubs', 'medium', 1),
(3, 'Inter-Campus Transport Schedule Update', 'Special shuttle buses will run between campuses for the upcoming Sports Meet.', 'Logistics', 'low', 1)
ON DUPLICATE KEY UPDATE `title`=`title`;

INSERT INTO `registrations` (`registration_id`, `events_event_id`, `users_user_id`, `status`, `notes`) VALUES
(1, 1, 2, 'confirmed', 'Team lead for Team Alpha'),
(2, 2, 2, 'confirmed', 'Interested in embedded systems'),
(3, 3, 3, 'confirmed', 'Badminton singles participant')
ON DUPLICATE KEY UPDATE `status`=`status`;
