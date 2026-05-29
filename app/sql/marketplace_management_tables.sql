-- Marketplace Management Page Tables

CREATE TABLE IF NOT EXISTS mktplace_challenges (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  icon VARCHAR(100) DEFAULT '',
  title VARCHAR(255) NOT NULL,
  description TEXT
);

CREATE TABLE IF NOT EXISTS mktplace_steps (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  step_number VARCHAR(10) DEFAULT '',
  title VARCHAR(255) NOT NULL,
  description TEXT,
  icon VARCHAR(100) DEFAULT ''
);

-- Seed data

INSERT IGNORE INTO mktplace_challenges (id, sort_order, is_active, icon, title, description) VALUES
(1,1,1,'visibility_off','Low Visibility','Poor rankings keep your products hidden from ready-to-buy customers.'),
(2,2,1,'trending_down','Weak Listings','Low-quality descriptions and unoptimized features kill conversion rates.'),
(3,3,1,'key','Poor Keywords','Missing buyer-intent search terms limits your product discoverability.'),
(4,4,1,'image_not_supported','Weak Images','Unprofessional visuals lead to drastically low click-through rates.'),
(5,5,1,'inventory','Stock Issues','Inventory mismanagement causes stock-outs and drops your marketplace rank.'),
(6,6,1,'money_off','High Ad Spend','Unoptimized advertising drains budget with a dangerously low return on investment.');

INSERT IGNORE INTO mktplace_steps (id, sort_order, is_active, step_number, title, description, icon) VALUES
(1,1,1,'01','Marketplace Audit','Reviewing current listings, account health, competitors, and market opportunities.','analytics'),
(2,2,1,'02','Listing Strategy','Creating strategy to improve listings, identify keywords, and enhance visibility.','strategy'),
(3,3,1,'03','Account Setup','Organizing inventory, pricing, order flow, and operational processes for efficiency.','settings_suggest'),
(4,4,1,'04','Ad Strategy','Launching sponsored ads designed to increase visibility and attract buyers.','ads_click'),
(5,5,1,'05','Monitoring','Continuously tracking listings, ad campaigns, and sales performance to optimize.','query_stats'),
(6,6,1,'06','Scaling','Expanding catalogs and scaling operations for sustainable business growth.','trending_up');
