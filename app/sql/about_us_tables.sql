-- About Us Page Tables

CREATE TABLE IF NOT EXISTS about_hero (
  id INT AUTO_INCREMENT PRIMARY KEY,
  h1_line1 VARCHAR(255) DEFAULT '',
  h1_line2_accent VARCHAR(255) DEFAULT '',
  h1_line3 VARCHAR(255) DEFAULT '',
  subtext TEXT,
  btn1_label VARCHAR(255) DEFAULT '',
  btn1_url VARCHAR(500) DEFAULT '',
  btn2_label VARCHAR(255) DEFAULT '',
  btn2_url VARCHAR(500) DEFAULT ''
);

CREATE TABLE IF NOT EXISTS about_hero_stats (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  badge VARCHAR(100) DEFAULT '',
  number VARCHAR(50) DEFAULT '',
  description TEXT
);

CREATE TABLE IF NOT EXISTS about_section_headers (
  slug VARCHAR(50) PRIMARY KEY,
  eyebrow VARCHAR(255) DEFAULT '',
  heading TEXT,
  sub_text TEXT,
  extra_text TEXT,
  btn_label VARCHAR(255) DEFAULT '',
  btn_url VARCHAR(500) DEFAULT ''
);

CREATE TABLE IF NOT EXISTS about_why_cards (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  card_number VARCHAR(20) DEFAULT '',
  body_text TEXT
);

CREATE TABLE IF NOT EXISTS about_who_sub_cards (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  badge VARCHAR(100) DEFAULT '',
  text TEXT
);

CREATE TABLE IF NOT EXISTS about_what_we_do (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  number VARCHAR(10) DEFAULT '',
  title VARCHAR(255) DEFAULT '',
  description TEXT
);

CREATE TABLE IF NOT EXISTS about_approach_pillars (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  number VARCHAR(10) DEFAULT '',
  badge VARCHAR(100) DEFAULT '',
  title VARCHAR(100) DEFAULT '',
  description TEXT
);

CREATE TABLE IF NOT EXISTS about_why_digi_cards (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  badge VARCHAR(100) DEFAULT '',
  title VARCHAR(100) DEFAULT '',
  description TEXT
);

CREATE TABLE IF NOT EXISTS about_mission_vision (
  id INT AUTO_INCREMENT PRIMARY KEY,
  mission_badge VARCHAR(100) DEFAULT '',
  mission_title VARCHAR(255) DEFAULT '',
  mission_text TEXT,
  vision_badge VARCHAR(100) DEFAULT '',
  vision_title VARCHAR(255) DEFAULT '',
  vision_text TEXT
);

CREATE TABLE IF NOT EXISTS about_cta (
  id INT AUTO_INCREMENT PRIMARY KEY,
  badge VARCHAR(255) DEFAULT '',
  heading VARCHAR(500) DEFAULT '',
  description TEXT,
  btn_label VARCHAR(255) DEFAULT '',
  btn_url VARCHAR(500) DEFAULT ''
);

-- Seed about_hero
INSERT IGNORE INTO about_hero (id, h1_line1, h1_line2_accent, h1_line3, subtext, btn1_label, btn1_url, btn2_label, btn2_url) VALUES
(1, 'Building Brands That', 'Perform', 'Scale and Lead',
 'In today\'s fast-moving digital world, building a successful brand requires more than good design or marketing, it requires strategy, consistency, creativity and performance. At Digifyce, we help businesses create strong brand foundations and scalable growth systems that drive real business results.',
 'Know More', '#values', 'Connect Today', 'leadform.php');

-- Seed about_hero_stats
INSERT IGNORE INTO about_hero_stats (id, sort_order, is_active, badge, number, description) VALUES
(1, 1, 1, 'Founded', '2022', 'Digifyce was built with one clear vision: to help modern brands grow with purpose, precision, and performance. We work with D2C brands, e-commerce businesses, startups, and growing companies that want to create stronger customer connections, improve conversions, and build long-term brand value.'),
(2, 2, 1, 'Brands', '120+', 'From branding and creative development to performance marketing, e-commerce growth, commercial shoots, content marketing, and marketplace management, we provide complete solutions that help businesses launch, scale, and dominate their digital presence.'),
(3, 3, 1, 'Focus', '360°', 'Our focus is simple: Build brands that do not just look good but perform better.'),
(4, 4, 1, 'Standard', 'ROI', 'We are not just a service provider, we are your growth partner.');

-- Seed about_section_headers
INSERT IGNORE INTO about_section_headers (slug, eyebrow, heading, sub_text, extra_text, btn_label, btn_url) VALUES
('why_brands', 'Choose Digifyce', 'Why Brands Choose Digifyce', '', '', 'Work With Us Today', 'leadform.php'),
('who_we_are', 'Who We Are', 'We are focused on clarity, execution, and growth.', 'We believe branding is not only about visuals and performance marketing is not only about running ads. Real growth happens when strategy, creativity, customer psychology, and data work together. That is where we come in.', '', '', ''),
('what_we_do', 'What We Do', 'Complete growth solutions for ambitious businesses.', '', '', 'Helping your business grow stronger and faster', '#our-approach'),
('approach', 'Our Approach', 'We believe growth should be strategic, not random.', 'That is why every project begins with understanding your business, your audience, and your long-term goals. We do not believe in one-size-fits-all solutions. Every strategy we create is customized to your brand\'s needs, market position, and growth stage. Our approach is built on three strong pillars:', 'This balance helps brands move from confusion to clarity and from effort to results.', '', ''),
('why_digi', 'Why Brands Choose Digifyce', 'What makes us different', '', '', '', '');

-- Seed about_why_cards
INSERT IGNORE INTO about_why_cards (id, sort_order, is_active, card_number, body_text) VALUES
(1, 1, 1, 'SYSTEM / 01', 'Brands choose Digifyce because modern business growth requires more than basic marketing. We combine strategy, creativity, performance, and technology-based solutions to help brands build strong digital foundations and achieve scalable growth. Our focus is not just on visibility, but on creating systems that improve conversions, strengthen brand identity, and drive long-term business success.'),
(2, 2, 1, 'SYSTEM / 02', 'At Digifyce, we understand the challenges brands face in competitive digital markets, from poor ad performance to slow e-commerce growth and weak customer retention. By combining branding, content, paid marketing, design, and technology under one growth-driven approach, we create solutions that deliver measurable results, profitability, and sustainable brand growth.');

-- Seed about_who_sub_cards
INSERT IGNORE INTO about_who_sub_cards (id, sort_order, is_active, badge, text) VALUES
(1, 1, 1, 'WHO WE ARE', 'A strategic branding and growth agency built for businesses that want measurable success not just marketing activity.'),
(2, 2, 1, 'OUR BELIEF', 'Branding is not only about visuals and performance marketing is not only about running ads. Real growth happens when strategy, creativity, customer psychology, and data work together.'),
(3, 3, 1, 'OUR FOCUS', 'We help businesses identify growth opportunities, create strong brand positioning, and build systems that generate consistent revenue.');

-- Seed about_what_we_do
INSERT IGNORE INTO about_what_we_do (id, sort_order, is_active, number, title, description) VALUES
(1, 1, 1, '01', 'Strategy', 'At Digifyce, we help brands build strong digital foundations through strategy, creativity, and performance-driven solutions.'),
(2, 2, 1, '02', 'Services', 'Our services include D2C branding, commercial shoots, performance marketing, e-commerce development, marketplace management, content marketing, and creative development.'),
(3, 3, 1, '03', 'Building Brands', 'We believe successful brands need more than marketing — they need the right strategy, clear positioning, and consistent execution.'),
(4, 4, 1, '04', 'E-commerce Development', 'We work with D2C brands, e-commerce businesses, startups, and growing companies to create better customer experiences, stronger brand identity, and scalable systems that support long-term business growth and success.'),
(5, 5, 1, '05', 'Marketplace Management', 'Every service is designed to improve visibility, increase conversions, strengthen customer trust, and help businesses grow faster in today\'s competitive digital market with confidence.'),
(6, 6, 1, '06', 'Content Marketing', 'At Digifyce, we focus on building brands that not only look good but also perform better, generate results, and create lasting value for long-term business growth.');

-- Seed about_approach_pillars
INSERT IGNORE INTO about_approach_pillars (id, sort_order, is_active, number, badge, title, description) VALUES
(1, 1, 1, '1', 'THE RIGHT ROADMAP', 'Strategy', 'We identify the right direction, positioning, and growth roadmap for your business.'),
(2, 2, 1, '2', 'TRUSTWORTHY VISUALS', 'Creativity', 'We create visuals, messaging, and experiences that make your brand memorable and trustworthy.'),
(3, 3, 1, '3', 'MEASURABLE OUTCOMES', 'Performance', 'We focus on measurable outcomes, better visibility, stronger conversions and sustainable growth.');

-- Seed about_why_digi_cards
INSERT IGNORE INTO about_why_digi_cards (id, sort_order, is_active, badge, title, description) VALUES
(1, 1, 1, 'D2C-FOCUSED EXPERTISE', 'Built for D2C', 'We understand how digital-first brands grow in highly competitive markets.'),
(2, 2, 1, 'STRATEGY + CREATIVITY + PERFORMANCE', 'One System', 'We combine branding, design, marketing, and growth under one unified system.'),
(3, 3, 1, 'ROI-DRIVEN EXECUTION', 'Performance First', 'Every decision is made with performance and profitability in mind.'),
(4, 4, 1, 'END-TO-END SUPPORT', 'Full Journey', 'From launching your brand to scaling your business, we support you at every step.');

-- Seed about_mission_vision
INSERT IGNORE INTO about_mission_vision (id, mission_badge, mission_title, mission_text, vision_badge, vision_title, vision_text) VALUES
(1, 'OUR MISSION', 'Meaningful, profitable and sustainable growth.',
 'Our mission is to help brands create meaningful, profitable, and sustainable growth through strategy-driven digital solutions. We believe every business deserves a strong brand, a better customer journey, and a growth system that supports long-term success. We aim to become the trusted growth partner for ambitious brands across India and beyond.',
 'OUR VISION', 'Brands that grow with consistency.',
 'Our vision is to build a future where brands grow with clarity, confidence, and consistency through marketing & technology-based solutions. We want to help businesses move beyond short-term marketing and create strong foundations that drive lasting impact. We believe strong brands, powered by smart marketing and technology, create stronger businesses.');

-- Seed about_cta
INSERT IGNORE INTO about_cta (id, badge, heading, description, btn_label, btn_url) VALUES
(1, 'Let\'s Build Something Bigger', 'Let\'s build a brand that performs.',
 'Growth does not happen by chance, it happens through the right strategy, strong execution, and consistent improvement. At Digifyce, we help brands move forward with confidence by creating systems that support visibility, trust, conversions, and long-term success. Whether you are starting your journey or scaling to the next level, we are here to help you grow.',
 'Connect with Digifyce Today', 'leadform.php');
