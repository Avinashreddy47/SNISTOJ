-- SNISTOJ Database Schema
-- Execute this file to set up the databases and tables

-- ============================================
-- Users Database (vlabreg)
-- ============================================

CREATE DATABASE IF NOT EXISTS vlabreg;
USE vlabreg;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    avatar_url VARCHAR(255),
    bio TEXT,
    role ENUM('user', 'admin', 'moderator') DEFAULT 'user',
    status ENUM('active', 'suspended', 'banned') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- Sessions table
CREATE TABLE IF NOT EXISTS sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_token VARCHAR(255) UNIQUE NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    expires_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_expires (expires_at)
);

-- Audit log table
CREATE TABLE IF NOT EXISTS audit_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
);

-- ============================================
-- Problems Database (vlabproblem)
-- ============================================

CREATE DATABASE IF NOT EXISTS vlabproblem;
USE vlabproblem;

-- Problems table
CREATE TABLE IF NOT EXISTS problems (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description LONGTEXT NOT NULL,
    difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'medium',
    category VARCHAR(50),
    time_limit INT DEFAULT 1,
    memory_limit INT DEFAULT 256,
    input_format LONGTEXT,
    output_format LONGTEXT,
    examples LONGTEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_difficulty (difficulty),
    INDEX idx_category (category),
    INDEX idx_created_by (created_by)
);

-- Test cases table
CREATE TABLE IF NOT EXISTS test_cases (
    id INT PRIMARY KEY AUTO_INCREMENT,
    problem_id INT NOT NULL,
    input LONGTEXT NOT NULL,
    expected_output LONGTEXT NOT NULL,
    is_example BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (problem_id) REFERENCES problems(id) ON DELETE CASCADE,
    INDEX idx_problem_id (problem_id),
    INDEX idx_is_example (is_example)
);

-- Submissions table
CREATE TABLE IF NOT EXISTS submissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    problem_id INT NOT NULL,
    user_id INT NOT NULL,
    code LONGTEXT NOT NULL,
    language VARCHAR(20) NOT NULL,
    status ENUM('pending', 'accepted', 'wrong_answer', 'runtime_error', 'time_limit_exceeded', 'memory_limit_exceeded', 'compilation_error') DEFAULT 'pending',
    execution_time FLOAT,
    memory_used INT,
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (problem_id) REFERENCES problems(id) ON DELETE CASCADE,
    INDEX idx_problem_id (problem_id),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Contests table
CREATE TABLE IF NOT EXISTS contests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description LONGTEXT,
    start_time TIMESTAMP,
    end_time TIMESTAMP,
    duration INT,
    status ENUM('scheduled', 'running', 'ended') DEFAULT 'scheduled',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_start_time (start_time)
);

-- Contest problems table
CREATE TABLE IF NOT EXISTS contest_problems (
    id INT PRIMARY KEY AUTO_INCREMENT,
    contest_id INT NOT NULL,
    problem_id INT NOT NULL,
    points INT DEFAULT 100,
    FOREIGN KEY (contest_id) REFERENCES contests(id) ON DELETE CASCADE,
    FOREIGN KEY (problem_id) REFERENCES problems(id) ON DELETE CASCADE,
    UNIQUE KEY unique_contest_problem (contest_id, problem_id)
);

-- Contest submissions table
CREATE TABLE IF NOT EXISTS contest_submissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    contest_id INT NOT NULL,
    problem_id INT NOT NULL,
    user_id INT NOT NULL,
    code LONGTEXT NOT NULL,
    language VARCHAR(20) NOT NULL,
    status ENUM('pending', 'accepted', 'wrong_answer', 'runtime_error', 'time_limit_exceeded', 'memory_limit_exceeded', 'compilation_error') DEFAULT 'pending',
    points INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contest_id) REFERENCES contests(id) ON DELETE CASCADE,
    FOREIGN KEY (problem_id) REFERENCES problems(id) ON DELETE CASCADE,
    INDEX idx_contest_id (contest_id),
    INDEX idx_user_id (user_id)
);

-- Contest standings table
CREATE TABLE IF NOT EXISTS contest_standings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    contest_id INT NOT NULL,
    user_id INT NOT NULL,
    rank INT,
    total_points INT DEFAULT 0,
    solved_problems INT DEFAULT 0,
    FOREIGN KEY (contest_id) REFERENCES contests(id) ON DELETE CASCADE,
    UNIQUE KEY unique_contest_user (contest_id, user_id)
);

-- User statistics table
CREATE TABLE IF NOT EXISTS user_statistics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    total_submissions INT DEFAULT 0,
    accepted_submissions INT DEFAULT 0,
    wrong_answer INT DEFAULT 0,
    runtime_error INT DEFAULT 0,
    time_limit_exceeded INT DEFAULT 0,
    compilation_error INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create indexes for performance
CREATE INDEX idx_submissions_user_problem ON submissions(user_id, problem_id);
CREATE INDEX idx_contest_submissions_user ON contest_submissions(contest_id, user_id);

-- Insert sample admin user (password: admin123)
INSERT INTO users (username, email, password, full_name, role) VALUES
('admin', 'admin@snistoj.com', '$2y$12$rZ7g1jQYvXZ8K5K7L5K5K5K5K5K5K5K5K5K5K5K5K5K5K5K5K5K5K.', 'Administrator', 'admin');

-- Insert sample problems
INSERT INTO problems (title, description, difficulty, category, created_by) VALUES
('Hello World', 'Print "Hello World" on a single line.', 'easy', 'basic', 1),
('Simple Sum', 'Read two integers and print their sum.', 'easy', 'basic', 1),
('Fibonacci Series', 'Print the first N Fibonacci numbers.', 'medium', 'math', 1);

COMMIT;
