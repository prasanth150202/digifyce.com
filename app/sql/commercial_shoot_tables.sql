-- Commercial Shoot Page Tables

CREATE TABLE IF NOT EXISTS commercial_shoot_challenges (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  text TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS commercial_shoot_services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  eyebrow VARCHAR(255) DEFAULT '',
  heading VARCHAR(255) NOT NULL,
  description TEXT,
  chips_json TEXT,
  img_src VARCHAR(500) DEFAULT ''
);

CREATE TABLE IF NOT EXISTS commercial_shoot_steps (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  step_number VARCHAR(10) DEFAULT '',
  title VARCHAR(255) NOT NULL,
  description TEXT
);

CREATE TABLE IF NOT EXISTS commercial_shoot_impacts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  title VARCHAR(255) NOT NULL,
  description TEXT
);

-- Seed data

INSERT IGNORE INTO commercial_shoot_challenges (id, sort_order, is_active, text) VALUES
(1,1,1,'Low-quality product images that reduce credibility.'),
(2,2,1,'Generic visuals that fail to differentiate the brand.'),
(3,3,1,'Poor ad creatives leading to weak campaign performance.'),
(4,4,1,'Inconsistent visual presentation across platforms.'),
(5,5,1,'Lack of storytelling in product communication.'),
(6,6,1,'Weak product presentation on websites and marketplaces.');

INSERT IGNORE INTO commercial_shoot_services (id, sort_order, is_active, eyebrow, heading, description, chips_json, img_src) VALUES
(1,1,1,'Product Photography Services','Clean, sharp, conversion-focused visuals','Product photography is the first impression of your brand. It directly influences how customers perceive quality and professionalism.','["E-Commerce Product Shoots","White Background Photography","Lifestyle Product Shoots","Creative Product Styling"]','public/assets/img/service_product_photography.png'),
(2,2,1,'Ad Shoot and Brand Shoot Services','Campaign-ready storytelling visuals','Videos and storytelling-based visuals are essential for performance marketing and brand awareness.','["Ad Films","Corporate Videos","Video Ad Shoots","Brand Storytelling Shoots"]','public/assets/img/service_ad_brand_shoot.png'),
(3,3,1,'Social Media Reels and Ad Creatives','Short-form content that stops the scroll','Short-form content is one of the strongest growth drivers today and we produce it for reach, engagement, and conversion.','["Instagram Reels","Social Media Product Videos","Launch Creatives","Scroll-stopping short-form visuals"]','public/assets/img/service_social_reels.png'),
(4,4,1,'Influencer-Style Content','Authentic content that feels native','Authentic content performs better than traditional ads in many cases and helps improve audience connection.','["UGC-style content","Creator-format product videos","Native social content","Trust-building customer-style creatives"]','public/assets/img/service_influencer_ugc.png');

INSERT IGNORE INTO commercial_shoot_steps (id, sort_order, is_active, step_number, title, description) VALUES
(1,1,1,'1','Discovery and Requirement Understanding','We begin by understanding your product, target audience, platform requirements, and campaign objectives.'),
(2,2,1,'2','Creative Planning and Shoot Strategy','We define the concept, shot list, styling direction, and creative approach before the shoot begins.'),
(3,3,1,'3','Production and Execution','Our team handles the complete shoot process including setup, lighting, styling, direction, and production management.'),
(4,4,1,'4','Editing and Post-Production','We professionally edit visuals for clarity, consistency, and platform readiness.'),
(5,5,1,'5','Delivery and Platform Optimization','Final assets are optimized for websites, ads, marketplaces, and social platforms for immediate use.');

INSERT IGNORE INTO commercial_shoot_impacts (id, sort_order, is_active, title, description) VALUES
(1,1,1,'Higher product trust','Better visuals increase credibility and buyer confidence.'),
(2,2,1,'Better ad engagement','Creative that is designed to perform improves campaign response.'),
(3,3,1,'Stronger brand recall','Consistent content improves memory and recognition.'),
(4,4,1,'Higher conversions','Strong content reduces hesitation and drives action.');
