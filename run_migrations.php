<?php
/**
 * One-time database table installer. Delete this file after running.
 */
require_once __DIR__ . '/config/database.php';

$pdo = Database::getInstance();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = <<<'SQL'

-- ═══════════════════════════════════════════
-- SERVICE TABLES
-- ═══════════════════════════════════════════
CREATE TABLE IF NOT EXISTS service_hero (
  id INT AUTO_INCREMENT PRIMARY KEY,
  badge_text VARCHAR(255) DEFAULT '',
  headline_main VARCHAR(500) DEFAULT '',
  headline_accent VARCHAR(255) DEFAULT '',
  sub_description TEXT,
  btn1_label VARCHAR(100) DEFAULT '',
  btn1_url VARCHAR(500) DEFAULT '',
  btn2_label VARCHAR(100) DEFAULT '',
  btn2_url VARCHAR(500) DEFAULT ''
);
INSERT IGNORE INTO service_hero (id, badge_text, headline_main, headline_accent, sub_description, btn1_label, btn1_url, btn2_label, btn2_url) VALUES
(1,'Full-Service Digital Agency','We Build Brands That','Grow Online','End-to-end digital solutions — from brand identity to performance marketing — designed to drive measurable growth for your business.','Explore Services','#services','Talk to Us','leadform.php');

CREATE TABLE IF NOT EXISTS service_cards (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  icon VARCHAR(100) DEFAULT '',
  title VARCHAR(255) NOT NULL,
  description TEXT,
  link_url VARCHAR(500) DEFAULT ''
);
INSERT IGNORE INTO service_cards (sort_order, is_active, icon, title, description, link_url) VALUES
(1,1,'fa-bullseye','Performance Marketing','Data-driven paid advertising across Google, Meta, and marketplaces to maximise ROI and customer acquisition.','performance-marketing.php'),
(2,1,'fa-palette','Creative Development','Strategic graphic design, video editing, and AI-powered creatives built to improve engagement and drive conversions.','creative-dev.php'),
(3,1,'fa-camera','Commercial Shoot','Professional product photography, ad films, and short-form content that build trust and perform across every platform.','brand-shoot.php'),
(4,1,'fa-layer-group','D2C Brand Building','Complete D2C brand strategy — from identity and positioning to full-funnel growth systems for direct-to-consumer brands.','d2c-branding.php'),
(5,1,'fa-shopping-cart','E-Commerce Development','High-converting Shopify, WooCommerce, and custom e-commerce stores built for performance and scale.','e-com-marketing.php'),
(6,1,'fa-store','Marketplace Management','Full Amazon, Flipkart, and marketplace management — listings, ads, account health, and catalog growth.','market-manage.php'),
(7,1,'fa-pen-nib','Content Marketing','SEO-focused blogs, website copy, social media content, and brand messaging that build authority and drive organic growth.','content-marketing.php'),
(8,1,'fa-microchip','Technology Solutions','Custom web applications, automation tools, and technology integrations that streamline operations and accelerate growth.','technology.php');

-- ═══════════════════════════════════════════
-- TECHNOLOGY TABLES
-- ═══════════════════════════════════════════
CREATE TABLE IF NOT EXISTS tech_hero (
  id INT AUTO_INCREMENT PRIMARY KEY,
  badge_text VARCHAR(255) DEFAULT '',
  headline_main VARCHAR(500) DEFAULT '',
  headline_accent VARCHAR(255) DEFAULT '',
  sub_description TEXT,
  btn1_label VARCHAR(100) DEFAULT '',
  btn1_url VARCHAR(500) DEFAULT '',
  btn2_label VARCHAR(100) DEFAULT '',
  btn2_url VARCHAR(500) DEFAULT ''
);
INSERT IGNORE INTO tech_hero (id, badge_text, headline_main, headline_accent, sub_description, btn1_label, btn1_url, btn2_label, btn2_url) VALUES
(1,'Technology Solutions','Smart Technology Built for','Business Growth','We build custom web applications, automation tools, and integrations that save time, reduce costs, and accelerate growth.','View Solutions','#solutions','Get a Quote','leadform.php');

CREATE TABLE IF NOT EXISTS tech_solutions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  icon VARCHAR(100) DEFAULT '',
  title VARCHAR(255) NOT NULL,
  description TEXT
);
INSERT IGNORE INTO tech_solutions (sort_order, is_active, icon, title, description) VALUES
(1,1,'fa-globe','Custom Web Development','Tailored websites and web applications built to your exact business requirements.'),
(2,1,'fa-robot','Automation & Integrations','Workflow automation and third-party integrations that reduce manual work and increase efficiency.'),
(3,1,'fa-mobile-alt','Mobile-First Solutions','Responsive, performance-optimized digital products that work seamlessly across all devices.'),
(4,1,'fa-shield-alt','Security & Performance','Robust, secure, and fast technology solutions built with best practices and scalability in mind.');

-- ═══════════════════════════════════════════
-- TESTIMONIAL TABLES
-- ═══════════════════════════════════════════
CREATE TABLE IF NOT EXISTS testimonials (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  client_name VARCHAR(255) NOT NULL,
  client_role VARCHAR(255) DEFAULT '',
  client_company VARCHAR(255) DEFAULT '',
  review TEXT,
  rating TINYINT DEFAULT 5,
  avatar_url VARCHAR(500) DEFAULT ''
);
INSERT IGNORE INTO testimonials (sort_order, is_active, client_name, client_role, client_company, review, rating) VALUES
(1,1,'Rajan Mehta','Founder','D2C Brand','Digifyce transformed our brand presence completely. Our social media engagement tripled within 60 days.',5),
(2,1,'Priya Sharma','Marketing Head','E-Commerce Store','The performance marketing team delivered a 4x return on ad spend. Absolutely recommend Digifyce.',5),
(3,1,'Arjun Patel','CEO','Startup','From creative development to marketplace management, the team handled everything professionally.',5);

-- ═══════════════════════════════════════════
-- PRODUCTS TABLES
-- ═══════════════════════════════════════════
CREATE TABLE IF NOT EXISTS product_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  icon VARCHAR(100) DEFAULT ''
);
INSERT IGNORE INTO product_categories (sort_order, is_active, name, description, icon) VALUES
(1,1,'Brand Identity','Logo, brand guidelines, and visual identity systems','fa-paint-brush'),
(2,1,'Marketing Creatives','Social media, ad banners, and campaign creatives','fa-images'),
(3,1,'Video Content','Ad films, reels, and brand storytelling videos','fa-video'),
(4,1,'Web Design','Landing pages, website design, and UI/UX','fa-desktop');

-- ═══════════════════════════════════════════
-- D2C BRANDING TABLES
-- ═══════════════════════════════════════════
CREATE TABLE IF NOT EXISTS d2c_hero (
  id INT AUTO_INCREMENT PRIMARY KEY,
  badge_text VARCHAR(255) DEFAULT '',
  headline_main VARCHAR(500) DEFAULT '',
  headline_accent VARCHAR(255) DEFAULT '',
  sub_description TEXT,
  btn1_label VARCHAR(100) DEFAULT '',
  btn1_url VARCHAR(500) DEFAULT '',
  btn2_label VARCHAR(100) DEFAULT '',
  btn2_url VARCHAR(500) DEFAULT ''
);
INSERT IGNORE INTO d2c_hero (id, badge_text, headline_main, headline_accent, sub_description, btn1_label, btn1_url, btn2_label, btn2_url) VALUES
(1,'D2C Brand Building','Build a Brand That','Sells Directly','Complete D2C brand strategy — from identity and positioning to full-funnel growth systems.','Start Building','leadform.php','See Our Work','#services');

CREATE TABLE IF NOT EXISTS d2c_cta (
  id INT AUTO_INCREMENT PRIMARY KEY,
  heading TEXT,
  subtext TEXT,
  btn_label VARCHAR(100) DEFAULT '',
  btn_url VARCHAR(500) DEFAULT ''
);
INSERT IGNORE INTO d2c_cta (id, heading, subtext, btn_label, btn_url) VALUES
(1,'Ready to Build a\nD2C Brand That Grows?','Let us help you build a brand that connects with customers and drives direct revenue.','Start Your D2C Journey','leadform.php');

CREATE TABLE IF NOT EXISTS d2c_intro_tags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  tag_name VARCHAR(255) NOT NULL
);
INSERT IGNORE INTO d2c_intro_tags (sort_order, is_active, tag_name) VALUES
(1,1,'Brand Strategy'),(2,1,'Visual Identity'),(3,1,'D2C Growth'),(4,1,'Performance Marketing'),(5,1,'Customer Acquisition');

CREATE TABLE IF NOT EXISTS d2c_challenges (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  title VARCHAR(255) NOT NULL,
  description TEXT
);
INSERT IGNORE INTO d2c_challenges (sort_order, is_active, title, description) VALUES
(1,1,'Weak Brand Identity','No clear visual identity means customers cannot remember or trust your brand.'),
(2,1,'Low Online Visibility','Without a strong digital presence, reaching your target audience becomes difficult.'),
(3,1,'Poor Conversion Rates','Traffic without the right brand experience fails to convert into customers.'),
(4,1,'Inconsistent Messaging','Mixed brand communication confuses customers and reduces trust.'),
(5,1,'High Customer Acquisition Cost','Without brand equity, every sale requires heavy advertising investment.');

CREATE TABLE IF NOT EXISTS d2c_pillars (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  number VARCHAR(10) DEFAULT '',
  name VARCHAR(255) NOT NULL,
  text TEXT,
  dots_json TEXT
);
INSERT IGNORE INTO d2c_pillars (sort_order, is_active, number, name, text, dots_json) VALUES
(1,1,'01','Brand Strategy','Define your brand purpose, positioning, and market differentiation strategy.','["Brand Purpose","Target Audience","Unique Positioning","Value Proposition"]'),
(2,1,'02','Visual Identity','Create a cohesive visual system that makes your brand instantly recognizable.','["Logo Design","Color System","Typography","Brand Guidelines"]'),
(3,1,'03','Growth Systems','Build full-funnel marketing systems that drive customer acquisition and retention.','["Performance Marketing","Content Strategy","CRM Setup","Retention Loops"]');

CREATE TABLE IF NOT EXISTS d2c_solutions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  name VARCHAR(255) NOT NULL,
  description TEXT
);
INSERT IGNORE INTO d2c_solutions (sort_order, is_active, name, description) VALUES
(1,1,'Brand Identity Design','Complete logo, color palette, typography, and brand guidelines.'),
(2,1,'Brand Strategy','Positioning, messaging framework, and competitive differentiation.'),
(3,1,'D2C Website Design','Conversion-focused website design built for direct sales.'),
(4,1,'Performance Marketing','Paid advertising systems designed for customer acquisition and ROAS.'),
(5,1,'Content & Creative','Strategic content and creatives aligned with brand identity and campaign goals.');

CREATE TABLE IF NOT EXISTS d2c_steps (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  step_number VARCHAR(10) DEFAULT '',
  title VARCHAR(255) NOT NULL,
  description TEXT
);
INSERT IGNORE INTO d2c_steps (sort_order, is_active, step_number, title, description) VALUES
(1,1,'1','Discovery and Brand Audit','Understand your business, market, competitors, and current brand position.'),
(2,1,'2','Strategy Development','Define brand positioning, target audience, messaging, and growth strategy.'),
(3,1,'3','Visual Identity Creation','Design logo, color system, typography, and complete brand guidelines.'),
(4,1,'4','Digital Asset Development','Create website, social media assets, and marketing collateral.'),
(5,1,'5','Launch and Growth','Execute go-to-market strategy and performance marketing campaigns.');

CREATE TABLE IF NOT EXISTS d2c_metrics (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  value VARCHAR(20) DEFAULT '',
  unit VARCHAR(10) DEFAULT '',
  label VARCHAR(255) DEFAULT '',
  bar_pct INT DEFAULT 100
);
INSERT IGNORE INTO d2c_metrics (sort_order, is_active, value, unit, label, bar_pct) VALUES
(1,1,'3','x','Revenue Growth',75),(2,1,'68','%','Brand Recall',68),(3,1,'4.2','x','ROAS',85),(4,1,'52','%','Lower CAC',52);

CREATE TABLE IF NOT EXISTS d2c_why_features (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  title VARCHAR(255) NOT NULL,
  description TEXT
);
INSERT IGNORE INTO d2c_why_features (sort_order, is_active, title, description) VALUES
(1,1,'Full-Stack Brand Building','We handle strategy, design, content, and performance marketing under one system.'),
(2,1,'D2C Specialization','Deep expertise in direct-to-consumer brands across fashion, beauty, health, and lifestyle.'),
(3,1,'Performance-Oriented Approach','Every brand decision is tied to measurable business outcomes and growth metrics.'),
(4,1,'End-to-End Execution','From brand identity to customer acquisition, we manage the complete brand journey.');

-- ═══════════════════════════════════════════
-- CREATIVE DEVELOPMENT TABLES
-- ═══════════════════════════════════════════
CREATE TABLE IF NOT EXISTS creative_pains (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  text TEXT NOT NULL
);
INSERT IGNORE INTO creative_pains (sort_order, is_active, text) VALUES
(1,1,'Inconsistent brand visuals across platforms'),
(2,1,'Weak social media creatives with low engagement'),
(3,1,'Poor ad creatives leading to low campaign performance'),
(4,1,'Generic design that fails to differentiate the brand'),
(5,1,'Lack of professional video content for marketing'),
(6,1,'Weak brand identity and poor customer recall'),
(7,1,'Creative assets that do not support conversions');

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
INSERT IGNORE INTO creative_pillars (sort_order, is_active, number, name, description, tags_json, svg_path) VALUES
(1,1,'01','Attention','Creating visuals that immediately capture interest and stop scrolling.','["Scroll-Stop Visuals","First-Second Hooks","Attention Triggers","Platform Native"]','M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'),
(2,1,'02','Brand Consistency','Ensuring your brand looks strong, premium, and recognizable across every platform.','["Visual Systems","Brand Alignment","Cross-Platform Unity","Style Guides"]','M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z'),
(3,1,'03','Conversion','Designing creatives that improve engagement, clicks, and customer action.','["CTA Design","Performance Assets","Conversion Copy","A/B Formats"]','M13 7h8m0 0v8m0-8l-8 8-4-4-6 6');

CREATE TABLE IF NOT EXISTS creative_services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  name VARCHAR(255) NOT NULL,
  subtitle VARCHAR(255) DEFAULT '',
  description TEXT,
  bullets_json TEXT
);
INSERT IGNORE INTO creative_services (sort_order, is_active, name, subtitle, description, bullets_json) VALUES
(1,1,'Graphic Design Services','Foundation of visual communication.','Graphic design is the foundation of visual communication. It helps customers understand your brand faster and builds stronger trust across all platforms.','["Strategic graphic design","Brand presence","Marketing effectiveness","Visual communication","Trust-building assets"]'),
(2,1,'Social Media Creatives','Designed for engagement.','Social media is one of the strongest brand-building platforms today, and strong visuals are essential for engagement.','["Instagram post creatives","Carousel designs","Promotional posts","Launch announcements","Product highlight creatives","Reels cover designs","Story creatives","Campaign-based content visuals"]'),
(3,1,'Ad Banners and Performance Creatives','Built for performance.','Paid advertising requires creatives that are built for performance, not just appearance.','["Meta Ads creatives","Google Display banners","Website banner ads","Offer promotion creatives","Product launch banners","Performance marketing creatives"]'),
(4,1,'Marketing Materials','Professional and authoritative.','Strong offline and online marketing materials improve professionalism and brand authority.','["Brochures","Flyers","Catalogs","Pitch decks","Company profiles","Sales presentation materials","Product launch assets"]'),
(5,1,'Website Creatives','Trust and conversion focused.','Website visuals play a major role in customer trust and conversion.','["Homepage banners","Product section visuals","Landing page graphics","Icon systems","Visual content blocks","Promotional website assets"]'),
(6,1,'Brand Visual Development','Consistent brand identity.','Build a cohesive visual identity that is strong, premium, and recognizable across every platform.','["Brand identity guidelines","Color and typography systems","Visual style direction","Asset libraries","Cross-platform templates"]'),
(7,1,'AI-Powered Creative Optimization','Speed and efficiency.','We combine human creativity with AI-powered tools to improve speed, efficiency, and creative performance.','["Workflow acceleration","Versioning at scale","Format optimization","Rapid iteration","Performance-led improvements"]');

CREATE TABLE IF NOT EXISTS creative_steps (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  step_number VARCHAR(10) DEFAULT '',
  title VARCHAR(255) NOT NULL,
  description TEXT
);
INSERT IGNORE INTO creative_steps (sort_order, is_active, step_number, title, description) VALUES
(1,1,'1','Discovery and Requirement Understanding','Understand your brand identity, target audience, business goals, and creative requirements.'),
(2,1,'2','Creative Strategy Planning','Define design direction, messaging approach, creative style, and platform-specific requirements.'),
(3,1,'3','Concept Development','Create concepts aligned with your branding, campaign goals, and customer expectations.'),
(4,1,'4','Design and Production','Execute graphic design, video editing, and creative production with precision.'),
(5,1,'5','Review and Optimization','Refine creatives based on feedback, campaign performance goals, and conversion strategy.'),
(6,1,'6','Final Delivery and Platform Readiness','Deliver optimized assets for websites, ads, marketplaces, and social platforms.');

CREATE TABLE IF NOT EXISTS creative_metrics (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  value VARCHAR(20) DEFAULT '',
  unit VARCHAR(10) DEFAULT '',
  label VARCHAR(255) DEFAULT ''
);
INSERT IGNORE INTO creative_metrics (sort_order, is_active, value, unit, label) VALUES
(1,1,'320','%','Brand Recognition'),(2,1,'58','%','Customer Trust'),(3,1,'4','x','Ad Performance'),(4,1,'99','%','Conversion Rates');

CREATE TABLE IF NOT EXISTS creative_why_cards (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  number VARCHAR(10) DEFAULT '',
  title VARCHAR(255) NOT NULL,
  description TEXT
);
INSERT IGNORE INTO creative_why_cards (sort_order, is_active, number, title, description) VALUES
(1,1,'01','Performance-Focused Creative Strategy','We align visuals with business outcomes and conversion goals.'),
(2,1,'02','D2C and E-commerce Expertise','We understand the platforms, customer psychology, and branding needs for online growth.'),
(3,1,'03','Graphic Design + Video Editing Under One System','Unified creative production ensures brand consistency and faster execution.'),
(4,1,'04','AI-Powered Workflows','Faster execution with smart AI tools without sacrificing quality.'),
(5,1,'05','Brand Consistency Across All Platforms','Your identity stays strong across ads, social, web, and marketing materials.'),
(6,1,'06','End-to-End Support','From concept to final delivery, we manage the full creative system.');

-- ═══════════════════════════════════════════
-- COMMERCIAL SHOOT TABLES
-- ═══════════════════════════════════════════
CREATE TABLE IF NOT EXISTS commercial_shoot_challenges (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  text TEXT NOT NULL
);
INSERT IGNORE INTO commercial_shoot_challenges (sort_order, is_active, text) VALUES
(1,1,'Low-quality product images that reduce credibility.'),
(2,1,'Generic visuals that fail to differentiate the brand.'),
(3,1,'Poor ad creatives leading to weak campaign performance.'),
(4,1,'Inconsistent visual presentation across platforms.'),
(5,1,'Lack of storytelling in product communication.'),
(6,1,'Weak product presentation on websites and marketplaces.');

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
INSERT IGNORE INTO commercial_shoot_services (sort_order, is_active, eyebrow, heading, description, chips_json, img_src) VALUES
(1,1,'Product Photography Services','Clean, sharp, conversion-focused visuals','Product photography is the first impression of your brand.','["E-Commerce Product Shoots","White Background Photography","Lifestyle Product Shoots","Creative Product Styling"]','public/assets/img/service_product_photography.png'),
(2,1,'Ad Shoot and Brand Shoot Services','Campaign-ready storytelling visuals','Videos and storytelling-based visuals are essential for performance marketing.','["Ad Films","Corporate Videos","Video Ad Shoots","Brand Storytelling Shoots"]','public/assets/img/service_ad_brand_shoot.png'),
(3,1,'Social Media Reels and Ad Creatives','Short-form content that stops the scroll','Short-form content is one of the strongest growth drivers today.','["Instagram Reels","Social Media Product Videos","Launch Creatives","Scroll-stopping short-form visuals"]','public/assets/img/service_social_reels.png'),
(4,1,'Influencer-Style Content','Authentic content that feels native','Authentic content performs better than traditional ads in many cases.','["UGC-style content","Creator-format product videos","Native social content","Trust-building customer-style creatives"]','public/assets/img/service_influencer_ugc.png');

CREATE TABLE IF NOT EXISTS commercial_shoot_steps (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  step_number VARCHAR(10) DEFAULT '',
  title VARCHAR(255) NOT NULL,
  description TEXT
);
INSERT IGNORE INTO commercial_shoot_steps (sort_order, is_active, step_number, title, description) VALUES
(1,1,'1','Discovery and Requirement Understanding','We begin by understanding your product, target audience, platform requirements, and campaign objectives.'),
(2,1,'2','Creative Planning and Shoot Strategy','We define the concept, shot list, styling direction, and creative approach before the shoot begins.'),
(3,1,'3','Production and Execution','Our team handles the complete shoot process including setup, lighting, styling, direction, and production management.'),
(4,1,'4','Editing and Post-Production','We professionally edit visuals for clarity, consistency, and platform readiness.'),
(5,1,'5','Delivery and Platform Optimization','Final assets are optimized for websites, ads, marketplaces, and social platforms for immediate use.');

CREATE TABLE IF NOT EXISTS commercial_shoot_impacts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  title VARCHAR(255) NOT NULL,
  description TEXT
);
INSERT IGNORE INTO commercial_shoot_impacts (sort_order, is_active, title, description) VALUES
(1,1,'Higher product trust','Better visuals increase credibility and buyer confidence.'),
(2,1,'Better ad engagement','Creative that is designed to perform improves campaign response.'),
(3,1,'Stronger brand recall','Consistent content improves memory and recognition.'),
(4,1,'Higher conversions','Strong content reduces hesitation and drives action.');

-- ═══════════════════════════════════════════
-- E-COMMERCE MARKETING TABLES
-- ═══════════════════════════════════════════
CREATE TABLE IF NOT EXISTS ecom_challenges (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  icon VARCHAR(100) DEFAULT '',
  title VARCHAR(255) NOT NULL,
  description TEXT
);
INSERT IGNORE INTO ecom_challenges (sort_order, is_active, icon, title, description) VALUES
(1,1,'speed','Slow Loading','Causes immediate drop-offs before users even see your products.'),
(2,1,'smartphone','Poor Mobile UX','Frustrates the largest segment of modern online shoppers.'),
(3,1,'shopping_cart_checkout','Complex Checkout','Leads to massive cart abandonment right at the finish line.'),
(4,1,'security','Low Trust Factors','Weak design makes customers hesitate to enter payment details.'),
(5,1,'api','Limited Integration','Manual operations bottleneck your ability to fulfill and scale.');

CREATE TABLE IF NOT EXISTS ecom_approaches (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  number VARCHAR(10) DEFAULT '',
  title VARCHAR(255) NOT NULL,
  description TEXT,
  tag VARCHAR(100) DEFAULT ''
);
INSERT IGNORE INTO ecom_approaches (sort_order, is_active, number, title, description, tag) VALUES
(1,1,'01','User Experience','A smooth, intuitive shopping journey that helps customers buy faster and with full confidence.','UX Design'),
(2,1,'02','Conversion Optimization','Every page, layout, and checkout step is engineered to lift sales and cut drop-offs.','CRO Strategy'),
(3,1,'03','Scalability','Your store is built to grow alongside your business.','Platform Architecture'),
(4,1,'04','Performance','Fast-loading, technically strong stores that rank well and convert better.','Core Web Vitals');

CREATE TABLE IF NOT EXISTS ecom_steps (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  step_number VARCHAR(10) DEFAULT '',
  title VARCHAR(255) NOT NULL,
  description TEXT
);
INSERT IGNORE INTO ecom_steps (sort_order, is_active, step_number, title, description) VALUES
(1,1,'1','Discovery & Strategy','We begin by understanding your products, target audience, customer journey, and business goals.'),
(2,1,'2','Experience Planning','We define store architecture, UI/UX flow, navigation structure, and conversion-focused customer journey mapping.'),
(3,1,'3','Development & Integration','Our team builds your online store with required features, payment gateways, apps, and third-party integrations.'),
(4,1,'4','Testing & Launch','We test website speed, responsiveness, mobile performance, and user flow to ensure a flawless launch.');

-- ═══════════════════════════════════════════
-- MARKETPLACE MANAGEMENT TABLES
-- ═══════════════════════════════════════════
CREATE TABLE IF NOT EXISTS mktplace_challenges (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  icon VARCHAR(100) DEFAULT '',
  title VARCHAR(255) NOT NULL,
  description TEXT
);
INSERT IGNORE INTO mktplace_challenges (sort_order, is_active, icon, title, description) VALUES
(1,1,'visibility_off','Low Visibility','Poor rankings keep your products hidden from ready-to-buy customers.'),
(2,1,'trending_down','Weak Listings','Low-quality descriptions and unoptimized features kill conversion rates.'),
(3,1,'key','Poor Keywords','Missing buyer-intent search terms limits your product discoverability.'),
(4,1,'image_not_supported','Weak Images','Unprofessional visuals lead to drastically low click-through rates.'),
(5,1,'inventory','Stock Issues','Inventory mismanagement causes stock-outs and drops your marketplace rank.'),
(6,1,'money_off','High Ad Spend','Unoptimized advertising drains budget with a dangerously low return on investment.');

CREATE TABLE IF NOT EXISTS mktplace_steps (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  step_number VARCHAR(10) DEFAULT '',
  title VARCHAR(255) NOT NULL,
  description TEXT,
  icon VARCHAR(100) DEFAULT ''
);
INSERT IGNORE INTO mktplace_steps (sort_order, is_active, step_number, title, description, icon) VALUES
(1,1,'01','Marketplace Audit','Reviewing current listings, account health, competitors, and market opportunities.','analytics'),
(2,1,'02','Listing Strategy','Creating strategy to improve listings, identify keywords, and enhance visibility.','strategy'),
(3,1,'03','Account Setup','Organizing inventory, pricing, order flow, and operational processes for efficiency.','settings_suggest'),
(4,1,'04','Ad Strategy','Launching sponsored ads designed to increase visibility and attract buyers.','ads_click'),
(5,1,'05','Monitoring','Continuously tracking listings, ad campaigns, and sales performance to optimize.','query_stats'),
(6,1,'06','Scaling','Expanding catalogs and scaling operations for sustainable business growth.','trending_up');

-- ═══════════════════════════════════════════
-- CONTENT MARKETING TABLES
-- ═══════════════════════════════════════════
CREATE TABLE IF NOT EXISTS content_solutions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  number VARCHAR(10) DEFAULT '',
  title VARCHAR(255) NOT NULL,
  description TEXT,
  bg_color VARCHAR(20) DEFAULT '#0f172a'
);
INSERT IGNORE INTO content_solutions (sort_order, is_active, number, title, description, bg_color) VALUES
(1,1,'01','Blog Writing','Long-form, SEO-optimised blog content that attracts search traffic, builds authority, and keeps your audience coming back.','#0f172a'),
(2,1,'02','Website Content','Persuasive, on-brand website copy for homepages, service pages, and about sections that convert visitors into leads.','#1e1b4b'),
(3,1,'03','Social Media Content','Platform-native content for Instagram, LinkedIn, and more — built for engagement, recall, and community growth.','#020617'),
(4,1,'04','SEO Content & Landing Pages','Keyword-targeted pages and high-conversion landing pages designed to rank, capture intent, and drive action.','#05070a'),
(5,1,'05','Brand Messaging','Strategic communication frameworks — tone of voice, taglines, and messaging pillars that unify your brand story.','#0f172a'),
(6,1,'06','Email Marketing','Nurture sequences and campaign emails that move subscribers down the funnel and drive repeat conversions.','#1e1b4b');

SQL;

// Strip comment lines first, then split and execute
$lines = explode("\n", $sql);
$cleanLines = array_filter($lines, fn($l) => !preg_match('/^\s*--/', $l));
$cleanSql = implode("\n", $cleanLines);

$statements = array_filter(array_map('trim', explode(';', $cleanSql)));
$ok = 0; $fail = 0; $errors = [];

foreach ($statements as $stmt) {
    if (empty($stmt)) continue;
    try {
        $pdo->exec($stmt);
        $ok++;
    } catch (PDOException $e) {
        $fail++;
        $errors[] = htmlspecialchars($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Database Setup</title>
<style>
  body { font-family: sans-serif; max-width: 700px; margin: 40px auto; padding: 20px; background: #f8fafc; }
  .ok { color: #16a34a; } .fail { color: #dc2626; }
  .box { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px; }
  pre { background: #fef2f2; border-radius: 6px; padding: 12px; font-size: 13px; overflow-x: auto; }
  a { color: #0066ff; }
</style>
</head>
<body>
<div class="box">
  <h2>Database Setup Complete</h2>
  <p class="ok">✔ <?= $ok ?> statements executed successfully.</p>
  <?php if ($fail): ?>
    <p class="fail">✘ <?= $fail ?> statements failed (shown below — usually safe to ignore if table already exists):</p>
    <pre><?= implode("\n", $errors) ?></pre>
  <?php endif; ?>
  <hr>
  <p>All tables are ready. <a href="app/admin/dashboard.php">Go to Admin Dashboard →</a></p>
  <p style="color:#888;font-size:13px;">⚠️ Delete <code>run_migrations.php</code> from your server after this step.</p>
</div>
</body>
</html>
