-- Brand Shoot Extra Tables (hero, section headers, approach panels, why bullets, CTA)

CREATE TABLE IF NOT EXISTS cs_hero (
  id INT AUTO_INCREMENT PRIMARY KEY,
  eyebrow VARCHAR(500) DEFAULT '',
  h1_line1 VARCHAR(255) DEFAULT '',
  h1_line2_accent VARCHAR(255) DEFAULT '',
  hero_copy TEXT,
  btn1_label VARCHAR(255) DEFAULT '',
  btn1_url VARCHAR(500) DEFAULT '',
  btn2_label VARCHAR(255) DEFAULT '',
  btn2_url VARCHAR(500) DEFAULT ''
);

CREATE TABLE IF NOT EXISTS cs_hero_features (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  label VARCHAR(255) DEFAULT '',
  title VARCHAR(255) DEFAULT '',
  copy TEXT,
  footer_text VARCHAR(255) DEFAULT '',
  chips_json TEXT
);

CREATE TABLE IF NOT EXISTS cs_section_headers (
  slug VARCHAR(50) PRIMARY KEY,
  eyebrow VARCHAR(255) DEFAULT '',
  heading TEXT,
  sub_text TEXT,
  extra_text TEXT
);

CREATE TABLE IF NOT EXISTS cs_approach_panels (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  step_label VARCHAR(100) DEFAULT '',
  heading VARCHAR(255) DEFAULT '',
  description TEXT,
  is_full_width TINYINT(1) DEFAULT 0
);

CREATE TABLE IF NOT EXISTS cs_why_bullets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  text TEXT
);

CREATE TABLE IF NOT EXISTS cs_cta (
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

INSERT IGNORE INTO cs_hero (id, eyebrow, h1_line1, h1_line2_accent, hero_copy, btn1_label, btn1_url, btn2_label, btn2_url) VALUES
(1, 'High-Impact Visual Content That Builds Trust and Drives Conversions', 'Commercial Shoot', 'Services India',
 'In today\'s digital market, strong visuals influence customer buying decisions and build brand trust. At Digifyce, we create professional commercial shoot content including product photography, ad films, brand storytelling, reels, and platform-ready visual assets designed to improve engagement and conversion.',
 'Let\'s Shoot Content That Sells', 'leadform.php', 'Explore Services', '#services');

INSERT IGNORE INTO cs_hero_features (id, sort_order, is_active, label, title, copy, footer_text, chips_json) VALUES
(1, 1, 1, 'Commercial Focus', 'Attention', 'Content that stops the scroll in a fast-moving feed.', 'First impression', '[]'),
(2, 2, 1, 'Brand Trust', 'Trust', 'Clean, premium visuals that make your product feel credible.', 'Confidence builder', '[]'),
(3, 3, 1, 'Conversion Layer', 'Sell', 'Each asset is planned to support awareness, consideration, and purchase.', '', '["Product Photography","Ad Films","Brand Storytelling"]');

INSERT IGNORE INTO cs_section_headers (slug, eyebrow, heading, sub_text, extra_text) VALUES
('why', 'Why it matters', 'Why Commercial Shoots Matter for Modern Brands', 'Many brands invest heavily in products and marketing but fail to generate strong results because their visual content does not create enough impact. Poor product photos, low-quality videos, and inconsistent branding reduce customer trust and directly affect purchase decisions.', ''),
('approach', 'Our approach', 'We treat every shoot like a conversion strategy.', 'At Digifyce, we do not approach shoots as just photography sessions. We plan each image and video with purpose, understanding your product, your audience, and your sales goals before the shoot begins.', 'Our objective is simple: create visuals that do more than look good — they must sell. We combine creative direction, product positioning, consumer psychology, and platform-specific strategy to ensure every asset supports business goals.'),
('services', 'Our commercial shoot services', 'Built for performance, not just aesthetics.', 'From ecommerce-ready photography to ad films and short-form content, we create assets that fit each platform and buying stage.', ''),
('process', 'Process', 'Structured from discovery to delivery.', 'We follow a clear process so every shoot delivers business value, not just visual output.', ''),
('why_choose', 'Our Advantage', 'Why Choose Digifyce', 'At Digifyce, we value our clients by creating professional visuals that deliver measurable business results. With the right commercial shoot strategy, you achieve higher product trust, better ad performance, increased website conversion rates, and stronger marketplace visibility.', '');

INSERT IGNORE INTO cs_approach_panels (id, sort_order, is_active, step_label, heading, description, is_full_width) VALUES
(1, 1, 1, '1. Attention', 'Stop the scroll.', 'We create visually striking content that immediately captures attention and makes people pause.', 0),
(2, 2, 1, '2. Trust', 'Build confidence.', 'High-quality photography and premium visuals create reliability, credibility, and value perception.', 0),
(3, 3, 1, '3. Conversion', 'Drive action.', 'Every image and video is designed to showcase features, benefits, and usage so customers can make informed buying decisions.', 1);

INSERT IGNORE INTO cs_why_bullets (id, sort_order, is_active, text) VALUES
(1, 1, 1, 'D2C-focused creative strategy'),
(2, 2, 1, 'Performance marketing-driven visual planning'),
(3, 3, 1, 'Product-first conversion-focused execution'),
(4, 4, 1, 'Platform-specific shoot optimization'),
(5, 5, 1, 'End-to-end support from concept to final delivery'),
(6, 6, 1, 'Strong understanding of ecommerce and marketplace requirements');

INSERT IGNORE INTO cs_cta (id, bg_text, heading, description, btn1_label, btn1_url, btn2_label, btn2_url) VALUES
(1, 'SHOOT', 'Book Your Brand Shoot Today', 'Customers trust what they can see. Strong visuals create stronger buying decisions. At Digifyce, we help brands create commercial shoot assets that improve visibility, trust, and conversions across every platform.', 'Book Your Brand Shoot Today', 'leadform.php', 'View Services', '#services');
