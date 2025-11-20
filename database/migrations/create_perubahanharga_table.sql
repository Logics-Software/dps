-- Create table perubahanharga
CREATE TABLE IF NOT EXISTS `perubahanharga` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `noperubahan` VARCHAR(15) NOT NULL,
  `tanggalperubahan` DATE NOT NULL,
  `keterangan` VARCHAR(100) NOT NULL,
  `kodebarang` VARCHAR(15) NOT NULL,
  `hargalama` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `discountlama` DECIMAL(8,2) NOT NULL DEFAULT 0.00,
  `hargabaru` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `discountbaru` DECIMAL(8,2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_noperubahan` (`noperubahan`),
  KEY `idx_kodebarang` (`kodebarang`),
  KEY `idx_tanggalperubahan` (`tanggalperubahan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

