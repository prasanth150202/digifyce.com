-- Content Marketing Extra Tables (hero, stats, challenges, pillars, metrics, why points, cta)

CREATE TABLE IF NOT EXISTS content_hero (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kicker VARCHAR(500) DEFAULT '',
  h1_line1 VARCHAR(255) DEFAULT '',
  h1_line2_gradient VARCHAR(255) DEFAULT '',
  hero_sub TEXT,
  btn1_label VARCHAR(255) DEFAULT '',
  btn1_url VARCHAR(500) DEFAULT '',
  btn2_label VARCHAR(255) DEFAULT '',
  btn2_url VARCHAR(500) DEFAULT ''
);

CREATE TABLE IF NOT EXISTS content_hero_stats (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  label VARCHAR(255) DEFAULT '',
  value VARCHAR(100) DEFAULT '',
  description TEXT
);

CREATE TABLE IF NOT EXISTS content_challenges (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  number_label VARCHAR(10) DEFAULT '',
  title VARCHAR(255) DEFAULT '',
  description TEXT,
  progress_width VARCHAR(20) DEFAULT 'w-1/2'
);

CREATE TABLE IF NOT EXISTS content_pillars (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  number_label VARCHAR(10) DEFAULT '',
  name VARCHAR(100) DEFAULT '',
  panel_title VARCHAR(255) DEFAULT '',
  panel_description TEXT,
  bullets_json TEXT
);

CREATE TABLE IF NOT EXISTS content_metrics (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  target_num INT DEFAULT 0,
  label VARCHAR(255) DEFAULT ''
);

CREATE TABLE IF NOT EXISTS content_why_points (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  icon VARCHAR(100) DEFAULT '',
  title VARCHAR(255) DEFAULT '',
  description TEXT
);

CREATE TABLE IF NOT EXISTS content_cta (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bg_text VARCHAR(100) DEFAULT '',
  heading VARCHAR(500) DEFAULT '',
  description TEXT,
  btn_label VARCHAR(255) DEFAULT '',
  btn_url VARCHAR(500) DEFAULT ''
);

-- Seed

INSERT IGNORE INTO content_hero (id, kicker, h1_line1, h1_line2_gradient, hero_sub, btn1_label, btn1_url, btn2_label, btn2_url) VALUES
(1,
 'Strategic Content That Builds Trust & Visibility',
 'Content Marketing',
 'Services in India',
 'We deliver semantic, SEO-focused, and engaging digital content that turns casual visitors into converted loyalists.',
 'Let\'s Create Together', 'leadform.php',
 'Explore Insights', 'blog_list.php');

INSERT IGNORE INTO content_hero_stats (id, sort_order, is_active, label, value, description) VALUES
(1, 1, 1, 'Avg Lift', '3.2x', ''),
(2, 2, 1, 'Time to Rank', '60 Days', ''),
(3, 3, 1, 'Retention', '+48%', ''),
(4, 4, 1, 'Channels', '8+', 'Multi-format distribution planning.'),
(5, 5, 1, 'Velocity', '2x', 'Content ops optimized for speed.');

INSERT IGNORE INTO content_challenges (id, sort_order, is_active, number_label, title, description, progress_width) VALUES
(1, 1, 1, '01', 'Low Organic Visibility', 'Diminished search performance hides you from ready-to-buy customers while competitors compound rankings.', 'w-3/5'),
(2, 2, 1, '02', 'Weak Trust Signals', 'Scattered messaging makes audiences doubt your expertise. Buyers need repeated clarity to commit.', 'w-2/3'),
(3, 3, 1, '03', 'Poor Retention', 'Without authoritative updates, social presence plateaus and audiences churn over time.', 'w-1/2');

INSERT IGNORE INTO content_pillars (id, sort_order, is_active, number_label, name, panel_title, panel_description, bullets_json) VALUES
(1, 1, 1, '01', 'Visibility', 'Command the Search Feed',
 'We help your brand appear exactly where your audience is actively seeking solutions. Through robust keyword optimization and intent-matching, we ensure you dominate the organic query space.',
 '["Search Intent Alignment","Keyword Dominance","Organic Reach Growth","SERP Features"]'),
(2, 2, 1, '02', 'Trust', 'Become the Gold Standard',
 'Creating deeply researched, valuable material explicitly builds credibility. We position your enterprise not just as a vendor, but as the indubitable authority in your market niche.',
 '["Thought Leadership","Deep-Dive Research","Consistent Tone","Education-First Style"]'),
(3, 3, 1, '03', 'Conversion', 'Accelerate Business Velocity',
 'Words that persuade. By supporting the customer\'s psychology through their buying journey, our content seamlessly turns passive readers into active generated leads and recurring sales.',
 '["Persuasive CTA Placement","Psychology-Driven Copy","Friction Reduction","High ROI Generation"]');

INSERT IGNORE INTO content_metrics (id, sort_order, is_active, target_num, label) VALUES
(1, 1, 1, 250, 'Organic Lift %'),
(2, 2, 1, 45, 'Bounce Drop %'),
(3, 3, 1, 3, 'Lead Multiplier'),
(4, 4, 1, 99, 'Client Trust %');

INSERT IGNORE INTO content_why_points (id, sort_order, is_active, icon, title, description) VALUES
(1, 1, 1, 'manage_search', 'SEO + Conversion Strategy', 'Our content is engineered not just for search visibility, but for maximum conversion.'),
(2, 2, 1, 'storefront', 'D2C & Growth Focused', 'We employ a business growth-driven writing approach tailored for direct-to-consumer success.'),
(3, 3, 1, 'workspace_premium', 'Authority Building', 'Long-form expertise that firmly establishes your brand as an authoritative industry voice.'),
(4, 4, 1, 'devices', 'Platform-Specific Content', 'Nuanced content planning customized for specific platforms to maximize engagement.'),
(5, 5, 1, 'psychology', 'Intent & Behavior Mastery', 'Strong understanding of search intent and customer behavior to guide the entire narrative.'),
(6, 6, 1, 'insights', 'Consistent Quality', 'High-level editorial standards paired with a measurable, data-backed business focus.');

INSERT IGNORE INTO content_cta (id, bg_text, heading, description, btn_label, btn_url) VALUES
(1, 'GROW',
 'Ready to Build a Content System That Scales?',
 'Your audience is already searching for solutions. We help ensure they find you first, trust you immediately, and convert flawlessly.',
 'Start Your Content Strategy', 'leadform.php');
