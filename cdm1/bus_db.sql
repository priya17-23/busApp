

-- Create the database
CREATE DATABASE bus_db;
USE bus_db;

-- =======================
-- Student Table
-- =======================                                             
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usn VARCHAR(20) UNIQUE,
    password VARCHAR(255),
    name VARCHAR(100),
    year VARCHAR(10), 
    branch VARCHAR(50),
    stop_name VARCHAR(100),
    bus_no VARCHAR(20),
    photo VARCHAR(255),
    fee_receipt VARCHAR(255),
    pass_generated TINYINT DEFAULT 0
);

-- =======================
-- Driver Table
-- =======================
CREATE TABLE drivers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    name VARCHAR(100),
    phone VARCHAR(15),
    bus_no VARCHAR(20),
    route VARCHAR(100)
);

-- =======================
-- Admin Table
-- =======================
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255)
);

-- =======================
-- Messages Table
-- =======================
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    driver_id INT,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE bus_locations (
  bus_no VARCHAR(10) PRIMARY KEY,
  latitude DOUBLE NOT NULL,
  longitude DOUBLE NOT NULL,
  last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =======================
-- Sample Data
-- =======================
INSERT INTO students (usn,password,name,year,branch,stop_name,bus_no) 
VALUES ('1RV23CS001','1234','Priya','5th','CSE','Kuvempunagar','12');

INSERT INTO drivers (username,password,name,phone,bus_no,route) 
VALUES ('driver1','1234','Ramesh','9876543210','12','Route A');

INSERT INTO admins (username,password) 
VALUES ('admin','1234');

