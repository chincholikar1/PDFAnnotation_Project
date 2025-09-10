-- Create Database
CREATE DATABASE IF NOT EXISTS interview_db;
USE interview_db;

-- Table: pdf_uploads
CREATE TABLE IF NOT EXISTS pdf_uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: pdf_annotations
CREATE TABLE IF NOT EXISTS pdf_annotations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    process_id VARCHAR(50) NOT NULL,
    form_id INT NOT NULL,
    field_name VARCHAR(100) NOT NULL,
    field_header VARCHAR(100),
    field_type VARCHAR(50),
    page INT,
    bbox_x1 DOUBLE,
    bbox_y1 DOUBLE,
    bbox_x2 DOUBLE,
    bbox_y2 DOUBLE,
    scale DOUBLE,
    metadata JSON,
    image_w DOUBLE,
    image_h DOUBLE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
