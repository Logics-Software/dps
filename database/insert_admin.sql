-- Insert default admin user
-- Password: admin123
-- Untuk testing, gunakan password: admin123

INSERT INTO `users` (`username`, `namalengkap`, `email`, `password`, `role`, `status`) 
VALUES (
    'admin',
    'Administrator',
    'admin@dps.local',
    '$2y$10$iU18gs6TyePRG7LI7DbaPu/Jq3IN0Lupg65HaOohER9e4aQjKf4Vi',
    'admin',
    'aktif'
);

-- Catatan: 
-- Password default: admin123
-- Hash di atas menggunakan bcrypt dengan cost 10
-- Setelah login pertama kali, disarankan untuk mengubah password
-- 
-- Untuk generate hash baru, jalankan:
-- php database/generate_password_hash.php [password]

