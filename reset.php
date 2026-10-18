<?php
declare(strict_types=1);

/**
 * LOCAL DEVELOPMENT ONLY — DESTRUCTIVE RESET.
 * Clears application records and recreates the administrator as:
 * username: admin
 * password: admin@1234
 *
 * Run only on localhost, then DELETE this file immediately.
 */
$remoteAddress = (string)($_SERVER['REMOTE_ADDR'] ?? '');
if (!in_array($remoteAddress, ['127.0.0.1', '::1'], true)) {
    http_response_code(404);
    exit('Not found.');
}

session_start();
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Cache-Control: no-store, max-age=0');

if (empty($_SESSION['reset_csrf'])) {
    $_SESSION['reset_csrf'] = bin2hex(random_bytes(32));
}
function h(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!hash_equals((string)$_SESSION['reset_csrf'], (string)($_POST['csrf_token'] ?? ''))) {
            throw new RuntimeException('درخواست معتبر نیست. صفحه را دوباره باز کنید.');
        }
        if (($_POST['confirmation'] ?? '') !== 'RESET ALL DATA') {
            throw new RuntimeException('برای تأیید، عبارت RESET ALL DATA را دقیق وارد کنید.');
        }

        $pdo = require __DIR__ . '/config/database.php';
        $pdo->beginTransaction();

        // Delete dependent records before their referenced records.
        $tables = [
            'activity_logs',
            'agreements',
            'offers',
            'property_visits',
            'customer_requests',
            'properties',
            'people',
            'exchange_rates',
            'users',
        ];
        foreach ($tables as $table) {
            // Table names are fixed constants in this script, not user input.
            $pdo->exec("DELETE FROM `{$table}`");
        }

        $username = 'admin';
        $password = 'admin@1234';
        $insert = $pdo->prepare(
            "INSERT INTO users (username, password_hash, full_name, role, is_active)
             VALUES (?, ?, ?, 'admin', 1)"
        );
        $insert->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
        $adminId = (int)$pdo->lastInsertId();

        $log = $pdo->prepare(
            'INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details)
             VALUES (?, ?, ?, ?, ?)'
        );
        $log->execute([
            $adminId,
            'system_data_reset',
            'users',
            $adminId,
            json_encode(['source' => 'localhost_reset_page'], JSON_UNESCAPED_UNICODE),
        ]);

        $pdo->commit();
        $success = true;
        $_SESSION['reset_csrf'] = bin2hex(random_bytes(32));
    } catch (Throwable $e) {
        if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = 'بازنشانی انجام نشد. بررسی کنید سیستم قبلاً نصب شده باشد و همه جدول‌های اصلی موجود باشند.';
        error_log('Local reset error: ' . $e->getMessage());
    }
}
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>بازنشانی سیستم مدیریت رهنمای معاملات</title>
<style>
*{box-sizing:border-box}
body{margin:0;padding:24px 14px;background:#f3f5f9;color:#1f2937;font-family:Tahoma,Arial,sans-serif;line-height:1.9}
.card{max-width:650px;margin:30px auto;background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:26px;box-shadow:0 8px 28px #0f172a0a}
h1{font-size:22px;margin:0 0 10px}
.warning{padding:14px;border-radius:8px;background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;margin:16px 0}
label{display:block;font-weight:bold;margin:16px 0 6px}
input{width:100%;padding:12px;border:1px solid #cbd5e1;border-radius:7px;font:inherit}
button{width:100%;padding:13px;margin-top:18px;background:#b91c1c;color:#fff;border:0;border-radius:7px;font:inherit;font-weight:bold;cursor:pointer}
.error,.success{padding:12px;border-radius:7px;margin:14px 0}
.error{background:#fef2f2;border:1px solid #fecaca;color:#991b1b}
.success{background:#f0fdf4;border:1px solid #bbf7d0;color:#166534}
small{color:#64748b}
</style>
</head>
<body>
<main class="card">
<h1>بازنشانی کامل اطلاعات سیستم</h1>
<div class="warning">
<strong>هشدار: این کار برگشت‌ناپذیر است.</strong><br>
تمام اطلاعات ثبت‌شده در جدول‌های سیستم، شامل ملک‌ها، اشخاص، درخواست‌ها، بازدیدها، پیشنهادها، قراردادها، نرخ ارز، گزارش فعالیت و حساب‌های کاربری حذف می‌شود. ساختار جدول‌ها حذف نمی‌شود.
</div>
<?php if ($error !== ''): ?><div class="error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?>
<div class="success">
بازنشانی انجام شد. حساب مدیر جدید ساخته شد.<br>
نام کاربری: <strong>admin</strong><br>
رمز عبور: <strong>admin@1234</strong>
</div>
<p><strong>اکنون فایل reset.php را فوراً از پوشه پروژه حذف کنید.</strong></p>
<p><a href="index.php">رفتن به صفحه ورود</a></p>
<?php else: ?>
<p>این صفحه فقط از همان کامپیوتری باز می‌شود که سرور محلی روی آن اجرا است.</p>
<p>برای ادامه، عبارت انگلیسی زیر را دقیق وارد کنید: <strong dir="ltr">RESET ALL DATA</strong></p>
<form method="post" autocomplete="off">
<input type="hidden" name="csrf_token" value="<?= h((string)$_SESSION['reset_csrf']) ?>">
<label for="confirmation">تأیید بازنشانی</label>
<input id="confirmation" name="confirmation" type="text" dir="ltr" required autocomplete="off" placeholder="RESET ALL DATA">
<button type="submit">حذف تمام اطلاعات و ساخت دوباره مدیر</button>
</form>
<?php endif; ?>
</main>
</body>
</html>
