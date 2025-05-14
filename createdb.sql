-- Database for ShareMyRide application

-- Drop database if exists
DROP DATABASE IF EXISTS share_my_ride;

-- Create database
CREATE DATABASE IF NOT EXISTS share_my_ride DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE share_my_ride;

-- Create users table
CREATE TABLE IF NOT EXISTS user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    creation_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Create reclamation table
CREATE TABLE IF NOT EXISTS reclamation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    description TEXT NOT NULL,
    status ENUM('en_cours', 'valider') NOT NULL DEFAULT 'en_cours',
    creation_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
);

-- Create response table
CREATE TABLE IF NOT EXISTS response (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reclamation_id INT NOT NULL,
    description TEXT NOT NULL,
    admin_id INT NOT NULL,
    creation_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reclamation_id) REFERENCES reclamation(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES user(id) ON DELETE CASCADE
);

-- Insert admin user (password: admin123)
INSERT INTO user (firstname, lastname, email, password, role, creation_date) 
VALUES ('Admin', 'User', 'admin@sharemyride.com', '$2y$10$DgzXaPzF6VaJFs90b4jkBuUBJktT5QTI.L.JVZ.6oOUQQrSwjU3Dq', 'admin', NOW());

-- Insert regular user (password: user123)
INSERT INTO user (firstname, lastname, email, password, role, creation_date) 
VALUES ('John', 'Doe', 'john@example.com', '$2y$10$rCnZJnLQn7coWdCqEBJKUeUavuJJE1GEF3dz6xB9YzA.zwezPgCDC', 'user', NOW());

-- Insert another regular user (password: user123)
INSERT INTO user (firstname, lastname, email, password, role, creation_date) 
VALUES ('Jane', 'Smith', 'jane@example.com', '$2y$10$rCnZJnLQn7coWdCqEBJKUeUavuJJE1GEF3dz6xB9YzA.zwezPgCDC', 'user', NOW());

-- Insert sample reclamations
INSERT INTO reclamation (user_id, description, status, creation_date) 
VALUES (2, 'I had an issue with my last ride. The driver was 20 minutes late and didn\'t follow the route I suggested, which resulted in a longer travel time.', 'en_cours', NOW() - INTERVAL 2 DAY);

INSERT INTO reclamation (user_id, description, status, creation_date) 
VALUES (2, 'The app crashed when I was trying to book a ride, causing me to miss my appointment. I would like to request a refund for the inconvenience.', 'valider', NOW() - INTERVAL 5 DAY);

INSERT INTO reclamation (user_id, description, status, creation_date) 
VALUES (3, 'My payment was charged twice for the same ride. I would like a refund for the duplicate charge.', 'valider', NOW() - INTERVAL 7 DAY);

-- Insert sample responses
INSERT INTO response (reclamation_id, description, admin_id, creation_date) 
VALUES (2, 'Dear customer, we apologize for the inconvenience. We have issued a full refund to your account and also added a free ride credit as compensation. The technical issue has been fixed in our latest app update.', 1, NOW() - INTERVAL 3 DAY);

INSERT INTO response (reclamation_id, description, admin_id, creation_date) 
VALUES (3, 'Dear customer, we apologize for the duplicate charge. We have identified the issue and the duplicate amount has been refunded to your account. It should reflect in your statement within 3-5 business days.', 1, NOW() - INTERVAL 6 DAY); 