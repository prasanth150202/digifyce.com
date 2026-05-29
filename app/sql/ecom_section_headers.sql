-- E-Commerce Marketing Section Headers

CREATE TABLE IF NOT EXISTS ecom_section_headers (
  slug VARCHAR(50) PRIMARY KEY,
  eyebrow VARCHAR(255) DEFAULT '',
  heading TEXT,
  sub_text TEXT,
  extra_text TEXT,
  btn_label VARCHAR(255) DEFAULT '',
  btn_url VARCHAR(500) DEFAULT ''
);

-- Seed
INSERT IGNORE INTO ecom_section_headers (slug, eyebrow, heading, sub_text, extra_text, btn_label, btn_url) VALUES
('challenges', 'The Friction', 'Why Many Stores Fail to Scale.', 'Many businesses invest in products and advertising but fail to generate consistent revenue because their store is not built for conversion. Without the right foundation, every marketing rupee is wasted.', 'Without the right foundation, marketing produces weak results.', '', ''),
('approaches', 'Strategic Focus', 'Our Approach to E-Commerce Growth', 'At Digifyce, we do not just build websites — we build complete e-commerce growth systems that convert visitors into customers and customers into loyal buyers.', '', '', ''),
('process', 'Execution', 'Our Development Process', '', '', '', ''),
('solutions', 'Platforms', 'Complete Solutions', 'End-to-end development tailored to your business model and growth objectives.', '', '', ''),
('why', 'Our Advantage', 'Why Choose Digifyce', 'At Digifyce, we value our clients by combining strategy, design, and technology to create e-commerce experiences that perform.', 'We do not just build stores, we build sales engines.', 'Build Your Sales Engine', 'leadform.php'),
('cta', '', '', 'We build sales engines.', '', '', '');
