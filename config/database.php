<?php
declare(strict_types=1);

/**
 * Creates a PDO connection for application pages.
 * Database credentials are stored in database.local.php, which must never be committed.
 */
$configFile = __DIR__ . '/database.local.php';
if (!is_file($configFile)) {
    throw new RuntimeException('سیستم هنوز نصب نشده است. ابتدا فایل install.php را اجرا کنید.');
}
$config = require $configFile;
if (!is_array($config)) {
    throw new RuntimeException('تنظیمات پایگاه داده معتبر نیست.');
}

$host = (string)($config['host'] ?? 'localhost');
$port = (int)($config['port'] ?? 3306);
$db = (string)($config['database'] ?? '');
$user = (string)($config['username'] ?? '');
$password = (string)($config['password'] ?? '');
$charset = (string)($config['charset'] ?? 'utf8mb4');

$dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";
return new PDO($dsn, $user, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);
