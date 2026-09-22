CREATE DATABASE IF NOT EXISTS vehicle_service_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vehicle_service_manager;
CREATE TABLE users(id INT AUTO_INCREMENT PRIMARY KEY,username VARCHAR(80) NOT NULL,email VARCHAR(150) NOT NULL UNIQUE,password VARCHAR(255) NOT NULL,role ENUM('customer','admin') NOT NULL DEFAULT 'customer',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE vehicles(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NOT NULL,plate_no VARCHAR(30) NOT NULL,make VARCHAR(60) NOT NULL,model VARCHAR(60) NOT NULL,year INT NOT NULL,mileage INT NOT NULL DEFAULT 0,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE);
CREATE TABLE appointments(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NOT NULL,vehicle_id INT NOT NULL,service_type VARCHAR(100) NOT NULL,appointment_date DATE NOT NULL,notes TEXT,status ENUM('Pending','Confirmed','Rejected','Completed','Cancelled') DEFAULT 'Pending',created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,FOREIGN KEY(vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE);
CREATE TABLE services(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100) NOT NULL UNIQUE,description TEXT NOT NULL,price VARCHAR(50) NOT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE service_records(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NOT NULL,vehicle_id INT NOT NULL,service_type VARCHAR(100) NOT NULL,service_date DATE NOT NULL,cost DECIMAL(10,2) DEFAULT 0,notes TEXT,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,FOREIGN KEY(vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE);
CREATE TABLE messages(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100) NOT NULL,email VARCHAR(150) NOT NULL,message TEXT NOT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
INSERT INTO services(name,description,price) VALUES
('Oil Change','Engine oil and filter replacement.','LKR 8,000+'),
('Full Service','Comprehensive inspection and scheduled maintenance.','LKR 18,000+'),
('Brake Inspection','Brake pads, discs and fluid inspection.','LKR 5,000+'),
('AC Service','AC inspection, cleaning and gas check.','LKR 7,500+'),
('Battery Check','Battery health and charging-system test.','LKR 2,500+'),
('Tyre Service','Pressure, rotation, balancing and alignment.','LKR 4,000+');
-- Admin login: admin@example.com / Admin@123
INSERT INTO users(username,email,password) VALUES('Administrator','admin@example.com','$2y$10$tvDb0gwTh6R0GpgGDDP7T.zJ2BvqkhoHt5eQ6AWmHVt.MWPgmS9VO');