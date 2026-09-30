CREATE DATABASE IF NOT EXISTS sqldemo;

USE sqldemo;

DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    role VARCHAR(50) NOT NULL
);

INSERT INTO users (username, password, role) VALUES
('admin', 'admin123', 'Administrator'),
('alice', 'alice123', 'User'),
('bob', 'bob123', 'User');
