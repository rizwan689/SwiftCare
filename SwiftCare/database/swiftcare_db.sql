-- SwiftCare Database Schema
-- Run this SQL script in phpMyAdmin or MySQL command line to create the database

-- Create database
CREATE DATABASE IF NOT EXISTS swiftcare_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE swiftcare_db;

-- Users table (patients, doctors, admins)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('patient', 'doctor', 'admin') NOT NULL DEFAULT 'patient',
    phone VARCHAR(20),
    address TEXT,
    specialization VARCHAR(255) DEFAULT NULL COMMENT 'For doctors only',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Schedules table (doctor availability)
CREATE TABLE IF NOT EXISTS schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    available_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_doctor_date (doctor_id, available_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sessions table (appointments/bookings)
CREATE TABLE IF NOT EXISTS sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    patient_id INT NOT NULL,
    schedule_id INT DEFAULT NULL COMMENT 'Reference to schedules table',
    session_date DATE NOT NULL,
    session_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE SET NULL,
    INDEX idx_doctor_date (doctor_id, session_date),
    INDEX idx_patient_date (patient_id, session_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings table (clinic settings)
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clinic_name VARCHAR(255) DEFAULT 'SwiftCare Clinic',
    clinic_phone VARCHAR(20),
    clinic_email VARCHAR(255),
    clinic_address TEXT,
    working_hours VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123)
-- Change this password after first login!
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@swiftcare.com', '$2y$12$G69b/Ia.iotlHb9cDCxN2ezxHSMddt.ZvxXW5uc7VChAK1AdyUPtC', 'admin');

-- Insert default settings
INSERT INTO settings (clinic_name, clinic_phone, clinic_email) VALUES 
('SwiftCare Clinic', '+1-234-567-8900', 'info@swiftcare.com');
