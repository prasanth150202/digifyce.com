CREATE TABLE IF NOT EXISTS `service_hero` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `badge` VARCHAR(255) DEFAULT '',
  `headline_line1` VARCHAR(255) DEFAULT '',
  `headline_line2` VARCHAR(255) DEFAULT '',
  `description` TEXT,
  `stat_label` VARCHAR(255) DEFAULT '',
  `stat_value` VARCHAR(50) DEFAULT '',
  `stat_suffix` VARCHAR(100) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `service_hero` (`id`,`badge`,`headline_line1`,`headline_line2`,`description`,`stat_label`,`stat_value`,`stat_suffix`) VALUES
(1,'Strategic Engineering & Performance Intelligence','The Architecture','of High-Yield Growth','Deep-dive analysis of our core service ecosystems. Engineered for performance-focused stakeholders requiring granular data and comparative insight.','Aggregate Performance Lift','+142%','vs avg.');

CREATE TABLE IF NOT EXISTS `service_blocks` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `section_number` VARCHAR(5) DEFAULT '01',
  `title_line1` VARCHAR(255) NOT NULL DEFAULT '',
  `title_line2` VARCHAR(255) DEFAULT '',
  `description` TEXT,
  `tech_badges` TEXT COMMENT 'Comma-separated badge labels',
  `left_col_heading` VARCHAR(255) DEFAULT '',
  `left_metrics_json` TEXT COMMENT 'JSON: [{label,value,bar_pct?,color?,note?,note_color?}]',
  `right_col_heading` VARCHAR(255) DEFAULT '',
  `right_metrics_json` TEXT COMMENT 'JSON: [{label,value,bar_pct,color}]',
  `case_study_icon` VARCHAR(100) DEFAULT 'terminal',
  `case_study_section_label` VARCHAR(255) DEFAULT '',
  `case_study_quote` VARCHAR(500) DEFAULT '',
  `case_study_body` TEXT,
  `case_study_image_url` VARCHAR(500) DEFAULT '',
  `case_study_stats_json` TEXT COMMENT 'JSON: [{label,value,color?}]'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `service_blocks` (`id`,`sort_order`,`section_number`,`title_line1`,`title_line2`,`description`,`tech_badges`,`left_col_heading`,`left_metrics_json`,`right_col_heading`,`right_metrics_json`,`case_study_icon`,`case_study_section_label`,`case_study_quote`,`case_study_body`,`case_study_image_url`,`case_study_stats_json`) VALUES
(1,1,'01','Website','Ecosystems','Transitioning from legacy monolithic structures to Headless E-commerce. We optimize for the technical metrics that drive transaction velocity.','Next.js,Hydrogen,Core Web Vitals','Infrastructure Metrics','[{"label":"LCP (Largest Contentful Paint)","value":"0.8s","bar_pct":90,"color":"green"},{"label":"TTFB (Time To First Byte)","value":"140ms","bar_pct":85,"color":"green"}]','Business Impact','[{"label":"Conv. Rate Lift (Post-Migration)","value":"+28.4%","bar_pct":70,"color":"blue"},{"label":"Avg. Page Load Reduction","value":"-62%","bar_pct":82,"color":"blue"}]','terminal','Analysis Snippet','"Solving the scale bottleneck."','Implemented a headless Shopify Hydrogen architecture for a high-volume retailer. By decoupling the frontend, we enabled dynamic personalization without sacrificing Core Web Vitals, resulting in a 14% increase in AOV.','https://lh3.googleusercontent.com/aida-public/AB6AXuAsFjE5EphKE8sXsbSQ24bJmMPfI2KN2iTUYcHr1anS9YeP_0K4i0-aQbO_ZIygHRwJx7kDRTiBoXz4HMFwjEwGAVpdyOzSLLnTScrx_Kj7k7Vl3H5SMXF54ePa1Gz3Ay7-t0cI2ZAjhLgSXEy31f6gEblwe-fW67QQJyW4B3QidAizLdkq0jCgRY6TH2B30Cmz7Zk9ujjbXjHmtw0IcGwU9DzhdvpeNdzAVJGwOLGZuMtMqs1WbmXzLwA6ZJXdLUvjCotMsDjAqw','[{"label":"Annual UV","value":"4.2M","color":"white"},{"label":"Delta Revenue","value":"+$2.1M","color":"green"}]'),
(2,2,'02','Marketplace','Dominance','Precision-engineered channel management across Amazon and Flipkart. Moving beyond basic PPC into full-funnel DSP and inventory forecasting.','Amazon DSP,Flipkart','Channel Efficiency','[{"label":"Buy Box Share","value":"94%","bar_pct":94,"color":"blue"},{"label":"Ad Sales % of Total","value":"18%","bar_pct":30,"color":"blue"}]','Inventory Velocity','[{"label":"Stock-out Rate","value":"<1.5%","bar_pct":10,"color":"green"},{"label":"Forecast Accuracy","value":"92%","bar_pct":92,"color":"blue"}]','inventory_2','Efficiency Brief','"Reclaiming margins from Amazon fees."','Reduced TACoS from 24% to 12.8% within 90 days for an FMCG brand. This was achieved via predictive listing optimization and aggressive cross-channel remarketing through Amazon DSP.','https://lh3.googleusercontent.com/aida-public/AB6AXuD9GRIg20tLm8_rtIrEIRZzPmJI9l4sMto2pUOSrMNciBm0yh4MM-wNQHjsusHeu-eFcN3ZU1LafE6IbYU_ItQOtY9UvWD2z4yF9FtDItqecgn-Lpm9pmOr9WwfAh9bynwbN7CBKBvXTFEOaNXsIaVIPifYzXehKNSyFV_nesJg6o0CG0V8oD2vfKv42ac_chEgYpli1_iNUfe9lA6VMoEtiTPf17mYNBetBv1MdgbJgMZlV2oAsuJSsU2glkRWyzKsHQZiLJEbzw','[{"label":"TACoS Reduction","value":"47%","color":"white"}]'),
(3,3,'03','Hyper-Precision','Lead Gen','Data-driven B2B and consumer acquisition. We focus on Intent-Based Lead Quality rather than sheer volume.','ABM Logic,Lead Scoring,Predictive Modeling','Funnel Analytics','[{"label":"MQL to SQL Conv.","value":"38%","note":"+18% vs avg.","note_color":"green"},{"label":"CPL Efficiency","value":"$42.50","note":"-22% vs benchmark","note_color":"blue"},{"label":"Lead Decay Rate","value":"0.8%","note":"Ultra-low","note_color":"muted"}]','','[]','target','Campaign Performance','"Eliminating waste in high-ticket B2B."','Built an Account-Based Marketing (ABM) engine for a SaaS provider. By integrating intent data from 3rd party signals, we targeted only accounts in an active buying window, increasing pipeline value by $4M in two quarters.','https://lh3.googleusercontent.com/aida-public/AB6AXuAnEXNAHGsKR5a99soipforEDk8q5j1hK0tL9cDoBykUU9u6fUAfFxaO_gcCrWv6uh8O8UFMkFHtu8WhMHjbiSg3jkKXxMrM-TJfWjHJ0_vGUtKQ4weeRyYlmyefUMEdTvHjtTQ4RZt9OIj9bG2eQKBNcFYIwxwdDcgJ-Nk77O62EaD8bGRvwDEMlDX9taEjoPIegSS-5SDKWCkfsIX39j5cOIB9GL8F09jBd7DL8dMN9-B_8_HdLoajIGW-esXnj-poGIM7AlucA','[{"label":"Pipeline Lift","value":"+$4.2M","color":"blue"}]');
