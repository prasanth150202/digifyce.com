-- Website Audits: free automated Scrapy-powered audit offered from leadform.php
-- when a lead says they have a website.

CREATE TABLE IF NOT EXISTS `website_audits` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `lead_id` INT NOT NULL,
  `url` VARCHAR(255) NOT NULL,
  `token` CHAR(32) NOT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `status` ENUM('pending','running','completed','failed') NOT NULL DEFAULT 'pending',
  `attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `score` TINYINT UNSIGNED DEFAULT NULL,
  `findings_json` LONGTEXT DEFAULT NULL,
  `brand_brief` TEXT DEFAULT NULL,
  `scraped_data_json` LONGTEXT DEFAULT NULL,
  `error_message` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uniq_token` (`token`),
  KEY `idx_lead_id` (`lead_id`),
  KEY `idx_status` (`status`),
  KEY `idx_ip_created` (`ip_address`, `created_at`),
  CONSTRAINT `fk_website_audits_lead` FOREIGN KEY (`lead_id`) REFERENCES `lead_form_submissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Migration: AI audit plan + model tracking (two-phase plan/execute flow)
ALTER TABLE `website_audits` ADD COLUMN `ai_plan_json` LONGTEXT DEFAULT NULL AFTER `scraped_data_json`;
ALTER TABLE `website_audits` ADD COLUMN `ai_model_used` VARCHAR(64) DEFAULT NULL AFTER `ai_plan_json`;

-- Migration: detailed whole-site audit extras (executive summary, priority
-- action plan, paid-advertising channel/budget guidance)
ALTER TABLE `website_audits` ADD COLUMN `executive_summary` TEXT DEFAULT NULL AFTER `ai_model_used`;
ALTER TABLE `website_audits` ADD COLUMN `ai_priority_actions_json` LONGTEXT DEFAULT NULL AFTER `executive_summary`;
ALTER TABLE `website_audits` ADD COLUMN `ai_ad_strategy_json` LONGTEXT DEFAULT NULL AFTER `ai_priority_actions_json`;

-- Migration: matched case studies (an array, up to a few) from Digifyce's
-- own case-study library (app/admin/case_studies.php), when the AI found
-- genuinely relevant ones
ALTER TABLE `website_audits` ADD COLUMN `ai_case_study_matches_json` LONGTEXT DEFAULT NULL AFTER `ai_ad_strategy_json`;

-- Migration: explicit lead opt-in, toggled on the closing slide of the
-- audit story (leadform.php) - a deliberate "yes, follow up with me"
-- signal distinct from merely having submitted the lead form, so the team
-- can prioritize outreach. Set via app/api/audit_keep_in_touch.php.
ALTER TABLE `website_audits` ADD COLUMN `keep_in_touch` TINYINT(1) NOT NULL DEFAULT 0 AFTER `ai_case_study_matches_json`;

INSERT IGNORE INTO `permissions` (`name`, `description`, `module`, `action`) VALUES
('audit.view', 'View website audit results', 'website_audits', 'view'),
('audit.delete', 'Delete website audit records', 'website_audits', 'delete');

INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `roles` r
CROSS JOIN `permissions` p
WHERE r.name IN ('Super Admin', 'Admin') AND p.module = 'website_audits';
