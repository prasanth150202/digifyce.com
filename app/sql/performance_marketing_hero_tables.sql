CREATE TABLE IF NOT EXISTS pm_hero (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kicker VARCHAR(255) DEFAULT '',
  headline_main VARCHAR(255) DEFAULT '',
  headline_accent VARCHAR(255) DEFAULT '',
  sub_text TEXT,
  btn1_label VARCHAR(100) DEFAULT '',
  btn1_url VARCHAR(255) DEFAULT '',
  btn2_label VARCHAR(100) DEFAULT '',
  btn2_url VARCHAR(255) DEFAULT '',
  card_heading VARCHAR(255) DEFAULT '',
  card_body TEXT,
  card_body2 TEXT
);

INSERT IGNORE INTO pm_hero (id, kicker, headline_main, headline_accent, sub_text, btn1_label, btn1_url, btn2_label, btn2_url, card_heading, card_body, card_body2) VALUES (
  1,
  'Data-Driven Marketing That Generates Leads, Sales, and Scalable Growth',
  'Performance Marketing ',
  'Services in India',
  'In today''s competitive digital market, businesses need measurable results, not just visibility. At Digifyce, we provide performance marketing services focused on lead generation, sales growth, and ROI. From Meta Ads, Google Ads, SEO, and PPC to automation and conversion optimization, we build strategies that turn traffic into long-term business growth.',
  'Scale Your Business with Proven Strategies',
  'leadform.php',
  'Explore Services',
  '#services',
  'Performance Marketing Agency for Modern Brands',
  'We provide complete performance marketing services including programmatic marketing, customer acquisition, retention systems, impression-to-conversion strategy, and full marketing automation.',
  'Our goal is simple: create a scalable marketing system that grows your business consistently.'
);

CREATE TABLE IF NOT EXISTS pm_hero_metrics (
  id INT AUTO_INCREMENT PRIMARY KEY,
  value INT DEFAULT 0,
  text TEXT,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1
);

INSERT IGNORE INTO pm_hero_metrics (id, value, text, sort_order) VALUES
(1, 4,   'Growth pillars: Acquisition, Conversion, Retention, Automation.', 1),
(2, 360, 'Full customer journey focus from first click to repeat business.', 2),
(3, 2,   'Balanced engine: Paid + Organic growth for long-term results.', 3),
(4, 1,   'Integrated strategy across SEO, Ads, Funnel, and Automation.', 4);

CREATE TABLE IF NOT EXISTS pm_benchmark_groups (
  id INT AUTO_INCREMENT PRIMARY KEY,
  industry_icon VARCHAR(100) DEFAULT '',
  industry_label VARCHAR(100) DEFAULT '',
  rows_json TEXT,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1
);

INSERT IGNORE INTO pm_benchmark_groups (id, industry_icon, industry_label, rows_json, sort_order) VALUES
(1,'apparel','Clothing & Apparel','[{"metric":"Blended ROAS","benchmark":"2.4x","digifyce_avg":"5.8x","lift":"+141%"},{"metric":"LTV\\/CAC Ratio","benchmark":"2.1:1","digifyce_avg":"4.2:1","lift":"+100%"},{"metric":"Purchase Frequency","benchmark":"1.8 \\/ yr","digifyce_avg":"3.2 \\/ yr","lift":"+77%"}]',1),
(2,'fastfood','FMCG & CPG','[{"metric":"Market Share Velocity","benchmark":"Low-Mod","digifyce_avg":"Hyper-Scale","lift":"+210%"},{"metric":"Subscription MoM","benchmark":"+4.2%","digifyce_avg":"+24.5%","lift":"+483%"},{"metric":"Amazon ACR","benchmark":"12.1%","digifyce_avg":"18.2%","lift":"+50%"}]',2),
(3,'business_center','B2B SaaS','[{"metric":"Lead-to-Close Rate","benchmark":"2.5%","digifyce_avg":"8.4%","lift":"+236%"},{"metric":"Avg. Deal Cycle","benchmark":"120 days","digifyce_avg":"74 days","lift":"-38% (Time)"}]',3),
(4,'spa','Skin Care & Beauty','[{"metric":"Conversion Rate","benchmark":"1.9%","digifyce_avg":"4.6%","lift":"+142%"},{"metric":"Repeat Purchase Rate","benchmark":"22%","digifyce_avg":"41%","lift":"+86%"},{"metric":"Customer Lifetime Value","benchmark":"₹4,200","digifyce_avg":"₹8,900","lift":"+112%"}]',4);

CREATE TABLE IF NOT EXISTS pm_section_headers (
  slug VARCHAR(50) PRIMARY KEY,
  kicker VARCHAR(255) DEFAULT '',
  heading TEXT,
  sub_text TEXT,
  extra_text TEXT,
  btn_label VARCHAR(100) DEFAULT '',
  btn_url VARCHAR(255) DEFAULT '',
  btn2_label VARCHAR(100) DEFAULT '',
  btn2_url VARCHAR(255) DEFAULT ''
);

INSERT IGNORE INTO pm_section_headers (slug, kicker, heading, sub_text, extra_text, btn_label, btn_url, btn2_label, btn2_url) VALUES
('problem','Core problem','Why Most Businesses Struggle with Performance Marketing','Many brands spend heavily on ads and marketing but fail to achieve sustainable results because their strategy lacks structure, optimization, and data-backed decision-making.','','','','',''),
('approach','Our approach','Our Approach to Performance Marketing','At Digifyce, performance marketing is not just about running ads, it is about building a complete revenue engine for your business. We combine acquisition, retention, automation, and optimization so every marketing activity contributes to business growth.','','','','',''),
('services','Paid + Organic + Automation Services','Performance Marketing Services Designed as a Scalable System','','','','','',''),
('leadgen','Growth Engine','Lead Generation Services','Generating leads is not just about running ads it requires strategy, testing, follow-up, and retention systems. Our lead generation services combine traditional and modern digital-driven strategies to bring high-quality leads that convert into customers.','','','','',''),
('seo','Organic Dominance','SEO Services in India for Organic Growth','Paid ads create immediate growth, but SEO builds long-term sustainable visibility. At Digifyce, our SEO services are designed to improve your organic presence across traditional search engines and new AI-driven search platforms.','','','','',''),
('process','Execution system','Our Performance Marketing Process','We follow a structured system to deliver predictable and scalable growth.','','','','',''),
('impact','Business impact','Why Choose Digifyce','','At Digifyce, we value our clients by combining strategy, creativity, data, and automation to create growth systems that perform. We do not simply run campaigns, we build growth engines that drive measurable business impact.','Start Scaling Today','leadform.php','',''),
('cta','','Start Your Performance Marketing Today','Your business deserves more than random campaigns and temporary results. At Digifyce, we help brands create predictable, profitable, and scalable growth through smart performance marketing. From first click to final conversion, we make every step count.','','Start Your Performance Marketing Today','leadform.php','See Approach','#approach');
