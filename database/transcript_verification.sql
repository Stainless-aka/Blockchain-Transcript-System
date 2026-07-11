-- ============================================================
-- Blockchain-Based Student Transcript Verification System
-- Database Schema
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `transcript_verification` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `transcript_verification`;

-- ============================================================
-- Table: users
-- Stores administrator accounts
-- ============================================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(150) NOT NULL,
  `role` ENUM('admin', 'super_admin') NOT NULL DEFAULT 'admin',
  `last_login` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin user (username: admin, password: admin123)
-- Hash generated with: password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12])
INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `created_at`) VALUES
(1, 'admin', 'admin@example.com', '$2y$12$bPWqHSUoWFnTgyJpTAGg3eLgWA2RGgd8/4.ISh13Y0YXTmmjWHqea', 'System Administrator', 'super_admin', NOW());

-- ============================================================
-- Table: students
-- Stores student information
-- ============================================================
DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` VARCHAR(30) NOT NULL,
  `matric_number` VARCHAR(30) NOT NULL,
  `full_name` VARCHAR(150) NOT NULL,
  `department` VARCHAR(100) NOT NULL,
  `faculty` VARCHAR(100) NOT NULL,
  `level` VARCHAR(10) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_id` (`student_id`),
  UNIQUE KEY `matric_number` (`matric_number`),
  KEY `idx_full_name` (`full_name`),
  KEY `idx_matric` (`matric_number`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample students
INSERT INTO `students` (`id`, `student_id`, `matric_number`, `full_name`, `department`, `faculty`, `level`, `email`, `created_at`) VALUES
(1, 'STU001', 'CSC/2020/001', 'John Adeyemi', 'Computer Science', 'Faculty of Science', '400', 'john.adeyemi@student.edu', NOW()),
(2, 'STU002', 'ENG/2019/042', 'Sarah Okafor', 'Electrical Engineering', 'Faculty of Engineering', '500', 'sarah.okafor@student.edu', NOW()),
(3, 'STU003', 'BIO/2021/015', 'David Eze', 'Biochemistry', 'Faculty of Science', '300', 'david.eze@student.edu', NOW());

-- ============================================================
-- Table: transcripts
-- Stores academic transcript records with hashes
-- ============================================================
DROP TABLE IF EXISTS `transcripts`;
CREATE TABLE `transcripts` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `transcript_id` VARCHAR(50) NOT NULL,
  `student_id` INT(11) UNSIGNED NOT NULL,
  `gpa` DECIMAL(3,2) NOT NULL,
  `cgpa` DECIMAL(3,2) NOT NULL,
  `graduation_year` YEAR NOT NULL,
  `degree` VARCHAR(150) NOT NULL,
  `pdf_path` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('pending', 'verified', 'rejected', 'tampered') NOT NULL DEFAULT 'pending',
  `hash` VARCHAR(64) NOT NULL COMMENT 'SHA-256 hash of transcript data',
  `verification_code` VARCHAR(32) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transcript_id` (`transcript_id`),
  UNIQUE KEY `verification_code` (`verification_code`),
  KEY `idx_student_id` (`student_id`),
  KEY `idx_status` (`status`),
  KEY `idx_hash` (`hash`),
  CONSTRAINT `fk_transcript_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: blocks
-- Blockchain storage (simulated chain using hash linkage)
-- ============================================================
DROP TABLE IF EXISTS `blocks`;
CREATE TABLE `blocks` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `block_index` INT(11) NOT NULL COMMENT 'Sequential block number',
  `previous_hash` VARCHAR(64) NOT NULL COMMENT 'Hash of the previous block',
  `current_hash` VARCHAR(64) NOT NULL COMMENT 'SHA-256 hash of this block',
  `nonce` INT(11) NOT NULL DEFAULT 0 COMMENT 'Proof-of-work nonce',
  `transcript_data` TEXT NOT NULL COMMENT 'JSON of transcript data stored in this block',
  `timestamp` BIGINT NOT NULL COMMENT 'UNIX timestamp when block was created',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `block_index` (`block_index`),
  UNIQUE KEY `current_hash` (`current_hash`),
  KEY `idx_previous_hash` (`previous_hash`),
  KEY `idx_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: verification_logs
-- Tracks all transcript verification attempts
-- ============================================================
DROP TABLE IF EXISTS `verification_logs`;
CREATE TABLE `verification_logs` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `transcript_id` INT(11) UNSIGNED DEFAULT NULL,
  `query_value` VARCHAR(100) NOT NULL COMMENT 'The ID or code entered by user',
  `query_type` ENUM('transcript_id', 'verification_code') NOT NULL,
  `status` ENUM('verified', 'tampered', 'not_found') NOT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `verified_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_transcript_id` (`transcript_id`),
  KEY `idx_verified_at` (`verified_at`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_verification_transcript` FOREIGN KEY (`transcript_id`) REFERENCES `transcripts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Table: activity_logs
-- Tracks all user activity in the system
-- ============================================================
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED DEFAULT NULL,
  `action` VARCHAR(50) NOT NULL COMMENT 'e.g., LOGIN, LOGOUT, CREATE_STUDENT',
  `description` TEXT NOT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Sample Activity Logs
-- ============================================================
INSERT INTO `activity_logs` (`user_id`, `action`, `description`, `ip_address`, `created_at`) VALUES
(1, 'LOGIN', 'System Administrator logged in successfully.', '127.0.0.1', NOW());

-- ============================================================
-- Indexes Summary
-- ============================================================
-- users:              username, email (unique + indexed)
-- students:           student_id, matric_number (unique + indexed)
-- transcripts:        transcript_id, verification_code (unique + indexed), hash, status
-- blocks:             block_index (unique), current_hash (unique), previous_hash
-- verification_logs:  transcript_id, verified_at, status
-- activity_logs:      user_id, action, created_at

COMMIT;

-- ============================================================
-- End of SQL Schema
-- ============================================================
