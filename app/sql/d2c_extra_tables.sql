-- D2C Branding Extra Tables (section headers)

CREATE TABLE IF NOT EXISTS d2c_section_headers (
  slug VARCHAR(50) PRIMARY KEY,
  eyebrow VARCHAR(255) DEFAULT '',
  heading TEXT,
  sub_text TEXT,
  extra_text TEXT,
  btn_label VARCHAR(255) DEFAULT '',
  btn_url VARCHAR(500) DEFAULT ''
);

-- Extend cs_section_headers with CTA fields
ALTER TABLE cs_section_headers ADD COLUMN IF NOT EXISTS btn_label VARCHAR(255) DEFAULT '';
ALTER TABLE cs_section_headers ADD COLUMN IF NOT EXISTS btn_url VARCHAR(500) DEFAULT '';

-- Seed d2c_section_headers
INSERT IGNORE INTO d2c_section_headers (slug, eyebrow, heading, sub_text, extra_text, btn_label, btn_url) VALUES
('hero_bg', '', 'BRAND', '', '', '', ''),
('marquee', '', '["BRAND IDENTITY","LOGO DESIGN","PACKAGING","BRAND STRATEGY","VISUAL GUIDELINES","D2C GROWTH"]', '', '', '', ''),
('intro', 'WHY', 'Understanding the Challenges', 'Many D2C brands struggle to achieve consistent growth because their branding lacks clarity and direction. In a crowded digital marketplace, customers are constantly exposed to multiple choices and without a strong brand identity, it becomes difficult to capture attention and build trust.', 'At Digifyce, we help businesses build clear, consistent, and impactful brand identities across every customer touchpoint, closing the gaps that reduce conversion rates and long-term brand recall.', '', ''),
('challenges', 'Common Friction Points', 'Common Challenges Include', 'These gaps not only affect customer perception but also reduce conversion rates and long-term brand recall.', '', '', ''),
('pillars', 'Our Philosophy', 'Our Approach to Building High-Performance D2C Brands', 'At Digifyce, we approach branding as a strategic business asset, not just a creative output. We focus on building a complete brand ecosystem that aligns with your product, audience, and market dynamics. Every element of your brand is carefully crafted to communicate value, establish trust, and influence buying behavior.', '', '', ''),
('solutions', 'What We Deliver', 'Comprehensive D2C Branding Solutions', '', '', '', ''),
('process', 'How We Work', 'End-to-End Branding Process', '', '', '', ''),
('process_cta', '', '', 'Speak with our team and get a tailored D2C branding roadmap.', '', 'Ready to turn this process into growth', 'leadform.php'),
('metrics', 'Why It Works', 'Branding Impact at a Glance', 'Our D2C branding process is designed to improve trust, recall, and conversion across every touchpoint.', '', '', ''),
('why', 'Building Brands That are Performance Driven.', 'Why Choose Digifyce', 'At Digifyce, we value our clients and understand that D2C brands require a specialized approach. We work as a growth partner, helping you build a brand that is ready to scale in the Indian market.', '', 'Start Your Brand Journey', 'leadform.php'),
('cta_bg', '', 'GROW', '', '', '', '');

-- Brand Shoot extra: new slug for challenges card
INSERT IGNORE INTO cs_section_headers (slug, eyebrow, heading, sub_text, extra_text, btn_label, btn_url) VALUES
('why_challenges', '', 'Common challenges include', 'Without strong visual content, even the best products struggle to perform.', '', '', '');
