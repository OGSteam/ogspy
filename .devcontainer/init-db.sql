-- Create OGSpy database with proper charset
CREATE DATABASE IF NOT EXISTS ogspy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user and grant privileges
CREATE USER IF NOT EXISTS 'ogspy_user'@'%' IDENTIFIED BY 'ogspy_password';
GRANT ALL PRIVILEGES ON ogspy.* TO 'ogspy_user'@'%';
FLUSH PRIVILEGES;

-- Switch to ogspy database
USE ogspy;

-- Set charset for current session
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
