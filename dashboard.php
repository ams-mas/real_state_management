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
function h(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
$counts = ['properties' => 0, 'people' => 0, 'requests' => 0, 'agreements' => 0];
$recent = [];
try {
    $pdo = require __DIR__ . '/config/database.php';
    $queries = [
        'properties' => 'SELECT COUNT(*) FROM properties',
        'people' => 'SELECT COUNT(*) FROM people',
        'requests' => "SELECT COUNT(*) FROM customer_requests WHERE status = 'open'",
        'agreements' => "SELECT COUNT(*) FROM agreements WHERE status IN ('draft','active')",
    ];
    foreach ($queries as $key => $sql) $counts[$key] = (int)$pdo->query($sql)->fetchColumn();
    $recent = $pdo->query('SELECT action, details, created_at FROM activity_logs ORDER BY id DESC LIMIT 8')->fetchAll();
} catch (Throwable $e) {
    error_log('Dashboard query failed: ' . $e->getMessage());
}
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>داشبورد | مدیریت رهنمای معاملات</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#f4f7fb;color:#1e293b;font-family:Tahoma,Arial,sans-serif}
header{background:#123b70;color:white;padding:18px 5%;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
header h1{font-size:20px;margin:0}header a{color:white;text-decoration:none;border:1px solid #ffffff80;border-radius:7px;padding:8px 12px}
main{max-width:1100px;margin:28px auto;padding:0 18px}.welcome{margin-bottom:20px}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:14px}
.card,.section{background:white;border:1px solid #e2e8f0;border-radius:12px;padding:20px}
.card span{color:#64748b;font-size:14px}.number{font-size:30px;font-weight:bold;margin-top:12px;color:#174ea6}
.section{margin-top:20px}h2{font-size:18px;margin-top:0}table{width:100%;border-collapse:collapse}th,td{text-align:right;padding:12px;border-bottom:1px solid #e2e8f0;font-size:14px}
nav{display:flex;flex-wrap:wrap;gap:10px;margin-top:18px}nav a{background:white;border:1px solid #dbe3ef;padding:12px 14px;border-radius:8px;text-decoration:none;color:#174ea6}
.muted{color:#64748b;line-height:1.8}
</style>
</head>
<body>
<header><h1>سیستم مدیریت رهنمای معاملات</h1><a href="logout.php">خروج از حساب</a></header>
<main>
<div class="welcome"><h2>خوش آمدید، <?= h((string)($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'کاربر')) ?></h2><p class="muted">خلاصه وضعیت دفتر و دسترسی سریع به بخش‌های اصلی.</p></div>
<div class="cards">
<div class="card"><span>مجموع ملک‌ها</span><div class="number"><?= $counts['properties'] ?></div></div>
<div class="card"><span>اشخاص و مشتریان</span><div class="number"><?= $counts['people'] ?></div></div>
<div class="card"><span>درخواست‌های باز مشتریان</span><div class="number"><?= $counts['requests'] ?></div></div>
<div class="card"><span>توافق‌نامه‌های فعال یا پیش‌نویس</span><div class="number"><?= $counts['agreements'] ?></div></div>
</div>
<nav>
<a href="properties.php">ملک‌ها</a><a href="people.php">اشخاص و مشتریان</a><a href="requests.php">درخواست مشتریان</a><a href="visits.php">بازدیدها</a><a href="offers.php">پیشنهادها</a><a href="agreements.php">توافق‌نامه‌ها</a><a href="exchange_rates.php">نرخ ارز</a><a href="create_user.php">ساخت حساب کاربری</a>
</nav>
<section class="section"><h2>آخرین فعالیت‌ها</h2>
<?php if (!$recent): ?><p class="muted">هنوز فعالیتی ثبت نشده است یا اتصال پایگاه داده در دسترس نیست.</p>
<?php else: ?><table><thead><tr><th>عملیات</th><th>جزئیات</th><th>زمان</th></tr></thead><tbody>
<?php foreach ($recent as $row): ?><tr><td><?= h((string)$row['action']) ?></td><td><?= h((string)($row['details'] ?? '')) ?></td><td><?= h((string)$row['created_at']) ?></td></tr><?php endforeach; ?>
</tbody></table><?php endif; ?>
</section>
</main></body></html>
