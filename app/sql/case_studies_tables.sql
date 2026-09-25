-- Case studies: source documents (PDF/DOCX/PPTX/XLSX) admins upload through
-- the admin panel, used as Digifyce's own reference library for the AI
-- website-audit feature to draw on (tools/website_audit/ai_analyzer.py) when
-- suggesting what Digifyce could do for a prospective client's business.

CREATE TABLE IF NOT EXISTS `case_studies` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(128) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `image_url` VARCHAR(255) DEFAULT NULL,
  `metrics_json` TEXT DEFAULT NULL,
  `position` INT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Migration: the uploaded source document itself (PDF/DOCX/PPTX/XLSX) - text
-- is extracted from this file at audit-generation time, not stored
-- separately, so re-uploading a new version takes effect on the next audit
-- with no extra step.
ALTER TABLE `case_studies` ADD COLUMN `file_path` VARCHAR(255) DEFAULT NULL AFTER `image_url`;

INSERT IGNORE INTO `permissions` (`name`, `description`, `module`, `action`) VALUES
('case_study.view', 'View case studies', 'case_studies', 'view'),
('case_study.edit', 'Add/edit case studies', 'case_studies', 'edit'),
('case_study.delete', 'Delete case studies', 'case_studies', 'delete');

INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id FROM `roles` r
CROSS JOIN `permissions` p
WHERE r.name IN ('Super Admin', 'Admin') AND p.module = 'case_studies';
