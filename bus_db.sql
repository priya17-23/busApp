CREATE DATABASE IF NOT EXISTS bus_db;
USE bus_db;

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usn VARCHAR(20) UNIQUE,
    password VARCHAR(255),
    name VARCHAR(100),
    photo VARCHAR(255),
    fee_receipt VARCHAR(255),
    pass_generated TINYINT DEFAULT 0
);

CREATE TABLE drivers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    route VARCHAR(100)
);

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255)
);

CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    driver_id INT,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample Data
INSERT INTO students (usn,password,name) VALUES ('1RV23CS001','1234','Chethana');
INSERT INTO students (usn,password,name) VALUES ('1RV23CS001','1234','Chethana');
INSERT INTO drivers (username,password,route) VALUES ('driver1','1234','Route A');
INSERT INTO admins (username,password) VALUES ('admin','1234');