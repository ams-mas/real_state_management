<?php
declare(strict_types=1);
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

function h(string $v): string { return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

$counts = ['properties' => 0, 'people' => 0, 'requests' => 0, 'visits' => 0, 'offers' => 0, 'agreements' => 0];
$recent = [];
$dbError = false;

try {
    $pdo = require __DIR__ . '/config/database.php';
    $queries = [
        'properties' => 'SELECT COUNT(*) FROM properties',
        'people' => 'SELECT COUNT(*) FROM people',
        'requests' => "SELECT COUNT(*) FROM customer_requests WHERE status IN ('open','matched')",
        'visits' => "SELECT COUNT(*) FROM property_visits WHERE visit_status = 'planned'",
        'offers' => "SELECT COUNT(*) FROM offers WHERE offer_status = 'pending'",
        'agreements' => "SELECT COUNT(*) FROM agreements WHERE status IN ('draft','active')",
    ];
    foreach ($queries as $key => $sql) $counts[$key] = (int)$pdo->query($sql)->fetchColumn();

    $stmt = $pdo->query(
        "SELECT action, entity_type, created_at FROM activity_logs ORDER BY id DESC LIMIT 8"
    );
    $recent = $stmt->fetchAll();
} catch (Throwable $e) {
    error_log('Dashboard error: ' . $e->getMessage());
    $dbError = true;
}

$labels = [
    'properties' => 'ملک‌ها',
    'people' => 'اشخاص و مشتریان',
    'requests' => 'درخواست‌های فعال مشتریان',
    'visits' => 'بازدیدهای برنامه‌ریزی‌شده',
    'offers' => 'پیشنهادهای در انتظار',
    'agreements' => 'توافق‌نامه‌های جاری',
];
$actionLabels = [
    'system_installed' => 'نصب سیستم',
    'login' => 'ورود به سیستم',
];
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#1d4ed8">
<title>داشبورد | سیستم مدیریت رهنمای معاملات</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#f3f5f9;color:#1f2937;font-family:Tahoma,Arial,sans-serif;line-height:1.8}.topbar{background:#fff;border-bottom:1px solid #e5e7eb;padding:14px 24px;display:flex;align-items:center;justify-content:space-between;gap:16px}.brand{font-weight:bold;color:#1d4ed8}.user-area{display:flex;align-items:center;gap:14px;font-size:13px}.logout{color:#b91c1c;text-decoration:none;border:1px solid #fecaca;padding:6px 12px;border-radius:7px}.layout{max-width:1200px;margin:0 auto;padding:28px 20px}.heading{display:flex;justify-content:space-between;align-items:end;gap:14px;margin-bottom:22px}.heading h1{font-size:25px;margin:0}.muted{color:#6b7280;font-size:13px}.cards{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}.metric{background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:20px}.metric .label{font-size:13px;color:#6b7280}.metric .number{font-size:30px;font-weight:bold;margin-top:6px;color:#111827}.metric .mark{width:38px;height:4px;border-radius:4px;background:#2563eb;margin-bottom:12px}.panel{margin-top:22px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:20px}.panel h2{font-size:17px;margin:0 0 12px}.links{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}.link-card{padding:13px;border:1px solid #e5e7eb;border-radius:8px;color:#374151;text-decoration:none;background:#fff}.link-card strong{display:block;font-size:14px}.link-card span{display:block;font-size:12px;color:#6b7280;margin-top:4px}.coming{display:inline-block;font-size:11px;color:#6b7280;margin-top:8px}.table-wrap{overflow-x:auto}table{border-collapse:collapse;width:100%;font-size:13px}th,td{text-align:right;padding:11px;border-bottom:1px solid #e5e7eb}th{color:#6b7280;font-weight:bold}.notice{background:#fffbeb;color:#92400e;border:1px solid #fde68a;border-radius:8px;padding:12px;margin-bottom:18px;font-size:13px}.empty{color:#6b7280;font-size:13px;padding:12px 0}@media(max-width:760px){.cards{grid-template-columns:repeat(2,minmax(0,1fr))}.links{grid-template-columns:repeat(2,minmax(0,1fr))}.topbar{padding:12px 15px}.layout{padding:22px 14px}.heading{align-items:start;flex-direction:column}}@media(max-width:460px){.cards,.links{grid-template-columns:1fr}.user-area{gap:8px}.brand{font-size:13px}.user-area span{max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}}
</style>
</head>
<body>
<header class="topbar">
<div class="brand">سیستم مدیریت رهنمای معاملات</div>
<div class="user-area"><span><?= h((string)($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'کاربر')) ?></span><a class="logout" href="logout.php">خروج</a></div>
</header>
<main class="layout">
<div class="heading"><div><h1>داشبورد اصلی</h1><div class="muted">خلاصه وضعیت دفتر و پیگیری‌های جاری</div></div><div class="muted"><?= h(date('Y/m/d')) ?></div></div>
<?php if ($dbError): ?><div class="notice">اتصال به پایگاه داده برقرار نشد. نصب سیستم و تنظیمات فایل config/database.local.php را بررسی کنید.</div><?php endif; ?>
<section class="cards" aria-label="خلاصه سیستم">
<?php foreach ($labels as $key => $label): ?>
<div class="metric"><div class="mark"></div><div class="label"><?= h($label) ?></div><div class="number"><?= number_format($counts[$key]) ?></div></div>
<?php endforeach; ?>
</section>
<section class="panel">
<h2>بخش‌های سیستم</h2>
<div class="links">
<div class="link-card"><strong>مدیریت ملک‌ها</strong><span>ثبت ملک، نوع معامله، قیمت و موقعیت روی نقشه</span><small class="coming">بخش بعدی برای پیاده‌سازی</small></div>
<div class="link-card"><strong>اشخاص و مشتریان</strong><span>اطلاعات مالکان، خریداران، فروشندگان و مستأجران</span><small class="coming">بخش بعدی برای پیاده‌سازی</small></div>
<div class="link-card"><strong>درخواست‌های مشتریان</strong><span>ثبت بودجه و مشخصات ملک مورد نیاز مشتری</span><small class="coming">بخش بعدی برای پیاده‌سازی</small></div>
<div class="link-card"><strong>بازدیدها و پیشنهادها</strong><span>پیگیری بازدیدها و پیشنهادهای خرید یا کرایه</span><small class="coming">بخش بعدی برای پیاده‌سازی</small></div>
<div class="link-card"><strong>توافق‌نامه‌ها</strong><span>ثبت توافق‌های فروش و کرایه</span><small class="coming">بخش بعدی برای پیاده‌سازی</small></div>
<div class="link-card"><strong>نرخ اسعار</strong><span>ثبت دستی نرخ دالر، کلدار و سایر اسعار در برابر افغانی</span><small class="coming">بخش بعدی برای پیاده‌سازی</small></div>
</div>
</section>
<section class="panel">
<h2>آخرین فعالیت‌های سیستم</h2>
<?php if (!$recent): ?><div class="empty">هنوز فعالیتی ثبت نشده است.</div><?php else: ?>
<div class="table-wrap"><table><thead><tr><th>فعالیت</th><th>بخش</th><th>تاریخ و زمان</th></tr></thead><tbody>
<?php foreach ($recent as $row): ?>
<tr><td><?= h($actionLabels[(string)$row['action']] ?? (string)$row['action']) ?></td><td><?= h((string)($row['entity_type'] ?? '—')) ?></td><td><?= h((string)$row['created_at']) ?></td></tr>
<?php endforeach; ?>
</tbody></table></div>
<?php endif; ?>
</section>
</main>
</body></html>
