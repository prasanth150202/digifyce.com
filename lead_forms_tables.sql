-- Lead Forms Tables
-- Run this to add the new lead form tables to your database

-- PDF Email Leads (from Strategy Matrix section)
CREATE TABLE IF NOT EXISTS pdf_email_leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    source VARCHAR(64) DEFAULT 'strategy_matrix',
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Main Lead Form
CREATE TABLE IF NOT EXISTS lead_form_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(128) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(32),
    company VARCHAR(128),
    budget VARCHAR(32),
    website VARCHAR(255),
    message TEXT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Job Applications
CREATE TABLE IF NOT EXISTS job_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(128) NOT NULL,
    email VARCHAR(255) NOT NULL,
    portfolio_url VARCHAR(255) NOT NULL,
    cover_letter TEXT NOT NULL,
    cv_filename VARCHAR(255),
    cv_filepath VARCHAR(512),
    job_opening_id INT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    status ENUM('pending','reviewed','shortlisted','rejected') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_opening_id) REFERENCES job_openings(id) ON DELETE SET NULL
);
