<?php
declare(strict_types=1);

/**
 * LOCAL DEVELOPMENT ONLY — DESTRUCTIVE DATABASE RESET.
 * Drops and recreates this application's core tables and creates a new admin.
 * Remove this file before any online deployment.
 */
session_start();
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Cache-Control: no-store');

$remote = (string)($_SERVER['REMOTE_ADDR'] ?? '');
if (!in_array($remote, ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit('این صفحه فقط از رایانه محلی قابل استفاده است.');
}
if (empty($_SESSION['database_reset_csrf'])) {
    $_SESSION['database_reset_csrf'] = bin2hex(random_bytes(32));
}
function h(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!hash_equals((string)$_SESSION['database_reset_csrf'], (string)($_POST['csrf_token'] ?? ''))) {
            throw new RuntimeException('درخواست معتبر نیست. صفحه را دوباره باز کنید.');
        }
        if ((string)($_POST['confirm_text'] ?? '') !== 'RESET ALL DATA') {
            throw new RuntimeException('عبارت تأیید را دقیقاً به شکل RESET ALL DATA وارد کنید.');
        }
        $adminUsername = trim((string)($_POST['admin_username'] ?? 'admin'));
        $adminPassword = (string)($_POST['admin_password'] ?? '');
        $adminPasswordConfirm = (string)($_POST['admin_password_confirm'] ?? '');
        if (!preg_match('/^[A-Za-z0-9_.@-]{3,64}$/', $adminUsername)) {
            throw new RuntimeException('نام کاربری مدیر معتبر نیست.');
        }
        if (strlen($adminPassword) < 12) {
            throw new RuntimeException('رمز عبور جدید مدیر باید حداقل ۱۲ نویسه داشته باشد.');
        }
        if (!hash_equals($adminPassword, $adminPasswordConfirm)) {
            throw new RuntimeException('رمز عبور و تکرار آن یکسان نیست.');
        }

        $pdo = require __DIR__ . '/config/database.php';
        if (!$pdo instanceof PDO) {
            throw new RuntimeException('اتصال پایگاه داده آماده نیست.');
        }
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // This page intentionally rebuilds only the application's known core tables.
        // The explicit table list avoids dropping unrelated tables in the database.
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        try {
            foreach ([
                'activity_logs', 'exchange_rates', 'agreements', 'offers',
                'property_visits', 'customer_requests', 'properties', 'people', 'users'
            ] as $table) {
                $pdo->exec('DROP TABLE IF EXISTS `' . $table . '`');
            }
        } finally {
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
        }

        $schema = [
            "CREATE TABLE users (
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
            "CREATE TABLE people (
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
                INDEX idx_people_name (full_name),
                INDEX idx_people_phone (phone),
                CONSTRAINT fk_people_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            "CREATE TABLE properties (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                property_code VARCHAR(40) NOT NULL UNIQUE,
                property_type VARCHAR(80) NOT NULL,
                transaction_type ENUM('sale','rent','sale_or_rent') NOT NULL,
                status ENUM('available','reserved','sold','rented','inactive') NOT NULL DEFAULT 'available',
                title VARCHAR(200) NOT NULL,
                description TEXT NULL,
                owner_id BIGINT UNSIGNED NULL,
                province VARCHAR(120) NULL,
                district VARCHAR(120) NULL,
                address TEXT NULL,
                latitude DECIMAL(10,7) NULL,
                longitude DECIMAL(10,7) NULL,
                map_place_id VARCHAR(255) NULL,
                size_value DECIMAL(12,2) NULL,
                size_unit VARCHAR(30) NULL,
                bedrooms SMALLINT UNSIGNED NULL,
                bathrooms SMALLINT UNSIGNED NULL,
                asking_price DECIMAL(18,2) NOT NULL,
                currency CHAR(3) NOT NULL DEFAULT 'AFN',
                price_type ENUM('fixed','negotiable') NOT NULL DEFAULT 'fixed',
                rent_period ENUM('monthly','yearly','weekly','daily','one_time') NULL,
                created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_property_status_type (status, property_type),
                INDEX idx_property_location (province, district),
                CONSTRAINT fk_property_owner FOREIGN KEY (owner_id) REFERENCES people(id) ON DELETE SET NULL,
                CONSTRAINT fk_property_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            "CREATE TABLE customer_requests (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                customer_id BIGINT UNSIGNED NOT NULL,
                transaction_type ENUM('sale','rent') NOT NULL,
                property_type VARCHAR(80) NOT NULL,
                province VARCHAR(120) NULL,
                district VARCHAR(120) NULL,
                budget_min DECIMAL(18,2) NULL,
                budget_max DECIMAL(18,2) NULL,
                currency CHAR(3) NOT NULL DEFAULT 'AFN',
                rent_period ENUM('monthly','yearly','weekly','daily','one_time') NULL,
                min_size DECIMAL(12,2) NULL,
                max_size DECIMAL(12,2) NULL,
                bedrooms SMALLINT UNSIGNED NULL,
                notes TEXT NULL,
                status ENUM('open','matched','closed','cancelled') NOT NULL DEFAULT 'open',
                created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_request_status (status, transaction_type, property_type),
                CONSTRAINT fk_request_customer FOREIGN KEY (customer_id) REFERENCES people(id),
                CONSTRAINT fk_request_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            "CREATE TABLE property_visits (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                property_id BIGINT UNSIGNED NOT NULL,
                customer_id BIGINT UNSIGNED NOT NULL,
                scheduled_at DATETIME NULL,
                visit_status ENUM('planned','completed','cancelled','no_show') NOT NULL DEFAULT 'planned',
                feedback TEXT NULL,
                created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_visit_schedule (scheduled_at, visit_status),
                CONSTRAINT fk_visit_property FOREIGN KEY (property_id) REFERENCES properties(id),
                CONSTRAINT fk_visit_customer FOREIGN KEY (customer_id) REFERENCES people(id),
                CONSTRAINT fk_visit_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            "CREATE TABLE offers (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                property_id BIGINT UNSIGNED NOT NULL,
                customer_id BIGINT UNSIGNED NOT NULL,
                offered_amount DECIMAL(18,2) NOT NULL,
                currency CHAR(3) NOT NULL DEFAULT 'AFN',
                offer_status ENUM('pending','accepted','rejected','withdrawn','countered') NOT NULL DEFAULT 'pending',
                offer_date DATE NOT NULL,
                notes TEXT NULL,
                created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_offer_status (property_id, offer_status),
                CONSTRAINT fk_offer_property FOREIGN KEY (property_id) REFERENCES properties(id),
                CONSTRAINT fk_offer_customer FOREIGN KEY (customer_id) REFERENCES people(id),
                CONSTRAINT fk_offer_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            "CREATE TABLE agreements (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                property_id BIGINT UNSIGNED NOT NULL,
                customer_id BIGINT UNSIGNED NOT NULL,
                agreement_type ENUM('sale','rent') NOT NULL,
                agreed_amount DECIMAL(18,2) NOT NULL,
                currency CHAR(3) NOT NULL DEFAULT 'AFN',
                rent_period ENUM('monthly','yearly','weekly','daily','one_time') NULL,
                agreement_date DATE NULL,
                start_date DATE NULL,
                end_date DATE NULL,
                status ENUM('draft','active','completed','cancelled') NOT NULL DEFAULT 'draft',
                notes TEXT NULL,
                created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_agreement_status (status, agreement_date),
                CONSTRAINT fk_agreement_property FOREIGN KEY (property_id) REFERENCES properties(id),
                CONSTRAINT fk_agreement_customer FOREIGN KEY (customer_id) REFERENCES people(id),
                CONSTRAINT fk_agreement_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            "CREATE TABLE exchange_rates (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                currency CHAR(3) NOT NULL,
                afn_per_unit DECIMAL(18,6) NOT NULL,
                effective_date DATE NOT NULL,
                notes VARCHAR(500) NULL,
                created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uq_currency_date (currency, effective_date),
                INDEX idx_rate_date (effective_date),
                CONSTRAINT fk_rate_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            "CREATE TABLE activity_logs (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NULL,
                action VARCHAR(100) NOT NULL,
                details TEXT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_activity_created (created_at),
                CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ];
        foreach ($schema as $sql) {
            $pdo->exec($sql);
        }

        $hash = password_hash($adminPassword, PASSWORD_DEFAULT);
        if ($hash === false) {
            throw new RuntimeException('ساخت رمز عبور مدیر ناموفق بود.');
        }
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, full_name, role, is_active) VALUES (:username, :hash, :name, 'admin', 1)");
        $stmt->execute([
            'username' => $adminUsername,
            'hash' => $hash,
            'name' => 'System Administrator',
        ]);
        $success = 'بازنشانی انجام شد. جدول‌های اصلی دوباره ساخته شدند و حساب مدیر ایجاد شد.';
        $_SESSION['database_reset_csrf'] = bin2hex(random_bytes(32));
    } catch (Throwable $e) {
        error_log('Database reset failed: ' . $e->getMessage());
        $error = 'بازنشانی کامل نشد. جزئیات خطا در PHP error log ثبت شده است: ' . $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>بازنشانی پایگاه داده</title>
<style>
*{box-sizing:border-box}body{margin:0;padding:24px;background:#f3f6fb;color:#172033;font-family:Tahoma,Arial,sans-serif}
main{max-width:650px;margin:4vh auto;background:#fff;border:1px solid #dbe3ef;border-radius:12px;padding:26px}
h1{font-size:23px}.warn,.error,.success{padding:12px;border-radius:8px;line-height:1.8}
.warn{background:#fff2e2;border:1px solid #fed7aa}.error{background:#ffeded;border:1px solid #fecaca}.success{background:#e8f7ed;border:1px solid #bbf7d0}
label{display:block;margin:15px 0 7px}input{width:100%;padding:12px;border:1px solid #cbd5e1;border-radius:7px;font:inherit}
button{margin-top:20px;padding:12px 18px;border:0;border-radius:7px;background:#a61b1b;color:#fff;font:inherit;cursor:pointer}
small{display:block;color:#64748b;line-height:1.8;margin-top:16px}
</style>
</head>
<body><main>
<h1>بازنشانی کامل پایگاه داده</h1>
<p class="warn"><strong>هشدار جدی:</strong> این عملیات جدول‌های اصلی برنامه و تمام اطلاعات داخل آن‌ها را حذف و دوباره ایجاد می‌کند. این کار قابل برگشت نیست. جدول‌های نامرتبط با برنامه حذف نمی‌شوند. فقط برای توسعه محلی استفاده کنید و قبل از انتشار آنلاین این فایل را حذف کنید.</p>
<?php if ($success !== ''): ?><p class="success"><?= h($success) ?></p><?php endif; ?>
<?php if ($error !== ''): ?><p class="error"><?= h($error) ?></p><?php endif; ?>
<form method="post" autocomplete="off">
<input type="hidden" name="csrf_token" value="<?= h((string)$_SESSION['database_reset_csrf']) ?>">
<label for="admin_username">نام کاربری مدیر جدید</label>
<input id="admin_username" name="admin_username" value="admin" required maxlength="64" pattern="[A-Za-z0-9_.@-]{3,64}">
<label for="admin_password">رمز عبور جدید مدیر (حداقل ۱۲ نویسه)</label>
<input id="admin_password" name="admin_password" type="password" minlength="12" required autocomplete="new-password">
<label for="admin_password_confirm">تکرار رمز عبور</label>
<input id="admin_password_confirm" name="admin_password_confirm" type="password" minlength="12" required autocomplete="new-password">
<label for="confirm_text">برای تأیید، دقیقاً بنویسید: RESET ALL DATA</label>
<input id="confirm_text" name="confirm_text" required autocomplete="off">
<button type="submit">حذف اطلاعات و ساخت دوباره جدول‌ها</button>
</form>
<small>این صفحه از localhost و 127.0.0.1 / ::1 قابل دسترسی است. پس از انجام کار، فایل reset_database.php را از پروژه حذف کنید.</small>
</main></body></html>
