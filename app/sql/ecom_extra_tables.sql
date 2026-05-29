-- E-Commerce Marketing Extra Tables (hero, solutions, why points, cta)

CREATE TABLE IF NOT EXISTS ecom_hero (
  id INT AUTO_INCREMENT PRIMARY KEY,
  badge VARCHAR(255) DEFAULT '',
  h1_line1 VARCHAR(255) DEFAULT '',
  h1_line2_accent VARCHAR(255) DEFAULT '',
  hero_sub TEXT,
  btn_label VARCHAR(255) DEFAULT '',
  btn_url VARCHAR(500) DEFAULT ''
);

CREATE TABLE IF NOT EXISTS ecom_solutions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  tag_label VARCHAR(100) DEFAULT '',
  tag_color VARCHAR(50) DEFAULT 'blue',
  title VARCHAR(255) DEFAULT '',
  description TEXT,
  bullets_json TEXT,
  bg_image VARCHAR(500) DEFAULT ''
);

CREATE TABLE IF NOT EXISTS ecom_why_points (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  icon VARCHAR(100) DEFAULT '',
  title VARCHAR(255) DEFAULT '',
  description TEXT
);

CREATE TABLE IF NOT EXISTS ecom_cta (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bg_text VARCHAR(100) DEFAULT '',
  heading VARCHAR(500) DEFAULT '',
  description TEXT,
  btn_label VARCHAR(255) DEFAULT '',
  btn_url VARCHAR(500) DEFAULT ''
);

-- Seed

INSERT IGNORE INTO ecom_hero (id, badge, h1_line1, h1_line2_accent, hero_sub, btn_label, btn_url) VALUES
(1,
 'Scale Your Online Store',
 'E-Commerce Marketing',
 'Services in India',
 'We build high-performing, conversion-focused Shopify, WooCommerce, and Custom e-commerce systems that turn traffic into revenue.',
 'Get Ready For Growth', 'leadform.php');

INSERT IGNORE INTO ecom_solutions (id, sort_order, is_active, tag_label, tag_color, title, description, bullets_json, bg_image) VALUES
(1, 1, 1, 'Shopify', 'blue', 'Shopify Dev',
 'Speed, flexibility, and scalability for brands that want to grow efficiently. Focused on performance, conversion, and brand experience.',
 '["Custom Theme Development","App Integration & Automation","Conversion Optimization"]',
 'public/assets/img/shopify.png'),
(2, 2, 1, 'WordPress', 'purple', 'WooCommerce',
 'Complete control over your platform. Scalable, performance-driven, and tailored to your exact business requirements.',
 '["Plugin Customization","Scalable Backend Structure","Payment Gateway Integration"]',
 'public/assets/img/woo-commerce.png'),
(3, 3, 1, 'Custom Dev', 'pink', 'Custom Builds',
 'For businesses requiring more than standard platforms—advanced functionality, complex APIs, and complete operational control.',
 '["Premium UI/UX Design","Custom Checkout Systems","CRM/ERP/API Integrations"]',
 'public/assets/img/custom-builds.jpg');

INSERT IGNORE INTO ecom_why_points (id, sort_order, is_active, icon, title, description) VALUES
(1, 1, 1, 'storefront', 'D2C & E-Commerce Focused', 'Our development approach is tailored specifically for direct-to-consumer online growth.'),
(2, 2, 1, 'code_blocks', 'Platform Expertise', 'Strong proficiency across Shopify, WooCommerce, and entirely custom platforms.'),
(3, 3, 1, 'shopping_cart_checkout', 'Conversion-First Design', 'Every layout and UI decision is driven by a philosophy of maximizing sales.'),
(4, 4, 1, 'speed', 'Built-In Performance', 'Speed and performance optimization are architected into every single project.'),
(5, 5, 1, 'manage_search', 'SEO-Friendly Development', 'Technical architecture designed to rank higher and attract organic traffic.'),
(6, 6, 1, 'rocket_launch', 'End-to-End Support', 'From initial setup and launch to ongoing scaling and long-term maintenance.');

INSERT IGNORE INTO ecom_cta (id, bg_text, heading, description, btn_label, btn_url) VALUES
(1, 'GROW',
 'Let\'s Build a Store That Sells More.',
 'Your online store should be your strongest sales channel—not your biggest limitation.',
 'Build Your Store Today', 'leadform.php');
