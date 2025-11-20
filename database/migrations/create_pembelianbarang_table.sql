-- Create table pembelianbarang
CREATE TABLE IF NOT EXISTS `pembelianbarang` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nopembelian` VARCHAR(15) NOT NULL,
  `tanggalpembelian` DATE NOT NULL,
  `namasupplier` VARCHAR(100) NOT NULL,
  `kodebarang` VARCHAR(15) NOT NULL,
  `jumlah` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `harga` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `discount` DECIMAL(8,2) NOT NULL DEFAULT 0.00,
  `totalharga` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_nopembelian` (`nopembelian`),
  KEY `idx_kodebarang` (`kodebarang`),
  KEY `idx_tanggalpembelian` (`tanggalpembelian`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

