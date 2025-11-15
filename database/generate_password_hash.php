<?php
// Script untuk generate password hash
// Usage: php generate_password_hash.php [password]

$password = $argv[1] ?? 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: {$password}\n";
echo "Hash: {$hash}\n";
echo "\n";
echo "SQL Query:\n";
echo "INSERT INTO `users` (`username`, `namalengkap`, `email`, `password`, `role`, `status`) \n";
echo "VALUES (\n";
echo "    'admin',\n";
echo "    'Administrator',\n";
echo "    'admin@dps.local',\n";
echo "    '{$hash}',\n";
echo "    'admin',\n";
echo "    'aktif'\n";
echo ");\n";

