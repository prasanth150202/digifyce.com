-- schema.sql: Digifyce Dynamic Website & Blog Platform

-- USERS & ROLES & PERMISSIONS
CREATE TABLE permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(128) NOT NULL UNIQUE,
    description TEXT,
    module VARCHAR(64) NOT NULL,
    action VARCHAR(64) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(64) NOT NULL UNIQUE,
    description TEXT,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE role_permissions (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(64) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(128) NOT NULL UNIQUE,
    full_name VARCHAR(255),
    role_id INT NOT NULL,
    status ENUM('active','inactive') DEFAULT 'active',
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
);

-- GLOBAL SITE SETTINGS
CREATE TABLE site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(64) NOT NULL UNIQUE,
    setting_value TEXT
);

-- NAVIGATION & FOOTER
CREATE TABLE navigation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(64) NOT NULL,
    url VARCHAR(255) NOT NULL,
    position INT NOT NULL,
    is_footer TINYINT(1) DEFAULT 0,
    parent_id INT NULL,
    FOREIGN KEY (parent_id) REFERENCES navigation(id) ON DELETE SET NULL
);

-- HERO SECTION
CREATE TABLE hero_section (
    id INT AUTO_INCREMENT PRIMARY KEY,
    headline VARCHAR(255) NOT NULL,
    subtext TEXT,
    cta_label VARCHAR(64),
    cta_url VARCHAR(255)
);

-- TRUSTED BRANDS
CREATE TABLE trusted_brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(64) NOT NULL,
    logo_url VARCHAR(255),
    position INT NOT NULL
);

-- METRICS
CREATE TABLE metrics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(64) NOT NULL,
    value VARCHAR(32) NOT NULL,
    description VARCHAR(128)
);

-- CORE SERVICES
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(128) NOT NULL,
    description TEXT,
    position INT NOT NULL
);

CREATE TABLE service_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    tag VARCHAR(64) NOT NULL,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);

-- BRAND STORIES
CREATE TABLE brand_stories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(128) NOT NULL,
    client_name VARCHAR(128),
    video_url VARCHAR(255) NOT NULL,
    position INT NOT NULL
);

-- METHODOLOGY STEPS
CREATE TABLE methodology_steps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    step_number INT NOT NULL,
    title VARCHAR(128) NOT NULL,
    description TEXT
);

-- STRATEGY MATRIX
CREATE TABLE strategy_matrix (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quadrant CHAR(1) NOT NULL,
    title VARCHAR(128) NOT NULL,
    diagnosis TEXT,
    position INT NOT NULL
);

CREATE TABLE strategy_steps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matrix_id INT NOT NULL,
    step_text TEXT NOT NULL,
    FOREIGN KEY (matrix_id) REFERENCES strategy_matrix(id) ON DELETE CASCADE
);

-- TOOLS & TECHNOLOGY
CREATE TABLE tools_tech (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(64) NOT NULL,
    logo_url VARCHAR(255),
    position INT NOT NULL
);

-- PAGES (Privacy Policy, Terms, etc)
CREATE TABLE pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(128) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- CASE STUDIES
CREATE TABLE case_studies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(128) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    metrics_json TEXT,
    position INT NOT NULL
);

-- BLOG MODULE
CREATE TABLE blog_authors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(128) NOT NULL,
    bio TEXT,
    avatar_url VARCHAR(255)
);

CREATE TABLE blog_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(64) NOT NULL,
    slug VARCHAR(128) NOT NULL UNIQUE
);

CREATE TABLE blog_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(64) NOT NULL,
    slug VARCHAR(128) NOT NULL UNIQUE
);

CREATE TABLE blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT,
    content LONGTEXT,
    featured_image VARCHAR(255),
    author_id INT,
    category_id INT,
    status ENUM('draft','published','scheduled','trashed') DEFAULT 'draft',
    scheduled_at DATETIME,
    published_at DATETIME,
    meta_title VARCHAR(255),
    meta_description VARCHAR(255),
    reading_time INT,
    view_count INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES blog_authors(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL
);

CREATE TABLE blog_tag_map (
    blog_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (blog_id, tag_id),
    FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES blog_tags(id) ON DELETE CASCADE
);

-- LEAD FORMS
-- PDF Email Leads (from Strategy Matrix section)
CREATE TABLE pdf_email_leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    source VARCHAR(64) DEFAULT 'strategy_matrix',
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Main Lead Form
CREATE TABLE lead_form_submissions (
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
CREATE TABLE job_openings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    position INT NOT NULL,
    status ENUM('active','inactive','closed') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE job_applications (
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
