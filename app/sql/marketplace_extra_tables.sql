-- Marketplace Management Extra Tables (hero, approach cards, impacts, why bullets, cta)

CREATE TABLE IF NOT EXISTS mktplace_hero (
  id INT AUTO_INCREMENT PRIMARY KEY,
  badge VARCHAR(255) DEFAULT '',
  h1_line1 VARCHAR(255) DEFAULT '',
  h1_line2_accent VARCHAR(255) DEFAULT '',
  hero_sub TEXT,
  btn_label VARCHAR(255) DEFAULT '',
  btn_url VARCHAR(500) DEFAULT ''
);

CREATE TABLE IF NOT EXISTS mktplace_approach_cards (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  number_label VARCHAR(10) DEFAULT '',
  title VARCHAR(255) DEFAULT '',
  description TEXT,
  icon VARCHAR(100) DEFAULT ''
);

CREATE TABLE IF NOT EXISTS mktplace_impacts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  icon VARCHAR(100) DEFAULT '',
  title VARCHAR(255) DEFAULT '',
  description TEXT
);

CREATE TABLE IF NOT EXISTS mktplace_why_bullets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  text TEXT
);

CREATE TABLE IF NOT EXISTS mktplace_cta (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bg_text VARCHAR(100) DEFAULT '',
  heading VARCHAR(500) DEFAULT '',
  description TEXT,
  btn1_label VARCHAR(255) DEFAULT '',
  btn1_url VARCHAR(500) DEFAULT '',
  btn2_label VARCHAR(255) DEFAULT '',
  btn2_url VARCHAR(500) DEFAULT ''
);

-- Seed

INSERT IGNORE INTO mktplace_hero (id, badge, h1_line1, h1_line2_accent, hero_sub, btn_label, btn_url) VALUES
(1,
 'Scale Your Brand On Amazon & Flipkart',
 'Marketplace Management',
 'Services in India',
 'Selling requires more than just product uploads. We provide complete listing optimization, SEO, account management and advertising to achieve profitable long-term growth.',
 'Let\'s Grow Your Sales', 'leadform.php');

INSERT IGNORE INTO mktplace_approach_cards (id, sort_order, is_active, number_label, title, description, icon) VALUES
(1, 1, 1, '01', 'Visibility', 'Helping your products rank higher and get discovered by the right buyers through rigorous SEO and keyword strategies.', 'travel_explore'),
(2, 2, 1, '02', 'Conversion', 'Improving product listings, pricing logic, and customer trust signals to drastically increase your sales conversion rate.', 'shopping_bag'),
(3, 3, 1, '03', 'Scalability', 'Creating structured systems that support long-term marketplace growth, inventory automation, and operational efficiency.', 'rocket_launch');

INSERT IGNORE INTO mktplace_impacts (id, sort_order, is_active, icon, title, description) VALUES
(1, 1, 1, 'query_stats', 'Higher Rankings', 'Increased visibility across search.'),
(2, 2, 1, 'touch_app', 'Better CTR', 'Optimized listings drive more clicks.'),
(3, 3, 1, 'star', 'Customer Trust', 'Improved seller ratings and reviews.'),
(4, 4, 1, 'savings', 'Higher ROAS', 'Stronger profitability on ad spend.');

INSERT IGNORE INTO mktplace_why_bullets (id, sort_order, is_active, text) VALUES
(1, 1, 1, 'Strong expertise in Amazon & Flipkart'),
(2, 2, 1, 'SEO + Ads + Operations under one roof'),
(3, 3, 1, 'Conversion-focused optimization'),
(4, 4, 1, 'End-to-end support to scaling');

INSERT IGNORE INTO mktplace_cta (id, bg_text, heading, description, btn1_label, btn1_url, btn2_label, btn2_url) VALUES
(1, 'Scale',
 'Why Choose Digifyce?',
 'At Digifyce, we value our clients by combining marketplace expertise, strategic optimization, and performance-driven execution to help brands grow faster and perform better across online marketplaces.',
 'Scale Your Business Now', 'leadform.php',
 'Talk to an Expert', 'contact.php');
