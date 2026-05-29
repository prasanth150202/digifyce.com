-- Creative Dev Extra Tables (hero, stat chips, cta)

CREATE TABLE IF NOT EXISTS creative_hero (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kicker VARCHAR(500) DEFAULT '',
  h1_line1 VARCHAR(255) DEFAULT '',
  h1_line2_accent VARCHAR(255) DEFAULT '',
  hero_sub TEXT,
  btn1_label VARCHAR(255) DEFAULT '',
  btn1_url VARCHAR(500) DEFAULT '',
  btn2_label VARCHAR(255) DEFAULT '',
  btn2_url VARCHAR(500) DEFAULT ''
);

CREATE TABLE IF NOT EXISTS creative_stat_chips (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  chip_num VARCHAR(50) DEFAULT '',
  chip_lbl VARCHAR(255) DEFAULT ''
);

CREATE TABLE IF NOT EXISTS creative_cta (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bg_text VARCHAR(100) DEFAULT '',
  heading VARCHAR(500) DEFAULT '',
  description TEXT,
  btn_label VARCHAR(255) DEFAULT '',
  btn_url VARCHAR(500) DEFAULT ''
);

-- Seed

INSERT IGNORE INTO creative_hero (id, kicker, h1_line1, h1_line2_accent, hero_sub, btn1_label, btn1_url, btn2_label, btn2_url) VALUES
(1,
 'Powerful Creative Solutions That Build Brands and Drive Engagement',
 'Creative Development',
 'Services in India',
 'Creative design is essential for building trust, improving engagement, and driving business growth. At Digifyce, we provide creative development services including graphic design, branding visuals, video editing and AI-powered creatives. We create high-performing visual content that strengthens brand identity, supports marketing goals, and helps your business stand out in the market.',
 'Create Designs That Converts', 'leadform.php',
 'View Services', '#services');

INSERT IGNORE INTO creative_stat_chips (id, sort_order, is_active, chip_num, chip_lbl) VALUES
(1, 1, 1, '8+', 'Service Types'),
(2, 2, 1, '3x', 'Ad Performance'),
(3, 3, 1, 'AI', 'Powered');

INSERT IGNORE INTO creative_cta (id, bg_text, heading, description, btn_label, btn_url) VALUES
(1, 'GROW',
 'Lets Build Creative Assets That Perform.',
 'Your brand deserves more than attractive visuals — it deserves creative assets that drive real growth. At Digifyce, we help businesses create powerful designs and videos that improve visibility, trust, and conversions across every customer touchpoint.',
 'Start Your Creative Journey with Digifyce', 'leadform.php');
