CREATE TABLE IF NOT EXISTS pm_challenges (
  id INT AUTO_INCREMENT PRIMARY KEY,
  icon VARCHAR(100) DEFAULT '',
  text TEXT,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1
);

INSERT IGNORE INTO pm_challenges (id, icon, text, sort_order) VALUES
(1,'trending_down','High ad spend with low return on investment',1),
(2,'group_off','Poor lead quality and low conversion rates',2),
(3,'money_off','Weak customer retention systems',3),
(4,'campaign','Ineffective ad creatives and messaging',4),
(5,'smart_toy','Lack of automation and follow-up systems',5),
(6,'visibility_off','Low organic visibility in search engines',6),
(7,'filter_alt_off','Poor funnel performance from click to conversion',7);

CREATE TABLE IF NOT EXISTS pm_approaches (
  id INT AUTO_INCREMENT PRIMARY KEY,
  step_label VARCHAR(100) DEFAULT '',
  heading VARCHAR(255) DEFAULT '',
  description TEXT,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1
);

INSERT IGNORE INTO pm_approaches (id, step_label, heading, description, sort_order) VALUES
(1,'Step 1: Acquisition','Bring high-quality traffic','We attract qualified leads through paid ads, SEO, and organic channels so the right audience enters your pipeline.',1),
(2,'Step 2: Conversion','Turn clicks into outcomes','We optimize landing pages, funnels, and customer journeys to reduce drop-offs and increase leads and sales.',2),
(3,'Step 3: Retention','Increase long-term value','We build retention systems that improve repeat purchases and customer lifetime value after first purchase.',3),
(4,'Step 4: Automation','Scale without chaos','We use smart automation to save time, improve follow-ups, and create a smoother customer experience.',4);

CREATE TABLE IF NOT EXISTS pm_services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tag VARCHAR(100) DEFAULT '',
  heading VARCHAR(255) DEFAULT '',
  description TEXT,
  chips_json TEXT,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1
);

INSERT IGNORE INTO pm_services (id, tag, heading, description, chips_json, sort_order) VALUES
(1,'Meta Ads','Facebook & Instagram campaigns built for profitable scaling','We plan and manage customer acquisition, lead generation, conversion, retargeting, brand awareness, and creative testing campaigns.','["Acquisition campaigns","Lead campaigns","Sales campaigns","Retargeting","Brand awareness","Creative testing"]',1),
(2,'Google Ads','Capture high-intent buyers at the right moment','We run Search, Display, Shopping, YouTube, and remarketing campaigns so your brand appears when users are actively looking to buy.','["Search Ads","Display Ads","Shopping Ads","YouTube Ads","Remarketing"]',2),
(3,'PPC','Precision campaign management with tight cost control','Our PPC setup includes bid strategy, audience segmentation, performance tracking, cost optimization, and conversion monitoring.','["Campaign setup","Bid management","Audience segmentation","Cost optimization","Conversion monitoring"]',3),
(4,'Funnel Optimization','Traffic is only step one, conversion is the real game','We optimize landing pages, checkout processes, sales flows, user journey paths, and conversion tracking systems.','["Landing pages","Checkout UX","Sales funnels","Journey flow","Tracking systems"]',4),
(5,'SEO Services in India','Organic growth for traditional and AI-driven search','Paid ads drive immediate growth, but SEO builds long-term visibility. We optimize for search engines and answer engines together.','["Keyword research","AEO","GEO","On-page SEO","Technical SEO","Link building"]',5),
(6,'Lead Generation','A complete lead ecosystem, not isolated campaigns','We combine paid media, email, WhatsApp automation, bot follow-ups, CRM nurturing, retargeting, and A/B testing for better lead quality.','["Paid ads","Email marketing","WhatsApp automation","CRM nurturing","Retargeting","A/B testing"]',6),
(7,'Conversion Layer','Audience, Creative, Landing Page, Retargeting','We improve targeting quality, creative hooks, messaging, CTA placement, trust factors, and recovery loops for users who do not convert initially.','["Audience research","Creative optimization","Landing page CRO","Retargeting systems","Cart recovery","Re-engagement"]',7);

CREATE TABLE IF NOT EXISTS pm_leadgen_tabs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tab_icon VARCHAR(100) DEFAULT '',
  title VARCHAR(255) DEFAULT '',
  intro_text TEXT,
  bullets_json TEXT,
  conclusion TEXT,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1
);

INSERT IGNORE INTO pm_leadgen_tabs (id, tab_icon, title, intro_text, bullets_json, conclusion, sort_order) VALUES
(1,'hub','Multichannel Strategy','We use multiple channels including:','["Paid ads","Email marketing","WhatsApp automation","Bot-based follow-up systems","CRM-driven lead nurturing","Retention-focused remarketing","A/B testing across campaigns and funnels"]','This creates a complete lead generation ecosystem instead of isolated campaigns.',1),
(2,'person_search','Target Audience Research','Understanding the right audience is the foundation of lead generation. We identify your ideal customers based on:','["Interests and behaviors","Buying intent","Pain points and needs","Customer journey stages","Geographic and demographic relevance"]','This helps create highly targeted campaigns with better conversion potential.',2),
(3,'draw','Ad Creatives Optimization','Creative performance directly affects lead quality. We optimize:','["Ad visuals","Ad copywriting","Hooks and messaging","Video creatives","Offer presentation","A/B testing variations"]','This improves engagement, click-through rates and overall campaign efficiency.',3),
(4,'web_traffic','Landing Page Conversion Optimization','Even the best ads fail if the landing page does not convert. We improve:','["User experience","Conversion-focused messaging","CTA placement","Form optimization","Trust-building elements","Mobile responsiveness"]','This reduces drop-offs and improves lead or sales conversions.',4),
(5,'sync','Retargeting Strategies','Most customers do not convert on the first interaction. We build retargeting systems to reconnect with users who have already shown interest in your brand. This includes:','["Website visitor retargeting","Cart abandonment recovery","Social engagement retargeting","CRM-based remarketing","WhatsApp and email re-engagement"]','This improves conversion rates and increases customer retention.',5);

CREATE TABLE IF NOT EXISTS pm_seo_panels (
  id INT AUTO_INCREMENT PRIMARY KEY,
  panel_icon VARCHAR(100) DEFAULT '',
  title VARCHAR(255) DEFAULT '',
  intro_text TEXT,
  bullets_json TEXT,
  conclusion TEXT,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1
);

INSERT IGNORE INTO pm_seo_panels (id, panel_icon, title, intro_text, bullets_json, conclusion, sort_order) VALUES
(1,'manage_search','Keyword Research','We identify high-performing keywords relevant to your business, products, and target audience. Our keyword strategy helps:','["Improve search rankings","Attract targeted organic traffic","Increase visibility for high-conversion searches","Build content opportunities for long-term growth"]','We focus on keywords that generate business value, not just traffic.',1),
(2,'record_voice_over','AEO (Answer Engine Optimization)','Search behavior is changing. Customers now search through featured snippets, voice search, and AI-based answer engines. We optimize your content to appear in:','["Featured snippets","Voice search results","Direct answer search results","AI-generated answer systems"]','This improves visibility where traditional SEO alone is no longer enough.',2),
(3,'auto_awesome','GEO (Generative Engine Optimization)','Generative search engines and AI platforms are shaping the future of search visibility. We structure your content to perform well on:','["AI-driven search platforms","Generative search engines","Conversational search experiences","Emerging answer-based discovery systems"]','This helps your brand stay visible in evolving digital ecosystems.',3),
(4,'web','On-Page SEO','We optimize all visible website elements to improve relevance and search engine performance. This includes:','["Content optimization","Heading structure","Meta titles and descriptions","Internal linking","Image optimization","User-focused content improvements"]','This strengthens both rankings and user experience.',4),
(5,'settings_ethernet','Technical SEO','Strong backend performance is essential for search visibility. We improve:','["Website speed","Crawlability","Indexing issues","Site architecture","Mobile performance","Core Web Vitals","Search engine accessibility"]','This ensures your website performs efficiently for both users and search engines.',5),
(6,'link','Link Building','Authority matters in SEO. We build high-quality backlinks that help improve:','["Domain authority","Search rankings","Brand credibility","Organic visibility"]','Our focus is on quality, relevance and long-term SEO strength.',6);

CREATE TABLE IF NOT EXISTS pm_steps (
  id INT AUTO_INCREMENT PRIMARY KEY,
  icon VARCHAR(100) DEFAULT '',
  heading VARCHAR(255) DEFAULT '',
  description TEXT,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1
);

INSERT IGNORE INTO pm_steps (id, icon, heading, description, sort_order) VALUES
(1,'manage_search','Step 1: Discovery and Business Audit','We study your business model, customer journey, audience, competitors, and growth opportunities to set a strong foundation.',1),
(2,'schema','Step 2: Strategy Planning','We define acquisition channels, campaign architecture, SEO roadmap, and conversion-focused growth planning.',2),
(3,'rocket_launch','Step 3: Campaign Setup and Launch','We launch ad systems, SEO operations, landing pages, and automation flows designed for measurable outcomes.',3),
(4,'tune','Step 4: Optimization and Testing','We continuously test creatives, targeting, and funnels to improve conversion rates and ROI.',4),
(5,'trending_up','Step 5: Scaling and Automation','We scale winning systems and automate repeatable operations for long-term predictable growth.',5);

CREATE TABLE IF NOT EXISTS pm_impacts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  icon VARCHAR(100) DEFAULT '',
  icon_color VARCHAR(80) DEFAULT 'text-4xl text-[var(--pm-accent)]',
  heading VARCHAR(255) DEFAULT '',
  description TEXT,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1
);

INSERT IGNORE INTO pm_impacts (id, icon, icon_color, heading, description, sort_order) VALUES
(1,'payments','text-4xl text-[var(--pm-accent)]','Higher ROI and efficiency','Maximize output from every marketing rupee with tighter optimization.',1),
(2,'stars','text-4xl text-green-400','Better lead and sales quality','Improve downstream conversion with cleaner acquisition inputs.',2),
(3,'sync_alt','text-3xl text-blue-400','Stronger retention systems','Increase repeat purchase and customer value with lifecycle flows.',3),
(4,'visibility','text-3xl text-blue-300','Paid + organic visibility','Build channel stability across search and paid ecosystem signals.',4),
(5,'rocket_launch','text-3xl text-blue-400','Scalable growth systems','Move from random campaigns to a repeatable growth engine.',5),
(6,'hub','text-3xl text-blue-400','Lower channel dependency','Reduce business risk with a diversified and connected funnel.',6);
