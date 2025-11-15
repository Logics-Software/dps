-- Table untuk menyimpan WebAuthn credentials (biometrik)
CREATE TABLE IF NOT EXISTS `webauthn_credentials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `credential_id` varchar(255) NOT NULL COMMENT 'Base64 encoded credential ID',
  `public_key` text NOT NULL COMMENT 'JSON encoded public key',
  `counter` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Signature counter',
  `aaguid` varchar(36) DEFAULT NULL COMMENT 'Authenticator AAGUID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_used_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_credential_id` (`credential_id`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `fk_webauthn_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

