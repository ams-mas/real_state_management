<?php
declare(strict_types=1);
session_start();
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: same-origin');
if (empty($_SESSION['login_csrf'])) {
    $_SESSION['login_csrf'] = bin2hex(random_bytes(32));
}
function h(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
$error = (string)($_SESSION['login_error'] ?? '');
unset($_SESSION['login_error']);
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>ورود | سیستم مدیریت رهنمای معاملات</title>
<style>
*{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;background:linear-gradient(135deg,#123b70,#1765a8);font-family:Tahoma,Arial,sans-serif;color:#172033;padding:20px}
.panel{width:min(100%,420px);background:#fff;border-radius:16px;padding:32px;box-shadow:0 18px 55px #001b3b55}
h1{font-size:23px;margin:0 0 8px}p{color:#5d6b80;line-height:1.8;margin:0 0 22px}
label{display:block;font-size:14px;margin:14px 0 7px}input{width:100%;padding:13px;border:1px solid #cbd5e1;border-radius:8px;font:inherit}
input:focus{outline:3px solid #bfdbfe;border-color:#2563eb}button{width:100%;margin-top:22px;padding:13px;background:#174ea6;color:white;border:0;border-radius:8px;font:inherit;font-weight:bold;cursor:pointer}
.error{background:#fff0f0;color:#9f1239;border:1px solid #fecdd3;border-radius:8px;padding:11px;margin-bottom:14px}
small{display:block;text-align:center;margin-top:20px;color:#7b8798}
</style>
</head>
<body>
<main class="panel">
<h1>سیستم مدیریت رهنمای معاملات</h1>
<p>برای ورود به سیستم، نام کاربری و رمز عبور خود را وارد کنید.</p>
<?php if ($error !== ''): ?><div class="error"><?= h($error) ?></div><?php endif; ?>
<form method="post" action="login.php" autocomplete="on">
<input type="hidden" name="csrf_token" value="<?= h((string)$_SESSION['login_csrf']) ?>">
<label for="username">نام کاربری</label>
<input id="username" name="username" required maxlength="64" autocomplete="username" autofocus>
<label for="password">رمز عبور</label>
<input id="password" name="password" type="password" required autocomplete="current-password">
<button type="submit">ورود به سیستم</button>
</form>
<small>دسترسی فقط برای کاربران مجاز</small>
</main>
</body>
</html>
