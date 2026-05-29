CREATE TABLE IF NOT EXISTS `products_hero` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `headline_main` VARCHAR(255) DEFAULT '',
  `headline_highlight` VARCHAR(255) DEFAULT '',
  `description` TEXT,
  `crm_btn_label` VARCHAR(100) DEFAULT 'Explore CRM',
  `zingbot_btn_label` VARCHAR(100) DEFAULT 'Explore Zingbot'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `products_hero` (`id`,`headline_main`,`headline_highlight`,`description`,`crm_btn_label`,`zingbot_btn_label`) VALUES
(1,'Our Products For','Your Growth','We build structured revenue systems. Capture leads. Automate engagement. Close more deals.','Explore CRM','Explore Zingbot');

CREATE TABLE IF NOT EXISTS `products_crm_section` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `label` VARCHAR(100) DEFAULT 'CRM',
  `heading` VARCHAR(255) DEFAULT '',
  `sub_desc` TEXT,
  `cta_label` VARCHAR(100) DEFAULT 'Request CRM Demo',
  `cta_url` VARCHAR(500) DEFAULT 'leadform.php'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `products_crm_section` (`id`,`label`,`heading`,`sub_desc`,`cta_label`,`cta_url`) VALUES
(1,'CRM','Smart CRM System','Organize leads. Track deals. Increase conversions.','Request CRM Demo','leadform.php');

CREATE TABLE IF NOT EXISTS `products_crm_features` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `icon_class` VARCHAR(100) DEFAULT '',
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `description` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `products_crm_features` (`id`,`sort_order`,`icon_class`,`title`,`description`) VALUES
(1,1,'fa-solid fa-address-book','Lead Management','Centralized lead capture and tracking. No more spreadsheets. No more lost prospects.'),
(2,2,'fa-solid fa-diagram-project','Pipeline Control','Visual deal stages. Sales tracking. Clear forecasting and revenue visibility.'),
(3,3,'fa-solid fa-chart-pie','Performance Analytics','Data-driven dashboards to monitor team performance and optimize conversions.');

CREATE TABLE IF NOT EXISTS `products_zingbot_section` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `label` VARCHAR(100) DEFAULT 'Automation',
  `heading` VARCHAR(255) DEFAULT '',
  `sub_desc` TEXT,
  `cta_label` VARCHAR(100) DEFAULT 'Request Zingbot Demo',
  `cta_url` VARCHAR(500) DEFAULT 'leadform.php'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `products_zingbot_section` (`id`,`label`,`heading`,`sub_desc`,`cta_label`,`cta_url`) VALUES
(1,'Automation','Zingbot Automation','Capture. Engage. Convert. Automatically.','Request Zingbot Demo','leadform.php');

CREATE TABLE IF NOT EXISTS `products_zingbot_features` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `icon_class` VARCHAR(100) DEFAULT '',
  `title` VARCHAR(255) NOT NULL DEFAULT '',
  `description` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `products_zingbot_features` (`id`,`sort_order`,`icon_class`,`title`,`description`) VALUES
(1,1,'fa-solid fa-comments','Website Chatbot','Instant replies. Smart qualification. 24/7 lead capture without manual effort.'),
(2,2,'fa-brands fa-whatsapp','WhatsApp Automation','Broadcast campaigns, drip sequences, and automated responses that scale engagement.'),
(3,3,'fa-solid fa-link','CRM Integration','Automatically push qualified leads into your CRM pipeline for seamless follow-up.');

CREATE TABLE IF NOT EXISTS `products_cta` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `heading` VARCHAR(255) DEFAULT '',
  `description` TEXT,
  `btn_label` VARCHAR(100) DEFAULT '',
  `btn_url` VARCHAR(500) DEFAULT 'leadform.php'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `products_cta` (`id`,`heading`,`description`,`btn_label`,`btn_url`) VALUES
(1,'Build Your Growth Engine','Stop managing leads manually. Start scaling with structured automation.','Book Free Strategy Call','leadform.php');
