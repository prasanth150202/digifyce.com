-- Content Marketing Page Tables

CREATE TABLE IF NOT EXISTS content_solutions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  number VARCHAR(10) DEFAULT '',
  title VARCHAR(255) NOT NULL,
  description TEXT,
  bg_color VARCHAR(20) DEFAULT '#0f172a'
);

-- Seed data

INSERT IGNORE INTO content_solutions (id, sort_order, is_active, number, title, description, bg_color) VALUES
(1,1,1,'01','Blog Writing','Long-form, SEO-optimised blog content that attracts search traffic, builds authority, and keeps your audience coming back.','#0f172a'),
(2,2,1,'02','Website Content','Persuasive, on-brand website copy for homepages, service pages, and about sections that convert visitors into leads.','#1e1b4b'),
(3,3,1,'03','Social Media Content','Platform-native content for Instagram, LinkedIn, and more — built for engagement, recall, and community growth.','#020617'),
(4,4,1,'04','SEO Content & Landing Pages','Keyword-targeted pages and high-conversion landing pages designed to rank, capture intent, and drive action.','#05070a'),
(5,5,1,'05','Brand Messaging','Strategic communication frameworks — tone of voice, taglines, and messaging pillars that unify your brand story.','#0f172a'),
(6,6,1,'06','Email Marketing','Nurture sequences and campaign emails that move subscribers down the funnel and drive repeat conversions.','#1e1b4b');
