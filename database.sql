-- Create Database
CREATE DATABASE IF NOT EXISTS loyalty_points_system;
USE loyalty_points_system;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(100),
    total_points INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_DATE
);

-- Points transactions
CREATE TABLE IF NOT EXISTS points_transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM('earned', 'redeemed', 'expired') NOT NULL,
    amount INT NOT NULL,
    description VARCHAR(255),
    balance_after INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_DATE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Rewards catalog
CREATE TABLE IF NOT EXISTS rewards (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    points_required INT NOT NULL,
    description TEXT,
    stock INT DEFAULT -1
);
