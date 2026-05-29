-- schema.sql: Digifyce Dynamic Website & Blog Platform

-- ==========================================
-- USERS, ROLES & PERMISSIONS
-- ==========================================
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

-- ==========================================
-- GLOBAL SITE SETTINGS & NAVIGATION
-- ==========================================
CREATE TABLE site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(64) NOT NULL UNIQUE,
    setting_value TEXT
);

CREATE TABLE navigation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(64) NOT NULL,
    url VARCHAR(255) NOT NULL,
    position INT NOT NULL,
    is_footer TINYINT(1) DEFAULT 0,
    parent_id INT NULL,
    FOREIGN KEY (parent_id) REFERENCES navigation(id) ON DELETE SET NULL
);

-- ==========================================
-- PAGE CONTENT META (NEW MASTER TABLE)
-- Stores dynamic text blocks for any webpage
-- ==========================================
CREATE TABLE page_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_slug VARCHAR(128) NOT NULL,    
    section_key VARCHAR(128) NOT NULL,  
    content LONGTEXT,                   
    UNIQUE KEY unique_page_section (page_slug, section_key)
);

-- STANDARD RICH-TEXT PAGES (Privacy Policy, Terms, etc)
CREATE TABLE pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(128) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ==========================================
-- LIST-BASED DYNAMIC SECTIONS (CRUD DATA)
-- ==========================================
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

-- CASE STUDIES
CREATE TABLE case_studies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(128) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    metrics_json TEXT,
    position INT NOT NULL
);

-- ==========================================
-- BLOG MODULE
-- ==========================================
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

-- ==========================================
-- LEAD FORMS & JOBS
-- ==========================================
CREATE TABLE pdf_email_leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    source VARCHAR(64) DEFAULT 'strategy_matrix',
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

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

-- ==========================================
-- DEFAULT SEED DATA
-- ==========================================

-- Seed default homepage content into the new meta table
INSERT INTO page_content (page_slug, section_key, content) VALUES
-- HERO
('home', 'hero_subtitle', 'Our Vision'),
('home', 'hero_title', 'TRANSFORM <br/><span class="text-white/20">DIGITAL PRESENCE</span> <br/>INTO REVENUE.'),
('home', 'hero_subtext', 'We scale high-growth brands through hyper-precision data and minimalist strategy. No noise, just performance.'),
('home', 'hero_cta_text', 'Get Free Audit'),
('home', 'hero_cta_url', '#'),
('home', 'hero_services_title', 'Select the services you''re interested in'),
('home', 'hero_services_cta_text', 'Get Your Brand Audit'),
('home', 'hero_services_cta_url', '#'),
('home', 'hero_chk_1', 'Brand Kit'),
('home', 'hero_chk_2', 'Package Design'),
('home', 'hero_chk_3', 'E-Commerce Sales'),
('home', 'hero_chk_4', 'E-com / Web Development'),
('home', 'hero_chk_5', 'Market Places(Amazon, Flipkart)'),
('home', 'hero_chk_6', 'End to End Digital Transformation'),
('home', 'hero_chk_7', 'Content Production'),
('home', 'hero_chk_8', 'CRM'),
('home', 'hero_chk_9', 'AI Chatbots'),
('home', 'hero_chk_10', 'Sales Audit & Scaling'),

-- WHO WE ARE
('home', 'who_title', 'Your Trusted Partner for <span class="italic font-light">Your Startup Business.</span>'),
('home', 'who_sub_1', 'Digifyce is more than a marketing agency, we are a strategic growth partner for modern businesses.'),
('home', 'who_sub_2', 'Our expertise in D2C branding helps brands create strong customer connections, improve product perception...'),
('home', 'who_sub_3', 'We work with startups, e-commerce businesses, and growing brands to create performance-driven growth strategies...'),
('home', 'who_cta_text', 'Know More About Us'),
('home', 'who_cta_url', 'about-us.php'),

-- REVENUE (METRICS)
('home', 'rev1_title', '12M'), ('home', 'rev1_sub', 'Revenue — FY2024'),
('home', 'rev2_sub', '↑ +148M vs last year'), ('home', 'rev2_title', '160M'), ('home', 'rev2_sub2', 'Revenue — FY2025'),
('home', 'rev3_title', '13.3X'), ('home', 'rev3_sub', 'Growth'),
('home', 'rev4_title', '82%'), ('home', 'rev4_sub', 'Retention Rate'),
('home', 'rev5_title', '120+'), ('home', 'rev5_sub', 'Brands Served'),

-- STATS GRID (4 Grids)
('home', 'grid1_sub', 'Founded'), ('home', 'grid1_title', '2022'), ('home', 'grid1_text', 'Digifyce was built with one clear vision...'),
('home', 'grid2_sub', 'Brands'), ('home', 'grid2_title', '120+'), ('home', 'grid2_text', 'From branding and creative development...'),
('home', 'grid3_sub', 'Focus'), ('home', 'grid3_title', '360°'), ('home', 'grid3_text', 'Our focus is simple: Build brands that perform.'),
('home', 'grid4_sub', 'Standard'), ('home', 'grid4_title', 'ROI'), ('home', 'grid4_text', 'We are not just a service provider, we are your growth partner.'),

-- METHODOLOGY MATRIX
('home', 'matrix_subtitle', 'Methodology'),
('home', 'matrix_main_title', 'Our Core Methodology'),
('home', 'matrix_qA_title', 'Impulse Zone'), ('home', 'matrix_qA_sub', 'High CTR / High Conv.'),
('home', 'matrix_qA_side_sub1', 'DIAGNOSIS'), ('home', 'matrix_qA_side_title', 'Maximum Efficiency Zone'), ('home', 'matrix_qA_side_text', 'Your creative resonance is perfectly aligned...'), ('home', 'matrix_qA_side_sub2', 'OPTIMIZATION STRATEGY'), ('home', 'matrix_qA_pt1', 'Increase budget horizontally across lookalike segments.'), ('home', 'matrix_qA_pt2', 'Test iterative variations of winning hooks only.'),

('home', 'matrix_qB_title', 'High Intent'), ('home', 'matrix_qB_sub', 'Low CTR / High Conv.'),
('home', 'matrix_qB_side_sub1', 'DIAGNOSIS'), ('home', 'matrix_qB_side_title', 'Trust Barrier Wall'), ('home', 'matrix_qB_side_text', 'Users want the product but face Friction...'), ('home', 'matrix_qB_side_sub2', 'OPTIMIZATION STRATEGY'), ('home', 'matrix_qB_pt1', 'Revamp creative thumbnails and first 3 seconds.'), ('home', 'matrix_qB_pt2', 'A/B test authority-based social proof.'),

('home', 'matrix_qC_title', 'Dead Space'), ('home', 'matrix_qC_sub', 'Low CTR / Low Conv.'),
('home', 'matrix_qC_side_sub1', 'DIAGNOSIS'), ('home', 'matrix_qC_side_title', 'Strategic Exit Point'), ('home', 'matrix_qC_side_text', 'Market mismatch. Neither the message nor the offer is sticking...'), ('home', 'matrix_qC_side_sub2', 'ACTION PLAN'), ('home', 'matrix_qC_pt1', 'Pause all active sets immediately.'), ('home', 'matrix_qC_pt2', 'Re-evaluate product-market fit.'),

('home', 'matrix_qD_title', 'Click Magnet'), ('home', 'matrix_qD_sub', 'High CTR / Low Conv.'),
('home', 'matrix_qD_side_sub1', 'DIAGNOSIS'), ('home', 'matrix_qD_side_title', 'Engagement Trap'), ('home', 'matrix_qD_side_text', 'Clickbait or high curiosity but low intent...'), ('home', 'matrix_qD_side_sub2', 'OPTIMIZATION STRATEGY'), ('home', 'matrix_qD_pt1', 'Align ad creative closer to actual offer.'), ('home', 'matrix_qD_pt2', 'Implement post-click educational funnels.'),

-- OUR STACK
('home', 'stack_title', 'Tools We Use'),

-- ACCORDION SERVICES
('home', 'serv_sub', 'Our services are built to support every stage of your brand journey.'),
('home', 'serv_title', 'Complete Startup Growth and Martech Solutions'),
('home', 'serv1_title', 'D2C Branding Services'), ('home', 'serv1_text', 'Build strong brand identity...'), ('home', 'serv1_cta', 'Build Your Brand'), ('home', 'serv1_url', 'leadform.php'),
('home', 'serv2_title', 'Commercial Shoot Services'), ('home', 'serv2_text', 'Professional product photography...'), ('home', 'serv2_cta', 'Make Your Brand Stand Out'), ('home', 'serv2_url', 'leadform.php'),
('home', 'serv3_title', 'Performance Marketing'), ('home', 'serv3_text', 'Meta Ads, Google Ads, SEO...'), ('home', 'serv3_cta', 'Scale Your Business'), ('home', 'serv3_url', 'leadform.php'),
('home', 'serv4_title', 'E-Commerce Development'), ('home', 'serv4_text', 'Shopify, WooCommerce...'), ('home', 'serv4_cta', 'Build a Store That Sells'), ('home', 'serv4_url', 'leadform.php'),
('home', 'serv5_title', 'Marketplace Management'), ('home', 'serv5_text', 'Amazon and Flipkart optimization...'), ('home', 'serv5_cta', 'Grow Your Sales'), ('home', 'serv5_url', 'leadform.php'),
('home', 'serv6_title', 'Content Marketing'), ('home', 'serv6_text', 'SEO-friendly content...'), ('home', 'serv6_cta', 'Create Content'), ('home', 'serv6_url', 'leadform.php'),
('home', 'serv7_title', 'Creative Development'), ('home', 'serv7_text', 'Graphic design, ad creatives...'), ('home', 'serv7_cta', 'Create Visuals'), ('home', 'serv7_url', 'leadform.php'),

-- PROCESS
('home', 'proc_title', 'Our 5-Step Architecture.'),
('home', 'proc_sub', 'As a top digital marketing agency in India, we follow a structured...'),
('home', 'proc1_sub', '01. DISCOVERY'), ('home', 'proc1_title', 'Uncovering Hidden Data'), ('home', 'proc1_text', 'We analyze real business data...'),
('home', 'proc2_sub', '02. STRATEGY'), ('home', 'proc2_title', 'The Growth Roadmap'), ('home', 'proc2_text', 'We create a customized 90-day roadmap...'),
('home', 'proc3_sub', '03. EXECUTION'), ('home', 'proc3_title', 'Agile Implementation'), ('home', 'proc3_text', 'Our team executes high-performance campaigns...'),
('home', 'proc4_sub', '04. OPTIMIZATION'), ('home', 'proc4_title', 'Iterative Precision'), ('home', 'proc4_text', 'We continuously monitor and improve...'),
('home', 'proc5_sub', '05. REPORTING'), ('home', 'proc5_title', 'Full Transparency'), ('home', 'proc5_text', 'We provide complete transparency through live dashboards...'),

-- PRESS & CASE STUDY
('home', 'press_title', 'We Got Recognized In'),
('home', 'case_title', 'Scaling Wellness to 3.5X ROAS.'),
('home', 'case_sub', 'As a results-driven e-commerce marketing agency...'),
('home', 'case_rev1_val', '+340%'), ('home', 'case_rev1_sub', 'Revenue'),
('home', 'case_rev2_val', '-38%'), ('home', 'case_rev2_sub', 'CPA Reduction'),
('home', 'case_img_path', 'public/assets/img/graph.png'),

-- WHY CHOOSE US
('home', 'why_title', 'Why Brands Choose Digifyce'),
('home', 'why_sub', 'At Digifyce, we believe successful D2C branding requires more than attractive visuals...'),
('home', 'why1_head', 'strategy'), ('home', 'why1_title', 'Brand-First Growth Strategy'), ('home', 'why1_text', 'We build brands with clear positioning...'),
('home', 'why2_head', 'execution'), ('home', 'why2_title', 'Performance-Driven Execution'), ('home', 'why2_text', 'Every branding and marketing decision...'),
('home', 'why3_head', 'support'), ('home', 'why3_title', 'Complete Growth Support'), ('home', 'why3_text', 'From brand launch to scaling revenue...'),
('home', 'why4_head', 'execution'), ('home', 'why4_title', 'Industry-Focused Expertise'), ('home', 'why4_text', 'We understand the challenges faced by D2C brands...'),

-- LAST SECTION
('home', 'last_title', 'READY TO <br /><span class="text-white/20">SCALE?</span>'),
('home', 'last_cta_text', 'Book Your Audit'),
('home', 'last_subtext', 'Only 3 slots remaining for Q4');