-- Table untuk menyimpan aktivitas kunjungan
CREATE TABLE IF NOT EXISTS `visit_activities` (
  `activity_id` int(11) NOT NULL AUTO_INCREMENT,
  `visit_id` int(11) NOT NULL,
  `activity_type` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`activity_id`),
  KEY `idx_visit_id` (`visit_id`),
  CONSTRAINT `fk_visit_activities_visit` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`visit_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

