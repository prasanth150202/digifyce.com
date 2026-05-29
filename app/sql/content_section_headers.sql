-- Content Marketing Section Headers + Signal Card Points

CREATE TABLE IF NOT EXISTS content_section_headers (
  slug VARCHAR(50) PRIMARY KEY,
  eyebrow VARCHAR(255) DEFAULT '',
  heading TEXT,
  sub_text TEXT,
  extra_text TEXT,
  btn_label VARCHAR(255) DEFAULT '',
  btn_url VARCHAR(500) DEFAULT ''
);

CREATE TABLE IF NOT EXISTS content_signal_points (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  title VARCHAR(255) NOT NULL,
  description TEXT
);

-- Seed content_section_headers
INSERT IGNORE INTO content_section_headers (slug, eyebrow, heading, sub_text, extra_text, btn_label, btn_url) VALUES
('hero_signal', 'Content Signal', '', '', '', '', ''),
('solutions', 'Our Services', 'End-to-End Content Solutions', 'At Digifyce, we do not just write content — we create strategic content systems that build authority, drive traffic, and support business growth at every stage of the customer journey.', '', '', ''),
('challenges', 'The Challenge', 'Why Modern Brands Demand Strategic Content', 'Paid campaigns fade the moment you stop spending. Content compounds over time — building authority, trust, and organic visibility that supports every other growth channel.', '', '', ''),
('pillars', '', 'Our 3-Pillar Approach', 'We treat content as a highly targeted growth tool, not just a brand activity. Every piece of content is planned with intent, platform, and audience in mind.', '', '', ''),
('metrics', '', 'Business Impact of Strong Content', 'Strategic content marketing creates massive business value by improving visibility, authority, and conversion across every stage of the buyer journey.', '', '', ''),
('why', 'Our Advantage', 'Why Choose Digifyce', 'At Digifyce, we value our clients by creating content that builds real business value. We combine strategy, SEO expertise, and conversion-focused writing to help brands grow with content.', 'We do not just create content, we create growth through content.', 'Create Growth Today', 'leadform.php'),
('why_right_title', '', 'What makes us different:', '', '', '', '');

-- Seed content_signal_points
INSERT IGNORE INTO content_signal_points (id, sort_order, is_active, title, description) VALUES
(1, 1, 1, 'Intent Mapping', 'Match every page to the buying stage that drives action.'),
(2, 2, 1, 'Editorial Engine', 'Consistent publishing that compounds authority.'),
(3, 3, 1, 'Conversion Copy', 'UX-first storytelling designed for qualified leads.');
