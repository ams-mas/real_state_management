<?php
declare(strict_types=1);
session_start();
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Cache-Control: no-store');

if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('شما اجازه ساخت حساب کاربری را ندارید.');
}
if (empty($_SESSION['create_user_csrf'])) {
    $_SESSION['create_user_csrf'] = bin2hex(random_bytes(32));
}
function h(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$error = '';
$success = '';
$username = '';
$fullName = '';
$role = 'staff';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!hash_equals((string)$_SESSION['create_user_csrf'], (string)($_POST['csrf_token'] ?? ''))) {
            throw new RuntimeException('درخواست معتبر نیست. صفحه را دوباره باز کنید.');
        }

        $username = trim((string)($_POST['username'] ?? ''));
        $fullName = trim((string)($_POST['full_name'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $passwordConfirm = (string)($_POST['password_confirm'] ?? '');
        $role = (string)($_POST['role'] ?? 'staff');

        if (!preg_match('/^[A-Za-z0-9_.@-]{3,64}$/', $username)) {
            throw new RuntimeException('نام کاربری باید ۳ تا ۶۴ نویسه و شامل حروف انگلیسی، عدد یا . _ @ - باشد.');
        }
        if ($fullName === '' || mb_strlen($fullName, 'UTF-8') > 150) {
            throw new RuntimeException('نام کامل را وارد کنید (حداکثر ۱۵۰ نویسه).');
        }
        if (strlen($password) < 12) {
            throw new RuntimeException('رمز عبور باید حداقل ۱۲ نویسه داشته باشد.');
        }
        if (!hash_equals($password, $passwordConfirm)) {
            throw new RuntimeException('رمز عبور و تکرار آن یکسان نیست.');
        }
        if (!in_array($role, ['admin', 'manager', 'staff'], true)) {
            throw new RuntimeException('نقش انتخاب‌شده معتبر نیست.');
        }

        $pdo = require __DIR__ . '/config/database.php';
        if (!$pdo instanceof PDO) {
            throw new RuntimeException('اتصال پایگاه داده آماده نیست.');
        }

        $check = $pdo->prepare('SELECT id FROM users WHERE username = :username LIMIT 1');
        $check->execute(['username' => $username]);
        if ($check->fetch()) {
            throw new RuntimeException('این نام کاربری قبلاً ثبت شده است.');
        }

        $insert = $pdo->prepare(
            'INSERT INTO users (username, password_hash, full_name, role, is_active)
             VALUES (:username, :password_hash, :full_name, :role, 1)'
        );
        $insert->execute([
            'username' => $username,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'full_name' => $fullName,
            'role' => $role,
        ]);
        $newUserId = (int)$pdo->lastInsertId();

        // JSON text works with both TEXT and JSON definitions of activity_logs.details.
        $details = json_encode([
            'created_user_id' => $newUserId,
            'created_username' => $username,
            'role' => $role,
        ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $log = $pdo->prepare(
            'INSERT INTO activity_logs (user_id, action, details)
             VALUES (:user_id, :action, :details)'
        );
        $log->execute([
            'user_id' => (int)$_SESSION['user_id'],
            'action' => 'user_created',
            'details' => $details,
        ]);

        $success = 'حساب کاربری با موفقیت ساخته شد.';
        $username = '';
        $fullName = '';
        $role = 'staff';
        $_SESSION['create_user_csrf'] = bin2hex(random_bytes(32));
    } catch (PDOException $e) {
        error_log('Create user database error: ' . $e->getMessage());
        if ($e->getCode() === '23000') {
            $error = 'این نام کاربری قبلاً ثبت شده است.';
        } else {
            $error = 'ساخت حساب انجام نشد. تنظیمات پایگاه داده و جدول‌های users و activity_logs را بررسی کنید.';
        }
    } catch (Throwable $e) {
        error_log('Create user failed: ' . $e->getMessage());
        $error = $e instanceof RuntimeException
            ? $e->getMessage()
            : 'ساخت حساب انجام نشد. گزارش خطا در PHP error log ثبت شده است.';
    }
}
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ساخت حساب کاربری | مدیریت رهنمای معاملات</title>
<style>
*{box-sizing:border-box}
body{margin:0;background:#f3f6fb;color:#1e293b;font-family:Tahoma,Arial,sans-serif;line-height:1.8}
header{background:#123b70;color:#fff;padding:16px 5%;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap}
header h1{font-size:19px;margin:0}
header a{color:#fff;text-decoration:none;border:1px solid #ffffff80;border-radius:7px;padding:7px 12px}
main{max-width:760px;margin:28px auto;padding:0 16px}
.panel{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:26px;box-shadow:0 8px 24px #0f172a0a}
h2{font-size:20px;margin:0 0 8px}.muted{color:#64748b;font-size:13px;margin-top:0}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.field label{display:block;font-size:14px;font-weight:bold;margin-bottom:6px}
.field input,.field select{width:100%;padding:11px;border:1px solid #cbd5e1;border-radius:7px;font:inherit;background:#fff}
.field input:focus,.field select:focus{outline:3px solid #bfdbfe;border-color:#2563eb}
button{width:100%;margin-top:20px;padding:12px;border:0;border-radius:7px;background:#174ea6;color:#fff;font:inherit;font-weight:bold;cursor:pointer}
.alert{padding:11px 13px;border-radius:7px;margin:15px 0;overflow-wrap:anywhere}
.error{background:#fff1f2;color:#9f1239;border:1px solid #fecdd3}
.success{background:#f0fdf4;color:#166534;border:1px solid #bbf7d0}
.actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:18px}.actions a{color:#174ea6;text-decoration:none}
@media(max-width:560px){.grid{grid-template-columns:1fr}.panel{padding:18px}}
</style>
</head>
<body>
<header><h1>سیستم مدیریت رهنمای معاملات</h1><a href="dashboard.php">بازگشت به داشبورد</a></header>
<main><section class="panel">
<h2>ساخت حساب کاربری جدید</h2>
<p class="muted">این صفحه فقط برای مدیر سیستم است. رمز عبور به شکل هش امن ذخیره می‌شود.</p>
<?php if ($error !== ''): ?><div class="alert error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success !== ''): ?><div class="alert success"><?= h($success) ?></div><?php endif; ?>
<form method="post" autocomplete="off">
<input type="hidden" name="csrf_token" value="<?= h((string)$_SESSION['create_user_csrf']) ?>">
<div class="grid">
<div class="field"><label for="username">نام کاربری (انگلیسی)</label><input id="username" name="username" value="<?= h($username) ?>" minlength="3" maxlength="64" required autocomplete="off"></div>
<div class="field"><label for="full_name">نام کامل</label><input id="full_name" name="full_name" value="<?= h($fullName) ?>" maxlength="150" required></div>
<div class="field"><label for="password">رمز عبور (حداقل ۱۲ نویسه)</label><input id="password" name="password" type="password" minlength="12" required autocomplete="new-password"></div>
<div class="field"><label for="password_confirm">تکرار رمز عبور</label><input id="password_confirm" name="password_confirm" type="password" minlength="12" required autocomplete="new-password"></div>
<div class="field"><label for="role">نقش کاربر</label><select id="role" name="role" required><option value="staff" <?= $role === 'staff' ? 'selected' : '' ?>>کارمند</option><option value="manager" <?= $role === 'manager' ? 'selected' : '' ?>>مدیر</option><option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>مدیر سیستم (دسترسی کامل)</option></select></div>
</div>
<button type="submit">ساخت حساب کاربری</button>
</form>
<div class="actions"><a href="dashboard.php">← بازگشت به داشبورد</a></div>
</section></main>
</body>
</html>
