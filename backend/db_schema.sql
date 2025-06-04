-- Create database and tables for graduation system
CREATE DATABASE IF NOT EXISTS graduation_system;
USE graduation_system;

-- Students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_siswa VARCHAR(255) NOT NULL,
    asal_sekolah VARCHAR(255) NOT NULL,
    no_pendaftaran VARCHAR(100) NOT NULL UNIQUE,
    status_kelulusan ENUM('LULUS', 'TIDAK LULUS') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admins table for authentication
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (username: admin, password: admin123)
INSERT INTO admins (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
