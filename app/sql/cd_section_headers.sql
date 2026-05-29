-- Creative Development Section Headers + Video Cards

CREATE TABLE IF NOT EXISTS cd_section_headers (
  slug VARCHAR(50) PRIMARY KEY,
  eyebrow VARCHAR(255) DEFAULT '',
  heading TEXT,
  sub_text TEXT,
  extra_text TEXT,
  btn_label VARCHAR(255) DEFAULT '',
  btn_url VARCHAR(500) DEFAULT ''
);

CREATE TABLE IF NOT EXISTS cd_video_cards (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  track TINYINT DEFAULT 1,
  title VARCHAR(255) NOT NULL
);

-- Seed cd_section_headers
INSERT IGNORE INTO cd_section_headers (slug, eyebrow, heading, sub_text, extra_text, btn_label, btn_url) VALUES
('pains', 'Why Creative Development Matters', 'Customers interact with visuals before they interact with your product.', 'A weak creative presence can reduce trust, lower engagement, and negatively affect conversions even when your product or service is excellent. Many businesses struggle because their creatives are inconsistent, outdated, or disconnected from their marketing strategy.', '', '', ''),
('pains_right', 'Common Challenges', '', '', '', '', ''),
('pillars', 'Our Approach to Creative Development', 'Strategic Creative Systems Built for Performance', 'We do not create random designs, we create strategic visual systems that align with your brand identity, marketing goals, and customer psychology. We combine human creativity with smart AI-powered tools to improve speed, efficiency, and creative performance.', '', '', ''),
('services', 'Complete Creative Development Solutions', 'End-to-End Creative Services', '', '', '', ''),
('video', 'Visual Assets That Convert', 'Video Editing Services', 'Video content is one of the most powerful tools for marketing, storytelling, and customer engagement. Strong video editing improves clarity, professionalism, and conversion performance.', 'Short-form video content drives visibility and engagement across platforms like Instagram, Facebook, and YouTube.', 'At Digifyce, we provide professional video editing services designed for ads, branding, social media, and business communication. Our goal is to transform raw content into high-performing visual assets that connect with your audience.', ''),
('process', 'Execution', 'Our Creative Development Process', '', '', '', ''),
('metrics', 'Business Impact of Strong Creative Development', 'Strategic Creative Builds Business Value', 'Strategic creative development creates measurable business value and makes brand growth easier to sustain.', '', '', ''),
('why', 'Business Impact', 'Why Choose Digifyce', 'At Digifyce, we value our clients by creating visuals that drive real business impact. By combining graphic design and video editing under one system, we ensure your creative assets are built for performance and scale.', '', 'Scale Your Creatives Today', 'leadform.php');

-- Seed cd_video_cards (track 1 = up, track 2 = down)
INSERT IGNORE INTO cd_video_cards (id, sort_order, is_active, track, title) VALUES
(1, 1, 1, 1, 'Instagram Reels'),
(2, 2, 1, 1, 'Product showcase videos'),
(3, 3, 1, 1, 'Viral-format content edits'),
(4, 1, 1, 2, 'Short-form promotional videos'),
(5, 2, 1, 2, 'Launch announcement videos'),
(6, 3, 1, 2, 'Social media story videos');
