-- E-Commerce Marketing Page Tables

CREATE TABLE IF NOT EXISTS ecom_challenges (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  icon VARCHAR(100) DEFAULT '',
  title VARCHAR(255) NOT NULL,
  description TEXT
);

CREATE TABLE IF NOT EXISTS ecom_approaches (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  number VARCHAR(10) DEFAULT '',
  title VARCHAR(255) NOT NULL,
  description TEXT,
  tag VARCHAR(100) DEFAULT ''
);

CREATE TABLE IF NOT EXISTS ecom_steps (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  step_number VARCHAR(10) DEFAULT '',
  title VARCHAR(255) NOT NULL,
  description TEXT
);

-- Seed data

INSERT IGNORE INTO ecom_challenges (id, sort_order, is_active, icon, title, description) VALUES
(1,1,1,'speed','Slow Loading','Causes immediate drop-offs before users even see your products.'),
(2,2,1,'smartphone','Poor Mobile UX','Frustrates the largest segment of modern online shoppers.'),
(3,3,1,'shopping_cart_checkout','Complex Checkout','Leads to massive cart abandonment right at the finish line.'),
(4,4,1,'security','Low Trust Factors','Weak design makes customers hesitate to enter payment details.'),
(5,5,1,'api','Limited Integration','Manual operations bottleneck your ability to fulfill and scale.');

INSERT IGNORE INTO ecom_approaches (id, sort_order, is_active, number, title, description, tag) VALUES
(1,1,1,'01','User Experience','A smooth, intuitive shopping journey that helps customers buy faster and with full confidence. Every touchpoint is optimised for clarity and decision-making speed.','UX Design'),
(2,2,1,'02','Conversion Optimization','Every page, layout, and checkout step is engineered to lift sales and cut drop-offs. We remove friction at every point so intent becomes action.','CRO Strategy'),
(3,3,1,'03','Scalability','Your store is built to grow alongside your business. We architect platforms that handle more products, more traffic, and more markets without breaking.','Platform Architecture'),
(4,4,1,'04','Performance','Fast-loading, technically strong stores that rank well and convert better. Speed is not an afterthought — it is a direct lever for revenue.','Core Web Vitals');

INSERT IGNORE INTO ecom_steps (id, sort_order, is_active, step_number, title, description) VALUES
(1,1,1,'1','Discovery & Strategy','We begin by understanding your products, target audience, customer journey, and business goals to select the right platform.'),
(2,2,1,'2','Experience Planning','We define store architecture, UI/UX flow, navigation structure, and conversion-focused customer journey mapping.'),
(3,3,1,'3','Development & Integration','Our team builds your online store with required features, payment gateways, apps, and third-party integrations.'),
(4,4,1,'4','Testing & Launch','We test website speed, responsiveness, mobile performance, and user flow to ensure a flawless launch.');
