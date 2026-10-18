<?php
declare(strict_types=1);
session_start();
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

$localConfig = __DIR__ . '/config/database.local.php';
if (is_file($localConfig)) {
    http_response_code(403);
    exit('سیستم قبلاً نصب شده است. برای امنیت، فایل install.php را حذف کنید.');
}
if (empty($_SESSION['installer_csrf'])) $_SESSION['installer_csrf'] = bin2hex(random_bytes(32));
function h(string $v): string { return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

$error = '';
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!hash_equals($_SESSION['installer_csrf'], (string)($_POST['csrf_token'] ?? ''))) {
            throw new RuntimeException('درخواست معتبر نیست. صفحه را دوباره باز کنید.');
        }
        $host = trim((string)($_POST['db_host'] ?? 'localhost'));
        $port = (int)($_POST['db_port'] ?? 3306);
        $db = trim((string)($_POST['db_name'] ?? 'real_estate_management'));
        $dbUser = trim((string)($_POST['db_user'] ?? 'root'));
        $dbPass = (string)($_POST['db_pass'] ?? '');
        $admin = trim((string)($_POST['admin_user'] ?? ''));
        $adminPass = (string)($_POST['admin_pass'] ?? '');
        $confirm = (string)($_POST['admin_confirm'] ?? '');

        if ($host === '' || $port < 1 || $port > 65535) throw new RuntimeException('سرور یا درگاه پایگاه داده معتبر نیست.');
        if (!preg_match('/^[A-Za-z0-9_]{1,64}$/', $db)) throw new RuntimeException('نام پایگاه داده فقط می‌تواند شامل حروف انگلیسی، عدد و زیرخط باشد.');
        if (!preg_match('/^[A-Za-z0-9_.@-]{3,64}$/', $admin)) throw new RuntimeException('نام کاربری مدیر باید حداقل ۳ نویسه و فقط شامل حروف انگلیسی، عدد یا . _ @ - باشد.');
        if (strlen($adminPass) < 12) throw new RuntimeException('رمز عبور مدیر باید حداقل ۱۲ نویسه داشته باشد.');
        if ($adminPass !== $confirm) throw new RuntimeException('رمز عبور و تکرار آن یکسان نیست.');

        $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false];
        $server = new PDO($dsn, $dbUser, $dbPass, $options);
        // Database name is restricted to safe identifier characters above.
        $server->exec("CREATE DATABASE IF NOT EXISTS {$db} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo = new PDO($dsn . ";dbname={$db}", $dbUser, $dbPass, $options);

        $tables = [
            "CREATE TABLE IF NOT EXISTS users (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(64) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                full_name VARCHAR(150) NOT NULL,
                role ENUM('admin','manager','staff') NOT NULL DEFAULT 'staff',
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                last_login_at DATETIME NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            "CREATE TABLE IF NOT EXISTS people (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                person_type ENUM('owner','buyer','seller','landlord','tenant','customer','other') NOT NULL DEFAULT 'customer',
                full_name VARCHAR(180) NOT NULL,
                phone VARCHAR(40) NULL,
                alternate_phone VARCHAR(40) NULL,
                identity_number VARCHAR(100) NULL,
                address TEXT NULL,
                notes TEXT NULL,
                created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_people_name (full_name), INDEX idx_people_phone (phone),
                CONSTRAINT fk_people_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            "CREATE TABLE IF NOT EXISTS properties (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                property_code VARCHAR(40) NOT NULL UNIQUE,
                property_type VARCHAR(80) NOT NULL,
                transaction_type ENUM('sale','rent','sale_or_rent') NOT NULL,
                status ENUM('available','reserved','sold','rented','inactive') NOT NULL DEFAULT 'available',
                title VARCHAR(200) NOT NULL,
                description TEXT NULL,
                owner_id BIGINT UNSIGNED NULL,
                province VARCHAR(120) NULL, district VARCHAR(120) NULL, address TEXT NULL,
                latitude DECIMAL(10,7) NULL, longitude DECIMAL(10,7) NULL, map_place_id VARCHAR(255) NULL,
                size_value DECIMAL(12,2) NULL, size_unit VARCHAR(30) NULL,
                bedrooms SMALLINT UNSIGNED NULL, bathrooms SMALLINT UNSIGNED NULL,
                asking_price DECIMAL(18,2) NOT NULL, currency CHAR(3) NOT NULL DEFAULT 'AFN',
                price_type ENUM('fixed','negotiable') NOT NULL DEFAULT 'fixed',
                rent_period ENUM('monthly','yearly','weekly','daily','one_time') NULL,
                created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_property_status_type (status, property_type), INDEX idx_property_location (province, district),
                CONSTRAINT fk_property_owner FOREIGN KEY (owner_id) REFERENCES people(id) ON DELETE SET NULL,
                CONSTRAINT fk_property_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            "CREATE TABLE IF NOT EXISTS customer_requests (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                customer_id BIGINT UNSIGNED NOT NULL,
                transaction_type ENUM('sale','rent') NOT NULL,
                property_type VARCHAR(80) NOT NULL,
                province VARCHAR(120) NULL, district VARCHAR(120) NULL,
                budget_min DECIMAL(18,2) NULL, budget_max DECIMAL(18,2) NULL,
                currency CHAR(3) NOT NULL DEFAULT 'AFN',
                rent_period ENUM('monthly','yearly','weekly','daily','one_time') NULL,
                min_size DECIMAL(12,2) NULL, max_size DECIMAL(12,2) NULL,
                bedrooms SMALLINT UNSIGNED NULL, notes TEXT NULL,
                status ENUM('open','matched','closed','cancelled') NOT NULL DEFAULT 'open',
                created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_request_status (status, transaction_type, property_type),
                CONSTRAINT fk_request_customer FOREIGN KEY (customer_id) REFERENCES people(id),
                CONSTRAINT fk_request_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            "CREATE TABLE IF NOT EXISTS property_visits (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                property_id BIGINT UNSIGNED NOT NULL, customer_id BIGINT UNSIGNED NOT NULL,
                scheduled_at DATETIME NULL,
                visit_status ENUM('planned','completed','cancelled','no_show') NOT NULL DEFAULT 'planned',
                feedback TEXT NULL, created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_visit_schedule (scheduled_at, visit_status),
                CONSTRAINT fk_visit_property FOREIGN KEY (property_id) REFERENCES properties(id),
                CONSTRAINT fk_visit_customer FOREIGN KEY (customer_id) REFERENCES people(id),
                CONSTRAINT fk_visit_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            "CREATE TABLE IF NOT EXISTS offers (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                property_id BIGINT UNSIGNED NOT NULL, customer_id BIGINT UNSIGNED NOT NULL,
                offered_amount DECIMAL(18,2) NOT NULL, currency CHAR(3) NOT NULL DEFAULT 'AFN',
                offer_status ENUM('pending','accepted','rejected','withdrawn','countered') NOT NULL DEFAULT 'pending',
                offer_date DATE NOT NULL, notes TEXT NULL, created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_offer_status (property_id, offer_status),
                CONSTRAINT fk_offer_property FOREIGN KEY (property_id) REFERENCES properties(id),
                CONSTRAINT fk_offer_customer FOREIGN KEY (customer_id) REFERENCES people(id),
                CONSTRAINT fk_offer_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            "CREATE TABLE IF NOT EXISTS agreements (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                property_id BIGINT UNSIGNED NOT NULL, customer_id BIGINT UNSIGNED NOT NULL,
                agreement_type ENUM('sale','rent') NOT NULL,
                agreed_amount DECIMAL(18,2) NOT NULL, currency CHAR(3) NOT NULL DEFAULT 'AFN',
                rent_period ENUM('monthly','yearly','weekly','daily','one_time') NULL,
                agreement_date DATE NULL, start_date DATE NULL, end_date DATE NULL,
                status ENUM('draft','active','completed','cancelled') NOT NULL DEFAULT 'draft',
                notes TEXT NULL, created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_agreement_status (status, agreement_date),
                CONSTRAINT fk_agreement_property FOREIGN KEY (property_id) REFERENCES properties(id),
                CONSTRAINT fk_agreement_customer FOREIGN KEY (customer_id) REFERENCES people(id),
                CONSTRAINT fk_agreement_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            "CREATE TABLE IF NOT EXISTS exchange_rates (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                currency CHAR(3) NOT NULL, afn_per_unit DECIMAL(18,6) NOT NULL,
                effective_date DATE NOT NULL, notes VARCHAR(500) NULL, created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uq_currency_date (currency, effective_date), INDEX idx_rate_date (effective_date),
                CONSTRAINT fk_rate_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            "CREATE TABLE IF NOT EXISTS activity_logs (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NULL, action VARCHAR(100) NOT NULL,
                entity_type VARCHAR(100) NULL, entity_id BIGINT UNSIGNED NULL,
                details JSON NULL, ip_address VARCHAR(45) NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_activity_user_date (user_id, created_at), INDEX idx_activity_entity (entity_type, entity_id),
                CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ];
        foreach ($tables as $sql) $pdo->exec($sql);

        $check = $pdo->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
        $check->execute([$admin]);
        if ($check->fetch()) throw new RuntimeException('این نام کاربری قبلاً وجود دارد. نام دیگری انتخاب کنید.');
        $stmt = $pdo->prepare('INSERT INTO users (username, password_hash, full_name, role) VALUES (?, ?, ?, ?)');
        $stmt->execute([$admin, password_hash($adminPass, PASSWORD_DEFAULT), 'مدیر سیستم', 'admin']);
        $adminId = (int)$pdo->lastInsertId();
        $log = $pdo->prepare('INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details) VALUES (?, ?, ?, ?, ?)');
        $log->execute([$adminId, 'system_installed', 'users', $adminId, json_encode(['message'=>'Initial installation'], JSON_UNESCAPED_UNICODE)]);

        $dir = __DIR__ . '/config';
        if (!is_dir($dir) && !mkdir($dir, 0750, true) && !is_dir($dir)) throw new RuntimeException('ساخت پوشه تنظیمات ناموفق بود.');
        $config = "<?php\ndeclare(strict_types=1);\nreturn [\n"
            . " 'host' => " . var_export($host, true) . ",\n"
            . " 'port' => {$port},\n"
            . " 'database' => " . var_export($db, true) . ",\n"
            . " 'username' => " . var_export($dbUser, true) . ",\n"
            . " 'password' => " . var_export($dbPass, true) . ",\n"
            . " 'charset' => 'utf8mb4',\n];\n";
        $tmp = $localConfig . '.tmp';
        if (file_put_contents($tmp, $config, LOCK_EX) === false || !rename($tmp, $localConfig)) {
            @unlink($tmp);
            throw new RuntimeException('ذخیره تنظیمات پایگاه داده ناموفق بود. دسترسی نوشتن پوشه config را بررسی کنید.');
        }
        @chmod($localConfig, 0640);
        $success = true;
        $_SESSION = [];
        session_destroy();
    } catch (Throwable $e) {
        $error = 'نصب انجام نشد: ' . $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>نصب سیستم مدیریت رهنمای معاملات</title>
<style>
*{box-sizing:border-box}body{margin:0;padding:24px 14px;background:#f3f5f9;color:#1f2937;font-family:Tahoma,Arial,sans-serif;line-height:1.8}.card{max-width:760px;margin:20px auto;background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:28px;box-shadow:0 8px 28px #0f172a0a}h1{font-size:23px;margin:0 0 8px}h2{font-size:17px;margin:24px 0 12px;border-bottom:1px solid #e5e7eb;padding-bottom:8px}.muted{color:#6b7280;font-size:13px}.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.field label{display:block;font-size:13px;font-weight:bold;margin-bottom:6px}.field input{width:100%;padding:11px;border:1px solid #d1d5db;border-radius:7px;font:inherit;font-size:14px;direction:ltr;text-align:left}.field input:focus{outline:2px solid #bfdbfe;border-color:#2563eb}.btn{width:100%;margin-top:22px;padding:13px;border:0;border-radius:7px;background:#1d4ed8;color:#fff;font:inherit;font-weight:bold;cursor:pointer}.alert{padding:12px 14px;border-radius:7px;margin:16px 0;overflow-wrap:anywhere}.error{background:#fef2f2;color:#991b1b;border:1px solid #fecaca}.success{background:#f0fdf4;color:#166534;border:1px solid #bbf7d0}.notice{background:#fffbeb;color:#92400e;border:1px solid #fde68a;padding:12px;border-radius:7px;font-size:13px;margin-top:16px}a{color:#1d4ed8}@media(max-width:560px){.grid{grid-template-columns:1fr}.card{padding:20px}}
</style>
</head>
<body><main class="card">
<h1>نصب اولیه سیستم مدیریت رهنمای معاملات</h1>
<p class="muted">این صفحه پایگاه داده، جدول‌های اصلی و حساب مدیر سیستم را می‌سازد.</p>
<?php if ($error !== ''): ?><div class="alert error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?>
<div class="alert success"><strong>نصب موفقانه انجام شد.</strong><br>پایگاه داده و جدول‌های اصلی ساخته شدند و حساب مدیر ایجاد شد. اکنون برای امنیت، فایل install.php را از سرور حذف کنید.</div>
<p><a href="index.php">رفتن به صفحه ورود</a></p>
<?php else: ?>
<div class="notice">این نصب‌کننده جدول‌های مورد نیاز را ایجاد می‌کند و جدول‌های موجود را حذف نمی‌کند. آن را فقط یک بار اجرا کنید. رمزها را با دیگران شریک نسازید.</div>
<form method="post" action="" autocomplete="off">
<input type="hidden" name="csrf_token" value="<?= h($_SESSION['installer_csrf']) ?>">
<h2>تنظیمات پایگاه داده MySQL</h2>
<div class="grid">
<div class="field"><label for="db_host">سرور پایگاه داده</label><input id="db_host" name="db_host" value="<?= h((string)($_POST['db_host'] ?? 'localhost')) ?>" required></div>
<div class="field"><label for="db_port">درگاه</label><input id="db_port" name="db_port" value="<?= h((string)($_POST['db_port'] ?? '3306')) ?>" inputmode="numeric" required></div>
<div class="field"><label for="db_name">نام پایگاه داده</label><input id="db_name" name="db_name" value="<?= h((string)($_POST['db_name'] ?? 'real_estate_management')) ?>" required></div>
<div class="field"><label for="db_user">نام کاربری MySQL</label><input id="db_user" name="db_user" value="<?= h((string)($_POST['db_user'] ?? 'root')) ?>" required></div>
<div class="field" style="grid-column:1/-1"><label for="db_pass">رمز عبور MySQL</label><input id="db_pass" name="db_pass" type="password" autocomplete="new-password"></div>
</div>
<h2>ساخت حساب مدیر سیستم</h2>
<div class="grid">
<div class="field"><label for="admin_user">نام کاربری مدیر</label><input id="admin_user" name="admin_user" value="<?= h((string)($_POST['admin_user'] ?? '')) ?>" minlength="3" maxlength="64" required></div>
<div class="field"><label for="admin_pass">رمز عبور مدیر (حداقل ۱۲ نویسه)</label><input id="admin_pass" name="admin_pass" type="password" minlength="12" autocomplete="new-password" required></div>
<div class="field"><label for="admin_confirm">تکرار رمز عبور مدیر</label><input id="admin_confirm" name="admin_confirm" type="password" minlength="12" autocomplete="new-password" required></div>
</div>
<button class="btn" type="submit">ایجاد پایگاه داده و نصب سیستم</button>
</form>
<?php endif; ?>
</main></body></html>
