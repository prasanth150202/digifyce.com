CREATE TABLE IF NOT EXISTS `testimonial_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `client_name` VARCHAR(255) NOT NULL DEFAULT '',
  `quote` TEXT,
  `story_label` VARCHAR(100) DEFAULT '',
  `video_path` VARCHAR(500) DEFAULT '',
  `thumbnail_path` VARCHAR(500) DEFAULT '',
  `logo_path` VARCHAR(500) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `testimonial_items` (`id`,`sort_order`,`client_name`,`quote`,`story_label`,`video_path`,`thumbnail_path`,`logo_path`) VALUES
(1,1,'AADHYA','Digifyce helped us scale our herbal care brand online. Their creative and performance marketing expertise is unmatched!','Client Story 01','public/assets/testimonials/videos/Aadhya.mp4','public/assets/testimonials/thumbnails/Aadhya.jpg','public/assets/cl_logos/Aadhya.png'),
(2,2,'AHA','We saw a 3x increase in leads after working with Digifyce. The team is proactive and results-driven.','Client Story 02','public/assets/testimonials/videos/Aha.mp4','public/assets/testimonials/thumbnails/Aha.png','public/assets/cl_logos/Aha.png'),
(3,3,'AISHWARYAM','From strategy to execution, Digifyce delivered beyond our expectations. Highly recommended!','Client Story 03','public/assets/testimonials/videos/Aishwaryam.mp4','public/assets/testimonials/thumbnails/Aishwaryam.jpg','public/assets/cl_logos/Aishwaryam.png'),
(4,4,'BAWSE BABY','The Digifyce team is creative, responsive, and truly cares about our growth. We\'re grateful for their partnership.','Client Story 04','public/assets/testimonials/videos/Bawse Baby.mp4','public/assets/testimonials/thumbnails/Bawse Baby.jpg','public/assets/cl_logos/Bawse Baby.png'),
(5,5,'SWEET SMITH','Digifyce\'s creative team brought our vision to life. We loved the process and the results!','Client Story 05','public/assets/testimonials/videos/Sweet Smith.mp4','public/assets/testimonials/thumbnails/Sweet Smith.jpg','public/assets/cl_logos/Sweet Smith.png'),
(6,6,'SWEET 16','We trust Digifyce for all our digital campaigns. Their approach is fresh and effective.','Client Story 06','public/assets/testimonials/videos/1.mp4','public/assets/testimonials/thumbnails/1.png','public/assets/cl_logos/Sweet 16.png');
