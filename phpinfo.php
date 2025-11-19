<?php
/**
 * PHP Info - untuk mengecek PHP settings
 * HAPUS FILE INI SETELAH SELESAI MENGECEK (untuk keamanan)
 * 
 * Akses file ini via browser: http://localhost/dps/phpinfo.php
 * Cari: upload_max_filesize dan post_max_size
 */

// Tampilkan info PHP
phpinfo();

// Tampilkan upload settings secara khusus
echo "<hr>";
echo "<h2>Upload Settings:</h2>";
echo "<ul>";
echo "<li><strong>upload_max_filesize:</strong> " . ini_get('upload_max_filesize') . "</li>";
echo "<li><strong>post_max_size:</strong> " . ini_get('post_max_size') . "</li>";
echo "<li><strong>max_execution_time:</strong> " . ini_get('max_execution_time') . " seconds</li>";
echo "<li><strong>max_input_time:</strong> " . ini_get('max_input_time') . " seconds</li>";
echo "<li><strong>memory_limit:</strong> " . ini_get('memory_limit') . "</li>";
echo "</ul>";

echo "<hr>";
echo "<h2>Lokasi php.ini:</h2>";
echo "<p><strong>" . php_ini_loaded_file() . "</strong></p>";
if (php_ini_scanned_files()) {
    echo "<p>Additional ini files: " . php_ini_scanned_files() . "</p>";
}

