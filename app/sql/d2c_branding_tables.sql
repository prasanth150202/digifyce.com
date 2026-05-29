-- D2C Branding Page Tables

CREATE TABLE IF NOT EXISTS d2c_hero (
  id INT AUTO_INCREMENT PRIMARY KEY,
  badge_text VARCHAR(255) DEFAULT '',
  headline_main VARCHAR(255) DEFAULT '',
  headline_accent VARCHAR(255) DEFAULT '',
  sub_description TEXT,
  btn1_label VARCHAR(255) DEFAULT '',
  btn1_url VARCHAR(500) DEFAULT '',
  btn2_label VARCHAR(255) DEFAULT '',
  btn2_url VARCHAR(500) DEFAULT ''
);

CREATE TABLE IF NOT EXISTS d2c_intro_tags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  tag_name VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS d2c_challenges (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  title VARCHAR(255) NOT NULL,
  description TEXT
);

CREATE TABLE IF NOT EXISTS d2c_pillars (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  number VARCHAR(10) DEFAULT '',
  name VARCHAR(255) NOT NULL,
  text TEXT,
  dots_json TEXT
);

CREATE TABLE IF NOT EXISTS d2c_solutions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  name VARCHAR(255) NOT NULL,
  description TEXT
);

CREATE TABLE IF NOT EXISTS d2c_steps (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  step_number VARCHAR(10) DEFAULT '',
  title VARCHAR(255) NOT NULL,
  description TEXT
);

CREATE TABLE IF NOT EXISTS d2c_metrics (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  value VARCHAR(20) DEFAULT '',
  unit VARCHAR(10) DEFAULT '',
  label VARCHAR(255) DEFAULT '',
  bar_pct INT DEFAULT 100
);

CREATE TABLE IF NOT EXISTS d2c_why_features (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  title VARCHAR(255) NOT NULL,
  description TEXT
);

CREATE TABLE IF NOT EXISTS d2c_cta (
  id INT AUTO_INCREMENT PRIMARY KEY,
  heading VARCHAR(500) DEFAULT '',
  description TEXT,
  btn_label VARCHAR(255) DEFAULT '',
  btn_url VARCHAR(500) DEFAULT ''
);

-- Seed data

INSERT IGNORE INTO d2c_hero (id, badge_text, headline_main, headline_accent, sub_description, btn1_label, btn1_url, btn2_label, btn2_url) VALUES
(1, 'Build a Scalable Brand That Drives Growth and Customer Loyalty', 'D2C Branding Services in', 'India',
 'In today\'s competitive D2C market, branding is more than just design, it builds trust, shapes customer perception and drives buying decisions. At Digifyce, we provide strategic D2C branding services that create strong, scalable and conversion-focused brands. We help businesses build clear, consistent, and impactful brand identities across every customer touchpoint.',
 'Let\'s Build Your Brand for Growth', 'leadform.php', 'Our Approach', '#pillars');

INSERT IGNORE INTO d2c_intro_tags (id, sort_order, is_active, tag_name) VALUES
(1, 1, 1, 'Brand Clarity'), (2, 2, 1, 'Visual Consistency'), (3, 3, 1, 'Emotional Connection'),
(4, 4, 1, 'Conversion Driven'), (5, 5, 1, 'D2C Strategy');

INSERT IGNORE INTO d2c_challenges (id, sort_order, is_active, title, description) VALUES
(1, 1, 1, 'Lack of Defined Brand Identity', 'Scattered visuals and unclear messaging make it impossible for customers to remember or trust your brand. Consistency is the bedrock of recognition.'),
(2, 2, 1, 'Generic Packaging', 'Packaging that fails to attract or engage customers. In the D2C space, packaging is your first physical handshake with the customer.'),
(3, 3, 1, 'Poor Market Positioning', 'Without a clear, differentiated strategy, you compete on price alone. Positioning creates a category of one.'),
(4, 4, 1, 'Inconsistent Messaging', 'When your brand sounds different across platforms, customers lose confidence. Unified messaging builds credibility.'),
(5, 5, 1, 'Weak Emotional Connection', 'Brands that fail to connect on an emotional level remain commodities, endlessly outbid by competitors.');

INSERT IGNORE INTO d2c_pillars (id, sort_order, is_active, number, name, text, dots_json) VALUES
(1, 1, 1, '01', 'Clarity', 'A strong brand begins with clarity. We define your brand purpose, values, target audience, and unique market position so customers immediately understand who you are and what you offer. Clear branding removes confusion and creates stronger customer trust. It helps your business communicate the right message to the right people at the right time.', '["Brand Purpose","Target Audience","Unique Positioning","Value Proposition"]'),
(2, 2, 1, '02', 'Consistency', 'Consistency builds recognition and long-term trust. We ensure your brand maintains the same visual identity, tone of voice, and messaging across your website, packaging, social media, advertisements, and every customer touchpoint. When customers see a consistent brand experience, it improves credibility and makes your business more memorable in a competitive market.', '["Visual Identity","Tone of Voice","Cross-Platform Unity","Brand Guidelines"]'),
(3, 3, 1, '03', 'Conversion', 'Branding should not only look attractive, it should drive action. We design every element of your brand with customer decision-making in mind, from packaging and messaging to website presentation and visual communication. Our focus is to create a brand experience that encourages trust, improves engagement, and increases conversions from visitors into loyal customers.', '["Conversion Copy","Packaging Psychology","CTA Architecture","ROI Tracking"]');

INSERT IGNORE INTO d2c_solutions (id, sort_order, is_active, name, description) VALUES
(1, 1, 1, 'Brand Identity Design', 'A strong brand identity is the foundation of your business. We create a cohesive and distinctive identity that reflects your brand\'s personality and resonates with your target audience.'),
(2, 2, 1, 'Logo Design', 'A logo is the face of your brand and the first visual element customers remember. We create professional logo designs with multiple variations such as primary logo, secondary logo, icon version, and simplified formats for different platforms.'),
(3, 3, 1, 'Colour Palette and Typography', 'Colours play an important role in influencing customer emotions and buying decisions. We carefully choose a brand colour palette based on your industry, audience behavior, and the psychological impact each colour creates.'),
(4, 4, 1, 'Packaging Design', 'Typography defines how your brand speaks visually. We create a structured typography system by selecting fonts that match your brand personality and ensure readability across both digital and print platforms.'),
(5, 5, 1, 'Visual Style Guidelines', 'Visual language is the complete design style that makes your brand unique and memorable. It includes imagery style, icon usage, design patterns, layouts, and creative direction that shape how customers experience your brand visually.'),
(6, 6, 1, 'Brand Strategy', 'In-depth market research, competitor analysis, and unique positioning tailored specifically for the Indian D2C landscape.');

INSERT IGNORE INTO d2c_steps (id, sort_order, is_active, step_number, title, description) VALUES
(1, 1, 1, '01', 'Discovery & Research', 'Understand your business, product, competitors, and audience behavior to identify key insights.'),
(2, 2, 1, '02', 'Strategy Development', 'Define brand positioning, messaging, and overall direction — the blueprint for all design.'),
(3, 3, 1, '03', 'Identity Creation', 'Design a visual identity that reflects your strategy and connects with your audience effectively.'),
(4, 4, 1, '04', 'Packaging & Experience', 'Develop packaging that enhances visual appeal and customer experience to support conversion.'),
(5, 5, 1, '05', 'Brand System Build', 'Create a complete brand system with guidelines to ensure consistency across all touchpoints.'),
(6, 6, 1, '06', 'Implementation & Support', 'Assist in applying your brand across websites, social media, and marketplaces for a seamless rollout.');

INSERT IGNORE INTO d2c_metrics (id, sort_order, is_active, value, unit, label, bar_pct) VALUES
(1, 1, 1, '95', '%', 'Trust Boost', 95),
(2, 2, 1, '80', '%', 'Brand Recall', 95),
(3, 3, 1, '3', 'x', 'Conv. Growth', 95),
(4, 4, 1, '100', '%', 'Scalability', 95);

INSERT IGNORE INTO d2c_why_features (id, sort_order, is_active, title, description) VALUES
(1, 1, 1, 'Specialized D2C Focus', 'Expertise in the D2C and e-commerce ecosystem — tailored strategies for modern digital businesses.'),
(2, 2, 1, 'Strategy-Led Design', 'Integration of strategy, design, and performance marketing for a holistic growth approach.'),
(3, 3, 1, 'Indian Market Native', 'Deep understanding of Indian consumer behavior, platform dynamics, and buying patterns.'),
(4, 4, 1, 'Conversion Oriented', 'A focused approach to branding and packaging designed specifically to drive customer action.'),
(5, 5, 1, 'End-to-End Support', 'Ownership of the full process from initial brand concept to final marketplace execution.');

INSERT IGNORE INTO d2c_cta (id, heading, description, btn_label, btn_url) VALUES
(1, 'Build a Brand That Stands Out and Scales', 'Your brand is one of your most valuable assets. Let\'s create something clear, consistent, and built for long-term growth.', 'Get Started with Digifyce Today', 'leadform.php');
