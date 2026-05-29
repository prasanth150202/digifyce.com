-- Creative Development Page Tables

CREATE TABLE IF NOT EXISTS creative_pains (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  text TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS creative_pillars (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  number VARCHAR(10) DEFAULT '',
  name VARCHAR(255) NOT NULL,
  description TEXT,
  tags_json TEXT,
  svg_path TEXT
);

CREATE TABLE IF NOT EXISTS creative_services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  name VARCHAR(255) NOT NULL,
  subtitle VARCHAR(255) DEFAULT '',
  description TEXT,
  bullets_json TEXT
);

CREATE TABLE IF NOT EXISTS creative_steps (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  step_number VARCHAR(10) DEFAULT '',
  title VARCHAR(255) NOT NULL,
  description TEXT
);

CREATE TABLE IF NOT EXISTS creative_metrics (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  value VARCHAR(20) DEFAULT '',
  unit VARCHAR(10) DEFAULT '',
  label VARCHAR(255) DEFAULT ''
);

CREATE TABLE IF NOT EXISTS creative_why_cards (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  number VARCHAR(10) DEFAULT '',
  title VARCHAR(255) NOT NULL,
  description TEXT
);

-- Seed data

INSERT IGNORE INTO creative_pains (id, sort_order, is_active, text) VALUES
(1,1,1,'Inconsistent brand visuals across platforms'),
(2,2,1,'Weak social media creatives with low engagement'),
(3,3,1,'Poor ad creatives leading to low campaign performance'),
(4,4,1,'Generic design that fails to differentiate the brand'),
(5,5,1,'Lack of professional video content for marketing'),
(6,6,1,'Weak brand identity and poor customer recall'),
(7,7,1,'Creative assets that do not support conversions');

INSERT IGNORE INTO creative_pillars (id, sort_order, is_active, number, name, description, tags_json, svg_path) VALUES
(1,1,1,'01','Attention','Creating visuals that immediately capture interest and stop scrolling.','["Scroll-Stop Visuals","First-Second Hooks","Attention Triggers","Platform Native"]','M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'),
(2,2,1,'02','Brand Consistency','Ensuring your brand looks strong, premium, and recognizable across every platform.','["Visual Systems","Brand Alignment","Cross-Platform Unity","Style Guides"]','M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z'),
(3,3,1,'03','Conversion','Designing creatives that improve engagement, clicks, and customer action.','["CTA Design","Performance Assets","Conversion Copy","A/B Formats"]','M13 7h8m0 0v8m0-8l-8 8-4-4-6 6');

INSERT IGNORE INTO creative_services (id, sort_order, is_active, name, subtitle, description, bullets_json) VALUES
(1,1,1,'Graphic Design Services','Foundation of visual communication.','Graphic design is the foundation of visual communication. It helps customers understand your brand faster and builds stronger trust across all platforms.','["Strategic graphic design","Brand presence","Marketing effectiveness","Visual communication","Trust-building assets"]'),
(2,2,1,'Social Media Creatives','Designed for engagement.','Social media is one of the strongest brand-building platforms today, and strong visuals are essential for engagement.','["Instagram post creatives","Carousel designs","Promotional posts","Launch announcements","Product highlight creatives","Reels cover designs","Story creatives","Campaign-based content visuals"]'),
(3,3,1,'Ad Banners and Performance Creatives','Built for performance.','Paid advertising requires creatives that are built for performance, not just appearance.','["Meta Ads creatives","Google Display banners","Website banner ads","Offer promotion creatives","Product launch banners","Performance marketing creatives"]'),
(4,4,1,'Marketing Materials','Professional and authoritative.','Strong offline and online marketing materials improve professionalism and brand authority.','["Brochures","Flyers","Catalogs","Pitch decks","Company profiles","Sales presentation materials","Product launch assets"]'),
(5,5,1,'Website Creatives','Trust and conversion focused.','Website visuals play a major role in customer trust and conversion.','["Homepage banners","Product section visuals","Landing page graphics","Icon systems","Visual content blocks","Promotional website assets"]'),
(6,6,1,'Brand Visual Development','Consistent brand identity.','Build a cohesive visual identity that is strong, premium, and recognizable across every platform.','["Brand identity guidelines","Color and typography systems","Visual style direction","Asset libraries","Cross-platform templates"]'),
(7,7,1,'AI-Powered Creative Optimization','Speed and efficiency.','We combine human creativity with AI-powered tools to improve speed, efficiency, and creative performance.','["Workflow acceleration","Versioning at scale","Format optimization","Rapid iteration","Performance-led improvements"]');

INSERT IGNORE INTO creative_steps (id, sort_order, is_active, step_number, title, description) VALUES
(1,1,1,'1','Discovery and Requirement Understanding','Understand your brand identity, target audience, business goals, and creative requirements.'),
(2,2,1,'2','Creative Strategy Planning','Define design direction, messaging approach, creative style, and platform-specific requirements.'),
(3,3,1,'3','Concept Development','Create concepts aligned with your branding, campaign goals, and customer expectations.'),
(4,4,1,'4','Design and Production','Execute graphic design, video editing, and creative production with precision.'),
(5,5,1,'5','Review and Optimization','Refine creatives based on feedback, campaign performance goals, and conversion strategy.'),
(6,6,1,'6','Final Delivery and Platform Readiness','Deliver optimized assets for websites, ads, marketplaces, and social platforms.');

INSERT IGNORE INTO creative_metrics (id, sort_order, is_active, value, unit, label) VALUES
(1,1,1,'320','%','Brand Recognition'),
(2,2,1,'58','%','Customer Trust'),
(3,3,1,'4','x','Ad Performance'),
(4,4,1,'99','%','Conversion Rates');

INSERT IGNORE INTO creative_why_cards (id, sort_order, is_active, number, title, description) VALUES
(1,1,1,'01','Performance-Focused Creative Strategy','We align visuals with business outcomes and conversion goals.'),
(2,2,1,'02','D2C and E-commerce Expertise','We understand the platforms, customer psychology, and branding needs for online growth.'),
(3,3,1,'03','Graphic Design + Video Editing Under One System','Unified creative production ensures brand consistency and faster execution.'),
(4,4,1,'04','AI-Powered Workflows','Faster execution with smart AI tools without sacrificing quality.'),
(5,5,1,'05','Brand Consistency Across All Platforms','Your identity stays strong across ads, social, web, and marketing materials.'),
(6,6,1,'06','End-to-End Support','From concept to final delivery, we manage the full creative system.');
