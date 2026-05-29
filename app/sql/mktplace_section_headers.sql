-- Marketplace Management Section Headers + Hero Icons + Service Blocks

CREATE TABLE IF NOT EXISTS mktplace_section_headers (
  slug VARCHAR(50) PRIMARY KEY,
  eyebrow VARCHAR(255) DEFAULT '',
  heading TEXT,
  sub_text TEXT,
  extra_text TEXT,
  btn_label VARCHAR(255) DEFAULT '',
  btn_url VARCHAR(500) DEFAULT ''
);

CREATE TABLE IF NOT EXISTS mktplace_hero_icons (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  title VARCHAR(100) NOT NULL,
  svg_file VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS mktplace_service_blocks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  title VARCHAR(255) NOT NULL,
  description TEXT
);

CREATE TABLE IF NOT EXISTS mktplace_service_block_cards (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  service_block_id INT NOT NULL DEFAULT 1,
  icon VARCHAR(100) DEFAULT '',
  title VARCHAR(255) NOT NULL,
  description TEXT,
  bullets_json TEXT,
  is_wide TINYINT(1) DEFAULT 0
);

-- Seed mktplace_section_headers
INSERT IGNORE INTO mktplace_section_headers (slug, eyebrow, heading, sub_text, extra_text, btn_label, btn_url) VALUES
('challenges', 'The Struggle', 'Why Many Brands Remain Invisible.', 'Without the right strategy, products get lost among thousands of competitors. Poor management causes brands to lose both sales and profitability.', '', '', ''),
('approach', 'Strategy', 'Our Approach to Growth', 'At Digifyce, we do not just manage marketplace accounts — we build marketplace growth systems focusing on three critical areas.', '', '', ''),
('services', 'Complete Solutions', 'End-to-End Marketplace Management Services', '', '', '', ''),
('services_sidebar', 'Your Digital Salesperson', '', 'Every solution is designed to improve marketplace visibility, customer trust, and business profitability.', '', '', ''),
('impacts', 'The Results', 'Business Impact of Strong Management', 'Marketplace management is not just account handling, it is revenue optimization.', '', '', '');

-- Seed mktplace_hero_icons
INSERT IGNORE INTO mktplace_hero_icons (id, sort_order, is_active, title, svg_file) VALUES
(1, 1, 1, 'Amazon', 'amazon-color-svgrepo-com.svg'),
(2, 2, 1, 'Flipkart', 'brand-flipkart-svgrepo-com.svg'),
(3, 3, 1, 'Myntra', 'myntra-svgrepo-com.svg'),
(4, 4, 1, 'Meesho', 'meesho-seeklogo.svg');

-- Seed mktplace_service_blocks
INSERT IGNORE INTO mktplace_service_blocks (id, sort_order, is_active, title, description) VALUES
(1, 1, 1, 'Amazon & Flipkart Listing Services', 'A well-optimized listing can significantly improve both visibility and conversions. We create listings designed to rank better and sell faster.'),
(2, 2, 1, 'Marketplace Account Management', 'Marketplace success depends heavily on daily operational management. Strong backend systems protect both sales and account health.'),
(3, 3, 1, 'Marketplace Ads Management', 'Organic visibility alone is often not enough. Sponsored advertising helps accelerate visibility and improve sales performance.');

-- Seed mktplace_service_block_cards (service_block_id: 1=Listing, 2=Account Mgmt, 3=Ads Mgmt)
INSERT IGNORE INTO mktplace_service_block_cards (id, sort_order, is_active, service_block_id, icon, title, description, bullets_json, is_wide) VALUES
(1, 1, 1, 1, 'edit_document', 'Product Creation', '', '["Title & description creation","Feature highlights & bullets","Technical specs & attributes","Marketplace-compliant formatting"]', 0),
(2, 2, 1, 1, 'manage_search', 'Marketplace SEO', '', '["High-performing keyword research","Buyer-intent search terms","Backend search term optimization","Category relevance improvement"]', 0),
(3, 3, 1, 1, 'imagesmode', 'Image Optimization', 'White background prep, infographic product images, lifestyle presentations, and platform compliance to maximize click-through rates.', '[]', 1),
(4, 1, 1, 2, 'inventory_2', 'Inventory & Pricing', '', '["Stock level monitoring","Competitor pricing analysis","Promotional pricing strategy","Profit margin optimization"]', 0),
(5, 2, 1, 2, 'support_agent', 'Order & Customer Handling', '', '["Processing & delivery workflow","Return handling support","Customer queries & support","Seller reputation & review monitoring"]', 0),
(6, 1, 1, 3, 'ads_click', 'Sponsored Campaigns', '', '["Sponsored Product Ads","Sponsored Brand Campaigns","Display ads & launch campaigns"]', 0),
(7, 2, 1, 3, 'insights', 'Optimization & ROI', '', '["Keyword & bid strategy optimization","Tracking ROAS & ACOS","Sales attribution insights"]', 0);
