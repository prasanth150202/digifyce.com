CREATE TABLE IF NOT EXISTS `technology_hero` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `badge` VARCHAR(255) DEFAULT '',
  `headline` VARCHAR(500) DEFAULT '',
  `description` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `technology_hero` (`id`,`badge`,`headline`,`description`) VALUES
(1,'Technology','Technology Engineered for Conversion & Scale','Every technology layer we build exists for one purpose — improve performance, automation and growth efficiency.');

CREATE TABLE IF NOT EXISTS `technology_panels` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `panel_number` VARCHAR(5) DEFAULT '01',
  `category_label` VARCHAR(255) DEFAULT '',
  `title` VARCHAR(500) DEFAULT '',
  `description` TEXT COMMENT 'Single paragraph for panels without bullets',
  `bullets_json` TEXT COMMENT 'JSON: [{h4, p}] for multi-bullet panels',
  `image_paths` TEXT COMMENT 'Comma-separated relative image paths'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `technology_panels` (`id`,`sort_order`,`panel_number`,`category_label`,`title`,`description`,`bullets_json`,`image_paths`) VALUES
(1,1,'01','DEVELOPMENT','High Performance Websites',NULL,'[{"h4":"Website Development","p":"Custom-built websites focused on performance, SEO structure and scalability."},{"h4":"High Conversion Ecommerce","p":"Ecommerce systems engineered for optimized checkout flow and revenue growth."},{"h4":"High Conversion Landing Pages","p":"Landing pages built for paid traffic and lead capture efficiency."}]','public/assets/tech-img/tech-web.png,public/assets/tech-img/tech-web1.png,public/assets/tech-img/tech-web2.png,public/assets/tech-img/tech-web3.png,public/assets/tech-img/tech-web4.png'),
(2,2,'02','CRM SYSTEMS','Centralized Customer Infrastructure','Connect marketing, sales and communication into one operational layer. Track leads, automate follow-ups and maintain full pipeline visibility.','[]','public/assets/tech-img/tech-crm.png,public/assets/tech-img/tech-crm2.png,public/assets/tech-img/tech-crm3.png'),
(3,3,'03','AUTOMATION','Automation & Conversational Bots','AI and rule-based automation bots deployed across WhatsApp, websites and CRM workflows to reduce manual workload and increase response speed.','[]','public/assets/tech-img/tech-zing1.png,public/assets/tech-img/tech-zing2.png,public/assets/tech-img/tech-zing3.png');
