-- Database tables for user dashboard functionality
-- Run this SQL to create the required tables

-- Table for user submitted papers
CREATE TABLE IF NOT EXISTS `user_papers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `abstract` text,
  `keywords` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'under-review',
  `submitted_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `reviewed_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for user library (bookmarked articles and videos)
CREATE TABLE IF NOT EXISTS `user_library` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `resource_url` varchar(500) DEFAULT NULL,
  `resource_type` varchar(50) DEFAULT NULL,
  `resource_id` varchar(100) DEFAULT NULL,
  `saved_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `resource_type` (`resource_type`),
  UNIQUE KEY `unique_bookmark` (`user_id`, `resource_type`, `resource_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add profile_picture and bio columns to users table if they don't exist
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `profile_picture` varchar(500) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `bio` text DEFAULT NULL;
