-- Table untuk menyimpan data kunjungan sales
CREATE TABLE IF NOT EXISTS `visits` (
  `visit_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `kodesales` varchar(15) DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `kodecustomer` varchar(15) NOT NULL,
  `check_in_time` datetime NOT NULL,
  `check_out_time` datetime DEFAULT NULL,
  `check_in_lat` decimal(10,8) DEFAULT NULL,
  `check_in_long` decimal(11,8) DEFAULT NULL,
  `check_out_lat` decimal(10,8) DEFAULT NULL,
  `check_out_long` decimal(11,8) DEFAULT NULL,
  `status_kunjungan` enum('Direncanakan','Sedang Berjalan','Selesai','Dibatalkan') NOT NULL DEFAULT 'Sedang Berjalan',
  `catatan` text DEFAULT NULL,
  `jarak_dari_kantor` decimal(10,2) DEFAULT NULL COMMENT 'Distance in km',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`visit_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_customer_id` (`customer_id`),
  KEY `idx_kodesales` (`kodesales`),
  KEY `idx_status_kunjungan` (`status_kunjungan`),
  KEY `idx_check_in_time` (`check_in_time`),
  CONSTRAINT `fk_visits_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_visits_customer` FOREIGN KEY (`customer_id`) REFERENCES `mastercustomer` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

