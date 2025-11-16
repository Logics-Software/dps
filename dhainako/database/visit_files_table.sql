-- Table untuk menyimpan file yang diupload saat checkout kunjungan
CREATE TABLE IF NOT EXISTS `visit_files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `visit_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL COMMENT 'Size in bytes',
  `file_type` varchar(50) NOT NULL COMMENT 'File extension',
  `uploaded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`file_id`),
  KEY `idx_visit_id` (`visit_id`),
  CONSTRAINT `fk_visit_files_visit` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`visit_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

